@extends('layouts.master')

@section('title', 'Unit MTD')
@section('content')
<x-page-title title="Unit MTD" pagetitle="Site, Perusahaan, Kategori, No Unit, MTD, AVG per Day" />

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="mb-0 fw-bold">Daftar Unit MTD</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('unit-mtd.import-form') }}" class="btn btn-success btn-sm">
                            <i class="material-icons-outlined">upload_file</i> Import Excel
                        </a>
                        <a href="{{ route('unit-mtd.download-template') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="material-icons-outlined">download</i> Template
                        </a>
                        <a href="{{ route('unit-mtd.create') }}" class="btn btn-primary btn-sm">
                            <i class="material-icons-outlined">add</i> Tambah
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="tableUnitMtd" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Site</th>
                                <th>Perusahaan</th>
                                <th>Kategori</th>
                                <th>No Unit</th>
                                <th>MTD</th>
                                <th>AVG per Day</th>
                                <th width="120">Aksi</th>
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

@section('css')
<link href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('scripts')
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#tableUnitMtd').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("unit-mtd.data") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'site', name: 'site' },
            { data: 'perusahaan', name: 'perusahaan' },
            { data: 'kategori', name: 'kategori' },
            { data: 'no_unit', name: 'no_unit' },
            { data: 'mtd', name: 'mtd' },
            { data: 'avg_per_day', name: 'avg_per_day' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ baris',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(filter dari _MAX_ total)',
            zeroRecords: 'Tidak ada data yang cocok.',
            paginate: { first: 'Awal', last: 'Akhir', next: 'Selanjutnya', previous: 'Sebelumnya' },
            processing: 'Memuat...'
        }
    });
});
</script>
@endsection
