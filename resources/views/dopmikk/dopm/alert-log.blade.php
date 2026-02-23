@extends('layouts.masterDopm')

@section('title', 'Alert Log - DOPM & IKK')

@section('content')
<x-page-title title="Alert Log" pagetitle="DOPM & IKK - Data IKK/Work Permit per Jam (Need Action & Warning)" />

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
                <h5 class="mb-0 fw-bold">Data IKK / Work Permit per Jam</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-4">
                    Per jam ditampilkan daftar IKK yang status matriks <strong>Merah (Need Action)</strong> dan <strong>Kuning (Warning)</strong>. Data tersimpan saat dashboard dibuka (tanggal hari ini) dan dari scheduler per jam.
                </p>
                @php $dopmAlertLogs = $dopmAlertLogs ?? collect(); @endphp
                @if($dopmAlertLogs->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <span class="material-icons-outlined mb-2" style="font-size: 48px;">schedule</span>
                        <p class="mb-0">Belum ada data alert untuk tanggal <strong>{{ $filterDate ?? now()->toDateString() }}</strong>.</p>
                        <p class="mb-0 small mt-1">Buka Dashboard Daily pada tanggal hari ini agar snapshot per jam tersimpan.</p>
                    </div>
                @else
                    <div class="accordion accordion-flush" id="alertLogAccordion">
                        @foreach($dopmAlertLogs as $idx => $log)
                            @php
                                $snap = $log->snapshot ?? [];
                                $needActionList = $snap['need_action'] ?? [];
                                $warningList = $snap['warning'] ?? [];
                                $collapseId = 'collapse-' . $log->id;
                            @endphp
                            <div class="accordion-item border rounded-3 mb-2">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $idx > 0 ? 'collapsed' : '' }} py-3" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="{{ $idx === 0 ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                                        <span class="d-flex align-items-center gap-3 flex-wrap">
                                            <strong class="text-nowrap">Jam {{ sprintf('%02d', $log->jam) }}:00</strong>
                                            <span class="badge bg-danger rounded-pill">{{ count($needActionList) }} Need Action</span>
                                            <span class="badge bg-warning text-dark rounded-pill">{{ count($warningList) }} Warning</span>
                                            <span class="text-muted small">Update: {{ $log->updated_at ? $log->updated_at->format('d/m/Y H:i') : '-' }}</span>
                                        </span>
                                    </button>
                                </h2>
                                <div id="{{ $collapseId }}" class="accordion-collapse collapse {{ $idx === 0 ? 'show' : '' }}" data-bs-parent="#alertLogAccordion">
                                    <div class="accordion-body pt-0 pb-4">
                                        {{-- Need Action (Merah) --}}
                                        <div class="mb-4">
                                            <h6 class="text-danger fw-bold mb-2 d-flex align-items-center gap-1">
                                                <span class="material-icons-outlined" style="font-size: 20px;">error</span>
                                                Need Action / Merah ({{ count($needActionList) }} IKK)
                                            </h6>
                                            @if(empty($needActionList))
                                                <p class="text-muted small mb-0">Tidak ada IKK status Merah pada jam ini.</p>
                                            @else
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered table-hover align-middle mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Kode IKK</th>
                                                                <th>Jenis IJK</th>
                                                                <th>Site</th>
                                                                <th>Nama Pekerjaan</th>
                                                                <th>Lokasi</th>
                                                                <th>Alasan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($needActionList as $ikk)
                                                                <tr>
                                                                    <td class="fw-semibold">{{ $ikk['code'] ?? '-' }}</td>
                                                                    <td>{{ $ikk['jenis_ijin_kerja_khusus'] ?? '-' }}</td>
                                                                    <td>{{ $ikk['site'] ?? '-' }}</td>
                                                                    <td>{{ $ikk['nama_pekerjaan'] ?? '-' }}</td>
                                                                    <td>{{ ($ikk['location_name'] ?? '') . ($ikk['location_detail_name'] ? ' / ' . $ikk['location_detail_name'] : '') ?: '-' }}</td>
                                                                    <td class="small text-muted" title="{{ $ikk['alasan_matriks'] ?? '' }}">{{ Str::limit($ikk['alasan_matriks'] ?? '-', 60) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                        {{-- Warning (Kuning) --}}
                                        <div>
                                            <h6 class="text-warning fw-bold mb-2 d-flex align-items-center gap-1">
                                                <span class="material-icons-outlined text-dark" style="font-size: 20px;">info</span>
                                                Warning / Kuning ({{ count($warningList) }} IKK)
                                            </h6>
                                            @if(empty($warningList))
                                                <p class="text-muted small mb-0">Tidak ada IKK status Kuning pada jam ini.</p>
                                            @else
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered table-hover align-middle mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Kode IKK</th>
                                                                <th>Jenis IJK</th>
                                                                <th>Site</th>
                                                                <th>Nama Pekerjaan</th>
                                                                <th>Lokasi</th>
                                                                <th>Alasan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($warningList as $ikk)
                                                                <tr>
                                                                    <td class="fw-semibold">{{ $ikk['code'] ?? '-' }}</td>
                                                                    <td>{{ $ikk['jenis_ijin_kerja_khusus'] ?? '-' }}</td>
                                                                    <td>{{ $ikk['site'] ?? '-' }}</td>
                                                                    <td>{{ $ikk['nama_pekerjaan'] ?? '-' }}</td>
                                                                    <td>{{ ($ikk['location_name'] ?? '') . ($ikk['location_detail_name'] ? ' / ' . $ikk['location_detail_name'] : '') ?: '-' }}</td>
                                                                    <td class="small text-muted" title="{{ $ikk['alasan_matriks'] ?? '' }}">{{ Str::limit($ikk['alasan_matriks'] ?? '-', 60) }}</td>
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
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
