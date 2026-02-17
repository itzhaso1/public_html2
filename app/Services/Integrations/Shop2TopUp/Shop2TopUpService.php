<?php

namespace App\Services\Integrations\Shop2TopUp;

use Illuminate\Support\Facades\Http;

class Shop2TopUpService
{
    public function __construct(
        private readonly ?string $baseUrl = null,
        private readonly ?string $apiKey = null,
    ) {
        //
    }

    private function baseUrl(): string
    {
        return rtrim($this->baseUrl ?? (string) config('services.shop2topup.base_url'), '/');
    }

    private function apiKey(): string
    {
        return (string) ($this->apiKey ?? config('services.shop2topup.api_key'));
    }

    /**
     * @return array{success:bool, offers:array<int, array{name:string,itemId:int,price:string}>, msg?:string}
     */
    public function getOffers(): array
    {
        $key = $this->apiKey();
        if ($key === '') {
            return ['success' => false, 'offers' => [], 'msg' => 'SHOP2TOPUP_API_KEY غير مضبوط'];
        }

        $timeout = (int) config('services.shop2topup.timeout', 20);

        $response = Http::timeout($timeout)
            ->acceptJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $key,
            ])
            ->get($this->baseUrl() . '/offers');

        if (! $response->successful()) {
            $json = $response->json();
            $msg = is_array($json) ? ($json['msg'] ?? null) : null;
            return [
                'success' => false,
                'offers' => [],
                'msg' => $msg ?: ('HTTP ' . $response->status()),
            ];
        }

        $data = $response->json();
        if (! is_array($data)) {
            return ['success' => false, 'offers' => [], 'msg' => 'رد غير صالح من المزود'];
        }

        return [
            'success' => (bool) ($data['success'] ?? false),
            'offers' => (array) ($data['offers'] ?? []),
            'msg' => (string) ($data['msg'] ?? ''),
        ];
    }

    /**
     * @return array{success:bool, balance?:string, msg?:string}
     */
    public function getBalance(): array
    {
        $key = $this->apiKey();
        if ($key === '') {
            return ['success' => false, 'msg' => 'SHOP2TOPUP_API_KEY غير مضبوط'];
        }

        $timeout = (int) config('services.shop2topup.timeout', 20);

        $response = Http::timeout($timeout)
            ->acceptJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $key,
            ])
            ->get($this->baseUrl() . '/balance');

        if (! $response->successful()) {
            $json = $response->json();
            $msg = is_array($json) ? ($json['msg'] ?? null) : null;
            return [
                'success' => false,
                'msg' => $msg ?: ('HTTP ' . $response->status()),
            ];
        }

        $data = $response->json();
        if (! is_array($data)) {
            return ['success' => false, 'msg' => 'رد غير صالح من المزود'];
        }

        return [
            'success' => (bool) ($data['success'] ?? false),
            'balance' => isset($data['balance']) ? (string) $data['balance'] : null,
            'msg' => (string) ($data['msg'] ?? ''),
        ];
    }
}

