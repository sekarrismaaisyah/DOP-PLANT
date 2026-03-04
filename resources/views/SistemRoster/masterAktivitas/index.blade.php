@extends('layouts.masterRoster')

@section('title', 'Master Aktivitas')

@section('content')
    <x-page-title title="Master Aktivitas" pagetitle="CRUD Master Aktivitas" />

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <h5 class="mb-0">Daftar Master Aktivitas</h5>
                        <small class="text-muted">Menampilkan {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} data</small>
                    </div>
                    <a href="{{ route('sistem-roster.master-aktivitas.create') }}" class="btn btn-primary rounded-3"><i class="bx bx-plus"></i> Tambah Aktivitas</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-3">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px">No</th>
                                    <th>Nama Aktivitas</th>
                                    <th>Periode Check</th>
                                    <th style="width: 140px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $idx => $item)
                                    <tr>
                                        <td>{{ ($items->firstItem() ?? 0) + $idx }}</td>
                                        <td>{{ $item->nama_aktivitas }}</td>
                                        <td>{{ $item->periode_check ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('sistem-roster.master-aktivitas.edit', $item->id) }}" class="btn btn-sm btn-warning rounded-3" title="Edit"><i class="bx bx-edit"></i></a>
                                                <form action="{{ route('sistem-roster.master-aktivitas.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-3" title="Hapus"><i class="bx bx-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada data. <a href="{{ route('sistem-roster.master-aktivitas.create') }}">Tambah aktivitas</a></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted">Per halaman:</span>
                            <form method="GET" action="{{ route('sistem-roster.master-aktivitas.index') }}" class="d-inline">
                                @foreach(request()->except('per_page') as $key => $val)
                                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                                @endforeach
                                <select name="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                    @foreach([10, 25, 50, 100] as $pp)
                                        <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        {{ $items->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
