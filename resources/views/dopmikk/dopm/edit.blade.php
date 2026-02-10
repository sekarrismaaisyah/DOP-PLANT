@extends('layouts.masterDopm')

@section('title', 'Edit DOPM')

@section('content')
    <x-page-title title="Edit DOPM" pagetitle="DOPM$IKK - DOPM" />

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success rounded-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
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
                    <h5 class="mb-0">Edit Data DOPM</h5>
                    <a href="{{ route('dopmikk.dopm.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">Kembali</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dopmikk.dopm.update', $dopm->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="id_dop" class="form-label">ID (contoh: DOP-JJ46)</label>
                                <input type="text" name="id_dop" id="id_dop" class="form-control" value="{{ old('id_dop', $dopm->id_dop) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="timestamp" class="form-label">Timestamp</label>
                                <input type="datetime-local" name="timestamp" id="timestamp" class="form-control" value="{{ old('timestamp', $dopm->timestamp?->format('Y-m-d\TH:i')) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="site_ijin_kerja_khusus" class="form-label">Site Ijin Kerja Khusus</label>
                                <input type="text" name="site_ijin_kerja_khusus" id="site_ijin_kerja_khusus" class="form-control" value="{{ old('site_ijin_kerja_khusus', $dopm->site_ijin_kerja_khusus) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="perusahaan_ijin_kerja_khusus" class="form-label">Perusahaan Ijin Kerja Khusus</label>
                                <input type="text" name="perusahaan_ijin_kerja_khusus" id="perusahaan_ijin_kerja_khusus" class="form-control" value="{{ old('perusahaan_ijin_kerja_khusus', $dopm->perusahaan_ijin_kerja_khusus) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="jenis_ijin_kerja_khusus" class="form-label">Jenis Ijin Kerja Khusus</label>
                                <input type="text" name="jenis_ijin_kerja_khusus" id="jenis_ijin_kerja_khusus" class="form-control" value="{{ old('jenis_ijin_kerja_khusus', $dopm->jenis_ijin_kerja_khusus) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="kode_ikk" class="form-label">Kode IKK</label>
                                <input type="text" name="kode_ikk" id="kode_ikk" class="form-control" value="{{ old('kode_ikk', $dopm->kode_ikk) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tanggal_selesai_ijin" class="form-label">Tanggal Selesai Ijin</label>
                                <input type="date" name="tanggal_selesai_ijin" id="tanggal_selesai_ijin" class="form-control" value="{{ old('tanggal_selesai_ijin', $dopm->tanggal_selesai_ijin?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nama_pekerjaan" class="form-label">Nama Pekerjaan</label>
                                <input type="text" name="nama_pekerjaan" id="nama_pekerjaan" class="form-control" value="{{ old('nama_pekerjaan', $dopm->nama_pekerjaan) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tanggal_dop" class="form-label">Tanggal DOP</label>
                                <input type="date" name="tanggal_dop" id="tanggal_dop" class="form-control" value="{{ old('tanggal_dop', $dopm->tanggal_dop?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status_pengiriman_notif" class="form-label">Status Pengiriman Notif</label>
                                <input type="text" name="status_pengiriman_notif" id="status_pengiriman_notif" class="form-control" value="{{ old('status_pengiriman_notif', $dopm->status_pengiriman_notif) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" name="status" id="status" class="form-control" value="{{ old('status', $dopm->status) }}">
                            </div>
                            <div class="col-12">
                                <label for="deskripsi_atau_alasan_cancel" class="form-label">Deskripsi / Alasan Cancel</label>
                                <textarea name="deskripsi_atau_alasan_cancel" id="deskripsi_atau_alasan_cancel" class="form-control" rows="2">{{ old('deskripsi_atau_alasan_cancel', $dopm->deskripsi_atau_alasan_cancel) }}</textarea>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="sid_layer_2" class="form-label">SID Layer 2</label>
                                <input type="text" name="sid_layer_2" id="sid_layer_2" class="form-control" value="{{ old('sid_layer_2', $dopm->sid_layer_2) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nama_layer_2" class="form-label">Nama Layer 2</label>
                                <input type="text" name="nama_layer_2" id="nama_layer_2" class="form-control" value="{{ old('nama_layer_2', $dopm->nama_layer_2) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="sid_layer_3" class="form-label">SID Layer 3</label>
                                <input type="text" name="sid_layer_3" id="sid_layer_3" class="form-control" value="{{ old('sid_layer_3', $dopm->sid_layer_3) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nama_layer_3" class="form-label">Nama Layer 3</label>
                                <input type="text" name="nama_layer_3" id="nama_layer_3" class="form-control" value="{{ old('nama_layer_3', $dopm->nama_layer_3) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="sid_layer_4" class="form-label">SID Layer 4</label>
                                <input type="text" name="sid_layer_4" id="sid_layer_4" class="form-control" value="{{ old('sid_layer_4', $dopm->sid_layer_4) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nama_layer_4" class="form-label">Nama Layer 4</label>
                                <input type="text" name="nama_layer_4" id="nama_layer_4" class="form-control" value="{{ old('nama_layer_4', $dopm->nama_layer_4) }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="jenis_pengawasan_layer" class="form-label">Jenis Pengawasan Layer</label>
                                <input type="text" name="jenis_pengawasan_layer" id="jenis_pengawasan_layer" class="form-control" value="{{ old('jenis_pengawasan_layer', $dopm->jenis_pengawasan_layer) }}">
                            </div>
                            <div class="col-12">
                                <label for="detail_lokasi" class="form-label">Detail Lokasi</label>
                                <textarea name="detail_lokasi" id="detail_lokasi" class="form-control" rows="2">{{ old('detail_lokasi', $dopm->detail_lokasi) }}</textarea>
                            </div>
                        </div>
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('dopmikk.dopm.index') }}" class="btn btn-secondary rounded-3">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-3">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
