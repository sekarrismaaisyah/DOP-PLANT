@extends('layouts.masterRoster')

@section('title', 'Master Data DOP')

@section('content')
    <x-page-title title="Master DOP" pagetitle="Master Data DOP" />

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
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('import_errors') && count(session('import_errors')) > 0)
                <div class="alert alert-warning alert-dismissible fade show rounded-4" role="alert">
                    <strong>Error saat import:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach (array_slice(session('import_errors'), 0, 10) as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        @if (count(session('import_errors')) > 10)
                            <li><em>... dan {{ count(session('import_errors')) - 10 }} error lainnya</em></li>
                        @endif
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <h5 class="mb-0">Daftar Daily Operation Plan</h5>
                        <small class="text-muted">Menampilkan {{ $dops->firstItem() ?? 0 }}-{{ $dops->lastItem() ?? 0 }} dari {{ $dops->total() }} data</small>
                    </div>
                    <div>
                        <a href="{{ route('sistem-roster.dop.create') }}" class="btn btn-primary rounded-3">
                            <i class="bx bx-plus"></i> Tambah DOP
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $siteOptions = \App\Models\DailyOperationPlan::query()
                            ->whereNotNull('unit_id')
                            ->distinct()
                            ->orderBy('unit_id')
                            ->pluck('unit_id');
                    @endphp
                    <form method="GET" action="{{ route('sistem-roster.dop.index') }}" class="row g-2 align-items-end mb-3">
                        <div class="col-md-4 col-lg-3">
                            <label for="site" class="form-label mb-1">Filter Site</label>
                            <select name="site" id="site" class="form-select">
                                <option value="">Semua Site</option>
                                @foreach($siteOptions as $site)
                                    <option value="{{ $site }}" {{ (string) $site === (string) request('site') ? 'selected' : '' }}>
                                        {{ $site }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <label for="search" class="form-label mb-1">Pencarian</label>
                            <input type="text" name="search" id="search" class="form-control"
                                   placeholder="Cari pekerjaan, aktivitas, lokasi, dll"
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 col-lg-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-3">
                                <i class="bx bx-filter-alt"></i> Terapkan
                            </button>
                            @if(request('site') || request('search'))
                                <a href="{{ route('sistem-roster.dop.index') }}" class="btn btn-outline-secondary rounded-3">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Pekerjaan</th>
                                    <th>Aktivitas</th>
                                    <th>Site</th>
                                    <th>Lokasi</th>
                                    <th>Detail Lokasi</th>
                                    <!-- <th>Longitude</th> -->
                                    <th>CCTV</th>
                                    <th>Foto</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dops as $dop)
                                    <tr>
                                        <td>{{ ($dops->firstItem() ?? 0) + $loop->index }}</td>
                                        <td>{{ $dop->tanggal->format('d M Y') }}</td>
                                        <td>{{ $dop->pekerjaan }}</td>
                                        <td>{{ $dop->aktivitas ?? '-' }}</td>
                                        <td>{{ $dop->unit_id }}</td>
                                        <td>{{ $dop->lokasi }}</td>
                                        <td>{{ $dop->detail_lokasi ?? '-' }}</td>
                                        <!-- <td>{{ $dop->longitude ?? '-' }}</td> -->
                                        <td>
                                            @if($dop->cctvs->count() > 0)
                                                <span class="badge bg-info">{{ $dop->cctvs->count() }} CCTV</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($dop->foto_pekerjaan)
                                                <a href="{{ asset('storage/' . $dop->foto_pekerjaan) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bx bx-image"></i> Lihat
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle" type="checkbox" role="switch" 
                                                    data-id="{{ $dop->id }}" 
                                                    {{ $dop->status ? 'checked' : '' }}
                                                    style="cursor: pointer; width: 3em; height: 1.5em;">
                                                <span class="status-label badge {{ $dop->status ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $dop->status ? 'ON' : 'OFF' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('sistem-roster.dop.show', $dop->id) }}" class="btn btn-sm btn-info rounded-3" title="Detail">
                                                    <i class="material-icons-outlined text-white">visibility</i>
                                                </a>
                                                <a href="{{ route('sistem-roster.dop.edit', $dop->id) }}" class="btn btn-sm btn-warning rounded-3" title="Edit">
                                                    <i class="material-icons-outlined">edit</i>
                                                </a>
                                                <form action="{{ route('sistem-roster.dop.destroy', $dop->id) }}" method="POST" class="d-inline form-delete-dop" data-pekerjaan="{{ e($dop->pekerjaan) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger rounded-3 btn-delete-dop" title="Hapus">
                                                        <i class="material-icons-outlined">delete</i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-muted">Belum ada data DOP</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center flex-column flex-lg-row gap-3">
                        <div class="text-muted">
                            Menampilkan {{ $dops->firstItem() ?? 0 }}-{{ $dops->lastItem() ?? 0 }} dari {{ $dops->total() }} data
                        </div>
                        {{ $dops->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Konfirmasi hapus DOP dengan SweetAlert
            document.querySelectorAll('.btn-delete-dop').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const form = this.closest('form.form-delete-dop');
                    const pekerjaan = form ? form.getAttribute('data-pekerjaan') || 'DOP ini' : 'DOP ini';
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: 'Apakah Anda yakin akan menghapus "' + pekerjaan + '"? Data yang dihapus tidak dapat dikembalikan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal'
                    }).then(function(result) {
                        if (result.isConfirmed && form) {
                            form.submit();
                        }
                    });
                });
            });

            document.querySelectorAll('.status-toggle').forEach(function(toggle) {
                toggle.addEventListener('change', function() {
                    const dopId = this.dataset.id;
                    const checkbox = this;
                    const statusLabel = this.parentElement.querySelector('.status-label');
                    
                    fetch(`{{ url('sistem-roster/dop') }}/${dopId}/toggle-status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.status) {
                                statusLabel.textContent = 'ON';
                                statusLabel.classList.remove('bg-secondary');
                                statusLabel.classList.add('bg-success');
                            } else {
                                statusLabel.textContent = 'OFF';
                                statusLabel.classList.remove('bg-success');
                                statusLabel.classList.add('bg-secondary');
                            }
                        } else {
                            checkbox.checked = !checkbox.checked;
                            alert('Gagal mengubah status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        checkbox.checked = !checkbox.checked;
                        alert('Terjadi kesalahan');
                    });
                });
            });
        });
    </script>
@endsection
