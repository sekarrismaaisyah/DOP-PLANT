@extends('PembatasanLV.layouts.app')

@section('title', 'Master Data Pembatasan LV')

@php
   $tabActiveClass = 'text-[#3952bc] border-b-2 border-[#3952bc] pb-3 font-bold text-sm tracking-tight';
   $tabInactiveClass = 'text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors pb-3 border-b-2 border-transparent';
   $thClass = 'px-6 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant';
   $tdClass = 'px-6 py-4 text-sm text-on-surface';
@endphp

@section('page-header')
   @include('PembatasanLV.partials.page-header', [
      'breadcrumbCurrent' => 'Master Data',
      'pageTitle' => 'Master Data',
      'pageSubtitle' => 'Kelola data referensi site, control room, dan unit LV',
   ])
@endsection

@section('content')
   <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
      <div class="flex flex-wrap items-center gap-8 border-b border-outline-variant/30 lg:border-b-0" role="tablist" aria-label="Kategori master data">
         <a href="{{ route('pembatasan-lv.master-data.index', array_filter(['tab' => 'site', 'q' => $q ?: null])) }}" role="tab" class="{{ $activeTab === 'site' ? $tabActiveClass : $tabInactiveClass }}">
            <span class="inline-flex items-center gap-2">
               <span class="material-symbols-outlined text-lg">location_on</span>
               Master Batas Lv Per Lokasi
            </span>
         </a>
         <a href="{{ route('pembatasan-lv.master-data.index', array_filter(['tab' => 'control-room', 'q' => $q ?: null])) }}" role="tab" class="{{ $activeTab === 'control-room' ? $tabActiveClass : $tabInactiveClass }}">
            <span class="inline-flex items-center gap-2">
               <span class="material-symbols-outlined text-lg">meeting_room</span>
               Pengawas Control Room
            </span>
         </a>
         <a href="{{ route('pembatasan-lv.master-data.index', array_filter(['tab' => 'lv', 'q' => $q ?: null])) }}" role="tab" class="{{ $activeTab === 'lv' ? $tabActiveClass : $tabInactiveClass }}">
            <span class="inline-flex items-center gap-2">
               <span class="material-symbols-outlined text-lg">local_shipping</span>
               Unit LV
            </span>
         </a>
      </div>

      <form method="GET" action="{{ route('pembatasan-lv.master-data.index') }}" id="plv-master-search-form" class="flex w-full max-w-md items-center gap-2">
         <input type="hidden" name="tab" value="{{ $activeTab }}"/>
         <div class="relative min-w-0 flex-1">
            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-lg text-on-surface-variant">search</span>
            <input type="search" id="plv-master-search-input" name="q" value="{{ $q }}" placeholder="Cari data master…" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] py-2.5 pl-10 pr-3 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
         </div>
         <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-xl border border-outline-variant/30 bg-white px-4 py-2.5 text-xs font-bold shadow-sm hover:bg-surface-container-high">Cari</button>
         @if($q !== '' && !in_array($activeTab, ['site', 'control-room', 'lv'], true))
         <a href="{{ route('pembatasan-lv.master-data.index', ['tab' => $activeTab]) }}" class="inline-flex shrink-0 items-center justify-center rounded-xl px-3 py-2.5 text-xs font-bold text-on-surface-variant hover:bg-[#f1f5f9]">Reset</a>
         @endif
         @if(in_array($activeTab, ['site', 'control-room', 'lv'], true))
         <button type="button" id="plv-master-search-reset" class="inline-flex shrink-0 items-center justify-center rounded-xl px-3 py-2.5 text-xs font-bold text-on-surface-variant hover:bg-[#f1f5f9] {{ $q === '' ? 'hidden' : '' }}">Reset</button>
         @endif
      </form>
   </div>

   <div class="bg-white rounded-2xl anchored-card overflow-hidden">
      <div class="flex flex-col gap-3 border-b border-outline-variant/20 p-6 sm:flex-row sm:items-center sm:justify-between">
         <div>
            @if($activeTab === 'site')
            <h3 class="font-headline font-bold text-lg text-on-background">Master Batas LV Per Lokasi</h3>
            <p class="mt-1 text-xs text-on-surface-variant">Kelola batas jumlah LV per site dan lokasi.</p>
            @elseif($activeTab === 'control-room')
            <h3 class="font-headline font-bold text-lg text-on-background">Pengawas Control Room</h3>
            <p class="mt-1 text-xs text-on-surface-variant">Kelola pengawas per control room beserta batas LV dan detail lokasi.</p>
            @else
            <h3 class="font-headline font-bold text-lg text-on-background">Master Unit LV</h3>
            <p class="mt-1 text-xs text-on-surface-variant">Data unit dari Becomeline (becomeline_unit).</p>
            @endif
         </div>
         @if($activeTab === 'site')
         <button type="button" id="plv-batas-lv-add-btn" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-95">
            <span class="material-symbols-outlined text-base">add</span>
            Tambah Data
         </button>
         @elseif($activeTab === 'control-room')
         <button type="button" id="plv-cr-pengawas-add-btn" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-95">
            <span class="material-symbols-outlined text-base">person_add</span>
            Tambah Pengawas
         </button>
         @endif
      </div>

      <div class="overflow-x-auto">
         @if($activeTab === 'site')
         @include('PembatasanLV.master-data.partials.batas-lv-per-lokasi-panel', ['siteOptions' => $siteOptions, 'q' => $q])

         @elseif($activeTab === 'control-room')
         @include('PembatasanLV.master-data.partials.control-room-pengawas-panel', [
            'controlRoomOptions' => $controlRoomOptions,
            'q' => $q,
            'activeTab' => $activeTab,
         ])

         @else
         @include('PembatasanLV.master-data.partials.unit-lv-panel', ['q' => $q])
         @endif
      </div>
   </div>

   @if($activeTab === 'site')
   @include('PembatasanLV.master-data.partials.batas-lv-per-lokasi-modal', ['siteOptions' => $siteOptions])
   @endif

   @if($activeTab === 'control-room')
   @include('PembatasanLV.master-data.partials.control-room-pengawas-modal', ['controlRoomOptions' => $controlRoomOptions])
   @endif
@endsection
