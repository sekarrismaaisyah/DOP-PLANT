@extends('layouts.master')

@section('title', 'DMS Dashboard')
@section('content')
<x-page-title title="DMS Dashboard" pagetitle="Driver Monitoring System - Real-time Monitoring" />

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
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }
    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .threshold-line {
        stroke-dasharray: 5,5;
        stroke-width: 2;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-1">Real-time Monitoring</h5>
                        <p class="text-muted mb-0">Data diperbarui setiap 2 detik</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="loading-spinner" id="loadingIndicator"></span>
                        <span class="text-muted small" id="lastUpdate">Memuat data...</span>
                    </div>
                </div>

                <div id="operatorsContainer">
                    <div class="empty-state">
                        <div class="empty-state-icon">📊</div>
                        <p>Memuat data operator...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Driver Details -->
<div class="modal fade" id="driverDetailModal" tabindex="-1" aria-labelledby="driverDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="driverDetailModalLabel">Detail Safety Score Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="driverDetailLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>
                <div id="driverDetailContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="driverLogsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Driver ID</th>
                                    <th>Timestamp</th>
                                    <th>EAR</th>
                                    <th>PERCLOS (60s)</th>
                                    <th>Blink (60s)</th>
                                    <th>Microsleep (60s)</th>
                                    <th>Fatigue</th>
                                    <th>Drift</th>
                                    <th>Safety Score</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="driverLogsTableBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="driverDetailError" style="display: none;" class="alert alert-danger">
                    <p class="mb-0">Gagal memuat data. Silakan coba lagi.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    let canvasData = {};
    let updateInterval;

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

    // Helper function to safely get number value
    function safeNumber(value, defaultValue = 0) {
        if (value === null || value === undefined || isNaN(value)) {
            return defaultValue;
        }
        const num = parseFloat(value);
        return isNaN(num) ? defaultValue : num;
    }

    // Function to render operator card
    function renderOperatorCard(operatorData) {
        const { driver_id, latest, chart_data } = operatorData;
        const statusClass = getStatusClass(latest.status);
        const statusText = formatStatus(latest.status);
        const canvasId = `chart-${driver_id.replace(/\s+/g, '-')}`;

        // Calculate threshold (60 for safety score)
        const threshold = 60;

        // Safely get values with defaults
        const safetyScore = safeNumber(latest.safety_score, 0);
        const fatigue = safeNumber(latest.fatigue, 0);
        const drift = safeNumber(latest.drift, 0);
        const perclos = safeNumber(latest.perclos_60s, 0);
        const slopeEar = safeNumber(latest.slope_ear_per_min, 0);
        const blinkCount = safeNumber(latest.blink_60s, 0);
        const microsleep = safeNumber(latest.microsleep_60s, 0);

        return `
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
        `;
    }

    // Function to update operator card data without recreating HTML
    function updateOperatorCard(driverId, latest, chartData) {
        const cardId = `operator-card-${driverId.replace(/\s+/g, '-')}`;
        const card = document.getElementById(cardId);
        
        if (!card) return false; // Card doesn't exist, need to create it
        
        // Update metrics without recreating the card
        const safetyScore = safeNumber(latest.safety_score, 0);
        const fatigue = safeNumber(latest.fatigue, 0);
        const drift = safeNumber(latest.drift, 0);
        const perclos = safeNumber(latest.perclos_60s, 0);
        const slopeEar = safeNumber(latest.slope_ear_per_min, 0);
        const blinkCount = safeNumber(latest.blink_60s, 0);
        const microsleep = safeNumber(latest.microsleep_60s, 0);
        const statusText = formatStatus(latest.status);
        const statusClass = getStatusClass(latest.status);
        
        // Update status badge
        const badge = card.querySelector('.status-badge');
        if (badge) {
            badge.className = `status-badge ${statusClass}`;
            badge.textContent = statusText;
        }
        
        // Update metric values using data attributes
        const updateMetric = (metricName, value) => {
            const metricItem = card.querySelector(`[data-metric="${metricName}"]`);
            if (metricItem) {
                const valueEl = metricItem.querySelector('.metric-value');
                if (valueEl) valueEl.textContent = value;
            }
        };
        
        // Update indicator values
        const updateIndicator = (indicatorName, value) => {
            const indicatorItem = card.querySelector(`[data-indicator="${indicatorName}"]`);
            if (indicatorItem) {
                const valueEl = indicatorItem.querySelector('.indicator-value');
                if (valueEl) valueEl.textContent = value || '-';
            }
        };
        
        updateMetric('safety-score', safeToFixed(safetyScore, 0));
        updateMetric('safety-band', statusText);
        updateMetric('fatigue', safeToFixed(fatigue, 0));
        updateMetric('drift', safeToFixed(drift, 0));
        updateMetric('perclos', safeToFixed(perclos * 100, 1) + '%');
        updateMetric('slope-ear', safeToFixed(slopeEar, 4));
        updateMetric('blink-count', blinkCount);
        updateMetric('microsleep', microsleep);
        
        // Update indicators (currently empty, but ready for future data)
        updateIndicator('fatigue', '-');
        updateIndicator('drift', '-');
        
        // Update chart smoothly - only if data changed
        const canvasId = `chart-${driverId.replace(/\s+/g, '-')}`;
        const threshold = latest.ear_threshold || 0.2;
        const earBandLow = latest.ear_band_low || 0.22;
        const earBandHigh = latest.ear_band_high || 0.3;
        
        // Check if canvas exists
        const canvas = document.getElementById(canvasId);
        if (canvas) {
            // Use requestAnimationFrame for smooth update
            requestAnimationFrame(() => {
                drawEARChart(
                    canvasId,
                    chartData.ear || [],
                    chartData.labels || [],
                    threshold,
                    earBandLow,
                    earBandHigh
                );
            });
        }
        
        return true;
    }

    // Function to fetch and update data
    async function fetchRealtimeData() {
        try {
            const response = await fetch('/api/dms/dashboard/realtime?minutes=60&limit=100');
            const result = await response.json();

            if (result.success && result.data) {
                const container = document.getElementById('operatorsContainer');
                
                if (result.data.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-state-icon">📊</div>
                            <p>Tidak ada data operator saat ini</p>
                        </div>
                    `;
                    return;
                }

                // Track which cards exist
                const existingCards = new Set();
                result.data.forEach(operatorData => {
                    const cardId = `operator-card-${operatorData.driver_id.replace(/\s+/g, '-')}`;
                    const card = document.getElementById(cardId);
                    if (card) {
                        existingCards.add(operatorData.driver_id);
                    }
                });

                // Update existing cards or create new ones
                const newCards = [];
                result.data.forEach(operatorData => {
                    const { driver_id, latest, chart_data } = operatorData;
                    
                    // Try to update existing card
                    const updated = updateOperatorCard(driver_id, latest, chart_data);
                    
                    if (!updated) {
                        // Card doesn't exist, need to create it
                        newCards.push(operatorData);
                    }
                });

                // Create new cards if any
                if (newCards.length > 0) {
                    newCards.forEach(operatorData => {
                        const { driver_id, latest, chart_data } = operatorData;
                        const cardHtml = renderOperatorCard(operatorData);
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = cardHtml;
                        const card = tempDiv.firstElementChild;
                        if (card) {
                            container.appendChild(card);
                            
                            // Draw chart for new card
                            const canvasId = `chart-${driver_id.replace(/\s+/g, '-')}`;
                            const threshold = latest.ear_threshold || 0.2;
                            const earBandLow = latest.ear_band_low || 0.22;
                            const earBandHigh = latest.ear_band_high || 0.3;
                            
                            setTimeout(() => {
                                drawEARChart(
                                    canvasId,
                                    chart_data.ear || [],
                                    chart_data.labels || [],
                                    threshold,
                                    earBandLow,
                                    earBandHigh
                                );
                            }, 50);
                        }
                    });
                }

                // Remove cards that no longer exist
                const currentDrivers = new Set(result.data.map(d => d.driver_id));
                const allCards = container.querySelectorAll('.operator-card');
                allCards.forEach(card => {
                    const cardId = card.id;
                    const driverId = cardId.replace('operator-card-', '').replace(/-/g, ' ');
                    if (!currentDrivers.has(driverId)) {
                        card.remove();
                    }
                });

                // Update last update time
                const now = new Date();
                document.getElementById('lastUpdate').textContent = 
                    `Terakhir diperbarui: ${now.toLocaleTimeString('id-ID')}`;
            }
        } catch (error) {
            console.error('Error fetching realtime data:', error);
        }
    }

    // Function to view details
    async function viewDetails(driverId) {
        const modalElement = document.getElementById('driverDetailModal');
        const modalTitle = document.getElementById('driverDetailModalLabel');
        const loadingDiv = document.getElementById('driverDetailLoading');
        const contentDiv = document.getElementById('driverDetailContent');
        const errorDiv = document.getElementById('driverDetailError');
        const tableBody = document.getElementById('driverLogsTableBody');

        // Set modal title
        modalTitle.textContent = `Detail Safety Score Logs - ${driverId}`;

        // Show loading, hide content and error
        loadingDiv.style.display = 'block';
        contentDiv.style.display = 'none';
        errorDiv.style.display = 'none';
        tableBody.innerHTML = '';

        // Show modal using Bootstrap 5
        let modal;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            modal = bootstrap.Modal.getOrCreateInstance(modalElement);
            modal.show();
        } else {
            // Fallback if Bootstrap is not available
            console.error('Bootstrap Modal is not available');
            return;
        }

        try {
            // Fetch driver logs
            const response = await fetch(`/api/dms/dashboard/driver-logs?driver_id=${encodeURIComponent(driverId)}&limit=1000`);
            const result = await response.json();

            if (result.success && result.data) {
                // Hide loading
                loadingDiv.style.display = 'none';

                if (result.data.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                Tidak ada data tersedia untuk driver ini
                            </td>
                        </tr>
                    `;
                } else {
                    // Build table rows
                    const rows = result.data.map(log => {
                        const statusClass = getStatusClass(log.status);
                        return `
                            <tr>
                                <td>${log.id || '-'}</td>
                                <td>${log.driver_id || '-'}</td>
                                <td>${log.timestamp || '-'}</td>
                                <td>${log.ear || '-'}</td>
                                <td>${log.perclos_60s || '-'}</td>
                                <td>${log.blink_60s || '-'}</td>
                                <td>${log.microsleep_60s || '-'}</td>
                                <td>${log.fatigue || '-'}</td>
                                <td>${log.drift || '-'}</td>
                                <td>${log.safety_score || '-'}</td>
                                <td><span class="status-badge ${statusClass}">${log.status || '-'}</span></td>
                            </tr>
                        `;
                    }).join('');

                    tableBody.innerHTML = rows;
                }

                // Show content
                contentDiv.style.display = 'block';
            } else {
                // Show error
                loadingDiv.style.display = 'none';
                errorDiv.style.display = 'block';
                errorDiv.querySelector('p').textContent = result.message || 'Gagal memuat data. Silakan coba lagi.';
            }
        } catch (error) {
            console.error('Error fetching driver logs:', error);
            // Show error
            loadingDiv.style.display = 'none';
            errorDiv.style.display = 'block';
            errorDiv.querySelector('p').textContent = 'Terjadi kesalahan saat memuat data. Silakan coba lagi.';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch data immediately
        fetchRealtimeData();

        // Set up interval for realtime updates (2 seconds)
        updateInterval = setInterval(fetchRealtimeData, 2000);

        // Handle window resize with debounce
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                // Redraw all charts on resize
                Object.keys(canvasData).forEach(canvasId => {
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

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
        });
    });
</script>

@endsection

