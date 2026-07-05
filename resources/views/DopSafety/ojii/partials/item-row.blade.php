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
   <input
      type="file"
      name="evidence_1[{{ $index }}]"
      class="w-full min-w-[120px] text-xs">

   @if(!empty($item['evidence_1']))
      <a
         href="{{ asset('storage/'.$item['evidence_1']) }}"
         target="_blank"
         class="text-blue-600 text-[10px] hover:underline">
         Lihat
      </a>
   @endif
</td>

<td class="px-2 py-2 border border-gray-200 bg-white">
   <input
      type="file"
      name="evidence_2[{{ $index }}]"
      class="w-full min-w-[120px] text-xs">

   @if(!empty($item['evidence_2']))
      <a
         href="{{ asset('storage/'.$item['evidence_2']) }}"
         target="_blank"
         class="text-blue-600 text-[10px] hover:underline">
         Lihat
      </a>
   @endif
</td>

<td class="px-2 py-2 border border-gray-200 bg-white">
   <input
      type="file"
      name="evidence_3[{{ $index }}]"
      class="w-full min-w-[120px] text-xs">

   @if(!empty($item['evidence_3']))
      <a
         href="{{ asset('storage/'.$item['evidence_3']) }}"
         target="_blank"
         class="text-blue-600 text-[10px] hover:underline">
         Lihat
      </a>
   @endif
</td>

<td class="px-2 py-2 border border-gray-200 bg-white">
   <input
      type="file"
      name="evidence_4[{{ $index }}]"
      class="w-full min-w-[120px] text-xs">

   @if(!empty($item['evidence_4']))
      <a
         href="{{ asset('storage/'.$item['evidence_4']) }}"
         target="_blank"
         class="text-blue-600 text-[10px] hover:underline">
         Lihat
      </a>
   @endif
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
   <td class="px-2 py-2 border border-gray-200 bg-white text-center">
    @if(!empty($item['id']))
        <div class="flex flex-col items-center gap-1.5 min-w-[90px]">
            @php
               $workerRow = \App\Models\DopOjiPlanItemWorker::query()->where('dop_oji_plan_item_id', $item['id'])->first();
               
               $totalWorkers = 0;
               $strNrp = '';
               $strName = '';
               $strPosition = '';

               if ($workerRow && !empty($workerRow->nrp)) {
                   $strNrp = $workerRow->nrp;
                   $strName = $workerRow->name;
                   $strPosition = $workerRow->position;
                   
                   $arrNrp = array_filter(array_map('trim', explode(';', $strNrp)));
                   $totalWorkers = count($arrNrp);
               }
            @endphp
            
            <span class="text-[10px] font-semibold text-gray-500">
               {{ $totalWorkers }} Pekerja
            </span>

            <div class="flex flex-col gap-1 w-full">
                @if($totalWorkers > 0)
                    <button type="button" 
                            class="view-mechanic-btn px-2 py-1 text-[9px] rounded bg-blue-500 hover:bg-blue-600 text-white font-bold shadow-sm"
                            data-nrps="{{ $strNrp }}"
                            data-names="{{ $strName }}"
                            data-positions="{{ $strPosition }}">
                        👁️ Lihat Data
                    </button>
                @endif

                <input type="file" 
                       id="worker_file_{{ $item['id'] }}" 
                       accept=".xlsx, .xls" 
                       class="hidden" 
                       data-id="{{ $item['id'] }}"
                       onchange="handleRowWorkerUpload(this)">
                       
                <button type="button" 
                        onclick="document.getElementById('worker_file_{{ $item['id'] }}').click()"
                        class="px-2 py-1 text-[9px] rounded bg-indigo-600 hover:bg-indigo-700 text-white font-medium shadow-sm">
                    📎 Upload Excel
                </button>
            </div>
        </div>
    @else
        <span class="text-gray-400 text-[10px] italic">Save item dahulu</span>
    @endif
</td>
   <td class="px-2 py-2 border border-gray-200 bg-white">

    @if(($item['approval_status'] ?? '') === 'rejected')

        <div class="rounded bg-red-100 border border-red-300 p-2 text-xs text-red-700">
            {{ $item['reject_reason'] ?? '-' }}
        </div>

    @else

        <span class="text-gray-400 text-xs">-</span>

    @endif

</td>
   <td class="px-2 py-2 border border-gray-200 bg-white">
    <div class="flex flex-col gap-1">
        {{-- <button
            type="button"
            class="px-2 py-1 text-[10px] rounded bg-green-600 text-white">
            Approve
        </button> --}}
        @php
    $approvalStatus = $item['approval_status'] ?? 'waiting_dept_head';

    $approveLabel = match ($approvalStatus) {
        'waiting_dept_head' => 'Waiting Approval Dept Head',
        'waiting_safety' => 'Waiting Approval Dept Head Safety',
        'waiting_pm' => 'Waiting Approval PM',
        'done' => 'Approved',
        'rejected' => 'Rejected',
        default => 'Approve',
    };
@endphp

<button
    type="button"
    class="approve-btn px-2 py-1 text-[10px] rounded text-white
        {{ $approvalStatus === 'done'
            ? 'bg-green-700'
            : ($approvalStatus === 'rejected'
                ? 'bg-gray-500'
                : 'bg-green-600') }}"
    data-id="{{ $item['id'] ?? '' }}"
    data-status="{{ $approvalStatus }}"
    @disabled(in_array($approvalStatus, ['done','rejected']))
>
    {{ $approveLabel }}
</button>

        <button
            type="button"
            class="reject-btn px-2 py-1 text-[10px] rounded bg-red-600 text-white"
            data-id="{{ $item['id'] ?? '' }}"
            data-status="{{ $approvalStatus }}"
            data-index="{{ $index }}"
            @disabled(in_array($approvalStatus, ['done','rejected']))
        >
            Reject
        </button>
        <input
    type="hidden"
    name="items[{{ $index }}][evidence_1]"
    value="{{ $item['evidence_1'] ?? '' }}">

<input
    type="hidden"
    name="items[{{ $index }}][evidence_2]"
    value="{{ $item['evidence_2'] ?? '' }}">

<input
    type="hidden"
    name="items[{{ $index }}][evidence_3]"
    value="{{ $item['evidence_3'] ?? '' }}">

<input
    type="hidden"
    name="items[{{ $index }}][evidence_4]"
    value="{{ $item['evidence_4'] ?? '' }}">

        <input
    type="hidden"
    name="items[{{ $index }}][id]"
    value="{{ $item['id'] ?? '' }}">


        <input
    type="hidden"
    name="items[{{ $index }}][approval_status]"
    value="{{ $approvalStatus }}">

        <input
            type="hidden"
            name="items[{{ $index }}][reject_reason]"
            value="{{ $item['reject_reason'] ?? '' }}">

    </div>
</td>
</tr>
