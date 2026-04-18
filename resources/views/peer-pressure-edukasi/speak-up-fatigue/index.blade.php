@extends('peer-pressure-edukasi.layouts.peer-app')

@section('title', 'Speak Up Fatigue')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@section('content')
@php
   $peerFotoUrls = $peerFotoUrls ?? [];
   $openImportModal = request('modal') === 'import' || $errors->any();
@endphp

{{-- Modal Import Excel --}}
<div id="suf-import-modal" class="fixed inset-0 z-[100] {{ $openImportModal ? 'flex' : 'hidden' }} items-center justify-center p-4" aria-modal="true" role="dialog" aria-labelledby="suf-import-modal-title" aria-hidden="{{ $openImportModal ? 'false' : 'true' }}">
   <div class="absolute inset-0 z-0 bg-black/40 backdrop-blur-[1px]" data-suf-close-modal tabindex="-1" aria-hidden="true"></div>
   <div class="relative z-10 w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-card-heavy border border-outline-variant/20">
      <div class="sticky top-0 flex items-start justify-between gap-3 border-b border-outline-variant/15 bg-white px-5 py-4 rounded-t-2xl">
         <div>
            <h3 id="suf-import-modal-title" class="font-headline font-bold text-lg text-on-surface">Import Excel — Speak Up Fatigue</h3>
            <p class="text-[11px] text-on-surface-variant mt-1 leading-relaxed">Satu sheet pertama; tiap baris satu record. Header baris pertama jangan diubah.</p>
         </div>
         <button type="button" class="shrink-0 rounded-lg p-1.5 text-on-surface-variant hover:bg-[#f1f5f9] hover:text-on-surface" data-suf-close-modal aria-label="Tutup">
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
            <p class="font-bold text-on-surface mb-1.5">Kolom</p>
            <p>Site, Perusahaan, SID, Nama, Tanggal, Waktu — sama dengan template.</p>
         </div>
         <div class="flex flex-wrap gap-2">
            <a href="{{ route('peer-pressure-edukasi.speak-up-fatigue.template') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-[11px] font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">download</span> Unduh template
            </a>
         </div>
         <form method="post" action="{{ route('peer-pressure-edukasi.speak-up-fatigue.import') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
               <label class="block text-[11px] font-bold text-on-surface-variant mb-1.5">File Excel (.xlsx / .xls, maks. 10 MB)</label>
               <input type="file" name="excel_file" accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" required class="block w-full text-xs text-on-surface file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-2 file:font-bold file:text-primary">
            </div>
            <div class="flex flex-wrap justify-end gap-2 pt-1">
               <button type="button" class="inline-flex items-center justify-center rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold text-on-surface hover:bg-[#f8fafc]" data-suf-close-modal>Batal</button>
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
         <h2 class="font-headline font-bold text-xl text-on-surface">Speak Up Fatigue</h2>
         <p class="text-xs text-on-surface-variant font-medium">Data site, perusahaan, SID, nama, tanggal &amp; waktu.</p>
      </div>
      <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
         <form method="get" action="{{ route('peer-pressure-edukasi.speak-up-fatigue.index') }}" class="flex w-full min-w-0 flex-1 flex-col gap-2 sm:max-w-md sm:flex-row sm:items-center">
            <div class="relative min-w-0 flex-1">
               <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 z-0 -translate-y-1/2 text-lg text-on-surface-variant">search</span>
               <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari site, perusahaan, SID, nama…" autocomplete="off" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] py-2 pl-10 pr-3 text-sm text-on-surface outline-none transition-shadow placeholder:text-on-surface-variant/60 focus:border-primary/40 focus:ring-2 focus:ring-primary/15" aria-label="Cari data">
            </div>
            <div class="flex shrink-0 gap-2">
               <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm transition-colors hover:bg-surface-container-high">Cari</button>
               @if(filled($q ?? null))
               <a href="{{ route('peer-pressure-edukasi.speak-up-fatigue.index') }}" class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-bold text-on-surface-variant hover:bg-[#f1f5f9]">Reset</a>
               @endif
            </div>
         </form>
         <div class="flex flex-wrap gap-2">
            <a href="{{ route('peer-pressure-edukasi.speak-up-fatigue.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md transition-transform active:scale-95">
               <span class="material-symbols-outlined text-base">add</span> Tambah data
            </a>
            <a href="{{ route('peer-pressure-edukasi.speak-up-fatigue.template') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">download</span> Template Excel
            </a>
            <button type="button" id="suf-open-import-modal" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
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
               <th class="px-8 py-5">Person</th>
               <th class="px-8 py-5">Tanggal &amp; Waktu</th>
               <th class="px-8 py-5 w-36">Aksi</th>
            </tr>
         </thead>
         <tbody class="divide-y divide-outline-variant/10">
            @forelse ($rows as $r)
               @php
                  $sidKey = \Illuminate\Support\Str::lower(trim((string) $r->sid));
                  $foto = $peerFotoUrls[$sidKey] ?? null;
                  $avatarBgs = ['bg-secondary-fixed', 'bg-primary-fixed', 'bg-tertiary-fixed'];
                  $waktuStr = $r->waktu;
                  if ($waktuStr instanceof \Carbon\Carbon) {
                      $waktuTampil = $waktuStr->format('H:i');
                  } else {
                      $ws = (string) $waktuStr;
                      $waktuTampil = strlen($ws) >= 8 ? substr($ws, 0, 5) : $ws;
                  }
               @endphp
               <tr class="hover:bg-[#f8fafc] transition-colors align-top">
                  <td class="px-8 py-5 font-bold text-on-surface">{{ filled($r->site) ? $r->site : '—' }}</td>
                  <td class="px-8 py-5 text-xs">{{ filled($r->perusahaan) ? $r->perusahaan : '—' }}</td>
                  <td class="px-8 py-5">
                     <div class="flex items-start gap-3">
                        <div class="relative w-9 h-9 shrink-0 rounded-full border-2 border-white shadow-md overflow-hidden bg-surface-container-high">
                           @if (filled($foto))
                              <img src="{{ $foto }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" width="36" height="36" decoding="async" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
                              <div class="hidden absolute inset-0 flex items-center justify-center text-[10px] font-bold {{ $avatarBgs[0] }}">{{ $r->initials() }}</div>
                           @else
                              <div class="flex h-full w-full items-center justify-center text-[10px] font-bold {{ $avatarBgs[0] }}">{{ $r->initials() }}</div>
                           @endif
                        </div>
                        <div>
                           <div class="font-bold text-on-surface text-xs">{{ $r->sid }}</div>
                           <div class="text-[11px] text-on-surface-variant">{{ $r->nama }}</div>
                        </div>
                     </div>
                  </td>
                  <td class="px-8 py-5 whitespace-nowrap">
                     <div class="font-bold text-xs text-on-surface">{{ $r->tanggal->format('d M Y') }}</div>
                     <div class="text-[10px] text-on-surface-variant font-medium mt-0.5">{{ $waktuTampil }}</div>
                  </td>
                  <td class="px-8 py-5">
                     <div class="flex flex-col gap-2">
                        <a href="{{ route('peer-pressure-edukasi.speak-up-fatigue.edit', $r->id) }}" class="inline-flex items-center justify-center gap-1 rounded-lg border border-primary/30 bg-primary/5 px-3 py-1.5 text-[11px] font-bold text-primary hover:bg-primary/10">Edit</a>
                        <form action="{{ route('peer-pressure-edukasi.speak-up-fatigue.destroy', $r->id) }}" method="post" onsubmit="return confirm('Hapus data #{{ $r->id }}?');">
                           @csrf
                           @method('DELETE')
                           <button type="submit" class="w-full inline-flex items-center justify-center gap-1 rounded-lg border border-error/30 bg-red-50 px-3 py-1.5 text-[11px] font-bold text-error hover:bg-red-100">Hapus</button>
                        </form>
                     </div>
                  </td>
               </tr>
            @empty
               <tr>
                  <td colspan="5" class="px-8 py-10 text-center text-sm text-on-surface-variant font-medium">
                     @if(filled($q ?? null))Tidak ada hasil untuk pencarian ini.@else Belum ada data Speak Up Fatigue.@endif
                  </td>
               </tr>
            @endforelse
         </tbody>
      </table>
   </div>
   <div class="p-6 bg-[#f8fafc] flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 border-t border-outline-variant/20">
      <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">
         @if ($rows->total() === 0)
            Menampilkan 0 entri
         @else
            Menampilkan {{ $rows->firstItem() }}–{{ $rows->lastItem() }} dari {{ number_format($rows->total()) }} entri
         @endif
      </p>
      <div class="flex gap-2">
         @if ($rows->onFirstPage())
            <button type="button" class="p-2 border border-outline-variant/30 rounded-lg opacity-40 cursor-not-allowed" disabled aria-disabled="true"><span class="material-symbols-outlined text-sm">chevron_left</span></button>
         @else
            <a href="{{ $rows->previousPageUrl() }}" class="p-2 border border-outline-variant/30 rounded-lg hover:bg-white hover:shadow-md transition-all inline-flex" rel="prev"><span class="material-symbols-outlined text-sm">chevron_left</span></a>
         @endif
         @if (! $rows->hasMorePages())
            <button type="button" class="p-2 border border-outline-variant/30 rounded-lg opacity-40 cursor-not-allowed" disabled aria-disabled="true"><span class="material-symbols-outlined text-sm">chevron_right</span></button>
         @else
            <a href="{{ $rows->nextPageUrl() }}" class="p-2 border border-outline-variant/30 rounded-lg hover:bg-white hover:shadow-md transition-all inline-flex" rel="next"><span class="material-symbols-outlined text-sm">chevron_right</span></a>
         @endif
      </div>
   </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
  var modal = document.getElementById('suf-import-modal');
  var openBtn = document.getElementById('suf-open-import-modal');
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
    modal.querySelectorAll('[data-suf-close-modal]').forEach(function (el) {
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
