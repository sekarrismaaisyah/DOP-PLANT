@extends('layouts.master')

@section('title', 'Insiden CCR')

@section('css')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <style>
        #insidenCcrTable th, #insidenCcrTable td {
            white-space: nowrap;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.5rem;
        }
        .dataTables_wrapper .dataTables_length select {
            border-radius: 0.5rem;
        }
        div.dt-buttons {
            margin-bottom: 0;
        }
    </style>
@endsection

@section('content')
    <x-page-title title="Insiden CCR" pagetitle="Manajemen Insiden CCR" />

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success rounded-4">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-warning rounded-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card rounded-4 h-100">
                <div class="card-header">
                    <h5 class="mb-0">Filter Data</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="jenis_insiden" class="form-label">Jenis Insiden</label>
                            <select name="jenis_insiden" id="jenis_insiden" class="form-select">
                                <option value="">Semua</option>
                                @foreach ($jenisInsidenList as $item)
                                    <option value="{{ $item }}" {{ request('jenis_insiden') == $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="site" class="form-label">Site</label>
                            <select name="site" id="site" class="form-select">
                                <option value="">Semua</option>
                                @foreach ($siteList as $item)
                                    <option value="{{ $item }}" {{ request('site') == $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="date_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-3 flex-fill">Filter</button>
                            <a href="{{ route('insiden-ccr.index') }}" class="btn btn-outline-secondary rounded-3">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card rounded-4 h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Upload Excel</h5>
                    <a href="{{ route('insiden-ccr.template') }}" class="btn btn-sm btn-outline-secondary rounded-3">
                        <i class="bx bx-download"></i> Template
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('insiden-ccr.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">File Excel (.xlsx/.xls/.csv)</label>
                            <input type="file" name="excel_file" id="excel_file" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success rounded-3">Upload &amp; Proses</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <h5 class="mb-0">Data Insiden CCR</h5>
                        <small class="text-muted">Total {{ $insidens->count() }} data</small>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <a href="{{ route('insiden-ccr.create') }}" class="btn btn-primary rounded-3">Tambah Data</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="insidenCcrTable" class="table table-striped table-hover align-middle mb-0" style="width: 100%;">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>CCR ID</th>
                                    <th>No. Kecelakaan</th>
                                    <th>Jenis Insiden</th>
                                    <th>Waktu Insiden</th>
                                    <th>Waktu Pelaporan</th>
                                    <th>Kronologi</th>
                                    <th>Nama Call Taker</th>
                                    <th>Perusahaan Call Taker</th>
                                    <th>Nama Pelapor</th>
                                    <th>Perusahaan Pelapor</th>
                                    <th>Lokasi Perusahaan</th>
                                    <th>Site</th>
                                    <th>Lokasi</th>
                                    <th>Detail Lokasi</th>
                                    <th>Keterangan Lokasi</th>
                                    <th>Status</th>
                                    <th>PIC Investigasi</th>
                                    <th>Perusahaan PIC</th>
                                    <th>Ket. Tidak Investigasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($insidens as $index => $insiden)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $insiden->ccr_id ?? '-' }}</td>
                                        <td>{{ $insiden->no_kecelakaan ?? '-' }}</td>
                                        <td>
                                            @if($insiden->ccr_jenis_insiden)
                                                <span class="badge 
                                                    @if(str_contains(strtolower($insiden->ccr_jenis_insiden), 'fire')) bg-danger
                                                    @elseif(str_contains(strtolower($insiden->ccr_jenis_insiden), 'vehicle')) bg-warning text-dark
                                                    @elseif(str_contains(strtolower($insiden->ccr_jenis_insiden), 'work')) bg-info
                                                    @elseif(str_contains(strtolower($insiden->ccr_jenis_insiden), 'illness')) bg-secondary
                                                    @elseif(str_contains(strtolower($insiden->ccr_jenis_insiden), 'equipment')) bg-primary
                                                    @elseif(str_contains(strtolower($insiden->ccr_jenis_insiden), 'water')) bg-info
                                                    @else bg-dark
                                                    @endif">
                                                    {{ $insiden->ccr_jenis_insiden }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td data-order="{{ $insiden->ccr_waktu_insiden?->format('Y-m-d H:i:s') ?? '' }}">
                                            {{ $insiden->ccr_waktu_insiden?->format('d/m/Y H:i') ?? '-' }}
                                        </td>
                                        <td data-order="{{ $insiden->ccr_waktu_pelaporan?->format('Y-m-d H:i:s') ?? '' }}">
                                            {{ $insiden->ccr_waktu_pelaporan?->format('d/m/Y H:i') ?? '-' }}
                                        </td>
                                        <td title="{{ $insiden->ccr_kronologi }}">{{ Str::limit($insiden->ccr_kronologi, 50) ?? '-' }}</td>
                                        <td>{{ $insiden->ccr_nama_call_taker ?? '-' }}</td>
                                        <td>{{ $insiden->ccr_perusahaan_call_taker ?? '-' }}</td>
                                        <td>{{ $insiden->ccr_nama_pelapor ?? '-' }}</td>
                                        <td>{{ $insiden->ccr_perusahaan_pelapor ?? '-' }}</td>
                                        <td>{{ $insiden->ccr_lokasi_perusahaan ?? '-' }}</td>
                                        <td>{{ $insiden->ccr_site ?? '-' }}</td>
                                        <td title="{{ $insiden->ccr_lokasi }}">{{ Str::limit($insiden->ccr_lokasi, 25) ?? '-' }}</td>
                                        <td title="{{ $insiden->ccr_detil_lokasi }}">{{ Str::limit($insiden->ccr_detil_lokasi, 25) ?? '-' }}</td>
                                        <td title="{{ $insiden->ccr_keterangan_lokasi }}">{{ Str::limit($insiden->ccr_keterangan_lokasi, 25) ?? '-' }}</td>
                                        <td>
                                            @if($insiden->ccr_status)
                                                <span class="badge 
                                                    @if(str_contains(strtolower($insiden->ccr_status), 'investigasi') && !str_contains(strtolower($insiden->ccr_status), 'tidak')) bg-success
                                                    @elseif(str_contains(strtolower($insiden->ccr_status), 'baru')) bg-warning text-dark
                                                    @elseif(str_contains(strtolower($insiden->ccr_status), 'tidak')) bg-secondary
                                                    @else bg-info
                                                    @endif">
                                                    {{ $insiden->ccr_status }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $insiden->ccr_pic_investigasi ?? '-' }}</td>
                                        <td>{{ $insiden->ccr_pic_investigasi_perusahaan ?? '-' }}</td>
                                        <td title="{{ $insiden->ket_not_investigasi }}">{{ Str::limit($insiden->ket_not_investigasi, 25) ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-info rounded-3" 
                                                        data-bs-toggle="modal" data-bs-target="#detailModal{{ $insiden->id }}">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                <a href="{{ route('insiden-ccr.edit', $insiden) }}"
                                                    class="btn btn-sm btn-outline-primary rounded-3">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('insiden-ccr.destroy', $insiden) }}"
                                                    onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
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

    {{-- Detail Modals --}}
    @foreach ($insidens as $insiden)
        <div class="modal fade" id="detailModal{{ $insiden->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $insiden->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel{{ $insiden->id }}">
                            Detail Insiden CCR #{{ $insiden->ccr_id }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <strong>CCR ID:</strong>
                                <p>{{ $insiden->ccr_id ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>No. Kecelakaan:</strong>
                                <p>{{ $insiden->no_kecelakaan ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Jenis Insiden:</strong>
                                <p>{{ $insiden->ccr_jenis_insiden ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Waktu Insiden:</strong>
                                <p>{{ $insiden->ccr_waktu_insiden?->format('d F Y H:i') ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Waktu Pelaporan:</strong>
                                <p>{{ $insiden->ccr_waktu_pelaporan?->format('d F Y H:i') ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                <p>{{ $insiden->ccr_status ?? '-' }}</p>
                            </div>
                            <div class="col-12">
                                <strong>Kronologi:</strong>
                                <p class="bg-light p-2 rounded">{{ $insiden->ccr_kronologi ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Nama Call Taker:</strong>
                                <p>{{ $insiden->ccr_nama_call_taker ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Perusahaan Call Taker:</strong>
                                <p>{{ $insiden->ccr_perusahaan_call_taker ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Nama Pelapor:</strong>
                                <p>{{ $insiden->ccr_nama_pelapor ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Perusahaan Pelapor:</strong>
                                <p>{{ $insiden->ccr_perusahaan_pelapor ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Lokasi Perusahaan:</strong>
                                <p>{{ $insiden->ccr_lokasi_perusahaan ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Site:</strong>
                                <p>{{ $insiden->ccr_site ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Lokasi:</strong>
                                <p>{{ $insiden->ccr_lokasi ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Detail Lokasi:</strong>
                                <p>{{ $insiden->ccr_detil_lokasi ?? '-' }}</p>
                            </div>
                            <div class="col-12">
                                <strong>Keterangan Lokasi:</strong>
                                <p>{{ $insiden->ccr_keterangan_lokasi ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>PIC Investigasi:</strong>
                                <p>{{ $insiden->ccr_pic_investigasi ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Perusahaan PIC Investigasi:</strong>
                                <p>{{ $insiden->ccr_pic_investigasi_perusahaan ?? '-' }}</p>
                            </div>
                            @if($insiden->ket_not_investigasi)
                            <div class="col-12">
                                <strong>Keterangan Tidak Investigasi:</strong>
                                <p>{{ $insiden->ket_not_investigasi }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
                        <a href="{{ route('insiden-ccr.edit', $insiden) }}" class="btn btn-primary rounded-3">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#insidenCcrTable').DataTable({
                scrollX: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                order: [[4, 'desc']], // Sort by Waktu Insiden descending (column index 4)
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 - 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Belum ada data"
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"B>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="bx bx-file"></i> Excel',
                        className: 'btn btn-sm btn-success rounded-3 me-1',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19] // All columns except Aksi
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="bx bx-printer"></i> Print',
                        className: 'btn btn-sm btn-secondary rounded-3',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19] // All columns except Aksi
                        }
                    }
                ],
                columnDefs: [
                    { orderable: false, targets: 20 }, // Disable sorting on Aksi column (index 20)
                    { className: 'text-center', targets: [0, 20] }
                ]
            });
        });
    </script>
@endsection
