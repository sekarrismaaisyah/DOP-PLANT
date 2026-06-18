{{-- Matriks Program (kiri) × Evidence per Site/Mitra — gaya PembatasanLV --}}
@php
   $mx = $siteMatrix ?? [];
   $columns = $mx['columns'] ?? [];
   $siteGroups = $mx['site_column_groups'] ?? [];
   $programGroups = $mx['program_groups'] ?? [];
   $riskScores = $mx['risk_scores'] ?? [];

   $totalCols = count($columns) + 1;

   $bandClass = static fn (string $bar): string => match ($bar) {
      'orange' => 'fm-mx-group-band--orange',
      'yellow' => 'fm-mx-group-band--yellow',
      'green' => 'fm-mx-group-band--green',
      'blue' => 'fm-mx-group-band--blue',
      'slate' => 'fm-mx-group-band--slate',
      default => 'fm-mx-group-band--blue',
   };

   $riskCardClass = static fn (string $tier): string => match ($tier) {
      'best' => 'fm-mx-risk-card--best',
      'unstable' => 'fm-mx-risk-card--unstable',
      default => 'fm-mx-risk-card--high',
   };
   $riskTextClass = static fn (string $tier): string => match ($tier) {
      'best' => 'fm-mx-risk--best',
      'unstable' => 'fm-mx-risk--unstable',
      default => 'fm-mx-risk--high',
   };

   $evBadgeClass = static fn (array $cell): string => match ($cell['status'] ?? '') {
      'verified', 'checklist' => 'fm-ev-badge--ok',
      'uploaded', 'revision' => 'fm-ev-badge--warn',
      'belum' => 'fm-ev-badge--bad',
      default => 'fm-ev-badge--na',
   };
   $evIcon = static fn (array $cell): string => match ($cell['status'] ?? '') {
      'verified', 'checklist' => 'check_circle',
      'uploaded' => 'upload',
      'revision' => 'edit_note',
      'belum' => 'cancel',
      default => '',
   };
   $evShortLabel = static fn (array $cell): string => match ($cell['status'] ?? '') {
      'verified' => 'OK',
      'checklist' => 'OK',
      'uploaded' => 'Upload',
      'revision' => 'Revisi',
      'belum' => 'Belum',
      default => '—',
   };

   $totalSubmit = collect($companyGroups ?? [])->sum('submitted_count');
   $avgChecklist = ($summary['pct_checklist'] ?? 0);
   $totalPrograms = collect($programGroups)->sum(fn ($g) => count($g['programs'] ?? []));
   $totalMitra = count($companyGroups ?? []);
   $singleSite = count($siteGroups) === 1;
   $scopeLabel = $singleSite ? ($siteGroups[0]['site_label'] ?? 'GMO') : count($siteGroups) . ' site';

   $siteSepKeys = [];
   $prevSite = null;
   foreach ($columns as $col) {
      if ($prevSite !== null && $col['site'] !== $prevSite) {
         $siteSepKeys[$col['cell_key']] = true;
      }
      $prevSite = $col['site'];
   }
@endphp

<section class="fm-mx space-y-4">
   <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
      <div class="fm-mx-stat">
         <span class="fm-mx-stat__icon"><span class="material-symbols-outlined">business</span></span>
         <p class="fm-mx-stat__label">Mitra</p>
         <p class="fm-mx-stat__value">{{ $totalMitra }}</p>
      </div>
      <div class="fm-mx-stat">
         <span class="fm-mx-stat__icon"><span class="material-symbols-outlined">checklist</span></span>
         <p class="fm-mx-stat__label">Program</p>
         <p class="fm-mx-stat__value">{{ $totalPrograms }}</p>
      </div>
      <div class="fm-mx-stat">
         <span class="fm-mx-stat__icon"><span class="material-symbols-outlined">upload_file</span></span>
         <p class="fm-mx-stat__label">Submit</p>
         <p class="fm-mx-stat__value">{{ $totalSubmit }}</p>
      </div>
      <div class="fm-mx-stat">
         <span class="fm-mx-stat__icon"><span class="material-symbols-outlined">task_alt</span></span>
         <p class="fm-mx-stat__label">Checklist</p>
         <p class="fm-mx-stat__value">{{ $avgChecklist }}%</p>
      </div>
   </div>

   <div class="fm-mx-surface rounded-2xl overflow-hidden">
      <div class="px-5 sm:px-6 pt-5 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-outline-variant/10">
         <div>
            <h2 class="font-headline font-semibold text-base text-on-background">Matriks Program & Evidence</h2>
            <p class="text-xs text-on-surface-variant mt-0.5">
               Site {{ $scopeLabel }} · Hasil evidence per mitra · {{ $isoWeek }} {{ $year }}
            </p>
         </div>
         <span class="inline-flex items-center gap-1.5 rounded-lg bg-primary/[0.08] px-2.5 py-1 text-[11px] font-bold text-primary shrink-0">
            <span class="material-symbols-outlined text-sm">location_on</span>
            {{ $scopeLabel }}
         </span>
      </div>

      <div class="fm-mx-scroll">
         <table class="fm-mx-table {{ $singleSite ? 'fm-mx-table--single-site' : '' }}">
            <thead>
               @if($singleSite)
               <tr class="border-b border-outline-variant/10">
                  <th class="fm-mx-corner">Program</th>
                  @foreach($columns as $col)
                  <th class="fm-mx-partner-th">{{ $col['partner_label'] }}</th>
                  @endforeach
               </tr>
               @else
               <tr class="border-b border-outline-variant/10">
                  <th class="fm-mx-corner" rowspan="2">Program</th>
                  @foreach($siteGroups as $sg)
                  <th colspan="{{ count($sg['partners']) }}" class="fm-mx-site-th">{{ $sg['site_label'] }}</th>
                  @endforeach
               </tr>
               <tr>
                  @foreach($columns as $col)
                  <th class="fm-mx-partner-th {{ isset($siteSepKeys[$col['cell_key']]) ? 'fm-mx-partner-th--sep' : '' }}">
                     {{ $col['partner_label'] }}
                  </th>
                  @endforeach
               </tr>
               @endif
            </thead>
            <tbody class="divide-y divide-outline-variant/5">
               @forelse($programGroups as $group)
               <tr class="fm-mx-group-row">
                  <td colspan="{{ $totalCols }}">
                     <div class="fm-mx-group-band {{ $bandClass($group['bar'] ?? 'blue') }}">
                        {{ $group['label'] ?? '' }}
                        <span class="text-on-surface-variant/60 font-semibold normal-case tracking-normal">
                           · {{ count($group['programs'] ?? []) }} program
                        </span>
                     </div>
                  </td>
               </tr>
               @foreach($group['programs'] ?? [] as $program)
               <tr>
                  <td class="fm-mx-program-cell">
                     <p class="fm-mx-program-title">{{ $program['title'] ?? '' }}</p>
                     <div class="fm-mx-program-meta">
                        <span class="fm-mx-program-no">#{{ $program['program_no'] ?? '—' }}</span>
                        <span class="fm-mx-freq-chip">
                           <span class="material-symbols-outlined">schedule</span>
                           {{ $program['frequency'] ?? '—' }}
                        </span>
                     </div>
                  </td>
                  @foreach($program['cells'] ?? [] as $ci => $cell)
                  @php
                     $colKey = $columns[$ci]['cell_key'] ?? '';
                  @endphp
                  <td class="fm-mx-data-cell {{ ! $singleSite && isset($siteSepKeys[$colKey]) ? 'fm-mx-data-cell--sep' : '' }}" title="{{ $cell['sub'] ?? '' }}">
                     @if($cell['empty'] ?? false)
                        <span class="fm-ev-badge fm-ev-badge--na">—</span>
                     @else
                        <span class="fm-ev-badge {{ $evBadgeClass($cell) }}">
                           @if($evIcon($cell) !== '')
                           <span class="material-symbols-outlined">{{ $evIcon($cell) }}</span>
                           @endif
                           {{ $evShortLabel($cell) }}
                        </span>
                     @endif
                  </td>
                  @endforeach
               </tr>
               @endforeach
               @empty
               <tr>
                  <td colspan="{{ $totalCols }}" class="fm-mx-empty-row">
                     <span class="material-symbols-outlined text-3xl text-primary/20 block mb-2">table_chart</span>
                     Belum ada data program untuk periode ini.
                  </td>
               </tr>
               @endforelse
            </tbody>
         </table>
      </div>

      <div class="fm-mx-legend">
         @foreach($mx['legend'] ?? [] as $item)
            @if(isset($item['note']))
            <span>{{ $item['note'] }}</span>
            @endif
         @endforeach
         <span class="fm-ev-badge fm-ev-badge--ok"><span class="material-symbols-outlined">check_circle</span> OK</span>
         <span class="fm-ev-badge fm-ev-badge--warn"><span class="material-symbols-outlined">upload</span> Upload / Revisi</span>
         <span class="fm-ev-badge fm-ev-badge--bad"><span class="material-symbols-outlined">cancel</span> Belum</span>
         <span class="fm-ev-badge fm-ev-badge--na">— N/A</span>
      </div>
   </div>

   @if($riskScores !== [] && ! $singleSite)
   <div class="fm-mx-surface rounded-2xl overflow-hidden">
      <div class="px-5 sm:px-6 pt-4 pb-3 border-b border-outline-variant/10">
         <h3 class="font-headline font-semibold text-sm text-on-background">Ringkasan per Site</h3>
         <p class="text-xs text-on-surface-variant mt-0.5">Compliance evidence berdasarkan program yang berlaku</p>
      </div>
      <div class="p-4 sm:p-5">
         <div class="fm-mx-risk-grid">
            @foreach($riskScores as $risk)
            <div class="fm-mx-risk-card {{ $riskCardClass($risk['tier'] ?? 'high') }}">
               <p class="fm-mx-risk-site">{{ $risk['site_label'] }}</p>
               <p class="fm-mx-risk-tier {{ $riskTextClass($risk['tier'] ?? 'high') }}">{{ $risk['tier_label'] ?? '' }}</p>
               <div class="fm-mx-risk-meter">
                  <div class="fm-mx-risk-meter-fill" style="width: {{ min(100, $risk['avg_checklist'] ?? 0) }}%"></div>
               </div>
               <p class="fm-mx-risk-avg">Evidence OK {{ $risk['avg_checklist'] ?? 0 }}%</p>
               @if(($risk['need_attention'] ?? 0) > 0)
               <span class="fm-mx-risk-count fm-mx-risk-count--warn">
                  <span class="material-symbols-outlined text-[11px]">warning</span>
                  {{ $risk['need_attention'] }} perlu perhatian
               </span>
               @elseif(($risk['caution'] ?? 0) > 0)
               <span class="fm-mx-risk-count fm-mx-risk-count--danger">
                  <span class="material-symbols-outlined text-[11px]">error</span>
                  {{ $risk['caution'] }} caution
               </span>
               @else
               <span class="fm-mx-risk-count fm-mx-risk-count--ok">
                  <span class="material-symbols-outlined text-[11px]">check_circle</span>
                  On track
               </span>
               @endif
            </div>
            @endforeach
         </div>
      </div>
   </div>
   @endif
</section>
