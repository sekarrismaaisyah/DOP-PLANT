@extends('layouts.masterRoster')

@section('title', 'Master Data IKK')

@section('content')
    <x-page-title title="Master IKK" pagetitle="Master Data Izin Kerja Khusus" />

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

            @if (!$clickhouseConnected)
                <div class="alert alert-warning alert-dismissible fade show rounded-4" role="alert">
                    <i class="bx bx-error-circle"></i> Koneksi ke ClickHouse tidak tersedia. Data IKK tidak dapat ditampilkan.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('sistem-roster.ikk.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $filterStartDate }}">
                        </div>
                        <div class="col-md-2">
                            <label for="end_date" class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $filterEndDate }}">
                        </div>
                        <div class="col-md-2">
                            <label for="site" class="form-label">Site</label>
                            <select name="site" id="site" class="form-select">
                                <option value="">Semua Site</option>
                                @foreach($siteList as $site)
                                    <option value="{{ $site }}" {{ $filterSite === $site ? 'selected' : '' }}>{{ $site }}</option>
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
                                <a href="{{ route('sistem-roster.ikk.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-reset"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <h5 class="mb-0">Daftar Izin Kerja Khusus (IKK)</h5>
                        <small class="text-muted">
                            Menampilkan {{ $ikks->firstItem() ?? 0 }}-{{ $ikks->lastItem() ?? 0 }} dari {{ $ikks->total() }} data
                            @if($filterStartDate && $filterEndDate)
                                | Periode: {{ \Carbon\Carbon::parse($filterStartDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($filterEndDate)->format('d M Y') }}
                            @endif
                            @if($filterSite)
                                | Site: {{ $filterSite }}
                            @endif
                        </small>
                    </div>
                    <div>
                        <span class="badge bg-{{ $clickhouseConnected ? 'success' : 'danger' }} rounded-pill">
                            <i class="bx bx-{{ $clickhouseConnected ? 'check-circle' : 'x-circle' }}"></i>
                            ClickHouse {{ $clickhouseConnected ? 'Connected' : 'Disconnected' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kode IKK</th>
                                    <th>Status</th>
                                    <th>Perusahaan</th>
                                    <th>Site</th>
                                    <th>Jenis Pekerjaan</th>
                                    <th>Lokasi</th>
                                    <th>Detail Lokasi</th>
                                    <th>Tanggal</th>
                                    <th>PIC Layer 1</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($ikks as $ikk)
                                    <tr>
                                        <td>{{ ($ikks->firstItem() ?? 0) + $loop->index }}</td>
                                        <td>
                                            <span class="fw-semibold text-primary">{{ $ikk->code ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match(strtoupper($ikk->status ?? '')) {
                                                    'APPROVED' => 'success',
                                                    'EXPIRED' => 'warning',
                                                    'REJECTED' => 'danger',
                                                    'SUBMITTED' => 'info',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}" title="Status: {{ $ikk->status ?? '-' }}">{{ $ikk->status_label ?? $ikk->status ?? '-' }}</span>
                                        </td>
                                        <td>{{ $ikk->company_name ?? '-' }}</td>
                                        <td>{{ $ikk->site ?? '-' }}</td>
                                        <td>{{ $ikk->job_name ?? '-' }}</td>
                                        <td>{{ $ikk->location_name ?? '-' }}</td>
                                        <td>{{ $ikk->location_detail_name ?? '-' }}</td>
                                        <td>
                                            @if($ikk->start_date && $ikk->end_date)
                                                {{ $ikk->start_date->format('d/m/Y') }} - {{ $ikk->end_date->format('d/m/Y') }}
                                            @elseif($ikk->start_date)
                                                {{ $ikk->start_date->format('d/m/Y') }}
                                            @elseif($ikk->end_date)
                                                {{ $ikk->end_date->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($ikk->layer_1_name)
                                                <span title="SID: {{ $ikk->layer_1_sid ?? '-' }}">
                                                    {{ $ikk->layer_1_name }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                @if(!empty($ikk->id))
                                                    <a href="{{ route('sistem-roster.ikk.show', $ikk->id) }}" class="btn btn-sm btn-info rounded-3" title="Detail">
                                                        <i class="material-icons-outlined text-white">visibility</i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle fs-1 d-block mb-2"></i>
                            @if(!$clickhouseConnected)
                                Koneksi ClickHouse tidak tersedia
                            @else
                                Belum ada data IKK untuk periode {{ \Carbon\Carbon::parse($filterStartDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($filterEndDate)->format('d M Y') }}
                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center flex-column flex-lg-row gap-3">
                        <div class="text-muted">
                            Menampilkan {{ $ikks->firstItem() ?? 0 }}-{{ $ikks->lastItem() ?? 0 }} dari {{ $ikks->total() }} data
                        </div>
                        {{ $ikks->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
