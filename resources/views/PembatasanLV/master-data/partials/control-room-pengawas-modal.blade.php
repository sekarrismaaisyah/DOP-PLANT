<div id="plv-cr-pengawas-modal" class="fixed inset-0 z-[200] hidden" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="plv-cr-pengawas-modal-title">
   <div class="fixed inset-0 bg-black/40 backdrop-blur-[1px]" data-plv-cr-pengawas-close></div>
   <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 overflow-y-auto pointer-events-none">
      <div class="pointer-events-auto relative w-full max-w-lg my-auto flex max-h-[min(90vh,720px)] flex-col rounded-2xl bg-white shadow-card-heavy">
         <div class="flex shrink-0 items-center justify-between border-b border-outline-variant/20 px-6 py-4">
            <h4 id="plv-cr-pengawas-modal-title" class="font-headline font-bold text-lg text-on-background">Tambah Pengawas</h4>
            <button type="button" class="rounded-lg p-2 text-on-surface-variant hover:bg-surface-container-high" data-plv-cr-pengawas-close aria-label="Tutup">
               <span class="material-symbols-outlined">close</span>
            </button>
         </div>
         <form id="plv-cr-pengawas-form" class="flex min-h-0 flex-1 flex-col">
            <input type="hidden" id="plv-cr-pengawas-id" name="id" value=""/>
            <div class="min-h-0 flex-1 overflow-y-auto px-6 py-5 space-y-4">
               <div id="plv-cr-pengawas-form-errors" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900"></div>
               <div>
                  <label for="plv-cr-pengawas-room" class="block text-xs font-bold text-on-surface-variant mb-1">Control Room <span class="text-error">*</span></label>
                  <input id="plv-cr-pengawas-room" type="text" name="control_room" required maxlength="255" list="plv-cr-pengawas-room-list" placeholder="Nama control room" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
                  <datalist id="plv-cr-pengawas-room-list">
                     @foreach($controlRoomOptions as $room)
                     <option value="{{ $room }}"></option>
                     @endforeach
                  </datalist>
               </div>
               <div>
                  <label for="plv-cr-pengawas-nama" class="block text-xs font-bold text-on-surface-variant mb-1">Nama Pengawas <span class="text-error">*</span></label>
                  <input id="plv-cr-pengawas-nama" type="text" name="nama_pengawas" required maxlength="255" placeholder="Nama lengkap pengawas" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               </div>
               <div>
                  <label for="plv-cr-pengawas-email" class="block text-xs font-bold text-on-surface-variant mb-1">Email</label>
                  <input id="plv-cr-pengawas-email" type="email" name="email_pengawas" maxlength="255" placeholder="email@example.com" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               </div>
               <div>
                  <label for="plv-cr-pengawas-hp" class="block text-xs font-bold text-on-surface-variant mb-1">No. HP</label>
                  <input id="plv-cr-pengawas-hp" type="text" name="no_hp_pengawas" maxlength="255" placeholder="08xxxxxxxxxx" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"/>
               </div>
               <div>
                  <label for="plv-cr-pengawas-keterangan" class="block text-xs font-bold text-on-surface-variant mb-1">Keterangan</label>
                  <textarea id="plv-cr-pengawas-keterangan" name="keterangan" rows="3" maxlength="2000" placeholder="Shift, jabatan, dll (opsional)" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15"></textarea>
               </div>
            </div>
            <div class="flex shrink-0 justify-end gap-3 border-t border-outline-variant/20 bg-[#f8fafc] px-6 py-4">
               <button type="button" class="rounded-xl border border-outline-variant/30 bg-white px-5 py-2.5 text-sm font-bold text-on-surface hover:bg-white" data-plv-cr-pengawas-close>Batal</button>
               <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-md hover:opacity-95">
                  <span class="material-symbols-outlined text-lg">save</span>
                  Simpan
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
