<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DiamondCode;
use App\Models\ManualPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Services\Integrations\Shop2TopUp\Shop2TopUpService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ManualPaymentController extends Controller
{
    private function normalizeShop2TopUpOfferGroupFromName(?string $name): string
    {
        $name = (string) $name;
        if (stripos($name, 'eu') !== false) {
            return 'EU';
        }
        if (stripos($name, 'global') !== false) {
            return 'GLOBAL';
        }
        return 'DEFAULT';
    }

    private function normalizeShop2TopUpOfferGroupFromRegion(?string $region): string
    {
        $region = strtoupper(trim((string) $region));
        if ($region === 'EU') {
            return 'EU';
        }
        // Shop2TopUp examples include RU; treat everything else as DEFAULT unless vendor specifies GLOBAL.
        if ($region === 'GLOBAL') {
            return 'GLOBAL';
        }
        return 'DEFAULT';
    }

    private function extractDiamondAmountKey(?string $name): ?int
    {
        $name = (string) $name;

        // Prefer patterns like "100 💎" or "💎 100"
        if (preg_match('/(\d+)\s*💎/u', $name, $m)) {
            return (int) $m[1];
        }
        if (preg_match('/💎\s*(\d+)/u', $name, $m)) {
            return (int) $m[1];
        }

        return null;
    }

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

        // If this request is for a "gems" product, perform Shop2TopUp topup before approving.
        if (($manualPaymentRequest->product?->service_type ?? null) === 'gems') {
            $product = $manualPaymentRequest->product;
            $playerId = trim((string) $manualPaymentRequest->player_id);
            $offerId = (int) ($product?->itemID ?? 0);

            if ($playerId === '' || mb_strlen($playerId) < 3) {
                return back()->withErrors(['error' => 'Player ID غير صحيح.']);
            }
            if ($offerId <= 0) {
                return back()->withErrors(['error' => 'هذا المنتج غير مربوط بعرض Shop2TopUp (itemId). قم بالمزامنة أو ضبط itemId أولاً.']);
            }

            $service = new Shop2TopUpService();

            // Reserve a trx id with a DB lock to prevent duplicate topups (double-click / concurrent requests).
            $reservedTrxId = null;
            $alreadyHadTrx = false;

            DB::transaction(function () use ($manualPaymentRequest, &$reservedTrxId, &$alreadyHadTrx, $request) {
                /** @var ManualPaymentRequest $mpr */
                $mpr = ManualPaymentRequest::query()
                    ->whereKey($manualPaymentRequest->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!empty($mpr->shop2topup_trx_id)) {
                    $reservedTrxId = (string) $mpr->shop2topup_trx_id;
                    $alreadyHadTrx = true;
                    return;
                }

                $reservedTrxId = (string) Str::uuid();
                $mpr->update([
                    'shop2topup_trx_id' => $reservedTrxId,
                    'shop2topup_status' => 'SUBMITTING',
                    'admin_note' => $request->input('admin_note'),
                ]);
            });

            // If already has trx, do not resend topup.
            if ($alreadyHadTrx) {
                try {
                    $trx = $service->getTransaction((string) $reservedTrxId);
                    if (($trx['success'] ?? false) === true) {
                        $manualPaymentRequest->update([
                            'shop2topup_status' => $trx['status'] ?? $manualPaymentRequest->shop2topup_status,
                            'shop2topup_order_id' => $trx['order_id'] ?? $manualPaymentRequest->shop2topup_order_id,
                            'shop2topup_secure_id' => $trx['secure_id'] ?? $manualPaymentRequest->shop2topup_secure_id,
                            'shop2topup_delivery_at' => !empty($trx['delivery_at']) ? $trx['delivery_at'] : $manualPaymentRequest->shop2topup_delivery_at,
                            'shop2topup_response' => $trx,
                        ]);
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            } else {
            // Ensure the player name is checked (API requires it)
            $check = $service->checkPlayer($playerId);
            if (!($check['success'] ?? false)) {
                $msg = $check['msg'] ?? 'NOT_READY';
                return back()->withErrors(['error' => 'Shop2TopUp: لا يمكن التحقق من اللاعب الآن: ' . $msg . '. حاول بعد دقيقة.']);
            }

            // Try to avoid REFUND_REGION by picking a region-matching offer if possible.
            $playerGroup = $this->normalizeShop2TopUpOfferGroupFromRegion($check['region'] ?? null);
            $productGroup = $this->normalizeShop2TopUpOfferGroupFromName($product?->name ?? null);
            // Global offers are treated as universal (do not block by region).
            if ($productGroup !== 'GLOBAL' && $playerGroup !== $productGroup) {
                $amountKey = $this->extractDiamondAmountKey($product?->name ?? null);
                if ($amountKey) {
                    $alt = \App\Models\Product::query()
                        ->where('service_type', 'gems')
                        ->whereNotNull('itemID')
                        ->where('itemID', '!=', '')
                        ->whereHas('translations', function ($q) use ($amountKey, $playerGroup) {
                            $q->where('name', 'like', '%' . $amountKey . '%');
                            if ($playerGroup === 'EU') {
                                $q->where('name', 'like', '%EU%');
                            } elseif ($playerGroup === 'GLOBAL') {
                                $q->where('name', 'like', '%Global%');
                            } else {
                                $q->where('name', 'not like', '%EU%')->where('name', 'not like', '%Global%');
                            }
                        })
                        ->orderBy('id', 'desc')
                        ->first();

                    if ($alt && (int) ($alt->itemID ?? 0) > 0) {
                        $offerId = (int) $alt->itemID;
                    }
                }
            }

            $topup = $service->topup($playerId, $offerId, (string) $reservedTrxId);

            if (!($topup['success'] ?? false)) {
                $msg = $topup['msg'] ?? 'TOPUP_FAILED';
                if ($msg === 'REFUND_REGION') {
                    return back()->withErrors(['error' => 'Shop2TopUp: REFUND_REGION — الباقة لا تناسب منطقة اللاعب. جرّب باقة EU أو Global أو الافتراضية حسب المنطقة.']);
                }
                return back()->withErrors(['error' => 'Shop2TopUp: فشل الشحن: ' . $msg]);
            }

            $providerTrx = $topup['trxID'] ?: (string) $reservedTrxId;

            // Save trx details on the request for tracking via /transaction
            try {
                $manualPaymentRequest->update([
                    'shop2topup_trx_id' => $providerTrx,
                    'shop2topup_status' => 'SUBMITTED',
                    'shop2topup_response' => $topup,
                ]);
            } catch (\Throwable $e) {
                report($e);
                return back()->withErrors([
                    'error' => 'تم إرسال الشحن للمزود ✅ لكن تعذر حفظ بيانات العملية محلياً. نفّذ: php artisan migrate --force ثم أعد المحاولة (لن نعيد الإرسال).'
                ]);
            }

            // Try to fetch transaction status immediately (best-effort)
            try {
                $trx = $service->getTransaction($providerTrx);
                if (($trx['success'] ?? false) === true) {
                    $manualPaymentRequest->update([
                        'shop2topup_status' => $trx['status'] ?? $manualPaymentRequest->shop2topup_status,
                        'shop2topup_order_id' => $trx['order_id'] ?? $manualPaymentRequest->shop2topup_order_id,
                        'shop2topup_secure_id' => $trx['secure_id'] ?? $manualPaymentRequest->shop2topup_secure_id,
                        'shop2topup_delivery_at' => !empty($trx['delivery_at']) ? $trx['delivery_at'] : $manualPaymentRequest->shop2topup_delivery_at,
                        'shop2topup_response' => $trx,
                    ]);
                }
            } catch (\Throwable $e) {
                // ignore
            }

            // Clear gems page cache (optional)
            foreach (['ar', 'en'] as $locale) {
                Cache::forget("diamonds.charge.$locale");
            }
            } // end resend guard else
        }

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

