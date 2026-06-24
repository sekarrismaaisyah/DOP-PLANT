@extends('DopSafety.layouts.app')

@section('title', 'Inspeksi L1–L3 — DOP Safety')

@push('head')
@include('DopSafety.partials.styles')
@endpush

@section('content')
@include('DopSafety.partials.page-header', [
   'title' => 'Inspeksi Pra Kerja, Saat Kerja & Pasca Pekerjaan',
   'subtitle' => 'Bagian C (L3 Safety PAMA & BC) dan Bagian D (L1 & L2 GL & SH). TIDAK LOLOS = PEKERJAAN DISTOP.',
   'breadcrumb' => 'Inspeksi L1–L3',
])

{{-- L3 Matrix --}}
<div class="ds-surface-card rounded-2xl p-6 mb-6">
   <h2 class="font-headline font-bold text-base mb-4 flex items-center gap-2">
      <span class="ds-level-pill ds-level-pill--l3">L3</span>
      Inspeksi Tim Safety PAMA & BC
   </h2>
   <div class="overflow-x-auto">
      <table class="ds-table w-full text-sm">
         <thead>
            <tr class="border-b border-outline-variant/20">
               <th class="text-left py-2">Pelaksana</th>
               <th class="text-left py-2">Shift</th>
               <th class="text-left py-2">Target Verifikasi</th>
               <th class="text-left py-2">Metode</th>
               <th class="text-left py-2">Output</th>
            </tr>
         </thead>
         <tbody>
            @foreach($l3Matrix as $row)
            <tr class="border-b border-outline-variant/10">
               <td class="py-3 font-semibold">{{ $row['executor'] }}</td>
               <td class="py-3">{{ $row['shift'] }}</td>
               <td class="py-3"><span class="ds-badge">{{ $row['target'] }}</span></td>
               <td class="py-3 text-on-surface-variant">{{ $row['method'] }}</td>
               <td class="py-3 text-on-surface-variant text-xs">{{ $row['output'] }}</td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>
</div>

<div class="grid lg:grid-cols-3 gap-4 mb-6">
   @foreach(['pre' => ['Inspeksi Pra Kerja', 'engineering'], 'during' => ['Observasi Saat Pekerjaan', 'visibility'], 'post' => ['Inspeksi Pasca Pekerjaan', 'task_alt']] as $key => [$title, $icon])
   <div class="ds-surface-card rounded-2xl p-5">
      <h3 class="font-bold text-sm mb-3 flex items-center gap-2">
         <span class="material-symbols-outlined text-primary text-lg">{{ $icon }}</span>
         {{ $title }}
      </h3>
      <ul class="space-y-2">
         @foreach($checklists[$key] as $item)
         <li class="flex items-start gap-2 text-xs text-on-surface-variant">
            <span class="material-symbols-outlined text-primary text-sm shrink-0">check_box_outline_blank</span>
            {{ $item }}
         </li>
         @endforeach
      </ul>
      @if($key === 'pre')
      <p class="mt-3 text-xs font-bold text-error">TIDAK LOLOS = PEKERJAAN DISTOP</p>
      @elseif($key === 'post')
      <p class="mt-3 text-xs font-bold text-error">Deviasi = REDO MAINTENANCE</p>
      @endif
   </div>
   @endforeach
</div>

<div class="ds-surface-card rounded-2xl p-6">
   <h2 class="font-headline font-bold text-base mb-4">Monitoring Aktivitas</h2>
   <div class="overflow-x-auto">
      <table class="ds-table w-full text-sm">
         <thead>
            <tr class="border-b border-outline-variant/20">
               <th class="text-left py-2">Aktivitas</th>
               <th class="text-left py-2">Section</th>
               <th class="text-left py-2">Pra Kerja</th>
               <th class="text-left py-2">Saat Kerja</th>
               <th class="text-left py-2">Pasca</th>
               <th class="text-left py-2">L3</th>
            </tr>
         </thead>
         <tbody>
            @foreach($activities as $act)
            <tr class="border-b border-outline-variant/10">
               <td class="py-3 font-semibold">{{ $act['activity'] }}</td>
               <td class="py-3">{{ $act['section'] }}</td>
               <td class="py-3"><span class="ds-badge ds-badge--success">{{ $act['pre'] }}</span></td>
               <td class="py-3"><span class="ds-badge {{ $act['during'] === 'Berlangsung' ? 'ds-badge--warning' : 'ds-badge--success' }}">{{ $act['during'] }}</span></td>
               <td class="py-3"><span class="ds-badge {{ $act['post'] === 'Belum' ? 'ds-badge--warning' : 'ds-badge--success' }}">{{ $act['post'] }}</span></td>
               <td class="py-3 text-xs text-on-surface-variant">{{ $act['l3'] }}</td>
            </tr>
            @endforeach
         </tbody>
      </table>
   </div>
</div>
@endsection
