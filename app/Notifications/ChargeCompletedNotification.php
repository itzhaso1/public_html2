<?php

namespace App\Notifications;

use App\Models\ManualPaymentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Schema;

class ChargeCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(public ManualPaymentRequest $mpr)
    {
        $this->mpr->loadMissing(['product']);
    }

    public function via(object $notifiable): array
    {
        $channels = [];

        // Store in DB if table exists
        if (Schema::hasTable('notifications')) {
            $channels[] = 'database';
        }

        // Email if user has email
        if (! empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $productName = $this->mpr->product?->name ?? 'طلب شحن';
        $ref = $this->mpr->reference ?? ('#' . $this->mpr->id);

        return (new MailMessage)
            ->subject('تم إتمام الشحن ✅')
            ->greeting('مرحباً ' . ($notifiable->name ?? ''))
            ->line('تم إتمام عملية الشحن بنجاح.')
            ->line('الطلب: ' . $ref)
            ->line('المنتج: ' . $productName)
            ->action('عرض مشترياتي', route('customer.purchases'))
            ->line('شكراً لتعاملك معنا.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'charge_completed',
            'manual_payment_request_id' => $this->mpr->id,
            'reference' => $this->mpr->reference,
            'product' => [
                'id' => $this->mpr->product_id,
                'name' => $this->mpr->product?->name,
            ],
            'delivered_at' => optional($this->mpr->shop2topup_delivery_at)->toIso8601String(),
            'status' => $this->mpr->shop2topup_status,
        ];
    }
}

