@php
   $defaultWeek = $filters['week'] ?? ($period['week'] ?? '');
   $defaultYear = $filters['year'] ?? ($period['year'] ?? '');
   $prefillSid = $prefillSid ?? '';
   $allowedMimes = config('auto_banned.treatment.allowed_mimes', ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xlsx', 'xls']);
   $acceptAttr = implode(',', array_map(
      static fn (string $mime): string => '.'.$mime,
      $allowedMimes
   ));
   $maxUploadMb = (int) ceil(((int) config('auto_banned.treatment.max_upload_kb', 10240)) / 1024);
@endphp

<form
   id="ab-treatment-form"
   method="POST"
   action="{{ route('auto-banned.treatment-evidence.store') }}"
   enctype="multipart/form-data"
   class="flex min-h-0 flex-1 flex-col"
>
   @csrf

   <div class="min-h-0 flex-1 overflow-y-auto px-6 py-5 space-y-5">
      <div class="rounded-xl border border-primary/10 bg-primary/[0.04] px-4 py-3 text-xs text-on-surface-variant leading-relaxed">
         Upload bukti treatment (evidence) untuk SID yang terbanned. File akan disimpan sebagai pengajuan unban dengan status <strong>Menunggu Review</strong>.
      </div>

      @if($errors->any())
      <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
         <ul class="list-disc pl-4 space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
         </ul>
      </div>
      @endif

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
         <div class="sm:col-span-2">
            <label for="ab-treatment-sid" class="block text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">SID <span class="text-red-500">*</span></label>
            <div class="flex gap-2">
               <input
                  type="text"
                  id="ab-treatment-sid"
                  name="sid"
                  value="{{ old('sid', $prefillSid) }}"
                  required
                  maxlength="64"
                  placeholder="Contoh: U8WAP"
                  class="flex-1 rounded-xl border border-outline-variant/25 bg-[#f8fafc] px-3 py-2.5 text-sm font-mono font-semibold uppercase text-on-surface focus:border-primary/30 focus:ring-2 focus:ring-primary/10"
               />
               <button type="button" id="ab-treatment-lookup-btn" class="shrink-0 inline-flex items-center gap-1 rounded-xl border border-primary/20 bg-white px-3 py-2.5 text-xs font-bold text-primary hover:bg-primary/[0.04]">
                  <span class="material-symbols-outlined text-base">search</span>
                  Cek SID
               </button>
            </div>
            <p id="ab-treatment-lookup-msg" class="mt-1.5 text-[11px] text-on-surface-variant"></p>
         </div>
         <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Periode</label>
            <div class="rounded-xl border border-outline-variant/20 bg-[#f8fafc] px-3 py-2.5 text-sm font-semibold text-on-background">
               {{ $defaultWeek ?: '—' }} · {{ $defaultYear ?: '—' }}
            </div>
            <input type="hidden" name="week" value="{{ old('week', $defaultWeek) }}"/>
            <input type="hidden" name="year" value="{{ old('year', $defaultYear) }}"/>
         </div>
      </div>

      <div id="ab-treatment-preview" class="hidden rounded-xl border border-outline-variant/15 bg-[#fafbfc] p-4">
         <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-2">Data Karyawan</p>
         <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div>
               <p class="text-[10px] text-on-surface-variant">Nama</p>
               <p id="ab-treatment-karyawan" class="font-semibold text-on-background">—</p>
            </div>
            <div>
               <p class="text-[10px] text-on-surface-variant">Perusahaan</p>
               <p id="ab-treatment-perusahaan" class="font-semibold text-on-background">—</p>
            </div>
            <div>
               <p class="text-[10px] text-on-surface-variant">Site</p>
               <p id="ab-treatment-site" class="font-semibold text-on-background">—</p>
            </div>
            <div>
               <p class="text-[10px] text-on-surface-variant">Alasan Banned</p>
               <p id="ab-treatment-reason" class="font-semibold text-on-background">—</p>
            </div>
         </div>
      </div>

      <div id="ab-treatment-scr-wrap" class="hidden">
         <label for="ab-treatment-scr-select" class="block text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">
            Record Daily Banned <span class="text-red-500">*</span>
         </label>
         <select
            id="ab-treatment-scr-select"
            name="scr_daily_banned_id"
            class="w-full rounded-xl border border-outline-variant/25 bg-[#f8fafc] px-3 py-2.5 text-sm text-on-surface focus:border-primary/30 focus:ring-2 focus:ring-primary/10"
         >
            <option value="">— Pilih setelah cek SID —</option>
         </select>
         <p class="mt-1.5 text-[11px] text-on-surface-variant">Pilih record banned harian yang ingin diajukan unban.</p>
      </div>

      <div>
         <label for="ab-treatment-alasan" class="block text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Ringkasan Treatment <span class="text-red-500">*</span></label>
         <textarea
            id="ab-treatment-alasan"
            name="alasan_pengajuan"
            rows="4"
            required
            maxlength="2000"
            placeholder="Jelaskan tindakan perbaikan / treatment yang telah dilakukan…"
            class="w-full rounded-xl border border-outline-variant/25 bg-[#f8fafc] px-3 py-2.5 text-sm text-on-surface focus:border-primary/30 focus:ring-2 focus:ring-primary/10"
         >{{ old('alasan_pengajuan') }}</textarea>
      </div>

      <div>
         <label for="ab-treatment-file" class="block text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">File Evidence <span class="text-red-500">*</span></label>
         <input
            type="file"
            id="ab-treatment-file"
            name="evidence_file"
            required
            accept="{{ $acceptAttr }}"
            class="block w-full text-sm text-on-surface-variant file:mr-4 file:rounded-lg file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-xs file:font-bold file:text-primary hover:file:bg-primary/15"
         />
         <p class="mt-1.5 text-[11px] text-on-surface-variant">
            PDF, gambar, Word, atau Excel. Maks. {{ $maxUploadMb }} MB.
         </p>
      </div>
   </div>

   <div class="shrink-0 flex items-center justify-end gap-3 border-t border-outline-variant/20 px-6 py-4 bg-[#fafbfc]/80">
      <button type="button" data-ab-inputasi-close class="rounded-xl border border-outline-variant/25 px-4 py-2.5 text-sm font-semibold text-on-surface-variant hover:bg-white">
         Batal
      </button>
      <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:opacity-95">
         <span class="material-symbols-outlined text-lg">upload_file</span>
         Upload Evidence
      </button>
   </div>
</form>
