@extends('pilot-project-validation.layout.peer-app')
@section('title', 'PPV - Edit History Snapshot')
@section('content')
<div class="bg-white rounded-2xl anchored-card overflow-hidden max-w-4xl">
<div class="p-6 border-b border-outline-variant/20"><h2 class="font-headline font-bold text-xl text-on-surface">Edit History Snapshot</h2></div>
<div class="p-6"><form method="POST" action="{{ route('pilot-project-validation.history-snapshots.update', $row) }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
@csrf @method('PUT')
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Project</label><select name="project_id" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm">@foreach($projects as $p)<option value="{{ $p->id }}" @selected(old('project_id',$row->project_id)==$p->id)>{{ $p->project_name }}</option>@endforeach</select></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Sort</label><input name="sort_order" value="{{ old('sort_order',$row->sort_order) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Snapshot Date</label><input name="snapshot_date" value="{{ old('snapshot_date',$row->snapshot_date) }}" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Progress</label><input type="number" step="0.01" min="0" max="100" name="progress" value="{{ old('progress',$row->progress) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Decision Score</label><input type="number" min="0" max="100" name="decision_score" value="{{ old('decision_score',$row->decision_score) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div class="md:col-span-2 flex gap-2 pt-2"><button class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md">Perbarui</button> <a href="{{ route('pilot-project-validation.history-snapshots.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">Kembali</a></div>
</form></div></div>
@endsection

