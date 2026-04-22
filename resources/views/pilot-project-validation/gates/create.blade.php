@extends('pilot-project-validation.layout.peer-app')
@section('title', 'PPV - Tambah Gate')
@section('content')
<div class="bg-white rounded-2xl anchored-card overflow-hidden max-w-5xl">
<div class="p-6 border-b border-outline-variant/20"><h2 class="font-headline font-bold text-xl text-on-surface">Tambah Gate</h2></div>
<div class="p-6"><form method="POST" action="{{ route('pilot-project-validation.gates.store') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
@csrf
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Project</label><select name="project_id" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm">@foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->project_name }}</option>@endforeach</select></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Sort</label><input name="sort_order" value="0" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Gate Label</label><input name="gate_label" value="Gate 1" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Gate Title</label><input name="gate_title" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Hard Gate</label><select name="hard_gate" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"><option value="0">No</option><option value="1">Yes</option></select></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Owner</label><input name="pic_owner" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Target Close Date</label><input type="date" name="target_close_date" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Reviewer Status</label><input name="reviewer_status" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Gate Caption</label><textarea name="gate_caption" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
<div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Gate Definition</label><textarea name="gate_definition" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
<div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Project Specific Explanation</label><textarea name="project_specific_explanation" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
<div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">What Gate Confirms</label><textarea name="what_gate_confirms" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
<div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">What PIC Needs To Fill</label><textarea name="what_pic_needs_to_fill" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Status</label><input name="pic_status" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Evidence Link Folder</label><input name="evidence_link_folder" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
<div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Notes Key Findings</label><textarea name="pic_notes_key_findings" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
<div class="md:col-span-2 flex gap-2 pt-2"><button class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md">Simpan</button> <a href="{{ route('pilot-project-validation.gates.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">Batal</a></div>
</form></div></div>
@endsection

