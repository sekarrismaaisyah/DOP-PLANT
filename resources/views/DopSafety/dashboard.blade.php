@extends('DopSafety.layouts.app')

@section('title', 'Dashboard KPI — DOP Safety')

@push('head')
@include('DopSafety.partials.styles')
@endpush

@section('content')
@include('DopSafety.partials.page-header', [
   'title' => 'Dashboard Program Darurat Keselamatan Plant',
   'subtitle' => config('dop_safety.program_subtitle') . ' · ' . config('dop_safety.organization'),
   'breadcrumb' => 'Dashboard KPI',
])

{{-- Leading Indicator Alert --}}
<div class="rounded-2xl p-5 ds-alert-critical ds-surface-card mb-6">
   <div class="flex items-start gap-3">
      <span class="material-symbols-outlined text-error text-2xl">warning</span>
      <div>
         <p class="font-bold text-sm text-error uppercase tracking-wide">Leading Indicator — Daily Coverage</p>
         <p class="text-sm text-on-surface mt-1">{{ $leadingIndicator['message'] }}</p>
         <p class="text-xs text-on-surface-variant mt-2">{{ $leadingIndicator['label'] }}</p>
      </div>
      <span class="ml-auto ds-badge {{ $leadingIndicator['target_met'] ? 'ds-badge--success' : 'ds-badge--danger' }}">
         {{ $leadingIndicator['target_met'] ? 'Target Tercapai' : 'Target Belum Tercapai' }}
      </span>
   </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('dop-safety.dashboard') }}" class="flex flex-wrap items-end gap-3 mb-6">
   <div>
      <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Tanggal</label>
      <input type="date" name="date" value="{{ $filters['date'] }}" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm font-semibold bg-white">
   </div>
   <div>
      <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Shift</label>
      <select name="shift" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm font-semibold bg-white min-w-[7rem]">
         @foreach($filterOptions['shifts'] as $s)
         <option value="{{ $s }}" @selected($filters['shift'] === $s)>{{ $s }}</option>
         @endforeach
      </select>
   </div>
   <div>
      <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Section</label>
      <select name="section" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm font-semibold bg-white min-w-[10rem]">
         @foreach($filterOptions['sections'] as $sec)
         <option value="{{ $sec }}" @selected($filters['section'] === $sec)>{{ $sec }}</option>
         @endforeach
      </select>
   </div>
   <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-sm font-bold text-white hover:opacity-95">
      <span class="material-symbols-outlined text-sm">filter_alt</span>
      Terapkan
   </button>
</form>

{{-- Summary Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
   @foreach([
      ['label' => 'Total Aktivitas DOP', 'value' => $summary['total_activities'], 'icon' => 'assignment'],
      ['label' => 'DOP Approved', 'value' => $summary['approved_dop'], 'icon' => 'check_circle'],
      ['label' => 'OJI Approved', 'value' => $summary['oji_approved'], 'icon' => 'verified_user'],
      ['label' => 'Daily Coverage', 'value' => $summary['coverage_pct'].'%', 'icon' => 'radar'],
   ] as $stat)
   <div class="ds-surface-card rounded-2xl p-5">
      <div class="flex items-center gap-3">
         <span class="material-symbols-outlined text-primary text-2xl">{{ $stat['icon'] }}</span>
         <div>
            <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">{{ $stat['label'] }}</p>
            <p class="text-2xl font-extrabold text-on-background">{{ $stat['value'] }}</p>
         </div>
      </div>
   </div>
   @endforeach
</div>

<div class="grid lg:grid-cols-3 gap-6">
   {{-- KPI per Level --}}
   <div class="lg:col-span-2 space-y-4">
      <h2 class="font-headline font-bold text-lg text-on-background">Parameter KPI per Level Pengawasan</h2>
      @foreach($kpiLevels as $level)
      @php
         $pillClass = match($level['level']) {
            'L1 & L2' => 'ds-level-pill--l1',
            'L3' => 'ds-level-pill--l3',
            'L4' => 'ds-level-pill--l4',
            default => 'ds-level-pill--l2',
         };
      @endphp
      <div class="ds-surface-card rounded-2xl p-5">
         <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
               <span class="ds-level-pill {{ $pillClass }}">{{ $level['level'] }}</span>
               <span class="text-sm font-semibold text-on-surface">{{ $level['role'] }}</span>
            </div>
            <span class="text-sm font-bold text-primary">{{ $level['progress'] ?? 0 }}%</span>
         </div>
         <div class="ds-progress-bar mb-4">
            <div class="ds-progress-fill" style="width: {{ $level['progress'] ?? 0 }}%"></div>
         </div>
         <ul class="space-y-2">
            @foreach($level['parameters'] as $param)
            <li class="flex items-center justify-between text-sm">
               <span class="text-on-surface-variant">{{ $param['label'] }}</span>
               <span class="ds-badge">{{ $param['target'] }}</span>
            </li>
            @endforeach
         </ul>
      </div>
      @endforeach
   </div>

   {{-- Flow L1 & L2 --}}
   <div>
      <h2 class="font-headline font-bold text-lg text-on-background mb-4">Flow L1 & L2 — GL & SH</h2>
      <div class="ds-surface-card rounded-2xl p-5">
         @foreach($flowSteps as $step)
         <div class="ds-flow-step">
            <div class="flex items-start gap-3 p-3 rounded-xl bg-surface-container-lowest border border-outline-variant/20">
               <span class="flex items-center justify-center w-7 h-7 rounded-full bg-primary text-white text-xs font-bold shrink-0">{{ $step['step'] }}</span>
               <div>
                  <p class="text-sm font-semibold text-on-surface">{{ $step['label'] }}</p>
                  @if($step['reject_label'])
                  <p class="text-xs text-error mt-0.5">TIDAK → {{ $step['reject_label'] }}</p>
                  @endif
               </div>
            </div>
         </div>
         @endforeach
      </div>
   </div>
</div>
@endsection
