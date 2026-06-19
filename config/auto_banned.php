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
        'use_dummy_when_empty' => (bool) env('AUTO_BANNED_HSCT_USE_DUMMY', false),
    ],

    'poll' => [
        'trigger_on_page_load' => (bool) env('AUTO_BANNED_POLL_ON_PAGE_LOAD', false),
        'lock_seconds' => (int) env('AUTO_BANNED_POLL_LOCK_SECONDS', 300),
        'stale_running_minutes' => (int) env('AUTO_BANNED_POLL_STALE_MINUTES', 10),
        'min_interval_seconds' => (int) env('AUTO_BANNED_POLL_MIN_INTERVAL', 60),
    ],

    'ban_verify' => [
        'table' => env('AUTO_BANNED_VERIFY_TABLE', ''),
        'sid_column' => env('AUTO_BANNED_VERIFY_SID_COLUMN', 'SID'),
        'status_column' => env('AUTO_BANNED_VERIFY_STATUS_COLUMN', 'Status_Banned_SID_SAP'),
        'week_column' => env('AUTO_BANNED_VERIFY_WEEK_COLUMN', 'Week'),
        'year_column' => env('AUTO_BANNED_VERIFY_YEAR_COLUMN', 'ISO_Year'),
        'executed_values' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('AUTO_BANNED_VERIFY_EXECUTED_VALUES', ''))
        ))),
    ],
];
