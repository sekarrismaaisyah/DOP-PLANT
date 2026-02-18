@extends('layouts.master')

@section('title', 'Alert Log')
@section('content')
<x-page-title title="Alert Log" pagetitle="Alert Log" />

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Tabs -->
<ul class="nav nav-tabs nav-tabs-custom mb-3" id="alertLogTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="supervisory-tab" data-bs-toggle="tab" data-bs-target="#supervisory" type="button" role="tab" aria-selected="true">
            <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">assignment</i>
            Supervisory
        </button>
    </li>
</ul>

<div class="tab-content" id="alertLogTabContent">
    <div class="tab-pane fade show active" id="supervisory" role="tabpanel">
        <div class="card rounded-4 border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Data Alert Supervisory</h5>
                        <p class="text-muted small mb-0 mt-1">Log status alert Pengawasan Berjarak — diperbarui otomatis pagi, siang, dan sore</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter bar -->
                <div class="row g-2 mb-4 p-3 rounded-3 bg-light align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small fw-medium text-muted mb-1">Tanggal</label>
                        <input type="date" id="filterTanggal" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-medium text-muted mb-1">Risk Level</label>
                        <select id="filterRiskLevel" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="HIGH">HIGH (Merah)</option>
                            <option value="MEDIUM">MEDIUM (Kuning)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="btnApplyFilter" class="btn btn-primary btn-sm">
                            <i class="material-icons-outlined me-1" style="font-size: 16px; vertical-align: middle;">filter_list</i>
                            Terapkan
                        </button>
                        <button type="button" id="btnResetFilter" class="btn btn-outline-secondary btn-sm ms-1">Reset</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="supervisoryTable" class="table table-hover table-bordered align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">Tanggal</th>
                                <th class="text-nowrap">Nama Lokasi</th>
                                <th class="text-nowrap">Risk Level</th>
                                <th class="text-nowrap">SAP Report</th>
                                <th class="text-nowrap">CCTV Online</th>
                                <th class="text-nowrap">High Risk Area</th>
                                <th class="text-nowrap">Diperbarui</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs-custom .nav-link {
        font-weight: 500;
        color: #6b7280;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.6rem 1rem;
    }
    .nav-tabs-custom .nav-link:hover { color: #111827; }
    .nav-tabs-custom .nav-link.active {
        color: #008cff;
        border-bottom-color: #008cff;
        background: transparent;
    }
    #supervisoryTable thead th { font-size: 0.8125rem; }
    #supervisoryTable tbody td { font-size: 0.875rem; }
    .dataTables_wrapper .dataTables_length select { padding: 0.25rem 1.5rem; }
    .dataTables_wrapper .dataTables_filter input { margin-left: 0.5rem; padding: 0.35rem 0.5rem; }
</style>
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
            { data: 'updated_at', name: 'updated_at', orderable: true }
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
});
</script>
@endsection
