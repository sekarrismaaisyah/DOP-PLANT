<!DOCTYPE html>
<html class="light" lang="en">
   <head>
      <meta charset="utf-8"/>
      <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
      <title>Summary Detail Lokasi</title>
      <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
      <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
      <script id="tailwind-config">
         tailwind.config = {
             darkMode: "class",
             theme: {
                 extend: {
                     colors: {
                         "primary": "#0df259",
                         "sage-green": "#8BA88E",
                         "muted-coral": "#E57373",
                         "soft-amber": "#FFB74D",
                         "background-light": "#f9fafb",
                         "background-dark": "#0f172a",
                     },
                     fontFamily: {
                         "display": ["Inter"]
                     },
                     borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                 },
             },
         }
      </script>
      <style type="text/tailwindcss">
         @layer base {
         body { @apply bg-background-light font-display text-slate-900; }
         }
         .thin-divider { @apply border-t border-slate-100 dark:border-slate-800; }
         .data-label { @apply text-[10px] font-bold uppercase tracking-wider text-slate-400; }
         .data-value { @apply text-sm font-semibold text-slate-700; }
         .stat-card { @apply bg-white rounded-lg border border-slate-200 p-4 shadow-sm flex items-center gap-4 transition-all hover:shadow-md; }
      </style>
   </head>
   <body class="bg-background-light dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen">
      <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
         <header class="sticky top-0 z-50 w-full border-b border-slate-200 bg-white/90 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/90 px-6 lg:px-12 py-3">
            <div class="mx-auto flex max-w-[1600px] items-center justify-between">
               <div class="flex items-center gap-10">
                  <div class="flex items-center gap-2.5">
                     <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <span class="material-symbols-outlined text-2xl font-bold">analytics</span>
                     </div>
                     <h2 class="text-lg font-bold tracking-tight">Summary Detail Lokasi </h2>
                  </div>
                 
               </div>
               <div class="flex items-center gap-4">
                  <div class="relative hidden sm:block">
                     <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                     <input class="h-9 w-64 rounded border-slate-200 bg-slate-50 pl-9 text-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="Quick find ID/Permit..." type="text"/>
                  </div>
                  <div class="h-9 w-9 rounded-full bg-slate-200 overflow-hidden border border-slate-200">
                     <img alt="User" class="h-full w-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCaiC29KzHvXxP2HsCwtyo0T81G-NydBpFFXHqPStut62Dn268gr3aAfAtbHfV2r_SOs_eR5MpdjVvVGdw2yYbhxQQq2hh5q-oYm5turip7dIkoDgvjjWTnXW5ZDhCEgegTnUYAvJzVOw7HRnjfkvH0QjGx8X2dZoDnHpVT4rhBbpr8fjs2LMPY6_jmtzEc9ONUnkPhhf9Zq248NEcgl6Ukyo4Vwjf7J8WqFxV3eblQip-Suu-qF0g8IZZKAvIeUoYVUI6WBibW2W9W"/>
                  </div>
               </div>
            </div>
         </header>
         <main class="mx-auto flex w-full max-w-[1600px] flex-col gap-8 px-6 lg:px-12 py-8 pb-32">
            @php
               $hasFilter = ($filterLokasi !== null && $filterLokasi !== '') || ($filterDetailLokasi !== null && $filterDetailLokasi !== '');
            @endphp
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
               <div>
                  <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">Summary Detail Lokasi {{ $filterLokasi ?: '—' }} / {{ $filterDetailLokasi ?: '—' }}</h1>
                  <p class="text-sm text-slate-500 mt-1">Industrial Operational Overview • Tanggal: <span class="font-mono">{{ $tanggal->format('d-m-Y') }}</span>@if($hasFilter) @endif</p>
               </div>
               <div class="flex gap-2">
                  <button class="flex h-10 items-center justify-center rounded border border-slate-200 bg-white px-4 text-xs font-bold hover:bg-slate-50">
                  <span class="material-symbols-outlined mr-2 text-lg">settings_input_component</span> System Config
                  </button>
                  <button class="flex h-10 items-center justify-center rounded bg-emerald-600 px-6 text-xs font-bold text-white hover:bg-emerald-700 shadow-sm transition-all">
                  Generate Shift Report
                  </button>
               </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="stat-card">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-50 text-muted-coral">
                     <span class="material-symbols-outlined">warning</span>
                  </div>
                  <div>
                     <p class="data-label">Total Insiden YTD</p>
                     <p class="text-xl font-black text-muted-coral">{{ $totalInsiden }}</p>
                  </div>
               </div>   
            
            <div class="stat-card">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                     <span class="material-symbols-outlined">groups</span>
                  </div>
                  <div>
                     <p class="data-label">Total Area/Aktivitas Kritis</p>
                     <p class="text-xl font-black text-slate-800">{{ $aktivitasKritis->count() }}</p>
                  </div>
               </div>

               <div class="stat-card">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-50 text-soft-amber">
                     <span class="material-symbols-outlined">emergency</span>
                  </div>
                  <div>
                     <p class="data-label">Total CCTV</p>
                     <p class="text-xl font-black text-slate-800">{{ $cctvActiveCount }}</p>
                  </div>
               </div>
              
               <div class="stat-card">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-sage-green">
                     <span class="material-symbols-outlined">assignment_turned_in</span>
                  </div>
                  <div>
                     <p class="data-label">Total Hazard Weekly</p>
                     <p class="text-xl font-black text-sage-green">{{ $totalHazardWeekly ?? 0 }}</p>
                  </div>
               </div>
               
               <div class="stat-card">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                     <span class="material-symbols-outlined">timer</span>
                  </div>
                  <div>
                     <p class="data-label">Hazard &amp; Inspeksi Open (SUBMITTED) Minggu Ini</p>
                     <p class="text-xl font-black text-slate-800">-</p>
                  </div>
               </div>
            </div>
            <div class="grid grid-cols-12 gap-6 items-stretch">
               {{-- Baris 1: Insiden YTD (kiri) dan Aktivitas Kritis (kanan) — tinggi disamakan lewat grid --}}
               <div class="col-span-12 lg:col-span-4 flex flex-col min-h-0 bg-slate-50 rounded-xl p-4">
                  <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col flex-1 min-h-0">
                     <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-4 py-3 shrink-0">
                        <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest">Insiden YTD di {{ $filterLokasi ?: '—' }}</h3>
                        <span class="text-[10px] font-bold text-slate-400 tracking-tighter uppercase">MTD Metrics</span>
                     </div>
                     <div class="p-4 flex-1 min-h-0 overflow-auto">
                        <div class="grid grid-cols-2 gap-4 mb-6">
                           <div class="rounded border border-slate-100 bg-slate-50 p-3 text-center">
                              <p class="data-label">Total Insiden</p>
                              <p class="text-2xl font-black text-slate-800">{{ $totalInsiden }}</p>
                           </div>
                           <div class="rounded border border-slate-100 bg-slate-50 p-3 text-center">
                              <p class="data-label">Last Event</p>
                              <p class="text-sm font-black text-slate-800">2025</p>
                           </div>
                        </div>
                        
                        <div class="space-y-3">
                           <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b pb-1">Detail Insiden</p>
                           @forelse($recentInsiden as $index => $insiden)
                              <div class="flex items-center justify-between cursor-pointer hover:bg-slate-50 rounded px-2 py-1 -mx-2 transition-colors js-insiden-row" role="button" tabindex="0" data-index="{{ $index }}" data-no="{{ e($insiden->no_kecelakaan) }}" onclick="openInsidenModal(this)" onkeydown="if(event.key==='Enter')openInsidenModal(this)">
                                 <div class="min-w-0">
                                    <p class="text-xs font-bold truncate">{{ $insiden->no_kecelakaan }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $insiden->lokasi ?: '—' }}@if($insiden->sublokasi) • {{ $insiden->sublokasi }}@endif</p>
                                 </div>
                                 @php
                                    $isClosed = strtoupper($insiden->status_lpi ?? '') === 'CLOSED';
                                 @endphp
                                 <span class="rounded px-2 py-0.5 text-[10px] font-bold border shrink-0 {{ $isClosed ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-orange-50 text-orange-600 border-orange-100' }}">{{ $insiden->kategori ?: ($insiden->status_lpi ?: '—') }}</span>
                              </div>
                           @empty
                              <p class="text-xs text-slate-500 py-2">Tidak ada data insiden untuk lokasi ini.</p>
                           @endforelse
                        </div>
                     </div>
                  </div>
                  {{-- Modal Detail Insiden (muncul saat klik baris Detail Insiden) --}}
                  @if($recentInsiden->isNotEmpty())
                  <div id="modalDetailInsiden" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
                     <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeInsidenModal()"></div>
                     <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
                        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col pointer-events-auto" onclick="event.stopPropagation()">
                           <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 shrink-0">
                              <h3 id="modalDetailInsidenTitle" class="text-sm font-extrabold text-slate-800 uppercase tracking-widest">Detail Insiden</h3>
                              <button type="button" onclick="closeInsidenModal()" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" aria-label="Tutup">
                                 <span class="material-symbols-outlined text-xl">close</span>
                              </button>
                           </div>
                           <div class="overflow-y-auto p-5">
                              @foreach($recentInsiden as $idx => $ins)
                                 <div id="insiden-detail-{{ $idx }}" class="insiden-detail-panel hidden space-y-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                       <div><p class="data-label">No. Kecelakaan</p><p class="data-value font-mono">{{ $ins->no_kecelakaan ?: '—' }}</p></div>
                                       <div><p class="data-label">Kode BE Investigasi</p><p class="data-value">{{ $ins->kode_be_investigasi ?: '—' }}</p></div>
                                       <div><p class="data-label">Status LPI</p><p class="data-value">{{ $ins->status_lpi ?: '—' }}</p></div>
                                       <div><p class="data-label">Kategori</p><p class="data-value">{{ $ins->kategori ?: '—' }}</p></div>
                                       <div><p class="data-label">Injury Status</p><p class="data-value">{{ $ins->injury_status ?: '—' }}</p></div>
                                       <div><p class="data-label">High Potential</p><p class="data-value">{{ $ins->high_potential ?: '—' }}</p></div>
                                       <div><p class="data-label">Site</p><p class="data-value">{{ $ins->site ?: '—' }}</p></div>
                                       <div><p class="data-label">Lokasi</p><p class="data-value">{{ $ins->lokasi ?: '—' }}</p></div>
                                       <div><p class="data-label">Sub Lokasi</p><p class="data-value">{{ $ins->sublokasi ?: '—' }}</p></div>
                                       <div><p class="data-label">Lokasi Spesifik</p><p class="data-value">{{ $ins->lokasi_spesifik ?: '—' }}</p></div>
                                       <div><p class="data-label">Perusahaan</p><p class="data-value">{{ $ins->perusahaan ?: '—' }}</p></div>
                                       <div><p class="data-label">Departemen</p><p class="data-value">{{ $ins->departemen ?: '—' }}</p></div>
                                       <div><p class="data-label">Shift</p><p class="data-value">{{ $ins->shift ?: '—' }}</p></div>
                                       <div><p class="data-label">Tanggal</p><p class="data-value">{{ $ins->tanggal?->format('d/m/Y') ?? ($ins->tahun && $ins->bulan ? $ins->bulan . '/' . $ins->tahun : '—') }}</p></div>
                                       <div><p class="data-label">Nama (Terlapor)</p><p class="data-value">{{ $ins->nama ?: '—' }}</p></div>
                                       <div><p class="data-label">Jabatan</p><p class="data-value">{{ $ins->jabatan ?: '—' }}</p></div>
                                       <div><p class="data-label">Alat Terlibat</p><p class="data-value">{{ $ins->alat_terlibat ?: '—' }}</p></div>
                                       <div><p class="data-label">Atasan Langsung</p><p class="data-value">{{ $ins->atasan_langsung ?: '—' }}</p></div>
                                       <div><p class="data-label">Loss Cost</p><p class="data-value">{{ $ins->loss_cost !== null && $ins->loss_cost != '' ? number_format($ins->loss_cost, 0, ',', '.') : '—' }}</p></div>
                                    </div>
                                    @if($ins->kronologis)
                                    <div>
                                       <p class="data-label">Kronologis</p>
                                       <p class="text-sm text-slate-700 whitespace-pre-wrap mt-1">{{ $ins->kronologis }}</p>
                                    </div>
                                    @endif
                                    @php $layers = $insidenLayersByNo[$ins->no_kecelakaan] ?? collect(); @endphp
                                    @if($layers->isNotEmpty())
                                    <div>
                                       <p class="data-label mb-2">Layer / Analisis (semua baris untuk no_kecelakaan ini)</p>
                                       <div class="overflow-x-auto rounded-lg border border-slate-200">
                                          <table class="w-full text-left text-xs">
                                             <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500">
                                                <tr>
                                                   <th class="px-3 py-2 font-bold">Layer</th>
                                                   <th class="px-3 py-2 font-bold">Jenis Item IPLS</th>
                                                   <th class="px-3 py-2 font-bold">Detail Layer</th>
                                                   <th class="px-3 py-2 font-bold">Klasifikasi Layer</th>
                                                   <th class="px-3 py-2 font-bold">Keterangan Layer</th>
                                                </tr>
                                             </thead>
                                             <tbody class="divide-y divide-slate-100">
                                                @foreach($layers as $layerRow)
                                                <tr class="hover:bg-slate-50">
                                                   <td class="px-3 py-2 font-medium text-slate-700">{{ $layerRow->layer ?: '—' }}</td>
                                                   <td class="px-3 py-2 text-slate-600">{{ $layerRow->jenis_item_ipls ?: '—' }}</td>
                                                   <td class="px-3 py-2 text-slate-600">{{ $layerRow->detail_layer ?: '—' }}</td>
                                                   <td class="px-3 py-2 text-slate-600">{{ $layerRow->klasifikasi_layer ?: '—' }}</td>
                                                   <td class="px-3 py-2 text-slate-700 whitespace-pre-wrap max-w-xs">{{ $layerRow->keterangan_layer ?: '—' }}</td>
                                                </tr>
                                                @endforeach
                                             </tbody>
                                          </table>
                                       </div>
                                    </div>
                                    @endif
                                 </div>
                              @endforeach
                           </div>
                        </div>
                     </div>
                  </div>
                  @endif
               </div>
               {{-- Kolom kanan: Aktivitas Kritis (sejajar tinggi dengan Insiden YTD) --}}
               <div class="col-span-12 lg:col-span-8 flex flex-col min-h-0">
                  <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col h-full min-h-0">
                     <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-4 py-3 shrink-0">
                        @if($hasFilter)
                           <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest">Aktivitas Kritis Di {{ $filterLokasi ?: '—' }} - {{ $filterDetailLokasi ?: '—' }}</h3>
                           <div class="flex items-center gap-3">
                              @if($aktivitasKritis->count() > 2)
                                 <button type="button" onclick="document.getElementById('modalDetailAktivitas').classList.remove('hidden')" class="text-[10px] font-bold text-slate-600 hover:text-emerald-600 hover:underline uppercase">Lihat Semua Detail ({{ $aktivitasKritis->count() }})</button>
                              @endif
                              <!-- <a href="{{ route('sistem-roster.tasklist.index', ['tanggal' => $tanggal->format('Y-m-d')]) }}" class="text-[10px] font-bold text-emerald-600 hover:underline uppercase">Tampilkan Semua Area</a> -->
                           </div>
                        @else
                           <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest">Area Kritis Hari Ini (Lokasi &amp; Detail Lokasi)</h3>
                        @endif
                     </div>
                     <div class="p-4 flex-1 min-h-0 overflow-auto">
                        @if($hasFilter)
                           {{-- Tampilkan maksimal 2 detail; sisanya di modal --}}
                           @forelse($aktivitasKritis->take(2) as $row)
                              <div class="rounded-lg border border-slate-100 bg-slate-50/50 p-4 mb-3 last:mb-0">
                                 <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                                    <div>
                                       <p class="data-label">Aktivitas</p>
                                       <p class="data-value">{{ $row->aktivitas ?: '—' }}</p>
                                    </div>
                                    @if($row->no_ikk)
                                    <div>
                                       <p class="data-label">No. IKK</p>
                                       <p class="data-value font-mono">{{ $row->no_ikk }}</p>
                                    </div>
                                    @endif
                                    <div>
                                       <p class="data-label">Site</p>
                                       <p class="data-value">{{ $row->site ?: '—' }}</p>
                                    </div>
                                    <div>
                                       <p class="data-label">Lokasi</p>
                                       <p class="data-value">{{ $row->lokasi ?: '—' }}</p>
                                    </div>
                                    <div>
                                       <p class="data-label">Detail Lokasi</p>
                                       <p class="data-value">{{ $row->detail_lokasi ?: '—' }}</p>
                                    </div>
                                    <div>
                                       <p class="data-label">Perusahaan</p>
                                       <p class="data-value">{{ $row->perusahaan_pic ?: '—' }}</p>
                                    </div>
                                    <div class="sm:col-span-2">
                                       <p class="data-label">Pengawas Langsung</p>
                                       <p class="data-value text-xs">{{ $row->pengawas_langsung ?: '—' }}</p>
                                    </div>
                                 </div>
                              </div>
                           @empty
                              <p class="text-sm text-slate-500 py-4">Tidak ada aktivitas untuk lokasi &amp; detail lokasi ini.</p>
                           @endforelse
                           @if($aktivitasKritis->count() > 2)
                              <p class="text-xs text-slate-500 mt-2">Menampilkan 2 dari {{ $aktivitasKritis->count() }}. Klik <strong>Lihat Semua Detail</strong> di atas untuk melihat semua.</p>
                           @endif
                        @else
                           {{-- Tanpa filter: tampilkan daftar area (lokasi + detail_lokasi) sebagai link --}}
                           @if($summaryAreas->isEmpty())
                              <p class="text-sm text-slate-500 py-4">Tidak ada data roster planning untuk tanggal {{ $tanggal->format('d-m-Y') }}.</p>
                           @else
                              <div class="flex flex-wrap gap-2">
                                 @foreach($summaryAreas as $area)
                                    <a href="{{ route('sistem-roster.tasklist.index', ['tanggal' => $tanggal->format('Y-m-d'), 'lokasi' => $area->lokasi, 'detail_lokasi' => $area->detail_lokasi]) }}"
                                       class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-left transition-colors hover:bg-slate-100 text-slate-700">
                                       <span class="font-medium text-xs">{{ $area->lokasi ?: '—' }}</span>
                                       <span class="text-slate-400">/</span>
                                       <span class="text-xs">{{ $area->detail_lokasi ?: '—' }}</span>
                                       <span class="rounded bg-slate-200 px-1.5 py-0.5 text-[10px] font-bold text-slate-600">{{ $area->total_aktivitas }}</span>
                                    </a>
                                 @endforeach
                              </div>
                           @endif
                        @endif
                     </div>
                  </div>
                  {{-- Modal: semua detail aktivitas (muncul ketika klik "Lihat Semua Detail") --}}
                  @if($hasFilter && $aktivitasKritis->isNotEmpty())
                  <div id="modalDetailAktivitas" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
                     <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modalDetailAktivitas').classList.add('hidden')"></div>
                     <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
                        <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] flex flex-col pointer-events-auto" onclick="event.stopPropagation()">
                           <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 shrink-0">
                              <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-widest">Aktivitas Kritis Di {{ $filterLokasi ?: '—' }} - {{ $filterDetailLokasi ?: '—' }}</h3>
                              <button type="button" onclick="document.getElementById('modalDetailAktivitas').classList.add('hidden')" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors" aria-label="Tutup">
                                 <span class="material-symbols-outlined text-xl">close</span>
                              </button>
                           </div>
                           <div class="overflow-y-auto p-5 space-y-4">
                              @foreach($aktivitasKritis as $row)
                                 <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                                       <div>
                                          <p class="data-label">Aktivitas</p>
                                          <p class="data-value">{{ $row->aktivitas ?: '—' }}</p>
                                       </div>
                                       @if($row->no_ikk)
                                       <div>
                                          <p class="data-label">No. IKK</p>
                                          <p class="data-value font-mono">{{ $row->no_ikk }}</p>
                                       </div>
                                       @endif
                                       <div>
                                          <p class="data-label">Site</p>
                                          <p class="data-value">{{ $row->site ?: '—' }}</p>
                                       </div>
                                       <div>
                                          <p class="data-label">Lokasi</p>
                                          <p class="data-value">{{ $row->lokasi ?: '—' }}</p>
                                       </div>
                                       <div>
                                          <p class="data-label">Detail Lokasi</p>
                                          <p class="data-value">{{ $row->detail_lokasi ?: '—' }}</p>
                                       </div>
                                       <div>
                                          <p class="data-label">Perusahaan</p>
                                          <p class="data-value">{{ $row->perusahaan_pic ?: '—' }}</p>
                                       </div>
                                       <div class="sm:col-span-2">
                                          <p class="data-label">Pengawas Langsung</p>
                                          <p class="data-value text-xs">{{ $row->pengawas_langsung ?: '—' }}</p>
                                       </div>
                                    </div>
                                 </div>
                              @endforeach
                           </div>
                        </div>
                     </div>
                  </div>
                  @endif
               </div>
               {{-- Baris 2: CCTV (kiri) + konten kanan (Hazard, dll) --}}
               <div class="col-span-12 lg:col-span-4 flex flex-col gap-6 bg-slate-50 rounded-xl p-4">
                  <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">
                     <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-4 py-3">
                        <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest">CCTV Status</h3>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">{{ $cctvActiveCount }} Active / {{ $cctvOfflineCount }} Offline</span>
                     </div>
                     <div class="divide-y divide-slate-100 pb-4">
                        @forelse($cctvList as $cctv)
                        @php
                           $isActive = strtolower(trim((string)($cctv->kondisi ?? ''))) === 'baik';
                        @endphp
                        <div class="flex items-center gap-3 p-3 hover:bg-slate-50 transition-colors">
                           <div class="relative h-12 w-20 overflow-hidden rounded bg-slate-200 shrink-0">
                              @if($isActive)
                              <img alt="CCTV Snapshot" class="w-full h-full object-cover opacity-80" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCyR_CiOEJphtz0dO0Uqv0J6qYKUNdcqx3g4fMhwQnN4JXq-WefE5kK5DBKpvZ89ytyDLqmtcOfcDbTVr47Pk4MbmBJtbFmEC7RpW3NIqPkZZ4CmUngXmhQy7sGQkj1G_isqc4_6eckuwvj8UGCNdhEcA6iR-wlEbv7uJCrBEYcoAV3rVVMR1NwVcWAg9zrbi9w93EP1eIjT-MJOgEsC55_Y0cGv8IT8s_T3Ray_rx2JoYQW6ZexReSob5Xsj9HZSlnOj1VmZmu_a8t"/>
                              <div class="absolute inset-0 bg-black/10"></div>
                              @else
                              <div class="h-full w-full bg-slate-800 flex items-center justify-center text-slate-500">
                                 <span class="material-symbols-outlined">videocam_off</span>
                              </div>
                              @endif
                           </div>
                           <div class="flex-1 min-w-0">
                              <div class="flex items-center gap-2">
                                 <div class="h-2 w-2 rounded-full shrink-0 {{ $isActive ? 'bg-emerald-500' : 'bg-red-500' }}"></div>
                                 <span class="text-sm font-bold truncate">CCTV {{ $cctv->no_cctv ?: ($cctv->no_cctv ?: '—') }}</span>
                              </div>
                              <p class="text-[10px] {{ $isActive ? 'text-slate-400 font-medium' : 'text-red-400 font-bold uppercase tracking-tight' }}">
                                 @if($isActive)
                                    {{ $cctv->kondisi }} • {{ $cctv->link_akses ?: ($cctv->lokasi_pemasangan ?: '—') }}
                                 @else
                                    {{ $cctv->kondisi ?: 'Offline' }}
                                 @endif
                              </p>
                           </div>
                        </div>
                        @empty
                        <p class="text-xs text-slate-500 py-4 px-3">Tidak ada CCTV untuk lokasi &amp; detail lokasi ini.@if(!$hasFilter) Pilih area di daftar untuk memfilter CCTV.@endif</p>
                        @endforelse
                     </div>
                  </div>
               </div>
               <div class="col-span-12 lg:col-span-8 flex flex-col gap-6">
                  @if($hasFilter && isset($hazardInspeksiWeekly))
                  @php
                     $hazardSubmittedOnly = array_values(array_filter($hazardInspeksiWeekly, function($row) {
                        $r = is_array($row) ? $row : (array) $row;
                        $s = $r['status'] ?? $r['Status'] ?? '';
                        return $s === 'SUBMITTED';
                     }));
                     $hazardDisplay = array_slice($hazardSubmittedOnly, 0, 10);
                     $hazardTotal = count($hazardInspeksiWeekly);
                  @endphp
                  <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">
                     <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-5 py-3">
                        <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest">Hazard &amp; Inspeksi Weekly</h3>
                        <div class="flex items-center gap-3">
                           <!-- <span class="text-[10px] font-bold text-slate-500">Lokasi: {{ $filterLokasi ?: '—' }} @if($filterDetailLokasi) • Detail: {{ $filterDetailLokasi }} @endif</span> -->
                           @if($hazardTotal > 10)
                           <button type="button" onclick="document.getElementById('modalHazardInspeksiAll').classList.remove('hidden')" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-700 hover:underline uppercase">Show All ({{ $hazardTotal }})</button>
                           @endif
                        </div>
                     </div>
                     <div class="overflow-x-auto">
                        <table class="w-full text-left">
                           <thead class="bg-slate-50/50 text-[10px] uppercase tracking-wider text-slate-500">
                              <tr>
                                 <th class="px-5 py-3 font-bold">Jenis</th>
                                 <th class="px-5 py-3 font-bold">Lokasi</th>
                                 <th class="px-5 py-3 font-bold">Detail Lokasi</th>
                                 <th class="px-5 py-3 font-bold">Deskripsi</th>
                                 <th class="px-5 py-3 font-bold">Status</th>
                                 <th class="px-5 py-3 font-bold">Tanggal</th>
                                 <th class="px-5 py-3 font-bold">Pelapor</th>
                                 <!-- <th class="px-5 py-3 font-bold">Nilai Resiko</th> -->
                                 <th class="px-5 py-3 font-bold">Kategori</th>
                              </tr>
                           </thead>
                           <tbody class="divide-y divide-slate-100 text-sm">
                              @forelse($hazardDisplay as $row)
                                 @php
                                    $r = is_array($row) ? $row : (array) $row;
                                    $jenis = $r['jenis_laporan'] ?? $r['Jenis_laporan'] ?? '—';
                                    $lokasi = $r['nama_lokasi'] ?? $r['Nama_lokasi'] ?? '—';
                                    $detailLokasi = $r['nama_detail_lokasi'] ?? $r['Nama_detail_lokasi'] ?? '—';
                                    $deskripsi = $r['deskripsi'] ?? $r['Deskripsi'] ?? '—';
                                    $status = $r['status'] ?? $r['Status'] ?? '—';
                                    $tglPembuatan = $r['tanggal_pembuatan'] ?? $r['Tanggal_pembuatan'] ?? null;
                                    $bedraft = $r['bedraft_date'] ?? $r['Bedraft_date'] ?? null;
                                    $tanggal = $tglPembuatan ?: $bedraft;
                                    $pelapor = $r['nama_pelapor'] ?? $r['Nama_pelapor'] ?? '—';
                                    $nilaiResiko = $r['nilai_resiko'] ?? $r['Nilai_resiko'] ?? '—';
                                    $kategori = $r['nama_kategori'] ?? $r['Nama_kategori'] ?? '—';
                                 @endphp
                                 <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-3">
                                       <span class="rounded-sm {{ $jenis === 'HAZARD' ? 'bg-muted-coral' : 'bg-soft-amber' }} px-1.5 py-0.5 text-[10px] font-black text-white">{{ $jenis }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-xs font-medium text-slate-600">{{ $lokasi }}</td>
                                    <td class="px-5 py-3 text-xs text-slate-700">{{ $detailLokasi }}</td>
                                    <td class="px-5 py-3 text-xs text-slate-900 max-w-[200px] truncate" title="{{ $deskripsi }}">{{ $deskripsi }}</td>
                                    <td class="px-5 py-3 text-xs font-medium">{{ $status }}</td>
                                    <td class="px-5 py-3 text-xs text-slate-600">{{ $tanggal ? \Carbon\Carbon::parse($tanggal)->format('d/m/Y H:i') : '—' }}</td>
                                    <td class="px-5 py-3 text-xs">{{ $pelapor }}</td>
                                    <!-- <td class="px-5 py-3 text-xs">{{ $nilaiResiko }}</td> -->
                                    <td class="px-5 py-3 text-xs">{{ $kategori }}</td>
                                 </tr>
                              @empty
                                 <tr>
                                    <td colspan="9" class="px-5 py-8 text-center text-slate-500 text-sm">Tidak ada data hazard/inpeksi minggu ini untuk lokasi ini.</td>
                                 </tr>
                              @endforelse
                           </tbody>
                        </table>
                     </div>
                  </div>
                  {{-- Modal: Semua Hazard & Inspeksi Weekly --}}
                  @php
                     $modalOpen = 0;
                     $modalClose = 0;
                     $modalBySub = [];
                     foreach ($hazardInspeksiWeekly as $row) {
                        $r = is_array($row) ? $row : (array) $row;
                        $st = trim((string)($r['status'] ?? $r['Status'] ?? ''));
                        $sub = trim((string)($r['subketidaksesuaian'] ?? $r['Subketidaksesuaian'] ?? ''));
                        if ($sub === '') $sub = '(Lainnya)';
                        if ($st === 'SUBMITTED') { $modalOpen++; } else { $modalClose++; }
                        if (!isset($modalBySub[$sub])) $modalBySub[$sub] = ['open' => 0, 'close' => 0];
                        if ($st === 'SUBMITTED') $modalBySub[$sub]['open']++; else $modalBySub[$sub]['close']++;
                     }
                     foreach ($modalBySub as $k => $v) {
                        $modalBySub[$k]['total'] = $v['open'] + $v['close'];
                     }
                     uasort($modalBySub, fn($a, $b) => ($b['total'] ?? 0) <=> ($a['total'] ?? 0));
                  @endphp
                  <div id="modalHazardInspeksiAll" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
                     <div class="flex min-h-full items-center justify-center p-4">
                        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('modalHazardInspeksiAll').classList.add('hidden')"></div>
                        <div class="relative w-full max-w-6xl max-h-[90vh] flex flex-col rounded-xl bg-white shadow-xl">
                           <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                              <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-widest">Semua Hazard &amp; Inspeksi Weekly ({{ $hazardTotal }})</h3>
                              <button type="button" onclick="document.getElementById('modalHazardInspeksiAll').classList.add('hidden')" class="rounded p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600" aria-label="Tutup">
                                 <span class="material-symbols-outlined text-xl">close</span>
                              </button>
                           </div>
                           <div class="overflow-auto flex-1 p-4 space-y-4">
                              {{-- Statistik Open vs Close --}}
                              <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                                 <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 mb-3">Status</h4>
                                 <div class="flex flex-wrap gap-6">
                                    <div class="flex items-center gap-2">
                                       <span class="h-3 w-3 rounded-full bg-amber-500"></span>
                                       <span class="text-sm font-bold text-slate-700">Open (SUBMITTED):</span>
                                       <span class="text-sm font-black text-slate-900">{{ $modalOpen }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                       <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                                       <span class="text-sm font-bold text-slate-700">Close:</span>
                                       <span class="text-sm font-black text-slate-900">{{ $modalClose }}</span>
                                    </div>
                                 </div>
                              </div>
                              {{-- Statistik per Subketidaksesuaian --}}
                              <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4">
                                 <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500 mb-3">Subketidaksesuaian</h4>
                                 <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse text-sm">
                                       <thead>
                                          <tr class="border-b border-slate-200">
                                             <th class="py-2 pr-4 font-bold text-slate-600">Subketidaksesuaian</th>
                                             <th class="py-2 px-4 font-bold text-slate-600 text-right">Total (Hazard &amp; Inspeksi)</th>
                                             <th class="py-2 px-4 font-bold text-slate-600 text-right">Open</th>
                                             <th class="py-2 px-4 font-bold text-slate-600 text-right">Close</th>
                                          </tr>
                                       </thead>
                                       <tbody class="divide-y divide-slate-100">
                                          @foreach($modalBySub as $namaSub => $counts)
                                          <tr>
                                             <td class="py-2 pr-4 font-medium text-slate-800">{{ $namaSub }}</td>
                                             <td class="py-2 px-4 text-right font-bold text-slate-800">{{ $counts['total'] ?? (($counts['open'] ?? 0) + ($counts['close'] ?? 0)) }}</td>
                                             <td class="py-2 px-4 text-right font-semibold text-amber-700">{{ $counts['open'] }}</td>
                                             <td class="py-2 px-4 text-right font-semibold text-emerald-700">{{ $counts['close'] }}</td>
                                          </tr>
                                          @endforeach
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                              {{-- Tabel data --}}
                              <h4 class="text-[10px] font-extrabold uppercase tracking-widest text-slate-500">Detail Data</h4>
                              <table class="w-full text-left border-collapse">
                                 <thead class="bg-slate-50/80 sticky top-0 text-[10px] uppercase tracking-wider text-slate-500">
                                    <tr>
                                       <th class="px-4 py-2 font-bold whitespace-nowrap">Jenis</th>
                                       <th class="px-4 py-2 font-bold whitespace-nowrap">Lokasi</th>
                                       <th class="px-4 py-2 font-bold whitespace-nowrap">Detail Lokasi</th>
                                       <th class="px-4 py-2 font-bold whitespace-nowrap">Deskripsi</th>
                                       <th class="px-4 py-2 font-bold whitespace-nowrap">Status</th>
                                       <th class="px-4 py-2 font-bold whitespace-nowrap">Tanggal</th>
                                       <th class="px-4 py-2 font-bold whitespace-nowrap">Pelapor</th>
                                       <th class="px-4 py-2 font-bold whitespace-nowrap">Nilai Resiko</th>
                                       <th class="px-4 py-2 font-bold whitespace-nowrap">Kategori</th>
                                    </tr>
                                 </thead>
                                 <tbody class="divide-y divide-slate-100 text-sm">
                                    @foreach($hazardInspeksiWeekly as $row)
                                       @php
                                          $r = is_array($row) ? $row : (array) $row;
                                          $jenis = $r['jenis_laporan'] ?? $r['Jenis_laporan'] ?? '—';
                                          $lokasi = $r['nama_lokasi'] ?? $r['Nama_lokasi'] ?? '—';
                                          $detailLokasi = $r['nama_detail_lokasi'] ?? $r['Nama_detail_lokasi'] ?? '—';
                                          $deskripsi = $r['deskripsi'] ?? $r['Deskripsi'] ?? '—';
                                          $status = $r['status'] ?? $r['Status'] ?? '—';
                                          $tglPembuatan = $r['tanggal_pembuatan'] ?? $r['Tanggal_pembuatan'] ?? null;
                                          $bedraft = $r['bedraft_date'] ?? $r['Bedraft_date'] ?? null;
                                          $tanggal = $tglPembuatan ?: $bedraft;
                                          $pelapor = $r['nama_pelapor'] ?? $r['Nama_pelapor'] ?? '—';
                                          $nilaiResiko = $r['nilai_resiko'] ?? $r['Nilai_resiko'] ?? '—';
                                          $kategori = $r['nama_kategori'] ?? $r['Nama_kategori'] ?? '—';
                                       @endphp
                                       <tr class="hover:bg-slate-50">
                                          <td class="px-4 py-2"><span class="rounded-sm {{ $jenis === 'HAZARD' ? 'bg-muted-coral' : 'bg-soft-amber' }} px-1.5 py-0.5 text-[10px] font-black text-white">{{ $jenis }}</span></td>
                                          <td class="px-4 py-2 text-xs font-medium text-slate-600">{{ $lokasi }}</td>
                                          <td class="px-4 py-2 text-xs text-slate-700">{{ $detailLokasi }}</td>
                                          <td class="px-4 py-2 text-xs text-slate-900 max-w-[240px]">{{ $deskripsi }}</td>
                                          <td class="px-4 py-2 text-xs font-medium">{{ $status }}</td>
                                          <td class="px-4 py-2 text-xs text-slate-600 whitespace-nowrap">{{ $tanggal ? \Carbon\Carbon::parse($tanggal)->format('d/m/Y H:i') : '—' }}</td>
                                          <td class="px-4 py-2 text-xs">{{ $pelapor }}</td>
                                          <td class="px-4 py-2 text-xs">{{ $nilaiResiko }}</td>
                                          <td class="px-4 py-2 text-xs">{{ $kategori }}</td>
                                       </tr>
                                    @endforeach
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
                  @endif
                  <!-- <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">
                     <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-5 py-3">
                        <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest">Aktivitas Kritis</h3>
                        <div class="flex gap-4">
                           @if($hasFilter)
                              <span class="text-[10px] font-bold text-slate-500">Lokasi: {{ $filterLokasi ?: '—' }} • Detail: {{ $filterDetailLokasi ?: '—' }}</span>
                           @else
                              <div class="flex items-center gap-1.5">
                                 <span class="h-2 w-2 rounded-full bg-muted-coral"></span>
                                 <span class="text-[10px] font-bold text-slate-500">Unclosed Critical</span>
                              </div>
                              <div class="flex items-center gap-1.5">
                                 <span class="h-2 w-2 rounded-full bg-soft-amber"></span>
                                 <span class="text-[10px] font-bold text-slate-500">To Be Concern</span>
                              </div>
                           @endif
                        </div>
                     </div>
                     <div class="overflow-x-auto">
                        <table class="w-full text-left">
                           <thead class="bg-slate-50/50 text-[10px] uppercase tracking-wider text-slate-500">
                              <tr>
                                 <th class="px-5 py-3 font-bold">Tasklist</th>
                                 <th class="px-5 py-3 font-bold">Lokasi / Detail</th>
                                 <th class="px-5 py-3 font-bold">Category</th>
                                 <th class="px-5 py-3 font-bold">Aktivitas</th>
                                 <th class="px-5 py-3 font-bold">PIC (Pengawas)</th>
                                 <th class="px-5 py-3 font-bold text-right">Action</th>
                              </tr>
                           </thead>
                           <tbody class="divide-y divide-slate-100 text-sm">
                              @forelse($aktivitasKritis as $row)
                                 <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-4">
                                       <div class="flex items-center gap-2">
                                          <span class="rounded-sm {{ $row->source_type === 'IKK' ? 'bg-muted-coral' : 'bg-soft-amber' }} px-1.5 py-0.5 text-[10px] font-black text-white">{{ $row->source_type }}</span>
                                          <span class="font-mono text-xs font-bold">{{ $row->no_ikk ?: '—' }}</span>
                                       </div>
                                    </td>
                                    <td class="px-5 py-4 text-xs">
                                       <span class="font-medium text-slate-600">{{ $row->lokasi ?: '—' }}</span>
                                       <span class="text-slate-400">/</span>
                                       <span class="text-slate-700">{{ $row->detail_lokasi ?: '—' }}</span>
                                    </td>
                                    <td class="px-5 py-4 font-medium text-slate-600">{{ $row->kategori_area ?: $row->source_type }}</td>
                                    <td class="px-5 py-4 text-xs font-medium text-slate-900">{{ $row->aktivitas ?: '—' }}</td>
                                    <td class="px-5 py-4 text-xs">{{ $row->pengawas_langsung ?: '—' }}</td>
                                    <td class="px-5 py-4 text-right">
                                       <button type="button" class="text-[10px] font-black uppercase text-emerald-600 hover:underline">Close</button>
                                    </td>
                                 </tr>
                              @empty
                                 <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-slate-500 text-sm">
                                       @if($hasFilter)
                                          Tidak ada aktivitas untuk lokasi &amp; detail lokasi ini.
                                       @else
                                          Tidak ada aktivitas kritis untuk tanggal ini.
                                       @endif
                                    </td>
                                 </tr>
                              @endforelse
                           </tbody>
                        </table>
                     </div>
                  </div> -->
                  <!-- <div class="flex flex-col gap-4">
                     <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest pl-1">Active Special Permits (IPK/OKK)</h3>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-lg border-l-4 border-l-emerald-500 border border-slate-200 bg-white p-4 shadow-sm relative overflow-hidden">
                           <div class="flex justify-between items-start mb-4">
                              <div>
                                 <div class="flex items-center gap-2">
                                    <span class="rounded bg-emerald-50 px-1.5 py-0.5 text-[10px] font-black text-emerald-700 border border-emerald-100">IPK</span>
                                    <span class="font-mono text-xs font-bold text-slate-500 tracking-tight">#PR-8821990</span>
                                 </div>
                                 <h4 class="mt-2 text-sm font-extrabold text-slate-900 uppercase">Hot Work: Welding B-7</h4>
                              </div>
                              <div class="text-right">
                                 <p class="text-[10px] font-bold text-slate-400 uppercase">Time Remaining</p>
                                 <p class="font-mono text-lg font-black text-emerald-600">02:44:12</p>
                              </div>
                           </div>
                           <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                              <div>
                                 <p class="data-label">Scope</p>
                                 <p class="data-value truncate">Pipe Welding</p>
                              </div>
                              <div>
                                 <p class="data-label">Supervisor</p>
                                 <p class="data-value truncate">David K. (Safety)</p>
                              </div>
                           </div>
                        </div>
                        <div class="rounded-lg border-l-4 border-l-soft-amber border border-slate-200 bg-white p-4 shadow-sm relative overflow-hidden">
                           <div class="flex justify-between items-start mb-4">
                              <div>
                                 <div class="flex items-center gap-2">
                                    <span class="rounded bg-orange-50 px-1.5 py-0.5 text-[10px] font-black text-orange-700 border border-orange-100">OKK</span>
                                    <span class="font-mono text-xs font-bold text-slate-500 tracking-tight">#PR-8822004</span>
                                 </div>
                                 <h4 class="mt-2 text-sm font-extrabold text-slate-900 uppercase">Confined Space: Tank 4</h4>
                              </div>
                              <div class="text-right">
                                 <p class="text-[10px] font-bold text-slate-400 uppercase">Time Remaining</p>
                                 <p class="font-mono text-lg font-black text-orange-500">00:12:44</p>
                              </div>
                           </div>
                           <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                              <div>
                                 <p class="data-label">Scope</p>
                                 <p class="data-value truncate">Internal Inspection</p>
                              </div>
                              <div>
                                 <p class="data-label">Supervisor</p>
                                 <p class="data-value truncate">Rina M. (HSE)</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div> -->
                  <!-- <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                     <div class="flex items-center justify-between mb-4">
                        <h4 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest">Zone Compliance Visualization</h4>
                        <span class="text-[10px] font-bold text-slate-400">Floor 01 Schematic</span>
                     </div>
                     <div class="aspect-[21/9] w-full rounded bg-slate-50 relative overflow-hidden border border-slate-100">
                        <img alt="Site Map" class="w-full h-full object-cover opacity-40 grayscale" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB2VB9dnp2obhE99qqcxoaZh26gqv-AmdyhL_fUx18wEP4zIpzp8d8-hfXxMkGcq8CZp-r38MO6MAlFIzJsB2Jp_d2K_cfHbuttVaDSFKJuCD9To211WZi9JNXLrjL5N6PtehoLVxsmqpAMh_bosx4gtstbdrG03euh6DSd1gN8iU10PZgl-HVbj6BdRVNCE0A8Ed7VntJ6YrS5Vf7DaXVivMyOJzL0oSTJobbW1EXu4ze5ohh7Z1_x2VNsiKqghIYOoG-Vn-7n4WpP"/>
                        <div class="absolute top-1/2 left-1/4 h-4 w-4">
                           <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-muted-coral opacity-75"></span>
                           <span class="relative inline-flex rounded-full h-4 w-4 bg-muted-coral"></span>
                        </div>
                        <div class="absolute bottom-1/4 right-1/3 h-4 w-4">
                           <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-soft-amber opacity-75"></span>
                           <span class="relative inline-flex rounded-full h-4 w-4 bg-soft-amber"></span>
                        </div>
                        <div class="absolute top-1/4 right-1/4 h-3 w-3 rounded-full bg-emerald-500"></div>
                     </div>
                  </div> -->
               </div>
            </div>
         </main>
         <footer class="fixed bottom-0 z-40 w-full border-t border-slate-200 bg-white/95 backdrop-blur-sm py-4 px-6 lg:px-12 dark:border-slate-800 dark:bg-slate-900/95 shadow-[0_-8px_30px_rgb(0,0,0,0.04)]">
            <div class="mx-auto flex max-w-[1600px] items-center justify-between">
               <div class="flex items-center gap-8">
                  <div class="flex items-center gap-2">
                     <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                     <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">System Nominal</span>
                  </div>
                  <div class="hidden sm:flex gap-4">
                     <div class="flex flex-col">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Hazards MTD</span>
                        <span class="text-xs font-black text-slate-700">124 Detected</span>
                     </div>
                     <div class="flex flex-col">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Resolution Rate</span>
                        <span class="text-xs font-black text-emerald-600">92.4%</span>
                     </div>
                  </div>
               </div>
               <div class="flex items-center gap-3">
                  <span class="text-[10px] font-bold text-slate-400 uppercase mr-2">Ready for Next Inspection Cycle</span>
                  <button class="inline-flex h-10 items-center justify-center rounded bg-slate-900 px-8 text-xs font-black text-white hover:bg-slate-800 transition-all shadow-md">
                  INITIATE FULL SITE AUDIT
                  </button>
               </div>
            </div>
         </footer>
      </div>
      <script>
         function openInsidenModal(btn) {
            var el = typeof btn === 'object' ? btn : document.querySelector('[data-index="' + btn + '"]');
            if (!el) return;
            var index = el.getAttribute('data-index');
            var no = el.getAttribute('data-no') || '';
            var modal = document.getElementById('modalDetailInsiden');
            var titleEl = document.getElementById('modalDetailInsidenTitle');
            if (!modal || !titleEl) return;
            document.querySelectorAll('.insiden-detail-panel').forEach(function(panel) { panel.classList.add('hidden'); });
            var panel = document.getElementById('insiden-detail-' + index);
            if (panel) panel.classList.remove('hidden');
            titleEl.textContent = 'Detail Insiden — ' + no;
            modal.classList.remove('hidden');
         }
         function closeInsidenModal() {
            var modal = document.getElementById('modalDetailInsiden');
            if (modal) modal.classList.add('hidden');
         }
         document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
               var m = document.getElementById('modalDetailAktivitas');
               if (m) m.classList.add('hidden');
               closeInsidenModal();
            }
         });
      </script>
   </body>
</html>