<?php

namespace App\Services\Wasender;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WasenderClient
{
    public function sendText(string $toE164, string $text): array
    {
        $baseUrl = (string) config('wasender.base_url');
        $apiKey = (string) config('wasender.api_key');
        $timeout = (int) config('wasender.timeout_seconds', 20);

        if ($apiKey === '') {
            return [
                'success' => false,
                'error' => 'Missing WASENDER_API_KEY',
            ];
        }

        $url = rtrim($baseUrl, '/') . '/send-message';

        try {
            $response = Http::timeout($timeout)
                ->acceptJson()
                ->asJson()
                ->withToken($apiKey)
                ->post($url, [
                    'to' => $toE164,
                    'text' => $text,
                ]);

            return [
                'success' => $response->successful(),
                'status' => $response->status(),
                'json' => $response->json(),
                'raw' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('Wasender sendText exception', [
                'message' => $e->getMessage(),
                'to' => $toE164,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

