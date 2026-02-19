<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\WhatsApp\WasenderNotifier;
use App\Support\WhatsApp\WhatsAppNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicProductRequestController extends Controller
{
    private const REASONS = [
        'البروفايل بدون نص المتجر',
        'الصور غير واضحة',
        'صور المعرض ناقصة أو مكررة',
        'الوصف غير مطابق/غير كامل',
        'السعر غير مناسب',
        'بيانات ناقصة',
        'الحساب مخالف للشروط',
    ];

    public function index(Request $request)
    {
        $filter = (string) $request->query('status', 'pending');
        if (!in_array($filter, ['pending', 'approved', 'rejected', 'all'], true)) {
            $filter = 'pending';
        }

        $q = Product::query()
            ->with(['translations', 'media'])
            ->where('publish_source', 'public')
            ->whereNull('service_type')
            ->latest();

        if ($filter !== 'all') {
            $map = [
                'pending' => 'draft',
                'approved' => 'published',
                'rejected' => 'archived',
            ];
            $q->where('status', $map[$filter]);
        }

        $requests = $q->paginate(40)->withQueryString();

        return view('dashboard.admin.public_products.index', [
            'pageTitle' => 'طلبات نشر الحسابات (مراجعة قبل النشر)',
            'requests' => $requests,
            'status' => $filter,
        ]);
    }

    public function show(Product $product)
    {
        $this->ensurePublic($product);
        $product->load(['translations', 'media']);

        $mainImage = $product->getMediaUrl('product', $product, null, 'media', 'product');
        $galleryImages = $product->getMultipleMediaUrls('product/gallery', $product, 'media', 'gallery');

        return view('dashboard.admin.public_products.show', [
            'pageTitle' => 'مراجعة طلب نشر الحساب',
            'product' => $product,
            'mainImage' => $mainImage,
            'galleryImages' => $galleryImages,
            'reasons' => self::REASONS,
        ]);
    }

    public function approve(Request $request, Product $product)
    {
        $this->ensurePublic($product);

        if ((string) ($product->status ?? '') !== 'draft') {
            return back()->withErrors(['error' => 'لا يمكن الموافقة لأن الطلب ليس قيد المراجعة.']);
        }

        $data = $request->validate([
            'review_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $product->update([
            'status' => 'published',
            'published_at' => now(),
            'review_note' => $data['review_note'] ?? null,
            'review_reject_reasons' => null,
            'reviewed_by' => auth('admin')->id(),
            'reviewed_at' => now(),
            'rejected_at' => null,
        ]);

        $this->flushWebsiteProductCaches();
        $this->notifyPublisher($product, true);

        return back()->with('success', 'تمت الموافقة وتم نشر الحساب ✅');
    }

    public function reject(Request $request, Product $product)
    {
        $this->ensurePublic($product);

        if ((string) ($product->status ?? '') !== 'draft') {
            return back()->withErrors(['error' => 'لا يمكن الرفض لأن الطلب ليس قيد المراجعة.']);
        }

        $data = $request->validate([
            'review_reject_reasons' => ['required', 'array', 'min:1'],
            'review_reject_reasons.*' => ['required', 'string', 'in:' . implode(',', self::REASONS)],
            'review_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $product->update([
            'status' => 'archived',
            'published_at' => null,
            'review_reject_reasons' => array_values(array_unique($data['review_reject_reasons'] ?? [])),
            'review_note' => $data['review_note'] ?? null,
            'reviewed_by' => auth('admin')->id(),
            'reviewed_at' => now(),
            'rejected_at' => now(),
        ]);

        $this->flushWebsiteProductCaches();
        $this->notifyPublisher($product, false);

        return back()->with('success', 'تم رفض الطلب ❌');
    }

    private function ensurePublic(Product $product): void
    {
        if ((string) ($product->publish_source ?? '') !== 'public') {
            abort(404);
        }
    }

    private function flushWebsiteProductCaches(): void
    {
        $locales = array_keys((array) config('laravellocalization.supportedLocales', []));
        if (empty($locales)) {
            $locales = (array) config('translatable.locales', []);
        }
        if (empty($locales)) {
            $locales = ['ar', 'en'];
        }

        foreach ($locales as $locale) {
            Cache::forget("home.products.$locale");
            Cache::forget("home.sections.$locale");
        }
    }

    private function notifyPublisher(Product $product, bool $approved): void
    {
        if (! (bool) config('services.wasender.enabled', false)) return;
        if (! (bool) config('services.wasender.notify_customers', true)) return;

        $to = WhatsAppNumber::normalize((string) ($product->client_number ?? ''));
        if ($to === '') return;

        $app = (string) config('app.name', 'المتجر');
        $name = (string) ($product->name ?? '');
        $trackUrl = route('public.products.track', ['slug' => $product->slug]);
        $note = trim((string) ($product->review_note ?? ''));
        $noteLine = $note !== '' ? ("\nملاحظة الإدارة: " . mb_substr($note, 0, 250)) : '';

        if ($approved) {
            $productUrl = route('website.product.show', $product);
            $text = trim(
                "{$app}\n" .
                "تم قبول طلب نشر حسابك ✅\n" .
                ($name !== '' ? "اسم الحساب: {$name}\n" : '') .
                "رابط الإعلان: {$productUrl}\n" .
                "متابعة الطلب: {$trackUrl}" .
                $noteLine
            );
        } else {
            $reasons = (array) ($product->review_reject_reasons ?? []);
            $reasonsLines = '';
            foreach ($reasons as $r) {
                $r = trim((string) $r);
                if ($r === '') continue;
                $reasonsLines .= "- {$r}\n";
            }
            $reasonsBlock = trim($reasonsLines) !== '' ? ("\nالأسباب:\n" . trim($reasonsLines)) : '';

            $text = trim(
                "{$app}\n" .
                "تم رفض طلب نشر حسابك ❌\n" .
                ($name !== '' ? "اسم الحساب: {$name}\n" : '') .
                $reasonsBlock .
                $noteLine . "\n" .
                "متابعة الطلب: {$trackUrl}"
            );
        }

        WasenderNotifier::sendAfterCommit($to, $text);
    }
}

