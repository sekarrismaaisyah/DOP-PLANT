@extends('layouts.master')

@section('title', 'DMS Dashboard - Static')
@section('content')
<x-page-title title="DMS Dashboard" pagetitle="Driver Monitoring System - Static Data" />

<style>
    .operator-card {
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        background: #ffffff;
        overflow: hidden;
        position: relative;
        margin-bottom: 24px;
    }
    .operator-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .operator-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f3f4f6;
        background: #ffffff;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .operator-title {
        font-size: 20px;
        font-weight: 600;
        color: #111827;
        margin: 0;
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
    .status-badge.medium {
        background: #fef3c7;
        color: #78350f;
    }
    .operator-body {
        padding: 24px;
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
    .indicator-section {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }
    .indicator-item {
        margin-bottom: 12px;
    }
    .indicator-item:last-child {
        margin-bottom: 0;
    }
    .indicator-label {
        font-size: 11px;
        color: #6b7280;
        margin-bottom: 4px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .indicator-value {
        font-size: 14px;
        color: #9ca3af;
        font-style: italic;
    }
    .chart-container {
        position: relative;
        height: 250px;
        margin-top: 16px;
        background: #ffffff;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #e5e7eb;
    }
    .chart-wrapper {
        position: relative;
        height: 100%;
        width: 100%;
    }
    .chart-wrapper canvas {
        width: 100%;
        height: 100%;
        border-radius: 8px;
    }
    .detail-btn {
        margin-top: 20px;
        padding: 12px 20px;
        background: #ffffff;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        color: #374151;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }
    .detail-btn:hover {
        background: #f9fafb;
        border-color: #3b82f6;
        color: #3b82f6;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .detail-btn:active {
        transform: translateY(0);
    }

    /* Responsive untuk mobile */
    @media (max-width: 768px) {
        .operator-card {
            margin-bottom: 16px;
        }
        .operator-header {
            padding: 16px;
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        .operator-title {
            font-size: 18px;
        }
        .status-badge {
            padding: 5px 12px;
            font-size: 11px;
            align-self: flex-end;
        }
        .operator-body {
            padding: 16px;
        }
        .metrics-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .metric-item {
            padding: 10px;
        }
        .metric-value {
            font-size: 20px;
        }
        .metric-value.orange {
            font-size: 24px;
        }
        .metric-value.small {
            font-size: 14px;
        }
        .chart-container {
            height: 200px;
            padding: 12px;
            margin-top: 12px;
        }
        .indicator-section {
            margin-top: 12px;
            padding-top: 12px;
        }
        .detail-btn {
            margin-top: 16px;
            padding: 10px 16px;
            font-size: 13px;
        }
    }

    @media (max-width: 576px) {
        .operator-header {
            padding: 12px;
        }
        .operator-title {
            font-size: 16px;
        }
        .operator-body {
            padding: 12px;
        }
        .chart-container {
            height: 180px;
            padding: 8px;
        }
        .metrics-grid {
            gap: 10px;
        }
        .metric-item {
            padding: 8px;
        }
        .metric-label {
            font-size: 10px;
        }
        .metric-value {
            font-size: 18px;
        }
        .metric-value.orange {
            font-size: 22px;
        }
        .metric-value.small {
            font-size: 13px;
        }
        .indicator-label {
            font-size: 10px;
        }
        .indicator-value {
            font-size: 12px;
        }
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                    <div>
                        <h5 class="mb-1">Monitoring</h5>
                        <p class="text-muted mb-0">Data statis untuk demo</p>
                    </div>
                </div>

                <div class="row" id="operatorsContainer">
                    <!-- Cards will be rendered here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let canvasData = {};

    // Function to get status badge class
    function getStatusClass(status) {
        const statusLower = (status || '').toLowerCase();
        if (statusLower === 'safe') return 'safe';
        if (statusLower === 'caution') return 'caution';
        if (statusLower === 'attention') return 'attention';
        return 'medium';
    }

    // Function to format status text
    function formatStatus(status) {
        const statusLower = (status || '').toLowerCase();
        if (statusLower === 'safe') return 'Safe';
        if (statusLower === 'caution') return 'Caution';
        if (statusLower === 'attention') return 'Attention';
        return 'Medium';
    }

    // Function to generate EAR data with different patterns
    function generateEARDataPattern1(count = 100) {
        // Pattern 1: Fluctuating dengan banyak turun naik (untuk attention status)
        const data = [];
        for (let i = 0; i < count; i++) {
            const base = 0.18;
            const variation = Math.sin(i / 8) * 0.12 + Math.cos(i / 15) * 0.08 + Math.random() * 0.06;
            data.push(Math.max(0.1, Math.min(0.35, base + variation)));
        }
        return data;
    }

    function generateEARDataPattern2(count = 100) {
        // Pattern 2: Stabil di atas threshold (untuk safe status)
        const data = [];
        for (let i = 0; i < count; i++) {
            const base = 0.28;
            const variation = Math.sin(i / 20) * 0.04 + Math.random() * 0.02;
            data.push(Math.max(0.22, Math.min(0.35, base + variation)));
        }
        return data;
    }

    function generateEARDataPattern3(count = 100) {
        // Pattern 3: Sedang turun naik dengan tren menurun (untuk caution status)
        const data = [];
        for (let i = 0; i < count; i++) {
            const base = 0.24 - (i / count) * 0.06;
            const variation = Math.sin(i / 12) * 0.08 + Math.random() * 0.04;
            data.push(Math.max(0.15, Math.min(0.32, base + variation)));
        }
        return data;
    }

    function generateEARDataPattern4(count = 100) {
        // Pattern 4: Spiky dengan banyak spike rendah (untuk attention status)
        const data = [];
        for (let i = 0; i < count; i++) {
            let base = 0.22;
            // Create spikes every 15-20 points
            if (i % 18 < 3) {
                base = 0.12 + Math.random() * 0.05; // Low spike
            } else {
                base = 0.25 + Math.sin(i / 10) * 0.06 + Math.random() * 0.03;
            }
            data.push(Math.max(0.1, Math.min(0.35, base)));
        }
        return data;
    }

    // Function to draw EAR waveform chart on canvas
    function drawEARChart(canvasId, earData, timeData, threshold, earBandLow, earBandHigh) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        // Set canvas size based on container
        const container = canvas.parentElement;
        let W, H;
        
        if (container) {
            const rect = container.getBoundingClientRect();
            const dpr = window.devicePixelRatio || 1;
            const displayWidth = rect.width;
            const displayHeight = rect.height;
            
            // Set internal size (scaled for device pixel ratio)
            canvas.width = displayWidth * dpr;
            canvas.height = displayHeight * dpr;
            
            // Set display size (CSS pixels)
            canvas.style.width = displayWidth + 'px';
            canvas.style.height = displayHeight + 'px';
            
            // Scale context to match device pixel ratio
            ctx.scale(dpr, dpr);
            
            // Use display dimensions for drawing
            W = displayWidth;
            H = displayHeight;
        } else {
            // Fallback
            const rect = canvas.getBoundingClientRect();
            W = rect.width;
            H = rect.height;
            canvas.width = W;
            canvas.height = H;
        }

        // Clear canvas
        ctx.clearRect(0, 0, W, H);

        // Draw grid background (optimized)
        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth = 1;
        
        // Batch draw vertical lines
        ctx.beginPath();
        for (let x = 0; x < W; x += 10) {
            ctx.moveTo(x, 0);
            ctx.lineTo(x, H);
        }
        ctx.stroke();
        
        // Batch draw horizontal lines
        ctx.beginPath();
        for (let y = 0; y < H; y += 10) {
            ctx.moveTo(0, y);
            ctx.lineTo(W, y);
        }
        ctx.stroke();

        if (!earData || earData.length === 0) return;

        // EAR range
        const minEAR = 0.05;
        const maxEAR = 0.4;

        // Convert EAR value to Y coordinate
        const yOf = (v) => H - ((v - minEAR) / (maxEAR - minEAR)) * H;

        // Draw EAR band area (green semi-transparent)
        if (earBandLow !== null && earBandHigh !== null) {
            ctx.fillStyle = '#a7f3d0';
            ctx.globalAlpha = 0.25;
            const yHigh = yOf(earBandLow);
            const yLow = yOf(earBandHigh);
            ctx.fillRect(0, Math.min(yHigh, yLow), W, Math.abs(yLow - yHigh));
            ctx.globalAlpha = 1;
        }

        // Draw threshold line (orange dashed)
        if (threshold !== null) {
            ctx.setLineDash([4, 4]);
            ctx.strokeStyle = '#f59e0b';
            ctx.lineWidth = 2;
            ctx.beginPath();
            const yThr = yOf(threshold);
            ctx.moveTo(0, yThr);
            ctx.lineTo(W, yThr);
            ctx.stroke();
            ctx.setLineDash([]);
        }

        // Draw EAR data line (dark grey/black)
        ctx.strokeStyle = '#111827';
        ctx.lineWidth = 2;
        ctx.beginPath();

        for (let i = 0; i < earData.length; i++) {
            const x = (i / earData.length) * W;
            const y = yOf(earData[i]);

            if (i === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        }
        ctx.stroke();

        // Store data for later use (events, etc.)
        canvasData[canvasId] = {
            earData,
            timeData,
            threshold,
            earBandLow,
            earBandHigh,
            W,
            H,
            minEAR,
            maxEAR
        };
    }

    // Helper function to safely format numbers
    function safeToFixed(value, decimals = 0) {
        if (value === null || value === undefined || isNaN(value)) {
            return '0';
        }
        const num = parseFloat(value);
        if (isNaN(num)) {
            return '0';
        }
        return num.toFixed(decimals);
    }

    // Static data for operators
    const staticOperatorsData = [
        {
            driver_id: 'Driver-001',
            latest: {
                safety_score: 13,
                fatigue: 57,
                drift: 26,
                perclos_60s: 0.258,
                slope_ear_per_min: 5.4497,
                blink_60s: 0,
                microsleep_60s: 4,
                status: 'attention',
                ear_threshold: 0.2,
                ear_band_low: 0.22,
                ear_band_high: 0.3
            }
        },
        {
            driver_id: 'Driver-002',
            latest: {
                safety_score: 75,
                fatigue: 25,
                drift: 12,
                perclos_60s: 0.15,
                slope_ear_per_min: 2.1234,
                blink_60s: 15,
                microsleep_60s: 0,
                status: 'safe',
                ear_threshold: 0.2,
                ear_band_low: 0.22,
                ear_band_high: 0.3
            }
        },
        {
            driver_id: 'Driver-003',
            latest: {
                safety_score: 45,
                fatigue: 42,
                drift: 18,
                perclos_60s: 0.22,
                slope_ear_per_min: 3.5678,
                blink_60s: 8,
                microsleep_60s: 2,
                status: 'caution',
                ear_threshold: 0.2,
                ear_band_low: 0.22,
                ear_band_high: 0.3
            }
        },
        {
            driver_id: 'Driver-004',
            latest: {
                safety_score: 35,
                fatigue: 55,
                drift: 22,
                perclos_60s: 0.28,
                slope_ear_per_min: 4.2345,
                blink_60s: 5,
                microsleep_60s: 3,
                status: 'attention',
                ear_threshold: 0.2,
                ear_band_low: 0.22,
                ear_band_high: 0.3
            }
        }
    ];

    // Function to render operator card
    function renderOperatorCard(operatorData) {
        const { driver_id, latest } = operatorData;
        const statusClass = getStatusClass(latest.status);
        const statusText = formatStatus(latest.status);
        const canvasId = `chart-${driver_id.replace(/\s+/g, '-')}`;

        // Safely get values with defaults
        const safetyScore = latest.safety_score || 0;
        const fatigue = latest.fatigue || 0;
        const drift = latest.drift || 0;
        const perclos = latest.perclos_60s || 0;
        const slopeEar = latest.slope_ear_per_min || 0;
        const blinkCount = latest.blink_60s || 0;
        const microsleep = latest.microsleep_60s || 0;

        return `
            <div class="col-12 col-md-6">
                <div class="operator-card" id="operator-card-${driver_id.replace(/\s+/g, '-')}">
                    <div class="operator-header">
                        <h3 class="operator-title">${driver_id}</h3>
                        <span class="status-badge ${statusClass}">${statusText}</span>
                    </div>
                    <div class="operator-body">
                        <div class="chart-container">
                            <div class="chart-wrapper">
                                <canvas id="${canvasId}" style="width: 100%; height: 100%;"></canvas>
                            </div>
                        </div>
                        <div class="metrics-grid mt-2">
                            <div class="metric-item" data-metric="safety-score">
                                <div class="metric-label">Safety Score</div>
                                <div class="metric-value orange">${safeToFixed(safetyScore, 0)}</div>
                            </div>
                            <div class="metric-item" data-metric="safety-band">
                                <div class="metric-label">Safety Band</div>
                                <div class="metric-value small">${statusText}</div>
                            </div>
                            <div class="metric-item" data-metric="fatigue">
                                <div class="metric-label">Fatigue</div>
                                <div class="metric-value">${safeToFixed(fatigue, 0)}</div>
                            </div>
                            <div class="metric-item" data-metric="drift">
                                <div class="metric-label">Drift</div>
                                <div class="metric-value">${safeToFixed(drift, 0)}</div>
                            </div>
                            <div class="metric-item" data-metric="perclos">
                                <div class="metric-label">PERCLOS</div>
                                <div class="metric-value">${safeToFixed(perclos * 100, 1)}%</div>
                            </div>
                            <div class="metric-item" data-metric="slope-ear">
                                <div class="metric-label">Slope EAR (/MIN)</div>
                                <div class="metric-value">${safeToFixed(slopeEar, 4)}</div>
                            </div>
                            <div class="metric-item" data-metric="blink-count">
                                <div class="metric-label">Blink Count (60s)</div>
                                <div class="metric-value">${blinkCount}</div>
                            </div>
                            <div class="metric-item" data-metric="microsleep">
                                <div class="metric-label">Microsleep (60s)</div>
                                <div class="metric-value">${microsleep}</div>
                            </div>
                        </div>
                        <div class="indicator-section">
                            <div class="indicator-item" data-indicator="fatigue">
                                <div class="indicator-label">Indikator Fatigue</div>
                                <div class="indicator-value">-</div>
                            </div>
                            <div class="indicator-item" data-indicator="drift">
                                <div class="indicator-label">Drift Pattern</div>
                                <div class="indicator-value">-</div>
                            </div>
                        </div>
                        <button class="detail-btn w-100" onclick="viewDetails('${driver_id}')">
                            <span>Lihat detail</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 8px;">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Function to render all static cards
    function renderStaticCards() {
        const container = document.getElementById('operatorsContainer');
        if (!container) return;

        let html = '';
        staticOperatorsData.forEach(operatorData => {
            html += renderOperatorCard(operatorData);
        });

        container.innerHTML = html;

        // Draw charts after a short delay to ensure DOM is ready
        setTimeout(() => {
            staticOperatorsData.forEach((operatorData, index) => {
                const { driver_id, latest } = operatorData;
                const canvasId = `chart-${driver_id.replace(/\s+/g, '-')}`;
                
                // Generate different pattern for each card
                let earData;
                switch(index) {
                    case 0:
                        earData = generateEARDataPattern1(100); // Fluctuating untuk Driver-001
                        break;
                    case 1:
                        earData = generateEARDataPattern2(100); // Stabil untuk Driver-002
                        break;
                    case 2:
                        earData = generateEARDataPattern3(100); // Menurun untuk Driver-003
                        break;
                    case 3:
                        earData = generateEARDataPattern4(100); // Spiky untuk Driver-004
                        break;
                    default:
                        earData = generateEARDataPattern1(100);
                }
                
                const timeData = earData.map((_, i) => i);
                const threshold = latest.ear_threshold || 0.2;
                const earBandLow = latest.ear_band_low || 0.22;
                const earBandHigh = latest.ear_band_high || 0.3;

                drawEARChart(canvasId, earData, timeData, threshold, earBandLow, earBandHigh);
            });
        }, 100);
    }

    // Function to view details (simplified for static version)
    function viewDetails(driverId) {
        alert(`Detail untuk ${driverId}\n\nIni adalah versi statis. Data detail tidak tersedia.`);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Render static cards
        renderStaticCards();

        // Handle window resize with debounce
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                // Redraw all charts on resize using stored data
                staticOperatorsData.forEach((operatorData, index) => {
                    const { driver_id, latest } = operatorData;
                    const canvasId = `chart-${driver_id.replace(/\s+/g, '-')}`;
                    const data = canvasData[canvasId];
                    if (data) {
                        drawEARChart(
                            canvasId,
                            data.earData,
                            data.timeData,
                            data.threshold,
                            data.earBandLow,
                            data.earBandHigh
                        );
                    }
                });
            }, 250);
        });
    });
</script>

@endsection

