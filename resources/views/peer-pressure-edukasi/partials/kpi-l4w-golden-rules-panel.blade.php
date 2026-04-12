{{--
  Panel ringkas + batang L4W (layout seperti mockup).
  @param int $total — total laporan L4W (jumlah 4 minggu) atau agregat dari JSON
  @param int $validGr — metrik sekunder (mis. Valid GR, atau nilai minggu terakhir)
  @param array $weeks
  @param array $values
  @param bool $grFromJson — data dari peer_pressure_golden_rules_by_site.json (sinkron JS filter)
  @param bool $ankFromJson — data dari peer_pressure_area_non_kritis_by_site.json
  @param bool $kritisFromJson — data dari peer_pressure_area_kritis_by_site.json
  @param string|null $chartTitle — judul chart kanan
  @param string|null $panelSubtitleSuffix — teks setelah angka sekunder (mis. "Valid GR" / "minggu terakhir")
  @param string|null $barColor — warna batang hex
--}}
@php
    $weeks = $weeks ?? ($eval['weeks'] ?? []);
    $vals = $values ?? ($eval['values'] ?? []);
    if (! count($vals)) {
        $weeks = ['W11', 'W12', 'W13', 'W14'];
        $vals = [1, 1, 3, 1];
    }
    $maxV = max(1e-9, (float) max(array_map(static fn ($v) => (float) $v, $vals)));
    $barRed = $barColor ?? '#c8102e';
    $chartPx = 104;
    $grFromJson = ! empty($grFromJson);
    $ankFromJson = ! empty($ankFromJson);
    $kritisFromJson = ! empty($kritisFromJson);
    $chartTitle = $chartTitle ?? 'L4W Golden Rules Violation';
    $panelSubtitleSuffix = $panelSubtitleSuffix ?? 'Valid GR';
    if ($kritisFromJson) {
        $chartRootClass = 'peer-kritis-l4w-chart-root';
    } elseif ($ankFromJson) {
        $chartRootClass = 'peer-ank-l4w-chart-root';
    } else {
        $chartRootClass = 'peer-gr-l4w-chart-root';
    }
@endphp
<div class="flex w-full flex-row gap-4 sm:gap-5">
   <div class="flex w-[38%] min-w-[6.5rem] shrink-0 flex-col justify-center gap-0.5 text-left">
      <p class="text-[13px] font-medium italic leading-tight" style="color: {{ $barRed }}">TOTAL Laporan</p>
      <p class="peer-kpi-hr-stack-total font-headline text-4xl font-bold leading-none tracking-tight text-neutral-900" @if($grFromJson) data-gr-json="1" @endif @if($ankFromJson) data-ank-json="1" @endif @if($kritisFromJson) data-kritis-json="1" @endif>{{ number_format((int) $total) }}</p>
      <p class="peer-gr-valid-gr-display mt-1 text-[13px] font-medium text-neutral-500" @if($grFromJson) data-gr-json="1" @endif @if($ankFromJson) data-ank-json="1" @endif @if($kritisFromJson) data-kritis-json="1" @endif>{{ number_format((int) $validGr) }} {{ $panelSubtitleSuffix }}</p>
      <p class="mt-2 text-[11px] font-bold uppercase tracking-wide text-neutral-900">REPORTS</p>
   </div>
   <div class="min-w-0 flex-1 flex flex-col">
      <p class="mb-2 text-left text-[12px] font-medium leading-tight text-neutral-900">{{ $chartTitle }}</p>
      <div class="{{ $chartRootClass }} relative w-full border-b border-neutral-200/90">
         <div
            class="pointer-events-none absolute inset-x-0 bottom-0 top-6 z-0 flex flex-col justify-between opacity-[0.45]"
            style="height: {{ $chartPx }}px"
            aria-hidden="true">
            @for ($g = 0; $g < 5; $g++)
            <div class="h-px w-full bg-neutral-300/80"></div>
            @endfor
         </div>
         <div class="relative z-10 flex items-end gap-1.5 sm:gap-2 pt-5" style="height: {{ $chartPx }}px">
            @foreach ($vals as $vi => $raw)
            @php
               $v = (float) $raw;
               $ratio = min(1.0, max(0.0, $v / $maxV));
               $barPct = $ratio * 100.0;
               $dec = (int) ($decimals ?? 0);
               $lbl = (abs($v - round($v)) < 0.0001) ? (string) (int) round($v) : number_format($v, $dec, ',', '.');
            @endphp
            <div class="flex h-full min-h-0 min-w-0 flex-1 flex-col items-center">
               <span class="mb-1 shrink-0 text-center text-[11px] font-semibold tabular-nums text-neutral-900">{{ $lbl }}</span>
               <div class="flex min-h-0 w-full flex-1 flex-col justify-end">
                  <div
                     class="w-full rounded-none"
                     style="height: {{ $barPct }}%; min-height: {{ $v > 0 ? '2px' : '0' }}; background-color: {{ $barRed }};"></div>
               </div>
            </div>
            @endforeach
         </div>
         <div class="mt-2 flex w-full gap-1.5 sm:gap-2">
            @foreach ($vals as $vi => $_)
            <span class="min-w-0 flex-1 text-center text-[9px] font-semibold uppercase tracking-wide text-neutral-600">{{ $weeks[$vi] ?? ('W' . (11 + $vi)) }}</span>
            @endforeach
         </div>
      </div>
   </div>
</div>
