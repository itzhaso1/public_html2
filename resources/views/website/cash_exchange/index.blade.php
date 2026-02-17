@extends('website.layouts.common.website')

@section('pageTitle')
{{ $pageTitle ?? 'استبدل رصيدك كاش' }}
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
                        <span>خدمة جديدة</span>
                    </div>
                    <h1 class="mt-3 text-2xl sm:text-3xl font-extrabold tracking-tight">
                        استبدل رصيدك كاش
                    </h1>
                    <p class="mt-2 text-sm sm:text-base text-white/80 max-w-2xl leading-relaxed">
                        اختر فئة الرصيد، اكتب كود البطاقة، ثم أدخل بيانات حسابك البنكي لاستلام المبلغ.
                    </p>
                </div>

                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-white/75">
                    <span class="inline-flex items-center gap-1 rounded-full bg-white/10 px-3 py-1">🧾 قيد المراجعة</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-white/10 px-3 py-1">🔒 بيانات آمنة</span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-white/10 px-3 py-1">💬 دعم عربي</span>
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
    @if(session('success'))
        <div class="mt-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
        <form method="POST" action="{{ route('website.cash_exchange.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-extrabold text-gray-900 mb-2">فئة الرصيد</label>
                <select id="offerSelect" name="offer_id"
                        class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/30"
                        required>
                    <option value="">-- اختر الفئة --</option>
                    @foreach($offers as $o)
                        <option value="{{ $o->id }}"
                                data-face="{{ (int) $o->face_value }}"
                                data-cash="{{ (float) $o->cash_value }}"
                                data-currency="{{ $o->currency }}"
                                @selected(old('offer_id') == $o->id)>
                            {{ $o->name }} ({{ (int) $o->face_value }})
                        </option>
                    @endforeach
                </select>
                @error('offer_id')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror

                <div class="mt-3 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                    <div class="text-xs text-gray-500">المبلغ الذي سيستلمه العميل</div>
                    <div id="cashPreview" class="mt-1 text-2xl font-extrabold text-green-700">—</div>
                    <div class="mt-1 text-xs text-gray-500">يتم احتساب المبلغ تلقائيًا حسب إعدادات الداشبورد.</div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-extrabold text-gray-900 mb-2">كود بطاقة الشحن</label>
                <input type="text" name="card_code" value="{{ old('card_code') }}"
                       class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono outline-none focus:ring-2 focus:ring-blue-500/30"
                       placeholder="مثال: 1234-5678-XXXX-XXXX"
                       required>
                @error('card_code')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="pt-2 border-t border-gray-100">
                <div class="text-sm font-extrabold text-gray-900">بيانات الحساب البنكي للعميل</div>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-extrabold text-gray-700 mb-1">اسم البنك</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                               class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/30"
                               required>
                        @error('bank_name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-gray-700 mb-1">اسم صاحب الحساب</label>
                        <input type="text" name="account_name" value="{{ old('account_name') }}"
                               class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500/30"
                               required>
                        @error('account_name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-gray-700 mb-1">رقم الحساب (اختياري)</label>
                        <input type="text" name="account_number" value="{{ old('account_number') }}"
                               class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono outline-none focus:ring-2 focus:ring-blue-500/30"
                               placeholder="ضع رقم الحساب أو اتركه إذا وضعت IBAN">
                        @error('account_number')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-gray-700 mb-1">IBAN (اختياري)</label>
                        <input type="text" name="iban" value="{{ old('iban') }}"
                               class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-mono outline-none focus:ring-2 focus:ring-blue-500/30"
                               placeholder="ضع IBAN أو اتركه إذا وضعت رقم الحساب">
                        @error('iban')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-500">ملاحظة: يجب إدخال رقم الحساب أو IBAN على الأقل.</div>
            </div>

            <button type="submit"
                    class="w-full rounded-2xl bg-black px-5 py-3 text-sm font-extrabold text-white hover:bg-gray-800 transition">
                إرسال الطلب
            </button>
        </form>
    </div>
</section>
@endsection

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('offerSelect');
    const preview = document.getElementById('cashPreview');
    if (!select || !preview) return;

    const render = () => {
      const opt = select.options[select.selectedIndex];
      if (!opt || !opt.value) {
        preview.textContent = '—';
        return;
      }
      const cash = opt.getAttribute('data-cash');
      const cur = opt.getAttribute('data-currency') || '';
      preview.textContent = `${cash} ${cur}`;
    };

    select.addEventListener('change', render);
    render();
  });
</script>
@endpush

