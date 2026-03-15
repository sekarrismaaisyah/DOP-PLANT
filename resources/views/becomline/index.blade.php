@extends('layouts.master')

@section('title', 'Becomline')
@section('content')
<x-page-title title="Becomline" pagetitle="Data Perusahaan & Permit SPIP" />

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="mb-0 fw-bold">Daftar Becomline</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('becomline.import-form') }}" class="btn btn-success btn-sm">
                            <i class="material-icons-outlined">upload_file</i> Import Excel
                        </a>
                        <a href="{{ route('becomline.download-template') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="material-icons-outlined">download</i> Template
                        </a>
                        <a href="{{ route('becomline.create') }}" class="btn btn-primary btn-sm">
                            <i class="material-icons-outlined">add</i> Tambah
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Perusahaan Pemilik</th>
                                <th>Site Operasional</th>
                                <th>Jenis Unit SPIP</th>
                                <th>Expired</th>
                                <th>Status Permit SPIP</th>
                                <th>No Register</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $item->perusahaan_pemilik ?? '-' }}</td>
                                <td>{{ $item->site_operasional ?? '-' }}</td>
                                <td>{{ $item->jenis_unit_spip ?? '-' }}</td>
                                <td>{{ $item->expired ? $item->expired->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->status_permit_spip ?? '-' }}</td>
                                <td>{{ $item->no_registrasi ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('becomline.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="material-icons-outlined" style="font-size:18px">edit</i>
                                    </a>
                                    <form action="{{ route('becomline.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?');">
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
                                <td colspan="8" class="text-center text-muted">Belum ada data. Gunakan Tambah atau Import Excel.</td>
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
