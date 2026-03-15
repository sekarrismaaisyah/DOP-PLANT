@extends('layouts.master')

@section('title', 'Evaluasi Unit - Fueling')

@section('css')
<style>
    .evaluasi-unit-table { border-collapse: separate; border-spacing: 0; }
    .evaluasi-unit-table thead th {
        font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.02em;
        color: #475569; border-bottom: 2px solid #e2e8f0; padding: 0.75rem 1rem; background: #f8fafc;
    }
    .evaluasi-unit-table tbody td {
        padding: 0.875rem 1rem; vertical-align: middle; border-bottom: 1px solid #f1f3f5;
    }
    .evaluasi-unit-table tbody tr:hover { background-color: #f8fafc; }
    .evaluasi-unit-table .text-jarak { font-weight: 600; color: #0369a1; }
    .evaluasi-unit-table .text-tanggal { font-size: 0.875rem; color: #64748b; }
</style>
@endsection

@section('content')
    <x-page-title title="Evaluasi Unit" pagetitle="Tabel Evaluasi Fuelling Unit" />

    <div class="row">
        <div class="col-12">
            @if (isset($error) && $error)
                <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
                    <strong>Error:</strong> {{ $error }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    {{-- Filter tanggal & Export --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-body">
                    <form method="get" action="{{ route('fueling-evaluasi.tabel') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Dari</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? '' }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Sampai</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? '' }}" required>
                        </div>
                        <div class="col-md-4 d-flex gap-2 align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="material-icons-outlined" style="font-size: 1rem; vertical-align: middle;">search</i>
                                Filter
                            </button>
                            @if (isset($dateFrom) && isset($dateTo))
                                <a href="{{ route('full-maps.export-evaluasi-unit-excel', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                                   class="btn btn-success" target="_blank" rel="noopener">
                                    <i class="material-icons-outlined" style="font-size: 1rem; vertical-align: middle;">download</i>
                                    Download Excel
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel NO UNIT | JARAK | WAKTU AKTIF | TANGGAL --}}
    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Data Evaluasi Unit</h5>
                </div>
                <div class="card-body p-0">
                    @php $evaluasiUnits = $evaluasiUnits ?? []; @endphp
                    @if (count($evaluasiUnits) > 0)
                        <div class="table-responsive">
                            <table class="table evaluasi-unit-table mb-0">
                                <thead>
                                    <tr>
                                        <th>NO UNIT</th>
                                        <th>JARAK YANG DITEMPUH</th>
                                        <th>WAKTU AKTIF (total jam)</th>
                                        <th>TANGGAL HARI AKTIF / ADA LOG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($evaluasiUnits as $row)
                                        <tr>
                                            <td>{{ $row['no_unit'] }}</td>
                                            <td class="text-jarak">{{ $row['jarak'] }}</td>
                                            <td>{{ $row['waktu_jam'] }} jam</td>
                                            <td class="text-tanggal">{{ $row['tanggal_aktif'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="material-icons-outlined" style="font-size: 48px;">inbox</i>
                            @if (!isset($dateFrom) && !isset($dateTo))
                                <p class="mt-2 mb-0">Buka halaman tabel evaluasi unit dengan rentang tanggal.</p>
                                <a href="{{ route('fueling-evaluasi.tabel') }}" class="btn btn-primary mt-3">Buka Tabel Evaluasi Unit</a>
                            @else
                                <p class="mt-2 mb-0">Tidak ada data untuk rentang tanggal yang dipilih.</p>
                                <p class="small">Gunakan filter di atas dan klik <strong>Filter</strong>, atau pastikan ClickHouse Nitip terhubung.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
