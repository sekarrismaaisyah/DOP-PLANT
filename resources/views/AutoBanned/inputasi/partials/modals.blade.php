@include('AutoBanned.inputasi.partials.modal-lv')
@include('AutoBanned.inputasi.partials.modal-orang')

@push('scripts')
<script>
(function () {
   var lvModal = document.getElementById('ab-inputasi-lv-modal');
   var orangModal = document.getElementById('ab-inputasi-orang-modal');

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

   window.abOpenInputasiModal = openInputasiModal;
   window.abCloseInputasiModals = closeInputasiModals;

   document.querySelectorAll('[data-ab-open-inputasi]').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
         e.preventDefault();
         openInputasiModal(btn.getAttribute('data-ab-open-inputasi'));
      });
   });

   document.querySelectorAll('[data-ab-inputasi-close]').forEach(function (el) {
      el.addEventListener('click', closeInputasiModals);
   });

   document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeInputasiModals();
   });

   @php
      $openModal = request('open_inputasi');
      if (!in_array($openModal, ['lv', 'orang'], true)) {
         $openModal = request('tab') === 'orang' ? 'orang' : (request('tab') === 'lv' ? 'lv' : null);
      }
   @endphp
   @if(in_array($openModal ?? '', ['lv', 'orang'], true))
   openInputasiModal(@json($openModal));
   @endif
})();
</script>
@endpush
