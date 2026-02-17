<?php

namespace  App\Repositories;

use App\Models\Category;
use App\Services\Contracts\CategoryInterface;
use Illuminate\Http\Request;
use App\DataTables\Dashboard\Admin\CategoryDataTable;
use Illuminate\Support\Facades\DB;
use App\Imports\CategoryImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
class CategoryRepository implements CategoryInterface {
    public function index(CategoryDataTable $categoryDataTable) {
        return $categoryDataTable->render('dashboard.admin.categories.index', ['pageTitle' => 'التصنيفات']);
    }

    public function create() {
        $categories = Category::getCategoryOptions();
        return view('dashboard.admin.categories.create', [
            'pageTitle' => 'إضافة تصنيف',
            'categories' => $categories
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'status' => 'nullable|in:active,inactive',
            'parent_id' => 'nullable|exists:categories,id',
            'is_featured' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $category = Category::create([
                'parent_id' => $request->parent_id,
                'status' => $request->status,
                'is_featured' => $request->boolean('is_featured'),
            ]);
            foreach (config('laravellocalization.supportedLocales') as $locale => $lang) {
                $category->translateOrNew($locale)->name = $request[$locale]['name'] ?? '';
                $category->translateOrNew($locale)->description = $request[$locale]['description'] ?? '';
                $category->translateOrNew($locale)->short_description = $request[$locale]['short_description'] ?? '';
            }
            $category->save();

            if ($request->hasFile('category')) {
                $category->uploadSingleMedia('category', $request->file('category'), $category, null, 'media', true);
            }
            DB::commit();
            $this->flushWebsiteCategoryCaches();
            return redirect()->route('admin.categories.index')->with('success', 'تم حفظ بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Category $category) {
        $category->load('media');
        $categories = Category::getCategoryOptions();
        return view('dashboard.admin.categories.edit', [
            'pageTitle' => 'تعديل تصنيف',
            'category' => $category,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Category $category) {
        $request->validate([
            'status' => 'nullable|in:active,inactive',
            'parent_id' => 'nullable|exists:categories,id',
            'is_featured' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $category->update([
                'parent_id' => $request->parent_id,
                'status' => $request->status,
                'is_featured'  => $request->boolean('is_featured'),
            ]);

            foreach (config('laravellocalization.supportedLocales') as $locale => $lang) {
                $category->translateOrNew($locale)->name = $request[$locale]['name'] ?? '';
                $category->translateOrNew($locale)->description = $request[$locale]['description'] ?? '';
                $category->translateOrNew($locale)->short_description = $request[$locale]['short_description'] ?? '';
            }
            $category->save();
            if ($request->hasFile('category')) {
                $category->updateSingleMedia('category', $request->file('category'), $category, null, 'media', true);
            }
            DB::commit();
            $this->flushWebsiteCategoryCaches();
            return redirect()->route('admin.categories.index')->with('success', 'تم حفظ التعديلات بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Category $category) {
        $subCategories = Category::where('parent_id', $category->id)->get();
        if ($subCategories->isNotEmpty()) {
            $subCategoryNames = $subCategories->pluck('name')->implode(', ');
            return redirect()->route('admin.categories.index')->with('error', 'لا يمكن حذف هذا التصنيف لأنه يحتوي على تصنيفات فرعية: ' . $subCategoryNames);
        }
        $category->deleteExistingMedia('category', $category, null, 'media', true, 'category');
        $category->delete();
        $this->flushWebsiteCategoryCaches();
        return redirect()->route('admin.categories.index')->with('success', 'تم الحذف بنجاح!');
    }

    private function flushWebsiteCategoryCaches(): void
    {
        $locales = array_keys(config('laravellocalization.supportedLocales', []));
        if (empty($locales)) {
            $locales = (array) config('translatable.locales', []);
        }
        if (empty($locales)) {
            $locales = ['ar', 'en'];
        }

        foreach ($locales as $locale) {
            Cache::forget("website.categories.menu.$locale");
            Cache::forget("home.featured_categories.$locale");
        }
        Cache::forget('shop.categories');
        Cache::forget('shop.subcategories.all');
    }

    public function import(Request $request) {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls']
        ]);
        Excel::import(new CategoryImport, $request->file('file'));
        $this->flushWebsiteCategoryCaches();
        return redirect()->back()->with('success', 'تم استيراد التصنيفات بنجاح');
    }
}
