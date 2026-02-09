@extends('layouts.masterMotionHazardAdmin')

@section('title', 'Tambah DOPM')

@section('content')
    <x-page-title title="Tambah DOPM" pagetitle="DOPM$IKK - DOPM" />

    <div class="row">
        <div class="col-12">
            @if ($errors->any())
                <div class="alert alert-warning rounded-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tambah Data DOPM</h5>
                    <a href="{{ route('dopmikk.dopm.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">Kembali</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dopmikk.dopm.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="id_dop" class="form-label">ID (contoh: DOP-JJ46)</label>
                                <input type="text" name="id_dop" id="id_dop" class="form-control" value="{{ old('id_dop') }}" placeholder="DOP-XXXXX">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="site_ijin_kerja_khusus" class="form-label">Site Ijin Kerja Khusus</label>
                                <input type="text" name="site_ijin_kerja_khusus" id="site_ijin_kerja_khusus" class="form-control" value="{{ old('site_ijin_kerja_khusus') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="perusahaan_ijin_kerja_khusus" class="form-label">Perusahaan Ijin Kerja Khusus</label>
                                <input type="text" name="perusahaan_ijin_kerja_khusus" id="perusahaan_ijin_kerja_khusus" class="form-control" value="{{ old('perusahaan_ijin_kerja_khusus') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="jenis_ijin_kerja_khusus" class="form-label">Jenis Ijin Kerja Khusus</label>
                                <input type="text" name="jenis_ijin_kerja_khusus" id="jenis_ijin_kerja_khusus" class="form-control" value="{{ old('jenis_ijin_kerja_khusus') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="kode_ikk" class="form-label">Kode IKK</label>
                                <input type="text" name="kode_ikk" id="kode_ikk" class="form-control" value="{{ old('kode_ikk') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tanggal_selesai_ijin" class="form-label">Tanggal Selesai Ijin</label>
                                <input type="date" name="tanggal_selesai_ijin" id="tanggal_selesai_ijin" class="form-control" value="{{ old('tanggal_selesai_ijin') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nama_pekerjaan" class="form-label">Nama Pekerjaan</label>
                                <input type="text" name="nama_pekerjaan" id="nama_pekerjaan" class="form-control" value="{{ old('nama_pekerjaan') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tanggal_dop" class="form-label">Tanggal DOP</label>
                                <input type="date" name="tanggal_dop" id="tanggal_dop" class="form-control" value="{{ old('tanggal_dop') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" name="status" id="status" class="form-control" value="{{ old('status') }}">
                            </div>
                            <div class="col-12">
                                <label for="detail_lokasi" class="form-label">Detail Lokasi</label>
                                <textarea name="detail_lokasi" id="detail_lokasi" class="form-control" rows="2">{{ old('detail_lokasi') }}</textarea>
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('dopmikk.dopm.index') }}" class="btn btn-secondary rounded-3">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-3">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
