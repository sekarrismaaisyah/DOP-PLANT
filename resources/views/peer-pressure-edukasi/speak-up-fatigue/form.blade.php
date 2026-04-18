@extends('peer-pressure-edukasi.layouts.peer-app')

@section('title', $mode === 'create' ? 'Tambah Speak Up Fatigue' : 'Edit Speak Up Fatigue')

@section('content')
@php
   $waktuVal = old('waktu');
   if ($waktuVal === null && isset($row->waktu) && $row->waktu !== null) {
      $raw = $row->waktu;
      $waktuVal = is_string($raw)
         ? (\Illuminate\Support\Str::length($raw) >= 8 ? substr($raw, 0, 5) : $raw)
         : \Carbon\Carbon::parse($raw)->format('H:i');
   }
@endphp

<div class="">
   <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <div>
         <h2 class="font-headline font-bold text-xl text-on-surface">{{ $mode === 'create' ? 'Tambah data' : 'Edit data #' . $row->id }}</h2>
         <p class="text-xs text-on-surface-variant mt-1">Speak Up Fatigue — Site, Perusahaan, SID, Nama, Tanggal, Waktu.</p>
      </div>
      <a href="{{ route('peer-pressure-edukasi.speak-up-fatigue.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline">
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

   <form method="post" action="{{ $mode === 'create' ? route('peer-pressure-edukasi.speak-up-fatigue.store') : route('peer-pressure-edukasi.speak-up-fatigue.update', $row->id) }}" class="bg-white rounded-2xl anchored-card overflow-hidden">
      @csrf
      @if ($mode === 'edit')
         @method('PUT')
      @endif

      <div class="p-6 space-y-6 border-b border-outline-variant/15">
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
               <label class="block text-xs font-bold text-on-surface-variant mb-1">SID</label>
               <input type="text" name="sid" value="{{ old('sid', $row->sid) }}" required maxlength="32" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
            </div>
            <div>
               <label class="block text-xs font-bold text-on-surface-variant mb-1">Nama</label>
               <input type="text" name="nama" value="{{ old('nama', $row->nama) }}" required maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
            </div>
            <div>
               <label class="block text-xs font-bold text-on-surface-variant mb-1">Tanggal</label>
               <input type="date" name="tanggal" value="{{ old('tanggal', $row->tanggal?->format('Y-m-d')) }}" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
            </div>
            <div>
               <label class="block text-xs font-bold text-on-surface-variant mb-1">Waktu</label>
               <input type="time" name="waktu" value="{{ $waktuVal }}" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
            </div>
         </div>
      </div>

      <div class="p-6 bg-[#f8fafc] flex flex-wrap justify-end gap-3 border-t border-outline-variant/20">
         <a href="{{ route('peer-pressure-edukasi.speak-up-fatigue.index') }}" class="inline-flex items-center justify-center rounded-xl border border-outline-variant/30 bg-white px-5 py-2.5 text-sm font-bold text-on-surface hover:bg-white">Batal</a>
         <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md hover:opacity-95">
            {{ $mode === 'create' ? 'Simpan' : 'Perbarui' }}
         </button>
      </div>
   </form>
</div>
@endsection
