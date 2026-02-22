@extends('layouts.master')

@section('title', 'Import User Excel')
@section('content')
<x-page-title title="Master User" pagetitle="Import User dari Excel" />

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <a href="{{ route('user-management.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="material-icons-outlined">arrow_back</i>
                    </a>
                    <h5 class="mb-0 fw-bold">Import User dari Excel</h5>
                </div>

                <div class="alert alert-info">
                    <strong>Format Excel:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Kolom wajib: <strong>name</strong>, <strong>email</strong>. Kolom <strong>role</strong> opsional (default: user).</li>
                        <li>Baris pertama harus header: <code>name</code>, <code>email</code>, <code>role</code>.</li>
                        <li>Email diisi dengan <strong>teks/kode</strong> (bukan alamat email, tanpa @). Contoh: XUJG3, H5UBW.</li>
                        <li>Password untuk semua user hasil import akan diset sama (isi di bawah).</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <a href="{{ route('user-management.download-template') }}" class="btn btn-outline-primary">
                        <i class="material-icons-outlined">download</i> Download Template Excel
                    </a>
                </div>

                <form action="{{ route('user-management.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="file" class="form-label">File Excel <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="default_password" class="form-label">Password default untuk user hasil import</label>
                            <input type="text" class="form-control" id="default_password" name="default_password" value="{{ old('default_password', 'password123') }}" minlength="6">
                            <div class="form-text">Semua user yang diimpor akan memakai password ini.</div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="material-icons-outlined">upload_file</i> Import
                            </button>
                            <a href="{{ route('user-management.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
