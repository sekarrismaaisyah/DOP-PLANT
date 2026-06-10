{{--
    Filter picker (gaya dashboard-peer): site, tanggal, control room.

    Variabel:
    - $sites         (iterable)
    - $controlRooms (iterable)
    - $filters       ['site' => ?, 'tanggal' => ?, 'control_room' => ?]
--}}
@php
   $filters = $filters ?? ['site' => '', 'tanggal' => now()->toDateString(), 'control_room' => ''];
   $siteLabel = $filters['site'] !== '' ? $filters['site'] : 'Semua Site';
   $tanggalLabel = \Carbon\Carbon::parse($filters['tanggal'])->format('d M Y');
   $controlRoomLabel = $filters['control_room'] !== '' ? $filters['control_room'] : 'Semua Control Room';
   $pickerBtnClass = 'inline-flex w-full items-center justify-center gap-2 rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-4 py-3 text-left shadow-inner transition-colors hover:bg-surface-container-high sm:w-auto sm:min-w-[14rem]';
   $dropdownClass = 'plv-filter-dropdown hidden absolute right-0 top-full z-40 mt-2 max-h-64 w-72 overflow-y-auto rounded-xl border border-outline-variant/20 bg-white py-2 shadow-card-heavy';
   $optionClass = 'flex w-full items-center px-4 py-2.5 text-left text-sm font-semibold text-on-surface transition-colors hover:bg-[#f5f7f9]';
@endphp
<form method="GET" action="{{ route('pembatasan-lv.index') }}" id="plv-filter-form" class="flex flex-wrap items-center gap-3">
   {{-- Site --}}
   <div class="relative" data-plv-filter-wrap>
      <button type="button" data-plv-filter-toggle class="{{ $pickerBtnClass }}" aria-haspopup="listbox" aria-expanded="false">
         <span class="material-symbols-outlined text-primary text-xl">location_on</span>
         <span class="flex min-w-0 flex-1 flex-col items-start">
            <span class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Site</span>
            <span id="plv-site-label" class="truncate text-sm font-bold text-on-surface">{{ $siteLabel }}</span>
         </span>
         <span class="material-symbols-outlined text-on-surface-variant">expand_more</span>
      </button>
      <div class="{{ $dropdownClass }}" data-plv-filter-menu role="listbox">
         <button type="button" class="{{ $optionClass }} plv-filter-option" data-name="site" data-value="" data-label="Semua Site">Semua Site</button>
         @foreach($sites as $site)
         <button type="button" class="{{ $optionClass }} plv-filter-option {{ $filters['site'] === $site ? 'bg-primary/5 text-primary' : '' }}" data-name="site" data-value="{{ $site }}" data-label="{{ $site }}">{{ $site }}</button>
         @endforeach
      </div>
      <input type="hidden" name="site" id="plv-filter-site" value="{{ $filters['site'] }}"/>
   </div>

   {{-- Tanggal --}}
   <div class="relative" data-plv-filter-wrap>
      <button type="button" id="plv-open-tanggal" class="{{ $pickerBtnClass }}" aria-haspopup="dialog">
         <span class="material-symbols-outlined text-primary text-xl">calendar_month</span>
         <span class="flex min-w-0 flex-1 flex-col items-start">
            <span class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Tanggal</span>
            <span id="plv-tanggal-label" class="truncate text-sm font-bold text-on-surface">{{ $tanggalLabel }}</span>
         </span>
         <span class="material-symbols-outlined text-on-surface-variant">expand_more</span>
      </button>
      <input type="date" name="tanggal" id="plv-filter-tanggal" value="{{ $filters['tanggal'] }}" class="pointer-events-none absolute opacity-0" tabindex="-1" aria-hidden="true"/>
   </div>

   {{-- Control Room --}}
   <div class="relative" data-plv-filter-wrap>
      <button type="button" data-plv-filter-toggle class="{{ $pickerBtnClass }}" aria-haspopup="listbox" aria-expanded="false">
         <span class="material-symbols-outlined text-primary text-xl">meeting_room</span>
         <span class="flex min-w-0 flex-1 flex-col items-start">
            <span class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">Control Room</span>
            <span id="plv-control-room-label" class="truncate text-sm font-bold text-on-surface">{{ $controlRoomLabel }}</span>
         </span>
         <span class="material-symbols-outlined text-on-surface-variant">expand_more</span>
      </button>
      <div class="{{ $dropdownClass }}" data-plv-filter-menu role="listbox">
         <button type="button" class="{{ $optionClass }} plv-filter-option" data-name="control_room" data-value="" data-label="Semua Control Room">Semua Control Room</button>
         @foreach($controlRooms as $controlRoom)
         <button type="button" class="{{ $optionClass }} plv-filter-option {{ $filters['control_room'] === $controlRoom ? 'bg-primary/5 text-primary' : '' }}" data-name="control_room" data-value="{{ $controlRoom }}" data-label="{{ $controlRoom }}">{{ $controlRoom }}</button>
         @endforeach
      </div>
      <input type="hidden" name="control_room" id="plv-filter-control-room" value="{{ $filters['control_room'] }}"/>
   </div>

   @if($filters['site'] || $filters['control_room'] || request()->has('tanggal'))
   <a href="{{ route('pembatasan-lv.index') }}" class="inline-flex items-center justify-center rounded-xl border border-outline-variant/30 bg-white px-3 py-3 text-on-surface-variant shadow-sm transition-colors hover:bg-surface-container-high" title="Reset filter">
      <span class="material-symbols-outlined text-xl">restart_alt</span>
   </a>
   @endif
</form>

@push('scripts')
<script>
(function () {
   var form = document.getElementById('plv-filter-form');
   if (!form) return;

   function closeAllMenus(except) {
      form.querySelectorAll('[data-plv-filter-menu]').forEach(function (menu) {
         if (menu === except) return;
         menu.classList.add('hidden');
         var toggle = menu.parentElement && menu.parentElement.querySelector('[data-plv-filter-toggle]');
         if (toggle) toggle.setAttribute('aria-expanded', 'false');
      });
   }

   form.querySelectorAll('[data-plv-filter-toggle]').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
         e.stopPropagation();
         var menu = btn.parentElement.querySelector('[data-plv-filter-menu]');
         var isOpen = menu && !menu.classList.contains('hidden');
         closeAllMenus();
         if (menu && !isOpen) {
            menu.classList.remove('hidden');
            btn.setAttribute('aria-expanded', 'true');
         }
      });
   });

   form.querySelectorAll('.plv-filter-option').forEach(function (opt) {
      opt.addEventListener('click', function () {
         var name = opt.getAttribute('data-name');
         var value = opt.getAttribute('data-value') || '';
         var label = opt.getAttribute('data-label') || '';
         var input = form.querySelector('[name="' + name + '"]');
         if (input) input.value = value;
         var labelEl = document.getElementById('plv-' + name.replace('_', '-') + '-label')
            || document.getElementById('plv-' + name + '-label');
         if (name === 'control_room') labelEl = document.getElementById('plv-control-room-label');
         if (name === 'site') labelEl = document.getElementById('plv-site-label');
         if (labelEl) labelEl.textContent = label;
         closeAllMenus();
         form.submit();
      });
   });

   var tanggalBtn = document.getElementById('plv-open-tanggal');
   var tanggalInput = document.getElementById('plv-filter-tanggal');
   if (tanggalBtn && tanggalInput) {
      tanggalBtn.addEventListener('click', function () {
         closeAllMenus();
         if (typeof tanggalInput.showPicker === 'function') {
            tanggalInput.showPicker();
         } else {
            tanggalInput.classList.remove('pointer-events-none');
            tanggalInput.focus();
            tanggalInput.click();
            tanggalInput.classList.add('pointer-events-none');
         }
      });
      tanggalInput.addEventListener('change', function () {
         var labelEl = document.getElementById('plv-tanggal-label');
         if (labelEl && tanggalInput.value) {
            var parts = tanggalInput.value.split('-');
            var months = ['Jan','Peb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            if (parts.length === 3) {
               labelEl.textContent = parseInt(parts[2], 10) + ' ' + months[parseInt(parts[1], 10) - 1] + ' ' + parts[0];
            }
         }
         form.submit();
      });
   }

   document.addEventListener('click', function () {
      closeAllMenus();
   });
})();
</script>
@endpush
