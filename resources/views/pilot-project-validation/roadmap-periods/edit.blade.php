@extends('pilot-project-validation.layout.peer-app')

@section('title', 'Pilot Project Validation - Edit Roadmap Period')

@section('content')
<div class="bg-white rounded-2xl anchored-card overflow-hidden max-w-5xl">
   <div class="p-6 border-b border-outline-variant/20">
      <h2 class="font-headline font-bold text-xl text-on-surface">Edit Roadmap Period</h2>
      <p class="mt-1 text-xs text-on-surface-variant font-medium">Perbarui data tabel <code>pilot_project_validation_roadmap_periods</code>.</p>
   </div>
   <div class="p-6">
      <form method="post" action="{{ route('pilot-project-validation.roadmap-periods.update', $row) }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
         @csrf
         @method('PUT')
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Project</label><select name="project_id" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm">@foreach($projects as $p)<option value="{{ $p->id }}" @selected(old('project_id',$row->project_id)==$p->id)>{{ $p->project_name }}</option>@endforeach</select></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Sort Order</label><input name="sort_order" value="{{ old('sort_order',$row->sort_order) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Display Current Period</label><input name="display_current_period" value="{{ old('display_current_period',$row->display_current_period) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Period</label><input name="period" value="{{ old('period',$row->period) }}" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Phase</label><input name="phase" value="{{ old('phase',$row->phase) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Status</label><input name="status" value="{{ old('status',$row->status) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Owner</label><input name="pic_owner" value="{{ old('pic_owner',$row->pic_owner) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Target Date</label><input type="date" name="target_date" value="{{ old('target_date',optional($row->target_date)->format('Y-m-d')) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Reviewer Status</label><input name="reviewer_status" value="{{ old('reviewer_status',$row->reviewer_status) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Progress %</label><input type="number" step="0.01" min="0" max="100" name="period_progress_percent" value="{{ old('period_progress_percent',$row->period_progress_percent) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Period Explanation</label><textarea name="period_explanation" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm">{{ old('period_explanation',$row->period_explanation) }}</textarea></div>
         <div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Planned Objective Outcome</label><textarea name="planned_objective_outcome" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm">{{ old('planned_objective_outcome',$row->planned_objective_outcome) }}</textarea></div>
         <div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Update Summary</label><textarea name="pic_update_summary" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm">{{ old('pic_update_summary',$row->pic_update_summary) }}</textarea></div>
         <div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Risks Dependencies</label><textarea name="pic_risks_dependencies" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm">{{ old('pic_risks_dependencies',$row->pic_risks_dependencies) }}</textarea></div>
         <div class="md:col-span-2 flex gap-2 pt-2"><button class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md">Perbarui</button><a href="{{ route('pilot-project-validation.roadmap-periods.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">Kembali</a></div>
      </form>
   </div>
</div>
@endsection

