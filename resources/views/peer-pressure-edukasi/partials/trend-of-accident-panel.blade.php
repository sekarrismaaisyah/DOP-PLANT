{{--
  Tiga kartu: A Trend of Incident | B Trend of Accident | C Incident Rates.
  Desain dihaluskan: radius besar, bayangan lembut, area chart ringan.
--}}
@php
    $weeks = ['L4W', 'W13', 'W14', 'W15'];
    $c = [
        'accident' => '#E67E22',
        'nearmiss' => '#BDC3C7',
        'fire' => '#2980B9',
        'injury' => '#E74C3C',
        'property' => '#F1C40F',
        'hipo_gr' => '#E74C3C',
        'hipo_non' => '#F1C40F',
        'non_hipo' => '#27AE60',
        'ifr' => '#7FB3B3',
        'afr' => '#1e3a5f',
        'lti_fr' => '#8B7355',
        'lti_sr' => '#C0392B',
    ];
    $aMain2025 = [1.57, 1.04];
    $aMain2026 = [1.2, 1.07];
    $aHipo2025 = [0.2, 0.35, 0.45];
    $aHipo2026 = [0.25, 0.3, 0.45];
    $bMain2025 = [0.13, 0.17, 0.74];
    $bMain2026 = [0.33, 0.15, 0.52];
    $bHipo2025 = [0.22, 0.18, 0.6];
    $bHipo2026 = [0.25, 0.15, 0.55];
    $pairYearsA = [['2025', $aMain2025], ['2026', $aMain2026]];
    $pairYearsAHipo = [['2025', $aHipo2025], ['2026', $aHipo2026]];
    $pairYearsB = [['2025', $bMain2025], ['2026', $bMain2026]];
    $pairYearsBHipo = [['2025', $bHipo2025], ['2026', $bHipo2026]];
    $rateRows = [
        ['IFR', 'Incident Frequency Rate', $c['ifr'], [2.57], [2.46], [2.5, 2.52, 2.48, 2.46]],
        ['AFR', 'Accident Frequency Rate', $c['afr'], [1.03], [1.12], [1.0, 1.05, 1.08, 1.12]],
        ['LTI FR', 'LTI Frequency Rate', $c['lti_fr'], [0.0], [0.07], [0, 0, 0.02, 0.07]],
        ['LTI SR', 'LTI Severity Rate', $c['lti_sr'], [0.0], [318.08], [0, 0, 120, 318.08]],
    ];
@endphp

@once
<style>
   .peer-trend-panel-card { transition: box-shadow 0.35s ease, transform 0.35s ease; }
   .peer-trend-panel-card:hover { box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.12); transform: translateY(-1px); }
   .peer-trend-chart-wrap { background: linear-gradient(165deg, rgba(248, 250, 252, 0.95) 0%, rgba(255, 255, 255, 0.98) 55%, rgba(241, 245, 249, 0.5) 100%); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9); }
   .peer-trend-stack-outer { box-shadow: inset 0 2px 8px rgba(15, 23, 42, 0.06); }
</style>
@endonce

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3 lg:gap-6">
   {{-- A. Trend of Incident --}}
   <div class="peer-trend-panel-card flex min-w-0 flex-col overflow-hidden rounded-3xl border border-white/80 bg-white shadow-lg shadow-slate-200/50 ring-1 ring-slate-900/[0.06]" style="border-color: rgba(107, 127, 63, 0.35);">
      <div class="flex flex-col gap-2.5 px-4 py-3.5 sm:flex-row sm:items-center sm:justify-between" style="background: linear-gradient(145deg, #7d9254 0%, #6B7F3F 45%, #5a6d35 100%); box-shadow: inset 0 1px 0 rgba(255,255,255,0.12);">
         <h3 class="font-headline text-[13px] font-bold tracking-tight text-white drop-shadow-sm sm:text-sm">A. Trend of Incident</h3>
         <div class="flex flex-wrap gap-2">
            <span class="rounded-full border border-white/25 bg-white/95 px-2.5 py-1 text-[10px] font-bold tabular-nums text-neutral-800 shadow-sm backdrop-blur-sm">L4W: 1.50</span>
            <span class="rounded-full border border-white/25 bg-white/95 px-2.5 py-1 text-[10px] font-bold tabular-nums text-neutral-800 shadow-sm backdrop-blur-sm">LW: 3.00</span>
         </div>
      </div>
      <div class="space-y-4 bg-gradient-to-b from-white via-slate-50/30 to-white p-4">
         <div class="flex flex-wrap items-end gap-3">
            <div class="flex shrink-0 flex-col gap-2">
               <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1.5 text-[9px] font-bold text-white shadow-md ring-1 ring-black/5" style="background: {{ $c['accident'] }}">Accident</span>
               <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1.5 text-[9px] font-bold text-neutral-800 shadow-md ring-1 ring-black/5" style="background: {{ $c['nearmiss'] }}">Nearmiss</span>
            </div>
            <div class="flex min-w-0 flex-1 flex-wrap items-end justify-center gap-5">
               @foreach ($pairYearsA as $row)
               @php [$yr, $parts] = $row; $sum = array_sum($parts) ?: 1; $cols = [$c['nearmiss'], $c['accident']]; @endphp
               <div class="flex flex-col items-center gap-1.5">
                  <div class="peer-trend-stack-outer relative flex h-36 w-12 flex-col-reverse overflow-hidden rounded-2xl bg-white/90 ring-1 ring-slate-200/70 sm:h-40 sm:w-[3.25rem]">
                     @foreach ($parts as $i => $p)
                     @php $h = max(8, ($p / $sum) * 100); @endphp
                     <div class="flex items-center justify-center text-[7px] font-bold leading-none text-white shadow-sm" style="height: {{ $h }}%; background: {{ $cols[$i] }};">{{ number_format($p, 2) }}</div>
                     @endforeach
                     @if($yr === '2026')
                     <span class="absolute -top-6 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full bg-red-50 px-1.5 py-0.5 text-[8px] font-bold text-red-600 ring-1 ring-red-100">-12.9%</span>
                     @endif
                  </div>
                  <span class="text-[10px] font-semibold text-slate-500">{{ $yr }}</span>
               </div>
               @endforeach
            </div>
            <div class="peer-trend-chart-wrap h-40 min-h-[10rem] w-full min-w-[9rem] flex-1 overflow-hidden rounded-2xl p-2 ring-1 ring-slate-200/60 sm:h-44 sm:w-auto">
               <canvas id="peer-chart-line-incident-main" class="max-h-full w-full" height="160" aria-label="Garis tren Accident vs Nearmiss"></canvas>
            </div>
         </div>
         <div class="border-t border-slate-100/90 pt-4">
            <div class="flex flex-wrap items-end gap-3">
               <div class="flex shrink-0 flex-col gap-1.5">
                  <span class="rounded-full px-2 py-1 text-[8px] font-bold text-white shadow-sm ring-1 ring-black/5" style="background: {{ $c['hipo_gr'] }}">HIPO GR</span>
                  <span class="rounded-full px-2 py-1 text-[8px] font-bold text-neutral-900 shadow-sm ring-1 ring-black/5" style="background: {{ $c['hipo_non'] }}">HIPO NON GR</span>
                  <span class="rounded-full px-2 py-1 text-[8px] font-bold text-white shadow-sm ring-1 ring-black/5" style="background: {{ $c['non_hipo'] }}">NON HIPO</span>
               </div>
               <div class="flex min-w-0 flex-1 flex-wrap items-end justify-center gap-5">
                  @foreach ($pairYearsAHipo as $row)
                  @php [$yr, $parts] = $row; $sum = array_sum($parts) ?: 1; $hipoCols = [$c['hipo_gr'], $c['hipo_non'], $c['non_hipo']]; @endphp
                  <div class="flex flex-col items-center gap-1.5">
                     <div class="peer-trend-stack-outer flex h-28 w-12 flex-col-reverse overflow-hidden rounded-2xl bg-white/90 ring-1 ring-slate-200/70 sm:h-32">
                        @foreach ($parts as $i => $p)
                        @php $h = max(6, ($p / $sum) * 100); @endphp
                        <div class="flex items-center justify-center text-[7px] font-bold text-white shadow-sm" style="height: {{ $h }}%; background: {{ $hipoCols[$i] }};">{{ number_format($p, 2) }}</div>
                        @endforeach
                     </div>
                     <span class="text-[10px] font-semibold text-slate-500">{{ $yr }}</span>
                  </div>
                  @endforeach
               </div>
               <div class="peer-trend-chart-wrap h-32 min-h-[8rem] w-full min-w-[9rem] flex-1 overflow-hidden rounded-2xl p-2 ring-1 ring-slate-200/60 sm:w-auto">
                  <canvas id="peer-chart-line-incident-hipo" class="max-h-full w-full" height="128" aria-label="Garis tren HIPO"></canvas>
               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- B. Trend of Accident --}}
   <div class="peer-trend-panel-card flex min-w-0 flex-col overflow-hidden rounded-3xl border border-white/80 bg-white shadow-lg shadow-slate-200/50 ring-1 ring-slate-900/[0.06]" style="border-color: rgba(30, 58, 95, 0.35);">
      <div class="flex flex-col gap-2.5 px-4 py-3.5 sm:flex-row sm:items-center sm:justify-between" style="background: linear-gradient(145deg, #2a4a73 0%, #1e3a5f 50%, #152a45 100%); box-shadow: inset 0 1px 0 rgba(255,255,255,0.08);">
         <h3 class="font-headline text-[13px] font-bold tracking-tight text-white drop-shadow-sm sm:text-sm">B. Trend of Accident</h3>
         <div class="flex flex-wrap gap-2">
            <span class="rounded-full border border-white/25 bg-white/95 px-2.5 py-1 text-[10px] font-bold tabular-nums text-neutral-800 shadow-sm">L4W: {{ $accidentTrendL4w ?? '0.50' }}</span>
            <span class="rounded-full border border-white/25 bg-white/95 px-2.5 py-1 text-[10px] font-bold tabular-nums text-neutral-800 shadow-sm">LW: {{ $accidentTrendLw ?? '1.00' }}</span>
         </div>
      </div>
      <div class="space-y-4 bg-gradient-to-b from-white via-slate-50/30 to-white p-4">
         <div class="flex flex-wrap items-end gap-3">
            <div class="flex shrink-0 flex-col gap-2">
               <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1.5 text-[9px] font-bold text-white shadow-md ring-1 ring-black/5" style="background: {{ $c['fire'] }}"><span class="material-symbols-outlined text-[15px] opacity-95">local_fire_department</span> Fire</span>
               <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1.5 text-[9px] font-bold text-white shadow-md ring-1 ring-black/5" style="background: {{ $c['injury'] }}"><span class="material-symbols-outlined text-[15px] opacity-95">personal_injury</span> Injury</span>
               <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1.5 text-[9px] font-bold text-neutral-900 shadow-md ring-1 ring-black/5" style="background: {{ $c['property'] }}"><span class="material-symbols-outlined text-[15px] opacity-95">car_crash</span> Property</span>
            </div>
            <div class="flex min-w-0 flex-1 flex-wrap items-end justify-center gap-5">
               @foreach ($pairYearsB as $row)
               @php [$yr, $parts] = $row; $sum = array_sum($parts) ?: 1; $cols = [$c['fire'], $c['injury'], $c['property']]; @endphp
               <div class="flex flex-col items-center gap-1.5">
                  <div class="peer-trend-stack-outer relative flex h-36 w-12 flex-col-reverse overflow-hidden rounded-2xl bg-white/90 ring-1 ring-slate-200/70 sm:h-40 sm:w-[3.25rem]">
                     @foreach ($parts as $i => $p)
                     @php $h = max(8, ($p / $sum) * 100); @endphp
                     <div class="flex items-center justify-center text-[7px] font-bold text-white shadow-sm" style="height: {{ $h }}%; background: {{ $cols[$i] }};">{{ number_format($p, 2) }}</div>
                     @endforeach
                  </div>
                  <span class="text-[10px] font-semibold text-slate-500">{{ $yr }}</span>
               </div>
               @endforeach
            </div>
            <div class="peer-trend-chart-wrap h-40 min-h-[10rem] w-full min-w-[9rem] flex-1 overflow-hidden rounded-2xl p-2 ring-1 ring-slate-200/60 sm:h-44 sm:w-auto">
               <canvas id="peer-chart-line-accident-main" class="max-h-full w-full" height="160" aria-label="Garis tren Fire Injury Property"></canvas>
            </div>
         </div>
         <div class="border-t border-slate-100/90 pt-4">
            <div class="flex flex-wrap items-end gap-3">
               <div class="flex shrink-0 flex-col gap-1.5">
                  <span class="rounded-full px-2 py-1 text-[8px] font-bold text-white shadow-sm ring-1 ring-black/5" style="background: {{ $c['hipo_gr'] }}">HIPO GR</span>
                  <span class="rounded-full px-2 py-1 text-[8px] font-bold text-neutral-900 shadow-sm ring-1 ring-black/5" style="background: {{ $c['hipo_non'] }}">HIPO NON GR</span>
                  <span class="rounded-full px-2 py-1 text-[8px] font-bold text-white shadow-sm ring-1 ring-black/5" style="background: {{ $c['non_hipo'] }}">NON HIPO</span>
               </div>
               <div class="flex min-w-0 flex-1 flex-wrap items-end justify-center gap-5">
                  @foreach ($pairYearsBHipo as $row)
                  @php [$yr, $parts] = $row; $sum = array_sum($parts) ?: 1; $hipoCols = [$c['hipo_gr'], $c['hipo_non'], $c['non_hipo']]; @endphp
                  <div class="flex flex-col items-center gap-1.5">
                     <div class="peer-trend-stack-outer flex h-28 w-12 flex-col-reverse overflow-hidden rounded-2xl bg-white/90 ring-1 ring-slate-200/70 sm:h-32">
                        @foreach ($parts as $i => $p)
                        @php $h = max(6, ($p / $sum) * 100); @endphp
                        <div class="flex items-center justify-center text-[7px] font-bold text-white shadow-sm" style="height: {{ $h }}%; background: {{ $hipoCols[$i] }};">{{ number_format($p, 2) }}</div>
                        @endforeach
                     </div>
                     <span class="text-[10px] font-semibold text-slate-500">{{ $yr }}</span>
                  </div>
                  @endforeach
               </div>
               <div class="peer-trend-chart-wrap h-32 min-h-[8rem] w-full min-w-[9rem] flex-1 overflow-hidden rounded-2xl p-2 ring-1 ring-slate-200/60 sm:w-auto">
                  <canvas id="peer-chart-line-accident-hipo" class="max-h-full w-full" height="128" aria-label="Garis tren HIPO Accident"></canvas>
               </div>
            </div>
         </div>
      </div>
   </div>

   {{-- C. Incident Rates --}}
   <div class="peer-trend-panel-card flex min-w-0 flex-col overflow-hidden rounded-3xl border border-white/80 bg-white shadow-lg shadow-slate-200/50 ring-1 ring-slate-900/[0.06]" style="border-color: rgba(230, 126, 34, 0.4);">
      <div class="px-4 py-3.5" style="background: linear-gradient(145deg, #f39c4d 0%, #E67E22 45%, #d35400 100%); box-shadow: inset 0 1px 0 rgba(255,255,255,0.15);">
         <h3 class="font-headline text-center text-[13px] font-bold tracking-tight text-white drop-shadow-sm sm:text-sm">C. Incident Rates 2025 vs YTD 2026</h3>
      </div>
      <div class="space-y-3 bg-gradient-to-b from-white via-amber-50/10 to-white p-4">
         @foreach ($rateRows as $ri => $rate)
         @php
            [$code, $name, $col, $v25, $v26, $linePts] = $rate;
            $cid = 'peer-chart-line-rates-' . $ri;
            $v25n = (float) ($v25[0] ?? 0);
            $v26n = (float) ($v26[0] ?? 0);
            $barH25 = $v25n > 0 ? min(100.0, $v25n * 3) : 2.0;
            $barH26 = $v26n > 0 ? ($code === 'LTI SR' ? min(100.0, $v26n / 4) : min(100.0, $v26n * 3)) : 2.0;
         @endphp
         <div class="rounded-2xl border border-slate-100/90 bg-white/90 p-3 shadow-sm ring-1 ring-slate-900/[0.04] transition-shadow duration-300 hover:shadow-md">
            <div class="mb-3 flex flex-wrap items-center gap-2">
               <span class="rounded-full px-2.5 py-1 text-[9px] font-bold text-white shadow-sm ring-1 ring-black/5" style="background: {{ $col }}">{{ $code }}</span>
               <span class="text-[10px] font-semibold text-slate-700">{{ $name }}</span>
            </div>
            <div class="flex flex-wrap items-end gap-3">
               <div class="flex gap-4">
                  <div class="flex flex-col items-center gap-1">
                     <div class="peer-trend-stack-outer flex h-[4.25rem] w-9 items-end justify-center rounded-xl bg-slate-50/90 p-1 ring-1 ring-slate-200/60">
                        <div class="w-full max-w-[1.75rem] rounded-t-lg shadow-inner" style="height: {{ $barH25 }}%; min-height: 3px; background: linear-gradient(180deg, {{ $col }} 0%, {{ $col }}dd 100%); opacity: 0.92;"></div>
                     </div>
                     <span class="text-[8px] font-semibold uppercase tracking-wide text-slate-400">2025</span>
                     <span class="text-[10px] font-bold tabular-nums text-slate-800">{{ number_format($v25n, 2) }}</span>
                  </div>
                  <div class="flex flex-col items-center gap-1">
                     <div class="peer-trend-stack-outer flex h-[4.25rem] w-9 items-end justify-center rounded-xl bg-slate-50/90 p-1 ring-1 ring-slate-200/60">
                        <div class="w-full max-w-[1.75rem] rounded-t-lg shadow-inner" style="height: {{ $barH26 }}%; min-height: 3px; background: linear-gradient(180deg, {{ $col }} 0%, {{ $col }} 100%);"></div>
                     </div>
                     <span class="text-[8px] font-semibold uppercase tracking-wide text-slate-400">2026</span>
                     <span class="text-[10px] font-bold tabular-nums text-slate-800">{{ number_format($v26n, 2) }}</span>
                  </div>
               </div>
               <div class="peer-trend-chart-wrap h-[5.25rem] min-h-[5.25rem] min-w-0 flex-1 overflow-hidden rounded-xl p-1.5 ring-1 ring-slate-200/50">
                  <canvas id="{{ $cid }}" data-metric="{{ $code }}" class="max-h-full w-full" height="84" aria-label="Tren {{ $name }}"></canvas>
               </div>
            </div>
         </div>
         @endforeach
      </div>
   </div>
</div>

@php
    $peerTrendPanelsPayload = [
        'weeks' => $weeks,
        'incidentMain' => [[1.1, 1.2, 1.15, 1.18], [1.5, 1.52, 1.48, 1.5]],
        'incidentHipo' => [[0.35, 0.38, 0.36, 0.4], [0.28, 0.3, 0.32, 0.35], [0.37, 0.35, 0.34, 0.36]],
        'accidentMain' => [[0.12, 0.14, 0.11, 0.13], [0.18, 0.19, 0.17, 0.2], [0.72, 0.7, 0.74, 0.71]],
        'accidentHipo' => [[0.2, 0.22, 0.21, 0.23], [0.18, 0.17, 0.19, 0.18], [0.58, 0.57, 0.59, 0.6]],
        'rates' => [
            [2.5, 2.52, 2.48, 2.46],
            [1.0, 1.05, 1.08, 1.12],
            [0, 0, 0.02, 0.07],
            [0, 0, 120, 318.08],
        ],
        'colors' => $c,
    ];
@endphp
<script type="application/json" id="peer-trend-panels-chart-data">{!! json_encode($peerTrendPanelsPayload, JSON_UNESCAPED_UNICODE) !!}</script>
