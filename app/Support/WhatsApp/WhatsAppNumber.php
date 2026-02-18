<?php

namespace App\Support\WhatsApp;

class WhatsAppNumber
{
    public static function normalize(?string $value): string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?: '';

        // Handle common international formats like 00XXXXXXXX
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        // Normalize common mistakes for KSA/JO where users keep the leading 0 after country code.
        // Example: 9660XXXXXXXXX -> 966XXXXXXXXX, 9620XXXXXXXXX -> 962XXXXXXXXX
        if (str_starts_with($digits, '9660')) {
            $digits = '966' . substr($digits, 4);
        } elseif (str_starts_with($digits, '9620')) {
            $digits = '962' . substr($digits, 4);
        }

        return (string) $digits;
    }
}

