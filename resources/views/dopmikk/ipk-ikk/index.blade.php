@extends('layouts.master')

@section('title', 'IPK-IKK')

@section('content')
    <x-page-title title="IPK-IKK" pagetitle="DOPM$IKK - IPK-IKK" />

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success rounded-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-6 d-flex">
            <div class="card rounded-4 w-100">
                <div class="card-header">
                    <h5 class="mb-0">Input Manual (ringkas)</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dopmikk.ipk-ikk.store') }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <label for="nama_pengawas" class="form-label">Nama Pengawas</label>
                                <input type="text" name="nama_pengawas" id="nama_pengawas" class="form-control" value="{{ old('nama_pengawas') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="kode_sid" class="form-label">Kode SID</label>
                                <input type="text" name="kode_sid" id="kode_sid" class="form-control" value="{{ old('kode_sid') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="kode_ikk" class="form-label">Kode IKK</label>
                                <input type="text" name="kode_ikk" id="kode_ikk" class="form-control" value="{{ old('kode_ikk') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                                <input type="text" name="nama_perusahaan" id="nama_perusahaan" class="form-control" value="{{ old('nama_perusahaan') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="site" class="form-label">Site</label>
                                <input type="text" name="site" id="site" class="form-control" value="{{ old('site') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="status_pekerjaan" class="form-label">Status Pekerjaan</label>
                                <input type="text" name="status_pekerjaan" id="status_pekerjaan" class="form-control" value="{{ old('status_pekerjaan') }}">
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
                        <small class="text-muted">Baris pertama = header (nama kolom)</small>
                    </div>
                    <a href="{{ route('dopmikk.ipk-ikk.download-template') }}" class="btn btn-outline-primary btn-sm rounded-3">
                        <i class="material-icons-outlined me-1" style="font-size: 1rem;">download</i> Template Excel
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('dopmikk.ipk-ikk.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="excel_file" class="form-label">File (.xlsx, .xls, .csv)</label>
                            <input type="file" name="excel_file" id="excel_file" class="form-control" required>
                            <small class="text-muted">Download template untuk format kolom.</small>
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
                        <h5 class="mb-0">Daftar IPK-IKK</h5>
                        <small class="text-muted">Menampilkan {{ $entries->firstItem() ?? 0 }}-{{ $entries->lastItem() ?? 0 }} dari {{ $entries->total() }} data</small>
                    </div>
                    <div class="d-flex gap-2">
                        <form method="GET" action="{{ route('dopmikk.ipk-ikk.index') }}" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}" style="min-width: 200px;">
                            <button type="submit" class="btn btn-outline-primary rounded-3">Cari</button>
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
                                    <th>Nama Pengawas</th>
                                    <th>Kode SID</th>
                                    <th>Kode IKK</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Site</th>
                                    <th>Status Pekerjaan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($entries as $entry)
                                    <tr>
                                        <td>{{ ($entries->firstItem() ?? 0) + $loop->index }}</td>
                                        <td>{{ Str::limit($entry->nama_pengawas ?? '-', 25) }}</td>
                                        <td>{{ $entry->kode_sid ?? '-' }}</td>
                                        <td>{{ $entry->kode_ikk ?? '-' }}</td>
                                        <td>{{ Str::limit($entry->nama_perusahaan ?? '-', 25) }}</td>
                                        <td>{{ $entry->site ?? '-' }}</td>
                                        <td>{{ $entry->status_pekerjaan ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('dopmikk.ipk-ikk.edit', $entry->id) }}" class="btn btn-sm btn-warning rounded-3">Edit</a>
                                                <form method="POST" action="{{ route('dopmikk.ipk-ikk.destroy', $entry->id) }}" onsubmit="return confirm('Yakin hapus?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-3">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Belum ada data</td>
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
