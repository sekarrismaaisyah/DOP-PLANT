@extends('layouts.master')

@section('title', 'Manajemen User')

@section('content')
    <x-page-title title="Manajemen User" pagetitle="User Management" />

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
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <h5 class="mb-0">Daftar User</h5>
                        <small class="text-muted">Menampilkan {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }}
                            dari {{ $users->total() }} user</small>
                    </div>
                    <a href="{{ route('users.create') }}" class="btn btn-primary rounded-3">Tambah User</a>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label for="search" class="form-label">Cari</label>
                            <input type="text" name="search" id="search" value="{{ $search }}" class="form-control"
                                placeholder="Nama / Email / Role">
                        </div>
                        <div class="col-md-2">
                            <label for="per_page" class="form-label">Baris per halaman</label>
                            <select name="per_page" id="per_page" class="form-select">
                                @foreach ([10, 25, 50, 100] as $option)
                                    <option value="{{ $option }}" {{ $perPage === $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-grid">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-outline-primary rounded-3">Cari</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $loop->index }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'primary' }} rounded-3">
                                                {{ $user->role ?? 'user' }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning rounded-3">
                                                    Edit
                                                </a>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" 
                                                      onsubmit="return confirm('Yakin ingin menghapus user ini?');" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger rounded-3">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data user</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

