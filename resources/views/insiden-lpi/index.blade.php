@extends('layouts.master')

@section('title', 'Insiden LPI')

@section('content')
    <x-page-title title="Insiden LPI" pagetitle="Manajemen Insiden LPI" />

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
                            <label for="search" class="form-label">Cari</label>
                            <input type="text" name="search" id="search" value="{{ $search ?? '' }}" class="form-control"
                                placeholder="No Kecelakaan / Nama / Perusahaan">
                        </div>
                        <div class="col-md-2">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select name="kategori" id="kategori" class="form-select">
                                <option value="">Semua</option>
                                @foreach ($kategoriList as $item)
                                    <option value="{{ $item }}" {{ request('kategori') == $item ? 'selected' : '' }}>
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
                            <label for="status_lpi" class="form-label">Status LPI</label>
                            <select name="status_lpi" id="status_lpi" class="form-select">
                                <option value="">Semua</option>
                                @foreach ($statusLpiList as $item)
                                    <option value="{{ $item }}" {{ request('status_lpi') == $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="insiden_ccr_id" class="form-label">Insiden CCR</label>
                            <select name="insiden_ccr_id" id="insiden_ccr_id" class="form-select">
                                <option value="">Semua</option>
                                @foreach ($insidenCcrList as $item)
                                    <option value="{{ $item->id }}" {{ request('insiden_ccr_id') == $item->id ? 'selected' : '' }}>
                                        #{{ $item->ccr_id }} - {{ $item->ccr_jenis_insiden }} ({{ $item->ccr_site ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 d-grid">
                            <button type="submit" class="btn btn-primary rounded-3">Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card rounded-4 h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Upload Excel</h5>
                    <a href="{{ route('insiden-lpi.template') }}" class="btn btn-sm btn-outline-secondary rounded-3">
                        <i class="bx bx-download"></i> Template
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('insiden-lpi.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <label for="import_insiden_ccr_id" class="form-label">Pilih Insiden CCR (opsional)</label>
                            <select name="insiden_ccr_id" id="import_insiden_ccr_id" class="form-select form-select-sm">
                                <option value="">-- Tanpa relasi CCR --</option>
                                @foreach ($insidenCcrList as $item)
                                    <option value="{{ $item->id }}">
                                        #{{ $item->ccr_id }} - {{ $item->ccr_site ?? '-' }} - {{ $item->ccr_jenis_insiden }} ({{ $item->ccr_waktu_insiden?->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
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
                        <h5 class="mb-0">Data Insiden LPI</h5>
                        <small class="text-muted">Menampilkan {{ $insidens->firstItem() ?? 0 }}-{{ $insidens->lastItem() ?? 0 }}
                            dari {{ $insidens->total() }} data</small>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <select name="per_page" id="per_page" class="form-select form-select-sm" style="width: auto;" onchange="window.location.href='?per_page=' + this.value + '&{{ http_build_query(request()->except('per_page', 'page')) }}'">
                            @foreach ([10, 25, 50, 100] as $option)
                                <option value="{{ $option }}" {{ $perPage === $option ? 'selected' : '' }}>
                                    {{ $option }} per halaman
                                </option>
                            @endforeach
                        </select>
                        <a href="{{ route('insiden-lpi.create') }}" class="btn btn-primary rounded-3">Tambah Data</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-3" style="white-space: nowrap;">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>No Kecelakaan</th>
                                    <th>Insiden CCR</th>
                                    <th>Status LPI</th>
                                    <th>Kategori</th>
                                    <th>Site</th>
                                    <th>Perusahaan</th>
                                    <th>Nama</th>
                                    <th>Layer</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($insidens as $index => $insiden)
                                    <tr>
                                        <td>{{ $insidens->firstItem() + $index }}</td>
                                        <td>{{ $insiden->no_kecelakaan ?? '-' }}</td>
                                        <td>
                                            @if($insiden->insidenCcr)
                                                <a href="{{ route('insiden-ccr.edit', $insiden->insidenCcr) }}" class="text-decoration-none">
                                                    #{{ $insiden->insidenCcr->ccr_id }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($insiden->status_lpi)
                                                <span class="badge 
                                                    @if(strtolower($insiden->status_lpi) == 'open') bg-warning text-dark
                                                    @elseif(strtolower($insiden->status_lpi) == 'closed') bg-success
                                                    @else bg-secondary
                                                    @endif">
                                                    {{ $insiden->status_lpi }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $insiden->kategori ?? '-' }}</td>
                                        <td>{{ $insiden->site ?? '-' }}</td>
                                        <td>{{ Str::limit($insiden->perusahaan, 25) ?? '-' }}</td>
                                        <td>{{ $insiden->nama ?? '-' }}</td>
                                        <td>
                                            @if($insiden->layers->count() > 0)
                                                <span class="badge bg-info">{{ $insiden->layers->count() }} layer(s)</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-info rounded-3" 
                                                        data-bs-toggle="modal" data-bs-target="#detailModal{{ $insiden->id }}">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                <a href="{{ route('insiden-lpi.edit', $insiden) }}"
                                                    class="btn btn-sm btn-outline-primary rounded-3">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('insiden-lpi.destroy', $insiden) }}"
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
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center flex-column flex-lg-row gap-3">
                        <div class="text-muted">
                            Halaman {{ $insidens->currentPage() }} dari {{ $insidens->lastPage() }}
                        </div>
                        {{ $insidens->onEachSide(1)->links('pagination::bootstrap-5') }}
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
                            Detail Insiden LPI - {{ $insiden->no_kecelakaan }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            @if($insiden->insidenCcr)
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong>Terkait dengan Insiden CCR:</strong> 
                                    #{{ $insiden->insidenCcr->ccr_id }} - {{ $insiden->insidenCcr->ccr_jenis_insiden }}
                                    ({{ $insiden->insidenCcr->ccr_waktu_insiden?->format('d/m/Y H:i') }})
                                </div>
                            </div>
                            @endif
                            <div class="col-md-4">
                                <strong>No. Kecelakaan:</strong>
                                <p>{{ $insiden->no_kecelakaan ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Kode BE Investigasi:</strong>
                                <p>{{ $insiden->kode_be_investigasi ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Status LPI:</strong>
                                <p>{{ $insiden->status_lpi ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Target Penyelesaian:</strong>
                                <p>{{ $insiden->target_penyelesaian_lpi?->format('d/m/Y') ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Actual Penyelesaian:</strong>
                                <p>{{ $insiden->actual_penyelesaian_lpi?->format('d/m/Y') ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Ketepatan Waktu:</strong>
                                <p>{{ $insiden->ketepatan_waktu_lpi ?? '-' }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Kategori:</strong>
                                <p>{{ $insiden->kategori ?? '-' }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Site:</strong>
                                <p>{{ $insiden->site ?? '-' }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Lokasi:</strong>
                                <p>{{ $insiden->lokasi ?? '-' }}</p>
                            </div>
                            <div class="col-md-3">
                                <strong>Perusahaan:</strong>
                                <p>{{ $insiden->perusahaan ?? '-' }}</p>
                            </div>
                            <div class="col-12">
                                <strong>Kronologis:</strong>
                                <p class="bg-light p-2 rounded">{{ $insiden->kronologis ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Nama:</strong>
                                <p>{{ $insiden->nama ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Jabatan:</strong>
                                <p>{{ $insiden->jabatan ?? '-' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>NPK:</strong>
                                <p>{{ $insiden->npk ?? '-' }}</p>
                            </div>
                            {{-- Layers Section --}}
                            @if($insiden->layers->count() > 0)
                            <div class="col-12">
                                <strong>Layers ({{ $insiden->layers->count() }}):</strong>
                                <div class="table-responsive mt-2">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th width="100">Layer</th>
                                                <th width="120">Jenis Item IPLS</th>
                                                <th width="200">Detail Layer</th>
                                                <th>Keterangan Layer</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($insiden->layers as $layer)
                                            <tr>
                                                <td>{{ $layer->layer ?? '-' }}</td>
                                                <td>
                                                    @if($layer->jenis_item_ipls == 'Nonconformity')
                                                        <span class="badge bg-warning text-dark">{{ $layer->jenis_item_ipls }}</span>
                                                    @elseif($layer->jenis_item_ipls == 'Rootcause')
                                                        <span class="badge bg-danger">{{ $layer->jenis_item_ipls }}</span>
                                                    @else
                                                        {{ $layer->jenis_item_ipls ?? '-' }}
                                                    @endif
                                                </td>
                                                <td>{{ $layer->detail_layer ?? '-' }}</td>
                                                <td style="white-space: pre-wrap;">{{ $layer->keterangan_layer ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @else
                            <div class="col-12">
                                <strong>Layers:</strong>
                                <p class="text-muted">Belum ada data layer</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
                        <a href="{{ route('insiden-lpi.edit', $insiden) }}" class="btn btn-primary rounded-3">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
