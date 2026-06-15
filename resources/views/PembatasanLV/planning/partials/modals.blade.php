@include('PembatasanLV.planning.partials.modal-lv')
@include('PembatasanLV.planning.partials.modal-orang')

@push('scripts')
<script>
(function () {
   var lvModal = document.getElementById('plv-planning-lv-modal');
   var orangModal = document.getElementById('plv-planning-orang-modal');

   function openPlanningModal(type) {
      var modal = type === 'orang' ? orangModal : lvModal;
      if (!modal) return;
      modal.classList.remove('hidden');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('overflow-hidden');
   }

   function closePlanningModals() {
      [lvModal, orangModal].forEach(function (modal) {
         if (!modal) return;
         modal.classList.add('hidden');
         modal.setAttribute('aria-hidden', 'true');
      });
      document.body.classList.remove('overflow-hidden');
   }

   document.querySelectorAll('[data-plv-open-planning]').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
         e.preventDefault();
         openPlanningModal(btn.getAttribute('data-plv-open-planning'));
      });
   });

   document.querySelectorAll('[data-plv-planning-close]').forEach(function (el) {
      el.addEventListener('click', closePlanningModals);
   });

   document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closePlanningModals();
   });

   @php
      $openModal = request('open_planning');
      if (!in_array($openModal, ['lv', 'orang'], true) && $errors->any()) {
         $openModal = old('tipe') === 'planning-orang' ? 'orang' : (old('tipe') === 'planning-lv' ? 'lv' : null);
      }
   @endphp
   @if(in_array($openModal ?? '', ['lv', 'orang'], true))
   openPlanningModal(@json($openModal));
   @endif
})();
</script>
@endpush
