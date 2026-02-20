@extends('website.layouts.common.website')

@section('pageTitle')
{{ $pageTitle ?? 'تفاصيل طلب التحويل' }}
@endsection

@section('content')
@php
    $dirLabel = ($req->direction ?? '') === 'usdt_to_sar' ? 'USDT → ريال' : 'ريال → USDT';
    $statusLabel = match($req->status) {
        'completed' => 'مكتمل',
        'rejected' => 'مرفوض',
        default => 'معلق',
    };
    $statusClass = match($req->status) {
        'completed' => 'bg-green-100 text-green-800 border-green-200',
        'rejected' => 'bg-red-100 text-red-800 border-red-200',
        default => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    };
@endphp

<section class="max-w-3xl mx-auto px-4 pt-6 pb-12" dir="rtl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">تفاصيل طلب التحويل</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $dirLabel }}</p>
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
                <div class="text-xs text-gray-500">المبلغ المحول</div>
                <div class="mt-1 font-extrabold text-gray-900">{{ $req->amount_from }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">المبلغ المستلم</div>
                <div class="mt-1 font-extrabold text-green-700">{{ $req->amount_to }}</div>
            </div>
        </div>

        @if(!empty($req->admin_note))
            <div class="mt-4 rounded-2xl border border-blue-200 bg-blue-50 p-4">
                <div class="text-sm font-extrabold text-blue-900">ملاحظة</div>
                <div class="mt-1 text-sm text-blue-900/90 whitespace-pre-line">{{ $req->admin_note }}</div>
            </div>
        @endif

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('customer.money_exchange.index') }}"
               class="flex-1 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-800 hover:bg-gray-50 transition">
                رجوع لطلباتي
            </a>
            <a href="{{ route('website.money_exchange.index') }}"
               class="flex-1 inline-flex items-center justify-center rounded-xl bg-black px-5 py-3 text-sm font-extrabold text-white hover:bg-gray-800 transition">
                طلب جديد
            </a>
        </div>
    </div>
</section>
@endsection

