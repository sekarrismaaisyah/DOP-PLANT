@extends('pilot-project-validation.layout.peer-app')
@section('title', 'PPV - Tambah Timeline Task')
@section('content')
<div class="bg-white rounded-2xl anchored-card overflow-hidden max-w-5xl">
<div class="p-6 border-b border-outline-variant/20"><h2 class="font-headline font-bold text-xl text-on-surface">Tambah Timeline Task</h2></div>
<div class="p-6"><form method="POST" action="{{ route('pilot-project-validation.timeline-tasks.store') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
@csrf
<div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Roadmap Period</label><select name="roadmap_period_id" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm">@foreach($periods as $p)<option value="{{ $p->id }}">{{ $p->project?->project_name }} | {{ $p->period }} | {{ $p->phase }}</option>@endforeach</select></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Sort</label><input name="sort_order" value="0" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Status</label><input name="task_status" value="plan" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Task Text</label><textarea name="task_text" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Task Owner</label><input name="task_owner" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Original Owner</label><input name="original_owner" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Original Status</label><input name="original_status" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Actual Owner</label><input name="pic_actual_owner" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Start Date</label><input type="date" name="pic_start_date" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Actual %</label><input type="number" step="0.01" min="0" max="100" name="pic_actual_percent" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Task Progress %</label><input type="number" step="0.01" min="0" max="100" name="task_progress_percent_normalized" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Target Date</label><input type="date" name="target_date" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Evidence Link</label><input name="evidence_link" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Progress Note</label><textarea name="pic_progress_note" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
<div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Dependency Blocker</label><textarea name="dependency_blocker" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
<div class="md:col-span-2 flex gap-2 pt-2"><button class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md">Simpan</button> <a href="{{ route('pilot-project-validation.timeline-tasks.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">Batal</a></div>
</form></div></div>
@endsection

