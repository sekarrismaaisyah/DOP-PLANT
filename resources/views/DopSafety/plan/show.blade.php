@extends('DopSafety.layouts.app')

@section('title', 'Detail DOP — DOP Safety')

@push('head')
@include('DopSafety.partials.styles')
<style>
   .ds-watermark {
      position: fixed;
      inset: 0;
      pointer-events: none;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0.04;
      font-size: 6rem;
      font-weight: 900;
      transform: rotate(-30deg);
      z-index: 0;
   }
</style>
@endpush

@section('content')
@php
   $tableStructure = config('dop_safety.table_structure', []);
@endphp
<div class="relative">
   <div class="ds-watermark">{{ $watermark }}</div>

   @include('DopSafety.partials.page-header', [
      'title' => 'Daily Operation Planning (DOP)',
      'subtitle' => 'PT. PAMA PERSADA — PLANT DEPT. · ' . $plan->site . ' · ' . $plan->plan_date->format('l, d M Y') . ' · ' . $plan->shiftLabel(),
      'breadcrumb' => 'Detail DOP',
   ])

   <div class="relative z-10 space-y-6">
      <div class="rounded-xl border border-error/20 bg-red-50/60 px-4 py-3 text-sm font-semibold text-error flex items-center gap-2">
         <span class="material-symbols-outlined">warning</span>
         {{ $disclaimer }}
      </div>

      <div class="flex flex-wrap gap-2 items-center">
         <span class="{{ $plan->status->badgeClass() }}">{{ $plan->status->label() }}</span>
         <a href="{{ route('dop-safety.plan.edit', $plan) }}" class="ds-badge">Edit</a>
         <a href="{{ route('dop-safety.plan.index') }}" class="ds-badge">Kembali</a>
         <a href="{{ route('dop-safety.plan.export-pdf', $plan) }}" target="_blank" class="ds-badge !bg-red-100 !text-red-700 border border-red-300 hover:!bg-red-200">Export to PDF</a>
      </div>

      @foreach($itemsBySection as $sectionName => $items)
      <div class="ds-surface-card rounded-2xl p-6">
         <div class="overflow-x-auto">
            <table class="ds-table ds-plan-table w-full text-sm border-collapse min-w-[1400px]">
               <thead>
                  @include('DopSafety.plan.partials.table-head', [
                     'tableStructure' => array_merge($tableStructure, [
                        'sections' => [['name' => $sectionName, 'colspan' => \App\Support\DopSafety\DopSafetyPlanTableStructure::EXCEL_SHIFT_SECTION_COLSPAN]],
                     ]),
                     'shiftOptions' => config('dop_safety.shifts', []),
                     'defaults' => ['shift' => $plan->shift],
                  ])
               </thead>
               <tbody>
                  @foreach($items as $item)
                  @php
                     $workers = \App\Support\DopSafety\DopSafetyPlanTableStructure::workersToDisplayCells(is_array($item->workers) ? $item->workers : []);
                     
                     // ---- LOGIC SINKRONISASI STATUS OJI ----
                     $isOjiApproved = false;
                     $ojiItem = null;
                     
                     $site = $plan->site;
                     $shift = $plan->shift;
                     $planDate = \Carbon\Carbon::parse($plan->plan_date)->format('Y-m-d');
              
                     $targetOjiPlan = \App\Models\DopOjiPlan::query()
                         ->where('site', $site)
                         ->where('plan_date', $planDate)
                         ->where('shift', $shift)
                         ->first();
              
                     if ($targetOjiPlan) {
                         $ojiItem = \App\Models\DopOjiPlanItem::query()
                             ->where('dop_oji_plan_id', $targetOjiPlan->id)
                             ->where('item_no', $item->item_no)
                             ->first();
              
                         if (!$ojiItem && !empty($item->unit_code)) {
                             $ojiItem = \App\Models\DopOjiPlanItem::query()
                                 ->where('dop_oji_plan_id', $targetOjiPlan->id)
                                 ->where('unit_code', $item->unit_code)
                                 ->where('job_detail', $item->job_detail ?? '')
                                 ->first();
                         }
                     }
              
                     if (!$ojiItem && !empty($item->unit_code)) {
                         $ojiItem = \App\Models\DopOjiPlanItem::query()
                             ->where('unit_code', $item->unit_code)
                             ->where('job_detail', $item->job_detail ?? '')
                             ->orderByRaw("FIELD(approval_status, 'done') DESC")
                             ->first();
                     }
              
                     if ($ojiItem && $ojiItem->approval_status === 'done') {
                         $isOjiApproved = true;
                     }
                  @endphp
                  <tr class="border-b border-gray-100 align-top bg-white hover:bg-gray-50">
                     <td class="px-2 py-3 border border-gray-200 text-center font-bold text-gray-700">{{ $item->item_no }}</td>
                     
                     <td class="px-2 py-3 border border-gray-200 text-center bg-white align-middle">
                        @if($isOjiApproved)
                           <input type="checkbox" disabled checked class="rounded border-gray-300 text-primary h-4 w-4 cursor-not-allowed opacity-70" title="Sudah Approved">
                        @else
                           <input type="checkbox" disabled class="rounded border-gray-200 text-gray-300 bg-gray-100 cursor-not-allowed h-4 w-4" title="Belum Approved">
                        @endif
                     </td>

                     <td class="p-3 align-middle text-center min-w-[150px] border border-gray-200 bg-white">
                          <div class="flex flex-col items-center gap-2">
                              @php
                                  $ojiStatus = $ojiItem->approval_status ?? 'waiting_dept_head';
                                  $ojiStatusClasses = match($ojiStatus) {
                                      'waiting_dept_head'     => 'bg-amber-50 text-amber-700 border-amber-200',
                                      'done'                  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                      'rejected'              => 'bg-rose-50 text-rose-700 border-rose-200',
                                      default                 => 'bg-gray-50 text-gray-700 border-gray-200'
                                  };
                                  $ojiStatusLabel = match($ojiStatus) {
                                      'waiting_dept_head'     => 'Waiting Dept. Head',
                                      'done'                  => 'OJI Approved',
                                      'rejected'              => 'OJI Rejected',
                                      default                 => strtoupper(str_replace('_', ' ', $ojiStatus))
                                  };
                              @endphp
                              
                              @if($ojiItem)
                                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $ojiStatusClasses }}">
                                      <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current"></span>
                                      {{ $ojiStatusLabel }}
                                  </span>
                              @else
                                  <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border bg-gray-50 text-gray-500 border-gray-200">
                                      <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current"></span>
                                      No Data OJI
                                  </span>
                              @endif
                  
                              @if($ojiItem && !empty($ojiItem->dop_oji_plan_id))
                                  <a href="{{ route('dop-safety.oji.show', $ojiItem->dop_oji_plan_id) }}" 
                                     target="_blank" 
                                     class="px-3 py-1 text-[10px] rounded border border-blue-200 bg-blue-50 hover:bg-blue-600 hover:text-white hover:border-blue-600 text-blue-700 font-bold shadow-sm whitespace-nowrap transition-all duration-200">
                                     👁️ Lihat Data OJI
                                  </a>
                              @endif
                          </div>
                     </td>

                     <td class="p-3 align-middle text-center min-w-[150px] border border-gray-200 bg-white">
                          @php
                              $currStatus = $item->approval_status ?? 'waiting_lce';
                              $statusClasses = match($currStatus) {
                                  'waiting_lce'           => 'bg-blue-50 text-blue-700 border-blue-200',
                                  'waiting_dept_head'     => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                  'waiting_dept_head_she' => 'bg-purple-50 text-purple-700 border-purple-200',
                                  'waiting_pm'            => 'bg-amber-50 text-amber-700 border-amber-200',
                                  'waiting_suptend_safety'=> 'bg-orange-50 text-orange-700 border-orange-200',
                                  'waiting_wktt'          => 'bg-rose-50 text-rose-700 border-rose-200',
                                  'done'                  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                  default                 => 'bg-gray-50 text-gray-700 border-gray-200'
                              };
                              $statusLabel = match($currStatus) {
                                    'waiting_lce'            => 'Waiting LCE',
                                    'waiting_dept_head'      => 'Waiting Dept. Head',
                                    'waiting_dept_head_she'  => 'Waiting DH SHE',
                                    'waiting_pm'             => 'Waiting PM',
                                    'waiting_suptend_safety' => 'Waiting Supt. Safety',
                                    'waiting_wktt'           => 'Waiting WKTT',
                                    'done'                   => 'Done',
                                  default                 => strtoupper(str_replace('_', ' ', $currStatus))
                              };
                          @endphp
                          <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClasses }}">
                              <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current"></span>
                              {{ $statusLabel }}
                          </span>
                     </td>

                     <td class="px-2 py-3 border border-gray-200 font-semibold">{{ $item->unit_code }}</td>
                     <td class="px-2 py-3 border border-gray-200">{{ $item->section_name }}</td>
                     <td class="px-2 py-3 border border-gray-200">{{ $item->location }}</td>
                     <td class="px-2 py-3 border border-gray-200">{{ $item->job_detail }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->work_permit }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">
                        @if(is_array($item->tools) && count($item->tools))
                        {{ implode(', ', $item->tools) }}
                        @else — @endif
                     </td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $workers['names'] ?: '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $workers['sids'] ?: '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->cctv ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->group_leader ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->group_leader_sid ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->section_head ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->section_head_sid ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->she_leader ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->she_leader_sid ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->dept_head ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->dept_head_sid ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->pja_bc ?? '—' }}</td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
      </div>
      @endforeach

      <div class="ds-surface-card rounded-2xl p-6 mb-6">
         <h2 class="font-headline font-bold text-base mb-4">Informasi Dokumen</h2>
         <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div><span class="font-bold">Site:</span> {{ $plan->site ?? '—' }}</div>
            <div><span class="font-bold">Tanggal Plan:</span> {{ $plan->plan_date ?? '—' }}</div>
            <div><span class="font-bold">Shift:</span> {{ $plan->shift ?? '—' }}</div>
            
            <div><span class="font-bold">Company:</span> {{ $plan->company ?? '—' }}</div>
            <div><span class="font-bold">Departemen:</span> {{ $plan->department ?? '—' }}</div>
         </div>
      </div>

      <div class="ds-surface-card rounded-2xl p-6">
         <h2 class="font-headline font-bold text-base mb-4">Otorisasi Dokumen</h2>
         <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div><span class="font-bold">Lokasi & Tanggal:</span> {{ $plan->auth_location_date ?? '—' }}</div>
            <div><span class="font-bold">Dibuat Oleh:</span> {{ $plan->created_by_name ?? '—' }} @if($plan->created_by_position)({{ $plan->created_by_position }})@endif</div>
            @foreach([1,2,3] as $n)
            @php
               $name = $plan->{'acknowledged_'.$n.'_name'};
               $pos = $plan->{'acknowledged_'.$n.'_position'};
            @endphp
            @if($name)
            <div><span class="font-bold">Mengetahui {{ $n }}:</span> {{ $name }} @if($pos)({{ $pos }})@endif</div>
            @endif
            @endforeach
         </div>
      </div>
   </div>
</div>
@endsection