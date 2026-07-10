
@extends('DopSafety.layouts.app')

@section('title', 'Detail DOP OJI — DOP Safety')

@push('head')
@include('DopSafety.partials.styles')
<style>
   .ds-watermark {
      position: fixed;
      inset: 0;
      pointer-events: none;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0.04;
      font-size: 6rem;
      font-weight: 900;
      transform: rotate(-30deg);
      z-index: 0;
   }
</style>
@endpush

@section('content')
@php
   // Gunakan struktur tabel OJI, bukan DOP biasa
   $tableStructure = \App\Support\DopSafety\DopOjiTableStructure::definition()['table_structure'] ?? config('dop_safety.table_structure', []);
@endphp
<div class="relative">
   <div class="ds-watermark">{{ $watermark }}</div>

   @include('DopSafety.partials.page-header', [
      'title' => 'Daily Operation Planning (OJI)',
      'subtitle' => 'PT. PAMA PERSADA — PLANT DEPT. · ' . $plan->site . ' · ' . $plan->plan_date->format('l, d M Y') . ' · Shift ' . $plan->shift,
      'breadcrumb' => 'Detail OJI',
   ])

   <div class="relative z-10 space-y-6">
      <div class="rounded-xl border border-error/20 bg-red-50/60 px-4 py-3 text-sm font-semibold text-error flex items-center gap-2">
         <span class="material-symbols-outlined">warning</span>
         {{ $disclaimer }}
      </div>

      <div class="flex flex-wrap gap-2 items-center">
         <span class="{{ $plan->status->badgeClass() ?? 'ds-badge' }}">{{ $plan->status->label() ?? $plan->status }}</span>
         <a href="{{ route('dop-safety.oji.edit', $plan) }}" class="ds-badge !bg-amber-100 !text-amber-800 hover:!bg-amber-200">Edit OJI</a>
         <a href="{{ route('dop-safety.oji.index') }}" class="ds-badge !bg-gray-100 !text-gray-700 hover:!bg-gray-200">Kembali</a>
      </div>

      @foreach($itemsBySection as $sectionName => $items)
      <div class="ds-surface-card rounded-2xl p-6">
         <div class="overflow-x-auto">
            <table class="ds-table ds-plan-table w-full text-sm border-collapse min-w-[1400px]">
               <thead>
                  @include('DopSafety.ojii.partials.table-head', [
                     'tableStructure' => array_merge($tableStructure, [
                        'sections' => [['name' => $sectionName, 'colspan' => \App\Support\DopSafety\DopOjiTableStructure::EXCEL_SHIFT_SECTION_COLSPAN]],
                     ]),
                     'shiftOptions' => config('dop_safety.shifts', []),
                     'defaults' => ['shift' => $plan->shift],
                  ])
               </thead>
               <tbody>
                  @foreach($items as $item)
                  @php
                     // Ekstrak data list pekerja biasa
                     $workers = \App\Support\DopSafety\DopOjiTableStructure::workersToDisplayCells(is_array($item->workers) ? $item->workers : []);
                     
                     // Ekstrak data mekanik relasi (Tabel Baru)
                     $workerRow = \App\Models\DopOjiPlanItemWorker::query()->where('dop_oji_plan_item_id', $item->id)->first();
                     $totalMekanik = 0;
                     $strNrp = ''; $strName = ''; $strPosition = '';

                     if ($workerRow && !empty($workerRow->nrp)) {
                         $strNrp = $workerRow->nrp;
                         $strName = $workerRow->name;
                         $strPosition = $workerRow->position;
                         $arrNrp = array_filter(array_map('trim', explode(';', $strNrp)));
                         $totalMekanik = count($arrNrp);
                     }
                  @endphp
                  <tr class="border-b border-gray-100 align-top bg-white hover:bg-gray-50">
                     <td class="px-2 py-3 border border-gray-200 text-center font-bold text-gray-700">{{ $item->item_no }}</td>
                     <td class="px-2 py-3 border border-gray-200 font-semibold">{{ $item->unit_code }}</td>
                     <td class="px-2 py-3 border border-gray-200">{{ $item->section_name }}</td>
                     <td class="px-2 py-3 border border-gray-200">{{ $item->location }}</td>
                     <td class="px-2 py-3 border border-gray-200">{{ $item->job_detail }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->work_permit }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">
                        @if(is_array($item->tools) && count($item->tools))
                           {{ implode(', ', $item->tools) }}
                        @else — @endif
                     </td>
                     
                     <!-- Kolom List Pekerja Biasa (Tepat 2 kolom: NAMA dan SID) -->
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $workers['names'] ?: '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $workers['sids'] ?: '—' }}</td>
                     
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->cctv ?? '—' }}</td>
                     
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->group_leader ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->group_leader_sid ?? '—' }}</td>
                     
                     @foreach(['evidence_1', 'evidence_2', 'evidence_3', 'evidence_4', 'evidence_5'] as $evField)
                     <td class="px-2 py-3 border border-gray-200 text-center text-xs">
                        @if(!empty($item->$evField))
                           <a href="{{ asset('storage/'.$item->$evField) }}" target="_blank" class="text-blue-600 font-bold hover:underline">Lihat</a>
                        @else
                           <span class="text-gray-300">—</span>
                        @endif
                     </td>
                     @endforeach
                     
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->section_head ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->section_head_sid ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->she_leader ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->she_leader_sid ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->dept_head ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->dept_head_sid ?? '—' }}</td>
                     <td class="px-2 py-3 border border-gray-200 text-xs">{{ $item->pja_bc ?? '—' }}</td>

                     <td class="px-2 py-3 border border-gray-200 text-center">
                        <span class="text-[10px] font-semibold text-gray-500 block mb-1">
                           {{ $totalMekanik }} Mekanik
                        </span>
                        @if($totalMekanik > 0)
                           <button type="button" 
                                   class="view-mechanic-btn px-2 py-1 text-[9px] rounded bg-blue-500 hover:bg-blue-600 text-white font-bold shadow-sm"
                                   data-nrps="{{ $strNrp }}"
                                   data-names="{{ $strName }}"
                                   data-positions="{{ $strPosition }}">
                               👁️ Lihat Data
                           </button>
                        @endif
                     </td>

                     <td class="px-2 py-3 border border-gray-200 text-xs">
                        @if($item->approval_status === 'rejected')
                            <div class="rounded bg-red-100 border border-red-300 p-2 text-red-700 font-medium">
                                {{ $item->reject_reason ?? '-' }}
                            </div>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                     </td>

                     <td class="px-2 py-3 border border-gray-200 text-center">
                        @php
                            $statusColor = match($item->approval_status) {
                                'done' => 'bg-green-100 text-green-800 border-green-300',
                                'rejected' => 'bg-red-100 text-red-800 border-red-300',
                                default => 'bg-amber-100 text-amber-800 border-amber-300',
                            };
                            $statusLabel = match($item->approval_status) {
                                'waiting_dept_head' => 'Wait Dept Head',
                                'waiting_safety' => 'Wait Safety',
                                'waiting_pm' => 'Wait PM',
                                'done' => 'Approved',
                                'rejected' => 'Rejected',
                                default => str_replace('_', ' ', $item->approval_status),
                            };
                        @endphp
                        <span class="inline-block px-2 py-1 text-[10px] rounded border font-bold uppercase tracking-wider whitespace-nowrap {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                     </td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
      </div>
      @endforeach

      <div class="ds-surface-card rounded-2xl p-6">
         <h2 class="font-headline font-bold text-base mb-4">Otorisasi Dokumen</h2>
         <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div><span class="font-bold">Lokasi & Tanggal:</span> {{ $plan->auth_location_date ?? '—' }}</div>
            <div><span class="font-bold">Dibuat Oleh:</span> {{ $plan->created_by_name ?? '—' }} @if($plan->created_by_position)({{ $plan->created_by_position }})@endif</div>
            @foreach([1,2,3] as $n)
            @php
               $name = $plan->{'acknowledged_'.$n.'_name'};
               $pos = $plan->{'acknowledged_'.$n.'_position'};
            @endphp
            @if($name)
            <div><span class="font-bold">Mengetahui {{ $n }}:</span> {{ $name }} @if($pos)({{ $pos }})@endif</div>
            @endif
            @endforeach
         </div>
      </div>
   </div>
</div>

<div id="mechanicViewModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[85vh] flex flex-col">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-xl">
            <h3 class="text-base font-bold text-gray-800">Daftar Pekerja Mekanik</h3>
            <button type="button" id="closeMechanicModal" class="text-gray-400 hover:text-red-600 text-xl font-bold px-2">&times;</button>
        </div>
        <div class="p-4 overflow-y-auto">
            <table class="w-full text-sm border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-200 px-3 py-2 text-center w-10">No.</th>
                        <th class="border border-gray-200 px-3 py-2 text-left">NRP</th>
                        <th class="border border-gray-200 px-3 py-2 text-left">Nama</th>
                        <th class="border border-gray-200 px-3 py-2 text-left">Jabatan</th>
                    </tr>
                </thead>
                <tbody id="mechanicTableBody">
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-200 flex justify-end">
            <button type="button" id="closeMechanicModalBtn" class="px-4 py-2 text-sm rounded border bg-white hover:bg-gray-100 font-bold text-gray-700">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
   // LOGIC MODAL VIEW MEKANIK
   const mechanicModal = document.getElementById('mechanicViewModal');
   const mechanicTableBody = document.getElementById('mechanicTableBody');

   function hideMechanicModal() {
       mechanicModal.classList.add('hidden');
       mechanicModal.classList.remove('flex');
   }

   document.getElementById('closeMechanicModal').addEventListener('click', hideMechanicModal);
   document.getElementById('closeMechanicModalBtn').addEventListener('click', hideMechanicModal);

   document.addEventListener('click', function(e) {
       const btn = e.target.closest('.view-mechanic-btn');
       if (!btn) return;

       const nrps = btn.getAttribute('data-nrps').split(';');
       const names = btn.getAttribute('data-names').split(';');
       const positions = btn.getAttribute('data-positions').split(';');

       mechanicTableBody.innerHTML = '';
       let hasData = false;

       for (let i = 0; i < names.length; i++) {
           const nrp = nrps[i] ? nrps[i].trim() : '-';
           const name = names[i] ? names[i].trim() : '-';
           const position = positions[i] ? positions[i].trim() : '-';

           if (name === '-' && nrp === '-') continue;

           hasData = true;
           
           const row = document.createElement('tr');
           row.innerHTML = `
               <td class="border border-gray-200 px-3 py-2 text-center">${i + 1}</td>
               <td class="border border-gray-200 px-3 py-2 text-gray-800 font-mono">${nrp}</td>
               <td class="border border-gray-200 px-3 py-2 font-semibold">${name}</td>
               <td class="border border-gray-200 px-3 py-2 text-xs uppercase tracking-wider">${position}</td>
           `;
           mechanicTableBody.appendChild(row);
       }

       if (!hasData) {
           mechanicTableBody.innerHTML = `<tr><td colspan="4" class="text-center py-6 text-gray-500 italic">Belum ada data pekerja mekanik.</td></tr>`;
       }

       mechanicModal.classList.remove('hidden');
       mechanicModal.classList.add('flex');
   });
</script>
@endpush
