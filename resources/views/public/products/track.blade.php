@extends('public.layouts.master')

@section('pageTitle')
    {{ $pageTitle ?? 'متابعة طلب نشر الحساب' }}
@endsection

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $status = (string) ($product->status ?? 'draft');
        $badge = match ($status) {
            'published' => 'bg-green-100 text-green-800 border-green-200',
            'archived' => 'bg-red-100 text-red-800 border-red-200',
            default => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        };
        $reasons = (array) ($product->review_reject_reasons ?? []);
        $note = trim((string) ($product->review_note ?? ''));
        $name = (string) ($product->name ?? ($product->translateOrNew('ar')->name ?? '—'));
        $productUrl = $status === 'published' ? route('website.product.show', $product) : null;
    @endphp

    <div class="bg-gray-100 min-h-screen" dir="rtl">
        <div class="bg-white px-4 pt-6 pb-10 max-w-md mx-auto w-full">
            @if(session('success'))
                <div class="rounded-2xl bg-green-100 text-green-800 px-4 py-3 text-center font-semibold mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-start justify-between gap-3">
                <div>
                    <h1 class="text-xl font-extrabold text-gray-900">متابعة طلب نشر الحساب</h1>
                    <div class="text-xs text-gray-500 mt-1">رقم الطلب: <span class="font-mono select-all">{{ $product->slug }}</span></div>
                </div>
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-extrabold {{ $badge }}">
                    {{ $statusLabel ?? '—' }}
                </span>
            </div>

            <div class="mt-5 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                <div class="text-xs text-gray-500">اسم الحساب</div>
                <div class="mt-1 font-extrabold text-gray-900">{{ $name }}</div>
                <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <div class="text-xs text-gray-500">السعر</div>
                        <div class="font-extrabold text-green-700">{{ number_format((float) ($product->price ?? 0), 2) }} ر.س</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">تاريخ الطلب</div>
                        <div class="font-bold text-gray-900">{{ $product->created_at?->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($status === 'archived')
                <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 p-4">
                    <div class="text-sm font-extrabold text-red-900">أسباب الرفض</div>
                    @if(count($reasons) > 0)
                        <ul class="mt-2 text-sm text-red-900/90 list-disc list-inside space-y-1">
                            @foreach($reasons as $r)
                                <li>{{ $r }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="mt-2 text-sm text-red-900/90">—</div>
                    @endif

                    @if($note !== '')
                        <div class="mt-3 text-sm font-extrabold text-red-900">ملاحظة الإدارة</div>
                        <div class="mt-1 text-sm text-red-900/90 whitespace-pre-line">{{ $note }}</div>
                    @endif
                </div>
            @endif

            @if($status === 'published' && $productUrl)
                <a href="{{ $productUrl }}"
                   class="mt-5 w-full inline-flex items-center justify-center rounded-2xl bg-black px-5 py-4 text-sm font-extrabold text-white hover:bg-gray-800 transition">
                    فتح الإعلان
                </a>
            @endif

            <a href="{{ route('public.products.create') }}"
               class="mt-3 w-full inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-5 py-4 text-sm font-bold text-gray-800 hover:bg-gray-50 transition">
                نشر حساب جديد
            </a>
        </div>
    </div>
@endsection

