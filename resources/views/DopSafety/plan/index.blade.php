@extends('DopSafety.layouts.app')

@section('title', 'Pengajuan DOP — DOP Safety')

@push('head')
@include('DopSafety.partials.styles')
@endpush

@section('content')
@include('DopSafety.partials.page-header', [
   'title' => 'Bagian B — Pengajuan & Approval DOP Daily',
   'subtitle' => 'GL mengajukan DOP H-1. Approval oleh Dept. Head Plant, Dept. Head SHE, dan Supt Safety BC.',
   'breadcrumb' => 'Pengajuan DOP',
])

@if($errors->any())
<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 mb-4">
   <ul class="list-disc pl-4 space-y-1">
      @foreach($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
   </ul>
</div>
@endif

@if(session('import_header_errors') && count(session('import_header_errors')) > 0)
<div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 mb-4">
   <p class="font-bold mb-2">Upload ditolak — kolom tidak sesuai template</p>
   <ul class="list-disc pl-4 space-y-1">
      @foreach(session('import_header_errors') as $hErr)
      <li>{{ $hErr }}</li>
      @endforeach
   </ul>
</div>
@endif

@if(session('import_errors') && count(session('import_errors')) > 0)
<div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 mb-4">
   <p class="font-bold mb-2">Catatan import:</p>
   <ul class="list-disc pl-4 space-y-1">
      @foreach(array_slice(session('import_errors'), 0, 15) as $err)
      <li>{{ $err }}</li>
      @endforeach
   </ul>
</div>
@endif

<div class="rounded-xl border border-error/20 bg-red-50/60 px-4 py-3 text-sm font-semibold text-error mb-6 flex items-center gap-2">
   <span class="material-symbols-outlined">warning</span>
   {{ $disclaimer }}
</div>

<div class="flex flex-wrap gap-2 mb-6">
   @foreach($approvers as $approver)
   <span class="ds-badge">{{ $approver }}</span>
   @endforeach
</div>

<div class="ds-surface-card rounded-2xl p-5 mb-6">
   <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div class="flex flex-wrap gap-2">
         <a href="{{ route('dop-safety.plan.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-bold text-white hover:opacity-95">
            <span class="material-symbols-outlined text-sm">add</span>
            Input DOP Baru
         </a>
         <a href="{{ route('dop-safety.plan.template', ['scope' => 'document']) }}" class="inline-flex items-center gap-2 rounded-xl border border-primary/20 bg-white px-4 py-2.5 text-sm font-bold text-primary hover:bg-primary/5">
            <span class="material-symbols-outlined text-sm">download</span>
            Download Template Excel
         </a>
      </div>
      <form action="{{ route('dop-safety.plan.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-end gap-2">
         @csrf
         <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Upload Excel</label>
            <input type="file" name="excel_file" accept=".xlsx,.xls" required class="text-sm rounded-xl border border-outline-variant/30 px-3 py-2 bg-white max-w-xs">
         </div>
         <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-secondary px-4 py-2.5 text-sm font-bold text-white hover:opacity-95">
            <span class="material-symbols-outlined text-sm">upload</span>
            Import
         </button>
      </form>
   </div>
</div>

<form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
   <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari unit / pekerjaan..." class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm col-span-2 md:col-span-1">
   <select name="shift" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm">
      <option value="">Semua Shift</option>
      @foreach(config('dop_safety.shifts') as $val => $label)
      <option value="{{ $val }}" @selected(request('shift') == (string)$val)>{{ $label }}</option>
      @endforeach
   </select>
   <select name="status" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm">
      <option value="">Semua Status</option>
      @foreach($statusOptions as $opt)
      <option value="{{ $opt['value'] }}" @selected(request('status') === $opt['value'])>{{ $opt['label'] }}</option>
      @endforeach
   </select>
   <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm">
   <button type="submit" class="rounded-xl bg-primary text-white text-sm font-bold px-4 py-2">Filter</button>
</form>

<div class="grid lg:grid-cols-4 gap-6">
   <div class="lg:col-span-1 ds-surface-card rounded-2xl p-6">
      <h2 class="font-headline font-bold text-base mb-4">Ketentuan</h2>
      <ol class="space-y-3">
         @foreach($rules as $rule)
         <li class="flex gap-3 text-sm">
            <span class="font-bold text-primary shrink-0">{{ $rule['no'] }}.</span>
            <span class="text-on-surface-variant">{{ $rule['rule'] }}</span>
         </li>
         @endforeach
      </ol>
   </div>

   <div class="lg:col-span-3 ds-surface-card rounded-2xl p-6">
      <h2 class="font-headline font-bold text-base mb-4">Daftar DOP Daily</h2>
      <div class="overflow-x-auto">
         <table class="ds-table w-full text-sm">
            <thead>
               <tr class="border-b border-outline-variant/20">
                  <th class="text-left py-2">Tanggal</th>
                  <th class="text-left py-2">Site</th>
                  <th class="text-left py-2">Shift</th>
                  <th class="text-left py-2">Item</th>
                  <th class="text-left py-2">Status</th>
                  <th class="text-left py-2">Aksi</th>
               </tr>
            </thead>
            <tbody>
               @forelse($plans as $plan)
               <tr class="border-b border-outline-variant/10">
                  <td class="py-3">{{ $plan->plan_date->format('d M Y') }}</td>
                  <td class="py-3 font-semibold">{{ $plan->site }}</td>
                  <td class="py-3">{{ $plan->shiftLabel() }}</td>
                  <td class="py-3">{{ $plan->items_count }} pekerjaan</td>
                  <td class="py-3"><span class="{{ $plan->status->badgeClass() }}">{{ $plan->status->label() }}</span></td>
                  <td class="py-3">
                     <div class="flex flex-wrap gap-2">
                        <a href="{{ route('dop-safety.plan.show', $plan) }}" class="text-primary text-xs font-bold hover:underline">Lihat</a>
                        <a href="{{ route('dop-safety.plan.edit', $plan) }}" class="text-on-surface-variant text-xs font-bold hover:underline">Edit</a>
                        <form action="{{ route('dop-safety.plan.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Hapus DOP ini?')">
                           @csrf
                           @method('DELETE')
                           <button type="submit" class="text-error text-xs font-bold hover:underline">Hapus</button>
                        </form>
                     </div>
                  </td>
               </tr>
               @empty
               <tr>
                  <td colspan="6" class="py-8 text-center text-on-surface-variant">Belum ada data DOP. Input manual atau import Excel.</td>
               </tr>
               @endforelse
            </tbody>
         </table>
      </div>
      @if($plans->hasPages())
      <div class="mt-4">{{ $plans->links() }}</div>
      @endif
   </div>
</div>
@endsection
