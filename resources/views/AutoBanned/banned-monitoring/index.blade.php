@extends('AutoBanned.layouts.app')

@section('title', 'Monitoring Daily Banned')

@section('page-header')
@endsection

@push('head')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Two+Tone" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
   .ab-phppot {
      --cyan: #01b0c6;
      --blue-line: #0000ff;
      --primary: #01b0c6;
      --card-shadow: 0 1px 20px 0 rgba(69, 90, 100, 0.08);
      --text-muted: #888;
      --text-dark: #333;
      --border-light: #f1f1f1;
   }
   .ab-phppot { color: var(--text-dark); font-size: 14px; }
   .ab-phppot .material-icons-two-tone {
      font-family: 'Material Icons Two Tone';
      font-weight: normal;
      font-style: normal;
      font-size: 42px;
      line-height: 1;
      letter-spacing: normal;
      text-transform: none;
      display: inline-block;
      white-space: nowrap;
      word-wrap: normal;
      direction: ltr;
      color: var(--primary);
   }
   .ab-phppot .text-primary { color: var(--primary) !important; }

   /* ── Bootstrap-like row/col gutter ── */
   .ab-phppot .dash-row { display: flex; flex-wrap: wrap; margin: 0 -12px; }
   .ab-phppot .dash-col-6 { width: 50%; padding: 0 12px; }
   .ab-phppot .dash-col-12 { width: 100%; padding: 0 12px; }
   @media (max-width: 1199px) {
      .ab-phppot .dash-col-xl-6 { width: 100%; }
   }
   @media (min-width: 1200px) {
      .ab-phppot .dash-col-xl-6 { width: 50%; padding: 0 12px; }
   }
   @media (max-width: 767px) {
      .ab-phppot .dash-col-6 { width: 100%; }
   }

   /* ── Product card (prod-p-card) ── */
   .ab-phppot .prod-p-card {
      background: #fff;
      border: none;
      border-radius: 5px;
      box-shadow: var(--card-shadow);
      margin-bottom: 24px;
   }
   .ab-phppot .prod-p-card .card-body { padding: 20px 25px; }
   .ab-phppot .prod-p-card h6.m-b-5 {
      margin-bottom: 5px;
      font-size: 14px;
      font-weight: 400;
      color: var(--text-muted);
   }
   .ab-phppot .prod-p-card h3.mb-0 {
      font-size: 28px;
      font-weight: 600;
      color: var(--text-dark);
      margin: 0;
      line-height: 1.2;
   }
   .ab-phppot .prod-p-card .card-icon-col { flex-shrink: 0; }

   /* ── Generic card ── */
   .ab-phppot .dash-card {
      background: #fff;
      border: none;
      border-radius: 5px;
      box-shadow: var(--card-shadow);
      margin-bottom: 24px;
   }
   .ab-phppot .card-header {
      padding: 16px 20px;
      background: transparent;
      border-bottom: 1px solid var(--border-light);
   }
   .ab-phppot .card-header h5 {
      margin: 0;
      font-size: 15px;
      font-weight: 600;
      color: var(--text-dark);
   }
   .ab-phppot .card-body { padding: 20px; }
   .ab-phppot .card-body.p-0 { padding: 0; }

   /* ── Support bar widgets ── */
   .ab-phppot .support-bar { overflow: hidden; }
   .ab-phppot .support-bar .card-body.pb-0 { padding-bottom: 0; }
   .ab-phppot .support-bar h2.m-0 {
      font-size: 32px;
      font-weight: 600;
      margin: 0;
      color: var(--text-dark);
      line-height: 1.2;
   }
   .ab-phppot .support-bar .label-cyan {
      color: var(--cyan);
      font-size: 14px;
      font-weight: 400;
      display: block;
      margin-top: 2px;
   }
   .ab-phppot .support-bar .widget-desc {
      font-size: 13px;
      color: var(--text-muted);
      margin: 12px 0 16px;
   }
   .ab-phppot .card-footer {
      padding: 14px 0;
      background: #fff;
      border-top: 1px solid var(--border-light);
   }
   .ab-phppot .card-footer.border-0 { border: none; }
   .ab-phppot .card-footer.bg-cyan {
      background-color: var(--cyan) !important;
      color: #fff;
   }
   .ab-phppot .card-footer .footer-stat h4 {
      font-size: 18px;
      font-weight: 600;
      margin: 0 0 2px;
   }
   .ab-phppot .card-footer .footer-stat span {
      font-size: 12px;
      opacity: 0.9;
   }
   .ab-phppot .card-footer.bg-cyan .footer-stat h4 { color: #fff; }
   .ab-phppot .card-footer:not(.bg-cyan) .footer-stat h4 { color: var(--text-dark); }
   .ab-phppot .card-footer:not(.bg-cyan) .footer-stat span { color: var(--text-muted); opacity: 1; }
   .ab-phppot .footer-row {
      display: flex;
      text-align: center;
   }
   .ab-phppot .footer-row > div { flex: 1; }

   /* ── Monthly report ── */
   .ab-phppot .report-metrics {
      display: flex;
      gap: 2rem;
      padding-bottom: 8px;
   }
   .ab-phppot .report-metrics h3 {
      font-size: 24px;
      font-weight: 600;
      margin: 0 0 4px;
      color: var(--text-dark);
   }
   .ab-phppot .report-metrics span {
      font-size: 13px;
      color: var(--text-muted);
   }
   .ab-phppot .chart-main { height: 350px; }

   /* ── Customer satisfaction ── */
   .ab-phppot .satisfaction h6 {
      font-size: 14px;
      font-weight: 600;
      margin: 0 0 6px;
      color: var(--text-dark);
   }
   .ab-phppot .satisfaction > span {
      font-size: 13px;
      color: var(--text-muted);
      line-height: 1.6;
      display: block;
   }
   .ab-phppot .chart-pie { height: 260px; }

   /* ── Wishlist table ── */
   .ab-phppot .wishlist-table { width: 100%; border-collapse: collapse; margin: 0; }
   .ab-phppot .wishlist-table thead th {
      padding: 12px 20px;
      font-size: 12px;
      font-weight: 600;
      color: var(--text-muted);
      text-align: left;
      border-bottom: 1px solid var(--border-light);
      background: #fafafa;
      white-space: nowrap;
   }
   .ab-phppot .wishlist-table thead th.sortable { cursor: pointer; }
   .ab-phppot .wishlist-table thead th .sort-icon {
      font-size: 11px;
      margin-left: 4px;
      opacity: 0.5;
   }
   .ab-phppot .wishlist-table tbody td {
      padding: 14px 20px;
      font-size: 13px;
      border-bottom: 1px solid #f5f5f5;
      vertical-align: middle;
      color: var(--text-dark);
   }
   .ab-phppot .wishlist-table tbody tr:hover { background: #fafbfc; }
   .ab-phppot .wishlist-table tbody tr:last-child td { border-bottom: none; }
   .ab-phppot .wishlist-scroll { max-height: 340px; overflow-y: auto; }
   .ab-phppot .wishlist-scroll::-webkit-scrollbar { width: 4px; }
   .ab-phppot .wishlist-scroll::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
   .ab-phppot .item-thumb {
      width: 32px; height: 32px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 10px;
      flex-shrink: 0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 700;
      color: #fff;
   }
   .ab-phppot .item-desc {
      display: inline-flex;
      align-items: center;
      min-width: 0;
   }
   .ab-phppot .item-desc .name {
      font-weight: 500;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 130px;
      display: inline-block;
      vertical-align: middle;
   }
   .ab-phppot .reason-cell {
      max-width: 180px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      color: var(--text-muted);
      font-size: 12px;
   }
   .ab-phppot .badge {
      display: inline-block;
      padding: 4px 10px;
      font-size: 11px;
      font-weight: 600;
      border-radius: 4px;
      line-height: 1.2;
   }
   .ab-phppot .badge-success { background: #1de9b6; color: #fff; }
   .ab-phppot .badge-danger { background: #f44236; color: #fff; }
   .ab-phppot .badge-warning { background: #f4c22b; color: #fff; }
   .ab-phppot .badge-info { background: #3ebfea; color: #fff; }
   .ab-phppot .badge-secondary { background: #a9b4bc; color: #fff; }
   .ab-phppot .action-btns { white-space: nowrap; }
   .ab-phppot .action-btns a, .ab-phppot .action-btns span {
      display: inline-block;
      font-size: 16px;
      cursor: pointer;
      text-decoration: none;
   }
   .ab-phppot .action-btns .act-edit { color: #1de9b6; }
   .ab-phppot .action-btns .act-del { color: #f44236; margin-left: 12px; }

   .ab-phppot .list-tabs {
      display: flex;
      gap: 4px;
      margin-left: auto;
      flex-shrink: 0;
   }
   .ab-phppot .card-header-with-tabs {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
   }
   .ab-phppot .list-tab-btn {
      font-size: 12px;
      font-weight: 600;
      padding: 6px 14px;
      border-radius: 4px;
      border: none;
      background: transparent;
      color: var(--text-muted);
      cursor: pointer;
      transition: all 0.2s;
      white-space: nowrap;
   }
   .ab-phppot .list-tab-btn:hover { color: var(--cyan); background: rgba(1, 176, 198, 0.08); }
   .ab-phppot .list-tab-btn[aria-selected="true"] {
      background: var(--cyan);
      color: #fff;
   }
   .ab-phppot .list-tab-btn .tab-count {
      display: inline-block;
      margin-left: 4px;
      font-size: 10px;
      opacity: 0.85;
   }
   .ab-phppot .list-panel.hidden { display: none; }

   .ab-phppot .page-top {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 20px;
   }
   .ab-phppot .page-top h1 {
      font-size: 18px;
      font-weight: 600;
      margin: 0;
      color: var(--text-dark);
   }
   .ab-phppot .page-top p { font-size: 12px; color: var(--text-muted); margin: 2px 0 0; }
   .ab-phppot .right-col-stretch { display: flex; flex-direction: column; }
   .ab-phppot .right-col-stretch > .dash-card { flex: 1; display: flex; flex-direction: column; }
   .ab-phppot .right-col-stretch .card-body { flex: 1; display: flex; flex-direction: column; }
   .ab-phppot .right-col-stretch .chart-main { flex: 1; min-height: 350px; }
</style>
@endpush

@section('content')
@php
   $periodLabel = !empty($period['filter_date'])
      ? \Carbon\Carbon::parse($period['filter_date'])->format('d M Y')
      : '—';
   $scrapedLabel = !empty($period['scraped_at'])
      ? \Carbon\Carbon::parse($period['scraped_at'])->format('d M Y H:i')
      : null;
   $chartData = $chartData ?? [];
   $scrTableAvailable = $scrTableAvailable ?? false;

   $successRate = (float) ($stats['successRate'] ?? 0);
   $trend = $chartData['dailyTrend'] ?? ['labels' => [], 'total' => [], 'success' => [], 'failed' => []];
   $trendLabels = $trend['labels'] ?? [];
   $trendTotal = $trend['total'] ?? [];
   $trendSuccess = $trend['success'] ?? [];
   $trendTotalSum = array_sum($trendTotal);
   $trendAvg = count($trendTotal) > 0 ? round(array_sum($trendTotal) / count($trendTotal), 1) : 0;

   $last3Labels = array_values(array_slice($trendLabels, -3));
   $last3Total = array_values(array_slice($trendTotal, -3));
   $last3Success = array_values(array_slice($trendSuccess, -3));
   while (count($last3Labels) < 3) { array_unshift($last3Labels, '—'); array_unshift($last3Total, 0); array_unshift($last3Success, 0); }
   $last3Labels = array_slice($last3Labels, -3);
   $last3Total = array_slice($last3Total, -3);
   $last3Success = array_slice($last3Success, -3);

   $productCards = [
      ['title' => 'Total Harus Di-Banned', 'value' => number_format($stats['totalToBan']), 'icon' => 'block'],
      ['title' => 'Automasi Berhasil', 'value' => number_format($stats['success']), 'icon' => 'check_circle'],
      ['title' => 'Sudah Diproses', 'value' => number_format($stats['processed']), 'icon' => 'task_alt'],
      ['title' => 'Belum Diproses', 'value' => number_format($stats['notProcessed']), 'icon' => 'hourglass_empty'],
   ];

   $pieLabels = $chartData['byBannedStatus']['labels'] ?? [];
   $pieValues = $chartData['byBannedStatus']['values'] ?? [];
   if (empty($pieLabels) && !empty($chartData['topReasons']['labels'])) {
      $pieLabels = $chartData['topReasons']['labels'];
      $pieValues = $chartData['topReasons']['values'];
   }

   $thumbColors = ['#01b0c6', '#019aab', '#48d1e0', '#017a8a', '#33c9dc', '#00bcd4'];

   $rowsToBan = collect($bannedRows)->filter(function ($row) {
      if (! $row['isProcessed']) {
         return true;
      }
      $status = $row['automationStatus']?->value ?? '';

      return $status !== \App\Enums\AutoBannedSidAutomationStatus::Success->value;
   })->values();

   $rowsBannedDone = $logRows->filter(
      fn ($row) => $row->automation_status?->value === \App\Enums\AutoBannedSidAutomationStatus::Success->value
   )->values();

   if ($rowsBannedDone->isEmpty()) {
      $rowsBannedDone = collect($bannedRows)->filter(
         fn ($row) => $row['automationStatus']?->value === \App\Enums\AutoBannedSidAutomationStatus::Success->value
      )->values();
   }

   $avgLineData = [];
   if (count($trendTotal) > 0) {
      $sum = array_sum($trendTotal);
      $mean = $sum / count($trendTotal);
      $avgLineData = array_fill(0, count($trendTotal), round($mean, 1));
   }
@endphp

<div class="ab-phppot -mt-1">

   <div class="page-top">
      <div>
         <h1>Monitoring Daily Banned</h1>
         <p>{{ $periodLabel }}@if($scrapedLabel) &bull; Scrape {{ $scrapedLabel }}@endif</p>
      </div>
      @include('AutoBanned.partials.daily-filter-bar', [
         'filters' => $filters,
         'filterOptions' => $filterOptions,
         'filterRoute' => 'auto-banned.banned-monitoring.index',
      ])
   </div>

   @if(!$scrTableAvailable)
   <div class="dash-card card-body text-sm text-[#888] mb-3">Tabel <code>scr_daily_banned</code> belum tersedia.</div>
   @endif

   <div class="dash-row">

      {{-- ══ LEFT xl=6 ══ --}}
      <div class="dash-col-12 dash-col-xl-6">

         {{-- 4 × ProductCard --}}
         <div class="dash-row">
            @foreach($productCards as $card)
            <div class="dash-col-6">
               <div class="prod-p-card">
                  <div class="card-body">
                     <div class="flex items-center justify-between">
                        <div>
                           <h6 class="m-b-5">{{ $card['title'] }}</h6>
                           <h3 class="mb-0">{{ $card['value'] }}</h3>
                        </div>
                        <div class="card-icon-col">
                           <i class="material-icons-two-tone text-primary">{{ $card['icon'] }}</i>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            @endforeach
         </div>

         {{-- Conversion + Placed Orders --}}
         <div class="dash-row">
            <div class="dash-col-6">
               <div class="dash-card support-bar">
                  <div class="card-body pb-0">
                     <h2 class="m-0">{{ number_format($successRate, 2) }}%</h2>
                     <span class="label-cyan">Tingkat Keberhasilan Automasi</span>
                     <p class="widget-desc mb-3 mt-3">Persentase proses banned SID yang berhasil dari total yang sudah dijalankan.</p>
                  </div>
                  <div id="ab-chart-conversion"></div>
                  <div class="card-footer border-0 bg-cyan">
                     <div class="footer-row">
                        @foreach($last3Labels as $i => $lbl)
                        <div class="footer-stat">
                           <h4 class="m-0 text-white">{{ $last3Success[$i] ?? 0 }}</h4>
                           <span>{{ $lbl }}</span>
                        </div>
                        @endforeach
                     </div>
                  </div>
               </div>
            </div>
            <div class="dash-col-6">
               <div class="dash-card support-bar">
                  <div class="card-body pb-0">
                     <h2 class="m-0">{{ number_format($stats['totalToBan']) }}</h2>
                     <span class="label-cyan">Volume Daily Banned</span>
                     <p class="widget-desc mb-3 mt-3">Jumlah karyawan terdeteksi harus di-banned pada periode ini.</p>
                  </div>
                  <div class="card-footer border-0">
                     <div class="footer-row">
                        @foreach($last3Labels as $i => $lbl)
                        <div class="footer-stat">
                           <h4 class="m-0">{{ $last3Total[$i] ?? 0 }}</h4>
                           <span>{{ $lbl }}</span>
                        </div>
                        @endforeach
                     </div>
                  </div>
                  <div id="ab-chart-orders"></div>
               </div>
            </div>
         </div>
      </div>

      {{-- ══ RIGHT xl=6 — Monthly Report ══ --}}
      <div class="dash-col-12 dash-col-xl-6 right-col-stretch">
         <div class="dash-card">
            <div class="card-header">
               <h5>Laporan Trend Banned Harian</h5>
            </div>
            <div class="card-body">
               <div class="report-metrics pb-2">
                  <div>
                     <h3 class="mb-1">{{ number_format($trendTotalSum) }}</h3>
                     <span>Total</span>
                  </div>
                  <div>
                     <h3 class="mb-1">{{ $trendAvg }}</h3>
                     <span>Avg.</span>
                  </div>
               </div>
               <div class="chart-main" id="ab-chart-main"></div>
            </div>
         </div>
      </div>

      {{-- ══ BOTTOM LEFT — Customer Satisfaction ══ --}}
      <div class="dash-col-12 dash-col-xl-6">
         <div class="dash-card satisfaction">
            <div class="card-body">
               <h6>Distribusi Status Banned</h6>
               <span>Proporsi jenis status banned dan alasan utama karyawan berdasarkan data scraping Daily Banned periode terpilih.</span>
               <div class="chart-pie mt-2" id="ab-chart-pie"></div>
            </div>
         </div>
      </div>

      {{-- ══ BOTTOM RIGHT — Daftar Karyawan Banned ══ --}}
      <div class="dash-col-12 dash-col-xl-6">
         <div class="dash-card">
            <div class="card-header card-header-with-tabs">
               <h5>Daftar Karyawan Banned</h5>
               <div class="list-tabs" role="tablist" aria-label="Filter daftar karyawan">
                  <button type="button" role="tab" class="list-tab-btn" id="ab-list-tab-toban" data-ab-list-tab="toban" aria-selected="true" aria-controls="ab-list-panel-toban">
                     Akan Di-Banned<span class="tab-count">({{ $rowsToBan->count() }})</span>
                  </button>
                  <button type="button" role="tab" class="list-tab-btn" id="ab-list-tab-done" data-ab-list-tab="done" aria-selected="false" aria-controls="ab-list-panel-done">
                     Sudah Di-Banned<span class="tab-count">({{ $rowsBannedDone->count() }})</span>
                  </button>
               </div>
            </div>

            {{-- Tab: Akan Di-Banned --}}
            <div class="card-body p-0 list-panel" id="ab-list-panel-toban" data-ab-list-panel="toban" role="tabpanel" aria-labelledby="ab-list-tab-toban">
               <div class="wishlist-scroll">
                  <table class="wishlist-table">
                     <thead>
                        <tr>
                           <th class="sortable">Karyawan <span class="sort-icon">⇅</span></th>
                           <th>Status</th>
                           <th>Site</th>
                           <th>Alasan Banned</th>
                           <th>Detail</th>
                        </tr>
                     </thead>
                     <tbody>
                        @forelse($rowsToBan as $row)
                        @php
                           $initials = collect(explode(' ', $row['nama'] ?? ''))->filter()->take(2)
                              ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->join('');
                           $cIdx = abs(crc32($row['sid'] ?: $row['nama'])) % count($thumbColors);
                           $procBadge = 'badge-warning';
                           $procLabel = 'Belum Diproses';
                           if ($row['isProcessed'] && $row['automationStatus']) {
                              $procBadge = match ($row['automationStatus']->value) {
                                 'FAILED' => 'badge-danger',
                                 'PROCESSING' => 'badge-info',
                                 'PENDING' => 'badge-warning',
                                 'SKIPPED' => 'badge-secondary',
                                 default => 'badge-warning',
                              };
                              $procLabel = $row['automationStatus']->label();
                           }
                        @endphp
                        <tr>
                           <td>
                              <span class="item-desc">
                                 <span class="item-thumb" style="background:{{ $thumbColors[$cIdx] }}">{{ $initials ?: '?' }}</span>
                                 <span>
                                    <span class="name" title="{{ $row['nama'] }}">{{ $row['nama'] ?: '—' }}</span>
                                    <br><span class="text-[10px] text-[#888] font-mono">{{ $row['sid'] ?: $row['nik'] }}</span>
                                 </span>
                              </span>
                           </td>
                           <td><label class="badge {{ $procBadge }}">{{ $procLabel }}</label></td>
                           <td>{{ $row['site'] ?: '—' }}</td>
                           <td>
                              <span class="reason-cell" title="{{ $row['bannedReason'] }}">{{ $row['bannedReason'] ?: '—' }}</span>
                              @if($row['bannedStatus'])
                              <br><span class="text-[10px] text-[#aaa]">{{ $row['bannedStatus'] }}</span>
                              @endif
                           </td>
                           <td>
                              <span class="action-btns">
                                 <span class="act-edit material-symbols-outlined" title="{{ $row['bannedReason'] }}">info</span>
                                 <span class="act-del material-symbols-outlined" title="{{ $row['onsiteStatus'] }}">location_on</span>
                              </span>
                           </td>
                        </tr>
                        @empty
                        <tr>
                           <td colspan="5" class="text-center py-10" style="color:#888">Semua karyawan sudah diproses banned</td>
                        </tr>
                        @endforelse
                     </tbody>
                  </table>
               </div>
            </div>

            {{-- Tab: Sudah Di-Banned --}}
            <div class="card-body p-0 list-panel hidden" id="ab-list-panel-done" data-ab-list-panel="done" role="tabpanel" aria-labelledby="ab-list-tab-done">
               <div class="wishlist-scroll">
                  <table class="wishlist-table">
                     <thead>
                        <tr>
                           <th>Karyawan</th>
                           <th>Status</th>
                           <th>Site</th>
                           <th>Alasan Banned</th>
                           <th>Selesai</th>
                        </tr>
                     </thead>
                     <tbody>
                        @forelse($rowsBannedDone as $row)
                        @php
                           $isLog = $row instanceof \App\Models\SidBannedLog;
                           $nama = $isLog ? $row->nama : ($row['nama'] ?? '—');
                           $sid = $isLog ? $row->sid : ($row['sid'] ?? '');
                           $nik = $isLog ? $row->nik : ($row['nik'] ?? '');
                           $site = $isLog ? $row->site_dedicated : ($row['site'] ?? '');
                           $reason = $isLog ? $row->banned_reason : ($row['bannedReason'] ?? '');
                           $bannedStatus = $isLog ? $row->banned_status : ($row['bannedStatus'] ?? '');
                           $completedAt = $isLog
                              ? $row->completed_at?->format('d M Y H:i')
                              : ($row['processedAt'] ?? '—');
                           $workPermit = $isLog ? ($row->work_permit_jenis ?? '') : '';
                           $initials = collect(explode(' ', $nama))->filter()->take(2)
                              ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->join('');
                           $cIdx = abs(crc32($sid ?: $nama)) % count($thumbColors);
                        @endphp
                        <tr>
                           <td>
                              <span class="item-desc">
                                 <span class="item-thumb" style="background:{{ $thumbColors[$cIdx] }}">{{ $initials ?: '?' }}</span>
                                 <span>
                                    <span class="name" title="{{ $nama }}">{{ $nama ?: '—' }}</span>
                                    <br><span class="text-[10px] text-[#888] font-mono">{{ $sid ?: $nik }}</span>
                                 </span>
                              </span>
                           </td>
                           <td><label class="badge badge-success">Berhasil</label></td>
                           <td>{{ $site ?: '—' }}</td>
                           <td>
                              <span class="reason-cell" title="{{ $reason }}">{{ $reason ?: '—' }}</span>
                              @if($bannedStatus)
                              <br><span class="text-[10px] text-[#aaa]">{{ $bannedStatus }}</span>
                              @endif
                           </td>
                           <td>
                              <span class="text-[11px] whitespace-nowrap">{{ $completedAt ?: '—' }}</span>
                              @if($workPermit)
                              <br><span class="text-[10px] text-[#aaa]">{{ $workPermit }}</span>
                              @endif
                           </td>
                        </tr>
                        @empty
                        <tr>
                           <td colspan="5" class="text-center py-10" style="color:#888">Belum ada karyawan yang selesai di-banned</td>
                        </tr>
                        @endforelse
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>

   </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
   /* Daftar karyawan — tab switch */
   document.querySelectorAll('[data-ab-list-tab]').forEach(function (tab) {
      tab.addEventListener('click', function () {
         var name = tab.getAttribute('data-ab-list-tab');
         document.querySelectorAll('[data-ab-list-tab]').forEach(function (t) {
            t.setAttribute('aria-selected', t.getAttribute('data-ab-list-tab') === name ? 'true' : 'false');
         });
         document.querySelectorAll('[data-ab-list-panel]').forEach(function (panel) {
            panel.classList.toggle('hidden', panel.getAttribute('data-ab-list-panel') !== name);
         });
      });
   });
})();
</script>
<script>
(function () {
   if (typeof ApexCharts === 'undefined') return;

   var CYAN = '#01b0c6';
   var BLUE = '#0000ff';
   var chartData = @json($chartData);
   var trend = chartData.dailyTrend || { labels: [], total: [], success: [] };
   var pieLabels = @json($pieLabels);
   var pieValues = @json($pieValues);
   var avgLine = @json($avgLineData);
   var font = 'Poppins, Helvetica, Arial, sans-serif';

   /* SalesSupportChartData — area sparkline */
   new ApexCharts(document.querySelector('#ab-chart-conversion'), {
      chart: {
         type: 'area',
         height: 100,
         sparkline: { enabled: true },
         background: 'transparent'
      },
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 2 },
      fill: {
         type: 'gradient',
         gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 90, 100] }
      },
      colors: [CYAN],
      series: [{ name: 'series1', data: (trend.success && trend.success.length) ? trend.success : [0, 0, 0, 0, 0] }],
      tooltip: {
         fixed: { enabled: false },
         x: { show: false },
         y: { title: { formatter: function () { return ''; } } },
         marker: { show: false }
      }
   }).render();

   /* SalesSupportChartData1 — dense mini bar */
   var barData = (trend.total && trend.total.length) ? trend.total : [25, 66, 41, 89, 63, 25, 44, 12, 36, 9, 54];
   new ApexCharts(document.querySelector('#ab-chart-orders'), {
      chart: {
         type: 'bar',
         height: 100,
         sparkline: { enabled: true }
      },
      plotOptions: { bar: { columnWidth: '80%' } },
      colors: [CYAN],
      series: [{ name: 'series1', data: barData }],
      tooltip: {
         fixed: { enabled: false },
         x: { show: false },
         y: { title: { formatter: function () { return ''; } } },
         marker: { show: false }
      }
   }).render();

   /* SalesAccountChartData — main report */
   new ApexCharts(document.querySelector('#ab-chart-main'), {
      chart: {
         height: 350,
         type: 'line',
         background: 'transparent',
         toolbar: {
            show: true,
            tools: {
               download: true,
               selection: false,
               zoom: false,
               zoomin: true,
               zoomout: true,
               pan: false,
               reset: false
            },
            export: { csv: { filename: 'DailyBannedReport' }, png: { filename: 'DailyBannedReport' } }
         },
         fontFamily: font
      },
      stroke: { width: [0, 3], curve: 'smooth' },
      plotOptions: { bar: { columnWidth: '50%' } },
      colors: [CYAN, BLUE],
      fill: { opacity: [0.85, 1] },
      labels: (trend.labels && trend.labels.length) ? trend.labels : ['—'],
      markers: { size: 0 },
      series: [
         { name: 'Total Banned', type: 'column', data: trend.total || [] },
         { name: 'Rata-rata', type: 'line', data: (avgLine.length ? avgLine : (trend.success || [])) }
      ],
      yaxis: { min: 0, labels: { style: { fontFamily: font, fontSize: '12px' } } },
      xaxis: { labels: { style: { fontFamily: font, fontSize: '11px' } } },
      grid: { strokeDashArray: 0, borderColor: '#f5f5f5' },
      legend: {
         labels: { useSeriesColors: true },
         markers: { width: 12, height: 12, radius: 0 },
         fontFamily: font
      },
      tooltip: {
         shared: true,
         intersect: false,
         y: { formatter: function (y) { return typeof y !== 'undefined' ? y.toFixed(0) : y; } }
      },
      theme: { mode: 'light' }
   }).render();

   /* SalesCustomerSatisfactionChartData — pie */
   new ApexCharts(document.querySelector('#ab-chart-pie'), {
      chart: {
         type: 'pie',
         height: 260,
         background: 'transparent',
         toolbar: {
            show: true,
            tools: {
               download: true,
               selection: false,
               zoom: false,
               zoomin: false,
               zoomout: false,
               pan: false,
               reset: false
            },
            export: { png: { filename: 'BannedDistribution' } }
         },
         fontFamily: font
      },
      labels: pieLabels.length ? pieLabels : ['No Data'],
      series: pieValues.length ? pieValues : [1],
      colors: pieLabels.length ? undefined : ['#e0e0e0'],
      dataLabels: {
         enabled: true,
         dropShadow: { enabled: false },
         style: { fontFamily: font, fontSize: '12px', fontWeight: 500 }
      },
      theme: {
         mode: 'light',
         monochrome: { enabled: pieLabels.length > 0, color: CYAN }
      },
      legend: {
         show: true,
         position: 'right',
         offsetY: 50,
         fontFamily: font,
         fontSize: '13px'
      },
      responsive: [{
         breakpoint: 768,
         options: {
            chart: { height: 320 },
            legend: { position: 'bottom', offsetY: 0 }
         }
      }]
   }).render();
})();
</script>
@endpush
