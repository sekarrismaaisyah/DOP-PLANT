<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>EAR Dashboard – Data Riil</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
<style>
  :root {
    --bg: #f1f5f9;
    --bg-card: #ffffff;
    --fg: #0f172a;
    --muted: #64748b;
    --grid: #e2e8f0;
    --primary: #0f172a;
    --primary-light: #1e293b;
    --amber: #f59e0b;
    --blue: #3b82f6;
    --red: #ef4444;
    --green: #10b981;
    --border: #e2e8f0;
    --shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    --shadow-md: 0 4px 14px rgba(15, 23, 42, 0.08), 0 2px 6px rgba(15, 23, 42, 0.04);
    --shadow-lg: 0 10px 40px -10px rgba(15, 23, 42, 0.12);
    --radius: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --transition: 0.2s ease;
  }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    padding: 28px 24px 40px;
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg);
    color: var(--fg);
    font-size: 14px;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
    min-height: 100vh;
  }
  .wrap { max-width: 1280px; margin: 0 auto; display: flex; flex-direction: column; gap: 28px; }
  .row { display: grid; gap: 16px; }
  .row.ops { grid-template-columns: repeat(1, minmax(0, 1fr)); }
  @media (min-width: 800px) { .row.ops { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  @media (min-width: 1100px) { .row.ops { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
  .card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 22px;
    box-shadow: var(--shadow);
    transition: box-shadow var(--transition), border-color var(--transition), transform var(--transition);
  }
  .card:hover { box-shadow: var(--shadow-md); }
  .card.card-elevated { box-shadow: var(--shadow-md); }
  .muted { color: var(--muted); font-weight: 400; }
  .tag {
    padding: 5px 14px;
    border-radius: 999px;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
  }
  .tag.high { background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); }
  .tag.med { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); }
  .tag.low { background: linear-gradient(135deg, #059669 0%, #047857 100%); }
  .btn {
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 9px 16px;
    background: var(--bg-card);
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: var(--fg);
    transition: background var(--transition), border-color var(--transition), color var(--transition), box-shadow var(--transition);
  }
  .btn:hover { background: var(--bg); border-color: #cbd5e1; box-shadow: 0 2px 8px rgba(15,23,42,0.06); }
  .btn-primary {
    border: none;
    color: #fff;
    background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
    box-shadow: 0 2px 8px rgba(15,23,42,0.2);
  }
  .btn-primary:hover { background: linear-gradient(135deg, #475569 0%, #334155 100%); box-shadow: 0 4px 12px rgba(15,23,42,0.25); }
  .kpi {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin-top: 14px;
  }
  .kpi .label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--muted);
    font-weight: 500;
  }
  .kpi .val { font-weight: 600; font-size: 15px; }
  .kpi-item {
    padding: 10px 12px;
    background: var(--bg);
    border-radius: var(--radius);
    border-left: 3px solid var(--border);
  }
  .kpi-item.safe { border-left-color: var(--green); }
  .kpi-item.caution { border-left-color: #d97706; }
  .kpi-item.attention { border-left-color: var(--red); }
  .flex { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .between { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
  .summary { display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 16px; }
  @media (min-width: 900px) { .summary { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
  .summary .card {
    border-left: 4px solid var(--border);
  }
  .summary .card.summary-safe { border-left-color: var(--green); }
  .summary .card.summary-caution { border-left-color: #d97706; }
  .summary .card.summary-attention { border-left-color: var(--red); }
  .tooltip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    border: 1px solid #cbd5e1;
    border-radius: 999px;
    font-size: 10px;
    color: #475569;
    margin-left: 4px;
  }
  #testStatus { font-size: 12px; font-weight: 500; }
  .page-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 50%, #334155 100%);
    color: #fff;
    padding: 28px 32px;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
  }
  .page-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 40%;
    height: 100%;
    background: radial-gradient(ellipse at 100% 50%, rgba(255,255,255,0.06) 0%, transparent 60%);
    pointer-events: none;
  }
  .page-header h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    position: relative;
  }
  .page-header .muted { color: rgba(255,255,255,0.85); }
  .live-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #22c55e;
    border-radius: 50%;
    margin-right: 6px;
    animation: pulse 2s ease-in-out infinite;
    vertical-align: middle;
  }
  @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
  .section-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--fg);
    margin-bottom: 4px;
  }
  .waveform-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 16px 24px;
    margin-bottom: 14px;
    padding: 12px 16px;
    background: var(--bg);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    font-size: 12px;
    color: var(--muted);
  }
  .waveform-legend span { display: inline-flex; align-items: center; gap: 6px; }
  .waveform-legend .dot { width: 10px; height: 10px; border-radius: 2px; }
  .waveform-legend .line-dash { width: 16px; height: 2px; background: currentColor; }
  .waveform-legend .line-dash.dashed { background: repeating-linear-gradient(90deg, currentColor 0, currentColor 4px, transparent 4px, transparent 8px); }
  .canvas-wrap {
    border: 1px solid var(--border);
    border-radius: var(--radius);
    background: #fafbfc;
    overflow: hidden;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
  }
  footer.dash-footer {
    font-size: 12px;
    color: var(--muted);
    padding: 20px 0 0;
    border-top: 1px solid var(--border);
    margin-top: 12px;
  }
  /* Operator card selected state */
  .op-card.selected {
    box-shadow: 0 0 0 2px var(--primary), var(--shadow-md);
    border-color: var(--primary);
  }
  /* Modal improvements */
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.5);
    backdrop-filter: blur(6px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 24px;
    font-family: 'Poppins', sans-serif;
    animation: fadeIn 0.2s ease;
  }
  @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
  .modal-content {
    background: var(--bg-card);
    border-radius: var(--radius-xl);
    max-width: 960px;
    width: 100%;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    border: 1px solid var(--border);
    animation: slideUp 0.25s ease;
  }
  @keyframes slideUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
  .modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--bg);
  }
  .modal-body { padding: 24px; }
  .modal-body table tbody tr:hover { background: var(--bg); }
  .modal-body table tbody tr:nth-child(even) { background: rgba(248,250,252,0.6); }
  .modal-body table tbody tr:nth-child(even):hover { background: var(--bg); }
  .op-btn { transition: transform 0.2s ease, box-shadow 0.2s ease; }
  .op-btn:hover { filter: brightness(1.05); }
  .op-btn-active { outline: 2px solid rgba(255,255,255,0.8); outline-offset: 2px; }
</style>
</head>
<body>
<div class="wrap">
  <header class="page-header between">
    <div>
      <h1>EAR Dashboard – Data Riil Multi Operator</h1>
      <div class="muted" style="font-size:13px;margin-top:6px"><span class="live-dot"></span>Monitoring real-time fatigue &amp; safety score</div>
    </div>
    <div id="opButtons" class="flex" style="gap:10px"></div>
  </header>

  <section class="row ops" id="opCards"></section>

  <section class="card card-elevated">
    <div class="between" style="margin-bottom:16px">
      <div>
        <div id="detailTitle" class="section-title">Detail Waveform</div>
        <div class="muted" style="font-size:12px;line-height:1.5;max-width:720px" id="detailDesc">Pilih operator untuk melihat grafik.</div>
      </div>
      <div class="muted" id="timeNow" style="font-size:12px;font-weight:500"></div>
    </div>
    <div class="waveform-legend" id="waveformLegend" style="display:none">
      <span><span class="dot" style="background:#111827"></span> EAR historis (60s)</span>
      <span><span class="line-dash dashed" style="color:#8b5cf6"></span> Prediksi EAR (60s)</span>
      <span><span class="line-dash dashed" style="color:#facc15"></span> T_close</span>
      <span><span class="dot" style="background:#a7f3d0;opacity:0.8"></span> Rentang EAR personal</span>
      <span><span class="dot" style="background:#3b82f6"></span> Blink</span>
      <span><span class="dot" style="background:#ef4444;opacity:0.7"></span> Microsleep</span>
    </div>
    <div class="canvas-wrap">
      <canvas id="bigCanvas" width="1200" height="260" style="width:100%;height:260px;display:block"></canvas>
    </div>
    <div id="detailKPIs" class="kpi" style="grid-template-columns:repeat(8,minmax(0,1fr));margin-top:10px"></div>

    <div class="row" style="grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-top:20px">
      <div class="card" style="padding:20px;border-left:4px solid var(--amber)">
        <div class="section-title" style="margin-bottom:10px">Indikator Fatigue</div>
        <ul id="fatigueList" style="margin:0;padding-left:20px;line-height:1.7;color:var(--muted);font-size:13px"></ul>
      </div>
      <div class="card" style="padding:20px;border-left:4px solid var(--blue)">
        <div class="section-title" style="margin-bottom:10px">Indikator Drift Pattern</div>
        <ul id="driftList" style="margin:0;padding-left:20px;line-height:1.7;color:var(--muted);font-size:13px"></ul>
      </div>
    </div>
  </section>

  <section class="card">
    <div class="between" style="margin-bottom:16px">
      <div class="section-title">Summary Insight</div>
      <div id="testStatus" class="muted"><span class="live-dot"></span> Memuat...</div>
    </div>
    <div id="summary" class="summary"></div>
  </section>

  <footer class="dash-footer muted">Data riil dari API DMS. Diperbarui setiap 2 detik. Sumber: /api/dms/dashboard/realtime</footer>
</div>

<!-- Modal Detail Log Driver -->
<div id="driverDetailModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <strong id="driverDetailModalTitle" style="font-size:16px;font-weight:600">Detail Safety Score Logs</strong>
      <button type="button" id="driverDetailModalClose" class="btn btn-primary">Tutup</button>
    </div>
    <div class="modal-body">
      <div id="driverDetailLoading" style="text-align:center;padding:40px;color:var(--muted);font-weight:500">Memuat data...</div>
      <div id="driverDetailContent" style="display:none">
        <div style="overflow-x:auto;border-radius:var(--radius);border:1px solid var(--border)">
          <table style="width:100%;border-collapse:collapse;font-size:13px;font-family:'Poppins',sans-serif">
            <thead><tr style="background:var(--bg);text-align:left">
              <th style="padding:12px 16px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.05em">ID</th>
              <th style="padding:12px 16px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.05em">Driver ID</th>
              <th style="padding:12px 16px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.05em">Timestamp</th>
              <th style="padding:12px 16px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.05em">EAR</th>
              <th style="padding:12px 16px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.05em">PERCLOS (60s)</th>
              <th style="padding:12px 16px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.05em">Blink (60s)</th>
              <th style="padding:12px 16px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.05em">Microsleep (60s)</th>
              <th style="padding:12px 16px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.05em">Fatigue</th>
              <th style="padding:12px 16px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.05em">Drift</th>
              <th style="padding:12px 16px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.05em">Safety Score</th>
              <th style="padding:12px 16px;border-bottom:2px solid var(--border);font-weight:600;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:0.05em">Status</th>
            </tr></thead>
            <tbody id="driverLogsTableBody"></tbody>
          </table>
        </div>
      </div>
      <div id="driverDetailError" style="display:none;padding:16px 20px;background:#fef2f2;color:#991b1b;border-radius:var(--radius);font-weight:500;border:1px solid #fecaca">Gagal memuat data.</div>
    </div>
  </div>
</div>

<script>
(function(){
  const $ = (id) => document.getElementById(id);
  const API_REALTIME = '/api/dms/dashboard/realtime?minutes=60&limit=100';
  const POLL_MS = 2000;

  let operatorsData = [];
  let selectedIndex = 0;
  let pollTimer = null;

  function safeNum(v, def){ return v != null && !isNaN(parseFloat(v)) ? parseFloat(v) : (def ?? 0); }
  function safeToFixed(v, d){ return safeNum(v, 0).toFixed(d ?? 0); }
  function getStatusClass(s){
    const t = (s || '').toLowerCase();
    if(t === 'safe') return 'safe'; if(t === 'caution') return 'caution'; if(t === 'attention') return 'attention';
    return 'medium';
  }
  function formatStatus(s){
    const t = (s || '').toLowerCase();
    if(t === 'safe') return 'Safe'; if(t === 'caution') return 'Caution'; if(t === 'attention') return 'Attention';
    return 'Medium';
  }
  function safetyBand(score){
    const s = safeNum(score, 0);
    return s >= 80 ? 'Safe' : s >= 60 ? 'Caution' : 'Attention';
  }
  function safetyColor(score){
    const s = safeNum(score, 0);
    return s >= 80 ? 'color:var(--green)' : s >= 60 ? 'color:#d97706' : 'color:#dc2626';
  }
  function priority(fatigue){
    const f = safeNum(fatigue, 0);
    return f >= 70 ? 'High' : f >= 40 ? 'Medium' : 'Low';
  }
  function badge(p){ return p === 'High' ? 'high' : p === 'Medium' ? 'med' : 'low'; }

  const minEAR = 0.05, maxEAR = 0.4;
  const PRED_SEC = 60; // prediksi 60 detik ke depan
  const PRED_POINTS = 60; // 1 titik per detik

  /** Prediksi EAR 60 detik ke depan dari slope (konsep sama seperti sebelumnya, horizon diperpanjang) */
  function buildEarPred(earBuf, slopePerMin, threshold, earBandLow, earBandHigh){
    if(!earBuf || earBuf.length === 0) return [];
    const last = earBuf[earBuf.length - 1];
    const slopePerSec = (slopePerMin || 0) / 60;
    const midBand = ((earBandLow || 0.22) + (earBandHigh || 0.3)) / 2;
    const k = 0.55;
    const preds = [];
    let x = last;
    const dt = PRED_SEC / Math.max(1, PRED_POINTS);
    for(let i = 1; i <= PRED_POINTS; i++){
      const proj = x + slopePerSec * dt;
      x = proj + (midBand - proj) * (1 - Math.exp(-k * dt));
      x = Math.max(minEAR, Math.min(maxEAR, x));
      preds.push(x);
    }
    return preds;
  }

  /** Grafik kecil di kartu – mengikuti desain.blade.php: grid vertikal 8px, band hijau (rentang EAR), garis EAR #111827, T_close putus-putus */
  function drawSmall(earData, threshold, earBandLow, earBandHigh, canvasId){
    const canvas = document.getElementById(canvasId);
    if(!canvas) return;
    const ctx = canvas.getContext('2d');
    const W = canvas.width, H = canvas.height;
    ctx.clearRect(0, 0, W, H);
    ctx.strokeStyle = '#e5e7eb';
    ctx.lineWidth = 1;
    for(let x = 0; x < W; x += 8){ ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, H); ctx.stroke(); }
    const yOf = (v) => H - ((v - minEAR) / (maxEAR - minEAR)) * H;
    if(earBandLow != null && earBandHigh != null){
      ctx.fillStyle = '#a7f3d0';
      ctx.globalAlpha = 0.25;
      const yHigh = yOf(earBandLow), yLow = yOf(earBandHigh);
      ctx.fillRect(0, Math.min(yHigh, yLow), W, Math.abs(yLow - yHigh));
      ctx.globalAlpha = 1;
    }
    if(earData && earData.length > 0){
      ctx.strokeStyle = '#111827';
      ctx.lineWidth = 1.5;
      ctx.beginPath();
      for(let i = 0; i < earData.length; i++){
        const x = (i / earData.length) * W;
        const y = yOf(earData[i]);
        if(i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
      }
      ctx.stroke();
    }
    if(threshold != null){
      ctx.setLineDash([3, 3]);
      ctx.strokeStyle = '#f59e0b';
      ctx.beginPath();
      ctx.moveTo(0, yOf(threshold));
      ctx.lineTo(W, yOf(threshold));
      ctx.stroke();
      ctx.setLineDash([]);
    }
  }

  /** Grafik besar detail – sesuai case:
   *  - Garis hitam solid  : EAR historis (60 detik terakhir)
   *  - Garis ungu putus   : Prediksi EAR (60 detik ke depan)
   *  - Garis kuning putus : Threshold T_close
   *  - Area hijau         : Band EAR personal
   *  - Garis biru vertikal: Waktu blink
   *  - Area merah         : Waktu microsleep
   */
  function drawBig(earBuf, earPred, threshold, earBandLow, earBandHigh, blinkCount, microCount){
    const cvs = document.getElementById('bigCanvas');
    if(!cvs) return;
    const ctx = cvs.getContext('2d');
    const W = cvs.width, H = cvs.height;
    ctx.clearRect(0, 0, W, H);
    ctx.lineWidth = 1;
    ctx.strokeStyle = getComputedStyle(document.body).getPropertyValue('--grid') || '#e5e7eb';
    for(let x = 0; x < W; x += 10){ ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, H); ctx.stroke(); }
    for(let y = 0; y < H; y += 10){ ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(W, y); ctx.stroke(); }
    const yOf = (v) => H - ((v - minEAR) / (maxEAR - minEAR)) * H;
    if(earBandLow != null && earBandHigh != null){
      ctx.fillStyle = '#a7f3d0';
      ctx.globalAlpha = 0.25;
      const yHigh = yOf(earBandLow), yLow = yOf(earBandHigh);
      ctx.fillRect(0, Math.min(yHigh, yLow), W, Math.abs(yLow - yHigh));
      ctx.globalAlpha = 1;
    }
    if(threshold != null){
      ctx.setLineDash([4, 4]);
      ctx.strokeStyle = '#facc15'; // kuning
      ctx.lineWidth = 2;
      ctx.beginPath();
      ctx.moveTo(0, yOf(threshold));
      ctx.lineTo(W, yOf(threshold));
      ctx.stroke();
      ctx.setLineDash([]);
    }
    const totalN = (earBuf ? earBuf.length : 0) + (earPred && earPred.length ? earPred.length : 0);
    const xOf = (i) => (i / Math.max(1, totalN - 1)) * W;
    // microsleep blocks (area merah transparan, disebar merata sepanjang sumbu X menggunakan agregat 60 detik)
    if(microCount && microCount > 0){
      const span = W / (microCount + 1);
      const blockW = Math.max(4, span * 0.5);
      ctx.save();
      ctx.fillStyle = '#ef4444';
      ctx.globalAlpha = 0.15;
      for(let k = 0; k < microCount; k++){
        const center = (k + 1) * span;
        const x0 = Math.max(0, center - blockW / 2);
        ctx.fillRect(x0, 0, Math.min(blockW, W - x0), H);
      }
      ctx.globalAlpha = 1;
      ctx.restore();
    }
    if(earBuf && earBuf.length > 0){
      ctx.strokeStyle = '#111827';
      ctx.lineWidth = 2;
      ctx.beginPath();
      for(let i = 0; i < earBuf.length; i++){
        const x = xOf(i), y = yOf(earBuf[i]);
        if(i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
      }
      ctx.stroke();
    }
    if(earPred && earPred.length > 0 && earBuf && earBuf.length > 0){
      ctx.save();
      ctx.strokeStyle = '#8b5cf6';
      ctx.setLineDash([6, 4]);
      ctx.lineWidth = 2;
      ctx.beginPath();
      ctx.moveTo(xOf(earBuf.length - 1), yOf(earBuf[earBuf.length - 1]));
      for(let j = 0; j < earPred.length; j++) ctx.lineTo(xOf(earBuf.length + j), yOf(earPred[j]));
      ctx.stroke();
      ctx.setLineDash([]);
      ctx.restore();
      const xStart = xOf(earBuf.length);
      ctx.save();
      ctx.fillStyle = '#8b5cf6';
      ctx.globalAlpha = 0.08;
      ctx.fillRect(xStart, 0, W - xStart, H);
      ctx.globalAlpha = 1;
      ctx.restore();
    }
    // blink markers (garis biru vertikal, disebar merata sepanjang sumbu X menggunakan agregat 60 detik)
    if(blinkCount && blinkCount > 0){
      ctx.save();
      ctx.strokeStyle = '#3b82f6';
      ctx.lineWidth = 1.5;
      for(let k = 0; k < blinkCount; k++){
        const t = (k + 1) / (blinkCount + 1);
        const x = t * W;
        ctx.beginPath();
        ctx.moveTo(x, 0);
        ctx.lineTo(x, H);
        ctx.stroke();
      }
      ctx.restore();
    }
  }

  function renderOperatorButtons(){
    const wrap = $('opButtons');
    wrap.innerHTML = '';
    operatorsData.forEach((op, i) => {
      const f = safeNum(op.latest.fatigue, 0);
      const p = priority(f);
      const b = document.createElement('button');
      b.className = 'btn op-btn' + (i === selectedIndex ? ' op-btn-active' : '');
      b.textContent = op.driver_id;
      b.style.color = '#fff';
      b.style.border = 'none';
      b.style.background = p === 'High' ? 'linear-gradient(135deg,#dc2626,#b91c1c)' : p === 'Medium' ? 'linear-gradient(135deg,#d97706,#b45309)' : 'linear-gradient(135deg,#059669,#047857)';
      b.style.fontWeight = '600';
      b.style.padding = '9px 18px';
      b.style.borderRadius = '10px';
      b.onclick = () => { selectedIndex = i; updateDetail(); renderCards(); renderOperatorButtons(); };
      if(i === selectedIndex){ b.style.boxShadow = '0 4px 14px rgba(0,0,0,0.25)'; b.style.transform = 'scale(1.02)'; } else { b.style.boxShadow = '0 2px 6px rgba(0,0,0,0.15)'; b.style.transform = ''; }
      wrap.appendChild(b);
    });
  }

  function renderCards(){
    const container = $('opCards');
    container.innerHTML = '';
    operatorsData.forEach((op, i) => {
      const l = op.latest;
      const cd = op.chart_data || {};
      const ear = cd.ear || [];
      const thr = safeNum(l.ear_threshold, 0.2);
      const bandLow = safeNum(l.ear_band_low, 0.22);
      const bandHigh = safeNum(l.ear_band_high, 0.3);
      const safety = safeNum(l.safety_score, 0);
      const fatigue = safeNum(l.fatigue, 0);
      const drift = safeNum(l.drift, 0);
      const perclos = safeNum(l.perclos_60s, 0);
      const blink = safeNum(l.blink_60s, 0);
      const micro = safeNum(l.microsleep_60s, 0);
      const slope = safeNum(l.slope_ear_per_min, 0);
      const statusText = formatStatus(l.status);
      const p = priority(fatigue);
      const canvasId = 'small-' + i;
      const card = document.createElement('div');
      card.className = 'card op-card' + (i === selectedIndex ? ' selected' : '');
      card.innerHTML = `
        <div class="between" style="margin-bottom:10px">
          <div class="section-title" style="margin:0">${op.driver_id}</div>
          <span class="tag ${badge(p)}">${p}</span>
        </div>
        <canvas id="${canvasId}" width="320" height="90" style="width:100%;height:90px;border:1px solid var(--border);border-radius:var(--radius);background:#f8fafc"></canvas>
        <div class="kpi">
          <div><div class="label">SafetyScore</div><div class="val" style="${safetyColor(safety)}">${safeToFixed(safety,0)}</div></div>
          <div><div class="label">Fatigue</div><div class="val">${safeToFixed(fatigue,0)}</div></div>
          <div><div class="label">Drift</div><div class="val">${safeToFixed(drift,0)}</div></div>
          <div><div class="label">PERCLOS</div><div class="val">${safeToFixed(perclos * 100, 1)}%</div></div>
          <div><div class="label">Blink (60s)</div><div class="val">${blink}</div></div>
          <div><div class="label">Microsleep (60s)</div><div class="val">${micro}</div></div>
          <div><div class="label">Slope EAR/min</div><div class="val">${safeToFixed(slope, 4)}</div></div>
          <div><div class="label">Status</div><div class="val" style="${safetyColor(safety)}">${statusText}</div></div>
        </div>
        <div class="row" style="grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:12px;font-size:12px">
          <div><div class="section-title" style="margin-bottom:2px;font-size:13px">Indikator Fatigue</div><div class="muted" style="font-size:12px">Data dari API (PERCLOS, blink, microsleep)</div></div>
          <div><div class="section-title" style="margin-bottom:2px;font-size:13px">Drift Pattern</div><div class="muted" style="font-size:12px">Slope EAR: ${safeToFixed(slope, 4)}</div></div>
        </div>
        <div class="flex" style="margin-top:12px">
          <button class="btn" data-driver="${op.driver_id.replace(/"/g, '&quot;')}">Lihat detail log</button>
        </div>
      `;
      container.appendChild(card);
      drawSmall(ear, thr, bandLow, bandHigh, canvasId);
      card.querySelector('button[data-driver]').onclick = () => { viewDetails(op.driver_id); };
    });
  }

  function updateDetail(){
    if(!operatorsData.length){
      $('detailTitle').textContent = 'Detail Waveform';
      $('timeNow').textContent = '';
      var le = document.getElementById('waveformLegend');
      if(le) le.style.display = 'none';
      return;
    }
    const op = operatorsData[selectedIndex];
    const l = op.latest;
    const cd = op.chart_data || {};
    const ear = cd.ear || [];
    const labels = cd.labels || [];
    const thr = safeNum(l.ear_threshold, 0.2);
    const bandLow = safeNum(l.ear_band_low, 0.22);
    const bandHigh = safeNum(l.ear_band_high, 0.3);
    const safety = safeNum(l.safety_score, 0);
    const fatigue = safeNum(l.fatigue, 0);
    const drift = safeNum(l.drift, 0);
    const perclos = safeNum(l.perclos_60s, 0);
    const blink = safeNum(l.blink_60s, 0);
    const micro = safeNum(l.microsleep_60s, 0);
    const slope = safeNum(l.slope_ear_per_min, 0);
    const lastTime = labels.length ? labels[labels.length - 1] : '';

    $('detailTitle').textContent = 'Detail Waveform – ' + op.driver_id;
    $('timeNow').textContent = labels.length ? 'Terakhir: ' + lastTime : '';
    var legendEl = document.getElementById('waveformLegend');
    if(legendEl){ legendEl.style.display = 'flex'; }

    const earPred = buildEarPred(ear, slope, thr, bandLow, bandHigh);
    drawBig(ear, earPred, thr, bandLow, bandHigh, blink, micro);

    const dk = $('detailKPIs');
    dk.innerHTML = `
      <div><div class="label">Driver SafetyScore</div><div class="val" style="${safetyColor(safety)}">${safeToFixed(safety,0)}</div></div>
      <div><div class="label">Fatigue</div><div class="val">${safeToFixed(fatigue,0)}</div></div>
      <div><div class="label">Drift</div><div class="val">${safeToFixed(drift,0)}</div></div>
      <div><div class="label">PERCLOS (60s)</div><div class="val">${safeToFixed(perclos * 100, 1)}%</div></div>
      <div><div class="label">Blink (60s)</div><div class="val">${blink}</div></div>
      <div><div class="label">Microsleep (60s)</div><div class="val">${micro}</div></div>
      <div><div class="label">Slope EAR/min</div><div class="val">${safeToFixed(slope, 4)}</div></div>
      <div><div class="label">Status</div><div class="val" style="${safetyColor(safety)}">${safeToFixed(safety,0)} · ${safetyBand(safety)}</div></div>
    `;

    const fl = $('fatigueList');
    fl.innerHTML = `
      <li>PERCLOS: ${safeToFixed(perclos * 100, 1)}%</li>
      <li>Blink (60s): ${blink}</li>
      <li>Microsleep (60s): ${micro}</li>
      <li>Data dari API realtime (timestamp: ${l.timestamp || '-'})</li>
    `;

    const dl = $('driftList');
    dl.innerHTML = `
      <li>Slope EAR (/min): ${safeToFixed(slope, 4)}</li>
      <li>EAR band: ${safeToFixed(bandLow, 3)} – ${safeToFixed(bandHigh, 3)}</li>
      <li>Threshold (T_close): ${safeToFixed(thr, 3)}</li>
    `;

    const summary = $('summary');
    summary.innerHTML = '';
    operatorsData.forEach((o) => {
      const ll = o.latest;
      const safetyVal = safeNum(ll.safety_score, 0);
      const band = safetyVal >= 80 ? 'Safe' : safetyVal >= 60 ? 'Caution' : 'Attention';
      const summaryClass = band === 'Safe' ? 'summary-safe' : band === 'Caution' ? 'summary-caution' : 'summary-attention';
      const items = [];
      if(safeNum(ll.microsleep_60s, 0) > 0) items.push('Microsleep terdeteksi');
      if(safeNum(ll.perclos_60s, 0) >= 0.2) items.push('PERCLOS tinggi');
      if(safeNum(ll.slope_ear_per_min, 0) < -0.02) items.push('EAR menurun (slope negatif)');
      if(items.length === 0) items.push('Stabil');
      const card = document.createElement('div');
      card.className = 'card ' + summaryClass;
      card.innerHTML =
        '<div class="between" style="margin-bottom:10px"><div class="section-title" style="margin:0">' + o.driver_id + '</div><div style="' + safetyColor(safetyVal) + ';font-weight:600;font-size:14px">' + safeToFixed(safetyVal, 0) + ' · ' + safetyBand(safetyVal) + '</div></div>' +
        '<ul style="margin:0;padding-left:20px;line-height:1.6;color:var(--muted);font-size:13px">' + items.map(t => '<li>' + t + '</li>').join('') + '</ul>';
      summary.appendChild(card);
    });
  }

  async function fetchRealtimeData(){
    try {
      const response = await fetch(API_REALTIME);
      const result = await response.json();
      if(result.success && result.data && Array.isArray(result.data)){
        operatorsData = result.data;
        if(operatorsData.length === 0){
          $('opCards').innerHTML = '<div class="card muted" style="text-align:center;padding:40px;font-weight:500;color:var(--muted)">Tidak ada data operator saat ini.</div>';
          $('detailTitle').textContent = 'Detail Waveform';
          $('timeNow').textContent = '';
          $('summary').innerHTML = '';
          $('testStatus').innerHTML = '<span class="live-dot"></span> Terakhir diperbarui: ' + new Date().toLocaleTimeString('id-ID');
          $('testStatus').style.color = 'var(--muted)';
          return;
        }
        renderOperatorButtons();
        renderCards();
        updateDetail();
        $('testStatus').innerHTML = '<span class="live-dot"></span> Terakhir diperbarui: ' + new Date().toLocaleTimeString('id-ID');
        $('testStatus').style.color = 'var(--green)';
      } else {
        $('testStatus').innerHTML = 'Gagal memuat data';
        $('testStatus').style.color = '#dc2626';
      }
    } catch(err){
      console.error('fetchRealtimeData', err);
      $('testStatus').innerHTML = 'Error: ' + (err.message || 'Gagal memuat');
      $('testStatus').style.color = '#dc2626';
    }
  }

  async function viewDetails(driverId){
    const modal = document.getElementById('driverDetailModal');
    const titleEl = document.getElementById('driverDetailModalTitle');
    const loadingEl = document.getElementById('driverDetailLoading');
    const contentEl = document.getElementById('driverDetailContent');
    const errorEl = document.getElementById('driverDetailError');
    const tbody = document.getElementById('driverLogsTableBody');
    if(!modal) return;
    titleEl.textContent = 'Detail Safety Score Logs – ' + driverId;
    modal.style.display = 'flex';
    loadingEl.style.display = 'block';
    contentEl.style.display = 'none';
    errorEl.style.display = 'none';
    tbody.innerHTML = '';
    try {
      const res = await fetch('/api/dms/dashboard/driver-logs?driver_id=' + encodeURIComponent(driverId) + '&limit=1000');
      const result = await res.json();
      loadingEl.style.display = 'none';
      if(result.success && result.data && result.data.length){
        tbody.innerHTML = result.data.map(log => {
          const sc = getStatusClass(log.status);
          return '<tr><td>' + (log.id || '-') + '</td><td>' + (log.driver_id || '-') + '</td><td>' + (log.timestamp || '-') + '</td><td>' + (log.ear ?? '-') + '</td><td>' + (log.perclos_60s ?? '-') + '</td><td>' + (log.blink_60s ?? '-') + '</td><td>' + (log.microsleep_60s ?? '-') + '</td><td>' + (log.fatigue ?? '-') + '</td><td>' + (log.drift ?? '-') + '</td><td>' + (log.safety_score ?? '-') + '</td><td><span class="tag ' + sc + '">' + (log.status || '-') + '</span></td></tr>';
        }).join('');
        contentEl.style.display = 'block';
      } else {
        tbody.innerHTML = '<tr><td colspan="11" style="padding:16px;text-align:center" class="muted">Tidak ada data untuk driver ini.</td></tr>';
        contentEl.style.display = 'block';
      }
    } catch(e){
      loadingEl.style.display = 'none';
      errorEl.style.display = 'block';
      errorEl.textContent = 'Gagal memuat data. ' + (e.message || '');
    }
  }

  document.getElementById('driverDetailModalClose').onclick = function(){
    document.getElementById('driverDetailModal').style.display = 'none';
  };
  document.getElementById('driverDetailModal').addEventListener('click', function(e){
    if(e.target === this) this.style.display = 'none';
  });

  fetchRealtimeData();
  pollTimer = setInterval(fetchRealtimeData, POLL_MS);
  window.addEventListener('beforeunload', function(){ if(pollTimer) clearInterval(pollTimer); });
})();
</script>
</body>
</html>
