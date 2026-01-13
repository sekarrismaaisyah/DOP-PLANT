@extends('layouts.master')

@section('title', 'Alert Issue Area Kerja') 
@section('content')
<x-page-title title="Alert Issue Area Kerja" pagetitle="Alert Issue Area Kerja" />

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mb-3" id="intervensiTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="open-tab" data-bs-toggle="tab" data-bs-target="#open-issues" type="button" role="tab">
            Daftar Issue
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="done-tab" data-bs-toggle="tab" data-bs-target="#done-issues" type="button" role="tab">
            Issue Selesai
        </button>
    </li>
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="intervensiTabContent">
    <!-- Tab: Open Issues -->
    <div class="tab-pane fade show active" id="open-issues" role="tabpanel">
        <div class="row">
            <div class="col-12">
                <div class="card rounded-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <h5 class="mb-0 fw-bold">Daftar Issue Area Kerja</h5>
                                <p class="text-muted mb-0">Kelola intervensi yang masih terbuka</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="intervensiTable" class="table table-bordered table-hover table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 8%;">ID</th>
                                        <th style="width: 12%;">Lokasi</th>
                                        <th style="width: 12%;">Area Kerja</th>
                                        <th style="width: 12%;">Pelapor</th>
                                        <th style="width: 10%;">SID PIC</th>
                                        <th style="width: 12%;">Nama PIC</th>
                                        <th style="width: 20%;">Issue</th>
                                        <th style="width: 8%;">Status</th>
                                        <th style="width: 12%;">Tanggal</th>
                                        <th style="width: 5%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via DataTable -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab: Done Issues -->
    <div class="tab-pane fade" id="done-issues" role="tabpanel">
        <div class="row">
            <div class="col-12">
                <div class="card rounded-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <h5 class="mb-0 fw-bold">Issue Selesai</h5>
                                <p class="text-muted mb-0">Daftar issue yang sudah ditangani dan ditutup</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="doneIntervensiTable" class="table table-bordered table-hover table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="width: 8%;">ID</th>
                                        <th style="width: 12%;">Lokasi</th>
                                        <th style="width: 12%;">Area Kerja</th>
                                        <th style="width: 12%;">Pelapor</th>
                                        <th style="width: 10%;">SID PIC</th>
                                        <th style="width: 12%;">Nama PIC</th>
                                        <th style="width: 20%;">Issue</th>
                                        <th style="width: 12%;">Tanggal Pelaporan</th>
                                        <th style="width: 12%;">Tanggal Selesai</th>
                                        <th style="width: 5%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via DataTable -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Intervensi Done -->
<div class="modal fade" id="detailDoneIntervensiModal" tabindex="-1" aria-labelledby="detailDoneIntervensiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailDoneIntervensiModalLabel">Detail Issue Selesai</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailDoneIntervensiBody" style="max-height: 80vh; overflow-y: auto;">
                <!-- Detail will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Close Intervensi dengan Evidence -->
<div class="modal fade" id="closeIntervensiModal" tabindex="-1" aria-labelledby="closeIntervensiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="closeIntervensiModalLabel">Close Intervensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="closeIntervensiForm" enctype="multipart/form-data">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <input type="hidden" id="closeIntervensiId" name="intervensi_id">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                    <!-- Issue Info -->
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Lokasi</label>
                                <div class="fw-semibold" id="closeLokasi">-</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">Area Kerja</label>
                                <div class="fw-semibold" id="closeAreaKerja">-</div>
                            </div>
                            <div class="col-md-12">
                                <label class="text-muted small mb-1">Issue</label>
                                <div id="closeIssue" class="text-break">-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Resolution -->
                    <div class="mb-4">
                        <label for="resolution" class="form-label">
                            Hasil/Resolusi <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="resolution" name="resolution" rows="4" required placeholder="Masukkan hasil atau resolusi dari issue yang ditangani..."></textarea>
                    </div>

                    <!-- Main Evidence -->
                    <div class="mb-4">
                        <label for="evidence" class="form-label">Evidence Utama</label>
                        <input type="file" class="form-control" id="evidence" name="evidence" accept="image/*,.pdf,.doc,.docx" onchange="previewFile(this, 'mainEvidencePreview')">
                        <div id="mainEvidencePreview" class="mt-2"></div>
                        <small class="text-muted">Format: JPG, PNG, PDF, DOC, DOCX | Max 10MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Close Intervensi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    #closeIntervensiModal .modal-body {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }
    
    #closeIntervensiModal .modal-body::-webkit-scrollbar {
        width: 8px;
    }
    
    #closeIntervensiModal .modal-body::-webkit-scrollbar-track {
        background: #f7fafc;
        border-radius: 4px;
    }
    
    #closeIntervensiModal .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }
    
    #closeIntervensiModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
    
    .file-preview {
        max-width: 150px;
        max-height: 150px;
        border-radius: 4px;
        margin-top: 8px;
        object-fit: contain;
    }
    
    #mainEvidencePreview img {
        max-width: 150px;
        max-height: 150px;
    }
</style>

<script>
function previewFile(input, previewId) {
    var preview = document.getElementById(previewId);
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        var file = input.files[0];
        var reader = new FileReader();
        
        reader.onload = function(e) {
            if (file.type.startsWith('image/')) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'file-preview img-thumbnail';
                img.style.maxWidth = '150px';
                img.style.maxHeight = '150px';
                img.style.objectFit = 'contain';
                preview.appendChild(img);
            } else {
                var div = document.createElement('div');
                div.className = 'alert alert-info mt-2 py-2';
                div.style.fontSize = '0.875rem';
                div.innerHTML = '<i class="material-icons-outlined me-2" style="font-size: 16px; vertical-align: middle;">description</i><strong>' + file.name + '</strong><br><small>(' + (file.size / 1024 / 1024).toFixed(2) + ' MB)</small>';
                preview.appendChild(div);
            }
        };
        
        reader.readAsDataURL(file);
    }
}
</script>

@endsection

@section('scripts')
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#intervensiTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('intervensi-area-kerja.data') }}",
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'lokasi', name: 'lokasi' },
            { data: 'area_kerja', name: 'area_kerja' },
            { data: 'created_by', name: 'created_by' },
            { data: 'pic_username', name: 'pic_username' },
            { data: 'pic_nama', name: 'pic_nama' },
            { data: 'issue', name: 'issue', orderable: false },
            { data: 'status', name: 'status' },
            { data: 'tanggal_pelaporan', name: 'tanggal_pelaporan' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[7, 'desc']], // Order by tanggal_pelaporan desc
        pageLength: 25,
        language: {
            processing: "Memproses...",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // Initialize Done Issues DataTable
    var doneTable = $('#doneIntervensiTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('intervensi-area-kerja.done.data') }}",
            type: 'GET'
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'lokasi', name: 'lokasi' },
            { data: 'area_kerja', name: 'area_kerja' },
            { data: 'created_by', name: 'created_by' },
            { data: 'pic_username', name: 'pic_username' },
            { data: 'pic_nama', name: 'pic_nama' },
            { data: 'issue', name: 'issue', orderable: false },
            { data: 'tanggal_pelaporan', name: 'tanggal_pelaporan' },
            { data: 'tanggal_selesai', name: 'tanggal_selesai' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[7, 'desc']], // Order by tanggal_selesai desc
        pageLength: 25,
        language: {
            processing: "Memproses...",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // Reload done table when tab is shown
    $('#done-tab').on('shown.bs.tab', function () {
        doneTable.ajax.reload();
    });

    // Handle view done detail button
    $(document).on('click', '.view-done-detail-btn', function() {
        var intervensiId = $(this).data('id');
        
        $('#detailDoneIntervensiBody').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat data...</p></div>');
        $('#detailDoneIntervensiModal').modal('show');
        
        $.ajax({
            url: "{{ url('intervensi-area-kerja') }}/" + intervensiId + "/done/detail",
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    var html = '';
                    
                    // Informasi Issue Card
                    html += '<div class="card mb-3 border-0 shadow-sm">';
                    html += '<div class="card-header bg-light">';
                    html += '<h6 class="mb-0"><i class="material-icons-outlined me-2 text-primary">info</i>Informasi Issue</h6>';
                    html += '</div>';
                    html += '<div class="card-body">';
                    html += '<div class="row g-3">';
                    html += '<div class="col-md-6">';
                    html += '<label class="text-muted small mb-1">Lokasi</label>';
                    html += '<div class="fw-semibold">' + (data.lokasi || '-') + '</div>';
                    html += '</div>';
                    html += '<div class="col-md-6">';
                    html += '<label class="text-muted small mb-1">Area Kerja</label>';
                    html += '<div class="fw-semibold">' + (data.area_kerja || '-') + '</div>';
                    html += '</div>';
                    html += '<div class="col-md-6">';
                    html += '<label class="text-muted small mb-1">ID Issue</label>';
                    html += '<div class="fw-semibold">#' + data.id + '</div>';
                    html += '</div>';
                    html += '<div class="col-md-6">';
                    html += '<label class="text-muted small mb-1">Tanggal Pelaporan</label>';
                    html += '<div>' + (data.tanggal_pelaporan || '-') + '</div>';
                    html += '</div>';
                    html += '<div class="col-md-6">';
                    html += '<label class="text-muted small mb-1">Tanggal Selesai</label>';
                    html += '<div class="text-success fw-semibold">' + (data.tanggal_selesai || '-') + '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    
                    // PIC Information Card
                    html += '<div class="card mb-3 border-0 shadow-sm">';
                    html += '<div class="card-header bg-light">';
                    html += '<h6 class="mb-0"><i class="material-icons-outlined me-2 text-primary">person</i>Informasi PIC</h6>';
                    html += '</div>';
                    html += '<div class="card-body">';
                    html += '<div class="row g-3">';
                    html += '<div class="col-md-4">';
                    html += '<label class="text-muted small mb-1">SID PIC</label>';
                    html += '<div>' + (data.pic_username || '-') + '</div>';
                    html += '</div>';
                    html += '<div class="col-md-4">';
                    html += '<label class="text-muted small mb-1">Nama PIC</label>';
                    html += '<div>' + (data.pic_nama || '-') + '</div>';
                    html += '</div>';
                    html += '<div class="col-md-4">';
                    html += '<label class="text-muted small mb-1">Telepon PIC</label>';
                    html += '<div>' + (data.pic_telepon || '-') + '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    
                    // Issue Card
                    html += '<div class="card mb-3 border-0 shadow-sm">';
                    html += '<div class="card-header bg-light">';
                    html += '<h6 class="mb-0"><i class="material-icons-outlined me-2 text-warning">report_problem</i>Issue</h6>';
                    html += '</div>';
                    html += '<div class="card-body">';
                    html += '<div class="text-break">' + (data.issue || '-') + '</div>';
                    html += '</div>';
                    html += '</div>';
                    
                    // Resolution Card
                    html += '<div class="card mb-3 border-0 shadow-sm">';
                    html += '<div class="card-header bg-light">';
                    html += '<h6 class="mb-0"><i class="material-icons-outlined me-2 text-success">check_circle</i>Resolusi/Hasil</h6>';
                    html += '</div>';
                    html += '<div class="card-body">';
                    html += '<div class="text-break">' + (data.resolution || '-') + '</div>';
                    html += '</div>';
                    html += '</div>';
                    
                    // Evidence Utama Card
                    if (data.evidence_path) {
                        html += '<div class="card mb-3 border-0 shadow-sm">';
                        html += '<div class="card-header bg-light">';
                        html += '<h6 class="mb-0"><i class="material-icons-outlined me-2 text-info">attach_file</i>Evidence Utama</h6>';
                        html += '</div>';
                        html += '<div class="card-body">';
                        var evidenceUrl = "{{ asset('storage/') }}/" + data.evidence_path;
                        if (data.evidence_path.match(/\.(jpg|jpeg|png|gif)$/i)) {
                            html += '<img src="' + evidenceUrl + '" class="img-thumbnail" style="max-width: 100%; max-height: 400px; object-fit: contain;" alt="Evidence">';
                        } else {
                            html += '<a href="' + evidenceUrl + '" target="_blank" class="btn btn-outline-primary"><i class="material-icons-outlined me-1">download</i>Download Evidence</a>';
                        }
                        html += '</div>';
                        html += '</div>';
                    }
                    
                    $('#detailDoneIntervensiBody').html(html);
                } else {
                    $('#detailDoneIntervensiBody').html('<div class="alert alert-danger">Gagal memuat data intervensi.</div>');
                }
            },
            error: function(xhr) {
                $('#detailDoneIntervensiBody').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data intervensi.</div>');
            }
        });
    });

    // Handle close intervensi button - Open modal form
    $(document).on('click', '.close-intervensi-btn', function() {
        var intervensiId = $(this).data('id');
        
        // Reset form
        $('#closeIntervensiForm')[0].reset();
        $('#mainEvidencePreview').html('');
        
        // Load intervensi detail
        $.ajax({
            url: "{{ url('intervensi-area-kerja') }}/" + intervensiId + "/detail",
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    
                    // Set form data
                    $('#closeIntervensiId').val(data.id);
                    $('#closeLokasi').text(data.lokasi || '-');
                    $('#closeAreaKerja').text(data.area_kerja || '-');
                    $('#closeIssue').text(data.issue || '-');
                    $('#resolution').val(data.resolution || '');
                    
                    // Show modal
                    $('#closeIntervensiModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Gagal memuat data intervensi.'
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat memuat data intervensi.'
                });
            }
        });
    });
    
    // Handle form submission
    $('#closeIntervensiForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get CSRF token
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (!csrfToken) {
            csrfToken = $('input[name="_token"]').val() || '{{ csrf_token() }}';
        }
        
        var formData = new FormData(this);
        formData.append('status', 'closed');
        formData.append('_token', csrfToken);
        
        var submitBtn = $(this).find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Memproses...');
        
        $.ajax({
            url: "{{ url('intervensi-area-kerja') }}/" + $('#closeIntervensiId').val() + "/status",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Intervensi berhasil ditutup dengan evidence.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#closeIntervensiModal').modal('hide');
                    table.ajax.reload();
                    // Reload done table if tab is active
                    if ($('#done-tab').hasClass('active')) {
                        doneTable.ajax.reload();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Terjadi kesalahan saat menutup intervensi.'
                    });
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                var errorMessage = 'Terjadi kesalahan saat menutup intervensi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endsection
