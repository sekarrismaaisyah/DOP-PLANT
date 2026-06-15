@extends('AutoBanned.layouts.app')

@section('title', 'Riwayat Email HSECT')

@push('head')
<style>
   .ab-hsct { --ab-ease: cubic-bezier(0.4, 0, 0.2, 1); }
   .ab-fade-in { animation: abFadeUp 0.55s var(--ab-ease) both; }
   .ab-fade-in-delay-1 { animation-delay: 0.06s; }
   .ab-fade-in-delay-2 { animation-delay: 0.12s; }
   .ab-fade-in-delay-3 { animation-delay: 0.18s; }
   @keyframes abFadeUp {
      from { opacity: 0; transform: translateY(10px); }
      to   { opacity: 1; transform: translateY(0); }
   }
   .ab-surface-card {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(57, 82, 188, 0.07);
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04), 0 8px 24px -6px rgba(57, 82, 188, 0.08);
   }
   .ab-badge {
      display: inline-flex;
      align-items: center;
      border-radius: 0.375rem;
      padding: 0.125rem 0.5rem;
      font-size: 10px;
      font-weight: 600;
      border: 1px solid rgba(57, 82, 188, 0.12);
      background: rgba(57, 82, 188, 0.06);
      color: #3952bc;
   }
   .ab-badge--ok {
      border-color: rgba(16, 185, 129, 0.25);
      background: rgba(16, 185, 129, 0.08);
      color: #047857;
   }
   .ab-badge--danger {
      border-color: rgba(239, 68, 68, 0.25);
      background: rgba(239, 68, 68, 0.08);
      color: #b91c1c;
   }
   .ab-sheet-table {
      border-collapse: collapse;
      font-size: 11px;
   }
   .ab-sheet-table thead th {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      padding: 0.5rem 0.625rem;
      font-size: 10px;
      font-weight: 600;
      color: #475569;
      white-space: nowrap;
   }
   .ab-sheet-table tbody td {
      border: 1px solid #e2e8f0;
      padding: 0.5rem 0.625rem;
      color: #1e293b;
      line-height: 1.45;
   }
   .ab-sheet-table tbody tr:hover td { background: #f8fafc; }
</style>
@endpush

@section('content')
@php
   $periodLabel = trim(($period['week'] ?? '').' · '.($period['year'] ?? ''), ' ·');
   $hsctCampaign = $hsctCampaign ?? null;
   $hsctEmailHistory = $hsctEmailHistory ?? collect();
   $hsctEmailAvailable = $hsctEmailAvailable ?? false;
   $hsctPendingItems = $hsctPendingItems ?? collect();
   $sendTime = config('auto_banned.hsct.send_time', '08:00');
@endphp

<div class="ab-hsct -mt-2 space-y-7">

   <section class="ab-page-header pb-6 border-b border-outline-variant/30">
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5 lg:gap-8">
         <div class="min-w-0 ab-fade-in">
            <nav class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.08em] text-on-surface-variant mb-2.5" aria-label="Breadcrumb">
               <span>Dashboard</span>
               <span class="material-symbols-outlined text-[13px] opacity-60">chevron_right</span>
               <span>Auto Banned</span>
               <span class="material-symbols-outlined text-[13px] opacity-60">chevron_right</span>
               <span class="text-primary">Riwayat Email HSECT</span>
            </nav>
            <div class="flex flex-wrap items-center gap-3">
               <h1 class="font-headline font-extrabold text-3xl sm:text-[2.125rem] text-on-background tracking-tight leading-tight">Riwayat Email HSECT</h1>
               <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/[0.08] border border-primary/15 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-primary">
                  <span class="material-symbols-outlined text-sm">mail</span>
                  Campaign Email
               </span>
            </div>
            <p class="text-sm text-on-surface-variant mt-2 max-w-2xl">
               Email awal setiap <strong>Selasa pukul {{ $sendTime }} WITA</strong> (semua SID banned tanpa SAP) ·
               Reminder harian untuk SID yang <strong>belum dikonfirmasi banned</strong>
               @if($periodLabel !== '') · {{ $periodLabel }}@endif
            </p>
         </div>
         <div class="shrink-0 ab-fade-in ab-fade-in-delay-1">
            @include('AutoBanned.partials.filter-bar', [
               'filters' => $filters,
               'filterOptions' => $filterOptions,
               'filterRoute' => $filterRoute ?? 'auto-banned.hsct-email.index',
            ])
         </div>
      </div>
   </section>

   <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
      <div class="xl:col-span-8 space-y-6">

         {{-- Campaign aktif --}}
         <div class="ab-fade-in ab-fade-in-delay-1 ab-surface-card rounded-2xl overflow-hidden">
            <div class="px-5 sm:px-6 pt-5 pb-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-outline-variant/10">
               <div>
                  <h2 class="font-headline font-semibold text-base text-on-background">Campaign Periode Ini</h2>
                  <p class="text-xs text-on-surface-variant mt-0.5">Status pengiriman email & progress konfirmasi banned HSECT</p>
               </div>
               @if($hsctEmailAvailable)
               <div class="flex flex-wrap items-center gap-2 self-start lg:self-auto">
                  <form method="POST" action="{{ route('auto-banned.hsct-email.initial') }}" class="inline">
                     @csrf
                     @foreach(['site', 'week', 'year', 'perusahaan', 'q'] as $fk)
                        @if(($filters[$fk] ?? '') !== '')<input type="hidden" name="{{ $fk }}" value="{{ $filters[$fk] }}"/>@endif
                     @endforeach
                     <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-3.5 py-2 text-xs font-semibold text-white hover:bg-primary-dim transition-colors">
                        <span class="material-symbols-outlined text-base">mail</span>
                        Kirim Email Awal
                     </button>
                  </form>
                  <form method="POST" action="{{ route('auto-banned.hsct-email.reminder') }}" class="inline">
                     @csrf
                     @foreach(['site', 'week', 'year', 'perusahaan', 'q'] as $fk)
                        @if(($filters[$fk] ?? '') !== '')<input type="hidden" name="{{ $fk }}" value="{{ $filters[$fk] }}"/>@endif
                     @endforeach
                     <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/25 bg-white px-3.5 py-2 text-xs font-semibold text-on-surface hover:border-primary/25 transition-colors">
                        <span class="material-symbols-outlined text-base">schedule_send</span>
                        Kirim Reminder
                     </button>
                  </form>
               </div>
               @endif
            </div>

            @if($hsctCampaign)
            <div class="px-5 sm:px-6 py-4 bg-[#fafbfc]/80 border-b border-outline-variant/10 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
               <div class="rounded-lg bg-white border border-outline-variant/15 px-3 py-2">
                  <p class="text-on-surface-variant">Status Campaign</p>
                  <p class="font-bold text-on-background mt-0.5">{{ $hsctCampaign['status']->label() }}</p>
               </div>
               <div class="rounded-lg bg-white border border-outline-variant/15 px-3 py-2">
                  <p class="text-on-surface-variant">Progress Banned</p>
                  <p class="font-bold text-on-background mt-0.5">{{ $hsctCampaign['confirmedItems'] }}/{{ $hsctCampaign['totalItems'] }} SID</p>
               </div>
               <div class="rounded-lg bg-white border border-outline-variant/15 px-3 py-2">
                  <p class="text-on-surface-variant">Belum Banned</p>
                  <p class="font-bold {{ $hsctCampaign['pendingItems'] > 0 ? 'text-amber-700' : 'text-emerald-700' }} mt-0.5">{{ $hsctCampaign['pendingItems'] }} SID</p>
               </div>
               <div class="rounded-lg bg-white border border-outline-variant/15 px-3 py-2">
                  <p class="text-on-surface-variant">Reminder Terkirim</p>
                  <p class="font-bold text-on-background mt-0.5">{{ $hsctCampaign['reminderCount'] }}x</p>
               </div>
            </div>
            @if($hsctCampaign['allBanned'])
            <div class="px-5 sm:px-6 py-3 bg-emerald-50/60 border-b border-emerald-100 text-xs text-emerald-800 flex items-center gap-2">
               <span class="material-symbols-outlined text-base">check_circle</span>
               Semua SID pada campaign ini sudah dikonfirmasi banned. Reminder otomatis dihentikan.
            </div>
            @endif
            @else
            <div class="px-5 sm:px-6 py-6 text-sm text-on-surface-variant">
               @if(!$hsctEmailAvailable)
               Jalankan migration untuk mengaktifkan fitur email HSECT.
               @else
               Belum ada campaign untuk periode ini. Email awal dikirim otomatis setiap Selasa pukul {{ $sendTime }} WITA.
               @endif
            </div>
            @endif
         </div>

         {{-- Riwayat email --}}
         <div class="ab-fade-in ab-fade-in-delay-2 ab-surface-card rounded-2xl overflow-hidden">
            <div class="px-5 sm:px-6 pt-5 pb-4 border-b border-outline-variant/10">
               <h2 class="font-headline font-semibold text-base text-on-background">Historis Pengiriman</h2>
               <p class="text-xs text-on-surface-variant mt-0.5">Log email awal & reminder yang sudah terkirim</p>
            </div>
            <div class="overflow-x-auto">
               <table class="ab-sheet-table w-full min-w-[900px] text-left">
                  <thead>
                     <tr>
                        <th>Waktu Kirim</th>
                        <th>Tipe</th>
                        <th>Reminder #</th>
                        <th>Periode</th>
                        <th>Total List</th>
                        <th>Sudah Banned</th>
                        <th>Belum Banned</th>
                        <th>Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse($hsctEmailHistory as $log)
                     <tr>
                        <td class="whitespace-nowrap">{{ $log->sent_at?->format('d M Y H:i') }}</td>
                        <td>{{ $log->email_type->label() }}</td>
                        <td class="tabular-nums">{{ $log->reminder_number }}</td>
                        <td>{{ $log->week }} {{ $log->iso_year }}</td>
                        <td class="tabular-nums">{{ $log->total_in_list }}</td>
                        <td class="tabular-nums text-emerald-700">{{ $log->confirmed_count }}</td>
                        <td class="tabular-nums {{ $log->pending_count > 0 ? 'text-amber-700 font-semibold' : '' }}">{{ $log->pending_count }}</td>
                        <td>
                           <span class="ab-badge {{ $log->status === 'sent' ? 'ab-badge--ok' : 'ab-badge--danger' }}">{{ ucfirst($log->status) }}</span>
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-sm text-on-surface-variant">
                           Belum ada email terkirim untuk filter periode ini.
                        </td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
      </div>

      {{-- Sidebar: pending items --}}
      <aside class="xl:col-span-4 space-y-4">
         <div class="ab-fade-in ab-fade-in-delay-2 ab-surface-card rounded-2xl p-5">
            <h3 class="font-headline font-semibold text-sm text-on-background mb-1">Alur Email</h3>
            <ol class="mt-3 space-y-3 text-xs text-on-surface-variant">
               <li class="flex gap-2">
                  <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary">1</span>
                  <span><strong class="text-on-background">Selasa</strong> — Email awal berisi semua SID Not Passed (tidak ada SAP)</span>
               </li>
               <li class="flex gap-2">
                  <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary/10 text-[10px] font-bold text-primary">2</span>
                  <span><strong class="text-on-background">Hari berikutnya</strong> — Reminder harian untuk SID yang belum banned</span>
               </li>
               <li class="flex gap-2">
                  <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-[10px] font-bold text-emerald-700">✓</span>
                  <span>Campaign selesai saat semua SID dikonfirmasi banned</span>
               </li>
            </ol>
         </div>

         <div class="ab-fade-in ab-fade-in-delay-3 ab-surface-card rounded-2xl p-5">
            <div class="flex items-center justify-between mb-4">
               <h3 class="font-headline font-semibold text-sm text-on-background">Belum Banned</h3>
               @if($hsctCampaign)
               <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">{{ $hsctPendingItems->count() }} SID</span>
               @endif
            </div>
            @if($hsctPendingItems->isNotEmpty())
            <div class="space-y-2 max-h-80 overflow-y-auto">
               @foreach($hsctPendingItems as $pendingItem)
               <form method="POST" action="{{ route('auto-banned.hsct-campaign-items.confirm', $pendingItem) }}" class="flex items-center justify-between gap-2 rounded-xl border border-amber-100 bg-amber-50/50 px-3 py-2.5">
                  @csrf
                  @foreach(['site', 'week', 'year', 'perusahaan', 'q'] as $fk)
                     @if(($filters[$fk] ?? '') !== '')<input type="hidden" name="{{ $fk }}" value="{{ $filters[$fk] }}"/>@endif
                  @endforeach
                  <div class="min-w-0">
                     <p class="text-xs font-mono font-semibold text-on-background truncate">{{ $pendingItem->sid }}</p>
                     @if($pendingItem->karyawan)
                     <p class="text-[10px] text-on-surface-variant truncate">{{ $pendingItem->karyawan }}</p>
                     @endif
                  </div>
                  <button type="submit" class="shrink-0 text-[10px] font-semibold text-emerald-700 hover:underline">Konfirmasi</button>
               </form>
               @endforeach
            </div>
            @elseif($hsctCampaign && $hsctCampaign['allBanned'])
            <p class="text-xs text-emerald-700 flex items-center gap-1.5">
               <span class="material-symbols-outlined text-sm">check_circle</span>
               Semua SID sudah banned
            </p>
            @else
            <p class="text-xs text-on-surface-variant">Tidak ada SID menunggu konfirmasi.</p>
            @endif
         </div>

         <div class="ab-fade-in ab-fade-in-delay-3 ab-surface-card rounded-2xl p-5">
            <h3 class="font-headline font-semibold text-sm text-on-background mb-3">Navigasi</h3>
            <a href="{{ route('auto-banned.index') }}" class="group flex w-full items-center gap-3 rounded-xl border border-transparent bg-[#f8fafc] px-4 py-3 transition-all hover:border-primary/15 hover:bg-primary/[0.04]">
               <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white shadow-sm text-primary">
                  <span class="material-symbols-outlined text-xl">dashboard</span>
               </span>
               <div class="min-w-0">
                  <p class="text-sm font-semibold text-on-background">Overview</p>
                  <p class="text-[11px] text-on-surface-variant">Monitoring lifecycle banned</p>
               </div>
               <span class="material-symbols-outlined text-on-surface-variant/40 ml-auto text-lg group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
            </a>
         </div>
      </aside>
   </div>
</div>
@endsection
