@extends('layouts.master')

@section('title', 'Tambah Insiden CCR')

@section('content')
    <x-page-title title="Tambah Insiden CCR" pagetitle="Manajemen Insiden CCR" />

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
                <div class="card-header">
                    <h5 class="mb-0">Form Tambah Insiden CCR</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('insiden-ccr.store') }}">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="ccr_id" class="form-label">CCR ID</label>
                                <input type="text" name="ccr_id" id="ccr_id" 
                                       class="form-control @error('ccr_id') is-invalid @enderror" 
                                       value="{{ old('ccr_id') }}">
                                @error('ccr_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="no_kecelakaan" class="form-label">No. Kecelakaan</label>
                                <input type="text" name="no_kecelakaan" id="no_kecelakaan" 
                                       class="form-control @error('no_kecelakaan') is-invalid @enderror" 
                                       value="{{ old('no_kecelakaan') }}">
                                @error('no_kecelakaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="ccr_jenis_insiden" class="form-label">Jenis Insiden</label>
                                <select name="ccr_jenis_insiden" id="ccr_jenis_insiden" 
                                        class="form-select @error('ccr_jenis_insiden') is-invalid @enderror">
                                    <option value="">-- Pilih Jenis Insiden --</option>
                                    @php
                                        $jenisOptions = ['Work Incident', 'Vehicle Incident', 'Equipment Incident', 'Fire Case', 'Water Incident', 'Illness', 'Non CCR - Golden Rules'];
                                    @endphp
                                    @foreach($jenisOptions as $option)
                                        <option value="{{ $option }}" {{ old('ccr_jenis_insiden') == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ccr_jenis_insiden')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ccr_waktu_insiden" class="form-label">Waktu Insiden</label>
                                <input type="datetime-local" name="ccr_waktu_insiden" id="ccr_waktu_insiden" 
                                       class="form-control @error('ccr_waktu_insiden') is-invalid @enderror" 
                                       value="{{ old('ccr_waktu_insiden') }}">
                                @error('ccr_waktu_insiden')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ccr_waktu_pelaporan" class="form-label">Waktu Pelaporan</label>
                                <input type="datetime-local" name="ccr_waktu_pelaporan" id="ccr_waktu_pelaporan" 
                                       class="form-control @error('ccr_waktu_pelaporan') is-invalid @enderror" 
                                       value="{{ old('ccr_waktu_pelaporan') }}">
                                @error('ccr_waktu_pelaporan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="ccr_kronologi" class="form-label">Kronologi</label>
                                <textarea name="ccr_kronologi" id="ccr_kronologi" rows="4" 
                                          class="form-control @error('ccr_kronologi') is-invalid @enderror" 
                                          placeholder="Kronologi kejadian secara detail">{{ old('ccr_kronologi') }}</textarea>
                                @error('ccr_kronologi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ccr_nama_call_taker" class="form-label">Nama Call Taker</label>
                                <input type="text" name="ccr_nama_call_taker" id="ccr_nama_call_taker" 
                                       class="form-control @error('ccr_nama_call_taker') is-invalid @enderror" 
                                       value="{{ old('ccr_nama_call_taker') }}">
                                @error('ccr_nama_call_taker')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ccr_perusahaan_call_taker" class="form-label">Perusahaan Call Taker</label>
                                <input type="text" name="ccr_perusahaan_call_taker" id="ccr_perusahaan_call_taker" 
                                       class="form-control @error('ccr_perusahaan_call_taker') is-invalid @enderror" 
                                       value="{{ old('ccr_perusahaan_call_taker') }}">
                                @error('ccr_perusahaan_call_taker')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ccr_nama_pelapor" class="form-label">Nama Pelapor</label>
                                <input type="text" name="ccr_nama_pelapor" id="ccr_nama_pelapor" 
                                       class="form-control @error('ccr_nama_pelapor') is-invalid @enderror" 
                                       value="{{ old('ccr_nama_pelapor') }}">
                                @error('ccr_nama_pelapor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ccr_perusahaan_pelapor" class="form-label">Perusahaan Pelapor</label>
                                <input type="text" name="ccr_perusahaan_pelapor" id="ccr_perusahaan_pelapor" 
                                       class="form-control @error('ccr_perusahaan_pelapor') is-invalid @enderror" 
                                       value="{{ old('ccr_perusahaan_pelapor') }}">
                                @error('ccr_perusahaan_pelapor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ccr_lokasi_perusahaan" class="form-label">Lokasi Perusahaan</label>
                                <input type="text" name="ccr_lokasi_perusahaan" id="ccr_lokasi_perusahaan" 
                                       class="form-control @error('ccr_lokasi_perusahaan') is-invalid @enderror" 
                                       value="{{ old('ccr_lokasi_perusahaan') }}">
                                @error('ccr_lokasi_perusahaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ccr_site" class="form-label">Site</label>
                                <select name="ccr_site" id="ccr_site" 
                                        class="form-select @error('ccr_site') is-invalid @enderror">
                                    <option value="">-- Pilih Site --</option>
                                    @php
                                        $siteOptions = ['SMO', 'LMO', 'GMO', 'BMO 1', 'BMO 2'];
                                    @endphp
                                    @foreach($siteOptions as $option)
                                        <option value="{{ $option }}" {{ old('ccr_site') == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ccr_site')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ccr_lokasi" class="form-label">Lokasi</label>
                                <input type="text" name="ccr_lokasi" id="ccr_lokasi" 
                                       class="form-control @error('ccr_lokasi') is-invalid @enderror" 
                                       value="{{ old('ccr_lokasi') }}">
                                @error('ccr_lokasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ccr_detil_lokasi" class="form-label">Detail Lokasi</label>
                                <input type="text" name="ccr_detil_lokasi" id="ccr_detil_lokasi" 
                                       class="form-control @error('ccr_detil_lokasi') is-invalid @enderror" 
                                       value="{{ old('ccr_detil_lokasi') }}">
                                @error('ccr_detil_lokasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="ccr_keterangan_lokasi" class="form-label">Keterangan Lokasi</label>
                                <textarea name="ccr_keterangan_lokasi" id="ccr_keterangan_lokasi" rows="2" 
                                          class="form-control @error('ccr_keterangan_lokasi') is-invalid @enderror">{{ old('ccr_keterangan_lokasi') }}</textarea>
                                @error('ccr_keterangan_lokasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="ccr_status" class="form-label">Status</label>
                                <select name="ccr_status" id="ccr_status" 
                                        class="form-select @error('ccr_status') is-invalid @enderror">
                                    <option value="">-- Pilih Status --</option>
                                    @php
                                        $statusOptions = ['INSIDEN BARU', 'INVESTIGASI', 'TIDAK INVESTIGASI'];
                                    @endphp
                                    @foreach($statusOptions as $option)
                                        <option value="{{ $option }}" {{ old('ccr_status') == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ccr_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="ccr_pic_investigasi" class="form-label">PIC Investigasi</label>
                                <input type="text" name="ccr_pic_investigasi" id="ccr_pic_investigasi" 
                                       class="form-control @error('ccr_pic_investigasi') is-invalid @enderror" 
                                       value="{{ old('ccr_pic_investigasi') }}">
                                @error('ccr_pic_investigasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="ccr_pic_investigasi_perusahaan" class="form-label">Perusahaan PIC Investigasi</label>
                                <input type="text" name="ccr_pic_investigasi_perusahaan" id="ccr_pic_investigasi_perusahaan" 
                                       class="form-control @error('ccr_pic_investigasi_perusahaan') is-invalid @enderror" 
                                       value="{{ old('ccr_pic_investigasi_perusahaan') }}">
                                @error('ccr_pic_investigasi_perusahaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="ket_not_investigasi" class="form-label">Keterangan Tidak Investigasi</label>
                                <textarea name="ket_not_investigasi" id="ket_not_investigasi" rows="2" 
                                          class="form-control @error('ket_not_investigasi') is-invalid @enderror">{{ old('ket_not_investigasi') }}</textarea>
                                @error('ket_not_investigasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary rounded-3">Simpan</button>
                            <a href="{{ route('insiden-ccr.index') }}" class="btn btn-secondary rounded-3">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
