<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Driver Fatigue Detection System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .header {
            background: #4a5568;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 20px;
            padding: 20px;
        }

        .video-section {
            background: #000;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
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

        .metrics-panel {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .metric-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            border-left: 4px solid #3b82f6;
        }

        .metric-card h3 {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .metric-value {
            font-size: 2.5em;
            font-weight: bold;
            color: #2d3748;
        }

        .safety-score-card {
            background: #e6fffa;
            border-left-color: #38b2ac;
        }

        .safety-score-card.caution {
            background: #fef5e7;
            border-left-color: #ed8936;
        }

        .safety-score-card.attention {
            background: #fed7d7;
            border-left-color: #e53e3e;
        }
        
        .safety-score-card h3 {
            color: #2d3748;
        }
        
        .safety-score-card h3 {
            color: #2d3748;
        }
        
        .safety-score-card .metric-value {
            color: #2d3748;
        }

        .calibration-status {
            background: #fef5e7;
            border: 2px solid #ed8936;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }

        .calibration-status.active {
            background: #e6f3ff;
            border-color: #4299e1;
        }

        .calibration-status.complete {
            background: #e6fffa;
            border-color: #38b2ac;
        }

        .calibration-timer {
            font-size: 2em;
            font-weight: bold;
            color: #2d3748;
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
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
            margin-top: 10px;
        }

        .status-safe {
            background: #38b2ac;
            color: white;
        }

        .status-caution {
            background: #ed8936;
            color: white;
        }

        .status-attention {
            background: #e53e3e;
            color: white;
        }

        .mode-selection {
            padding: 20px;
            background: white;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 2px solid #e2e8f0;
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
            border: 2px solid #cbd5e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .mode-input-group input:focus,
        .mode-input-group select:focus {
            outline: none;
            border-color: #4299e1;
        }

        .required-mark {
            color: #e53e3e;
        }

        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚗 Driver Fatigue Detection System</h1>
            <p>Real-time monitoring menggunakan MediaPipe Face Landmarker</p>
        </div>

        <div class="main-content">
            <div class="video-section">
                <video id="videoElement" autoplay playsinline></video>
                <canvas id="canvasElement"></canvas>
                <div class="status-overlay">
                    <div>Status: <span id="detectionStatus">Initializing...</span></div>
                    <div>EAR: <span id="currentEAR">--</span></div>
                </div>
            </div>

            <div class="metrics-panel">
                <div class="calibration-status" id="calibrationStatus">
                    <h3>Kalibrasi</h3>
                    <div class="calibration-timer" id="calibrationTimer">00:00</div>
                    <p>Mengumpulkan data baseline (15 menit)</p>
                </div>

                <div class="metric-card safety-score-card" id="safetyScoreCard">
                    <h3>Safety Score</h3>
                    <div class="metric-value" id="safetyScore">--</div>
                    <span class="status-badge status-safe" id="statusBadge">Safe</span>
                </div>

                <div class="metric-card">
                    <h3>Fatigue Score</h3>
                    <div class="metric-value" id="fatigueScore">--</div>
                </div>

                <div class="metric-card">
                    <h3>Drift Score</h3>
                    <div class="metric-value" id="driftScore">--</div>
                </div>

                <div class="metric-card">
                    <h3>PERCLOS (60s)</h3>
                    <div class="metric-value" id="perclosValue">--</div>
                </div>

                <div class="metric-card">
                    <h3>Blink Count (60s)</h3>
                    <div class="metric-value" id="blinkCount">0</div>
                </div>

                <div class="metric-card">
                    <h3>Microsleep (60s)</h3>
                    <div class="metric-value" id="microsleepCount">0</div>
                </div>
            </div>
        </div>

        <div class="controls">
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
                        <input type="text" id="driverNameInput" placeholder="Contoh: John Doe / D001">
                    </div>
                    <div class="mode-input-group">
                        <label>
                            Trip ID (Opsional)
                        </label>
                        <input type="text" id="tripIdInput" placeholder="Contoh: T001">
                    </div>
                </div>

                <!-- Dropdown untuk Mode Deteksi -->
                <div id="detectionInputs" style="display: none;">
                    <div class="mode-input-group">
                        <label>
                            Pilih Kalibrasi <span class="required-mark">*</span>
                        </label>
                        <select id="calibrationSelect">
                            <option value="">-- Pilih Kalibrasi --</option>
                        </select>
                        <button onclick="loadCalibrations()" 
                                style="margin-top: 10px; padding: 8px 16px; background: #4299e1; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
                            🔄 Refresh List
                        </button>
                    </div>
                </div>
            </div>

            <div class="upload-section" id="uploadSection">
                <h3 style="margin-bottom: 15px; color: #2d3748;">Upload Video</h3>
                <div class="file-input-wrapper">
                    <input type="file" id="videoFileInput" accept="video/*" onchange="handleVideoUpload(event)">
                    <label for="videoFileInput" class="file-input-label">Pilih Video File</label>
                </div>
                <div class="video-info" id="videoInfo" style="display: none;">
                    <p><strong>File:</strong> <span id="videoFileName"></span></p>
                    <p><strong>Duration:</strong> <span id="videoDuration"></span></p>
                </div>
            </div>
            
            <div class="video-controls">
                <button class="btn-start" id="startBtn" onclick="startDetection()" disabled>Start Detection</button>
                <button class="btn-stop" id="stopBtn" onclick="stopDetection()" disabled>Stop Detection</button>
                <button class="btn-start" id="playBtn" onclick="playVideo()" style="display: none;">Play Video</button>
                <button class="btn-stop" id="pauseBtn" onclick="pauseVideo()" style="display: none;">Pause Video</button>
            </div>
        </div>
    </div>

    <!-- MediaPipe Face Landmarker from CDN -->
    <!-- Using correct import path based on MediaPipe package structure -->
    <script type="module">
        (async function() {
            try {
                console.log('Loading MediaPipe tasks-vision...');
                
                // The correct import path for MediaPipe tasks-vision
                // MediaPipe exports from the wasm directory
                // Try importing from the wasm subdirectory which contains the actual implementation
                let mediapipeModule;
                
                // First try: import from wasm directory (most likely correct)
                try {
                    mediapipeModule = await import('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/wasm/index.mjs');
                    console.log('Loaded from .mjs extension');
                } catch (e1) {
                    try {
                        mediapipeModule = await import('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/wasm/index.js');
                        console.log('Loaded from .js extension');
                    } catch (e2) {
                        try {
                            mediapipeModule = await import('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/wasm');
                            console.log('Loaded from /wasm directory');
                        } catch (e3) {
                            // Last resort: try main package
                            mediapipeModule = await import('https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3');
                            console.log('Loaded from main package');
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
                
                // Try fallback: unpkg with multiple paths
                const unpkgPaths = [
                    'https://unpkg.com/@mediapipe/tasks-vision@0.10.3/wasm/index.mjs',
                    'https://unpkg.com/@mediapipe/tasks-vision@0.10.3/wasm/index.js',
                    'https://unpkg.com/@mediapipe/tasks-vision@0.10.3/wasm',
                    'https://unpkg.com/@mediapipe/tasks-vision@0.10.3'
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
        
        // Mode management
        let currentMode = 'calibration'; // 'calibration' or 'detection'
        let selectedCalibration = null;

        // Initialize
        async function init() {
            video = document.getElementById('videoElement');
            canvas = document.getElementById('canvasElement');
            ctx = canvas.getContext('2d');

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
                
                alert('MediaPipe gagal dimuat dari CDN.\n\n' +
                      'Kemungkinan penyebab:\n' +
                      '• Masalah koneksi internet\n' +
                      '• CDN tidak dapat diakses\n' +
                      '• Struktur package berubah\n\n' +
                      'Silakan buka console browser (F12) untuk detail error.');
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
                const vision = await window.MediaPipeVision.FilesetResolver.forVisionTasks(
                    "https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.3/wasm"
                );

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
                // For uploaded video, use video currentTime in milliseconds
                const videoTimeMs = videoUrl ? (video.currentTime * 1000) : startTimeMs;
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

        // Draw landmarks
        function drawLandmarks(landmarks) {
            if (!video || !canvas || !ctx) return;
            
            // Ensure canvas matches video dimensions
            if (canvas.width !== video.videoWidth || canvas.height !== video.videoHeight) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            }
            
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw eye landmarks
            ctx.strokeStyle = '#00ff00';
            ctx.lineWidth = 2;
            ctx.fillStyle = '#00ff00';

            // Left eye - draw 6 points
            try {
                const leftEyePoints = LEFT_EYE_INDICES.map(i => ({
                    x: landmarks[i].x * canvas.width,
                    y: landmarks[i].y * canvas.height
                }));
                ctx.beginPath();
                ctx.moveTo(leftEyePoints[0].x, leftEyePoints[0].y);
                for (let i = 1; i < leftEyePoints.length; i++) {
                    ctx.lineTo(leftEyePoints[i].x, leftEyePoints[i].y);
                }
                ctx.closePath();
                ctx.stroke();
                
                // Draw points
                leftEyePoints.forEach(p => {
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, 3, 0, 2 * Math.PI);
                    ctx.fill();
                });
            } catch (e) {
                console.warn('Error drawing left eye:', e);
            }

            // Right eye - draw 6 points
            try {
                const rightEyePoints = RIGHT_EYE_INDICES.map(i => ({
                    x: landmarks[i].x * canvas.width,
                    y: landmarks[i].y * canvas.height
                }));
                ctx.beginPath();
                ctx.moveTo(rightEyePoints[0].x, rightEyePoints[0].y);
                for (let i = 1; i < rightEyePoints.length; i++) {
                    ctx.lineTo(rightEyePoints[i].x, rightEyePoints[i].y);
                }
                ctx.closePath();
                ctx.stroke();
                
                // Draw points
                rightEyePoints.forEach(p => {
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, 3, 0, 2 * Math.PI);
                    ctx.fill();
                });
            } catch (e) {
                console.warn('Error drawing right eye:', e);
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
            document.getElementById('calibrationTimer').textContent = 
                `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

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
                document.getElementById('calibrationStatus').classList.add('complete');
                document.getElementById('calibrationStatus').innerHTML = 
                    '<h3>Kalibrasi Selesai</h3><p>Baseline: T_close=' + baseline.T_close.toFixed(4) + 
                    ', Mean=' + baseline.EAR_mean.toFixed(4) + ', SD=' + baseline.EAR_sd.toFixed(4) + '</p>';

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

            // Send to API every 5 seconds
            if (timestamp - lastApiCall >= API_INTERVAL) {
                sendToAPI(metrics, timestamp);
                lastApiCall = timestamp;
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

        // Update UI metrics
        function updateMetrics(metrics) {
            if (!metrics) return;

            document.getElementById('safetyScore').textContent = metrics.safetyScore.toFixed(1);
            document.getElementById('fatigueScore').textContent = metrics.fatigue.toFixed(1);
            document.getElementById('driftScore').textContent = metrics.drift.toFixed(1);
            document.getElementById('perclosValue').textContent = (metrics.perclos * 100).toFixed(2) + '%';
            document.getElementById('blinkCount').textContent = metrics.blinkCount;
            document.getElementById('microsleepCount').textContent = metrics.microsleepCount;

            // Update safety score card styling
            const card = document.getElementById('safetyScoreCard');
            const badge = document.getElementById('statusBadge');
            
            card.className = 'metric-card safety-score-card';
            badge.className = 'status-badge';
            
            if (metrics.status === 'Safe') {
                card.classList.add('safe');
                badge.classList.add('status-safe');
                badge.textContent = 'Safe';
            } else if (metrics.status === 'Caution') {
                card.classList.add('caution');
                badge.classList.add('status-caution');
                badge.textContent = 'Caution';
            } else {
                card.classList.add('attention');
                badge.classList.add('status-attention');
                badge.textContent = 'Attention';
            }
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
            if (!metrics) return;

            const payload = {
                driver_id: driverId,
                trip_id: tripId,
                timestamp: new Date(timestamp).toISOString(),
                ear: metrics.ear,
                perclos_60s: metrics.perclos,
                blink_60s: metrics.blinkCount,
                microsleep_60s: metrics.microsleepCount,
                fatigue: metrics.fatigue,
                drift: metrics.drift,
                safety_score: metrics.safetyScore,
                status: metrics.status
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

                if (!response.ok) {
                    console.error('API error:', await response.text());
                }
            } catch (error) {
                console.error('Failed to send to API:', error);
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
            } else {
                // Reset untuk mode kalibrasi
                driverId = '';
                tripId = 'T' + Date.now();
                selectedCalibration = null;
                baseline = { T_close: null, EAR_mean: null, EAR_sd: null };
                isCalibrating = true;
                calibrationData = [];
                earHistory = [];
                
                // Reset calibration status
                document.getElementById('calibrationStatus').classList.remove('complete');
                document.getElementById('calibrationStatus').innerHTML = 
                    '<h3>Kalibrasi</h3><div class="calibration-timer" id="calibrationTimer">00:00</div><p>Mengumpulkan data baseline (15 menit)</p>';
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

        // Load Baseline dari Kalibrasi yang dipilih
        function loadBaselineFromCalibration(calibration) {
            baseline.T_close = parseFloat(calibration.t_close);
            baseline.EAR_mean = parseFloat(calibration.ear_mean);
            baseline.EAR_sd = parseFloat(calibration.ear_sd);
            
            // Set driver_id dan trip_id
            driverId = calibration.driver_id;
            tripId = calibration.trip_id || 'T' + Date.now();
            
            // Skip kalibrasi, langsung ke deteksi
            isCalibrating = false;
            
            // Update UI
            document.getElementById('calibrationStatus').classList.add('complete');
            const date = new Date(calibration.calibration_start_time);
            document.getElementById('calibrationStatus').innerHTML = 
                '<h3>Baseline Loaded</h3>' +
                '<p><strong>Driver:</strong> ' + calibration.driver_id + '</p>' +
                '<p><strong>Tanggal:</strong> ' + date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) + '</p>' +
                '<p><strong>T_close:</strong> ' + baseline.T_close.toFixed(4) + '</p>' +
                '<p><strong>EAR_mean:</strong> ' + baseline.EAR_mean.toFixed(4) + '</p>' +
                '<p><strong>EAR_sd:</strong> ' + baseline.EAR_sd.toFixed(4) + '</p>';
            
            updateStatus('Baseline loaded - Ready for detection');
        }

        // Event listener untuk calibration select
        document.addEventListener('DOMContentLoaded', function() {
            const calibrationSelect = document.getElementById('calibrationSelect');
            if (calibrationSelect) {
                calibrationSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.value) {
                        selectedCalibration = JSON.parse(selectedOption.dataset.calibration);
                        loadBaselineFromCalibration(selectedCalibration);
                    } else {
                        selectedCalibration = null;
                    }
                });
            }
        });

        // Update status
        function updateStatus(status) {
            document.getElementById('detectionStatus').textContent = status;
        }

        // Handle video file upload
        function handleVideoUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.startsWith('video/')) {
                alert('File harus berupa video!');
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
                alert('Error loading video file');
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
                    alert('Silakan upload video terlebih dahulu!');
                    return;
                }

                // Handle mode-specific validation
                if (currentMode === 'calibration') {
                    // Mode Kalibrasi: Validasi input nama
                    const driverName = document.getElementById('driverNameInput').value.trim();
                    if (!driverName) {
                        alert('Silakan masukkan Nama / ID Orang untuk kalibrasi!');
                        return;
                    }
                    driverId = driverName;
                    tripId = document.getElementById('tripIdInput').value.trim() || 'T' + Date.now();
                    
                    // Reset kalibrasi
                    isCalibrating = true;
                    calibrationData = [];
                    baseline = { T_close: null, EAR_mean: null, EAR_sd: null };
                    calibrationStartTime = Date.now();
                    
                } else {
                    // Mode Deteksi: Validasi kalibrasi dipilih
                    if (!selectedCalibration) {
                        alert('Silakan pilih kalibrasi terlebih dahulu!');
                        return;
                    }
                    
                    // Baseline sudah di-load dari loadBaselineFromCalibration()
                    // Skip kalibrasi
                    isCalibrating = false;
                }

                // Ensure video is loaded
                if (video.readyState < 2) {
                    await new Promise((resolve, reject) => {
                        video.onloadedmetadata = resolve;
                        video.onerror = reject;
                        setTimeout(() => reject(new Error('Video load timeout')), 5000);
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

                document.getElementById('startBtn').disabled = true;
                document.getElementById('stopBtn').disabled = false;
                document.getElementById('playBtn').style.display = 'none';
                document.getElementById('pauseBtn').style.display = 'inline-block';

                // Update UI based on mode
                if (currentMode === 'calibration') {
                    document.getElementById('calibrationStatus').classList.remove('complete');
                    document.getElementById('calibrationStatus').classList.add('active');
                    document.getElementById('calibrationStatus').innerHTML = 
                        '<h3>Kalibrasi</h3><div class="calibration-timer" id="calibrationTimer">00:00</div><p>Mengumpulkan data baseline (15 menit)</p>';
                    updateStatus('Calibrating...');
                } else {
                    updateStatus('Detecting...');
                }

                // Start processing frames
                processFrame();
            } catch (error) {
                console.error('Error starting detection:', error);
                updateStatus('Error: ' + error.message);
                alert('Tidak dapat memulai deteksi. Pastikan video sudah dimuat dengan benar.');
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

        // Initialize on load
        window.addEventListener('load', init);
    </script>
</body>
</html>

