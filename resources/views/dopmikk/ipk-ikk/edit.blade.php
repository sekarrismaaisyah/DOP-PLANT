@extends('layouts.master')

@section('title', 'Edit IPK-IKK')

@section('content')
    <x-page-title title="Edit IPK-IKK" pagetitle="DOPM$IKK - IPK-IKK" />

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success rounded-4">{{ session('success') }}</div>
            @endif
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
                    <h5 class="mb-0">Edit Data IPK-IKK</h5>
                    <a href="{{ route('dopmikk.ipk-ikk.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">Kembali</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dopmikk.ipk-ikk.update', $ipkIkk->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="nama_pengawas" class="form-label">Nama Pengawas</label>
                                <input type="text" name="nama_pengawas" id="nama_pengawas" class="form-control" value="{{ old('nama_pengawas', $ipkIkk->nama_pengawas) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="kode_sid" class="form-label">Kode SID</label>
                                <input type="text" name="kode_sid" id="kode_sid" class="form-control" value="{{ old('kode_sid', $ipkIkk->kode_sid) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="kode_ikk" class="form-label">Kode IKK</label>
                                <input type="text" name="kode_ikk" id="kode_ikk" class="form-control" value="{{ old('kode_ikk', $ipkIkk->kode_ikk) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                                <input type="text" name="nama_perusahaan" id="nama_perusahaan" class="form-control" value="{{ old('nama_perusahaan', $ipkIkk->nama_perusahaan) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="site" class="form-label">Site</label>
                                <input type="text" name="site" id="site" class="form-control" value="{{ old('site', $ipkIkk->site) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="durasi_jam" class="form-label">Durasi Pekerjaan (jam)</label>
                                <input type="text" name="durasi_jam" id="durasi_jam" class="form-control" value="{{ old('durasi_jam', $ipkIkk->durasi_jam) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="kategori_ijk" class="form-label">Kategori IJK</label>
                                <input type="text" name="kategori_ijk" id="kategori_ijk" class="form-control" value="{{ old('kategori_ijk', $ipkIkk->kategori_ijk) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nama_pekerjaan" class="form-label">Nama Pekerjaan</label>
                                <input type="text" name="nama_pekerjaan" id="nama_pekerjaan" class="form-control" value="{{ old('nama_pekerjaan', $ipkIkk->nama_pekerjaan) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status_pekerjaan" class="form-label">Status Pekerjaan</label>
                                <input type="text" name="status_pekerjaan" id="status_pekerjaan" class="form-control" value="{{ old('status_pekerjaan', $ipkIkk->status_pekerjaan) }}">
                            </div>
                            <div class="col-12">
                                <label for="detail_lokasi" class="form-label">Detail Lokasi</label>
                                <textarea name="detail_lokasi" id="detail_lokasi" class="form-control" rows="2">{{ old('detail_lokasi', $ipkIkk->detail_lokasi) }}</textarea>
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('dopmikk.ipk-ikk.index') }}" class="btn btn-secondary rounded-3">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-3">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
