@extends('layouts.master')

@section('title', 'Import CCTV Coverage')
@section('css')
    <link href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection 

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush 
@section('content')
<x-page-title title="CCTV Coverage" pagetitle="Data CCTV Coverage" />

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <h5 class="mb-0 fw-bold">Data CCTV Coverage</h5>
                        <p class="mb-0 text-muted">Daftar Data CCTV Coverage yang sudah diimport</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('cctv-data.coverage.export') }}" class="btn btn-info">
                            <i class="material-icons-outlined">download</i> Download Excel
                        </a>
                        <button type="button" class="btn btn-primary" id="btnImportCoverage">
                            <i class="material-icons-outlined">upload</i> Import Coverage
                        </button>
                        <div class="dropdown">
                            <a href="javascript:;" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="material-icons-outlined">more_vert</i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('cctv-data.coverage.export') }}"><i class="material-icons-outlined me-2">download</i> Download Excel</a></li>
                                <li><a class="dropdown-item" href="{{ route('cctv-data.index') }}"><i class="material-icons-outlined me-2">arrow_back</i> Kembali</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

<!-- Data Table Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <!-- <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <h5 class="mb-0 fw-bold">Data CCTV Coverage</h5>
                        <p class="mb-0 text-muted">Daftar Data CCTV Coverage yang sudah diimport</p>
                    </div>
                </div> -->

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="coverageDataTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No. CCTV</th>
                                <th>Coverage Lokasi</th>
                                <th>Coverage Detail Lokasi</th>
                                <th>Kategori Aktivitas</th>
                                <th>Kategori Area</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
                    </div>
                </div>

<!-- Modal Import Coverage -->
<div class="modal fade" id="importCoverageModal" tabindex="-1" aria-labelledby="importCoverageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="importCoverageModalLabel">
                    <i class="material-icons-outlined me-2">upload</i> Import CCTV Coverage dari Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('import_errors') && count(session('import_errors')) > 0)
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Peringatan:</strong> Beberapa data gagal diimpor.
                        <ul class="mb-0 mt-2">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-8">
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Langkah-langkah:</h6>
                                <ol class="mb-0">
                                    <li class="mb-2">Download template Excel dengan klik tombol <strong>"Download Template"</strong> di bawah</li>
                                    <li class="mb-2">Buka file Excel yang sudah didownload</li>
                                    <li class="mb-2">Isi data sesuai dengan format yang ada di template</li>
                                    <li class="mb-2">Pastikan Site, Perusahaan CCTV, dan Nomer CCTV sesuai dengan data di database</li>
                                    <li class="mb-2">Simpan file Excel</li>
                                    <li class="mb-2">Upload file Excel yang sudah diisi melalui form di bawah</li>
                                </ol>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mb-3">
                            <a href="{{ route('cctv-data.download-template-coverage') }}" class="btn btn-success" id="btnDownloadTemplate">
                                <i class="material-icons-outlined">download</i> Download Template Excel
                            </a>
                        </div>

                        <form action="{{ route('cctv-data.import-coverage') }}" method="POST" enctype="multipart/form-data" id="importCoverageForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="file" class="form-label fw-bold">Pilih File Excel/CSV yang Sudah Diisi <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                <small class="form-text text-muted">
                                    Format yang didukung: .xlsx, .xls, .csv (Maksimal 10MB)
                                </small>
                            </div>

                            <div class="alert alert-info">
                                <h6 class="fw-bold mb-2">Format File Excel - Dua Mode Tersedia:</h6>
                                
                                <div class="mb-3">
                                    <h6 class="fw-bold text-primary mb-2">📝 Mode UPDATE (Update Data yang Sudah Ada):</h6>
                                    <p class="mb-2 small">Untuk update kategori aktivitas dan kategori area pada data yang sudah ada, cukup gunakan 4 kolom berikut:</p>
                                    <ul class="mb-0 small">
                                        <li><strong>Coverage Lokasi</strong> - Lokasi coverage (contoh: Dermaga, Workshop FAD) - <span class="text-danger">*Key untuk matching</span></li>
                                        <li><strong>Coverage Detail Lokasi</strong> - Detail lokasi coverage (contoh: Dermaga FAD Prapatan, Base Workshop) - <span class="text-danger">*Key untuk matching</span></li>
                                        <li><strong>Kategori Aktivitas</strong> - Kategori aktivitas (contoh: Aktivitas Non Kritis, Aktivitas Kritis, Aktivitas Highrisk)</li>
                                        <li><strong>Kategori Area</strong> - Kategori area (contoh: Area Non Kritis, Area Kritis, Area Highrisk)</li>
                                    </ul>
                                    <p class="mb-0 mt-2 small text-muted"><strong>Catatan:</strong> Sistem akan mencari data yang cocok berdasarkan Coverage Lokasi dan Coverage Detail Lokasi, lalu mengupdate Kategori Aktivitas dan Kategori Area.</p>
                                </div>

                                <div class="mb-3">
                                    <h6 class="fw-bold text-success mb-2">➕ Mode CREATE (Buat Data Baru):</h6>
                                    <p class="mb-2 small">Untuk membuat data coverage baru, gunakan semua kolom berikut:</p>
                                <ul class="mb-0 small">
                                        <li><strong>Site</strong> - Site CCTV (contoh: HO, LMO, BMO 2)</li>
                                        <li><strong>Perusahaan CCTV</strong> - Nama perusahaan (contoh: PT Fajar Anugerah Dinamika, PT Bukit Makmur Mandiri Utama)</li>
                                        <li><strong>Nomer CCTV</strong> - Nomor CCTV (contoh: CCTV 01 FAD LMO, LMO-FAD-0001, 1172178038)</li>
                                    <li><strong>Coverage Lokasi</strong> - Lokasi coverage (contoh: Dermaga, Workshop FAD)</li>
                                    <li><strong>Coverage Detail Lokasi</strong> - Detail lokasi coverage (contoh: Dermaga FAD Prapatan, Base Workshop)</li>
                                        <li><strong>Kategori Aktivitas</strong> - Kategori aktivitas (opsional)</li>
                                        <li><strong>Kategori Area</strong> - Kategori area (opsional)</li>
                                </ul>
                                    <p class="mb-0 mt-2 small text-muted"><strong>Catatan:</strong> Data CCTV harus sudah ada di database terlebih dahulu. Sistem akan mencocokkan berdasarkan Site, Perusahaan CCTV, dan Nomer CCTV.</p>
                                </div>
                            </div>

                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="material-icons-outlined">upload</i> Import Data
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="material-icons-outlined">close</i> Batal
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <div class="card rounded-4 bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <h6 class="fw-bold mb-0">Petunjuk Import</h6>
                                </div>
                                <ol class="small mb-0">
                                    <li>Pastikan file Excel memiliki header di baris pertama</li>
                                    <li>Kolom wajib: Site, Perusahaan CCTV, Nomer CCTV</li>
                                    <li>Data dimulai dari baris kedua</li>
                                    <li>Baris kosong akan diabaikan</li>
                                    <li>Data CCTV harus sudah ada di database</li>
                                    <li>Sistem akan mencocokkan nomor CCTV secara fleksibel</li>
                                    <li>Data coverage duplikat akan di-skip</li>
                                </ol>
                                <div class="mt-3 p-2 bg-warning bg-opacity-10 rounded">
                                    <small class="text-warning">
                                        <strong>Tips:</strong> Jika CCTV tidak ditemukan, pastikan Site, Perusahaan, dan format Nomer CCTV sesuai dengan data di database.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Coverage -->
<div class="modal fade" id="editCoverageModal" tabindex="-1" aria-labelledby="editCoverageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title fw-bold" id="editCoverageModalLabel">
                    <i class="material-icons-outlined me-2">edit</i> Edit CCTV Coverage
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCoverageForm">
                <div class="modal-body">
                    <input type="hidden" id="editCoverageId" name="id">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">
                                <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">videocam</i>
                                No. CCTV
                            </label>
                            <div class="form-control bg-light" id="editNoCctv" style="border: 1px solid #dee2e6; min-height: 38px; display: flex; align-items: center;">
                                -
                            </div>
                            <small class="form-text text-muted">Informasi CCTV (tidak dapat diubah)</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="editCoverageLokasi" class="form-label fw-bold">
                                <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">location_on</i>
                                Coverage Lokasi <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editCoverageLokasi" name="coverage_lokasi" 
                                   placeholder="Contoh: Dermaga, Workshop FAD" required>
                            <div class="invalid-feedback">
                                Coverage Lokasi harus diisi
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="editCoverageDetailLokasi" class="form-label fw-bold">
                                <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">place</i>
                                Coverage Detail Lokasi <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="editCoverageDetailLokasi" name="coverage_detail_lokasi" 
                                   placeholder="Contoh: Dermaga FAD Prapatan, Base Workshop" required>
                            <div class="invalid-feedback">
                                Coverage Detail Lokasi harus diisi
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editKategoriAktivitas" class="form-label fw-bold">
                                <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">category</i>
                                Kategori Aktivitas
                            </label>
                            <div class="input-group">
                                <select class="form-select" id="editKategoriAktivitas" name="kategori_aktivitas">
                                    <option value="">-- Pilih atau Ketik Manual --</option>
                                    <option value="Aktivitas Non Kritis">Aktivitas Non Kritis</option>
                                    <option value="Aktivitas Kritis">Aktivitas Kritis</option>
                                    <option value="Aktivitas Highrisk">Aktivitas Highrisk</option>
                                    <option value="Operasional">Operasional</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Produksi">Produksi</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" id="btnEditKategoriAktivitasManual" title="Input Manual">
                                    <i class="material-icons-outlined">edit</i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2 d-none" id="editKategoriAktivitasManual" placeholder="Ketik kategori aktivitas manual">
                            <small class="form-text text-muted">Pilih dari dropdown atau ketik manual</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editKategoriArea" class="form-label fw-bold">
                                <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">map</i>
                                Kategori Area
                            </label>
                            <div class="input-group">
                                <select class="form-select" id="editKategoriArea" name="kategori_area">
                                    <option value="">-- Pilih atau Ketik Manual --</option>
                                    <option value="Area Non Kritis">Area Non Kritis</option>
                                    <option value="Area Kritis">Area Kritis</option>
                                    <option value="Area Highrisk">Area Highrisk</option>
                                    <option value="Area Produksi">Area Produksi</option>
                                    <option value="Area Workshop">Area Workshop</option>
                                    <option value="Area Parkir">Area Parkir</option>
                                    <option value="Area Fabrikasi">Area Fabrikasi</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" id="btnEditKategoriAreaManual" title="Input Manual">
                                    <i class="material-icons-outlined">edit</i>
                                </button>
                            </div>
                            <input type="text" class="form-control mt-2 d-none" id="editKategoriAreaManual" placeholder="Ketik kategori area manual">
                            <small class="form-text text-muted">Pilih dari dropdown atau ketik manual</small>
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="material-icons-outlined me-2" style="font-size: 18px; vertical-align: middle;">info</i>
                        <strong>Catatan:</strong> Field yang bertanda <span class="text-danger">*</span> wajib diisi.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="material-icons-outlined me-1">close</i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons-outlined me-1">save</i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Hindari reinitialise DataTable: destroy dulu jika sudah ada
        if ($.fn.DataTable.isDataTable('#coverageDataTable')) {
            $('#coverageDataTable').DataTable().destroy();
        }
        var table = $('#coverageDataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('cctv-data.coverage.data') }}",
                type: "GET"
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'no_cctv', name: 'no_cctv' },
                { data: 'coverage_lokasi', name: 'coverage_lokasi' },
                { data: 'coverage_detail_lokasi', name: 'coverage_detail_lokasi' },
                { data: 'kategori_aktivitas', name: 'kategori_aktivitas' },
                { data: 'kategori_area', name: 'kategori_area' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            responsive: true,
            scrollX: true,
            language: {
                processing: "Memproses data...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                },
                emptyTable: "Tidak ada data yang tersedia",
                zeroRecords: "Tidak ada data yang cocok dengan pencarian"
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 2 }
            ]
        });

        // Handle download template button click
        $('#btnDownloadTemplate').on('click', function(e) {
            // Show loading
            Swal.fire({
                title: 'Menyiapkan template...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });

        // Handle button click to open modal - menggunakan cara sederhana
        $('#btnImportCoverage').on('click', function(e) {
            e.preventDefault();
            var modalElement = $('#importCoverageModal');
            if (modalElement.length) {
                // Coba menggunakan Bootstrap 5 API
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try {
                        var modal = bootstrap.Modal.getOrCreateInstance(modalElement[0]);
                        modal.show();
                    } catch (e) {
                        console.error('Error showing modal with Bootstrap 5:', e);
                        // Fallback ke jQuery
                        modalElement.modal('show');
                    }
                } else {
                    // Fallback ke jQuery jika Bootstrap belum tersedia
                    modalElement.modal('show');
                }
            } else {
                console.error('Modal element not found');
            }
        });

        // Handle form submit - reload table after successful import
        $('#importCoverageForm').on('submit', function(e) {
            // Form akan submit normal, setelah redirect akan reload table
        });

        // Show success/error message dari session
        @if(session('success'))
        // Close modal if open
        setTimeout(function() {
            var modalElement = $('#importCoverageModal');
            if (modalElement.length) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    try {
                        var modal = bootstrap.Modal.getInstance(modalElement[0]);
                        if (modal) {
                            modal.hide();
                        }
                    } catch (e) {
                        modalElement.modal('hide');
                    }
                } else {
                    modalElement.modal('hide');
                }
            }
        }, 100);
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        }).then(() => {
            // Reload DataTable setelah import berhasil
            table.ajax.reload(null, false);
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
        @endif

        // Handle View Coverage
        $(document).on('click', '.btn-view-coverage', function() {
            var id = $(this).data('id');
            viewCoverage(id);
        });

        // Handle Edit Coverage
        $(document).on('click', '.btn-edit-coverage', function() {
            var id = $(this).data('id');
            editCoverage(id);
        });

        // Handle Delete Coverage
        $(document).on('click', '.btn-delete-coverage', function() {
            var id = $(this).data('id');
            var lokasi = $(this).data('lokasi');
            var detail = $(this).data('detail');
            deleteCoverage(id, lokasi, detail);
        });

        // Handle toggle manual input untuk Kategori Aktivitas
        $('#btnEditKategoriAktivitasManual').on('click', function() {
            var manualInput = $('#editKategoriAktivitasManual');
            if (manualInput.hasClass('d-none')) {
                manualInput.removeClass('d-none');
                $('#editKategoriAktivitas').val('');
                manualInput.focus();
            } else {
                manualInput.addClass('d-none').val('');
            }
        });

        // Handle toggle manual input untuk Kategori Area
        $('#btnEditKategoriAreaManual').on('click', function() {
            var manualInput = $('#editKategoriAreaManual');
            if (manualInput.hasClass('d-none')) {
                manualInput.removeClass('d-none');
                $('#editKategoriArea').val('');
                manualInput.focus();
            } else {
                manualInput.addClass('d-none').val('');
            }
        });

        // Jika select kategori aktivitas diubah, sembunyikan input manual
        $('#editKategoriAktivitas').on('change', function() {
            if ($(this).val()) {
                $('#editKategoriAktivitasManual').addClass('d-none').val('');
            }
        });

        // Jika select kategori area diubah, sembunyikan input manual
        $('#editKategoriArea').on('change', function() {
            if ($(this).val()) {
                $('#editKategoriAreaManual').addClass('d-none').val('');
            }
        });
    });

    function viewCoverage(id) {
        // Fetch data via AJAX
        $.ajax({
            url: "{{ url('cctv-data-coverage-import') }}/" + id,
            type: "GET",
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    var content = `
                        <div class="p-3">
                            <h6 class="fw-bold mb-3">Detail CCTV Coverage</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" style="width: 150px;">No. CCTV:</td>
                                    <td>${data.no_cctv || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Coverage Lokasi:</td>
                                    <td>${data.coverage_lokasi || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Coverage Detail Lokasi:</td>
                                    <td>${data.coverage_detail_lokasi || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Kategori Aktivitas:</td>
                                    <td>${data.kategori_aktivitas || '-'}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Kategori Area:</td>
                                    <td>${data.kategori_area || '-'}</td>
                                </tr>
                            </table>
                        </div>
                    `;
                    Swal.fire({
                        title: 'Detail Coverage',
                        html: content,
                        width: '600px',
                        confirmButtonText: 'Tutup'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal memuat data'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data coverage'
                });
            }
        });
    }

    function editCoverage(id) {
        // Fetch data via AJAX
        $.ajax({
            url: "{{ url('cctv-data-coverage-import') }}/" + id,
            type: "GET",
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    
                    // Set form values
                    $('#editCoverageId').val(data.id);
                    $('#editNoCctv').text(data.no_cctv || '-');
                    $('#editCoverageLokasi').val(data.coverage_lokasi || '');
                    $('#editCoverageDetailLokasi').val(data.coverage_detail_lokasi || '');
                    
                    // Set kategori aktivitas - cek apakah ada di option, jika tidak gunakan input manual
                    var kategoriAktivitas = data.kategori_aktivitas || '';
                    if (kategoriAktivitas) {
                        var aktivitasOption = $('#editKategoriAktivitas option[value="' + kategoriAktivitas + '"]');
                        if (aktivitasOption.length > 0) {
                            $('#editKategoriAktivitas').val(kategoriAktivitas);
                            $('#editKategoriAktivitasManual').addClass('d-none').val('');
                        } else {
                            $('#editKategoriAktivitas').val('');
                            $('#editKategoriAktivitasManual').removeClass('d-none').val(kategoriAktivitas);
                        }
                    } else {
                        $('#editKategoriAktivitas').val('');
                        $('#editKategoriAktivitasManual').addClass('d-none').val('');
                    }
                    
                    // Set kategori area - cek apakah ada di option, jika tidak gunakan input manual
                    var kategoriArea = data.kategori_area || '';
                    if (kategoriArea) {
                        var areaOption = $('#editKategoriArea option[value="' + kategoriArea + '"]');
                        if (areaOption.length > 0) {
                            $('#editKategoriArea').val(kategoriArea);
                            $('#editKategoriAreaManual').addClass('d-none').val('');
                        } else {
                            $('#editKategoriArea').val('');
                            $('#editKategoriAreaManual').removeClass('d-none').val(kategoriArea);
                        }
                    } else {
                        $('#editKategoriArea').val('');
                        $('#editKategoriAreaManual').addClass('d-none').val('');
                    }
                    
                    // Show modal
                    var editModal = new bootstrap.Modal(document.getElementById('editCoverageModal'));
                    editModal.show();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal memuat data'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data coverage'
                });
            }
        });
    }

    // Handle form submit untuk edit
    $('#editCoverageForm').on('submit', function(e) {
        e.preventDefault();
        
        // Ambil nilai dari select atau input manual
        var kategoriAktivitas = $('#editKategoriAktivitasManual').hasClass('d-none') 
            ? $('#editKategoriAktivitas').val().trim() 
            : $('#editKategoriAktivitasManual').val().trim();
        var kategoriArea = $('#editKategoriAreaManual').hasClass('d-none') 
            ? $('#editKategoriArea').val().trim() 
            : $('#editKategoriAreaManual').val().trim();
        
        var formData = {
            id: $('#editCoverageId').val(),
            coverage_lokasi: $('#editCoverageLokasi').val().trim(),
            coverage_detail_lokasi: $('#editCoverageDetailLokasi').val().trim(),
            kategori_aktivitas: kategoriAktivitas,
            kategori_area: kategoriArea
        };

        // Validasi
        if (!formData.coverage_lokasi || !formData.coverage_detail_lokasi) {
            Swal.fire({
                icon: 'warning',
                title: 'Validasi Gagal',
                text: 'Coverage Lokasi dan Coverage Detail Lokasi harus diisi!'
            });
            return;
        }

        // Disable submit button
        var submitBtn = $('#editCoverageForm button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

        $.ajax({
            url: "{{ url('cctv-data-coverage-import') }}/" + formData.id,
            type: "PUT",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Close modal
                    var editModal = bootstrap.Modal.getInstance(document.getElementById('editCoverageModal'));
                    editModal.hide();
                    
                    // Reload table
                    table.ajax.reload(null, false);
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data coverage berhasil diupdate',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Gagal menyimpan data'
                    });
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var errorMsg = 'Gagal menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
                });
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    function deleteCoverage(id, lokasi, detail) {
        Swal.fire({
            title: 'Hapus Coverage?',
            html: `<p>Apakah Anda yakin ingin menghapus coverage ini?</p>
                   <p class="text-muted"><strong>Lokasi:</strong> ${lokasi || '-'}<br>
                   <strong>Detail:</strong> ${detail || '-'}</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('cctv-data-coverage-import') }}/" + id,
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data coverage berhasil dihapus'
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Gagal menghapus data'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menghapus data coverage'
                        });
                    }
                });
            }
        });
    }
</script>
@endsection

