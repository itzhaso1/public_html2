@extends('website.layouts.common.website')

@section('pageTitle')
{{ $pageTitle ?? 'تم استلام طلبك' }}
@endsection

@section('content')
@php
    $dirLabel = ($req->direction ?? '') === 'usdt_to_sar' ? 'USDT → ريال' : 'ريال → USDT';
    $statusLabel = ($req->status ?? '') === 'completed' ? 'مكتمل' : (($req->status ?? '') === 'rejected' ? 'مرفوض' : 'معلق');
@endphp

<section class="max-w-3xl mx-auto px-4 pt-6 pb-12" dir="rtl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="text-center">
            <div class="text-4xl">✅</div>
            <h2 class="mt-2 text-2xl font-extrabold text-gray-900">تم استلام طلب التحويل</h2>
            <p class="mt-1 text-sm text-gray-600">طلبك {{ $statusLabel }} وسيتم التعامل معه من الإدارة.</p>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">رقم الطلب</div>
                <div class="mt-1 font-mono font-extrabold text-gray-900 select-all">{{ $req->reference }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">نوع التحويل</div>
                <div class="mt-1 font-bold text-gray-900">{{ $dirLabel }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">المبلغ المحوّل</div>
                <div class="mt-1 font-extrabold text-gray-900">{{ $req->amount_from }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">المبلغ المستلم</div>
                <div class="mt-1 font-extrabold text-green-700">{{ $req->amount_to }}</div>
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('customer.money_exchange.index') }}"
               class="flex-1 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-800 hover:bg-gray-50 transition">
                طلباتي
            </a>
            <a href="{{ route('website.money_exchange.index') }}"
               class="flex-1 inline-flex items-center justify-center rounded-xl bg-black px-5 py-3 text-sm font-extrabold text-white hover:bg-gray-800 transition">
                طلب جديد
            </a>
        </div>
    </div>
</section>
@endsection

