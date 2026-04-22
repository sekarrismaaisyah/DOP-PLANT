@extends('peer-pressure-edukasi.layouts.peer-app')

@section('title', 'Pilot Project Validation - Tambah Roadmap Period')

@section('content')
<div class="bg-white rounded-2xl anchored-card overflow-hidden max-w-5xl">
   <div class="p-6 border-b border-outline-variant/20">
      <h2 class="font-headline font-bold text-xl text-on-surface">Tambah Roadmap Period</h2>
      <p class="mt-1 text-xs text-on-surface-variant font-medium">Isi kolom untuk tabel <code>pilot_project_validation_roadmap_periods</code>.</p>
   </div>
   <div class="p-6">
      <form method="post" action="{{ route('pilot-project-validation.roadmap-periods.store') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
         @csrf
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Project</label><select name="project_id" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm">@foreach($projects as $p)<option value="{{ $p->id }}">{{ $p->project_name }}</option>@endforeach</select></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Sort Order</label><input name="sort_order" value="0" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Display Current Period</label><input name="display_current_period" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Period</label><input name="period" required class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Phase</label><input name="phase" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Status</label><input name="status" value="plan" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Owner</label><input name="pic_owner" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Target Date</label><input type="date" name="target_date" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Reviewer Status</label><input name="reviewer_status" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Progress %</label><input type="number" step="0.01" min="0" max="100" name="period_progress_percent" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></div>
         <div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Period Explanation</label><textarea name="period_explanation" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
         <div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Planned Objective Outcome</label><textarea name="planned_objective_outcome" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
         <div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Update Summary</label><textarea name="pic_update_summary" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
         <div class="md:col-span-2"><label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">PIC Risks Dependencies</label><textarea name="pic_risks_dependencies" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm"></textarea></div>
         <div class="md:col-span-2 flex gap-2 pt-2"><button class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md">Simpan</button><a href="{{ route('pilot-project-validation.roadmap-periods.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">Batal</a></div>
      </form>
   </div>
</div>
@endsection

