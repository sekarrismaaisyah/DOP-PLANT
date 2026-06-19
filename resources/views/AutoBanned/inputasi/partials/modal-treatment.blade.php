<div id="ab-inputasi-treatment-modal" class="fixed inset-0 z-[200] hidden" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="ab-inputasi-treatment-modal-title">
   <div class="fixed inset-0 bg-black/40 backdrop-blur-[1px]" data-ab-inputasi-close></div>
   <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6 overflow-y-auto pointer-events-none">
      <div class="pointer-events-auto relative w-full max-w-3xl my-auto flex max-h-[min(92vh,820px)] flex-col rounded-2xl bg-white shadow-card-heavy">
         <div class="flex shrink-0 items-center justify-between border-b border-outline-variant/20 px-6 py-4">
            <div>
               <h4 id="ab-inputasi-treatment-modal-title" class="font-headline font-bold text-lg text-on-background">Upload Evidence Treatment</h4>
               <p class="text-xs text-on-surface-variant mt-0.5">Lampirkan bukti tindakan perbaikan untuk SID yang terbanned</p>
            </div>
            <button type="button" class="rounded-lg p-2 text-on-surface-variant hover:bg-surface-container-high" data-ab-inputasi-close aria-label="Tutup">
               <span class="material-symbols-outlined">close</span>
            </button>
         </div>
         @include('AutoBanned.inputasi.partials.tab-treatment')
      </div>
   </div>
</div>
