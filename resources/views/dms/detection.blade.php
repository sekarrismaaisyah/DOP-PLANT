@extends('layouts.master')

@section('title', 'Deteksi Operator')
@section('content')
<x-page-title title="Deteksi Operator" pagetitle="Driver Monitoring System - Mode Deteksi" />

<style>
    :root {
        --dms-bg: #f0f9ff;
        --dms-card: #ffffff;
        --dms-border: #e0f2fe;
        --dms-primary: #0ea5e9;
        --dms-primary-hover: #0284c7;
        --dms-success: #10b981;
        --dms-danger: #ef4444;
        --dms-muted: #64748b;
        --dms-radius: 16px;
        --dms-shadow: 0 4px 20px rgba(14, 165, 233, 0.08);
        --dms-shadow-hover: 0 8px 30px rgba(14, 165, 233, 0.12);
    }

    .dms-detection-wrap {
        background: linear-gradient(180deg, var(--dms-bg) 0%, #fff 120px);
        min-height: 60px;
        border-radius: var(--dms-radius);
        padding-bottom: 2rem;
    }

    .dms-add-section {
        background: var(--dms-card);
        border: 2px dashed var(--dms-primary);
        border-radius: var(--dms-radius);
        padding: 24px;
        margin-bottom: 24px;
        text-align: center;
        box-shadow: var(--dms-shadow);
        transition: all 0.25s ease;
    }
    .dms-add-section:hover {
        border-color: var(--dms-primary-hover);
        box-shadow: var(--dms-shadow-hover);
    }
    .dms-add-section p {
        color: var(--dms-muted);
        margin-bottom: 16px;
        font-size: 0.95rem;
    }
    .btn-add-detection {
        background: linear-gradient(135deg, var(--dms-primary), var(--dms-primary-hover));
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 14px rgba(14, 165, 233, 0.35);
    }
    .btn-add-detection:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(14, 165, 233, 0.45);
    }

    .dms-card-item {
        background: var(--dms-card);
        border-radius: var(--dms-radius);
        border: 1px solid var(--dms-border);
        box-shadow: var(--dms-shadow);
        overflow: hidden;
        margin-bottom: 24px;
        transition: box-shadow 0.25s ease;
    }
    .dms-card-item:hover {
        box-shadow: var(--dms-shadow-hover);
    }
    .dms-card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 16px 20px;
        border-bottom: 1px solid var(--dms-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .dms-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dms-card-title .badge-num {
        background: var(--dms-primary);
        color: #fff;
        width: 28px;
        height: 28px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }
    .btn-remove-detection {
        background: #fee2e2;
        color: var(--dms-danger);
        border: none;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-remove-detection:hover {
        background: #fecaca;
    }

    .dms-card-body {
        padding: 20px;
    }
    .dms-field {
        margin-bottom: 16px;
    }
    .dms-field label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #334155;
        font-size: 0.875rem;
    }
    .dms-field .required { color: #dc2626; }
    .dms-field select,
    .dms-field input[type="text"] {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .dms-field select:focus,
    .dms-field input[type="text"]:focus {
        outline: none;
        border-color: var(--dms-primary);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
    }
    .btn-refresh-list {
        margin-top: 8px;
        padding: 8px 16px;
        font-size: 13px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.2s;
    }
    .btn-refresh-list:hover { background: #e2e8f0; }

    .dms-video-source {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        padding: 12px;
        background: #f8fafc;
        border-radius: 10px;
        margin-bottom: 12px;
    }
    .dms-video-source label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #475569;
        margin: 0;
    }
    .dms-video-source input[type="radio"] { margin: 0; cursor: pointer; }

    .dms-file-upload {
        position: relative;
        display: block;
        width: 100%;
    }
    .dms-file-upload input[type="file"] {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }
    .dms-file-upload .label-btn {
        display: block;
        padding: 12px 20px;
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: #fff;
        border-radius: 10px;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .dms-file-upload .label-btn:hover { opacity: 0.95; }
    .dms-video-info {
        margin-top: 10px;
        font-size: 13px;
        color: var(--dms-muted);
    }

    .dms-video-section {
        background: #0f172a;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        margin-top: 16px;
        aspect-ratio: 16/9;
    }
    .dms-video-section video {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }
    .dms-video-section canvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }
    .dms-status-overlay {
        position: absolute;
        top: 12px;
        left: 12px;
        background: rgba(0,0,0,0.75);
        color: #fff;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 12px;
        line-height: 1.5;
    }

    .dms-controls {
        display: flex;
        gap: 10px;
        margin-top: 16px;
        flex-wrap: wrap;
    }
    .dms-btn-start {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .dms-btn-start:hover { transform: translateY(-1px); }
    .dms-btn-stop {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .dms-btn-stop:hover { transform: translateY(-1px); }
    .dms-btn-stop:disabled {
        background: #94a3b8;
        cursor: not-allowed;
        transform: none;
    }

    .dms-metrics-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 16px;
    }
    .dms-metric-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        transition: border-color 0.2s;
    }
    .dms-metric-box:hover { border-color: #cbd5e1; }
    .dms-metric-label {
        font-size: 10px;
        font-weight: 700;
        color: var(--dms-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .dms-metric-value {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    .dms-metric-value.highlight {
        color: #0ea5e9;
        font-size: 24px;
    }
    .dms-status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        margin-top: 6px;
    }
    .dms-status-badge.safe { background: #d1fae5; color: #065f46; }
    .dms-status-badge.caution { background: #fef3c7; color: #92400e; }
    .dms-status-badge.attention { background: #fee2e2; color: #991b1b; }

    .dms-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
        gap: 24px;
    }
    @media (max-width: 900px) {
        .dms-cards-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="row mt-4 dms-detection-wrap">
    <div class="col-12">
        <div class="dms-add-section">
            <p class="mb-0">Jalankan deteksi fatigue untuk beberapa operator sekaligus. Tambah slot untuk setiap sumber video.</p>
            <button type="button" class="btn-add-detection" onclick="addDetectionCard()">+ Tambah Slot Deteksi</button>
        </div>
        <div id="detectionCardsContainer" class="dms-cards-grid">
            <!-- Cards will be injected here -->
        </div>
    </div>
</div>

<script type="module">
(async function() {
    try {
        let m = await import('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3');
        let F = m.FilesetResolver || m.default?.FilesetResolver;
        let FL = m.FaceLandmarker || m.default?.FaceLandmarker;
        if (!F && m.default) { F = m.default.FilesetResolver; FL = m.default.FaceLandmarker; }
        if (!F || !FL) throw new Error('MediaPipe not found');
        window.MediaPipeVision = { FilesetResolver: F, FaceLandmarker: FL };
        window.mediaPipeReady = true;
        window.dispatchEvent(new CustomEvent('mediapipe-loaded'));
    } catch (err) {
        console.error(err);
        try {
            const fb = await import('https://unpkg.com/@mediapipe/tasks-vision@0.10.3');
            const F = fb.FilesetResolver || fb.default?.FilesetResolver;
            const FL = fb.FaceLandmarker || fb.default?.FaceLandmarker;
            if (F && FL) {
                window.MediaPipeVision = { FilesetResolver: F, FaceLandmarker: FL };
                window.mediaPipeReady = true;
                window.dispatchEvent(new CustomEvent('mediapipe-loaded'));
                return;
            }
        } catch (e2) {}
        window.mediaPipeError = err;
        window.mediaPipeReady = false;
        window.dispatchEvent(new CustomEvent('mediapipe-error', { detail: err }));
    }
})();
</script>

<script>
(function() {
    const FPS = 25;
    const FRAME_INTERVAL = 1000 / FPS;
    const WINDOW_SIZE = 60 * 1000;
    const BLINK_MIN_DURATION = 0.06 * 1000;
    const BLINK_MAX_DURATION = 0.35 * 1000;
    const MICROSLEEP_DURATION = 1.4 * 1000;
    const LEFT_EYE_INDICES = [33, 160, 158, 133, 153, 144];
    const RIGHT_EYE_INDICES = [362, 385, 387, 263, 373, 380];

    let faceLandmarker = null;
    let cardCounter = 0;
    let detectionCards = {};
    let calibrationsCache = null;

    function setStatus(cardId, text) {
        const el = document.getElementById(cardId + '-status');
        if (el) el.textContent = text;
    }

    async function init() {
        const firstStatus = document.querySelector('[id$="-status"]');
        if (firstStatus) firstStatus.textContent = 'Loading MediaPipe...';
        try {
            await new Promise((resolve, reject) => {
                if (window.mediaPipeReady && window.MediaPipeVision) { resolve(); return; }
                if (window.mediaPipeError) { reject(window.mediaPipeError); return; }
                const t = setTimeout(() => reject(new Error('MediaPipe timeout')), 15000);
                window.addEventListener('mediapipe-loaded', () => { clearTimeout(t); resolve(); }, { once: true });
                window.addEventListener('mediapipe-error', e => { clearTimeout(t); reject(e.detail); }, { once: true });
            });
        } catch (e) {
            document.querySelectorAll('[id$="-status"]').forEach(el => { el.textContent = 'Error: MediaPipe failed'; });
            return;
        }
        if (!window.MediaPipeVision || !window.MediaPipeVision.FilesetResolver) return;
        try {
            document.querySelectorAll('[id$="-status"]').forEach(el => { el.textContent = 'Initializing...'; });
            const vision = await window.MediaPipeVision.FilesetResolver.forVisionTasks('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/wasm');
            faceLandmarker = await window.MediaPipeVision.FaceLandmarker.createFromOptions(vision, {
                baseOptions: { modelAssetPath: 'https://storage.googleapis.com/mediapipe-models/face_landmarker/face_landmarker/float16/1/face_landmarker.task', delegate: 'GPU' },
                outputFaceBlendshapes: false,
                runningMode: 'VIDEO',
                numFaces: 1
            });
            document.querySelectorAll('[id$="-status"]').forEach(el => { el.textContent = 'Ready'; });
        } catch (err) {
            document.querySelectorAll('[id$="-status"]').forEach(el => { el.textContent = 'Error: ' + err.message; });
        }
    }

    function calculateEAR(landmarks, indices) {
        const points = indices.map(i => ({ x: landmarks[i].x, y: landmarks[i].y }));
        const v1 = Math.sqrt(Math.pow(points[1].x - points[5].x, 2) + Math.pow(points[1].y - points[5].y, 2));
        const v2 = Math.sqrt(Math.pow(points[2].x - points[4].x, 2) + Math.pow(points[2].y - points[4].y, 2));
        const h = Math.sqrt(Math.pow(points[0].x - points[3].x, 2) + Math.pow(points[0].y - points[3].y, 2));
        return (v1 + v2) / (2.0 * h);
    }

    function ensureCanvasMatchesVideo(videoEl, canvasEl, ctx) {
        if (!videoEl || !canvasEl || !ctx) return { w: 0, h: 0 };
        const w = videoEl.videoWidth || 0, h = videoEl.videoHeight || 0;
        if (!w || !h) return { w: 0, h: 0 };
        const dpr = window.devicePixelRatio || 1;
        if (canvasEl.width !== Math.floor(w * dpr) || canvasEl.height !== Math.floor(h * dpr)) {
            canvasEl.width = Math.floor(w * dpr);
            canvasEl.height = Math.floor(h * dpr);
            canvasEl.style.width = w + 'px';
            canvasEl.style.height = h + 'px';
        }
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        return { w, h };
    }

    function drawSimpleEye(ctx, points) {
        if (!points || points.length < 2) return;
        ctx.strokeStyle = '#00ff00';
        ctx.lineWidth = 2;
        ctx.fillStyle = '#00ff00';
        ctx.beginPath();
        ctx.moveTo(points[0].x, points[0].y);
        for (let i = 1; i < points.length; i++) ctx.lineTo(points[i].x, points[i].y);
        ctx.closePath();
        ctx.stroke();
        points.forEach(p => { ctx.beginPath(); ctx.arc(p.x, p.y, 3, 0, 2 * Math.PI); ctx.fill(); });
    }

    function buildCalibrationOptions(list) {
        if (!list || list.length === 0) return '<option value="">-- Pilih Kalibrasi --</option>';
        var opts = ['<option value="">-- Pilih Kalibrasi --</option>'];
        list.forEach(function(cal) {
            var d = new Date(cal.calibration_start_time);
            var earVal = (cal.ear_mean != null) ? parseFloat(cal.ear_mean) : 0;
            var dataStr = JSON.stringify(cal).replace(/'/g, '&#39;');
            opts.push('<option value="' + cal.id + '" data-calibration=\'' + dataStr + '\'>' + (cal.driver_id || '') + ' - ' + d.toLocaleDateString('id-ID') + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' (EAR: ' + earVal.toFixed(4) + ')</option>');
        });
        return opts.join('');
    }

    function fillSelectFromCache(selectEl) {
        if (!selectEl || !calibrationsCache || calibrationsCache.length === 0) return false;
        selectEl.innerHTML = buildCalibrationOptions(calibrationsCache);
        return true;
    }

    async function loadCalibrationsForSelect(selectEl, thenFillAll) {
        if (!selectEl) return;
        try {
            var response = await fetch('/api/dms/calibrations', {
                method: 'GET',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            if (!response.ok) return;
            var data = await response.json();
            calibrationsCache = (data.success && data.data) ? data.data : [];
            selectEl.innerHTML = buildCalibrationOptions(calibrationsCache);
            if (thenFillAll) fillAllCalibrationSelects();
        } catch (e) { console.error(e); }
    }

    function fillAllCalibrationSelects() {
        if (!calibrationsCache || calibrationsCache.length === 0) return;
        var html = buildCalibrationOptions(calibrationsCache);
        document.querySelectorAll('[id$="-calibrationSelect"]').forEach(function(sel) {
            if (sel.id && detectionCards[sel.id.replace('-calibrationSelect', '')]) sel.innerHTML = html;
        });
    }

    async function loadCalibrations(cardId) {
        var sel = document.getElementById(cardId + '-calibrationSelect');
        if (!sel) return;
        await loadCalibrationsForSelect(sel, true);
    }

    function getCardState(cardId) {
        return detectionCards[cardId];
    }

    function loadBaselineForCard(cardId, calibration) {
        if (!calibration || !detectionCards[cardId]) return;
        var earMean = parseFloat(calibration.ear_mean != null ? calibration.ear_mean : calibration.earMean);
        var earSd = parseFloat(calibration.ear_sd != null ? calibration.ear_sd : calibration.earSd);
        if (!Number.isFinite(earMean)) earMean = 0.25;
        if (!Number.isFinite(earSd)) earSd = 0.02;
        detectionCards[cardId].baseline = {
            T_close: earMean - 0.02,
            EAR_mean: earMean,
            EAR_sd: earSd
        };
        detectionCards[cardId].selectedCalibration = calibration;
        setStatus(cardId, 'Baseline loaded');
    }

    function createCardState() {
        return {
            isRunning: false,
            videoFile: null,
            videoUrl: null,
            streamUrl: null,
            videoSource: 'upload',
            isVideoLoaded: false,
            earHistory: [],
            baseline: null,
            selectedCalibration: null,
            previousMetrics: { safety_score: null, status: null, fatigue: null, drift: null, perclos_60s: null, blink_60s: null, microsleep_60s: null },
            frameCount: 0
        };
    }

    function addDetectionCard() {
        cardCounter++;
        const cardId = 'det-' + cardCounter;
        detectionCards[cardId] = createCardState();

        const num = cardCounter;
        const cardHtml = `
        <div class="dms-card-item" id="card-${cardId}" data-card-id="${cardId}">
            <div class="dms-card-header">
                <h3 class="dms-card-title"><span class="badge-num">${num}</span> Slot Deteksi</h3>
                <button type="button" class="btn-remove-detection" onclick="removeDetectionCard('${cardId}')">Hapus</button>
            </div>
            <div class="dms-card-body">
                <div class="dms-field">
                    <label>Pilih Kalibrasi <span class="required">*</span></label>
                    <select id="${cardId}-calibrationSelect" class="form-control"></select>
                    <button type="button" class="btn-refresh-list" onclick="loadCalibrations('${cardId}')">🔄 Refresh</button>
                </div>
                <div class="dms-field">
                    <label>Sumber Video <span class="required">*</span></label>
                    <div class="dms-video-source">
                        <label><input type="radio" name="${cardId}-videoSource" value="upload" checked> 📁 Upload</label>
                        <label><input type="radio" name="${cardId}-videoSource" value="stream"> 🔗 Stream URL</label>
                    </div>
                    <div id="${cardId}-uploadSection">
                        <div class="dms-file-upload">
                            <input type="file" id="${cardId}-videoFile" accept="video/*">
                            <span class="label-btn">📹 Pilih Video</span>
                        </div>
                        <div class="dms-video-info" id="${cardId}-videoInfo" style="display:none;"><span id="${cardId}-videoFileName">-</span></div>
                    </div>
                    <div id="${cardId}-streamSection" style="display:none;">
                        <input type="text" id="${cardId}-streamUrl" placeholder="https://example.com/stream.m3u8" class="form-control mt-2">
                        <button type="button" class="btn-refresh-list mt-2" onclick="loadCardStream('${cardId}')">Load Stream</button>
                    </div>
                </div>
                <div class="dms-controls" id="${cardId}-controls" style="display:none;">
                    <button type="button" class="dms-btn-start" id="${cardId}-startBtn" onclick="startDetectionMode('${cardId}')">Start</button>
                    <button type="button" class="dms-btn-stop" id="${cardId}-stopBtn" onclick="stopDetectionMode('${cardId}')" disabled>Stop</button>
                </div>
                <div id="${cardId}-videoMetricsRow" style="display:none;">
                    <div class="dms-video-section mt-3">
                        <video id="${cardId}-videoElement" autoplay playsinline></video>
                        <canvas id="${cardId}-canvasElement"></canvas>
                        <div class="dms-status-overlay">Status: <span id="${cardId}-status">Ready</span><br>EAR: <span id="${cardId}-ear">--</span></div>
                    </div>
                    <div class="dms-metrics-row">
                        <div class="dms-metric-box">
                            <div class="dms-metric-label">Safety Score</div>
                            <div class="dms-metric-value highlight" id="${cardId}-safetyScore">--</div>
                            <span class="dms-status-badge safe" id="${cardId}-statusBadge">Safe</span>
                        </div>
                        <div class="dms-metric-box">
                            <div class="dms-metric-label">Fatigue</div>
                            <div class="dms-metric-value" id="${cardId}-fatigueScore">--</div>
                        </div>
                        <div class="dms-metric-box">
                            <div class="dms-metric-label">Drift</div>
                            <div class="dms-metric-value" id="${cardId}-driftScore">--</div>
                        </div>
                        <div class="dms-metric-box">
                            <div class="dms-metric-label">PERCLOS (60s)</div>
                            <div class="dms-metric-value" id="${cardId}-perclos">--</div>
                        </div>
                        <div class="dms-metric-box">
                            <div class="dms-metric-label">Blink (60s)</div>
                            <div class="dms-metric-value" id="${cardId}-blinkCount">0</div>
                        </div>
                        <div class="dms-metric-box">
                            <div class="dms-metric-label">Microsleep (60s)</div>
                            <div class="dms-metric-value" id="${cardId}-microsleepCount">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        document.getElementById('detectionCardsContainer').insertAdjacentHTML('beforeend', cardHtml);

        document.querySelectorAll(`input[name="${cardId}-videoSource"]`).forEach(radio => {
            radio.addEventListener('change', function() {
                const isUpload = this.value === 'upload';
                document.getElementById(cardId + '-uploadSection').style.display = isUpload ? 'block' : 'none';
                document.getElementById(cardId + '-streamSection').style.display = isUpload ? 'none' : 'block';
            });
        });

        document.getElementById(cardId + '-videoFile').addEventListener('change', function(e) {
            handleCardVideoUpload(cardId, e);
        });

        document.getElementById(cardId + '-calibrationSelect').addEventListener('change', function() {
            var opt = this.options[this.selectedIndex];
            if (opt && opt.value) {
                try {
                    var raw = opt.getAttribute('data-calibration');
                    var cal = raw ? JSON.parse(raw.replace(/&#39;/g, "'")) : null;
                    if (cal) loadBaselineForCard(cardId, cal);
                } catch (err) {}
            } else {
                detectionCards[cardId].baseline = null;
                detectionCards[cardId].selectedCalibration = null;
            }
        });

        var newSelect = document.getElementById(cardId + '-calibrationSelect');
        if (fillSelectFromCache(newSelect)) {
            /* List filled from cache (slot 1 already loaded calibrations) */
        } else {
            loadCalibrationsForSelect(newSelect, false);
        }
        setStatus(cardId, window.mediaPipeReady ? 'Ready' : 'Menunggu MediaPipe...');
    }

    function removeDetectionCard(cardId) {
        const state = detectionCards[cardId];
        if (state) {
            if (state.isRunning) stopDetectionMode(cardId);
            if (state.videoUrl) URL.revokeObjectURL(state.videoUrl);
        }
        const card = document.getElementById('card-' + cardId);
        if (card) card.remove();
        delete detectionCards[cardId];
    }

    function handleCardVideoUpload(cardId, event) {
        const file = event.target.files[0];
        if (!file) return;
        if (!file.type.startsWith('video/')) {
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Invalid', text: 'File harus video!' });
            return;
        }
        const state = detectionCards[cardId];
        if (!state) return;
        if (state.videoUrl) URL.revokeObjectURL(state.videoUrl);
        state.videoFile = file;
        state.videoUrl = URL.createObjectURL(file);
        state.streamUrl = null;
        state.videoSource = 'upload';
        state.frameCount = 0;
        state.earHistory = [];
        state.isVideoLoaded = true;

        const video = document.getElementById(cardId + '-videoElement');
        const canvas = document.getElementById(cardId + '-canvasElement');
        video.src = state.videoUrl;
        video.srcObject = null;
        video.removeAttribute('crossorigin');
        document.getElementById(cardId + '-videoMetricsRow').style.display = 'block';
        document.getElementById(cardId + '-controls').style.display = 'flex';
        document.getElementById(cardId + '-videoFileName').textContent = file.name;
        document.getElementById(cardId + '-videoInfo').style.display = 'block';
        document.getElementById(cardId + '-startBtn').disabled = false;
        video.onloadedmetadata = () => {
            if (canvas) { canvas.width = video.videoWidth; canvas.height = video.videoHeight; }
            setStatus(cardId, 'Video loaded - Start untuk deteksi');
        };
        video.onerror = () => setStatus(cardId, 'Error loading video');
    }

    function loadCardStream(cardId) {
        const url = document.getElementById(cardId + '-streamUrl').value.trim();
        if (!url) {
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'URL kosong', text: 'Masukkan URL stream!' });
            return;
        }
        const state = detectionCards[cardId];
        if (!state) return;
        if (state.videoUrl) URL.revokeObjectURL(state.videoUrl);
        state.videoUrl = null;
        state.streamUrl = url;
        state.videoSource = 'stream';
        state.frameCount = 0;
        state.earHistory = [];
        state.isVideoLoaded = true;

        const video = document.getElementById(cardId + '-videoElement');
        const canvas = document.getElementById(cardId + '-canvasElement');
        setStatus(cardId, 'Loading stream...');
        video.src = '';
        video.crossOrigin = 'anonymous';
        video.src = url;
        video.load();
        document.getElementById(cardId + '-videoMetricsRow').style.display = 'block';
        document.getElementById(cardId + '-controls').style.display = 'flex';
        document.getElementById(cardId + '-startBtn').disabled = false;
        video.onloadedmetadata = () => {
            if (canvas) { canvas.width = video.videoWidth; canvas.height = video.videoHeight; }
            setStatus(cardId, 'Stream loaded - Start untuk deteksi');
        };
        video.onerror = () => {
            setStatus(cardId, 'Error loading stream');
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memuat stream.' });
        };
    }

    function calculateMetricsForDetectionMode(history, currentTime, baseline) {
        if (!baseline || !baseline.T_close || history.length === 0) return null;
        const windowStart = currentTime - WINDOW_SIZE;
        const windowData = history.filter(d => d.timestamp >= windowStart);
        if (windowData.length === 0) return null;
        const belowThreshold = windowData.filter(d => d.ear < baseline.T_close).length;
        const perclos = belowThreshold / windowData.length;
        let blinkCount = 0, inBlink = false, blinkStart = null;
        for (let i = 0; i < windowData.length; i++) {
            const isBelow = windowData[i].ear < baseline.T_close;
            const prevIsBelow = i > 0 ? windowData[i - 1].ear < baseline.T_close : false;
            if (isBelow && !prevIsBelow) { inBlink = true; blinkStart = windowData[i].timestamp; }
            else if (!isBelow && prevIsBelow && inBlink) {
                const duration = windowData[i].timestamp - blinkStart;
                if (duration >= BLINK_MIN_DURATION && duration <= BLINK_MAX_DURATION) blinkCount++;
                inBlink = false;
            }
        }
        if (inBlink && windowData.length > 0) {
            const duration = currentTime - blinkStart;
            if (duration >= BLINK_MIN_DURATION && duration <= BLINK_MAX_DURATION) blinkCount++;
        }
        let microsleepCount = 0, inMicrosleep = false, microsleepStart = null;
        for (let i = 0; i < windowData.length; i++) {
            const isBelow = windowData[i].ear < baseline.T_close;
            const prevIsBelow = i > 0 ? windowData[i - 1].ear < baseline.T_close : false;
            if (isBelow && !prevIsBelow) { inMicrosleep = true; microsleepStart = windowData[i].timestamp; }
            else if (!isBelow && prevIsBelow && inMicrosleep) {
                if (windowData[i].timestamp - microsleepStart >= MICROSLEEP_DURATION) microsleepCount++;
                inMicrosleep = false;
            }
        }
        if (inMicrosleep && windowData.length > 0 && currentTime - microsleepStart >= MICROSLEEP_DURATION) microsleepCount++;
        const n = windowData.length;
        const sumX = windowData.reduce((s, d, i) => s + i, 0);
        const sumY = windowData.reduce((s, d) => s + d.ear, 0);
        const sumXY = windowData.reduce((s, d, i) => s + i * d.ear, 0);
        const sumX2 = windowData.reduce((s, d, i) => s + i * i, 0);
        const denom = n * sumX2 - sumX * sumX;
        const slope = (denom === 0 || !Number.isFinite(denom)) ? 0 : (n * sumXY - sumX * sumY) / denom;
        const lowerBound = baseline.EAR_mean - baseline.EAR_sd;
        const upperBound = baseline.EAR_mean + baseline.EAR_sd;
        const outOfBand = windowData.filter(d => d.ear < lowerBound || d.ear > upperBound).length;
        const bandOutRatio = outOfBand / windowData.length;
        const midPoint = Math.floor(windowData.length / 2);
        const firstHalf = windowData.slice(0, midPoint);
        const secondHalf = windowData.slice(midPoint);
        const perclosFirst = firstHalf.length > 0 ? firstHalf.filter(d => d.ear < baseline.T_close).length / firstHalf.length : 0;
        const perclosSecond = secondHalf.length > 0 ? secondHalf.filter(d => d.ear < baseline.T_close).length / secondHalf.length : 0;
        const deltaPerclos = perclosSecond - perclosFirst;
        const zPerclos = Math.min(perclos, 1.0);
        const microsleepIndicator = microsleepCount > 0 ? 1 : 0;
        const blinkRate = blinkCount / 60;
        const sigmaDeltaBlink = Math.min(Math.abs(blinkRate - 0.375) / 0.125 / 3.0, 1.0);
        const denseBlink = blinkRate > 0.5 ? 1 : 0;
        const fatigue = 100 * (0.45 * zPerclos + 0.25 * microsleepIndicator + 0.20 * sigmaDeltaBlink + 0.10 * denseBlink);
        const slopePerSec = slope * FPS;
        const sigmaNegSlope = Math.min(Math.max(0, -slopePerSec) * 10, 1.0);
        const drift = 100 * (0.5 * sigmaNegSlope + 0.3 * bandOutRatio + 0.2 * Math.abs(deltaPerclos));
        const safetyScore = Math.max(0, Math.min(100, 100 - (0.7 * fatigue + 0.3 * drift + microsleepCount * 10)));
        let status = 'Safe';
        if (safetyScore < 60) status = 'Attention';
        else if (safetyScore < 80) status = 'Caution';
        var lastEar = windowData[windowData.length - 1].ear;
        return {
            ear: lastEar,
            perclos: Number.isFinite(perclos) ? perclos : 0,
            blinkCount: blinkCount || 0,
            microsleepCount: microsleepCount || 0,
            fatigue: Number.isFinite(fatigue) ? fatigue : 0,
            drift: Number.isFinite(drift) ? drift : 0,
            safetyScore: Number.isFinite(safetyScore) ? safetyScore : 100,
            status: status
        };
    }

    function hasMetricsChanged(state, metrics) {
        if (!metrics) return false;
        if (state.previousMetrics.safety_score === null) return true;
        const th = 0.01;
        const ch = (a, b) => (a === null || b === null ? a !== b : Math.abs(a - b) >= th);
        return ch(state.previousMetrics.safety_score, metrics.safetyScore) || state.previousMetrics.status !== metrics.status ||
            ch(state.previousMetrics.fatigue, metrics.fatigue) || ch(state.previousMetrics.drift, metrics.drift) ||
            ch(state.previousMetrics.perclos_60s, metrics.perclos) || state.previousMetrics.blink_60s !== metrics.blinkCount ||
            state.previousMetrics.microsleep_60s !== metrics.microsleepCount;
    }

    function updateCardMetrics(cardId, metrics) {
        if (!metrics || typeof metrics.safetyScore !== 'number') return;
        const el = function(idSuffix) { return document.getElementById(cardId + '-' + idSuffix); };
        const safeNum = function(v) { return (typeof v === 'number' && Number.isFinite(v)) ? v : 0; };
        const safetyEl = el('safetyScore');
        const fatigueEl = el('fatigueScore');
        const driftEl = el('driftScore');
        const perclosEl = el('perclos');
        const blinkEl = el('blinkCount');
        const microEl = el('microsleepCount');
        const badgeEl = el('statusBadge');
        if (safetyEl) safetyEl.textContent = Math.round(safeNum(metrics.safetyScore)).toString();
        if (fatigueEl) fatigueEl.textContent = safeNum(metrics.fatigue).toFixed(0);
        if (driftEl) driftEl.textContent = safeNum(metrics.drift).toFixed(0);
        if (perclosEl) perclosEl.textContent = (safeNum(metrics.perclos) * 100).toFixed(1) + '%';
        if (blinkEl) blinkEl.textContent = String(metrics.blinkCount != null ? metrics.blinkCount : 0);
        if (microEl) microEl.textContent = String(metrics.microsleepCount != null ? metrics.microsleepCount : 0);
        if (badgeEl) {
            badgeEl.className = 'dms-status-badge ' + (metrics.status === 'Safe' ? 'safe' : metrics.status === 'Caution' ? 'caution' : 'attention');
            badgeEl.textContent = metrics.status || 'Safe';
        }
    }

    async function sendCardToAPI(cardId, metrics, timestamp) {
        const state = detectionCards[cardId];
        if (!metrics || !state || !state.selectedCalibration) return;
        const cal = state.selectedCalibration;
        const payload = {
            driver_id: cal.driver_id,
            trip_id: cal.trip_id || null,
            calibration_id: cal.id,
            timestamp: new Date(timestamp).toISOString(),
            ear: metrics.ear || null,
            perclos_60s: metrics.perclos || null,
            blink_60s: metrics.blinkCount || 0,
            microsleep_60s: metrics.microsleepCount || 0,
            fatigue: metrics.fatigue || null,
            drift: metrics.drift || null,
            safety_score: metrics.safetyScore || null,
            status: metrics.status || null
        };
        try {
            await fetch('/api/dms/safety-score', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify(payload)
            });
        } catch (e) { console.error(e); }
    }

    function drawCardLandmarks(landmarks, video, canvas, ctx) {
        if (!video || !canvas || !ctx) return;
        const { w, h } = ensureCanvasMatchesVideo(video, canvas, ctx);
        if (!w || !h) return;
        ctx.clearRect(0, 0, w, h);
        try {
            const leftEyePoints = LEFT_EYE_INDICES.map(i => ({ x: landmarks[i].x * w, y: landmarks[i].y * h }));
            drawSimpleEye(ctx, leftEyePoints);
        } catch (e) {}
        try {
            const rightEyePoints = RIGHT_EYE_INDICES.map(i => ({ x: landmarks[i].x * w, y: landmarks[i].y * h }));
            drawSimpleEye(ctx, rightEyePoints);
        } catch (e) {}
    }

    function processCardDetection(cardId, ear, timestamp) {
        const state = detectionCards[cardId];
        if (!state) return;
        state.earHistory.push({ ear, timestamp });
        state.earHistory = state.earHistory.filter(d => d.timestamp >= timestamp - WINDOW_SIZE);
        if (state.earHistory.length < 10) return;
        var base = state.baseline;
        if (!base || typeof base.T_close !== 'number' || !Number.isFinite(base.T_close)) return;
        const metrics = calculateMetricsForDetectionMode(state.earHistory, timestamp, base);
        if (metrics) updateCardMetrics(cardId, metrics);
        if (metrics && hasMetricsChanged(state, metrics)) {
            sendCardToAPI(cardId, metrics, timestamp);
            state.previousMetrics = {
                safety_score: metrics.safetyScore, status: metrics.status, fatigue: metrics.fatigue, drift: metrics.drift,
                perclos_60s: metrics.perclos, blink_60s: metrics.blinkCount, microsleep_60s: metrics.microsleepCount
            };
        }
    }

    function processDetectionFrame(cardId) {
        const state = detectionCards[cardId];
        if (!state || !state.isRunning || !faceLandmarker) return;
        const video = document.getElementById(cardId + '-videoElement');
        const canvas = document.getElementById(cardId + '-canvasElement');
        const ctx = canvas.getContext('2d');
        const now = Date.now();
        const startTimeMs = performance.now();
        if (!video || (video.readyState < 2 && !state.videoUrl && !state.streamUrl)) {
            setTimeout(() => processDetectionFrame(cardId), FRAME_INTERVAL);
            return;
        }
        if ((state.videoUrl || state.streamUrl) && video.paused) {
            setTimeout(() => processDetectionFrame(cardId), FRAME_INTERVAL);
            return;
        }
        try {
            const videoTimeMs = (state.videoUrl || state.streamUrl) ? state.frameCount * FRAME_INTERVAL : startTimeMs;
            if (state.videoUrl || state.streamUrl) state.frameCount++;
            const results = faceLandmarker.detectForVideo(video, videoTimeMs);
            if (results.faceLandmarks && results.faceLandmarks.length > 0) {
                const landmarks = results.faceLandmarks[0];
                if (landmarks.length < 400) {
                    setStatus(cardId, 'Insufficient landmarks');
                    document.getElementById(cardId + '-ear').textContent = '--';
                    setTimeout(() => processDetectionFrame(cardId), FRAME_INTERVAL);
                    return;
                }
                const leftEAR = calculateEAR(landmarks, LEFT_EYE_INDICES);
                const rightEAR = calculateEAR(landmarks, RIGHT_EYE_INDICES);
                const avgEAR = (leftEAR + rightEAR) / 2;
                if (isNaN(avgEAR) || avgEAR <= 0 || avgEAR > 1) {
                    setStatus(cardId, 'Invalid EAR');
                    document.getElementById(cardId + '-ear').textContent = '--';
                    setTimeout(() => processDetectionFrame(cardId), FRAME_INTERVAL);
                    return;
                }
                drawCardLandmarks(landmarks, video, canvas, ctx);
                document.getElementById(cardId + '-ear').textContent = avgEAR.toFixed(4);
                setStatus(cardId, 'Face Detected');
                processCardDetection(cardId, avgEAR, now);
            } else {
                setStatus(cardId, 'No Face');
                document.getElementById(cardId + '-ear').textContent = '--';
                if (ctx) ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
        } catch (err) {
            setStatus(cardId, 'Error: ' + err.message);
        }
        setTimeout(() => processDetectionFrame(cardId), FRAME_INTERVAL);
    }

    async function startDetectionMode(cardId) {
        const state = detectionCards[cardId];
        if (!state) return;
        if (!state.selectedCalibration) {
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Kalibrasi belum dipilih', text: 'Pilih kalibrasi untuk slot ini.' });
            return;
        }
        if (!state.isVideoLoaded) {
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Video belum dimuat', text: 'Upload video atau load stream terlebih dahulu.' });
            return;
        }
        const video = document.getElementById(cardId + '-videoElement');
        const canvas = document.getElementById(cardId + '-canvasElement');
        if (!state.baseline || !state.baseline.T_close) loadBaselineForCard(cardId, state.selectedCalibration);
        if (video.readyState < 2) {
            await new Promise((resolve, reject) => {
                video.addEventListener('loadedmetadata', resolve, { once: true });
                video.addEventListener('error', reject, { once: true });
                video.load();
                setTimeout(() => reject(new Error('Timeout')), 10000);
            });
        }
        try { await video.play(); } catch (e) {}
        if (canvas) { canvas.width = video.videoWidth; canvas.height = video.videoHeight; }
        if (state.videoUrl) video.onended = () => { if (state.isRunning) { video.currentTime = 0; video.play(); } };
        state.isRunning = true;
        state.earHistory = [];
        state.frameCount = state.frameCount || 0;
        state.previousMetrics = { safety_score: null, status: null, fatigue: null, drift: null, perclos_60s: null, blink_60s: null, microsleep_60s: null };
        document.getElementById(cardId + '-startBtn').disabled = true;
        document.getElementById(cardId + '-stopBtn').disabled = false;
        setStatus(cardId, 'Detecting...');
        processDetectionFrame(cardId);
    }

    function stopDetectionMode(cardId) {
        const state = detectionCards[cardId];
        if (!state) return;
        state.isRunning = false;
        const video = document.getElementById(cardId + '-videoElement');
        if (video) video.pause();
        document.getElementById(cardId + '-startBtn').disabled = false;
        document.getElementById(cardId + '-stopBtn').disabled = true;
        setStatus(cardId, 'Stopped');
    }

    window.addDetectionCard = addDetectionCard;
    window.removeDetectionCard = removeDetectionCard;
    window.loadCalibrations = loadCalibrations;
    window.loadCardStream = loadCardStream;
    window.startDetectionMode = startDetectionMode;
    window.stopDetectionMode = stopDetectionMode;
    window.fillAllCalibrationSelects = fillAllCalibrationSelects;

    window.addEventListener('load', function() {
        addDetectionCard();
        init();
    });
})();
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
