<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

use App\Services\Contracts\ProductInterface;
use App\Models\Category;
use App\Models\Type;
use App\Models\Tag;
use App\Models\Product;
use App\Support\WhatsApp\WhatsAppNumber;

class PublicProductController extends Controller
{
    protected ProductInterface $productInterface;

    public function __construct(ProductInterface $productInterface)
    {
        $this->productInterface = $productInterface;
    }

    /**
     * عرض فورم نشر المنتج مباشرة
     */
    public function create()
    {
        return view('public.products.form', [
            'pageTitle' => 'نشر منتج',
            'data' => [
                'categories' => Category::all(),
                'types' => Type::all(),
                'tags' => Tag::all(),
            ],
        ]);
    }

    /**
     * حفظ المنتج
     */
    public function store(Request $request)
    {
        try {
            // Normalize customer WhatsApp number (digits only, fixes common formats).
            $normalizedPhone = WhatsAppNumber::normalize((string) $request->input('client_number', ''));
            if ($normalizedPhone !== '') {
                $request->merge(['client_number' => $normalizedPhone]);
            }

            // Always create as "pending review" before publishing.
            $slug = Str::random(32);
            $payload = [
                'status' => 'draft',
                // Ensure we control the tracking slug; never trust client-provided slug.
                'slug' => $slug,
                'published_at' => null,
            ];
            try {
                if (Schema::hasColumn('products', 'publish_source')) {
                    $payload['publish_source'] = 'public';
                }
            } catch (\Throwable $e) {
                // ignore (migrations not yet applied)
            }
            $request->merge($payload);

            $this->productInterface->store($request);

            $product = Product::query()->where('slug', $slug)->first();

            return redirect()
                ->route('public.products.track', ['slug' => $slug])
                ->with('success', 'تم إرسال طلبك للمراجعة وسيتم نشر الحساب بعد موافقة الإدارة.');

        } catch (ValidationException $e) {

            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Throwable $e) {

            return redirect()
                ->route('home')
                ->with('error', 'حدث خطأ غير متوقع');
        }
    }

    /**
     * Track a public publish request by slug (no login required).
     */
    public function track(string $slug)
    {
        $q = Product::query()
            ->with(['translations', 'media'])
            ->where('slug', $slug);
        try {
            if (Schema::hasColumn('products', 'publish_source')) {
                $q->where('publish_source', 'public');
            }
        } catch (\Throwable $e) {
            // ignore (migrations not yet applied)
        }
        $product = $q->firstOrFail();

        $status = (string) ($product->status ?? 'draft');
        $statusLabel = match ($status) {
            'published' => 'تمت الموافقة (منشور)',
            'archived' => 'مرفوض',
            default => 'قيد المراجعة',
        };

        return view('public.products.track', [
            'pageTitle' => 'متابعة طلب نشر الحساب',
            'product' => $product,
            'statusLabel' => $statusLabel,
        ]);
    }
}
