<!DOCTYPE html>
<html class="light" lang="en">
   <head>
      <meta charset="utf-8"/>
      <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
      <title>Peer Pressure Program Evaluation</title>
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
                  <h1 class="font-headline font-bold text-[#3952bc] text-xl tracking-tighter leading-tight">Sambarata Mining Operation</h1>
                  <p class="text-on-surface-variant text-[10px] font-bold uppercase tracking-widest">Peer Pressure Program</p>
               </div>
               <div class="h-8 w-px bg-[#dfe3e6] hidden lg:block"></div>
               <nav class="hidden md:flex gap-8">
                  <a class="text-[#3952bc] border-b-2 border-[#3952bc] pb-1 font-bold text-sm tracking-tight" href="#">Overview</a>
                  <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Site Filters</a>
                  <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Department</a>
                  <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Reports</a>
                  <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Analytics</a>
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
         @endphp
         <!-- Header & Top Filters -->
         <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6 pb-6 border-b border-outline-variant/30">
            <div>
               <nav class="flex items-center gap-2 text-xs font-bold text-on-surface-variant uppercase mb-2">
                  <span>Dashboard</span>
                  <span class="material-symbols-outlined text-xs">chevron_right</span>
                  <span class="text-primary">Peer Pressure Evaluation</span>
               </nav>
               <h2 class="font-headline font-extrabold text-4xl text-on-background tracking-tight">Peer Pressure Evaluation</h2>
               <p class="text-on-surface-variant font-medium mt-1">Program performance metrics 2025 - 2026 • Updated 5 mins ago</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
              
                <button type="button" id="peer-open-weekly-period" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-4 py-3 text-left shadow-inner transition-colors hover:bg-surface-container-high sm:w-auto sm:min-w-[14rem]">
                        <span class="material-symbols-outlined text-primary text-xl">calendar_month</span>
                        <span class="flex min-w-0 flex-1 flex-col items-start">
                           <span class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Periode chart</span>
                           <span id="peer-weekly-period-label" class="truncate text-sm font-bold text-on-surface">{{ $wt['period_caption'] ?? 'Pilih bulan' }}</span>
                        </span>
                        <span class="material-symbols-outlined text-on-surface-variant">expand_more</span>
                     </button>

          
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
         @endphp
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
         <button type="button" id="peer-kpi-deviation-card" class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between text-left w-full cursor-pointer transition-all hover:shadow-md hover:ring-2 hover:ring-primary/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40" aria-haspopup="dialog" aria-expanded="false" aria-controls="peer-deviation-category-modal">
               <div class="flex justify-between items-start">
                  <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Total Deviasi Pelanggaran</span>
                  <div class="p-2 bg-[#fef3c7] rounded-lg">
                     <span class="material-symbols-outlined text-[#d97706]" data-icon="groups">groups</span>
                  </div>
               </div>
               <div class="mt-4">
                  <p id="peer-kpi-deviation-total" class="font-headline font-extrabold text-4xl tabular-nums">{{ number_format($dvPreTotal) }}</p>
                  <p class="text-on-surface-variant text-[11px] font-medium mt-1">Jumlah kejadian menurut kategori deviasi · klik untuk detail</p>
               </div>
            </button>
            <div class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between">
               <div class="flex justify-between items-start">
                  <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Pelaksanaan Peer Pressure</span>
                  <div class="p-2 bg-primary/10 rounded-lg">
                     <span class="material-symbols-outlined text-primary" data-icon="assignment_late">assignment_late</span>
                  </div>
               </div>
               <div class="mt-4">
                  <p id="peer-kpi-total" class="font-headline font-extrabold text-4xl">{{ number_format($kpiTotal) }}</p>
                  <div id="peer-kpi-total-trend" class="mt-1">
                  @if($kpiTrendPct !== null)
                  <p class="text-[11px] font-bold flex items-center gap-1 {{ $kpiTrendPct <= 0 ? 'text-[#059669]' : 'text-error' }}">
                     <span class="material-symbols-outlined text-xs" data-icon="{{ $kpiTrendPct <= 0 ? 'trending_down' : 'trending_up' }}">{{ $kpiTrendPct <= 0 ? 'trending_down' : 'trending_up' }}</span>
                     {{ $kpi['total_cases_trend_label'] ?? '' }}
                  </p>
                  @else
                  <p class="text-on-surface-variant text-[11px] font-medium">{{ $kpi['total_cases_trend_label'] ?? '—' }}</p>
                  @endif
                  </div>
               </div>
            </div>
            <div class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between">
               <div class="flex justify-between items-start">
                  <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Pelaksanaan Rate</span>
                  <div class="p-2 bg-[#dcfce7] rounded-lg">
                     <span class="material-symbols-outlined text-[#16a34a]" data-icon="task_alt">task_alt</span>
                  </div>
               </div>
               <div class="mt-4">
                  <div class="flex justify-between items-end gap-2">
                     <p id="peer-kpi-completion" class="font-headline font-extrabold text-4xl">{{ number_format($kpiCompletion, 1) }}%</p>
                     @if(isset($kpi['completion_rate_delta_pp']) && $kpi['completion_rate_delta_pp'] !== null)
                     <span id="peer-kpi-completion-delta" class="text-[11px] font-bold shrink-0 {{ ($kpi['completion_rate_delta_pp'] ?? 0) >= 0 ? 'text-[#16a34a]' : 'text-error' }}">{{ ($kpi['completion_rate_delta_pp'] ?? 0) >= 0 ? '+' : '' }}{{ number_format((float) $kpi['completion_rate_delta_pp'], 1) }} p.p.</span>
                     @else
                     <span id="peer-kpi-completion-delta" class="text-[11px] font-bold text-on-surface-variant shrink-0">—</span>
                     @endif
                  </div>
                  <p id="peer-kpi-completion-hint" class="text-on-surface-variant text-[10px] font-medium mt-1">{{ ($chartPeriodMonth ?? false) ? 'Selesai (CLOSED/SELESAI) ÷ total kejadian pada bulan yang dipilih' : 'Selesai (CLOSED/SELESAI) ÷ total kejadian (seluruh data)' }}</p>
                  <div class="w-full bg-[#f1f5f9] h-2 rounded-full mt-3 overflow-hidden border border-outline-variant/10">
                     <div id="peer-kpi-completion-bar" class="bg-[#16a34a] h-full rounded-full transition-all" style="width: {{ $kpiBarW }}%"></div>
                  </div>
               </div>
            </div>
           
            <button type="button" id="peer-kpi-compliance-card" class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between text-left w-full cursor-pointer transition-all hover:shadow-md hover:ring-2 hover:ring-secondary/25 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40" aria-haspopup="dialog" aria-expanded="false" aria-controls="peer-compliance-detail-modal">
               <div class="flex justify-between items-start">
                  <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Pelaksanaan Comply</span>
                  <div class="p-2 bg-secondary/10 rounded-lg">
                     <span class="material-symbols-outlined text-secondary" data-icon="verified">verified</span>
                  </div>
               </div>
               <div class="mt-4">
                  <p id="peer-kpi-pelaksanaan-compliance" class="font-headline font-extrabold text-4xl">{{ number_format((float) ($kpi['peer_pressure_compliance_pct'] ?? 0), 1) }}<span class="text-2xl font-bold">%</span></p>
                  <p class="text-on-surface-variant text-[11px] font-medium mt-1 leading-snug">
                     <span id="peer-kpi-pelaksanaan-compliance-count">{{ (int) ($kpi['peer_pressure_compliance_comply'] ?? 0) }}/{{ (int) ($kpi['peer_pressure_compliance_total'] ?? 0) }}</span> kejadian (5 kategori). Klik untuk penjelasan detail.
                  </p>
               </div>
            </button>
         </div>
         <!-- Charts & Recommendations Grid -->
         <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Trend Analysis -->
            @php
               $wtWeeks = $wt['weeks'] ?? [];
               $wtLinePct = (float) ($wt['target_line_bottom_pct'] ?? 0);
               $wtDevCats = $wt['deviation_categories'] ?? [];
               $wtGran = $wt['chart_granularity'] ?? 'month';
               $wtGranLabel = $wtGran === 'week' ? 'Per minggu (dalam bulan dipilih)' : 'Per bulan';
            @endphp
            <div id="peer-weekly-chart-card" class="relative lg:col-span-8 bg-white p-8 rounded-2xl anchored-card">
               <div id="peer-weekly-chart-loading" class="hidden absolute inset-0 z-20 flex flex-col items-center justify-center rounded-2xl bg-white/85 backdrop-blur-[2px]" aria-live="polite" aria-busy="false">
                  <span class="material-symbols-outlined text-4xl animate-spin text-primary" style="animation-duration:1.1s">progress_activity</span>
                  <p class="mt-3 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Memuat chart…</p>
               </div>
               <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                  <div>
                     <h3 class="font-headline font-bold text-xl">Trend Pelanggaran</h3>
                     <!-- <p id="peer-trend-chart-subtitle" class="text-[10px] text-on-surface-variant font-medium mt-0.5">{{ $wtGranLabel }} · batang bertumpuk = kategori deviasi (peer pressure), berdasarkan tanggal temuan</p> -->
                     <p id="peer-trend-period-caption" class="text-[10px] text-on-surface-variant/80 font-medium mt-1">{{ $wt['period_caption'] ?? '' }}</p>
                  </div>
                  <div class="flex w-full flex-col items-stretch gap-3 sm:items-end lg:max-w-2xl">
                     <div class="flex flex-wrap items-center justify-end gap-x-3 gap-y-2 text-[9px] font-bold uppercase tracking-wider text-on-surface-variant">
                        <div id="peer-trend-legend-cats-inner" class="flex flex-wrap items-center justify-end gap-x-3 gap-y-2">
                        @foreach ($wtDevCats as $dc)
                        <span class="inline-flex items-center gap-1.5" title="{{ $dc['label'] ?? '' }}">
                           <span class="h-2.5 w-2.5 shrink-0 rounded-sm shadow-sm ring-1 ring-black/5" style="background-color: {{ $dc['color'] ?? '#94a3b8' }}"></span>
                           <span class="max-w-[10rem] truncate sm:max-w-none">{{ $dc['label'] ?? '' }}</span>
                        </span>
                        @endforeach
                        </div>
                        <!-- <span class="flex items-center gap-2 border-l border-outline-variant/30 pl-3">
                        <span class="w-4 h-0 border-t-2 border-dashed border-error/60"></span>
                        <span id="peer-trend-avg-label">{{ $wt['avg_legend_label'] ?? 'Rata-rata' }}</span> (<span id="peer-trend-avg">{{ number_format((float) ($wt['avg_count'] ?? 0), 1) }}</span>)
                        </span> -->
                     </div>
                     <!-- <button type="button" id="peer-open-weekly-period" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-4 py-3 text-left shadow-inner transition-colors hover:bg-surface-container-high sm:w-auto sm:min-w-[14rem]">
                        <span class="material-symbols-outlined text-primary text-xl">calendar_month</span>
                        <span class="flex min-w-0 flex-1 flex-col items-start">
                           <span class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Periode chart</span>
                           <span id="peer-weekly-period-label" class="truncate text-sm font-bold text-on-surface">{{ $wt['period_caption'] ?? 'Pilih bulan' }}</span>
                        </span>
                        <span class="material-symbols-outlined text-on-surface-variant">expand_more</span>
                     </button> -->
                  </div>
               </div>
               <div class="peer-chart-scroll w-full overflow-x-auto">
                  <div class="peer-chart-scroll-inner w-max min-w-full px-2">
                     <div class="relative h-80">
                        <div id="peer-chart-target-line-wrap" class="@if(($wt['max_count'] ?? 0) <= 0) hidden @endif pointer-events-none absolute left-0 right-0 z-0 h-px border-t-2 border-dashed border-error opacity-40" style="bottom: {{ min(100, max(0, $wtLinePct)) }}%"></div>
                        <div id="peer-chart-bars" class="peer-chart-bars relative z-10 flex h-full w-full items-stretch gap-1 sm:gap-2">
                           @forelse ($wtWeeks as $w)
                           @php
                              $barH = min(100, max(0, (float) ($w['bar_height_pct'] ?? 0)));
                              $stackP = $w['category_stack_pct'] ?? [];
                              $byC = $w['by_category'] ?? [];
                              $cnt = (int) ($w['count'] ?? 0);
                              $tipParts = [];
                              foreach ($wtDevCats as $dc) {
                                 $k = $dc['key'] ?? '';
                                 $n = (int) ($byC[$k] ?? 0);
                                 if ($n > 0) {
                                    $tipParts[] = ($dc['label'] ?? $k).': '.$n;
                                 }
                              }
                              $tip = ($w['range_short'] ?? '').' — total '.$cnt.' kejadian'.(count($tipParts) ? ' · '.implode(', ', $tipParts) : '');
                           @endphp
                           <div class="peer-chart-bar-col group relative flex h-full min-h-0 min-w-[2.25rem] flex-1 basis-0 flex-col justify-end rounded-t-lg border-x border-t border-outline-variant/10 bg-[#f8fafc]" title="{{ $tip }}">
                              <div class="relative w-full" style="height: {{ $barH }}%">
                                 <span class="absolute -top-6 left-1/2 z-10 -translate-x-1/2 whitespace-nowrap text-[10px] font-semibold text-on-surface">{{ $cnt }}</span>
                                 <div class="absolute inset-0 flex flex-col justify-end overflow-hidden rounded-t-md shadow-inner ring-1 ring-black/10">
                                    @foreach ($wtDevCats as $dc)
                                       @php
                                          $key = $dc['key'] ?? '';
                                          $segH = (float) ($stackP[$key] ?? 0);
                                          $col = $dc['color'] ?? '#94a3b8';
                                       @endphp
                                       @if($segH > 0)
                                       <div class="min-h-[2px] w-full shrink-0 transition-opacity group-hover:opacity-95" style="height: {{ $segH }}%; background-color: {{ $col }}"></div>
                                       @endif
                                    @endforeach
                                 </div>
                              </div>
                           </div>
                           @empty
                           <div class="peer-chart-empty flex w-full min-w-full items-center justify-center py-12 text-sm text-on-surface-variant">Belum ada data untuk chart.</div>
                           @endforelse
                        </div>
                     </div>
                     <div id="peer-chart-axis-labels" class="mt-6 flex w-full gap-1 sm:gap-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                        @foreach ($wtWeeks as $w)
                        <span class="peer-chart-axis-tick min-w-[2rem] flex-1 basis-0 text-center leading-tight">{{ $w['label'] ?? '—' }}</span>
                        @endforeach
                     </div>
                  </div>
               </div>
            </div>
            <!-- Priority Panels: ringkasan evaluasi (aturan + agregasi data) -->
            @php
               $es = $evaluationSummary ?? ['generated_at' => '', 'total_kejadian' => 0, 'rows' => [], 'narrative' => '', 'repeat_period_caption' => 'Seluruh data', 'chart_period_month' => false];
               $esRows = $es['rows'] ?? [];
            @endphp
            <div class="lg:col-span-4 flex flex-col gap-6">
               <div class="rounded-2xl border border-outline-variant/20 bg-white p-6 shadow-sm anchored-card">
                  <div class="mb-4 flex items-start justify-between gap-3">
                     <div class="flex min-w-0 items-center gap-2">
                        <span class="material-symbols-outlined shrink-0 text-2xl text-primary" data-icon="smart_toy">smart_toy</span>
                        <div class="min-w-0">
                           <h4 class="font-headline text-base font-bold text-on-surface">Ringkasan evaluasi data</h4>
                           <!-- <p id="peer-eval-scope-subtitle" class="text-[10px] font-bold uppercase tracking-wider text-primary/90">{{ ($chartPeriodMonth ?? false) ? ('Sesuai periode chart: '.($es['repeat_period_caption'] ?? '')) : 'Sesuai periode chart: seluruh data' }}</p> -->
                           <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Repetitif pelanggaran mengikuti filter yang sama dengan chart</p>
                        </div>
                     </div>
                     <span class="shrink-0 rounded-full bg-[#f1f5f9] px-2 py-1 text-[9px] font-bold uppercase tracking-wide text-on-surface-variant" title="Bukan model AI eksternal">Aturan data</span>
                  </div>
                  <p id="peer-eval-narrative" class="mb-4 text-xs leading-relaxed text-on-surface-variant">{{ $es['narrative'] ?? '' }}</p>
                  <div class="overflow-x-auto rounded-xl border border-outline-variant/20">
                     <table class="w-full min-w-[520px] text-left text-[11px]">
                        <thead>
                           <tr class="border-b border-outline-variant/20 bg-[#f8fafc] text-[9px] font-bold uppercase tracking-wider text-on-surface-variant">
                              <th class="w-[22%] px-2 py-2 sm:px-3">Metrik</th>
                              <th class="px-2 py-2 sm:px-3">Deskripsi</th>
                              <th class="w-[32%] px-2 py-2 text-right sm:px-3">Ambang tindakan</th>
                           </tr>
                        </thead>
                        <tbody id="peer-eval-tbody" class="divide-y divide-outline-variant/15 text-on-surface">
                           @forelse ($esRows as $row)
                           <tr class="bg-white hover:bg-[#fafbfc]">
                              <td class="px-2 py-2.5 align-top font-bold sm:px-3">{{ $row['metric'] ?? '—' }}</td>
                              <td class="max-w-[14rem] px-2 py-2.5 align-top text-on-surface-variant sm:max-w-none sm:px-3">{{ $row['description'] ?? '—' }}</td>
                              <td class="px-2 py-2.5 text-right sm:px-3">
                                 <span class="inline-flex items-center justify-end gap-1.5">
                                    <span class="h-2 w-2 shrink-0 rounded-full @if(($row['status'] ?? '') === 'critical') bg-red-500 @elseif(($row['status'] ?? '') === 'warning') bg-amber-500 @elseif(($row['status'] ?? '') === 'ok') bg-emerald-500 @else bg-slate-400 @endif" aria-hidden="true"></span>
                                    <span class="text-[10px] font-semibold leading-snug">{{ $row['action_threshold'] ?? '—' }}</span>
                                 </span>
                              </td>
                           </tr>
                           @empty
                           <tr>
                              <td colspan="3" class="px-3 py-6 text-center text-on-surface-variant">Belum ada data untuk evaluasi.</td>
                           </tr>
                           @endforelse
                        </tbody>
                     </table>
                  </div>
                  <!-- @php $rvRow = collect($esRows)->firstWhere('key', 'repeat_violator'); @endphp
                  <div id="peer-eval-repeat-block" class="mt-4 @if(!$rvRow || empty($rvRow['violators_detail'])) hidden @endif">
                     <p id="peer-eval-repeat-title" class="mb-2 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Pelanggar repetitif ({{ $es['repeat_period_caption'] ?? 'Seluruh data' }})</p>
                     <div class="max-h-48 overflow-y-auto overflow-x-auto rounded-xl border border-outline-variant/20">
                        <table class="w-full min-w-[480px] text-left text-[10px]">
                           <thead class="sticky top-0 z-[1] border-b border-outline-variant/20 bg-[#f1f5f9] text-[9px] font-bold uppercase tracking-wider text-on-surface-variant">
                              <tr>
                                 <th class="px-2 py-1.5">Nama</th>
                                 <th class="px-2 py-1.5">SID</th>
                                 <th class="px-2 py-1.5">Dept</th>
                                 <th class="px-2 py-1.5 text-right">×</th>
                              </tr>
                           </thead>
                           <tbody id="peer-eval-repeat-tbody" class="divide-y divide-outline-variant/10 text-on-surface">
                              @foreach (($rvRow['violators_detail'] ?? []) as $v)
                              <tr class="peer-rv-toggle cursor-pointer transition-colors hover:bg-[#f8fafc]" role="button" tabindex="0" aria-expanded="false">
                                 <td class="max-w-[7rem] truncate px-2 py-1.5 font-medium" title="{{ $v['nama'] ?? '' }}">{{ $v['nama'] ?? '—' }}</td>
                                 <td class="px-2 py-1.5 font-mono text-[9px] text-on-surface-variant">{{ $v['sid'] ?? '—' }}</td>
                                 <td class="max-w-[6rem] truncate px-2 py-1.5 text-on-surface-variant" title="{{ $v['departemen'] ?? '' }}">{{ $v['departemen'] ?? '—' }}</td>
                                 <td class="whitespace-nowrap px-2 py-1.5 text-right">
                                    <span class="inline-flex max-w-full flex-nowrap items-center justify-end gap-0.5">
                                       <span class="shrink-0 font-bold tabular-nums">{{ (int) ($v['kasus'] ?? 0) }}×</span>
                                       <span class="material-symbols-outlined peer-rv-chevron shrink-0 text-base leading-none text-on-surface-variant" aria-hidden="true">expand_more</span>
                                    </span>
                                 </td>
                              </tr>
                              <tr class="peer-rv-expand hidden bg-[#f8fafc]/90">
                                 <td colspan="4" class="border-t border-outline-variant/10 px-3 py-2">
                                    <p class="mb-1.5 text-[9px] font-bold uppercase tracking-wide text-on-surface-variant">Tanggal &amp; kategori deviasi</p>
                                    <div class="overflow-x-auto rounded-lg border border-outline-variant/15 bg-white">
                                       <table class="w-full min-w-[280px] border-collapse text-left text-[9px] text-on-surface">
                                          <thead>
                                             <tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[8px] font-bold uppercase tracking-wide text-on-surface-variant">
                                                <th class="whitespace-nowrap px-2 py-1.5">Tanggal</th>
                                                <th class="px-2 py-1.5">Kategori deviasi</th>
                                             </tr>
                                          </thead>
                                          <tbody class="divide-y divide-outline-variant/10">
                                             @foreach (($v['kejadian_list'] ?? []) as $kj)
                                             <tr>
                                                <td class="whitespace-nowrap px-2 py-1 font-medium tabular-nums">{{ $kj['tanggal_label'] ?? '—' }}</td>
                                                <td class="px-2 py-1 text-on-surface-variant">{{ $kj['kategori_deviasi'] ?? '—' }}</td>
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
                  </div>
                  @php $recRow = collect($esRows)->firstWhere('key', 'recency'); $recD = $recRow['recency_detail'] ?? null; @endphp
                  <div id="peer-eval-recency-wrap" class="mt-4 @if(!$recD) hidden @endif">
                     <div id="peer-eval-recency-inner">
                        @if($recD)
                        <p class="mb-2 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Jarak waktu pelanggaran berulang — data</p>
                        <p class="mb-3 text-[9px] leading-relaxed text-on-surface-variant">{{ $recD['metric_explanation'] ?? '' }}</p>
                        <div class="overflow-x-auto rounded-xl border border-outline-variant/20 bg-white">
                           <table class="w-full min-w-[260px] text-left text-[10px] text-on-surface">
                              <tbody class="divide-y divide-outline-variant/10">
                                 <tr>
                                    <th class="w-[42%] whitespace-nowrap bg-[#f8fafc] px-2 py-2 align-top text-[9px] font-bold uppercase text-on-surface-variant">Pelanggar</th>
                                    <td class="px-2 py-2 font-medium">{{ $recD['pelanggar_nama'] ?? '—' }}</td>
                                 </tr>
                                 <tr>
                                    <th class="whitespace-nowrap bg-[#f8fafc] px-2 py-2 align-top text-[9px] font-bold uppercase text-on-surface-variant">SID</th>
                                    <td class="px-2 py-2 font-mono text-[9px] text-on-surface-variant">{{ $recD['pelanggar_sid'] ?? '—' }}</td>
                                 </tr>
                                 <tr>
                                    <th class="whitespace-nowrap bg-[#f8fafc] px-2 py-2 align-top text-[9px] font-bold uppercase text-on-surface-variant">Temuan terbaru</th>
                                    <td class="px-2 py-2 align-top">
                                       <div class="flex flex-col gap-0.5">
                                          <span class="font-medium">{{ $recD['latest']['tanggal_label'] ?? '—' }} <span class="font-mono text-[9px] text-on-surface-variant">#{{ (int) ($recD['latest']['kejadian_id'] ?? 0) }}</span></span>
                                          <span class="text-[8px] leading-snug text-on-surface-variant"><span class="font-semibold">Kategori deviasi:</span> {{ $recD['latest']['kategori_deviasi'] ?? '—' }}</span>
                                       </div>
                                    </td>
                                 </tr>
                                 <tr>
                                    <th class="whitespace-nowrap bg-[#f8fafc] px-2 py-2 align-top text-[9px] font-bold uppercase text-on-surface-variant">Temuan sebelumnya</th>
                                    <td class="px-2 py-2 align-top">
                                       <div class="flex flex-col gap-0.5">
                                          <span class="font-medium">{{ $recD['previous']['tanggal_label'] ?? '—' }} <span class="font-mono text-[9px] text-on-surface-variant">#{{ (int) ($recD['previous']['kejadian_id'] ?? 0) }}</span></span>
                                          <span class="text-[8px] leading-snug text-on-surface-variant"><span class="font-semibold">Kategori deviasi:</span> {{ $recD['previous']['kategori_deviasi'] ?? '—' }}</span>
                                       </div>
                                    </td>
                                 </tr>
                                 <tr>
                                    <th class="whitespace-nowrap bg-[#f8fafc] px-2 py-2 align-top text-[9px] font-bold uppercase text-on-surface-variant">Selisih kalender</th>
                                    <td class="px-2 py-2 font-semibold tabular-nums">{{ (int) ($recD['gap_days'] ?? 0) }} hari</td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                        <p class="mt-2 text-[9px] text-on-surface-variant">{{ $recD['footnote'] ?? '' }}</p>
                        @endif
                     </div>
                  </div> -->
                  <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                     <p id="peer-eval-footer-meta" class="text-[10px] text-on-surface-variant">Diperbarui {{ $es['generated_at'] ?? '—' }} · {{ (int) ($es['total_kejadian'] ?? 0) }} kejadian</p>
                     <button type="button" id="peer-open-evaluation-modal" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-sm transition-opacity hover:opacity-95">
                        Lihat detail evaluasi
                        <span class="material-symbols-outlined text-base" data-icon="arrow_forward">arrow_forward</span>
                     </button>
                  </div>
               </div>
            </div>
         </div>
         <!-- Secondary Metrics Grid -->
         @php
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
         </div>
         <!-- Data Table Section -->
         <div class="bg-white rounded-2xl anchored-card overflow-hidden">
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
         </div>
         <!-- Highlight Issue & Rekomendasi (data agregat dashboard + AI Gemini) -->
         <section class="overflow-hidden rounded-2xl border border-outline-variant/15 bg-white anchored-card" aria-labelledby="peer-highlight-heading">
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
         </section>
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
      <!-- Modal statistik kategori deviasi (dari kartu KPI Total Deviasi) -->
      <div id="peer-deviation-category-modal" class="hidden fixed inset-0 z-[206] flex items-center justify-center p-4 sm:p-6 bg-black/40 backdrop-blur-sm" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="peer-deviation-category-title">
         <div class="absolute inset-0 cursor-pointer peer-deviation-category-backdrop" aria-hidden="true"></div>
         <div class="relative z-10 flex max-h-[min(90vh,720px)] w-full max-w-lg flex-col overflow-hidden rounded-2xl border border-outline-variant/20 bg-white text-on-surface shadow-xl">
            <div class="flex shrink-0 items-start justify-between gap-3 border-b border-outline-variant/20 px-5 py-4 sm:px-6">
               <div>
                  <h2 id="peer-deviation-category-title" class="font-headline text-lg font-bold text-on-surface">Statistik kategori deviasi</h2>
                  <p class="mt-1 text-xs text-on-surface-variant">{{ ($chartPeriodMonth ?? false) ? 'Periode sesuai filter chart (tanggal temuan).' : 'Seluruh data kejadian.' }}</p>
               </div>
               <button type="button" id="peer-deviation-category-close" class="rounded-lg p-2 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-on-surface" aria-label="Tutup">
                  <span class="material-symbols-outlined text-2xl" data-icon="close">close</span>
               </button>
            </div>
            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4 sm:px-6">
               <div class="overflow-x-auto rounded-lg border border-outline-variant/20 bg-[#fafbfc]">
                  <table class="w-full min-w-[320px] text-left text-[13px] text-on-surface">
                     <thead>
                        <tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">
                           <th class="px-3 py-2.5">Kategori deviasi</th>
                           <th class="whitespace-nowrap px-3 py-2.5 text-right">Jumlah</th>
                        </tr>
                     </thead>
                     <tbody id="peer-deviation-modal-tbody" class="divide-y divide-outline-variant/10 bg-white">
                        @forelse ($dvPreCats as $drow)
                        <tr class="hover:bg-[#f8fafc]">
                           <td class="px-3 py-2.5">{{ $drow['kategori_deviasi'] ?? '—' }}</td>
                           <td class="px-3 py-2.5 text-right tabular-nums font-semibold">{{ number_format((int) ($drow['jumlah'] ?? 0)) }}</td>
                        </tr>
                        @empty
                        <tr>
                           <td colspan="2" class="px-3 py-6 text-center text-[11px] text-on-surface-variant">Belum ada data kategori deviasi.</td>
                        </tr>
                        @endforelse
                     </tbody>
                     <tfoot>
                        <tr class="border-t-2 border-outline-variant/25 bg-[#f1f5f9] font-headline font-bold">
                           <td class="px-3 py-3 text-on-surface">Total</td>
                           <td id="peer-deviation-modal-total" class="px-3 py-3 text-right tabular-nums text-primary">{{ number_format($dvPreFooterTotal) }}</td>
                        </tr>
                     </tfoot>
                  </table>
               </div>
               <p class="mt-3 text-[10px] leading-relaxed text-on-surface-variant">Total dihitung dari penjumlahan kolom jumlah per kategori (sama dengan total kejadian pada periode yang sama).</p>
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
            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4 sm:px-6">
               <!-- Ringkasan singkat -->
               <div class="rounded-xl border border-secondary/20 bg-secondary/5 p-4 sm:p-5">
                  <p class="text-[10px] font-bold uppercase tracking-wider text-secondary">Ringkasan</p>
                  <div class="mt-2 flex flex-wrap items-end gap-3">
                     <p id="peer-compliance-modal-summary-pct" class="font-headline text-4xl font-extrabold text-on-surface tabular-nums">{{ number_format((float) ($kpi['peer_pressure_compliance_pct'] ?? 0), 1) }}<span class="text-2xl font-bold">%</span></p>
                     <p id="peer-compliance-modal-summary-line" class="text-sm font-medium text-on-surface">
                        <span id="peer-compliance-modal-summary-count">{{ (int) ($kpi['peer_pressure_compliance_comply'] ?? 0) }}</span> dari <span id="peer-compliance-modal-summary-total">{{ (int) ($kpi['peer_pressure_compliance_total'] ?? 0) }}</span> kejadian terlacak memenuhi syarat <span class="font-semibold text-secondary">comply</span>.
                     </p>
                  </div>
                  <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-white/80">
                     <div id="peer-compliance-modal-summary-bar" class="h-full rounded-full bg-secondary transition-all" style="width: {{ max(0, min(100, (float) ($kpi['peer_pressure_compliance_pct'] ?? 0))) }}%"></div>
                  </div>
               </div>
               @php
                  $kpiComply = (int) ($kpi['peer_pressure_compliance_comply'] ?? 0);
                  $kpiTracked = (int) ($kpi['peer_pressure_compliance_total'] ?? 0);
                  $kpiNonComply = max(0, $kpiTracked - $kpiComply);
               @endphp

               <section class="mt-6 rounded-xl border border-outline-variant/20 bg-[#f8fafc] p-4 sm:p-5">
                  <h3 class="font-headline text-sm font-bold text-on-surface">Ringkasan data</h3>
                  <p class="mt-1 text-[11px] leading-snug text-on-surface-variant">Jumlah kejadian pada lima kategori deviasi terlacak untuk periode yang sama dengan kartu KPI.</p>
                  <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                     <div class="rounded-lg border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-center shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-emerald-800">Comply</p>
                        <p id="peer-compliance-brief-comply" class="mt-1 font-headline text-2xl font-extrabold tabular-nums text-emerald-950">{{ number_format($kpiComply) }}</p>
                        <p class="text-[10px] text-emerald-800/90">kejadian</p>
                     </div>
                     <div class="rounded-lg border border-red-200 bg-red-50/90 px-4 py-3 text-center shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-red-800">Tidak comply</p>
                        <p id="peer-compliance-brief-noncomply" class="mt-1 font-headline text-2xl font-extrabold tabular-nums text-red-950">{{ number_format($kpiNonComply) }}</p>
                        <p class="text-[10px] text-red-800/90">kejadian</p>
                     </div>
                     <div class="rounded-lg border border-slate-200 bg-white px-4 py-3 text-center shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wide text-slate-600">Total terlacak</p>
                        <p id="peer-compliance-brief-total" class="mt-1 font-headline text-2xl font-extrabold tabular-nums text-slate-900">{{ number_format($kpiTracked) }}</p>
                        <p class="text-[10px] text-slate-500">pembilang metrik</p>
                     </div>
                  </div>
                  <p id="peer-compliance-brief-narrative" class="mt-4 text-[12px] leading-relaxed text-on-surface">
                     @if($kpiTracked === 0)
                        Belum ada kejadian terlacak pada periode ini (hanya lima kategori deviasi tertentu yang dihitung).
                     @else
                        Dari {{ number_format($kpiTracked) }} kejadian terlacak: <strong class="text-emerald-800">{{ number_format($kpiComply) }} comply</strong> dan <strong class="text-red-800">{{ number_format($kpiNonComply) }} tidak comply</strong>. Persentase di atas = comply ÷ total × 100 ({{ number_format((float) ($kpi['peer_pressure_compliance_pct'] ?? 0), 1) }}%).
                     @endif
                  </p>
               </section>


               <section class="mt-6">
                  <h3 class="font-headline text-sm font-bold text-on-surface">Data per kejadian</h3>
                  <p class="mt-1 text-[11px] leading-snug text-on-surface-variant">
                     Rincian per ID (lima kategori deviasi terlacak), ditampilkan per halaman. Klik ID untuk membuka detail kejadian.
                  </p>
                  <div id="peer-compliance-table-loading" class="mt-3 hidden rounded-lg border border-outline-variant/20 bg-[#f8fafc] px-4 py-8 text-center text-[12px] font-medium text-on-surface-variant" aria-live="polite">
                     <span class="material-symbols-outlined mb-2 inline-block animate-spin text-2xl text-secondary" style="animation-duration:1s">progress_activity</span>
                     <span class="block">Memuat data…</span>
                  </div>
                  <p id="peer-compliance-table-error" class="mt-3 hidden rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-[12px] text-red-800"></p>
                  <div id="peer-compliance-table-wrap" class="mt-3 overflow-x-auto rounded-lg border border-outline-variant/20 bg-[#fafbfc]">
                     <table class="w-full min-w-[880px] text-left text-[11px] text-on-surface">
                        <thead>
                           <tr class="border-b border-outline-variant/20 bg-[#f1f5f9] text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">
                              <th class="whitespace-nowrap px-2 py-2.5">ID</th>
                              <th class="whitespace-nowrap px-2 py-2.5">Tgl temuan</th>
                              <th class="min-w-[140px] px-2 py-2.5">Kategori deviasi (data)</th>
                              <th class="min-w-[120px] px-2 py-2.5">Kelompok</th>
                              <th class="min-w-[100px] px-2 py-2.5">Status pelaksanaan</th>
                              <th class="whitespace-nowrap px-2 py-2.5">id BeRecord</th>
                              <th class="whitespace-nowrap px-2 py-2.5">Hasil</th>
                              <th class="min-w-[200px] px-2 py-2.5">Keterangan</th>
                           </tr>
                        </thead>
                        <tbody id="peer-compliance-modal-tbody" class="divide-y divide-outline-variant/10 bg-white"></tbody>
                     </table>
                  </div>
                  <p id="peer-compliance-table-empty" class="mt-3 hidden rounded-lg border border-dashed border-outline-variant/30 bg-white px-4 py-8 text-center text-[12px] text-on-surface-variant">
                     Tidak ada kejadian pada periode ini yang masuk lima kategori pelacakan.
                  </p>
                  <div id="peer-compliance-pagination" class="mt-3 hidden flex flex-wrap items-center justify-between gap-3 border-t border-outline-variant/15 pt-3"></div>
               </section>

               
               <section class="mt-6" id="peer-compliance-recommendations-section">
                  <h3 class="font-headline text-sm font-bold text-on-surface">Rekomendasi perbaikan</h3>
                  <p class="mt-1 text-[11px] leading-snug text-on-surface-variant">
                     Kejadian <span class="font-semibold text-on-surface">tidak comply</span> dijelaskan per penyebab dalam bentuk <span class="font-semibold text-on-surface">uraian deskriptif</span> (bukan tabel). Klik nomor ID untuk membuka detail kejadian.
                  </p>
                  <div id="peer-compliance-recommendations" class="mt-3 space-y-3"></div>
               </section>
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
        function fillDeviationCategoryModal(dev) {
          if (!dev || typeof dev !== 'object') return;
          var tbody = document.getElementById('peer-deviation-modal-tbody');
          var totalFoot = document.getElementById('peer-deviation-modal-total');
          var kpiBig = document.getElementById('peer-kpi-deviation-total');
          var cats = dev.categories || [];
          var apiTotal = parseInt(String(dev.total != null ? dev.total : 0), 10) || 0;
          if (kpiBig) kpiBig.textContent = apiTotal.toLocaleString('id-ID');
          var sum = 0;
          var rows = cats
            .map(function (row) {
              var name = row.kategori_deviasi != null ? String(row.kategori_deviasi) : '—';
              var j = parseInt(String(row.jumlah != null ? row.jumlah : 0), 10) || 0;
              sum += j;
              return (
                '<tr class="hover:bg-[#f8fafc]"><td class="px-3 py-2.5">' +
                escHtml(name) +
                '</td><td class="px-3 py-2.5 text-right tabular-nums font-semibold">' +
                j.toLocaleString('id-ID') +
                '</td></tr>'
              );
            })
            .join('');
          if (!rows) {
            rows =
              '<tr><td colspan="2" class="px-3 py-6 text-center text-[11px] text-on-surface-variant">Belum ada data kategori deviasi.</td></tr>';
          }
          if (tbody) tbody.innerHTML = rows;
          var footerVal = sum > 0 ? sum : apiTotal;
          if (totalFoot) totalFoot.textContent = footerVal.toLocaleString('id-ID');
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
        var devModal = document.getElementById('peer-deviation-category-modal');
        var devCard = document.getElementById('peer-kpi-deviation-card');
        var devClose = document.getElementById('peer-deviation-category-close');
        var devBackdrop = devModal ? devModal.querySelector('.peer-deviation-category-backdrop') : null;
        function openDeviationModal() {
          if (!devModal) return;
          devModal.classList.remove('hidden');
          devModal.setAttribute('aria-hidden', 'false');
          if (devCard) devCard.setAttribute('aria-expanded', 'true');
        }
        function closeDeviationModal() {
          if (!devModal) return;
          devModal.classList.add('hidden');
          devModal.setAttribute('aria-hidden', 'true');
          if (devCard) devCard.setAttribute('aria-expanded', 'false');
        }
        if (devCard) devCard.addEventListener('click', openDeviationModal);
        if (devClose) devClose.addEventListener('click', closeDeviationModal);
        if (devBackdrop) devBackdrop.addEventListener('click', closeDeviationModal);
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
                    var badgeClass = comply ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800';
                    var badgeText = comply ? 'Comply' : 'Tidak comply';
                    var id = row.id != null ? String(row.id) : '';
                    var tgl = row.tanggal_temuan != null ? String(row.tanggal_temuan) : '—';
                    var kat = row.kategori_deviasi != null ? String(row.kategori_deviasi) : '—';
                    var kel = row.bucket_label != null ? String(row.bucket_label) : '—';
                    var st = row.status_pelaksanaan_edukasi != null ? String(row.status_pelaksanaan_edukasi) : '—';
                    var be = row.id_berecord != null ? String(row.id_berecord) : '—';
                    var als = row.alasan != null ? String(row.alasan) : '—';
                    return (
                      '<tr class="align-top hover:bg-[#f8fafc]">' +
                      '<td class="px-2 py-2 tabular-nums"><button type="button" class="js-peer-compliance-detail-btn font-semibold text-primary hover:underline" data-id="' +
                      escAttr(id) +
                      '">#' +
                      escHtml(id) +
                      '</button></td>' +
                      '<td class="px-2 py-2 whitespace-nowrap tabular-nums">' +
                      escHtml(tgl) +
                      '</td>' +
                      '<td class="px-2 py-2 max-w-[200px]" title="' +
                      escAttr(kat) +
                      '"><span class="line-clamp-2">' +
                      escHtml(kat) +
                      '</span></td>' +
                      '<td class="px-2 py-2 text-[10px] leading-snug">' +
                      escHtml(kel) +
                      '</td>' +
                      '<td class="px-2 py-2 max-w-[140px]" title="' +
                      escAttr(st) +
                      '"><span class="line-clamp-2">' +
                      escHtml(st) +
                      '</span></td>' +
                      '<td class="px-2 py-2 font-mono text-[10px]">' +
                      escHtml(be) +
                      '</td>' +
                      '<td class="px-2 py-2 whitespace-nowrap"><span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold ' +
                      badgeClass +
                      '">' +
                      badgeText +
                      '</span></td>' +
                      '<td class="px-2 py-2 text-[10px] leading-snug text-on-surface-variant">' +
                      escHtml(als) +
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
          complianceModal.classList.remove('hidden');
          complianceModal.setAttribute('aria-hidden', 'false');
          if (complianceCard) complianceCard.setAttribute('aria-expanded', 'true');
          loadComplianceBreakdown(1);
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