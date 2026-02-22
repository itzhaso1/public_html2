<?php

namespace App\Services\Wasender;

use App\Support\WhatsApp;
use Illuminate\Support\Facades\Log;

class WasenderNotifier
{
    public function __construct(protected WasenderClient $client)
    {
    }

    public function enabled(): bool
    {
        return (bool) config('wasender.enabled', false);
    }

    public function toE164(?string $rawNumber): ?string
    {
        $digits = WhatsApp::normalizeNumber($rawNumber);
        if ($digits === '') {
            return null;
        }

        return '+' . $digits;
    }

    public function notifyAdmins(string $message): void
    {
        if (! $this->enabled()) {
            return;
        }

        $targets = (array) config('wasender.notify_to', []);
        foreach ($targets as $raw) {
            $to = $this->toE164((string) $raw);
            if (! $to) {
                continue;
            }

            $res = $this->client->sendText($to, $message);
            if (! ($res['success'] ?? false)) {
                Log::warning('Wasender admin notify failed', [
                    'to' => $to,
                    'status' => $res['status'] ?? null,
                    'error' => $res['error'] ?? null,
                    'raw' => $res['raw'] ?? null,
                ]);
            }
        }
    }

    public function notifyCustomer(?string $customerPhone, string $message): void
    {
        if (! $this->enabled()) {
            return;
        }

        if (! (bool) config('wasender.notify_customers', false)) {
            return;
        }

        $to = $this->toE164($customerPhone);
        if (! $to) {
            return;
        }

        $res = $this->client->sendText($to, $message);
        if (! ($res['success'] ?? false)) {
            Log::warning('Wasender customer notify failed', [
                'to' => $to,
                'status' => $res['status'] ?? null,
                'error' => $res['error'] ?? null,
                'raw' => $res['raw'] ?? null,
            ]);
        }
    }
}

