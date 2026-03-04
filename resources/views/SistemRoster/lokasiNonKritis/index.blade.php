@extends('layouts.masterRoster')

@section('title', 'Lokasi Non Kritis')

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        #lokasiNonKritisTable thead th { white-space: nowrap; }
        .dataTables_wrapper .dataTables_filter input { border-radius: 0.5rem; }
        .dataTables_wrapper .dataTables_length select { border-radius: 0.5rem; }
    </style>
@endsection

@section('content')
    <x-page-title title="Lokasi Non Kritis" pagetitle="Kategori Area Kritis / Non Kritis" />

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
        </div>
    </div>

    <!-- Filter & Generate Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-8">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="filter_tanggal" class="form-label">Tanggal</label>
                                    <input type="date" id="filter_tanggal" class="form-control" value="{{ $filterTanggal }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="filter_kategori" class="form-label">Kategori Area</label>
                                    <select id="filter_kategori" class="form-select">
                                        <option value="">-- Semua --</option>
                                        <option value="kritis" {{ $filterKategori === 'kritis' ? 'selected' : '' }}>Kritis</option>
                                        <option value="non_kritis" {{ $filterKategori === 'non_kritis' ? 'selected' : '' }}>Non Kritis</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" id="btnApplyFilter" class="btn btn-primary rounded-3">
                                        <i class="bx bx-search"></i> Terapkan Filter
                                    </button>
                                    <a href="{{ route('sistem-roster.lokasi-non-kritis.index') }}" class="btn btn-outline-secondary rounded-3">
                                        <i class="bx bx-reset"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <form method="POST" action="{{ route('sistem-roster.lokasi-non-kritis.generate') }}" class="d-flex justify-content-lg-end align-items-end" id="formGenerate">
                                @csrf
                                <input type="hidden" name="tanggal" id="generate_tanggal" value="{{ $filterTanggal }}">
                                <button type="submit" class="btn btn-success rounded-3">
                                    <i class="bx bx-refresh"></i> Generate (DOP & IKK vs Master Lokasi)
                                </button>
                            </form>
                        </div>
                    </div>
                    <p class="text-muted small mb-0 mt-2">
                        <i class="bx bx-info-circle"></i> Generate membandingkan master lokasi (ClickHouse) dengan DOP & IKK pada tanggal terpilih. Lokasi yang <strong>tidak</strong> ada di DOP/IKK = <strong>Non Kritis</strong>; yang ada = <strong>Kritis</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Lokasi (Kategori Area)</h5>
                    <small class="text-muted">Server-side DataTables</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="lokasiNonKritisTable" class="table table-striped table-hover align-middle mb-0" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Site</th>
                                    <th>Lokasi</th>
                                    <th>Detail Lokasi</th>
                                    <th>Kategori Area</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var table = $('#lokasiNonKritisTable').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: '{{ route("sistem-roster.lokasi-non-kritis.data") }}',
                    type: 'GET',
                    data: function(d) {
                        d.tanggal = $('#filter_tanggal').val() || '{{ $filterTanggal }}';
                        d.kategori_area = $('#filter_kategori').val() || '';
                    }
                },
                columns: [
                    { data: 0, name: 'rownum', orderable: false, searchable: false },
                    { data: 1, name: 'tanggal' },
                    { data: 2, name: 'site' },
                    { data: 3, name: 'lokasi' },
                    { data: 4, name: 'detil_lokasi' },
                    { data: 5, name: 'kategori_area', orderable: true, searchable: true, render: function(data) { return data === 'kritis' ? '<span class="badge bg-danger">Kritis</span>' : '<span class="badge bg-secondary">Non Kritis</span>'; } }
                ],
                order: [[2, 'asc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                    processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Memuat...</span></div>'
                }
            });

            $('#btnApplyFilter').on('click', function() {
                var tgl = $('#filter_tanggal').val();
                $('#generate_tanggal').val(tgl);
                table.ajax.reload();
            });

            $('#formGenerate').on('submit', function() {
                $('#generate_tanggal').val($('#filter_tanggal').val());
            });
        });
    </script>
@endsection
