@extends('layouts.masterRoster')

@section('title', 'Performance Dashboard')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root{
      --kt-text:#071437;
      --kt-muted:#99a1b7;
      --kt-border:#eef0f4;
      --kt-primary:#1b84ff;
      --kt-body:#f5f8fa;
      --kt-card:#ffffff;
    }

    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body {
      font-family: 'Poppins', sans-serif;
      background: var(--kt-body);
      color: var(--kt-text);
    }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: #edf1f7; }
    ::-webkit-scrollbar-thumb { background: #c9d2e3; border-radius: 999px; }
    ::-webkit-scrollbar-thumb:hover { background: #b7c3d9; }

    /* Metronic-like cards */
    .kt-card {
      background: var(--kt-card);
      border: 1px solid var(--kt-border);
      border-radius: 0.95rem;
      box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }
    .kt-card-header {
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:.75rem;
      padding: 1rem 1.25rem;
      border-bottom: 1px solid var(--kt-border);
    }
    .kt-card-title {
      font-size: .95rem;
      font-weight: 600;
      color: var(--kt-text);
      letter-spacing: -.01em;
    }
    .kt-card-subtitle {
      font-size: .7rem;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: var(--kt-muted);
      margin-top: .15rem;
    }
    .kt-card-body {
      padding: 1.25rem;
    }

    /* Heatmap header toolbar (month filter + export) */
    .heatmap-toolbar {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    .heatmap-toolbar .month-control {
      display: inline-flex;
      align-items: center;
      background: #fff;
      border: 1px solid var(--kt-border);
      border-radius: 14px;
      overflow: hidden;
      height: 48px;
      box-shadow: 0 1px 3px rgba(0,0,0,.04);
      transition: box-shadow .2s ease, border-color .2s ease;
    }
    .heatmap-toolbar .month-control:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,.06);
      border-color: #e2e8f0;
    }
    .heatmap-toolbar .month-control .btn-nav {
      border: 0;
      width: 44px;
      height: 100%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #64748b;
      background: transparent;
      transition: background .2s ease, color .2s ease;
    }
    .heatmap-toolbar .month-control .btn-nav:hover {
      background: #f1f5f9;
      color: #2563eb;
    }
    .heatmap-toolbar .month-control .label {
      min-width: 160px;
      text-align: center;
      font-weight: 700;
      color: var(--kt-text);
      font-size: .9rem;
      padding: 0 1rem;
      border-left: 1px solid var(--kt-border);
      border-right: 1px solid var(--kt-border);
      height: 100%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    .heatmap-toolbar .btn-export-heatmap {
      height: 48px;
      border-radius: 12px;
      font-weight: 700;
      padding: 0 1.25rem;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: none;
      background: #2563eb;
      color: #fff;
      box-shadow: 0 2px 8px rgba(37,99,235,.25);
      transition: transform .15s ease, box-shadow .2s ease;
    }
    .heatmap-toolbar .btn-export-heatmap:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 14px rgba(37,99,235,.35);
      color: #fff;
      background: #1d4ed8;
    }
    .heatmap-toolbar .btn-export-heatmap:active { transform: translateY(0); }
    #heatmapRefreshBtn .bi-arrow-clockwise.spin { animation: heatmap-spin .8s linear infinite; }
    @keyframes heatmap-spin { to { transform: rotate(360deg); } }
    @media (max-width: 768px) {
      .kt-card-header.heatmap-header { flex-direction: column; align-items: stretch; gap: 1rem; }
      .heatmap-toolbar { width: 100%; justify-content: space-between; }
      .heatmap-toolbar .month-control .label { min-width: 120px; font-size: .85rem; }
    }

    /* Buttons */
    .btn-kt {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:.4rem;
      border:1px solid var(--kt-border);
      background:#fff;
      color:#4b5675;
      font-size:.75rem;
      font-weight:500;
      padding:.55rem .8rem;
      border-radius:.6rem;
      transition:all .18s ease;
      line-height:1;
    }
    .btn-kt:hover { border-color:#d8e0ee; background:#f9fbfd; color:#252f4a; }
    .btn-kt.active {
      background: #1b84ff;
      color: #fff;
      border-color: #1b84ff;
      box-shadow: 0 6px 18px rgba(27,132,255,.2);
    }
    .btn-kt-soft {
      background: #f9fbff;
      border-color: #e4edfb;
      color: #1b84ff;
    }

    /* Badge */
    .badge-kt {
      display:inline-flex;
      align-items:center;
      gap:.35rem;
      border-radius:999px;
      padding:.25rem .55rem;
      font-size:.68rem;
      font-weight:600;
      line-height:1;
    }
    .badge-success { background:#e8fff3; color:#17c653; }
    .badge-danger  { background:#ffeef3; color:#f8285a; }
    .badge-neutral { background:#f1f5f9; color:#475569; }
    .badge-primary { background:#e9f3ff; color:#1b84ff; }
    .badge-warning { background:#fff8dd; color:#b78200; }

    /* KPI */
    .kpi-value {
      font-size: 1.9rem;
      font-weight: 700;
      line-height: 1.1;
      letter-spacing: -.02em;
      color: #071437;
    }
    .kpi-label {
      color: #78829d;
      font-size: .72rem;
      text-transform: uppercase;
      letter-spacing: .08em;
      font-weight: 600;
    }
    .progress-track {
      height: 7px;
      background: #eef3f9;
      border-radius: 999px;
      overflow: hidden;
    }
    .progress-fill {
      height:100%;
      background: linear-gradient(90deg, #1b84ff 0%, #6ea8fe 100%);
      border-radius: 999px;
      transition: width .8s cubic-bezier(.22,1,.36,1);
    }

    /* Tables */
    .table-kt {
      width:100%;
      border-collapse: separate;
      border-spacing: 0;
    }
    .table-kt thead th {
      background:#f9fafb;
      color:#78829d;
      font-size:.68rem;
      font-weight:700;
      text-transform:uppercase;
      letter-spacing:.08em;
      border-bottom:1px solid var(--kt-border);
      padding:.85rem .9rem;
      white-space:nowrap;
    }
    .table-kt tbody td {
      padding:.8rem .9rem;
      border-bottom:1px solid var(--kt-border);
      font-size:.78rem;
      color:#252f4a;
      vertical-align:middle;
    }
    .table-kt tbody tr:hover td {
      background:#fcfdff;
    }

    /* Heatmap cells */
    .heatmap-cell {
      position: relative;
      border-radius: .65rem;
      padding: .65rem .35rem;
      text-align:center;
      font-weight:600;
      transition: transform .15s ease, box-shadow .15s ease;
      border:1px solid rgba(0,0,0,0.03);
      cursor:pointer;
      overflow:hidden;
    }
    .heatmap-cell::after{
      content:'';
      position:absolute; inset:0;
      background: linear-gradient(180deg, rgba(255,255,255,.18), rgba(255,255,255,0));
      pointer-events:none;
    }
    .heatmap-cell:hover{
      transform: translateY(-1px);
      box-shadow: 0 8px 18px rgba(15,23,42,.08);
      z-index: 2;
    }

    .hm-100 { background:#1b84ff; color:#fff; }
    .hm-80  { background:#3a96ff; color:#fff; }
    .hm-67  { background:#5aa8ff; color:#fff; }
    .hm-62  { background:#6cb2ff; color:#fff; }
    .hm-56  { background:#7ebcff; color:#0f172a; }
    .hm-50  { background:#a9d2ff; color:#0f172a; }
    .hm-33  { background:#d7eaff; color:#1e293b; }
    .hm-20  { background:#edf5ff; color:#334155; }
    .hm-0   { background:#f8fafc; color:#94a3b8; border:1px dashed #d9e2ef; }
    .hm-empty { background:transparent; color:#c3ccdc; border:1px dashed #eef0f4; }

    /* Tooltip */
    [data-tip] { position: relative; }
    [data-tip]:hover::before {
      content: attr(data-tip);
      position: absolute;
      bottom: 110%;
      left: 50%;
      transform: translateX(-50%);
      background: #111827;
      color: #fff;
      font-size: .68rem;
      padding: .35rem .6rem;
      border-radius: .5rem;
      white-space: nowrap;
      box-shadow: 0 8px 20px rgba(0,0,0,.18);
      z-index: 40;
      pointer-events:none;
    }

    .animate-in {
      animation: fadeUp .45s ease both;
    }
    @keyframes fadeUp {
      from { opacity:0; transform: translateY(10px); }
      to   { opacity:1; transform: translateY(0); }
    }

    .sidebar-link {
      display:flex;
      align-items:center;
      gap:.65rem;
      padding:.7rem .85rem;
      border-radius:.7rem;
      color:#cbd5e1;
      font-size:.82rem;
      font-weight:500;
      transition:all .15s ease;
    }
    .sidebar-link:hover { background:rgba(255,255,255,.06); color:#fff; }
    .sidebar-link.active {
      background: linear-gradient(90deg, rgba(27,132,255,.22), rgba(27,132,255,.08));
      color:#fff;
      border:1px solid rgba(27,132,255,.25);
    }
    .sidebar-dot { width:8px; height:8px; border-radius:999px; background:#64748b; }
    .sidebar-link.active .sidebar-dot { background:#1b84ff; box-shadow:0 0 0 4px rgba(27,132,255,.18); }

    .mini-spark { display:flex; align-items:flex-end; gap:3px; height:26px; }
    .mini-spark-bar {
      width:7px; border-radius:2px;
      background:#dce6f5;
      transition: all .25s ease;
    }
    .mini-spark-bar.lit { background:#1b84ff; }

    .row-muted td { background:#fcfcfd; }

    /* Coverage cards & layer indicator */
    .coverage-metric {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: .8rem;
    }

    .coverage-icon {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .8rem;
      font-weight: 700;
      border: 1px solid transparent;
    }

    .layer-pill {
      min-width: 32px;
      height: 22px;
      padding: 0 .45rem;
      border-radius: 999px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: .62rem;
      font-weight: 700;
      border: 1px solid #e5e7eb;
      background: #f8fafc;
      color: #64748b;
    }

    .layer-pill.on {
      background: #e9f3ff;
      border-color: #bfdbfe;
      color: #1b84ff;
    }

    .layer-pill.off {
      background: #f8fafc;
      border-style: dashed;
      color: #94a3b8;
    }

    .coverage-chip {
      display:inline-flex;
      align-items:center;
      gap:.35rem;
      padding:.35rem .6rem;
      border-radius:999px;
      font-size:.7rem;
      font-weight:600;
      border:1px solid #e5e7eb;
      background:#fff;
      color:#475569;
    }
    .coverage-chip .dot {
      width:8px; height:8px; border-radius:999px;
    }

    .mk-bc-tag {
      font-size: .66rem;
      font-weight: 700;
      border-radius: 999px;
      padding: .22rem .5rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    .mk-bc-on {
      background: #e8fff3;
      color: #17c653;
    }
    .mk-bc-off {
      background: #f1f5f9;
      color: #64748b;
    }

    .loc-row-clickable {
      cursor: pointer;
      transition: background-color .15s ease;
    }
    .loc-row-clickable:hover td {
      background: #fbfdff;
    }
    .loc-row-clickable.active td {
      background: #f3f8ff !important;
    }

    .coverage-grid-4 {
      display:grid;
      grid-template-columns: repeat(2, minmax(0,1fr));
      gap:.75rem;
    }
    @media (min-width: 640px) {
      .coverage-grid-4 {
        grid-template-columns: repeat(4, minmax(0,1fr));
      }
    }

    /* LMO Performance section — smooth & polished */
    .lmo-perf { scroll-margin-top: 1rem; }
    .lmo-perf .headline h1 {
      font-size: clamp(1.5rem, 2.2vw, 2.5rem);
      font-weight: 800;
      margin-bottom: .35rem;
      color: var(--kt-text);
      letter-spacing: -.02em;
      line-height: 1.2;
    }
    .lmo-perf .headline p {
      color: #64748b;
      font-size: 1rem;
      margin-bottom: 0;
      font-weight: 500;
      transition: color .2s ease;
    }
    .lmo-perf .month-control {
      display: flex;
      align-items: center;
      background: #fff;
      border: 1px solid var(--kt-border);
      border-radius: 14px;
      overflow: hidden;
      height: 50px;
      box-shadow: 0 1px 3px rgba(0,0,0,.04);
      transition: box-shadow .25s ease, border-color .2s ease;
    }
    .lmo-perf .month-control:hover { box-shadow: 0 4px 12px rgba(0,0,0,.06); border-color: #e2e8f0; }
    .lmo-perf .month-control .btn {
      border: 0;
      border-radius: 0;
      width: 48px;
      height: 48px;
      color: #475467;
      transition: background .2s ease, color .2s ease;
    }
    .lmo-perf .month-control .btn:hover { background: #f1f5f9; color: #2563eb; }
    .lmo-perf .month-control .label {
      min-width: 180px;
      text-align: center;
      font-weight: 700;
      color: var(--kt-text);
      border-left: 1px solid var(--kt-border);
      border-right: 1px solid var(--kt-border);
      height: 100%;
      display: grid;
      place-items: center;
      background: #fff;
      font-size: .95rem;
    }
    .lmo-perf .btn-export {
      height: 50px;
      border-radius: 14px;
      font-weight: 700;
      padding: 0 1.25rem;
      box-shadow: 0 2px 8px rgba(37,99,235,.2);
      transition: transform .15s ease, box-shadow .25s ease, background .2s ease;
    }
    .lmo-perf .btn-export:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(37,99,235,.3); }
    .lmo-perf .btn-export:active { transform: translateY(0); }
    .lmo-perf .card-panel {
      background: var(--kt-card);
      border: 1px solid var(--kt-border);
      border-radius: 18px;
      box-shadow: 0 2px 8px rgba(16,24,40,.04), 0 1px 0 rgba(16,24,40,.02);
      transition: box-shadow .3s ease, border-color .2s ease;
    }
    .lmo-perf .card-panel:hover { box-shadow: 0 8px 24px rgba(16,24,40,.06); }
    .lmo-perf .calendar-card { overflow: hidden; }
    .lmo-perf .calendar-scroll { border-radius: 0 0 18px 18px; }
    .lmo-perf .calendar-head {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
      border-bottom: 1px solid var(--kt-border);
    }
    .lmo-perf .calendar-head div {
      padding: .9rem .75rem;
      font-size: .75rem;
      font-weight: 700;
      letter-spacing: .1em;
      color: #64748b;
      text-transform: uppercase;
      text-align: center;
      border-right: 1px solid rgba(226,232,240,.8);
    }
    .lmo-perf .calendar-head div:last-child { border-right: 0; }
    .lmo-perf .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); }
    .lmo-perf .day-cell {
      min-height: 148px;
      border-right: 1px solid rgba(226,232,240,.9);
      border-bottom: 1px solid rgba(226,232,240,.9);
      padding: .75rem .85rem;
      position: relative;
      background: #fff;
      transition: background .25s ease, transform .2s ease, box-shadow .25s ease;
    }
    .lmo-perf .calendar-grid .day-cell:nth-child(7n) { border-right: 0; }
    .lmo-perf .day-cell.heatmap-day-cell { cursor: pointer; }
    .lmo-perf .day-cell:not(.state-neutral):not(:empty):hover {
      transform: scale(1.02);
      z-index: 2;
      box-shadow: 0 8px 20px rgba(0,0,0,.08);
    }
    .lmo-perf .day-cell.state-good { background: linear-gradient(145deg, #f0fdf4 0%, #dcfce7 100%); }
    .lmo-perf .day-cell.state-warn { background: linear-gradient(145deg, #fffbeb 0%, #fef3c7 100%); }
    .lmo-perf .day-cell.state-bad { background: linear-gradient(145deg, #fff5f5 0%, #ffe4e6 100%); }
    .lmo-perf .day-cell.state-neutral { background: #f8fafc; }
    .lmo-perf .day-num {
      color: #94a3b8;
      font-size: .9rem;
      font-weight: 600;
      line-height: 1;
      margin-bottom: .35rem;
      transition: color .2s ease;
    }
    .lmo-perf .day-cell:hover .day-num { color: #64748b; }
    .lmo-perf .day-center {
      height: calc(100% - 22px);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: .55rem;
      text-align: center;
    }
    .lmo-perf .score {
      font-size: clamp(1.35rem, 1.4vw, 1.95rem);
      font-weight: 800;
      letter-spacing: -.02em;
      line-height: 1;
      transition: transform .2s ease;
    }
    .lmo-perf .day-cell:hover .score { transform: scale(1.05); }
    .lmo-perf .score.good { color: #047857; }
    .lmo-perf .score.warn { color: #b45309; }
    .lmo-perf .score.bad { color: #e11d48; }
    .lmo-perf .score.neutral { color: #6b7280; }
    .lmo-perf .mini-progress {
      width: 62%;
      height: 6px;
      border-radius: 99px;
      background: rgba(148,163,184,.2);
      overflow: hidden;
    }
    .lmo-perf .mini-progress .fill {
      height: 100%;
      border-radius: 99px;
      transition: width .6s cubic-bezier(.4,0,.2,1);
    }
    .lmo-perf .fill.good { background: linear-gradient(90deg, #059669, #10b981); }
    .lmo-perf .fill.warn { background: linear-gradient(90deg, #d97706, #f59e0b); }
    .lmo-perf .fill.bad { background: linear-gradient(90deg, #dc2626, #f43f5e); }
    .lmo-perf .fill.neutral { background: #94a3b8; }
    .lmo-perf .off-label { color: #94a3b8; font-size: .95rem; font-weight: 500; }
    .lmo-perf .day-cell.selected {
      box-shadow: inset 0 0 0 3px #2563eb, 0 4px 16px rgba(37,99,235,.15);
      background: linear-gradient(145deg, #eff6ff 0%, #dbeafe 100%) !important;
      z-index: 3;
    }
    .lmo-perf .day-cell.selected .day-num { color: #1d4ed8; font-weight: 800; }
    .lmo-perf .day-cell.selected:hover { transform: none; box-shadow: inset 0 0 0 3px #2563eb, 0 6px 20px rgba(37,99,235,.2); }
    .lmo-perf .current-pill {
      position: absolute;
      top: 10px;
      right: 10px;
      background: linear-gradient(135deg, #2563eb, #3b82f6);
      color: #fff;
      font-size: .6rem;
      font-weight: 700;
      letter-spacing: .05em;
      padding: .25rem .5rem;
      border-radius: 8px;
      text-transform: uppercase;
      box-shadow: 0 2px 6px rgba(37,99,235,.35);
    }
    .lmo-perf .completion {
      color: #1d4ed8;
      font-size: .8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .03em;
      margin-top: -.15rem;
    }
    .lmo-perf .section-title {
      font-weight: 800;
      font-size: 1.15rem;
      margin: 0;
      color: var(--kt-text);
      letter-spacing: -.01em;
    }
    .lmo-perf .soft-chip-btn {
      border: 1px solid var(--kt-border);
      background: #f8fafc;
      color: #344054;
      border-radius: 12px;
      padding: .5rem .9rem;
      font-weight: 600;
      font-size: .88rem;
      transition: background .2s ease, border-color .2s ease, color .2s ease, transform .15s ease;
    }
    .lmo-perf .soft-chip-btn:hover {
      background: #f1f5f9;
      border-color: #cbd5e1;
      color: #1e293b;
      transform: translateY(-1px);
    }
    .lmo-perf .table-wrap {
      border-top: 1px solid var(--kt-border);
      overflow-x: auto;
      border-radius: 0 0 18px 18px;
    }
    .lmo-perf .table-custom { margin-bottom: 0; min-width: 940px; }
    .lmo-perf .table-custom thead th {
      font-size: .75rem;
      letter-spacing: .1em;
      color: #64748b;
      text-transform: uppercase;
      font-weight: 700;
      border-bottom: 1px solid var(--kt-border);
      background: linear-gradient(180deg, #fafbfc 0%, #f5f6f8 100%);
      padding: 1rem 1.1rem;
      white-space: nowrap;
    }
    .lmo-perf .table-custom tbody tr {
      transition: background .2s ease;
    }
    .lmo-perf .table-custom tbody tr:hover { background: #f8fafc; }
    .lmo-perf .table-custom tbody td {
      padding: 1rem 1.1rem;
      border-bottom: 1px solid rgba(226,232,240,.8);
      vertical-align: middle;
      transition: color .15s ease;
    }
    .lmo-perf .status-pill {
      display: inline-flex;
      align-items: center;
      padding: .28rem .6rem;
      border-radius: 999px;
      font-size: .82rem;
      line-height: 1.2;
      font-weight: 600;
      border: 1px solid transparent;
      white-space: nowrap;
      transition: transform .15s ease, box-shadow .2s ease;
    }
    .lmo-perf .status-pill:hover { transform: scale(1.03); }
    .lmo-perf .status-complete { color: #067647; background: #ecfdf3; border-color: #a7f3d0; }
    .lmo-perf .status-progress { color: #1d4ed8; background: #eff6ff; border-color: #bfdbfe; }
    .lmo-perf .status-pending { color: #475569; background: #f1f5f9; border-color: #e2e8f0; }
    .lmo-perf .status-issue { color: #b91c1c; background: #fef2f2; border-color: #fecaca; }
    .lmo-perf .action-link {
      font-weight: 700;
      text-decoration: none;
      font-size: .9rem;
      white-space: nowrap;
      transition: color .2s ease, opacity .2s ease;
    }
    .lmo-perf .action-link:hover { opacity: .85; }
    .lmo-perf .action-blue { color: #2563eb; }
    .lmo-perf .action-blue:hover { color: #1d4ed8; }
    .lmo-perf .action-red { color: #dc2626; }
    .lmo-perf .action-red:hover { color: #b91c1c; }
    .lmo-perf .card-footer-lite {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      padding: 1rem 1.1rem;
      color: #64748b;
      font-size: .9rem;
      background: #fafbfc;
      border-radius: 0 0 18px 18px;
    }
    .lmo-perf .pagination-lite .page-link {
      border-radius: 10px !important;
      margin-left: .3rem;
      border: 1px solid var(--kt-border);
      color: #475569;
      min-width: 36px;
      height: 36px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      transition: background .2s ease, border-color .2s ease, color .2s ease;
    }
    .lmo-perf .pagination-lite .page-link:hover {
      background: #f1f5f9;
      border-color: #cbd5e1;
      color: #1e293b;
    }
    .lmo-perf .pagination-lite .page-item.active .page-link {
      background: linear-gradient(135deg, #2563eb, #3b82f6);
      border-color: #2563eb;
      color: #fff;
      box-shadow: 0 2px 6px rgba(37,99,235,.3);
    }
    @media (max-width: 992px) {
      .lmo-perf .month-toolbar { width: 100%; justify-content: stretch !important; }
      .lmo-perf .month-control .label { min-width: 0; }
      .lmo-perf .calendar-head div { font-size: .65rem; padding: .65rem .35rem; }
      .lmo-perf .day-cell { min-height: 110px; padding: .55rem; }
      .lmo-perf .day-cell:not(.state-neutral):not(:empty):hover { transform: none; }
      .lmo-perf .score { font-size: 1.25rem; }
      .lmo-perf .completion { font-size: .68rem; }
    }
    @media (max-width: 768px) {
      .lmo-perf .calendar-grid, .lmo-perf .calendar-head { min-width: 840px; }
      .lmo-perf .calendar-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; scroll-behavior: smooth; }
    }

    /* LMO Coverage Performance Overview - Bootstrap-style */
    .lmo-coverage-root {
      --lmo-bg:#f4f6fb;
      --lmo-panel:#ffffff;
      --lmo-line:#e6ebf2;
      --lmo-text:#101828;
      --lmo-muted:#667085;
      --lmo-blue:#2563eb;
      --lmo-blue-soft:#eaf1ff;
      --lmo-green:#22b573;
      --lmo-green-soft:#eafaf2;
      --lmo-orange:#f59e0b;
      --lmo-red:#ef4444;
      --lmo-red-soft:#fff1f2;
    }
    .lmo-page-head {
      background:#fff;
      border:1px solid var(--lmo-line,#e6ebf2);
      border-radius:18px;
      padding:22px 22px 18px;
      margin-bottom:16px;
    }
    .lmo-breadcrumb-row {
      color:#667085;
      font-size:.9rem;
      margin-bottom:10px;
      display:flex;
      flex-wrap:wrap;
      gap:8px;
      align-items:center;
    }
    .lmo-breadcrumb-row .current { color:#1d4ed8; font-weight:700; }
    .lmo-page-head-main {
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:18px;
      flex-wrap:wrap;
    }
    .lmo-page-title h2 { margin:0; font-size:2rem; line-height:1.15; font-weight:900; letter-spacing:-.02em; }
    .lmo-page-title p { margin:.35rem 0 0; color:var(--lmo-muted); font-size:1.05rem; }
    .lmo-head-actions { display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-left:auto; }
    .lmo-live-pill {
      background:#def6ea;
      color:#12a361;
      border-radius:999px;
      padding:8px 14px;
      font-weight:800;
      font-size:.78rem;
      letter-spacing:.04em;
      display:inline-flex;
      align-items:center;
      gap:8px;
      text-transform:uppercase;
    }
    .lmo-live-pill::before {
      content:"";
      width:7px; height:7px;
      border-radius:50%;
      background:#22c55e;
      box-shadow:0 0 0 6px rgba(34,197,94,.12);
    }
    .lmo-segmented {
      background:#f2f5fa;
      border-radius:12px;
      padding:4px;
      display:inline-flex;
      gap:4px;
    }
    .lmo-segmented button {
      border:none;
      background:transparent;
      color:#667085;
      font-weight:700;
      padding:8px 16px;
      border-radius:10px;
      min-width:70px;
    }
    .lmo-segmented button.active {
      background:#fff;
      color:#1d4ed8;
      box-shadow:0 1px 2px rgba(16,24,40,.06);
    }
    .lmo-btn-export {
      border:none;
      background:#2563eb;
      color:#fff;
      font-weight:800;
      border-radius:12px;
      padding:12px 18px;
      min-height:46px;
      display:flex;
      align-items:center;
      gap:10px;
      box-shadow:0 10px 20px rgba(37,99,235,.22);
    }
    .lmo-card-grid {
      display:grid;
      grid-template-columns:repeat(4, minmax(0, 1fr));
      gap:16px;
      margin-bottom:16px;
    }
    .lmo-kpi-card {
      background:#fff;
      border:1px solid var(--lmo-line,#e6ebf2);
      border-radius:16px;
      padding:18px 18px 16px;
      box-shadow:0 1px 0 rgba(16,24,40,.02);
    }
    .lmo-kpi-top {
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:10px;
      margin-bottom:12px;
    }
    .lmo-kpi-icon {
      width:44px; height:44px;
      border-radius:12px;
      display:grid;
      place-items:center;
      font-size:1.15rem;
      font-weight:700;
    }
    .lmo-icon-blue { background:var(--lmo-blue-soft); color:var(--lmo-blue); }
    .lmo-icon-green { background:#dff5eb; color:#1ea66b; }
    .lmo-icon-red { background:#fee9ea; color:#ef4444; }
    .lmo-delta { font-weight:800; font-size:.95rem; display:flex; align-items:center; gap:4px; }
    .lmo-delta.up { color:#12b76a; }
    .lmo-delta.down { color:#f04438; }
    .lmo-kpi-title { color:#475467; font-weight:500; font-size:1.05rem; margin-bottom:2px; }
    .lmo-kpi-value { font-size:2rem; font-weight:900; line-height:1.1; letter-spacing:-.02em; margin-bottom:8px; }
    .lmo-kpi-sub { color:#98a2b3; font-size:.9rem; }
    .lmo-kpi-card-clickable { cursor: pointer; transition: box-shadow .2s ease, border-color .2s ease; }
    .lmo-kpi-card-clickable:hover { box-shadow: 0 4px 12px rgba(16,24,40,.08); border-color: var(--lmo-blue, #1b84ff) !important; }
    .lmo-layer-grid {
      display:grid;
      grid-template-columns:repeat(4, minmax(0,1fr));
      gap:16px;
      margin-bottom:16px;
    }
    .lmo-layer-card {
      background:#fff;
      border:1px solid var(--lmo-line,#e6ebf2);
      border-radius:14px;
      padding:14px 16px 14px;
    }
    .lmo-layer-row { display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:10px; }
    .lmo-layer-name { color:#5d6b86; font-weight:800; font-size:.95rem; letter-spacing:.03em; text-transform:uppercase; }
    .lmo-layer-value { font-weight:900; font-size:1.05rem; letter-spacing:-.01em; }
    .lmo-progress-thin {
      height:6px;
      background:#edf1f7;
      border-radius:999px;
      overflow:hidden;
    }
    .lmo-progress-thin > span { display:block; height:100%; border-radius:999px; }
    .lmo-table-card {
      background:#fff;
      border:1px solid var(--lmo-line,#e6ebf2);
      border-radius:20px;
      overflow:hidden;
      box-shadow:0 4px 24px rgba(15,23,42,.06);
      transition:box-shadow .25s ease, border-color .25s ease;
    }
    .lmo-table-card:hover { box-shadow:0 8px 32px rgba(15,23,42,.08); border-color:#e2e8f0; }
    .lmo-table-head {
      padding:24px 28px;
      border-bottom:1px solid var(--lmo-line,#e6ebf2);
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:16px;
      flex-wrap:wrap;
      background:linear-gradient(180deg, #fefefe 0%, #fafbfe 100%);
    }
    .lmo-table-head h3 { margin:0; font-size:1.05rem; color:#667085; font-weight:500; }
    .lmo-table-head .big { display:block; color:#101828; font-size:1.55rem; line-height:1.2; font-weight:900; margin-bottom:4px; letter-spacing:-.02em; }
    .lmo-table-head .desc { color:#98a2b3; font-size:.95rem; }
    .lmo-table-actions { display:flex; gap:16px; align-items:center; flex-wrap:wrap; color:#667085; font-weight:500; }
    .lmo-table-actions .link-strong { color:#1d4ed8; font-weight:800; text-decoration:none; transition:color .2s ease, opacity .2s ease; }
    .lmo-table-actions .link-strong:hover { color:#1e40af; opacity:.9; }
    .lmo-action-sep { width:1px; height:14px; background:linear-gradient(180deg, transparent, #e2e8f0, transparent); border-radius:1px; }
    .lmo-team-label { color:#64748b; font-size:.9rem; }
    .lmo-coverage-val--danger { color:#ef4444 !important; }
    .lmo-table-scroll {
      overflow-x:auto;
      overflow-y:visible;
      -webkit-overflow-scrolling:touch;
      scroll-behavior:smooth;
      border-radius:0 0 20px 20px;
    }
    .lmo-table-scroll::-webkit-scrollbar { height:8px; }
    .lmo-table-scroll::-webkit-scrollbar-track { background:#f1f5f9; border-radius:0 0 20px 20px; }
    .lmo-table-scroll::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:999px; }
    /* Coverage by Location card accent */
    .lmo-coverage-card { border-left: 4px solid var(--kt-primary); }
    .lmo-coverage-header { flex-wrap: wrap; gap: 1rem; }
    .lmo-coverage-icon-wrap {
      width: 44px; height: 44px;
      border-radius: 12px;
      background: linear-gradient(135deg, #e9f3ff 0%, #dbeafe 100%);
      color: var(--kt-primary);
      display: grid; place-items: center;
      font-size: 1.25rem;
      flex-shrink: 0;
    }
    .lmo-coverage-legend {
      display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
      font-size: .7rem; color: var(--kt-muted);
      letter-spacing: .02em;
    }
    .lmo-coverage-legend span { display: inline-flex; align-items: center; gap: .35rem; }
    .lmo-coverage-legend .text-green-600 { color: #16a34a; }
    .lmo-coverage-legend .text-amber-500 { color: #f59e0b; }
    .lmo-coverage-legend .text-slate-300 { color: #cbd5e1; }
    table.lmo-coverage-table {
      width:100%;
      border-collapse:separate;
      border-spacing:0;
      min-width:1040px;
    }
    .lmo-coverage-table thead th {
      background:linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
      color:#64748b;
      font-size:.78rem;
      letter-spacing:.06em;
      text-transform:uppercase;
      font-weight:800;
      padding:18px 16px;
      border-bottom:1px solid #e2e8f0;
      white-space:nowrap;
      text-align:left;
    }
    .lmo-coverage-table thead th:first-child { padding-left:28px; }
    .lmo-coverage-table tbody td {
      padding:20px 16px;
      border-bottom:1px solid #f1f5f9;
      vertical-align:middle;
      background:#fff;
      transition:background .2s ease;
    }
    .lmo-coverage-table tbody td:first-child { padding-left:28px; }
    .lmo-coverage-table tbody tr { transition:background .2s ease; }
    .lmo-coverage-table tbody tr:nth-child(even) td { background:#fafbfd; }
    .lmo-coverage-table tbody tr:hover td { background:#f0f7ff !important; }
    .lmo-coverage-table tbody tr:last-child td { border-bottom:none; }
    .lmo-area-cell { display:flex; align-items:center; gap:14px; min-width:240px; }
    .lmo-status-dot {
      width:10px; height:10px; border-radius:50%; flex:0 0 auto;
      box-shadow:0 0 0 4px rgba(0,0,0,.04);
      transition:transform .2s ease, box-shadow .2s ease;
    }
    .lmo-coverage-table tbody tr:hover .lmo-status-dot { box-shadow:0 0 0 6px rgba(0,0,0,.06); transform:scale(1.1); }
    .lmo-dot-green { background:#22b573; }
    .lmo-dot-orange { background:#f59e0b; }
    .lmo-dot-red { background:#ef4444; }
    .lmo-area-name { font-weight:900; font-size:1rem; line-height:1.2; margin-bottom:2px; color:#0f172a; }
    .lmo-area-meta { color:#94a3b8; font-size:.85rem; line-height:1.2; }
    .lmo-area-alert { color:#ef4444; font-weight:800; font-size:.8rem; text-transform:uppercase; margin-top:4px; line-height:1.1; }
    .lmo-level-icons { display:flex; gap:10px; align-items:center; }
    .lmo-check-badge, .lmo-warn-badge, .lmo-x-badge, .lmo-empty-badge {
      width:20px; height:20px;
      border-radius:50%;
      display:grid;
      place-items:center;
      font-size:.7rem;
      font-weight:700;
      line-height:1;
      transition:transform .2s ease, box-shadow .2s ease;
    }
    .lmo-coverage-table tbody tr:hover .lmo-check-badge,
    .lmo-coverage-table tbody tr:hover .lmo-warn-badge,
    .lmo-coverage-table tbody tr:hover .lmo-x-badge { transform:scale(1.08); }
    .lmo-check-badge { background:linear-gradient(135deg, #22b573, #16a34a); color:#fff; box-shadow:0 2px 6px rgba(34,197,94,.3); }
    .lmo-warn-badge { background:linear-gradient(135deg, #f59e0b, #d97706); color:#fff; box-shadow:0 2px 6px rgba(245,158,11,.3); }
    .lmo-x-badge { background:linear-gradient(135deg, #ef4444, #dc2626); color:#fff; box-shadow:0 2px 6px rgba(239,68,68,.3); }
    .lmo-empty-badge { background:#e2e8f0; color:transparent; }
    .lmo-coverage-cell { display:flex; align-items:center; gap:14px; min-width:170px; }
    .lmo-coverage-bar {
      width:128px;
      height:8px;
      border-radius:999px;
      background:#e2e8f0;
      overflow:hidden;
    }
    .lmo-coverage-bar > span {
      height:100%; display:block; border-radius:999px;
      transition:width .5s cubic-bezier(.4,0,.2,1);
    }
    .lmo-coverage-val { font-weight:900; font-size:.95rem; min-width:42px; transition:color .2s ease; }
    .lmo-pill {
      display:inline-flex; align-items:center; border-radius:10px;
      padding:6px 12px; font-size:.75rem; font-weight:800;
      letter-spacing:.02em; text-transform:uppercase;
      border:1px solid transparent; white-space:nowrap;
      transition:transform .2s ease, box-shadow .2s ease;
    }
    .lmo-coverage-table tbody tr:hover .lmo-pill { transform:translateY(-1px); }
    .lmo-pill-optimal { color:#12a361; background:linear-gradient(135deg, #ecfdf5, #d1fae5); border-color:#a7f3d0; }
    .lmo-pill-incomplete { color:#d97706; background:linear-gradient(135deg, #fffbeb, #fef3c7); border-color:#fde68a; }
    .lmo-pill-critical { color:#ef4444; background:linear-gradient(135deg, #fef2f2, #fee2e2); border-color:#fecaca; }
    .lmo-last-inspection { min-width:150px; line-height:1.3; }
    .lmo-last-inspection .main { font-weight:700; color:#0f172a; display:block; }
    .lmo-last-inspection .sub { color:#94a3b8; font-size:.85rem; display:block; margin-top:4px; }
    .lmo-last-inspection .sub.warn { color:#f59e0b; font-weight:700; }
    .lmo-last-inspection .sub.red { color:#ef4444; font-weight:800; }
    .lmo-team-cell { display:flex; align-items:center; gap:10px; min-width:180px; }
    .lmo-avatar-stack { display:flex; align-items:center; }
    .lmo-avatar-chip {
      width:34px; height:34px;
      border-radius:50%;
      background:#f1f5f9;
      border:2px solid #fff;
      display:grid;
      place-items:center;
      font-size:.72rem;
      font-weight:800;
      color:#64748b;
      margin-left:-10px;
      box-shadow:0 2px 6px rgba(0,0,0,.06);
      transition:transform .2s ease, margin-left .2s ease;
    }
    .lmo-avatar-chip:first-child { margin-left:0; }
    .lmo-coverage-table tbody tr:hover .lmo-avatar-chip { transform:translateY(-1px); }
    .lmo-avatar-chip.blue { background:linear-gradient(135deg, #dbeafe, #bfdbfe); color:#1d4ed8; }
    .lmo-avatar-chip.green { background:linear-gradient(135deg, #d1fae5, #a7f3d0); color:#059669; }
    .lmo-assign-btn {
      margin-left:auto;
      border:none;
      background:linear-gradient(135deg, #2563eb, #1d4ed8);
      color:#fff;
      font-weight:700;
      border-radius:10px;
      padding:8px 14px;
      font-size:.82rem;
      white-space:nowrap;
      box-shadow:0 4px 12px rgba(37,99,235,.25);
      transition:transform .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .lmo-assign-btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(37,99,235,.35); }
    .lmo-assign-btn:active { transform:translateY(0); }
    @media (max-width: 1200px) {
      .lmo-card-grid, .lmo-layer-grid { grid-template-columns:repeat(2, minmax(0,1fr)); }
    }
    @media (max-width: 992px) {
      .lmo-card-grid, .lmo-layer-grid { grid-template-columns:1fr; }
    }
    /* Modal Detail (Inspeksi Hazard + OAK + Observasi): di atas backdrop */
    #coverageDetailAllModal.modal { z-index: 1060 !important; }
    #coverageDetailAllModal .modal-content { background: #fff; position: relative; z-index: 1; }
    #heatmapDayDetailModal.modal { z-index: 1060 !important; }
    #heatmapDayDetailModal .modal-content { background: #fff; position: relative; z-index: 1; }
    #coverageAreaAllModal.modal { z-index: 1060 !important; }
    #coverageAreaAllModal .modal-content { background: #fff; position: relative; z-index: 1; }
    #coverageDopModal.modal { z-index: 1060 !important; }
    #coverageDopModal .modal-content { background: #fff; position: relative; z-index: 1; }
    body.modal-open .modal-backdrop { z-index: 1055 !important; }
    /* Saat modal coverage iframe (Area All / DOP) terbuka: naikkan z-index agar di atas overlay/sidebar */
    body.lmo-coverage-iframe-open .modal-backdrop { z-index: 9998 !important; }
    body.lmo-coverage-iframe-open #coverageAreaAllModal.modal,
    body.lmo-coverage-iframe-open #coverageDopModal.modal,
    body.lmo-coverage-iframe-open #coverageIkkModal.modal { z-index: 9999 !important; }
    #coverageDopModal.modal .modal-dialog,
    #coverageDopModal.modal .modal-content { pointer-events: auto !important; }
    /* Tour guide button in header */
    .btn-dashboard-tour { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 10px; font-size: 12px; font-weight: 500; background: linear-gradient(135deg, #1b84ff 0%, #0d6efd 100%); color: #fff; border: none; cursor: pointer; box-shadow: 0 2px 8px rgba(27,132,255,.3); transition: transform .15s ease, box-shadow .2s ease; }
    .btn-dashboard-tour:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(27,132,255,.4); }

    /* Modal coverage (Area All / DOP): header di atas iframe agar tombol tutup tetap bisa diklik */
    .lmo-coverage-modal-header { position: relative; z-index: 2; background: var(--kt-card) !important; }
    .lmo-coverage-modal-body { min-height: 70vh; }
    .btn-dashboard-tour i { font-size: 1rem; }
</style>
<link rel="stylesheet" href="https://unpkg.com/@sjmc11/tourguidejs/dist/css/tour.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
@endsection

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            poppins: ['Poppins', 'sans-serif']
          },
          colors: {
            kt: {
              body: '#f5f8fa',
              card: '#ffffff',
              border: '#eef0f4',
              text: '#071437',
              muted: '#99a1b7',
              primary: '#1b84ff',
              'primary-light': '#e9f3ff',
              success: '#17c653',
              'success-light': '#e8fff3',
              warning: '#f6c000',
              'warning-light': '#fff8dd',
              danger: '#f8285a',
              'danger-light': '#ffeef3',
              dark: '#0f172a',
              sidebar: '#111827'
            }
          },
          boxShadow: {
            'kt-sm': '0 2px 8px rgba(15, 23, 42, 0.04)',
            'kt-md': '0 6px 24px rgba(15, 23, 42, 0.08)',
          },
          borderRadius: {
            'kt': '0.75rem',
            'kt-lg': '1rem'
          }
        }
      }
    }
  </script>
  <script src="https://unpkg.com/@sjmc11/tourguidejs/dist/tour.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<div class="min-h-screen flex">
    

    <!-- CONTENT -->
    <div class="flex-1 min-w-0">
      <!-- TOPBAR -->
     

      <main class="px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- LMO Coverage Performance Overview (Bootstrap-style) -->
        <div class="lmo-coverage-root">
          <!-- Page header -->
          <section class="lmo-page-head" id="dashboardTourIntro">
            <div class="lmo-breadcrumb-row">
              <span>Critical Area Monitoring</span>
              <span>›</span>
              <span class="current">Full-Width Performance Overview</span>
            </div>

            <div class="lmo-page-head-main">
              <div class="lmo-page-title">
                <h2>Coverage Performance</h2>
                <p>Expanded operational monitoring for all site locations with detailed layer analysis.</p>
              </div>

              <div class="lmo-head-actions">
                <button type="button" class="btn-dashboard-tour" id="dashboardTourStartBtn" title="Mulai panduan dashboard">
                  <i class="bi bi-info-circle-fill"></i> Panduan Dashboard
                </button>
                <div class="lmo-live-pill">Live Operational</div>

                <div class="lmo-segmented" role="tablist" aria-label="Period filter">
                  <button type="button">Day</button>
                  <button type="button" class="active">Week</button>
                  <button type="button">Month</button>
                </div>

                <button class="lmo-btn-export" type="button">
                  <i class="bi bi-download"></i>
                  Export Report
                </button>
              </div>
            </div>
          </section>

          <!-- KPI cards -->
          <section class="lmo-card-grid">
            <div class="lmo-kpi-card lmo-kpi-card-clickable" id="coverageAreaAllKpiCard" role="button" tabindex="0" title="Klik untuk lihat Dashboard Coverage Area All">
              <div class="lmo-kpi-top">
                <div class="lmo-kpi-icon lmo-icon-blue"><i class="bi bi-clipboard-data-fill"></i></div>
                <div class="lmo-delta up"><i class="bi bi-arrow-up-right"></i> </div>
              </div>
              <div class="lmo-kpi-title">Coverage Area All</div>
              <div class="lmo-kpi-value">100%</div>
              <div class="lmo-kpi-sub">Coverage Area All</div>
            </div>

            <div class="lmo-kpi-card lmo-kpi-card-clickable" id="coverageDopKpiCard" role="button" tabindex="0" title="Klik untuk lihat Dashboard Coverage Activity DOP">
              <div class="lmo-kpi-top">
                <div class="lmo-kpi-icon lmo-icon-blue"><i class="bi bi-speedometer2"></i></div>
                <div class="lmo-delta up"><i class="bi bi-arrow-up-right"></i> </div>
              </div>
              <div class="lmo-kpi-title">Coverage Activity DOP</div>
              <div class="lmo-kpi-value">100%</div>
              <div class="lmo-kpi-sub">Coverage Activity DOP</div>
            </div>

            <div class="lmo-kpi-card lmo-kpi-card-clickable" id="coverageIkkKpiCard" role="button" tabindex="0" title="Klik untuk lihat Dashboard Coverage Activity BeIKK">
              <div class="lmo-kpi-top">
                <div class="lmo-kpi-icon lmo-icon-green"><i class="bi bi-check-circle-fill"></i></div>
                <div class="lmo-delta down"><i class="bi bi-arrow-down-right"></i></div>
              </div>
              <div class="lmo-kpi-title">Coverage Activity BeIKK</div>
              <div class="lmo-kpi-value">100%</div>
              <div class="lmo-kpi-sub">Coverage Activity BeIKK</div>
            </div>

            <div class="lmo-kpi-card" id="dashboardTourKpiNonKritis">
              <div class="lmo-kpi-top">
                <div class="lmo-kpi-icon lmo-icon-red"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="lmo-delta down" style="color:#f04438;"><i class="bi bi-arrow-up-right"></i> </div>
              </div>
              <div class="lmo-kpi-title">Coverage Area Non Kritis</div>
              <div class="lmo-kpi-value">100%</div>
              <div class="lmo-kpi-sub">Coverage Area Non Kritis</div>
            </div>
          </section>

          <!-- Layer summary -->
          <!-- <section class="lmo-layer-grid">
            <div class="lmo-layer-card">
              <div class="lmo-layer-row">
                <div class="lmo-layer-name">L1 Base Layer</div>
                <div class="lmo-layer-value">100%</div>
              </div>
              <div class="lmo-progress-thin"><span style="width:100%; background:#22b573;"></span></div>
            </div>

            <div class="lmo-layer-card">
              <div class="lmo-layer-row">
                <div class="lmo-layer-name">L1-L4 Range</div>
                <div class="lmo-layer-value">82.5%</div>
              </div>
              <div class="lmo-progress-thin"><span style="width:82.5%; background:#2563eb;"></span></div>
            </div>

            <div class="lmo-layer-card">
              <div class="lmo-layer-row">
                <div class="lmo-layer-name">MK-BC Layer Sync</div>
                <div class="lmo-layer-value" style="color:#f59e0b;">64.0%</div>
              </div>
              <div class="lmo-progress-thin"><span style="width:64%; background:#f59e0b;"></span></div>
            </div>

            <div class="lmo-layer-card">
              <div class="lmo-layer-row">
                <div class="lmo-layer-name">Full Ecosystem</div>
                <div class="lmo-layer-value">48.2%</div>
              </div>
              <div class="lmo-progress-thin"><span style="width:48.2%; background:#ef4444;"></span></div>
            </div>
          </section> -->

         
        </div>

        <section class="kt-card animate-in heatmap-calendar-section" id="dashboardTourHeatmap" style="animation-delay:.05s">
          <div class="kt-card-header heatmap-header">
            <div class="flex-shrink-0">
              <div class="kt-card-title">Performance Heatmap</div>
              <div class="kt-card-subtitle">Actual / Plan — Actual SAP</div>
            </div>

            <div class="heatmap-toolbar">
              <div class="month-control" role="group" aria-label="Pilih bulan">
                <button class="btn-nav" type="button" id="heatmapPrevMonth" aria-label="Bulan sebelumnya">
                  <i class="bi bi-chevron-left" style="font-size:1.25rem"></i>
                </button>
                <div class="label" id="heatmapMonthLabel">—</div>
                <button class="btn-nav" type="button" id="heatmapNextMonth" aria-label="Bulan berikutnya">
                  <i class="bi bi-chevron-right" style="font-size:1.25rem"></i>
                </button>
              </div>
              <div class="flex items-center gap-1 p-1 bg-slate-50 border border-kt-border rounded-xl flex-wrap" data-btn-group="heatmap-site">
                <button type="button" onclick="filterHeatmapSite(this,'all')" class="btn-kt active text-[11px] px-3 py-2">Semua</button>
                <button type="button" onclick="filterHeatmapSite(this,'BMO 1')" class="btn-kt text-[11px] px-3 py-2">BMO 1</button>
                <button type="button" onclick="filterHeatmapSite(this,'BMO 2')" class="btn-kt text-[11px] px-3 py-2">BMO 2</button>
                <button type="button" onclick="filterHeatmapSite(this,'BMO 3')" class="btn-kt text-[11px] px-3 py-2">BMO 3</button>
                <button type="button" onclick="filterHeatmapSite(this,'SMO')" class="btn-kt text-[11px] px-3 py-2">SMO</button>
                <button type="button" onclick="filterHeatmapSite(this,'LMO')" class="btn-kt text-[11px] px-3 py-2">LMO</button>
                <button type="button" onclick="filterHeatmapSite(this,'GMO')" class="btn-kt text-[11px] px-3 py-2">GMO</button>
                <button type="button" onclick="filterHeatmapSite(this,'Marine')" class="btn-kt text-[11px] px-3 py-2">Marine</button>
                <button type="button" onclick="filterHeatmapSite(this,'HO')" class="btn-kt text-[11px] px-3 py-2">HO</button>
                <button type="button" onclick="filterHeatmapSite(this,'EXPLORASI')" class="btn-kt text-[11px] px-3 py-2">EXPLORASI</button>
              </div>
              <button type="button" id="heatmapRefreshBtn" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" title="Refresh kalender &amp; data">
                <i class="bi bi-arrow-clockwise" id="heatmapRefreshIcon"></i>
                <span>Refresh</span>
              </button>
            </div>
          </div>

          <div class="lmo-perf">
          <section class="card-panel calendar-card mb-4">
            <div class="calendar-scroll">
              <div class="calendar-head">
                <div>Min</div>
                <div>Sen</div>
                <div>Sel</div>
                <div>Rab</div>
                <div>Kam</div>
                <div>Jum</div>
                <div>Sab</div>
              </div>
              <div class="calendar-grid" id="heatmapCalendarGrid">
                <!-- Diisi oleh JS berdasarkan bulan/tahun yang dipilih -->
              </div>
            </div>
          </section>
          </div>

         
        </section>

        {{-- Modal: Detail per hari heatmap (per karyawan + lokasi: Inspeksi, OAK, Observasi, Coaching) --}}
        <div class="modal fade" id="heatmapDayDetailModal" tabindex="-1" aria-labelledby="heatmapDayDetailModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header border-kt-border">
                <h5 class="modal-title" id="heatmapDayDetailModalLabel"><i class="bi bi-calendar-day me-2"></i>Detail Hari</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
              </div>
              <div class="modal-body">
                <div id="heatmapDayDetailMeta" class="mb-3 text-sm text-kt-muted"></div>
                <div id="heatmapDayDetailLoading" class="text-center py-4 d-none">
                  <div class="spinner-border spinner-border-sm me-2" role="status"></div> Memuat...
                </div>
                <div id="heatmapDayDetailError" class="alert alert-warning py-2 d-none"></div>
                <div id="heatmapDayDetailWrap" class="overflow-auto border border-kt-border rounded" style="max-height: 60vh;">
                  <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top">
                      <tr>
                        <th class="text-nowrap">Karyawan</th>
                        <th class="text-nowrap">Lokasi</th>
                        <th class="text-nowrap">Detail Lokasi</th>
                        <th class="text-center">Inspeksi</th>
                        <th class="text-center">OAK</th>
                        <th class="text-center">Observasi</th>
                        <th class="text-center">Coaching</th>
                      </tr>
                    </thead>
                    <tbody id="heatmapDayDetailTbody"></tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer border-kt-border">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>

   

        <section class="animate-in" id="dashboardTourCoverageByLocation" style="animation-delay:.1s">
          <div class="kt-card w-full lmo-coverage-card">
            <div class="kt-card-header lmo-coverage-header flex-col items-start gap-4 lg:flex-row lg:items-center lg:justify-between">
              <div class="flex items-center gap-3">
                <div class="lmo-coverage-icon-wrap">
                  <i class="bi bi-pin-map-fill"></i>
                </div>
                <div>
                  <div class="kt-card-title">Coverage by Location</div>
                  <div class="kt-card-subtitle">Data dari roster_plannings — Completion % per hari (ada SAP, OAK, Observasi, atau Coaching di lokasi dari siapapun; tidak match nama). Tanggal: <strong>{{ \Carbon\Carbon::parse($filterDate ?? now())->format('d/m/Y') }}</strong></div>
                </div>
              </div>
              <div class="flex flex-nowrap items-center gap-3 w-full min-w-0">
                <form method="GET" action="{{ route('sistem-roster.dashboard.index') }}" class="flex items-center shrink-0 rounded-xl border border-kt-border bg-white shadow-sm overflow-hidden" id="dashboardDateFilterForm">
                  <span class="pl-3 pr-1 py-2.5 bg-slate-50 border-r border-kt-border text-[11px] font-semibold text-kt-muted uppercase tracking-wide">Tanggal</span>
                  <input type="date" name="date" id="filterDate" value="{{ $filterDate ?? now()->format('Y-m-d') }}" class="border-0 px-3 py-2 text-sm bg-transparent w-[148px] focus:ring-0 focus:outline-none" title="Pilih tanggal jadwal planning">
                  <button type="submit" class="px-4 py-2.5 bg-kt-primary text-white text-xs font-medium hover:opacity-90 transition-opacity shrink-0">Lihat</button>
                  <a href="{{ route('sistem-roster.dashboard.index') }}" class="px-3 py-2.5 text-kt-muted hover:bg-slate-50 text-xs font-medium transition-colors shrink-0" title="Reset ke hari ini">Hari ini</a>
                </form>
                <div class="flex items-center shrink-0 rounded-xl border border-kt-border bg-white shadow-sm overflow-hidden">
                  <label for="filterSiteSelect" class="pl-3 pr-1 py-2.5 bg-slate-50 border-r border-kt-border text-[11px] font-semibold text-kt-muted uppercase tracking-wide">Site</label>
                  <select id="filterSiteSelect" class="border-0 px-3 py-2 text-sm bg-transparent w-[140px] focus:ring-0 focus:outline-none cursor-pointer" onchange="filterSiteBySelect(this)">
                    <option value="all">Semua Site</option>
                    <option value="BMO 1">BMO 1</option>
                    <option value="BMO 2">BMO 2</option>
                    <option value="BMO 3">BMO 3</option>
                    <option value="SMO">SMO</option>
                    <option value="LMO">LMO</option>
                    <option value="GMO">GMO</option>
                    <option value="Marine">Marine</option>
                    <option value="HO">HO</option>
                    <option value="EXPLORASI">EXPLORASI</option>
                  </select>
                </div>
                <div class="flex items-center gap-1 p-1.5 bg-slate-50/80 border border-kt-border rounded-xl flex-1 min-w-0 justify-end" data-btn-group="coverage-tab">
                  <button type="button" onclick="switchCoverageTab(this, 'all')" class="btn-kt active text-[11px] px-3 py-2 rounded-lg shrink-0" data-tab="all">All Location</button>
                  <button type="button" onclick="switchCoverageTab(this, 'ikk')" class="btn-kt text-[11px] px-3 py-2 rounded-lg shrink-0" data-tab="ikk">By IKK</button>
                  <button type="button" onclick="switchCoverageTab(this, 'dop')" class="btn-kt text-[11px] px-3 py-2 rounded-lg shrink-0" data-tab="dop">By DOP</button>
                  <button type="button" onclick="switchCoverageTab(this, 'nonkritis')" class="btn-kt text-[11px] px-3 py-2 rounded-lg shrink-0" data-tab="nonkritis">Non Kritis</button>
                </div>
              </div>
            </div>

            @php
              $coverageTabVars = [
                'all' => $coverageLocations ?? [],
                'ikk' => $coverageByIkk ?? [],
                'dop' => $coverageByDop ?? [],
                'nonkritis' => $coverageNonKritis ?? [],
              ];
            @endphp
            @foreach($coverageTabVars as $tabKey => $coverageList)
              <div class="coverage-tab-pane lmo-table-scroll {{ $loop->first ? '' : 'd-none' }}" id="coverageTab-{{ $tabKey }}" data-coverage-tab="{{ $tabKey }}">
                <table class="lmo-coverage-table">
                  <thead>
                    <tr>
                      <th>Area Location</th>
                      <th>L1</th>
                      <th>L2</th>
                      <th>L3</th>
                      <th>L4</th>
                      <th>MK-BC</th>
                      <th>% Coverage</th>
                      <th>Status</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody class="coverageTableBody">
                    @forelse($coverageList as $loc)
                      @php
                        $dotClass = $loc->pct >= 80 ? 'lmo-dot-green' : ($loc->pct > 0 ? 'lmo-dot-orange' : 'lmo-dot-red');
                        $barStyle = $loc->pct >= 80 ? 'width:'.$loc->pct.'%; background:linear-gradient(90deg, #22b573, #16a34a);' : ($loc->pct > 0 ? 'width:'.$loc->pct.'%; background:linear-gradient(90deg, #f59e0b, #d97706);' : 'width:'.max(5, $loc->pct).'%; background:linear-gradient(90deg, #ef4444, #dc2626);');
                        $valClass = $loc->pct >= 80 ? '' : ($loc->pct > 0 ? '' : 'lmo-coverage-val--danger');
                        $pillClass = $loc->pct >= 80 ? 'lmo-pill-optimal' : ($loc->pct > 0 ? 'lmo-pill-incomplete' : 'lmo-pill-critical');
                        $pillLabel = $loc->pct >= 80 ? 'Sudah Tercover' : ($loc->pct > 0 ? 'Incomplete' : 'Belum Tercover');
                      @endphp
                      <tr data-site="{{ $loc->site ?? '' }}">
                        <td>
                          <div class="lmo-area-cell">
                            <span class="lmo-status-dot {{ $dotClass }}"></span>
                            <div>
                              <div class="lmo-area-name">{{ $loc->lokasi ?: '—' }}</div>
                              <div class="lmo-area-meta">{{ $loc->detail_lokasi ?: '—' }}</div>
                            </div>
                          </div>
                        </td>
                        <td><div class="lmo-empty-badge">•</div></td>
                        <td><div class="lmo-empty-badge">•</div></td>
                        <td><div class="lmo-empty-badge">•</div></td>
                        <td><div class="lmo-empty-badge">•</div></td>
                        <td><div class="lmo-empty-badge">•</div></td>
                        <td>
                          <div class="lmo-coverage-cell">
                            <div class="lmo-coverage-bar"><span style="{{ $barStyle }}"></span></div>
                            <div class="lmo-coverage-val {{ $valClass }}">{{ $loc->pct }}%</div>
                          </div>
                        </td>
                        <td><span class="lmo-pill {{ $pillClass }}">{{ $pillLabel }}</span></td>
                        <td class="text-center">
                          <button type="button" class="btn-kt btn-kt-sm bg-kt-primary/10 text-kt-primary hover:bg-kt-primary hover:text-white border border-kt-primary/30 text-xs px-3 py-1.5 rounded-lg transition-colors coverage-detail-all-btn" title="Lihat Inspeksi Hazard, OAK & Observasi"
                            data-lokasi="{{ e($loc->lokasi ?? '') }}"
                            data-detail-lokasi="{{ e($loc->detail_lokasi ?? '') }}"
                            data-task-ids="{{ json_encode($loc->task_ids ?? []) }}">
                            <i class="bi bi-eye me-1"></i> Lihat Detail
                          </button>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="9" class="text-center py-8 text-kt-muted text-sm">Belum ada lokasi untuk tab ini.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            @endforeach
          </div>

          {{-- Modal: Satu modal untuk Inspeksi Hazard + OAK + Observasi --}}
          <div class="modal fade" id="coverageDetailAllModal" tabindex="-1" aria-labelledby="coverageDetailAllModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
              <div class="modal-content">
                <div class="modal-header border-kt-border">
                  <h5 class="modal-title" id="coverageDetailAllModalLabel"><i class="bi bi-eye me-2"></i>Detail per Lokasi — Inspeksi Hazard, OAK & Observasi</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <div class="text-[11px] font-semibold text-kt-muted uppercase tracking-wide mb-1">Lokasi</div>
                    <div id="coverageDetailAllModalLokasi" class="font-medium text-kt-text">—</div>
                  </div>
                  <div class="mb-4">
                    <div class="text-[11px] font-semibold text-kt-muted uppercase tracking-wide mb-1">Detail Lokasi</div>
                    <div id="coverageDetailAllModalDetailLokasi" class="text-slate-600">—</div>
                  </div>

                  <div class="mb-4">
                    <div class="text-[11px] font-semibold text-kt-muted uppercase tracking-wide mb-2">Inspeksi Hazard (SAP)</div>
                    <div id="coverageDetailAllSapLoading" class="text-center py-3 text-kt-muted d-none"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Memuat...</div>
                    <div id="coverageDetailAllSapError" class="alert alert-warning py-2 d-none"></div>
                    <div id="coverageDetailAllSapWrap" class="overflow-auto border border-kt-border rounded mb-0" style="max-height: 28vh;">
                      <table class="table table-sm table-hover mb-0">
                        <thead class="table-light sticky-top"><tr><th>ID</th><th>Jenis</th><th>Status</th><th>Pelapor</th><th>Deskripsi</th><th>Kategori/Sub</th><th>Nilai Resiko</th><th>PIC</th><th>Tanggal</th><th>Foto</th></tr></thead>
                        <tbody id="coverageDetailAllSapTbody"></tbody>
                      </table>
                    </div>
                  </div>

                  <div class="mb-4">
                    <div class="text-[11px] font-semibold text-kt-muted uppercase tracking-wide mb-2">OAK</div>
                    <div id="coverageDetailAllOakLoading" class="text-center py-3 text-kt-muted d-none"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Memuat...</div>
                    <div id="coverageDetailAllOakError" class="alert alert-warning py-2 d-none"></div>
                    <div id="coverageDetailAllOakWrap" class="overflow-auto border border-kt-border rounded mb-0" style="max-height: 28vh;">
                      <table class="table table-sm table-hover mb-0">
                        <thead class="table-light sticky-top"><tr><th>ID</th><th>Site</th><th>Tipe</th><th>Shift</th><th>Activity</th><th>Submit By</th><th>Submit Date</th><th>Conclusion</th><th>Tools</th><th>Foto</th></tr></thead>
                        <tbody id="coverageDetailAllOakTbody"></tbody>
                      </table>
                    </div>
                  </div>

                  <div class="mb-0">
                    <div class="text-[11px] font-semibold text-kt-muted uppercase tracking-wide mb-2">Observasi</div>
                    <div id="coverageDetailAllObsLoading" class="text-center py-3 text-kt-muted d-none"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Memuat...</div>
                    <div id="coverageDetailAllObsError" class="alert alert-warning py-2 d-none"></div>
                    <div id="coverageDetailAllObsWrap" class="overflow-auto border border-kt-border rounded mb-0" style="max-height: 28vh;">
                      <table class="table table-sm table-hover mb-0">
                        <thead class="table-light sticky-top"><tr><th>Task ID</th><th>Tanggal</th><th>Pelapor</th><th>Site</th><th>Jenis Kegiatan</th><th>Tools</th><th>Tindakan/Umpan Balik</th><th>Foto</th></tr></thead>
                        <tbody id="coverageDetailAllObsTbody"></tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="modal-footer border-kt-border">
                  <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
              </div>
            </div>
          </div>

          {{-- Modal: Dashboard Coverage Area All (iframe view coverage-all) — dipindah ke body via JS agar backdrop/klik berfungsi --}}
          <div class="modal fade" id="coverageAreaAllModal" tabindex="-1" aria-labelledby="coverageAreaAllModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
              <div class="modal-content">
                <div class="modal-header border-kt-border lmo-coverage-modal-header">
                  <h5 class="modal-title" id="coverageAreaAllModalLabel"><i class="bi bi-clipboard-data-fill me-2"></i>Dashboard Coverage Area All</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-0 bg-light d-flex flex-column" style="min-height: 0;">
                  <iframe id="coverageAreaAllIframe" src="" title="Coverage Area All" class="w-100 border-0 flex-grow-1" style="min-height: 70vh; height: calc(100vh - 60px);"></iframe>
                </div>
              </div>
            </div>
          </div>

          {{-- Modal: Dashboard Coverage Activity DOP (iframe view coverage-dop) — dipindah ke body via JS agar backdrop/klik berfungsi --}}
          <div class="modal fade" id="coverageDopModal" tabindex="-1" aria-labelledby="coverageDopModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
              <div class="modal-content">
                <div class="modal-header border-kt-border lmo-coverage-modal-header">
                  <h5 class="modal-title" id="coverageDopModalLabel"><i class="bi bi-speedometer2 me-2"></i>Dashboard Coverage Activity DOP</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-0 bg-light d-flex flex-column lmo-coverage-modal-body">
                  <iframe id="coverageDopIframe" src="" title="Coverage Activity DOP" class="w-100 border-0 flex-grow-1" style="min-height: 70vh; height: calc(100vh - 120px);"></iframe>
                </div>
              </div>
            </div>
          </div>

          {{-- Modal: Dashboard Coverage Activity BeIKK (iframe view coverage-ikk) --}}
          <div class="modal fade" id="coverageIkkModal" tabindex="-1" aria-labelledby="coverageIkkModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
              <div class="modal-content">
                <div class="modal-header border-kt-border lmo-coverage-modal-header">
                  <h5 class="modal-title" id="coverageIkkModalLabel"><i class="bi bi-check-circle me-2"></i>Dashboard Coverage Activity BeIKK</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-0 bg-light d-flex flex-column lmo-coverage-modal-body">
                  <iframe id="coverageIkkIframe" src="" title="Coverage Activity BeIKK" class="w-100 border-0 flex-grow-1" style="min-height: 70vh; height: calc(100vh - 120px);"></iframe>
                </div>
              </div>
            </div>
          </div>
        </section>

    
        <section class="kt-card animate-in" style="animation-delay:.15s">
          <div class="kt-card-header flex-col items-start gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
              <div class="kt-card-title">Detail Plan Pengecekan</div>
              <div class="kt-card-subtitle">Per lokasi & karyawan — OK = ada SAP, OAK, Observasi, atau Coaching dari karyawan yang di-assign (match nama + lokasi + tanggal); NOT OK = belum ada. Tanggal: <strong>{{ \Carbon\Carbon::parse($filterDate ?? now())->format('d/m/Y') }}</strong></div>
            </div>

            <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-2 sm:items-center">
              <div class="flex items-center gap-1 p-1 bg-slate-50 border border-kt-border rounded-xl" data-btn-group="shift">
                <button onclick="filterShift(this,'all')" class="btn-kt active text-[11px] px-3 py-2">Semua</button>
                <button onclick="filterShift(this,'1')" class="btn-kt text-[11px] px-3 py-2">Shift 1</button>
                <button onclick="filterShift(this,'2')" class="btn-kt text-[11px] px-3 py-2">Shift 2</button>
              </div>

              <div class="flex items-center gap-1 p-1 bg-slate-50 border border-kt-border rounded-xl" data-btn-group="status">
                <button onclick="filterStatus(this,'all')" class="btn-kt active text-[11px] px-3 py-2">All Status</button>
                <button onclick="filterStatus(this,'ok')" class="btn-kt text-[11px] px-3 py-2">OK</button>
                <button onclick="filterStatus(this,'notok')" class="btn-kt text-[11px] px-3 py-2">Not OK</button>
              </div>
            </div>
          </div>

          <div class="kt-card-body p-0">
            <div class="overflow-x-auto">
              <table class="table-kt min-w-[1050px]" id="detailTable">
                <thead>
                  <tr>
                    <th class="text-left">Tanggal</th>
                    <th class="text-left">Shift</th>
                    <th class="text-left">Kategori Area</th>
                    <th class="text-left">Lokasi</th>
                    <th class="text-left">Detail Lokasi</th>
                    <th class="text-left">Aktifitas</th>
                    <th class="text-left">Karyawan</th>
                    <th class="text-left">Task ID</th>
                    <th class="text-left">Detail / Reason</th>
                    <th class="text-left">Jenis SAP</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody id="tableBody">
                  @forelse($detailByLokasi ?? [] as $row)
                    @php
                      $shiftVal = $row->shift_val ?? '';
                      $dataShift = (strpos($shiftVal, '1') !== false) ? '1' : ((strpos($shiftVal, '2') !== false) ? '2' : 'all');
                      $rowDateStr = $row->tanggal ? $row->tanggal->format('Y-m-d') : '';
                    @endphp
                    <tr data-shift="{{ $dataShift }}" data-status="{{ $row->car_status ?? 'notok' }}" data-tanggal="{{ $rowDateStr }}">
                      <td class="font-medium">
                        <div class="text-sm font-semibold">{{ $row->tanggal ? $row->tanggal->format('d/m') : '—' }}</div>
                        <div class="text-[11px] text-kt-muted">{{ $row->tanggal ? $row->tanggal->format('Y') : '—' }}</div>
                      </td>
                      <td>
                        @if($row->shift)
                          <span class="badge-kt badge-neutral">{{ Str::limit($row->shift, 8) }}</span>
                        @else
                          <span class="text-kt-muted">—</span>
                        @endif
                      </td>
                      <td>{{ $row->kategori_area ?? '—' }}</td>
                      <td class="font-medium text-slate-700">{{ Str::limit($row->lokasi ?? '—', 30) }}</td>
                      <td class="text-slate-600">{{ Str::limit($row->detail_lokasi ?? '—', 30) }}</td>
                      <td class="text-slate-600">{{ Str::limit($row->aktivitas ?? '—', 28) }}</td>
                      <td class="font-medium">{{ $row->karyawan_nama ?? '—' }}</td>
                      <td class="text-slate-500">{{ Str::limit($row->car_task_id ?? '—', 20) }}</td>
                      <td class="text-slate-500">{{ Str::limit($row->detail_reason ?? '—', 35) }}</td>
                      <td>
                        @if($row->jenis_sap ?? null)
                          <span class="badge-kt badge-primary">{{ Str::limit($row->jenis_sap, 12) }}</span>
                        @else
                          <span class="text-kt-muted">—</span>
                        @endif
                      </td>
                      <td class="text-center">
                        @if(($row->car_status ?? 'notok') === 'ok')
                          <span class="badge-kt badge-success">OK</span>
                        @else
                          <span class="badge-kt badge-danger">NOT OK</span>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="11" class="text-center py-8 text-kt-muted text-sm">Belum ada plan yang di-assign ke karyawan.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <div class="px-5 py-3 border-t border-kt-border bg-slate-50/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
              <span id="rowCount" class="text-kt-muted">Menampilkan semua record</span>
              <span class="text-kt-muted">Total: <span id="totalRowText" class="font-medium text-kt-text">{{ count($detailByLokasi ?? []) }}</span> entri (per karyawan)</span>
            </div>
          </div>
        </section>

       

        <section class="text-xs text-kt-muted flex flex-col md:flex-row md:items-center md:justify-between gap-2 px-1">
          <div class="font-semibold text-kt-text">Performance Monitor</div>
          <div class="uppercase tracking-[.14em]">Critical Area Inspection — LMO · 2026</div>
          <div>Data updated: 03 Mar 2026, 00:00</div>
        </section>
      </main>
    </div>
</div>


  <script>
    // --- Filter tanggal: submit form saat tanggal berubah (opsional)
    document.getElementById('filterDate')?.addEventListener('change', function() {
      document.getElementById('dashboardDateFilterForm')?.submit();
    });

    // --- Button group helper ---
    function setActiveInGroup(btn){
      const group = btn.closest('[data-btn-group]');
      if (!group) return;
      group.querySelectorAll('.btn-kt').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    }

    // --- Time filter ---
    function setTimeFilter(btn, period) {
      setActiveInGroup(btn);
      // hook untuk future logic (replace data berdasarkan day/week/month)
      console.log('time filter:', period);
    }

    // --- Shift / Status filters ---
    let currentShift = 'all';
    let currentStatus = 'all';

    function filterShift(btn, shift) {
      setActiveInGroup(btn);
      currentShift = shift;
      applyFilters();
    }

    function filterStatus(btn, status) {
      setActiveInGroup(btn);
      currentStatus = status;
      applyFilters();
    }

    function switchCoverageTab(btn, tabKey) {
      setActiveInGroup(btn);
      document.querySelectorAll('.coverage-tab-pane').forEach(pane => {
        const isActive = pane.getAttribute('data-coverage-tab') === tabKey;
        pane.classList.toggle('d-none', !isActive);
      });
      window.currentCoverageTab = tabKey;
      if (typeof filterSiteLast === 'function') filterSiteLast();
    }

    function applySiteFilter() {
      const site = (document.getElementById('filterSiteSelect') && document.getElementById('filterSiteSelect').value) || window.coverageFilterSite || 'all';
      window.coverageFilterSite = site;
      const activePane = document.querySelector('.coverage-tab-pane:not(.d-none)');
      const container = activePane ? activePane.querySelector('.coverageTableBody') : null;
      const rows = container ? Array.from(container.querySelectorAll('tr[data-site]')) : [];
      rows.forEach(row => {
        const rowSite = (row.getAttribute('data-site') || '').trim();
        row.style.display = (site === 'all' || rowSite === site) ? '' : 'none';
      });
    }
    function filterSiteBySelect(selectEl) {
      if (!selectEl) return;
      window.coverageFilterSite = selectEl.value;
      applySiteFilter();
    }
    function filterSiteLast() {
      const sel = document.getElementById('filterSiteSelect');
      if (sel) window.coverageFilterSite = sel.value;
      applySiteFilter();
    }

    var sapDetailUrl = @json(route('sistem-roster.dashboard.sap-detail'));
    var oakDetailUrl = @json(route('sistem-roster.dashboard.oak-detail'));
    var observasiDetailUrl = @json(route('sistem-roster.dashboard.observasi-detail'));

    function getDashboardFilterDate() {
      var el = document.getElementById('filterDate');
      return (el && el.value) ? el.value : '';
    }

    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.coverage-detail-all-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          var lokasi = this.getAttribute('data-lokasi') || '';
          var detailLokasi = this.getAttribute('data-detail-lokasi') || '';
          var taskIdsJson = this.getAttribute('data-task-ids') || '[]';
          var taskIds = [];
          try { taskIds = JSON.parse(taskIdsJson); } catch (e) {}
          var date = getDashboardFilterDate();

          document.getElementById('coverageDetailAllModalLokasi').textContent = lokasi || '—';
          document.getElementById('coverageDetailAllModalDetailLokasi').textContent = detailLokasi || '—';

          function resetSection(loadingId, errorId, wrapId, tbodyId) {
            var loadingEl = document.getElementById(loadingId);
            var errorEl = document.getElementById(errorId);
            var wrapEl = document.getElementById(wrapId);
            var tbodyEl = document.getElementById(tbodyId);
            if (loadingEl) { loadingEl.classList.add('d-none'); }
            if (errorEl) { errorEl.classList.add('d-none'); errorEl.textContent = ''; }
            if (wrapEl) wrapEl.classList.add('d-none');
            if (tbodyEl) tbodyEl.innerHTML = '';
            return { loadingEl: loadingEl, errorEl: errorEl, wrapEl: wrapEl, tbodyEl: tbodyEl };
          }
          var sap = resetSection('coverageDetailAllSapLoading', 'coverageDetailAllSapError', 'coverageDetailAllSapWrap', 'coverageDetailAllSapTbody');
          var oak = resetSection('coverageDetailAllOakLoading', 'coverageDetailAllOakError', 'coverageDetailAllOakWrap', 'coverageDetailAllOakTbody');
          var obs = resetSection('coverageDetailAllObsLoading', 'coverageDetailAllObsError', 'coverageDetailAllObsWrap', 'coverageDetailAllObsTbody');
          sap.loadingEl.classList.remove('d-none');
          oak.loadingEl.classList.remove('d-none');
          obs.loadingEl.classList.remove('d-none');

          var modalEl = document.getElementById('coverageDetailAllModal');
          if (modalEl.parentNode !== document.body) document.body.appendChild(modalEl);
          if (typeof bootstrap !== 'undefined' && bootstrap.Modal) bootstrap.Modal.getOrCreateInstance(modalEl).show();
          else { modalEl.classList.add('show'); modalEl.style.display = 'block'; document.body.classList.add('modal-open'); }

          function renderSap(data) {
            sap.loadingEl.classList.add('d-none');
            if (!data.length) { sap.errorEl.textContent = 'Tidak ada data inspeksi hazard.'; sap.errorEl.classList.remove('d-none'); return; }
            sap.errorEl.classList.add('d-none');
            data.forEach(function(row) {
              var tr = document.createElement('tr');
              var desc = (row.deskripsi || '').toString().substring(0, 80);
              if ((row.deskripsi || '').length > 80) desc += '…';
              var kategori = (row.nama_kategori || '') + (row.subketidaksesuaian ? ' / ' + row.subketidaksesuaian : '');
              var pic = (row.nama_pic || '') + (row.departemen_pic ? ' — ' + row.departemen_pic : '');
              var tanggal = row.tanggal_pembuatan || row.bedraft_date || '—';
              var foto = row.url_photo ? '<a href="' + (row.url_photo || '').replace(/"/g, '&quot;') + '" target="_blank" rel="noopener" class="text-kt-primary small">Link</a>' : '—';
              tr.innerHTML = '<td class="text-nowrap"><code>' + (row.id || '').toString().replace(/</g, '&lt;') + '</code></td><td class="text-nowrap">' + (row.jenis_laporan || '—').toString().replace(/</g, '&lt;') + '</td><td class="text-nowrap">' + (row.status || '—').toString().replace(/</g, '&lt;') + '</td><td class="text-nowrap">' + (row.nama_pelapor || '—').toString().replace(/</g, '&lt;') + '</td><td class="small">' + desc.replace(/</g, '&lt;') + '</td><td class="small">' + (kategori || '—').toString().replace(/</g, '&lt;') + '</td><td class="text-nowrap">' + (row.nilai_resiko || '—').toString().replace(/</g, '&lt;') + '</td><td class="small">' + (pic || '—').toString().replace(/</g, '&lt;') + '</td><td class="text-nowrap small">' + (tanggal || '—').toString().replace(/</g, '&lt;') + '</td><td class="text-nowrap">' + foto + '</td>';
              sap.tbodyEl.appendChild(tr);
            });
            sap.wrapEl.classList.remove('d-none');
          }
          function renderOak(data) {
            oak.loadingEl.classList.add('d-none');
            if (!data.length) { oak.errorEl.textContent = 'Tidak ada data OAK.'; oak.errorEl.classList.remove('d-none'); return; }
            oak.errorEl.classList.add('d-none');
            data.forEach(function(row) {
              var tr = document.createElement('tr');
              var foto = row.url_photo ? '<a href="' + (row.url_photo || '').replace(/"/g, '&quot;') + '" target="_blank" rel="noopener" class="text-kt-primary small">Link</a>' : '—';
              tr.innerHTML = '<td class="text-nowrap"><code>' + (row.id || '').toString().replace(/</g, '&lt;') + '</code></td><td class="text-nowrap">' + (row.site || '—').toString().replace(/</g, '&lt;') + '</td><td class="text-nowrap">' + (row.tipe || '—').toString().replace(/</g, '&lt;') + '</td><td class="text-nowrap">' + (row.shift || '—').toString().replace(/</g, '&lt;') + '</td><td class="small">' + (row.activity || '—').toString().replace(/</g, '&lt;') + '</td><td class="text-nowrap">' + (row.submit_by || '—').toString().replace(/</g, '&lt;') + '</td><td class="text-nowrap small">' + (row.submit_date || '—').toString().replace(/</g, '&lt;') + '</td><td class="small">' + (row.conclusion || '—').toString().replace(/</g, '&lt;') + '</td><td class="small">' + (row.tools_observasi || '—').toString().replace(/</g, '&lt;') + '</td><td class="text-nowrap">' + foto + '</td>';
              oak.tbodyEl.appendChild(tr);
            });
            oak.wrapEl.classList.remove('d-none');
          }
          function renderObs(data) {
            obs.loadingEl.classList.add('d-none');
            if (!data.length) { obs.errorEl.textContent = 'Tidak ada data Observasi.'; obs.errorEl.classList.remove('d-none'); return; }
            obs.errorEl.classList.add('d-none');
            data.forEach(function(row) {
              var tr = document.createElement('tr');
              var tindakan = (row.tindakan_perbaikan || '') + (row.umpan_balik ? ' / ' + row.umpan_balik : '');
              var foto = row.url_photo ? '<a href="' + (row.url_photo || '').replace(/"/g, '&quot;') + '" target="_blank" rel="noopener" class="text-kt-primary small">Link</a>' : '—';
              tr.innerHTML = '<td class="text-nowrap"><code>' + (row.task_id || '').toString().replace(/</g, '&lt;') + '</code></td><td class="text-nowrap small">' + (row.report_datetime || '—').toString().replace(/</g, '&lt;') + '</td><td class="text-nowrap">' + (row.nama_pelapor || '—').toString().replace(/</g, '&lt;') + '</td><td class="text-nowrap">' + (row.site || '—').toString().replace(/</g, '&lt;') + '</td><td class="small">' + (row.jenis_kegiatan || '—').toString().replace(/</g, '&lt;') + '</td><td class="small">' + (row.tools_observasi || '—').toString().replace(/</g, '&lt;') + '</td><td class="small">' + (tindakan ? (tindakan.length > 50 ? tindakan.substring(0, 50) + '…' : tindakan) : '—').replace(/</g, '&lt;') + '</td><td class="text-nowrap">' + foto + '</td>';
              obs.tbodyEl.appendChild(tr);
            });
            obs.wrapEl.classList.remove('d-none');
          }

          var sapPromise = taskIds.length
            ? fetch(sapDetailUrl + '?' + taskIds.map(function(id) { return 'task_ids[]=' + encodeURIComponent(id); }).join('&'), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r) { return r.json(); }).then(function(res) { renderSap(res.data || []); }).catch(function() { sap.loadingEl.classList.add('d-none'); sap.errorEl.textContent = 'Gagal memuat SAP.'; sap.errorEl.classList.remove('d-none'); })
            : Promise.resolve(function() { sap.loadingEl.classList.add('d-none'); sap.errorEl.textContent = 'Tidak ada Task ID untuk lokasi ini.'; sap.errorEl.classList.remove('d-none'); }());
          var oakPromise = fetch(oakDetailUrl + '?' + new URLSearchParams({ lokasi: lokasi, detail_lokasi: detailLokasi, date: date }).toString(), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r) { return r.json(); }).then(function(res) { renderOak(res.data || []); }).catch(function() { oak.loadingEl.classList.add('d-none'); oak.errorEl.textContent = 'Gagal memuat OAK.'; oak.errorEl.classList.remove('d-none'); });
          var obsPromise = fetch(observasiDetailUrl + '?' + new URLSearchParams({ lokasi: lokasi, detail_lokasi: detailLokasi, date: date }).toString(), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(function(r) { return r.json(); }).then(function(res) { renderObs(res.data || []); }).catch(function() { obs.loadingEl.classList.add('d-none'); obs.errorEl.textContent = 'Gagal memuat Observasi.'; obs.errorEl.classList.remove('d-none'); });

          Promise.all([sapPromise, oakPromise, obsPromise]);
        });
      });
    });

    window.heatmapData = @json($heatmapData ?? []);
    window.currentHeatmapSite = 'all';

    function filterHeatmapSite(btn, site) {
      setActiveInGroup(btn);
      window.currentHeatmapSite = site;
      if (typeof window.renderHeatmapCalendar === 'function') window.renderHeatmapCalendar();
    }

    function applyFilters() {
      const rows = Array.from(document.querySelectorAll('#tableBody tr[data-shift]'));
      let visible = 0;

      rows.forEach(row => {
        const shift = row.getAttribute('data-shift');
        const status = row.getAttribute('data-status');
        const shiftOk = currentShift === 'all' || shift === currentShift;
        const statusOk = currentStatus === 'all' || status === currentStatus;

        row.style.display = (shiftOk && statusOk) ? '' : 'none';
        if (shiftOk && statusOk) visible++;
      });

      const total = rows.length;
      document.getElementById('totalRowText').textContent = total;
      document.getElementById('rowCount').textContent =
        (visible === total)
          ? 'Menampilkan semua record'
          : `Menampilkan ${visible} dari ${total} record`;
    }

    // --- Coverage detail: click row to populate panel ---
    function selectLocationCoverage(row) {
      document.querySelectorAll('#coverageLocationTbody .loc-row-clickable').forEach(r => r.classList.remove('active'));
      row.classList.add('active');

      const d = row.dataset;

      const l1 = d.l1 === '1';
      const l2 = d.l2 === '1';
      const l3 = d.l3 === '1';
      const l4 = d.l4 === '1';
      const mkbc = d.mkbc === '1';
      const coveredPoint = Number(d.coveredPoint || 0);
      const totalPoint = Number(d.totalPoint || 5);
      const pct = totalPoint > 0 ? Math.round((coveredPoint / totalPoint) * 100) : 0;
      const fullLayer = d.fullLayer === '1';

      // basic info
      document.getElementById('detailLokasiName').textContent = d.lokasi || '-';
      document.getElementById('detailLokasiArea').textContent = d.area || '-';
      document.getElementById('detailLokasiDesc').textContent = d.detail || '-';

      // layer statuses
      setLayerText('detailL1', l1);
      setLayerText('detailL2', l2);
      setLayerText('detailL3', l3);
      setLayerText('detailL4', l4);

      // coverage progress
      document.getElementById('detailCoveragePct').textContent = pct + '%';
      document.getElementById('detailCoverageBar').style.width = pct + '%';
      document.getElementById('detailCoveredPoint').textContent = `${coveredPoint}/${totalPoint} point`;

      // MK-BC chip
      const mkbcEl = document.getElementById('detailMkbc');
      mkbcEl.innerHTML = mkbc
        ? `<span class="dot bg-green-500"></span> MK-BC Covered`
        : `<span class="dot bg-slate-400"></span> MK-BC Not Covered`;

      // gap / notes / last update
      document.getElementById('detailGap').textContent = d.gap || '-';
      document.getElementById('detailNotes').textContent = d.notes || '-';
      document.getElementById('detailLastUpdate').textContent = d.lastUpdate || '-';

      // status badge
      const badge = document.getElementById('detailCoverageStatusBadge');
      badge.className = 'badge-kt';
      if (fullLayer && mkbc) {
        badge.classList.add('badge-success');
        badge.textContent = 'Full';
      } else if (pct >= 60) {
        badge.classList.add('badge-primary');
        badge.textContent = 'Good';
      } else if (pct > 0) {
        badge.classList.add('badge-warning');
        badge.textContent = 'Partial';
      } else {
        badge.classList.add('badge-danger');
        badge.textContent = 'Low';
      }
    }

    function setLayerText(elId, isCovered) {
      const el = document.getElementById(elId);
      el.textContent = isCovered ? 'Covered' : 'Not Covered';
      el.className = 'mt-1 text-sm font-semibold ' + (isCovered ? 'text-green-600' : 'text-slate-500');
    }

    // --- Heatmap Calendar: actual/plan dari roster_plannings + CAR match, filter by site ---
    (function heatmapCalendar() {
      const MONTH_NAMES_ID = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      const gridEl = document.getElementById('heatmapCalendarGrid');
      const labelEl = document.getElementById('heatmapMonthLabel');
      const btnPrev = document.getElementById('heatmapPrevMonth');
      const btnNext = document.getElementById('heatmapNextMonth');

      if (!gridEl || !labelEl) return;

      let currentYear, currentMonth;
      const today = new Date();

      function getDaysInMonth(y, m) {
        return new Date(y, m + 1, 0).getDate();
      }

      function getFirstDayOfWeek(y, m) {
        return new Date(y, m, 1).getDay();
      }

      function pad(n) {
        return n < 10 ? '0' + n : String(n);
      }

      function getDayData(dayNum) {
        const dateStr = currentYear + '-' + pad(currentMonth + 1) + '-' + pad(dayNum);
        const data = window.heatmapData || [];
        const siteFilter = window.currentHeatmapSite || 'all';
        let planned = 0, actual = 0;
        data.forEach(function (row) {
          if (row.date !== dateStr) return;
          if (siteFilter !== 'all' && row.site !== siteFilter) return;
          planned += row.planned || 0;
          actual += row.actual || 0;
        });
        const pct = planned > 0 ? Math.round((actual / planned) * 100) : 0;
        let state = 'neutral';
        if (planned > 0) {
          if (pct >= 80) state = 'good';
          else if (pct > 0) state = 'warn';
          else state = 'bad';
        }
        return { planned, actual, pct, state };
      }

      function renderCalendar() {
        labelEl.textContent = MONTH_NAMES_ID[currentMonth] + ' ' + currentYear;

        const firstDay = getFirstDayOfWeek(currentYear, currentMonth);
        const daysInMonth = getDaysInMonth(currentYear, currentMonth);
        const isCurrentMonth = (today.getFullYear() === currentYear && today.getMonth() === currentMonth);

        let html = '';

        for (let i = 0; i < firstDay; i++) {
          html += '<div class="day-cell state-neutral"></div>';
        }

        for (let d = 1; d <= daysInMonth; d++) {
          const dateStr = currentYear + '-' + pad(currentMonth + 1) + '-' + pad(d);
          const isToday = isCurrentMonth && d === today.getDate();
          const info = getDayData(d);
          const stateClass = info.state === 'good' ? 'state-good' : (info.state === 'warn' ? 'state-warn' : (info.state === 'bad' ? 'state-bad' : 'state-neutral'));
          const scoreClass = info.state === 'good' ? 'good' : (info.state === 'warn' ? 'warn' : (info.state === 'bad' ? 'bad' : ''));

          if (isToday) {
            html += '<div class="day-cell heatmap-day-cell selected" data-date="' + dateStr + '" role="button" title="Klik untuk detail">';
            html += '<div class="day-num">' + d + '</div>';
            html += '<span class="current-pill">Hari ini</span>';
            html += '<div class="day-center">';
            html += '<div class="score">' + info.actual + ' / ' + info.planned + '</div>';
            html += '<div class="mini-progress"><div class="fill" style="width:' + (info.planned ? info.pct : 0) + '%; background:#2563eb"></div></div>';
            html += '<div class="completion">' + (info.planned ? info.pct + '% Actual' : '—') + '</div>';
            html += '</div></div>';
          } else {
            html += '<div class="day-cell heatmap-day-cell ' + stateClass + '" data-date="' + dateStr + '" role="button" title="Klik untuk detail">';
            html += '<div class="day-num">' + d + '</div>';
            html += '<div class="day-center">';
            html += '<div class="score ' + scoreClass + '">' + info.actual + ' / ' + info.planned + '</div>';
            html += '<div class="mini-progress"><div class="fill ' + scoreClass + '" style="width:' + (info.planned ? info.pct : 0) + '%"></div></div>';
            html += '</div></div>';
          }
        }

        gridEl.innerHTML = html;
      }

      window.renderHeatmapCalendar = renderCalendar;

      function goPrevMonth() {
        currentMonth--;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        renderCalendar();
      }

      function goNextMonth() {
        currentMonth++;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        renderCalendar();
      }

      currentYear = today.getFullYear();
      currentMonth = today.getMonth();
      renderCalendar();

      if (btnPrev) btnPrev.addEventListener('click', goPrevMonth);
      if (btnNext) btnNext.addEventListener('click', goNextMonth);

      // Klik day cell: tampilkan modal detail per karyawan + lokasi (Inspeksi, OAK, Observasi)
      var heatmapDayDetailUrl = @json(route('sistem-roster.dashboard.heatmap-day-detail'));
      gridEl.addEventListener('click', function(e) {
        var cell = e.target.closest('.heatmap-day-cell');
        if (!cell) return;
        var date = cell.getAttribute('data-date');
        if (!date) return;
        var site = window.currentHeatmapSite || 'all';

        var modalEl = document.getElementById('heatmapDayDetailModal');
        var titleEl = document.getElementById('heatmapDayDetailModalLabel');
        var metaEl = document.getElementById('heatmapDayDetailMeta');
        var loadingEl = document.getElementById('heatmapDayDetailLoading');
        var errorEl = document.getElementById('heatmapDayDetailError');
        var wrapEl = document.getElementById('heatmapDayDetailWrap');
        var tbodyEl = document.getElementById('heatmapDayDetailTbody');

        if (titleEl) titleEl.textContent = 'Detail Hari — ' + date + (site !== 'all' ? ' · ' + site : '');
        if (metaEl) metaEl.textContent = 'Per planning (karyawan + lokasi): jumlah Inspeksi Hazard, OAK, Observasi, Coaching yang match.';
        loadingEl.classList.remove('d-none');
        errorEl.classList.add('d-none');
        errorEl.textContent = '';
        wrapEl.classList.add('d-none');
        tbodyEl.innerHTML = '';

        if (modalEl.parentNode !== document.body) document.body.appendChild(modalEl);
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) bootstrap.Modal.getOrCreateInstance(modalEl).show();
        else { modalEl.classList.add('show'); modalEl.style.display = 'block'; document.body.classList.add('modal-open'); }

        var params = new URLSearchParams({ date: date, site: site });
        fetch(heatmapDayDetailUrl + '?' + params.toString(), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
          .then(function(r) { return r.json(); })
          .then(function(res) {
            loadingEl.classList.add('d-none');
            var data = res.data || [];
            if (data.length === 0) {
              errorEl.textContent = 'Tidak ada planning untuk tanggal ini' + (site !== 'all' ? ' di site ' + site : '') + '.';
              errorEl.classList.remove('d-none');
              return;
            }
            errorEl.classList.add('d-none');
            // Group by karyawan_nama untuk rowspan: hanya kolom Karyawan yang di-merge, Lokasi/Detail per baris
            var byName = {};
            data.forEach(function(row) {
              var n = (row.karyawan_nama || '—').toString();
              if (!byName[n]) byName[n] = [];
              byName[n].push(row);
            });
            Object.keys(byName).forEach(function(nama) {
              var list = byName[nama];
              list.forEach(function(row, i) {
                var tr = document.createElement('tr');
                var namaCell = '';
                if (i === 0) {
                  namaCell = '<td class="font-medium align-top" rowspan="' + list.length + '">' + nama.replace(/</g, '&lt;') + '</td>';
                }
                tr.innerHTML = namaCell +
                  '<td class="text-nowrap">' + (row.lokasi || '—').toString().replace(/</g, '&lt;') + '</td>' +
                  '<td class="text-nowrap small">' + (row.detail_lokasi || '—').toString().replace(/</g, '&lt;') + '</td>' +
                  '<td class="text-center">' + (row.count_inspeksi || 0) + '</td>' +
                  '<td class="text-center">' + (row.count_oak || 0) + '</td>' +
                  '<td class="text-center">' + (row.count_observasi || 0) + '</td>' +
                  '<td class="text-center">' + (row.count_coaching || 0) + '</td>';
                tbodyEl.appendChild(tr);
              });
            });
            wrapEl.classList.remove('d-none');
          })
          .catch(function(err) {
            loadingEl.classList.add('d-none');
            errorEl.textContent = 'Gagal memuat: ' + (err.message || 'Unknown error');
            errorEl.classList.remove('d-none');
          });
      });

      // Refresh kalender: reload halaman agar data heatmap (planned/actual) ter-update dari server
      var refreshBtn = document.getElementById('heatmapRefreshBtn');
      var refreshIcon = document.getElementById('heatmapRefreshIcon');
      if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
          if (refreshIcon) refreshIcon.classList.add('spin');
          refreshBtn.disabled = true;
          refreshBtn.querySelector('span').textContent = 'Memuat...';
          window.location.reload();
        });
      }
    })();

    // Modal Coverage Area All: pindah modal ke body, naikkan z-index saat buka agar bisa diklik/tutup
    (function() {
      var coverageAreaAllUrl = '{{ url()->route("sistem-roster.dashboard.coverage-all") }}';
      function initCoverageAreaAllModal() {
        var cardEl = document.getElementById('coverageAreaAllKpiCard');
        var modalEl = document.getElementById('coverageAreaAllModal');
        var iframeEl = document.getElementById('coverageAreaAllIframe');
        if (!cardEl || !modalEl || !iframeEl) return;
        if (modalEl.parentNode !== document.body) document.body.appendChild(modalEl);
        modalEl.addEventListener('shown.bs.modal', function() { document.body.classList.add('lmo-coverage-iframe-open'); });
        modalEl.addEventListener('hidden.bs.modal', function() { document.body.classList.remove('lmo-coverage-iframe-open'); });
        cardEl.addEventListener('click', function() {
          iframeEl.src = coverageAreaAllUrl;
          if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
          } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.setAttribute('aria-modal', 'true');
            modalEl.removeAttribute('aria-hidden');
            document.body.classList.add('modal-open');
            var backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.setAttribute('data-coverage-area-all-backdrop', '1');
            document.body.appendChild(backdrop);
          }
        });
        cardEl.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); cardEl.click(); }
        });
      }
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCoverageAreaAllModal);
      } else {
        initCoverageAreaAllModal();
      }
    })();

    // Modal Coverage Activity DOP: pindah modal ke body, naikkan z-index saat buka agar bisa diklik/tutup
    (function() {
      var coverageDopUrl = '{{ url()->route("sistem-roster.dashboard.coverage-dop") }}';
      function initCoverageDopModal() {
        var cardEl = document.getElementById('coverageDopKpiCard');
        var modalEl = document.getElementById('coverageDopModal');
        var iframeEl = document.getElementById('coverageDopIframe');
        if (!cardEl || !modalEl || !iframeEl) return;
        if (modalEl.parentNode !== document.body) document.body.appendChild(modalEl);
        modalEl.addEventListener('shown.bs.modal', function() { document.body.classList.add('lmo-coverage-iframe-open'); });
        modalEl.addEventListener('hidden.bs.modal', function() { document.body.classList.remove('lmo-coverage-iframe-open'); });
        cardEl.addEventListener('click', function() {
          iframeEl.src = coverageDopUrl;
          if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
          } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.setAttribute('aria-modal', 'true');
            modalEl.removeAttribute('aria-hidden');
            document.body.classList.add('modal-open');
            var backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.setAttribute('data-coverage-dop-backdrop', '1');
            document.body.appendChild(backdrop);
          }
        });
        cardEl.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); cardEl.click(); }
        });
      }
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCoverageDopModal);
      } else {
        initCoverageDopModal();
      }
    })();

    // Modal Coverage Activity BeIKK: sama seperti DOP, iframe load coverage-ikk
    (function() {
      var coverageIkkUrl = '{{ url()->route("sistem-roster.dashboard.coverage-ikk") }}';
      function initCoverageIkkModal() {
        var cardEl = document.getElementById('coverageIkkKpiCard');
        var modalEl = document.getElementById('coverageIkkModal');
        var iframeEl = document.getElementById('coverageIkkIframe');
        if (!cardEl || !modalEl || !iframeEl) return;
        if (modalEl.parentNode !== document.body) document.body.appendChild(modalEl);
        modalEl.addEventListener('shown.bs.modal', function() { document.body.classList.add('lmo-coverage-iframe-open'); });
        modalEl.addEventListener('hidden.bs.modal', function() { document.body.classList.remove('lmo-coverage-iframe-open'); });
        cardEl.addEventListener('click', function() {
          iframeEl.src = coverageIkkUrl;
          if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
          } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.setAttribute('aria-modal', 'true');
            modalEl.removeAttribute('aria-hidden');
            document.body.classList.add('modal-open');
            var backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.setAttribute('data-coverage-ikk-backdrop', '1');
            document.body.appendChild(backdrop);
          }
        });
        cardEl.addEventListener('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); cardEl.click(); }
        });
      }
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCoverageIkkModal);
      } else {
        initCoverageIkkModal();
      }
    })();

    // --- Dashboard Tour Guide (TourGuide JS) ---
    (function initDashboardTour() {
      var startBtn = document.getElementById('dashboardTourStartBtn');
      var TourGuideClient = typeof tourguide !== 'undefined' && tourguide.TourGuideClient;
      if (!TourGuideClient) return;

      function getDashboardTourSteps() {
        var el = function(id) { return document.getElementById(id); };
        return [
          { target: el('dashboardTourIntro'), content: '<strong>Dashboard Coverage Performance</strong><br><br>Ini adalah dashboard utama untuk memantau performa coverage operasional di semua lokasi site. Anda dapat melihat KPI coverage, heatmap kesesuaian plan vs actual, dan detail lokasi yang di-assign.', title: 'Selamat datang' },
          { target: el('coverageAreaAllKpiCard'), content: '<strong>Coverage Area All</strong><br><br>Kartu ini menampilkan persentase coverage area secara keseluruhan. <strong>Klik kartu ini</strong> untuk membuka dashboard detail Coverage Area All.', title: 'Coverage Area All' },
          { target: el('coverageDopKpiCard'), content: '<strong>Coverage Activity DOP</strong><br><br>KPI aktivitas coverage berdasarkan DOP (Daily Operation Plan). Klik untuk melihat detail coverage activity DOP.', title: 'Coverage Activity DOP' },
          { target: el('coverageIkkKpiCard'), content: '<strong>Coverage Activity BeIKK</strong><br><br>KPI coverage aktivitas BeIKK (Indikator Kinerja Kunci). Klik untuk melihat detail coverage activity BeIKK.', title: 'Coverage Activity IKK' },
          { target: el('dashboardTourKpiNonKritis'), content: '<strong>Coverage Area Non Kritis</strong><br><br>Persentase coverage untuk area non kritis. Gunakan untuk memantau lokasi yang tidak termasuk kategori kritis.', title: 'Coverage Area Non Kritis' },
          { target: el('dashboardTourHeatmap'), content: '<strong>Performance Heatmap</strong><br><br>Menampilkan kesesuaian <strong>aktual dan planning</strong> dari assign karyawan untuk setiap aktivitas per hari. Warna menunjukkan tingkat pencapaian (Actual / Plan — Actual SAP). Filter per bulan dan site tersedia di sini.', title: 'Heatmap Kesesuaian' },
          { target: el('dashboardTourCoverageByLocation'), content: '<strong>Coverage by Location</strong><br><br>Daftar <strong>lokasi dari masing-masing yang di-assign</strong>. Persentase coverage per lokasi, status (Sudah Tercover / Incomplete / Belum Tercover), dan tombol "Lihat Detail" untuk Inspeksi Hazard, OAK & Observasi. Filter berdasarkan tanggal, site, dan tab (All Location, By IKK, By DOP, Non Kritis).', title: 'Lokasi per Assign' }
        ].filter(function(s) { return s.target; });
      }

      var tg = new TourGuideClient({
        nextLabel: 'Lanjut',
        prevLabel: 'Kembali',
        finishLabel: 'Selesai',
        showStepProgress: true,
        exitOnEscape: true,
        exitOnClickOutside: true,
        rememberStep: false,
        steps: getDashboardTourSteps()
      });

      function startDashboardTour() {
        tg.setOptions({ steps: getDashboardTourSteps() });
        tg.start();
      }

      if (startBtn) startBtn.addEventListener('click', startDashboardTour);

      // Auto-buka panduan saat pertama kali akses halaman
      window.addEventListener('load', function() {
        setTimeout(startDashboardTour, 600);
      });
    })();

    // --- Animate progress bars on load ---
    window.addEventListener('load', () => {
      document.querySelectorAll('.progress-fill').forEach(bar => {
        const target = bar.style.width || '0%';
        bar.style.width = '0%';
        setTimeout(() => { bar.style.width = target; }, 180);
      });

      applyFilters();

      // Auto select first coverage row
      const firstCoverageRow = document.querySelector('#coverageLocationTbody .loc-row-clickable');
      if (firstCoverageRow) {
        setTimeout(() => selectLocationCoverage(firstCoverageRow), 250);
      }
    });
  </script>
@endsection
