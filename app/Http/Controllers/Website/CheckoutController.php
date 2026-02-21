<?php

namespace App\Http\Controllers\Website;

use App\Services\Services\ERP\ERPService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Services\Contracts\CartInterface;
use App\Services\Wasender\WasenderNotifier;

class CheckoutController extends Controller
{
    public function __construct(protected CartInterface $cartInterface)
    {
        $this->cartInterface = $cartInterface;
    }

    public function create()
    {
        $cart = $this->cartInterface->get();
        $total = $this->cartInterface->total();

        if ($cart->count() == 0) {
            return redirect()->route('home');
        }

        return view('website.pages.checkout')->with([
            'cart' => $cart,
            'total' => $total,
            'pageTitle' => trans('site/site.checkout'),
            'breadcrumbs' => [
                ['title' => trans('site/site.checkout')],
            ]
        ]);
    }

    public function store(Request $request)
    {
        $cart = $this->cartInterface->get();
        $coupon = null;
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', $request->post('coupon_code'))->first();
            if (!$coupon) {
                return redirect()->back()->with('error', 'الكوبون غير صالح');
            }
            if ($coupon->status != 'active') {
                return redirect()->back()->with('error', 'هذا الكوبون غير مفعل حالياً');
            }
            $now = now();
            if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
                return redirect()->back()->with('error', 'الكوبون غير مفعل بعد');
            }
            if ($coupon->expires_at && $now->gt($coupon->expires_at)) {
                return redirect()->back()->with('error', 'انتهت صلاحية الكوبون');
            }
            $cartTotal = $this->cartInterface->total();
            if ($coupon->min_spend && $cartTotal < $coupon->min_spend) {
                return redirect()->back()->with('error', 'الحد الأدنى لتفعيل الكوبون هو ' . $coupon->min_spend . ' جنيه');
            }
            if ($coupon->max_spend && $cartTotal > $coupon->max_spend) {
                return redirect()->back()->with('error', 'الحد الأقصى لاستخدام الكوبون هو ' . $coupon->max_spend . ' جنيه');
            }
            $discountedTotal = $this->cartInterface->applyCoupon($coupon);
        } else {
            $discountedTotal = $this->cartInterface->total();
        }
        //try {
            DB::beginTransaction();
            $order = Order::create([
            'user_id' => auth()?->user()?->id,
                'payment_type' => 'cash_on_delivery',
                'status' => 'pending',
                'payment_status' => 'pending',
                'total_price' => $discountedTotal,
                'coupon_id' => $coupon?->id,
            ]);
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'options' => $item->options,
                ]);
            }
            foreach ($request->post('addr') as $type => $address) {
                $address['type'] = $type;
                $order->addresses()->create($address);
            }
            DB::commit();
            $this->cartInterface->empty();
            // الآن أرسل الطلب للـ ERP
            $erpService = new ERPService();
            $erpResponse = $erpService->sendOrder($order);
            // يمكنك التعامل مع الاستجابة كما تريد، مثلاً تسجيلها أو إرسال رسالة للمستخدم
            if (!$erpResponse['success']) {
                // سجل الخطأ في اللوغ مثلاً
                \Log::error('Failed to send order to ERP', $erpResponse);
            }

            // WhatsApp notifications (Wasender) - should never break checkout
            try {
                /** @var WasenderNotifier $notifier */
                $notifier = app(WasenderNotifier::class);

                $billing = $order->addresses()->where('type', 'billing')->first();
                $customerPhone = $billing?->phone ?: ($order->user?->phone ?? null);

                $notifier->notifyAdmins(
                    "طلب جديد ✅\n"
                    ."رقم الطلب: {$order->number}\n"
                    ."الإجمالي: {$order->total_price}\n"
                    ."العميل: ".($billing?->first_name ? trim(($billing?->first_name ?? '').' '.($billing?->last_name ?? '')) : ($order->user?->name ?? 'Guest'))."\n"
                    ."الجوال: ".($billing?->phone ?? ($order->user?->phone ?? '—'))
                );

                $notifier->notifyCustomer(
                    $customerPhone,
                    "تم استلام طلبك ✅\nرقم الطلب: {$order->number}\nالإجمالي: {$order->total_price}"
                );
            } catch (\Throwable $e) {
                \Log::error('Wasender notify failed (checkout)', ['error' => $e->getMessage()]);
            }

            return redirect()->route('shop.index')->with([
                'success' => trans('site/site.checkout_successfully')
            ]);
        /*} catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '!حدث خطأ ما');
        }*/
    }

    public function applyCoupon(Request $request)
    {
        $code = $request->input('coupon_code');
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || $coupon->status != 'active') {
            return response()->json(['success' => false, 'message' => 'الكوبون غير صالح أو غير مفعل']);
        }

        $now = now();
        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            return response()->json(['success' => false, 'message' => 'الكوبون غير مفعل بعد']);
        }
        if ($coupon->expires_at && $now->gt($coupon->expires_at)) {
            return response()->json(['success' => false, 'message' => 'انتهت صلاحية الكوبون']);
        }

        $total = $this->cartInterface->total();
        if ($coupon->min_spend && $total < $coupon->min_spend) {
            return response()->json(['success' => false, 'message' => 'الحد الأدنى لتفعيل الكوبون هو ' . $coupon->min_spend . ' جنيه']);
        }
        if ($coupon->max_spend && $total > $coupon->max_spend) {
            return response()->json(['success' => false, 'message' => 'الحد الأقصى لاستخدام الكوبون هو ' . $coupon->max_spend . ' جنيه']);
        }
        session()->put('applied_coupon', $coupon->id);
        $discountedTotal = $this->cartInterface->applyCoupon($coupon);
        return response()->json([
            'success' => true,
            'discounted_total' => number_format($discountedTotal, 2)
        ]);
    }
}