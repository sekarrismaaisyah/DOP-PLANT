<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>EAR Dummy – Standalone</title>
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
</style>
</head>
<body>
<div class="wrap">
  <header class="between">
    <h1 style="margin:0;font-size:20px;font-weight:700">EAR Dummy – Personalized Multi Operator (Standalone)</h1>
    <div id="opButtons" class="flex"></div>
  </header>

  <section class="row ops" id="opCards"></section>

  <section class="card">
    <div class="between" style="margin-bottom:6px">
      <div>
        <div id="detailTitle" style="font-weight:600">Detail Waveform</div>
        <div class="muted" style="font-size:12px">Band hijau = rentang EAR personal; putus-putus = T_close; biru = blink; blok merah = microsleep</div>
      </div>
      <div class="muted" id="timeNow" style="font-size:12px"></div>
    </div>
    <canvas id="bigCanvas" width="1200" height="260" style="width:100%;height:260px;border:1px solid #ddd;border-radius:12px"></canvas>
    <div id="detailKPIs" class="kpi" style="grid-template-columns:repeat(6,minmax(0,1fr));margin-top:10px"></div>

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
    <div style="font-weight:600;margin-bottom:6px">Summary Insight</div>
    <div id="summary" class="summary"></div>
  </section>

  <footer class="muted" style="font-size:12px">Demo menggunakan data simulasi 25 Hz, jendela 60 detik. Fitur: multi-operator, baseline personal (auto kalibrasi 15 menit), skor fatigue personal, kerapatan blink, dan deteksi perubahan perilaku.</footer>
</div>

<script>
(function(){
  const FPS = 25;
  const WINDOW_SEC = 60;
  const BUFFER_LEN = WINDOW_SEC * FPS;
  const MAX_CAL_SEC = 900;
  const OP_COUNT = 4;
  const EAR_OPEN = 0.28;
  const EAR_CLOSED = 0.1;
  const PRED_SEC = 5;
  const PRED_LEN = PRED_SEC * FPS;
  const MICRO_MIN_SEC = 1.4;
  const $ = (id) => document.getElementById(id);
  const clamp = (n, lo, hi) => Math.max(lo, Math.min(hi, n));
  const lerp = (a,b,t)=>a+(b-a)*t;
  const sigmoid = (x)=>1/(1+Math.exp(-x));

  function makeProfile(i){
    const seed=(i+1)*13;
    const tclose = EAR_OPEN - 0.35*(EAR_OPEN - EAR_CLOSED) + (seed%5-2)*0.003;
    const perclos_base = clamp(0.08 + (seed%7)*0.01, 0.05, 0.18);
    const blink_base = 14 + (seed%6);
    const ibi_sd_base = 0.12 + (seed%5)*0.02;
    const yawn_base = 1 + (seed%3)*0.3;
    const head_base = 0.8 + (seed%4)*0.1;
    const ear_band_low = 0.22 + (seed%4)*0.004;
    const ear_band_high = 0.30 + (seed%4)*0.004;
    return {T_close:tclose, perclos_base, blink_base, ibi_sd_base, yawn_base, head_base, ear_band_low, ear_band_high};
  }

  function makeOp(i){
    return {
      id: `OP${i+1}`,
      label: `Operator ${i+1}`,
      now: 0,
      earBuf: Array(BUFFER_LEN).fill(EAR_OPEN + (Math.random()-0.5)*0.01),
      timeBuf: Array(BUFFER_LEN).fill(0),
      events: [],
      sim: {phase:"idle", left:0},
      profile: makeProfile(i),
      histEar: [], histTime: [], histEvents: [],
      calib: {done:false}
    };
  }

  let ops = Array.from({length:OP_COUNT}, (_,i)=>makeOp(i));
  let selected = 0;

  function priority(score){return score>=70?"High":score>=40?"Medium":"Low"}
  function badge(p){return p==="High"?"high":p==="Medium"?"med":"low"}
  function safetyBand(s){return s>=80?"Safe":s>=60?"Caution":"Attention"}
  function safetyColor(s){return s>=80?"color:var(--green)":s>=60?"color:#d97706":"color:#dc2626"}

  function computeCalib(op){
    const samples = op.histEar.length;
    if(samples < FPS*30) return op.profile;
    const T = op.profile.T_close;
    const perclos_base = op.histEar.filter(v=>v<T).length / Math.max(1,samples);
    const mean = op.histEar.reduce((s,v)=>s+v,0)/samples;
    const sd = Math.sqrt(op.histEar.reduce((s,v)=>s+(v-mean)*(v-mean),0)/Math.max(1,samples-1));
    const ear_band_low = Math.max(0.1, mean - 1*sd);
    const ear_band_high= Math.min(0.36, mean + 1*sd);
    const tMin = op.histTime[0]||0;
    const tMax = op.histTime[op.histTime.length-1]||0;
    const minutes = Math.max(1/60,(tMax-tMin)/60);
    const blink_base = Math.round(op.histEvents.filter(e=>e.kind==="blink" && e.t>=tMin).length/minutes);
    const ibi_sd_base=0.12,yawn_base=1,head_base=1;
    return {...op.profile, perclos_base:clamp(perclos_base,0.03,0.6), blink_base:clamp(blink_base,4,45), ear_band_low, ear_band_high, ibi_sd_base, yawn_base, head_base};
  }

  function stepOp(op){
    const s = {...op.sim};
    if(s.phase==="idle"){
      const blinkPerMin = op.profile.blink_base;
      const pBlink = (blinkPerMin/60/FPS) * lerp(0.7,1.3,Math.random());
      const pMicro = (1/(110 + (op.id.charCodeAt(2)%70)))/FPS;
      if(Math.random()<pMicro){ s.phase="microsleep"; s.left=(1.4+Math.random()*1.6)*FPS; }
      else if(Math.random()<pBlink){ s.phase="blink"; s.left=(0.07+Math.random()*0.22)*FPS; }
    }
    const tNow = op.now + 1/FPS;
    const drift = 0.004*Math.sin(tNow*2*Math.PI*(0.015+(op.id.charCodeAt(2)%5)/200));
    const baseline = EAR_OPEN + 0.01*Math.sin(tNow*2*Math.PI*0.22) + 0.005*Math.sin(tNow*2*Math.PI*0.07) + drift;
    let ear = baseline + (Math.random()-0.5)*0.009;
    let newEvents = op.events;
    let newHistEvents = op.histEvents;
    if(s.phase==="blink"){
      const progress = 1 - s.left/Math.max(1,s.left+1);
      const depth = 0.1 + Math.random()*0.05;
      const dip = depth*Math.sin(Math.min(Math.PI, progress*Math.PI));
      ear = baseline - dip;
      s.left -= 1;
      if(s.left<=0){
        const dur = clamp(0.08+Math.random()*0.18,0.06,0.35);
        const ev = {t:tNow, kind:"blink", duration:dur};
        newEvents = [...op.events.slice(-199), ev];
        newHistEvents = [...op.histEvents, ev].slice(-MAX_CAL_SEC*FPS);
        s.phase = "idle";
      }
    } else if(s.phase==="microsleep"){
      ear = EAR_CLOSED + 0.008*Math.random();
      s.left -= 1;
      if(s.left<=0){
        const ev = {t:tNow, kind:"microsleep", duration:1.6+Math.random()*0.6};
        newEvents = [...op.events.slice(-199), ev];
        newHistEvents = [...op.histEvents, ev].slice(-MAX_CAL_SEC*FPS);
        s.phase = "idle";
      }
    }
    ear = clamp(ear,0.05,0.4);
    const nextEar = op.earBuf.slice(1); nextEar.push(ear);
    const nextTime = op.timeBuf.slice(1); nextTime.push(tNow);
    const histEar = [...op.histEar, ear].slice(-MAX_CAL_SEC*FPS);
    const histTime= [...op.histTime, tNow].slice(-MAX_CAL_SEC*FPS);
    let nextProfile = op.profile;
    let nextCalib = op.calib;
    const span = (histTime[histTime.length-1]||0) - (histTime[0]||0);
    if(!op.calib.done && span>=MAX_CAL_SEC){ nextProfile = computeCalib({...op, histEar, histTime, histEvents:newHistEvents, profile:op.profile}); nextCalib={done:true}; }
    return {...op, now:tNow, earBuf:nextEar, timeBuf:nextTime, sim:s, events:newEvents, histEar, histTime, histEvents:newHistEvents, profile:nextProfile, calib:nextCalib};
  }

  function metricsFor(op){$1return {fatigueMetrics:{fatigue, perclos, blinkPerMin, blinkPerMin15, micro60, denseBlink, notes:fatigueNotes}, driftMetrics:{drift, earSlopePerMin, slopePerSec, bandOutRatio, behaviorShift, earDrop, perclosJump, notes:driftNotes}, safety};
  }

  function forecastFor(op, m){
    const thr = op.profile.T_close;
    const last = op.earBuf[op.earBuf.length-1];
    const slope = m.driftMetrics.slopePerSec;
    const midBand = (op.profile.ear_band_low + op.profile.ear_band_high) / 2;

    const k = 0.55;
    const preds = [];
    let x = last;
    for(let i=1;i<=PRED_LEN;i++){
      const dt = 1/FPS;
      const proj = x + slope*dt;
      x = proj + (midBand - proj) * (1 - Math.exp(-k*dt));
      x = clamp(x, 0.05, 0.4);
      preds.push(x);
    }

    const perclosPred = preds.filter(v=>v<thr).length / Math.max(1,preds.length);

    const minSamples = Math.ceil(MICRO_MIN_SEC * FPS);
    let run = 0;
    let microPred = 0;
    for(let i=0;i<preds.length;i++){
      if(preds[i] < thr){ run++; if(run>=minSamples){ microPred = 1; break; } }
      else run = 0;
    }

    const fatigueDelta = (
      55*(perclosPred - op.profile.perclos_base)
      + 10*(m.fatigueMetrics.denseBlink?1:0)
      + 25*microPred
    );
    const fatiguePred = clamp(m.fatigueMetrics.fatigue + fatigueDelta, 0, 100);

    const driftDelta = (
      80*clamp((-m.driftMetrics.earSlopePerMin - (-0.01)), -0.1, 0.1)
      + 60*clamp((m.driftMetrics.bandOutRatio - 0.15), -0.3, 0.5)
      + 120*clamp((m.driftMetrics.perclosJump - 0.05), -0.2, 0.4)
    );
    const driftPred = clamp(m.driftMetrics.drift + driftDelta*0.08, 0, 100);

    const microProb = clamp(sigmoid((fatiguePred-55)/10 + (driftPred-55)/14 + (perclosPred*100-15)/18 + (m.fatigueMetrics.micro60>0?0.6:0)), 0, 1);

    const safetyPred = clamp(100 - (0.6*fatiguePred + 0.3*driftPred + 12*microProb + 8*Math.min(1, m.driftMetrics.bandOutRatio/0.3)), 0, 100);

    return {
      horizonSec: PRED_SEC,
      earPred: preds,
      earEnd: preds[preds.length-1] ?? last,
      perclosPred,
      microPred,
      microProb,
      fatiguePred,
      driftPred,
      safetyPred
    };
  }

  function drawBig(op, f){
    const cvs = $("bigCanvas"); const ctx = cvs.getContext("2d");
    const W=cvs.width, H=cvs.height;
    ctx.clearRect(0,0,W,H);
    ctx.lineWidth=1; ctx.strokeStyle=getComputedStyle(document.body).getPropertyValue("--grid")||"#e5e7eb";
    for(let x=0;x<W;x+=10){ctx.beginPath();ctx.moveTo(x,0);ctx.lineTo(x,H);ctx.stroke();}
    for(let y=0;y<H;y+=10){ctx.beginPath();ctx.moveTo(0,y);ctx.lineTo(W,y);ctx.stroke();}
    const minEAR=0.05,maxEAR=0.4; const yOf=(v)=> H-((v-minEAR)/(maxEAR-minEAR))*H;
    ctx.fillStyle="#a7f3d0"; ctx.globalAlpha=0.25; const yHigh=yOf(op.profile.ear_band_low), yLow=yOf(op.profile.ear_band_high); ctx.fillRect(0,Math.min(yHigh,yLow),W,Math.abs(yLow-yHigh)); ctx.globalAlpha=1;
    ctx.setLineDash([4,4]); ctx.strokeStyle="#f59e0b"; ctx.beginPath(); const yThr=yOf(op.profile.T_close); ctx.moveTo(0,yThr); ctx.lineTo(W,yThr); ctx.stroke(); ctx.setLineDash([]);
    ctx.strokeStyle="#111827"; ctx.lineWidth=2; ctx.beginPath();
    const totalN = op.earBuf.length + (f && f.earPred ? f.earPred.length : 0);
    const xOf = (i)=> (i/Math.max(1,totalN-1))*W;
    for(let i=0;i<op.earBuf.length;i++){
      const x=xOf(i);
      const y=yOf(op.earBuf[i]);
      if(i===0) ctx.moveTo(x,y); else ctx.lineTo(x,y);
    }
    ctx.stroke();

    if(f && f.earPred && f.earPred.length){
      ctx.save();
      ctx.strokeStyle="#8b5cf6";
      ctx.setLineDash([6,4]);
      ctx.lineWidth=2;
      ctx.beginPath();
      const startIdx = op.earBuf.length-1;
      ctx.moveTo(xOf(startIdx), yOf(op.earBuf[op.earBuf.length-1]));
      for(let j=0;j<f.earPred.length;j++){
        const i = op.earBuf.length + j;
        ctx.lineTo(xOf(i), yOf(f.earPred[j]));
      }
      ctx.stroke();
      ctx.setLineDash([]);
      ctx.restore();

      const xStart = xOf(op.earBuf.length);
      ctx.save();
      ctx.fillStyle="#8b5cf6";
      ctx.globalAlpha=0.08;
      ctx.fillRect(xStart,0,W-xStart,H);
      ctx.globalAlpha=1;
      ctx.restore();
    }
    const t0=op.timeBuf[0], t1=op.timeBuf[op.timeBuf.length-1];
    const baseW = (f && f.earPred && f.earPred.length) ? (W * (op.earBuf.length-1) / Math.max(1, (op.earBuf.length + f.earPred.length - 1))) : W;
    const toX=(t)=> ((t-t0)/Math.max(0.0001,t1-t0))*baseW;
    op.events.filter(e=>e.t>=t0).forEach(e=>{ const x=toX(e.t); ctx.save(); if(e.kind==="blink"){ ctx.strokeStyle="#3b82f6"; ctx.beginPath(); ctx.moveTo(x,0); ctx.lineTo(x,H); ctx.stroke(); } else { ctx.fillStyle="#ef4444"; ctx.globalAlpha=0.15; const w=Math.max(2,(e.duration/60)*W); ctx.fillRect(x,0,w,H); ctx.globalAlpha=1; } ctx.restore(); });
  }

  function drawSmall(op, idx){
    const cvs = document.querySelector(`#small-${idx}`); if(!cvs) return; const ctx=cvs.getContext("2d"); const W=cvs.width,H=cvs.height;
    ctx.clearRect(0,0,W,H);
    ctx.strokeStyle="#e5e7eb"; ctx.lineWidth=1; for(let x=0;x<W;x+=8){ctx.beginPath();ctx.moveTo(x,0);ctx.lineTo(x,H);ctx.stroke();}
    const minEAR=0.05,maxEAR=0.4; const yOf=(v)=> H-((v-minEAR)/(maxEAR-minEAR))*H;
    ctx.fillStyle="#a7f3d0"; ctx.globalAlpha=0.25; const yHigh=yOf(op.profile.ear_band_low), yLow=yOf(op.profile.ear_band_high); ctx.fillRect(0,Math.min(yHigh,yLow),W,Math.abs(yLow-yHigh)); ctx.globalAlpha=1;
    ctx.strokeStyle="#111827"; ctx.lineWidth=1.5; ctx.beginPath(); for(let i=0;i<op.earBuf.length;i++){ const x=(i/op.earBuf.length)*W; const y=yOf(op.earBuf[i]); if(i===0) ctx.moveTo(x,y); else ctx.lineTo(x,y);} ctx.stroke();
    ctx.setLineDash([3,3]); ctx.strokeStyle="#f59e0b"; ctx.beginPath(); const yThr=yOf(op.profile.T_close); ctx.moveTo(0,yThr); ctx.lineTo(W,yThr); ctx.stroke(); ctx.setLineDash([]);
  }

  function render(){
    const btnWrap = $("opButtons"); btnWrap.innerHTML="";
    ops.forEach((op,i)=>{
      const m = metricsFor(op); const f = forecastFor(op, m); const p = priority(m.fatigueMetrics.fatigue);
      const b = document.createElement("button"); b.className = "btn"; b.textContent = op.id; b.style.color="#fff"; b.style.background = p==="High"?"#dc2626":p==="Medium"?"#d97706":"#059669";
      b.onclick=()=>{selected=i; updateDetail();}; btnWrap.appendChild(b);
    });

    const cards = $("opCards"); cards.innerHTML="";
    ops.forEach((op,i)=>{
      const m = metricsFor(op); const p = priority(m.fatigueMetrics.fatigue);
      const card = document.createElement("div"); card.className="card"; if(i===selected) card.style.boxShadow="0 0 0 2px rgba(0,0,0,.05)";
      card.innerHTML = `
        <div class="between" style="margin-bottom:6px">
          <div style="font-weight:600">${op.label}</div>
          <span class="tag ${badge(p)}">${p}</span>
        </div>
        <canvas id="small-${i}" width="320" height="90" style="width:100%;height:90px;border:1px solid #ddd;border-radius:8px"></canvas>
        <div class="kpi">
          <div><div class="label">SafetyScore</div><div class="val" style="${safetyColor(m.safety)}">${m.safety.toFixed(0)}</div></div>
          <div><div class="label">Safety Band</div><div class="val">${safetyBand(m.safety)}</div></div>
          <div><div class="label">Fatigue</div><div class="val">${m.fatigueMetrics.fatigue.toFixed(0)}</div></div>
          <div><div class="label">Drift</div><div class="val">${m.driftMetrics.drift.toFixed(0)}</div></div>
          <div><div class="label">PRED ${PRED_SEC}s</div><div class="val" style="${safetyColor(f.safetyPred)}">${f.safetyPred.toFixed(0)} · ${safetyBand(f.safetyPred)}</div></div>
          <div><div class="label">P(micro)</div><div class="val">${(f.microProb*100).toFixed(0)}%</div></div>
        </div>
        <div class="row" style="grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:6px;font-size:12px">
          <div>
            <div style="font-weight:600">Indikator Fatigue</div>
            ${m.fatigueMetrics.notes.length?`<ul style="margin:0;padding-left:18px">${m.fatigueMetrics.notes.map(n=>`<li>${n}</li>`).join("")}</ul>`:`<div class="muted">—</div>`}
          </div>
          <div>
            <div style="font-weight:600">Drift Pattern</div>
            ${m.driftMetrics.notes.length?`<ul style="margin:0;padding-left:18px">${m.driftMetrics.notes.map(n=>`<li>${n}</li>`).join("")}</ul>`:`<div class="muted">—</div>`}
          </div>
        </div>
        <div class="flex" style="margin-top:8px"><button class="btn" data-idx="${i}">Lihat detail</button></div>
      `;
      cards.appendChild(card);
      drawSmall(op,i);
      card.querySelector("button.btn").onclick=()=>{selected=i; updateDetail();};
    });

    updateDetail();
  }

  function updateDetail(){
    const op = ops[selected]; const m = metricsFor(op);
    $("detailTitle").textContent = `Detail Waveform – ${op.label}`;
    $("timeNow").textContent = `t = ${op.now.toFixed(1)} s`;
    const f = forecastFor(op, m);
    drawBig(op, f);

    const dk = $("detailKPIs");
    dk.innerHTML = `
      <div><div class="label">Driver SafetyScore</div><div class="val" style="${safetyColor(m.safety)}">${m.safety.toFixed(0)}</div></div>
      <div><div class="label">Band</div><div class="val">${safetyBand(m.safety)}</div></div>
      <div><div class="label">Fatigue</div><div class="val">${m.fatigueMetrics.fatigue.toFixed(0)}</div></div>
      <div><div class="label">Drift</div><div class="val">${m.driftMetrics.drift.toFixed(0)}</div></div>
      <div><div class="label">Pred Safety (${PRED_SEC}s)</div><div class="val" style="${safetyColor(f.safetyPred)}">${f.safetyPred.toFixed(0)} · ${safetyBand(f.safetyPred)}</div></div>
      <div><div class="label">P(micro next ${PRED_SEC}s)</div><div class="val">${(f.microProb*100).toFixed(0)}%</div></div>
    `;

    const fl = $("fatigueList");
    const fatigueInfo = [
      `PERCLOS: ${(m.fatigueMetrics.perclos*100).toFixed(1)}% <span class=\"tooltip\" title=\"Persentase EAR di bawah T_close dalam 60s; <10% aman, 10–20% caution, >20% attention.\">i</span>`,
      `Blink/min: ${m.fatigueMetrics.blinkPerMin} (15s eqv: ${m.fatigueMetrics.blinkPerMin15}) <span class=\"tooltip\" title=\"Kedipan per menit; 15s eqv adalah estimasi cepat dari jendela 15 detik (sensitif perubahan).\">i</span>`,
      `Microsleep (60s): ${m.fatigueMetrics.micro60} <span class=\"tooltip\" title=\"Jumlah episode mata tertutup lama (≈≥1.4s) dalam 60s; indikasi bahaya akut.\">i</span>`,
      `Prediksi ${PRED_SEC}s → PERCLOS: ${(f.perclosPred*100).toFixed(1)}%, P(microsleep): ${(f.microProb*100).toFixed(0)}% <span class=\"tooltip\" title=\"Prediksi jangka sangat pendek (5s) berbasis slope EAR + mean reversion ke band personal. Akan terkoreksi otomatis tiap frame saat data baru masuk.\">i</span>`
    ];
    m.fatigueMetrics.notes.forEach(n=>{
      const tip = n==="Microsleep terdeteksi"?"Sedikitnya 1 episode microsleep teramati; memicu peringatan prioritas tinggi & tindakan segera.": n==="Kerapatan blink tinggi"?"Lonjakan kepadatan kedipan 15s; tanda perubahan fisiologis/kelelahan.":"";
      fatigueInfo.push(`${n}${tip?` <span class=\"tooltip\" title=\"${tip}\">i</span>`:""}`);
    });
    fl.innerHTML = fatigueInfo.map(s=>`<li>${s}</li>`).join("");

    const dl = $("driftList");
    const driftInfo = [
      `Slope EAR (/min): ${m.driftMetrics.earSlopePerMin.toFixed(3)}`,
      `Di luar band EAR: ${(m.driftMetrics.bandOutRatio*100).toFixed(0)}%`,
      `ΔEAR mean (awal→akhir 30s): ${m.driftMetrics.earDrop.toFixed(3)}`,
      `ΔPERCLOS (awal→akhir 30s): ${(m.driftMetrics.perclosJump*100).toFixed(1)}%`,
      `Pred EAR (akhir +${PRED_SEC}s): ${f.earEnd.toFixed(3)} <span class=\"tooltip\" title=\"Garis putus ungu pada waveform adalah proyeksi EAR beberapa detik ke depan.\">i</span>`
    ];
    m.driftMetrics.notes.forEach(n=>driftInfo.push(n));
    dl.innerHTML = driftInfo.map(s=>`<li>${s}</li>`).join("");

    const summary = $("summary"); summary.innerHTML = "";
    ops.forEach((o)=>{
      const mm = metricsFor(o);
      const ff = forecastFor(o, mm);
      const items = [];
      if(mm.fatigueMetrics.micro60>0) items.push("Microsleep terdeteksi");
      if(mm.fatigueMetrics.perclos>=0.2) items.push("PERCLOS tinggi");
      if(mm.fatigueMetrics.blinkPerMin<8 || mm.fatigueMetrics.blinkPerMin>30) items.push("Blink/min di luar normal");
      if(mm.driftMetrics.earSlopePerMin<-0.02) items.push("EAR menurun konsisten");
      if(mm.driftMetrics.bandOutRatio>0.3) items.push("EAR sering di luar band");
      if(ff.microProb>=0.5) items.push(`Prediksi: peluang microsleep ${Math.round(ff.microProb*100)}%`);
      if(items.length===0) items.push("Stabil");
      const card=document.createElement("div"); card.className="card"; card.innerHTML=
        `<div class=\"between\" style=\"margin-bottom:6px\"><div style=\"font-weight:600\">${o.label}</div><div style=\"${safetyColor(mm.safety)}\">${mm.safety.toFixed(0)} · ${safetyBand(mm.safety)}</div></div>`+
        `<div class=\"muted\" style=\"font-size:12px;margin-bottom:4px\">Pred ${PRED_SEC}s: <span style=\"${safetyColor(ff.safetyPred)}\">${ff.safetyPred.toFixed(0)} · ${safetyBand(ff.safetyPred)}</span> | P(micro): ${(ff.microProb*100).toFixed(0)}%</div>`+
        `<ul style=\"margin:0;padding-left:18px;line-height:1.4\">${items.map(t=>`<li>${t}</li>`).join("")}</ul>`;
      summary.appendChild(card);
    });
  }

  function tick(){ ops = ops.map(stepOp); render(); }

  // Initial DOM for cards and buttons
  render();
  setInterval(()=>{ tick(); }, 1000/FPS);
})();
</script>
</body>
</html>
