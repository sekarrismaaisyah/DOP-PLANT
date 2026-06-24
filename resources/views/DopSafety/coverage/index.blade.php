@extends('DopSafety.layouts.app')

@section('title', 'Daily Coverage — DOP Safety')

@push('head')
@include('DopSafety.partials.styles')
@endpush

@section('content')
@include('DopSafety.partials.page-header', [
   'title' => $leadingIndicator['title'],
   'subtitle' => $leadingIndicator['concept'],
   'breadcrumb' => 'Daily Coverage',
])

<div class="rounded-2xl p-5 ds-alert-critical ds-surface-card mb-6">
   <div class="flex items-center gap-3">
      <span class="material-symbols-outlined text-error text-3xl">gpp_bad</span>
      <p class="font-bold text-sm text-error">{{ $leadingIndicator['rule'] }}</p>
   </div>
</div>

<div class="ds-surface-card rounded-2xl p-6">
   <h2 class="font-headline font-bold text-base mb-4">Coverage per Area Workshop</h2>
   <div class="overflow-x-auto">
      <table class="ds-table w-full text-sm">
         <thead>
            <tr class="border-b border-outline-variant/20">
               <th class="text-left py-2">Area</th>
               <th class="text-left py-2">Target SAP</th>
               <th class="text-left py-2">Coverage</th>
               <th class="text-left py-2">Status</th>
               <th class="text-left py-2">Pengawas</th>
            </tr>
         </thead>
         <tbody>
            @foreach($areas as $area)
            <tr class="border-b border-outline-variant/10">
               <td class="py-3 font-semibold">{{ $area['area'] }}</td>
               <td class="py-3">{{ number_format($area['sap_target'], 0) }}%</td>
               <td class="py-3">
                  <div class="flex items-center gap-2">
                     <div class="ds-progress-bar flex-1 max-w-[8rem]">
                        <div class="ds-progress-fill" style="width: {{ $area['coverage_pct'] }}%"></div>
                     </div>
                     <span class="font-bold">{{ number_format($area['coverage_pct'], 1) }}%</span>
                  </div>
               </td>
               <td class="py-3">
                  <span class="ds-badge {{ $area['status'] === 'Tercapai' ? 'ds-badge--success' : 'ds-badge--danger' }}">
                     {{ $area['status'] }}
                  </span>
               </td>
               <td class="py-3">
                  @if($area['supervisor_allowed'])
                  <span class="inline-flex items-center gap-1 text-emerald-700 text-xs font-bold">
                     <span class="material-symbols-outlined text-sm">check_circle</span> Diperbolehkan
                  </span>
                  @else
                  <span class="inline-flex items-center gap-1 text-error text-xs font-bold">
                     <span class="material-symbols-outlined text-sm">block</span> Tidak Diperbolehkan
                  </span>
                  @endif
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>
</div>
@endsection
