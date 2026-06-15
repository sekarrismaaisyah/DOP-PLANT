@php
   $filters = $filters ?? ['site' => '', 'week' => '', 'year' => '', 'perusahaan' => '', 'q' => ''];
   $filterOptions = $filterOptions ?? ['sites' => collect(), 'weeks' => collect(), 'years' => collect(), 'perusahaan' => collect()];

   $siteLabel = $filters['site'] !== '' ? $filters['site'] : 'Semua Site';
   $weekLabel = $filters['week'] !== '' ? $filters['week'] : 'Semua Minggu';
   $yearLabel = $filters['year'] !== '' ? $filters['year'] : 'Semua Tahun';
   $perusahaanLabel = $filters['perusahaan'] !== '' ? $filters['perusahaan'] : 'Semua Perusahaan';

   $pickerBtnClass = 'inline-flex w-full items-center gap-2.5 rounded-2xl border border-outline-variant/15 bg-white/80 backdrop-blur-sm px-3.5 py-2.5 text-left shadow-sm transition-all duration-300 hover:border-primary/20 hover:shadow-md sm:w-auto sm:min-w-[10.5rem]';
   $dropdownClass = 'ab-filter-dropdown hidden absolute left-0 right-auto top-full z-50 mt-2 max-h-64 w-72 overflow-y-auto rounded-2xl border border-outline-variant/15 bg-white py-2 shadow-lg sm:left-auto sm:right-0';
   $optionClass = 'flex w-full items-center px-4 py-2.5 text-left text-sm font-medium text-on-surface transition-colors duration-200 hover:bg-primary/[0.04]';
@endphp

@php
   $filterRoute = $filterRoute ?? 'auto-banned.index';
@endphp

<form method="GET" action="{{ route($filterRoute) }}" id="ab-filter-form" class="flex flex-wrap items-center justify-end gap-2.5">
   @if(($filters['q'] ?? '') !== '')
   <input type="hidden" name="q" value="{{ $filters['q'] }}"/>
   @endif

   {{-- Site --}}
   <div class="relative" data-ab-filter-wrap>
      <button type="button" data-ab-filter-toggle class="{{ $pickerBtnClass }}" aria-haspopup="listbox" aria-expanded="false">
         <span class="material-symbols-outlined text-primary/80 text-lg shrink-0">location_on</span>
         <span class="flex min-w-0 flex-1 flex-col items-start leading-tight">
            <span class="text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant/70">Site</span>
            <span id="ab-site-label" class="truncate text-xs font-semibold text-on-surface max-w-[8rem]">{{ $siteLabel }}</span>
         </span>
         <span class="material-symbols-outlined text-on-surface-variant/50 text-lg shrink-0">expand_more</span>
      </button>
      <div class="{{ $dropdownClass }}" data-ab-filter-menu role="listbox">
         <button type="button" class="{{ $optionClass }} ab-filter-option" data-name="site" data-value="" data-label="Semua Site">Semua Site</button>
         @foreach($filterOptions['sites'] as $site)
         <button type="button" class="{{ $optionClass }} ab-filter-option" data-name="site" data-value="{{ $site }}" data-label="{{ $site }}">{{ $site }}</button>
         @endforeach
      </div>
      <input type="hidden" name="site" id="ab-filter-site" value="{{ $filters['site'] }}"/>
   </div>

   {{-- Week --}}
   <div class="relative" data-ab-filter-wrap>
      <button type="button" data-ab-filter-toggle class="{{ $pickerBtnClass }}" aria-haspopup="listbox" aria-expanded="false">
         <span class="material-symbols-outlined text-primary/80 text-lg shrink-0">date_range</span>
         <span class="flex min-w-0 flex-1 flex-col items-start leading-tight">
            <span class="text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant/70">Minggu</span>
            <span id="ab-week-label" class="truncate text-xs font-semibold text-on-surface">{{ $weekLabel }}</span>
         </span>
         <span class="material-symbols-outlined text-on-surface-variant/50 text-lg shrink-0">expand_more</span>
      </button>
      <div class="{{ $dropdownClass }}" data-ab-filter-menu role="listbox">
         <button type="button" class="{{ $optionClass }} ab-filter-option" data-name="week" data-value="" data-label="Semua Minggu">Semua Minggu</button>
         @foreach($filterOptions['weeks'] as $week)
         <button type="button" class="{{ $optionClass }} ab-filter-option" data-name="week" data-value="{{ $week }}" data-label="{{ $week }}">{{ $week }}</button>
         @endforeach
      </div>
      <input type="hidden" name="week" id="ab-filter-week" value="{{ $filters['week'] }}"/>
   </div>

   {{-- Year --}}
   <div class="relative" data-ab-filter-wrap>
      <button type="button" data-ab-filter-toggle class="{{ $pickerBtnClass }}" aria-haspopup="listbox" aria-expanded="false">
         <span class="material-symbols-outlined text-primary/80 text-lg shrink-0">calendar_month</span>
         <span class="flex min-w-0 flex-1 flex-col items-start leading-tight">
            <span class="text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant/70">Tahun</span>
            <span id="ab-year-label" class="truncate text-xs font-semibold text-on-surface">{{ $yearLabel }}</span>
         </span>
         <span class="material-symbols-outlined text-on-surface-variant/50 text-lg shrink-0">expand_more</span>
      </button>
      <div class="{{ $dropdownClass }}" data-ab-filter-menu role="listbox">
         <button type="button" class="{{ $optionClass }} ab-filter-option" data-name="year" data-value="" data-label="Semua Tahun">Semua Tahun</button>
         @foreach($filterOptions['years'] as $year)
         <button type="button" class="{{ $optionClass }} ab-filter-option" data-name="year" data-value="{{ $year }}" data-label="{{ $year }}">{{ $year }}</button>
         @endforeach
      </div>
      <input type="hidden" name="year" id="ab-filter-year" value="{{ $filters['year'] }}"/>
   </div>

   {{-- Perusahaan --}}
   <div class="relative" data-ab-filter-wrap>
      <button type="button" data-ab-filter-toggle class="{{ $pickerBtnClass }}" aria-haspopup="listbox" aria-expanded="false">
         <span class="material-symbols-outlined text-primary/80 text-lg shrink-0">domain</span>
         <span class="flex min-w-0 flex-1 flex-col items-start leading-tight">
            <span class="text-[9px] font-semibold uppercase tracking-wider text-on-surface-variant/70">Perusahaan</span>
            <span id="ab-perusahaan-label" class="truncate text-xs font-semibold text-on-surface max-w-[8rem]">{{ $perusahaanLabel }}</span>
         </span>
         <span class="material-symbols-outlined text-on-surface-variant/50 text-lg shrink-0">expand_more</span>
      </button>
      <div class="{{ $dropdownClass }}" data-ab-filter-menu role="listbox">
         <button type="button" class="{{ $optionClass }} ab-filter-option" data-name="perusahaan" data-value="" data-label="Semua Perusahaan">Semua Perusahaan</button>
         @foreach($filterOptions['perusahaan'] as $perusahaan)
         <button type="button" class="{{ $optionClass }} ab-filter-option" data-name="perusahaan" data-value="{{ $perusahaan }}" data-label="{{ $perusahaan }}">{{ $perusahaan }}</button>
         @endforeach
      </div>
      <input type="hidden" name="perusahaan" id="ab-filter-perusahaan" value="{{ $filters['perusahaan'] }}"/>
   </div>
</form>

@push('scripts')
<script>
(function () {
   var form = document.getElementById('ab-filter-form');
   if (!form) return;

   var labelMap = {
      site: 'ab-site-label',
      week: 'ab-week-label',
      year: 'ab-year-label',
      perusahaan: 'ab-perusahaan-label'
   };

   function closeAllMenus(except) {
      form.querySelectorAll('[data-ab-filter-menu]').forEach(function (menu) {
         if (menu === except) return;
         menu.classList.add('hidden');
         var toggle = menu.parentElement && menu.parentElement.querySelector('[data-ab-filter-toggle]');
         if (toggle) toggle.setAttribute('aria-expanded', 'false');
      });
   }

   form.querySelectorAll('[data-ab-filter-toggle]').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
         e.stopPropagation();
         var menu = btn.parentElement.querySelector('[data-ab-filter-menu]');
         var isOpen = menu && !menu.classList.contains('hidden');
         closeAllMenus();
         if (menu && !isOpen) {
            menu.classList.remove('hidden');
            btn.setAttribute('aria-expanded', 'true');
         }
      });
   });

   form.querySelectorAll('.ab-filter-option').forEach(function (opt) {
      opt.addEventListener('click', function () {
         var name = opt.getAttribute('data-name');
         var value = opt.getAttribute('data-value') || '';
         var label = opt.getAttribute('data-label') || '';
         var input = form.querySelector('[name="' + name + '"]');
         if (input) input.value = value;
         var labelId = labelMap[name];
         var labelEl = labelId ? document.getElementById(labelId) : null;
         if (labelEl) labelEl.textContent = label;
         closeAllMenus();
         form.submit();
      });
   });

   document.addEventListener('click', function () {
      closeAllMenus();
   });
})();
</script>
@endpush
