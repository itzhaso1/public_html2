<?php

namespace App\Observers;

use App\Jobs\SendWasenderWhatsAppMessage;
use App\Models\CashExchangeRequest;
use App\Models\ManualPaymentRequest;
use App\Models\MoneyExchangeRequest;
use Illuminate\Database\Eloquent\Model;

class NewDashboardRequestWhatsAppObserver
{
    public function created(Model $model): void
    {
        $recipients = $this->recipients();
        if (empty($recipients)) {
            return;
        }

        $text = $this->buildMessage($model);
        if ($text === '') {
            return;
        }

        foreach ($recipients as $to) {
            // After-response so we don't slow down the customer request.
            SendWasenderWhatsAppMessage::dispatch($to, $text)->afterResponse();
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

    private function buildMessage(Model $model): string
    {
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

    private function safeRoute(string $name, mixed $param): ?string
    {
        try {
            return route($name, $param);
        } catch (\Throwable $e) {
            return null;
        }
    }
}

