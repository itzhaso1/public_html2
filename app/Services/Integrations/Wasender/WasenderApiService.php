<?php

namespace App\Services\Integrations\Wasender;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WasenderApiService
{
    public function sendMessage(string $to, string $text): bool
    {
        $enabled = (bool) config('services.wasender.enabled', false);
        $apiKey = (string) config('services.wasender.api_key', '');
        $baseUrl = rtrim((string) config('services.wasender.base_url', 'https://www.wasenderapi.com/api'), '/');

        if (! $enabled || $apiKey === '') {
            return false;
        }

        $to = $this->normalizeTo($to);
        $text = trim($text);
        if ($to === '' || $text === '') {
            return false;
        }

        try {
            $res = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(3)
                ->retry(0, 0)
                ->post($baseUrl . '/send-message', [
                    'to' => $to,
                    'text' => $text,
                ]);

            if ($res->successful()) {
                return true;
            }

            Log::warning('Wasender send-message failed', [
                'status' => $res->status(),
                'body' => $res->json() ?? $res->body(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Wasender send-message exception', [
                'error' => $e->getMessage(),
            ]);
        }

        return false;
    }

    private function normalizeTo(string $to): string
    {
        $digits = preg_replace('/\D+/', '', $to) ?: '';
        return (string) $digits;
    }
}

