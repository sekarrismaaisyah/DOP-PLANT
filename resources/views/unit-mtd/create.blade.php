@extends('layouts.master')

@section('title', 'Tambah Unit MTD')
@section('content')
<x-page-title title="Unit MTD" pagetitle="Tambah Data" />

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <a href="{{ route('unit-mtd.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="material-icons-outlined">arrow_back</i>
                    </a>
                    <h5 class="mb-0 fw-bold">Form Tambah Unit MTD</h5>
                </div>

                <form action="{{ route('unit-mtd.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="site" class="form-label">Site</label>
                            <input type="text" class="form-control @error('site') is-invalid @enderror" id="site" name="site" value="{{ old('site') }}" maxlength="255" placeholder="Contoh: BMO 2">
                            @error('site')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="perusahaan" class="form-label">Perusahaan</label>
                            <input type="text" class="form-control @error('perusahaan') is-invalid @enderror" id="perusahaan" name="perusahaan" value="{{ old('perusahaan') }}" maxlength="255" placeholder="Contoh: PAMA">
                            @error('perusahaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="kategori" class="form-label">Kategori</label>
                            <input type="text" class="form-control @error('kategori') is-invalid @enderror" id="kategori" name="kategori" value="{{ old('kategori') }}" maxlength="255" placeholder="Contoh: Wheel loader, Bulldozers">
                            @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="no_unit" class="form-label">No Unit</label>
                            <input type="text" class="form-control @error('no_unit') is-invalid @enderror" id="no_unit" name="no_unit" value="{{ old('no_unit') }}" maxlength="100" placeholder="Contoh: BRBMWE201">
                            @error('no_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="mtd" class="form-label">MTD</label>
                            <input type="text" class="form-control @error('mtd') is-invalid @enderror" id="mtd" name="mtd" value="{{ old('mtd') }}" placeholder="204,1 atau 204.1">
                            @error('mtd')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="avg_per_day" class="form-label">AVG per Day</label>
                            <input type="text" class="form-control @error('avg_per_day') is-invalid @enderror" id="avg_per_day" name="avg_per_day" value="{{ old('avg_per_day') }}" placeholder="204,10 atau 204.10">
                            @error('avg_per_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><i class="material-icons-outlined">save</i> Simpan</button>
                            <a href="{{ route('unit-mtd.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
