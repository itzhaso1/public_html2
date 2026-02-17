@extends('website.layouts.common.website')

@section('pageTitle')
{{ $pageTitle ?? 'تم استلام طلبك' }}
@endsection

@section('content')
@php
    $product = $mpr->product;
    $isCodes = ($product?->service_type ?? null) === 'codes';
    $methods = (array) config('bank.methods', []);
    $methodKey = $mpr->payment_method ?? null;
    $method = $methodKey && isset($methods[$methodKey]) ? $methods[$methodKey] : null;
@endphp

@include('website.diamonds.partials.header', [
    'title' => 'تم استلام طلبك',
    'subtitle' => 'طلبك قيد المراجعة وسيتم تنفيذ الشحن بعد التأكيد.',
    'active' => $isCodes ? 'codes' : 'charge',
])

<section class="max-w-3xl mx-auto px-4 pb-12" dir="rtl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="text-center">
            <div class="text-4xl">✅</div>
            <h2 class="mt-2 text-2xl font-extrabold text-gray-900">تم استلام طلب الدفع اليدوي</h2>
            <p class="mt-1 text-sm text-gray-600">احتفظ برقم الطلب للمتابعة.</p>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">رقم الطلب</div>
                <div class="mt-1 font-extrabold text-gray-900 select-all">{{ $mpr->reference }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">الحالة</div>
                <div class="mt-1 font-extrabold text-yellow-700">قيد المراجعة</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">الباقة</div>
                <div class="mt-1 font-bold text-gray-900">{{ $product?->name }}</div>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-xs text-gray-500">Player ID</div>
                <div class="mt-1 font-bold text-gray-900 select-all">{{ $mpr->player_id }}</div>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-4">
            <div class="text-sm font-extrabold text-gray-900 mb-2">طريقة الدفع</div>
            @if($method)
                <div class="text-sm text-gray-700 space-y-1">
                    <div class="font-bold">{{ $method['title'] ?? $methodKey }}</div>
                    @if($methodKey === 'sa_bank')
                        @if(!empty($method['bank_name'])) <div><span class="text-gray-500">البنك:</span> <span class="font-bold select-all">{{ $method['bank_name'] }}</span></div> @endif
                        @if(!empty($method['account_name'])) <div><span class="text-gray-500">اسم الحساب:</span> <span class="font-bold select-all">{{ $method['account_name'] }}</span></div> @endif
                        @if(!empty($method['account_number'])) <div><span class="text-gray-500">رقم الحساب:</span> <span class="font-bold select-all">{{ $method['account_number'] }}</span></div> @endif
                        @if(!empty($method['iban'])) <div><span class="text-gray-500">IBAN:</span> <span class="font-bold select-all">{{ $method['iban'] }}</span></div> @endif
                    @elseif($methodKey === 'jo_click')
                        @if(!empty($method['bank_name'])) <div><span class="text-gray-500">البنك:</span> <span class="font-bold select-all">{{ $method['bank_name'] }}</span></div> @endif
                        @if(!empty($method['account_name'])) <div><span class="text-gray-500">الاسم:</span> <span class="font-bold select-all">{{ $method['account_name'] }}</span></div> @endif
                        @if(!empty($method['click_id'])) <div><span class="text-gray-500">Click ID:</span> <span class="font-bold select-all">{{ $method['click_id'] }}</span></div> @endif
                    @elseif($methodKey === 'binance_trc20')
                        <div><span class="text-gray-500">Network:</span> <span class="font-bold select-all">{{ $method['network'] ?? 'TRC20' }}</span></div>
                        @if(!empty($method['address'])) <div><span class="text-gray-500">Address:</span> <span class="font-mono text-xs select-all">{{ $method['address'] }}</span></div> @endif
                        @if(!empty($method['link']))
                            <div>
                                <span class="text-gray-500">Link:</span>
                                <a class="text-blue-600 underline" href="{{ $method['link'] }}" target="_blank">فتح الرابط</a>
                                <span class="mx-1 text-gray-400">|</span>
                                <span class="text-blue-700 font-bold select-all" data-copy-text="{{ $method['link'] }}">نسخ الرابط</span>
                            </div>
                        @endif
                    @endif
                    <div class="pt-2 text-xs text-gray-500">بعد التحويل سيتم تنفيذ الطلب بعد التأكيد.</div>
                </div>
            @else
                <div class="text-sm text-gray-600">تم استلام الطلب. سيتم التنفيذ بعد التأكيد.</div>
            @endif
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="{{ $isCodes ? route('website.diamonds.codes') : route('website.diamonds.charge') }}"
               class="flex-1 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-bold text-gray-800 hover:bg-gray-50 transition">
                الرجوع لقسم الدايموند
            </a>
            <a href="{{ route('home') }}"
               class="flex-1 inline-flex items-center justify-center rounded-xl bg-black px-5 py-3 text-sm font-extrabold text-white hover:bg-gray-800 transition">
                الرئيسية
            </a>
        </div>
    </div>
</section>
@endsection

