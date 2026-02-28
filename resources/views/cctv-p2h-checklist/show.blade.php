@extends('layouts.master')

@section('title', 'Detail P2H Checklist')

@section('content')
    <x-page-title title="Detail P2H Checklist" pagetitle="Detail Data P2H" />

    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail P2H Checklist</h5>
                    <a href="{{ route('cctv-p2h-checklist.index') }}" class="btn btn-secondary rounded-3">
                        <i class="material-icons-outlined">arrow_back</i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <!-- Info Umum -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Informasi Umum</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Control Room</strong></td>
                                    <td>: {{ $checklist->control_room }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Pemeriksaan</strong></td>
                                    <td>: {{ $checklist->tanggal_pemeriksaan->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Shift</strong></td>
                                    <td>: <span class="badge bg-info">Shift {{ $checklist->shift }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Pengawas</strong></td>
                                    <td>: {{ $checklist->nama_pengawas }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>: 
                                        @if($checklist->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($checklist->status == 'verified')
                                            <span class="badge bg-primary">Verified</span>
                                        @else
                                            <span class="badge bg-warning">Draft</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created At</strong></td>
                                    <td>: {{ $checklist->created_at->format('d M Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Jenis CCTV</h6>
                            @if($checklist->jenis_cctv && is_array($checklist->jenis_cctv))
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($checklist->jenis_cctv as $jenis)
                                        <span class="badge bg-secondary">{{ $jenis }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">Tidak ada data</span>
                            @endif

                            @if($checklist->catatan_lain)
                                <h6 class="fw-bold text-primary mb-3 mt-4">Catatan Lain</h6>
                                <p class="text-muted">{{ $checklist->catatan_lain }}</p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <!-- Pemeriksaan Fisik -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">Section A: Pemeriksaan Fisik</h6>
                        @if($checklist->pemeriksaan_fisik && is_array($checklist->pemeriksaan_fisik) && count($checklist->pemeriksaan_fisik) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Item</th>
                                            <th>Jumlah</th>
                                            <th>Ketersediaan</th>
                                            <th>Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($checklist->pemeriksaan_fisik as $key => $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $key }}</td>
                                                <td>{{ $item['jumlah'] ?? '-' }}</td>
                                                <td>
                                                    @if(isset($item['ketersediaan']))
                                                        @if($item['ketersediaan'] == 'ada')
                                                            <span class="badge bg-success">Ada</span>
                                                        @else
                                                            <span class="badge bg-danger">Tidak Ada</span>
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(isset($item['kondisi']))
                                                        @if($item['kondisi'] == 'baik')
                                                            <span class="badge bg-success">Baik</span>
                                                        @else
                                                            <span class="badge bg-danger">Rusak</span>
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Tidak ada data pemeriksaan fisik</p>
                        @endif
                    </div>

                    <hr>

                    <!-- Pemeriksaan Fungsi -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">Section B: Pemeriksaan Fungsi</h6>
                        @if($checklist->pemeriksaan_fungsi && is_array($checklist->pemeriksaan_fungsi) && count($checklist->pemeriksaan_fungsi) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Item</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($checklist->pemeriksaan_fungsi as $key => $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $key }}</td>
                                                <td>
                                                    @if(isset($item['status']))
                                                        @if($item['status'] == 'baik')
                                                            <span class="badge bg-success">Baik</span>
                                                        @elseif($item['status'] == 'rusak')
                                                            <span class="badge bg-danger">Rusak</span>
                                                        @else
                                                            <span class="badge bg-secondary">Tidak Ada</span>
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Tidak ada data pemeriksaan fungsi</p>
                        @endif
                    </div>

                    <hr>

                    <!-- Detail CCTV -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">Detail CCTV</h6>
                        @if($checklist->detail_cctv && is_array($checklist->detail_cctv) && count($checklist->detail_cctv) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>CCTV ID</th>
                                            <th>Nama CCTV</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($checklist->detail_cctv as $detail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $detail['cctv_id'] ?? '-' }}</td>
                                                <td>{{ $detail['nama_cctv'] ?? '-' }}</td>
                                                <td>
                                                    @if(isset($detail['status']))
                                                        @if($detail['status'] == 'baik' || $detail['status'] == 'aktif')
                                                            <span class="badge bg-success">{{ ucfirst($detail['status']) }}</span>
                                                        @else
                                                            <span class="badge bg-danger">{{ ucfirst($detail['status']) }}</span>
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $detail['catatan'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Tidak ada detail CCTV</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
