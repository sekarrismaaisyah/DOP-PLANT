@extends('layouts.master')

@section('title', 'Becomline')
@section('content')
<x-page-title title="Becomline" pagetitle="Data Perusahaan & Permit SPIP" />

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
                    <h5 class="mb-0 fw-bold">Daftar Becomline</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('becomline.import-form') }}" class="btn btn-success btn-sm">
                            <i class="material-icons-outlined">upload_file</i> Import Excel
                        </a>
                        <a href="{{ route('becomline.download-template') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="material-icons-outlined">download</i> Template
                        </a>
                        <a href="{{ route('becomline.create') }}" class="btn btn-primary btn-sm">
                            <i class="material-icons-outlined">add</i> Tambah
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="tableBecomline" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Perusahaan Pemilik</th>
                                <th>Site Operasional</th>
                                <th>Jenis Unit SPIP</th>
                                <th>Expired</th>
                                <th>Status Permit SPIP</th>
                                <th>No Register</th>
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
    $('#tableBecomline').DataTable({
        serverSide: true,
        processing: true,
        ajax: '{{ route("becomline.data") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'perusahaan_pemilik', name: 'perusahaan_pemilik' },
            { data: 'site_operasional', name: 'site_operasional' },
            { data: 'jenis_unit_spip', name: 'jenis_unit_spip' },
            { data: 'expired', name: 'expired' },
            { data: 'status_permit_spip', name: 'status_permit_spip' },
            { data: 'no_registrasi', name: 'no_registrasi' },
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
