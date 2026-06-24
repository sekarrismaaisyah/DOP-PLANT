@php
   $item = $item ?? [];
   $index = $index ?? 0;
@endphp
<div class="ds-item-row rounded-xl border border-outline-variant/20 p-4 bg-white/80" data-index="{{ $index }}">
   <div class="flex items-center justify-between mb-3">
      <span class="text-xs font-bold uppercase text-primary tracking-wide">Item #{{ is_numeric($index) ? ((int)$index + 1) : '' }}</span>
      <button type="button" class="ds-remove-item text-error text-xs font-bold hover:underline">Hapus</button>
   </div>
   <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Section Kerja *</label>
         <select name="items[{{ $index }}][section_name]" required class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
            @foreach($sectionOptions as $sec)
            <option value="{{ $sec }}" @selected(($item['section_name'] ?? '') === $sec)>{{ $sec }}</option>
            @endforeach
         </select>
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Kode Unit</label>
         <input type="text" name="items[{{ $index }}][unit_code]" value="{{ $item['unit_code'] ?? '' }}" placeholder="N/A" class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Kategori Unit *</label>
         <select name="items[{{ $index }}][unit_category]" required class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
            @foreach($unitCategories as $cat)
            <option value="{{ $cat }}" @selected(($item['unit_category'] ?? '') === $cat)>{{ $cat }}</option>
            @endforeach
         </select>
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Lokasi *</label>
         <input type="text" name="items[{{ $index }}][location]" value="{{ $item['location'] ?? '' }}" required class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
      </div>
      <div class="md:col-span-2">
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Detail Pekerjaan *</label>
         <textarea name="items[{{ $index }}][job_detail]" required rows="2" class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">{{ $item['job_detail'] ?? '' }}</textarea>
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Izin Kerja</label>
         <input type="text" name="items[{{ $index }}][work_permit]" value="{{ $item['work_permit'] ?? 'N/A' }}" class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Alat Bantu (koma)</label>
         <input type="text" name="items[{{ $index }}][tools]" value="{{ $item['tools'] ?? '' }}" class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Pekerja (koma)</label>
         <input type="text" name="items[{{ $index }}][workers]" value="{{ $item['workers'] ?? '' }}" class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">CCTV</label>
         <input type="text" name="items[{{ $index }}][cctv]" value="{{ $item['cctv'] ?? '' }}" class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Group Leader (L1)</label>
         <input type="text" name="items[{{ $index }}][group_leader]" value="{{ $item['group_leader'] ?? '' }}" class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Section Head (L2)</label>
         <input type="text" name="items[{{ $index }}][section_head]" value="{{ $item['section_head'] ?? '' }}" class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">SHE Leader (L3)</label>
         <input type="text" name="items[{{ $index }}][she_leader]" value="{{ $item['she_leader'] ?? '' }}" class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">Dept. Head (L4)</label>
         <input type="text" name="items[{{ $index }}][dept_head]" value="{{ $item['dept_head'] ?? '' }}" class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
      </div>
      <div>
         <label class="block text-[10px] font-bold text-on-surface-variant mb-1">PJA BC</label>
         <input type="text" name="items[{{ $index }}][pja_bc]" value="{{ $item['pja_bc'] ?? '' }}" class="w-full rounded-lg border border-outline-variant/30 px-2 py-1.5 text-sm">
      </div>
   </div>
</div>
