@extends('layouts.masterDopm')

@section('title', 'Alert Log - DOPM & IKK')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}">
<style>
.alert-log-page .filter-card { border-radius: 1rem; }
.alert-log-page .main-card { border-radius: 1rem; overflow: hidden; }
.alert-log-page .accordion-item { border-radius: 0.75rem; margin-bottom: 0.75rem; border: 1px solid rgba(0,0,0,.08); }
.alert-log-page .accordion-button { font-size: 1rem; background: #f8f9fa; }
.alert-log-page .accordion-button:not(.collapsed) { background: #fff; box-shadow: 0 -1px 0 0 rgba(0,0,0,.05); }
.alert-log-page .accordion-body { padding: 1rem 1.25rem; background: #fff; }
.alert-log-page .table-section { margin-bottom: 1.5rem; }
.alert-log-page .table-section:last-child { margin-bottom: 0; }
.alert-log-page .table-section-title { font-size: 0.95rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.35rem; }
.alert-log-page .dataTables_wrapper .row:first-child { margin-bottom: 0.75rem; }
.alert-log-page .dataTables_wrapper .dataTables_length label, .alert-log-page .dataTables_wrapper .dataTables_filter label { margin-bottom: 0; }
.alert-log-page table.dataTable { width: 100% !important; }
.alert-log-page table.dataTable thead th { border-bottom: 2px solid #e9ecef; white-space: nowrap; }
.alert-log-page .empty-state { padding: 3rem 1rem; text-align: center; color: #6c757d; }
</style>
@endsection

@section('content')
<div class="alert-log-page">
    <x-page-title title="Alert Log" pagetitle="DOPM & IKK - Data IKK/Work Permit per IKK (Status Alert 1, 2, 3)" />

    <div class="row">
        <div class="col-12">
            <div class="card filter-card shadow-sm mb-4">
                <div class="card-body py-3">
                    <form method="get" action="{{ route('dopmikk.dopm.alert-log') }}" class="row g-3 align-items-end">
                        <div class="col-12 col-md-4 col-lg-3">
                            <label for="filterDate" class="form-label small fw-semibold text-muted mb-1">Tanggal</label>
                            <input type="date" name="date" id="filterDate" class="form-control form-control-sm rounded-3" value="{{ $filterDate ?? now()->toDateString() }}">
                        </div>
                        <div class="col-12 col-md-auto">
                            <button type="submit" class="btn btn-primary btn-sm rounded-3 px-4">
                                <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">search</i>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card main-card shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="material-icons-outlined text-danger">warning</span>
                        <h5 class="mb-0 fw-bold">Data Alert IKK per Baris</h5>
                    </div>
                    <p class="mb-0 small text-muted mt-1">
                        Setiap baris mewakili satu alert dari tabel <code>dopm_alert_per_ikk</code>:
                        per tanggal, per kode IKK, dan per <strong>alert ke-1/2/3</strong>.
                        Jika sudah terintervensi di level tertentu, level di atasnya tidak akan dibuat lagi.
                    </p>
                </div>
                <div class="card-body">
                    @php $alertRows = $alertRows ?? collect(); @endphp
                    @if($alertRows->isEmpty())
                        <div class="empty-state">
                            <span class="material-icons-outlined mb-2" style="font-size: 48px;">schedule</span>
                            <p class="mb-0 fw-medium">Belum ada data alert untuk tanggal <strong>{{ $filterDate ?? now()->toDateString() }}</strong>.</p>
                            <p class="mb-0 small mt-1">Buka Dashboard Daily pada tanggal hari ini agar snapshot per jam tersimpan.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table id="table-alert-log-ikk" class="table table-sm table-bordered table-hover align-middle alert-log-datatable mb-0" width="100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal Alert</th>
                                        <th>Kode IKK</th>
                                        <th>Alert Ke</th>
                                        <th>Tipe</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Jam Mulai</th>
                                        <th>Jenis IJK</th>
                                        <th>Site</th>
                                        <th>Nama Pekerjaan</th>
                                        <th>Lokasi</th>
                                        <th>Alasan</th>
                                        <th>Intervensi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alertRows as $row)
                                        @php
                                            $snap = $row->ikk_snapshot ?? [];
                                            $kodeIkk = $row->kode_ikk ?? '';
                                            $tanggal = $row->tanggal ? $row->tanggal->format('Y-m-d') : ($filterDate ?? now()->toDateString());
                                            $alertLevel = (int) ($row->alert_level ?? 1);
                                            $intervensiLevels = ($intervensiLevelsByIkk[$kodeIkk] ?? []);
                                            $terintervensi = in_array($alertLevel, $intervensiLevels, true);
                                        @endphp
                                        <tr>
                                            <td>{{ $tanggal }}</td>
                                            <td class="fw-semibold">{{ $kodeIkk ?: '-' }}</td>
                                            <td>Alert {{ $alertLevel }}</td>
                                            <td>
                                                @if(($snap['type'] ?? 'need_action') === 'need_action')
                                                    <span class="badge bg-danger">Need Action</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Warning</span>
                                                @endif
                                            </td>
                                            <td>{{ $snap['start_date_tanggal'] ?? '-' }}</td>
                                            <td>{{ $snap['start_date_jam'] ?? '-' }}</td>
                                            <td>{{ $snap['jenis_ijin_kerja_khusus'] ?? '-' }}</td>
                                            <td>{{ $snap['site'] ?? '-' }}</td>
                                            <td>{{ $snap['nama_pekerjaan'] ?? '-' }}</td>
                                            <td>{{ trim(($snap['location_name'] ?? '') . (($snap['location_detail_name'] ?? '') ? ' / ' . ($snap['location_detail_name'] ?? '') : '')) ?: '-' }}</td>
                                            <td class="small text-muted" title="{{ $snap['alasan_matriks'] ?? '' }}">{{ Str::limit($snap['alasan_matriks'] ?? '-', 50) }}</td>
                                            <td class="align-middle">
                                                @if($terintervensi)
                                                    <span class="badge bg-success me-1">Terintervensi</span>
                                                @else
                                                    <span class="badge bg-warning text-dark me-1">Belum terintervensi</span>
                                                @endif
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-warning btn-intervensi-alert-log mt-1"
                                                    data-kode-ikk="{{ $kodeIkk }}"
                                                    data-tanggal="{{ $tanggal }}"
                                                    data-alert-level="{{ $alertLevel }}"
                                                    title="Intervensi Alert ke-{{ $alertLevel }}">
                                                    <span class="material-icons-outlined" style="font-size:16px;">campaign</span>
                                                </button>
                                            </td>
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

    {{-- Modal Intervensi DOPM (sama seperti Dashboard): PJO, IPK-IKK, OKK, OAK + Layer 1/2/3/4 --}}
    <div class="modal fade" id="intervensiDopmModal" tabindex="-1" aria-labelledby="intervensiDopmModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg border border-light">
                <div class="modal-header rounded-top-4 py-3 bg-warning bg-opacity-10">
                    <div class="d-flex align-items-center flex-grow-1">
                        <span class="material-icons-outlined me-2 fs-4 text-warning">campaign</span>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="intervensiDopmModalLabel">
                                <span id="intervensiDopmTitle">Intervensi DOPM</span>
                            </h5>
                            <small class="text-muted" id="intervensiDopmSubtitle">Kode IKK: —</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div id="intervensiPjoWrap" class="intervensi-section mb-4 d-none">
                        <h6 class="text-info border-bottom pb-2 mb-3"><i class="material-icons-outlined align-middle me-1" style="font-size:20px;">person</i> PJO — Intervensi by WA</h6>
                        <div class="card border-info mb-3">
                            <div class="card-header bg-info bg-opacity-10 py-2">
                                <span class="material-icons-outlined align-middle me-1 text-info">contact_phone</span>
                                <strong>PJO Work Permit: <span id="intervensiPjoNameDisplay" class="text-dark">—</span></strong>
                            </div>
                            <div class="card-body py-3">
                                <p class="small text-muted mb-2">Kirim intervensi via WhatsApp ke nomor PJO yang terdaftar (pencarian by nama).</p>
                                <div id="intervensiPjoUsers" class="d-flex flex-wrap gap-2"></div>
                                <div id="intervensiPjoEmpty" class="text-muted small d-none">Tidak ada user terdaftar dengan nama tersebut.</div>
                                <div id="intervensiPjoLoading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1" role="status"></span>Memuat nomor PJO...</div>
                            </div>
                        </div>
                    </div>
                    <div class="intervensi-section mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="material-icons-outlined align-middle me-1" style="font-size:20px;">checklist</i> IPK-IKK <span class="badge bg-primary ms-1" id="intervensiBadgeIpk">0</span></h6>
                        <div id="intervensiLayer1Wrap" class="card border-warning mb-3">
                            <div class="card-header bg-warning bg-opacity-10 py-2">
                                <span class="material-icons-outlined align-middle me-1 text-warning">notifications_active</span>
                                <strong>Layer 1 — Pengingat Isi IPK (INSPEKSI PRA KERJA)</strong>
                            </div>
                            <div class="card-body py-3">
                                <p class="small mb-2"><strong>Nama Layer:</strong> <span id="intervensiLayer1NameDisplay" class="text-dark">—</span></p>
                                <p class="small text-muted mb-2">Klik tombol di bawah untuk langsung kirim intervensi via WhatsApp.</p>
                                <div id="intervensiLayer1Users" class="d-flex flex-wrap gap-2"></div>
                                <div id="intervensiLayer1Empty" class="text-muted small d-none">Tidak ada user terdaftar untuk Layer 1 ini.</div>
                                <div id="intervensiLayer1NoName" class="text-muted small d-none">Kolom <strong>SID Layer 1</strong> atau <strong>Nama Layer 1</strong> untuk DOPM ini belum diisi.</div>
                                <div id="intervensiLayer1Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1" role="status"></span>Memuat daftar PIC Layer 1...</div>
                            </div>
                        </div>
                        <div id="intervensiIpkLoading" class="text-center py-3 d-none"><div class="spinner-border text-primary spinner-border-sm" role="status"></div><p class="text-muted mb-0 mt-2 small">Memuat data IPK-IKK...</p></div>
                        <div id="intervensiIpkEmpty" class="text-center py-3 d-none"><span class="material-icons-outlined text-muted" style="font-size: 32px;">inbox</span><p class="text-muted mt-2 mb-0 small">Tidak ada data IPK-IKK.</p></div>
                        <div id="intervensiIpkTableWrap" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="intervensiTableIpk">
                                    <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Durasi</th><th>CCTV</th><th>Kategori IJK</th><th>Status</th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="intervensi-section mb-4">
                        <h6 class="text-success border-bottom pb-2 mb-3"><i class="material-icons-outlined align-middle me-1" style="font-size:20px;">folder_open</i> OKK <span class="badge bg-success ms-1" id="intervensiBadgeOkk">0</span></h6>
                        <div id="intervensiOkkLayer1Wrap" class="card border-success mb-3">
                            <div class="card-header bg-success bg-opacity-10 py-2">
                                <span class="material-icons-outlined align-middle me-1 text-success">notifications_active</span>
                                <strong>Layer 1 — Intervensi OKK (OBSERVASI KEGIATAN KERJA)</strong>
                            </div>
                            <div class="card-body py-3">
                                <p class="small mb-2"><strong>Nama Layer:</strong> <span id="intervensiOkkLayer1NameDisplay" class="text-dark">—</span></p>
                                <p class="small text-muted mb-2">Klik tombol di bawah untuk langsung kirim intervensi via WhatsApp.</p>
                                <div id="intervensiOkkLayer1Users" class="d-flex flex-wrap gap-2"></div>
                                <div id="intervensiOkkLayer1Empty" class="text-muted small d-none">Tidak ada user terdaftar untuk Layer 1 ini.</div>
                                <div id="intervensiOkkLayer1NoName" class="text-muted small d-none">Kolom <strong>SID Layer 1</strong> atau <strong>Nama Layer 1</strong> untuk DOPM ini belum diisi.</div>
                                <div id="intervensiOkkLayer1Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1" role="status"></span>Memuat daftar PIC Layer 1...</div>
                            </div>
                        </div>
                        <div id="intervensiOkkLoading" class="text-center py-3 d-none"><div class="spinner-border text-success spinner-border-sm" role="status"></div><p class="text-muted mb-0 mt-2 small">Memuat data OKK...</p></div>
                        <div id="intervensiOkkEmpty" class="text-center py-3 d-none"><span class="material-icons-outlined text-muted" style="font-size: 32px;">inbox</span><p class="text-muted mt-2 mb-0 small">Tidak ada data OKK.</p></div>
                        <div id="intervensiOkkTableWrap" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="intervensiTableOkk">
                                    <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Jenis IJK</th><th>Layer</th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="intervensi-section">
                        <h6 class="text-warning text-dark border-bottom pb-2 mb-3"><i class="material-icons-outlined align-middle me-1" style="font-size:20px;">visibility</i> OAK <span class="badge bg-warning text-dark ms-1" id="intervensiBadgeOak">0</span></h6>
                        <div id="intervensiOakLayersWrap" class="card border-warning mb-3">
                            <div class="card-header bg-warning bg-opacity-10 py-2">
                                <span class="material-icons-outlined align-middle me-1 text-warning">notifications_active</span>
                                <strong>Intervensi OAK — Layer 2, 3, 4</strong>
                            </div>
                            <div class="card-body py-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="border rounded p-2 bg-light">
                                            <p class="small mb-1 fw-semibold">Layer 2</p>
                                            <p class="small mb-1 text-muted"><strong>Nama:</strong> <span id="intervensiOakLayer2Name" class="text-dark">—</span></p>
                                            <div id="intervensiOakLayer2Users" class="d-flex flex-wrap gap-1"></div>
                                            <div id="intervensiOakLayer2Empty" class="text-muted small d-none">Tidak ada user.</div>
                                            <div id="intervensiOakLayer2Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1"></span>Memuat...</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-2 bg-light">
                                            <p class="small mb-1 fw-semibold">Layer 3</p>
                                            <p class="small mb-1 text-muted"><strong>Nama:</strong> <span id="intervensiOakLayer3Name" class="text-dark">—</span></p>
                                            <div id="intervensiOakLayer3Users" class="d-flex flex-wrap gap-1"></div>
                                            <div id="intervensiOakLayer3Empty" class="text-muted small d-none">Tidak ada user.</div>
                                            <div id="intervensiOakLayer3Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1"></span>Memuat...</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-2 bg-light">
                                            <p class="small mb-1 fw-semibold">Layer 4</p>
                                            <p class="small mb-1 text-muted"><strong>Nama:</strong> <span id="intervensiOakLayer4Name" class="text-dark">—</span></p>
                                            <div id="intervensiOakLayer4Users" class="d-flex flex-wrap gap-1"></div>
                                            <div id="intervensiOakLayer4Empty" class="text-muted small d-none">Tidak ada user.</div>
                                            <div id="intervensiOakLayer4Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1"></span>Memuat...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="intervensiOakLoading" class="text-center py-3 d-none"><div class="spinner-border text-warning spinner-border-sm" role="status"></div><p class="text-muted mb-0 mt-2 small">Memuat data OAK...</p></div>
                        <div id="intervensiOakEmpty" class="text-center py-3 d-none"><span class="material-icons-outlined text-muted" style="font-size: 32px;">inbox</span><p class="text-muted mt-2 mb-0 small">Tidak ada data OAK.</p></div>
                        <div id="intervensiOakTableWrap" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="intervensiTableOak">
                                    <thead class="table-light"><tr><th>Activity</th><th>Sub Activity</th><th>Submit Date</th><th>Submit By</th><th>SID Pelapor</th><th>Lokasi</th><th>Detail Lokasi</th><th>Conclusion</th><th>Site</th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
(function() {
    var dtOptions = {
        order: [[0, 'asc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Semua']],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ baris',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(filter dari _MAX_ data)',
            paginate: { first: 'Awal', last: 'Akhir', next: 'Selanjutnya', previous: 'Sebelumnya' },
            zeroRecords: 'Tidak ada data yang cocok'
        },
        dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
        drawCallback: function() { }
    };
    document.querySelectorAll('.alert-log-datatable').forEach(function(tbl) {
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable(tbl)) {
            $(tbl).DataTable(dtOptions);
        }
    });
})();

(function() {
    var ikkContextUrl = @json(route('dopmikk.api.ikk-context-alert-log'));
    var intervensiStoreUrl = @json(route('dopmikk.api.alert-log-intervensi'));
    var modalApiUrl = @json(route('dopmikk.api.ikk-modal-data'));
    var layer1UsersApiUrl = @json(route('dopmikk.api.layer1-users'));
    var layers234UsersApiUrl = @json(route('dopmikk.api.layers234-users'));
    var ipkFormLink = 'https://beikk.beraucoal.co.id/monitoring-ipk';
    var intervensiModalEl = document.getElementById('intervensiDopmModal');
    var intervensiModal = intervensiModalEl ? new bootstrap.Modal(intervensiModalEl) : null;

    function tr(cells) {
        var row = document.createElement('tr');
        cells.forEach(function(c) {
            var td = document.createElement('td');
            td.textContent = c == null || c === undefined ? '—' : String(c);
            row.appendChild(td);
        });
        return row;
    }
    function safeStr(val, maxLen) {
        if (val == null || val === undefined) return '—';
        var s = String(val).trim();
        if (!s) return '—';
        if (maxLen && s.length > maxLen) s = s.substring(0, maxLen);
        return s;
    }
    function formatTs(ts) {
        if (!ts) return '—';
        var s = String(ts).trim();
        if (!s) return '—';
        var m = s.match(/^(\d{4})-(\d{2})-(\d{2})[T\s](\d{2}):(\d{2})/);
        if (m) return m[3] + '/' + m[2] + '/' + m[1] + ' ' + m[4] + ':' + m[5];
        return s;
    }
    function normalizeWaNumber(selular) {
        if (!selular || typeof selular !== 'string') return '';
        var s = selular.replace(/\s+/g, '').replace(/-/g, '');
        if (/^0\d+/.test(s)) return '62' + s.substring(1);
        if (!/^62/.test(s) && /^\d+/.test(s)) return '62' + s;
        return s;
    }

    document.addEventListener('click', function(e) {
        var btnAlertLog = e.target.closest('.btn-intervensi-alert-log');
        if (btnAlertLog) {
            var kodeIkk = (btnAlertLog.getAttribute('data-kode-ikk') || '').trim();
            var tanggal = (btnAlertLog.getAttribute('data-tanggal') || '').trim();
            var alertLevel = parseInt(btnAlertLog.getAttribute('data-alert-level') || '1', 10) || 1;
            if (!kodeIkk) return;
            var csrfToken = (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || @json(csrf_token());
            var openModal = function() {
                var url = ikkContextUrl + '?kode_ikk=' + encodeURIComponent(kodeIkk) + '&tanggal_dop=' + encodeURIComponent(tanggal);
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res && res.success && res.dopm) {
                            var fake = document.createElement('button');
                            fake.className = 'btn-intervensi-dopm d-none';
                            fake.setAttribute('data-dopm', JSON.stringify(res.dopm));
                            document.body.appendChild(fake);
                            fake.click();
                            fake.remove();
                        } else {
                            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Gagal', text: (res && res.message) ? res.message : 'Data IKK tidak ditemukan.' });
                        }
                    })
                    .catch(function(err) {
                        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Gagal memuat', text: err.message || 'Gagal memuat data IKK.' });
                    });
            };
            // Simpan intervensi dulu (jika terintervensi di jam ke-2, alert jam ke-3 tidak akan muncul lagi)
            var formData = new FormData();
            formData.append('tanggal', tanggal);
            formData.append('kode_ikk', kodeIkk);
            formData.append('alert_level', alertLevel);
            if (csrfToken) formData.append('_token', csrfToken);
            fetch(intervensiStoreUrl, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken || '' },
                body: formData
            }).then(function() { openModal(); }).catch(function() { openModal(); });
            return;
        }

        var btnIntervensi = e.target.closest('.btn-intervensi-dopm');
        if (btnIntervensi && intervensiModal) {
            var data = JSON.parse(btnIntervensi.getAttribute('data-dopm') || '{}');
            var namaLayer1 = (data.nama_layer_1 || '').trim();
            var sidLayer1 = (data.sid_layer_1 || '').trim();
            var hasLayer1 = sidLayer1 !== '' || namaLayer1 !== '';
            document.getElementById('intervensiDopmTitle').textContent = (data.id_dop || 'Intervensi') + ' — ' + (data.nama_pekerjaan || 'DOPM').substring(0, 50);
            document.getElementById('intervensiDopmSubtitle').textContent = 'Kode IKK: ' + (data.kode_ikk || '—');
            document.getElementById('intervensiBadgeIpk').textContent = '0';
            document.getElementById('intervensiBadgeOkk').textContent = '0';
            document.getElementById('intervensiBadgeOak').textContent = '0';
            var raPjoName = (data.ra_pjo_name || '').trim();
            var pjoWrap = document.getElementById('intervensiPjoWrap');
            var pjoNameDisplay = document.getElementById('intervensiPjoNameDisplay');
            var pjoUsers = document.getElementById('intervensiPjoUsers');
            var pjoEmpty = document.getElementById('intervensiPjoEmpty');
            var pjoLoading = document.getElementById('intervensiPjoLoading');
            var hasKodeIkk = (data.kode_ikk || '').trim() !== '';
            if (pjoWrap) {
                if (raPjoName === '' && !hasKodeIkk) { pjoWrap.classList.add('d-none'); } else {
                    pjoWrap.classList.remove('d-none');
                    if (pjoNameDisplay) pjoNameDisplay.textContent = raPjoName || 'Memuat...';
                    if (pjoUsers) pjoUsers.innerHTML = '';
                    if (pjoEmpty) pjoEmpty.classList.add('d-none');
                    if (pjoLoading) pjoLoading.classList.remove('d-none');
                }
            }
            document.getElementById('intervensiIpkLoading').classList.remove('d-none');
            document.getElementById('intervensiIpkEmpty').classList.add('d-none');
            document.getElementById('intervensiIpkTableWrap').classList.add('d-none');
            document.getElementById('intervensiOkkLoading').classList.remove('d-none');
            document.getElementById('intervensiOkkEmpty').classList.add('d-none');
            document.getElementById('intervensiOkkTableWrap').classList.add('d-none');
            document.getElementById('intervensiOakLoading').classList.remove('d-none');
            document.getElementById('intervensiOakEmpty').classList.add('d-none');
            document.getElementById('intervensiOakTableWrap').classList.add('d-none');
            [2, 3, 4].forEach(function(n) {
                var usersEl = document.getElementById('intervensiOakLayer' + n + 'Users');
                var emptyEl = document.getElementById('intervensiOakLayer' + n + 'Empty');
                var loadingEl = document.getElementById('intervensiOakLayer' + n + 'Loading');
                var nameEl = document.getElementById('intervensiOakLayer' + n + 'Name');
                if (usersEl) usersEl.innerHTML = '';
                if (emptyEl) emptyEl.classList.add('d-none');
                if (loadingEl) loadingEl.classList.remove('d-none');
                if (nameEl) nameEl.textContent = '—';
            });
            var layer1Wrap = document.getElementById('intervensiLayer1Wrap');
            var layer1UsersEl = document.getElementById('intervensiLayer1Users');
            var layer1EmptyEl = document.getElementById('intervensiLayer1Empty');
            var layer1NoNameEl = document.getElementById('intervensiLayer1NoName');
            var layer1LoadingEl = document.getElementById('intervensiLayer1Loading');
            var okkLayer1Wrap = document.getElementById('intervensiOkkLayer1Wrap');
            var okkLayer1NameDisplay = document.getElementById('intervensiOkkLayer1NameDisplay');
            var okkLayer1UsersEl = document.getElementById('intervensiOkkLayer1Users');
            var okkLayer1EmptyEl = document.getElementById('intervensiOkkLayer1Empty');
            var okkLayer1NoNameEl = document.getElementById('intervensiOkkLayer1NoName');
            var okkLayer1LoadingEl = document.getElementById('intervensiOkkLayer1Loading');
            layer1Wrap.classList.remove('d-none');
            layer1UsersEl.innerHTML = '';
            document.getElementById('intervensiLayer1NameDisplay').textContent = namaLayer1 || '—';
            layer1EmptyEl.classList.add('d-none');
            layer1NoNameEl.classList.add('d-none');
            layer1LoadingEl.classList.add('d-none');
            if (okkLayer1Wrap) {
                okkLayer1Wrap.classList.remove('d-none');
                okkLayer1UsersEl.innerHTML = '';
                okkLayer1NameDisplay.textContent = namaLayer1 || '—';
                okkLayer1EmptyEl.classList.add('d-none');
                okkLayer1NoNameEl.classList.add('d-none');
                okkLayer1LoadingEl.classList.add('d-none');
            }
            if (!hasLayer1) {
                layer1NoNameEl.classList.remove('d-none');
                if (okkLayer1NoNameEl) okkLayer1NoNameEl.classList.remove('d-none');
            } else {
                layer1LoadingEl.classList.remove('d-none');
                if (okkLayer1LoadingEl) okkLayer1LoadingEl.classList.remove('d-none');
            }
            intervensiModal.show();

            var params = new URLSearchParams({
                kode_ikk: data.kode_ikk || '',
                jenis_ijin_kerja_khusus: data.jenis_ijin_kerja_khusus || '',
                sid_layer_2: data.sid_layer_2 || '',
                sid_layer_3: data.sid_layer_3 || '',
                sid_layer_4: data.sid_layer_4 || '',
                nama_layer_2: data.nama_layer_2 || '',
                nama_layer_3: data.nama_layer_3 || '',
                nama_layer_4: data.nama_layer_4 || '',
                tanggal_dop: data.tanggal_dop || '',
                location_name: data.location_name || '',
                location_detail_name: data.location_detail_name || ''
            });

            function doIntervensiFetch() {
                fetch(modalApiUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(function(res) {
                        if (!res || !res.success) throw new Error('Request failed');
                        var ipkAll = res.ipk_ikk || [];
                        var okkAll = res.okk || [];
                        var oak = res.oak || [];
                        function isLayer1Row(row) {
                            var raw = (row.employee_type !== undefined && row.employee_type !== null && row.employee_type !== '') ? row.employee_type : (row.layer_pengawas || '');
                            var lv = raw.toString().trim().toLowerCase();
                            return lv === 'layer 1' || lv === 'layer1' || lv === '1';
                        }
                        var ipk = ipkAll.filter(function(r) { if (r.employee_type === undefined && r.layer_pengawas === undefined) return true; return isLayer1Row(r); });
                        var okk = okkAll.filter(function(r) { if (r.employee_type === undefined && r.layer_pengawas === undefined) return true; return isLayer1Row(r); });
                        document.getElementById('intervensiBadgeIpk').textContent = ipk.length;
                        document.getElementById('intervensiBadgeOkk').textContent = okk.length;
                        document.getElementById('intervensiBadgeOak').textContent = oak.length;
                        document.getElementById('intervensiIpkLoading').classList.add('d-none');
                        document.getElementById('intervensiOkkLoading').classList.add('d-none');
                        document.getElementById('intervensiOakLoading').classList.add('d-none');
                        if (ipk.length === 0) { document.getElementById('intervensiIpkEmpty').classList.remove('d-none'); document.getElementById('intervensiIpkTableWrap').classList.add('d-none'); } else {
                            document.getElementById('intervensiIpkEmpty').classList.add('d-none');
                            document.getElementById('intervensiIpkTableWrap').classList.remove('d-none');
                            var tbody = document.querySelector('#intervensiTableIpk tbody');
                            if (tbody) { tbody.innerHTML = ''; ipk.forEach(function(r) { tbody.appendChild(tr([formatTs(r.ts), safeStr(r.nama_pengawas), safeStr(r.kode_sid), safeStr(r.kode_ikk), safeStr(r.nama_perusahaan, 40), safeStr(r.site), safeStr(r.durasi_jam), safeStr(r.cctv_terekam), safeStr(r.kategori_ijk, 35), safeStr(r.status_pekerjaan)])); }); }
                        }
                        if (okk.length === 0) { document.getElementById('intervensiOkkEmpty').classList.remove('d-none'); document.getElementById('intervensiOkkTableWrap').classList.add('d-none'); } else {
                            document.getElementById('intervensiOkkEmpty').classList.add('d-none');
                            document.getElementById('intervensiOkkTableWrap').classList.remove('d-none');
                            var tbody = document.querySelector('#intervensiTableOkk tbody');
                            if (tbody) { tbody.innerHTML = ''; okk.forEach(function(r) { tbody.appendChild(tr([formatTs(r.ts), safeStr(r.nama_pengawas), safeStr(r.kode_sid), safeStr(r.kode_ikk), safeStr(r.nama_perusahaan, 40), safeStr(r.site), safeStr(r.jenis_ijk, 35), safeStr(r.layer_pengawas)])); }); }
                        }
                        if (oak.length === 0) { document.getElementById('intervensiOakEmpty').classList.remove('d-none'); document.getElementById('intervensiOakTableWrap').classList.add('d-none'); } else {
                            document.getElementById('intervensiOakEmpty').classList.add('d-none');
                            document.getElementById('intervensiOakTableWrap').classList.remove('d-none');
                            var tbody = document.querySelector('#intervensiTableOak tbody');
                            if (tbody) { tbody.innerHTML = ''; oak.forEach(function(r) { tbody.appendChild(tr([safeStr(r.activity), safeStr(r.sub_activity), safeStr(r.submit_date), safeStr(r.submit_by), safeStr(r.kode_sid_pelapor), safeStr(r.location), safeStr(r.detail_location), safeStr(r.conclusion, 50), safeStr(r.site)])); }); }
                        }
                    })
                    .catch(function(err) {
                        document.getElementById('intervensiIpkLoading').classList.add('d-none');
                        document.getElementById('intervensiOkkLoading').classList.add('d-none');
                        document.getElementById('intervensiOakLoading').classList.add('d-none');
                        document.getElementById('intervensiIpkEmpty').classList.remove('d-none');
                        document.getElementById('intervensiOkkEmpty').classList.remove('d-none');
                        document.getElementById('intervensiOakEmpty').classList.remove('d-none');
                        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Gagal memuat', text: err.message || 'Gagal memuat data.' });
                    });
            }
            function doLayer1Fetch() {
                if (!hasLayer1) return;
                var layer1UsersEl2 = document.getElementById('intervensiLayer1Users');
                var layer1EmptyEl2 = document.getElementById('intervensiLayer1Empty');
                var layer1LoadingEl2 = document.getElementById('intervensiLayer1Loading');
                var okkLayer1UsersEl2 = document.getElementById('intervensiOkkLayer1Users');
                var okkLayer1EmptyEl2 = document.getElementById('intervensiOkkLayer1Empty');
                var okkLayer1LoadingEl2 = document.getElementById('intervensiOkkLayer1Loading');
                layer1LoadingEl2.classList.remove('d-none');
                layer1UsersEl2.innerHTML = '';
                layer1EmptyEl2.classList.add('d-none');
                if (okkLayer1LoadingEl2) { okkLayer1LoadingEl2.classList.remove('d-none'); okkLayer1UsersEl2.innerHTML = ''; okkLayer1EmptyEl2.classList.add('d-none'); }
                var qs = new URLSearchParams();
                if (sidLayer1) qs.set('sid_layer_1', sidLayer1);
                if (namaLayer1) qs.set('nama_layer_1', namaLayer1);
                fetch(layer1UsersApiUrl + '?' + qs.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        layer1LoadingEl2.classList.add('d-none');
                        if (okkLayer1LoadingEl2) okkLayer1LoadingEl2.classList.add('d-none');
                        var usersRaw = (res && res.success && res.users) ? res.users : [];
                        var seen = {}; var users = [];
                        usersRaw.forEach(function(u) {
                            var key = u.id ? ('id_' + u.id) : ('wa_' + normalizeWaNumber(u.selular) + '_n_' + (u.nama || '').trim() + '_u_' + (u.username || '').trim());
                            if (key && !seen[key]) { seen[key] = true; users.push(u); }
                        });
                        var displayName = (res && res.nama_layer_1) ? res.nama_layer_1 : namaLayer1;
                        document.getElementById('intervensiLayer1NameDisplay').textContent = displayName || '—';
                        if (document.getElementById('intervensiOkkLayer1NameDisplay')) document.getElementById('intervensiOkkLayer1NameDisplay').textContent = displayName || '—';
                        var ipkMsg = (displayName || 'PIC') + ', anda harus mengisi INSPEKSI PRA KERJA (IPK) untuk pekerjaan berikut:\n\nIKK: ' + (data.kode_ikk || '—') + (data.nama_pekerjaan ? ' - ' + data.nama_pekerjaan : '') + '\nHari: ' + (data.tanggal_dop || '—') + '\nLokasi: ' + (data.location_name || '—') + '\nDetail Lokasi: ' + (data.location_detail_name || '—') + '\nLayer 1: ' + (data.nama_layer_1 || '—') + '\nLayer 2: ' + (data.nama_layer_2 || '—') + '\nLayer 3: ' + (data.nama_layer_3 || '—') + '\nLayer 4: ' + (data.nama_layer_4 || '—') + '\n\n' + ipkFormLink;
                        var okkMsg = (displayName || 'PIC') + ', mohon perhatian untuk OBSERVASI KEGIATAN KERJA (OKK).\n\nIKK: ' + (data.kode_ikk || '—') + (data.nama_pekerjaan ? ' - ' + data.nama_pekerjaan : '') + '\nHari: ' + (data.tanggal_dop || '—') + '\nLokasi: ' + (data.location_name || '—') + '\nDetail Lokasi: ' + (data.location_detail_name || '—') + '\nLayer 1-4: ' + (data.nama_layer_1 || '—') + ', ' + (data.nama_layer_2 || '—') + ', ' + (data.nama_layer_3 || '—') + ', ' + (data.nama_layer_4 || '—');
                        if (users.length === 0) { layer1EmptyEl2.classList.remove('d-none'); if (okkLayer1EmptyEl2) okkLayer1EmptyEl2.classList.remove('d-none'); return; }
                        users.forEach(function(u) {
                            var num = normalizeWaNumber(u.selular); var label = u.nama || u.username || 'User';
                            if (!num) return;
                            var btn = document.createElement('button'); btn.type = 'button'; btn.className = 'btn btn-sm btn-success';
                            btn.innerHTML = '<i class="material-icons-outlined me-1" style="font-size:16px;">send</i> WA (IPK) → ' + label;
                            btn.addEventListener('click', function() { window.open('https://wa.me/' + num + '?text=' + encodeURIComponent(ipkMsg), '_blank'); });
                            layer1UsersEl2.appendChild(btn);
                        });
                        users.forEach(function(u) {
                            var num = normalizeWaNumber(u.selular); var label = u.nama || u.username || 'User';
                            if (!num) return;
                            var btn = document.createElement('button'); btn.type = 'button'; btn.className = 'btn btn-sm btn-success';
                            btn.innerHTML = '<i class="material-icons-outlined me-1" style="font-size:16px;">send</i> WA (OKK) → ' + label;
                            btn.addEventListener('click', function() { window.open('https://wa.me/' + num + '?text=' + encodeURIComponent(okkMsg), '_blank'); });
                            okkLayer1UsersEl2.appendChild(btn);
                        });
                    })
                    .catch(function() {
                        layer1LoadingEl2.classList.add('d-none'); layer1EmptyEl2.classList.remove('d-none');
                        if (okkLayer1LoadingEl2) { okkLayer1LoadingEl2.classList.add('d-none'); if (okkLayer1EmptyEl2) okkLayer1EmptyEl2.classList.remove('d-none'); }
                    });
            }
            function doOakLayers234Fetch() {
                var qs = new URLSearchParams();
                qs.set('sid_layer_2', data.sid_layer_2 || ''); qs.set('sid_layer_3', data.sid_layer_3 || ''); qs.set('sid_layer_4', data.sid_layer_4 || '');
                qs.set('nama_layer_2', data.nama_layer_2 || ''); qs.set('nama_layer_3', data.nama_layer_3 || ''); qs.set('nama_layer_4', data.nama_layer_4 || '');
                fetch(layers234UsersApiUrl + '?' + qs.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        [2, 3, 4].forEach(function(n) {
                            var key = 'layer_' + n;
                            var usersEl = document.getElementById('intervensiOakLayer' + n + 'Users');
                            var emptyEl = document.getElementById('intervensiOakLayer' + n + 'Empty');
                            var loadingEl = document.getElementById('intervensiOakLayer' + n + 'Loading');
                            var nameEl = document.getElementById('intervensiOakLayer' + n + 'Name');
                            if (loadingEl) loadingEl.classList.add('d-none');
                            var layerData = res && res[key] ? res[key] : { users: [], nama_layer: '' };
                            var usersRaw = layerData.users || [];
                            var seen = {}; var users = [];
                            usersRaw.forEach(function(u) {
                                var k = u.id ? ('id_' + u.id) : ('wa_' + normalizeWaNumber(u.selular) + '_n_' + (u.nama || '').trim() + '_u_' + (u.username || '').trim());
                                if (k && !seen[k]) { seen[k] = true; users.push(u); }
                            });
                            var displayName = layerData.nama_layer || '—';
                            if (nameEl) nameEl.textContent = displayName;
                            if (!usersEl) return;
                            usersEl.innerHTML = '';
                            var oakMsg = (displayName !== '—' ? displayName : 'PIC') + ', mohon perhatian untuk OAK (Observasi Aktivitas Kerja) sesuai IKK ini.';
                            users.forEach(function(u) {
                                var num = normalizeWaNumber(u.selular); if (!num) return;
                                var label = u.nama || u.username || 'User';
                                var a = document.createElement('a');
                                a.href = 'https://wa.me/' + num + '?text=' + encodeURIComponent(oakMsg); a.target = '_blank'; a.rel = 'noopener';
                                a.className = 'btn btn-sm btn-warning text-dark';
                                a.innerHTML = '<i class="material-icons-outlined me-1" style="font-size:14px;">send</i> Intervensi by WA'; a.title = label;
                                usersEl.appendChild(a);
                            });
                            if (users.length === 0 && emptyEl) emptyEl.classList.remove('d-none');
                        });
                    })
                    .catch(function() {
                        [2, 3, 4].forEach(function(n) {
                            var loadingEl = document.getElementById('intervensiOakLayer' + n + 'Loading');
                            var emptyEl = document.getElementById('intervensiOakLayer' + n + 'Empty');
                            if (loadingEl) loadingEl.classList.add('d-none');
                            if (emptyEl) emptyEl.classList.remove('d-none');
                        });
                    });
            }
            function doPjoFetch(pjoName) {
                var pjoUsersEl = document.getElementById('intervensiPjoUsers');
                var pjoEmptyEl = document.getElementById('intervensiPjoEmpty');
                var pjoLoadingEl = document.getElementById('intervensiPjoLoading');
                if (!pjoUsersEl || !pjoLoadingEl) return;
                pjoUsersEl.innerHTML = ''; if (pjoEmptyEl) pjoEmptyEl.classList.add('d-none');
                pjoLoadingEl.classList.remove('d-none');
                var qs = new URLSearchParams({ nama_layer_1: pjoName });
                fetch(layer1UsersApiUrl + '?' + qs.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        pjoLoadingEl.classList.add('d-none');
                        var users = (res && res.success && res.users) ? res.users : [];
                        pjoUsersEl.innerHTML = '';
                        var intervensiMsg = 'Assalamu\'alaikum. Intervensi DOPM/IKK: mohon perhatian untuk kelengkapan IPK-IKK, OKK, dan OAK. Terima kasih.';
                        users.forEach(function(u) {
                            var num = normalizeWaNumber(u.selular || ''); if (!num) return;
                            var nama = (u.nama || u.username || 'PJO').trim();
                            var a = document.createElement('a');
                            a.href = 'https://wa.me/' + num + '?text=' + encodeURIComponent(intervensiMsg); a.target = '_blank'; a.rel = 'noopener';
                            a.className = 'btn btn-sm btn-outline-success';
                            a.innerHTML = '<i class="material-icons-outlined me-1" style="font-size:16px;">send</i> WA ke ' + (nama.length > 25 ? nama.substring(0, 22) + '...' : nama);
                            pjoUsersEl.appendChild(a);
                        });
                        if (pjoEmptyEl) { if (users.length === 0) pjoEmptyEl.classList.remove('d-none'); else pjoEmptyEl.classList.add('d-none'); }
                    })
                    .catch(function() { if (pjoLoadingEl) pjoLoadingEl.classList.add('d-none'); if (pjoEmptyEl) pjoEmptyEl.classList.remove('d-none'); });
            }
            function doPjoFetchFromApi(data) {
                var pjoNameDisplay = document.getElementById('intervensiPjoNameDisplay');
                var pjoUsersEl = document.getElementById('intervensiPjoUsers');
                var pjoEmptyEl = document.getElementById('intervensiPjoEmpty');
                var pjoLoadingEl = document.getElementById('intervensiPjoLoading');
                if (!pjoLoadingEl) return;
                var apiParams = new URLSearchParams({ kode_ikk: data.kode_ikk || '', jenis_ijin_kerja_khusus: data.jenis_ijin_kerja_khusus || '', location_name: data.location_name || '', location_detail_name: data.location_detail_name || '', tanggal_dop: data.tanggal_dop || '' });
                fetch(modalApiUrl + '?' + apiParams.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.ok ? r.json() : Promise.reject(new Error('HTTP ' + r.status)); })
                    .then(function(res) {
                        var name = (res && res.ra_pjo_name) ? String(res.ra_pjo_name).trim() : '';
                        if (pjoNameDisplay) pjoNameDisplay.textContent = name || '—';
                        pjoLoadingEl.classList.add('d-none');
                        if (name !== '') doPjoFetch(name);
                        else { if (pjoUsersEl) pjoUsersEl.innerHTML = ''; if (pjoEmptyEl) { pjoEmptyEl.textContent = 'Tidak ada data PJO untuk IKK ini.'; pjoEmptyEl.classList.remove('d-none'); } }
                    })
                    .catch(function() { pjoLoadingEl.classList.add('d-none'); if (pjoNameDisplay) pjoNameDisplay.textContent = '—'; if (pjoUsersEl) pjoUsersEl.innerHTML = ''; if (pjoEmptyEl) { pjoEmptyEl.textContent = 'Gagal memuat data PJO.'; pjoEmptyEl.classList.remove('d-none'); } });
            }

            doIntervensiFetch();
            doLayer1Fetch();
            doOakLayers234Fetch();
            if (raPjoName !== '') doPjoFetch(raPjoName);
            else if (hasKodeIkk) doPjoFetchFromApi(data);
            return;
        }
    });
})();
</script>
@endsection
