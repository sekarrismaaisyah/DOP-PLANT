@php
   $formAction = $formAction ?? route('dop-safety.ojii.store');
   $formMethod = $formMethod ?? 'POST';
   $submitLabel = $submitLabel ?? 'Simpan DOP';
   $oldItems = old('items', $defaults['items'] ?? []);
   $tableStructure = $tableStructure ?? config('dop_safety.table_structure', []);
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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
            <a href="{{ route('dop-safety.download-worker-template') }}" class="inline-flex items-center gap-1 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 px-3 py-1.5 text-xs font-bold hover:bg-indigo-100">
               <span class="material-symbols-outlined text-sm">group</span> Template Mekanik
            </a>
            <a href="{{ route('dop-safety.oji.template', ['scope' => 'items']) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white text-gray-700 px-3 py-1.5 text-xs font-bold hover:bg-gray-50">
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
               @include('DopSafety.ojii.partials.table-head', [
                  'tableStructure' => $tableStructure,
                  'shiftOptions' => $shiftOptions,
                  'defaults' => $defaults,
               ])
            </thead>
            <tbody id="ds-items-container">
               @foreach($oldItems as $index => $item)
               @include('DopSafety.ojii.partials.item-row', ['index' => $index, 'item' => $item, 'sectionOptions' => $sectionOptions])
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
      <a href="{{ route('dop-safety.oji.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 px-5 py-2.5 text-sm font-bold text-on-surface-variant hover:bg-white">Batal</a>
   </div>
</form>

<!-- Reject Modal -->
<div
    id="rejectModal"
    class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">

        <h2 class="text-lg font-bold mb-3">
            Reject Approval
        </h2>

        <p class="text-sm text-gray-600 mb-3">
            Masukkan alasan reject.
        </p>

        <textarea
            id="rejectReason"
            rows="5"
            class="w-full border rounded-lg p-2 text-sm"
            placeholder="Masukkan alasan reject..."></textarea>

        <div class="flex justify-end gap-2 mt-4">

            <button
                type="button"
                id="cancelReject"
                class="px-4 py-2 rounded bg-gray-300">
                Batal
            </button>

            <button
                type="button"
                id="submitReject"
                class="px-4 py-2 rounded bg-red-600 text-white">
                Reject
            </button>

        </div>

    </div>

</div>

<template id="ds-item-row-template">
@include('DopSafety.ojii.partials.item-row', ['index' => '__INDEX__', 'item' => [
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
<div id="approvalModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

    <div class="bg-white rounded-xl shadow-xl w-[420px] p-6">

        <h3 class="text-lg font-bold mb-3">
            Konfirmasi Approval
        </h3>

        <p class="text-sm text-gray-600 mb-6">
            Apakah Anda yakin ingin menyetujui data ini?
        </p>

        <div class="flex justify-end gap-2">
            <button
                id="cancelApproval"
                type="button"
                class="px-4 py-2 rounded border">
                Batal
            </button>

            <button
                id="confirmApproval"
                type="button"
                class="px-4 py-2 rounded bg-green-600 text-white">
                Ya, Approve
            </button>
        </div>

    </div>
</div>

<div id="mechanicViewModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[85vh] flex flex-col">
        
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl">
            <h3 class="text-base font-bold text-gray-800">
                Daftar Pekerja Mekanik
            </h3>
            <button type="button" id="closeMechanicModal" class="text-gray-400 hover:text-red-600 text-xl font-bold px-2">
                &times;
            </button>
        </div>

        <div class="p-4 overflow-y-auto">
            <table class="w-full text-sm border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-200 px-3 py-2 text-center w-10">No.</th>
                        <th class="border border-gray-200 px-3 py-2 text-left">NRP</th>
                        <th class="border border-gray-200 px-3 py-2 text-left">Nama</th>
                        <th class="border border-gray-200 px-3 py-2 text-left">Jabatan</th>
                    </tr>
                </thead>
                <tbody id="mechanicTableBody">
                    </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 flex justify-end">
            <button type="button" id="closeMechanicModalBtn" class="px-4 py-2 text-sm rounded border bg-white hover:bg-gray-100 font-bold text-gray-700">
                Tutup
            </button>
        </div>

    </div>
</div>

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
   const rejectUrl = @json(route('dop-safety.oji.item.reject', ['item' => '__ID__']));
   const approveUrl = @json(
    route('dop-safety.oji.items.approve', ['item' => '__ID__'])
);

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
   const modal = document.getElementById('approvalModal');
const confirmBtn = document.getElementById('confirmApproval');
const cancelBtn = document.getElementById('cancelApproval');

let currentButton = null;

container.addEventListener('click', function(e){

    const btn = e.target.closest('.approve-btn');

    if(!btn) return;

    currentButton = btn;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

});

cancelBtn.addEventListener('click', function(){

    modal.classList.add('hidden');
    modal.classList.remove('flex');

});

confirmBtn.addEventListener('click', function () {

    if (!currentButton) return;

    const itemId = currentButton.dataset.id;

    fetch(
        approveUrl.replace('__ID__', itemId),
        {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        }
    )
    .then(response => response.json())
    .then(data => {

        if (!data.success) {
            alert('Approve gagal');
            return;
        }

        currentButton.dataset.status = data.status;

        switch (data.status) {

            case 'waiting_safety':
                currentButton.innerHTML = 'Waiting Approval Dept Head Safety';
                break;

            case 'waiting_pm':
                currentButton.innerHTML = 'Waiting Approval PM';
                break;

            case 'done':
                currentButton.innerHTML = 'Approved';
                currentButton.disabled = true;
                currentButton.classList.remove('bg-green-600');
                currentButton.classList.add('bg-green-800');
                break;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');

    })
    .catch(() => {
        alert('Terjadi kesalahan.');
    });

});

let currentRejectButton = null;

document.addEventListener('click', function (e) {

    const btn = e.target.closest('.reject-btn');

    if (!btn) return;

    currentRejectButton = btn;

    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');

});

document
.getElementById('cancelReject')
.addEventListener('click', function () {

    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');

    document.getElementById('rejectReason').value = '';

});

document
.getElementById('submitReject')
.addEventListener('click', function () {

    const reason = document.getElementById('rejectReason').value.trim();

    if (reason === '') {
        alert('Alasan reject wajib diisi.');
        return;
    }

    const itemId = currentRejectButton.dataset.id;

    fetch(
        rejectUrl.replace('__ID__', itemId),
        {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                reason: reason
            })
        }
    )
   .then(response => response.json())
   .then(data => {

      if (!data.success) {
         alert('Reject gagal.');
         return;
      }

      currentRejectButton.innerHTML = 'Rejected';
      currentRejectButton.disabled = true;

      currentRejectButton.classList.remove('bg-red-600');
      currentRejectButton.classList.add('bg-gray-500');

      document.getElementById('rejectModal').classList.add('hidden');
      document.getElementById('rejectModal').classList.remove('flex');

      document.getElementById('rejectReason').value = '';

      // Reload setelah 300ms
      setTimeout(() => {
         window.location.reload();
      }, 300);

   })
   .catch(() => {
      alert('Terjadi kesalahan.');
   });

});

window.handleRowWorkerUpload = function(input) {
    const itemId = input.getAttribute('data-id');
    if (!input.files || input.files[0] == null) return;

    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('file_excel', input.files[0]);
    formData.append('_token', '{{ csrf_token() }}');

    const btn = input.nextElementSibling;
    const originalText = btn.innerText;
    btn.disabled = true;
    btn.innerText = 'Uploading...';
    btn.classList.add('opacity-50');

    fetch("{{ route('dop-safety.upload-item-workers') }}", {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload(); // Reload untuk memperbarui angka jumlah mekanik
        } else {
            alert('Gagal: ' + data.message);
            btn.disabled = false;
            btn.innerText = originalText;
            btn.classList.remove('opacity-50');
        }
        input.value = '';
    })
    .catch(error => {
        console.error(error);
        alert('Terjadi kesalahan koneksi sistem.');
        input.value = '';
        btn.disabled = false;
        btn.innerText = originalText;
        btn.classList.remove('opacity-50');
    });
};

})();

// --- LOGIC MODAL VIEW MEKANIK ---
const mechanicModal = document.getElementById('mechanicViewModal');
const mechanicTableBody = document.getElementById('mechanicTableBody');

// Fungsi menutup modal
function hideMechanicModal() {
    mechanicModal.classList.add('hidden');
    mechanicModal.classList.remove('flex');
}

document.getElementById('closeMechanicModal').addEventListener('click', hideMechanicModal);
document.getElementById('closeMechanicModalBtn').addEventListener('click', hideMechanicModal);

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-mechanic-btn');
    if (!btn) return;

    const nrps = btn.getAttribute('data-nrps').split(';');
    const names = btn.getAttribute('data-names').split(';');
    const positions = btn.getAttribute('data-positions').split(';');

    mechanicTableBody.innerHTML = '';
    let hasData = false;

    for (let i = 0; i < names.length; i++) {
        const nrp = nrps[i] ? nrps[i].trim() : '-';
        const name = names[i] ? names[i].trim() : '-';
        const position = positions[i] ? positions[i].trim() : '-';

        if (name === '-' && nrp === '-') continue;

        hasData = true;
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="border border-gray-200 px-3 py-2 text-center">${i + 1}</td>
            <td class="border border-gray-200 px-3 py-2 text-gray-800 font-mono">${nrp}</td>
            <td class="border border-gray-200 px-3 py-2 font-semibold">${name}</td>
            <td class="border border-gray-200 px-3 py-2 text-xs uppercase tracking-wider">${position}</td>
        `;
        mechanicTableBody.appendChild(row);
    }

    if (!hasData) {
        mechanicTableBody.innerHTML = `<tr><td colspan="4" class="text-center py-6 text-gray-500 italic">Belum ada data pekerja mekanik.</td></tr>`;
    }

    mechanicModal.classList.remove('hidden');
    mechanicModal.classList.add('flex');
});
</script>
@endpush
