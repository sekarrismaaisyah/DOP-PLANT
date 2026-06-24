@php
   use App\Enums\AutoBannedUnbanStatus;

   $stats = $unban['stats'] ?? [];
   $chartData = $unban['chartData'] ?? [];
   $period = $unban['period'] ?? [];
   $tableAvailable = $unban['tableAvailable'] ?? false;
   $unbanRows = $unban['unbanRows'] ?? collect();
   $detailQuery = $detailQuery ?? [];

   $approvalRate = (float) ($stats['approvalRate'] ?? 0);
   $periodLabel = !empty($period['filter_date'])
      ? \Carbon\Carbon::parse($period['filter_date'])->format('d M Y')
      : 'Semua Tanggal';

   $productCards = [
      ['title' => 'Total Pengajuan', 'value' => number_format($stats['total'] ?? 0), 'icon' => 'assignment'],
      ['title' => 'Menunggu', 'value' => number_format($stats['pending'] ?? 0), 'icon' => 'hourglass_top'],
      ['title' => 'Disetujui', 'value' => number_format($stats['approved'] ?? 0), 'icon' => 'check_circle'],
      ['title' => 'Ditolak', 'value' => number_format($stats['rejected'] ?? 0), 'icon' => 'cancel'],
   ];

   $pieLabels = $chartData['byStatus']['labels'] ?? [];
   $pieValues = $chartData['byStatus']['values'] ?? [];

   $previewRows = $unbanRows
      ->filter(fn ($row) => $row->status === AutoBannedUnbanStatus::Pending)
      ->take(5)
      ->values();
@endphp

<section class="ab-overview-section">
   <div class="ab-overview-section-head">
      <div>
         <h2 class="ab-overview-section-title">Monitoring Un Banned</h2>
         <p class="ab-overview-section-sub">Pengajuan unban & treatment &bull; {{ $periodLabel }}</p>
      </div>
      <a href="{{ route('auto-banned.unban-monitoring.index', $detailQuery) }}" class="ab-overview-detail-link">
         Lihat detail <span class="material-symbols-outlined text-sm">arrow_forward</span>
      </a>
   </div>

   @if(!$tableAvailable)
   <div class="dash-card card-body text-sm text-[#888]">Tabel <code>auto_banned_unban_requests</code> belum tersedia.</div>
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
               <h2 class="m-0" style="font-size:26px">{{ number_format($approvalRate, 1) }}%</h2>
               <span class="label-cyan">Tingkat Persetujuan</span>
            </div>
            <div id="ab-overview-unban-spark" style="height:70px"></div>
         </div>
      </div>
      <div class="dash-col-6">
         <div class="dash-card satisfaction ab-overview-widget">
            <div class="card-body p-3">
               <h6 style="font-size:13px;margin-bottom:4px">Distribusi Status</h6>
               <div id="ab-overview-unban-pie" style="height:140px"></div>
            </div>
         </div>
      </div>
   </div>

   <div class="dash-card">
      <div class="card-header" style="padding:12px 16px">
         <h5 style="font-size:13px">Menunggu Review ({{ $previewRows->count() }} dari {{ $unbanRows->count() }})</h5>
      </div>
      <div class="card-body p-0">
         <div class="wishlist-scroll" style="max-height:200px">
            <table class="wishlist-table">
               <thead>
                  <tr>
                     <th>Karyawan</th>
                     <th>Site</th>
                     <th>Alasan Di-Banned</th>
                  </tr>
               </thead>
               <tbody>
                  @forelse($previewRows as $row)
                  @php $scr = $row->scrDailyBanned; @endphp
                  <tr>
                     <td>
                        <span class="name">{{ $row->karyawan ?: '—' }}</span>
                        <br><span class="text-[10px] text-[#888] font-mono">{{ $row->sid }}</span>
                     </td>
                     <td>{{ $row->site_dedicated ?: '—' }}</td>
                     <td>
                        <span class="reason-cell" title="{{ $scr?->Banned_Daily_Reason }}">
                           {{ $scr ? trim((string) ($scr->Banned_Daily_Reason ?? '')) ?: '—' : '—' }}
                        </span>
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
