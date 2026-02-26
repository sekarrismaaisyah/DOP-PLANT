@extends('layouts.master')

@section('title', 'Alert Log')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}">
<style>
.alert-log-page .filter-card { border-radius: 1rem; }
.alert-log-page .main-card { border-radius: 1rem; overflow: hidden; }
.alert-log-page .nav-tabs .nav-link { font-weight: 500; color: #495057; }
.alert-log-page .nav-tabs .nav-link.active { color: #0d6efd; border-bottom: 2px solid #0d6efd; }
.alert-log-page .tab-content { padding-top: 1.5rem; }
.alert-log-page .dataTables_wrapper .row:first-child { margin-bottom: 0.75rem; }
.alert-log-page table.dataTable { width: 100% !important; border-collapse: separate; border-spacing: 0; }
.alert-log-page table.dataTable thead th {
    border-bottom: 2px solid #e9ecef;
    white-space: nowrap;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: #495057;
    background: #f8f9fa;
}
.alert-log-page table.dataTable tbody td {
    padding: 0.85rem 1rem;
    font-size: 0.9rem;
    line-height: 1.4;
    vertical-align: middle;
}
.alert-log-page table.dataTable tbody tr:hover { background-color: #f8f9fa; }
.alert-log-page .empty-state { padding: 3rem 1rem; text-align: center; color: #6c757d; }
.alert-log-page .badge-high { background-color: #dc3545; }
.alert-log-page .badge-medium { background-color: #ffc107; color: #212529; }
.alert-log-page .badge-low { background-color: #198754; }
</style>
@endsection

@section('content')
<div class="alert-log-page">
    <x-page-title title="Alert Log" pagetitle="Alert Log" />

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card main-card">
        <div class="card-header bg-white py-3">
            <ul class="nav nav-tabs card-header-tabs" id="alertLogTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="supervisory-tab" data-bs-toggle="tab" data-bs-target="#supervisory" type="button" role="tab" aria-selected="true">
                        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">assignment</i>
                        Alert Supervisory
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="mobility-tab" data-bs-toggle="tab" data-bs-target="#mobility" type="button" role="tab" aria-selected="false">
                        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">directions_car</i>
                        Alert Mobility
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="critical-area-tab" data-bs-toggle="tab" data-bs-target="#critical-area" type="button" role="tab" aria-selected="false">
                        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">warning</i>
                        Alert Critical Area
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="probability-tab" data-bs-toggle="tab" data-bs-target="#probability" type="button" role="tab" aria-selected="false">
                        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">insights</i>
                        Alert Probability Area
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="alertLogTabContent">
                {{-- Tab 1: Alert Supervisory --}}
                <div class="tab-pane fade show active" id="supervisory" role="tabpanel">
                    <div class="card filter-card mb-4 border">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Tanggal</label>
                                    <input type="date" id="filterTanggal" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Risk Level</label>
                                    <select id="filterRiskLevel" class="form-select">
                                        <option value="">Semua</option>
                                        <option value="HIGH">HIGH (Merah)</option>
                                        <option value="MEDIUM">MEDIUM (Kuning)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" id="btnApplyFilter" class="btn btn-primary w-100">
                                        <i class="material-icons-outlined align-middle me-1" style="font-size:18px;">filter_alt</i> Filter
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" id="btnResetFilter" class="btn btn-outline-secondary w-100">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="supervisoryTable" class="table table-hover table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nama Lokasi</th>
                                    <th>Risk Level</th>
                                    <th>SAP Report</th>
                                    <th>CCTV Online</th>
                                    <th>High Risk Area</th>
                                    <th>Diperbarui</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab 2: Alert Mobility --}}
                <div class="tab-pane fade" id="mobility" role="tabpanel">
                    <div id="mobilityTableWrap" class="table-responsive d-none">
                        <table id="mobilityTable" class="table table-hover table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Kode / Unit</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="mobilityTableBody"></tbody>
                        </table>
                    </div>
                    <div id="mobilityEmpty" class="empty-state d-none">
                        <i class="material-icons-outlined" style="font-size: 48px;">directions_car</i>
                        <p class="mt-2 mb-0">Belum ada data alert Mobility. Data dapat diintegrasikan dari sistem Unit & Orang.</p>
                    </div>
                    <div id="mobilityLoading" class="empty-state">
                        <div class="spinner-border spinner-border-sm text-primary"></div>
                        <p class="mt-2 mb-0">Memuat data...</p>
                    </div>
                </div>

                {{-- Tab 3: Alert Critical Area --}}
                <div class="tab-pane fade" id="critical-area" role="tabpanel">
                    <div id="criticalAreaTableWrap" class="table-responsive d-none">
                        <table id="criticalAreaTable" class="table table-hover table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Kode IKK</th>
                                    <th>Jenis IJK</th>
                                    <th>Nama Pekerjaan</th>
                                    <th>Site</th>
                                    <th>Status Matriks</th>
                                    <th>Status Pekerjaan</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="criticalAreaTableBody"></tbody>
                        </table>
                    </div>
                    <div id="criticalAreaEmpty" class="empty-state d-none">
                        <i class="material-icons-outlined" style="font-size: 48px;">check_circle</i>
                        <p class="mt-2 mb-0">Tidak ada alert Critical Area (semua IKK sudah ada IPK/OKK).</p>
                    </div>
                    <div id="criticalAreaLoading" class="empty-state">
                        <div class="spinner-border spinner-border-sm text-primary"></div>
                        <p class="mt-2 mb-0">Memuat data IKK...</p>
                    </div>
                </div>

                {{-- Tab 4: Alert Probability Area --}}
                <div class="tab-pane fade" id="probability" role="tabpanel">
                    <div id="probabilityTableWrap" class="table-responsive d-none">
                        <table id="probabilityTable" class="table table-hover table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nama PJA</th>
                                    <th>Site / Lokasi</th>
                                    <th>Tipe / Kategori</th>
                                    <th>Layer</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="probabilityTableBody"></tbody>
                        </table>
                    </div>
                    <div id="probabilityEmpty" class="empty-state d-none">
                        <i class="material-icons-outlined" style="font-size: 48px;">insights</i>
                        <p class="mt-2 mb-0">Tidak ada data PJA (Probability).</p>
                    </div>
                    <div id="probabilityLoading" class="empty-state">
                        <div class="spinner-border spinner-border-sm text-primary"></div>
                        <p class="mt-2 mb-0">Memuat data PJA...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Hasil Matriks TARP --}}
<div class="modal fade" id="matrixResultModal" tabindex="-1" aria-labelledby="matrixResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="matrixResultModalLabel"><i class="material-icons-outlined me-2">assessment</i> Hasil Matriks TARP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="matrixResultModalBody">
                <div class="text-center py-5 text-muted">
                    <div class="spinner-border" role="status"></div>
                    <p class="mt-2 mb-0">Memuat hasil matriks...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
$(document).ready(function() {
    var table = $('#supervisoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('supervisory-alert-log.data') }}",
            type: 'GET',
            data: function(d) {
                d.tanggal = $('#filterTanggal').val() || '';
                d.risk_level = $('#filterRiskLevel').val() || '';
            }
        },
        columns: [
            { data: 'tanggal', name: 'tanggal', orderable: true },
            { data: 'nama_lokasi', name: 'nama_lokasi', orderable: true },
            { data: 'risk_level', name: 'risk_level', orderable: true },
            { data: 'has_sap_report', name: 'has_sap_report', orderable: true },
            { data: 'has_online_cctv', name: 'has_online_cctv', orderable: true },
            { data: 'is_high_risk_area', name: 'is_high_risk_area', orderable: true },
            { data: 'updated_at', name: 'updated_at', orderable: true },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Memproses...</span></div> Memproses...',
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ada data yang cocok",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: "Cari:",
            paginate: {
                first: "&laquo;",
                last: "&raquo;",
                next: "&rsaquo;",
                previous: "&lsaquo;"
            }
        },
        drawCallback: function() {
            $('.dataTables_wrapper .dataTables_info').addClass('text-muted small');
        }
    });

    $('#btnApplyFilter').on('click', function() {
        table.ajax.reload();
    });

    $('#btnResetFilter').on('click', function() {
        $('#filterTanggal').val('');
        $('#filterRiskLevel').val('');
        table.ajax.reload();
    });

    $('#filterTanggal, #filterRiskLevel').on('change', function() {
        table.ajax.reload();
    });

    // Tab Mobility: load data saat tab ditampilkan
    $('button[data-bs-target="#mobility"]').on('shown.bs.tab', function() {
        if ($('#mobilityTableBody').data('loaded')) return;
        $('#mobilityLoading').removeClass('d-none');
        $('#mobilityTableWrap').addClass('d-none');
        $('#mobilityEmpty').addClass('d-none');
        $.get("{{ route('supervisory-alert-log.data-mobility') }}").done(function(res) {
            $('#mobilityLoading').addClass('d-none');
            $('#mobilityTableBody').data('loaded', true);
            var data = res.data || [];
            if (data.length === 0) {
                $('#mobilityEmpty').removeClass('d-none');
            } else {
                var intervensiBase = "{{ route('intervensi-area-kerja.index') }}";
                var rows = data.map(function(r) {
                    var lok = (r.lokasi || r.unit || r.kode || '').toString();
                    var intervensiUrl = lok ? (intervensiBase + '?lokasi=' + encodeURIComponent(lok)) : intervensiBase;
                    return '<tr><td>' + (r.kode || r.unit || '-') + '</td><td>' + (r.lokasi || '-') + '</td><td>' + (r.status || '-') + '</td><td>' + (r.waktu || '-') + '</td><td class="text-end"><a href="' + intervensiUrl + '" class="btn btn-sm btn-outline-success" title="Intervensi"><i class="material-icons-outlined me-1" style="font-size:16px;vertical-align:middle;">campaign</i> Intervensi</a></td></tr>';
                }).join('');
                $('#mobilityTableBody').html(rows);
                $('#mobilityTableWrap').removeClass('d-none');
            }
        }).fail(function() {
            $('#mobilityLoading').addClass('d-none');
            $('#mobilityEmpty').removeClass('d-none').find('p').text('Gagal memuat data Mobility.');
        });
    });

    // Tab Critical Area: load IKK dari full-maps API
    var escapeHtml = function(s) {
        if (s == null || s === '') return '';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    };
    $('button[data-bs-target="#critical-area"]').on('shown.bs.tab', function() {
        if ($('#criticalAreaTableBody').data('loaded')) return;
        $('#criticalAreaLoading').removeClass('d-none');
        $('#criticalAreaTableWrap').addClass('d-none');
        $('#criticalAreaEmpty').addClass('d-none');
        $.get("{{ route('full-maps.api.ikk-for-controlroom-sidebar') }}").done(function(res) {
            $('#criticalAreaLoading').addClass('d-none');
            $('#criticalAreaTableBody').data('loaded', true);
            var data = (res.success && res.data) ? res.data : [];
            if (data.length === 0) {
                $('#criticalAreaEmpty').removeClass('d-none');
            } else {
                var badge = function(s) {
                    if (s === 'Merah') return '<span class="badge bg-danger">Merah</span>';
                    if (s === 'Kuning') return '<span class="badge bg-warning text-dark">Kuning</span>';
                    return '<span class="badge bg-secondary">' + escapeHtml(s || '-') + '</span>';
                };
                var dashboardUrl = "{{ route('dopmikk.dopm.dashboard') }}";
                var today = "{{ now()->format('Y-m-d') }}";
                var rows = data.map(function(r) {
                    var tanggal = (r.tanggal_dop || today);
                    var intervensiUrl = dashboardUrl + '?tanggal=' + encodeURIComponent(tanggal);
                    var btn = '<a href="' + intervensiUrl + '" target="_blank" class="btn btn-sm btn-outline-success" title="Intervensi"><i class="material-icons-outlined me-1" style="font-size:16px;vertical-align:middle;">campaign</i> Intervensi</a>';
                    return '<tr><td>' + escapeHtml(r.code || '-') + '</td><td>' + escapeHtml(r.jenis_ijin_kerja_khusus || '-') + '</td><td>' + escapeHtml(r.nama_pekerjaan || '-') + '</td><td>' + escapeHtml(r.site || '-') + '</td><td>' + badge(r.status_matriks) + '</td><td>' + escapeHtml(r.status_pekerjaan || '-') + '</td><td class="text-end">' + btn + '</td></tr>';
                }).join('');
                $('#criticalAreaTableBody').html(rows);
                $('#criticalAreaTableWrap').removeClass('d-none');
            }
        }).fail(function() {
            $('#criticalAreaLoading').addClass('d-none');
            $('#criticalAreaEmpty').removeClass('d-none').find('p').text('Gagal memuat data IKK.');
        });
    });

    // Tab Probability: load PJA dari maps API
    $('button[data-bs-target="#probability"]').on('shown.bs.tab', function() {
        if ($('#probabilityTableBody').data('loaded')) return;
        $('#probabilityLoading').removeClass('d-none');
        $('#probabilityTableWrap').addClass('d-none');
        $('#probabilityEmpty').addClass('d-none');
        $.get("{{ route('maps.api.pja-data') }}").done(function(res) {
            $('#probabilityLoading').addClass('d-none');
            $('#probabilityTableBody').data('loaded', true);
            var data = (res.success && res.data) ? res.data : [];
            if (!Array.isArray(data)) data = [];
            if (data.length === 0) {
                $('#probabilityEmpty').removeClass('d-none');
            } else {
                var esc = typeof escapeHtml === 'function' ? escapeHtml : function(s){ return (s==null||s==='') ? '' : String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); };
                var intervensiBase = "{{ route('intervensi-area-kerja.index') }}";
                var rows = data.map(function(r) {
                    var nama = r.nama_pja || r.name || ('PJA ' + (r.pja_id || ''));
                    var siteLok = [r.site, r.lokasi, r.detail_lokasi].filter(Boolean).join(' / ');
                    var tipeCat = [r.pja_type_name, r.pja_category_name].filter(Boolean).join(' / ');
                    var lokParam = (r.site || r.lokasi || r.detail_lokasi || siteLok || '').toString();
                    var intervensiUrl = lokParam ? (intervensiBase + '?lokasi=' + encodeURIComponent(lokParam)) : intervensiBase;
                    var btn = '<a href="' + intervensiUrl + '" class="btn btn-sm btn-outline-success" title="Intervensi"><i class="material-icons-outlined me-1" style="font-size:16px;vertical-align:middle;">campaign</i> Intervensi</a>';
                    return '<tr><td>' + esc(nama) + '</td><td>' + esc(siteLok || '-') + '</td><td>' + esc(tipeCat || '-') + '</td><td>' + esc(r.pja_layer || '-') + '</td><td class="text-end">' + btn + '</td></tr>';
                }).join('');
                $('#probabilityTableBody').html(rows);
                $('#probabilityTableWrap').removeClass('d-none');
            }
        }).fail(function() {
            $('#probabilityLoading').addClass('d-none');
            $('#probabilityEmpty').removeClass('d-none').find('p').text('Gagal memuat data PJA.');
        });
    });

    $(document).on('click', '.btn-view-matrix', function() {
        var id = $(this).data('id');
        if (!id) return;
        var url = "{{ url('supervisory-alert-log') }}/" + id + "/detail";
        var $body = $('#matrixResultModalBody');
        $body.html('<div class="text-center py-5 text-muted"><div class="spinner-border"></div><p class="mt-2 mb-0">Memuat hasil matriks...</p></div>');
        var $modal = new bootstrap.Modal(document.getElementById('matrixResultModal'));
        $modal.show();
        $.get(url).done(function(res) {
            if (!res.success || !res.data) {
                $body.html('<div class="alert alert-danger">Data tidak ditemukan.</div>');
                return;
            }
            var d = res.data;
            var riskColor = d.risk_level === 'HIGH' ? '#dc2626' : (d.risk_level === 'MEDIUM' ? '#f59e0b' : '#22c55e');
            var riskWarna = d.risk_level === 'HIGH' ? 'Merah' : (d.risk_level === 'MEDIUM' ? 'Orange' : 'Hijau');
            var sapText = d.has_sap_report ? 'MEMENUHI' : 'TIDAK MEMENUHI';
            var cctvText = d.has_online_cctv ? 'MEMENUHI' : 'TIDAK MEMENUHI';
            var highRiskText = d.is_high_risk_area ? 'YA' : 'TIDAK';
            var sapInHighText = d.has_sap_in_high_risk ? 'MEMENUHI' : 'TIDAK MEMENUHI';
            var cctvCount = (d.cctv_list || []).length;
            var sapCount = (d.sap_list || []).length;

            var html = '<div class="row"><div class="col-md-6">';
            html += '<div class="card mb-3"><div class="card-header"><h6 class="mb-0"><i class="material-icons-outlined">info</i> Informasi Area Kerja</h6></div><div class="card-body small">';
            html += '<p class="mb-1"><strong>Lokasi:</strong> ' + (d.nama_lokasi || '-') + '</p>';
            html += '<p class="mb-1"><strong>ID Lokasi:</strong> ' + (d.id_lokasi || '-') + '</p>';
            html += '<p class="mb-1"><strong>Tanggal:</strong> ' + (d.tanggal || '-') + '</p>';
            html += '</div></div>';

            html += '<div class="card mb-3"><div class="card-header" style="background-color:' + riskColor + '20;"><h6 class="mb-0"><i class="material-icons-outlined">assessment</i> Risk Matrix Summary</h6></div><div class="card-body small">';
            html += '<p class="mb-1"><strong>Risk Level:</strong> <span style="color:' + riskColor + ';font-weight:bold;">' + (d.risk_level || '-') + '</span></p>';
            html += '<p class="mb-1">' + (d.has_sap_report ? '✓' : '✗') + ' Terdapat Laporan SAP: <span style="color:' + (d.has_sap_report ? '#22c55e' : '#dc2626') + '">' + sapText + '</span></p>';
            html += '<p class="mb-1">' + (d.has_online_cctv ? '✓' : '✗') + ' CCTV Kondisi Online: <span style="color:' + (d.has_online_cctv ? '#22c55e' : '#dc2626') + '">' + cctvText + '</span>' + (cctvCount ? ' (' + cctvCount + ' CCTV ditemukan)' : '') + '</p>';
            html += '<p class="mb-1">⚠ Area Highrisk: <span style="color:' + (d.is_high_risk_area ? '#f59e0b' : '#6b7280') + '">' + highRiskText + '</span></p>';
            if (d.is_high_risk_area) {
                html += '<p class="mb-0">' + (d.has_sap_in_high_risk ? '✓' : '✗') + ' Area Highrisk ada Laporan SAP: <span style="color:' + (d.has_sap_in_high_risk ? '#22c55e' : '#dc2626') + '">' + sapInHighText + '</span></p>';
            }
            html += '</div></div></div><div class="col-md-6">';

            html += '<div class="card mb-3"><div class="card-header"><h6 class="mb-0"><i class="material-icons-outlined">rule</i> TARP (Triggered Action Response Plan)</h6></div><div class="card-body small">';
            html += '<p class="mb-1"><strong>Level:</strong> <span style="color:' + riskColor + ';font-weight:bold;">' + (d.risk_level || '-') + '</span></p>';
            html += '<p class="mb-1"><strong>Warna:</strong> ' + riskWarna + '</p>';
            html += '<p class="mb-0"><strong>Kriteria:</strong> ' + (d.risk_level === 'HIGH' ? 'Risiko tinggi, pelanggaran kritikal.' : (d.risk_level === 'MEDIUM' ? 'Potensi moderate, closed loop tidak tuntas.' : 'Kondisi memenuhi.')) + '</p>';
            html += '</div></div>';

            var recs = d.tarp_recommendations || [];
            if (recs.length) {
                html += '<div class="card mb-3"><div class="card-header"><h6 class="mb-0"><i class="material-icons-outlined">list_alt</i> Rekomendasi Tindakan</h6></div><div class="card-body small">';
                html += '<p class="text-muted small mb-2">' + (d.tanggal || '') + '</p>';
                recs.forEach(function(r) {
                    var badge = r.priority === 'HIGH' ? 'danger' : (r.priority === 'MEDIUM' ? 'warning' : 'success');
                    html += '<div class="d-flex gap-2 mb-2"><span class="badge bg-' + badge + '">' + (r.priority || '') + '</span><span>' + (r.action || '') + '</span></div>';
                });
                html += '</div></div>';
            }
            html += '</div></div>';

            html += '<div class="card mb-3"><div class="card-header"><h6 class="mb-0"><i class="material-icons-outlined">videocam</i> CCTV di Area (' + cctvCount + ')</h6></div><div class="card-body small">';
            if (cctvCount === 0) {
                html += '<p class="text-muted mb-0">Tidak ada CCTV ditemukan di area ini.</p>';
            } else {
                (d.cctv_list || []).forEach(function(c) {
                    var status = (c.kondisi || c.status || '').toLowerCase();
                    var online = status === 'baik' || status === 'online' || (c.connected || '').toLowerCase() === 'yes';
                    html += '<div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2"><div><strong>' + (c.nama_cctv || c.no_cctv || 'CCTV') + '</strong><br><small class="text-muted">No: ' + (c.no_cctv || '-') + ' | <span style="color:' + (online ? '#22c55e' : '#dc2626') + '">' + (online ? 'Online' : 'Offline') + '</span></small>' + (c.lokasi ? '<br><small class="text-muted">' + c.lokasi + '</small>' : '') + '</div></div>';
                });
            }
            html += '</div></div>';

            html += '<div class="card mb-0"><div class="card-header"><h6 class="mb-0"><i class="material-icons-outlined">description</i> Laporan SAP Hari Ini (' + sapCount + ')</h6></div><div class="card-body small">';
            if (sapCount === 0) {
                html += '<p class="text-muted mb-0">Tidak ada laporan SAP hari ini di area ini.</p>';
            } else {
                (d.sap_list || []).forEach(function(s) {
                    html += '<div class="border-bottom pb-2 mb-2"><strong>' + (s.jenis_laporan || 'SAP') + '</strong> #' + (s.task_number || '-') + '<br><small class="text-muted">Lokasi: ' + (s.lokasi || s.detail_lokasi || '-') + '</small></div>';
                });
            }
            html += '</div></div>';

            $body.html(html);
        }).fail(function() {
            $body.html('<div class="alert alert-danger">Gagal memuat data.</div>');
        });
    });
});
</script>
@endsection
