@php
   $ctx = $formContext ?? [];
   $oldOrang = old('tipe') === 'planning-orang';
   $controlRooms = collect($ctx['control_rooms'] ?? []);
   $selectedControlRoom = $oldOrang ? old('control_room', $ctx['control_room'] ?? '') : ($ctx['control_room'] ?? '');
   $defaultShift = (int) ($ctx['shift'] ?? 1);
@endphp

<form method="POST" action="{{ route('pembatasan-lv.planning.orang.store') }}" id="plv-planning-orang-form" class="flex min-h-0 flex-1 flex-col">
   @csrf
   <input type="hidden" name="tipe" value="planning-orang"/>

   <div class="min-h-0 flex-1 overflow-y-auto px-6 py-5 space-y-4">
      @if($errors->any() && old('tipe') === 'planning-orang')
      <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">
         <p class="font-semibold mb-1">Periksa input berikut:</p>
         <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $err)
               <li>{{ $err }}</li>
            @endforeach
         </ul>
      </div>
      @endif

      @if($controlRooms->isEmpty() && $selectedControlRoom === '')
      <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
         Akun Anda belum terdaftar sebagai pengawas control room. Hubungi admin untuk mendaftarkan email/nama Anda di Master Data → Pengawas Control Room.
      </div>
      @endif

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
         <div>
            <label for="plv-plan-orang-tanggal" class="block text-xs font-bold text-on-surface-variant mb-1">Tanggal Plan <span class="text-error">*</span></label>
            <input id="plv-plan-orang-tanggal" type="date" name="tanggal_plan" required value="{{ $oldOrang ? old('tanggal_plan') : now()->format('Y-m-d') }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
         </div>

         <div>
            <label for="plv-plan-orang-shift" class="block text-xs font-bold text-on-surface-variant mb-1">Shift <span class="text-error">*</span></label>
            <select id="plv-plan-orang-shift" name="shift" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               <option value="1" @selected($oldOrang ? (int) old('shift') === 1 : $defaultShift === 1)>Shift 1 (06:00–18:00)</option>
               <option value="2" @selected($oldOrang ? (int) old('shift') === 2 : $defaultShift === 2)>Shift 2 (18:00–06:00)</option>
            </select>
         </div>

         <div>
            <label for="plv-plan-orang-status" class="block text-xs font-bold text-on-surface-variant mb-1">Status <span class="text-error">*</span></label>
            <select id="plv-plan-orang-status" name="status" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               <option value="">Pilih Status</option>
               <option value="schedule" @selected($oldOrang && old('status') === 'schedule')>Schedule</option>
               <option value="unschedule" @selected($oldOrang && old('status') === 'unschedule')>Unschedule</option>
            </select>
         </div>

         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Creator</label>
            <div class="flex h-[42px] items-center rounded-xl border border-outline-variant/20 bg-[#f8fafc] px-3 text-sm font-medium text-on-surface">
               {{ $oldOrang ? old('creator_name', $ctx['creator_name'] ?? '—') : ($ctx['creator_name'] ?? '—') }}
            </div>
         </div>

         <div class="md:col-span-2">
            <label for="plv-plan-orang-control-room" class="block text-xs font-bold text-on-surface-variant mb-1">Control Room <span class="text-error">*</span></label>
            @if($controlRooms->count() <= 1)
            <div class="flex h-[42px] items-center rounded-xl border border-outline-variant/20 bg-[#f8fafc] px-3 text-sm font-medium text-on-surface">
               {{ $selectedControlRoom !== '' ? $selectedControlRoom : '— Tidak terdaftar sebagai pengawas control room —' }}
            </div>
            <input type="hidden" id="plv-plan-orang-control-room" name="control_room" value="{{ $selectedControlRoom }}"/>
            @else
            <select id="plv-plan-orang-control-room" name="control_room" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               @foreach($controlRooms as $room)
               <option value="{{ $room }}" @selected($selectedControlRoom === $room)>{{ $room }}</option>
               @endforeach
            </select>
            @endif
         </div>

         <div class="plv-plan-orang-combobox-wrap" data-plv-plan-orang-combobox data-url="{{ route('pembatasan-lv.inputasi.options.sid') }}" data-field="sid">
            <label class="block text-xs font-bold text-on-surface-variant mb-1">SID <span class="text-error">*</span></label>
            <div class="relative">
               <input type="text" name="sid" id="plv-plan-orang-sid" autocomplete="off" required value="{{ $oldOrang ? old('sid') : '' }}" placeholder="Cari kode SID…" class="plv-plan-orang-combobox-input w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               <ul class="plv-plan-orang-combobox-list absolute z-[210] mt-1 hidden max-h-52 w-full overflow-y-auto rounded-xl border border-outline-variant/30 bg-white py-1 shadow-lg"></ul>
            </div>
         </div>

         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Nama</label>
            <div id="plv-plan-orang-nama-display" class="flex min-h-[42px] items-center rounded-xl border border-outline-variant/20 bg-[#f8fafc] px-3 text-sm font-medium text-on-surface">{{ $oldOrang ? old('nama', '—') : '—' }}</div>
            <input type="hidden" name="nama" id="plv-plan-orang-nama" value="{{ $oldOrang ? old('nama') : '' }}"/>
         </div>

         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Perusahaan</label>
            <div id="plv-plan-orang-perusahaan-display" class="flex min-h-[42px] items-center rounded-xl border border-outline-variant/20 bg-[#f8fafc] px-3 text-sm font-medium text-on-surface">{{ $oldOrang ? old('nama_perusahaan', '—') : '—' }}</div>
            <input type="hidden" name="nama_perusahaan" id="plv-plan-orang-perusahaan" value="{{ $oldOrang ? old('nama_perusahaan') : '' }}"/>
         </div>

         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">NIK</label>
            <div id="plv-plan-orang-nik-display" class="flex min-h-[42px] items-center rounded-xl border border-outline-variant/20 bg-[#f8fafc] px-3 text-sm font-medium text-on-surface">{{ $oldOrang ? old('nik', '—') : '—' }}</div>
            <input type="hidden" name="nik" id="plv-plan-orang-nik" value="{{ $oldOrang ? old('nik') : '' }}"/>
         </div>

         <!-- <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Site</label>
            <div id="plv-plan-orang-site-display" class="flex min-h-[42px] items-center rounded-xl border border-outline-variant/20 bg-[#f8fafc] px-3 text-sm font-medium text-on-surface">{{ $oldOrang ? old('site', '—') : '—' }}</div>
            <input type="hidden" name="site" id="plv-plan-orang-site" value="{{ $oldOrang ? old('site') : '' }}"/>
         </div> -->

         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Dept</label>
            <div id="plv-plan-orang-dept-display" class="flex min-h-[42px] items-center rounded-xl border border-outline-variant/20 bg-[#f8fafc] px-3 text-sm font-medium text-on-surface">{{ $oldOrang ? old('dept', '—') : '—' }}</div>
            <input type="hidden" name="dept" id="plv-plan-orang-dept" value="{{ $oldOrang ? old('dept') : '' }}"/>
         </div>

         <div class="plv-plan-orang-combobox-wrap" data-plv-plan-orang-combobox data-url="{{ route('pembatasan-lv.inputasi.options.lokasi') }}" data-field="lokasi" data-target-detail="#plv-plan-orang-detail-lokasi-wrap">
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Lokasi <span class="text-error">*</span></label>
            <div class="relative">
               <input type="text" name="lokasi" autocomplete="off" required value="{{ $oldOrang ? old('lokasi') : '' }}" placeholder="Cari lokasi…" class="plv-plan-orang-combobox-input w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               <ul class="plv-plan-orang-combobox-list absolute z-[210] mt-1 hidden max-h-52 w-full overflow-y-auto rounded-xl border border-outline-variant/30 bg-white py-1 shadow-lg"></ul>
            </div>
         </div>

         <div id="plv-plan-orang-detail-lokasi-wrap" class="plv-plan-orang-combobox-wrap md:col-span-2" data-plv-plan-orang-combobox data-url="{{ route('pembatasan-lv.inputasi.options.detail-lokasi') }}" data-field="detail_lokasi" data-lokasi-param="lokasi">
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Detail Lokasi</label>
            <div class="relative">
               <input type="text" name="detail_lokasi" autocomplete="off" value="{{ $oldOrang ? old('detail_lokasi') : '' }}" placeholder="Pilih lokasi terlebih dahulu…" class="plv-plan-orang-combobox-input w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               <ul class="plv-plan-orang-combobox-list absolute z-[210] mt-1 hidden max-h-52 w-full overflow-y-auto rounded-xl border border-outline-variant/30 bg-white py-1 shadow-lg"></ul>
            </div>
         </div>

         <div class="plv-plan-orang-combobox-wrap md:col-span-2 lg:col-span-3" data-plv-plan-orang-combobox data-url="{{ route('pembatasan-lv.inputasi.options.aktivitas') }}" data-field="aktivitas" data-allow-custom="1">
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Aktivitas</label>
            <div class="relative">
               <input type="text" name="aktivitas" autocomplete="off" value="{{ $oldOrang ? old('aktivitas') : '' }}" placeholder="Pilih atau ketik aktivitas…" class="plv-plan-orang-combobox-input w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               <ul class="plv-plan-orang-combobox-list absolute z-[210] mt-1 hidden max-h-52 w-full overflow-y-auto rounded-xl border border-outline-variant/30 bg-white py-1 shadow-lg"></ul>
            </div>
         </div>
      </div>

      <div>
         <label for="plv-plan-orang-catatan" class="block text-xs font-bold text-on-surface-variant mb-1">Catatan</label>
         <textarea id="plv-plan-orang-catatan" name="catatan" rows="3" placeholder="Keterangan tambahan (opsional)" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">{{ $oldOrang ? old('catatan') : '' }}</textarea>
      </div>
   </div>

   <div class="shrink-0 flex flex-wrap justify-end gap-3 border-t border-outline-variant/20 bg-[#f8fafc] px-6 py-4">
      <button type="button" data-plv-planning-close class="inline-flex items-center justify-center rounded-xl border border-outline-variant/30 bg-white px-5 py-2.5 text-sm font-bold text-on-surface hover:bg-white">Batal</button>
      <button type="submit" id="plv-plan-orang-submit-btn" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md hover:opacity-95 disabled:cursor-not-allowed disabled:opacity-50" @disabled($controlRooms->isEmpty() && $selectedControlRoom === '')>
         <span class="material-symbols-outlined text-lg">save</span>
         Simpan Planning Orang
      </button>
   </div>
</form>

@push('scripts')
<script>
(function () {
   var form = document.getElementById('plv-planning-orang-form');
   if (!form) return;

   function escapeHtml(value) {
      return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
   }

   function debounce(fn, ms) {
      var timer;
      return function () { var args = arguments, ctx = this; clearTimeout(timer); timer = setTimeout(function () { fn.apply(ctx, args); }, ms); };
   }

   function displayOrDash(value) {
      return value && String(value).trim() !== '' ? String(value).trim() : '—';
   }

   function fillKaryawan(item) {
      var fields = ['nama', 'nama_perusahaan', 'nik', 'site', 'dept'];
      fields.forEach(function (key) {
         var val = item ? (item[key] || '') : '';
         var hidden = document.getElementById('plv-plan-orang-' + (key === 'nama_perusahaan' ? 'perusahaan' : key));
         var display = document.getElementById('plv-plan-orang-' + (key === 'nama_perusahaan' ? 'perusahaan' : key) + '-display');
         if (hidden) hidden.value = val;
         if (display) display.textContent = displayOrDash(val);
      });
   }

   form.querySelectorAll('[data-plv-plan-orang-combobox]').forEach(function (wrap) {
      var url = wrap.getAttribute('data-url');
      var field = wrap.getAttribute('data-field');
      var input = wrap.querySelector('.plv-plan-orang-combobox-input');
      var list = wrap.querySelector('.plv-plan-orang-combobox-list');
      var lokasiParam = wrap.getAttribute('data-lokasi-param');
      var detailTarget = wrap.getAttribute('data-target-detail');
      var lastItems = [];
      if (!url || !input || !list) return;

      function setValue(item) {
         if (field === 'sid' && item) {
            input.value = item.sid || item.label || item.id || '';
            fillKaryawan(item);
         } else if (item) {
            input.value = item.label || item.value || item.sid || '';
         }
         list.classList.add('hidden');
         if (detailTarget && field === 'lokasi') {
            var detailWrap = form.querySelector(detailTarget);
            if (detailWrap) {
               var detailInput = detailWrap.querySelector('input[name="detail_lokasi"]');
               if (detailInput) detailInput.value = '';
            }
         }
      }

      function renderList(items) {
         lastItems = items || [];
         if (!lastItems.length) {
            list.innerHTML = '<li class="px-3 py-2 text-xs text-on-surface-variant">Tidak ada data</li>';
            list.classList.remove('hidden');
            return;
         }
         list.innerHTML = lastItems.map(function (item, idx) {
            var label = escapeHtml(item.label || item.value || item.sid || item.nama || '');
            var sub = escapeHtml(item.subtitle || '');
            return '<li><button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-[#f1f5f9]" data-plv-plan-orang-idx="' + idx + '"><span class="font-medium text-on-background">' + label + '</span>' + (sub ? '<span class="block text-xs text-on-surface-variant">' + sub + '</span>' : '') + '</button></li>';
         }).join('');
         list.classList.remove('hidden');
      }

      var fetchOptions = debounce(function () {
         var q = input.value.trim();
         var fetchUrl = new URL(url, window.location.origin);
         if (q) fetchUrl.searchParams.set('q', q);
         if (lokasiParam) {
            var lokasiInput = form.querySelector('input[name="lokasi"]');
            if (lokasiInput && lokasiInput.value) fetchUrl.searchParams.set('lokasi', lokasiInput.value);
         }
         fetch(fetchUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.json(); })
            .then(function (json) { renderList(json.data || []); })
            .catch(function () { renderList([]); });
      }, 250);

      input.addEventListener('focus', fetchOptions);
      input.addEventListener('input', function () { if (field === 'sid') fillKaryawan(null); fetchOptions(); });
      input.addEventListener('blur', function () { setTimeout(function () { list.classList.add('hidden'); }, 150); });
      list.addEventListener('mousedown', function (e) {
         e.preventDefault();
         var btn = e.target.closest('[data-plv-plan-orang-idx]');
         if (!btn) return;
         var item = lastItems[parseInt(btn.getAttribute('data-plv-plan-orang-idx'), 10)];
         if (item) setValue(item);
      });
   });
})();
</script>
@endpush
