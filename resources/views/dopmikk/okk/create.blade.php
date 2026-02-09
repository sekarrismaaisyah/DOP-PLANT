@extends('layouts.master')

@section('title', 'Tambah OKK')

@section('content')
    <x-page-title title="Tambah OKK" pagetitle="DOPM$IKK - OKK" />

    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tambah Data OKK</h5>
                    <a href="{{ route('dopmikk.okk.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">Kembali</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dopmikk.okk.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="nama_pengawas" class="form-label">Nama Pengawas</label>
                                <input type="text" name="nama_pengawas" id="nama_pengawas" class="form-control" value="{{ old('nama_pengawas') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="kode_sid" class="form-label">Kode SID</label>
                                <input type="text" name="kode_sid" id="kode_sid" class="form-control" value="{{ old('kode_sid') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="kode_ikk" class="form-label">Kode IKK</label>
                                <input type="text" name="kode_ikk" id="kode_ikk" class="form-control" value="{{ old('kode_ikk') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                                <input type="text" name="nama_perusahaan" id="nama_perusahaan" class="form-control" value="{{ old('nama_perusahaan') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="site" class="form-label">Site</label>
                                <input type="text" name="site" id="site" class="form-control" value="{{ old('site') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="jenis_ijk" class="form-label">Jenis Ijin Kerja Khusus</label>
                                <input type="text" name="jenis_ijk" id="jenis_ijk" class="form-control" value="{{ old('jenis_ijk') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="layer_pengawas" class="form-label">Layer Pengawas</label>
                                <input type="text" name="layer_pengawas" id="layer_pengawas" class="form-control" value="{{ old('layer_pengawas') }}">
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('dopmikk.okk.index') }}" class="btn btn-secondary rounded-3">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-3">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
