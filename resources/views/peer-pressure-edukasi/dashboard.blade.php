<!DOCTYPE html>
<html class="light" lang="en">
   <head>
      <meta charset="utf-8"/>
      <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
      <title>BMO2 Safety - Peer Pressure Program Evaluation</title>
      <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&amp;family=Inter:wght@300;400;500;600&amp;display=swap" rel="stylesheet"/>
      <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
      <script id="tailwind-config">
         tailwind.config = {
           darkMode: "class",
           theme: {
             extend: {
               colors: {
                 "outline": "#747779",
                 "primary-container": "#859aff",
                 "on-error-container": "#510017",
                 "on-error": "#ffefef",
                 "secondary-fixed": "#e4c6ff",
                 "on-tertiary-fixed-variant": "#00377b",
                 "primary-fixed-dim": "#748cf9",
                 "surface-container-low": "#eef1f3",
                 "secondary-dim": "#653b91",
                 "secondary-fixed-dim": "#dab4ff",
                 "on-primary-container": "#001867",
                 "error": "#b41340",
                 "error-dim": "#a70138",
                 "on-tertiary-fixed": "#00163b",
                 "secondary": "#72479e",
                 "error-container": "#f74b6d",
                 "surface-container-high": "#dfe3e6",
                 "on-secondary-fixed": "#481c73",
                 "surface-tint": "#3952bc",
                 "on-secondary-container": "#5d3288",
                 "surface-container": "#e5e9eb",
                 "inverse-primary": "#7991ff",
                 "on-background": "#2c2f31",
                 "surface": "#f5f7f9",
                 "surface-container-lowest": "#ffffff",
                 "on-primary-fixed-variant": "#00207e",
                 "inverse-on-surface": "#9a9d9f",
                 "on-secondary": "#fbefff",
                 "tertiary-fixed": "#8ab0ff",
                 "tertiary-fixed-dim": "#73a2ff",
                 "surface-container-highest": "#d9dde0",
                 "surface-variant": "#d9dde0",
                 "tertiary": "#0057bd",
                 "on-primary-fixed": "#000000",
                 "primary": "#3952bc",
                 "background": "#f5f7f9",
                 "on-surface": "#2c2f31",
                 "surface-bright": "#f5f7f9",
                 "secondary-container": "#e4c6ff",
                 "on-tertiary-container": "#002e6a",
                 "on-secondary-fixed-variant": "#663c92",
                 "inverse-surface": "#0b0f10",
                 "surface-dim": "#d0d5d8",
                 "outline-variant": "#abadaf",
                 "on-tertiary": "#f0f2ff",
                 "primary-dim": "#2b45af",
                 "tertiary-dim": "#004ca6",
                 "primary-fixed": "#859aff",
                 "tertiary-container": "#8ab0ff",
                 "on-primary": "#f2f1ff",
                 "on-surface-variant": "#595c5e"
               },
               fontFamily: {
                 "headline": ["Poppins"],
                 "body": ["Poppins"],
                 "label": ["Poppins"]
               },
               borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "2xl": "1rem", "full": "9999px"},
               boxShadow: {
                 'card': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                 'card-heavy': '0 10px 15px -3px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.1)',
                 'card-sharp': '0 8px 0px -2px rgba(0, 0, 0, 0.05), 0 15px 30px -5px rgba(0, 0, 0, 0.12)',
               }
             },
           },
         }
      </script>
      <style>
         .material-symbols-outlined {
         font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
         vertical-align: middle;
         }
         .signature-gradient {
         background: linear-gradient(135deg, #3952bc 0%, #72479e 100%);
         }
         .glass-panel {
         background: rgba(255, 255, 255, 0.7);
         backdrop-filter: blur(20px);
         }
         .hide-scrollbar::-webkit-scrollbar { display: none; }
         .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
         /* Custom Shadow Overrides for Crispness */
         .anchored-card {
         box-shadow: 0 4px 0px 0px rgba(0, 0, 0, 0.05), 0 12px 24px -4px rgba(0, 0, 0, 0.15);
         border: 1px solid rgba(0, 0, 0, 0.08);
         transition: transform 0.2s ease, box-shadow 0.2s ease;
         }
         .anchored-card:hover {
         transform: translateY(-2px);
         box-shadow: 0 6px 0px 0px rgba(0, 0, 0, 0.05), 0 20px 32px -8px rgba(0, 0, 0, 0.2);
         }
      </style>
   </head>
   <body class="bg-[#f0f2f5] font-body text-on-surface min-h-screen flex flex-col">
      <!-- TopAppBar -->
      <header class="w-full sticky top-0 bg-[#ffffff] border-b border-[#dfe3e6] z-50 shadow-sm">
         <div class=" mx-auto px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-10">
               <div class="flex flex-col">
                  <h1 class="font-headline font-bold text-[#3952bc] text-xl tracking-tighter leading-tight">OHS Division</h1>
                  <p class="text-on-surface-variant text-[10px] font-bold uppercase tracking-widest">Safety Performance Review</p>
               </div>
               <div class="h-8 w-px bg-[#dfe3e6] hidden lg:block"></div>
               <nav class="hidden md:flex gap-8">
                  <a class="text-[#3952bc] border-b-2 border-[#3952bc] pb-1 font-bold text-sm tracking-tight" href="#">Lagging</a>
                  <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Dash Performance</a>
                  <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Thematic Alignment</a>
                  <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Site Notic</a>
                  <!-- <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Analytics</a> -->
               </nav>
            </div>
            <div class="flex items-center gap-6">
               <div class="relative group hidden xl:block">
                  <input class="bg-[#f5f7f9] border-none rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-primary w-80 transition-all shadow-inner" placeholder="Search safety records..." type="text"/>
                  <span class="material-symbols-outlined absolute right-3 top-2 text-on-surface-variant" data-icon="search">search</span>
               </div>
               <div class="flex items-center gap-3">
                  <button class="p-2 hover:bg-[#dfe3e6] rounded-full transition-colors relative">
                  <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                  <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-error border-2 border-white rounded-full"></span>
                  </button>
                  <button class="flex items-center gap-2 p-1.5 pr-4 bg-white hover:bg-[#dfe3e6] rounded-full transition-colors border border-outline-variant/30 shadow-sm">
                     <span class="material-symbols-outlined text-3xl text-primary" data-icon="account_circle">account_circle</span>
                     <div class="text-left">
                        <p class="text-[10px] font-bold text-primary uppercase leading-none">Safety Admin</p>
                        <p class="text-[9px] text-on-surface-variant font-medium">Site Manager</p>
                     </div>
                  </button>
               </div>
            </div>
         </div>
      </header>
      <!-- Main Content Area -->
      <main class="flex-grow w-full  mx-auto p-8 space-y-8">
         @php
            $wt = $weeklyTrend ?? [
                'weeks' => [],
                'max_count' => 0,
                'avg_count' => 0,
                'target_line_bottom_pct' => 0,
                'period_caption' => 'Semua data (per bulan)',
                'chart_year' => null,
                'chart_month' => null,
                'month_label' => '',
                'period_scope' => 'all',
                'avg_legend_label' => 'Rata-rata bulanan',
            ];
            $pickerYear = (int) min(max(now()->year, 2025), 2026);
            $pickerMonth = (int) now()->month;
            $cy = ($chartPeriodMonth ?? false) ? (int) ($chartYear ?? $pickerYear) : $pickerYear;
            $cm = ($chartPeriodMonth ?? false) ? (int) ($chartMonth ?? $pickerMonth) : $pickerMonth;
            $peerResetParams = [];
            if (($q ?? '') !== '') {
                $peerResetParams['q'] = $q;
            }
            if (!empty($chartPeriodMonth)) {
                $peerResetParams['year'] = $chartYear;
                $peerResetParams['month'] = $chartMonth;
            }
            $monthsShort = [1 => 'Jan', 2 => 'Peb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
            $peerSiteFilterList = [];
            foreach (
                [
                    ($peerHazardReportingBySite ?? [])['sites'] ?? [],
                    ($peerTbcHighBySite ?? [])['sites'] ?? [],
                    ($peerTbcBlindspotBySite ?? [])['sites'] ?? [],
                    ($peerGoldenRulesBySite ?? [])['sites'] ?? [],
                    ($peerAreaNonKritisBySite ?? [])['sites'] ?? [],
                    ($peerAreaKritisBySite ?? [])['sites'] ?? [],
                ] as $_siteArr
            ) {
                foreach ($_siteArr as $_s) {
                    $_s = (string) $_s;
                    if ($_s !== '' && ! in_array($_s, $peerSiteFilterList, true)) {
                        $peerSiteFilterList[] = $_s;
                    }
                }
            }
            $tbcCategoryTrendPath = resource_path('data/peer_pressure_tbc_category_trend.json');
            $tbcCategoryTrendData = is_file($tbcCategoryTrendPath)
                ? json_decode((string) file_get_contents($tbcCategoryTrendPath), true)
                : null;
         @endphp
         <!-- Header & Top Filters -->
         <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6 pb-6 border-b border-outline-variant/30">
            <div>
               <nav class="flex items-center gap-2 text-xs font-bold text-on-surface-variant uppercase mb-2">
                  <span>Dashboard</span>
                  <span class="material-symbols-outlined text-xs">chevron_right</span>
                  <span class="text-primary">Alignment Meeting Dashboard</span>
               </nav>
               <h2 class="font-headline font-extrabold text-4xl text-on-background tracking-tight">Safety Performance Review</h2>
               <p class="text-on-surface-variant font-medium mt-1">Program performance Week 15 2026 • Updated 5 mins ago</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
               <!-- <div class="bg-white px-4 py-2.5 rounded-xl border border-outline-variant/30 flex items-center gap-2 text-sm font-semibold text-on-surface-variant cursor-pointer hover:shadow-md transition-all">
                  <span class="material-symbols-outlined text-lg" data-icon="calendar_today">calendar_today</span>
                  Last 30 Days
                  <span class="material-symbols-outlined text-lg">expand_more</span>
               </div> -->
                <div class="flex w-full flex-col gap-1.5 sm:w-auto sm:min-w-[16rem]">
                     <label for="peer-hazard-site-filter" class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Filter site</label>
                     <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-primary">
                           <span class="material-symbols-outlined text-xl" data-icon="location_on">location_on</span>
                        </span>
                        <select id="peer-hazard-site-filter" name="hazard_site" class="peer w-full cursor-pointer appearance-none rounded-xl border border-outline-variant/30 bg-[#f8fafc] py-3 pl-11 pr-10 text-sm font-bold text-on-surface shadow-inner transition-colors hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/30" data-initial-site="{{ e($hazardSite ?? '__all') }}">
                           <option value="__all" {{ ($hazardSite ?? '__all') === '__all' ? 'selected' : '' }}>Semua site</option>
                           @if(!empty($peerSiteFilterList))
                              @foreach($peerSiteFilterList as $siteOpt)
                              <option value="{{ e($siteOpt) }}" {{ ($hazardSite ?? '') === $siteOpt ? 'selected' : '' }}>{{ $siteOpt }}</option>
                              @endforeach
                           @endif
                        </select>
                        <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant">
                           <span class="material-symbols-outlined">expand_more</span>
                        </span>
                     </div>
                     <span id="peer-weekly-period-label" class="sr-only">{{ $wt['period_caption'] ?? '—' }}</span>
                  </div>

              
               <!-- <button class="signature-gradient text-white font-bold px-6 py-2.5 rounded-xl shadow-lg hover:opacity-90 active:scale-[0.98] transition-all flex items-center gap-2">
               <span class="material-symbols-outlined text-lg" data-icon="file_download">file_download</span>
               Export Report
               </button> -->
            </div>
         </div>
         <!-- KPI Row -->
         @php
            $kpi = $kpi ?? [];
            $kpiTotal = (int) ($kpi['total_cases'] ?? 0);
            $kpiCompletion = (float) ($kpi['completion_rate'] ?? 0);
            $kpiBarW = max(0, min(100, $kpiCompletion));
            $kpiTrendPct = $kpi['total_cases_trend_pct'] ?? null;
            $icPre = $insightCards ?? [];
            $dvPre = $icPre['deviation'] ?? [];
            $dvPreCats = $dvPre['categories'] ?? [];
            $dvPreTotal = (int) ($dvPre['total'] ?? 0);
            $dvPreSumJumlah = (int) collect($dvPreCats)->sum(fn ($r) => (int) ($r['jumlah'] ?? 0));
            $dvPreFooterTotal = $dvPreSumJumlah > 0 ? $dvPreSumJumlah : $dvPreTotal;
            /** Mini bar Hazard Reporting (mingguan) — dari resources/data/peer_pressure_hazard_reporting_by_site.json jika ada */
            $peerHrEval = $peerHrEvalFromJson
               ?? $peerKpiHazardReportingEval
               ?? [
                  'weeks' => ['W10', 'W11', 'W12', 'W13'],
                  'label' => 'Hazard Reporting',
                  'bar' => '#DEE5EF',
                  'values' => [20.595, 15.543, 17.861, 19.570],
                  'decimals' => 3,
               ];
            /** L4W Golden Rules — dari resources/data/peer_pressure_golden_rules_by_site.json jika ada */
            $peerGoldenRulesL4w = $peerKpiGoldenRulesL4w ?? [
               'weeks' => ['W11', 'W12', 'W13', 'W14'],
               'values' => [1, 1, 3, 1],
            ];
            if (! empty($peerGoldenRulesEvalFromJson)) {
                $peerGoldenRulesL4w = [
                    'weeks' => $peerGoldenRulesEvalFromJson['weeks'],
                    'values' => $peerGoldenRulesEvalFromJson['values'],
                ];
            }
            $kpiValidGrCount = (int) ($kpi['peer_pressure_compliance_comply'] ?? 0);
            $grL4wVals = $peerGoldenRulesL4w['values'] ?? [];
            $grPanelTotalDisplay = ! empty($peerGoldenRulesBySite)
                ? (int) round(array_sum($grL4wVals))
                : $kpiTotal;
            $grPanelValidGrDisplay = ! empty($peerGoldenRulesBySite)
                ? (count($grL4wVals) ? (int) round($grL4wVals[count($grL4wVals) - 1]) : 0)
                : $kpiValidGrCount;
            /** Panel L4W kiri — Area Non Kritis Need to Check (peer_pressure_area_non_kritis_by_site.json) */
            $peerAreaNonKritisL4w = [
                'weeks' => ['W12', 'W13', 'W14', 'W15'],
                'values' => [0, 0, 0, 0],
            ];
            if (! empty($peerAreaNonKritisEvalFromJson)) {
                $peerAreaNonKritisL4w = [
                    'weeks' => $peerAreaNonKritisEvalFromJson['weeks'],
                    'values' => $peerAreaNonKritisEvalFromJson['values'],
                ];
            }
            $ankL4wVals = $peerAreaNonKritisL4w['values'] ?? [];
            $ankPanelTotalDisplay = count($ankL4wVals) ? (int) round(array_sum($ankL4wVals)) : 0;
            $ankPanelLastDisplay = count($ankL4wVals) ? (int) round($ankL4wVals[count($ankL4wVals) - 1]) : 0;
            $ankParamLabel = (string) (($peerAreaNonKritisBySite ?? [])['parameter'] ?? 'Area Non Kritis Need to Check');
            /** Panel kedua — Area Kritis Need to Check (peer_pressure_area_kritis_by_site.json) */
            $peerAreaKritisL4w = [
                'weeks' => ['W12', 'W13', 'W14', 'W15'],
                'values' => [0, 0, 0, 0],
            ];
            if (! empty($peerAreaKritisEvalFromJson)) {
                $peerAreaKritisL4w = [
                    'weeks' => $peerAreaKritisEvalFromJson['weeks'],
                    'values' => $peerAreaKritisEvalFromJson['values'],
                ];
            }
            $akL4wVals = $peerAreaKritisL4w['values'] ?? [];
            $akPanelTotalDisplay = count($akL4wVals) ? (int) round(array_sum($akL4wVals)) : 0;
            $akPanelLastDisplay = count($akL4wVals) ? (int) round($akL4wVals[count($akL4wVals) - 1]) : 0;
            $akParamLabel = (string) (($peerAreaKritisBySite ?? [])['parameter'] ?? 'Area Kritis Need to Check');
            /** Kartu KPI compliance (peer-kpi-compliance-card): total GR + 4 minggu dari peer_pressure_golden_rules_by_site.json */
            $peerGrParamLabel = (string) (($peerGoldenRulesBySite ?? [])['parameter'] ?? 'Jumlah GR');
            $peerGrKpiEval = [
                'weeks' => $peerGoldenRulesL4w['weeks'] ?? [],
                'values' => $peerGoldenRulesL4w['values'] ?? [],
                'label' => $peerGrParamLabel,
                'bar' => '#c8102e',
                'decimals' => 0,
            ];
            $grValsCard = $peerGrKpiEval['values'] ?? [];
            $grWksCard = $peerGrKpiEval['weeks'] ?? [];
            $nGrCard = count($grValsCard);
            $grCardTotalDisplay = $nGrCard > 0 ? (int) round(array_sum($grValsCard)) : 0;
            $grCardWoWPct = null;
            $grCardWkPrev = 'W13';
            $grCardWkLast = 'W14';
            if ($nGrCard >= 2) {
                $grCardPrev = (float) $grValsCard[$nGrCard - 2];
                $grCardLast = (float) $grValsCard[$nGrCard - 1];
                $grCardWkPrev = (string) ($grWksCard[$nGrCard - 2] ?? 'W13');
                $grCardWkLast = (string) ($grWksCard[$nGrCard - 1] ?? 'W14');
                if ($grCardPrev > 0.0001) {
                    $grCardWoWPct = (($grCardLast - $grCardPrev) / $grCardPrev) * 100;
                }
            }
            /** TBC High — dari resources/data/peer_pressure_tbc_high_by_site.json jika ada */
            $peerTbcHigh = $peerTbcEvalFromJson
               ?? $peerKpiTbcHighRiskEval
               ?? [
                  'weeks' => ['W10', 'W11', 'W12', 'W13'],
                  'label' => 'To Be Concerned High Risk Hazards',
                  'bar' => '#E2E2E2',
                  'values' => [7.600, 5.670, 5.754, 6.237],
                  'decimals' => 3,
               ];
            /** Blindspot TBC — dari resources/data/peer_pressure_tbc_blindspot_by_site.json jika ada */
            $peerTbcBlind = $peerTbcBlindEvalFromJson
               ?? $peerKpiTbcBlindspotEval
               ?? [
                  'weeks' => ['W10', 'W11', 'W12', 'W13'],
                  'label' => 'To Be Concerned High Risk Hazards Blindspot',
                  'bar' => '#FFCC33',
                  'values' => [42, 26, 8, 21],
                  'decimals' => 0,
               ];
            /** Minggu untuk sparkline modal (sinkron dengan mini bar Hazard Reporting); override via $peerDeviationModalSparkWeeks dari controller */
            if (! isset($peerDeviationModalSparkWeeks) || ! is_array($peerDeviationModalSparkWeeks)) {
                $peerDeviationModalSparkWeeks = $peerHrEval['weeks'] ?? ['W11', 'W12', 'W13', 'W14'];
            }
            $makeDeviationWeekly = static function (int $j, string $kat, array $wks): array {
                $n = count($wks);
                if ($n === 0) {
                    return [];
                }
                if ($j <= 0) {
                    return array_fill(0, $n, 0);
                }
                $h = 2166136261;
                $len = strlen($kat);
                for ($i = 0; $i < $len; $i++) {
                    $h = (($h ^ ord($kat[$i])) * 16777619) & 0xFFFFFFFF;
                }
                $weights = [];
                $sumW = 0.0;
                for ($i = 0; $i < $n; $i++) {
                    $w = 0.12 + ((($h >> ($i * 7)) & 0xFF) / 255) * 0.88;
                    $weights[] = $w;
                    $sumW += $w;
                }
                $vals = [];
                $acc = 0;
                for ($i = 0; $i < $n - 1; $i++) {
                    $vals[] = (int) max(0, (int) round($j * $weights[$i] / $sumW));
                    $acc += $vals[$i];
                }
                $vals[] = max(0, $j - $acc);

                return $vals;
            };
            $dvPreCatsSpark = [];
            foreach ($dvPreCats as $idx => $drow) {
                $kat = (string) ($drow['kategori_deviasi'] ?? '');
                $j = (int) ($drow['jumlah'] ?? 0);
                $dvPreCatsSpark[] = array_merge($drow, [
                    'weekly_values' => $makeDeviationWeekly($j, $kat, $peerDeviationModalSparkWeeks),
                    '_idx' => $idx,
                ]);
            }
         @endphp
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <button type="button" id="peer-kpi-deviation-card" class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-start text-left w-full cursor-pointer transition-all hover:shadow-md hover:ring-2 hover:ring-primary/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40" aria-haspopup="dialog" aria-expanded="false" aria-controls="peer-deviation-category-modal" data-json-hazard="{{ !empty($peerHazardReportingBySite) ? '1' : '0' }}">
               <div class="flex justify-between items-start">
                  <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Hazard Reporting</span>
                  <div class="p-2 bg-[#fef3c7] rounded-lg">
                     <span class="material-symbols-outlined text-[#d97706]" data-icon="groups">groups</span>
                  </div>
               </div>
               <div class="mt-1">
                  @php
                     $hrVals = $peerHrEval['values'] ?? [];
                     $hrWks = $peerHrEval['weeks'] ?? [];
                     $nHr = count($hrVals);
                     $hrLast = $nHr > 0 ? (float) $hrVals[$nHr - 1] : (float) $kpiTotal;
                     $hrWoWPct = null;
                     $hrWkPrev = 'W14';
                     $hrWkLast = 'W15';
                     if ($nHr >= 2) {
                        $hrPrev = (float) $hrVals[$nHr - 2];
                        $hrWkPrev = (string) ($hrWks[$nHr - 2] ?? 'W14');
                        $hrWkLast = (string) ($hrWks[$nHr - 1] ?? 'W15');
                        if ($hrPrev > 0.0001) {
                           $hrWoWPct = (($hrLast - $hrPrev) / $hrPrev) * 100;
                        }
                     }
                  @endphp
                  <p id="peer-kpi-hazard-total" class="font-headline font-extrabold text-4xl tabular-nums" data-json-driven="1">{{ number_format((int) round($hrLast)) }}</p>
                  <div id="peer-kpi-hazard-trend" class="mt-1" data-json-driven="1">
                  @if($hrWoWPct !== null)
                  <p class="text-[11px] font-bold flex items-center gap-1 {{ $hrWoWPct <= 0 ? 'text-[#059669]' : 'text-error' }}">
                     <span class="material-symbols-outlined text-xs" data-icon="{{ $hrWoWPct <= 0 ? 'trending_down' : 'trending_up' }}">{{ $hrWoWPct <= 0 ? 'trending_down' : 'trending_up' }}</span>
                     WoW {{ $hrWoWPct >= 0 ? '+' : '' }}{{ number_format($hrWoWPct, 1) }}% ({{ $hrWkPrev }} → {{ $hrWkLast }})
                  </p>
                  @else
                  <p class="text-on-surface-variant text-[11px] font-medium">—</p>
                  @endif
                  </div>
               </div>
               <div id="peer-kpi-hazard-mini-bar-host" class="min-w-0 w-full">
               @include('peer-pressure-edukasi.partials.kpi-hazard-mini-bar', ['eval' => $peerHrEval, 'chartTopMargin' => 'mt-1.5'])
               </div>
            </button>
            <button type="button" id="peer-kpi-tbc-high-card" class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between text-left w-full cursor-pointer transition-all hover:shadow-md hover:ring-2 hover:ring-primary/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40" aria-haspopup="dialog" aria-expanded="false" aria-controls="peer-tbc-general-modal" data-json-tbc="{{ !empty($peerTbcHighBySite) ? '1' : '0' }}">
               <div class="flex justify-between items-start">
                  <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">To Be Concerned High Risk Hazards</span>
                  <div class="p-2 bg-primary/10 rounded-lg">
                     <span class="material-symbols-outlined text-primary" data-icon="assignment_late">assignment_late</span>
                  </div>
                 
               </div>
               <div class="">
                  @php
                     $tbcVals = $peerTbcHigh['values'] ?? [];
                     $tbcWks = $peerTbcHigh['weeks'] ?? [];
                     $nTbc = count($tbcVals);
                     $tbcLast = $nTbc > 0 ? (float) $tbcVals[$nTbc - 1] : (float) $kpiTotal;
                     $tbcWoWPct = null;
                     $tbcWkPrev = 'W14';
                     $tbcWkLast = 'W15';
                     if ($nTbc >= 2) {
                        $tbcPrev = (float) $tbcVals[$nTbc - 2];
                        $tbcWkPrev = (string) ($tbcWks[$nTbc - 2] ?? 'W14');
                        $tbcWkLast = (string) ($tbcWks[$nTbc - 1] ?? 'W15');
                        if ($tbcPrev > 0.0001) {
                           $tbcWoWPct = (($tbcLast - $tbcPrev) / $tbcPrev) * 100;
                        }
                     }
                  @endphp
                  <p id="peer-kpi-tbc-high-total" class="peer-kpi-tbc-high-total font-headline font-extrabold text-4xl tabular-nums" data-json-driven="{{ !empty($peerTbcHighBySite) ? '1' : '0' }}">{{ number_format((int) round($tbcLast)) }}</p>
                  <div id="peer-kpi-tbc-high-trend" class="peer-kpi-tbc-high-trend mt-1" data-json-driven="{{ !empty($peerTbcHighBySite) ? '1' : '0' }}">
                  @if($tbcWoWPct !== null)
                  <p class="text-[11px] font-bold flex items-center gap-1 {{ $tbcWoWPct <= 0 ? 'text-[#059669]' : 'text-error' }}">
                     <span class="material-symbols-outlined text-xs" data-icon="{{ $tbcWoWPct <= 0 ? 'trending_down' : 'trending_up' }}">{{ $tbcWoWPct <= 0 ? 'trending_down' : 'trending_up' }}</span>
                     WoW {{ $tbcWoWPct >= 0 ? '+' : '' }}{{ number_format($tbcWoWPct, 1) }}% ({{ $tbcWkPrev }} → {{ $tbcWkLast }})
                  </p>
                  @else
                  <p class="text-on-surface-variant text-[11px] font-medium">—</p>
                  @endif
                  </div>
               </div>

               <div id="peer-kpi-tbc-mini-bar-host" class="min-w-0 w-full">
               @include('peer-pressure-edukasi.partials.kpi-hazard-mini-bar', ['eval' => $peerTbcHigh])
               </div>
            </button>
            <button type="button" id="peer-kpi-blindspot-card" onclick="window.peerOpenBlindspotModal && window.peerOpenBlindspotModal(event)" class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between text-left w-full cursor-pointer transition-all hover:shadow-md hover:ring-2 hover:ring-emerald-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300" aria-haspopup="dialog" aria-expanded="false" aria-controls="peer-blindspot-modal" data-json-blindspot="{{ !empty($peerTbcBlindspotBySite) ? '1' : '0' }}">
               <div class="flex justify-between items-start">
                  <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">To Be Concerned High Risk Hazards Blindspot</span>
                  <div class="p-2 bg-[#dcfce7] rounded-lg">
                     <span class="material-symbols-outlined text-[#16a34a]" data-icon="task_alt">task_alt</span>
                  </div>
               </div>
               <div class="">
                  @php
                     $blVals = $peerTbcBlind['values'] ?? [];
                     $blWks = $peerTbcBlind['weeks'] ?? [];
                     $nBl = count($blVals);
                     $blLast = $nBl > 0 ? (float) $blVals[$nBl - 1] : (float) $kpiTotal;
                     $blWoWPct = null;
                     $blWkPrev = 'W14';
                     $blWkLast = 'W15';
                     if ($nBl >= 2) {
                        $blPrev = (float) $blVals[$nBl - 2];
                        $blWkPrev = (string) ($blWks[$nBl - 2] ?? 'W14');
                        $blWkLast = (string) ($blWks[$nBl - 1] ?? 'W15');
                        if ($blPrev > 0.0001) {
                           $blWoWPct = (($blLast - $blPrev) / $blPrev) * 100;
                        }
                     }
                  @endphp
                  <p id="peer-kpi-blindspot-total" class="font-headline font-extrabold text-4xl tabular-nums" data-json-driven="{{ !empty($peerTbcBlindspotBySite) ? '1' : '0' }}">{{ number_format((int) round($blLast)) }}</p>
                  <div id="peer-kpi-blindspot-trend" class="mt-1" data-json-driven="{{ !empty($peerTbcBlindspotBySite) ? '1' : '0' }}">
                  @if($blWoWPct !== null)
                  <p class="text-[11px] font-bold flex items-center gap-1 {{ $blWoWPct <= 0 ? 'text-[#059669]' : 'text-error' }}">
                     <span class="material-symbols-outlined text-xs" data-icon="{{ $blWoWPct <= 0 ? 'trending_down' : 'trending_up' }}">{{ $blWoWPct <= 0 ? 'trending_down' : 'trending_up' }}</span>
                     WoW {{ $blWoWPct >= 0 ? '+' : '' }}{{ number_format($blWoWPct, 1) }}% ({{ $blWkPrev }} → {{ $blWkLast }})
                  </p>
                  @else
                  <p class="text-on-surface-variant text-[11px] font-medium">—</p>
                  @endif
                  </div>
               </div>
               <div id="peer-kpi-blindspot-mini-bar-host" class="min-w-0 w-full">
               @include('peer-pressure-edukasi.partials.kpi-hazard-mini-bar', ['eval' => $peerTbcBlind])
               </div>
            </button>
           
            <button type="button" id="peer-kpi-compliance-card" class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between text-left w-full cursor-pointer transition-all hover:shadow-md hover:ring-2 hover:ring-secondary/25 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40" aria-haspopup="dialog" aria-expanded="false" aria-controls="peer-compliance-detail-modal" data-json-gr="{{ !empty($peerGoldenRulesBySite) ? '1' : '0' }}">
               <div class="flex justify-between items-start">
                  <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">{{ $peerGrParamLabel }}</span>
                  <div class="p-2 bg-secondary/10 rounded-lg">
                     <span class="material-symbols-outlined text-secondary" data-icon="verified">verified</span>
                  </div>
               </div>
               <div class="">
                  <p id="peer-kpi-gr-total" class="font-headline font-extrabold text-4xl tabular-nums" data-json-driven="{{ !empty($peerGoldenRulesBySite) ? '1' : '0' }}">{{ number_format($grCardTotalDisplay) }}</p>
                  <div id="peer-kpi-gr-trend" class="mt-1" data-json-driven="{{ !empty($peerGoldenRulesBySite) ? '1' : '0' }}">
                  @if($grCardWoWPct !== null)
                  <p class="text-[11px] font-bold flex items-center gap-1 {{ $grCardWoWPct <= 0 ? 'text-[#059669]' : 'text-error' }}">
                     <span class="material-symbols-outlined text-xs" data-icon="{{ $grCardWoWPct <= 0 ? 'trending_down' : 'trending_up' }}">{{ $grCardWoWPct <= 0 ? 'trending_down' : 'trending_up' }}</span>
                     WoW {{ $grCardWoWPct >= 0 ? '+' : '' }}{{ number_format($grCardWoWPct, 1) }}% ({{ $grCardWkPrev }} → {{ $grCardWkLast }})
                  </p>
                  @else
                  <p class="text-on-surface-variant text-[11px] font-medium">—</p>
                  @endif
                  </div>
               </div>
               <div id="peer-kpi-gr-compliance-mini-bar-host" class="min-w-0 w-full space-y-1">
                  @include('peer-pressure-edukasi.partials.kpi-hazard-mini-bar', ['eval' => $peerGrKpiEval, 'compact' => true])
               </div>
               
            </button>
         </div>

         <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:items-stretch">
            <div id="peer-kpi-hazard-matrix-column" class="flex min-h-0 w-full flex-col gap-6 lg:col-span-3 lg:h-full">
               <button type="button" id="peer-kpi-hazard-reporting-card" class="bg-white p-6 rounded-2xl anchored-card flex w-full flex-col justify-start text-left cursor-pointer transition-all hover:shadow-md hover:ring-2 hover:ring-primary/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40" aria-haspopup="dialog" aria-expanded="false" aria-controls="peer-deviation-category-modal">
                  @include('peer-pressure-edukasi.partials.kpi-l4w-golden-rules-panel', [
                      'total' => $ankPanelTotalDisplay,
                      'validGr' => $ankPanelLastDisplay,
                      'weeks' => $peerAreaNonKritisL4w['weeks'] ?? [],
                      'values' => $peerAreaNonKritisL4w['values'] ?? [],
                      'decimals' => 0,
                      'grFromJson' => false,
                      'ankFromJson' => ! empty($peerAreaNonKritisBySite),
                      'chartTitle' => 'L4W ' . $ankParamLabel,
                      'panelSubtitleSuffix' => 'minggu terakhir',
                      'barColor' => '#ea580c',
                  ])
                  <div class="peer-kpi-hr-stack-trend hidden" aria-hidden="true">
                     @if($kpiTrendPct !== null)
                     <p class="text-[11px] font-bold flex items-center gap-1 {{ $kpiTrendPct <= 0 ? 'text-[#059669]' : 'text-error' }}">
                        <span class="material-symbols-outlined text-xs" data-icon="{{ $kpiTrendPct <= 0 ? 'trending_down' : 'trending_up' }}">{{ $kpiTrendPct <= 0 ? 'trending_down' : 'trending_up' }}</span>
                        {{ $kpi['total_cases_trend_label'] ?? '' }}
                     </p>
                     @else
                     <p class="text-on-surface-variant text-[11px] font-medium">{{ $kpi['total_cases_trend_label'] ?? '—' }}</p>
                     @endif
                  </div>
               </button>

               <button type="button" id="peer-kpi-deviation-card-stack" class="bg-white p-6 rounded-2xl anchored-card flex w-full flex-col justify-start text-left cursor-pointer transition-all hover:shadow-md hover:ring-2 hover:ring-primary/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40" aria-haspopup="dialog" aria-expanded="false" aria-controls="peer-deviation-category-modal">
                  @include('peer-pressure-edukasi.partials.kpi-l4w-golden-rules-panel', [
                      'total' => $akPanelTotalDisplay,
                      'validGr' => $akPanelLastDisplay,
                      'weeks' => $peerAreaKritisL4w['weeks'] ?? [],
                      'values' => $peerAreaKritisL4w['values'] ?? [],
                      'decimals' => 0,
                      'grFromJson' => false,
                      'ankFromJson' => false,
                      'kritisFromJson' => ! empty($peerAreaKritisBySite),
                      'chartTitle' => 'L4W ' . $akParamLabel,
                      'panelSubtitleSuffix' => 'minggu terakhir',
                      'barColor' => '#dc2626',
                  ])
                  <div class="peer-kpi-hr-stack-trend hidden" aria-hidden="true">
                     @if($kpiTrendPct !== null)
                     <p class="text-[11px] font-bold flex items-center gap-1 {{ $kpiTrendPct <= 0 ? 'text-[#059669]' : 'text-error' }}">
                        <span class="material-symbols-outlined text-xs" data-icon="{{ $kpiTrendPct <= 0 ? 'trending_down' : 'trending_up' }}">{{ $kpiTrendPct <= 0 ? 'trending_down' : 'trending_up' }}</span>
                        {{ $kpi['total_cases_trend_label'] ?? '' }}
                     </p>
                     @else
                     <p class="text-on-surface-variant text-[11px] font-medium">{{ $kpi['total_cases_trend_label'] ?? '—' }}</p>
                     @endif
                  </div>
               </button>

               @include('peer-pressure-edukasi.partials.peer-performance-brief-summary')
            </div>

            <div class="min-w-0 lg:col-span-9">
               @include('peer-pressure-edukasi.partials.operational-performance-matrix', ['matrixShellClass' => 'w-full min-w-0'])
            </div>
         </div>



<!-- 
         <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="min-w-0 lg:col-span-12">
               @include('peer-pressure-edukasi.partials.trend-of-accident-panel')
            </div>
         </div> -->


         @php
            $tableauEmbeddingApiSrc = 'https://idashboard.beraucoal.co.id/javascripts/api/tableau.embedding.3.latest.min.js';
            $tableauOverviewSafetyVizSrc = 'https://idashboard.beraucoal.co.id/t/hsedivision/views/OverviewSafetyPerformance_17471016698280/OverviewSafetyPerformanceAllSites2';
            $tableauTbcAllSiteVizSrc = 'https://idashboard.beraucoal.co.id/t/hsedivision/views/DashboardTBCAllsiteRev/SlideAligmnentv2toUSE';
            $tableauBlindspotVizSrc = 'https://idashboard.beraucoal.co.id/t/hsedivision/views/DashboardTBCAllsiteRev/SlideAligmnentBlindspottoUSE';
         @endphp
         <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-12">
               <div class="overflow-hidden rounded-2xl border border-outline-variant/30 bg-white shadow-[0_4px_0px_0px_rgba(0,0,0,0.05),0_12px_24px_-4px_rgba(0,0,0,0.15)]">
                  <div class="border-b border-outline-variant/20 bg-gradient-to-r from-slate-50 to-white px-5 py-4 sm:px-6">
                     <h3 class="font-headline text-base font-bold text-on-surface">Overview Safety Performance</h3>
                     <p class="mt-0.5 text-[11px] text-on-surface-variant">Tableau Embedding API v3 — iDashboard Berau Coal</p>
                  </div>
                  <div class="w-full overflow-x-auto bg-slate-50/30 p-2 sm:p-4">
                     <script type="module" src="{{ $tableauEmbeddingApiSrc }}"></script>
                     <tableau-viz
                        id="tableau-viz"
                        src="{{ $tableauOverviewSafetyVizSrc }}"
                        width="1654"
                        height="1209"
                        hide-tabs
                        toolbar="bottom"
                        class="mx-auto block max-w-full"
                     ></tableau-viz>
                  </div>
               </div>
            </div>
         </div>

         @include('peer-pressure-edukasi.partials.thematic-alignment-program-table')





       
         









         <!-- Secondary Metrics Grid -->
         <!-- @php
            $ic = isset($insightCards) ? $insightCards : [
                'deviation' => ['total' => 0, 'total_label' => '0', 'conic_gradient' => 'conic-gradient(rgb(241 245 249) 0% 100%)', 'categories' => []],
                'compliance' => ['berecord_pct' => 0, 'evidence_pct' => 0, 'size_pct' => 0, 'h1_pct' => 0, 'duration_label' => '—', 'triangle_rotate_deg' => 12],
                'locations' => [],
                'profiling_pelanggar' => [],
            ];
            $dvCats = $ic['deviation']['categories'] ?? [];
            $locRows = $ic['locations'] ?? [];
            $profilingPelanggar = $ic['profiling_pelanggar'] ?? [];
            $co = $ic['compliance'] ?? [];
         @endphp
         <div id="peer-insight-cards-root" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-2xl anchored-card">
               <h3 class="font-headline font-bold text-[11px] mb-6 uppercase tracking-widest text-on-surface-variant">Deviation Category</h3>
               <div class="flex justify-center mb-8">
                  <div class="relative w-36 h-36 rounded-full p-[14px] shadow-inner" style="background: {{ $ic['deviation']['conic_gradient'] ?? 'conic-gradient(rgb(241 245 249) 0% 100%)' }}">
                     <div class="flex h-full w-full items-center justify-center rounded-full bg-white">
                        <div class="text-center">
                           <span class="block font-extrabold text-2xl tabular-nums">{{ $ic['deviation']['total_label'] ?? '0' }}</span>
                           <span class="block text-[9px] uppercase font-bold text-on-surface-variant">Total</span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="max-h-64 space-y-3 overflow-y-auto overflow-x-hidden pr-1">
                  @forelse ($dvCats as $row)
                  <div class="flex justify-between items-center gap-2 text-xs">
                     <span class="flex min-w-0 flex-1 items-center gap-2">
                        <span class="h-2.5 w-2.5 shrink-0 rounded-full shadow-sm ring-1 ring-black/5" style="background: {{ $row['color'] ?? 'hsl(215 14% 72%)' }}"></span>
                        <span class="truncate" title="{{ $row['kategori_deviasi'] ?? '' }}">{{ $row['kategori_deviasi'] ?? '—' }}</span>
                     </span>
                     <span class="shrink-0 font-bold tabular-nums">{{ number_format((float) ($row['pct'] ?? 0), 1, ',', '.') }}%</span>
                  </div>
                  @empty
                  <p class="text-[11px] text-on-surface-variant">Belum ada data.</p>
                  @endforelse
               </div>
            </div>
            <div class="bg-white p-6 rounded-2xl anchored-card">
               <h3 class="font-headline font-bold text-[11px] mb-6 uppercase tracking-widest text-on-surface-variant">Compliance Radar</h3>
               <div class="relative flex aspect-square w-full items-center justify-center">
                  <div class="absolute inset-0 flex items-center justify-center">
                     <div class="w-[85%] h-[85%] border border-outline-variant/20 rounded-full"></div>
                     <div class="absolute w-[60%] h-[60%] border border-outline-variant/20 rounded-full"></div>
                     <div class="absolute w-[35%] h-[35%] border border-outline-variant/20 rounded-full"></div>
                  </div>
                  <div class="w-0 h-0 scale-125 cursor-crosshair border-b-[70px] border-l-[45px] border-r-[45px] border-b-primary/40 border-l-transparent border-r-transparent transition-transform hover:scale-150" style="transform: rotate({{ (float) ($co['triangle_rotate_deg'] ?? 12) }}deg)"></div>
                  <div class="absolute inset-0 flex flex-col justify-between p-1 text-center text-[10px] font-bold uppercase tracking-tighter text-on-surface-variant">
                     <span class="flex flex-col items-center gap-0.5 leading-tight">
                        <span>BeRecord</span>
                        <span class="text-[9px] font-bold tabular-nums text-primary">{{ number_format((float) ($co['berecord_pct'] ?? 0), 0, ',', '.') }}%</span>
                     </span>
                     <div class="flex w-full justify-between px-1">
                        <span class="flex flex-col items-center gap-0.5 leading-tight">
                           <span>Evidence</span>
                           <span class="text-[9px] font-bold tabular-nums text-primary">{{ number_format((float) ($co['evidence_pct'] ?? 0), 0, ',', '.') }}%</span>
                        </span>
                        <span class="flex flex-col items-center gap-0.5 leading-tight">
                           <span>Size</span>
                           <span class="text-[9px] font-bold tabular-nums text-primary">{{ number_format((float) ($co['size_pct'] ?? 0), 0, ',', '.') }}%</span>
                        </span>
                     </div>
                     <div class="flex w-full justify-between px-3 pb-3">
                        <span class="flex flex-col items-center gap-0.5 leading-tight">
                           <span>H+1</span>
                           <span class="text-[9px] font-bold tabular-nums text-primary">{{ number_format((float) ($co['h1_pct'] ?? 0), 0, ',', '.') }}%</span>
                        </span>
                        <span class="flex flex-col items-center gap-0.5 leading-tight">
                           <span>Duration</span>
                           <span class="text-[9px] font-bold tabular-nums text-primary">{{ $co['duration_label'] ?? '—' }}</span>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
            <div class="bg-white p-6 rounded-2xl anchored-card">
               <h3 class="font-headline font-bold text-[11px] mb-6 uppercase tracking-widest text-on-surface-variant">Location Analysis</h3>
               <div class="peer-loc-scroll max-h-96 space-y-5 overflow-y-auto overflow-x-hidden pr-1 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                  @forelse ($locRows as $loc)
                  <div class="space-y-2">
                     <div class="flex justify-between text-[10px] font-bold uppercase tracking-wider">
                        <span class="min-w-0 truncate pr-2" title="{{ $loc['name'] ?? '' }}">{{ $loc['name'] ?? '—' }}</span>
                        <span class="shrink-0 text-primary tabular-nums">{{ (int) ($loc['count'] ?? 0) }}</span>
                     </div>
                     <div class="w-full bg-[#f1f5f9] h-2.5 rounded-full overflow-hidden border border-outline-variant/10 shadow-inner">
                        <div class="bg-primary h-full rounded-full transition-[width] duration-300" style="width: {{ max(0, min(100, (float) ($loc['bar_pct'] ?? 0))) }}%"></div>
                     </div>
                  </div>
                  @empty
                  <p class="text-[11px] text-on-surface-variant">Belum ada data lokasi.</p>
                  @endforelse
               </div>
            </div>
            <div class="bg-white p-6 rounded-2xl anchored-card">
               <h3 class="font-headline font-bold text-[11px] mb-2 uppercase tracking-widest text-on-surface-variant">Profiling Analysis</h3>
               <p class="mb-4 text-[10px] leading-snug text-on-surface-variant">Pelanggar dengan kejadian terbanyak; korelasi = porsi terhadap total insiden pada periode yang sama. Klik baris untuk modal detail, termasuk kriteria <span class="font-semibold text-on-surface">Per Orang</span> (compliance, repetition, awareness).</p>
               <div class="max-h-80 space-y-2.5 overflow-y-auto overflow-x-hidden pr-1 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                  @forelse ($profilingPelanggar as $p)
                  <div class="peer-profiling-row flex items-center gap-3 rounded-xl border border-outline-variant/15 bg-[#fafbfc] p-2.5 cursor-pointer transition-colors hover:bg-[#f1f5f9] hover:border-primary/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/30" role="button" tabindex="0" data-sid="{{ $p['sid'] ?? '' }}" data-nama="{{ $p['nama'] ?? '' }}">
                     @if(!empty($p['foto_url']))
                     <img src="{{ $p['foto_url'] }}" alt="" class="h-11 w-11 shrink-0 rounded-full object-cover ring-1 ring-outline-variant/20" loading="lazy" decoding="async" />
                     @else
                     <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/12 text-sm font-bold uppercase text-primary ring-1 ring-outline-variant/20" aria-hidden="true">{{ mb_substr((string) ($p['nama'] ?? '?'), 0, 1) }}</div>
                     @endif
                     <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-bold text-on-surface" title="{{ $p['nama'] ?? '' }}">{{ $p['nama'] ?? '—' }}</p>
                        <p class="font-mono text-[10px] text-on-surface-variant">{{ $p['sid'] ?? '—' }}</p>
                     </div>
                     <div class="shrink-0 text-right">
                        <p class="text-base font-extrabold tabular-nums leading-tight text-primary">{{ (int) ($p['kasus'] ?? 0) }}×</p>
                        <p class="text-[9px] text-on-surface-variant">{{ number_format((float) ($p['insiden_share_pct'] ?? 0), 1, ',', '.') }}% insiden</p>
                     </div>
                  </div>
                  @empty
                  <p class="text-[11px] text-on-surface-variant">Belum ada data pelanggar pada periode ini.</p>
                  @endforelse
               </div>
            </div>
         </div> -->
         <!-- Data Table Section -->
         <!-- <div class="bg-white rounded-2xl anchored-card overflow-hidden">
            <div class="p-6 border-b border-outline-variant/20 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
               <div>
                  <h3 class="font-headline font-bold text-xl">Data Peer Pressure</h3>
                  <p class="text-xs text-on-surface-variant font-medium">Detailed log of safety incidents and peer interactions</p>
               </div>
               <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
                  <form id="peer-dashboard-search-form" method="get" action="{{ route('peer-pressure-edukasi.dashboard') }}" class="flex w-full min-w-0 flex-1 flex-col gap-2 sm:max-w-md sm:flex-row sm:items-center">
                     <input type="hidden" id="peer-dashboard-year" name="year" value="{{ (int) $cy }}" @if(empty($chartPeriodMonth)) disabled @endif>
                     <input type="hidden" id="peer-dashboard-month" name="month" value="{{ (int) $cm }}" @if(empty($chartPeriodMonth)) disabled @endif>
                     <div class="relative min-w-0 flex-1">
                        <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 z-0 -translate-y-1/2 text-lg text-on-surface-variant">search</span>
                        <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari lokasi, kategori, dept, SID, nama, leader, status…" autocomplete="off" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] py-2 pl-10 pr-3 text-sm text-on-surface outline-none transition-shadow placeholder:text-on-surface-variant/60 focus:border-primary/40 focus:ring-2 focus:ring-primary/15" aria-label="Cari data kejadian">
                     </div>
                     <div class="flex shrink-0 gap-2">
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm transition-colors hover:bg-surface-container-high">Cari</button>
                        @if(filled($q ?? null))
                        <a href="{{ route('peer-pressure-edukasi.dashboard', $peerResetParams) }}" class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-bold text-on-surface-variant hover:bg-[#f1f5f9]">Reset</a>
                        @endif
                     </div>
                  </form>
                  <div class="flex flex-wrap items-center gap-3">
                     <div class="flex bg-[#f1f5f9] p-1 rounded-xl border border-outline-variant/20">
                        <button type="button" class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-wider bg-white shadow-sm rounded-lg border border-outline-variant/10">Real-time</button>
                        <button type="button" class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant hover:text-primary transition-colors">Archived</button>
                     </div>
                     <button type="button" class="px-4 py-2 bg-white text-xs font-bold rounded-xl hover:bg-surface-container-high transition-colors flex items-center gap-2 border border-outline-variant/30 shadow-sm">
                     <span class="material-symbols-outlined text-sm">filter_list</span> Filters
                     </button>
                     <button type="button" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow-md transition-transform active:scale-95 flex items-center gap-2">
                     <span class="material-symbols-outlined text-sm">download</span> CSV
                     </button>
                  </div>
               </div>
            </div>
            <div class="overflow-x-auto">
               <table class="w-full text-sm text-left">
                  <thead class="bg-[#f8fafc] text-on-surface-variant font-bold text-[10px] uppercase tracking-[0.15em] border-b border-outline-variant/20">
                     <tr>
                        <th class="px-8 py-5">Incident Detail</th>
                        <th class="px-8 py-5">Violator &amp; Dept</th>
                        <th class="px-8 py-5">Peer Group</th>
                        <th class="px-8 py-5">Duration</th>
                        <th class="px-8 py-5">Evidence</th>
                        <th class="px-8 py-5">Status</th>
                     </tr>
                  </thead>
                  <tbody class="divide-y divide-outline-variant/10">
                     @forelse ($kejadian as $k)
                        @php
                           $pelanggar = $k->peserta->firstWhere('peran', 'pelanggar');
                           $peers = $k->peserta->where('peran', 'peer')->values();
                           $visiblePeers = $peers->take(3);
                           $extraPeerCount = max(0, $peers->count() - 3);
                           $avatarBgs = ['bg-secondary-fixed', 'bg-primary-fixed', 'bg-tertiary-fixed'];
                           $catPrimary = 'mt-2 inline-block bg-primary-container/20 text-primary text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider border border-primary/10';
                           $catSecondary = 'mt-2 inline-block bg-secondary-container/20 text-secondary text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider border border-secondary/10';
                           $statusBadge = $k->dashboardStatusBadge();
                        @endphp
                     <tr class="hover:bg-[#f8fafc] transition-colors cursor-pointer group js-peer-kejadian-row" data-kejadian-id="{{ $k->id }}" role="button" tabindex="0">
                        <td class="px-8 py-5">
                           <div class="font-bold text-on-surface">{{ $k->formattedTemuanDatetime() }}</div>
                           <div class="text-[10px] text-on-surface-variant flex items-center gap-1 mt-0.5">
                              <span class="material-symbols-outlined text-[12px]" data-icon="location_on">location_on</span> {{ $k->lokasi_temuan }}
                           </div>
                           <span class="{{ $loop->iteration % 2 === 1 ? $catPrimary : $catSecondary }}">{{ $k->kategori_deviasi }}</span>
                        </td>
                        <td class="px-8 py-5">
                           <div class="font-bold">{{ $pelanggar ? $pelanggar->sid . ' | ' . ($pelanggar->nama ?: '—') : '—' }}</div>
                           <div class="text-xs text-on-surface-variant">{{ $k->departemen ?: '—' }} / {{ $k->aktivitas_pekerjaan ?: '—' }}</div>
                        </td>
                        <td class="px-8 py-5">
                           <div class="flex -space-x-2">
                              @foreach ($visiblePeers as $peer)
                                 @php
                                    $pi = $loop->index;
                                    $sidKey = \Illuminate\Support\Str::lower(trim((string) $peer->sid));
                                    $peerFoto = $peerFotoUrls[$sidKey] ?? null;
                                 @endphp
                                 <div class="relative w-8 h-8 shrink-0 rounded-full border-2 border-white shadow-md overflow-hidden bg-surface-container-high">
                                    @if (filled($peerFoto))
                                    <img src="{{ $peerFoto }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" width="32" height="32" decoding="async" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
                                    <div class="hidden absolute inset-0 flex items-center justify-center text-[10px] font-bold {{ $avatarBgs[$pi] ?? 'bg-surface-container-high' }}">{{ $peer->initials() }}</div>
                                    @else
                                    <div class="flex h-full w-full items-center justify-center text-[10px] font-bold {{ $avatarBgs[$pi] ?? 'bg-surface-container-high' }}">{{ $peer->initials() }}</div>
                                    @endif
                                 </div>
                              @endforeach
                              @if ($extraPeerCount > 0)
                                 <div class="w-8 h-8 rounded-full bg-surface-container-high text-[10px] flex items-center justify-center font-bold border-2 border-white shadow-md text-on-surface-variant">+{{ $extraPeerCount }}</div>
                              @endif
                           </div>
                           <div class="text-[10px] mt-2 font-bold text-on-surface-variant">Leader: {{ $k->pemimpin_edukasi ?: '—' }}</div>
                        </td>
                        <td class="px-8 py-5 font-bold text-xs text-on-surface">{{ $k->durasi_edukasi_menit }}m</td>
                        <td class="px-8 py-5">
                           @if (filled($k->evidence_url))
                           <a href="{{ $k->evidence_url }}" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()" class="text-primary hover:underline flex items-center gap-1 text-xs font-bold transition-all relative z-10">
                           <span class="material-symbols-outlined text-lg" data-icon="attach_file">attach_file</span> View Records
                           </a>
                           @else
                           <div class="text-error font-bold text-xs flex items-center gap-1">
                           <span class="material-symbols-outlined text-lg" data-icon="warning">warning</span> Missing Evidence
                           </div>
                           @endif
                        </td>
                        <td class="px-8 py-5">
                           <span class="{{ $statusBadge['spanClass'] }}">{{ $statusBadge['label'] }}</span>
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="6" class="px-8 py-10 text-center text-sm text-on-surface-variant font-medium">@if(filled($q ?? null))Tidak ada hasil untuk pencarian ini.@else Belum ada data kejadian.@endif</td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
            <div class="p-6 bg-[#f8fafc] flex justify-between items-center border-t border-outline-variant/20">
               <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">@if ($kejadian->total() === 0)Showing 0 of 0 entries @else Showing {{ $kejadian->firstItem() }}-{{ $kejadian->lastItem() }} of {{ number_format($kejadian->total()) }} entries @endif</p>
               <div class="flex gap-2">
                  @if ($kejadian->onFirstPage())
                  <button type="button" class="p-2 border border-outline-variant/30 rounded-lg opacity-40 cursor-not-allowed" disabled aria-disabled="true"><span class="material-symbols-outlined text-sm">chevron_left</span></button>
                  @else
                  <a href="{{ $kejadian->previousPageUrl() }}" class="p-2 border border-outline-variant/30 rounded-lg hover:bg-white hover:shadow-md transition-all inline-flex" rel="prev"><span class="material-symbols-outlined text-sm">chevron_left</span></a>
                  @endif
                  @if (! $kejadian->hasMorePages())
                  <button type="button" class="p-2 border border-outline-variant/30 rounded-lg opacity-40 cursor-not-allowed" disabled aria-disabled="true"><span class="material-symbols-outlined text-sm">chevron_right</span></button>
                  @else
                  <a href="{{ $kejadian->nextPageUrl() }}" class="p-2 border border-outline-variant/30 rounded-lg hover:bg-white hover:shadow-md transition-all inline-flex" rel="next"><span class="material-symbols-outlined text-sm">chevron_right</span></a>
                  @endif
               </div>
            </div>
         </div> -->
         <!-- Highlight Issue & Rekomendasi (data agregat dashboard + AI Gemini) -->
         <!-- <section class="overflow-hidden rounded-2xl border border-outline-variant/15 bg-white anchored-card" aria-labelledby="peer-highlight-heading">
            <div class="flex flex-col gap-3 border-b border-outline-variant/20 bg-gradient-to-r from-primary/[0.06] to-secondary/[0.04] px-6 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-8">
               <div>
                  <h3 id="peer-highlight-heading" class="font-headline text-xl font-extrabold tracking-tight text-on-background">Highlight Issue &amp; Rekomendasi</h3>
                  <p class="mt-1 text-[11px] text-on-surface-variant">Narasi ringkas mengikuti alur metrik di halaman ini (KPI, trend, evaluasi, insight, tabel). Sumber data = agregat aktual; teks disusun AI dari JSON tersebut.</p>
               </div>
               <div class="flex flex-wrap items-center gap-2">
                  <span id="peer-highlight-badge" class="hidden rounded-full border border-outline-variant/25 bg-white px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant"></span>
                  <button type="button" id="peer-highlight-refresh" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-[11px] font-bold text-primary transition-colors hover:bg-[#f8fafc]">
                     <span class="material-symbols-outlined text-base" data-icon="refresh">refresh</span>
                     Muat ulang
                  </button>
               </div>
            </div>
            <div id="peer-highlight-loading" class="flex items-center gap-3 px-6 py-10 sm:px-8 text-on-surface-variant">
               <span class="material-symbols-outlined animate-spin text-2xl text-primary" style="animation-duration:1.1s" data-icon="progress_activity">progress_activity</span>
               <span class="text-sm font-medium">Menyusun ringkasan dari data dashboard…</span>
            </div>
            <div id="peer-highlight-content" class="hidden">
               <div class="overflow-x-auto">
                  <table class="w-full border-collapse text-left">
                     <thead>
                        <tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[10px] font-extrabold uppercase tracking-[0.12em] text-on-surface-variant">
                           <th class="w-[18%] px-4 py-3 align-top sm:px-6">Tema</th>
                           <th class="w-[41%] border-l border-outline-variant/15 px-4 py-3 align-top sm:px-6">Issue</th>
                           <th class="w-[41%] border-l border-outline-variant/15 px-4 py-3 align-top sm:px-6">Rekomendasi</th>
                        </tr>
                     </thead>
                     <tbody id="peer-highlight-tbody" class="divide-y divide-outline-variant/10 text-[12px] leading-relaxed text-on-surface"></tbody>
                  </table>
               </div>
               <p id="peer-highlight-meta" class="border-t border-outline-variant/15 px-6 py-3 text-[10px] text-on-surface-variant sm:px-8"></p>
            </div>
            <p id="peer-highlight-error" class="hidden px-6 py-6 text-sm font-medium text-error sm:px-8"></p>
         </section> -->
         <!-- Footer -->
         <footer class="flex flex-col sm:flex-row justify-between items-center py-10 border-t border-outline-variant/30 text-on-surface-variant">
            <div class="flex flex-col items-center sm:items-start gap-1">
               <span class="text-xs font-bold">© 2026 Site SMO</span>
               <span class="text-[10px] opacity-70">Empowering proactive safety through peer evaluation</span>
            </div>
            <div class="flex gap-8 mt-6 sm:mt-0">
               <a class="text-[11px] font-bold uppercase tracking-wider hover:text-primary transition-colors border-b-2 border-transparent hover:border-primary pb-0.5" href="#">Contact Admin</a>
               <a class="text-[11px] font-bold uppercase tracking-wider hover:text-primary transition-colors border-b-2 border-transparent hover:border-primary pb-0.5" href="#">Technical Support</a>
               <a class="text-[11px] font-bold uppercase tracking-wider hover:text-primary transition-colors border-b-2 border-transparent hover:border-primary pb-0.5" href="#">System Status</a>
            </div>
         </footer>
      </main>
      <!-- Modal pilih periode chart mingguan -->
      <div id="peer-weekly-period-modal" class="hidden fixed inset-0 z-[205] flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="peer-weekly-period-title">
         <div class="absolute inset-0 peer-weekly-period-backdrop cursor-pointer" aria-hidden="true"></div>
         <div class="relative z-10 w-full max-w-md rounded-2xl border border-outline-variant/20 bg-white p-6 shadow-xl">
            <h3 id="peer-weekly-period-title" class="font-headline mb-1 text-lg font-bold text-on-surface">Pilih periode chart</h3>
            <p class="mb-4 text-xs text-on-surface-variant">Semua data: agregasi per bulan (2025–2026). Atau pilih tahun lalu bulan — chart per minggu ISO dalam bulan tersebut.</p>
            <button type="button" id="peer-modal-all-data" class="peer-modal-all-data mb-4 w-full rounded-xl border py-3 text-sm font-bold transition-colors {{ ($chartPeriodMonth ?? false) ? 'border-outline-variant/20 bg-[#f8fafc] text-on-surface hover:bg-surface-container-high' : 'border-primary bg-primary text-white shadow-sm' }}">
               Semua data
            </button>
            <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Tahun</p>
            <div class="mb-4 flex gap-2" id="peer-modal-years">
               @foreach ([2025, 2026] as $y)
               <button type="button" class="peer-modal-year flex-1 rounded-lg border border-outline-variant/20 py-2.5 text-sm font-bold transition-colors {{ ($chartPeriodMonth ?? false) && $cy === $y ? 'bg-primary text-white shadow-sm' : 'bg-[#f8fafc] text-on-surface-variant hover:bg-surface-container-high' }}" data-year="{{ $y }}">{{ $y }}</button>
               @endforeach
            </div>
            <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Bulan</p>
            <div class="grid grid-cols-4 gap-1.5 sm:grid-cols-6" id="peer-modal-months">
               @foreach (range(1, 12) as $m)
               <button type="button" class="peer-modal-month rounded-lg border border-outline-variant/15 py-2 text-[10px] font-bold uppercase tracking-wide transition-colors {{ ($chartPeriodMonth ?? false) && $cm === $m ? 'bg-primary text-white shadow-sm' : 'bg-white text-on-surface-variant hover:bg-surface-container-high' }}" data-month="{{ $m }}">{{ $monthsShort[$m] }}</button>
               @endforeach
            </div>
            <div class="mt-6 flex justify-end gap-2">
               <button type="button" id="peer-weekly-period-cancel" class="rounded-xl px-4 py-2 text-xs font-bold text-on-surface-variant hover:bg-[#f1f5f9]">Batal</button>
               <button type="button" id="peer-weekly-period-apply" class="rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:opacity-95">Terapkan</button>
            </div>
         </div>
      </div>
      @php
         $peerSapModalStatic = [
            'weeks' => ['W12', 'W13', 'W14', 'W15'],
            'rowOrder' => ['COACHING', 'HAZARD', 'INSPEKSI', 'OBSERVASI', 'OBSERVASI AREA KRITIS'],
            'all' => [
               ['OBSERVASI AREA KRITIS', 'W15', 28477], ['OBSERVASI', 'W15', 16315], ['INSPEKSI', 'W15', 9365], ['HAZARD', 'W15', 10379], ['COACHING', 'W15', 6290],
               ['OBSERVASI AREA KRITIS', 'W14', 28181], ['OBSERVASI', 'W14', 14958], ['INSPEKSI', 'W14', 8767], ['HAZARD', 'W14', 9732], ['COACHING', 'W14', 6077],
               ['OBSERVASI AREA KRITIS', 'W13', 27058], ['OBSERVASI', 'W13', 13384], ['INSPEKSI', 'W13', 8242], ['HAZARD', 'W13', 9021], ['COACHING', 'W13', 6000],
               ['OBSERVASI AREA KRITIS', 'W12', 19835], ['OBSERVASI', 'W12', 12548], ['INSPEKSI', 'W12', 6871], ['HAZARD', 'W12', 7515], ['COACHING', 'W12', 4900],
            ],
            'bySite' => [
               'BMO 1' => [
                  ['OBSERVASI AREA KRITIS', 'W15', 4472], ['OBSERVASI', 'W15', 4754], ['INSPEKSI', 'W15', 1089], ['HAZARD', 'W15', 1201], ['COACHING', 'W15', 775],
                  ['OBSERVASI AREA KRITIS', 'W14', 4180], ['OBSERVASI', 'W14', 3882], ['INSPEKSI', 'W14', 986], ['HAZARD', 'W14', 1207], ['COACHING', 'W14', 710],
                  ['OBSERVASI AREA KRITIS', 'W13', 3986], ['OBSERVASI', 'W13', 3177], ['INSPEKSI', 'W13', 936], ['HAZARD', 'W13', 1105], ['COACHING', 'W13', 673],
                  ['OBSERVASI AREA KRITIS', 'W12', 3183], ['OBSERVASI', 'W12', 3443], ['INSPEKSI', 'W12', 804], ['HAZARD', 'W12', 918], ['COACHING', 'W12', 554],
               ],
               'BMO 2' => [
                  ['OBSERVASI AREA KRITIS', 'W15', 3952], ['OBSERVASI', 'W15', 1522], ['INSPEKSI', 'W15', 1479], ['HAZARD', 'W15', 1541], ['COACHING', 'W15', 1652],
                  ['OBSERVASI AREA KRITIS', 'W14', 4156], ['OBSERVASI', 'W14', 1432], ['INSPEKSI', 'W14', 1438], ['HAZARD', 'W14', 1527], ['COACHING', 'W14', 1564],
                  ['OBSERVASI AREA KRITIS', 'W13', 4135], ['OBSERVASI', 'W13', 1291], ['INSPEKSI', 'W13', 1400], ['HAZARD', 'W13', 1441], ['COACHING', 'W13', 1517],
                  ['OBSERVASI AREA KRITIS', 'W12', 3000], ['OBSERVASI', 'W12', 1198], ['INSPEKSI', 'W12', 1228], ['HAZARD', 'W12', 1279], ['COACHING', 'W12', 1311],
               ],
               'BMO 3' => [
                  ['OBSERVASI AREA KRITIS', 'W15', 1145], ['OBSERVASI', 'W15', 346], ['INSPEKSI', 'W15', 356], ['HAZARD', 'W15', 393], ['COACHING', 'W15', 213],
                  ['OBSERVASI AREA KRITIS', 'W14', 1223], ['OBSERVASI', 'W14', 229], ['INSPEKSI', 'W14', 314], ['HAZARD', 'W14', 368], ['COACHING', 'W14', 207],
                  ['OBSERVASI AREA KRITIS', 'W13', 1058], ['OBSERVASI', 'W13', 246], ['INSPEKSI', 'W13', 302], ['HAZARD', 'W13', 314], ['COACHING', 'W13', 244],
                  ['OBSERVASI AREA KRITIS', 'W12', 641], ['OBSERVASI', 'W12', 194], ['INSPEKSI', 'W12', 233], ['HAZARD', 'W12', 252], ['COACHING', 'W12', 186],
               ],
               'Eksplorasi' => [
                  ['OBSERVASI AREA KRITIS', 'W15', 452], ['OBSERVASI', 'W15', 623], ['INSPEKSI', 'W15', 55], ['HAZARD', 'W15', 22], ['COACHING', 'W15', 79],
                  ['OBSERVASI AREA KRITIS', 'W14', 435], ['OBSERVASI', 'W14', 714], ['INSPEKSI', 'W14', 54], ['HAZARD', 'W14', 17], ['COACHING', 'W14', 82],
                  ['OBSERVASI AREA KRITIS', 'W13', 390], ['OBSERVASI', 'W13', 700], ['INSPEKSI', 'W13', 56], ['HAZARD', 'W13', 24], ['COACHING', 'W13', 82],
                  ['OBSERVASI AREA KRITIS', 'W12', 255], ['OBSERVASI', 'W12', 479], ['INSPEKSI', 'W12', 52], ['HAZARD', 'W12', 12], ['COACHING', 'W12', 62],
               ],
               'GMO' => [
                  ['OBSERVASI AREA KRITIS', 'W15', 7061], ['OBSERVASI', 'W15', 2549], ['INSPEKSI', 'W15', 2412], ['HAZARD', 'W15', 2480], ['COACHING', 'W15', 1007],
                  ['OBSERVASI AREA KRITIS', 'W14', 6745], ['OBSERVASI', 'W14', 2367], ['INSPEKSI', 'W14', 2262], ['HAZARD', 'W14', 2373], ['COACHING', 'W14', 1051],
                  ['OBSERVASI AREA KRITIS', 'W13', 6442], ['OBSERVASI', 'W13', 1810], ['INSPEKSI', 'W13', 2092], ['HAZARD', 'W13', 2199], ['COACHING', 'W13', 1050],
                  ['OBSERVASI AREA KRITIS', 'W12', 4673], ['OBSERVASI', 'W12', 2272], ['INSPEKSI', 'W12', 1664], ['HAZARD', 'W12', 1725], ['COACHING', 'W12', 756],
               ],
               'HO' => [
                  ['OBSERVASI AREA KRITIS', 'W15', 464], ['OBSERVASI', 'W15', 285], ['INSPEKSI', 'W15', 177], ['HAZARD', 'W15', 167], ['COACHING', 'W15', 180],
                  ['OBSERVASI AREA KRITIS', 'W14', 431], ['OBSERVASI', 'W14', 250], ['INSPEKSI', 'W14', 137], ['HAZARD', 'W14', 137], ['COACHING', 'W14', 137],
                  ['OBSERVASI AREA KRITIS', 'W13', 475], ['OBSERVASI', 'W13', 252], ['INSPEKSI', 'W13', 115], ['HAZARD', 'W13', 112], ['COACHING', 'W13', 123],
                  ['OBSERVASI AREA KRITIS', 'W12', 347], ['OBSERVASI', 'W12', 207], ['INSPEKSI', 'W12', 127], ['HAZARD', 'W12', 115], ['COACHING', 'W12', 124],
               ],
               'LMO' => [
                  ['OBSERVASI AREA KRITIS', 'W15', 5310], ['OBSERVASI', 'W15', 3762], ['INSPEKSI', 'W15', 2806], ['HAZARD', 'W15', 2172], ['COACHING', 'W15', 1172],
                  ['OBSERVASI AREA KRITIS', 'W14', 5324], ['OBSERVASI', 'W14', 3633], ['INSPEKSI', 'W14', 2655], ['HAZARD', 'W14', 2010], ['COACHING', 'W14', 1247],
                  ['OBSERVASI AREA KRITIS', 'W13', 5045], ['OBSERVASI', 'W13', 3460], ['INSPEKSI', 'W13', 2510], ['HAZARD', 'W13', 1868], ['COACHING', 'W13', 1216],
                  ['OBSERVASI AREA KRITIS', 'W12', 3761], ['OBSERVASI', 'W12', 2781], ['INSPEKSI', 'W12', 2063], ['HAZARD', 'W12', 1601], ['COACHING', 'W12', 992],
               ],
               'Marine' => [
                  ['OBSERVASI AREA KRITIS', 'W15', 367], ['OBSERVASI', 'W15', 878], ['INSPEKSI', 'W15', 431], ['HAZARD', 'W15', 1796], ['COACHING', 'W15', 299],
                  ['OBSERVASI AREA KRITIS', 'W14', 340], ['OBSERVASI', 'W14', 862], ['INSPEKSI', 'W14', 388], ['HAZARD', 'W14', 1507], ['COACHING', 'W14', 215],
                  ['OBSERVASI AREA KRITIS', 'W13', 321], ['OBSERVASI', 'W13', 888], ['INSPEKSI', 'W13', 313], ['HAZARD', 'W13', 1378], ['COACHING', 'W13', 220],
                  ['OBSERVASI AREA KRITIS', 'W12', 259], ['OBSERVASI', 'W12', 667], ['INSPEKSI', 'W12', 251], ['HAZARD', 'W12', 1159], ['COACHING', 'W12', 180],
               ],
               'SMO' => [
                  ['OBSERVASI AREA KRITIS', 'W15', 5254], ['OBSERVASI', 'W15', 1596], ['INSPEKSI', 'W15', 560], ['HAZARD', 'W15', 607], ['COACHING', 'W15', 913],
                  ['OBSERVASI AREA KRITIS', 'W14', 5347], ['OBSERVASI', 'W14', 1589], ['INSPEKSI', 'W14', 533], ['HAZARD', 'W14', 586], ['COACHING', 'W14', 864],
                  ['OBSERVASI AREA KRITIS', 'W13', 5206], ['OBSERVASI', 'W13', 1560], ['INSPEKSI', 'W13', 518], ['HAZARD', 'W13', 580], ['COACHING', 'W13', 874],
                  ['OBSERVASI AREA KRITIS', 'W12', 3716], ['OBSERVASI', 'W12', 1307], ['INSPEKSI', 'W12', 449], ['HAZARD', 'W12', 454], ['COACHING', 'W12', 735],
               ],
            ],
         ];
         $peerPengawasanBerjarakModalStatic = [
            'weeks' => ['W12', 'W13', 'W14', 'W15'],
            'rowOrder' => ['Real Time', 'Post Event', 'Pengawasan Langsung'],
            'all' => [
               ['Real Time', 'W15', 21086], ['Post Event', 'W15', 13989], ['Pengawasan Langsung', 'W15', 35751],
               ['Real Time', 'W14', 20666], ['Post Event', 'W14', 13485], ['Pengawasan Langsung', 'W14', 33564],
               ['Real Time', 'W13', 18577], ['Post Event', 'W13', 12277], ['Pengawasan Langsung', 'W13', 32851],
               ['Real Time', 'W12', 14559], ['Post Event', 'W12', 10388], ['Pengawasan Langsung', 'W12', 26722],
            ],
            'bySite' => [
               'BMO 1' => [
                  ['Real Time', 'W15', 4291], ['Post Event', 'W15', 1038], ['Pengawasan Langsung', 'W15', 6962],
                  ['Real Time', 'W14', 4403], ['Post Event', 'W14', 1076], ['Pengawasan Langsung', 'W14', 5486],
                  ['Real Time', 'W13', 3567], ['Post Event', 'W13', 1043], ['Pengawasan Langsung', 'W13', 5267],
                  ['Real Time', 'W12', 3135], ['Post Event', 'W12', 904], ['Pengawasan Langsung', 'W12', 4863],
               ],
               'BMO 2' => [
                  ['Real Time', 'W15', 2410], ['Post Event', 'W15', 1865], ['Pengawasan Langsung', 'W15', 5871],
                  ['Real Time', 'W14', 2366], ['Post Event', 'W14', 1860], ['Pengawasan Langsung', 'W14', 5891],
                  ['Real Time', 'W13', 2231], ['Post Event', 'W13', 1704], ['Pengawasan Langsung', 'W13', 5849],
                  ['Real Time', 'W12', 1859], ['Post Event', 'W12', 1397], ['Pengawasan Langsung', 'W12', 4760],
               ],
               'BMO 3' => [
                  ['Real Time', 'W15', 1734], ['Post Event', 'W15', 171], ['Pengawasan Langsung', 'W15', 548],
                  ['Real Time', 'W14', 1622], ['Post Event', 'W14', 178], ['Pengawasan Langsung', 'W14', 541],
                  ['Real Time', 'W13', 1510], ['Post Event', 'W13', 159], ['Pengawasan Langsung', 'W13', 495],
                  ['Real Time', 'W12', 1043], ['Post Event', 'W12', 101], ['Pengawasan Langsung', 'W12', 362],
               ],
               'Eksplorasi' => [
                  ['Real Time', 'W15', 151], ['Post Event', 'W15', 297], ['Pengawasan Langsung', 'W15', 783],
                  ['Real Time', 'W14', 138], ['Post Event', 'W14', 287], ['Pengawasan Langsung', 'W14', 877],
                  ['Real Time', 'W13', 59], ['Post Event', 'W13', 225], ['Pengawasan Langsung', 'W13', 968],
                  ['Real Time', 'W12', 39], ['Post Event', 'W12', 181], ['Pengawasan Langsung', 'W12', 640],
               ],
               'GMO' => [
                  ['Real Time', 'W15', 3492], ['Post Event', 'W15', 4329], ['Pengawasan Langsung', 'W15', 7688],
                  ['Real Time', 'W14', 3357], ['Post Event', 'W14', 4068], ['Pengawasan Langsung', 'W14', 7373],
                  ['Real Time', 'W13', 3031], ['Post Event', 'W13', 3235], ['Pengawasan Langsung', 'W13', 7327],
                  ['Real Time', 'W12', 2103], ['Post Event', 'W12', 3402], ['Pengawasan Langsung', 'W12', 5585],
               ],
               'HO' => [
                  ['Real Time', 'W15', 134], ['Post Event', 'W15', 330], ['Pengawasan Langsung', 'W15', 809],
                  ['Real Time', 'W14', 103], ['Post Event', 'W14', 325], ['Pengawasan Langsung', 'W14', 664],
                  ['Real Time', 'W13', 119], ['Post Event', 'W13', 306], ['Pengawasan Langsung', 'W13', 652],
                  ['Real Time', 'W12', 91], ['Post Event', 'W12', 247], ['Pengawasan Langsung', 'W12', 582],
               ],
               'LMO' => [
                  ['Real Time', 'W15', 4748], ['Post Event', 'W15', 3755], ['Pengawasan Langsung', 'W15', 6719],
                  ['Real Time', 'W14', 4459], ['Post Event', 'W14', 3609], ['Pengawasan Langsung', 'W14', 6801],
                  ['Real Time', 'W13', 4027], ['Post Event', 'W13', 3588], ['Pengawasan Langsung', 'W13', 6484],
                  ['Real Time', 'W12', 3322], ['Post Event', 'W12', 2641], ['Pengawasan Langsung', 'W12', 5235],
               ],
               'Marine' => [
                  ['Real Time', 'W15', 1038], ['Post Event', 'W15', 301], ['Pengawasan Langsung', 'W15', 2432],
                  ['Real Time', 'W14', 1037], ['Post Event', 'W14', 183], ['Pengawasan Langsung', 'W14', 2092],
                  ['Real Time', 'W13', 907], ['Post Event', 'W13', 244], ['Pengawasan Langsung', 'W13', 1969],
                  ['Real Time', 'W12', 721], ['Post Event', 'W12', 148], ['Pengawasan Langsung', 'W12', 1647],
               ],
               'SMO' => [
                  ['Real Time', 'W15', 3088], ['Post Event', 'W15', 1903], ['Pengawasan Langsung', 'W15', 3939],
                  ['Real Time', 'W14', 3181], ['Post Event', 'W14', 1899], ['Pengawasan Langsung', 'W14', 3839],
                  ['Real Time', 'W13', 3126], ['Post Event', 'W13', 1773], ['Pengawasan Langsung', 'W13', 3839],
                  ['Real Time', 'W12', 2246], ['Post Event', 'W12', 1367], ['Pengawasan Langsung', 'W12', 3048],
               ],
            ],
         ];
         $peerPelaporModalStatic = [
            'weeks' => ['W12', 'W13', 'W14', 'W15'],
            'all' => [
               ['W12', 3484], ['W13', 3622], ['W14', 3773], ['W15', 3904],
            ],
            'bySite' => [
               'BMO 1' => [['W12', 498], ['W13', 526], ['W14', 551], ['W15', 603]],
               'BMO 2' => [['W12', 586], ['W13', 619], ['W14', 627], ['W15', 662]],
               'BMO 3' => [['W12', 98], ['W13', 103], ['W14', 120], ['W15', 122]],
               'Eksplorasi' => [['W12', 68], ['W13', 76], ['W14', 78], ['W15', 80]],
               'GMO' => [['W12', 537], ['W13', 563], ['W14', 593], ['W15', 589]],
               'HO' => [['W12', 171], ['W13', 176], ['W14', 181], ['W15', 205]],
               'LMO' => [['W12', 717], ['W13', 722], ['W14', 767], ['W15', 793]],
               'Marine' => [['W12', 653], ['W13', 707], ['W14', 734], ['W15', 751]],
               'SMO' => [['W12', 367], ['W13', 376], ['W14', 388], ['W15', 408]],
            ],
         ];
      @endphp
      <script>window.PEER_SAP_MODAL_STATIC = @json($peerSapModalStatic);</script>
      <script>window.PEER_PENGAWASAN_BERJARAK_MODAL_STATIC = @json($peerPengawasanBerjarakModalStatic);</script>
      <script>window.PEER_PELOPOR_MODAL_STATIC = @json($peerPelaporModalStatic);</script>
      <!-- Modal statistik kategori deviasi (dari kartu KPI Hazard Reporting / deviasi) -->
      <div id="peer-deviation-category-modal" class="hidden fixed inset-0 z-[206] flex items-center justify-center p-3 sm:p-6 bg-black/40 backdrop-blur-sm" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="peer-deviation-category-title">
         <div class="absolute inset-0 cursor-pointer peer-deviation-category-backdrop" aria-hidden="true"></div>
         <div class="relative z-10 flex max-h-[min(94vh,960px)] w-full max-w-[min(96vw,1280px)] flex-col overflow-hidden rounded-2xl border border-outline-variant/20 bg-white text-on-surface shadow-xl">
            <div class="flex shrink-0 flex-wrap items-start justify-between gap-3 border-b border-outline-variant/20 px-4 py-3 sm:px-6 sm:py-4">
               <div class="min-w-0 flex-1">
                  <h2 id="peer-deviation-category-title" class="font-headline text-lg font-bold text-on-surface sm:text-xl">Hazard Reporting, SAP, Pengawasan Berjarak &amp; Pelapor</h2>
                  <p class="mt-1 text-xs text-on-surface-variant">{{ ($chartPeriodMonth ?? false) ? 'Periode sesuai filter chart (tanggal temuan).' : 'Seluruh data kejadian.' }}</p>
                  <p id="peer-deviation-modal-site-label" class="mt-1 text-[10px] font-semibold text-primary">Site: All Site</p>
               </div>
               <div class="flex shrink-0 flex-wrap items-center gap-2 sm:gap-3">
                  <div class="flex items-center gap-2">
                     <label for="peer-deviation-site-filter" class="whitespace-nowrap text-[10px] font-semibold text-slate-600">Site</label>
                     <select id="peer-deviation-site-filter" class="min-w-[8.5rem] rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-[11px] font-semibold text-slate-800 shadow-sm">
                        <option value="all">All Site</option>
                        <option value="bmo-1">BMO 1</option>
                        <option value="bmo-2">BMO 2</option>
                        <option value="bmo-3">BMO 3</option>
                        <option value="eksplorasi">Eksplorasi</option>
                        <option value="gmo">GMO</option>
                        <option value="ho">HO</option>
                        <option value="lmo">LMO</option>
                        <option value="marine">Marine</option>
                        <option value="smo">SMO</option>
                     </select>
                  </div>
                  <button type="button" id="peer-deviation-category-close" class="rounded-lg p-2 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-on-surface" aria-label="Tutup">
                     <span class="material-symbols-outlined text-2xl" data-icon="close">close</span>
                  </button>
               </div>
            </div>
            <div class="min-h-0 flex-1 overflow-y-auto px-4 py-4 sm:px-6">
               <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                  <div class="rounded-xl border border-outline-variant/20 bg-white p-3 shadow-sm">
                     <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Grafik SAP by Sumber Data</p>
                     <p class="mt-0.5 text-[9px] text-on-surface-variant/90">Batang berkelompok per minggu: COACHING, HAZARD, INSPEKSI, OBSERVASI, OBSERVASI AREA KRITIS (sesuai filter site).</p>
                     <div class="relative mt-2 h-56 w-full min-h-[14rem] sm:h-64">
                        <canvas
                           id="peer-deviation-modal-sap-chart"
                           class="peer-deviation-sap-chart max-h-full w-full"
                           data-sap-labels='@json($peerSapModalStatic['weeks'])'
                           data-sap-series="[]"
                        ></canvas>
                     </div>
                  </div>
                  <div class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
                     <div class="border-b border-slate-300 bg-[#cfe8d9] px-3 py-2">
                        <p class="text-[11px] font-bold text-slate-800">1. Jumlah Laporan SAP by Sumber Data</p>
                     </div>
                     <div class="overflow-x-auto">
                        <table class="w-full min-w-[420px] border-collapse text-[11px] text-slate-800">
                           <thead>
                              <tr class="border-b border-slate-300">
                                 <th class="bg-[#f3f4f6] px-2 py-2 text-left font-bold text-slate-700">Sumberdata Jenis Laporan</th>
                                 @foreach ($peerSapModalStatic['weeks'] as $w)
                                    <th class="bg-[#f5f0c4] px-2 py-2 text-center font-bold tabular-nums text-slate-800">{{ $w }}</th>
                                 @endforeach
                              </tr>
                           </thead>
                           <tbody id="peer-deviation-sap-tbody" data-static-sap="1"></tbody>
                        </table>
                     </div>
                  </div>
               </div>
               <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-1">
                 
                  <div class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
                     <div class="border-b border-slate-300 bg-[#cfe8d9] px-3 py-2">
                        <p class="text-[11px] font-bold text-slate-800">2. Jumlah laporan pengawasan berjarak</p>
                     </div>
                     <div class="overflow-x-auto">
                        <table class="w-full min-w-[420px] border-collapse text-[11px] text-slate-800">
                           <thead>
                              <tr class="border-b border-slate-300">
                                 <th class="bg-[#f3f4f6] px-2 py-2 text-left font-bold text-slate-700">Tools pengawasan (group)</th>
                                 @foreach ($peerPengawasanBerjarakModalStatic['weeks'] as $w)
                                    <th class="bg-[#f5f0c4] px-2 py-2 text-center font-bold tabular-nums text-slate-800">{{ $w }}</th>
                                 @endforeach
                              </tr>
                           </thead>
                           <tbody id="peer-deviation-pjb-tbody" data-static-pjb="1"></tbody>
                        </table>
                     </div>
                  </div>
               </div>
               <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
                  <div class="rounded-xl border border-outline-variant/20 bg-white p-3 shadow-sm">
                     <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Jumlah pelapor (distinct)</p>
                     <p class="mt-0.5 text-[9px] text-on-surface-variant/90">ISO week tanggal pelaporan beDraft · distinct <span class="font-semibold">sid pelapor</span> · bar chart.</p>
                     <div class="relative mt-2 h-48 w-full min-h-[12rem] sm:h-52">
                        <canvas
                           id="peer-deviation-modal-pelapor-chart"
                           class="peer-deviation-pelapor-chart max-h-full w-full"
                           data-static-pelapor="1"
                           data-pelapor-weeks='@json($peerPelaporModalStatic['weeks'])'
                           data-pelapor-values="[]"
                        ></canvas>
                     </div>
                  </div>
                  <div class="overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
                     <div class="border-b border-slate-300 bg-[#cfe8d9] px-3 py-2">
                        <p class="text-[11px] font-bold text-slate-800">3. Jumlah pelapor (distinct sid)</p>
                     </div>
                     <div class="overflow-x-auto">
                        <table class="w-full min-w-[280px] border-collapse text-[11px] text-slate-800">
                           <thead>
                              <tr class="border-b border-slate-300">
                                 <th class="bg-[#f3f4f6] px-2 py-2 text-left font-bold text-slate-700">ISO week (pelaporan beDraft)</th>
                                 <th class="bg-[#f5f0c4] px-2 py-2 text-center font-bold text-slate-800">Distinct pelapor</th>
                              </tr>
                           </thead>
                           <tbody id="peer-deviation-pelapor-tbody" data-static-pelapor="1"></tbody>
                        </table>
                     </div>
                  </div>
               </div>
               <p class="mt-4 text-[10px] leading-relaxed text-on-surface-variant">Konten statik pada modal ini (SAP, pengawasan berjarak, pelapor) mengikuti set data yang Anda berikan dan berubah sesuai filter site.</p>
            </div>
         </div>
      </div>
      <!-- Modal detail metrik Pelaksanaan Comply -->
      <div id="peer-compliance-detail-modal" class="hidden fixed inset-0 z-[207] flex items-center justify-center p-4 sm:p-6 bg-black/40 backdrop-blur-sm" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="peer-compliance-detail-title">
         <div class="absolute inset-0 cursor-pointer peer-compliance-detail-backdrop" aria-hidden="true"></div>
         <div class="relative z-10 flex max-h-[min(92vh,900px)] w-full max-w-5xl flex-col overflow-hidden rounded-2xl border border-outline-variant/20 bg-white text-on-surface shadow-xl">
            <div class="flex shrink-0 items-start justify-between gap-3 border-b border-outline-variant/20 px-5 py-4 sm:px-6">
               <div>
                  <h2 id="peer-compliance-detail-title" class="font-headline text-lg font-bold text-on-surface">Pelaksanaan Comply</h2>
                  <p id="peer-compliance-modal-period" class="mt-1 text-xs text-on-surface-variant">{{ ($chartPeriodMonth ?? false) ? 'Periode: filter chart (tanggal temuan dalam bulan yang dipilih).' : 'Periode: seluruh data kejadian (tanpa filter bulan).' }}</p>
               </div>
               <button type="button" id="peer-compliance-detail-close" class="rounded-lg p-2 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-on-surface" aria-label="Tutup">
                  <span class="material-symbols-outlined text-2xl" data-icon="close">close</span>
               </button>
            </div>
            <div class="min-h-0 flex-1 overflow-y-auto bg-[#f8fafc] px-4 py-4 sm:px-6">
               <div class="mx-auto grid max-w-[1380px] grid-cols-1 gap-3 xl:grid-cols-12">
                  <div class="space-y-3 xl:col-span-4">
                     <section class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                        <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                           <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">Hazards Evaluation</h3>
                        </div>
                        <div class="overflow-x-auto px-3 py-3">
                           <table class="w-full min-w-[420px] border-collapse text-[11px] text-slate-700">
                              <tbody>
                                 <tr class="border-b border-slate-200">
                                    <td class="w-40 border-r border-slate-200 bg-slate-50 px-2 py-2 font-semibold">Hazard Reporting</td>
                                    <td class="px-2 py-2 text-center tabular-nums">20,595</td>
                                    <td class="px-2 py-2 text-center tabular-nums">15,543</td>
                                    <td class="px-2 py-2 text-center tabular-nums">17,861</td>
                                    <td class="px-2 py-2 text-center tabular-nums">19,570</td>
                                 </tr>
                                 <tr class="border-b border-slate-200">
                                    <td class="border-r border-slate-200 bg-slate-50 px-2 py-2 font-semibold">To Be Concerned High Risk Hazards</td>
                                    <td class="px-2 py-2 text-center tabular-nums">7,600</td>
                                    <td class="px-2 py-2 text-center tabular-nums">5,670</td>
                                    <td class="px-2 py-2 text-center tabular-nums">5,754</td>
                                    <td class="px-2 py-2 text-center tabular-nums">6,237</td>
                                 </tr>
                                 <tr>
                                    <td class="border-r border-slate-200 bg-slate-50 px-2 py-2 font-semibold">To Be Concerned High Risk Hazards Blindspot</td>
                                    <td class="px-2 py-2 text-center tabular-nums">42</td>
                                    <td class="px-2 py-2 text-center tabular-nums">26</td>
                                    <td class="px-2 py-2 text-center tabular-nums">8</td>
                                    <td class="px-2 py-2 text-center tabular-nums">21</td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </section>
                     <section class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                        <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                           <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">Golden Rules Hazard</h3>
                        </div>
                        <div class="p-3">
                           <div class="rounded-md border border-slate-200 bg-slate-50 p-3">
                              <p class="text-[10px] font-bold uppercase text-slate-500">Total Laporan</p>
                              <p class="mt-1 text-4xl font-extrabold tabular-nums text-slate-900">533</p>
                              <p class="text-sm text-slate-600">1 Valid GR Reports</p>
                           </div>
                        </div>
                     </section>
                  </div>
                  <div class="space-y-3 xl:col-span-5">
                     <section class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                        <div class="flex items-center justify-between border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                           <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">To Be Concerned Highrisk Hazard</h3>
                           <div class="text-[10px] font-bold text-slate-700">BC | MK</div>
                        </div>
                        <div class="hide-scrollbar overflow-x-auto px-3 py-3">
                           <table class="w-full min-w-[740px] border-collapse text-[11px] text-slate-700">
                              <tbody>
                                 <tr class="border-b border-slate-200">
                                    <td class="w-56 bg-slate-50 px-2 py-2 font-semibold">To Be Concerned Hazard L4W</td>
                                    <td class="px-2 py-2 text-center tabular-nums">7,600</td>
                                    <td class="px-2 py-2 text-center tabular-nums">5,670</td>
                                    <td class="px-2 py-2 text-center tabular-nums">5,754</td>
                                    <td class="px-2 py-2 text-center tabular-nums">6,237</td>
                                 </tr>
                                 <tr class="border-b border-slate-200">
                                    <td class="bg-slate-50 px-2 py-2 font-semibold">Avg YTD'26 : 4,566</td>
                                    <td colspan="4" class="px-2 py-2 text-right text-[10px] text-slate-500">GMO | LMO</td>
                                 </tr>
                                 <tr class="border-b border-slate-200"><td class="bg-slate-50 px-2 py-2">1. Deviasi pengoperasian kendaraan/unit</td><td class="px-2 py-2 text-center tabular-nums">1,669</td><td class="px-2 py-2 text-center tabular-nums">1,210</td><td class="px-2 py-2 text-center tabular-nums">1,331</td><td class="px-2 py-2 text-center tabular-nums">1,876</td></tr>
                                 <tr class="border-b border-slate-200"><td class="bg-slate-50 px-2 py-2">2. Deviasi penggunaan APD</td><td class="px-2 py-2 text-center tabular-nums">453</td><td class="px-2 py-2 text-center tabular-nums">303</td><td class="px-2 py-2 text-center tabular-nums">330</td><td class="px-2 py-2 text-center tabular-nums">356</td></tr>
                                 <tr class="border-b border-slate-200"><td class="bg-slate-50 px-2 py-2">3. Posisi pekerja pada area tidak aman</td><td class="px-2 py-2 text-center tabular-nums">482</td><td class="px-2 py-2 text-center tabular-nums">293</td><td class="px-2 py-2 text-center tabular-nums">215</td><td class="px-2 py-2 text-center tabular-nums">271</td></tr>
                                 <tr><td class="bg-slate-50 px-2 py-2">4. Deviasi loading/dumping</td><td class="px-2 py-2 text-center tabular-nums">558</td><td class="px-2 py-2 text-center tabular-nums">489</td><td class="px-2 py-2 text-center tabular-nums">512</td><td class="px-2 py-2 text-center tabular-nums">553</td></tr>
                              </tbody>
                           </table>
                        </div>
                     </section>
                     <section class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                        <div class="grid grid-cols-3 border-b border-slate-200 text-[10px] font-bold uppercase">
                           <div class="bg-emerald-700 px-3 py-2 text-white">Tools Pengamatan</div>
                           <div class="bg-slate-100 px-3 py-2 text-slate-700">Perusahaan Pelapor</div>
                           <div class="bg-blue-600 px-3 py-2 text-white">BC | MK</div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 p-3 text-[11px]">
                           <div class="rounded-md border border-slate-200 bg-slate-50 p-2 text-center"><p class="font-semibold text-slate-500">Langsung</p><p class="mt-1 text-xl font-extrabold text-slate-800">49%</p></div>
                           <div class="rounded-md border border-slate-200 bg-slate-50 p-2 text-center"><p class="font-semibold text-slate-500">Berjarak</p><p class="mt-1 text-xl font-extrabold text-slate-800">51%</p></div>
                           <div class="rounded-md border border-slate-200 bg-slate-50 p-2 text-center"><p class="font-semibold text-slate-500">BC 14% / MK 86%</p></div>
                        </div>
                     </section>
                  </div>
                  <div class="space-y-3 xl:col-span-3">
                     <section class="rounded-lg border border-slate-300 bg-white">
                        <div class="space-y-0 text-[11px]">
                           <div class="flex items-center justify-between border-b border-slate-200 px-3 py-2"><p class="font-bold text-slate-800">TBC Workshop</p><p class="text-slate-600">Avg YTD'26 : <span class="font-semibold">676</span></p></div>
                           <div class="flex items-center justify-between border-b border-slate-200 px-3 py-2"><p class="font-bold text-slate-800">TBC Drill &amp; Blast</p><p class="text-slate-600">Avg YTD'26 : <span class="font-semibold">121</span></p></div>
                           <div class="flex items-center justify-between border-b border-slate-200 px-3 py-2"><p class="font-bold text-slate-800">TBC Marine</p><p class="text-slate-600">Avg YTD'26 : <span class="font-semibold">196</span></p></div>
                           <div class="flex items-center justify-between border-b border-slate-200 px-3 py-2"><p class="font-bold text-slate-800">TBC Eksplorasi</p><p class="text-slate-600">Avg YTD'26 : <span class="font-semibold">36</span></p></div>
                           <div class="flex items-center justify-between border-b border-slate-200 px-3 py-2"><p class="font-bold text-slate-800">TBC HO - Transportasi Massal</p><p class="text-slate-600">Avg YTD'26 : <span class="font-semibold">64</span></p></div>
                           <div class="flex items-center justify-between border-b border-slate-200 px-3 py-2"><p class="font-bold text-slate-800">TBC Geotechnic</p><p class="text-slate-600">Avg YTD'26 : <span class="font-semibold">364</span></p></div>
                           <div class="flex items-center justify-between px-3 py-2"><p class="font-bold text-slate-800">TBC Area Project</p><p class="text-slate-600">Avg YTD'26 : <span class="font-semibold">379</span></p></div>
                        </div>
                     </section>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- Modal ringkasan evaluasi data -->
      <div id="peer-evaluation-summary-modal" class="hidden fixed inset-0 z-[204] flex items-center justify-center p-4 sm:p-6 bg-black/40 backdrop-blur-sm" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="peer-evaluation-summary-title">
         <div class="absolute inset-0 cursor-pointer peer-evaluation-summary-backdrop" aria-hidden="true"></div>
         <div class="relative z-10 flex max-h-[min(92vh,900px)] w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-outline-variant/20 bg-white text-on-surface shadow-xl">
            <div class="flex shrink-0 items-start justify-between gap-3 border-b border-outline-variant/20 px-5 py-4 sm:px-6">
               <div>
                  <h2 id="peer-evaluation-summary-title" class="font-headline text-lg font-bold text-on-surface">Detail evaluasi</h2>
                  <p id="peer-eval-modal-subtitle" class="mt-1 text-xs text-on-surface-variant">{{ ($chartPeriodMonth ?? false) ? ('Ringkasan per metrik & pelanggar repetitif · periode chart: '.($es['repeat_period_caption'] ?? '')) : 'Ringkasan per metrik & pelanggar repetitif · seluruh data' }}</p>
               </div>
               <button type="button" id="peer-evaluation-summary-close" class="rounded-lg p-2 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-on-surface" aria-label="Tutup">
                  <span class="material-symbols-outlined text-2xl" data-icon="close">close</span>
               </button>
            </div>
            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4 sm:px-6">
               <div id="peer-evaluation-modal-dynamic">
               @php
                  $es = $es ?? [];
                  $esRows = $esRows ?? ($es['rows'] ?? []);
               @endphp
               @foreach ($esRows as $row)
               <div class="mb-4 rounded-xl border border-outline-variant/20 bg-[#f8fafc] px-4 py-3 last:mb-0">
                  <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">{{ $row['metric'] ?? '—' }}</p>
                  @if(($row['key'] ?? '') === 'deviation_variety' && !empty($row['deviation_variety_detail']))
                  @php $dv = $row['deviation_variety_detail']; @endphp
                  <div class="mt-3 overflow-x-auto rounded-lg border border-outline-variant/20 bg-white">
                     <table class="w-full min-w-[280px] text-left text-[12px] text-on-surface">
                        <thead>
                           <tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">
                              <th class="px-3 py-2">Kategori deviasi</th>
                              <th class="whitespace-nowrap px-3 py-2 text-right">Kejadian</th>
                           </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                           @forelse (($dv['categories'] ?? []) as $cat)
                           <tr class="hover:bg-[#fafbfc]">
                              <td class="px-3 py-2">{{ $cat['kategori_deviasi'] ?? '—' }}</td>
                              <td class="px-3 py-2 text-right tabular-nums font-semibold">{{ (int) ($cat['jumlah'] ?? 0) }}</td>
                           </tr>
                           @empty
                           <tr>
                              <td colspan="2" class="px-3 py-4 text-center text-[11px] text-on-surface-variant">Belum ada data kategori.</td>
                           </tr>
                           @endforelse
                        </tbody>
                     </table>
                  </div>
                  @elseif(($row['key'] ?? '') === 'peer_correlation' && !empty($row['peer_correlation_detail']))
                  @php $pc = $row['peer_correlation_detail']; @endphp
                  <p class="mt-2 text-[11px] leading-relaxed text-on-surface">{{ $pc['definition'] ?? '' }}</p>
                  <div class="mt-3 overflow-x-auto rounded-lg border border-outline-variant/20 bg-white">
                     <table class="w-full min-w-[260px] text-left text-[12px] text-on-surface">
                        <thead>
                           <tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">
                              <th class="px-3 py-2">Metrik</th>
                              <th class="whitespace-nowrap px-3 py-2 text-right">Nilai</th>
                           </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                           <tr class="hover:bg-[#fafbfc]">
                              <td class="px-3 py-2">Total pasangan unik (semua kejadian)</td>
                              <td class="px-3 py-2 text-right tabular-nums font-semibold">{{ (int) ($pc['total_unique_pairs'] ?? 0) }}</td>
                           </tr>
                           <tr class="hover:bg-[#fafbfc]">
                              <td class="px-3 py-2">Pasangan dengan frekuensi ≥ 2</td>
                              <td class="px-3 py-2 text-right tabular-nums font-semibold">{{ (int) ($pc['pairs_with_freq_gte_2'] ?? 0) }}</td>
                           </tr>
                           <tr class="hover:bg-[#fafbfc]">
                              <td class="px-3 py-2">Frekuensi maksimal satu pasangan</td>
                              <td class="px-3 py-2 text-right tabular-nums font-semibold">{{ (int) ($pc['max_pair_frequency'] ?? 0) }} kejadian</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="mt-3 overflow-x-auto rounded-lg border border-outline-variant/20 bg-white">
                     <table class="w-full min-w-[320px] text-left text-[12px] text-on-surface">
                        <thead>
                           <tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">
                              <th class="px-3 py-2">Pelanggar (SID)</th>
                              <th class="px-3 py-2">Peer (SID)</th>
                              <th class="whitespace-nowrap px-3 py-2 text-right">Frekuensi</th>
                           </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                           @forelse (($pc['top_pairs'] ?? []) as $tp)
                           <tr class="hover:bg-[#fafbfc]">
                              <td class="px-3 py-2 font-mono text-[11px]">{{ $tp['pelanggar_sid'] ?? '—' }}</td>
                              <td class="px-3 py-2 font-mono text-[11px]">{{ $tp['peer_sid'] ?? '—' }}</td>
                              <td class="px-3 py-2 text-right tabular-nums font-semibold">{{ (int) ($tp['frekuensi'] ?? 0) }}×</td>
                           </tr>
                           @empty
                           <tr>
                              <td colspan="3" class="px-3 py-4 text-center text-[11px] text-on-surface-variant">Tidak ada pasangan berulang (frekuensi ≥ 2).</td>
                           </tr>
                           @endforelse
                        </tbody>
                     </table>
                  </div>
                  @elseif(!empty($row['detail_bullets']))
                  <ul class="mt-2 list-inside list-disc space-y-1 text-[13px] leading-relaxed text-on-surface">
                     @foreach ($row['detail_bullets'] as $b)
                     <li>{{ $b }}</li>
                     @endforeach
                  </ul>
                  @endif
                  @if(($row['key'] ?? '') === 'repeat_violator' && !empty($row['violators_detail']))
                  <div class="mt-4 overflow-x-auto rounded-lg border border-outline-variant/20 bg-white">
                     <table class="w-full min-w-[520px] text-left text-[12px] text-on-surface">
                        <thead>
                           <tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">
                              <th class="px-3 py-2">Nama</th>
                              <th class="px-3 py-2">SID</th>
                              <th class="px-3 py-2">Departemen</th>
                              <th class="px-3 py-2 text-right">Repetitif</th>
                           </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                           @foreach ($row['violators_detail'] as $v)
                           <tr class="peer-rv-toggle cursor-pointer transition-colors hover:bg-[#fafbfc]" role="button" tabindex="0" aria-expanded="false">
                              <td class="px-3 py-2 font-medium">{{ $v['nama'] ?? '—' }}</td>
                              <td class="px-3 py-2 font-mono text-[11px] text-on-surface-variant">{{ $v['sid'] ?? '—' }}</td>
                              <td class="px-3 py-2 text-on-surface-variant">{{ $v['departemen'] ?? '—' }}</td>
                              <td class="whitespace-nowrap px-3 py-2 text-right">
                                 <span class="inline-flex max-w-full flex-nowrap items-center justify-end gap-1">
                                    <span class="shrink-0 font-bold tabular-nums">{{ (int) ($v['kasus'] ?? 0) }}×</span>
                                    <span class="material-symbols-outlined peer-rv-chevron shrink-0 text-lg leading-none text-on-surface-variant" aria-hidden="true">expand_more</span>
                                 </span>
                              </td>
                           </tr>
                           <tr class="peer-rv-expand hidden bg-[#f8fafc]/90">
                              <td colspan="4" class="border-t border-outline-variant/10 px-4 py-3">
                                 <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">Tanggal &amp; kategori deviasi</p>
                                 <div class="overflow-x-auto rounded-lg border border-outline-variant/15 bg-white">
                                    <table class="w-full min-w-[320px] border-collapse text-left text-[12px] text-on-surface">
                                       <thead>
                                          <tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">
                                             <th class="whitespace-nowrap px-3 py-2">Tanggal</th>
                                             <th class="px-3 py-2">Kategori deviasi</th>
                                          </tr>
                                       </thead>
                                       <tbody class="divide-y divide-outline-variant/10">
                                          @foreach (($v['kejadian_list'] ?? []) as $kj)
                                          <tr>
                                             <td class="whitespace-nowrap px-3 py-2 font-medium tabular-nums">{{ $kj['tanggal_label'] ?? '—' }}</td>
                                             <td class="px-3 py-2 text-on-surface-variant">{{ $kj['kategori_deviasi'] ?? '—' }}</td>
                                          </tr>
                                          @endforeach
                                       </tbody>
                                    </table>
                                 </div>
                              </td>
                           </tr>
                           @endforeach
                        </tbody>
                     </table>
                  </div>
                  <p class="mt-2 text-[10px] text-on-surface-variant">Nama dari peserta pada kejadian terbaru per SID. Kolom Dept: gabungan unik. Detail: tanggal temuan &amp; kategori deviasi per kejadian.</p>
                  @endif
                  @if(($row['key'] ?? '') === 'recency' && !empty($row['recency_detail']))
                  @php $rd = $row['recency_detail']; @endphp
                  <p class="mt-2 text-[12px] leading-relaxed text-on-surface-variant">{{ $rd['metric_explanation'] ?? '' }}</p>
                  <div class="mt-3 overflow-x-auto rounded-lg border border-outline-variant/20 bg-white">
                     <table class="w-full min-w-[300px] text-left text-[12px] text-on-surface">
                        <tbody class="divide-y divide-outline-variant/10">
                           <tr>
                              <th class="w-[40%] whitespace-nowrap bg-[#f8fafc] px-3 py-2.5 align-top text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">Pelanggar</th>
                              <td class="px-3 py-2.5 font-medium">{{ $rd['pelanggar_nama'] ?? '—' }}</td>
                           </tr>
                           <tr>
                              <th class="whitespace-nowrap bg-[#f8fafc] px-3 py-2.5 align-top text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">SID</th>
                              <td class="px-3 py-2.5 font-mono text-[11px] text-on-surface-variant">{{ $rd['pelanggar_sid'] ?? '—' }}</td>
                           </tr>
                           <tr>
                              <th class="w-[40%] whitespace-nowrap bg-[#f8fafc] px-3 py-2.5 align-top text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">Temuan terbaru</th>
                              <td class="px-3 py-2.5 align-top">
                                 <div class="flex flex-col gap-1">
                                    <span class="font-medium">{{ $rd['latest']['tanggal_label'] ?? '—' }} <span class="font-mono text-[11px] text-on-surface-variant">#{{ (int) ($rd['latest']['kejadian_id'] ?? 0) }}</span></span>
                                    <span class="text-[11px] leading-snug text-on-surface-variant"><span class="font-semibold">Kategori deviasi:</span> {{ $rd['latest']['kategori_deviasi'] ?? '—' }}</span>
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th class="whitespace-nowrap bg-[#f8fafc] px-3 py-2.5 align-top text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">Temuan sebelumnya</th>
                              <td class="px-3 py-2.5 align-top">
                                 <div class="flex flex-col gap-1">
                                    <span class="font-medium">{{ $rd['previous']['tanggal_label'] ?? '—' }} <span class="font-mono text-[11px] text-on-surface-variant">#{{ (int) ($rd['previous']['kejadian_id'] ?? 0) }}</span></span>
                                    <span class="text-[11px] leading-snug text-on-surface-variant"><span class="font-semibold">Kategori deviasi:</span> {{ $rd['previous']['kategori_deviasi'] ?? '—' }}</span>
                                 </div>
                              </td>
                           </tr>
                           <tr>
                              <th class="whitespace-nowrap bg-[#f8fafc] px-3 py-2.5 align-top text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">Selisih kalender</th>
                              <td class="px-3 py-2.5 font-semibold tabular-nums">{{ (int) ($rd['gap_days'] ?? 0) }} hari</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <p class="mt-2 text-[10px] text-on-surface-variant">{{ $rd['footnote'] ?? '' }}</p>
                  @endif
               </div>
               @endforeach
               </div>
               <p class="mt-2 text-[10px] text-on-surface-variant">Sumber: tabel peer_pressure_kejadian_edukasi &amp; peer_pressure_peserta_edukasi. Metrik dihitung saat halaman dimuat.</p>
            </div>
         </div>
      </div>
      <!-- Modal TBC GENERAL — kartu kejadian (geser horizontal) -->
      <div id="peer-tbc-general-modal" class="hidden fixed inset-0 z-[208] flex items-center justify-center bg-slate-900/40 p-3 backdrop-blur-[2px] sm:p-6" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="peer-tbc-general-title">
         <div class="absolute inset-0 cursor-pointer peer-tbc-general-backdrop" aria-hidden="true"></div>
          <div class="relative z-10 flex max-h-[min(95vh,940px)] w-full max-w-[min(98vw,1380px)] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-[0_24px_80px_-30px_rgba(15,23,42,0.38)]">
            <div class="shrink-0 border-b border-slate-200 bg-white px-4 py-3 sm:px-6 sm:py-4">
               <div class="flex items-start justify-between gap-4">
                  <div class="min-w-0">
                     <div class="inline-flex items-center gap-2 rounded-md border border-[#e9b949] bg-[#f7c948] px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.08em] text-slate-700">
                        <span class="material-symbols-outlined text-sm">analytics</span>
                        TBC General Dashboard
                     </div>
                     <h2 id="peer-tbc-general-title" class="mt-2 font-headline text-base font-semibold tracking-tight text-slate-900 sm:text-lg">TBC General - To Be Concerned Highrisk Hazard</h2>
                     <p id="peer-tbc-general-subtitle" class="mt-1 max-w-4xl text-[10px] font-medium text-slate-500 sm:text-[11px]">Ringkasan visual 4 minggu terakhir: total laporan, valid concern, matriks hazard, dan pemetaan kategori prioritas per site.</p>
                  </div>
                  <button type="button" id="peer-tbc-general-close" class="rounded-lg border border-slate-200 bg-white p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900" aria-label="Tutup">
                     <span class="material-symbols-outlined text-2xl" data-icon="close">close</span>
                  </button>
               </div>
            </div>
            <div id="peer-tbc-general-loading" class="hidden flex flex-1 flex-col items-center justify-center gap-2 bg-white px-6 py-16" aria-live="polite">
               <span class="material-symbols-outlined animate-spin text-3xl text-primary/70" style="animation-duration:1s">progress_activity</span>
               <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">Memuat data tren TBC</p>
            </div>
            <div id="peer-tbc-general-error" class="hidden bg-white px-6 py-8 text-center text-[12px] font-medium text-red-600"></div>
            <div id="peer-tbc-general-body" class="min-h-0 flex-1 overflow-x-auto overflow-y-auto bg-[#fbfcfd] px-3 py-4 sm:px-5 sm:py-5">
               <div id="peer-tbc-general-content" class="mx-auto max-w-[1280px] space-y-4">
                  <div class="rounded-xl border border-slate-200 bg-white p-3 sm:p-4">
                     <div class="grid grid-cols-1 gap-3 xl:grid-cols-12">
                        <div class="space-y-3 xl:col-span-3">
                           <section class="rounded-lg border border-slate-200 bg-white">
                              <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                                 <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">Valid To Be Concerned Highrisk Hazard</h3>
                              </div>
                              <div class="px-3 py-2.5">
                                 <div class="relative h-36">
                                    <canvas id="peer-tbc-general-bar-canvas" aria-label="Grafik total laporan mingguan"></canvas>
                                 </div>
                              </div>
                           </section>
                          
                           <section class="rounded-lg border border-slate-200 bg-white p-3">
                              <p class="text-[11px] font-bold text-slate-600">Matriks Repetitive Laporan L4W</p>
                              <p class="mt-1 text-[10px] text-slate-400">Sebaran risiko di kuadran perhatian.</p>
                              <div class="mt-2 h-60 rounded-md border border-slate-200 bg-white p-2">
                                 <canvas id="peer-tbc-repetitive-scatter-canvas" aria-label="Grafik matriks repetitive laporan"></canvas>
                              </div>
                           </section>
                        </div>

                        <div class="space-y-3 xl:col-span-9">
                           <section class="rounded-lg border border-slate-200 bg-white">
                              <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                                 <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">To Be Concerned Highrisk Hazard</h3>
                              </div>
                              <div class="hide-scrollbar overflow-x-auto px-3 py-3">
                                 <div id="peer-tbc-category-cards" class="min-w-[920px] rounded-md border border-slate-200 bg-white" aria-label="Daftar kategori TBC"></div>
                                 <p id="peer-tbc-category-empty" class="hidden px-2 py-10 text-center text-sm font-medium text-slate-500">Belum ada data tren kategori.</p>
                              </div>
                           </section>

                           <section class="rounded-lg border border-slate-200 bg-white">
                              <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                                 <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">Tools Pengamatan &amp; Perusahaan Pelapor</h3>
                              </div>
                              <div class="grid grid-cols-1 gap-3 px-3 py-3 lg:grid-cols-3">
                                 <div class="rounded-md border border-slate-200 bg-slate-50 p-3 text-sm">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Langsung</p>
                                    <p class="mt-1 text-xl font-semibold text-slate-800">41%</p>
                                 </div>
                                 <div class="rounded-md border border-slate-200 bg-slate-50 p-3 text-sm">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Berjarak</p>
                                    <p class="mt-1 text-xl font-semibold text-slate-800">59%</p>
                                 </div>
                                 <div class="rounded-md border border-slate-200 bg-slate-50 p-3 text-sm">
                                    <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500" for="peer-tbc-category-site-select">Filter Site</label>
                                    <select id="peer-tbc-category-site-select" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-sm font-semibold text-slate-800 outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
                                       <option value="__all">Semua site (agregat)</option>
                                    </select>
                                 </div>
                              </div>
                           </section>
                           <section class="rounded-lg border border-slate-200 bg-white">
                              <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                                 <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">Need to Check TBC Last Week (Top 5 Kuadran I &amp; IV)</h3>
                              </div>
                              <div class="hide-scrollbar overflow-x-auto px-3 py-3">
                                 <div id="peer-tbc-need-check-top5" class="min-w-[1080px] rounded-md border border-slate-200 bg-white" aria-label="Top 5 kategori prioritas kuadran"></div>
                              </div>
                           </section>
                        </div>
                     </div>
                  </div>
                  <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                     <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                        <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">Dashboard TBC All Site (Tableau)</h3>
                        <p class="mt-0.5 text-[10px] font-medium text-slate-600">iDashboard · Tableau Embedding API v3</p>
                     </div>
                     <div class="w-full overflow-x-auto bg-slate-50/30 p-2 sm:p-4">
                        {{-- Tableau API dimuat sekali di blok Overview (629–644); src viz di-set saat modal dibuka (lazy) --}}
                        <tableau-viz
                           id="tableau-viz-tbc"
                           data-src="{{ $tableauTbcAllSiteVizSrc }}"
                           data-tableau-lazy="1"
                           width="1800"
                           height="1063"
                           hide-tabs
                           toolbar="bottom"
                           class="mx-auto block max-w-full"
                        ></tableau-viz>
                     </div>
                  </section>
               </div>
            </div>
         </div>
      </div>
      <div id="peer-blindspot-modal" class="hidden fixed inset-0 z-[209] flex items-center justify-center bg-slate-900/40 p-3 backdrop-blur-[2px] sm:p-6" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="peer-blindspot-title">
         <div class="absolute inset-0 cursor-pointer peer-blindspot-backdrop" aria-hidden="true"></div>
         <div class="relative z-10 flex max-h-[min(95vh,960px)] w-full max-w-[min(98vw,1460px)] flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-[0_24px_80px_-30px_rgba(15,23,42,0.38)]">
            <div class="shrink-0 border-b border-slate-200 bg-white px-4 py-3 sm:px-6 sm:py-4">
               <div class="flex items-start justify-between gap-4">
                  <div class="min-w-0">
                     <h2 id="peer-blindspot-title" class="font-headline text-base font-semibold tracking-tight text-slate-900 sm:text-lg">Blindspot Dashboard - To Be Concerned High Risk</h2>
                     <p class="mt-1 text-[10px] font-medium text-slate-500 sm:text-[11px]">Visualisasi total blindspot, leaderboard utama, dan prioritas tindak lanjut minggu terakhir.</p>
                  </div>
                  <button type="button" id="peer-blindspot-close" class="rounded-lg border border-slate-200 bg-white p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900" aria-label="Tutup">
                     <span class="material-symbols-outlined text-2xl" data-icon="close">close</span>
                  </button>
               </div>
            </div>
            <div id="peer-blindspot-loading" class="hidden flex flex-1 flex-col items-center justify-center gap-2 bg-white px-6 py-16" aria-live="polite">
               <span class="material-symbols-outlined animate-spin text-3xl text-emerald-600/70" style="animation-duration:1s">progress_activity</span>
               <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">Memuat data blindspot</p>
            </div>
            <div id="peer-blindspot-error" class="hidden bg-white px-6 py-8 text-center text-[12px] font-medium text-red-600"></div>
            <div id="peer-blindspot-body" class="min-h-0 flex-1 overflow-y-auto bg-[#fbfcfd] px-3 py-4 sm:px-5 sm:py-5">
               <div class="mx-auto max-w-[1360px] space-y-4">
                  <div class="grid grid-cols-1 gap-3 xl:grid-cols-12">
                  <div class="space-y-3 xl:col-span-6">
                     <section class="rounded-lg border border-slate-200 bg-white">
                        <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                           <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">Total Laporan BEATS</h3>
                        </div>
                        <div class="px-3 py-3">
                           <div id="peer-blindspot-summary" class="mb-2 grid grid-cols-2 gap-2"></div>
                           <div class="h-44 rounded-md border border-slate-200 bg-white p-2">
                              <canvas id="peer-blindspot-stack-canvas" aria-label="Grafik total blindspot per minggu"></canvas>
                           </div>
                        </div>
                     </section>
                     <section class="rounded-lg border border-slate-200 bg-white p-3">
                        <p class="text-[11px] font-bold text-slate-600">Matriks Repetitive Blindspot L4W</p>
                        <div class="mt-2 h-56 rounded-md border border-slate-200 bg-white p-2">
                           <canvas id="peer-blindspot-scatter-canvas" aria-label="Matriks repetitive blindspot"></canvas>
                        </div>
                     </section>
                     <section class="rounded-lg border border-slate-200 bg-white">
                        <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                           <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">Need to Check Blindspot (Top 5 Kuadran I &amp; IV)</h3>
                        </div>
                        <div class="hide-scrollbar overflow-x-auto px-3 py-3">
                           <div id="peer-blindspot-need-check" class="min-w-[840px] rounded-md border border-slate-200 bg-white"></div>
                        </div>
                     </section>
                  </div>
                  <div class="space-y-3 xl:col-span-6">
                     <section class="rounded-lg border border-slate-200 bg-white">
                        <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                           <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">Leaderboard by Site Blindspot</h3>
                        </div>
                        <div class="hide-scrollbar max-h-60 overflow-auto px-3 py-3">
                           <div id="peer-blindspot-leaderboard-site" class="min-w-[620px] rounded-md border border-slate-200 bg-white"></div>
                        </div>
                     </section>
                     <section class="rounded-lg border border-slate-200 bg-white">
                        <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                           <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">Top Leaderboard by Pelapor BC</h3>
                        </div>
                        <div class="hide-scrollbar max-h-56 overflow-auto px-3 py-3">
                           <div id="peer-blindspot-leaderboard-bc" class="min-w-[620px] rounded-md border border-slate-200 bg-white"></div>
                        </div>
                     </section>
                     <section class="rounded-lg border border-slate-200 bg-white">
                        <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                           <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">Top Leaderboard by Pelapor MK</h3>
                        </div>
                        <div class="hide-scrollbar max-h-56 overflow-auto px-3 py-3">
                           <div id="peer-blindspot-leaderboard-mk" class="min-w-[620px] rounded-md border border-slate-200 bg-white"></div>
                        </div>
                     </section>
                  </div>
                  </div>
                  <section class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                     <div class="border-b border-[#e9b949] bg-[#f7c948] px-3 py-2">
                        <h3 class="text-[11px] font-bold uppercase tracking-wide text-slate-700">Dashboard Blindspot (Tableau)</h3>
                        <p class="mt-0.5 text-[10px] font-medium text-slate-600">iDashboard · Tableau Embedding API v3</p>
                     </div>
                     <div class="w-full overflow-x-auto bg-slate-50/30 p-2 sm:p-4">
                        <tableau-viz
                           id="tableau-viz-blindspot"
                           data-src="{{ $tableauBlindspotVizSrc }}"
                           data-tableau-lazy="1"
                           width="2560"
                           height="1463"
                           hide-tabs
                           toolbar="bottom"
                           class="mx-auto block max-w-full"
                        ></tableau-viz>
                     </div>
                  </section>
               </div>
            </div>
         </div>
      </div>
      <!-- Detail modal (data dari API peer-pressure-edukasi.kejadian.detail) -->
      <div id="peer-pressure-detail-modal" class="hidden fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-6 bg-black/40 backdrop-blur-sm" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="peer-pressure-detail-title">
         <div class="absolute inset-0 peer-pressure-modal-backdrop" aria-hidden="true"></div>
         <div class="relative z-10 bg-white max-w-6xl w-full max-h-[min(96vh,1040px)] flex flex-col rounded-2xl border border-neutral-200/90 overflow-hidden">
            <div class="flex items-start justify-between gap-4 px-6 py-5 sm:px-8 sm:py-6 border-b border-neutral-200 shrink-0">
               <div>
                  <h2 id="peer-pressure-detail-title" class="font-headline font-semibold text-xl sm:text-2xl text-neutral-900 tracking-tight">Detail kejadian</h2>
                  <p class="text-[13px] text-neutral-500 mt-1">Kejadian edukasi lalu profil BeRecord pelanggar (jika tersedia)</p>
               </div>
               <button type="button" id="peer-pressure-detail-close" class="p-2 rounded-lg text-neutral-500 hover:text-neutral-900 hover:bg-neutral-100 transition-colors -mr-1" aria-label="Tutup">
                  <span class="material-symbols-outlined text-2xl" data-icon="close">close</span>
               </button>
            </div>
            <div id="peer-pressure-detail-body" class="overflow-y-auto px-6 py-6 sm:px-8 sm:py-8 flex-1 min-h-0 bg-neutral-50/80">
               <div id="peer-pressure-detail-loading" class="hidden flex flex-col items-center justify-center py-16 gap-3 text-on-surface-variant">
                  <span class="material-symbols-outlined text-4xl animate-pulse text-primary" data-icon="progress_activity">progress_activity</span>
                  <p class="text-xs font-bold uppercase tracking-widest">Memuat detail…</p>
               </div>
               <div id="peer-pressure-detail-error" class="hidden text-center py-12 text-error text-sm font-medium"></div>
               <div id="peer-pressure-detail-content" class="hidden max-w-full"></div>
            </div>
         </div>
      </div>
      <!-- Modal detail profiling pelanggar (klik baris Profiling Analysis) -->
      <div id="peer-pelanggar-profiling-modal" class="hidden fixed inset-0 z-[203] flex items-center justify-center p-4 sm:p-6 bg-black/40 backdrop-blur-sm" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="peer-pelanggar-profiling-title">
         <div class="absolute inset-0 peer-pelanggar-profiling-backdrop" aria-hidden="true"></div>
         <div class="relative z-10 flex max-h-[min(94vh,920px)] w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-outline-variant/20 bg-white shadow-xl">
            <div class="flex shrink-0 items-start justify-between gap-3 border-b border-outline-variant/15 px-5 py-4 sm:px-6">
               <div class="min-w-0 flex items-start gap-3">
                  <span class="material-symbols-outlined mt-0.5 shrink-0 text-primary text-2xl" data-icon="assignment">assignment</span>
                  <div class="min-w-0">
                     <h2 id="peer-pelanggar-profiling-title" class="font-headline text-base font-bold leading-snug text-on-surface sm:text-lg">Detail pelanggar</h2>
                     <p id="peer-pelanggar-profiling-subtitle" class="mt-0.5 truncate text-xs text-on-surface-variant font-mono"></p>
                  </div>
               </div>
               <button type="button" id="peer-pelanggar-profiling-close" class="shrink-0 rounded-xl p-2 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-on-surface" aria-label="Tutup">
                  <span class="material-symbols-outlined text-2xl" data-icon="close">close</span>
               </button>
            </div>
            <div id="peer-pelanggar-profiling-scroll" class="min-h-0 flex-1 overflow-y-auto px-5 py-4 sm:px-6">
               <div id="peer-pelanggar-profiling-loading" class="hidden flex flex-col items-center justify-center gap-3 py-16 text-on-surface-variant">
                  <span class="material-symbols-outlined animate-pulse text-4xl text-primary" data-icon="progress_activity">progress_activity</span>
                  <p class="text-xs font-bold uppercase tracking-widest">Memuat detail profiling…</p>
               </div>
               <div id="peer-pelanggar-profiling-error" class="hidden rounded-xl border border-error/25 bg-error/5 px-4 py-3 text-sm text-error"></div>
               <div id="peer-pelanggar-profiling-body" class="hidden space-y-6"></div>
            </div>
            <div id="peer-pelanggar-profiling-footer" class="hidden shrink-0 flex flex-wrap items-center justify-end gap-2 border-t border-outline-variant/15 bg-[#fafbfc] px-5 py-4 sm:px-6">
               <button type="button" id="peer-pelanggar-profiling-note" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-4 py-2.5 text-xs font-bold text-on-surface shadow-sm transition-colors hover:bg-surface-container-high">
                  <span class="material-symbols-outlined text-base" data-icon="edit_note">edit_note</span>
                  Catat tindakan
               </button>
               <button type="button" id="peer-pelanggar-profiling-pdf" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-4 py-2.5 text-xs font-bold text-on-surface shadow-sm transition-colors hover:bg-surface-container-high">
                  <span class="material-symbols-outlined text-base" data-icon="picture_as_pdf">picture_as_pdf</span>
                  Export PDF
               </button>
               <button type="button" id="peer-pelanggar-profiling-close2" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-sm transition-opacity hover:opacity-95">
                  Tutup
               </button>
            </div>
         </div>
      </div>
      
      <script>
      (function () {
        const modal = document.getElementById('peer-pressure-detail-modal');
        const bodyEl = document.getElementById('peer-pressure-detail-body');
        const loadingEl = document.getElementById('peer-pressure-detail-loading');
        const errorEl = document.getElementById('peer-pressure-detail-error');
        const contentEl = document.getElementById('peer-pressure-detail-content');
        const closeBtn = document.getElementById('peer-pressure-detail-close');
        const titleEl = document.getElementById('peer-pressure-detail-title');
        const detailUrlBase = @json(rtrim(url('/peer-pressure-edukasi/kejadian'), '/'));

        function escapeHtml(s) {
          if (s == null || s === '') return '';
          const d = document.createElement('div');
          d.textContent = String(s);
          return d.innerHTML;
        }
        function dash(s) {
          if (s == null || s === '') return '—';
          return String(s);
        }
        /** Avatar: foto dari API (nitip.bep_vw_wp_karyawan) atau inisial di belakang */
        function avatarStack(fotoUrl, initials, sizeClass) {
          var sz = sizeClass || 'w-10 h-10';
          var ini = escapeHtml(initials || '?');
          var img = fotoUrl
            ? '<img src="' + escapeHtml(fotoUrl) + '" alt="" class="absolute inset-0 z-10 h-full w-full rounded-full object-cover" loading="lazy" decoding="async" referrerpolicy="no-referrer" onerror="this.remove()" />'
            : '';
          return '<div class="relative ' + sz + ' shrink-0 overflow-hidden rounded-full bg-neutral-200">'
            + '<span class="absolute inset-0 z-0 flex items-center justify-center text-[11px] font-bold text-neutral-600">' + ini + '</span>'
            + img
            + '</div>';
        }
        function peranLabel(p) {
          return p === 'pelanggar' ? 'Pelanggar' : (p === 'peer' ? 'Peer' : dash(p));
        }

        function hasBerecordData(d) {
          return d.pelanggar_berecord != null && typeof d.pelanggar_berecord === 'object';
        }

        function brRow(label, key, br, wide) {
          var v = br[key];
          return '<div class="' + (wide ? 'sm:col-span-2' : '') + ' flex flex-col gap-1 py-3 first:pt-0 last:pb-0">'
            + '<span class="text-[11px] font-medium text-neutral-500">' + label + '</span>'
            + '<span class="text-sm text-neutral-900 leading-snug break-words">' + escapeHtml(dash(v)) + '</span></div>';
        }

        function renderBerecordSection(d) {
          var br = d.pelanggar_berecord;
          var pg = null;
          (d.peserta || []).forEach(function (p) {
            if (p.peran === 'pelanggar') pg = p;
          });
          if (!pg) return '';
          if (!br || typeof br !== 'object') {
            return ''
              + '<div class="mt-10 pt-8 border-t border-neutral-200 space-y-4">'
              + '<p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">BeRecord pelanggar</p>'
              + '<div class="flex gap-4 text-sm text-neutral-600">'
              + avatarStack(pg.foto_url, pg.initials, 'w-12 h-12')
              + '<div class="flex min-w-0 flex-1 gap-3">'
              + '<span class="material-symbols-outlined text-neutral-400 shrink-0 text-xl" data-icon="link_off">link_off</span>'
              + '<div><p class="font-medium text-neutral-900 mb-1">Tidak terhubung ke BeRecord Nitip</p>'
              + 'Tidak ada baris di <code class="text-xs bg-neutral-100 px-1.5 py-0.5 rounded text-neutral-700">bep_vw_berecord</code> untuk SID <span class="font-mono font-semibold text-neutral-900">' + escapeHtml(pg.sid) + '</span>.</div>'
              + '</div></div></div>';
          }
          var st = (br.status_proses_berecord || '').toUpperCase();
          var statusChip = st.indexOf('ACTIVE') >= 0
            ? 'bg-emerald-50 text-emerald-800'
            : 'bg-neutral-100 text-neutral-700';
          return ''
            + '<div class="mt-10 pt-8 border-t border-neutral-200 space-y-8">'
            + '<p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">BeRecord pelanggar</p>'
            + '<div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">'
            + '<div class="flex min-w-0 gap-4">'
            + avatarStack(pg.foto_url, pg.initials, 'w-14 h-14')
            + '<div class="min-w-0">'
            + '<p class="text-xs text-neutral-500 mb-1">Nama (Nitip · bep_vw_berecord)</p>'
            + '<p class="text-lg font-semibold text-neutral-900 leading-snug">' + escapeHtml(dash(br.nama)) + '</p>'
            + '</div></div>'
            + '<div class="flex flex-wrap gap-2 shrink-0">'
            + '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono bg-neutral-100 text-neutral-900">' + escapeHtml(dash(br.kode_sid)) + '</span>'
            + '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono bg-neutral-100 text-neutral-900">#' + escapeHtml(dash(br.be_record)) + '</span>'
            + '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium ' + statusChip + '">' + escapeHtml(dash(br.status_proses_berecord)) + '</span>'
            + '</div></div>'
            + '<div>'
            + '<p class="text-xs font-semibold text-neutral-500 mb-2">Ringkasan kasus</p>'
            + '<p class="text-sm text-neutral-800 leading-relaxed">' + escapeHtml(dash(br.diskripsi)) + '</p>'
            + '</div>'
            + '<div class="flex flex-wrap gap-x-10 gap-y-2 text-sm">'
            + '<div><span class="text-xs text-neutral-500 block mb-0.5">Mulai berlaku</span><span class="font-medium text-neutral-900">' + escapeHtml(dash(br.start_date_be_record)) + '</span></div>'
            + '<div><span class="text-xs text-neutral-500 block mb-0.5">Berakhir</span><span class="font-medium text-neutral-900">' + escapeHtml(dash(br.end_date_be_record)) + '</span></div>'
            + '</div>'
            + '<div class="space-y-3 pt-2 border-t border-neutral-100">'
            + '<p class="text-xs font-semibold text-neutral-500">Organisasi &amp; lokasi</p>'
            + '<div class="divide-y divide-neutral-100">'
            + brRow('Perusahaan', 'perusahaan', br) + brRow('Provinsi', 'alamat_province', br) + brRow('Struktural', 'j_strutural', br) + brRow('Fungsional', 'j_fungsional', br)
            + '</div></div>'
            + '<div class="space-y-3 pt-2 border-t border-neutral-100">'
            + '<p class="text-xs font-semibold text-neutral-500">Aturan &amp; izin</p>'
            + '<div class="divide-y divide-neutral-100">' + brRow('Work permit', 'work_permit', br, true) + brRow('Golden rules', 'golden_rules', br, true) + '</div></div>'
            + '<div class="space-y-3 pt-2 border-t border-neutral-100">'
            + '<p class="text-xs font-semibold text-neutral-500">PIC &amp; status</p>'
            + '<div class="divide-y divide-neutral-100">'
            + brRow('PIC persetujuan', 'pic_approval', br) + brRow('PIC verifikasi', 'pic_verifikasi', br)
            + brRow('Status izin', 'status_permit', br) + brRow('Tipe', 'tipe_berecord', br) + brRow('Status berlaku', 'status_berecord', br) + brRow('Kategori', 'kategori_berecord', br) + brRow('Kategori kecelakaan', 'kategori_kecelakaan', br)
            + '</div></div>'
            + '<div class="flex flex-wrap gap-x-8 gap-y-2 text-xs text-neutral-500 pt-4 border-t border-neutral-100">'
            + '<span>ID karyawan: <span class="font-mono text-neutral-800">' + escapeHtml(dash(br.id_status_karyawan)) + '</span></span>'
            + '<span>ID sink Nitip: <span class="font-mono text-neutral-800">' + escapeHtml(dash(br.id)) + '</span></span>'
            + '</div>'
            + '</div>';
        }

        function renderPeerPressureSection(d) {
          const sb = d.status_badge || {};
          const statusHtml = '<span class="' + escapeHtml(sb.spanClass || '') + '">' + escapeHtml(sb.label || '') + '</span>';
          const showPerusahaanHere = !hasBerecordData(d);
          let pesertaRows = '';
          const list = d.peserta || [];
          if (list.length === 0) {
            pesertaRows = '<tr><td colspan="5" class="py-3 text-sm text-neutral-500">Tidak ada peserta.</td></tr>';
          } else {
            list.forEach(function (p) {
              pesertaRows += '<tr>' +
                '<td class="py-3 pr-2 align-middle">' + avatarStack(p.foto_url, p.initials, 'w-10 h-10') + '</td>' +
                '<td class="py-3 pr-4 text-sm font-medium font-mono text-neutral-900 align-middle">' + escapeHtml(p.sid) + '</td>' +
                '<td class="py-3 pr-4 text-sm text-neutral-800 align-middle">' + escapeHtml(p.nama) + '</td>' +
                '<td class="py-3 pr-4 text-sm text-neutral-600 align-middle">' + escapeHtml(peranLabel(p.peran)) + '</td>' +
                '<td class="py-3 text-sm text-neutral-500 tabular-nums align-middle w-14">' + escapeHtml(String(p.urutan != null ? p.urutan : '')) + '</td></tr>';
            });
          }
          const ev = d.evidence_url;
          const evidenceBlock = ev
            ? '<a href="' + escapeHtml(ev) + '" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline"><span class="material-symbols-outlined text-lg" data-icon="open_in_new">open_in_new</span>Buka tautan evidence</a>'
            : '<span class="text-sm text-neutral-500">Belum ada tautan evidence</span>';
          var ringkasan = '';
          if (showPerusahaanHere) {
            ringkasan += '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Perusahaan (kejadian)</span><span class="text-sm text-neutral-900">' + escapeHtml(dash(d.perusahaan)) + '</span></div>';
          }
          ringkasan += ''
            + '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Departemen</span><span class="text-sm text-neutral-900">' + escapeHtml(dash(d.departemen)) + '</span></div>'
            + '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Aktivitas &amp; kelompok</span><span class="text-sm text-neutral-900">' + escapeHtml(dash(d.aktivitas_pekerjaan)) + (d.kelompok_aktivitas_pekerjaan ? ' · ' + escapeHtml(dash(d.kelompok_aktivitas_pekerjaan)) : '') + '</span></div>'
            + '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Jenis kelompok kerja</span><span class="text-sm text-neutral-900">' + escapeHtml(dash(d.jenis_kelompok_kerja)) + '</span></div>'
            + '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Tasklist temuan</span><span class="text-sm text-neutral-900">' + escapeHtml(dash(d.tasklist_temuan)) + '</span></div>'
            + '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Referensi BeRecord (impor Excel)</span><span class="text-sm font-mono text-neutral-900">' + escapeHtml(dash(d.id_berecord)) + '</span></div>'
            + '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Status pelaksanaan edukasi</span><span class="text-sm text-neutral-900">' + escapeHtml(dash(d.status_pelaksanaan_edukasi)) + '</span></div>';
          return ''
            + '<div class="space-y-8">'
            + '<div class="flex flex-wrap items-start justify-between gap-3 pb-5 border-b border-neutral-200">'
            + '<div>'
            + '<h3 class="text-base font-semibold text-neutral-900">Kejadian &amp; edukasi</h3>'
            + '<p class="text-[13px] text-neutral-500 mt-0.5">Catatan Peer Pressure di aplikasi</p></div>'
            + '<div class="flex items-center gap-2">' + statusHtml + '</div></div>'
            + '<div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">'
            + '<div class="space-y-2 pl-4 border-l-2 border-neutral-300">'
            + '<p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">Temuan</p>'
            + '<p class="text-sm font-medium text-neutral-900">' + escapeHtml(d.formatted_temuan) + '</p>'
            + '<p class="text-sm text-neutral-600 leading-relaxed">' + escapeHtml(dash(d.kelompok_lokasi_temuan)) + ' · ' + escapeHtml(dash(d.lokasi_temuan)) + '</p>'
            + '</div>'
            + '<div class="space-y-2 pl-4 border-l-2 border-neutral-300">'
            + '<p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">Edukasi</p>'
            + '<p class="text-sm font-medium text-neutral-900">' + escapeHtml(d.formatted_edukasi) + '</p>'
            + '<p class="text-sm text-neutral-600 leading-relaxed">' + escapeHtml(dash(d.kelompok_lokasi_edukasi)) + ' · ' + escapeHtml(dash(d.lokasi_edukasi)) + '</p>'
            + '<p class="text-sm text-neutral-600">Pemimpin: ' + escapeHtml(dash(d.pemimpin_edukasi)) + ' · Durasi ' + escapeHtml(String(d.durasi_edukasi_menit != null ? d.durasi_edukasi_menit : '—')) + ' menit</p>'
            + '</div></div>'
            + '<div>'
            + '<p class="text-xs font-semibold text-neutral-500 mb-2">Kategori deviasi</p>'
            + '<span class="text-sm font-medium text-neutral-900">' + escapeHtml(dash(d.kategori_deviasi)) + '</span>'
            + '</div>'
            + '<div>'
            + '<p class="text-xs font-semibold text-neutral-500 mb-2">Kronologi</p>'
            + '<p class="text-sm text-neutral-800 leading-relaxed whitespace-pre-wrap">' + escapeHtml(dash(d.kronologi_temuan)) + '</p></div>'
            + '<div class="space-y-3">'
            + '<p class="text-xs font-semibold text-neutral-500">Konteks tambahan</p>'
            + '<div class="divide-y divide-neutral-200">' + ringkasan + '</div></div>'
            + '<div>'
            + '<p class="text-xs font-semibold text-neutral-500 mb-2">Evidence</p>' + evidenceBlock + '</div>'
            + '<div>'
            + '<p class="text-xs font-semibold text-neutral-500 mb-1">Peserta</p>'
            + '<p class="text-[11px] text-neutral-400 mb-2">Foto dari Nitip · <span class="font-mono">bep_vw_wp_karyawan</span> (kode_sid)</p>'
            + '<div class="overflow-x-auto">'
            + '<table class="w-full text-left border-collapse">'
            + '<thead><tr class="border-b border-neutral-200 text-[11px] font-semibold text-neutral-500">'
            + '<th class="w-12 py-2.5 pr-2 text-left"><span class="sr-only">Foto</span></th>'
            + '<th class="py-2.5 pr-4 text-left">SID</th><th class="py-2.5 pr-4 text-left">Nama</th><th class="py-2.5 pr-4 text-left">Peran</th><th class="py-2.5 w-16 text-left">Urut</th></tr></thead>'
            + '<tbody class="divide-y divide-neutral-100">' + pesertaRows + '</tbody></table></div></div>'
            + '</div>';
        }

        function renderDetail(d) {
          return '<div class="space-y-0 max-w-full">' + renderPeerPressureSection(d) + renderBerecordSection(d) + '</div>';
        }

        function setOpen(open) {
          if (!modal) return;
          if (open) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
          } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
          }
        }

        function showLoading() {
          loadingEl.classList.remove('hidden');
          loadingEl.classList.add('flex');
          errorEl.classList.add('hidden');
          contentEl.classList.add('hidden');
          errorEl.textContent = '';
          contentEl.innerHTML = '';
        }

        function showError(msg) {
          loadingEl.classList.add('hidden');
          loadingEl.classList.remove('flex');
          errorEl.classList.remove('hidden');
          errorEl.textContent = msg;
          contentEl.classList.add('hidden');
        }

        function showContent(html) {
          loadingEl.classList.add('hidden');
          loadingEl.classList.remove('flex');
          errorEl.classList.add('hidden');
          contentEl.classList.remove('hidden');
          contentEl.innerHTML = html;
        }

        async function openForId(id) {
          showLoading();
          setOpen(true);
          titleEl.textContent = 'Detail kejadian #' + id;
          try {
            const res = await fetch(detailUrlBase + '/' + id, {
              headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
              credentials: 'same-origin'
            });
            if (!res.ok) {
              showError(res.status === 404 ? 'Data tidak ditemukan.' : 'Gagal memuat detail (' + res.status + ').');
              return;
            }
            const data = await res.json();
            showContent(renderDetail(data));
          } catch (e) {
            showError('Tidak dapat memuat detail. Periksa koneksi lalu coba lagi.');
          }
        }

        window.peerPressureOpenKejadianDetail = openForId;

        document.querySelectorAll('.js-peer-kejadian-row').forEach(function (row) {
          row.addEventListener('click', function (e) {
            if (e.target.closest('a')) return;
            const id = row.getAttribute('data-kejadian-id');
            if (id) openForId(id);
          });
          row.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            e.preventDefault();
            const id = row.getAttribute('data-kejadian-id');
            if (id) openForId(id);
          });
        });

        function closeModal() {
          setOpen(false);
        }

        closeBtn.addEventListener('click', closeModal);
        modal.querySelector('.peer-pressure-modal-backdrop').addEventListener('click', closeModal);
        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
        });
      })();
      </script>
      <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
      @include('peer-pressure-edukasi.partials.trend-of-accident-panel-charts')
      <script>
      (function () {
        const weeklyTrendUrl = @json(route('peer-pressure-edukasi.dashboard.weekly-trend'));
        const peerHighlightUrl = @json(route('peer-pressure-edukasi.dashboard.highlight-issue-recommendation'));
        const complianceBreakdownUrl = @json(route('peer-pressure-edukasi.dashboard.compliance-breakdown'));
        const modal = document.getElementById('peer-weekly-period-modal');
        const backdrop = modal ? modal.querySelector('.peer-weekly-period-backdrop') : null;
        const openBtn = document.getElementById('peer-open-weekly-period');
        const cancelBtn = document.getElementById('peer-weekly-period-cancel');
        const applyBtn = document.getElementById('peer-weekly-period-apply');
        const allDataBtn = document.getElementById('peer-modal-all-data');
        const loadingEl = document.getElementById('peer-weekly-chart-loading');
        var state = {
          all: {{ ($chartPeriodMonth ?? false) ? 'false' : 'true' }},
          year: {{ (int) $cy }},
          month: {{ (int) $cm }}
        };
        var tempYear = state.year;
        var tempMonth = state.month;
        var tempAll = state.all;
        var peerDeviationSparkWeeks = @json($peerDeviationModalSparkWeeks);
        var peerHazardReportingBySite = @json($peerHazardReportingBySite ?? null);
        var peerTbcHighBySite = @json($peerTbcHighBySite ?? null);
        var peerTbcBlindspotBySite = @json($peerTbcBlindspotBySite ?? null);
        var peerGoldenRulesBySite = @json($peerGoldenRulesBySite ?? null);
        var peerAreaNonKritisBySite = @json($peerAreaNonKritisBySite ?? null);
        var peerAreaKritisBySite = @json($peerAreaKritisBySite ?? null);
        var peerTbcCategoryTrend = @json($tbcCategoryTrendData ?? null);
        function peerMetricEvalFromJson(json, site, defaultBar) {
          if (!json || !json.weeks || !json.bySite) return null;
          var weeks = json.weeks;
          var bySite = json.bySite;
          var values = [];
          var i;
          var wk;
          var sum;
          var row;
          if (site === '__all') {
            for (i = 0; i < weeks.length; i++) {
              wk = weeks[i];
              sum = 0;
              Object.keys(bySite).forEach(function (s) {
                row = bySite[s];
                if (row && row[wk] != null) sum += Number(row[wk]) || 0;
              });
              values.push(sum);
            }
          } else {
            row = bySite[site] || {};
            for (i = 0; i < weeks.length; i++) {
              wk = weeks[i];
              values.push(Number(row[wk]) || 0);
            }
          }
          return {
            weeks: weeks,
            label: json.parameter || '',
            bar: defaultBar || '#64748b',
            values: values,
            decimals: 0,
          };
        }
        function peerHazardWowHtml(values, weeks) {
          if (!values || values.length < 2) {
            return '<p class="text-on-surface-variant text-[11px] font-medium">—</p>';
          }
          var last = Number(values[values.length - 1]) || 0;
          var prev = Number(values[values.length - 2]) || 0;
          var wkPrev = weeks[weeks.length - 2] || 'W14';
          var wkLast = weeks[weeks.length - 1] || 'W15';
          if (prev <= 0.0001) {
            return '<p class="text-on-surface-variant text-[11px] font-medium">—</p>';
          }
          var pct = ((last - prev) / prev) * 100;
          var down = pct <= 0;
          var icon = down ? 'trending_down' : 'trending_up';
          var color = down ? 'text-[#059669]' : 'text-error';
          var sign = pct >= 0 ? '+' : '';
          return (
            '<p class="text-[11px] font-bold flex items-center gap-1 ' +
            color +
            '"><span class="material-symbols-outlined text-xs" data-icon="' +
            icon +
            '">' +
            icon +
            '</span> WoW ' +
            sign +
            pct.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) +
            '% (' +
            wkPrev +
            ' → ' +
            wkLast +
            ')</p>'
          );
        }
        function renderPeerHazardMiniBarHtml(evalObj, compact) {
          var weeks = evalObj.weeks || [];
          var vals = evalObj.values || [];
          var bar = evalObj.bar || '#d97706';
          var dec = evalObj.decimals != null ? Number(evalObj.decimals) : 0;
          if (!vals.length) return '';
          var maxV = Math.max.apply(
            null,
            vals.map(function (v) {
              return Number(v) || 0;
            })
          );
          if (maxV <= 0) maxV = 1;
          var isCompact = !!compact;
          var hChart = isCompact ? 'h-20 sm:h-24' : 'h-24 sm:h-28';
          var fsVal = isCompact ? 'text-[7px] sm:text-[8px]' : 'text-[8px] sm:text-[9px]';
          var fsAxis = isCompact ? 'text-[7px] sm:text-[8px]' : 'text-[8px] sm:text-[9px]';
          var mtWrap = isCompact ? 'mt-2' : 'mt-1.5';
          var barCols = [];
          var vi;
          for (vi = 0; vi < vals.length; vi++) {
            var val = Number(vals[vi]) || 0;
            var pct = Math.min(100, Math.max(0, Math.round((val / maxV) * 100)));
            var fmt = val.toLocaleString('id-ID', { minimumFractionDigits: dec, maximumFractionDigits: dec });
            var wkLbl = weeks[vi] || 'W' + (vi + 1);
            var tip = wkLbl + ' — ' + fmt;
            barCols.push(
              '<div class="peer-chart-bar-col group relative flex h-full min-h-0 min-w-[2.25rem] flex-1 basis-0 flex-col justify-end rounded-t-lg border-x border-t border-outline-variant/10 bg-[#f8fafc]" title="' +
              String(tip).replace(/"/g, '&quot;') +
              '">' +
              '<div class="relative w-full" style="height: ' +
              pct +
              '%">' +
              '<span class="absolute -top-5 left-1/2 z-10 -translate-x-1/2 whitespace-nowrap font-semibold tabular-nums text-on-surface ' +
              fsVal +
              '">' +
              fmt +
              '</span>' +
              '<div class="absolute inset-0 flex flex-col justify-end overflow-hidden rounded-t-md shadow-inner ring-1 ring-black/10">' +
              '<div class="min-h-[2px] w-full shrink-0 transition-opacity group-hover:opacity-95" style="height: 100%; background-color: ' +
              bar +
              '"></div></div></div></div>'
            );
          }
          var axis = [];
          for (vi = 0; vi < vals.length; vi++) {
            axis.push(
              '<span class="peer-chart-axis-tick min-w-[2rem] flex-1 basis-0 text-center leading-tight">' +
              (weeks[vi] || 'W' + (vi + 1)) +
              '</span>'
            );
          }
          return (
            '<div class="' +
            mtWrap +
            ' w-full">' +
            '<div class="mb-1.5 flex items-start gap-2">' +
            '<span class="mt-0.5 h-2 w-2 shrink-0 rounded-full shadow-sm ring-2 ring-white" style="background-color: ' +
            bar +
            '"></span>' +
            '<p class="min-w-0 flex-1 text-left text-[10px] font-semibold leading-snug text-slate-800">Trend Last 4 Weeks</p></div>' +
            '<div class="peer-chart-scroll w-full overflow-x-auto">' +
            '<div class="peer-chart-scroll-inner w-max min-w-full px-0.5">' +
            '<div class="relative ' +
            hChart +
            '">' +
            '<div class="peer-chart-bars relative z-10 flex h-full w-full items-stretch gap-1 sm:gap-2">' +
            barCols.join('') +
            '</div></div>' +
            '<div class="peer-chart-axis-labels mt-1.5 flex w-full min-w-0 gap-1 sm:gap-2 font-bold uppercase tracking-wider text-on-surface-variant sm:tracking-widest ' +
            fsAxis +
            '">' +
            axis.join('') +
            '</div></div></div></div>'
          );
        }
        function applyPeerHazardSiteFilter(site) {
          if (!peerHazardReportingBySite) return;
          var ev = peerMetricEvalFromJson(peerHazardReportingBySite, site || '__all', '#d97706');
          if (!ev) return;
          var host = document.getElementById('peer-kpi-hazard-mini-bar-host');
          var totalEl = document.getElementById('peer-kpi-hazard-total');
          var trendEl = document.getElementById('peer-kpi-hazard-trend');
          var vals = ev.values || [];
          var last = vals.length ? Number(vals[vals.length - 1]) : 0;
          if (totalEl) totalEl.textContent = Math.round(last).toLocaleString('id-ID');
          if (trendEl) trendEl.innerHTML = peerHazardWowHtml(vals, ev.weeks || []);
          if (host) host.innerHTML = renderPeerHazardMiniBarHtml(ev);
          try {
            var u = new URL(window.location.href);
            if (site && site !== '__all') u.searchParams.set('hazard_site', site);
            else u.searchParams.delete('hazard_site');
            window.history.replaceState({}, '', u.toString());
          } catch (e) {}
        }
        function applyPeerTbcHighSiteFilter(site) {
          if (!peerTbcHighBySite) return;
          var ev = peerMetricEvalFromJson(peerTbcHighBySite, site || '__all', '#3952bc');
          if (!ev) return;
          var host = document.getElementById('peer-kpi-tbc-mini-bar-host');
          var totalEl = document.getElementById('peer-kpi-tbc-high-total');
          var trendEl = document.getElementById('peer-kpi-tbc-high-trend');
          var vals = ev.values || [];
          var last = vals.length ? Number(vals[vals.length - 1]) : 0;
          if (totalEl) totalEl.textContent = Math.round(last).toLocaleString('id-ID');
          if (trendEl) trendEl.innerHTML = peerHazardWowHtml(vals, ev.weeks || []);
          if (host) host.innerHTML = renderPeerHazardMiniBarHtml(ev);
        }
        function applyPeerTbcBlindspotSiteFilter(site) {
          if (!peerTbcBlindspotBySite) return;
          var ev = peerMetricEvalFromJson(peerTbcBlindspotBySite, site || '__all', '#16a34a');
          if (!ev) return;
          var host = document.getElementById('peer-kpi-blindspot-mini-bar-host');
          var totalEl = document.getElementById('peer-kpi-blindspot-total');
          var trendEl = document.getElementById('peer-kpi-blindspot-trend');
          var vals = ev.values || [];
          var last = vals.length ? Number(vals[vals.length - 1]) : 0;
          if (totalEl) totalEl.textContent = Math.round(last).toLocaleString('id-ID');
          if (trendEl) trendEl.innerHTML = peerHazardWowHtml(vals, ev.weeks || []);
          if (host) host.innerHTML = renderPeerHazardMiniBarHtml(ev);
        }
        function buildPeerGoldenRulesChartInnerHtml(weeks, vals, barColor) {
          var chartPx = 104;
          var barRed = barColor != null && String(barColor) !== '' ? String(barColor) : '#c8102e';
          var maxV = Math.max(
            1e-9,
            Math.max.apply(
              null,
              vals.map(function (v) {
                return Number(v) || 0;
              })
            )
          );
          var gridLines = '';
          var g;
          for (g = 0; g < 5; g++) {
            gridLines += '<div class="h-px w-full bg-neutral-300/80"></div>';
          }
          var gridHtml =
            '<div class="pointer-events-none absolute inset-x-0 bottom-0 top-6 z-0 flex flex-col justify-between opacity-[0.45]" style="height:' +
            chartPx +
            'px" aria-hidden="true">' +
            gridLines +
            '</div>';
          var barsInner = '';
          var vi;
          for (vi = 0; vi < vals.length; vi++) {
            var v = Number(vals[vi]) || 0;
            var ratio = Math.min(1, Math.max(0, v / maxV));
            var barPct = ratio * 100;
            var lbl =
              Math.abs(v - Math.round(v)) < 0.0001 ? String(Math.round(v)) : String(v);
            barsInner +=
              '<div class="flex h-full min-h-0 min-w-0 flex-1 flex-col items-center">' +
              '<span class="mb-1 shrink-0 text-center text-[11px] font-semibold tabular-nums text-neutral-900">' +
              lbl +
              '</span>' +
              '<div class="flex min-h-0 w-full flex-1 flex-col justify-end">' +
              '<div class="w-full rounded-none" style="height:' +
              barPct +
              '%;min-height:' +
              (v > 0 ? '2px' : '0') +
              ';background-color:' +
              barRed +
              ';"></div></div></div>';
          }
          var barsHtml =
            '<div class="relative z-10 flex items-end gap-1.5 sm:gap-2 pt-5" style="height:' +
            chartPx +
            'px">' +
            barsInner +
            '</div>';
          var labInner = '';
          for (vi = 0; vi < vals.length; vi++) {
            var wk = weeks[vi] != null ? String(weeks[vi]) : 'W' + (11 + vi);
            labInner +=
              '<span class="min-w-0 flex-1 text-center text-[9px] font-semibold uppercase tracking-wide text-neutral-600">' +
              wk +
              '</span>';
          }
          var labelsHtml = '<div class="mt-2 flex w-full gap-1.5 sm:gap-2">' + labInner + '</div>';
          return gridHtml + barsHtml + labelsHtml;
        }
        function applyPeerGoldenRulesSiteFilter(site) {
          if (!peerGoldenRulesBySite) return;
          var ev = peerMetricEvalFromJson(peerGoldenRulesBySite, site || '__all', '#c8102e');
          if (!ev) return;
          var vals = ev.values || [];
          var weeks = ev.weeks || [];
          var sum = 0;
          vals.forEach(function (v) {
            sum += Number(v) || 0;
          });
          var last = vals.length ? Number(vals[vals.length - 1]) : 0;
          var totalStr = Math.round(sum).toLocaleString('id-ID');
          var validStr = Math.round(last).toLocaleString('id-ID');
          document.querySelectorAll('.peer-kpi-hr-stack-total[data-gr-json="1"]').forEach(function (el) {
            el.textContent = totalStr;
          });
          document.querySelectorAll('.peer-gr-valid-gr-display[data-gr-json="1"]').forEach(function (el) {
            el.textContent = validStr + ' Valid GR';
          });
          var inner = buildPeerGoldenRulesChartInnerHtml(weeks, vals, '#c8102e');
          document.querySelectorAll('.peer-gr-l4w-chart-root').forEach(function (root) {
            root.innerHTML = inner;
          });
          var grCardTotal = document.getElementById('peer-kpi-gr-total');
          var grCardTrend = document.getElementById('peer-kpi-gr-trend');
          var grCardMiniHost = document.getElementById('peer-kpi-gr-compliance-mini-bar-host');
          if (grCardTotal) grCardTotal.textContent = totalStr;
          if (grCardTrend) grCardTrend.innerHTML = peerHazardWowHtml(vals, weeks);
          if (grCardMiniHost) grCardMiniHost.innerHTML = renderPeerHazardMiniBarHtml(ev, true);
        }
        function applyPeerAreaNonKritisSiteFilter(site) {
          if (!peerAreaNonKritisBySite) return;
          var ev = peerMetricEvalFromJson(peerAreaNonKritisBySite, site || '__all', '#ea580c');
          if (!ev) return;
          var vals = ev.values || [];
          var weeks = ev.weeks || [];
          var sum = 0;
          vals.forEach(function (v) {
            sum += Number(v) || 0;
          });
          var last = vals.length ? Number(vals[vals.length - 1]) : 0;
          var totalStr = Math.round(sum).toLocaleString('id-ID');
          var validStr = Math.round(last).toLocaleString('id-ID');
          document.querySelectorAll('.peer-kpi-hr-stack-total[data-ank-json="1"]').forEach(function (el) {
            el.textContent = totalStr;
          });
          document.querySelectorAll('.peer-gr-valid-gr-display[data-ank-json="1"]').forEach(function (el) {
            el.textContent = validStr + ' minggu terakhir';
          });
          var innerAnk = buildPeerGoldenRulesChartInnerHtml(weeks, vals, '#ea580c');
          document.querySelectorAll('.peer-ank-l4w-chart-root').forEach(function (root) {
            root.innerHTML = innerAnk;
          });
        }
        function applyPeerAreaKritisSiteFilter(site) {
          if (!peerAreaKritisBySite) return;
          var ev = peerMetricEvalFromJson(peerAreaKritisBySite, site || '__all', '#dc2626');
          if (!ev) return;
          var vals = ev.values || [];
          var weeks = ev.weeks || [];
          var sum = 0;
          vals.forEach(function (v) {
            sum += Number(v) || 0;
          });
          var last = vals.length ? Number(vals[vals.length - 1]) : 0;
          var totalStr = Math.round(sum).toLocaleString('id-ID');
          var validStr = Math.round(last).toLocaleString('id-ID');
          document.querySelectorAll('.peer-kpi-hr-stack-total[data-kritis-json="1"]').forEach(function (el) {
            el.textContent = totalStr;
          });
          document.querySelectorAll('.peer-gr-valid-gr-display[data-kritis-json="1"]').forEach(function (el) {
            el.textContent = validStr + ' minggu terakhir';
          });
          var innerKr = buildPeerGoldenRulesChartInnerHtml(weeks, vals, '#dc2626');
          document.querySelectorAll('.peer-kritis-l4w-chart-root').forEach(function (root) {
            root.innerHTML = innerKr;
          });
        }
        var peerHazardSiteSelect = document.getElementById('peer-hazard-site-filter');
        if (
          peerHazardSiteSelect &&
          (peerHazardReportingBySite ||
            peerTbcHighBySite ||
            peerTbcBlindspotBySite ||
            peerGoldenRulesBySite ||
            peerAreaNonKritisBySite ||
            peerAreaKritisBySite)
        ) {
          peerHazardSiteSelect.addEventListener('change', function () {
            var v = peerHazardSiteSelect.value;
            applyPeerHazardSiteFilter(v);
            applyPeerTbcHighSiteFilter(v);
            applyPeerTbcBlindspotSiteFilter(v);
            applyPeerGoldenRulesSiteFilter(v);
            applyPeerAreaNonKritisSiteFilter(v);
            applyPeerAreaKritisSiteFilter(v);
          });
        }
        var peerDeviationChartInstances = [];

        function destroyPeerDeviationModalCharts() {
          peerDeviationChartInstances.forEach(function (ch) {
            try {
              ch.destroy();
            } catch (e) {}
          });
          peerDeviationChartInstances = [];
        }
        function fillColorFromLineColor(border) {
          if (!border) return 'rgba(57, 82, 188, 0.14)';
          var b = String(border).trim();
          if (b.charAt(0) === '#' && b.length >= 7) {
            var r = parseInt(b.slice(1, 3), 16);
            var g = parseInt(b.slice(3, 5), 16);
            var bl = parseInt(b.slice(5, 7), 16);
            return 'rgba(' + r + ',' + g + ',' + bl + ',0.14)';
          }
          var rgbM = b.match(/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i);
          if (rgbM) {
            return 'rgba(' + rgbM[1] + ',' + rgbM[2] + ',' + rgbM[3] + ',0.14)';
          }
          if (b.indexOf('hsl') === 0) return 'rgba(148, 163, 184, 0.2)';
          return 'rgba(57, 82, 188, 0.14)';
        }
        function lineColorForDeviation(canvasEl, fallback) {
          var c = canvasEl && canvasEl.getAttribute('data-line-color');
          if (c) return c;
          return fallback || '#2563eb';
        }
        function peerDeviationHrSampleMean(arr) {
          if (!arr || !arr.length) return 0;
          var s = 0;
          for (var i = 0; i < arr.length; i++) s += arr[i];
          return s / arr.length;
        }
        function peerDeviationHrSampleStdev(arr) {
          var n = arr ? arr.length : 0;
          if (n < 2) return 0;
          var m = peerDeviationHrSampleMean(arr);
          var ss = 0;
          for (var i = 0; i < n; i++) ss += Math.pow(arr[i] - m, 2);
          return Math.sqrt(ss / (n - 1));
        }
        function createPeerDeviationHrBarChart(canvas, labels, data, barColor, mainSeriesLabel) {
          if (typeof Chart === 'undefined' || !canvas) return null;
          var bc = barColor || '#3952bc';
          if (bc === '#DEE5EF' || bc === '#dee5ef' || bc === '#e2e2e2') bc = '#3952bc';
          var mainLab =
            mainSeriesLabel != null && String(mainSeriesLabel).trim() !== ''
              ? String(mainSeriesLabel).trim()
              : 'Nilai';
          var ctx = canvas.getContext('2d');
          if (!ctx) return null;
          var lbls = Array.isArray(labels) ? labels : [];
          var pts = Array.isArray(data) ? data : [];
          if (lbls.length !== pts.length && pts.length > 0) {
            while (lbls.length < pts.length) lbls.push(String(lbls.length + 1));
            lbls = lbls.slice(0, pts.length);
          }
          if (!pts.length) {
            return null;
          }
          var nums = [];
          for (var ni = 0; ni < pts.length; ni++) {
            var nv = Number(pts[ni]);
            if (!isNaN(nv)) nums.push(nv);
          }
          var mean = peerDeviationHrSampleMean(nums);
          var stdev = peerDeviationHrSampleStdev(nums);
          var ucl = mean + 0.75 * stdev;
          var lcl = mean - 0.3 * stdev;
          var nPts = pts.length;
          var uclArr = nPts ? lbls.map(function () { return ucl; }) : [];
          var lclArr = nPts ? lbls.map(function () { return lcl; }) : [];
          var meanArr = nPts ? lbls.map(function () { return mean; }) : [];
          var yVals = nums.length ? nums.concat([ucl, lcl, mean]) : [0, 1];
          var yMin = Math.min.apply(null, yVals);
          var yMax = Math.max.apply(null, yVals);
          var pad = (yMax - yMin) * 0.12 || Math.max(Math.abs(yMax) * 0.05, 1);
          var peerHrControlCaption = {
            id: 'peerHrControlCaption',
            afterDraw: function (ch) {
              var c = ch.ctx;
              var ca = ch.chartArea;
              if (!ca) return;
              c.save();
              c.fillStyle = '#64748b';
              c.font = '600 9px Poppins, system-ui, sans-serif';
              c.textAlign = 'right';
              c.textBaseline = 'bottom';
              var cap =
                'Mean ' +
                mean.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +
                ' · UCL ' +
                ucl.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +
                ' · LCL ' +
                lcl.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
              c.fillText(cap, ca.right, ca.top - 2);
              c.restore();
            },
          };
          var peerHrPointLabels = {
            id: 'peerHrPointLabels',
            afterDatasetsDraw: function (ch) {
              var dsIdx = ch.data.datasets.length - 1;
              var meta = ch.getDatasetMeta(dsIdx);
              if (!meta || meta.hidden) return;
              var ds = ch.data.datasets[dsIdx];
              var c = ch.ctx;
              c.save();
              c.font = '600 10px Poppins, system-ui, sans-serif';
              c.textAlign = 'center';
              c.textBaseline = 'bottom';
              meta.data.forEach(function (pt, i) {
                if (!pt || pt.skip) return;
                var raw = ds.data[i];
                if (raw == null || raw === '') return;
                var v = Number(raw);
                if (isNaN(v)) return;
                var col = v > ucl ? '#dc2626' : v < lcl ? '#ea580c' : '#15803d';
                c.fillStyle = col;
                var txt = Math.abs(v - Math.round(v)) < 1e-6 ? String(Math.round(v)) : v.toFixed(1);
                c.fillText(txt, pt.x, pt.y - 6);
              });
              c.restore();
            },
          };
          try {
            return new Chart(ctx, {
              type: 'line',
              data: {
                labels: lbls,
                datasets: [
                  {
                    label: 'UCL',
                    data: uclArr,
                    borderColor: 'rgba(220, 38, 38, 0.95)',
                    borderDash: [5, 5],
                    borderWidth: 1.5,
                    pointRadius: 0,
                    fill: false,
                    tension: 0,
                    order: 10,
                  },
                  {
                    label: 'LCL',
                    data: lclArr,
                    borderColor: 'rgba(107, 114, 128, 0.92)',
                    borderDash: [5, 5],
                    borderWidth: 1.5,
                    pointRadius: 0,
                    fill: '-1',
                    backgroundColor: 'rgba(203, 213, 225, 0.28)',
                    tension: 0,
                    order: 10,
                  },
                  {
                    label: 'Mean',
                    data: meanArr,
                    borderColor: 'rgba(22, 163, 74, 0.95)',
                    borderDash: [4, 4],
                    borderWidth: 1.5,
                    pointRadius: 0,
                    fill: false,
                    tension: 0,
                    order: 11,
                  },
                  {
                    label: mainLab,
                    data: pts,
                    borderWidth: 2.5,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderWidth: 2,
                    fill: false,
                    order: 12,
                    segment: {
                      borderColor: function (seg) {
                        var y = seg.p1 && seg.p1.parsed && seg.p1.parsed.y;
                        if (y == null || isNaN(y)) return bc;
                        if (y > ucl) return '#dc2626';
                        if (y < lcl) return '#ea580c';
                        return '#16a34a';
                      },
                    },
                    borderColor: bc,
                    pointBorderColor: function (ctx) {
                      var y = ctx.parsed && ctx.parsed.y;
                      if (y == null || isNaN(y)) return bc;
                      if (y > ucl) return '#dc2626';
                      if (y < lcl) return '#ea580c';
                      return '#16a34a';
                    },
                  },
                ],
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 22, right: 8, bottom: 4, left: 6 } },
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                  legend: { display: false },
                  tooltip: {
                    callbacks: {
                      label: function (ctx) {
                        var lab = ctx.dataset.label || '';
                        if (lab === 'UCL') return 'UCL: ' + ucl.toLocaleString('id-ID', { maximumFractionDigits: 2 });
                        if (lab === 'LCL') return 'LCL: ' + lcl.toLocaleString('id-ID', { maximumFractionDigits: 2 });
                        if (lab === 'Mean') return 'Mean: ' + mean.toLocaleString('id-ID', { maximumFractionDigits: 2 });
                        var v =
                          ctx.parsed && ctx.parsed.y != null && !isNaN(ctx.parsed.y)
                            ? ctx.parsed.y
                            : ctx.raw;
                        if (v == null || v === '') return '';
                        var n = Number(v);
                        if (isNaN(n)) return String(v);
                        var isInt = Math.abs(n - Math.round(n)) < 1e-9;
                        var prefix = lab + ': ';
                        return isInt
                          ? prefix + n.toLocaleString('id-ID')
                          : prefix + n.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 3 });
                      },
                    },
                  },
                },
                scales: {
                  x: {
                    offset: true,
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { size: 9 }, maxRotation: 0, autoSkip: false },
                  },
                  y: {
                    min: yMin - pad,
                    max: yMax + pad,
                    grid: { color: 'rgba(0, 0, 0, 0.06)', drawBorder: false },
                    ticks: {
                      display: false,
                      font: { size: 8 },
                      maxTicksLimit: 6,
                      callback: function (val) {
                        return Number(val).toLocaleString('id-ID', { maximumFractionDigits: 1 });
                      },
                    },
                    border: { display: false },
                  },
                },
              },
              plugins: [peerHrControlCaption, peerHrPointLabels],
            });
          } catch (err) {
            return null;
          }
        }
        function createPeerDeviationLineChart(canvas, labels, data, borderColor) {
          if (typeof Chart === 'undefined' || !canvas) return null;
          var bc = borderColor || '#2563eb';
          var ctx = canvas.getContext('2d');
          if (!ctx) return null;
          var lbls = Array.isArray(labels) ? labels : [];
          var pts = Array.isArray(data) ? data : [];
          if (lbls.length !== pts.length && pts.length > 0) {
            while (lbls.length < pts.length) lbls.push(String(lbls.length + 1));
            lbls = lbls.slice(0, pts.length);
          }
          try {
            return new Chart(ctx, {
              type: 'bar',
              data: {
                labels: lbls,
                datasets: [
                  {
                    label: 'Jumlah',
                    data: pts,
                    borderColor: bc,
                    backgroundColor: 'rgba(79, 121, 167, 0.95)',
                    borderWidth: 0,
                    borderRadius: 0,
                    barThickness: 26,
                    maxBarThickness: 30,
                  },
                ],
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 6, right: 2, bottom: 0, left: 2 } },
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                  legend: { display: false },
                  tooltip: {
                    callbacks: {
                      label: function (ctx) {
                        var v =
                          ctx.parsed && ctx.parsed.y != null && !isNaN(ctx.parsed.y)
                            ? ctx.parsed.y
                            : ctx.raw;
                        if (v == null || v === '') return '';
                        var n = Number(v);
                        if (isNaN(n)) return String(v);
                        var isInt = Math.abs(n - Math.round(n)) < 1e-9;
                        return isInt
                          ? n.toLocaleString('id-ID')
                          : n.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 3 });
                      },
                    },
                  },
                },
                scales: {
                  x: {
                    offset: true,
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { size: 9, weight: '700' }, maxRotation: 0, autoSkip: false, color: '#7c8ea3' },
                  },
                  y: {
                    beginAtZero: true,
                    grid: { display: false, drawBorder: false, lineWidth: 0 },
                    ticks: { display: false },
                    border: { display: false },
                  },
                },
              },
            });
          } catch (err) {
            return null;
          }
        }
        /** Mini line/area chart for TBC category cards (grid + W12–W15 labels). */
        function createPeerTbcCategoryMiniLineChart(canvas, labels, data, borderColor) {
          if (typeof Chart === 'undefined' || !canvas) return null;
          var bc = borderColor || '#2563eb';
          var ctx = canvas.getContext('2d');
          if (!ctx) return null;
          var lbls = Array.isArray(labels) ? labels : [];
          var pts = Array.isArray(data) ? data : [];
          if (lbls.length !== pts.length && pts.length > 0) {
            while (lbls.length < pts.length) lbls.push(String(lbls.length + 1));
            lbls = lbls.slice(0, pts.length);
          }
          try {
            return new Chart(ctx, {
              type: 'line',
              data: {
                labels: lbls,
                datasets: [
                  {
                    label: 'Nilai',
                    data: pts,
                    borderColor: bc,
                    backgroundColor: fillColorFromLineColor(bc),
                    fill: true,
                    tension: 0.35,
                    spanGaps: false,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: bc,
                    pointBorderWidth: 2,
                  },
                ],
              },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 10, right: 6, bottom: 2, left: 4 } },
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                  legend: { display: false },
                  tooltip: {
                    callbacks: {
                      title: function (items) {
                        return items && items[0] ? String(items[0].label || '') : '';
                      },
                      label: function (ctx) {
                        var v =
                          ctx.parsed && ctx.parsed.y != null && !isNaN(ctx.parsed.y)
                            ? ctx.parsed.y
                            : ctx.raw;
                        if (v == null || v === '') return '—';
                        var n = Number(v);
                        if (isNaN(n)) return String(v);
                        var isInt = Math.abs(n - Math.round(n)) < 1e-9;
                        return isInt
                          ? 'Jumlah: ' + n.toLocaleString('id-ID')
                          : 'Jumlah: ' + n.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 });
                      },
                    },
                  },
                },
                scales: {
                  x: {
                    offset: true,
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { size: 9, weight: '600' }, maxRotation: 0, autoSkip: false, color: '#64748b' },
                  },
                  y: {
                    beginAtZero: true,
                    grid: {
                      display: true,
                      drawBorder: false,
                      color: 'rgba(148, 163, 184, 0.45)',
                      lineWidth: 1,
                      borderDash: [2, 4],
                    },
                    ticks: { display: false },
                    border: { display: false },
                  },
                },
              },
            });
          } catch (err) {
            return null;
          }
        }
        function createPeerSapGroupedBarChart(canvas, weekLabels, series) {
          if (typeof Chart === 'undefined' || !canvas) return null;
          var ctx = canvas.getContext('2d');
          if (!ctx) return null;
          var lbls = Array.isArray(weekLabels) ? weekLabels : [];
          var sers = Array.isArray(series) ? series : [];
          if (!lbls.length || !sers.length) return null;
          var n = lbls.length;
          var datasets = sers.map(function (s) {
            var raw = Array.isArray(s.data) ? s.data : [];
            var data = [];
            var i;
            for (i = 0; i < n; i++) {
              var nv = Number(raw[i]);
              data.push(isNaN(nv) ? 0 : nv);
            }
            return {
              label: s.label != null ? String(s.label) : '',
              data: data,
              backgroundColor: s.backgroundColor || 'rgba(100, 116, 139, 0.88)',
              borderWidth: 0,
              borderRadius: 3,
              maxBarThickness: 16,
            };
          });
          try {
            return new Chart(ctx, {
              type: 'bar',
              data: { labels: lbls, datasets: datasets },
              options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 6, right: 4, bottom: 2, left: 4 } },
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                  legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                      boxWidth: 10,
                      boxHeight: 10,
                      padding: 6,
                      font: { size: 8, family: 'Poppins, system-ui, sans-serif' },
                      color: '#475569',
                    },
                  },
                  tooltip: {
                    callbacks: {
                      label: function (ctx) {
                        var lab = ctx.dataset.label || '';
                        var v =
                          ctx.parsed && ctx.parsed.y != null && !isNaN(ctx.parsed.y) ? ctx.parsed.y : ctx.raw;
                        if (v == null || v === '') return lab;
                        var num = Number(v);
                        if (isNaN(num)) return lab + ': ' + String(v);
                        var isInt = Math.abs(num - Math.round(num)) < 1e-9;
                        return (
                          lab +
                          ': ' +
                          (isInt
                            ? num.toLocaleString('id-ID')
                            : num.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 }))
                        );
                      },
                    },
                  },
                },
                scales: {
                  x: {
                    stacked: false,
                    offset: true,
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { size: 10, weight: '700' }, maxRotation: 0, autoSkip: false, color: '#475569' },
                  },
                  y: {
                    beginAtZero: true,
                    stacked: false,
                    grid: { color: 'rgba(0, 0, 0, 0.06)', drawBorder: false },
                    ticks: {
                      font: { size: 8 },
                      maxTicksLimit: 7,
                      callback: function (val) {
                        return Number(val).toLocaleString('id-ID');
                      },
                    },
                    border: { display: false },
                  },
                },
              },
            });
          } catch (err) {
            return null;
          }
        }
        function resizePeerDeviationModalCharts() {
          peerDeviationChartInstances.forEach(function (ch) {
            try {
              if (ch && typeof ch.resize === 'function') ch.resize();
            } catch (e) {}
          });
        }
        function initPeerDeviationModalCharts() {
          if (typeof Chart === 'undefined') return;
          destroyPeerDeviationModalCharts();
          var modal = document.getElementById('peer-deviation-category-modal');
          if (!modal) return;
          var sapEl = document.getElementById('peer-deviation-modal-sap-chart');
          if (sapEl) {
            var sl = [];
            var ss = [];
            try {
              sl = JSON.parse(sapEl.getAttribute('data-sap-labels') || '[]');
              ss = JSON.parse(sapEl.getAttribute('data-sap-series') || '[]');
            } catch (e) {
              sl = [];
              ss = [];
            }
            var sch = createPeerSapGroupedBarChart(sapEl, sl, ss);
            if (sch) peerDeviationChartInstances.push(sch);
          }
          var pjbEl = document.getElementById('peer-deviation-modal-pjb-chart');
          if (pjbEl) {
            var pl = [];
            var ps = [];
            try {
              pl = JSON.parse(pjbEl.getAttribute('data-pjb-labels') || '[]');
              ps = JSON.parse(pjbEl.getAttribute('data-pjb-series') || '[]');
            } catch (e) {
              pl = [];
              ps = [];
            }
            var pch = createPeerSapGroupedBarChart(pjbEl, pl, ps);
            if (pch) peerDeviationChartInstances.push(pch);
          }
          var pelaporEl = document.getElementById('peer-deviation-modal-pelapor-chart');
          if (pelaporEl) {
            var pWeeks = [];
            var pVals = [];
            try {
              pWeeks = JSON.parse(pelaporEl.getAttribute('data-pelapor-weeks') || '[]');
              pVals = JSON.parse(pelaporEl.getAttribute('data-pelapor-values') || '[]');
            } catch (e) {
              pWeeks = [];
              pVals = [];
            }
            var pelCh = createPeerDeviationLineChart(pelaporEl, pWeeks, pVals, '#1d4ed8');
            if (pelCh) peerDeviationChartInstances.push(pelCh);
          }
          modal.querySelectorAll('canvas.peer-deviation-cat-chart').forEach(function (el) {
            var vals = [];
            var wks = [];
            try {
              vals = JSON.parse(el.getAttribute('data-values') || '[]');
              wks = JSON.parse(el.getAttribute('data-weeks') || '[]');
            } catch (e) {
              vals = [];
              wks = [];
            }
            var lc = lineColorForDeviation(el, '#2563eb');
            var cch = createPeerDeviationLineChart(el, wks, vals, lc);
            if (cch) peerDeviationChartInstances.push(cch);
          });
          resizePeerDeviationModalCharts();
          requestAnimationFrame(function () {
            resizePeerDeviationModalCharts();
          });
        }

        function selYearClass(on) {
          return on
            ? 'peer-modal-year flex-1 rounded-lg border border-primary py-2.5 text-sm font-bold transition-colors bg-primary text-white shadow-sm'
            : 'peer-modal-year flex-1 rounded-lg border border-outline-variant/20 py-2.5 text-sm font-bold transition-colors bg-[#f8fafc] text-on-surface-variant hover:bg-surface-container-high';
        }
        function selMonthClass(on) {
          return on
            ? 'peer-modal-month rounded-lg border border-primary py-2 text-[10px] font-bold uppercase tracking-wide transition-colors bg-primary text-white shadow-sm'
            : 'peer-modal-month rounded-lg border border-outline-variant/15 py-2 text-[10px] font-bold uppercase tracking-wide transition-colors bg-white text-on-surface-variant hover:bg-surface-container-high';
        }
        function selAllDataClass(on) {
          return on
            ? 'peer-modal-all-data mb-4 w-full rounded-xl border border-primary bg-primary py-3 text-sm font-bold text-white shadow-sm transition-colors'
            : 'peer-modal-all-data mb-4 w-full rounded-xl border border-outline-variant/20 bg-[#f8fafc] py-3 text-sm font-bold text-on-surface-variant transition-colors hover:bg-surface-container-high';
        }
        function syncModalHighlight() {
          if (allDataBtn) allDataBtn.className = selAllDataClass(tempAll);
          document.querySelectorAll('.peer-modal-year').forEach(function (b) {
            var on = !tempAll && +b.getAttribute('data-year') === tempYear;
            b.className = selYearClass(on);
          });
          document.querySelectorAll('.peer-modal-month').forEach(function (b) {
            var on = !tempAll && +b.getAttribute('data-month') === tempMonth;
            b.className = selMonthClass(on);
          });
        }
        function openWeeklyModal() {
          tempAll = state.all;
          tempYear = state.year;
          tempMonth = state.month;
          syncModalHighlight();
          if (modal) {
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
          }
        }
        function closeWeeklyModal() {
          if (modal) {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
          }
        }
        function renderKpi(kpi, periodScope) {
          if (!kpi) return;
          var hintEl = document.getElementById('peer-kpi-completion-hint');
          if (hintEl && periodScope) {
            hintEl.textContent =
              periodScope === 'month'
                ? 'Selesai (CLOSED/SELESAI) ÷ total kejadian pada bulan yang dipilih'
                : 'Selesai (CLOSED/SELESAI) ÷ total kejadian (seluruh data)';
          }
          var totalEl = document.getElementById('peer-kpi-total');
          var trendEl = document.getElementById('peer-kpi-total-trend');
          var compEl = document.getElementById('peer-kpi-completion');
          var deltaEl = document.getElementById('peer-kpi-completion-delta');
          var barEl = document.getElementById('peer-kpi-completion-bar');
          var compPctEl = document.getElementById('peer-kpi-pelaksanaan-compliance');
          var compCountEl = document.getElementById('peer-kpi-pelaksanaan-compliance-count');
          if (totalEl) totalEl.textContent = Number(kpi.total_cases != null ? kpi.total_cases : 0).toLocaleString('id-ID');
          if (trendEl) {
            var pct = kpi.total_cases_trend_pct;
            var label = kpi.total_cases_trend_label != null ? String(kpi.total_cases_trend_label) : '—';
            if (pct !== null && pct !== undefined && !isNaN(Number(pct))) {
              var n = Number(pct);
              var down = n <= 0;
              var icon = down ? 'trending_down' : 'trending_up';
              var color = down ? 'text-[#059669]' : 'text-error';
              trendEl.innerHTML =
                '<p class="text-[11px] font-bold flex items-center gap-1 ' +
                color +
                '"><span class="material-symbols-outlined text-xs" data-icon="' +
                icon +
                '">' +
                icon +
                '</span>' +
                label.replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                '</p>';
            } else {
              trendEl.innerHTML =
                '<p class="text-on-surface-variant text-[11px] font-medium">' +
                label.replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                '</p>';
            }
          }
          var hrStack = document.getElementById('peer-kpi-hazard-matrix-column');
          if (hrStack) {
            var hrTotals = hrStack.querySelectorAll('.peer-kpi-hr-stack-total');
            var hrTrends = hrStack.querySelectorAll('.peer-kpi-hr-stack-trend');
            var numStr = Number(kpi.total_cases != null ? kpi.total_cases : 0).toLocaleString('id-ID');
            for (var hi = 0; hi < hrTotals.length; hi++) {
              if (hrTotals[hi].getAttribute('data-gr-json') === '1') continue;
              hrTotals[hi].textContent = numStr;
            }
            var pctH = kpi.total_cases_trend_pct;
            var labelH = kpi.total_cases_trend_label != null ? String(kpi.total_cases_trend_label) : '—';
            var trendInnerH = '';
            if (pctH !== null && pctH !== undefined && !isNaN(Number(pctH))) {
              var nH = Number(pctH);
              var downH = nH <= 0;
              var iconH = downH ? 'trending_down' : 'trending_up';
              var colorH = downH ? 'text-[#059669]' : 'text-error';
              trendInnerH =
                '<p class="text-[11px] font-bold flex items-center gap-1 ' +
                colorH +
                '"><span class="material-symbols-outlined text-xs" data-icon="' +
                iconH +
                '">' +
                iconH +
                '</span>' +
                labelH.replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                '</p>';
            } else {
              trendInnerH =
                '<p class="text-on-surface-variant text-[11px] font-medium">' +
                labelH.replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                '</p>';
            }
            for (var hj = 0; hj < hrTrends.length; hj++) {
              hrTrends[hj].innerHTML = trendInnerH;
            }
            var briefTotalEl = document.getElementById('peer-kpi-brief-total');
            var briefGrEl = document.getElementById('peer-kpi-brief-valid-gr');
            var briefCompEl = document.getElementById('peer-kpi-brief-completion');
            var briefTrendEl = document.getElementById('peer-kpi-brief-trend');
            if (briefTotalEl) briefTotalEl.textContent = numStr;
            if (briefGrEl) {
              briefGrEl.textContent = Number(
                kpi.peer_pressure_compliance_comply != null ? kpi.peer_pressure_compliance_comply : 0
              ).toLocaleString('id-ID');
            }
            if (briefCompEl) {
              var crB = Number(kpi.completion_rate != null ? kpi.completion_rate : 0);
              briefCompEl.textContent =
                (isNaN(crB) ? 0 : crB).toLocaleString('id-ID', {
                  minimumFractionDigits: 1,
                  maximumFractionDigits: 1,
                }) + '%';
            }
            if (briefTrendEl) briefTrendEl.innerHTML = trendInnerH;
          }
          var tbcCardEl = document.getElementById('peer-kpi-tbc-high-card');
          if (tbcCardEl) {
            var tbcTot = tbcCardEl.querySelector('.peer-kpi-tbc-high-total');
            var tbcTrend = tbcCardEl.querySelector('.peer-kpi-tbc-high-trend');
            if (tbcTot && tbcTot.getAttribute('data-json-driven') !== '1') {
              tbcTot.textContent = Number(kpi.total_cases != null ? kpi.total_cases : 0).toLocaleString('id-ID');
            }
            if (tbcTrend && tbcTrend.getAttribute('data-json-driven') !== '1') {
              var pct2 = kpi.total_cases_trend_pct;
              var label2 = kpi.total_cases_trend_label != null ? String(kpi.total_cases_trend_label) : '—';
              if (pct2 !== null && pct2 !== undefined && !isNaN(Number(pct2))) {
                var n2 = Number(pct2);
                var down2 = n2 <= 0;
                var icon2 = down2 ? 'trending_down' : 'trending_up';
                var color2 = down2 ? 'text-[#059669]' : 'text-error';
                tbcTrend.innerHTML =
                  '<p class="text-[11px] font-bold flex items-center gap-1 ' +
                  color2 +
                  '"><span class="material-symbols-outlined text-xs" data-icon="' +
                  icon2 +
                  '">' +
                  icon2 +
                  '</span>' +
                  label2.replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                  '</p>';
              } else {
                tbcTrend.innerHTML =
                  '<p class="text-on-surface-variant text-[11px] font-medium">' +
                  label2.replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                  '</p>';
              }
            }
          }
          if (compEl) {
            var cr = Number(kpi.completion_rate != null ? kpi.completion_rate : 0);
            compEl.textContent = (isNaN(cr) ? 0 : cr).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + '%';
          }
          if (deltaEl) {
            var d = kpi.completion_rate_delta_pp;
            if (d !== null && d !== undefined && !isNaN(Number(d))) {
              var dn = Number(d);
              deltaEl.className =
                'text-[11px] font-bold shrink-0 ' + (dn >= 0 ? 'text-[#16a34a]' : 'text-error');
              deltaEl.textContent = (dn >= 0 ? '+' : '') + dn.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + ' p.p.';
            } else {
              deltaEl.className = 'text-[11px] font-bold text-on-surface-variant shrink-0';
              deltaEl.textContent = '—';
            }
          }
          if (barEl) {
            var w = Number(kpi.completion_rate != null ? kpi.completion_rate : 0);
            if (isNaN(w)) w = 0;
            w = Math.min(100, Math.max(0, w));
            barEl.style.width = w + '%';
          }
          if (compPctEl) {
            var pp = Number(kpi.peer_pressure_compliance_pct != null ? kpi.peer_pressure_compliance_pct : 0);
            var pv = (isNaN(pp) ? 0 : pp).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
            compPctEl.innerHTML = pv + '<span class="text-2xl font-bold">%</span>';
          }
          if (compCountEl) {
            var cc = Number(kpi.peer_pressure_compliance_comply != null ? kpi.peer_pressure_compliance_comply : 0);
            var ct = Number(kpi.peer_pressure_compliance_total != null ? kpi.peer_pressure_compliance_total : 0);
            if (isNaN(cc)) cc = 0;
            if (isNaN(ct)) ct = 0;
            compCountEl.textContent = cc + '/' + ct;
          }
          syncComplianceModalFromKpi(kpi, periodScope);
        }
        function syncComplianceModalFromKpi(kpi, periodScope) {
          if (!kpi || typeof kpi !== 'object') return;
          var periodEl = document.getElementById('peer-compliance-modal-period');
          if (periodEl && periodScope) {
            periodEl.textContent =
              periodScope === 'month'
                ? 'Periode: filter chart (tanggal temuan dalam bulan yang dipilih).'
                : 'Periode: seluruh data kejadian (tanpa filter bulan).';
          }
          var pct = Number(kpi.peer_pressure_compliance_pct != null ? kpi.peer_pressure_compliance_pct : 0);
          if (isNaN(pct)) pct = 0;
          var cc = Number(kpi.peer_pressure_compliance_comply != null ? kpi.peer_pressure_compliance_comply : 0);
          var ct = Number(kpi.peer_pressure_compliance_total != null ? kpi.peer_pressure_compliance_total : 0);
          if (isNaN(cc)) cc = 0;
          if (isNaN(ct)) ct = 0;
          var pctStr = pct.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
          var sumPct = document.getElementById('peer-compliance-modal-summary-pct');
          if (sumPct) {
            sumPct.innerHTML = pctStr + '<span class="text-2xl font-bold">%</span>';
          }
          var sumC = document.getElementById('peer-compliance-modal-summary-count');
          var sumT = document.getElementById('peer-compliance-modal-summary-total');
          if (sumC) sumC.textContent = String(cc);
          if (sumT) sumT.textContent = String(ct);
          var bar = document.getElementById('peer-compliance-modal-summary-bar');
          if (bar) {
            var w = Math.min(100, Math.max(0, pct));
            bar.style.width = w + '%';
          }
          var nc = Math.max(0, ct - cc);
          var bComply = document.getElementById('peer-compliance-brief-comply');
          var bNon = document.getElementById('peer-compliance-brief-noncomply');
          var bTot = document.getElementById('peer-compliance-brief-total');
          var bNar = document.getElementById('peer-compliance-brief-narrative');
          if (bComply) bComply.textContent = cc.toLocaleString('id-ID');
          if (bNon) bNon.textContent = nc.toLocaleString('id-ID');
          if (bTot) bTot.textContent = ct.toLocaleString('id-ID');
          if (bNar) {
            if (ct === 0) {
              bNar.textContent =
                'Belum ada kejadian terlacak pada periode ini (hanya lima kategori deviasi tertentu yang dihitung).';
            } else {
              var pctFmt = pct.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
              bNar.innerHTML =
                'Dari ' +
                ct.toLocaleString('id-ID') +
                ' kejadian terlacak: <strong class="text-emerald-800">' +
                cc.toLocaleString('id-ID') +
                ' comply</strong> dan <strong class="text-red-800">' +
                nc.toLocaleString('id-ID') +
                ' tidak comply</strong>. Persentase di atas = comply ÷ total × 100 (' +
                pctFmt +
                '%).';
            }
          }
        }
        function renderInsightCards(ic) {
          if (!ic || typeof ic !== 'object') return;
          var root = document.getElementById('peer-insight-cards-root');
          if (!root) return;
          var dev = ic.deviation || {};
          var co = ic.compliance || {};
          var locs = ic.locations || [];
          var prof = ic.profiling_pelanggar || [];
          var cg = dev.conic_gradient || 'conic-gradient(rgb(241 245 249) 0% 100%)';
          var rot = Number(co.triangle_rotate_deg != null ? co.triangle_rotate_deg : 12);
          if (isNaN(rot)) rot = 12;
          function pctStr(x) {
            var n = Number(x != null ? x : 0);
            if (isNaN(n)) n = 0;
            return n.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
          }
          function pct1(x) {
            var n = Number(x != null ? x : 0);
            if (isNaN(n)) n = 0;
            return n.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });
          }
          var cats = dev.categories || [];
          var segRows = cats
            .map(function (row) {
              var name = row.kategori_deviasi != null ? String(row.kategori_deviasi) : '—';
              var p = Number(row.pct != null ? row.pct : 0);
              if (isNaN(p)) p = 0;
              var col = row.color != null ? String(row.color) : 'hsl(215 14% 72%)';
              col = col.replace(/[<>"']/g, '');
              return (
                '<div class="flex justify-between items-center gap-2 text-xs">' +
                '<span class="flex min-w-0 flex-1 items-center gap-2">' +
                '<span class="h-2.5 w-2.5 shrink-0 rounded-full shadow-sm ring-1 ring-black/5" style="background:' +
                col +
                '"></span>' +
                '<span class="truncate" title="' +
                escAttr(name) +
                '">' +
                escHtml(name) +
                '</span></span>' +
                '<span class="shrink-0 font-bold tabular-nums">' +
                p.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) +
                '%</span></div>'
              );
            })
            .join('');
          if (!segRows) {
            segRows = '<p class="text-[11px] text-on-surface-variant">Belum ada data.</p>';
          }
          var locHtml = locs
            .map(function (loc) {
              var bw = Number(loc.bar_pct != null ? loc.bar_pct : 0);
              if (isNaN(bw)) bw = 0;
              bw = Math.min(100, Math.max(0, bw));
              var cnt = parseInt(String(loc.count != null ? loc.count : 0), 10) || 0;
              return (
                '<div class="space-y-2">' +
                '<div class="flex justify-between text-[10px] font-bold uppercase tracking-wider">' +
                '<span class="min-w-0 truncate pr-2" title="' +
                escAttr(loc.name || '') +
                '">' +
                escHtml(loc.name || '—') +
                '</span>' +
                '<span class="shrink-0 text-primary tabular-nums">' +
                cnt +
                '</span></div>' +
                '<div class="w-full bg-[#f1f5f9] h-2.5 rounded-full overflow-hidden border border-outline-variant/10 shadow-inner">' +
                '<div class="bg-primary h-full rounded-full transition-[width] duration-300" style="width:' +
                bw +
                '%"></div></div></div>'
              );
            })
            .join('');
          if (!locHtml) {
            locHtml = '<p class="text-[11px] text-on-surface-variant">Belum ada data lokasi.</p>';
          }
          var profHtml = prof
            .map(function (p) {
              var nama = p.nama != null ? String(p.nama) : '—';
              var sid = p.sid != null ? String(p.sid) : '—';
              var kasus = parseInt(String(p.kasus != null ? p.kasus : 0), 10) || 0;
              var share = Number(p.insiden_share_pct != null ? p.insiden_share_pct : 0);
              if (isNaN(share)) share = 0;
              var foto = p.foto_url != null ? String(p.foto_url).trim() : '';
              foto = foto.replace(/[<>"']/g, '');
              var ini = nama.length ? nama.charAt(0) : '?';
              var av = foto
                ? '<img src="' +
                  escAttr(foto) +
                  '" alt="" class="h-11 w-11 shrink-0 rounded-full object-cover ring-1 ring-outline-variant/20" loading="lazy" decoding="async" />'
                : '<div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary/12 text-sm font-bold uppercase text-primary ring-1 ring-outline-variant/20" aria-hidden="true">' +
                  escHtml(ini) +
                  '</div>';
              return (
                '<div class="peer-profiling-row flex items-center gap-3 rounded-xl border border-outline-variant/15 bg-[#fafbfc] p-2.5 cursor-pointer transition-colors hover:bg-[#f1f5f9] hover:border-primary/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/30" role="button" tabindex="0" data-sid="' +
                escAttr(sid) +
                '" data-nama="' +
                escAttr(nama) +
                '">' +
                av +
                '<div class="min-w-0 flex-1">' +
                '<p class="truncate text-xs font-bold text-on-surface" title="' +
                escAttr(nama) +
                '">' +
                escHtml(nama) +
                '</p>' +
                '<p class="font-mono text-[10px] text-on-surface-variant">' +
                escHtml(sid) +
                '</p></div>' +
                '<div class="shrink-0 text-right">' +
                '<p class="text-base font-extrabold tabular-nums leading-tight text-primary">' +
                kasus +
                '×</p>' +
                '<p class="text-[9px] text-on-surface-variant">' +
                share.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) +
                '% insiden</p></div></div>'
              );
            })
            .join('');
          if (!profHtml) {
            profHtml = '<p class="text-[11px] text-on-surface-variant">Belum ada data pelanggar pada periode ini.</p>';
          }
          root.innerHTML =
            '<div class="bg-white p-6 rounded-2xl anchored-card">' +
            '<h3 class="font-headline font-bold text-[11px] mb-6 uppercase tracking-widest text-on-surface-variant">Deviation Category</h3>' +
            '<div class="flex justify-center mb-8">' +
            '<div class="relative w-36 h-36 rounded-full p-[14px] shadow-inner" style="background:' +
            cg +
            '">' +
            '<div class="flex h-full w-full items-center justify-center rounded-full bg-white">' +
            '<div class="text-center">' +
            '<span class="block font-extrabold text-2xl tabular-nums">' +
            escHtml(dev.total_label || '0') +
            '</span>' +
            '<span class="block text-[9px] uppercase font-bold text-on-surface-variant">Total</span>' +
            '</div></div></div></div>' +
            '<div class="max-h-64 space-y-3 overflow-y-auto overflow-x-hidden pr-1">' +
            segRows +
            '</div>' +
            '</div>' +
            '<div class="bg-white p-6 rounded-2xl anchored-card">' +
            '<h3 class="font-headline font-bold text-[11px] mb-6 uppercase tracking-widest text-on-surface-variant">Compliance Radar</h3>' +
            '<div class="relative flex aspect-square w-full items-center justify-center">' +
            '<div class="absolute inset-0 flex items-center justify-center">' +
            '<div class="w-[85%] h-[85%] border border-outline-variant/20 rounded-full"></div>' +
            '<div class="absolute w-[60%] h-[60%] border border-outline-variant/20 rounded-full"></div>' +
            '<div class="absolute w-[35%] h-[35%] border border-outline-variant/20 rounded-full"></div></div>' +
            '<div class="w-0 h-0 scale-125 cursor-crosshair border-b-[70px] border-l-[45px] border-r-[45px] border-b-primary/40 border-l-transparent border-r-transparent transition-transform hover:scale-150" style="transform:rotate(' +
            rot +
            'deg)"></div>' +
            '<div class="absolute inset-0 flex flex-col justify-between p-1 text-center text-[10px] font-bold uppercase tracking-tighter text-on-surface-variant">' +
            '<span class="flex flex-col items-center gap-0.5 leading-tight"><span>BeRecord</span><span class="text-[9px] font-bold tabular-nums text-primary">' +
            pctStr(co.berecord_pct) +
            '%</span></span>' +
            '<div class="flex w-full justify-between px-1">' +
            '<span class="flex flex-col items-center gap-0.5 leading-tight"><span>Evidence</span><span class="text-[9px] font-bold tabular-nums text-primary">' +
            pctStr(co.evidence_pct) +
            '%</span></span>' +
            '<span class="flex flex-col items-center gap-0.5 leading-tight"><span>Size</span><span class="text-[9px] font-bold tabular-nums text-primary">' +
            pctStr(co.size_pct) +
            '%</span></span></div>' +
            '<div class="flex w-full justify-between px-3 pb-3">' +
            '<span class="flex flex-col items-center gap-0.5 leading-tight"><span>H+1</span><span class="text-[9px] font-bold tabular-nums text-primary">' +
            pctStr(co.h1_pct) +
            '%</span></span>' +
            '<span class="flex flex-col items-center gap-0.5 leading-tight"><span>Duration</span><span class="text-[9px] font-bold tabular-nums text-primary">' +
            escHtml(co.duration_label || '—') +
            '</span></span></div></div></div></div>' +
            '<div class="bg-white p-6 rounded-2xl anchored-card">' +
            '<h3 class="font-headline font-bold text-[11px] mb-6 uppercase tracking-widest text-on-surface-variant">Location Analysis</h3>' +
            '<div class="peer-loc-scroll max-h-96 space-y-5 overflow-y-auto overflow-x-hidden pr-1 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">' +
            locHtml +
            '</div></div>' +
            '<div class="bg-white p-6 rounded-2xl anchored-card">' +
            '<h3 class="font-headline font-bold text-[11px] mb-2 uppercase tracking-widest text-on-surface-variant">Profiling Analysis</h3>' +
            '<p class="mb-4 text-[10px] leading-snug text-on-surface-variant">Pelanggar dengan kejadian terbanyak; korelasi = porsi terhadap total insiden pada periode yang sama.</p>' +
            '<div class="max-h-80 space-y-2.5 overflow-y-auto overflow-x-hidden pr-1 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">' +
            profHtml +
            '</div></div>';
          fillDeviationCategoryModal(dev);
        }
        function fnv1aUtf8Peer(str) {
          var bytes = new TextEncoder().encode(str);
          var h = 2166136261 >>> 0;
          for (var i = 0; i < bytes.length; i++) {
            h = Math.imul(h ^ bytes[i], 16777619) >>> 0;
          }
          return h >>> 0;
        }
        function weeklyValuesFromJumlah(jumlah, name) {
          var wks = peerDeviationSparkWeeks && peerDeviationSparkWeeks.length ? peerDeviationSparkWeeks : ['W11', 'W12', 'W13', 'W14'];
          var n = wks.length;
          var out = [];
          var i;
          if (jumlah <= 0) {
            for (i = 0; i < n; i++) out.push(0);
            return out;
          }
          var h = fnv1aUtf8Peer(name);
          var weights = [];
          var sumW = 0;
          for (i = 0; i < n; i++) {
            var w = 0.12 + (((h >> (i * 7)) & 0xff) / 255) * 0.88;
            weights.push(w);
            sumW += w;
          }
          var acc = 0;
          for (i = 0; i < n - 1; i++) {
            var v = Math.max(0, Math.round((jumlah * weights[i]) / sumW));
            out.push(v);
            acc += v;
          }
          out.push(Math.max(0, jumlah - acc));
          return out;
        }
        function fillDeviationCategoryModal(dev) {
          if (!dev || typeof dev !== 'object') return;
          var sapTbodyEarly = document.getElementById('peer-deviation-sap-tbody');
          var pjbTbodyEarly = document.getElementById('peer-deviation-pjb-tbody');
          var pelaporTbodyEarly = document.getElementById('peer-deviation-pelapor-tbody');
          if (
            (sapTbodyEarly && sapTbodyEarly.getAttribute('data-static-sap') === '1') ||
            (pjbTbodyEarly && pjbTbodyEarly.getAttribute('data-static-pjb') === '1') ||
            (pelaporTbodyEarly && pelaporTbodyEarly.getAttribute('data-static-pelapor') === '1')
          ) {
            return;
          }
          destroyPeerDeviationModalCharts();
          var catRoot = document.getElementById('peer-deviation-modal-categories');
          var totalFoot = document.getElementById('peer-deviation-modal-total');
          var kpiBig = document.querySelector('#peer-kpi-deviation-card .font-headline.font-extrabold.text-4xl');
          var cats = dev.categories || [];
          var apiTotal = parseInt(String(dev.total != null ? dev.total : 0), 10) || 0;
          if (kpiBig && kpiBig.getAttribute('data-json-driven') !== '1') {
            kpiBig.textContent = apiTotal.toLocaleString('id-ID');
          }
          var sum = 0;
          var wks =
            typeof peerDeviationSparkWeeks !== 'undefined' && peerDeviationSparkWeeks && peerDeviationSparkWeeks.length
              ? peerDeviationSparkWeeks
              : ['W11', 'W12', 'W13', 'W14'];
          var rows = cats
            .map(function (row, idx) {
              var name = row.kategori_deviasi != null ? String(row.kategori_deviasi) : '—';
              var j = parseInt(String(row.jumlah != null ? row.jumlah : 0), 10) || 0;
              sum += j;
              var col = row.color != null ? String(row.color) : 'hsl(215 14% 62%)';
              col = col.replace(/[<>"']/g, '');
              var vals = weeklyValuesFromJumlah(j, name);
              return (
                '<div class="overflow-hidden rounded border border-slate-300 bg-white shadow-[inset_0_1px_0_rgba(255,255,255,0.7)]">' +
                '<div class="flex items-center justify-between gap-2 border-b border-slate-300 bg-slate-100 px-2.5 py-1.5">' +
                '<p class="min-w-0 truncate text-[10px] font-bold text-slate-700">' +
                (idx + 1) +
                '. ' +
                escHtml(name) +
                '</p>' +
                '<span class="shrink-0 text-[10px] font-extrabold tabular-nums text-slate-700">' +
                j.toLocaleString('id-ID') +
                '</span></div>' +
                '<div class="bg-[#f8fbff] px-2 py-1.5">' +
                '<div class="relative h-28 w-full min-h-[7rem]">' +
                '<canvas class="peer-deviation-cat-chart max-h-full w-full" data-values=\'' +
                JSON.stringify(vals) +
                '\' data-weeks=\'' +
                JSON.stringify(wks) +
                '\' data-line-color="' +
                escAttr(col) +
                '"></canvas></div></div></div>'
              );
            })
            .join('');
          if (!rows) {
            rows =
              '<div class="col-span-full px-3 py-8 text-center text-[11px] text-on-surface-variant">Belum ada data kategori deviasi.</div>';
          }
          if (catRoot) catRoot.innerHTML = rows;
          var footerVal = sum > 0 ? sum : apiTotal;
          if (totalFoot) totalFoot.textContent = footerVal.toLocaleString('id-ID');
          setTimeout(function () {
            initPeerDeviationModalCharts();
            resizePeerDeviationModalCharts();
          }, 0);
        }
        function escHtml(s) {
          if (s == null) return '';
          return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
        }
        function peerHighlightQueryString() {
          var yEl = document.getElementById('peer-dashboard-year');
          var mEl = document.getElementById('peer-dashboard-month');
          if (yEl && mEl && !yEl.disabled && !mEl.disabled) {
            return 'year=' + encodeURIComponent(String(yEl.value)) + '&month=' + encodeURIComponent(String(mEl.value));
          }
          return '';
        }
        function loadPeerHighlightIssueRecommendation() {
          var loading = document.getElementById('peer-highlight-loading');
          var content = document.getElementById('peer-highlight-content');
          var errEl = document.getElementById('peer-highlight-error');
          var tbody = document.getElementById('peer-highlight-tbody');
          var meta = document.getElementById('peer-highlight-meta');
          var badge = document.getElementById('peer-highlight-badge');
          if (!tbody || !loading) return;
          if (errEl) errEl.classList.add('hidden');
          loading.classList.remove('hidden');
          if (content) content.classList.add('hidden');
          var qs = peerHighlightQueryString();
          var u = peerHighlightUrl + (qs ? '?' + qs : '');
          fetch(u, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
          })
            .then(function (r) {
              if (!r.ok) throw new Error('Gagal memuat ringkasan highlight');
              return r.json();
            })
            .then(function (data) {
              var rows = data.rows || [];
              tbody.innerHTML = rows
                .map(function (row) {
                  return (
                    '<tr class="align-top hover:bg-[#fafbfc]">' +
                    '<td class="px-4 py-4 text-[11px] font-bold text-primary sm:px-6">' +
                    escHtml(row.judul != null ? String(row.judul) : '—') +
                    '</td>' +
                    '<td class="border-l border-outline-variant/10 px-4 py-4 text-on-surface-variant sm:px-6">' +
                    escHtml(row.issue != null ? String(row.issue) : '') +
                    '</td>' +
                    '<td class="border-l border-outline-variant/10 px-4 py-4 sm:px-6">' +
                    escHtml(row.rekomendasi != null ? String(row.rekomendasi) : '') +
                    '</td></tr>'
                  );
                })
                .join('');
              if (!rows.length) {
                tbody.innerHTML =
                  '<tr><td colspan="3" class="px-4 py-8 text-center text-[11px] text-on-surface-variant sm:px-6">Belum ada baris ringkasan.</td></tr>';
              }
              if (meta) {
                var src = data.ai_used ? 'AI (Gemini)' : 'Fallback agregat';
                meta.textContent =
                  'Periode: ' +
                  (data.period_label != null ? String(data.period_label) : '—') +
                  ' · ' +
                  (data.generated_at != null ? String(data.generated_at) : '') +
                  ' · Sumber: ' +
                  src;
              }
              if (badge) {
                badge.textContent = data.ai_used ? 'AI' : 'Fallback';
                badge.classList.remove('hidden');
              }
              loading.classList.add('hidden');
              if (content) content.classList.remove('hidden');
            })
            .catch(function (e) {
              loading.classList.add('hidden');
              if (content) content.classList.add('hidden');
              if (errEl) {
                errEl.textContent = e.message || 'Gagal memuat ringkasan';
                errEl.classList.remove('hidden');
              }
            });
        }
        var peerHighlightRefresh = document.getElementById('peer-highlight-refresh');
        if (peerHighlightRefresh) {
          peerHighlightRefresh.addEventListener('click', function () {
            loadPeerHighlightIssueRecommendation();
          });
        }
        function escAttr(s) {
          if (s == null) return '';
          return String(s)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
        }
        function evalStatusDotClass(st) {
          if (st === 'critical') return 'bg-red-500';
          if (st === 'warning') return 'bg-amber-500';
          if (st === 'ok') return 'bg-emerald-500';
          return 'bg-slate-400';
        }
        function buildPeerRvKejadianDetailHtml(klist, sizeClass) {
          var isModal = sizeClass === 'modal';
          var th = isModal ? 'text-[10px] px-3 py-2' : 'text-[8px] px-2 py-1.5';
          var tdD = isModal ? 'text-[12px] px-3 py-2' : 'text-[9px] px-2 py-1';
          var minW = isModal ? 'min-w-[320px]' : 'min-w-[280px]';
          var rows = (klist || [])
            .map(function (kj) {
              var kat = kj.kategori_deviasi != null ? String(kj.kategori_deviasi) : '—';
              return (
                '<tr>' +
                '<td class="' +
                tdD +
                ' whitespace-nowrap font-medium tabular-nums text-on-surface">' +
                escHtml(kj.tanggal_label) +
                '</td>' +
                '<td class="' +
                tdD +
                ' text-on-surface-variant">' +
                escHtml(kat) +
                '</td></tr>'
              );
            })
            .join('');
          return (
            '<div class="overflow-x-auto rounded-lg border border-outline-variant/15 bg-white">' +
            '<table class="w-full ' +
            minW +
            ' border-collapse text-left text-on-surface">' +
            '<thead><tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-left font-bold uppercase tracking-wide text-on-surface-variant ' +
            th +
            '">' +
            '<th class="whitespace-nowrap">Tanggal</th><th>Kategori deviasi</th></tr></thead>' +
            '<tbody class="divide-y divide-outline-variant/10">' +
            rows +
            '</tbody></table></div>'
          );
        }
        function buildPeerRvCardRowsHtml(violators) {
          return violators
            .map(function (v) {
              var kasus = parseInt(String(v.kasus != null ? v.kasus : 0), 10) || 0;
              var detailTbl = buildPeerRvKejadianDetailHtml(v.kejadian_list, 'card');
              return (
                '<tr class="peer-rv-toggle cursor-pointer transition-colors hover:bg-[#f8fafc]" role="button" tabindex="0" aria-expanded="false">' +
                '<td class="max-w-[7rem] truncate px-2 py-1.5 font-medium" title="' +
                escAttr(v.nama || '') +
                '">' +
                escHtml(v.nama || '—') +
                '</td>' +
                '<td class="px-2 py-1.5 font-mono text-[9px] text-on-surface-variant">' +
                escHtml(v.sid || '—') +
                '</td>' +
                '<td class="max-w-[6rem] truncate px-2 py-1.5 text-on-surface-variant" title="' +
                escAttr(v.departemen || '') +
                '">' +
                escHtml(v.departemen || '—') +
                '</td>' +
                '<td class="whitespace-nowrap px-2 py-1.5 text-right">' +
                '<span class="inline-flex max-w-full flex-nowrap items-center justify-end gap-0.5">' +
                '<span class="shrink-0 font-bold tabular-nums">' +
                kasus +
                '×</span>' +
                '<span class="material-symbols-outlined peer-rv-chevron shrink-0 text-base leading-none text-on-surface-variant" aria-hidden="true">expand_more</span>' +
                '</span></td></tr>' +
                '<tr class="peer-rv-expand hidden bg-[#f8fafc]/90">' +
                '<td colspan="4" class="border-t border-outline-variant/10 px-3 py-2">' +
                '<p class="mb-1.5 text-[9px] font-bold uppercase tracking-wide text-on-surface-variant">Tanggal &amp; kategori deviasi</p>' +
                detailTbl +
                '</td></tr>'
              );
            })
            .join('');
        }
        function buildPeerRvModalRowsHtml(violators) {
          return violators
            .map(function (v) {
              var kasus = parseInt(String(v.kasus != null ? v.kasus : 0), 10) || 0;
              var detailTbl = buildPeerRvKejadianDetailHtml(v.kejadian_list, 'modal');
              return (
                '<tr class="peer-rv-toggle cursor-pointer transition-colors hover:bg-[#fafbfc]" role="button" tabindex="0" aria-expanded="false">' +
                '<td class="px-3 py-2 font-medium">' +
                escHtml(v.nama || '—') +
                '</td>' +
                '<td class="px-3 py-2 font-mono text-[11px] text-on-surface-variant">' +
                escHtml(v.sid || '—') +
                '</td>' +
                '<td class="px-3 py-2 text-on-surface-variant">' +
                escHtml(v.departemen || '—') +
                '</td>' +
                '<td class="whitespace-nowrap px-3 py-2 text-right">' +
                '<span class="inline-flex max-w-full flex-nowrap items-center justify-end gap-1">' +
                '<span class="shrink-0 font-bold tabular-nums">' +
                kasus +
                '×</span>' +
                '<span class="material-symbols-outlined peer-rv-chevron shrink-0 text-lg leading-none text-on-surface-variant" aria-hidden="true">expand_more</span>' +
                '</span></td></tr>' +
                '<tr class="peer-rv-expand hidden bg-[#f8fafc]/90">' +
                '<td colspan="4" class="border-t border-outline-variant/10 px-4 py-3">' +
                '<p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">Tanggal &amp; kategori deviasi</p>' +
                detailTbl +
                '</td></tr>'
              );
            })
            .join('');
        }
        function buildRecencyCardSectionHtml(rd) {
          if (!rd || !rd.latest || !rd.previous) return '';
          var gap = parseInt(String(rd.gap_days != null ? rd.gap_days : 0), 10) || 0;
          var lid = parseInt(String(rd.latest.kejadian_id != null ? rd.latest.kejadian_id : 0), 10) || 0;
          var pid = parseInt(String(rd.previous.kejadian_id != null ? rd.previous.kejadian_id : 0), 10) || 0;
          return (
            '<p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Jarak waktu pelanggaran berulang — data</p>' +
            '<p class="mb-3 text-[9px] leading-relaxed text-on-surface-variant">' +
            escHtml(rd.metric_explanation || '') +
            '</p>' +
            '<div class="overflow-x-auto rounded-xl border border-outline-variant/20 bg-white">' +
            '<table class="w-full min-w-[260px] text-left text-[10px] text-on-surface">' +
            '<tbody class="divide-y divide-outline-variant/10">' +
            '<tr><th class="w-[42%] whitespace-nowrap bg-[#f8fafc] px-2 py-2 align-top text-[9px] font-bold uppercase text-on-surface-variant">Pelanggar</th><td class="px-2 py-2 font-medium">' +
            escHtml(rd.pelanggar_nama || '—') +
            '</td></tr>' +
            '<tr><th class="whitespace-nowrap bg-[#f8fafc] px-2 py-2 align-top text-[9px] font-bold uppercase text-on-surface-variant">SID</th><td class="px-2 py-2 font-mono text-[9px] text-on-surface-variant">' +
            escHtml(rd.pelanggar_sid || '—') +
            '</td></tr>' +
            '<tr><th class="whitespace-nowrap bg-[#f8fafc] px-2 py-2 align-top text-[9px] font-bold uppercase text-on-surface-variant">Temuan terbaru</th><td class="px-2 py-2 align-top">' +
            '<div class="flex flex-col gap-0.5">' +
            '<span class="font-medium">' +
            escHtml(rd.latest.tanggal_label) +
            ' <span class="font-mono text-[9px] text-on-surface-variant">#' +
            lid +
            '</span></span>' +
            '<span class="text-[8px] leading-snug text-on-surface-variant"><span class="font-semibold">Kategori deviasi:</span> ' +
            escHtml(rd.latest.kategori_deviasi || '—') +
            '</span></div></td></tr>' +
            '<tr><th class="whitespace-nowrap bg-[#f8fafc] px-2 py-2 align-top text-[9px] font-bold uppercase text-on-surface-variant">Temuan sebelumnya</th><td class="px-2 py-2 align-top">' +
            '<div class="flex flex-col gap-0.5">' +
            '<span class="font-medium">' +
            escHtml(rd.previous.tanggal_label) +
            ' <span class="font-mono text-[9px] text-on-surface-variant">#' +
            pid +
            '</span></span>' +
            '<span class="text-[8px] leading-snug text-on-surface-variant"><span class="font-semibold">Kategori deviasi:</span> ' +
            escHtml(rd.previous.kategori_deviasi || '—') +
            '</span></div></td></tr>' +
            '<tr><th class="whitespace-nowrap bg-[#f8fafc] px-2 py-2 align-top text-[9px] font-bold uppercase text-on-surface-variant">Selisih kalender</th><td class="px-2 py-2 font-semibold tabular-nums">' +
            gap +
            ' hari</td></tr>' +
            '</tbody></table></div>' +
            '<p class="mt-2 text-[9px] text-on-surface-variant">' +
            escHtml(rd.footnote || '') +
            '</p>'
          );
        }
        function buildRecencyModalExtraHtml(rd) {
          if (!rd || !rd.latest || !rd.previous) return '';
          var gap = parseInt(String(rd.gap_days != null ? rd.gap_days : 0), 10) || 0;
          var lid = parseInt(String(rd.latest.kejadian_id != null ? rd.latest.kejadian_id : 0), 10) || 0;
          var pid = parseInt(String(rd.previous.kejadian_id != null ? rd.previous.kejadian_id : 0), 10) || 0;
          return (
            '<p class="mt-2 text-[12px] leading-relaxed text-on-surface-variant">' +
            escHtml(rd.metric_explanation || '') +
            '</p>' +
            '<div class="mt-3 overflow-x-auto rounded-lg border border-outline-variant/20 bg-white">' +
            '<table class="w-full min-w-[300px] text-left text-[12px] text-on-surface">' +
            '<tbody class="divide-y divide-outline-variant/10">' +
            '<tr><th class="w-[40%] whitespace-nowrap bg-[#f8fafc] px-3 py-2.5 align-top text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">Pelanggar</th><td class="px-3 py-2.5 font-medium">' +
            escHtml(rd.pelanggar_nama || '—') +
            '</td></tr>' +
            '<tr><th class="whitespace-nowrap bg-[#f8fafc] px-3 py-2.5 align-top text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">SID</th><td class="px-3 py-2.5 font-mono text-[11px] text-on-surface-variant">' +
            escHtml(rd.pelanggar_sid || '—') +
            '</td></tr>' +
            '<tr><th class="w-[40%] whitespace-nowrap bg-[#f8fafc] px-3 py-2.5 align-top text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">Temuan terbaru</th><td class="px-3 py-2.5 align-top">' +
            '<div class="flex flex-col gap-1">' +
            '<span class="font-medium">' +
            escHtml(rd.latest.tanggal_label) +
            ' <span class="font-mono text-[11px] text-on-surface-variant">#' +
            lid +
            '</span></span>' +
            '<span class="text-[11px] leading-snug text-on-surface-variant"><span class="font-semibold">Kategori deviasi:</span> ' +
            escHtml(rd.latest.kategori_deviasi || '—') +
            '</span></div></td></tr>' +
            '<tr><th class="whitespace-nowrap bg-[#f8fafc] px-3 py-2.5 align-top text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">Temuan sebelumnya</th><td class="px-3 py-2.5 align-top">' +
            '<div class="flex flex-col gap-1">' +
            '<span class="font-medium">' +
            escHtml(rd.previous.tanggal_label) +
            ' <span class="font-mono text-[11px] text-on-surface-variant">#' +
            pid +
            '</span></span>' +
            '<span class="text-[11px] leading-snug text-on-surface-variant"><span class="font-semibold">Kategori deviasi:</span> ' +
            escHtml(rd.previous.kategori_deviasi || '—') +
            '</span></div></td></tr>' +
            '<tr><th class="whitespace-nowrap bg-[#f8fafc] px-3 py-2.5 align-top text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">Selisih kalender</th><td class="px-3 py-2.5 font-semibold tabular-nums">' +
            gap +
            ' hari</td></tr>' +
            '</tbody></table></div>' +
            '<p class="mt-2 text-[10px] text-on-surface-variant">' +
            escHtml(rd.footnote || '') +
            '</p>'
          );
        }
        function buildDeviationVarietyModalHtml(dv) {
          if (!dv || typeof dv !== 'object') return '';
          var cats = dv.categories || [];
          var rows = cats
            .map(function (cat) {
              var k = escHtml(cat.kategori_deviasi != null ? String(cat.kategori_deviasi) : '—');
              var j = parseInt(String(cat.jumlah != null ? cat.jumlah : 0), 10) || 0;
              return (
                '<tr class="hover:bg-[#fafbfc]">' +
                '<td class="px-3 py-2">' +
                k +
                '</td>' +
                '<td class="px-3 py-2 text-right tabular-nums font-semibold">' +
                j +
                '</td>' +
                '</tr>'
              );
            })
            .join('');
          if (!rows) {
            rows =
              '<tr><td colspan="2" class="px-3 py-4 text-center text-[11px] text-on-surface-variant">Belum ada data kategori.</td></tr>';
          }
          return (
            '<div class="mt-3 overflow-x-auto rounded-lg border border-outline-variant/20 bg-white">' +
            '<table class="w-full min-w-[280px] text-left text-[12px] text-on-surface">' +
            '<thead><tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">' +
            '<th class="px-3 py-2">Kategori deviasi</th>' +
            '<th class="whitespace-nowrap px-3 py-2 text-right">Kejadian</th>' +
            '</tr></thead>' +
            '<tbody class="divide-y divide-outline-variant/10">' +
            rows +
            '</tbody></table></div>'
          );
        }
        function buildPeerCorrelationModalHtml(pc) {
          if (!pc || typeof pc !== 'object') return '';
          var def = escHtml(pc.definition || '');
          var tu = parseInt(String(pc.total_unique_pairs != null ? pc.total_unique_pairs : 0), 10) || 0;
          var g2 = parseInt(String(pc.pairs_with_freq_gte_2 != null ? pc.pairs_with_freq_gte_2 : 0), 10) || 0;
          var mx = parseInt(String(pc.max_pair_frequency != null ? pc.max_pair_frequency : 0), 10) || 0;
          var top = pc.top_pairs || [];
          var pairRows = top
            .map(function (tp) {
              var pl = escHtml(tp.pelanggar_sid != null ? String(tp.pelanggar_sid) : '—');
              var pr = escHtml(tp.peer_sid != null ? String(tp.peer_sid) : '—');
              var f = parseInt(String(tp.frekuensi != null ? tp.frekuensi : 0), 10) || 0;
              return (
                '<tr class="hover:bg-[#fafbfc]">' +
                '<td class="px-3 py-2 font-mono text-[11px]">' +
                pl +
                '</td>' +
                '<td class="px-3 py-2 font-mono text-[11px]">' +
                pr +
                '</td>' +
                '<td class="px-3 py-2 text-right tabular-nums font-semibold">' +
                f +
                '×</td>' +
                '</tr>'
              );
            })
            .join('');
          if (!pairRows) {
            pairRows =
              '<tr><td colspan="3" class="px-3 py-4 text-center text-[11px] text-on-surface-variant">Tidak ada pasangan berulang (frekuensi ≥ 2).</td></tr>';
          }
          return (
            '<p class="mt-2 text-[11px] leading-relaxed text-on-surface">' +
            def +
            '</p>' +
            '<div class="mt-3 overflow-x-auto rounded-lg border border-outline-variant/20 bg-white">' +
            '<table class="w-full min-w-[260px] text-left text-[12px] text-on-surface">' +
            '<thead><tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">' +
            '<th class="px-3 py-2">Metrik</th>' +
            '<th class="whitespace-nowrap px-3 py-2 text-right">Nilai</th>' +
            '</tr></thead>' +
            '<tbody class="divide-y divide-outline-variant/10">' +
            '<tr class="hover:bg-[#fafbfc]"><td class="px-3 py-2">Total pasangan unik (semua kejadian)</td><td class="px-3 py-2 text-right tabular-nums font-semibold">' +
            tu +
            '</td></tr>' +
            '<tr class="hover:bg-[#fafbfc]"><td class="px-3 py-2">Pasangan dengan frekuensi ≥ 2</td><td class="px-3 py-2 text-right tabular-nums font-semibold">' +
            g2 +
            '</td></tr>' +
            '<tr class="hover:bg-[#fafbfc]"><td class="px-3 py-2">Frekuensi maksimal satu pasangan</td><td class="px-3 py-2 text-right tabular-nums font-semibold">' +
            mx +
            ' kejadian</td></tr>' +
            '</tbody></table></div>' +
            '<div class="mt-3 overflow-x-auto rounded-lg border border-outline-variant/20 bg-white">' +
            '<table class="w-full min-w-[320px] text-left text-[12px] text-on-surface">' +
            '<thead><tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">' +
            '<th class="px-3 py-2">Pelanggar (SID)</th>' +
            '<th class="px-3 py-2">Peer (SID)</th>' +
            '<th class="whitespace-nowrap px-3 py-2 text-right">Frekuensi</th>' +
            '</tr></thead>' +
            '<tbody class="divide-y divide-outline-variant/10">' +
            pairRows +
            '</tbody></table></div>'
          );
        }
        function renderEvaluationSummary(es) {
          if (!es || typeof es !== 'object') return;
          var scopeEl = document.getElementById('peer-eval-scope-subtitle');
          if (scopeEl) {
            scopeEl.textContent = es.chart_period_month
              ? 'Sesuai periode chart: ' + (es.repeat_period_caption || '')
              : 'Sesuai periode chart: seluruh data';
          }
          var modalSub = document.getElementById('peer-eval-modal-subtitle');
          if (modalSub) {
            modalSub.textContent = es.chart_period_month
              ? 'Ringkasan per metrik & pelanggar repetitif · periode chart: ' + (es.repeat_period_caption || '')
              : 'Ringkasan per metrik & pelanggar repetitif · seluruh data';
          }
          var nar = document.getElementById('peer-eval-narrative');
          if (nar) nar.textContent = es.narrative || '';
          var rows = es.rows || [];
          var tbody = document.getElementById('peer-eval-tbody');
          if (tbody) {
            if (!rows.length) {
              tbody.innerHTML =
                '<tr><td colspan="3" class="px-3 py-6 text-center text-on-surface-variant">Belum ada data untuk evaluasi.</td></tr>';
            } else {
              tbody.innerHTML = rows
                .map(function (row) {
                  var st = row.status || '';
                  return (
                    '<tr class="bg-white hover:bg-[#fafbfc]">' +
                    '<td class="px-2 py-2.5 align-top font-bold sm:px-3">' +
                    escHtml(row.metric) +
                    '</td>' +
                    '<td class="max-w-[14rem] px-2 py-2.5 align-top text-on-surface-variant sm:max-w-none sm:px-3">' +
                    escHtml(row.description) +
                    '</td>' +
                    '<td class="px-2 py-2.5 text-right sm:px-3">' +
                    '<span class="inline-flex items-center justify-end gap-1.5">' +
                    '<span class="h-2 w-2 shrink-0 rounded-full ' +
                    evalStatusDotClass(st) +
                    '" aria-hidden="true"></span>' +
                    '<span class="text-[10px] font-semibold leading-snug">' +
                    escHtml(row.action_threshold) +
                    '</span></span></td></tr>'
                  );
                })
                .join('');
            }
          }
          var rvRow = null;
          for (var ri = 0; ri < rows.length; ri++) {
            if (rows[ri].key === 'repeat_violator') {
              rvRow = rows[ri];
              break;
            }
          }
          var violators = rvRow && rvRow.violators_detail ? rvRow.violators_detail : [];
          var repeatBlock = document.getElementById('peer-eval-repeat-block');
          var repeatTitle = document.getElementById('peer-eval-repeat-title');
          var repeatTbody = document.getElementById('peer-eval-repeat-tbody');
          if (repeatBlock) {
            if (violators.length) repeatBlock.classList.remove('hidden');
            else repeatBlock.classList.add('hidden');
          }
          if (repeatTitle) {
            repeatTitle.textContent =
              'Pelanggar repetitif (' + (es.repeat_period_caption || 'Seluruh data') + ')';
          }
          if (repeatTbody) {
            repeatTbody.innerHTML = buildPeerRvCardRowsHtml(violators);
          }
          var recRowJs = null;
          for (var rj = 0; rj < rows.length; rj++) {
            if (rows[rj].key === 'recency') {
              recRowJs = rows[rj];
              break;
            }
          }
          var recWrap = document.getElementById('peer-eval-recency-wrap');
          var recInner = document.getElementById('peer-eval-recency-inner');
          if (recWrap && recInner) {
            var rd = recRowJs && recRowJs.recency_detail ? recRowJs.recency_detail : null;
            if (!rd) {
              recWrap.classList.add('hidden');
              recInner.innerHTML = '';
            } else {
              recWrap.classList.remove('hidden');
              recInner.innerHTML = buildRecencyCardSectionHtml(rd);
            }
          }
          var foot = document.getElementById('peer-eval-footer-meta');
          if (foot) {
            foot.textContent =
              'Diperbarui ' +
              (es.generated_at || '—') +
              ' · ' +
              (parseInt(String(es.total_kejadian != null ? es.total_kejadian : 0), 10) || 0) +
              ' kejadian';
          }
          var modalDyn = document.getElementById('peer-evaluation-modal-dynamic');
          if (modalDyn) {
            var detailBlocks = rows
              .map(function (row) {
                var bullets = row.detail_bullets || [];
                var useDv = row.key === 'deviation_variety' && row.deviation_variety_detail;
                var usePc = row.key === 'peer_correlation' && row.peer_correlation_detail;
                var ul =
                  !useDv && !usePc && bullets.length > 0
                    ? '<ul class="mt-2 list-inside list-disc space-y-1 text-[13px] leading-relaxed text-on-surface">' +
                      bullets
                        .map(function (b) {
                          return '<li>' + escHtml(b) + '</li>';
                        })
                        .join('') +
                      '</ul>'
                    : '';
                var extra = '';
                if (useDv) {
                  extra += buildDeviationVarietyModalHtml(row.deviation_variety_detail);
                }
                if (usePc) {
                  extra += buildPeerCorrelationModalHtml(row.peer_correlation_detail);
                }
                if (row.key === 'repeat_violator' && row.violators_detail && row.violators_detail.length) {
                  extra +=
                    '<div class="mt-4 overflow-x-auto rounded-lg border border-outline-variant/20 bg-white">' +
                    '<table class="w-full min-w-[520px] text-left text-[12px] text-on-surface">' +
                    '<thead><tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">' +
                    '<th class="px-3 py-2">Nama</th><th class="px-3 py-2">SID</th><th class="px-3 py-2">Departemen</th>' +
                    '<th class="px-3 py-2 text-right">Repetitif</th></tr></thead><tbody class="divide-y divide-outline-variant/10">';
                  extra += buildPeerRvModalRowsHtml(row.violators_detail);
                  extra += '</tbody></table></div>';
                  extra +=
                    '<p class="mt-2 text-[10px] text-on-surface-variant">Nama dari peserta pada kejadian terbaru per SID. Kolom Dept: gabungan unik. Detail: tanggal temuan &amp; kategori deviasi per kejadian.</p>';
                }
                if (row.key === 'recency' && row.recency_detail) {
                  extra += buildRecencyModalExtraHtml(row.recency_detail);
                }
                return (
                  '<div class="mb-4 rounded-xl border border-outline-variant/20 bg-[#f8fafc] px-4 py-3 last:mb-0">' +
                  '<p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">' +
                  escHtml(row.metric) +
                  '</p>' +
                  ul +
                  extra +
                  '</div>'
                );
              })
              .join('');
            modalDyn.innerHTML = detailBlocks;
          }
        }
        function renderWeeklyChart(wt) {
          var cap = document.getElementById('peer-trend-period-caption');
          var lbl = document.getElementById('peer-weekly-period-label');
          var avg = document.getElementById('peer-trend-avg');
          var avgLeg = document.getElementById('peer-trend-avg-label');
          var sub = document.getElementById('peer-trend-chart-subtitle');
          if (cap) cap.textContent = wt.period_caption || '';
          if (lbl) lbl.textContent = wt.period_caption || '—';
          if (avg) avg.textContent = (Number(wt.avg_count) || 0).toFixed(1);
          if (avgLeg && wt.avg_legend_label) avgLeg.textContent = wt.avg_legend_label;
          if (sub) {
            var g = wt.chart_granularity === 'week' ? 'Per minggu (dalam bulan dipilih)' : 'Per bulan';
            sub.textContent =
              g + ' · batang bertumpuk = kategori deviasi (peer pressure), berdasarkan tanggal temuan';
          }
          var legInner = document.getElementById('peer-trend-legend-cats-inner');
          if (legInner && wt.deviation_categories && wt.deviation_categories.length) {
            legInner.innerHTML = wt.deviation_categories
              .map(function (dc) {
                var col = (dc.color || '#94a3b8').replace(/[<>"']/g, '');
                var lab = dc.label != null ? String(dc.label) : '';
                return (
                  '<span class="inline-flex items-center gap-1.5" title="' +
                  escAttr(lab) +
                  '"><span class="h-2.5 w-2.5 shrink-0 rounded-sm shadow-sm ring-1 ring-black/5" style="background-color:' +
                  col +
                  '"></span><span class="max-w-[10rem] truncate sm:max-w-none">' +
                  escHtml(lab) +
                  '</span></span>'
                );
              })
              .join('');
          }
          var lineWrap = document.getElementById('peer-chart-target-line-wrap');
          var maxC = wt.max_count || 0;
          if (lineWrap) {
            if (maxC > 0) {
              lineWrap.classList.remove('hidden');
              var pct = Math.min(100, Math.max(0, Number(wt.target_line_bottom_pct) || 0));
              lineWrap.style.bottom = pct + '%';
            } else {
              lineWrap.classList.add('hidden');
            }
          }
          var bars = document.getElementById('peer-chart-bars');
          if (!bars) return;
          bars.innerHTML = '';
          var weeks = wt.weeks || [];
          if (!weeks.length) {
            var empty = document.createElement('div');
            empty.className =
              'peer-chart-empty flex w-full min-w-full flex-[1_1_100%] basis-full items-center justify-center py-12 text-sm text-on-surface-variant';
            empty.textContent = 'Belum ada data untuk chart.';
            bars.appendChild(empty);
          } else {
            var devCats = wt.deviation_categories || [];
            weeks.forEach(function (w) {
              var barH = Math.min(100, Math.max(0, Number(w.bar_height_pct) || 0));
              var cnt = parseInt(String(w.count != null ? w.count : 0), 10) || 0;
              var by = w.by_category || {};
              var stackP = w.category_stack_pct || {};
              var tipParts = [];
              devCats.forEach(function (dc) {
                var nk = parseInt(String(by[dc.key] != null ? by[dc.key] : 0), 10) || 0;
                if (nk > 0) tipParts.push((dc.label || dc.key) + ': ' + nk);
              });
              var tip =
                (w.range_short || '') +
                ' — total ' +
                cnt +
                ' kejadian' +
                (tipParts.length ? ' · ' + tipParts.join(', ') : '');

              var col = document.createElement('div');
              col.className =
                'peer-chart-bar-col group relative flex h-full min-h-0 min-w-[2.25rem] flex-1 basis-0 flex-col justify-end rounded-t-lg border-x border-t border-outline-variant/10 bg-[#f8fafc]';
              col.setAttribute('title', tip);

              var wrap = document.createElement('div');
              wrap.className = 'relative w-full';
              wrap.style.height = barH + '%';

              var sp = document.createElement('span');
              sp.className =
                'absolute -top-6 left-1/2 z-10 -translate-x-1/2 whitespace-nowrap text-[10px] font-semibold text-on-surface';
              sp.textContent = String(cnt);
              wrap.appendChild(sp);

              var inner = document.createElement('div');
              inner.className =
                'absolute inset-0 flex flex-col justify-end overflow-hidden rounded-t-md shadow-inner ring-1 ring-black/10';

              devCats.forEach(function (dc) {
                var segH = Number(stackP[dc.key] != null ? stackP[dc.key] : 0);
                if (!segH || segH <= 0) return;
                var seg = document.createElement('div');
                seg.className = 'min-h-[2px] w-full shrink-0 transition-opacity group-hover:opacity-95';
                seg.style.height = segH + '%';
                seg.style.backgroundColor = String(dc.color || '#94a3b8').replace(/[<>"']/g, '');
                inner.appendChild(seg);
              });

              wrap.appendChild(inner);
              col.appendChild(wrap);
              bars.appendChild(col);
            });
          }
          var labels = document.getElementById('peer-chart-axis-labels');
          if (labels) {
            labels.innerHTML = '';
            weeks.forEach(function (w) {
              var s = document.createElement('span');
              s.className = 'peer-chart-axis-tick min-w-[2rem] flex-1 basis-0 text-center leading-tight';
              s.textContent = w.label || '—';
              labels.appendChild(s);
            });
          }
          if (wt.period_scope === 'all') {
            state.all = true;
          } else {
            state.all = false;
            state.year = wt.chart_year != null ? +wt.chart_year : state.year;
            state.month = wt.chart_month != null ? +wt.chart_month : state.month;
          }
        }
        function updateFormHiddenAndUrl() {
          var yEl = document.getElementById('peer-dashboard-year');
          var mEl = document.getElementById('peer-dashboard-month');
          if (state.all) {
            if (yEl) {
              yEl.setAttribute('disabled', 'disabled');
            }
            if (mEl) {
              mEl.setAttribute('disabled', 'disabled');
            }
          } else {
            if (yEl) {
              yEl.removeAttribute('disabled');
              yEl.value = String(state.year);
            }
            if (mEl) {
              mEl.removeAttribute('disabled');
              mEl.value = String(state.month);
            }
          }
          try {
            var u = new URL(window.location.href);
            if (state.all) {
              u.searchParams.delete('year');
              u.searchParams.delete('month');
            } else {
              u.searchParams.set('year', String(state.year));
              u.searchParams.set('month', String(state.month));
            }
            var qIn = document.querySelector('#peer-dashboard-search-form input[name="q"]');
            if (qIn && qIn.value) u.searchParams.set('q', qIn.value);
            else u.searchParams.delete('q');
            window.history.replaceState({}, '', u.toString());
          } catch (e) {}
        }
        if (openBtn) openBtn.addEventListener('click', openWeeklyModal);
        if (backdrop) backdrop.addEventListener('click', closeWeeklyModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeWeeklyModal);
        function peerSapModalCfg() {
          return window.PEER_SAP_MODAL_STATIC || { weeks: [], rowOrder: [], all: [], bySite: {} };
        }
        function peerSapModalSiteTitle(siteKey) {
          var m = {
            all: 'All Site',
            'bmo-1': 'BMO 1',
            'bmo-2': 'BMO 2',
            'bmo-3': 'BMO 3',
            eksplorasi: 'Eksplorasi',
            gmo: 'GMO',
            ho: 'HO',
            lmo: 'LMO',
            marine: 'Marine',
            smo: 'SMO'
          };
          return m[siteKey] || 'All Site';
        }
        function peerSapModalRowsForFilter(siteKey) {
          var cfg = peerSapModalCfg();
          if (!siteKey || siteKey === 'all') return cfg.all || [];
          var label = peerSapModalSiteTitle(siteKey);
          var map = cfg.bySite || {};
          return map[label] || [];
        }
        function peerSapModalPivot(rows) {
          var pivot = {};
          (rows || []).forEach(function (r) {
            var t = r[0];
            var w = r[1];
            var q = Number(r[2]);
            if (!pivot[t]) pivot[t] = {};
            pivot[t][w] = q;
          });
          return pivot;
        }
        function peerSapModalEsc(s) {
          if (s == null) return '';
          return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
        }
        function peerSapModalRenderTable(siteKey) {
          var cfg = peerSapModalCfg();
          var weeks = cfg.weeks || [];
          var order = cfg.rowOrder || [];
          var tbody = document.getElementById('peer-deviation-sap-tbody');
          if (!tbody) return;
          var rows = peerSapModalRowsForFilter(siteKey);
          var pivot = peerSapModalPivot(rows);
          tbody.innerHTML = order
            .map(function (type, idx) {
              var trCls = idx % 2 ? 'bg-slate-50' : 'bg-white';
              var cells = weeks
                .map(function (w) {
                  var v = pivot[type] && pivot[type][w] != null ? pivot[type][w] : null;
                  var display = v == null ? '—' : Number(v).toLocaleString('id-ID');
                  return '<td class="border-b border-slate-200 px-2 py-2 text-center tabular-nums">' + display + '</td>';
                })
                .join('');
              return (
                '<tr class="' +
                trCls +
                '">' +
                '<td class="border-b border-slate-200 px-2 py-2 text-left font-semibold text-slate-800">' +
                peerSapModalEsc(type) +
                '</td>' +
                cells +
                '</tr>'
              );
            })
            .join('');
        }
        function peerSapModalTypeColors() {
          return {
            COACHING: '#0d9488',
            HAZARD: '#dc2626',
            INSPEKSI: '#2563eb',
            OBSERVASI: '#7c3aed',
            'OBSERVASI AREA KRITIS': '#ea580c',
          };
        }
        function peerSapModalChartSeries(siteKey) {
          var cfg = peerSapModalCfg();
          var weeks = cfg.weeks || [];
          var order = cfg.rowOrder || [];
          var rows = peerSapModalRowsForFilter(siteKey);
          var pivot = peerSapModalPivot(rows);
          var cols = peerSapModalTypeColors();
          return order.map(function (type) {
            return {
              label: type,
              data: weeks.map(function (w) {
                var v = pivot[type] && pivot[type][w];
                return v != null ? Number(v) : 0;
              }),
              backgroundColor: cols[type] || '#64748b',
            };
          });
        }
        function peerPjbModalCfg() {
          return (
            window.PEER_PENGAWASAN_BERJARAK_MODAL_STATIC || { weeks: [], rowOrder: [], all: [], bySite: {} }
          );
        }
        function peerPjbModalRowsForFilter(siteKey) {
          var cfg = peerPjbModalCfg();
          if (!siteKey || siteKey === 'all') return cfg.all || [];
          var label = peerSapModalSiteTitle(siteKey);
          var map = cfg.bySite || {};
          return map[label] || [];
        }
        function peerPjbModalRenderTable(siteKey) {
          var cfg = peerPjbModalCfg();
          var weeks = cfg.weeks || [];
          var order = cfg.rowOrder || [];
          var tbody = document.getElementById('peer-deviation-pjb-tbody');
          if (!tbody) return;
          var rows = peerPjbModalRowsForFilter(siteKey);
          var pivot = peerSapModalPivot(rows);
          tbody.innerHTML = order
            .map(function (type, idx) {
              var trCls = idx % 2 ? 'bg-slate-50' : 'bg-white';
              var cells = weeks
                .map(function (w) {
                  var v = pivot[type] && pivot[type][w] != null ? pivot[type][w] : null;
                  var display = v == null ? '—' : Number(v).toLocaleString('id-ID');
                  return '<td class="border-b border-slate-200 px-2 py-2 text-center tabular-nums">' + display + '</td>';
                })
                .join('');
              return (
                '<tr class="' +
                trCls +
                '">' +
                '<td class="border-b border-slate-200 px-2 py-2 text-left font-semibold text-slate-800">' +
                peerSapModalEsc(type) +
                '</td>' +
                cells +
                '</tr>'
              );
            })
            .join('');
        }
        function peerPjbModalTypeColors() {
          return {
            'Real Time': '#0284c7',
            'Post Event': '#a855f7',
            'Pengawasan Langsung': '#16a34a',
          };
        }
        function peerPjbModalChartSeries(siteKey) {
          var cfg = peerPjbModalCfg();
          var weeks = cfg.weeks || [];
          var order = cfg.rowOrder || [];
          var rows = peerPjbModalRowsForFilter(siteKey);
          var pivot = peerSapModalPivot(rows);
          var cols = peerPjbModalTypeColors();
          return order.map(function (type) {
            return {
              label: type,
              data: weeks.map(function (w) {
                var v = pivot[type] && pivot[type][w];
                return v != null ? Number(v) : 0;
              }),
              backgroundColor: cols[type] || '#64748b',
            };
          });
        }
        function peerPelaporModalCfg() {
          return window.PEER_PELOPOR_MODAL_STATIC || { weeks: [], all: [], bySite: {} };
        }
        function peerPelaporModalRowsForFilter(siteKey) {
          var cfg = peerPelaporModalCfg();
          if (!siteKey || siteKey === 'all') return cfg.all || [];
          var label = peerSapModalSiteTitle(siteKey);
          var map = cfg.bySite || {};
          return map[label] || [];
        }
        function peerPelaporModalWeekValues(siteKey) {
          var cfg = peerPelaporModalCfg();
          var weeks = cfg.weeks || [];
          var rows = peerPelaporModalRowsForFilter(siteKey);
          var byW = {};
          (rows || []).forEach(function (r) {
            if (!r || r.length < 2) return;
            var w = r[0];
            var q = Number(r[1]);
            if (!isNaN(q)) byW[w] = q;
          });
          return weeks.map(function (w) {
            return byW[w] != null ? byW[w] : 0;
          });
        }
        function peerPelaporModalRenderTable(siteKey) {
          var cfg = peerPelaporModalCfg();
          var weeks = cfg.weeks || [];
          var tbody = document.getElementById('peer-deviation-pelapor-tbody');
          if (!tbody) return;
          var vals = peerPelaporModalWeekValues(siteKey);
          tbody.innerHTML = weeks
            .map(function (w, idx) {
              var trCls = idx % 2 ? 'bg-slate-50' : 'bg-white';
              var v = vals[idx] != null ? vals[idx] : null;
              var display = v == null || v === '' ? '—' : Number(v).toLocaleString('id-ID');
              return (
                '<tr class="' +
                trCls +
                '"><td class="border-b border-slate-200 px-2 py-2 font-semibold text-slate-800">' +
                peerSapModalEsc(w) +
                '</td><td class="border-b border-slate-200 px-2 py-2 text-center tabular-nums font-semibold text-slate-800">' +
                display +
                '</td></tr>'
              );
            })
            .join('');
        }
        function refreshPeerDeviationModalSite(siteKey) {
          var sk = siteKey || 'all';
          var labelEl = document.getElementById('peer-deviation-modal-site-label');
          if (labelEl) labelEl.textContent = 'Site: ' + peerSapModalSiteTitle(sk);
          peerSapModalRenderTable(sk);
          peerPjbModalRenderTable(sk);
          peerPelaporModalRenderTable(sk);
          var sapCanvas = document.getElementById('peer-deviation-modal-sap-chart');
          if (sapCanvas) {
            var cfg = peerSapModalCfg();
            sapCanvas.setAttribute('data-sap-labels', JSON.stringify(cfg.weeks || []));
            sapCanvas.setAttribute('data-sap-series', JSON.stringify(peerSapModalChartSeries(sk)));
          }
          var pjbCanvas = document.getElementById('peer-deviation-modal-pjb-chart');
          if (pjbCanvas) {
            var pcfg = peerPjbModalCfg();
            pjbCanvas.setAttribute('data-pjb-labels', JSON.stringify(pcfg.weeks || []));
            pjbCanvas.setAttribute('data-pjb-series', JSON.stringify(peerPjbModalChartSeries(sk)));
          }
          var pelaporCanvas = document.getElementById('peer-deviation-modal-pelapor-chart');
          if (pelaporCanvas) {
            var lcfg = peerPelaporModalCfg();
            pelaporCanvas.setAttribute('data-pelapor-weeks', JSON.stringify(lcfg.weeks || []));
            pelaporCanvas.setAttribute('data-pelapor-values', JSON.stringify(peerPelaporModalWeekValues(sk)));
          }
          if (sapCanvas || pjbCanvas || pelaporCanvas) {
            initPeerDeviationModalCharts();
            resizePeerDeviationModalCharts();
          }
        }
        var devModal = document.getElementById('peer-deviation-category-modal');
        var devCard = document.getElementById('peer-kpi-deviation-card');
        var devCardStack = document.getElementById('peer-kpi-deviation-card-stack');
        var hazardReportingCard = document.getElementById('peer-kpi-hazard-reporting-card');
        var devClose = document.getElementById('peer-deviation-category-close');
        var devSiteFilter = document.getElementById('peer-deviation-site-filter');
        var devBackdrop = devModal ? devModal.querySelector('.peer-deviation-category-backdrop') : null;
        function openDeviationModal() {
          if (!devModal) return;
          devModal.classList.remove('hidden');
          devModal.setAttribute('aria-hidden', 'false');
          if (devCard) devCard.setAttribute('aria-expanded', 'true');
          if (devCardStack) devCardStack.setAttribute('aria-expanded', 'true');
          if (hazardReportingCard) hazardReportingCard.setAttribute('aria-expanded', 'true');
          var initialSite = devSiteFilter ? devSiteFilter.value : 'all';
          setTimeout(function () {
            refreshPeerDeviationModalSite(initialSite);
            requestAnimationFrame(function () {
              resizePeerDeviationModalCharts();
              setTimeout(resizePeerDeviationModalCharts, 80);
            });
          }, 120);
        }
        function closeDeviationModal() {
          if (!devModal) return;
          destroyPeerDeviationModalCharts();
          devModal.classList.add('hidden');
          devModal.setAttribute('aria-hidden', 'true');
          if (devCard) devCard.setAttribute('aria-expanded', 'false');
          if (devCardStack) devCardStack.setAttribute('aria-expanded', 'false');
          if (hazardReportingCard) hazardReportingCard.setAttribute('aria-expanded', 'false');
        }
        if (devCard) devCard.addEventListener('click', openDeviationModal);
        if (devCardStack) devCardStack.addEventListener('click', openDeviationModal);
        if (hazardReportingCard) hazardReportingCard.addEventListener('click', openDeviationModal);
        if (devClose) devClose.addEventListener('click', closeDeviationModal);
        if (devBackdrop) devBackdrop.addEventListener('click', closeDeviationModal);
        if (devSiteFilter) {
          devSiteFilter.addEventListener('change', function () {
            refreshPeerDeviationModalSite(devSiteFilter.value || 'all');
          });
        }
        function peerTableauLazyLoadViz(vizId) {
          var el = document.getElementById(vizId);
          if (!el || el.getAttribute('data-tableau-loaded') === '1') return;
          var url = el.getAttribute('data-src');
          if (!url) return;
          el.setAttribute('src', url);
          el.setAttribute('data-tableau-loaded', '1');
        }
        var tbcGeneralModal = document.getElementById('peer-tbc-general-modal');
        var tbcHighCard = document.getElementById('peer-kpi-tbc-high-card');
        var tbcGeneralClose = document.getElementById('peer-tbc-general-close');
        var tbcGeneralBackdrop = tbcGeneralModal ? tbcGeneralModal.querySelector('.peer-tbc-general-backdrop') : null;
        var peerTbcGeneralBarChart = null;
        var peerTbcRepetitiveScatterChart = null;
        var peerTbcCategoryLineCharts = [];
        function peerTbcNum(v) {
          if (v === null || v === undefined || v === '') return null;
          var n = Number(v);
          return isNaN(n) ? null : n;
        }
        function peerTbcWeekTotalsFromCategories(categories) {
          var weeks = (peerTbcCategoryTrend && peerTbcCategoryTrend.weeks) || ['W12', 'W13', 'W14', 'W15'];
          var n = weeks.length;
          var totals = [];
          var w;
          for (w = 0; w < n; w++) totals[w] = 0;
          if (!categories || !categories.length) return totals;
          categories.forEach(function (cat) {
            var vals = cat.values || [];
            for (w = 0; w < n; w++) {
              var nv = peerTbcNum(vals[w]);
              if (nv !== null) totals[w] += nv;
            }
          });
          return totals;
        }
        function peerTbcGetCategoriesForSite(siteKey) {
          if (!peerTbcCategoryTrend || !peerTbcCategoryTrend.all_sites) return [];
          if (!siteKey || siteKey === '__all') {
            return peerTbcCategoryTrend.all_sites.categories || [];
          }
          var bs = peerTbcCategoryTrend.by_site && peerTbcCategoryTrend.by_site[siteKey];
          if (bs && bs.categories && bs.categories.length) return bs.categories;
          return [];
        }
        function peerTbcBuildRepetitivePoints(categories, weeks) {
          var pts = [];
          if (!Array.isArray(categories) || !categories.length) return pts;
          categories.forEach(function (cat, idx) {
            var vals = Array.isArray(cat.values) ? cat.values : [];
            vals.forEach(function (raw, wi) {
              var x = peerTbcNum(raw);
              if (x === null) return;
              var baseY = Math.sqrt(Math.max(0, x)) * 1.9;
              var jitter = ((idx % 6) - 2.5) * 0.55 + wi * 0.35;
              var y = Math.max(0, Math.min(30, baseY + jitter));
              pts.push({
                x: Number(x.toFixed(2)),
                y: Number(y.toFixed(2)),
                category: (cat.label != null ? String(cat.label) : '—'),
                rank: cat.rank != null ? Number(cat.rank) : idx + 1,
                week: weeks[wi] || ('W' + (wi + 1)),
              });
            });
          });
          return pts;
        }
        function peerTbcRenderNeedCheckTop5(points, xMid, yMid) {
          var root = document.getElementById('peer-tbc-need-check-top5');
          if (!root) return;
          if (!Array.isArray(points) || !points.length) {
            root.innerHTML = '<p class="px-3 py-8 text-center text-sm text-slate-500">Belum ada data kuadran.</p>';
            return;
          }
          var weeks = (peerTbcCategoryTrend && peerTbcCategoryTrend.weeks) || ['W12', 'W13', 'W14', 'W15'];
          var bySite = (peerTbcCategoryTrend && peerTbcCategoryTrend.by_site) ? peerTbcCategoryTrend.by_site : {};
          var siteKeys = Object.keys(bySite);
          var weekLastIdx = Math.max(0, weeks.length - 1);

          function weekIndexOf(w) {
            var i = weeks.indexOf(w);
            return i >= 0 ? i : weekLastIdx;
          }
          function valueAtSite(siteKey, categoryLabel, weekIdx) {
            var data = bySite[siteKey];
            var cats = data && Array.isArray(data.categories) ? data.categories : [];
            var i;
            for (i = 0; i < cats.length; i++) {
              var c = cats[i];
              if (String(c.label || '') !== String(categoryLabel || '')) continue;
              var vals = Array.isArray(c.values) ? c.values : [];
              var n = peerTbcNum(vals[weekIdx]);
              return n === null ? 0 : n;
            }
            return 0;
          }
          function quadrantOf(p) {
            if (p.x >= xMid && p.y >= yMid) return 'Kuadran I';
            if (p.x < xMid && p.y < yMid) return 'Kuadran IV';
            return '';
          }

          var top = points
            .map(function (p) {
              var q = quadrantOf(p);
              return {
                category: p.category,
                week: p.week,
                weekIdx: weekIndexOf(p.week),
                x: p.x,
                y: p.y,
                kuadran: q,
                score: (p.x * p.y),
              };
            })
            .filter(function (r) { return r.kuadran === 'Kuadran I' || r.kuadran === 'Kuadran IV'; })
            .sort(function (a, b) { return b.score - a.score; })
            .slice(0, 5);
          if (!top.length) {
            root.innerHTML = '<p class="px-3 py-8 text-center text-sm text-slate-500">Belum ada item prioritas pada Kuadran I dan IV.</p>';
            return;
          }
          var siteHeaderHtml = siteKeys.map(function (k) {
            return '<th class="px-2 py-2 text-center text-[10px] font-bold uppercase tracking-wide text-slate-500 whitespace-nowrap">' + escHtml(k) + '</th>';
          }).join('');
          var rows = top.map(function (it, idx) {
            var catatan = (idx + 1) + '. ' + it.category;
            var sub = 'Priority check ' + it.week;
            var siteCells = siteKeys.map(function (siteKey) {
              var v = valueAtSite(siteKey, it.category, it.weekIdx);
              return '<td class="px-2 py-2 text-center text-[11px] tabular-nums text-slate-700 whitespace-nowrap">' + Number(v).toLocaleString('id-ID') + '</td>';
            }).join('');
            var grand = siteKeys.reduce(function (acc, k) { return acc + valueAtSite(k, it.category, it.weekIdx); }, 0);
            return (
              '<tr class="border-b border-slate-200 ' + (idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/70') + '">' +
              '<td class="px-2.5 py-2 text-[11px] font-semibold text-slate-700 align-top">' + escHtml(catatan) + '</td>' +
              '<td class="px-2.5 py-2 text-[11px] text-slate-600">' + escHtml(sub) + '</td>' +
              '<td class="px-2.5 py-2 text-center text-[11px] font-semibold text-slate-600 whitespace-nowrap">' + escHtml(it.kuadran) + '</td>' +
              siteCells +
              '<td class="px-2.5 py-2 text-center text-[11px] font-bold tabular-nums text-slate-700">' + Number(grand).toLocaleString('id-ID') + '</td>' +
              '</tr>'
            );
          }).join('');
          root.innerHTML =
            '<table class="w-full min-w-[1080px] border-collapse">' +
            '<thead><tr class="bg-[#f8fafc] text-[10px] uppercase tracking-wide text-slate-600 border-b border-slate-200">' +
            '<th class="px-2.5 py-2 text-left">Catatan (group)</th>' +
            '<th class="px-2.5 py-2 text-left">Subketidaksesuaian</th>' +
            '<th class="px-2.5 py-2 text-center">Kuadran</th>' +
            siteHeaderHtml +
            '<th class="px-2.5 py-2 text-center">Grand</th>' +
            '</tr></thead>' +
            '<tbody>' + rows + '</tbody></table>';
        }
        function peerTbcRenderRepetitiveScatter(siteKey) {
          var canvas = document.getElementById('peer-tbc-repetitive-scatter-canvas');
          if (!canvas || typeof Chart === 'undefined') return;
          if (peerTbcRepetitiveScatterChart) {
            try { peerTbcRepetitiveScatterChart.destroy(); } catch (e) {}
            peerTbcRepetitiveScatterChart = null;
          }
          var weeks = (peerTbcCategoryTrend && peerTbcCategoryTrend.weeks) || ['W12', 'W13', 'W14', 'W15'];
          var categories = peerTbcGetCategoriesForSite(siteKey && siteKey !== '' ? siteKey : '__all');
          var points = peerTbcBuildRepetitivePoints(categories, weeks);
          if (!points.length) {
            peerTbcRenderNeedCheckTop5([], 0, 0);
            return;
          }
          var xs = points.map(function (p) { return p.x; });
          var ys = points.map(function (p) { return p.y; });
          var xMax = Math.max.apply(null, xs.concat([50]));
          var yMax = Math.max.apply(null, ys.concat([10]));
          var xMid = Math.max(40, xMax * 0.45);
          var yMid = Math.max(8, yMax * 0.5);
          peerTbcRenderNeedCheckTop5(points, xMid, yMid);

          var dotColors = ['#34a0a4', '#6a4c93', '#4cc9f0', '#f72585', '#e9c46a', '#90be6d', '#577590', '#f9844a'];
          var quadPlugin = {
            id: 'peerTbcQuadrantGuide',
            afterDraw: function (ch) {
              var ctx = ch.ctx;
              var area = ch.chartArea;
              if (!area) return;
              var xScale = ch.scales.x;
              var yScale = ch.scales.y;
              var xPx = xScale.getPixelForValue(xMid);
              var yPx = yScale.getPixelForValue(yMid);
              ctx.save();
              ctx.strokeStyle = 'rgba(100,116,139,0.55)';
              ctx.lineWidth = 1;
              ctx.beginPath();
              ctx.moveTo(xPx, area.top);
              ctx.lineTo(xPx, area.bottom);
              ctx.moveTo(area.left, yPx);
              ctx.lineTo(area.right, yPx);
              ctx.stroke();
              ctx.fillStyle = 'rgba(239,68,68,0.70)';
              ctx.font = '700 12px Poppins, system-ui, sans-serif';
              ctx.fillText('Kuadran I', Math.min(area.right - 78, xPx + 8), Math.max(area.top + 14, yPx - 8));
              ctx.fillStyle = 'rgba(239,68,68,0.55)';
              ctx.fillText('Kuadran IV', Math.max(area.left + 8, xPx - 86), Math.min(area.bottom - 8, yPx + 18));
              ctx.restore();
            },
          };
          peerTbcRepetitiveScatterChart = new Chart(canvas.getContext('2d'), {
            type: 'scatter',
            data: {
              datasets: [{
                label: 'Sebaran Laporan',
                data: points,
                parsing: false,
                pointRadius: 4,
                pointHoverRadius: 5,
                pointBackgroundColor: function (ctx) {
                  return dotColors[ctx.dataIndex % dotColors.length];
                },
                pointBorderWidth: 0,
              }],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              animation: false,
              plugins: {
                legend: { display: false },
                tooltip: {
                  callbacks: {
                    label: function (ctx) {
                      var raw = ctx.raw || {};
                      return (raw.category || '—') + ' · ' + (raw.week || '') + ' · Laporan: ' + Number(raw.x || 0).toLocaleString('id-ID') + ' · Repetisi: ' + Number(raw.y || 0).toLocaleString('id-ID', { maximumFractionDigits: 1 });
                    },
                  },
                },
              },
              scales: {
                x: {
                  min: 0,
                  max: Math.ceil(xMax / 25) * 25,
                  title: { display: true, text: 'Jumlah Laporan ✦', color: '#475569', font: { size: 11, weight: '600' } },
                  ticks: { color: '#64748b', maxTicksLimit: 8 },
                  grid: { color: 'rgba(148,163,184,0.16)', drawBorder: false },
                },
                y: {
                  min: 0,
                  max: Math.ceil((yMax + 2) / 5) * 5,
                  title: { display: true, text: 'Repetisi Hari ✦', color: '#475569', font: { size: 11, weight: '600' } },
                  ticks: { color: '#64748b', maxTicksLimit: 7 },
                  grid: { color: 'rgba(148,163,184,0.16)', drawBorder: false },
                },
              },
            },
            plugins: [quadPlugin],
          });
        }
        var peerTbcLinePalette = [
          '57, 82, 188',
          '220, 38, 38',
          '22, 163, 74',
          '234, 88, 12',
          '147, 51, 234',
          '8, 145, 178',
          '190, 24, 93',
          '101, 163, 13',
          '217, 119, 6',
          '59, 130, 246',
          '239, 68, 68',
          '16, 185, 129',
          '245, 158, 11',
          '99, 102, 241'
        ];
        function peerTbcDestroyCategoryCards() {
          peerTbcCategoryLineCharts.forEach(function (ch) {
            try {
              if (ch && typeof ch.destroy === 'function') ch.destroy();
            } catch (e) {}
          });
          peerTbcCategoryLineCharts = [];
          var root = document.getElementById('peer-tbc-category-cards');
          if (root) root.innerHTML = '';
        }
        function peerTbcRgbFromPalette(idx) {
          var rgb = peerTbcLinePalette[idx % peerTbcLinePalette.length];
          return 'rgb(' + rgb + ')';
        }
        function peerTbcRenderCategoryLineChart(siteKey) {
          var sk = siteKey && siteKey !== '' ? siteKey : '__all';
          var cardsRoot = document.getElementById('peer-tbc-category-cards');
          var emptyEl = document.getElementById('peer-tbc-category-empty');
          peerTbcDestroyCategoryCards();
          if (!cardsRoot) return;
          var weeks = (peerTbcCategoryTrend && peerTbcCategoryTrend.weeks) || ['W12', 'W13', 'W14', 'W15'];
          var categories = peerTbcGetCategoriesForSite(sk);
          if (!categories.length) {
            if (emptyEl) emptyEl.classList.remove('hidden');
            return;
          }
          if (emptyEl) emptyEl.classList.add('hidden');
          var allNumbers = [];
          categories.forEach(function (cat) {
            var vals = cat.values || [];
            vals.forEach(function (v) {
              var nv = peerTbcNum(v);
              if (nv !== null) allNumbers.push(nv);
            });
          });
          var maxVal = allNumbers.length ? Math.max.apply(null, allNumbers) : 0;

          function intensityStyle(n) {
            if (n === null || maxVal <= 0) {
              return 'background:#ffffff;color:#334155;';
            }
            var ratio = Math.max(0, Math.min(1, n / maxVal));
            var alpha = 0.06 + ratio * 0.42;
            var text = ratio > 0.55 ? '#7f1d1d' : '#334155';
            return 'background:rgba(239,68,68,' + alpha.toFixed(3) + ');color:' + text + ';';
          }

          var headerWeeks = weeks
            .map(function (w) {
              return '<th class="px-2 py-2 text-center text-[10px] font-bold uppercase tracking-wide text-slate-500">' + escHtml(w) + '</th>';
            })
            .join('');

          var rowsHtml = categories
            .map(function (cat, idx) {
              var vals = cat.values || [];
              var title = (cat.rank != null ? String(cat.rank) + '. ' : '') + (cat.label != null ? String(cat.label) : '—');
              var weekCells = '';
              var sum = 0;
              var count = 0;
              weeks.forEach(function (_w, wi) {
                var nv = peerTbcNum(vals[wi]);
                if (nv !== null) {
                  sum += nv;
                  count += 1;
                }
                weekCells += '<td class="px-2 py-1.5 text-center text-[11px] font-semibold tabular-nums" style="' + intensityStyle(nv) + '">' + (nv === null ? '—' : nv.toLocaleString('id-ID')) + '</td>';
              });
              var avg = count ? sum / count : 0;
              var concern = avg >= maxVal * 0.6 ? 'Tinggi' : avg >= maxVal * 0.35 ? 'Sedang' : 'Rendah';
              var concernClass = concern === 'Tinggi' ? 'bg-red-100 text-red-700' : concern === 'Sedang' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700';
              return (
                '<tr class="' + (idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/70') + '">' +
                '<td class="sticky left-0 z-[1] border-r border-slate-200 px-2.5 py-2 text-[11px] font-semibold text-slate-700 ' + (idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/70') + '">' + escHtml(title) + '</td>' +
                weekCells +
                '<td class="px-2 py-1.5 text-center text-[11px] font-bold tabular-nums text-slate-700">' + avg.toFixed(1) + '</td>' +
                '<td class="px-2 py-1.5 text-center"><span class="inline-flex rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide ' + concernClass + '">' + concern + '</span></td>' +
                '</tr>'
              );
            })
            .join('');

          cardsRoot.innerHTML =
            '<div class="overflow-x-auto">' +
            '<table class="min-w-[980px] w-full border-collapse">' +
            '<thead class="sticky top-0 z-[2] bg-white">' +
            '<tr class="border-b border-slate-200 bg-[#fff8e6]">' +
            '<th class="sticky left-0 z-[3] border-r border-slate-200 px-2.5 py-2 text-left text-[10px] font-bold uppercase tracking-wide text-slate-600 bg-[#fff8e6]">Deviasi Pengamatan</th>' +
            headerWeeks +
            '<th class="px-2 py-2 text-center text-[10px] font-bold uppercase tracking-wide text-slate-500">Avg YTD\'26</th>' +
            '<th class="px-2 py-2 text-center text-[10px] font-bold uppercase tracking-wide text-slate-500">Concern</th>' +
            '</tr>' +
            '</thead>' +
            '<tbody>' + rowsHtml + '</tbody>' +
            '</table>' +
            '</div>';
        }
        function peerTbcRenderBarChart(siteKey) {
          var sk = siteKey && siteKey !== '' ? siteKey : '__all';
          var canvas = document.getElementById('peer-tbc-general-bar-canvas');
          if (!canvas || typeof Chart === 'undefined') return;
          if (peerTbcGeneralBarChart) {
            try {
              peerTbcGeneralBarChart.destroy();
            } catch (e) {}
            peerTbcGeneralBarChart = null;
          }
          var weeks = (peerTbcCategoryTrend && peerTbcCategoryTrend.weeks) || ['W12', 'W13', 'W14', 'W15'];
          var cats = peerTbcGetCategoriesForSite(sk);
          var totals = peerTbcWeekTotalsFromCategories(cats);
          var dsLabel =
            sk === '__all'
              ? 'Jumlah TBC (agregat semua kategori)'
              : 'Jumlah TBC — ' + sk;
          peerTbcGeneralBarChart = createPeerDeviationHrBarChart(canvas, weeks, totals, '#3952bc', dsLabel);
          if (peerTbcGeneralBarChart && peerTbcGeneralBarChart.options && peerTbcGeneralBarChart.options.scales) {
            // Mode polos: tanpa sumbu Y dan tanpa garis background/grid.
            if (peerTbcGeneralBarChart.options.scales.y) {
              peerTbcGeneralBarChart.options.scales.y.display = false;
              if (peerTbcGeneralBarChart.options.scales.y.grid) {
                peerTbcGeneralBarChart.options.scales.y.grid.display = false;
                peerTbcGeneralBarChart.options.scales.y.grid.drawBorder = false;
              }
              if (peerTbcGeneralBarChart.options.scales.y.ticks) {
                peerTbcGeneralBarChart.options.scales.y.ticks.display = false;
              }
            }
            if (peerTbcGeneralBarChart.options.scales.x && peerTbcGeneralBarChart.options.scales.x.grid) {
              peerTbcGeneralBarChart.options.scales.x.grid.display = false;
              peerTbcGeneralBarChart.options.scales.x.grid.drawBorder = false;
            }
            peerTbcGeneralBarChart.update();
          }
        }
        function peerTbcPopulateSiteSelect() {
          var sel = document.getElementById('peer-tbc-category-site-select');
          if (!sel) return;
          var keys = ['__all'];
          if (peerTbcCategoryTrend && peerTbcCategoryTrend.by_site) {
            Object.keys(peerTbcCategoryTrend.by_site).forEach(function (k) {
              if (keys.indexOf(k) === -1) keys.push(k);
            });
          }
          sel.innerHTML = '';
          keys.forEach(function (k) {
            var opt = document.createElement('option');
            opt.value = k;
            opt.textContent = k === '__all' ? 'Semua site (agregat)' : k;
            sel.appendChild(opt);
          });
        }
        function loadTbcGeneralCards() {
          var loading = document.getElementById('peer-tbc-general-loading');
          var errEl = document.getElementById('peer-tbc-general-error');
          var body = document.getElementById('peer-tbc-general-body');
          if (errEl) {
            errEl.classList.add('hidden');
            errEl.textContent = '';
          }
          if (loading) loading.classList.remove('hidden');
          if (body) body.classList.add('hidden');
          window.setTimeout(function () {
            if (loading) loading.classList.add('hidden');
            if (body) body.classList.remove('hidden');
            if (!peerTbcCategoryTrend || !peerTbcCategoryTrend.all_sites || !peerTbcCategoryTrend.all_sites.categories) {
              if (errEl) {
                errEl.textContent = 'Data tren kategori TBC tidak tersedia (periksa file JSON).';
                errEl.classList.remove('hidden');
              }
              peerTbcDestroyCategoryCards();
              return;
            }
            peerTbcPopulateSiteSelect();
            var sel = document.getElementById('peer-tbc-category-site-select');
            if (sel) {
              sel.onchange = function () {
                var v = sel.value || '__all';
                peerTbcRenderBarChart(v);
                peerTbcRenderCategoryLineChart(v);
                peerTbcRenderRepetitiveScatter(v);
              };
            }
            var v0 = sel && sel.value ? sel.value : '__all';
            peerTbcRenderBarChart(v0);
            peerTbcRenderCategoryLineChart(v0);
            peerTbcRenderRepetitiveScatter(v0);
          }, 80);
        }
        function openTbcGeneralModal() {
          if (!tbcGeneralModal) return;
          if (blindspotModal && !blindspotModal.classList.contains('hidden')) closeBlindspotModal();
          tbcGeneralModal.classList.remove('hidden');
          tbcGeneralModal.setAttribute('aria-hidden', 'false');
          if (tbcHighCard) tbcHighCard.setAttribute('aria-expanded', 'true');
          peerTableauLazyLoadViz('tableau-viz-tbc');
          loadTbcGeneralCards();
        }
        function closeTbcGeneralModal() {
          if (!tbcGeneralModal) return;
          if (peerTbcGeneralBarChart) {
            try {
              peerTbcGeneralBarChart.destroy();
            } catch (e) {}
            peerTbcGeneralBarChart = null;
          }
          if (peerTbcRepetitiveScatterChart) {
            try {
              peerTbcRepetitiveScatterChart.destroy();
            } catch (e) {}
            peerTbcRepetitiveScatterChart = null;
          }
          peerTbcDestroyCategoryCards();
          tbcGeneralModal.classList.add('hidden');
          tbcGeneralModal.setAttribute('aria-hidden', 'true');
          if (tbcHighCard) tbcHighCard.setAttribute('aria-expanded', 'false');
        }
        if (tbcHighCard) tbcHighCard.addEventListener('click', openTbcGeneralModal);
        if (tbcGeneralClose) tbcGeneralClose.addEventListener('click', closeTbcGeneralModal);
        if (tbcGeneralBackdrop) tbcGeneralBackdrop.addEventListener('click', closeTbcGeneralModal);
        document.addEventListener('keydown', function (e) {
          if (e.key !== 'Escape') return;
          if (tbcGeneralModal && !tbcGeneralModal.classList.contains('hidden')) closeTbcGeneralModal();
        });
        var blindspotModal = document.getElementById('peer-blindspot-modal');
        var blindspotCard = document.getElementById('peer-kpi-blindspot-card');
        var blindspotClose = document.getElementById('peer-blindspot-close');
        var blindspotBackdrop = blindspotModal ? blindspotModal.querySelector('.peer-blindspot-backdrop') : null;
        var peerBlindspotStackChart = null;
        var peerBlindspotScatterChart = null;
        function peerBlindspotDestroyCharts() {
          if (peerBlindspotStackChart) {
            try { peerBlindspotStackChart.destroy(); } catch (e) {}
            peerBlindspotStackChart = null;
          }
          if (peerBlindspotScatterChart) {
            try { peerBlindspotScatterChart.destroy(); } catch (e) {}
            peerBlindspotScatterChart = null;
          }
        }
        function peerBlindspotRenderSummary(weeks, siteKeys, bySite) {
          var root = document.getElementById('peer-blindspot-summary');
          if (!root) return;
          var lastWeek = weeks.length ? weeks[weeks.length - 1] : 'W15';
          var ytd = 0;
          var l4w = 0;
          siteKeys.forEach(function (s) {
            var row = bySite[s] || {};
            weeks.forEach(function (w) {
              var n = peerTbcNum(row[w]);
              if (n !== null) ytd += n;
            });
            var l = peerTbcNum(row[lastWeek]);
            if (l !== null) l4w += l;
          });
          root.innerHTML =
            '<div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">' +
            '<p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Total Blindspot L4W</p>' +
            '<p class="mt-1 text-2xl font-bold tabular-nums text-slate-800">' + Number(l4w).toLocaleString('id-ID') + '</p>' +
            '</div>' +
            '<div class="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">' +
            '<p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Total Blindspot YTD 2026</p>' +
            '<p class="mt-1 text-2xl font-bold tabular-nums text-slate-800">' + Number(ytd).toLocaleString('id-ID') + '</p>' +
            '</div>';
        }
        function peerBlindspotRenderStackChart(weeks, siteKeys, bySite) {
          var canvas = document.getElementById('peer-blindspot-stack-canvas');
          if (!canvas || typeof Chart === 'undefined') return;
          var palette = ['#f59e0b', '#a78bfa', '#60a5fa', '#34d399', '#f472b6', '#4ade80', '#f97316', '#94a3b8', '#22c55e', '#0ea5e9'];
          var datasets = siteKeys.map(function (site, idx) {
            return {
              label: site,
              data: weeks.map(function (w) {
                var n = peerTbcNum((bySite[site] || {})[w]);
                return n === null ? 0 : n;
              }),
              backgroundColor: palette[idx % palette.length],
              borderWidth: 0,
              stack: 'blindspot',
              borderRadius: 3,
            };
          });
          peerBlindspotStackChart = new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: { labels: weeks, datasets: datasets },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              animation: false,
              plugins: { legend: { position: 'top', align: 'start', labels: { boxWidth: 10, boxHeight: 10, font: { size: 10 } } } },
              scales: {
                x: { stacked: true, grid: { display: false, drawBorder: false }, ticks: { color: '#64748b', font: { size: 10 } } },
                y: { stacked: true, display: false, grid: { display: false, drawBorder: false }, ticks: { display: false } },
              },
            },
          });
        }
        function peerBlindspotRenderScatter(weeks, siteKeys, bySite) {
          var canvas = document.getElementById('peer-blindspot-scatter-canvas');
          if (!canvas || typeof Chart === 'undefined') return;
          var points = [];
          siteKeys.forEach(function (site, si) {
            weeks.forEach(function (w, wi) {
              var x = peerTbcNum((bySite[site] || {})[w]);
              if (x === null) return;
              var y = Math.max(0, Math.min(30, (x * 2.2) + (si % 4) + (wi * 0.4)));
              points.push({ x: x, y: y, site: site, week: w });
            });
          });
          var xMax = Math.max.apply(null, points.map(function (p) { return p.x; }).concat([10]));
          var yMax = Math.max.apply(null, points.map(function (p) { return p.y; }).concat([10]));
          var xMid = Math.max(4, xMax * 0.5);
          var yMid = Math.max(8, yMax * 0.5);
          var qPlugin = {
            id: 'peerBlindspotQuad',
            afterDraw: function (ch) {
              var ctx = ch.ctx;
              var area = ch.chartArea;
              if (!area) return;
              var xPx = ch.scales.x.getPixelForValue(xMid);
              var yPx = ch.scales.y.getPixelForValue(yMid);
              ctx.save();
              ctx.strokeStyle = 'rgba(120,130,145,0.55)';
              ctx.lineWidth = 1;
              ctx.beginPath();
              ctx.moveTo(xPx, area.top);
              ctx.lineTo(xPx, area.bottom);
              ctx.moveTo(area.left, yPx);
              ctx.lineTo(area.right, yPx);
              ctx.stroke();
              ctx.fillStyle = 'rgba(239,68,68,0.7)';
              ctx.font = '700 11px Poppins, system-ui, sans-serif';
              ctx.fillText('Kuadran I', Math.min(area.right - 76, xPx + 8), Math.max(area.top + 14, yPx - 8));
              ctx.fillStyle = 'rgba(239,68,68,0.55)';
              ctx.fillText('Kuadran IV', Math.max(area.left + 8, xPx - 86), Math.min(area.bottom - 8, yPx + 16));
              ctx.restore();
            },
          };
          peerBlindspotScatterChart = new Chart(canvas.getContext('2d'), {
            type: 'scatter',
            data: { datasets: [{ data: points, pointRadius: 4, pointBackgroundColor: '#a3a3a3', borderWidth: 0 }] },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              animation: false,
              plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: function (ctx) { var p = ctx.raw || {}; return (p.site || '') + ' · ' + (p.week || '') + ' · ' + Number(p.x || 0).toLocaleString('id-ID'); } } },
              },
              scales: {
                x: { min: 0, max: Math.ceil(xMax + 2), title: { display: true, text: 'Jumlah Laporan Blindspot ✦', color: '#475569', font: { size: 11, weight: '600' } }, grid: { color: 'rgba(148,163,184,.16)', drawBorder: false }, ticks: { color: '#64748b' } },
                y: { min: 0, max: Math.ceil(yMax + 2), title: { display: true, text: 'Repetisi Hari ✦', color: '#475569', font: { size: 11, weight: '600' } }, grid: { color: 'rgba(148,163,184,.16)', drawBorder: false }, ticks: { color: '#64748b' } },
              },
            },
            plugins: [qPlugin],
          });
          return { xMid: xMid, yMid: yMid, points: points };
        }
        function peerBlindspotRenderSimpleLeaderboard(rootId, titleLabel, rows) {
          var root = document.getElementById(rootId);
          if (!root) return;
          if (!rows.length) {
            root.innerHTML = '<p class="px-3 py-8 text-center text-sm text-slate-500">Belum ada data.</p>';
            return;
          }
          var body = rows.map(function (r, idx) {
            return '<tr class="' + (idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/70') + '">' +
              '<td class="px-2.5 py-1.5 text-[11px] text-slate-700">' + escHtml(r.name) + '</td>' +
              '<td class="px-2.5 py-1.5 text-[11px] text-slate-600">' + escHtml(r.site) + '</td>' +
              '<td class="px-2.5 py-1.5 text-[11px] text-slate-600">' + escHtml(r.company) + '</td>' +
              '<td class="px-2.5 py-1.5 text-[11px] text-center font-semibold tabular-nums text-slate-700">' + Number(r.total).toLocaleString('id-ID') + '</td>' +
              '</tr>';
          }).join('');
          root.innerHTML =
            '<table class="w-full min-w-[620px] border-collapse">' +
            '<thead><tr class="bg-[#f8fafc] border-b border-slate-200 text-[10px] uppercase tracking-wide text-slate-600">' +
            '<th class="px-2.5 py-2 text-left">' + escHtml(titleLabel) + '</th>' +
            '<th class="px-2.5 py-2 text-left">Site New</th>' +
            '<th class="px-2.5 py-2 text-left">Perusahaan</th>' +
            '<th class="px-2.5 py-2 text-center">Jumlah Laporan</th>' +
            '</tr></thead><tbody>' + body + '</tbody></table>';
        }
        function peerBlindspotRenderNeedCheck(points, xMid, yMid, siteKeys, bySite, weeks) {
          var root = document.getElementById('peer-blindspot-need-check');
          if (!root) return;
          var lastWeek = weeks.length ? weeks[weeks.length - 1] : null;
          var rows = points
            .filter(function (p) {
              var isQ1 = p.x >= xMid && p.y >= yMid;
              var isQ4 = p.x < xMid && p.y < yMid;
              return (isQ1 || isQ4) && p.week === lastWeek;
            })
            .map(function (p) {
              var q = p.x >= xMid && p.y >= yMid ? 'Kuadran I' : 'Kuadran IV';
              return { site: p.site, week: p.week, kuadran: q, value: p.x, score: p.x * p.y };
            })
            .sort(function (a, b) { return b.score - a.score; })
            .slice(0, 5);
          if (!rows.length) {
            root.innerHTML = '<p class="px-3 py-8 text-center text-sm text-slate-500">Belum ada prioritas tindak lanjut.</p>';
            return;
          }
          var headSites = siteKeys.map(function (s) { return '<th class="px-2 py-2 text-center text-[10px] font-bold uppercase tracking-wide text-slate-500">' + escHtml(s) + '</th>'; }).join('');
          var body = rows.map(function (r, idx) {
            var siteVals = siteKeys.map(function (s) {
              var n = peerTbcNum((bySite[s] || {})[lastWeek]);
              return '<td class="px-2 py-1.5 text-center text-[11px] tabular-nums text-slate-700">' + (n === null ? '0' : Number(n).toLocaleString('id-ID')) + '</td>';
            }).join('');
            var grand = siteKeys.reduce(function (acc, s) { var n = peerTbcNum((bySite[s] || {})[lastWeek]); return acc + (n === null ? 0 : n); }, 0);
            return '<tr class="' + (idx % 2 === 0 ? 'bg-white' : 'bg-slate-50/70') + '">' +
              '<td class="px-2.5 py-1.5 text-[11px] font-semibold text-slate-700">' + (idx + 1) + '. Fokus blindspot ' + escHtml(r.site) + '</td>' +
              '<td class="px-2.5 py-1.5 text-[11px] text-slate-600">Monitoring khusus ' + escHtml(r.week) + '</td>' +
              '<td class="px-2.5 py-1.5 text-center text-[11px] font-semibold text-slate-600">' + escHtml(r.kuadran) + '</td>' +
              siteVals +
              '<td class="px-2.5 py-1.5 text-center text-[11px] font-bold tabular-nums text-slate-700">' + Number(grand).toLocaleString('id-ID') + '</td>' +
              '</tr>';
          }).join('');
          root.innerHTML =
            '<table class="w-full min-w-[840px] border-collapse">' +
            '<thead><tr class="bg-[#f8fafc] border-b border-slate-200 text-[10px] uppercase tracking-wide text-slate-600">' +
            '<th class="px-2.5 py-2 text-left">Catatan (group)</th>' +
            '<th class="px-2.5 py-2 text-left">Subketidaksesuaian</th>' +
            '<th class="px-2.5 py-2 text-center">Kuadran</th>' +
            headSites +
            '<th class="px-2.5 py-2 text-center">Grand</th>' +
            '</tr></thead><tbody>' + body + '</tbody></table>';
        }
        function loadBlindspotModal() {
          var loading = document.getElementById('peer-blindspot-loading');
          var body = document.getElementById('peer-blindspot-body');
          var errEl = document.getElementById('peer-blindspot-error');
          if (errEl) { errEl.classList.add('hidden'); errEl.textContent = ''; }
          if (loading) loading.classList.remove('hidden');
          if (body) body.classList.add('hidden');
          window.setTimeout(function () {
            if (loading) loading.classList.add('hidden');
            if (body) body.classList.remove('hidden');
            if (!peerTbcBlindspotBySite || !peerTbcBlindspotBySite.bySite) {
              if (errEl) {
                errEl.textContent = 'Data blindspot tidak tersedia.';
                errEl.classList.remove('hidden');
              }
              return;
            }
            var weeks = Array.isArray(peerTbcBlindspotBySite.weeks) ? peerTbcBlindspotBySite.weeks : ['W12', 'W13', 'W14', 'W15'];
            var bySite = peerTbcBlindspotBySite.bySite || {};
            var siteKeys = Array.isArray(peerTbcBlindspotBySite.sites) ? peerTbcBlindspotBySite.sites.slice() : Object.keys(bySite);
            peerBlindspotDestroyCharts();
            peerBlindspotRenderSummary(weeks, siteKeys, bySite);
            peerBlindspotRenderStackChart(weeks, siteKeys, bySite);
            var scatterData = peerBlindspotRenderScatter(weeks, siteKeys, bySite);
            if (scatterData) {
              peerBlindspotRenderNeedCheck(scatterData.points, scatterData.xMid, scatterData.yMid, siteKeys, bySite, weeks);
            }
            var leaderboardRows = siteKeys.map(function (s) {
              var total = weeks.reduce(function (acc, w) {
                var n = peerTbcNum((bySite[s] || {})[w]);
                return acc + (n === null ? 0 : n);
              }, 0);
              return {
                name: 'PIC ' + s,
                site: s,
                company: s === 'BMO 3' || s === 'BMO 2' || s === 'BMO 1' ? 'PT Berau Coal' : 'PT Pamapersada Nusantara',
                total: total,
              };
            }).sort(function (a, b) { return b.total - a.total; });
            peerBlindspotRenderSimpleLeaderboard('peer-blindspot-leaderboard-site', 'Nama PIC', leaderboardRows.slice(0, 12));
            peerBlindspotRenderSimpleLeaderboard('peer-blindspot-leaderboard-bc', 'Sid Pelapor', leaderboardRows.filter(function (r) { return r.company === 'PT Berau Coal'; }).slice(0, 10));
            peerBlindspotRenderSimpleLeaderboard('peer-blindspot-leaderboard-mk', 'Sid Pelapor', leaderboardRows.filter(function (r) { return r.company !== 'PT Berau Coal'; }).slice(0, 10));
          }, 80);
        }
        function openBlindspotModal() {
          if (!blindspotModal) return;
          if (tbcGeneralModal && !tbcGeneralModal.classList.contains('hidden')) closeTbcGeneralModal();
          if (complianceModal && !complianceModal.classList.contains('hidden')) closeComplianceModal();
          blindspotModal.classList.remove('hidden');
          blindspotModal.setAttribute('aria-hidden', 'false');
          if (blindspotCard) blindspotCard.setAttribute('aria-expanded', 'true');
          peerTableauLazyLoadViz('tableau-viz-blindspot');
          loadBlindspotModal();
        }
        function closeBlindspotModal() {
          if (!blindspotModal) return;
          peerBlindspotDestroyCharts();
          blindspotModal.classList.add('hidden');
          blindspotModal.setAttribute('aria-hidden', 'true');
          if (blindspotCard) blindspotCard.setAttribute('aria-expanded', 'false');
        }
        window.peerOpenBlindspotModal = function (ev) {
          if (ev && typeof ev.preventDefault === 'function') ev.preventDefault();
          if (ev && typeof ev.stopPropagation === 'function') ev.stopPropagation();
          if (typeof openBlindspotModal === 'function') {
            openBlindspotModal();
            return;
          }
          var modalEl = document.getElementById('peer-blindspot-modal');
          if (!modalEl) return;
          modalEl.classList.remove('hidden');
          modalEl.setAttribute('aria-hidden', 'false');
          peerTableauLazyLoadViz('tableau-viz-blindspot');
        };
        if (blindspotCard) blindspotCard.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          openBlindspotModal();
        });
        document.addEventListener('click', function (e) {
          var blindspotBtn = e.target && e.target.closest ? e.target.closest('#peer-kpi-blindspot-card') : null;
          if (!blindspotBtn) return;
          e.preventDefault();
          e.stopPropagation();
          openBlindspotModal();
        });
        if (blindspotClose) blindspotClose.addEventListener('click', closeBlindspotModal);
        if (blindspotBackdrop) blindspotBackdrop.addEventListener('click', closeBlindspotModal);
        document.addEventListener('keydown', function (e) {
          if (e.key !== 'Escape') return;
          if (blindspotModal && !blindspotModal.classList.contains('hidden')) closeBlindspotModal();
        });
        var complianceModal = document.getElementById('peer-compliance-detail-modal');
        var complianceCard = document.getElementById('peer-kpi-compliance-card');
        var complianceClose = document.getElementById('peer-compliance-detail-close');
        var complianceBackdrop = complianceModal ? complianceModal.querySelector('.peer-compliance-detail-backdrop') : null;
        function formatTanggalDeskriptifId(iso) {
          if (iso == null || iso === '') return 'tanggal temuan belum tercatat';
          var s = String(iso).trim();
          var p = s.split('-');
          if (p.length !== 3) return s;
          var y = parseInt(p[0], 10);
          var mo = parseInt(p[1], 10) - 1;
          var d = parseInt(p[2], 10);
          var months = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
          ];
          if (isNaN(y) || mo < 0 || mo > 11 || isNaN(d)) return s;
          return d + ' ' + months[mo] + ' ' + y;
        }
        function complianceRecIdBtn(id) {
          return (
            '<button type="button" class="js-peer-compliance-detail-btn font-semibold text-primary hover:underline" data-id="' +
            escAttr(id) +
            '">#' +
            escHtml(id) +
            '</button>'
          );
        }
        function renderComplianceRecommendationDescriptive(rows, code) {
          if (!rows || !rows.length) {
            return '';
          }
          var blocks = rows.map(function (row) {
            var id = row.id != null ? String(row.id) : '';
            var tgl = formatTanggalDeskriptifId(row.tanggal_temuan);
            var kat = row.kategori_deviasi != null ? String(row.kategori_deviasi) : 'kategori tidak tercatat';
            var kel = row.bucket_label != null ? String(row.bucket_label) : '';
            var kelTxt = kel ? ' Kelompok deviasi: ' + escHtml(kel) + '.' : '';
            var base =
              'Pada kejadian ' +
              complianceRecIdBtn(id) +
              ', tanggal temuan ' +
              tgl +
              ', dengan kategori deviasi «' +
              escHtml(kat) +
              '».' +
              kelTxt;

            if (code === 'be_tanpa_id_berecord') {
              return (
                '<p class="text-[11px] leading-relaxed text-on-surface">' +
                base +
                ' <strong class="font-semibold text-on-surface">Pelaksanaan edukasi sudah dinyatakan selesai</strong>, namun <strong class="font-semibold text-on-surface">kolom id BeRecord masih kosong</strong>. ' +
                'Rekomendasi: isi id BeRecord yang sesuai dengan rekaman kasus di BeRecord agar pelacakan integrasi lengkap.' +
                '</p>'
              );
            }
            if (code === 'be_belum_selesai') {
              return (
                '<p class="text-[11px] leading-relaxed text-on-surface">' +
                base +
                ' <strong class="font-semibold text-on-surface">Status pelaksanaan edukasi belum menunjukkan selesai</strong> (belum ada CLOSED/SELESAI). ' +
                'Rekomendasi: tutup pelaksanaan setelah edukasi benar-benar rampung, lalu verifikasi di sistem.' +
                '</p>'
              );
            }
            if (code === 'be_belum_selesai_dan_tanpa_id') {
              return (
                '<p class="text-[11px] leading-relaxed text-on-surface">' +
                base +
                ' <strong class="font-semibold text-on-surface">Pelaksanaan belum selesai dan id BeRecord juga kosong.</strong> ' +
                'Rekomendasi: selesaikan siklus edukasi terlebih dahulu, kemudian lengkapi id BeRecord.' +
                '</p>'
              );
            }
            if (code === 'fb_belum_selesai') {
              return (
                '<p class="text-[11px] leading-relaxed text-on-surface">' +
                base +
                ' Untuk kategori Fatigue/Blindspot, BeRecord tidak dipakai; yang perlu adalah <strong class="font-semibold text-on-surface">status pelaksanaan selesai</strong> (CLOSED/SELESAI). ' +
                'Rekomendasi: perbarui status setelah edukasi selesai dilaksanakan.' +
                '</p>'
              );
            }
            return (
              '<p class="text-[11px] leading-relaxed text-on-surface">' +
              base +
              ' Tinjau detail kejadian dan sesuaikan dengan standar penutupan serta pengisian data.' +
              '</p>'
            );
          });
          return (
            '<div class="mt-3 space-y-3 border-t border-dashed border-outline-variant/25 pt-3">' + blocks.join('') + '</div>'
          );
        }
        function renderComplianceRecommendations(recs) {
          var root = document.getElementById('peer-compliance-recommendations');
          if (!root) return;
          if (!recs || !recs.length) {
            root.innerHTML =
              '<p class="text-[11px] text-on-surface-variant">Belum ada rekomendasi (muat ulang data).</p>';
            return;
          }
          root.innerHTML = recs
            .map(function (r) {
              var isAllOk = r.code === 'all_ok';
              var box = isAllOk
                ? 'border-emerald-200 bg-emerald-50/90'
                : 'border-slate-200/90 bg-white';
              var daftar = r.daftar_kejadian || [];
              var n = daftar.length > 0 ? daftar.length : Number(r.jumlah != null ? r.jumlah : 0);
              var code = r.code != null ? String(r.code) : '';
              var sub =
                !isAllOk && n > 0
                  ? '<p class="mt-1 text-[10px] text-on-surface-variant">' +
                    'Mencakup <span class="font-semibold text-on-surface">' +
                    Number(n).toLocaleString('id-ID') +
                    '</span> kejadian.</p>'
                  : '';
              var tbl = isAllOk ? '' : renderComplianceRecommendationDescriptive(daftar, code);
              var emptyList =
                !isAllOk && n > 0 && (!daftar || !daftar.length)
                  ? '<p class="mt-2 text-[10px] text-on-surface-variant">Daftar kejadian tidak tersedia.</p>'
                  : '';
              return (
                '<div class="rounded-xl border ' +
                box +
                ' p-3 sm:p-4 shadow-sm">' +
                '<div class="flex flex-wrap items-start justify-between gap-2">' +
                '<p class="min-w-0 flex-1 text-[12px] font-bold leading-snug text-on-surface">' +
                escHtml(r.judul != null ? String(r.judul) : '—') +
                '</p>' +
                '</div>' +
                sub +
                '<p class="mt-2 text-[11px] leading-relaxed text-on-surface">' +
                escHtml(r.rekomendasi != null ? String(r.rekomendasi) : '') +
                '</p>' +
                tbl +
                emptyList +
                '</div>'
              );
            })
            .join('');
        }
        function renderCompliancePagination(p) {
          var nav = document.getElementById('peer-compliance-pagination');
          if (!nav) return;
          if (!p || !p.total || p.total === 0) {
            nav.classList.add('hidden');
            nav.innerHTML = '';
            return;
          }
          nav.classList.remove('hidden');
          var cur = p.current_page != null ? +p.current_page : 1;
          var last = p.last_page != null ? +p.last_page : 1;
          var from = p.from != null ? p.from : '—';
          var to = p.to != null ? p.to : '—';
          var tot = p.total != null ? +p.total : 0;
          var prevDis = cur <= 1;
          var nextDis = cur >= last;
          nav.innerHTML =
            '<p class="text-[11px] text-on-surface-variant">Menampilkan ' +
            from +
            '–' +
            to +
            ' dari ' +
            tot.toLocaleString('id-ID') +
            ' kejadian</p>' +
            '<div class="flex items-center gap-2">' +
            '<button type="button" class="js-peer-compliance-page rounded-lg border border-outline-variant/30 bg-white px-3 py-1.5 text-[11px] font-bold transition-colors ' +
            (prevDis ? 'cursor-not-allowed opacity-40' : 'hover:bg-surface-container-high') +
            '" data-page="' +
            (cur - 1) +
            '"' +
            (prevDis ? ' disabled' : '') +
            '>Sebelumnya</button>' +
            '<span class="text-[11px] font-semibold tabular-nums text-on-surface">' +
            cur +
            ' / ' +
            last +
            '</span>' +
            '<button type="button" class="js-peer-compliance-page rounded-lg border border-outline-variant/30 bg-white px-3 py-1.5 text-[11px] font-bold transition-colors ' +
            (nextDis ? 'cursor-not-allowed opacity-40' : 'hover:bg-surface-container-high') +
            '" data-page="' +
            (cur + 1) +
            '"' +
            (nextDis ? ' disabled' : '') +
            '>Berikutnya</button>' +
            '</div>';
        }
        function loadComplianceBreakdown(requestedPage) {
          var page = requestedPage != null ? parseInt(String(requestedPage), 10) : 1;
          if (isNaN(page) || page < 1) page = 1;

          var loadingEl = document.getElementById('peer-compliance-table-loading');
          var errEl = document.getElementById('peer-compliance-table-error');
          var wrap = document.getElementById('peer-compliance-table-wrap');
          var tbody = document.getElementById('peer-compliance-modal-tbody');
          var emptyEl = document.getElementById('peer-compliance-table-empty');
          var pagEl = document.getElementById('peer-compliance-pagination');
          if (errEl) {
            errEl.classList.add('hidden');
            errEl.textContent = '';
          }
          if (emptyEl) emptyEl.classList.add('hidden');
          if (tbody) tbody.innerHTML = '';
          if (pagEl) {
            pagEl.classList.add('hidden');
            pagEl.innerHTML = '';
          }
          var recRoot = document.getElementById('peer-compliance-recommendations');
          if (recRoot) {
            recRoot.innerHTML =
              '<p class="text-[11px] italic text-on-surface-variant">Memuat rekomendasi…</p>';
          }
          if (loadingEl) loadingEl.classList.remove('hidden');
          if (wrap) wrap.classList.add('hidden');

          var u = new URL(complianceBreakdownUrl, window.location.origin);
          u.searchParams.set('page', String(page));
          u.searchParams.set('per_page', '15');
          if (!state.all) {
            u.searchParams.set('year', String(state.year));
            u.searchParams.set('month', String(state.month));
          }
          fetch(u.toString(), {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
          })
            .then(function (r) {
              if (!r.ok) throw new Error('Gagal memuat data comply');
              return r.json();
            })
            .then(function (data) {
              if (loadingEl) loadingEl.classList.add('hidden');

              var kpiLike = {
                peer_pressure_compliance_pct: data.peer_pressure_compliance_pct,
                peer_pressure_compliance_comply: data.peer_pressure_compliance_comply,
                peer_pressure_compliance_total: data.peer_pressure_compliance_total
              };
              var scope = data.period_scope === 'month' ? 'month' : 'all';
              syncComplianceModalFromKpi(kpiLike, scope);

              var periodEl = document.getElementById('peer-compliance-modal-period');
              if (periodEl && data.period_caption) {
                if (data.period_scope === 'month') {
                  periodEl.textContent = 'Periode: ' + data.period_caption + ' (filter tanggal temuan).';
                } else {
                  periodEl.textContent = 'Periode: seluruh data kejadian (tanpa filter bulan).';
                }
              }

              renderComplianceRecommendations(data.recommendations || []);

              var total = data.peer_pressure_compliance_total != null ? +data.peer_pressure_compliance_total : 0;
              if (total === 0) {
                if (emptyEl) emptyEl.classList.remove('hidden');
                if (wrap) wrap.classList.add('hidden');
                renderCompliancePagination(null);
                return;
              }

              if (emptyEl) emptyEl.classList.add('hidden');
              if (wrap) wrap.classList.remove('hidden');

              var rows = data.rows || [];
              if (tbody) {
                tbody.innerHTML = rows
                  .map(function (row) {
                    var comply = row.comply === true;
                    var id = row.id != null ? String(row.id) : '';
                    var kat = row.kategori_deviasi != null ? String(row.kategori_deviasi) : 'Deviasi tidak terdefinisi';
                    var site =
                      row.site_new != null
                        ? String(row.site_new)
                        : row.site != null
                        ? String(row.site)
                        : row.bucket_label != null
                        ? String(row.bucket_label)
                        : 'ALL';
                    var week =
                      row.week_label != null
                        ? String(row.week_label)
                        : row.week != null
                        ? String(row.week)
                        : row.tanggal_temuan != null
                        ? String(row.tanggal_temuan)
                        : '—';
                    var jumlah =
                      row.jumlah != null
                        ? Number(row.jumlah)
                        : row.value != null
                        ? Number(row.value)
                        : 1;
                    if (isNaN(jumlah)) jumlah = 1;
                    var ket =
                      row.alasan != null && String(row.alasan).trim() !== ''
                        ? String(row.alasan)
                        : comply
                        ? 'Status comply, lanjutkan konsistensi kontrol.'
                        : 'Perlu follow-up perbaikan dan verifikasi lapangan.';
                    return (
                      '<tr class="align-top hover:bg-[#f8fafc]">' +
                      '<td class="px-2 py-2 tabular-nums text-center"><button type="button" class="js-peer-compliance-detail-btn font-semibold text-primary hover:underline" data-id="' +
                      escAttr(id) +
                      '">#' +
                      escHtml(id) +
                      '</button></td>' +
                      '<td class="px-2 py-2 max-w-[260px]" title="' +
                      escAttr(kat) +
                      '"><span class="line-clamp-2 font-semibold text-slate-700">' +
                      escHtml(kat) +
                      '</span></td>' +
                      '<td class="px-2 py-2 text-center whitespace-nowrap text-[10px] font-semibold uppercase tracking-wide text-slate-600">' +
                      escHtml(site) +
                      '</td>' +
                      '<td class="px-2 py-2 text-center whitespace-nowrap tabular-nums text-slate-600">' +
                      escHtml(week) +
                      '</td>' +
                      '<td class="px-2 py-2 text-center font-bold tabular-nums text-slate-700">' +
                      Number(jumlah).toLocaleString('id-ID') +
                      '</td>' +
                      '<td class="px-2 py-2 text-[10px] leading-snug text-slate-500">' +
                      escHtml(ket) +
                      '</td>' +
                      '</tr>'
                    );
                  })
                  .join('');
              }

              renderCompliancePagination(data.pagination || null);
            })
            .catch(function (err) {
              if (loadingEl) loadingEl.classList.add('hidden');
              if (wrap) wrap.classList.add('hidden');
              renderComplianceRecommendations([]);
              renderCompliancePagination(null);
              if (errEl) {
                errEl.textContent = err.message || 'Gagal memuat data.';
                errEl.classList.remove('hidden');
              }
            });
        }
        function openComplianceModal() {
          if (!complianceModal) return;
          if (blindspotModal && !blindspotModal.classList.contains('hidden')) closeBlindspotModal();
          complianceModal.classList.remove('hidden');
          complianceModal.setAttribute('aria-hidden', 'false');
          if (complianceCard) complianceCard.setAttribute('aria-expanded', 'true');
        }
        function closeComplianceModal() {
          if (!complianceModal) return;
          complianceModal.classList.add('hidden');
          complianceModal.setAttribute('aria-hidden', 'true');
          if (complianceCard) complianceCard.setAttribute('aria-expanded', 'false');
        }
        if (complianceCard) complianceCard.addEventListener('click', openComplianceModal);
        if (complianceClose) complianceClose.addEventListener('click', closeComplianceModal);
        if (complianceBackdrop) complianceBackdrop.addEventListener('click', closeComplianceModal);
        if (complianceModal) {
          complianceModal.addEventListener('click', function (e) {
            var pg = e.target.closest('.js-peer-compliance-page');
            if (pg) {
              if (pg.disabled) return;
              e.preventDefault();
              var pn = parseInt(pg.getAttribute('data-page'), 10);
              if (!isNaN(pn) && pn >= 1) loadComplianceBreakdown(pn);
              return;
            }
            var btn = e.target.closest('.js-peer-compliance-detail-btn');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();
            var id = btn.getAttribute('data-id');
            if (id && typeof window.peerPressureOpenKejadianDetail === 'function') {
              closeComplianceModal();
              window.peerPressureOpenKejadianDetail(id);
            }
          });
        }
        if (allDataBtn) {
          allDataBtn.addEventListener('click', function () {
            tempAll = true;
            syncModalHighlight();
          });
        }
        document.querySelectorAll('.peer-modal-year').forEach(function (b) {
          b.addEventListener('click', function () {
            tempAll = false;
            tempYear = +b.getAttribute('data-year');
            syncModalHighlight();
          });
        });
        document.querySelectorAll('.peer-modal-month').forEach(function (b) {
          b.addEventListener('click', function () {
            tempAll = false;
            tempMonth = +b.getAttribute('data-month');
            syncModalHighlight();
          });
        });
        if (applyBtn) {
          applyBtn.addEventListener('click', function () {
            closeWeeklyModal();
            if (loadingEl) {
              loadingEl.classList.remove('hidden');
              loadingEl.setAttribute('aria-busy', 'true');
            }
            var u = new URL(weeklyTrendUrl, window.location.origin);
            if (!tempAll) {
              u.searchParams.set('year', String(tempYear));
              u.searchParams.set('month', String(tempMonth));
            }
            fetch(u.toString(), {
              headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
              credentials: 'same-origin'
            })
              .then(function (r) {
                if (!r.ok) throw new Error('Gagal memuat data chart');
                return r.json();
              })
              .then(function (wt) {
                renderWeeklyChart(wt);
                if (wt.kpi) renderKpi(wt.kpi, wt.period_scope);
                if (wt.evaluation_summary) renderEvaluationSummary(wt.evaluation_summary);
                if (wt.insight_cards) renderInsightCards(wt.insight_cards);
                updateFormHiddenAndUrl();
                loadPeerHighlightIssueRecommendation();
              })
              .catch(function (err) {
                alert(err.message || 'Terjadi kesalahan');
              })
              .finally(function () {
                if (loadingEl) {
                  loadingEl.classList.add('hidden');
                  loadingEl.setAttribute('aria-busy', 'false');
                }
              });
          });
        }
        document.addEventListener('keydown', function (e) {
          if (e.key !== 'Escape') return;
          if (complianceModal && !complianceModal.classList.contains('hidden')) {
            closeComplianceModal();
            return;
          }
          if (devModal && !devModal.classList.contains('hidden')) {
            closeDeviationModal();
            return;
          }
          if (!modal || modal.classList.contains('hidden')) return;
          closeWeeklyModal();
        });
        document.addEventListener('click', function (e) {
          var t = e.target.closest('tr.peer-rv-toggle');
          if (!t) return;
          var detail = t.nextElementSibling;
          if (!detail || !detail.classList.contains('peer-rv-expand')) return;
          detail.classList.toggle('hidden');
          var isOpen = !detail.classList.contains('hidden');
          var ch = t.querySelector('.peer-rv-chevron');
          if (ch) ch.textContent = isOpen ? 'expand_less' : 'expand_more';
          t.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
        document.addEventListener('keydown', function (e) {
          if (e.key !== 'Enter' && e.key !== ' ') return;
          var t = e.target.closest('tr.peer-rv-toggle');
          if (!t || document.activeElement !== t) return;
          e.preventDefault();
          t.click();
        });
        loadPeerHighlightIssueRecommendation();
      })();
      </script>
      <script>
      (function () {
        var modal = document.getElementById('peer-evaluation-summary-modal');
        var openBtn = document.getElementById('peer-open-evaluation-modal');
        var closeBtn = document.getElementById('peer-evaluation-summary-close');
        var backdrop = modal ? modal.querySelector('.peer-evaluation-summary-backdrop') : null;
        function openModal() {
          if (!modal) return;
          modal.classList.remove('hidden');
          modal.setAttribute('aria-hidden', 'false');
        }
        function closeModal() {
          if (!modal) return;
          modal.classList.add('hidden');
          modal.setAttribute('aria-hidden', 'true');
        }
        if (openBtn) openBtn.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (backdrop) backdrop.addEventListener('click', closeModal);
        document.addEventListener('keydown', function (e) {
          if (e.key !== 'Escape' || !modal || modal.classList.contains('hidden')) return;
          closeModal();
        });
      })();
      </script>
      <script>
      (function () {
        var profilingDetailUrl = @json(route('peer-pressure-edukasi.dashboard.pelanggar-profiling'));
        var modal = document.getElementById('peer-pelanggar-profiling-modal');
        var titleEl = document.getElementById('peer-pelanggar-profiling-title');
        var subEl = document.getElementById('peer-pelanggar-profiling-subtitle');
        var loadingEl = document.getElementById('peer-pelanggar-profiling-loading');
        var errorEl = document.getElementById('peer-pelanggar-profiling-error');
        var bodyEl = document.getElementById('peer-pelanggar-profiling-body');
        var footerEl = document.getElementById('peer-pelanggar-profiling-footer');
        var closeBtn = document.getElementById('peer-pelanggar-profiling-close');
        var closeBtn2 = document.getElementById('peer-pelanggar-profiling-close2');
        var noteBtn = document.getElementById('peer-pelanggar-profiling-note');
        var pdfBtn = document.getElementById('peer-pelanggar-profiling-pdf');
        var backdrop = modal ? modal.querySelector('.peer-pelanggar-profiling-backdrop') : null;

        function esc(s) {
          if (s == null) return '';
          var d = document.createElement('div');
          d.textContent = String(s);
          return d.innerHTML;
        }

        function setOpen(open) {
          if (!modal) return;
          if (open) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
          } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
          }
        }

        function showLoading() {
          if (loadingEl) {
            loadingEl.classList.remove('hidden');
            loadingEl.classList.add('flex');
          }
          if (errorEl) errorEl.classList.add('hidden');
          if (bodyEl) {
            bodyEl.classList.add('hidden');
            bodyEl.innerHTML = '';
          }
          if (footerEl) footerEl.classList.add('hidden');
        }

        function showError(msg) {
          if (loadingEl) {
            loadingEl.classList.add('hidden');
            loadingEl.classList.remove('flex');
          }
          if (bodyEl) bodyEl.classList.add('hidden');
          if (footerEl) footerEl.classList.add('hidden');
          if (errorEl) {
            errorEl.classList.remove('hidden');
            errorEl.textContent = msg || 'Terjadi kesalahan.';
          }
        }

        function sectionTitle(icon, text) {
          return (
            '<div class="flex items-center gap-2 text-on-surface">' +
            '<span class="material-symbols-outlined text-lg text-primary" data-icon="' +
            esc(icon) +
            '">' +
            esc(icon) +
            '</span>' +
            '<h3 class="text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">' +
            esc(text) +
            '</h3></div>' +
            '<div class="my-2 border-t border-outline-variant/20"></div>'
          );
        }

        function buildRecencyChart(gaps) {
          if (!gaps || gaps.length === 0) {
            return '<p class="text-[11px] leading-relaxed text-on-surface-variant">Belum cukup data interval (minimal 2 kejadian dalam jendela).</p>';
          }
          var w = 100;
          var h = 40;
          var pad = 8;
          var maxG = Math.max.apply(null, gaps);
          var minG = Math.min.apply(null, gaps);
          var range = Math.max(maxG - minG, 1);
          var n = gaps.length;
          var pts = gaps.map(function (g, i) {
            var x = n === 1 ? w / 2 : pad + (i / (n - 1)) * (w - 2 * pad);
            var y = h - pad - ((g - minG) / range) * (h - 2 * pad);
            return [x, y];
          });
          var dPath = pts
            .map(function (pt, i) {
              return (i === 0 ? 'M' : 'L') + pt[0].toFixed(2) + ' ' + pt[1].toFixed(2);
            })
            .join(' ');
          var circles = pts
            .map(function (pt) {
              return (
                '<circle cx="' +
                pt[0].toFixed(2) +
                '" cy="' +
                pt[1].toFixed(2) +
                '" r="2" fill="currentColor" class="text-primary" />'
              );
            })
            .join('');
          var labelRow = gaps
            .map(function (g) {
              return '<span class="text-[10px] font-extrabold tabular-nums text-primary">' + esc(String(g)) + ' h</span>';
            })
            .join('<span class="px-1 text-on-surface-variant/45">→</span>');
          return (
            '<div class="space-y-3">' +
            '<svg class="w-full max-w-md text-primary" viewBox="0 0 ' +
            w +
            ' ' +
            h +
            '" preserveAspectRatio="xMidYMid meet" aria-hidden="true">' +
            '<path d="' +
            dPath +
            '" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />' +
            circles +
            '</svg>' +
            '<div class="flex flex-wrap items-center gap-1">' +
            labelRow +
            '</div></div>'
          );
        }

        function buildPoOneDetailSection(sec) {
          var items = sec.items || [];
          var inner;
          if (!items.length) {
            inner =
              '<p class="py-2 text-[10px] italic leading-relaxed text-on-surface-variant">Belum ada data kejadian untuk bagian ini pada jendela 6 bulan.</p>';
          } else {
            inner =
              '<div class="overflow-x-auto">' +
              '<table class="w-full min-w-[280px] border-collapse text-left text-[10px]">' +
              '<thead><tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[9px] font-bold uppercase tracking-wide text-on-surface-variant">' +
              '<th class="whitespace-nowrap px-2 py-1.5">ID</th>' +
              '<th class="whitespace-nowrap px-2 py-1.5">Tanggal temuan</th>' +
              '<th class="px-2 py-1.5">Kategori temuan</th>' +
              '<th class="px-2 py-1.5">Lokasi</th>' +
              '</tr></thead><tbody>' +
              items
                .map(function (it) {
                  return (
                    '<tr class="border-b border-outline-variant/10">' +
                    '<td class="whitespace-nowrap px-2 py-1.5 font-mono font-semibold tabular-nums text-primary">#' +
                    esc(String(it.kejadian_id != null ? it.kejadian_id : '—')) +
                    '</td>' +
                    '<td class="whitespace-nowrap px-2 py-1.5 tabular-nums text-on-surface">' +
                    esc(it.tanggal || '') +
                    '</td>' +
                    '<td class="px-2 py-1.5 text-on-surface">' +
                    esc(it.kategori || '') +
                    '</td>' +
                    '<td class="px-2 py-1.5 text-on-surface-variant">' +
                    esc(it.lokasi || '') +
                    '</td></tr>'
                  );
                })
                .join('') +
              '</tbody></table></div>';
          }
          return (
            '<div class="rounded-lg border border-outline-variant/20 bg-white p-3 shadow-sm">' +
            '<p class="mb-2 border-b border-outline-variant/15 pb-2 text-[10px] font-bold uppercase tracking-wide text-primary">' +
            esc(sec.heading || '') +
            '</p>' +
            inner +
            '</div>'
          );
        }

        function buildPerOrangSection(d) {
          var po = d.per_orang;
          if (!po || !po.rows || !po.rows.length) {
            return '';
          }
          var tableRows = po.rows
            .map(function (r) {
              var hit = !!r.terpenuhi;
              var trCls = hit
                ? 'bg-amber-50/60 border-l-2 border-amber-400/80'
                : 'border-l-2 border-transparent';
              var sections = r.detail_sections || [];
              var hasDetail = sections.length > 0;
              var rowCls =
                trCls +
                ' border-b border-outline-variant/10' +
                (hasDetail ? ' peer-po-toggle cursor-pointer hover:bg-[#f0f4f8]' : '');
              var chev = hasDetail
                ? '<span class="material-symbols-outlined ml-1 shrink-0 text-lg text-primary peer-po-chevron" data-icon="expand_more">expand_more</span>'
                : '';
              var mainTr =
                '<tr class="' +
                rowCls +
                '"' +
                (hasDetail
                  ? ' tabindex="0" role="button" title="Klik atau Enter untuk buka/tutup detail kejadian"'
                  : '') +
                '>' +
                '<td class="px-3 py-2.5 text-[11px] font-bold text-primary align-top">' +
                esc(r.kategori || '—') +
                '</td>' +
                '<td class="px-3 py-2.5 text-[11px] leading-snug text-on-surface-variant align-top">' +
                esc(r.kriteria || '') +
                '</td>' +
                '<td class="px-3 py-2.5 align-top">' +
                '<div class="flex items-center justify-between gap-2">' +
                '<span class="text-[11px] font-semibold text-on-surface">' +
                esc(r.status || '—') +
                '</span>' +
                chev +
                '</div></td></tr>';
              if (!hasDetail) {
                return mainTr;
              }
              var detailBlocks = sections.map(buildPoOneDetailSection).join('');
              var detailTr =
                '<tr class="peer-po-detail hidden border-b border-outline-variant/10 bg-[#f8fafc]">' +
                '<td colspan="3" class="px-3 py-3 sm:px-4">' +
                '<p class="mb-2 text-[10px] font-bold text-on-surface-variant">Detail kejadian</p>' +
                '<div class="space-y-3">' +
                detailBlocks +
                '</div></td></tr>';
              return mainTr + detailTr;
            })
            .join('');
          var issueText = po.issue_ringkas || '';
          var peerN = po.peer_sebagai_peer_kejadian != null ? String(po.peer_sebagai_peer_kejadian) : '0';
          return (
            '<section class="rounded-xl border border-outline-variant/15 bg-white p-4">' +
            sectionTitle('badge', 'Per Orang') +
            '<p class="mb-3 text-[10px] leading-relaxed text-on-surface-variant">Klik baris yang memiliki ikon panah untuk melihat daftar kejadian sebagai <strong class="font-semibold text-on-surface">pelanggar</strong> dan sebagai <strong class="font-semibold text-on-surface">peer</strong> (Awareness / Awareness 2).</p>' +
            '<div class="overflow-x-auto rounded-lg border border-outline-variant/15">' +
            '<table class="w-full min-w-[320px] text-left text-[11px]">' +
            '<thead class="bg-[#f8fafc] text-[9px] font-bold uppercase tracking-wide text-on-surface-variant">' +
            '<tr><th class="px-3 py-2">Kategori</th><th class="px-3 py-2">Kriteria</th><th class="px-3 py-2">Status / nilai</th></tr></thead>' +
            '<tbody id="peer-po-tbody">' +
            tableRows +
            '</tbody></table></div>' +
            '<div class="mt-4 rounded-xl border border-primary/15 bg-primary/[0.04] px-3 py-3">' +
            '<p class="text-[9px] font-bold uppercase tracking-wider text-primary">Issue (ringkasan)</p>' +
            '<p class="mt-1 text-[11px] leading-relaxed text-on-surface">' +
            esc(issueText) +
            '</p></div>' +
            '<p class="mt-2 text-[10px] text-on-surface-variant">Kejadian sebagai peer dalam jendela yang sama: <span class="font-mono font-semibold text-on-surface">' +
            esc(peerN) +
            '</span> kejadian.</p></section>'
          );
        }

        function trendBlock(d) {
          var tr = d.recency_trend || 'stable';
          var icon = tr === 'worsening' ? 'trending_down' : tr === 'improving' ? 'trending_up' : 'timeline';
          var cls =
            tr === 'worsening'
              ? 'text-amber-700 bg-amber-50 border-amber-200/80'
              : tr === 'improving'
                ? 'text-[#059669] bg-[#ecfdf5] border-[#059669]/25'
                : 'text-on-surface-variant bg-[#f8fafc] border-outline-variant/20';
          return (
            '<p class="mt-2 flex items-start gap-2 rounded-xl border px-3 py-2 text-[11px] leading-snug ' +
            cls +
            '">' +
            '<span class="material-symbols-outlined shrink-0 text-base" data-icon="' +
            esc(icon) +
            '">' +
            esc(icon) +
            '</span><span>' +
            esc(d.recency_caption || '') +
            '</span></p>'
          );
        }

        function renderBody(d) {
          var st = d.status_level || 'normal';
          var stCls =
            st === 'high'
              ? 'text-amber-800 bg-amber-50 border-amber-200/90'
              : st === 'moderate'
                ? 'text-amber-800/90 bg-amber-50/80 border-amber-200/60'
                : 'text-[#059669] bg-[#ecfdf5] border-[#059669]/25';
          var rows = (d.riwayat || [])
            .map(function (r) {
              return (
                '<tr class="border-b border-outline-variant/10">' +
                '<td class="whitespace-nowrap px-2 py-2 text-[11px] font-medium tabular-nums text-on-surface">' +
                esc(r.tanggal_short) +
                '</td>' +
                '<td class="px-2 py-2 text-[11px] text-on-surface">' +
                esc(r.kategori) +
                '</td>' +
                '<td class="px-2 py-2 text-[11px] text-on-surface-variant">' +
                esc(r.lokasi) +
                '</td>' +
                '<td class="whitespace-nowrap px-2 py-2 text-[10px] font-bold uppercase text-on-surface-variant">' +
                esc(r.status) +
                '</td></tr>'
              );
            })
            .join('');
          var kor = (d.korelasi || [])
            .map(function (line) {
              return (
                '<li class="flex gap-2 text-[11px] leading-relaxed text-on-surface">' +
                '<span class="shrink-0 text-[#059669]">✓</span><span>' +
                esc(line) +
                '</span></li>'
              );
            })
            .join('');
          var rek = (d.rekomendasi || [])
            .map(function (line) {
              return (
                '<li class="flex gap-2.5 text-[11px] leading-relaxed text-on-surface">' +
                '<span class="mt-0.5 inline-flex h-4 w-4 shrink-0 rounded border border-outline-variant/35 bg-white"></span>' +
                '<span>' +
                esc(line) +
                '</span></li>'
              );
            })
            .join('');
          return (
            '<p class="text-[10px] leading-relaxed text-on-surface-variant">' +
            esc(d.window_caption || '') +
            '</p>' +
            '<section class="rounded-xl border border-outline-variant/15 bg-[#fafbfc] p-4">' +
            sectionTitle('person', 'Informasi dasar') +
            '<div class="grid grid-cols-1 gap-3 text-[11px] sm:grid-cols-2">' +
            '<div><span class="block text-[9px] font-bold uppercase tracking-wider text-on-surface-variant">NPK</span><span class="font-mono font-semibold text-on-surface">' +
            esc(d.npk) +
            '</span></div>' +
            '<div><span class="block text-[9px] font-bold uppercase tracking-wider text-on-surface-variant">Departemen</span><span class="text-on-surface">' +
            esc(d.departemen) +
            '</span></div>' +
            '<div><span class="block text-[9px] font-bold uppercase tracking-wider text-on-surface-variant">Posisi</span><span class="text-on-surface">' +
            esc(d.posisi) +
            '</span></div>' +
            '<div><span class="block text-[9px] font-bold uppercase tracking-wider text-on-surface-variant">Grup</span><span class="text-on-surface">' +
            esc(d.grup) +
            '</span></div>' +
            '<div><span class="block text-[9px] font-bold uppercase tracking-wider text-on-surface-variant">Status</span><span class="inline-flex rounded-lg border px-2 py-0.5 text-[10px] font-bold ' +
            stCls +
            '">' +
            esc(d.status_label) +
            '</span></div>' +
            '<div><span class="block text-[9px] font-bold uppercase tracking-wider text-on-surface-variant">Last education</span><span class="tabular-nums text-on-surface">' +
            esc(d.last_education_label) +
            '</span></div></div></section>' +
            buildPerOrangSection(d) +
            '<section class="rounded-xl border border-outline-variant/15 bg-white p-4">' +
            sectionTitle('table_chart', 'Riwayat pelanggaran (6 bulan terakhir)') +
            '<div class="overflow-x-auto rounded-lg border border-outline-variant/15">' +
            '<table class="w-full min-w-[280px] text-left text-[11px]">' +
            '<thead class="bg-[#f8fafc] text-[9px] font-bold uppercase tracking-wide text-on-surface-variant">' +
            '<tr><th class="px-2 py-2">Tanggal</th><th class="px-2 py-2">Kategori</th><th class="px-2 py-2">Lokasi</th><th class="px-2 py-2">Status</th></tr></thead>' +
            '<tbody>' +
            (rows || '<tr><td colspan="4" class="px-2 py-3 text-on-surface-variant">Tidak ada baris.</td></tr>') +
            '</tbody></table></div></section>' +
            '<section class="rounded-xl border border-outline-variant/15 bg-white p-4">' +
            sectionTitle('show_chart', 'Recency score trend') +
            buildRecencyChart(d.recency_gap_days || []) +
            trendBlock(d) +
            '</section>' +
            '<section class="rounded-xl border border-outline-variant/15 bg-white p-4">' +
            sectionTitle('hub', 'Korelasi terdeteksi') +
            '<ul class="space-y-2">' +
            (kor || '<li class="text-[11px] text-on-surface-variant">—</li>') +
            '</ul></section>' +
            '<section class="rounded-xl border border-outline-variant/15 bg-white p-4">' +
            sectionTitle('task_alt', 'Rekomendasi tindakan') +
            '<ul class="space-y-2.5">' +
            rek +
            '</ul></section>'
          );
        }

        function openProfiling(sid, nama) {
          if (!modal || !sid) return;
          var label = (nama || '').trim() ? String(nama).trim().toUpperCase() : 'PELANGGAR';
          if (titleEl) {
            titleEl.textContent = 'Detail pelanggar: ' + label + ' (' + String(sid).trim() + ')';
          }
          if (subEl) subEl.textContent = '';
          setOpen(true);
          showLoading();
          fetch(profilingDetailUrl + '?sid=' + encodeURIComponent(String(sid).trim()), {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
          })
            .then(function (res) {
              return res.json().then(function (data) {
                if (!res.ok) {
                  throw new Error((data && data.message) || res.statusText || 'Gagal memuat data.');
                }
                return data;
              });
            })
            .then(function (d) {
              if (loadingEl) {
                loadingEl.classList.add('hidden');
                loadingEl.classList.remove('flex');
              }
              if (errorEl) errorEl.classList.add('hidden');
              if (bodyEl) {
                bodyEl.innerHTML = renderBody(d);
                bodyEl.classList.remove('hidden');
              }
              if (footerEl) footerEl.classList.remove('hidden');
            })
            .catch(function (err) {
              showError(err.message || 'Gagal memuat detail.');
            });
        }

        function closeProfiling() {
          setOpen(false);
        }

        if (closeBtn) closeBtn.addEventListener('click', closeProfiling);
        if (closeBtn2) closeBtn2.addEventListener('click', closeProfiling);
        if (backdrop) backdrop.addEventListener('click', closeProfiling);
        document.addEventListener('keydown', function (e) {
          if (e.key !== 'Escape' || !modal || modal.classList.contains('hidden')) return;
          closeProfiling();
        });

        document.addEventListener('click', function (e) {
          var row = e.target.closest('#peer-insight-cards-root .peer-profiling-row');
          if (!row) return;
          e.preventDefault();
          openProfiling(row.getAttribute('data-sid'), row.getAttribute('data-nama'));
        });

        document.addEventListener('keydown', function (e) {
          if (e.key !== 'Enter' && e.key !== ' ') return;
          var row = e.target.closest('#peer-insight-cards-root .peer-profiling-row');
          if (!row) return;
          e.preventDefault();
          openProfiling(row.getAttribute('data-sid'), row.getAttribute('data-nama'));
        });

        if (noteBtn) {
          noteBtn.addEventListener('click', function () {
            window.alert('Catat tindakan melalui prosedur internal HSE / sistem dokumentasi yang berlaku.');
          });
        }
        if (pdfBtn) {
          pdfBtn.addEventListener('click', function () {
            var el = document.getElementById('peer-pelanggar-profiling-body');
            if (!el || el.classList.contains('hidden')) return;
            var w = window.open('', '_blank');
            if (!w) return;
            w.document.write(
              '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Detail pelanggar</title><style>body{font-family:system-ui,sans-serif;padding:24px;font-size:12px;color:#111;} h1{font-size:16px;} table{border-collapse:collapse;width:100%;margin-top:12px;} th,td{border:1px solid #ccc;padding:6px;text-align:left;}</style></head><body><h1>Detail pelanggar</h1>'
            );
            w.document.write(el.innerHTML);
            w.document.write('</body></html>');
            w.document.close();
            w.focus();
            w.print();
            try {
              w.close();
            } catch (err) {}
          });
        }

        function togglePeerPoRow(mainTr) {
          var detail = mainTr.nextElementSibling;
          if (!detail || !detail.classList.contains('peer-po-detail')) return;
          detail.classList.toggle('hidden');
          var open = !detail.classList.contains('hidden');
          var ch = mainTr.querySelector('.peer-po-chevron');
          if (ch) {
            ch.textContent = open ? 'expand_less' : 'expand_more';
            ch.setAttribute('data-icon', open ? 'expand_less' : 'expand_more');
          }
        }

        document.addEventListener('click', function (e) {
          var tr = e.target.closest('#peer-pelanggar-profiling-body tr.peer-po-toggle');
          if (!tr) return;
          togglePeerPoRow(tr);
        });

        document.addEventListener('keydown', function (e) {
          if (e.key !== 'Enter' && e.key !== ' ') return;
          var tr = e.target.closest('#peer-pelanggar-profiling-body tr.peer-po-toggle');
          if (!tr || document.activeElement !== tr) return;
          e.preventDefault();
          togglePeerPoRow(tr);
        });
      })();
      </script>
   </body>
</html>