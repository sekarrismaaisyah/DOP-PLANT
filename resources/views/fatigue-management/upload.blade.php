@extends('PembatasanLV.layouts.app')

@section('title', 'Upload Evidence Fatigue Management GMO')

@push('head')
@include('fatigue-management.partials.styles')
@endpush

@section('content')
@php
   $doc = $dashboard['document'] ?? [];
   $isoWeek = $filters['isoWeek'] ?? '';
   $year = $filters['year'] ?? date('Y');
   $uploadFrequencyGroups = $uploadFrequencyGroups ?? [];
   $uploadPageContext = $uploadPageContext ?? [];
   $partnerAccess = $partnerAccess ?? ['locked' => false];
   $isPartnerLocked = (bool) ($partnerAccess['locked'] ?? false);

   $typeClass = static function (?string $type): string {
      return match ($type) {
         'mandatory', 'wajib' => 'fm-type-pill--mandatory',
         'upgrade' => 'fm-type-pill--upgrade',
         default => 'fm-type-pill--mitra',
      };
   };
@endphp

<div class="fm-mon -mt-2 space-y-6">
   <section class="pb-5 border-b border-outline-variant/30">
      <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-5">
         <div class="min-w-0">
            <nav class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.08em] text-on-surface-variant mb-2.5">
               <span>Fatigue Management GMO</span>
               <span class="material-symbols-outlined text-[13px] opacity-60">chevron_right</span>
               <span class="text-primary">Upload Evidence</span>
            </nav>
            <h1 class="font-headline font-extrabold text-3xl text-on-background tracking-tight">Inputasi Upload Evidence Mitra</h1>
            <p class="mt-1.5 text-sm text-on-surface-variant">
               {{ $doc['perkuatan_title'] ?? 'Perkuatan Fatigue Management Piala Dunia 2026' }} · {{ $isoWeek }} {{ $year }} · Klasifikasi HO: M / U
            </p>
            <div class="mt-2 flex flex-wrap gap-2 text-[10px] font-bold uppercase tracking-wide">
               <span class="fm-type-pill fm-type-pill--mandatory">M — Wajib</span>
               <span class="fm-type-pill fm-type-pill--upgrade">U — Upgrade</span>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
               @unless($isPartnerLocked)
               <a href="{{ route('fatigue-management.dashboard', request()->only(['year', 'iso_week', 'partner'])) }}" class="fm-action-btn fm-action-btn--ghost">
                  <span class="material-symbols-outlined text-sm">dashboard</span>
                  Ke Dashboard Checklist
               </a>
               @endunless
            </div>
         </div>

         <form method="GET" action="{{ route('fatigue-management.upload') }}" class="flex flex-wrap items-end gap-3">
            @if($isPartnerLocked)
            <input type="hidden" name="partner" value="{{ $partnerAccess['partner_key'] ?? '' }}">
            @endif
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
            @unless($isPartnerLocked)
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Mitra</label>
               <select name="partner" class="fm-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[10rem]">
                  <option value="">Semua Mitra</option>
                  @foreach($filterOptions['partners'] ?? [] as $p)
                  <option value="{{ $p['value'] }}" @selected(($filters['partnerKey'] ?? '') === $p['value'])>{{ $p['label'] }}</option>
                  @endforeach
               </select>
            </div>
            @endunless
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

   @if($isPartnerLocked)
   <div class="rounded-xl border border-primary/15 bg-gradient-to-r from-primary/[0.06] to-white px-4 py-3.5 shadow-sm flex flex-wrap items-center gap-3">
      <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary/10 text-primary">
         <span class="material-symbols-outlined">business</span>
      </span>
      <div>
         <p class="text-[10px] font-bold uppercase tracking-wider text-primary">Akun Mitra</p>
         <p class="text-sm font-bold text-on-background mt-0.5">
            {{ $partnerAccess['partner_name'] ?? $partnerAccess['partner_key'] }}
            <span class="text-on-surface-variant font-semibold">({{ $partnerAccess['partner_key'] }})</span>
         </p>
         <p class="text-xs text-on-surface-variant mt-0.5">Akun terdeteksi dari email/nama — hanya program perusahaan ini yang ditampilkan.</p>
      </div>
   </div>
   @endif

   <div class="rounded-xl border border-primary/10 bg-white px-4 py-3.5 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
         <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-primary">Waktu Server</p>
            <p class="text-sm font-semibold text-on-background mt-0.5">
               {{ $uploadPageContext['now'] ?? '—' }}
               <span class="text-on-surface-variant font-normal text-xs">({{ $uploadPageContext['timezone'] ?? '' }})</span>
            </p>
            <p class="text-xs text-on-surface-variant mt-1">{{ $uploadPageContext['week_relation_label'] ?? '' }}</p>
         </div>
         <div class="flex flex-wrap gap-2">
            @if(($uploadPageContext['active_shift'] ?? null) !== null)
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 border border-amber-100 px-2.5 py-1.5 text-[11px] font-bold text-amber-800">
               <span class="material-symbols-outlined text-sm">schedule</span>
               Aktif: {{ $uploadPageContext['active_shift']['label'] ?? '' }}
               <span class="font-normal opacity-80">({{ $uploadPageContext['active_shift']['time_window'] ?? '' }})</span>
            </span>
            @endif
            @if($uploadPageContext['today_label'] ?? null)
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 border border-emerald-100 px-2.5 py-1.5 text-[11px] font-bold text-emerald-800">
               <span class="material-symbols-outlined text-sm">today</span>
               Hari ini: {{ $uploadPageContext['today_label'] }}
            </span>
            @endif
            <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#eef2ff] border border-indigo-100 px-2.5 py-1.5 text-[11px] font-bold text-primary">
               Shift {{ $uploadPageContext['current_shift_number'] ?? '—' }}
            </span>
         </div>
      </div>
   </div>

   <div class="rounded-xl border border-primary/10 bg-primary/[0.04] px-4 py-3 text-sm text-on-surface-variant">
      <p class="font-semibold text-on-background mb-1">Aturan slot upload</p>
      <ul class="list-disc pl-5 space-y-0.5 text-xs">
         <li><strong>Shift 1</strong> — aktif <strong>06:00–18:00</strong> · <strong>Shift 2</strong> — aktif <strong>18:00–06:00</strong> (melewati tengah malam)</li>
         <li><strong>Harian</strong> — hanya slot <strong>hari ini</strong> yang muncul & bisa diupload</li>
         <li><strong>Mingguan</strong> — bebas <strong>Sen–Min</strong> dalam minggu {{ $isoWeek }} (semua slot terbuka)</li>
      </ul>
   </div>

   @forelse($uploadFrequencyGroups as $group)
   <section class="fm-mon-card overflow-hidden" id="fm-freq-{{ $group['key'] }}">
      <div class="px-5 py-4 border-b border-outline-variant/15 bg-[#fafbfc]">
         <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
               <h2 class="font-headline font-bold text-lg text-on-background flex items-center gap-2">
                  <span class="fm-freq-section-icon fm-freq-section-icon--{{ $group['key'] }}">
                     <span class="material-symbols-outlined text-lg">
                        @if($group['key'] === 'shift') schedule
                        @elseif($group['key'] === 'daily') today
                        @else date_range
                        @endif
                     </span>
                  </span>
                  {{ $group['label'] }}
               </h2>
               <p class="text-xs text-on-surface-variant mt-0.5">{{ $group['description'] ?? '' }}</p>
            </div>
            <span class="text-xs font-bold text-primary bg-primary/10 px-2.5 py-1 rounded-lg">
               {{ count($group['rows'] ?? []) }} program
            </span>
         </div>
      </div>

      <div class="overflow-x-auto">
         <table class="w-full text-sm fm-upload-table">
            <thead class="bg-surface-container-low/60 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">
               <tr>
                  <th class="px-4 py-3 text-left w-12">No</th>
                  <th class="px-4 py-3 text-left">Tipe</th>
                  <th class="px-4 py-3 text-left min-w-[200px]">Program</th>
                  <th class="px-4 py-3 text-left">Mitra</th>
                  <th class="px-4 py-3 text-left">Frekuensi</th>
                  <th class="px-4 py-3 text-left">Progress</th>
                  <th class="px-4 py-3 text-left min-w-[280px]">Slot Upload</th>
               </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
               @foreach($group['rows'] ?? [] as $row)
               <tr class="hover:bg-primary/[0.02] align-top">
                  <td class="px-4 py-3 font-bold text-primary tabular-nums">{{ $row['program_no'] }}</td>
                  <td class="px-4 py-3">
                     <span class="fm-type-pill {{ $typeClass($row['program_type'] ?? null) }}">{{ $row['program_type_label'] ?? '—' }}</span>
                  </td>
                  <td class="px-4 py-3 max-w-xs">
                     <p class="font-semibold text-on-background">{{ $row['program_title'] }}</p>
                     @if($row['implementation_indicator'] ?? null)
                     <p class="text-[11px] text-on-surface-variant mt-1 line-clamp-2">{{ $row['implementation_indicator'] }}</p>
                     @endif
                  </td>
                  <td class="px-4 py-3">
                     <p class="font-semibold">{{ $row['partner_key'] }}</p>
                     <p class="text-[11px] text-on-surface-variant">{{ Str::limit($row['partner_name'] ?? '', 24) }}</p>
                  </td>
                  <td class="px-4 py-3 text-xs whitespace-nowrap">
                     <span class="font-semibold text-on-background">{{ $row['frequency_category_label'] ?? '' }}</span>
                     <p class="text-on-surface-variant mt-0.5">{{ $row['frequency_raw'] ?? '' }}</p>
                  </td>
                  <td class="px-4 py-3">
                     <span class="fm-status fm-status--{{ ($row['checklist_met'] ?? false) ? 'green' : (($row['slots_done'] ?? 0) > 0 ? 'blue' : 'gray') }}">
                        {{ $row['slots_progress_label'] ?? '0/0' }}
                     </span>
                     <p class="text-[10px] text-on-surface-variant mt-1">{{ $row['checklist_label'] ?? '' }}</p>
                  </td>
                  <td class="px-4 py-3">
                     @php
                        $visibleSlots = $row['upload_slot_states'] ?? array_filter(
                           $row['slot_states'] ?? [],
                           static fn (array $s): bool => (bool) ($s['is_visible'] ?? false),
                        );
                     @endphp
                     @if($visibleSlots === [])
                     <p class="text-[11px] text-on-surface-variant italic">
                        @if(($uploadPageContext['week_relation'] ?? '') === 'future')
                           Minggu belum dimulai
                        @elseif($group['key'] === 'shift')
                           Tidak ada shift aktif saat ini — Shift 1: 06:00–18:00, Shift 2: 18:00–06:00
                        @elseif($group['key'] === 'daily')
                           Slot harian hari ini belum tersedia atau sudah lengkap
                        @else
                           Semua slot sudah terisi
                        @endif
                     </p>
                     @else
                     <div class="fm-slot-grid fm-slot-grid--{{ $group['key'] }}">
                        @foreach($visibleSlots as $slot)
                        @php
                           $isDone = (bool) ($slot['done'] ?? false);
                           $isUploadable = (bool) ($slot['is_uploadable'] ?? false);
                           $isActive = (bool) ($slot['is_active'] ?? false);
                           $btnClass = $isDone
                              ? 'fm-slot-btn--done'
                              : ($isUploadable ? 'fm-slot-btn--active' : 'fm-slot-btn--locked');
                        @endphp
                        @if($isUploadable || $isDone)
                        <button
                           type="button"
                           class="fm-slot-btn {{ $btnClass }} js-upload-slot-btn"
                           data-row='@json($row)'
                           data-slot-key="{{ $slot['key'] }}"
                           data-slot-label="{{ $slot['label'] }}"
                           title="{{ $isDone ? 'Ganti: ' : 'Upload: ' }}{{ $slot['label'] }}@if($slot['time_window'] ?? '') ({{ $slot['time_window'] }})@endif"
                        >
                           <span class="fm-slot-btn__label">{{ $slot['label'] }}</span>
                           @if($slot['time_window'] ?? '')
                           <span class="fm-slot-btn__window">{{ $slot['time_window'] }}</span>
                           @endif
                           @if($isDone)
                           <span class="material-symbols-outlined fm-slot-btn__icon">check_circle</span>
                           @if($slot['evidence_file_url'] ?? null)
                           <a href="{{ $slot['evidence_file_url'] }}" class="fm-slot-btn__file" onclick="event.stopPropagation()" title="Download">
                              <span class="material-symbols-outlined text-[14px]">attach_file</span>
                           </a>
                           @endif
                           @elseif($isActive)
                           <span class="material-symbols-outlined fm-slot-btn__icon">upload</span>
                           @endif
                        </button>
                        @else
                        <span class="fm-slot-btn fm-slot-btn--locked" title="{{ $slot['hint'] ?? '' }}">
                           <span class="fm-slot-btn__label">{{ $slot['label'] }}</span>
                           <span class="fm-slot-btn__window">{{ $slot['time_window'] ?? '' }}</span>
                           <span class="material-symbols-outlined fm-slot-btn__icon">lock</span>
                        </span>
                        @endif
                        @endforeach
                     </div>
                     @endif
                  </td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
   </section>
   @empty
   <section class="fm-mon-card p-12 text-center text-on-surface-variant">
      <span class="material-symbols-outlined text-4xl text-primary/25 mb-2">inbox</span>
      <p class="font-semibold">Tidak ada program untuk filter ini.</p>
   </section>
   @endforelse
</div>

<div id="fm-upload-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
   <div class="fm-modal-backdrop absolute inset-0" data-fm-modal-close></div>
   <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">
      <div class="fm-modal-panel fm-mon-card w-full max-w-lg flex flex-col overflow-hidden bg-white shadow-2xl" role="dialog" aria-modal="true">
         <div class="px-5 py-4 border-b border-outline-variant/15 flex items-start justify-between gap-4">
            <div>
               <p class="text-[10px] font-bold uppercase tracking-wider text-primary">Upload Evidence</p>
               <h3 id="fm-upload-title" class="font-headline font-bold text-lg text-on-background mt-1">—</h3>
               <p id="fm-upload-subtitle" class="text-xs text-on-surface-variant mt-1">—</p>
               <p id="fm-upload-slot-label" class="mt-2 inline-flex items-center gap-1 rounded-lg bg-primary/10 px-2 py-1 text-[11px] font-bold text-primary">—</p>
            </div>
            <button type="button" class="rounded-xl p-2 text-on-surface-variant hover:bg-surface-container-low" data-fm-modal-close aria-label="Tutup">
               <span class="material-symbols-outlined text-xl">close</span>
            </button>
         </div>
         <form id="fm-upload-form" method="POST" action="{{ route('fatigue-management.monitoring.evidence.store') }}" enctype="multipart/form-data" class="px-5 py-4 space-y-4">
            @csrf
            <input type="hidden" name="program_key" id="fm-upload-program-key" />
            <input type="hidden" name="partner_key" id="fm-upload-partner-key" />
            <input type="hidden" name="frequency_slot" id="fm-upload-frequency-slot" />
            <input type="hidden" name="year" value="{{ $year }}" />
            <input type="hidden" name="iso_week" value="{{ $isoWeek }}" />
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">File Evidence *</label>
               <input type="file" name="evidence_file" required accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls,.doc,.docx,.zip" class="w-full text-sm fm-filter-pill rounded-xl px-3 py-2.5" />
               <p class="mt-1 text-[11px] text-on-surface-variant">PDF, gambar, Excel, Word, ZIP — maks. 10 MB</p>
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">PIC Upload</label>
               <input type="text" name="pic_name" class="w-full fm-filter-pill rounded-xl px-3 py-2.5 text-sm" placeholder="Nama pengupload" />
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Catatan</label>
               <textarea name="evidence_notes" rows="2" class="w-full fm-filter-pill rounded-xl px-3 py-2.5 text-sm" placeholder="Keterangan evidence"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-outline-variant/15">
               <button type="button" class="fm-action-btn fm-action-btn--ghost" data-fm-modal-close>Batal</button>
               <button type="submit" class="fm-action-btn fm-action-btn--primary px-4">
                  <span class="material-symbols-outlined text-sm">save</span>
                  Simpan Evidence
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
   function openModal() {
      var m = document.getElementById('fm-upload-modal');
      if (!m) return;
      m.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
   }
   function closeAllModals() {
      var m = document.getElementById('fm-upload-modal');
      if (m) m.classList.add('hidden');
      document.body.style.overflow = '';
      var form = document.getElementById('fm-upload-form');
      if (form) form.reset();
   }

   document.querySelectorAll('.js-upload-slot-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
         var row = JSON.parse(btn.dataset.row);
         var slotKey = btn.dataset.slotKey;
         var slotLabel = btn.dataset.slotLabel;
         document.getElementById('fm-upload-program-key').value = row.program_key;
         document.getElementById('fm-upload-partner-key').value = row.partner_key;
         document.getElementById('fm-upload-frequency-slot').value = slotKey;
         document.getElementById('fm-upload-title').textContent = row.partner_key + ' · ' + (row.program_type_label || '');
         document.getElementById('fm-upload-subtitle').textContent = row.program_title;
         document.getElementById('fm-upload-slot-label').textContent = 'Slot: ' + slotLabel;
         openModal();
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
