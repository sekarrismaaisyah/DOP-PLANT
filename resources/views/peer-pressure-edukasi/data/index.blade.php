@extends('peer-pressure-edukasi.layouts.peer-app')

@section('title', 'Data Peer Pressure')

@section('content')
@php $peerFotoUrls = $peerFotoUrls ?? []; @endphp
<div class="bg-white rounded-2xl anchored-card overflow-hidden">
   <div class="p-6 border-b border-outline-variant/20 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
      <div>
         <h2 class="font-headline font-bold text-xl text-on-surface">Data Peer Pressure</h2>
         <p class="text-xs text-on-surface-variant font-medium">Kelola kejadian temuan &amp; edukasi beserta daftar pelanggar dan peer.</p>
      </div>
      <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
         <form method="get" action="{{ route('peer-pressure-edukasi.data.index') }}" class="flex w-full min-w-0 flex-1 flex-col gap-2 sm:max-w-md sm:flex-row sm:items-center">
            <div class="relative min-w-0 flex-1">
               <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 z-0 -translate-y-1/2 text-lg text-on-surface-variant">search</span>
               <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari lokasi, kategori, dept, SID, nama, leader, status…" autocomplete="off" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] py-2 pl-10 pr-3 text-sm text-on-surface outline-none transition-shadow placeholder:text-on-surface-variant/60 focus:border-primary/40 focus:ring-2 focus:ring-primary/15" aria-label="Cari data kejadian">
            </div>
            <div class="flex shrink-0 gap-2">
               <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm transition-colors hover:bg-surface-container-high">Cari</button>
               @if(filled($q ?? null))
               <a href="{{ route('peer-pressure-edukasi.data.index') }}" class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-bold text-on-surface-variant hover:bg-[#f1f5f9]">Reset</a>
               @endif
            </div>
         </form>
         <div class="flex flex-wrap gap-2">
            <a href="{{ route('peer-pressure-edukasi.data.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md transition-transform active:scale-95">
               <span class="material-symbols-outlined text-base">add</span> Tambah data
            </a>
            <a href="{{ route('peer-pressure-edukasi.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">upload_file</span> Import Excel
            </a>
         </div>
      </div>
   </div>
   <div class="overflow-x-auto">
      <table class="w-full text-sm text-left">
         <thead class="bg-[#f8fafc] text-on-surface-variant font-bold text-[10px] uppercase tracking-[0.15em] border-b border-outline-variant/20">
            <tr>
               <th class="px-8 py-5">Incident Detail</th>
               <th class="px-8 py-5">Pelanggar &amp; Dept</th>
               <th class="px-8 py-5">Peer Group</th>
               <th class="px-8 py-5">Duration</th>
               <th class="px-8 py-5">Evidence</th>
               <th class="px-8 py-5">Status</th>
               <th class="px-8 py-5 w-36">Aksi</th>
            </tr>
         </thead>
         <tbody class="divide-y divide-outline-variant/10">
            @forelse ($kejadian as $k)
               @php
                  $pelanggarList = $k->peserta->where('peran', 'pelanggar')->values();
                  $peers = $k->peserta->where('peran', 'peer')->values();
                  $visiblePelanggar = $pelanggarList->take(3);
                  $extraPelanggarCount = max(0, $pelanggarList->count() - 3);
                  $visiblePeers = $peers->take(3);
                  $extraPeerCount = max(0, $peers->count() - 3);
                  $avatarBgs = ['bg-secondary-fixed', 'bg-primary-fixed', 'bg-tertiary-fixed'];
                  $statusBadge = $k->dashboardStatusBadge();
               @endphp
               <tr class="hover:bg-[#f8fafc] transition-colors align-top">
                  <td class="px-8 py-5">
                     <div class="font-bold text-on-surface">{{ $k->formattedTemuanDatetime() }}</div>
                     <div class="text-[10px] text-on-surface-variant flex items-center gap-1 mt-0.5">
                        <span class="material-symbols-outlined text-[12px]" data-icon="location_on">location_on</span> {{ $k->lokasi_temuan }}
                     </div>
                     <span class="mt-2 inline-block bg-primary-container/20 text-primary text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider border border-primary/10">{{ $k->kategori_deviasi }}</span>
                  </td>
                  <td class="px-8 py-5">
                     @if ($pelanggarList->isEmpty())
                        <div class="font-bold text-on-surface-variant">—</div>
                     @else
                        <div class="flex -space-x-2">
                           @foreach ($visiblePelanggar as $pg)
                              @php
                                 $pi = $loop->index;
                                 $sidKeyPg = \Illuminate\Support\Str::lower(trim((string) $pg->sid));
                                 $pelanggarFoto = $peerFotoUrls[$sidKeyPg] ?? null;
                              @endphp
                              <div class="relative w-8 h-8 shrink-0 rounded-full border-2 border-white shadow-md overflow-hidden bg-surface-container-high">
                                 @if (filled($pelanggarFoto))
                                    <img src="{{ $pelanggarFoto }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" width="32" height="32" decoding="async" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
                                    <div class="hidden absolute inset-0 flex items-center justify-center text-[10px] font-bold {{ $avatarBgs[$pi] ?? 'bg-surface-container-high' }}">{{ $pg->initials() }}</div>
                                 @else
                                    <div class="flex h-full w-full items-center justify-center text-[10px] font-bold {{ $avatarBgs[$pi] ?? 'bg-surface-container-high' }}">{{ $pg->initials() }}</div>
                                 @endif
                              </div>
                           @endforeach
                           @if ($extraPelanggarCount > 0)
                              <div class="w-8 h-8 rounded-full bg-surface-container-high text-[10px] flex items-center justify-center font-bold border-2 border-white shadow-md text-on-surface-variant">+{{ $extraPelanggarCount }}</div>
                           @endif
                        </div>
                        @php $firstPg = $pelanggarList->first(); @endphp
                        <div class="font-bold mt-2">{{ $firstPg->sid }} | {{ $firstPg->nama ?: '—' }}</div>
                        @if ($pelanggarList->count() > 1)
                           <div class="text-[10px] text-on-surface-variant font-medium">+{{ $pelanggarList->count() - 1 }} pelanggar lainnya</div>
                        @endif
                     @endif
                     <div class="text-xs text-on-surface-variant mt-1">{{ $k->departemen ?: '—' }} / {{ $k->aktivitas_pekerjaan ?: '—' }}</div>
                  </td>
                  <td class="px-8 py-5">
                     @if ($peers->isEmpty())
                        <span class="text-on-surface-variant text-xs">—</span>
                     @else
                        <div class="flex -space-x-2">
                           @foreach ($visiblePeers as $peer)
                              @php
                                 $pi = $loop->index;
                                 $sidKey = \Illuminate\Support\Str::lower(trim((string) $peer->sid));
                                 $peerFoto = $peerFotoUrls[$sidKey] ?? null;
                              @endphp
                              <div class="relative w-8 h-8 shrink-0 rounded-full border-2 border-white shadow-md overflow-hidden bg-surface-container-high">
                                 @if (filled($peerFoto))
                                    <img src="{{ $peerFoto }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" width="32" height="32" decoding="async" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
                                    <div class="hidden absolute inset-0 flex items-center justify-center text-[10px] font-bold {{ $avatarBgs[$pi] ?? 'bg-surface-container-high' }}">{{ $peer->initials() }}</div>
                                 @else
                                    <div class="flex h-full w-full items-center justify-center text-[10px] font-bold {{ $avatarBgs[$pi] ?? 'bg-surface-container-high' }}">{{ $peer->initials() }}</div>
                                 @endif
                              </div>
                           @endforeach
                           @if ($extraPeerCount > 0)
                              <div class="w-8 h-8 rounded-full bg-surface-container-high text-[10px] flex items-center justify-center font-bold border-2 border-white shadow-md text-on-surface-variant">+{{ $extraPeerCount }}</div>
                           @endif
                        </div>
                     @endif
                     <div class="text-[10px] mt-2 font-bold text-on-surface-variant">Leader: {{ $k->pemimpin_edukasi ?: '—' }}</div>
                  </td>
                  <td class="px-8 py-5 font-bold text-xs text-on-surface whitespace-nowrap">{{ $k->durasi_edukasi_menit }}m</td>
                  <td class="px-8 py-5">
                     @if (filled($k->evidence_url))
                        <a href="{{ $k->evidence_url }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline flex items-center gap-1 text-xs font-bold transition-all relative z-10">
                           <span class="material-symbols-outlined text-lg" data-icon="attach_file">attach_file</span> View Records
                        </a>
                     @else
                        <div class="text-error font-bold text-xs flex items-center gap-1">
                           <span class="material-symbols-outlined text-lg" data-icon="warning">warning</span> Missing Evidence
                        </div>
                     @endif
                  </td>
                  <td class="px-8 py-5">
                     <span class="{{ $statusBadge['spanClass'] }}">{{ $statusBadge['label'] }}</span>
                  </td>
                  <td class="px-8 py-5">
                     <div class="flex flex-col gap-2">
                        <a href="{{ route('peer-pressure-edukasi.data.edit', $k->id) }}" class="inline-flex items-center justify-center gap-1 rounded-lg border border-primary/30 bg-primary/5 px-3 py-1.5 text-[11px] font-bold text-primary hover:bg-primary/10">Edit</a>
                        <form action="{{ route('peer-pressure-edukasi.data.destroy', $k->id) }}" method="post" onsubmit="return confirm('Hapus kejadian #{{ $k->id }} beserta peserta?');">
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
                     @if(filled($q ?? null))Tidak ada hasil untuk pencarian ini.@else Belum ada data kejadian.@endif
                  </td>
               </tr>
            @endforelse
         </tbody>
      </table>
   </div>
   <div class="p-6 bg-[#f8fafc] flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 border-t border-outline-variant/20">
      <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">
         @if ($kejadian->total() === 0)
            Menampilkan 0 entri
         @else
            Menampilkan {{ $kejadian->firstItem() }}–{{ $kejadian->lastItem() }} dari {{ number_format($kejadian->total()) }} entri
         @endif
      </p>
      <div class="flex gap-2">
         @if ($kejadian->onFirstPage())
            <button type="button" class="p-2 border border-outline-variant/30 rounded-lg opacity-40 cursor-not-allowed" disabled aria-disabled="true"><span class="material-symbols-outlined text-sm">chevron_left</span></button>
         @else
            <a href="{{ $kejadian->previousPageUrl() }}" class="p-2 border border-outline-variant/30 rounded-lg hover:bg-white hover:shadow-md transition-all inline-flex" rel="prev"><span class="material-symbols-outlined text-sm">chevron_left</span></a>
         @endif
         @if (! $kejadian->hasMorePages())
            <button type="button" class="p-2 border border-outline-variant/30 rounded-lg opacity-40 cursor-not-allowed" disabled aria-disabled="true"><span class="material-symbols-outlined text-sm">chevron_right</span></button>
         @else
            <a href="{{ $kejadian->nextPageUrl() }}" class="p-2 border border-outline-variant/30 rounded-lg hover:bg-white hover:shadow-md transition-all inline-flex" rel="next"><span class="material-symbols-outlined text-sm">chevron_right</span></a>
         @endif
      </div>
   </div>
</div>
@endsection
