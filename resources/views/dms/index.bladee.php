@extends('layouts.master')

@section('title', 'DMS Detection')
@section('content')
<x-page-title title="DMS Detection" pagetitle="Driver Monitoring System - Real-time Detection" />

<style>

        .video-section {
            background: #000;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            margin-bottom: 20px;
            max-width: 100%;
        }

        #videoElement {
            width: 100%;
            height: auto;
            display: block;
        }

        #canvasElement {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .status-overlay {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            font-size: 14px;
        }

        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .driver-card {
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background: #ffffff;
            overflow: hidden;
            position: relative;
            margin-bottom: 24px;
        }
        .driver-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .driver-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f3f4f6;
            background: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .driver-card-title {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }

        .card-video-section {
            background: #000;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            margin-bottom: 15px;
            width: 100%;
            aspect-ratio: 16/9;
        }

        .card-video-element {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .card-video-iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: none;
            position: absolute;
            top: 0;
            left: 0;
        }

        .card-video-iframe.active {
            display: block;
        }

        #display-container {
            width: 100%;
            height: 100%;
            position: relative;
            background: #000;
        }

        .detection-video-wrapper {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .detection-video-wrapper video {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .detection-video-wrapper canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        #display-container {
            width: 100%;
            height: 100%;
            position: relative;
            background: #000;
        }

        .detection-video-wrapper {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .detection-video-wrapper video {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .detection-video-wrapper canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .card-canvas-element {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .card-status-overlay {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            z-index: 10;
        }

        .card-video-controls {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .card-video-controls button {
            padding: 8px 16px;
            font-size: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .card-upload-section {
            margin-bottom: 15px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 2px dashed #cbd5e0;
        }

        .card-upload-section.has-video {
            border-color: #38b2ac;
            border-style: solid;
        }

        .video-source-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            padding: 10px;
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .video-source-toggle label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }

        .video-source-toggle input[type="radio"] {
            margin: 0;
            cursor: pointer;
        }

        .stream-input-group {
            margin-top: 15px;
        }

        .stream-input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .stream-input-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-load-stream {
            margin-top: 10px;
            padding: 8px 16px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-load-stream:hover {
            background: #059669;
        }

        .btn-load-stream:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        .card-file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .card-file-input-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .card-file-input-label {
            display: inline-block;
            padding: 8px 16px;
            background: #4299e1;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            width: 100%;
            text-align: center;
        }

        .card-file-input-label:hover {
            background: #3182ce;
        }

        .card-video-info {
            margin-top: 8px;
            font-size: 0.85em;
            color: #4a5568;
        }

        .driver-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .driver-card-title {
            font-size: 1.2em;
            font-weight: bold;
            color: #2d3748;
        }

        .driver-card-actions {
            display: flex;
            gap: 10px;
        }

        .btn-remove-card {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 0.85em;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-remove-card:hover {
            background: #dc2626;
        }

        .metrics-panel {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }
        .metric-item {
            display: flex;
            flex-direction: column;
            padding: 12px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }
        .metric-item:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }
        .metric-label {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 6px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .metric-value {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
        }
        .metric-value.orange {
            color: #f97316;
            font-size: 28px;
        }
        .metric-value.small {
            font-size: 16px;
            font-weight: 600;
        }
        .safety-score-card {
            background: #d1fae5;
            border-color: #10b981;
        }
        .safety-score-card.caution {
            background: #fed7aa;
            border-color: #f59e0b;
        }
        .safety-score-card.attention {
            background: #fee2e2;
            border-color: #ef4444;
        }

        .calibration-status {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            margin-bottom: 16px;
        }

        .calibration-status.active {
            background: #dbeafe;
            border-color: #3b82f6;
        }

        .calibration-status.complete {
            background: #d1fae5;
            border-color: #10b981;
        }

        .calibration-timer {
            font-size: 1.5em;
            font-weight: bold;
            color: #111827;
            margin: 10px 0;
        }

        .controls {
            padding: 20px;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .upload-section {
            width: 100%;
            max-width: 600px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            border: 2px dashed #cbd5e0;
            text-align: center;
        }

        .upload-section.has-video {
            border-color: #38b2ac;
            border-style: solid;
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-label {
            display: inline-block;
            padding: 12px 24px;
            background: #4299e1;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .file-input-label:hover {
            background: #3182ce;
        }

        .video-info {
            margin-top: 10px;
            font-size: 0.9em;
            color: #4a5568;
        }

        .video-controls {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        button {
            padding: 12px 30px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-start {
            background: #38b2ac;
            color: white;
        }

        .btn-start:hover {
            background: #319795;
            transform: translateY(-2px);
        }

        .btn-stop {
            background: #e53e3e;
            color: white;
        }

        .btn-stop:hover {
            background: #c53030;
            transform: translateY(-2px);
        }

        .btn-stop:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-badge.safe {
            background: #d1fae5;
            color: #065f46;
        }
        .status-badge.caution {
            background: #fed7aa;
            color: #92400e;
        }
        .status-badge.attention {
            background: #fee2e2;
            color: #991b1b;
        }
        .driver-card-body {
            padding: 24px;
        }

        .add-card-section {
            padding: 20px;
            background: white;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 2px dashed #cbd5e0;
            text-align: center;
        }

        .btn-add-card {
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-add-card:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .mode-selection {
            padding: 20px;
            background: white;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
        }

        .mode-selection h3 {
            margin-bottom: 15px;
            color: #2d3748;
            font-size: 1.1em;
        }

        .mode-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .btn-mode {
            flex: 1;
            padding: 12px 24px;
            border: 2px solid #cbd5e0;
            background: white;
            color: #4a5568;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-mode:hover {
            border-color: #4299e1;
            color: #4299e1;
        }

        .btn-mode.active {
            background: #4299e1;
            color: white;
            border-color: #4299e1;
        }

        .mode-input-group {
            margin-bottom: 15px;
        }

        .mode-input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9em;
        }

        .mode-input-group input,
        .mode-input-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .mode-input-group input:focus,
        .mode-input-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .required-mark {
            color: #e53e3e;
        }

        @media (max-width: 1024px) {
            .cards-container {
                grid-template-columns: 1fr;
            }
            .metrics-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="add-card-section">
                    <button class="btn-add-card" onclick="showAddCalibrationModal()">+ Tambah Kalibrasi Driver</button>
                </div>

                <div class="cards-container" id="cardsContainer">
                    <!-- Cards will be dynamically added here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Tambah Kalibrasi -->
<div class="modal fade" id="addCalibrationModal" tabindex="-1" aria-labelledby="addCalibrationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCalibrationModalLabel">Tambah Kalibrasi Driver</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCalibrationForm">
                    <div class="mb-3">
                        <label for="modalDriverId" class="form-label">
                            Nama Driver <span class="required-mark">*</span>
                        </label>
                        <input type="text" class="form-control" id="modalDriverId" name="driver_id" required placeholder="Contoh: John Doe">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="modalTripId" class="form-label">
                            Unit / Trip ID <span class="required-mark">*</span>
                        </label>
                        <input type="text" class="form-control" id="modalTripId" name="trip_id" required placeholder="Contoh: T001 atau Unit-001">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="modalNotes" class="form-label">
                            Link Streaming <span class="required-mark">*</span>
                        </label>
                        <input type="text" class="form-control" id="modalNotes" name="notes" required placeholder="Contoh: https://example.com/stream.m3u8">
                        <div class="invalid-feedback"></div>
                        <small class="form-text text-muted">Masukkan URL streaming yang akan digunakan untuk kalibrasi</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitAddCalibrationForm()">Simpan & Buat Card</button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <!-- Mode Selection -->
                <div class="mode-selection">
                    <h3>Mode Operasi</h3>
                    <div class="mode-buttons">
                        <button id="modeCalibrationBtn" class="btn-mode active" onclick="setMode('calibration')">
                            Mode Kalibrasi
                        </button>
                        <button id="modeDetectionBtn" class="btn-mode" onclick="setMode('detection')">
                            Mode Deteksi
                        </button>
                    </div>

                    <!-- Input untuk Mode Kalibrasi -->
                    <div id="calibrationInputs" style="display: block;">
                        <div class="mode-input-group">
                            <label>
                                Nama / ID Orang <span class="required-mark">*</span>
                            </label>
                            <input type="text" id="driverNameInput" class="form-control" placeholder="Contoh: John Doe / D001">
                        </div>
                        <div class="mode-input-group">
                            <label>
                                Trip ID (Opsional)
                            </label>
                            <input type="text" id="tripIdInput" class="form-control" placeholder="Contoh: T001">
                        </div>
                    </div>

                    <!-- Dropdown untuk Mode Deteksi -->
                    <div id="detectionInputs" style="display: none;">
                        <div class="mode-input-group">
                            <label>
                                Pilih Kalibrasi <span class="required-mark">*</span>
                            </label>
                            <select id="calibrationSelect" class="form-control">
                                <option value="">-- Pilih Kalibrasi --</option>
                            </select>
                            <button onclick="loadCalibrations()" 
                                    class="btn btn-primary mt-2" style="font-size: 14px;">
                                🔄 Refresh List
                            </button>
                        </div>
                        
                        <!-- Video Source untuk Mode Deteksi -->
                        <div class="mode-input-group mt-3">
                            <label>Video Source untuk Deteksi <span class="required-mark">*</span></label>
                            <div class="video-source-toggle">
                                <label>
                                    <input type="radio" name="detectionVideoSource" value="upload" checked onchange="toggleDetectionVideoSource('upload')">
                                    <span>📁 Upload Video</span>
                                </label>
                                <label>
                                    <input type="radio" name="detectionVideoSource" value="stream" onchange="toggleDetectionVideoSource('stream')">
                                    <span>🔗 Stream URL</span>
                                </label>
                            </div>
                            
                            <!-- Upload Section -->
                            <div id="detectionUploadSection" style="margin-top: 15px;">
                                <div class="card-file-input-wrapper">
                                    <input type="file" id="detectionVideoFile" accept="video/*" onchange="handleDetectionVideoUpload(event)">
                                    <label for="detectionVideoFile" class="card-file-input-label">📹 Pilih Video File</label>
                                </div>
                                <div class="card-video-info" id="detectionVideoInfo" style="display: none; margin-top: 10px;">
                                    <p><strong>File:</strong> <span id="detectionVideoFileName">-</span></p>
                                    <p id="detectionVideoDurationP" style="display: none;"><strong>Duration:</strong> <span id="detectionVideoDuration">-</span></p>
                                </div>
                            </div>
                            
                            <!-- Stream Section -->
                            <div id="detectionStreamSection" style="display: none; margin-top: 15px;">
                                <div class="stream-input-group">
                                    <input type="text" id="detectionStreamUrl" placeholder="Contoh: https://example.com/stream.m3u8" class="form-control">
                                    <div style="display: flex; gap: 8px; margin-top: 10px;">
                                        <button class="btn-load-stream" onclick="loadDetectionStream()" style="flex: 1;">Load Stream</button>
                                        <button class="btn-load-stream" onclick="loadDetectionStreamIframe()" style="flex: 1; background: #8b5cf6;">Load as Iframe</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Video Player untuk Detection -->
                        <div class="video-section mt-3" id="detectionVideoSection" style="display: none; position: relative; background: #000; border-radius: 12px; overflow: hidden;">
                            <div style="position: relative; width: 100%; background: #000; aspect-ratio: 16/9;">
                                <video id="detectionVideoElement" autoplay playsinline style="width: 100%; height: 100%; display: block; object-fit: contain;"></video>
                                <canvas id="detectionCanvasElement" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; display: block;"></canvas>
                            </div>
                            <div class="status-overlay">
                                <div>Status: <span id="detectionStatus">Ready</span></div>
                                <div>EAR: <span id="detectionEAR">--</span></div>
                            </div>
                        </div>
                        
                        <!-- Controls untuk Detection -->
                        <div class="video-controls mt-3" id="detectionControls" style="display: none;">
                            <button class="btn-start" id="detectionStartBtn" onclick="startDetectionMode()">Start</button>
                            <button class="btn-stop" id="detectionStopBtn" onclick="stopDetectionMode()" disabled>Stop</button>
                        </div>
                        
                        <!-- Metrics untuk Detection -->
                        <div class="metrics-grid mt-3" id="detectionMetrics" style="display: none;">
                            <div class="metric-item" data-metric="safety-score">
                                <div class="metric-label">Safety Score</div>
                                <div class="metric-value orange" id="detectionSafetyScore">--</div>
                                <span class="status-badge safe mt-2" id="detectionStatusBadge" style="display: inline-block; width: fit-content;">Safe</span>
                            </div>
                            <div class="metric-item" data-metric="fatigue">
                                <div class="metric-label">Fatigue Score</div>
                                <div class="metric-value" id="detectionFatigueScore">--</div>
                            </div>
                            <div class="metric-item" data-metric="drift">
                                <div class="metric-label">Drift Score</div>
                                <div class="metric-value" id="detectionDriftScore">--</div>
                            </div>
                            <div class="metric-item" data-metric="perclos">
                                <div class="metric-label">PERCLOS (60s)</div>
                                <div class="metric-value" id="detectionPerclosValue">--</div>
                            </div>
                            <div class="metric-item" data-metric="blink-count">
                                <div class="metric-label">Blink Count (60s)</div>
                                <div class="metric-value" id="detectionBlinkCount">0</div>
                            </div>
                            <div class="metric-item" data-metric="microsleep">
                                <div class="metric-label">Microsleep (60s)</div>
                                <div class="metric-value" id="detectionMicrosleepCount">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- MediaPipe Face Landmarker from CDN -->
    <!-- Using correct import path based on MediaPipe package structure -->
    <!-- Note: Using ES module only (bundle script removed due to MIME type issues) -->
    <script type="module">
        (async function() {
            try {
                console.log('Loading MediaPipe tasks-vision...');
                
                // The correct import path for MediaPipe tasks-vision
                // MediaPipe exports from the wasm directory
                // Try importing from the wasm subdirectory which contains the actual implementation
                let mediapipeModule;
                
                // Try correct import paths for MediaPipe tasks-vision
                // Use main package entry point first (most reliable)
                try {
                    mediapipeModule = await import('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3');
                    console.log('Loaded from main package');
                } catch (e1) {
                    try {
                        // Try with /dist path
                        mediapipeModule = await import('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/dist/index.js');
                        console.log('Loaded from /dist/index.js');
                    } catch (e2) {
                        try {
                            // Try unpkg as fallback
                            mediapipeModule = await import('https://unpkg.com/@mediapipe/tasks-vision@0.10.3');
                            console.log('Loaded from unpkg main package');
                        } catch (e3) {
                            // Last resort: try older version
                            try {
                                mediapipeModule = await import('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.0');
                                console.log('Loaded from version 0.10.0');
                            } catch (e4) {
                                throw new Error('All MediaPipe import paths failed');
                            }
                        }
                    }
                }
                
                console.log('Module loaded, checking exports...', Object.keys(mediapipeModule));
                
                // MediaPipe exports might be in different structures
                // Try to get FilesetResolver and FaceLandmarker
                let FilesetResolver, FaceLandmarker;
                
                // Check direct exports
                if (mediapipeModule.FilesetResolver) {
                    FilesetResolver = mediapipeModule.FilesetResolver;
                    FaceLandmarker = mediapipeModule.FaceLandmarker;
                }
                // Check default export
                else if (mediapipeModule.default) {
                    if (mediapipeModule.default.FilesetResolver) {
                        FilesetResolver = mediapipeModule.default.FilesetResolver;
                        FaceLandmarker = mediapipeModule.default.FaceLandmarker;
                    } else {
                        // Default might be the namespace
                        FilesetResolver = mediapipeModule.default;
                        // Try to find FaceLandmarker
                        FaceLandmarker = mediapipeModule.FaceLandmarker || mediapipeModule.default.FaceLandmarker;
                    }
                }
                // Check if it's a namespace with vision property
                else if (mediapipeModule.vision) {
                    FilesetResolver = mediapipeModule.vision.FilesetResolver;
                    FaceLandmarker = mediapipeModule.vision.FaceLandmarker;
                }
                // Last resort: check all keys
                else {
                    const keys = Object.keys(mediapipeModule);
                    console.log('Available module keys:', keys);
                    
                    // Try to import from wasm subpath
                    try {
                        const wasmModule = await import('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/wasm');
                        if (wasmModule.FilesetResolver) {
                            FilesetResolver = wasmModule.FilesetResolver;
                            FaceLandmarker = wasmModule.FaceLandmarker;
                        } else if (wasmModule.default) {
                            FilesetResolver = wasmModule.default.FilesetResolver;
                            FaceLandmarker = wasmModule.default.FaceLandmarker;
                        }
                    } catch (wasmErr) {
                        console.warn('WASM subpath failed:', wasmErr);
                    }
                }
                
                if (!FilesetResolver) {
                    throw new Error('FilesetResolver not found. Available keys: ' + Object.keys(mediapipeModule).join(', '));
                }
                
                if (!FaceLandmarker) {
                    throw new Error('FaceLandmarker not found');
                }
                
                // Make it available globally
                window.MediaPipeVision = {
                    FilesetResolver,
                    FaceLandmarker
                };
                
                window.mediaPipeReady = true;
                console.log('MediaPipe loaded successfully!');
                
                // Dispatch event
                window.dispatchEvent(new CustomEvent('mediapipe-loaded'));
            } catch (error) {
                console.error('Failed to load MediaPipe from jsDelivr:', error);
                
                // Try fallback: unpkg with correct paths
                const unpkgPaths = [
                    'https://unpkg.com/@mediapipe/tasks-vision@0.10.3',
                    'https://unpkg.com/@mediapipe/tasks-vision@0.10.3/dist/index.js',
                    'https://unpkg.com/@mediapipe/tasks-vision@0.10.0',
                    'https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.0'
                ];
                
                for (const path of unpkgPaths) {
                    try {
                        console.log('Trying fallback path:', path);
                        const fallbackModule = await import(path);
                        
                        if (fallbackModule.FilesetResolver || fallbackModule.default?.FilesetResolver) {
                            const FilesetResolver = fallbackModule.FilesetResolver || fallbackModule.default.FilesetResolver;
                            const FaceLandmarker = fallbackModule.FaceLandmarker || fallbackModule.default.FaceLandmarker;
                            
                            window.MediaPipeVision = {
                                FilesetResolver,
                                FaceLandmarker
                            };
                            window.mediaPipeReady = true;
                            console.log('MediaPipe loaded from fallback CDN:', path);
                            window.dispatchEvent(new CustomEvent('mediapipe-loaded'));
                            return;
                        }
                    } catch (fallbackError) {
                        console.warn('Fallback path failed:', path, fallbackError.message);
                        continue;
                    }
                }
                
                // All ES module paths failed
                // Note: Bundle script fallback removed due to MIME type issues
                
                // All paths failed
                const finalError = new Error(
                    'Failed to load MediaPipe from all CDN sources. ' +
                    'This might be due to:\n' +
                    '1. Network connectivity issues\n' +
                    '2. CDN availability\n' +
                    '3. Package structure changes\n\n' +
                    'Please check the browser console for detailed error messages. ' +
                    'Last error: ' + error.message
                );
                
                console.error('All MediaPipe loading attempts failed');
                console.error('Original error:', error);
                console.error('Please check: https://www.npmjs.com/package/@mediapipe/tasks-vision for correct import paths');
                
                window.mediaPipeError = finalError;
                window.mediaPipeReady = false;
                window.dispatchEvent(new CustomEvent('mediapipe-error', { detail: finalError }));
            }
        })();
    </script>

    <script>
        // Configuration
        const FPS = 25; // 25 Hz
        const FRAME_INTERVAL = 1000 / FPS; // 40ms per frame
        const CALIBRATION_DURATION = 15 * 60 * 1000; // 15 minutes in ms
        const WINDOW_SIZE = 60 * 1000; // 60 seconds window
        const WINDOW_FRAMES = Math.floor(WINDOW_SIZE / FRAME_INTERVAL); // ~1500 frames at 25Hz
        const API_INTERVAL = 1000; // Send to API every 1 second for real-time storage
        const BLINK_MIN_DURATION = 0.06 * 1000; // 60ms
        const BLINK_MAX_DURATION = 0.35 * 1000; // 350ms
        const MICROSLEEP_DURATION = 1.4 * 1000; // 1.4 seconds

        // Eye landmark indices (6 points per eye)
        const LEFT_EYE_INDICES = [33, 160, 158, 133, 153, 144];
        const RIGHT_EYE_INDICES = [362, 385, 387, 263, 373, 380];

        // State
        let faceLandmarker = null;
        let video = null;
        let canvas = null;
        let ctx = null;
        let isRunning = false;
        let calibrationStartTime = null;
        let isCalibrating = true;
        let calibrationData = [];
        let baseline = {
            T_close: null,
            EAR_mean: null,
            EAR_sd: null
        };
        
        // Cards management
        let cardCounter = 0;
        let driverCards = {}; // Store state for each card

        // Data buffers
        let earHistory = []; // Rolling window of EAR values with timestamps
        let lastApiCall = 0;
        let driverId = 'D123';
        let tripId = 'T' + Date.now();
        let videoFile = null;
        let videoUrl = null;
        let isVideoUploaded = false;
        let videoLoopCount = 0; // Track how many times video has looped
        let lastVideoTime = 0; // Track last video time to detect loops
        let videoStartTimestamp = 0; // Track when video processing started (for monotonically increasing timestamp)
        let frameCount = 0; // Frame counter for monotonically increasing timestamp
        
        // Mode management
        let currentMode = 'calibration'; // 'calibration' or 'detection'
        let selectedCalibration = null;
        
        // Previous metrics for change detection
        let previousMetrics = {
            safety_score: null,
            status: null,
            fatigue: null,
            drift: null,
            perclos_60s: null,
            blink_60s: null,
            microsleep_60s: null
        };

        // Initialize
        async function init() {
            // Note: videoElement and canvasElement are legacy - now using card-based system
            // Only initialize MediaPipe here, video/canvas are handled per-card
            video = document.getElementById('videoElement'); // May be null - legacy support
            canvas = document.getElementById('canvasElement'); // May be null - legacy support
            
            // Only get context if canvas exists (for legacy support)
            if (canvas) {
                ctx = canvas.getContext('2d');
            }

            updateStatus('Loading MediaPipe...');

            // Wait for MediaPipe ES module to load - use event listener for better reliability
            try {
                await new Promise((resolve, reject) => {
                    // Check if already loaded
                    if (window.mediaPipeReady && window.MediaPipeVision) {
                        resolve();
                        return;
                    }
                    
                    // Check for error first
                    if (window.mediaPipeError) {
                        reject(window.mediaPipeError);
                        return;
                    }
                    
                    // Wait for load event
                    const timeout = setTimeout(() => {
                        reject(new Error('MediaPipe load timeout after 15 seconds. Check console for CDN errors.'));
                    }, 15000);
                    
                    window.addEventListener('mediapipe-loaded', () => {
                        clearTimeout(timeout);
                        resolve();
                    }, { once: true });
                    
                    window.addEventListener('mediapipe-error', (e) => {
                        clearTimeout(timeout);
                        reject(e.detail || new Error('MediaPipe loading failed'));
                    }, { once: true });
                    
                    // Also poll as fallback
                    const pollInterval = setInterval(() => {
                        if (window.mediaPipeReady && window.MediaPipeVision) {
                            clearTimeout(timeout);
                            clearInterval(pollInterval);
                            resolve();
                        }
                        if (window.mediaPipeError) {
                            clearTimeout(timeout);
                            clearInterval(pollInterval);
                            reject(window.mediaPipeError);
                        }
                    }, 100);
                });
            } catch (error) {
                updateStatus('Error: MediaPipe failed to load');
                console.error('MediaPipe loading error:', error);
                console.error('mediaPipeReady:', window.mediaPipeReady);
                console.error('MediaPipeVision:', window.MediaPipeVision);
                console.error('\nTroubleshooting:');
                console.error('1. Check browser console for 404 errors');
                console.error('2. Verify CDN is accessible: https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/');
                console.error('3. Try refreshing the page');
                console.error('4. Check network tab for failed requests');
                
                Swal.fire({
                    icon: 'error',
                    title: 'MediaPipe Gagal Dimuat',
                    html: 'MediaPipe gagal dimuat dari CDN.<br><br>' +
                          'Kemungkinan penyebab:<br>' +
                          '• Masalah koneksi internet<br>' +
                          '• CDN tidak dapat diakses<br>' +
                          '• Struktur package berubah<br><br>' +
                          'Silakan buka console browser (F12) untuk detail error.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (!window.MediaPipeVision || !window.MediaPipeVision.FilesetResolver) {
                updateStatus('Error: MediaPipe not properly initialized');
                console.error('MediaPipe library not available');
                console.error('mediaPipeReady:', window.mediaPipeReady);
                console.error('MediaPipeVision:', window.MediaPipeVision);
                return;
            }

            try {
                updateStatus('Initializing Face Landmarker...');
                
                // Initialize MediaPipe Face Landmarker
                // Use correct WASM path - try multiple CDNs
                let wasmPath = "https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/wasm";
                let vision;
                
                try {
                    vision = await window.MediaPipeVision.FilesetResolver.forVisionTasks(wasmPath);
                } catch (wasmError) {
                    console.warn('Primary WASM path failed, trying alternatives...', wasmError);
                    // Try alternative paths
                    const wasmPaths = [
                        "https://unpkg.com/@mediapipe/tasks-vision@0.10.3/wasm",
                        "https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.0/wasm",
                        "https://unpkg.com/@mediapipe/tasks-vision@0.10.0/wasm"
                    ];
                    
                    for (const path of wasmPaths) {
                        try {
                            vision = await window.MediaPipeVision.FilesetResolver.forVisionTasks(path);
                            console.log('WASM loaded from:', path);
                            break;
                        } catch (pathError) {
                            console.warn('WASM path failed:', path, pathError);
                            continue;
                        }
                    }
                    
                    if (!vision) {
                        throw new Error('Failed to load WASM from all paths');
                    }
                }

                faceLandmarker = await window.MediaPipeVision.FaceLandmarker.createFromOptions(vision, {
                    baseOptions: {
                        modelAssetPath: `https://storage.googleapis.com/mediapipe-models/face_landmarker/face_landmarker/float16/1/face_landmarker.task`,
                        delegate: "GPU"
                    },
                    outputFaceBlendshapes: false,
                    runningMode: "VIDEO",
                    numFaces: 1
                });

                updateStatus('Ready - Click Start to begin');
                console.log('Face Landmarker initialized successfully');
            } catch (error) {
                console.error('Error initializing MediaPipe:', error);
                updateStatus('Error: ' + error.message);
            }
        }

        // Calculate EAR from 6 points
        function calculateEAR(landmarks, indices) {
            // Get the 6 points
            const points = indices.map(i => ({
                x: landmarks[i].x,
                y: landmarks[i].y
            }));

            // Calculate distances
            const vertical1 = Math.sqrt(
                Math.pow(points[1].x - points[5].x, 2) + 
                Math.pow(points[1].y - points[5].y, 2)
            );
            const vertical2 = Math.sqrt(
                Math.pow(points[2].x - points[4].x, 2) + 
                Math.pow(points[2].y - points[4].y, 2)
            );
            const horizontal = Math.sqrt(
                Math.pow(points[0].x - points[3].x, 2) + 
                Math.pow(points[0].y - points[3].y, 2)
            );

            // EAR formula
            const ear = (vertical1 + vertical2) / (2.0 * horizontal);
            return ear;
        }

        // Process video frame
        async function processFrame() {
            if (!isRunning || !faceLandmarker) return;

            const now = Date.now();
            const startTimeMs = performance.now();

            // Skip if video not ready
            if (!video || (video.readyState < 2 && !videoUrl)) {
                setTimeout(processFrame, FRAME_INTERVAL);
                return;
            }

            // For uploaded video, check if video is playing
            if (videoUrl && video.paused) {
                setTimeout(processFrame, FRAME_INTERVAL);
                return;
            }

            try {
                // For MediaPipe, we need monotonically increasing timestamp
                // For uploaded video, use frame-based timestamp that always increases
                // For webcam, use performance.now()
                let videoTimeMs;
                if (videoUrl) {
                    // Use frame count * frame interval to ensure monotonically increasing
                    // This prevents timestamp mismatch when video loops
                    videoTimeMs = frameCount * FRAME_INTERVAL;
                    frameCount++;
                } else {
                    videoTimeMs = startTimeMs;
                }
                const results = faceLandmarker.detectForVideo(video, videoTimeMs);

                if (results.faceLandmarks && results.faceLandmarks.length > 0) {
                    const landmarks = results.faceLandmarks[0];

                    // Verify we have enough landmarks
                    if (landmarks.length < 400) {
                        updateStatus('Insufficient Landmarks');
                        document.getElementById('currentEAR').textContent = '--';
                        setTimeout(processFrame, FRAME_INTERVAL);
                        return;
                    }

                    // Calculate EAR for both eyes
                    const leftEAR = calculateEAR(landmarks, LEFT_EYE_INDICES);
                    const rightEAR = calculateEAR(landmarks, RIGHT_EYE_INDICES);
                    const avgEAR = (leftEAR + rightEAR) / 2.0;

                    // Validate EAR (should be positive and reasonable)
                    if (isNaN(avgEAR) || avgEAR <= 0 || avgEAR > 1.0) {
                        updateStatus('Invalid EAR');
                        document.getElementById('currentEAR').textContent = '--';
                        setTimeout(processFrame, FRAME_INTERVAL);
                        return;
                    }

                    // Draw landmarks on canvas
                    drawLandmarks(landmarks);

                    // Update current EAR display
                    document.getElementById('currentEAR').textContent = avgEAR.toFixed(4);
                    updateStatus('Face Detected');

                    // Handle calibration
                    if (isCalibrating) {
                        handleCalibration(avgEAR, now);
                    } else {
                        // Process detection
                        processDetection(avgEAR, now);
                    }
                } else {
                    // No face detected - skip frame but continue
                    updateStatus('No Face Detected');
                    document.getElementById('currentEAR').textContent = '--';
                    // Clear canvas
                    if (canvas && ctx) {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                    }
                }
            } catch (error) {
                console.error('Error processing frame:', error);
                updateStatus('Error: ' + error.message);
            }

            // Schedule next frame
            setTimeout(processFrame, FRAME_INTERVAL);
        }

        // ===== Simple Eye Landmark (dari eyepoint.blade.php) =====
        // Style sederhana dan jelas untuk eye point detection
        
        // Resize canvas agar tajam (DPR aware) + koordinat pakai CSS pixel (videoWidth/videoHeight)
        function ensureCanvasMatchesVideo(videoElement, canvasElement, ctxElement) {
            if (!videoElement || !canvasElement || !ctxElement) return { w: 0, h: 0 };

            const w = videoElement.videoWidth || 0;
            const h = videoElement.videoHeight || 0;
            if (!w || !h) return { w: 0, h: 0 };

            const dpr = window.devicePixelRatio || 1;

            // Set ukuran canvas internal (device pixel)
            const needResize =
                canvasElement.width !== Math.floor(w * dpr) ||
                canvasElement.height !== Math.floor(h * dpr);

            if (needResize) {
                canvasElement.width = Math.floor(w * dpr);
                canvasElement.height = Math.floor(h * dpr);
                // Pastikan canvas tampil sesuai ukuran video (CSS pixel)
                canvasElement.style.width = w + "px";
                canvasElement.style.height = h + "px";
            }

            // Transform agar menggambar pakai koordinat CSS pixel
            ctxElement.setTransform(dpr, 0, 0, dpr, 0, 0);

            return { w, h };
        }

        // Draw simple eye points (style dari eyepoint.blade.php)
        function drawSimpleEye(ctx, points, w, h) {
            if (!points || points.length < 2) return;

            // Style sederhana dan jelas
            ctx.strokeStyle = '#00ff00';
            ctx.lineWidth = 2;
            ctx.fillStyle = '#00ff00';

            // Draw garis penghubung antar titik
            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);
            for (let i = 1; i < points.length; i++) {
                ctx.lineTo(points[i].x, points[i].y);
            }
            ctx.closePath();
            ctx.stroke();
            
            // Draw titik-titik eye landmark
            points.forEach(p => {
                ctx.beginPath();
                ctx.arc(p.x, p.y, 3, 0, 2 * Math.PI);
                ctx.fill();
            });
        }

        // Draw landmarks
        // function drawLandmarks(landmarks) {
        //     if (!video || !canvas || !ctx) return;
            
        //     // Ensure canvas matches video dimensions
        //     if (canvas.width !== video.videoWidth || canvas.height !== video.videoHeight) {
        //         canvas.width = video.videoWidth;
        //         canvas.height = video.videoHeight;
        //     }
            
        //     ctx.clearRect(0, 0, canvas.width, canvas.height);

        //     // Draw eye landmarks - halus dan tidak mencolok
        //     // Warna hijau yang lebih soft dengan opacity
        //     ctx.strokeStyle = 'rgba(0, 255, 0, 0.7)'; // Hijau dengan opacity 70%
        //     ctx.fillStyle = 'rgba(0, 255, 0, 0.6)'; // Hijau dengan opacity 60% untuk titik
        //     ctx.lineWidth = 1.5; // Garis lebih tipis

        //     // Left eye - draw 6 titik hijau halus dengan garis penghubung
        //     try {
        //         const leftEyePoints = LEFT_EYE_INDICES.map(i => ({
        //             x: landmarks[i].x * canvas.width,
        //             y: landmarks[i].y * canvas.height
        //         }));
                
        //         // Draw garis penghubung antar titik (halus)
        //         ctx.beginPath();
        //         ctx.moveTo(leftEyePoints[0].x, leftEyePoints[0].y);
        //         for (let i = 1; i < leftEyePoints.length; i++) {
        //             ctx.lineTo(leftEyePoints[i].x, leftEyePoints[i].y);
        //         }
        //         ctx.closePath();
        //         ctx.stroke(); // Garis hijau halus mengelilingi kelopak mata
                
        //         // Draw 6 titik hijau kecil dan halus
        //         leftEyePoints.forEach(p => {
        //             ctx.beginPath();
        //             ctx.arc(p.x, p.y, 2.5, 0, 2 * Math.PI); // Titik lebih kecil (radius 2.5)
        //             ctx.fill(); // Fill hijau halus untuk titik
        //         });
        //     } catch (e) {
        //         console.warn('Error drawing left eye:', e);
        //     }

        //     // Right eye - draw 6 titik hijau halus dengan garis penghubung
        //     try {
        //         const rightEyePoints = RIGHT_EYE_INDICES.map(i => ({
        //             x: landmarks[i].x * canvas.width,
        //             y: landmarks[i].y * canvas.height
        //         }));
                
        //         // Draw garis penghubung antar titik (halus)
        //         ctx.beginPath();
        //         ctx.moveTo(rightEyePoints[0].x, rightEyePoints[0].y);
        //         for (let i = 1; i < rightEyePoints.length; i++) {
        //             ctx.lineTo(rightEyePoints[i].x, rightEyePoints[i].y);
        //         }
        //         ctx.closePath();
        //         ctx.stroke(); // Garis hijau halus mengelilingi kelopak mata
                
        //         // Draw 6 titik hijau kecil dan halus
        //         rightEyePoints.forEach(p => {
        //             ctx.beginPath();
        //             ctx.arc(p.x, p.y, 2.5, 0, 2 * Math.PI); // Titik lebih kecil (radius 2.5)
        //             ctx.fill(); // Fill hijau halus untuk titik
        //         });
        //     } catch (e) {
        //         console.warn('Error drawing right eye:', e);
        //     }
        // }
        // Draw landmarks dengan style sederhana dari eyepoint.blade.php
        function drawLandmarks(landmarks) {
            if (!video || !canvas || !ctx) return;

            const { w, h } = ensureCanvasMatchesVideo(video, canvas, ctx);
            if (!w || !h) return;

            // Clear pakai koordinat CSS pixel (karena ctx sudah setTransform(dpr,...))
            ctx.clearRect(0, 0, w, h);

            // Left eye
            try {
                const leftEyePoints = LEFT_EYE_INDICES.map(i => ({
                    x: landmarks[i].x * w,
                    y: landmarks[i].y * h
                }));
                drawSimpleEye(ctx, leftEyePoints, w, h);
            } catch (e) {
                console.warn("Error drawing left eye:", e);
            }

            // Right eye
            try {
                const rightEyePoints = RIGHT_EYE_INDICES.map(i => ({
                    x: landmarks[i].x * w,
                    y: landmarks[i].y * h
                }));
                drawSimpleEye(ctx, rightEyePoints, w, h);
            } catch (e) {
                console.warn("Error drawing right eye:", e);
            }
        }


        // Handle calibration phase
        function handleCalibration(ear, timestamp) {
            calibrationData.push({ ear, timestamp });

            // For uploaded video, track total elapsed time including loops
            let elapsed;
            if (videoUrl) {
                // Detect video loop (when currentTime goes back to near 0)
                if (video.currentTime < lastVideoTime - 1) {
                    videoLoopCount++;
                }
                lastVideoTime = video.currentTime;
                
                // Calculate total elapsed time: (loopCount * videoDuration) + currentTime
                const videoDurationMs = video.duration * 1000;
                elapsed = (videoLoopCount * videoDurationMs) + (video.currentTime * 1000);
            } else {
                elapsed = timestamp - calibrationStartTime;
            }
            
            const remaining = Math.max(0, CALIBRATION_DURATION - elapsed);
            const minutes = Math.floor(remaining / 60000);
            const seconds = Math.floor((remaining % 60000) / 1000);
            const timerText = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            // Update calibration timer for all cards
            Object.keys(driverCards).forEach(cardId => {
                const calibrationTimerEl = document.getElementById(cardId + '-calibrationTimer');
                if (calibrationTimerEl) {
                    calibrationTimerEl.textContent = timerText;
                }
            });

            if (elapsed >= CALIBRATION_DURATION) {
                // Calculate baseline
                const ears = calibrationData.map(d => d.ear);
                baseline.EAR_mean = ears.reduce((a, b) => a + b, 0) / ears.length;
                
                const variance = ears.reduce((sum, ear) => {
                    return sum + Math.pow(ear - baseline.EAR_mean, 2);
                }, 0) / ears.length;
                baseline.EAR_sd = Math.sqrt(variance);
                
                // T_close = EAR_mean - 2*EAR_sd (or use a fixed threshold)
                baseline.T_close = baseline.EAR_mean - 2 * baseline.EAR_sd;
                // Ensure T_close is reasonable (not too low)
                baseline.T_close = Math.max(baseline.T_close, baseline.EAR_mean * 0.7);

                isCalibrating = false;
                
                // Update calibration status for all cards
                Object.keys(driverCards).forEach(cardId => {
                    const calibrationStatusEl = document.getElementById(cardId + '-calibrationStatus');
                    if (calibrationStatusEl) {
                        calibrationStatusEl.classList.add('complete');
                        calibrationStatusEl.innerHTML = 
                            '<h3>Kalibrasi Selesai</h3><p>Baseline: T_close=' + baseline.T_close.toFixed(4) + 
                            ', Mean=' + baseline.EAR_mean.toFixed(4) + ', SD=' + baseline.EAR_sd.toFixed(4) + '</p>';
                    }
                });

                // Save calibration data to database
                saveCalibrationToAPI();
            }
        }

        // Process detection after calibration
        function processDetection(ear, timestamp) {
            // Add to rolling window
            earHistory.push({ ear, timestamp });

            // Remove old data outside 60s window
            const cutoff = timestamp - WINDOW_SIZE;
            earHistory = earHistory.filter(d => d.timestamp >= cutoff);

            if (earHistory.length < 10) return; // Need minimum data

            // Calculate metrics
            const metrics = calculateMetrics(earHistory, timestamp);

            // Update UI
            updateMetrics(metrics);

            // Check if metrics have changed and save to API
            if (metrics && hasMetricsChanged(metrics)) {
                sendToAPI(metrics, timestamp);
                // Update previous metrics
                previousMetrics = {
                    safety_score: metrics.safetyScore,
                    status: metrics.status,
                    fatigue: metrics.fatigue,
                    drift: metrics.drift,
                    perclos_60s: metrics.perclos,
                    blink_60s: metrics.blinkCount,
                    microsleep_60s: metrics.microsleepCount
                };
            }
        }

        // Calculate all metrics
        function calculateMetrics(history, currentTime) {
            if (!baseline.T_close || history.length === 0) {
                return null;
            }

            const windowStart = currentTime - WINDOW_SIZE;
            const windowData = history.filter(d => d.timestamp >= windowStart);

            if (windowData.length === 0) return null;

            // PERCLOS: proportion of EAR < T_close
            const belowThreshold = windowData.filter(d => d.ear < baseline.T_close).length;
            const perclos = belowThreshold / windowData.length;

            // Blink detection: transient 0.06-0.35s below T_close
            let blinkCount = 0;
            let inBlink = false;
            let blinkStart = null;

            for (let i = 0; i < windowData.length; i++) {
                const isBelow = windowData[i].ear < baseline.T_close;
                const prevIsBelow = i > 0 ? windowData[i-1].ear < baseline.T_close : false;

                if (isBelow && !prevIsBelow) {
                    // Blink start
                    inBlink = true;
                    blinkStart = windowData[i].timestamp;
                } else if (!isBelow && prevIsBelow && inBlink) {
                    // Blink end
                    const duration = windowData[i].timestamp - blinkStart;
                    if (duration >= BLINK_MIN_DURATION && duration <= BLINK_MAX_DURATION) {
                        blinkCount++;
                    }
                    inBlink = false;
                    blinkStart = null;
                }
            }
            
            // Handle blink that extends beyond window
            if (inBlink && windowData.length > 0) {
                const duration = currentTime - blinkStart;
                if (duration >= BLINK_MIN_DURATION && duration <= BLINK_MAX_DURATION) {
                    blinkCount++;
                }
            }

            // Microsleep: EAR < T_close >= 1.4s continuous
            let microsleepCount = 0;
            let inMicrosleep = false;
            let microsleepStart = null;
            let microsleepEvents = []; // Track all microsleep events

            for (let i = 0; i < windowData.length; i++) {
                const isBelow = windowData[i].ear < baseline.T_close;
                const prevIsBelow = i > 0 ? windowData[i-1].ear < baseline.T_close : false;

                if (isBelow && !prevIsBelow) {
                    // Microsleep start
                    inMicrosleep = true;
                    microsleepStart = windowData[i].timestamp;
                } else if (!isBelow && prevIsBelow && inMicrosleep) {
                    // Microsleep end
                    const duration = windowData[i].timestamp - microsleepStart;
                    if (duration >= MICROSLEEP_DURATION) {
                        microsleepEvents.push({ start: microsleepStart, end: windowData[i].timestamp });
                        microsleepCount++;
                    }
                    inMicrosleep = false;
                    microsleepStart = null;
                }
            }

            // Check if still in microsleep at end of window
            if (inMicrosleep && windowData.length > 0) {
                const duration = currentTime - microsleepStart;
                if (duration >= MICROSLEEP_DURATION) {
                    // Check if this microsleep was already counted
                    const alreadyCounted = microsleepEvents.some(e => 
                        Math.abs(e.start - microsleepStart) < 100
                    );
                    if (!alreadyCounted) {
                        microsleepCount++;
                    }
                }
            }

            // EAR Slope (linear regression)
            const n = windowData.length;
            const sumX = windowData.reduce((sum, d, idx) => sum + idx, 0);
            const sumY = windowData.reduce((sum, d) => sum + d.ear, 0);
            const sumXY = windowData.reduce((sum, d, idx) => sum + idx * d.ear, 0);
            const sumX2 = windowData.reduce((sum, d, idx) => sum + idx * idx, 0);
            const slope = (n * sumXY - sumX * sumY) / (n * sumX2 - sumX * sumX);

            // Band-out ratio: EAR outside EAR_mean ± EAR_sd
            const lowerBound = baseline.EAR_mean - baseline.EAR_sd;
            const upperBound = baseline.EAR_mean + baseline.EAR_sd;
            const outOfBand = windowData.filter(d => 
                d.ear < lowerBound || d.ear > upperBound
            ).length;
            const bandOutRatio = outOfBand / windowData.length;

            // ΔPERCLOS: change in PERCLOS between window halves
            const midPoint = Math.floor(windowData.length / 2);
            const firstHalf = windowData.slice(0, midPoint);
            const secondHalf = windowData.slice(midPoint);

            const firstHalfBelow = firstHalf.filter(d => d.ear < baseline.T_close).length;
            const secondHalfBelow = secondHalf.filter(d => d.ear < baseline.T_close).length;
            const perclosFirst = firstHalf.length > 0 ? firstHalfBelow / firstHalf.length : 0;
            const perclosSecond = secondHalf.length > 0 ? secondHalfBelow / secondHalf.length : 0;
            const deltaPerclos = perclosSecond - perclosFirst;

            // Calculate Fatigue Score with proper z-score normalization
            // σ(z_PERCLOS): standardize PERCLOS (already 0-1, treat as z-score)
            const zPerclos = Math.min(perclos, 1.0); // Clamp to [0,1]
            
            // I(microsleep): indicator function (0 or 1)
            const microsleepIndicator = microsleepCount > 0 ? 1 : 0;
            
            // σ(Δblink): normalized deviation from normal blink rate
            // Normal blink rate: 15-30 blinks/min = 0.25-0.5 blinks/sec
            const blinkRate = blinkCount / 60; // blinks per second
            const normalBlinkRate = 0.375; // mean of 0.25-0.5
            const blinkStd = 0.125; // approximate std dev
            const deltaBlinkZ = Math.abs(blinkRate - normalBlinkRate) / blinkStd;
            const sigmaDeltaBlink = Math.min(deltaBlinkZ / 3.0, 1.0); // Normalize to [0,1]
            
            // σ(denseBlink): indicator for dense blinking (>0.5 blinks/sec)
            const denseBlink = blinkRate > 0.5 ? 1 : 0;

            const fatigue = 100 * (
                0.45 * zPerclos +
                0.25 * microsleepIndicator +
                0.20 * sigmaDeltaBlink +
                0.10 * denseBlink
            );

            // Calculate Drift Score
            // σ(-slope): normalized negative slope (closing trend)
            // Slope is in EAR per frame, convert to per second
            const slopePerSec = slope * FPS; // EAR change per second
            const negSlope = Math.max(0, -slopePerSec); // Only negative slopes matter
            const sigmaNegSlope = Math.min(negSlope * 10, 1.0); // Normalize (assume max 0.1 EAR/sec decline)
            
            // bandOut: already a ratio [0,1]
            const bandOut = bandOutRatio;
            
            // ΔPERCLOS: absolute change between halves
            const absDeltaPerclos = Math.abs(deltaPerclos);

            const drift = 100 * (
                0.5 * sigmaNegSlope +
                0.3 * bandOut +
                0.2 * absDeltaPerclos
            );

            // Calculate Safety Score
            const microsleepPenalty = microsleepCount * 10; // Penalty per microsleep
            const safetyScore = Math.max(0, Math.min(100, 
                100 - (0.7 * fatigue + 0.3 * drift + microsleepPenalty)
            ));

            // Determine status
            let status = 'Safe';
            if (safetyScore < 60) {
                status = 'Attention';
            } else if (safetyScore < 80) {
                status = 'Caution';
            }

            return {
                ear: windowData[windowData.length - 1].ear,
                perclos,
                blinkCount,
                microsleepCount,
                fatigue,
                drift,
                safetyScore,
                status
            };
        }

        // Check if metrics have changed
        function hasMetricsChanged(metrics) {
            if (!metrics) return false;
            
            // If this is the first time (all previous values are null), save it
            if (previousMetrics.safety_score === null) {
                return true;
            }
            
            // Threshold for floating point comparison (to avoid saving tiny changes)
            const FLOAT_THRESHOLD = 0.01;
            
            // Helper function to compare floating point numbers
            const hasFloatChanged = (oldVal, newVal) => {
                if (oldVal === null || newVal === null) return oldVal !== newVal;
                return Math.abs(oldVal - newVal) >= FLOAT_THRESHOLD;
            };
            
            // Check if any metric has changed significantly
            const safetyScoreChanged = hasFloatChanged(previousMetrics.safety_score, metrics.safetyScore);
            const statusChanged = previousMetrics.status !== metrics.status;
            const fatigueChanged = hasFloatChanged(previousMetrics.fatigue, metrics.fatigue);
            const driftChanged = hasFloatChanged(previousMetrics.drift, metrics.drift);
            const perclosChanged = hasFloatChanged(previousMetrics.perclos_60s, metrics.perclos);
            const blinkChanged = previousMetrics.blink_60s !== metrics.blinkCount;
            const microsleepChanged = previousMetrics.microsleep_60s !== metrics.microsleepCount;
            
            const changed = safetyScoreChanged || statusChanged || fatigueChanged || 
                          driftChanged || perclosChanged || blinkChanged || microsleepChanged;
            
            // Log changes for debugging
            if (changed) {
                console.log('Metrics changed:', {
                    safetyScore: { old: previousMetrics.safety_score, new: metrics.safetyScore, changed: safetyScoreChanged },
                    status: { old: previousMetrics.status, new: metrics.status, changed: statusChanged },
                    fatigue: { old: previousMetrics.fatigue, new: metrics.fatigue, changed: fatigueChanged },
                    drift: { old: previousMetrics.drift, new: metrics.drift, changed: driftChanged },
                    perclos: { old: previousMetrics.perclos_60s, new: metrics.perclos, changed: perclosChanged },
                    blink: { old: previousMetrics.blink_60s, new: metrics.blinkCount, changed: blinkChanged },
                    microsleep: { old: previousMetrics.microsleep_60s, new: metrics.microsleepCount, changed: microsleepChanged }
                });
            }
            
            return changed;
        }

        // Update UI metrics for all cards (legacy function, kept for compatibility)
        function updateMetrics(metrics) {
            if (!metrics) return;
            // This function is kept for backward compatibility but may not be used
            // Individual card updates are handled by updateCardMetrics
        }

        // Send calibration data to Laravel API
        async function saveCalibrationToAPI() {
            if (!baseline.T_close || !calibrationStartTime) return;

            const payload = {
                driver_id: driverId,
                trip_id: tripId,
                calibration_start_time: new Date(calibrationStartTime).toISOString(),
                calibration_end_time: new Date(Date.now()).toISOString(),
                t_close: baseline.T_close,
                ear_mean: baseline.EAR_mean,
                ear_sd: baseline.EAR_sd,
                data_points_count: calibrationData.length,
                notes: 'Auto-calibration completed'
            };

            try {
                const response = await fetch('/api/dms/calibration', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Calibration API error:', errorText);
                } else {
                    console.log('Calibration data saved successfully');
                }
            } catch (error) {
                console.error('Failed to save calibration to API:', error);
            }
        }

        // Send data to Laravel API
        async function sendToAPI(metrics, timestamp) {
            if (!metrics) {
                console.warn('sendToAPI: No metrics provided');
                return;
            }

            // Validate required fields
            if (!driverId) {
                console.warn('sendToAPI: driver_id is required');
                return;
            }

            const payload = {
                driver_id: driverId,
                trip_id: tripId || null,
                calibration_id: selectedCalibration ? selectedCalibration.id : null,
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

            console.log('Saving metrics to database:', {
                safety_score: payload.safety_score,
                fatigue: payload.fatigue,
                drift: payload.drift,
                perclos_60s: payload.perclos_60s,
                blink_60s: payload.blink_60s,
                microsleep_60s: payload.microsleep_60s,
                status: payload.status
            });

            try {
                const response = await fetch('/api/dms/safety-score', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const responseData = await response.json();

                if (!response.ok) {
                    console.error('API error response:', responseData);
                    console.error('Failed to save metrics to database');
                } else {
                    console.log('Metrics saved successfully to database:', responseData);
                }
            } catch (error) {
                console.error('Failed to send to API:', error);
                console.error('Error details:', {
                    message: error.message,
                    stack: error.stack,
                    payload: payload
                });
            }
        }

        // Set Mode (Calibration or Detection)
        function setMode(mode) {
            currentMode = mode;
            
            // Update buttons
            document.getElementById('modeCalibrationBtn').classList.toggle('active', mode === 'calibration');
            document.getElementById('modeDetectionBtn').classList.toggle('active', mode === 'detection');
            
            // Show/hide inputs
            document.getElementById('calibrationInputs').style.display = 
                mode === 'calibration' ? 'block' : 'none';
            document.getElementById('detectionInputs').style.display = 
                mode === 'detection' ? 'block' : 'none';
            
            // Reset state
            if (mode === 'detection') {
                loadCalibrations();
                
                // Reset detection mode state
                if (detectionModeState.isRunning) {
                    stopDetectionMode();
                }
                detectionModeState.isVideoLoaded = false;
                detectionModeState.videoFile = null;
                if (detectionModeState.videoUrl) {
                    URL.revokeObjectURL(detectionModeState.videoUrl);
                    detectionModeState.videoUrl = null;
                }
                detectionModeState.streamUrl = null;
                detectionModeState.earHistory = [];
                detectionModeState.videoLoopCount = 0;
                detectionModeState.lastVideoTime = 0;
                // Jangan reset frameCount di sini - hanya reset ketika video benar-benar baru dimuat
                // frameCount harus terus increment untuk timestamp monotonically increasing
                detectionModeState.baseline = null; // Reset baseline detection mode
                selectedCalibration = null; // Reset selected calibration
                
                // Hide video section and controls
                document.getElementById('detectionVideoSection').style.display = 'none';
                document.getElementById('detectionControls').style.display = 'none';
                document.getElementById('detectionMetrics').style.display = 'none';
                document.getElementById('detectionVideoInfo').style.display = 'none';
                
                // Reset video element
                const video = document.getElementById('detectionVideoElement');
                if (video) {
                    video.src = '';
                    video.srcObject = null;
                    video.pause();
                }
                
                // Reset form
                document.getElementById('detectionVideoFile').value = '';
                document.getElementById('detectionStreamUrl').value = '';
            } else {
                // Reset untuk mode kalibrasi
                driverId = '';
                tripId = 'T' + Date.now();
                selectedCalibration = null;
                baseline = { T_close: null, EAR_mean: null, EAR_sd: null };
                isCalibrating = true;
                calibrationData = [];
                earHistory = [];
                
                // Reset calibration status for all cards
                Object.keys(driverCards).forEach(cardId => {
                    const calibrationStatusEl = document.getElementById(cardId + '-calibrationStatus');
                    if (calibrationStatusEl) {
                        calibrationStatusEl.classList.remove('complete');
                        calibrationStatusEl.innerHTML = 
                            '<h3>Kalibrasi</h3><div class="calibration-timer" id="' + cardId + '-calibrationTimer">00:00</div><p>Mengumpulkan data baseline (15 menit)</p>';
                    }
                });
            }
        }

        // Load Calibrations dari Database
        async function loadCalibrations() {
            try {
                const response = await fetch('/api/dms/calibrations', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) {
                    console.error('Failed to load calibrations');
                    const select = document.getElementById('calibrationSelect');
                    select.innerHTML = '<option value="">-- Error loading calibrations --</option>';
                    return;
                }

                const data = await response.json();
                const select = document.getElementById('calibrationSelect');
                
                // Clear options
                select.innerHTML = '<option value="">-- Pilih Kalibrasi --</option>';
                
                // Add options
                if (data.success && data.data && data.data.length > 0) {
                    data.data.forEach(cal => {
                        const option = document.createElement('option');
                        option.value = cal.id;
                        const date = new Date(cal.calibration_start_time);
                        option.textContent = `${cal.driver_id} - ${date.toLocaleDateString('id-ID')} ${date.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})} (EAR: ${parseFloat(cal.ear_mean).toFixed(4)})`;
                        option.dataset.calibration = JSON.stringify(cal);
                        select.appendChild(option);
                    });
                } else {
                    select.innerHTML = '<option value="">-- Tidak ada kalibrasi tersedia --</option>';
                }
            } catch (error) {
                console.error('Error loading calibrations:', error);
                const select = document.getElementById('calibrationSelect');
                select.innerHTML = '<option value="">-- Error loading calibrations --</option>';
            }
        }

        // Load Baseline dari Kalibrasi yang dipilih (untuk cards di atas)
        function loadBaselineFromCalibration(calibration) {
            baseline.T_close = parseFloat(calibration.t_close);
            baseline.EAR_mean = parseFloat(calibration.ear_mean);
            baseline.EAR_sd = parseFloat(calibration.ear_sd);
            
            // Set driver_id dan trip_id
            driverId = calibration.driver_id;
            tripId = calibration.trip_id || 'T' + Date.now();
            
            // Skip kalibrasi, langsung ke deteksi
            isCalibrating = false;
            
            // Update UI for all cards
            const date = new Date(calibration.calibration_start_time);
            Object.keys(driverCards).forEach(cardId => {
                const calibrationStatusEl = document.getElementById(cardId + '-calibrationStatus');
                if (calibrationStatusEl) {
                    calibrationStatusEl.classList.add('complete');
                    calibrationStatusEl.innerHTML = 
                        '<h3>Baseline Loaded</h3>' +
                        '<p><strong>Driver:</strong> ' + calibration.driver_id + '</p>' +
                        '<p><strong>Tanggal:</strong> ' + date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) + '</p>' +
                        '<p><strong>T_close:</strong> ' + baseline.T_close.toFixed(4) + '</p>' +
                        '<p><strong>EAR_mean:</strong> ' + baseline.EAR_mean.toFixed(4) + '</p>' +
                        '<p><strong>EAR_sd:</strong> ' + baseline.EAR_sd.toFixed(4) + '</p>';
                }
            });
            
            updateStatus('Baseline loaded - Ready for detection');
        }

        // Load Baseline untuk Detection Mode (hanya untuk video di section Mode Deteksi)
        function loadBaselineForDetectionMode(calibration) {
            // Set baseline khusus untuk detection mode (tidak mengupdate cards)
            detectionModeState.baseline = {
                T_close: parseFloat(calibration.t_close),
                EAR_mean: parseFloat(calibration.ear_mean),
                EAR_sd: parseFloat(calibration.ear_sd)
            };
            
            // Update UI di detection section saja
            const date = new Date(calibration.calibration_start_time);
            document.getElementById('detectionStatus').textContent = 'Baseline loaded - Ready for detection';
            
            console.log('Baseline loaded for detection mode:', detectionModeState.baseline);
        }

        // Event listener untuk calibration select
        document.addEventListener('DOMContentLoaded', function() {
            const calibrationSelect = document.getElementById('calibrationSelect');
            if (calibrationSelect) {
                calibrationSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        selectedCalibration = JSON.parse(selectedOption.dataset.calibration);
                        // Jika di detection mode, gunakan fungsi khusus untuk detection mode
                        if (currentMode === 'detection') {
                            loadBaselineForDetectionMode(selectedCalibration);
                        } else {
                            // Untuk mode lain, gunakan fungsi biasa
                            loadBaselineFromCalibration(selectedCalibration);
                        }
                    } else {
                        selectedCalibration = null;
                        // Reset baseline detection mode jika tidak ada yang dipilih
                        if (currentMode === 'detection') {
                            detectionModeState.baseline = null;
                        }
                    }
                });
            }
        });

        // State untuk Mode Detection
        let detectionModeState = {
            isRunning: false,
            videoFile: null,
            videoUrl: null,
            streamUrl: null,
            videoSource: 'upload', // 'upload' or 'stream'
            isVideoLoaded: false,
            earHistory: [],
            baseline: null, // Baseline khusus untuk detection mode (terpisah dari cards)
            previousMetrics: {
                safety_score: null,
                status: null,
                fatigue: null,
                drift: null,
                perclos_60s: null,
                blink_60s: null,
                microsleep_60s: null
            },
            videoLoopCount: 0,
            lastVideoTime: 0,
            frameCount: 0,
            useHybridMode: false
        };

        // Toggle video source untuk detection mode
        function toggleDetectionVideoSource(source) {
            detectionModeState.videoSource = source;
            const uploadSection = document.getElementById('detectionUploadSection');
            const streamSection = document.getElementById('detectionStreamSection');
            
            if (source === 'upload') {
                uploadSection.style.display = 'block';
                streamSection.style.display = 'none';
            } else {
                uploadSection.style.display = 'none';
                streamSection.style.display = 'block';
            }
            
            // Reset state
            detectionModeState.isVideoLoaded = false;
            detectionModeState.videoFile = null;
            if (detectionModeState.videoUrl) {
                URL.revokeObjectURL(detectionModeState.videoUrl);
                detectionModeState.videoUrl = null;
            }
            detectionModeState.streamUrl = null;
            
            // Hide video section
            document.getElementById('detectionVideoSection').style.display = 'none';
            document.getElementById('detectionControls').style.display = 'none';
            document.getElementById('detectionMetrics').style.display = 'none';
        }

        // Handle video upload untuk detection mode
        function handleDetectionVideoUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('video/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Tidak Valid',
                    text: 'File harus berupa video!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            detectionModeState.videoFile = file;
            
            if (detectionModeState.videoUrl) {
                URL.revokeObjectURL(detectionModeState.videoUrl);
            }

            detectionModeState.videoLoopCount = 0;
            detectionModeState.lastVideoTime = 0;
            detectionModeState.frameCount = 0;
            detectionModeState.earHistory = [];
            detectionModeState.useHybridMode = false;

            detectionModeState.videoUrl = URL.createObjectURL(file);
            const video = document.getElementById('detectionVideoElement');
            const canvas = document.getElementById('detectionCanvasElement');
            
            video.src = detectionModeState.videoUrl;
            video.srcObject = null;
            video.removeAttribute('crossorigin');

            // Show video section
            document.getElementById('detectionVideoSection').style.display = 'block';
            document.getElementById('detectionControls').style.display = 'block';

            // Update UI
            document.getElementById('detectionVideoFileName').textContent = file.name;
            document.getElementById('detectionVideoInfo').style.display = 'block';
            document.getElementById('detectionStartBtn').disabled = false;

            video.onloadedmetadata = () => {
                const duration = video.duration;
                const minutes = Math.floor(duration / 60);
                const seconds = Math.floor(duration % 60);
                document.getElementById('detectionVideoDuration').textContent = 
                    `${minutes}:${String(seconds).padStart(2, '0')}`;
                document.getElementById('detectionVideoDurationP').style.display = 'block';
                
                if (canvas) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                }
                
                document.getElementById('detectionStatus').textContent = 'Video loaded - Click Start to begin';
            };

            video.onerror = () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error loading video file',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                document.getElementById('detectionStatus').textContent = 'Error loading video';
            };

            detectionModeState.isVideoLoaded = true;
        }

        // Load stream untuk detection mode
        function loadDetectionStream() {
            const streamUrl = document.getElementById('detectionStreamUrl').value.trim();
            if (!streamUrl) {
                Swal.fire({
                    icon: 'warning',
                    title: 'URL Stream Kosong',
                    text: 'Silakan masukkan URL stream!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const video = document.getElementById('detectionVideoElement');
            const canvas = document.getElementById('detectionCanvasElement');
            
            document.getElementById('detectionStatus').textContent = 'Loading stream...';
            
            detectionModeState.streamUrl = streamUrl;
            detectionModeState.videoSource = 'stream';
            detectionModeState.useHybridMode = false;
            
            // Clean up previous
            if (detectionModeState.videoUrl) {
                URL.revokeObjectURL(detectionModeState.videoUrl);
                detectionModeState.videoUrl = null;
            }
            
            // Reset video
            video.src = '';
            video.removeAttribute('crossorigin');
            video.crossOrigin = 'anonymous';
            video.src = streamUrl;
            video.load();

            // Show video section
            document.getElementById('detectionVideoSection').style.display = 'block';
            document.getElementById('detectionControls').style.display = 'block';

            video.onloadedmetadata = () => {
                if (canvas) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                }
                document.getElementById('detectionStatus').textContent = 'Stream loaded - Click Start to begin';
                document.getElementById('detectionStartBtn').disabled = false;
                detectionModeState.isVideoLoaded = true;
            };

            video.onerror = () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error Loading Stream',
                    text: 'Gagal memuat stream. Coba gunakan "Load as Iframe" atau periksa URL.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                document.getElementById('detectionStatus').textContent = 'Error loading stream';
            };
        }

        // Load stream as iframe untuk detection mode
        function loadDetectionStreamIframe() {
            const streamUrl = document.getElementById('detectionStreamUrl').value.trim();
            if (!streamUrl) {
                Swal.fire({
                    icon: 'warning',
                    title: 'URL Stream Kosong',
                    text: 'Silakan masukkan URL stream!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                icon: 'info',
                title: 'Mode Iframe',
                text: 'Mode iframe hanya untuk preview, tidak bisa digunakan untuk deteksi. Gunakan "Load Stream" untuk deteksi.',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        }

        // Start detection mode
        async function startDetectionMode() {
            if (!selectedCalibration) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Kalibrasi Belum Dipilih',
                    text: 'Silakan pilih kalibrasi terlebih dahulu!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (!detectionModeState.isVideoLoaded) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Video Belum Dimuat',
                    text: 'Silakan upload video atau load stream terlebih dahulu!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const video = document.getElementById('detectionVideoElement');
            const canvas = document.getElementById('detectionCanvasElement');
            const ctx = canvas.getContext('2d');

            // Load baseline untuk detection mode (jika belum dimuat)
            if (!detectionModeState.baseline || !detectionModeState.baseline.T_close) {
                if (selectedCalibration) {
                    loadBaselineForDetectionMode(selectedCalibration);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kalibrasi Belum Dipilih',
                        text: 'Silakan pilih kalibrasi terlebih dahulu!',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            }

            if (video.readyState < 2) {
                await new Promise((resolve, reject) => {
                    video.addEventListener('loadedmetadata', resolve, { once: true });
                    video.addEventListener('error', reject, { once: true });
                    video.load();
                    setTimeout(() => reject(new Error('Video load timeout')), 10000);
                });
            }

            try {
                await video.play();
            } catch (error) {
                console.warn('Play error:', error);
            }

            if (canvas) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            }

            if (detectionModeState.videoUrl) {
                video.onended = () => {
                    if (detectionModeState.isRunning) {
                        video.currentTime = 0;
                        video.play();
                    }
                };
            }

            detectionModeState.isRunning = true;
            detectionModeState.earHistory = [];
            detectionModeState.videoLoopCount = 0;
            detectionModeState.lastVideoTime = 0;
            // Jangan reset frameCount - biarkan terus increment untuk timestamp monotonically increasing
            // frameCount hanya di-reset ketika video benar-benar baru dimuat, bukan ketika detection dimulai ulang
            if (detectionModeState.frameCount === undefined) {
                detectionModeState.frameCount = 0;
            }
            
            detectionModeState.previousMetrics = {
                safety_score: null,
                status: null,
                fatigue: null,
                drift: null,
                perclos_60s: null,
                blink_60s: null,
                microsleep_60s: null
            };

            document.getElementById('detectionStartBtn').disabled = true;
            document.getElementById('detectionStopBtn').disabled = false;
            document.getElementById('detectionStatus').textContent = 'Detecting...';
            document.getElementById('detectionMetrics').style.display = 'grid';

            // Start processing frames
            processDetectionFrame();
        }

        // Stop detection mode
        function stopDetectionMode() {
            detectionModeState.isRunning = false;
            
            const video = document.getElementById('detectionVideoElement');
            if (video) {
                video.pause();
            }

            document.getElementById('detectionStartBtn').disabled = false;
            document.getElementById('detectionStopBtn').disabled = true;
            document.getElementById('detectionStatus').textContent = 'Stopped';
        }

        // Process frame untuk detection mode
        async function processDetectionFrame() {
            if (!detectionModeState.isRunning || !faceLandmarker) {
                return;
            }

            const video = document.getElementById('detectionVideoElement');
            const canvas = document.getElementById('detectionCanvasElement');
            const ctx = canvas.getContext('2d');

            const now = Date.now();
            const startTimeMs = performance.now();

            if (!video || (video.readyState < 2 && !detectionModeState.videoUrl && !detectionModeState.streamUrl)) {
                setTimeout(processDetectionFrame, FRAME_INTERVAL);
                return;
            }

            if ((detectionModeState.videoUrl || detectionModeState.streamUrl) && video.paused) {
                setTimeout(processDetectionFrame, FRAME_INTERVAL);
                return;
            }

            try {
                // For MediaPipe, we need monotonically increasing timestamp
                // Always use frameCount to ensure timestamp never decreases
                let videoTimeMs;
                if (detectionModeState.videoUrl || detectionModeState.streamUrl) {
                    // Always use frameCount for monotonically increasing timestamp
                    // Never use video.currentTime as it can reset when video loops
                    videoTimeMs = detectionModeState.frameCount * FRAME_INTERVAL;
                    detectionModeState.frameCount++;
                } else {
                    videoTimeMs = startTimeMs;
                }
                
                const results = faceLandmarker.detectForVideo(video, videoTimeMs);

                if (results.faceLandmarks && results.faceLandmarks.length > 0) {
                    const landmarks = results.faceLandmarks[0];

                    if (landmarks.length < 400) {
                        document.getElementById('detectionStatus').textContent = 'Insufficient Landmarks';
                        document.getElementById('detectionEAR').textContent = '--';
                        setTimeout(processDetectionFrame, FRAME_INTERVAL);
                        return;
                    }

                    const leftEAR = calculateEAR(landmarks, LEFT_EYE_INDICES);
                    const rightEAR = calculateEAR(landmarks, RIGHT_EYE_INDICES);
                    const avgEAR = (leftEAR + rightEAR) / 2.0;

                    if (isNaN(avgEAR) || avgEAR <= 0 || avgEAR > 1.0) {
                        document.getElementById('detectionStatus').textContent = 'Invalid EAR';
                        document.getElementById('detectionEAR').textContent = '--';
                        setTimeout(processDetectionFrame, FRAME_INTERVAL);
                        return;
                    }

                    // Draw landmarks
                    drawDetectionLandmarks(landmarks, video, canvas, ctx);

                    document.getElementById('detectionEAR').textContent = avgEAR.toFixed(4);
                    document.getElementById('detectionStatus').textContent = 'Face Detected';

                    // Process detection
                    processDetectionModeDetection(avgEAR, now);
                } else {
                    document.getElementById('detectionStatus').textContent = 'No Face Detected';
                    document.getElementById('detectionEAR').textContent = '--';
                    if (canvas && ctx) {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                    }
                }
            } catch (error) {
                console.error('Error processing detection frame:', error);
                document.getElementById('detectionStatus').textContent = 'Error: ' + error.message;
            }

            setTimeout(processDetectionFrame, FRAME_INTERVAL);
        }

        // Draw landmarks untuk detection mode dengan style sederhana
        function drawDetectionLandmarks(landmarks, video, canvas, ctx) {
            if (!video || !canvas || !ctx) return;

            const { w, h } = ensureCanvasMatchesVideo(video, canvas, ctx);
            if (!w || !h) return;

            // Clear pakai koordinat CSS pixel (karena ctx sudah setTransform(dpr,...))
            ctx.clearRect(0, 0, w, h);

            // Left eye
            try {
                const leftEyePoints = LEFT_EYE_INDICES.map(i => ({
                    x: landmarks[i].x * w,
                    y: landmarks[i].y * h
                }));
                drawSimpleEye(ctx, leftEyePoints, w, h);
            } catch (e) {
                console.warn("Error drawing left eye:", e);
            }

            // Right eye
            try {
                const rightEyePoints = RIGHT_EYE_INDICES.map(i => ({
                    x: landmarks[i].x * w,
                    y: landmarks[i].y * h
                }));
                drawSimpleEye(ctx, rightEyePoints, w, h);
            } catch (e) {
                console.warn("Error drawing right eye:", e);
            }
        }

        // Process detection untuk detection mode
        function processDetectionModeDetection(ear, timestamp) {
            detectionModeState.earHistory.push({ ear, timestamp });

            const cutoff = timestamp - WINDOW_SIZE;
            detectionModeState.earHistory = detectionModeState.earHistory.filter(d => d.timestamp >= cutoff);

            if (detectionModeState.earHistory.length < 10) return;

            // Gunakan baseline detection mode (bukan baseline global)
            if (!detectionModeState.baseline || !detectionModeState.baseline.T_close) {
                console.warn('Baseline belum dimuat untuk detection mode');
                return;
            }

            const metrics = calculateMetricsForDetectionMode(detectionModeState.earHistory, timestamp, detectionModeState.baseline);

            updateDetectionMetrics(metrics);

            if (metrics && hasDetectionMetricsChanged(metrics)) {
                sendDetectionToAPI(metrics, timestamp);
                detectionModeState.previousMetrics = {
                    safety_score: metrics.safetyScore,
                    status: metrics.status,
                    fatigue: metrics.fatigue,
                    drift: metrics.drift,
                    perclos_60s: metrics.perclos,
                    blink_60s: metrics.blinkCount,
                    microsleep_60s: metrics.microsleepCount
                };
            }
        }

        // Calculate metrics untuk detection mode dengan baseline khusus
        function calculateMetricsForDetectionMode(history, currentTime, baseline) {
            if (!baseline.T_close || history.length === 0) {
                return null;
            }

            const windowStart = currentTime - WINDOW_SIZE;
            const windowData = history.filter(d => d.timestamp >= windowStart);

            if (windowData.length === 0) return null;

            // PERCLOS: proportion of EAR < T_close
            const belowThreshold = windowData.filter(d => d.ear < baseline.T_close).length;
            const perclos = belowThreshold / windowData.length;

            // Blink detection: transient 0.06-0.35s below T_close
            let blinkCount = 0;
            let inBlink = false;
            let blinkStart = null;

            for (let i = 0; i < windowData.length; i++) {
                const isBelow = windowData[i].ear < baseline.T_close;
                const prevIsBelow = i > 0 ? windowData[i-1].ear < baseline.T_close : false;

                if (isBelow && !prevIsBelow) {
                    inBlink = true;
                    blinkStart = windowData[i].timestamp;
                } else if (!isBelow && prevIsBelow && inBlink) {
                    const duration = windowData[i].timestamp - blinkStart;
                    if (duration >= BLINK_MIN_DURATION && duration <= BLINK_MAX_DURATION) {
                        blinkCount++;
                    }
                    inBlink = false;
                    blinkStart = null;
                }
            }
            
            if (inBlink && windowData.length > 0) {
                const duration = currentTime - blinkStart;
                if (duration >= BLINK_MIN_DURATION && duration <= BLINK_MAX_DURATION) {
                    blinkCount++;
                }
            }

            // Microsleep: EAR < T_close >= 1.4s continuous
            let microsleepCount = 0;
            let inMicrosleep = false;
            let microsleepStart = null;

            for (let i = 0; i < windowData.length; i++) {
                const isBelow = windowData[i].ear < baseline.T_close;
                const prevIsBelow = i > 0 ? windowData[i-1].ear < baseline.T_close : false;

                if (isBelow && !prevIsBelow) {
                    inMicrosleep = true;
                    microsleepStart = windowData[i].timestamp;
                } else if (!isBelow && prevIsBelow && inMicrosleep) {
                    const duration = windowData[i].timestamp - microsleepStart;
                    if (duration >= MICROSLEEP_DURATION) {
                        microsleepCount++;
                    }
                    inMicrosleep = false;
                    microsleepStart = null;
                }
            }

            if (inMicrosleep && windowData.length > 0) {
                const duration = currentTime - microsleepStart;
                if (duration >= MICROSLEEP_DURATION) {
                    microsleepCount++;
                }
            }

            // EAR Slope (linear regression)
            const n = windowData.length;
            const sumX = windowData.reduce((sum, d, idx) => sum + idx, 0);
            const sumY = windowData.reduce((sum, d) => sum + d.ear, 0);
            const sumXY = windowData.reduce((sum, d, idx) => sum + idx * d.ear, 0);
            const sumX2 = windowData.reduce((sum, d, idx) => sum + idx * idx, 0);
            const slope = (n * sumXY - sumX * sumY) / (n * sumX2 - sumX * sumX);

            // Band-out ratio: EAR outside EAR_mean ± EAR_sd
            const lowerBound = baseline.EAR_mean - baseline.EAR_sd;
            const upperBound = baseline.EAR_mean + baseline.EAR_sd;
            const outOfBand = windowData.filter(d => 
                d.ear < lowerBound || d.ear > upperBound
            ).length;
            const bandOutRatio = outOfBand / windowData.length;

            // ΔPERCLOS: change in PERCLOS between window halves
            const midPoint = Math.floor(windowData.length / 2);
            const firstHalf = windowData.slice(0, midPoint);
            const secondHalf = windowData.slice(midPoint);

            const firstHalfBelow = firstHalf.filter(d => d.ear < baseline.T_close).length;
            const secondHalfBelow = secondHalf.filter(d => d.ear < baseline.T_close).length;
            const perclosFirst = firstHalf.length > 0 ? firstHalfBelow / firstHalf.length : 0;
            const perclosSecond = secondHalf.length > 0 ? secondHalfBelow / secondHalf.length : 0;
            const deltaPerclos = perclosSecond - perclosFirst;

            // Calculate Fatigue Score
            const zPerclos = Math.min(perclos, 1.0);
            const microsleepIndicator = microsleepCount > 0 ? 1 : 0;
            const blinkRate = blinkCount / 60;
            const normalBlinkRate = 0.375;
            const blinkStd = 0.125;
            const deltaBlinkZ = Math.abs(blinkRate - normalBlinkRate) / blinkStd;
            const sigmaDeltaBlink = Math.min(deltaBlinkZ / 3.0, 1.0);
            const denseBlink = blinkRate > 0.5 ? 1 : 0;

            const fatigue = 100 * (
                0.45 * zPerclos +
                0.25 * microsleepIndicator +
                0.20 * sigmaDeltaBlink +
                0.10 * denseBlink
            );

            // Calculate Drift Score
            const slopePerSec = slope * FPS;
            const negSlope = Math.max(0, -slopePerSec);
            const sigmaNegSlope = Math.min(negSlope * 10, 1.0);
            const bandOut = bandOutRatio;
            const absDeltaPerclos = Math.abs(deltaPerclos);

            const drift = 100 * (
                0.5 * sigmaNegSlope +
                0.3 * bandOut +
                0.2 * absDeltaPerclos
            );

            // Calculate Safety Score
            const microsleepPenalty = microsleepCount * 10;
            const safetyScore = Math.max(0, Math.min(100, 
                100 - (0.7 * fatigue + 0.3 * drift + microsleepPenalty)
            ));

            // Determine status
            let status = 'Safe';
            if (safetyScore < 60) {
                status = 'Attention';
            } else if (safetyScore < 80) {
                status = 'Caution';
            }

            return {
                ear: windowData[windowData.length - 1].ear,
                perclos,
                blinkCount,
                microsleepCount,
                fatigue,
                drift,
                safetyScore,
                status
            };
        }

        // Check if metrics changed untuk detection mode
        function hasDetectionMetricsChanged(metrics) {
            if (!metrics) return false;
            
            if (detectionModeState.previousMetrics.safety_score === null) {
                return true;
            }
            
            const FLOAT_THRESHOLD = 0.01;
            const hasFloatChanged = (oldVal, newVal) => {
                if (oldVal === null || newVal === null) return oldVal !== newVal;
                return Math.abs(oldVal - newVal) >= FLOAT_THRESHOLD;
            };
            
            return hasFloatChanged(detectionModeState.previousMetrics.safety_score, metrics.safetyScore) ||
                   detectionModeState.previousMetrics.status !== metrics.status ||
                   hasFloatChanged(detectionModeState.previousMetrics.fatigue, metrics.fatigue) ||
                   hasFloatChanged(detectionModeState.previousMetrics.drift, metrics.drift) ||
                   hasFloatChanged(detectionModeState.previousMetrics.perclos_60s, metrics.perclos) ||
                   detectionModeState.previousMetrics.blink_60s !== metrics.blinkCount ||
                   detectionModeState.previousMetrics.microsleep_60s !== metrics.microsleepCount;
        }

        // Update metrics UI untuk detection mode
        function updateDetectionMetrics(metrics) {
            if (!metrics) return;

            const safetyScoreEl = document.getElementById('detectionSafetyScore');
            const fatigueScoreEl = document.getElementById('detectionFatigueScore');
            const driftScoreEl = document.getElementById('detectionDriftScore');
            const perclosValueEl = document.getElementById('detectionPerclosValue');
            const blinkCountEl = document.getElementById('detectionBlinkCount');
            const microsleepCountEl = document.getElementById('detectionMicrosleepCount');
            const statusBadgeEl = document.getElementById('detectionStatusBadge');

            if (safetyScoreEl) safetyScoreEl.textContent = metrics.safetyScore.toFixed(0);
            if (fatigueScoreEl) fatigueScoreEl.textContent = metrics.fatigue.toFixed(0);
            if (driftScoreEl) driftScoreEl.textContent = metrics.drift.toFixed(0);
            if (perclosValueEl) perclosValueEl.textContent = (metrics.perclos * 100).toFixed(1) + '%';
            if (blinkCountEl) blinkCountEl.textContent = metrics.blinkCount;
            if (microsleepCountEl) microsleepCountEl.textContent = metrics.microsleepCount;

            if (statusBadgeEl) {
                statusBadgeEl.className = 'status-badge';
                
                if (metrics.status === 'Safe') {
                    statusBadgeEl.classList.add('safe');
                    statusBadgeEl.textContent = 'Safe';
                } else if (metrics.status === 'Caution') {
                    statusBadgeEl.classList.add('caution');
                    statusBadgeEl.textContent = 'Caution';
                } else {
                    statusBadgeEl.classList.add('attention');
                    statusBadgeEl.textContent = 'Attention';
                }
            }
        }

        // Send detection data to API
        async function sendDetectionToAPI(metrics, timestamp) {
            if (!metrics || !selectedCalibration) return;

            const payload = {
                driver_id: selectedCalibration.driver_id,
                trip_id: selectedCalibration.trip_id || null,
                calibration_id: selectedCalibration.id,
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
                const response = await fetch('/api/dms/safety-score', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const responseData = await response.json();

                if (!response.ok) {
                    console.error('API error response:', responseData);
                } else {
                    console.log('Detection metrics saved successfully:', responseData);
                }
            } catch (error) {
                console.error('Failed to send detection to API:', error);
            }
        }

        // Update status (legacy function - for backward compatibility)
        // Note: This is for legacy single video element, now using card-based system
        function updateStatus(status) {
            const statusEl = document.getElementById('detectionStatus');
            if (statusEl) {
                statusEl.textContent = status;
            } else {
                // Element doesn't exist (card-based system), just log it
                console.log('Status (legacy):', status);
            }
        }

        // Handle video file upload
        function handleVideoUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.startsWith('video/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Tidak Valid',
                    text: 'File harus berupa video!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            videoFile = file;
            
            // Clean up previous video URL if exists
            if (videoUrl) {
                URL.revokeObjectURL(videoUrl);
            }

            // Reset video-related variables
            videoLoopCount = 0;
            lastVideoTime = 0;
            isRunning = false;
            videoStartTimestamp = 0;
            frameCount = 0;
            
            // Only reset calibration if in calibration mode
            if (currentMode === 'calibration') {
                isCalibrating = true;
                calibrationData = [];
                baseline = { T_close: null, EAR_mean: null, EAR_sd: null };
            }
            earHistory = [];

            // Create object URL for video
            videoUrl = URL.createObjectURL(file);
            video.src = videoUrl;
            video.srcObject = null; // Clear any previous stream

            // Update UI
            document.getElementById('videoFileName').textContent = file.name;
            document.getElementById('uploadSection').classList.add('has-video');
            document.getElementById('videoInfo').style.display = 'block';
            document.getElementById('startBtn').disabled = false;

            // Wait for video metadata
            video.onloadedmetadata = () => {
                const duration = video.duration;
                const minutes = Math.floor(duration / 60);
                const seconds = Math.floor(duration % 60);
                document.getElementById('videoDuration').textContent = 
                    `${minutes}:${String(seconds).padStart(2, '0')}`;
                
                // Set canvas dimensions
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                
                updateStatus('Video loaded - Click Start to begin');
            };

            video.onerror = () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error loading video file',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                updateStatus('Error loading video');
            };

            isVideoUploaded = true;
        }

        // Play video
        function playVideo() {
            if (video && videoUrl) {
                video.play();
                document.getElementById('playBtn').style.display = 'none';
                document.getElementById('pauseBtn').style.display = 'inline-block';
            }
        }

        // Pause video
        function pauseVideo() {
            if (video) {
                video.pause();
                document.getElementById('playBtn').style.display = 'inline-block';
                document.getElementById('pauseBtn').style.display = 'none';
            }
        }

        // Start detection
        async function startDetection() {
            try {
                // Check if video is uploaded
                if (!isVideoUploaded || !videoUrl) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Video Belum Diupload',
                        text: 'Silakan upload video terlebih dahulu!',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Handle mode-specific validation
                if (currentMode === 'calibration') {
                    // Mode Kalibrasi: Validasi input nama
                    const driverName = document.getElementById('driverNameInput').value.trim();
                    if (!driverName) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Data Belum Lengkap',
                            text: 'Silakan masukkan Nama / ID Orang untuk kalibrasi!',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }
                    driverId = driverName;
                    tripId = document.getElementById('tripIdInput').value.trim() || 'T' + Date.now();
                    
                    // Reset kalibrasi for all cards
                    Object.keys(driverCards).forEach(cardId => {
                        const cardState = driverCards[cardId];
                        cardState.isCalibrating = true;
                        cardState.calibrationData = [];
                        cardState.baseline = { T_close: null, EAR_mean: null, EAR_sd: null };
                        cardState.calibrationStartTime = Date.now();
                    });
                    
                    isCalibrating = true;
                    calibrationData = [];
                    baseline = { T_close: null, EAR_mean: null, EAR_sd: null };
                    calibrationStartTime = Date.now();
                    
                } else {
                    // Mode Deteksi: Validasi kalibrasi dipilih
                    if (!selectedCalibration) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Kalibrasi Belum Dipilih',
                            text: 'Silakan pilih kalibrasi terlebih dahulu!',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }
                    
                    // Baseline sudah di-load dari loadBaselineFromCalibration()
                    // Skip kalibrasi
                    isCalibrating = false;
                }

                // Ensure video is loaded - with longer timeout for streams
                const timeoutDuration = 15000; // 15 seconds for streams
                
                if (video.readyState < 2) {
                    await new Promise((resolve, reject) => {
                        let resolved = false;
                        
                        const cleanup = () => {
                            if (resolved) return;
                            resolved = true;
                            video.removeEventListener('loadedmetadata', onLoaded);
                            video.removeEventListener('canplay', onCanPlay);
                            video.removeEventListener('error', onError);
                            clearTimeout(timeoutId);
                        };
                        
                        const onLoaded = () => {
                            cleanup();
                            resolve();
                        };
                        
                        const onCanPlay = () => {
                            if (!resolved) {
                                cleanup();
                                resolve();
                            }
                        };
                        
                        const onError = (e) => {
                            cleanup();
                            reject(new Error('Video load error'));
                        };
                        
                        video.addEventListener('loadedmetadata', onLoaded, { once: true });
                        video.addEventListener('canplay', onCanPlay, { once: true });
                        video.addEventListener('error', onError, { once: true });
                        
                        video.load();
                        
                        const timeoutId = setTimeout(() => {
                            if (!resolved) {
                                cleanup();
                                if (video.readyState >= 1) {
                                    resolve(); // Video has some data, proceed
                                } else {
                                    reject(new Error(`Video load timeout after ${timeoutDuration/1000}s`));
                                }
                            }
                        }, timeoutDuration);
                    });
                }

                // Play video
                await video.play();

                // Set canvas dimensions
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                // Handle video end - loop if needed for calibration or detection
                video.onended = () => {
                    if (isRunning) {
                        if (isCalibrating) {
                            // If still calibrating, loop the video
                            video.currentTime = 0;
                            video.play();
                        } else {
                            // If detection phase, also loop to continue monitoring
                            video.currentTime = 0;
                            video.play();
                        }
                    }
                };

                isRunning = true;
                earHistory = [];
                lastApiCall = Date.now();
                videoLoopCount = 0;
                lastVideoTime = 0;
                videoStartTimestamp = performance.now();
                // Jangan reset frameCount - biarkan terus increment untuk timestamp monotonically increasing
                // frameCount hanya di-reset ketika video benar-benar baru dimuat, bukan ketika detection dimulai ulang
                if (frameCount === undefined) {
                    frameCount = 0;
                }
                
                // Reset previous metrics
                previousMetrics = {
                    safety_score: null,
                    status: null,
                    fatigue: null,
                    drift: null,
                    perclos_60s: null,
                    blink_60s: null,
                    microsleep_60s: null
                };

                document.getElementById('startBtn').disabled = true;
                document.getElementById('stopBtn').disabled = false;
                document.getElementById('playBtn').style.display = 'none';
                document.getElementById('pauseBtn').style.display = 'inline-block';

                // Update UI based on mode
                if (currentMode === 'calibration') {
                    Object.keys(driverCards).forEach(cardId => {
                        const calibrationStatusEl = document.getElementById(cardId + '-calibrationStatus');
                        if (calibrationStatusEl) {
                            calibrationStatusEl.classList.remove('complete');
                            calibrationStatusEl.classList.add('active');
                            calibrationStatusEl.innerHTML = 
                                '<h3>Kalibrasi</h3><div class="calibration-timer" id="' + cardId + '-calibrationTimer">00:00</div><p>Mengumpulkan data baseline (15 menit)</p>';
                        }
                    });
                    updateStatus('Calibrating...');
                } else {
                    updateStatus('Detecting...');
                }

                // Start processing frames
                processFrame();
            } catch (error) {
                console.error('Error starting detection:', error);
                updateStatus('Error: ' + error.message);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memulai Deteksi',
                    text: 'Tidak dapat memulai deteksi. Pastikan video sudah dimuat dengan benar.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            }
        }

        // Stop detection
        function stopDetection() {
            isRunning = false;
            
            // Stop video
            if (video) {
                video.pause();
            }

            // Stop webcam stream if exists
            if (video.srcObject) {
                const tracks = video.srcObject.getTracks();
                tracks.forEach(track => track.stop());
                video.srcObject = null;
            }

            document.getElementById('startBtn').disabled = false;
            document.getElementById('stopBtn').disabled = true;
            document.getElementById('playBtn').style.display = 'inline-block';
            document.getElementById('pauseBtn').style.display = 'none';
            updateStatus('Stopped');
        }

        // Create a new driver card
        function createDriverCard(cardId = null) {
            if (!cardId) {
                cardId = 'card-' + (++cardCounter);
            }
            
            const driverNumber = cardCounter;
            
            const card = document.createElement('div');
            card.className = 'driver-card';
            card.id = cardId;
            
            card.innerHTML = `
                <div class="driver-card-header">
                    <h3 class="driver-card-title">Driver ${driverNumber}</h3>
                    <div class="driver-card-actions">
                        <button class="btn-remove-card" onclick="removeDriverCard('${cardId}')">Hapus</button>
                    </div>
                </div>
                <div class="driver-card-body">
                    <div class="card-video-section">
                        <!-- Hidden video for detection (background processing) -->
                        <video class="card-video-element" id="${cardId}-video" autoplay playsinline style="display: none;"></video>
                        <!-- Iframe for display (what user sees) -->
                        <iframe class="card-video-iframe" id="${cardId}-iframe" allow="autoplay; fullscreen" allowfullscreen style="display: none;"></iframe>
                        <!-- Canvas overlay for detection visualization (optional) -->
                        <canvas class="card-canvas-element" id="${cardId}-canvas" style="display: none;"></canvas>
                        <!-- Display container - shows iframe or video based on mode -->
                        <div id="${cardId}-display-container" style="width: 100%; height: 100%; position: relative;">
                            <!-- This will show either iframe or video+canvas -->
                        </div>
                        <div class="card-status-overlay">
                            <div>Status: <span id="${cardId}-status">Ready</span></div>
                            <div>EAR: <span id="${cardId}-ear">--</span></div>
                            <div id="${cardId}-detection-mode" style="font-size: 10px; margin-top: 4px; opacity: 0.8;">Mode: Standby</div>
                        </div>
                    </div>

                    <div class="card-video-controls">
                        <button class="btn btn-success btn-sm" id="${cardId}-startBtn" onclick="startCardDetection('${cardId}')">Start</button>
                        <button class="btn btn-danger btn-sm" id="${cardId}-stopBtn" onclick="stopCardDetection('${cardId}')" disabled>Stop</button>
                        <button class="btn btn-primary btn-sm" id="${cardId}-playBtn" onclick="playCardVideo('${cardId}')" style="display: none;">Play</button>
                        <button class="btn btn-warning btn-sm" id="${cardId}-pauseBtn" onclick="pauseCardVideo('${cardId}')" style="display: none;">Pause</button>
                    </div>

                    <div class="card-upload-section" id="${cardId}-uploadSection">
                        <div class="video-source-toggle">
                            <label>
                                <input type="radio" name="${cardId}-videoSource" value="upload" checked onchange="toggleVideoSource('${cardId}', 'upload')">
                                <span>📁 Upload Video</span>
                            </label>
                            <label>
                                <input type="radio" name="${cardId}-videoSource" value="stream" onchange="toggleVideoSource('${cardId}', 'stream')">
                                <span>🔗 Stream URL</span>
                            </label>
                        </div>
                        
                        <div id="${cardId}-uploadSection-content">
                            <div class="card-file-input-wrapper">
                                <input type="file" id="${cardId}-videoFile" accept="video/*" onchange="handleCardVideoUpload('${cardId}', event)">
                                <label for="${cardId}-videoFile" class="card-file-input-label">📹 Pilih Video File</label>
                            </div>
                        </div>
                        
                        <div id="${cardId}-streamSection-content" style="display: none;">
                            <div class="stream-input-group">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 14px;">
                                    Stream URL (HLS, RTSP, atau Video URL)
                                </label>
                                <input type="text" id="${cardId}-streamUrl" placeholder="Contoh: https://example.com/stream.m3u8 atau rtsp://example.com/stream" class="form-control">
                                <div style="display: flex; gap: 8px; margin-top: 10px;">
                                    <button class="btn-load-stream" onclick="loadStreamUrl('${cardId}')" style="flex: 1;">Load Stream</button>
                                    <button class="btn-load-stream" onclick="loadStreamUrlIframe('${cardId}')" style="flex: 1; background: #8b5cf6;">Load as Iframe</button>
                                </div>
                                <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">
                                    <strong>Catatan:</strong> 
                                    <br>• "Load Stream" = Mencoba video element dengan deteksi wajah (direkomendasikan)
                                    <br>• "Load as Iframe" = Preview saja tanpa deteksi (jika video element gagal)
                                    <br>• Sistem akan otomatis mencoba beberapa metode untuk memastikan stream bisa dimuat
                                </p>
                            </div>
                        </div>
                        
                        <div class="card-video-info" id="${cardId}-videoInfo" style="display: none;">
                            <p><strong>Source:</strong> <span id="${cardId}-videoSourceType"></span></p>
                            <p><strong>File/URL:</strong> <span id="${cardId}-videoFileName"></span></p>
                            <p id="${cardId}-videoDurationP" style="display: none;"><strong>Duration:</strong> <span id="${cardId}-videoDuration"></span></p>
                        </div>
                    </div>

                    <div class="calibration-status" id="${cardId}-calibrationStatus">
                        <h3>Kalibrasi</h3>
                        <div class="calibration-timer" id="${cardId}-calibrationTimer">00:00</div>
                        <p>Mengumpulkan data baseline (15 menit)</p>
                    </div>

                    <div class="metrics-grid">
                        <div class="metric-item" data-metric="safety-score">
                            <div class="metric-label">Safety Score</div>
                            <div class="metric-value orange" id="${cardId}-safetyScore">--</div>
                            <span class="status-badge safe mt-2" id="${cardId}-statusBadge" style="display: inline-block; width: fit-content;">Safe</span>
                        </div>
                        <div class="metric-item" data-metric="fatigue">
                            <div class="metric-label">Fatigue Score</div>
                            <div class="metric-value" id="${cardId}-fatigueScore">--</div>
                        </div>
                        <div class="metric-item" data-metric="drift">
                            <div class="metric-label">Drift Score</div>
                            <div class="metric-value" id="${cardId}-driftScore">--</div>
                        </div>
                        <div class="metric-item" data-metric="perclos">
                            <div class="metric-label">PERCLOS (60s)</div>
                            <div class="metric-value" id="${cardId}-perclosValue">--</div>
                        </div>
                        <div class="metric-item" data-metric="blink-count">
                            <div class="metric-label">Blink Count (60s)</div>
                            <div class="metric-value" id="${cardId}-blinkCount">0</div>
                        </div>
                        <div class="metric-item" data-metric="microsleep">
                            <div class="metric-label">Microsleep (60s)</div>
                            <div class="metric-value" id="${cardId}-microsleepCount">0</div>
                        </div>
                    </div>
                </div>
            `;
            
            // Initialize card state
            driverCards[cardId] = {
                calibrationId: null, // ID from database
                driverId: null, // Driver ID from form
                tripId: null, // Trip ID from form
                baseline: { T_close: null, EAR_mean: null, EAR_sd: null },
                calibrationData: [],
                earHistory: [],
                isCalibrating: true,
                calibrationStartTime: null,
                isRunning: false,
                videoFile: null,
                videoUrl: null,
                streamUrl: null,
                blobUrl: null, // For fetch + blob method
                videoSource: 'upload', // 'upload' or 'stream'
                isVideoUploaded: false,
                isStreamLoaded: false,
                useHybridMode: false, // Iframe for display, hidden video for detection
                videoLoopCount: 0,
                lastVideoTime: 0,
                videoStartTimestamp: 0,
                frameCount: 0,
                faceLandmarker: null,
                hlsInstance: null, // For HLS streams
                previousMetrics: {
                    safety_score: null,
                    status: null,
                    fatigue: null,
                    drift: null,
                    perclos_60s: null,
                    blink_60s: null,
                    microsleep_60s: null
                }
            };
            
            return card;
        }
        
        // Show modal form for adding calibration
        function showAddCalibrationModal() {
            // Reset form
            document.getElementById('addCalibrationForm').reset();
            // Remove validation classes
            document.querySelectorAll('#addCalibrationForm .is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('addCalibrationModal'));
            modal.show();
        }

        // Submit form and create card
        async function submitAddCalibrationForm() {
            const form = document.getElementById('addCalibrationForm');
            const formData = {
                driver_id: document.getElementById('modalDriverId').value.trim(),
                trip_id: document.getElementById('modalTripId').value.trim(),
                notes: document.getElementById('modalNotes').value.trim()
            };

            // Validation
            let isValid = true;
            if (!formData.driver_id) {
                showFieldError('modalDriverId', 'Nama Driver wajib diisi');
                isValid = false;
            } else {
                clearFieldError('modalDriverId');
            }

            if (!formData.trip_id) {
                showFieldError('modalTripId', 'Unit / Trip ID wajib diisi');
                isValid = false;
            } else {
                clearFieldError('modalTripId');
            }

            if (!formData.notes) {
                showFieldError('modalNotes', 'Link Streaming wajib diisi');
                isValid = false;
            } else {
                clearFieldError('modalNotes');
            }

            if (!isValid) {
                return;
            }

            // Disable submit button
            const submitBtn = document.querySelector('#addCalibrationModal .btn-primary');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';

            // Show loading SweetAlert
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Sedang membuat kalibrasi baru',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                // Create initial calibration record
                const response = await fetch('/api/dms/calibration/create-initial', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Gagal membuat kalibrasi');
                }

                // Close loading SweetAlert
                Swal.close();

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addCalibrationModal'));
                modal.hide();

                // Create card with calibration data
                const cardId = await addDriverCardWithCalibration(result.data);

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Kalibrasi berhasil dibuat! Card baru telah ditambahkan.',
                    confirmButtonColor: '#10b981',
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true
                });

            } catch (error) {
                console.error('Error creating calibration:', error);
                // Close loading SweetAlert if still open
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Membuat Kalibrasi',
                    text: error.message || 'Terjadi kesalahan saat membuat kalibrasi.',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            } finally {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        }

        // Helper function to show field error
        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            field.classList.add('is-invalid');
            const feedback = field.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = message;
            }
        }

        // Helper function to clear field error
        function clearFieldError(fieldId) {
            const field = document.getElementById(fieldId);
            field.classList.remove('is-invalid');
        }

        // Add a new driver card with calibration data
        async function addDriverCardWithCalibration(calibrationData) {
            const container = document.getElementById('cardsContainer');
            const cardId = 'card-' + (++cardCounter);
            const card = createDriverCard(cardId);
            container.appendChild(card);

            // Store calibration data in card state
            const cardState = driverCards[cardId];
            if (cardState && calibrationData) {
                cardState.calibrationId = calibrationData.id;
                cardState.driverId = calibrationData.driver_id;
                cardState.tripId = calibrationData.trip_id;
                cardState.streamUrl = calibrationData.notes; // Store stream URL from notes
                
                // Update card title with driver name
                const cardTitle = document.querySelector(`#${cardId} .driver-card-title`);
                if (cardTitle) {
                    cardTitle.textContent = calibrationData.driver_id;
                }

                // Pre-fill stream URL if provided and switch to stream tab
                if (calibrationData.notes) {
                    const streamUrlInput = document.getElementById(cardId + '-streamUrl');
                    if (streamUrlInput) {
                        streamUrlInput.value = calibrationData.notes;
                    }

                    // Switch to stream tab
                    const streamRadio = document.querySelector(`input[name="${cardId}-videoSource"][value="stream"]`);
                    if (streamRadio) {
                        streamRadio.checked = true;
                        toggleVideoSource(cardId, 'stream');
                        
                        // Auto-load stream after a short delay to ensure UI is ready
                        setTimeout(() => {
                            // Try to load stream automatically using iframe mode
                            if (calibrationData.notes) {
                                updateCardStatus(cardId, 'Loading stream...');
                                loadStreamUrlIframe(cardId);
                            }
                        }, 500);
                    }
                }
            }

            return cardId;
        }

        // Add a new driver card (legacy function, kept for compatibility)
        function addDriverCard() {
            const container = document.getElementById('cardsContainer');
            const card = createDriverCard();
            container.appendChild(card);
        }
        
        // Toggle between upload and stream
        function toggleVideoSource(cardId, source) {
            const cardState = driverCards[cardId];
            if (!cardState) return;
            
            cardState.videoSource = source;
            
            const uploadSection = document.getElementById(cardId + '-uploadSection-content');
            const streamSection = document.getElementById(cardId + '-streamSection-content');
            
            if (source === 'upload') {
                uploadSection.style.display = 'block';
                streamSection.style.display = 'none';
            } else {
                uploadSection.style.display = 'none';
                streamSection.style.display = 'block';
            }
            
            // Reset video state
            cardState.isVideoUploaded = false;
            cardState.isStreamLoaded = false;
            cardState.videoFile = null;
            cardState.videoUrl = null;
            cardState.streamUrl = null;
            
            // Clean up HLS instance if exists
            if (cardState.hlsInstance) {
                cardState.hlsInstance.destroy();
                cardState.hlsInstance = null;
            }
            
            // Reset UI
            const video = document.getElementById(cardId + '-video');
            if (video) {
                video.src = '';
                video.srcObject = null;
            }
            
            document.getElementById(cardId + '-startBtn').disabled = true;
            document.getElementById(cardId + '-videoInfo').style.display = 'none';
        }

        // Setup display container content
        function setupDisplayContainer(cardId, mode) {
            const displayContainer = document.getElementById(cardId + '-display-container');
            if (!displayContainer) return;
            
            const video = document.getElementById(cardId + '-video');
            const iframe = document.getElementById(cardId + '-iframe');
            const canvas = document.getElementById(cardId + '-canvas');
            
            if (mode === 'iframe') {
                // Show iframe in container
                displayContainer.innerHTML = '';
                if (iframe) {
                    iframe.style.position = 'absolute';
                    iframe.style.top = '0';
                    iframe.style.left = '0';
                    iframe.style.width = '100%';
                    iframe.style.height = '100%';
                    iframe.style.display = 'block';
                    displayContainer.appendChild(iframe);
                }
            } else if (mode === 'video') {
                // Show video + canvas in container
                displayContainer.innerHTML = '';
                const wrapper = document.createElement('div');
                wrapper.className = 'detection-video-wrapper';
                wrapper.style.width = '100%';
                wrapper.style.height = '100%';
                wrapper.style.position = 'relative';
                wrapper.style.background = '#000';
                
                // Ensure video is visible and properly styled
                if (video) {
                    // Remove inline display:none style
                    video.removeAttribute('style');
                    video.style.position = 'relative';
                    video.style.width = '100%';
                    video.style.height = '100%';
                    video.style.display = 'block';
                    video.style.opacity = '1';
                    video.style.objectFit = 'contain';
                    video.style.background = '#000';
                    video.style.margin = '0';
                    video.style.padding = '0';
                    
                    // Ensure video attributes are set
                    video.setAttribute('playsinline', '');
                    video.setAttribute('autoplay', '');
                    
                    wrapper.appendChild(video);
                }
                
                // Ensure canvas is visible and properly styled
                if (canvas) {
                    // Remove inline display:none style
                    canvas.removeAttribute('style');
                    canvas.style.position = 'absolute';
                    canvas.style.top = '0';
                    canvas.style.left = '0';
                    canvas.style.width = '100%';
                    canvas.style.height = '100%';
                    canvas.style.display = 'block';
                    canvas.style.pointerEvents = 'none';
                    canvas.style.zIndex = '10';
                    wrapper.appendChild(canvas);
                }
                
                displayContainer.appendChild(wrapper);
            }
        }

        // Load stream as iframe (for streams that work in browser but not in video element)
        // Hybrid mode: iframe for display, hidden video for detection
        function loadStreamUrlIframe(cardId) {
            const cardState = driverCards[cardId];
            if (!cardState) return;
            
            const streamUrlInput = document.getElementById(cardId + '-streamUrl');
            const streamUrl = streamUrlInput.value.trim();
            
            if (!streamUrl) {
                Swal.fire({
                    icon: 'warning',
                    title: 'URL Stream Kosong',
                    text: 'Silakan masukkan URL stream!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            const video = document.getElementById(cardId + '-video');
            const iframe = document.getElementById(cardId + '-iframe');
            const displayContainer = document.getElementById(cardId + '-display-container');
            const loadBtn = streamUrlInput.nextElementSibling?.querySelector('.btn-load-stream');
            
            if (!video || !iframe || !displayContainer) return;
            
            updateCardStatus(cardId, 'Loading stream (hybrid mode)...');
            document.getElementById(cardId + '-detection-mode').textContent = 'Mode: Loading...';
            
            // Clean up previous
            if (cardState.hlsInstance) {
                cardState.hlsInstance.destroy();
                cardState.hlsInstance = null;
            }
            
            if (cardState.videoUrl) {
                URL.revokeObjectURL(cardState.videoUrl);
                cardState.videoUrl = null;
            }
            
            // Setup iframe for display (what user sees)
            iframe.src = streamUrl;
            iframe.classList.add('active');
            setupDisplayContainer(cardId, 'iframe');
            
            // Try to setup hidden video for detection using proxy (background processing)
            // First try proxy, if fails, try direct URL
            const proxyUrl = `/dms-proxy/video-stream?url=${encodeURIComponent(streamUrl)}`;
            
            // Track if video loaded successfully (declare before use)
            let videoLoaded = false;
            let statusUpdated = false;
            let proxyTimeout = null;
            
            // Reset video (keep it hidden for background detection)
            video.src = '';
            video.removeAttribute('crossorigin');
            // Video stays hidden (position: absolute, off-screen)
            
            // Function to update status when video is ready
            const updateVideoReadyStatus = () => {
                if (statusUpdated) return;
                statusUpdated = true;
                
                const canvas = document.getElementById(cardId + '-canvas');
                if (canvas && video.videoWidth > 0 && video.videoHeight > 0) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                }
                
                document.getElementById(cardId + '-detection-mode').textContent = 'Mode: Hybrid (iframe display + video detection)';
                updateCardStatus(cardId, 'Stream loaded - Ready for detection');
                console.log('Hybrid mode ready: iframe displaying, video ready for detection');
            };
            
            // Function to handle video error
            const handleVideoError = () => {
                if (statusUpdated) return;
                statusUpdated = true;
                videoLoaded = false;
                
                console.warn('Hidden video for detection failed, detection will be disabled');
                document.getElementById(cardId + '-detection-mode').textContent = 'Mode: Iframe only (detection disabled)';
                document.getElementById(cardId + '-startBtn').disabled = true;
                updateCardStatus(cardId, 'Stream loaded (iframe only - detection unavailable)');
                console.log('Detection unavailable, but iframe display works');
            };
            
            // Clear proxy timeout when video loads
            const clearProxyTimeout = () => {
                if (proxyTimeout) {
                    clearTimeout(proxyTimeout);
                    proxyTimeout = null;
                }
            };
            
            // Try to load video via proxy for detection first
            video.src = proxyUrl;
            video.crossOrigin = 'anonymous';
            video.load();
            
            // If proxy fails, try direct URL after timeout
            proxyTimeout = setTimeout(() => {
                if (!videoLoaded && video.readyState === 0 && !statusUpdated) {
                    console.log('Proxy timeout, trying direct stream URL...');
                    video.src = '';
                    video.removeAttribute('crossorigin');
                    video.src = streamUrl;
                    video.crossOrigin = 'anonymous';
                    video.load();
                }
            }, 5000);
            
            console.log('Hybrid mode: iframe for display, hidden video for detection');
            
            // Update state
            cardState.isStreamLoaded = true;
            cardState.streamUrl = streamUrl;
            cardState.videoSource = 'stream';
            cardState.useHybridMode = true; // Flag for hybrid mode
            
            // Update UI
            document.getElementById(cardId + '-startBtn').disabled = false; // Enable detection in hybrid mode
            document.getElementById(cardId + '-videoInfo').style.display = 'block';
            document.getElementById(cardId + '-videoSourceType').textContent = 'Stream (Hybrid: iframe display + video detection)';
            document.getElementById(cardId + '-videoFileName').textContent = streamUrl.length > 60 ? streamUrl.substring(0, 60) + '...' : streamUrl;
            document.getElementById(cardId + '-videoDurationP').style.display = 'none';
            
            // Wait for video to load for detection
            video.addEventListener('loadedmetadata', () => {
                clearProxyTimeout();
                if (video.videoWidth > 0 && video.videoHeight > 0) {
                    videoLoaded = true;
                    updateVideoReadyStatus();
                }
            }, { once: true });
            
            video.addEventListener('loadeddata', () => {
                clearProxyTimeout();
                if (video.videoWidth > 0 && video.videoHeight > 0 && !videoLoaded) {
                    videoLoaded = true;
                    updateVideoReadyStatus();
                }
            }, { once: true });
            
            video.addEventListener('canplay', () => {
                clearProxyTimeout();
                if (!statusUpdated && video.readyState >= 2) {
                    videoLoaded = true;
                    updateVideoReadyStatus();
                }
            }, { once: true });
            
            video.addEventListener('error', (e) => {
                clearProxyTimeout();
                // If proxy failed, try direct URL
                if (video.src === proxyUrl || video.src.includes('/dms-proxy/')) {
                    if (!videoLoaded && !statusUpdated) {
                        console.log('Proxy failed, trying direct stream URL...');
                        video.src = '';
                        video.removeAttribute('crossorigin');
                        video.src = streamUrl;
                        video.crossOrigin = 'anonymous';
                        video.load();
                        
                        // Reset error handler for direct URL attempt
                        video.addEventListener('error', () => {
                            handleVideoError();
                        }, { once: true });
                        
                        // Also add success handlers for direct URL
                        video.addEventListener('loadedmetadata', () => {
                            clearProxyTimeout();
                            if (video.videoWidth > 0 && video.videoHeight > 0) {
                                videoLoaded = true;
                                updateVideoReadyStatus();
                            }
                        }, { once: true });
                    } else {
                        handleVideoError();
                    }
                } else {
                    handleVideoError();
                }
            }, { once: true });
            
            // Timeout: if video doesn't load in 10 seconds, assume iframe-only mode
            setTimeout(() => {
                if (!statusUpdated) {
                    console.warn('Video load timeout - using iframe-only mode');
                    if (video.readyState === 0 || video.error) {
                        handleVideoError();
                    } else if (video.videoWidth > 0 && video.videoHeight > 0) {
                        updateVideoReadyStatus();
                    } else {
                        // Video still loading but no dimensions yet - wait a bit more
                        setTimeout(() => {
                            if (!statusUpdated) {
                                if (video.videoWidth > 0 && video.videoHeight > 0) {
                                    updateVideoReadyStatus();
                                } else {
                                    handleVideoError();
                                }
                            }
                        }, 5000);
                    }
                }
            }, 10000);
            
            if (loadBtn) loadBtn.disabled = false;
            
            // Show info
            const infoMsg = document.createElement('div');
            infoMsg.className = 'alert alert-info mt-2';
            infoMsg.style.fontSize = '12px';
            infoMsg.innerHTML = '<strong>Hybrid Mode:</strong> Stream ditampilkan via iframe, deteksi wajah dilakukan di background menggunakan video element via proxy. Jika deteksi tidak berfungsi, gunakan "Load Stream" untuk mode full detection.';
            streamUrlInput.parentElement.appendChild(infoMsg);
            
            setTimeout(() => {
                if (infoMsg.parentElement) {
                    infoMsg.remove();
                }
            }, 10000);
        }

        // Load stream from URL
        async function loadStreamUrl(cardId) {
            const cardState = driverCards[cardId];
            if (!cardState) return;
            
            const streamUrlInput = document.getElementById(cardId + '-streamUrl');
            const streamUrl = streamUrlInput.value.trim();
            
            if (!streamUrl) {
                Swal.fire({
                    icon: 'warning',
                    title: 'URL Stream Kosong',
                    text: 'Silakan masukkan URL stream!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            const video = document.getElementById(cardId + '-video');
            if (!video) return;
            
            updateCardStatus(cardId, 'Loading stream...');
            
            // Disable button while loading
            const loadBtn = streamUrlInput.nextElementSibling;
            if (loadBtn) loadBtn.disabled = true;
            
            try {
                // Clean up previous stream
                if (cardState.hlsInstance) {
                    cardState.hlsInstance.destroy();
                    cardState.hlsInstance = null;
                }
                
                if (cardState.videoUrl) {
                    URL.revokeObjectURL(cardState.videoUrl);
                    cardState.videoUrl = null;
                }
                
                if (cardState.blobUrl) {
                    URL.revokeObjectURL(cardState.blobUrl);
                    cardState.blobUrl = null;
                }
                
                // Reset video
                video.src = '';
                video.srcObject = null;
                
                // Helper function to setup video success
                const setupVideoSuccess = (sourceType, isLive = false, useIframe = false) => {
                    cardState.isStreamLoaded = true;
                    cardState.streamUrl = streamUrl;
                    document.getElementById(cardId + '-startBtn').disabled = false;
                    document.getElementById(cardId + '-videoInfo').style.display = 'block';
                    document.getElementById(cardId + '-videoSourceType').textContent = sourceType;
                    document.getElementById(cardId + '-videoFileName').textContent = streamUrl.length > 60 ? streamUrl.substring(0, 60) + '...' : streamUrl;
                    document.getElementById(cardId + '-videoDurationP').style.display = isLive ? 'none' : 'block';
                    updateCardStatus(cardId, 'Stream loaded');
                    
                    const displayContainer = document.getElementById(cardId + '-display-container');
                    const iframe = document.getElementById(cardId + '-iframe');
                    
                    if (useIframe) {
                        // Hybrid mode: iframe for display, hidden video for detection
                        setupDisplayContainer(cardId, 'iframe');
                        if (iframe) {
                            iframe.src = streamUrl;
                            iframe.classList.add('active');
                        }
                        // Video stays hidden (off-screen) for background detection
                        document.getElementById(cardId + '-detection-mode').textContent = 'Mode: Hybrid (iframe display + video detection)';
                    } else {
                        // Normal mode: video element for both display and detection
                        setupDisplayContainer(cardId, 'video');
                        if (iframe) {
                            iframe.classList.remove('active');
                            iframe.src = '';
                            iframe.style.display = 'none';
                        }
                        // Video is visible in container
                        document.getElementById(cardId + '-detection-mode').textContent = 'Mode: Video element (full detection)';
                        
                        const canvas = document.getElementById(cardId + '-canvas');
                        if (canvas) {
                            const setupCanvas = () => {
                                if (video.videoWidth > 0 && video.videoHeight > 0) {
                                    canvas.width = video.videoWidth;
                                    canvas.height = video.videoHeight;
                                }
                            };
                            
                            if (video.readyState >= 2) {
                                setupCanvas();
                            } else {
                                video.addEventListener('loadedmetadata', setupCanvas, { once: true });
                            }
                        }
                    }
                };
                
                // Helper function to handle video errors
                const handleVideoError = (errorMsg) => {
                    console.error('Video error:', errorMsg);
                    updateCardStatus(cardId, 'Error: ' + errorMsg);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Loading Video Stream',
                        html: 'Error loading video stream: ' + errorMsg + '<br><br>' +
                              '<strong>Kemungkinan penyebab:</strong><br>' +
                              '1. Server tidak mengizinkan CORS (Cross-Origin Resource Sharing)<br>' +
                              '2. URL tidak valid atau tidak dapat diakses<br>' +
                              '3. Format video tidak didukung browser<br>' +
                              '4. Server memerlukan authentication/headers khusus<br><br>' +
                              '<strong>Solusi:</strong><br>' +
                              '- Coba buka URL di browser baru untuk verifikasi<br>' +
                              '- Hubungi admin server untuk mengaktifkan CORS<br>' +
                              '- Gunakan format HLS (.m3u8) jika memungkinkan',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK',
                        width: '600px'
                    });
                };
                
                // Check if it's HLS stream (.m3u8) or if URL contains specific patterns
                const isHLS = streamUrl.includes('.m3u8') || 
                            streamUrl.toLowerCase().includes('hls') || 
                            streamUrl.includes('application/vnd.apple.mpegurl') ||
                            streamUrl.includes('/hls/') ||
                            streamUrl.includes('/stream.m3u8');
                
                if (isHLS) {
                    // Load HLS.js library if not already loaded
                    if (typeof Hls === 'undefined') {
                        const script = document.createElement('script');
                        script.src = 'https://cdn.jsdelivr.net/npm/hls.js@latest';
                        document.head.appendChild(script);
                        await new Promise((resolve, reject) => {
                            script.onload = resolve;
                            script.onerror = () => reject(new Error('Failed to load HLS.js library'));
                            setTimeout(() => reject(new Error('HLS.js load timeout')), 10000);
                        });
                    }
                    
                    if (Hls.isSupported()) {
                        cardState.hlsInstance = new Hls({
                            enableWorker: true,
                            lowLatencyMode: false,
                            backBufferLength: 60,
                            xhrSetup: function (xhr, url) {
                                // Allow CORS
                                xhr.withCredentials = false;
                            }
                        });
                        
                        cardState.hlsInstance.loadSource(streamUrl);
                        cardState.hlsInstance.attachMedia(video);
                        
                        cardState.hlsInstance.on(Hls.Events.MANIFEST_PARSED, () => {
                            setupVideoSuccess('Stream (HLS)', true);
                            if (loadBtn) loadBtn.disabled = false;
                        });
                        
                        cardState.hlsInstance.on(Hls.Events.ERROR, (event, data) => {
                            console.error('HLS error:', data);
                            if (data.fatal) {
                                let errorMsg = 'Unknown error';
                                switch(data.type) {
                                    case Hls.ErrorTypes.NETWORK_ERROR:
                                        errorMsg = 'Network error - check URL and CORS settings';
                                        break;
                                    case Hls.ErrorTypes.MEDIA_ERROR:
                                        errorMsg = 'Media error - try reloading';
                                        cardState.hlsInstance.recoverMediaError();
                                        break;
                                    default:
                                        errorMsg = data.type || 'Unknown error';
                                        cardState.hlsInstance.destroy();
                                        break;
                                }
                                handleVideoError(errorMsg);
                                if (loadBtn) loadBtn.disabled = false;
                            }
                        });
                    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                        // Native HLS support (Safari)
                        video.src = streamUrl;
                        video.crossOrigin = 'anonymous';
                        
                        video.addEventListener('loadedmetadata', () => {
                            setupVideoSuccess('Stream (HLS - Native)', true);
                            if (loadBtn) loadBtn.disabled = false;
                        }, { once: true });
                        
                        video.addEventListener('error', (e) => {
                            handleVideoError('Failed to load HLS stream');
                            if (loadBtn) loadBtn.disabled = false;
                        }, { once: true });
                    } else {
                        handleVideoError('HLS not supported in this browser');
                        if (loadBtn) loadBtn.disabled = false;
                    }
                } else if (streamUrl.startsWith('rtsp://')) {
                    // RTSP stream - need to convert via proxy
                    Swal.fire({
                        icon: 'info',
                        title: 'RTSP Stream',
                        text: 'RTSP streams perlu dikonversi melalui proxy. Silakan gunakan endpoint HLS dari server.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    updateCardStatus(cardId, 'RTSP requires proxy');
                    if (loadBtn) loadBtn.disabled = false;
                } else {
                    // For URLs with query parameters (like oneVideo), try direct load first
                    // If it fails, will automatically try HLS fallback
                    // Direct video URL (mp4, webm, etc.) or unknown format - try multiple methods
                    let loadAttempted = false;
                    let fallbackToHLS = false;
                    
                    // Try to detect content type first (for URLs with query params)
                    const detectContentType = async () => {
                        try {
                            updateCardStatus(cardId, 'Detecting stream type...');
                            
                            const response = await fetch(streamUrl, { 
                                method: 'HEAD',
                                mode: 'cors',
                                cache: 'no-cache',
                                credentials: 'omit', // CRITICAL: No credentials for wildcard CORS
                                headers: {
                                    'Accept': '*/*'
                                }
                            });
                            
                            const contentType = response.headers.get('content-type') || '';
                            console.log('Content-Type detected:', contentType);
                            
                            // If it's HLS or m3u8, use HLS loader
                            if (contentType.includes('application/vnd.apple.mpegurl') || 
                                contentType.includes('application/x-mpegURL') ||
                                contentType.includes('m3u8')) {
                                console.log('Detected as HLS from Content-Type');
                                fallbackToHLS = true;
                                tryHLSFallback();
                                return;
                            }
                            
                            // If it's video/*, proceed with direct load
                            if (contentType.startsWith('video/')) {
                                console.log('Detected as direct video:', contentType);
                                tryDirectLoad();
                                return;
                            }
                            
                            // Unknown content type, try direct first, then HLS
                            console.log('Unknown content type, trying direct load first');
                            tryDirectLoad();
                        } catch (fetchError) {
                            // HEAD request failed (CORS or other), try direct load anyway
                            console.log('HEAD request failed (CORS?), trying direct load:', fetchError);
                            console.log('This is normal if server blocks CORS - will try direct video load');
                            updateCardStatus(cardId, 'CORS blocked, trying direct load...');
                            tryDirectLoad();
                        }
                    };
                    
                    // First, try to detect if it might be HLS by checking the response
                    const tryDirectLoad = () => {
                        if (loadAttempted) return;
                        loadAttempted = true;
                        
                        // Reset video element
                        video.src = '';
                        video.srcObject = null;
                        video.removeAttribute('src');
                        
                        // Set video attributes - try multiple approaches
                        video.preload = 'auto';
                        video.playsInline = true;
                        video.muted = false; // Allow audio
                        video.autoplay = false; // Don't autoplay, let user control
                        video.controls = false; // We have our own controls
                        
                        // Remove any existing source elements
                        while (video.firstChild) {
                            video.removeChild(video.firstChild);
                        }
                        
                        // Strategy: Try multiple CORS settings and methods
                        // Since iframe works, the server allows embedding
                        // Error shows server returns Access-Control-Allow-Origin: * but we're using credentials
                        // Solution: Use 'anonymous' (no credentials) or null (no CORS)
                        let corsAttempt = 0;
                        // IMPORTANT: Don't use 'use-credentials' if server returns wildcard *
                        // Only try null and 'anonymous' to avoid CORS error
                        const corsModes = [null, 'anonymous'];
                        
                        const tryLoadWithCORS = (corsMode) => {
                            console.log(`Attempting to load with CORS mode: ${corsMode || 'no CORS'}`, streamUrl);
                            
                            // Reset video completely
                            video.src = '';
                            video.srcObject = null;
                            video.removeAttribute('src');
                            
                            // Remove all source elements
                            while (video.firstChild) {
                                video.removeChild(video.firstChild);
                            }
                            
                            // Set CORS - CRITICAL: Don't use credentials with wildcard CORS
                            if (corsMode === null) {
                                video.removeAttribute('crossorigin');
                            } else {
                                video.crossOrigin = corsMode; // 'anonymous' = no credentials
                            }
                            
                            // Try direct src first (simpler, works better for some servers)
                            video.src = streamUrl;
                            video.load();
                        };
                        
                        // Try using fetch + blob URL as alternative (bypasses some CORS issues)
                        const tryFetchBlob = async () => {
                            console.log('Trying fetch + blob URL method...');
                            updateCardStatus(cardId, 'Trying fetch method...');
                            
                            try {
                                // Fetch with no credentials
                                const response = await fetch(streamUrl, {
                                    method: 'GET',
                                    mode: 'cors',
                                    credentials: 'omit', // CRITICAL: No credentials
                                    cache: 'no-cache',
                                    headers: {
                                        'Accept': 'video/*,*/*'
                                    }
                                });
                                
                                if (!response.ok) {
                                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                                }
                                
                                // Get blob
                                const blob = await response.blob();
                                const blobUrl = URL.createObjectURL(blob);
                                
                                // Reset video
                                video.src = '';
                                video.removeAttribute('crossorigin');
                                
                                // Use blob URL
                                video.src = blobUrl;
                                video.load();
                                
                                console.log('Fetch + blob URL successful');
                                
                                // Clean up blob URL after video loads
                                video.addEventListener('loadedmetadata', () => {
                                    // Keep blob URL until video is done
                                }, { once: true });
                                
                                // Store blob URL for cleanup
                                cardState.blobUrl = blobUrl;
                                
                            } catch (fetchError) {
                                console.log('Fetch method failed:', fetchError);
                                // Fall back to direct load
                                tryLoadWithCORS('anonymous');
                            }
                        };
                        
                        // Try using Laravel proxy to bypass CORS
                        const tryProxy = () => {
                            console.log('Trying Laravel proxy to bypass CORS...');
                            updateCardStatus(cardId, 'Trying proxy method...');
                            
                            // Use Laravel proxy endpoint
                            const proxyUrl = `/dms-proxy/video-stream?url=${encodeURIComponent(streamUrl)}`;
                            
                            // Reset video
                            video.src = '';
                            video.removeAttribute('crossorigin');
                            
                            // Use proxy URL (same-origin, no CORS issues)
                            video.src = proxyUrl;
                            video.load();
                            
                            console.log('Using proxy URL:', proxyUrl);
                        };
                        
                        // Strategy: Try direct first, then proxy if CORS fails
                        // Start with 'anonymous' (no credentials) - this should work with wildcard CORS
                        tryLoadWithCORS('anonymous');
                        
                        // If direct load fails due to CORS, try proxy after a delay
                        setTimeout(() => {
                            if (!cardState.isStreamLoaded && video.readyState === 0) {
                                console.log('Direct load may have CORS issue, trying proxy...');
                                tryProxy();
                            }
                        }, 5000);
                        
                        // Set timeout for loading (longer timeout for streams)
                        const loadTimeout = setTimeout(() => {
                            if (!cardState.isStreamLoaded && !fallbackToHLS) {
                                // Try as HLS as fallback
                                fallbackToHLS = true;
                                console.log('Direct load timeout, trying as HLS...');
                                tryHLSFallback();
                            } else if (!cardState.isStreamLoaded) {
                                // Last resort: try iframe
                                console.log('All methods timeout, trying iframe...');
                                tryIframeFallback();
                            }
                        }, 20000); // Increased to 20 seconds for streams
                        
                        const cleanup = () => {
                            clearTimeout(loadTimeout);
                            if (loadBtn) loadBtn.disabled = false;
                        };
                        
                        // Try to play the video to trigger loading
                        const attemptPlay = async () => {
                            try {
                                // Check if video has valid dimensions (indicates it's loaded)
                                if (video.videoWidth > 0 && video.videoHeight > 0) {
                                    const duration = video.duration;
                                    const isLive = !duration || !isFinite(duration) || isNaN(duration);
                                    setupVideoSuccess('Stream (Direct)', isLive);
                                    
                                    if (!isLive && duration) {
                                        const minutes = Math.floor(duration / 60);
                                        const seconds = Math.floor(duration % 60);
                                        document.getElementById(cardId + '-videoDuration').textContent = 
                                            `${minutes}:${String(seconds).padStart(2, '0')}`;
                                    }
                                    
                                    cleanup();
                                    return;
                                }
                                
                                // Try to play to trigger loading
                                await video.play();
                                
                                // If play succeeds, video is likely loaded
                                if (!cardState.isStreamLoaded) {
                                    // Wait a bit for video to initialize
                                    setTimeout(() => {
                                        if (video.videoWidth > 0 && video.videoHeight > 0) {
                                            const duration = video.duration;
                                            const isLive = !duration || !isFinite(duration) || isNaN(duration);
                                            setupVideoSuccess('Stream (Direct)', isLive);
                                            
                                            if (!isLive && duration) {
                                                const minutes = Math.floor(duration / 60);
                                                const seconds = Math.floor(duration % 60);
                                                document.getElementById(cardId + '-videoDuration').textContent = 
                                                    `${minutes}:${String(seconds).padStart(2, '0')}`;
                                            }
                                            
                                            cleanup();
                                        }
                                    }, 500);
                                }
                            } catch (playError) {
                                // Play error is normal if video needs user interaction or autoplay is blocked
                                console.log('Play error (may be normal, will retry):', playError);
                                
                                // If video has dimensions, it's loaded even if play failed
                                if (video.videoWidth > 0 && video.videoHeight > 0 && !cardState.isStreamLoaded) {
                                    const duration = video.duration;
                                    const isLive = !duration || !isFinite(duration) || isNaN(duration);
                                    setupVideoSuccess('Stream (Direct)', isLive);
                                    cleanup();
                                }
                            }
                        };
                        
                        video.addEventListener('loadedmetadata', () => {
                            clearTimeout(loadTimeout);
                            const duration = video.duration;
                            const isLive = !duration || !isFinite(duration) || isNaN(duration);
                            
                            setupVideoSuccess('Stream (Direct)', isLive);
                            
                            if (!isLive && duration) {
                                const minutes = Math.floor(duration / 60);
                                const seconds = Math.floor(duration % 60);
                                document.getElementById(cardId + '-videoDuration').textContent = 
                                    `${minutes}:${String(seconds).padStart(2, '0')}`;
                            }
                            
                            cleanup();
                        }, { once: true });
                        
                        video.addEventListener('canplay', () => {
                            clearTimeout(loadTimeout);
                            if (!cardState.isStreamLoaded) {
                                // If loadedmetadata didn't fire, assume it's a live stream
                                setupVideoSuccess('Stream (Live)', true);
                                cleanup();
                            }
                        }, { once: true });
                        
                        video.addEventListener('loadeddata', () => {
                            clearTimeout(loadTimeout);
                            if (!cardState.isStreamLoaded) {
                                // Video has loaded some data
                                setupVideoSuccess('Stream (Direct)', true);
                                cleanup();
                            }
                        }, { once: true });
                        
                        // Track retry attempts
                        let retryCount = 0;
                        const maxRetries = 3;
                        
                        video.addEventListener('error', (e) => {
                            retryCount++;
                            console.log(`Video error attempt ${retryCount}/${maxRetries}:`, video.error);
                            
                            const error = video.error;
                            let errorMsg = 'Unknown error';
                            let shouldRetry = false;
                            
                            if (error) {
                                switch(error.code) {
                                    case error.MEDIA_ERR_ABORTED:
                                        errorMsg = 'Loading aborted';
                                        if (retryCount < maxRetries) {
                                            shouldRetry = true;
                                        }
                                        break;
                                    case error.MEDIA_ERR_NETWORK:
                                        errorMsg = 'Network/CORS error';
                                        // Try different CORS modes first
                                        if (retryCount < corsModes.length) {
                                            console.log(`Network error, trying CORS mode ${retryCount}: ${corsModes[retryCount] || 'no CORS'}`);
                                            setTimeout(() => {
                                                tryLoadWithCORS(corsModes[retryCount]);
                                            }, 500);
                                            return; // Don't show error yet
                                        }
                                        // If CORS modes failed, try proxy
                                        if (retryCount === corsModes.length) {
                                            console.log('CORS error persists, trying proxy...');
                                            setTimeout(() => {
                                                tryProxy();
                                            }, 500);
                                            return;
                                        }
                                        // Try HLS as fallback
                                        if (!fallbackToHLS && retryCount >= maxRetries) {
                                            fallbackToHLS = true;
                                            console.log('Network/CORS error persists, trying as HLS...');
                                            setTimeout(tryHLSFallback, 500);
                                            return;
                                        }
                                        break;
                                    case error.MEDIA_ERR_DECODE:
                                        errorMsg = 'Decode error - format tidak didukung';
                                        // Try HLS as fallback
                                        if (!fallbackToHLS) {
                                            fallbackToHLS = true;
                                            console.log('Decode error, trying as HLS...');
                                            setTimeout(tryHLSFallback, 500);
                                            return;
                                        }
                                        break;
                                    case error.MEDIA_ERR_SRC_NOT_SUPPORTED:
                                        errorMsg = 'Format tidak didukung';
                                        // Try different CORS first
                                        if (retryCount < corsModes.length) {
                                            console.log(`Trying CORS mode ${retryCount} for format error`);
                                            tryLoadWithCORS(corsModes[retryCount]);
                                            return;
                                        }
                                        // Then try HLS
                                        if (!fallbackToHLS) {
                                            fallbackToHLS = true;
                                            console.log('Format not supported, trying as HLS...');
                                            setTimeout(tryHLSFallback, 500);
                                            return;
                                        }
                                        errorMsg = 'Format tidak didukung atau URL tidak valid';
                                        break;
                                    default:
                                        errorMsg = 'Error code: ' + error.code;
                                        if (retryCount < corsModes.length) {
                                            shouldRetry = true;
                                        }
                                }
                            } else {
                                // No error code but error event fired - likely CORS issue
                                errorMsg = 'CORS/Unknown error';
                                // Try different CORS modes first
                                if (retryCount < corsModes.length) {
                                    console.log(`Trying CORS mode ${retryCount} for unknown error: ${corsModes[retryCount] || 'no CORS'}`);
                                    setTimeout(() => {
                                        tryLoadWithCORS(corsModes[retryCount]);
                                    }, 500);
                                    return;
                                }
                                // Try proxy if CORS modes failed
                                if (retryCount === corsModes.length) {
                                    console.log('CORS error, trying proxy...');
                                    setTimeout(() => {
                                        tryProxy();
                                    }, 500);
                                    return;
                                }
                                
                                // If still fails, try iframe as last resort
                                if (!fallbackToHLS && retryCount >= maxRetries) {
                                    errorMsg = 'Video element gagal karena CORS - mencoba iframe (detection akan disabled)...';
                                    console.log('Video element failed after all attempts, trying iframe...');
                                    setTimeout(() => {
                                        tryIframeFallback();
                                    }, 500);
                                    return;
                                }
                            }
                            
                            // If we've exhausted all retries and fallbacks
                            if (retryCount >= maxRetries && !shouldRetry && !fallbackToHLS) {
                                clearTimeout(loadTimeout);
                                handleVideoError(errorMsg + '\n\nSemua metode CORS sudah dicoba. Gunakan "Load as Iframe" untuk preview.');
                                cleanup();
                            } else if (shouldRetry && retryCount < maxRetries) {
                                // Retry with different CORS
                                setTimeout(() => {
                                    tryLoadWithCORS(corsModes[retryCount] || null);
                                }, 1000);
                            }
                        });
                        
                        // Try to load - use load() first
                        video.load();
                        
                        // For streams that work in browser, try multiple approaches
                        // Sometimes the video needs a moment to initialize
                        let checkInterval = setInterval(() => {
                            if (cardState.isStreamLoaded) {
                                clearInterval(checkInterval);
                                return;
                            }
                            
                            // If video has loaded some data, try to setup
                            if (video.readyState >= 2) {
                                clearInterval(checkInterval);
                                attemptPlay();
                            } else if (video.readyState >= 1) {
                                // Has metadata, try to setup
                                clearInterval(checkInterval);
                                attemptPlay();
                            }
                        }, 500);
                        
                        // Clear interval after timeout
                        setTimeout(() => {
                            clearInterval(checkInterval);
                        }, 15000);
                        
                        // Also try direct play after a delay (some streams need this)
                        setTimeout(() => {
                            if (!cardState.isStreamLoaded && video.readyState >= 1) {
                                attemptPlay();
                            }
                        }, 2000);
                    };
                    
                    // Fallback: Try as HLS
                    const tryHLSFallback = async () => {
                        if (cardState.isStreamLoaded) return;
                        
                        console.log('Attempting HLS fallback for:', streamUrl);
                        updateCardStatus(cardId, 'Trying HLS format...');
                        
                        // Load HLS.js if needed
                        if (typeof Hls === 'undefined') {
                            try {
                                const script = document.createElement('script');
                                script.src = 'https://cdn.jsdelivr.net/npm/hls.js@latest';
                                document.head.appendChild(script);
                                await new Promise((resolve, reject) => {
                                    script.onload = resolve;
                                    script.onerror = () => reject(new Error('Failed to load HLS.js'));
                                    setTimeout(() => reject(new Error('HLS.js timeout')), 10000);
                                });
                            } catch (e) {
                                handleVideoError('Failed to load HLS.js library');
                                if (loadBtn) loadBtn.disabled = false;
                                return;
                            }
                        }
                        
                        if (Hls.isSupported()) {
                            // Reset video completely
                            video.src = '';
                            video.removeAttribute('src');
                            video.removeAttribute('crossorigin');
                            
                            // Set CORS to anonymous (no credentials) for HLS too
                            video.crossOrigin = 'anonymous';
                            
                            cardState.hlsInstance = new Hls({
                                enableWorker: true,
                                lowLatencyMode: false,
                                backBufferLength: 60,
                                xhrSetup: function (xhr, url) {
                                    // CRITICAL: Don't use credentials with wildcard CORS
                                    xhr.withCredentials = false;
                                    // Don't set any custom headers that might trigger preflight
                                }
                            });
                            
                            cardState.hlsInstance.loadSource(streamUrl);
                            cardState.hlsInstance.attachMedia(video);
                            
                            cardState.hlsInstance.on(Hls.Events.MANIFEST_PARSED, () => {
                                setupVideoSuccess('Stream (HLS)', true);
                                if (loadBtn) loadBtn.disabled = false;
                            });
                            
                            cardState.hlsInstance.on(Hls.Events.ERROR, (event, data) => {
                                console.error('HLS fallback error:', data);
                                if (data.fatal) {
                                    // If HLS also fails due to CORS, suggest iframe
                                    if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
                                        console.log('HLS network error (likely CORS), suggesting iframe...');
                                        setTimeout(() => {
                                            tryIframeFallback();
                                        }, 1000);
                                    } else {
                                        handleVideoError('HLS juga gagal: ' + (data.type || 'Unknown error') + '. Gunakan "Load as Iframe" untuk preview.');
                                        if (loadBtn) loadBtn.disabled = false;
                                    }
                                }
                            });
                        } else {
                            // HLS not supported, try iframe
                            console.log('HLS not supported, trying iframe...');
                            tryIframeFallback();
                        }
                    };
                    
                    // Fallback: Try iframe if video element fails
                    // Use hybrid mode: iframe for display, hidden video via proxy for detection
                    const tryIframeFallback = () => {
                        console.log('Trying hybrid mode (iframe + hidden video) for:', streamUrl);
                        updateCardStatus(cardId, 'Trying hybrid mode (iframe display + video detection)...');
                        
                        const iframe = document.getElementById(cardId + '-iframe');
                        if (!iframe) {
                            handleVideoError('Iframe element not found');
                            if (loadBtn) loadBtn.disabled = false;
                            return;
                        }
                        
                        // Setup iframe for display
                        iframe.src = streamUrl;
                        iframe.classList.add('active');
                        setupDisplayContainer(cardId, 'iframe');
                        
                        // Try to setup hidden video for detection using proxy
                        const proxyUrl = `/dms-proxy/video-stream?url=${encodeURIComponent(streamUrl)}`;
                        video.src = '';
                        video.removeAttribute('crossorigin');
                        video.src = proxyUrl;
                        video.crossOrigin = 'anonymous';
                        video.muted = true; // Mute hidden video
                        video.load();
                        
                        // Set hybrid mode flag
                        cardState.useHybridMode = true;
                        cardState.isStreamLoaded = true;
                        cardState.streamUrl = streamUrl;
                        
                        // Track if video loaded successfully
                        let fallbackVideoLoaded = false;
                        let fallbackStatusUpdated = false;
                        
                        // Function to update status when video is ready
                        const updateFallbackVideoReadyStatus = () => {
                            if (fallbackStatusUpdated) return;
                            fallbackStatusUpdated = true;
                            
                            const canvas = document.getElementById(cardId + '-canvas');
                            if (canvas && video.videoWidth > 0 && video.videoHeight > 0) {
                                canvas.width = video.videoWidth;
                                canvas.height = video.videoHeight;
                            }
                            setupVideoSuccess('Stream (Hybrid: iframe + video detection)', true, true);
                            document.getElementById(cardId + '-startBtn').disabled = false;
                            document.getElementById(cardId + '-detection-mode').textContent = 'Mode: Hybrid (iframe display + video detection)';
                            updateCardStatus(cardId, 'Stream loaded - Ready for detection');
                            if (loadBtn) loadBtn.disabled = false;
                            console.log('Hybrid mode ready: iframe displaying, video ready for detection');
                        };
                        
                        // Function to handle video error
                        const handleFallbackVideoError = () => {
                            if (fallbackStatusUpdated) return;
                            fallbackStatusUpdated = true;
                            fallbackVideoLoaded = false;
                            
                            console.warn('Hidden video for detection failed');
                            // Iframe still works for display, but detection unavailable
                            setupVideoSuccess('Stream (iframe only - detection unavailable)', true, true);
                            document.getElementById(cardId + '-startBtn').disabled = true;
                            document.getElementById(cardId + '-detection-mode').textContent = 'Mode: Iframe only (detection unavailable)';
                            updateCardStatus(cardId, 'Stream loaded (iframe only - detection unavailable)');
                            if (loadBtn) loadBtn.disabled = false;
                        };
                        
                        // Wait for video to load for detection
                        video.addEventListener('loadedmetadata', () => {
                            if (video.videoWidth > 0 && video.videoHeight > 0) {
                                fallbackVideoLoaded = true;
                                updateFallbackVideoReadyStatus();
                            }
                        }, { once: true });
                        
                        video.addEventListener('loadeddata', () => {
                            if (video.videoWidth > 0 && video.videoHeight > 0 && !fallbackVideoLoaded) {
                                fallbackVideoLoaded = true;
                                updateFallbackVideoReadyStatus();
                            }
                        }, { once: true });
                        
                        video.addEventListener('canplay', () => {
                            if (!fallbackStatusUpdated && video.readyState >= 2) {
                                fallbackVideoLoaded = true;
                                updateFallbackVideoReadyStatus();
                            }
                        }, { once: true });
                        
                        video.addEventListener('error', (e) => {
                            handleFallbackVideoError();
                        }, { once: true });
                        
                        // Timeout: if video doesn't load in 10 seconds, assume iframe-only mode
                        setTimeout(() => {
                            if (!fallbackStatusUpdated) {
                                console.warn('Video load timeout - using iframe-only mode');
                                if (video.readyState === 0 || video.error) {
                                    handleFallbackVideoError();
                                } else if (video.videoWidth > 0 && video.videoHeight > 0) {
                                    updateFallbackVideoReadyStatus();
                                } else {
                                    // Video still loading but no dimensions yet - wait a bit more
                                    setTimeout(() => {
                                        if (!fallbackStatusUpdated) {
                                            if (video.videoWidth > 0 && video.videoHeight > 0) {
                                                updateFallbackVideoReadyStatus();
                                            } else {
                                                handleFallbackVideoError();
                                            }
                                        }
                                    }, 5000);
                                }
                            }
                        }, 10000);
                    };
                    
                    // Start with content type detection, then fallback to direct load
                    detectContentType().catch(() => {
                        // If detection fails, just try direct load
                        tryDirectLoad();
                    });
                }
            } catch (error) {
                console.error('Error loading stream:', error);
                updateCardStatus(cardId, 'Error: ' + error.message);
                Swal.fire({
                    icon: 'error',
                    title: 'Error Loading Stream',
                    text: 'Error loading stream: ' + error.message,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                if (loadBtn) loadBtn.disabled = false;
            }
        }

        // Remove a driver card
        function removeDriverCard(cardId) {
            const card = document.getElementById(cardId);
            if (card) {
                const cardState = driverCards[cardId];
                
                // Stop detection if running
                if (cardState && cardState.isRunning) {
                    stopCardDetection(cardId);
                }
                
                // Clean up HLS instance
                if (cardState && cardState.hlsInstance) {
                    cardState.hlsInstance.destroy();
                    cardState.hlsInstance = null;
                }
                
                // Clean up video URL and blob URL
                if (cardState) {
                    if (cardState.videoUrl) {
                        URL.revokeObjectURL(cardState.videoUrl);
                    }
                    if (cardState.blobUrl) {
                        URL.revokeObjectURL(cardState.blobUrl);
                    }
                }
                
                // Remove card from DOM
                card.remove();
                
                // Remove from state
                delete driverCards[cardId];
            }
        }
        
        // Initialize default cards on load
        function initializeDefaultCards() {
            const container = document.getElementById('cardsContainer');
            for (let i = 0; i < 4; i++) {
                const card = createDriverCard();
                container.appendChild(card);
            }
            // Reset counter to 4 since we created 4 default cards
            cardCounter = 4;
        }

        // Handle video upload for specific card
        function handleCardVideoUpload(cardId, event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.startsWith('video/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format File Tidak Valid',
                    text: 'File harus berupa video!',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const cardState = driverCards[cardId];
            if (!cardState) return;

            cardState.videoFile = file;
            
            // Clean up previous video URL if exists
            if (cardState.videoUrl) {
                URL.revokeObjectURL(cardState.videoUrl);
            }

            // Reset video-related variables
            cardState.videoLoopCount = 0;
            cardState.lastVideoTime = 0;
            cardState.isRunning = false;
            cardState.videoStartTimestamp = 0;
            cardState.frameCount = 0;
            
            // Reset calibration if in calibration mode
            if (currentMode === 'calibration') {
                cardState.isCalibrating = true;
                cardState.calibrationData = [];
                cardState.baseline = { T_close: null, EAR_mean: null, EAR_sd: null };
            }
            cardState.earHistory = [];

            // Create object URL for video
            cardState.videoUrl = URL.createObjectURL(file);
            const video = document.getElementById(cardId + '-video');
            video.src = cardState.videoUrl;
            video.srcObject = null; // Clear any previous stream

            // Setup display container to show video
            setupDisplayContainer(cardId, 'video');
            
            // Hide iframe if it was showing
            const iframe = document.getElementById(cardId + '-iframe');
            if (iframe) {
                iframe.classList.remove('active');
                iframe.src = '';
                iframe.style.display = 'none';
            }
            
            // Update card state
            cardState.useHybridMode = false;
            cardState.videoSource = 'upload';

            // Update UI
            document.getElementById(cardId + '-videoFileName').textContent = file.name;
            document.getElementById(cardId + '-videoSourceType').textContent = 'Uploaded File';
            document.getElementById(cardId + '-uploadSection').classList.add('has-video');
            document.getElementById(cardId + '-videoInfo').style.display = 'block';
            document.getElementById(cardId + '-startBtn').disabled = false;
            document.getElementById(cardId + '-detection-mode').textContent = 'Mode: Video element (ready)';

            // Wait for video metadata
            video.onloadedmetadata = () => {
                const duration = video.duration;
                const minutes = Math.floor(duration / 60);
                const seconds = Math.floor(duration % 60);
                document.getElementById(cardId + '-videoDuration').textContent = 
                    `${minutes}:${String(seconds).padStart(2, '0')}`;
                
                // Set canvas dimensions
                const canvas = document.getElementById(cardId + '-canvas');
                if (canvas) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                }
                
                updateCardStatus(cardId, 'Video loaded - Click Start to begin');
            };

            video.onerror = () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error loading video file',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                updateCardStatus(cardId, 'Error loading video');
            };

            cardState.isVideoUploaded = true;
        }

        // Play video for specific card
        function playCardVideo(cardId) {
            const cardState = driverCards[cardId];
            if (!cardState) return;
            
            const video = document.getElementById(cardId + '-video');
            if (video && cardState.videoUrl) {
                video.play();
                document.getElementById(cardId + '-playBtn').style.display = 'none';
                document.getElementById(cardId + '-pauseBtn').style.display = 'inline-block';
            }
        }

        // Pause video for specific card
        function pauseCardVideo(cardId) {
            const cardState = driverCards[cardId];
            if (!cardState) return;
            
            const video = document.getElementById(cardId + '-video');
            if (video) {
                video.pause();
                document.getElementById(cardId + '-playBtn').style.display = 'inline-block';
                document.getElementById(cardId + '-pauseBtn').style.display = 'none';
            }
        }

        // Start detection for specific card
        async function startCardDetection(cardId) {
            const cardState = driverCards[cardId];
            if (!cardState) return;

            try {
                // Check if video is loaded (either uploaded or stream)
                const hasVideo = (cardState.isVideoUploaded && cardState.videoUrl) || 
                                (cardState.isStreamLoaded && cardState.streamUrl);
                
                if (!hasVideo) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Video Belum Dimuat',
                        text: 'Silakan upload video atau load stream terlebih dahulu!',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const video = document.getElementById(cardId + '-video');
                const canvas = document.getElementById(cardId + '-canvas');
                const ctx = canvas.getContext('2d');

                if (!video) {
                    throw new Error('Video element not found');
                }

                // Ensure video is loaded - with longer timeout for streams
                const timeoutDuration = cardState.streamUrl ? 15000 : 5000; // 15s for streams, 5s for uploaded
                
                if (video.readyState < 2) {
                    updateCardStatus(cardId, 'Waiting for video to load...');
                    
                    await new Promise((resolve, reject) => {
                        let resolved = false;
                        
                        const cleanup = () => {
                            if (resolved) return;
                            resolved = true;
                            video.removeEventListener('loadedmetadata', onLoaded);
                            video.removeEventListener('canplay', onCanPlay);
                            video.removeEventListener('loadeddata', onLoadedData);
                            video.removeEventListener('error', onError);
                            clearTimeout(timeoutId);
                        };
                        
                        const onLoaded = () => {
                            cleanup();
                            resolve();
                        };
                        
                        const onCanPlay = () => {
                            // canplay is also acceptable
                            if (!resolved) {
                                cleanup();
                                resolve();
                            }
                        };
                        
                        const onLoadedData = () => {
                            // loadeddata is also acceptable for streams
                            if (!resolved && video.videoWidth > 0) {
                                cleanup();
                                resolve();
                            }
                        };
                        
                        const onError = (e) => {
                            cleanup();
                            reject(new Error('Video load error: ' + (video.error ? video.error.message : 'Unknown error')));
                        };
                        
                        video.addEventListener('loadedmetadata', onLoaded, { once: true });
                        video.addEventListener('canplay', onCanPlay, { once: true });
                        video.addEventListener('loadeddata', onLoadedData, { once: true });
                        video.addEventListener('error', onError, { once: true });
                        
                        // Try to trigger loading
                        if (video.readyState === 0) {
                            video.load();
                        }
                        
                        // For streams, also try to play to trigger loading
                        if (cardState.streamUrl && video.readyState >= 1) {
                            video.play().catch(() => {
                                // Play error is OK, video might still load
                            });
                        }
                        
                        const timeoutId = setTimeout(() => {
                            if (!resolved) {
                                cleanup();
                                // Check if video has any data
                                if (video.readyState >= 1 || video.videoWidth > 0) {
                                    resolve(); // Video has some data, proceed
                                } else {
                                    reject(new Error(`Video load timeout after ${timeoutDuration/1000}s. Video may not be accessible or format not supported.`));
                                }
                            }
                        }, timeoutDuration);
                    });
                }

                // Verify video has valid dimensions
                if (video.videoWidth === 0 || video.videoHeight === 0) {
                    // Wait a bit more for dimensions
                    await new Promise((resolve) => {
                        const checkDimensions = () => {
                            if (video.videoWidth > 0 && video.videoHeight > 0) {
                                resolve();
                            } else {
                                setTimeout(checkDimensions, 500);
                            }
                        };
                        checkDimensions();
                    });
                }

                // Play video (muted if in hybrid mode to avoid audio conflicts)
                if (cardState.useHybridMode) {
                    video.muted = true; // Mute hidden video in hybrid mode
                }
                
                try {
                    await video.play();
                } catch (playError) {
                    // Play error might be due to autoplay policy, but video might still work
                    console.warn('Play error (may be normal):', playError);
                    // Continue anyway if video has data
                    if (video.readyState < 2) {
                        throw new Error('Video cannot play and is not loaded');
                    }
                }

                // Set canvas dimensions
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                // Handle video end - loop if needed (only for uploaded videos, not streams)
                if (cardState.videoUrl) {
                    video.onended = () => {
                        if (cardState.isRunning) {
                            video.currentTime = 0;
                            video.play();
                        }
                    };
                }

                cardState.isRunning = true;
                cardState.earHistory = [];
                cardState.videoLoopCount = 0;
                cardState.lastVideoTime = 0;
                cardState.videoStartTimestamp = performance.now();
                // Jangan reset frameCount - biarkan terus increment untuk timestamp monotonically increasing
                // frameCount hanya di-reset ketika video benar-benar baru dimuat, bukan ketika detection dimulai ulang
                if (cardState.frameCount === undefined) {
                    cardState.frameCount = 0;
                }
                
                // Set calibration start time if in calibration mode
                if (currentMode === 'calibration') {
                    cardState.calibrationStartTime = Date.now();
                    cardState.isCalibrating = true;
                    cardState.calibrationData = [];
                    cardState.baseline = { T_close: null, EAR_mean: null, EAR_sd: null };
                    
                    // Update calibration status UI
                    const calibrationStatusEl = document.getElementById(cardId + '-calibrationStatus');
                    if (calibrationStatusEl) {
                        calibrationStatusEl.classList.remove('complete');
                        calibrationStatusEl.classList.add('active');
                        calibrationStatusEl.innerHTML = 
                            '<h3>Kalibrasi</h3><div class="calibration-timer" id="' + cardId + '-calibrationTimer">00:00</div><p>Mengumpulkan data baseline (15 menit)</p>';
                    }
                }

                // Reset previous metrics
                cardState.previousMetrics = {
                    safety_score: null,
                    status: null,
                    fatigue: null,
                    drift: null,
                    perclos_60s: null,
                    blink_60s: null,
                    microsleep_60s: null
                };

                document.getElementById(cardId + '-startBtn').disabled = true;
                document.getElementById(cardId + '-stopBtn').disabled = false;
                document.getElementById(cardId + '-playBtn').style.display = 'none';
                document.getElementById(cardId + '-pauseBtn').style.display = 'inline-block';

                if (cardState.useHybridMode) {
                    updateCardStatus(cardId, 'Detecting... (Hybrid: iframe display + video detection)');
                    document.getElementById(cardId + '-detection-mode').textContent = 'Mode: Hybrid - Detecting';
                } else {
                    updateCardStatus(cardId, 'Detecting...');
                    document.getElementById(cardId + '-detection-mode').textContent = 'Mode: Video - Detecting';
                }

                // Start processing frames for this card
                processCardFrame(cardId);
            } catch (error) {
                console.error('Error starting detection for card:', cardId, error);
                updateCardStatus(cardId, 'Error: ' + error.message);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memulai Deteksi',
                    text: 'Tidak dapat memulai deteksi. Pastikan video sudah dimuat dengan benar.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            }
        }

        // Stop detection for specific card
        function stopCardDetection(cardId) {
            const cardState = driverCards[cardId];
            if (!cardState) return;

            cardState.isRunning = false;
            
            const video = document.getElementById(cardId + '-video');
            if (video) {
                video.pause();
            }

            document.getElementById(cardId + '-startBtn').disabled = false;
            document.getElementById(cardId + '-stopBtn').disabled = true;
            document.getElementById(cardId + '-playBtn').style.display = 'inline-block';
            document.getElementById(cardId + '-pauseBtn').style.display = 'none';
            updateCardStatus(cardId, 'Stopped');
        }

        // Update status for specific card
        function updateCardStatus(cardId, status) {
            const statusEl = document.getElementById(cardId + '-status');
            if (statusEl) {
                statusEl.textContent = status;
            }
        }

        // Process frame for specific card
        async function processCardFrame(cardId) {
            const cardState = driverCards[cardId];
            if (!cardState || !cardState.isRunning || !faceLandmarker) {
                return;
            }

            const video = document.getElementById(cardId + '-video');
            const canvas = document.getElementById(cardId + '-canvas');
            const ctx = canvas.getContext('2d');

            const now = Date.now();
            const startTimeMs = performance.now();

            // Skip if video not ready
            // For streams, allow readyState >= 1 (HAVE_METADATA) to proceed
            const minReadyState = cardState.streamUrl ? 1 : 2;
            if (!video || (video.readyState < minReadyState && !cardState.videoUrl && !cardState.streamUrl)) {
                setTimeout(() => processCardFrame(cardId), FRAME_INTERVAL);
                return;
            }
            
            // For streams, if video doesn't have dimensions yet, wait a bit
            if (cardState.streamUrl && (video.videoWidth === 0 || video.videoHeight === 0)) {
                // Wait a bit for dimensions
                setTimeout(() => processCardFrame(cardId), FRAME_INTERVAL * 2);
                return;
            }

            // For uploaded video or stream, check if video is playing
            // In hybrid mode, video is hidden but should still be playing for detection
            if ((cardState.videoUrl || cardState.streamUrl) && video.paused) {
                // Try to play hidden video for detection (muted to avoid audio issues)
                if (cardState.useHybridMode) {
                    video.muted = true; // Mute hidden video
                    video.play().catch(() => {
                        // Play failed, continue anyway
                    });
                }
                setTimeout(() => processCardFrame(cardId), FRAME_INTERVAL);
                return;
            }

            try {
                // For MediaPipe, we need monotonically increasing timestamp
                // Always use frameCount to ensure timestamp never decreases
                let videoTimeMs;
                if (cardState.videoUrl || cardState.streamUrl) {
                    // Always use frameCount for monotonically increasing timestamp
                    // Never use video.currentTime as it can reset when video loops
                    videoTimeMs = cardState.frameCount * FRAME_INTERVAL;
                    cardState.frameCount++;
                } else {
                    videoTimeMs = startTimeMs;
                }
                
                const results = faceLandmarker.detectForVideo(video, videoTimeMs);

                if (results.faceLandmarks && results.faceLandmarks.length > 0) {
                    const landmarks = results.faceLandmarks[0];

                    // Verify we have enough landmarks
                    if (landmarks.length < 400) {
                        updateCardStatus(cardId, 'Insufficient Landmarks');
                        document.getElementById(cardId + '-ear').textContent = '--';
                        setTimeout(() => processCardFrame(cardId), FRAME_INTERVAL);
                        return;
                    }

                    // Calculate EAR for both eyes
                    const leftEAR = calculateEAR(landmarks, LEFT_EYE_INDICES);
                    const rightEAR = calculateEAR(landmarks, RIGHT_EYE_INDICES);
                    const avgEAR = (leftEAR + rightEAR) / 2.0;

                    // Validate EAR
                    if (isNaN(avgEAR) || avgEAR <= 0 || avgEAR > 1.0) {
                        updateCardStatus(cardId, 'Invalid EAR');
                        document.getElementById(cardId + '-ear').textContent = '--';
                        setTimeout(() => processCardFrame(cardId), FRAME_INTERVAL);
                        return;
                    }

                    // Draw landmarks on canvas (only if not in hybrid mode, or if we want to show overlay)
                    // In hybrid mode, canvas is hidden but still used for processing
                    if (!cardState.useHybridMode) {
                        drawCardLandmarks(cardId, landmarks, video, canvas, ctx);
                    } else {
                        // In hybrid mode, we can still draw on hidden canvas for processing
                        // But user sees iframe, not the canvas overlay
                        drawCardLandmarks(cardId, landmarks, video, canvas, ctx);
                    }

                    // Update current EAR display
                    document.getElementById(cardId + '-ear').textContent = avgEAR.toFixed(4);
                    updateCardStatus(cardId, 'Face Detected');

                    // Handle calibration or detection
                    if (cardState.isCalibrating) {
                        handleCardCalibration(cardId, avgEAR, now);
                    } else {
                        processCardDetection(cardId, avgEAR, now);
                    }
                } else {
                    // No face detected
                    updateCardStatus(cardId, 'No Face Detected');
                    document.getElementById(cardId + '-ear').textContent = '--';
                    if (canvas && ctx) {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                    }
                }
            } catch (error) {
                console.error('Error processing frame for card:', cardId, error);
                updateCardStatus(cardId, 'Error: ' + error.message);
            }

            // Schedule next frame
            setTimeout(() => processCardFrame(cardId), FRAME_INTERVAL);
        }

        // Draw landmarks for specific card dengan style sederhana
        function drawCardLandmarks(cardId, landmarks, video, canvas, ctx) {
            if (!video || !canvas || !ctx) return;

            const { w, h } = ensureCanvasMatchesVideo(video, canvas, ctx);
            if (!w || !h) return;

            // Clear pakai koordinat CSS pixel (karena ctx sudah setTransform(dpr,...))
            ctx.clearRect(0, 0, w, h);

            // Left eye
            try {
                const leftEyePoints = LEFT_EYE_INDICES.map(i => ({
                    x: landmarks[i].x * w,
                    y: landmarks[i].y * h
                }));
                drawSimpleEye(ctx, leftEyePoints, w, h);
            } catch (e) {
                console.warn("Error drawing left eye:", e);
            }

            // Right eye
            try {
                const rightEyePoints = RIGHT_EYE_INDICES.map(i => ({
                    x: landmarks[i].x * w,
                    y: landmarks[i].y * h
                }));
                drawSimpleEye(ctx, rightEyePoints, w, h);
            } catch (e) {
                console.warn("Error drawing right eye:", e);
            }
        }

        // Handle calibration for specific card
        function handleCardCalibration(cardId, ear, timestamp) {
            const cardState = driverCards[cardId];
            if (!cardState) return;

            cardState.calibrationData.push({ ear, timestamp });

            // For uploaded video, track total elapsed time including loops
            // For streams, use real-time elapsed
            let elapsed;
            const video = document.getElementById(cardId + '-video');
            if (cardState.videoUrl && video && video.duration) {
                // Uploaded video - track loops
                if (video.currentTime < cardState.lastVideoTime - 1) {
                    cardState.videoLoopCount++;
                }
                cardState.lastVideoTime = video.currentTime;
                
                const videoDurationMs = video.duration * 1000;
                elapsed = (cardState.videoLoopCount * videoDurationMs) + (video.currentTime * 1000);
            } else if (cardState.streamUrl) {
                // Stream - use real-time elapsed
                elapsed = timestamp - (cardState.calibrationStartTime || timestamp);
            } else {
                elapsed = timestamp - (cardState.calibrationStartTime || timestamp);
            }
            
            const remaining = Math.max(0, CALIBRATION_DURATION - elapsed);
            const minutes = Math.floor(remaining / 60000);
            const seconds = Math.floor((remaining % 60000) / 1000);
            const timerText = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            // Update calibration timer
            const calibrationTimerEl = document.getElementById(cardId + '-calibrationTimer');
            if (calibrationTimerEl) {
                calibrationTimerEl.textContent = timerText;
            }

            if (elapsed >= CALIBRATION_DURATION) {
                // Calculate baseline
                const ears = cardState.calibrationData.map(d => d.ear);
                cardState.baseline.EAR_mean = ears.reduce((a, b) => a + b, 0) / ears.length;
                
                const variance = ears.reduce((sum, ear) => {
                    return sum + Math.pow(ear - cardState.baseline.EAR_mean, 2);
                }, 0) / ears.length;
                cardState.baseline.EAR_sd = Math.sqrt(variance);
                
                cardState.baseline.T_close = cardState.baseline.EAR_mean - 2 * cardState.baseline.EAR_sd;
                cardState.baseline.T_close = Math.max(cardState.baseline.T_close, cardState.baseline.EAR_mean * 0.7);

                cardState.isCalibrating = false;
                
                // Update calibration status
                const calibrationStatusEl = document.getElementById(cardId + '-calibrationStatus');
                if (calibrationStatusEl) {
                    calibrationStatusEl.classList.add('complete');
                    calibrationStatusEl.innerHTML = 
                        '<h3>Kalibrasi Selesai</h3><p>Baseline: T_close=' + cardState.baseline.T_close.toFixed(4) + 
                        ', Mean=' + cardState.baseline.EAR_mean.toFixed(4) + ', SD=' + cardState.baseline.EAR_sd.toFixed(4) + '</p>';
                }

                // Save calibration data to database
                saveCardCalibrationToAPI(cardId);
            }
        }

        // Process detection for specific card
        function processCardDetection(cardId, ear, timestamp) {
            const cardState = driverCards[cardId];
            if (!cardState) return;

            // Add to rolling window
            cardState.earHistory.push({ ear, timestamp });

            // Remove old data outside 60s window
            const cutoff = timestamp - WINDOW_SIZE;
            cardState.earHistory = cardState.earHistory.filter(d => d.timestamp >= cutoff);

            if (cardState.earHistory.length < 10) return; // Need minimum data

            // Calculate metrics using card's baseline
            const metrics = calculateCardMetrics(cardId, cardState.earHistory, timestamp, cardState.baseline);

            // Update UI for this card
            updateCardMetrics(cardId, metrics);

            // Check if metrics have changed and save to API
            if (metrics && hasCardMetricsChanged(cardId, metrics)) {
                sendCardToAPI(cardId, metrics, timestamp);
                // Update previous metrics
                cardState.previousMetrics = {
                    safety_score: metrics.safetyScore,
                    status: metrics.status,
                    fatigue: metrics.fatigue,
                    drift: metrics.drift,
                    perclos_60s: metrics.perclos,
                    blink_60s: metrics.blinkCount,
                    microsleep_60s: metrics.microsleepCount
                };
            }
        }

        // Calculate metrics for specific card
        function calculateCardMetrics(cardId, history, currentTime, baseline) {
            if (!baseline.T_close || history.length === 0) {
                return null;
            }

            const windowStart = currentTime - WINDOW_SIZE;
            const windowData = history.filter(d => d.timestamp >= windowStart);

            if (windowData.length === 0) return null;

            // PERCLOS
            const belowThreshold = windowData.filter(d => d.ear < baseline.T_close).length;
            const perclos = belowThreshold / windowData.length;

            // Blink detection
            let blinkCount = 0;
            let inBlink = false;
            let blinkStart = null;

            for (let i = 0; i < windowData.length; i++) {
                const isBelow = windowData[i].ear < baseline.T_close;
                const prevIsBelow = i > 0 ? windowData[i-1].ear < baseline.T_close : false;

                if (isBelow && !prevIsBelow) {
                    inBlink = true;
                    blinkStart = windowData[i].timestamp;
                } else if (!isBelow && prevIsBelow && inBlink) {
                    const duration = windowData[i].timestamp - blinkStart;
                    if (duration >= BLINK_MIN_DURATION && duration <= BLINK_MAX_DURATION) {
                        blinkCount++;
                    }
                    inBlink = false;
                    blinkStart = null;
                }
            }
            
            if (inBlink && windowData.length > 0) {
                const duration = currentTime - blinkStart;
                if (duration >= BLINK_MIN_DURATION && duration <= BLINK_MAX_DURATION) {
                    blinkCount++;
                }
            }

            // Microsleep
            let microsleepCount = 0;
            let inMicrosleep = false;
            let microsleepStart = null;

            for (let i = 0; i < windowData.length; i++) {
                const isBelow = windowData[i].ear < baseline.T_close;
                const prevIsBelow = i > 0 ? windowData[i-1].ear < baseline.T_close : false;

                if (isBelow && !prevIsBelow) {
                    inMicrosleep = true;
                    microsleepStart = windowData[i].timestamp;
                } else if (!isBelow && prevIsBelow && inMicrosleep) {
                    const duration = windowData[i].timestamp - microsleepStart;
                    if (duration >= MICROSLEEP_DURATION) {
                        microsleepCount++;
                    }
                    inMicrosleep = false;
                    microsleepStart = null;
                }
            }

            if (inMicrosleep && windowData.length > 0) {
                const duration = currentTime - microsleepStart;
                if (duration >= MICROSLEEP_DURATION) {
                    microsleepCount++;
                }
            }

            // Calculate Fatigue, Drift, and Safety Score (same logic as before)
            const blinkRate = blinkCount / 60;
            const normalBlinkRate = 0.375;
            const blinkStd = 0.125;
            const deltaBlinkZ = Math.abs(blinkRate - normalBlinkRate) / blinkStd;
            const sigmaDeltaBlink = Math.min(deltaBlinkZ / 3.0, 1.0);
            const denseBlink = blinkRate > 0.5 ? 1 : 0;
            const microsleepIndicator = microsleepCount > 0 ? 1 : 0;
            const zPerclos = Math.min(perclos, 1.0);

            const fatigue = 100 * (
                0.45 * zPerclos +
                0.25 * microsleepIndicator +
                0.20 * sigmaDeltaBlink +
                0.10 * denseBlink
            );

            // EAR Slope
            const n = windowData.length;
            const sumX = windowData.reduce((sum, d, idx) => sum + idx, 0);
            const sumY = windowData.reduce((sum, d) => sum + d.ear, 0);
            const sumXY = windowData.reduce((sum, d, idx) => sum + idx * d.ear, 0);
            const sumX2 = windowData.reduce((sum, d, idx) => sum + idx * idx, 0);
            const slope = (n * sumXY - sumX * sumY) / (n * sumX2 - sumX * sumX);
            const slopePerSec = slope * FPS;
            const negSlope = Math.max(0, -slopePerSec);
            const sigmaNegSlope = Math.min(negSlope * 10, 1.0);

            // Band-out ratio
            const lowerBound = baseline.EAR_mean - baseline.EAR_sd;
            const upperBound = baseline.EAR_mean + baseline.EAR_sd;
            const outOfBand = windowData.filter(d => 
                d.ear < lowerBound || d.ear > upperBound
            ).length;
            const bandOutRatio = outOfBand / windowData.length;

            // ΔPERCLOS
            const midPoint = Math.floor(windowData.length / 2);
            const firstHalf = windowData.slice(0, midPoint);
            const secondHalf = windowData.slice(midPoint);
            const firstHalfBelow = firstHalf.filter(d => d.ear < baseline.T_close).length;
            const secondHalfBelow = secondHalf.filter(d => d.ear < baseline.T_close).length;
            const perclosFirst = firstHalf.length > 0 ? firstHalfBelow / firstHalf.length : 0;
            const perclosSecond = secondHalf.length > 0 ? secondHalfBelow / secondHalf.length : 0;
            const deltaPerclos = perclosSecond - perclosFirst;
            const absDeltaPerclos = Math.abs(deltaPerclos);

            const drift = 100 * (
                0.5 * sigmaNegSlope +
                0.3 * bandOutRatio +
                0.2 * absDeltaPerclos
            );

            const microsleepPenalty = microsleepCount * 10;
            const safetyScore = Math.max(0, Math.min(100, 
                100 - (0.7 * fatigue + 0.3 * drift + microsleepPenalty)
            ));

            let status = 'Safe';
            if (safetyScore < 60) {
                status = 'Attention';
            } else if (safetyScore < 80) {
                status = 'Caution';
            }

            return {
                ear: windowData[windowData.length - 1].ear,
                perclos,
                blinkCount,
                microsleepCount,
                fatigue,
                drift,
                safetyScore,
                status
            };
        }

        // Check if metrics have changed for specific card
        function hasCardMetricsChanged(cardId, metrics) {
            const cardState = driverCards[cardId];
            if (!cardState || !metrics) return false;
            
            if (cardState.previousMetrics.safety_score === null) {
                return true;
            }
            
            const FLOAT_THRESHOLD = 0.01;
            const hasFloatChanged = (oldVal, newVal) => {
                if (oldVal === null || newVal === null) return oldVal !== newVal;
                return Math.abs(oldVal - newVal) >= FLOAT_THRESHOLD;
            };
            
            return hasFloatChanged(cardState.previousMetrics.safety_score, metrics.safetyScore) ||
                   cardState.previousMetrics.status !== metrics.status ||
                   hasFloatChanged(cardState.previousMetrics.fatigue, metrics.fatigue) ||
                   hasFloatChanged(cardState.previousMetrics.drift, metrics.drift) ||
                   hasFloatChanged(cardState.previousMetrics.perclos_60s, metrics.perclos) ||
                   cardState.previousMetrics.blink_60s !== metrics.blinkCount ||
                   cardState.previousMetrics.microsleep_60s !== metrics.microsleepCount;
        }

        // Update UI metrics for specific card
        function updateCardMetrics(cardId, metrics) {
            if (!metrics) return;

            const safetyScoreEl = document.getElementById(cardId + '-safetyScore');
            const fatigueScoreEl = document.getElementById(cardId + '-fatigueScore');
            const driftScoreEl = document.getElementById(cardId + '-driftScore');
            const perclosValueEl = document.getElementById(cardId + '-perclosValue');
            const blinkCountEl = document.getElementById(cardId + '-blinkCount');
            const microsleepCountEl = document.getElementById(cardId + '-microsleepCount');
            const safetyScoreCardEl = document.getElementById(cardId + '-safetyScoreCard');
            const statusBadgeEl = document.getElementById(cardId + '-statusBadge');

            if (safetyScoreEl) safetyScoreEl.textContent = metrics.safetyScore.toFixed(0);
            if (fatigueScoreEl) fatigueScoreEl.textContent = metrics.fatigue.toFixed(0);
            if (driftScoreEl) driftScoreEl.textContent = metrics.drift.toFixed(0);
            if (perclosValueEl) perclosValueEl.textContent = (metrics.perclos * 100).toFixed(1) + '%';
            if (blinkCountEl) blinkCountEl.textContent = metrics.blinkCount;
            if (microsleepCountEl) microsleepCountEl.textContent = metrics.microsleepCount;

            if (statusBadgeEl) {
                statusBadgeEl.className = 'status-badge';
                
                if (metrics.status === 'Safe') {
                    statusBadgeEl.classList.add('safe');
                    statusBadgeEl.textContent = 'Safe';
                } else if (metrics.status === 'Caution') {
                    statusBadgeEl.classList.add('caution');
                    statusBadgeEl.textContent = 'Caution';
                } else {
                    statusBadgeEl.classList.add('attention');
                    statusBadgeEl.textContent = 'Attention';
                }
            }
        }

        // Save calibration to API for specific card
        async function saveCardCalibrationToAPI(cardId) {
            const cardState = driverCards[cardId];
            if (!cardState || !cardState.baseline.T_close || !cardState.calibrationStartTime) return;

            const payload = {
                driver_id: cardState.driverId || driverId || `Driver-${cardId}`,
                trip_id: cardState.tripId || tripId || 'T' + Date.now(),
                calibration_start_time: new Date(cardState.calibrationStartTime).toISOString(),
                calibration_end_time: new Date(Date.now()).toISOString(),
                t_close: cardState.baseline.T_close,
                ear_mean: cardState.baseline.EAR_mean,
                ear_sd: cardState.baseline.EAR_sd,
                data_points_count: cardState.calibrationData.length,
                notes: cardState.streamUrl || 'Auto-calibration completed'
            };

            try {
                // If calibrationId exists, update the existing record
                // Otherwise create new one (for backward compatibility)
                if (cardState.calibrationId) {
                    // Update existing calibration record
                    const response = await fetch(`/api/dms/calibration/${cardState.calibrationId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    });

                    if (!response.ok) {
                        // If update fails, try creating new one
                        console.warn('Update failed, creating new calibration record');
                        await createNewCalibrationRecord(payload);
                    } else {
                        console.log('Calibration data updated successfully for card:', cardId);
                    }
                } else {
                    // Create new calibration record
                    await createNewCalibrationRecord(payload);
                }
            } catch (error) {
                console.error('Failed to save calibration to API for card:', cardId, error);
            }
        }

        // Helper function to create new calibration record
        async function createNewCalibrationRecord(payload) {
            const response = await fetch('/api/dms/calibration', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Calibration API error:', errorText);
            } else {
                const result = await response.json();
                console.log('Calibration data saved successfully:', result);
            }
        }

        // Send data to API for specific card
        async function sendCardToAPI(cardId, metrics, timestamp) {
            const cardState = driverCards[cardId];
            if (!cardState || !metrics) return;

            const payload = {
                driver_id: cardState.driverId || driverId || `Driver-${cardId}`,
                trip_id: cardState.tripId || tripId || null,
                calibration_id: cardState.calibrationId || (selectedCalibration ? selectedCalibration.id : null),
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
                const response = await fetch('/api/dms/safety-score', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const responseData = await response.json();

                if (!response.ok) {
                    console.error('API error response for card:', cardId, responseData);
                } else {
                    console.log('Metrics saved successfully for card:', cardId, responseData);
                }
            } catch (error) {
                console.error('Failed to send to API for card:', cardId, error);
            }
        }

        // Initialize on load
        window.addEventListener('load', function() {
            initializeDefaultCards();
            init();
        });
    </script>

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection

