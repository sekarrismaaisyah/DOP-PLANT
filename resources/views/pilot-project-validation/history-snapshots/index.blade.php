@extends('pilot-project-validation.layout.peer-app')
@section('title', 'PPV - History Snapshots')
@section('content')
<div class="bg-white rounded-2xl anchored-card overflow-hidden">
  <div class="p-6 border-b border-outline-variant/20 flex items-center justify-between">
    <div><h2 class="font-headline font-bold text-xl text-on-surface">History Snapshots</h2><p class="text-xs text-on-surface-variant font-medium">Kelola tabel <code>pilot_project_validation_history_snapshots</code>.</p></div>
    <a href="{{ route('pilot-project-validation.history-snapshots.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md"><span class="material-symbols-outlined text-base">add</span> Tambah snapshot</a>
  </div>
  <div class="overflow-x-auto"><table class="w-full min-w-[860px] text-sm text-left">
    <thead class="bg-[#f8fafc] text-on-surface-variant font-bold text-[10px] uppercase tracking-[0.15em] border-b border-outline-variant/20"><tr><th class="px-8 py-5">ID</th><th class="px-8 py-5">Project</th><th class="px-8 py-5">Snapshot Date</th><th class="px-8 py-5">Progress</th><th class="px-8 py-5">Decision Score</th><th class="px-8 py-5">Aksi</th></tr></thead>
    <tbody class="divide-y divide-outline-variant/10">@forelse($rows as $row)<tr class="hover:bg-[#f8fafc] transition-colors align-top"><td class="px-8 py-5 text-xs">{{ $row->id }}</td><td class="px-8 py-5 text-xs">{{ $row->project?->project_name }}</td><td class="px-8 py-5 text-xs">{{ $row->snapshot_date }}</td><td class="px-8 py-5 text-xs">{{ $row->progress }}</td><td class="px-8 py-5 text-xs">{{ $row->decision_score }}</td><td class="px-8 py-5"><div class="flex flex-col gap-2"><a href="{{ route('pilot-project-validation.history-snapshots.edit', $row) }}" class="inline-flex items-center justify-center rounded-lg border border-primary/30 bg-primary/5 px-3 py-1.5 text-[11px] font-bold text-primary hover:bg-primary/10">Edit</a><form method="post" action="{{ route('pilot-project-validation.history-snapshots.destroy', $row) }}" onsubmit="return confirm('Hapus data?')">@csrf @method('DELETE')<button class="w-full inline-flex items-center justify-center rounded-lg border border-error/30 bg-red-50 px-3 py-1.5 text-[11px] font-bold text-error hover:bg-red-100">Hapus</button></form></div></td></tr>@empty<tr><td colspan="6" class="px-8 py-10 text-center text-sm text-on-surface-variant font-medium">Belum ada data</td></tr>@endforelse</tbody>
  </table></div>
  <div class="p-6 bg-[#f8fafc] border-t border-outline-variant/20">{{ $rows->links() }}</div>
</div>
@endsection

