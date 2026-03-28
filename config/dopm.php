<?php

return [
    /*
    |--------------------------------------------------------------------------
    | IPK/OKK ClickHouse Cutoff Date
    |--------------------------------------------------------------------------
    | Mulai tanggal ini, data IPK dan OKK diambil dari ClickHouse (ipk_assessment,
    | okk_assessment by work_permit_id). Sebelum tanggal ini, data dari MySQL
    | (ipk_ikk, okk) tetap dipakai untuk evaluasi.
    */
    'ipk_okk_clickhouse_cutoff_date' => env('IPK_OKK_CLICKHOUSE_CUTOFF', '2025-02-20'),

    /*
    |--------------------------------------------------------------------------
    | Dashboard weekly (DOPMWeeklyController::dashboard)
    |--------------------------------------------------------------------------
    | Cache response agar filter/week yang sama tidak menjalankan ulang query
    | ClickHouse/MySQL berat. TTL dalam detik; 0 = tanpa cache.
    */
    'weekly_dashboard_cache_ttl' => (int) env('DOPM_WEEKLY_CACHE_TTL', 300),

    /*
    | Batas eksekusi PHP untuk request dashboard (0 = tidak mengubah default php.ini).
    | Naikkan jika masih timeout setelah index + cache.
    */
    'weekly_dashboard_max_execution_seconds' => (int) env('DOPM_WEEKLY_MAX_EXECUTION_SECONDS', 120),
];
