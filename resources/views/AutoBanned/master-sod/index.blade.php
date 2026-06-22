@extends('AutoBanned.layouts.app')

@section('title', 'Master SOD Auto Banned')

@section('page-header')
   @include('AutoBanned.partials.page-header', [
      'breadcrumbCurrent' => 'Master SOD',
      'pageTitle' => 'Master SOD',
      'pageSubtitle' => 'Kelola data referensi SOD — nama, site, dan nomor HP',
   ])
@endsection

@section('content')
   <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
      <form method="GET" action="{{ route('auto-banned.master-sod.index') }}" id="ab-master-sod-search-form" class="flex w-full max-w-md items-center gap-2">
         <div class="relative min-w-0 flex-1">
            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-lg text-on-surface-variant">search</span>
            <input type="search" id="ab-master-sod-search-input" name="q" value="{{ $q }}" placeholder="Cari nama, site, atau no HP…" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] py-2.5 pl-10 pr-3 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
         </div>
         <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-xl border border-outline-variant/30 bg-white px-4 py-2.5 text-xs font-bold shadow-sm hover:bg-surface-container-high">Cari</button>
         @if($q !== '')
         <button type="button" id="ab-master-sod-search-reset" class="inline-flex shrink-0 items-center justify-center rounded-xl px-3 py-2.5 text-xs font-bold text-on-surface-variant hover:bg-[#f1f5f9]">Reset</button>
         @endif
      </form>
   </div>

   <div class="bg-white rounded-2xl anchored-card overflow-hidden">
      <div class="flex flex-col gap-3 border-b border-outline-variant/20 p-6 sm:flex-row sm:items-center sm:justify-between">
         <div>
            <h3 class="font-headline font-bold text-lg text-on-background">Daftar Master SOD</h3>
            <p class="mt-1 text-xs text-on-surface-variant">Data SOD per site beserta nomor HP kontak.</p>
         </div>
         <button type="button" id="ab-master-sod-add-btn" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-95">
            <span class="material-symbols-outlined text-base">add</span>
            Tambah Data
         </button>
      </div>

      <div class="overflow-x-auto">
         @include('AutoBanned.master-sod.partials.master-sod-panel')
      </div>
   </div>

   @include('AutoBanned.master-sod.partials.master-sod-modal')
@endsection
