@php
    $pointPath = resource_path('data/point.json');
    $pointPayload = is_file($pointPath) ? json_decode((string) file_get_contents($pointPath), true) : [];

    // Requirement: decode JSON string into $jsonData.
    $jsonString = is_array($pointPayload) ? json_encode($pointPayload['rows'] ?? []) : '[]';
    $jsonData = json_decode((string) $jsonString, true) ?? [];

    $programPath = resource_path('data/peer_pressure_thematic_alignment_program.json');
    $programPayload = is_file($programPath) ? json_decode((string) file_get_contents($programPath), true) : [];
    $programRows = is_array($programPayload['rows'] ?? null) ? $programPayload['rows'] : [];
    $sertifikatTeknisPath = resource_path('data/SertifikatTeknis.json');
    $sertifikatTeknisPayload = is_file($sertifikatTeknisPath) ? json_decode((string) file_get_contents($sertifikatTeknisPath), true) : [];
    $sertifikatTeknisRows = is_array($sertifikatTeknisPayload['rows'] ?? null) ? $sertifikatTeknisPayload['rows'] : [];
    $blindspotGrPath = resource_path('data/blindspotGR.json');
    $blindspotGrPayload = is_file($blindspotGrPath) ? json_decode((string) file_get_contents($blindspotGrPath), true) : [];
    $blindspotGrRows = is_array($blindspotGrPayload['rows'] ?? null) ? $blindspotGrPayload['rows'] : [];

    $dataBlindspotGrPath = resource_path('data/dataBlindspotGR.json');
    $dataBlindspotGrRaw = is_file($dataBlindspotGrPath)
        ? json_decode((string) file_get_contents($dataBlindspotGrPath), true)
        : null;
    if (! is_array($dataBlindspotGrRaw)) {
        $dataBlindspotGrRaw = [];
    }
    $parseIsoWeekNum = static function (string $label): int {
        if (preg_match('/W?(\d+)/i', $label, $m)) {
            return (int) $m[1];
        }

        return 0;
    };
    $cellBuckets = [];
    $weeksPivotSet = [];
    foreach ($dataBlindspotGrRaw as $row) {
        if (! is_array($row)) {
            continue;
        }
        $pSite = trim((string) ($row['site'] ?? ''));
        $pPic = trim((string) ($row['perusahaan_pic'] ?? ''));
        $pWeek = trim((string) ($row['iso_week_of_date_for_join'] ?? ''));
        if ($pSite === '' || $pPic === '' || $pWeek === '') {
            continue;
        }
        $weeksPivotSet[$pWeek] = true;
        $bucketKey = $pSite . "\x1e" . $pPic . "\x1e" . $pWeek;
        if (! isset($cellBuckets[$bucketKey])) {
            $cellBuckets[$bucketKey] = ['sum' => 0.0, 'n' => 0];
        }
        $pctRaw = $row['blindspot_tbc_bc_percent'] ?? null;
        if ($pctRaw !== null && $pctRaw !== '') {
            $cellBuckets[$bucketKey]['sum'] += (float) $pctRaw;
            $cellBuckets[$bucketKey]['n']++;
        }
    }
    $weeksPivot = array_keys($weeksPivotSet);
    usort($weeksPivot, static fn (string $a, string $b): int => $parseIsoWeekNum($a) <=> $parseIsoWeekNum($b));
    $nestedPivot = [];
    foreach ($cellBuckets as $bucketKey => $bucket) {
        [$pSite, $pPic, $pWeek] = explode("\x1e", $bucketKey, 3);
        if (! isset($nestedPivot[$pSite])) {
            $nestedPivot[$pSite] = [];
        }
        if (! isset($nestedPivot[$pSite][$pPic])) {
            $nestedPivot[$pSite][$pPic] = [];
        }
        if ($bucket['n'] > 0) {
            $avg = $bucket['sum'] / $bucket['n'];
            $nestedPivot[$pSite][$pPic][$pWeek] = number_format($avg, 1, '.', '') . '%';
        } else {
            $nestedPivot[$pSite][$pPic][$pWeek] = null;
        }
    }
    ksort($nestedPivot);
    $blindspotGrPivotSiteGroups = [];
    foreach ($nestedPivot as $siteName => $pics) {
        ksort($pics);
        $companies = [];
        foreach ($pics as $picName => $weekMap) {
            $cells = [];
            foreach ($weeksPivot as $wLabel) {
                $cells[] = array_key_exists($wLabel, $weekMap) ? $weekMap[$wLabel] : null;
            }
            $companies[] = [
                'perusahaan_pic' => $picName,
                'cells' => $cells,
            ];
        }
        $blindspotGrPivotSiteGroups[] = [
            'site' => $siteName,
            'companies' => $companies,
        ];
    }
    $pivotYear = 2026;
    foreach ($blindspotGrRows as $r) {
        if (! is_array($r)) {
            continue;
        }
        $y = (int) ($r['year_of_date_for_join'] ?? 0);
        if ($y >= 2000 && $y <= 2100) {
            $pivotYear = $y;
            break;
        }
    }
    $blindspotGrPivot = [
        'year' => $pivotYear,
        'weeks' => $weeksPivot,
        'siteGroups' => $blindspotGrPivotSiteGroups,
    ];

    $parseWeek = static function ($week): ?int {
        if ($week === null || $week === '') {
            return null;
        }
        if (is_numeric($week)) {
            return (int) $week;
        }
        if (preg_match('/(\d+)/', (string) $week, $m)) {
            return (int) $m[1];
        }

        return null;
    };

    $weeks = [];
    $sites = [];
    $indicators = [];
    foreach ($jsonData as $row) {
        $w = $parseWeek($row['week'] ?? null);
        if ($w !== null) {
            $weeks[] = $w;
        }
        $site = trim((string) ($row['site'] ?? ''));
        if ($site !== '') {
            $sites[] = $site;
        }
        $indicator = trim((string) ($row['detail_indikator'] ?? ''));
        if ($indicator !== '') {
            $indicators[] = $indicator;
        }
    }
    $weeks = array_values(array_unique($weeks));
    sort($weeks);
    $sites = array_values(array_unique($sites));
    sort($sites);
    $indicators = array_values(array_unique($indicators));
    sort($indicators);

    $thematics = [];
    $indicatorCategoryMap = [];
    foreach ($programRows as $row) {
        $tematik = trim((string) ($row['tematik'] ?? ''));
        if ($tematik !== '') {
            $thematics[] = $tematik;
        }
        $detailIndikator = trim((string) ($row['detail_indikator'] ?? ''));
        $kategori = trim((string) ($row['kategori'] ?? ''));
        if ($detailIndikator !== '' && $kategori !== '' && !isset($indicatorCategoryMap[$detailIndikator])) {
            $indicatorCategoryMap[$detailIndikator] = $kategori;
        }
    }
    $thematics = array_values(array_unique($thematics));
    sort($thematics);

    $lastWeek = empty($weeks) ? 16 : (int) max($weeks);
    $lastFourWeeks = array_slice($weeks, -4);
    if (count($lastFourWeeks) < 4) {
        $start = max(1, $lastWeek - 3);
        $lastFourWeeks = range($start, $lastWeek);
    }

    $defaultIndicator = in_array('% Blindspot TBC', $indicators, true) ? '% Blindspot TBC' : ($indicators[0] ?? '% Blindspot TBC');

    $alpinePayload = [
        'rawData' => $jsonData,
        'thematicRows' => $programRows,
        'weeks' => $weeks,
        'sites' => $sites,
        'thematics' => $thematics,
        'indicators' => $indicators,
        'defaultIndicator' => $defaultIndicator,
        'defaultWeek' => $lastWeek,
        'defaultSites' => array_slice($sites, 0, 5),
        'defaultLast4Weeks' => $lastFourWeeks,
        'indicatorCategoryMap' => $indicatorCategoryMap,
        'sertifikatTeknisRows' => $sertifikatTeknisRows,
        'blindspotGrRows' => $blindspotGrRows,
        'blindspotGrPivot' => $blindspotGrPivot,
    ];
@endphp

@once
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script type="module" src="https://idashboard.beraucoal.co.id/javascripts/api/tableau.embedding.3.latest.min.js"></script>
    <style>
        .blindspot-pivot-wrap tbody tr:nth-child(even) .blindspot-pivot-pin { background-color: #f8fafc; }
        .blindspot-pivot-wrap tbody tr:nth-child(odd) .blindspot-pivot-pin { background-color: #ffffff; }
    </style>
@endonce

<section class="bg-white p-4 font-['Inter'] sm:p-6 lg:p-8"
    x-data='thematicAlignmentDashboard(@json($alpinePayload))' x-init="init()">
    <header class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3">
            <p class="text-[11px] text-slate-500">Pilih <span class="font-medium text-slate-700">indikator</span>, lalu <span class="font-medium text-slate-700">minggu</span> — daftar minggu hanya yang punya data untuk indikator itu.</p>
            <div class="grid w-full gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Indikator</label>
                    <select
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm"
                        x-model="indicator"
                        @change="onIndicatorFilterChange()"
                    >
                        <template x-for="item in indicatorChoices" :key="'hdr-ind-' + item">
                            <option :value="item" x-text="item"></option>
                        </template>
                    </select>
                </div>
                <div class="relative flex flex-col gap-1" @click.outside="dropdownWeek = false">
                    <label class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Minggu <span class="font-normal normal-case text-slate-400">(ada datanya)</span></label>
                    <button type="button" @click="dropdownWeek = !dropdownWeek" class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2 text-left text-sm shadow-sm">
                        <span><span class="font-semibold" x-text="'W' + selectedWeek"></span><span class="ml-1 text-xs text-slate-500" x-show="(indicatorWeekChoices || []).length" x-text="'· ' + (indicatorWeekChoices || []).length + ' pilihan'"></span></span>
                        <span class="text-slate-400">▾</span>
                    </button>
                    <div x-show="dropdownWeek" x-cloak class="absolute left-0 right-0 top-full z-30 mt-1 max-h-56 overflow-auto rounded-xl border border-slate-200 bg-white p-1 shadow-lg">
                        <template x-for="week in indicatorWeekChoices" :key="'w-ind-' + week">
                            <button type="button" class="block w-full rounded-lg px-3 py-2 text-left text-sm hover:bg-slate-100" @click="selectedWeek = week; dropdownWeek = false; refreshAll()"><span x-text="'W' + week"></span></button>
                        </template>
                    </div>
                </div>
                <div class="relative flex flex-col gap-1" @click.outside="dropdownSite = false">
                    <label class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Site</label>
                    <button type="button" @click="dropdownSite = !dropdownSite" class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm">
                        <span class="font-semibold" x-text="selectedSites.length ? selectedSites.length + ' dipilih' : 'Semua site'"></span>
                        <span class="text-slate-400">▾</span>
                    </button>
                    <div x-show="dropdownSite" x-cloak class="absolute left-0 right-0 top-full z-30 mt-1 max-h-64 overflow-auto rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
                        <template x-for="site in siteChoices" :key="'s-' + site">
                            <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-slate-100">
                                <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600" :checked="selectedSites.includes(site)" @change="toggleSite(site)">
                                <span x-text="site"></span>
                            </label>
                        </template>
                    </div>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Tematik</label>
                    <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm" x-model="selectedThematic" @change="refreshAll()">
                        <option value="All">Semua tematik</option>
                        <template x-for="item in thematicChoices" :key="'th-' + item">
                            <option :value="item" x-text="item"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>
    </header>

    <div class="mb-5 grid gap-4 md:grid-cols-2">
        <article class="rounded-2xl border border-indigo-100 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Scoring All Site YTD</p>
            <p class="mt-3 text-3xl font-bold text-slate-900" x-text="indicatorKpiScoring"></p>
            <div class="mt-3 h-2.5 rounded-full bg-indigo-100" x-show="indicatorKpiScoringBarPct !== null">
                <div class="h-2.5 rounded-full bg-indigo-500" :style="'width: ' + indicatorKpiScoringBarPct + '%'"></div>
            </div>
            <p class="mt-1 text-xs text-slate-500">Dari program tematik (baris minggu terbaru untuk indikator ini)</p>
        </article>
        <article class="rounded-2xl border border-emerald-100 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Grade YTD</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">
                <span
                    class="inline-flex rounded-full px-3 py-1 text-2xl font-bold leading-none"
                    :class="gradeClass(indicatorKpiGrade)"
                    x-text="indicatorKpiGrade"
                ></span>
            </p>
            <p class="mt-1 text-xs text-slate-500">Grade YTD dari program tematik</p>
        </article>
    </div>

    <div class="grid gap-4 xl:grid-cols-2 mb-5 ">
        <article class="rounded-2xl border border-slate-200  p-4 shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-200 pb-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Heatmap Site vs Last 4 Weeks</h3>
                    <p class="mt-1 text-[11px] text-slate-600">Menampilkan seluruh site untuk indikator terpilih.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-[10px] text-slate-700">
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-white/80 px-2 py-1"><span class="h-2.5 w-2.5 bg-[#4e9f63]"></span><span x-text="isLowerBetter() ? '&lt; 50%' : '&gt; 80%'"></span></span>
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-white/80 px-2 py-1"><span class="h-2.5 w-2.5 bg-[#e4cc4a]"></span>50-79%</span>
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-white/80 px-2 py-1"><span class="h-2.5 w-2.5 bg-[#d84f4b]"></span><span x-text="isLowerBetter() ? '&gt; 80%' : '&lt; 50%'"></span></span>
                </div>
            </div>
            <div class="mt-3 overflow-x-auto rounded-lg border border-slate-300 ">
                <table class="w-full min-w-[620px] border-separate border-spacing-0 text-sm">
                    <thead class="text-[13px] font-semibold text-slate-700">
                        <tr>
                            <th class="sticky left-0 z-10  px-3 py-2 text-left"></th>
                            <template x-for="w in last4Weeks" :key="'hmw-' + w">
                                <th class="px-4 py-2 text-center" x-text="'W' + w"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="!heatmapRows.length">
                            <tr>
                                <td class="px-3 py-6 text-center text-xs text-slate-500" :colspan="last4Weeks.length + 1">Belum ada data heatmap untuk indikator ini.</td>
                            </tr>
                        </template>
                        <template x-for="row in heatmapRows" :key="'hm-' + row.site">
                            <tr>
                                <td class="sticky left-0 z-[1] whitespace-nowrap px-3 py-2 text-[14px] font-semibold text-slate-700" x-text="row.site"></td>
                                <template x-for="cell in row.values" :key="'hmc-' + row.site + '-' + cell.week">
                                    <td class="h-10 min-w-20 px-3 py-2 text-center text-[11px] font-semibold tabular-nums" :class="heatClass(cell.value)" x-text="cell.display"></td>
                                </template>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </article>
        <article class="flex h-full flex-col rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div>
                <h3 class="text-sm font-semibold text-slate-700">Tren Pemenuhan 4 Minggu Terakhir</h3>
                <p class="mt-1 text-[11px] text-slate-500" x-text="'Indikator (dari filter atas): ' + indicator"></p>
            </div>
            <div
                class="mt-4 min-h-[420px] flex-1"
                :style="`height:${Math.max(420, (heatmapRows.length * 40) + 110)}px`"
            >
                <canvas id="thematicTrendChart" x-ref="trendChart"></canvas>
            </div>
        </article>
    </div>

    <div class="grid gap-4 mb-5 ">
        <article class="min-w-0 max-w-full rounded-2xl border border-slate-200 p-4 shadow-sm sm:p-5">
            <h3 class="text-sm font-semibold text-slate-700">Detail Data</h3>

            <div x-show="detailMode === 'blindspot_gr'" x-cloak class="mt-4 min-w-0 max-w-full">
                <p class="mb-2 text-xs text-slate-500 sm:mb-3">Tabel pivot: persentase blindspot TBC dari BC per site, perusahaan PIC, dan minggu Date for Join (mengikuti filter site).</p>
                <p class="mb-2 flex items-center gap-1.5 text-[11px] text-slate-500 sm:hidden">
                    <span class="inline-block shrink-0 rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 font-medium text-slate-600">← →</span>
                    <span>Geser horizontal untuk melihat kolom minggu.</span>
                </p>
                <div
                    class="blindspot-pivot-wrap -mx-1 max-w-full overflow-x-auto overscroll-x-contain rounded-xl border border-slate-200 bg-white shadow-sm [scrollbar-gutter:stable] sm:mx-0"
                    style="-webkit-overflow-scrolling: touch"
                >
                    <table class="min-w-max w-full max-w-none border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr>
                                <th
                                    colspan="2"
                                    rowspan="2"
                                    class="sticky left-0 z-30 min-w-[13.5rem] border border-slate-200 bg-slate-50 px-2 py-2 sm:min-w-[19rem]"
                                ></th>
                                <th
                                    class="border border-slate-200 bg-slate-50 px-2 py-2 text-center text-[10px] font-semibold tracking-wide text-slate-700 sm:px-3 sm:text-xs"
                                    :colspan="Math.max(1, (blindspotGrPivot.weeks || []).length)"
                                >Date for Join</th>
                            </tr>
                            <tr>
                                <th
                                    class="border border-slate-200 bg-slate-50 px-2 py-2 text-center text-[10px] font-semibold text-slate-600 sm:px-3 sm:text-xs"
                                    :colspan="Math.max(1, (blindspotGrPivot.weeks || []).length)"
                                    x-text="blindspotGrPivot.year"
                                ></th>
                            </tr>
                            <tr>
                                <th
                                    class="sticky left-0 z-20 w-20 min-w-[5rem] border border-slate-200 border-r-slate-300 bg-slate-50 px-2 py-2 text-left text-[10px] font-semibold uppercase tracking-wide text-slate-600 shadow-[4px_0_8px_-4px_rgba(15,23,42,0.12)] sm:w-28 sm:min-w-[7rem] sm:px-3 sm:text-xs"
                                >site</th>
                                <th
                                    class="sticky left-20 z-20 min-w-[8.5rem] max-w-[11rem] border border-slate-200 border-r-slate-300 bg-slate-50 px-2 py-2 text-left text-[10px] font-semibold text-slate-600 shadow-[4px_0_8px_-4px_rgba(15,23,42,0.12)] sm:left-28 sm:min-w-[12rem] sm:max-w-[14rem] sm:px-3 sm:text-xs"
                                >perusahaan pic</th>
                                <template x-for="w in (blindspotGrPivot.weeks || [])" :key="'bh-' + w">
                                    <th
                                        class="min-w-[2.75rem] whitespace-nowrap border border-slate-200 bg-slate-50 px-1.5 py-2 text-center text-[10px] font-semibold text-slate-600 sm:min-w-[3.25rem] sm:px-2 sm:text-xs"
                                        x-text="w"
                                    ></th>
                                </template>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="grp in blindspotGrPivotSiteGroups" :key="'bgp-' + grp.site">
                                <template x-for="(co, ci) in grp.companies" :key="'bgp-' + grp.site + '-' + ci">
                                    <tr class="border-b border-slate-100 bg-white even:bg-slate-50">
                                        <template x-if="ci === 0">
                                            <td
                                                class="blindspot-pivot-pin sticky left-0 z-10 w-20 min-w-[5rem] border border-slate-200 border-r-slate-300 px-2 py-2 align-top text-left text-xs font-semibold text-slate-900 shadow-[4px_0_8px_-4px_rgba(15,23,42,0.1)] sm:w-28 sm:min-w-[7rem] sm:px-3 sm:text-sm"
                                                :rowspan="grp.companies.length"
                                                x-text="grp.site"
                                            ></td>
                                        </template>
                                        <td
                                            class="blindspot-pivot-pin sticky left-20 z-10 min-w-[8.5rem] max-w-[11rem] border border-slate-200 border-r-slate-300 px-2 py-2 text-left text-[11px] leading-snug text-slate-800 shadow-[4px_0_8px_-4px_rgba(15,23,42,0.1)] sm:left-28 sm:min-w-[12rem] sm:max-w-[14rem] sm:px-3 sm:text-sm"
                                            x-text="co.perusahaan_pic"
                                        ></td>
                                        <template x-for="(cell, wi) in co.cells" :key="'bc-' + grp.site + '-' + ci + '-' + wi">
                                            <td
                                                class="min-w-[2.75rem] border border-slate-200 px-1.5 py-2 text-center text-[11px] tabular-nums text-slate-700 sm:min-w-[3.25rem] sm:px-2 sm:text-sm"
                                                x-text="cell != null && cell !== '' ? cell : ''"
                                            ></td>
                                        </template>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
                <p class="mt-2 text-xs text-slate-500" x-show="(blindspotGrPivotSiteGroups || []).length === 0">Tidak ada baris untuk filter site yang dipilih.</p>
            </div>

            <div x-show="detailMode === 'aggregator_behealth'" x-cloak class="mt-4 min-w-0 max-w-full">
                <p class="mb-3 text-xs text-slate-500">Dashboard Tableau: Excel Aggregator Fatigue Management (embed).</p>
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-sm">
                    <div class="relative w-full overflow-auto" style="min-height: min(85vh, 1209px);">
                        <tableau-viz
                            id="tableau-viz-aggregator-behealth"
                            src="https://idashboard.beraucoal.co.id/t/hsedivision/views/ExcelAggregatorFatigueManagement/AggregatorFatigueTest"
                            width="1654"
                            height="1209"
                            hide-tabs
                            toolbar="bottom"
                            class="mx-auto block max-w-full"
                        ></tableau-viz>
                    </div>
                </div>
            </div>

            <div x-show="detailMode === 'sertifikasi_pengawas_teknis'" x-cloak class="mt-4 space-y-6">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl border border-slate-200 bg-gradient-to-br from-indigo-50/50 to-white p-4 shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Total baris</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900" x-text="sertifikatTeknisTotals.total"></p>
                        <p class="mt-0.5 text-[10px] text-slate-500">Work permit / entri</p>
                    </div>
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 p-4 shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-700">Cukup</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-800" x-text="sertifikatTeknisTotals.cukup"></p>
                        <p class="mt-0.5 text-[10px] text-emerald-700/80">Memenuhi SID</p>
                    </div>
                    <div class="rounded-xl border border-rose-100 bg-rose-50/60 p-4 shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-rose-700">Tidak cukup</p>
                        <p class="mt-1 text-2xl font-bold text-rose-800" x-text="sertifikatTeknisTotals.tidakCukup"></p>
                        <p class="mt-0.5 text-[10px] text-rose-700/80">Perlu tindak lanjut</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-100/80 p-4 shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-600">Site ter-cover</p>
                        <p class="mt-1 text-2xl font-bold text-slate-800" x-text="sertifikatTeknisTotals.siteCount"></p>
                        <p class="mt-0.5 text-[10px] text-slate-600">Setelah filter site</p>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-5">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:col-span-2">
                        <p class="text-xs font-semibold text-slate-700">Distribusi status Jumlah SID</p>
                        <p class="mt-0.5 text-[11px] text-slate-500">Cukup vs tidak cukup (filter site aktif)</p>
                        <div class="relative mx-auto mt-2 h-56 max-w-[280px]">
                            <canvas x-ref="sertifikatTeknisDonut"></canvas>
                        </div>
                        <div class="mt-3 flex flex-wrap justify-center gap-3 text-[10px] text-slate-600">
                            <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Cukup</span>
                            <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-rose-500"></span>Tidak cukup</span>
                        </div>
                    </div>
                    <div class="space-y-3 lg:col-span-3">
                        <p class="text-xs font-semibold text-slate-700">Proporsi per site</p>
                        <div class="max-h-[420px] space-y-3 overflow-y-auto pr-1">
                            <template x-for="block in sertifikatTeknisBySite" :key="'st-' + block.site">
                                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-3 shadow-sm">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-sm font-bold text-slate-800" x-text="block.site"></span>
                                        <span class="text-[11px] font-medium text-slate-500"><span x-text="block.total"></span> baris</span>
                                    </div>
                                    <div class="mt-2 flex h-2.5 overflow-hidden rounded-full bg-slate-200">
                                        <div class="bg-emerald-500 transition-all" :style="'width:' + sertifikatTeknisBarPct(block.cukup, block) + '%'"></div>
                                        <div class="bg-rose-500 transition-all" :style="'width:' + sertifikatTeknisBarPct(block.tidakCukup, block) + '%'"></div>
                                    </div>
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        <template x-for="(e, ei) in block.entries.slice(0, 6)" :key="'st-e-' + block.site + '-' + ei">
                                            <span
                                                class="inline-flex max-w-[220px] truncate rounded-lg border px-2 py-0.5 text-[10px] font-medium"
                                                :class="normalizeText(e.jumlah_sid).includes('tidak cukup') ? 'border-rose-200 bg-rose-50 text-rose-900' : 'border-emerald-200 bg-emerald-50 text-emerald-900'"
                                                :title="e.work_permit"
                                                x-text="e.nama_perusahaan"
                                            ></span>
                                        </template>
                                        <span x-show="block.entries.length > 6" class="text-[10px] text-slate-500" x-text="'+' + (block.entries.length - 6) + ' lainnya'"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-slate-50/30">
                    <p class="border-b border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700">Detail data lengkap</p>
                    <table class="w-full min-w-[900px] text-sm">
                        <thead class="border-b border-slate-200 bg-white text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-3 py-2 text-left">Site</th>
                                <th class="px-3 py-2 text-left">Nama Perusahaan</th>
                                <th class="px-3 py-2 text-left">Main Kon</th>
                                <th class="px-3 py-2 text-left">Departement</th>
                                <th class="px-3 py-2 text-left">Work Permit</th>
                                <th class="px-3 py-2 text-left">Jumlah SID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="row in detailedRows" :key="'d-sertif-' + (row.site ?? '') + '-' + (row.departement ?? '') + '-' + (row.work_permit ?? '')">
                                <tr class="border-b border-slate-100 bg-white">
                                    <td class="px-3 py-2" x-text="row.site"></td>
                                    <td class="px-3 py-2" x-text="row.nama_perusahaan"></td>
                                    <td class="px-3 py-2" x-text="row.main_kon"></td>
                                    <td class="px-3 py-2" x-text="row.departement"></td>
                                    <td class="px-3 py-2 text-xs leading-relaxed" x-text="row.work_permit"></td>
                                    <td class="px-3 py-2">
                                        <span class="rounded-full px-2 py-1 text-xs font-semibold" :class="sidClass(row.jumlah_sid)" x-text="row.jumlah_sid"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div x-show="detailMode === 'default'" class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="border-b border-slate-200 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-3 py-2 text-left">Week</th><th class="px-3 py-2 text-left">Site</th><th class="px-3 py-2 text-left">Thematic</th><th class="px-3 py-2 text-left">Indikator</th><th class="px-3 py-2 text-left">Nilai</th><th class="px-3 py-2 text-left">Grade</th><th class="px-3 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in detailedRows" :key="'d-default-' + (row.site ?? '') + '-' + (row.week ?? '') + '-' + (row.indikator ?? '')">
                            <tr class="border-b border-slate-100">
                                <td class="px-3 py-2" x-text="'W' + row.week"></td>
                                <td class="px-3 py-2" x-text="row.site"></td>
                                <td class="px-3 py-2" x-text="row.thematic"></td>
                                <td class="px-3 py-2" x-text="row.indikator"></td>
                                <td class="px-3 py-2 font-medium" x-text="row.nilai"></td>
                                <td class="px-3 py-2"><span class="rounded-full px-2 py-1 text-xs font-semibold" :class="gradeClass(row.grade)" x-text="row.grade"></span></td>
                                <td class="px-3 py-2"><span class="rounded-full px-2 py-1 text-xs font-semibold" :class="row.status === 'On Track' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'" x-text="row.status"></span></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </article>
       
    </div>
</section>

<script>
    function thematicAlignmentDashboard(config) {
        let chartInstance = null;
        let sertifikatTeknisChartInstance = null;

        return {
            dropdownWeek: false,
            dropdownSite: false,
            weekChoices: config.weeks?.length ? config.weeks : Array.from({ length: 16 }, (_, i) => i + 1),
            indicatorWeekChoices: [],
            siteChoices: config.sites ?? [],
            thematicChoices: config.thematics ?? [],
            indicatorChoices: config.indicators ?? [],
            indicatorCategoryMap: config.indicatorCategoryMap ?? {},
            selectedWeek: config.defaultWeek ?? 16,
            selectedSites: config.defaultSites ?? [],
            selectedThematic: 'All',
            indicator: config.defaultIndicator ?? '% Blindspot TBC',
            rawData: config.rawData ?? [],
            thematicRows: config.thematicRows ?? [],
            sertifikatTeknisRows: config.sertifikatTeknisRows ?? [],
            blindspotGrRows: config.blindspotGrRows ?? [],
            blindspotGrPivot: config.blindspotGrPivot ?? { year: 2026, weeks: [], siteGroups: [] },
            blindspotGrPivotSiteGroups: [],
            blindspotGrTotals: { n0: 0, n1: 0, nNull: 0, total: 0 },
            blindspotGrBySite: [],
            sertifikatTeknisTotals: { cukup: 0, tidakCukup: 0, total: 0, siteCount: 0 },
            sertifikatTeknisBySite: [],
            last4Weeks: config.defaultLast4Weeks ?? [13, 14, 15, 16],
            heatmapRows: [],
            detailedRows: [],
            detailMode: 'default',
            leaderboard: [],
            colors: ['#6366F1', '#0EA5E9', '#10B981', '#F59E0B', '#EF4444'],
            indicatorKpiScoring: '—',
            indicatorKpiGrade: '—',
            indicatorKpiScoringBarPct: null,
            init() { this.refreshAll(); },
            rebuildIndicatorWeekChoices() {
                const set = new Set();
                (this.rawData || []).forEach((row) => {
                    if (!this.indicatorMatches(row.detail_indikator ?? '')) return;
                    const w = this.parseWeek(row.week);
                    if (w !== null) set.add(w);
                });
                let arr = [...set].sort((a, b) => a - b);
                if (!arr.length) {
                    arr = [...(this.weekChoices?.length ? this.weekChoices : Array.from({ length: 16 }, (_, i) => i + 1))];
                }
                this.indicatorWeekChoices = arr;
                const sw = Number(this.selectedWeek);
                if (!arr.includes(sw)) {
                    this.selectedWeek = arr[arr.length - 1] ?? arr[0] ?? this.selectedWeek;
                }
            },
            onIndicatorFilterChange() {
                this.refreshAll();
            },
            updateIndicatorKpiFromProgram() {
                this.indicatorKpiScoring = '—';
                this.indicatorKpiGrade = '—';
                this.indicatorKpiScoringBarPct = null;
                const rows = (this.thematicRows || []).filter((row) => this.indicatorMatches(row.detail_indikator ?? ''));
                if (!rows.length) {
                    return;
                }
                let maxWeek = -Infinity;
                rows.forEach((row) => {
                    const w = Number(row.week);
                    if (Number.isFinite(w)) {
                        maxWeek = Math.max(maxWeek, w);
                    }
                });
                if (!Number.isFinite(maxWeek) || maxWeek === -Infinity) {
                    return;
                }
                const latest = rows.filter((row) => Number(row.week) === maxWeek);
                const pick = latest[0];
                if (!pick) {
                    return;
                }
                const scoringRaw = pick.scoring_all_site_ytd;
                const gradeRaw = pick.grade_ytd;
                if (scoringRaw !== null && scoringRaw !== undefined && String(scoringRaw).trim() !== '') {
                    this.indicatorKpiScoring = String(scoringRaw).trim();
                }
                if (gradeRaw !== null && gradeRaw !== undefined && String(gradeRaw).trim() !== '') {
                    this.indicatorKpiGrade = String(gradeRaw).trim();
                }
                const s = this.indicatorKpiScoring;
                if (s !== '—' && s.includes('%')) {
                    const n = this.parseNumber(s);
                    if (n !== null && Number.isFinite(n)) {
                        this.indicatorKpiScoringBarPct = Math.max(0, Math.min(100, n));
                    }
                }
            },
            parseWeek(value) {
                const hit = String(value ?? '').match(/(\d+)/);
                return hit ? Number(hit[1]) : null;
            },
            parseNumber(value) {
                const hit = String(value ?? '').match(/(\d+(?:\.\d+)?)/);
                return hit ? Number(hit[1]) : null;
            },
            normalizeText(value) {
                return String(value ?? '').trim().toLowerCase();
            },
            indicatorMatches(value) {
                return this.normalizeText(value) === this.normalizeText(this.indicator);
            },
            isAggregatorBehealthIndicator() {
                const i = this.normalizeText(this.indicator);
                return i === 'pengisian aggregator behealth' || i === '% pengisian aggregator behealth';
            },
            toggleSite(site) {
                if (this.selectedSites.includes(site)) this.selectedSites = this.selectedSites.filter((s) => s !== site);
                else this.selectedSites.push(site);
                this.refreshAll();
            },
            gradeFromValue(val) {
                if (val >= 80) return 'L4';
                if (val >= 50) return 'L3';
                return 'L2';
            },
            gradeClass(grade) {
                const g = String(grade ?? '').trim();
                if (g === '—' || g === '-' || g === 'N/A' || g === 'n/a') {
                    return 'bg-slate-100 text-slate-600';
                }
                if (g === 'L4') return 'bg-emerald-100 text-emerald-700';
                if (g === 'L3' || g === 'L3.5') return 'bg-amber-100 text-amber-700';
                if (!g) return 'bg-slate-100 text-slate-600';
                return 'bg-rose-100 text-rose-700';
            },
            sidClass(jumlahSid) {
                const value = this.normalizeText(jumlahSid);
                if (value.includes('tidak cukup')) return 'bg-rose-100 text-rose-700';
                return 'bg-emerald-100 text-emerald-700';
            },
            sertifikatTeknisBarPct(part, block) {
                const t = block.cukup + block.tidakCukup;
                return t ? Math.round((100 * part) / t) : 0;
            },
            buildSertifikatTeknisChart() {
                if (sertifikatTeknisChartInstance) {
                    try {
                        sertifikatTeknisChartInstance.destroy();
                    } catch (e) {
                        /* ignore */
                    }
                    sertifikatTeknisChartInstance = null;
                }
                if (!this.indicatorMatches('% Sertifikasi Pengawas Teknis')) return;
                const canvas = this.$refs.sertifikatTeknisDonut;
                if (!canvas || !window.Chart) return;
                const ctx = canvas.getContext('2d');
                if (!ctx) return;
                const t = this.sertifikatTeknisTotals;
                if (!t.total) return;
                sertifikatTeknisChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Cukup', 'Tidak cukup'],
                        datasets: [{
                            data: [t.cukup, t.tidakCukup],
                            backgroundColor: ['#22c55e', '#f43f5e'],
                            borderWidth: 0,
                            hoverOffset: 6,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '64%',
                        plugins: {
                            legend: { display: false },
                        },
                    },
                });
            },
            isLowerBetter() {
                const selected = this.normalizeText(this.indicator);
                const matchedKey = Object.keys(this.indicatorCategoryMap ?? {})
                    .find((key) => this.normalizeText(key) === selected);
                const category = this.normalizeText(matchedKey ? this.indicatorCategoryMap[matchedKey] : '');
                return category.includes('kecil');
            },
            heatClass(value) {
                const v = this.parseNumber(value);
                if (v === null) return 'bg-[#cfd8dc] text-slate-600 border border-white/70';
                if (this.isLowerBetter()) {
                    if (v > 80) return 'bg-[#d84f4b] text-white border border-white/70';
                    if (v >= 50) return 'bg-[#e4cc4a] text-slate-800 border border-white/70';
                    return 'bg-[#4e9f63] text-white border border-white/70';
                }
                if (v > 80) return 'bg-[#4e9f63] text-white border border-white/70';
                if (v >= 50) return 'bg-[#e4cc4a] text-slate-800 border border-white/70';
                return 'bg-[#d84f4b] text-white border border-white/70';
            },
            getSiteFilteredRows() {
                const sites = this.selectedSites.length ? this.selectedSites : this.siteChoices;
                return this.rawData.filter((row) => {
                    const week = this.parseWeek(row.week);
                    if (week === null || !this.last4Weeks.includes(week)) return false;
                    if (!sites.includes(row.site)) return false;
                    if (this.indicator && !this.indicatorMatches(row.detail_indikator)) return false;
                    return true;
                });
            },
            buildHeatmap() {
                // Heatmap selalu tampilkan seluruh site untuk indikator aktif.
                const rows = this.rawData.filter((row) => {
                    const week = this.parseWeek(row.week);
                    if (week === null || !this.last4Weeks.includes(week)) return false;
                    if (this.indicator && !this.indicatorMatches(row.detail_indikator)) return false;
                    return true;
                });
                const bySite = {};
                rows.forEach((row) => {
                    bySite[row.site] = bySite[row.site] || {};
                    bySite[row.site][this.parseWeek(row.week)] = row.data;
                });
                const sites = [...this.siteChoices].sort((a, b) => a.localeCompare(b));
                this.heatmapRows = sites.map((site) => ({
                    site,
                    values: this.last4Weeks.map((week) => ({
                        week,
                        value: bySite[site]?.[week] ?? null,
                        display: bySite[site]?.[week] ?? '-',
                    })),
                }));
            },
            buildDetailsAndLeaderboard() {
                if (this.indicatorMatches('% Blindspot GR')) {
                    this.detailMode = 'blindspot_gr';
                    const siteFilters = this.selectedSites.length ? this.selectedSites : this.siteChoices;
                    const filtered = this.blindspotGrRows.filter((row) => siteFilters.includes(String(row.site ?? '').trim()));
                    let n0 = 0;
                    let n1 = 0;
                    let nNull = 0;
                    const bySite = {};
                    filtered.forEach((row) => {
                        const site = String(row.site ?? '').trim() || '-';
                        if (!bySite[site]) {
                            bySite[site] = { site, n0: 0, n1: 0, nNull: 0, total: 0, entries: [] };
                        }
                        const block = bySite[site];
                        const v = row.blindspot_tbc_dari_bc;
                        if (v === 0 || v === '0') {
                            block.n0++;
                            n0++;
                        } else if (v === 1 || v === '1') {
                            block.n1++;
                            n1++;
                        } else {
                            block.nNull++;
                            nNull++;
                        }
                        block.total++;
                        block.entries.push({
                            perusahaan_pic: row.perusahaan_pic ?? '-',
                            blindspot_tbc_dari_bc: v,
                            year_of_date_for_join: row.year_of_date_for_join ?? null,
                        });
                    });
                    this.blindspotGrTotals = { n0, n1, nNull, total: filtered.length };
                    this.blindspotGrBySite = Object.values(bySite).sort((a, b) => a.site.localeCompare(b.site));
                    const pivotGroups = this.blindspotGrPivot.siteGroups || [];
                    this.blindspotGrPivotSiteGroups = pivotGroups.filter((g) => siteFilters.includes(String(g.site ?? '').trim()));
                    this.detailedRows = [];
                    this.leaderboard = this.blindspotGrBySite
                        .map((b) => ({
                            site: b.site,
                            score: b.total ? Math.round(100 * (1 - b.n1 / b.total)) : 0,
                        }))
                        .sort((a, b) => b.score - a.score)
                        .slice(0, 3);
                    return;
                }

                if (this.isAggregatorBehealthIndicator()) {
                    this.detailMode = 'aggregator_behealth';
                    this.detailedRows = [];
                    const rankMap = {};
                    this.getSiteFilteredRows().forEach((row) => {
                        const score = this.parseNumber(row.data) ?? 0;
                        rankMap[row.site] = rankMap[row.site] || { site: row.site, score: 0, count: 0 };
                        rankMap[row.site].score += score;
                        rankMap[row.site].count += 1;
                    });
                    this.leaderboard = Object.values(rankMap)
                        .map((it) => ({ site: it.site, score: Math.round(it.score / Math.max(it.count, 1)) }))
                        .sort((a, b) => b.score - a.score)
                        .slice(0, 3);
                    return;
                }

                if (this.indicatorMatches('% Sertifikasi Pengawas Teknis')) {
                    this.detailMode = 'sertifikasi_pengawas_teknis';
                    const siteFilters = this.selectedSites.length ? this.selectedSites : this.siteChoices;
                    const filtered = this.sertifikatTeknisRows.filter((row) => siteFilters.includes(String(row.site ?? '').trim()));
                    let cukup = 0;
                    let tidakCukup = 0;
                    const bySite = {};
                    filtered.forEach((row) => {
                        const site = String(row.site ?? '').trim() || '-';
                        const isTidak = this.normalizeText(row.jumlah_sid ?? '').includes('tidak cukup');
                        if (!bySite[site]) {
                            bySite[site] = { site, cukup: 0, tidakCukup: 0, total: 0, entries: [] };
                        }
                        const block = bySite[site];
                        if (isTidak) {
                            block.tidakCukup++;
                            tidakCukup++;
                        } else {
                            block.cukup++;
                            cukup++;
                        }
                        block.total++;
                        block.entries.push({
                            nama_perusahaan: row.nama_perusahaan ?? '-',
                            work_permit: row.work_permit ?? '-',
                            jumlah_sid: row.jumlah_sid ?? '-',
                        });
                    });
                    this.sertifikatTeknisTotals = {
                        cukup,
                        tidakCukup,
                        total: filtered.length,
                        siteCount: Object.keys(bySite).length,
                    };
                    this.sertifikatTeknisBySite = Object.values(bySite).sort((a, b) => a.site.localeCompare(b.site));
                    this.detailedRows = filtered.map((row) => ({
                        nama_perusahaan: row.nama_perusahaan ?? '-',
                        site: row.site ?? '-',
                        main_kon: row.main_kon ?? '-',
                        departement: row.departement ?? '-',
                        work_permit: row.work_permit ?? '-',
                        jumlah_sid: row.jumlah_sid ?? '-',
                    }));

                    const scoreMap = {};
                    this.detailedRows.forEach((row) => {
                        const site = row.site ?? '-';
                        scoreMap[site] = scoreMap[site] || { site, score: 0, count: 0 };
                        scoreMap[site].score += this.normalizeText(row.jumlah_sid).includes('tidak cukup') ? 0 : 100;
                        scoreMap[site].count += 1;
                    });
                    this.leaderboard = Object.values(scoreMap)
                        .map((row) => ({
                            site: row.site,
                            score: Math.round(row.score / Math.max(row.count, 1)),
                        }))
                        .sort((a, b) => b.score - a.score)
                        .slice(0, 3);
                    return;
                }

                this.detailMode = 'default';
                const thematicMap = {};
                this.thematicRows.forEach((row) => {
                    if (row.detail_indikator && !thematicMap[row.detail_indikator]) thematicMap[row.detail_indikator] = row.tematik ?? '-';
                });

                const details = this.getSiteFilteredRows().map((row) => {
                    const nilai = this.parseNumber(row.data) ?? 0;
                    const grade = this.gradeFromValue(nilai);
                    return {
                        week: this.parseWeek(row.week) ?? '-',
                        site: row.site ?? '-',
                        thematic: this.selectedThematic === 'All' ? (thematicMap[row.detail_indikator] ?? '-') : this.selectedThematic,
                        indikator: row.detail_indikator ?? '-',
                        nilai: row.data ?? '-',
                        grade,
                        status: nilai >= 50 ? 'On Track' : 'Alert',
                    };
                });

                this.detailedRows = this.selectedThematic === 'All'
                    ? details
                    : details.filter((row) => row.thematic === this.selectedThematic);

                const rankMap = {};
                this.detailedRows.forEach((row) => {
                    const score = this.parseNumber(row.nilai) ?? 0;
                    rankMap[row.site] = rankMap[row.site] || { site: row.site, score: 0, count: 0 };
                    rankMap[row.site].score += score;
                    rankMap[row.site].count += 1;
                });
                this.leaderboard = Object.values(rankMap)
                    .map((it) => ({ site: it.site, score: Math.round(it.score / Math.max(it.count, 1)) }))
                    .sort((a, b) => b.score - a.score)
                    .slice(0, 3);
            },
            buildTrendChart() {
                let chartWeeks = [...this.last4Weeks];
                // Strict: indikator terpilih + last 4 week aktif (tanpa fallback minggu lain).
                const rows = this.rawData.filter((row) => {
                    const week = this.parseWeek(row.week);
                    if (week === null || !chartWeeks.includes(week)) return false;
                    if (this.indicator && !this.indicatorMatches(row.detail_indikator)) return false;
                    return true;
                });

                const siteAgg = {};
                rows.forEach((row) => {
                    const site = row.site ?? 'Unknown';
                    const week = this.parseWeek(row.week);
                    const value = this.parseNumber(row.data);
                    if (week === null || value === null) return;
                    siteAgg[site] = siteAgg[site] || {};
                    siteAgg[site][week] = value;
                });

                const allSites = Object.keys(siteAgg)
                    .map((site) => {
                        const vals = chartWeeks.map((w) => siteAgg[site][w]).filter((v) => typeof v === 'number');
                        const avg = vals.length ? vals.reduce((a, b) => a + b, 0) / vals.length : 0;
                        return { site, avg };
                    })
                    .sort((a, b) => b.avg - a.avg);

                const lineColor = (index, total) => {
                    const hue = Math.round((index * (360 / Math.max(total, 1))) % 360);
                    return `hsl(${hue} 70% 45%)`;
                };

                const datasets = allSites.map((item, index) => ({
                    label: item.site,
                    data: chartWeeks.map((week) => siteAgg[item.site][week] ?? null),
                    borderColor: lineColor(index, allSites.length),
                    backgroundColor: lineColor(index, allSites.length),
                    tension: 0.35,
                    borderWidth: 2.2,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                    pointHitRadius: 10,
                    spanGaps: true,
                }));

                const canvas = this.$refs.trendChart;
                if (!canvas || !window.Chart) return;

                const ctx = canvas.getContext('2d');
                if (!ctx) return;

                const labels = chartWeeks.map((w) => `W${w}`);
                const safeDatasets = datasets.length
                    ? datasets
                    : [{
                        label: `${this.indicator || 'No Data'} (no data)`,
                        data: labels.map(() => null),
                        borderColor: '#94A3B8',
                        backgroundColor: '#94A3B8',
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 3,
                    }];

                if (chartInstance) {
                    try {
                        chartInstance.destroy();
                        chartInstance = null;
                    } catch (error) {
                        chartInstance = null;
                    }
                }

                try {
                    chartInstance = new Chart(ctx, {
                        type: 'line',
                        data: { labels, datasets: safeDatasets },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'nearest', intersect: false },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, pointStyle: 'circle' },
                                },
                                title: { display: true, text: this.indicator },
                            },
                            scales: {
                                x: {
                                    grid: { display: false, drawBorder: false },
                                    border: { display: false },
                                },
                                y: {
                                    min: 0,
                                    max: 100,
                                    ticks: { callback: (v) => `${v}%` },
                                    grid: { display: false, drawBorder: false },
                                    border: { display: false },
                                },
                            },
                        },
                    });
                } catch (error) {
                    throw error;
                }
            },
            refreshAll() {
                this.rebuildIndicatorWeekChoices();
                this.updateIndicatorKpiFromProgram();
                const week = Number(this.selectedWeek);
                this.last4Weeks = [week - 3, week - 2, week - 1, week].filter((v) => v > 0);
                this.buildHeatmap();
                this.buildDetailsAndLeaderboard();
                this.buildTrendChart();
                queueMicrotask(() => this.buildSertifikatTeknisChart());
            },
        };
    }
</script>
