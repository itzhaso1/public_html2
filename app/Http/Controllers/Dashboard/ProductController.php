<?php
 
namespace App\Http\Controllers\Dashboard;
 
use App\DataTables\Dashboard\Admin\ProductDataTable;
use App\Http\Controllers\Controller;
use App\Services\Contracts\ProductInterface;
use App\Models\Product;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Services\Services\ERP\ERPService;
use Illuminate\Support\Str; // مكتبة للنصوص
use Illuminate\Support\Facades\Cache;
 
class ProductController extends Controller
{
    protected ERPService $erpService;
 
    public function __construct(ERPService $erpService, protected ProductDataTable $productDataTable, protected ProductInterface $productInterface)
    {
        $this->productInterface = $productInterface;
        $this->productDataTable = $productDataTable;
        $this->erpService = $erpService;
    }
 
    // --- الدوال الأساسية ---
    public function index(ProductDataTable $productDataTable) { return $this->productInterface->index($this->productDataTable); }
    public function create() { return $this->productInterface->create(); }
    public function store(Request $request) { return $this->productInterface->store($request); }
    public function edit(Product $product) { return $this->productInterface->edit($product); }
    public function update(Request $request, Product $product) { return $this->productInterface->update($request, $product); }
    public function destroy(Product $product) { return $this->productInterface->destroy($product); }
    
    public function import(Request $request) {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        Excel::import(new ProductsImport, $request->file('file'));
        return response()->json(['message' => 'تم حفظ المنتج بنجاح ✅']);
    }
 
    public function testConnection() {
        $result = $this->erpService->testConnection();
        return $result['success'] ? back()->with('success', $result['message']) : back()->with('error', $result['message']);
    }
 
    public function exportProductsToERP() {
        $products = Product::with(['translations', 'category.translations', 'type.translations'])->get();
        $formattedProducts = $this->erpService::formatProductsForERP($products);
        $result = $this->erpService->sendProducts($formattedProducts);
        return $result['success'] ? back()->with('success', 'تم الإرسال بنجاح') : back()->with('error', 'فشل الإرسال');
    }
 
    public function show($id) {
        return redirect()->route('admin.products.index');
    }
 
    // ==========================================
    // ✅ (الجديد) قسم إضافة منتجات الشحن
    // ==========================================
    public function createChargeProduct()
    {
        return view('dashboard.admin.products.create_charge');
    }
 
    public function storeChargeProduct(Request $request)
    {
        // التحقق: نطلب أن تكون القيمة إما gems أو codes
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            // NOTE: this field is actually the service type (gems/codes)
            'type_id' => 'required|in:gems,codes',
        ]);
 
        try {
            // نأخذ أول قسم ونوع موجودين لتجنب الأخطاء
            $categoryId = \DB::table('categories')->value('id');
            $typeId = \DB::table('types')->value('id');

            if (! $categoryId) {
                return redirect()->back()->withErrors(['error' => 'لا يوجد تصنيف (Category) في النظام. أضف تصنيف واحد على الأقل ثم أعد المحاولة.']);
            }

            $baseSlug = Str::slug($request->name) ?: ('charge-'.time());
            $slug = $baseSlug.'-'.Str::lower(Str::random(6)).'-'.time();

            // SKU should be unique enough (even on fast repeated submits)
            $sku = 'CHG-'.Str::lower(Str::random(6)).'-'.time();

            $id = \DB::table('products')->insertGetId([
                'slug'         => $slug,
                'type'         => 'simple',
                'category_id'  => $categoryId,
                'type_id'      => $typeId ?: null,
                'service_type' => $request->type_id, // gems/codes
                'price'        => $request->price,
                'stock'        => 9999,
                'sku'          => $sku,
                'status'       => 'published',
                'published_at' => now(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
 
            // حفظ الاسم في الترجمة
            try {
                \DB::table('product_translations')->insert([
                    ['product_id' => $id, 'locale' => 'ar', 'name' => $request->name, 'description' => $request->name],
                    ['product_id' => $id, 'locale' => 'en', 'name' => $request->name, 'description' => $request->name],
                ]);
            } catch (\Exception $e) {}

            // مسح كاش صفحات الشحن/الأكواد حتى تظهر الباقات مباشرة
            $locales = array_keys(config('laravellocalization.supportedLocales', []));
            if (empty($locales)) {
                $locales = ['ar', 'en'];
            }

            foreach ($locales as $locale) {
                if ($request->type_id === 'gems') {
                    Cache::forget("diamonds.charge.$locale");
                }
                if ($request->type_id === 'codes') {
                    Cache::forget("diamonds.codes.$locale");
                }
            }
 
            return redirect()->back()->with('success', 'تم إضافة الباقة بنجاح ✅ وستظهر مباشرة في القسم.');
 
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    
}