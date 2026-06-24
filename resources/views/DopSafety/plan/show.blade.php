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
                  @endphp
                  <tr class="border-b border-gray-100 align-top bg-white hover:bg-gray-50">
                     <td class="px-2 py-3 border border-gray-200 text-center font-bold text-gray-700">{{ $item->item_no }}</td>
                     <td class="px-2 py-3 border border-gray-200 font-semibold">{{ $item->unit_code }}</td>
                     <td class="px-2 py-3 border border-gray-200">{{ $item->section_name }}</td>
                     <td class="px-2 py-3 border border-gray-200">{{ $item->location }}</td>
                     <td class="px-2 py-3 border border-gray-200">
                        {{ $item->job_detail }}
                     </td>
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
