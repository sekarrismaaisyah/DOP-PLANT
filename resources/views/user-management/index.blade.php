@extends('layouts.master')

@section('title', 'Master User')
@section('content')
<x-page-title title="Master User" pagetitle="Manajemen Akun User" />

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('import_errors') && count(session('import_errors')) > 0)
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Peringatan:</strong> Beberapa data gagal diimpor:
    <ul class="mb-0 mt-2 small">
        @foreach(session('import_errors') as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h5 class="mb-0 fw-bold">Daftar User</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('user-management.import-form') }}" class="btn btn-success btn-sm">
                            <i class="material-icons-outlined">upload_file</i> Import Excel
                        </a>
                        <a href="{{ route('user-management.create') }}" class="btn btn-primary btn-sm">
                            <i class="material-icons-outlined">add</i> Tambah User
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email (Kode)</th>
                                <th>Role</th>
                                <th width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td><code>{{ $user->email }}</code></td>
                                <td>
                                    <span class="badge bg-{{ $user->role === 'admin' || $user->role === 'administrator' ? 'danger' : 'secondary' }}">
                                        {{ $user->role ?? 'user' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('user-management.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="material-icons-outlined" style="font-size:18px">edit</i>
                                    </a>
                                    <form action="{{ route('user-management.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="material-icons-outlined" style="font-size:18px">delete</i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada user.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
