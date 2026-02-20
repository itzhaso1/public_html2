<?php

namespace  App\Repositories;

use App\Models\{Section,Product, Category};
use App\Services\Contracts\SectionInterface;
use Illuminate\Http\Request;
use App\DataTables\Dashboard\Admin\SectionDataTable;
use Illuminate\Support\Facades\DB;
use App\Actions\Section\StoreSectionAction;
use Illuminate\Support\Facades\Cache;
class SectionRepository implements SectionInterface {
    public function __construct(protected StoreSectionAction $storeAction) {
        $this->storeAction = $storeAction;
    }

    public function index(SectionDataTable $sectionDataTable)
    {
        return $sectionDataTable->render('dashboard.admin.sections.index', [
            'pageTitle' => 'الاقسام'
        ]);
    }

    public function create() {
        $orders = range(1, Section::count() + 1);
        $products = Product::all();
        $categories = Category::active()->get();
        return view('dashboard.admin.sections.create', [
            'pageTitle' => 'إضافة قسم',
            'orders' => $orders,
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function store(Request $request) {
        return $this->storeAction->execute($request);
    }

    public function edit(Section $section)
    {
        $orders = range(1, Section::count());
        $products = Product::all();
        $categories = Category::active()->get();

        return view('dashboard.admin.sections.edit', compact('section', 'orders', 'products', 'categories'));
    }

    public function update(Request $request, Section $section) {
        DB::beginTransaction();
        try {
            $section->update([
                //'category_id' => $request->category_id ?? null,
                'order' => $request->input('order', 0),
            ]);

            foreach (config('laravellocalization.supportedLocales') as $locale => $lang) {
                $section->translateOrNew($locale)->name = $request[$locale]['name'] ?? '';
                $section->translateOrNew($locale)->description = $request[$locale]['description'] ?? '';
            }

            $section->save();

            $section->products()->sync($request->product_ids ?? []);
            $section->categories()->sync($request->category_ids ?? []);

            DB::commit();
            $this->flushHomeSectionsCache();

            return redirect()->route('admin.sections.index')->with('success', 'تم التحديث بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Section $section)
    {
        $section->delete();
        $this->flushHomeSectionsCache();
        return redirect()->route('admin.sections.index')->with('success', 'تم الحذف بنجاح!');
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
            Cache::forget("home.sections.v2.$locale");
            Cache::forget("home.products.v2.$locale");
        }
    }
}