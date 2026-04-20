@extends('layouts.master')

@section('title', 'Tambah Proyek Pilot')
@section('content')
<x-page-title title="Tambah proyek" pagetitle="Pilot Project Validation" />

<div class="row">
    <div class="col-lg-8">
        <div class="card rounded-4">
            <div class="card-body">
                <p class="text-muted small">Proyek baru dibuat dengan 4 gate default, satu periode roadmap, dan satu metrik per gate. Detail dapat diedit di halaman Input Page.</p>

                <form action="{{ route('pilot-project-validation.projects.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama proyek <span class="text-danger">*</span></label>
                        <input type="text" name="project_name" class="form-control @error('project_name') is-invalid @enderror" value="{{ old('project_name') }}" required maxlength="255" />
                        @error('project_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subtitle</label>
                        <textarea name="subtitle" class="form-control @error('subtitle') is-invalid @enderror" rows="2">{{ old('subtitle') }}</textarea>
                        @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pilot area</label>
                            <input type="text" name="pilot_area" class="form-control" value="{{ old('pilot_area') }}" maxlength="512" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Progress (%)</label>
                            <input type="number" name="progress" class="form-control" value="{{ old('progress', 0) }}" min="0" max="100" step="0.01" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Perlu dukungan (PIC)</label>
                        <input type="text" name="need_support_pic" class="form-control" value="{{ old('need_support_pic') }}" maxlength="255" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Support</label>
                        <textarea name="support" class="form-control" rows="2">{{ old('support') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fase saat ini</label>
                        <input type="text" name="current_phase" class="form-control" value="{{ old('current_phase') }}" />
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Periode saat ini</label>
                            <input type="text" name="current_period" class="form-control" value="{{ old('current_period') }}" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Milestone berikutnya</label>
                            <input type="text" name="next_milestone" class="form-control" value="{{ old('next_milestone') }}" />
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('pilot-project-validation.projects.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
