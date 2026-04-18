@extends('peer-pressure-edukasi.layouts.peer-app')

@section('title', $mode === 'create' ? 'Tambah Kelompok SBS' : 'Edit Kelompok SBS')

@section('content')
@php
   $anggotaOld = old('anggota', $anggotaRows);
   if (! is_array($anggotaOld)) {
      $anggotaOld = [['sid' => '', 'nama' => '']];
   }
@endphp

<div class="">
   <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <div>
         <h2 class="font-headline font-bold text-xl text-on-surface">{{ $mode === 'create' ? 'Tambah kelompok SBS' : 'Edit kelompok #' . $row->id }}</h2>
         <p class="text-xs text-on-surface-variant mt-1">Isi data kelompok lalu tambahkan satu atau lebih anggota.</p>
      </div>
      <a href="{{ route('peer-pressure-edukasi.sbs.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline">
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

   <form method="post" action="{{ $mode === 'create' ? route('peer-pressure-edukasi.sbs.store') : route('peer-pressure-edukasi.sbs.update', $row->id) }}" class="bg-white rounded-2xl anchored-card overflow-hidden">
      @csrf
      @if ($mode === 'edit')
         @method('PUT')
      @endif

      <div class="p-6 space-y-8 border-b border-outline-variant/15">
         <section>
            <h3 class="font-headline font-bold text-sm text-primary uppercase tracking-wider mb-4">Data kelompok</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Site</label>
                  <input type="text" name="site" value="{{ old('site', $row->site) }}" maxlength="255" placeholder="Opsional" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Perusahaan</label>
                  <input type="text" name="perusahaan" value="{{ old('perusahaan', $row->perusahaan) }}" maxlength="255" placeholder="Opsional" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Level grup</label>
                  <input type="text" name="level_grup" value="{{ old('level_grup', $row->level_grup) }}" required maxlength="255" placeholder="mis. Non Pengawas" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Nama kelompok</label>
                  <input type="text" name="nama_kelompok" value="{{ old('nama_kelompok', $row->nama_kelompok) }}" required maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Nama bapak asuh</label>
                  <input type="text" name="nama_bapak_asuh" value="{{ old('nama_bapak_asuh', $row->nama_bapak_asuh) }}" required maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">SID bapak asuh</label>
                  <input type="text" name="sid_bapak_asuh" value="{{ old('sid_bapak_asuh', $row->sid_bapak_asuh) }}" required maxlength="32" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
            </div>
         </section>

         <section>
            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
               <h3 class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Anggota (banyak baris)</h3>
               <button type="button" id="sbs-add-anggota" class="text-xs font-bold text-primary hover:underline">+ Tambah anggota</button>
            </div>
            <div id="sbs-anggota-rows" class="space-y-3">
               @foreach ($anggotaOld as $i => $rowAnggota)
                  <div class="sbs-repeat-row grid grid-cols-1 md:grid-cols-2 gap-3 rounded-xl border border-outline-variant/20 bg-[#f8fafc] p-3">
                     <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant mb-1">SID anggota</label>
                        <input type="text" name="anggota[{{ $i }}][sid]" value="{{ is_array($rowAnggota) ? ($rowAnggota['sid'] ?? '') : '' }}" maxlength="32" class="w-full rounded-lg border border-outline-variant/30 bg-white px-2 py-1.5 text-sm">
                     </div>
                     <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Nama anggota</label>
                        <input type="text" name="anggota[{{ $i }}][nama]" value="{{ is_array($rowAnggota) ? ($rowAnggota['nama'] ?? '') : '' }}" maxlength="255" class="w-full rounded-lg border border-outline-variant/30 bg-white px-2 py-1.5 text-sm">
                     </div>
                  </div>
               @endforeach
            </div>
         </section>
      </div>

      <div class="p-6 bg-[#f8fafc] flex flex-wrap justify-end gap-3 border-t border-outline-variant/20">
         <a href="{{ route('peer-pressure-edukasi.sbs.index') }}" class="inline-flex items-center justify-center rounded-xl border border-outline-variant/30 bg-white px-5 py-2.5 text-sm font-bold text-on-surface hover:bg-white">Batal</a>
         <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md hover:opacity-95">
            {{ $mode === 'create' ? 'Simpan' : 'Perbarui' }}
         </button>
      </div>
   </form>
</div>

<template id="sbs-tpl-anggota">
   <div class="sbs-repeat-row grid grid-cols-1 md:grid-cols-2 gap-3 rounded-xl border border-outline-variant/20 bg-[#f8fafc] p-3">
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">SID anggota</label>
         <input type="text" name="anggota[__IDX__][sid]" value="" maxlength="32" class="w-full rounded-lg border border-outline-variant/30 bg-white px-2 py-1.5 text-sm">
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Nama anggota</label>
         <input type="text" name="anggota[__IDX__][nama]" value="" maxlength="255" class="w-full rounded-lg border border-outline-variant/30 bg-white px-2 py-1.5 text-sm">
      </div>
   </div>
</template>
<script>
(function () {
  var addBtn = document.getElementById('sbs-add-anggota');
  var root = document.getElementById('sbs-anggota-rows');
  var tpl = document.getElementById('sbs-tpl-anggota');
  var idx = {{ count($anggotaOld) }};
  if (addBtn && root && tpl) {
    addBtn.addEventListener('click', function () {
      var html = tpl.innerHTML.replace(/__IDX__/g, String(idx));
      idx += 1;
      root.insertAdjacentHTML('beforeend', html);
    });
  }
})();
</script>
@endsection
