@extends('peer-pressure-edukasi.layouts.peer-app')

@section('title', 'Data Peer Pressure')

@section('content')
@php
   $peerFotoUrls = $peerFotoUrls ?? [];
   $importLogs = $importLogs ?? collect();
   $openImportModal = request('modal') === 'import' || filled(session('import_errors')) || $errors->any();
   $openHistoryModal = request('modal') === 'history';
   $importErrors = session('import_errors', []);
@endphp
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
            <button type="button" id="pp-data-open-import-modal" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">upload_file</span> Import Excel
            </button>
            <button type="button" id="pp-data-open-history-modal" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">history</span> Riwayat Import
            </button>
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

{{-- Modal Riwayat Import Excel --}}
<div id="pp-data-history-modal" class="fixed inset-0 z-[100] {{ $openHistoryModal ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 p-4" aria-modal="true" role="dialog" aria-labelledby="pp-data-history-modal-title" aria-hidden="{{ $openHistoryModal ? 'false' : 'true' }}">
   <div class="relative flex max-h-[min(90vh,720px)] w-full max-w-4xl flex-col rounded-2xl border border-outline-variant/20 bg-white shadow-2xl">
      <div class="shrink-0 border-b border-outline-variant/15 px-5 py-4">
         <div class="flex items-start justify-between gap-3">
            <div>
               <h3 id="pp-data-history-modal-title" class="font-headline text-lg font-bold text-on-surface">Riwayat Import Excel</h3>
               <p class="mt-1 text-[11px] leading-relaxed text-on-surface-variant">Catatan unggahan Excel oleh pengguna. Status <span class="font-semibold text-emerald-800">Sukses</span> berarti file sesuai ketentuan dan data tersimpan; <span class="font-semibold text-red-800">Gagal</span> menampilkan kolom atau baris yang tidak sesuai.</p>
            </div>
            <button type="button" id="pp-data-close-history-modal" class="shrink-0 rounded-lg p-1.5 text-on-surface-variant hover:bg-[#f1f5f9] hover:text-on-surface" aria-label="Tutup">
               <span class="material-symbols-outlined text-xl">close</span>
            </button>
         </div>
      </div>
      <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
         @if ($importLogs->isEmpty())
            <p class="py-10 text-center text-sm text-on-surface-variant">Belum ada riwayat import. Unggah file Excel melalui tombol <span class="font-semibold text-on-surface">Import Excel</span>.</p>
         @else
            <div class="overflow-x-auto rounded-xl border border-outline-variant/15">
               <table class="w-full min-w-[720px] text-left text-sm">
                  <thead class="border-b border-outline-variant/15 bg-[#f8fafc] text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">
                     <tr>
                        <th class="px-4 py-3 whitespace-nowrap">Waktu</th>
                        <th class="px-4 py-3 whitespace-nowrap">Pengguna</th>
                        <th class="px-4 py-3 whitespace-nowrap">File</th>
                        <th class="px-4 py-3 whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 whitespace-nowrap">Hasil</th>
                        <th class="px-4 py-3 whitespace-nowrap">Detail</th>
                     </tr>
                  </thead>
                  <tbody class="divide-y divide-outline-variant/10">
                     @foreach ($importLogs as $log)
                        @php
                           $logDetailId = 'pp-import-log-detail-' . $log->id;
                           $affectedColumns = $log->affectedColumnLabels();
                           $logErrors = $log->validation_errors ?? [];
                        @endphp
                        <tr class="align-top hover:bg-[#f8fafc]">
                           <td class="px-4 py-3 text-[12px] tabular-nums text-on-surface-variant whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                           <td class="px-4 py-3 text-[12px] font-semibold text-on-surface">{{ $log->user_name }}</td>
                           <td class="px-4 py-3 text-[11px] text-on-surface-variant max-w-[160px] truncate" title="{{ $log->original_filename }}">{{ $log->original_filename ?: '—' }}</td>
                           <td class="px-4 py-3 whitespace-nowrap">
                              @if ($log->isSuccess())
                                 <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-900">Sukses</span>
                              @else
                                 <span class="inline-flex rounded-full bg-red-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-red-900">Gagal</span>
                              @endif
                           </td>
                           <td class="px-4 py-3 text-[11px] text-on-surface">
                              @if ($log->isSuccess())
                                 <span class="font-semibold">{{ number_format($log->imported_kejadian) }}</span> kejadian,
                                 <span class="font-semibold">{{ number_format($log->imported_peserta) }}</span> peserta
                              @else
                                 <span class="text-on-surface-variant">Tidak diimpor</span>
                              @endif
                           </td>
                           <td class="px-4 py-3">
                              @if ($log->isFailed() && ($logErrors !== [] || $affectedColumns !== [] || filled($log->message)))
                                 <button type="button" class="pp-import-log-toggle inline-flex items-center gap-1 rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-[10px] font-bold text-red-900 hover:bg-red-100" data-target="{{ $logDetailId }}" aria-expanded="false">
                                    <span class="material-symbols-outlined text-sm">error</span> Lihat ketidaksesuaian
                                 </button>
                              @elseif ($log->isSuccess() && $logErrors !== [])
                                 <button type="button" class="pp-import-log-toggle inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-900 hover:bg-amber-100" data-target="{{ $logDetailId }}" aria-expanded="false">
                                    <span class="material-symbols-outlined text-sm">info</span> Peringatan
                                 </button>
                              @elseif (filled($log->message))
                                 <span class="text-[11px] leading-snug text-on-surface-variant">{{ \Illuminate\Support\Str::limit($log->message, 120) }}</span>
                              @else
                                 <span class="text-on-surface-variant">—</span>
                              @endif
                           </td>
                        </tr>
                        @if (($log->isFailed() && ($logErrors !== [] || $affectedColumns !== [] || filled($log->message))) || ($log->isSuccess() && $logErrors !== []))
                           <tr id="{{ $logDetailId }}" class="hidden bg-[#fafafa]">
                              <td colspan="6" class="px-4 py-3">
                                 @if ($affectedColumns !== [])
                                    <div class="mb-2">
                                       <p class="mb-1 text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">Kolom / bagian tidak sesuai</p>
                                       <div class="flex flex-wrap gap-1.5">
                                          @foreach ($affectedColumns as $colLabel)
                                             <span class="inline-flex rounded-full border border-red-200 bg-white px-2 py-0.5 text-[10px] font-bold text-red-900">{{ $colLabel }}</span>
                                          @endforeach
                                       </div>
                                    </div>
                                 @endif
                                 @if ($logErrors !== [])
                                    <div>
                                       <p class="mb-1 text-[10px] font-bold uppercase tracking-wide text-on-surface-variant">{{ $log->isSuccess() ? 'Peringatan' : 'Detail error' }}</p>
                                       <ul class="max-h-36 list-disc space-y-1 overflow-y-auto pl-4 text-[11px] leading-snug text-on-surface">
                                          @foreach ($logErrors as $err)
                                             <li>{{ $err }}</li>
                                          @endforeach
                                       </ul>
                                    </div>
                                 @elseif (filled($log->message))
                                    <p class="text-[11px] leading-snug text-red-800">{{ $log->message }}</p>
                                 @endif
                              </td>
                           </tr>
                        @endif
                     @endforeach
                  </tbody>
               </table>
            </div>
            <p class="mt-3 text-center text-[10px] text-on-surface-variant">Menampilkan {{ $importLogs->count() }} unggahan terakhir.</p>
         @endif
      </div>
   </div>
</div>

{{-- Modal Import Excel — Data Peer Pressure --}}
<div id="pp-data-import-modal" class="fixed inset-0 z-[100] {{ $openImportModal ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 p-4" aria-modal="true" role="dialog" aria-labelledby="pp-data-import-modal-title" aria-hidden="{{ $openImportModal ? 'false' : 'true' }}">
   <div class="relative w-full max-w-lg rounded-2xl border border-outline-variant/20 bg-white shadow-2xl">
      <div class="border-b border-outline-variant/15 px-5 py-4">
         <h3 id="pp-data-import-modal-title" class="font-headline text-lg font-bold text-on-surface">Import Excel — Data Peer Pressure</h3>
         <p class="mt-1 text-[11px] leading-relaxed text-on-surface-variant">Sheet pertama. Baris 1 berisi petunjuk; <strong>header kolom di baris 2</strong> harus sama persis dengan template unduhan. Arahkan kursor ke tanda segitiga merah pada header untuk petunjuk tiap kolom. Tanggal isi sebagai <strong>DD/MM/YYYY</strong> atau tanggal Excel; jam <strong>HH:MM</strong> (24 jam).</p>
      </div>
      <div class="max-h-[min(70vh,520px)] overflow-y-auto px-5 py-4 space-y-4">
         <div class="flex flex-wrap gap-2">
            <a href="{{ route('peer-pressure-edukasi.data.template') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-[11px] font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">download</span> Unduh template
            </a>
         </div>
         @if ($importErrors !== [])
            <div class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-[11px] font-medium text-red-900">
               <p class="font-bold mb-1">Perbaiki isi file:</p>
               <ul class="list-disc space-y-1 pl-4 max-h-40 overflow-y-auto">
                  @foreach ($importErrors as $err)
                     <li>{{ $err }}</li>
                  @endforeach
               </ul>
            </div>
         @endif
         <form method="post" action="{{ route('peer-pressure-edukasi.data.import') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
               <label for="pp-data-excel-file" class="mb-1.5 block text-[11px] font-bold text-on-surface-variant">File Excel (.xlsx / .xls, maks. 15 MB)</label>
               <input id="pp-data-excel-file" type="file" name="excel_file" accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" required class="@error('excel_file') border-red-400 @enderror block w-full text-xs text-on-surface file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-2 file:font-bold file:text-primary">
               @error('excel_file')
                  <p class="mt-1 text-[11px] font-semibold text-error">{{ $message }}</p>
               @enderror
            </div>
            <div class="flex flex-wrap justify-end gap-2 pt-1">
               <button type="button" id="pp-data-close-import-modal" class="rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold text-on-surface shadow-sm hover:bg-surface-container-high">Batal</button>
               <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:opacity-95">
                  <span class="material-symbols-outlined text-base">upload_file</span> Unggah &amp; validasi
               </button>
            </div>
         </form>
      </div>
   </div>
</div>

<script>
(function () {
   var importModal = document.getElementById('pp-data-import-modal');
   var openImportBtn = document.getElementById('pp-data-open-import-modal');
   var closeImportBtn = document.getElementById('pp-data-close-import-modal');
   if (importModal && openImportBtn) {
      function openImportM() { importModal.classList.remove('hidden'); importModal.classList.add('flex'); importModal.setAttribute('aria-hidden', 'false'); }
      function closeImportM() { importModal.classList.add('hidden'); importModal.classList.remove('flex'); importModal.setAttribute('aria-hidden', 'true'); }
      openImportBtn.addEventListener('click', openImportM);
      if (closeImportBtn) closeImportBtn.addEventListener('click', closeImportM);
      importModal.addEventListener('click', function (e) { if (e.target === importModal) closeImportM(); });
      @if ($openImportModal)
      openImportM();
      @endif
   }

   var historyModal = document.getElementById('pp-data-history-modal');
   var openHistoryBtn = document.getElementById('pp-data-open-history-modal');
   var closeHistoryBtn = document.getElementById('pp-data-close-history-modal');
   if (historyModal && openHistoryBtn) {
      function openHistoryM() { historyModal.classList.remove('hidden'); historyModal.classList.add('flex'); historyModal.setAttribute('aria-hidden', 'false'); }
      function closeHistoryM() { historyModal.classList.add('hidden'); historyModal.classList.remove('flex'); historyModal.setAttribute('aria-hidden', 'true'); }
      openHistoryBtn.addEventListener('click', openHistoryM);
      if (closeHistoryBtn) closeHistoryBtn.addEventListener('click', closeHistoryM);
      historyModal.addEventListener('click', function (e) { if (e.target === historyModal) closeHistoryM(); });
      @if ($openHistoryModal)
      openHistoryM();
      @endif
   }

   document.querySelectorAll('.pp-import-log-toggle').forEach(function (btn) {
      btn.addEventListener('click', function () {
         var targetId = btn.getAttribute('data-target');
         if (!targetId) return;
         var row = document.getElementById(targetId);
         if (!row) return;
         var isHidden = row.classList.contains('hidden');
         row.classList.toggle('hidden', !isHidden);
         btn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
      });
   });
})();
</script>
@endsection
