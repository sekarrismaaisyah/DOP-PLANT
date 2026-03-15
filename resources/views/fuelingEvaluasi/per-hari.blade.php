@extends('layouts.master')

@section('title', 'Evaluasi Unit Per Hari - Fueling')

@section('css')
<link href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
<style>
    .evaluasi-perhari-table { border-collapse: separate; border-spacing: 0; }
    .evaluasi-perhari-table thead th {
        font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.02em;
        color: #475569; border-bottom: 2px solid #e2e8f0; padding: 0.75rem 1rem; background: #f8fafc;
    }
    .evaluasi-perhari-table tbody td {
        padding: 0.875rem 1rem; vertical-align: middle; border-bottom: 1px solid #f1f3f5;
    }
    .evaluasi-perhari-table tbody tr:hover { background-color: #f8fafc; }
    .evaluasi-perhari-table .text-jarak { font-weight: 600; color: #0369a1; }
</style>
@endsection

@section('content')
    <x-page-title title="Evaluasi Unit Per Hari" pagetitle="Jarak & Durasi Masing-masing Unit per Tanggal" />

    <div class="row">
        <div class="col-12">
            @if (isset($error) && $error)
                <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                    <strong>Error:</strong> {{ $error }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-body">
                    <form method="get" action="{{ route('fueling-evaluasi.per-hari') }}" class="row g-3 align-items-end" id="formFilterPerHari">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Dari</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom ?? '' }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Sampai</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo ?? '' }}" required>
                        </div>
                        <div class="col-md-4 d-flex gap-2 align-items-center flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="material-icons-outlined" style="font-size: 1rem; vertical-align: middle;">search</i>
                                Filter
                            </button>
                            @if (isset($dateFrom) && isset($dateTo))
                                <a href="{{ route('fueling-evaluasi.per-hari.export-excel', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                                   class="btn btn-success" target="_blank" rel="noopener">
                                    <i class="material-icons-outlined" style="font-size: 1rem; vertical-align: middle;">download</i>
                                    Download Excel
                                </a>
                            @endif
                            <a href="{{ route('fueling-evaluasi.tabel') }}" class="btn btn-outline-secondary">Tabel per Unit</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Per Hari per Unit (masing-masing unit)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table evaluasi-perhari-table mb-0" id="tablePerHari" style="width:100%">
                            <thead>
                                <tr>
                                    <th>TANGGAL</th>
                                    <th>NO UNIT</th>
                                    <th>JARAK YANG DITEMPUH</th>
                                    <th>DURASI (jam)</th>
                                    <th>Perusahaan Pemilik</th>
                                    <th>Site Operasional</th>
                                    <th>Jenis Unit SPIP</th>
                                    <th>Expired</th>
                                    <th>Status Permit SPIP</th>
                                    <th>MTD</th>
                                    <th>AVG per Day</th>
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
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
(function() {
    var table = $('#tablePerHari');
    if (!table.length) return;
    var dateFrom = document.getElementById('date_from');
    var dateTo = document.getElementById('date_to');
    table.DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '{{ route("fueling-evaluasi.per-hari.data") }}',
            data: function(d) {
                d.date_from = dateFrom ? dateFrom.value : '';
                d.date_to = dateTo ? dateTo.value : '';
            }
        },
        columns: [
            { data: 0, name: 'tanggal' },
            { data: 1, name: 'no_unit' },
            { data: 2, name: 'jarak', className: 'text-jarak' },
            { data: 3, name: 'total_jam' },
            { data: 4, name: 'perusahaan_pemilik' },
            { data: 5, name: 'site_operasional' },
            { data: 6, name: 'jenis_unit_spip' },
            { data: 7, name: 'expired' },
            { data: 8, name: 'status_permit_spip' },
            { data: 9, name: 'mtd' },
            { data: 10, name: 'avg_per_day' }
        ],
        order: [[0, 'asc'], [1, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ baris',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(filter dari _MAX_ total)',
            zeroRecords: 'Tidak ada data untuk rentang tanggal yang dipilih.',
            paginate: { first: 'Awal', last: 'Akhir', next: 'Selanjutnya', previous: 'Sebelumnya' },
            processing: 'Memuat...'
        }
    });
})();
</script>
@endsection
