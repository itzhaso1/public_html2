@extends('website.layouts.common.website')
@section('css')
<script src="https://cdn.tailwindcss.com"></script>
{{--<style>
    :root {
        --footer-height: 76px;
    }

    .slider-track {
        display: flex;
        will-change: transform;
        touch-action: pan-y;
        transition: transform 0.4s ease-in-out;
    }

    .thumb-active {
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        border-color: rgb(37 99 235);
    }

    .slider-track {
        direction: ltr !important;
    }

    .thumbs-scroll::-webkit-scrollbar {
        height: 8px;
    }

    .thumbs-scroll::-webkit-scrollbar-thumb {
        background: rgba(100, 116, 139, 0.28);
        border-radius: 9999px;
    }

    .slide-inner {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
    }

    .slide-inner img.original {
        object-fit: cover;
        object-position: center;
        width: 100%;
        height: 100%;
    }

    .slide {
        flex-shrink: 0;
    }
</style>--}}
<style>
    .slider-track {
        display: flex;
        transition: transform 0.4s ease-in-out;
        will-change: transform;
    }

    .slide {
        flex: 0 0 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .slide-inner {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .slide-inner img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        display: block;
    }

    .thumb-active {
        border: 2px solid #2563eb !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);
    }

    .thumbs-scroll::-webkit-scrollbar {
        height: 6px;
    }

    .thumbs-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
</style>
@endsection

@section('pageTitle')
{{$pageTitle ?? $product?->name}}
@endsection

@section('content')
<main class="flex-grow pb-[var(--footer-height)]">
    <div class="container mx-auto px-4">

        <!-- سلايدر -->
        <section class="mt-6">
            <div id="sliderWrap" class="relative w-full max-w-sm mx-auto overflow-hidden rounded-lg shadow-lg bg-white"
                style="height: 200px;">
                <div id="sliderTrack" class="slider-track h-full flex"></div>

                <button id="prevBtn"
                    class="absolute left-2 top-1/2 -translate-y-1/2 bg-white bg-opacity-70 p-2 rounded-full shadow-sm hover:bg-opacity-90 z-20 transition-all"
                    aria-label="السابق">‹</button>
                <button id="nextBtn"
                    class="absolute right-2 top-1/2 -translate-y-1/2 bg-white bg-opacity-70 p-2 rounded-full shadow-sm hover:bg-opacity-90 z-20 transition-all"
                    aria-label="التالي">›</button>
            </div>

            <!-- الثمبنات -->
            <div class="max-w-sm mx-auto mt-3">
                <div id="thumbs" class="flex gap-2 overflow-x-auto thumbs-scroll py-2 px-1 touch-pan-x snap-x snap-mandatory"></div>
            </div>
            <!-- فيديو مع عنوان واستعراض الحساب -->
            {{--<div class="max-w-sm mx-auto mt-5 bg-white rounded-lg shadow-lg border-4 border-red-500 overflow-hidden">
                <div class="text-center py-2 bg-red-100 border-b border-red-300">
                    <h3 class="text-lg font-bold text-red-800">استعراض الحساب</h3>
                </div>
                <div class="w-full h-[200px]">
                    <iframe class="w-full h-full" src="/2025-10-19 00-28-42.mp4" title="استعراض الحساب" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>--}}
            
            <!-- فيديو مع عنوان واستعراض الحساب -->
            @php
                $productVideo = $product->videos->first();
            @endphp
            @if($productVideo)
            <div class="max-w-sm mx-auto mt-5 bg-white rounded-lg shadow-lg border-4 border-red-500 overflow-hidden">
                <div class="text-center py-2 bg-red-100 border-b border-red-300">
                    <h3 class="text-lg font-bold text-red-800">استعراض الحساب</h3>
                </div>
                <div class="w-full h-[200px]">
                    <video class="w-full h-full" controls controlsList="nodownload">
                       <source src="{{ asset('public/' . $productVideo->video_path) }}" type="video/mp4">

                        متصفحك لا يدعم تشغيل الفيديو.
                    </video>
                </div>
                @if($productVideo->video_name)
                <div class="text-center py-2 bg-gray-100">
                    <p class="text-sm text-gray-600">{{ $product?->name }}</p>
                </div>
                @endif
            </div>
            @else
            <!-- عرض فيديو افتراضي إذا لم يكن هناك فيديو -->
            <div class="max-w-sm mx-auto mt-5 bg-white rounded-lg shadow-lg border-4 border-gray-300 overflow-hidden">
                <div class="text-center py-2 bg-gray-100 border-b border-gray-300">
                    <h3 class="text-lg font-bold text-gray-600">استعراض الحساب</h3>
                </div>
                <div class="w-full h-[200px] bg-gray-200 flex items-center justify-center">
                    <p class="text-gray-500">لا يوجد فيديو متاح</p>
                </div>
            </div>
            @endif


        </section>

        <!-- وصف المنتج -->
        <section class="max-w-sm mx-auto mt-6 p-4 bg-white rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-2">{{$product?->name}}</h2>
            <p class="text-gray-700 mb-4">{{$product?->short_description}}</p>

            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-sm text-gray-500">السعر</div>
                    <div class="text-2xl font-extrabold text-green-600">{{$product?->price}}</div>
                </div>

                <div class="flex flex-col sm:flex-row gap-2">
                    <a id="customContactBtn" href="#"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 transition text-center"></a>
                    <a href="#"
                        class="bg-white border border-blue-600 text-blue-600 px-3 py-2 rounded-md shadow-sm hover:bg-blue-50 text-center"></a>
                </div>
            </div>
        </section>

        <!-- قسم التواصل -->
        <div class="max-w-sm mx-auto mt-6 p-4 bg-white rounded-lg shadow-md text-center border border-blue-100">
            <h3 class="text-lg font-bold text-gray-800 mb-2">تواصل معنا مباشرة</h3>
            <p class="text-gray-600 mb-4">نسعد بخدمتك والإجابة على استفساراتك في أي وقت.</p>

            <div class="text-2xl font-extrabold text-blue-600 mb-3 select-all">{{ \App\Support\WhatsApp::display() }}</div>

            <div class="flex justify-center gap-3 flex-wrap">
                <a href="tel:0777515306"
                    class="bg-green-600 text-white px-5 py-2 rounded-md shadow hover:bg-green-700 transition">
                    اتصل الآن
                </a>
                @php($whatsappHref = \App\Support\WhatsApp::href())
                @if($whatsappHref)
                    <a href="{{ $whatsappHref }}" target="_blank" rel="noopener noreferrer"
                        class="bg-[#25D366] text-white px-5 py-2 rounded-md shadow hover:bg-[#1ebe5d] transition">
                        تواصل عبر واتساب
                    </a>
                @endif
            </div>
        </div>

    </div>
  @php
        $mainImage = $product->getMediaUrl('product', $product, null, 'media', 'product');
        $galleryImages = $product->getMultipleMediaUrls('product/gallery', $product, 'media', 'gallery');
    @endphp


<script>
    window.productData = {
        mainImage: @json($mainImage),
        galleryImages: @json($galleryImages),
    };
</script>
@endsection

{{--@push('js')
<script>
    (function () {
  const { mainImage, galleryImages } = window.productData;

  if (!mainImage && (!galleryImages || galleryImages.length === 0)) return;

  const sliderImages = [mainImage, ...galleryImages.map(img => img.original)];
  const sliderWrap = document.getElementById('sliderWrap');
  const track = document.getElementById('sliderTrack');
  const thumbsContainer = document.getElementById('thumbs');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');

  let currentIndex = 0;
  let slideWidth = 0;

  function buildSlidesAndThumbs() {
    track.innerHTML = '';
    thumbsContainer.innerHTML = '';

    sliderImages.forEach((src, idx) => {
      const slideDiv = document.createElement('div');
      slideDiv.className = 'slide';
      slideDiv.style.height = '100%';

      const inner = document.createElement('div');
      inner.className = 'slide-inner h-full';

      const img = document.createElement('img');
      img.className = 'original';
      img.src = src;
      img.alt = `Slide ${idx + 1}`;
      img.loading = 'lazy';

      inner.appendChild(img);
      slideDiv.appendChild(inner);
      track.appendChild(slideDiv);

      const thumb = document.createElement('img');
      thumb.src = src;
      thumb.dataset.index = idx;
      thumb.alt = `Thumb ${idx + 1}`;
      thumb.className = 'w-20 h-14 object-cover rounded cursor-pointer border-2 border-transparent snap-start';
      thumb.addEventListener('click', () => goToSlide(idx));
      thumbsContainer.appendChild(thumb);
    });
  }

  function updateLayout() {
    slideWidth = sliderWrap.clientWidth;
    Array.from(track.children).forEach(sl => sl.style.width = slideWidth + 'px');
    track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
  }

  function goToSlide(i, smooth = true) {
    const total = sliderImages.length;
    currentIndex = Math.max(0, Math.min(i, total - 1));
    if (!smooth) {
      track.style.transition = 'none';
      track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
      void track.offsetWidth;
      track.style.transition = 'transform 0.4s ease-in-out';
    } else {
      track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
    }
    updateActiveThumb();
  }

  function updateActiveThumb() {
    const thumbs = Array.from(thumbsContainer.querySelectorAll('img'));
    thumbs.forEach((t, idx) => t.classList.toggle('thumb-active', idx === currentIndex));
  }

  nextBtn.addEventListener('click', () => goToSlide((currentIndex + 1) % sliderImages.length));
  prevBtn.addEventListener('click', () => goToSlide((currentIndex - 1 + sliderImages.length) % sliderImages.length));

  window.addEventListener('resize', () => {
    clearTimeout(window._sliderResizeTimer);
    window._sliderResizeTimer = setTimeout(updateLayout, 150);
  });

  function init() {
    buildSlidesAndThumbs();
    updateLayout();
    goToSlide(0, false);
  }

  window.addEventListener('load', init);
})();
</script>
@endpush--}}
@push('js')
<script>
    (function () {
  const { mainImage, galleryImages } = window.productData || {};

  if (!mainImage && (!galleryImages || galleryImages.length === 0)) return;

  // دمج الصورة الرئيسية مع صور الجاليري
  const sliderImages = [mainImage, ...(galleryImages || []).map(img => img.original)];

  const sliderWrap = document.getElementById('sliderWrap');
  const track = document.getElementById('sliderTrack');
  const thumbsContainer = document.getElementById('thumbs');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');

  let currentIndex = 0;
  let slideWidth = 0;

  function buildSlidesAndThumbs() {
    track.innerHTML = '';
    thumbsContainer.innerHTML = '';

    sliderImages.forEach((src, idx) => {
      const slideDiv = document.createElement('div');
      slideDiv.className = 'slide';
      slideDiv.style.flexShrink = '0'; // مهم عشان كل سلايد يبقى ثابت في العرض
      slideDiv.style.height = '100%';

      const inner = document.createElement('div');
      inner.className = 'slide-inner h-full flex justify-center items-center';

      const img = document.createElement('img');
      img.className = 'original object-cover object-center w-full h-full';
      img.src = src;
      img.alt = `Slide ${idx + 1}`;
      img.loading = 'lazy';

      inner.appendChild(img);
      slideDiv.appendChild(inner);
      track.appendChild(slideDiv);

      // الثامبنيلز
      const thumb = document.createElement('img');
      thumb.src = src;
      thumb.dataset.index = idx;
      thumb.alt = `Thumb ${idx + 1}`;
      thumb.className = 'w-20 h-14 object-cover rounded cursor-pointer border-2 border-transparent snap-start';
      thumb.addEventListener('click', () => goToSlide(idx));
      thumbsContainer.appendChild(thumb);
    });
  }

  function updateLayout() {
    slideWidth = sliderWrap.clientWidth;
    Array.from(track.children).forEach(slide => {
      slide.style.width = `${slideWidth}px`;
    });
    track.style.width = `${slideWidth * sliderImages.length}px`;
    //track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
    track.style.transform = `translate3d(-${currentIndex * slideWidth}px, 0, 0)`;
  }

  function goToSlide(i, smooth = true) {
    const total = sliderImages.length;
    if (total === 0) return;
    currentIndex = Math.max(0, Math.min(i, total - 1));
    if (!smooth) {
      track.style.transition = 'none';
      track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
      void track.offsetWidth; // لإعادة التفعيل
      track.style.transition = 'transform 0.4s ease-in-out';
    } else {
      track.style.transition = 'transform 0.4s ease-in-out';
      track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
    }
    updateActiveThumb();
    console.log('goToSlide =>', currentIndex, sliderImages[currentIndex]);
  }

  function updateActiveThumb() {
    const thumbs = thumbsContainer.querySelectorAll('img');
    thumbs.forEach((t, idx) => t.classList.toggle('thumb-active', idx === currentIndex));
  }

  nextBtn.addEventListener('click', () => goToSlide((currentIndex + 1) % sliderImages.length));
  prevBtn.addEventListener('click', () => goToSlide((currentIndex - 1 + sliderImages.length) % sliderImages.length));

  window.addEventListener('resize', () => {
    clearTimeout(window._sliderResizeTimer);
    window._sliderResizeTimer = setTimeout(updateLayout, 150);
  });

  function init() {
    buildSlidesAndThumbs();
    setTimeout(() => {
      updateLayout();
      goToSlide(0, false);
    }, 100);
  }

  window.addEventListener('load', init);
})();
</script>
@endpush
