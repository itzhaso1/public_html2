<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DiamondCode;
use App\Models\ManualPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Services\Integrations\Shop2TopUp\Shop2TopUpService;

class ManualPaymentController extends Controller
{
    public function index()
    {
        $requests = ManualPaymentRequest::query()
            ->with(['product', 'user'])
            ->latest()
            ->paginate(30);

        return view('dashboard.admin.manual_payments.index', [
            'pageTitle' => 'طلبات الدفع اليدوي',
            'requests' => $requests,
        ]);
    }

    public function show(ManualPaymentRequest $manualPaymentRequest)
    {
        $manualPaymentRequest->load(['product', 'user']);

        return view('dashboard.admin.manual_payments.show', [
            'pageTitle' => 'تفاصيل طلب الدفع اليدوي',
            'mpr' => $manualPaymentRequest,
            'receiptUrl' => $manualPaymentRequest->receipt_path
                ? route('admin.manual_payments.receipt', $manualPaymentRequest)
                : null,
            'receiptIsPdf' => $manualPaymentRequest->receipt_path
                ? str_ends_with(strtolower($manualPaymentRequest->receipt_path), '.pdf')
                : false,
        ]);
    }

    public function receipt(ManualPaymentRequest $manualPaymentRequest)
    {
        abort_if(! $manualPaymentRequest->receipt_path, 404);
        abort_if(! Storage::disk('public')->exists($manualPaymentRequest->receipt_path), 404);

        return Storage::disk('public')->response($manualPaymentRequest->receipt_path);
    }

    public function checkTransaction(Request $request, ManualPaymentRequest $manualPaymentRequest)
    {
        $request->validate([
            'trx_id' => ['required', 'string', 'max:100'],
        ]);

        $trxId = (string) $request->input('trx_id');

        $service = new Shop2TopUpService();
        $res = $service->getTransaction($trxId);

        if (! ($res['success'] ?? false)) {
            $msg = $res['msg'] ?? 'فشل التحقق من العملية';
            return back()->withErrors(['error' => 'Shop2TopUp: ' . $msg]);
        }

        $manualPaymentRequest->update([
            'shop2topup_trx_id' => $trxId,
            'shop2topup_status' => $res['status'] ?? null,
            'shop2topup_order_id' => $res['order_id'] ?? null,
            'shop2topup_secure_id' => $res['secure_id'] ?? null,
            'shop2topup_delivery_at' => !empty($res['delivery_at']) ? $res['delivery_at'] : null,
            'shop2topup_response' => $res,
        ]);

        return back()->with('success', 'تم تحديث حالة العملية من Shop2TopUp ✅');
    }

    public function approve(Request $request, ManualPaymentRequest $manualPaymentRequest)
    {
        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $manualPaymentRequest->load(['product']);

        // If this request is for a "codes" product, allocate and deliver a code.
        if (($manualPaymentRequest->product?->service_type ?? null) === 'codes') {
            if (! $manualPaymentRequest->user_id) {
                return back()->withErrors(['error' => 'لا يمكن تسليم الكود بدون مستخدم (تأكد أن العميل مسجّل دخول).']);
            }

            $available = DiamondCode::query()
                ->where('product_id', $manualPaymentRequest->product_id)
                ->where('status', 'available')
                ->orderBy('id')
                ->first();

            if (! $available) {
                return back()->withErrors(['error' => 'لا يوجد أكواد متاحة لهذا المنتج. أضف أكواد من الداشبورد أولاً.']);
            }

            $available->update([
                'status' => 'delivered',
                'user_id' => $manualPaymentRequest->user_id,
                'manual_payment_request_id' => $manualPaymentRequest->id,
                'delivered_at' => now(),
            ]);

            // Codes page is cached; clear it so out-of-stock products disappear immediately.
            foreach (['ar', 'en'] as $locale) {
                Cache::forget("diamonds.codes.$locale");
            }
        }

        $manualPaymentRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
            'admin_note' => $request->input('admin_note'),
        ]);

        return redirect()
            ->route('admin.manual_payments.show', $manualPaymentRequest)
            ->with('success', 'تمت الموافقة على الطلب.');
    }

    public function reject(Request $request, ManualPaymentRequest $manualPaymentRequest)
    {
        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $manualPaymentRequest->load(['product']);

        $manualPaymentRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->input('admin_note'),
        ]);

        if (($manualPaymentRequest->product?->service_type ?? null) === 'codes') {
            foreach (['ar', 'en'] as $locale) {
                Cache::forget("diamonds.codes.$locale");
            }
        }

        return redirect()
            ->route('admin.manual_payments.show', $manualPaymentRequest)
            ->with('success', 'تم رفض الطلب.');
    }
}

