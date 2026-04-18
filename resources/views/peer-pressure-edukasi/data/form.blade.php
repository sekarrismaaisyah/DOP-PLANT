@extends('peer-pressure-edukasi.layouts.peer-app')

@section('title', $mode === 'create' ? 'Tambah Data Peer Pressure' : 'Edit Data Peer Pressure')

@section('content')
@php
   $pelanggarOld = old('pelanggar', $pelanggarRows);
   if (! is_array($pelanggarOld)) {
      $pelanggarOld = [['sid' => '', 'nama' => '']];
   }
   $peerOld = old('peer', $peerRows);
   if (! is_array($peerOld)) {
      $peerOld = [['sid' => '', 'nama' => '']];
   }
   $timeIn = function (string $attr) use ($kejadian) {
      $v = old($attr);
      if ($v !== null && $v !== '') {
         $s = (string) $v;

         return strlen($s) >= 5 ? substr($s, 0, 5) : $s;
      }
      $m = $kejadian->{$attr} ?? null;
      if ($m === null || $m === '') {
         return '';
      }
      $s = (string) $m;

      return strlen($s) >= 5 ? substr($s, 0, 5) : $s;
   };
@endphp

<div class="">
   <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <div>
         <h2 class="font-headline font-bold text-xl text-on-surface">{{ $mode === 'create' ? 'Tambah kejadian' : 'Edit kejadian #' . $kejadian->id }}</h2>
         <p class="text-xs text-on-surface-variant mt-1">Isi data utama lalu tambahkan satu atau lebih pelanggar dan peer.</p>
      </div>
      <a href="{{ route('peer-pressure-edukasi.data.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline">
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

   <form method="post" action="{{ $mode === 'create' ? route('peer-pressure-edukasi.data.store') : route('peer-pressure-edukasi.data.update', $kejadian->id) }}" class="bg-white rounded-2xl anchored-card overflow-hidden">
      @csrf
      @if ($mode === 'edit')
         @method('PUT')
      @endif

      <div class="p-6 space-y-8 border-b border-outline-variant/15">
         <section>
            <h3 class="font-headline font-bold text-sm text-primary uppercase tracking-wider mb-4">Temuan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Tanggal temuan</label>
                  <input type="date" name="tanggal_temuan" value="{{ old('tanggal_temuan', $kejadian->tanggal_temuan?->format('Y-m-d')) }}" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Jam temuan</label>
                  <input type="time" name="jam_temuan" value="{{ $timeIn('jam_temuan') }}" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Kelompok lokasi temuan</label>
                  <input type="text" name="kelompok_lokasi_temuan" value="{{ old('kelompok_lokasi_temuan', $kejadian->kelompok_lokasi_temuan) }}" required maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Lokasi temuan</label>
                  <input type="text" name="lokasi_temuan" value="{{ old('lokasi_temuan', $kejadian->lokasi_temuan) }}" required maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
            </div>
         </section>

         <section>
            <h3 class="font-headline font-bold text-sm text-primary uppercase tracking-wider mb-4">Edukasi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
               <div class="md:col-span-2">
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Keterangan / kelompok lokasi edukasi</label>
                  <input type="text" name="kelompok_lokasi_edukasi" value="{{ old('kelompok_lokasi_edukasi', $kejadian->kelompok_lokasi_edukasi) }}" required maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div class="md:col-span-2">
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Lokasi edukasi</label>
                  <input type="text" name="lokasi_edukasi" value="{{ old('lokasi_edukasi', $kejadian->lokasi_edukasi) }}" required maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Tanggal edukasi</label>
                  <input type="date" name="tanggal_edukasi" value="{{ old('tanggal_edukasi', $kejadian->tanggal_edukasi?->format('Y-m-d')) }}" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Jam edukasi</label>
                  <input type="time" name="jam_edukasi" value="{{ $timeIn('jam_edukasi') }}" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Durasi edukasi (menit)</label>
                  <input type="number" name="durasi_edukasi_menit" value="{{ old('durasi_edukasi_menit', $kejadian->durasi_edukasi_menit) }}" required min="0" max="65535" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Status pelaksanaan edukasi</label>
                  <input type="text" name="status_pelaksanaan_edukasi" value="{{ old('status_pelaksanaan_edukasi', $kejadian->status_pelaksanaan_edukasi) }}" required maxlength="50" placeholder="mis. CLOSED, OPEN" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">SID / nama pemimpin edukasi</label>
                  <input type="text" name="pemimpin_edukasi" value="{{ old('pemimpin_edukasi', $kejadian->pemimpin_edukasi) }}" required maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
            </div>
         </section>

         <section>
            <h3 class="font-headline font-bold text-sm text-primary uppercase tracking-wider mb-4">Perusahaan &amp; uraian</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
               <div class="md:col-span-2">
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Perusahaan</label>
                  <input type="text" name="perusahaan" value="{{ old('perusahaan', $kejadian->perusahaan) }}" required maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div class="md:col-span-2">
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Fasilitas temuan (jika ada)</label>
                  <input type="text" name="tasklist_temuan" value="{{ old('tasklist_temuan', $kejadian->tasklist_temuan) }}" maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div class="md:col-span-2">
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Kronologi temuan</label>
                  <textarea name="kronologi_temuan" rows="4" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">{{ old('kronologi_temuan', $kejadian->kronologi_temuan) }}</textarea>
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Kategori deviasi</label>
                  <input type="text" name="kategori_deviasi" value="{{ old('kategori_deviasi', $kejadian->kategori_deviasi) }}" required maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Id berasal / berecord</label>
                  <input type="text" name="id_berecord" value="{{ old('id_berecord', $kejadian->id_berecord) }}" maxlength="64" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Departemen</label>
                  <input type="text" name="departemen" value="{{ old('departemen', $kejadian->departemen) }}" maxlength="100" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Jenis kelompok kerja</label>
                  <input type="text" name="jenis_kelompok_kerja" value="{{ old('jenis_kelompok_kerja', $kejadian->jenis_kelompok_kerja) }}" maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Kelompok aktivitas pekerjaan</label>
                  <input type="text" name="kelompok_aktivitas_pekerjaan" value="{{ old('kelompok_aktivitas_pekerjaan', $kejadian->kelompok_aktivitas_pekerjaan) }}" maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div>
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Aktivitas pekerjaan</label>
                  <input type="text" name="aktivitas_pekerjaan" value="{{ old('aktivitas_pekerjaan', $kejadian->aktivitas_pekerjaan) }}" maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
               <div class="md:col-span-2">
                  <label class="block text-xs font-bold text-on-surface-variant mb-1">Evidence (URL)</label>
                  <input type="text" name="evidence_url" value="{{ old('evidence_url', $kejadian->evidence_url) }}" placeholder="https://…" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               </div>
            </div>
         </section>

         <section>
            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
               <h3 class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Pelanggar (banyak baris)</h3>
               <button type="button" id="pp-add-pelanggar" class="text-xs font-bold text-primary hover:underline">+ Tambah pelanggar</button>
            </div>
            <div id="pp-pelanggar-rows" class="space-y-3">
               @foreach ($pelanggarOld as $i => $row)
                  <div class="pp-repeat-row grid grid-cols-1 md:grid-cols-2 gap-3 rounded-xl border border-outline-variant/20 bg-[#f8fafc] p-3">
                     <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant mb-1">SID pelanggar</label>
                        <input type="text" name="pelanggar[{{ $i }}][sid]" value="{{ is_array($row) ? ($row['sid'] ?? '') : '' }}" maxlength="32" class="w-full rounded-lg border border-outline-variant/30 bg-white px-2 py-1.5 text-sm">
                     </div>
                     <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Nama pelanggar</label>
                        <input type="text" name="pelanggar[{{ $i }}][nama]" value="{{ is_array($row) ? ($row['nama'] ?? '') : '' }}" maxlength="255" class="w-full rounded-lg border border-outline-variant/30 bg-white px-2 py-1.5 text-sm">
                     </div>
                  </div>
               @endforeach
            </div>
         </section>

         <section>
            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
               <h3 class="font-headline font-bold text-sm text-primary uppercase tracking-wider">Peer (banyak baris)</h3>
               <button type="button" id="pp-add-peer" class="text-xs font-bold text-primary hover:underline">+ Tambah peer</button>
            </div>
            <div id="pp-peer-rows" class="space-y-3">
               @foreach ($peerOld as $i => $row)
                  <div class="pp-repeat-row grid grid-cols-1 md:grid-cols-2 gap-3 rounded-xl border border-outline-variant/20 bg-[#f8fafc] p-3">
                     <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant mb-1">SID peer</label>
                        <input type="text" name="peer[{{ $i }}][sid]" value="{{ is_array($row) ? ($row['sid'] ?? '') : '' }}" maxlength="32" class="w-full rounded-lg border border-outline-variant/30 bg-white px-2 py-1.5 text-sm">
                     </div>
                     <div>
                        <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Nama peer</label>
                        <input type="text" name="peer[{{ $i }}][nama]" value="{{ is_array($row) ? ($row['nama'] ?? '') : '' }}" maxlength="255" class="w-full rounded-lg border border-outline-variant/30 bg-white px-2 py-1.5 text-sm">
                     </div>
                  </div>
               @endforeach
            </div>
         </section>
      </div>

      <div class="p-6 bg-[#f8fafc] flex flex-wrap justify-end gap-3 border-t border-outline-variant/20">
         <a href="{{ route('peer-pressure-edukasi.data.index') }}" class="inline-flex items-center justify-center rounded-xl border border-outline-variant/30 bg-white px-5 py-2.5 text-sm font-bold text-on-surface hover:bg-white">Batal</a>
         <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md hover:opacity-95">
            {{ $mode === 'create' ? 'Simpan' : 'Perbarui' }}
         </button>
      </div>
   </form>
</div>

<template id="pp-tpl-pelanggar">
   <div class="pp-repeat-row grid grid-cols-1 md:grid-cols-2 gap-3 rounded-xl border border-outline-variant/20 bg-[#f8fafc] p-3">
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">SID pelanggar</label>
         <input type="text" name="pelanggar[__IDX__][sid]" value="" maxlength="32" class="w-full rounded-lg border border-outline-variant/30 bg-white px-2 py-1.5 text-sm">
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Nama pelanggar</label>
         <input type="text" name="pelanggar[__IDX__][nama]" value="" maxlength="255" class="w-full rounded-lg border border-outline-variant/30 bg-white px-2 py-1.5 text-sm">
      </div>
   </div>
</template>
<template id="pp-tpl-peer">
   <div class="pp-repeat-row grid grid-cols-1 md:grid-cols-2 gap-3 rounded-xl border border-outline-variant/20 bg-[#f8fafc] p-3">
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">SID peer</label>
         <input type="text" name="peer[__IDX__][sid]" value="" maxlength="32" class="w-full rounded-lg border border-outline-variant/30 bg-white px-2 py-1.5 text-sm">
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Nama peer</label>
         <input type="text" name="peer[__IDX__][nama]" value="" maxlength="255" class="w-full rounded-lg border border-outline-variant/30 bg-white px-2 py-1.5 text-sm">
      </div>
   </div>
</template>
<script>
(function () {
  var addPel = document.getElementById('pp-add-pelanggar');
  var addPeer = document.getElementById('pp-add-peer');
  var rootPel = document.getElementById('pp-pelanggar-rows');
  var rootPeer = document.getElementById('pp-peer-rows');
  var tplPel = document.getElementById('pp-tpl-pelanggar');
  var tplPeer = document.getElementById('pp-tpl-peer');
  var pelIdx = {{ count($pelanggarOld) }};
  var peerIdx = {{ count($peerOld) }};
  if (addPel && rootPel && tplPel) {
    addPel.addEventListener('click', function () {
      var html = tplPel.innerHTML.replace(/__IDX__/g, String(pelIdx));
      pelIdx += 1;
      rootPel.insertAdjacentHTML('beforeend', html);
    });
  }
  if (addPeer && rootPeer && tplPeer) {
    addPeer.addEventListener('click', function () {
      var html = tplPeer.innerHTML.replace(/__IDX__/g, String(peerIdx));
      peerIdx += 1;
      rootPeer.insertAdjacentHTML('beforeend', html);
    });
  }
})();
</script>
@endsection
