@php
   use App\Enums\AutoBannedSidAutomationStatus;

   $stats = $banned['stats'] ?? [];
   $chartData = $banned['chartData'] ?? [];
   $period = $banned['period'] ?? [];
   $scrTableAvailable = $banned['scrTableAvailable'] ?? false;
   $bannedRows = collect($banned['bannedRows'] ?? []);
   $detailQuery = $detailQuery ?? [];

   $successRate = (float) ($stats['successRate'] ?? 0);
   $periodLabel = !empty($period['filter_date'])
      ? \Carbon\Carbon::parse($period['filter_date'])->format('d M Y')
      : 'Semua Tanggal';

   $productCards = [
      ['title' => 'Harus Di-Banned', 'value' => number_format($stats['totalToBan'] ?? 0), 'icon' => 'block'],
      ['title' => 'Automasi OK', 'value' => number_format($stats['success'] ?? 0), 'icon' => 'check_circle'],
      ['title' => 'Diproses', 'value' => number_format($stats['processed'] ?? 0), 'icon' => 'task_alt'],
      ['title' => 'Belum Proses', 'value' => number_format($stats['notProcessed'] ?? 0), 'icon' => 'hourglass_empty'],
   ];

   $pieLabels = $chartData['byBannedStatus']['labels'] ?? [];
   $pieValues = $chartData['byBannedStatus']['values'] ?? [];
   if (empty($pieLabels) && !empty($chartData['topReasons']['labels'])) {
      $pieLabels = $chartData['topReasons']['labels'];
      $pieValues = $chartData['topReasons']['values'];
   }

   $previewRows = $bannedRows->filter(function ($row) {
      if (! ($row['isProcessed'] ?? false)) {
         return true;
      }
      return ($row['automationStatus']?->value ?? '') !== AutoBannedSidAutomationStatus::Success->value;
   })->take(5)->values();
@endphp

<section class="ab-overview-section">
   <div class="ab-overview-section-head">
      <div>
         <h2 class="ab-overview-section-title">Monitoring Banned</h2>
         <p class="ab-overview-section-sub">Daily banned & automasi SID &bull; {{ $periodLabel }}</p>
      </div>
      <a href="{{ route('auto-banned.banned-monitoring.index', $detailQuery) }}" class="ab-overview-detail-link">
         Lihat detail <span class="material-symbols-outlined text-sm">arrow_forward</span>
      </a>
   </div>

   @if(!$scrTableAvailable)
   <div class="dash-card card-body text-sm text-[#888]">Tabel <code>scr_daily_banned</code> belum tersedia.</div>
   @else
   <div class="dash-row">
      @foreach($productCards as $card)
      <div class="dash-col-6">
         <div class="prod-p-card ab-overview-kpi">
            <div class="card-body">
               <div class="flex items-center justify-between">
                  <div>
                     <h6 class="m-b-5">{{ $card['title'] }}</h6>
                     <h3 class="mb-0">{{ $card['value'] }}</h3>
                  </div>
                  <i class="material-icons-two-tone text-primary" style="font-size:32px">{{ $card['icon'] }}</i>
               </div>
            </div>
         </div>
      </div>
      @endforeach
   </div>

   <div class="dash-row">
      <div class="dash-col-6">
         <div class="dash-card support-bar ab-overview-widget">
            <div class="card-body pb-0">
               <h2 class="m-0" style="font-size:26px">{{ number_format($successRate, 1) }}%</h2>
               <span class="label-cyan">Keberhasilan Automasi</span>
            </div>
            <div id="ab-overview-banned-spark" style="height:70px"></div>
         </div>
      </div>
      <div class="dash-col-6">
         <div class="dash-card satisfaction ab-overview-widget">
            <div class="card-body p-3">
               <h6 style="font-size:13px;margin-bottom:4px">Distribusi Status</h6>
               <div id="ab-overview-banned-pie" style="height:140px"></div>
            </div>
         </div>
      </div>
   </div>

   <div class="dash-card">
      <div class="card-header" style="padding:12px 16px">
         <h5 style="font-size:13px">Antrian Belum Selesai ({{ $previewRows->count() }} dari {{ $bannedRows->count() }})</h5>
      </div>
      <div class="card-body p-0">
         <div class="wishlist-scroll" style="max-height:200px">
            <table class="wishlist-table">
               <thead>
                  <tr>
                     <th>Karyawan</th>
                     <th>Site</th>
                     <th>Status</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($previewRows as $row)
                  <tr>
                     <td>
                        <span class="name">{{ $row['nama'] ?: '—' }}</span>
                        <br><span class="text-[10px] text-[#888] font-mono">{{ $row['sid'] ?: '' }}</span>
                     </td>
                     <td>{{ $row['site'] ?: '—' }}</td>
                     <td>
                        @if(!($row['isProcessed'] ?? false))
                        <label class="badge badge-warning">Belum</label>
                        @else
                        <label class="badge badge-info">{{ $row['automationStatus']?->label() ?? '—' }}</label>
                        @endif
                     </td>
                  </tr>
                  @empty
                  <tr><td colspan="3" class="text-center py-6 text-[#888]">Tidak ada antrian</td></tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>
   </div>
   @endif
</section>
