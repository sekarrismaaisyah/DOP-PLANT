@php
   $matrixShellClass = $matrixShellClass ?? 'md:col-span-2 lg:col-span-7';
   $matrixRowsRaw = $operationalPerformanceMatrixRows ?? null;
   if ($matrixRowsRaw === null) {
       $matrixJsonPath = resource_path('data/operational_performance_matrix.json');
       $matrixRowsRaw = is_file($matrixJsonPath)
           ? json_decode((string) file_get_contents($matrixJsonPath), true)
           : [];
   }
   if (! is_array($matrixRowsRaw)) {
       $matrixRowsRaw = [];
   }

   $peerOpMatrixIssuesRaw = $operationalPerformanceMatrixIssues ?? null;
   if ($peerOpMatrixIssuesRaw === null) {
       $issuesJsonPath = resource_path('data/operational_performance_matrix_issues.json');
       $peerOpMatrixIssuesRaw = is_file($issuesJsonPath)
           ? json_decode((string) file_get_contents($issuesJsonPath), true)
           : [];
   }
   if (! is_array($peerOpMatrixIssuesRaw)) {
       $peerOpMatrixIssuesRaw = [];
   }

   /** Kolom matrix per grup site (index ke $peerOpMatrixCols / $peerOpMatrixColDefs) */
   $peerOpMatrixSiteGroups = [
       'BMO 1' => [0, 1, 2],
       'BMO 2' => [3],
       'BMO 3' => [4],
       'GMO' => [5],
       'LMO' => [6, 7],
       'SMO' => [8],
   ];

   $peerOpMatrixNormalizeSite = static function (string $s): string {
       $s = trim($s);
       if (preg_match('/^BMO\s*1$/i', $s)) {
           return 'BMO 1';
       }
       if (preg_match('/^BMO\s*2$/i', $s)) {
           return 'BMO 2';
       }
       if (preg_match('/^BMO\s*3$/i', $s)) {
           return 'BMO 3';
       }

       return $s;
   };

   /** Urutan kolom = header: BMO1×3, BMO2, BMO3, GMO, LMO×2, SMO */
   $peerOpMatrixColDefs = [
       ['BMO 1', 'BUMA'],
       ['BMO 1', 'KDC'],
       ['BMO 1', 'MTL'],
       ['BMO 2', 'PAMA'],
       ['BMO 3', 'BAR'],
       ['GMO', 'PAMA'],
       ['LMO', 'BUMA'],
       ['LMO', 'FAD'],
       ['SMO', 'MTN'],
   ];

   $peerOpMatrixCols = [];
   foreach ($peerOpMatrixColDefs as [$st, $mt]) {
       $found = null;
       foreach ($matrixRowsRaw as $r) {
           if (! is_array($r)) {
               continue;
           }
           $rs = $peerOpMatrixNormalizeSite((string) ($r['Site'] ?? ''));
           $rm = trim((string) ($r['Mitra Kerja'] ?? ''));
           if ($rs === $st && strcasecmp($rm, $mt) === 0) {
               $found = $r;
               break;
           }
       }
       $peerOpMatrixCols[] = $found;
   }

   $matrixMeta = $matrixRowsRaw[0] ?? [];
   $matrixWeek = (int) ($matrixMeta['Week'] ?? 0);
   $matrixYear = (int) ($matrixMeta['Year'] ?? 0);

   $K = [
       'ratioTbc' => 'Ratio Pelaporan TBC (TBC/person)',
       'covAll' => 'Coverage Area All',
       'covKrit' => 'Coverage Area Kritis',
   ];

   $peerOpMatrixFmt = static function (?array $row, string $key): string {
       if ($row === null || ! array_key_exists($key, $row)) {
           return '—';
       }
       $v = $row[$key];
       if ($v === null || $v === '') {
           return '—';
       }
       if (is_string($v) && trim($v) === '') {
           return '—';
       }
       if (is_string($v) && strtoupper(trim($v)) === 'N/A') {
           return 'N/A';
       }
       if (is_int($v)) {
           return (string) $v;
       }
       if (is_float($v)) {
           return number_format($v, 2, ',', '.');
       }
       if (is_numeric($v)) {
           $f = (float) $v;

           return abs($f - round($f)) < 1e-9 ? (string) (int) round($f) : number_format($f, 2, ',', '.');
       }

       return (string) $v;
   };

   $peerOpMatrixFmtIf = static function (?array $row, string $key, int $decimals): string {
       if ($row === null || ! array_key_exists($key, $row)) {
           return '—';
       }
       $v = $row[$key];
       if ($v === null || $v === '') {
           return '—';
       }
       if (! is_numeric($v)) {
           return '—';
       }

       return number_format((float) $v, $decimals, ',', '.');
   };

   $peerOpMatrixParsePct = static function ($v): ?float {
       if ($v === null || $v === '') {
           return null;
       }
       if (is_numeric($v)) {
           return (float) $v;
       }
       $s = str_replace(['%', ' '], ['', ''], (string) $v);
       $s = str_replace(',', '.', $s);
       if ($s === '' || strtoupper($s) === 'N/A') {
           return null;
       }

       return is_numeric($s) ? (float) $s : null;
   };

   $peerOpMatrixCrit = static function (?array $row, string $mode) use ($K, $peerOpMatrixParsePct): bool {
       if ($row === null) {
           return false;
       }
       switch ($mode) {
           case 'gr':
               return isset($row['GR']) && is_numeric($row['GR']) && (float) $row['GR'] > 0;
           case 'pspp':
               return isset($row['PSPP']) && is_numeric($row['PSPP']) && (float) $row['PSPP'] > 0;
           case 'blindspot':
               $p = $peerOpMatrixParsePct($row['Blindspot TBC'] ?? null);

               return $p !== null && $p > 1.5;
           case 'overdue':
               $p = $peerOpMatrixParsePct($row['Overdue Hazard'] ?? null);

               return $p !== null && $p > 0.5;
           case 'cov_all':
               $p = $peerOpMatrixParsePct($row[$K['covAll']] ?? null);

               return $p !== null && $p < 100;
           case 'pja_mk':
               $p = $peerOpMatrixParsePct($row['PJA MK'] ?? null);

               return $p !== null && $p < 99;
           case 'ratio':
               $v = $row[$K['ratioTbc']] ?? null;

               return is_numeric($v) && ((float) $v >= 5.0 || (float) $v <= 1.2);
           case 'realtime':
               $p = $peerOpMatrixParsePct($row['Real Time'] ?? null);

               return $p !== null && $p < 50;
           case 'post':
               $p = $peerOpMatrixParsePct($row['Post Event'] ?? null);

               return $p !== null && $p < 50;
           default:
               return false;
       }
   };

   $peerOpMatrixTd = static function (bool $borderTop, bool $critical): string {
       $c = 'px-4 py-3 text-center font-bold text-xs';
       if ($borderTop) {
           $c .= ' border-t border-slate-100';
       }
       if ($critical) {
           $c .= ' bg-critical-cell text-error';
       }

       return $c;
   };
@endphp
@once
{{-- Selaras dengan style block Operational Performance Matrix di alignment/index.blade.php --}}
<style>
   .bg-critical-cell { background-color: #ffdaed; }
   .peer-matrix-table td.bg-critical-cell {
      border-radius: 0.25rem;
      transition: background-color 0.2s ease;
   }
   .peer-matrix-shell {
      transition: box-shadow 0.35s ease, transform 0.35s ease;
   }
   .peer-matrix-shell:hover {
      box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.14);
   }
   .peer-matrix-site-btn { cursor: pointer; }
</style>
@endonce
         <div class="peer-matrix-shell group relative {{ $matrixShellClass }} overflow-hidden rounded-2xl border border-slate-100/90 bg-surface-container-lowest text-left shadow-xl shadow-slate-200/50 ring-1 ring-slate-900/[0.04]">
               <div class="relative overflow-hidden border-b border-slate-100/90 bg-gradient-to-br from-white via-slate-50/50 to-indigo-50/25 px-5 py-5 sm:px-8 sm:py-6">
                  <div class="pointer-events-none absolute -right-16 -top-12 h-40 w-40 rounded-full bg-primary/[0.07] blur-3xl"></div>
                  <div class="pointer-events-none absolute -bottom-8 left-1/3 h-24 w-72 rounded-full bg-secondary/[0.06] blur-2xl"></div>
                  <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                     <div class="flex min-w-0 items-center gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-primary shadow-[inset_0_1px_0_rgba(255,255,255,0.9)] ring-1 ring-primary/15">
                           <span class="material-symbols-outlined text-[26px]" data-icon="analytics">analytics</span>
                        </div>
                        <div class="min-w-0">
                           <h3 class="font-headline text-lg font-bold tracking-tight text-on-surface">Operational Performance Matrix</h3>
                           <p class="mt-0.5 text-[11px] font-medium text-slate-500">Ringkasan kinerja lintas unit operasi</p>
                        </div>
                     </div>
                     <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/80 bg-white/70 px-3 py-1.5 text-[11px] font-semibold text-slate-600 shadow-sm backdrop-blur-sm transition hover:bg-white">
                           <span class="h-2 w-2 shrink-0 rounded-full bg-tertiary-fixed-dim ring-2 ring-white shadow-sm"></span>
                           Lagging
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/80 bg-white/70 px-3 py-1.5 text-[11px] font-semibold text-slate-600 shadow-sm backdrop-blur-sm transition hover:bg-white">
                           <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-400 ring-2 ring-white shadow-sm"></span>
                           Leading
                        </span>
                     </div>
                  </div>
               </div>
               <div class="overflow-x-auto hide-scrollbar px-0.5 pb-0.5 sm:px-0">
                  <table class="peer-matrix-table w-full border-collapse text-left">
                     <thead>
                        <tr class="border-b border-slate-100/90 bg-gradient-to-b from-slate-50 to-slate-50/80 shadow-[0_1px_0_0_rgba(15,23,42,0.05)]">
                           <th class="px-4 py-3.5 text-[10px] font-bold uppercase tracking-wider text-slate-400" colspan="2">Indicator &amp; Metrics</th>
                           <th class="border-l border-slate-100/90 px-2 py-2 text-center" colspan="3">
                              <button type="button" class="peer-matrix-site-btn w-full rounded-xl px-3 py-2.5 text-[11px] font-bold tracking-tight text-slate-700 transition hover:bg-slate-100/90 focus:outline-none focus:ring-2 focus:ring-primary/30" data-peer-matrix-site="BMO 1">BMO 1</button>
                           </th>
                           <th class="border-l border-slate-100/90 px-2 py-2 text-center">
                              <button type="button" class="peer-matrix-site-btn w-full rounded-xl px-3 py-2.5 text-[11px] font-bold tracking-tight text-slate-700 transition hover:bg-slate-100/90 focus:outline-none focus:ring-2 focus:ring-primary/30" data-peer-matrix-site="BMO 2">BMO 2</button>
                           </th>
                           <th class="border-l border-slate-100/90 px-2 py-2 text-center">
                              <button type="button" class="peer-matrix-site-btn w-full rounded-xl px-3 py-2.5 text-[11px] font-bold tracking-tight text-slate-700 transition hover:bg-slate-100/90 focus:outline-none focus:ring-2 focus:ring-primary/30" data-peer-matrix-site="BMO 3">BMO 3</button>
                           </th>
                           <th class="border-l border-slate-100/90 px-2 py-2 text-center">
                              <button type="button" class="peer-matrix-site-btn w-full rounded-xl px-3 py-2.5 text-[11px] font-bold tracking-tight text-slate-700 transition hover:bg-slate-100/90 focus:outline-none focus:ring-2 focus:ring-primary/30" data-peer-matrix-site="GMO">GMO</button>
                           </th>
                           <th class="border-l border-slate-100/90 px-2 py-2 text-center" colspan="2">
                              <button type="button" class="peer-matrix-site-btn w-full rounded-xl px-3 py-2.5 text-[11px] font-bold tracking-tight text-slate-700 transition hover:bg-slate-100/90 focus:outline-none focus:ring-2 focus:ring-primary/30" data-peer-matrix-site="LMO">LMO</button>
                           </th>
                           <th class="border-l border-slate-100/90 px-2 py-2 text-center">
                              <button type="button" class="peer-matrix-site-btn w-full rounded-xl px-3 py-2.5 text-[11px] font-bold tracking-tight text-slate-700 transition hover:bg-slate-100/90 focus:outline-none focus:ring-2 focus:ring-primary/30" data-peer-matrix-site="SMO">SMO</button>
                           </th>
                        </tr>
                        <tr class="border-b border-slate-100/90 bg-surface-container-low/90">
                           <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500" colspan="2"></th>
                           <th class="border-l border-slate-100/80 px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500">BUMA</th>
                           <th class="border-l border-slate-100/80 px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500">KDC</th>
                           <th class="border-l border-slate-100/80 px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500">MTL</th>
                           <th class="border-l border-slate-100/80 px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500">PAMA</th>
                           <th class="border-l border-slate-100/80 px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500">BAR</th>
                           <th class="border-l border-slate-100/80 px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500">PAMA</th>
                           <th class="border-l border-slate-100/80 px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500">BUMA</th>
                           <th class="border-l border-slate-100/80 px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500">FAD</th>
                           <th class="border-l border-slate-100/80 px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500">MTN</th>
                        </tr>
                     </thead>
                     <tbody class="divide-y divide-slate-100/80">
                        @include('peer-pressure-edukasi.partials.operational-performance-matrix-tbody')
                     </tbody>
                  </table>
               </div>
               <div class="flex flex-col gap-3 border-t border-slate-100/90 bg-gradient-to-r from-slate-50/90 via-white to-slate-50/90 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                  <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                     @if($matrixWeek > 0 && $matrixYear > 0)
                     Data Week {{ $matrixWeek }} · {{ $matrixYear }} · sumber JSON matrix
                     @else
                     Showing data — sumber JSON matrix
                     @endif
                  </p>
                  <div class="flex flex-wrap items-center gap-3 text-[10px] font-bold">
                     <button type="button" class="rounded-lg px-2 py-1 text-primary transition-colors hover:bg-primary/5 hover:text-primary/90">Download Matrix CSV</button>
                     <span class="hidden h-3 w-px bg-slate-200 sm:inline" aria-hidden="true"></span>
                     <button type="button" class="rounded-lg px-2 py-1 text-primary transition-colors hover:bg-primary/5 hover:text-primary/90">View Historical Trend</button>
                  </div>
               </div>
            </div>
@once
<div id="peer-matrix-site-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="peer-matrix-site-modal-title" aria-hidden="true">
   <div class="fixed inset-0 bg-slate-900/55 backdrop-blur-[2px] transition-opacity" data-peer-matrix-site-close tabindex="-1"></div>
   <div class="relative mx-auto flex min-h-full max-w-6xl items-start justify-center px-3 py-8 sm:px-6 lg:px-8">
      <div class="relative w-full overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-2xl shadow-slate-900/15 ring-1 ring-slate-900/[0.06]">
         <div class="flex items-start justify-between gap-3 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-4 py-4 sm:px-6">
            <div class="min-w-0">
               <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Operational Performance Matrix</p>
               <h2 id="peer-matrix-site-modal-title" class="mt-1 font-headline text-lg font-bold tracking-tight text-on-surface" data-peer-matrix-modal-title>Site</h2>
               @if($matrixWeek > 0 && $matrixYear > 0)
               <p class="mt-0.5 text-[11px] text-slate-500">Week {{ $matrixWeek }} · {{ $matrixYear }}</p>
               @endif
            </div>
            <button type="button" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200/80 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary/30" data-peer-matrix-site-close aria-label="Tutup">
               <span class="material-symbols-outlined text-[22px]" data-icon="close">close</span>
            </button>
         </div>
         <div class="max-h-[min(78vh,880px)] overflow-y-auto px-4 py-5 sm:px-6 sm:py-6">
            @foreach ($peerOpMatrixSiteGroups as $siteKey => $indices)
            @php
               $colsForSite = array_map(static fn ($i) => $peerOpMatrixCols[$i] ?? null, $indices);
               $issuesForSite = [];
               foreach ($peerOpMatrixIssuesRaw as $ir) {
                   if (! is_array($ir)) {
                       continue;
                   }
                   $ns = $peerOpMatrixNormalizeSite((string) ($ir['Site'] ?? ''));
                   if ($ns === $siteKey) {
                       $issuesForSite[] = $ir;
                   }
               }
            @endphp
            <div class="peer-matrix-site-panel hidden space-y-8" data-peer-matrix-site-panel="{{ $siteKey }}">
               <div>
                  <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Kinerja — {{ $siteKey }} saja</h3>
                  <div class="overflow-x-auto rounded-xl border border-slate-100/90">
                     <table class="peer-matrix-table w-full min-w-[520px] border-collapse text-left">
                        <thead>
                           <tr class="border-b border-slate-100/90 bg-gradient-to-b from-slate-50 to-slate-50/80">
                              <th class="px-4 py-3.5 text-[10px] font-bold uppercase tracking-wider text-slate-400" colspan="2">Indicator &amp; Metrics</th>
                              <th class="border-l border-slate-100/90 px-4 py-3.5 text-center text-[11px] font-bold tracking-tight text-slate-700" colspan="{{ count($indices) }}">{{ $siteKey }}</th>
                           </tr>
                           <tr class="border-b border-slate-100/90 bg-surface-container-low/90">
                              <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500" colspan="2"></th>
                              @foreach ($indices as $idx)
                              <th class="border-l border-slate-100/80 px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ $peerOpMatrixColDefs[$idx][1] ?? '' }}</th>
                              @endforeach
                           </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/80">
                           @include('peer-pressure-edukasi.partials.operational-performance-matrix-tbody', ['peerOpMatrixCols' => $colsForSite])
                        </tbody>
                     </table>
                  </div>
               </div>
               <div>
                  <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Issue &amp; rekomendasi — {{ $siteKey }}</h3>
                  @forelse ($issuesForSite as $issue)
                  <div class="overflow-x-auto rounded-xl border border-slate-100/90">
                     <table class="w-full min-w-[640px] border-collapse text-left text-xs">
                        <thead>
                           <tr class="border-b border-slate-100 bg-slate-50/90">
                              <th class="px-3 py-2.5 font-bold text-slate-600">Issue Date</th>
                              <th class="border-l border-slate-100 px-3 py-2.5 font-bold text-slate-600">Kategori</th>
                              <th class="border-l border-slate-100 px-3 py-2.5 font-bold text-slate-600">Due Date</th>
                              <th class="border-l border-slate-100 px-3 py-2.5 font-bold text-slate-600">Status</th>
                              <th class="border-l border-slate-100 px-3 py-2.5 font-bold text-slate-600">Aktual Penyelesaian</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr class="align-top">
                              <td class="border-t border-slate-100 px-3 py-3 text-slate-700">{{ ($issue['Issue Date'] ?? '') !== '' ? $issue['Issue Date'] : '—' }}</td>
                              <td class="border-l border-t border-slate-100 px-3 py-3 text-slate-700">{{ $issue['Kategori'] ?? '—' }}</td>
                              <td class="border-l border-t border-slate-100 px-3 py-3 text-slate-700">{{ ($issue['Due Date'] ?? '') !== '' ? $issue['Due Date'] : '—' }}</td>
                              <td class="border-l border-t border-slate-100 px-3 py-3">
                                 <span class="inline-flex rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-900 ring-1 ring-amber-200/80">{{ $issue['Status'] ?? '—' }}</span>
                              </td>
                              <td class="border-l border-t border-slate-100 px-3 py-3 text-slate-700">{{ ($issue['Aktual Penyelesaian'] ?? '') !== '' ? $issue['Aktual Penyelesaian'] : '—' }}</td>
                           </tr>
                           <tr class="align-top bg-slate-50/40">
                              <td class="px-3 py-2 text-[10px] font-bold uppercase tracking-wide text-slate-500" colspan="5">Issue</td>
                           </tr>
                           <tr class="align-top">
                              <td class="border-t border-slate-100 px-3 py-3 text-slate-800" colspan="5">
                                 <div class="max-h-48 overflow-y-auto whitespace-pre-wrap leading-relaxed">{{ $issue['Issue'] ?? '—' }}</div>
                              </td>
                           </tr>
                           <tr class="align-top bg-slate-50/40">
                              <td class="px-3 py-2 text-[10px] font-bold uppercase tracking-wide text-slate-500" colspan="5">Recommendation / Project</td>
                           </tr>
                           <tr class="align-top">
                              <td class="border-t border-slate-100 px-3 py-3 text-slate-800" colspan="5">
                                 <div class="max-h-56 overflow-y-auto whitespace-pre-wrap leading-relaxed">{{ $issue['Recommendation / Project'] ?? '—' }}</div>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  @empty
                  <p class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 px-4 py-6 text-center text-xs text-slate-500">Belum ada data issue &amp; rekomendasi untuk site ini pada sumber JSON.</p>
                  @endforelse
               </div>
            </div>
            @endforeach
         </div>
      </div>
   </div>
</div>
<script>
(function () {
   function openPeerMatrixSiteModal(site) {
      var modal = document.getElementById('peer-matrix-site-modal');
      if (!modal) return;
      modal.classList.remove('hidden');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('overflow-hidden');
      var title = modal.querySelector('[data-peer-matrix-modal-title]');
      if (title) title.textContent = site + ' — detail kinerja & issue';
      modal.querySelectorAll('[data-peer-matrix-site-panel]').forEach(function (el) {
         el.classList.toggle('hidden', el.getAttribute('data-peer-matrix-site-panel') !== site);
      });
   }
   function closePeerMatrixSiteModal() {
      var modal = document.getElementById('peer-matrix-site-modal');
      if (!modal) return;
      modal.classList.add('hidden');
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('overflow-hidden');
   }
   document.addEventListener('click', function (e) {
      var openBtn = e.target.closest('[data-peer-matrix-site]');
      if (openBtn) {
         e.preventDefault();
         openPeerMatrixSiteModal(openBtn.getAttribute('data-peer-matrix-site'));
         return;
      }
      if (e.target.closest('[data-peer-matrix-site-close]')) {
         closePeerMatrixSiteModal();
      }
   });
   document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closePeerMatrixSiteModal();
   });
})();
</script>
@endonce
