@extends('PembatasanLV.layouts.app')

@section('title', 'Dashboard Overview Pembatasan LV & Orang')

@section('page-header')
   @include('PembatasanLV.partials.page-header', [
      'breadcrumbCurrent' => 'Pembatasan LV',
      'pageTitle' => 'Dashboard Overview',
      'pageSubtitle' => 'Monitoring dan evaluasi pembatasan level • ' . now()->format('d M Y'),
      'actionsPartial' => 'PembatasanLV.partials.filter-bar',
      'actionsData' => [
         'sites' => $sites,
         'controlRooms' => $controlRooms,
         'filters' => $filters,
      ],
   ])
@endsection

@section('content')
   <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between">
         <div class="flex justify-between items-start">
            <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Total LV Masuk</span>
            <div class="p-2 bg-primary/10 rounded-lg">
               <span class="material-symbols-outlined text-primary" data-icon="inventory_2">inventory_2</span>
            </div>
         </div>
         <div class="mt-4">
            <p class="font-headline font-extrabold text-4xl tabular-nums">{{ number_format($lvMasukAktif) }}</p>
            <p class="text-on-surface-variant text-[11px] font-medium mt-1">
               Belum checkout · area CR Anda
            </p>
         </div>
      </div>

      <div class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between">
         <div class="flex justify-between items-start">
            <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Total LV Keluar</span>
            <div class="p-2 bg-primary/10 rounded-lg">
               <span class="material-symbols-outlined text-primary" data-icon="logout">logout</span>
            </div>
         </div>
         <div class="mt-4">
            <p class="font-headline font-extrabold text-4xl tabular-nums">{{ number_format($lvKeluar) }}</p>
            <p class="text-on-surface-variant text-[11px] font-medium mt-1">
               Checkout {{ \Carbon\Carbon::parse($filters['tanggal'])->format('d M Y') }}
               @if($filters['control_room'] !== '')
                  • {{ $filters['control_room'] }}
               @endif
            </p>
         </div>
      </div>

      <div class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between">
         <div class="flex justify-between items-start">
            <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Total Orang Masuk</span>
            <div class="p-2 bg-primary/10 rounded-lg">
               <span class="material-symbols-outlined text-primary" data-icon="groups">groups</span>
            </div>
         </div>
         <div class="mt-4">
            <p class="font-headline font-extrabold text-4xl tabular-nums">{{ number_format($orangMasukAktif) }}</p>
            <p class="text-on-surface-variant text-[11px] font-medium mt-1">
               Belum checkout · area CR Anda
            </p>
         </div>
      </div>

      <div class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between">
         <div class="flex justify-between items-start">
            <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Total Orang Keluar</span>
            <div class="p-2 bg-primary/10 rounded-lg">
               <span class="material-symbols-outlined text-primary" data-icon="groups">groups</span>
            </div>
         </div>
         <div class="mt-4">
            <p class="font-headline font-extrabold text-4xl tabular-nums">{{ number_format($orangKeluar) }}</p>
            <p class="text-on-surface-variant text-[11px] font-medium mt-1">
               Checkout {{ \Carbon\Carbon::parse($filters['tanggal'])->format('d M Y') }}
               @if($filters['control_room'] !== '')
                  • {{ $filters['control_room'] }}
               @endif
            </p>
         </div>
      </div>
   </div>

   <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-2xl anchored-card overflow-hidden">
         <div class="border-b border-outline-variant/20 px-6 py-4 flex items-start justify-between gap-3">
            <div>
               <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">LV Masuk (Belum Checkout)</span>
               <!-- <p class="font-headline font-extrabold text-3xl tabular-nums mt-1">{{ number_format($lvMasukAktif) }}</p> -->
               <!-- <p class="text-on-surface-variant text-[11px] font-medium mt-1">
                  @if($supervisedRooms->isNotEmpty())
                     {{ $supervisedRooms->implode(', ') }}
                  @else
                     Belum terdaftar sebagai pengawas control room
                  @endif
               </p> -->
            </div>
            <div class="p-2 bg-primary/10 rounded-lg shrink-0">
               <span class="material-symbols-outlined text-primary">local_shipping</span>
            </div>
         </div>
         <div class="overflow-x-auto max-h-[320px] overflow-y-auto">
            <table class="w-full text-left">
               <thead class="border-b border-outline-variant/20 bg-[#f8fafc] sticky top-0">
                  <tr>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">No</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Driver</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">LV</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Lokasi</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Durasi</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant w-24">Aksi</th>
                  </tr>
               </thead>
               <tbody id="plv-lv-masuk-aktif-tbody" class="divide-y divide-outline-variant/10">
                  @forelse($lvMasukAktifList as $index => $row)
                  <tr class="transition-colors hover:bg-[#f8fafc]" data-inputasi-id="{{ $row->id }}">
                     <td class="px-4 py-3 text-sm tabular-nums text-on-surface-variant">{{ $index + 1 }}</td>
                     <td class="px-4 py-3 text-sm text-on-surface">{{ $row->nama_driver }}</td>
                     <td class="px-4 py-3 text-sm font-bold text-on-background">{{ $row->no_lambung }}</td>
                     <td class="px-4 py-3 text-sm text-on-surface">
                        <div>{{ $row->lokasi }}</div>
                        @if($row->detail_lokasi)
                        <div class="text-xs text-on-surface-variant mt-0.5">{{ $row->detail_lokasi }}</div>
                        @endif
                     </td>
                     <td class="px-4 py-3 text-sm whitespace-nowrap">
                        <span class="plv-durasi-live font-mono font-bold text-primary tabular-nums" data-checkin-at="{{ $row->checkin_at?->timezone(config('app.timezone'))->toIso8601String() }}">00:00:00</span>
                     </td>
                     <td class="px-4 py-3 text-sm">
                        <form method="POST" action="{{ route('pembatasan-lv.checkout.lv', $row) }}" class="plv-checkout-form inline" data-unit="{{ $row->no_lambung }}" data-driver="{{ $row->nama_driver }}">
                           @csrf
                           <button type="submit" class="inline-flex items-center gap-0.5 rounded-lg bg-emerald-600 px-2 py-1 text-[10px] font-bold text-white hover:bg-emerald-700">
                              <span class="material-symbols-outlined text-sm">logout</span>
                              Checkout
                           </button>
                        </form>
                     </td>
                  </tr>
                  @empty
                  <tr id="plv-lv-masuk-aktif-empty">
                     <td colspan="6" class="px-4 py-8 text-center text-sm text-on-surface-variant">
                        @if($supervisedRooms->isEmpty())
                           Tidak ada control room terdaftar.
                        @else
                           Tidak ada LV masuk yang belum checkout.
                        @endif
                     </td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>

      <div class="bg-white rounded-2xl anchored-card overflow-hidden">
         <div class="border-b border-outline-variant/20 px-6 py-4 flex items-start justify-between gap-3">
            <div>
               <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Orang Masuk (Belum Checkout)</span>
            </div>
            <div class="p-2 bg-primary/10 rounded-lg shrink-0">
               <span class="material-symbols-outlined text-primary">groups</span>
            </div>
         </div>
         <div class="overflow-x-auto max-h-[320px] overflow-y-auto">
            <table class="w-full text-left">
               <thead class="border-b border-outline-variant/20 bg-[#f8fafc] sticky top-0">
                  <tr>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">No</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Nama</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">SID</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Lokasi</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Durasi</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant w-24">Aksi</th>
                  </tr>
               </thead>
               <tbody id="plv-orang-masuk-aktif-tbody" class="divide-y divide-outline-variant/10">
                  @forelse($orangMasukAktifList as $index => $row)
                  <tr class="transition-colors hover:bg-[#f8fafc]" data-inputasi-id="{{ $row->id }}">
                     <td class="px-4 py-3 text-sm tabular-nums text-on-surface-variant">{{ $index + 1 }}</td>
                     <td class="px-4 py-3 text-sm text-on-surface">{{ $row->nama }}</td>
                     <td class="px-4 py-3 text-sm font-bold text-on-background">{{ $row->sid }}</td>
                     <td class="px-4 py-3 text-sm text-on-surface">
                        <div>{{ $row->lokasi }}</div>
                        @if($row->detail_lokasi)
                        <div class="text-xs text-on-surface-variant mt-0.5">{{ $row->detail_lokasi }}</div>
                        @endif
                     </td>
                     <td class="px-4 py-3 text-sm whitespace-nowrap">
                        <span class="plv-durasi-live font-mono font-bold text-primary tabular-nums" data-checkin-at="{{ $row->checkin_at?->timezone(config('app.timezone'))->toIso8601String() }}">00:00:00</span>
                     </td>
                     <td class="px-4 py-3 text-sm">
                        <form method="POST" action="{{ route('pembatasan-lv.checkout.orang', $row) }}" class="plv-checkout-orang-form inline" data-sid="{{ $row->sid }}" data-nama="{{ $row->nama }}">
                           @csrf
                           <button type="submit" class="inline-flex items-center gap-0.5 rounded-lg bg-emerald-600 px-2 py-1 text-[10px] font-bold text-white hover:bg-emerald-700">
                              <span class="material-symbols-outlined text-sm">logout</span>
                              Checkout
                           </button>
                        </form>
                     </td>
                  </tr>
                  @empty
                  <tr id="plv-orang-masuk-aktif-empty">
                     <td colspan="6" class="px-4 py-8 text-center text-sm text-on-surface-variant">
                        @if($supervisedRooms->isEmpty())
                           Tidak ada control room terdaftar.
                        @else
                           Tidak ada orang masuk yang belum checkout.
                        @endif
                     </td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>

     
   </div>

   <!-- <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
     

      <div class="bg-white rounded-2xl anchored-card overflow-hidden">
         <div class="border-b border-outline-variant/20 px-6 py-4 flex items-start justify-between gap-3">
            <div>
               <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Orang Keluar (Checkout)</span>
               <p class="font-headline font-extrabold text-3xl tabular-nums mt-1">{{ number_format($orangKeluar) }}</p>
               <p class="text-on-surface-variant text-[11px] font-medium mt-1">
                  {{ \Carbon\Carbon::parse($filters['tanggal'])->format('d M Y') }}
                  @if($filters['control_room'] !== '')
                     • {{ $filters['control_room'] }}
                  @else
                     • Semua CR Anda
                  @endif
               </p>
            </div>
            <div class="p-2 bg-emerald-50 rounded-lg shrink-0">
               <span class="material-symbols-outlined text-emerald-700">logout</span>
            </div>
         </div>
         <div class="overflow-x-auto max-h-[320px] overflow-y-auto">
            <table class="w-full text-left">
               <thead class="border-b border-outline-variant/20 bg-[#f8fafc] sticky top-0">
                  <tr>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">No</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Nama</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">SID</th>
                     <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Check-in</th>
                  </tr>
               </thead>
               <tbody class="divide-y divide-outline-variant/10">
                  @forelse($orangKeluarList as $index => $row)
                  <tr class="transition-colors hover:bg-[#f8fafc]">
                     <td class="px-4 py-3 text-sm tabular-nums text-on-surface-variant">{{ $index + 1 }}</td>
                     <td class="px-4 py-3 text-sm text-on-surface">{{ $row->nama }}</td>
                     <td class="px-4 py-3 text-sm font-bold text-on-background">{{ $row->sid }}</td>
                     <td class="px-4 py-3 text-sm text-on-surface whitespace-nowrap">{{ $row->checkin_at?->timezone(config('app.timezone'))->format('d M Y H:i') }}</td>
                  </tr>
                  @empty
                  <tr>
                     <td colspan="4" class="px-4 py-8 text-center text-sm text-on-surface-variant">
                        Tidak ada orang checkout pada tanggal ini.
                     </td>
                  </tr>
                  @endforelse
               </tbody>
            </table>
         </div>
      </div>
   </div> -->

   <div class="mt-6 bg-white rounded-2xl anchored-card overflow-hidden">
      <div class="border-b border-outline-variant/20 px-6 py-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
         <div>
            <h3 class="font-headline font-bold text-base text-on-background">Daftar Semua Inputasi LV</h3>
            <p class="text-xs text-on-surface-variant">Semua LV check-in & check-out di control room Anda • filter tanggal {{ \Carbon\Carbon::parse($filters['tanggal'])->format('d M Y') }}</p>
         </div>
         <a href="{{ route('pembatasan-lv.inputasi.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline">
            <span class="material-symbols-outlined text-base">add</span>
            Inputasi LV Baru
         </a>
      </div>

      <div class="overflow-x-auto">
         <table class="w-full min-w-[1000px] text-left">
            <thead class="border-b border-outline-variant/20 bg-[#f8fafc]">
               <tr>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">No</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">No Unit</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Driver</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Control Room</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Lokasi</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Check-in</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Check-out</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Status</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Shift</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant w-28">Aksi</th>
               </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
               @forelse($lvAllList as $index => $row)
               <tr class="transition-colors hover:bg-[#f8fafc]">
                  <td class="px-4 py-4 text-sm tabular-nums text-on-surface-variant">{{ $index + 1 }}</td>
                  <td class="px-4 py-4 text-sm font-bold text-on-background">{{ $row->no_lambung }}</td>
                  <td class="px-4 py-4 text-sm text-on-surface">{{ $row->nama_driver }}</td>
                  <td class="px-4 py-4 text-sm text-on-surface">{{ $row->control_room }}</td>
                  <td class="px-4 py-4 text-sm text-on-surface">
                     <div>{{ $row->lokasi }}</div>
                     @if($row->detail_lokasi)
                     <div class="text-xs text-on-surface-variant mt-0.5">{{ $row->detail_lokasi }}</div>
                     @endif
                  </td>
                  <td class="px-4 py-4 text-sm text-on-surface whitespace-nowrap">{{ $row->checkin_at?->timezone(config('app.timezone'))->format('d M Y H:i') }}</td>
                  <td class="px-4 py-4 text-sm text-on-surface whitespace-nowrap">
                     @if($row->checkout_at)
                        {{ $row->checkout_at->timezone(config('app.timezone'))->format('d M Y H:i') }}
                     @else
                        <span class="text-on-surface-variant">—</span>
                     @endif
                  </td>
                  <td class="px-4 py-4 text-sm">
                     @if($row->checkout_at)
                     <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-800">Checkout</span>
                     @else
                     <span class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-800">Di Area</span>
                     @endif
                  </td>
                  <td class="px-4 py-4 text-sm">
                     <span class="inline-flex rounded-full bg-[#eef2ff] px-2.5 py-0.5 text-[10px] font-bold text-primary">Shift {{ $row->shift }}</span>
                  </td>
                  <td class="px-4 py-4 text-sm">
                     @if($row->checkout_at === null)
                     <form method="POST" action="{{ route('pembatasan-lv.checkout.lv', $row) }}" class="plv-checkout-form inline" data-unit="{{ $row->no_lambung }}" data-driver="{{ $row->nama_driver }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700">
                           <span class="material-symbols-outlined text-base">logout</span>
                           Checkout
                        </button>
                     </form>
                     @else
                     <span class="text-xs text-on-surface-variant">—</span>
                     @endif
                  </td>
               </tr>
               @empty
               <tr>
                  <td colspan="10" class="px-6 py-12 text-center text-sm text-on-surface-variant">
                     @if($supervisedRooms->isEmpty())
                        Tidak ada control room terdaftar untuk akun Anda.
                     @else
                        Tidak ada data inputasi LV pada tanggal ini.
                     @endif
                  </td>
               </tr>
               @endforelse
            </tbody>
         </table>
      </div>
   </div>

   <div class="mt-6 bg-white rounded-2xl anchored-card overflow-hidden">
      <div class="border-b border-outline-variant/20 px-6 py-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
         <div>
            <h3 class="font-headline font-bold text-base text-on-background">Daftar Semua Inputasi Orang</h3>
            <p class="text-xs text-on-surface-variant">Semua orang check-in & check-out di control room Anda • filter tanggal {{ \Carbon\Carbon::parse($filters['tanggal'])->format('d M Y') }}</p>
         </div>
         <a href="{{ route('pembatasan-lv.inputasi.index', ['tab' => 'orang']) }}" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline">
            <span class="material-symbols-outlined text-base">add</span>
            Inputasi Orang Baru
         </a>
      </div>

      <div class="overflow-x-auto">
         <table class="w-full min-w-[1000px] text-left">
            <thead class="border-b border-outline-variant/20 bg-[#f8fafc]">
               <tr>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">No</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">SID</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Nama</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Perusahaan</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Control Room</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Lokasi</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Check-in</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Check-out</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Status</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant">Shift</th>
                  <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant w-28">Aksi</th>
               </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
               @forelse($orangAllList as $index => $row)
               <tr class="transition-colors hover:bg-[#f8fafc]">
                  <td class="px-4 py-4 text-sm tabular-nums text-on-surface-variant">{{ $index + 1 }}</td>
                  <td class="px-4 py-4 text-sm font-bold text-on-background">{{ $row->sid }}</td>
                  <td class="px-4 py-4 text-sm text-on-surface">{{ $row->nama }}</td>
                  <td class="px-4 py-4 text-sm text-on-surface">{{ $row->nama_perusahaan ?: '—' }}</td>
                  <td class="px-4 py-4 text-sm text-on-surface">{{ $row->control_room }}</td>
                  <td class="px-4 py-4 text-sm text-on-surface">
                     <div>{{ $row->lokasi }}</div>
                     @if($row->detail_lokasi)
                     <div class="text-xs text-on-surface-variant mt-0.5">{{ $row->detail_lokasi }}</div>
                     @endif
                  </td>
                  <td class="px-4 py-4 text-sm text-on-surface whitespace-nowrap">{{ $row->checkin_at?->timezone(config('app.timezone'))->format('d M Y H:i') }}</td>
                  <td class="px-4 py-4 text-sm text-on-surface whitespace-nowrap">
                     @if($row->checkout_at)
                        {{ $row->checkout_at->timezone(config('app.timezone'))->format('d M Y H:i') }}
                     @else
                        <span class="text-on-surface-variant">—</span>
                     @endif
                  </td>
                  <td class="px-4 py-4 text-sm">
                     @if($row->checkout_at)
                     <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-800">Checkout</span>
                     @else
                     <span class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-800">Di Area</span>
                     @endif
                  </td>
                  <td class="px-4 py-4 text-sm">
                     <span class="inline-flex rounded-full bg-[#eef2ff] px-2.5 py-0.5 text-[10px] font-bold text-primary">Shift {{ $row->shift }}</span>
                  </td>
                  <td class="px-4 py-4 text-sm">
                     @if($row->checkout_at === null)
                     <form method="POST" action="{{ route('pembatasan-lv.checkout.orang', $row) }}" class="plv-checkout-orang-form inline" data-sid="{{ $row->sid }}" data-nama="{{ $row->nama }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700">
                           <span class="material-symbols-outlined text-base">logout</span>
                           Checkout
                        </button>
                     </form>
                     @else
                     <span class="text-xs text-on-surface-variant">—</span>
                     @endif
                  </td>
               </tr>
               @empty
               <tr>
                  <td colspan="11" class="px-6 py-12 text-center text-sm text-on-surface-variant">
                     @if($supervisedRooms->isEmpty())
                        Tidak ada control room terdaftar untuk akun Anda.
                     @else
                        Tidak ada data inputasi orang pada tanggal ini.
                     @endif
                  </td>
               </tr>
               @endforelse
            </tbody>
         </table>
      </div>
   </div>
@endsection

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
   function showFlash() {
      @if(session('success'))
      if (typeof Swal !== 'undefined') {
         Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: @json(session('success')),
            confirmButtonText: 'OK',
            confirmButtonColor: '#3952bc',
         });
      }
      @endif
      @if(session('error'))
      if (typeof Swal !== 'undefined') {
         Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: @json(session('error')),
            confirmButtonText: 'OK',
            confirmButtonColor: '#3952bc',
         });
      }
      @endif
   }

   document.addEventListener('submit', function (e) {
      var lvForm = e.target.closest('.plv-checkout-form');
      var orangForm = e.target.closest('.plv-checkout-orang-form');
      var form = lvForm || orangForm;
      if (!form) return;
      e.preventDefault();

      var message;
      var title;
      if (orangForm) {
         var nama = orangForm.getAttribute('data-nama') || 'Orang';
         var sid = orangForm.getAttribute('data-sid') || '';
         message = sid !== ''
            ? 'Checkout ' + nama + ' (SID: ' + sid + ') sekarang?'
            : 'Checkout ' + nama + ' sekarang?';
         title = 'Checkout Orang?';
      } else {
         var unit = form.getAttribute('data-unit') || 'LV';
         var driver = form.getAttribute('data-driver') || '';
         message = driver !== ''
            ? 'Checkout unit ' + unit + ' (Driver: ' + driver + ') sekarang?'
            : 'Checkout unit ' + unit + ' sekarang?';
         title = 'Checkout LV?';
      }

      if (typeof Swal === 'undefined') {
         if (window.confirm(message)) form.submit();
         return;
      }

      Swal.fire({
         title: title,
         text: message,
         icon: 'question',
         showCancelButton: true,
         confirmButtonText: 'Ya, Checkout',
         cancelButtonText: 'Batal',
         confirmButtonColor: '#059669',
         cancelButtonColor: '#94a3b8',
         reverseButtons: true,
      }).then(function (result) {
         if (result.isConfirmed) form.submit();
      });
   });

   var masukAktifUrl = @json(route('pembatasan-lv.lv-masuk-aktif.data'));
   var orangMasukAktifUrl = @json(route('pembatasan-lv.orang-masuk-aktif.data'));
   var checkoutUrlBase = @json(url('/pembatasan-lv/checkout'));
   var checkoutOrangUrlBase = @json(url('/pembatasan-lv/checkout-orang'));
   var csrfToken = @json(csrf_token());
   var filterParams = @json($filters);
   var supervisedRoomsEmpty = @json($supervisedRooms->isEmpty());
   var serverOffsetMs = 0;

   function escapeHtml(value) {
      return String(value ?? '')
         .replace(/&/g, '&amp;')
         .replace(/</g, '&lt;')
         .replace(/>/g, '&gt;')
         .replace(/"/g, '&quot;');
   }

   function formatDurasi(totalSeconds) {
      totalSeconds = Math.max(0, Math.floor(totalSeconds));
      var h = Math.floor(totalSeconds / 3600);
      var m = Math.floor((totalSeconds % 3600) / 60);
      var s = totalSeconds % 60;
      var pad = function (n) { return String(n).padStart(2, '0'); };
      if (h >= 24) {
         var d = Math.floor(h / 24);
         h = h % 24;
         return d + 'h ' + pad(h) + ':' + pad(m) + ':' + pad(s);
      }
      return pad(h) + ':' + pad(m) + ':' + pad(s);
   }

   function durasiSecondsFromCheckin(checkinIso) {
      if (!checkinIso) return 0;
      var start = Date.parse(checkinIso);
      if (Number.isNaN(start)) return 0;
      return Math.max(0, Math.floor((Date.now() + serverOffsetMs - start) / 1000));
   }

   function tickDurasiLive() {
      document.querySelectorAll('.plv-durasi-live').forEach(function (el) {
         var checkinAt = el.getAttribute('data-checkin-at');
         el.textContent = formatDurasi(durasiSecondsFromCheckin(checkinAt));
      });
   }

   function renderMasukAktifRows(rows) {
      var tbody = document.getElementById('plv-lv-masuk-aktif-tbody');
      if (!tbody) return;

      if (!rows.length) {
         tbody.innerHTML = '<tr id="plv-lv-masuk-aktif-empty"><td colspan="6" class="px-4 py-8 text-center text-sm text-on-surface-variant">' +
            (supervisedRoomsEmpty ? 'Tidak ada control room terdaftar.' : 'Tidak ada LV masuk yang belum checkout.') +
         '</td></tr>';
         return;
      }

      tbody.innerHTML = rows.map(function (row, index) {
         return '<tr class="transition-colors hover:bg-[#f8fafc]" data-inputasi-id="' + row.id + '">' +
            '<td class="px-4 py-3 text-sm tabular-nums text-on-surface-variant">' + (index + 1) + '</td>' +
            '<td class="px-4 py-3 text-sm text-on-surface">' + escapeHtml(row.nama_driver) + '</td>' +
            '<td class="px-4 py-3 text-sm font-bold text-on-background">' + escapeHtml(row.no_lambung) + '</td>' +
            '<td class="px-4 py-3 text-sm text-on-surface">' +
               '<div>' + escapeHtml(row.lokasi) + '</div>' +
               (row.detail_lokasi ? '<div class="text-xs text-on-surface-variant mt-0.5">' + escapeHtml(row.detail_lokasi) + '</div>' : '') +
            '</td>' +
            '<td class="px-4 py-3 text-sm whitespace-nowrap">' +
               '<span class="plv-durasi-live font-mono font-bold text-primary tabular-nums" data-checkin-at="' + escapeHtml(row.checkin_at) + '">' +
                  formatDurasi(row.durasi_detik || 0) +
               '</span>' +
            '</td>' +
            '<td class="px-4 py-3 text-sm">' +
               '<form method="POST" action="' + checkoutUrlBase + '/' + row.id + '" class="plv-checkout-form inline" data-unit="' + escapeHtml(row.no_lambung) + '" data-driver="' + escapeHtml(row.nama_driver) + '">' +
                  '<input type="hidden" name="_token" value="' + escapeHtml(csrfToken) + '">' +
                  '<button type="submit" class="inline-flex items-center gap-0.5 rounded-lg bg-emerald-600 px-2 py-1 text-[10px] font-bold text-white hover:bg-emerald-700">' +
                     '<span class="material-symbols-outlined text-sm">logout</span> Checkout' +
                  '</button>' +
               '</form>' +
            '</td>' +
         '</tr>';
      }).join('');

      tickDurasiLive();
   }

   function fetchMasukAktif() {
      var url = new URL(masukAktifUrl, window.location.origin);
      if (filterParams.site) url.searchParams.set('site', filterParams.site);
      if (filterParams.tanggal) url.searchParams.set('tanggal', filterParams.tanggal);
      if (filterParams.control_room) url.searchParams.set('control_room', filterParams.control_room);

      fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
         .then(function (res) { return res.json(); })
         .then(function (json) {
            if (json.meta && json.meta.server_now) {
               var serverNow = Date.parse(json.meta.server_now);
               if (!Number.isNaN(serverNow)) {
                  serverOffsetMs = serverNow - Date.now();
               }
            }
            renderMasukAktifRows(json.data || []);
         })
         .catch(function () { /* keep current rows on error */ });
   }

   function renderOrangMasukAktifRows(rows) {
      var tbody = document.getElementById('plv-orang-masuk-aktif-tbody');
      if (!tbody) return;

      if (!rows.length) {
         tbody.innerHTML = '<tr id="plv-orang-masuk-aktif-empty"><td colspan="6" class="px-4 py-8 text-center text-sm text-on-surface-variant">' +
            (supervisedRoomsEmpty ? 'Tidak ada control room terdaftar.' : 'Tidak ada orang masuk yang belum checkout.') +
         '</td></tr>';
         return;
      }

      tbody.innerHTML = rows.map(function (row, index) {
         return '<tr class="transition-colors hover:bg-[#f8fafc]" data-inputasi-id="' + row.id + '">' +
            '<td class="px-4 py-3 text-sm tabular-nums text-on-surface-variant">' + (index + 1) + '</td>' +
            '<td class="px-4 py-3 text-sm text-on-surface">' + escapeHtml(row.nama) + '</td>' +
            '<td class="px-4 py-3 text-sm font-bold text-on-background">' + escapeHtml(row.sid) + '</td>' +
            '<td class="px-4 py-3 text-sm text-on-surface">' +
               '<div>' + escapeHtml(row.lokasi) + '</div>' +
               (row.detail_lokasi ? '<div class="text-xs text-on-surface-variant mt-0.5">' + escapeHtml(row.detail_lokasi) + '</div>' : '') +
            '</td>' +
            '<td class="px-4 py-3 text-sm whitespace-nowrap">' +
               '<span class="plv-durasi-live font-mono font-bold text-primary tabular-nums" data-checkin-at="' + escapeHtml(row.checkin_at) + '">' +
                  formatDurasi(row.durasi_detik || 0) +
               '</span>' +
            '</td>' +
            '<td class="px-4 py-3 text-sm">' +
               '<form method="POST" action="' + checkoutOrangUrlBase + '/' + row.id + '" class="plv-checkout-orang-form inline" data-sid="' + escapeHtml(row.sid) + '" data-nama="' + escapeHtml(row.nama) + '">' +
                  '<input type="hidden" name="_token" value="' + escapeHtml(csrfToken) + '">' +
                  '<button type="submit" class="inline-flex items-center gap-0.5 rounded-lg bg-emerald-600 px-2 py-1 text-[10px] font-bold text-white hover:bg-emerald-700">' +
                     '<span class="material-symbols-outlined text-sm">logout</span> Checkout' +
                  '</button>' +
               '</form>' +
            '</td>' +
         '</tr>';
      }).join('');

      tickDurasiLive();
   }

   function fetchOrangMasukAktif() {
      var url = new URL(orangMasukAktifUrl, window.location.origin);
      if (filterParams.site) url.searchParams.set('site', filterParams.site);
      if (filterParams.tanggal) url.searchParams.set('tanggal', filterParams.tanggal);
      if (filterParams.control_room) url.searchParams.set('control_room', filterParams.control_room);

      fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
         .then(function (res) { return res.json(); })
         .then(function (json) {
            if (json.meta && json.meta.server_now) {
               var serverNow = Date.parse(json.meta.server_now);
               if (!Number.isNaN(serverNow)) {
                  serverOffsetMs = serverNow - Date.now();
               }
            }
            renderOrangMasukAktifRows(json.data || []);
         })
         .catch(function () { /* keep current rows on error */ });
   }

   tickDurasiLive();
   setInterval(tickDurasiLive, 1000);
   setInterval(fetchMasukAktif, 30000);
   setInterval(fetchOrangMasukAktif, 30000);
   fetchMasukAktif();
   fetchOrangMasukAktif();

   showFlash();
})();
</script>
@endpush
