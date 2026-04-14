{{-- Ringkasan singkat kinerja — mengisi sisa tinggi kolom di samping Operational Performance Matrix --}}
@php
    $briefTotal = (int) ($kpiTotal ?? 0);
    $briefValidGr = (int) ($kpiValidGrCount ?? 0);
    $briefCompletion = (float) ($kpiCompletion ?? 0);
    $briefTrendPct = $kpiTrendPct ?? null;
    $peerAi = $peerResourcesAiSummary ?? null;
    $peerAiText = is_array($peerAi) ? trim((string) ($peerAi['text'] ?? '')) : '';
    $peerAiSource = is_array($peerAi) ? ($peerAi['source'] ?? 'heuristic') : 'heuristic';
    $spBrief = is_array($sitePerformanceBrief ?? null) ? $sitePerformanceBrief : [];
    $spOk = !empty($spBrief['ok']);
    $spNarrative = $spOk ? trim((string) ($spBrief['narrative'] ?? '')) : trim((string) ($spBrief['message'] ?? ''));
    $spLastY = $spBrief['last_year'] ?? null;
    $spLastW = $spBrief['last_week'] ?? null;
    $spAttention = $spOk ? ($spBrief['attention_sites'] ?? []) : [];
    $spRepetitive = $spOk ? ($spBrief['repetitive'] ?? []) : [];
    $spOverall = $spOk ? ($spBrief['overall_by_site_mitra'] ?? []) : [];
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
   @if($spNarrative !== '')
   <div class="mt-4 rounded-xl border border-emerald-700/20 bg-emerald-50/80 p-3.5 shadow-inner" aria-label="Analisis site performance dari JSON">
      <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
         <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-900">Site performance</span>
         @if($spOk && $spLastY !== null && $spLastW !== null)
         <span class="rounded-full bg-white/90 px-2 py-0.5 text-[9px] font-semibold text-emerald-900 ring-1 ring-emerald-700/15" title="Minggu terakhir pada site_performance.json">Minggu terakhir: {{ $spLastY }} &mdash; W{{ $spLastW }}</span>
         @endif
      </div>
      <div class="max-h-40 overflow-y-auto text-[11px] leading-relaxed text-emerald-950">
         {!! nl2br(e($spNarrative)) !!}
      </div>
      @if($spOk && (count($spAttention) > 0 || count($spRepetitive) > 0 || count($spOverall) > 0))
      <div class="mt-3 space-y-2 border-t border-emerald-700/10 pt-3 text-[10px] text-emerald-950">
         @if(count($spAttention) > 0)
         <div>
            <p class="font-bold text-emerald-900">Perlu perhatian (&gt;3 gap)</p>
            <ul class="mt-1 list-inside list-disc space-y-0.5 text-emerald-950/95">
               @foreach(array_slice($spAttention, 0, 8) as $row)
               <li>{{ $row['site'] ?? '' }} / {{ $row['mitra'] ?? '' }} — {{ (int) ($row['gap_count'] ?? 0) }} parameter</li>
               @endforeach
            </ul>
            @if(count($spAttention) > 8)
            <p class="mt-1 text-[9px] text-emerald-800">+{{ count($spAttention) - 8 }} lainnya…</p>
            @endif
         </div>
         @endif
         @if(count($spRepetitive) > 0)
         <div>
            <p class="font-bold text-emerald-900">Repetitif (3 minggu berturut)</p>
            <ul class="mt-1 list-inside list-disc space-y-0.5 text-emerald-950/95">
               @foreach(array_slice($spRepetitive, 0, 8) as $r)
               <li>{{ $r['site'] ?? '' }} / {{ $r['mitra'] ?? '' }} — {{ $r['parameter'] ?? '' }}</li>
               @endforeach
            </ul>
         </div>
         @endif
         @if(count($spOverall) > 0)
         <div>
            <p class="font-bold text-emerald-900">Overall (heuristik)</p>
            <ul class="mt-1 list-inside list-disc space-y-0.5 text-emerald-950/95">
               @foreach(array_slice($spOverall, 0, 10, true) as $k => $lvl)
               <li><span class="font-mono text-[9px]">{{ $k }}</span>: {{ $lvl }}</li>
               @endforeach
            </ul>
         </div>
         @endif
      </div>
      @endif
   </div>
   @endif
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
   
</section>
