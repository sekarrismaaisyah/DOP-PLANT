@extends('layouts.master')

@section('title', 'Incident Back Analysis')

@section('content')
<x-page-title title="Peer Pressure Edukasi" pagetitle="Incident Back Analysis Tool" />

<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Incident Back Analysis (Non-React)</h5>
    <p class="text-muted mb-3">Halaman ini sudah dibersihkan dari React JSX.</p>
    <div class="table-responsive">
      <table class="table table-sm">
        <thead>
          <tr>
            <th>Week</th>
            <th class="text-end">Hazard</th>
            <th class="text-end">TBC</th>
            <th class="text-end">Blindspot</th>
            <th class="text-end">Coverage</th>
          </tr>
        </thead>
        <tbody id="iba-rows"></tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
(() => {
  const rows = [
    ['W49', 23280, 8480, 32, '70.5%'],
    ['W50', 22600, 8190, 42, '67.8%'],
    ['W51', 23540, 8595, 29, '71.3%']
  ];
  const tbody = document.getElementById('iba-rows');
  if (!tbody) return;
  tbody.innerHTML = rows.map(r => `
    <tr>
      <td>${r[0]}</td>
      <td class="text-end">${Number(r[1]).toLocaleString('en-US')}</td>
      <td class="text-end">${Number(r[2]).toLocaleString('en-US')}</td>
      <td class="text-end">${Number(r[3]).toLocaleString('en-US')}</td>
      <td class="text-end">${r[4]}</td>
    </tr>
  `).join('');
})();
</script>
@endsection

@extends('layouts.master')

@section('title', 'Incident Back Analysis')

@section('css')
<style>
  .iba-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px}
  .iba-title{font-size:11px;text-transform:uppercase;letter-spacing:.08em;font-weight:700;color:#475569}
  .iba-value{font-weight:800;color:#0f172a}
</style>
@endsection

@section('content')
<x-page-title title="Peer Pressure Edukasi" pagetitle="Incident Back Analysis Tool" />
<div class="row g-3">
  <div class="col-12 col-xl-8">
    <div class="iba-card p-3">
      <div class="iba-title mb-2">Statistical Score Trend</div>
      <div style="height:320px"><canvas id="iba-trend-chart"></canvas></div>
    </div>
  </div>
  <div class="col-12 col-xl-4">
    <div class="iba-card p-3 mb-3"><div class="iba-title">Average Score</div><div id="iba-avg" class="iba-value fs-2">0</div></div>
    <div class="iba-card p-3 mb-3"><div class="iba-title">Peak Week</div><div id="iba-peak" class="iba-value fs-5">-</div></div>
    <div class="iba-card p-3"><div class="iba-title">Latest Week</div><div id="iba-latest" class="iba-value fs-5">-</div></div>
  </div>
  <div class="col-12">
    <div class="iba-card p-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="iba-title">Weekly Detail</div>
        <select id="iba-site" class="form-select form-select-sm" style="max-width:220px"></select>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light"><tr><th>Week</th><th class="text-end">Hazard</th><th class="text-end">TBC</th><th class="text-end">Blindspot</th><th class="text-end">Coverage %</th><th class="text-end">Incident</th><th class="text-end">Score</th></tr></thead>
          <tbody id="iba-tbody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
  const DATA = {
    "All Site":[["W40",23980,8830,24,73.2,0],["W41",23640,8650,28,72.1,1],["W42",23410,8510,31,71.4,0],["W43",23120,8440,34,70.9,1],["W44",22840,8335,39,69.7,2],["W45",22680,8280,43,68.4,2],["W46",22490,8210,46,67.6,3],["W47",22720,8290,37,69.1,1],["W48",22950,8355,35,69.8,1],["W49",23280,8480,32,70.5,0],["W50",22600,8190,42,67.8,1],["W51",23540,8595,29,71.3,0]],
    "LMO":[["W40",7210,2520,9,75.0,0],["W41",7050,2440,11,73.9,1],["W42",6970,2400,12,73.0,0],["W43",6900,2370,14,72.2,1],["W44",6840,2345,16,71.6,1],["W45",6760,2310,17,70.7,2]]
  };
  const siteEl = document.getElementById('iba-site');
  const tbody = document.getElementById('iba-tbody');
  const avg = document.getElementById('iba-avg');
  const peak = document.getElementById('iba-peak');
  const latest = document.getElementById('iba-latest');
  let chart = null;

  function score(r){ return Math.min(100, Math.max(0, r[1]/300 + r[2]/120 + r[3]*1.5 + r[5]*8 + Math.max(0,80-r[4]))); }
  function render(site){
    const rows = (DATA[site] || []).map(r => ({ week:r[0], hazard:r[1], tbc:r[2], blind:r[3], cov:r[4], inc:r[5], score:score(r) }));
    tbody.innerHTML = rows.map(r=>`<tr><td>${r.week}</td><td class="text-end">${r.hazard.toLocaleString('en-US')}</td><td class="text-end">${r.tbc.toLocaleString('en-US')}</td><td class="text-end">${r.blind}</td><td class="text-end">${r.cov.toFixed(1)}</td><td class="text-end">${r.inc}</td><td class="text-end fw-bold">${r.score.toFixed(1)}</td></tr>`).join('');
    const avgVal = rows.reduce((s,r)=>s+r.score,0)/(rows.length||1); avg.textContent = avgVal.toFixed(1);
    const peakRow = rows.reduce((m,r)=>r.score>m.score?r:m, rows[0]||{week:'-',score:0}); peak.textContent = `${peakRow.week} (${peakRow.score.toFixed(1)})`;
    const last = rows[rows.length-1]||{week:'-',score:0}; latest.textContent = `${last.week} (${last.score.toFixed(1)})`;
    if (chart) chart.destroy();
    chart = new Chart(document.getElementById('iba-trend-chart'),{
      type:'line',
      data:{ labels:rows.map(r=>r.week), datasets:[{label:'Score',data:rows.map(r=>r.score),borderColor:'#0f172a',tension:.3,yAxisID:'y'},{label:'Incident',data:rows.map(r=>r.inc),borderColor:'#2563eb',tension:.25,yAxisID:'y1'}]},
      options:{responsive:true,maintainAspectRatio:false,scales:{y:{min:0,max:100},y1:{position:'right',min:0,suggestedMax:4,grid:{display:false}}}}
    });
  }
  Object.keys(DATA).forEach(k=>{ const o=document.createElement('option'); o.value=k; o.textContent=k; siteEl.appendChild(o); });
  siteEl.value='All Site'; siteEl.addEventListener('change',()=>render(siteEl.value)); render('All Site');
})();
</script>
@endsection

@extends('layouts.master')

@section('title', 'Incident Back Analysis')

@section('css')
<style>
  .iba-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px}
  .iba-title{font-size:11px;text-transform:uppercase;letter-spacing:.08em;font-weight:700;color:#475569}
  .iba-value{font-weight:800;color:#0f172a}
</style>
@endsection

@section('content')
<x-page-title title="Peer Pressure Edukasi" pagetitle="Incident Back Analysis Tool" />
<div class="row g-3">
  <div class="col-12 col-xl-8">
    <div class="iba-card p-3">
      <div class="iba-title mb-2">Statistical Score Trend</div>
      <div style="height:320px"><canvas id="iba-trend-chart"></canvas></div>
    </div>
  </div>
  <div class="col-12 col-xl-4">
    <div class="iba-card p-3 mb-3"><div class="iba-title">Average Score</div><div id="iba-avg" class="iba-value fs-2">0</div></div>
    <div class="iba-card p-3 mb-3"><div class="iba-title">Peak Week</div><div id="iba-peak" class="iba-value fs-5">-</div></div>
    <div class="iba-card p-3"><div class="iba-title">Latest Week</div><div id="iba-latest" class="iba-value fs-5">-</div></div>
  </div>
  <div class="col-12">
    <div class="iba-card p-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="iba-title">Weekly Detail</div>
        <select id="iba-site" class="form-select form-select-sm" style="max-width:220px"></select>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light"><tr><th>Week</th><th class="text-end">Hazard</th><th class="text-end">TBC</th><th class="text-end">Blindspot</th><th class="text-end">Coverage %</th><th class="text-end">Incident</th><th class="text-end">Score</th></tr></thead>
          <tbody id="iba-tbody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
  const DATA = {
    "All Site":[["W40",23980,8830,24,73.2,0],["W41",23640,8650,28,72.1,1],["W42",23410,8510,31,71.4,0],["W43",23120,8440,34,70.9,1],["W44",22840,8335,39,69.7,2],["W45",22680,8280,43,68.4,2],["W46",22490,8210,46,67.6,3],["W47",22720,8290,37,69.1,1],["W48",22950,8355,35,69.8,1],["W49",23280,8480,32,70.5,0],["W50",22600,8190,42,67.8,1],["W51",23540,8595,29,71.3,0]],
    "LMO":[["W40",7210,2520,9,75.0,0],["W41",7050,2440,11,73.9,1],["W42",6970,2400,12,73.0,0],["W43",6900,2370,14,72.2,1],["W44",6840,2345,16,71.6,1],["W45",6760,2310,17,70.7,2]]
  };
  const siteEl = document.getElementById('iba-site');
  const tbody = document.getElementById('iba-tbody');
  const avg = document.getElementById('iba-avg');
  const peak = document.getElementById('iba-peak');
  const latest = document.getElementById('iba-latest');
  let chart = null;

  function score(r){ return Math.min(100, Math.max(0, r[1]/300 + r[2]/120 + r[3]*1.5 + r[5]*8 + Math.max(0,80-r[4]))); }
  function render(site){
    const rows = (DATA[site] || []).map(r => ({ week:r[0], hazard:r[1], tbc:r[2], blind:r[3], cov:r[4], inc:r[5], score:score(r) }));
    tbody.innerHTML = rows.map(r=>`<tr><td>${r.week}</td><td class="text-end">${r.hazard.toLocaleString('en-US')}</td><td class="text-end">${r.tbc.toLocaleString('en-US')}</td><td class="text-end">${r.blind}</td><td class="text-end">${r.cov.toFixed(1)}</td><td class="text-end">${r.inc}</td><td class="text-end fw-bold">${r.score.toFixed(1)}</td></tr>`).join('');
    const avgVal = rows.reduce((s,r)=>s+r.score,0)/(rows.length||1); avg.textContent = avgVal.toFixed(1);
    const peakRow = rows.reduce((m,r)=>r.score>m.score?r:m, rows[0]||{week:'-',score:0}); peak.textContent = `${peakRow.week} (${peakRow.score.toFixed(1)})`;
    const last = rows[rows.length-1]||{week:'-',score:0}; latest.textContent = `${last.week} (${last.score.toFixed(1)})`;
    if (chart) chart.destroy();
    chart = new Chart(document.getElementById('iba-trend-chart'),{
      type:'line',
      data:{ labels:rows.map(r=>r.week), datasets:[{label:'Score',data:rows.map(r=>r.score),borderColor:'#0f172a',tension:.3,yAxisID:'y'},{label:'Incident',data:rows.map(r=>r.inc),borderColor:'#2563eb',tension:.25,yAxisID:'y1'}]},
      options:{responsive:true,maintainAspectRatio:false,scales:{y:{min:0,max:100},y1:{position:'right',min:0,suggestedMax:4,grid:{display:false}}}}
    });
  }
  Object.keys(DATA).forEach(k=>{ const o=document.createElement('option'); o.value=k; o.textContent=k; siteEl.appendChild(o); });
  siteEl.value='All Site'; siteEl.addEventListener('change',()=>render(siteEl.value)); render('All Site');
})();
</script>
@endsection


@section('title', 'Incident Back Analysis')

@section('css')
<style>
  .iba-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; }
  .iba-title { font-size:11px; text-transform:uppercase; letter-spacing:.08em; font-weight:700; color:#475569; }
  .iba-value { font-weight:800; color:#0f172a; }
  .iba-grid { display:grid; gap:12px; }
  .iba-scrollbar-hide::-webkit-scrollbar { display:none; }
  .iba-scrollbar-hide { -ms-overflow-style:none; scrollbar-width:none; }
</style>
@endsection

@section('content')
<x-page-title title="Peer Pressure Edukasi" pagetitle="Incident Back Analysis Tool" />

<div class="row g-3">
  <div class="col-12">
    <div class="iba-card p-3 p-md-4">
      <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
          <div class="iba-title">Incident Back Analysis</div>
          <h4 class="mb-1">Statistical Monitoring Dashboard</h4>
          <p class="mb-0 text-muted small">Versi Blade HTML/CSS/JS tanpa React.</p>
        </div>
        <div class="d-flex gap-2 align-items-end">
          <div>
            <label class="form-label mb-1 small fw-semibold">Site</label>
            <select id="iba-site" class="form-select form-select-sm"></select>
          </div>
          <button id="iba-reset" class="btn btn-sm btn-outline-secondary">Reset</button>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-8">
    <div class="iba-card p-3">
      <div class="iba-title mb-2">Statistical Score Trend</div>
      <div style="height:320px"><canvas id="iba-trend-chart"></canvas></div>
    </div>
  </div>

  <div class="col-12 col-xl-4 iba-grid">
    <div class="iba-card p-3">
      <div class="iba-title">Average Score</div>
      <div id="iba-avg-score" class="iba-value fs-3">0</div>
    </div>
    <div class="iba-card p-3">
      <div class="iba-title">Peak Week</div>
      <div id="iba-peak-week" class="iba-value fs-5">-</div>
    </div>
    <div class="iba-card p-3">
      <div class="iba-title">Latest Week</div>
      <div id="iba-latest-week" class="iba-value fs-5">-</div>
    </div>
  </div>

  <div class="col-12">
    <div class="iba-card p-3">
      <div class="iba-title mb-2">Weekly Detail</div>
      <div class="table-responsive iba-scrollbar-hide">
        <table class="table table-sm align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Week</th>
              <th class="text-end">Hazard</th>
              <th class="text-end">TBC</th>
              <th class="text-end">Blindspot</th>
              <th class="text-end">Coverage %</th>
              <th class="text-end">Actual Incident</th>
              <th class="text-end">Score</th>
            </tr>
          </thead>
          <tbody id="iba-tbody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
  const SITE_ROWS = {
    "All Site": [
      { week:"W40", hazard:23980, tbc:8830, blindspotTbc:24, coverageArea:73.2, actualIncidents:0 },
      { week:"W41", hazard:23640, tbc:8650, blindspotTbc:28, coverageArea:72.1, actualIncidents:1 },
      { week:"W42", hazard:23410, tbc:8510, blindspotTbc:31, coverageArea:71.4, actualIncidents:0 },
      { week:"W43", hazard:23120, tbc:8440, blindspotTbc:34, coverageArea:70.9, actualIncidents:1 },
      { week:"W44", hazard:22840, tbc:8335, blindspotTbc:39, coverageArea:69.7, actualIncidents:2 },
      { week:"W45", hazard:22680, tbc:8280, blindspotTbc:43, coverageArea:68.4, actualIncidents:2 },
      { week:"W46", hazard:22490, tbc:8210, blindspotTbc:46, coverageArea:67.6, actualIncidents:3 },
      { week:"W47", hazard:22720, tbc:8290, blindspotTbc:37, coverageArea:69.1, actualIncidents:1 },
      { week:"W48", hazard:22950, tbc:8355, blindspotTbc:35, coverageArea:69.8, actualIncidents:1 },
      { week:"W49", hazard:23280, tbc:8480, blindspotTbc:32, coverageArea:70.5, actualIncidents:0 },
      { week:"W50", hazard:22600, tbc:8190, blindspotTbc:42, coverageArea:67.8, actualIncidents:1 },
      { week:"W51", hazard:23540, tbc:8595, blindspotTbc:29, coverageArea:71.3, actualIncidents:0 }
    ],
    "LMO": [
      { week:"W40", hazard:7210, tbc:2520, blindspotTbc:9, coverageArea:75.0, actualIncidents:0 },
      { week:"W41", hazard:7050, tbc:2440, blindspotTbc:11, coverageArea:73.9, actualIncidents:1 },
      { week:"W42", hazard:6970, tbc:2400, blindspotTbc:12, coverageArea:73.0, actualIncidents:0 },
      { week:"W43", hazard:6900, tbc:2370, blindspotTbc:14, coverageArea:72.2, actualIncidents:1 },
      { week:"W44", hazard:6840, tbc:2345, blindspotTbc:16, coverageArea:71.6, actualIncidents:1 },
      { week:"W45", hazard:6760, tbc:2310, blindspotTbc:17, coverageArea:70.7, actualIncidents:2 }
    ],
    "SMO": [
      { week:"W40", hazard:7945, tbc:2790, blindspotTbc:8, coverageArea:73.6, actualIncidents:0 },
      { week:"W41", hazard:7890, tbc:2765, blindspotTbc:9, coverageArea:73.0, actualIncidents:0 },
      { week:"W42", hazard:7750, tbc:2705, blindspotTbc:11, coverageArea:71.8, actualIncidents:1 },
      { week:"W43", hazard:7640, tbc:2670, blindspotTbc:13, coverageArea:70.9, actualIncidents:1 },
      { week:"W44", hazard:7520, tbc:2620, blindspotTbc:14, coverageArea:69.9, actualIncidents:2 },
      { week:"W45", hazard:7445, tbc:2590, blindspotTbc:16, coverageArea:69.0, actualIncidents:2 }
    ],
    "GMO": [
      { week:"W40", hazard:7130, tbc:2520, blindspotTbc:7, coverageArea:71.5, actualIncidents:0 },
      { week:"W41", hazard:7010, tbc:2485, blindspotTbc:8, coverageArea:70.8, actualIncidents:1 },
      { week:"W42", hazard:6950, tbc:2460, blindspotTbc:9, coverageArea:70.0, actualIncidents:0 },
      { week:"W43", hazard:6880, tbc:2435, blindspotTbc:10, coverageArea:69.6, actualIncidents:0 },
      { week:"W44", hazard:6795, tbc:2400, blindspotTbc:11, coverageArea:68.8, actualIncidents:1 },
      { week:"W45", hazard:6735, tbc:2375, blindspotTbc:12, coverageArea:68.2, actualIncidents:1 }
    ]
  };

  const siteSelect = document.getElementById('iba-site');
  const tbody = document.getElementById('iba-tbody');
  const avgEl = document.getElementById('iba-avg-score');
  const peakEl = document.getElementById('iba-peak-week');
  const latestEl = document.getElementById('iba-latest-week');
  const resetBtn = document.getElementById('iba-reset');
  let trendChart = null;

  function scoreOf(row) {
    const hazardNorm = row.hazard / 300;
    const tbcNorm = row.tbc / 120;
    const blindspotNorm = row.blindspotTbc * 1.5;
    const incidentNorm = row.actualIncidents * 8;
    const coveragePenalty = Math.max(0, 80 - row.coverageArea);
    return Math.min(100, Math.max(0, hazardNorm + tbcNorm + blindspotNorm + incidentNorm + coveragePenalty));
  }

  function render(site) {
    const rows = (SITE_ROWS[site] || []).map(r => ({ ...r, score: scoreOf(r) }));
    tbody.innerHTML = rows.map(r => `
      <tr>
        <td>${r.week}</td>
        <td class="text-end">${r.hazard.toLocaleString('en-US')}</td>
        <td class="text-end">${r.tbc.toLocaleString('en-US')}</td>
        <td class="text-end">${r.blindspotTbc.toLocaleString('en-US')}</td>
        <td class="text-end">${r.coverageArea.toFixed(1)}</td>
        <td class="text-end">${r.actualIncidents}</td>
        <td class="text-end fw-bold">${r.score.toFixed(1)}</td>
      </tr>
    `).join('');

    const avg = rows.length ? rows.reduce((s, r) => s + r.score, 0) / rows.length : 0;
    const peak = rows.reduce((m, r) => r.score > m.score ? r : m, rows[0] || { week: '-', score: 0 });
    const latest = rows[rows.length - 1] || { week: '-', score: 0 };

    avgEl.textContent = avg.toFixed(1);
    peakEl.textContent = `${peak.week} (${peak.score.toFixed(1)})`;
    latestEl.textContent = `${latest.week} (${latest.score.toFixed(1)})`;

    const labels = rows.map(r => r.week);
    const scores = rows.map(r => r.score);
    const actuals = rows.map(r => r.actualIncidents);
    if (trendChart) trendChart.destroy();
    trendChart = new Chart(document.getElementById('iba-trend-chart').getContext('2d'), {
      type: 'line',
      data: {
        labels,
        datasets: [
          { label: 'Statistical Score', data: scores, borderColor: '#0f172a', backgroundColor: 'rgba(15,23,42,.06)', tension: .3, yAxisID: 'y' },
          { label: 'Actual Incident', data: actuals, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.06)', tension: .25, yAxisID: 'y1' }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: {
          y: { min: 0, max: 100, grid: { color: 'rgba(148,163,184,.2)' } },
          y1: { position: 'right', min: 0, suggestedMax: 4, grid: { display: false } }
        }
      }
    });
  }

  Object.keys(SITE_ROWS).forEach(site => {
    const opt = document.createElement('option');
    opt.value = site;
    opt.textContent = site;
    siteSelect.appendChild(opt);
  });

  siteSelect.value = 'All Site';
  siteSelect.addEventListener('change', () => render(siteSelect.value));
  resetBtn.addEventListener('click', () => {
    siteSelect.value = 'All Site';
    render('All Site');
  });

  render('All Site');
})();
</script>
@endsection
