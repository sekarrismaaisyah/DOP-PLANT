<form method="POST" action="#" id="ab-inputasi-lv-form" class="flex min-h-0 flex-1 flex-col">
   @csrf
   <input type="hidden" name="tipe" value="lv"/>

   <div class="min-h-0 flex-1 overflow-y-auto px-6 py-5 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Shift</label>
            <div class="flex h-[42px] items-center rounded-xl border border-outline-variant/20 bg-[#eef2ff] px-3 text-sm font-bold text-primary">
               Shift 1
            </div>
            <p class="mt-1 text-[11px] text-on-surface-variant">06:00–18:00 = Shift 1 · 18:00–06:00 = Shift 2</p>
         </div>

         <div>
            <label for="lv-status" class="block text-xs font-bold text-on-surface-variant mb-1">Status <span class="text-error">*</span></label>
            <select id="lv-status" name="status" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
               <option value="">Pilih Status</option>
               <option value="schedule">Schedule</option>
               <option value="unschedule">Unschedule</option>
            </select>
         </div>

         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Check-in</label>
            <div class="flex h-[42px] items-center rounded-xl border border-outline-variant/20 bg-[#f8fafc] px-3 text-sm font-medium text-on-surface">
               {{ now()->format('d M Y H:i') }}
            </div>
            <p class="mt-1 text-[11px] text-on-surface-variant">Tanggal & jam otomatis saat simpan</p>
         </div>

         <div>
            <label class="block text-xs font-bold text-on-surface-variant mb-1">Creator</label>
            <div class="flex h-[42px] items-center rounded-xl border border-outline-variant/20 bg-[#f8fafc] px-3 text-sm font-medium text-on-surface">
               —
            </div>
         </div>

         <div class="md:col-span-2">
            <label for="lv-control-room" class="block text-xs font-bold text-on-surface-variant mb-1">Control Room <span class="text-error">*</span></label>
            <input type="text" id="lv-control-room" name="control_room" required placeholder="Nama control room" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
            <p class="mt-1 text-[11px] text-on-surface-variant">Diisi otomatis dari penugasan pengawas control room Anda</p>
         </div>

         <div>
            <label for="lv-nama-driver" class="block text-xs font-bold text-on-surface-variant mb-1">Nama Driver <span class="text-error">*</span></label>
            <input type="text" id="lv-nama-driver" name="nama_driver" autocomplete="off" required placeholder="Nama driver" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
         </div>

         <div class="md:col-span-2">
            <label for="lv-no-lambung" class="block text-xs font-bold text-on-surface-variant mb-1">No Unit <span class="text-error">*</span></label>
            <input type="text" id="lv-no-lambung" name="no_lambung" autocomplete="off" required placeholder="No lambung" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
         </div>

         <div>
            <label for="lv-lokasi" class="block text-xs font-bold text-on-surface-variant mb-1">Lokasi <span class="text-error">*</span></label>
            <input type="text" id="lv-lokasi" name="lokasi" autocomplete="off" required placeholder="Nama lokasi" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
         </div>

         <div class="md:col-span-2">
            <label for="lv-detail-lokasi" class="block text-xs font-bold text-on-surface-variant mb-1">Detail Lokasi</label>
            <input type="text" id="lv-detail-lokasi" name="detail_lokasi" autocomplete="off" placeholder="Detail lokasi (opsional)" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
         </div>

         <div class="md:col-span-2 lg:col-span-3">
            <label for="lv-aktivitas" class="block text-xs font-bold text-on-surface-variant mb-1">Aktivitas</label>
            <input type="text" id="lv-aktivitas" name="aktivitas" autocomplete="off" placeholder="Aktivitas (opsional)" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
         </div>
      </div>

      <div>
         <label for="lv-catatan" class="block text-xs font-bold text-on-surface-variant mb-1">Catatan</label>
         <textarea id="lv-catatan" name="catatan" rows="3" placeholder="Catatan tambahan (opsional)" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"></textarea>
      </div>
   </div>

   <div class="shrink-0 flex flex-wrap justify-end gap-3 border-t border-outline-variant/20 bg-[#f8fafc] px-6 py-4">
      <button type="button" data-ab-inputasi-close class="inline-flex items-center justify-center rounded-xl border border-outline-variant/30 bg-white px-5 py-2.5 text-sm font-bold text-on-surface hover:bg-white">Batal</button>
      <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md hover:opacity-95">
         <span class="material-symbols-outlined text-lg">save</span>
         Simpan Inputasi LV
      </button>
   </div>
</form>
