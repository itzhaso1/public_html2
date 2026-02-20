<?php

namespace App\Support\WhatsApp;

use App\Services\Integrations\Wasender\WasenderApiService;
use Illuminate\Support\Facades\DB;

class WasenderNotifier
{
    public static function send(string $to, string $text): bool
    {
        $to = WhatsAppNumber::normalize($to);
        $text = trim($text);
        if ($to === '' || $text === '') return false;

        /** @var WasenderApiService $api */
        $api = app(WasenderApiService::class);
        return $api->sendMessage($to, $text);
    }

    public static function sendAfterCommit(string $to, string $text): void
    {
        try {
            if (DB::transactionLevel() > 0) {
                DB::afterCommit(function () use ($to, $text) {
                    self::send($to, $text);
                });
                return;
            }
        } catch (\Throwable $e) {
            // fall through
        }

        self::send($to, $text);
    }
}

