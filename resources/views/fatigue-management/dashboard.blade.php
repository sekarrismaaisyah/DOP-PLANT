@extends('PembatasanLV.layouts.app')

@section('title', 'Dashboard Checklist Fatigue Management GMO')

@push('head')
@include('fatigue-management.partials.styles')
@include('fatigue-management.partials.site-matrix-styles')
@endpush

@section('content')
@php
   $doc = $dashboard['document'] ?? [];
   $isoWeek = $filters['isoWeek'] ?? '';
   $year = $filters['year'] ?? date('Y');
   $typeClass = static function (?string $type): string {
      return match ($type) {
         'mandatory', 'wajib' => 'fm-type-pill--mandatory',
         'upgrade' => 'fm-type-pill--upgrade',
         default => 'fm-type-pill--mitra',
      };
   };
   $checklistClass = static function (?string $color): string {
      return match ($color) {
         'green' => 'fm-status--green',
         'blue' => 'fm-status--blue',
         'amber' => 'fm-status--amber',
         'red' => 'fm-status--red',
         default => 'fm-status--gray',
      };
   };
   $freqIcon = static function (string $key): string {
      return match ($key) {
         'shift' => 'schedule',
         'daily' => 'today',
         default => 'date_range',
      };
   };
@endphp

<div class="fm-mon -mt-2 space-y-7">
   <section class="pb-5 border-b border-outline-variant/30">
      <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-5">
         <div class="min-w-0">
            <nav class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.08em] text-on-surface-variant mb-2.5">
               <span>Fatigue Management GMO</span>
               <span class="material-symbols-outlined text-[13px] opacity-60">chevron_right</span>
               <span class="text-primary">Dashboard Checklist</span>
            </nav>
            <h1 class="font-headline font-extrabold text-3xl text-on-background tracking-tight">Checklist per Perusahaan</h1>
            <p class="mt-1.5 text-sm text-on-surface-variant">
               Site GMO · {{ $doc['code'] ?? 'FMP-STD-001' }} · {{ $isoWeek }} {{ $year }} · Shift / Harian / Mingguan
            </p>
            <div class="mt-2 flex flex-wrap gap-2 text-[10px] font-bold uppercase tracking-wide">
               <span class="fm-type-pill fm-type-pill--mandatory">M — Wajib dijalankan</span>
               <span class="fm-type-pill fm-type-pill--upgrade">U — Upgrade frekuensi</span>
            </div>
            <div class="mt-3">
               <a href="{{ route('fatigue-management.upload', request()->only(['year', 'iso_week', 'partner'])) }}" class="fm-action-btn fm-action-btn--primary">
                  <span class="material-symbols-outlined text-sm">upload</span>
                  Ke Halaman Upload
               </a>
            </div>
         </div>

         <form method="GET" action="{{ route('fatigue-management.dashboard') }}" class="flex flex-wrap items-end gap-3">
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Tahun</label>
               <select name="year" class="fm-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[6rem]">
                  @foreach($filterOptions['years'] ?? [] as $y)
                  <option value="{{ $y }}" @selected((int)($filters['year'] ?? 0) === (int)$y)>{{ $y }}</option>
                  @endforeach
               </select>
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Minggu</label>
               <select name="iso_week" class="fm-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[6rem]">
                  @foreach($filterOptions['weeks'] ?? [] as $w)
                  <option value="{{ $w }}" @selected(($filters['isoWeek'] ?? '') === $w)>{{ $w }}</option>
                  @endforeach
               </select>
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Perusahaan</label>
               <select name="partner" class="fm-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[11rem]">
                  <option value="">Semua Perusahaan</option>
                  @foreach($filterOptions['partners'] ?? [] as $p)
                  <option value="{{ $p['value'] }}" @selected(($filters['partnerKey'] ?? '') === $p['value'])>{{ $p['label'] }}</option>
                  @endforeach
               </select>
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Tipe Program</label>
               <select name="program_type" class="fm-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[11rem]">
                  <option value="">Semua Tipe</option>
                  @foreach($filterOptions['program_types'] ?? [] as $t)
                  <option value="{{ $t['value'] }}" @selected(($filters['programType'] ?? '') === $t['value'])>{{ $t['label'] }}</option>
                  @endforeach
               </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:opacity-95">
               <span class="material-symbols-outlined text-lg">filter_alt</span>
               Terapkan
            </button>
         </form>
      </div>
   </section>

   @include('fatigue-management.partials.site-matrix-summary', [
      'siteMatrix' => $siteMatrix ?? [],
      'companyGroups' => $companyGroups ?? [],
      'summary' => $summary ?? [],
      'isoWeek' => $isoWeek,
      'year' => $year,
   ])

   @if(($frequencyGroups ?? []) !== [])
   <section class="grid grid-cols-1 md:grid-cols-3 gap-3">
      @foreach($frequencyGroups as $fg)
      <div class="fm-mon-card p-4 rounded-2xl fm-freq-summary-card">
         <div class="flex items-start justify-between gap-2">
            <div class="flex items-center gap-2">
               <span class="fm-freq-section-icon fm-freq-section-icon--{{ $fg['key'] }}">
                  <span class="material-symbols-outlined text-lg">{{ $freqIcon($fg['key']) }}</span>
               </span>
               <div>
                  <h3 class="font-headline font-bold text-sm text-on-background">{{ $fg['label'] }}</h3>
                  <p class="text-[10px] text-on-surface-variant mt-0.5">{{ $fg['description'] ?? '' }}</p>
               </div>
            </div>
            <span class="text-lg font-extrabold text-primary tabular-nums">{{ $fg['pct_checklist'] ?? 0 }}%</span>
         </div>
         <div class="mt-3 flex items-center justify-between text-xs font-semibold text-on-surface-variant">
            <span>{{ $fg['checklist_ok'] ?? 0 }}/{{ $fg['total'] ?? 0 }} checklist OK</span>
            <span>{{ $fg['total'] ?? 0 }} program</span>
         </div>
         <div class="fm-type-bar-track mt-2">
            <div class="fm-type-bar-fill fm-type-bar-fill--{{ $fg['key'] }} is-animated" data-target-width="{{ $fg['pct_checklist'] ?? 0 }}"></div>
         </div>
      </div>
      @endforeach
   </section>
   @endif

   <section class="fm-mon-card p-0 overflow-hidden rounded-2xl">
      <div class="px-5 sm:px-6 pt-5 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-outline-variant/10">
         <div>
            <h2 class="font-headline font-semibold text-base text-on-background">Daftar Perusahaan</h2>
            <p class="text-xs text-on-surface-variant mt-0.5">Ringkasan submit & checklist per mitra · {{ $isoWeek }} {{ $year }}</p>
         </div>
         <div class="flex gap-2">
            <button type="button" id="fm-expand-all" class="fm-action-btn fm-action-btn--ghost text-xs">
               <span class="material-symbols-outlined text-sm">unfold_more</span>
               Bentang Semua
            </button>
            <button type="button" id="fm-collapse-all" class="fm-action-btn fm-action-btn--ghost text-xs">
               <span class="material-symbols-outlined text-sm">unfold_less</span>
               Ciutkan Semua
            </button>
         </div>
      </div>

      <div class="fm-company-list">
         @forelse($companyGroups as $group)
         @php
            $isOpen = ($expandedPartner ?? '') !== '' && strtoupper($expandedPartner) === strtoupper($group['partner_key']);
            $sections = $group['frequency_sections'] ?? [];
            $tier = $group['status_tier'] ?? 'warning';
            $pct = $group['pct_checklist'] ?? 0;
         @endphp
         <article class="fm-company-card fm-company-card--{{ $tier }} {{ $isOpen ? 'is-open' : '' }}" data-partner="{{ $group['partner_key'] }}">
            <button type="button" class="fm-company-summary" data-fm-toggle-company aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
               <div class="fm-company-avatar hidden md:flex" aria-hidden="true">{{ $group['partner_key'] }}</div>

               <div class="fm-company-meta">
                  <div class="flex flex-wrap items-start justify-between gap-2">
                     <div>
                        <p class="fm-company-name">{{ $group['partner_name'] }}</p>
                        <div class="fm-company-sub">
                           <span class="fm-tier-badge">
                              <span class="material-symbols-outlined text-[12px]">
                                 @if($tier === 'complete') verified
                                 @elseif($tier === 'good') thumb_up
                                 @elseif($tier === 'warning') schedule
                                 @else warning
                                 @endif
                              </span>
                              {{ $group['status_label'] ?? '—' }}
                           </span>
                           <span>{{ $group['partner_key'] }}</span>
                           <span>·</span>
                           <span>{{ $group['total'] }} program</span>
                        </div>
                     </div>
                     <div class="fm-ring-wrap md:hidden">
                        <svg class="fm-ring" viewBox="0 0 36 36" aria-hidden="true">
                           <path class="fm-ring-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                           <path class="fm-ring-fill" stroke-dasharray="{{ $pct }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="fm-ring-label">
                           <span class="fm-ring-pct">{{ $pct }}%</span>
                        </div>
                     </div>
                  </div>

                  <div class="fm-stat-grid">
                     <div class="fm-stat-box fm-stat-box--submit">
                        <p class="fm-stat-box__value">{{ $group['submitted_count'] ?? 0 }}</p>
                        <p class="fm-stat-box__label">Sudah Submit</p>
                     </div>
                     <div class="fm-stat-box fm-stat-box--pending">
                        <p class="fm-stat-box__value">{{ $group['belum_submit'] ?? 0 }}</p>
                        <p class="fm-stat-box__label">Belum Submit</p>
                     </div>
                     <div class="fm-stat-box fm-stat-box--check">
                        <p class="fm-stat-box__value">{{ $group['checklist_ok'] ?? 0 }}/{{ $group['total'] ?? 0 }}</p>
                        <p class="fm-stat-box__label">Checklist OK</p>
                     </div>
                     <div class="fm-stat-box fm-stat-box--verify">
                        <p class="fm-stat-box__value">{{ $group['verified_count'] ?? 0 }}</p>
                        <p class="fm-stat-box__label">Terverifikasi</p>
                     </div>
                  </div>

                  <div class="fm-type-bars">
                     <div class="fm-type-bar-row">
                        <span class="fm-freq-bar-label">
                           <span class="fm-freq-bar-dot fm-freq-bar-dot--shift"></span>
                           Shift
                        </span>
                        <div class="fm-type-bar-track">
                           <div class="fm-type-bar-fill fm-type-bar-fill--shift is-animated" data-target-width="{{ $group['shift_pct'] ?? 0 }}"></div>
                        </div>
                        <span class="fm-type-bar-count">{{ $group['shift_ok'] ?? 0 }}/{{ $group['shift_total'] ?? 0 }}</span>
                     </div>
                     <div class="fm-type-bar-row">
                        <span class="fm-freq-bar-label">
                           <span class="fm-freq-bar-dot fm-freq-bar-dot--daily"></span>
                           Harian
                        </span>
                        <div class="fm-type-bar-track">
                           <div class="fm-type-bar-fill fm-type-bar-fill--daily is-animated" data-target-width="{{ $group['daily_pct'] ?? 0 }}"></div>
                        </div>
                        <span class="fm-type-bar-count">{{ $group['daily_ok'] ?? 0 }}/{{ $group['daily_total'] ?? 0 }}</span>
                     </div>
                     <div class="fm-type-bar-row">
                        <span class="fm-freq-bar-label">
                           <span class="fm-freq-bar-dot fm-freq-bar-dot--weekly"></span>
                           Mingguan
                        </span>
                        <div class="fm-type-bar-track">
                           <div class="fm-type-bar-fill fm-type-bar-fill--weekly is-animated" data-target-width="{{ $group['weekly_pct'] ?? 0 }}"></div>
                        </div>
                        <span class="fm-type-bar-count">{{ $group['weekly_ok'] ?? 0 }}/{{ $group['weekly_total'] ?? 0 }}</span>
                     </div>
                  </div>

                  <div class="fm-detail-toggle">
                     <span data-fm-toggle-label>{{ $isOpen ? 'Sembunyikan Detail Program' : 'Lihat Detail Program' }}</span>
                     <span class="material-symbols-outlined">expand_more</span>
                  </div>
               </div>

               <div class="fm-ring-wrap hidden md:block">
                  <svg class="fm-ring" viewBox="0 0 36 36" role="img" aria-label="Progress checklist {{ $pct }} persen">
                     <path class="fm-ring-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                     <path class="fm-ring-fill" stroke-dasharray="{{ $pct }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                  </svg>
                  <div class="fm-ring-label">
                     <span class="fm-ring-pct">{{ $pct }}%</span>
                     <span class="fm-ring-caption">Checklist</span>
                  </div>
               </div>
            </button>

            <div class="fm-program-panel-wrap">
            <div class="fm-program-panel-inner">
            <div class="fm-program-panel">
               <div class="fm-detail-header">
                  <div>
                     <h3>Detail Program — {{ $group['partner_name'] }}</h3>
                     <p class="text-[11px] text-on-surface-variant mt-1">Submit {{ $group['pct_submitted'] ?? 0 }}% · Checklist {{ $pct }}% · Disetujui {{ $group['eval_approved'] ?? 0 }} program</p>
                  </div>
                  <div class="fm-detail-chips">
                     <span class="fm-detail-chip">
                        <span class="material-symbols-outlined text-[13px] text-primary">upload_file</span>
                        {{ $group['submitted_count'] ?? 0 }} submit
                     </span>
                     <span class="fm-detail-chip">
                        <span class="material-symbols-outlined text-[13px] text-emerald-600">check_circle</span>
                        {{ $group['checklist_ok'] ?? 0 }} checklist
                     </span>
                     <span class="fm-detail-chip">
                        <span class="material-symbols-outlined text-[13px] text-indigo-600">verified</span>
                        {{ $group['verified_count'] ?? 0 }} verified
                     </span>
                     <a href="{{ route('fatigue-management.upload', ['year' => $year, 'iso_week' => $isoWeek, 'partner' => $group['partner_key']]) }}" class="fm-detail-chip hover:bg-primary/5 text-primary" onclick="event.stopPropagation()">
                        <span class="material-symbols-outlined text-[13px]">add_circle</span>
                        Upload
                     </a>
                  </div>
               </div>

               @foreach($sections as $section)
               @php
                  $sectionItems = $section['rows'] ?? [];
                  $sectionOk = collect($sectionItems)->where('checklist_met', true)->count();
               @endphp
               <div class="fm-program-section">
                  <p class="fm-program-section-title">
                     <span class="inline-flex items-center gap-1.5">
                        <span class="fm-freq-section-icon fm-freq-section-icon--{{ $section['key'] ?? 'weekly' }}">
                           <span class="material-symbols-outlined text-sm">{{ $freqIcon($section['key'] ?? 'weekly') }}</span>
                        </span>
                        {{ $section['label'] ?? '' }}
                     </span>
                     <span class="fm-program-section-count">{{ $sectionOk }}/{{ count($sectionItems) }}</span>
                  </p>
                  @if($section['description'] ?? null)
                  <p class="text-[10px] text-on-surface-variant mb-2 -mt-1">{{ $section['description'] }}</p>
                  @endif

                  @foreach($sectionItems as $prog)
                  @php
                     $slots = $prog['frequency_slots'] ?? [];
                     $isDone = (bool) ($prog['checklist_met'] ?? false);
                  @endphp
                  <div class="fm-program-item {{ $isDone ? 'is-done' : 'is-pending' }}">
                     <div class="fm-program-check-wrap {{ $isDone ? 'is-done' : 'is-pending' }}" aria-hidden="true">
                        <span class="material-symbols-outlined">{{ $isDone ? 'check_box' : 'check_box_outline_blank' }}</span>
                     </div>
                     <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                           <p class="font-semibold text-sm text-on-background leading-snug">{{ $prog['program_title'] }}</p>
                           <div class="flex flex-wrap items-center gap-1.5 shrink-0">
                              <span class="fm-freq-pill">
                                 <span class="material-symbols-outlined text-[13px]">schedule</span>
                                 {{ $prog['frequency_category_label'] ?? $prog['frequency_raw'] ?? $prog['frequency'] ?? '—' }}
                              </span>
                              <span class="fm-type-pill {{ $typeClass($prog['program_type'] ?? null) }}">{{ $prog['program_type_label'] ?? '' }}</span>
                           </div>
                        </div>

                        <div class="fm-freq-slots" title="Slot frekuensi periode {{ $isoWeek }}">
                           @foreach($slots as $slot)
                           <span class="fm-freq-slot {{ ($slot['done'] ?? false) ? 'is-done' : '' }}" title="{{ $slot['label'] }}">
                              @if($slot['done'] ?? false)
                              <span class="material-symbols-outlined text-[11px]">check</span>
                              @else
                              {{ Str::limit($slot['label'], 3, '') }}
                              @endif
                           </span>
                           @endforeach
                        </div>

                        <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px]">
                           <span class="fm-status {{ $checklistClass($prog['checklist_color'] ?? null) }}">{{ $prog['checklist_label'] ?? '—' }}</span>
                           @if($prog['evidence_uploaded_at'] ?? null)
                           <span class="inline-flex items-center gap-1 text-on-surface-variant">
                              <span class="material-symbols-outlined text-[13px]">upload</span>
                              {{ $prog['evidence_uploaded_at'] }}
                           </span>
                           @if($prog['evidence_file_url'] ?? null)
                           <a href="{{ $prog['evidence_file_url'] }}" class="inline-flex items-center gap-1 font-semibold text-primary hover:underline">
                              <span class="material-symbols-outlined text-[13px]">attach_file</span>
                              {{ Str::limit($prog['evidence_original_name'] ?? 'File', 18) }}
                           </a>
                           @endif
                           @else
                           <span class="text-red-600 font-semibold">Belum submit evidence</span>
                           @endif
                           @if($prog['id'] ?? null)
                           <button type="button" class="fm-action-btn fm-action-btn--ghost js-eval-btn" data-row='@json($prog)'>
                              <span class="material-symbols-outlined text-sm">rate_review</span>
                              Evaluasi
                           </button>
                           @endif
                        </div>
                     </div>
                  </div>
                  @endforeach
               </div>
               @endforeach
            </div>
            </div>
            </div>
         </article>
         @empty
         <div class="px-5 py-16 text-center text-on-surface-variant text-sm">
            <span class="material-symbols-outlined text-4xl opacity-30 block mb-2">business</span>
            Tidak ada perusahaan untuk filter ini.
         </div>
         @endforelse
      </div>
   </section>
</div>

<div id="fm-eval-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
   <div class="fm-modal-backdrop absolute inset-0" data-fm-modal-close></div>
   <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">
      <div class="fm-modal-panel fm-mon-card w-full max-w-lg flex flex-col overflow-hidden bg-white shadow-2xl" role="dialog" aria-modal="true">
         <div class="px-5 py-4 border-b border-outline-variant/15 flex items-start justify-between gap-4">
            <div>
               <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Proses Evaluasi GMO</p>
               <h3 id="fm-eval-title" class="font-headline font-bold text-lg text-on-background mt-1">—</h3>
               <p id="fm-eval-subtitle" class="text-xs text-on-surface-variant mt-1">—</p>
            </div>
            <button type="button" class="rounded-xl p-2 text-on-surface-variant hover:bg-surface-container-low" data-fm-modal-close aria-label="Tutup">
               <span class="material-symbols-outlined text-xl">close</span>
            </button>
         </div>
         <form id="fm-eval-form" method="POST" class="px-5 py-4 space-y-4">
            @csrf
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Status Evaluasi *</label>
               <select name="evaluation_status" id="fm-eval-status" required class="w-full fm-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold">
                  @foreach($filterOptions['evaluation_statuses'] ?? [] as $s)
                     @if($s['value'] !== 'menunggu_evidence')
                     <option value="{{ $s['value'] }}">{{ $s['label'] }}</option>
                     @endif
                  @endforeach
               </select>
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Skor (0–100)</label>
               <input type="number" name="evaluation_score" min="0" max="100" class="w-full fm-filter-pill rounded-xl px-3 py-2.5 text-sm" placeholder="Opsional" />
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Evaluator</label>
               <input type="text" name="evaluated_by" class="w-full fm-filter-pill rounded-xl px-3 py-2.5 text-sm" placeholder="Nama evaluator GMO" />
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Catatan Evaluasi</label>
               <textarea name="evaluation_notes" rows="3" class="w-full fm-filter-pill rounded-xl px-3 py-2.5 text-sm" placeholder="Temuan, perbaikan, atau persetujuan"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-outline-variant/15">
               <button type="button" class="fm-action-btn fm-action-btn--ghost" data-fm-modal-close>Batal</button>
               <button type="submit" class="fm-action-btn fm-action-btn--primary px-4" style="background:#047857">
                  <span class="material-symbols-outlined text-sm">check</span>
                  Simpan Evaluasi
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
   function animateProgressBars(root) {
      var scope = root || document;
      scope.querySelectorAll('.fm-type-bar-fill.is-animated[data-target-width]').forEach(function (el, i) {
         var target = parseFloat(el.getAttribute('data-target-width') || '0');
         if (isNaN(target)) target = 0;
         el.style.width = '0%';
         requestAnimationFrame(function () {
            setTimeout(function () {
               el.style.width = Math.min(100, Math.max(0, target)) + '%';
            }, 80 + (i * 40));
         });
      });

      scope.querySelectorAll('.fm-ring-fill').forEach(function (ring) {
         var dash = ring.getAttribute('stroke-dasharray');
         if (!dash) return;
         var parts = dash.split(',');
         var target = parseFloat(parts[0] || '0');
         ring.setAttribute('stroke-dasharray', '0, 100');
         requestAnimationFrame(function () {
            setTimeout(function () {
               ring.setAttribute('stroke-dasharray', target + ', 100');
            }, 120);
         });
      });
   }

   function setCompanyOpen(card, open) {
      card.classList.toggle('is-open', open);
      var btn = card.querySelector('[data-fm-toggle-company]');
      var label = card.querySelector('[data-fm-toggle-label]');
      if (btn) btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (label) label.textContent = open ? 'Sembunyikan Detail Program' : 'Lihat Detail Program';
      if (open) {
         requestAnimationFrame(function () {
            animateProgressBars(card);
         });
      }
   }

   document.querySelectorAll('[data-fm-toggle-company]').forEach(function (btn) {
      btn.addEventListener('click', function () {
         var card = btn.closest('.fm-company-card');
         if (!card) return;
         setCompanyOpen(card, !card.classList.contains('is-open'));
      });
   });

   var expandAll = document.getElementById('fm-expand-all');
   var collapseAll = document.getElementById('fm-collapse-all');
   if (expandAll) {
      expandAll.addEventListener('click', function () {
         document.querySelectorAll('.fm-company-card').forEach(function (c) { setCompanyOpen(c, true); });
      });
   }
   if (collapseAll) {
      collapseAll.addEventListener('click', function () {
         document.querySelectorAll('.fm-company-card').forEach(function (c) { setCompanyOpen(c, false); });
      });
   }

   animateProgressBars(document);

   function openModal(id) {
      var m = document.getElementById(id);
      if (!m) return;
      m.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
   }
   function closeAllModals() {
      document.querySelectorAll('#fm-eval-modal').forEach(function (m) { m.classList.add('hidden'); });
      document.body.style.overflow = '';
   }

   document.querySelectorAll('.js-eval-btn').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
         e.stopPropagation();
         var row = JSON.parse(btn.dataset.row);
         if (!row.id) return;
         document.getElementById('fm-eval-form').action = '/fatigue-management/monitoring/' + row.id + '/evaluation';
         document.getElementById('fm-eval-title').textContent = row.partner_key + ' · ' + row.program_type_label;
         document.getElementById('fm-eval-subtitle').textContent = row.program_title;
         if (row.evaluation_status) document.getElementById('fm-eval-status').value = row.evaluation_status;
         openModal('fm-eval-modal');
      });
   });

   document.querySelectorAll('[data-fm-modal-close]').forEach(function (el) {
      el.addEventListener('click', closeAllModals);
   });
   document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeAllModals();
   });
})();
</script>
@endpush
