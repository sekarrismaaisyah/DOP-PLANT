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
        $tw = $peerThematicNormalizeWeek($tRow['week'] ?? null);
        $drill = [];
        foreach ($pointRows as $p) {
            if (trim((string) ($p['detail_indikator'] ?? '')) !== $detailKey || $detailKey === '') {
                continue;
            }
            $pw = $peerThematicNormalizeWeek($p['week'] ?? null);
            if ($tw === null || $pw === null || $tw !== $pw) {
                continue;
            }
            $drill[] = [
                'detail_indikator' => (string) ($p['detail_indikator'] ?? ''),
                'site' => (string) ($p['site'] ?? ''),
                'week' => (string) ($p['week'] ?? ''),
                'data' => (string) ($p['data'] ?? ''),
            ];
        }
        usort($drill, static function (array $a, array $b): int {
            $cmp = strcmp($a['site'], $b['site']);
            if ($cmp !== 0) {
                return $cmp;
            }

            return strcmp($a['week'], $b['week']);
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
            <table class="w-full min-w-[520px] text-left text-sm">
               <thead class="border-b border-outline-variant/20 bg-[#f8fafc] text-[10px] font-bold uppercase tracking-[0.12em] text-on-surface-variant">
                  <tr>
                     <th class="px-4 py-3">Detail Indikator</th>
                     <th class="px-4 py-3">Site</th>
                     <th class="px-4 py-3">Week</th>
                     <th class="px-4 py-3">Data</th>
                  </tr>
               </thead>
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
   var tbody = document.getElementById('peer-thematic-drill-tbody');
   var emptyEl = document.getElementById('peer-thematic-drill-empty');
   var subEl = document.getElementById('peer-thematic-drill-sub');
   if (!modal || !tbody) return;

   function esc(s) {
      if (s == null) return '';
      return String(s)
         .replace(/&/g, '&amp;')
         .replace(/</g, '&lt;')
         .replace(/>/g, '&gt;')
         .replace(/"/g, '&quot;');
   }

   function openDrill(index) {
      var row = rows[index];
      if (!row) return;
      var drill = row.drill_down;
      if (!Array.isArray(drill)) drill = [];
      tbody.innerHTML = '';
      if (drill.length === 0) {
         emptyEl.classList.remove('hidden');
         if (subEl) {
            subEl.textContent =
               (row.tematik || '') +
               (row.detail_indikator ? ' · ' + row.detail_indikator : '');
         }
      } else {
         emptyEl.classList.add('hidden');
         if (subEl) {
            subEl.textContent =
               (row.tematik || '') +
               (row.week != null ? ' · Week ' + row.week : '') +
               (row.detail_indikator ? ' · ' + row.detail_indikator : '');
         }
         drill.forEach(function (d) {
            var tr = document.createElement('tr');
            tr.className = 'hover:bg-[#f8fafc]';
            tr.innerHTML =
               '<td class="px-4 py-3 align-top text-xs text-on-surface">' +
               esc(d.detail_indikator) +
               '</td>' +
               '<td class="px-4 py-3 align-top text-xs font-semibold text-on-surface">' +
               esc(d.site) +
               '</td>' +
               '<td class="px-4 py-3 align-top text-xs tabular-nums text-on-surface-variant">' +
               esc(d.week) +
               '</td>' +
               '<td class="px-4 py-3 align-top text-xs font-bold tabular-nums text-on-surface">' +
               esc(d.data) +
               '</td>';
            tbody.appendChild(tr);
         });
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
