<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CashExchangeOffer;
use Illuminate\Http\Request;

class CashExchangeOfferController extends Controller
{
    public function index()
    {
        $offers = CashExchangeOffer::query()
            ->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->paginate(30);

        return view('dashboard.admin.cash_exchange.offers.index', [
            'pageTitle' => 'فئات استبدال الرصيد (كاش)',
            'offers' => $offers,
        ]);
    }

    public function create()
    {
        return view('dashboard.admin.cash_exchange.offers.create', [
            'pageTitle' => 'إضافة فئة جديدة',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'face_value' => ['required', 'integer', 'min:1', 'max:1000000'],
            'cash_value' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'enabled' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000000'],
        ]);

        CashExchangeOffer::create([
            'name' => trim($data['name']),
            'face_value' => (int) $data['face_value'],
            'cash_value' => (float) $data['cash_value'],
            'currency' => strtoupper(trim($data['currency'])),
            'enabled' => (bool) ($data['enabled'] ?? false),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        return redirect()
            ->route('admin.cash_exchange.offers.index')
            ->with('success', 'تمت إضافة الفئة بنجاح ✅');
    }

    public function edit(CashExchangeOffer $offer)
    {
        return view('dashboard.admin.cash_exchange.offers.edit', [
            'pageTitle' => 'تعديل الفئة',
            'offer' => $offer,
        ]);
    }

    public function update(Request $request, CashExchangeOffer $offer)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'face_value' => ['required', 'integer', 'min:1', 'max:1000000'],
            'cash_value' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'enabled' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000000'],
        ]);

        $offer->update([
            'name' => trim($data['name']),
            'face_value' => (int) $data['face_value'],
            'cash_value' => (float) $data['cash_value'],
            'currency' => strtoupper(trim($data['currency'])),
            'enabled' => (bool) ($data['enabled'] ?? false),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ]);

        return redirect()
            ->route('admin.cash_exchange.offers.index')
            ->with('success', 'تم تحديث الفئة بنجاح ✅');
    }

    public function destroy(CashExchangeOffer $offer)
    {
        $offer->delete();

        return back()->with('success', 'تم حذف الفئة ✅');
    }
}

