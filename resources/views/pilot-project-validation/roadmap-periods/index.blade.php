@extends('pilot-project-validation.layout.peer-app')

@section('title', 'Pilot Project Validation - Roadmap Periods')

@section('content')
<div class="bg-white rounded-2xl anchored-card overflow-hidden">
   <div class="p-6 border-b border-outline-variant/20 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
      <div>
         <h2 class="font-headline font-bold text-xl text-on-surface">Roadmap Periods</h2>
         <p class="text-xs text-on-surface-variant font-medium">Kelola tabel <code>pilot_project_validation_roadmap_periods</code>.</p>
      </div>
      <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
         <form method="get" action="{{ route('pilot-project-validation.roadmap-periods.index') }}" class="flex w-full min-w-0 flex-1 flex-col gap-2 sm:max-w-md sm:flex-row sm:items-center">
            <div class="relative min-w-0 flex-1">
               <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-lg text-on-surface-variant">search</span>
               <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari period, phase, status..." class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] py-2 pl-10 pr-3 text-sm text-on-surface outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
            </div>
            <div class="flex shrink-0 gap-2">
               <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">Cari</button>
               @if(filled($q ?? null))
               <a href="{{ route('pilot-project-validation.roadmap-periods.index') }}" class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-bold text-on-surface-variant hover:bg-[#f1f5f9]">Reset</a>
               @endif
            </div>
         </form>
         <div class="flex flex-wrap gap-2">
            <a href="{{ route('pilot-project-validation.roadmap-periods.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">calendar_view_week</span> Roadmap Periods
            </a>
            <a href="{{ route('pilot-project-validation.timeline-tasks.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">task_alt</span> Timeline Tasks
            </a>
            <a href="{{ route('pilot-project-validation.gates.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">checklist</span> Gates
            </a>
            <a href="{{ route('pilot-project-validation.metrics.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">monitoring</span> Metrics
            </a>
            <a href="{{ route('pilot-project-validation.history-snapshots.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">history</span> History
            </a>
            <a href="{{ route('pilot-project-validation.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">dashboard</span> Buka Dashboard
            </a>
            <a href="{{ route('pilot-project-validation.projects.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md transition-transform active:scale-95">
               <span class="material-symbols-outlined text-base">add</span> Tambah proyek
            </a>
         </div>
         <div class="flex flex-wrap gap-2">
            <a href="{{ route('pilot-project-validation.projects.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high"><span class="material-symbols-outlined text-base">folder</span> Projects</a>
            <a href="{{ route('pilot-project-validation.roadmap-periods.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md"><span class="material-symbols-outlined text-base">add</span> Tambah period</a>
         </div>
      </div>
   </div>

   @if(session('success'))
   <div class="px-6 pt-4">
      <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-900">
         {{ session('success') }}
      </div>
   </div>
   @endif
   @if(session('error'))
   <div class="px-6 pt-4">
      <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs font-medium text-red-900">
         {{ session('error') }}
      </div>
   </div>
   @endif

   <div class="px-6 pt-4">
      <div class="rounded-xl border border-outline-variant/20 bg-[#f8fafc] p-4">
         <div class="mb-2 text-xs font-bold uppercase tracking-wide text-on-surface-variant">Import Excel Roadmap Periods</div>
         <p class="mb-3 text-[11px] text-on-surface-variant">
            Gunakan kolom: <strong>Project</strong>, <strong>Current Period</strong>, <strong>Roadmap Period</strong>, <strong>Phase</strong>, <strong>Period Status</strong>, <strong>Period Explanation</strong>, <strong>Planned Objective / Outcome</strong>, <strong>PIC Update Summary</strong>, <strong>PIC Risks / Dependencies</strong>, <strong>PIC Owner</strong>, <strong>Target Date</strong>, <strong>Reviewer Status</strong>, <strong>Period Progress %</strong>.
            Relasi dijaga berdasarkan nama project: kolom <strong>Project</strong> dicocokkan ke <code>pilot_project_validation_projects.project_name</code>.
         </p>
         <form method="post" action="{{ route('pilot-project-validation.roadmap-periods.import-excel') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls" required class="rounded-lg border border-outline-variant/30 bg-white px-3 py-2 text-xs text-on-surface">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md">
               <span class="material-symbols-outlined text-base">upload_file</span> Upload Roadmap Excel
            </button>
            <a href="{{ route('pilot-project-validation.roadmap-periods.template-excel') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
               <span class="material-symbols-outlined text-base">download</span> Unduh contoh template
            </a>
         </form>
      </div>
   </div>

   <div class="overflow-x-auto">
      <table class="w-full min-w-[1080px] text-sm text-left">
         <thead class="bg-[#f8fafc] text-on-surface-variant font-bold text-[10px] uppercase tracking-[0.15em] border-b border-outline-variant/20">
            <tr>
               <!-- <th class="px-8 py-5">ID</th> -->
               <th class="px-8 py-5">Project</th>
               <th class="px-8 py-5">Period</th>
               <th class="px-8 py-5">Phase</th>
               <th class="px-8 py-5">Status</th>
               <th class="px-8 py-5">Progress %</th>
               <th class="px-8 py-5">Aksi</th>
            </tr>
         </thead>
         <tbody class="divide-y divide-outline-variant/10">
            @php
               $groupedRows = $rows->getCollection()->groupBy(function ($item) {
                  return $item->project?->project_name ?: 'Tanpa Project';
               });
            @endphp
            @forelse($groupedRows as $projectName => $projectRows)
            <tr class="bg-[#f3f6fb]">
               <td colspan="7" class="px-8 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-primary">
                  {{ $projectName }} ({{ $projectRows->count() }} period)
               </td>
            </tr>
            @foreach($projectRows as $row)
               <tr class="hover:bg-[#f8fafc] transition-colors align-top">
                  <!-- <td class="px-8 py-5 text-xs text-on-surface-variant">{{ $row->id }}</td> -->
                  <td class="px-8 py-5 text-xs font-semibold text-on-surface">{{ $row->project?->project_name ?: '—' }}</td>
                  <td class="px-8 py-5 text-xs text-on-surface">{{ $row->period }}</td>
                  <td class="px-8 py-5 text-xs text-on-surface-variant">{{ $row->phase ?: '—' }}</td>
                  <td class="px-8 py-5 text-xs text-on-surface-variant">{{ $row->status ?: '—' }}</td>
                  <td class="px-8 py-5 text-xs text-on-surface-variant">{{ $row->period_progress_percent ?? '—' }}</td>
                  <td class="px-8 py-5">
                     <div class="flex flex-col gap-2">
                        <a href="{{ route('pilot-project-validation.roadmap-periods.edit', $row) }}" class="inline-flex items-center justify-center rounded-lg border border-primary/30 bg-primary/5 px-3 py-1.5 text-[11px] font-bold text-primary hover:bg-primary/10">Edit</a>
                        <form method="post" action="{{ route('pilot-project-validation.roadmap-periods.destroy', $row) }}" onsubmit="return confirm('Hapus data?')">
                           @csrf
                           @method('DELETE')
                           <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg border border-error/30 bg-red-50 px-3 py-1.5 text-[11px] font-bold text-error hover:bg-red-100">Hapus</button>
                        </form>
                     </div>
                  </td>
               </tr>
            @endforeach
            @empty
               <tr><td colspan="7" class="px-8 py-10 text-center text-sm text-on-surface-variant font-medium">Belum ada data.</td></tr>
            @endforelse
         </tbody>
      </table>
   </div>

   <div class="p-6 bg-[#f8fafc] border-t border-outline-variant/20">{{ $rows->links() }}</div>
</div>
@endsection

