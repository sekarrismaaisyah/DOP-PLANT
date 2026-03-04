@extends('layouts.masterRoster')

@section('title', 'Detail Daily Operation Plan')

@section('content')
    <x-page-title title="Detail Daily Operation Plan" pagetitle="Detail DOP" />

    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informasi DOP</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('sistem-roster.dop.edit', $dop->id) }}" class="btn btn-warning btn-sm rounded-3">
                            <i class="bx bx-edit"></i> Edit
                        </a>
                        <a href="{{ route('sistem-roster.dop.index') }}" class="btn btn-secondary btn-sm rounded-3">
                            <i class="bx bx-arrow-back"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Tanggal</label>
                            <div class="fw-semibold">{{ $dop->tanggal->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Pekerjaan</label>
                            <div class="fw-semibold">{{ $dop->pekerjaan }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Aktivitas</label>
                            <div class="fw-semibold">{{ $dop->aktivitas ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Unit ID</label>
                            <div class="fw-semibold">{{ $dop->unit_id }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Lokasi</label>
                            <div class="fw-semibold">{{ $dop->lokasi }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Perusahaan</label>
                            <div class="fw-semibold">{{ $dop->perusahaan ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small mb-1">Latitude</label>
                            <div class="fw-semibold">{{ $dop->latitude ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small mb-1">Longitude</label>
                            <div class="fw-semibold">{{ $dop->longitude ?? '-' }}</div>
                        </div>
                        @if($dop->detail_lokasi)
                        <div class="col-12">
                            <label class="text-muted small mb-1">Detail Lokasi</label>
                            <div>{{ $dop->detail_lokasi }}</div>
                        </div>
                        @endif
                        @if($dop->foto_pekerjaan)
                        <div class="col-12">
                            <label class="text-muted small mb-1">Foto Pekerjaan</label>
                            <div>
                                <a href="{{ asset('storage/' . $dop->foto_pekerjaan) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $dop->foto_pekerjaan) }}" class="img-thumbnail" style="max-width: 500px; max-height: 500px;">
                                </a>
                            </div>
                        </div>
                        @endif
                        @if($dop->cctvs->count() > 0)
                        <div class="col-12">
                            <label class="text-muted small mb-1">CCTV yang Mengcover</label>
                            <div>
                                <ul class="list-unstyled mb-0">
                                    @foreach($dop->cctvs as $cctv)
                                        <li class="mb-1">
                                            <i class="bx bx-video"></i> 
                                            <strong>{{ $cctv->nama_cctv }}</strong> 
                                            ({{ $cctv->no_cctv }})
                                            @if($cctv->lokasi_pemasangan)
                                                - {{ $cctv->lokasi_pemasangan }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif
                        @if($dop->potensi_resiko)
                        <div class="col-12">
                            <label class="text-muted small mb-1">Potensi Risiko</label>
                            <div class="text-break">{{ $dop->potensi_resiko }}</div>
                        </div>
                        @endif
                        @if($dop->pengendalian_bahaya)
                        <div class="col-12">
                            <label class="text-muted small mb-1">Pengendalian Bahaya</label>
                            <div class="text-break">{{ $dop->pengendalian_bahaya }}</div>
                        </div>
                        @endif
                        @if($dop->catatan)
                        <div class="col-12">
                            <label class="text-muted small mb-1">Catatan</label>
                            <div class="text-break">{{ $dop->catatan }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 col-lg-6">
            <div class="card rounded-4">
                <div class="card-header">
                    <h5 class="mb-0">PIC PT Berau Coal</h5>
                </div>
                <div class="card-body">
                    @forelse($dop->picBerauCoal as $pic)
                        <div class="mb-3 p-3 border rounded">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="text-muted small mb-1">Shift</label>
                                    <div class="fw-semibold">{{ $pic->shift }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small mb-1">Nama PIC</label>
                                    <div class="fw-semibold">{{ $pic->nama_pic }}</div>
                                </div>
                                @if($pic->layer)
                                <div class="col-md-4">
                                    <label class="text-muted small mb-1">Layer</label>
                                    <div class="fw-semibold">{{ $pic->layer }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted">Tidak ada data PIC</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card rounded-4">
                <div class="card-header">
                    <h5 class="mb-0">Pengawas Mitra Kerja</h5>
                </div>
                <div class="card-body">
                    @forelse($dop->pengawasMitraKerja as $pengawas)
                        <div class="mb-3 p-3 border rounded">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="text-muted small mb-1">Shift</label>
                                    <div class="fw-semibold">{{ $pengawas->shift }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-muted small mb-1">Nama Pengawas</label>
                                    <div class="fw-semibold">{{ $pengawas->nama_pengawas }}</div>
                                </div>
                                @if($pengawas->layer)
                                <div class="col-md-4">
                                    <label class="text-muted small mb-1">Layer</label>
                                    <div class="fw-semibold">{{ $pengawas->layer }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted">Tidak ada data Pengawas</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
