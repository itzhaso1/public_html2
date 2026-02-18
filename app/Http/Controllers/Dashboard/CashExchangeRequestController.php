<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CashExchangeRequest;
use App\Support\WhatsApp\WhatsAppNumber;
use App\Support\WhatsApp\WasenderNotifier;
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
        if ((string) ($cashExchangeRequest->status ?? '') !== 'pending') {
            return back()->withErrors(['error' => 'لا يمكن تنفيذ هذا الإجراء لأن الطلب ليس قيد المراجعة.']);
        }

        $oldStatus = (string) ($cashExchangeRequest->status ?? '');
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $cashExchangeRequest->update([
            'status' => 'completed',
            'completed_at' => now(),
            'admin_note' => $data['admin_note'] ?? $cashExchangeRequest->admin_note,
        ]);

        if ($oldStatus !== 'completed') {
            $this->notifyCustomer($cashExchangeRequest);
        }

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

    public function destroy(Request $request, CashExchangeRequest $cashExchangeRequest)
    {
        $request->validate([
            'confirm' => ['required', 'in:DELETE'],
        ]);

        $cashExchangeRequest->delete();

        return redirect()
            ->route('admin.cash_exchange.requests.index', ['status' => 'all'])
            ->with('success', 'تم حذف الطلب ✅');
    }

    public function bulkDelete(Request $request)
    {
        $data = $request->validate([
            'confirm' => ['required', 'in:DELETE'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = array_values(array_unique(array_map('intval', $data['ids'] ?? [])));
        if (empty($ids)) {
            return back()->withErrors(['error' => 'لم يتم تحديد طلبات للحذف.']);
        }

        CashExchangeRequest::query()->whereIn('id', $ids)->delete();

        return back()->with('success', 'تم حذف الطلبات المحددة ✅');
    }

    public function deleteAll(Request $request)
    {
        $data = $request->validate([
            'confirm' => ['required', 'in:DELETE'],
            'status' => ['nullable', 'in:pending,completed,all'],
        ]);

        $status = (string) ($data['status'] ?? $request->query('status', 'all'));
        if (!in_array($status, ['pending', 'completed', 'all'], true)) {
            $status = 'all';
        }

        $q = CashExchangeRequest::query();
        if ($status !== 'all') {
            $q->where('status', $status);
        }
        $q->delete();

        return back()->with('success', 'تم حذف الطلبات ✅');
    }

    private function notifyCustomer(CashExchangeRequest $req): void
    {
        if (! (bool) config('services.wasender.enabled', false)) return;
        if (! (bool) config('services.wasender.notify_customers', true)) return;

        try { $req->loadMissing(['user', 'user.profile', 'offer']); } catch (\Throwable $e) {}

        $to = WhatsAppNumber::normalize($req->user?->phone ?? '');
        if ($to === '') $to = WhatsAppNumber::normalize($req->user?->profile?->phone ?? '');
        if ($to === '') return;

        $app = (string) config('app.name', 'المتجر');
        $offer = (string) ($req->offer?->name ?? '');
        $note = trim((string) ($req->admin_note ?? ''));
        $noteLine = $note !== '' ? ("\nملاحظة: " . mb_substr($note, 0, 180)) : '';

        $text = trim(
            "{$app}\n" .
            "تم إكمال طلبك ✅\n" .
            "الخدمة: استبدال رصيدك كاش\n" .
            "رقم الطلب: {$req->reference}\n" .
            ($offer !== '' ? "الفئة: {$offer}\n" : '') .
            $noteLine
        );

        WasenderNotifier::sendAfterCommit($to, $text);
    }
}

