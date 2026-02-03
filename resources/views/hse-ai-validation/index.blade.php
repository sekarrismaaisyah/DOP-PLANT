@extends('layouts.master-validasi')

@section('title', 'Validasi TBC BY AI')
@section('content')
<x-page-title title="Validasi TBC BY AI" pagetitle="Data Validasi TBC AI per Site" />

@php
    $stats = $stats ?? [];
    $total = $stats['total'] ?? 0;
    $pctAiMatch = $total > 0 ? round(($stats['ai_match_found'] ?? 0) / $total * 100, 1) : 0;
    $pctNoMatch = $total > 0 ? round(($stats['ai_no_match'] ?? 0) / $total * 100, 1) : 0;
    $pctTbcDone = $total > 0 ? round(($stats['evaluator_tbc_done'] ?? 0) / $total * 100, 1) : 0;
    $pctGrDone = $total > 0 ? round(($stats['evaluator_gr_done'] ?? 0) / $total * 100, 1) : 0;
    $tbcPending = $stats['evaluator_tbc_pending'] ?? 0;
    $grPending = $stats['evaluator_gr_pending'] ?? 0;
    $siteLabels = $sites ? array_values($sites) : [];
    $siteValues = $sites ? array_map(function ($k) use ($siteCounts) { return $siteCounts[$k] ?? 0; }, array_keys($sites)) : [];
@endphp

<!-- Statistik Validasi - Dashboard Style -->
<div class="row g-3 mb-4 validation-dashboard">
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body position-relative">
                <div class="position-absolute top-0 end-0 mt-2 me-2">
                    <span class="material-icons-outlined text-muted" style="font-size: 20px;">more_vert</span>
                </div>
                <h2 class="mb-1 fw-bold">{{ number_format($total) }}</h2>
                <div class="d-flex align-items-center gap-1 mb-2">
                    @if($pctAiMatch >= $pctNoMatch)
                        <span class="text-success small d-flex align-items-center"><span class="material-icons-outlined" style="font-size: 16px;">trending_up</span> {{ $pctAiMatch }}%</span>
                    @else
                        <span class="text-danger small d-flex align-items-center"><span class="material-icons-outlined" style="font-size: 16px;">trending_down</span> {{ $pctNoMatch }}%</span>
                    @endif
                </div>
                <p class="text-muted small mb-3">Total Data Validasi</p>
                <div class="chart-container-line" style="height: 80px;">
                    <canvas id="chartTotalTrend"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-0">
                <div class="row g-0 h-100">
                    <div class="col-6 col-md-3 border-end border-bottom border-md-bottom-0">
                        <div class="p-3 text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                                <span class="material-icons-outlined text-primary">summarize</span>
                                <h5 class="mb-0 fw-bold">{{ number_format($total) }}</h5>
                            </div>
                            <p class="mb-0 small text-muted">Total Data</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 border-end border-bottom border-md-bottom-0">
                        <div class="p-3 text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                                <span class="material-icons-outlined text-info">smart_toy</span>
                                <h5 class="mb-0 fw-bold">{{ number_format($stats['ai_match_found'] ?? 0) }}</h5>
                            </div>
                            <p class="mb-0 small text-muted">Validasi BY AI</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 border-end">
                        <div class="p-3 text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                                <span class="material-icons-outlined text-success">done_all</span>
                                <h5 class="mb-0 fw-bold">{{ number_format($stats['evaluator_tbc_done'] ?? 0) }}</h5>
                            </div>
                            <p class="mb-0 small text-muted">TBC Evaluator</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                                <span class="material-icons-outlined text-success">verified</span>
                                <h5 class="mb-0 fw-bold">{{ number_format($stats['evaluator_gr_done'] ?? 0) }}</h5>
                            </div>
                            <p class="mb-0 small text-muted">GR Evaluator</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold">Validasi per Site</h6>
                    <span class="material-icons-outlined text-muted" style="font-size: 18px;">more_vert</span>
                </div>
                <div class="chart-container-bar" style="height: 200px;">
                    <canvas id="chartPerSite"></canvas>
                </div>
                @if($total > 0)
                    <p class="small text-muted mb-0 mt-2">{{ count($siteLabels) }} site &bull; {{ number_format($total) }} total</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold">Status Validasi Evaluator</h6>
                    <span class="material-icons-outlined text-muted" style="font-size: 18px;">more_vert</span>
                </div>
                <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                    <div class="position-relative" style="width: 160px; height: 160px;">
                        <canvas id="chartEvaluatorDonut"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <span class="d-block fw-bold fs-5">{{ $total > 0 ? round((($stats['evaluator_tbc_done'] ?? 0) + ($stats['evaluator_gr_done'] ?? 0)) / 2 / $total * 100, 0) : 0 }}%</span>
                            <span class="small text-muted">Rata-rata</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center gap-4 mt-2 small">
                    <span class="d-flex align-items-center gap-1"><span class="rounded-circle d-inline-block" style="width:8px;height:8px;background:#10b981;"></span> Sudah</span>
                    <span class="d-flex align-items-center gap-1"><span class="rounded-circle d-inline-block" style="width:8px;height:8px;background:#e5e7eb;"></span> Belum</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold">AI vs Evaluator</h6>
                    <span class="material-icons-outlined text-muted" style="font-size: 18px;">more_vert</span>
                </div>
                <div class="chart-container-bar" style="height: 200px;">
                    <canvas id="chartAiVsEvaluator"></canvas>
                </div>
                <div class="d-flex justify-content-center gap-3 mt-2 small">
                    <span class="d-flex align-items-center gap-1"><span class="rounded-circle d-inline-block" style="width:8px;height:8px;background:#0ea5e9;"></span> AI Match</span>
                    <span class="d-flex align-items-center gap-1"><span class="rounded-circle d-inline-block" style="width:8px;height:8px;background:#10b981;"></span> Evaluator</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 fw-bold">Progress Validasi Evaluator</h6>
                    <span class="material-icons-outlined text-muted" style="font-size: 18px;">more_vert</span>
                </div>
                <h5 class="mb-1 fw-bold">{{ $total > 0 ? round(($pctTbcDone + $pctGrDone) / 2, 1) : 0 }}%</h5>
                <div class="d-flex align-items-center gap-1 text-success small mb-2">
                    <span class="material-icons-outlined" style="font-size: 16px;">trending_up</span>
                    <span>Rata-rata TBC &amp; GR</span>
                </div>
                <p class="text-muted small mb-2">{{ $tbcPending + $grPending }} belum validasi</p>
                <div class="progress rounded-3" style="height: 10px;">
                    <div class="progress-bar bg-primary rounded-3" role="progressbar" style="width: {{ $total > 0 ? ($pctTbcDone + $pctGrDone) / 2 : 0 }}%" aria-valuenow="{{ $total > 0 ? ($pctTbcDone + $pctGrDone) / 2 : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="position-relative d-inline-block mb-2">
                                <canvas id="chartTbcRing" width="120" height="120"></canvas>
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <span class="d-block fw-bold fs-6">{{ $pctTbcDone }}%</span>
                                    <span class="small text-muted">TBC</span>
                                </div>
                            </div>
                            <p class="mb-0 small text-muted">Validasi TBC</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="position-relative d-inline-block mb-2">
                                <canvas id="chartGrRing" width="120" height="120"></canvas>
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <span class="d-block fw-bold fs-6">{{ $pctGrDone }}%</span>
                                    <span class="small text-muted">GR</span>
                                </div>
                            </div>
                            <p class="mb-0 small text-muted">Validasi GR</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                    @foreach($sites as $siteKey => $siteName)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                    id="{{ strtolower(str_replace(' ', '-', $siteKey)) }}-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#{{ strtolower(str_replace(' ', '-', $siteKey)) }}" 
                                    type="button" 
                                    role="tab"
                                    data-site="{{ $siteKey }}">
                                {{ $siteName }}
                                <span class="badge bg-primary ms-2">{{ $siteCounts[$siteKey] ?? 0 }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    @foreach($sites as $siteKey => $siteName)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                             id="{{ strtolower(str_replace(' ', '-', $siteKey)) }}" 
                             role="tabpanel">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <h5 class="mb-0 fw-bold">Data Validasi {{ $siteName }}</h5>
                                    <p class="mb-0 text-muted">Data validasi HSE AI untuk site {{ $siteName }}</p>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <!-- Column Visibility Toggle -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                id="columnToggle-{{ strtolower(str_replace(' ', '-', $siteKey)) }}" 
                                                data-bs-toggle="dropdown" 
                                                data-bs-auto-close="outside"
                                                aria-expanded="false">
                                            <i class="material-icons-outlined" style="font-size: 16px; vertical-align: middle;">view_column</i> 
                                            Kolom
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end column-visibility-menu p-3" 
                                             aria-labelledby="columnToggle-{{ strtolower(str_replace(' ', '-', $siteKey)) }}"
                                             style="min-width: 280px;">
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                <span class="fw-bold text-primary">Tampilkan Kolom</span>
                                                <div>
                                                    <button type="button" class="btn btn-xs btn-success me-1" onclick="showAllColumns('{{ $siteKey }}')" title="Tampilkan Semua">
                                                        <i class="material-icons-outlined" style="font-size: 14px;">add</i> Semua
                                                    </button>
                                                    <button type="button" class="btn btn-xs btn-danger" onclick="hideAllColumns('{{ $siteKey }}')" title="Sembunyikan Semua">
                                                        <i class="material-icons-outlined" style="font-size: 14px;">remove</i> Reset
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="column-list" id="columnList-{{ strtolower(str_replace(' ', '-', $siteKey)) }}">
                                                <!-- Column checkboxes will be populated dynamically -->
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="loadSiteData('{{ $siteKey }}')">
                                        <i class="material-icons-outlined">refresh</i> Refresh
                                    </button>
                                </div>
                            </div>

                                            <div class="table-responsive">
                                <table id="dataTable-{{ strtolower(str_replace(' ', '-', $siteKey)) }}" class="table table-bordered table-striped nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Task Number</th>
                                            <th>Jenis Laporan</th>
                                            <th>Aktivitas Pekerjaan</th>
                                            <th>Lokasi</th>
                                            <th>Detail Lokasi</th>
                                            <th>Keterangan</th>
                                            <th>Tanggal Pelaporan</th>
                                            <th>Perusahaan Pelapor</th>
                                            <th>Pelapor</th>
                                            <th>SID Pelapor</th>
                                            <th>Jabatan Fungsional Pelapor</th>
                                            <th>Departemen Pelapor</th>
                                            <th>PIC</th>
                                            <th>SID PIC</th>
                                            <th>Jabatan Fungsional PIC</th>
                                            <th>Perusahaan PIC</th>
                                            <th>Departemen PIC</th>
                                            <th>Foto Temuan</th>
                                            <th>Foto Penyelesaian</th>
                                            <th>Tools Pengawasan</th>
                                            <th>Catatan Tindakan</th>
                                            <th>NIK Pelapor</th>
                                            <th>Nama Pelapor</th>
                                            <th>Nama Perusahaan Karyawan</th>
                                            <th>Jabatan Fungsional Karyawan</th>
                                            <th>Latitude</th>
                                            <th>Longitude</th>
                                            <th>Site</th>
                                            <th>Keterangan Lokasi</th>
                                            <th>Jam</th>
                                            <th>Menit</th>
                                            <th>Nama Lokasi</th>
                                            <th>Nama Detail Lokasi</th>
                                            <th>Validasi BY AI</th>
                                            <th>Klasifikasi BY AI</th>
                                            <th>AI Sub Category</th>
                                            <th>AI TBC</th>
                                            <th>AI PSPP</th>
                                            <th>AI GR</th>
                                            <th>AI Incident</th>
                                            <th>AI Justification</th>
                                            <th>AI Confidence Score</th>
                                            <th>Tanggal Validasi</th>
                                            <th>Validated By</th>
                                            <th>TBC</th>
                                            <th>GR</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<link href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
<style>
    .nav-tabs-custom .nav-link {
        color: #6b7280;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 12px 20px;
        font-weight: 500;
    }
    .nav-tabs-custom .nav-link:hover {
        color: #3b82f6;
        border-bottom-color: #e5e7eb;
    }
    .nav-tabs-custom .nav-link.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
        background-color: transparent;
    }
    .badge {
        font-size: 0.75rem;
    }
    
    /* Tooltip Styles */
    .inspection-tooltip {
        position: fixed;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 16px;
        z-index: 10000;
        max-width: 500px;
        max-height: 80vh;
        overflow-y: auto;
        font-size: 12px;
        pointer-events: none;
    }
    
    .inspection-tooltip .tooltip-header {
        font-weight: bold;
        font-size: 14px;
        color: #3b82f6;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 2px solid #3b82f6;
    }
    
    .inspection-tooltip .tooltip-row {
        display: flex;
        margin-bottom: 8px;
        line-height: 1.5;
    }
    
    .inspection-tooltip .tooltip-label {
        font-weight: 600;
        color: #6b7280;
        min-width: 140px;
        flex-shrink: 0;
    }
    
    .inspection-tooltip .tooltip-value {
        color: #1f2937;
        flex: 1;
    }
    
    .inspection-tooltip .tooltip-section {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #e5e7eb;
    }
    
    .inspection-tooltip .tooltip-section-title {
        font-weight: bold;
        color: #3b82f6;
        margin-bottom: 8px;
    }
    
    .task-number-cell {
        cursor: help;
        color: #3b82f6;
        text-decoration: underline;
    }
    
    .photo-cell img {
        max-width: 150px;
        max-height: 150px;
        object-fit: contain;
        cursor: pointer;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .select-cell {
        min-width: 150px;
    }
    
    /* DataTable Dynamic Scroll Styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .dataTables_wrapper {
        width: 100%;
    }
    
    .dataTables_wrapper .dataTables_scroll {
        overflow: visible;
    }
    
    .dataTables_wrapper .dataTables_scrollBody {
        overflow-x: auto !important;
        overflow-y: auto !important;
    }
    
    /* Make table expand based on content */
    table.dataTable {
        width: auto !important;
        min-width: 100%;
    }
    
    table.dataTable th {
        white-space: nowrap;
        padding: 8px 12px;
    }
    
    table.dataTable td {
        padding: 8px 12px;
        vertical-align: top;
    }
    
    /* Specific column widths */
    table.dataTable th:nth-child(1),
    table.dataTable td:nth-child(1) {
        min-width: 100px; /* Task Number */
    }
    
    table.dataTable .photo-cell {
        min-width: 160px;
    }
    
    table.dataTable .select-cell {
        min-width: 160px;
    }
    
    /* AI Justification column - fixed width with text wrapping */
    .col-ai-justification {
        max-width: 300px !important;
        min-width: 300px !important;
        width: 300px !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        word-break: break-word !important;
        overflow: hidden !important;
        vertical-align: top;
    }
    
    /* Keterangan column - fixed width with text wrapping */
    .col-keterangan {
        max-width: 250px !important;
        min-width: 250px !important;
        width: 250px !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        word-break: break-word !important;
        overflow: hidden !important;
        vertical-align: top;
    }
    
    /* AI Sub Category column - fixed width with text wrapping */
    .col-ai-subcategory {
        max-width: 200px !important;
        min-width: 200px !important;
        width: 200px !important;
        white-space: normal !important;
        word-wrap: break-word !important;
        word-break: break-word !important;
        overflow: hidden !important;
        vertical-align: top;
    }
    
    /* Catatan column - prevent overflow */
    .col-catatan {
        max-width: 250px !important;
        min-width: 200px !important;
        overflow: hidden !important;
    }
    
    /* Column Visibility Styles */
    .column-visibility-menu {
        max-height: 500px;
        overflow-y: auto;
        min-width: 320px !important;
    }
    
    .column-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .column-visibility-header {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
        padding-bottom: 10px;
    }
    
    .column-group-title {
        font-size: 11px;
        font-weight: 700;
        color: #6366f1;
        text-transform: uppercase;
        padding: 8px 12px 4px;
        margin-top: 8px;
        border-top: 1px solid #e5e7eb;
    }
    
    .column-group-title:first-child {
        margin-top: 0;
        border-top: none;
    }
    
    .column-visibility-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        margin-bottom: 4px;
        background: #f8f9fa;
        border-radius: 6px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .column-visibility-item:hover {
        background: #e9ecef;
    }
    
    .column-visibility-item.hidden-column {
        background: #fff3cd;
        opacity: 0.7;
    }
    
    .column-visibility-item .column-name {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
    }
    
    .column-visibility-item .column-actions {
        display: flex;
        gap: 4px;
    }
    
    .column-visibility-item .btn-toggle {
        width: 28px;
        height: 28px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 18px;
        line-height: 1;
    }
    
    .column-visibility-item .btn-show {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }
    
    .column-visibility-item .btn-show:hover {
        background: #059669;
        border-color: #059669;
    }
    
    .column-visibility-item .btn-hide {
        background: #ef4444;
        border-color: #ef4444;
        color: white;
    }
    
    .column-visibility-item .btn-hide:hover {
        background: #dc2626;
        border-color: #dc2626;
    }
    
    .btn-xs {
        padding: 2px 8px;
        font-size: 11px;
    }
    
    .column-visibility-status {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 10px;
        margin-left: 8px;
    }
    
    .column-visibility-status.visible {
        background: #d1fae5;
        color: #065f46;
    }
    
    .column-visibility-status.hidden {
        background: #fee2e2;
        color: #991b1b;
    }
    
    /* Column count badge */
    .column-count-badge {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        background: #3b82f6;
        color: white;
        margin-left: 4px;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Validation Dashboard Charts
    (function initValidationCharts() {
        var total = {{ $total ?? 0 }};
        var aiMatch = {{ $stats['ai_match_found'] ?? 0 }};
        var aiNoMatch = {{ $stats['ai_no_match'] ?? 0 }};
        var tbcDone = {{ $stats['evaluator_tbc_done'] ?? 0 }};
        var grDone = {{ $stats['evaluator_gr_done'] ?? 0 }};
        var pctTbcDone = {{ $pctTbcDone ?? 0 }};
        var pctGrDone = {{ $pctGrDone ?? 0 }};
        var siteLabels = @json($siteLabels ?? []);
        var siteValues = @json($siteValues ?? []);

        if (typeof Chart === 'undefined') return;

        // Line chart - Total trend (simple area)
        var ctxTrend = document.getElementById('chartTotalTrend');
        if (ctxTrend) {
            var trendData = total > 0 ? [aiNoMatch, aiMatch, total].map(function(v) { return Math.max(0, v); }) : [0, 0, 0];
            new Chart(ctxTrend.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['', '', ''],
                    datasets: [{
                        data: trendData,
                        borderColor: 'rgba(16, 185, 129, 1)',
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { display: false, beginAtZero: true }, x: { display: false } }
                }
            });
        }

        // Bar chart - Per Site
        var ctxSite = document.getElementById('chartPerSite');
        if (ctxSite && siteLabels.length) {
            new Chart(ctxSite.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: siteLabels,
                    datasets: [{
                        data: siteValues,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 0,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { maxTicksLimit: 5 } },
                        x: { ticks: { maxRotation: 45, font: { size: 10 } } }
                    }
                }
            });
        }

        // Donut - Evaluator status (TBC done vs pending, GR done vs pending -> average)
        var ctxDonut = document.getElementById('chartEvaluatorDonut');
        if (ctxDonut && total > 0) {
            var doneAvg = (tbcDone + grDone) / 2;
            var pendingAvg = total - doneAvg;
            if (pendingAvg < 0) pendingAvg = 0;
            new Chart(ctxDonut.getContext('2d'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [doneAvg, pendingAvg],
                        backgroundColor: ['#10b981', '#e5e7eb'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: { legend: { display: false } }
                }
            });
        }

        // Bar - AI vs Evaluator (2 groups: AI Match, Evaluator TBC+GR)
        var ctxAiVs = document.getElementById('chartAiVsEvaluator');
        if (ctxAiVs) {
            new Chart(ctxAiVs.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Validasi AI', 'Evaluator'],
                    datasets: [{
                        label: 'Jumlah',
                        data: [aiMatch, (tbcDone + grDone) / 2],
                        backgroundColor: ['#0ea5e9', '#10b981'],
                        borderWidth: 0,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { maxTicksLimit: 5 } },
                        x: { ticks: { font: { size: 11 } } }
                    }
                }
            });
        }

        // Ring - TBC %
        var ctxTbc = document.getElementById('chartTbcRing');
        if (ctxTbc) {
            new Chart(ctxTbc.getContext('2d'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [pctTbcDone, 100 - pctTbcDone],
                        backgroundColor: ['#3b82f6', '#e5e7eb'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: { legend: { display: false } }
                }
            });
        }

        // Ring - GR %
        var ctxGr = document.getElementById('chartGrRing');
        if (ctxGr) {
            new Chart(ctxGr.getContext('2d'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [pctGrDone, 100 - pctGrDone],
                        backgroundColor: ['#10b981', '#e5e7eb'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: { legend: { display: false } }
                }
            });
        }
    })();

    // Store DataTable instances
    const dataTableInstances = {};
    
    // Store tooltip data for each site (indexed by row ID)
    const tooltipDataStore = {};
    
    // Site mapping
    const siteMap = {
        'BMO 1': 'bmo-1',
        'BMO 2': 'bmo-2',
        'BMO 3': 'bmo-3',
        'SMO': 'smo',
        'LMO': 'lmo',
        'GMO': 'gmo',
        'Marine': 'marine'
    };
    
    // Column definitions for visibility toggle (ALL columns from database)
    const columnDefinitions = [
        { index: 0, name: 'Task Number', key: 'task_number', defaultVisible: true },
        { index: 1, name: 'Jenis Laporan', key: 'jenis_laporan', defaultVisible: false },
        { index: 2, name: 'Aktivitas Pekerjaan', key: 'aktivitas_pekerjaan', defaultVisible: false },
        { index: 3, name: 'Lokasi', key: 'lokasi', defaultVisible: false },
        { index: 4, name: 'Detail Lokasi', key: 'detail_lokasi', defaultVisible: false },
        { index: 5, name: 'Keterangan', key: 'keterangan', defaultVisible: true },
        { index: 6, name: 'Tanggal Pelaporan', key: 'tanggal_pelaporan', defaultVisible: false },
        { index: 7, name: 'Perusahaan Pelapor', key: 'perusahaan_pelapor', defaultVisible: false },
        { index: 8, name: 'Pelapor', key: 'pelapor', defaultVisible: false },
        { index: 9, name: 'SID Pelapor', key: 'sid_pelapor', defaultVisible: false },
        { index: 10, name: 'Jabatan Fungsional Pelapor', key: 'jabatan_fungsional_pelapor', defaultVisible: false },
        { index: 11, name: 'Departemen Pelapor', key: 'departemen_pelapor', defaultVisible: false },
        { index: 12, name: 'PIC', key: 'pic', defaultVisible: false },
        { index: 13, name: 'SID PIC', key: 'sid_pic', defaultVisible: false },
        { index: 14, name: 'Jabatan Fungsional PIC', key: 'jabatan_fungsional_pic', defaultVisible: false },
        { index: 15, name: 'Perusahaan PIC', key: 'perusahaan_pic', defaultVisible: false },
        { index: 16, name: 'Departemen PIC', key: 'departemen_pic', defaultVisible: false },
        { index: 17, name: 'Foto Temuan', key: 'foto_temuan', defaultVisible: true },
        { index: 18, name: 'Foto Penyelesaian', key: 'foto_penyelesaian', defaultVisible: true },
        { index: 19, name: 'Tools Pengawasan', key: 'tools_pengawasan', defaultVisible: false },
        { index: 20, name: 'Catatan Tindakan', key: 'catatan_tindakan', defaultVisible: false },
        { index: 21, name: 'NIK Pelapor', key: 'nik_pelapor', defaultVisible: false },
        { index: 22, name: 'Nama Pelapor', key: 'nama_pelapor', defaultVisible: false },
        { index: 23, name: 'Nama Perusahaan Karyawan', key: 'nama_perusahaan_pelapor_karyawan', defaultVisible: false },
        { index: 24, name: 'Jabatan Fungsional Karyawan', key: 'jabatan_fungsional_karyawan_pelapor', defaultVisible: false },
        { index: 25, name: 'Latitude', key: 'latitude', defaultVisible: false },
        { index: 26, name: 'Longitude', key: 'longitude', defaultVisible: false },
        { index: 27, name: 'Site', key: 'site', defaultVisible: false },
        { index: 28, name: 'Keterangan Lokasi', key: 'keterangan_lokasi', defaultVisible: false },
        { index: 29, name: 'Jam', key: 'jam', defaultVisible: false },
        { index: 30, name: 'Menit', key: 'menit', defaultVisible: false },
        { index: 31, name: 'Nama Lokasi', key: 'nama_lokasi', defaultVisible: false },
        { index: 32, name: 'Nama Detail Lokasi', key: 'nama_detail_lokasi', defaultVisible: false },
        { index: 33, name: 'Validasi BY AI', key: 'ai_match_found', defaultVisible: true },
        { index: 34, name: 'Klasifikasi BY AI', key: 'ai_main_category', defaultVisible: true },
        { index: 35, name: 'AI Sub Category', key: 'ai_sub_category', defaultVisible: false },
        { index: 36, name: 'AI TBC', key: 'ai_tbc', defaultVisible: false },
        { index: 37, name: 'AI PSPP', key: 'ai_pspp', defaultVisible: false },
        { index: 38, name: 'AI GR', key: 'ai_gr', defaultVisible: false },
        { index: 39, name: 'AI Incident', key: 'ai_incident', defaultVisible: false },
        { index: 40, name: 'AI Justification', key: 'ai_justification', defaultVisible: true },
        { index: 41, name: 'AI Confidence Score', key: 'ai_confidence_score', defaultVisible: false },
        { index: 42, name: 'Tanggal Validasi', key: 'validation_date', defaultVisible: false },
        { index: 43, name: 'Validated By', key: 'validated_by', defaultVisible: false },
        { index: 44, name: 'TBC', key: 'tbc', defaultVisible: true },
        { index: 45, name: 'GR', key: 'gr', defaultVisible: true },
        { index: 46, name: 'Catatan', key: 'catatan', defaultVisible: true }
    ];
    
    // LocalStorage key prefix - Version 3 to invalidate old preferences (added Foto Penyelesaian column)
    const STORAGE_KEY_PREFIX = 'hse_ai_validation_columns_v3_';
    
    // Clear old localStorage keys on first load
    (function clearOldStorage() {
        const oldPrefixes = ['hse_ai_validation_columns_', 'hse_ai_validation_columns_v2_'];
        Object.keys(localStorage).forEach(key => {
            for (const oldPrefix of oldPrefixes) {
                if (key.startsWith(oldPrefix) && !key.startsWith(STORAGE_KEY_PREFIX)) {
                    localStorage.removeItem(key);
                    break;
                }
            }
        });
    })();
    
    // Get column visibility preferences from localStorage
    function getColumnPreferences(siteKey) {
        const storageKey = STORAGE_KEY_PREFIX + siteMap[siteKey];
        const stored = localStorage.getItem(storageKey);
        if (stored) {
            try {
                const prefs = JSON.parse(stored);
                // Validate that preferences have correct number of columns
                if (Object.keys(prefs).length !== columnDefinitions.length) {
                    localStorage.removeItem(storageKey);
                    return null;
                }
                return prefs;
            } catch (e) {
                return null;
            }
        }
        return null;
    }
    
    // Save column visibility preferences to localStorage
    function saveColumnPreferences(siteKey, preferences) {
        const storageKey = STORAGE_KEY_PREFIX + siteMap[siteKey];
        localStorage.setItem(storageKey, JSON.stringify(preferences));
    }
    
    // Get current column visibility state
    function getCurrentColumnState(siteKey) {
        const table = dataTableInstances[siteKey];
        if (!table) return {};
        
        const state = {};
        columnDefinitions.forEach(col => {
            state[col.key] = table.column(col.index).visible();
        });
        return state;
    }
    
    // Column groups for better organization
    const columnGroups = [
        { name: 'Informasi Dasar', start: 0, end: 6 },
        { name: 'Informasi Pelapor', start: 7, end: 11 },
        { name: 'Informasi PIC', start: 12, end: 16 },
        { name: 'Media & Tools', start: 17, end: 20 },
        { name: 'Detail Pelapor', start: 21, end: 24 },
        { name: 'Lokasi & Waktu', start: 25, end: 32 },
        { name: 'Analisis AI', start: 33, end: 43 },
        { name: 'Input Validasi', start: 44, end: 46 }
    ];

    // Populate column visibility list
    function populateColumnList(siteKey) {
        const containerId = siteMap[siteKey];
        const listContainer = document.getElementById('columnList-' + containerId);
        if (!listContainer) return;
        
        const table = dataTableInstances[siteKey];
        if (!table) return;
        
        let html = '';
        let visibleCount = 0;
        
        columnGroups.forEach(group => {
            html += `<div class="column-group-title">${group.name}</div>`;
            
            for (let i = group.start; i <= group.end; i++) {
                const col = columnDefinitions[i];
                if (!col) continue;
                
                const isVisible = table.column(col.index).visible();
                if (isVisible) visibleCount++;
                
                const statusClass = isVisible ? 'visible' : 'hidden';
                const statusText = isVisible ? 'Tampil' : 'Tersembunyi';
                const itemClass = isVisible ? '' : 'hidden-column';
                
                html += `
                    <div class="column-visibility-item ${itemClass}" data-column-index="${col.index}" data-site-key="${siteKey}">
                        <div class="d-flex align-items-center">
                            <span class="column-name">${col.name}</span>
                            <span class="column-visibility-status ${statusClass}">${statusText}</span>
                        </div>
                        <div class="column-actions">
                            <button type="button" class="btn btn-toggle btn-show" onclick="event.stopPropagation(); toggleColumn('${siteKey}', ${col.index}, true)" title="Tampilkan" ${isVisible ? 'disabled style="opacity:0.5"' : ''}>
                                <i class="material-icons-outlined" style="font-size: 16px;">add</i>
                            </button>
                            <button type="button" class="btn btn-toggle btn-hide" onclick="event.stopPropagation(); toggleColumn('${siteKey}', ${col.index}, false)" title="Sembunyikan" ${!isVisible ? 'disabled style="opacity:0.5"' : ''}>
                                <i class="material-icons-outlined" style="font-size: 16px;">remove</i>
                            </button>
                        </div>
                    </div>
                `;
            }
        });
        
        listContainer.innerHTML = html;
        
        // Update column count badge on button
        updateColumnCountBadge(siteKey, visibleCount);
    }
    
    // Update column count badge
    function updateColumnCountBadge(siteKey, visibleCount) {
        const containerId = siteMap[siteKey];
        const toggleBtn = document.getElementById('columnToggle-' + containerId);
        if (toggleBtn) {
            // Remove existing badge
            const existingBadge = toggleBtn.querySelector('.column-count-badge');
            if (existingBadge) existingBadge.remove();
            
            // Add new badge
            const badge = document.createElement('span');
            badge.className = 'column-count-badge';
            badge.textContent = visibleCount + '/' + columnDefinitions.length;
            toggleBtn.appendChild(badge);
        }
    }
    
    // Toggle single column visibility
    function toggleColumn(siteKey, columnIndex, show) {
        const table = dataTableInstances[siteKey];
        if (!table) return;
        
        // Toggle column visibility
        table.column(columnIndex).visible(show);
        
        // Adjust columns after visibility change
        setTimeout(function() {
            table.columns.adjust().draw(false);
        }, 50);
        
        // Save preferences
        saveColumnPreferences(siteKey, getCurrentColumnState(siteKey));
        
        // Refresh the column list UI
        populateColumnList(siteKey);
    }
    
    // Show all columns
    function showAllColumns(siteKey) {
        const table = dataTableInstances[siteKey];
        if (!table) return;
        
        columnDefinitions.forEach(col => {
            table.column(col.index).visible(true);
        });
        
        // Adjust columns after visibility change
        setTimeout(function() {
            table.columns.adjust().draw(false);
        }, 50);
        
        // Save preferences
        saveColumnPreferences(siteKey, getCurrentColumnState(siteKey));
        
        // Refresh the column list UI
        populateColumnList(siteKey);
    }
    
    // Reset columns to default visibility
    function hideAllColumns(siteKey) {
        const table = dataTableInstances[siteKey];
        if (!table) return;
        
        // Reset all columns to their default visibility
        columnDefinitions.forEach(col => {
            table.column(col.index).visible(col.defaultVisible);
        });
        
        // Adjust columns after visibility change
        setTimeout(function() {
            table.columns.adjust().draw(false);
        }, 50);
        
        // Clear saved preferences (reset to default)
        const storageKey = STORAGE_KEY_PREFIX + siteMap[siteKey];
        localStorage.removeItem(storageKey);
        
        // Refresh the column list UI
        populateColumnList(siteKey);
    }
    
    // Apply saved column preferences
    function applyColumnPreferences(siteKey) {
        const preferences = getColumnPreferences(siteKey);
        const table = dataTableInstances[siteKey];
        if (!table) return;
        
        columnDefinitions.forEach(col => {
            if (preferences && preferences.hasOwnProperty(col.key)) {
                // Use saved preference
                table.column(col.index).visible(preferences[col.key]);
            }
            // If no preferences saved, column will use defaultVisible from DataTable init
        });
    }

    // Initialize DataTable for a site
    function initDataTable(siteKey) {
        const containerId = siteMap[siteKey];
        const tableId = '#dataTable-' + containerId;
        
        // Destroy existing instance if any
        if (dataTableInstances[siteKey]) {
            dataTableInstances[siteKey].destroy();
        }

        // Initialize DataTable
        dataTableInstances[siteKey] = $(tableId).DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: `{{ url('hse-ai-validation/data') }}/${containerId}`,
                dataSrc: function(json) {
                    // Store tooltip data and create index map
                    if (json.tooltipData && json.data) {
                        tooltipDataStore[siteKey] = {};
                        json.data.forEach(function(row, index) {
                            if (row.id && json.tooltipData[index]) {
                                tooltipDataStore[siteKey][row.id] = json.tooltipData[index];
                            }
                        });
                    }
                    return json.data;
                }
            },
            columns: [
                // 0: Task Number
                {
                    data: 'task_number',
                    name: 'task_number',
                    title: 'Task Number',
                    visible: columnDefinitions[0].defaultVisible,
                    render: function(data, type, row) {
                        return '<span class="task-number-cell" data-row-id="' + row.id + '">' + (data || '') + '</span>';
                    }
                },
                // 1: Jenis Laporan
                { data: 'jenis_laporan', name: 'jenis_laporan', title: 'Jenis Laporan', visible: columnDefinitions[1].defaultVisible },
                // 2: Aktivitas Pekerjaan
                { data: 'aktivitas_pekerjaan', name: 'aktivitas_pekerjaan', title: 'Aktivitas Pekerjaan', visible: columnDefinitions[2].defaultVisible },
                // 3: Lokasi
                { data: 'lokasi', name: 'lokasi', title: 'Lokasi', visible: columnDefinitions[3].defaultVisible },
                // 4: Detail Lokasi
                { data: 'detail_lokasi', name: 'detail_lokasi', title: 'Detail Lokasi', visible: columnDefinitions[4].defaultVisible },
                // 5: Keterangan
                { data: 'keterangan', name: 'keterangan', title: 'Keterangan', visible: columnDefinitions[5].defaultVisible, className: 'col-keterangan' },
                // 6: Tanggal Pelaporan
                { data: 'tanggal_pelaporan', name: 'tanggal_pelaporan', title: 'Tanggal Pelaporan', visible: columnDefinitions[6].defaultVisible },
                // 7: Perusahaan Pelapor
                { data: 'perusahaan_pelapor', name: 'perusahaan_pelapor', title: 'Perusahaan Pelapor', visible: columnDefinitions[7].defaultVisible },
                // 8: Pelapor
                { data: 'pelapor', name: 'pelapor', title: 'Pelapor', visible: columnDefinitions[8].defaultVisible },
                // 9: SID Pelapor
                { data: 'sid_pelapor', name: 'sid_pelapor', title: 'SID Pelapor', visible: columnDefinitions[9].defaultVisible },
                // 10: Jabatan Fungsional Pelapor
                { data: 'jabatan_fungsional_pelapor', name: 'jabatan_fungsional_pelapor', title: 'Jabatan Fungsional Pelapor', visible: columnDefinitions[10].defaultVisible },
                // 11: Departemen Pelapor
                { data: 'departemen_pelapor', name: 'departemen_pelapor', title: 'Departemen Pelapor', visible: columnDefinitions[11].defaultVisible },
                // 12: PIC
                { data: 'pic', name: 'pic', title: 'PIC', visible: columnDefinitions[12].defaultVisible },
                // 13: SID PIC
                { data: 'sid_pic', name: 'sid_pic', title: 'SID PIC', visible: columnDefinitions[13].defaultVisible },
                // 14: Jabatan Fungsional PIC
                { data: 'jabatan_fungsional_pic', name: 'jabatan_fungsional_pic', title: 'Jabatan Fungsional PIC', visible: columnDefinitions[14].defaultVisible },
                // 15: Perusahaan PIC
                { data: 'perusahaan_pic', name: 'perusahaan_pic', title: 'Perusahaan PIC', visible: columnDefinitions[15].defaultVisible },
                // 16: Departemen PIC
                { data: 'departemen_pic', name: 'departemen_pic', title: 'Departemen PIC', visible: columnDefinitions[16].defaultVisible },
                // 17: Foto Temuan
                {
                    data: 'foto_temuan',
                    name: 'foto_temuan',
                    title: 'Foto Temuan',
                    orderable: false,
                    searchable: false,
                    visible: columnDefinitions[17].defaultVisible,
                    render: function(data, type, row) {
                        if (data && data.trim() !== '') {
                            return '<div class="photo-cell"><img src="' + data + '" alt="Foto Temuan" onclick="window.open(\'' + data + '\', \'_blank\')" onerror="this.parentElement.innerHTML=\'<span style=\\\'color: #999;\\\'>Gambar tidak dapat dimuat</span>\'" /></div>';
                        }
                        return '<span style="color: #999;">Tidak ada foto</span>';
                    }
                },
                // 18: Foto Penyelesaian
                {
                    data: 'foto_penyelesaian',
                    name: 'foto_penyelesaian',
                    title: 'Foto Penyelesaian',
                    orderable: false,
                    searchable: false,
                    visible: columnDefinitions[18].defaultVisible,
                    render: function(data, type, row) {
                        if (data && data.trim() !== '') {
                            return '<div class="photo-cell"><img src="' + data + '" alt="Foto Penyelesaian" onclick="window.open(\'' + data + '\', \'_blank\')" onerror="this.parentElement.innerHTML=\'<span style=\\\'color: #999;\\\'>Gambar tidak dapat dimuat</span>\'" /></div>';
                        }
                        return '<span style="color: #999;">Tidak ada foto</span>';
                    }
                },
                // 19: Tools Pengawasan
                { data: 'tools_pengawasan', name: 'tools_pengawasan', title: 'Tools Pengawasan', visible: columnDefinitions[19].defaultVisible },
                // 20: Catatan Tindakan
                { data: 'catatan_tindakan', name: 'catatan_tindakan', title: 'Catatan Tindakan', visible: columnDefinitions[20].defaultVisible },
                // 21: NIK Pelapor
                { data: 'nik_pelapor', name: 'nik_pelapor', title: 'NIK Pelapor', visible: columnDefinitions[21].defaultVisible },
                // 22: Nama Pelapor
                { data: 'nama_pelapor', name: 'nama_pelapor', title: 'Nama Pelapor', visible: columnDefinitions[22].defaultVisible },
                // 23: Nama Perusahaan Karyawan
                { data: 'nama_perusahaan_pelapor_karyawan', name: 'nama_perusahaan_pelapor_karyawan', title: 'Nama Perusahaan Karyawan', visible: columnDefinitions[23].defaultVisible },
                // 24: Jabatan Fungsional Karyawan
                { data: 'jabatan_fungsional_karyawan_pelapor', name: 'jabatan_fungsional_karyawan_pelapor', title: 'Jabatan Fungsional Karyawan', visible: columnDefinitions[24].defaultVisible },
                // 25: Latitude
                { data: 'latitude', name: 'latitude', title: 'Latitude', visible: columnDefinitions[25].defaultVisible },
                // 26: Longitude
                { data: 'longitude', name: 'longitude', title: 'Longitude', visible: columnDefinitions[26].defaultVisible },
                // 27: Site
                { data: 'site', name: 'site', title: 'Site', visible: columnDefinitions[27].defaultVisible },
                // 28: Keterangan Lokasi
                { data: 'keterangan_lokasi', name: 'keterangan_lokasi', title: 'Keterangan Lokasi', visible: columnDefinitions[28].defaultVisible },
                // 29: Jam
                { data: 'jam', name: 'jam', title: 'Jam', visible: columnDefinitions[29].defaultVisible },
                // 30: Menit
                { data: 'menit', name: 'menit', title: 'Menit', visible: columnDefinitions[30].defaultVisible },
                // 31: Nama Lokasi
                { data: 'nama_lokasi', name: 'nama_lokasi', title: 'Nama Lokasi', visible: columnDefinitions[31].defaultVisible },
                // 32: Nama Detail Lokasi
                { data: 'nama_detail_lokasi', name: 'nama_detail_lokasi', title: 'Nama Detail Lokasi', visible: columnDefinitions[32].defaultVisible },
                // 33: Validasi BY AI (ai_match_found)
                { 
                    data: 'ai_match_found', 
                    name: 'ai_match_found', 
                    title: 'Validasi BY AI', 
                    visible: columnDefinitions[33].defaultVisible,
                    render: function(data, type, row) {
                        if (data == 1 || data === '1' || data === true) {
                            return '<span class="badge bg-success">Valid</span>';
                        } else if (data == 0 || data === '0' || data === false) {
                            return '<span class="badge bg-danger">Tidak Valid</span>';
                        }
                        return data || '-';
                    }
                },
                // 34: Klasifikasi BY AI (ai_main_category)
                { data: 'ai_main_category', name: 'ai_main_category', title: 'Klasifikasi BY AI', visible: columnDefinitions[34].defaultVisible },
                // 35: AI Sub Category
                { data: 'ai_sub_category', name: 'ai_sub_category', title: 'AI Sub Category', visible: columnDefinitions[35].defaultVisible, className: 'col-ai-subcategory' },
                // 36: AI TBC
                { 
                    data: 'ai_tbc', 
                    name: 'ai_tbc', 
                    title: 'AI TBC', 
                    visible: columnDefinitions[36].defaultVisible,
                    render: function(data, type, row) {
                        if (data == 1 || data === '1') return '<span class="badge bg-warning text-dark">TBC</span>';
                        return '<span class="badge bg-secondary">-</span>';
                    }
                },
                // 37: AI PSPP
                { 
                    data: 'ai_pspp', 
                    name: 'ai_pspp', 
                    title: 'AI PSPP', 
                    visible: columnDefinitions[37].defaultVisible,
                    render: function(data, type, row) {
                        if (data == 1 || data === '1') return '<span class="badge bg-info">PSPP</span>';
                        return '<span class="badge bg-secondary">-</span>';
                    }
                },
                // 38: AI GR
                { 
                    data: 'ai_gr', 
                    name: 'ai_gr', 
                    title: 'AI GR', 
                    visible: columnDefinitions[38].defaultVisible,
                    render: function(data, type, row) {
                        if (data == 1 || data === '1') return '<span class="badge bg-danger">GR</span>';
                        return '<span class="badge bg-secondary">-</span>';
                    }
                },
                // 39: AI Incident
                { 
                    data: 'ai_incident', 
                    name: 'ai_incident', 
                    title: 'AI Incident', 
                    visible: columnDefinitions[39].defaultVisible,
                    render: function(data, type, row) {
                        if (data == 1 || data === '1') return '<span class="badge bg-dark">Incident</span>';
                        return '<span class="badge bg-secondary">-</span>';
                    }
                },
                // 40: AI Justification
                { data: 'ai_justification', name: 'ai_justification', title: 'AI Justification', visible: columnDefinitions[40].defaultVisible, className: 'col-ai-justification' },
                // 41: AI Confidence Score
                { 
                    data: 'ai_confidence_score', 
                    name: 'ai_confidence_score', 
                    title: 'AI Confidence Score', 
                    visible: columnDefinitions[41].defaultVisible,
                    render: function(data, type, row) {
                        if (data) {
                            // Handle both comma and period as decimal separator
                            const scoreStr = String(data).replace(',', '.');
                            const score = parseFloat(scoreStr);
                            if (isNaN(score)) return data;
                            let badgeClass = 'bg-danger';
                            if (score >= 0.9) badgeClass = 'bg-success';
                            else if (score >= 0.7) badgeClass = 'bg-warning text-dark';
                            else if (score >= 0.5) badgeClass = 'bg-info';
                            return '<span class="badge ' + badgeClass + '">' + (score * 100).toFixed(0) + '%</span>';
                        }
                        return '-';
                    }
                },
                // 42: Tanggal Validasi
                { data: 'validation_date', name: 'validation_date', title: 'Tanggal Validasi', visible: columnDefinitions[42].defaultVisible },
                // 43: Validated By
                { data: 'validated_by', name: 'validated_by', title: 'Validated By', visible: columnDefinitions[43].defaultVisible },
                // 44: TBC (Input)
                {
                    data: 'tbc',
                    name: 'tbc',
                    title: 'Validasi TBC BY Evaluator',
                    orderable: false,
                    visible: columnDefinitions[44].defaultVisible,
                    render: function(data, type, row) {
                        const options = ['', 'Valid', 'Invalid'];
                        let select = '<select class="form-select form-select-sm select-cell tbc-select" data-id="' + row.id + '">';
                        options.forEach(function(option) {
                            const selected = (data === option) ? 'selected' : '';
                            select += '<option value="' + option + '" ' + selected + '>' + (option || '-') + '</option>';
                        });
                        select += '</select>';
                        return select;
                    }
                },
                // 45: GR (Input)
                {
                    data: 'gr',
                    name: 'gr',
                    title: 'GR',
                    orderable: false,
                    visible: columnDefinitions[45].defaultVisible,
                    render: function(data, type, row) {
                        const options = ['', 'Valid', 'Potential', 'Invalid', 'NonGrRelated'];
                        let select = '<select class="form-select form-select-sm select-cell gr-select" data-id="' + row.id + '">';
                        options.forEach(function(option) {
                            const selected = (data === option) ? 'selected' : '';
                            select += '<option value="' + option + '" ' + selected + '>' + (option || '-') + '</option>';
                        });
                        select += '</select>';
                        return select;
                    }
                },
                // 46: Catatan (Input)
                {
                    data: 'catatan',
                    name: 'catatan',
                    title: 'Catatan',
                    orderable: false,
                    visible: columnDefinitions[46].defaultVisible,
                    className: 'col-catatan',
                    render: function(data, type, row) {
                        const options = [
                            '',
                            'Deviasi pengoperasian kendaraan/unit',
                            'Deviasi penggunaan APD',
                            'Geotech & Hydrology',
                            'Posisi Pekerja pada Area Tidak aman/Tidak Sesuai Prosedur',
                            'Deviasi Loading/Dumping',
                            'Tidak terdapat pengawas/pengawas tidak memadai',
                            'LOTO',
                            'Deviasi Road Management',
                            'Kesesuaian Dokumen Kerja',
                            'Tools Tidak Standard / Penggunaan Tools Tidak Tepat',
                            'Bahaya Elektrikal',
                            'Bahaya Biologis',
                            'Aktivitas Drill and blast',
                            'Technology'
                        ];
                        let select = '<select class="form-select form-select-sm select-cell catatan-select" data-id="' + row.id + '">';
                        options.forEach(function(option, index) {
                            const selected = (data === option) ? 'selected' : '';
                            const displayText = option || '-';
                            select += '<option value="' + option + '" ' + selected + '>' + displayText + '</option>';
                        });
                        select += '</select>';
                        return select;
                    }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            scrollX: true,
            scrollCollapse: false,
            autoWidth: false,
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
            drawCallback: function() {
                // Attach tooltip listeners after table is drawn
                attachTooltipListeners(siteKey);
                
                // Adjust columns after draw
                setTimeout(function() {
                    if (dataTableInstances[siteKey]) {
                        dataTableInstances[siteKey].columns.adjust();
                    }
                }, 100);
            },
            initComplete: function() {
                // Apply saved column preferences after table is fully initialized
                applyColumnPreferences(siteKey);
                
                // Populate the column visibility list
                populateColumnList(siteKey);
                
                // Adjust column widths
                this.api().columns.adjust();
            }
        });

        // Handle select changes
        $(tableId).on('change', '.tbc-select', function() {
            const id = $(this).data('id');
            const value = $(this).val();
            updateField(id, 'tbc', value);
        });

        $(tableId).on('change', '.gr-select', function() {
            const id = $(this).data('id');
            const value = $(this).val();
            updateField(id, 'gr', value);
        });

        $(tableId).on('change', '.catatan-select', function() {
            const id = $(this).data('id');
            const value = $(this).val();
            updateField(id, 'catatan', value);
        });
    }

    // Load data for a specific site
    function loadSiteData(siteKey) {
        if (dataTableInstances[siteKey]) {
            dataTableInstances[siteKey].ajax.reload();
        }
    }

    // Update field function
    function updateField(id, field, value) {
        // Show loading toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
        
        $.ajax({
            url: `{{ url('hse-ai-validation/update') }}/${id}`,
            type: 'PUT',
            data: {
                [field]: value,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Data berhasil diperbarui'
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: response.message || 'Terjadi kesalahan'
                    });
                }
            },
            error: function(xhr) {
                console.error('Error updating data:', xhr.responseJSON);
                const errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat memperbarui data';
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: errorMessage,
                    confirmButtonColor: '#3b82f6'
                });
            }
        });
    }

    // Attach tooltip listeners to Task Number cells
    function attachTooltipListeners(siteKey) {
        if (!tooltipDataStore[siteKey]) {
            return;
        }

        const containerId = siteMap[siteKey];
        const tableId = '#dataTable-' + containerId;
        
        // Remove existing listeners
        $(tableId + ' .task-number-cell').off('mouseenter mouseleave');
        
        // Attach new listeners
        $(tableId + ' .task-number-cell').on('mouseenter', function(e) {
            const rowId = $(this).data('row-id');
            const tooltipData = tooltipDataStore[siteKey][rowId];
            
            if (tooltipData) {
                showTooltip(e, tooltipData, siteKey);
            }
        }).on('mouseleave', function() {
            hideTooltip();
        });
    }
    
    // Tooltip functions
    let currentTooltip = null;
    
    function showTooltip(event, data, siteKey) {
        // Remove existing tooltip
        hideTooltip();
        
        // Create tooltip element
        const tooltip = document.createElement('div');
        tooltip.className = 'inspection-tooltip';
        
        // Build tooltip content
        let html = '<div class="tooltip-header">Detail Inspeksi Hazard</div>';
        
        // Basic Information Section
        html += '<div class="tooltip-section">';
        html += '<div class="tooltip-section-title">Informasi Dasar</div>';
        html += formatTooltipRow('Task Number', data.task_number);
        html += formatTooltipRow('Jenis Laporan', data.jenis_laporan);
        html += formatTooltipRow('Aktivitas Pekerjaan', data.aktivitas_pekerjaan);
        html += formatTooltipRow('Tanggal Pelaporan', data.tanggal_pelaporan);
        html += formatTooltipRow('Site', data.site);
        html += '</div>';
        
        // Location Information
        html += '<div class="tooltip-section">';
        html += '<div class="tooltip-section-title">Informasi Lokasi</div>';
        html += formatTooltipRow('Lokasi', data.lokasi);
        html += formatTooltipRow('Detail Lokasi', data.detail_lokasi);
        html += formatTooltipRow('Nama Lokasi', data.nama_lokasi);
        html += formatTooltipRow('Nama Detail Lokasi', data.nama_detail_lokasi);
        html += formatTooltipRow('Keterangan Lokasi', data.keterangan_lokasi);
        if (data.latitude && data.longitude) {
            html += formatTooltipRow('Koordinat', `${data.latitude}, ${data.longitude}`);
        }
        html += '</div>';
        
        // Reporter Information
        html += '<div class="tooltip-section">';
        html += '<div class="tooltip-section-title">Informasi Pelapor</div>';
        html += formatTooltipRow('Pelapor', data.pelapor);
        html += formatTooltipRow('NIK Pelapor', data.nik_pelapor);
        html += formatTooltipRow('Nama Pelapor', data.nama_pelapor);
        html += formatTooltipRow('SID Pelapor', data.sid_pelapor);
        html += formatTooltipRow('Jabatan Fungsional', data.jabatan_fungsional_pelapor);
        html += formatTooltipRow('Departemen', data.departemen_pelapor);
        html += formatTooltipRow('Perusahaan', data.perusahaan_pelapor);
        html += formatTooltipRow('Perusahaan Karyawan', data.nama_perusahaan_pelapor_karyawan);
        html += '</div>';
        
        // PIC Information
        html += '<div class="tooltip-section">';
        html += '<div class="tooltip-section-title">Informasi PIC</div>';
        html += formatTooltipRow('PIC', data.pic);
        html += formatTooltipRow('SID PIC', data.sid_pic);
        html += formatTooltipRow('Jabatan Fungsional', data.jabatan_fungsional_pic);
        html += formatTooltipRow('Perusahaan', data.perusahaan_pic);
        html += formatTooltipRow('Departemen', data.departemen_pic);
        html += '</div>';
        
        // Description and Notes
        html += '<div class="tooltip-section">';
        html += '<div class="tooltip-section-title">Keterangan & Catatan</div>';
        html += formatTooltipRow('Keterangan', data.keterangan);
        html += formatTooltipRow('Tools Pengawasan', data.tools_pengawasan);
        html += formatTooltipRow('Catatan Tindakan', data.catatan_tindakan);
        html += '</div>';
        
        // AI Analysis Section
        html += '<div class="tooltip-section">';
        html += '<div class="tooltip-section-title">Analisis AI</div>';
        html += formatTooltipRow('AI Sub Category', data.ai_sub_category);
        html += formatTooltipRow('AI TBC', data.ai_tbc);
        html += formatTooltipRow('AI PSPP', data.ai_pspp);
        html += formatTooltipRow('AI GR', data.ai_gr);
        html += formatTooltipRow('AI Incident', data.ai_incident);
        html += formatTooltipRow('AI Confidence Score', data.ai_confidence_score);
        html += '</div>';
        
        // Validation Information
        if (data.validation_date || data.validated_by) {
            html += '<div class="tooltip-section">';
            html += '<div class="tooltip-section-title">Informasi Validasi</div>';
            html += formatTooltipRow('Tanggal Validasi', data.validation_date);
            html += formatTooltipRow('Validated By', data.validated_by);
            html += '</div>';
        }
        
        tooltip.innerHTML = html;
        document.body.appendChild(tooltip);
        currentTooltip = tooltip;
        
        // Position tooltip
        positionTooltip(event, tooltip);
    }
    
    function formatTooltipRow(label, value) {
        if (!value || value === '' || value === 'null' || value === 'undefined') {
            return '';
        }
        return `<div class="tooltip-row">
            <span class="tooltip-label">${label}:</span>
            <span class="tooltip-value">${value}</span>
        </div>`;
    }
    
    function positionTooltip(event, tooltip) {
        // Use clientX/clientY for fixed positioning (viewport-relative)
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        
        let left = (event.clientX || 0) + 15;
        let top = (event.clientY || 0) + 15;
        
        // Wait for tooltip to be rendered to get its dimensions
        setTimeout(function() {
            const rect = tooltip.getBoundingClientRect();
            
            // Adjust if tooltip goes off right edge
            if (left + rect.width > viewportWidth) {
                left = (event.clientX || 0) - rect.width - 15;
            }
            
            // Adjust if tooltip goes off bottom edge
            if (top + rect.height > viewportHeight) {
                top = (event.clientY || 0) - rect.height - 15;
            }
            
            // Ensure tooltip doesn't go off left or top edge
            if (left < 0) left = 15;
            if (top < 0) top = 15;
            
            tooltip.style.left = left + 'px';
            tooltip.style.top = top + 'px';
        }, 0);
        
        // Set initial position
        tooltip.style.left = left + 'px';
        tooltip.style.top = top + 'px';
    }
    
    function hideTooltip() {
        if (currentTooltip) {
            currentTooltip.remove();
            currentTooltip = null;
        }
    }
    
    // Hide tooltip when mouse leaves table area
    $(document).on('mouseout', function(e) {
        if (currentTooltip && !currentTooltip.contains(e.relatedTarget)) {
            const target = $(e.target);
            if (!target.closest('.dataTables_wrapper').length && !target.closest('.inspection-tooltip').length) {
                hideTooltip();
            }
        }
    });

    // Initialize when page loads
    $(document).ready(function() {
        // Initialize first tab
        const firstSite = Object.keys(siteMap)[0];
        initDataTable(firstSite);

        // Initialize other tabs when clicked
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const siteKey = $(this).data('site');
            if (siteKey && !dataTableInstances[siteKey]) {
                initDataTable(siteKey);
            }
        });
    });
</script>
@endsection
