@extends('layouts.master')

@section('title', 'Evaluasi Unit Per Hari - Fueling')

@section('css')
<style>
    .evaluasi-perhari-table { border-collapse: separate; border-spacing: 0; }
    .evaluasi-perhari-table thead th {
        font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.02em;
        color: #475569; border-bottom: 2px solid #e2e8f0; padding: 0.75rem 1rem; background: #f8fafc;
    }
    .evaluasi-perhari-table tbody td {
        padding: 0.875rem 1rem; vertical-align: middle; border-bottom: 1px solid #f1f3f5;
    }
    .evaluasi-perhari-table tbody tr:hover { background-color: #f8fafc; }
    .evaluasi-perhari-table .text-jarak { font-weight: 600; color: #0369a1; }
</style>
@endsection

@section('content')
    <x-page-title title="Evaluasi Unit Per Hari" pagetitle="Jarak & Durasi Masing-masing Unit per Tanggal" />

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

    <div class="row mb-4">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-body">
                    <form method="get" action="{{ route('fueling-evaluasi.per-hari') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Dari</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? '' }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Sampai</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? '' }}" required>
                        </div>
                        <div class="col-md-4 d-flex gap-2 align-items-center flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="material-icons-outlined" style="font-size: 1rem; vertical-align: middle;">search</i>
                                Filter
                            </button>
                            @if (isset($dateFrom) && isset($dateTo))
                                <a href="{{ route('fueling-evaluasi.per-hari.export-excel', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                                   class="btn btn-success" target="_blank" rel="noopener">
                                    <i class="material-icons-outlined" style="font-size: 1rem; vertical-align: middle;">download</i>
                                    Download Excel
                                </a>
                            @endif
                            <a href="{{ route('fueling-evaluasi.tabel') }}" class="btn btn-outline-secondary">Tabel per Unit</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Per Hari per Unit (masing-masing unit)</h5>
                </div>
                <div class="card-body p-0">
                    @php $dailyPerUnit = $dailyPerUnit ?? []; @endphp
                    @if (count($dailyPerUnit) > 0)
                        <div class="table-responsive">
                            <table class="table evaluasi-perhari-table mb-0">
                                <thead>
                                    <tr>
                                        <th>TANGGAL</th>
                                        <th>NO UNIT</th>
                                        <th>JARAK YANG DITEMPUH</th>
                                        <th>DURASI (jam)</th>
                                        <th>Perusahaan Pemilik</th>
                                        <th>Site Operasional</th>
                                        <th>Jenis Unit SPIP</th>
                                        <th>Expired</th>
                                        <th>Status Permit SPIP</th>
                                        <th>MTD</th>
                                        <th>AVG per Day</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dailyPerUnit as $row)
                                        <tr>
                                            <td>{{ $row['tanggal'] }}</td>
                                            <td>{{ $row['no_unit'] }}</td>
                                            <td class="text-jarak">{{ $row['jarak'] }}</td>
                                            <td>{{ $row['total_jam'] }} jam</td>
                                            <td>{{ $row['perusahaan_pemilik'] ?? '-' }}</td>
                                            <td>{{ $row['site_operasional'] ?? '-' }}</td>
                                            <td>{{ $row['jenis_unit_spip'] ?? '-' }}</td>
                                            <td>{{ isset($row['expired']) && $row['expired'] ? $row['expired'] : '-' }}</td>
                                            <td>{{ $row['status_permit_spip'] ?? '-' }}</td>
                                            <td>{{ isset($row['mtd']) && $row['mtd'] !== null ? number_format($row['mtd'], 2, ',', '.') : '-' }}</td>
                                            <td>{{ isset($row['avg_per_day']) && $row['avg_per_day'] !== null ? number_format($row['avg_per_day'], 2, ',', '.') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="material-icons-outlined" style="font-size: 48px;">inbox</i>
                            <p class="mt-2 mb-0">Tidak ada data untuk rentang tanggal yang dipilih.</p>
                            <p class="small">Gunakan filter di atas dan klik <strong>Filter</strong>.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
