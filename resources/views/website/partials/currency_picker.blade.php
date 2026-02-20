<div class="px-4 py-4 flex flex-row-reverse items-center justify-center gap-4 bg-gradient-to-l from-yellow-50 to-white rounded-lg shadow-md border border-gray-200">
    <span class="font-bold text-base text-gray-800">اختر عملة بلدك</span>

    @php
        $rates = $currencyRatesByCountry ?? ['SA' => 1, 'JO' => 0.1885, 'US' => 0.2666];
        $rateSA = (float) ($rates['SA'] ?? 1);
        $rateJO = (float) ($rates['JO'] ?? 0.1885);
        $rateUS = (float) ($rates['US'] ?? 0.2666);
    @endphp

    <button class="currency-btn bg-white hover:bg-yellow-100 p-1.5 rounded-full shadow transition-all duration-200 border border-gray-200 hover:scale-105 ring-2 ring-yellow-500"
            data-symbol="ر.س" data-rate="{{ number_format($rateSA, 6, '.', '') }}" data-country="SA" title="الريال السعودي">
        <img src="https://upload.wikimedia.org/wikipedia/commons/0/0d/Flag_of_Saudi_Arabia.svg"
             class="w-7 h-7 rounded-full" alt="علم السعودية" loading="lazy" decoding="async" width="28" height="28">
    </button>

    <button class="currency-btn bg-white hover:bg-yellow-100 p-1.5 rounded-full shadow transition-all duration-200 border border-gray-200 hover:scale-105"
            data-symbol="د.أ" data-rate="{{ number_format($rateJO, 6, '.', '') }}" data-country="JO" title="الدينار الأردني">
        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c0/Flag_of_Jordan.svg"
             class="w-7 h-7 rounded-full" alt="علم الأردن" loading="lazy" decoding="async" width="28" height="28">
    </button>

    <button class="currency-btn bg-white hover:bg-yellow-100 p-1.5 rounded-full shadow transition-all duration-200 border border-gray-200 hover:scale-105"
            data-symbol="$" data-rate="{{ number_format($rateUS, 6, '.', '') }}" data-country="US" title="الدولار الأمريكي">
        <img src="https://upload.wikimedia.org/wikipedia/en/a/a4/Flag_of_the_United_States.svg"
             class="w-7 h-7 rounded-full" alt="علم أمريكا" loading="lazy" decoding="async" width="28" height="28">
    </button>
</div>

