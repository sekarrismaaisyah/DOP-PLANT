@extends('PembatasanLV.layouts.app')

@section('title', 'Inputasi Pembatasan LV')

@section('page-header')
   @include('PembatasanLV.partials.page-header', [
      'breadcrumbCurrent' => 'Inputasi',
      'pageTitle' => 'Inputasi Data',
      'pageSubtitle' => 'Pencatatan LV dan orang masuk/keluar area',
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
   <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">
      {{ session('success') }}
   </div>
   @endif

   <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl">
      <button type="button" data-plv-open-inputasi="lv" class="group flex items-center gap-4 rounded-2xl border border-outline-variant/15 bg-white p-6 text-left shadow-sm transition-all duration-300 hover:border-primary/20 hover:shadow-md">
         <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary transition-transform duration-300 group-hover:scale-105">
            <span class="material-symbols-outlined text-2xl">local_shipping</span>
         </span>
         <div class="min-w-0 flex-1">
            <p class="font-headline font-bold text-base text-on-background">Inputasi LV</p>
            <p class="mt-1 text-xs text-on-surface-variant">Catat unit LV masuk/keluar — shift & control room otomatis</p>
         </div>
         <span class="material-symbols-outlined text-on-surface-variant/40 transition-transform duration-300 group-hover:translate-x-0.5">arrow_forward</span>
      </button>

      <button type="button" data-plv-open-inputasi="orang" class="group flex items-center gap-4 rounded-2xl border border-outline-variant/15 bg-white p-6 text-left shadow-sm transition-all duration-300 hover:border-primary/20 hover:shadow-md">
         <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary transition-transform duration-300 group-hover:scale-105">
            <span class="material-symbols-outlined text-2xl">groups</span>
         </span>
         <div class="min-w-0 flex-1">
            <p class="font-headline font-bold text-base text-on-background">Inputasi Orang</p>
            <p class="mt-1 text-xs text-on-surface-variant">Catat personel masuk/keluar — data karyawan dari SID</p>
         </div>
         <span class="material-symbols-outlined text-on-surface-variant/40 transition-transform duration-300 group-hover:translate-x-0.5">arrow_forward</span>
      </button>
   </div>

   @include('PembatasanLV.inputasi.partials.modals')
@endsection
