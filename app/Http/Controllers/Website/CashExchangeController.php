<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\CashExchangeOffer;
use App\Models\CashExchangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CashExchangeController extends Controller
{
    public function index()
    {
        $offers = CashExchangeOffer::query()
            ->where('enabled', true)
            ->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->get();

        return view('website.cash_exchange.index', [
            'pageTitle' => 'استبدل رصيدك كاش',
            'offers' => $offers,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'offer_id' => ['required', 'integer', 'exists:cash_exchange_offers,id'],
            'card_code' => ['required', 'string', 'min:3', 'max:500'],
            'bank_name' => ['required', 'string', 'max:190'],
            'account_name' => ['required', 'string', 'max:190'],
            'account_number' => ['nullable', 'string', 'max:190'],
            'iban' => ['nullable', 'string', 'max:190'],
        ]);

        if (empty($data['account_number']) && empty($data['iban'])) {
            return back()->withErrors(['iban' => 'ضع رقم الحساب أو IBAN.'])->withInput();
        }

        $offer = CashExchangeOffer::query()
            ->whereKey($data['offer_id'])
            ->where('enabled', true)
            ->first();

        if (! $offer) {
            return back()->withErrors(['offer_id' => 'هذه الفئة غير متاحة حالياً.'])->withInput();
        }

        $ref = strtoupper(Str::random(10));
        while (CashExchangeRequest::query()->where('reference', $ref)->exists()) {
            $ref = strtoupper(Str::random(10));
        }

        CashExchangeRequest::create([
            'reference' => $ref,
            'user_id' => $request->user()->id,
            'offer_id' => $offer->id,
            'face_value' => (int) $offer->face_value,
            'cash_value' => (float) $offer->cash_value,
            'currency' => (string) $offer->currency,
            'card_code' => trim((string) $data['card_code']),
            'bank_name' => trim((string) $data['bank_name']),
            'account_name' => trim((string) $data['account_name']),
            'account_number' => !empty($data['account_number']) ? trim((string) $data['account_number']) : null,
            'iban' => !empty($data['iban']) ? trim((string) $data['iban']) : null,
            'status' => 'pending',
        ]);

        return redirect()->route('website.cash_exchange.thanks', ['reference' => $ref]);
    }

    public function thanks(string $reference)
    {
        $req = CashExchangeRequest::query()
            ->with(['offer'])
            ->where('reference', $reference)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('website.cash_exchange.thanks', [
            'pageTitle' => 'تم استلام طلبك',
            'req' => $req,
        ]);
    }

    public function show(string $reference)
    {
        $req = CashExchangeRequest::query()
            ->with(['offer'])
            ->where('reference', $reference)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('website.cash_exchange.show', [
            'pageTitle' => 'تفاصيل طلب الاستبدال',
            'req' => $req,
        ]);
    }
}

