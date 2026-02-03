@extends('layouts.master')

@section('title', 'Tambah Insiden LPI')

@section('content')
    <x-page-title title="Tambah Insiden LPI" pagetitle="Manajemen Insiden LPI" />

    @if ($errors->any())
        <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-error-circle fs-4 me-2"></i>
                <div>
                    <strong>Perhatian!</strong> Terdapat kesalahan pada form:
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('insiden-lpi.store') }}">
        @csrf
        
        {{-- Relasi CCR Card --}}
        <div class="card rounded-4 border-0 shadow-sm mb-4">
            <div class="card-header  rounded-top-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-link-alt fs-4 me-2"></i>
                    <h6 class="mb-0 fw-semibold">Relasi Insiden CCR</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <label for="insiden_ccr_id" class="form-label">
                            <i class="bx bx-search-alt text-primary me-1"></i> Pilih Insiden CCR
                        </label>
                        <select name="insiden_ccr_id" id="insiden_ccr_id" class="form-select form-select-lg">
                            <option value="">-- Pilih Insiden CCR (opsional) --</option>
                            @foreach ($insidenCcrList as $ccr)
                                <option value="{{ $ccr->id }}" {{ (old('insiden_ccr_id', $selectedCcrId) == $ccr->id) ? 'selected' : '' }}>
                                    #{{ $ccr->ccr_id }} - [{{ $ccr->ccr_site ?? '-' }}] {{ $ccr->ccr_jenis_insiden }} 
                                    ({{ $ccr->ccr_waktu_insiden?->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted"><i class="bx bx-info-circle"></i> Data akan terisi otomatis saat memilih Insiden CCR</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- ============ KOLOM KIRI ============ --}}
            <div class="col-lg-6">
                {{-- Informasi Dasar --}}
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center">
                            <span class="bg-info bg-opacity-10 p-2 rounded-3 me-2">
                                <i class="bx bx-id-card text-info fs-5"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Informasi Dasar</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="no_kecelakaan" class="form-label">
                                    <i class="bx bx-hash text-muted me-1"></i> No. Kecelakaan
                                </label>
                                <input type="text" name="no_kecelakaan" id="no_kecelakaan" class="form-control" 
                                       value="{{ old('no_kecelakaan') }}" placeholder="Masukkan no. kecelakaan">
                            </div>
                            <div class="col-md-6">
                                <label for="kode_be_investigasi" class="form-label">
                                    <i class="bx bx-barcode text-muted me-1"></i> Kode BE Investigasi
                                </label>
                                <input type="text" name="kode_be_investigasi" id="kode_be_investigasi" class="form-control" 
                                       value="{{ old('kode_be_investigasi') }}" placeholder="Masukkan kode BE">
                            </div>
                            <div class="col-md-4">
                                <label for="status_lpi" class="form-label">
                                    <i class="bx bx-loader-circle text-muted me-1"></i> Status LPI
                                </label>
                                <select name="status_lpi" id="status_lpi" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Open" {{ old('status_lpi') == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="Closed" {{ old('status_lpi') == 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="target_penyelesaian_lpi" class="form-label">
                                    <i class="bx bx-target-lock text-muted me-1"></i> Target
                                </label>
                                <input type="date" name="target_penyelesaian_lpi" id="target_penyelesaian_lpi" class="form-control" 
                                       value="{{ old('target_penyelesaian_lpi') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="actual_penyelesaian_lpi" class="form-label">
                                    <i class="bx bx-check-circle text-muted me-1"></i> Actual
                                </label>
                                <input type="date" name="actual_penyelesaian_lpi" id="actual_penyelesaian_lpi" class="form-control" 
                                       value="{{ old('actual_penyelesaian_lpi') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Waktu Insiden --}}
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center">
                            <span class="bg-warning bg-opacity-10 p-2 rounded-3 me-2">
                                <i class="bx bx-time-five text-warning fs-5"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Waktu Insiden</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-4">
                                <label for="tanggal" class="form-label">
                                    <i class="bx bx-calendar-event text-muted me-1"></i> Tanggal
                                </label>
                                <input type="number" name="tanggal" id="tanggal" class="form-control" 
                                       value="{{ old('tanggal') }}" placeholder="1-31" min="1" max="31">
                            </div>
                            <div class="col-4">
                                <label for="bulan" class="form-label">
                                    <i class="bx bx-calendar text-muted me-1"></i> Bulan
                                </label>
                                <input type="number" name="bulan" id="bulan" class="form-control" 
                                       value="{{ old('bulan') }}" placeholder="1-12" min="1" max="12">
                            </div>
                            <div class="col-4">
                                <label for="tahun" class="form-label">
                                    <i class="bx bx-calendar-alt text-muted me-1"></i> Tahun
                                </label>
                                <input type="number" name="tahun" id="tahun" class="form-control" 
                                       value="{{ old('tahun') }}" placeholder="2024">
                            </div>
                            <div class="col-4">
                                <label for="jam" class="form-label">
                                    <i class="bx bx-time text-muted me-1"></i> Jam
                                </label>
                                <input type="number" name="jam" id="jam" class="form-control" 
                                       value="{{ old('jam') }}" placeholder="0-23" min="0" max="23">
                            </div>
                            <div class="col-4">
                                <label for="menit" class="form-label">
                                    <i class="bx bx-stopwatch text-muted me-1"></i> Menit
                                </label>
                                <input type="number" name="menit" id="menit" class="form-control" 
                                       value="{{ old('menit') }}" placeholder="0-59" min="0" max="59">
                            </div>
                            <div class="col-4">
                                <label for="shift" class="form-label">
                                    <i class="bx bx-transfer-alt text-muted me-1"></i> Shift
                                </label>
                                <input type="text" name="shift" id="shift" class="form-control" 
                                       value="{{ old('shift') }}" placeholder="Shift">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Korban --}}
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center">
                            <span class="bg-danger bg-opacity-10 p-2 rounded-3 me-2">
                                <i class="bx bx-user-circle text-danger fs-5"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Data Korban</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama" class="form-label">
                                    <i class="bx bx-user text-muted me-1"></i> Nama
                                </label>
                                <input type="text" name="nama" id="nama" class="form-control" 
                                       value="{{ old('nama') }}" placeholder="Nama korban">
                            </div>
                            <div class="col-md-6">
                                <label for="jabatan" class="form-label">
                                    <i class="bx bx-briefcase text-muted me-1"></i> Jabatan
                                </label>
                                <input type="text" name="jabatan" id="jabatan" class="form-control" 
                                       value="{{ old('jabatan') }}" placeholder="Jabatan korban">
                            </div>
                            <div class="col-md-4">
                                <label for="npk" class="form-label">
                                    <i class="bx bx-id-card text-muted me-1"></i> NPK
                                </label>
                                <input type="text" name="npk" id="npk" class="form-control" 
                                       value="{{ old('npk') }}" placeholder="No. induk">
                            </div>
                            <div class="col-md-4">
                                <label for="umur" class="form-label">
                                    <i class="bx bx-cake text-muted me-1"></i> Umur
                                </label>
                                <div class="input-group">
                                    <input type="number" name="umur" id="umur" class="form-control" 
                                           value="{{ old('umur') }}" placeholder="Umur">
                                    <span class="input-group-text">th</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="bagian_luka" class="form-label">
                                    <i class="bx bx-plus-medical text-muted me-1"></i> Bagian Luka
                                </label>
                                <input type="text" name="bagian_luka" id="bagian_luka" class="form-control" 
                                       value="{{ old('bagian_luka') }}" placeholder="Bagian tubuh">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ KOLOM KANAN ============ --}}
            <div class="col-lg-6">
                {{-- Lokasi Insiden --}}
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center">
                            <span class="bg-success bg-opacity-10 p-2 rounded-3 me-2">
                                <i class="bx bx-map text-success fs-5"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Lokasi Insiden</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="site" class="form-label">
                                    <i class="bx bx-buildings text-muted me-1"></i> Site
                                </label>
                                <input type="text" name="site" id="site" class="form-control" 
                                       value="{{ old('site') }}" placeholder="Nama site">
                            </div>
                            <div class="col-md-6">
                                <label for="perusahaan" class="form-label">
                                    <i class="bx bx-building-house text-muted me-1"></i> Perusahaan
                                </label>
                                <input type="text" name="perusahaan" id="perusahaan" class="form-control" 
                                       value="{{ old('perusahaan') }}" placeholder="Nama perusahaan">
                            </div>
                            <div class="col-md-6">
                                <label for="lokasi" class="form-label">
                                    <i class="bx bx-map-pin text-muted me-1"></i> Lokasi
                                </label>
                                <input type="text" name="lokasi" id="lokasi" class="form-control" 
                                       value="{{ old('lokasi') }}" placeholder="Lokasi insiden">
                            </div>
                            <div class="col-md-6">
                                <label for="sublokasi" class="form-label">
                                    <i class="bx bx-current-location text-muted me-1"></i> Sublokasi
                                </label>
                                <input type="text" name="sublokasi" id="sublokasi" class="form-control" 
                                       value="{{ old('sublokasi') }}" placeholder="Detail sublokasi">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kategori Insiden --}}
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center">
                            <span class="bg-primary bg-opacity-10 p-2 rounded-3 me-2">
                                <i class="bx bx-category text-primary fs-5"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Kategori Insiden</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="kategori" class="form-label">
                                    <i class="bx bx-purchase-tag text-muted me-1"></i> Kategori
                                </label>
                                <input type="text" name="kategori" id="kategori" class="form-control" 
                                       value="{{ old('kategori') }}" placeholder="Jenis kategori">
                            </div>
                            <div class="col-md-6">
                                <label for="injury_status" class="form-label">
                                    <i class="bx bx-heart text-muted me-1"></i> Injury Status
                                </label>
                                <select name="injury_status" id="injury_status" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Injury" {{ old('injury_status') == 'Injury' ? 'selected' : '' }}>Injury</option>
                                    <option value="Non Injury" {{ old('injury_status') == 'Non Injury' ? 'selected' : '' }}>Non Injury</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="high_potential" class="form-label">
                                    <i class="bx bx-error text-muted me-1"></i> High Potential
                                </label>
                                <input type="text" name="high_potential" id="high_potential" class="form-control" 
                                       value="{{ old('high_potential') }}" placeholder="High potential">
                            </div>
                            <div class="col-md-6">
                                <label for="alat_terlibat" class="form-label">
                                    <i class="bx bx-wrench text-muted me-1"></i> Alat Terlibat
                                </label>
                                <input type="text" name="alat_terlibat" id="alat_terlibat" class="form-control" 
                                       value="{{ old('alat_terlibat') }}" placeholder="Alat/kendaraan">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kronologis --}}
                <div class="card rounded-4 border-0 shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <div class="d-flex align-items-center">
                            <span class="bg-secondary bg-opacity-10 p-2 rounded-3 me-2">
                                <i class="bx bx-detail text-secondary fs-5"></i>
                            </span>
                            <h6 class="mb-0 fw-semibold">Kronologis</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <label for="kronologis" class="form-label">
                            <i class="bx bx-file text-muted me-1"></i> Deskripsi Kronologis Kejadian
                        </label>
                        <textarea name="kronologis" id="kronologis" rows="6" class="form-control" 
                                  placeholder="Jelaskan kronologis kejadian secara detail...">{{ old('kronologis') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Layer Information - Full Width --}}
        <div class="card rounded-4 border-0 shadow-sm mb-4">
            <div class="card-header bg-light border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="bg-purple bg-opacity-10 p-2 rounded-3 me-2" style="background-color: rgba(111, 66, 193, 0.1);">
                            <i class="bx bx-layer text-purple fs-5" style="color: #6f42c1;"></i>
                        </span>
                        <h6 class="mb-0 fw-semibold">Layer Information</h6>
                    </div>
                    <button type="button" class="btn btn-success btn-sm rounded-3" id="addLayerBtn">
                        <i class="bx bx-plus me-1"></i> Tambah Layer
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="layersContainer">
                    {{-- Layer Row Template --}}
                    <div class="layer-row border rounded-3 p-3 mb-3 bg-light" data-index="0">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                <i class="bx bx-layer me-1"></i> Layer #1
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle remove-layer-btn" style="display: none;">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="bx bx-layer text-muted me-1"></i> Layer
                                </label>
                                <select name="layers[0][layer]" class="form-select">
                                    <option value="">-- Pilih Layer --</option>
                                    <option value="Layer 1">Layer 1</option>
                                    <option value="Layer 2">Layer 2</option>
                                    <option value="Layer 3">Layer 3</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="bx bx-list-check text-muted me-1"></i> Jenis Item IPLS
                                </label>
                                <select name="layers[0][jenis_item_ipls]" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Nonconformity">Nonconformity</option>
                                    <option value="Rootcause">Rootcause</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bx bx-detail text-muted me-1"></i> Detail Layer
                                </label>
                                <input type="text" name="layers[0][detail_layer]" class="form-control" placeholder="Contoh: 3.6 Kondisi Area Kerja">
                            </div>
                            <div class="col-12">
                                <label class="form-label">
                                    <i class="bx bx-note text-muted me-1"></i> Keterangan Layer
                                </label>
                                <textarea name="layers[0][keterangan_layer]" rows="2" class="form-control" placeholder="Keterangan detail..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="card rounded-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('insiden-lpi.index') }}" class="btn btn-outline-secondary rounded-3">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                    <div class="d-flex gap-2">
                        <button type="reset" class="btn btn-light rounded-3">
                            <i class="bx bx-refresh me-1"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-primary rounded-3 px-4">
                            <i class="bx bx-save me-1"></i> Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let layerIndex = 0;
    const container = document.getElementById('layersContainer');
    const addBtn = document.getElementById('addLayerBtn');
    const ccrSelect = document.getElementById('insiden_ccr_id');

    // Auto-fill from CCR data
    ccrSelect.addEventListener('change', function() {
        const ccrId = this.value;
        if (!ccrId) {
            return;
        }

        // Show loading state
        ccrSelect.classList.add('opacity-50');
        
        fetch(`{{ url('insiden-lpi/ccr-data') }}/${ccrId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                const data = result.data;
                
                // Auto-fill fields if they have data with highlight effect
                const fieldsToFill = [
                    { id: 'no_kecelakaan', value: data.no_kecelakaan },
                    { id: 'tanggal', value: data.tanggal },
                    { id: 'bulan', value: data.bulan },
                    { id: 'tahun', value: data.tahun },
                    { id: 'jam', value: data.jam },
                    { id: 'menit', value: data.menit },
                    { id: 'site', value: data.site },
                    { id: 'lokasi', value: data.lokasi },
                    { id: 'sublokasi', value: data.sublokasi },
                    { id: 'perusahaan', value: data.perusahaan },
                    { id: 'kategori', value: data.kategori },
                    { id: 'kronologis', value: data.kronologis }
                ];
                
                fieldsToFill.forEach(field => {
                    if (field.value) {
                        const element = document.getElementById(field.id);
                        if (element) {
                            element.value = field.value;
                            // Add highlight animation
                            element.classList.add('bg-success', 'bg-opacity-10');
                            setTimeout(() => {
                                element.classList.remove('bg-success', 'bg-opacity-10');
                            }, 1500);
                        }
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error fetching CCR data:', error);
        })
        .finally(() => {
            ccrSelect.classList.remove('opacity-50');
        });
    });

    function updateLayerNumbers() {
        const rows = container.querySelectorAll('.layer-row');
        rows.forEach((row, index) => {
            const badge = row.querySelector('.badge');
            badge.innerHTML = '<i class="bx bx-layer me-1"></i> Layer #' + (index + 1);
            // Show/hide remove button
            const removeBtn = row.querySelector('.remove-layer-btn');
            if (rows.length > 1) {
                removeBtn.style.display = 'flex';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    addBtn.addEventListener('click', function() {
        layerIndex++;
        const template = `
            <div class="layer-row border rounded-3 p-3 mb-3 bg-light" data-index="${layerIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-primary rounded-pill px-3 py-2">
                        <i class="bx bx-layer me-1"></i> Layer #${layerIndex + 1}
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle remove-layer-btn">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bx bx-layer text-muted me-1"></i> Layer
                        </label>
                        <select name="layers[${layerIndex}][layer]" class="form-select">
                            <option value="">-- Pilih Layer --</option>
                            <option value="Layer 1">Layer 1</option>
                            <option value="Layer 2">Layer 2</option>
                            <option value="Layer 3">Layer 3</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bx bx-list-check text-muted me-1"></i> Jenis Item IPLS
                        </label>
                        <select name="layers[${layerIndex}][jenis_item_ipls]" class="form-select">
                            <option value="">-- Pilih --</option>
                            <option value="Nonconformity">Nonconformity</option>
                            <option value="Rootcause">Rootcause</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bx bx-detail text-muted me-1"></i> Detail Layer
                        </label>
                        <input type="text" name="layers[${layerIndex}][detail_layer]" class="form-control" placeholder="Contoh: 3.6 Kondisi Area Kerja">
                    </div>
                    <div class="col-12">
                        <label class="form-label">
                            <i class="bx bx-note text-muted me-1"></i> Keterangan Layer
                        </label>
                        <textarea name="layers[${layerIndex}][keterangan_layer]" rows="2" class="form-control" placeholder="Keterangan detail..."></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
        updateLayerNumbers();
        
        // Scroll to new layer with smooth animation
        const newLayer = container.lastElementChild;
        newLayer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        newLayer.classList.add('border-primary');
        setTimeout(() => newLayer.classList.remove('border-primary'), 1500);
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-layer-btn')) {
            const row = e.target.closest('.layer-row');
            row.style.opacity = '0';
            row.style.transform = 'translateX(20px)';
            setTimeout(() => {
                row.remove();
                updateLayerNumbers();
            }, 200);
        }
    });

    updateLayerNumbers();

    // Trigger auto-fill if CCR already selected (e.g., from query param)
    if (ccrSelect.value) {
        ccrSelect.dispatchEvent(new Event('change'));
    }
});
</script>

<style>
    .layer-row {
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
    }
    .card {
        transition: box-shadow 0.2s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
</style>
@endsection
