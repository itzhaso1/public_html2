<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MoneyExchangeRequest;
use Illuminate\Http\Request;

class MoneyExchangeRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->query('status', 'pending');
        if (!in_array($status, ['pending', 'completed', 'rejected', 'all'], true)) {
            $status = 'pending';
        }

        $q = MoneyExchangeRequest::query()->with(['user'])->latest();
        if ($status !== 'all') {
            $q->where('status', $status);
        }

        $requests = $q->paginate(40)->withQueryString();

        return view('dashboard.admin.money_exchange.requests.index', [
            'pageTitle' => 'طلبات تحويل الأموال (SAR ↔ USDT)',
            'requests' => $requests,
            'status' => $status,
        ]);
    }

    public function show(MoneyExchangeRequest $moneyExchangeRequest)
    {
        $moneyExchangeRequest->load(['user']);

        return view('dashboard.admin.money_exchange.requests.show', [
            'pageTitle' => 'تفاصيل طلب التحويل',
            'req' => $moneyExchangeRequest,
        ]);
    }

    public function complete(Request $request, MoneyExchangeRequest $moneyExchangeRequest)
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $moneyExchangeRequest->update([
            'status' => 'completed',
            'completed_at' => now(),
            'admin_note' => $data['admin_note'] ?? $moneyExchangeRequest->admin_note,
        ]);

        return back()->with('success', 'تم تغيير الحالة إلى مكتمل ✅');
    }

    public function reject(Request $request, MoneyExchangeRequest $moneyExchangeRequest)
    {
        $data = $request->validate([
            'admin_note' => ['required', 'string', 'max:2000'],
        ]);

        $moneyExchangeRequest->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'admin_note' => $data['admin_note'],
        ]);

        return back()->with('success', 'تم رفض الطلب ✅');
    }
}

