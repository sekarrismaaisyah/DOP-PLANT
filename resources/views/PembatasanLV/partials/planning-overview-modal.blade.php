<div id="plv-planning-overview-modal" class="fixed inset-0 z-[200] hidden" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="plv-planning-overview-modal-title">
   <div class="fixed inset-0 bg-black/40 backdrop-blur-[1px]" data-plv-planning-overview-close></div>
   <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 overflow-y-auto pointer-events-none">
      <div class="pointer-events-auto relative w-full max-w-5xl my-auto flex max-h-[min(92vh,820px)] flex-col rounded-2xl bg-white shadow-card-heavy">
         <div class="flex shrink-0 items-center justify-between border-b border-outline-variant/20 px-6 py-4">
            <div>
               <h4 id="plv-planning-overview-modal-title" class="font-headline font-bold text-lg text-on-background">Planning — Check-in</h4>
               <p id="plv-planning-overview-subtitle" class="text-xs text-on-surface-variant mt-0.5">Data rencana yang belum check-in</p>
            </div>
            <div class="flex items-center gap-2">
               <a href="{{ route('pembatasan-lv.planning.index') }}" class="hidden sm:inline-flex items-center gap-1 rounded-lg border border-outline-variant/30 px-3 py-1.5 text-[11px] font-bold text-primary hover:bg-primary/5">
                  <span class="material-symbols-outlined text-base">open_in_new</span>
                  Kelola Planning
               </a>
               <button type="button" class="rounded-lg p-2 text-on-surface-variant hover:bg-surface-container-high" data-plv-planning-overview-close aria-label="Tutup">
                  <span class="material-symbols-outlined">close</span>
               </button>
            </div>
         </div>

         <div class="shrink-0 border-b border-outline-variant/15 px-6 py-3">
            <div class="inline-flex p-1 rounded-xl bg-[#f1f5f9]/80 gap-0.5" role="tablist">
               <button type="button" role="tab" id="plv-planning-tab-lv" data-plv-planning-tab="lv" aria-selected="true" class="rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm">Planning LV <span id="plv-planning-count-lv" class="ml-1 opacity-80">(0)</span></button>
               <button type="button" role="tab" id="plv-planning-tab-orang" data-plv-planning-tab="orang" aria-selected="false" class="rounded-lg px-4 py-2 text-xs font-semibold text-on-surface-variant hover:bg-white/80">Planning Orang <span id="plv-planning-count-orang" class="ml-1">(0)</span></button>
            </div>
         </div>

         <div class="min-h-0 flex-1 overflow-y-auto">
            <div id="plv-planning-panel-lv" data-plv-planning-panel="lv" class="overflow-x-auto">
               <table class="w-full min-w-[720px] text-left">
                  <thead class="bg-[#f8fafc] border-b border-outline-variant/15 sticky top-0 z-10">
                     <tr>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase text-on-surface-variant">No Unit</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase text-on-surface-variant">Driver</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase text-on-surface-variant">Shift</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase text-on-surface-variant">Lokasi</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase text-on-surface-variant">CR</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase text-on-surface-variant w-28">Aksi</th>
                     </tr>
                  </thead>
                  <tbody id="plv-planning-lv-tbody" class="divide-y divide-outline-variant/10">
                     <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-on-surface-variant">Memuat data…</td></tr>
                  </tbody>
               </table>
            </div>

            <div id="plv-planning-panel-orang" data-plv-planning-panel="orang" class="overflow-x-auto hidden">
               <table class="w-full min-w-[720px] text-left">
                  <thead class="bg-[#f8fafc] border-b border-outline-variant/15 sticky top-0 z-10">
                     <tr>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase text-on-surface-variant">SID</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase text-on-surface-variant">Nama</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase text-on-surface-variant">Shift</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase text-on-surface-variant">Lokasi</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase text-on-surface-variant">CR</th>
                        <th class="px-4 py-3 text-[10px] font-bold uppercase text-on-surface-variant w-28">Aksi</th>
                     </tr>
                  </thead>
                  <tbody id="plv-planning-orang-tbody" class="divide-y divide-outline-variant/10">
                     <tr><td colspan="6" class="px-6 py-10 text-center text-sm text-on-surface-variant">Memuat data…</td></tr>
                  </tbody>
               </table>
            </div>
         </div>

         <div class="shrink-0 border-t border-outline-variant/20 bg-[#f8fafc] px-6 py-3 flex justify-end">
            <button type="button" data-plv-planning-overview-close class="rounded-xl border border-outline-variant/30 bg-white px-5 py-2.5 text-sm font-bold text-on-surface hover:bg-white">Tutup</button>
         </div>
      </div>
   </div>
</div>

@push('scripts')
<script>
(function () {
   var modal = document.getElementById('plv-planning-overview-modal');
   var openBtn = document.getElementById('plv-open-planning-overview');
   if (!modal || !openBtn) return;

   var lvTbody = document.getElementById('plv-planning-lv-tbody');
   var orangTbody = document.getElementById('plv-planning-orang-tbody');
   var countLv = document.getElementById('plv-planning-count-lv');
   var countOrang = document.getElementById('plv-planning-count-orang');
   var subtitle = document.getElementById('plv-planning-overview-subtitle');
   var pendingUrl = @json(route('pembatasan-lv.planning.pending-overview'));
   var checkinLvBase = @json(url('/pembatasan-lv/planning/lv'));
   var checkinOrangBase = @json(url('/pembatasan-lv/planning/orang'));
   var csrf = @json(csrf_token());
   var planningFilterParams = @json($filters ?? []);

   function escapeHtml(v) {
      return String(v ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
   }

   function openModal() {
      modal.classList.remove('hidden');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('overflow-hidden');
      loadPending();
   }

   function closeModal() {
      modal.classList.add('hidden');
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('overflow-hidden');
   }

   openBtn.addEventListener('click', function (e) { e.preventDefault(); openModal(); });
   modal.querySelectorAll('[data-plv-planning-overview-close]').forEach(function (el) {
      el.addEventListener('click', closeModal);
   });
   document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
   });

   var tabs = modal.querySelectorAll('[data-plv-planning-tab]');
   var panels = modal.querySelectorAll('[data-plv-planning-panel]');
   tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
         var name = tab.getAttribute('data-plv-planning-tab');
         tabs.forEach(function (t) {
            var active = t.getAttribute('data-plv-planning-tab') === name;
            t.setAttribute('aria-selected', active ? 'true' : 'false');
            t.className = active
               ? 'rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm'
               : 'rounded-lg px-4 py-2 text-xs font-semibold text-on-surface-variant hover:bg-white/80';
         });
         panels.forEach(function (p) {
            p.classList.toggle('hidden', p.getAttribute('data-plv-planning-panel') !== name);
         });
      });
   });

   function renderLvRows(rows) {
      if (countLv) countLv.textContent = '(' + rows.length + ')';
      if (!rows.length) {
         lvTbody.innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-sm text-on-surface-variant">Tidak ada planning LV untuk tanggal ini.</td></tr>';
         return;
      }
      lvTbody.innerHTML = rows.map(function (row) {
         return '<tr class="hover:bg-[#f8fafc]">' +
            '<td class="px-4 py-3 text-sm font-bold">' + escapeHtml(row.no_lambung) + '</td>' +
            '<td class="px-4 py-3 text-sm">' + escapeHtml(row.nama_driver) + '</td>' +
            '<td class="px-4 py-3 text-sm">' + escapeHtml(row.shift_label) + '</td>' +
            '<td class="px-4 py-3 text-sm"><div>' + escapeHtml(row.lokasi) + '</div>' +
               (row.detail_lokasi ? '<div class="text-xs text-on-surface-variant">' + escapeHtml(row.detail_lokasi) + '</div>' : '') + '</td>' +
            '<td class="px-4 py-3 text-sm text-on-surface-variant">' + escapeHtml(row.control_room) + '</td>' +
            '<td class="px-4 py-3"><button type="button" class="inline-flex items-center gap-1 rounded-lg bg-primary px-2.5 py-1.5 text-[10px] font-bold text-white hover:opacity-95" data-plv-checkin-lv="' + row.id + '">' +
               '<span class="material-symbols-outlined text-sm">login</span> Check-in</button></td></tr>';
      }).join('');
   }

   function renderOrangRows(rows) {
      if (countOrang) countOrang.textContent = '(' + rows.length + ')';
      if (!rows.length) {
         orangTbody.innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-sm text-on-surface-variant">Tidak ada planning orang untuk tanggal ini.</td></tr>';
         return;
      }
      orangTbody.innerHTML = rows.map(function (row) {
         return '<tr class="hover:bg-[#f8fafc]">' +
            '<td class="px-4 py-3 text-sm font-bold">' + escapeHtml(row.sid) + '</td>' +
            '<td class="px-4 py-3 text-sm">' + escapeHtml(row.nama) + '</td>' +
            '<td class="px-4 py-3 text-sm">' + escapeHtml(row.shift_label) + '</td>' +
            '<td class="px-4 py-3 text-sm"><div>' + escapeHtml(row.lokasi) + '</div>' +
               (row.detail_lokasi ? '<div class="text-xs text-on-surface-variant">' + escapeHtml(row.detail_lokasi) + '</div>' : '') + '</td>' +
            '<td class="px-4 py-3 text-sm text-on-surface-variant">' + escapeHtml(row.control_room) + '</td>' +
            '<td class="px-4 py-3"><button type="button" class="inline-flex items-center gap-1 rounded-lg bg-primary px-2.5 py-1.5 text-[10px] font-bold text-white hover:opacity-95" data-plv-checkin-orang="' + row.id + '">' +
               '<span class="material-symbols-outlined text-sm">login</span> Check-in</button></td></tr>';
      }).join('');
   }

   function loadPending() {
      lvTbody.innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-sm text-on-surface-variant">Memuat data…</td></tr>';
      orangTbody.innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-sm text-on-surface-variant">Memuat data…</td></tr>';

      var url = new URL(pendingUrl, window.location.origin);
      if (planningFilterParams.site) url.searchParams.set('site', planningFilterParams.site);
      if (planningFilterParams.tanggal) url.searchParams.set('tanggal', planningFilterParams.tanggal);
      if (planningFilterParams.control_room) url.searchParams.set('control_room', planningFilterParams.control_room);

      fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
         .then(function (r) { return r.json(); })
         .then(function (json) {
            var data = json.data || {};
            if (subtitle && json.meta && json.meta.tanggal) {
               subtitle.textContent = 'Planning belum check-in · tanggal filter: ' + json.meta.tanggal;
            }
            renderLvRows(data.lv || []);
            renderOrangRows(data.orang || []);
         })
         .catch(function () {
            lvTbody.innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-sm text-error">Gagal memuat planning.</td></tr>';
            orangTbody.innerHTML = lvTbody.innerHTML;
         });
   }

   function doCheckin(url) {
      fetch(url, {
         method: 'POST',
         headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
         },
         body: JSON.stringify({}),
      })
         .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, json: j }; }); })
         .then(function (result) {
            if (!result.ok || !result.json.success) {
               var msg = result.json.message || 'Check-in gagal.';
               if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Gagal', text: msg, confirmButtonColor: '#3952bc' });
               else window.alert(msg);
               return;
            }
            if (typeof Swal !== 'undefined') {
               Swal.fire({ icon: 'success', title: 'Berhasil', text: result.json.message, confirmButtonColor: '#3952bc' })
                  .then(function () { window.location.reload(); });
            } else {
               window.location.reload();
            }
            loadPending();
            if (typeof window.fetchMasukAktif === 'function') window.fetchMasukAktif();
            if (typeof window.fetchOrangMasukAktif === 'function') window.fetchOrangMasukAktif();
         })
         .catch(function () {
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan jaringan.', confirmButtonColor: '#3952bc' });
         });
   }

   lvTbody.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-plv-checkin-lv]');
      if (!btn || btn.disabled) return;
      var id = btn.getAttribute('data-plv-checkin-lv');
      if (!window.confirm('Check-in LV dari planning ini? Unit akan masuk area.')) return;
      btn.disabled = true;
      doCheckin(checkinLvBase + '/' + id + '/checkin');
   });

   orangTbody.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-plv-checkin-orang]');
      if (!btn || btn.disabled) return;
      var id = btn.getAttribute('data-plv-checkin-orang');
      if (!window.confirm('Check-in orang dari planning ini? Personel akan masuk area.')) return;
      btn.disabled = true;
      doCheckin(checkinOrangBase + '/' + id + '/checkin');
   });

   window.plvReloadPlanningOverview = loadPending;
})();
</script>
@endpush
