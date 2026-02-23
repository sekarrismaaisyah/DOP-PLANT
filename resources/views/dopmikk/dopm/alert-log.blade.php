@extends('layouts.masterDopm')

@section('title', 'Alert Log - DOPM & IKK')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}">
<style>
.alert-log-page .filter-card { border-radius: 1rem; }
.alert-log-page .main-card { border-radius: 1rem; overflow: hidden; }
.alert-log-page .accordion-item { border-radius: 0.75rem; margin-bottom: 0.75rem; border: 1px solid rgba(0,0,0,.08); }
.alert-log-page .accordion-button { font-size: 1rem; background: #f8f9fa; }
.alert-log-page .accordion-button:not(.collapsed) { background: #fff; box-shadow: 0 -1px 0 0 rgba(0,0,0,.05); }
.alert-log-page .accordion-body { padding: 1rem 1.25rem; background: #fff; }
.alert-log-page .table-section { margin-bottom: 1.5rem; }
.alert-log-page .table-section:last-child { margin-bottom: 0; }
.alert-log-page .table-section-title { font-size: 0.95rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.35rem; }
.alert-log-page .dataTables_wrapper .row:first-child { margin-bottom: 0.75rem; }
.alert-log-page .dataTables_wrapper .dataTables_length label, .alert-log-page .dataTables_wrapper .dataTables_filter label { margin-bottom: 0; }
.alert-log-page table.dataTable { width: 100% !important; }
.alert-log-page table.dataTable thead th { border-bottom: 2px solid #e9ecef; white-space: nowrap; }
.alert-log-page .empty-state { padding: 3rem 1rem; text-align: center; color: #6c757d; }
</style>
@endsection

@section('content')
<div class="alert-log-page">
    <x-page-title title="Alert Log" pagetitle="DOPM & IKK - Data IKK/Work Permit per Jam (Need Action & Warning)" />

    <div class="row">
        <div class="col-12">
            <div class="card filter-card shadow-sm mb-4">
                <div class="card-body py-3">
                    <form method="get" action="{{ route('dopmikk.dopm.alert-log') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-md-4 col-lg-3">
                            <label for="filterDate" class="form-label small fw-semibold text-muted mb-1">Tanggal</label>
                            <input type="date" name="date" id="filterDate" class="form-control form-control-sm rounded-3" value="{{ $filterDate ?? now()->toDateString() }}">
                        </div>
                        <div class="col-12 col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm rounded-3 px-4">
                                <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">search</i>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card main-card shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-icons-outlined text-danger">warning</span>
                        <h5 class="mb-0 fw-bold">Data IKK / Work Permit per Jam</h5>
                    </div>
                    <p class="mb-0 small text-muted mt-1">Need Action = Merah, Warning = Kuning. Data tersimpan per jam saat dashboard dibuka dan dari scheduler.</p>
                </div>
                <div class="card-body">
                    @php $dopmAlertLogs = $dopmAlertLogs ?? collect(); @endphp
                    @if($dopmAlertLogs->isEmpty())
                        <div class="empty-state">
                            <span class="material-icons-outlined mb-2" style="font-size: 48px;">schedule</span>
                            <p class="mb-0 fw-medium">Belum ada data alert untuk tanggal <strong>{{ $filterDate ?? now()->toDateString() }}</strong>.</p>
                            <p class="mb-0 small mt-1">Buka Dashboard Daily pada tanggal hari ini agar snapshot per jam tersimpan.</p>
                        </div>
                    @else
                        <div class="accordion" id="alertLogAccordion">
                            @foreach($dopmAlertLogs as $idx => $log)
                                @php
                                    $snap = $log->snapshot ?? [];
                                    $needActionList = $snap['need_action'] ?? [];
                                    $warningList = $snap['warning'] ?? [];
                                    $collapseId = 'collapse-' . $log->id;
                                @endphp
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $idx > 0 ? 'collapsed' : '' }} py-3" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $idx === 0 ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                                            <span class="d-flex align-items-center gap-3 flex-wrap">
                                                <strong class="text-nowrap">Jam {{ sprintf('%02d', $log->jam) }}:00</strong>
                                                <span class="badge bg-danger rounded-pill">{{ count($needActionList) }} Need Action</span>
                                                <span class="badge bg-warning text-dark rounded-pill">{{ count($warningList) }} Warning</span>
                                                <span class="text-muted small">Update: {{ $log->updated_at ? $log->updated_at->format('d/m/Y H:i') : '-' }}</span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $idx === 0 ? 'show' : '' }}" data-bs-parent="#alertLogAccordion">
                                        <div class="accordion-body">
                                            {{-- Need Action (Merah) --}}
                                            <div class="table-section">
                                                <div class="table-section-title text-danger fw-bold">
                                                    <span class="material-icons-outlined" style="font-size: 20px;">error</span>
                                                    Need Action / Merah ({{ count($needActionList) }} IKK)
                                                </div>
                                                @if(empty($needActionList))
                                                    <p class="text-muted small mb-0">Tidak ada IKK status Merah pada jam ini.</p>
                                                @else
                                                    <div class="table-responsive">
                                                        <table id="table-need-{{ $log->id }}" class="table table-sm table-bordered table-hover align-middle alert-log-datatable mb-0" width="100%">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Kode IKK</th>
                                                                    <th>Tanggal Mulai</th>
                                                                    <th>Jam Mulai</th>
                                                                    <th>Jenis IJK</th>
                                                                    <th>Site</th>
                                                                    <th>Nama Pekerjaan</th>
                                                                    <th>Lokasi</th>
                                                                    <th>Alasan</th>
                                                                    <th>Intervensi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($needActionList as $ikk)
                                                                    <tr>
                                                                        <td class="fw-semibold">{{ $ikk['code'] ?? '-' }}</td>
                                                                        <td>{{ $ikk['start_date_tanggal'] ?? '-' }}</td>
                                                                        <td>{{ $ikk['start_date_jam'] ?? '-' }}</td>
                                                                        <td>{{ $ikk['jenis_ijin_kerja_khusus'] ?? '-' }}</td>
                                                                        <td>{{ $ikk['site'] ?? '-' }}</td>
                                                                        <td>{{ $ikk['nama_pekerjaan'] ?? '-' }}</td>
                                                                        <td>{{ trim(($ikk['location_name'] ?? '') . (($ikk['location_detail_name'] ?? '') ? ' / ' . ($ikk['location_detail_name'] ?? '') : '')) ?: '-' }}</td>
                                                                        <td class="small text-muted" title="{{ $ikk['alasan_matriks'] ?? '' }}">{{ Str::limit($ikk['alasan_matriks'] ?? '-', 80) }}</td>
                                                                        <td>
                                                                            <a href="{{ route('dopmikk.dopm.dashboard', ['date' => $filterDate ?? now()->toDateString(), 'kode_ikk' => $ikk['code'] ?? '']) }}" class="btn btn-sm btn-outline-warning" target="_blank" rel="noopener" title="Buka Dashboard untuk Intervensi (IPK-IKK, OKK, OAK)">
                                                                                <span class="material-icons-outlined" style="font-size:18px;">campaign</span> Intervensi
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- Warning (Kuning) --}}
                                            <div class="table-section">
                                                <div class="table-section-title text-warning fw-bold">
                                                    <span class="material-icons-outlined text-dark" style="font-size: 20px;">info</span>
                                                    Warning / Kuning ({{ count($warningList) }} IKK)
                                                </div>
                                                @if(empty($warningList))
                                                    <p class="text-muted small mb-0">Tidak ada IKK status Kuning pada jam ini.</p>
                                                @else
                                                    <div class="table-responsive">
                                                        <table id="table-warning-{{ $log->id }}" class="table table-sm table-bordered table-hover align-middle alert-log-datatable mb-0" width="100%">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Kode IKK</th>
                                                                    <th>Tanggal Mulai</th>
                                                                    <th>Jam Mulai</th>
                                                                    <th>Jenis IJK</th>
                                                                    <th>Site</th>
                                                                    <th>Nama Pekerjaan</th>
                                                                    <th>Lokasi</th>
                                                                    <th>Alasan</th>
                                                                    <th>Intervensi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($warningList as $ikk)
                                                                    <tr>
                                                                        <td class="fw-semibold">{{ $ikk['code'] ?? '-' }}</td>
                                                                        <td>{{ $ikk['start_date_tanggal'] ?? '-' }}</td>
                                                                        <td>{{ $ikk['start_date_jam'] ?? '-' }}</td>
                                                                        <td>{{ $ikk['jenis_ijin_kerja_khusus'] ?? '-' }}</td>
                                                                        <td>{{ $ikk['site'] ?? '-' }}</td>
                                                                        <td>{{ $ikk['nama_pekerjaan'] ?? '-' }}</td>
                                                                        <td>{{ trim(($ikk['location_name'] ?? '') . (($ikk['location_detail_name'] ?? '') ? ' / ' . ($ikk['location_detail_name'] ?? '') : '')) ?: '-' }}</td>
                                                                        <td class="small text-muted" title="{{ $ikk['alasan_matriks'] ?? '' }}">{{ Str::limit($ikk['alasan_matriks'] ?? '-', 80) }}</td>
                                                                        <td>
                                                                            <a href="{{ route('dopmikk.dopm.dashboard', ['date' => $filterDate ?? now()->toDateString(), 'kode_ikk' => $ikk['code'] ?? '']) }}" class="btn btn-sm btn-outline-warning" target="_blank" rel="noopener" title="Buka Dashboard untuk Intervensi (IPK-IKK, OKK, OAK)">
                                                                                <span class="material-icons-outlined" style="font-size:18px;">campaign</span> Intervensi
                                                                            </a>
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
                            @endforeach
                        </div>
                    @endif
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
(function() {
    var dtOptions = {
        order: [[0, 'asc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Semua']],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ baris',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(filter dari _MAX_ data)',
            paginate: { first: 'Awal', last: 'Akhir', next: 'Selanjutnya', previous: 'Sebelumnya' },
            zeroRecords: 'Tidak ada data yang cocok'
        },
        dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        drawCallback: function() { }
    };
    document.querySelectorAll('.alert-log-datatable').forEach(function(tbl) {
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable(tbl)) {
            $(tbl).DataTable(dtOptions);
        }
    });
})();
</script>
@endsection
