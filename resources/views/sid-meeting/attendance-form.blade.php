<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Form Absensi Event</title>
</head>
<body class="min-h-screen bg-[#f0ebf8] py-10 px-4 text-slate-800">
    <div class="mx-auto max-w-3xl space-y-4">
        <div class="rounded-2xl border border-[#d9cffa] bg-white shadow-sm overflow-hidden">
            <div class="h-3 w-full bg-[#673ab7]"></div>
            <div class="px-6 py-6 md:px-8">
                <h1 class="text-2xl font-semibold text-slate-900">Form Absensi Meeting</h1>
                <p class="mt-2 text-sm text-slate-600">
                    Silakan isi data berikut untuk melakukan absensi event.
                </p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white px-6 py-5 md:px-8">
            <h2 class="text-base font-semibold text-slate-900">Informasi Event</h2>
            <div class="mt-3 grid gap-2 text-sm text-slate-700">
                <p><span class="font-semibold">Kode Event:</span> {{ $event->event_code }}</p>
                <p><span class="font-semibold">Site:</span> {{ $event->site->name }}</p>
                <p><span class="font-semibold">Jenis Meeting:</span> {{ $event->meetingType->name }}</p>
                <p><span class="font-semibold">Tanggal:</span> {{ optional($event->meeting_date)->format('Y-m-d') }}</p>
                <p><span class="font-semibold">Waktu:</span> {{ substr((string) $event->start_time, 0, 5) }} - {{ substr((string) $event->end_time, 0, 5) }} WITA</p>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-medium text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <form method="post" class="rounded-2xl border border-slate-200 bg-white px-6 py-6 md:px-8">
            @csrf
            <div class="space-y-2">
                <label for="kode_sid" class="block text-sm font-semibold text-slate-900">
                    Kode SID <span class="text-red-500">*</span>
                </label>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-stretch">
                    <input
                        id="kode_sid"
                        name="kode_sid"
                        value="{{ old('kode_sid') }}"
                        class="w-full flex-1 rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#673ab7] focus:ring-4 focus:ring-[#ede7f6]"
                        placeholder="Contoh: 532TM"
                        autocomplete="off"
                        required
                    >
                    <button
                        type="button"
                        id="btn_cek_sid"
                        class="shrink-0 rounded-xl border border-[#673ab7] bg-[#ede7f6] px-4 py-3 text-sm font-semibold text-[#4527a0] hover:bg-[#e1d5f4]"
                    >
                        Cek SID
                    </button>
                </div>
                <label class="mt-1 inline-flex items-center gap-2 text-xs font-medium text-slate-700">
                    <input
                        id="no_sid_toggle"
                        name="no_sid"
                        type="checkbox"
                        value="1"
                        @checked(old('no_sid'))
                        class="h-4 w-4 rounded border-slate-300 text-[#673ab7] focus:ring-[#673ab7]"
                    >
                    Saya tidak mempunyai SID
                </label>
                <p id="lookup_status" class="mt-1 min-h-[1.25rem] text-xs font-medium"></p>
                <!-- <p class="text-xs text-slate-500">Data diisi otomatis dari <strong>ClickHouse Nitip</strong> (view <code class="rounded bg-slate-100 px-1">bep_vw_wp_karyawan</code>) berdasarkan <code class="rounded bg-slate-100 px-1">kode_sid</code>.</p> -->
            </div>

            <div id="manual_panel" class="mt-5 hidden space-y-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Input manual (SID tidak ditemukan)</p>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="manual_nama" class="mb-1 block text-xs font-semibold text-slate-700">Nama <span class="text-red-500">*</span></label>
                        <input id="manual_nama" name="manual_nama" value="{{ old('manual_nama') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#673ab7] focus:ring-4 focus:ring-[#ede7f6]" placeholder="Nama lengkap">
                    </div>
                    <div>
                        <label for="manual_perusahaan" class="mb-1 block text-xs font-semibold text-slate-700">Perusahaan <span class="text-red-500">*</span></label>
                        <input id="manual_perusahaan" name="manual_perusahaan" value="{{ old('manual_perusahaan') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#673ab7] focus:ring-4 focus:ring-[#ede7f6]" placeholder="Nama perusahaan">
                    </div>
                    <div>
                        <label for="manual_jabatan" class="mb-1 block text-xs font-semibold text-slate-700">Jabatan <span class="text-red-500">*</span></label>
                        <input id="manual_jabatan" name="manual_jabatan" value="{{ old('manual_jabatan') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#673ab7] focus:ring-4 focus:ring-[#ede7f6]" placeholder="Jabatan">
                    </div>
                    <div>
                        <label for="manual_divisi" class="mb-1 block text-xs font-semibold text-slate-700">Divisi</label>
                        <input id="manual_divisi" name="manual_divisi" value="{{ old('manual_divisi') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#673ab7] focus:ring-4 focus:ring-[#ede7f6]" placeholder="Divisi">
                    </div>
                    <div>
                        <label for="manual_departemen" class="mb-1 block text-xs font-semibold text-slate-700">Departemen</label>
                        <input id="manual_departemen" name="manual_departemen" value="{{ old('manual_departemen') }}" class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none focus:border-[#673ab7] focus:ring-4 focus:ring-[#ede7f6]" placeholder="Departemen">
                    </div>
                </div>
                <p class="text-xs text-amber-800">Jika SID tidak ada di sistem, isi data manual lalu klik Kirim Absensi.</p>
            </div>

            <div id="preview_panel" class="mt-5 hidden space-y-3 rounded-xl border border-[#d1c4e9] bg-[#faf8ff] px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#5e35b1]">Data terdeteksi</p>
                <div id="pv_foto_wrap" class="hidden sm:col-span-2">
                    <p class="text-xs text-slate-500 mb-1">Foto</p>
                    <img id="pv_foto" src="" alt="" class="h-24 w-24 rounded-lg border border-slate-200 object-cover bg-white">
                </div>
                <dl class="grid gap-2 text-sm sm:grid-cols-2">
                    <div class="sm:col-span-2"><dt class="text-slate-500">Nama</dt><dd id="pv_nama" class="font-medium text-slate-900">—</dd></div>
                    <div><dt class="text-slate-500">NIK</dt><dd id="pv_nik" class="text-slate-800">—</dd></div>
                    <div><dt class="text-slate-500">Perusahaan</dt><dd id="pv_perusahaan" class="text-slate-800">—</dd></div>
                    <div><dt class="text-slate-500">Jabatan fungsional</dt><dd id="pv_jf" class="text-slate-800">—</dd></div>
                    <div><dt class="text-slate-500">Jabatan struktural</dt><dd id="pv_js" class="text-slate-800">—</dd></div>
                    <div><dt class="text-slate-500">Divisi</dt><dd id="pv_divisi" class="text-slate-800">—</dd></div>
                    <div><dt class="text-slate-500">Departemen</dt><dd id="pv_dept" class="text-slate-800">—</dd></div>
                    <div><dt class="text-slate-500">Usia</dt><dd id="pv_usia" class="text-slate-800">—</dd></div>
                    <div><dt class="text-slate-500">Kategori karyawan</dt><dd id="pv_katkar" class="text-slate-800">—</dd></div>
                    <div><dt class="text-slate-500">Kategori</dt><dd id="pv_kategori" class="text-slate-800">—</dd></div>
                    <div><dt class="text-slate-500">Work permit</dt><dd id="pv_wp" class="text-slate-800">—</dd></div>
                    <div><dt class="text-slate-500">Level jabatan</dt><dd id="pv_lvl" class="text-slate-800">—</dd></div>
                    <div><dt class="text-slate-500">Status karyawan</dt><dd id="pv_status" class="text-slate-800">—</dd></div>
                </dl>
            </div>

            <div class="mt-6 flex items-center justify-between gap-3">
                <p class="text-xs text-slate-500">Form ini tidak memerlukan login akun.</p>
                <button class="rounded-xl bg-[#673ab7] px-6 py-3 text-sm font-semibold text-white hover:bg-[#5e35b1]">
                    Kirim Absensi
                </button>
            </div>
        </form>
    </div>
    <script>
        (function () {
            // URL relatif agar fetch selalu ke origin yang sama dengan halaman (hindari mismatch APP_URL).
            var lookupPath = @json(route('sid-meeting.attendance.lookup', ['qrToken' => $event->qr_token], false));
            var kodeInput = document.getElementById('kode_sid');
            var btn = document.getElementById('btn_cek_sid');
            var noSidToggle = document.getElementById('no_sid_toggle');
            var statusEl = document.getElementById('lookup_status');
            var panel = document.getElementById('preview_panel');
            var manualPanel = document.getElementById('manual_panel');
            var manualRequiredIds = ['manual_nama', 'manual_perusahaan', 'manual_jabatan'];

            function setStatus(msg, cls) {
                if (!statusEl) return;
                statusEl.textContent = msg || '';
                statusEl.className = 'mt-1 min-h-[1.25rem] text-xs font-medium ' + (cls || '');
            }

            function setDd(id, text) {
                var el = document.getElementById(id);
                if (el) el.textContent = text || '—';
            }

            function fillPreview(d) {
                var fotoWrap = document.getElementById('pv_foto_wrap');
                var fotoEl = document.getElementById('pv_foto');
                if (fotoWrap && fotoEl) {
                    if (d.foto) {
                        fotoEl.src = d.foto;
                        fotoEl.alt = d.nama || 'Foto';
                        fotoWrap.classList.remove('hidden');
                    } else {
                        fotoWrap.classList.add('hidden');
                        fotoEl.removeAttribute('src');
                    }
                }
                setDd('pv_nama', d.nama);
                setDd('pv_nik', d.nik);
                setDd('pv_perusahaan', d.nama_perusahaan);
                setDd('pv_jf', d.jabatan_fungsional);
                setDd('pv_js', d.jabatan_struktural);
                setDd('pv_divisi', d.divisi);
                setDd('pv_dept', d.departement);
                setDd('pv_usia', d.usia);
                setDd('pv_katkar', d.kategori_karyawan);
                setDd('pv_kategori', d.kategori);
                setDd('pv_wp', d.work_permit);
                setDd('pv_lvl', d.level_jabatan);
                setDd('pv_status', d.status_karyawan);
                if (panel) panel.classList.remove('hidden');
            }

            function setManualPanel(visible) {
                if (!manualPanel) return;
                manualPanel.classList.toggle('hidden', !visible);
                manualRequiredIds.forEach(function (id) {
                    var el = document.getElementById(id);
                    if (!el) return;
                    el.required = !!visible;
                });
            }

            function setNoSidMode(enabled) {
                if (!kodeInput || !btn) return;
                kodeInput.required = !enabled;
                kodeInput.readOnly = !!enabled;
                if (enabled) {
                    kodeInput.value = '';
                    btn.disabled = true;
                    btn.classList.add('opacity-60', 'cursor-not-allowed');
                    if (panel) panel.classList.add('hidden');
                    setManualPanel(true);
                    setStatus('Mode manual aktif. Silakan isi data tanpa SID.', 'text-amber-700');
                    return;
                }

                btn.disabled = false;
                btn.classList.remove('opacity-60', 'cursor-not-allowed');
                setManualPanel(false);
                setStatus('', '');
            }

            function runLookup() {
                if (!kodeInput || !btn) return;
                var sid = (kodeInput.value || '').trim();
                if (!sid) {
                    setStatus('Isi Kode SID terlebih dahulu.', 'text-amber-700');
                    if (panel) panel.classList.add('hidden');
                    return;
                }
                setStatus('Memeriksa…', 'text-slate-600');
                btn.disabled = true;
                var sep = lookupPath.indexOf('?') >= 0 ? '&' : '?';
                var url = lookupPath + sep + 'kode_sid=' + encodeURIComponent(sid);
                fetch(url, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function (r) {
                        return r.text().then(function (text) {
                            var body = null;
                            if (text) {
                                try {
                                    body = JSON.parse(text);
                                } catch (e) {
                                    body = { _parseError: true, _raw: text.slice(0, 200) };
                                }
                            }
                            return { ok: r.ok, status: r.status, body: body };
                        });
                    })
                    .then(function (res) {
                        btn.disabled = false;
                        if (res.body && res.body._parseError) {
                            if (panel) panel.classList.add('hidden');
                            setStatus('Server mengembalikan non-JSON (HTTP ' + res.status + '). Periksa URL / log server.', 'text-red-700');
                            return;
                        }
                        if (res.body && res.body.ok && res.body.data) {
                            if (res.body.data.kode_sid) {
                                kodeInput.value = res.body.data.kode_sid;
                            }
                            fillPreview(res.body.data);
                            setManualPanel(false);
                            setStatus('Data ditemukan. Lanjutkan dengan Kirim Absensi.', 'text-emerald-700');
                            return;
                        }
                        if (panel) panel.classList.add('hidden');
                        var msg = (res.body && res.body.message) ? res.body.message : 'Gagal memuat data.';
                        if (res.status === 419) {
                            msg = 'Sesi halaman kedaluwarsa. Muat ulang halaman lalu coba lagi.';
                        }
                        setStatus(msg, res.status === 503 ? 'text-amber-800' : 'text-red-700');
                    })
                    .catch(function (err) {
                        btn.disabled = false;
                        if (panel) panel.classList.add('hidden');
                        var detail = (err && err.message) ? err.message : '';
                        setStatus('Tidak terhubung ke server. ' + (detail ? '(' + detail + ')' : 'Periksa jaringan atau muat ulang halaman.'), 'text-red-700');
                    });
            }

            if (btn) btn.addEventListener('click', runLookup);
            if (kodeInput) kodeInput.addEventListener('blur', function () {
                if (noSidToggle && noSidToggle.checked) return;
                if ((kodeInput.value || '').trim().length >= 3) {
                    runLookup();
                }
            });

            if (noSidToggle) {
                noSidToggle.addEventListener('change', function () {
                    setNoSidMode(!!noSidToggle.checked);
                });
            }

            @if(old('no_sid'))
                setManualPanel(true);
                setNoSidMode(true);
            @elseif(old('manual_nama') || old('manual_perusahaan') || old('manual_jabatan') || old('manual_divisi') || old('manual_departemen'))
                setManualPanel(true);
            @endif
        })();
    </script>
</body>
</html>
