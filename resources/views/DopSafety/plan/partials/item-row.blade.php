@php
   $item = $item ?? [];
   $index = $index ?? 0;
@endphp
<tr class="ds-item-row align-top" data-index="{{ $index }}">
   <td class="px-2 py-2 border border-gray-200 text-center text-xs font-bold text-gray-700 bg-white">
      {{ is_numeric($index) ? ((int) $index + 1) : '' }}
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][unit_code]" value="{{ $item['unit_code'] ?? '' }}" placeholder="N/A" class="w-full min-w-[80px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <select name="items[{{ $index }}][section_name]" required class="w-full min-w-[120px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
         @foreach($sectionOptions as $sec)
         <option value="{{ $sec }}" @selected(($item['section_name'] ?? '') === $sec)>{{ $sec }}</option>
         @endforeach
      </select>
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][location]" value="{{ $item['location'] ?? '' }}" required class="w-full min-w-[100px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <textarea name="items[{{ $index }}][job_detail]" required rows="2" class="w-full min-w-[160px] rounded border border-outline-variant/30 px-2 py-1 text-xs">{{ $item['job_detail'] ?? '' }}</textarea>
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][work_permit]" value="{{ $item['work_permit'] ?? 'N/A' }}" class="w-full min-w-[80px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][tools]" value="{{ $item['tools'] ?? '' }}" placeholder="pisahkan koma" class="w-full min-w-[120px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][worker_names]" value="{{ $item['worker_names'] ?? '' }}" placeholder="NAMA" class="w-full min-w-[90px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][worker_sids]" value="{{ $item['worker_sids'] ?? '' }}" placeholder="SID" class="w-full min-w-[80px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][cctv]" value="{{ $item['cctv'] ?? '' }}" placeholder="CCTV" class="w-full min-w-[80px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][group_leader]" value="{{ $item['group_leader'] ?? '' }}" class="w-full min-w-[90px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][group_leader_sid]" value="{{ $item['group_leader_sid'] ?? '' }}" class="w-full min-w-[70px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][section_head]" value="{{ $item['section_head'] ?? '' }}" class="w-full min-w-[90px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][section_head_sid]" value="{{ $item['section_head_sid'] ?? '' }}" class="w-full min-w-[70px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][she_leader]" value="{{ $item['she_leader'] ?? '' }}" class="w-full min-w-[90px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][she_leader_sid]" value="{{ $item['she_leader_sid'] ?? '' }}" class="w-full min-w-[70px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][dept_head]" value="{{ $item['dept_head'] ?? '' }}" class="w-full min-w-[90px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <input type="text" name="items[{{ $index }}][dept_head_sid]" value="{{ $item['dept_head_sid'] ?? '' }}" class="w-full min-w-[70px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
   </td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
      <div class="flex items-center gap-1">
         <input type="text" name="items[{{ $index }}][pja_bc]" value="{{ $item['pja_bc'] ?? '' }}" class="w-full min-w-[80px] rounded border border-outline-variant/30 px-2 py-1 text-xs">
         <button type="button" class="ds-remove-item shrink-0 text-error text-[10px] font-bold hover:underline" title="Hapus baris">×</button>
      </div>
   </td>
</tr>
