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
         <h2 class="font-headline font-bold text-lg mb-4 text-primary">{{ $sectionName }}</h2>
         <div class="overflow-x-auto">
            <table class="ds-table w-full text-sm">
               <thead>
                  <tr class="border-b border-outline-variant/20">
                     <th class="text-left py-2">No</th>
                     <th class="text-left py-2">Unit</th>
                     <th class="text-left py-2">Lokasi</th>
                     <th class="text-left py-2">Detail Pekerjaan</th>
                     <th class="text-left py-2">Pekerja</th>
                     <th class="text-left py-2">CCTV</th>
                     <th class="text-left py-2">L1–L4</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach($items as $item)
                  <tr class="border-b border-outline-variant/10 align-top">
                     <td class="py-3 font-bold">{{ $item->item_no }}</td>
                     <td class="py-3">
                        <div class="font-semibold">{{ $item->unit_code }}</div>
                        <div class="text-xs text-on-surface-variant">{{ $item->unit_category }}</div>
                     </td>
                     <td class="py-3">{{ $item->location }}</td>
                     <td class="py-3">
                        <div>{{ $item->job_detail }}</div>
                        <div class="text-xs text-on-surface-variant mt-1">Izin: {{ $item->work_permit }}</div>
                        @if(is_array($item->tools) && count($item->tools))
                        <div class="text-xs mt-1">Alat: {{ implode(', ', $item->tools) }}</div>
                        @endif
                     </td>
                     <td class="py-3 text-xs">{{ is_array($item->workers) ? implode(', ', $item->workers) : '—' }}</td>
                     <td class="py-3 text-xs">{{ $item->cctv ?? '—' }}</td>
                     <td class="py-3 text-xs space-y-0.5">
                        @if($item->group_leader)<div>GL: {{ $item->group_leader }}</div>@endif
                        @if($item->section_head)<div>SH: {{ $item->section_head }}</div>@endif
                        @if($item->she_leader)<div>SHE: {{ $item->she_leader }}</div>@endif
                        @if($item->dept_head)<div>DH: {{ $item->dept_head }}</div>@endif
                        @if($item->pja_bc)<div>PJA: {{ $item->pja_bc }}</div>@endif
                     </td>
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
