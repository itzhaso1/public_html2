<?php

return [
    'whatsapp' => [
        /*
         * Primary WhatsApp number in international format.
         * Examples:
         * - +962777515306
         * - 966508424351
         */
        'number' => env('WHATSAPP_NUMBER', env('BANK_WHATSAPP', '962777515306')),

        /*
         * Optional custom link (e.g. a WhatsApp group invite URL).
         * If set, the app will use it instead of building a wa.me link from the number.
         */
        'link' => env('WHATSAPP_LINK', ''),

        /*
         * What to display to users (banner/footer/etc).
         */
        'display' => env('WHATSAPP_DISPLAY', '+962 777 515 306'),
    ],

    'instagram_url' => env('INSTAGRAM_URL', 'https://instagram.com/king2game.com'),
];

