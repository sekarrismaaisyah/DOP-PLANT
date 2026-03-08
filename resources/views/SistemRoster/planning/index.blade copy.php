@extends('layouts.masterRoster')

@section('title', 'Roster Planning')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
.select2-container { width: 100% !important; }
.select2-container--bootstrap-5 .select2-selection { min-height: 32px; font-size: 0.875rem; }
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice { font-size: 0.75rem; padding: 2px 6px; }
.karyawan-select-cell { min-width: 250px; }
.saving-indicator { display: none; }
.saving-indicator.show { display: inline-block; }

/* Tabel ringkas (tingkat 1) */
#planningSummaryTable { border-collapse: separate; border-spacing: 0; }
#planningSummaryTable thead tr { background: #f8fafc !important; }
#planningSummaryTable thead th {
    font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.02em;
    color: #475569; border-bottom: 2px solid #e2e8f0; padding: 0.75rem 1rem;
}
.summary-row {
    transition: background-color 0.15s ease, box-shadow 0.15s ease;
}
.summary-row:hover { background-color: #f8fafc !important; }
.summary-row td { padding: 0.875rem 1rem; vertical-align: middle !important; border-bottom: 1px solid #f1f3f5; }
.summary-row .badge { font-weight: 500; padding: 0.35em 0.65em; }
.summary-total { font-weight: 600; color: #0d6efd; min-width: 1.5em; display: inline-block; text-align: center; }
.expand-btn {
    font-weight: 500; padding: 0.35rem 0.75rem; transition: all 0.2s ease;
    border-width: 1.5px;
}
.expand-btn:hover { transform: translateY(-1px); }
.expand-btn .expand-icon { font-size: 1.1em; vertical-align: -0.05em; }

/* Panel detail (tingkat 2) */
.detail-wrapper-row td { vertical-align: top !important; padding: 0 !important; border: none !important; }
.detail-inner {
    margin: 0 0.5rem 0.5rem; padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0; border-radius: 0.5rem;
    border-left: 4px solid #0d6efd;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}
.detail-inner .table {
    border-radius: 0.5rem; overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.detail-inner .table thead th {
    font-size: 0.8125rem; font-weight: 600; color: #475569;
    background: #fff !important; border-bottom: 2px solid #e2e8f0;
    padding: 0.6rem 0.75rem;
}
.detail-inner .table tbody tr { transition: background-color 0.15s ease; }
.detail-inner .table tbody tr:hover { background-color: #f8fafc !important; }
.detail-inner .table td { padding: 0.5rem 0.75rem; vertical-align: middle !important; font-size: 0.875rem; }
.detail-row td { border-color: #f1f5f9 !important; }
.collapse-btn {
    font-weight: 500; font-size: 0.8125rem;
    padding: 0.3rem 0.65rem; margin-top: 0.25rem;
}
.detail-row td { vertical-align: middle !important; }

/* Empty state */
#planningSummaryTable tbody > tr > td[colspan="7"] {
    padding: 2.5rem 1rem !important; color: #64748b;
}

/* Tabs per site */
.nav-tabs-custom { border-bottom: 2px solid #e9ecef; gap: 4px; }
.nav-tabs-custom .nav-link { border: 1px solid #dee2e6; border-bottom: none; margin-bottom: -2px; font-weight: 500; color: #6c757d; }
.nav-tabs-custom .nav-link:hover { color: #0d6efd; border-color: #dee2e6; }
.nav-tabs-custom .nav-link.active { color: #0d6efd; background: #fff; border-color: #dee2e6 #dee2e6 #fff; }
</style>
@endsection

@section('content')
    <x-page-title title="Roster Planning" pagetitle="Planning & Assignment Karyawan" />

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show rounded-4" role="alert">
                    <i class="bx bx-loader-alt bx-spin"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (($queueConnection ?? 'sync') !== 'sync')
                <div class="alert alert-secondary alert-dismissible fade show rounded-4" role="alert">
                    <i class="bx bx-info-circle"></i> Generate berjalan di background. Jika data belum muncul, jalankan queue worker: <code>php artisan queue:work</code>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (isset($latestJob) && $latestJob && in_array($latestJob->status, ['pending', 'processing']) && $latestJob->created_at->diffInMinutes(now()) >= 2)
                <div class="alert alert-warning alert-dismissible fade show rounded-4" role="alert">
                    <i class="bx bx-time-five"></i> Job generate masih antre/berjalan. Jika data belum muncul, pastikan queue worker aktif: <code>php artisan queue:work</code>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Job Progress Section -->
    <div class="row mb-4" id="jobProgressSection" style="display: none;">
        <div class="col-12">
            <div class="card rounded-4 border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1">
                                <i class="bx bx-loader-alt bx-spin text-primary"></i>
                                <span id="jobStatusText">Proses Generate Planning...</span>
                            </h6>
                            <small class="text-muted" id="jobProgressDetail">
                                DOP: <span id="dopProgress">0</span> | IKK: <span id="ikkProgress">0</span>
                            </small>
                        </div>
                        <div id="jobStatusBadge">
                            <span class="badge bg-primary">Processing</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Generate Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-8">
                            <form method="GET" action="{{ route('sistem-roster.planning.index') }}" id="filterForm" class="row g-3 align-items-end">
                                <div class="col-12">
                                    <label for="search" class="form-label">Cari (No IKK, Aktivitas, Lokasi, Site, Perusahaan, Pengawas)</label>
                                    <input type="text" name="search" id="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Ketik untuk cari di semua kolom...">
                                </div>
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $filterStartDate }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $filterEndDate }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="filter_site" class="form-label">Filter Site</label>
                                    <select name="filter_site" id="filter_site" class="form-select">
                                        <option value="">-- Semua Site --</option>
                                        @foreach($sites ?? [] as $s)
                                            <option value="{{ $s }}" {{ ($filterSite ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter_perusahaan" class="form-label">Filter Perusahaan</label>
                                    <select name="filter_perusahaan" id="filter_perusahaan" class="form-select">
                                        <option value="">-- Semua Perusahaan --</option>
                                        @foreach($perusahaanList ?? [] as $p)
                                            <option value="{{ $p }}" {{ ($filterPerusahaan ?? '') == $p ? 'selected' : '' }}>{{ Str::limit($p, 40) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="per_page" class="form-label">Per Halaman</label>
                                    <select name="per_page" id="per_page" class="form-select">
                                        @foreach([10, 25, 50, 100] as $pp)
                                            <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-search"></i> Filter
                                        </button>
                                        <a href="{{ route('sistem-roster.planning.index') }}" class="btn btn-outline-secondary">
                                            <i class="bx bx-reset"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="col-lg-4">
                            <form method="POST" action="{{ route('sistem-roster.planning.generate') }}" class="d-flex justify-content-end align-items-end h-100">
                                @csrf
                                <input type="hidden" name="start_date" value="{{ $filterStartDate }}">
                                <input type="hidden" name="end_date" value="{{ $filterEndDate }}">
                                <button type="submit" class="btn btn-success" id="generateBtn">
                                    <i class="bx bx-refresh"></i> Generate Planning
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs per Site -->
    @php
        $siteTabs = ['Semua', 'BMO 1', 'BMO 2', 'BMO 3', 'GMO', 'SMO', 'LMO', 'HO', 'Explorasi'];
        $currentFilterSite = $filterSite ?? '';
    @endphp
    <div class="row mb-3">
        <div class="col-12">
            <ul class="nav nav-tabs nav-tabs-custom flex-wrap" id="siteTabs" role="tablist">
                @foreach($siteTabs as $tab)
                    @php
                        $tabValue = $tab === 'Semua' ? '' : $tab;
                        $tabId = $tab === 'Semua' ? 'semua' : Str::slug($tab);
                        $isActive = ($currentFilterSite === $tabValue);
                    @endphp
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $isActive ? 'active' : '' }} rounded-3 px-3 py-2"
                                type="button"
                                data-site="{{ $tabValue }}"
                                id="tab-{{ $tabId }}"
                                role="tab">
                            {{ $tab }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Planning Table -->
    <div class="row">
        <div class="col-12">
            <div class="card rounded-4 shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        {{-- Tingkat 1: Tabel ringkas (Tanggal, Site, Jenis, Total Aktivitas, Total Assigned). Klik Aksi untuk buka detail. --}}
                        <table class="table table-hover align-middle mb-0" id="planningSummaryTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px"></th>
                                    <th>Tanggal</th>
                                    <th>Site</th>
                                    <th>Jenis</th>
                                    <th class="text-center">Total Aktivitas</th>
                                    <th class="text-center">Total Assigned</th>
                                    <th style="width: 100px" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $detailIndex = $plannings->isEmpty() ? 0 : (int) $plannings->firstItem() - 1; @endphp
                                @forelse ($grouped as $groupKey => $items)
                                    @php
                                        $first = $items->first();
                                        $tanggal = $first->tanggal;
                                        $site = $first->site ?? '-';
                                        $jenis = $first->source_type ?? '-';
                                        $totalAktivitas = $items->count();
                                        $totalAssigned = $items->filter(fn($p) => $p->karyawans->count() > 0)->count();
                                    @endphp
                                    <tr class="summary-row table-light" data-group-key="{{ $groupKey }}">
                                        <td class="py-2" style="width: 50px;"></td>
                                        <td class="py-2">{{ $tanggal->format('d M Y') }}</td>
                                        <td class="py-2">{{ $site }}</td>
                                        <td class="py-2">
                                            <span class="badge bg-{{ $jenis === 'DOP' ? 'secondary' : 'info' }}">{{ $jenis }}</span>
                                        </td>
                                        <td class="py-2 text-center"><span class="summary-total">{{ $totalAktivitas }}</span></td>
                                        <td class="py-2 text-center"><span class="summary-total">{{ $totalAssigned }}</span></td>
                                        <td class="py-2 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary expand-btn rounded-pill" data-group-key="{{ $groupKey }}" aria-expanded="false">
                                                <i class="expand-icon bx bx-plus"></i> Buka detail
                                            </button>
                                        </td>
                                    </tr>
                                    {{-- Tingkat 2: Tabel detail (No, Tanggal, Sumber, ..., Karyawan, Status, Aksi) hanya muncul setelah Aksi diklik --}}
                                    <tr class="detail-wrapper-row" data-group-key="{{ $groupKey }}" style="display: none;">
                                        <td colspan="7" class="p-0 border-0">
                                            <div class="detail-inner">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <h6 class="mb-0 text-secondary fw-semibold">
                                                        <i class="bx bx-list-ul me-2"></i>Detail aktivitas — assign karyawan di bawah
                                                    </h6>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary collapse-btn rounded-pill" data-group-key="{{ $groupKey }}">
                                                        <i class="bx bx-chevron-up"></i> Tutup detail
                                                    </button>
                                                </div>
                                                <table class="table table-sm table-bordered align-middle mb-0 bg-white">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 40px">No</th>
                                                            <th>Tanggal</th>
                                                            <th>Sumber</th>
                                                            <th>Site</th>
                                                            <th>No IKK</th>
                                                            <th>Aktivitas</th>
                                                            <th>Lokasi</th>
                                                            <th>Perusahaan</th>
                                                            <th>Shift</th>
                                                            <th class="karyawan-select-cell">Karyawan</th>
                                                            <th>Status</th>
                                                            <th style="width: 90px">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($items as $planning)
                                                            @php $detailIndex++; @endphp
                                                            <tr class="detail-row" data-planning-id="{{ $planning->id }}">
                                                                <td>{{ $detailIndex }}</td>
                                                                <td>{{ $planning->tanggal->format('d M Y') }}</td>
                                                                <td>
                                                                    <span class="badge bg-{{ $planning->source_type === 'DOP' ? 'secondary' : 'info' }}">{{ $planning->source_type }}</span>
                                                                </td>
                                                                <td><small>{{ $planning->site ?? '-' }}</small></td>
                                                                <td><small>{{ $planning->no_ikk ?? '-' }}</small></td>
                                                                <td><small>{{ Str::limit($planning->aktivitas, 30) ?? '-' }}</small></td>
                                                                <td><small>{{ Str::limit($planning->lokasi, 20) ?? '-' }}</small></td>
                                                                <td><small>{{ Str::limit($planning->perusahaan_pic, 20) ?? '-' }}</small></td>
                                                                <td>{{ $planning->shift ?? '-' }}</td>
                                                                <td class="karyawan-select-cell">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <select class="form-select form-select-sm karyawan-select"
                                                                            data-planning-id="{{ $planning->id }}"
                                                                            multiple="multiple">
                                                                            @foreach($planning->karyawans as $k)
                                                                                <option value="{{ $k->user_id }}"
                                                                                    data-nama="{{ $k->nama_karyawan }}"
                                                                                    data-sid="{{ $k->sid_karyawan ?? '' }}"
                                                                                    selected>{{ $k->nama_karyawan }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <span class="saving-indicator" id="saving-{{ $planning->id }}">
                                                                            <i class="bx bx-loader-alt bx-spin text-primary"></i>
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $statusColors = ['draft' => 'secondary', 'assigned' => 'primary', 'completed' => 'success'];
                                                                    @endphp
                                                                    <span class="badge bg-{{ $statusColors[$planning->status] ?? 'secondary' }}" id="status-{{ $planning->id }}">{{ ucfirst($planning->status) }}</span>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex gap-1">
                                                                        <button type="button" class="btn btn-sm btn-info rounded-3" onclick="openEditModal({{ $planning->id }}, '{{ addslashes($planning->shift ?? '') }}', '{{ addslashes($planning->kategori_area ?? '') }}', '{{ addslashes($planning->jenis_sap ?? '') }}', '{{ $planning->status }}')" title="Lihat/Edit">
                                                                            <i class="material-icons-outlined text-white">visibility</i>
                                                                        </button>
                                                                        <form action="{{ route('sistem-roster.planning.destroy', $planning->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus planning ini?')">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-danger rounded-3" title="Hapus"><i class="material-icons-outlined">delete</i></button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle fs-1 d-block mb-2"></i>
                                            Belum ada data planning untuk periode ini.
                                            <br>
                                            <small>Klik tombol "Generate dari IKK & DOP" untuk membuat planning.</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center flex-column flex-lg-row gap-3">
                        <div class="text-muted">
                            Menampilkan {{ $plannings->firstItem() ?? 0 }}-{{ $plannings->lastItem() ?? 0 }} dari {{ $plannings->total() }} data
                        </div>
                        {{ $plannings->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Planning</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editShift" class="form-label">Shift</label>
                            <select name="shift" id="editShift" class="form-select">
                                <option value="">-- Pilih Shift --</option>
                                <option value="Shift 1 s/d 2">Shift 1 s/d 2</option>
                                <option value="Shift 2 s/d 1">Shift 2 s/d 1</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editKategoriArea" class="form-label">Kategori Area</label>
                            <input type="text" name="kategori_area" id="editKategoriArea" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="editJenisSap" class="form-label">Jenis SAP</label>
                            <input type="text" name="jenis_sap" id="editJenisSap" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select name="status" id="editStatus" class="form-select">
                                <option value="draft">Draft</option>
                                <option value="assigned">Assigned</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const csrfToken = '{{ csrf_token() }}';
const usersUrl = '{{ route("sistem-roster.planning.users") }}';

function initKaryawanSelect(container) {
    var $scope = container ? $(container) : $(document);
    $scope.find('.karyawan-select').each(function() {
        const $select = $(this);
        if ($select.hasClass('select2-hidden-accessible')) return;
        const planningId = $select.data('planning-id');
        
        $select.select2({
            theme: 'bootstrap-5',
            placeholder: 'Cari karyawan...',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: usersUrl,
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return { search: params.term };
                },
                processResults: function(data) {
                    return {
                        results: data.map(u => ({
                            id: u.id,
                            text: u.nama + ' (' + u.nik + ')',
                            nama: u.nama,
                            nik: u.nik
                        }))
                    };
                },
                cache: true
            }
        });
        
        $select.on('change', function() {
            saveKaryawan(planningId, $select);
        });
    });
}

function saveKaryawan(planningId, $select) {
    const $indicator = $('#saving-' + planningId);
    $indicator.addClass('show');
    
    const selectedData = $select.select2('data');
    const karyawans = selectedData.map((item, idx) => ({
        user_id: item.id,
        nama_karyawan: item.nama || item.text.split(' (')[0],
        sid_karyawan: item.nik || ''
    }));
    
    fetch('/sistem-roster/planning/' + planningId + '/assign-karyawan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ karyawans: karyawans })
    })
    .then(res => res.json())
    .then(data => {
        $indicator.removeClass('show');
        const $status = $('#status-' + planningId);
        if (karyawans.length > 0) {
            $status.removeClass('bg-secondary bg-primary bg-success').addClass('bg-primary').text('Assigned');
            if (data.status === 'assigned' && data.planning) {
                window.lastAssignedPlanningId = data.planning.id;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Karyawan telah di-assign.',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bx bxl-whatsapp me-1"></i> Kirim notifikasi ke WA',
                        cancelButtonText: 'Tutup',
                        confirmButtonColor: '#25D366',
                        customClass: { confirmButton: 'rounded-3', cancelButton: 'rounded-3' }
                    }).then(function(result) {
                        if (result.isConfirmed) openWaNotifModal();
                    });
                }
            }
        } else {
            $status.removeClass('bg-secondary bg-primary bg-success').addClass('bg-secondary').text('Draft');
        }
    })
    .catch(() => {
        $indicator.removeClass('show');
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menyimpan. Silakan coba lagi.' });
        } else {
            alert('Gagal menyimpan. Silakan coba lagi.');
        }
    });
}

function openEditModal(id, shift, kategoriArea, jenisSap, status) {
    document.getElementById('editForm').action = '/sistem-roster/planning/' + id;
    document.getElementById('editShift').value = shift || '';
    document.getElementById('editKategoriArea').value = kategoriArea || '';
    document.getElementById('editJenisSap').value = jenisSap || '';
    document.getElementById('editStatus').value = status || 'draft';
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

// Job Status Polling
let jobPollingInterval = null;
const hasActiveJob = {{ isset($latestJob) && $latestJob ? 'true' : 'false' }};

function checkJobStatus() {
    fetch('{{ route("sistem-roster.planning.job-status") }}')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'not_found') {
                hideJobProgress();
                enableGenerateButton();
                if (jobPollingInterval) {
                    clearInterval(jobPollingInterval);
                    jobPollingInterval = null;
                }
                return;
            }
            
            updateJobProgress(data);
            
            if (data.status === 'completed') {
                if (jobPollingInterval) {
                    clearInterval(jobPollingInterval);
                    jobPollingInterval = null;
                }
                setTimeout(() => location.reload(), 2000);
            } else if (data.status === 'failed') {
                if (jobPollingInterval) {
                    clearInterval(jobPollingInterval);
                    jobPollingInterval = null;
                }
                enableGenerateButton();
            }
        })
        .catch(() => {});
}

function showJobProgress() {
    document.getElementById('jobProgressSection').style.display = 'block';
}

function hideJobProgress() {
    document.getElementById('jobProgressSection').style.display = 'none';
}

function updateJobProgress(data) {
    showJobProgress();
    const dopTotal = (data.dop_created || 0) + (data.dop_updated || 0);
    const ikkTotal = (data.ikk_created || 0) + (data.ikk_updated || 0);
    document.getElementById('dopProgress').textContent = dopTotal;
    document.getElementById('ikkProgress').textContent = ikkTotal;
    
    const badgeEl = document.getElementById('jobStatusBadge');
    const textEl = document.getElementById('jobStatusText');
    
    if (data.status === 'completed') {
        badgeEl.innerHTML = '<span class="badge bg-success">Selesai</span>';
        textEl.innerHTML = '<i class="bx bx-check-circle text-success"></i> Generate selesai! Memuat ulang...';
    } else if (data.status === 'failed') {
        badgeEl.innerHTML = '<span class="badge bg-danger">Gagal</span>';
        textEl.innerHTML = '<i class="bx bx-error text-danger"></i> Gagal: ' + (data.error_message || 'Unknown error');
    }
}

function disableGenerateButton() {
    const btn = document.getElementById('generateBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Processing...';
    }
}

function enableGenerateButton() {
    const btn = document.getElementById('generateBtn');
    if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-refresh"></i> Generate dari IKK & DOP';
    }
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function normalizeWaNumber(selular) {
    if (!selular || typeof selular !== 'string') return '';
    var s = String(selular).replace(/\s+/g, '').replace(/-/g, '').replace(/,/g, '');
    s = s.replace(/\D/g, '');
    if (/^0\d+/.test(s)) s = '62' + s.slice(1);
    else if (!/^62/.test(s) && /^\d+/.test(s)) s = '62' + s;
    return s;
}

function openWaNotifModal() {
    const planningId = window.lastAssignedPlanningId;
    if (!planningId) return;
    if (typeof Swal === 'undefined') return;
    Swal.fire({
        title: 'Kirim notifikasi ke WhatsApp',
        html: '<div class="text-center py-4"><i class="bx bx-loader-alt bx-spin text-primary" style="font-size:2rem"></i><p class="mt-2 small text-muted mb-0">Memuat konten pesan...</p></div>',
        showConfirmButton: false,
        allowOutsideClick: false
    });
    fetch('/sistem-roster/planning/' + planningId + '/wa-message', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
    })
    .then(res => res.json())
    .then(data => {
        const msg = (data.message || '').trim();
        window._waMessageContent = msg;
        var karyawans = data.karyawans || [];
        window._waKaryawans = karyawans;
        var defaultPhone = '';
        var targetLabel = '';
        var hasAnySelular = karyawans.some(function(k) { return k.selular && normalizeWaNumber(k.selular); });
        if (hasAnySelular && karyawans[0].selular) {
            defaultPhone = normalizeWaNumber(karyawans[0].selular);
            if (karyawans.length === 1) {
                targetLabel = '<p class="text-success small mb-2 text-start"><i class="bx bxl-whatsapp me-1"></i> Nomor tujuan: ' + escapeHtml(karyawans[0].nama_karyawan || 'Karyawan') + ' (otomatis dari data karyawan)</p>';
            } else {
                targetLabel = '<p class="text-success small mb-2 text-start"><i class="bx bxl-whatsapp me-1"></i> Nomor tujuan: ' + karyawans.length + ' karyawan (otomatis dari data karyawan). Klik Buka WhatsApp untuk kirim ke semua.</p>';
            }
        }
        if (!targetLabel) targetLabel = '<p class="text-muted small mb-2 text-start">Isi nomor WA atau kosongkan untuk memilih kontak manual.</p>';
        const preStyle = 'white-space:pre-wrap;word-break:break-word;max-height:320px;overflow-y:auto;text-align:left;background:#f8f9fa;border:1px solid #dee2e6;border-radius:0.375rem;padding:1rem;font-size:0.875rem;margin-bottom:1rem;';
        const inputVal = defaultPhone ? escapeHtml(defaultPhone) : '';
        Swal.fire({
            title: 'Kirim notifikasi ke WhatsApp',
            html: targetLabel + '<p class="text-muted small mb-2 text-start">Pesan berisi lokasi yang harus dikunjungi dan summary lokasi.</p><pre id="waMessageText" style="' + preStyle + '">' + escapeHtml(msg) + '</pre><label class="form-label small text-start d-block">Nomor WA (bisa edit jika kirim ke nomor lain)</label><input type="text" class="form-control form-control-sm" id="waPhoneInput" value="' + inputVal + '" placeholder="6281234567890 (otomatis dari karyawan yang dipilih)">',
            showCancelButton: true,
            confirmButtonText: '<i class="bx bxl-whatsapp me-1"></i> Buka WhatsApp',
            cancelButtonText: 'Tutup',
            confirmButtonColor: '#25D366',
            width: '32rem',
            customClass: { confirmButton: 'rounded-3', cancelButton: 'rounded-3' },
            // wa.me tetap ke nomor karyawan yang di-tuju: input sudah diisi otomatis dari selular (vw_user). Buka WhatsApp = wa.me/{nomor}?text=...
            preConfirm: function() {
                const m = window._waMessageContent || '';
                const inp = document.getElementById('waPhoneInput');
                let num = (inp && inp.value || '').trim();
                num = num ? normalizeWaNumber(num) : '';
                var kList = window._waKaryawans || [];
                var numbersToOpen = [];
                if (num) {
                    numbersToOpen.push({ num: num });
                } else if (kList.length > 0) {
                    kList.forEach(function(k) {
                        var n = k.selular ? normalizeWaNumber(k.selular) : '';
                        if (n) numbersToOpen.push({ num: n });
                    });
                }
                if (numbersToOpen.length === 0) {
                    window.open('https://wa.me/?text=' + encodeURIComponent(m), '_blank');
                } else {
                    numbersToOpen.forEach(function(o) {
                        window.open('https://wa.me/' + o.num + '?text=' + encodeURIComponent(m), '_blank');
                    });
                }
            }
        });
    })
    .catch(function() {
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memuat konten pesan.' });
    });
}

function toggleGroupDetail(key, open) {
    var wrapper = document.querySelector('.detail-wrapper-row[data-group-key="' + key + '"]');
    var btn = document.querySelector('.expand-btn[data-group-key="' + key + '"]');
    if (!wrapper || !btn) return;
    if (open) {
        wrapper.style.display = '';
        btn.setAttribute('aria-expanded', 'true');
        btn.innerHTML = '<i class="expand-icon bx bx-minus"></i> Tutup';
        initKaryawanSelect(wrapper);
    } else {
        wrapper.style.display = 'none';
        btn.setAttribute('aria-expanded', 'false');
        btn.innerHTML = '<i class="expand-icon bx bx-plus"></i> Buka detail';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Expand: klik "Buka detail" di baris ringkas → tampil tabel detail (No, Tanggal, Sumber, ..., Karyawan, Status, Aksi)
    document.querySelectorAll('.expand-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var key = this.getAttribute('data-group-key');
            var expanded = this.getAttribute('aria-expanded') === 'true';
            toggleGroupDetail(key, !expanded);
        });
    });
    // Tutup: klik "Tutup detail" di dalam panel detail
    document.querySelectorAll('.collapse-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var key = this.getAttribute('data-group-key');
            toggleGroupDetail(key, false);
        });
    });

    // Tab per site: klik tab set filter_site dan submit form
    document.querySelectorAll('#siteTabs [data-site]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var site = this.getAttribute('data-site');
            var filterSite = document.getElementById('filter_site');
            if (filterSite) {
                filterSite.value = site;
                document.getElementById('filterForm').submit();
            }
        });
    });

    if (hasActiveJob) {
        showJobProgress();
        disableGenerateButton();
        checkJobStatus();
        jobPollingInterval = setInterval(checkJobStatus, 3000);
    }
});
</script>
@endsection
