@php
   $thClass = 'px-6 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant';
   $tdClass = 'px-6 py-4 text-sm text-on-surface';
@endphp

<div id="plv-batas-lv-panel">
   <table class="w-full text-left">
      <thead class="border-b border-outline-variant/20 bg-[#f8fafc]">
         <tr>
            <th class="{{ $thClass }}">No</th>
            <th class="{{ $thClass }}">Site</th>
            <th class="{{ $thClass }}">Lokasi</th>
            <th class="{{ $thClass }}">Detail Lokasi</th>
            <th class="{{ $thClass }}">Batas LV</th>
            <th class="{{ $thClass }} w-32">Aksi</th>
         </tr>
      </thead>
      <tbody id="plv-batas-lv-tbody" class="divide-y divide-outline-variant/10">
         <tr>
            <td colspan="6" class="px-6 py-12 text-center text-sm text-on-surface-variant">Memuat data…</td>
         </tr>
      </tbody>
   </table>

   <div id="plv-batas-lv-pagination" class="flex flex-col gap-3 border-t border-outline-variant/20 px-6 py-4 sm:flex-row sm:items-center sm:justify-between hidden">
      <p id="plv-batas-lv-info" class="text-xs font-medium text-on-surface-variant"></p>
      <div id="plv-batas-lv-pages" class="flex flex-wrap items-center gap-2"></div>
   </div>
</div>

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
   var panel = document.getElementById('plv-batas-lv-panel');
   if (!panel) return;

   var tbody = document.getElementById('plv-batas-lv-tbody');
   var paginationWrap = document.getElementById('plv-batas-lv-pagination');
   var infoEl = document.getElementById('plv-batas-lv-info');
   var pagesEl = document.getElementById('plv-batas-lv-pages');
   var modal = document.getElementById('plv-batas-lv-modal');
   var form = document.getElementById('plv-batas-lv-form');
   var modalTitle = document.getElementById('plv-batas-lv-modal-title');
   var formErrors = document.getElementById('plv-batas-lv-form-errors');
   var idInput = document.getElementById('plv-batas-lv-id');
   var addBtn = document.getElementById('plv-batas-lv-add-btn');
   var searchForm = document.getElementById('plv-master-search-form');
   var searchInput = document.getElementById('plv-master-search-input');

   var dataUrl = @json(route('pembatasan-lv.master-data.batas-lv-per-lokasi.data'));
   var storeUrl = @json(route('pembatasan-lv.master-data.batas-lv-per-lokasi.store'));
   var showUrlBase = @json(url('/pembatasan-lv/master-data/batas-lv-per-lokasi'));
   var csrf = @json(csrf_token());

   var state = { page: 1, perPage: 10, q: @json($q) };

   function escapeHtml(value) {
      return String(value ?? '')
         .replace(/&/g, '&amp;')
         .replace(/</g, '&lt;')
         .replace(/>/g, '&gt;')
         .replace(/"/g, '&quot;');
   }

   function tdClass() {
      return 'px-6 py-4 text-sm text-on-surface';
   }

   function renderRows(rows, meta) {
      if (!rows.length) {
         tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-sm text-on-surface-variant">Belum ada data batas LV per lokasi.</td></tr>';
         paginationWrap.classList.add('hidden');
         return;
      }

      tbody.innerHTML = rows.map(function (row, index) {
         var no = (meta.from || 1) + index;
         return '<tr class="transition-colors hover:bg-[#f8fafc]">' +
            '<td class="' + tdClass() + ' tabular-nums text-on-surface-variant">' + no + '</td>' +
            '<td class="' + tdClass() + ' font-bold">' + escapeHtml(row.site) + '</td>' +
            '<td class="' + tdClass() + ' font-semibold">' + escapeHtml(row.lokasi) + '</td>' +
            '<td class="' + tdClass() + ' text-on-surface-variant">' + (escapeHtml(row.detail_lokasi) || '—') + '</td>' +
            '<td class="' + tdClass() + ' font-bold tabular-nums">' + Number(row.batas_lv).toLocaleString('id-ID') + '</td>' +
            '<td class="' + tdClass() + '">' +
               '<div class="flex items-center gap-1">' +
                  '<button type="button" class="rounded-lg p-2 text-primary hover:bg-primary/10" title="Edit" data-plv-batas-lv-edit="' + row.id + '">' +
                     '<span class="material-symbols-outlined text-lg">edit</span>' +
                  '</button>' +
                  '<button type="button" class="rounded-lg p-2 text-error hover:bg-error/10" title="Hapus" data-plv-batas-lv-delete="' + row.id + '">' +
                     '<span class="material-symbols-outlined text-lg">delete</span>' +
                  '</button>' +
               '</div>' +
            '</td>' +
         '</tr>';
      }).join('');

      infoEl.textContent = 'Menampilkan ' + meta.from + '–' + meta.to + ' dari ' + meta.total + ' data';
      renderPagination(meta);
      paginationWrap.classList.remove('hidden');
   }

   function renderPagination(meta) {
      pagesEl.innerHTML = '';
      if (meta.last_page <= 1) return;

      function pageBtn(label, page, disabled, active) {
         var btn = document.createElement('button');
         btn.type = 'button';
         btn.textContent = label;
         btn.className = active
            ? 'rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white'
            : 'rounded-lg border border-outline-variant/30 bg-white px-3 py-1.5 text-xs font-bold text-on-surface hover:bg-surface-container-high';
         if (disabled) {
            btn.disabled = true;
            btn.className += ' opacity-50 cursor-not-allowed';
         } else {
            btn.addEventListener('click', function () {
               state.page = page;
               loadData();
            });
         }
         pagesEl.appendChild(btn);
      }

      pageBtn('‹', meta.current_page - 1, meta.current_page <= 1, false);
      for (var p = 1; p <= meta.last_page; p++) {
         if (meta.last_page > 7 && Math.abs(p - meta.current_page) > 2 && p !== 1 && p !== meta.last_page) {
            if (p === 2 || p === meta.last_page - 1) {
               var dots = document.createElement('span');
               dots.textContent = '…';
               dots.className = 'px-1 text-xs text-on-surface-variant';
               pagesEl.appendChild(dots);
            }
            continue;
         }
         pageBtn(String(p), p, false, p === meta.current_page);
      }
      pageBtn('›', meta.current_page + 1, meta.current_page >= meta.last_page, false);
   }

   function loadData() {
      tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-sm text-on-surface-variant">Memuat data…</td></tr>';
      var url = new URL(dataUrl, window.location.origin);
      url.searchParams.set('page', state.page);
      url.searchParams.set('per_page', state.perPage);
      if (state.q) url.searchParams.set('q', state.q);

      fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
         .then(function (res) { return res.json(); })
         .then(function (json) { renderRows(json.data || [], json.meta || {}); })
         .catch(function () {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-sm text-error">Gagal memuat data.</td></tr>';
         });
   }

   function openModal(mode, payload) {
      if (!modal || !form) return;
      form.reset();
      formErrors.classList.add('hidden');
      formErrors.innerHTML = '';
      idInput.value = payload && payload.id ? payload.id : '';
      modalTitle.textContent = mode === 'edit' ? 'Edit Batas LV' : 'Tambah Batas LV';

      if (payload) {
         document.getElementById('plv-batas-lv-site').value = payload.site || '';
         document.getElementById('plv-batas-lv-lokasi').value = payload.lokasi || '';
         document.getElementById('plv-batas-lv-detail').value = payload.detail_lokasi || '';
         document.getElementById('plv-batas-lv-batas').value = payload.batas_lv ?? 0;
      }

      modal.classList.remove('hidden');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('overflow-hidden');
   }

   function closeModal() {
      if (!modal) return;
      modal.classList.add('hidden');
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('overflow-hidden');
   }

   function showFormErrors(errors) {
      var messages = [];
      if (typeof errors === 'object') {
         Object.keys(errors).forEach(function (key) {
            (errors[key] || []).forEach(function (msg) { messages.push(msg); });
         });
      }
      formErrors.innerHTML = messages.map(function (m) { return '<div>' + escapeHtml(m) + '</div>'; }).join('');
      formErrors.classList.toggle('hidden', messages.length === 0);
   }

   function showSuccessAlert(message) {
      if (typeof Swal === 'undefined') {
         window.alert(message || 'Data master berhasil disimpan.');
         return;
      }
      Swal.fire({
         icon: 'success',
         title: 'Berhasil',
         text: message || 'Data master berhasil disimpan.',
         confirmButtonText: 'OK',
         confirmButtonColor: '#3952bc',
      });
   }

   function showErrorAlert(message) {
      if (typeof Swal === 'undefined') {
         window.alert(message || 'Terjadi kesalahan.');
         return;
      }
      Swal.fire({
         icon: 'error',
         title: 'Gagal',
         text: message || 'Terjadi kesalahan.',
         confirmButtonText: 'OK',
         confirmButtonColor: '#3952bc',
      });
   }

   if (addBtn) {
      addBtn.addEventListener('click', function () { openModal('create'); });
   }

   if (modal) {
      modal.querySelectorAll('[data-plv-batas-lv-close]').forEach(function (el) {
         el.addEventListener('click', closeModal);
      });

      document.addEventListener('keydown', function (e) {
         if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeModal();
         }
      });
   }

   if (form) {
   form.addEventListener('submit', function (e) {
      e.preventDefault();
      var id = idInput.value;
      var url = id ? showUrlBase + '/' + id : storeUrl;
      var method = id ? 'PUT' : 'POST';
      var body = {
         site: document.getElementById('plv-batas-lv-site').value,
         lokasi: document.getElementById('plv-batas-lv-lokasi').value,
         detail_lokasi: document.getElementById('plv-batas-lv-detail').value,
         batas_lv: parseInt(document.getElementById('plv-batas-lv-batas').value, 10) || 0,
      };

      fetch(url, {
         method: method,
         headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
         },
         body: JSON.stringify(body),
      })
         .then(function (res) { return res.json().then(function (json) { return { ok: res.ok, json: json }; }); })
         .then(function (result) {
            if (!result.ok) {
               showFormErrors(result.json.errors || { error: [result.json.message || 'Gagal menyimpan data.'] });
               return;
            }
            closeModal();
            loadData();
            showSuccessAlert(result.json.message || 'Data master batas LV per lokasi berhasil disimpan.');
         })
         .catch(function () {
            showFormErrors({ error: ['Gagal menyimpan data.'] });
            showErrorAlert('Gagal menyimpan data master.');
         });
   });
   }

   if (tbody) {
   tbody.addEventListener('click', function (e) {
      var editBtn = e.target.closest('[data-plv-batas-lv-edit]');
      var deleteBtn = e.target.closest('[data-plv-batas-lv-delete]');

      if (editBtn) {
         var editId = editBtn.getAttribute('data-plv-batas-lv-edit');
         fetch(showUrlBase + '/' + editId, { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (json) { openModal('edit', json.data); });
         return;
      }

      if (deleteBtn) {
         var deleteId = deleteBtn.getAttribute('data-plv-batas-lv-delete');
         if (!window.confirm('Hapus data batas LV per lokasi ini?')) return;
         fetch(showUrlBase + '/' + deleteId, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
         })
            .then(function (res) { return res.json().then(function (json) { return { ok: res.ok, json: json }; }); })
            .then(function (result) {
               if (!result.ok) {
                  showErrorAlert(result.json.message || 'Gagal menghapus data master.');
                  return;
               }
               loadData();
               showSuccessAlert(result.json.message || 'Data master batas LV per lokasi berhasil dihapus.');
            });
      }
   });
   }

   if (searchForm) {
      searchForm.addEventListener('submit', function (e) {
         e.preventDefault();
         state.q = searchInput ? searchInput.value.trim() : '';
         state.page = 1;
         loadData();
         var resetBtn = document.getElementById('plv-master-search-reset');
         if (resetBtn) resetBtn.classList.toggle('hidden', state.q === '');
      });
   }

   var searchResetBtn = document.getElementById('plv-master-search-reset');
   if (searchResetBtn) {
      searchResetBtn.addEventListener('click', function () {
         if (searchInput) searchInput.value = '';
         state.q = '';
         state.page = 1;
         searchResetBtn.classList.add('hidden');
         loadData();
      });
   }

   loadData();
})();
</script>
@endpush
