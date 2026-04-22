@php
   $project = $project ?? null;
   $submitLabel = $submitLabel ?? 'Simpan';
@endphp

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
   <div class="md:col-span-2">
      <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Nama proyek <span class="text-error">*</span></label>
      <input type="text" name="project_name" value="{{ old('project_name', $project?->project_name) }}" required maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm text-on-surface outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15 @error('project_name') border-error/60 ring-2 ring-error/15 @enderror">
      @error('project_name')<p class="mt-1 text-xs font-medium text-error">{{ $message }}</p>@enderror
   </div>

   <div class="md:col-span-2">
      <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Subtitle</label>
      <textarea name="subtitle" rows="2" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm text-on-surface outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15 @error('subtitle') border-error/60 ring-2 ring-error/15 @enderror">{{ old('subtitle', $project?->subtitle) }}</textarea>
      @error('subtitle')<p class="mt-1 text-xs font-medium text-error">{{ $message }}</p>@enderror
   </div>

   <div>
      <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Pilot area</label>
      <input type="text" name="pilot_area" value="{{ old('pilot_area', $project?->pilot_area) }}" maxlength="512" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm text-on-surface outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
   </div>

   <div>
      <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Progress (%)</label>
      <input type="number" name="progress" value="{{ old('progress', $project?->progress ?? 0) }}" min="0" max="100" step="0.01" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm text-on-surface outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
   </div>

   <div class="md:col-span-2">
      <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Perlu dukungan (PIC)</label>
      <input type="text" name="need_support_pic" value="{{ old('need_support_pic', $project?->need_support_pic) }}" maxlength="255" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm text-on-surface outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
   </div>

   <div class="md:col-span-2">
      <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Support</label>
      <textarea name="support" rows="2" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm text-on-surface outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">{{ old('support', $project?->support) }}</textarea>
   </div>

   <div>
      <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Fase saat ini</label>
      <input type="text" name="current_phase" value="{{ old('current_phase', $project?->current_phase) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm text-on-surface outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
   </div>

   <div>
      <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Periode saat ini</label>
      <input type="text" name="current_period" value="{{ old('current_period', $project?->current_period) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm text-on-surface outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
   </div>

   <div class="md:col-span-2">
      <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-on-surface-variant">Milestone berikutnya</label>
      <input type="text" name="next_milestone" value="{{ old('next_milestone', $project?->next_milestone) }}" class="w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2.5 text-sm text-on-surface outline-none focus:border-primary/40 focus:ring-2 focus:ring-primary/15">
   </div>
</div>

<div class="mt-5 flex flex-wrap gap-2">
   <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shadow-md transition-transform active:scale-95">
      <span class="material-symbols-outlined text-base">save</span> {{ $submitLabel }}
   </button>
   <a href="{{ route('pilot-project-validation.projects.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
      <span class="material-symbols-outlined text-base">arrow_back</span> Kembali
   </a>
   <a href="{{ route('pilot-project-validation.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2 text-xs font-bold shadow-sm hover:bg-surface-container-high">
      <span class="material-symbols-outlined text-base">dashboard</span> Buka dashboard
   </a>
</div>
