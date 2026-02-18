<?php

namespace App\Support\WhatsApp;

class WhatsAppNumber
{
    public static function normalize(?string $value): string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?: '';
        return (string) $digits;
    }
}

