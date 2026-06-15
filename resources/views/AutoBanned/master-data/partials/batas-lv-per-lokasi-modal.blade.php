<div id="ab-batas-lv-modal" class="fixed inset-0 z-[200] hidden" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="ab-batas-lv-modal-title">
   <div class="fixed inset-0 bg-black/40 backdrop-blur-[1px]" data-ab-batas-lv-close></div>
   <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 overflow-y-auto pointer-events-none">
      <div class="pointer-events-auto relative w-full max-w-lg my-auto flex max-h-[min(90vh,720px)] flex-col rounded-2xl bg-white shadow-card-heavy">
         <div class="flex shrink-0 items-center justify-between border-b border-outline-variant/20 px-6 py-4">
            <h4 id="ab-batas-lv-modal-title" class="font-headline font-bold text-lg text-on-background">Tambah Batas LV</h4>
            <button type="button" class="rounded-lg p-2 text-on-surface-variant hover:bg-surface-container-high" data-ab-batas-lv-close aria-label="Tutup">
               <span class="material-symbols-outlined">close</span>
            </button>
         </div>
         <form id="ab-batas-lv-form" class="flex min-h-0 flex-1 flex-col">
            <input type="hidden" id="ab-batas-lv-id" name="id" value=""/>
            <div class="min-h-0 flex-1 overflow-y-auto px-6 py-5 space-y-4">
               <div>
                  <label for="ab-batas-lv-site" class="block text-xs font-bold text-on-surface-variant mb-1">Site <span class="text-error">*</span></label>
                  <input id="ab-batas-lv-site" type="text" name="site" required maxlength="255" placeholder="Nama site" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               </div>
               <div>
                  <label for="ab-batas-lv-lokasi" class="block text-xs font-bold text-on-surface-variant mb-1">Lokasi <span class="text-error">*</span></label>
                  <input id="ab-batas-lv-lokasi" type="text" name="lokasi" required maxlength="255" placeholder="Nama lokasi" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               </div>
               <div>
                  <label for="ab-batas-lv-detail" class="block text-xs font-bold text-on-surface-variant mb-1">Detail Lokasi</label>
                  <textarea id="ab-batas-lv-detail" name="detail_lokasi" rows="3" maxlength="2000" placeholder="Detail lokasi (opsional)" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"></textarea>
               </div>
               <div>
                  <label for="ab-batas-lv-batas" class="block text-xs font-bold text-on-surface-variant mb-1">Batas LV <span class="text-error">*</span></label>
                  <input id="ab-batas-lv-batas" type="number" name="batas_lv" required min="0" max="999999" value="0" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               </div>
            </div>
            <div class="flex shrink-0 justify-end gap-3 border-t border-outline-variant/20 bg-[#f8fafc] px-6 py-4">
               <button type="button" class="rounded-xl border border-outline-variant/30 bg-white px-5 py-2.5 text-sm font-bold text-on-surface hover:bg-white" data-ab-batas-lv-close>Batal</button>
               <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md hover:opacity-95">
                  <span class="material-symbols-outlined text-lg">save</span>
                  Simpan
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
