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
      border-radius:18px;
      overflow:hidden;
    }
    .lmo-table-head {
      padding:22px 26px;
      border-bottom:1px solid var(--lmo-line,#e6ebf2);
      display:flex;
      align-items:flex-start;
      justify-content:space-between;
      gap:16px;
      flex-wrap:wrap;
    }
    .lmo-table-head h3 { margin:0; font-size:1.05rem; color:#667085; font-weight:500; }
    .lmo-table-head .big { display:block; color:#101828; font-size:1.55rem; line-height:1.2; font-weight:900; margin-bottom:2px; }
    .lmo-table-head .desc { color:#98a2b3; font-size:.95rem; }
    .lmo-table-actions { display:flex; gap:16px; align-items:center; flex-wrap:wrap; color:#667085; font-weight:500; }
    .lmo-table-actions .link-strong { color:#1d4ed8; font-weight:800; text-decoration:none; }
    .lmo-table-scroll { overflow-x:auto; }
    table.lmo-coverage-table {
      width:100%;
      border-collapse:separate;
      border-spacing:0;
      min-width:1040px;
    }
    .lmo-coverage-table thead th {
      background:#fafbfe;
      color:#667085;
      font-size:.78rem;
      letter-spacing:.06em;
      text-transform:uppercase;
      font-weight:800;
      padding:16px 14px;
      border-bottom:1px solid var(--lmo-line,#e6ebf2);
      white-space:nowrap;
      text-align:left;
    }
    .lmo-coverage-table tbody td {
      padding:18px 14px;
      border-bottom:1px solid #edf1f6;
      vertical-align:middle;
      background:#fff;
    }
    .lmo-coverage-table tbody tr:last-child td { border-bottom:none; }
    .lmo-area-cell { display:flex; align-items:center; gap:12px; min-width:240px; }
    .lmo-status-dot { width:10px; height:10px; border-radius:50%; flex:0 0 auto; box-shadow:0 0 0 4px rgba(0,0,0,.03); }
    .lmo-dot-green { background:#22b573; }
    .lmo-dot-orange { background:#f59e0b; }
    .lmo-dot-red { background:#ef4444; }
    .lmo-area-name { font-weight:900; font-size:1rem; line-height:1.2; margin-bottom:2px; }
    .lmo-area-meta { color:#94a3b8; font-size:.85rem; line-height:1.2; }
    .lmo-area-alert { color:#ef4444; font-weight:800; font-size:.8rem; text-transform:uppercase; margin-top:4px; line-height:1.1; }
    .lmo-level-icons { display:flex; gap:10px; align-items:center; }
    .lmo-check-badge, .lmo-warn-badge, .lmo-x-badge, .lmo-empty-badge {
      width:18px; height:18px;
      border-radius:50%;
      display:grid;
      place-items:center;
      font-size:.72rem;
      font-weight:700;
      line-height:1;
    }
    .lmo-check-badge { background:#22b573; color:#fff; }
    .lmo-warn-badge { background:#f59e0b; color:#fff; }
    .lmo-x-badge { background:#ef4444; color:#fff; }
    .lmo-empty-badge { background:#d8dee8; color:transparent; }
    .lmo-coverage-cell { display:flex; align-items:center; gap:12px; min-width:170px; }
    .lmo-coverage-bar {
      width:120px;
      height:6px;
      border-radius:99px;
      background:#edf1f7;
      overflow:hidden;
    }
    .lmo-coverage-bar > span { height:100%; display:block; border-radius:99px; }
    .lmo-coverage-val { font-weight:900; font-size:.95rem; min-width:40px; }
    .lmo-pill { display:inline-flex; align-items:center; border-radius:8px; padding:4px 10px; font-size:.76rem; font-weight:800; letter-spacing:.02em; text-transform:uppercase; border:1px solid transparent; white-space:nowrap; }
    .lmo-pill-optimal { color:#12a361; background:#e8f9f0; border-color:#b7ebce; }
    .lmo-pill-incomplete { color:#d97706; background:#fff5e5; border-color:#fed7aa; }
    .lmo-pill-critical { color:#ef4444; background:#fff1f2; border-color:#fecdd3; }
    .lmo-last-inspection { min-width:140px; line-height:1.2; }
    .lmo-last-inspection .main { font-weight:700; color:#111827; display:block; }
    .lmo-last-inspection .sub { color:#98a2b3; font-size:.85rem; display:block; margin-top:4px; }
    .lmo-last-inspection .sub.warn { color:#f59e0b; font-weight:700; }
    .lmo-last-inspection .sub.red { color:#ef4444; font-weight:800; }
    .lmo-team-cell { display:flex; align-items:center; gap:8px; min-width:180px; }
    .lmo-avatar-stack { display:flex; align-items:center; }
    .lmo-avatar-chip {
      width:32px; height:32px;
      border-radius:50%;
      background:#eef2f7;
      border:2px solid #fff;
      display:grid;
      place-items:center;
      font-size:.72rem;
      font-weight:800;
      color:#64748b;
      margin-left:-8px;
    }
    .lmo-avatar-chip:first-child { margin-left:0; }
    .lmo-avatar-chip.blue { background:#dbe8ff; color:#2457d6; }
    .lmo-avatar-chip.green { background:#ddf7ea; color:#159a60; }
    .lmo-assign-btn {
      margin-left:auto;
      border:none;
      background:#2563eb;
      color:#fff;
      font-weight:700;
      border-radius:10px;
      padding:7px 12px;
      font-size:.82rem;
      white-space:nowrap;
      box-shadow:0 8px 16px rgba(37,99,235,.18);
    }
    @media (max-width: 1200px) {
      .lmo-card-grid, .lmo-layer-grid { grid-template-columns:repeat(2, minmax(0,1fr)); }
    }
    @media (max-width: 992px) {
      .lmo-card-grid, .lmo-layer-grid { grid-template-columns:1fr; }
    }
</style>
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


<div class="min-h-screen flex">
    

    <!-- CONTENT -->
    <div class="flex-1 min-w-0">
      <!-- TOPBAR -->
     

      <main class="px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- LMO Coverage Performance Overview (Bootstrap-style) -->
        <div class="lmo-coverage-root">
          <!-- Page header -->
          <section class="lmo-page-head">
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
            <div class="lmo-kpi-card">
              <div class="lmo-kpi-top">
                <div class="lmo-kpi-icon lmo-icon-blue"><i class="bi bi-clipboard-data-fill"></i></div>
                <div class="lmo-delta up"><i class="bi bi-arrow-up-right"></i> +12%</div>
              </div>
              <div class="lmo-kpi-title">Total Coverage DOP/IKK </div>
              <div class="lmo-kpi-value">100%</div>
              <div class="lmo-kpi-sub">Current period activity</div>
            </div>

            <div class="lmo-kpi-card">
              <div class="lmo-kpi-top">
                <div class="lmo-kpi-icon lmo-icon-blue"><i class="bi bi-speedometer2"></i></div>
                <div class="lmo-delta up"><i class="bi bi-arrow-up-right"></i> +5.4%</div>
              </div>
              <div class="lmo-kpi-title">Avg Completion</div>
              <div class="lmo-kpi-value">94.2%</div>
              <div class="lmo-kpi-sub">90% Target benchmark</div>
            </div>

            <div class="lmo-kpi-card">
              <div class="lmo-kpi-top">
                <div class="lmo-kpi-icon lmo-icon-green"><i class="bi bi-check-circle-fill"></i></div>
                <div class="lmo-delta down"><i class="bi bi-arrow-down-right"></i> -2%</div>
              </div>
              <div class="lmo-kpi-title">Status OK</div>
              <div class="lmo-kpi-value">1,102</div>
              <div class="lmo-kpi-sub">Compliance verified areas</div>
            </div>

            <div class="lmo-kpi-card">
              <div class="lmo-kpi-top">
                <div class="lmo-kpi-icon lmo-icon-red"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="lmo-delta down" style="color:#f04438;"><i class="bi bi-arrow-up-right"></i> +8%</div>
              </div>
              <div class="lmo-kpi-title">Hazard Ditemukan</div>
              <div class="lmo-kpi-value">42</div>
              <div class="lmo-kpi-sub">Immediate action required</div>
            </div>
          </section>

          <!-- Layer summary -->
          <section class="lmo-layer-grid">
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
          </section>

          <!-- Coverage table -->
         
        </div>

        <!-- HEATMAP -->
        <section class="kt-card animate-in" style="animation-delay:.05s">
          <div class="kt-card-header heatmap-header">
            <div class="flex-shrink-0">
              <div class="kt-card-title">Performance Heatmap</div>
              <div class="kt-card-subtitle">Actual / Plan — Kategori Area / Aktivitas Kritis</div>
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
              <button class="btn-export-heatmap" type="button">
                <i class="bi bi-download"></i>
                <span>Export Report</span>
              </button>
            </div>
          </div>

          <div class="lmo-perf">
          <section class="card-panel calendar-card mb-4">
            <div class="calendar-scroll">
              <div class="calendar-head">
                <div>Minggu</div>
                <div>Senin</div>
                <div>Selasa</div>
                <div>Rabu</div>
                <div>Kamis</div>
                <div>Jumat</div>
                <div>Sabtu</div>
              </div>
              <div class="calendar-grid" id="heatmapCalendarGrid">
                <!-- Diisi oleh JS berdasarkan bulan/tahun yang dipilih -->
              </div>
            </div>
          </section>
          </div>

          

         
        </section>

        <section class="lmo-table-card">
            <div class="lmo-table-head">
              <div>
                <h3><span class="big">Coverage by Location</span></h3>
                <div class="desc">Detailed inspection status and team allocation per area</div>
              </div>

              <div class="lmo-table-actions">
                <span>Sort by: <a href="#" class="link-strong">Status Priority</a></span>
                <span style="color:#cbd5e1;">|</span>
                <a href="#" class="link-strong">View Detailed Map <i class="bi bi-box-arrow-up-right"></i></a>
              </div>
            </div>

            <div class="lmo-table-scroll">
              <table class="lmo-coverage-table">
                <thead>
                  <tr>
                    <th>Area Location</th>
                    <th>L1</th>
                    <th>L2</th>
                    <th>L3</th>
                    <th>L4</th>
                    <th>MK-<br>BC</th>
                    <th>% Coverage</th>
                    <th>Status</th>
                    <th>Last Inspection</th>
                    <th>Assigned Team</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="lmo-area-cell">
                        <span class="lmo-status-dot lmo-dot-green"></span>
                        <div>
                          <div class="lmo-area-name">Pit North Alpha</div>
                          <div class="lmo-area-meta">North Sector • Active</div>
                        </div>
                      </div>
                    </td>
                    <td><div class="lmo-check-badge"><i class="bi bi-check"></i></div></td>
                    <td><div class="lmo-check-badge"><i class="bi bi-check"></i></div></td>
                    <td><div class="lmo-check-badge"><i class="bi bi-check"></i></div></td>
                    <td><div class="lmo-check-badge"><i class="bi bi-check"></i></div></td>
                    <td><div class="lmo-empty-badge">•</div></td>
                    <td>
                      <div class="lmo-coverage-cell">
                        <div class="lmo-coverage-bar"><span style="width:80%; background:#22b573;"></span></div>
                        <div class="lmo-coverage-val">80%</div>
                      </div>
                    </td>
                    <td><span class="lmo-pill lmo-pill-optimal">Optimal</span></td>
                    <td>
                      <div class="lmo-last-inspection">
                        <span class="main">Today, 08:45 AM</span>
                        <span class="sub">Verified by Site AI</span>
                      </div>
                    </td>
                    <td>
                      <div class="lmo-team-cell">
                        <div class="lmo-avatar-stack">
                          <div class="lmo-avatar-chip">JD</div>
                          <div class="lmo-avatar-chip blue">T-A</div>
                          <div class="lmo-avatar-chip green">S1</div>
                        </div>
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div class="lmo-area-cell">
                        <span class="lmo-status-dot lmo-dot-orange"></span>
                        <div>
                          <div class="lmo-area-name">Stockpile Zone 4</div>
                          <div class="lmo-area-meta">Logistics Hub • Warning</div>
                        </div>
                      </div>
                    </td>
                    <td><div class="lmo-check-badge"><i class="bi bi-check"></i></div></td>
                    <td><div class="lmo-check-badge"><i class="bi bi-check"></i></div></td>
                    <td><div class="lmo-warn-badge"><i class="bi bi-exclamation"></i></div></td>
                    <td><div class="lmo-empty-badge">•</div></td>
                    <td><div class="lmo-empty-badge">•</div></td>
                    <td>
                      <div class="lmo-coverage-cell">
                        <div class="lmo-coverage-bar"><span style="width:45%; background:#f59e0b;"></span></div>
                        <div class="lmo-coverage-val">45%</div>
                      </div>
                    </td>
                    <td><span class="lmo-pill lmo-pill-incomplete">Incomplete</span></td>
                    <td>
                      <div class="lmo-last-inspection">
                        <span class="main">Yesterday, 04:20 PM</span>
                        <span class="sub warn">Re-check required</span>
                      </div>
                    </td>
                    <td>
                      <div class="lmo-team-cell">
                        <div class="lmo-avatar-stack">
                          <div class="lmo-avatar-chip">T-B</div>
                        </div>
                        <span style="color:#64748b; font-size:.9rem;">Logistics Alpha</span>
                      </div>
                    </td>
                  </tr>

                  <tr>
                    <td>
                      <div class="lmo-area-cell">
                        <span class="lmo-status-dot lmo-dot-red"></span>
                        <div>
                          <div class="lmo-area-name">Hauling Road KM 12</div>
                          <div class="lmo-area-alert">Critical Gap Detected</div>
                        </div>
                      </div>
                    </td>
                    <td><div class="lmo-check-badge"><i class="bi bi-check"></i></div></td>
                    <td><div class="lmo-x-badge"><i class="bi bi-x"></i></div></td>
                    <td><div class="lmo-empty-badge">•</div></td>
                    <td><div class="lmo-empty-badge">•</div></td>
                    <td><div class="lmo-empty-badge">•</div></td>
                    <td>
                      <div class="lmo-coverage-cell">
                        <div class="lmo-coverage-bar"><span style="width:20%; background:#ef4444;"></span></div>
                        <div class="lmo-coverage-val" style="color:#ef4444;">20%</div>
                      </div>
                    </td>
                    <td><span class="lmo-pill lmo-pill-critical">Critical Gap</span></td>
                    <td>
                      <div class="lmo-last-inspection">
                        <span class="main">14 Hours Overdue</span>
                        <span class="sub red">Missed L2 Window</span>
                      </div>
                    </td>
                    <td>
                      <div class="lmo-team-cell">
                        <div class="lmo-avatar-stack">
                          <div class="lmo-avatar-chip">R1</div>
                          <div class="lmo-avatar-chip blue">QH</div>
                        </div>
                        <button class="lmo-assign-btn" type="button">Assign Team</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>

        <!-- TREND + CATEGORY SUMMARY -->
        <section class="grid grid-cols-1 xl:grid-cols-3 gap-4 animate-in" style="animation-delay:.1s">
          <!-- Trend -->
          <div class="xl:col-span-2 kt-card">
            <div class="kt-card-header">
              <div>
                <div class="kt-card-title">Trend Harian</div>
                <div class="kt-card-subtitle">Completion % per hari</div>
              </div>
              <div class="badge-kt badge-primary">8 Hari</div>
            </div>
            <div class="kt-card-body">
              <div class="h-56 flex items-end gap-3">
                <div class="flex-1 flex flex-col items-center gap-2" data-tip="88%">
                  <div class="w-full rounded-md bg-[#1b84ff]" style="height:88%"></div>
                  <div class="text-[11px] text-kt-muted">24/2</div>
                  <div class="text-xs font-semibold text-kt-text">88%</div>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2" data-tip="67%">
                  <div class="w-full rounded-md bg-[#56a8ff]" style="height:67%"></div>
                  <div class="text-[11px] text-kt-muted">25/2</div>
                  <div class="text-xs font-semibold text-kt-text">67%</div>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2" data-tip="62%">
                  <div class="w-full rounded-md bg-[#6cb2ff]" style="height:62%"></div>
                  <div class="text-[11px] text-kt-muted">26/2</div>
                  <div class="text-xs font-semibold text-kt-text">62%</div>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2" data-tip="78%">
                  <div class="w-full rounded-md bg-[#3a96ff]" style="height:78%"></div>
                  <div class="text-[11px] text-kt-muted">27/2</div>
                  <div class="text-xs font-semibold text-kt-text">78%</div>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2" data-tip="56%">
                  <div class="w-full rounded-md bg-[#8fc4ff]" style="height:56%"></div>
                  <div class="text-[11px] text-kt-muted">28/2</div>
                  <div class="text-xs font-semibold text-kt-text">56%</div>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2" data-tip="75%">
                  <div class="w-full rounded-md bg-[#4099ff]" style="height:75%"></div>
                  <div class="text-[11px] text-kt-muted">01/3</div>
                  <div class="text-xs font-semibold text-kt-text">75%</div>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2" data-tip="33%">
                  <div class="w-full rounded-md bg-[#d7eaff]" style="height:33%"></div>
                  <div class="text-[11px] text-kt-muted">02/3</div>
                  <div class="text-xs font-semibold text-kt-text">33%</div>
                </div>
                <div class="flex-1 flex flex-col items-center gap-2" data-tip="0%">
                  <div class="w-full rounded-md border border-dashed border-slate-300 bg-slate-50" style="height:4%; min-height:4px"></div>
                  <div class="text-[11px] text-kt-text font-semibold">03/3</div>
                  <div class="text-xs font-semibold text-kt-text">0%</div>
                </div>
              </div>

              <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="rounded-xl border border-kt-border p-3 bg-slate-50/70">
                  <div class="text-[11px] uppercase tracking-[.14em] text-kt-muted">Avg</div>
                  <div class="text-lg font-semibold mt-1">56%</div>
                </div>
                <div class="rounded-xl border border-kt-border p-3 bg-slate-50/70">
                  <div class="text-[11px] uppercase tracking-[.14em] text-kt-muted">Peak</div>
                  <div class="text-lg font-semibold mt-1">88% <span class="text-xs text-kt-muted">(24 Feb)</span></div>
                </div>
                <div class="rounded-xl border border-kt-border p-3 bg-slate-50/70">
                  <div class="text-[11px] uppercase tracking-[.14em] text-kt-muted">Low</div>
                  <div class="text-lg font-semibold mt-1">0% <span class="text-xs text-kt-muted">(03 Mar)</span></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Category Summary -->
          <div class="kt-card">
            <div class="kt-card-header">
              <div>
                <div class="kt-card-title">Kategori Summary</div>
                <div class="kt-card-subtitle">Average 8 hari</div>
              </div>
              <div class="badge-kt badge-neutral">Ranking</div>
            </div>
            <div class="kt-card-body space-y-4">
              <div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                  <span class="font-medium">Area Kritis Mining</span>
                  <span class="text-kt-muted">43%</span>
                </div>
                <div class="progress-track"><div class="progress-fill" style="width:43%"></div></div>
              </div>

              <div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                  <span class="font-medium">Dewatering</span>
                  <span class="text-kt-muted">79%</span>
                </div>
                <div class="progress-track"><div class="progress-fill" style="width:79%"></div></div>
              </div>

              <div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                  <span class="font-medium">Drill & Blast</span>
                  <span class="text-kt-muted">94%</span>
                </div>
                <div class="progress-track"><div class="progress-fill" style="width:94%"></div></div>
              </div>

              <div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                  <span class="font-medium">IKDA</span>
                  <span class="text-kt-muted">88%</span>
                </div>
                <div class="progress-track"><div class="progress-fill" style="width:88%"></div></div>
              </div>

              <div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                  <span class="font-medium">IKDP</span>
                  <span class="text-kt-muted">83%</span>
                </div>
                <div class="progress-track"><div class="progress-fill" style="width:83%"></div></div>
              </div>

              <div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                  <span class="font-medium">IKDK</span>
                  <span class="text-kt-muted">50%</span>
                </div>
                <div class="progress-track"><div class="progress-fill" style="width:50%"></div></div>
              </div>

              <div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                  <span class="font-medium">IKDW</span>
                  <span class="text-kt-muted">50%</span>
                </div>
                <div class="progress-track"><div class="progress-fill" style="width:50%"></div></div>
              </div>

              <div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                  <span class="font-medium">IKPM</span>
                  <span class="text-kt-muted">0%</span>
                </div>
                <div class="progress-track"><div class="progress-fill" style="width:0%"></div></div>
              </div>
            </div>
          </div>
        </section>

        <!-- DETAIL TABLE -->
        <section class="kt-card animate-in" style="animation-delay:.15s">
          <div class="kt-card-header flex-col items-start gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
              <div class="kt-card-title">Detail Plan Pengecekan</div>
              <div class="kt-card-subtitle">Log aktivitas kritis per shift</div>
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
                    <th class="text-left">Aktifitas</th>
                    <th class="text-left">Karyawan</th>
                    <th class="text-left">Task ID</th>
                    <th class="text-left">Detail / Reason</th>
                    <th class="text-left">Jenis SAP</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody id="tableBody">
                  <tr data-shift="1" data-status="notok">
                    <td class="font-medium">
                      <div class="text-sm font-semibold">3/3</div>
                      <div class="text-[11px] text-kt-muted">2026</div>
                    </td>
                    <td><span class="badge-kt badge-neutral">S1</span></td>
                    <td>Area Kritis Mining</td>
                    <td class="text-slate-600">IPD-72 HR BARAT</td>
                    <td class="font-medium">ANDI MUAMMAR</td>
                    <td class="text-kt-muted">—</td>
                    <td class="text-kt-muted">—</td>
                    <td class="text-kt-muted">—</td>
                    <td class="text-center"><span class="badge-kt badge-danger">Not OK</span></td>
                  </tr>

                  <tr data-shift="1" data-status="ok">
                    <td class="font-medium">
                      <div class="text-sm font-semibold">3/2</div>
                      <div class="text-[11px] text-kt-muted">2026</div>
                    </td>
                    <td><span class="badge-kt badge-neutral">S1</span></td>
                    <td>Dewatering</td>
                    <td class="text-slate-600">Sump P Barat & Su.</td>
                    <td class="font-medium">ANTHONIUS ANG.</td>
                    <td class="text-slate-500">5147275</td>
                    <td class="text-kt-muted">—</td>
                    <td><span class="badge-kt badge-primary">OBSERVASI</span></td>
                    <td class="text-center"><span class="badge-kt badge-success">OK</span></td>
                  </tr>

                  <tr data-shift="1" data-status="ok">
                    <td class="font-medium">
                      <div class="text-sm font-semibold">3/1</div>
                      <div class="text-[11px] text-kt-muted">2026</div>
                    </td>
                    <td><span class="badge-kt badge-neutral">S1</span></td>
                    <td>Drill & Blast</td>
                    <td class="text-slate-600">PENGEBORAN DAN P.</td>
                    <td class="font-medium">ANDI MUAMMAR.</td>
                    <td class="text-slate-500">8321546</td>
                    <td class="text-slate-500">Genangan air dilokasi Parkiran pe.</td>
                    <td><span class="badge-kt badge-warning">HAZARD</span></td>
                    <td class="text-center"><span class="badge-kt badge-success">OK</span></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="px-5 py-3 border-t border-kt-border bg-slate-50/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs">
              <span id="rowCount" class="text-kt-muted">Menampilkan semua record</span>
              <span class="text-kt-muted">Total: <span id="totalRowText" class="font-medium text-kt-text">0</span> entri</span>
            </div>
          </div>
        </section>

        <!-- Performance Pengecekan Area / Aktifitas Kritis (LMO Monitor) -->
        <div class="lmo-perf animate-in mt-4" style="animation-delay:.2s">
          <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-4 mb-4 pb-2">
            <div class="headline">
              <h1>Performance Pengecekan Area / Aktifitas Kritis</h1>
              <p>Monthly operational performance tracker for critical mining zones</p>
            </div>
            <div class="d-flex align-items-center gap-3 month-toolbar flex-shrink-0">
              <div class="month-control">
                <button class="btn" type="button" aria-label="Previous month"><i class="material-icons-outlined" style="font-size:1.35rem">chevron_left</i></button>
                <div class="label">February 2024</div>
                <button class="btn" type="button" aria-label="Next month"><i class="material-icons-outlined" style="font-size:1.35rem">chevron_right</i></button>
              </div>
              <button class="btn btn-primary btn-export" type="button">
                <i class="material-icons-outlined me-2" style="font-size:1.1rem">download</i>Export Report
              </button>
            </div>
          </div>

          <!-- Calendar Card -->
          <section class="card-panel calendar-card mb-4">
            <div class="calendar-scroll">
              <div class="calendar-head">
                <div>Sunday</div>
                <div>Monday</div>
                <div>Tuesday</div>
                <div>Wednesday</div>
                <div>Thursday</div>
                <div>Friday</div>
                <div>Saturday</div>
              </div>
              <div class="calendar-grid">
                <div class="day-cell state-neutral"></div>
                <div class="day-cell state-neutral"></div>
                <div class="day-cell state-neutral"></div>
                <div class="day-cell state-neutral"></div>
                <div class="day-cell state-good">
                  <div class="day-num">1</div>
                  <div class="day-center">
                    <div class="score good">8 / 8</div>
                    <div class="mini-progress"><div class="fill good" style="width:100%"></div></div>
                  </div>
                </div>
                <div class="day-cell state-good">
                  <div class="day-num">2</div>
                  <div class="day-center">
                    <div class="score good">10 / 10</div>
                    <div class="mini-progress"><div class="fill good" style="width:100%"></div></div>
                  </div>
                </div>
                <div class="day-cell state-warn">
                  <div class="day-num">3</div>
                  <div class="day-center">
                    <div class="score warn">6 / 9</div>
                    <div class="mini-progress"><div class="fill warn" style="width:66%"></div></div>
                  </div>
                </div>
                <div class="day-cell">
                  <div class="day-num">4</div>
                  <div class="day-center">
                    <div class="off-label">Off Schedule</div>
                  </div>
                </div>
                <div class="day-cell state-bad">
                  <div class="day-num">5</div>
                  <div class="day-center">
                    <div class="score bad">4 / 12</div>
                    <div class="mini-progress"><div class="fill bad" style="width:33%"></div></div>
                  </div>
                </div>
                <div class="day-cell state-good">
                  <div class="day-num">6</div>
                  <div class="day-center">
                    <div class="score good">12 / 12</div>
                    <div class="mini-progress"><div class="fill good" style="width:100%"></div></div>
                  </div>
                </div>
                <div class="day-cell state-warn">
                  <div class="day-num">7</div>
                  <div class="day-center">
                    <div class="score warn">7 / 10</div>
                    <div class="mini-progress"><div class="fill warn" style="width:70%"></div></div>
                  </div>
                </div>
                <div class="day-cell state-good">
                  <div class="day-num">8</div>
                  <div class="day-center">
                    <div class="score good">9 / 10</div>
                    <div class="mini-progress"><div class="fill good" style="width:90%"></div></div>
                  </div>
                </div>
                <div class="day-cell state-good">
                  <div class="day-num">9</div>
                  <div class="day-center">
                    <div class="score good">8 / 8</div>
                    <div class="mini-progress"><div class="fill good" style="width:100%"></div></div>
                  </div>
                </div>
                <div class="day-cell state-bad">
                  <div class="day-num">10</div>
                  <div class="day-center">
                    <div class="score bad">2 / 11</div>
                    <div class="mini-progress"><div class="fill bad" style="width:18%"></div></div>
                  </div>
                </div>
                <div class="day-cell state-good">
                  <div class="day-num">11</div>
                  <div class="day-center">
                    <div class="score good">14 / 15</div>
                    <div class="mini-progress"><div class="fill good" style="width:93%"></div></div>
                  </div>
                </div>
                <div class="day-cell selected">
                  <div class="day-num">24</div>
                  <span class="current-pill">Current</span>
                  <div class="day-center">
                    <div class="score">7 / 8</div>
                    <div class="mini-progress"><div class="fill" style="width:88%; background:#2563eb"></div></div>
                    <div class="completion">88% Completion</div>
                  </div>
                </div>
                <div class="day-cell state-good">
                  <div class="day-num">25</div>
                  <div class="day-center">
                    <div class="score good">8 / 8</div>
                    <div class="mini-progress"><div class="fill good" style="width:100%"></div></div>
                  </div>
                </div>
                <div class="day-cell state-good">
                  <div class="day-num">26</div>
                  <div class="day-center">
                    <div class="score good">12 / 12</div>
                    <div class="mini-progress"><div class="fill good" style="width:100%"></div></div>
                  </div>
                </div>
                <div class="day-cell state-warn">
                  <div class="day-num">27</div>
                  <div class="day-center">
                    <div class="score warn">8 / 12</div>
                    <div class="mini-progress"><div class="fill warn" style="width:67%"></div></div>
                  </div>
                </div>
                <div class="day-cell state-good">
                  <div class="day-num">28</div>
                  <div class="day-center">
                    <div class="score good">10 / 10</div>
                    <div class="mini-progress"><div class="fill good" style="width:100%"></div></div>
                  </div>
                </div>
                <div class="day-cell state-good">
                  <div class="day-num">29</div>
                  <div class="day-center">
                    <div class="score good">11 / 11</div>
                    <div class="mini-progress"><div class="fill good" style="width:100%"></div></div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Activity Log Card -->
          <section class="card-panel">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 p-4 pb-3">
              <h2 class="section-title">Detail Plan Pengecekan (Activity Log)</h2>
              <div class="d-flex gap-2 flex-wrap">
                <button class="soft-chip-btn" type="button">Shift: Day</button>
                <button class="soft-chip-btn" type="button">Status: All</button>
                <button class="soft-chip-btn" type="button"><i class="material-icons-outlined me-1" style="font-size:1rem;vertical-align:middle">filter_list</i>Filters</button>
              </div>
            </div>
            <div class="table-wrap">
              <table class="table table-custom align-middle">
                <thead>
                  <tr>
                    <th style="width:140px;">Time</th>
                    <th>Area</th>
                    <th>Inspector</th>
                    <th>Layer</th>
                    <th style="width:170px;">Status</th>
                    <th style="width:160px;" class="text-end">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>08:45 AM</td>
                    <td>South-West Pit 4</td>
                    <td>John D. Miller</td>
                    <td>Level -240m</td>
                    <td><span class="status-pill status-complete">Completed</span></td>
                    <td class="text-end"><a href="javascript:;" class="action-link action-blue">VIEW DETAILS</a></td>
                  </tr>
                  <tr>
                    <td>09:12 AM</td>
                    <td>Main Haul Road B</td>
                    <td>Sarah Jenkins</td>
                    <td>Surface</td>
                    <td><span class="status-pill status-progress">In Progress</span></td>
                    <td class="text-end"><a href="javascript:;" class="action-link action-blue">MONITOR</a></td>
                  </tr>
                  <tr>
                    <td>10:05 AM</td>
                    <td>Primary Crusher</td>
                    <td>Mike Thompson</td>
                    <td>Infrastructure</td>
                    <td><span class="status-pill status-pending">Pending</span></td>
                    <td class="text-end"><a href="javascript:;" class="action-link action-blue">ASSIGN</a></td>
                  </tr>
                  <tr>
                    <td>10:30 AM</td>
                    <td>North Tailings Dam</td>
                    <td>Emma Wilson</td>
                    <td>Environmental</td>
                    <td><span class="status-pill status-issue">Issue Detected</span></td>
                    <td class="text-end"><a href="javascript:;" class="action-link action-red">URGENT REVIEW</a></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="card-footer-lite">
              <div>Showing 1 to 4 of 32 results</div>
              <nav aria-label="Pagination">
                <ul class="pagination pagination-sm mb-0 pagination-lite">
                  <li class="page-item disabled"><a class="page-link" href="javascript:;"><i class="material-icons-outlined" style="font-size:1rem">chevron_left</i></a></li>
                  <li class="page-item active"><a class="page-link" href="javascript:;">1</a></li>
                  <li class="page-item"><a class="page-link" href="javascript:;">2</a></li>
                  <li class="page-item"><a class="page-link" href="javascript:;">3</a></li>
                  <li class="page-item"><a class="page-link" href="javascript:;"><i class="material-icons-outlined" style="font-size:1rem">chevron_right</i></a></li>
                </ul>
              </nav>
            </div>
          </section>
        </div>

        <!-- Footer -->
        <section class="text-xs text-kt-muted flex flex-col md:flex-row md:items-center md:justify-between gap-2 px-1">
          <div class="font-semibold text-kt-text">Performance Monitor</div>
          <div class="uppercase tracking-[.14em]">Critical Area Inspection — LMO · 2026</div>
          <div>Data updated: 03 Mar 2026, 00:00</div>
        </section>
      </main>
    </div>
</div>


  <script>
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

    // --- Heatmap Calendar: kalender dinamis, deteksi bulan berjalan, navigasi ---
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

      function getDayState(dayNum) {
        var seed = currentYear * 10000 + currentMonth * 100 + dayNum;
        var r = (seed * 9301 + 49297) % 233280;
        var ratio = r / 233280;
        if (ratio < 0.6) return { state: 'good', done: 8, total: 8, pct: 100 };
        if (ratio < 0.85) return { state: 'warn', done: Math.floor(6 + ratio * 4), total: 10, pct: Math.floor(60 + ratio * 25) };
        return { state: 'bad', done: Math.floor(2 + ratio * 3), total: 10, pct: Math.floor(20 + ratio * 20) };
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
          const isToday = isCurrentMonth && d === today.getDate();
          const info = getDayState(d);
          const stateClass = info.state === 'good' ? 'state-good' : (info.state === 'warn' ? 'state-warn' : 'state-bad');
          const scoreClass = info.state === 'good' ? 'good' : (info.state === 'warn' ? 'warn' : 'bad');

          if (isToday) {
            html += '<div class="day-cell selected">';
            html += '<div class="day-num">' + d + '</div>';
            html += '<span class="current-pill">Hari ini</span>';
            html += '<div class="day-center">';
            html += '<div class="score">' + info.done + ' / ' + info.total + '</div>';
            html += '<div class="mini-progress"><div class="fill" style="width:' + info.pct + '%; background:#2563eb"></div></div>';
            html += '<div class="completion">' + info.pct + '% Completion</div>';
            html += '</div></div>';
          } else {
            html += '<div class="day-cell ' + stateClass + '">';
            html += '<div class="day-num">' + d + '</div>';
            html += '<div class="day-center">';
            html += '<div class="score ' + scoreClass + '">' + info.done + ' / ' + info.total + '</div>';
            html += '<div class="mini-progress"><div class="fill ' + scoreClass + '" style="width:' + info.pct + '%"></div></div>';
            html += '</div></div>';
          }
        }

        gridEl.innerHTML = html;
      }

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
