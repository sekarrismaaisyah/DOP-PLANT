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
];
