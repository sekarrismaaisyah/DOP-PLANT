@extends('AutoBanned.layouts.app')

@section('title', 'Dashboard Overview Auto Banned')

@section('page-header')
@endsection

@push('head')
<style>
   .ab-overview { --ab-ease: cubic-bezier(0.4, 0, 0.2, 1); }
   .ab-fade-in { animation: abFadeUp 0.55s var(--ab-ease) both; }
   .ab-fade-in-delay-1 { animation-delay: 0.06s; }
   .ab-fade-in-delay-2 { animation-delay: 0.12s; }
   .ab-fade-in-delay-3 { animation-delay: 0.18s; }
   .ab-fade-in-delay-4 { animation-delay: 0.24s; }
   @keyframes abFadeUp {
      from { opacity: 0; transform: translateY(10px); }
      to   { opacity: 1; transform: translateY(0); }
   }
   .ab-surface-card {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(57, 82, 188, 0.07);
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04), 0 8px 24px -6px rgba(57, 82, 188, 0.08);
      transition: box-shadow 0.35s var(--ab-ease), border-color 0.35s var(--ab-ease);
   }
   .ab-surface-card:hover {
      box-shadow: 0 2px 4px rgba(44, 47, 49, 0.05), 0 14px 32px -8px rgba(57, 82, 188, 0.12);
      border-color: rgba(57, 82, 188, 0.12);
   }
   .ab-stat-accent { position: relative; }
   .ab-stat-accent::before {
      content: '';
      position: absolute;
      left: 0; top: 1.25rem; bottom: 1.25rem;
      width: 3px;
      border-radius: 0 4px 4px 0;
      background: linear-gradient(180deg, rgba(57,82,188,0.5), rgba(57,82,188,0.15));
      opacity: 0;
      transition: opacity 0.35s var(--ab-ease);
   }
   .ab-stat-accent:hover::before { opacity: 1; }
   .ab-tab-btn {
      transition: color 0.25s var(--ab-ease), background 0.25s var(--ab-ease), box-shadow 0.25s var(--ab-ease);
   }
   .ab-tab-btn[aria-selected="true"] {
      background: #fff;
      color: #3952bc;
      box-shadow: 0 1px 3px rgba(57, 82, 188, 0.1);
   }
   .ab-empty-illus {
      background: linear-gradient(145deg, #f1f5f9 0%, #eef2ff 100%);
   }
   .ab-table-soft thead th { font-weight: 600; letter-spacing: 0.04em; }
   .ab-table-soft tbody tr { transition: background 0.2s var(--ab-ease); }
   .ab-table-soft tbody tr:hover { background: rgba(57, 82, 188, 0.02); }
   .ab-pulse-dot { animation: abPulse 2.4s ease-in-out infinite; }
   @keyframes abPulse {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.55; transform: scale(0.92); }
   }
   .ab-ring-track { stroke: #e8ecf4; }
   .ab-ring-fill {
      stroke: #3952bc;
      stroke-linecap: round;
      transition: stroke-dashoffset 1s var(--ab-ease);
   }
   .ab-page-header {
      border-bottom: 1px solid rgba(171, 173, 175, 0.35);
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
   .ab-badge--muted {
      border-color: rgba(171, 173, 175, 0.35);
      background: #f1f5f9;
      color: #595c5e;
   }
   .ab-badge--ok {
      border-color: rgba(16, 185, 129, 0.25);
      background: rgba(16, 185, 129, 0.08);
      color: #047857;
   }
   .ab-badge--wait {
      border-color: rgba(57, 82, 188, 0.2);
      background: rgba(57, 82, 188, 0.08);
      color: #2b45af;
   }
   .ab-badge--danger {
      border-color: rgba(239, 68, 68, 0.25);
      background: rgba(239, 68, 68, 0.08);
      color: #b91c1c;
   }
   .ab-badge--info {
      border-color: rgba(59, 130, 246, 0.25);
      background: rgba(59, 130, 246, 0.08);
      color: #1d4ed8;
   }
   .ab-flow-step {
      position: relative;
      flex: 1;
      min-width: 0;
   }
   .ab-flow-step:not(:last-child)::after {
      content: '';
      position: absolute;
      top: 1.125rem;
      right: -0.5rem;
      width: 1rem;
      height: 2px;
      background: rgba(57, 82, 188, 0.15);
   }
   .ab-flow-dot {
      width: 2.25rem;
      height: 2.25rem;
      border-radius: 9999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 700;
      border: 2px solid rgba(57, 82, 188, 0.15);
      background: #fff;
      color: #3952bc;
   }
   .ab-flow-dot--active {
      border-color: #3952bc;
      background: rgba(57, 82, 188, 0.08);
   }
   .ab-flow-dot--done {
      border-color: #10b981;
      background: rgba(16, 185, 129, 0.1);
      color: #047857;
   }
   .ab-flow-dot--warn {
      border-color: #f59e0b;
      background: rgba(245, 158, 11, 0.1);
      color: #b45309;
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
      text-transform: none;
      letter-spacing: 0;
      color: #475569;
      vertical-align: bottom;
      line-height: 1.35;
      white-space: nowrap;
   }
   .ab-sheet-table tbody td {
      border: 1px solid #e2e8f0;
      padding: 0.5rem 0.625rem;
      vertical-align: top;
      color: #1e293b;
      line-height: 1.45;
   }
   .ab-sheet-table tbody tr:hover td {
      background: #f8fafc;
   }
   .ab-sheet-sub {
      display: block;
      margin-top: 0.2rem;
      font-size: 9px;
      color: #64748b;
      line-height: 1.35;
   }
   .ab-sheet-muted { color: #94a3b8; }
   .ab-sheet-danger { color: #b91c1c; font-weight: 600; }
   .ab-sheet-wait { color: #2b45af; }
</style>
@endpush

@section('content')
@php
   $periodLabel = trim(($period['week'] ?? '').' · '.($period['year'] ?? ''), ' ·');
   $scrapedLabel = !empty($period['scraped_at'])
      ? \Carbon\Carbon::parse($period['scraped_at'])->format('d M Y H:i')
      : null;
   $syncStats = $syncStats ?? [
      'totalDetected' => 0, 'hsctPending' => 0, 'hsctSent' => 0, 'hsctConfirmed' => 0,
      'openBanned' => 0, 'overdueBanned' => 0, 'onTreatment' => 0, 'openUnbanned' => 0, 'closedUnbanned' => 0,
   ];
   $pollMeta = $pollMeta ?? ['lastPollAt' => null, 'lastPollStatus' => null, 'lastPollRows' => 0, 'lastPollChanges' => 0];
   $monitoringLifecycleRows = $monitoringLifecycleRows ?? collect();
   $recentChanges = $recentChanges ?? collect();
   $trackingAvailable = $trackingAvailable ?? false;

   $statCards = [
      ['label' => 'Belum Kirim HSECT', 'value' => $syncStats['hsctPending'], 'hint' => 'Menunggu dispatch', 'icon' => 'outbound'],
      ['label' => 'Terkirim HSECT', 'value' => $syncStats['hsctSent'], 'hint' => 'Menunggu konfirmasi', 'icon' => 'send'],
      ['label' => 'Dikonfirmasi HSECT', 'value' => $syncStats['hsctConfirmed'], 'hint' => 'Sync selesai', 'icon' => 'verified'],
      ['label' => 'On Treatment', 'value' => $syncStats['onTreatment'], 'hint' => 'Proses perbaikan', 'icon' => 'medical_services'],
   ];
   $ringPct = min(100, ($stats['totalBanned'] ?? 0) > 0 ? max(10, min(100, $stats['totalBanned'] * 5)) : 0);
   $ringOffset = 97.4 - (97.4 * $ringPct / 100);

   $flowSteps = [
      ['num' => 1, 'label' => 'Scraping', 'desc' => 'Tableau → DB', 'state' => $tableAvailable ? 'done' : 'warn'],
      ['num' => 2, 'label' => 'Polling', 'desc' => $pollMeta['lastPollAt'] ?: 'Belum poll', 'state' => ($pollMeta['lastPollStatus'] ?? '') === 'completed' ? 'done' : 'active'],
      ['num' => 3, 'label' => 'Deteksi', 'desc' => ($pollMeta['lastPollChanges'] ?? 0).' perubahan', 'state' => ($pollMeta['lastPollChanges'] ?? 0) > 0 ? 'active' : ''],
      ['num' => 4, 'label' => 'HSECT', 'desc' => $syncStats['hsctConfirmed'].'/'.$syncStats['totalDetected'].' sync', 'state' => $syncStats['hsctConfirmed'] > 0 ? 'done' : 'active'],
      ['num' => 5, 'label' => 'Treatment', 'desc' => $syncStats['onTreatment'].' on treatment', 'state' => $syncStats['onTreatment'] > 0 ? 'active' : ''],
      ['num' => 6, 'label' => 'Unban', 'desc' => $syncStats['closedUnbanned'].' closed', 'state' => $syncStats['closedUnbanned'] > 0 ? 'done' : ''],
   ];
@endphp

<div class="ab-overview -mt-2 space-y-7">

   {{-- Header: animasi hanya pada kolom judul agar dropdown filter tidak ter-anchor ke ancestor transform --}}
   <section class="ab-page-header pb-6">
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5 lg:gap-8">
         <div class="min-w-0 ab-fade-in">
            <nav class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.08em] text-on-surface-variant mb-2.5" aria-label="Breadcrumb">
               <span>Dashboard</span>
               <span class="material-symbols-outlined text-[13px] opacity-60">chevron_right</span>
               <span class="text-primary">Auto Banned</span>
            </nav>
            <div class="flex flex-wrap items-center gap-3">
               <h1 class="font-headline font-extrabold text-3xl sm:text-[2.125rem] text-on-background tracking-tight leading-tight">Monitoring SAP & TBC</h1>
               @if($tableAvailable)
               <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 border border-emerald-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-700">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 ab-pulse-dot"></span>
                  Live
               </span>
               @endif
            </div>
            <p class="mt-1.5 text-sm text-on-surface-variant">
               Monitoring lifecycle banned &bull; {{ $periodLabel ?: '—' }}
               @if($scrapedLabel) &bull; Scrape {{ $scrapedLabel }} @endif
               @if($trackingAvailable && ($pollMeta['lastPollAt'] ?? null)) &bull; Poll {{ $pollMeta['lastPollAt'] }} @endif
            </p>
         </div>
         <div class="shrink-0 w-full lg:w-auto">
            @include('AutoBanned.partials.filter-bar', [
               'filters' => $filters,
               'filterOptions' => $filterOptions,
            ])
         </div>
      </div>
   </section>

   @if(!$tableAvailable)
   <div class="ab-fade-in rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-4 py-3 text-sm text-on-surface-variant flex items-start gap-2" role="alert">
      <span class="material-symbols-outlined text-primary text-lg shrink-0">info</span>
      <span>Tabel <code class="font-mono text-xs">scr_auto_banned_tbc_sap</code> belum tersedia. Data akan tampil setelah scraping terpasang.</span>
   </div>
   @endif

   @if(!$trackingAvailable)
   <div class="ab-fade-in rounded-xl border border-amber-200/60 bg-amber-50 px-4 py-3 text-sm text-amber-900 flex items-start gap-2" role="alert">
      <span class="material-symbols-outlined text-amber-600 text-lg shrink-0">sync</span>
      <span>Tabel tracking status belum tersedia. Jalankan <code class="font-mono text-xs">php artisan migrate</code> lalu polling otomatis akan aktif tiap menit.</span>
   </div>
   @endif

   {{-- Flow Pipeline --}}
   <section class="ab-fade-in ab-surface-card rounded-2xl p-5 sm:p-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
         <div>
            <h2 class="font-headline font-semibold text-sm text-on-background">Alur Proses Auto Banned</h2>
            <p class="text-xs text-on-surface-variant mt-0.5">Scraping → Polling → Deteksi perubahan → HSECT → Treatment → Unban</p>
         </div>
         @if($trackingAvailable)
         <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/[0.06] border border-primary/10 px-3 py-1 text-[10px] font-semibold text-primary">
            <span class="material-symbols-outlined text-sm">schedule</span>
            Poll otomatis setiap 1 menit
         </span>
         @endif
      </div>
      <div class="flex flex-wrap gap-4 sm:gap-2">
         @foreach($flowSteps as $step)
         <div class="ab-flow-step flex flex-col items-center text-center min-w-[5.5rem]">
            <span class="ab-flow-dot {{ $step['state'] === 'done' ? 'ab-flow-dot--done' : ($step['state'] === 'active' ? 'ab-flow-dot--active' : ($step['state'] === 'warn' ? 'ab-flow-dot--warn' : '')) }}">{{ $step['num'] }}</span>
            <p class="mt-2 text-[11px] font-semibold text-on-background">{{ $step['label'] }}</p>
            <p class="text-[10px] text-on-surface-variant leading-tight mt-0.5 max-w-[7rem]">{{ $step['desc'] }}</p>
         </div>
         @endforeach
      </div>
   </section>

   {{-- KPI --}}
   <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-4">
      <div class="ab-fade-in ab-fade-in-delay-1 ab-surface-card ab-stat-accent rounded-2xl p-6 xl:col-span-4 flex flex-col justify-between min-h-[168px]">
         <div class="flex items-start justify-between gap-3">
            <div>
               <p class="text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant">Total Banned</p>
               <p class="mt-3 font-headline font-bold text-5xl tabular-nums text-on-background leading-none">{{ number_format($stats['totalBanned']) }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/[0.08] text-primary">
               <span class="material-symbols-outlined text-2xl">block</span>
            </div>
         </div>
         <div class="mt-5 flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-1 rounded-lg bg-[#f1f5f9] px-2.5 py-1 text-[11px] font-medium text-on-surface-variant">
               <span class="material-symbols-outlined text-sm text-primary/70">badge</span> {{ number_format($stats['totalSid']) }} SID
            </span>
            <span class="inline-flex items-center gap-1 rounded-lg bg-[#f1f5f9] px-2.5 py-1 text-[11px] font-medium text-on-surface-variant">
               <span class="material-symbols-outlined text-sm text-amber-500/80">warning</span> {{ number_format($syncStats['overdueBanned']) }} overdue
            </span>
            <span class="inline-flex items-center gap-1 rounded-lg bg-[#f1f5f9] px-2.5 py-1 text-[11px] font-medium text-on-surface-variant">
               <span class="material-symbols-outlined text-sm text-emerald-500/80">check_circle</span> {{ number_format($syncStats['closedUnbanned']) }} unbanned
            </span>
         </div>
      </div>

      @foreach($statCards as $i => $stat)
      <div class="ab-fade-in ab-fade-in-delay-{{ min($i + 2, 4) }} ab-surface-card ab-stat-accent rounded-2xl p-5 xl:col-span-2 flex flex-col justify-between min-h-[140px]">
         <div class="flex items-center justify-between">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-on-surface-variant">{{ $stat['label'] }}</span>
            <span class="material-symbols-outlined text-lg text-primary/40">{{ $stat['icon'] }}</span>
         </div>
         <div class="mt-2">
            <p class="font-headline font-bold text-3xl tabular-nums text-on-background">{{ number_format($stat['value']) }}</p>
            <p class="mt-1 text-[11px] text-on-surface-variant/80">{{ $stat['hint'] }}</p>
         </div>
      </div>
      @endforeach
   </section>

   {{-- Main --}}
   <section class="grid grid-cols-1 xl:grid-cols-12 gap-5">
      <div class="xl:col-span-8 space-y-5">
         {{-- Monitoring lifecycle --}}
         <div class="ab-fade-in ab-fade-in-delay-2 ab-surface-card rounded-2xl overflow-hidden">
            <div class="px-5 sm:px-6 pt-5 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-outline-variant/10">
               <div>
                  <h2 class="font-headline font-semibold text-base text-on-background">Daftar Monitoring Lifecycle</h2>
                  <p class="text-xs text-on-surface-variant mt-0.5">
                     Status lifecycle per SID dari polling &amp; HSECT &bull; {{ $periodLabel }}
                  </p>
               </div>
               <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
                  <div class="inline-flex p-1 rounded-xl bg-[#f1f5f9]/80 gap-0.5" role="tablist" aria-label="Jenis monitoring">
                  <button type="button" role="tab" id="ab-mon-tab-banned" data-ab-mon-tab="banned" aria-selected="true" aria-controls="ab-mon-panel-banned" class="ab-tab-btn rounded-lg px-4 py-2 text-xs font-semibold text-on-surface-variant">Banned</button>
                  <button type="button" role="tab" id="ab-mon-tab-unban" data-ab-mon-tab="unban" aria-selected="false" aria-controls="ab-mon-panel-unban" class="ab-tab-btn rounded-lg px-4 py-2 text-xs font-semibold text-on-surface-variant">Unbanned</button>
                  <button type="button" role="tab" id="ab-mon-tab-changes" data-ab-mon-tab="changes" aria-selected="false" aria-controls="ab-mon-panel-changes" class="ab-tab-btn rounded-lg px-4 py-2 text-xs font-semibold text-on-surface-variant">Perubahan</button>
                  </div>
               </div>
            </div>

            <div id="ab-mon-panel-banned" role="tabpanel" aria-labelledby="ab-mon-tab-banned" data-ab-mon-panel="banned" class="overflow-x-auto">
               <table class="ab-sheet-table w-full min-w-[1400px] text-left">
                  <thead>
                     <tr>
                        <th>Nama</th>
                        <th>SID</th>
                        <th>Status System</th>
                        <th>Status Follow up</th>
                        <th>SLA Banned (Hari)<br><span class="font-normal text-[9px] text-slate-400">Maks 3 hari</span></th>
                        <th>Status Banned</th>
                        <th>Remaining Banned</th>
                        <th>Status Treatment</th>
                        <th>Status Verifikasi Treatment</th>
                        <th>SLA Unbanned (Hari)<br><span class="font-normal text-[9px] text-slate-400">Maks 3 hari</span></th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse($monitoringLifecycleRows as $row)
                     <tr>
                        <td class="font-medium">{{ $row['karyawan'] ?: '—' }}</td>
                        <td class="font-mono font-semibold text-primary">{{ $row['sid'] ?: '—' }}</td>
                        <td>{{ $row['systemStatus']->label() ?? '—' }}</td>
                        <td>{{ $row['followUpLabel'] ?? '—' }}</td>
                        <td>
                           @if(($row['slaBannedLabel'] ?? '—') !== '—')
                           <span class="{{ ($row['slaBannedTone'] ?? '') === 'danger' ? 'ab-sheet-danger' : 'ab-sheet-wait' }}">{{ $row['slaBannedLabel'] }}</span>
                           @if(!empty($row['slaBannedDetail']) && $row['slaBannedDetail'] !== '—')
                           <span class="ab-sheet-sub">{{ $row['slaBannedDetail'] }}</span>
                           @endif
                           @else
                           <span class="ab-sheet-muted">—</span>
                           @endif
                        </td>
                        <td>{{ $row['banStatus']->label() ?? '—' }}</td>
                        <td>
                           @if(($row['remainingBannedLabel'] ?? '—') !== '—')
                           <span>{{ $row['remainingBannedLabel'] }}</span>
                           @if(!empty($row['remainingBannedDetail']) && $row['remainingBannedDetail'] !== '—')
                           <span class="ab-sheet-sub">{{ $row['remainingBannedDetail'] }}</span>
                           @endif
                           @else
                           <span class="ab-sheet-muted">—</span>
                           @endif
                        </td>
                        <td><span class="ab-sheet-muted">—</span></td>
                        <td>{{ $row['verificationStatus']->label() ?? '—' }}</td>
                        <td>
                           @if(($row['slaUnbannedLabel'] ?? '—') !== '—')
                           <span class="{{ ($row['slaUnbannedTone'] ?? '') === 'danger' ? 'ab-sheet-danger' : (($row['slaUnbannedTone'] ?? '') === 'wait' ? 'ab-sheet-wait' : '') }}">{{ $row['slaUnbannedLabel'] }}</span>
                           @if(!empty($row['slaUnbannedDetail']) && $row['slaUnbannedDetail'] !== '—')
                           <span class="ab-sheet-sub">{{ $row['slaUnbannedDetail'] }}</span>
                           @endif
                           @else
                           <span class="ab-sheet-muted">—</span>
                           @endif
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="10" class="px-4 py-12 text-center">
                           <div class="ab-empty-illus mx-auto max-w-sm rounded-2xl px-6 py-8 text-center">
                              <span class="material-symbols-outlined text-4xl text-primary/25 mb-3 block">person_off</span>
                              <p class="text-sm font-medium text-on-surface">Tidak ada data monitoring</p>
                              <p class="text-xs text-on-surface-variant mt-1">Sesuaikan filter atau jalankan polling scraping</p>
                           </div>
                        </td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>

            <div id="ab-mon-panel-unban" role="tabpanel" aria-labelledby="ab-mon-tab-unban" data-ab-mon-panel="unban" class="hidden overflow-x-auto">
               <div class="px-5 sm:px-6 py-3 flex justify-end border-b border-outline-variant/5 bg-[#fafbfc]/50">
                  <a href="{{ route('auto-banned.inputasi.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary-dim transition-colors">
                     <span class="material-symbols-outlined text-base">add_circle</span>
                     Ajukan Unbanned
                  </a>
               </div>
               <table class="ab-table-soft w-full min-w-[640px] text-left">
                  <thead class="bg-[#fafbfc]/80">
                     <tr class="border-b border-outline-variant/10">
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">No</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Karyawan</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">SID</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Alasan</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Status</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Tanggal</th>
                     </tr>
                  </thead>
                  <tbody class="divide-y divide-outline-variant/5">
                     @forelse($unbanRows as $index => $row)
                     @php
                        $badgeClass = match ($row->status->value) {
                           'approved' => 'ab-badge--ok',
                           'rejected' => 'ab-badge--muted',
                           default => 'ab-badge--wait',
                        };
                     @endphp
                     <tr>
                        <td class="px-4 py-3 text-xs text-on-surface-variant tabular-nums">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                           <p class="text-xs font-semibold text-on-background">{{ $row->karyawan }}</p>
                           <p class="text-[10px] text-on-surface-variant">{{ $row->perusahaan ?: '—' }}</p>
                        </td>
                        <td class="px-4 py-3 text-xs font-mono font-semibold text-primary">{{ $row->sid }}</td>
                        <td class="px-4 py-3 text-xs text-on-surface-variant max-w-[180px] truncate" title="{{ $row->alasan_pengajuan }}">{{ \Illuminate\Support\Str::limit($row->alasan_pengajuan, 50) }}</td>
                        <td class="px-4 py-3"><span class="ab-badge {{ $badgeClass }}">{{ $row->status->label() }}</span></td>
                        <td class="px-4 py-3 text-[11px] text-on-surface-variant whitespace-nowrap">{{ $row->created_at?->format('d M Y H:i') }}</td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="6" class="px-4 py-12">
                           <div class="ab-empty-illus mx-auto max-w-sm rounded-2xl px-6 py-8 text-center">
                              <span class="material-symbols-outlined text-4xl text-primary/25 mb-3 block">inbox</span>
                              <p class="text-sm font-medium text-on-surface">Belum ada pengajuan unbanned</p>
                              <p class="text-xs text-on-surface-variant mt-1">Pengajuan baru akan muncul di sini</p>
                           </div>
                        </td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>

            <div id="ab-mon-panel-changes" role="tabpanel" aria-labelledby="ab-mon-tab-changes" data-ab-mon-panel="changes" class="hidden overflow-x-auto">
               <table class="ab-table-soft w-full min-w-[720px] text-left">
                  <thead class="bg-[#fafbfc]/80">
                     <tr class="border-b border-outline-variant/10">
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Waktu Deteksi</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">SID</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Karyawan</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Perubahan</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Dari</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Ke</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Scrape</th>
                     </tr>
                  </thead>
                  <tbody class="divide-y divide-outline-variant/5">
                     @forelse($recentChanges as $change)
                     <tr>
                        <td class="px-4 py-3 text-[11px] text-on-surface-variant whitespace-nowrap">{{ $change->detected_at?->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 text-xs font-mono font-semibold text-primary">{{ $change->sid }}</td>
                        <td class="px-4 py-3 text-xs text-on-background">{{ $change->snapshot?->karyawan ?: '—' }}</td>
                        <td class="px-4 py-3"><span class="ab-badge ab-badge--info">{{ $change->change_type->label() }}</span></td>
                        <td class="px-4 py-3 text-xs text-on-surface-variant">{{ $change->from_system_status ?: '—' }}</td>
                        <td class="px-4 py-3 text-xs font-medium text-on-background">{{ $change->to_system_status }}</td>
                        <td class="px-4 py-3 text-[10px] text-on-surface-variant whitespace-nowrap">{{ $change->scr_scraped_at?->format('d M Y H:i') ?: '—' }}</td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-on-surface-variant">
                           Belum ada perubahan status terdeteksi. Polling berjalan otomatis setiap menit.
                        </td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>

         {{-- Detail --}}
         <div class="ab-fade-in ab-fade-in-delay-3 ab-surface-card rounded-2xl overflow-hidden">
            <div class="px-5 sm:px-6 pt-5 pb-4 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 border-b border-outline-variant/10">
               <div>
                  <h2 class="font-headline font-semibold text-base text-on-background">Detail Alasan Banned</h2>
                  <p class="text-xs text-on-surface-variant mt-0.5">SAP, TBC, percentile & alasan blokir SID</p>
               </div>
               <form method="GET" action="{{ route('auto-banned.index') }}" class="flex items-center gap-2 self-start lg:self-auto">
                  @foreach(['site', 'week', 'year', 'perusahaan'] as $filterKey)
                     @if(($filters[$filterKey] ?? '') !== '')
                     <input type="hidden" name="{{ $filterKey }}" value="{{ $filters[$filterKey] }}"/>
                     @endif
                  @endforeach
                  <div class="relative">
                     <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/50 text-base pointer-events-none">search</span>
                     <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari SID, nama, alasan…" class="rounded-xl border border-outline-variant/25 bg-[#f8fafc] pl-9 pr-3 py-2.5 text-xs font-medium text-on-surface w-52 sm:w-64 focus:border-primary/30 focus:ring-2 focus:ring-primary/10"/>
                  </div>
               </form>
            </div>
            <div class="overflow-x-auto">
               <table class="ab-table-soft w-full min-w-[920px] text-left">
                  <thead class="bg-[#fafbfc]/80">
                     <tr class="border-b border-outline-variant/10">
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">No</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">SID</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Karyawan</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Perusahaan</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Site</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">SAP</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">TBC</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Alasan</th>
                        <th class="px-4 py-3 text-[10px] uppercase text-on-surface-variant/70">Status</th>
                     </tr>
                  </thead>
                  <tbody class="divide-y divide-outline-variant/5">
                     @forelse($bannedRows as $index => $row)
                     <tr>
                        <td class="px-4 py-3 text-xs text-on-surface-variant">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-xs font-mono font-semibold text-primary">{{ $row['sid'] ?: '—' }}</td>
                        <td class="px-4 py-3 text-xs font-medium text-on-background">{{ $row['karyawan'] ?: '—' }}</td>
                        <td class="px-4 py-3 text-xs text-on-surface-variant">{{ $row['perusahaan'] ?: '—' }}</td>
                        <td class="px-4 py-3 text-xs text-on-surface">{{ $row['site'] ?: '—' }}</td>
                        <td class="px-4 py-3 text-xs tabular-nums">
                           {{ $row['sap'] !== '' ? $row['sap'] : '—' }}
                           @if($row['sapPercentile'] !== '')
                           <span class="text-[10px] text-on-surface-variant">({{ $row['sapPercentile'] }})</span>
                           @endif
                        </td>
                        <td class="px-4 py-3 text-xs tabular-nums">{{ $row['tbc'] !== '' ? $row['tbc'] : '—' }}</td>
                        <td class="px-4 py-3 text-xs text-on-surface-variant max-w-[200px] truncate" title="{{ $row['reason'] }}">{{ $row['reason'] ?: '—' }}</td>
                        <td class="px-4 py-3"><span class="ab-badge ab-badge--muted">{{ $row['status'] ?: '—' }}</span></td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="9" class="px-6 py-14 text-center text-sm text-on-surface-variant">Tidak ada data untuk filter ini.</td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
      </div>

      {{-- Sidebar --}}
      <aside class="xl:col-span-4 space-y-4">
         <div class="ab-fade-in ab-fade-in-delay-2 ab-surface-card rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
               <h3 class="font-headline font-semibold text-sm text-on-background">Ringkasan Periode</h3>
               @if(($period['week'] ?? '') !== '')
               <span class="text-[10px] font-bold uppercase tracking-wider text-primary bg-primary/[0.08] px-2 py-0.5 rounded-md">{{ $period['week'] }}</span>
               @endif
            </div>
            <div class="flex items-center gap-4">
               <div class="relative shrink-0">
                  <svg class="w-20 h-20 -rotate-90" viewBox="0 0 36 36" aria-hidden="true">
                     <circle class="ab-ring-track" cx="18" cy="18" r="15.5" fill="none" stroke-width="2.5"/>
                     <circle class="ab-ring-fill" cx="18" cy="18" r="15.5" fill="none" stroke-width="2.5" stroke-dasharray="97.4" stroke-dashoffset="{{ $ringOffset }}"/>
                  </svg>
                  <span class="absolute inset-0 flex items-center justify-center font-headline font-bold text-lg text-on-background tabular-nums">{{ $stats['totalBanned'] }}</span>
               </div>
               <div class="min-w-0 space-y-2 text-xs text-on-surface-variant">
                  <p><span class="font-semibold text-on-surface">{{ $stats['totalSid'] }}</span> SID unik banned</p>
                  <p><span class="font-semibold text-on-surface">{{ $unbanRows->count() }}</span> total pengajuan unban</p>
                  <p><span class="font-semibold text-primary">{{ $stats['pendingUnban'] }}</span> menunggu review</p>
               </div>
            </div>
         </div>

         <div class="ab-fade-in ab-fade-in-delay-3 ab-surface-card rounded-2xl p-5">
            <h3 class="font-headline font-semibold text-sm text-on-background mb-4">Aksi Cepat</h3>
            <div class="space-y-2">
               <a href="{{ route('auto-banned.inputasi.index') }}" class="group flex w-full items-center gap-3 rounded-xl border border-transparent bg-[#f8fafc] px-4 py-3 transition-all duration-300 hover:border-primary/15 hover:bg-primary/[0.04]">
                  <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white shadow-sm text-primary transition-transform duration-300 group-hover:scale-105">
                     <span class="material-symbols-outlined text-xl">how_to_reg</span>
                  </span>
                  <div class="min-w-0">
                     <p class="text-sm font-semibold text-on-background">Ajukan Unbanned</p>
                     <p class="text-[11px] text-on-surface-variant truncate">Form pengajuan SID</p>
                  </div>
                  <span class="material-symbols-outlined text-on-surface-variant/40 ml-auto text-lg transition-transform duration-300 group-hover:translate-x-0.5">arrow_forward</span>
               </a>
               <a href="{{ route('auto-banned.hsct-email.index') }}" class="group flex w-full items-center gap-3 rounded-xl border border-transparent bg-[#f8fafc] px-4 py-3 transition-all duration-300 hover:border-primary/15 hover:bg-primary/[0.04]">
                  <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white shadow-sm text-primary transition-transform duration-300 group-hover:scale-105">
                     <span class="material-symbols-outlined text-xl">mail</span>
                  </span>
                  <div class="min-w-0">
                     <p class="text-sm font-semibold text-on-background">Riwayat Email HSECT</p>
                     <p class="text-[11px] text-on-surface-variant truncate">Campaign email awal & reminder</p>
                  </div>
                  <span class="material-symbols-outlined text-on-surface-variant/40 ml-auto text-lg transition-transform duration-300 group-hover:translate-x-0.5">arrow_forward</span>
               </a>
               <a href="{{ route('auto-banned.master-data.index') }}" class="group flex items-center gap-3 rounded-xl border border-transparent bg-[#f8fafc] px-4 py-3 transition-all duration-300 hover:border-primary/15 hover:bg-primary/[0.04]">
                  <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white shadow-sm text-primary transition-transform duration-300 group-hover:scale-105">
                     <span class="material-symbols-outlined text-xl">database</span>
                  </span>
                  <div class="min-w-0">
                     <p class="text-sm font-semibold text-on-background">Master Data</p>
                     <p class="text-[11px] text-on-surface-variant truncate">Referensi & konfigurasi</p>
                  </div>
                  <span class="material-symbols-outlined text-on-surface-variant/40 ml-auto text-lg transition-transform duration-300 group-hover:translate-x-0.5">arrow_forward</span>
               </a>
            </div>
         </div>

         <div class="ab-fade-in ab-fade-in-delay-4 ab-surface-card rounded-2xl p-5">
            <h3 class="font-headline font-semibold text-sm text-on-background mb-3">Sync HSECT</h3>
            <div class="space-y-2 text-xs">
               <div class="flex justify-between items-center rounded-lg bg-[#f8fafc] px-3 py-2">
                  <span class="text-on-surface-variant">Terdeteksi Not Passed</span>
                  <span class="font-bold text-on-background tabular-nums">{{ $syncStats['totalDetected'] }}</span>
               </div>
               <div class="flex justify-between items-center rounded-lg bg-[#f8fafc] px-3 py-2">
                  <span class="text-on-surface-variant">Belum dikirim</span>
                  <span class="font-bold text-primary tabular-nums">{{ $syncStats['hsctPending'] }}</span>
               </div>
               <div class="flex justify-between items-center rounded-lg bg-[#f8fafc] px-3 py-2">
                  <span class="text-on-surface-variant">Terkirim</span>
                  <span class="font-bold text-on-background tabular-nums">{{ $syncStats['hsctSent'] }}</span>
               </div>
               <div class="flex justify-between items-center rounded-lg bg-[#f8fafc] px-3 py-2">
                  <span class="text-on-surface-variant">Dikonfirmasi</span>
                  <span class="font-bold text-emerald-700 tabular-nums">{{ $syncStats['hsctConfirmed'] }}</span>
               </div>
            </div>
            @if($trackingAvailable && $syncStats['totalDetected'] > 0)
            @php $syncPct = (int) round(($syncStats['hsctConfirmed'] / max(1, $syncStats['totalDetected'])) * 100); @endphp
            <div class="mt-3 h-1.5 rounded-full bg-[#e8ecf4] overflow-hidden">
               <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" style="width: {{ $syncPct }}%"></div>
            </div>
            <p class="text-[10px] text-on-surface-variant mt-1.5">{{ $syncPct }}% SID sudah dikonfirmasi HSECT</p>
            @endif
         </div>

         <div class="ab-fade-in ab-fade-in-delay-4 ab-surface-card rounded-2xl p-5">
            <h3 class="font-headline font-semibold text-sm text-on-background mb-3">Status Unban</h3>
            <div class="flex flex-wrap gap-2">
               <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f1f5f9] border border-outline-variant/20 px-3 py-1.5 text-[11px] font-medium text-on-surface-variant">
                  <span class="w-1.5 h-1.5 rounded-full bg-primary/60"></span>
                  {{ $stats['pendingUnban'] }} Pending
               </span>
               <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f1f5f9] border border-outline-variant/20 px-3 py-1.5 text-[11px] font-medium text-on-surface-variant">
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                  {{ $stats['approvedUnban'] }} Disetujui
               </span>
               <span class="inline-flex items-center gap-1.5 rounded-full bg-[#f1f5f9] border border-outline-variant/20 px-3 py-1.5 text-[11px] font-medium text-on-surface-variant">
                  <span class="material-symbols-outlined text-sm">block</span>
                  {{ $stats['rejectedUnban'] }} Ditolak
               </span>
            </div>
         </div>

         @if($scrapedLabel)
         <div class="ab-fade-in ab-fade-in-delay-4 ab-surface-card rounded-2xl p-5">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-on-surface-variant mb-1">Sumber Data</p>
            <p class="text-xs text-on-surface-variant leading-relaxed">Tableau SAP PJ TBC All Site</p>
            <p class="text-[10px] text-on-surface-variant/70 mt-2">Update: {{ $scrapedLabel }}</p>
         </div>
         @endif
      </aside>
   </section>
</div>
@endsection

@push('scripts')
<script>
(function () {
   var tabs = document.querySelectorAll('[data-ab-mon-tab]');
   var panels = document.querySelectorAll('[data-ab-mon-panel]');
   if (!tabs.length) return;

   tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
         var name = tab.getAttribute('data-ab-mon-tab');
         tabs.forEach(function (t) {
            t.setAttribute('aria-selected', t.getAttribute('data-ab-mon-tab') === name ? 'true' : 'false');
         });
         panels.forEach(function (panel) {
            panel.classList.toggle('hidden', panel.getAttribute('data-ab-mon-panel') !== name);
         });
      });
   });
})();
</script>
@endpush
