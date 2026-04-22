@extends('pilot-project-validation.layout.peer-app')
@section('title', 'PPV - Timeline Tasks')
@section('content')
<div class="bg-white rounded-2xl anchored-card overflow-hidden">
  <div class="p-6 border-b border-outline-variant/20 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
    <div>
      <h2 class="font-headline font-bold text-xl text-on-surface">Timeline Tasks</h2>
      <p class="text-xs text-on-surface-variant font-medium">Kelola tabel <code>pilot_project_validation_timeline_tasks</code>.</p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('pilot-project-validation.timeline-tasks.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md"><span class="material-symbols-outlined text-base">add</span> Tambah task</a>
    </div>
  </div>

  @if(session('success'))
  <div class="px-6 pt-4">
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-900">{{ session('success') }}</div>
  </div>
  @endif
  @if(session('error'))
  <div class="px-6 pt-4">
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs font-medium text-red-900">{{ session('error') }}</div>
  </div>
  @endif

  <div class="px-6 pt-4">
    <div class="rounded-xl border border-outline-variant/20 bg-[#f8fafc] p-4">
      
      <form method="post" action="{{ route('pilot-project-validation.timeline-tasks.import-excel') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
        @csrf
        <input type="file" name="file" accept=".xlsx,.xls" required class="rounded-lg border border-outline-variant/30 bg-white px-3 py-2 text-xs text-on-surface">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md"><span class="material-symbols-outlined text-base">upload_file</span> Upload Timeline Excel</button>
        <a href="{{ route('pilot-project-validation.timeline-tasks.template-excel') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high"><span class="material-symbols-outlined text-base">download</span> Unduh contoh template</a>
      </form>
    
  </div>

  <div class="overflow-x-auto"><table class="w-full min-w-[980px] text-sm text-left">
    <thead class="bg-[#f8fafc] text-on-surface-variant font-bold text-[10px] uppercase tracking-[0.15em] border-b border-outline-variant/20"><tr><th class="px-8 py-5">ID</th><th class="px-8 py-5">Project</th><th class="px-8 py-5">Period</th><th class="px-8 py-5">Task</th><th class="px-8 py-5">Status</th><th class="px-8 py-5">Aksi</th></tr></thead>
    <tbody class="divide-y divide-outline-variant/10">
      @php
        $groupedRows = $rows->getCollection()->groupBy(function ($item) {
          return $item->roadmapPeriod?->project?->project_name ?: 'Tanpa Project';
        });
      @endphp
      @forelse($groupedRows as $projectName => $projectRows)
        <tr class="bg-[#f3f6fb]"><td colspan="6" class="px-8 py-3 text-[11px] font-bold uppercase tracking-[0.12em] text-primary">{{ $projectName }} ({{ $projectRows->count() }} task)</td></tr>
        @foreach($projectRows as $row)
          <tr class="hover:bg-[#f8fafc] transition-colors align-top">
            <td class="px-8 py-5 text-xs">{{ $row->id }}</td>
            <td class="px-8 py-5 text-xs">{{ $row->roadmapPeriod?->project?->project_name }}</td>
            <td class="px-8 py-5 text-xs">{{ $row->roadmapPeriod?->period }}</td>
            <td class="px-8 py-5 text-xs">{{ \Illuminate\Support\Str::limit($row->task_text, 80) }}</td>
            <td class="px-8 py-5 text-xs">{{ $row->task_status }}</td>
            <td class="px-8 py-5"><div class="flex flex-col gap-2"><a href="{{ route('pilot-project-validation.timeline-tasks.edit', $row) }}" class="inline-flex items-center justify-center rounded-lg border border-primary/30 bg-primary/5 px-3 py-1.5 text-[11px] font-bold text-primary hover:bg-primary/10">Edit</a><form method="post" action="{{ route('pilot-project-validation.timeline-tasks.destroy', $row) }}" onsubmit="return confirm('Hapus data?')">@csrf @method('DELETE')<button class="w-full inline-flex items-center justify-center rounded-lg border border-error/30 bg-red-50 px-3 py-1.5 text-[11px] font-bold text-error hover:bg-red-100">Hapus</button></form></div></td>
          </tr>
        @endforeach
      @empty
        <tr><td colspan="6" class="px-8 py-10 text-center text-sm text-on-surface-variant font-medium">Belum ada data</td></tr>
      @endforelse
    </tbody>
  </table></div>
  <div class="p-6 bg-[#f8fafc] border-t border-outline-variant/20">{{ $rows->links() }}</div>
</div>
@endsection

