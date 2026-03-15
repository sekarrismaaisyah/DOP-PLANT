@extends('layouts.master')

@section('title', 'Import Becomline Excel')
@section('content')
<x-page-title title="Becomline" pagetitle="Import dari Excel" />

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
                <div class="d-flex align-items-center gap-2 mb-3">
                    <a href="{{ route('becomline.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="material-icons-outlined">arrow_back</i>
                    </a>
                    <h5 class="mb-0 fw-bold">Import Becomline dari Excel</h5>
                </div>

                <div class="alert alert-info">
                    <strong>Format Excel (baris pertama = header):</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Perusahaan Pemilik</strong> — teks</li>
                        <li><strong>Site Operasional</strong> — teks</li>
                        <li><strong>Jenis Unit SPIP</strong> — teks</li>
                        <li><strong>Expired</strong> — tanggal (contoh: Tuesday, 09 March 2027 atau YYYY-MM-DD). Boleh kosong.</li>
                        <li><strong>Status Permit SPIP</strong> — teks (contoh: PASSED, N/A)</li>
                        <li><strong>No Register</strong> — teks (contoh: BMCEX-241, MTN-470)</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <a href="{{ route('becomline.download-template') }}" class="btn btn-outline-primary">
                        <i class="material-icons-outlined">download</i> Download Template Excel
                    </a>
                </div>

                <form action="{{ route('becomline.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="excel_file" class="form-label">File Excel <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('excel_file') is-invalid @enderror" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                            @error('excel_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="material-icons-outlined">upload_file</i> Import
                            </button>
                            <a href="{{ route('becomline.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
