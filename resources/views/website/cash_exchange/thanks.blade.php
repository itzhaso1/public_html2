@extends('website.layouts.common.website')

@section('pageTitle')
{{ $pageTitle ?? 'تم استلام طلبك' }}
@endsection

@section('content')
@php
    $status = (string) ($req->status ?? 'pending');
    $statusLabel = match ($status) {
        'completed' => 'مكتمل',
        'rejected' => 'مرفوض',
        default => 'قيد المراجعة',
    };
    $statusClass = match ($status) {
        'completed' => 'text-green-700',
        'rejected' => 'text-red-700',
        default => 'text-yellow-700',
    };
    $statusIcon = match ($status) {
        'completed' => '✅',
        'rejected' => '❌',
        default => '✅',
    };
    $statusMsg = match ($status) {
        'completed' => 'تم إكمال طلبك ✅',
        'rejected' => 'تم رفض طلبك ❌',
        default => 'تم استلام طلبك ✅',
    };
@endphp
<section class="max-w-3xl mx-auto px-4 pt-6 pb-12" dir="rtl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="text-center">
            <div class="text-4xl">{{ $statusIcon }}</div>
            <h2 class="mt-2 text-2xl font-extrabold text-gray-900">{{ $statusMsg }}</h2>
            <p class="mt-1 text-sm text-gray-600">حالة الطلب: {{ $statusLabel }}.</p>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">رقم الطلب</div>
                <div class="mt-1 font-mono font-extrabold text-gray-900 select-all">{{ $req->reference }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">الحالة</div>
                <div class="mt-1 font-extrabold {{ $statusClass }}">{{ $statusLabel }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">الفئة</div>
                <div class="mt-1 font-bold text-gray-900">{{ $req->offer?->name ?? '—' }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">المبلغ بالكاش</div>
                <div class="mt-1 font-extrabold text-green-700">{{ $req->cash_value }} {{ $req->currency }}</div>
            </div>
        </div>

        @if(!empty($req->admin_note))
            <div class="mt-4 rounded-2xl border border-blue-200 bg-blue-50 p-4">
                <div class="text-sm font-extrabold text-blue-900">ملاحظة</div>
                <div class="mt-1 text-sm text-blue-900/90 whitespace-pre-line">{{ $req->admin_note }}</div>
            </div>
        @endif

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('home') }}"
               class="flex-1 inline-flex items-center justify-center rounded-xl bg-black px-5 py-3 text-sm font-extrabold text-white hover:bg-gray-800 transition">
                الرئيسية
            </a>
            <a href="{{ route('website.cash_exchange.index') }}"
               class="flex-1 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-800 hover:bg-gray-50 transition">
                طلب جديد
            </a>
        </div>
    </div>
</section>
@endsection

