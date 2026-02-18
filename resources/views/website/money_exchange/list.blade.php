@extends('website.layouts.common.website')

@section('pageTitle')
{{ $pageTitle ?? 'طلباتي - تحويل الأموال' }}
@endsection

@section('content')
<section class="max-w-5xl mx-auto px-4 py-8" dir="rtl">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">طلباتي - تحويل الأموال</h1>
            <p class="text-sm text-gray-600 mt-1">تتبع حالة طلبات التحويل (معلق/مكتمل/مرفوض).</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('website.money_exchange.index') }}"
               class="inline-flex items-center justify-center rounded-xl bg-black px-4 py-2 text-sm font-bold text-white hover:bg-gray-800 transition">
                طلب جديد
            </a>
            <a href="{{ route('customer.dashboard') }}"
               class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold hover:bg-gray-50 transition">
                العودة للحساب
            </a>
        </div>
    </div>

    <div class="mt-5 space-y-3">
        @forelse($requests as $r)
            @php
                $dirLabel = ($r->direction ?? '') === 'usdt_to_sar' ? 'USDT → ريال' : 'ريال → USDT';
                $statusLabel = match($r->status) {
                    'completed' => 'مكتمل',
                    'rejected' => 'مرفوض',
                    default => 'معلق',
                };
                $statusClass = match($r->status) {
                    'completed' => 'bg-green-100 text-green-800 border-green-200',
                    'rejected' => 'bg-red-100 text-red-800 border-red-200',
                    default => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                };
            @endphp

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="text-xs text-gray-500">رقم الطلب</div>
                        <div class="font-mono text-sm select-all">{{ $r->reference }}</div>
                        <div class="mt-2 font-extrabold text-gray-900">{{ $dirLabel }}</div>
                        <div class="text-sm text-gray-600 mt-1">
                            من: <span class="font-bold">{{ $r->amount_from }}</span>
                            <span class="mx-1 text-gray-400">→</span>
                            إلى: <span class="font-bold">{{ $r->amount_to }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-start sm:items-end gap-2">
                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                        <div class="text-xs text-gray-500">{{ $r->created_at?->format('Y-m-d H:i') }}</div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('customer.money_exchange.show', ['reference' => $r->reference]) }}"
                       class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-xs font-extrabold text-gray-800 hover:bg-gray-50 transition">
                        عرض التفاصيل
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
                <div class="text-3xl mb-2">💱</div>
                <h3 class="font-extrabold text-gray-900">لا توجد طلبات تحويل بعد</h3>
                <p class="text-sm text-gray-600 mt-1">ابدأ بطلب جديد وسيظهر هنا.</p>
                <a href="{{ route('website.money_exchange.index') }}"
                   class="mt-4 inline-flex items-center justify-center rounded-xl bg-black px-5 py-2.5 text-sm font-bold text-white hover:bg-gray-800 transition">
                    إنشاء طلب
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-5">
        {{ $requests->links() }}
    </div>
</section>
@endsection

