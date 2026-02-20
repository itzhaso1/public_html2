<?php
 
namespace App\Http\Controllers\Website;
 
use App\Http\Controllers\Controller;
use App\Models\{Category,Slider, Section, Product};
use Illuminate\Support\Facades\Cache;
 
class WebsiteController extends Controller
{
    public function __invoke()
    {
        $locale = app()->getLocale();

        $sliders = Cache::remember("home.sliders.$locale", 60 * 5, function () {
            return Slider::with(['translations', 'media'])->latest()->get();
        });

        // `$categories` is shared via View Composer for website.*
        $categories = Cache::get("website.categories.menu.$locale")
            ?? Category::with(['translations', 'media', 'children.translations'])
                ->whereNull('parent_id')
                ->where('status', 'active')
                ->get();

        $featuredCategories = Cache::remember("home.featured_categories.$locale", 60 * 5, function () {
            return Category::query()
                ->whereNull('parent_id')
                ->where('status', 'active')
                ->with(['translations', 'media'])
                ->get();
        });

        $categoryCount = $categories->count();
        $slidesPerView = $categoryCount < 10 ? $categoryCount : 10;
 
        $sections = Cache::remember("home.sections.v2.$locale", 60 * 5, function () {
            return Section::with([
                'translations',
                'products' => function ($q) {
                    $q->with(['translations', 'media', 'codeThumbnail'])
                        ->orderByDesc('price')
                        ->orderByDesc('id');
                },
                'categories.translations',
            ])
                ->orderBy('order')
                ->get();
        });
        
        $sectionProductIds = $sections->pluck('products')->flatten()->pluck('id')->unique();
        
        // تعديل بسيط: إخفاء منتجات الشحن من الصفحة الرئيسية أيضاً
        $products = Cache::remember("home.products.v2.$locale", 60 * 5, function () use ($sectionProductIds) {
            return Product::with(['translations', 'media'])
                ->where('status', 'published')
                ->whereNotIn('id', $sectionProductIds)
                ->whereNull('service_type') // ✅ إخفاء الجواهر من هنا
                ->orderByDesc('price')
                ->orderByDesc('id')
                ->get();
        });
            
        $categoryCount = $categories->count();
        $slidesPerView = $categoryCount < 10 ? $categoryCount : 10;
 
        return view('website.pages.home', ['pageTitle' => trans('site/site.home_page_title'),
            'categories' => $categories,
            'sliders' => $sliders,
            'featuredCategories' => $featuredCategories,
            'categoryCount' => $categoryCount,
            'slidesPerView' => $slidesPerView,
            'sections' => $sections,
            'products' => $products,
        ]);
    }
 
    public function show(Product $product)
    {
        // Hide unapproved public submissions from direct links.
        if (($product->publish_source ?? null) === 'public' && (string) ($product->status ?? 'draft') !== 'published') {
            abort(404);
        }

        // ============================================================
        // ✅ التعديل الجديد: توجيه منتجات الشحن لصفحة خاصة
        // ============================================================
        if (!empty($product->service_type)) {
            // إذا كان المنتج شحناً، نستخدم تصميماً مبسطاً
            // سنحتاج لإنشاء هذا الملف في الخطوة التالية
            return view('website.diamonds.show_charge', compact('product'));
        }
        // ============================================================
 
        $product->load(['translations', 'media', 'videos']);
        $mainImage = $product->getMediaUrl('product', $product, null, 'media', 'product');
        $galleryImages = $product->getMultipleMediaUrls('product/gallery', $product, 'media', 'gallery');
        $productVideo = $product->videos->first();
        return view('website.pages.products_show', compact('product', 'mainImage', 'galleryImages', 'productVideo'));
    }
}
