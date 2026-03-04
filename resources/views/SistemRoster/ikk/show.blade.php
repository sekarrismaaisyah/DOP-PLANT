@extends('layouts.masterRoster')

@section('title', 'Detail IKK - ' . ($ikk->code ?? ''))

@section('content')
    <x-page-title title="Detail IKK" pagetitle="Detail Izin Kerja Khusus" />

    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('sistem-roster.ikk.index') }}" class="btn btn-outline-secondary rounded-3">
                <i class="bx bx-arrow-back"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Info IKK -->
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card rounded-4 mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bx bx-file"></i> Informasi IKK</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="35%">Kode IKK</th>
                            <td><span class="fw-semibold text-primary">{{ $ikk->code ?? '-' }}</span></td>
                        </tr>
                        <tr>
                            <th>Status</th>
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
                                <span class="badge bg-{{ $statusClass }}">{{ $ikk->status ?? '-' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Site</th>
                            <td>{{ $ikk->site ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Pekerjaan</th>
                            <td>{{ $ikk->job_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi</th>
                            <td>{{ $ikk->location_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Detail Lokasi</th>
                            <td>{{ $ikk->location_detail_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Mulai</th>
                            <td>
                                @if($ikk->start_date)
                                    {{ $ikk->start_date->format('d M Y H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Selesai</th>
                            <td>
                                @if($ikk->end_date)
                                    {{ $ikk->end_date->format('d M Y H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card rounded-4 mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bx bx-user"></i> Penanggung Jawab (PIC)</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Layer</th>
                                <th>Nama</th>
                                <th>SID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-primary">Layer 1</span></td>
                                <td>{{ $ikk->layer_1_name ?? '-' }}</td>
                                <td>{{ $ikk->layer_1_sid ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-info">Layer 2</span></td>
                                <td>{{ $ikk->layer_2_name ?? '-' }}</td>
                                <td>{{ $ikk->layer_2_sid ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning">Layer 3</span></td>
                                <td>{{ $ikk->layer_3_name ?? '-' }}</td>
                                <td>{{ $ikk->layer_3_sid ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-danger">Layer 4</span></td>
                                <td>{{ $ikk->layer_4_name ?? '-' }}</td>
                                <td>{{ $ikk->layer_4_sid ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
