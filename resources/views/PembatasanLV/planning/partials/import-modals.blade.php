@php
   $importErrors = session('import_errors', []);
   $openImportLv = request('modal') === 'import-lv';
   $openImportOrang = request('modal') === 'import-orang';
@endphp

<div id="plv-planning-import-lv-modal" class="fixed inset-0 z-[200] {{ $openImportLv ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 p-4" aria-modal="true" role="dialog">
   <div class="relative w-full max-w-lg rounded-2xl border border-outline-variant/20 bg-white shadow-2xl">
      <div class="border-b border-outline-variant/15 px-5 py-4">
         <h3 class="font-headline text-lg font-bold text-on-surface">Import Excel — Planning LV</h3>
         <p class="mt-1 text-[11px] text-on-surface-variant">Baris 1 = header. Unduh template terlebih dahulu. Kolom sama dengan form planning LV.</p>
      </div>
      <div class="px-5 py-4 space-y-4">
         <a href="{{ route('pembatasan-lv.planning.lv.template') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-[11px] font-bold shadow-sm hover:bg-surface-container-high">
            <span class="material-symbols-outlined text-base">download</span> Unduh template LV
         </a>
         @if($openImportLv && $importErrors !== [])
         <div class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-[11px] text-red-900">
            <ul class="list-disc pl-4 max-h-40 overflow-y-auto">@foreach($importErrors as $err)<li>{{ $err }}</li>@endforeach</ul>
         </div>
         @endif
         <form method="post" action="{{ route('pembatasan-lv.planning.lv.import') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input type="file" name="excel_file" accept=".xlsx,.xls" required class="block w-full text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-2 file:font-bold file:text-primary"/>
            <div class="flex justify-end gap-2">
               <button type="button" id="plv-planning-close-import-lv" class="rounded-xl border px-4 py-2 text-xs font-bold">Batal</button>
               <button type="submit" class="inline-flex items-center gap-1 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white"><span class="material-symbols-outlined text-base">upload_file</span> Import</button>
            </div>
         </form>
      </div>
   </div>
</div>

<div id="plv-planning-import-orang-modal" class="fixed inset-0 z-[200] {{ $openImportOrang ? 'flex' : 'hidden' }} items-center justify-center bg-black/40 p-4" aria-modal="true" role="dialog">
   <div class="relative w-full max-w-lg rounded-2xl border border-outline-variant/20 bg-white shadow-2xl">
      <div class="border-b border-outline-variant/15 px-5 py-4">
         <h3 class="font-headline text-lg font-bold text-on-surface">Import Excel — Planning Orang</h3>
         <p class="mt-1 text-[11px] text-on-surface-variant">Baris 1 = header. Unduh template terlebih dahulu. Kolom sama dengan form planning orang.</p>
      </div>
      <div class="px-5 py-4 space-y-4">
         <a href="{{ route('pembatasan-lv.planning.orang.template') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-[11px] font-bold shadow-sm hover:bg-surface-container-high">
            <span class="material-symbols-outlined text-base">download</span> Unduh template Orang
         </a>
         @if($openImportOrang && $importErrors !== [])
         <div class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-[11px] text-red-900">
            <ul class="list-disc pl-4 max-h-40 overflow-y-auto">@foreach($importErrors as $err)<li>{{ $err }}</li>@endforeach</ul>
         </div>
         @endif
         <form method="post" action="{{ route('pembatasan-lv.planning.orang.import') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <input type="file" name="excel_file" accept=".xlsx,.xls" required class="block w-full text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-primary/10 file:px-3 file:py-2 file:font-bold file:text-primary"/>
            <div class="flex justify-end gap-2">
               <button type="button" id="plv-planning-close-import-orang" class="rounded-xl border px-4 py-2 text-xs font-bold">Batal</button>
               <button type="submit" class="inline-flex items-center gap-1 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white"><span class="material-symbols-outlined text-base">upload_file</span> Import</button>
            </div>
         </form>
      </div>
   </div>
</div>

@push('scripts')
<script>
(function () {
   function bindImport(openBtnId, modalId, closeBtnId) {
      var modal = document.getElementById(modalId);
      var openBtn = document.getElementById(openBtnId);
      var closeBtn = document.getElementById(closeBtnId);
      if (!modal || !openBtn) return;
      function openM() { modal.classList.remove('hidden'); modal.classList.add('flex'); }
      function closeM() { modal.classList.add('hidden'); modal.classList.remove('flex'); }
      openBtn.addEventListener('click', openM);
      if (closeBtn) closeBtn.addEventListener('click', closeM);
      modal.addEventListener('click', function (e) { if (e.target === modal) closeM(); });
   }
   bindImport('plv-planning-open-import-lv', 'plv-planning-import-lv-modal', 'plv-planning-close-import-lv');
   bindImport('plv-planning-open-import-orang', 'plv-planning-import-orang-modal', 'plv-planning-close-import-orang');
})();
</script>
@endpush
