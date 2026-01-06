@extends('layouts.masterMotionHazardAdmin')

@section('title', 'Checklist P2H CCTV - Beraucoal')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .checklist-form {
        background: white;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .form-section {
        margin-bottom: 32px;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .checklist-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .checklist-table th,
    .checklist-table td {
        padding: 12px;
        text-align: left;
        border: 1px solid #e5e7eb;
    }
    
    .checklist-table th {
        background-color: #f9fafb;
        font-weight: 600;
        color: #374151;
    }
    
    .checklist-table td {
        background-color: white;
    }
    
    .form-check-input:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }
    
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }
    
    .cctv-item {
        padding: 8px;
        margin-bottom: 8px;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        background-color: #f9fafb;
    }
</style>
@endsection

@section('content')
<x-page-title title="Checklist P2H CCTV" pagetitle="Formulir Checklist P2H CCTV Mining Eyes, Mining Eyes Analytics, Plant & Support" />

<div class="row">
    <div class="col-12">
        <div class="checklist-form">
            <form id="p2hForm" method="POST" action="{{ route('hazard-detection.p2h.store') }}">
                @csrf
                
                <!-- Header Information -->
                <div class="form-section">
                    <h3 class="section-title">Informasi Umum</h3>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="control_room" class="form-label fw-bold">Control Room <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="control_room" name="control_room" value="{{ $controlRoom }}" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_pemeriksaan" class="form-label fw-bold">Tanggal Pemeriksaan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_pemeriksaan" name="tanggal_pemeriksaan" value="{{ $today->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="shift" class="form-label fw-bold">Shift <span class="text-danger">*</span></label>
                            <select class="form-control" id="shift" name="shift" required>
                                <option value="1" {{ $currentShift == '1' ? 'selected' : '' }}>Shift 1 (06:00 - 14:00)</option>
                                <option value="2" {{ $currentShift == '2' ? 'selected' : '' }}>Shift 2 (14:00 - 22:00)</option>
                                <option value="3" {{ $currentShift == '3' ? 'selected' : '' }}>Shift 3 (22:00 - 06:00)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Jenis CCTV <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="jenis_cctv[]" value="Mining Eyes" id="jenis_mining_eyes">
                                    <label class="form-check-label" for="jenis_mining_eyes">Mining Eyes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="jenis_cctv[]" value="Mining Eyes Analytics" id="jenis_mining_eyes_analytics">
                                    <label class="form-check-label" for="jenis_mining_eyes_analytics">Mining Eyes Analytics</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="jenis_cctv[]" value="Plant" id="jenis_plant">
                                    <label class="form-check-label" for="jenis_plant">Plant</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="jenis_cctv[]" value="Support" id="jenis_support">
                                    <label class="form-check-label" for="jenis_support">Support</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Section A: Pemeriksaan Fisik -->
                <div class="form-section">
                    <h3 class="section-title">A. Pemeriksaan Fisik</h3>
                    <div class="table-responsive">
                        <table class="checklist-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 40%;">Item Pengecekan</th>
                                    <th style="width: 10%;">Jumlah</th>
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
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item }}</td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" 
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
                                                <label class="form-check-label" for="fisik_kondisi_baik_{{ $index }}">Baik</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" 
                                                       name="pemeriksaan_fisik[{{ $index }}][kondisi]" 
                                                       value="rusak" 
                                                       id="fisik_kondisi_rusak_{{ $index }}"
                                                       {{ $existingP2h && isset($existingP2h->pemeriksaan_fisik[$index]['kondisi']) && $existingP2h->pemeriksaan_fisik[$index]['kondisi'] == 'rusak' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="fisik_kondisi_rusak_{{ $index }}">Rusak</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Section B: Pemeriksaan Fungsi -->
                <div class="form-section">
                    <h3 class="section-title">B. Pemeriksaan Fungsi</h3>
                    <div class="table-responsive">
                        <table class="checklist-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
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
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item }}</td>
                                    <td>
                                        <div class="d-flex gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" 
                                                       name="pemeriksaan_fungsi[{{ $index }}][status]" 
                                                       value="baik" 
                                                       id="fungsi_baik_{{ $index }}"
                                                       {{ $existingP2h && isset($existingP2h->pemeriksaan_fungsi[$index]['status']) && $existingP2h->pemeriksaan_fungsi[$index]['status'] == 'baik' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="fungsi_baik_{{ $index }}">Baik</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" 
                                                       name="pemeriksaan_fungsi[{{ $index }}][status]" 
                                                       value="rusak" 
                                                       id="fungsi_rusak_{{ $index }}"
                                                       {{ $existingP2h && isset($existingP2h->pemeriksaan_fungsi[$index]['status']) && $existingP2h->pemeriksaan_fungsi[$index]['status'] == 'rusak' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="fungsi_rusak_{{ $index }}">Rusak</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" 
                                                       name="pemeriksaan_fungsi[{{ $index }}][status]" 
                                                       value="tidak_ada" 
                                                       id="fungsi_tidak_ada_{{ $index }}"
                                                       {{ $existingP2h && isset($existingP2h->pemeriksaan_fungsi[$index]['status']) && $existingP2h->pemeriksaan_fungsi[$index]['status'] == 'tidak_ada' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="fungsi_tidak_ada_{{ $index }}">Tidak Ada</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- CCTV List -->
                <div class="form-section">
                    <h3 class="section-title">Daftar CCTV di Control Room</h3>
                    <div class="row">
                        @foreach($cctvList as $cctv)
                        <div class="col-md-6 mb-2">
                            <div class="cctv-item">
                                <strong>{{ $cctv->nama_cctv ?? 'CCTV ' . $cctv->id }}</strong>
                                @if($cctv->no_cctv)
                                    <br><small class="text-muted">No. CCTV: {{ $cctv->no_cctv }}</small>
                                @endif
                                @if($cctv->lokasi_pemasangan)
                                    <br><small class="text-muted">Lokasi: {{ $cctv->lokasi_pemasangan }}</small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Notes -->
                <div class="form-section">
                    <div class="alert alert-info">
                        <strong>Note:</strong> Pemeriksaan fisik dan fungsi pada unit Server, UPS dan AC dilaksanakan jika unit tersebut berada di ruangan control room.
                    </div>
                    <div class="mb-3">
                        <label for="catatan_lain" class="form-label fw-bold">Catatan lain-lain (jika ada):</label>
                        <textarea class="form-control" id="catatan_lain" name="catatan_lain" rows="3" placeholder="Masukkan catatan lain jika ada...">{{ $existingP2h ? $existingP2h->catatan_lain : '' }}</textarea>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('hazard-detection.map') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Checklist P2H</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('p2hForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Convert form data to proper format
        const data = {
            control_room: formData.get('control_room'),
            tanggal_pemeriksaan: formData.get('tanggal_pemeriksaan'),
            shift: formData.get('shift'),
            jenis_cctv: formData.getAll('jenis_cctv[]'),
            pemeriksaan_fisik: {},
            pemeriksaan_fungsi: {},
            catatan_lain: formData.get('catatan_lain')
        };
        
        // Process pemeriksaan_fisik
        for (let i = 0; i < 9; i++) {
            data.pemeriksaan_fisik[i] = {
                jumlah: formData.get(`pemeriksaan_fisik[${i}][jumlah]`) || 0,
                ketersediaan: formData.get(`pemeriksaan_fisik[${i}][ketersediaan]`) || '',
                kondisi: formData.get(`pemeriksaan_fisik[${i}][kondisi]`) || ''
            };
        }
        
        // Process pemeriksaan_fungsi
        for (let i = 0; i < 8; i++) {
            data.pemeriksaan_fungsi[i] = {
                status: formData.get(`pemeriksaan_fungsi[${i}][status]`) || ''
            };
        }
        
        // Submit via AJAX
        fetch('{{ route("hazard-detection.p2h.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: result.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '{{ route("hazard-detection.map") }}';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: result.message || 'Terjadi kesalahan saat menyimpan data.'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menyimpan data.'
            });
        });
    });
</script>
@endsection

