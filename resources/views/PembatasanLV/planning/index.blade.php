@extends('PembatasanLV.layouts.app')

@section('title', 'Planning Pembatasan LV')

@php
   $activeTab = in_array(request('tab'), ['orang'], true) ? 'orang' : 'lv';
   $tabActiveClass = 'rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm';
   $tabInactiveClass = 'rounded-lg px-4 py-2 text-xs font-semibold text-on-surface-variant hover:bg-white/80';
@endphp

@section('page-header')
   @include('PembatasanLV.partials.page-header', [
      'breadcrumbCurrent' => 'Planning',
      'pageTitle' => 'Planning',
      'pageSubtitle' => 'Rencana LV dan orang — format isian sama dengan inputasi',
   ])
@endsection

@section('content')
   <div class="mb-4">
      <a href="{{ route('pembatasan-lv.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline">
         <span class="material-symbols-outlined text-base">arrow_back</span>
         Kembali ke Overview
      </a>
   </div>

   @if (session('success'))
   <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">{{ session('success') }}</div>
   @endif
   @if (session('error'))
   <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">{{ session('error') }}</div>
   @endif

   <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl mb-8">
      <button type="button" data-plv-open-planning="lv" class="group flex items-center gap-4 rounded-2xl border border-outline-variant/15 bg-white p-6 text-left shadow-sm transition-all duration-300 hover:border-primary/20 hover:shadow-md">
         <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary transition-transform duration-300 group-hover:scale-105">
            <span class="material-symbols-outlined text-2xl">local_shipping</span>
         </span>
         <div class="min-w-0 flex-1">
            <p class="font-headline font-bold text-base text-on-background">Planning LV</p>
            <p class="mt-1 text-xs text-on-surface-variant">Isian sama dengan inputasi LV + tanggal & shift rencana</p>
         </div>
         <span class="material-symbols-outlined text-on-surface-variant/40 transition-transform duration-300 group-hover:translate-x-0.5">arrow_forward</span>
      </button>

      <button type="button" data-plv-open-planning="orang" class="group flex items-center gap-4 rounded-2xl border border-outline-variant/15 bg-white p-6 text-left shadow-sm transition-all duration-300 hover:border-primary/20 hover:shadow-md">
         <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary transition-transform duration-300 group-hover:scale-105">
            <span class="material-symbols-outlined text-2xl">groups</span>
         </span>
         <div class="min-w-0 flex-1">
            <p class="font-headline font-bold text-base text-on-background">Planning Orang</p>
            <p class="mt-1 text-xs text-on-surface-variant">Isian sama dengan inputasi orang + tanggal & shift rencana</p>
         </div>
         <span class="material-symbols-outlined text-on-surface-variant/40 transition-transform duration-300 group-hover:translate-x-0.5">arrow_forward</span>
      </button>
   </div>

   <div class="bg-white rounded-2xl anchored-card overflow-hidden">
      <div class="flex flex-col gap-4 border-b border-outline-variant/20 p-6 lg:flex-row lg:items-center lg:justify-between">
         <div class="inline-flex p-1 rounded-xl bg-[#f1f5f9]/80 gap-0.5" role="tablist" aria-label="Daftar planning">
            <a href="{{ route('pembatasan-lv.planning.index', ['tab' => 'lv']) }}" role="tab" class="{{ $activeTab === 'lv' ? $tabActiveClass : $tabInactiveClass }}">Planning LV</a>
            <a href="{{ route('pembatasan-lv.planning.index', ['tab' => 'orang']) }}" role="tab" class="{{ $activeTab === 'orang' ? $tabActiveClass : $tabInactiveClass }}">Planning Orang</a>
         </div>
         <div class="flex flex-wrap items-center gap-2">
            @if($activeTab === 'lv')
            <a href="{{ route('pembatasan-lv.planning.lv.template') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-[11px] font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">download</span> Template LV
            </a>
            <button type="button" id="plv-planning-open-import-lv" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-[11px] font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">upload_file</span> Import LV
            </button>
            <button type="button" data-plv-open-planning="lv" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-3 py-2 text-[11px] font-bold text-white shadow-md hover:opacity-95">
               <span class="material-symbols-outlined text-base">add</span> Tambah LV
            </button>
            @else
            <a href="{{ route('pembatasan-lv.planning.orang.template') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-[11px] font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">download</span> Template Orang
            </a>
            <button type="button" id="plv-planning-open-import-orang" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-[11px] font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">upload_file</span> Import Orang
            </button>
            <button type="button" data-plv-open-planning="orang" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-3 py-2 text-[11px] font-bold text-white shadow-md hover:opacity-95">
               <span class="material-symbols-outlined text-base">add</span> Tambah Orang
            </button>
            @endif
         </div>
      </div>

      @if($activeTab === 'lv')
      @include('PembatasanLV.planning.partials.data-lv-panel')
      @else
      @include('PembatasanLV.planning.partials.data-orang-panel')
      @endif
   </div>

   @include('PembatasanLV.planning.partials.modals')
   @include('PembatasanLV.planning.partials.import-modals')
@endsection
