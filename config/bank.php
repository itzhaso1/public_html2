<?php

return [
    /*
     * Manual bank transfer details (shown to customers).
     * Configure via environment variables on your server.
     */
    'enabled' => env('MANUAL_BANK_TRANSFER_ENABLED', true),

    // Backward-compatible Saudi bank fields (used by the Saudi method below)
    'bank_name' => env('BANK_NAME', ''),
    'account_name' => env('BANK_ACCOUNT_NAME', ''),
    'account_number' => env('BANK_ACCOUNT_NUMBER', ''),
    'iban' => env('BANK_IBAN', ''),
    'note' => env('BANK_NOTE', ''),
    'whatsapp' => env('BANK_WHATSAPP', ''),

    /*
     * Supported manual payment methods (for charge + codes).
     * You can disable any method by setting its env enabled flag to false.
     */
    'methods' => [
        // السعودية: تحويل بنكي
        'sa_bank' => [
            'enabled' => env('PAY_SA_ENABLED', true),
            'title' => env('PAY_SA_TITLE', 'السعودية - تحويل بنكي'),
            'bank_name' => env('BANK_NAME', ''),
            'account_name' => env('BANK_ACCOUNT_NAME', ''),
            'account_number' => env('BANK_ACCOUNT_NUMBER', ''),
            'iban' => env('BANK_IBAN', ''),
            'note' => env('BANK_NOTE', ''),
        ],

        // الأردن: Click - بنك الاتحاد
        'jo_click' => [
            'enabled' => env('PAY_JO_CLICK_ENABLED', true),
            'title' => env('PAY_JO_CLICK_TITLE', 'الأردن كليك - بنك الاتحاد'),
            'bank_name' => env('JO_CLICK_BANK_NAME', 'بنك الاتحاد'),
            'account_name' => env('JO_CLICK_ACCOUNT_NAME', 'HASO2'),
            // Optional: click id / phone / account identifier
            'click_id' => env('JO_CLICK_ID', ''),
            'note' => env('JO_CLICK_NOTE', ''),
        ],

        // Binance: USDT TRC20
        'binance_trc20' => [
            'enabled' => env('PAY_BINANCE_ENABLED', true),
            'title' => env('PAY_BINANCE_TITLE', 'Binance - USDT (TRC20)'),
            'network' => env('BINANCE_TRC20_NETWORK', 'TRC20'),
            // Put either address or a payment link (or both)
            'address' => env('BINANCE_TRC20_ADDRESS', ''),
            'link' => env('BINANCE_TRC20_LINK', ''),
            'note' => env('BINANCE_TRC20_NOTE', ''),
        ],
    ],

    // Which methods are allowed for the "charge" flow
    'charge_method_keys' => ['sa_bank', 'jo_click', 'binance_trc20'],
];

