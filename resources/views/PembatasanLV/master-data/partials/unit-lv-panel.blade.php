<div id="plv-unit-lv-panel">
   <table class="w-full min-w-[960px] text-left">
      <thead class="border-b border-outline-variant/20 bg-[#f8fafc]">
         <tr>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant w-12">No</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant min-w-[180px]">Perusahaan</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant min-w-[100px]">SID Unit</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant min-w-[100px]">No Lambung</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant min-w-[100px]">Kategori</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant min-w-[140px]">Jenis Unit</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant min-w-[100px]">Merk</th>
            <th class="px-4 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant min-w-[120px]">Tipe Detail</th>
         </tr>
      </thead>
      <tbody id="plv-unit-lv-tbody" class="divide-y divide-outline-variant/10">
         <tr>
            <td colspan="8" class="px-6 py-12 text-center text-sm text-on-surface-variant">Memuat data…</td>
         </tr>
      </tbody>
   </table>

   <div id="plv-unit-lv-pagination" class="flex flex-col gap-3 border-t border-outline-variant/20 px-6 py-4 sm:flex-row sm:items-center sm:justify-between hidden">
      <p id="plv-unit-lv-info" class="text-xs font-medium text-on-surface-variant"></p>
      <div id="plv-unit-lv-pages" class="flex flex-wrap items-center gap-2"></div>
   </div>
</div>

@push('scripts')
<script>
(function () {
   var panel = document.getElementById('plv-unit-lv-panel');
   if (!panel) return;

   var tbody = document.getElementById('plv-unit-lv-tbody');
   var paginationWrap = document.getElementById('plv-unit-lv-pagination');
   var infoEl = document.getElementById('plv-unit-lv-info');
   var pagesEl = document.getElementById('plv-unit-lv-pages');
   var searchForm = document.getElementById('plv-master-search-form');
   var searchInput = document.getElementById('plv-master-search-input');

   var dataUrl = @json(route('pembatasan-lv.master-data.becomeline-unit.data'));
   var state = { page: 1, perPage: 10, q: @json($q) };

   function escapeHtml(value) {
      return String(value ?? '')
         .replace(/&/g, '&amp;')
         .replace(/</g, '&lt;')
         .replace(/>/g, '&gt;')
         .replace(/"/g, '&quot;');
   }

   function tdClass() {
      return 'px-4 py-4 text-sm text-on-surface';
   }

   function cell(value) {
      var text = String(value ?? '').trim();
      return text !== '' ? escapeHtml(text) : '<span class="text-on-surface-variant">—</span>';
   }

   function renderRows(rows, meta) {
      if (!rows.length) {
         tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-12 text-center text-sm text-on-surface-variant">Belum ada data unit LV.</td></tr>';
         paginationWrap.classList.add('hidden');
         return;
      }

      var html = '';
      rows.forEach(function (row, index) {
         var rowNo = (meta.from || 1) + index;
         html += '<tr class="transition-colors hover:bg-[#f8fafc]">' +
            '<td class="' + tdClass() + ' tabular-nums text-on-surface-variant">' + rowNo + '</td>' +
            '<td class="' + tdClass() + '">' + cell(row.perusahaan) + '</td>' +
            '<td class="' + tdClass() + ' font-medium">' + cell(row.sid_unit) + '</td>' +
            '<td class="' + tdClass() + ' font-bold">' + cell(row.no_lambung) + '</td>' +
            '<td class="' + tdClass() + '">' + cell(row.kategori_unit) + '</td>' +
            '<td class="' + tdClass() + '">' + cell(row.jenis_unit) + '</td>' +
            '<td class="' + tdClass() + '">' + cell(row.merk_unit) + '</td>' +
            '<td class="' + tdClass() + '">' + cell(row.tipe_detail_unit) + '</td>' +
         '</tr>';
      });

      tbody.innerHTML = html;
      infoEl.textContent = 'Menampilkan ' + meta.from + '–' + meta.to + ' dari ' + meta.total + ' unit';
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
            tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-12 text-center text-sm text-error">Gagal memuat data unit LV.</td></tr>';
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
