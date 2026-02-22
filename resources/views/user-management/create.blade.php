@extends('layouts.master')

@section('title', 'Tambah User')
@section('content')
<x-page-title title="Master User" pagetitle="Tambah User Baru" />

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <a href="{{ route('user-management.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="material-icons-outlined">arrow_back</i>
                    </a>
                    <h5 class="mb-0 fw-bold">Form Tambah User</h5>
                </div>

                <p class="text-muted small">Email diisi dengan kode/text (bukan alamat email). Kolom wajib: Nama dan Email.</p>

                <form action="{{ route('user-management.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required maxlength="255">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email (Kode) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required maxlength="255" placeholder="Contoh: XUJG3, H5UBW">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Isi dengan teks/kode, tanpa @</div>
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                                <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>user</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>admin</option>
                                <option value="administrator" {{ old('role') === 'administrator' ? 'selected' : '' }}>administrator</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required minlength="6">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required minlength="6">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="material-icons-outlined">save</i> Simpan
                            </button>
                            <a href="{{ route('user-management.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
