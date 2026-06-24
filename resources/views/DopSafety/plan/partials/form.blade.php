@php
   $formAction = $formAction ?? route('dop-safety.plan.store');
   $formMethod = $formMethod ?? 'POST';
   $submitLabel = $submitLabel ?? 'Simpan DOP';
   $oldItems = old('items', $defaults['items'] ?? []);
@endphp

<form action="{{ $formAction }}" method="POST" class="space-y-6">
   @csrf
   @if($formMethod !== 'POST')
   @method($formMethod)
   @endif

   <div class="ds-surface-card rounded-2xl p-6">
      <h2 class="font-headline font-bold text-base mb-4">Header Dokumen</h2>
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Site *</label>
            <input type="text" name="site" value="{{ old('site', $defaults['site'] ?? '') }}" required class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm">
         </div>
         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Hari/Tanggal *</label>
            <input type="date" name="plan_date" value="{{ old('plan_date', $defaults['plan_date'] ?? '') }}" required class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm">
         </div>
         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Shift *</label>
            <select name="shift" required class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm">
               @foreach($shiftOptions as $val => $label)
               <option value="{{ $val }}" @selected((int)old('shift', $defaults['shift'] ?? 1) === (int)$val)>{{ $label }}</option>
               @endforeach
            </select>
         </div>
         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Status *</label>
            <select name="status" required class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm">
               @foreach($statusOptions as $opt)
               <option value="{{ $opt['value'] }}" @selected(old('status', $defaults['status'] ?? 'draft') === $opt['value'])>{{ $opt['label'] }}</option>
               @endforeach
            </select>
         </div>
      </div>
   </div>

   <div class="ds-surface-card rounded-2xl p-6">
      <div class="flex items-center justify-between mb-4">
         <h2 class="font-headline font-bold text-base">Item Pekerjaan</h2>
         <button type="button" id="ds-add-item" class="inline-flex items-center gap-1 rounded-lg bg-primary/10 text-primary px-3 py-1.5 text-xs font-bold">
            <span class="material-symbols-outlined text-sm">add</span> Tambah Baris
         </button>
      </div>

      <div id="ds-items-container" class="space-y-4">
         @foreach($oldItems as $index => $item)
         @include('DopSafety.plan.partials.item-row', ['index' => $index, 'item' => $item])
         @endforeach
      </div>
   </div>

   <div class="ds-surface-card rounded-2xl p-6">
      <h2 class="font-headline font-bold text-base mb-4">Blok Otorisasi Dokumen</h2>
      <div class="grid md:grid-cols-2 gap-4">
         <div class="md:col-span-2">
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Lokasi & Tanggal Pembuatan</label>
            <input type="text" name="auth_location_date" value="{{ old('auth_location_date', $defaults['auth_location_date'] ?? '') }}" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm">
         </div>
         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Dibuat Oleh — Nama</label>
            <input type="text" name="created_by_name" value="{{ old('created_by_name', $defaults['created_by_name'] ?? '') }}" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm">
         </div>
         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Dibuat Oleh — Jabatan</label>
            <input type="text" name="created_by_position" value="{{ old('created_by_position', $defaults['created_by_position'] ?? '') }}" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm">
         </div>
         @foreach([1, 2, 3] as $n)
         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Mengetahui {{ $n }} — Nama</label>
            <input type="text" name="acknowledged_{{ $n }}_name" value="{{ old('acknowledged_'.$n.'_name', $defaults['acknowledged_'.$n.'_name'] ?? '') }}" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm">
         </div>
         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Mengetahui {{ $n }} — Jabatan</label>
            <input type="text" name="acknowledged_{{ $n }}_position" value="{{ old('acknowledged_'.$n.'_position', $defaults['acknowledged_'.$n.'_position'] ?? '') }}" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2 text-sm">
         </div>
         @endforeach
      </div>
   </div>

   <div class="flex flex-wrap gap-3">
      <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white hover:opacity-95">{{ $submitLabel }}</button>
      <a href="{{ route('dop-safety.plan.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 px-5 py-2.5 text-sm font-bold text-on-surface-variant hover:bg-white">Batal</a>
   </div>
</form>

<template id="ds-item-row-template">
@include('DopSafety.plan.partials.item-row', ['index' => '__INDEX__', 'item' => [
   'section_name' => config('dop_safety.sections.0'),
   'unit_code' => '',
   'unit_category' => 'TRACK',
   'location' => '',
   'job_detail' => '',
   'work_permit' => 'N/A',
   'tools' => '',
   'workers' => '',
   'cctv' => '',
   'group_leader' => '',
   'section_head' => '',
   'she_leader' => '',
   'dept_head' => '',
   'pja_bc' => '',
]])
</template>

@push('scripts')
<script>
(function () {
   const container = document.getElementById('ds-items-container');
   const template = document.getElementById('ds-item-row-template');
   const addBtn = document.getElementById('ds-add-item');
   if (!container || !template || !addBtn) return;

   let nextIndex = container.querySelectorAll('.ds-item-row').length;

   addBtn.addEventListener('click', function () {
      const html = template.innerHTML.replace(/__INDEX__/g, String(nextIndex));
      const wrapper = document.createElement('div');
      wrapper.innerHTML = html.trim();
      container.appendChild(wrapper.firstElementChild);
      nextIndex++;
   });

   container.addEventListener('click', function (e) {
      const btn = e.target.closest('.ds-remove-item');
      if (!btn) return;
      const rows = container.querySelectorAll('.ds-item-row');
      if (rows.length <= 1) {
         alert('Minimal satu item pekerjaan.');
         return;
      }
      btn.closest('.ds-item-row')?.remove();
   });
})();
</script>
@endpush
