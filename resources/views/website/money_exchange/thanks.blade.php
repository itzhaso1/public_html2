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

        @if(!empty($settings))
            @php
                $hasSarTransferInfo = (string) ($settings->receive_sar_bank_name ?? '') !== ''
                    || (string) ($settings->receive_sar_account_name ?? '') !== ''
                    || (string) ($settings->receive_sar_account_number ?? '') !== ''
                    || (string) ($settings->receive_sar_iban ?? '') !== ''
                    || (string) ($settings->receive_sar_note ?? '') !== '';

                $hasUsdtTransferInfo = (string) ($settings->receive_usdt_trc20_address ?? '') !== ''
                    || (string) ($settings->receive_usdt_binance_id ?? '') !== ''
                    || (string) ($settings->receive_usdt_note ?? '') !== '';
            @endphp

            <div class="mt-6 rounded-2xl border border-gray-100 bg-white p-5">
                <div class="text-sm font-extrabold text-gray-900 mb-2">معلومات التحويل</div>

                @if(($req->direction ?? '') === 'sar_to_usdt')
                    <div class="text-xs font-extrabold text-gray-700 mb-2">أرسل مبلغ SAR إلى بيانات الإدارة التالية:</div>
                    @if(!$hasSarTransferInfo)
                        <div class="text-sm text-gray-600">لم يتم تحديد بيانات التحويل بعد. تواصل مع الدعم.</div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            @if(($settings->receive_sar_bank_name ?? '') !== '')
                                <div>
                                    <div class="text-[11px] text-gray-500">اسم البنك</div>
                                    <div class="mt-0.5 font-mono font-extrabold text-gray-900 select-all">{{ $settings->receive_sar_bank_name }}</div>
                                </div>
                            @endif
                            @if(($settings->receive_sar_account_name ?? '') !== '')
                                <div>
                                    <div class="text-[11px] text-gray-500">اسم صاحب الحساب</div>
                                    <div class="mt-0.5 font-mono font-extrabold text-gray-900 select-all">{{ $settings->receive_sar_account_name }}</div>
                                </div>
                            @endif
                            @if(($settings->receive_sar_account_number ?? '') !== '')
                                <div>
                                    <div class="text-[11px] text-gray-500">رقم الحساب</div>
                                    <div class="mt-0.5 font-mono font-extrabold text-gray-900 select-all">{{ $settings->receive_sar_account_number }}</div>
                                </div>
                            @endif
                            @if(($settings->receive_sar_iban ?? '') !== '')
                                <div>
                                    <div class="text-[11px] text-gray-500">IBAN</div>
                                    <div class="mt-0.5 font-mono font-extrabold text-gray-900 select-all">{{ $settings->receive_sar_iban }}</div>
                                </div>
                            @endif
                        </div>
                        @if(($settings->receive_sar_note ?? '') !== '')
                            <div class="mt-2 text-xs text-gray-600">{{ $settings->receive_sar_note }}</div>
                        @endif
                    @endif
                @else
                    <div class="text-xs font-extrabold text-gray-700 mb-2">أرسل مبلغ USDT إلى بيانات الإدارة التالية:</div>
                    @if(!$hasUsdtTransferInfo)
                        <div class="text-sm text-gray-600">لم يتم تحديد بيانات التحويل بعد. تواصل مع الدعم.</div>
                    @else
                        @if(($settings->receive_usdt_trc20_address ?? '') !== '')
                            <div>
                                <div class="text-[11px] text-gray-500">عنوان TRC20 (USDT)</div>
                                <div class="mt-0.5 font-mono font-extrabold text-gray-900 select-all break-all">{{ $settings->receive_usdt_trc20_address }}</div>
                            </div>
                        @endif
                        @if(($settings->receive_usdt_binance_id ?? '') !== '')
                            <div class="mt-3">
                                <div class="text-[11px] text-gray-500">Binance ID</div>
                                <div class="mt-0.5 font-mono font-extrabold text-gray-900 select-all">{{ $settings->receive_usdt_binance_id }}</div>
                            </div>
                        @endif
                        @if(($settings->receive_usdt_note ?? '') !== '')
                            <div class="mt-2 text-xs text-gray-600">{{ $settings->receive_usdt_note }}</div>
                        @endif
                    @endif
                @endif
            </div>
        @endif

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

