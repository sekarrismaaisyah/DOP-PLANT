@extends('layouts.master')

@section('title', 'Dashboard CCTV')
@section('content')
<x-page-title title="Dashboard CCTV" pagetitle="Statistik & Analisis CCTV" />

<style>
    .dashboard-card {
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        background: #ffffff;
        overflow: hidden;
        position: relative;
    }
    .dashboard-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .card-header-custom {
        padding: 20px 24px;
        border-bottom: 1px solid #f3f4f6;
        background: #ffffff;
    }
    .card-body-custom {
        padding: 24px;
    }
    .stat-value-large {
        font-size: 36px;
        font-weight: 700;
        color: #111827;
        margin: 12px 0;
        line-height: 1.2;
    }
    .stat-label {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 8px;
        font-weight: 500;
    }
    .stat-change {
        font-size: 13px;
        margin-top: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .stat-change.positive {
        color: #10b981;
    }
    .stat-change.negative {
        color: #ef4444;
    }
    .chart-container {
        position: relative;
        height: 200px;
        margin-top: 16px;
    }
    .chart-container-large {
        position: relative;
        height: 280px;
        margin-top: 16px;
    }
    .progress-bar-custom {
        height: 10px;
        border-radius: 8px;
        background: #f3f4f6;
        overflow: hidden;
        margin-top: 12px;
    }
    .progress-fill {
        height: 100%;
        border-radius: 8px;
        transition: width 0.3s ease;
    }
    .device-breakdown {
        display: flex;
        gap: 12px;
        margin-top: 16px;
    }
    .device-item {
        flex: 1;
        padding: 12px;
        border-radius: 12px;
        background: #f9fafb;
        text-align: center;
    }
    .device-item .device-icon {
        font-size: 24px;
        margin-bottom: 8px;
    }
    .device-item .device-value {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
    }
    .device-item .device-label {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }
    .revenue-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .revenue-item:last-child {
        border-bottom: none;
    }
    .revenue-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .revenue-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }
    .revenue-details h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }
    .revenue-tag {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 500;
        margin-top: 4px;
    }
    .revenue-amount {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
    }
    .transaction-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .transaction-item:last-child {
        border-bottom: none;
    }
    .transaction-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .transaction-logo {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 700;
        color: white;
    }
    .transaction-details h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }
    .transaction-details p {
        margin: 0;
        font-size: 12px;
        color: #6b7280;
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-paid {
        background: #d1fae5;
        color: #065f46;
    }
    .status-unpaid {
        background: #fee2e2;
        color: #991b1b;
    }
    .country-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .country-item:last-child {
        border-bottom: none;
    }
    .country-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .country-flag {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .country-name {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }
    .country-amount {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
    }
    .circular-progress {
        width: 180px;
        height: 180px;
        margin: 0 auto;
        position: relative;
    }
    .support-metric {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .support-metric:last-child {
        border-bottom: none;
    }
    .support-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .support-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
    }
    .expense-breakdown {
        display: flex;
        gap: 16px;
        margin-top: 20px;
    }
    .expense-item {
        flex: 1;
        text-align: center;
        padding: 12px;
        background: #f9fafb;
        border-radius: 12px;
    }
    .expense-item-label {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 4px;
    }
    .expense-item-value {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }
    .three-dots {
        position: absolute;
        top: 20px;
        right: 20px;
        cursor: pointer;
        color: #9ca3af;
    }
    .progress-segmented {
        display: flex;
        height: 10px;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 12px;
    }
    .progress-segment {
        height: 100%;
    }
</style>

<!-- Row 1: Main Stats -->
<div class="row g-3 mb-4">
    <!-- Last 6 months - Total CCTV -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-body-custom">
                <p class="stat-label mb-0">Last 6 months</p>
                <h2 class="stat-value-large mb-0">{{ number_format($stats['cctv']['total']) }}</h2>
                <p class="stat-change positive mb-0">
                    <i class="material-icons-outlined" style="font-size: 16px;">trending_up</i>
                    <span>Total CCTV Database</span>
                </p>
                <div class="chart-container">
                    <canvas id="totalCctvChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Traffic - Device View Status -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-body-custom">
                <p class="stat-label mb-0">72% Total traffic</p>
                <div class="progress-segmented">
                    <div class="progress-segment" style="width: 45%; background: #6366f1;"></div>
                    <div class="progress-segment" style="width: 30%; background: #10b981;"></div>
                    <div class="progress-segment" style="width: 25%; background: #f59e0b;"></div>
                </div>
                <div class="device-breakdown">
                    <div class="device-item">
                        <div class="device-icon" style="color: #6366f1;">📱</div>
                        <div class="device-value">{{ number_format($stats['cctv']['with_link']) }}</div>
                        <div class="device-label">With Link</div>
                    </div>
                    <div class="device-item">
                        <div class="device-icon" style="color: #10b981;">📊</div>
                        <div class="device-value">{{ number_format($stats['cctv']['live']) }}</div>
                        <div class="device-label">Live View</div>
                    </div>
                    <div class="device-item">
                        <div class="device-icon" style="color: #f59e0b;">📍</div>
                        <div class="device-value">{{ number_format($stats['cctv']['with_coordinates']) }}</div>
                        <div class="device-label">With Coord</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Expense -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-body-custom">
                <p class="stat-label mb-0">Total Coverage</p>
                <h2 class="stat-value-large mb-0">{{ number_format($stats['coverage']['total']) }}</h2>
                <p class="stat-label mb-0" style="font-size: 12px;">Coverage is {{ $stats['cctv']['total'] > 0 ? round(($stats['coverage']['total'] / $stats['cctv']['total']) * 100, 1) : 0 }}% of total CCTV</p>
                <div class="chart-container">
                    <canvas id="coverageDonutChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversion Rate -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-body-custom">
                <p class="stat-label mb-0">CCTV Baik Rate</p>
                <h2 class="stat-value-large mb-0">{{ $stats['cctv']['baik_percentage'] }}%</h2>
                <p class="stat-change positive mb-0">
                    <i class="material-icons-outlined" style="font-size: 16px;">trending_up</i>
                    <span>{{ $stats['cctv']['baik_percentage'] }}% increase</span>
                </p>
                <div class="progress-bar-custom">
                    <div class="progress-fill bg-success" style="width: {{ $stats['cctv']['baik_percentage'] }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Revenue Sources, Visitors, Customers, Sessions -->
<div class="row g-3 mb-4">
    <!-- Revenue Sources -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-header-custom">
                <h5 class="mb-0 fw-bold">CCTV Sources</h5>
            </div>
            <div class="card-body-custom">
                <div class="revenue-item">
                    <div class="revenue-info">
                        <div class="revenue-icon" style="background: #3b82f6;">
                            <i class="material-icons-outlined">link</i>
                        </div>
                        <div class="revenue-details">
                            <h6>PJA Dedicated</h6>
                            <span class="revenue-tag" style="background: #dbeafe; color: #1e40af;">PJA</span>
                        </div>
                    </div>
                    <div class="revenue-amount">{{ number_format($stats['pja_dedicated']['total']) }}</div>
                </div>
                <div class="revenue-item">
                    <div class="revenue-info">
                        <div class="revenue-icon" style="background: #10b981;">
                            <i class="material-icons-outlined">search</i>
                        </div>
                        <div class="revenue-details">
                            <h6>CCTV Coverage</h6>
                            <span class="revenue-tag" style="background: #d1fae5; color: #065f46;">Coverage</span>
                        </div>
                    </div>
                    <div class="revenue-amount">{{ number_format($stats['coverage']['total']) }}</div>
                </div>
                <div class="revenue-item">
                    <div class="revenue-info">
                        <div class="revenue-icon" style="background: #f59e0b;">
                            <i class="material-icons-outlined">people</i>
                        </div>
                        <div class="revenue-details">
                            <h6>Pengawas</h6>
                            <span class="revenue-tag" style="background: #fef3c7; color: #92400e;">Pengawas</span>
                        </div>
                    </div>
                    <div class="revenue-amount">{{ number_format($stats['pengawas']['total']) }}</div>
                </div>
                <div class="revenue-item">
                    <div class="revenue-info">
                        <div class="revenue-icon" style="background: #8b5cf6;">
                            <i class="material-icons-outlined">share</i>
                        </div>
                        <div class="revenue-details">
                            <h6>WMS Links</h6>
                            <span class="revenue-tag" style="background: #ede9fe; color: #5b21b6;">WMS</span>
                        </div>
                    </div>
                    <div class="revenue-amount">{{ number_format($stats['wms']['total']) }}</div>
                </div>
                <div class="revenue-item">
                    <div class="revenue-info">
                        <div class="revenue-icon" style="background: #6b7280;">
                            <i class="material-icons-outlined">map</i>
                        </div>
                        <div class="revenue-details">
                            <h6>GeoJSON Areas</h6>
                            <span class="revenue-tag" style="background: #f3f4f6; color: #374151;">GeoJSON</span>
                        </div>
                    </div>
                    <div class="revenue-amount">{{ number_format($stats['geojson']['total']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Website Visitors -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-body-custom">
                <p class="stat-label mb-0">CCTV Baik</p>
                <h2 class="stat-value-large mb-0">{{ number_format($stats['cctv']['baik']) }}</h2>
                <p class="stat-change positive mb-0">
                    <i class="material-icons-outlined" style="font-size: 16px;">trending_up</i>
                    <span>{{ $stats['cctv']['baik_percentage'] }}% dari total</span>
                </p>
                <div class="chart-container">
                    <canvas id="baikChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- New Customers -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-body-custom">
                <p class="stat-label mb-0">CCTV Live</p>
                <h2 class="stat-value-large mb-0">{{ number_format($stats['cctv']['live']) }}</h2>
                <p class="stat-change positive mb-0">
                    <i class="material-icons-outlined" style="font-size: 16px;">trending_up</i>
                    <span>{{ $stats['cctv']['live_percentage'] }}% dari total</span>
                </p>
                <div class="chart-container">
                    <canvas id="liveChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Sessions -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-body-custom">
                <p class="stat-label mb-0">CCTV Rusak</p>
                <h2 class="stat-value-large mb-0">{{ number_format($stats['cctv']['rusak']) }}</h2>
                <p class="stat-change negative mb-0">
                    <i class="material-icons-outlined" style="font-size: 16px;">trending_down</i>
                    <span>{{ $stats['cctv']['rusak_percentage'] }}% dari total</span>
                </p>
                <div class="chart-container">
                    <canvas id="rusakChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Transactions and Expenses -->
<div class="row g-3 mb-4">
    <!-- Transactions -->
    <div class="col-xl-6 col-lg-12">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-header-custom">
                <h5 class="mb-0 fw-bold">Top Sites</h5>
            </div>
            <div class="card-body-custom">
                @forelse($stats['cctv']['distribution_by_site']->take(5) as $index => $site)
                <div class="transaction-item">
                    <div class="transaction-info">
                        <div class="transaction-logo" style="background: {{ ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'][$index] ?? '#6b7280' }};">
                            {{ strtoupper(substr($site->site, 0, 2)) }}
                        </div>
                        <div class="transaction-details">
                            <h6>{{ $site->site }}</h6>
                            <p>Site Location</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="status-badge status-paid">Active</span>
                        <span class="revenue-amount">{{ number_format($site->count) }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">Tidak ada data</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- All Expenses -->
    <div class="col-xl-6 col-lg-12">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-header-custom">
                <h5 class="mb-0 fw-bold">All Coverage</h5>
            </div>
            <div class="card-body-custom">
                <div class="chart-container-large">
                    <canvas id="allCoverageChart"></canvas>
                </div>
                <div class="expense-breakdown">
                    <div class="expense-item">
                        <div class="expense-item-label">Total CCTV</div>
                        <div class="expense-item-value">{{ number_format($stats['cctv']['total']) }}</div>
                    </div>
                    <div class="expense-item">
                        <div class="expense-item-label">With Coverage</div>
                        <div class="expense-item-value">{{ number_format($stats['coverage']['total']) }}</div>
                    </div>
                    <div class="expense-item">
                        <div class="expense-item-label">Coverage %</div>
                        <div class="expense-item-value">{{ $stats['cctv']['total'] > 0 ? round(($stats['coverage']['total'] / $stats['cctv']['total']) * 100, 1) : 0 }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Visitors Comparison, Support Status, Sales by Countries -->
<div class="row g-3 mb-4">
    <!-- New vs Old Visitors -->
    <div class="col-xl-4 col-lg-12">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-header-custom">
                <h5 class="mb-0 fw-bold">Kondisi vs Status</h5>
                <p class="mb-0 text-muted" style="font-size: 12px;">Monthly comparison</p>
            </div>
            <div class="card-body-custom">
                <div class="chart-container-large">
                    <canvas id="comparisonChart"></canvas>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <div>
                        <span class="badge" style="background: #8b5cf6; color: white; padding: 6px 12px;">{{ number_format($stats['cctv']['baik']) }} Baik</span>
                    </div>
                    <div>
                        <span class="badge" style="background: #10b981; color: white; padding: 6px 12px;">{{ number_format($stats['cctv']['live']) }} Live</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Status -->
    <div class="col-xl-4 col-lg-12">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-header-custom">
                <h5 class="mb-0 fw-bold">Control Room Status</h5>
                <p class="mb-0 text-muted" style="font-size: 12px;">Last 7 Days</p>
            </div>
            <div class="card-body-custom">
                <div class="circular-progress">
                    <canvas id="supportStatusChart"></canvas>
                </div>
                <div class="mt-4">
                    <div class="support-metric">
                        <div class="support-info">
                            <div class="support-icon" style="background: #3b82f6;">
                                <i class="material-icons-outlined">link</i>
                            </div>
                            <div>
                                <h6 class="mb-0">Total Pengawas</h6>
                            </div>
                        </div>
                        <div>
                            <span class="revenue-amount">{{ number_format($stats['pengawas']['total']) }}</span>
                            <span class="stat-change positive">+{{ $stats['pengawas']['total'] > 0 ? round(($stats['pengawas']['total'] / max($stats['cctv']['total'], 1)) * 100, 1) : 0 }}%</span>
                        </div>
                    </div>
                    <div class="support-metric">
                        <div class="support-info">
                            <div class="support-icon" style="background: #8b5cf6;">
                                <i class="material-icons-outlined">search</i>
                            </div>
                            <div>
                                <h6 class="mb-0">Control Rooms</h6>
                            </div>
                        </div>
                        <div>
                            <span class="revenue-amount">{{ number_format($stats['cctv']['total_control_rooms']) }}</span>
                            <span class="stat-change positive">+{{ $stats['cctv']['total_control_rooms'] > 0 ? round(($stats['cctv']['total_control_rooms'] / max($stats['cctv']['total'], 1)) * 100, 1) : 0 }}%</span>
                        </div>
                    </div>
                    <div class="support-metric">
                        <div class="support-info">
                            <div class="support-icon" style="background: #ef4444;">
                                <i class="material-icons-outlined">email</i>
                            </div>
                            <div>
                                <h6 class="mb-0">Coverage Rate</h6>
                            </div>
                        </div>
                        <div>
                            <span class="revenue-amount">{{ $stats['cctv']['total'] > 0 ? round(($stats['coverage']['total'] / $stats['cctv']['total']) * 100, 1) : 0 }}%</span>
                            <span class="stat-change negative">-{{ 100 - ($stats['cctv']['total'] > 0 ? round(($stats['coverage']['total'] / $stats['cctv']['total']) * 100, 1) : 0) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales by Countries -->
    <div class="col-xl-4 col-lg-12">
        <div class="card dashboard-card border-0 h-100 position-relative">
            <span class="three-dots material-icons-outlined">more_vert</span>
            <div class="card-header-custom">
                <h5 class="mb-0 fw-bold">Top Perusahaan</h5>
                <p class="mb-0 text-muted" style="font-size: 12px;">Monthly Overview</p>
            </div>
            <div class="card-body-custom">
                @forelse($stats['cctv']['distribution_by_perusahaan']->take(8) as $perusahaan)
                <div class="country-item">
                    <div class="country-info">
                        <div class="country-flag">🏢</div>
                        <div class="country-name">{{ $perusahaan->perusahaan }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="country-amount">{{ number_format($perusahaan->count) }}</span>
                        <span class="stat-change positive">+{{ $stats['cctv']['total'] > 0 ? round(($perusahaan->count / $stats['cctv']['total']) * 100, 1) : 0 }}%</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">Tidak ada data</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Total CCTV Chart (Bar Chart)
        const totalCctvCtx = document.getElementById('totalCctvChart');
        if (totalCctvCtx) {
            const months = @json(collect($stats['cctv']['recent_months'])->pluck('month'));
            const counts = @json(collect($stats['cctv']['recent_months'])->pluck('count'));
            new Chart(totalCctvCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        data: counts,
                        backgroundColor: 'rgba(99, 102, 241, 0.8)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 0,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, display: false },
                        x: { display: false }
                    }
                }
            });
        }

        // Coverage Donut Chart
        const coverageDonutCtx = document.getElementById('coverageDonutChart');
        if (coverageDonutCtx) {
            const coveragePercent = {{ $stats['cctv']['total'] > 0 ? round(($stats['coverage']['total'] / $stats['cctv']['total']) * 100, 1) : 0 }};
            new Chart(coverageDonutCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [coveragePercent, 100 - coveragePercent],
                        backgroundColor: ['rgba(99, 102, 241, 0.8)', 'rgba(243, 244, 246, 1)'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    cutout: '75%'
                }
            });
        }

        // Baik Chart (Line)
        const baikCtx = document.getElementById('baikChart');
        if (baikCtx) {
            const months = @json(collect($stats['cctv']['recent_months'])->pluck('month'));
            new Chart(baikCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        data: @json(collect($stats['cctv']['recent_months'])->pluck('count')),
                        borderColor: 'rgba(99, 102, 241, 1)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { display: false }, x: { display: false } }
                }
            });
        }

        // Live Chart (Bar)
        const liveCtx = document.getElementById('liveChart');
        if (liveCtx) {
            const months = @json(collect($stats['cctv']['recent_months'])->pluck('month'));
            new Chart(liveCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        data: @json(collect($stats['cctv']['recent_months'])->pluck('count')),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderWidth: 0,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { display: false }, x: { display: false } }
                }
            });
        }

        // Rusak Chart (Line)
        const rusakCtx = document.getElementById('rusakChart');
        if (rusakCtx) {
            const months = @json(collect($stats['cctv']['recent_months'])->pluck('month'));
            new Chart(rusakCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        data: @json(collect($stats['cctv']['recent_months'])->pluck('count')),
                        borderColor: 'rgba(239, 68, 68, 1)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(239, 68, 68, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { display: false }, x: { display: false } }
                }
            });
        }

        // All Coverage Chart (Doughnut)
        const allCoverageCtx = document.getElementById('allCoverageChart');
        if (allCoverageCtx) {
            const kategoriData = @json($stats['coverage']['by_kategori_area']);
            new Chart(allCoverageCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: kategoriData.map(item => item.kategori_area || 'Unknown'),
                    datasets: [{
                        data: kategoriData.map(item => item.count),
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(139, 92, 246, 0.8)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            });
        }

        // Comparison Chart (Line with 2 datasets)
        const comparisonCtx = document.getElementById('comparisonChart');
        if (comparisonCtx) {
            const months = @json(collect($stats['cctv']['recent_months'])->pluck('month'));
            new Chart(comparisonCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Baik',
                        data: @json(collect($stats['cctv']['recent_months'])->pluck('count')),
                        borderColor: 'rgba(139, 92, 246, 1)',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Live',
                        data: @json(collect($stats['cctv']['recent_months'])->pluck('count')),
                        borderColor: 'rgba(16, 185, 129, 1)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Support Status Chart (Circular Progress)
        const supportStatusCtx = document.getElementById('supportStatusChart');
        if (supportStatusCtx) {
            const solvedPercent = {{ $stats['cctv']['total'] > 0 ? round(($stats['cctv']['baik'] / $stats['cctv']['total']) * 100, 1) : 0 }};
            new Chart(supportStatusCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [solvedPercent, 100 - solvedPercent],
                        backgroundColor: ['rgba(139, 92, 246, 0.8)', 'rgba(243, 244, 246, 1)'],
                        borderWidth: 0,
                        cutout: '70%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    }
                },
                plugins: [{
                    id: 'centerText',
                    beforeDraw: function(chart) {
                        const ctx = chart.ctx;
                        const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                        const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                        ctx.save();
                        ctx.font = 'bold 16px Arial';
                        ctx.fillStyle = '#111827';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText('CCTV Baik', centerX, centerY - 10);
                        ctx.font = 'bold 24px Arial';
                        ctx.fillText(solvedPercent + '%', centerX, centerY + 10);
                        ctx.restore();
                    }
                }]
            });
        }
    });
</script>
@endsection
