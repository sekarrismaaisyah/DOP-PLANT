@extends('layouts.master')

@section('title', 'HSE Validation')
@section('css')
    <link href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection 
@section('content')
<x-page-title title="HSE Validation" pagetitle="Validasi Laporan HSE Berbasis AI" />

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <h5 class="mb-3 fw-bold">Validasi HSE Hari Ini</h5>
                <p class="text-muted mb-4">
                    Sistem akan mengambil data dari ClickHouse database untuk hari ini (<strong>{{ $validation_date }}</strong>)
                    dan melakukan validasi otomatis menggunakan AI untuk setiap temuan HSE.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="mb-3">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Informasi Validasi:</h6>
                        <ul class="mb-0">
                            <li>Data diambil dari tabel <strong>hse_automation.aaj_car_all_year_from_dav</strong> di ClickHouse</li>
                            <li>Hanya data dengan tanggal hari ini yang akan divalidasi</li>
                            <li>Hasil validasi akan disimpan ke database dengan semua field original + hasil klasifikasi AI</li>
                            <li>Klasifikasi AI meliputi: TBC, PSPP, GR, dan Incident</li>
                        </ul>
                    </div>
                </div>

                @if($validated_count > 0)
                    <div class="alert alert-warning">
                        <strong>Perhatian:</strong> Sudah ada <strong>{{ $validated_count }}</strong> data yang divalidasi untuk hari ini. 
                        Memulai validasi lagi akan memproses ulang semua data hari ini.
                    </div>
                @endif

                <form action="{{ route('hse-validation.process') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="ri-play-circle-line me-1"></i> Mulai Validasi Data Hari Ini
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 
@section('scripts')  
    <script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
@endsection

