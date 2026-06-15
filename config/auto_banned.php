<?php

declare(strict_types=1);

return [
    'hsct' => [
        'timezone' => env('AUTO_BANNED_HSCT_TIMEZONE', 'Asia/Makassar'),
        'recipients' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('AUTO_BANNED_HSCT_EMAILS', ''))
        ))),
        'initial_day' => (int) env('AUTO_BANNED_HSCT_INITIAL_DAY', 2), // 0=Sun, 2=Tuesday
        'send_time' => env('AUTO_BANNED_HSCT_SEND_TIME', '08:00'),
        'use_dummy_when_empty' => (bool) env('AUTO_BANNED_HSCT_USE_DUMMY', true),
    ],
];
