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
        'send_lock_seconds' => (int) env('AUTO_BANNED_HSCT_SEND_LOCK_SECONDS', 600),
        'use_dummy_when_empty' => (bool) env('AUTO_BANNED_HSCT_USE_DUMMY', false),
    ],

    'daily_banned' => [
        'timezone' => env('AUTO_BANNED_DAILY_TIMEZONE', 'Asia/Makassar'),
        'recipients' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('AUTO_BANNED_DAILY_EMAILS', ''))
        ))),
        'send_time' => env('AUTO_BANNED_DAILY_SEND_TIME', '08:00'),
        'send_lock_seconds' => (int) env('AUTO_BANNED_DAILY_SEND_LOCK_SECONDS', 600),
    ],

    'poll' => [
        'trigger_on_page_load' => (bool) env('AUTO_BANNED_POLL_ON_PAGE_LOAD', false),
        'lock_seconds' => (int) env('AUTO_BANNED_POLL_LOCK_SECONDS', 300),
        'stale_running_minutes' => (int) env('AUTO_BANNED_POLL_STALE_MINUTES', 10),
        'min_interval_seconds' => (int) env('AUTO_BANNED_POLL_MIN_INTERVAL', 60),
    ],

    'ban_verify' => [
        'connection' => env('AUTO_BANNED_VERIFY_CONNECTION', 'pgsql_ssh'),
        'schema' => env('AUTO_BANNED_VERIFY_SCHEMA', 'bcsid'),
        'table' => env('AUTO_BANNED_VERIFY_TABLE', 'bep_vw_safety_all_karyawan'),
        'sid_column' => env('AUTO_BANNED_VERIFY_SID_COLUMN', 'kode_sid'),
        'status_column' => env('AUTO_BANNED_VERIFY_STATUS_COLUMN', 'status_permit'),
        'week_column' => env('AUTO_BANNED_VERIFY_WEEK_COLUMN', ''),
        'year_column' => env('AUTO_BANNED_VERIFY_YEAR_COLUMN', ''),
        'executed_values' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('AUTO_BANNED_VERIFY_EXECUTED_VALUES', 'NOT PASSED'))
        ))),
        'require_ssh_tunnel' => (bool) env('AUTO_BANNED_VERIFY_REQUIRE_SSH_TUNNEL', true),
    ],

    'treatment' => [
        'max_upload_kb' => (int) env('AUTO_BANNED_TREATMENT_MAX_UPLOAD_KB', 10240),
        'allowed_mimes' => ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'heic', 'heif', 'doc', 'docx', 'xlsx', 'xls'],
        'allowed_mime_types' => [
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp',
            'image/heic',
            'image/heif',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/octet-stream',
        ],
        'public_form_enabled' => (bool) env('AUTO_BANNED_PUBLIC_TREATMENT_FORM', true),
    ],
];
