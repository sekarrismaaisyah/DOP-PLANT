@extends('PembatasanLV.layouts.app')

@section('title', 'Evaluasi Plan vs Aktual — Pembatasan LV')

@push('head')
<style>
   .plv-eval { --plv-ease: cubic-bezier(0.4, 0, 0.2, 1); }
   .plv-eval-card {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(57, 82, 188, 0.07);
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04), 0 8px 24px -6px rgba(57, 82, 188, 0.08);
      border-radius: 1rem;
   }
   .plv-eval-kpi { position: relative; overflow: hidden; }
   .plv-eval-kpi::after {
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
   .plv-status {
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
   .plv-status--tercatat { background: #ecfdf5; color: #047857; }
   .plv-status--belum { background: #fff7ed; color: #c2410c; }
   .plv-status--deviasi { background: #fef2f2; color: #b91c1c; }
   .plv-status--tidak { background: #f1f5f9; color: #64748b; }
   .plv-source-pill {
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
   .plv-source-pill--plan { background: #eef2ff; color: #3952bc; }
   .plv-source-pill--aktual { background: #ecfdf5; color: #047857; }
   .plv-source-pill--sap { background: #fef2f2; color: #b91c1c; }
   .plv-tab-btn[aria-selected="true"] {
      background: #fff;
      color: #3952bc;
      box-shadow: 0 1px 3px rgba(57, 82, 188, 0.1);
   }
   .plv-filter-pill {
      background: #ffffff;
      border: 1px solid rgba(171, 173, 175, 0.28);
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04);
   }
   .plv-eval-row {
      cursor: pointer;
      transition: background 0.2s ease;
   }
   .plv-eval-row:hover { background: rgba(57, 82, 188, 0.04); }
   .plv-eval-row:focus-visible { outline: 2px solid rgba(57, 82, 188, 0.35); outline-offset: -2px; }
   .plv-eval-modal-backdrop {
      background: rgba(15, 23, 42, 0.45);
      backdrop-filter: blur(4px);
   }
   .plv-eval-modal-panel {
      max-height: min(85vh, 720px);
   }
</style>
@endpush

@section('content')
@php
   $tanggalLabel = $filters['tanggal_label'] ?? '';
   $shiftLabel = $filters['shift'] !== '' ? $filters['shift'] : 'Semua Shift';
   $perusahaanLabel = $filters['perusahaan'] !== '' ? $filters['perusahaan'] : 'Semua Perusahaan';
   $kpis = [
      ['label' => 'Aktivitas Plan', 'value' => $summary['total_plan'], 'hint' => 'Register aktivitas GMO', 'color' => 'text-primary', 'icon' => 'assignment'],
      ['label' => 'Logbook Aktual', 'value' => $summary['total_aktual'], 'hint' => 'Pencatatan pada tanggal ini', 'color' => 'text-emerald-600', 'icon' => 'menu_book'],
      ['label' => 'Laporan SAP', 'value' => $summary['total_sap'], 'hint' => 'Deviasi hazard tanggal ini', 'color' => 'text-red-600', 'icon' => 'report'],
      ['label' => 'Tingkat Pencatatan', 'value' => $summary['tingkat_pencatatan'].'%', 'hint' => $summary['aktivitas_tercatat'].' tercatat · '.$summary['aktivitas_belum_tercatat'].' belum', 'color' => 'text-secondary', 'icon' => 'percent'],
   ];
@endphp

<div class="plv-eval -mt-2 space-y-7">
   <section class="pb-6 border-b border-outline-variant/30">
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
         <div class="min-w-0">
            <nav class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.08em] text-on-surface-variant mb-2.5" aria-label="Breadcrumb">
               <span>Pembatasan LV</span>
               <span class="material-symbols-outlined text-[13px] opacity-60">chevron_right</span>
               <span class="text-primary">Evaluasi Plan vs Aktual</span>
            </nav>
            <h1 class="font-headline font-extrabold text-3xl text-on-background tracking-tight">Evaluasi Aktivitas GMO</h1>
            <p class="mt-1.5 text-sm text-on-surface-variant">
               Plan (kategori aktivitas) · Aktual (logbook <code class="text-xs">alasan</code>) · Deviasi (SAP) &bull; {{ $tanggalLabel }}
            </p>
            <div class="mt-3 flex flex-wrap gap-2">
               <span class="plv-source-pill plv-source-pill--plan"><span class="material-symbols-outlined text-sm">assignment</span> Plan</span>
               <span class="plv-source-pill plv-source-pill--aktual"><span class="material-symbols-outlined text-sm">menu_book</span> Aktual</span>
               <span class="plv-source-pill plv-source-pill--sap"><span class="material-symbols-outlined text-sm">report</span> SAP Deviasi</span>
            </div>
         </div>

         <form method="GET" action="{{ route('pembatasan-lv.evaluasi.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Site</label>
               <select name="site" class="plv-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[8rem]">
                  @foreach($filterOptions['sites'] as $site)
                  <option value="{{ $site }}" @selected($filters['site'] === $site)>{{ $site }}</option>
                  @endforeach
               </select>
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Tanggal</label>
               <input type="date" name="tanggal" value="{{ $filters['tanggal'] }}" class="plv-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[10rem]"/>
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Shift</label>
               <select name="shift" class="plv-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[9rem]">
                  <option value="">Semua Shift</option>
                  @foreach($filterOptions['shifts'] as $shift)
                  <option value="{{ $shift }}" @selected($filters['shift'] === $shift)>{{ $shift }}</option>
                  @endforeach
               </select>
            </div>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Perusahaan</label>
               <select name="perusahaan" class="plv-filter-pill rounded-xl px-3 py-2.5 text-sm font-semibold min-w-[12rem]">
                  <option value="">Semua Perusahaan</option>
                  @foreach($filterOptions['perusahaan'] as $perusahaan)
                  <option value="{{ $perusahaan }}" @selected($filters['perusahaan'] === $perusahaan)>{{ $perusahaan }}</option>
                  @endforeach
               </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:opacity-95">
               <span class="material-symbols-outlined text-lg">filter_alt</span>
               Terapkan
            </button>
            @if($filters['shift'] || $filters['perusahaan'] || request()->has('tanggal'))
            <a href="{{ route('pembatasan-lv.evaluasi.index', ['site' => $filters['site']]) }}" class="plv-filter-pill inline-flex items-center justify-center rounded-xl px-3 py-2.5" title="Reset filter">
               <span class="material-symbols-outlined text-xl text-on-surface-variant">restart_alt</span>
            </a>
            @endif
         </form>
      </div>
   </section>

   @if(!$sapAvailable)
   <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900" role="status">
      <span class="font-semibold">SAP ClickHouse tidak tersedia.</span> Data deviasi SAP tidak dapat dimuat saat ini. Plan dan logbook tetap ditampilkan.
   </div>
   @endif

   <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
      @foreach($kpis as $kpi)
      <div class="plv-eval-card plv-eval-kpi p-5 {{ $kpi['color'] }}">
         <div class="flex items-start justify-between gap-3 relative z-10">
            <div>
               <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">{{ $kpi['label'] }}</p>
               <p class="mt-2 font-headline font-bold text-4xl tabular-nums text-on-background leading-none">{{ is_numeric($kpi['value']) ? number_format($kpi['value']) : $kpi['value'] }}</p>
               <p class="mt-2 text-xs text-on-surface-variant">{{ $kpi['hint'] }}</p>
            </div>
            <span class="material-symbols-outlined text-3xl opacity-70">{{ $kpi['icon'] }}</span>
         </div>
      </div>
      @endforeach
   </section>

   <section class="plv-eval-card overflow-hidden">
      <div class="px-5 py-4 border-b border-outline-variant/15 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
         <div>
            <h2 class="font-headline font-bold text-lg text-on-background">Matriks Evaluasi</h2>
            <p class="text-xs text-on-surface-variant mt-0.5">{{ $tanggalLabel }} · {{ $shiftLabel }} · {{ $perusahaanLabel }} · Klik baris untuk lihat logbook</p>
         </div>
         <div class="flex flex-wrap gap-2 text-xs">
            <span class="plv-status plv-status--tercatat">Tercatat</span>
            <span class="plv-status plv-status--belum">Belum Tercatat</span>
            <span class="plv-status plv-status--deviasi">Ada Deviasi SAP</span>
            <span class="plv-status plv-status--tidak">Tidak Dijadwalkan</span>
         </div>
      </div>
      <div class="overflow-x-auto">
         <table class="w-full text-sm">
            <thead class="bg-surface-container-low/60 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">
               <tr>
                  <th class="px-4 py-3 text-left">Perusahaan</th>
                  <th class="px-4 py-3 text-left">Kategori Aktivitas (Plan)</th>
                  <th class="px-4 py-3 text-left">Frekuensi</th>
                  <th class="px-4 py-3 text-center">Aktual (Alasan)</th>
                  <th class="px-4 py-3 text-center">SAP Deviasi</th>
                  <th class="px-4 py-3 text-left">Status</th>
               </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
               @forelse($rows as $row)
               @php
                  $statusClass = match($row['status']) {
                     'tercatat' => 'plv-status--tercatat',
                     'belum_tercatat' => 'plv-status--belum',
                     'deviasi_sap' => 'plv-status--deviasi',
                     default => 'plv-status--tidak',
                  };
               @endphp
               <tr
                  class="plv-eval-row hover:bg-primary/[0.02] transition-colors"
                  tabindex="0"
                  role="button"
                  data-plv-eval-row
                  data-row-id="{{ $row['id'] }}"
                  aria-label="Lihat logbook {{ $row['kategori'] }}"
               >
                  <td class="px-4 py-3 font-medium text-on-background whitespace-nowrap">{{ $row['perusahaan'] }}</td>
                  <td class="px-4 py-3 max-w-xs">
                     <p class="font-semibold text-on-background">{{ $row['kategori'] }}</p>
                     <p class="text-xs text-on-surface-variant mt-0.5 line-clamp-2">{{ $row['detail'] }}</p>
                  </td>
                  <td class="px-4 py-3 text-on-surface-variant whitespace-nowrap">{{ $row['frekuensi'] ?: '—' }}</td>
                  <td class="px-4 py-3 text-center">
                     <span class="inline-flex items-center justify-center min-w-[2rem] rounded-lg bg-emerald-50 px-2 py-1 font-bold tabular-nums text-emerald-700">{{ $row['aktual_count'] }}</span>
                     @if(!empty($row['aktual_alasan']))
                     <p class="text-[10px] text-emerald-700 mt-1 max-w-[12rem] truncate" title="{{ implode(' · ', $row['aktual_alasan']) }}">{{ implode(' · ', array_slice($row['aktual_alasan'], 0, 2)) }}{{ count($row['aktual_alasan']) > 2 ? '…' : '' }}</p>
                     @elseif($row['aktual_count'] === 0)
                     <p class="text-[10px] text-on-surface-variant/60 mt-1">Belum ada alasan cocok</p>
                     @endif
                     @if(!empty($row['aktual_karyawan']))
                     <p class="text-[10px] text-on-surface-variant mt-0.5 max-w-[12rem] truncate" title="{{ implode(', ', $row['aktual_karyawan']) }}">{{ implode(', ', array_slice($row['aktual_karyawan'], 0, 3)) }}{{ count($row['aktual_karyawan']) > 3 ? '…' : '' }}</p>
                     @endif
                  </td>
                  <td class="px-4 py-3 text-center">
                     @if($row['sap_count'] > 0)
                     <span class="inline-flex items-center justify-center min-w-[2rem] rounded-lg bg-red-50 px-2 py-1 font-bold tabular-nums text-red-700">{{ $row['sap_count'] }}</span>
                     @else
                     <span class="text-on-surface-variant/50">0</span>
                     @endif
                  </td>
                  <td class="px-4 py-3">
                     <span class="plv-status {{ $statusClass }}">{{ $row['status_label'] }}</span>
                  </td>
               </tr>
               @empty
               <tr>
                  <td colspan="6" class="px-4 py-12 text-center text-on-surface-variant">
                     <span class="material-symbols-outlined text-4xl opacity-30 block mb-2">inventory_2</span>
                     Belum ada data aktivitas plan untuk filter ini.
                  </td>
               </tr>
               @endforelse
            </tbody>
         </table>
      </div>
   </section>

   <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
      <div class="plv-eval-card overflow-hidden">
         <div class="px-5 py-4 border-b border-outline-variant/15 flex items-center gap-2">
            <span class="plv-source-pill plv-source-pill--aktual"><span class="material-symbols-outlined text-sm">menu_book</span> Aktual</span>
            <h2 class="font-headline font-bold text-base text-on-background">Logbook GMO</h2>
            <span class="ml-auto text-xs font-bold text-on-surface-variant tabular-nums">{{ count($logbookRows) }} entri</span>
         </div>
         <div class="overflow-x-auto max-h-[28rem] overflow-y-auto">
            <table class="w-full text-sm">
               <thead class="sticky top-0 bg-white text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">
                  <tr>
                     <th class="px-4 py-2.5 text-left">Tanggal</th>
                     <th class="px-4 py-2.5 text-left">Jam</th>
                     <th class="px-4 py-2.5 text-left">Shift</th>
                     <th class="px-4 py-2.5 text-left">Karyawan</th>
                     <th class="px-4 py-2.5 text-left">Alasan (Aktual)</th>
                     <th class="px-4 py-2.5 text-left">Perusahaan</th>
                     <th class="px-4 py-2.5 text-center">Verifikasi</th>
                  </tr>
               </thead>
               <tbody class="divide-y divide-outline-variant/10">
                  @forelse($logbookRows as $log)
                  <tr class="hover:bg-emerald-50/30">
                     <td class="px-4 py-2.5 whitespace-nowrap text-on-surface-variant">{{ $log['tanggal'] }}</td>
                     <td class="px-4 py-2.5 tabular-nums whitespace-nowrap">{{ $log['jam'] ?: '—' }}</td>
                     <td class="px-4 py-2.5 whitespace-nowrap">{{ $log['shift'] }}</td>
                     <td class="px-4 py-2.5 font-medium">{{ $log['nama_karyawan'] }}</td>
                     <td class="px-4 py-2.5 text-xs max-w-[10rem]">
                        <span class="line-clamp-2 {{ $log['alasan'] ? 'text-emerald-800 font-medium' : 'text-on-surface-variant/50' }}">{{ $log['alasan'] ?: '—' }}</span>
                     </td>
                     <td class="px-4 py-2.5 text-on-surface-variant">{{ $log['perusahan'] }}</td>
                     <td class="px-4 py-2.5 text-center">
                        @if($log['verifikasi_izin'] === true)
                        <span class="material-symbols-outlined text-emerald-600 text-lg" title="Terverifikasi">check_circle</span>
                        @elseif($log['verifikasi_izin'] === false)
                        <span class="material-symbols-outlined text-on-surface-variant/40 text-lg">radio_button_unchecked</span>
                        @else
                        <span class="text-on-surface-variant/40 text-xs">—</span>
                        @endif
                     </td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="7" class="px-4 py-10 text-center text-on-surface-variant text-sm">Tidak ada logbook pada tanggal/shift ini.</td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>

      <div class="plv-eval-card overflow-hidden">
         <div class="px-5 py-4 border-b border-outline-variant/15 flex items-center gap-2">
            <span class="plv-source-pill plv-source-pill--sap"><span class="material-symbols-outlined text-sm">report</span> Deviasi</span>
            <h2 class="font-headline font-bold text-base text-on-background">Laporan SAP</h2>
            <span class="ml-auto text-xs font-bold text-on-surface-variant tabular-nums">{{ count($sapRows) }} laporan</span>
         </div>
         <div class="overflow-x-auto max-h-[28rem] overflow-y-auto">
            <table class="w-full text-sm">
               <thead class="sticky top-0 bg-white text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">
                  <tr>
                     <th class="px-4 py-2.5 text-left">Tanggal</th>
                     <th class="px-4 py-2.5 text-left">Pelapor</th>
                     <th class="px-4 py-2.5 text-left">Jenis</th>
                     <th class="px-4 py-2.5 text-left">Ketidaksesuaian</th>
                     <th class="px-4 py-2.5 text-left">Risiko</th>
                  </tr>
               </thead>
               <tbody class="divide-y divide-outline-variant/10">
                  @forelse($sapRows as $sap)
                  <tr class="hover:bg-red-50/30">
                     <td class="px-4 py-2.5 whitespace-nowrap text-xs text-on-surface-variant">{{ $sap['tanggal'] ? \Carbon\Carbon::parse($sap['tanggal'])->format('d M Y') : '—' }}</td>
                     <td class="px-4 py-2.5">
                        <p class="font-medium">{{ $sap['nama_pelapor'] }}</p>
                        <p class="text-[10px] text-on-surface-variant">{{ $sap['perusahaan_pelapor'] }}</p>
                     </td>
                     <td class="px-4 py-2.5 whitespace-nowrap">{{ $sap['jenis_laporan'] }}</td>
                     <td class="px-4 py-2.5 max-w-[12rem]">
                        <p class="line-clamp-2 text-xs">{{ $sap['ketidaksesuaian'] ?: $sap['deskripsi'] ?: '—' }}</p>
                     </td>
                     <td class="px-4 py-2.5 whitespace-nowrap text-xs font-semibold">{{ $sap['nilai_resiko'] ?: '—' }}</td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="5" class="px-4 py-10 text-center text-on-surface-variant text-sm">Tidak ada laporan SAP deviasi pada tanggal ini.</td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>
   </section>
</div>

{{-- Modal detail logbook per baris matriks --}}
<div id="plv-eval-logbook-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
   <div class="plv-eval-modal-backdrop absolute inset-0" data-plv-eval-modal-close></div>
   <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">
      <div class="plv-eval-modal-panel plv-eval-card w-full max-w-5xl flex flex-col overflow-hidden bg-white shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="plv-eval-modal-title">
         <div class="px-5 py-4 border-b border-outline-variant/15 flex items-start justify-between gap-4 shrink-0">
            <div class="min-w-0">
               <p class="text-[10px] font-bold uppercase tracking-wider text-primary">Detail Logbook Aktual</p>
               <h3 id="plv-eval-modal-title" class="font-headline font-bold text-lg text-on-background mt-1 truncate">—</h3>
               <p id="plv-eval-modal-subtitle" class="text-xs text-on-surface-variant mt-1 line-clamp-2">—</p>
            </div>
            <button type="button" class="shrink-0 rounded-xl p-2 text-on-surface-variant hover:bg-surface-container-low transition-colors" data-plv-eval-modal-close aria-label="Tutup">
               <span class="material-symbols-outlined text-xl">close</span>
            </button>
         </div>
         <div class="px-5 py-3 border-b border-outline-variant/10 flex flex-wrap gap-2 text-xs shrink-0">
            <span id="plv-eval-modal-meta-perusahaan" class="inline-flex items-center gap-1 rounded-lg bg-[#f1f5f9] px-2.5 py-1 font-medium text-on-surface-variant"></span>
            <span id="plv-eval-modal-meta-frekuensi" class="inline-flex items-center gap-1 rounded-lg bg-[#f1f5f9] px-2.5 py-1 font-medium text-on-surface-variant"></span>
            <span id="plv-eval-modal-meta-count" class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1 font-bold text-emerald-700"></span>
         </div>
         <div class="overflow-auto flex-1 min-h-0">
            <table class="w-full text-sm">
               <thead class="sticky top-0 bg-white text-[10px] font-bold uppercase tracking-wider text-on-surface-variant shadow-sm">
                  <tr>
                     <th class="px-4 py-2.5 text-left">Tanggal</th>
                     <th class="px-4 py-2.5 text-left">Jam</th>
                     <th class="px-4 py-2.5 text-left">Shift</th>
                     <th class="px-4 py-2.5 text-left">Karyawan</th>
                     <th class="px-4 py-2.5 text-left">Alasan (Aktual)</th>
                     <th class="px-4 py-2.5 text-left">Perusahaan</th>
                     <th class="px-4 py-2.5 text-center">Verifikasi</th>
                  </tr>
               </thead>
               <tbody id="plv-eval-modal-tbody" class="divide-y divide-outline-variant/10"></tbody>
            </table>
         </div>
         <div id="plv-eval-modal-empty" class="hidden px-5 py-12 text-center text-on-surface-variant text-sm">
            <span class="material-symbols-outlined text-4xl opacity-30 block mb-2">menu_book</span>
            Belum ada logbook yang cocok dengan aktivitas plan ini.
         </div>
      </div>
   </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
   var modal = document.getElementById('plv-eval-logbook-modal');
   if (!modal) return;

   var rows = @json($rows);
   var rowMap = {};
   rows.forEach(function (row) { rowMap[String(row.id)] = row; });

   var titleEl = document.getElementById('plv-eval-modal-title');
   var subtitleEl = document.getElementById('plv-eval-modal-subtitle');
   var metaPerusahaan = document.getElementById('plv-eval-modal-meta-perusahaan');
   var metaFrekuensi = document.getElementById('plv-eval-modal-meta-frekuensi');
   var metaCount = document.getElementById('plv-eval-modal-meta-count');
   var tbody = document.getElementById('plv-eval-modal-tbody');
   var emptyState = document.getElementById('plv-eval-modal-empty');

   function verifikasiHtml(value) {
      if (value === true) {
         return '<span class="material-symbols-outlined text-emerald-600 text-lg" title="Terverifikasi">check_circle</span>';
      }
      if (value === false) {
         return '<span class="material-symbols-outlined text-on-surface-variant/40 text-lg">radio_button_unchecked</span>';
      }
      return '<span class="text-on-surface-variant/40 text-xs">—</span>';
   }

   function escapeHtml(str) {
      return String(str || '')
         .replace(/&/g, '&amp;')
         .replace(/</g, '&lt;')
         .replace(/>/g, '&gt;')
         .replace(/"/g, '&quot;');
   }

   function openModal(row) {
      if (!row) return;

      titleEl.textContent = row.kategori || '—';
      subtitleEl.textContent = row.detail || '—';
      metaPerusahaan.textContent = row.perusahaan || '—';
      metaFrekuensi.textContent = row.frekuensi ? ('Frekuensi: ' + row.frekuensi) : 'Frekuensi: —';
      metaCount.textContent = (row.aktual_count || 0) + ' logbook';

      var items = row.logbook_items || [];
      tbody.innerHTML = '';

      if (items.length === 0) {
         emptyState.classList.remove('hidden');
         tbody.closest('table').classList.add('hidden');
      } else {
         emptyState.classList.add('hidden');
         tbody.closest('table').classList.remove('hidden');

         items.forEach(function (log) {
            var tr = document.createElement('tr');
            tr.className = 'hover:bg-emerald-50/30';
            tr.innerHTML =
               '<td class="px-4 py-2.5 whitespace-nowrap text-on-surface-variant">' + escapeHtml(log.tanggal || '—') + '</td>' +
               '<td class="px-4 py-2.5 tabular-nums whitespace-nowrap">' + escapeHtml(log.jam || '—') + '</td>' +
               '<td class="px-4 py-2.5 whitespace-nowrap">' + escapeHtml(log.shift || '—') + '</td>' +
               '<td class="px-4 py-2.5 font-medium">' + escapeHtml(log.nama_karyawan || '—') + '</td>' +
               '<td class="px-4 py-2.5 text-xs max-w-[12rem]"><span class="line-clamp-2 ' + (log.alasan ? 'text-emerald-800 font-medium' : 'text-on-surface-variant/50') + '">' + escapeHtml(log.alasan || '—') + '</span></td>' +
               '<td class="px-4 py-2.5 text-on-surface-variant">' + escapeHtml(log.perusahan || '—') + '</td>' +
               '<td class="px-4 py-2.5 text-center">' + verifikasiHtml(log.verifikasi_izin) + '</td>';
            tbody.appendChild(tr);
         });
      }

      modal.classList.remove('hidden');
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
   }

   function closeModal() {
      modal.classList.add('hidden');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
   }

   document.querySelectorAll('[data-plv-eval-row]').forEach(function (tr) {
      function handleOpen() {
         var id = tr.getAttribute('data-row-id');
         openModal(rowMap[id]);
      }
      tr.addEventListener('click', handleOpen);
      tr.addEventListener('keydown', function (e) {
         if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            handleOpen();
         }
      });
   });

   modal.querySelectorAll('[data-plv-eval-modal-close]').forEach(function (el) {
      el.addEventListener('click', closeModal);
   });

   document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
         closeModal();
      }
   });
})();
</script>
@endpush
