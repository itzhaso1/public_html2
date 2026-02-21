@extends('website.layouts.common.website')

@section('pageTitle')
{{ $pageTitle ?? 'الدفع اليدوي' }}
@endsection

@section('content')
@php
    $isCodes = ($product?->service_type ?? null) === 'codes';
@endphp

@include('website.diamonds.partials.header', [
    'title' => $isCodes ? 'أكواد ملابس' : 'شحن الجواهر',
    'subtitle' => 'اخترت الدفع اليدوي: حوّل المبلغ ثم ارفع إيصال التحويل.',
    'active' => $isCodes ? 'codes' : 'charge',
])

<section class="max-w-4xl mx-auto px-4 pb-12" dir="rtl">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
            <h2 class="text-xl font-extrabold text-gray-900">تفاصيل الباقة</h2>
            <div class="mt-3 rounded-2xl bg-gray-50 border border-gray-100 p-4">
                <div class="text-sm text-gray-700 font-bold">{{ $product->name }}</div>
                <div class="mt-1 text-xs text-gray-500">السعر</div>
                <div class="mt-1 text-3xl font-extrabold text-green-600 product-price"
                     data-base-price="{{ (float) $product->price }}">
                    <span class="current-price">ر.س {{ number_format((float) $product->price, 2) }}</span>
                </div>
            </div>

            <div class="mt-5">
                <div class="text-sm font-extrabold text-gray-900">بيانات التحويل البنكي</div>
                <div class="mt-2 text-sm text-gray-700 space-y-2">
                    @if(config('bank.bank_name'))
                        <div><span class="text-gray-500">البنك:</span> <span class="font-bold">{{ config('bank.bank_name') }}</span></div>
                    @endif
                    @if(config('bank.account_name'))
                        <div><span class="text-gray-500">اسم الحساب:</span> <span class="font-bold">{{ config('bank.account_name') }}</span></div>
                    @endif
                    @if(config('bank.account_number'))
                        <div><span class="text-gray-500">رقم الحساب:</span> <span class="font-bold select-all">{{ config('bank.account_number') }}</span></div>
                    @endif
                    @if(config('bank.iban'))
                        <div><span class="text-gray-500">IBAN:</span> <span class="font-bold select-all">{{ config('bank.iban') }}</span></div>
                    @endif
                    @if(config('bank.note'))
                        <div class="text-xs text-gray-500">{{ config('bank.note') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
            <h2 class="text-xl font-extrabold text-gray-900">إرسال طلب الدفع اليدوي</h2>
            <p class="mt-1 text-sm text-gray-600">
                @if($isCodes)
                    ارفع إيصال التحويل فقط، وسيتم تسليم الكود بعد الموافقة.
                @else
                    أدخل الـ ID وارفع إيصال التحويل.
                @endif
            </p>

            <form class="mt-4 space-y-4" method="POST" enctype="multipart/form-data"
                  action="{{ route('website.diamonds.manual_payment.store', $product) }}">
                @csrf

                @unless($isCodes)
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Player ID / ID الحساب</label>
                        <input type="text" name="player_id" value="{{ old('player_id') }}"
                               class="w-full rounded-xl border border-gray-200 px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-yellow-400/60"
                               placeholder="مثال: 123456789" required>
                        @error('player_id')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                @endunless

                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-1">إيصال التحويل</label>
                    <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.webp,.pdf"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2 text-sm bg-white" required>
                    <div class="text-xs text-gray-500 mt-1">الأنواع المسموحة: JPG/PNG/WEBP/PDF — حتى 5MB</div>
                    @error('receipt')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-black px-5 py-3 text-sm font-extrabold text-white hover:bg-yellow-400 hover:text-black transition">
                    إرسال الطلب
                </button>
            </form>

            @php($whatsappHref = \App\Support\WhatsApp::href())
            @if($whatsappHref)
                <a class="mt-3 w-full inline-flex items-center justify-center rounded-xl bg-[#25D366] px-5 py-3 text-sm font-extrabold text-white hover:brightness-95 transition"
                   target="_blank"
                   rel="noopener noreferrer"
                   href="{{ $whatsappHref }}">
                    إرسال الإيصال عبر واتساب (اختياري)
                </a>
            @endif
        </div>
    </div>
</section>
@endsection

