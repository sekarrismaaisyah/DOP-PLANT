@extends('layouts.master')

@section('title', 'Peer Pressure Edukasi')
@section('content')
<x-page-title title="Peer Pressure" pagetitle="Import Excel & Data Kejadian Edukasi" />

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

<div class="row mb-3">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Upload Excel</h5>
                <p class="text-muted small mb-3">
                    Baris pertama = header. Satu kejadian dimulai saat kolom <strong>Tanggal Temuan</strong> terisi.
                    Baris berikutnya dengan Tanggal Temuan kosong akan ditambahkan sebagai peserta (SID Pelanggar/Peer) pada kejadian yang sama.
                    Tanggal di Excel (tanggal temuan &amp; edukasi) memakai <strong>DD/MM/YYYY</strong> atau format tanggal Excel; import membaca nilai asli sel sehingga sama dengan yang tampil di spreadsheet.
                </p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a href="{{ route('peer-pressure-edukasi.download-template') }}" class="btn btn-outline-primary btn-sm">
                        <i class="material-icons-outlined">download</i> Download template Excel
                    </a>
                </div>
                <form action="{{ route('peer-pressure-edukasi.import') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-8">
                        <label for="excel_file" class="form-label">File Excel (.xlsx / .xls)</label>
                        <input type="file" class="form-control @error('excel_file') is-invalid @enderror" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                        @error('excel_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="material-icons-outlined">upload_file</i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h5 class="mb-0 fw-bold">Data Kejadian</h5>
                    <form method="get" action="{{ route('peer-pressure-edukasi.index') }}" class="d-flex gap-2">
                        <input type="search" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="Cari perusahaan, lokasi, kronologi, SID, nama…" style="min-width:220px">
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Cari</button>
                    </form>
                </div>

                @forelse($kejadian as $k)
                    <div class="border rounded-3 mb-3 overflow-hidden">
                        <div class="px-3 py-2 bg-light d-flex flex-wrap justify-content-between align-items-start gap-2">
                            <div>
                                <strong>#{{ $k->id }}</strong>
                                — {{ $k->tanggal_temuan?->format('d/m/Y') }}
                                @php
                                    $jt = $k->jam_temuan;
                                    $jamT = is_string($jt) ? substr($jt, 0, 5) : '';
                                @endphp
                                {{ $jamT }}
                                · <span class="text-primary">{{ $k->perusahaan }}</span>@if(filled($k->site))
                                · <span class="text-muted">{{ $k->site }}</span>@endif
                            </div>
                            <div class="small text-muted text-md-end">
                                {{ $k->lokasi_temuan }} · {{ $k->kategori_deviasi }} ·
                                <span class="badge bg-secondary">{{ $k->status_pelaksanaan_edukasi }}</span>
                            </div>
                        </div>
                        <div class="px-3 py-2 small">
                            <div class="mb-1"><strong>Kronologi:</strong> {{ \Illuminate\Support\Str::limit($k->kronologi_temuan, 280) }}</div>
                            <div class="row g-2">
                                <div class="col-md-6"><strong>Edukasi:</strong> {{ $k->tanggal_edukasi?->format('d/m/Y') }}
                                    @php
                                        $je = $k->jam_edukasi;
                                        $jamE = is_string($je) ? substr($je, 0, 5) : '';
                                    @endphp
                                    {{ $jamE }} — {{ $k->lokasi_edukasi }} · {{ $k->pemimpin_edukasi }}
                                </div>
                                <div class="col-md-6"><strong>Durasi:</strong> {{ $k->durasi_edukasi_menit }} menit
                                    @if($k->evidence_url)
                                        · <a href="{{ $k->evidence_url }}" target="_blank" rel="noopener">Evidence</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Peran</th>
                                        <th>SID</th>
                                        <th>Nama</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($k->peserta as $i => $p)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td>{{ $p->peran }}</td>
                                            <td>{{ $p->sid }}</td>
                                            <td>{{ $p->nama }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($k->peserta->isEmpty())
                            <div class="px-3 py-2 text-muted small">Belum ada peserta.</div>
                        @endif
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada data. Upload Excel atau jalankan migrasi tabel terlebih dahulu.</p>
                @endforelse

                <div class="mt-3">
                    {{ $kejadian->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
