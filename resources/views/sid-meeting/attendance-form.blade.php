<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <style>
        :root {
            --brand-1: #1f7a5f;
            --brand-2: #2a9d74;
            --ink-soft: #5f6f69;
        }
        .app-shell {
            border: 1px solid rgba(31, 122, 95, 0.12);
            background: linear-gradient(180deg, #f3f8f4 0%, #edf4ef 100%);
            box-shadow: 0 20px 50px rgba(16, 34, 28, 0.12);
        }
        .glass-card {
            border: 1px solid rgba(31, 122, 95, 0.14);
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            backdrop-filter: blur(4px);
        }
        .hero-band {
            background: linear-gradient(135deg, #d9efe3 0%, #cce7da 45%, #b6dfcd 100%);
        }
        .input-modern {
            border: 1px solid #d7e4dd;
            border-radius: 12px;
            background: #fafdfb;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .input-modern:focus {
            outline: none;
            border-color: #2a9d74;
            box-shadow: 0 0 0 4px rgba(42, 157, 116, 0.16);
        }
        .btn-primary {
            border-radius: 12px;
            background: linear-gradient(90deg, var(--brand-1), var(--brand-2));
            color: #fff;
            font-weight: 600;
            box-shadow: 0 10px 22px rgba(79, 70, 229, 0.28);
        }
        .btn-primary:hover { filter: brightness(0.97); }
        .face-scan-line {
            animation: face-scan-move 2.4s linear infinite;
        }
        .face-box-canvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        @keyframes face-scan-move {
            0% { transform: translateY(-120%); opacity: 0; }
            20% { opacity: 0.9; }
            50% { transform: translateY(0%); opacity: 0.9; }
            80% { opacity: 0.9; }
            100% { transform: translateY(120%); opacity: 0; }
        }
    </style>
    <title>Form Absensi Event</title>
</head>
<body class="min-h-screen bg-[#e8efe9] py-6 px-3 text-slate-800">
    <div class="mx-auto max-w-xl space-y-4 rounded-[28px] p-3 app-shell">
        <div class="overflow-hidden rounded-3xl glass-card">
            <div class="hero-band px-5 py-5 md:px-6">
                <p class="text-xs font-semibold uppercase tracking-widest text-[#2f5f4d]">Absensi Digital</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-[#1f3b31]">Form Absensi Meeting</h1>
                <p class="mt-1 text-sm text-[#3f5f53]">
                    Silakan isi data berikut untuk melakukan absensi event.
                </p>
            </div>
        </div>

        <div class="rounded-3xl glass-card px-5 py-4 md:px-6">
            <h2 class="text-base font-semibold text-slate-900">Informasi Event</h2>
            <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                <p class="rounded-xl bg-[#f3f8f4] px-3 py-2"><span class="font-semibold">Kode Event:</span> {{ $event->event_code }}</p>
                <p class="rounded-xl bg-[#f3f8f4] px-3 py-2"><span class="font-semibold">Site:</span> {{ $event->site->name }}</p>
                <p class="rounded-xl bg-[#f3f8f4] px-3 py-2"><span class="font-semibold">Jenis Meeting:</span> {{ $event->meetingType->name }}</p>
                <p class="rounded-xl bg-[#f3f8f4] px-3 py-2"><span class="font-semibold">Tanggal:</span> {{ optional($event->meeting_date)->format('Y-m-d') }}</p>
                <p class="rounded-xl bg-[#f3f8f4] px-3 py-2 sm:col-span-2"><span class="font-semibold">Waktu:</span> {{ substr((string) $event->start_time, 0, 5) }} - {{ substr((string) $event->end_time, 0, 5) }} WITA</p>
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

        <form method="post" class="rounded-3xl glass-card px-5 py-5 md:px-6">
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
                        class="input-modern w-full flex-1 px-4 py-3 text-sm"
                        placeholder="Contoh: 532TM"
                        autocomplete="off"
                        required
                    >
                    <button
                        type="button"
                        id="btn_cek_sid"
                        class="shrink-0 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 hover:bg-emerald-100"
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

            <div id="manual_panel" class="mt-5 hidden space-y-3 rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Input manual (SID tidak ditemukan)</p>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="manual_nama" class="mb-1 block text-xs font-semibold text-slate-700">Nama <span class="text-red-500">*</span></label>
                        <input id="manual_nama" name="manual_nama" value="{{ old('manual_nama') }}" class="input-modern w-full px-4 py-3 text-sm" placeholder="Nama lengkap">
                    </div>
                    <div>
                        <label for="manual_perusahaan" class="mb-1 block text-xs font-semibold text-slate-700">Perusahaan <span class="text-red-500">*</span></label>
                        <input id="manual_perusahaan" name="manual_perusahaan" value="{{ old('manual_perusahaan') }}" class="input-modern w-full px-4 py-3 text-sm" placeholder="Nama perusahaan">
                    </div>
                    <div>
                        <label for="manual_jabatan" class="mb-1 block text-xs font-semibold text-slate-700">Jabatan <span class="text-red-500">*</span></label>
                        <input id="manual_jabatan" name="manual_jabatan" value="{{ old('manual_jabatan') }}" class="input-modern w-full px-4 py-3 text-sm" placeholder="Jabatan">
                    </div>
                    <div>
                        <label for="manual_divisi" class="mb-1 block text-xs font-semibold text-slate-700">Divisi</label>
                        <input id="manual_divisi" name="manual_divisi" value="{{ old('manual_divisi') }}" class="input-modern w-full px-4 py-3 text-sm" placeholder="Divisi">
                    </div>
                    <div>
                        <label for="manual_departemen" class="mb-1 block text-xs font-semibold text-slate-700">Departemen</label>
                        <input id="manual_departemen" name="manual_departemen" value="{{ old('manual_departemen') }}" class="input-modern w-full px-4 py-3 text-sm" placeholder="Departemen">
                    </div>
                </div>
                <p class="text-xs text-amber-800">Jika SID tidak ada di sistem, isi data manual lalu klik Kirim Absensi.</p>
            </div>

            <div id="preview_panel" class="mt-5 hidden space-y-3 rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50/80 to-teal-50/80 px-4 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Data terdeteksi</p>
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

            <input type="hidden" name="face_verified" id="face_verified" value="0">
            <input type="hidden" name="face_distance" id="face_distance" value="">
            <input type="hidden" name="face_bypass" id="face_bypass" value="0">

            <div class="mt-6 flex items-center justify-between gap-3 border-t border-slate-200 pt-5">
                <p class="text-xs text-slate-500">Form ini tidak memerlukan login akun.</p>
                <button id="btn_submit_attendance" class="btn-primary px-6 py-3 text-sm">
                    Kirim Absensi
                </button>
            </div>
        </form>
    </div>

    <div id="face_modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 px-4 backdrop-blur-sm">
        <div class="w-full max-w-4xl rounded-3xl border border-slate-200 bg-white p-5 shadow-2xl">
            <div class="mb-4 flex items-start justify-between gap-3 border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Verifikasi Wajah</h3>
                    <p class="text-sm text-slate-600">Pastikan wajah berada di area frame sebelum lanjut kirim absensi.</p>
                </div>
                <button id="face_modal_close" type="button" class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-100">Tutup</button>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <p class="mb-1 text-xs text-slate-500">Foto Referensi</p>
                    <img id="face_ref_img" src="" alt="" class="h-64 w-full rounded-2xl border border-slate-200 object-contain bg-slate-100 shadow-sm">
                </div>
                <div>
                    <p class="mb-1 text-xs text-slate-500">Live Camera</p>
                    <div id="face_camera_wrap" class="relative h-64 overflow-hidden rounded-2xl border border-slate-300 bg-slate-950 shadow-xl">
                        <video id="face_live_video" class="h-full w-full object-cover" autoplay playsinline muted></video>
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-cyan-500/10 via-transparent to-purple-500/10"></div>
                        <div id="face_scan_line" class="face-scan-line pointer-events-none absolute inset-x-4 top-1/2 h-[2px] bg-gradient-to-r from-transparent via-cyan-300 to-transparent opacity-80"></div>
                        <div class="absolute left-3 top-3 rounded-full border border-cyan-300/50 bg-cyan-500/20 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-cyan-100">
                            Face Scan
                        </div>
                        <div id="face_identity_live" class="absolute left-3 right-3 top-12 hidden rounded-lg border border-emerald-300/70 bg-emerald-500/20 px-2 py-1 text-xs font-semibold text-emerald-50 backdrop-blur-sm"></div>
                        <canvas id="face_box_canvas" class="face-box-canvas"></canvas>
                        <svg class="pointer-events-none absolute inset-0 h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                            <rect x="10" y="12" width="80" height="76" rx="12" ry="12" fill="none" stroke="rgba(255,255,255,0.10)" stroke-width="0.6"/>
                        </svg>
                        <div id="face_fail_badge" class="absolute inset-x-3 bottom-3 hidden rounded-lg border border-red-300 bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">
                            Wajah tidak cocok. Coba posisikan wajah lebih dekat dan terang.
                        </div>
                    </div>
                </div>
            </div>

            <div id="face_identity_ok" class="mt-4 hidden rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"></div>
            <p id="face_verify_status" class="mt-3 min-h-[1.25rem] rounded-lg bg-slate-100 px-3 py-2 text-xs font-medium text-slate-700"></p>
            <canvas id="face_capture_canvas" width="360" height="360" class="hidden"></canvas>

            <div class="mt-4 flex flex-wrap justify-end gap-2">
                <button id="btn_start_camera" type="button" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">Aktifkan Kamera</button>
                <button id="btn_verify_face" type="button" class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">Verifikasi & Kirim</button>
                <button id="btn_confirm_submit" type="button" class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700">Kirim Absensi Sekarang</button>
                <button id="btn_bypass_submit" type="button" class="w-full rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900 hover:bg-amber-100 sm:w-auto">Kirim absensi tanpa verifikasi wajah</button>

            </div>
            <div class="mt-5 border-t border-slate-200 pt-4">
                <p class="mb-2 text-xs text-slate-500">Jika kamera atau AI tidak bisa mendeteksi wajah, Anda tetap dapat mengirim absensi lewat opsi di bawah (tanpa verifikasi wajah).</p>
                <!-- <button id="btn_bypass_submit" type="button" class="w-full rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900 hover:bg-amber-100 sm:w-auto">Kirim absensi tanpa verifikasi wajah</button> -->
            </div>
        </div>
    </div>
    <script>
        (function () {
            // URL relatif agar fetch selalu ke origin yang sama dengan halaman (hindari mismatch APP_URL).
            var lookupPath = @json(route('sid-meeting.attendance.lookup', ['qrToken' => $event->qr_token], false));
            var photoProxyPath = @json(route('sid-meeting.attendance.photo-proxy', ['qrToken' => $event->qr_token], false));
            var kodeInput = document.getElementById('kode_sid');
            var btn = document.getElementById('btn_cek_sid');
            var noSidToggle = document.getElementById('no_sid_toggle');
            var statusEl = document.getElementById('lookup_status');
            var panel = document.getElementById('preview_panel');
            var manualPanel = document.getElementById('manual_panel');
            var manualRequiredIds = ['manual_nama', 'manual_perusahaan', 'manual_jabatan'];
            var faceStatusEl = document.getElementById('face_verify_status');
            var faceRefImg = document.getElementById('face_ref_img');
            var faceVideo = document.getElementById('face_live_video');
            var faceCanvas = document.getElementById('face_capture_canvas');
            var faceBoxCanvas = document.getElementById('face_box_canvas');
            var faceVerifiedInput = document.getElementById('face_verified');
            var faceDistanceInput = document.getElementById('face_distance');
            var faceBypassInput = document.getElementById('face_bypass');
            var faceModal = document.getElementById('face_modal');
            var faceModalClose = document.getElementById('face_modal_close');
            var faceIdentityOk = document.getElementById('face_identity_ok');
            var faceIdentityLive = document.getElementById('face_identity_live');
            var faceScanLine = document.getElementById('face_scan_line');
            var faceFailBadge = document.getElementById('face_fail_badge');
            var faceCameraWrap = document.getElementById('face_camera_wrap');
            var btnStartCamera = document.getElementById('btn_start_camera');
            var btnVerifyFace = document.getElementById('btn_verify_face');
            var btnConfirmSubmit = document.getElementById('btn_confirm_submit');
            var btnBypassSubmit = document.getElementById('btn_bypass_submit');
            var faceModelsReady = false;
            var faceModelLoadPromise = null;
            var cameraStream = null;
            var latestPhotoUrl = null;
            var latestPhotoProxyUrl = null;
            var faceThreshold = 0.50;
            var tinyDetectorOptions = null;
            var allowDirectSubmit = false;
            /** Hanya true setelah "Verifikasi & Kirim" benar-benar match (boleh dipercaya untuk kirim form). */
            var lastFaceMatchOk = false;
            var formEl = document.querySelector('form[method="post"]');
            var faceBoxLoopHandle = null;

            function makeProxyPhotoUrl(rawUrl) {
                if (!rawUrl) return null;
                var sep = photoProxyPath.indexOf('?') >= 0 ? '&' : '?';
                return photoProxyPath + sep + 'foto=' + encodeURIComponent(rawUrl);
            }

            function waitForImageReady(imgEl) {
                return new Promise(function (resolve, reject) {
                    if (!imgEl) {
                        reject(new Error('Elemen gambar referensi tidak tersedia.'));
                        return;
                    }
                    if (imgEl.complete && imgEl.naturalWidth > 0) {
                        resolve(true);
                        return;
                    }
                    var onLoad = function () {
                        cleanup();
                        resolve(true);
                    };
                    var onError = function () {
                        cleanup();
                        reject(new Error('Gagal memuat foto referensi.'));
                    };
                    var cleanup = function () {
                        imgEl.removeEventListener('load', onLoad);
                        imgEl.removeEventListener('error', onError);
                    };
                    imgEl.addEventListener('load', onLoad);
                    imgEl.addEventListener('error', onError);
                });
            }

            function setFaceStatus(msg, cls) {
                if (!faceStatusEl) return;
                faceStatusEl.textContent = msg || '';
                faceStatusEl.className = 'min-h-[1.25rem] text-xs font-medium ' + (cls || 'text-slate-700');
            }

            function setFaceVerified(ok, distance) {
                if (faceVerifiedInput) faceVerifiedInput.value = ok ? '1' : '0';
                if (faceDistanceInput) faceDistanceInput.value = (typeof distance === 'number' && isFinite(distance)) ? distance.toFixed(4) : '';
            }

            function resetFaceVerification() {
                lastFaceMatchOk = false;
                if (faceBypassInput) faceBypassInput.value = '0';
                setFaceVerified(false);
                setFaceStatus('', 'text-slate-700');
                if (faceIdentityOk) {
                    faceIdentityOk.classList.add('hidden');
                    faceIdentityOk.textContent = '';
                }
                if (faceIdentityLive) {
                    faceIdentityLive.classList.add('hidden');
                    faceIdentityLive.textContent = '';
                }
                if (faceFailBadge) faceFailBadge.classList.add('hidden');
                if (faceCameraWrap) {
                    faceCameraWrap.classList.remove('border-red-400', 'ring-2', 'ring-red-300', 'border-emerald-300', 'ring-emerald-300/40');
                    faceCameraWrap.classList.add('border-slate-300');
                }
                if (faceScanLine) faceScanLine.classList.remove('hidden');
                clearFaceBoxOverlay();
            }

            function clearFaceBoxOverlay() {
                if (!faceBoxCanvas) return;
                var ctx = faceBoxCanvas.getContext('2d');
                if (!ctx) return;
                ctx.clearRect(0, 0, faceBoxCanvas.width, faceBoxCanvas.height);
            }

            function syncFaceBoxCanvasSize() {
                if (!faceBoxCanvas || !faceVideo) return;
                var box = faceVideo.getBoundingClientRect();
                var w = Math.max(1, Math.round(box.width));
                var h = Math.max(1, Math.round(box.height));
                if (faceBoxCanvas.width !== w || faceBoxCanvas.height !== h) {
                    faceBoxCanvas.width = w;
                    faceBoxCanvas.height = h;
                }
            }

            function drawFaceBox(detection, verified) {
                if (!faceBoxCanvas || !faceVideo) return;
                syncFaceBoxCanvasSize();
                var ctx = faceBoxCanvas.getContext('2d');
                if (!ctx) return;
                ctx.clearRect(0, 0, faceBoxCanvas.width, faceBoxCanvas.height);
                if (!detection || !detection.box) return;

                var box = detection.box;
                var scaleX = faceBoxCanvas.width / (faceVideo.videoWidth || faceBoxCanvas.width);
                var scaleY = faceBoxCanvas.height / (faceVideo.videoHeight || faceBoxCanvas.height);
                var x = box.x * scaleX;
                var y = box.y * scaleY;
                var w = box.width * scaleX;
                var h = box.height * scaleY;
                var color = verified ? '#34d399' : '#22c55e';

                ctx.strokeStyle = color;
                ctx.lineWidth = 3;
                ctx.shadowColor = color;
                ctx.shadowBlur = 10;
                ctx.strokeRect(x, y, w, h);
                ctx.shadowBlur = 0;

                ctx.fillStyle = 'rgba(34, 197, 94, 0.15)';
                ctx.fillRect(x, y, w, h);
            }

            async function ensureFaceModelsLoaded() {
                if (faceModelsReady) return true;
                if (faceModelLoadPromise) return faceModelLoadPromise;
                if (!window.faceapi) {
                    throw new Error('Library face-api.js belum termuat.');
                }
                if (!tinyDetectorOptions) {
                    tinyDetectorOptions = new faceapi.TinyFaceDetectorOptions({
                        inputSize: 416,
                        scoreThreshold: 0.3
                    });
                }

                var modelPath = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@latest/model';
                faceModelLoadPromise = Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(modelPath),
                    faceapi.nets.faceLandmark68Net.loadFromUri(modelPath),
                    faceapi.nets.faceRecognitionNet.loadFromUri(modelPath)
                ]).then(function () {
                    faceModelsReady = true;
                    return true;
                });

                return faceModelLoadPromise;
            }

            async function startCamera() {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    throw new Error('Browser tidak mendukung akses kamera.');
                }
                if (cameraStream) return;
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    },
                    audio: false
                });
                if (faceVideo) {
                    faceVideo.srcObject = cameraStream;
                    await faceVideo.play();
                    startFaceBoxLoop();
                }
            }

            function openFaceModal() {
                if (!faceModal) return;
                faceModal.classList.remove('hidden');
                faceModal.classList.add('flex');
            }

            function closeFaceModal() {
                if (!faceModal) return;
                faceModal.classList.add('hidden');
                faceModal.classList.remove('flex');
                stopFaceBoxLoop();
                clearFaceBoxOverlay();
            }

            async function detectFaceBoxFromElement(el) {
                if (!window.faceapi) return null;
                if (!tinyDetectorOptions) {
                    tinyDetectorOptions = new faceapi.TinyFaceDetectorOptions({
                        inputSize: 416,
                        scoreThreshold: 0.3
                    });
                }
                return await faceapi.detectSingleFace(el, tinyDetectorOptions);
            }

            function stopFaceBoxLoop() {
                if (faceBoxLoopHandle) {
                    window.clearInterval(faceBoxLoopHandle);
                    faceBoxLoopHandle = null;
                }
            }

            function startFaceBoxLoop() {
                stopFaceBoxLoop();
                if (!faceVideo || !faceBoxCanvas) return;
                faceBoxLoopHandle = window.setInterval(function () {
                    if (!faceModal || faceModal.classList.contains('hidden')) return;
                    if (!faceVideo.videoWidth || !faceVideo.videoHeight) return;
                    detectFaceBoxFromElement(faceVideo)
                        .then(function (det) {
                            drawFaceBox(det, false);
                        })
                        .catch(function () {
                            clearFaceBoxOverlay();
                        });
                }, 250);
            }

            async function detectDescriptorFromImage(el) {
                if (!window.faceapi) {
                    throw new Error('Library face-api.js belum termuat.');
                }
                if (!tinyDetectorOptions) {
                    tinyDetectorOptions = new faceapi.TinyFaceDetectorOptions({
                        inputSize: 416,
                        scoreThreshold: 0.3
                    });
                }
                var result = await faceapi
                    .detectSingleFace(el, tinyDetectorOptions)
                    .withFaceLandmarks()
                    .withFaceDescriptor();
                return result ? result.descriptor : null;
            }

            async function detectDescriptorWithFallback(imgEl) {
                var direct = await detectDescriptorFromImage(imgEl);
                if (direct) return direct;

                // Fallback: upscale foto referensi agar wajah kecil lebih mudah terdeteksi.
                var fallbackCanvas = document.createElement('canvas');
                var targetW = 640;
                var ratio = imgEl.naturalWidth > 0 ? (imgEl.naturalHeight / imgEl.naturalWidth) : 1;
                var targetH = Math.max(360, Math.round(targetW * ratio));
                fallbackCanvas.width = targetW;
                fallbackCanvas.height = targetH;
                var fallbackCtx = fallbackCanvas.getContext('2d');
                fallbackCtx.drawImage(imgEl, 0, 0, targetW, targetH);
                return await detectDescriptorFromImage(fallbackCanvas);
            }

            async function verifyCurrentFace() {
                resetFaceVerification();
                if (!latestPhotoProxyUrl) {
                    setFaceStatus('Foto referensi belum tersedia.', 'text-amber-700');
                    return;
                }

                setFaceStatus('Memuat model AI...', 'text-slate-600');
                await ensureFaceModelsLoaded();
                await startCamera();

                if (!faceRefImg || !faceVideo || !faceCanvas) {
                    throw new Error('Komponen verifikasi wajah tidak lengkap.');
                }
                await waitForImageReady(faceRefImg);

                setFaceStatus('Menganalisis wajah...', 'text-slate-600');

                var refDescriptor = await detectDescriptorWithFallback(faceRefImg);
                if (!refDescriptor) {
                    setFaceStatus('Wajah pada foto referensi tidak terdeteksi. Coba SID lain atau update foto profil.', 'text-red-700');
                    return;
                }

                var ctx = faceCanvas.getContext('2d');
                var size = 360;
                ctx.drawImage(faceVideo, 0, 0, size, size);

                var liveDescriptor = await detectDescriptorFromImage(faceCanvas);
                if (!liveDescriptor) {
                    setFaceStatus('Wajah pada kamera tidak terdeteksi. Pastikan wajah jelas dan terang.', 'text-amber-700');
                    return;
                }

                var distance = faceapi.euclideanDistance(refDescriptor, liveDescriptor);
                var matched = distance <= faceThreshold;
                setFaceVerified(matched, distance);

                if (matched) {
                    lastFaceMatchOk = true;
                    if (faceBypassInput) faceBypassInput.value = '0';
                    setFaceStatus('Verifikasi berhasil (jarak: ' + distance.toFixed(4) + ').', 'text-emerald-700');
                    if (faceIdentityOk) {
                        var nama = document.getElementById('pv_nama');
                        var nik = document.getElementById('pv_nik');
                        var perusahaan = document.getElementById('pv_perusahaan');
                        faceIdentityOk.innerHTML = 'Data diri terverifikasi: <strong>' + (nama ? nama.textContent : '-') + '</strong> | NIK: <strong>' + (nik ? nik.textContent : '-') + '</strong> | Perusahaan: <strong>' + (perusahaan ? perusahaan.textContent : '-') + '</strong>';
                        faceIdentityOk.classList.remove('hidden');
                        if (faceIdentityLive) {
                            faceIdentityLive.textContent = 'Terverifikasi: ' + (nama ? nama.textContent : '-');
                            faceIdentityLive.classList.remove('hidden');
                        }
                    }
                    if (faceFailBadge) faceFailBadge.classList.add('hidden');
                    if (faceCameraWrap) {
                        faceCameraWrap.classList.remove('border-red-400', 'ring-2', 'ring-red-300', 'border-slate-300');
                        faceCameraWrap.classList.add('border-emerald-300', 'ring-2', 'ring-emerald-300/40');
                    }
                    if (faceScanLine) faceScanLine.classList.add('hidden');
                    detectFaceBoxFromElement(faceVideo).then(function (det) { drawFaceBox(det, true); }).catch(function () {});
                } else {
                    lastFaceMatchOk = false;
                    setFaceStatus('Verifikasi gagal (jarak: ' + distance.toFixed(4) + '). Coba ulangi.', 'text-red-700');
                    if (faceFailBadge) faceFailBadge.classList.remove('hidden');
                    if (faceIdentityLive) {
                        faceIdentityLive.classList.add('hidden');
                        faceIdentityLive.textContent = '';
                    }
                    if (faceCameraWrap) {
                        faceCameraWrap.classList.remove('border-slate-300', 'border-emerald-300', 'ring-emerald-300/40');
                        faceCameraWrap.classList.add('border-red-400', 'ring-2', 'ring-red-300');
                    }
                    if (faceScanLine) faceScanLine.classList.remove('hidden');
                    detectFaceBoxFromElement(faceVideo).then(function (det) { drawFaceBox(det, false); }).catch(function () {});
                }
            }

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
                        latestPhotoUrl = d.foto;
                        latestPhotoProxyUrl = makeProxyPhotoUrl(d.foto);
                        if (faceRefImg) {
                            faceRefImg.src = latestPhotoProxyUrl || '';
                            faceRefImg.alt = d.nama || 'Foto referensi';
                        }
                        resetFaceVerification();
                    } else {
                        fotoWrap.classList.add('hidden');
                        fotoEl.removeAttribute('src');
                        latestPhotoUrl = null;
                        latestPhotoProxyUrl = null;
                        resetFaceVerification();
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
                    latestPhotoUrl = null;
                    latestPhotoProxyUrl = null;
                    resetFaceVerification();
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
                            latestPhotoUrl = null;
                            latestPhotoProxyUrl = null;
                            resetFaceVerification();
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
                        latestPhotoUrl = null;
                        latestPhotoProxyUrl = null;
                        resetFaceVerification();
                        var msg = (res.body && res.body.message) ? res.body.message : 'Gagal memuat data.';
                        if (res.status === 419) {
                            msg = 'Sesi halaman kedaluwarsa. Muat ulang halaman lalu coba lagi.';
                        }
                        setStatus(msg, res.status === 503 ? 'text-amber-800' : 'text-red-700');
                    })
                    .catch(function (err) {
                        btn.disabled = false;
                        if (panel) panel.classList.add('hidden');
                        latestPhotoUrl = null;
                        latestPhotoProxyUrl = null;
                        resetFaceVerification();
                        var detail = (err && err.message) ? err.message : '';
                        setStatus('Tidak terhubung ke server. ' + (detail ? '(' + detail + ')' : 'Periksa jaringan atau muat ulang halaman.'), 'text-red-700');
                    });
            }

            if (btn) btn.addEventListener('click', runLookup);
            if (btnStartCamera) {
                btnStartCamera.addEventListener('click', function () {
                    startCamera()
                        .then(function () {
                            setFaceStatus('Kamera aktif. Lanjutkan verifikasi wajah.', 'text-slate-700');
                        })
                        .catch(function (e) {
                            setFaceStatus((e && e.message) ? e.message : 'Tidak bisa mengaktifkan kamera.', 'text-red-700');
                        });
                });
            }
            if (btnVerifyFace) {
                btnVerifyFace.addEventListener('click', function () {
                    verifyCurrentFace().catch(function (e) {
                        lastFaceMatchOk = false;
                        setFaceStatus((e && e.message) ? e.message : 'Terjadi kesalahan verifikasi wajah.', 'text-red-700');
                        setFaceVerified(false);
                        if (faceFailBadge) faceFailBadge.classList.remove('hidden');
                        if (faceIdentityLive) {
                            faceIdentityLive.classList.add('hidden');
                            faceIdentityLive.textContent = '';
                        }
                        if (faceCameraWrap) {
                            faceCameraWrap.classList.remove('border-slate-300', 'border-emerald-300', 'ring-emerald-300/40');
                            faceCameraWrap.classList.add('border-red-400', 'ring-2', 'ring-red-300');
                        }
                    });
                });
            }
            if (btnConfirmSubmit) {
                btnConfirmSubmit.addEventListener('click', function () {
                    var fvEl = document.getElementById('face_verified');
                    var fdEl = document.getElementById('face_distance');
                    if (!lastFaceMatchOk || !fvEl || fvEl.value !== '1') {
                        setFaceStatus('Verifikasi wajah belum valid. Klik "Verifikasi & Kirim" sampai status berhasil.', 'text-red-700');
                        return;
                    }
                    if (!fdEl || fdEl.value === '' || fdEl.value === null) {
                        setFaceStatus('Data verifikasi tidak lengkap. Ulangi verifikasi wajah.', 'text-red-700');
                        return;
                    }
                    var distNum = parseFloat(fdEl.value);
                    if (!isFinite(distNum) || distNum > faceThreshold + 0.02) {
                        setFaceStatus('Skor verifikasi tidak sah. Ulangi verifikasi wajah.', 'text-red-700');
                        lastFaceMatchOk = false;
                        if (fvEl) fvEl.value = '0';
                        return;
                    }
                    if (faceBypassInput) faceBypassInput.value = '0';
                    allowDirectSubmit = true;
                    closeFaceModal();
                    if (formEl) formEl.submit();
                });
            }
            if (btnBypassSubmit) {
                btnBypassSubmit.addEventListener('click', function () {
                    if (noSidToggle && noSidToggle.checked) {
                        setFaceStatus('Opsi ini hanya untuk peserta dengan SID.', 'text-amber-700');
                        return;
                    }
                    if (!kodeInput || (kodeInput.value || '').trim() === '') {
                        setFaceStatus('Isi dan cek Kode SID terlebih dahulu.', 'text-amber-700');
                        return;
                    }
                    lastFaceMatchOk = false;
                    setFaceVerified(false);
                    if (faceBypassInput) faceBypassInput.value = '1';
                    setFaceStatus('Mengirim absensi tanpa verifikasi wajah…', 'text-amber-800');
                    allowDirectSubmit = true;
                    closeFaceModal();
                    if (formEl) formEl.submit();
                });
            }
            if (kodeInput) kodeInput.addEventListener('blur', function () {
                if (noSidToggle && noSidToggle.checked) return;
                if ((kodeInput.value || '').trim().length >= 3) {
                    runLookup();
                }
            });

            if (formEl) {
                formEl.addEventListener('submit', function (evt) {
                    if (allowDirectSubmit) {
                        allowDirectSubmit = false;
                        return;
                    }
                    if (noSidToggle && noSidToggle.checked) return;
                    var bypassEl = document.getElementById('face_bypass');
                    if (bypassEl && bypassEl.value === '1') {
                        return;
                    }
                    if (!latestPhotoUrl) {
                        var sidNow = kodeInput ? (kodeInput.value || '').trim() : '';
                        if (sidNow !== '') {
                            evt.preventDefault();
                            openFaceModal();
                            setFaceStatus('Tidak ada foto referensi atau verifikasi gagal? Gunakan tombol oranye di bawah untuk kirim tanpa verifikasi wajah.', 'text-amber-800');
                            return;
                        }
                        evt.preventDefault();
                        setFaceStatus('Foto referensi belum tersedia. Cek SID terlebih dahulu.', 'text-red-700');
                        return;
                    }
                    var fvEl = document.getElementById('face_verified');
                    var fdEl = document.getElementById('face_distance');
                    var distParsed = fdEl && fdEl.value !== '' ? parseFloat(fdEl.value) : NaN;
                    var distOk = isFinite(distParsed) && distParsed <= faceThreshold + 0.02;
                    if (!lastFaceMatchOk || !fvEl || fvEl.value !== '1' || !distOk) {
                        evt.preventDefault();
                        resetFaceVerification();
                        openFaceModal();
                        startCamera()
                            .then(function () {
                                setFaceStatus('Kamera aktif. Klik "Verifikasi & Kirim" hingga berhasil, lalu "Kirim Absensi Sekarang".', 'text-slate-700');
                            })
                            .catch(function (e) {
                                setFaceStatus((e && e.message) ? e.message : 'Tidak bisa mengaktifkan kamera.', 'text-red-700');
                            });
                    }
                });
            }
            if (faceModalClose) {
                faceModalClose.addEventListener('click', function () {
                    closeFaceModal();
                });
            }
            if (faceModal) {
                faceModal.addEventListener('click', function (evt) {
                    if (evt.target === faceModal) {
                        closeFaceModal();
                    }
                });
            }

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
