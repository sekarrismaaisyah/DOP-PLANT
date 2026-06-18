@extends('PembatasanLV.layouts.app')

@section('title', 'Monitoring Program Fatigue Management GMO')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<style>
   .fm-mon { --fm-ease: cubic-bezier(0.4, 0, 0.2, 1); }
   .fm-mon-card {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(57, 82, 188, 0.07);
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04), 0 8px 24px -6px rgba(57, 82, 188, 0.08);
      border-radius: 1rem;
   }
   .fm-mon-kpi { position: relative; overflow: hidden; }
   .fm-mon-kpi::after {
      content: '';
      position: absolute;
      right: -1rem;
      top: -1rem;
      width: 5rem;
      height: 5rem;
      border-radius: 9999px;
      opacity: 0.08;
      background: currentColor;
   }
   .fm-status {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      border-radius: 9999px;
      padding: 0.25rem 0.65rem;
      font-size: 0.6875rem;
      font-weight: 700;
      letter-spacing: 0.02em;
      white-space: nowrap;
   }
   .fm-status--gray { background: #f1f5f9; color: #64748b; }
   .fm-status--blue { background: #eef2ff; color: #3952bc; }
   .fm-status--amber { background: #fff7ed; color: #c2410c; }
   .fm-status--green { background: #ecfdf5; color: #047857; }
   .fm-status--indigo { background: #e0e7ff; color: #4338ca; }
   .fm-status--red { background: #fef2f2; color: #b91c1c; }
   .fm-source-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      border-radius: 0.5rem;
      padding: 0.2rem 0.55rem;
      font-size: 0.625rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
   }
   .fm-source-pill--evidence { background: #eef2ff; color: #3952bc; }
   .fm-source-pill--eval { background: #ecfdf5; color: #047857; }
   .fm-filter-pill {
      background: #ffffff;
      border: 1px solid rgba(171, 173, 175, 0.28);
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04);
   }
   .fm-action-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      border-radius: 0.625rem;
      padding: 0.4rem 0.65rem;
      font-size: 0.6875rem;
      font-weight: 700;
      transition: opacity 0.2s var(--fm-ease);
   }
   .fm-action-btn:hover { opacity: 0.92; }
   .fm-action-btn--primary { background: #3952bc; color: #fff; }
   .fm-action-btn--ghost {
      background: #fff;
      border: 1px solid rgba(171, 173, 175, 0.35);
      color: #2c2f31;
   }
   .fm-modal-backdrop {
      background: rgba(15, 23, 42, 0.45);
      backdrop-filter: blur(4px);
   }
   .fm-modal-panel { max-height: min(85vh, 640px); }
</style>
@endpush

@section('content')
@php
   $doc = $dashboard['document'] ?? [];
   $isoWeek = $filters['isoWeek'] ?? '';
   $year = $filters['year'] ?? date('Y');
   $partnerLabel = ($filters['partnerKey'] ?? '') !== '' ? $filters['partnerKey'] : 'Semua Mitra';
   $kpis = [
      ['label' => 'Total Item Monitoring', 'value' => $summary['total_items'] ?? 0, 'hint' => 'Program standar × mitra', 'color' => 'text-primary', 'icon' => 'grid_view'],
      ['label' => 'Sudah Upload Evidence', 'value' => ($summary['pct_uploaded'] ?? 0).'%', 'hint' => ($summary['evidence_uploaded'] ?? 0).' dari '.($summary['total_items'] ?? 0).' item', 'color' => 'text-emerald-600', 'icon' => 'upload_file'],
      ['label' => 'Belum Upload', 'value' => $summary['evidence_belum'] ?? 0, 'hint' => 'Perlu upload evidence mitra', 'color' => 'text-red-600', 'icon' => 'pending_actions'],
      ['label' => 'Menunggu Review', 'value' => $summary['menunggu_review'] ?? 0, 'hint' => 'Evidence siap dievaluasi GMO', 'color' => 'text-secondary', 'icon' => 'rate_review'],
      ['label' => 'Disetujui', 'value' => $summary['disetujui'] ?? 0, 'hint' => 'Evaluasi program disetujui', 'color' => 'text-emerald-600', 'icon' => 'check_circle'],
      ['label' => 'Terverifikasi', 'value' => ($summary['pct_verified'] ?? 0).'%', 'hint' => ($summary['evidence_verified'] ?? 0).' evidence terverifikasi', 'color' => 'text-primary', 'icon' => 'verified'],
   ];
   $evidenceStatusClass = static function (?string $status): string {
      return match ($status) {
         'sudah_upload' => 'fm-status--blue',
         'terverifikasi' => 'fm-status--green',
         'perlu_lengkap' => 'fm-status--amber',
         default => 'fm-status--gray',
      };
   };
   $evalStatusClass = static function (?string $status): string {
      return match ($status) {
         'menunggu_review' => 'fm-status--blue',
         'dalam_evaluasi' => 'fm-status--indigo',
         'perlu_perbaikan' => 'fm-status--amber',
         'disetujui' => 'fm-status--green',
         'ditolak' => 'fm-status--red',
         default => 'fm-status--gray',
      };
   };
@endphp

<div class="fm-mon -mt-2 space-y-7">
   <section class="pb-6 border-b border-outline-variant/30">
      <div class="flex flex-col xl:flex-row xl:items-end xl:justify-between gap-5">
         <div class="min-w-0">
            <nav class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.08em] text-on-surface-variant mb-2.5" aria-label="Breadcrumb">
               <span>Fatigue Management GMO</span>
               <span class="material-symbols-outlined text-[13px] opacity-60">chevron_right</span>
               <span class="text-primary">Monitoring & Evaluasi Program</span>
            </nav>
            <h1 class="font-headline font-extrabold text-3xl text-on-background tracking-tight">Monitoring Pelaksanaan Program per Mitra</h1>
            <p class="mt-1.5 text-sm text-on-surface-variant">
               {{ $doc['code'] ?? 'FMP-STD-001' }} · {{ $isoWeek }} {{ $year }} · Upload evidence & proses evaluasi per standar site GMO
            </p>
            <div class="mt-3 flex flex-wrap gap-2">
               <span class="fm-source-pill fm-source-pill--evidence"><span class="material-symbols-outlined text-sm">upload_file</span> Evidence</span>
               <span class="fm-source-pill fm-source-pill--eval"><span class="material-symbols-outlined text-sm">fact_check</span> Evaluasi GMO</span>
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
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Mitra</label>
               <select name="partner" class="fm-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[10rem]">
                  <option value="">Semua Mitra</option>
                  @foreach($filterOptions['partners'] ?? [] as $p)
                  <option value="{{ $p['value'] }}" @selected(($filters['partnerKey'] ?? '') === $p['value'])>{{ $p['label'] }}</option>
                  @endforeach
               </select>
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Program</label>
               <select name="program" class="fm-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[12rem] max-w-[14rem]">
                  <option value="">Semua Program</option>
                  @foreach($filterOptions['programs'] ?? [] as $p)
                  <option value="{{ $p['value'] }}" @selected(($filters['programKey'] ?? '') === $p['value'])>{{ Str::limit($p['label'], 42) }}</option>
                  @endforeach
               </select>
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Evidence</label>
               <select name="evidence_status" class="fm-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[9rem]">
                  <option value="">Semua</option>
                  @foreach($filterOptions['evidence_statuses'] ?? [] as $s)
                  <option value="{{ $s['value'] }}" @selected(($filters['evidenceStatus'] ?? '') === $s['value'])>{{ $s['label'] }}</option>
                  @endforeach
               </select>
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Evaluasi</label>
               <select name="evaluation_status" class="fm-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[9rem]">
                  <option value="">Semua</option>
                  @foreach($filterOptions['evaluation_statuses'] ?? [] as $s)
                  <option value="{{ $s['value'] }}" @selected(($filters['evaluationStatus'] ?? '') === $s['value'])>{{ $s['label'] }}</option>
                  @endforeach
               </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:opacity-95">
               <span class="material-symbols-outlined text-lg">filter_alt</span>
               Terapkan
            </button>
            @if(($filters['partnerKey'] ?? '') || ($filters['programKey'] ?? '') || ($filters['evidenceStatus'] ?? '') || ($filters['evaluationStatus'] ?? ''))
            <a href="{{ route('fatigue-management.dashboard', ['year' => $year, 'iso_week' => $isoWeek]) }}" class="fm-filter-pill inline-flex items-center justify-center rounded-xl px-3 py-2.5" title="Reset filter">
               <span class="material-symbols-outlined text-xl text-on-surface-variant">restart_alt</span>
            </a>
            @endif
         </form>
      </div>
   </section>

   <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6 gap-4">
      @foreach($kpis as $kpi)
      <div class="fm-mon-card fm-mon-kpi p-5 {{ $kpi['color'] }}">
         <div class="flex items-start justify-between gap-3 relative z-10">
            <div>
               <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">{{ $kpi['label'] }}</p>
               <p class="mt-2 font-headline font-bold text-3xl tabular-nums text-on-background leading-none">{{ is_numeric($kpi['value']) ? number_format($kpi['value']) : $kpi['value'] }}</p>
               <p class="mt-2 text-xs text-on-surface-variant">{{ $kpi['hint'] }}</p>
            </div>
            <span class="material-symbols-outlined text-3xl opacity-70">{{ $kpi['icon'] }}</span>
         </div>
      </div>
      @endforeach
   </section>

   <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
      <div class="fm-mon-card p-5">
         <h2 class="font-headline font-bold text-base text-on-background">% Upload Evidence per Mitra</h2>
         <p class="text-xs text-on-surface-variant mt-0.5 mb-3">{{ $isoWeek }} {{ $year }}</p>
         <div id="fm-chart-upload" class="h-64"></div>
      </div>
      <div class="fm-mon-card p-5">
         <h2 class="font-headline font-bold text-base text-on-background">Distribusi Status Evaluasi</h2>
         <p class="text-xs text-on-surface-variant mt-0.5 mb-3">Proses review GMO</p>
         <div id="fm-chart-eval" class="h-64"></div>
      </div>
   </section>

   <section class="fm-mon-card overflow-hidden">
      <div class="px-5 py-4 border-b border-outline-variant/15 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
         <div>
            <h2 class="font-headline font-bold text-lg text-on-background">Matriks Monitoring Program</h2>
            <p class="text-xs text-on-surface-variant mt-0.5">{{ $isoWeek }} {{ $year }} · {{ $partnerLabel }} · Upload evidence & evaluasi per standar</p>
         </div>
         <div class="flex flex-wrap gap-2 text-xs">
            <span class="fm-status fm-status--gray">Belum Upload</span>
            <span class="fm-status fm-status--blue">Sudah Upload</span>
            <span class="fm-status fm-status--green">Terverifikasi</span>
            <span class="fm-status fm-status--amber">Perlu Perbaikan</span>
         </div>
      </div>
      <div class="overflow-x-auto">
         <table class="w-full text-sm">
            <thead class="bg-surface-container-low/60 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">
               <tr>
                  <th class="px-4 py-3 text-left">No</th>
                  <th class="px-4 py-3 text-left min-w-[220px]">Program / Standar</th>
                  <th class="px-4 py-3 text-left">Pilar HO</th>
                  <th class="px-4 py-3 text-left">Mitra</th>
                  <th class="px-4 py-3 text-left">Evidence</th>
                  <th class="px-4 py-3 text-left">Upload</th>
                  <th class="px-4 py-3 text-left">Evaluasi</th>
                  <th class="px-4 py-3 text-center">Skor</th>
                  <th class="px-4 py-3 text-left">Aksi</th>
               </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
               @forelse($rows as $row)
               <tr class="hover:bg-primary/[0.02] transition-colors align-top">
                  <td class="px-4 py-3 font-bold text-primary tabular-nums">{{ $row['program_no'] }}</td>
                  <td class="px-4 py-3 max-w-xs">
                     <p class="font-semibold text-on-background">{{ $row['program_title'] }}</p>
                     <p class="text-[11px] text-on-surface-variant mt-1 line-clamp-2" title="{{ $row['evidence_requirement'] ?? '' }}">{{ $row['evidence_requirement'] ?? '' }}</p>
                  </td>
                  <td class="px-4 py-3 text-xs text-on-surface-variant whitespace-nowrap">{{ $row['program_pillar'] }}</td>
                  <td class="px-4 py-3">
                     <p class="font-semibold text-on-background">{{ $row['partner_key'] }}</p>
                     <p class="text-[11px] text-on-surface-variant">{{ Str::limit($row['partner_name'] ?? '', 24) }}</p>
                  </td>
                  <td class="px-4 py-3">
                     <span class="fm-status {{ $evidenceStatusClass($row['evidence_status'] ?? null) }}">{{ $row['evidence_status_label'] ?? '—' }}</span>
                     @if($row['evidence_file_url'] ?? null)
                     <a href="{{ $row['evidence_file_url'] }}" class="mt-1.5 flex items-center gap-1 text-[11px] font-semibold text-primary hover:underline">
                        <span class="material-symbols-outlined text-sm">attach_file</span>
                        {{ Str::limit($row['evidence_original_name'] ?? 'Download', 22) }}
                     </a>
                     @endif
                  </td>
                  <td class="px-4 py-3 text-xs text-on-surface-variant">
                     @if($row['evidence_uploaded_at'] ?? null)
                        <p>{{ $row['evidence_uploaded_at'] }}</p>
                        @if($row['pic_name'] ?? null)<p class="mt-0.5">PIC: {{ $row['pic_name'] }}</p>@endif
                     @else
                        <span class="text-red-600 font-semibold">Belum ada</span>
                     @endif
                  </td>
                  <td class="px-4 py-3">
                     <span class="fm-status {{ $evalStatusClass($row['evaluation_status'] ?? null) }}">{{ $row['evaluation_status_label'] ?? '—' }}</span>
                     @if($row['evaluated_at'] ?? null)
                     <p class="text-[11px] text-on-surface-variant mt-1">{{ $row['evaluated_at'] }}</p>
                     @if($row['evaluated_by'] ?? null)<p class="text-[11px] text-on-surface-variant">oleh {{ $row['evaluated_by'] }}</p>@endif
                     @endif
                  </td>
                  <td class="px-4 py-3 text-center">
                     <span class="inline-flex min-w-[2rem] justify-center rounded-lg bg-primary/5 px-2 py-1 font-bold tabular-nums text-primary">
                        {{ isset($row['evaluation_score']) ? $row['evaluation_score'] : '—' }}
                     </span>
                  </td>
                  <td class="px-4 py-3">
                     <div class="flex flex-col gap-1.5">
                        <button type="button" class="fm-action-btn fm-action-btn--primary js-upload-btn" data-row='@json($row)'>
                           <span class="material-symbols-outlined text-sm">upload</span>
                           {{ ($row['evidence_status'] ?? '') === 'belum_upload' ? 'Upload' : 'Ganti' }}
                        </button>
                        @if($row['id'] ?? null)
                        <button type="button" class="fm-action-btn fm-action-btn--ghost js-eval-btn" data-row='@json($row)'>
                           <span class="material-symbols-outlined text-sm">rate_review</span>
                           Evaluasi
                        </button>
                        @endif
                     </div>
                  </td>
               </tr>
               @empty
               <tr>
                  <td colspan="9" class="px-4 py-12 text-center text-on-surface-variant">
                     <span class="material-symbols-outlined text-4xl opacity-30 block mb-2">inventory_2</span>
                     Tidak ada data monitoring untuk filter ini.
                  </td>
               </tr>
               @endforelse
            </tbody>
         </table>
      </div>
   </section>
</div>

{{-- Modal Upload --}}
<div id="fm-upload-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
   <div class="fm-modal-backdrop absolute inset-0" data-fm-modal-close></div>
   <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">
      <div class="fm-modal-panel fm-mon-card w-full max-w-lg flex flex-col overflow-hidden bg-white shadow-2xl" role="dialog" aria-modal="true">
         <div class="px-5 py-4 border-b border-outline-variant/15 flex items-start justify-between gap-4">
            <div>
               <p class="text-[10px] font-bold uppercase tracking-wider text-primary">Upload Evidence</p>
               <h3 id="fm-upload-title" class="font-headline font-bold text-lg text-on-background mt-1">—</h3>
               <p id="fm-upload-subtitle" class="text-xs text-on-surface-variant mt-1">—</p>
            </div>
            <button type="button" class="rounded-xl p-2 text-on-surface-variant hover:bg-surface-container-low" data-fm-modal-close aria-label="Tutup">
               <span class="material-symbols-outlined text-xl">close</span>
            </button>
         </div>
         <form id="fm-upload-form" method="POST" action="{{ route('fatigue-management.monitoring.evidence.store') }}" enctype="multipart/form-data" class="px-5 py-4 space-y-4">
            @csrf
            <input type="hidden" name="program_key" id="fm-upload-program-key" />
            <input type="hidden" name="partner_key" id="fm-upload-partner-key" />
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

{{-- Modal Evaluasi --}}
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
               <button type="submit" class="fm-action-btn fm-action-btn--primary px-4 bg-emerald-600" style="background:#047857">
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
   var chartData = @json($chart);

   function openModal(id) {
      var m = document.getElementById(id);
      if (!m) return;
      m.classList.remove('hidden');
      m.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
   }
   function closeAllModals() {
      document.querySelectorAll('#fm-upload-modal, #fm-eval-modal').forEach(function (m) {
         m.classList.add('hidden');
         m.setAttribute('aria-hidden', 'true');
      });
      document.body.style.overflow = '';
   }

   document.querySelectorAll('.js-upload-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
         var row = JSON.parse(btn.dataset.row);
         document.getElementById('fm-upload-program-key').value = row.program_key;
         document.getElementById('fm-upload-partner-key').value = row.partner_key;
         document.getElementById('fm-upload-title').textContent = 'Std ' + row.program_no + ' · ' + row.partner_key;
         document.getElementById('fm-upload-subtitle').textContent = row.program_title;
         openModal('fm-upload-modal');
      });
   });

   document.querySelectorAll('.js-eval-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
         var row = JSON.parse(btn.dataset.row);
         if (!row.id) return;
         document.getElementById('fm-eval-form').action = '/fatigue-management/monitoring/' + row.id + '/evaluation';
         document.getElementById('fm-eval-title').textContent = 'Std ' + row.program_no + ' · ' + row.partner_key;
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

   if (window.echarts) {
      var uploadEl = document.getElementById('fm-chart-upload');
      if (uploadEl) {
         var c1 = echarts.init(uploadEl);
         c1.setOption({
            grid: { left: 40, right: 12, top: 12, bottom: 56 },
            tooltip: { trigger: 'axis' },
            xAxis: { type: 'category', data: chartData.partner_labels || [], axisLabel: { rotate: 30, fontSize: 10 } },
            yAxis: { type: 'value', max: 100, axisLabel: { formatter: '{value}%' } },
            series: [{ type: 'bar', data: chartData.partner_upload_pct || [], itemStyle: { color: '#3952bc', borderRadius: [4,4,0,0] }, barMaxWidth: 32 }],
         });
         window.addEventListener('resize', function () { c1.resize(); });
      }

      var evalEl = document.getElementById('fm-chart-eval');
      if (evalEl) {
         var counts = chartData.evaluation_counts || {};
         var labels = {
            menunggu_evidence: 'Menunggu Evidence',
            menunggu_review: 'Menunggu Review',
            dalam_evaluasi: 'Dalam Evaluasi',
            perlu_perbaikan: 'Perlu Perbaikan',
            disetujui: 'Disetujui',
            ditolak: 'Ditolak'
         };
         var colors = ['#94a3b8','#3952bc','#6366f1','#c2410c','#047857','#b91c1c'];
         var c2 = echarts.init(evalEl);
         c2.setOption({
            tooltip: { trigger: 'item' },
            legend: { bottom: 0, type: 'scroll' },
            series: [{
               type: 'pie',
               radius: ['42%', '65%'],
               center: ['50%', '44%'],
               data: Object.keys(counts).map(function (k, i) {
                  return { name: labels[k] || k, value: counts[k], itemStyle: { color: colors[i % colors.length] } };
               }),
               label: { fontSize: 10 },
            }],
         });
         window.addEventListener('resize', function () { c2.resize(); });
      }
   }
})();
</script>
@endpush
