@extends('layouts.master')

@section('title', 'Pilot Project Validation — Daftar Proyek')
@section('content')
<x-page-title title="Pilot Project Validation" pagetitle="Data master proyek (Input Page)" />

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="mb-0 fw-bold">Daftar proyek pilot</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('pilot-project-validation.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="material-icons-outlined">dashboard</i> Gate Go / No Go (Input &amp; Dashboard)
                        </a>
                        <a href="{{ route('pilot-project-validation.projects.create') }}" class="btn btn-primary btn-sm">
                            <i class="material-icons-outlined">add</i> Tambah proyek
                        </a>
                    </div>
                </div>

                <p class="text-muted small mb-3">
                    Struktur lengkap (timeline, gate, metrik) diisi lewat halaman Input Page; simpan ke server dari sana.
                    Di halaman ini Anda mengelola baris master proyek (nama, progress, periode, dll.).
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:48px">#</th>
                                <th>Nama proyek</th>
                                <th>Progress</th>
                                <th>Periode saat ini</th>
                                <th>Diperbarui</th>
                                <th style="width:160px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $i => $project)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $project->project_name }}</td>
                                <td>{{ $project->progress }}%</td>
                                <td>{{ $project->current_period ?: '—' }}</td>
                                <td>{{ $project->updated_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('pilot-project-validation.projects.edit', $project) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="material-icons-outlined" style="font-size:18px">edit</i>
                                    </a>
                                    <form action="{{ route('pilot-project-validation.projects.destroy', $project) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus proyek ini beserta timeline, gate, dan metrik?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="material-icons-outlined" style="font-size:18px">delete</i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data. Tambah proyek atau isi dari halaman Input Page lalu simpan ke server.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
