<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CashExchangeRequest;
use Illuminate\Http\Request;

class CashExchangeRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->query('status', 'pending');
        if (!in_array($status, ['pending', 'completed', 'all'], true)) {
            $status = 'pending';
        }

        $q = CashExchangeRequest::query()->with(['user', 'offer'])->latest();
        if ($status !== 'all') {
            $q->where('status', $status);
        }

        $requests = $q->paginate(40)->withQueryString();

        return view('dashboard.admin.cash_exchange.requests.index', [
            'pageTitle' => 'طلبات استبدال الرصيد (كاش)',
            'requests' => $requests,
            'status' => $status,
        ]);
    }

    public function show(CashExchangeRequest $cashExchangeRequest)
    {
        $cashExchangeRequest->load(['user', 'offer']);

        return view('dashboard.admin.cash_exchange.requests.show', [
            'pageTitle' => 'تفاصيل طلب الاستبدال',
            'req' => $cashExchangeRequest,
        ]);
    }

    public function complete(Request $request, CashExchangeRequest $cashExchangeRequest)
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $cashExchangeRequest->update([
            'status' => 'completed',
            'completed_at' => now(),
            'admin_note' => $data['admin_note'] ?? $cashExchangeRequest->admin_note,
        ]);

        return back()->with('success', 'تم تغيير الحالة إلى مكتمل ✅');
    }

    public function updateNote(Request $request, CashExchangeRequest $cashExchangeRequest)
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $cashExchangeRequest->update([
            'admin_note' => $data['admin_note'] ?? null,
        ]);

        return back()->with('success', 'تم حفظ الملاحظة ✅');
    }
}

