<div id="ab-master-sod-modal" class="fixed inset-0 z-[200] hidden" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="ab-master-sod-modal-title">
   <div class="fixed inset-0 bg-black/40 backdrop-blur-[1px]" data-ab-master-sod-close></div>
   <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 overflow-y-auto pointer-events-none">
      <div class="pointer-events-auto relative w-full max-w-lg my-auto flex max-h-[min(90vh,720px)] flex-col rounded-2xl bg-white shadow-card-heavy">
         <div class="flex shrink-0 items-center justify-between border-b border-outline-variant/20 px-6 py-4">
            <h4 id="ab-master-sod-modal-title" class="font-headline font-bold text-lg text-on-background">Tambah Master SOD</h4>
            <button type="button" class="rounded-lg p-2 text-on-surface-variant hover:bg-surface-container-high" data-ab-master-sod-close aria-label="Tutup">
               <span class="material-symbols-outlined">close</span>
            </button>
         </div>
         <form id="ab-master-sod-form" class="flex min-h-0 flex-1 flex-col">
            <input type="hidden" id="ab-master-sod-id" name="id" value=""/>
            <div id="ab-master-sod-form-errors" class="hidden mx-6 mt-4 rounded-xl border border-error/30 bg-error/5 px-4 py-3 text-xs font-medium text-error"></div>
            <div class="min-h-0 flex-1 overflow-y-auto px-6 py-5 space-y-4">
               <div>
                  <label for="ab-master-sod-nama" class="block text-xs font-bold text-on-surface-variant mb-1">Nama <span class="text-error">*</span></label>
                  <input id="ab-master-sod-nama" type="text" name="nama" required maxlength="255" placeholder="Nama SOD" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               </div>
               <div>
                  <label for="ab-master-sod-site" class="block text-xs font-bold text-on-surface-variant mb-1">Site <span class="text-error">*</span></label>
                  <input id="ab-master-sod-site" type="text" name="site" required maxlength="255" placeholder="Nama site" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               </div>
               <div>
                  <label for="ab-master-sod-hp" class="block text-xs font-bold text-on-surface-variant mb-1">No. HP <span class="text-error">*</span></label>
                  <input id="ab-master-sod-hp" type="text" name="no_hp" required maxlength="32" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               </div>
            </div>
            <div class="flex shrink-0 justify-end gap-3 border-t border-outline-variant/20 bg-[#f8fafc] px-6 py-4">
               <button type="button" class="rounded-xl border border-outline-variant/30 bg-white px-5 py-2.5 text-sm font-bold text-on-surface hover:bg-white" data-ab-master-sod-close>Batal</button>
               <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md hover:opacity-95">
                  <span class="material-symbols-outlined text-lg">save</span>
                  Simpan
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
