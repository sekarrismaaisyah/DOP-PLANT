@extends('DopSafety.layouts.app')

@section('title', 'FGD — DOP Safety')

@push('head')
@include('DopSafety.partials.styles')
@endpush

@section('content')
@include('DopSafety.partials.page-header', [
   'title' => 'Bagian F — FGD Per 2 Minggu Per Section',
   'subtitle' => 'Agenda FGD setiap 2 minggu + tindak lanjut. Peserta: Mekanik + GL + SH.',
   'breadcrumb' => 'FGD',
])

<div class="flex flex-wrap gap-2 mb-6">
   <span class="text-xs font-bold uppercase text-on-surface-variant mr-2">Peserta:</span>
   @foreach($participants as $p)
   <span class="ds-badge">{{ $p }}</span>
   @endforeach
   <span class="text-xs font-bold uppercase text-on-surface-variant mx-2">|</span>
   <span class="text-xs font-bold uppercase text-on-surface-variant mr-2">Output:</span>
   @foreach($outputs as $o)
   <span class="ds-badge ds-badge--success">{{ $o }}</span>
   @endforeach
</div>

<div class="grid lg:grid-cols-2 gap-6">
   <div class="ds-surface-card rounded-2xl p-6">
      <h2 class="font-headline font-bold text-base mb-4">Jadwal FGD 8 Minggu</h2>
      <div class="overflow-x-auto">
         <table class="ds-table w-full text-sm">
            <thead>
               <tr class="border-b border-outline-variant/20">
                  <th class="text-left py-2">Periode</th>
                  <th class="text-left py-2">Section</th>
                  <th class="text-left py-2">Tema</th>
               </tr>
            </thead>
            <tbody>
               @foreach($schedule as $item)
               <tr class="border-b border-outline-variant/10">
                  <td class="py-3 font-semibold">{{ $item['period'] }}</td>
                  <td class="py-3 text-on-surface-variant">{{ $item['section'] }}</td>
                  <td class="py-3">{{ $item['theme'] }}</td>
               </tr>
               @endforeach
            </tbody>
         </table>
      </div>
   </div>

   <div class="ds-surface-card rounded-2xl p-6">
      <h2 class="font-headline font-bold text-base mb-4">Sesi FGD Berjalan</h2>
      <div class="space-y-3">
         @foreach($sessions as $session)
         @php
            $statusClass = $session['status'] === 'Selesai' ? 'ds-badge--success' : 'ds-badge--warning';
         @endphp
         <div class="p-4 rounded-xl border border-outline-variant/20">
            <div class="flex items-center justify-between mb-2">
               <span class="font-semibold text-sm">{{ $session['theme'] }}</span>
               <span class="ds-badge {{ $statusClass }}">{{ $session['status'] }}</span>
            </div>
            <p class="text-xs text-on-surface-variant">{{ $session['period'] }} · {{ $session['action_items'] }} action items</p>
         </div>
         @endforeach
      </div>
   </div>
</div>
@endsection
