@extends('website.layouts.common.website')

@section('pageTitle')
{{ $pageTitle ?? 'تفاصيل الطلب' }}
@endsection

@section('content')
@php
    $statusLabel = ($req->status ?? '') === 'completed' ? 'مكتمل' : 'قيد المراجعة';
    $statusClass = ($req->status ?? '') === 'completed'
        ? 'bg-green-100 text-green-800 border-green-200'
        : 'bg-yellow-100 text-yellow-800 border-yellow-200';

    $mask = function (?string $v) {
        $v = trim((string) $v);
        if ($v === '') return '—';
        $len = mb_strlen($v);
        if ($len <= 6) return str_repeat('•', max(0, $len - 2)) . mb_substr($v, -2);
        return str_repeat('•', $len - 4) . mb_substr($v, -4);
    };
@endphp

<section class="max-w-3xl mx-auto px-4 pt-6 pb-12" dir="rtl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">تفاصيل طلب الاستبدال</h2>
                <p class="mt-1 text-sm text-gray-600">تابع حالة طلبك ومعلوماته.</p>
            </div>
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold {{ $statusClass }}">
                {{ $statusLabel }}
            </span>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">رقم الطلب</div>
                <div class="mt-1 font-mono font-extrabold text-gray-900 select-all">{{ $req->reference }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">تاريخ الطلب</div>
                <div class="mt-1 font-bold text-gray-900">{{ $req->created_at?->format('Y-m-d H:i') }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">الفئة</div>
                <div class="mt-1 font-bold text-gray-900">{{ $req->offer?->name ?? '—' }}</div>
                <div class="mt-1 text-xs text-gray-500">قيمة الرصيد: {{ (int) $req->face_value }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">المبلغ بالكاش</div>
                <div class="mt-1 font-extrabold text-green-700">{{ number_format((float)$req->cash_value, 2) }} {{ $req->currency }}</div>
                @if(($req->status ?? '') === 'completed' && $req->completed_at)
                    <div class="mt-1 text-xs text-gray-500">تم الإكمال: {{ $req->completed_at?->format('Y-m-d H:i') }}</div>
                @endif
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-4">
            <div class="text-sm font-extrabold text-gray-900 mb-2">بيانات التحويل البنكي</div>
            <div class="text-sm text-gray-700 space-y-2">
                <div><span class="text-gray-500">اسم البنك:</span> <span class="font-bold">{{ $req->bank_name }}</span></div>
                <div><span class="text-gray-500">اسم صاحب الحساب:</span> <span class="font-bold">{{ $req->account_name }}</span></div>
                @if($req->account_number)
                    <div><span class="text-gray-500">رقم الحساب:</span> <span class="font-mono font-bold">{{ $mask($req->account_number) }}</span></div>
                @endif
                @if($req->iban)
                    <div><span class="text-gray-500">IBAN:</span> <span class="font-mono font-bold">{{ $mask($req->iban) }}</span></div>
                @endif
            </div>
            <div class="mt-3 text-xs text-gray-500">ملاحظة: لا نعرض كود البطاقة هنا حفاظاً على الخصوصية.</div>
        </div>

        @if(!empty($req->admin_note))
            <div class="mt-4 rounded-2xl border border-blue-200 bg-blue-50 p-4">
                <div class="text-sm font-extrabold text-blue-900">ملاحظة</div>
                <div class="mt-1 text-sm text-blue-900/90 whitespace-pre-line">{{ $req->admin_note }}</div>
            </div>
        @endif

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('customer.purchases') }}"
               class="flex-1 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-800 hover:bg-gray-50 transition">
                رجوع لمشترياتي
            </a>
            <a href="{{ route('website.cash_exchange.index') }}"
               class="flex-1 inline-flex items-center justify-center rounded-xl bg-black px-5 py-3 text-sm font-extrabold text-white hover:bg-gray-800 transition">
                طلب جديد
            </a>
        </div>
    </div>
</section>
@endsection

