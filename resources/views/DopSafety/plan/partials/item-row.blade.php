@php
   $item = $item ?? [];
   $index = isset($index) ? (int)$index : 0; 
   $itemNo = $item['item_no'] ?? ($index + 1);
   
   $isOjiApproved = false;
   $ojiItem = null;

   if (isset($plan) && $plan instanceof \App\Models\DopSafetyPlan) {
       $site = $plan->site;
       $shift = $plan->shift;
       
       $planDate = \Carbon\Carbon::parse($plan->plan_date)->format('Y-m-d');

       $targetOjiPlan = \App\Models\DopOjiPlan::query()
           ->where('site', $site)
           ->where('plan_date', $planDate)
           ->where('shift', $shift)
           ->first();

       if ($targetOjiPlan) {
           $ojiItem = \App\Models\DopOjiPlanItem::query()
               ->where('dop_oji_plan_id', $targetOjiPlan->id)
               ->where('item_no', $itemNo)
               ->first();

           if (!$ojiItem && !empty($item['unit_code'])) {
               $ojiItem = \App\Models\DopOjiPlanItem::query()
                   ->where('dop_oji_plan_id', $targetOjiPlan->id)
                   ->where('unit_code', $item['unit_code'])
                   ->where('job_detail', $item['job_detail'] ?? '')
                   ->first();
           }
       }
   }

   if (!$ojiItem && !empty($item['unit_code'])) {
       $ojiItem = \App\Models\DopOjiPlanItem::query()
           ->where('unit_code', $item['unit_code'])
           ->where('job_detail', $item['job_detail'] ?? '')
           ->orderByRaw("FIELD(approval_status, 'done') DESC")
           ->first();
   }

   if ($ojiItem && $ojiItem->approval_status === 'done') {
       $isOjiApproved = true;
   }

   $itemId = $item['id'] ?? ($ojiItem->dop_safety_plan_item_id ?? '');
@endphp
<tr class="ds-item-row align-top" data-index="{{ $index }}">
   <td class="px-2 py-2 border border-gray-200 text-center text-xs font-bold text-gray-700 bg-white">
      {{ is_numeric($index) ? ((int) $index + 1) : '' }}
   </td>
   
   <td class="px-2 py-2 border border-gray-200 text-center bg-white vertical-middle">
      @if($isOjiApproved)
         <input type="checkbox" name="selected_items[]" value="{{ $itemId }}" class="ds-selector rounded border-gray-300 text-primary h-4 w-4 cursor-pointer">
      @else
         <input type="checkbox" disabled title="OJI pasangan item ini belum Approved" class="rounded border-gray-200 text-gray-300 bg-gray-100 cursor-not-allowed h-4 w-4">
      @endif
   </td>
   
   <td class="p-3 align-middle text-center min-w-[150px] border border-gray-200 bg-white">
        <div class="flex flex-col items-center gap-2">
            @php
                $ojiStatus = $ojiItem->approval_status ?? 'waiting_dept_head';
                $ojiStatusClasses = match($ojiStatus) {
                    'waiting_dept_head'     => 'bg-amber-50 text-amber-700 border-amber-200',
                    'done'                  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'rejected'              => 'bg-rose-50 text-rose-700 border-rose-200',
                    default                 => 'bg-gray-50 text-gray-700 border-gray-200'
                };

                $ojiStatusLabel = match($ojiStatus) {
                    'waiting_dept_head'     => 'Waiting Dept. Head',
                    'done'                  => 'OJI Approved',
                    'rejected'              => 'OJI Rejected',
                    default                 => strtoupper(str_replace('_', ' ', $ojiStatus))
                };
            @endphp
            
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $ojiStatusClasses }}">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current"></span>
                {{ $ojiStatusLabel }}
            </span>

            @if($ojiItem && !empty($ojiItem->dop_oji_plan_id))
                <a href="{{ route('dop-safety.oji.show', $ojiItem->dop_oji_plan_id) }}" 
                   target="_blank" 
                   class="px-3 py-1 mt-1 text-[10px] rounded border border-blue-200 bg-blue-50 hover:bg-blue-600 hover:text-white hover:border-blue-600 text-blue-700 font-bold shadow-sm whitespace-nowrap transition-all duration-200">
                   👁️ Lihat Data OJI
                </a>
            @endif
        </div>
   </td>
   
   <td class="p-3 align-middle min-w-[150px]">
        @php
            $currStatus = $item->approval_status ?? $item['approval_status'] ?? 'waiting_lce';
            $statusClasses = match($currStatus) {
                'waiting_lce'           => 'bg-blue-50 text-blue-700 border-blue-200',
                'waiting_dept_head'     => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'waiting_dept_head_she' => 'bg-purple-50 text-purple-700 border-purple-200',
                'waiting_pm'            => 'bg-amber-50 text-amber-700 border-amber-200',
                'waiting_suptend_safety'=> 'bg-orange-50 text-orange-700 border-orange-200',
                'waiting_wktt'          => 'bg-rose-50 text-rose-700 border-rose-200',
                'done'                  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                default                 => 'bg-gray-50 text-gray-700 border-gray-200'
            };

            $statusLabel = match($currStatus) {
               'waiting_lce'            => 'Waiting LCE',
               'waiting_dept_head'      => 'Waiting Dept. Head',
               'waiting_dept_head_she'  => 'Waiting DH SHE',
               'waiting_pm'             => 'Waiting PM',
               'waiting_suptend_safety' => 'Waiting Supt. Safety',
               'waiting_wktt'           => 'Waiting WKTT',
               'done'                   => 'Done',
                default                 => strtoupper(str_replace('_', ' ', $currStatus))
            };
        @endphp
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClasses }}">
            <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current"></span>
            {{ $statusLabel }}
        </span>
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