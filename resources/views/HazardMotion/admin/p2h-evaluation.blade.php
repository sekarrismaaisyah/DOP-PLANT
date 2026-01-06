@extends('layouts.masterMotionHazardAdmin')

@section('title', 'Evaluasi Pelaksanaan P2H - Beraucoal')

@section('css')
<style>
    .evaluation-header {
        margin-bottom: 24px;
    }

    .evaluation-title {
        font-size: 24px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 8px;
    }

    .evaluation-subtitle {
        font-size: 14px;
        color: #6b7280;
    }

    .stats-card {
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        color: white;
    }

    .stats-card.total {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stats-card.completion {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .stats-card.control-rooms {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }

    .stats-card.records {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .stats-number {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .stats-label {
        font-size: 14px;
        opacity: 0.9;
    }

    .filter-controls {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .chart-container {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .chart-title {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 16px;
    }

    .table-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-completed {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-pending {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .progress-bar-custom {
        height: 8px;
        border-radius: 4px;
        background-color: #e5e7eb;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        transition: width 0.3s ease;
    }
</style>
@endsection

@section('content')
<div class="evaluation-header">
    <h1 class="evaluation-title">Evaluasi Pelaksanaan P2H</h1>
    <p class="evaluation-subtitle">Dashboard evaluasi dan monitoring pelaksanaan P2H CCTV</p>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="stats-card total">
            <div class="stats-number">{{ $totalRecords }}</div>
            <div class="stats-label">Total P2H Terselesaikan</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card completion">
            <div class="stats-number">{{ number_format($completionRate, 1) }}%</div>
            <div class="stats-label">Tingkat Penyelesaian</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card control-rooms">
            <div class="stats-number">{{ $uniqueControlRooms }}/{{ $totalControlRooms }}</div>
            <div class="stats-label">Control Room Aktif</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card records">
            <div class="stats-number">{{ $totalControlRooms }}</div>
            <div class="stats-label">Total Control Room</div>
        </div>
    </div>
</div>

<!-- Filter Controls -->
<div class="filter-controls">
    <form method="GET" action="{{ route('hazard-detection.p2h.evaluation') }}" id="filterForm">
        <div class="row">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
            </div>
            <div class="col-md-2">
                <label for="shift" class="form-label">Shift</label>
                <select class="form-select" id="shift" name="shift">
                    <option value="all" {{ $shift === 'all' ? 'selected' : '' }}>Semua Shift</option>
                    <option value="1" {{ $shift === '1' ? 'selected' : '' }}>Shift 1</option>
                    <option value="2" {{ $shift === '2' ? 'selected' : '' }}>Shift 2</option>
                    <option value="3" {{ $shift === '3' ? 'selected' : '' }}>Shift 3</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="control_room" class="form-label">Control Room</label>
                <select class="form-select" id="control_room" name="control_room">
                    <option value="all" {{ $controlRoom === 'all' ? 'selected' : '' }}>Semua Control Room</option>
                    @foreach($controlRooms as $cr)
                    <option value="{{ $cr }}" {{ $controlRoom === $cr ? 'selected' : '' }}>{{ $cr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="material-icons-outlined">filter_list</i> Filter
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Charts -->
<div class="row">
    <div class="col-md-12">
        <div class="chart-container">
            <h5 class="chart-title">Trend Pelaksanaan P2H Harian</h5>
            <canvas id="dailyChart" height="80"></canvas>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="chart-title">Distribusi per Shift</h5>
            <canvas id="shiftChart"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-container">
            <h5 class="chart-title">Evaluasi Item Pemeriksaan Fisik</h5>
            <canvas id="fisikChart"></canvas>
        </div>
    </div>
</div>

<!-- Control Room Statistics Table -->
<div class="table-card">
    <h5 class="chart-title mb-3">Statistik per Control Room</h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Control Room</th>
                    <th>Total P2H</th>
                    <th>Tanggal Terakhir</th>
                    <th>Shift Terakhir</th>
                    <th>Pengawas</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($controlRoomStats as $stat)
                <tr>
                    <td><strong>{{ $stat['control_room'] }}</strong></td>
                    <td>{{ $stat['total_p2h'] }}</td>
                    <td>{{ $stat['latest_date'] ? \Carbon\Carbon::parse($stat['latest_date'])->format('d/m/Y') : '-' }}</td>
                    <td>{{ $stat['latest_shift'] ?? '-' }}</td>
                    <td>{{ $stat['latest_pengawas'] ?? '-' }}</td>
                    <td>
                        <span class="status-badge {{ $stat['status'] === 'completed' ? 'status-completed' : 'status-pending' }}">
                            {{ $stat['status'] === 'completed' ? 'Aktif' : 'Belum Ada' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Items Evaluation Table -->
<div class="table-card">
    <h5 class="chart-title mb-3">Evaluasi Detail Item Pemeriksaan</h5>
    <ul class="nav nav-tabs" id="itemsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="fisik-tab" data-bs-toggle="tab" data-bs-target="#fisik" type="button" role="tab">
                Pemeriksaan Fisik
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="fungsi-tab" data-bs-toggle="tab" data-bs-target="#fungsi" type="button" role="tab">
                Pemeriksaan Fungsi
            </button>
        </li>
    </ul>
    <div class="tab-content" id="itemsTabContent">
        <div class="tab-pane fade show active" id="fisik" role="tabpanel">
            <div class="table-responsive mt-3">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Total Pemeriksaan</th>
                            <th>Baik</th>
                            <th>Rusak</th>
                            <th>Tingkat Kebaikan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($itemsEvaluation['fisik'] as $item => $eval)
                        <tr>
                            <td><strong>{{ $item }}</strong></td>
                            <td>{{ $eval['total'] }}</td>
                            <td><span class="text-success">{{ $eval['baik'] }}</span></td>
                            <td><span class="text-danger">{{ $eval['rusak'] }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress-bar-custom flex-grow-1 me-2">
                                        <div class="progress-fill" style="width: {{ $eval['baik_percentage'] }}%"></div>
                                    </div>
                                    <span class="text-muted">{{ number_format($eval['baik_percentage'], 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="fungsi" role="tabpanel">
            <div class="table-responsive mt-3">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Total Pemeriksaan</th>
                            <th>Baik</th>
                            <th>Rusak</th>
                            <th>Tingkat Kebaikan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($itemsEvaluation['fungsi'] as $item => $eval)
                        <tr>
                            <td><strong>{{ $item }}</strong></td>
                            <td>{{ $eval['total'] }}</td>
                            <td><span class="text-success">{{ $eval['baik'] }}</span></td>
                            <td><span class="text-danger">{{ $eval['rusak'] }}</span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress-bar-custom flex-grow-1 me-2">
                                        <div class="progress-fill" style="width: {{ $eval['baik_percentage'] }}%"></div>
                                    </div>
                                    <span class="text-muted">{{ number_format($eval['baik_percentage'], 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Daily Chart
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    const dailyChart = new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: @json(array_column($dailyStats, 'date')),
            datasets: [
                {
                    label: 'Total P2H',
                    data: @json(array_column($dailyStats, 'count')),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Shift 1',
                    data: @json(array_column($dailyStats, 'shift_1')),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Shift 2',
                    data: @json(array_column($dailyStats, 'shift_2')),
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Shift 3',
                    data: @json(array_column($dailyStats, 'shift_3')),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Shift Chart
    const shiftCtx = document.getElementById('shiftChart').getContext('2d');
    const shiftChart = new Chart(shiftCtx, {
        type: 'doughnut',
        data: {
            labels: ['Shift 1', 'Shift 2', 'Shift 3'],
            datasets: [{
                data: [
                    {{ $shiftStats['shift_1'] }},
                    {{ $shiftStats['shift_2'] }},
                    {{ $shiftStats['shift_3'] }}
                ],
                backgroundColor: [
                    '#10b981',
                    '#f59e0b',
                    '#ef4444'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Fisik Chart
    const fisikCtx = document.getElementById('fisikChart').getContext('2d');
    const fisikData = {
        labels: @json(array_keys($itemsEvaluation['fisik'])),
        datasets: [{
            label: 'Tingkat Kebaikan (%)',
            data: @json(array_column($itemsEvaluation['fisik'], 'baik_percentage')),
            backgroundColor: 'rgba(16, 185, 129, 0.6)',
            borderColor: '#10b981',
            borderWidth: 1
        }]
    };
    const fisikChart = new Chart(fisikCtx, {
        type: 'bar',
        data: fisikData,
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection

