@extends('pilot-project-validation.layout.peer-app')

@section('title', 'Pilot Project Validation - Data Master Proyek')

@section('content')
<div class="bg-white rounded-2xl anchored-card overflow-hidden">
   <div class="p-6 border-b border-outline-variant/20 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
      <div>
         <h2 class="font-headline font-bold text-xl text-on-surface">Data Master Proyek Pilot Validation</h2>
         <p class="text-xs text-on-surface-variant font-medium">Kelola proyek yang tampil pada dashboard `Key Pilot Projects & Technical Validation`.</p>
      </div>
      <div class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
         <form method="get" action="{{ route('pilot-project-validation.projects.index') }}" class="flex w-full min-w-0 flex-1 flex-col gap-2 sm:max-w-md sm:flex-row sm:items-center">
            <div class="relative min-w-0 flex-1">
               <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 z-0 -translate-y-1/2 text-lg text-on-surface-variant">search</span>
               <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Cari nama proyek, area, fase, periode, milestone..." autocomplete="off" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] py-2 pl-10 pr-3 text-sm text-on-surface outline-none transition-shadow placeholder:text-on-surface-variant/60 focus:border-primary/40 focus:ring-2 focus:ring-primary/15" aria-label="Cari proyek pilot">
            </div>
            <div class="flex shrink-0 gap-2">
               <button type="submit" class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm transition-colors hover:bg-surface-container-high">Cari</button>
               @if(filled($q ?? null))
               <a href="{{ route('pilot-project-validation.projects.index') }}" class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-bold text-on-surface-variant hover:bg-[#f1f5f9]">Reset</a>
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
      </div>
   </div>
   <div class="overflow-x-auto">
      <table class="w-full min-w-[1040px] text-sm text-left">
         <thead class="bg-[#f8fafc] text-on-surface-variant font-bold text-[10px] uppercase tracking-[0.15em] border-b border-outline-variant/20">
            <tr>
               <th class="px-8 py-5">Project</th>
               <th class="px-8 py-5">Subtitle</th>
               <th class="px-8 py-5">Area &amp; Fase</th>
               <th class="px-8 py-5">Support</th>
               <th class="px-8 py-5">Progress</th>
               <th class="px-8 py-5">Periode</th>
               <th class="px-8 py-5">Milestone</th>
               <th class="px-8 py-5">Need Support PIC</th>
               <th class="px-8 py-5">Diperbarui</th>
               <th class="px-8 py-5 w-40">Aksi</th>
            </tr>
         </thead>
         <tbody class="divide-y divide-outline-variant/10">
            @forelse($projects as $project)
            <tr class="hover:bg-[#f8fafc] transition-colors align-top">
               <td class="px-8 py-5">
                  <div class="font-bold text-on-surface">{{ $project->project_name }}</div>
                  <div class="text-[11px] text-on-surface-variant mt-1">ID #{{ $project->id }}</div>
               </td>
               <td class="px-8 py-5 text-xs text-on-surface-variant">{{ $project->subtitle ?: '—' }}</td>
               <td class="px-8 py-5">
                  <div class="text-xs font-semibold text-on-surface">{{ $project->pilot_area ?: '—' }}</div>
                  <div class="text-[11px] text-on-surface-variant mt-1">{{ $project->current_phase ?: 'Fase belum diisi' }}</div>
               </td>
               <td class="px-8 py-5 text-xs text-on-surface-variant">{{ $project->support ?: '—' }}</td>
               <td class="px-8 py-5">
                  <div class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-xs font-bold text-primary border border-primary/20">
                     {{ number_format((float) $project->progress, 2) }}%
                  </div>
               </td>
               <td class="px-8 py-5 text-xs font-semibold text-on-surface">{{ $project->current_period ?: '—' }}</td>
               <td class="px-8 py-5 text-xs text-on-surface-variant">{{ $project->next_milestone ?: '—' }}</td>
               <td class="px-8 py-5 text-xs text-on-surface-variant">{{ $project->need_support_pic ?: '—' }}</td>
               <td class="px-8 py-5 text-xs text-on-surface-variant">{{ $project->updated_at?->format('d/m/Y H:i') ?: '—' }}</td>
               <td class="px-8 py-5">
                  <div class="flex flex-col gap-2">
                     <a href="{{ route('pilot-project-validation.projects.edit', $project) }}" class="inline-flex items-center justify-center gap-1 rounded-lg border border-primary/30 bg-primary/5 px-3 py-1.5 text-[11px] font-bold text-primary hover:bg-primary/10">Edit</a>
                     <form action="{{ route('pilot-project-validation.projects.destroy', $project) }}" method="post" onsubmit="return confirm('Hapus proyek ini beserta timeline, gate, dan metrik?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1 rounded-lg border border-error/30 bg-red-50 px-3 py-1.5 text-[11px] font-bold text-error hover:bg-red-100">Hapus</button>
                     </form>
                  </div>
               </td>
            </tr>
            @empty
            <tr>
               <td colspan="10" class="px-8 py-10 text-center text-sm text-on-surface-variant font-medium">
                  @if(filled($q ?? null))Tidak ada hasil untuk kata kunci ini.@else Belum ada proyek master. Klik "Tambah proyek" untuk mulai.@endif
               </td>
            </tr>
            @endforelse
         </tbody>
      </table>
   </div>
   <div class="p-6 bg-[#f8fafc] flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 border-t border-outline-variant/20">
      <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">
         @if ($projects->total() === 0)
            Menampilkan 0 proyek
         @else
            Menampilkan {{ $projects->firstItem() }}-{{ $projects->lastItem() }} dari {{ number_format($projects->total()) }} proyek
         @endif
      </p>
      <div class="flex gap-2">
         @if ($projects->onFirstPage())
            <button type="button" class="p-2 border border-outline-variant/30 rounded-lg opacity-40 cursor-not-allowed" disabled aria-disabled="true"><span class="material-symbols-outlined text-sm">chevron_left</span></button>
         @else
            <a href="{{ $projects->previousPageUrl() }}" class="p-2 border border-outline-variant/30 rounded-lg hover:bg-white hover:shadow-md transition-all inline-flex" rel="prev"><span class="material-symbols-outlined text-sm">chevron_left</span></a>
         @endif
         @if (! $projects->hasMorePages())
            <button type="button" class="p-2 border border-outline-variant/30 rounded-lg opacity-40 cursor-not-allowed" disabled aria-disabled="true"><span class="material-symbols-outlined text-sm">chevron_right</span></button>
         @else
            <a href="{{ $projects->nextPageUrl() }}" class="p-2 border border-outline-variant/30 rounded-lg hover:bg-white hover:shadow-md transition-all inline-flex" rel="next"><span class="material-symbols-outlined text-sm">chevron_right</span></a>
         @endif
      </div>
   </div>
</div>
@endsection
