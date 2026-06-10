<div id="plv-cr-pengawas-panel">
   <table class="w-full min-w-[1100px] text-left">
      <thead class="border-b border-outline-variant/20 bg-[#f8fafc]">
         <tr>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant w-12">No</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant min-w-[140px]">Lokasi</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant w-20">Kode</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant min-w-[120px]">Site</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant w-24 text-center">Batas LV</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant min-w-[280px]">Detail Lokasi</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant min-w-[220px]">Pengawas</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant w-16"></th>
         </tr>
      </thead>
      <tbody id="plv-cr-pengawas-tbody" class="divide-y divide-outline-variant/10">
         <tr>
            <td colspan="8" class="px-6 py-12 text-center text-sm text-on-surface-variant">Memuat data…</td>
         </tr>
      </tbody>
   </table>

   <div id="plv-cr-pengawas-pagination" class="flex flex-col gap-3 border-t border-outline-variant/20 px-6 py-4 sm:flex-row sm:items-center sm:justify-between hidden">
      <p id="plv-cr-pengawas-info" class="text-xs font-medium text-on-surface-variant"></p>
      <div id="plv-cr-pengawas-pages" class="flex flex-wrap items-center gap-2"></div>
   </div>
</div>

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
   .plv-cr-detail-scroll { max-height: 280px; overflow-y: auto; }
   .plv-cr-detail-scroll::-webkit-scrollbar { width: 4px; }
   .plv-cr-detail-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
   var panel = document.getElementById('plv-cr-pengawas-panel');
   if (!panel) return;

   var tbody = document.getElementById('plv-cr-pengawas-tbody');
   var paginationWrap = document.getElementById('plv-cr-pengawas-pagination');
   var infoEl = document.getElementById('plv-cr-pengawas-info');
   var pagesEl = document.getElementById('plv-cr-pengawas-pages');
   var modal = document.getElementById('plv-cr-pengawas-modal');
   var form = document.getElementById('plv-cr-pengawas-form');
   var modalTitle = document.getElementById('plv-cr-pengawas-modal-title');
   var formErrors = document.getElementById('plv-cr-pengawas-form-errors');
   var idInput = document.getElementById('plv-cr-pengawas-id');
   var addBtn = document.getElementById('plv-cr-pengawas-add-btn');
   var searchForm = document.getElementById('plv-master-search-form');
   var searchInput = document.getElementById('plv-master-search-input');

   var dataUrl = @json(route('pembatasan-lv.master-data.control-room-pengawas.data'));
   var storeUrl = @json(route('pembatasan-lv.master-data.control-room-pengawas.store'));
   var showUrlBase = @json(url('/pembatasan-lv/master-data/control-room-pengawas'));
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
      return 'px-4 py-5 text-sm text-on-surface align-top';
   }

   function showSuccessAlert(message) {
      if (typeof Swal === 'undefined') {
         window.alert(message || 'Data berhasil disimpan.');
         return;
      }
      Swal.fire({
         icon: 'success',
         title: 'Berhasil',
         text: message || 'Data berhasil disimpan.',
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

   function renderDetailItems(items) {
      if (!items || !items.length) {
         return '<span class="text-xs italic text-on-surface-variant">—</span>';
      }

      var html = '<div class="plv-cr-detail-scroll flex flex-col gap-2">';
      items.forEach(function (item, idx) {
         html += '<div class="flex items-start gap-2">' +
            '<span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#374151] text-[10px] font-bold leading-none text-white">' + (idx + 1) + '</span>' +
            '<span class="text-xs leading-snug text-on-surface">' + escapeHtml(item) + '</span>' +
         '</div>';
      });
      html += '</div>';
      return html;
   }

   function renderPengawasCards(pengawas) {
      if (!pengawas.length) {
         return '<span class="text-xs italic text-on-surface-variant">Belum ada pengawas</span>';
      }

      var html = '<div class="flex flex-col gap-2">';
      pengawas.forEach(function (p, idx) {
         html += '<div class="relative rounded-lg border border-gray-200 bg-white p-3 pr-11 shadow-sm">' +
            '<button type="button" class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded bg-red-500 text-white hover:bg-red-600" title="Hapus" data-plv-cr-pengawas-delete="' + p.id + '">' +
               '<span class="material-symbols-outlined text-[18px]">delete</span>' +
            '</button>' +
            '<button type="button" class="block w-full text-left" data-plv-cr-pengawas-edit="' + p.id + '" title="Edit pengawas">' +
               '<div class="text-sm font-bold text-on-background">' + (idx + 1) + '. ' + escapeHtml(p.nama_pengawas) + '</div>' +
               (p.email_pengawas ? '<div class="mt-1 text-xs text-on-surface-variant">' + escapeHtml(p.email_pengawas) + '</div>' : '') +
               (p.no_hp_pengawas ? '<div class="text-xs text-on-surface-variant">' + escapeHtml(p.no_hp_pengawas) + '</div>' : '') +
            '</button>' +
         '</div>';
      });
      html += '</div>';
      return html;
   }

   function renderRows(groups, meta) {
      if (!groups.length) {
         tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-12 text-center text-sm text-on-surface-variant">Belum ada data control room.</td></tr>';
         paginationWrap.classList.add('hidden');
         return;
      }

      var html = '';
      groups.forEach(function (group, index) {
         var rowNo = (meta.from || 1) + index;
         var roomKey = encodeURIComponent(group.control_room);

         html += '<tr class="transition-colors hover:bg-[#f8fafc]">' +
            '<td class="' + tdClass() + ' tabular-nums text-on-surface-variant">' + rowNo + '</td>' +
            '<td class="' + tdClass() + ' font-semibold">' + escapeHtml(group.control_room) + '</td>' +
            '<td class="' + tdClass() + ' font-medium">' + escapeHtml(group.kode || '—') + '</td>' +
            '<td class="' + tdClass() + '">' + escapeHtml(group.site || '—') + '</td>' +
            '<td class="' + tdClass() + ' text-center">' +
               '<span class="inline-flex min-w-[2.75rem] justify-center rounded-full bg-[#3952bc] px-3 py-1 text-sm font-bold text-white">' +
                  escapeHtml(group.batas_lv || 0) +
               '</span>' +
            '</td>' +
            '<td class="' + tdClass() + '">' + renderDetailItems(group.detail_items) + '</td>' +
            '<td class="' + tdClass() + '">' + renderPengawasCards(group.pengawas || []) + '</td>' +
            '<td class="' + tdClass() + '">' +
               '<button type="button" class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#3952bc] text-white shadow-sm hover:opacity-90" title="Tambah pengawas" data-plv-cr-add-to="' + roomKey + '">' +
                  '<span class="material-symbols-outlined text-[22px]">person_add</span>' +
               '</button>' +
            '</td>' +
         '</tr>';
      });

      tbody.innerHTML = html;
      infoEl.textContent = 'Menampilkan ' + meta.from + '–' + meta.to + ' dari ' + meta.total + ' control room';
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
            continue;
         }
         pageBtn(String(p), p, false, p === meta.current_page);
      }
      pageBtn('›', meta.current_page + 1, meta.current_page >= meta.last_page, false);
   }

   function loadData() {
      tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-12 text-center text-sm text-on-surface-variant">Memuat data…</td></tr>';
      var url = new URL(dataUrl, window.location.origin);
      url.searchParams.set('page', state.page);
      url.searchParams.set('per_page', state.perPage);
      if (state.q) url.searchParams.set('q', state.q);

      fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
         .then(function (res) { return res.json(); })
         .then(function (json) { renderRows(json.data || [], json.meta || {}); })
         .catch(function () {
            tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-12 text-center text-sm text-error">Gagal memuat data.</td></tr>';
         });
   }

   function openModal(mode, payload, presetRoom) {
      if (!modal || !form) return;
      form.reset();
      formErrors.classList.add('hidden');
      formErrors.innerHTML = '';
      idInput.value = payload && payload.id ? payload.id : '';
      modalTitle.textContent = mode === 'edit' ? 'Edit Pengawas Control Room' : 'Tambah Pengawas Control Room';

      document.getElementById('plv-cr-pengawas-room').value = (payload && payload.control_room) || presetRoom || '';
      if (payload) {
         document.getElementById('plv-cr-pengawas-nama').value = payload.nama_pengawas || '';
         document.getElementById('plv-cr-pengawas-email').value = payload.email_pengawas || '';
         document.getElementById('plv-cr-pengawas-hp').value = payload.no_hp_pengawas || '';
         document.getElementById('plv-cr-pengawas-keterangan').value = payload.keterangan || '';
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

   if (addBtn) {
      addBtn.addEventListener('click', function () { openModal('create'); });
   }

   if (modal) {
      modal.querySelectorAll('[data-plv-cr-pengawas-close]').forEach(function (el) {
         el.addEventListener('click', closeModal);
      });
      document.addEventListener('keydown', function (e) {
         if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
      });
   }

   if (form) {
      form.addEventListener('submit', function (e) {
         e.preventDefault();
         var id = idInput.value;
         var url = id ? showUrlBase + '/' + id : storeUrl;
         var method = id ? 'PUT' : 'POST';
         var body = {
            control_room: document.getElementById('plv-cr-pengawas-room').value,
            nama_pengawas: document.getElementById('plv-cr-pengawas-nama').value,
            email_pengawas: document.getElementById('plv-cr-pengawas-email').value,
            no_hp_pengawas: document.getElementById('plv-cr-pengawas-hp').value,
            keterangan: document.getElementById('plv-cr-pengawas-keterangan').value,
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
               showSuccessAlert(result.json.message || 'Pengawas control room berhasil disimpan.');
            })
            .catch(function () {
               showFormErrors({ error: ['Gagal menyimpan data.'] });
               showErrorAlert('Gagal menyimpan data pengawas.');
            });
      });
   }

   if (tbody) {
      tbody.addEventListener('click', function (e) {
         var addToBtn = e.target.closest('[data-plv-cr-add-to]');
         var editBtn = e.target.closest('[data-plv-cr-pengawas-edit]');
         var deleteBtn = e.target.closest('[data-plv-cr-pengawas-delete]');

         if (addToBtn) {
            openModal('create', null, decodeURIComponent(addToBtn.getAttribute('data-plv-cr-add-to') || ''));
            return;
         }

         if (editBtn) {
            var editId = editBtn.getAttribute('data-plv-cr-pengawas-edit');
            fetch(showUrlBase + '/' + editId, { headers: { 'Accept': 'application/json' } })
               .then(function (res) { return res.json(); })
               .then(function (json) { openModal('edit', json.data); });
            return;
         }

         if (deleteBtn) {
            var deleteId = deleteBtn.getAttribute('data-plv-cr-pengawas-delete');
            if (!window.confirm('Hapus pengawas ini dari control room?')) return;
            fetch(showUrlBase + '/' + deleteId, {
               method: 'DELETE',
               headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
            })
               .then(function (res) { return res.json().then(function (json) { return { ok: res.ok, json: json }; }); })
               .then(function (result) {
                  if (!result.ok) {
                     showErrorAlert(result.json.message || 'Gagal menghapus data.');
                     return;
                  }
                  loadData();
                  showSuccessAlert(result.json.message || 'Pengawas berhasil dihapus.');
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
