<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Incident Back Analysis Tool</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/recharts/2.8.0/Recharts.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #f1f5f9;
    --surface: #ffffff;
    --surface-2: #f8fafc;
    --border: #e2e8f0;
    --border-2: #cbd5e1;
    --text-primary: #0f172a;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
    --accent: #0f172a;
    --red: #ef4444;
    --red-bg: #fef2f2;
    --red-text: #b91c1c;
    --red-border: #fecaca;
    --yellow: #f59e0b;
    --yellow-bg: #fffbeb;
    --yellow-text: #92400e;
    --yellow-border: #fde68a;
    --green: #10b981;
    --green-bg: #ecfdf5;
    --green-text: #065f46;
    --green-border: #a7f3d0;
    --blue: #2563eb;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --radius: 24px;
    --radius-sm: 16px;
    --radius-xs: 12px;
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text-primary);
    min-height: 100vh;
    padding: 24px;
    font-size: 14px;
    line-height: 1.5;
  }

  .container { max-width: 1280px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }

  /* CARDS */
  .card {
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    outline: 1px solid var(--border);
  }
  .card-header { padding: 24px 24px 0; }
  .card-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .card-content { padding: 20px 24px 24px; }

  /* GRID */
  .grid { display: grid; gap: 24px; }
  .grid-2 { grid-template-columns: 1fr 1fr; }
  .grid-3 { grid-template-columns: repeat(3, 1fr); }
  .grid-4 { grid-template-columns: repeat(4, 1fr); }
  .grid-6 { grid-template-columns: repeat(6, 1fr); }
  .grid-main { grid-template-columns: 1.15fr 0.85fr; }
  .grid-main2 { grid-template-columns: 1.1fr 0.9fr; }
  .col-span-2 { grid-column: span 2; }
  .col-span-1 { grid-column: span 1; }

  @media (max-width: 900px) {
    .grid-2, .grid-3, .grid-4, .grid-6, .grid-main, .grid-main2 { grid-template-columns: 1fr; }
    body { padding: 12px; }
  }

  /* HEADER HERO */
  .hero {
    background: var(--surface);
    border-radius: var(--radius);
    padding: 28px;
    outline: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    display: flex;
    gap: 16px;
    justify-content: space-between;
    align-items: flex-end;
    flex-wrap: wrap;
  }
  .hero-label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: var(--text-muted);
    font-family: 'DM Mono', monospace;
  }
  .hero-title {
    font-size: 26px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 8px 0 6px;
    line-height: 1.2;
  }
  .hero-desc { font-size: 13px; color: var(--text-secondary); max-width: 680px; }
  .hero-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }

  /* BUTTONS */
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.15s;
    font-family: 'DM Sans', sans-serif;
  }
  .btn-primary { background: var(--accent); color: #fff; }
  .btn-primary:hover { background: #1e293b; }
  .btn-ghost { background: transparent; color: var(--text-secondary); }
  .btn-ghost:hover { background: var(--surface-2); color: var(--text-primary); }
  .btn-active { background: var(--surface) !important; color: var(--text-primary) !important; box-shadow: var(--shadow-sm); }

  .page-switcher {
    display: inline-flex;
    background: #f1f5f9;
    border-radius: var(--radius-sm);
    padding: 4px;
    outline: 1px solid var(--border);
  }

  /* FORM CONTROLS */
  select, input[type="number"], input[type="text"], textarea {
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 8px 12px;
    width: 100%;
    background: var(--surface);
    color: var(--text-primary);
    outline: none;
    transition: border-color 0.15s;
    -webkit-appearance: none;
  }
  select:focus, input:focus, textarea:focus { border-color: var(--accent); }
  select { cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px; }
  textarea { resize: vertical; line-height: 1.7; }
  label { font-size: 12px; font-weight: 500; color: var(--text-secondary); display: block; margin-bottom: 6px; }
  input[readonly] { background: var(--surface-2); color: var(--text-secondary); }

  /* BADGES */
  .badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 12px;
    font-weight: 500;
    border: 1px solid;
  }
  .badge-red { background: var(--red-bg); color: var(--red-text); border-color: var(--red-border); }
  .badge-yellow { background: var(--yellow-bg); color: var(--yellow-text); border-color: var(--yellow-border); }
  .badge-green { background: var(--green-bg); color: var(--green-text); border-color: var(--green-border); }

  /* KPI CARDS */
  .kpi-label { font-size: 10px; font-weight: 600; letter-spacing: 0.2em; text-transform: uppercase; color: var(--text-muted); font-family: 'DM Mono', monospace; }
  .kpi-value { font-size: 28px; font-weight: 600; color: var(--text-primary); margin: 8px 0 4px; }
  .kpi-sub { font-size: 12px; color: var(--text-secondary); }

  /* CHART CONTAINER */
  .chart-wrap {
    background: var(--surface-2);
    border-radius: var(--radius);
    padding: 16px;
    outline: 1px solid var(--border);
    width: 100%;
    position: relative;
  }

  /* INFO BOXES */
  .info-box {
    background: var(--surface-2);
    border-radius: var(--radius);
    padding: 20px;
    outline: 1px solid var(--border);
  }
  .info-box-title { font-weight: 600; color: var(--text-primary); margin-bottom: 8px; }
  .info-box-red { background: var(--red-bg); outline-color: var(--red-border); color: var(--red-text); }
  .info-box-yellow { background: var(--yellow-bg); outline-color: var(--yellow-border); color: var(--yellow-text); }
  .info-box-green { background: var(--green-bg); outline-color: var(--green-border); color: var(--green-text); }

  /* TABLE */
  .table-wrap { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  thead tr { border-bottom: 1px solid var(--border); }
  th { padding: 12px 16px; color: var(--text-muted); font-weight: 500; text-align: left; font-size: 12px; white-space: nowrap; }
  td { padding: 14px 16px; color: var(--text-primary); border-bottom: 1px solid #f8fafc; white-space: nowrap; }
  tr:last-child td { border-bottom: none; }
  .text-red { color: var(--red-text); font-weight: 600; }
  .text-green { color: var(--green-text); font-weight: 600; }

  /* DRIVER ROW */
  .driver-row {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: var(--surface-2);
    border-radius: var(--radius-sm);
    padding: 14px;
    outline: 1px solid var(--border);
  }
  .driver-icon {
    background: var(--surface);
    border-radius: var(--radius-xs);
    padding: 8px;
    outline: 1px solid var(--border);
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .driver-icon svg { width: 16px; height: 16px; stroke: var(--text-secondary); }
  .driver-title { font-weight: 600; color: var(--text-primary); }
  .driver-meta { font-size: 12px; color: var(--text-muted); margin-top: 2px; font-family: 'DM Mono', monospace; }
  .driver-desc { font-size: 12px; color: var(--text-secondary); margin-top: 4px; }

  /* STAT HIGHLIGHT */
  .stat-dark { background: var(--accent); color: #fff; border-radius: var(--radius); padding: 20px; }
  .stat-dark .kpi-label { color: #94a3b8; }
  .stat-dark .kpi-value { color: #fff; }

  /* METHODOLOGY */
  .method-item { background: var(--surface-2); border-radius: var(--radius); padding: 20px; outline: 1px solid var(--border); }
  .method-title { font-weight: 600; color: var(--text-primary); margin-bottom: 8px; }
  .method-body { font-size: 13px; color: var(--text-secondary); line-height: 1.6; }

  /* SEPARATOR */
  .space-y > * + * { margin-top: 12px; }

  /* HIDDEN */
  .hidden { display: none !important; }

  /* Simple SVG chart using canvas-like approach */
  .simple-chart { width: 100%; height: 340px; }
  .simple-chart-sm { width: 100%; height: 300px; }

  /* Inline chart via SVG */
  svg.chart { overflow: visible; }
  .chart-line { fill: none; stroke-linecap: round; stroke-linejoin: round; }
  .chart-dot { transition: r 0.15s; cursor: pointer; }
  .chart-dot:hover { r: 7; }
  .chart-tooltip {
    position: absolute;
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 12px;
    pointer-events: none;
    box-shadow: var(--shadow);
    z-index: 10;
    display: none;
    min-width: 180px;
  }
  .chart-tooltip strong { color: var(--text-primary); display: block; margin-bottom: 4px; }
  .chart-tooltip span { color: var(--text-secondary); display: block; line-height: 1.7; }

  .action-item { background: var(--surface-2); border-radius: var(--radius); padding: 14px 16px; font-size: 13px; color: var(--text-secondary); outline: 1px solid var(--border); }

  .scroll-x { overflow-x: auto; }
  
  /* scrollbar */
  ::-webkit-scrollbar { width: 6px; height: 6px; }
  ::-webkit-scrollbar-track { background: transparent; }
  ::-webkit-scrollbar-thumb { background: var(--border-2); border-radius: 9999px; }
</style>
</head>
<body>
<div class="container" id="app">
  <!-- Content rendered by JS -->
</div>

<div class="chart-tooltip" id="tooltip"></div>

<script>
// ─── DATA ───────────────────────────────────────────────────────────────────
const FEATURE_META = [
  { key: 'blindspotTbc',        label: 'Blindspot TBC',                  description: 'Area risiko yang belum cukup tertangkap atau ditindaklanjuti.',              icon: 'eye' },
  { key: 'coverageArea',        label: 'Daily Coverage Area',             description: 'Jangkauan pengawasan terhadap area kritikal operasi.',                       icon: 'map-pin' },
  { key: 'goldenRules',         label: 'Golden Rules',                    description: 'Disiplin terhadap kontrol kritikal dan aturan fatal risk.',                  icon: 'activity' },
  { key: 'hazard',              label: 'Pelaporan Hazard',                description: 'Sensitivitas identifikasi deviasi dan hazard lapangan.',                    icon: 'file-text' },
  { key: 'tbc',                 label: 'Pelaporan TBC',                   description: 'Kemampuan mengangkat concern hazard signifikan.',                           icon: 'alert-triangle' },
  { key: 'rfidSupervisor',      label: 'RFID Pengawas',                   description: 'Kapasitas presence pengawasan di lapangan.',                                icon: 'shield-check' },
  { key: 'ratioNonToSupervisor',label: 'Rasio Non Pengawas : Pengawas',  description: 'Ketimpangan antara eksposur aktivitas dan kapasitas pengawasan.',            icon: 'users' },
];

const SITE_ROWS = {
  'All Site': [
    { week:'W40',actualIncidents:0,hazard:23980,rfidNonSupervisor:13180,rfidSupervisor:4175,tbc:8830,goldenRules:3,blindspotTbc:24,coverageArea:73.2},
    { week:'W41',actualIncidents:1,hazard:23640,rfidNonSupervisor:13260,rfidSupervisor:4090,tbc:8650,goldenRules:2,blindspotTbc:28,coverageArea:72.1},
    { week:'W42',actualIncidents:0,hazard:23410,rfidNonSupervisor:13310,rfidSupervisor:4045,tbc:8510,goldenRules:2,blindspotTbc:31,coverageArea:71.4},
    { week:'W43',actualIncidents:1,hazard:23120,rfidNonSupervisor:13395,rfidSupervisor:3980,tbc:8440,goldenRules:2,blindspotTbc:34,coverageArea:70.9},
    { week:'W44',actualIncidents:2,hazard:22840,rfidNonSupervisor:13490,rfidSupervisor:3920,tbc:8335,goldenRules:1,blindspotTbc:39,coverageArea:69.7},
    { week:'W45',actualIncidents:2,hazard:22680,rfidNonSupervisor:13540,rfidSupervisor:3895,tbc:8280,goldenRules:1,blindspotTbc:43,coverageArea:68.4},
    { week:'W46',actualIncidents:3,hazard:22490,rfidNonSupervisor:13620,rfidSupervisor:3845,tbc:8210,goldenRules:1,blindspotTbc:46,coverageArea:67.6},
    { week:'W47',actualIncidents:1,hazard:22720,rfidNonSupervisor:13510,rfidSupervisor:3905,tbc:8290,goldenRules:2,blindspotTbc:37,coverageArea:69.1},
    { week:'W48',actualIncidents:1,hazard:22950,rfidNonSupervisor:13420,rfidSupervisor:3940,tbc:8355,goldenRules:2,blindspotTbc:35,coverageArea:69.8},
    { week:'W49',actualIncidents:0,hazard:23280,rfidNonSupervisor:13360,rfidSupervisor:3995,tbc:8480,goldenRules:2,blindspotTbc:32,coverageArea:70.5},
    { week:'W50',actualIncidents:1,hazard:22600,rfidNonSupervisor:13480,rfidSupervisor:3875,tbc:8190,goldenRules:1,blindspotTbc:42,coverageArea:67.8},
    { week:'W51',actualIncidents:0,hazard:23540,rfidNonSupervisor:13295,rfidSupervisor:4048,tbc:8595,goldenRules:2,blindspotTbc:29,coverageArea:71.3},
  ],
  LMO: [
    { week:'W40',actualIncidents:0,hazard:7210,rfidNonSupervisor:3850,rfidSupervisor:1228,tbc:2520,goldenRules:2,blindspotTbc:9,coverageArea:75.0},
    { week:'W41',actualIncidents:1,hazard:7050,rfidNonSupervisor:3890,rfidSupervisor:1200,tbc:2440,goldenRules:2,blindspotTbc:11,coverageArea:73.9},
    { week:'W42',actualIncidents:0,hazard:6970,rfidNonSupervisor:3925,rfidSupervisor:1188,tbc:2400,goldenRules:2,blindspotTbc:12,coverageArea:73.0},
    { week:'W43',actualIncidents:1,hazard:6900,rfidNonSupervisor:3960,rfidSupervisor:1168,tbc:2370,goldenRules:1,blindspotTbc:14,coverageArea:72.2},
    { week:'W44',actualIncidents:1,hazard:6840,rfidNonSupervisor:3995,rfidSupervisor:1149,tbc:2345,goldenRules:1,blindspotTbc:16,coverageArea:71.6},
    { week:'W45',actualIncidents:2,hazard:6760,rfidNonSupervisor:4035,rfidSupervisor:1128,tbc:2310,goldenRules:1,blindspotTbc:17,coverageArea:70.7},
    { week:'W46',actualIncidents:2,hazard:6715,rfidNonSupervisor:4060,rfidSupervisor:1110,tbc:2295,goldenRules:1,blindspotTbc:18,coverageArea:69.8},
    { week:'W47',actualIncidents:1,hazard:6860,rfidNonSupervisor:4010,rfidSupervisor:1150,tbc:2350,goldenRules:2,blindspotTbc:15,coverageArea:71.2},
    { week:'W48',actualIncidents:0,hazard:6965,rfidNonSupervisor:3970,rfidSupervisor:1174,tbc:2395,goldenRules:2,blindspotTbc:13,coverageArea:72.6},
    { week:'W49',actualIncidents:0,hazard:7055,rfidNonSupervisor:3920,rfidSupervisor:1191,tbc:2425,goldenRules:2,blindspotTbc:11,coverageArea:73.4},
    { week:'W50',actualIncidents:1,hazard:6795,rfidNonSupervisor:4050,rfidSupervisor:1122,tbc:2305,goldenRules:1,blindspotTbc:17,coverageArea:70.1},
    { week:'W51',actualIncidents:0,hazard:7140,rfidNonSupervisor:3895,rfidSupervisor:1210,tbc:2475,goldenRules:2,blindspotTbc:10,coverageArea:74.0},
  ],
  SMO: [
    { week:'W40',actualIncidents:0,hazard:7945,rfidNonSupervisor:4290,rfidSupervisor:1335,tbc:2790,goldenRules:3,blindspotTbc:8,coverageArea:73.6},
    { week:'W41',actualIncidents:0,hazard:7890,rfidNonSupervisor:4325,rfidSupervisor:1318,tbc:2765,goldenRules:2,blindspotTbc:9,coverageArea:73.0},
    { week:'W42',actualIncidents:1,hazard:7750,rfidNonSupervisor:4375,rfidSupervisor:1290,tbc:2705,goldenRules:2,blindspotTbc:11,coverageArea:71.8},
    { week:'W43',actualIncidents:1,hazard:7640,rfidNonSupervisor:4420,rfidSupervisor:1268,tbc:2670,goldenRules:1,blindspotTbc:13,coverageArea:70.9},
    { week:'W44',actualIncidents:2,hazard:7520,rfidNonSupervisor:4460,rfidSupervisor:1245,tbc:2620,goldenRules:1,blindspotTbc:14,coverageArea:69.9},
    { week:'W45',actualIncidents:2,hazard:7445,rfidNonSupervisor:4490,rfidSupervisor:1226,tbc:2590,goldenRules:1,blindspotTbc:16,coverageArea:69.0},
    { week:'W46',actualIncidents:3,hazard:7380,rfidNonSupervisor:4540,rfidSupervisor:1208,tbc:2560,goldenRules:1,blindspotTbc:18,coverageArea:68.1},
    { week:'W47',actualIncidents:1,hazard:7540,rfidNonSupervisor:4465,rfidSupervisor:1250,tbc:2635,goldenRules:2,blindspotTbc:14,coverageArea:70.4},
    { week:'W48',actualIncidents:1,hazard:7630,rfidNonSupervisor:4410,rfidSupervisor:1278,tbc:2675,goldenRules:2,blindspotTbc:12,coverageArea:71.0},
    { week:'W49',actualIncidents:0,hazard:7755,rfidNonSupervisor:4360,rfidSupervisor:1298,tbc:2710,goldenRules:2,blindspotTbc:11,coverageArea:71.9},
    { week:'W50',actualIncidents:1,hazard:7460,rfidNonSupervisor:4515,rfidSupervisor:1216,tbc:2585,goldenRules:1,blindspotTbc:16,coverageArea:68.9},
    { week:'W51',actualIncidents:0,hazard:7830,rfidNonSupervisor:4335,rfidSupervisor:1310,tbc:2750,goldenRules:2,blindspotTbc:9,coverageArea:72.5},
  ],
  GMO: [
    { week:'W40',actualIncidents:0,hazard:7130,rfidNonSupervisor:3890,rfidSupervisor:1260,tbc:2520,goldenRules:2,blindspotTbc:7,coverageArea:71.5},
    { week:'W41',actualIncidents:1,hazard:7010,rfidNonSupervisor:3920,rfidSupervisor:1248,tbc:2485,goldenRules:2,blindspotTbc:8,coverageArea:70.8},
    { week:'W42',actualIncidents:0,hazard:6950,rfidNonSupervisor:3960,rfidSupervisor:1235,tbc:2460,goldenRules:2,blindspotTbc:9,coverageArea:70.0},
    { week:'W43',actualIncidents:0,hazard:6880,rfidNonSupervisor:4010,rfidSupervisor:1218,tbc:2435,goldenRules:2,blindspotTbc:10,coverageArea:69.6},
    { week:'W44',actualIncidents:1,hazard:6795,rfidNonSupervisor:4035,rfidSupervisor:1204,tbc:2400,goldenRules:1,blindspotTbc:11,coverageArea:68.8},
    { week:'W45',actualIncidents:1,hazard:6735,rfidNonSupervisor:4065,rfidSupervisor:1190,tbc:2375,goldenRules:1,blindspotTbc:12,coverageArea:68.2},
    { week:'W46',actualIncidents:2,hazard:6660,rfidNonSupervisor:4095,rfidSupervisor:1176,tbc:2355,goldenRules:1,blindspotTbc:13,coverageArea:67.4},
    { week:'W47',actualIncidents:1,hazard:6780,rfidNonSupervisor:4040,rfidSupervisor:1201,tbc:2405,goldenRules:2,blindspotTbc:10,coverageArea:68.9},
    { week:'W48',actualIncidents:0,hazard:6865,rfidNonSupervisor:3995,rfidSupervisor:1220,tbc:2440,goldenRules:2,blindspotTbc:9,coverageArea:69.7},
    { week:'W49',actualIncidents:0,hazard:6940,rfidNonSupervisor:3960,rfidSupervisor:1232,tbc:2470,goldenRules:2,blindspotTbc:8,coverageArea:70.1},
    { week:'W50',actualIncidents:1,hazard:6705,rfidNonSupervisor:4088,rfidSupervisor:1185,tbc:2370,goldenRules:1,blindspotTbc:12,coverageArea:67.9},
    { week:'W51',actualIncidents:0,hazard:7040,rfidNonSupervisor:3925,rfidSupervisor:1244,tbc:2492,goldenRules:2,blindspotTbc:8,coverageArea:70.8},
  ],
};

// ─── STATE ──────────────────────────────────────────────────────────────────
const state = {
  page: 'dashboard',
  site: 'All Site',
  selectedWeek: 'W50',
  lookback: 6,
  alertThreshold: 30,
  actualOverride: null,
};

// ─── MATH UTILS ─────────────────────────────────────────────────────────────
const fmt = (n, d=2) => !Number.isFinite(n) ? '-' : Number(n).toLocaleString('en-US',{minimumFractionDigits:d,maximumFractionDigits:d});
const clamp = (v,a,b) => Math.min(b,Math.max(a,v));
const toFin = (v,fb=0) => { const p=Number(v); return Number.isFinite(p)?p:fb; };
const mean = a => !a.length?0:a.reduce((s,v)=>s+v,0)/a.length;
const std = a => { if(a.length<=1)return 1; const m=mean(a); const v=a.reduce((s,v)=>s+(v-m)**2,0)/(a.length-1); return Math.sqrt(v)||1; };
const median = a => { if(!a.length)return 0; const s=[...a].sort((a,b)=>a-b); const m=Math.floor(s.length/2); return s.length%2===0?(s[m-1]+s[m])/2:s[m]; };
const quantile = (a,q) => { if(!a.length)return 0; const s=[...a].sort((a,b)=>a-b); const p=clamp(q,0,1)*(s.length-1); const b=Math.floor(p); const r=p-b; return s[b+1]!==undefined?s[b]+r*(s[b+1]-s[b]):s[b]; };

function averageRanks(values) {
  const indexed = values.map((v,i)=>({v,i})).sort((a,b)=>a.v-b.v);
  const ranks = new Array(values.length).fill(0);
  let i=0;
  while(i<indexed.length){ let j=i; while(j<indexed.length&&indexed[j].v===indexed[i].v)j++; const ar=(i+j-1)/2+1; for(let k=i;k<j;k++)ranks[indexed[k].i]=ar; i=j; }
  return ranks;
}
function pearsonCorrelation(xs,ys){ if(!xs.length||xs.length!==ys.length)return 0; const mx=mean(xs),my=mean(ys); let n=0,dx2=0,dy2=0; for(let i=0;i<xs.length;i++){const dx=xs[i]-mx,dy=ys[i]-my;n+=dx*dy;dx2+=dx*dx;dy2+=dy*dy;} if(!dx2||!dy2)return 0; return n/Math.sqrt(dx2*dy2); }
function spearmanCorrelation(xs,ys){ return pearsonCorrelation(averageRanks(xs),averageRanks(ys)); }
function aucBinary(scores,labels){ if(!scores.length||scores.length!==labels.length)return 0.5; const pos=labels.filter(l=>l===1).length,neg=labels.filter(l=>l===0).length; if(!pos||!neg)return 0.5; const ranks=averageRanks(scores); let prs=0; for(let i=0;i<labels.length;i++)if(labels[i]===1)prs+=ranks[i]; return (prs-(pos*(pos+1))/2)/(pos*neg); }
function confusionMetrics(scores,incidents,threshold){ const labels=incidents.map(v=>v>=1?1:0); let tp=0,tn=0,fp=0,fn=0; for(let i=0;i<scores.length;i++){const p=scores[i]>=threshold?1:0,a=labels[i]; if(p===1&&a===1)tp++;else if(p===0&&a===0)tn++;else if(p===1&&a===0)fp++;else fn++;} const tot=tp+tn+fp+fn; const acc=tot?(tp+tn)/tot:0,prec=tp+fp?tp/(tp+fp):0,rec=tp+fn?tp/(tp+fn):0,spec=tn+fp?tn/(tn+fp):0,f1=prec+rec?(2*prec*rec)/(prec+rec):0; return{tp,tn,fp,fn,accuracy:acc,precision:prec,recall:rec,specificity:spec,f1,auc:aucBinary(scores,labels)}; }

function solveLinearSystem(matrix,vector){ const n=matrix.length; const aug=matrix.map((r,ri)=>[...r,vector[ri]]); for(let col=0;col<n;col++){ let pr=col; for(let r=col+1;r<n;r++)if(Math.abs(aug[r][col])>Math.abs(aug[pr][col]))pr=r; if(Math.abs(aug[pr][col])<1e-10)aug[col][col]=1e-10; else if(pr!==col)[aug[col],aug[pr]]=[aug[pr],aug[col]]; const pv=aug[col][col]; for(let j=col;j<=n;j++)aug[col][j]/=pv; for(let r=0;r<n;r++){ if(r===col)continue; const f=aug[r][col]; for(let j=col;j<=n;j++)aug[r][j]-=f*aug[col][j]; } } return aug.map(r=>r[n]); }

function enrichRow(row){ const rns=Math.max(toFin(row.rfidNonSupervisor),0); const rs=Math.max(toFin(row.rfidSupervisor),0); return {...row,hazard:toFin(row.hazard),rfidNonSupervisor:rns,rfidSupervisor:rs,tbc:toFin(row.tbc),goldenRules:toFin(row.goldenRules),blindspotTbc:toFin(row.blindspotTbc),coverageArea:toFin(row.coverageArea),actualIncidents:Math.max(toFin(row.actualIncidents),0),ratioNonToSupervisor:rs>0?rns/rs:0}; }
function getFV(row,key){ return toFin(row[key],0); }

const RIDGE_ALPHA=1.2;
function fitStatisticalModel(rows){ const er=rows.map(enrichRow); const fks=FEATURE_META.map(f=>f.key); const xRaw=er.map(r=>fks.map(k=>getFV(r,k))); const yRaw=er.map(r=>r.actualIncidents); const xMeans=fks.map((_,i)=>mean(xRaw.map(r=>r[i]))); const xStds=fks.map((_,i)=>{const v=std(xRaw.map(r=>r[i]));return v>0?v:1;}); const yMean=mean(yRaw); const yStd=std(yRaw)||1; const xS=xRaw.map(r=>r.map((v,i)=>(v-xMeans[i])/xStds[i])); const yS=yRaw.map(v=>(v-yMean)/yStd); const dim=fks.length; const xtx=Array.from({length:dim},()=>Array.from({length:dim},()=>0)); const xty=Array.from({length:dim},()=>0); for(let i=0;i<xS.length;i++){for(let j=0;j<dim;j++){xty[j]+=xS[i][j]*yS[i];for(let k=0;k<dim;k++)xtx[j][k]+=xS[i][j]*xS[i][k];}} for(let i=0;i<dim;i++)xtx[i][i]+=RIDGE_ALPHA; const betas=solveLinearSystem(xtx,xty).map(v=>Number.isFinite(v)?v:0); function predict(rawRow){const er2=enrichRow(rawRow);const sf=fks.map((k,i)=>(getFV(er2,k)-xMeans[i])/xStds[i]);const ysh=sf.reduce((s,v,i)=>s+v*betas[i],0);return{predictedIncidents:Math.max(yMean+ysh*yStd,0),standardizedFeatures:sf,enrichedRow:er2};} const fitted=er.map(r=>{const p=predict(r);return{...r,predictedIncidents:p.predictedIncidents,standardizedFeatures:p.standardizedFeatures};}); const fp=fitted.map(r=>r.predictedIncidents); const minP=Math.min(...fp),maxP=Math.max(...fp); const sr=maxP-minP||1; const sty=quantile(fp,0.5),str=quantile(fp,0.8); function pts(pi){return clamp(((pi-minP)/sr)*100,0,100);} function ptstatus(pi){if(pi>=str)return'Merah';if(pi>=sty)return'Kuning';return'Hijau';} return{featureKeys:fks,xMeans,xStds,yMean,yStd,betas,fitted,predict,predictedToScore:pts,predictedToStatus:ptstatus}; }

function getBaselineWindow(rows,selectedIndex,lookback){ const si=selectedIndex>=0?selectedIndex:rows.length-1; const sl=clamp(Math.floor(toFin(lookback,6)),2,Math.max(rows.length-1,2)); const start=Math.max(0,si-sl); let br=rows.slice(start,si); if(br.length>=2)return br; br=rows.filter((_,i)=>i!==si).slice(0,Math.min(sl,Math.max(rows.length-1,0))); return br; }

function computeBaselineStats(rows,selectedIndex,lookback){ const br=getBaselineWindow(rows,selectedIndex,lookback).map(enrichRow); const byFeature=FEATURE_META.reduce((acc,f)=>{ const vals=br.map(r=>getFV(r,f.key)); acc[f.key]={mean:mean(vals),median:median(vals),std:std(vals)||1}; return acc; },{}); return{baselineRows:br,byFeature}; }

function computeContributionHistory(rows,model,lookback){ const hist=FEATURE_META.reduce((a,f)=>{a[f.key]=[];return a;},{}); rows.forEach((row,ri)=>{ const bs=computeBaselineStats(rows,ri,lookback); FEATURE_META.forEach((f,fi)=>{ const st=bs.byFeature[f.key]; const z=st.std?(getFV(row,f.key)-st.mean)/st.std:0; const c=z*model.betas[fi]; hist[f.key].push(Number.isFinite(c)?c:0); }); }); return hist; }

function contributionStatus(contribution,historyValues){ const c=Number.isFinite(contribution)?contribution:0; const ph=historyValues.filter(v=>v>0); const yc=ph.length?quantile(ph,0.5):0; const rc=ph.length?quantile(ph,0.8):0; if(c<=0)return{color:'green',label:'Hijau'}; if(c>=rc&&rc>0)return{color:'red',label:'Merah'}; if(c>=yc&&yc>0)return{color:'yellow',label:'Kuning'}; return{color:'yellow',label:'Kuning'}; }

function statusColor(s){ if(s==='Merah')return'red'; if(s==='Kuning')return'yellow'; return'green'; }
function badgeClass(c){ if(c==='red')return'badge badge-red'; if(c==='yellow')return'badge badge-yellow'; return'badge badge-green'; }
function chartStatusColor(s){ if(s==='Merah')return'#ef4444'; if(s==='Kuning')return'#f59e0b'; return'#10b981'; }

// ─── COMPUTED ───────────────────────────────────────────────────────────────
function getSiteRows(){ return SITE_ROWS[state.site].map(enrichRow); }
function getModel(rows){ return fitStatisticalModel(rows); }
function getSelectedIndex(rows){ const i=rows.findIndex(r=>r.week===state.selectedWeek); return i>=0?i:Math.max(rows.length-1,0); }

function getActualRow(rows,selectedIndex){ const base=rows[selectedIndex]??rows[rows.length-1]; if(!base)return null; return state.actualOverride?enrichRow({...base,...state.actualOverride}):base; }

function computeWeeklySeries(rows,model,lookback){ return rows.map((row,ri)=>{ const p=model.predict(row); const score=model.predictedToScore(p.predictedIncidents); const status=model.predictedToStatus(p.predictedIncidents); const rb=computeBaselineStats(rows,ri,lookback); const contribs=FEATURE_META.map((f,fi)=>{ const st=rb.byFeature[f.key]; const z=st.std?(getFV(row,f.key)-st.mean)/st.std:0; return{key:f.key,contribution:z*model.betas[fi]}; }).sort((a,b)=>b.contribution-a.contribution); const tdKey=contribs[0]?.key; const topDriver=FEATURE_META.find(f=>f.key===tdKey)?.label??'Terkendali'; return{week:row.week,score,status,actualIncidents:row.actualIncidents,predictedIncidents:p.predictedIncidents,topDriver,fill:chartStatusColor(status)}; }); }

function computeAnalysis(rows,model,selectedIndex,actualRow,baselineStats,contributionHistory){ if(!actualRow)return{score:0,status:'Hijau',predictedIncidents:0,indicators:[],coefficientTable:[],topDrivers:[],actionPriority:[],narrative:'Tidak ada data minggu terpilih.'}; const p=model.predict(actualRow); const score=model.predictedToScore(p.predictedIncidents); const status=model.predictedToStatus(p.predictedIncidents); const indicators=FEATURE_META.map((f,fi)=>{ const st=baselineStats.byFeature[f.key]; const av=getFV(actualRow,f.key); const z=st.std?(av-st.mean)/st.std:0; const beta=model.betas[fi]; const contrib=z*beta; const cs=contributionStatus(contrib,contributionHistory[f.key]??[]); return{...f,actual:av,baselineMean:st.mean,baselineMedian:st.median,baselineStd:st.std,zScore:z,beta,contribution:contrib,direction:contrib>=0?'Menaikkan predicted risk':'Menurunkan predicted risk',status:cs}; }).sort((a,b)=>b.contribution-a.contribution); const topDrivers=indicators.filter(i=>i.contribution>0).slice(0,3); const topProtectors=indicators.filter(i=>i.contribution<0).sort((a,b)=>a.contribution-b.contribution).slice(0,2); const coefficientTable=FEATURE_META.map((f,i)=>({...f,beta:model.betas[i]})).sort((a,b)=>Math.abs(b.beta)-Math.abs(a.beta)); const np=[`Pada ${state.site} minggu ${state.selectedWeek}, model statistik menghasilkan predicted incident level sebesar ${fmt(p.predictedIncidents,2)} dengan score ${fmt(score,1)} dan status ${status.toLowerCase()}.`,`Reference minggu ini dihitung dari rolling baseline ${baselineStats.baselineRows.length} minggu sebelumnya, dengan pendekatan mean dan standard deviation per indikator.`]; if(actualRow.actualIncidents>0)np.push(`Insiden aktual tercatat ${fmt(actualRow.actualIncidents,0)}, sehingga pembacaan difokuskan pada kontributor statistik yang mendorong predicted risk ke arah positif.`); else np.push('Belum ada insiden aktual, namun score tetap dibaca sebagai tekanan risiko relatif terhadap pola historis site.'); if(topDrivers.length)np.push(`Kontributor risiko terbesar minggu ini adalah ${topDrivers.map(i=>i.label).join(', ')}.`); if(topProtectors.length)np.push(`Sementara itu, faktor yang masih menahan kenaikan risiko adalah ${topProtectors.map(i=>i.label).join(', ')}.`); const actionPriority=topDrivers.map((item,i)=>`${i+1}. ${item.label} — kontribusi ${fmt(item.contribution,2)} pada model, dengan deviasi z-score ${fmt(item.zScore,2)} dari rolling baseline.`); return{score,status,predictedIncidents:p.predictedIncidents,indicators,coefficientTable,topDrivers,actionPriority,narrative:np.join(' ')}; }

// ─── SVG ICONS ───────────────────────────────────────────────────────────────
const ICONS = {
  'eye': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`,
  'map-pin': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>`,
  'activity': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>`,
  'file-text': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>`,
  'alert-triangle': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`,
  'shield-check': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>`,
  'users': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
  'trending-up': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>`,
  'brain': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96-.46 2.5 2.5 0 0 1-1.1-4.79 3 3 0 0 1 .34-5.58 2.5 2.5 0 0 1 1.32-4.67z"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96-.46 2.5 2.5 0 0 0 1.1-4.79 3 3 0 0 0-.34-5.58 2.5 2.5 0 0 0-1.32-4.67z"/></svg>`,
  'target': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>`,
  'gauge': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z"/><path d="m12 17 4-4-4-4"/><path d="M8 12h8"/></svg>`,
  'sigma': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 7V5H6l6 7-6 7h12v-2"/></svg>`,
  'layout': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>`,
  'bar-chart': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>`,
  'refresh': `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>`,
};

function icon(name, size=16, color='currentColor') {
  const svg = ICONS[name] || '';
  return svg.replace('<svg ', `<svg width="${size}" height="${size}" style="color:${color}" `);
}

// ─── SVG LINE CHART ─────────────────────────────────────────────────────────
function renderLineChart(containerId, data, series, options={}) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const W = container.clientWidth || 600;
  const H = options.height || 300;
  const pad = { top: 20, right: 20, bottom: 30, left: 45 };
  const iW = W - pad.left - pad.right;
  const iH = H - pad.top - pad.bottom;

  const allVals = series.flatMap(s => data.map(d => d[s.key])).filter(Number.isFinite);
  const minY = options.minY !== undefined ? options.minY : Math.min(0, ...allVals);
  const maxY = options.maxY !== undefined ? options.maxY : Math.max(...allVals) * 1.1 || 10;
  const yRange = maxY - minY || 1;

  const xPos = (i) => pad.left + (i / (data.length - 1)) * iW;
  const yPos = (v) => pad.top + (1 - (v - minY) / yRange) * iH;

  let svgContent = `<svg width="${W}" height="${H}" viewBox="0 0 ${W} ${H}" class="chart">`;

  // Grid lines
  const yTicks = 5;
  for (let t = 0; t <= yTicks; t++) {
    const v = minY + (yRange * t) / yTicks;
    const y = yPos(v);
    svgContent += `<line x1="${pad.left}" y1="${y}" x2="${W - pad.right}" y2="${y}" stroke="#e2e8f0" stroke-dasharray="3 3"/>`;
    svgContent += `<text x="${pad.left - 6}" y="${y + 4}" text-anchor="end" font-size="11" fill="#94a3b8" font-family="DM Mono, monospace">${fmt(v, 0)}</text>`;
  }

  // X ticks
  data.forEach((d, i) => {
    const x = xPos(i);
    svgContent += `<text x="${x}" y="${H - 6}" text-anchor="middle" font-size="11" fill="#94a3b8" font-family="DM Mono, monospace">${d.week}</text>`;
  });

  // Reference lines
  if (options.refLines) {
    options.refLines.forEach(rl => {
      const y = yPos(rl.value);
      svgContent += `<line x1="${pad.left}" y1="${y}" x2="${W - pad.right}" y2="${y}" stroke="${rl.color}" stroke-dasharray="4 4" stroke-width="1.5"/>`;
    });
  }

  // Lines + dots per series
  series.forEach(s => {
    const pts = data.map((d, i) => ({ x: xPos(i), y: yPos(d[s.key]), d }));
    const pathD = pts.map((p, i) => (i === 0 ? `M ${p.x},${p.y}` : `L ${p.x},${p.y}`)).join(' ');
    svgContent += `<path d="${pathD}" class="chart-line" stroke="${s.color}" stroke-width="${s.width || 2.5}"/>`;
    pts.forEach((p, i) => {
      const dd = JSON.stringify(p.d).replace(/"/g, '&quot;');
      svgContent += `<circle cx="${p.x}" cy="${p.y}" r="4" fill="${s.color}" stroke="white" stroke-width="2" class="chart-dot" data-point="${dd}" data-series="${s.label}" style="cursor:pointer"/>`;
    });
  });

  svgContent += `</svg>`;
  container.innerHTML = svgContent;
  container.style.position = 'relative';

  // Tooltip
  const tooltip = document.getElementById('tooltip');
  container.querySelectorAll('.chart-dot').forEach(dot => {
    dot.addEventListener('mouseenter', e => {
      const d = JSON.parse(e.target.dataset.point);
      tooltip.innerHTML = `<strong>${d.week}</strong><span>Statistical Score: ${fmt(d.score, 1)}</span><span>Predicted Inc: ${fmt(d.predictedIncidents, 2)}</span><span>Actual Inc: ${fmt(d.actualIncidents, 0)}</span>${d.topDriver ? `<span>Top Driver: ${d.topDriver}</span>` : ''}`;
      tooltip.style.display = 'block';
    });
    dot.addEventListener('mousemove', e => {
      tooltip.style.left = (e.pageX + 12) + 'px';
      tooltip.style.top = (e.pageY - 40) + 'px';
    });
    dot.addEventListener('mouseleave', () => { tooltip.style.display = 'none'; });
  });
}

// ─── RENDER ──────────────────────────────────────────────────────────────────
function render() {
  const app = document.getElementById('app');
  const rows = getSiteRows();
  const model = getModel(rows);
  const selectedIndex = getSelectedIndex(rows);
  const actualRow = getActualRow(rows, selectedIndex);
  const baselineStats = computeBaselineStats(rows, selectedIndex, state.lookback);
  const contributionHistory = computeContributionHistory(rows, model, state.lookback);
  const weeklySeries = computeWeeklySeries(rows, model, state.lookback);
  const analysis = computeAnalysis(rows, model, selectedIndex, actualRow, baselineStats, contributionHistory);

  const avgScore = mean(weeklySeries.map(r => r.score));
  const peak = weeklySeries.reduce((m, r) => r.score > m.score ? r : m, weeklySeries[0]);
  const latest = weeklySeries[weeklySeries.length - 1];

  // Validation metrics
  const swScores = weeklySeries.map(r => r.score);
  const swInc = weeklySeries.map(r => r.actualIncidents);
  const nwScores = weeklySeries.slice(0, -1).map(r => r.score);
  const nwInc = weeklySeries.slice(1).map(r => r.actualIncidents);
  const swM = { pearson: pearsonCorrelation(swScores, swInc), spearman: spearmanCorrelation(swScores, swInc), ...confusionMetrics(swScores, swInc, state.alertThreshold) };
  const nwM = { pearson: pearsonCorrelation(nwScores, nwInc), spearman: spearmanCorrelation(nwScores, nwInc), ...confusionMetrics(nwScores, nwInc, state.alertThreshold) };

  const siteOptions = Object.keys(SITE_ROWS).map(s => `<option value="${s}" ${s === state.site ? 'selected' : ''}>${s}</option>`).join('');
  const weekOptions = SITE_ROWS[state.site].map(r => `<option value="${r.week}" ${r.week === state.selectedWeek ? 'selected' : ''}>${r.week}</option>`).join('');

  const dashActive = state.page === 'dashboard';

  // Field list for actual values
  const fieldsList = [
    ['hazard', 'Pelaporan Hazard'],
    ['rfidSupervisor', 'RFID Pengawas'],
    ['tbc', 'Pelaporan TBC'],
    ['goldenRules', 'Golden Rules'],
    ['blindspotTbc', 'Blindspot TBC'],
    ['coverageArea', 'Daily Coverage Area (%)'],
    ['rfidNonSupervisor', 'RFID Non Pengawas'],
  ];

  const actualValuesForm = fieldsList.map(([f, l]) => `
    <div style="margin-bottom:12px">
      <label>${l}</label>
      <input type="number" value="${fmt(actualRow?.[f] ?? 0, 0)}" data-field="${f}" class="actual-input" step="any">
    </div>`).join('');

  const baselineRef = FEATURE_META.map(f => `
    <div class="info-box" style="padding:14px;margin-bottom:8px">
      <div style="font-weight:600;color:var(--text-primary);font-size:13px">${f.label}</div>
      <div style="font-size:12px;color:var(--text-muted);margin-top:4px;font-family:'DM Mono',monospace">
        Mean ${fmt(baselineStats.byFeature[f.key].mean, 2)} · Median ${fmt(baselineStats.byFeature[f.key].median, 2)} · Std ${fmt(baselineStats.byFeature[f.key].std, 2)}
      </div>
    </div>`).join('');

  const topDriversHTML = analysis.topDrivers.length === 0
    ? `<div class="info-box info-box-green" style="font-size:13px">Tidak ada kontribusi positif besar pada minggu ini.</div>`
    : analysis.topDrivers.map(item => `
        <div class="driver-row">
          <div class="driver-icon">${icon(item.icon, 16)}</div>
          <div>
            <div class="driver-title">${item.label}</div>
            <div class="driver-meta">β = ${fmt(item.beta, 2)} · z = ${fmt(item.zScore, 2)} · contribution = ${fmt(item.contribution, 2)}</div>
            <div class="driver-desc">${item.description}</div>
          </div>
        </div>`).join('');

  const indicatorTable = analysis.indicators.map((item, i) => `
    <tr>
      <td>${i + 1}</td>
      <td style="font-weight:600">${item.label}</td>
      <td>${fmt(item.actual, 2)}</td>
      <td>${fmt(item.baselineMean, 2)}</td>
      <td class="${item.zScore >= 0 ? '' : 'text-muted'}" style="font-family:'DM Mono',monospace">${fmt(item.zScore, 2)}</td>
      <td class="${item.beta >= 0 ? 'text-red' : 'text-green'}" style="font-family:'DM Mono',monospace">${fmt(item.beta, 2)}</td>
      <td class="${item.contribution >= 0 ? 'text-red' : 'text-green'}" style="font-family:'DM Mono',monospace">${fmt(item.contribution, 2)}</td>
      <td style="color:var(--text-secondary)">${item.direction}</td>
      <td><span class="${badgeClass(item.status.color)}">${item.status.label}</span></td>
    </tr>`).join('');

  const coeffTable = analysis.coefficientTable.map(item => `
    <div class="driver-row">
      <div class="driver-icon">${icon(item.icon, 16)}</div>
      <div style="flex:1">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:8px">
          <div style="font-weight:600;color:var(--text-primary)">${item.label}</div>
          <div style="font-size:13px;font-weight:700;font-family:'DM Mono',monospace;color:${item.beta >= 0 ? 'var(--red-text)' : 'var(--green-text)'}">β = ${fmt(item.beta, 2)}</div>
        </div>
        <div style="font-size:12px;color:var(--text-secondary);margin-top:3px">${item.description}</div>
      </div>
    </div>`).join('');

  const actionPriorityHTML = analysis.actionPriority.length === 0
    ? `<div class="info-box info-box-green" style="font-size:13px">Tidak ada kontribusi risiko yang menonjol. Pertahankan konsistensi kontrol.</div>`
    : analysis.actionPriority.map(line => `<div class="action-item">${line}</div>`).join('');

  const alertTableRows = weeklySeries.map(row => `
    <tr>
      <td style="font-weight:600">${row.week}</td>
      <td style="font-family:'DM Mono',monospace">${fmt(row.score, 1)}</td>
      <td style="font-family:'DM Mono',monospace">${fmt(row.predictedIncidents, 2)}</td>
      <td style="font-family:'DM Mono',monospace">${fmt(row.actualIncidents, 0)}</td>
      <td>${row.score >= state.alertThreshold ? '<span style="color:var(--red-text);font-weight:600">Alert</span>' : 'No Alert'}</td>
      <td><span class="${badgeClass(statusColor(row.status))}">${row.status}</span></td>
    </tr>`).join('');

  const dashboardPage = `
    <div class="grid grid-main">
      <!-- Trend Chart Card -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">${icon('trending-up', 18)} Statistical score trend per week</div>
        </div>
        <div class="card-content">
          <div class="chart-wrap" id="trend-chart-wrap" style="height:320px"></div>
          <div class="grid grid-3" style="margin-top:16px;gap:12px">
            <div class="info-box">
              <div class="kpi-label">Predicted Incident</div>
              <div class="kpi-value" style="font-size:22px">${fmt(analysis.predictedIncidents, 2)}</div>
              <div class="kpi-sub">Fitted from standardized ridge model</div>
            </div>
            <div class="info-box">
              <div class="kpi-label">Score</div>
              <div class="kpi-value" style="font-size:22px">${fmt(analysis.score, 1)}</div>
              <div class="kpi-sub">Normalized from model prediction</div>
            </div>
            <div class="info-box">
              <div class="kpi-label">Status</div>
              <div style="margin-top:10px"><span class="${badgeClass(statusColor(analysis.status))}">${analysis.status}</span></div>
              <div class="kpi-sub" style="margin-top:10px">Derived from site-specific score quantiles</div>
            </div>
          </div>
        </div>
      </div>
      <!-- Top Drivers -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">${icon('brain', 18)} Top statistical drivers</div>
        </div>
        <div class="card-content space-y">${topDriversHTML}</div>
      </div>
    </div>

    <div class="grid grid-main2">
      <!-- Selected week & rolling ref -->
      <div class="card">
        <div class="card-header"><div class="card-title">Selected week and rolling reference</div></div>
        <div class="card-content">
          <div class="grid grid-3" style="gap:12px;margin-bottom:16px">
            <div>
              <label>Week</label>
              <select id="week-select">${weekOptions}</select>
            </div>
            <div>
              <label>Site</label>
              <input type="text" value="${state.site}" readonly>
            </div>
            <div>
              <label>Actual Incidents</label>
              <input type="number" id="actual-incidents-input" value="${actualRow?.actualIncidents ?? 0}" min="0">
            </div>
          </div>
          <div class="grid grid-2" style="gap:16px">
            <div class="card" style="background:var(--surface-2);box-shadow:none">
              <div class="card-header"><div class="card-title" style="font-size:15px">Actual values</div></div>
              <div class="card-content">${actualValuesForm}</div>
            </div>
            <div class="card" style="background:var(--surface-2);box-shadow:none">
              <div class="card-header"><div class="card-title" style="font-size:15px">Rolling baseline reference</div></div>
              <div class="card-content">
                <div class="info-box" style="margin-bottom:10px;padding:14px">
                  <div style="font-weight:600;font-size:13px">Window used</div>
                  <div style="font-size:12px;color:var(--text-secondary);margin-top:4px">${baselineStats.baselineRows.length} minggu historis sebelum ${state.selectedWeek}</div>
                </div>
                ${baselineRef}
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Narrative + Actions -->
      <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card">
          <div class="card-header"><div class="card-title">Narrative insight</div></div>
          <div class="card-content">
            <textarea style="min-height:220px;font-size:13px;line-height:1.7" readonly>${analysis.narrative}</textarea>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><div class="card-title">Priority actions</div></div>
          <div class="card-content space-y">${actionPriorityHTML}</div>
        </div>
      </div>
    </div>

    <!-- Indicator Table -->
    <div class="card">
      <div class="card-header"><div class="card-title">Indicator contribution table</div></div>
      <div class="card-content">
        <div class="scroll-x">
          <table>
            <thead><tr><th>Priority</th><th>Indicator</th><th>Actual</th><th>Baseline Mean</th><th>Z-score</th><th>Std. β</th><th>Contribution</th><th>Direction</th><th>Status</th></tr></thead>
            <tbody>${indicatorTable}</tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Methodology + Coefficients -->
    <div class="grid grid-2">
      <div class="card">
        <div class="card-header"><div class="card-title">${icon('sigma', 18)} Statistical methodology</div></div>
        <div class="card-content space-y">
          <div class="method-item"><div class="method-title">Baseline / reference</div><div class="method-body">Rolling baseline dihitung dari ${state.lookback} minggu historis sebelumnya pada site yang sama. Untuk tiap indikator digunakan mean, median, dan standard deviation.</div></div>
          <div class="method-item"><div class="method-title">Weight / coefficient</div><div class="method-body">Bobot indikator berasal dari standardized coefficient ridge regression yang di-fit pada histori site. Tidak ada expert weight manual pada versi ini.</div></div>
          <div class="method-item"><div class="method-title">Overall score</div><div class="method-body">Score mingguan berasal dari predicted incident level model lalu dinormalisasi ke 0–100 pada distribusi fitted prediction site yang sama.</div></div>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title">Model coefficient ranking</div></div>
        <div class="card-content space-y">${coeffTable}</div>
      </div>
    </div>`;

  const accuracyPage = `
    <div class="grid grid-main">
      <!-- Overlay chart -->
      <div class="card">
        <div class="card-header"><div class="card-title">${icon('trending-up', 18)} Overlay score statistik vs insiden aktual</div></div>
        <div class="card-content">
          <div class="chart-wrap" id="acc-chart-wrap" style="height:320px"></div>
          <div class="info-box" style="margin-top:16px;font-size:13px;color:var(--text-secondary)">
            Same-week menilai seberapa baik score statistik menjelaskan minggu yang sedang berjalan. Next-week menilai seberapa baik score itu bekerja sebagai early warning satu minggu ke depan.
          </div>
        </div>
      </div>
      <!-- Alert table -->
      <div class="card">
        <div class="card-header"><div class="card-title">${icon('bar-chart', 18)} Tabel alert vs aktual</div></div>
        <div class="card-content">
          <div class="scroll-x">
            <table>
              <thead><tr><th>Week</th><th>Score</th><th>Pred. Inc</th><th>Actual</th><th>Prediksi</th><th>Status</th></tr></thead>
              <tbody>${alertTableRows}</tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="grid grid-2">
      <!-- Same week -->
      <div class="card">
        <div class="card-header"><div class="card-title">${icon('target', 18)} Accuracy check — same week</div></div>
        <div class="card-content">
          <div class="grid grid-3" style="gap:12px;margin-bottom:12px">
            <div class="stat-dark"><div class="kpi-label">Pearson</div><div class="kpi-value" style="font-size:24px">${fmt(swM.pearson, 2)}</div></div>
            <div class="info-box"><div class="kpi-label">Spearman</div><div class="kpi-value" style="font-size:24px">${fmt(swM.spearman, 2)}</div></div>
            <div class="info-box"><div class="kpi-label">AUC</div><div class="kpi-value" style="font-size:24px">${fmt(swM.auc, 2)}</div></div>
          </div>
          <div class="grid grid-4" style="gap:10px">
            <div class="info-box" style="padding:14px"><div class="kpi-label" style="font-size:10px">Accuracy</div><div style="font-size:20px;font-weight:700;margin-top:8px">${fmt(swM.accuracy * 100, 1)}%</div></div>
            <div class="info-box" style="padding:14px"><div class="kpi-label" style="font-size:10px">Precision</div><div style="font-size:20px;font-weight:700;margin-top:8px">${fmt(swM.precision * 100, 1)}%</div></div>
            <div class="info-box" style="padding:14px"><div class="kpi-label" style="font-size:10px">Recall</div><div style="font-size:20px;font-weight:700;margin-top:8px">${fmt(swM.recall * 100, 1)}%</div></div>
            <div class="info-box" style="padding:14px"><div class="kpi-label" style="font-size:10px">Specificity</div><div style="font-size:20px;font-weight:700;margin-top:8px">${fmt(swM.specificity * 100, 1)}%</div></div>
          </div>
        </div>
      </div>
      <!-- Next week -->
      <div class="card">
        <div class="card-header"><div class="card-title">${icon('gauge', 18)} Accuracy check — next week</div></div>
        <div class="card-content">
          <div class="grid grid-3" style="gap:12px;margin-bottom:12px">
            <div class="stat-dark"><div class="kpi-label">Pearson</div><div class="kpi-value" style="font-size:24px">${fmt(nwM.pearson, 2)}</div></div>
            <div class="info-box"><div class="kpi-label">Spearman</div><div class="kpi-value" style="font-size:24px">${fmt(nwM.spearman, 2)}</div></div>
            <div class="info-box"><div class="kpi-label">AUC</div><div class="kpi-value" style="font-size:24px">${fmt(nwM.auc, 2)}</div></div>
          </div>
          <div class="grid grid-4" style="gap:10px">
            <div class="info-box" style="padding:14px"><div class="kpi-label" style="font-size:10px">Accuracy</div><div style="font-size:20px;font-weight:700;margin-top:8px">${fmt(nwM.accuracy * 100, 1)}%</div></div>
            <div class="info-box" style="padding:14px"><div class="kpi-label" style="font-size:10px">Precision</div><div style="font-size:20px;font-weight:700;margin-top:8px">${fmt(nwM.precision * 100, 1)}%</div></div>
            <div class="info-box" style="padding:14px"><div class="kpi-label" style="font-size:10px">Recall</div><div style="font-size:20px;font-weight:700;margin-top:8px">${fmt(nwM.recall * 100, 1)}%</div></div>
            <div class="info-box" style="padding:14px"><div class="kpi-label" style="font-size:10px">Specificity</div><div style="font-size:20px;font-weight:700;margin-top:8px">${fmt(nwM.specificity * 100, 1)}%</div></div>
          </div>
          <div class="info-box info-box-yellow" style="margin-top:12px;font-size:13px">
            Bila same-week jauh lebih tinggi daripada next-week, maka model ini lebih kuat untuk back analysis daripada early warning murni.
          </div>
        </div>
      </div>
    </div>`;

  app.innerHTML = `
    <!-- HERO -->
    <div class="hero">
      <div>
        <div class="hero-label">Incident Back Analysis</div>
        <h1 class="hero-title">Fully Statistical Back Analysis Tool</h1>
        <p class="hero-desc">Baseline dihitung dari rolling historical window, bobot berasal dari koefisien ridge regression terstandarisasi, dan overall score berasal dari predicted incident risk yang dinormalisasi dari model site.</p>
      </div>
      <div class="hero-actions">
        <div class="page-switcher">
          <button class="btn btn-ghost ${dashActive ? 'btn-active' : ''}" id="btn-dashboard">${icon('layout', 16)} Analysis Dashboard</button>
          <button class="btn btn-ghost ${!dashActive ? 'btn-active' : ''}" id="btn-accuracy">${icon('target', 16)} Accuracy Check</button>
        </div>
        <button class="btn btn-primary" id="btn-reset">${icon('refresh', 16)} Reset demo</button>
      </div>
    </div>

    <!-- KPI GRID -->
    <div class="grid grid-6" style="gap:16px">
      <div class="card" style="grid-column:span 1">
        <div class="card-content" style="padding:20px">
          <div class="kpi-label">Site</div>
          <div style="margin-top:10px"><select id="site-select">${siteOptions}</select></div>
        </div>
      </div>
      <div class="card" style="grid-column:span 1">
        <div class="card-content" style="padding:20px">
          <div class="kpi-label">Lookback baseline</div>
          <div style="margin-top:10px"><input type="number" id="lookback-input" value="${state.lookback}" min="2" max="10"></div>
          <div style="font-size:11px;color:var(--text-muted);margin-top:6px">Jumlah minggu historis untuk rolling reference.</div>
        </div>
      </div>
      <div class="card" style="grid-column:span 1">
        <div class="card-content" style="padding:20px">
          <div class="kpi-label">Alert threshold</div>
          <div style="margin-top:10px"><input type="number" id="threshold-input" value="${state.alertThreshold}" min="0" max="100"></div>
          <div style="font-size:11px;color:var(--text-muted);margin-top:6px">Dipakai untuk klasifikasi di accuracy check.</div>
        </div>
      </div>
      <div class="card" style="grid-column:span 1">
        <div class="card-content" style="padding:20px">
          <div class="kpi-label">Average score</div>
          <div class="kpi-value">${fmt(avgScore, 1)}</div>
          <div class="kpi-sub">Mean statistical risk score</div>
        </div>
      </div>
      <div class="card" style="grid-column:span 1">
        <div class="card-content" style="padding:20px">
          <div class="kpi-label">Peak week</div>
          <div class="kpi-value">${peak?.week ?? '-'}</div>
          <div class="kpi-sub">Score ${fmt(peak?.score, 1)} · ${peak?.status}</div>
        </div>
      </div>
      <div class="card" style="grid-column:span 1">
        <div class="card-content" style="padding:20px">
          <div class="kpi-label">Latest week</div>
          <div class="kpi-value">${latest?.week ?? '-'}</div>
          <div class="kpi-sub">Score ${fmt(latest?.score, 1)} · ${latest?.status}</div>
        </div>
      </div>
    </div>

    <!-- PAGE CONTENT -->
    <div id="page-content">
      ${dashActive ? dashboardPage : accuracyPage}
    </div>
  `;

  // ── Event listeners ───────────────────────────────────────────────────────
  document.getElementById('site-select')?.addEventListener('change', e => {
    state.site = e.target.value;
    state.selectedWeek = SITE_ROWS[state.site][SITE_ROWS[state.site].length - 2]?.week ?? SITE_ROWS[state.site][0].week;
    state.actualOverride = null;
    render();
  });

  document.getElementById('lookback-input')?.addEventListener('change', e => {
    state.lookback = clamp(Math.floor(toFin(e.target.value, 6)), 2, 10);
    render();
  });

  document.getElementById('threshold-input')?.addEventListener('change', e => {
    state.alertThreshold = clamp(toFin(e.target.value, 30), 0, 100);
    render();
  });

  document.getElementById('week-select')?.addEventListener('change', e => {
    state.selectedWeek = e.target.value;
    state.actualOverride = null;
    render();
  });

  document.getElementById('actual-incidents-input')?.addEventListener('change', e => {
    const v = Math.max(toFin(e.target.value, 0), 0);
    state.actualOverride = { ...(state.actualOverride ?? {}), actualIncidents: v };
    render();
  });

  document.querySelectorAll('.actual-input').forEach(inp => {
    inp.addEventListener('change', e => {
      const field = e.target.dataset.field;
      const v = toFin(e.target.value, 0);
      state.actualOverride = { ...(state.actualOverride ?? {}), [field]: v };
      render();
    });
  });

  document.getElementById('btn-dashboard')?.addEventListener('click', () => { state.page = 'dashboard'; render(); });
  document.getElementById('btn-accuracy')?.addEventListener('click', () => { state.page = 'accuracy'; render(); });
  document.getElementById('btn-reset')?.addEventListener('click', () => {
    state.page = 'dashboard'; state.site = 'All Site'; state.lookback = 6; state.alertThreshold = 30;
    state.selectedWeek = 'W50'; state.actualOverride = null;
    render();
  });

  // ── Charts ────────────────────────────────────────────────────────────────
  if (dashActive) {
    setTimeout(() => {
      renderLineChart('trend-chart-wrap', weeklySeries, [
        { key: 'score', color: '#0f172a', width: 3, label: 'Score' },
      ], {
        height: 300, minY: 0, maxY: 100,
        refLines: [{ value: 50, color: '#f59e0b' }, { value: 80, color: '#ef4444' }],
      });
    }, 0);
  } else {
    setTimeout(() => {
      const accData = weeklySeries.map(d => ({ ...d, actualScaled: d.actualIncidents * (100 / (Math.max(...weeklySeries.map(r => r.actualIncidents)) || 1)) }));
      renderLineChart('acc-chart-wrap', weeklySeries, [
        { key: 'score', color: '#0f172a', width: 3, label: 'Score' },
        { key: 'actualIncidents', color: '#2563eb', width: 2.5, label: 'Actual Incidents' },
      ], {
        height: 300, minY: 0,
        refLines: [{ value: state.alertThreshold, color: '#0f172a' }],
      });
    }, 0);
  }
}

// ── Initial render ──────────────────────────────────────────────────────────
render();

// Resize charts on window resize
let resizeTimer;
window.addEventListener('resize', () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    const rows = getSiteRows();
    const model = getModel(rows);
    const weeklySeries = computeWeeklySeries(rows, model, state.lookback);
    if (state.page === 'dashboard') {
      renderLineChart('trend-chart-wrap', weeklySeries, [
        { key: 'score', color: '#0f172a', width: 3, label: 'Score' },
      ], { height: 300, minY: 0, maxY: 100, refLines: [{ value: 50, color: '#f59e0b' }, { value: 80, color: '#ef4444' }] });
    } else {
      renderLineChart('acc-chart-wrap', weeklySeries, [
        { key: 'score', color: '#0f172a', width: 3, label: 'Score' },
        { key: 'actualIncidents', color: '#2563eb', width: 2.5, label: 'Actual' },
      ], { height: 300, minY: 0, refLines: [{ value: state.alertThreshold, color: '#0f172a' }] });
    }
  }, 200);
});
</script>
</body>
</html>