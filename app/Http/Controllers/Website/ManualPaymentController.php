<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\DiamondCode;
use App\Models\ManualPaymentRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\Wasender\WasenderNotifier;

class ManualPaymentController extends Controller
{
    private function forgetCodesPageCache(): void
    {
        foreach (['ar', 'en'] as $locale) {
            Cache::forget("diamonds.codes.$locale");
        }
    }

    private function ensureCodesAvailabilityOrRedirect(Product $product)
    {
        $available = DiamondCode::query()
            ->where('product_id', $product->id)
            ->where('status', 'available')
            ->count();

        $pending = ManualPaymentRequest::query()
            ->where('product_id', $product->id)
            ->where('status', 'pending')
            ->count();

        if (($available - $pending) <= 0) {
            return redirect()
                ->route('website.diamonds.codes')
                ->withErrors(['error' => 'نفذت الكمية لهذا المنتج حالياً.']);
        }

        return null;
    }

    public function create(Product $product)
    {
        abort_unless(config('bank.enabled'), 404);

        $isCodes = ($product->service_type ?? null) === 'codes';
        if ($isCodes) {
            $redirect = $this->ensureCodesAvailabilityOrRedirect($product);
            if ($redirect) return $redirect;
        }

        return view('website.diamonds.manual_payment', [
            'product' => $product,
            'pageTitle' => 'الدفع اليدوي',
        ]);
    }

    public function store(Request $request, Product $product)
    {
        abort_unless(config('bank.enabled'), 404);

        $isCodes = ($product->service_type ?? null) === 'codes';
        if ($isCodes) {
            $redirect = $this->ensureCodesAvailabilityOrRedirect($product);
            if ($redirect) return $redirect;
        }

        $rules = [
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ];

        // For gems top-up we need the player's ID. For codes we don't.
        if (! $isCodes) {
            $rules['player_id'] = ['required', 'string', 'max:64'];
        }

        $data = $request->validate($rules);

        try {
            Storage::disk('public')->makeDirectory('manual-payments');

            $receiptPath = null;
            if ($request->hasFile('receipt')) {
                $file = $request->file('receipt');
                if (! $file->isValid()) {
                    return back()->withErrors(['receipt' => 'فشل رفع الإيصال، حاول مرة أخرى.'])->withInput();
                }

                $receiptPath = Storage::disk('public')->putFile('manual-payments', $file);
                if (! $receiptPath) {
                    return back()->withErrors(['receipt' => 'تعذر حفظ الإيصال على السيرفر.'])->withInput();
                }
            }
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['receipt' => 'حدث خطأ أثناء رفع الإيصال.'])->withInput();
        }

        $mpr = ManualPaymentRequest::create([
            'reference' => (string) Str::uuid(),
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            // `player_id` is required for gems and not required for codes.
            'player_id' => $data['player_id'] ?? '-',
            'contact_phone' => null,
            'contact_email' => null,
            'amount' => (float) $product->price,
            'currency' => 'SAR',
            'receipt_path' => $receiptPath ?? null,
            'status' => 'pending',
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 512, ''),
        ]);

        // WhatsApp notifications (Wasender) - should never break the request flow
        try {
            /** @var WasenderNotifier $notifier */
            $notifier = app(WasenderNotifier::class);

            $notifier->notifyAdmins(
                "طلب دفع يدوي جديد ✅\n"
                ."المرجع: {$mpr->reference}\n"
                ."المنتج: {$product->name}\n"
                ."المبلغ: {$mpr->amount} {$mpr->currency}\n"
                ."المستخدم: ".(Auth::user()?->name ?? '—')
            );

            $notifier->notifyCustomer(
                Auth::user()?->phone,
                "تم استلام طلب الدفع اليدوي ✅\nالمرجع: {$mpr->reference}\nالمنتج: {$product->name}\nالمبلغ: {$mpr->amount} {$mpr->currency}"
            );
        } catch (\Throwable $e) {
            \Log::error('Wasender notify failed (manual payment)', ['error' => $e->getMessage()]);
        }

        if ($isCodes) {
            // After creating a pending request, the product may become effectively out-of-stock.
            $this->forgetCodesPageCache();
        }

        return redirect()->route('website.diamonds.manual_payment.thanks', ['reference' => $mpr->reference]);
    }

    public function thanks(string $reference)
    {
        $mpr = ManualPaymentRequest::query()
            ->where('reference', $reference)
            ->with(['product'])
            ->firstOrFail();

        return view('website.diamonds.manual_payment_thanks', [
            'mpr' => $mpr,
            'pageTitle' => 'تم استلام طلبك',
        ]);
    }
}

