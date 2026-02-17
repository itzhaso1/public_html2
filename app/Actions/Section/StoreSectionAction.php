<?php
namespace App\Actions\Section;
use App\Models\{Section,Product,Category};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
class StoreSectionAction {
    public function execute(Request $request): RedirectResponse {
        DB::beginTransaction();
        try {
            if (
                $request->design_type === 'layout3' &&
                (!is_array($request->category_ids) || empty(array_filter($request->category_ids, fn($id) => $id != 0)))
            ) {
                return back()->with('error', 'يجب اختيار تصنيف واحد على الأقل عند استخدام تصميم 3')->withInput();
            }
            $section = Section::create([
                'order' => $request->filled('order') ? (int)$request->order : 0,
                'design_type' => $request->design_type ?? 'layout1',
            ]);
            foreach (config('laravellocalization.supportedLocales') as $locale => $lang) {
                $section->translateOrNew($locale)->name = $request[$locale]['name'] ?? '';
                $section->translateOrNew($locale)->description = $request[$locale]['description'] ?? '';
            }
            $section->save();
            if ($request->has('category_ids')) {
                $section->categories()->sync($request->category_ids);
            }

            if ($request->has('product_ids') && count($request->product_ids)) {
                $section->products()->sync($request->product_ids);
            } elseif ($request->has('category_ids') && count($request->category_ids)) {
                $products = Product::whereIn('category_id', $request->category_ids)->get();
                $categoryProducts = $products->groupBy('category_id');
                $emptyCategories = [];
                foreach ($request->category_ids as $categoryId) {
                    if (!$categoryProducts->has($categoryId)) {
                        $category = Category::find($categoryId);
                        $emptyCategories[] = $category?->name ?? 'تصنيف #' . $categoryId;
                    }
                }
                if (!empty($emptyCategories)) {
                    return back()->with('error', 'التصنيفات التالية لا تحتوي على منتجات: ' . implode('، ', $emptyCategories))->withInput();
                }
                $section->products()->sync($products->pluck('id')->toArray());
            }
            DB::commit();
            $this->flushHomeSectionsCache();
            return redirect()->route('admin.sections.index')->with('success', 'تم الحفظ بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage())->withInput();
        }
    }

    private function flushHomeSectionsCache(): void
    {
        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        if (empty($locales)) {
            $locales = (array) config('translatable.locales', []);
        }
        if (empty($locales)) {
            $locales = ['ar', 'en'];
        }

        foreach ($locales as $locale) {
            Cache::forget("home.sections.$locale");
            Cache::forget("home.products.$locale");
        }
    }
}
