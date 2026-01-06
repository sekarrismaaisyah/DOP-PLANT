<div class="dashboard-content">
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-md bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center">
                                <i class="material-icons-outlined text-white fs-4">videocam</i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total CCTV</p>
                            <h4 class="mb-0">{{ number_format($stats['total_cctv']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-md bg-success bg-gradient rounded-circle d-flex align-items-center justify-content-center">
                                <i class="material-icons-outlined text-white fs-4">check_circle</i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Kondisi Baik</p>
                            <h4 class="mb-0">{{ number_format($stats['cctv_baik']) }}</h4>
                            <small class="text-muted">{{ $stats['total_cctv'] > 0 ? number_format(($stats['cctv_baik'] / $stats['total_cctv']) * 100, 1) : 0 }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-md bg-danger bg-gradient rounded-circle d-flex align-items-center justify-content-center">
                                <i class="material-icons-outlined text-white fs-4">error</i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Kondisi Rusak</p>
                            <h4 class="mb-0">{{ number_format($stats['cctv_rusak']) }}</h4>
                            <small class="text-muted">{{ $stats['total_cctv'] > 0 ? number_format(($stats['cctv_rusak'] / $stats['total_cctv']) * 100, 1) : 0 }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-md bg-info bg-gradient rounded-circle d-flex align-items-center justify-content-center">
                                <i class="material-icons-outlined text-white fs-4">live_tv</i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Live View</p>
                            <h4 class="mb-0">{{ number_format($stats['cctv_live']) }}</h4>
                            <small class="text-muted">{{ $stats['total_cctv'] > 0 ? number_format(($stats['cctv_live'] / $stats['total_cctv']) * 100, 1) : 0 }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-md bg-warning bg-gradient rounded-circle d-flex align-items-center justify-content-center">
                                <i class="material-icons-outlined text-white fs-4">link</i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Dengan Link Akses</p>
                            <h4 class="mb-0">{{ number_format($stats['cctv_with_link']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-md bg-secondary bg-gradient rounded-circle d-flex align-items-center justify-content-center">
                                <i class="material-icons-outlined text-white fs-4">place</i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Dengan Koordinat</p>
                            <h4 class="mb-0">{{ number_format($stats['cctv_with_coordinates']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-md bg-purple bg-gradient rounded-circle d-flex align-items-center justify-content-center">
                                <i class="material-icons-outlined text-white fs-4">meeting_room</i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Control Room</p>
                            <h4 class="mb-0">{{ number_format($stats['total_control_rooms']) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0"><i class="material-icons-outlined me-2">bar_chart</i> Distribusi per Site</h5>
                </div>
                <div class="card-body">
                    <canvas id="siteChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0"><i class="material-icons-outlined me-2">pie_chart</i> Distribusi per Perusahaan</h5>
                </div>
                <div class="card-body">
                    <canvas id="perusahaanChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0"><i class="material-icons-outlined me-2">donut_large</i> Distribusi Kondisi</h5>
                </div>
                <div class="card-body">
                    <canvas id="kondisiChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0"><i class="material-icons-outlined me-2">assessment</i> Distribusi Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Sites and Companies Table -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0"><i class="material-icons-outlined me-2">location_on</i> Top 10 Site</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Site</th>
                                    <th class="text-end">Jumlah CCTV</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['distribution_by_site'] as $index => $site)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $site->site }}</strong></td>
                                    <td class="text-end">
                                        <span class="badge bg-primary rounded-pill">{{ number_format($site->count) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0"><i class="material-icons-outlined me-2">business</i> Top 10 Perusahaan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Perusahaan</th>
                                    <th class="text-end">Jumlah CCTV</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['distribution_by_perusahaan'] as $index => $perusahaan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $perusahaan->perusahaan }}</strong></td>
                                    <td class="text-end">
                                        <span class="badge bg-success rounded-pill">{{ number_format($perusahaan->count) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('dashboard-charts')
<script>
    var siteChart, perusahaanChart, kondisiChart, statusChart;
    var chartsInitialized = false;
    
    function initializeCharts() {
        if (chartsInitialized) {
            return;
        }
        
        // Site Chart
        const siteCtx = document.getElementById('siteChart');
        if (!siteCtx) return;
        
        const siteCtx2d = siteCtx.getContext('2d');
        if (siteChart) {
            siteChart.destroy();
        }
        siteChart = new Chart(siteCtx2d, {
            type: 'bar',
            data: {
                labels: @json($stats['distribution_by_site']->pluck('site')),
                datasets: [{
                    label: 'Jumlah CCTV',
                    data: @json($stats['distribution_by_site']->pluck('count')),
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 0
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            maxTicksLimit: 10,
                            precision: 0
                        }
                    }
                }
            }
        });

        // Perusahaan Chart
        const perusahaanCtx = document.getElementById('perusahaanChart');
        if (!perusahaanCtx) return;
        
        const perusahaanCtx2d = perusahaanCtx.getContext('2d');
        if (perusahaanChart) {
            perusahaanChart.destroy();
        }
        perusahaanChart = new Chart(perusahaanCtx2d, {
            type: 'doughnut',
            data: {
                labels: @json($stats['distribution_by_perusahaan']->pluck('perusahaan')),
                datasets: [{
                    data: @json($stats['distribution_by_perusahaan']->pluck('count')),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.6)',
                        'rgba(16, 185, 129, 0.6)',
                        'rgba(245, 158, 11, 0.6)',
                        'rgba(239, 68, 68, 0.6)',
                        'rgba(139, 92, 246, 0.6)',
                        'rgba(236, 72, 153, 0.6)',
                        'rgba(6, 182, 212, 0.6)',
                        'rgba(251, 146, 60, 0.6)',
                        'rgba(34, 197, 94, 0.6)',
                        'rgba(168, 85, 247, 0.6)'
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(6, 182, 212, 1)',
                        'rgba(251, 146, 60, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(168, 85, 247, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 0
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Kondisi Chart
        const kondisiCtx = document.getElementById('kondisiChart');
        if (!kondisiCtx) return;
        
        const kondisiCtx2d = kondisiCtx.getContext('2d');
        if (kondisiChart) {
            kondisiChart.destroy();
        }
        kondisiChart = new Chart(kondisiCtx2d, {
            type: 'pie',
            data: {
                labels: @json($stats['distribution_by_kondisi']->pluck('kondisi')),
                datasets: [{
                    data: @json($stats['distribution_by_kondisi']->pluck('count')),
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.6)',
                        'rgba(239, 68, 68, 0.6)',
                        'rgba(156, 163, 175, 0.6)'
                    ],
                    borderColor: [
                        'rgba(16, 185, 129, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(156, 163, 175, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 0
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart');
        if (!statusCtx) return;
        
        const statusCtx2d = statusCtx.getContext('2d');
        if (statusChart) {
            statusChart.destroy();
        }
        statusChart = new Chart(statusCtx2d, {
            type: 'bar',
            data: {
                labels: @json($stats['distribution_by_status']->pluck('status')),
                datasets: [{
                    label: 'Jumlah CCTV',
                    data: @json($stats['distribution_by_status']->pluck('count')),
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 0
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            maxTicksLimit: 10,
                            precision: 0
                        }
                    }
                }
            }
        });
        
        chartsInitialized = true;
    }
    
    $(document).ready(function() {
        // Initialize charts when dashboard tab is shown (only once)
        $('#dashboard-tab').one('shown.bs.tab', function (e) {
            setTimeout(function() {
                initializeCharts();
            }, 100);
        });
        
        // Also initialize if dashboard tab is already active on page load
        if ($('#dashboard-tab').hasClass('active') && $('#dashboard').hasClass('active')) {
            setTimeout(function() {
                initializeCharts();
            }, 300);
        }
    });
    
    // Prevent chart resize on window resize
    $(window).on('resize', function() {
        if (siteChart) siteChart.resize();
        if (perusahaanChart) perusahaanChart.resize();
        if (kondisiChart) kondisiChart.resize();
        if (statusChart) statusChart.resize();
    });
</script>
@endpush

