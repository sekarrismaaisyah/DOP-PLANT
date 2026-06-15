<div id="plv-planning-orang-data-panel">
   <div class="overflow-x-auto">
      <table class="w-full min-w-[900px] text-left">
         <thead class="border-b border-outline-variant/20 bg-[#f8fafc]">
            <tr>
               <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">No</th>
               <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Tanggal</th>
               <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Shift</th>
               <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">SID</th>
               <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Nama</th>
               <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Control Room</th>
               <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Lokasi</th>
               <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Status</th>
               <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant w-20">Aksi</th>
            </tr>
         </thead>
         <tbody id="plv-planning-orang-tbody" class="divide-y divide-outline-variant/10">
            <tr><td colspan="9" class="px-6 py-12 text-center text-sm text-on-surface-variant">Memuat data…</td></tr>
         </tbody>
      </table>
   </div>
   <div id="plv-planning-orang-pagination" class="hidden flex-col gap-3 border-t border-outline-variant/20 px-6 py-4 sm:flex-row sm:items-center sm:justify-between flex">
      <p id="plv-planning-orang-info" class="text-xs font-medium text-on-surface-variant"></p>
      <div id="plv-planning-orang-pages" class="flex flex-wrap items-center gap-2"></div>
   </div>
</div>

@push('scripts')
<script>
(function () {
   var panel = document.getElementById('plv-planning-orang-data-panel');
   if (!panel) return;

   var tbody = document.getElementById('plv-planning-orang-tbody');
   var paginationWrap = document.getElementById('plv-planning-orang-pagination');
   var infoEl = document.getElementById('plv-planning-orang-info');
   var pagesEl = document.getElementById('plv-planning-orang-pages');
   var dataUrl = @json(route('pembatasan-lv.planning.orang.data'));
   var destroyUrlBase = @json(url('/pembatasan-lv/planning/orang'));
   var csrf = @json(csrf_token());
   var state = { page: 1, perPage: 10 };

   function escapeHtml(v) { return String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

   function renderRows(rows, meta) {
      if (!rows.length) {
         tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-12 text-center text-sm text-on-surface-variant">Belum ada data planning orang.</td></tr>';
         paginationWrap.classList.add('hidden');
         return;
      }
      tbody.innerHTML = rows.map(function (row, i) {
         var no = (meta.from || 1) + i;
         return '<tr class="hover:bg-[#f8fafc]">' +
            '<td class="px-4 py-3 text-sm tabular-nums text-on-surface-variant">' + no + '</td>' +
            '<td class="px-4 py-3 text-sm font-medium">' + escapeHtml(row.tanggal_plan) + '</td>' +
            '<td class="px-4 py-3 text-sm">' + escapeHtml(row.shift_label) + '</td>' +
            '<td class="px-4 py-3 text-sm font-bold">' + escapeHtml(row.sid) + '</td>' +
            '<td class="px-4 py-3 text-sm">' + escapeHtml(row.nama) + '</td>' +
            '<td class="px-4 py-3 text-sm">' + escapeHtml(row.control_room) + '</td>' +
            '<td class="px-4 py-3 text-sm text-on-surface-variant">' + escapeHtml(row.lokasi) + '</td>' +
            '<td class="px-4 py-3 text-sm capitalize">' + escapeHtml(row.status) + '</td>' +
            '<td class="px-4 py-3"><button type="button" class="rounded-lg p-2 text-error hover:bg-error/10" data-plv-plan-orang-delete="' + row.id + '" title="Hapus"><span class="material-symbols-outlined text-lg">delete</span></button></td>' +
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
         btn.type = 'button'; btn.textContent = label;
         btn.className = active ? 'rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-white' : 'rounded-lg border border-outline-variant/30 bg-white px-3 py-1.5 text-xs font-bold hover:bg-surface-container-high';
         if (disabled) { btn.disabled = true; btn.className += ' opacity-50'; }
         else btn.addEventListener('click', function () { state.page = page; loadData(); });
         pagesEl.appendChild(btn);
      }
      pageBtn('‹', meta.current_page - 1, meta.current_page <= 1, false);
      for (var p = 1; p <= meta.last_page; p++) pageBtn(String(p), p, false, p === meta.current_page);
      pageBtn('›', meta.current_page + 1, meta.current_page >= meta.last_page, false);
   }

   function loadData() {
      tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-12 text-center text-sm text-on-surface-variant">Memuat data…</td></tr>';
      var url = new URL(dataUrl, window.location.origin);
      url.searchParams.set('page', state.page);
      url.searchParams.set('per_page', state.perPage);
      fetch(url, { headers: { 'Accept': 'application/json' } })
         .then(function (r) { return r.json(); })
         .then(function (j) { renderRows(j.data || [], j.meta || {}); })
         .catch(function () { tbody.innerHTML = '<tr><td colspan="9" class="px-6 py-12 text-center text-sm text-error">Gagal memuat data.</td></tr>'; });
   }

   tbody.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-plv-plan-orang-delete]');
      if (!btn) return;
      var id = btn.getAttribute('data-plv-plan-orang-delete');
      if (!window.confirm('Hapus planning orang ini?')) return;
      fetch(destroyUrlBase + '/' + id, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } })
         .then(function (r) { return r.json(); })
         .then(function () { loadData(); });
   });

   loadData();
})();
</script>
@endpush
