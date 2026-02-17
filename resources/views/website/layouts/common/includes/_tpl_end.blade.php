<script>
    const menuButton = document.getElementById('mobile-menu-button');
const mobileMenu = document.getElementById('mobile-menu');
menuButton.addEventListener('click', () => { mobileMenu.classList.toggle('hidden'); });
</script>

<!-- Country / Currency picker modal (first visit) -->
<div id="countryPickerModal" class="hidden fixed inset-0 z-[9999] bg-black/60">
  <div class="min-h-full flex items-center justify-center p-4" dir="rtl">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-xl border border-gray-200 p-5">
      <div class="text-center">
        <div class="text-3xl">🌍</div>
        <h3 class="mt-2 text-xl font-extrabold text-gray-900">اختر بلدك</h3>
        <p class="mt-1 text-sm text-gray-600">سيتم حفظ الاختيار لتعديل الأسعار تلقائياً.</p>
      </div>

      <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
        <button type="button" data-pick-country="SA" class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-bold hover:bg-gray-50">السعودية</button>
        <button type="button" data-pick-country="JO" class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-bold hover:bg-gray-50">الأردن</button>
        <button type="button" data-pick-country="US" class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-bold hover:bg-gray-50">دولار</button>
      </div>

      <div class="mt-4 text-[11px] text-gray-500">
        يمكنك تغيير العملة لاحقاً من صفحة الدفع.
      </div>
    </div>
  </div>
</div>
<!-- سكربت العملات -->
  <!-- سكربت العملات -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll(".currency-btn");
  const CACHE_KEY = "user_country_code";
  const CACHE_TTL = 6 * 60 * 60 * 1000;

  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      const symbol = btn.dataset.symbol;
      const rate = parseFloat(btn.dataset.rate);
      const country = btn.dataset.country || "";

      document.querySelectorAll(".product-price").forEach(p => {
        const current = p.querySelector(".current-price");
        const old = p.querySelector(".old-price");
        const base = parseFloat(p.dataset.basePrice);
        const baseOld = parseFloat(p.dataset.baseOld);

        if (current && !isNaN(base)) {
          const converted = (base * rate).toFixed(2).replace(/\.00$/, "");
          current.textContent = `${symbol} ${converted}`;
        }

        if (old && !isNaN(baseOld)) {
          const convertedOld = (baseOld * rate).toFixed(2).replace(/\.00$/, "");
          old.textContent = convertedOld;
        }
      });

      // إبراز الزر النشط
      buttons.forEach(b => b.classList.remove("ring-2", "ring-yellow-500"));
      btn.classList.add("ring-2", "ring-yellow-500");

      if (country) {
        try {
          localStorage.setItem(CACHE_KEY, JSON.stringify({ code: country, ts: Date.now() }));
        } catch (e) {}
      }
    });
  });

  const applyCurrency = (countryCode) => {
    const btn = document.querySelector(`.currency-btn[data-country="${countryCode}"]`)
      || document.querySelector(`.currency-btn[data-country="SA"]`)
      || buttons[0];
    if (btn) btn.click();
  };

  const readCachedCountry = () => {
    try {
      const cached = JSON.parse(localStorage.getItem(CACHE_KEY) || "null");
      if (cached && cached.code && (Date.now() - cached.ts) < CACHE_TTL) return cached.code;
    } catch (e) {}
    return null;
  };

  // First-visit modal (choose country once)
  const modal = document.getElementById('countryPickerModal');
  const cachedCode = readCachedCountry();
  if (!cachedCode && modal) {
    modal.classList.remove('hidden');
    modal.querySelectorAll('[data-pick-country]').forEach(el => {
      el.addEventListener('click', () => {
        const code = el.getAttribute('data-pick-country');
        try { localStorage.setItem(CACHE_KEY, JSON.stringify({ code, ts: Date.now() })); } catch (e) {}
        modal.classList.add('hidden');
        if (buttons.length) applyCurrency(code);
      });
    });
  } else if (cachedCode && buttons.length) {
    applyCurrency(cachedCode);
  }
});
</script>

<!-- Copy to clipboard for payment info -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const toastId = 'copyToast';
  const showToast = (msg) => {
    let t = document.getElementById(toastId);
    if (!t) {
      t = document.createElement('div');
      t.id = toastId;
      t.style.position = 'fixed';
      t.style.left = '50%';
      t.style.bottom = '24px';
      t.style.transform = 'translateX(-50%)';
      t.style.zIndex = '99999';
      t.style.padding = '10px 14px';
      t.style.borderRadius = '12px';
      t.style.background = 'rgba(0,0,0,0.85)';
      t.style.color = '#fff';
      t.style.fontSize = '13px';
      t.style.fontWeight = '700';
      t.style.boxShadow = '0 10px 25px rgba(0,0,0,0.25)';
      t.style.opacity = '0';
      t.style.transition = 'opacity 160ms ease-in-out';
      document.body.appendChild(t);
    }
    t.textContent = msg || 'تم النسخ';
    t.style.opacity = '1';
    clearTimeout(window.__copyToastTimer);
    window.__copyToastTimer = setTimeout(() => { t.style.opacity = '0'; }, 900);
  };

  const fallbackCopy = (text) => {
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.setAttribute('readonly', '');
    ta.style.position = 'fixed';
    ta.style.top = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); } catch (e) {}
    document.body.removeChild(ta);
  };

  document.addEventListener('click', async (e) => {
    const el = e.target && e.target.closest ? e.target.closest('.select-all') : null;
    if (!el) return;
    const raw = (el.getAttribute('data-copy-text') || el.textContent || '').trim();
    if (!raw) return;
    try {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        await navigator.clipboard.writeText(raw);
      } else {
        fallbackCopy(raw);
      }
      showToast('تم نسخ النص');
    } catch (err) {
      fallbackCopy(raw);
      showToast('تم النسخ');
    }
  });
});
</script>

    <!-- سكربت الشارات -->
    <script>
        document.querySelectorAll('.product').forEach(p => {
    const status = p.dataset.status;
    const discount = p.dataset.discount;
    if (status === "مباع") {
        const badge = document.createElement('div');
        badge.className = "badge badge-sold";
        badge.textContent = "مباع";
        p.prepend(badge);
        const btn = p.querySelector('button');
        btn.disabled = true;
        btn.classList.add("bg-gray-400", "cursor-not-allowed");
        btn.textContent = "مباع";
    }
    if (discount && !status) {
        const badge = document.createElement('div');
        badge.className = "badge badge-sale";
        badge.textContent = `خصم ${discount}%`;
        p.prepend(badge);
        const priceEl = p.querySelector('[data-base-price]');
        const base = parseFloat(priceEl.getAttribute('data-base-price'));
        const newPrice = base - (base * (discount / 100));
        priceEl.textContent = `ر.س ${newPrice.toFixed(2)} (بدلاً من ${base})`;
    }
    });
    </script>

    <!-- ترتيب المنتجات -->
    <script>
        const productsContainer = document.querySelector('.grid.grid-cols-2.md\\:grid-cols-3');
    const products = Array.from(productsContainer.children);
    products.sort((a,b) => {
    const priceA = parseFloat(a.querySelector('p.font-semibold').textContent.replace(/[^\d]/g,''));
    const priceB = parseFloat(b.querySelector('p.font-semibold').textContent.replace(/[^\d]/g,''));
    return priceB - priceA;
    });
    products.forEach(p => productsContainer.appendChild(p));
    </script>

    <!-- سلايدر Swiper -->
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper('.swiper-container', {
    loop:true,
    autoplay:{delay:3000},
    slidesPerView:1,
    spaceBetween:0
    });
    </script>
    <!-- header style two End -->
    @stack('js')
    </body>
</html>
