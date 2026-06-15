@php
   $openImportModal = request('modal') === 'import';
   $importErrors = session('import_errors', []);
@endphp

<div id="plv-aktivitas-import-modal" class="fixed inset-0 z-[200] {{ $openImportModal ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 p-4" aria-modal="true" role="dialog" aria-labelledby="plv-aktivitas-import-modal-title" aria-hidden="{{ $openImportModal ? 'false' : 'true' }}">
   <div class="relative w-full max-w-lg rounded-2xl border border-outline-variant/20 bg-white shadow-2xl">
      <div class="border-b border-outline-variant/15 px-5 py-4">
         <h3 id="plv-aktivitas-import-modal-title" class="font-headline text-lg font-bold text-on-surface">Import Excel — Master Aktivitas</h3>
         <p class="mt-1 text-[11px] leading-relaxed text-on-surface-variant">Baris pertama berisi header kolom. Unduh format Excel terlebih dahulu agar struktur kolom sesuai. Data baru akan <strong>ditambahkan</strong> (tidak menimpa data lama).</p>
      </div>
      <div class="max-h-[min(70vh,520px)] overflow-y-auto px-5 py-4 space-y-4">
         <div class="flex flex-wrap gap-2">
            <a href="{{ route('pembatasan-lv.master-data.aktivitas.template') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-[11px] font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">download</span> Unduh format Excel
            </a>
         </div>
         @if ($importErrors !== [])
            <div class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-[11px] font-medium text-red-900">
               <p class="font-bold mb-1">Perbaiki isi file:</p>
               <ul class="list-disc space-y-1 pl-4 max-h-40 overflow-y-auto">
                  @foreach ($importErrors as $err)
                     <li>{{ $err }}</li>
                  @endforeach
               </ul>
            </div>
         @endif
         <form method="post" action="{{ route('pembatasan-lv.master-data.aktivitas.import') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
               <label for="plv-aktivitas-excel-file" class="mb-1.5 block text-[11px] font-bold text-on-surface-variant">File Excel (.xlsx / .xls, maks. 10 MB)</label>
               <input id="plv-aktivitas-excel-file" type="file" name="excel_file" accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" required class="@error('excel_file') border-red-400 @enderror block w-full text-xs text-on-surface file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-2 file:font-bold file:text-primary">
               @error('excel_file')
                  <p class="mt-1 text-[11px] font-semibold text-error">{{ $message }}</p>
               @enderror
            </div>
            <div class="flex flex-wrap justify-end gap-2 pt-1">
               <button type="button" id="plv-aktivitas-close-import-modal" class="rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold text-on-surface shadow-sm hover:bg-surface-container-high">Batal</button>
               <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md hover:opacity-95">
                  <span class="material-symbols-outlined text-base">upload_file</span> Unggah &amp; Import
               </button>
            </div>
         </form>
      </div>
   </div>
</div>

@push('scripts')
<script>
(function () {
   var importModal = document.getElementById('plv-aktivitas-import-modal');
   var openImportBtn = document.getElementById('plv-aktivitas-import-btn');
   var closeImportBtn = document.getElementById('plv-aktivitas-close-import-modal');
   if (!importModal || !openImportBtn) return;

   function openImportM() {
      importModal.classList.remove('hidden');
      importModal.classList.add('flex');
      importModal.setAttribute('aria-hidden', 'false');
   }

   function closeImportM() {
      importModal.classList.add('hidden');
      importModal.classList.remove('flex');
      importModal.setAttribute('aria-hidden', 'true');
   }

   openImportBtn.addEventListener('click', openImportM);
   if (closeImportBtn) closeImportBtn.addEventListener('click', closeImportM);
   importModal.addEventListener('click', function (e) {
      if (e.target === importModal) closeImportM();
   });

   @if ($openImportModal)
   openImportM();
   @endif
})();
</script>
@endpush
