{{-- Mini bar mingguan (selaras alignment hazardEvalGrid). @include dengan: ['eval' => [...], 'compact' => optional bool, 'chartTopMargin' => optional Tailwind classes] --}}
@php
    $weeks = $eval['weeks'] ?? [];
    $vals = $eval['values'] ?? [];
    $bar = $eval['bar'] ?? '#94a3b8';
    $label = $eval['label'] ?? '';
    $dec = (int) ($eval['decimals'] ?? 2);
    $_mx = count($vals) > 0 ? max($vals) : 0;
    $maxV = $_mx > 0 ? $_mx : 1;
    $compact = !empty($compact);
    $hChart = $compact ? 'h-20 sm:h-24' : 'h-24 sm:h-28';
    $mtWrap = $chartTopMargin ?? ($compact ? 'mt-2' : 'mt-3');
    $fsAxis = $compact ? 'text-[7px] sm:text-[8px]' : 'text-[8px] sm:text-[9px]';
    $fsVal = $compact ? 'text-[7px] sm:text-[8px]' : 'text-[8px] sm:text-[9px]';
@endphp
@if (count($vals))
<div class="{{ $mtWrap }} w-full">
   <div class="mb-1.5 flex items-start gap-2">
      <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full shadow-sm ring-2 ring-white" style="background-color: {{ $bar }}"></span>
      <p class="min-w-0 flex-1 text-left text-[10px] font-semibold leading-snug text-slate-800">Trend Last 4 Weeks</p>
   </div>
   <div class="peer-chart-scroll w-full overflow-x-auto">
      <div class="peer-chart-scroll-inner w-max min-w-full px-0.5">
         <div class="relative {{ $hChart }}">
            <div class="peer-chart-bars relative z-10 flex h-full w-full items-stretch gap-1 sm:gap-2">
               @foreach ($vals as $vi => $val)
               @php
                  $pct = (int) round(((float) $val / (float) $maxV) * 100);
                  $barH = min(100, max(0, (float) $pct));
                  $fmt = number_format((float) $val, $dec, ',', '.');
                  $wkLbl = $weeks[$vi] ?? ('W' . ($vi + 1));
                  $tip = $wkLbl.' — '.$fmt;
               @endphp
               <div class="peer-chart-bar-col group relative flex h-full min-h-0 min-w-[2.25rem] flex-1 basis-0 flex-col justify-end rounded-t-lg border-x border-t border-outline-variant/10 bg-[#f8fafc]" title="{{ $tip }}">
                  <div class="relative w-full" style="height: {{ $barH }}%">
                     <span class="absolute -top-5 left-1/2 z-10 -translate-x-1/2 whitespace-nowrap font-semibold tabular-nums text-on-surface {{ $fsVal }}">{{ $fmt }}</span>
                     <div class="absolute inset-0 flex flex-col justify-end overflow-hidden rounded-t-md shadow-inner ring-1 ring-black/10">
                        <div class="min-h-[2px] w-full shrink-0 transition-opacity group-hover:opacity-95" style="height: 100%; background-color: {{ $bar }}"></div>
                     </div>
                  </div>
               </div>
               @endforeach
            </div>
         </div>
         <div class="peer-chart-axis-labels mt-1.5 flex w-full min-w-0 gap-1 sm:gap-2 font-bold uppercase tracking-wider text-on-surface-variant sm:tracking-widest {{ $fsAxis }}">
            @foreach ($vals as $vi => $_)
            <span class="peer-chart-axis-tick min-w-[2rem] flex-1 basis-0 text-center leading-tight">{{ $weeks[$vi] ?? ('W'.($vi + 1)) }}</span>
            @endforeach
         </div>
      </div>
   </div>
</div>
@endif
