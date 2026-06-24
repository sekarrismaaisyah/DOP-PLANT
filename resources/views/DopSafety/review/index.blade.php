@extends('DopSafety.layouts.app')

@section('title', 'Review L4 — DOP Safety')

@push('head')
@include('DopSafety.partials.styles')
@endpush

@section('content')
@include('DopSafety.partials.page-header', [
   'title' => 'L4 — Review & Evaluasi oleh DH & PJO',
   'subtitle' => 'Review planning harian, keputusan & arahan, continuous improvement terhadap deviasi berisiko.',
   'breadcrumb' => 'Review L4',
])

<div class="grid lg:grid-cols-3 gap-6">
   <div class="space-y-4">
      <div class="ds-surface-card rounded-2xl p-6">
         <span class="ds-level-pill ds-level-pill--l4 mb-3 inline-block">L4</span>
         <h2 class="font-headline font-bold text-base mb-4">Kewajiban DH & PJO</h2>
         <ul class="space-y-2">
            @foreach($duties as $duty)
            <li class="flex items-start gap-2 text-sm text-on-surface-variant">
               <span class="material-symbols-outlined text-primary text-sm shrink-0">arrow_right</span>
               {{ $duty }}
            </li>
            @endforeach
         </ul>
      </div>

      @if($kpi)
      <div class="ds-surface-card rounded-2xl p-6">
         <h3 class="font-bold text-sm mb-3">Parameter Keberhasilan</h3>
         <ul class="space-y-2">
            @foreach($kpi['parameters'] as $param)
            <li class="flex justify-between text-sm">
               <span class="text-on-surface-variant">{{ $param['label'] }}</span>
               <span class="ds-badge">{{ $param['target'] }}</span>
            </li>
            @endforeach
         </ul>
      </div>
      @endif
   </div>

   <div class="lg:col-span-2 ds-surface-card rounded-2xl p-6">
      <h2 class="font-headline font-bold text-base mb-4">Riwayat Review Aktivitas Kritikal</h2>
      <div class="space-y-4">
         @foreach($reviews as $review)
         <div class="p-4 rounded-xl border border-outline-variant/20 bg-white">
            <div class="flex items-start justify-between gap-3 mb-2">
               <p class="font-semibold text-sm">{{ $review['activity'] }}</p>
               <span class="ds-badge shrink-0">{{ $review['reviewer'] }}</span>
            </div>
            <p class="text-sm text-on-surface-variant"><strong>Keputusan:</strong> {{ $review['decision'] }}</p>
            <p class="text-sm text-on-surface-variant mt-1"><strong>Intervensi:</strong> {{ $review['intervention'] }}</p>
            <p class="text-xs text-on-surface-variant mt-2 opacity-70">{{ $review['reviewed_at'] }}</p>
         </div>
         @endforeach
      </div>
   </div>
</div>
@endsection
