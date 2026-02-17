@extends('website.layouts.common.website')

@section('pageTitle')
شحن الجواهر
@endsection

@section('content')
@php
    $fallbackImage = asset('img/قريبا.jpg');
    $products = $products ?? collect();
@endphp

@include('website.diamonds.partials.header', [
    'title' => 'شحن الجواهر',
    'subtitle' => 'اختر الباقة المناسبة، تنفيذ سريع ودعم عربي.',
    'active' => 'charge',
])

<section class="max-w-7xl mx-auto px-4 pb-10" dir="rtl">
    <div class="mt-4">
        @include('website.partials.currency_picker')
    </div>

    <div class="bg-white/70 backdrop-blur rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-yellow-400/15 flex items-center justify-center text-yellow-700 font-extrabold">
                    💎
                </div>
                <div>
                    <div class="text-sm text-gray-500">قسم الشحن</div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-gray-900">باقات شحن الجواهر</h2>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input id="diamondSearch"
                       type="search"
                       class="w-full sm:w-80 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-yellow-400/60"
                       placeholder="ابحث باسم الباقة...">
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs sm:text-sm">
            <div class="flex items-center gap-2 bg-white rounded-xl border border-gray-100 p-3">
                <span class="text-green-600 font-bold">✓</span>
                <span class="text-gray-700">تنفيذ سريع</span>
            </div>
            <div class="flex items-center gap-2 bg-white rounded-xl border border-gray-100 p-3">
                <span class="text-blue-600 font-bold">✓</span>
                <span class="text-gray-700">دعم عربي</span>
            </div>
            <div class="flex items-center gap-2 bg-white rounded-xl border border-gray-100 p-3">
                <span class="text-yellow-600 font-bold">✓</span>
                <span class="text-gray-700">دفع آمن</span>
            </div>
        </div>
    </div>

    @if($products->count() > 0)
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($products as $product)
                @php
                    $imageUrl = method_exists($product, 'getMediaUrl')
                        ? $product->getMediaUrl('product', $product, null, 'media', 'product')
                        : null;
                    $productImage = $imageUrl ?: $fallbackImage;
                    $title = $product->name ?? 'باقة شحن';
                    $desc = $product->description ?? $product->short_description ?? null;
                    $descText = $desc ? \Illuminate\Support\Str::limit(trim(strip_tags($desc)), 90) : 'شحن فوري وآمن';
                @endphp

                <article class="diamond-card bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden"
                         data-title="{{ mb_strtolower($title) }}">
                    <a href="{{ route('website.product.show', $product) }}" class="block">
                        <div class="aspect-[2/1] bg-gray-50">
                            <img src="{{ $productImage }}"
                                 alt="{{ $title }}"
                                 class="w-full h-full object-cover"
                                 loading="lazy"
                                 decoding="async">
                        </div>
                    </a>

                    <div class="p-4 flex flex-col gap-3">
                        <div>
                            <h3 class="font-extrabold text-gray-900 text-base sm:text-lg leading-snug">
                                {{ $title }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-600 leading-relaxed">
                                {{ $descText }}
                            </p>
                        </div>

                        <div class="flex items-end justify-between gap-3">
                            <div class="text-right">
                                <div class="text-xs text-gray-500">السعر</div>
                                <div class="text-lg font-extrabold text-green-600 product-price"
                                     data-base-price="{{ (float) $product->price }}">
                                    <span class="current-price">ر.س {{ number_format((float) $product->price, 2) }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('website.product.show', $product) }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-black px-4 py-2 text-sm font-bold text-white hover:bg-yellow-400 hover:text-black transition">
                                    عرض التفاصيل
                                    <span aria-hidden="true">›</span>
                                </a>
                                @if(config('bank.enabled'))
                                    <a href="{{ route('website.diamonds.manual_payment.create', $product) }}"
                                       class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-800 hover:bg-gray-50 transition">
                                        دفع يدوي
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="text-3xl mb-2">💎</div>
            <h3 class="font-extrabold text-gray-900">لا توجد باقات شحن متاحة حالياً</h3>
            <p class="text-sm text-gray-600 mt-1">جرّب لاحقاً أو تواصل معنا وسنساعدك.</p>
            <a href="{{ route('home') }}"
               class="mt-4 inline-flex items-center justify-center rounded-xl bg-black px-5 py-2.5 text-sm font-bold text-white hover:bg-gray-800 transition">
                الرجوع للرئيسية
            </a>
        </div>
    @endif
</section>
@endsection

@push('js')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('diamondSearch');
    const cards = document.querySelectorAll('.diamond-card');
    if (!input || !cards.length) return;

    const normalize = (s) => (s || '').toString().toLowerCase().trim();

    input.addEventListener('input', () => {
      const q = normalize(input.value);
      cards.forEach(card => {
        const title = normalize(card.dataset.title);
        card.style.display = !q || title.includes(q) ? '' : 'none';
      });
    });
  });
</script>
@endpush