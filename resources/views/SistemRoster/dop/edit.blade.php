@extends('layouts.masterRoster')

@section('title', 'Edit Daily Operation Plan')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@section('content')
    <x-page-title title="Edit Daily Operation Plan" pagetitle="Edit DOP" />

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
        </div>
    </div>

    <form method="POST" action="{{ route('sistem-roster.dop.update', $dop->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-12">
                <div class="card rounded-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Umum</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', $dop->tanggal->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="pekerjaan" class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                                <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" value="{{ old('pekerjaan', $dop->pekerjaan) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="aktivitas" class="form-label">Aktivitas</label>
                                <select name="aktivitas" id="aktivitas" class="form-select">
                                    <option value="">-- Pilih Aktivitas --</option>
                                    @isset($masterAktivitas)
                                        @foreach($masterAktivitas as $akt)
                                            <option value="{{ $akt->nama_aktivitas }}" {{ old('aktivitas', $dop->aktivitas) == $akt->nama_aktivitas ? 'selected' : '' }}>{{ $akt->nama_aktivitas }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="lokasi_select" class="form-label">Lokasi (dari master) <span class="text-muted">— ketik untuk cari, pilih untuk mengisi Site, Lokasi, Detail Lokasi</span></label>
                                <select id="lokasi_select" class="form-select" data-placeholder="Cari atau pilih lokasi...">
                                    <option value="">-- Pilih Lokasi --</option>
                                    @isset($lokasiList)
                                        @foreach($lokasiList as $loc)
                                            @php
                                                $site = $loc['site'] ?? '';
                                                $lokasi = $loc['lokasi'] ?? '';
                                                $detil = $loc['detil_lokasi'] ?? '';
                                                $label = trim(implode(' - ', array_filter([$site, $lokasi, $detil])));
                                                $value = json_encode(['site' => $site, 'lokasi' => $lokasi, 'detil_lokasi' => $detil]);
                                            @endphp
                                            <option value="{{ htmlspecialchars($value) }}" data-site="{{ e($site) }}" data-lokasi="{{ e($lokasi) }}" data-detil="{{ e($detil) }}">{{ $label ?: '-' }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="unit_id" class="form-label">Unit ID / Site <span class="text-danger">*</span></label>
                                <input type="text" name="unit_id" id="unit_id" class="form-control" value="{{ old('unit_id', $dop->unit_id) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" name="lokasi" id="lokasi" class="form-control" value="{{ old('lokasi', $dop->lokasi) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="perusahaan" class="form-label">Perusahaan</label>
                                <input type="text" name="perusahaan" id="perusahaan" class="form-control" value="{{ old('perusahaan', $dop->perusahaan) }}" placeholder="Nama perusahaan">
                            </div>
                            <div class="col-md-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" step="any" name="latitude" id="latitude" class="form-control" value="{{ old('latitude', $dop->latitude) }}" placeholder="-2.186253" min="-90" max="90">
                                <small class="text-muted">Format: -90 sampai 90</small>
                            </div>
                            <div class="col-md-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" step="any" name="longitude" id="longitude" class="form-control" value="{{ old('longitude', $dop->longitude) }}" placeholder="117.4539035" min="-180" max="180">
                                <small class="text-muted">Format: -180 sampai 180</small>
                            </div>
                            <div class="col-12">
                                <label for="detail_lokasi" class="form-label">Detail Lokasi</label>
                                <textarea name="detail_lokasi" id="detail_lokasi" class="form-control" rows="2">{{ old('detail_lokasi', $dop->detail_lokasi) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label for="foto_pekerjaan" class="form-label">Upload Foto Pekerjaan</label>
                                @if($dop->foto_pekerjaan)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $dop->foto_pekerjaan) }}" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">
                                        <p class="text-muted small mt-1">Foto saat ini</p>
                                    </div>
                                @endif
                                <input type="file" name="foto_pekerjaan" id="foto_pekerjaan" class="form-control" accept="image/*" onchange="previewImage(this)">
                                <small class="text-muted">Format: JPG, PNG | Max 5MB (kosongkan jika tidak ingin mengubah)</small>
                                <div id="imagePreview" class="mt-2"></div>
                            </div>
                            <div class="col-12">
                                <label for="cctv_ids" class="form-label">CCTV yang Mengcover</label>
                                <select name="cctv_ids[]" id="cctv_ids" class="form-select" multiple="multiple" data-placeholder="Pilih CCTV yang mengcover area pekerjaan">
                                    @if($cctvs->count() > 0)
                                        @foreach($cctvs as $cctv)
                                            <option value="{{ $cctv->id }}" {{ (old('cctv_ids') ? in_array($cctv->id, old('cctv_ids')) : $dop->cctvs->contains($cctv->id)) ? 'selected' : '' }}>
                                                {{ $cctv->nama_cctv }}{!! $cctv->no_cctv ? ' (' . $cctv->no_cctv . ')' : '' !!}{!! $cctv->lokasi_pemasangan ? ' - ' . $cctv->lokasi_pemasangan : '' !!}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>-- Tidak ada CCTV tersedia --</option>
                                    @endif
                                </select>
                                <small class="text-muted d-block mt-1">
                                    <i class="bx bx-info-circle"></i> Pilih satu atau lebih CCTV yang mengcover area pekerjaan
                                </small>
                            </div>
                            <div class="col-12">
                                <label for="potensi_resiko" class="form-label">Potensi Risiko</label>
                                <textarea name="potensi_resiko" id="potensi_resiko" class="form-control" rows="3" placeholder="Masukkan potensi risiko (satu per baris atau dipisahkan dengan koma)">{{ old('potensi_resiko', $dop->potensi_resiko) }}</textarea>
                                <small class="text-muted">Contoh: Tenggelam, Terbalik, Terguling</small>
                            </div>
                            <div class="col-12">
                                <label for="pengendalian_bahaya" class="form-label">Pengendalian Bahaya</label>
                                <textarea name="pengendalian_bahaya" id="pengendalian_bahaya" class="form-control" rows="3" placeholder="Masukkan pengendalian bahaya (satu per baris atau dipisahkan dengan koma)">{{ old('pengendalian_bahaya', $dop->pengendalian_bahaya) }}</textarea>
                                <small class="text-muted">Contoh: Assessment, JSA, SOP, Pengawas KPO</small>
                            </div>
                            <div class="col-12">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea name="catatan" id="catatan" class="form-control" rows="3">{{ old('catatan', $dop->catatan) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-lg-6">
                <div class="card rounded-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">PIC PT Berau Coal</h5>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addPicRow()">
                            <i class="bx bx-plus"></i> Tambah
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="picContainer">
                            @forelse($dop->picBerauCoal as $index => $pic)
                                <div class="pic-row mb-3 p-3 border rounded">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label small">Shift</label>
                                            <select name="pic_berau_coal[{{ $index }}][shift]" class="form-control form-control-sm" required>
                                                <option value="">Pilih Shift</option>
                                                <option value="Shift 1 s/d 2" {{ $pic->shift == 'Shift 1 s/d 2' ? 'selected' : '' }}>Shift 1 s/d 2</option>
                                                <option value="Shift 2 s/d 1" {{ $pic->shift == 'Shift 2 s/d 1' ? 'selected' : '' }}>Shift 2 s/d 1</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Nama PIC</label>
                                            <input type="text" name="pic_berau_coal[{{ $index }}][nama_pic]" class="form-control form-control-sm" value="{{ $pic->nama_pic }}" placeholder="Nama PIC" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Layer</label>
                                            <input type="text" name="pic_berau_coal[{{ $index }}][layer]" class="form-control form-control-sm" value="{{ $pic->layer }}" placeholder="Layer (opsional)">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removePicRow(this)">
                                        <i class="bx bx-trash"></i> Hapus
                                    </button>
                                </div>
                            @empty
                                <div class="pic-row mb-3 p-3 border rounded">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label small">Shift</label>
                                            <select name="pic_berau_coal[0][shift]" class="form-control form-control-sm" required>
                                                <option value="">Pilih Shift</option>
                                                <option value="Shift 1 s/d 2">Shift 1 s/d 2</option>
                                                <option value="Shift 2 s/d 1">Shift 2 s/d 1</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Nama PIC</label>
                                            <input type="text" name="pic_berau_coal[0][nama_pic]" class="form-control form-control-sm" placeholder="Nama PIC" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Layer</label>
                                            <input type="text" name="pic_berau_coal[0][layer]" class="form-control form-control-sm" placeholder="Layer (opsional)">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removePicRow(this)">
                                        <i class="bx bx-trash"></i> Hapus
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card rounded-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pengawas Mitra Kerja</h5>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addPengawasRow()">
                            <i class="bx bx-plus"></i> Tambah
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="pengawasContainer">
                            @forelse($dop->pengawasMitraKerja as $index => $pengawas)
                                <div class="pengawas-row mb-3 p-3 border rounded">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label small">Shift</label>
                                            <select name="pengawas_mitra_kerja[{{ $index }}][shift]" class="form-control form-control-sm" required>
                                                <option value="">Pilih Shift</option>
                                                <option value="Shift 1 s/d 2" {{ $pengawas->shift == 'Shift 1 s/d 2' ? 'selected' : '' }}>Shift 1 s/d 2</option>
                                                <option value="Shift 2 s/d 1" {{ $pengawas->shift == 'Shift 2 s/d 1' ? 'selected' : '' }}>Shift 2 s/d 1</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Nama Pengawas</label>
                                            <input type="text" name="pengawas_mitra_kerja[{{ $index }}][nama_pengawas]" class="form-control form-control-sm" value="{{ $pengawas->nama_pengawas }}" placeholder="Nama Pengawas" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Layer</label>
                                            <input type="text" name="pengawas_mitra_kerja[{{ $index }}][layer]" class="form-control form-control-sm" value="{{ $pengawas->layer }}" placeholder="Layer (opsional)">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removePengawasRow(this)">
                                        <i class="bx bx-trash"></i> Hapus
                                    </button>
                                </div>
                            @empty
                                <div class="pengawas-row mb-3 p-3 border rounded">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label small">Shift</label>
                                            <select name="pengawas_mitra_kerja[0][shift]" class="form-control form-control-sm" required>
                                                <option value="">Pilih Shift</option>
                                                <option value="Shift 1 s/d 2">Shift 1 s/d 2</option>
                                                <option value="Shift 2 s/d 1">Shift 2 s/d 1</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Nama Pengawas</label>
                                            <input type="text" name="pengawas_mitra_kerja[0][nama_pengawas]" class="form-control form-control-sm" placeholder="Nama Pengawas" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Layer</label>
                                            <input type="text" name="pengawas_mitra_kerja[0][layer]" class="form-control form-control-sm" placeholder="Layer (opsional)">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removePengawasRow(this)">
                                        <i class="bx bx-trash"></i> Hapus
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('sistem-roster.dop.index') }}" class="btn btn-secondary rounded-3">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-3">
                        <i class="bx bx-save"></i> Update DOP
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        let picIndex = {{ $dop->picBerauCoal->count() > 0 ? $dop->picBerauCoal->count() : 1 }};
        let pengawasIndex = {{ $dop->pengawasMitraKerja->count() > 0 ? $dop->pengawasMitraKerja->count() : 1 }};

        function onLokasiSelectChange() {
            const sel = document.getElementById('lokasi_select');
            const opt = sel.options[sel.selectedIndex];
            if (!opt || opt.value === '') return;
            try {
                const data = JSON.parse(opt.value);
                document.getElementById('unit_id').value = data.site || '';
                document.getElementById('lokasi').value = data.lokasi || '';
                document.getElementById('detail_lokasi').value = data.detil_lokasi || '';
            } catch (e) {
                if (opt.dataset.site !== undefined) {
                    document.getElementById('unit_id').value = opt.dataset.site || '';
                    document.getElementById('lokasi').value = opt.dataset.lokasi || '';
                    document.getElementById('detail_lokasi').value = opt.dataset.detil || '';
                }
            }
        }
        document.getElementById('lokasi_select').addEventListener('change', onLokasiSelectChange);

        function addPicRow() {
            const container = document.getElementById('picContainer');
            const newRow = document.createElement('div');
            newRow.className = 'pic-row mb-3 p-3 border rounded';
            newRow.innerHTML = `
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label small">Shift</label>
                        <select name="pic_berau_coal[${picIndex}][shift]" class="form-control form-control-sm" required>
                            <option value="">Pilih Shift</option>
                            <option value="Shift 1 s/d 2">Shift 1 s/d 2</option>
                            <option value="Shift 2 s/d 1">Shift 2 s/d 1</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Nama PIC</label>
                        <input type="text" name="pic_berau_coal[${picIndex}][nama_pic]" class="form-control form-control-sm" placeholder="Nama PIC" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Layer</label>
                        <input type="text" name="pic_berau_coal[${picIndex}][layer]" class="form-control form-control-sm" placeholder="Layer (opsional)">
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removePicRow(this)">
                    <i class="bx bx-trash"></i> Hapus
                </button>
            `;
            container.appendChild(newRow);
            picIndex++;
        }

        function removePicRow(button) {
            const container = document.getElementById('picContainer');
            if (container.children.length > 1) {
                button.closest('.pic-row').remove();
            } else {
                alert('Minimal harus ada satu PIC');
            }
        }

        function addPengawasRow() {
            const container = document.getElementById('pengawasContainer');
            const newRow = document.createElement('div');
            newRow.className = 'pengawas-row mb-3 p-3 border rounded';
            newRow.innerHTML = `
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label small">Shift</label>
                        <select name="pengawas_mitra_kerja[${pengawasIndex}][shift]" class="form-control form-control-sm" required>
                            <option value="">Pilih Shift</option>
                            <option value="Shift 1 s/d 2">Shift 1 s/d 2</option>
                            <option value="Shift 2 s/d 1">Shift 2 s/d 1</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Nama Pengawas</label>
                        <input type="text" name="pengawas_mitra_kerja[${pengawasIndex}][nama_pengawas]" class="form-control form-control-sm" placeholder="Nama Pengawas" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Layer</label>
                        <input type="text" name="pengawas_mitra_kerja[${pengawasIndex}][layer]" class="form-control form-control-sm" placeholder="Layer (opsional)">
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removePengawasRow(this)">
                    <i class="bx bx-trash"></i> Hapus
                </button>
            `;
            container.appendChild(newRow);
            pengawasIndex++;
        }

        function removePengawasRow(button) {
            const container = document.getElementById('pengawasContainer');
            if (container.children.length > 1) {
                button.closest('.pengawas-row').remove();
            } else {
                alert('Minimal harus ada satu Pengawas');
            }
        }

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">`;
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = '';
            }
        }

        function initSelect2() {
            if (typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.select2 !== 'undefined') {
                var $ = window.jQuery;
                $('#cctv_ids').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Pilih CCTV yang mengcover area pekerjaan',
                    allowClear: true,
                    closeOnSelect: false,
                    language: { noResults: function() { return "Tidak ada CCTV ditemukan"; } }
                });
                $('#aktivitas').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Cari atau pilih aktivitas',
                    allowClear: true,
                    language: { noResults: function() { return "Tidak ada aktivitas ditemukan"; } }
                });
                $('#lokasi_select').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Cari atau pilih lokasi (ketik site, lokasi, atau detail)',
                    allowClear: true,
                    language: { noResults: function() { return "Tidak ada lokasi ditemukan"; } }
                });
                $('#lokasi_select').on('select2:select', onLokasiSelectChange);
            } else {
                setTimeout(initSelect2, 100);
            }
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSelect2);
        } else {
            initSelect2();
        }
    </script>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
