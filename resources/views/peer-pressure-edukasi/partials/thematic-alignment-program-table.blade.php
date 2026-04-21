@php
    $thematicProgramPath = resource_path('data/peer_pressure_thematic_alignment_program.json');
    $thematicProgram = is_file($thematicProgramPath)
        ? json_decode((string) file_get_contents($thematicProgramPath), true)
        : null;
    $thematicRows = is_array($thematicProgram) && isset($thematicProgram['rows']) && is_array($thematicProgram['rows'])
        ? $thematicProgram['rows']
        : [];
    $thematicTitle = is_array($thematicProgram) ? (string) ($thematicProgram['title'] ?? 'Thematic Alignment Program') : 'Thematic Alignment Program';
    $thematicSubtitle = is_array($thematicProgram) ? (string) ($thematicProgram['subtitle'] ?? '') : '';

    $pointPath = resource_path('data/point.json');
    $pointPayload = is_file($pointPath)
        ? json_decode((string) file_get_contents($pointPath), true)
        : null;
    $pointRows = is_array($pointPayload) && isset($pointPayload['rows']) && is_array($pointPayload['rows'])
        ? $pointPayload['rows']
        : [];

    $peerThematicNormalizeWeek = static function ($week): ?int {
        if ($week === null || $week === '') {
            return null;
        }
        if (is_int($week)) {
            return $week;
        }
        if (is_float($week)) {
            return (int) $week;
        }
        $s = trim((string) $week);
        if ($s !== '' && ctype_digit($s)) {
            return (int) $s;
        }
        if (preg_match('/W\s*(\d+)/i', $s, $m)) {
            return (int) $m[1];
        }

        return null;
    };

    foreach ($thematicRows as $idx => $tRow) {
        $detailKey = trim((string) ($tRow['detail_indikator'] ?? ''));
        $drill = [];
        foreach ($pointRows as $p) {
            if (trim((string) ($p['detail_indikator'] ?? '')) !== $detailKey || $detailKey === '') {
                continue;
            }
            $drill[] = [
                'detail_indikator' => (string) ($p['detail_indikator'] ?? ''),
                'site' => (string) ($p['site'] ?? ''),
                'week' => (string) ($p['week'] ?? ''),
                'data' => (string) ($p['data'] ?? ''),
            ];
        }
        usort($drill, static function (array $a, array $b) use ($peerThematicNormalizeWeek): int {
            $cmp = strcmp($a['site'], $b['site']);
            if ($cmp !== 0) {
                return $cmp;
            }
            $wa = $peerThematicNormalizeWeek($a['week'] ?? null);
            $wb = $peerThematicNormalizeWeek($b['week'] ?? null);
            $wa = $wa ?? -1;
            $wb = $wb ?? -1;
            if ($wa !== $wb) {
                return $wa <=> $wb;
            }

            return strcmp((string) ($a['week'] ?? ''), (string) ($b['week'] ?? ''));
        });
        $thematicRows[$idx]['drill_down'] = $drill;
    }

    $peerThematicParsePercent = static function ($raw): ?float {
        if ($raw === null || $raw === '') {
            return null;
        }
        $s = trim((string) $raw);
        if ($s === '') {
            return null;
        }
        if (preg_match('/(\d+(?:\.\d+)?)\s*%/', $s, $m)) {
            return (float) $m[1];
        }
        if (preg_match('/^\d+(?:\.\d+)?$/', $s)) {
            return (float) $s;
        }

        return null;
    };

    /** Warna sel heatmap / batang: sama dengan logika modal (50% / 80%). */
    $peerThematicBarFillForValue = static function (float $v, bool $lowerIsBetter): string {
        if ($lowerIsBetter) {
            if ($v > 80.0) {
                return '#d84f4b';
            }
            if ($v >= 50.0) {
                return '#e4cc4a';
            }

            return '#4e9f63';
        }
        if ($v > 80.0) {
            return '#4e9f63';
        }
        if ($v >= 50.0) {
            return '#e4cc4a';
        }

        return '#d84f4b';
    };

    /**
     * Grafik batang trend all site: rata-rata nilai semua site per minggu (urut W), dari drill_down.
     * Warna batang mengikuti kategori indikator (peer_pressure_thematic_alignment_program.json).
     */
    $peerThematicBuildAllSiteBarChart = static function (array $drill, string $kategori) use ($peerThematicNormalizeWeek, $peerThematicParsePercent, $peerThematicBarFillForValue): array {
        $lowerIsBetter = stripos($kategori, 'kecil') !== false && stripos($kategori, 'tinggi') === false;
        $byWeek = [];
        foreach ($drill as $d) {
            $w = $peerThematicNormalizeWeek($d['week'] ?? null);
            if ($w === null) {
                continue;
            }
            $v = $peerThematicParsePercent($d['data'] ?? '');
            if ($v === null) {
                continue;
            }
            if (! isset($byWeek[$w])) {
                $byWeek[$w] = [];
            }
            $byWeek[$w][] = $v;
        }
        ksort($byWeek, SORT_NUMERIC);
        $weeks = array_keys($byWeek);
        $avgs = [];
        foreach ($weeks as $wk) {
            $avgs[] = array_sum($byWeek[$wk]) / count($byWeek[$wk]);
        }
        $n = count($avgs);
        if ($n < 1) {
            return [
                'svg' => '<span class="inline-block min-h-[2.25rem] text-[11px] leading-9 text-on-surface-variant/80">—</span>',
                'title' => 'Belum ada data drill-down untuk grafik all site.',
            ];
        }

        $svgW = 140;
        $svgH = 42;
        $padL = 4.0;
        $padR = 4.0;
        $padT = 4.0;
        $padB = 12.0;
        $chartW = $svgW - $padL - $padR;
        $chartH = $svgH - $padT - $padB;

        $min = min($avgs);
        $max = max($avgs);
        if (abs($max - $min) < 1e-9) {
            $min -= 0.5;
            $max += 0.5;
        }
        $span = $max - $min;

        $gap = $n > 8 ? 1.0 : ($n > 5 ? 2.0 : 2.5);
        $barW = max(3.0, ($chartW - ($n - 1) * $gap) / $n);
        $labelFs = $n > 10 ? 5.5 : ($n > 7 ? 6.5 : 7);

        $parts = [];
        foreach ($weeks as $i => $wk) {
            $parts[] = 'W'.$wk.': '.number_format($avgs[$i], 2, '.', '').'%';
        }
        $title = 'All site (rata-rata per minggu): '.implode(' · ', $parts).'. '.$kategori.'.';
        $escTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$svgW.'" height="'.$svgH.'" viewBox="0 0 '.$svgW.' '.$svgH.'" class="max-h-[2.75rem] w-full max-w-[9rem]" role="img" aria-label="'.$escTitle.'">';
        $svg .= '<line x1="'.$padL.'" y1="'.($padT + $chartH).'" x2="'.($svgW - $padR).'" y2="'.($padT + $chartH).'" stroke="#cbd5e1" stroke-width="1"/>';

        for ($i = 0; $i < $n; $i++) {
            $x = $padL + $i * ($barW + $gap);
            $h = (($avgs[$i] - $min) / $span) * $chartH;
            $h = max(1.0, $h);
            $y = $padT + $chartH - $h;
            $fill = $peerThematicBarFillForValue($avgs[$i], $lowerIsBetter);
            $svg .= '<rect x="'.round($x, 2).'" y="'.round($y, 2).'" width="'.round($barW, 2).'" height="'.round($h, 2).'" rx="1.5" fill="'.$fill.'" fill-opacity="0.92"/>';
            $label = 'W'.$weeks[$i];
            $lx = $x + $barW / 2;
            if ($barW >= 10 || $n <= 8) {
                $svg .= '<text x="'.round($lx, 2).'" y="'.($svgH - 2).'" text-anchor="middle" font-size="'.$labelFs.'" font-weight="600" fill="#64748b">'.$label.'</text>';
            }
        }
        $svg .= '</svg>';

        return [
            'svg' => $svg,
            'title' => $title,
        ];
    };

    foreach ($thematicRows as $idx => $tRow) {
        $drill = $thematicRows[$idx]['drill_down'] ?? [];
        $katRow = trim((string) ($tRow['kategori'] ?? ''));
        if ($katRow === '') {
            $katRow = 'Semakin tinggi semakin bagus';
        }
        $thematicRows[$idx]['trend_all_site_chart'] = $peerThematicBuildAllSiteBarChart(
            is_array($drill) ? $drill : [],
            $katRow
        );
    }

    $allSitesFromPoint = [];
    foreach ($pointRows as $p) {
        $s = trim((string) ($p['site'] ?? ''));
        if ($s !== '') {
            $allSitesFromPoint[$s] = true;
        }
    }
    $allSitesFromPoint = array_keys($allSitesFromPoint);
    sort($allSitesFromPoint);

    $thematicRowsForJs = [];
    foreach ($thematicRows as $r) {
        $kat = trim((string) ($r['kategori'] ?? ''));
        if ($kat === '') {
            $kat = 'Semakin tinggi semakin bagus';
        }
        $thematicRowsForJs[] = [
            'tematik' => $r['tematik'] ?? null,
            'week' => $r['week'] ?? null,
            'detail_indikator' => $r['detail_indikator'] ?? '',
            'kategori' => $kat,
            'drill_down' => $r['drill_down'] ?? [],
        ];
    }

    /** Baris berturut dengan week + tematik sama: kolom Week & Tematik digabung (rowspan). */
    $thematicRowMergeMeta = [];
    $nThematic = count($thematicRows);
    for ($i = 0; $i < $nThematic; $i++) {
        $w = trim((string) ($thematicRows[$i]['week'] ?? ''));
        $tm = trim((string) ($thematicRows[$i]['tematik'] ?? ''));
        if ($i > 0) {
            $pw = trim((string) ($thematicRows[$i - 1]['week'] ?? ''));
            $ptm = trim((string) ($thematicRows[$i - 1]['tematik'] ?? ''));
            if ($w === $pw && $tm === $ptm) {
                $thematicRowMergeMeta[$i] = [
                    'show_week_tematik' => false,
                ];

                continue;
            }
        }
        $span = 1;
        for ($j = $i + 1; $j < $nThematic; $j++) {
            $w2 = trim((string) ($thematicRows[$j]['week'] ?? ''));
            $tm2 = trim((string) ($thematicRows[$j]['tematik'] ?? ''));
            if ($w2 === $w && $tm2 === $tm) {
                $span++;
            } else {
                break;
            }
        }
        $thematicRowMergeMeta[$i] = [
            'show_week_tematik' => true,
            'rowspan' => $span,
        ];
    }
@endphp
<div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
   <div class="min-w-0 lg:col-span-12">
      <div class="overflow-hidden rounded-2xl border border-outline-variant/30 bg-white anchored-card">
         <div class="flex flex-col gap-4 border-b border-outline-variant/20 p-6 lg:flex-row lg:items-start lg:justify-between">
            <div>
               <h3 class="font-headline text-xl font-bold text-on-surface">{{ $thematicTitle }}</h3>
               @if($thematicSubtitle !== '')
               <p class="mt-0.5 text-xs font-medium text-on-surface-variant">{{ $thematicSubtitle }}</p>
               @endif
               <p class="mt-1 text-[11px] text-on-surface-variant/80">Klik baris untuk melihat detail per site &amp; minggu (jika tersedia).</p>
            </div>
         </div>
         <div class="overflow-x-auto">
            <table class="w-full min-w-[1220px] text-left text-sm">
               <thead class="border-b border-outline-variant/20 bg-[#f8fafc] text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">
                  <tr>
                     <th class="whitespace-nowrap px-6 py-5 lg:px-8">Week</th>
                     <th class="min-w-[10rem] px-6 py-5 lg:px-8">Tematik</th>
                     <th class="min-w-[14rem] px-6 py-5 lg:px-8">Recommendation</th>
                     <th class="min-w-[8rem] px-6 py-5 lg:px-8">Program Related</th>
                     <th class="min-w-[8rem] px-6 py-5 lg:px-8">Indikator</th>
                     <th class="min-w-[12rem] px-6 py-5 lg:px-8">Detail Indikator</th>
                     <th class="whitespace-nowrap px-6 py-5 lg:px-8">Scoring All Site YTD</th>
                     <th class="min-w-[9rem] px-6 py-5 lg:px-8" title="Grafik batang: rata-rata semua site per minggu (dari data drill-down)">Trend (All Site)</th>
                     <th class="whitespace-nowrap px-6 py-5 lg:px-8">Grade YTD</th>
                  </tr>
               </thead>
               <tbody class="divide-y divide-outline-variant/10">
                  @foreach ($thematicRows as $idx => $row)
                  @php
                     $merge = $thematicRowMergeMeta[$idx] ?? ['show_week_tematik' => true, 'rowspan' => 1];
                     $rs = (int) ($merge['rowspan'] ?? 1);
                     $trendChart = $row['trend_all_site_chart'] ?? ['svg' => '<span class="text-[11px] text-on-surface-variant/80">—</span>', 'title' => ''];
                  @endphp
                  <tr
                     class="js-thematic-alignment-row cursor-pointer transition-colors hover:bg-[#f8fafc] focus-within:bg-[#f8fafc]"
                     data-thematic-index="{{ $idx }}"
                     role="button"
                     tabindex="0"
                  >
                     @if (! empty($merge['show_week_tematik']))
                     <td class="whitespace-nowrap px-6 py-4 align-middle text-center font-bold text-on-surface lg:px-8" @if ($rs > 1) rowspan="{{ $rs }}" @endif>{{ ($row['week'] ?? '') !== '' ? $row['week'] : '—' }}</td>
                     <td class="px-6 py-4 align-middle text-center text-xs font-semibold leading-snug text-on-surface lg:px-8" @if ($rs > 1) rowspan="{{ $rs }}" @endif>{{ ($row['tematik'] ?? '') !== '' ? $row['tematik'] : '—' }}</td>
                     @endif
                     <td class="px-6 py-4 align-top text-xs leading-relaxed text-on-surface lg:px-8">{{ $row['recommendation'] ?? '—' }}</td>
                     <td class="px-6 py-4 align-top text-xs text-on-surface-variant lg:px-8">{{ $row['program_related'] ?? '—' }}</td>
                     <td class="px-6 py-4 align-top text-xs font-medium text-on-surface lg:px-8">{{ $row['indikator'] ?? '—' }}</td>
                     <td class="px-6 py-4 align-top text-xs leading-relaxed text-on-surface lg:px-8">{{ $row['detail_indikator'] ?? '—' }}</td>
                     <td class="whitespace-nowrap px-6 py-4 align-top text-xs font-bold tabular-nums text-on-surface lg:px-8">{{ ($row['scoring_all_site_ytd'] ?? '') !== '' ? $row['scoring_all_site_ytd'] : '—' }}</td>
                     <td class="px-4 py-3 align-middle lg:px-6" title="{{ e($trendChart['title'] ?? '') }}">
                        <div class="pointer-events-none flex items-center justify-start text-primary">
                           {!! $trendChart['svg'] ?? '—' !!}
                        </div>
                     </td>
                     <td class="whitespace-nowrap px-6 py-4 align-top text-xs font-bold text-on-surface lg:px-8">{{ ($row['grade_ytd'] ?? '') !== '' ? $row['grade_ytd'] : '—' }}</td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
         <div class="flex items-center justify-between border-t border-outline-variant/20 bg-[#f8fafc] px-6 py-4 lg:px-8">
            <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">{{ count($thematicRows) }} baris program</p>
         </div>
      </div>
   </div>
</div>

@once
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" crossorigin="anonymous"></script>
@endonce

<div id="peer-thematic-drill-modal" class="fixed inset-0 z-[120] hidden" role="dialog" aria-modal="true" aria-labelledby="peer-thematic-drill-title">
   <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px] transition-opacity peer-thematic-drill-backdrop" data-peer-thematic-drill-close></div>
   <div class="relative mx-auto flex min-h-full max-w-6xl items-start justify-center px-3 py-8 sm:px-6 sm:py-10">
      <div class="w-full overflow-hidden rounded-2xl border border-outline-variant/30 bg-white shadow-2xl">
         <div class="flex items-start justify-between gap-3 border-b border-outline-variant/20 bg-gradient-to-r from-slate-50 to-white px-5 py-4 sm:px-6">
            <div class="min-w-0">
               <h3 id="peer-thematic-drill-title" class="font-headline text-lg font-bold text-on-surface">Detail indikator per site</h3>
               <p id="peer-thematic-drill-sub" class="mt-0.5 text-[11px] text-on-surface-variant"></p>
            </div>
            <button type="button" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/30" data-peer-thematic-drill-close aria-label="Tutup">
               <span class="material-symbols-outlined text-[22px]">close</span>
            </button>
         </div>
         <div class="max-h-[min(88vh,940px)] overflow-y-auto p-4 sm:p-6">
            <p id="peer-thematic-drill-empty" class="hidden py-10 text-center text-sm text-slate-500">Belum ada data detail untuk baris ini.</p>
            <div id="peer-thematic-drill-panel" class="hidden">
               <div class="mb-5 grid gap-4 ">
                  <article class="rounded-2xl border border-slate-200 p-4 shadow-sm">
                     <div class="flex flex-col gap-3 border-b border-slate-200 pb-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                           <h4 class="text-sm font-semibold text-slate-800">Heatmap Site vs Last 4 Weeks</h4>
                           <p class="mt-1 text-[11px] text-slate-600">Menampilkan seluruh site untuk indikator terpilih.</p>
                        </div>
                        <div id="peer-thematic-drill-legend" class="flex flex-wrap items-center gap-2 text-[10px] text-slate-700"></div>
                     </div>
                     <div class="mt-3 overflow-x-auto rounded-lg border border-slate-300">
                        <div id="peer-thematic-drill-heatmap-root"></div>
                     </div>
                  </article>
                  <article class="flex h-full min-h-0 flex-col rounded-2xl border border-slate-200 p-5 shadow-sm">
                     <div>
                        <h4 class="text-sm font-semibold text-slate-700">Tren Pemenuhan 4 Minggu Terakhir</h4>
                        <p id="peer-thematic-drill-chart-sub" class="mt-1 text-[11px] text-slate-500"></p>
                     </div>
                     <div id="peer-thematic-drill-chart-wrap" class="mt-4 min-h-[420px] flex-1">
                        <canvas id="peer-thematic-drill-trend-canvas"></canvas>
                     </div>
                  </article>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
(function () {
   var rows = @json($thematicRowsForJs);
   var allSitesMaster = @json($allSitesFromPoint);
   var modal = document.getElementById('peer-thematic-drill-modal');
   var emptyEl = document.getElementById('peer-thematic-drill-empty');
   var panelEl = document.getElementById('peer-thematic-drill-panel');
   var subEl = document.getElementById('peer-thematic-drill-sub');
   var legendEl = document.getElementById('peer-thematic-drill-legend');
   var heatmapRoot = document.getElementById('peer-thematic-drill-heatmap-root');
   var chartSubEl = document.getElementById('peer-thematic-drill-chart-sub');
   var chartWrap = document.getElementById('peer-thematic-drill-chart-wrap');
   var trendCanvas = document.getElementById('peer-thematic-drill-trend-canvas');
   var trendChartInstance = null;

   if (!modal || !emptyEl || !panelEl || !heatmapRoot) return;

   function esc(s) {
      if (s == null) return '';
      return String(s)
         .replace(/&/g, '&amp;')
         .replace(/</g, '&lt;')
         .replace(/>/g, '&gt;')
         .replace(/"/g, '&quot;');
   }

   function normalizeWeek(week) {
      if (week == null) return null;
      var raw = String(week).trim();
      if (raw === '') return null;
      var digit = raw.match(/(\d+)/);
      if (!digit) return null;
      return parseInt(digit[1], 10);
   }

   function weekLabel(week) {
      var n = normalizeWeek(week);
      if (n == null || isNaN(n)) return String(week || '—');
      return 'W' + n;
   }

   function parseNumber(raw) {
      if (raw == null) return null;
      var s = String(raw).trim();
      if (s === '') return null;
      var withPercent = s.match(/(\d+(?:\.\d+)?)\s*%/);
      if (withPercent) return parseFloat(withPercent[1]);
      var numeric = s.match(/^\d+(?:\.\d+)?$/);
      if (numeric) return parseFloat(numeric[0]);
      return null;
   }

   function normalizeText(v) {
      return String(v ?? '').trim().toLowerCase();
   }

   function isLowerBetterFromKategori(kategori) {
      var t = normalizeText(kategori);
      return t.indexOf('kecil') !== -1 && t.indexOf('tinggi') === -1;
   }

   function heatClass(value, lowerBetter) {
      var v = parseNumber(value);
      if (v === null || isNaN(v)) {
         return 'bg-[#cfd8dc] text-slate-600 border border-white/70';
      }
      if (lowerBetter) {
         if (v > 80) return 'bg-[#d84f4b] text-white border border-white/70';
         if (v >= 50) return 'bg-[#e4cc4a] text-slate-800 border border-white/70';
         return 'bg-[#4e9f63] text-white border border-white/70';
      }
      if (v > 80) return 'bg-[#4e9f63] text-white border border-white/70';
      if (v >= 50) return 'bg-[#e4cc4a] text-slate-800 border border-white/70';
      return 'bg-[#d84f4b] text-white border border-white/70';
   }

   function renderLegend(lowerBetter) {
      if (!legendEl) return;
      var good = lowerBetter ? '&lt; 50%' : '&gt; 80%';
      var bad = lowerBetter ? '&gt; 80%' : '&lt; 50%';
      legendEl.innerHTML =
         '<span class="inline-flex items-center gap-1.5 rounded-md bg-white/80 px-2 py-1"><span class="h-2.5 w-2.5 bg-[#4e9f63]"></span><span>' +
         good +
         '</span></span>' +
         '<span class="inline-flex items-center gap-1.5 rounded-md bg-white/80 px-2 py-1"><span class="h-2.5 w-2.5 bg-[#e4cc4a]"></span>50-79%</span>' +
         '<span class="inline-flex items-center gap-1.5 rounded-md bg-white/80 px-2 py-1"><span class="h-2.5 w-2.5 bg-[#d84f4b]"></span><span>' +
         bad +
         '</span></span>';
   }

   function collectWeeksAndBySite(drill) {
      var weeks = [];
      drill.forEach(function (d) {
         var n = normalizeWeek(d.week);
         if (n != null && !isNaN(n) && weeks.indexOf(n) === -1) weeks.push(n);
      });
      weeks.sort(function (a, b) {
         return a - b;
      });
      var last4Weeks = weeks.slice(-4);
      var bySite = {};
      drill.forEach(function (d) {
         var site = String(d.site || '').trim() || 'Unknown Site';
         var n = normalizeWeek(d.week);
         if (n == null || isNaN(n) || last4Weeks.indexOf(n) === -1) return;
         if (!bySite[site]) bySite[site] = {};
         bySite[site][n] = d.data;
      });
      return { last4Weeks: last4Weeks, bySite: bySite };
   }

   function mergeSiteList(bySite) {
      var set = {};
      (allSitesMaster || []).forEach(function (s) {
         if (s) set[s] = true;
      });
      Object.keys(bySite || {}).forEach(function (s) {
         if (s) set[s] = true;
      });
      return Object.keys(set).sort(function (a, b) {
         return a.localeCompare(b);
      });
   }

   function renderHeatmap(drill, lowerBetter) {
      var pack = collectWeeksAndBySite(drill);
      var last4Weeks = pack.last4Weeks;
      var bySite = pack.bySite;
      var sites = mergeSiteList(bySite);

      if (!last4Weeks.length || !sites.length) {
         heatmapRoot.innerHTML =
            '<table class="w-full min-w-[620px] border-separate border-spacing-0 text-sm"><tbody><tr><td class="px-3 py-6 text-center text-xs text-slate-500" colspan="' +
            (last4Weeks.length + 1) +
            '">Belum ada data heatmap untuk indikator ini.</td></tr></tbody></table>';
         return pack;
      }

      var theadHtml =
         '<table class="w-full min-w-[620px] border-separate border-spacing-0 text-sm">' +
         '<thead class="text-[13px] font-semibold text-slate-700"><tr>' +
         '<th class="sticky left-0 z-10 bg-[#f8fafc] px-3 py-2 text-left shadow-[2px_0_0_0_rgba(226,232,240,0.9)]"></th>';
      last4Weeks.forEach(function (w) {
         theadHtml += '<th class="px-4 py-2 text-center">' + esc(weekLabel(w)) + '</th>';
      });
      theadHtml += '</tr></thead><tbody>';

      var bodyHtml = '';
      sites.forEach(function (site) {
         bodyHtml += '<tr>';
         bodyHtml +=
            '<td class="sticky left-0 z-[1] whitespace-nowrap border-b border-slate-100 bg-white px-3 py-2 text-[14px] font-semibold text-slate-700 shadow-[2px_0_0_0_rgba(226,232,240,0.85)]">' +
            esc(site) +
            '</td>';
         last4Weeks.forEach(function (w) {
            var raw = bySite[site] ? bySite[site][w] : null;
            var display = raw != null && raw !== '' ? String(raw) : '-';
            bodyHtml +=
               '<td class="h-10 min-w-[5rem] border-b border-slate-100 px-3 py-2 text-center text-[11px] font-semibold tabular-nums ' +
               heatClass(raw, lowerBetter) +
               '">' +
               esc(display) +
               '</td>';
         });
         bodyHtml += '</tr>';
      });
      theadHtml += bodyHtml + '</tbody></table>';
      heatmapRoot.innerHTML = theadHtml;
      return pack;
   }

   function lineColor(index, total) {
      var hue = Math.round((index * (360 / Math.max(total, 1))) % 360);
      return 'hsl(' + hue + ' 70% 45%)';
   }

   function computeYMax(datasets) {
      var maxV = 0;
      var has = false;
      datasets.forEach(function (ds) {
         (ds.data || []).forEach(function (v) {
            if (v != null && !isNaN(v)) {
               has = true;
               maxV = Math.max(maxV, v);
            }
         });
      });
      if (!has) return 100;
      if (maxV > 100) return Math.ceil(maxV * 1.1);
      if (maxV <= 15) return Math.max(5, Math.ceil(maxV * 1.25));
      return 100;
   }

   function destroyTrendChart() {
      if (trendChartInstance) {
         try {
            trendChartInstance.destroy();
         } catch (e) {
            /* ignore */
         }
         trendChartInstance = null;
      }
   }

   function buildTrendChart(drill, last4Weeks, indicatorLabel, siteCount) {
      destroyTrendChart();
      if (!trendCanvas || !window.Chart) return;
      if (!last4Weeks || !last4Weeks.length) return;

      var chartWeeks = last4Weeks.slice();
      var drillRows = drill.filter(function (d) {
         var week = normalizeWeek(d.week);
         return week != null && chartWeeks.indexOf(week) !== -1;
      });
      var siteAgg = {};
      drillRows.forEach(function (d) {
         var site = d.site || 'Unknown';
         var week = normalizeWeek(d.week);
         var value = parseNumber(d.data);
         if (week === null || value === null) return;
         if (!siteAgg[site]) siteAgg[site] = {};
         siteAgg[site][week] = value;
      });

      var allSites = Object.keys(siteAgg)
         .map(function (site) {
            var vals = chartWeeks.map(function (w) {
               return siteAgg[site][w];
            }).filter(function (v) {
               return typeof v === 'number';
            });
            var avg = vals.length ? vals.reduce(function (a, b) {
               return a + b;
            }, 0) / vals.length : 0;
            return { site: site, avg: avg };
         })
         .sort(function (a, b) {
            return b.avg - a.avg;
         });

      var datasets = allSites.map(function (item, index) {
         return {
            label: item.site,
            data: chartWeeks.map(function (week) {
               return siteAgg[item.site][week] != null ? siteAgg[item.site][week] : null;
            }),
            borderColor: lineColor(index, allSites.length),
            backgroundColor: lineColor(index, allSites.length),
            tension: 0.35,
            borderWidth: 2.2,
            pointRadius: 2,
            pointHoverRadius: 4,
            pointHitRadius: 10,
            spanGaps: true,
         };
      });

      var labels = chartWeeks.map(function (w) {
         return 'W' + w;
      });
      var safeDatasets = datasets.length
         ? datasets
         : [
              {
                 label: (indicatorLabel || 'No Data') + ' (no data)',
                 data: labels.map(function () {
                    return null;
                 }),
                 borderColor: '#94A3B8',
                 backgroundColor: '#94A3B8',
                 tension: 0.4,
                 borderWidth: 2,
                 pointRadius: 3,
              },
           ];

      var yMax = computeYMax(safeDatasets);
      var ctx = trendCanvas.getContext('2d');
      if (!ctx) return;

      try {
         trendChartInstance = new Chart(ctx, {
            type: 'line',
            data: { labels: labels, datasets: safeDatasets },
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
                  title: { display: true, text: indicatorLabel || '' },
               },
               scales: {
                  x: {
                     grid: { display: false, drawBorder: false },
                     border: { display: false },
                  },
                  y: {
                     min: 0,
                     max: yMax,
                     ticks: {
                        callback: function (v) {
                           return yMax <= 20 ? String(v) : v + '%';
                        },
                     },
                     grid: { display: false, drawBorder: false },
                     border: { display: false },
                  },
               },
            },
         });
      } catch (err) {
         /* ignore */
      }

      if (chartWrap && typeof siteCount === 'number') {
         chartWrap.style.height = Math.max(420, siteCount * 40 + 110) + 'px';
      }
   }

   function openDrill(index) {
      var row = rows[index];
      if (!row) return;
      var drill = row.drill_down;
      if (!Array.isArray(drill)) drill = [];
      var kategori = row.kategori || 'Semakin tinggi semakin bagus';
      var lowerBetter = isLowerBetterFromKategori(kategori);

      destroyTrendChart();
      heatmapRoot.innerHTML = '';

      if (drill.length === 0) {
         emptyEl.classList.remove('hidden');
         panelEl.classList.add('hidden');
         if (subEl) {
            subEl.textContent =
               (row.tematik || '') + (row.detail_indikator ? ' · ' + row.detail_indikator : '');
         }
      } else {
         emptyEl.classList.add('hidden');
         panelEl.classList.remove('hidden');
         if (subEl) {
            subEl.textContent =
               (row.tematik || '') +
               (row.week != null ? ' · Week ' + row.week : '') +
               (row.detail_indikator ? ' · ' + row.detail_indikator : '');
         }
         renderLegend(lowerBetter);
         var pack = renderHeatmap(drill, lowerBetter);
         var sites = mergeSiteList(pack.bySite);
         if (chartSubEl) {
            chartSubEl.textContent = 'Indikator: ' + (row.detail_indikator || '—');
         }
         var indLabel = row.detail_indikator || '';

         modal.classList.remove('hidden');
         modal.setAttribute('aria-hidden', 'false');
         document.body.classList.add('overflow-hidden');

         requestAnimationFrame(function () {
            requestAnimationFrame(function () {
               buildTrendChart(drill, pack.last4Weeks, indLabel, sites.length);
            });
         });
         return;
      }

      modal.classList.remove('hidden');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('overflow-hidden');
   }

   function closeDrill() {
      destroyTrendChart();
      modal.classList.add('hidden');
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('overflow-hidden');
   }

   document.querySelectorAll('.js-thematic-alignment-row').forEach(function (tr) {
      tr.addEventListener('click', function () {
         var i = parseInt(tr.getAttribute('data-thematic-index'), 10);
         if (!isNaN(i)) openDrill(i);
      });
      tr.addEventListener('keydown', function (e) {
         if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            var i = parseInt(tr.getAttribute('data-thematic-index'), 10);
            if (!isNaN(i)) openDrill(i);
         }
      });
   });

   modal.querySelectorAll('[data-peer-thematic-drill-close]').forEach(function (el) {
      el.addEventListener('click', closeDrill);
   });
   document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeDrill();
   });
})();
</script>
