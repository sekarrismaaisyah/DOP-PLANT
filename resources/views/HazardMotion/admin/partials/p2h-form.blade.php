<form id="p2hForm" method="POST" action="{{ route('hazard-detection.p2h.store') }}">
    @csrf
    
    <!-- Header Information Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="material-icons-outlined me-2 text-primary">info</i>
                Informasi Umum
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="control_room" class="form-label fw-semibold">Control Room <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="control_room" name="control_room" value="{{ $controlRoom }}" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="col-md-4">
                    <label for="tanggal_pemeriksaan" class="form-label fw-semibold">Tanggal Pemeriksaan <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="tanggal_pemeriksaan" name="tanggal_pemeriksaan" value="{{ $today->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label for="shift" class="form-label fw-semibold">Shift <span class="text-danger">*</span></label>
                    <select class="form-select" id="shift" name="shift" required>
                        <option value="1" {{ $currentShift == '1' ? 'selected' : '' }}>Shift 1 (06:00 - 14:00)</option>
                        <option value="2" {{ $currentShift == '2' ? 'selected' : '' }}>Shift 2 (14:00 - 22:00)</option>
                        <option value="3" {{ $currentShift == '3' ? 'selected' : '' }}>Shift 3 (22:00 - 06:00)</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Jenis CCTV <span class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="jenis_cctv[]" value="Mining Eyes" id="jenis_mining_eyes">
                            <label class="form-check-label" for="jenis_mining_eyes">Mining Eyes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="jenis_cctv[]" value="Mining Eyes Analytics" id="jenis_mining_eyes_analytics">
                            <label class="form-check-label" for="jenis_mining_eyes_analytics">Mining Eyes Analytics</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="jenis_cctv[]" value="Plant" id="jenis_plant">
                            <label class="form-check-label" for="jenis_plant">Plant</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="jenis_cctv[]" value="Support" id="jenis_support">
                            <label class="form-check-label" for="jenis_support">Support</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section C: Kondisi Per CCTV - PRIORITAS -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="material-icons-outlined me-2 text-primary">videocam</i>
                C. Kondisi Per CCTV
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <i class="material-icons-outlined me-2">info</i>
                <strong>Penting:</strong> Tentukan kondisi setiap CCTV apakah baik, rusak, atau tidak ada.
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;" class="text-center">No</th>
                            <th style="width: 30%;">Nama CCTV</th>
                            <th style="width: 20%;">No. CCTV</th>
                            <th style="width: 25%;">Lokasi</th>
                            <th style="width: 20%;" class="text-center">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $existingDetailCctv = $existingP2h && $existingP2h->detail_cctv ? collect($existingP2h->detail_cctv)->keyBy('cctv_id') : collect();
                        @endphp
                        @foreach($cctvList as $index => $cctv)
                        @php
                            $cctvDetail = $existingDetailCctv->get($cctv->id, ['status' => '', 'catatan' => '']);
                        @endphp
                        <tr>
                            <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $cctv->nama_cctv ?? 'CCTV ' . $cctv->id }}</strong>
                            </td>
                            <td>
                                <span class="text-muted">{{ $cctv->no_cctv ?? '-' }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $cctv->lokasi_pemasangan ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="detail_cctv[{{ $cctv->id }}][status]" 
                                               value="baik" 
                                               id="cctv_baik_{{ $cctv->id }}"
                                               {{ $cctvDetail['status'] == 'baik' ? 'checked' : '' }}>
                                        <label class="form-check-label text-success fw-semibold" for="cctv_baik_{{ $cctv->id }}">Baik</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="detail_cctv[{{ $cctv->id }}][status]" 
                                               value="rusak" 
                                               id="cctv_rusak_{{ $cctv->id }}"
                                               {{ $cctvDetail['status'] == 'rusak' ? 'checked' : '' }}>
                                        <label class="form-check-label text-danger fw-semibold" for="cctv_rusak_{{ $cctv->id }}">Rusak</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="detail_cctv[{{ $cctv->id }}][status]" 
                                               value="tidak_ada" 
                                               id="cctv_tidak_ada_{{ $cctv->id }}"
                                               {{ $cctvDetail['status'] == 'tidak_ada' ? 'checked' : '' }}>
                                        <label class="form-check-label text-muted fw-semibold" for="cctv_tidak_ada_{{ $cctv->id }}">Tidak Ada</label>
                                    </div>
                                </div>
                                <input type="hidden" name="detail_cctv[{{ $cctv->id }}][cctv_id]" value="{{ $cctv->id }}">
                                <input type="hidden" name="detail_cctv[{{ $cctv->id }}][nama_cctv]" value="{{ $cctv->nama_cctv ?? 'CCTV ' . $cctv->id }}">
                                <div class="mt-2">
                                    <input type="text" class="form-control form-control-sm" 
                                           name="detail_cctv[{{ $cctv->id }}][catatan]" 
                                           value="{{ $cctvDetail['catatan'] ?? '' }}"
                                           placeholder="Catatan (opsional)">
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Section A: Pemeriksaan Fisik -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="material-icons-outlined me-2 text-primary">build</i>
                A. Pemeriksaan Fisik
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width: 5%;" class="text-center">No</th>
                            <th style="width: 40%;">Item Pengecekan</th>
                            <th style="width: 10%;" class="text-center">Jumlah</th>
                            <th style="width: 22.5%;">Ketersediaan</th>
                            <th style="width: 22.5%;">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $fisikItems = [
                                'Kamera CCTV',
                                'Solar Panel/Baterai (sesuai sumber energi yang digunakan)',
                                'Unit PC',
                                'Unit NVR (Network Video Record)',
                                'Additional Monitor',
                                'Kondisi Penerangan (khusus shift 2)',
                                'Unit UPS (Uninterruptible Power Supply) server',
                                'Unit Server',
                                'Air conditioner (AC)'
                            ];
                        @endphp
                        @foreach($fisikItems as $index => $item)
                        <tr>
                            <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                            <td>{{ $item }}</td>
                            <td>
                                <input type="number" class="form-control form-control-sm text-center" 
                                       name="pemeriksaan_fisik[{{ $index }}][jumlah]" 
                                       value="{{ $existingP2h && isset($existingP2h->pemeriksaan_fisik[$index]['jumlah']) ? $existingP2h->pemeriksaan_fisik[$index]['jumlah'] : '' }}" 
                                       min="0" style="width: 80px;">
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="pemeriksaan_fisik[{{ $index }}][ketersediaan]" 
                                               value="ada" 
                                               id="fisik_ketersediaan_ada_{{ $index }}"
                                               {{ $existingP2h && isset($existingP2h->pemeriksaan_fisik[$index]['ketersediaan']) && $existingP2h->pemeriksaan_fisik[$index]['ketersediaan'] == 'ada' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="fisik_ketersediaan_ada_{{ $index }}">Ada</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="pemeriksaan_fisik[{{ $index }}][ketersediaan]" 
                                               value="tidak_ada" 
                                               id="fisik_ketersediaan_tidak_ada_{{ $index }}"
                                               {{ $existingP2h && isset($existingP2h->pemeriksaan_fisik[$index]['ketersediaan']) && $existingP2h->pemeriksaan_fisik[$index]['ketersediaan'] == 'tidak_ada' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="fisik_ketersediaan_tidak_ada_{{ $index }}">Tidak Ada</label>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="pemeriksaan_fisik[{{ $index }}][kondisi]" 
                                               value="baik" 
                                               id="fisik_kondisi_baik_{{ $index }}"
                                               {{ $existingP2h && isset($existingP2h->pemeriksaan_fisik[$index]['kondisi']) && $existingP2h->pemeriksaan_fisik[$index]['kondisi'] == 'baik' ? 'checked' : '' }}>
                                        <label class="form-check-label text-success" for="fisik_kondisi_baik_{{ $index }}">Baik</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="pemeriksaan_fisik[{{ $index }}][kondisi]" 
                                               value="rusak" 
                                               id="fisik_kondisi_rusak_{{ $index }}"
                                               {{ $existingP2h && isset($existingP2h->pemeriksaan_fisik[$index]['kondisi']) && $existingP2h->pemeriksaan_fisik[$index]['kondisi'] == 'rusak' ? 'checked' : '' }}>
                                        <label class="form-check-label text-danger" for="fisik_kondisi_rusak_{{ $index }}">Rusak</label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Section B: Pemeriksaan Fungsi -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="material-icons-outlined me-2 text-primary">settings</i>
                B. Pemeriksaan Fungsi
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th style="width: 5%;" class="text-center">No</th>
                            <th style="width: 60%;">Item Pengecekan</th>
                            <th style="width: 35%;">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $fungsiItems = [
                                'Unit PC dapat dinyalakan dan berfungsi',
                                'Unit NVR dapat dinyalakan dan berfungsi',
                                'Gambar dari kamera dapat ditampilkan dengan jelas dalam PC maupun additional monitor',
                                'PC dapat tersambung ke internet (khusus Mining Eyes)',
                                'Gambar di website Mining Eyes Analytics dapat ditampilkan dengan jelas dalam PC',
                                'Unit UPS server dapat dinyalakan dan berfungsi',
                                'Unit Server dapat dinyalakan dan berfungsi',
                                'Unit AC dapat dinyalakan dan berfungsi'
                            ];
                        @endphp
                        @foreach($fungsiItems as $index => $item)
                        <tr>
                            <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                            <td>{{ $item }}</td>
                            <td>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="pemeriksaan_fungsi[{{ $index }}][status]" 
                                               value="baik" 
                                               id="fungsi_baik_{{ $index }}"
                                               {{ $existingP2h && isset($existingP2h->pemeriksaan_fungsi[$index]['status']) && $existingP2h->pemeriksaan_fungsi[$index]['status'] == 'baik' ? 'checked' : '' }}>
                                        <label class="form-check-label text-success" for="fungsi_baik_{{ $index }}">Baik</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="pemeriksaan_fungsi[{{ $index }}][status]" 
                                               value="rusak" 
                                               id="fungsi_rusak_{{ $index }}"
                                               {{ $existingP2h && isset($existingP2h->pemeriksaan_fungsi[$index]['status']) && $existingP2h->pemeriksaan_fungsi[$index]['status'] == 'rusak' ? 'checked' : '' }}>
                                        <label class="form-check-label text-danger" for="fungsi_rusak_{{ $index }}">Rusak</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="pemeriksaan_fungsi[{{ $index }}][status]" 
                                               value="tidak_ada" 
                                               id="fungsi_tidak_ada_{{ $index }}"
                                               {{ $existingP2h && isset($existingP2h->pemeriksaan_fungsi[$index]['status']) && $existingP2h->pemeriksaan_fungsi[$index]['status'] == 'tidak_ada' ? 'checked' : '' }}>
                                        <label class="form-check-label text-muted" for="fungsi_tidak_ada_{{ $index }}">Tidak Ada</label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Notes Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="material-icons-outlined me-2 text-primary">note</i>
                Catatan
            </h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <i class="material-icons-outlined me-2">info</i>
                <strong>Note:</strong> Pemeriksaan fisik dan fungsi pada unit Server, UPS dan AC dilaksanakan jika unit tersebut berada di ruangan control room.
            </div>
            <div class="mb-0">
                <label for="catatan_lain" class="form-label fw-semibold">Catatan lain-lain (jika ada):</label>
                <textarea class="form-control" id="catatan_lain" name="catatan_lain" rows="3" placeholder="Masukkan catatan lain jika ada...">{{ $existingP2h ? $existingP2h->catatan_lain : '' }}</textarea>
            </div>
        </div>
    </div>
</form>

<style>
    .card {
        border-radius: 8px;
        transition: box-shadow 0.2s ease;
    }
    
    .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }
    
    .card-header {
        border-radius: 8px 8px 0 0 !important;
        padding: 16px 20px;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .table {
        font-size: 13px;
    }
    
    .table th {
        font-size: 12px;
        font-weight: 600;
        background-color: #f8f9fa;
    }
    
    .form-check-input:checked {
        background-color: #2196F3;
        border-color: #2196F3;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #2196F3;
        box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
    }
</style>
