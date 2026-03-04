@extends('layouts.masterRoster')

@section('title', 'Roster Planning')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
.select2-container { width: 100% !important; }
.select2-container--bootstrap-5 .select2-selection { min-height: 32px; font-size: 0.875rem; }
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice { font-size: 0.75rem; padding: 2px 6px; }
.karyawan-select-cell { min-width: 250px; }
.saving-indicator { display: none; }
.saving-indicator.show { display: inline-block; }
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
                            <form method="GET" action="{{ route('sistem-roster.planning.index') }}" class="row g-3 align-items-end">
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

    <!-- Planning Table -->
    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-4">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px">No</th>
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
                                    <th style="width: 80px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($plannings as $idx => $planning)
                                    <tr data-planning-id="{{ $planning->id }}">
                                        <td>{{ $plannings->firstItem() + $idx }}</td>
                                        <td>{{ $planning->tanggal->format('d M Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $planning->source_type === 'DOP' ? 'secondary' : 'info' }}">
                                                {{ $planning->source_type }}
                                            </span>
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
                                                            data-sid="{{ $k->sid_karyawan }}"
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
                                                $statusColors = [
                                                    'draft' => 'secondary',
                                                    'assigned' => 'primary',
                                                    'completed' => 'success',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$planning->status] ?? 'secondary' }}" id="status-{{ $planning->id }}">
                                                {{ ucfirst($planning->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-info rounded-3" 
                                                    onclick="openEditModal({{ $planning->id }}, '{{ $planning->shift }}', '{{ $planning->kategori_area }}', '{{ $planning->jenis_sap }}', '{{ $planning->status }}')" 
                                                    title="Edit">
                                                    <i class="material-icons-outlined text-white">visibility</i>
                                                </button>
                                                <form action="{{ route('sistem-roster.planning.destroy', $planning->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus planning ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-3" title="Hapus">
                                                    <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-muted py-4">
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
<script>
const csrfToken = '{{ csrf_token() }}';
const usersUrl = '{{ route("sistem-roster.planning.users") }}';

function initKaryawanSelect() {
    $('.karyawan-select').each(function() {
        const $select = $(this);
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
        } else {
            $status.removeClass('bg-secondary bg-primary bg-success').addClass('bg-secondary').text('Draft');
        }
    })
    .catch(() => {
        $indicator.removeClass('show');
        alert('Gagal menyimpan. Silakan coba lagi.');
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

document.addEventListener('DOMContentLoaded', function() {
    initKaryawanSelect();
    
    if (hasActiveJob) {
        showJobProgress();
        disableGenerateButton();
        checkJobStatus();
        jobPollingInterval = setInterval(checkJobStatus, 3000);
    }
});
</script>
@endsection
