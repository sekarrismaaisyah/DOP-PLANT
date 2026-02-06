<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>EAR Dashboard – Data Riil</title>
<style>
  :root{--bg:#fff;--fg:#111;--muted:#666;--grid:#e5e7eb;--primary:#111827;--amber:#f59e0b;--blue:#3b82f6;--red:#ef4444;--green:#10b981}
  *{box-sizing:border-box}
  body{margin:0;padding:16px;font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,Noto Sans,sans-serif;background:var(--bg);color:var(--fg)}
  .wrap{max-width:1200px;margin:0 auto;display:flex;flex-direction:column;gap:16px}
  .row{display:grid;gap:12px}
  .row.ops{grid-template-columns:repeat(1,minmax(0,1fr))}
  @media (min-width:800px){.row.ops{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media (min-width:1100px){.row.ops{grid-template-columns:repeat(4,minmax(0,1fr))}}
  .card{border:1px solid #ddd;border-radius:14px;padding:12px}
  .muted{color:var(--muted)}
  .tag{padding:2px 8px;border-radius:999px;color:#fff;font-size:12px}
  .tag.high{background:#dc2626}.tag.med{background:#d97706}.tag.low{background:#059669}
  .btn{border:1px solid #ddd;border-radius:10px;padding:6px 10px;background:#fff;cursor:pointer}
  .kpi{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:8px}
  .kpi .label{font-size:10px;text-transform:uppercase;letter-spacing:.04em;color:#6b7280}
  .kpi .val{font-weight:600}
  .flex{display:flex;align-items:center;gap:8px}
  .between{display:flex;align-items:center;justify-content:space-between}
  .summary{display:grid;grid-template-columns:repeat(1,minmax(0,1fr));gap:12px}
  @media (min-width:900px){.summary{grid-template-columns:repeat(4,minmax(0,1fr))}}
  .tooltip{display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border:1px solid #cbd5e1;border-radius:999px;font-size:10px;color:#475569;margin-left:4px}
  #testStatus{font-size:12px}
</style>
</head>
<body>
<div class="wrap">
  <header class="between">
    <h1 style="margin:0;font-size:20px;font-weight:700">EAR Dashboard – Data Riil Multi Operator</h1>
    <div id="opButtons" class="flex"></div>
  </header>

  <section class="row ops" id="opCards"></section>

  <section class="card">
    <div class="between" style="margin-bottom:6px">
      <div>
        <div id="detailTitle" style="font-weight:600">Detail Waveform</div>
        <div class="muted" style="font-size:12px">
          Garis hitam = EAR historis (60 detik terakhir);
          garis ungu putus-putus = prediksi EAR (60 detik ke depan);
          garis kuning putus-putus = T_close;
          band hijau = rentang EAR personal;
          garis biru vertikal = blink;
          blok merah = microsleep.
        </div>
      </div>
      <div class="muted" id="timeNow" style="font-size:12px"></div>
    </div>
    <canvas id="bigCanvas" width="1200" height="260" style="width:100%;height:260px;border:1px solid #ddd;border-radius:12px"></canvas>
    <div id="detailKPIs" class="kpi" style="grid-template-columns:repeat(8,minmax(0,1fr));margin-top:10px"></div>

    <div class="row" style="grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-top:12px">
      <div class="card">
        <div style="font-weight:600;margin-bottom:4px">Indikator Fatigue</div>
        <ul id="fatigueList" style="margin:0;padding-left:18px;line-height:1.4"></ul>
      </div>
      <div class="card">
        <div style="font-weight:600;margin-bottom:4px">Indikator Drift Pattern</div>
        <ul id="driftList" style="margin:0;padding-left:18px;line-height:1.4"></ul>
      </div>
    </div>
  </section>

  <section class="card">
    <div class="between" style="margin-bottom:6px">
      <div style="font-weight:600">Summary Insight</div>
      <div id="testStatus" class="muted"></div>
    </div>
    <div id="summary" class="summary"></div>
  </section>

  <footer class="muted" style="font-size:12px">Data riil dari API DMS. Diperbarui setiap 2 detik. Sumber: /api/dms/dashboard/realtime</footer>
</div>

<!-- Modal Detail Log Driver (optional, for "Lihat detail" logs) -->
<div id="driverDetailModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;padding:20px">
  <div style="background:var(--bg);border-radius:14px;max-width:900px;width:100%;max-height:90vh;overflow:auto;box-shadow:0 4px 24px rgba(0,0,0,.15)">
    <div style="padding:16px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center">
      <strong id="driverDetailModalTitle">Detail Safety Score Logs</strong>
      <button type="button" id="driverDetailModalClose" class="btn">Tutup</button>
    </div>
    <div style="padding:16px">
      <div id="driverDetailLoading" style="text-align:center;padding:24px;color:var(--muted)">Memuat data...</div>
      <div id="driverDetailContent" style="display:none">
        <div style="overflow-x:auto">
          <table style="width:100%;border-collapse:collapse;font-size:12px">
            <thead><tr style="background:#f3f4f6;text-align:left">
              <th style="padding:8px;border:1px solid #e5e7eb">ID</th>
              <th style="padding:8px;border:1px solid #e5e7eb">Driver ID</th>
              <th style="padding:8px;border:1px solid #e5e7eb">Timestamp</th>
              <th style="padding:8px;border:1px solid #e5e7eb">EAR</th>
              <th style="padding:8px;border:1px solid #e5e7eb">PERCLOS (60s)</th>
              <th style="padding:8px;border:1px solid #e5e7eb">Blink (60s)</th>
              <th style="padding:8px;border:1px solid #e5e7eb">Microsleep (60s)</th>
              <th style="padding:8px;border:1px solid #e5e7eb">Fatigue</th>
              <th style="padding:8px;border:1px solid #e5e7eb">Drift</th>
              <th style="padding:8px;border:1px solid #e5e7eb">Safety Score</th>
              <th style="padding:8px;border:1px solid #e5e7eb">Status</th>
            </tr></thead>
            <tbody id="driverLogsTableBody"></tbody>
          </table>
        </div>
      </div>
      <div id="driverDetailError" style="display:none;padding:12px;background:#fee2e2;color:#991b1b;border-radius:8px">Gagal memuat data.</div>
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
      b.className = 'btn';
      b.textContent = op.driver_id;
      b.style.color = '#fff';
      b.style.background = p === 'High' ? '#dc2626' : p === 'Medium' ? '#d97706' : '#059669';
      b.onclick = () => { selectedIndex = i; updateDetail(); };
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
      card.className = 'card';
      if(i === selectedIndex) card.style.boxShadow = '0 0 0 2px rgba(0,0,0,.05)';
      card.innerHTML = `
        <div class="between" style="margin-bottom:6px">
          <div style="font-weight:600">${op.driver_id}</div>
          <span class="tag ${badge(p)}">${p}</span>
        </div>
        <canvas id="${canvasId}" width="320" height="90" style="width:100%;height:90px;border:1px solid #ddd;border-radius:8px"></canvas>
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
        <div class="row" style="grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:6px;font-size:12px">
          <div><div style="font-weight:600">Indikator Fatigue</div><div class="muted">Data dari API (PERCLOS, blink, microsleep)</div></div>
          <div><div style="font-weight:600">Drift Pattern</div><div class="muted">Slope EAR: ${safeToFixed(slope, 4)}</div></div>
        </div>
        <div class="flex" style="margin-top:8px">
          <button class="btn" data-driver="${op.driver_id.replace(/"/g, '&quot;')}">Lihat detail log</button>
        </div>
      `;
      container.appendChild(card);
      drawSmall(ear, thr, bandLow, bandHigh, canvasId);
      card.querySelector('button[data-driver]').onclick = () => { viewDetails(op.driver_id); };
    });
  }

  function updateDetail(){
    if(!operatorsData.length){ $('detailTitle').textContent = 'Detail Waveform'; $('timeNow').textContent = ''; return; }
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
      const items = [];
      if(safeNum(ll.microsleep_60s, 0) > 0) items.push('Microsleep terdeteksi');
      if(safeNum(ll.perclos_60s, 0) >= 0.2) items.push('PERCLOS tinggi');
      if(safeNum(ll.slope_ear_per_min, 0) < -0.02) items.push('EAR menurun (slope negatif)');
      if(items.length === 0) items.push('Stabil');
      const card = document.createElement('div');
      card.className = 'card';
      card.innerHTML =
        '<div class="between" style="margin-bottom:6px"><div style="font-weight:600">' + o.driver_id + '</div><div style="' + safetyColor(safetyVal) + '">' + safeToFixed(safetyVal, 0) + ' · ' + safetyBand(safetyVal) + '</div></div>' +
        '<ul style="margin:0;padding-left:18px;line-height:1.4">' + items.map(t => '<li>' + t + '</li>').join('') + '</ul>';
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
          $('opCards').innerHTML = '<div class="card muted" style="text-align:center;padding:24px">Tidak ada data operator saat ini.</div>';
          $('detailTitle').textContent = 'Detail Waveform';
          $('timeNow').textContent = '';
          $('summary').innerHTML = '';
          $('testStatus').textContent = 'Terakhir diperbarui: ' + new Date().toLocaleTimeString('id-ID');
          return;
        }
        renderOperatorButtons();
        renderCards();
        updateDetail();
        $('testStatus').textContent = 'Terakhir diperbarui: ' + new Date().toLocaleTimeString('id-ID');
        $('testStatus').style.color = 'var(--green)';
      } else {
        $('testStatus').textContent = 'Gagal memuat data';
        $('testStatus').style.color = '#dc2626';
      }
    } catch(err){
      console.error('fetchRealtimeData', err);
      $('testStatus').textContent = 'Error: ' + (err.message || 'Gagal memuat');
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
