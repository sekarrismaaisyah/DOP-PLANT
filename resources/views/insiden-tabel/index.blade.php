@extends('layouts.master')

@section('title', 'Insiden Tabel')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<style>
/* Tabel ringkas (tingkat 1) — konsep sama dengan planning */
#insidenSummaryTable { border-collapse: separate; border-spacing: 0; }
#insidenSummaryTable thead tr { background: #f8fafc !important; }
#insidenSummaryTable thead th {
    font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.02em;
    color: #475569; border-bottom: 2px solid #e2e8f0; padding: 0.75rem 1rem;
}
.insiden-summary-row {
    transition: background-color 0.15s ease, box-shadow 0.15s ease;
}
.insiden-summary-row:hover { background-color: #f8fafc !important; }
.insiden-summary-row td {
    padding: 0.875rem 1rem; vertical-align: middle !important; border-bottom: 1px solid #f1f3f5;
}
.insiden-summary-row .badge { font-weight: 500; padding: 0.35em 0.65em; }
.expand-btn-insiden { font-weight: 500; transition: all 0.2s ease; border-width: 1.5px; }
.expand-btn-insiden:hover { transform: translateY(-1px); }

/* Panel detail (tingkat 2) - child row */
.detail-inner-insiden {
    margin: 0 0.5rem 0.5rem; padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0; border-radius: 0.5rem;
    border-left: 4px solid #0d6efd;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}
.detail-inner-insiden .table {
    border-radius: 0.5rem; overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,.05);
}
.detail-inner-insiden .table thead th {
    font-size: 0.8125rem; font-weight: 600; color: #475569;
    background: #fff !important; border-bottom: 2px solid #e2e8f0;
    padding: 0.6rem 0.75rem;
}
.detail-inner-insiden .table tbody tr { transition: background-color 0.15s ease; }
.detail-inner-insiden .table tbody tr:hover { background-color: #f8fafc !important; }
.detail-inner-insiden .table td { padding: 0.5rem 0.75rem; vertical-align: middle !important; font-size: 0.875rem; border-color: #f1f5f9 !important; }

/* Input & saving indicator (konsep planning) */
.insiden-tag-input { min-width: 120px; }
.saving-indicator-insiden { display: none; margin-left: 4px; }
.saving-indicator-insiden.show { display: inline-block; }

/* DataTables overrides */
#insidenSummaryTable_wrapper .dataTables_length select { padding: 0.25rem 1.5rem; }
#insidenSummaryTable_wrapper .dataTables_filter input { margin-left: 0.5rem; padding: 0.25rem 0.5rem; }
</style>
@endsection

@section('content')
    <x-page-title title="Insiden Tabel" pagetitle="Manajemen Insiden" />

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
                <div class="alert alert-warning alert-dismissible fade show rounded-4" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Filter & Upload Section -->
    <!-- <div class="row mb-4">
        <div class="col-12">
            <div class="card rounded-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-8">
                            <label class="form-label">Cari (No Kecelakaan, Site, Kategori)</label>
                            <p class="text-muted small mb-0">Gunakan kotak pencarian di bawah tabel atau filter panjang halaman.</p>
                        </div>
                        <div class="col-lg-4">
                            <div class="card border h-100 rounded-3">
                                <div class="card-body">
                                    <label class="form-label fw-semibold">Upload Excel</label>
                                    <small class="text-muted d-block mb-2">.xlsx / .xls / .csv</small>
                                    <form method="POST" action="{{ route('insiden-tabel.import') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-2">
                                            <input type="file" name="excel_file" id="excel_file" class="form-control form-control-sm" required>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-sm w-100 rounded-3">
                                            <i class="bx bx-upload"></i> Upload & Proses
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Tabel Data Insiden (DataTables server-side + child row detail) -->
    <div class="row">
        <div class="col-12">
            <div class="card rounded-4 border-0 shadow-sm mt-2">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-0 fw-bold">
                            Data Insiden
                            <span class="text-muted fw-normal fs-6" id="insidenTableInfo">(0-0 dari 0)</span>
                        </h5>
                        <small class="text-muted">Ringkas per No Kecelakaan. Klik tombol [+] untuk melihat detail entri.</small>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnExpandAllInsiden" title="Expand All">
                            <i class="material-icons-outlined" style="font-size: 16px;">unfold_more</i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnCollapseAllInsiden" title="Collapse All">
                            <i class="material-icons-outlined" style="font-size: 16px;">unfold_less</i>
                        </button>
                        <a href="{{ route('insiden-tabel.template') }}" class="btn btn-outline-success btn-sm rounded-3">
                            <i class="bx bx-download"></i> Template Excel
                        </a>
                        <form method="POST" action="{{ route('insiden-tabel.import') }}" enctype="multipart/form-data" class="d-inline" id="insidenUploadForm">
                            @csrf
                            <input type="file" name="excel_file" id="insidenExcelFile" class="d-none" accept=".xlsx,.xls,.csv">
                            <button type="button" class="btn btn-success btn-sm rounded-3" id="btnUploadInsiden">
                                <i class="bx bx-upload"></i> Upload Excel
                            </button>
                        </form>
                        <a href="{{ route('insiden-tabel.create') }}" class="btn btn-primary rounded-3">Tambah Data</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive px-3 pt-3">
                        <table class="table table-hover align-middle mb-0 w-100" id="insidenSummaryTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;"></th>
                                    <th>No</th>
                                    <th>No Kecelakaan</th>
                                    <th>Site</th>
                                    <th>Kategori</th>
                                    <th>Status LPI</th>
                                    <th class="text-center">Total Entri</th>
                                    <th>Tag</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
(function() {
    var table;
    var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    function buildDetailHtml(noKecelakaan, detail) {
        if (!detail || detail.length === 0) {
            return '<div class="detail-inner-insiden"><p class="text-muted mb-0">Tidak ada detail.</p></div>';
        }
        var rows = detail.map(function(d) {
            return '<tr>' +
                '<td>' + (d.row_num || '') + '</td>' +
                '<td>' + escapeHtml(d.kategori) + '</td>' +
                '<td>' + escapeHtml(d.site) + '</td>' +
                '<td>' + escapeHtml(d.layer) + '</td>' +
                '<td>' + escapeHtml(d.jenis_item_ipls) + '</td>' +
                '<td>' + escapeHtml(d.detail_layer) + '</td>' +
                '<td>' + escapeHtml(d.klasifikasi_layer) + '</td>' +
                '<td>' + escapeHtml(d.keterangan_layer) + '</td>' +
                '<td>' + escapeHtml(d.status_lpi) + '</td>' +
                '<td>' + escapeHtml(d.tanggal) + '</td>' +
                '<td><div class="d-flex gap-1">' +
                '<a href="' + escapeHtml(d.edit_url) + '" class="btn btn-sm btn-outline-primary rounded-3" title="Edit"><i class="material-icons-outlined" style="font-size: 16px;">edit</i></a>' +
                '<form method="POST" action="' + escapeHtml(d.destroy_url) + '" class="d-inline" onsubmit="return confirm(\'Yakin ingin menghapus data ini?\');">' +
                '<input type="hidden" name="_token" value="' + escapeHtml(d.csrf || csrfToken) + '">' +
                '<input type="hidden" name="_method" value="DELETE">' +
                '<button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Hapus"><i class="material-icons-outlined" style="font-size: 16px;">delete</i></button>' +
                '</form></div></td></tr>';
        }).join('');
        return '<div class="detail-inner-insiden">' +
            '<div class="d-flex align-items-center mb-2"><small class="text-muted fw-semibold"><i class="bx bx-list-ul me-1"></i> Detail entri — No Kecelakaan: ' + escapeHtml(noKecelakaan) + '</small></div>' +
            '<div class="table-responsive">' +
            '<table class="table table-sm table-bordered table-hover align-middle mb-0 bg-white">' +
            '<thead class="table-secondary"><tr>' +
            '<th style="width: 40px">#</th><th>Kategori</th><th>Site</th><th>Layer</th><th>Jenis Item IPLS</th><th>Detail Layer</th>' +
            '<th>Klasifikasi Layer</th><th>Keterangan Layer</th><th>Status LPI</th><th>Tanggal</th><th style="width: 120px">Aksi</th></tr></thead>' +
            '<tbody>' + rows + '</tbody></table></div>' +
            '<button type="button" class="btn btn-sm btn-outline-secondary mt-2 btn-collapse-child" title="Tutup"><i class="material-icons-outlined" style="font-size: 14px; vertical-align: -0.2em;">unfold_less</i> Tutup</button>' +
            '</div>';
    }

    function escapeHtml(str) {
        if (str == null) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function updateTableInfo() {
        if (!table) return;
        var info = table.page.info();
        var start = info.recordsDisplay === 0 ? 0 : info.start + 1;
        var end = Math.min(info.start + info.length, info.recordsDisplay);
        document.getElementById('insidenTableInfo').textContent = '(' + start + '-' + end + ' dari ' + info.recordsDisplay + ')';
    }

    function saveGroupMeta(noKecelakaan, payload, indicatorId) {
        var $indicator = indicatorId ? $('#' + indicatorId) : null;
        if ($indicator && $indicator.length) $indicator.addClass('show');
        var body = { no_kecelakaan: noKecelakaan };
        if (payload.tags !== undefined) body.tags = payload.tags;
        fetch('{{ route("insiden-tabel.update-group-meta") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(body)
        })
        .then(function(r) { return r.json(); })
        .then(function() {
            if ($indicator && $indicator.length) $indicator.removeClass('show');
        })
        .catch(function() {
            if ($indicator && $indicator.length) $indicator.removeClass('show');
            alert('Gagal menyimpan. Silakan coba lagi.');
        });
    }

    table = $('#insidenSummaryTable').DataTable({
        serverSide: true,
        ajax: {
            url: '{{ route("insiden-tabel.data") }}',
            type: 'GET'
        },
        order: [[2, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return '<button type="button" class="btn btn-sm btn-outline-primary expand-btn-insiden p-0 btn-expand-child" style="width: 28px; height: 28px; line-height: 1;" data-no-kecelakaan="' + escapeHtml(row.no_kecelakaan) + '"><i class="material-icons-outlined" style="font-size: 18px;">add</i></button>';
                }
            },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-end' },
            { data: 'no_kecelakaan', name: 'no_kecelakaan' },
            { data: 'site', name: 'site' },
            {
                data: 'kategori',
                name: 'kategori',
                render: function(data) {
                    return '<span class="badge bg-secondary">' + escapeHtml(data || '-') + '</span>';
                }
            },
            {
                data: 'status_lpi',
                name: 'status_lpi',
                render: function(data) {
                    return '<span class="badge bg-info">' + escapeHtml(data || '-') + '</span>';
                }
            },
            { data: 'total_entri', name: 'total_entri', className: 'text-center', orderable: true, searchable: false },
            {
                data: 'tags',
                name: 'tag',
                orderable: true,
                searchable: true,
                render: function(data, type, row) {
                    var tags = Array.isArray(row.tags) ? row.tags : [];
                    var value = tags.join(', ');
                    var rowId = row.DT_RowIndex != null ? row.DT_RowIndex : '';
                    return '<div class="d-flex align-items-center gap-1">' +
                        '<input type="text" class="form-control form-control-sm insiden-tag-input" ' +
                        'data-no-kecelakaan="' + escapeHtml(row.no_kecelakaan) + '" value="' + escapeHtml(value) + '" data-row-id="' + escapeHtml(String(rowId)) + '" placeholder="Tag1, Tag2, ...">' +
                        '<span class="saving-indicator-insiden" id="saving-tag-row-' + escapeHtml(String(rowId)) + '"><i class="bx bx-loader-alt bx-spin text-primary"></i></span>' +
                        '</div>';
                }
            }
        ],
        drawCallback: function() {
            updateTableInfo();
            $('#insidenSummaryTable tbody tr').addClass('insiden-summary-row');
            $('#insidenSummaryTable').off('blur.insidenMeta').on('blur.insidenMeta', '.insiden-tag-input', function() {
                var $el = $(this);
                var noKec = $el.data('no-kecelakaan');
                var rowId = $el.data('row-id');
                var tagStr = $el.val() || '';
                var tags = tagStr.split(',').map(function(s) { return s.trim(); }).filter(Boolean);
                saveGroupMeta(noKec, { tags: tags }, 'saving-tag-row-' + rowId);
            });
            $('#insidenSummaryTable tbody tr .btn-expand-child').off('click').on('click', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var rowData = row.data();
                if (!rowData) return;
                if (row.child.isShown()) {
                    row.child.hide();
                    tr.find('.btn-expand-child i').text('add');
                } else {
                    var detailHtml = buildDetailHtml(rowData.no_kecelakaan, rowData.detail);
                    row.child(detailHtml).show();
                    tr.find('.btn-expand-child i').text('remove');
                    row.child().find('.btn-collapse-child').off('click').on('click', function() {
                        row.child.hide();
                        tr.find('.btn-expand-child i').text('add');
                    });
                }
            });
        },
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ baris',
            info: '_START_–_END_ dari _TOTAL_',
            infoEmpty: '0–0 dari 0',
            infoFiltered: '(filter dari _MAX_)',
            paginate: { first: 'Awal', last: 'Akhir', next: 'Selanjutnya', previous: 'Sebelumnya' },
            processing: 'Memuat...',
            zeroRecords: 'Tidak ada data'
        },
        dom: '<"row px-3 pt-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"d-flex justify-content-between align-items-center flex-wrap gap-3 px-3 py-3 border-top bg-white"ip>'
    });

    $('#btnExpandAllInsiden').on('click', function() {
        table.rows().every(function() {
            var row = this;
            var d = row.data();
            if (d && !row.child.isShown()) {
                row.child(buildDetailHtml(d.no_kecelakaan, d.detail)).show();
                $(row.node()).find('.btn-expand-child i').text('remove');
            }
        });
    });

    $('#btnCollapseAllInsiden').on('click', function() {
        table.rows().every(function() {
            this.child.hide();
            $(this.node()).find('.btn-expand-child i').text('add');
        });
    });

    // Upload Excel (trigger file input)
    $('#btnUploadInsiden').on('click', function() {
        $('#insidenExcelFile').trigger('click');
    });
    $('#insidenExcelFile').on('change', function() {
        if (this.files && this.files.length > 0) {
            $('#insidenUploadForm').trigger('submit');
        }
    });

})();
</script>
@endsection
