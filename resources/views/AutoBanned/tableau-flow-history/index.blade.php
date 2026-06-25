@extends('AutoBanned.layouts.app')

@section('title', 'Monitoring Flow Tablue')

@push('head')
<style>
   .ab-tableau { --ab-ease: cubic-bezier(0.4, 0, 0.2, 1); }
   .ab-fade-in { animation: abFadeUp 0.55s var(--ab-ease) both; }
   .ab-fade-in-delay-1 { animation-delay: 0.06s; }
   .ab-fade-in-delay-2 { animation-delay: 0.12s; }
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
      display: inline-flex;
      align-items: center;
      border-radius: 0.375rem;
      padding: 0.125rem 0.5rem;
      font-size: 10px;
      font-weight: 600;
      border: 1px solid rgba(57, 82, 188, 0.12);
      background: rgba(57, 82, 188, 0.06);
      color: #3952bc;
   }
   .ab-badge--ok {
      border-color: rgba(16, 185, 129, 0.25);
      background: rgba(16, 185, 129, 0.08);
      color: #047857;
   }
   .ab-badge--wait {
      border-color: rgba(245, 158, 11, 0.3);
      background: rgba(245, 158, 11, 0.1);
      color: #b45309;
   }
   .ab-badge--danger {
      border-color: rgba(239, 68, 68, 0.25);
      background: rgba(239, 68, 68, 0.08);
      color: #b91c1c;
   }
   .ab-badge--muted {
      border-color: rgba(171, 173, 175, 0.35);
      background: #f1f5f9;
      color: #595c5e;
   }
   .ab-sheet-table {
      border-collapse: collapse;
      font-size: 11px;
   }
   .ab-sheet-table thead th {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      padding: 0.5rem 0.625rem;
      font-size: 10px;
      font-weight: 600;
      color: #475569;
      white-space: nowrap;
   }
   .ab-sheet-table tbody td {
      border: 1px solid #e2e8f0;
      padding: 0.5rem 0.625rem;
      color: #1e293b;
      line-height: 1.45;
      vertical-align: top;
   }
   .ab-sheet-table tbody tr:hover td { background: #f8fafc; }
   .ab-filter-input {
      border-radius: 0.75rem;
      border: 1px solid rgba(171, 173, 175, 0.35);
      background: #fff;
      padding: 0.5rem 0.75rem;
      font-size: 12px;
      color: #2c2f31;
   }
   .ab-filter-input:focus {
      outline: none;
      ring: 2px;
      border-color: rgba(57, 82, 188, 0.35);
      box-shadow: 0 0 0 2px rgba(57, 82, 188, 0.12);
   }
</style>
@endpush

@section('content')
@php
   $historySummary = $historySummary ?? [
      'totalRecords' => 0,
      'latestLoggedAt' => null,
      'latestLoggedLabel' => null,
      'runningCount' => 0,
      'pendingCount' => 0,
      'successCount' => 0,
      'failedCount' => 0,
   ];
   $historyRows = $historyRows ?? collect();
   $statusCodeOptions = $statusCodeOptions ?? collect();
   $flowNameOptions = $flowNameOptions ?? collect();
   $tableAvailable = $tableAvailable ?? false;
   $timezone = config('auto_banned.hsct.timezone', 'Asia/Makassar');

   $statusBadgeClass = function (?string $code): string {
      $normalized = strtoupper(trim((string) $code));
      return match (true) {
         in_array($normalized, ['SUCCESS', 'SUCCEEDED', 'COMPLETED'], true) => 'ab-badge--ok',
         in_array($normalized, ['RUNNING', 'IN_PROGRESS'], true) => 'ab-badge',
         in_array($normalized, ['PENDING', 'QUEUED', 'WAITING'], true) => 'ab-badge--wait',
         in_array($normalized, ['FAILED', 'ERROR', 'CANCELLED', 'CANCELED'], true) => 'ab-badge--danger',
         default => 'ab-badge--muted',
      };
   };
@endphp

<div class="ab-tableau -mt-2 space-y-7">

   <section class="ab-page-header pb-6 border-b border-outline-variant/30">
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5 lg:gap-8">
         <div class="min-w-0 ab-fade-in">
            <nav class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.08em] text-on-surface-variant mb-2.5" aria-label="Breadcrumb">
               <span>Dashboard</span>
               <span class="material-symbols-outlined text-[13px] opacity-60">chevron_right</span>
               <span>Auto Banned</span>
               <span class="material-symbols-outlined text-[13px] opacity-60">chevron_right</span>
               <span class="text-primary">Riwayat Tableau Flow</span>
            </nav>
            <div class="flex flex-wrap items-center gap-3">
               <h1 class="font-headline font-extrabold text-3xl sm:text-[2.125rem] text-on-background tracking-tight leading-tight">Riwayat Tableau Flow</h1>
               <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/[0.08] border border-primary/15 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-primary">
                  <span class="material-symbols-outlined text-sm">sync</span>
                  Flow History
               </span>
            </div>
            <p class="text-sm text-on-surface-variant mt-2 max-w-2xl">
               Historis eksekusi flow Tableau Prep (scraping data Auto Banned). Hanya menampilkan log yang tersimpan di database.
            </p>
         </div>
         <div class="shrink-0 ab-fade-in ab-fade-in-delay-1">
            <form method="GET" action="{{ route($filterRoute ?? 'auto-banned.tableau-flow-history.index') }}" class="flex flex-wrap items-end justify-end gap-2">
               <div>
                  <label for="ab-flow-q" class="block text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant mb-1">Cari</label>
                  <input id="ab-flow-q" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Flow, output, status..." class="ab-filter-input w-44 sm:w-52"/>
               </div>
               <div>
                  <label for="ab-flow-status" class="block text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant mb-1">Status Code</label>
                  <select id="ab-flow-status" name="status_code" class="ab-filter-input min-w-[8rem]">
                     <option value="">Semua Status</option>
                     @foreach($statusCodeOptions as $statusCode)
                     <option value="{{ $statusCode }}" @selected(($filters['status_code'] ?? '') === $statusCode)>{{ $statusCode }}</option>
                     @endforeach
                  </select>
               </div>
               <div>
                  <label for="ab-flow-name" class="block text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant mb-1">Flow Name</label>
                  <select id="ab-flow-name" name="flow_name" class="ab-filter-input min-w-[10rem] max-w-[14rem]">
                     <option value="">Semua Flow</option>
                     @foreach($flowNameOptions as $flowName)
                     <option value="{{ $flowName }}" @selected(($filters['flow_name'] ?? '') === $flowName)>{{ $flowName }}</option>
                     @endforeach
                  </select>
               </div>
               <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-3.5 py-2 text-xs font-semibold text-white hover:bg-primary-dim transition-colors">
                  <span class="material-symbols-outlined text-base">filter_alt</span>
                  Filter
               </button>
               @if(($filters['q'] ?? '') !== '' || ($filters['status_code'] ?? '') !== '' || ($filters['flow_name'] ?? '') !== '')
               <a href="{{ route($filterRoute ?? 'auto-banned.tableau-flow-history.index') }}" class="inline-flex items-center gap-1 rounded-xl border border-outline-variant/25 bg-white px-3 py-2 text-xs font-semibold text-on-surface hover:border-primary/25 transition-colors">
                  Reset
               </a>
               @endif
            </form>
         </div>
      </div>
   </section>

   @if(!$tableAvailable)
   <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 flex items-start gap-2" role="alert">
      <span class="material-symbols-outlined text-base shrink-0">info</span>
      <span>Tabel <code class="font-mono text-xs">tableau_flow_history</code> belum tersedia. Jalankan migration atau pastikan proses scraping Tableau sudah menulis data ke database.</span>
   </div>
   @endif

   <div class="ab-fade-in ab-fade-in-delay-1 ab-surface-card rounded-2xl overflow-hidden">
      <div class="px-5 sm:px-6 pt-5 pb-4 border-b border-outline-variant/10">
         <h2 class="font-headline font-semibold text-base text-on-background">Historis Eksekusi Flow</h2>
         <p class="text-xs text-on-surface-variant mt-0.5">Log status flow Tableau — waktu, output, trigger, dan tautan ke iDashboard</p>
      </div>

      <div class="px-5 sm:px-6 py-4 bg-[#fafbfc]/80 border-b border-outline-variant/10 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-xs">
         <div class="rounded-lg bg-white border border-outline-variant/15 px-3 py-2">
            <p class="text-on-surface-variant">Total Log</p>
            <p class="font-bold text-on-background mt-0.5 tabular-nums">{{ number_format($historySummary['totalRecords']) }}</p>
         </div>
         <div class="rounded-lg bg-white border border-outline-variant/15 px-3 py-2">
            <p class="text-on-surface-variant">Terakhir Logged</p>
            <p class="font-bold text-on-background mt-0.5">{{ $historySummary['latestLoggedLabel'] ?? '—' }}</p>
         </div>
         <div class="rounded-lg bg-white border border-outline-variant/15 px-3 py-2">
            <p class="text-on-surface-variant">Running</p>
            <p class="font-bold text-primary mt-0.5 tabular-nums">{{ number_format($historySummary['runningCount']) }}</p>
         </div>
         <div class="rounded-lg bg-white border border-outline-variant/15 px-3 py-2">
            <p class="text-on-surface-variant">Pending</p>
            <p class="font-bold text-amber-700 mt-0.5 tabular-nums">{{ number_format($historySummary['pendingCount']) }}</p>
         </div>
         <div class="rounded-lg bg-white border border-outline-variant/15 px-3 py-2">
            <p class="text-on-surface-variant">Success</p>
            <p class="font-bold text-emerald-700 mt-0.5 tabular-nums">{{ number_format($historySummary['successCount']) }}</p>
         </div>
         <div class="rounded-lg bg-white border border-outline-variant/15 px-3 py-2">
            <p class="text-on-surface-variant">Failed</p>
            <p class="font-bold {{ $historySummary['failedCount'] > 0 ? 'text-red-700' : 'text-on-background' }} mt-0.5 tabular-nums">{{ number_format($historySummary['failedCount']) }}</p>
         </div>
      </div>

      <div class="overflow-x-auto">
         <table class="ab-sheet-table w-full min-w-[1100px] text-left">
            <thead>
               <tr>
                  <th>#</th>
                  <th>Logged At (WITA)</th>
                  <th>Status Code</th>
                  <th>Flow Name</th>
                  <th>Output Name</th>
                  <th>Status Detail</th>
                  <th>Trigger</th>
                  <th>Flow URL</th>
                  <th>Created At</th>
               </tr>
            </thead>
            <tbody>
               @forelse($historyRows as $row)
               <tr>
                  <td class="tabular-nums text-on-surface-variant">{{ $row->id }}</td>
                  <td class="whitespace-nowrap font-medium">
                     {{ $row->logged_at?->timezone($timezone)->format('d M Y H:i:s') ?? '—' }}
                  </td>
                  <td>
                     <span class="ab-badge {{ $statusBadgeClass($row->status_code) }}">{{ $row->status_code ?: '—' }}</span>
                  </td>
                  <td class="min-w-[12rem]">{{ $row->flow_name ?: '—' }}</td>
                  <td>{{ $row->output_name ?: '—' }}</td>
                  <td>{{ $row->status_detail ?: '—' }}</td>
                  <td>{{ $row->trigger_type ?: '—' }}</td>
                  <td class="max-w-[14rem]">
                     @if($row->flow_url)
                     <a href="{{ $row->flow_url }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline break-all text-[10px]">
                        Buka Flow
                        <span class="material-symbols-outlined text-[12px] align-middle">open_in_new</span>
                     </a>
                     @else
                     —
                     @endif
                  </td>
                  <td class="whitespace-nowrap text-on-surface-variant">
                     {{ $row->created_at?->timezone($timezone)->format('d M Y H:i') ?? '—' }}
                  </td>
               </tr>
               @empty
               <tr>
                  <td colspan="9" class="px-4 py-10 text-center text-sm text-on-surface-variant">
                     @if(!$tableAvailable)
                     Data belum tersedia — tabel history belum ada.
                     @else
                     Belum ada log flow untuk filter yang dipilih.
                     @endif
                  </td>
               </tr>
               @endforelse
            </tbody>
         </table>
      </div>

      @if($historyRows instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $historyRows->hasPages())
      <div class="px-5 sm:px-6 py-4 border-t border-outline-variant/10">
         {{ $historyRows->links() }}
      </div>
      @endif
   </div>
</div>
@endsection
