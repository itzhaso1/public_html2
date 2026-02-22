<?php

return [
    'enabled' => (bool) env('WASENDER_ENABLED', false),

    'base_url' => rtrim((string) env('WASENDER_BASE_URL', 'https://www.wasenderapi.com/api'), '/'),
    'api_key' => (string) env('WASENDER_API_KEY', ''),

    /*
     * Comma-separated list of numbers to notify (admins/support).
     * Example: 9665xxxxxxx,9627xxxxxxx
     */
    'notify_to' => array_values(array_filter(array_map(
        static fn ($v) => trim((string) $v),
        explode(',', (string) env('WASENDER_NOTIFY_TO', ''))
    ))),

    'notify_customers' => (bool) env('WASENDER_NOTIFY_CUSTOMERS', false),

    'timeout_seconds' => (int) env('WASENDER_TIMEOUT_SECONDS', 20),
];

