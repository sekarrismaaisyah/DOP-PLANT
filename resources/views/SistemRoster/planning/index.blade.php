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
        <div class="col-8">
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
                    <i class="bx bx-info-circle"></i>Jika belum muncul data IKK/DOP, Klik tombol Generate Planning di samping ini </i>
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
         <div class="col-4 my-auto">
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
                        <div class="col-lg-12">
                            <form method="GET" action="{{ route('sistem-roster.planning.index') }}" id="filterForm" class="row g-3 align-items-end">
                               
                                <div class="col-md-2">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $filterStartDate }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $filterEndDate }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="filter_site" class="form-label">Filter Site</label>
                                    <select name="filter_site" id="filter_site" class="form-select">
                                        <option value="">-- Semua Site --</option>
                                        @foreach($planningFilterSites ?? [] as $s)
                                            <option value="{{ $s }}" {{ ($filterSite ?? '') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="filter_perusahaan" class="form-label">Filter Perusahaan</label>
                                    <select name="filter_perusahaan" id="filter_perusahaan" class="form-select">
                                        <option value="">-- Semua Perusahaan --</option>
                                        @foreach($perusahaanList ?? [] as $p)
                                            <option value="{{ $p }}" {{ ($filterPerusahaan ?? '') == $p ? 'selected' : '' }}>{{ Str::limit($p, 40) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
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
                        
                        <!-- <div class="col-lg-4">
                            <form method="POST" action="{{ route('sistem-roster.planning.generate') }}" class="d-flex justify-content-end align-items-end h-100">
                                @csrf
                                <input type="hidden" name="start_date" value="{{ $filterStartDate }}">
                                <input type="hidden" name="end_date" value="{{ $filterEndDate }}">
                                <button type="submit" class="btn btn-success" id="generateBtn">
                                    <i class="bx bx-refresh"></i> Generate Planning
                                </button>
                            </form>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs per site: sama urutan dengan dropdown; klik = filter + reload (DOP + IKK + Roster acuan per site) -->
    @php
        $currentFilterSite = $filterSite ?? '';
        $planningSiteTabsList = $planningSiteTabs ?? [];
    @endphp
    <div class="row mb-3">
        <div class="col-12">
           
            <ul class="nav nav-tabs nav-tabs-custom flex-wrap" id="siteTabs" role="tablist" aria-label="Filter site planning">
                @foreach($planningSiteTabsList as $tab)
                    @php
                        $tabValue = $tab['value'] ?? '';
                        $tabLabel = $tab['label'] ?? '';
                        $tabSlug = $tab['slug'] ?? 'tab';
                        $isActive = ($currentFilterSite === $tabValue);
                    @endphp
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $isActive ? 'active' : '' }} rounded-3 px-3 py-2"
                                type="button"
                                data-site="{{ $tabValue }}"
                                id="tab-{{ $tabSlug }}"
                                role="tab"
                                aria-selected="{{ $isActive ? 'true' : 'false' }}">
                            {{ $tabLabel }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

  

    {{-- Bahan Evaluasi: per lokasi + detail lokasi — kapan terakhir ada data inspeksi hazard & subketidaksesuaian --}}
  

    <!-- Planning Table (referensi: dashboard-weekly Data IKK Weekly) -->
    <div class="row">
        <div class="col-12">
            <div class="card rounded-4 border-0 shadow-sm mt-2">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-0 fw-bold">
                            Daftar Planning
                            @if($plannings->isNotEmpty())
                                <span class="text-muted fw-normal fs-6">({{ $plannings->firstItem() ?? 0 }}-{{ $plannings->lastItem() ?? 0 }} dari {{ $plannings->total() }})</span>
                            @endif
                        </h5>
                        <small class="text-muted">Ringkas per Tanggal, Site & Jenis. Klik tombol [+] untuk melihat detail aktivitas dan assign karyawan.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm" id="btnInputIkkManual" title="Tambah IKK dari hse_automation.ikk_work_permit ke planning">
                            <i class="bx bx-plus-circle me-1"></i> Input Manual Jika Tidak ada IKK pada hari ini
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(count($grouped ?? []) > 0 || count($groupedRoster ?? []) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 w-100" id="planningSummaryTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Site</th>
                                        <th>Jenis</th>
                                        <th>Aktivitas</th>
                                        <th class="text-center">Total Aktivitas</th>
                                        <th class="text-center">Total Assigned</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $detailIndex = (int) $plannings->firstItem() - 1; @endphp
                                    @foreach($grouped as $groupKey => $items)
                                        @php
                                            $first = $items->first();
                                            $tanggal = $first->tanggal;
                                            $site = $first->site ?? '-';
                                            $jenis = $first->source_type ?? '-';
                                            $totalAktivitas = $items->count();
                                            $totalAssigned = $items->filter(fn($p) => $p->karyawans->count() > 0)->count();
                                        @endphp
                                        {{-- Main Row --}}
                                        <tr class="planning-main-row" data-group-key="{{ $groupKey }}">
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary planning-toggle-btn p-0"
                                                        data-target="planning-detail-{{ $loop->iteration }}"
                                                        style="width: 28px; height: 28px; line-height: 1;">
                                                    <i class="material-icons-outlined" style="font-size: 18px;">add</i>
                                                </button>
                                            </td>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $tanggal->format('d M Y') }}</td>
                                            <td>{{ $site }}</td>
                                            <td>
                                                <span class="badge bg-{{ $jenis === 'DOP' ? 'secondary' : 'info' }}">{{ $jenis }}</span>
                                            </td>
                                            <td>
                                                @if(strtoupper($jenis) === 'IKK' || strtoupper($jenis) === 'DOP')
                                                    <span class="badge bg-warning text-dark">Highrisk/Kritis</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center"><strong>{{ $totalAktivitas }}</strong></td>
                                            <td class="text-center"><strong>{{ $totalAssigned }}</strong></td>
                                        </tr>
                                        {{-- Detail Row (Hidden by default) --}}
                                        <tr class="planning-detail-row d-none" id="planning-detail-{{ $loop->iteration }}">
                                            <td colspan="8" class="p-0 bg-light">
                                                <div class="p-3">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <small class="text-muted fw-semibold">
                                                            <i class="bx bx-list-ul me-1"></i> Detail aktivitas — assign karyawan di bawah
                                                        </small>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered table-hover align-middle mb-0 bg-white">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th style="width: 40px">No</th>
                                                                    <th>Tanggal</th>
                                                                    <th>Sumber</th>
                                                                    <th>Site</th>
                                                                    <th>No IKK</th>
                                                                    <th>Aktivitas</th>
                                                                    <th>Lokasi</th>
                                                                    <th>Detail Lokasi</th>
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
                                                                        <td><small>{{ Str::limit($planning->detail_lokasi ?? '-', 25) }}</small></td>
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
                                                                                    <i class="material-icons-outlined text-white" style="font-size: 16px;">visibility</i>
                                                                                </button>
                                                                                <form action="{{ route('sistem-roster.planning.destroy', $planning->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus planning ini?')">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-sm btn-danger rounded-3" title="Hapus"><i class="material-icons-outlined" style="font-size: 16px;">delete</i></button>
                                                                                </form>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    {{-- Baris Roster (acuan dari roster_bmo1, roster_bmo3, roster_gmo, roster_hote, roster_lmo) — Save ke Planning --}}
                                    @php $rosterRowIndex = 0; @endphp
                                    @foreach($groupedRoster ?? [] as $rosterKey => $rosterItems)
                                        @php
                                            $rosterRowIndex++;
                                            $firstRoster = $rosterItems->first();
                                            $rosterTanggal = $firstRoster->date_ins ? \Carbon\Carbon::parse($firstRoster->date_ins) : null;
                                            $rosterSite = $firstRoster->site ?? '-';
                                            $rosterTable = $firstRoster->roster_table ?? '';
                                            $existingKey = $rosterTanggal ? $rosterTanggal->format('Y-m-d') . '|' . $rosterSite . '|' . $rosterTable : '';
                                            $alreadySaved = in_array($existingKey, $existingRosterKeys ?? [], true);
                                        @endphp
                                        <tr class="planning-main-row roster-reference-row" data-roster-key="{{ $rosterKey }}">
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary planning-toggle-btn p-0"
                                                        data-target="planning-detail-roster-{{ $rosterRowIndex }}"
                                                        style="width: 28px; height: 28px; line-height: 1;">
                                                    <i class="material-icons-outlined" style="font-size: 18px;">add</i>
                                                </button>
                                            </td>
                                            <td>{{ count($grouped ?? []) + $rosterRowIndex }}</td>
                                            <td>{{ $rosterTanggal ? $rosterTanggal->format('d M Y') : '-' }}</td>
                                            <td>{{ $rosterSite }}</td>
                                            <td>
                                                <span class="badge bg-success">Roster</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">Non Area Kritis</span>
                                            </td>
                                            <td class="text-center"><strong>{{ $rosterItems->count() }}</strong></td>
                                            <td class="text-center">{{ $alreadySaved ? $rosterItems->count() : '-' }}</td>
                                        </tr>
                                        <tr class="planning-detail-row d-none" id="planning-detail-roster-{{ $rosterRowIndex }}">
                                            <td colspan="8" class="p-0 bg-light">
                                                <div class="p-3">
                                                    <!-- <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                            <small class="text-muted fw-semibold mb-0">
                                                                <i class="bx bx-list-ul me-1"></i> Data roster (acuan) — Non Area Kritis. Klik "Save ke Planning" untuk memasukkan ke daftar planning.
                                                            </small>
                                                            @if($alreadySaved)
                                                                <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bx bx-check-circle me-1"></i> Sudah di-Planning</span>
                                                            @else
                                                                <button type="button" class="btn btn-success btn-save-roster rounded-pill px-3 shadow-sm"
                                                                        data-tanggal="{{ $rosterTanggal ? $rosterTanggal->format('Y-m-d') : '' }}"
                                                                        data-roster-table="{{ $rosterTable }}"
                                                                        data-site="{{ $rosterSite }}">
                                                                    <i class="bx bx-save me-1"></i> Save
                                                                </button>
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-warning btn-reset-roster-exclusions"
                                                                data-tanggal="{{ $rosterTanggal ? $rosterTanggal->format('Y-m-d') : '' }}"
                                                                data-roster-table="{{ $rosterTable }}"
                                                                data-site="{{ $rosterSite }}"
                                                                title="Kembalikan semua lokasi acuan yang telah dihapus">
                                                            <i class="bx bx-reset me-1"></i> Setting ulang
                                                        </button>
                                                    </div> -->
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered table-hover align-middle mb-0 bg-white">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th style="width: 40px">No</th>
                                                                    <th>Tanggal</th>
                                                                    <th>Nama</th>
                                                                    <th>Lokasi</th>
                                                                    <th>Detail Lokasi</th>
                                                                    <th>Hazard Inspeksi</th>
                                                                    <th style="width: 70px" class="text-center">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $rosterNameGroups = [];
                                                                    $currentName = null;
                                                                    $currentGroup = [];
                                                                    foreach ($rosterItems as $r) {
                                                                        $nama = $r->nama ?? 'Tanpa Nama';
                                                                        if ($nama !== $currentName) {
                                                                            if (!empty($currentGroup)) {
                                                                                $rosterNameGroups[] = ['nama' => $currentName, 'rows' => $currentGroup];
                                                                            }
                                                                            $currentName = $nama;
                                                                            $currentGroup = [$r];
                                                                        } else {
                                                                            $currentGroup[] = $r;
                                                                        }
                                                                    }
                                                                    if (!empty($currentGroup)) {
                                                                        $rosterNameGroups[] = ['nama' => $currentName, 'rows' => $currentGroup];
                                                                    }
                                                                    $rosterRowNo = 0;
                                                                @endphp
                                                                @foreach($rosterNameGroups as $group)
                                                                    @foreach($group['rows'] as $subIdx => $r)
                                                                        @php $rosterRowNo++; @endphp
                                                                        <tr>
                                                                            <td>{{ $rosterRowNo }}</td>
                                                                            <td>{{ $r->date_ins ? \Carbon\Carbon::parse($r->date_ins)->format('d M Y') : '-' }}</td>
                                                                            @if($subIdx === 0)
                                                                                <td rowspan="{{ count($group['rows']) }}" class="align-top">{{ $group['nama'] }}</td>
                                                                            @endif
                                                                            <td><small>{{ $r->lokasi ?? '-' }}</small></td>
                                                                            <td><small>{{ $r->detail_lokasi ?? '-' }}</small></td>
                                                                            <td class="text-start">
                                                                                @php
                                                                                    $lastDate = $r->last_inspeksi_date ?? null;
                                                                                    $subket = $r->last_inspeksi_subketidaksesuaian ?? null;
                                                                                @endphp
                                                                                @if($lastDate || $subket)
                                                                                    @if($lastDate)
                                                                                        <div class="small"><strong>Terakhir ada data:</strong> {{ \Carbon\Carbon::parse($lastDate)->format('d/m/Y') }}</div>
                                                                                    @endif
                                                                                    @if($subket !== null && $subket !== '')
                                                                                        <div class="small text-muted" title="Subketidaksesuaian"><strong>Subketidaksesuaian:</strong> {{ $subket }}</div>
                                                                                    @endif
                                                                                @else
                                                                                    <span class="text-muted">Belum ada data inspeksi hazard</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <button type="button" class="btn btn-sm btn-outline-danger btn-exclude-roster-location p-1"
                                                                                        title="Hapus lokasi ini dari acuan (bisa dikembalikan dengan Setting ulang)"
                                                                                        data-tanggal="{{ $rosterTanggal ? $rosterTanggal->format('Y-m-d') : '' }}"
                                                                                        data-roster-table="{{ $rosterTable }}"
                                                                                        data-site="{{ $rosterSite }}"
                                                                                        data-nama="{{ e($r->nama ?? '') }}"
                                                                                        data-lokasi="{{ e($r->lokasi ?? '') }}"
                                                                                        data-detail-lokasi="{{ e($r->detail_lokasi ?? '') }}">
                                                                                        <i class="material-icons-outlined">delete</i>
                                                                                </button>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center flex-column flex-lg-row gap-3 p-3 border-top">
                            <div class="text-muted small">
                                Menampilkan {{ $plannings->firstItem() ?? 0 }}-{{ $plannings->lastItem() ?? 0 }} dari {{ $plannings->total() }} data planning
                                @if(count($groupedRoster ?? []) > 0)
                                    <span class="ms-2">| {{ count($groupedRoster) }} grup Roster (acuan)</span>
                                @endif
                            </div>
                            {{ $plannings->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="bx bx-info-circle" style="font-size: 48px;"></i>
                            <p class="mb-0 mt-2">Belum ada data planning untuk periode ini.</p>
                            <small>Klik tombol "Generate Planning" untuk membuat planning dari IKK & DOP.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(count($groupedRoster ?? []) > 0)
        @php
            $firstRosterGroup = collect($groupedRoster)->first();
            $firstRosterForToolbar = $firstRosterGroup->first();
            $toolbarTanggal = $firstRosterForToolbar->date_ins ? \Carbon\Carbon::parse($firstRosterForToolbar->date_ins) : null;
            $toolbarSite = $firstRosterForToolbar->site ?? '-';
            $toolbarTable = $firstRosterForToolbar->roster_table ?? '';
            $toolbarExistingKey = $toolbarTanggal ? $toolbarTanggal->format('Y-m-d') . '|' . $toolbarSite . '|' . $toolbarTable : '';
            $toolbarAlreadySaved = in_array($toolbarExistingKey, $existingRosterKeys ?? [], true);
        @endphp
        <div class="row mb-3 mt-2">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <small class="text-muted fw-semibold mb-0">
                            <i class="bx bx-list-ul me-1"></i> Data roster (acuan) — Non Area Kritis. Klik "Save ke Planning" untuk memasukkan ke daftar planning.
                        </small>
                        @if($toolbarAlreadySaved)
                            <span class="badge bg-success px-3 py-2 rounded-pill">
                                <i class="bx bx-check-circle me-1"></i> Sudah di-Planning
                            </span>
                        @else
                            <button type="button" class="btn btn-success btn-save-roster rounded-pill px-3 shadow-sm"
                                    data-tanggal="{{ $toolbarTanggal ? $toolbarTanggal->format('Y-m-d') : '' }}"
                                    data-roster-table="{{ $toolbarTable }}"
                                    data-site="{{ $toolbarSite }}">
                                <i class="bx bx-save me-1"></i> Save
                            </button>
                        @endif
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-warning btn-reset-roster-exclusions"
                            data-tanggal="{{ $toolbarTanggal ? $toolbarTanggal->format('Y-m-d') : '' }}"
                            data-roster-table="{{ $toolbarTable }}"
                            data-site="{{ $toolbarSite }}"
                            title="Kembalikan semua lokasi acuan yang telah dihapus">
                        <i class="bx bx-reset me-1"></i> Setting ulang
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Summary per orang: gabungan IKK, DOP, dan Roster — tabel grouping, klik + untuk detail lokasi --}}
    @if(count($summaryByPersonMerged ?? []) > 0)
        <div class="row mb-3 mt-3">
            <div class="col-12">
                <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-0 fw-bold"><i class="bx bx-user-check me-1"></i> Summary per orang — harus mengunjungi</h6>
                            <small class="text-muted">Grouping per nama. Klik tombol [+] untuk melihat detail lokasi dan detail lokasi.</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnExpandAllSummary" title="Expand All"><i class="material-icons-outlined" style="font-size: 16px;">unfold_more</i></button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnCollapseAllSummary" title="Collapse All"><i class="material-icons-outlined" style="font-size: 16px;">unfold_less</i></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover align-middle mb-0" id="summaryTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px"></th>
                                        <th style="width: 40px">No</th>
                                        <th>Nama</th>
                                        <th>Site</th>
                                        <th class="text-center">Total kunjungan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summaryByPersonMerged as $nama => $items)
                                        @php $idx = $loop->iteration; @endphp
                                        <tr class="summary-main-row">
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary planning-toggle-btn p-0"
                                                        data-target="summary-detail-{{ $idx }}"
                                                        style="width: 28px; height: 28px; line-height: 1;">
                                                    <i class="material-icons-outlined" style="font-size: 18px;">add</i>
                                                </button>
                                            </td>
                                            <td>{{ $idx }}</td>
                                            <td><strong>{{ $nama }}</strong></td>
                                            <td>
                                                @php
                                                    $sites = collect($items)->pluck('site')->filter()->unique()->values();
                                                @endphp
                                                @if($sites->isEmpty())
                                                    <span class="text-muted">-</span>
                                                @else
                                                    @foreach($sites as $site)
                                                        <span class="badge bg-light text-dark me-1 mb-1">{{ $site }}</span>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="text-center"><strong>{{ $items->count() }}</strong></td>
                                        </tr>
                                        <tr class="planning-detail-row d-none" id="summary-detail-{{ $idx }}">
                                            <td colspan="4" class="p-0 bg-light">
                                                <div class="p-3">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered table-hover align-middle mb-0 bg-white">
                                                            <thead class="table-secondary">
                                                                <tr>
                                                                    <th style="width: 40px">No</th>
                                                                    <th>Tanggal</th>
                                                                    <th>Site</th>
                                                                    <th>Sumber</th>
                                                                    <th>Lokasi</th>
                                                                    <th>Detail Lokasi</th>
                                                                    <th>Aktivitas</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($items as $subNo => $it)
                                                                    <tr>
                                                                        <td>{{ $subNo + 1 }}</td>
                                                                        <td>{{ $it->tanggal ? ($it->tanggal instanceof \DateTimeInterface ? $it->tanggal->format('d/m/Y') : \Carbon\Carbon::parse($it->tanggal)->format('d/m/Y')) : '-' }}</td>
                                                                        <td><span class="badge bg-light text-dark">{{ $it->site ?? '-' }}</span></td>
                                                                        <td>
                                                                            @php
                                                                                $badgeClass = 'bg-secondary';
                                                                                if (($it->source_type ?? '') === 'IKK') $badgeClass = 'bg-info';
                                                                                elseif (($it->source_type ?? '') === 'DOP') $badgeClass = 'bg-secondary';
                                                                                elseif (($it->source_type ?? '') === 'Roster') $badgeClass = 'bg-success';
                                                                            @endphp
                                                                            <span class="badge {{ $badgeClass }}">{{ $it->source_type ?? '-' }}</span>
                                                                        </td>
                                                                        <td><small>{{ $it->lokasi ?? '-' }}</small></td>
                                                                        <td><small class="text-muted">{{ $it->detail_lokasi ?? '-' }}</small></td>
                                                                        <td><small>{{ Str::limit($it->aktivitas ?? '-', 40) }}</small></td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
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
        </div>
    @endif

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

    {{-- Modal: Simpan ke Planning & Kirim Email Summary --}}
    <div class="modal fade" id="modalSendPlanningEmail" tabindex="-1" aria-labelledby="modalSendPlanningEmailLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalSendPlanningEmailLabel">
                        <i class="bx bx-save me-2 text-success"></i> Simpan ke Planning & Kirim Email
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-1">
                    <p class="text-muted small mb-3">Data roster akan disimpan ke daftar planning. Anda dapat mengirimkan summary planning ke email berikut (opsional).</p>
                    <label for="planningEmailRecipients" class="form-label fw-semibold">Kirim summary ke email <span class="text-muted fw-normal">(pisahkan dengan koma atau baris baru)</span></label>
                    <textarea class="form-control" id="planningEmailRecipients" rows="4" placeholder="email1@contoh.com, email2@contoh.com"></textarea>
                    <div class="form-text">Kosongkan jika hanya ingin menyimpan tanpa mengirim email.</div>
                </div>
                <div class="modal-footer border-0 pt-0 flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-outline-success" id="btnSavePlanningOnly">
                        <i class="bx bx-save me-1"></i> Simpan saja
                    </button>
                    <button type="button" class="btn btn-success" id="btnSaveAndSendEmail">
                        <i class="bx bx-send me-1"></i> Simpan & Kirim Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Input IKK Manual (pilih IKK dari ikk_work_permit, simpan ke roster_plannings) --}}
    <div class="modal fade" id="modalInputIkkManual" tabindex="-1" aria-labelledby="modalInputIkkManualLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalInputIkkManualLabel">
                        <i class="bx bx-plus-circle me-2 text-primary"></i> Input IKK Manual ke Planning
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formInputIkkManual">
                    <div class="modal-body pt-1">
                        <p class="text-muted small mb-3">Pilih IKK dari <strong>hse_automation.ikk_work_permit</strong>. Setelah disimpan, IKK akan muncul di daftar planning.</p>
                        <div class="mb-3">
                            <label for="ikkManualTanggal" class="form-label fw-semibold">Tanggal Planning</label>
                            <input type="date" class="form-control" id="ikkManualTanggal" name="tanggal" value="{{ $filterStartDate ?? now()->toDateString() }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="selectIkkManual" class="form-label fw-semibold">Pilih IKK</label>
                            <select id="selectIkkManual" name="ikk_id" class="form-select" style="width:100%;" required>
                                <option value="">-- Cari / pilih IKK --</option>
                            </select>
                            <div class="form-text">Ketik kode, site, lokasi, atau perusahaan untuk mencari.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSimpanIkkManual">
                            <i class="bx bx-save me-1"></i> Simpan ke Planning
                        </button>
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
const ikkListUrl = '{{ route("sistem-roster.planning.ikk-list") }}';
const storeIkkManualUrl = '{{ route("sistem-roster.planning.store-ikk-manual") }}';

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

document.addEventListener('DOMContentLoaded', function() {
    // Toggle individual planning row (referensi: dashboard-weekly IKK toggle)
    document.querySelectorAll('.planning-toggle-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var targetRow = document.getElementById(targetId);
            var icon = this.querySelector('i');
            if (!targetRow || !icon) return;
            if (targetRow.classList.contains('d-none')) {
                targetRow.classList.remove('d-none');
                icon.textContent = 'remove';
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                initKaryawanSelect(targetRow);
            } else {
                targetRow.classList.add('d-none');
                icon.textContent = 'add';
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-primary');
            }
        });
    });

    // Expand All
    var btnExpandAll = document.getElementById('btnExpandAllPlanning');
    if (btnExpandAll) {
        btnExpandAll.addEventListener('click', function() {
            document.querySelectorAll('.planning-detail-row').forEach(function(row) {
                row.classList.remove('d-none');
                initKaryawanSelect(row);
            });
            document.querySelectorAll('.planning-toggle-btn').forEach(function(btn) {
                var icon = btn.querySelector('i');
                if (icon) icon.textContent = 'remove';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
            });
        });
    }

    // Collapse All
    var btnCollapseAll = document.getElementById('btnCollapseAllPlanning');
    if (btnCollapseAll) {
        btnCollapseAll.addEventListener('click', function() {
            document.querySelectorAll('.planning-detail-row').forEach(function(row) {
                row.classList.add('d-none');
            });
            document.querySelectorAll('.planning-toggle-btn').forEach(function(btn) {
                var icon = btn.querySelector('i');
                if (icon) icon.textContent = 'add';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
        });
    }

    // Input IKK Manual: buka modal, init Select2 (AJAX dari ikk_work_permit), submit simpan ke roster_plannings
    var btnInputIkkManual = document.getElementById('btnInputIkkManual');
    var modalInputIkkManual = document.getElementById('modalInputIkkManual');
    if (btnInputIkkManual && modalInputIkkManual) {
        var selectIkkManualInited = false;
        btnInputIkkManual.addEventListener('click', function() {
            document.getElementById('ikkManualTanggal').value = '{{ $filterStartDate ?? now()->toDateString() }}';
            var $sel = $('#selectIkkManual');
            if ($sel.length && $sel.hasClass('select2-hidden-accessible')) {
                $sel.val(null).trigger('change');
            } else {
                $sel.val('');
            }
            var modal = new bootstrap.Modal(modalInputIkkManual);
            modal.show();
            if (!selectIkkManualInited) {
                selectIkkManualInited = true;
                $(modalInputIkkManual).one('shown.bs.modal', function() {
                    var $sel = $('#selectIkkManual');
                    if ($sel.length && !$sel.hasClass('select2-hidden-accessible')) {
                        $sel.select2({
                            theme: 'bootstrap-5',
                            placeholder: 'Ketik kode, site, lokasi, atau perusahaan...',
                            allowClear: true,
                            minimumInputLength: 0,
                            dropdownParent: $('#modalInputIkkManual'),
                            ajax: {
                                url: ikkListUrl,
                                dataType: 'json',
                                delay: 300,
                                data: function(params) {
                                    return { q: params.term || '', limit: 30 };
                                },
                                processResults: function(data) {
                                    return { results: data.results || [] };
                                }
                            }
                        });
                    }
                });
            }
        });
    }

    document.getElementById('formInputIkkManual') && document.getElementById('formInputIkkManual').addEventListener('submit', function(e) {
        e.preventDefault();
        var form = this;
        var tanggal = document.getElementById('ikkManualTanggal').value;
        var ikkId = document.getElementById('selectIkkManual').value;
        if (!tanggal || !ikkId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih tanggal dan IKK.' });
            } else {
                alert('Pilih tanggal dan IKK.');
            }
            return;
        }
        var btn = document.getElementById('btnSimpanIkkManual');
        if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...'; }
        fetch(storeIkkManualUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: 'tanggal=' + encodeURIComponent(tanggal) + '&ikk_id=' + encodeURIComponent(ikkId)
        })
        .then(function(res) { return res.json().then(function(data) { return { ok: res.ok, data: data }; }); })
        .then(function(r) {
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save me-1"></i> Simpan ke Planning'; }
            if (r.ok && r.data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: r.data.message || 'IKK berhasil ditambahkan ke planning.' }).then(function() {
                        window.location.reload();
                    });
                } else {
                    alert(r.data.message || 'IKK berhasil ditambahkan.');
                    window.location.reload();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: r.data.message || 'Gagal menyimpan IKK.' });
                } else {
                    alert(r.data.message || 'Gagal menyimpan IKK.');
                }
            }
        })
        .catch(function() {
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save me-1"></i> Simpan ke Planning'; }
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menyimpan. Silakan coba lagi.' });
            } else {
                alert('Gagal menyimpan. Silakan coba lagi.');
            }
        });
    });

    // Summary table: Expand All / Collapse All (hanya di dalam #summaryTable)
    var summaryTable = document.getElementById('summaryTable');
    var btnExpandSummary = document.getElementById('btnExpandAllSummary');
    if (summaryTable && btnExpandSummary) {
        btnExpandSummary.addEventListener('click', function() {
            summaryTable.querySelectorAll('.planning-detail-row').forEach(function(row) { row.classList.remove('d-none'); });
            summaryTable.querySelectorAll('.planning-toggle-btn').forEach(function(btn) {
                var icon = btn.querySelector('i');
                if (icon) icon.textContent = 'remove';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
            });
        });
    }
    var btnCollapseSummary = document.getElementById('btnCollapseAllSummary');
    if (summaryTable && btnCollapseSummary) {
        btnCollapseSummary.addEventListener('click', function() {
            summaryTable.querySelectorAll('.planning-detail-row').forEach(function(row) { row.classList.add('d-none'); });
            summaryTable.querySelectorAll('.planning-toggle-btn').forEach(function(btn) {
                var icon = btn.querySelector('i');
                if (icon) icon.textContent = 'add';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
        });
    }

    // Save Roster ke Planning: klik tombol → buka modal pilih kirim email
    var saveRosterModalData = { btn: null, tanggal: '', rosterTable: '', site: '' };
    document.body.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-save-roster');
        if (!btn) return;
        e.preventDefault();
        var tanggal = btn.getAttribute('data-tanggal');
        var rosterTable = btn.getAttribute('data-roster-table');
        var site = btn.getAttribute('data-site') || '';
        if (!tanggal || !rosterTable) return;
        if (btn.disabled) return;
        saveRosterModalData = { btn: btn, tanggal: tanggal, rosterTable: rosterTable, site: site };
        document.getElementById('planningEmailRecipients').value = '';
        var modal = new bootstrap.Modal(document.getElementById('modalSendPlanningEmail'));
        modal.show();
    });

    function parseEmailRecipients(text) {
        if (!text || !String(text).trim()) return [];
        return String(text).split(/[\s,;]+/).map(function(s) { return s.trim(); }).filter(Boolean);
    }

    function doSaveRosterToPlanning(emails) {
        var data = saveRosterModalData;
        if (!data.tanggal || !data.rosterTable) return;
        var btn = data.btn;
        var payload = { tanggal: data.tanggal, roster_table: data.rosterTable };
        if (emails && emails.length) payload.emails = emails;
        btn.disabled = true;
        var origHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Menyimpan...';
        var modalEl = document.getElementById('modalSendPlanningEmail');
        if (modalEl) bootstrap.Modal.getInstance(modalEl).hide();
        fetch('{{ route("sistem-roster.planning.save-roster") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message || 'Data roster berhasil disimpan.' + (emails && emails.length ? ' Email akan dikirim.' : '') }).then(function() { window.location.reload(); });
                } else {
                    window.location.reload();
                }
            } else {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                alert(res.message || 'Gagal menyimpan');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            alert('Gagal menyimpan. Silakan coba lagi.');
        });
    }

    document.getElementById('btnSavePlanningOnly').addEventListener('click', function() {
        doSaveRosterToPlanning([]);
    });
    document.getElementById('btnSaveAndSendEmail').addEventListener('click', function() {
        var raw = document.getElementById('planningEmailRecipients').value;
        var emails = parseEmailRecipients(raw);
        if (!emails.length) {
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Email kosong', text: 'Masukkan minimal satu alamat email untuk mengirim summary.' });
            else alert('Masukkan minimal satu alamat email.');
            return;
        }
        doSaveRosterToPlanning(emails);
    });

    // Hapus lokasi dari acuan roster (take out) — setelah itu bisa Setting ulang
    document.body.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-exclude-roster-location');
        if (!btn) return;
        e.preventDefault();
        if (btn.disabled) return;
        var tanggal = btn.getAttribute('data-tanggal');
        var rosterTable = btn.getAttribute('data-roster-table');
        var site = btn.getAttribute('data-site');
        var nama = btn.getAttribute('data-nama') || '';
        var lokasi = btn.getAttribute('data-lokasi') || '';
        var detailLokasi = btn.getAttribute('data-detail-lokasi') || '';
        if (!tanggal || !rosterTable || !site) return;
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus lokasi dari acuan?',
                text: 'Lokasi dan detail lokasi ini akan disembunyikan dari daftar. Anda bisa mengembalikan dengan tombol "Setting ulang".',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus dari acuan',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                btn.disabled = true;
                fetch('{{ route("sistem-roster.planning.exclude-roster-location") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ tanggal: tanggal, roster_table: rosterTable, site: site, nama: nama, lokasi: lokasi, detail_lokasi: detailLokasi })
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) { window.location.reload(); } else { btn.disabled = false; alert(data.message || 'Gagal'); }
                })
                .catch(function() { btn.disabled = false; alert('Gagal. Silakan coba lagi.'); });
            });
        } else {
            if (!confirm('Hapus lokasi ini dari acuan?')) return;
            btn.disabled = true;
            fetch('{{ route("sistem-roster.planning.exclude-roster-location") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ tanggal: tanggal, roster_table: rosterTable, site: site, nama: nama, lokasi: lokasi, detail_lokasi: detailLokasi })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) { window.location.reload(); } else { btn.disabled = false; alert(data.message || 'Gagal'); }
            })
            .catch(function() { btn.disabled = false; alert('Gagal. Silakan coba lagi.'); });
        }
    });

    // Setting ulang: kembalikan semua lokasi acuan yang dihapus untuk grup ini
    document.body.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-reset-roster-exclusions');
        if (!btn) return;
        e.preventDefault();
        if (btn.disabled) return;
        var tanggal = btn.getAttribute('data-tanggal');
        var rosterTable = btn.getAttribute('data-roster-table');
        var site = btn.getAttribute('data-site');
        if (!tanggal || !rosterTable || !site) return;
        btn.disabled = true;
        var origHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> ...';
        fetch('{{ route("sistem-roster.planning.reset-roster-exclusions") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ tanggal: tanggal, roster_table: rosterTable, site: site })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Berhasil', text: data.message }).then(function() { window.location.reload(); });
                else { alert(data.message); window.location.reload(); }
            } else {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                alert(data.message || 'Gagal');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            alert('Gagal. Silakan coba lagi.');
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
