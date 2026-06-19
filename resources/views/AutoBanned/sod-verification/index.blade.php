@extends('AutoBanned.layouts.app')

@section('title', 'Verifikasi SOD — Pengajuan Treatment')

@push('head')
<style>
   .ab-sod { --ab-ease: cubic-bezier(0.4, 0, 0.2, 1); }
   .ab-fade-in { animation: abFadeUp 0.55s var(--ab-ease) both; }
   @keyframes abFadeUp {
      from { opacity: 0; transform: translateY(10px); }
      to   { opacity: 1; transform: translateY(0); }
   }
   .ab-surface-card {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(57, 82, 188, 0.07);
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04), 0 8px 24px -6px rgba(57, 82, 188, 0.08);
   }
   .ab-badge {
      display: inline-flex; align-items: center; border-radius: 0.375rem;
      padding: 0.125rem 0.5rem; font-size: 10px; font-weight: 600;
      border: 1px solid rgba(57, 82, 188, 0.12); background: rgba(57, 82, 188, 0.06); color: #3952bc;
   }
   .ab-badge--ok { border-color: rgba(16,185,129,.25); background: rgba(16,185,129,.08); color: #047857; }
   .ab-badge--wait { border-color: rgba(245,158,11,.3); background: rgba(245,158,11,.1); color: #b45309; }
   .ab-badge--muted { border-color: rgba(171,173,175,.35); background: #f1f5f9; color: #595c5e; }
   .ab-sheet-table { border-collapse: collapse; font-size: 11px; width: 100%; }
   .ab-sheet-table thead th {
      background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.5rem 0.625rem;
      font-size: 10px; font-weight: 600; color: #475569; white-space: nowrap;
   }
   .ab-sheet-table tbody td { border: 1px solid #e2e8f0; padding: 0.5rem 0.625rem; color: #1e293b; vertical-align: top; }
   .ab-sheet-table tbody tr:hover td { background: #f8fafc; }
   .ab-tab-btn { transition: all .2s var(--ab-ease); }
   .ab-tab-btn[aria-selected="true"] { background: #fff; color: #3952bc; box-shadow: 0 1px 3px rgba(57,82,188,.1); }
   .ab-btn-approve { background: #059669; color: #fff; }
   .ab-btn-reject { background: #fff; color: #b91c1c; border: 1px solid #fecaca; }
   .ab-btn-sm {
      display: inline-flex; align-items: center; gap: .25rem; border-radius: .5rem;
      padding: .35rem .65rem; font-size: 10px; font-weight: 700;
   }
</style>
@endpush

@section('content')
@php
   $periodLabel = trim(($period['week'] ?? '').' · '.($period['year'] ?? ''), ' ·');
   $summaryCounts = $summaryCounts ?? ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'total' => 0, 'withEvidence' => 0];
   $submittedRequests = $submittedRequests ?? collect();
   $currentStatus = $filters['status'] ?? 'pending';
   $statusTabs = [
      'pending' => 'Menunggu Review',
      'approved' => 'Disetujui',
      'rejected' => 'Ditolak',
      'all' => 'Semua',
   ];
   $queryBase = array_filter([
      'site' => $filters['site'] ?? '',
      'week' => $filters['week'] ?? '',
      'year' => $filters['year'] ?? '',
      'perusahaan' => $filters['perusahaan'] ?? '',
      'q' => $filters['q'] ?? '',
   ], fn ($v) => $v !== '' && $v !== null);
@endphp

<div class="ab-sod -mt-2 space-y-7">
   <section class="pb-6 border-b border-outline-variant/30">
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
         <div class="min-w-0 ab-fade-in">
            <nav class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.08em] text-on-surface-variant mb-2.5">
               <span>Auto Banned</span>
               <span class="material-symbols-outlined text-[13px] opacity-60">chevron_right</span>
               <span class="text-primary">Verifikasi SOD</span>
            </nav>
            <h1 class="font-headline font-extrabold text-3xl text-on-background tracking-tight">Verifikasi Pengajuan Treatment</h1>
            <p class="text-sm text-on-surface-variant mt-2 max-w-2xl">
               Daftar pengajuan unbanned / evidence treatment yang sudah disubmit dari form publik atau inputasi.
               @if($periodLabel !== '') Periode filter: <strong>{{ $periodLabel }}</strong>.@endif
            </p>
         </div>
         <div class="shrink-0">
            @include('AutoBanned.partials.filter-bar', [
               'filters' => $filters,
               'filterOptions' => $filterOptions,
               'filterRoute' => $filterRoute,
               'preserveParams' => ['status' => in_array($currentStatus, ['pending', 'approved', 'rejected', 'all'], true) ? $currentStatus : 'pending'],
            ])
         </div>
      </div>
   </section>

   @if(!$tableAvailable)
   <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
      Tabel <code class="text-xs">auto_banned_unban_requests</code> belum tersedia. Jalankan migration.
   </div>
   @else
   <section class="grid grid-cols-2 sm:grid-cols-4 gap-3">
      <div class="ab-surface-card rounded-2xl p-4">
         <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Menunggu Review</p>
         <p class="mt-1 font-headline text-3xl font-bold text-amber-600 tabular-nums">{{ $summaryCounts['pending'] }}</p>
      </div>
      <div class="ab-surface-card rounded-2xl p-4">
         <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Disetujui</p>
         <p class="mt-1 font-headline text-3xl font-bold text-emerald-700 tabular-nums">{{ $summaryCounts['approved'] }}</p>
      </div>
      <div class="ab-surface-card rounded-2xl p-4">
         <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Ditolak</p>
         <p class="mt-1 font-headline text-3xl font-bold text-on-surface-variant tabular-nums">{{ $summaryCounts['rejected'] }}</p>
      </div>
      <div class="ab-surface-card rounded-2xl p-4">
         <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Ada Evidence</p>
         <p class="mt-1 font-headline text-3xl font-bold text-primary tabular-nums">{{ $summaryCounts['withEvidence'] }}</p>
      </div>
   </section>

   <section class="ab-surface-card rounded-2xl overflow-hidden">
      <div class="px-5 sm:px-6 pt-5 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-outline-variant/10">
         <div>
            <h2 class="font-headline font-semibold text-base text-on-background">Daftar Pengajuan</h2>
            <p class="text-xs text-on-surface-variant mt-0.5">{{ $submittedRequests->count() }} baris · verifikasi treatment oleh SOD</p>
         </div>
         <div class="inline-flex p-1 rounded-xl bg-[#f1f5f9]/80 gap-0.5 flex-wrap" role="tablist">
            @foreach($statusTabs as $statusKey => $statusLabel)
            @php
               $tabQuery = array_merge($queryBase, $statusKey === 'all' ? [] : ['status' => $statusKey]);
            @endphp
            <a href="{{ route('auto-banned.sod-verification.index', $tabQuery) }}"
               role="tab"
               aria-selected="{{ ($currentStatus === $statusKey || ($statusKey === 'all' && $currentStatus === '')) ? 'true' : 'false' }}"
               class="ab-tab-btn rounded-lg px-3 py-2 text-xs font-semibold {{ ($currentStatus === $statusKey || ($statusKey === 'all' && !in_array($currentStatus, ['pending','approved','rejected'], true))) ? 'text-primary bg-white shadow-sm' : 'text-on-surface-variant' }}">
               {{ $statusLabel }}
            </a>
            @endforeach
         </div>
      </div>

      <div class="px-5 sm:px-6 py-3 border-b border-outline-variant/5 bg-[#fafbfc]/50">
         <form method="GET" action="{{ route('auto-banned.sod-verification.index') }}" class="flex flex-wrap gap-2 items-center">
            @foreach($queryBase as $key => $val)
            <input type="hidden" name="{{ $key }}" value="{{ $val }}"/>
            @endforeach
            @if($currentStatus !== '' && $currentStatus !== 'all')
            <input type="hidden" name="status" value="{{ $currentStatus }}"/>
            @endif
            <div class="relative flex-1 min-w-[200px] max-w-sm">
               <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-base">search</span>
               <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari SID, nama, perusahaan…" class="w-full rounded-xl border border-outline-variant/25 bg-white pl-9 pr-3 py-2 text-xs font-medium"/>
            </div>
            <button type="submit" class="rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white">Cari</button>
            <a href="{{ route('auto-banned.public.treatment.form', array_filter(['week' => $filters['week'] ?? '', 'year' => $filters['year'] ?? ''])) }}" target="_blank" rel="noopener" class="ml-auto inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline">
               <span class="material-symbols-outlined text-base">open_in_new</span>
               Form publik treatment
            </a>
         </form>
      </div>

      <div class="overflow-x-auto">
         <table class="ab-sheet-table min-w-[1100px]">
            <thead>
               <tr>
                  <th>No</th>
                  <th>Tanggal Submit</th>
                  <th>SID</th>
                  <th>Karyawan</th>
                  <th>Perusahaan</th>
                  <th>Site</th>
                  <th>Ringkasan Treatment</th>
                  <th>Evidence</th>
                  <th>Status</th>
                  <th>Verifikasi</th>
                  <th>Aksi</th>
               </tr>
            </thead>
            <tbody>
               @forelse($submittedRequests as $index => $row)
               @php
                  $badgeClass = match ($row->status->value) {
                     'approved' => 'ab-badge--ok',
                     'rejected' => 'ab-badge--muted',
                     default => 'ab-badge--wait',
                  };
               @endphp
               <tr>
                  <td class="tabular-nums text-on-surface-variant">{{ $index + 1 }}</td>
                  <td class="whitespace-nowrap text-[11px]">
                     {{ $row->created_at?->format('d M Y H:i') }}
                     @if($row->submitted_by_name)
                     <span class="block text-[10px] text-on-surface-variant mt-0.5">{{ $row->submitted_by_name }}</span>
                     @endif
                  </td>
                  <td class="font-mono font-bold text-primary">{{ $row->sid }}</td>
                  <td class="font-medium">{{ $row->karyawan }}</td>
                  <td>{{ $row->perusahaan ?: '—' }}</td>
                  <td>{{ $row->site_dedicated ?: '—' }}</td>
                  <td class="max-w-[220px]">
                     <p class="line-clamp-3" title="{{ $row->alasan_pengajuan }}">{{ $row->alasan_pengajuan }}</p>
                     @if($row->banned_reason)
                     <span class="block text-[10px] text-on-surface-variant mt-1">Alasan banned: {{ $row->banned_reason }}</span>
                     @endif
                  </td>
                  <td>
                     @if(!empty($row->evidence_file_path))
                     <a href="{{ route('auto-banned.unban-requests.evidence', $row) }}" class="inline-flex items-center gap-1 font-semibold text-primary hover:underline text-[11px]">
                        <span class="material-symbols-outlined text-sm">download</span>
                        {{ \Illuminate\Support\Str::limit($row->evidence_original_name ?: 'Evidence', 20) }}
                     </a>
                     @else
                     <span class="text-on-surface-variant">—</span>
                     @endif
                  </td>
                  <td><span class="ab-badge {{ $badgeClass }}">{{ $row->status->label() }}</span></td>
                  <td class="text-[11px] text-on-surface-variant whitespace-nowrap">
                     @if($row->reviewed_at)
                     <span class="block">{{ $row->reviewed_at->format('d M Y H:i') }}</span>
                     <span class="block">{{ $row->reviewed_by_name ?: '—' }}</span>
                     @if($row->catatan_review)
                     <span class="block mt-1 text-[10px] italic" title="{{ $row->catatan_review }}">{{ \Illuminate\Support\Str::limit($row->catatan_review, 40) }}</span>
                     @endif
                     @else
                     —
                     @endif
                  </td>
                  <td>
                     @if($row->status === \App\Enums\AutoBannedUnbanStatus::Pending)
                     <div class="flex flex-col gap-1.5 min-w-[9rem]">
                        <form method="POST" action="{{ route('auto-banned.unban-requests.review', $row) }}" class="inline">
                           @csrf
                           <input type="hidden" name="action" value="approve"/>
                           <button type="submit" class="ab-btn-sm ab-btn-approve w-full justify-center" onclick="return confirm('Setujui pengajuan SID {{ $row->sid }}?')">
                              <span class="material-symbols-outlined text-sm">check_circle</span>
                              Setujui
                           </button>
                        </form>
                        <form method="POST" action="{{ route('auto-banned.unban-requests.review', $row) }}" class="inline space-y-1">
                           @csrf
                           <input type="hidden" name="action" value="reject"/>
                           <input type="text" name="catatan_review" placeholder="Catatan (opsional)" maxlength="2000" class="w-full rounded-lg border border-outline-variant/25 px-2 py-1 text-[10px]"/>
                           <button type="submit" class="ab-btn-sm ab-btn-reject w-full justify-center" onclick="return confirm('Tolak pengajuan SID {{ $row->sid }}?')">
                              <span class="material-symbols-outlined text-sm">cancel</span>
                              Tolak
                           </button>
                        </form>
                     </div>
                     @else
                     <span class="text-[10px] text-on-surface-variant">Selesai</span>
                     @endif
                  </td>
               </tr>
               @empty
               <tr>
                  <td colspan="11" class="py-14 text-center text-sm text-on-surface-variant">
                     @if($currentStatus === 'pending')
                     Tidak ada pengajuan yang menunggu verifikasi SOD.
                     @else
                     Tidak ada data untuk filter ini.
                     @endif
                  </td>
               </tr>
               @endforelse
            </tbody>
         </table>
      </div>
   </section>
   @endif
</div>
@endsection
