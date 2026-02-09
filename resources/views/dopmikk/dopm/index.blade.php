@extends('layouts.masterMotionHazardAdmin')

@section('title', 'DOPM')

@section('content')
    <x-page-title title="DOPM" pagetitle="DOPM$IKK - DOPM" />

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success rounded-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
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

    <div class="row">
        <div class="col-12 col-xl-6 d-flex">
            <div class="card rounded-4 w-100">
                <div class="card-header">
                    <h5 class="mb-0">Input Manual</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dopmikk.dopm.store') }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <label for="id_dop" class="form-label">ID (contoh: DOP-JJ46)</label>
                                <input type="text" name="id_dop" id="id_dop" class="form-control" value="{{ old('id_dop') }}" placeholder="DOP-XXXXX">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="site_ijin_kerja_khusus" class="form-label">Site Ijin Kerja Khusus</label>
                                <input type="text" name="site_ijin_kerja_khusus" id="site_ijin_kerja_khusus" class="form-control" value="{{ old('site_ijin_kerja_khusus') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="perusahaan_ijin_kerja_khusus" class="form-label">Perusahaan Ijin Kerja Khusus</label>
                                <input type="text" name="perusahaan_ijin_kerja_khusus" id="perusahaan_ijin_kerja_khusus" class="form-control" value="{{ old('perusahaan_ijin_kerja_khusus') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="kode_ikk" class="form-label">Kode IKK</label>
                                <input type="text" name="kode_ikk" id="kode_ikk" class="form-control" value="{{ old('kode_ikk') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nama_pekerjaan" class="form-label">Nama Pekerjaan</label>
                                <input type="text" name="nama_pekerjaan" id="nama_pekerjaan" class="form-control" value="{{ old('nama_pekerjaan') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tanggal_dop" class="form-label">Tanggal DOP</label>
                                <input type="date" name="tanggal_dop" id="tanggal_dop" class="form-control" value="{{ old('tanggal_dop') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" name="status" id="status" class="form-control" value="{{ old('status') }}">
                            </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary rounded-3">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6 d-flex">
            <div class="card rounded-4 w-100">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Upload Excel</h5>
                        <small class="text-muted">Format: kolom sesuai tabel DOPM (baris pertama = header)</small>
                    </div>
                    <a href="{{ route('dopmikk.dopm.download-template') }}" class="btn btn-outline-primary btn-sm rounded-3">
                        <i class="material-icons-outlined me-1" style="font-size: 1rem;">download</i> Template Excel
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dopmikk.dopm.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">File (.xlsx, .xls, .csv)</label>
                            <input type="file" name="excel_file" id="excel_file" class="form-control" required>
                            <small class="text-muted">Maksimal 10MB. Download template untuk format kolom.</small>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success rounded-3">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <h5 class="mb-0">Daftar DOPM</h5>
                        <small class="text-muted">Menampilkan {{ $entries->firstItem() ?? 0 }}-{{ $entries->lastItem() ?? 0 }} dari {{ $entries->total() }} data</small>
                    </div>
                    <div class="d-flex gap-2">
                        <form method="GET" action="{{ route('dopmikk.dopm.index') }}" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}" style="min-width: 200px;">
                            <button type="submit" class="btn btn-outline-primary rounded-3">Cari</button>
                            @if(request('search'))
                                <a href="{{ route('dopmikk.dopm.index') }}" class="btn btn-outline-secondary rounded-3">Reset</a>
                            @endif
                        </form>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary rounded-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                {{ $perPage }} per halaman
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}">10</a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}">25</a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}">50</a></li>
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['per_page' => 100]) }}">100</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Site IKK</th>
                                    <th>Perusahaan IKK</th>
                                    <th>Kode IKK</th>
                                    <th>Nama Pekerjaan</th>
                                    <th>Tanggal DOP</th>
                                    <th>Status</th>
                                    <th>Layer 2</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($entries as $entry)
                                    <tr>
                                        <td>{{ ($entries->firstItem() ?? 0) + $loop->index }}</td>
                                        <td>{{ $entry->id_dop ?? '-' }}</td>
                                        <td>{{ $entry->site_ijin_kerja_khusus ?? '-' }}</td>
                                        <td>{{ $entry->perusahaan_ijin_kerja_khusus ?? '-' }}</td>
                                        <td>{{ $entry->kode_ikk ?? '-' }}</td>
                                        <td>{{ Str::limit($entry->nama_pekerjaan ?? '-', 30) }}</td>
                                        <td>{{ $entry->tanggal_dop?->format('d M Y') }}</td>
                                        <td>{{ $entry->status ?? '-' }}</td>
                                        <td>{{ $entry->nama_layer_2 ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('dopmikk.dopm.edit', $entry->id) }}" class="btn btn-sm btn-warning rounded-3">Edit</a>
                                                <form method="POST" action="{{ route('dopmikk.dopm.destroy', $entry->id) }}" onsubmit="return confirm('Yakin hapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-3">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="text-muted">
                            Menampilkan {{ $entries->firstItem() ?? 0 }}-{{ $entries->lastItem() ?? 0 }} dari {{ $entries->total() }} data
                        </div>
                        {{ $entries->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
