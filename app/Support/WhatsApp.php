<?php

namespace App\Support;

class WhatsApp
{
    public static function normalizeNumber(?string $raw): string
    {
        $digits = preg_replace('/\D+/', '', (string) $raw) ?? '';

        // Convert international prefix 00xxxxxxxx -> xxxxxxxx
        $digits = preg_replace('/^00/', '', $digits) ?? $digits;

        // Common local-trunk "0" that appears after country code in some stored numbers.
        // Example: +962 0 777... should become 962777...
        foreach (['962', '966'] as $cc) {
            if (str_starts_with($digits, $cc . '0')) {
                $digits = $cc . substr($digits, strlen($cc) + 1);
                break;
            }
        }

        return $digits;
    }

    public static function href(?string $number = null, ?string $text = null): ?string
    {
        $custom = trim((string) config('contact.whatsapp.link'));
        if ($custom !== '') {
            return $custom;
        }

        $digits = self::normalizeNumber($number ?? (string) config('contact.whatsapp.number'));
        if ($digits === '') {
            return null;
        }

        $url = 'https://wa.me/' . $digits;

        $text = $text !== null ? trim($text) : '';
        if ($text !== '') {
            $url .= '?text=' . rawurlencode($text);
        }

        return $url;
    }

    public static function display(): string
    {
        $display = trim((string) config('contact.whatsapp.display'));
        if ($display !== '') {
            return $display;
        }

        $digits = self::normalizeNumber((string) config('contact.whatsapp.number'));
        return $digits !== '' ? ('+' . $digits) : '';
    }
}

