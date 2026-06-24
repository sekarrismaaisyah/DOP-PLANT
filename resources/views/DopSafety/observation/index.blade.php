@extends('DopSafety.layouts.app')

@section('title', 'Observasi — DOP Safety')

@push('head')
@include('DopSafety.partials.styles')
@endpush

@section('content')
@include('DopSafety.partials.page-header', [
   'title' => 'Bagian E — Observasi Aktivitas Berdasarkan DOP',
   'subtitle' => 'Temuan dicatat langsung di BEATS saat observasi. Target coverage GL & SH 100%.',
   'breadcrumb' => 'Observasi',
])

<div class="grid lg:grid-cols-3 gap-6 mb-6">
   <div class="ds-surface-card rounded-2xl p-6 lg:col-span-1">
      <h2 class="font-headline font-bold text-base mb-4">Checklist Observasi</h2>
      <ul class="space-y-2">
         @foreach($checklist as $item)
         <li class="flex items-start gap-2 text-sm">
            <span class="material-symbols-outlined text-primary text-lg shrink-0">check_box_outline_blank</span>
            {{ $item }}
         </li>
         @endforeach
      </ul>
   </div>

   <div class="ds-surface-card rounded-2xl p-6 lg:col-span-2">
      <h2 class="font-headline font-bold text-base mb-4">Target Coverage Observasi</h2>
      <div class="space-y-3">
         @foreach($coverageTargets as $target)
         <div class="p-4 rounded-xl border border-outline-variant/20">
            <p class="font-bold text-sm text-primary">{{ $target['role'] }}</p>
            <p class="text-sm text-on-surface-variant mt-1">{{ $target['target'] }}</p>
         </div>
         @endforeach
      </div>
   </div>
</div>

<div class="ds-surface-card rounded-2xl p-6">
   <h2 class="font-headline font-bold text-base mb-4">Log Observasi</h2>
   <div class="overflow-x-auto">
      <table class="ds-table w-full text-sm">
         <thead>
            <tr class="border-b border-outline-variant/20">
               <th class="text-left py-2">Observer</th>
               <th class="text-left py-2">Peran</th>
               <th class="text-left py-2">Aktivitas</th>
               <th class="text-left py-2">Coverage</th>
               <th class="text-left py-2">BEATS</th>
            </tr>
         </thead>
         <tbody>
            @foreach($observations as $obs)
            <tr class="border-b border-outline-variant/10">
               <td class="py-3 font-semibold">{{ $obs['observer'] }}</td>
               <td class="py-3"><span class="ds-badge">{{ $obs['role'] }}</span></td>
               <td class="py-3">{{ $obs['activity'] }}</td>
               <td class="py-3">{{ number_format($obs['coverage_pct'], 0) }}%</td>
               <td class="py-3">
                  <span class="ds-badge {{ $obs['beats_logged'] ? 'ds-badge--success' : 'ds-badge--danger' }}">
                     {{ $obs['beats_logged'] ? 'Tercatat' : 'Belum' }}
                  </span>
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>
</div>
@endsection
