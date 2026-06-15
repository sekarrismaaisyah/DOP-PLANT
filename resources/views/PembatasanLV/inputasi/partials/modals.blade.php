@include('PembatasanLV.inputasi.partials.modal-lv')
@include('PembatasanLV.inputasi.partials.modal-orang')

@push('scripts')
<script>
(function () {
   var lvModal = document.getElementById('plv-inputasi-lv-modal');
   var orangModal = document.getElementById('plv-inputasi-orang-modal');

   function openInputasiModal(type) {
      var modal = type === 'orang' ? orangModal : lvModal;
      if (!modal) return;
      modal.classList.remove('hidden');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('overflow-hidden');
   }

   function closeInputasiModals() {
      [lvModal, orangModal].forEach(function (modal) {
         if (!modal) return;
         modal.classList.add('hidden');
         modal.setAttribute('aria-hidden', 'true');
      });
      document.body.classList.remove('overflow-hidden');
   }

   window.plvOpenInputasiModal = openInputasiModal;
   window.plvCloseInputasiModals = closeInputasiModals;

   document.querySelectorAll('[data-plv-open-inputasi]').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
         e.preventDefault();
         openInputasiModal(btn.getAttribute('data-plv-open-inputasi'));
      });
   });

   document.querySelectorAll('[data-plv-inputasi-close]').forEach(function (el) {
      el.addEventListener('click', closeInputasiModals);
   });

   document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeInputasiModals();
   });

   @php
      $openModal = request('open_inputasi');
      if (!in_array($openModal, ['lv', 'orang'], true) && $errors->any()) {
         $openModal = old('tipe') === 'orang' ? 'orang' : (old('tipe') === 'lv' ? 'lv' : null);
      }
   @endphp
   @if(in_array($openModal ?? '', ['lv', 'orang'], true))
   openInputasiModal(@json($openModal));
   @endif
})();
</script>
@endpush
