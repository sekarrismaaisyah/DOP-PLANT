<div id="plv-inputasi-orang-modal" class="fixed inset-0 z-[200] hidden" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="plv-inputasi-orang-modal-title">
   <div class="fixed inset-0 bg-black/40 backdrop-blur-[1px]" data-plv-inputasi-close></div>
   <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 overflow-y-auto pointer-events-none">
      <div class="pointer-events-auto relative w-full max-w-4xl my-auto flex max-h-[min(92vh,820px)] flex-col rounded-2xl bg-white shadow-card-heavy">
         <div class="flex shrink-0 items-center justify-between border-b border-outline-variant/20 px-6 py-4">
            <div>
               <h4 id="plv-inputasi-orang-modal-title" class="font-headline font-bold text-lg text-on-background">Inputasi Orang</h4>
               <p class="text-xs text-on-surface-variant mt-0.5">Pencatatan personel — data karyawan terisi otomatis dari SID</p>
            </div>
            <button type="button" class="rounded-lg p-2 text-on-surface-variant hover:bg-surface-container-high" data-plv-inputasi-close aria-label="Tutup">
               <span class="material-symbols-outlined">close</span>
            </button>
         </div>
         @include('PembatasanLV.inputasi.partials.tab-orang')
      </div>
   </div>
</div>
