@extends('peer-pressure-edukasi.layouts.peer-app')

@section('title', 'Grup SBS')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
@php
   $peerFotoUrls = $peerFotoUrls ?? [];
   $openImportModal = request('modal') === 'import' || $errors->any();
@endphp

{{-- Modal Import Excel --}}
<div id="sbs-import-modal" class="fixed inset-0 z-[100] {{ $openImportModal ? 'flex' : 'hidden' }} items-center justify-center p-4" aria-modal="true" role="dialog" aria-labelledby="sbs-import-modal-title" aria-hidden="{{ $openImportModal ? 'false' : 'true' }}">
   <div class="absolute inset-0 z-0 bg-black/40 backdrop-blur-[1px]" data-sbs-close-modal tabindex="-1" aria-hidden="true"></div>
   <div class="relative z-10 w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-card-heavy border border-outline-variant/20">
      <div class="sticky top-0 flex items-start justify-between gap-3 border-b border-outline-variant/15 bg-white px-5 py-4 rounded-t-2xl">
         <div>
            <h3 id="sbs-import-modal-title" class="font-headline font-bold text-lg text-on-surface">Import Excel — Grup SBS</h3>
            <p class="text-[11px] text-on-surface-variant mt-1 leading-relaxed">Satu sheet (pertama): tiap baris satu anggota; kolom kelompok diulang. Header baris pertama jangan diubah.</p>
         </div>
         <button type="button" class="shrink-0 rounded-lg p-1.5 text-on-surface-variant hover:bg-[#f1f5f9] hover:text-on-surface" data-sbs-close-modal aria-label="Tutup">
            <span class="material-symbols-outlined text-xl">close</span>
         </button>
      </div>
      <div class="px-5 py-4 space-y-4">
         @if ($errors->any())
         <div class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-900" role="alert">
            <p class="font-bold mb-1">Periksa file</p>
            <ul class="list-disc list-inside space-y-0.5">
               @foreach ($errors->all() as $err)
                  <li>{{ $err }}</li>
               @endforeach
            </ul>
         </div>
         @endif
         <div class="rounded-xl border border-outline-variant/20 bg-[#f8fafc] p-3 text-[11px] text-on-surface-variant leading-relaxed">
            <p class="font-bold text-on-surface mb-1.5">Ringkas</p>
            <ul class="list-disc list-inside space-y-1">
               <li>Unduh template, isi sheet pertama — per baris: data kelompok + satu anggota.</li>
               <li>Anggota lain: baris baru dengan kolom kelompok identik &amp; <strong>Nama Kelompok</strong> sama.</li>
            </ul>
         </div>
         <div class="flex flex-wrap gap-2">
            <a href="{{ route('peer-pressure-edukasi.sbs.template') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-[11px] font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">download</span> Unduh template
            </a>
         </div>
         <form method="post" action="{{ route('peer-pressure-edukasi.sbs.import') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
               <label class="block text-[11px] font-bold text-on-surface-variant mb-1.5">File Excel (.xlsx / .xls, maks. 10 MB)</label>
               <input type="file" name="excel_file" accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" required class="block w-full text-xs text-on-surface file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-2 file:font-bold file:text-primary">
            </div>
            <div class="flex flex-wrap justify-end gap-2 pt-1">
               <button type="button" class="inline-flex items-center justify-center rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold text-on-surface hover:bg-[#f8fafc]" data-sbs-close-modal>Batal</button>
               <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:opacity-95">
                  <span class="material-symbols-outlined text-base">upload_file</span> Unggah &amp; impor
               </button>
            </div>
         </form>
      </div>
   </div>
</div>

<div class="bg-white rounded-2xl anchored-card overflow-hidden">
   <div class="p-6 border-b border-outline-variant/20 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
      <div>
         <h2 class="font-headline font-bold text-xl text-on-surface">Grup SBS</h2>
         <p class="text-xs text-on-surface-variant font-medium">Kelola kelompok SBS, bapak asuh, dan daftar anggota.</p>
      </div>
      <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
         <form method="get" action="{{ route('peer-pressure-edukasi.sbs.index') }}" class="flex w-full min-w-0 flex-1 flex-col gap-2 sm:max-w-md sm:flex-row sm:items-center">
            <div class="relative min-w-0 flex-1">
               <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 z-0 -translate-y-1/2 text-lg text-on-surface-variant">search</span>
               <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari site, perusahaan, level, nama kelompok, bapak asuh, SID, anggota…" autocomplete="off" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] py-2 pl-10 pr-3 text-sm text-on-surface outline-none transition-shadow placeholder:text-on-surface-variant/60 focus:border-primary/40 focus:ring-2 focus:ring-primary/15" aria-label="Cari kelompok SBS">
            </div>
            <div class="flex shrink-0 gap-2">
               <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm transition-colors hover:bg-surface-container-high">Cari</button>
               @if(filled($q ?? null))
               <a href="{{ route('peer-pressure-edukasi.sbs.index') }}" class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-bold text-on-surface-variant hover:bg-[#f1f5f9]">Reset</a>
               @endif
            </div>
         </form>
         <div class="flex flex-wrap gap-2">
            <a href="{{ route('peer-pressure-edukasi.sbs.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md transition-transform active:scale-95">
               <span class="material-symbols-outlined text-base">add</span> Tambah kelompok
            </a>
            <a href="{{ route('peer-pressure-edukasi.sbs.template') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">download</span> Template Excel
            </a>
            <button type="button" id="sbs-open-import-modal" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">upload_file</span> Import Excel
            </button>
         </div>
      </div>
   </div>
   <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
         <thead class="bg-[#f8fafc] text-on-surface-variant font-bold text-[10px] uppercase tracking-[0.15em] border-b border-outline-variant/20">
            <tr>
               <th class="px-8 py-5">Site</th>
               <th class="px-8 py-5">Perusahaan</th>
               <th class="px-8 py-5">Level Grup</th>
               <th class="px-8 py-5">Nama Kelompok</th>
               <th class="px-8 py-5">Bapak Asuh</th>
               <th class="px-8 py-5">Anggota</th>
               <th class="px-8 py-5 w-36">Aksi</th>
            </tr>
         </thead>
         <tbody class="divide-y divide-outline-variant/10">
            @forelse ($kelompok as $k)
               @php
                  $anggotaList = $k->anggota;
                  $visibleAnggota = $anggotaList->take(3);
                  $extraAnggotaCount = max(0, $anggotaList->count() - 3);
                  $avatarBgs = ['bg-secondary-fixed', 'bg-primary-fixed', 'bg-tertiary-fixed'];
                  $sidKeyBapak = \Illuminate\Support\Str::lower(trim((string) $k->sid_bapak_asuh));
                  $bapakFoto = $peerFotoUrls[$sidKeyBapak] ?? null;
               @endphp
               <tr class="hover:bg-[#f8fafc] transition-colors align-top">
                  <td class="px-8 py-5">
                     <div class="font-bold text-on-surface">{{ filled($k->site) ? $k->site : '—' }}</div>
                  </td>
                  <td class="px-8 py-5">
                     <div class="text-xs text-on-surface">{{ filled($k->perusahaan) ? $k->perusahaan : '—' }}</div>
                  </td>
                  <td class="px-8 py-5">
                     <span class="inline-block bg-primary-container/20 text-primary text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider border border-primary/10">{{ $k->level_grup }}</span>
                  </td>
                  <td class="px-8 py-5">
                     <div class="font-bold text-on-surface">{{ $k->nama_kelompok }}</div>
                  </td>
                  <td class="px-8 py-5">
                     <div class="flex items-start gap-3">
                        <div class="relative w-8 h-8 shrink-0 rounded-full border-2 border-white shadow-md overflow-hidden bg-surface-container-high">
                           @if (filled($bapakFoto))
                              <img src="{{ $bapakFoto }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" width="32" height="32" decoding="async" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
                              <div class="hidden absolute inset-0 flex items-center justify-center text-[10px] font-bold bg-primary-fixed">{{ $k->bapakAsuhInitials() }}</div>
                           @else
                              <div class="flex h-full w-full items-center justify-center text-[10px] font-bold bg-primary-fixed">{{ $k->bapakAsuhInitials() }}</div>
                           @endif
                        </div>
                        <div>
                           <div class="font-bold text-on-surface text-xs">{{ $k->nama_bapak_asuh }}</div>
                           <div class="text-[10px] text-on-surface-variant font-medium">{{ $k->sid_bapak_asuh }}</div>
                        </div>
                     </div>
                  </td>
                  <td class="px-8 py-5">
                     @if ($anggotaList->isEmpty())
                        <span class="text-on-surface-variant text-xs">—</span>
                     @else
                        <div class="flex -space-x-2">
                           @foreach ($visibleAnggota as $ag)
                              @php
                                 $pi = $loop->index;
                                 $sidKeyAg = \Illuminate\Support\Str::lower(trim((string) $ag->sid));
                                 $agFoto = $peerFotoUrls[$sidKeyAg] ?? null;
                              @endphp
                              <div class="relative w-8 h-8 shrink-0 rounded-full border-2 border-white shadow-md overflow-hidden bg-surface-container-high">
                                 @if (filled($agFoto))
                                    <img src="{{ $agFoto }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" width="32" height="32" decoding="async" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
                                    <div class="hidden absolute inset-0 flex items-center justify-center text-[10px] font-bold {{ $avatarBgs[$pi] ?? 'bg-surface-container-high' }}">{{ $ag->initials() }}</div>
                                 @else
                                    <div class="flex h-full w-full items-center justify-center text-[10px] font-bold {{ $avatarBgs[$pi] ?? 'bg-surface-container-high' }}">{{ $ag->initials() }}</div>
                                 @endif
                              </div>
                           @endforeach
                           @if ($extraAnggotaCount > 0)
                              <div class="w-8 h-8 rounded-full bg-surface-container-high text-[10px] flex items-center justify-center font-bold border-2 border-white shadow-md text-on-surface-variant">+{{ $extraAnggotaCount }}</div>
                           @endif
                        </div>
                        @php $firstAg = $anggotaList->first(); @endphp
                        @if ($firstAg)
                           <div class="font-bold mt-2 text-xs">{{ $firstAg->sid }} | {{ $firstAg->nama ?: '—' }}</div>
                           @if ($anggotaList->count() > 1)
                              <div class="text-[10px] text-on-surface-variant font-medium">+{{ $anggotaList->count() - 1 }} anggota lainnya</div>
                           @endif
                        @endif
                     @endif
                  </td>
                  <td class="px-8 py-5">
                     <div class="flex flex-col gap-2">
                        <a href="{{ route('peer-pressure-edukasi.sbs.edit', $k->id) }}" class="inline-flex items-center justify-center gap-1 rounded-lg border border-primary/30 bg-primary/5 px-3 py-1.5 text-[11px] font-bold text-primary hover:bg-primary/10">Edit</a>
                        <form action="{{ route('peer-pressure-edukasi.sbs.destroy', $k->id) }}" method="post" onsubmit="return confirm('Hapus kelompok #{{ $k->id }} beserta anggota?');">
                           @csrf
                           @method('DELETE')
                           <button type="submit" class="w-full inline-flex items-center justify-center gap-1 rounded-lg border border-error/30 bg-red-50 px-3 py-1.5 text-[11px] font-bold text-error hover:bg-red-100">Hapus</button>
                        </form>
                     </div>
                  </td>
               </tr>
            @empty
               <tr>
                  <td colspan="7" class="px-8 py-10 text-center text-sm text-on-surface-variant font-medium">
                     @if(filled($q ?? null))Tidak ada hasil untuk pencarian ini.@else Belum ada kelompok SBS.@endif
                  </td>
               </tr>
            @endforelse
         </tbody>
      </table>
   </div>
   <div class="p-6 bg-[#f8fafc] flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 border-t border-outline-variant/20">
      <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">
         @if ($kelompok->total() === 0)
            Menampilkan 0 entri
         @else
            Menampilkan {{ $kelompok->firstItem() }}–{{ $kelompok->lastItem() }} dari {{ number_format($kelompok->total()) }} entri
         @endif
      </p>
      <div class="flex gap-2">
         @if ($kelompok->onFirstPage())
            <button type="button" class="p-2 border border-outline-variant/30 rounded-lg opacity-40 cursor-not-allowed" disabled aria-disabled="true"><span class="material-symbols-outlined text-sm">chevron_left</span></button>
         @else
            <a href="{{ $kelompok->previousPageUrl() }}" class="p-2 border border-outline-variant/30 rounded-lg hover:bg-white hover:shadow-md transition-all inline-flex" rel="prev"><span class="material-symbols-outlined text-sm">chevron_left</span></a>
         @endif
         @if (! $kelompok->hasMorePages())
            <button type="button" class="p-2 border border-outline-variant/30 rounded-lg opacity-40 cursor-not-allowed" disabled aria-disabled="true"><span class="material-symbols-outlined text-sm">chevron_right</span></button>
         @else
            <a href="{{ $kelompok->nextPageUrl() }}" class="p-2 border border-outline-variant/30 rounded-lg hover:bg-white hover:shadow-md transition-all inline-flex" rel="next"><span class="material-symbols-outlined text-sm">chevron_right</span></a>
         @endif
      </div>
   </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
  var modal = document.getElementById('sbs-import-modal');
  var openBtn = document.getElementById('sbs-open-import-modal');
  function openModal() {
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }
  function closeModal() {
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }
  if (openBtn) openBtn.addEventListener('click', openModal);
  if (modal) {
    modal.querySelectorAll('[data-sbs-close-modal]').forEach(function (el) {
      el.addEventListener('click', closeModal);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
    });
  }
  @if ($openImportModal)
  openModal();
  @endif
})();
document.addEventListener('DOMContentLoaded', function () {
  @if (session('notify_success'))
  Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: @json(session('notify_success')),
    confirmButtonColor: '#3952bc'
  });
  @endif
  @if (session('notify_error'))
  Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: @json(session('notify_error')),
    confirmButtonColor: '#3952bc'
  });
  @endif
});
</script>
@endsection
