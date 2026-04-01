@extends('layouts.masterRoster')

@section('title', 'Tambah DOP')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
@endsection

@section('content')
    <x-page-title title="Tambah DOP" pagetitle="Tambah Daily Operation Plan" />

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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

            @if (session('import_errors') && count(session('import_errors')) > 0)
                <div class="alert alert-warning alert-dismissible fade show rounded-4" role="alert">
                    <strong>Error saat import:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach (array_slice(session('import_errors'), 0, 10) as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        @if (count(session('import_errors')) > 10)
                            <li><em>... dan {{ count(session('import_errors')) - 10 }} error lainnya</em></li>
                        @endif
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('import_header_errors') && count(session('import_header_errors')) > 0)
                <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                    <strong><i class="bx bx-error-circle"></i> Upload ditolak — kolom tidak sesuai template DOP</strong>
                    <p class="mb-2 small">Baris judul (baris 1) harus sama persis dengan file <strong>Download Template</strong>. Periksa penulisan, urutan, dan jangan menambah/menghapus kolom.</p>
                    <ul class="mb-0 small">
                        @foreach (session('import_header_errors') as $hErr)
                            <li>{{ $hErr }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Excel Upload Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card rounded-4 border-primary">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-upload"></i> Upload Excel (Bulk Import)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <form method="POST" action="{{ route('sistem-roster.dop.import') }}" enctype="multipart/form-data" id="excelImportForm">
                                @csrf
                                <label for="excel_file" class="form-label">Pilih File Excel <span class="text-danger">*</span></label>
                                <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx,.xls" required>
                                <small class="text-muted d-block mt-1">
                                    <i class="bx bx-info-circle"></i> Format: .xlsx atau .xls | Max 10MB — judul kolom baris 1 wajib sama dengan <a href="{{ route('sistem-roster.dop.template') }}">template DOP</a> (upload akan ditolak jika tidak sesuai).
                                </small>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <a href="{{ route('sistem-roster.dop.template') }}" class="btn btn-outline-primary">
                                    <i class="bx bx-download"></i> Download Template
                                </a>
                                <button type="submit" form="excelImportForm" class="btn btn-primary">
                                    <i class="bx bx-upload"></i> Upload Excel
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="mb-2"><i class="bx bx-info-circle text-primary"></i> Panduan Upload Excel:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0 small">
                                    <li><strong>Tanggal:</strong> Format YYYY-MM-DD (contoh: 2026-01-15) <span class="text-danger">*</span></li>
                                    <li><strong>Pekerjaan:</strong> Nama pekerjaan yang akan dilakukan <span class="text-danger">*</span></li>
                                    <li><strong>Aktivitas:</strong> Kategori aktivitas (opsional)</li>
                                    <li><strong>Site:</strong> Site lokasi (opsional)</li>
                                    <li><strong>Unit ID:</strong> ID unit yang digunakan <span class="text-danger">*</span></li>
                                    <li><strong>Lokasi:</strong> Lokasi pekerjaan <span class="text-danger">*</span></li>
                                    <li><strong>Latitude:</strong> Koordinat latitude (opsional, format: -90 sampai 90)</li>
                                    <li><strong>Longitude:</strong> Koordinat longitude (opsional, format: -180 sampai 180)</li>
                                    <li><strong>Detail Lokasi:</strong> Detail lokasi (opsional)</li>
                                    <li><strong>Potensi Risiko:</strong> Pisahkan dengan koma jika lebih dari satu</li>
                                    <li><strong>Pengendalian Bahaya:</strong> Pisahkan dengan koma jika lebih dari satu</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0 small">
                                    <li><strong>Catatan:</strong> Catatan tambahan (opsional)</li>
                                    <li><strong>CCTV IDs:</strong> Pisahkan dengan koma (contoh: 1,2,3)</li>
                                    <li><strong>PIC Berau Coal:</strong> Shift, Nama PIC, Layer</li>
                                    <li><strong>Pengawas Mitra Kerja:</strong> Shift, Nama Pengawas, Layer</li>
                                    <li><strong>Shift:</strong> Gunakan "Shift 1 s/d 2" atau "Shift 2 s/d 1"</li>
                                    <li><strong>Untuk PIC/Pengawas multiple:</strong> Buat baris baru dengan data yang sama</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <hr class="my-4">

    <div class="text-center mb-3">
        <h6 class="text-muted">ATAU</h6>
    </div> -->

    <!-- Manual Input Form -->
    <form method="POST" action="{{ route('sistem-roster.dop.store') }}" enctype="multipart/form-data">
        @csrf
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
                                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="pekerjaan" class="form-label">Pekerjaan/Aktivitas <span class="text-danger">*</span></label>
                                <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="aktivitas" class="form-label">Kategori aktivitas</label>
                                <select name="aktivitas" id="aktivitas" class="form-select">
                                    <option value="">-- Pilih Aktivitas --</option>
                                    @isset($masterAktivitas)
                                        @foreach($masterAktivitas as $akt)
                                            <option value="{{ $akt->nama_aktivitas }}" {{ old('aktivitas') == $akt->nama_aktivitas ? 'selected' : '' }}>{{ $akt->nama_aktivitas }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="lokasi_select" class="form-label">Lokasi (dari Hseautomation) <span class="text-muted">— ketik untuk cari, pilih untuk mengisi Site, Lokasi, Detail Lokasi</span></label>
                                <select id="lokasi_select" class="form-select" data-placeholder="Cari atau pilih lokasi...">
                                    <option value="">-- Pilih Lokasi --</option>
                                    @php
                                        $lokasiList = $lokasiList ?? [];
                                        $lokasiSeen = [];
                                    @endphp
                                    @foreach($lokasiList as $loc)
                                        @php
                                            $site = $loc['site'] ?? '';
                                            $lokasi = $loc['lokasi'] ?? '';
                                            $detil = $loc['detil_lokasi'] ?? '';
                                            $key = $site . '|' . $lokasi . '|' . $detil;
                                            if (isset($lokasiSeen[$key])) { continue; }
                                            $lokasiSeen[$key] = true;
                                            $label = trim(implode(' - ', array_filter([$site, $lokasi, $detil])));
                                            $value = json_encode(['site' => $site, 'lokasi' => $lokasi, 'detil_lokasi' => $detil]);
                                        @endphp
                                        <option value="{{ htmlspecialchars($value) }}" data-site="{{ e($site) }}" data-lokasi="{{ e($lokasi) }}" data-detil="{{ e($detil) }}">{{ $label ?: '-' }}</option>
                                    @endforeach
                                </select>
                                @if(empty($lokasiList))
                                    <small class="text-warning d-block mt-1"><i class="bx bx-info-circle"></i> </small>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="unit_id" class="form-label">Site <span class="text-danger">*</span></label>
                                <input type="text" name="unit_id" id="unit_id" class="form-control" value="{{ old('unit_id') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" name="lokasi" id="lokasi" class="form-control" value="{{ old('lokasi') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="perusahaan" class="form-label">Perusahaan</label>
                                <input type="text" name="perusahaan" id="perusahaan" class="form-control" value="{{ old('perusahaan') }}" placeholder="Nama perusahaan">
                            </div>
                            <!-- <div class="col-md-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" step="any" name="latitude" id="latitude" class="form-control" value="{{ old('latitude') }}" placeholder="-2.186253" min="-90" max="90">
                                <small class="text-muted">Format: -90 sampai 90</small>
                            </div>
                            <div class="col-md-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" step="any" name="longitude" id="longitude" class="form-control" value="{{ old('longitude') }}" placeholder="117.4539035" min="-180" max="180">
                                <small class="text-muted">Format: -180 sampai 180</small>
                            </div> -->
                            <div class="col-6">
                                <label for="detail_lokasi" class="form-label">Detail Lokasi</label>
                                <input type="text" name="detail_lokasi" id="detail_lokasi" class="form-control" rows="2" placeholder="Terisi otomatis jika memilih dari dropdown Lokasi">{{ old('detail_lokasi') }}</textarea>
                            </div>
                            <!-- <div class="col-12">
                                <label for="foto_pekerjaan" class="form-label">Upload Foto Pekerjaan</label>
                                <input type="file" name="foto_pekerjaan" id="foto_pekerjaan" class="form-control" accept="image/*" onchange="previewImage(this)">
                                <small class="text-muted">Format: JPG, PNG | Max 5MB</small>
                                <div id="imagePreview" class="mt-2"></div>
                            </div> -->
                            <div class="col-12">
                                <label for="cctv_ids" class="form-label">CCTV yang Mengcover</label>
                                <select name="cctv_ids[]" id="cctv_ids" class="form-select" multiple="multiple" data-placeholder="Pilih CCTV yang mengcover area pekerjaan">
                                    @if(isset($cctvs) && $cctvs->count() > 0)
                                        @foreach($cctvs as $cctv)
                                            <option value="{{ $cctv->id }}" {{ in_array($cctv->id, old('cctv_ids', [])) ? 'selected' : '' }}>
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
                            <!-- <div class="col-12">
                                <label for="potensi_resiko" class="form-label">Potensi Risiko</label>
                                <textarea name="potensi_resiko" id="potensi_resiko" class="form-control" rows="3" placeholder="Masukkan potensi risiko (satu per baris atau dipisahkan dengan koma)">{{ old('potensi_resiko') }}</textarea>
                                <small class="text-muted">Contoh: Tenggelam, Terbalik, Terguling</small>
                            </div> -->
                            <!-- <div class="col-12">
                                <label for="pengendalian_bahaya" class="form-label">Pengendalian Bahaya</label>
                                <textarea name="pengendalian_bahaya" id="pengendalian_bahaya" class="form-control" rows="3" placeholder="Masukkan pengendalian bahaya (satu per baris atau dipisahkan dengan koma)">{{ old('pengendalian_bahaya') }}</textarea>
                                <small class="text-muted">Contoh: Assessment, JSA, SOP, Pengawas KPO</small>
                            </div> -->
                            <!-- <div class="col-12">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea name="catatan" id="catatan" class="form-control" rows="3">{{ old('catatan') }}</textarea>
                            </div> -->
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('sistem-roster.dop.index') }}" class="btn btn-secondary rounded-3">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                    <button type="reset" class="btn btn-outline-secondary rounded-3">Reset</button>
                    <button type="submit" class="btn btn-primary rounded-3">
                        <i class="bx bx-save"></i> Simpan DOP
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        let picIndex = 1;
        let pengawasIndex = 1;

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @if(session('dop_import_template_invalid'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var errs = @json(session('import_header_errors', []));
            var listHtml = '';
            if (Array.isArray(errs) && errs.length) {
                listHtml = '<ul class="text-start mb-0 small mt-2">' + errs.map(function(e) {
                    return '<li>' + String(e).replace(/</g, '&lt;') + '</li>';
                }).join('') + '</ul>';
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload ditolak',
                    html: '<p class="mb-0 text-start">Kolom Excel tidak sesuai template DOP Sistem Roster. Gunakan <strong>Download Template</strong> dan jangan mengubah baris judul (baris 1).</p>' + listHtml,
                    confirmButtonText: 'Mengerti',
                    width: '34rem'
                });
            }
        });
    </script>
    @endif
@endsection
