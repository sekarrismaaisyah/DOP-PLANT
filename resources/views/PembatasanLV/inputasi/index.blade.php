@extends('PembatasanLV.layouts.app')

@section('title', 'Inputasi Pembatasan LV')

@php
   $activeTab = old('tipe') === 'orang' ? 'orang' : (request('tab') === 'orang' ? 'orang' : 'lv');
   $tabActiveClass = 'text-[#3952bc] border-b-2 border-[#3952bc] pb-3 font-bold text-sm tracking-tight';
   $tabInactiveClass = 'text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors pb-3 border-b-2 border-transparent';
@endphp

@section('page-header')
   @include('PembatasanLV.partials.page-header', [
      'breadcrumbCurrent' => 'Inputasi',
      'pageTitle' => 'Inputasi Data',
      'pageSubtitle' => 'Form pencatatan LV masuk/keluar dan orang masuk/keluar',
   ])
@endsection

@section('content')
   <div class="mb-4">
      <a href="{{ route('pembatasan-lv.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline">
         <span class="material-symbols-outlined text-base">arrow_back</span>
         Kembali ke Overview
      </a>
   </div>

   <div class="mb-6 flex flex-wrap items-center gap-8 border-b border-outline-variant/30" role="tablist" aria-label="Jenis inputasi">
      <button type="button" role="tab" id="plv-tab-lv" data-plv-inputasi-tab="lv" aria-selected="{{ $activeTab === 'lv' ? 'true' : 'false' }}" aria-controls="plv-panel-lv" class="{{ $activeTab === 'lv' ? $tabActiveClass : $tabInactiveClass }}">
         <span class="inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">local_shipping</span>
            Inputasi LV
         </span>
      </button>
      <button type="button" role="tab" id="plv-tab-orang" data-plv-inputasi-tab="orang" aria-selected="{{ $activeTab === 'orang' ? 'true' : 'false' }}" aria-controls="plv-panel-orang" class="{{ $activeTab === 'orang' ? $tabActiveClass : $tabInactiveClass }}">
         <span class="inline-flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">groups</span>
            Inputasi Orang
         </span>
      </button>
   </div>

   @if ($errors->any())
   <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">
      <p class="font-bold mb-2">Periksa input berikut:</p>
      <ul class="list-disc list-inside space-y-1">
         @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
         @endforeach
      </ul>
   </div>
   @endif

   @if (session('success'))
   <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">
      {{ session('success') }}
   </div>
   @endif

   <div id="plv-panel-lv" role="tabpanel" aria-labelledby="plv-tab-lv" data-plv-inputasi-panel="lv" class="{{ $activeTab === 'orang' ? 'hidden' : '' }}">
      @include('PembatasanLV.inputasi.partials.tab-lv')
   </div>

   <div id="plv-panel-orang" role="tabpanel" aria-labelledby="plv-tab-orang" data-plv-inputasi-panel="orang" class="{{ $activeTab === 'orang' ? '' : 'hidden' }}">
      @include('PembatasanLV.inputasi.partials.tab-orang')
   </div>
@endsection

@push('scripts')
<script>
(function () {
   var tabs = document.querySelectorAll('[data-plv-inputasi-tab]');
   var panels = document.querySelectorAll('[data-plv-inputasi-panel]');
   var tabActive = 'text-[#3952bc] border-b-2 border-[#3952bc] pb-3 font-bold text-sm tracking-tight';
   var tabInactive = 'text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors pb-3 border-b-2 border-transparent';

   function showTab(name) {
      tabs.forEach(function (tab) {
         var isActive = tab.getAttribute('data-plv-inputasi-tab') === name;
         tab.className = isActive ? tabActive : tabInactive;
         tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });
      panels.forEach(function (panel) {
         var isActive = panel.getAttribute('data-plv-inputasi-panel') === name;
         panel.classList.toggle('hidden', !isActive);
      });
      if (window.history.replaceState) {
         var url = new URL(window.location.href);
         url.searchParams.set('tab', name);
         window.history.replaceState({}, '', url);
      }
   }

   tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
         showTab(tab.getAttribute('data-plv-inputasi-tab'));
      });
   });
})();
</script>
@endpush
