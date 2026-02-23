@extends('layouts.masterDopm')

@section('title', 'Alert Log - DOPM & IKK')

@section('content')
<x-page-title title="Alert Log" pagetitle="DOPM & IKK - Riwayat Alert per Jam" />

<div class="row">
    <div class="col-12">
        <div class="card rounded-4 mb-3">
            <div class="card-body py-3">
                <form method="get" action="{{ route('dopmikk.dopm.alert-log') }}" class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label for="filterDate" class="form-label small fw-semibold text-muted">Tanggal</label>
                        <input type="date" name="date" id="filterDate" class="form-control rounded-3" value="{{ $filterDate ?? now()->toDateString() }}">
                    </div>
                    <div class="col-12 col-md-auto">
                        <button type="submit" class="btn btn-primary rounded-3 px-4">
                            <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">search</i>
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card rounded-4">
            <div class="card-header d-flex align-items-center gap-2">
                <span class="material-icons-outlined text-danger">warning</span>
                <h5 class="mb-0 fw-bold">Riwayat Alert per Jam</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-4">
                    Need Action = IKK status Merah, Warning = IKK status Kuning. Data tersimpan saat dashboard dibuka (tanggal hari ini) dan dari scheduler per jam.
                </p>
                @php $dopmAlertLogs = $dopmAlertLogs ?? collect(); @endphp
                @if($dopmAlertLogs->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <span class="material-icons-outlined mb-2" style="font-size: 48px;">schedule</span>
                        <p class="mb-0">Belum ada data alert untuk tanggal <strong>{{ $filterDate ?? now()->toDateString() }}</strong>.</p>
                        <p class="mb-0 small mt-1">Buka Dashboard Daily pada tanggal hari ini agar snapshot per jam tersimpan.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle rounded-4">
                            <thead class="table-light">
                                <tr>
                                    <th class="rounded-start">Jam</th>
                                    <th>Need Action (Merah)</th>
                                    <th>Warning (Kuning)</th>
                                    <th class="rounded-end">Terakhir update</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dopmAlertLogs as $log)
                                    <tr>
                                        <td class="fw-semibold">Jam {{ sprintf('%02d', $log->jam) }}:00</td>
                                        <td><span class="badge bg-danger rounded-pill">{{ $log->need_action_count ?? 0 }}</span></td>
                                        <td><span class="badge bg-warning text-dark rounded-pill">{{ $log->warning_count ?? 0 }}</span></td>
                                        <td class="text-muted small">{{ $log->updated_at ? $log->updated_at->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
