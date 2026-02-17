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

        return Cache::remember('fx.sar.latest.v1', now()->addHours(12), function () use ($fallback) {
            try {
                $res = Http::timeout(6)->retry(2, 200)->get('https://api.frankfurter.app/latest', [
                    'from' => 'SAR',
                    'to' => 'USD,JOD',
                ]);

                if (! $res->successful()) {
                    return $fallback;
                }

                $data = $res->json() ?: [];
                $rates = $data['rates'] ?? [];

                $usd = isset($rates['USD']) ? (float) $rates['USD'] : null;
                $jod = isset($rates['JOD']) ? (float) $rates['JOD'] : null;

                if (! $usd || ! $jod) {
                    return $fallback;
                }

                return [
                    'USD' => $usd,
                    'JOD' => $jod,
                    'date' => $data['date'] ?? null,
                    'source' => 'frankfurter',
                ];
            } catch (\Throwable $e) {
                return $fallback;
            }
        });
    }
}

