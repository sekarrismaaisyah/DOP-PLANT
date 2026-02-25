@extends('layouts.masterDopm')

@section('title', 'Issue Closure - DOPM & IKK')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}">
<style>
.issue-closure-page .filter-card { border-radius: 1rem; }
.issue-closure-page .main-card { border-radius: 1rem; overflow: hidden; }
.issue-closure-page .nav-tabs .nav-link { font-weight: 500; color: #495057; }
.issue-closure-page .nav-tabs .nav-link.active { color: #0d6efd; border-bottom: 2px solid #0d6efd; }
.issue-closure-page .tab-content { padding-top: 1.5rem; }
.issue-closure-page .dataTables_wrapper .row:first-child { margin-bottom: 0.75rem; }
.issue-closure-page table.dataTable { width: 100% !important; border-collapse: separate; border-spacing: 0; }
.issue-closure-page table.dataTable thead th {
    border-bottom: 2px solid #e9ecef;
    white-space: nowrap;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #495057;
    background: #f8f9fa;
}
.issue-closure-page table.dataTable tbody td {
    padding: 0.85rem 1rem;
    font-size: 0.9rem;
    line-height: 1.4;
    vertical-align: middle;
}
.issue-closure-page table.dataTable tbody tr:hover { background-color: #f8f9fa; }
.issue-closure-page .badge-open { background-color: #dc3545; }
.issue-closure-page .badge-in-progress { background-color: #ffc107; color: #212529; }
.issue-closure-page .badge-closed { background-color: #198754; }
.issue-closure-page .empty-state { padding: 3rem 1rem; text-align: center; color: #6c757d; }
.issue-closure-page .evidence-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; cursor: pointer; }
.issue-closure-page .evidence-list { display: flex; flex-wrap: wrap; gap: 8px; }
</style>
@endsection

@section('content')
<div class="issue-closure-page">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">DOPM & IKK</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dopmikk.dopm.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Issue Closure</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card filter-card mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="filterDateStart" name="date_start" value="{{ $dateStart }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="filterDateEnd" name="date_end" value="{{ $dateEnd }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="filterStatus" name="status">
                        <option value="">Semua Status</option>
                        <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">filter_alt</i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card main-card">
        <div class="card-header bg-white py-3">
            <ul class="nav nav-tabs card-header-tabs" id="issueTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="open-tab" data-bs-toggle="tab" data-bs-target="#openIssues" type="button" role="tab">
                        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">warning</i>
                        Open <span class="badge bg-danger ms-1">{{ $openCount }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progressIssues" type="button" role="tab">
                        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">pending</i>
                        In Progress <span class="badge bg-warning text-dark ms-1">{{ $inProgressCount }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="closed-tab" data-bs-toggle="tab" data-bs-target="#closedIssues" type="button" role="tab">
                        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">check_circle</i>
                        Closed <span class="badge bg-success ms-1">{{ $closedCount }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="issueTabContent">
                {{-- Open Issues --}}
                <div class="tab-pane fade show active" id="openIssues" role="tabpanel">
                    @if($openIssues->isEmpty())
                        <div class="empty-state">
                            <i class="material-icons-outlined" style="font-size:48px;">inbox</i>
                            <p class="mt-2 mb-0">Tidak ada issue yang perlu di-close.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tableOpen">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kode IKK</th>
                                        <th>Alert Level</th>
                                        <th>Diintervensi Oleh</th>
                                        <th>PIC</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($openIssues as $issue)
                                    <tr>
                                        <td>{{ $issue->tanggal->format('d M Y') }}</td>
                                        <td><strong>{{ $issue->kode_ikk }}</strong></td>
                                        <td><span class="badge bg-{{ $issue->alert_level == 3 ? 'danger' : ($issue->alert_level == 2 ? 'warning text-dark' : 'info') }}">Alert {{ $issue->alert_level }}</span></td>
                                        <td>{{ $issue->user_name ?? '-' }}</td>
                                        <td>{{ $issue->pic_name ?? '-' }}</td>
                                        <td><span class="badge badge-open">Open</span></td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-assign-pic" data-id="{{ $issue->id }}" data-kode="{{ $issue->kode_ikk }}" title="Assign PIC">
                                                <i class="material-icons-outlined" style="font-size:16px;">person_add</i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success btn-close-issue" data-id="{{ $issue->id }}" data-kode="{{ $issue->kode_ikk }}" title="Close Issue">
                                                <i class="material-icons-outlined" style="font-size:16px;">check</i> Close
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- In Progress Issues --}}
                <div class="tab-pane fade" id="progressIssues" role="tabpanel">
                    @if($inProgressIssues->isEmpty())
                        <div class="empty-state">
                            <i class="material-icons-outlined" style="font-size:48px;">hourglass_empty</i>
                            <p class="mt-2 mb-0">Tidak ada issue dalam proses.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tableProgress">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kode IKK</th>
                                        <th>Alert Level</th>
                                        <th>Diintervensi Oleh</th>
                                        <th>PIC</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inProgressIssues as $issue)
                                    <tr>
                                        <td>{{ $issue->tanggal->format('d M Y') }}</td>
                                        <td><strong>{{ $issue->kode_ikk }}</strong></td>
                                        <td><span class="badge bg-{{ $issue->alert_level == 3 ? 'danger' : ($issue->alert_level == 2 ? 'warning text-dark' : 'info') }}">Alert {{ $issue->alert_level }}</span></td>
                                        <td>{{ $issue->user_name ?? '-' }}</td>
                                        <td>{{ $issue->pic_name ?? '-' }}</td>
                                        <td><span class="badge badge-in-progress">In Progress</span></td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-success btn-close-issue" data-id="{{ $issue->id }}" data-kode="{{ $issue->kode_ikk }}" title="Close Issue">
                                                <i class="material-icons-outlined" style="font-size:16px;">check</i> Close
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Closed Issues --}}
                <div class="tab-pane fade" id="closedIssues" role="tabpanel">
                    @if($closedIssues->isEmpty())
                        <div class="empty-state">
                            <i class="material-icons-outlined" style="font-size:48px;">task_alt</i>
                            <p class="mt-2 mb-0">Belum ada issue yang ditutup.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tableClosed">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kode IKK</th>
                                        <th>Alert Level</th>
                                        <th>PIC</th>
                                        <th>Ditutup Oleh</th>
                                        <th>Waktu Ditutup</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($closedIssues as $issue)
                                    <tr>
                                        <td>{{ $issue->tanggal->format('d M Y') }}</td>
                                        <td><strong>{{ $issue->kode_ikk }}</strong></td>
                                        <td><span class="badge bg-{{ $issue->alert_level == 3 ? 'danger' : ($issue->alert_level == 2 ? 'warning text-dark' : 'info') }}">Alert {{ $issue->alert_level }}</span></td>
                                        <td>{{ $issue->pic_name ?? '-' }}</td>
                                        <td>{{ $issue->closure->closed_by_name ?? '-' }}</td>
                                        <td>{{ $issue->closure ? $issue->closure->closed_at->format('d M Y H:i') : '-' }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-info btn-view-closure" data-id="{{ $issue->id }}" data-kode="{{ $issue->kode_ikk }}" title="Lihat Detail">
                                                <i class="material-icons-outlined" style="font-size:16px;">visibility</i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Assign PIC --}}
<div class="modal fade" id="assignPicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign PIC</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignPicForm">
                <div class="modal-body">
                    <input type="hidden" id="assignIssueId" name="issue_id">
                    <p class="mb-3">Kode IKK: <strong id="assignKodeIkk"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cari PIC</label>
                        <input type="text" class="form-control" id="searchPic" placeholder="Ketik nama atau username...">
                    </div>
                    <div id="picSearchResults" class="list-group" style="max-height: 200px; overflow-y: auto;"></div>
                    <div class="mt-3 d-none" id="selectedPicWrap">
                        <label class="form-label fw-semibold">PIC Terpilih</label>
                        <div class="alert alert-success py-2 mb-0">
                            <input type="hidden" id="picUserId" name="pic_user_id">
                            <input type="hidden" id="picName" name="pic_name">
                            <input type="hidden" id="picEmail" name="pic_email">
                            <span id="selectedPicName"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSavePic" disabled>Simpan PIC</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Close Issue --}}
<div class="modal fade" id="closeIssueModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Close Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="closeIssueForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="closeIssueId" name="issue_id">
                    <p class="mb-3">Kode IKK: <strong id="closeKodeIkk"></strong></p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="closeKeterangan" name="keterangan" rows="3" required placeholder="Jelaskan penyelesaian issue..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Root Cause</label>
                        <textarea class="form-control" id="closeRootCause" name="root_cause" rows="2" placeholder="Penyebab akar masalah..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tindakan yang Dilakukan</label>
                        <textarea class="form-control" id="closeTindakan" name="tindakan" rows="2" placeholder="Tindakan/solusi yang diambil..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Evidence (Bukti) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="closeEvidences" name="evidences[]" multiple accept="image/*,.pdf,.doc,.docx" required>
                        <small class="text-muted">Upload gambar, PDF, atau dokumen sebagai bukti penyelesaian. Bisa pilih lebih dari satu file.</small>
                    </div>

                    <div id="evidencePreview" class="evidence-list mt-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitClose">
                        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">check_circle</i> Close Issue
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal View Closure Detail --}}
<div class="modal fade" id="viewClosureModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Closure</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Kode IKK:</strong> <span id="viewKodeIkk"></span></p>
                        <p class="mb-1"><strong>Tanggal:</strong> <span id="viewTanggal"></span></p>
                        <p class="mb-1"><strong>Alert Level:</strong> <span id="viewAlertLevel"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>PIC:</strong> <span id="viewPicName"></span></p>
                        <p class="mb-1"><strong>Ditutup Oleh:</strong> <span id="viewClosedBy"></span></p>
                        <p class="mb-1"><strong>Waktu Ditutup:</strong> <span id="viewClosedAt"></span></p>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <h6 class="fw-semibold">Keterangan</h6>
                    <p id="viewKeterangan" class="text-muted">-</p>
                </div>
                <div class="mb-3">
                    <h6 class="fw-semibold">Root Cause</h6>
                    <p id="viewRootCause" class="text-muted">-</p>
                </div>
                <div class="mb-3">
                    <h6 class="fw-semibold">Tindakan</h6>
                    <p id="viewTindakan" class="text-muted">-</p>
                </div>
                <div class="mb-3">
                    <h6 class="fw-semibold">Evidence</h6>
                    <div id="viewEvidences" class="evidence-list"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Preview Image --}}
<div class="modal fade" id="previewImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewImageTitle">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImageSrc" src="" alt="Preview" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // DataTables
    if (document.getElementById('tableOpen') && document.getElementById('tableOpen').querySelector('tbody tr')) {
        $('#tableOpen').DataTable({ pageLength: 25, order: [[0, 'desc']] });
    }
    if (document.getElementById('tableProgress') && document.getElementById('tableProgress').querySelector('tbody tr')) {
        $('#tableProgress').DataTable({ pageLength: 25, order: [[0, 'desc']] });
    }
    if (document.getElementById('tableClosed') && document.getElementById('tableClosed').querySelector('tbody tr')) {
        $('#tableClosed').DataTable({ pageLength: 25, order: [[0, 'desc']] });
    }

    var assignPicModal = new bootstrap.Modal(document.getElementById('assignPicModal'));
    var closeIssueModal = new bootstrap.Modal(document.getElementById('closeIssueModal'));
    var viewClosureModal = new bootstrap.Modal(document.getElementById('viewClosureModal'));
    var previewImageModal = new bootstrap.Modal(document.getElementById('previewImageModal'));

    var searchPicUrl = @json(route('dopmikk.api.search-users'));
    var assignPicUrl = @json(route('dopmikk.api.assign-pic'));
    var closeIssueUrl = @json(route('dopmikk.api.close-issue'));
    var getClosureUrl = @json(route('dopmikk.api.get-closure'));

    // Assign PIC
    document.addEventListener('click', function(e) {
        var btnAssign = e.target.closest('.btn-assign-pic');
        if (btnAssign) {
            document.getElementById('assignIssueId').value = btnAssign.dataset.id;
            document.getElementById('assignKodeIkk').textContent = btnAssign.dataset.kode;
            document.getElementById('searchPic').value = '';
            document.getElementById('picSearchResults').innerHTML = '';
            document.getElementById('selectedPicWrap').classList.add('d-none');
            document.getElementById('btnSavePic').disabled = true;
            assignPicModal.show();
        }
    });

    var searchTimeout;
    document.getElementById('searchPic').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        var q = this.value.trim();
        if (q.length < 2) {
            document.getElementById('picSearchResults').innerHTML = '';
            return;
        }
        searchTimeout = setTimeout(function() {
            fetch(searchPicUrl + '?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    var html = '';
                    if (data.users && data.users.length > 0) {
                        data.users.forEach(function(u) {
                            html += '<a href="#" class="list-group-item list-group-item-action pic-option" data-id="'+u.id+'" data-name="'+u.nama+'" data-email="'+(u.email||'')+'">';
                            html += '<strong>'+u.nama+'</strong> <small class="text-muted">('+u.username+')</small>';
                            html += '</a>';
                        });
                    } else {
                        html = '<div class="list-group-item text-muted">Tidak ditemukan</div>';
                    }
                    document.getElementById('picSearchResults').innerHTML = html;
                });
        }, 300);
    });

    document.getElementById('picSearchResults').addEventListener('click', function(e) {
        e.preventDefault();
        var opt = e.target.closest('.pic-option');
        if (opt) {
            document.getElementById('picUserId').value = opt.dataset.id;
            document.getElementById('picName').value = opt.dataset.name;
            document.getElementById('picEmail').value = opt.dataset.email;
            document.getElementById('selectedPicName').textContent = opt.dataset.name;
            document.getElementById('selectedPicWrap').classList.remove('d-none');
            document.getElementById('btnSavePic').disabled = false;
            document.getElementById('picSearchResults').innerHTML = '';
            document.getElementById('searchPic').value = '';
        }
    });

    document.getElementById('assignPicForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var fd = new FormData(this);
        fetch(assignPicUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                assignPicModal.hide();
                location.reload();
            } else {
                alert(data.message || 'Gagal menyimpan PIC');
            }
        })
        .catch(() => alert('Terjadi kesalahan'));
    });

    // Close Issue
    document.addEventListener('click', function(e) {
        var btnClose = e.target.closest('.btn-close-issue');
        if (btnClose) {
            document.getElementById('closeIssueId').value = btnClose.dataset.id;
            document.getElementById('closeKodeIkk').textContent = btnClose.dataset.kode;
            document.getElementById('closeIssueForm').reset();
            document.getElementById('evidencePreview').innerHTML = '';
            closeIssueModal.show();
        }
    });

    document.getElementById('closeEvidences').addEventListener('change', function() {
        var preview = document.getElementById('evidencePreview');
        preview.innerHTML = '';
        Array.from(this.files).forEach(function(file) {
            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'evidence-thumb';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else {
                var div = document.createElement('div');
                div.className = 'badge bg-secondary';
                div.textContent = file.name;
                preview.appendChild(div);
            }
        });
    });

    document.getElementById('closeIssueForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = document.getElementById('btnSubmitClose');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

        var fd = new FormData(this);
        fetch(closeIssueUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: fd
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeIssueModal.hide();
                location.reload();
            } else {
                alert(data.message || 'Gagal menutup issue');
                btn.disabled = false;
                btn.innerHTML = '<i class="material-icons-outlined align-middle me-1" style="font-size:18px;">check_circle</i> Close Issue';
            }
        })
        .catch(() => {
            alert('Terjadi kesalahan');
            btn.disabled = false;
            btn.innerHTML = '<i class="material-icons-outlined align-middle me-1" style="font-size:18px;">check_circle</i> Close Issue';
        });
    });

    // View Closure
    document.addEventListener('click', function(e) {
        var btnView = e.target.closest('.btn-view-closure');
        if (btnView) {
            fetch(getClosureUrl + '?issue_id=' + btnView.dataset.id)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        var d = data.data;
                        document.getElementById('viewKodeIkk').textContent = d.kode_ikk || '-';
                        document.getElementById('viewTanggal').textContent = d.tanggal || '-';
                        document.getElementById('viewAlertLevel').textContent = 'Alert ' + (d.alert_level || '-');
                        document.getElementById('viewPicName').textContent = d.pic_name || '-';
                        document.getElementById('viewClosedBy').textContent = d.closed_by_name || '-';
                        document.getElementById('viewClosedAt').textContent = d.closed_at || '-';
                        document.getElementById('viewKeterangan').textContent = d.keterangan || '-';
                        document.getElementById('viewRootCause').textContent = d.root_cause || '-';
                        document.getElementById('viewTindakan').textContent = d.tindakan || '-';

                        var evHtml = '';
                        if (d.evidences && d.evidences.length > 0) {
                            d.evidences.forEach(function(ev) {
                                if (ev.file_type && ev.file_type.startsWith('image/')) {
                                    evHtml += '<img src="'+ev.file_url+'" class="evidence-thumb preview-evidence" data-url="'+ev.file_url+'" data-name="'+ev.file_name+'" alt="'+ev.file_name+'">';
                                } else {
                                    evHtml += '<a href="'+ev.file_url+'" target="_blank" class="badge bg-secondary text-decoration-none">'+ev.file_name+'</a>';
                                }
                            });
                        } else {
                            evHtml = '<span class="text-muted">Tidak ada evidence</span>';
                        }
                        document.getElementById('viewEvidences').innerHTML = evHtml;
                        viewClosureModal.show();
                    } else {
                        alert(data.message || 'Gagal memuat data');
                    }
                });
        }
    });

    // Preview Image
    document.addEventListener('click', function(e) {
        var img = e.target.closest('.preview-evidence');
        if (img) {
            document.getElementById('previewImageTitle').textContent = img.dataset.name || 'Preview';
            document.getElementById('previewImageSrc').src = img.dataset.url;
            previewImageModal.show();
        }
    });
});
</script>
@endsection
