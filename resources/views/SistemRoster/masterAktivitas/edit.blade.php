@extends('layouts.masterRoster')

@section('title', 'Edit Master Aktivitas')

@section('content')
    <x-page-title title="Master Aktivitas" pagetitle="Edit Aktivitas" />

    <div class="row">
        <div class="col-12">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card rounded-4">
                <div class="card-header">
                    <h5 class="mb-0">Edit Master Aktivitas</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('sistem-roster.master-aktivitas.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nama_aktivitas" class="form-label">Nama Aktivitas <span class="text-danger">*</span></label>
                            <input type="text" name="nama_aktivitas" id="nama_aktivitas" class="form-control" value="{{ old('nama_aktivitas', $item->nama_aktivitas) }}" placeholder="Contoh: Inspeksi Alat Berat" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="periode_check" class="form-label">Periode Check</label>
                            <input type="text" name="periode_check" id="periode_check" class="form-control" value="{{ old('periode_check', $item->periode_check) }}" placeholder="Contoh: Harian, Mingguan, Bulanan" maxlength="100">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-3">
                                <i class="bx bx-save"></i> Update
                            </button>
                            <a href="{{ route('sistem-roster.master-aktivitas.index') }}" class="btn btn-outline-secondary rounded-3">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
