@php
   $tableStructure = $tableStructure ?? config('dop_safety.table_structure', []);
   $shiftLabel = $shiftOptions[old('shift', $defaults['shift'] ?? 1)] ?? 'SHIFT 1';
   $sectionLabel = $tableStructure['sections'][0]['name'] ?? 'FIELD TRACK';
@endphp

<tr class="ds-table-shift-row">
   <th class="text-center py-2 text-xs font-bold uppercase tracking-wide"></th>
   <th colspan="{{ \App\Support\DopSafety\DopSafetyPlanTableStructure::EXCEL_SHIFT_SECTION_COLSPAN }}" class="text-center py-2 text-xs font-bold uppercase tracking-wide">
      {{ $shiftLabel }}
   </th>
</tr>
<tr class="ds-table-section-row">
   <th class="text-center py-2 text-xs font-bold uppercase tracking-wide"></th>
   <th colspan="{{ \App\Support\DopSafety\DopSafetyPlanTableStructure::EXCEL_SHIFT_SECTION_COLSPAN }}" class="text-center py-2 text-xs font-bold uppercase tracking-wide">
      {{ $sectionLabel }}
   </th>
</tr>
<tr class="ds-table-header-row text-[10px] uppercase">
   @foreach($tableStructure['columns'] ?? [] as $column)
      @if(isset($column['sub_columns']))
         <th colspan="{{ count($column['sub_columns']) }}" class="text-center px-2 py-2 font-bold">
            {{ $column['name'] }}
         </th>
      @else
         <th rowspan="2" class="text-center px-2 py-2 font-bold align-middle">
            {{ $column['name'] }}
         </th>
      @endif
   @endforeach
</tr>
<tr class="ds-table-subheader-row text-[10px] uppercase">
   @foreach($tableStructure['columns'] ?? [] as $column)
      @if(isset($column['sub_columns']))
         @foreach($column['sub_columns'] as $sub)
            <th class="text-center px-2 py-2 font-bold">{{ $sub['name'] }}</th>
         @endforeach
      @endif
   @endforeach
</tr>
