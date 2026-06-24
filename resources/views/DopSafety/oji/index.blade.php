@extends('DopSafety.layouts.app')

@section('title', 'OJI — DOP Safety')

@push('head')
@include('DopSafety.partials.styles')
@endpush

@section('content')
@include('DopSafety.partials.page-header', [
   'title' => 'Bagian A — Pelaksanaan OJI',
   'subtitle' => 'Orientasi Jabatan dan Izin Kerja. Pekerjaan TIDAK BOLEH dimulai hingga OJI di-approve.',
   'breadcrumb' => 'OJI',
])

<div class="grid lg:grid-cols-2 gap-6">
   <div class="ds-surface-card rounded-2xl p-6">
      <h2 class="font-headline font-bold text-base mb-4 flex items-center gap-2">
         <span class="material-symbols-outlined text-primary">rule</span>
         Ketentuan OJI
      </h2>
      <div class="overflow-x-auto">
         <table class="ds-table w-full text-sm">
            <thead>
               <tr class="border-b border-outline-variant/20">
                  <th class="text-left py-2 pr-4">No</th>
                  <th class="text-left py-2 pr-4">Aspek</th>
                  <th class="text-left py-2">Ketentuan</th>
               </tr>
            </thead>
            <tbody>
               @foreach($rules as $rule)
               <tr class="border-b border-outline-variant/10">
                  <td class="py-3 pr-4 font-bold text-primary">{{ $rule['no'] }}</td>
                  <td class="py-3 pr-4 font-semibold">{{ $rule['aspect'] }}</td>
                  <td class="py-3 text-on-surface-variant">{{ $rule['rule'] }}</td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
   </div>

   <div class="ds-surface-card rounded-2xl p-6">
      <h2 class="font-headline font-bold text-base mb-4 flex items-center gap-2">
         <span class="material-symbols-outlined text-primary">pending_actions</span>
         Status OJI Hari Ini
      </h2>
      <div class="space-y-3">
         @forelse($pendingItems as $item)
         @php
            $badgeClass = str_contains($item['status'], 'Approved') ? 'ds-badge--success' : 'ds-badge--warning';
         @endphp
         <div class="p-4 rounded-xl border border-outline-variant/20 bg-white">
            <div class="flex items-center justify-between mb-2">
               <span class="font-semibold text-sm">{{ $item['section'] }} — {{ $item['gl'] }}</span>
               <span class="ds-badge {{ $badgeClass }}">{{ $item['status'] }}</span>
            </div>
            <p class="text-xs text-on-surface-variant">{{ $item['shift'] }} · Diajukan {{ $item['submitted_at'] }}</p>
         </div>
         @empty
         <p class="text-sm text-on-surface-variant">Belum ada data OJI.</p>
         @endforelse
      </div>
   </div>
</div>
@endsection
