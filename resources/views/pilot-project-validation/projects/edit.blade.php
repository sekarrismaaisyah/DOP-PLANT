@extends('layouts.master')

@section('title', 'Edit Proyek Pilot')
@section('content')
<x-page-title title="Edit proyek" pagetitle="{{ $project->project_name }}" />

<div class="row">
    <div class="col-lg-8">
        <div class="card rounded-4">
            <div class="card-body">
                <p class="text-muted small">Mengubah nama proyek memengaruhi kunci tautan ke data timeline/gate/metrik. Untuk mengedit isi gate dan metrik gunakan halaman Input Page.</p>

                <form action="{{ route('pilot-project-validation.projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama proyek <span class="text-danger">*</span></label>
                        <input type="text" name="project_name" class="form-control @error('project_name') is-invalid @enderror" value="{{ old('project_name', $project->project_name) }}" required maxlength="255" />
                        @error('project_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subtitle</label>
                        <textarea name="subtitle" class="form-control @error('subtitle') is-invalid @enderror" rows="2">{{ old('subtitle', $project->subtitle) }}</textarea>
                        @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pilot area</label>
                            <input type="text" name="pilot_area" class="form-control" value="{{ old('pilot_area', $project->pilot_area) }}" maxlength="512" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Progress (%)</label>
                            <input type="number" name="progress" class="form-control" value="{{ old('progress', $project->progress) }}" min="0" max="100" step="0.01" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Perlu dukungan (PIC)</label>
                        <input type="text" name="need_support_pic" class="form-control" value="{{ old('need_support_pic', $project->need_support_pic) }}" maxlength="255" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Support</label>
                        <textarea name="support" class="form-control" rows="2">{{ old('support', $project->support) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fase saat ini</label>
                        <input type="text" name="current_phase" class="form-control" value="{{ old('current_phase', $project->current_phase) }}" />
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Periode saat ini</label>
                            <input type="text" name="current_period" class="form-control" value="{{ old('current_period', $project->current_period) }}" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Milestone berikutnya</label>
                            <input type="text" name="next_milestone" class="form-control" value="{{ old('next_milestone', $project->next_milestone) }}" />
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                        <a href="{{ route('pilot-project-validation.projects.index') }}" class="btn btn-outline-secondary">Kembali</a>
                        <a href="{{ route('pilot-project-validation.index') }}" class="btn btn-outline-primary ms-auto">Buka Input Page</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
