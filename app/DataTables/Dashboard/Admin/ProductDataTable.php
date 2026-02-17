<?php
 
namespace App\DataTables\Dashboard\Admin;
 
use App\DataTables\Base\BaseDataTable;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Utilities\Request as DataTableRequest;
 
class ProductDataTable extends BaseDataTable {
    public function __construct(DataTableRequest $request)
    {
        parent::__construct(new Product);
        $this->request = $request;
    }
 
    public function dataTable($query): EloquentDataTable {
        return (new EloquentDataTable($query))
            ->addColumn('action', function (Product $product) {
                return view('dashboard.admin.products.btn.actions', compact('product'));
            })
            ->editColumn('product', function (Product $product) {
                return '<img src="' . $product->getMediaUrl('product', $product, null, 'media', 'product') . '" class="img-fluid" alt="' . $product?->name . '" style="max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 5px;"/>';
            })
            ->addColumn('category', function (Product $product) {
                return $product?->category?->name;
            })
            ->addColumn('brand', function (Product $product) {
                return $product?->brand?->name;
            })
            ->addColumn('tags', function (Product $product) {
                if ($product->tags->isEmpty()) return '<span class="text-muted">لا يوجد</span>';
                $badges = '';
                foreach ($product->tags as $tag) {
                    $badges .= '<span class="badge bg-info text-dark me-1">' . $tag->name . '</span>';
                }
                return $badges;
            })
            ->editColumn('created_at', function (Product $product) {
                return $this->formatBadge($this->formatDate($product->created_at));
            })
            ->editColumn('updated_at', function (Product $product) {
                return $this->formatBadge($this->formatDate($product->updated_at));
            })
            ->rawColumns(['category','tags','types','action', 'created_at', 'updated_at', 'product']);
    }
 
    public function query(): QueryBuilder
    {
        $query = Product::with(['media','category', 'brand', 'tags'])->latest();

        $group = request()->get('group');
        if ($group === 'accounts') {
            // المنتجات العادية (حسابات): ليست شحن وليست أكواد
            $query->whereNull('service_type');
        } elseif ($group === 'charge') {
            // باقات الشحن (جواهر)
            $query->where('service_type', 'gems');
        } elseif ($group === 'codes') {
            // منتجات الأكواد
            $query->where('service_type', 'codes');
        }

        return $query;
    }
 
    public function getColumns(): array
    {
        return [
            ['name' => 'id', 'data' => 'id', 'title' => '#', 'orderable' => false, 'searchable' => false],
            ['name' => 'name', 'data' => 'name', 'title' => trans('dashboard/admin.product.name')],
            ['name' => 'product', 'data' => 'product', 'title' => 'الصوره', 'orderable' => false, 'searchable' => false],
            ['name' => 'category', 'data' => 'category', 'title' => 'التصنيف', 'orderable' => false, 'searchable' => false],
            ['name' => 'brand', 'data' => 'brand', 'title' => 'الماركه', 'orderable' => false, 'searchable' => false],
            ['name' => 'tags', 'data' => 'tags', 'title' => 'الوسوم', 'orderable' => false, 'searchable' => false],
            ['name' => 'price', 'data' => 'price', 'title' => trans('dashboard/admin.product.price')],
            ['name' => 'created_at', 'data' => 'created_at', 'title' => trans('dashboard/general.created_at'), 'orderable' => false, 'searchable' => false],
            ['name' => 'updated_at', 'data' => 'updated_at', 'title' => trans('dashboard/general.updated_at'), 'orderable' => false, 'searchable' => false],
            ['name' => 'action', 'data' => 'action', 'title' => trans('dashboard/general.actions'), 'orderable' => false, 'searchable' => false],
        ];
    }
}