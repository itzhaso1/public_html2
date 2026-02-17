@extends('website.layouts.common.website')

@section('pageTitle')
{{ $pageTitle ?? 'تم استلام طلبك' }}
@endsection

@section('content')
<section class="max-w-3xl mx-auto px-4 pt-6 pb-12" dir="rtl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="text-center">
            <div class="text-4xl">✅</div>
            <h2 class="mt-2 text-2xl font-extrabold text-gray-900">تم استلام طلب الاستبدال</h2>
            <p class="mt-1 text-sm text-gray-600">طلبك قيد المراجعة وسيتم التحويل بعد التأكيد.</p>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">رقم الطلب</div>
                <div class="mt-1 font-mono font-extrabold text-gray-900 select-all">{{ $req->reference }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">الحالة</div>
                <div class="mt-1 font-extrabold text-yellow-700">قيد المراجعة</div>
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

