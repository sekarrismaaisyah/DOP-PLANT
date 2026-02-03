@extends('layouts.master')

@section('title', 'Edit Insiden LPI')

@section('content')
    <x-page-title title="Edit Insiden LPI" pagetitle="Manajemen Insiden LPI" />

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
                    <h5 class="mb-0">Form Edit Insiden LPI - {{ $insiden->no_kecelakaan }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('insiden-lpi.update', $insiden) }}">
                        @csrf
                        @method('PUT')
                        
                        {{-- Relasi ke Insiden CCR --}}
                        <div class="card mb-4 bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Relasi Insiden CCR</h6>
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label for="insiden_ccr_id" class="form-label">Pilih Insiden CCR</label>
                                        <select name="insiden_ccr_id" id="insiden_ccr_id" class="form-select">
                                            <option value="">-- Pilih Insiden CCR (opsional) --</option>
                                            @foreach ($insidenCcrList as $ccr)
                                                <option value="{{ $ccr->id }}" {{ (old('insiden_ccr_id', $insiden->insiden_ccr_id) == $ccr->id) ? 'selected' : '' }}>
                                                    #{{ $ccr->ccr_id }} - [{{ $ccr->ccr_site ?? '-' }}] {{ $ccr->ccr_jenis_insiden }} 
                                                    ({{ $ccr->ccr_waktu_insiden?->format('d/m/Y') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Informasi Dasar --}}
                        <h6 class="mb-3">Informasi Dasar</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="no_kecelakaan" class="form-label">No. Kecelakaan</label>
                                <input type="text" name="no_kecelakaan" id="no_kecelakaan" class="form-control" 
                                       value="{{ old('no_kecelakaan', $insiden->no_kecelakaan) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="kode_be_investigasi" class="form-label">Kode BE Investigasi</label>
                                <input type="text" name="kode_be_investigasi" id="kode_be_investigasi" class="form-control" 
                                       value="{{ old('kode_be_investigasi', $insiden->kode_be_investigasi) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="status_lpi" class="form-label">Status LPI</label>
                                <select name="status_lpi" id="status_lpi" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Open" {{ old('status_lpi', $insiden->status_lpi) == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="Closed" {{ old('status_lpi', $insiden->status_lpi) == 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="target_penyelesaian_lpi" class="form-label">Target Penyelesaian</label>
                                <input type="date" name="target_penyelesaian_lpi" id="target_penyelesaian_lpi" class="form-control" 
                                       value="{{ old('target_penyelesaian_lpi', $insiden->target_penyelesaian_lpi?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="actual_penyelesaian_lpi" class="form-label">Actual Penyelesaian</label>
                                <input type="date" name="actual_penyelesaian_lpi" id="actual_penyelesaian_lpi" class="form-control" 
                                       value="{{ old('actual_penyelesaian_lpi', $insiden->actual_penyelesaian_lpi?->format('Y-m-d')) }}">
                            </div>
                        </div>

                        {{-- Waktu & Lokasi --}}
                        <h6 class="mb-3">Waktu & Lokasi</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-2">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="number" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', $insiden->tanggal) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="bulan" class="form-label">Bulan</label>
                                <input type="number" name="bulan" id="bulan" class="form-control" value="{{ old('bulan', $insiden->bulan) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="tahun" class="form-label">Tahun</label>
                                <input type="number" name="tahun" id="tahun" class="form-control" value="{{ old('tahun', $insiden->tahun) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="jam" class="form-label">Jam</label>
                                <input type="number" name="jam" id="jam" class="form-control" value="{{ old('jam', $insiden->jam) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="menit" class="form-label">Menit</label>
                                <input type="number" name="menit" id="menit" class="form-control" value="{{ old('menit', $insiden->menit) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="shift" class="form-label">Shift</label>
                                <input type="text" name="shift" id="shift" class="form-control" value="{{ old('shift', $insiden->shift) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="site" class="form-label">Site</label>
                                <input type="text" name="site" id="site" class="form-control" value="{{ old('site', $insiden->site) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="lokasi" class="form-label">Lokasi</label>
                                <input type="text" name="lokasi" id="lokasi" class="form-control" value="{{ old('lokasi', $insiden->lokasi) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="sublokasi" class="form-label">Sublokasi</label>
                                <input type="text" name="sublokasi" id="sublokasi" class="form-control" value="{{ old('sublokasi', $insiden->sublokasi) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="perusahaan" class="form-label">Perusahaan</label>
                                <input type="text" name="perusahaan" id="perusahaan" class="form-control" value="{{ old('perusahaan', $insiden->perusahaan) }}">
                            </div>
                        </div>

                        {{-- Kategori & Kronologi --}}
                        <h6 class="mb-3">Kategori & Kronologi</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <input type="text" name="kategori" id="kategori" class="form-control" value="{{ old('kategori', $insiden->kategori) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="injury_status" class="form-label">Injury Status</label>
                                <select name="injury_status" id="injury_status" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    <option value="Injury" {{ old('injury_status', $insiden->injury_status) == 'Injury' ? 'selected' : '' }}>Injury</option>
                                    <option value="Non Injury" {{ old('injury_status', $insiden->injury_status) == 'Non Injury' ? 'selected' : '' }}>Non Injury</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="high_potential" class="form-label">High Potential</label>
                                <input type="text" name="high_potential" id="high_potential" class="form-control" value="{{ old('high_potential', $insiden->high_potential) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="alat_terlibat" class="form-label">Alat Terlibat</label>
                                <input type="text" name="alat_terlibat" id="alat_terlibat" class="form-control" value="{{ old('alat_terlibat', $insiden->alat_terlibat) }}">
                            </div>
                            <div class="col-12">
                                <label for="kronologis" class="form-label">Kronologis</label>
                                <textarea name="kronologis" id="kronologis" rows="3" class="form-control">{{ old('kronologis', $insiden->kronologis) }}</textarea>
                            </div>
                        </div>

                        {{-- Data Korban --}}
                        <h6 class="mb-3">Data Korban</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $insiden->nama) }}">
                            </div>
                            <div class="col-md-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <input type="text" name="jabatan" id="jabatan" class="form-control" value="{{ old('jabatan', $insiden->jabatan) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="npk" class="form-label">NPK</label>
                                <input type="text" name="npk" id="npk" class="form-control" value="{{ old('npk', $insiden->npk) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="umur" class="form-label">Umur</label>
                                <input type="number" name="umur" id="umur" class="form-control" value="{{ old('umur', $insiden->umur) }}">
                            </div>
                            <div class="col-md-2">
                                <label for="bagian_luka" class="form-label">Bagian Luka</label>
                                <input type="text" name="bagian_luka" id="bagian_luka" class="form-control" value="{{ old('bagian_luka', $insiden->bagian_luka) }}">
                            </div>
                        </div>

                        {{-- Layer Information --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Layer Information</h6>
                            <button type="button" class="btn btn-sm btn-success rounded-3" id="addLayerBtn">
                                <i class="bx bx-plus"></i> Tambah Layer
                            </button>
                        </div>
                        
                        <div id="layersContainer">
                            @forelse($insiden->layers as $index => $layer)
                            <div class="layer-row card mb-3 bg-light" data-index="{{ $index }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary">Layer #{{ $index + 1 }}</span>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-layer-btn" @if($insiden->layers->count() <= 1) style="display: none;" @endif>
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Layer</label>
                                            <select name="layers[{{ $index }}][layer]" class="form-select">
                                                <option value="">-- Pilih Layer --</option>
                                                <option value="Layer 1" {{ $layer->layer == 'Layer 1' ? 'selected' : '' }}>Layer 1</option>
                                                <option value="Layer 2" {{ $layer->layer == 'Layer 2' ? 'selected' : '' }}>Layer 2</option>
                                                <option value="Layer 3" {{ $layer->layer == 'Layer 3' ? 'selected' : '' }}>Layer 3</option>
                                                @if($layer->layer && !in_array($layer->layer, ['Layer 1', 'Layer 2', 'Layer 3']))
                                                <option value="{{ $layer->layer }}" selected>{{ $layer->layer }}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Jenis Item IPLS</label>
                                            <select name="layers[{{ $index }}][jenis_item_ipls]" class="form-select">
                                                <option value="">-- Pilih --</option>
                                                <option value="Nonconformity" {{ $layer->jenis_item_ipls == 'Nonconformity' ? 'selected' : '' }}>Nonconformity</option>
                                                <option value="Rootcause" {{ $layer->jenis_item_ipls == 'Rootcause' ? 'selected' : '' }}>Rootcause</option>
                                                @if($layer->jenis_item_ipls && !in_array($layer->jenis_item_ipls, ['Nonconformity', 'Rootcause']))
                                                <option value="{{ $layer->jenis_item_ipls }}" selected>{{ $layer->jenis_item_ipls }}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Detail Layer</label>
                                            <input type="text" name="layers[{{ $index }}][detail_layer]" class="form-control" value="{{ $layer->detail_layer }}" placeholder="Contoh: 3.6 Kondisi Area Kerja">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Keterangan Layer</label>
                                            <textarea name="layers[{{ $index }}][keterangan_layer]" rows="3" class="form-control" placeholder="Keterangan detail...">{{ $layer->keterangan_layer }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            {{-- Empty state - show one empty layer row --}}
                            <div class="layer-row card mb-3 bg-light" data-index="0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary">Layer #1</span>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-layer-btn" style="display: none;">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Layer</label>
                                            <select name="layers[0][layer]" class="form-select">
                                                <option value="">-- Pilih Layer --</option>
                                                <option value="Layer 1">Layer 1</option>
                                                <option value="Layer 2">Layer 2</option>
                                                <option value="Layer 3">Layer 3</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Jenis Item IPLS</label>
                                            <select name="layers[0][jenis_item_ipls]" class="form-select">
                                                <option value="">-- Pilih --</option>
                                                <option value="Nonconformity">Nonconformity</option>
                                                <option value="Rootcause">Rootcause</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Detail Layer</label>
                                            <input type="text" name="layers[0][detail_layer]" class="form-control" placeholder="Contoh: 3.6 Kondisi Area Kerja">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Keterangan Layer</label>
                                            <textarea name="layers[0][keterangan_layer]" rows="3" class="form-control" placeholder="Keterangan detail..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforelse
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary rounded-3">Update</button>
                            <a href="{{ route('insiden-lpi.index') }}" class="btn btn-secondary rounded-3">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let layerIndex = {{ $insiden->layers->count() > 0 ? $insiden->layers->count() - 1 : 0 }};
    const container = document.getElementById('layersContainer');
    const addBtn = document.getElementById('addLayerBtn');

    function updateLayerNumbers() {
        const rows = container.querySelectorAll('.layer-row');
        rows.forEach((row, index) => {
            row.querySelector('.badge').textContent = 'Layer #' + (index + 1);
            // Show/hide remove button
            const removeBtn = row.querySelector('.remove-layer-btn');
            if (rows.length > 1) {
                removeBtn.style.display = 'inline-block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    addBtn.addEventListener('click', function() {
        layerIndex++;
        const template = `
            <div class="layer-row card mb-3 bg-light" data-index="${layerIndex}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary">Layer #${layerIndex + 1}</span>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-layer-btn">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Layer</label>
                            <select name="layers[${layerIndex}][layer]" class="form-select">
                                <option value="">-- Pilih Layer --</option>
                                <option value="Layer 1">Layer 1</option>
                                <option value="Layer 2">Layer 2</option>
                                <option value="Layer 3">Layer 3</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Jenis Item IPLS</label>
                            <select name="layers[${layerIndex}][jenis_item_ipls]" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Nonconformity">Nonconformity</option>
                                <option value="Rootcause">Rootcause</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Detail Layer</label>
                            <input type="text" name="layers[${layerIndex}][detail_layer]" class="form-control" placeholder="Contoh: 3.6 Kondisi Area Kerja">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan Layer</label>
                            <textarea name="layers[${layerIndex}][keterangan_layer]" rows="3" class="form-control" placeholder="Keterangan detail..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', template);
        updateLayerNumbers();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-layer-btn')) {
            const row = e.target.closest('.layer-row');
            row.remove();
            updateLayerNumbers();
        }
    });

    updateLayerNumbers();
});
</script>
@endsection
