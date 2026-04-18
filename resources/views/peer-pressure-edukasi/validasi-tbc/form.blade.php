@extends('peer-pressure-edukasi.layouts.peer-app')

@section('title', $mode === 'create' ? 'Tambah Validasi TBC' : 'Edit Validasi TBC')

@section('content')
@php
   $fields = [
      'validator' => 'Validator',
      'tasklist' => 'Tasklist',
      'to_be_concerned_hazard' => 'TobeConcernedHazard',
      'gr_pspp' => 'GR/PSPP',
      'catatan' => 'Catatan',
      'no_item_pspp' => 'No Item PSPP',
      'kategori_gr' => 'Kategori GR',
      'kategori_gr_valid_kpi' => 'Kategori GR valid KPI',
      'blindspot_terlapor_bc' => 'Blindspot terlapor BC',
      'pic_aktual' => 'PIC Aktual (pelaku/pelanggar)',
      'kronologi_singkat' => 'Kronologi Singkat (summary dari Deskripsi)',
      'rootcause_aktual' => 'Rootcause Aktual',
      'detail_rootcause_aktual' => 'Detail Rootcause Aktual',
      'tindakan_perbaikan_aktual' => 'Tindakan Perbaikan Aktual',
   ];
@endphp

<div class="">
   <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <div>
         <h2 class="font-headline font-bold text-xl text-on-surface">{{ $mode === 'create' ? 'Tambah data' : 'Edit data #' . $row->id }}</h2>
         <p class="text-xs text-on-surface-variant mt-1">Validasi TBC — lengkapi kolom sesuai kebutuhan.</p>
      </div>
      <a href="{{ route('peer-pressure-edukasi.validasi-tbc.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline">
         <span class="material-symbols-outlined text-base">arrow_back</span> Kembali ke daftar
      </a>
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

   <form method="post" action="{{ $mode === 'create' ? route('peer-pressure-edukasi.validasi-tbc.store') : route('peer-pressure-edukasi.validasi-tbc.update', $row->id) }}" class="bg-white rounded-2xl anchored-card overflow-hidden">
      @csrf
      @if ($mode === 'edit')
         @method('PUT')
      @endif

      <div class="p-6 space-y-4 border-b border-outline-variant/15">
         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($fields as $name => $label)
               <div class="{{ in_array($name, ['catatan', 'to_be_concerned_hazard', 'blindspot_terlapor_bc', 'kronologi_singkat', 'rootcause_aktual', 'detail_rootcause_aktual', 'tindakan_perbaikan_aktual'], true) ? 'md:col-span-2' : '' }}">
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">{{ $label }}</label>
                  <textarea name="{{ $name }}" rows="{{ in_array($name, ['validator', 'tasklist', 'gr_pspp', 'no_item_pspp', 'kategori_gr', 'kategori_gr_valid_kpi', 'pic_aktual'], true) ? 2 : 3 }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">{{ old($name, $row->{$name} ?? '') }}</textarea>
               </div>
            @endforeach
         </div>
      </div>

      <div class="p-6 bg-[#f8fafc] flex flex-wrap justify-end gap-3 border-t border-outline-variant/20">
         <a href="{{ route('peer-pressure-edukasi.validasi-tbc.index') }}" class="inline-flex items-center justify-center rounded-xl border border-outline-variant/30 bg-white px-5 py-2.5 text-sm font-bold text-on-surface hover:bg-white">Batal</a>
         <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md hover:opacity-95">
            {{ $mode === 'create' ? 'Simpan' : 'Perbarui' }}
         </button>
      </div>
   </form>
</div>
@endsection
