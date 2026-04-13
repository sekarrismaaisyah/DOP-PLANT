{{-- Ringkasan singkat kinerja — mengisi sisa tinggi kolom di samping Operational Performance Matrix --}}
@php
    $briefTotal = (int) ($kpiTotal ?? 0);
    $briefValidGr = (int) ($kpiValidGrCount ?? 0);
    $briefCompletion = (float) ($kpiCompletion ?? 0);
    $briefTrendPct = $kpiTrendPct ?? null;
    $peerAi = $peerResourcesAiSummary ?? null;
    $peerAiText = is_array($peerAi) ? trim((string) ($peerAi['text'] ?? '')) : '';
    $peerAiSource = is_array($peerAi) ? ($peerAi['source'] ?? 'heuristic') : 'heuristic';
@endphp
<section class="anchored-card flex min-h-0 flex-1 flex-col justify-between rounded-2xl bg-white p-5" aria-label="Ringkasan singkat kinerja">
   <div class="flex items-start gap-3">
      <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary ring-1 ring-primary/15">
         <span class="material-symbols-outlined text-[22px]" data-icon="summarize">summarize</span>
      </div>
      <div class="min-w-0 flex-1">
         <h3 class="font-headline text-sm font-bold tracking-tight text-on-surface">Brief summary</h3>
         <p class="mt-0.5 text-[10px] font-medium leading-snug text-on-surface-variant">Cuplikan indikator utama Peer Pressure &amp; kepatuhan untuk periode yang dipilih, plus ringkasan dari seluruh data <code class="rounded bg-surface-container-high px-1 py-px text-[9px]">resources/data</code>.</p>
      </div>
   </div>
   @if($peerAiText !== '')
   <div class="mt-4 rounded-xl border border-outline-variant/25 bg-surface-container-low/90 p-3.5 shadow-inner" aria-label="Ringkasan AI dari data JSON">
      <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
         <span class="text-[10px] font-bold uppercase tracking-wider text-primary">Ringkasan AI</span>
         @if($peerAiSource === 'openai')
         <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[9px] font-semibold text-primary">OpenAI</span>
         @else
         <span class="rounded-full bg-on-surface-variant/10 px-2 py-0.5 text-[9px] font-medium text-on-surface-variant" title="Tanpa OPENAI_API_KEY: narasi dibangun otomatis dari angka di JSON">Lokal</span>
         @endif
      </div>
      <div class="max-h-48 overflow-y-auto text-[11px] leading-relaxed text-on-surface">
         {!! nl2br(e($peerAiText)) !!}
      </div>
   </div>
   @endif
   <!-- <ul class="mt-4 space-y-3 text-[11px] leading-relaxed text-on-surface">
      <li class="flex gap-2">
         <span class="mt-0.5 shrink-0 text-primary" aria-hidden="true">
            <span class="material-symbols-outlined text-[16px]" data-icon="numbers">numbers</span>
         </span>
         <span><span class="font-semibold text-on-surface">Total kasus:</span>
            <span id="peer-kpi-brief-total" class="tabular-nums font-bold text-on-background">{{ number_format($briefTotal) }}</span></span>
      </li>
      <li class="flex gap-2">
         <span class="mt-0.5 shrink-0 text-secondary" aria-hidden="true">
            <span class="material-symbols-outlined text-[16px]" data-icon="rule">rule</span>
         </span>
         <span><span class="font-semibold text-on-surface">Peer pressure comply (GR):</span>
            <span id="peer-kpi-brief-valid-gr" class="tabular-nums font-bold text-on-background">{{ number_format($briefValidGr) }}</span></span>
      </li>
      <li class="flex gap-2">
         <span class="mt-0.5 shrink-0 text-tertiary" aria-hidden="true">
            <span class="material-symbols-outlined text-[16px]" data-icon="percent">percent</span>
         </span>
         <span><span class="font-semibold text-on-surface">Tingkat penyelesaian:</span>
            <span id="peer-kpi-brief-completion" class="tabular-nums font-bold text-on-background">{{ number_format($briefCompletion, 1) }}%</span></span>
      </li>
      <li class="flex gap-2 border-t border-outline-variant/25 pt-3">
         <span class="mt-0.5 shrink-0 text-on-surface-variant" aria-hidden="true">
            <span class="material-symbols-outlined text-[16px]" data-icon="show_chart">show_chart</span>
         </span>
         <span class="min-w-0 flex-1">
            <span class="font-semibold text-on-surface">Tren kasus:</span>
            <div id="peer-kpi-brief-trend" class="mt-1">
               @if($briefTrendPct !== null)
               <p class="text-[11px] font-bold flex items-center gap-1 {{ $briefTrendPct <= 0 ? 'text-[#059669]' : 'text-error' }}">
                  <span class="material-symbols-outlined text-xs" data-icon="{{ $briefTrendPct <= 0 ? 'trending_down' : 'trending_up' }}">{{ $briefTrendPct <= 0 ? 'trending_down' : 'trending_up' }}</span>
                  {{ $kpi['total_cases_trend_label'] ?? '' }}
               </p>
               @else
               <p class="text-on-surface-variant text-[11px] font-medium">{{ $kpi['total_cases_trend_label'] ?? '—' }}</p>
               @endif
            </div>
         </span>
      </li>
   </ul> -->
</section>
