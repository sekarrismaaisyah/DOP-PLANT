@extends('layouts.master')

@section('title', 'Edit Becomline')
@section('content')
<x-page-title title="Becomline" pagetitle="Edit Data" />

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <a href="{{ route('becomline.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="material-icons-outlined">arrow_back</i>
                    </a>
                    <h5 class="mb-0 fw-bold">Edit Becomline</h5>
                </div>

                <form action="{{ route('becomline.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="perusahaan_pemilik" class="form-label">Perusahaan Pemilik</label>
                            <input type="text" class="form-control @error('perusahaan_pemilik') is-invalid @enderror" id="perusahaan_pemilik" name="perusahaan_pemilik" value="{{ old('perusahaan_pemilik', $item->perusahaan_pemilik) }}" maxlength="255">
                            @error('perusahaan_pemilik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="site_operasional" class="form-label">Site Operasional</label>
                            <input type="text" class="form-control @error('site_operasional') is-invalid @enderror" id="site_operasional" name="site_operasional" value="{{ old('site_operasional', $item->site_operasional) }}" maxlength="255">
                            @error('site_operasional')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="jenis_unit_spip" class="form-label">Jenis Unit SPIP</label>
                            <input type="text" class="form-control @error('jenis_unit_spip') is-invalid @enderror" id="jenis_unit_spip" name="jenis_unit_spip" value="{{ old('jenis_unit_spip', $item->jenis_unit_spip) }}" maxlength="255">
                            @error('jenis_unit_spip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="expired" class="form-label">Expired</label>
                            <input type="date" class="form-control @error('expired') is-invalid @enderror" id="expired" name="expired" value="{{ old('expired', $item->expired ? $item->expired->format('Y-m-d') : '') }}">
                            @error('expired')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status_permit_spip" class="form-label">Status Permit SPIP</label>
                            <input type="text" class="form-control @error('status_permit_spip') is-invalid @enderror" id="status_permit_spip" name="status_permit_spip" value="{{ old('status_permit_spip', $item->status_permit_spip) }}" maxlength="100">
                            @error('status_permit_spip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="no_registrasi" class="form-label">No Register</label>
                            <input type="text" class="form-control @error('no_registrasi') is-invalid @enderror" id="no_registrasi" name="no_registrasi" value="{{ old('no_registrasi', $item->no_registrasi) }}" maxlength="100">
                            @error('no_registrasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><i class="material-icons-outlined">save</i> Simpan</button>
                            <a href="{{ route('becomline.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
