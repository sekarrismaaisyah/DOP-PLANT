@php
   $formAction = $formAction ?? route('dop-safety.plan.store');
   $formMethod = $formMethod ?? 'POST';
   $submitLabel = $submitLabel ?? 'Simpan DOP';
   $oldItems = old('items', $defaults['items'] ?? []);
   $tableStructure = $tableStructure ?? config('dop_safety.table_structure', []);
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
      <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
         <h2 class="font-headline font-bold text-base">Item Pekerjaan</h2>
         <div class="flex flex-wrap items-end gap-2">
            <a href="{{ route('dop-safety.plan.template', ['scope' => 'items']) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white text-gray-700 px-3 py-1.5 text-xs font-bold hover:bg-gray-50">
               <span class="material-symbols-outlined text-sm">download</span> Template Excel
            </a>
            <div>
               <label class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-1">Upload Excel Item</label>
               <input type="file" id="ds-items-excel-file" accept=".xlsx,.xls" class="text-xs rounded-lg border border-gray-200 px-2 py-1.5 bg-white max-w-[200px]">
            </div>
            <button type="button" id="ds-items-excel-upload" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white text-gray-700 px-3 py-1.5 text-xs font-bold hover:bg-gray-50">
               <span class="material-symbols-outlined text-sm">upload</span> Muat ke Tabel
            </button>
            <button type="button" id="ds-add-item" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white text-gray-700 px-3 py-1.5 text-xs font-bold hover:bg-gray-50">
               <span class="material-symbols-outlined text-sm">add</span> Tambah Baris
            </button>
         </div>
      </div>

      <div id="ds-items-import-alert" class="hidden rounded-xl border px-4 py-3 text-sm mb-4"></div>

      <div class="overflow-x-auto">
         <table class="ds-table ds-plan-table w-full text-sm border-collapse min-w-[1400px]">
            <thead>
               @include('DopSafety.plan.partials.table-head', [
                  'tableStructure' => $tableStructure,
                  'shiftOptions' => $shiftOptions,
                  'defaults' => $defaults,
               ])
            </thead>
            <tbody id="ds-items-container">
               @foreach($oldItems as $index => $item)
               @include('DopSafety.plan.partials.item-row', ['index' => $index, 'item' => $item, 'sectionOptions' => $sectionOptions])
               @endforeach
            </tbody>
         </table>
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
   'location' => '',
   'job_detail' => '',
   'work_permit' => 'N/A',
   'tools' => '',
   'worker_names' => '',
   'worker_sids' => '',
   'cctv' => '',
   'group_leader' => '',
   'group_leader_sid' => '',
   'section_head' => '',
   'section_head_sid' => '',
   'she_leader' => '',
   'she_leader_sid' => '',
   'dept_head' => '',
   'dept_head_sid' => '',
   'pja_bc' => '',
], 'sectionOptions' => $sectionOptions])
</template>

@push('scripts')
<script>
(function () {
   const container = document.getElementById('ds-items-container');
   const template = document.getElementById('ds-item-row-template');
   const addBtn = document.getElementById('ds-add-item');
   const uploadBtn = document.getElementById('ds-items-excel-upload');
   const fileInput = document.getElementById('ds-items-excel-file');
   const alertBox = document.getElementById('ds-items-import-alert');
   const importUrl = @json(route('dop-safety.plan.import-items'));
   const csrfToken = @json(csrf_token());

   if (!container || !template || !addBtn) return;

   let nextIndex = container.querySelectorAll('.ds-item-row').length;

   function renumberRows() {
      container.querySelectorAll('.ds-item-row').forEach(function (row, idx) {
         const noCell = row.querySelector('td:first-child');
         if (noCell) noCell.textContent = String(idx + 1);
      });
   }

   function showImportAlert(type, message, errors) {
      if (!alertBox) return;
      alertBox.classList.remove('hidden', 'border-red-200', 'bg-red-50', 'text-red-900', 'border-amber-200', 'bg-amber-50', 'text-amber-900', 'border-green-200', 'bg-green-50', 'text-green-900');
      if (type === 'error') {
         alertBox.classList.add('border-red-200', 'bg-red-50', 'text-red-900');
      } else if (type === 'warning') {
         alertBox.classList.add('border-amber-200', 'bg-amber-50', 'text-amber-900');
      } else {
         alertBox.classList.add('border-green-200', 'bg-green-50', 'text-green-900');
      }
      let html = '<p class="font-bold">' + message + '</p>';
      if (errors && errors.length) {
         html += '<ul class="list-disc pl-4 mt-2 space-y-1">';
         errors.slice(0, 10).forEach(function (err) {
            html += '<li>' + err + '</li>';
         });
         html += '</ul>';
      }
      alertBox.innerHTML = html;
   }

   function appendItemRow(item, index) {
      const html = template.innerHTML.replace(/__INDEX__/g, String(index));
      const wrapper = document.createElement('tbody');
      wrapper.innerHTML = html.trim();
      const row = wrapper.firstElementChild;
      if (!row) return;

      Object.keys(item).forEach(function (key) {
         const field = row.querySelector('[name="items[' + index + '][' + key + ']"]');
         if (field) {
            field.value = item[key] ?? '';
         }
      });

      container.appendChild(row);
   }

   function replaceItems(items) {
      container.innerHTML = '';
      items.forEach(function (item, index) {
         appendItemRow(item, index);
      });
      nextIndex = items.length;
      renumberRows();
   }

   addBtn.addEventListener('click', function () {
      appendItemRow({
         section_name: @json(config('dop_safety.sections.0')),
         unit_code: '',
         location: '',
         job_detail: '',
         work_permit: 'N/A',
         tools: '',
         worker_names: '',
         worker_sids: '',
         cctv: '',
         group_leader: '',
         group_leader_sid: '',
         section_head: '',
         section_head_sid: '',
         she_leader: '',
         she_leader_sid: '',
         dept_head: '',
         dept_head_sid: '',
         pja_bc: '',
      }, nextIndex);
      nextIndex++;
      renumberRows();
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
      renumberRows();
   });

   if (uploadBtn && fileInput) {
      uploadBtn.addEventListener('click', function () {
         const file = fileInput.files && fileInput.files[0];
         if (!file) {
            showImportAlert('error', 'Pilih file Excel item pekerjaan terlebih dahulu.', []);
            return;
         }

         uploadBtn.disabled = true;
         uploadBtn.classList.add('opacity-60');

         const formData = new FormData();
         formData.append('excel_file', file);
         formData.append('_token', csrfToken);

         fetch(importUrl, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
         })
            .then(function (response) {
               return response.json().then(function (data) {
                  return { ok: response.ok, data: data };
               });
            })
            .then(function (result) {
               if (!result.ok || !result.data.success) {
                  showImportAlert('error', result.data.message || 'Import gagal.', result.data.errors || []);
                  return;
               }

               replaceItems(result.data.items || []);
               if (result.data.errors && result.data.errors.length) {
                  showImportAlert('warning', result.data.message, result.data.errors);
               } else {
                  showImportAlert('success', result.data.message, []);
               }
               fileInput.value = '';
            })
            .catch(function () {
               showImportAlert('error', 'Gagal mengupload file. Periksa koneksi atau format file.', []);
            })
            .finally(function () {
               uploadBtn.disabled = false;
               uploadBtn.classList.remove('opacity-60');
            });
      });
   }
})();
</script>
@endpush
