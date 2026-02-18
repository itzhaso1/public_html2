<?php

namespace App\Observers;

use App\Jobs\SendWasenderWhatsAppMessage;
use App\Models\CashExchangeRequest;
use App\Models\ManualPaymentRequest;
use App\Models\MoneyExchangeRequest;
use App\Models\Order;
use App\Support\WhatsApp\WhatsAppNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NewDashboardRequestWhatsAppObserver
{
    public function created(Model $model): void
    {
        // Ensure user phone is available for customer notifications
        try {
            if (method_exists($model, 'loadMissing')) {
                $model->loadMissing(['user.profile']);
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $recipients = $this->recipients();
        $adminText = $this->buildAdminMessage($model);
        if ($adminText !== '' && !empty($recipients)) {
            foreach ($recipients as $to) {
                // After-response so we don't slow down the customer request.
                $this->dispatchSafely($to, $adminText);
            }
        }

        if ((bool) config('services.wasender.notify_customers', true)) {
            $customerTo = $this->customerNumber($model);
            $customerText = $this->buildCustomerMessage($model);
            if ($customerTo !== '' && $customerText !== '') {
                $this->dispatchSafely($customerTo, $customerText);
            }
        }
    }

    private function recipients(): array
    {
        if (! (bool) config('services.wasender.enabled', false)) {
            return [];
        }

        $list = (array) config('services.wasender.notify_to', []);
        $out = [];
        foreach ($list as $to) {
            $digits = preg_replace('/\D+/', '', (string) $to) ?: '';
            if ($digits !== '') {
                $out[] = $digits;
            }
        }
        return array_values(array_unique($out));
    }

    private function buildAdminMessage(Model $model): string
    {
        if ($model instanceof Order) {
            $number = $model->number ?? $model->id;
            return trim(
                "طلب جديد: شراء من المتجر\n" .
                "رقم الطلب: {$number}\n" .
                "المبلغ: {$model->total_price}\n" .
                "الحالة: {$model->status}\n"
            );
        }

        if ($model instanceof ManualPaymentRequest) {
            $url = $this->safeRoute('admin.manual_payments.show', $model->id);
            return trim(
                "طلب جديد: دفع يدوي\n" .
                "المرجع: {$model->reference}\n" .
                "المنتج ID: {$model->product_id}\n" .
                "المبلغ: {$model->amount} {$model->currency}\n" .
                "الطريقة: {$model->payment_method}\n" .
                "الحالة: {$model->status}\n" .
                ($url ? "رابط الداشبورد: {$url}\n" : '')
            );
        }

        if ($model instanceof CashExchangeRequest) {
            $url = $this->safeRoute('admin.cash_exchange.requests.show', $model->id);
            return trim(
                "طلب جديد: استبدال رصيد كاش\n" .
                "المرجع: {$model->reference}\n" .
                "الفئة ID: {$model->offer_id}\n" .
                "القيمة: {$model->face_value}\n" .
                "المبلغ: {$model->cash_value} {$model->currency}\n" .
                "الحالة: {$model->status}\n" .
                ($url ? "رابط الداشبورد: {$url}\n" : '')
            );
        }

        if ($model instanceof MoneyExchangeRequest) {
            $dirLabel = ($model->direction ?? '') === 'usdt_to_sar' ? 'USDT → SAR' : 'SAR → USDT';
            $url = $this->safeRoute('admin.money_exchange.requests.show', $model->id);
            return trim(
                "طلب جديد: تحويل الأموال\n" .
                "المرجع: {$model->reference}\n" .
                "النوع: {$dirLabel}\n" .
                "من: {$model->amount_from}\n" .
                "إلى: {$model->amount_to}\n" .
                "الحالة: {$model->status}\n" .
                ($url ? "رابط الداشبورد: {$url}\n" : '')
            );
        }

        return '';
    }

    private function customerNumber(Model $model): string
    {
        if ($model instanceof Order) {
            try {
                $model->loadMissing(['user.profile', 'addresses']);
            } catch (\Throwable $e) {}

            $uPhone = WhatsAppNumber::normalize($model->user?->phone ?? '');
            if ($uPhone !== '') return $uPhone;
            $pPhone = WhatsAppNumber::normalize($model->user?->profile?->phone ?? '');
            if ($pPhone !== '') return $pPhone;
            $addrPhone = WhatsAppNumber::normalize(optional($model->addresses?->first())->phone ?? '');
            return $addrPhone;
        }

        // Prefer explicit contact_phone (if ever used), then user phone, then user profile phone.
        $contact = method_exists($model, 'getAttribute') ? (string) ($model->getAttribute('contact_phone') ?? '') : '';
        $contact = WhatsAppNumber::normalize($contact);
        if ($contact !== '') return $contact;

        $user = method_exists($model, 'user') ? $model->user : null;
        if (!$user && method_exists($model, 'getAttribute')) {
            $user = $model->getAttribute('user');
        }

        $uPhone = WhatsAppNumber::normalize($user?->phone ?? '');
        if ($uPhone !== '') return $uPhone;

        try {
            $profilePhone = WhatsAppNumber::normalize($user?->profile?->phone ?? '');
            return $profilePhone;
        } catch (\Throwable $e) {
            return '';
        }
    }

    private function buildCustomerMessage(Model $model): string
    {
        $app = (string) config('app.name', 'المتجر');
        $base = rtrim((string) config('app.url', ''), '/');

        if ($model instanceof Order) {
            $number = $model->number ?? $model->id;
            $link = $this->safeRoute('customer.orders_by_status', 'pending');
            return trim(
                "{$app}\n" .
                "تم استلام طلبك ✅\n" .
                "رقم الطلب: {$number}\n" .
                "المبلغ: {$model->total_price}\n" .
                "الحالة: قيد الانتظار\n" .
                ($link ? "متابعة الطلبات: {$link}\n" : ($base ? "متابعة الطلبات: {$base}/ar/customer/orders\n" : ''))
            );
        }

        if ($model instanceof ManualPaymentRequest) {
            $link = $base ? ($base . '/ar/diamonds/manual-payment/thanks/' . $model->reference) : null;
            return trim(
                "{$app}\n" .
                "تم استلام طلبك ✅\n" .
                "رقم الطلب: {$model->reference}\n" .
                "الحالة: قيد المراجعة\n" .
                ($link ? "تفاصيل الطلب: {$link}\n" : '')
            );
        }

        if ($model instanceof CashExchangeRequest) {
            $link = $base ? ($base . '/ar/cash-exchange/requests/' . $model->reference) : null;
            return trim(
                "{$app}\n" .
                "تم استلام طلبك ✅\n" .
                "الخدمة: استبدال رصيدك كاش\n" .
                "رقم الطلب: {$model->reference}\n" .
                "الحالة: قيد المراجعة\n" .
                ($link ? "تفاصيل الطلب: {$link}\n" : '')
            );
        }

        if ($model instanceof MoneyExchangeRequest) {
            $link = $base ? ($base . '/ar/customer/money-exchange/' . $model->reference) : null;
            $dirLabel = ($model->direction ?? '') === 'usdt_to_sar' ? 'USDT → SAR' : 'SAR → USDT';
            return trim(
                "{$app}\n" .
                "تم استلام طلبك ✅\n" .
                "الخدمة: تحويل الأموال ({$dirLabel})\n" .
                "رقم الطلب: {$model->reference}\n" .
                "الحالة: معلق\n" .
                ($link ? "تفاصيل الطلب: {$link}\n" : '')
            );
        }

        return '';
    }

    private function safeRoute(string $name, mixed $param): ?string
    {
        try {
            return route($name, $param);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function dispatchSafely(string $to, string $text): void
    {
        try {
            if (DB::transactionLevel() > 0) {
                DB::afterCommit(function () use ($to, $text) {
                    SendWasenderWhatsAppMessage::dispatch($to, $text)->afterResponse();
                });
                return;
            }
        } catch (\Throwable $e) {
            // ignore
        }
        SendWasenderWhatsAppMessage::dispatch($to, $text)->afterResponse();
    }
}

