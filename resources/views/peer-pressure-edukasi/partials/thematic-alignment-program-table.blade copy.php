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
            <table class="w-full min-w-[1100px] text-left text-sm">
               <thead class="border-b border-outline-variant/20 bg-[#f8fafc] text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">
                  <tr>
                     <th class="whitespace-nowrap px-6 py-5 lg:px-8">Week</th>
                     <th class="min-w-[10rem] px-6 py-5 lg:px-8">Tematik</th>
                     <th class="min-w-[14rem] px-6 py-5 lg:px-8">Recommendation</th>
                     <th class="min-w-[8rem] px-6 py-5 lg:px-8">Program Related</th>
                     <th class="min-w-[8rem] px-6 py-5 lg:px-8">Indikator</th>
                     <th class="min-w-[12rem] px-6 py-5 lg:px-8">Detail Indikator</th>
                     <th class="whitespace-nowrap px-6 py-5 lg:px-8">Scoring All Site YTD</th>
                     <th class="whitespace-nowrap px-6 py-5 lg:px-8">Grade YTD</th>
                  </tr>
               </thead>
               <tbody class="divide-y divide-outline-variant/10">
                  @foreach ($thematicRows as $idx => $row)
                  @php
                     $merge = $thematicRowMergeMeta[$idx] ?? ['show_week_tematik' => true, 'rowspan' => 1];
                     $rs = (int) ($merge['rowspan'] ?? 1);
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

<div id="peer-thematic-drill-modal" class="fixed inset-0 z-[120] hidden" role="dialog" aria-modal="true" aria-labelledby="peer-thematic-drill-title">
   <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px] transition-opacity peer-thematic-drill-backdrop" data-peer-thematic-drill-close></div>
   <div class="relative mx-auto flex min-h-full max-w-4xl items-start justify-center px-3 py-10 sm:px-6">
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
         <div class="max-h-[min(70vh,560px)] overflow-x-auto overflow-y-auto p-4 sm:p-6">
            <table class="w-full min-w-[680px] text-left text-sm">
               <thead id="peer-thematic-drill-thead" class="border-b border-outline-variant/20 bg-[#f8fafc] text-[10px] font-bold uppercase tracking-[0.12em] text-on-surface-variant"></thead>
               <tbody id="peer-thematic-drill-tbody" class="divide-y divide-outline-variant/10"></tbody>
            </table>
            <p id="peer-thematic-drill-empty" class="hidden py-8 text-center text-sm text-on-surface-variant">Belum ada data detail untuk baris ini.</p>
         </div>
      </div>
   </div>
</div>

<script>
(function () {
   var rows = @json($thematicRows);
   var modal = document.getElementById('peer-thematic-drill-modal');
   var thead = document.getElementById('peer-thematic-drill-thead');
   var tbody = document.getElementById('peer-thematic-drill-tbody');
   var emptyEl = document.getElementById('peer-thematic-drill-empty');
   var subEl = document.getElementById('peer-thematic-drill-sub');
   if (!modal || !tbody || !thead) return;

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

   function parsePercentValue(raw) {
      if (raw == null) return null;
      var s = String(raw).trim();
      if (s === '') return null;
      var withPercent = s.match(/(\d+(?:\.\d+)?)\s*%/);
      if (withPercent) return parseFloat(withPercent[1]);
      var numeric = s.match(/^\d+(?:\.\d+)?$/);
      if (numeric) return parseFloat(numeric[0]);
      return null;
   }

   function heatCellClass(raw) {
      var pct = parsePercentValue(raw);
      if (pct == null || isNaN(pct)) {
         return 'bg-slate-50 text-on-surface';
      }
      if (pct >= 80) {
         return 'bg-emerald-500/80 text-white';
      }
      if (pct >= 50) {
         return 'bg-emerald-200 text-emerald-900';
      }
      if (pct > 0) {
         return 'bg-rose-200 text-rose-900';
      }
      return 'bg-rose-400/80 text-white';
   }

   function renderWeekMatrix(drill) {
      var weeks = [];
      drill.forEach(function (d) {
         var n = normalizeWeek(d.week);
         if (n != null && !isNaN(n) && weeks.indexOf(n) === -1) {
            weeks.push(n);
         }
      });
      weeks.sort(function (a, b) { return a - b; });
      var last4Weeks = weeks.slice(-4);

      var bySite = {};
      drill.forEach(function (d) {
         var site = String(d.site || '').trim() || 'Unknown Site';
         var n = normalizeWeek(d.week);
         if (n == null || isNaN(n) || last4Weeks.indexOf(n) === -1) return;
         if (!bySite[site]) bySite[site] = {};
         bySite[site][n] = d.data;
      });

      var sites = Object.keys(bySite).sort(function (a, b) {
         return a.localeCompare(b);
      });

      if (last4Weeks.length === 0 || sites.length === 0) {
         thead.innerHTML = '';
         tbody.innerHTML = '';
         emptyEl.classList.remove('hidden');
         return;
      }

      var theadHtml = '<tr><th class="px-4 py-3 whitespace-nowrap">Site</th>';
      last4Weeks.forEach(function (w) {
         theadHtml += '<th class="px-4 py-3 text-center whitespace-nowrap">' + weekLabel(w) + '</th>';
      });
      theadHtml += '</tr>';
      thead.innerHTML = theadHtml;

      var bodyHtml = '';
      sites.forEach(function (site) {
         bodyHtml += '<tr class="hover:bg-[#f8fafc]">';
         bodyHtml += '<td class="px-4 py-3 align-top text-xs font-semibold text-on-surface">' + esc(site) + '</td>';
         last4Weeks.forEach(function (w) {
            var value = bySite[site][w];
            var display = value != null && value !== '' ? String(value) : '—';
            bodyHtml += '<td class="px-4 py-3 align-top text-center text-xs font-bold tabular-nums ' + heatCellClass(display) + '">' + esc(display) + '</td>';
         });
         bodyHtml += '</tr>';
      });
      tbody.innerHTML = bodyHtml;
      emptyEl.classList.add('hidden');
   }

   function openDrill(index) {
      var row = rows[index];
      if (!row) return;
      var drill = row.drill_down;
      if (!Array.isArray(drill)) drill = [];
      thead.innerHTML = '';
      tbody.innerHTML = '';
      if (drill.length === 0) {
         emptyEl.classList.remove('hidden');
         if (subEl) {
            subEl.textContent =
               (row.tematik || '') +
               (row.detail_indikator ? ' · ' + row.detail_indikator : '');
         }
      } else {
         if (subEl) {
            subEl.textContent =
               (row.tematik || '') +
               (row.week != null ? ' · Week ' + row.week : '') +
               (row.detail_indikator ? ' · ' + row.detail_indikator : '');
         }
         renderWeekMatrix(drill);
      }
      modal.classList.remove('hidden');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('overflow-hidden');
   }

   function closeDrill() {
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
