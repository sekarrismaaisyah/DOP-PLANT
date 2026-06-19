@include('AutoBanned.inputasi.partials.modal-lv')
@include('AutoBanned.inputasi.partials.modal-orang')
@include('AutoBanned.inputasi.partials.modal-treatment')

@push('scripts')
<script>
(function () {
   var lvModal = document.getElementById('ab-inputasi-lv-modal');
   var orangModal = document.getElementById('ab-inputasi-orang-modal');
   var treatmentModal = document.getElementById('ab-inputasi-treatment-modal');
   var modals = [lvModal, orangModal, treatmentModal].filter(Boolean);

   function openInputasiModal(type) {
      modals.forEach(function (modal) {
         modal.classList.add('hidden');
         modal.setAttribute('aria-hidden', 'true');
      });

      var modal = type === 'orang'
         ? orangModal
         : (type === 'treatment' ? treatmentModal : lvModal);

      if (!modal) return;

      modal.classList.remove('hidden');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('overflow-hidden');

      if (type === 'treatment') {
         var sidInput = document.getElementById('ab-treatment-sid');
         if (sidInput && sidInput.value.trim() !== '') {
            lookupSid();
         }
      }
   }

   function closeInputasiModals() {
      modals.forEach(function (modal) {
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

   var lookupBtn = document.getElementById('ab-treatment-lookup-btn');
   var sidInput = document.getElementById('ab-treatment-sid');
   var lookupMsg = document.getElementById('ab-treatment-lookup-msg');
   var preview = document.getElementById('ab-treatment-preview');

   function setPreview(data) {
      if (!preview) return;
      document.getElementById('ab-treatment-karyawan').textContent = data.karyawan || '—';
      document.getElementById('ab-treatment-perusahaan').textContent = data.perusahaan || '—';
      document.getElementById('ab-treatment-site').textContent = data.site_dedicated || '—';
      document.getElementById('ab-treatment-reason').textContent = data.banned_reason || '—';
      preview.classList.remove('hidden');
   }

   function lookupSid() {
      if (!sidInput || !lookupMsg) return;

      var sid = sidInput.value.trim();
      if (sid === '') {
         lookupMsg.textContent = 'Masukkan SID terlebih dahulu.';
         lookupMsg.className = 'mt-1.5 text-[11px] text-red-600';
         if (preview) preview.classList.add('hidden');
         return;
      }

      lookupMsg.textContent = 'Mencari SID…';
      lookupMsg.className = 'mt-1.5 text-[11px] text-on-surface-variant';

      var weekInput = document.querySelector('#ab-treatment-form input[name="week"]');
      var yearInput = document.querySelector('#ab-treatment-form input[name="year"]');
      var params = new URLSearchParams({
         sid: sid,
         week: weekInput ? weekInput.value : '',
         year: yearInput ? yearInput.value : ''
      });

      fetch(@json(route('auto-banned.treatment-evidence.lookup-sid')) + '?' + params.toString(), {
         headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      })
         .then(function (res) { return res.json(); })
         .then(function (payload) {
            if (!payload.found) {
               lookupMsg.textContent = payload.message || 'SID tidak ditemukan.';
               lookupMsg.className = 'mt-1.5 text-[11px] text-red-600';
               if (preview) preview.classList.add('hidden');
               return;
            }

            lookupMsg.textContent = 'SID ditemukan.';
            lookupMsg.className = 'mt-1.5 text-[11px] text-emerald-700';
            setPreview(payload.data || {});
         })
         .catch(function () {
            lookupMsg.textContent = 'Gagal memeriksa SID. Coba lagi.';
            lookupMsg.className = 'mt-1.5 text-[11px] text-red-600';
         });
   }

   if (lookupBtn) {
      lookupBtn.addEventListener('click', lookupSid);
   }

   if (sidInput) {
      sidInput.addEventListener('blur', function () {
         if (sidInput.value.trim() !== '') lookupSid();
      });
   }

   @php
      $openModal = request('open_inputasi');
      if (!in_array($openModal, ['lv', 'orang', 'treatment'], true)) {
         $openModal = request('tab') === 'orang'
            ? 'orang'
            : (request('tab') === 'treatment'
               ? 'treatment'
               : (request('tab') === 'lv' ? 'lv' : null));
      }
   @endphp
   @if(in_array($openModal ?? '', ['lv', 'orang', 'treatment'], true))
   openInputasiModal(@json($openModal));
   @elseif($errors->any() && (old('sid') || old('alasan_pengajuan')))
   openInputasiModal('treatment');
   @endif
})();
</script>
@endpush
