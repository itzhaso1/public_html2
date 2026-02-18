@extends('website.layouts.common.website')

@section('pageTitle')
{{ $pageTitle ?? 'تحويل الأموال / تبادل العملات' }}
@endsection

@section('content')
<section class="max-w-7xl mx-auto px-4 pt-6 pb-4" dir="rtl">
    <div class="rounded-3xl overflow-hidden border border-gray-200 shadow-sm bg-gradient-to-l from-gray-950 to-black text-white">
        <div class="p-6 sm:p-10">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white/90">
                        <span>متجر الممالك</span>
                        <span class="opacity-60">•</span>
                        <span>تحويل SAR ↔ USDT</span>
                    </div>
                    <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight">
                        تحويل الأموال / تبادل العملات
                    </h1>
                    <p class="mt-2 text-sm sm:text-base text-white/80 max-w-2xl leading-relaxed">
                        اختر نوع التحويل، أدخل المبلغ، وشاهد المبلغ المستلم قبل الإرسال.
                    </p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('customer.money_exchange.index') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-white/15 bg-white/10 px-4 py-2 text-sm font-extrabold hover:bg-white/15 transition">
                        طلباتي
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="max-w-4xl mx-auto px-4 pb-12" dir="rtl">
    @if($errors->any())
        <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $errors->first() }}
        </div>
    @endif

    @if(!$settings)
        <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-center">
            <div class="text-3xl mb-2">⛔</div>
            <div class="font-extrabold text-gray-900">الخدمة غير متاحة حالياً</div>
            <div class="text-sm text-gray-600 mt-1">يرجى المحاولة لاحقاً.</div>
        </div>
    @else
        <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
            <form method="POST" action="{{ route('website.money_exchange.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm cursor-pointer">
                        <div class="flex items-center gap-2">
                            <input type="radio" name="direction" value="sar_to_usdt" class="accent-blue-600"
                                   {{ old('direction', 'sar_to_usdt') === 'sar_to_usdt' ? 'checked' : '' }}>
                            <span class="font-extrabold">ريال → USDT</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">تحويل من ريال إلى USDT حسب سعر الصرف المحدد.</div>
                    </label>
                    <label class="rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm cursor-pointer">
                        <div class="flex items-center gap-2">
                            <input type="radio" name="direction" value="usdt_to_sar" class="accent-blue-600"
                                   {{ old('direction') === 'usdt_to_sar' ? 'checked' : '' }}>
                            <span class="font-extrabold">USDT → ريال</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">تحويل من USDT إلى ريال مع هامش ربح.</div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-extrabold text-gray-900 mb-2">المبلغ</label>
                    <input id="amountInput" type="number" step="0.0001" min="0" name="amount" value="{{ old('amount') }}"
                           class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono outline-none focus:ring-2 focus:ring-blue-500/30"
                           placeholder="مثال: 100">
                    @error('amount')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                    <div class="text-xs text-gray-500">المبلغ المستلم (تقديري)</div>
                    <div id="receivePreview" class="mt-1 text-2xl font-extrabold text-green-700">—</div>
                    <div class="mt-1 text-xs text-gray-500">يتم احتساب المبلغ تلقائيًا حسب إعدادات الأدمن.</div>
                </div>

                <div id="sarToUsdtFields" class="rounded-2xl border border-gray-200 bg-white p-4">
                    <div class="text-sm font-extrabold text-gray-900 mb-2">بيانات الاستلام (USDT)</div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <label class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm cursor-pointer">
                            <input type="radio" name="destination_type" value="trc20" class="accent-blue-600" {{ old('destination_type', 'trc20') === 'trc20' ? 'checked' : '' }}>
                            <span class="font-bold">TRC20</span>
                        </label>
                        <label class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm cursor-pointer">
                            <input type="radio" name="destination_type" value="binance_id" class="accent-blue-600" {{ old('destination_type') === 'binance_id' ? 'checked' : '' }}>
                            <span class="font-bold">Binance ID</span>
                        </label>
                        <label class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm cursor-pointer">
                            <input type="radio" name="destination_type" value="email" class="accent-blue-600" {{ old('destination_type') === 'email' ? 'checked' : '' }}>
                            <span class="font-bold">Email</span>
                        </label>
                    </div>
                    <div class="mt-3">
                        <label class="block text-xs font-extrabold text-gray-700 mb-1">العنوان / المعرف</label>
                        <input type="text" name="destination_value" value="{{ old('destination_value') }}"
                               class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono outline-none focus:ring-2 focus:ring-blue-500/30"
                               placeholder="ضع عنوان TRC20 أو Binance ID أو البريد">
                        @error('destination_value')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div id="usdtToSarFields" class="rounded-2xl border border-gray-200 bg-white p-4 hidden">
                    <div class="text-sm font-extrabold text-gray-900 mb-2">بيانات الاستلام (ريال)</div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-extrabold text-gray-700 mb-1">اسم البنك</label>
                            <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                                   class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/30">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-gray-700 mb-1">اسم صاحب الحساب</label>
                            <input type="text" name="account_name" value="{{ old('account_name') }}"
                                   class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/30">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-gray-700 mb-1">رقم الحساب (اختياري)</label>
                            <input type="text" name="account_number" value="{{ old('account_number') }}"
                                   class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono outline-none focus:ring-2 focus:ring-blue-500/30">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-gray-700 mb-1">IBAN (اختياري)</label>
                            <input type="text" name="iban" value="{{ old('iban') }}"
                                   class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono outline-none focus:ring-2 focus:ring-blue-500/30">
                        </div>
                    </div>
                    <div class="mt-2 text-xs text-gray-500">يجب إدخال رقم الحساب أو IBAN على الأقل.</div>
                </div>

                <input type="hidden" id="sarPerUsdt" value="{{ (float) $settings->sar_per_usdt }}">
                <input type="hidden" id="usdtToSarRate" value="{{ (float) $settings->usdt_to_sar_rate }}">

                <button type="submit"
                        class="w-full rounded-2xl bg-black px-5 py-3 text-sm font-extrabold text-white hover:bg-gray-800 transition">
                    إرسال الطلب
                </button>
            </form>
        </div>
    @endif
</section>
@endsection

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const dirInputs = document.querySelectorAll('input[name="direction"]');
    const amountInput = document.getElementById('amountInput');
    const receive = document.getElementById('receivePreview');
    const sarToUsdt = document.getElementById('sarToUsdtFields');
    const usdtToSar = document.getElementById('usdtToSarFields');
    const sarPerUsdt = parseFloat(document.getElementById('sarPerUsdt')?.value || '0');
    const usdtToSarRate = parseFloat(document.getElementById('usdtToSarRate')?.value || '0');

    const getDir = () => (document.querySelector('input[name="direction"]:checked')?.value || 'sar_to_usdt');

    const toggleFields = () => {
      const d = getDir();
      if (sarToUsdt) sarToUsdt.classList.toggle('hidden', d !== 'sar_to_usdt');
      if (usdtToSar) usdtToSar.classList.toggle('hidden', d !== 'usdt_to_sar');
    };

    const render = () => {
      const d = getDir();
      const amt = parseFloat(amountInput?.value || '0');
      if (!amt || amt <= 0) { receive.textContent = '—'; return; }
      if (d === 'sar_to_usdt' && sarPerUsdt > 0) {
        const usdt = (amt / sarPerUsdt);
        receive.textContent = usdt.toFixed(4) + ' USDT';
      } else if (d === 'usdt_to_sar' && usdtToSarRate > 0) {
        const sar = (amt * usdtToSarRate);
        receive.textContent = sar.toFixed(2) + ' SAR';
      } else {
        receive.textContent = '—';
      }
    };

    dirInputs.forEach(i => i.addEventListener('change', () => { toggleFields(); render(); }));
    amountInput?.addEventListener('input', render);
    toggleFields(); render();
  });
</script>
@endpush

