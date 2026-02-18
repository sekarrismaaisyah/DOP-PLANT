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

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mb-3" id="alertLogTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="supervisory-tab" data-bs-toggle="tab" data-bs-target="#supervisory" type="button" role="tab">
            Supervisory
        </button>
    </li>
    {{-- Tab lain bisa ditambah di sini --}}
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="alertLogTabContent">
    <!-- Tab: Supervisory (data supervisory_alert_log) -->
    <div class="tab-pane fade show active" id="supervisory" role="tabpanel">
        <div class="row">
            <div class="col-12">
                <div class="card rounded-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3 flex-wrap gap-2">
                            <div>
                                <h5 class="mb-0 fw-bold">Data Alert Supervisory</h5>
                                <p class="text-muted mb-0">Log status alert Pengawasan Berjarak (per area per hari, hanya HIGH/MEDIUM)</p>
                            </div>
                        </div>

                        <form method="get" action="{{ route('supervisory-alert-log.index') }}" class="row g-2 mb-3">
                            <div class="col-auto">
                                <label class="col-form-label col-form-label-sm">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ request('tanggal') }}" style="width: auto;">
                            </div>
                            <div class="col-auto">
                                <label class="col-form-label col-form-label-sm">Risk</label>
                                <select name="risk_level" class="form-select form-select-sm" style="width: auto;">
                                    <option value="">Semua</option>
                                    <option value="HIGH" {{ request('risk_level') === 'HIGH' ? 'selected' : '' }}>HIGH (Merah)</option>
                                    <option value="MEDIUM" {{ request('risk_level') === 'MEDIUM' ? 'selected' : '' }}>MEDIUM (Kuning)</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label class="col-form-label col-form-label-sm">Lokasi</label>
                                <input type="text" name="nama_lokasi" class="form-control form-control-sm" placeholder="Cari nama lokasi" value="{{ request('nama_lokasi') }}" style="width: 180px;">
                            </div>
                            <div class="col-auto d-flex align-items-end">
                                <button type="submit" class="btn btn-sm btn-primary me-1">Filter</button>
                                <a href="{{ route('supervisory-alert-log.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nama Lokasi</th>
                                        <th>Risk Level</th>
                                        <th>SAP Report</th>
                                        <th>CCTV Online</th>
                                        <th>High Risk Area</th>
                                        <th>Diperbarui</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($alerts as $row)
                                    <tr>
                                        <td>{{ $row->tanggal ? $row->tanggal->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $row->nama_lokasi }}</td>
                                        <td>
                                            @if($row->risk_level === 'HIGH')
                                                <span class="badge bg-danger">HIGH</span>
                                            @elseif($row->risk_level === 'MEDIUM')
                                                <span class="badge bg-warning text-dark">MEDIUM</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $row->risk_level }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $row->has_sap_report ? 'Ya' : 'Tidak' }}</td>
                                        <td>{{ $row->has_online_cctv ? 'Ya' : 'Tidak' }}</td>
                                        <td>{{ $row->is_high_risk_area ? 'Ya' : 'Tidak' }}</td>
                                        <td>{{ $row->updated_at ? $row->updated_at->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada data alert supervisory.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($alerts->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted">
                                Menampilkan {{ $alerts->firstItem() }}–{{ $alerts->lastItem() }} dari {{ $alerts->total() }} data
                            </small>
                            <div>
                                {{ $alerts->links() }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
