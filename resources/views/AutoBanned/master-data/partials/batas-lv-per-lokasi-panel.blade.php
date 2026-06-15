@php
   $thClass = 'px-6 py-4 text-[10px] font-bold uppercase tracking-[0.15em] text-on-surface-variant';
@endphp

<div id="ab-batas-lv-panel">
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
      <tbody id="ab-batas-lv-tbody" class="divide-y divide-outline-variant/10">
         <tr>
            <td colspan="6" class="px-6 py-12 text-center text-sm text-on-surface-variant">Belum ada data batas LV per lokasi.</td>
         </tr>
      </tbody>
   </table>
</div>

@push('scripts')
<script>
(function () {
   var addBtn = document.getElementById('ab-batas-lv-add-btn');
   var modal = document.getElementById('ab-batas-lv-modal');
   var form = document.getElementById('ab-batas-lv-form');
   var modalTitle = document.getElementById('ab-batas-lv-modal-title');
   var idInput = document.getElementById('ab-batas-lv-id');

   function openModal(mode) {
      if (!modal || !form) return;
      form.reset();
      if (idInput) idInput.value = '';
      if (modalTitle) modalTitle.textContent = mode === 'edit' ? 'Edit Batas LV' : 'Tambah Batas LV';
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
      modal.querySelectorAll('[data-ab-batas-lv-close]').forEach(function (el) {
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
