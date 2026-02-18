<?php

namespace App\Services\Currency;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    /**
     * Get SAR based rates for UI conversion.
     *
     * Returns: ['USD' => float, 'JOD' => float, 'date' => ?string, 'source' => string]
     */
    public function sarRates(): array
    {
        // Fallback rates (1 SAR -> X)
        // Note: these are only used if the live provider is unreachable.
        $fallback = [
            'USD' => 0.2666,
            'JOD' => 0.1885,
            'date' => null,
            'source' => 'fallback',
        ];

        $cacheKey = 'fx.sar.latest.v1';
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && isset($cached['USD'], $cached['JOD'])) {
            return $cached;
        }

        try {
            // Keep this fast: rates are only for UI currency display.
            $res = Http::timeout(2)->retry(1, 150)->get('https://api.frankfurter.app/latest', [
                'from' => 'SAR',
                'to' => 'USD,JOD',
            ]);

            if (! $res->successful()) {
                Cache::put($cacheKey, $fallback, now()->addMinutes(30));
                return $fallback;
            }

            $data = $res->json() ?: [];
            $rates = $data['rates'] ?? [];

            $usd = isset($rates['USD']) ? (float) $rates['USD'] : null;
            $jod = isset($rates['JOD']) ? (float) $rates['JOD'] : null;

            if (! $usd || ! $jod) {
                Cache::put($cacheKey, $fallback, now()->addMinutes(30));
                return $fallback;
            }

            $value = [
                'USD' => $usd,
                'JOD' => $jod,
                'date' => $data['date'] ?? null,
                'source' => 'frankfurter',
            ];

            Cache::put($cacheKey, $value, now()->addHours(12));
            return $value;
        } catch (\Throwable $e) {
            Cache::put($cacheKey, $fallback, now()->addMinutes(30));
            return $fallback;
        }
    }
}

