@extends('layouts.master')

@section('title', 'Intervensi Issue') 
@section('content')
<x-page-title title="Intervensi Issue" pagetitle="Daftar Intervensi Issue" />

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <h5 class="mb-0 fw-bold">Daftar Issue Intervensi</h5>
                        <p class="text-muted mb-0">Kelola intervensi </p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="intervensiTable" class="table table-bordered table-hover table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 8%;">ID</th>
                                <th style="width: 12%;">Control Room</th>
                                <th style="width: 10%;">PIC Username</th>
                                <th style="width: 12%;">PIC Nama</th>
                                <th style="width: 10%;">PIC Telepon</th>
                                <th style="width: 20%;">Issue</th>
                                <th style="width: 8%;">Status</th>
                                <th style="width: 10%;">Created By</th>
                                <th style="width: 10%;">Created At</th>
                                <th style="width: 5%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTable -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Intervensi -->
<div class="modal fade" id="detailIntervensiModal" tabindex="-1" aria-labelledby="detailIntervensiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailIntervensiModalLabel">Detail Intervensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailIntervensiBody">
                <!-- Detail will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#intervensiTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('cctv-data.intervensi-control-room.data') }}",
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'control_room', name: 'control_room' },
            { data: 'pic_username', name: 'pic_username' },
            { data: 'pic_nama', name: 'pic_nama' },
            { data: 'pic_telepon', name: 'pic_telepon' },
            { data: 'issue', name: 'issue', orderable: false },
            { data: 'status', name: 'status' },
            { data: 'created_by', name: 'created_by' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[9, 'desc']], // Order by created_at desc
        pageLength: 25,
        language: {
            processing: "Memproses...",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // Handle close intervensi button
    $(document).on('click', '.close-intervensi-btn', function() {
        var intervensiId = $(this).data('id');
        var button = $(this);

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menutup intervensi ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tutup',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Disable button
                button.prop('disabled', true);
                button.html('<span class="spinner-border spinner-border-sm"></span>');

                // Send AJAX request
                $.ajax({
                    url: "{{ url('cctv-data-control-room/intervensi') }}/" + intervensiId + "/status",
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        status: 'closed'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Status intervensi berhasil diupdate.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            // Reload table
                            table.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Terjadi kesalahan saat mengupdate status.'
                            });
                            button.prop('disabled', false);
                            button.html('<i class="material-icons-outlined" style="font-size: 16px;">check_circle</i> Close');
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'Terjadi kesalahan saat mengupdate status.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                        button.prop('disabled', false);
                        button.html('<i class="material-icons-outlined" style="font-size: 16px;">check_circle</i> Close');
                    }
                });
            }
        });
    });
});
</script>
@endsection

