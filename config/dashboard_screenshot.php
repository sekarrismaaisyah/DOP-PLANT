<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard URL untuk screenshot
    |--------------------------------------------------------------------------
    */
    'url' => env('DASHBOARD_SCREENSHOT_URL', 'https://besentry-dev.beraucoal.co.id/dopmikk/dopm/dashboard'),

    /*
    |--------------------------------------------------------------------------
    | Email penerima (pisahkan dengan koma di .env)
    |--------------------------------------------------------------------------
    */
    'emails' => array_filter(array_map('trim', explode(',', env('DASHBOARD_SCREENSHOT_EMAILS', '')))),

    /*
    |--------------------------------------------------------------------------
    | Direktori penyimpanan sementara screenshot
    |--------------------------------------------------------------------------
    */
    'storage_path' => storage_path('app/dashboard-screenshots'),

    /*
    |--------------------------------------------------------------------------
    | Timeout Browsershot (detik)
    |--------------------------------------------------------------------------
    */
    'timeout' => (int) env('DASHBOARD_SCREENSHOT_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Lebar viewport (px)
    |--------------------------------------------------------------------------
    */
    'width' => (int) env('DASHBOARD_SCREENSHOT_WIDTH', 1920),

    /*
    |--------------------------------------------------------------------------
    | Tinggi viewport (px) - full page akan diambil jika perlu
    |--------------------------------------------------------------------------
    */
    'height' => (int) env('DASHBOARD_SCREENSHOT_HEIGHT', 1080),

];
