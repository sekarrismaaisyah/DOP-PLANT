<div id="plv-aktivitas-modal" class="fixed inset-0 z-[200] hidden" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="plv-aktivitas-modal-title">
   <div class="fixed inset-0 bg-black/40 backdrop-blur-[1px]" data-plv-aktivitas-close></div>
   <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 overflow-y-auto pointer-events-none">
      <div class="pointer-events-auto relative w-full max-w-2xl my-auto flex max-h-[min(92vh,820px)] flex-col rounded-2xl bg-white shadow-card-heavy">
         <div class="flex shrink-0 items-center justify-between border-b border-outline-variant/20 px-6 py-4">
            <h4 id="plv-aktivitas-modal-title" class="font-headline font-bold text-lg text-on-background">Tambah Aktivitas Luar Kabin</h4>
            <button type="button" class="rounded-lg p-2 text-on-surface-variant hover:bg-surface-container-high" data-plv-aktivitas-close aria-label="Tutup">
               <span class="material-symbols-outlined">close</span>
            </button>
         </div>
         <form id="plv-aktivitas-form" class="flex min-h-0 flex-1 flex-col">
            <input type="hidden" id="plv-aktivitas-id" name="id" value=""/>
            <div class="min-h-0 flex-1 overflow-y-auto px-6 py-5 space-y-4">
               <div id="plv-aktivitas-form-errors" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900"></div>
               <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                  <div>
                     <label for="plv-aktivitas-site" class="block text-xs font-bold text-on-surface-variant mb-1">Site <span class="text-error">*</span></label>
                     <input id="plv-aktivitas-site" type="text" name="site" required maxlength="255" list="plv-aktivitas-site-list" placeholder="Nama site" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
                     <datalist id="plv-aktivitas-site-list">
                        @foreach($siteOptions as $site)
                        <option value="{{ $site }}"></option>
                        @endforeach
                     </datalist>
                  </div>
                  <div>
                     <label for="plv-aktivitas-perusahaan" class="block text-xs font-bold text-on-surface-variant mb-1">Perusahaan <span class="text-error">*</span></label>
                     <input id="plv-aktivitas-perusahaan" type="text" name="perusahaan" required maxlength="255" placeholder="Nama perusahaan" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
                  </div>
                  <div>
                     <label for="plv-aktivitas-departemen" class="block text-xs font-bold text-on-surface-variant mb-1">Departemen <span class="text-error">*</span></label>
                     <input id="plv-aktivitas-departemen" type="text" name="departemen" required maxlength="255" placeholder="Nama departemen" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
                  </div>
               </div>
               <div>
                  <label for="plv-aktivitas-kategori" class="block text-xs font-bold text-on-surface-variant mb-1">Kategori Aktivitas Pekerjaan di Luar Kabin <span class="text-error">*</span></label>
                  <textarea id="plv-aktivitas-kategori" name="kategori_aktivitas_luar_kabin" rows="3" required maxlength="5000" placeholder="Contoh: Mobilisasi karyawan" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"></textarea>
               </div>
               <div>
                  <label for="plv-aktivitas-detail" class="block text-xs font-bold text-on-surface-variant mb-1">Detail Aktivitas Pengoperasian LV <span class="text-error">*</span></label>
                  <textarea id="plv-aktivitas-detail" name="detail_aktivitas_pengoperasian_lv" rows="4" required maxlength="5000" placeholder="Contoh: Penjemputan dan Pengantaran Karyawan" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"></textarea>
               </div>
               <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                     <label for="plv-aktivitas-frekuensi" class="block text-xs font-bold text-on-surface-variant mb-1">Frekuensi Aktivitas dalam 1 Shift <span class="text-error">*</span></label>
                     <input id="plv-aktivitas-frekuensi" type="number" name="frekuensi_aktivitas_per_shift" required min="0" max="9999" value="0" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
                  </div>
                  <div>
                     <label for="plv-aktivitas-estimasi-lv" class="block text-xs font-bold text-on-surface-variant mb-1">Estimasi Jumlah LV beraktivitas dalam 1 Shift <span class="text-error">*</span></label>
                     <input id="plv-aktivitas-estimasi-lv" type="number" name="estimasi_jumlah_lv_per_shift" required min="0" max="9999" value="0" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
                  </div>
               </div>
            </div>
            <div class="flex shrink-0 justify-end gap-3 border-t border-outline-variant/20 bg-[#f8fafc] px-6 py-4">
               <button type="button" class="rounded-xl border border-outline-variant/30 bg-white px-5 py-2.5 text-sm font-bold text-on-surface hover:bg-white" data-plv-aktivitas-close>Batal</button>
               <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md hover:opacity-95">
                  <span class="material-symbols-outlined text-lg">save</span>
                  Simpan
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
