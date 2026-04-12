<?php

/**
 * Data statis untuk halaman alignment demo.
 * Di-require dari index.blade.php agar variabel berada di scope view yang sama ($wt, $kpi, $alignmentPageMocks, …).
 */

$chartPeriodMonth = false;
$chartYear = null;
$chartMonth = null;
$q = '';
$peerResetParams = [];
$pickerYear = 2026;
$pickerMonth = 4;
$cy = 2026;
$cm = 4;
$monthsShort = [1 => 'Jan', 2 => 'Peb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];

$deviationCategories = [
    ['key' => 'tidak_speak_up_fatigue', 'label' => 'Tidak speak up / fatigue', 'color' => '#64748b'],
    ['key' => 'blindspot_to_be_concerned', 'label' => 'Blind spot', 'color' => '#818cf8'],
    ['key' => 'pelanggaran_pspp', 'label' => 'Pelanggaran PSPP', 'color' => '#3952bc'],
    ['key' => 'pelanggaran_golden_rules', 'label' => 'Golden rules', 'color' => '#72479e'],
    ['key' => 'insiden', 'label' => 'Insiden', 'color' => '#f59e0b'],
    ['key' => 'lainnya', 'label' => 'Lainnya', 'color' => '#94a3b8'],
];
$keys = array_column($deviationCategories, 'key');
$monthBars = [
    ['label' => 'Jan', 'range_short' => 'Jan 2025', 'count' => 12],
    ['label' => 'Peb', 'range_short' => 'Peb 2025', 'count' => 15],
    ['label' => 'Mar', 'range_short' => 'Mar 2025', 'count' => 10],
    ['label' => 'Apr', 'range_short' => 'Apr 2025', 'count' => 18],
    ['label' => 'Mei', 'range_short' => 'Mei 2025', 'count' => 14],
    ['label' => 'Jun', 'range_short' => 'Jun 2025', 'count' => 11],
];
$maxCount = 0;
foreach ($monthBars as $mb) {
    $maxCount = max($maxCount, $mb['count']);
}
$weeks = [];
foreach ($monthBars as $mb) {
    $cnt = $mb['count'];
    $byCategory = [];
    $nKeys = count($keys);
    $base = intdiv($cnt, $nKeys);
    $rem = $cnt % $nKeys;
    foreach ($keys as $ki => $key) {
        $byCategory[$key] = $base + ($ki < $rem ? 1 : 0);
    }
    $stack = [];
    foreach ($keys as $key) {
        $stack[$key] = $cnt > 0 ? round(($byCategory[$key] / $cnt) * 100, 2) : 0.0;
    }
    $weeks[] = [
        'label' => $mb['label'],
        'range_short' => $mb['range_short'],
        'count' => $cnt,
        'bar_height_pct' => $maxCount > 0 ? round(($cnt / $maxCount) * 100, 2) : 0.0,
        'by_category' => $byCategory,
        'category_stack_pct' => $stack,
    ];
}
$sumCounts = array_sum(array_column($monthBars, 'count'));
$avgCount = count($monthBars) > 0 ? round($sumCounts / count($monthBars), 1) : 0.0;

$wt = [
    'weeks' => $weeks,
    'max_count' => $maxCount,
    'avg_count' => $avgCount,
    'target_line_bottom_pct' => 45.0,
    'period_caption' => 'Semua data (per bulan) — demo statis',
    'chart_year' => null,
    'chart_month' => null,
    'month_label' => '',
    'period_scope' => 'all',
    'avg_legend_label' => 'Rata-rata bulanan',
    'chart_granularity' => 'month',
    'deviation_categories' => $deviationCategories,
];

$kpi = [
    'total_cases' => 80,
    'completion_rate' => 72.5,
    'total_cases_trend_pct' => -3.1,
    'total_cases_trend_label' => 'vs periode sebelumnya: -3,1% (dummy)',
    'completion_rate_delta_pp' => 1.2,
    'peer_pressure_compliance_pct' => 68.0,
    'peer_pressure_compliance_comply' => 34,
    'peer_pressure_compliance_total' => 50,
];
$kpiTotal = (int) ($kpi['total_cases'] ?? 0);
$kpiCompletion = (float) ($kpi['completion_rate'] ?? 0);
$kpiBarW = max(0, min(100, $kpiCompletion));
$kpiTrendPct = $kpi['total_cases_trend_pct'] ?? null;

$insightCards = [
    'deviation' => [
        'total' => 80,
        'total_label' => '80',
        'conic_gradient' => 'conic-gradient(#3952bc 0% 25%, #72479e 25% 50%, #f59e0b 50% 72%, #64748b 72% 88%, #94a3b8 88% 100%)',
        'categories' => [
            ['kategori_deviasi' => 'Pelanggaran PSPP', 'pct' => 28.5, 'color' => '#3952bc', 'jumlah' => 23],
            ['kategori_deviasi' => 'Golden rules', 'pct' => 22.0, 'color' => '#72479e', 'jumlah' => 18],
            ['kategori_deviasi' => 'Tidak speak up', 'pct' => 18.0, 'color' => '#64748b', 'jumlah' => 14],
            ['kategori_deviasi' => 'Insiden', 'pct' => 15.5, 'color' => '#f59e0b', 'jumlah' => 12],
            ['kategori_deviasi' => 'Lainnya', 'pct' => 16.0, 'color' => '#94a3b8', 'jumlah' => 13],
        ],
    ],
    'compliance' => [
        'berecord_pct' => 82,
        'evidence_pct' => 76,
        'size_pct' => 71,
        'h1_pct' => 88,
        'duration_label' => '45m',
        'triangle_rotate_deg' => 18,
    ],
    'locations' => [
        ['name' => 'Pit South', 'count' => 22, 'bar_pct' => 92],
        ['name' => 'Stockyard', 'count' => 15, 'bar_pct' => 63],
        ['name' => 'Workshop', 'count' => 10, 'bar_pct' => 42],
    ],
    'profiling_pelanggar' => [
        ['sid' => 'S10001', 'nama' => 'Budi Santoso', 'kasus' => 5, 'insiden_share_pct' => 12.5, 'foto_url' => null],
        ['sid' => 'S10002', 'nama' => 'Andi Wijaya', 'kasus' => 4, 'insiden_share_pct' => 10.0, 'foto_url' => null],
    ],
];
$icPre = $insightCards;
$dvPre = $icPre['deviation'] ?? [];
$dvPreCats = $dvPre['categories'] ?? [];
$dvPreTotal = (int) ($dvPre['total'] ?? 0);
$dvPreSumJumlah = 0;
foreach ($dvPreCats as $r) {
    $dvPreSumJumlah += (int) ($r['jumlah'] ?? 0);
}
$dvPreFooterTotal = $dvPreSumJumlah > 0 ? $dvPreSumJumlah : $dvPreTotal;

$evaluationSummary = [
    'generated_at' => '11 Apr 2026, 10:00 WIB (dummy)',
    'total_kejadian' => 80,
    'narrative' => 'Ini ringkasan evaluasi statis untuk keperluan demo tampilan. Tidak ada koneksi ke basis data atau model AI.',
    'repeat_period_caption' => 'Seluruh data (dummy)',
    'chart_period_month' => false,
    'rows' => [
        [
            'metric' => 'Volume kejadian',
            'description' => 'Agregat dummy untuk ilustrasi dashboard.',
            'status' => 'warning',
            'action_threshold' => 'Pantau tren bulanan',
        ],
        [
            'metric' => 'Kepatuhan peer',
            'description' => 'Proporsi dummy pelaksanaan sesuai kategori.',
            'status' => 'ok',
            'action_threshold' => 'Pertahankan program edukasi',
        ],
        [
            'metric' => 'Pelanggar repetitif',
            'description' => 'Tidak ada data nyata pada halaman demo.',
            'status' => 'ok',
            'action_threshold' => 'Review SOP',
        ],
    ],
];

$alignmentPageMocks = [
    'weekly' => array_merge($wt, [
        'kpi' => $kpi,
        'evaluation_summary' => $evaluationSummary,
        'insight_cards' => $insightCards,
    ]),
    'detail' => [
        'id' => 9001,
        'formatted_temuan' => '10 Apr 2026 08:30',
        'formatted_edukasi' => '11 Apr 2026 09:00',
        'lokasi_temuan' => 'Stockyard A',
        'kelompok_lokasi_temuan' => 'Site',
        'lokasi_edukasi' => 'Meeting room HSE',
        'kelompok_lokasi_edukasi' => 'Kantor',
        'kategori_deviasi' => 'Pelanggaran PSPP',
        'kronologi_temuan' => 'Contoh kronologi dummy untuk modal detail.',
        'perusahaan' => 'PT Demo',
        'departemen' => 'Operasi',
        'aktivitas_pekerjaan' => 'Penggalian',
        'kelompok_aktivitas_pekerjaan' => 'Mining',
        'jenis_kelompok_kerja' => 'Kontraktor',
        'tasklist_temuan' => 'P2H unit',
        'id_berecord' => 'BR-DEMO-001',
        'status_pelaksanaan_edukasi' => 'SELESAI',
        'pemimpin_edukasi' => 'HSE Officer',
        'durasi_edukasi_menit' => 45,
        'evidence_url' => null,
        'status_badge' => [
            'spanClass' => 'inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-800',
            'label' => 'SELESAI',
        ],
        'peserta' => [
            ['sid' => 'S10001', 'nama' => 'Budi Santoso', 'peran' => 'pelanggar', 'foto_url' => null, 'initials' => 'BS', 'urutan' => 1],
            ['sid' => 'S20001', 'nama' => 'Dewi Lestari', 'peran' => 'peer', 'foto_url' => null, 'initials' => 'DL', 'urutan' => 2],
        ],
        'pelanggar_berecord' => null,
    ],
    'highlight' => [
        'rows' => [
            ['judul' => 'Kepatuhan prosedur', 'issue' => 'Data dummy: temuan terkait prosedur kerja.', 'rekomendasi' => 'Perkuat briefing harian dan inspeksi lapangan.'],
            ['judul' => 'Edukasi peer', 'issue' => 'Data dummy: cakupan edukasi bervariasi antar site.', 'rekomendasi' => 'Standarisasi materi dan dokumentasi evidence.'],
        ],
        'period_label' => 'Demo statis (tanpa DB)',
        'generated_at' => '11 Apr 2026',
        'ai_used' => false,
    ],
    'compliance' => [
        'peer_pressure_compliance_pct' => 68.0,
        'peer_pressure_compliance_comply' => 34,
        'peer_pressure_compliance_total' => 50,
        'period_scope' => 'all',
        'period_caption' => 'Semua data (dummy)',
        'recommendations' => ['Pastikan checklist 5 kategori terisi untuk setiap kejadian (dummy).'],
        'rows' => [
            [
                'id' => 9001,
                'tanggal_temuan' => '2026-04-10',
                'kategori_deviasi' => 'Pelanggaran PSPP',
                'bucket_label' => 'PSPP',
                'status_pelaksanaan_edukasi' => 'SELESAI',
                'id_berecord' => 'BR-DEMO-001',
                'alasan' => '—',
                'comply' => true,
            ],
        ],
        'pagination' => [
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => 15,
            'total' => 1,
        ],
    ],
    'profiling' => [
        'window_caption' => 'Jendela 6 bulan terakhir (data dummy).',
        'status_level' => 'normal',
        'npk' => 'S10001',
        'departemen' => 'Operasi',
        'posisi' => 'Operator',
        'grup' => 'Shift A',
        'status_label' => 'Aktif',
        'last_education_label' => 'Apr 2026',
        'riwayat' => [
            ['tanggal_short' => '10/04/2026', 'kategori' => 'PSPP', 'lokasi' => 'Stockyard', 'status' => 'CLOSED'],
        ],
        'korelasi' => ['Korelasi dummy: insiden vs shift malam.'],
        'rekomendasi' => ['Lanjutkan coaching peer untuk kategori serupa.'],
        'recency_gap_days' => [30, 45, 20],
        'recency_trend' => 'stable',
        'recency_caption' => 'Tren recency stabil pada data dummy.',
        'per_orang' => [
            'rows' => [],
        ],
    ],
];

/** Grid mini-chart « Hazards Evaluation » (W10–W13) — data demo */
$hazardEvalGrid = [
    'weeks' => ['W10', 'W11', 'W12', 'W13'],
    'rows' => [
        [
            'label' => 'Hazard Reporting',
            'bar' => '#DEE5EF',
            'values' => [20.595, 15.543, 17.861, 19.570],
            'decimals' => 3,
        ],
        [
            'label' => 'To Be Concerned High Risk Hazards',
            'bar' => '#E2E2E2',
            'values' => [7.600, 5.670, 5.754, 6.237],
            'decimals' => 3,
        ],
        [
            'label' => 'To Be Concerned High Risk Hazards Blindspot',
            'bar' => '#FFCC33',
            'values' => [42, 26, 8, 21],
            'decimals' => 0,
        ],
    ],
];

/** Multi sparkline Hazards Evaluation (W11–W14) — line chart per kategori (data demo, selaras referensi) */
$hazardEvalLineCharts = [
    'weeks' => ['W11', 'W12', 'W13', 'W14'],
    /** Satu warna UI/grafik seperti kategori Pengawasan Tidak Memadai */
    'line_color' => '#4338ca',
    'rows' => [
        ['label' => 'Deviasi pengoperasian kendaraan/unit', 'accent' => '#4338ca', 'trend' => [1569, 1210, 1313, 1376]],
        ['label' => 'Deviasi penggunaan APD', 'accent' => '#4338ca', 'trend' => [453, 303, 330, 355]],
        ['label' => 'Geotech & Hydrology', 'accent' => '#4338ca', 'trend' => [482, 393, 315, 271]],
        ['label' => 'Posisi Pekerja pada Area Tidak aman', 'accent' => '#4338ca', 'trend' => [216, 139, 126, 177]],
        ['label' => 'Deviasi Loading/Dumping', 'accent' => '#4338ca', 'trend' => [658, 489, 512, 553]],
        ['label' => 'Pengawasan Tidak Memadai', 'accent' => '#4338ca', 'trend' => [120, 69, 124, 97]],
        ['label' => 'LOTO', 'accent' => '#4338ca', 'trend' => [64, 36, 37, 33]],
        ['label' => 'Deviasi Road Management', 'accent' => '#4338ca', 'trend' => [2334, 1801, 1797, 2065]],
        ['label' => 'Kesesuaian Dokumen Kerja', 'accent' => '#4338ca', 'trend' => [84, 34, 41, 40]],
        ['label' => 'Tools Tidak Standard', 'accent' => '#4338ca', 'trend' => [559, 407, 387, 474]],
        ['label' => 'Bahaya Elektrikal', 'accent' => '#4338ca', 'trend' => [259, 209, 189, 192]],
        ['label' => 'Bahaya Biologis', 'accent' => '#4338ca', 'trend' => [389, 269, 310, 309]],
        ['label' => 'Aktivitas Drill & Blast', 'accent' => '#4338ca', 'trend' => [228, 159, 116, 156]],
        ['label' => 'Technology', 'accent' => '#4338ca', 'trend' => [189, 152, 173, 145]],
    ],
];

/** Matrix « To Be Concerned Highrisk Hazard » — layout referensi dashboard (data demo) */
$tbchHazardMatrix = [
    'title' => 'To Be Concerned Highrisk Hazard',
    'weeks' => ['W11', 'W12', 'W13', 'W14'],
    'avg_ytd_label' => "Avg YTD'24: 4,566",
    'colors' => [
        'bc' => '#84bd5e',
        'mk' => '#4a7ebb',
    ],
    'rows' => [
        ['n' => 1, 'label' => 'Deviasi pengoperasian kendaraan/unit', 'bg' => '#d8dee9', 'fg' => '#1e293b', 'trend' => [1569, 1210, 1313, 1376], 'gmo' => ['v' => 0], 'lmo' => ['t' => 32, 'bc' => 10, 'mk' => 22]],
        ['n' => 2, 'label' => 'Deviasi penggunaan APD', 'bg' => '#f1f5f9', 'fg' => '#1e293b', 'trend' => [890, 920, 880, 910], 'gmo' => ['v' => 3], 'lmo' => ['t' => 11, 'bc' => 4, 'mk' => 7]],
        ['n' => 3, 'label' => 'Geotech & Hydrology', 'bg' => '#22c55e', 'fg' => '#ffffff', 'trend' => [420, 380, 400, 410], 'gmo' => ['v' => 0], 'lmo' => ['t' => 18, 'bc' => 12, 'mk' => 6]],
        ['n' => 4, 'label' => 'Posisi Pekerja pada Area Tidak aman', 'bg' => '#22d3ee', 'fg' => '#0f172a', 'trend' => [2100, 2050, 2080, 2120], 'gmo' => ['v' => 0], 'lmo' => ['t' => 45, 'bc' => 20, 'mk' => 25]],
        ['n' => 5, 'label' => 'Deviasi Loading/Dumping', 'bg' => '#facc15', 'fg' => '#422006', 'trend' => [560, 540, 550, 530], 'gmo' => ['v' => 2], 'lmo' => ['t' => 9, 'bc' => 3, 'mk' => 6]],
        ['n' => 6, 'label' => 'Pengawasan Tidak Memadai', 'bg' => '#4338ca', 'fg' => '#ffffff', 'trend' => [120, 118, 122, 119], 'gmo' => ['v' => 0], 'lmo' => ['t' => 5, 'bc' => 2, 'mk' => 3]],
        ['n' => 7, 'label' => 'LOTO', 'bg' => '#ea580c', 'fg' => '#ffffff', 'trend' => [340, 330, 335, 328], 'gmo' => ['v' => 0], 'lmo' => ['t' => 7, 'bc' => 3, 'mk' => 4]],
        ['n' => 8, 'label' => 'Deviasi Road Management', 'bg' => '#a855f7', 'fg' => '#ffffff', 'trend' => [780, 760, 770, 765], 'gmo' => ['v' => 0], 'lmo' => ['t' => 14, 'bc' => 5, 'mk' => 9]],
        ['n' => 9, 'label' => 'Kesesuaian Dokumen Kerja', 'bg' => '#94a3b8', 'fg' => '#0f172a', 'trend' => [450, 440, 445, 448], 'gmo' => ['v' => 1], 'lmo' => ['t' => 6, 'bc' => 2, 'mk' => 4]],
        ['n' => 10, 'label' => 'Tools Tidak Standard', 'bg' => '#7dd3fc', 'fg' => '#0c4a6e', 'trend' => [200, 195, 198, 202], 'gmo' => ['v' => 0], 'lmo' => ['t' => 4, 'bc' => 1, 'mk' => 3]],
        ['n' => 11, 'label' => 'Bahaya Elektrikal', 'bg' => '#86efac', 'fg' => '#14532d', 'trend' => [95, 92, 94, 93], 'gmo' => ['v' => 0], 'lmo' => ['t' => 3, 'bc' => 1, 'mk' => 2]],
        ['n' => 12, 'label' => 'Bahaya Biologis', 'bg' => '#57534e', 'fg' => '#fafaf9', 'trend' => [55, 52, 54, 53], 'gmo' => ['v' => 0], 'lmo' => ['t' => 2, 'bc' => 0, 'mk' => 2]],
        ['n' => 13, 'label' => 'Aktivitas Drill & Blast', 'bg' => '#fb7185', 'fg' => '#450a0a', 'trend' => [310, 300, 305, 308], 'gmo' => ['v' => 0], 'lmo' => ['t' => 8, 'bc' => 3, 'mk' => 5]],
        ['n' => 14, 'label' => 'Technology', 'bg' => '#fecaca', 'fg' => '#450a0a', 'trend' => [180, 175, 178, 176], 'gmo' => ['v' => 0], 'lmo' => ['t' => 5, 'bc' => 2, 'mk' => 3]],
    ],
    'tools_pengamatan' => [
        'langsung_pct' => 49,
        'berjarak_pct' => 51,
        'langsung_bc_pct' => 29,
        'langsung_mk_pct' => 71,
        'l2_pct' => 100,
        'l1_pct' => 100,
        'post_event_pct' => 78,
        'realtime_pct' => 22,
    ],
    'perusahaan_pelapor' => [
        'bc_pct' => 14,
        'mk_pct' => 86,
    ],
];
