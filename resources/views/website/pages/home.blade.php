@extends('website.layouts.common.website')

@push('css')
<style>
    .reviewsSwiper {
        padding-bottom: 40px;
    }

    .reviewsSwiper .swiper-slide {
        transition: all 0.4s ease;
    }

    .reviewsSwiper .swiper-slide-active {
        transform: scale(1.03);
        background: #fffef7;
        border-color: #facc15;
    }

    .reviewsSwiper .swiper-pagination {
        position: relative !important;
        bottom: 0 !important;
        margin-top: 1rem;
        margin-bottom: -10px;
        text-align: center;
    }

    .reviewsSwiper .swiper-pagination-bullet {
        background: #d1d5db;
        opacity: 1;
        width: 8px;
        height: 8px;
        margin: 0 4px !important;
        transition: all 0.3s ease;
    }

    .reviewsSwiper .swiper-pagination-bullet-active {
        background: #facc15;
        width: 12px;
        height: 12px;
    }

    /* تثبيت أبعاد السلايدر لتقليل CLS */
    .home-hero-swiper {
        aspect-ratio: 2 / 1;
        min-height: 300px;
    }

    .home-hero-swiper .swiper-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 0.5rem;
        display: block;
    }
</style>
@endpush

@section('pageTitle')
{{ $pageTitle }}
@endsection

@section('content')
@php
    $fallbackImage = asset('img/قريبا.jpg');
@endphp

<!-- السلايدر -->
<div class="my-4 px-2">
    <div class="swiper-container home-hero-swiper">
        <div class="swiper-wrapper">
            @foreach($sliders as $slider)
                @php
                    $imageUrl = $slider->getMediaUrl('slider', $slider, null, 'media', 'slider');
                    $sliderImage = $imageUrl ?: $fallbackImage;
                @endphp
                @if($imageUrl)
                    <div class="swiper-slide flex justify-center">
                        <img src="{{ $sliderImage }}"
                             alt="{{ $slider->name ?? 'slider' }}"
                             width="1200" height="600"
                             loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                             fetchpriority="{{ $loop->first ? 'high' : 'auto' }}"
                             decoding="async">
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<!-- اختيار العملة -->
<div class="mt-2">
    @include('website.partials.currency_picker')
</div>

<!-- أقسام سريعة -->
<div class="px-4 mt-4">
    <div class="grid grid-cols-2 gap-3 sm:gap-4">

        <!-- شحن جواهر -->
        <a href="{{ route('website.diamonds.charge') }}"
           class="group relative overflow-hidden rounded-2xl border border-yellow-200 bg-gradient-to-l from-yellow-50 to-white shadow-sm transition hover:shadow-md active:scale-[0.99]">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-center">
                    {{-- غيّر الصورة كما تريد --}}
                    <img src="{{ asset('public/uploads/oki/old.png') }}"
                         alt="شحن جواهر"
                         class="w-full h-28 sm:h-32 object-cover rounded-xl"
                         loading="lazy" decoding="async">
                </div>

                <div class="mt-2 text-center">
                    <span class="inline-block text-xs text-gray-500">القسم</span>
                    <h3 class="font-extrabold text-sm sm:text-base text-gray-900 mt-0.5">شحن جواهر</h3>
                </div>

                <p class="text-[11px] sm:text-sm text-gray-600 mt-2 text-center leading-relaxed">
                    ادخل للشحن واختر الباقة المناسبة
                </p>

                <div class="mt-3 flex justify-center">
                    <span class="inline-flex items-center gap-2 rounded-full bg-yellow-400/15 px-3 py-1 text-xs font-bold text-yellow-700">
                        <i class="bi bi-gem"></i>
                        دخول القسم
                    </span>
                </div>
            </div>
        </a>

        <!-- أكواد جواهر -->
        <a href="{{ route('website.diamonds.codes') }}"
           class="group relative overflow-hidden rounded-2xl border border-blue-200 bg-gradient-to-l from-blue-50 to-white shadow-sm transition hover:shadow-md active:scale-[0.99]">
            <div class="p-3 sm:p-4">
                <div class="flex items-center justify-center">
                    {{-- غيّر الصورة كما تريد --}}
                    <img src="{{ asset('public/uploads/oki/old.png') }}"
                         alt="أكواد جواهر"
                         class="w-full h-28 sm:h-32 object-cover rounded-xl"
                         loading="lazy" decoding="async">
                </div>

                <div class="mt-2 text-center">
                    <span class="inline-block text-xs text-gray-500">القسم</span>
                    <h3 class="font-extrabold text-sm sm:text-base text-gray-900 mt-0.5">أكواد ملابس</h3>
                </div>

                <p class="text-[11px] sm:text-sm text-gray-600 mt-2 text-center leading-relaxed">
                    ادخل لشراء/استخدام أكواد الجواهر
                </p>

                <div class="mt-3 flex justify-center">
                    <span class="inline-flex items-center gap-2 rounded-full bg-blue-500/10 px-3 py-1 text-xs font-bold text-blue-700">
                        <i class="bi bi-upc-scan"></i>
                        دخول القسم
                    </span>
                </div>
            </div>
        </a>

    </div>
</div>



<!-- الأقسام والمنتجات -->
@foreach($sections as $section)
    <div class="px-4 py-6">
        <h2 class="text-center font-bold text-xl mb-4">{{ $section->name }}</h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($section->products as $product)
                @php
                    $imageUrl = $product->getMediaUrl('product', $product, null, 'media', 'product');
                    $productImage = $imageUrl ?: $fallbackImage;
                    $isSold = $product->featured === 1;
                    $discountPercent = null;

                    if (!empty($product->price_before_discount) && $product->price_before_discount > 0) {
                        $discountPercent = round((($product->price_before_discount - $product->price) / $product->price_before_discount) * 100);
                    }
                @endphp

                <div class="relative bg-white p-3 rounded-lg shadow text-center overflow-hidden flex flex-col h-full">
                    @if($isSold)
                        <div class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded shadow">
                            مباع
                        </div>
                    @endif

                    @if(!$isSold && !empty($discountPercent) && $discountPercent > 0)
                        <div class="absolute top-2 right-2 bg-yellow-400 text-black text-xs font-bold px-2 py-1 rounded shadow">
                            خصم {{ $discountPercent }}%
                        </div>
                    @endif

                    <img src="{{ $productImage }}"
                         class="product-img mx-auto rounded-md object-cover w-full h-auto"
                         alt="{{ $product->name ?? 'Product' }}"
                         width="600" height="300"
                         loading="lazy"
                         decoding="async">

                    <h3 class="font-bold mt-2">{{ $product->name }}</h3>

                    <p class="text-gray-500 text-sm">
                        {{ $product->short_description ?? 'لا يوجد وصف لهذا المنتج' }}
                    </p>

                    @if(!empty($product->price_before_discount))
                        <p class="font-semibold mt-1 product-price text-red-600"
                           data-base-price="{{ $product->price }}"
                           data-base-old="{{ $product->price_before_discount }}">
                            <span class="current-price">ر.س {{ $product->price }}</span>
                            <span class="old-price text-gray-500 text-sm line-through">
                                {{ $product->price_before_discount }}
                            </span>
                        </p>
                    @else
                        <p class="font-semibold mt-1 product-price" data-base-price="{{ $product->price }}">
                            <span class="current-price">ر.س {{ $product->price }}</span>
                        </p>
                    @endif

                    @if($isSold)
                        <button class="mt-auto w-full bg-gray-400 text-white py-1 rounded text-sm cursor-not-allowed">
                            مباع
                        </button>
                    @else
                        <a href="{{ route('website.product.show', $product->id) }}"
                           class="mt-auto block w-full bg-black text-white py-1 rounded text-sm text-center hover:bg-gray-800 transition">
                            عرض تفاصيل
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endforeach

<!-- حسابات متجر الممالك -->
<div class="px-4 py-6">
    <h2 class="text-center font-bold text-xl mb-4 border-b border-gray-300 pb-2 text-yellow-500">
        حسابات متجر الممالك
    </h2>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($products as $product)
            @php
                $imageUrl = $product->getMediaUrl('product', $product, null, 'media', 'product');
                $productImage = $imageUrl ?: $fallbackImage;
                $isSold = $product->featured === 1;
                $discountPercent = null;

                if (!empty($product->price_before_discount) && $product->price_before_discount > 0) {
                    $discountPercent = round((($product->price_before_discount - $product->price) / $product->price_before_discount) * 100);
                }
            @endphp

            <div class="relative bg-white p-3 rounded-lg shadow text-center product flex flex-col h-full"
                 data-status="{{ $isSold ? 'مباع' : '' }}">

                @if($isSold)
                    <div class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded shadow">
                        مباع
                    </div>
                @endif

                @if(!$isSold && !empty($discountPercent) && $discountPercent > 0)
                    <div class="absolute top-2 right-2 bg-yellow-400 text-black text-xs font-bold px-2 py-1 rounded shadow">
                        خصم {{ $discountPercent }}%
                    </div>
                @endif

                <img src="{{ $productImage }}"
                     class="product-img mx-auto rounded-md object-cover"
                     alt="{{ $product->name }}"
                     width="600" height="300"
                     loading="lazy"
                     decoding="async">

                <h2 class="font-bold mt-2">{{ $product->name }}</h2>

                <p class="text-gray-500 text-sm">
                    {{ $product->short_description ?? 'لا يوجد وصف متاح' }}
                </p>

                @if(!empty($product->price_before_discount))
                    <p class="font-semibold mt-1 product-price text-red-600"
                       data-base-price="{{ $product->price }}"
                       data-base-old="{{ $product->price_before_discount }}">
                        <span class="current-price">ر.س {{ $product->price }}</span>
                        <span class="old-price text-gray-500 text-sm line-through">
                            {{ $product->price_before_discount }}
                        </span>
                    </p>
                @else
                    <p class="font-semibold mt-1 product-price" data-base-price="{{ $product->price }}">
                        <span class="current-price">ر.س {{ $product->price }}</span>
                    </p>
                @endif

                @if($isSold)
                    <button class="mt-auto w-full bg-gray-400 text-white py-1 rounded text-sm cursor-not-allowed">
                        مباع
                    </button>
                @else
                    <a href="{{ route('website.product.show', $product->id) }}"
                       class="mt-auto block w-full bg-black text-white py-1 rounded text-sm text-center">
                        عرض تفاصيل
                    </a>
                @endif
            </div>
        @endforeach
    </div>
</div>

<!-- آراء العملاء -->
<div class="px-4 py-10 bg-gradient-to-l from-yellow-50 to-white border-t border-gray-200">
    <h2 class="text-center font-bold text-xl mb-6 text-gray-800">
        آراء <span class="text-yellow-500">عملائنا</span>
    </h2>

    <div class="swiper reviewsSwiper max-w-6xl mx-auto">
        <div class="swiper-wrapper">
            @php
                $reviews = [
                    ['name' => 'تركي القحطاني 🇸🇦', 'text' => 'متجر الممالك ثقة 🔥 جربته أكثر من مرة وما خيب ظني أبد 💪'],
                    ['name' => 'سارة المطيري 🇸🇦', 'text' => 'تنفيذ سريع جدًا وخدمة محترمة 🖤 أفضل متجر فري فاير بلا منازع!'],
                    ['name' => 'عبدالله الشهري 🇸🇦', 'text' => 'يا عيال المتجر ذا فخم 🔥 سرعة بالتنفيذ وثقة ما بعدها ثقة 💚'],
                    ['name' => 'ريم العتيبي 🇸🇦', 'text' => 'الممالك فخم فخم فخم 👑 كل طلب يوصلني بثواني حرفيًا!'],
                    ['name' => 'فهد الحربي 🇸🇦', 'text' => 'اطلق ممالك فالعالم 💚 ما في تأخير ولا مشاكل، متجر محترم جدًا.'],
                    ['name' => 'نواف الدوسري 🇸🇦', 'text' => 'تجربة خرافية 😍 أسعار ممتازة وتنفيذ لحظي، شكراً لكم!'],
                    ['name' => 'منيرة القحطاني 🇸🇦', 'text' => 'المتجر الوحيد اللي أتعامل معه 💛 تعامل راقي وسرعة تنفيذ 🔥'],
                    ['name' => 'عبدالرحمن الزهراني 🇸🇦', 'text' => 'متجر الممالك يستحق خمس نجوم ⭐⭐⭐⭐⭐ ثقة وأمان وسرعة.'],
                    ['name' => 'مشعل العنزي 🇸🇦', 'text' => 'جربت أكثر من مرة وكل مرة نفس الجودة 💚 المتجر الأفضل بلا منازع.'],
                    ['name' => 'لطيفة الشمري 🇸🇦', 'text' => 'والله ما أتوقع فيه متجر ينافسهم 🔥 سرعة ودقة بالتعامل.'],
                ];
            @endphp

            @foreach($reviews as $review)
                <div class="swiper-slide bg-white rounded-2xl shadow-sm p-4 border border-gray-100 transition transform hover:scale-[1.02]">
                    <h3 class="font-semibold text-gray-800 mb-1 text-sm sm:text-base">{{ $review['name'] }}</h3>
                    <p class="text-gray-600 text-xs sm:text-sm leading-relaxed">{{ $review['text'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="swiper-pagination mt-4"></div>
    </div>
</div>

<!-- المزايا -->
<div class="px-4 py-8 text-center bg-white border-t border-gray-100">
    <div class="flex flex-wrap justify-center gap-8">
        <div class="flex items-center gap-2">
            <i class="bi bi-shield-check text-green-500 text-xl"></i>
            <span class="text-gray-700 font-semibold text-sm">ضمان استرجاع الأموال</span>
        </div>
        <div class="flex items-center gap-2">
            <i class="bi bi-truck text-blue-500 text-xl"></i>
            <span class="text-gray-700 font-semibold text-sm">تنفيذ فوري وآمن</span>
        </div>
        <div class="flex items-center gap-2">
            <i class="bi bi-lock text-yellow-500 text-xl"></i>
            <span class="text-gray-700 font-semibold text-sm">دفع مشفر وآمن</span>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const currencyButtons = document.querySelectorAll(".currency-btn");
    const DEFAULT_COUNTRY = "SA";
    const CACHE_KEY = "user_country_code";
    const CACHE_TTL = 6 * 60 * 60 * 1000;

    const applyCurrency = (countryCode) => {
        const btn = document.querySelector(`.currency-btn[data-country="${countryCode}"]`)
            || document.querySelector(`.currency-btn[data-country="${DEFAULT_COUNTRY}"]`)
            || currencyButtons[0];

        if (btn) {
            btn.click();
        }
    };

    const detectCountry = async () => {
        try {
            const cached = JSON.parse(localStorage.getItem(CACHE_KEY) || "null");
            if (cached && (Date.now() - cached.ts) < CACHE_TTL) {
                applyCurrency(cached.code);
                return;
            }

            const res = await fetch("https://ipwho.is/?fields=country_code");
            const data = await res.json();
            const code = data?.country_code;

            if (code) {
                localStorage.setItem(CACHE_KEY, JSON.stringify({ code, ts: Date.now() }));
                applyCurrency(code);
                return;
            }
        } catch (e) {
            // ignore
        }

        applyCurrency(DEFAULT_COUNTRY);
    };

    currencyButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const symbol = btn.dataset.symbol;
            const rate = parseFloat(btn.dataset.rate);

            document.querySelectorAll(".product-price").forEach(p => {
                const base = parseFloat(p.dataset.basePrice);
                const baseOld = parseFloat(p.dataset.baseOld);
                const current = p.querySelector(".current-price");
                const old = p.querySelector(".old-price");

                if (current && !isNaN(base)) {
                    const converted = Math.round(base * rate);
                    current.textContent = `${symbol} ${converted}`;
                }

                if (old && !isNaN(baseOld)) {
                    const convertedOld = Math.round(baseOld * rate);
                    old.textContent = convertedOld;
                }
            });

            currencyButtons.forEach(b => b.classList.remove("ring-2", "ring-yellow-500"));
            btn.classList.add("ring-2", "ring-yellow-500");
        });
    });

    applyCurrency(DEFAULT_COUNTRY);
    detectCountry();

    const stars = document.querySelectorAll(".star");
    stars.forEach((star, index) => {
        star.addEventListener("click", () => {
            stars.forEach((s, i) => {
                s.classList.toggle("text-yellow-400", i <= index);
                s.classList.toggle("text-gray-400", i > index);
                if (s.previousElementSibling) {
                    s.previousElementSibling.checked = true;
                }
            });
        });
    });

    if (typeof Swiper !== "undefined") {
        new Swiper(".reviewsSwiper", {
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            slidesPerView: 1.2,
            spaceBetween: 12,
            centeredSlides: true,
            speed: 600,
            effect: "slide",
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                480: { slidesPerView: 1.4 },
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
            },
        });
    }

    if (typeof Swiper !== "undefined" && document.querySelector('.swiper-container')) {
        new Swiper('.swiper-container', {
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }
});
</script>
@endpush