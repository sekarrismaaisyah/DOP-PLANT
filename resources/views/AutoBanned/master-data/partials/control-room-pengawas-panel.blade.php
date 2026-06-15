<div id="ab-cr-pengawas-panel">
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
      <tbody id="ab-cr-pengawas-tbody" class="divide-y divide-outline-variant/10">
         <tr>
            <td colspan="8" class="px-6 py-12 text-center text-sm text-on-surface-variant">Belum ada data control room.</td>
         </tr>
      </tbody>
   </table>
</div>

@push('scripts')
<script>
(function () {
   var addBtn = document.getElementById('ab-cr-pengawas-add-btn');
   var modal = document.getElementById('ab-cr-pengawas-modal');
   var form = document.getElementById('ab-cr-pengawas-form');
   var modalTitle = document.getElementById('ab-cr-pengawas-modal-title');
   var idInput = document.getElementById('ab-cr-pengawas-id');

   function openModal(mode) {
      if (!modal || !form) return;
      form.reset();
      if (idInput) idInput.value = '';
      if (modalTitle) modalTitle.textContent = mode === 'edit' ? 'Edit Pengawas Control Room' : 'Tambah Pengawas Control Room';
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

   if (addBtn) addBtn.addEventListener('click', function () { openModal('create'); });

   if (modal) {
      modal.querySelectorAll('[data-ab-cr-pengawas-close]').forEach(function (el) {
         el.addEventListener('click', closeModal);
      });
      document.addEventListener('keydown', function (e) {
         if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
      });
   }

   if (form) {
      form.addEventListener('submit', function (e) {
         e.preventDefault();
         closeModal();
      });
   }
})();
</script>
@endpush
