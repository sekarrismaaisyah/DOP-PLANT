@php
    /** @var int $wizardStep */
    $wizardQuery = array_filter(
        request()->only(['year', 'month', 'q', 'hazard_site']),
        static fn ($v) => $v !== null && $v !== ''
    );
@endphp
<div class="flex flex-col gap-4 rounded-2xl border border-outline-variant/25 bg-white/90 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5 shadow-sm">
   <div class="text-sm text-on-surface-variant">
      <span class="font-semibold text-on-surface">Halaman {{ (int) $wizardStep }} dari 3</span>
      @if ((int) $wizardStep === 1)
         <span class="hidden sm:inline"> — Overview Safety Performance</span>
      @elseif ((int) $wizardStep === 2)
         <span class="hidden sm:inline"> — Dash Performance</span>
      @else
         <span class="hidden sm:inline"> — Thematic Alignment</span>
      @endif
   </div>
   <div class="flex flex-wrap items-center gap-3">
      @if ((int) $wizardStep > 1)
         <a
            href="{{ (int) $wizardStep === 2 ? route('peer-pressure-edukasi.dashboard', $wizardQuery) : route('peer-pressure-edukasi.dashboard-performance', $wizardQuery) }}"
            class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/40 bg-white px-4 py-2.5 text-sm font-bold text-on-surface shadow-sm transition-colors hover:bg-surface-container-high"
         >
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Kembali
         </a>
      @else
         <span
            class="inline-flex cursor-not-allowed items-center gap-1.5 rounded-xl border border-outline-variant/20 bg-surface-container-low/80 px-4 py-2.5 text-sm font-bold text-on-surface-variant opacity-60"
            aria-disabled="true"
         >
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Kembali
         </span>
      @endif
      @if ((int) $wizardStep < 3)
         <a
            href="{{ (int) $wizardStep === 1 ? route('peer-pressure-edukasi.dashboard-performance', $wizardQuery) : route('peer-pressure-edukasi.tematic', $wizardQuery) }}"
            class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-on-primary shadow-sm transition-colors hover:bg-primary-dim"
         >
            Lanjut
            <span class="material-symbols-outlined text-lg">arrow_forward</span>
         </a>
      @else
         <span
            class="inline-flex cursor-not-allowed items-center gap-1.5 rounded-xl bg-surface-container-high px-4 py-2.5 text-sm font-bold text-on-surface-variant opacity-60"
            aria-disabled="true"
         >
            Lanjut
            <span class="material-symbols-outlined text-lg">arrow_forward</span>
         </span>
      @endif
   </div>
</div>
