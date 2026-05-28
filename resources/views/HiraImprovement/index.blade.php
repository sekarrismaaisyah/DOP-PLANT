<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Risk Driver Correlation Dashboard | Week 18 2026</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&amp;family=Inter:wght@300;400;500;600&amp;display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            page: "#f0f2f5",
            ink: "#2c2f31",
            muted: "#595c5e",
            line: "#dfe3e6",
            primary: "#3952bc",
            error: "#dc2626"
          },
          boxShadow: {
            soft: "0 6px 16px -6px rgba(15, 23, 42, 0.16)",
            anchored: "0 4px 0px 0px rgba(15, 23, 42, 0.04), 0 14px 24px -10px rgba(15, 23, 42, 0.2)",
            anchoredHover: "0 6px 0px 0px rgba(15, 23, 42, 0.04), 0 18px 34px -12px rgba(15, 23, 42, 0.26)",
            popup: "0 24px 54px rgba(15, 23, 42, 0.22)"
          },
          borderRadius: {
            card: "18px"
          },
          fontFamily: {
            sans: ["Inter", "ui-sans-serif", "system-ui", "-apple-system", "BlinkMacSystemFont", "Segoe UI", "sans-serif"],
            headline: ["Poppins"],
            body: ["Poppins"],
            label: ["Poppins"]
          }
        }
      }
    };
  </script>
  <style type="text/tailwindcss">
    @layer base {
      :root {
        --bg: #f0f2f5;
        --ink: #2c2f31;
        --muted: #595c5e;
        --line: #dfe3e6;
        --green: #33a852;
        --green-soft: #e7f6ea;
        --yellow: #f5bd24;
        --yellow-soft: #fff6d9;
        --orange: #ef8a21;
        --orange-soft: #fff0df;
        --red: #d92828;
        --red-soft: #fde9e9;
        --blue: #2677d9;
        --blue-soft: #e9f2ff;
      }

      * { @apply box-border; }

      body {
        @apply m-0 min-h-screen bg-[#f4f6f8] text-ink font-body antialiased;
      }

      button,
      select { font: inherit; }

      button,
      select {
        @apply cursor-pointer rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-[13px] font-medium text-ink shadow-sm transition-colors duration-150;
      }

      button:hover,
      select:hover { @apply border-slate-300 bg-slate-50; }

      button.active { @apply border-[#3952bc] bg-[#3952bc] text-white shadow-sm; }

      table { @apply w-full min-w-[860px] border-collapse; }
      th, td { @apply border-b border-[#edf1f5] px-3.5 py-3 text-left align-middle text-[13px]; }
      th { @apply whitespace-nowrap bg-[#f8fafc] text-[10px] font-bold uppercase tracking-[.07em] text-[#5f6772]; }
      tr:last-child td { @apply border-b-0; }

      [hidden] { display: none !important; }
    }

    @layer components {
      .layout-shell { @apply w-full px-4 sm:px-6 lg:px-8 xl:px-10 2xl:px-12; }

      .page-hero {
        @apply rounded-xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-sm;
      }
      .page-hero-inner {
        @apply grid gap-8 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,1fr)] lg:items-start;
      }
      .page-hero-label {
        @apply mb-2 text-[11px] font-semibold uppercase tracking-wider text-[#3952bc];
      }
      .page-hero h1 {
        @apply m-0 text-[clamp(1.375rem,2.5vw,2rem)] font-bold leading-snug tracking-tight text-[#1f2937];
      }
      .page-hero .subtitle {
        @apply mt-3 max-w-none text-[14px] leading-relaxed text-muted font-normal;
      }

      .hero-strip {
        @apply mt-6 flex w-full flex-col items-stretch gap-3 sm:flex-row sm:flex-wrap sm:items-end sm:justify-end lg:mt-7 lg:flex-nowrap lg:gap-4;
      }
      .hero-filter-field {
        @apply flex min-w-0 flex-col gap-1.5 sm:w-[min(100%,200px)] lg:w-[212px];
      }
      .hero-filter-field label {
        @apply text-[11px] font-bold uppercase tracking-[0.1em] text-slate-600;
      }
      .hero-filter-field .hero-select {
        @apply w-full cursor-pointer rounded-[11px] border border-slate-200 bg-white py-2.5 pl-3.5 pr-3 text-[13px] font-semibold text-slate-800 shadow-sm outline-none transition hover:border-slate-300 focus:border-[rgba(57,82,188,0.55)] focus:ring-2 focus:ring-[rgba(57,82,188,0.18)];
      }
      .hero-stat-card {
        @apply flex min-h-[118px] min-w-0 flex-1 flex-col justify-between rounded-[14px] border border-slate-200/95 bg-white px-4 py-3.5 shadow-sm transition hover:border-slate-300/90 hover:shadow-md sm:min-h-[120px] sm:max-w-[min(100%,240px)] lg:min-w-[172px] lg:max-w-[220px];
      }
      .hero-stat-head {
        @apply flex items-center gap-2;
      }
      .hero-stat-ico {
        @apply text-[18px] leading-none text-[#3952bc];
        font-variation-settings: "FILL" 0, "wght" 500, "GRAD" 0, "opsz" 24;
      }
      .hero-stat-kicker {
        @apply text-[10px] font-bold uppercase leading-tight tracking-[0.12em] text-slate-600;
      }
      .hero-stat-value {
        @apply font-headline text-[1.85rem] font-extrabold leading-none tracking-tight text-[#3952bc] sm:text-[2.05rem];
      }
      .hero-stat-note {
        @apply text-[11px] font-medium leading-snug text-slate-500;
      }

      .toolbar {
        @apply flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-200/90 bg-white px-3 py-3 shadow-sm;
      }
      .tabs, .filters { @apply flex flex-wrap items-center gap-2.5; }

      .page-intro { @apply mb-[18px] grid grid-cols-[1fr_auto] items-center gap-4; }
      .page-intro h2 { @apply m-0 mb-1.5 text-xl font-bold tracking-tight text-[#1f2937] sm:text-2xl; }
      .page-intro p { @apply m-0 text-[13px] leading-[1.55] text-muted; }
      .page-counter { @apply inline-flex min-w-[72px] items-center justify-center whitespace-nowrap rounded-full bg-[#e9f2ff] px-3 py-2.5 text-xs font-black text-[#1559a8]; }

      .grid { @apply gap-[18px]; }
      .grid-site-cards { @apply grid-cols-1 sm:grid-cols-2 lg:grid-cols-3; }
      .grid-2 { @apply grid-cols-2; }
      .card {
        @apply rounded-xl border border-slate-200/90 bg-white p-5 sm:p-6 shadow-sm;
        transition: box-shadow .15s ease, border-color .15s ease;
      }
      .card:hover {
        @apply border-slate-300/90 shadow-md;
      }
      .card-title { @apply mb-3.5 flex items-start justify-between gap-3; }
      .card-title h2, .card-title h3 { @apply m-0 leading-[1.12] tracking-[-.03em]; }
      .card-title h2 { @apply text-lg font-bold text-[#1f2937] sm:text-xl; }
      .card-title h3 { @apply text-base font-semibold text-[#1f2937] sm:text-lg; }

      .muted { @apply text-muted; }
      .small { @apply text-xs; }
      .strong { @apply font-black; }

      .badge { @apply inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-[7px] text-[11px] font-bold; }
      .badge.green { @apply bg-[#e7f6ea] text-[#167232]; }
      .badge.yellow { @apply bg-[#fff6d9] text-[#8a6200]; }
      .badge.orange { @apply bg-[#fff0df] text-[#9e5000]; }
      .badge.red { @apply bg-[#fde9e9] text-[#ad1010]; }
      .badge.blue { @apply bg-[#e9f2ff] text-[#1559a8]; }

      .cards-wrap { @apply relative; }
      .score-card { @apply relative min-h-[186px] cursor-pointer overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm transition duration-150 ease-out; }
      .score-card:hover { @apply border-slate-300 shadow-md; }
      .score-card.selected { @apply ring-2 ring-[rgba(57,82,188,0.25)] ring-offset-2; outline: none; }
      .score-top { @apply relative z-[2] flex items-start justify-between gap-2.5; }
      .site-name { @apply m-0 text-xl font-semibold tracking-tight text-[#1f2937] sm:text-2xl; }
      .risk-score { @apply mt-4 text-3xl font-bold tracking-tight text-[#111827] sm:text-[2rem]; }

      .mini-bars, .driver-list, .summary-list, .action-card, .driver-detail-list { @apply grid gap-2.5; }
      .mini-bars { @apply relative z-[2] mt-4; }
      .metric-line, .driver-row { @apply grid grid-cols-[150px_1fr_42px] items-center gap-2.5 text-xs; }
      .metric-line { @apply grid-cols-[82px_1fr_36px] text-[11px] text-muted; }
      .bar-bg { @apply h-2 overflow-hidden rounded-full bg-[#e7edf3]; }
      .bar-fill { @apply h-full rounded-full bg-[#3952bc]; }
      .bar-fill.yellow { @apply bg-[#f5bd24]; }
      .bar-fill.orange { @apply bg-[#ef8a21]; }
      .bar-fill.red { @apply bg-[#d92828]; }
      .bar-fill.blue { @apply bg-[#2677d9]; }

      .section { @apply mt-[18px]; }
      .summary-item { @apply grid grid-cols-[36px_1fr_auto] items-center gap-3 rounded-2xl border border-line bg-white p-3; }
      .rank { @apply grid h-9 w-9 place-items-center rounded-xl bg-[#eef2ff] text-[#243d8f] font-extrabold; }
      .table-wrap { @apply overflow-auto rounded-[16px] border border-slate-200 bg-white shadow-sm; }

      .heatmap-cell { @apply inline-flex min-w-[92px] justify-center whitespace-nowrap rounded-[10px] px-2.5 py-2 font-black text-[#16301f]; }
      .heat-low { @apply bg-[#e7f6ea] text-[#14722e]; }
      .heat-med { @apply bg-[#fff6d9] text-[#8a6200]; }
      .heat-high { @apply bg-[#fff0df] text-[#9e5000]; }
      .heat-critical { @apply bg-[#fde9e9] text-[#a81414]; }

      .pill-status { @apply inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1.5 text-[11px] font-black; }
      .legend { @apply mt-3 flex flex-wrap items-center gap-3; }
      .legend-item { @apply inline-flex items-center gap-[7px] text-xs font-semibold text-muted; }
      .swatch { @apply h-3 w-3 rounded; }

      .scatter-box { @apply relative min-h-[420px] overflow-hidden rounded-xl border border-slate-200 bg-white; background-image: linear-gradient(90deg, rgba(51,168,82,.06) 0 50%, rgba(217,40,40,.06) 50% 100%), linear-gradient(0deg, rgba(51,168,82,.05) 0 50%, rgba(239,138,33,.05) 50% 100%); }
      .scatter-axis-x, .scatter-axis-y { @apply absolute z-[1] bg-[rgba(23,37,29,0.25)]; }
      .scatter-axis-x { @apply left-[8%] right-[6%] top-1/2 h-px; }
      .scatter-axis-y { @apply bottom-[10%] left-1/2 top-[8%] w-px; }
      .quadrant-label { @apply absolute text-xs font-black uppercase tracking-[.04em] text-[rgba(23,37,29,0.42)]; }
      .q1 { @apply right-[22px] top-[18px]; }
      .q2 { @apply left-[22px] top-[18px]; }
      .q3 { @apply bottom-[26px] left-[22px]; }
      .q4 { @apply bottom-[26px] right-[22px]; }
      .point { @apply absolute z-[3] grid h-[42px] w-[42px] -translate-x-1/2 -translate-y-1/2 cursor-pointer place-items-center rounded-full border-4 border-white/90 text-xs font-black text-white shadow-[0_10px_24px_rgba(23,37,29,0.20)]; }
      .point-label { @apply absolute left-1/2 top-[39px] -translate-x-1/2 whitespace-nowrap rounded-full border border-line bg-white/90 px-2 py-1 text-[11px] text-ink shadow-[0_10px_20px_rgba(23,37,29,0.08)]; }

      .driver-detail-card { @apply rounded-2xl border border-line bg-white p-3; }
      .driver-detail-title { @apply mb-2 flex items-center justify-between gap-2.5 font-bold; }
      .driver-detail-note, .driver-mini-note { @apply text-xs leading-[1.55] text-muted; }
      .driver-mini-note { @apply col-span-full -mt-1 text-[11px]; }
      .detail-chip-row { @apply mt-2 flex flex-wrap gap-1.5; }
      .detail-chip { @apply inline-flex items-center rounded-full border border-slate-200 bg-[#f8fafc] px-2 py-1 text-[10px] font-bold text-[#475569]; }

      .bridge { @apply grid grid-cols-2 gap-3; }
      .bridge-box { @apply min-h-[150px] rounded-[18px] border border-line bg-white p-4; }
      .bridge-box h4 { @apply m-0 mb-2 text-[15px] font-bold text-[#1f2937]; }
      .bridge-box p { @apply m-0 text-[13px] leading-normal text-muted; }
      .bridge-box.critical { @apply border-[#d92828]/20 bg-[#fde9e9]; }
      .bridge-box.watch { @apply border-[#f5bd24]/25 bg-[#fff6d9]; }
      .bridge-box.latent { @apply border-[#ef8a21]/20 bg-[#fff0df]; }
      .bridge-box.safe { @apply border-[#33a852]/20 bg-[#e7f6ea]; }

      .sparkline { @apply h-[42px] w-full overflow-visible; }
      .footer-note { @apply mt-[18px] rounded-[18px] border border-line bg-white px-4 py-3.5 text-xs leading-[1.6] text-muted; }
      .action-item { @apply rounded-[18px] border border-line bg-white p-3.5; }
      .action-head { @apply mb-2.5 flex items-center justify-between gap-2.5; }
      .action-title { @apply font-extrabold text-[#1f2937]; }
      .checklist { @apply m-0 pl-[18px] text-[13px] leading-[1.55] text-muted; }
      .risk-profile-board {
        @apply relative mt-1 overflow-hidden rounded-2xl border border-slate-300/90 bg-white p-3 shadow-sm sm:p-4;
      }
      .risk-profile-main {
        @apply relative rounded-xl border border-slate-300 bg-white p-2 sm:p-2.5;
      }
      .risk-profile-main::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 1rem;
        pointer-events: none;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
      }
      .risk-profile-main-chart { @apply h-[260px] w-full rounded-md border border-slate-300 bg-white; }
      .risk-profile-toolbar { @apply mb-2 flex flex-wrap items-center justify-between gap-2; }
      .risk-profile-site-filter {
        @apply rounded-md border border-slate-300 bg-white px-2 py-1 text-[11px] font-semibold text-[#334155];
      }
      .risk-profile-mini-grid { @apply mt-3 grid grid-cols-1 gap-2.5 md:grid-cols-2 xl:grid-cols-3; }
      .risk-profile-mini {
        @apply rounded-lg border border-slate-300 p-2 shadow-[0_10px_24px_-20px_rgba(15,23,42,0.5)] transition-transform duration-200 sm:p-2.5;
      }
      .risk-profile-mini.selected { @apply border-[#0ea5e9] ring-2 ring-[#bae6fd]; }
      .risk-profile-mini:hover { transform: translateY(-2px); }
      .risk-profile-head { @apply mb-2 flex items-center justify-between gap-2; }
      .risk-profile-site { @apply border-l-[3px] border-[#dc2626] pl-1.5 text-sm font-black tracking-tight text-[#111827]; }
      .risk-profile-chip-wrap { @apply flex items-center gap-1.5; }
      .risk-chip { @apply rounded border border-slate-300 bg-[#ececec] px-1.5 py-0.5 text-[9px] font-semibold text-[#334155]; }
      .risk-profile-status { @apply inline-flex items-center rounded-full border px-2 py-[3px] text-[10px] font-black; }
      .risk-profile-score { @apply text-[11px] font-semibold text-[#334155]; }
      .risk-profile-mini-chart { @apply h-[118px] w-full; }
      .risk-profile-legend { @apply mt-2.5 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-[11px] text-[#374151]; }
      .legend-line { @apply inline-flex items-center gap-1.5; }
      .legend-line i { @apply inline-block h-[3px] w-5 rounded-full; }
      .risk-profile-footer { @apply mt-2 flex flex-wrap items-center gap-3 text-[11px] italic text-[#4b5563]; }
      .risk-profile-band { @apply inline-flex items-center gap-1.5; }
      .risk-profile-band i { @apply inline-block h-4 w-10 rounded-sm; }

      .exec-summary-panel.risk-profile-board {
        @apply border-slate-200/95 p-0 shadow-[0_1px_0_rgba(255,255,255,.8)_inset,0_12px_40px_-24px_rgba(15,23,42,0.12)] sm:p-0;
      }
      .exec-summary-top {
        @apply flex flex-col gap-4 border-b border-slate-100 px-5 pb-5 pt-5 sm:flex-row sm:items-end sm:justify-between sm:px-6 sm:pb-6 sm:pt-6;
      }
      .exec-summary-top .card-title {
        @apply mb-0 flex-1;
      }
      .exec-summary-actions {
        @apply flex w-full flex-shrink-0 flex-wrap items-center gap-2 sm:w-auto sm:justify-end;
      }
      .exec-summary-actions .risk-profile-site-filter {
        @apply min-w-[140px] flex-1 sm:flex-none;
      }
      .exec-kpi-grid {
        @apply grid grid-cols-1 gap-4 px-5 pb-2 pt-5 sm:grid-cols-3 sm:gap-5 sm:px-6 sm:pb-2 sm:pt-6;
      }
      .exec-kpi-card {
        @apply relative flex min-h-[132px] flex-col justify-between overflow-hidden rounded-[14px] border border-slate-200/90 bg-white px-5 py-4 shadow-sm transition-[box-shadow,transform,border-color] duration-200 sm:min-h-[140px] sm:rounded-2xl sm:px-5 sm:py-5;
      }
      .exec-kpi-card::before {
        content: "";
        @apply pointer-events-none absolute inset-x-0 top-0 h-[3px] bg-gradient-to-r from-[#3952bc]/90 via-[#6366f1]/70 to-[#0ea5e9]/80 opacity-90;
      }
      .exec-kpi-card:hover {
        @apply -translate-y-0.5 border-slate-300/90 shadow-md;
      }
      .exec-kpi-label {
        @apply text-[10px] font-bold uppercase leading-tight tracking-[0.14em] text-slate-500;
      }
      .exec-kpi-value {
        @apply mt-3 font-headline text-[clamp(1.65rem,3.8vw,2.15rem)] font-extrabold leading-none tracking-tight text-[#111827] tabular-nums;
      }
      .exec-kpi-value .exec-kpi-arrow {
        @apply mx-0.5 font-normal text-slate-400;
      }
      .exec-kpi-sub {
        @apply mt-2.5 text-[13px] font-medium leading-snug text-slate-500;
      }
      .mgmt-highlight {
        @apply mx-5 mb-5 mt-2 rounded-[14px] border border-slate-200/90 bg-gradient-to-b from-white to-slate-50/90 px-5 py-5 shadow-sm sm:mx-6 sm:mb-6 sm:mt-3 sm:rounded-2xl sm:px-6 sm:py-6;
      }
      .mgmt-highlight-title {
        @apply m-0 text-base font-bold tracking-tight text-[#1e293b] sm:text-[17px];
      }
      .mgmt-highlight-body {
        @apply mt-4 space-y-3 text-[14px] leading-[1.68] text-slate-600;
      }
      .mgmt-highlight-body strong {
        @apply font-bold text-[#0f172a];
      }

      .improvement-rekap-card .card-title h2 {
        @apply text-lg font-bold text-[#1f2937] sm:text-xl;
      }
      .improvement-rekap-filter-hint {
        @apply mt-2 flex flex-wrap items-center gap-2 text-[12px] leading-snug text-slate-600;
      }
      .improvement-rekap-filter-hint button {
        @apply rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-600 shadow-none;
      }
      .improvement-rekap-filter-hint button:hover {
        @apply border-slate-300 bg-slate-50 text-slate-800;
      }
      .improvement-rekap-wrap {
        @apply overflow-x-auto rounded-xl border border-slate-200/95 bg-white;
      }
      .improvement-rekap-table {
        @apply min-w-[980px] border-collapse text-[13px];
      }
      .improvement-rekap-table thead th {
        @apply border-b border-slate-200 bg-[#f0f4f8] px-3 py-3.5 text-center text-[10px] font-bold uppercase tracking-[0.08em] text-[#5f6772];
      }
      .improvement-rekap-table thead th:first-child {
        @apply text-left;
      }
      .improvement-rekap-table tbody td {
        @apply border-b border-slate-100 px-3 py-3 align-middle text-[13px] text-[#334155];
      }
      .improvement-rekap-table tbody td:first-child {
        @apply max-w-[280px] text-left font-medium text-[#1e293b];
      }
      .improvement-rekap-table tbody td.improvement-rekap-exposure {
        @apply text-center text-[13px] font-bold text-[#111827];
      }
      .improvement-rekap-table tbody td.improvement-rekap-num {
        @apply text-center tabular-nums text-[13px] text-slate-700;
      }
      .improvement-rekap-table tbody tr {
        @apply cursor-pointer transition-colors duration-150;
      }
      .improvement-rekap-table tbody tr:hover {
        @apply bg-slate-50/90;
      }
      .improvement-rekap-table tbody tr.is-selected {
        @apply bg-[#eff6ff] outline outline-1 -outline-offset-1 outline-[#3952bc]/35;
      }
      .pill-risk,
      .pill-decision {
        @apply inline-flex items-center justify-center whitespace-nowrap rounded-full border px-2.5 py-1 text-[11px] font-bold leading-none;
      }
      .pill-risk--significant {
        @apply border-[#d9534f] bg-[#f9ebeb] text-[#c9302c];
      }
      .pill-risk--high {
        @apply border-[#f0ad4e] bg-[#fcf4e7] text-[#c77c11];
      }
      .pill-risk--medium {
        @apply border-[#dfb81c] bg-[#fef9e7] text-[#9a7b0a];
      }
      .pill-decision--target {
        @apply border-[#d9534f] bg-[#f9ebeb] text-[#b52a26];
      }
      .pill-decision--verify {
        @apply border-[#dfb81c] bg-[#fef9e7] text-[#8a7200];
      }
      .pill-decision--coverage {
        @apply border-[#f0ad4e] bg-[#fcf4e7] text-[#b8740e];
      }
      .improvement-rekap-view-btn {
        @apply grid h-9 w-9 place-items-center rounded-lg border border-slate-200 bg-white p-0 text-slate-600 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 hover:text-[#3952bc];
      }
      .improvement-rekap-view-btn .material-symbols-outlined {
        @apply text-[20px];
        font-variation-settings: "FILL" 0, "wght" 500, "GRAD" 0, "opsz" 24;
      }
      .improvement-detail-modal {
        @apply fixed inset-0 z-[200] flex items-center justify-center p-4;
      }
      .improvement-detail-modal[hidden] {
        display: none !important;
      }
      .improvement-detail-modal__backdrop {
        @apply absolute inset-0 bg-slate-900/40 backdrop-blur-[2px];
      }
      .improvement-detail-modal__panel {
        @apply relative z-[1] w-full max-w-lg rounded-2xl border border-slate-200/90 bg-white p-6 shadow-popup;
      }
      .improvement-detail-modal__panel h3 {
        @apply m-0 pr-10 text-lg font-bold tracking-tight text-[#1f2937];
      }
      .improvement-detail-modal__panel .improvement-detail-body {
        @apply mt-4 space-y-3 text-[13px] leading-relaxed text-slate-600;
      }

      .risk-matrix-section .risk-matrix-dual {
        @apply grid grid-cols-1 gap-6 lg:grid-cols-2 lg:gap-8;
      }
      .risk-matrix-panel {
        @apply rounded-2xl border border-slate-200/95 bg-white p-4 shadow-sm sm:p-5;
      }
      .risk-matrix-panel h3 {
        @apply m-0 text-[15px] font-bold leading-snug tracking-tight text-[#1e3a5f] sm:text-base;
      }
      .risk-matrix-panel .risk-matrix-sub {
        @apply mt-1.5 text-[12px] leading-relaxed text-slate-500 sm:text-[13px];
      }
      .risk-matrix-grid {
        @apply mt-4 grid w-full max-w-full;
        grid-template-columns: 2.75rem repeat(5, minmax(0, 1fr));
        grid-template-rows: 2rem repeat(5, minmax(2.65rem, 1fr));
        gap: 0.4rem;
      }
      @media (min-width: 640px) {
        .risk-matrix-grid {
          grid-template-columns: 3rem repeat(5, minmax(0, 1fr));
          grid-template-rows: 2.25rem repeat(5, minmax(3rem, 1fr));
          gap: 0.5rem;
        }
      }
      .risk-matrix-corner {
        @apply flex items-center justify-center rounded-lg border border-slate-200/80 bg-slate-50 text-[11px] font-black tracking-tight text-slate-600;
      }
      .risk-matrix-col-head,
      .risk-matrix-row-head {
        @apply flex items-center justify-center rounded-lg border border-slate-200/70 bg-[#f0f4f8] text-[11px] font-bold text-slate-600;
      }
      .risk-matrix-cell {
        @apply relative min-h-[2.65rem] rounded-[11px] border text-center text-[14px] font-extrabold tabular-nums text-[#0f172a] shadow-sm transition-[transform,box-shadow,outline] duration-150 sm:min-h-[3rem] sm:rounded-xl sm:text-[15px];
      }
      .risk-matrix-cell:hover {
        @apply z-[1] -translate-y-px shadow-md;
      }
      .risk-matrix-cell:focus-visible {
        @apply z-[1] outline outline-2 outline-offset-2 outline-[#3952bc];
      }
      .risk-matrix-cell--green {
        @apply border-emerald-300/80 bg-emerald-100/95;
      }
      .risk-matrix-cell--yellow {
        @apply border-amber-300/90 bg-amber-100/95;
      }
      .risk-matrix-cell--orange {
        @apply border-orange-300/90 bg-orange-100/95;
      }
      .risk-matrix-cell--red {
        @apply border-red-300/90 bg-red-100/95;
      }
      .risk-matrix-cell.is-selected {
        @apply z-[2] ring-2 ring-[#3952bc] ring-offset-2 ring-offset-white;
      }
      .risk-matrix-cell .tooltip-wrap {
        @apply flex h-full min-h-[inherit] w-full cursor-pointer items-center justify-center rounded-[inherit] px-0.5 py-0.5 font-extrabold text-inherit;
      }

      .site-performance-card { @apply mt-3 rounded-lg border border-[#ddd8cb] bg-white p-2; }
      .site-performance-table-wrap { @apply overflow-x-auto rounded-sm border border-[#dbd6ca] bg-white; }
      .site-performance-table { @apply min-w-[1120px] border-collapse text-[9px] text-[#1f2937]; }
      .site-performance-table th,
      .site-performance-table td { @apply border border-[#d8d4c8] px-1.5 py-1 text-center align-middle; }
      .site-performance-table th:first-child,
      .site-performance-table td:first-child,
      .site-performance-table th:nth-child(2),
      .site-performance-table td:nth-child(2),
      .site-performance-table th:nth-child(3),
      .site-performance-table td:nth-child(3) { @apply text-left; }
      .site-performance-table thead th { @apply bg-[#e7e3d7] text-[9px] font-black uppercase tracking-[.02em]; }
      .site-performance-table .head-site { @apply bg-[#e7e3d7] text-center align-bottom pb-0.5; }
      .site-performance-table .head-contractor { @apply bg-[#f0ece2] pt-0 pb-1 text-[8px] font-bold text-[#6b7280]; }
      .site-performance-table .group-head { @apply bg-[#efe7cc] font-bold text-[#475569] align-top; }
      .site-performance-table .group-head.people { @apply bg-[#deefe8]; }
      .site-performance-table .group-head.process { @apply bg-[#ebf3d2]; }
      .site-performance-table .group-head.technology { @apply bg-[#e6f0db]; }
      .site-performance-table .parameter-head { @apply bg-[#f2efe6] font-bold text-[#475569]; }
      .site-performance-table .metric-head { @apply bg-[#f2efe6] font-semibold text-[#64748b]; }
      .site-performance-table .site-sub { @apply block text-[8px] font-semibold text-[#6b7280]; }
      .site-performance-table .marker {
        @apply mr-1 inline-flex h-3 w-3 items-center justify-center rounded-full text-[8px] font-black text-[#8b5e00];
        background: #ffd561;
      }
      .site-performance-table .sub-metric { @apply pl-3 text-[#7c8798]; }
      .site-performance-table .cell-watch { @apply bg-[#f8d2f6] font-bold text-[#6b2175]; }
      .site-performance-table .cell-danger { @apply border-2 border-[#ef4444] bg-[#fee2e2] font-black text-[#991b1b]; }
      .site-performance-table .cell-muted { @apply text-[#6b7280]; }
      .site-performance-correlation { @apply mt-2.5 grid grid-cols-1 gap-2 md:grid-cols-2 xl:grid-cols-3; }
      .site-correlation-item { @apply rounded-sm border-l-[3px] border-[#eab308] bg-white px-2 py-1.5; }
      .site-correlation-item.best { @apply border-[#16a34a]; }
      .site-correlation-item h4 { @apply m-0 text-[11px] font-black text-[#111827]; }
      .site-correlation-item p { @apply mt-1 text-[10px] leading-[1.35] text-[#374151]; }
      .site-correlation-note { @apply mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[9px] text-[#4b5563]; }
      .site-correlation-note span { @apply inline-flex items-center gap-1; }
      .site-correlation-note i { @apply inline-block h-2.5 w-2.5 rounded-sm; }

      .stat-tile { @apply rounded-[18px] border border-line bg-white p-3.5; }
      .stat-tile .num { @apply mt-1.5 text-[26px] font-extrabold tracking-[-.03em] text-[#111827]; }
      .formula-box { @apply overflow-auto rounded-[18px] bg-[#102b1b] p-4 font-mono text-xs leading-[1.7] text-[#d9ffe3]; }

      .tooltip-wrap { @apply relative inline-flex cursor-help items-center; }
      .floating-tooltip { @apply pointer-events-none fixed left-0 top-0 z-[9999] max-w-[min(340px,calc(100vw_-_28px))] -translate-x-[9999px] -translate-y-[9999px] rounded-[14px] bg-[rgba(16,43,27,0.96)] px-3 py-2.5 text-left text-xs font-semibold leading-[1.45] text-white opacity-0 shadow-[0_14px_28px_rgba(16,43,27,0.25)] transition-opacity duration-150; white-space: normal; }
      .floating-tooltip.show { @apply opacity-100; }
      .help-dot { @apply ml-1.5 grid h-4 w-4 place-items-center rounded-full border border-slate-200 bg-[#f8fafc] text-[10px] font-bold text-muted; }

      .site-popup { @apply absolute z-[120] hidden w-[min(430px,calc(100vw_-_36px))] rounded-card border border-line/95 bg-white/95 p-[18px] shadow-popup backdrop-blur-md; }
      .site-popup.show { @apply block; }
      .site-popup::before { content: ""; position: absolute; top: -9px; left: var(--popup-arrow-left, 34px); width: 18px; height: 18px; background: rgba(255,255,255,.98); border-top: 1px solid rgba(223,232,226,.95); border-left: 1px solid rgba(223,232,226,.95); transform: rotate(45deg); }
      .popup-head { @apply mb-3 flex items-start justify-between gap-3; }
      .popup-close { @apply grid h-8 w-8 flex-none place-items-center rounded-full p-0 text-lg leading-none shadow-none; }
      .popup-close:hover { @apply translate-y-0 bg-[#f6f8f7] shadow-none; }
      .popup-grid { @apply mb-3 grid grid-cols-2 gap-3; }
    }

    @media (max-width: 980px) {
      .grid-2,
      .popup-grid { @apply grid-cols-1; }

      .hero-stat-card { @apply sm:flex-1 sm:max-w-none; }
      .scatter-box { @apply min-h-[360px]; }
      .bridge { @apply grid-cols-1; }
      .site-popup { position: fixed; left: 14px !important; right: 14px !important; bottom: 14px; top: auto !important; width: auto; max-height: 78vh; overflow: auto; }
      .site-popup::before { display: none; }
    }

    @media (max-width: 620px) {
      .page-intro { @apply grid-cols-1; }
      .page-counter { @apply justify-self-start; }
      .page-hero { @apply p-5; }
      .hero-strip { @apply flex-col; }
      .hero-stat-card { @apply max-w-none; }
      .card { @apply rounded-[20px] p-4; }
      .toolbar { @apply items-stretch; }
      .tabs, .filters { @apply w-full; }
      button, select { @apply flex-1; }
      .improvement-rekap-view-btn {
        flex: 0 0 auto !important;
        width: 2.25rem;
        height: 2.25rem;
      }
      .driver-row { @apply grid-cols-[122px_1fr_34px]; }
      .metric-line { @apply grid-cols-[76px_1fr_34px]; }
      .summary-item { @apply grid-cols-[34px_1fr]; }
      .summary-item .badge { @apply col-start-2 justify-self-start; }
    }
  </style>
</head>
<body class="bg-[#f4f6f8] font-body text-[#2c2f31] min-h-screen flex flex-col">
  <header class="w-full sticky top-0 z-50 border-b border-[#e2e8f0] bg-white/95 shadow-sm backdrop-blur-sm">
    <div class="layout-shell flex flex-col gap-4 py-3.5 sm:flex-row sm:items-center sm:justify-between sm:py-4">
      <div class="flex items-center gap-10">
        <div class="flex flex-col">
          <h1 class="font-headline font-bold text-[#3952bc] text-xl tracking-tighter leading-tight">OHS Division</h1>
          <p class="text-[#64748b] text-[10px] font-semibold uppercase tracking-widest">Safety Performance Review</p>
        </div>
        <div class="h-8 w-px bg-[#dfe3e6] hidden lg:block"></div>
        <!-- <nav class="hidden flex-wrap gap-x-6 gap-y-2 md:flex lg:gap-8">
          <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="{{ route('peer-pressure-edukasi.dashboard', request()->only(['year', 'month', 'q', 'hazard_site'])) }}">Lagging</a>
          <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="{{ route('peer-pressure-edukasi.dashboard-performance', request()->only(['year', 'month', 'q', 'hazard_site'])) }}">Dash Performance</a>
          <a class="text-[#3952bc] border-b-2 border-[#3952bc] pb-1 font-bold text-sm tracking-tight" href="{{ route('peer-pressure-edukasi.dashboard-risk-score', request()->only(['year', 'month', 'q', 'hazard_site'])) }}">Risk Score Site</a>
          <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="{{ route('peer-pressure-edukasi.tematic', request()->only(['year', 'month', 'q', 'hazard_site'])) }}">Thematic Alignment</a>
          <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Site Notic</a>
        </nav> -->
      </div>
      <div class="flex items-center gap-6">
        <div class="relative group hidden xl:block">
          <input class="bg-[#f5f7f9] border-none rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-primary w-80 transition-all shadow-inner" placeholder="Search safety records..." type="text"/>
          <span class="material-symbols-outlined absolute right-3 top-2 text-[#64748b]">search</span>
        </div>
        <div class="flex items-center gap-3">
          <button type="button" class="p-2 hover:bg-[#dfe3e6] rounded-full transition-colors relative">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-error border-2 border-white rounded-full"></span>
          </button>
          <button type="button" class="flex items-center gap-2 rounded-full border border-slate-200/80 bg-white p-1.5 pr-4 shadow-sm transition-colors hover:bg-slate-50">
            <span class="material-symbols-outlined text-3xl text-primary">account_circle</span>
            <div class="text-left">
              <p class="text-[10px] font-bold text-primary uppercase leading-none">Safety Admin</p>
              <p class="text-[9px] font-medium text-[#64748b]">Site Manager</p>
            </div>
          </button>
        </div>
      </div>
    </div>
  </header>

  <main class="flex w-full flex-grow flex-col pb-12 pt-6 sm:pt-8">
    <div class="layout-shell flex flex-col space-y-6 sm:space-y-8">
    <section class="page-hero">
      <div class="page-hero-inner">
        <div>
          <!-- <p class="page-hero-label">HIRA</p> -->
          <h1>HIRA Improvement Treatment Register</h1>
          <p class="subtitle">Dashboard, Input HIRA Detail, S-Curve Improvement, popup detail, dan matrix filtering</p>
          <div class="hero-strip" role="region" aria-label="Filter dan ringkasan HIRA">
            <!-- <div class="hero-filter-field">
              <label for="hiraHeroCompany">Perusahaan</label>
              <select id="hiraHeroCompany" class="hero-select" aria-label="Filter perusahaan">
                <option value="bm" selected>Bukit Makmur</option>
                <option value="all">Semua perusahaan</option>
                <option value="th">Thiess</option>
                <option value="pam">PAMA</option>
              </select>
            </div>
            <div class="hero-filter-field">
              <label for="hiraHeroYear">Periode Tahun</label>
              <select id="hiraHeroYear" class="hero-select" aria-label="Filter tahun">
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026" selected>2026</option>
              </select>
            </div> -->
            <!-- <article class="hero-stat-card">
              <div class="hero-stat-head">
                <span class="material-symbols-outlined hero-stat-ico" aria-hidden="true">assignment_turned_in</span>
                <span class="hero-stat-kicker">Improvement plan</span>
              </div>
              <div class="hero-stat-value">5</div>
              <p class="hero-stat-note m-0">Meng-cover 16 risiko</p>
            </article>
            <article class="hero-stat-card">
              <div class="hero-stat-head">
                <span class="material-symbols-outlined hero-stat-ico" aria-hidden="true">shield_person</span>
                <span class="hero-stat-kicker">Exposure ter-cover</span>
              </div>
              <div class="hero-stat-value">75.3%</div>
              <p class="hero-stat-note m-0">2.268 dari 3.013</p>
            </article> -->
          </div>
        </div>
        
      </div>
    </section>

    <div class="toolbar">
      <div class="tabs" id="tabs">
        <button class="active" type="button" data-view="overview">Overview</button>
        <button type="button" data-view="driver">Input HIRA Detail</button>
        <button type="button" data-view="correlation">S-Curve Improvement</button>
        <button type="button" data-view="action">Action Priority</button>
      </div>
      <!-- <div class="filters" data-page-only="overview">
        <select id="riskFilter" aria-label="Pilih Risk Status">
          <option value="ALL">Semua Status</option>
          <option value="High Risk">High Risk</option>
          <option value="Unstable">Unstable</option>
          <option value="Best Profile">Best Profile</option>
        </select>
      </div> -->
    </div>

    <section class="card page-intro" id="pageIntro"></section>
    <section class="risk-profile-board exec-summary-panel" data-page-only="overview" aria-labelledby="execSummaryHeading">
      <div class="exec-summary-top">
        <div class="card-title">
          <div>
            <h3 id="execSummaryHeading">Executive Summary</h3>
            <div class="muted small">Risk Treatment &amp; Improvement Impact</div>
          </div>
        </div>
        <div class="exec-summary-actions">
          <span class="badge green" id="riskProfileSiteLabel">All Site</span>
          <select id="riskProfileSiteFilter" class="risk-profile-site-filter" aria-label="Pilih site risk profiling"></select>
          <span class="badge blue">Data Weekly W01–W19 2026</span>
        </div>
      </div>

      <div class="exec-kpi-grid" role="list">
        <article class="exec-kpi-card" role="listitem">
          <div>
            <p class="exec-kpi-label">Improvement plan</p>
            <p class="exec-kpi-value" id="execSummaryImprovementPlans">5</p>
          </div>
          <p class="exec-kpi-sub"><span id="execSummaryRiskCoverLabel">Meng-cover</span> <span id="execSummaryRiskCoverCount">16</span> risiko</p>
        </article>
        <article class="exec-kpi-card" role="listitem">
          <div>
            <p class="exec-kpi-label">Risk movement</p>
            <p class="exec-kpi-value tabular-nums">
              <span id="execSummaryRiskFrom">213</span><span class="exec-kpi-arrow" aria-hidden="true">→</span><span id="execSummaryRiskTo">116</span>
            </p>
          </div>
          <p class="exec-kpi-sub">Selisih <span id="execSummaryRiskDelta">97</span> poin</p>
        </article>
        <article class="exec-kpi-card" role="listitem">
          <div>
            <p class="exec-kpi-label">Exposure covered</p>
            <p class="exec-kpi-value" id="execSummaryExposurePct">75.3%</p>
          </div>
          <p class="exec-kpi-sub"><span id="execSummaryExposureNum">2.268</span> dari <span id="execSummaryExposureDen">3.013</span></p>
        </article>
      </div>

      <div class="mgmt-highlight">
        <h4 class="mgmt-highlight-title">Management Highlight</h4>
        <div class="mgmt-highlight-body">
          <p>
            Total <strong id="mgmtHlPlans">5</strong> improvement plan meng-cover <strong id="mgmtHlRisks">16</strong> risiko, dengan pergerakan nilai risiko dari <strong id="mgmtHlRiskFrom">213</strong> menjadi <strong id="mgmtHlRiskTo">116</strong>.
          </p>
          <p>
            Exposure pengendalian sudah mencakup <strong id="mgmtHlExposurePct">75.3%</strong> dari exposure aktual sebelum pengendalian lanjutan.
          </p>
          <p>
            Prioritas perhatian: <strong id="mgmtHlPrioritySite">Interlock Seatbelt Unit HD dan LV</strong> karena residual tertinggi berada pada level <strong>High</strong> dan keputusan saat ini <strong>Belum Capai Target</strong>.
          </p>
        </div>
      </div>
    </section>

    <section class="card improvement-rekap-card" data-page-only="overview" aria-labelledby="improvementRekapHeading">
      <div class="card-title !mb-3">
        <div>
          <h2 id="improvementRekapHeading">Rekap per Improvement Plan</h2>
          <p class="muted small m-0 mt-1 max-w-[62rem] leading-relaxed">
            Klik baris untuk filter matriks. Klik icon mata untuk popup detail improvement.
          </p>
          <p id="improvementRekapFilterHint" class="improvement-rekap-filter-hint" hidden>
            <span>Matriks difilter: <strong data-filter-label></strong></span>
            <button type="button" id="improvementRekapClearFilter" class="!shadow-sm">Hapus filter</button>
          </p>
        </div>
      </div>
      <div class="improvement-rekap-wrap">
        <table class="improvement-rekap-table" id="improvementRekapTable">
          <thead>
            <tr>
              <th scope="col">Improvement Plan</th>
              <th scope="col">Rows</th>
              <th scope="col">Section</th>
              <th scope="col">Activity</th>
              <th scope="col">Sub Activity</th>
              <th scope="col">Sub-sub Activity</th>
              <th scope="col">Exposure</th>
              <th scope="col">Awal</th>
              <th scope="col">Sisa</th>
              <th scope="col">Decision</th>
              <th scope="col">View</th>
            </tr>
          </thead>
          <tbody>
            <tr data-plan-id="1" data-plan-title="Interlock Seatbelt Unit HD dan LV" tabindex="0" role="button" aria-label="Pilih improvement plan Interlock Seatbelt Unit HD dan LV">
              <td>Interlock Seatbelt Unit HD dan LV</td>
              <td class="improvement-rekap-num">4</td>
              <td class="improvement-rekap-num">3</td>
              <td class="improvement-rekap-num">2</td>
              <td class="improvement-rekap-num">2</td>
              <td class="improvement-rekap-num">2</td>
              <td class="improvement-rekap-exposure">81.2%</td>
              <td class="text-center"><span class="pill-risk pill-risk--significant">Significant</span></td>
              <td class="text-center"><span class="pill-risk pill-risk--high">High</span></td>
              <td class="text-center"><span class="pill-decision pill-decision--target">Belum Capai Target</span></td>
              <td class="text-center">
                <button type="button" class="improvement-rekap-view-btn" data-plan-view aria-label="Detail improvement Interlock Seatbelt Unit HD dan LV">
                  <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                </button>
              </td>
            </tr>
            <tr data-plan-id="2" data-plan-title="CCTV Analytic Workshop Area" tabindex="0" role="button" aria-label="Pilih improvement plan CCTV Analytic Workshop Area">
              <td>CCTV Analytic Workshop Area</td>
              <td class="improvement-rekap-num">3</td>
              <td class="improvement-rekap-num">2</td>
              <td class="improvement-rekap-num">2</td>
              <td class="improvement-rekap-num">1</td>
              <td class="improvement-rekap-num">1</td>
              <td class="improvement-rekap-exposure">95.5%</td>
              <td class="text-center"><span class="pill-risk pill-risk--high">High</span></td>
              <td class="text-center"><span class="pill-risk pill-risk--medium">Medium</span></td>
              <td class="text-center"><span class="pill-decision pill-decision--verify">Verifikasi Efektivitas</span></td>
              <td class="text-center">
                <button type="button" class="improvement-rekap-view-btn" data-plan-view aria-label="Detail improvement CCTV Analytic Workshop Area">
                  <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                </button>
              </td>
            </tr>
            <tr data-plan-id="3" data-plan-title="Speed Limit &amp; Geofencing Haul Road" tabindex="0" role="button" aria-label="Pilih improvement plan Speed Limit dan Geofencing Haul Road">
              <td>Speed Limit &amp; Geofencing Haul Road</td>
              <td class="improvement-rekap-num">5</td>
              <td class="improvement-rekap-num">4</td>
              <td class="improvement-rekap-num">3</td>
              <td class="improvement-rekap-num">2</td>
              <td class="improvement-rekap-num">1</td>
              <td class="improvement-rekap-exposure">72.4%</td>
              <td class="text-center"><span class="pill-risk pill-risk--medium">Medium</span></td>
              <td class="text-center"><span class="pill-risk pill-risk--medium">Medium</span></td>
              <td class="text-center"><span class="pill-decision pill-decision--coverage">Lanjutkan Coverage</span></td>
              <td class="text-center">
                <button type="button" class="improvement-rekap-view-btn" data-plan-view aria-label="Detail improvement Speed Limit dan Geofencing Haul Road">
                  <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                </button>
              </td>
            </tr>
            <tr data-plan-id="4" data-plan-title="Pre-start Checklist Alat Berat" tabindex="0" role="button" aria-label="Pilih improvement plan Pre-start Checklist Alat Berat">
              <td>Pre-start Checklist Alat Berat</td>
              <td class="improvement-rekap-num">2</td>
              <td class="improvement-rekap-num">2</td>
              <td class="improvement-rekap-num">1</td>
              <td class="improvement-rekap-num">1</td>
              <td class="improvement-rekap-num">1</td>
              <td class="improvement-rekap-exposure">88.0%</td>
              <td class="text-center"><span class="pill-risk pill-risk--high">High</span></td>
              <td class="text-center"><span class="pill-risk pill-risk--significant">Significant</span></td>
              <td class="text-center"><span class="pill-decision pill-decision--target">Belum Capai Target</span></td>
              <td class="text-center">
                <button type="button" class="improvement-rekap-view-btn" data-plan-view aria-label="Detail improvement Pre-start Checklist Alat Berat">
                  <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                </button>
              </td>
            </tr>
            <tr data-plan-id="5" data-plan-title="Housekeeping &amp; Aisles Marking Stockpile" tabindex="0" role="button" aria-label="Pilih improvement plan Housekeeping dan Aisles Marking Stockpile">
              <td>Housekeeping &amp; Aisles Marking Stockpile</td>
              <td class="improvement-rekap-num">3</td>
              <td class="improvement-rekap-num">3</td>
              <td class="improvement-rekap-num">2</td>
              <td class="improvement-rekap-num">2</td>
              <td class="improvement-rekap-num">2</td>
              <td class="improvement-rekap-exposure">69.8%</td>
              <td class="text-center"><span class="pill-risk pill-risk--significant">Significant</span></td>
              <td class="text-center"><span class="pill-risk pill-risk--high">High</span></td>
              <td class="text-center"><span class="pill-decision pill-decision--verify">Verifikasi Efektivitas</span></td>
              <td class="text-center">
                <button type="button" class="improvement-rekap-view-btn" data-plan-view aria-label="Detail improvement Housekeeping dan Aisles Marking Stockpile">
                  <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <section class="card risk-matrix-section" data-page-only="overview" aria-label="Matriks risiko awal dan sisa">
      @php
        $rmLevels = [
          'K5' => ['yellow', 'orange', 'orange', 'red', 'red'],
          'K4' => ['green', 'yellow', 'orange', 'orange', 'red'],
          'K3' => ['green', 'yellow', 'yellow', 'orange', 'orange'],
          'K2' => ['green', 'green', 'yellow', 'yellow', 'orange'],
          'K1' => ['green', 'green', 'green', 'green', 'yellow'],
        ];
        $rmRows = array_keys($rmLevels);
        $rmCols = ['C1', 'C2', 'C3', 'C4', 'C5'];
        $rmInitial = ['K4-C4' => 2, 'K4-C5' => 2, 'K3-C3' => 2, 'K3-C4' => 9, 'K3-C5' => 1];
        $rmResidual = ['K2-C3' => 2, 'K2-C4' => 10, 'K2-C5' => 1, 'K1-C4' => 1, 'K1-C5' => 2];
        $rmTipsAwal = [
          'K3-C3' => 'Aktivitas: seatbelt interlock unit HD/LV · Bahaya: pengemudi tidak terikat saat manuver.',
          'K3-C4' => 'Aktivitas: hauling bundar · Bahaya: interaksi pekerja dengan alat berat bergerak.',
          'K3-C5' => 'Aktivitas: dumping stockpile · Bahaya: tubrukan unit dan longsor material.',
          'K4-C4' => 'Aktivitas: workshop LV & HD · Bahaya: seatbelt tidak terkunci saat manuver terbatas.',
          'K4-C5' => 'Aktivitas: night shift maintenance · Bahaya: fatigued driving & komunikasi terbatas.',
        ];
        $rmTipsSisa = [
          'K2-C3' => 'Aktivitas: interlock seatbelt & checklist · Bahaya: bypass sensor / override tidak resmi.',
          'K2-C4' => 'Aktivitas: CCTV analytic area workshop · Bahaya: slips, trips, falls & peralatan tersimpan tidak rapi.',
          'K2-C5' => 'Aktivitas: stockpile marking · Bahaya: pejalan kaki vs alat angkat.',
          'K1-C4' => 'Aktivitas: pre-start checklist · Bahaya: komponen kritis tidak terinspeksi.',
          'K1-C5' => 'Aktivitas: geofencing haul road · Bahaya: overspeed di tikungan.',
        ];
      @endphp
      <div class="risk-matrix-dual">
        <article class="risk-matrix-panel">
          <h3 id="riskMatrixAwalHeading">Matriks Risiko Awal - Tanpa Pengendalian Improvement</h3>
          <p class="risk-matrix-sub m-0">Klik sel untuk menyorot posisi yang sama di matriks lain · Hover untuk aktivitas &amp; bahaya</p>
          <div class="risk-matrix-grid" role="grid" aria-labelledby="riskMatrixAwalHeading">
            <div class="risk-matrix-corner">K/C</div>
            @foreach ($rmCols as $c)
              <div class="risk-matrix-col-head">{{ $c }}</div>
            @endforeach
            @foreach ($rmRows as $rk)
              <div class="risk-matrix-row-head">{{ $rk }}</div>
              @foreach ($rmCols as $i => $col)
                @php
                  $cellKey = $rk . '-' . $col;
                  $lv = $rmLevels[$rk][$i];
                  $v = $rmInitial[$cellKey] ?? null;
                  $tip = $rmTipsAwal[$cellKey] ?? 'Tidak ada agregasi risiko di sel ' . $rk . ' × ' . $col . ' (matriks awal).';
                @endphp
                <button type="button" class="risk-matrix-cell risk-matrix-cell--{{ $lv }}" data-matrix="initial" data-k="{{ $rk }}" data-c="{{ $col }}" aria-label="Sel matriks awal {{ $rk }} {{ $col }}">
                  <span class="tooltip-wrap" data-tooltip="{{ e($tip) }}" tabindex="-1">@if ($v !== null){{ $v }}@endif</span>
                </button>
              @endforeach
            @endforeach
          </div>
        </article>
        <article class="risk-matrix-panel">
          <h3 id="riskMatrixSisaHeading">Matriks Risiko Sisa - Dengan Pengendalian Improvement</h3>
          <p class="risk-matrix-sub m-0">Klik sel untuk menyorot posisi yang sama di matriks lain · Hover untuk aktivitas &amp; bahaya</p>
          <div class="risk-matrix-grid" role="grid" aria-labelledby="riskMatrixSisaHeading">
            <div class="risk-matrix-corner">K/C</div>
            @foreach ($rmCols as $c)
              <div class="risk-matrix-col-head">{{ $c }}</div>
            @endforeach
            @foreach ($rmRows as $rk)
              <div class="risk-matrix-row-head">{{ $rk }}</div>
              @foreach ($rmCols as $i => $col)
                @php
                  $cellKey = $rk . '-' . $col;
                  $lv = $rmLevels[$rk][$i];
                  $v = $rmResidual[$cellKey] ?? null;
                  $tip = $rmTipsSisa[$cellKey] ?? 'Tidak ada agregasi risiko di sel ' . $rk . ' × ' . $col . ' (matriks sisa).';
                @endphp
                <button type="button" class="risk-matrix-cell risk-matrix-cell--{{ $lv }}" data-matrix="residual" data-k="{{ $rk }}" data-c="{{ $col }}" aria-label="Sel matriks sisa {{ $rk }} {{ $col }}">
                  <span class="tooltip-wrap" data-tooltip="{{ e($tip) }}" tabindex="-1">@if ($v !== null){{ $v }}@endif</span>
                </button>
              @endforeach
            @endforeach
          </div>
        </article>
      </div>
    </section>

  

    <div class="cards-wrap" data-page-only="overview">
      <section class="grid grid-site-cards" id="siteCards"></section>
      <div id="sitePopup" class="site-popup" aria-live="polite"></div>
    </div>

    <section class="section grid grid-2" data-section="overview">
      <div class="card">
        <div class="card-title">
          <div>
            <h2>Risk Score Site W18</h2>
            <div class="muted small">Ranking risk site dan stabilitas baseline berdasarkan grafik profiling.</div>
          </div>
          <span class="badge blue">Site Level</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Rank</th>
                <th>Site</th>
                <th>Risk W18</th>
                <th>Status</th>
                <th>Driver Ringkas</th>
                <th>Trend 4 Minggu</th>
              </tr>
            </thead>
            <tbody id="rankingTable"></tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <div class="card-title">
          <div>
            <h2>Kontribusi Driver Utama</h2>
            <div class="muted small">Agregasi driver kontraktor yang paling menjelaskan risk site.</div>
          </div>
          <span class="badge yellow">W15–W18</span>
        </div>
        <div class="driver-list" id="driverContribution"></div>
        <div class="footer-note">Pembobotan rekomendasi: Safety Accountability 35% mencakup Golden Rules, TBC, Blindspot, Ratio Pelaporan, dan Overdue Hazard; Coverage Quality 25% mencakup coverage weekly/daily dan area kritis; Exposure/RFID 20% adalah skor komposit berbasis RFID pengawas, RFID non-pengawas, rasio non-pengawas terhadap pengawas, dan perubahan exposure mingguan; Fatigue Management 20% mencakup true alert fatigue, FTW jam tidur kurang, dan speak up sebelum alert.</div>
      </div>
    </section>

    <section class="section grid grid-2" data-section="overview">
      <div class="card">
        <div class="card-title">
          <div>
            <h2>Scatter Korelasi</h2>
            <div class="muted small">Sumbu X: Contractor Driver Risk Index · Sumbu Y: Risk Score Site W18</div>
          </div>
          <span class="badge red">Prioritas</span>
        </div>
        <div class="scatter-box" id="scatterBox">
          <div class="scatter-axis-x"></div>
          <div class="scatter-axis-y"></div>
          <div class="quadrant-label q1">True High Risk</div>
          <div class="quadrant-label q2">Model Check</div>
          <div class="quadrant-label q3">Stable Control</div>
          <div class="quadrant-label q4">Latent Risk</div>
        </div>
        <div class="legend">
          <div class="legend-item"><span class="swatch bg-[#33a852]"></span>Benchmark</div>
          <div class="legend-item"><span class="swatch bg-[#f5bd24]"></span>Watch</div>
          <div class="legend-item"><span class="swatch bg-[#ef8a21]"></span>Unstable</div>
          <div class="legend-item"><span class="swatch bg-[#d92828]"></span>High Risk</div>
        </div>
      </div>

      <div class="card">
        <div class="card-title">
          <div>
            <h2>Detail Komponen Driver</h2>
            <div class="muted small">Definisi isi dari Safety Accountability, Coverage Quality, Exposure/RFID, dan Fatigue Management.</div>
          </div>
          <span class="badge green">Driver Dictionary</span>
        </div>
        <div class="summary-list" id="driverDictionary"></div>
      </div>
    </section>

    <section class="section card" data-section="driver" hidden>
      <div class="card-title">
        <div>
          <h2>Contractor Driver Heatmap</h2>
          <div class="muted small">Arahkan kursor ke setiap nilai untuk melihat penjelasan tanpa terpotong oleh tabel.</div>
        </div>
        <span class="badge orange">Driver Diagnostic</span>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Site</th>
              <th>Risk Site</th>
              <th>Safety Accountability</th>
              <th>Coverage Quality</th>
              <th>Exposure / RFID</th>
              <th>Fatigue Management</th>
              <th>Driver Index</th>
              <th>Detail Dominan</th>
              <th>Kesimpulan</th>
            </tr>
          </thead>
          <tbody id="heatmapTable"></tbody>
        </table>
      </div>
      <div class="legend">
        <div class="legend-item"><span class="swatch border border-[#9ad6a6] bg-[#e7f6ea]"></span>Low</div>
        <div class="legend-item"><span class="swatch border border-[#f5d875] bg-[#fff6d9]"></span>Medium</div>
        <div class="legend-item"><span class="swatch border border-[#f3b46f] bg-[#fff0df]"></span>High</div>
        <div class="legend-item"><span class="swatch border border-[#ed8b8b] bg-[#fde9e9]"></span>Critical</div>
      </div>
    </section>

    <section class="section grid grid-2" data-section="driver" hidden>
      <div class="card">
        <div class="card-title">
          <div>
            <h2>Trend Driver Mingguan</h2>
            <div class="muted small">Simulasi tren W15–W18 dari indikator yang ditandai merah pada dashboard kontraktor.</div>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Site</th>
                <th>Risk</th>
                <th>Driver Trend</th>
                <th>Momentum</th>
              </tr>
            </thead>
            <tbody id="trendTable"></tbody>
          </table>
        </div>
      </div>
      <div class="card">
        <div class="card-title">
          <div>
            <h2>Formula Index</h2>
            <div class="muted small">Struktur kalkulasi agar dashboard kontraktor bisa dijadikan driver risk site.</div>
          </div>
        </div>
        <div class="formula-box">Contractor Driver Risk Index =<br>&nbsp;&nbsp;35% Safety Accountability<br>+ 25% Coverage Quality<br>+ 20% Exposure / RFID<br>+ 20% Fatigue Management<br><br>Safety Accountability = GR + TBC + Blindspot + Ratio Pelaporan + Overdue Hazard<br>Coverage Quality = Coverage Weekly + Coverage Daily + Coverage Area Kritis<br>Exposure/RFID = RFID Pengawas + RFID Non Pengawas + Rasio Non Pengawas : Pengawas + perubahan exposure mingguan<br>Fatigue Management = True Alert Fatigue + FTW Jam Tidur Kurang + Speak Up Sebelum Alert</div>
      </div>
    </section>

    <section class="section grid grid-2" data-section="correlation" hidden>
      <div class="card">
        <div class="card-title">
          <div>
            <h2>Correlation Bridge Matrix</h2>
            <div class="muted small">Mekanisme membaca kecocokan risk site dengan driver kontraktor.</div>
          </div>
        </div>
        <div class="bridge">
          <div class="bridge-box critical">
            <h4>Risk Site Tinggi + Driver Merah</h4>
            <p><b>True High Risk.</b> Risiko site benar-benar didukung oleh sinyal kontraktor. Perlu intervensi langsung ke driver dominan.</p>
          </div>
          <div class="bridge-box watch">
            <h4>Risk Site Tinggi + Driver Hijau</h4>
            <p><b>Model / Data Check.</b> Perlu cek apakah ada driver belum masuk, data terlambat, atau exposure khusus yang belum terukur.</p>
          </div>
          <div class="bridge-box latent">
            <h4>Risk Site Rendah + Driver Merah</h4>
            <p><b>Latent Risk.</b> Belum terlihat pada risk score, tetapi dapat menjadi early warning untuk 1–4 minggu berikutnya.</p>
          </div>
          <div class="bridge-box safe">
            <h4>Risk Site Rendah + Driver Hijau</h4>
            <p><b>Benchmark.</b> Kontrol stabil dan dapat dijadikan referensi praktik untuk site lain.</p>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-title">
          <div>
            <h2>Narasi Eksekutif</h2>
            <div class="muted small">Ringkasan siap pakai untuk BOD/management.</div>
          </div>
          <span class="badge green">Ringkasan manajemen</span>
        </div>
        <p class="muted leading-[1.7] mt-0">Risk Score Site Week 18 menunjukkan LMO sebagai benchmark dengan profil risiko terendah dan paling stabil, sedangkan BMO 1 menjadi prioritas utama karena mengalami lonjakan risk score tertinggi. Jika dikorelasikan dengan dashboard level kontraktor, peningkatan risiko terutama dipengaruhi oleh kombinasi Safety Accountability, Blindspot TBC, Blindspot GR, penurunan Ratio Pengawas dengan TBC, Coverage Daily yang belum optimal, serta tekanan Fatigue Management. Dengan demikian, dashboard kontraktor berfungsi sebagai early driver diagnostic untuk menjelaskan penyebab risk score site, bukan hanya sebagai pelaporan indikator terpisah.</p>
        <div class="summary-list" id="bridgeSummary"></div>
      </div>
    </section>

    <section class="section grid grid-2" data-section="action" hidden>
      <div class="card">
        <div class="card-title">
          <div>
            <h2>Action Priority</h2>
            <div class="muted small">Prioritas intervensi berdasarkan risk site dan driver kontraktor.</div>
          </div>
          <span class="badge red">Execution</span>
        </div>
        <div class="action-card" id="actionList"></div>
      </div>
      <div class="card">
        <div class="card-title">
          <div>
            <h2>Management Control Focus</h2>
            <div class="muted small">Fokus kontrol yang direkomendasikan untuk 1–4 minggu ke depan.</div>
          </div>
        </div>
        <div class="summary-list">
          <div class="summary-item">
            <div class="rank">1</div>
            <div>
              <div class="strong">Validasi Blindspot Area</div>
              <div class="muted small">Cek area tidak tercover, area kritis, dan kualitas tindak lanjut TBC.</div>
            </div>
            <span class="badge red">Urgent</span>
          </div>
          <div class="summary-item">
            <div class="rank">2</div>
            <div>
              <div class="strong">Leadership Intervention</div>
              <div class="muted small">Gunakan Golden Rules dan Blindspot GR sebagai trigger eskalasi L2–L3.</div>
            </div>
            <span class="badge orange">High</span>
          </div>
          <div class="summary-item">
            <div class="rank">3</div>
            <div>
              <div class="strong">Coverage Quality Review</div>
              <div class="muted small">Tidak hanya mengejar coverage 100%, tetapi memastikan temuan kritis tertangkap.</div>
            </div>
            <span class="badge yellow">Watch</span>
          </div>
          <div class="summary-item">
            <div class="rank">4</div>
            <div>
              <div class="strong">Fatigue Management</div>
              <div class="muted small">Korelasikan alert fatigue dengan FTW jam tidur kurang dan exposure shift.</div>
            </div>
            <span class="badge blue">Monitor</span>
          </div>
        </div>
      </div>
    </section>

    <div class="footer-note">Catatan: angka dashboard ini disusun dari interpretasi visual risk profiling Week 18 dan dashboard kontraktor yang diberikan. Untuk implementasi final, hubungkan dengan data asli per kontraktor/site agar skor, tren, dan korelasi dapat dihitung otomatis.</div>
    @include('peer-pressure-edukasi.partials.peer-dashboard-wizard-nav', ['wizardStep' => 2])
    </div>
  </main>

  <div id="improvementDetailModal" class="improvement-detail-modal" hidden aria-modal="true" role="dialog" aria-labelledby="improvementDetailModalTitle">
    <div class="improvement-detail-modal__backdrop" data-close-improvement-modal tabindex="-1" aria-hidden="true"></div>
    <div class="improvement-detail-modal__panel">
      <button type="button" class="popup-close absolute right-4 top-4" aria-label="Tutup" data-close-improvement-modal>×</button>
      <h3 id="improvementDetailModalTitle"></h3>
      <div id="improvementDetailModalBody" class="improvement-detail-body muted"></div>
    </div>
  </div>

  @php
    $historicalRiskProfilePath = resource_path('views/peer-pressure-edukasi/dataJson/historical.json');
    $historicalRiskProfile = file_exists($historicalRiskProfilePath)
      ? collect(json_decode(file_get_contents($historicalRiskProfilePath), true) ?: [])
          ->filter(fn ($row) => (int) ($row['Tahun'] ?? 0) === 2026 && (int) ($row['Minggu'] ?? 0) >= 1 && (int) ($row['Minggu'] ?? 0) <= 19)
          ->values()
          ->all()
      : [];
    $predictionRiskProfilePath = resource_path('views/peer-pressure-edukasi/dataJson/prediksi.json');
    $predictionRiskProfile = file_exists($predictionRiskProfilePath)
      ? (json_decode(file_get_contents($predictionRiskProfilePath), true) ?: [])
      : [];
    $sitePerformancePath = resource_path('views/peer-pressure-edukasi/dataJson/siteperformance.json');
    $sitePerformanceRows = file_exists($sitePerformancePath)
      ? collect(json_decode(file_get_contents($sitePerformancePath), true) ?: [])
          ->filter(fn ($row) => (int) ($row['Year'] ?? 0) === 2026 && (int) ($row['Week'] ?? 0) === 19)
          ->values()
          ->all()
      : [];

    $scrIncidentWeek = strtoupper((string) request()->query('incident_week', 'W' . str_pad((string) now()->subWeek()->isoWeek(), 2, '0', STR_PAD_LEFT)));
    $scrIncidentYear = (string) request()->query('incident_year', now()->subWeek()->isoWeekYear());
    $scrIncidentRows = [];
    $roadStandardWeek = '19';
    $roadStandardRows = [];
    $coverageAreaKritisWeek = 'W19';
    $coverageAreaKritisRows = [];
    $blindspotTbcWeek = 'W19';
    $blindspotTbcRows = [];

    try {
      $scrIncidentTable = \Illuminate\Support\Facades\Schema::hasTable('scr_incident') ? 'scr_incident' : 'scr_insiden';
      $scrIncidentQuery = fn () => \Illuminate\Support\Facades\DB::table($scrIncidentTable)
        ->select(['Site', 'Perusahaan', 'Kategori_Incident', 'HIPO_NonHipo', 'Kronologi_Incident', 'Count_of_2025', 'ISO_Week_of_DATE', 'ISO_Year_of_DATE']);

      $rawScrIncidentRows = $scrIncidentQuery()
        ->whereRaw('TRIM(CAST(ISO_Year_of_DATE AS CHAR)) = ?', [$scrIncidentYear])
        ->whereRaw('UPPER(TRIM(CAST(ISO_Week_of_DATE AS CHAR))) = ?', [$scrIncidentWeek])
        ->get();

      if ($rawScrIncidentRows->isEmpty()) {
        $latestScrIncident = $scrIncidentQuery()
          ->whereNotNull('ISO_Year_of_DATE')
          ->whereNotNull('ISO_Week_of_DATE')
          ->orderByDesc('scraped_at')
          ->orderByDesc('id')
          ->first();

        if ($latestScrIncident) {
          $scrIncidentWeek = strtoupper(trim((string) $latestScrIncident->ISO_Week_of_DATE));
          $scrIncidentYear = trim((string) $latestScrIncident->ISO_Year_of_DATE);
          $rawScrIncidentRows = $scrIncidentQuery()
            ->whereRaw('TRIM(CAST(ISO_Year_of_DATE AS CHAR)) = ?', [$scrIncidentYear])
            ->whereRaw('UPPER(TRIM(CAST(ISO_Week_of_DATE AS CHAR))) = ?', [$scrIncidentWeek])
            ->get();
        }
      }

      $scrIncidentRows = $rawScrIncidentRows
        ->map(function ($row) {
          $count = is_numeric($row->Count_of_2025 ?? null) ? (int) $row->Count_of_2025 : 1;

          return [
            'site' => trim((string) ($row->Site ?? '')),
            'perusahaan' => trim((string) ($row->Perusahaan ?? '')),
            'kategori' => trim((string) ($row->Kategori_Incident ?? '')),
            'hipo' => trim((string) ($row->HIPO_NonHipo ?? '')),
            'kronologi' => trim((string) ($row->Kronologi_Incident ?? '')),
            'total' => max(1, $count),
          ];
        })
        ->values()
        ->all();
    } catch (\Throwable $exception) {
      $scrIncidentRows = [];
    }

    try {
      if (\Illuminate\Support\Facades\Schema::hasTable('scr_road_standard')) {
        $roadStandardRows = \Illuminate\Support\Facades\DB::table('scr_road_standard')
          ->select(['Site', 'Mitra_Kerja', 'Nilai_Numerik', 'Sub_sub_kategori', 'Week'])
          ->whereRaw('CAST(TRIM(CAST(Week AS CHAR)) AS UNSIGNED) BETWEEN 1 AND ?', [(int) $roadStandardWeek])
          ->where('Sub_sub_kategori', '% Road Standard')
          ->get()
          ->map(function ($row) {
            $rawValue = trim((string) ($row->Nilai_Numerik ?? ''));
            $normalizedValue = str_replace(',', '.', $rawValue);

            return [
              'site' => trim((string) ($row->Site ?? '')),
              'mitra' => trim((string) ($row->Mitra_Kerja ?? '')),
              'week' => (int) ($row->Week ?? 0),
              'value' => is_numeric($normalizedValue) ? (float) $normalizedValue : null,
              'raw' => $rawValue,
            ];
          })
          ->values()
          ->all();
      }
    } catch (\Throwable $exception) {
      $roadStandardRows = [];
    }

    try {
      if (\Illuminate\Support\Facades\Schema::hasTable('scr_coverage_area_kritis_daily')) {
        $coverageAreaKritisRows = \Illuminate\Support\Facades\DB::table('scr_coverage_area_kritis_daily')
          ->select(['ISO_Week_of_Date', 'Site', 'Year_of_Date', 'Avg_Coverage_Area_Kritis_Daily'])
          ->whereRaw('UPPER(TRIM(CAST(ISO_Week_of_Date AS CHAR))) = ?', [$coverageAreaKritisWeek])
          ->whereRaw('TRIM(CAST(Year_of_Date AS CHAR)) = ?', ['2026'])
          ->get()
          ->map(function ($row) {
            $rawValue = trim((string) ($row->Avg_Coverage_Area_Kritis_Daily ?? ''));
            $normalizedValue = str_replace(',', '.', $rawValue);

            return [
              'site' => trim((string) ($row->Site ?? '')),
              'week' => strtoupper(trim((string) ($row->ISO_Week_of_Date ?? ''))),
              'year' => trim((string) ($row->Year_of_Date ?? '')),
              'value' => is_numeric($normalizedValue) ? (float) $normalizedValue : null,
              'raw' => $rawValue,
            ];
          })
          ->values()
          ->all();
      }
    } catch (\Throwable $exception) {
      $coverageAreaKritisRows = [];
    }

    try {
      if (\Illuminate\Support\Facades\Schema::hasTable('scr_blindspot_tbc')) {
        $blindspotTbcRows = \Illuminate\Support\Facades\DB::table('scr_blindspot_tbc')
          ->select(['ISO_Week_of_Date_for_Join', 'perusahaan_pic', 'site', 'Year_of_Date_for_Join', 'Blindspot_TBC_dari_BC'])
          ->whereRaw("REPLACE(UPPER(TRIM(CAST(ISO_Week_of_Date_for_Join AS CHAR))), 'W', '') = ?", ['19'])
          ->whereRaw('TRIM(CAST(Year_of_Date_for_Join AS CHAR)) = ?', ['2026'])
          ->get()
          ->map(function ($row) {
            $rawValue = trim((string) ($row->Blindspot_TBC_dari_BC ?? ''));
            $normalizedValue = str_replace(',', '.', $rawValue);

            return [
              'site' => trim((string) ($row->site ?? '')),
              'perusahaanPic' => trim((string) ($row->perusahaan_pic ?? '')),
              'week' => strtoupper(trim((string) ($row->ISO_Week_of_Date_for_Join ?? ''))),
              'year' => trim((string) ($row->Year_of_Date_for_Join ?? '')),
              'value' => is_numeric($normalizedValue) ? (float) $normalizedValue : null,
              'raw' => $rawValue,
            ];
          })
          ->values()
          ->all();
      }
    } catch (\Throwable $exception) {
      $blindspotTbcRows = [];
    }
  @endphp
  <script>
    const historicalRiskProfileData = @json($historicalRiskProfile);
    const predictionRiskProfileData = @json($predictionRiskProfile);
    const sitePerformanceRows = @json($sitePerformanceRows);
    const scrIncidentWeek = @json($scrIncidentWeek);
    const scrIncidentYear = @json($scrIncidentYear);
    const scrIncidentRows = @json($scrIncidentRows);
    const roadStandardWeek = @json($roadStandardWeek);
    const roadStandardRows = @json($roadStandardRows);
    const coverageAreaKritisWeek = @json($coverageAreaKritisWeek);
    const coverageAreaKritisRows = @json($coverageAreaKritisRows);
    const blindspotTbcWeek = @json($blindspotTbcWeek);
    const blindspotTbcRows = @json($blindspotTbcRows);
    const sites = [
      {
        site: "BMO 1",
        score: 39.47,
        status: "High Risk",
        delta: "+17.4",
        color: "red",
        driverIndex: 83,
        safety: 90,
        coverage: 68,
        exposure: 76,
        fatigue: 72,
        exposureBreakdown: { load: 75, ratio: 78, control: 80, momentum: 70 },
        sourceMetrics: { rfidPengawas: 557, rfidNonPengawas: 1499, gr: 2, tbc: 338, blindspot: 2, blindspotGR: 1, ratioSAP: 22.03, ratioTBC: 0.66, coverageWeekly: 99.07, coverageDaily: 75.1, fatigueAlert: 12, ftwJamTidurKurang: 14 },
        trend: [31, 28, 34, 39],
        summary: "Lonjakan risk score tertinggi. Driver kontraktor menunjukkan kombinasi GR meningkat, blindspot muncul, ratio TBC turun, coverage daily belum kuat, dan fatigue alert kembali naik.",
        actions: ["Audit area blindspot dan validasi exposure pengawas vs non-pengawas.", "Eskalasi Golden Rules dan Blindspot GR ke mekanisme L2-L3.", "Review kualitas coverage daily, bukan hanya angka coverage weekly.", "Lakukan fatigue intervention pada kontraktor dengan alert naik."],
        quadrant: "True High Risk"
      },
      {
        site: "BMO 2",
        score: 20.70,
        status: "Unstable",
        delta: "+0.6",
        color: "orange",
        driverIndex: 58,
        safety: 54,
        coverage: 60,
        exposure: 62,
        fatigue: 75,
        exposureBreakdown: { load: 80, ratio: 85, control: 30, momentum: 35 },
        sourceMetrics: { rfidPengawas: 696, rfidNonPengawas: 2821, gr: 0, tbc: 1307, blindspot: 0, blindspotGR: 0, ratioSAP: 15.42, ratioTBC: 1.91, coverageWeekly: 99.10, coverageDaily: 67.0, fatigueAlert: 35, ftwJamTidurKurang: 46 },
        trend: [23, 21, 20, 21],
        summary: "Risk belum ekstrem, tetapi baseline tidak stabil karena fatigue alert sempat sangat tinggi dan coverage belum konsisten.",
        actions: ["Fokus pada fatigue alert dan FTW jam tidur kurang.", "Pastikan coverage weekly konsisten dan tidak turun di area kritis.", "Monitor rasio pelaporan TBC agar tidak melemah."],
        quadrant: "Latent Risk"
      },
      {
        site: "BMO 3",
        score: 23.02,
        status: "Unstable",
        delta: "+1.2",
        color: "orange",
        driverIndex: 62,
        safety: 64,
        coverage: 58,
        exposure: 55,
        fatigue: 50,
        exposureBreakdown: { load: 45, ratio: 75, control: 50, momentum: 50 },
        sourceMetrics: { rfidPengawas: 108, rfidNonPengawas: 420, gr: 0, tbc: 231, blindspot: 2, blindspotGR: 0, ratioSAP: 20.90, ratioTBC: 1.93, coverageWeekly: 96.88, coverageDaily: 81.6, fatigueAlert: 1, ftwJamTidurKurang: 0 },
        trend: [24, 22, 21, 23],
        summary: "Unstable karena indikator accountability dan exposure belum stabil: Golden Rules fluktuatif, blindspot muncul, dan coverage daily masih perlu diperkuat.",
        actions: ["Stabilkan Golden Rules control dan tindak lanjut temuan kritis.", "Perkuat coverage daily pada area kerja dengan exposure tinggi.", "Pantau blindspot agar tidak berubah menjadi latent risk."],
        quadrant: "Latent Risk"
      },
      {
        site: "GMO",
        score: 21.98,
        status: "High Risk",
        delta: "+2.7",
        color: "red",
        driverIndex: 74,
        safety: 78,
        coverage: 63,
        exposure: 70,
        fatigue: 84,
        exposureBreakdown: { load: 75, ratio: 80, control: 70, momentum: 50 },
        sourceMetrics: { rfidPengawas: 587, rfidNonPengawas: 2347, gr: 0, tbc: 1482, blindspot: 7, blindspotGR: 0, ratioSAP: 24.29, ratioTBC: 2.33, coverageWeekly: 99.09, coverageDaily: 79.6, fatigueAlert: 13, ftwJamTidurKurang: 86 },
        trend: [23, 19, 20, 22],
        summary: "Risk didorong oleh kombinasi blindspot, fatigue spike yang melonjak, dan leadership signal. Fatigue GMO diklasifikasikan Critical karena ada lonjakan tajam, bukan sekadar level absolut.",
        actions: ["Validasi penyebab peningkatan blindspot dan GR.", "Treat GMO fatigue sebagai Critical spike: cek jam tidur kurang, pola shift, dan exposure pekerja.", "Pastikan TBC yang turun bukan karena under-reporting."],
        quadrant: "True High Risk"
      },
      {
        site: "SMO",
        score: 20.80,
        status: "High Risk",
        delta: "+1.4",
        color: "red",
        driverIndex: 70,
        safety: 82,
        coverage: 42,
        exposure: 48,
        fatigue: 40,
        exposureBreakdown: { load: 54, ratio: 70, control: 35, momentum: 20 },
        sourceMetrics: { rfidPengawas: 456, rfidNonPengawas: 1507, gr: 1, tbc: 628, blindspot: 13, blindspotGR: 0, ratioSAP: 22.41, ratioTBC: 1.52, coverageWeekly: 99.53, coverageDaily: 90.7, fatigueAlert: 1, ftwJamTidurKurang: 3 },
        trend: [18, 17, 19, 21],
        summary: "Coverage relatif tinggi, namun blindspot tetap menonjol. Ini menunjukkan isu kualitas kontrol, bukan hanya kuantitas coverage.",
        actions: ["Lakukan review kualitas temuan dan tindak lanjut blindspot.", "Bandingkan area yang sering dicek vs area yang menghasilkan TBC.", "Gunakan SMO sebagai contoh kasus coverage tinggi tetapi risk belum turun."],
        quadrant: "True High Risk"
      },
      {
        site: "LMO",
        score: 19.64,
        status: "Best Profile",
        delta: "-0.8",
        color: "green",
        driverIndex: 38,
        safety: 35,
        coverage: 38,
        exposure: 45,
        fatigue: 28,
        exposureBreakdown: { load: 70, ratio: 60, control: 20, momentum: 10 },
        sourceMetrics: { rfidPengawas: 800, rfidNonPengawas: 2733, gr: 0, tbc: 1857, blindspot: 0, blindspotGR: 0, ratioSAP: 19.63, ratioTBC: 2.38, coverageWeekly: 97.67, coverageDaily: 83.2, fatigueAlert: 64, ftwJamTidurKurang: 2 },
        trend: [20, 20, 19, 20],
        summary: "Benchmark karena risk profile paling rendah dan driver kontraktor relatif terkendali: blindspot rendah, GR terkendali, dan coverage relatif stabil.",
        actions: ["Dokumentasikan praktik kontrol LMO sebagai benchmark.", "Gunakan baseline LMO untuk pembanding site high risk.", "Pertahankan stabilitas coverage dan low blindspot."],
        quadrant: "Stable Control"
      }
    ];

    const exposureWeights = {
      load: 0.30,
      ratio: 0.30,
      control: 0.25,
      momentum: 0.15
    };

    function calculateExposureScore(siteData) {
      const item = siteData.exposureBreakdown;
      if (!item) return siteData.exposure;
      return Math.round(
        item.load * exposureWeights.load +
        item.ratio * exposureWeights.ratio +
        item.control * exposureWeights.control +
        item.momentum * exposureWeights.momentum
      );
    }

    sites.forEach(siteData => {
      siteData.exposure = calculateExposureScore(siteData);
    });

    const pageMeta = {
      overview: {
        title: "Executive Summary",
        description: "Risk Treatment & Improvement Impact",
        index: "1/4"
      },
      driver: {
        title: "Driver Kontraktor",
        description: "Halaman ini fokus pada heatmap empat driver utama: Safety Accountability, Coverage Quality, Exposure/RFID, dan Fatigue Management, termasuk detail dominan per site.",
        index: "2/4"
      },
      correlation: {
        title: "Correlation Bridge",
        description: "Halaman ini menjelaskan hubungan antara risk score site dan driver kontraktor untuk membedakan true high risk, latent risk, model check, dan benchmark.",
        index: "3/4"
      },
      action: {
        title: "Action Priority",
        description: "Halaman ini menampilkan urutan prioritas intervensi dan fokus kontrol manajemen untuk 1-4 minggu ke depan.",
        index: "4/4"
      }
    };

    const driverNames = [
      ["Safety Accountability", "safety"],
      ["Coverage Quality", "coverage"],
      ["Exposure / RFID", "exposure"],
      ["Fatigue Management", "fatigue"]
    ];

    const driverDefinitions = {
      safety: {
        label: "Safety Accountability",
        weight: "35%",
        note: "Mengukur kualitas tanggung jawab keselamatan dan kemampuan organisasi menangkap serta menindaklanjuti sinyal risiko kritis.",
        components: ["Golden Rules", "TBC", "Blindspot TBC", "Blindspot GR", "Ratio Pengawas dengan SAP", "Ratio Pengawas dengan TBC", "Overdue Hazard", "PSPP/GR"]
      },
      coverage: {
        label: "Coverage Quality",
        weight: "25%",
        note: "Mengukur apakah aktivitas pengawasan benar-benar menjangkau area yang tepat, rutin, dan area kritis, bukan hanya memenuhi angka coverage total.",
        components: ["Coverage Weekly", "Coverage Daily", "Coverage Area All", "Coverage Area Kritis", "Konsistensi coverage", "Gap area berulang", "Kualitas follow-up temuan"]
      },
      exposure: {
        label: "Exposure / RFID",
        weight: "20%",
        note: "Skor komposit 0-100 untuk membaca paparan pekerja dari RFID. Nilai dihitung dari Exposure Load, Rasio Non Pengawas : Pengawas, Gap Pengawas, dan Momentum WoW.",
        components: ["RFID Pengawas", "RFID Non Pengawas", "Rasio Non Pengawas : Pengawas", "Jumlah pekerja terekspos", "Perubahan exposure mingguan", "Dominasi kontraktor/site tertentu"]
      },
      fatigue: {
        label: "Fatigue Management",
        weight: "20%",
        note: "Mengukur tekanan kelelahan pekerja dan kemampuan kontrol fatigue sebelum berubah menjadi unsafe behavior atau incident precursor.",
        components: ["True Alert Fatigue", "FTW Jam Tidur Kurang", "Speak Up Sebelum Alert", "Spike alert", "Alert berulang", "Pola shift dan jam kerja", "Exposure fatigue per kontraktor"]
      }
    };

    const historicalWeekLimit = 19;
    const historicalTooltipFields = [
      "Site",
      "Tahun",
      "Minggu",
      "Incident",
      "Pelanggaran GR",
      "Pelanggaran PSPP",
      "Pemenuhan SAP All Pengawas",
      "Ratio SAP Pengawas Layer 3 up",
      "Average Daily Coverage All Area",
      "Average Daily Coverage Area Kritis",
      "True Alert Fatigue",
      "Ratio Pelaporan TBC oleh Pengawas",
      "% Laporan Pengawasan Berjarak Real Time (All Layer)",
      "% Laporan Pengawasan Berjarak Post Event (L2 up)",
      "% Alert Intervented",
      "% Alert Intervented on Time",
      "% Road Standard",
      "Avg Time to Repair",
      "% Pencapaian Improvement Teknologi YTD",
      "% Kesesuaian IPK OKK",
      "Golden Time",
      "Ratio Pelaporan CCV",
      "% Closing Rekomendasi Investigasi On Time",
      "Jumlah Blindspot TBC",
      "Jumlah Blindspot GR",
      "Jumlah Overdue Hazard",
      "Jumlah Speak Up Setelah Alert",
      "Pekerja dengan Jam Tidur Kurang",
      "Ratio Kelayakan Kerja hasil MCU",
      "Persentase Pekerja Baru",
      "Pattern Similarity",
      "Jumlah Detail Lokasi Area Kritis",
      "Jumlah IKK",
      "Jumlah Pengawas masuk",
      "Jumlah Non Pengawas masuk",
      "Jumlah Unit beroperasi based on DMS",
      "Rekomendasi Rekayasa Engineering",
      "% Emergency Equipment Availability",
      "% Investigasi kurang dari 5 hari"
    ];

    let selectedSite = "BMO 1";
    let selectedRiskProfileSite = "ALL";
    let improvementRekapSelectedId = null;
    let selectedRiskMatrixCell = null;
    let activeView = "overview";
    let riskProfileMainChartInstance = null;
    let riskProfileMiniChartInstances = [];

    const riskFilter = document.getElementById("riskFilter");
    const riskProfileSiteFilter = document.getElementById("riskProfileSiteFilter");
    const riskProfileSiteLabel = document.getElementById("riskProfileSiteLabel");
    const siteCards = document.getElementById("siteCards");
    const sitePopup = document.getElementById("sitePopup");

    function escapeHtml(value) {
      return String(value).replace(/[&<>"']/g, char => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        "\"": "&quot;",
        "'": "&#039;"
      }[char]));
    }

    function setupRiskProfileSiteFilter() {
      if (!riskProfileSiteFilter) return;
      riskProfileSiteFilter.innerHTML = `
        <option value="ALL">All Site</option>
        ${sites.map(siteData => `<option value="${escapeHtml(siteData.site)}">${escapeHtml(siteData.site)}</option>`).join("")}
      `;
      riskProfileSiteFilter.value = selectedRiskProfileSite;
    }

    function cssClass(value) {
      return String(value).toLowerCase().replace(/[^a-z0-9_-]/g, "-");
    }

    function pctClass(value) {
      if (value >= 80) return "red";
      if (value >= 65) return "orange";
      if (value >= 50) return "yellow";
      return "green";
    }

    function riskBadge(status) {
      if (status === "High Risk") return "red";
      if (status === "Unstable") return "orange";
      return "green";
    }

    function scoreLevel(value) {
      if (value >= 80) return "Critical";
      if (value >= 65) return "High";
      if (value >= 50) return "Medium";
      return "Low";
    }

    function heatClass(level) {
      return {
        Low: "heat-low",
        Medium: "heat-med",
        High: "heat-high",
        Critical: "heat-critical"
      }[level] || "heat-low";
    }

    function getSite(siteName) {
      return sites.find(item => item.site === siteName) || sites[0];
    }

    function applyFilters(source = sites) {
      const statusFilter = riskFilter?.value ?? "ALL";
      return source.filter(item => statusFilter === "ALL" || item.status === statusFilter);
    }

    function renderCards() {
      const filtered = applyFilters();
      siteCards.innerHTML = filtered.map(siteData => `
        <article class="card score-card ${selectedSite === siteData.site ? "selected" : ""}" data-site="${escapeHtml(siteData.site)}">
          <div class="score-top">
            <div>
              <h3 class="site-name">${escapeHtml(siteData.site)}</h3>
              <span class="badge ${riskBadge(siteData.status)}">${escapeHtml(siteData.status)}</span>
            </div>
            <span class="pill-status" style="background: var(--${siteData.color}-soft); color: var(--${siteData.color});">${escapeHtml(siteData.delta)}%</span>
          </div>
          <div class="risk-score">${siteData.score.toFixed(2)}<span class="small muted">%</span></div>
          <div class="mini-bars">
            ${driverNames.map(([label, key]) => `
              <div class="metric-line">
                <span>${escapeHtml(label.split(" ")[0])}</span>
                <div class="bar-bg"><div class="bar-fill ${pctClass(siteData[key])}" style="width:${siteData[key]}%"></div></div>
                <b>${siteData[key]}</b>
              </div>
            `).join("")}
          </div>
        </article>
      `).join("");
    }

    function renderDriverDictionary() {
      document.getElementById("driverDictionary").innerHTML = Object.values(driverDefinitions).map(driver => `
        <div class="driver-detail-card">
          <div class="driver-detail-title">
            <span>${escapeHtml(driver.label)}</span>
            <span class="badge blue">${escapeHtml(driver.weight)}</span>
          </div>
          <div class="driver-detail-note">${escapeHtml(driver.note)}</div>
          <div class="detail-chip-row">
            ${driver.components.slice(0, 5).map(component => `<span class="detail-chip">${escapeHtml(component)}</span>`).join("")}
          </div>
        </div>
      `).join("");
    }

    function driverDetailBlock(siteData, key) {
      const driver = driverDefinitions[key];
      return `
        <div class="driver-detail-card">
          <div class="driver-detail-title">
            <span>${escapeHtml(driver.label)}</span>
            <span class="badge ${pctClass(siteData[key])}">${escapeHtml(driver.weight)}</span>
          </div>
          <div class="driver-detail-note">${escapeHtml(driver.note)}</div>
          <div class="detail-chip-row">
            ${driver.components.map(component => `<span class="detail-chip">${escapeHtml(component)}</span>`).join("")}
          </div>
        </div>
      `;
    }

    function renderSitePopup() {
      const selected = getSite(selectedSite);
      sitePopup.innerHTML = `
        <div class="popup-head">
          <div>
            <h3 class="m-0 text-xl tracking-[-.03em]">Selected Site Insight · ${escapeHtml(selected.site)}</h3>
            <div class="muted small">Klik kartu site lain untuk berpindah.</div>
          </div>
          <button class="popup-close" type="button" aria-label="Tutup" data-close-popup="true">×</button>
        </div>
        <div class="popup-grid">
          <div class="stat-tile">
            <div class="muted small">Risk Score W18</div>
            <div class="num">${selected.score.toFixed(2)}%</div>
            <span class="badge ${riskBadge(selected.status)}">${escapeHtml(selected.status)}</span>
          </div>
          <div class="stat-tile">
            <div class="muted small">Driver Index</div>
            <div class="num">${selected.driverIndex}</div>
            <span class="badge ${pctClass(selected.driverIndex)}">${escapeHtml(selected.quadrant)}</span>
          </div>
        </div>
        <p class="muted my-3.5 leading-[1.65]">${escapeHtml(selected.summary)}</p>
        <div class="driver-list">
          ${driverNames.map(([label, key]) => `
            <div class="driver-row">
              <b>${escapeHtml(label)}</b>
              <div class="bar-bg"><div class="bar-fill ${pctClass(selected[key])}" style="width:${selected[key]}%"></div></div>
              <b>${selected[key]}</b>
              <div class="driver-mini-note">${escapeHtml(sourceMetricText(selected, key))}</div>
            </div>
          `).join("")}
        </div>

      `;

      const card = Array.from(document.querySelectorAll(".score-card")).find(element => element.dataset.site === selected.site);
      const wrapper = document.querySelector(".cards-wrap");
      if (!card || !wrapper || activeView !== "overview") {
        closeSitePopup();
        return;
      }

      sitePopup.classList.add("show");
      if (window.matchMedia("(max-width: 980px)").matches) {
        sitePopup.style.left = "14px";
        sitePopup.style.top = "";
        sitePopup.style.visibility = "visible";
        sitePopup.style.setProperty("--popup-arrow-left", "34px");
        return;
      }

      sitePopup.style.visibility = "hidden";
      sitePopup.style.left = "0px";
      sitePopup.style.top = "0px";

      const wrapperRect = wrapper.getBoundingClientRect();
      const cardRect = card.getBoundingClientRect();
      const popupWidth = sitePopup.offsetWidth || 430;
      const popupHeight = sitePopup.offsetHeight || 300;
      let left = cardRect.left - wrapperRect.left + cardRect.width / 2 - popupWidth / 2;
      left = Math.max(0, Math.min(left, wrapperRect.width - popupWidth));
      let top = cardRect.bottom - wrapperRect.top + 14;
      const availableBelow = window.innerHeight - cardRect.bottom - 24;
      if (availableBelow < popupHeight && cardRect.top > popupHeight + 24) {
        top = cardRect.top - wrapperRect.top - popupHeight - 14;
      }
      const arrowLeft = Math.max(24, Math.min(cardRect.left - wrapperRect.left + cardRect.width / 2 - left - 9, popupWidth - 34));
      sitePopup.style.left = `${left}px`;
      sitePopup.style.top = `${Math.max(0, top)}px`;
      sitePopup.style.visibility = "visible";
      sitePopup.style.setProperty("--popup-arrow-left", `${arrowLeft}px`);
    }

    function closeSitePopup() {
      sitePopup.classList.remove("show");
    }

    function closeImprovementDetailModal() {
      const modal = document.getElementById("improvementDetailModal");
      if (modal) modal.hidden = true;
      document.body.style.overflow = "";
    }

    function openImprovementDetailModal(title) {
      const modal = document.getElementById("improvementDetailModal");
      const titleEl = document.getElementById("improvementDetailModalTitle");
      const bodyEl = document.getElementById("improvementDetailModalBody");
      if (!modal || !titleEl || !bodyEl) return;
      const label = title || "Improvement plan";
      titleEl.textContent = label;
      const safe = escapeHtml(label);
      bodyEl.innerHTML = `
        <p class="m-0">Ringkasan treatment, milestone, PIC, dan evidence dapat ditampilkan di sini setelah terhubung ke API.</p>
        <p class="m-0"><strong>Plan:</strong> ${safe}</p>
      `;
      modal.hidden = false;
      document.body.style.overflow = "hidden";
    }

    function clearImprovementPlanFilter() {
      const tbody = document.querySelector("#improvementRekapTable tbody");
      const hint = document.getElementById("improvementRekapFilterHint");
      tbody?.querySelectorAll("tr.is-selected").forEach(row => row.classList.remove("is-selected"));
      improvementRekapSelectedId = null;
      if (hint) hint.hidden = true;
      window.dispatchEvent(new CustomEvent("improvement-plan-filter", { detail: { id: null, title: null } }));
    }

    function applyImprovementPlanFilter(row) {
      const tbody = document.querySelector("#improvementRekapTable tbody");
      const hint = document.getElementById("improvementRekapFilterHint");
      const label = hint?.querySelector("[data-filter-label]");
      const id = row.dataset.planId;
      if (improvementRekapSelectedId === id) {
        clearImprovementPlanFilter();
        return;
      }
      tbody?.querySelectorAll("tr.is-selected").forEach(r => r.classList.remove("is-selected"));
      row.classList.add("is-selected");
      improvementRekapSelectedId = id;
      if (hint && label) {
        label.textContent = row.dataset.planTitle || "";
        hint.hidden = false;
      }
      window.dispatchEvent(new CustomEvent("improvement-plan-filter", { detail: { id, title: row.dataset.planTitle } }));
    }

    function bindImprovementRekapTable() {
      const tbody = document.querySelector("#improvementRekapTable tbody");
      if (!tbody) return;
      tbody.addEventListener("click", event => {
        if (event.target.closest("button[data-plan-view]")) {
          event.stopPropagation();
          const row = event.target.closest("tr[data-plan-id]");
          openImprovementDetailModal(row?.dataset.planTitle);
          return;
        }
        const row = event.target.closest("tr[data-plan-id]");
        if (!row) return;
        applyImprovementPlanFilter(row);
      });
      tbody.addEventListener("keydown", event => {
        if (event.key !== "Enter" && event.key !== " ") return;
        if (event.target.closest("button[data-plan-view]")) return;
        const row = event.target.closest("tr[data-plan-id]");
        if (!row) return;
        event.preventDefault();
        applyImprovementPlanFilter(row);
      });
      document.getElementById("improvementRekapClearFilter")?.addEventListener("click", () => clearImprovementPlanFilter());
      document.querySelectorAll("[data-close-improvement-modal]").forEach(element => {
        element.addEventListener("click", () => closeImprovementDetailModal());
      });
      document.addEventListener("keydown", event => {
        if (event.key !== "Escape") return;
        const modal = document.getElementById("improvementDetailModal");
        if (!modal || modal.hidden) return;
        closeImprovementDetailModal();
      });
    }

    function clearRiskMatrixSelection() {
      const dual = document.querySelector(".risk-matrix-dual");
      selectedRiskMatrixCell = null;
      dual?.querySelectorAll(".risk-matrix-cell.is-selected").forEach(el => el.classList.remove("is-selected"));
    }

    function bindRiskMatrixCells() {
      const dual = document.querySelector(".risk-matrix-dual");
      if (!dual) return;
      dual.addEventListener("click", event => {
        const cell = event.target.closest(".risk-matrix-cell[data-k]");
        if (!cell) return;
        const { k, c, matrix } = cell.dataset;
        if (!k || !c) return;
        const pair = `${k}-${c}`;
        if (selectedRiskMatrixCell === pair) {
          clearRiskMatrixSelection();
          window.dispatchEvent(new CustomEvent("risk-matrix-cell-select", { detail: { k, c, matrix: null } }));
          return;
        }
        selectedRiskMatrixCell = pair;
        dual.querySelectorAll(".risk-matrix-cell.is-selected").forEach(el => el.classList.remove("is-selected"));
        dual.querySelectorAll(`.risk-matrix-cell[data-k="${k}"][data-c="${c}"]`).forEach(el => el.classList.add("is-selected"));
        window.dispatchEvent(new CustomEvent("risk-matrix-cell-select", { detail: { k, c, matrix: matrix || null } }));
      });
    }

    function renderRanking() {
      const ranked = [...sites].sort((a, b) => b.score - a.score);
      document.getElementById("rankingTable").innerHTML = ranked.map((siteData, index) => `
        <tr>
          <td><span class="rank">${index + 1}</span></td>
          <td><b>${escapeHtml(siteData.site)}</b></td>
          <td><b>${siteData.score.toFixed(2)}%</b> <span class="muted small">(${escapeHtml(siteData.delta)}%)</span></td>
          <td><span class="badge ${riskBadge(siteData.status)}">${escapeHtml(siteData.status)}</span></td>
          <td>${escapeHtml(siteData.summary)}</td>
          <td>${sparkline(siteData.trend, siteData.color)}</td>
        </tr>
      `).join("");
    }

    function sparkline(values, colorKey = "green") {
      const max = Math.max(...values) + 3;
      const min = Math.min(...values) - 3;
      const width = 120;
      const height = 38;
      const span = Math.max(max - min, 1);
      const step = width / Math.max(values.length - 1, 1);
      const points = values.map((value, index) => `${index * step},${height - ((value - min) / span) * height}`).join(" ");
      const cssVar = `var(--${colorKey})`;
      const circles = values.map((value, index) => {
        const x = index * step;
        const y = height - ((value - min) / span) * height;
        return `<circle cx="${x}" cy="${y}" r="3" fill="${cssVar}" />`;
      }).join("");
      return `<svg class="sparkline" viewBox="0 0 ${width} ${height}" preserveAspectRatio="none"><polyline points="${points}" fill="none" stroke="${cssVar}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />${circles}</svg>`;
    }

    function renderScatter() {
      const box = document.getElementById("scatterBox");
      box.querySelectorAll(".point").forEach(point => point.remove());
      sites.forEach(siteData => {
        const x = 8 + (siteData.driverIndex / 100) * 86;
        const y = 90 - ((siteData.score - 15) / 30) * 78;
        const point = document.createElement("button");
        point.type = "button";
        point.className = "point";
        point.dataset.site = siteData.site;
        point.style.left = `${Math.max(8, Math.min(94, x))}%`;
        point.style.top = `${Math.max(10, Math.min(88, y))}%`;
        point.style.background = `var(--${siteData.color})`;
        point.innerHTML = `${escapeHtml(siteData.site.replace("BMO ", "B"))}<span class="point-label">${siteData.score.toFixed(1)}% · DI ${siteData.driverIndex}</span>`;
        point.title = `${siteData.site}: ${siteData.quadrant}`;
        box.appendChild(point);
      });
    }

    function renderDriverContribution() {
      const averages = driverNames.map(([label, key]) => {
        const value = Math.round(sites.reduce((sum, siteData) => sum + siteData[key], 0) / sites.length);
        return { label, key, value };
      }).sort((a, b) => b.value - a.value);
      document.getElementById("driverContribution").innerHTML = averages.map(driver => `
        <div class="driver-detail-card">
          <div class="driver-row">
            <b>${escapeHtml(driver.label)}</b>
            <div class="bar-bg"><div class="bar-fill ${pctClass(driver.value)}" style="width:${driver.value}%"></div></div>
            <b>${driver.value}</b>
          </div>
          <div class="driver-detail-note">${escapeHtml(driverDefinitions[driver.key].note)}</div>
          <div class="detail-chip-row">
            ${driverDefinitions[driver.key].components.slice(0, 6).map(component => `<span class="detail-chip">${escapeHtml(component)}</span>`).join("")}
          </div>
        </div>
      `).join("");
    }

    function withTooltip(content, text) {
      return `<span class="tooltip-wrap" data-tooltip="${escapeHtml(text)}">${content}<span class="help-dot">i</span></span>`;
    }

    function withCellTooltip(content, text) {
      return `<span class="tooltip-wrap" data-tooltip="${escapeHtml(text)}">${content}</span>`;
    }

    function withCellHtmlTooltip(content, html) {
      return `<span class="tooltip-wrap" data-tooltip-html="${escapeHtml(html)}">${content}</span>`;
    }

    function parseSitePerformanceNumber(value) {
      if (value === null || value === undefined || value === "" || value === "N/A") return null;
      const number = Number(String(value).replace(",", "."));
      return Number.isFinite(number) ? number : null;
    }

    function getSitePerformanceRecord(site, partner) {
      const targetSite = normalizeSiteCode(site);
      const targetPartner = normalizeSiteCode(partner);
      return sitePerformanceRows.find(row => normalizeSiteCode(row.Site) === targetSite && normalizeSiteCode(row["Mitra Kerja"]) === targetPartner) || null;
    }

    function getSitePerformanceValue(site, partner, field) {
      const record = getSitePerformanceRecord(site, partner);
      return parseSitePerformanceNumber(record?.[field]);
    }

    function getSitePerformancePercentValue(site, partner, field) {
      const value = getSitePerformanceValue(site, partner, field);
      return Number.isFinite(value) ? value * 100 : null;
    }

    function formatSitePerformancePercent(value, digits = 1) {
      return Number.isFinite(value) ? `${(value * 100).toFixed(digits)}%` : "-";
    }

    function formatSitePerformanceNumber(value, digits = 2) {
      if (!Number.isFinite(value)) return "-";
      return Number.isInteger(value) ? `${value}` : value.toFixed(digits);
    }

    function sitePerformanceTooltip(site, partner, field, label = field) {
      const record = getSitePerformanceRecord(site, partner);
      if (!record) return `Tidak ada data Site Performance W19 untuk ${site} - ${partner}.`;
      const value = record[field] ?? "-";
      return `${label} W19\nSite: ${record.Site}\nMitra Kerja: ${record["Mitra Kerja"]}\nNilai: ${value}`;
    }

    function dominantDetails(siteData) {
      return driverNames
        .map(([label, key]) => ({ label, key, value: siteData[key] }))
        .sort((a, b) => b.value - a.value)
        .slice(0, 2)
        .map(item => `${item.label} (${item.value})`)
        .join(" · ");
    }

    function sourceMetricText(siteData, key) {
      const metric = siteData.sourceMetrics || {};
      if (key === "safety") return `GR ${metric.gr} · TBC ${metric.tbc} · Blindspot ${metric.blindspot} · Blindspot GR ${metric.blindspotGR} · Ratio SAP ${metric.ratioSAP} · Ratio TBC ${metric.ratioTBC}`;
      if (key === "coverage") return `Coverage Weekly ${metric.coverageWeekly}% · Coverage Daily ${metric.coverageDaily}%`;
      if (key === "exposure") return `RFID Pengawas ${metric.rfidPengawas} · RFID Non Pengawas ${metric.rfidNonPengawas} · Rasio Non Pengawas : Pengawas ${(metric.rfidNonPengawas / metric.rfidPengawas).toFixed(2)}`;
      if (key === "fatigue") return `True Alert Fatigue ${metric.fatigueAlert} · FTW Jam Tidur Kurang ${metric.ftwJamTidurKurang}`;
      return "Data sumber belum tersedia";
    }

    function exposureFormulaText(siteData) {
      const item = siteData.exposureBreakdown;
      if (!item) return `Nilai ${siteData.site}: ${siteData.exposure}.`;
      return `Nilai ${siteData.site}: ${siteData.exposure} = 30% Exposure Load (${item.load}) + 30% Rasio Non Pengawas : Pengawas (${item.ratio}) + 25% Gap Pengawas (${item.control}) + 15% Momentum WoW (${item.momentum}).`;
    }

    function getHeatmapExplanation(site, key) {
      const siteData = getSite(site);
      if (key === "risk") return `${siteData.site}: ${siteData.summary}`;
      if (key === "driverIndex") return `Driver Index ${siteData.driverIndex}: hasil agregasi 4 pilar driver dengan pembobotan Safety Accountability, Coverage Quality, Exposure/RFID, dan Fatigue Management.`;
      if (key === "dominant") return `Detail dominan di ${siteData.site}: ${dominantDetails(siteData)}. Driver ini menjadi fokus pembacaan awal sebelum masuk ke indikator detail.`;
      if (key === "conclusion") return `${siteData.quadrant}: ${siteData.summary}`;
      const driver = driverDefinitions[key];
      if (!driver) return "Penjelasan belum tersedia.";
      if (key === "exposure") return `${driver.label} (${driver.weight}): ${driver.note} Komponen: ${driver.components.join(", ")}. Sumber dashboard: ${sourceMetricText(siteData, key)}. ${exposureFormulaText(siteData)} Level: ${scoreLevel(siteData[key])}.`;
      return `${driver.label} (${driver.weight}): ${driver.note} Komponen: ${driver.components.join(", ")}. Sumber dashboard: ${sourceMetricText(siteData, key)}. Nilai ${siteData.site}: ${siteData[key]} (${scoreLevel(siteData[key])}).`;
    }

    function getScrIncidentRows(site, partner) {
      const targetSite = normalizeSiteCode(site);
      const targetPartner = normalizeSiteCode(partner);
      return scrIncidentRows.filter(row => normalizeSiteCode(row.site) === targetSite && normalizeSiteCode(row.perusahaan) === targetPartner);
    }

    function getScrIncidentCount(site, partner) {
      return getScrIncidentRows(site, partner).reduce((sum, row) => sum + (Number(row.total) || 0), 0);
    }

    function getScrIncidentTooltip(site, partner) {
      const incidents = getScrIncidentRows(site, partner);
      if (!incidents.length) return `Tidak ada incident ${scrIncidentWeek} ${scrIncidentYear} untuk ${site} - ${partner}.`;
      return incidents.map((incident, index) => {
        const title = `${index + 1}. ${incident.kategori || "Incident"} · ${incident.hipo || "-"}`;
        const chronology = incident.kronologi ? `\n${incident.kronologi}` : "";
        return `${title}\nCount: ${incident.total}${chronology}`;
      }).join("\n\n");
    }

    const partnerCompanyPicNames = {
      BUMA: "PT Bukit Makmur Mandiri Utama",
      PAMA: "PT Pamapersada Nusantara",
      KDC: "PT Kaltim Diamond Coal",
      MTL: "PT Mutiara Tanjung Lestari",
      MTN: "PT Madhani Talatah Nusantara",
      BAR: "PT Bumi Artlantis Raya",
      FAD: "PT Fajar Anugerah Dinamika"
    };

    function getPartnerCompanyPicName(partner) {
      return partnerCompanyPicNames[normalizeSiteCode(partner)] || partner;
    }

    function getBlindspotTbcRecords(site, partner) {
      const targetSite = normalizeSiteCode(site);
      const targetCompany = normalizeSiteCode(getPartnerCompanyPicName(partner));
      return blindspotTbcRows.filter(row => normalizeSiteCode(row.site) === targetSite && normalizeSiteCode(row.perusahaanPic) === targetCompany);
    }

    function getBlindspotTbcValue(site, partner) {
      const records = getBlindspotTbcRecords(site, partner);
      if (!records.length) return null;
      return records.reduce((sum, row) => sum + (Number.isFinite(row.value) ? row.value : 0), 0);
    }

    function blindspotTbcTooltip(site, partner) {
      const companyName = getPartnerCompanyPicName(partner);
      const records = getBlindspotTbcRecords(site, partner);
      if (!records.length) return `Tidak ada data Blindspot TBC ${blindspotTbcWeek} untuk ${site} - ${companyName}.`;
      const total = records.reduce((sum, row) => sum + (Number.isFinite(row.value) ? row.value : 0), 0);
      const details = records.map((row, index) => `${index + 1}. ${row.site} · ${row.perusahaanPic} · ${Number.isFinite(row.value) ? row.value : "-"}`).join("\n");
      return `Blindspot TBC ${blindspotTbcWeek}\nSite: ${site}\nPerusahaan PIC: ${companyName}\nTotal: ${total}\n\n${details}`;
    }

    function getRoadStandardHistory(site, partner) {
      const targetSite = normalizeSiteCode(site);
      const targetPartner = normalizeSiteCode(partner);
      return roadStandardRows
        .filter(row => normalizeSiteCode(row.site) === targetSite && normalizeSiteCode(row.mitra) === targetPartner)
        .sort((a, b) => Number(a.week) - Number(b.week));
    }

    function getRoadStandardRecord(site, partner, week = roadStandardWeek) {
      const targetWeek = Number(week);
      return getRoadStandardHistory(site, partner).find(row => Number(row.week) === targetWeek) || null;
    }

    function getRoadStandardValue(site, partner) {
      const record = getRoadStandardRecord(site, partner);
      return Number.isFinite(record?.value) ? record.value * 100 : null;
    }

    function getRoadStandardWeekValue(site, partner, week) {
      const record = getRoadStandardRecord(site, partner, week);
      return Number.isFinite(record?.value) ? record.value * 100 : null;
    }

    function isRoadStandardDeclining(site, partner) {
      const w16 = getRoadStandardWeekValue(site, partner, 16);
      const w17 = getRoadStandardWeekValue(site, partner, 17);
      const w18 = getRoadStandardWeekValue(site, partner, 18);
      const w19 = getRoadStandardWeekValue(site, partner, 19);
      const hasLastTwo = Number.isFinite(w18) && Number.isFinite(w19);
      if (!hasLastTwo) return false;

      const consecutiveDrop = Number.isFinite(w17) && w18 < w17 && w19 < w18;
      const previousTwo = [w16, w17].filter(Number.isFinite);
      const lastTwoAverage = (w18 + w19) / 2;
      const previousTwoAverage = previousTwo.length ? previousTwo.reduce((sum, value) => sum + value, 0) / previousTwo.length : null;
      return consecutiveDrop || (Number.isFinite(previousTwoAverage) && lastTwoAverage < previousTwoAverage);
    }

    function roadStandardTooltipHtml(site, partner) {
      const history = getRoadStandardHistory(site, partner);
      if (!history.length) return `Tidak ada data Road Standard Week 1-19 untuk ${site} - ${partner}.`;
      const historyMap = new Map(history.map(record => [Number(record.week), record]));
      const weekHeaders = Array.from({ length: Number(roadStandardWeek) }, (_, index) => {
        const week = index + 1;
        return `<th style="padding:6px 8px; border:1px solid #e5e7eb; background:#f8fafc; color:#475569; text-align:center; white-space:nowrap;">W${String(week).padStart(2, "0")}</th>`;
      }).join("");
      const valueCells = Array.from({ length: Number(roadStandardWeek) }, (_, index) => {
        const week = index + 1;
        const record = historyMap.get(week);
        const value = record && Number.isFinite(record.value) ? `${(record.value * 100).toFixed(2)}%` : "-";
        const cellStyle = Number.isFinite(record?.value) && record.value * 100 < 80
          ? "background:#fee2e2; color:#991b1b; font-weight:800;"
          : "background:#ffffff; color:#111827; font-weight:700;";
        return `<td style="padding:7px 8px; border:1px solid #e5e7eb; text-align:center; white-space:nowrap; ${cellStyle}">${escapeHtml(value)}</td>`;
      }).join("");
      const declining = isRoadStandardDeclining(site, partner);
      const status = declining ? "Menurun dalam 2 minggu terakhir" : "Tidak menurun 2 minggu berturut-turut";
      return `
        <div style="min-width:720px; max-width:min(920px, calc(100vw - 48px)); color:#111827;">
          <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:10px;">
            <div>
              <div style="font-weight:900; font-size:13px; color:#0f172a;">Road Standard Week 1-19</div>
              <div style="margin-top:2px; color:#475569; font-size:11px;">${escapeHtml(site)} · ${escapeHtml(partner)}</div>
            </div>
            <div style="border-radius:999px; padding:5px 9px; font-size:10px; font-weight:800; white-space:nowrap; background:${declining ? "#fee2e2" : "#dcfce7"}; color:${declining ? "#991b1b" : "#166534"};">${escapeHtml(status)}</div>
          </div>
          <div style="overflow-x:auto; padding-bottom:2px;">
            <table style="width:max-content; min-width:100%; border-collapse:collapse; border:1px solid #e5e7eb; border-radius:10px; overflow:hidden; font-size:11px;">
              <thead><tr>${weekHeaders}</tr></thead>
              <tbody><tr>${valueCells}</tr></tbody>
            </table>
          </div>
        </div>
      `;
    }

    function getCoverageAreaKritisRecord(site) {
      const targetSite = normalizeSiteCode(site);
      return coverageAreaKritisRows.find(row => normalizeSiteCode(row.site) === targetSite) || null;
    }

    function getCoverageAreaKritisValue(site) {
      const record = getCoverageAreaKritisRecord(site);
      return Number.isFinite(record?.value) ? record.value * 100 : null;
    }

    function coverageAreaKritisTooltip(site) {
      const record = getCoverageAreaKritisRecord(site);
      if (!record) return `Tidak ada data Area Kritis ${coverageAreaKritisWeek} untuk ${site}.`;
      const value = Number.isFinite(record.value) ? `${(record.value * 100).toFixed(2)}%` : "-";
      return `Area Kritis ${coverageAreaKritisWeek}\nSite: ${record.site}\nNilai: ${value}`;
    }

    function renderHeatmap() {
      document.getElementById("heatmapTable").innerHTML = sites.map(siteData => {
        const safetyLevel = scoreLevel(siteData.safety);
        const coverageLevel = scoreLevel(siteData.coverage);
        const exposureLevel = scoreLevel(siteData.exposure);
        const fatigueLevel = scoreLevel(siteData.fatigue);
        return `
          <tr>
            <td><b>${escapeHtml(siteData.site)}</b></td>
            <td>${withTooltip(`<span class="badge ${riskBadge(siteData.status)}">${siteData.score.toFixed(2)}% · ${escapeHtml(siteData.status)}</span>`, getHeatmapExplanation(siteData.site, "risk"))}</td>
            <td>${withTooltip(`<span class="heatmap-cell ${heatClass(safetyLevel)}">${safetyLevel} · ${siteData.safety}</span>`, getHeatmapExplanation(siteData.site, "safety"))}</td>
            <td>${withTooltip(`<span class="heatmap-cell ${heatClass(coverageLevel)}">${coverageLevel} · ${siteData.coverage}</span>`, getHeatmapExplanation(siteData.site, "coverage"))}</td>
            <td>${withTooltip(`<span class="heatmap-cell ${heatClass(exposureLevel)}">${exposureLevel} · ${siteData.exposure}</span>`, getHeatmapExplanation(siteData.site, "exposure"))}</td>
            <td>${withTooltip(`<span class="heatmap-cell ${heatClass(fatigueLevel)}">${fatigueLevel} · ${siteData.fatigue}</span>`, getHeatmapExplanation(siteData.site, "fatigue"))}</td>
            <td>${withTooltip(`<b>${siteData.driverIndex}</b>`, getHeatmapExplanation(siteData.site, "driverIndex"))}</td>
            <td>${withTooltip(escapeHtml(dominantDetails(siteData)), getHeatmapExplanation(siteData.site, "dominant"))}</td>
            <td>${withTooltip(escapeHtml(siteData.quadrant), getHeatmapExplanation(siteData.site, "conclusion"))}</td>
          </tr>
        `;
      }).join("");
      bindTooltips();
    }

    function renderTrendTable() {
      document.getElementById("trendTable").innerHTML = sites.map(siteData => {
        const start = siteData.trend[0];
        const end = siteData.trend[siteData.trend.length - 1];
        const momentum = end > start ? "Naik" : end < start ? "Turun" : "Stabil";
        const badge = momentum === "Naik" ? "red" : momentum === "Turun" ? "green" : "yellow";
        return `
          <tr>
            <td><b>${escapeHtml(siteData.site)}</b></td>
            <td>${siteData.score.toFixed(2)}%</td>
            <td>${sparkline(siteData.trend, siteData.color)}</td>
            <td><span class="badge ${badge}">${momentum}</span></td>
          </tr>
        `;
      }).join("");
    }

    function renderBridgeSummary() {
      document.getElementById("bridgeSummary").innerHTML = sites.map(siteData => `
        <div class="summary-item">
          <div class="rank">${escapeHtml(siteData.site.replace("BMO ", "B"))}</div>
          <div>
            <div class="strong">${escapeHtml(siteData.quadrant)}</div>
            <div class="muted small">${escapeHtml(siteData.summary)}</div>
          </div>
          <span class="badge ${riskBadge(siteData.status)}">${escapeHtml(siteData.status)}</span>
        </div>
      `).join("");
    }

    function renderSitePerformance() {
      const headEl = document.getElementById("sitePerformanceHead");
      const bodyEl = document.getElementById("sitePerformanceBody");
      const correlationEl = document.getElementById("sitePerformanceCorrelation");
      if (!headEl || !bodyEl || !correlationEl) return;

      const siteColumns = [
        { site: "BMO 1", partners: ["BUMA", "KDC", "MTL", "MTN"] },
        { site: "BMO 2", partners: ["PAMA"] },
        { site: "BMO 3", partners: ["BAR"] },
        { site: "GMO", partners: ["PAMA"] },
        { site: "LMO", partners: ["FAD", "BUMA"] },
        { site: "SMO", partners: ["MTN"] }
      ];
      const columnMap = siteColumns.flatMap(group => group.partners.map((partner, partnerIndex) => ({
        site: group.site,
        partner,
        partnerIndex
      })));

      headEl.innerHTML = `
        <tr>
          <th colspan="3" rowspan="2"></th>
          ${siteColumns.map(group => `<th class="head-site" colspan="${group.partners.length}">${escapeHtml(group.site)}</th>`).join("")}
        </tr>
        <tr>
          ${columnMap.map(column => `<th class="head-contractor">${escapeHtml(column.partner)}</th>`).join("")}
        </tr>
      `;

      const rows = [
        { group: "Lagging Indikator", marker: "L", parameter: "Lagging Indikator", metric: "Incident", field: "Incident", value: (_site, column) => getSitePerformanceValue(column.site, column.partner, "Incident"), format: value => formatSitePerformanceNumber(value, 0), fromSitePerformance: true },
        { group: "Lagging Indikator", marker: "L", parameter: "Lagging Indikator", metric: "Accident", field: "Accident", value: (_site, column) => getSitePerformanceValue(column.site, column.partner, "Accident"), format: value => formatSitePerformanceNumber(value, 0), fromSitePerformance: true, sub: true },
        { group: "Lagging Indikator", marker: "L", parameter: "Lagging Indikator", metric: "IFR", field: "IFR", value: (_site, column) => getSitePerformanceValue(column.site, column.partner, "IFR"), format: value => formatSitePerformanceNumber(value, 2), fromSitePerformance: true },
        { group: "Lagging Indikator", marker: "L", parameter: "Lagging Indikator", metric: "AFR", field: "AFR", value: (_site, column) => getSitePerformanceValue(column.site, column.partner, "AFR"), format: value => formatSitePerformanceNumber(value, 2), fromSitePerformance: true, sub: true },
        { group: "Leadership", marker: "C", parameter: "PJA Performance", metric: "PJA BC", field: "PJA BC", value: (_site, column) => getSitePerformancePercentValue(column.site, column.partner, "PJA BC"), format: value => Number.isFinite(value) ? `${value.toFixed(1)}%` : "-", fromSitePerformance: true },
        { group: "Leadership", marker: "C", parameter: "PJA Performance", metric: "PJA MK", field: "PJA MK", value: (_site, column) => getSitePerformancePercentValue(column.site, column.partner, "PJA MK"), format: value => Number.isFinite(value) ? `${value.toFixed(1)}%` : "-", fromSitePerformance: true, sub: true },
        { group: "Leadership", marker: "C", parameter: "Coverage Area", metric: "All Area", field: "Coverage Area All", value: (_site, column) => getSitePerformancePercentValue(column.site, column.partner, "Coverage Area All"), format: value => Number.isFinite(value) ? `${value.toFixed(1)}%` : "-", fromSitePerformance: true },
        { group: "Leadership", marker: "C", parameter: "Coverage Area", metric: "Area Kritis", field: "Coverage Area Kritis", value: (_site, column) => getSitePerformancePercentValue(column.site, column.partner, "Coverage Area Kritis"), format: value => Number.isFinite(value) ? `${value.toFixed(1)}%` : "-", fromSitePerformance: true, sub: true },
        { group: "Leadership", marker: "S", parameter: "Ratio Pelaporan", metric: "Ratio Pelaporan TBC", field: "Ratio Pelaporan TBC (TBC/person)", value: (_site, column) => getSitePerformanceValue(column.site, column.partner, "Ratio Pelaporan TBC (TBC/person)"), format: value => formatSitePerformanceNumber(value, 2), fromSitePerformance: true },
        { group: "Leadership", marker: "C", parameter: "Blindspot TBC", metric: "Blindspot TBC", field: "Blindspot TBC", value: (_site, column) => getSitePerformancePercentValue(column.site, column.partner, "Blindspot TBC"), format: value => Number.isFinite(value) ? `${value.toFixed(2)}%` : "-", fromSitePerformance: true },
        { group: "Leadership", marker: "C", parameter: "Overdue Hazard", metric: "Overdue Hazard", field: "Overdue Hazard", value: (_site, column) => getSitePerformancePercentValue(column.site, column.partner, "Overdue Hazard"), format: value => Number.isFinite(value) ? `${value.toFixed(2)}%` : "-", fromSitePerformance: true },
        { group: "Leadership", marker: "C", parameter: "Partisipasi Pelaporan", metric: "Pelaporan SAP L1- L2 MK", field: "Partisipasi Pelaporan SAP L1- L2 MK", value: (_site, column) => getSitePerformancePercentValue(column.site, column.partner, "Partisipasi Pelaporan SAP L1- L2 MK"), format: value => Number.isFinite(value) ? `${value.toFixed(1)}%` : "-", fromSitePerformance: true },
        { group: "People", marker: "L", parameter: "Valid GR & PSPP", metric: "GR", field: "GR", value: (_site, column) => getSitePerformanceValue(column.site, column.partner, "GR"), format: value => formatSitePerformanceNumber(value, 0), fromSitePerformance: true },
        { group: "People", marker: "L", parameter: "Valid GR & PSPP", metric: "PSPP", field: "PSPP", value: (_site, column) => getSitePerformanceValue(column.site, column.partner, "PSPP"), format: value => formatSitePerformanceNumber(value, 0), fromSitePerformance: true, sub: true },
        { group: "Process", marker: "S", parameter: "Road Management", metric: "% Road Standard", field: "% Achivement Road Standard", value: (_site, column) => getSitePerformancePercentValue(column.site, column.partner, "% Achivement Road Standard"), format: value => Number.isFinite(value) ? `${value.toFixed(1)}%` : "-", fromSitePerformance: true },
        { group: "Process", marker: "S", parameter: "Road Management", metric: "Hazard Road Safety", field: "Hazard Road Safety", value: (_site, column) => getSitePerformanceValue(column.site, column.partner, "Hazard Road Safety"), format: value => formatSitePerformanceNumber(value, 0), fromSitePerformance: true },
        { group: "Process", marker: "S", parameter: "Road Management", metric: "Hazard Road Teknis", field: "Hazard Road Teknis", value: (_site, column) => getSitePerformanceValue(column.site, column.partner, "Hazard Road Teknis"), format: value => formatSitePerformanceNumber(value, 0), fromSitePerformance: true, sub: true },
        { group: "Process", marker: "S", parameter: "Fatigue", metric: "True Alert Fatigue", field: "True Alert Fatigue", value: (_site, column) => getSitePerformanceValue(column.site, column.partner, "True Alert Fatigue"), format: value => formatSitePerformanceNumber(value, 0), fromSitePerformance: true },
        { group: "Process", marker: "C", parameter: "Fatigue", metric: "Speak Up Sebelum Alert", field: "Speak Up Sebelum Alert", value: (_site, column) => getSitePerformancePercentValue(column.site, column.partner, "Speak Up Sebelum Alert"), format: value => Number.isFinite(value) ? `${value.toFixed(1)}%` : "-", fromSitePerformance: true },
        { group: "Technology", marker: "S", parameter: "Pengawasan Berjarak", metric: "Real Time", field: "Real Time", value: (_site, column) => getSitePerformancePercentValue(column.site, column.partner, "Real Time"), format: value => Number.isFinite(value) ? `${value.toFixed(1)}%` : "-", fromSitePerformance: true },
        { group: "Technology", marker: "S", parameter: "Pengawasan Berjarak", metric: "Post Event", field: "Post Event", value: (_site, column) => getSitePerformancePercentValue(column.site, column.partner, "Post Event"), format: value => Number.isFinite(value) ? `${value.toFixed(1)}%` : "-", fromSitePerformance: true, sub: true }
      ];

      bodyEl.innerHTML = rows.map((row, rowIndex) => {
        const showGroup = rowIndex === 0 || rows[rowIndex - 1].group !== row.group;
        const groupRowSpan = rows.filter(item => item.group === row.group).length;
        const showParameter = rowIndex === 0 || rows[rowIndex - 1].group !== row.group || rows[rowIndex - 1].parameter !== row.parameter;
        const parameterRowSpan = rows.filter(item => item.group === row.group && item.parameter === row.parameter).length;
        const groupClass = cssClass(row.group);
        return `
          <tr>
            ${showGroup ? `<td class="group-head ${groupClass}" rowspan="${groupRowSpan}">${escapeHtml(row.group)}</td>` : ""}
            ${showParameter ? `<td class="parameter-head" rowspan="${parameterRowSpan}"><span class="marker">${escapeHtml(row.marker)}</span>${escapeHtml(row.parameter)}</td>` : ""}
            <td class="metric-head ${row.sub ? "sub-metric" : ""}">${escapeHtml(row.metric)}</td>
            ${columnMap.map(column => {
              const site = getSite(column.site);
              const raw = row.value(site, column);
              const hasRawValue = Number.isFinite(raw);
              const value = hasRawValue ? raw : 0;
              const variance = Math.round((column.partnerIndex - 1) * 2);
              const adjustedValue = row.fromScrIncident
                ? value
                : row.fromSitePerformance
                ? (hasRawValue ? value : null)
                : row.fromRoadStandard
                ? (hasRawValue ? value : null)
                : row.fromCoverageAreaKritis
                ? (hasRawValue ? value : null)
                : row.fromBlindspotTbc
                ? (hasRawValue ? value : null)
                : row.forceZero
                ? 0
                : Math.max(0, value + variance * 0.01 * (row.metric.includes("%") || row.metric.includes("Area") ? 100 : 1));
              const isDanger = (
                (row.fromScrIncident && adjustedValue >= 1) ||
                (row.fromRoadStandard && isRoadStandardDeclining(column.site, column.partner)) ||
                (row.metric === "Ratio Pelaporan TBC" && adjustedValue < 2.5) ||
                (row.metric === "Blindspot TBC" && adjustedValue > 0) ||
                (row.parameter === "Fatigue" && adjustedValue >= 10) ||
                (row.parameter === "Pengawasan Berjarak" && adjustedValue >= 4) ||
                ((row.metric === "All Area" || row.metric === "Area Kritis") && adjustedValue < 80)
              );
              const isWatch = (
                (row.metric === "PJA BC" && adjustedValue < 98) ||
                (row.parameter === "Road Management" && adjustedValue >= 55 && adjustedValue < 70) ||
                (row.metric === "GR" && adjustedValue >= 1) ||
                (row.metric === "PJA MK" && adjustedValue < 99)
              );
              const className = isDanger ? "cell-danger" : isWatch ? "cell-watch" : adjustedValue === 0 || adjustedValue === null ? "cell-muted" : "";
              const content = row.format(adjustedValue);
              const cellContent = row.fromScrIncident
                ? withCellTooltip(content, getScrIncidentTooltip(column.site, column.partner))
                : row.fromRoadStandard
                ? withCellHtmlTooltip(content, roadStandardTooltipHtml(column.site, column.partner))
                : row.fromCoverageAreaKritis
                ? withCellTooltip(content, coverageAreaKritisTooltip(column.site))
                : row.fromBlindspotTbc
                ? withCellTooltip(content, blindspotTbcTooltip(column.site, column.partner))
                : row.fromSitePerformance
                ? withCellTooltip(content, sitePerformanceTooltip(column.site, column.partner, row.field, row.metric))
                : content;
              return `<td class="${className}">${cellContent}</td>`;
            }).join("")}
          </tr>
        `;
      }).join("");
      bindTooltips();

      correlationEl.innerHTML = sites.map(site => `
        <article class="site-correlation-item ${site.status === "Best Profile" ? "best" : ""}">
          <h4>${escapeHtml(site.site)} · ${escapeHtml(site.status)}</h4>
          <p>${escapeHtml(site.actions.slice(0, 2).map((item, index) => `${index + 1}) ${item}`).join(" · "))}</p>
        </article>
      `).join("");
    }

    function renderAction() {
      const priorityWeight = { "High Risk": 3, "Unstable": 2, "Best Profile": 1 };
      const priority = [...sites].sort((a, b) => (priorityWeight[b.status] * 100 + b.driverIndex) - (priorityWeight[a.status] * 100 + a.driverIndex));
      document.getElementById("actionList").innerHTML = priority.map((siteData, index) => `
        <div class="action-item">
          <div class="action-head">
            <div>
              <div class="action-title">${index + 1}. ${escapeHtml(siteData.site)}</div>
              <div class="muted small">Risk ${siteData.score.toFixed(2)}% · Driver Index ${siteData.driverIndex}</div>
            </div>
            <span class="badge ${riskBadge(siteData.status)}">${escapeHtml(siteData.status)}</span>
          </div>
          <ul class="checklist">
            ${siteData.actions.map(action => `<li>${escapeHtml(action)}</li>`).join("")}
          </ul>
        </div>
      `).join("");
    }

    function getFloatingTooltip() {
      let tooltip = document.getElementById("floatingTooltip");
      if (!tooltip) {
        tooltip = document.createElement("div");
        tooltip.id = "floatingTooltip";
        tooltip.className = "floating-tooltip";
        document.body.appendChild(tooltip);
      }
      return tooltip;
    }

    function showTooltip(element, event) {
      const tooltip = getFloatingTooltip();
      if (element.dataset.tooltipHtml) {
        tooltip.innerHTML = element.dataset.tooltipHtml;
        tooltip.style.background = "#ffffff";
        tooltip.style.color = "#111827";
        tooltip.style.maxWidth = "min(940px, calc(100vw - 28px))";
      } else {
        tooltip.textContent = element.dataset.tooltip || "";
        tooltip.style.background = "";
        tooltip.style.color = "";
        tooltip.style.maxWidth = "";
      }
      tooltip.classList.add("show");
      const rect = tooltip.getBoundingClientRect();
      const sourceRect = element.getBoundingClientRect();
      const eventX = Number.isFinite(event.clientX) && event.clientX !== 0 ? event.clientX : sourceRect.left;
      const eventY = Number.isFinite(event.clientY) && event.clientY !== 0 ? event.clientY : sourceRect.top;
      let left = eventX + 14;
      let top = eventY - rect.height - 16;
      if (left + rect.width > window.innerWidth - 12) left = window.innerWidth - rect.width - 12;
      if (left < 12) left = 12;
      if (top < 12) top = eventY + 18;
      tooltip.style.left = `${left}px`;
      tooltip.style.top = `${top}px`;
      tooltip.style.transform = "translate(0,0)";
    }

    function hideTooltip() {
      const tooltip = getFloatingTooltip();
      tooltip.classList.remove("show");
      tooltip.style.transform = "translate(-9999px,-9999px)";
    }

    function bindTooltips() {
      document.querySelectorAll(".tooltip-wrap").forEach(element => {
        if (element.dataset.bound === "1") return;
        element.dataset.bound = "1";
        element.tabIndex = 0;
        element.addEventListener("mouseenter", event => showTooltip(element, event));
        element.addEventListener("mousemove", event => showTooltip(element, event));
        element.addEventListener("mouseleave", hideTooltip);
        element.addEventListener("focus", event => showTooltip(element, event));
        element.addEventListener("blur", hideTooltip);
      });
    }

    function renderPageIntro(view) {
      const meta = pageMeta[view] || pageMeta.overview;
      document.getElementById("pageIntro").innerHTML = `
        <div>
          <h2>${escapeHtml(meta.title)}</h2>
          <p>${escapeHtml(meta.description)}</p>
        </div>
        <span class="page-counter">Page ${escapeHtml(meta.index)}</span>
      `;
    }

    function normalizeSiteCode(siteName) {
      return String(siteName || "").replace(/\s+/g, "").toUpperCase();
    }

    function numericValue(value) {
      const number = Number(value);
      return Number.isFinite(number) ? number : null;
    }

    function shouldDisplayAsPercent(field, number) {
      const fieldName = String(field || "");
      const percentLikeField = /^%/.test(fieldName) || /(coverage|ratio|availability|kesesuaian|kelayakan|golden time|road standard|alert intervented|improvement|investigasi|closing|laporan)/i.test(fieldName);
      return (number > 0 && number < 1) || (percentLikeField && (number === 0 || number === 1));
    }

    function formatTooltipValue(value, field = "") {
      if (value === null || value === undefined || value === "") return "-";
      const number = Number(value);
      if (!Number.isFinite(number)) return escapeHtml(value);
      if (shouldDisplayAsPercent(field, number)) return escapeHtml(`${(number * 100).toFixed(2)}%`);
      return escapeHtml(Number.isInteger(number) ? number : number.toFixed(3));
    }

    function getHistoricalRecords(siteName) {
      const targetSite = normalizeSiteCode(siteName);
      return historicalRiskProfileData
        .filter(row => normalizeSiteCode(row.Site) === targetSite && Number(row.Minggu) >= 1 && Number(row.Minggu) <= historicalWeekLimit)
        .sort((a, b) => Number(a.Minggu) - Number(b.Minggu));
    }

    function getHistoricalRecordByWeek(siteName, week) {
      return getHistoricalRecords(siteName).find(row => Number(row.Minggu) === Number(week)) || null;
    }

    function getHistoricalObservedValue(record) {
      return numericValue(record?.["Observed Risk"] ?? record?.Risk_Engine_Base ?? record?.Weighted_Score);
    }

    function movingAverage(values, window = 4) {
      return values.map((_, index) => {
        const start = Math.max(0, index - window + 1);
        const slice = values.slice(start, index + 1).filter(value => Number.isFinite(value));
        return slice.length ? slice.reduce((sum, num) => sum + num, 0) / slice.length : null;
      });
    }

    function smoothScenario(start, end, points = 8, curve = 1.85) {
      return Array.from({ length: points }, (_, index) => {
        const t = index / Math.max(points - 1, 1);
        return start + (end - start) * (1 - Math.exp(-curve * t));
      });
    }

    function polylinePoints(values, width, height, minY = 0, maxY = 70, startIndex = 0, totalPoints = values.length) {
      const xStep = width / Math.max(totalPoints - 1, 1);
      return values.map((value, index) => {
        const x = (startIndex + index) * xStep;
        const bounded = Math.max(minY, Math.min(maxY, value));
        const y = height - ((bounded - minY) / (maxY - minY)) * height;
        return `${x.toFixed(2)},${y.toFixed(2)}`;
      }).join(" ");
    }

    function buildObservedFromHistorical(siteData) {
      if (siteData.site === "ALL") {
        return Array.from({ length: historicalWeekLimit }, (_, index) => {
          const week = index + 1;
          const weekValues = historicalRiskProfileData
            .filter(row => Number(row.Minggu) === week)
            .map(getHistoricalObservedValue)
            .filter(value => Number.isFinite(value));
          return weekValues.length ? weekValues.reduce((sum, value) => sum + value, 0) / weekValues.length : null;
        });
      }

      const records = getHistoricalRecords(siteData.site);
      return Array.from({ length: historicalWeekLimit }, (_, index) => {
        const record = records.find(row => Number(row.Minggu) === index + 1);
        return getHistoricalObservedValue(record);
      });
    }

    const predictionScenarioNames = {
      controlled: "Controlled Recovery",
      bau: "BAU",
      worst: "Worst",
      proposed: "Proposed"
    };

    function getPredictionSiteKey(siteName) {
      return normalizeSiteCode(siteName);
    }

    function getPredictionWeeks(siteData) {
      if (siteData.site === "ALL") {
        const weeks = new Set();
        Object.values(predictionRiskProfileData || {}).forEach(sitePrediction => {
          Object.keys(sitePrediction || {}).forEach(week => weeks.add(Number(week)));
        });
        return Array.from(weeks).filter(Number.isFinite).sort((a, b) => a - b);
      }

      const sitePrediction = predictionRiskProfileData?.[getPredictionSiteKey(siteData.site)] || {};
      return Object.keys(sitePrediction).map(Number).filter(Number.isFinite).sort((a, b) => a - b);
    }

    function getPredictionScenarioValue(siteData, scenarioKey, week) {
      const scenarioName = predictionScenarioNames[scenarioKey];
      if (!scenarioName) return null;

      if (siteData.site === "ALL") {
        const values = Object.values(predictionRiskProfileData || {})
          .map(sitePrediction => sitePrediction?.[String(week)] || [])
          .flat()
          .filter(item => item.scenario === scenarioName)
          .map(item => Number(item.projected_risk))
          .filter(Number.isFinite);
        return values.length ? values.reduce((sum, value) => sum + value, 0) / values.length : null;
      }

      const weekItems = predictionRiskProfileData?.[getPredictionSiteKey(siteData.site)]?.[String(week)] || [];
      const item = weekItems.find(row => row.scenario === scenarioName);
      const value = Number(item?.projected_risk);
      return Number.isFinite(value) ? value : null;
    }

    function buildPredictionScenario(siteData, scenarioKey, breakoutValue) {
      const predictionWeeks = getPredictionWeeks(siteData);
      const values = predictionWeeks.map(week => getPredictionScenarioValue(siteData, scenarioKey, week));
      if (!values.some(Number.isFinite)) return null;
      const firstPrediction = values.find(Number.isFinite) ?? breakoutValue;
      return { weeks: predictionWeeks, values: [firstPrediction, ...values] };
    }

    function buildProfileData(siteData, seed = 0) {
      const historicalObserved = buildObservedFromHistorical(siteData);
      const hasHistoricalObserved = historicalObserved.some(value => Number.isFinite(value));
      const observedLength = historicalWeekLimit;
      const base = siteData.score + (siteData.status === "High Risk" ? 6 : siteData.status === "Unstable" ? 2 : -2);
      const volatility = siteData.status === "High Risk" ? 8 : siteData.status === "Unstable" ? 6 : 4;
      const observed = hasHistoricalObserved ? historicalObserved : Array.from({ length: observedLength }, (_, index) => {
        const wave = Math.sin((index + 1 + seed) * 0.75) * volatility;
        const drift = (index - observedLength / 2) * 0.15;
        const noise = Math.cos((index + seed) * 1.12) * 2.4;
        return Math.max(8, Math.min(55, base + wave + drift + noise));
      });
      const moving = movingAverage(observed);
      const breakout = observed.reduce((lastIndex, value, index) => Number.isFinite(value) ? index : lastIndex, 0);
      const breakoutValue = Number.isFinite(observed[breakout]) ? observed[breakout] : siteData.score;
      const predictionWeeks = getPredictionWeeks(siteData);
      const controlledPrediction = buildPredictionScenario(siteData, "controlled", breakoutValue);
      const bauPrediction = buildPredictionScenario(siteData, "bau", breakoutValue);
      const worstPrediction = buildPredictionScenario(siteData, "worst", breakoutValue);
      const proposedPrediction = buildPredictionScenario(siteData, "proposed", breakoutValue);
      const proposed = proposedPrediction?.values || smoothScenario(breakoutValue, Math.max(4, siteData.score * 0.24), 9, 2.1);
      const controlled = controlledPrediction?.values || smoothScenario(breakoutValue, Math.max(10, siteData.score * 0.5), 9, 1.8);
      const bau = bauPrediction?.values || smoothScenario(breakoutValue, Math.max(20, siteData.score * 0.9), 9, 1.45);
      const worst = worstPrediction?.values || smoothScenario(breakoutValue, Math.min(66, siteData.score + 36), 9, 2.35);
      return { observed, moving, proposed, controlled, bau, worst, breakout, predictionWeeks };
    }

    function profileSvg(data, width = 860, height = 230) {
      const totalPoints = data.observed.length + data.proposed.length - 1;
      const scenarioOffset = data.observed.length - 1;
      const observedPath = polylinePoints(data.observed, width, height, 0, 70, 0, totalPoints);
      const movingPath = polylinePoints(data.moving, width, height, 0, 70, 0, totalPoints);
      const proposedPath = polylinePoints(data.proposed, width, height, 0, 70, scenarioOffset, totalPoints);
      const controlledPath = polylinePoints(data.controlled, width, height, 0, 70, scenarioOffset, totalPoints);
      const bauPath = polylinePoints(data.bau, width, height, 0, 70, scenarioOffset, totalPoints);
      const worstPath = polylinePoints(data.worst, width, height, 0, 70, scenarioOffset, totalPoints);
      const breakX = (data.breakout / Math.max(totalPoints - 1, 1)) * width;
      const breakY = height - ((data.observed[data.breakout] / 70) * height);
      const observedArea = `${observedPath} ${width.toFixed(2)},${height.toFixed(2)} 0,${height.toFixed(2)}`;
      const gridY = [0, 10, 20, 30, 40, 50, 60, 70].map(value => {
        const y = height - (value / 70) * height;
        return `<line x1="0" y1="${y.toFixed(2)}" x2="${width}" y2="${y.toFixed(2)}" stroke="#e5e7eb" stroke-width="${value % 20 === 0 ? 1.1 : 0.8}" stroke-dasharray="${value % 20 === 0 ? "0" : "3 4"}"/>`;
      }).join("");
      const gridX = [0, 4, 8, 12, 16, totalPoints - 1].map(index => {
        const x = (index / Math.max(totalPoints - 1, 1)) * width;
        return `<line x1="${x.toFixed(2)}" y1="0" x2="${x.toFixed(2)}" y2="${height}" stroke="#eef2f7" stroke-width="1"/>`;
      }).join("");
      const yLabels = [70, 50, 30, 10].map(value => {
        const y = height - (value / 70) * height - 3;
        return `<text x="6" y="${Math.max(10, y).toFixed(2)}" fill="#94a3b8" font-size="10" font-weight="700">${value}</text>`;
      }).join("");
      return `
        <svg viewBox="0 0 ${width} ${height}" class="w-full h-[220px] sm:h-[250px]" preserveAspectRatio="none">
          <defs>
            <linearGradient id="riskObservedFill-${width}-${height}" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#e879f9" stop-opacity=".28"/>
              <stop offset="100%" stop-color="#e879f9" stop-opacity="0"/>
            </linearGradient>
          </defs>
          ${gridX}
          ${gridY}
          <polygon points="${observedArea}" fill="url(#riskObservedFill-${width}-${height})"/>
          <polyline points="${movingPath}" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-dasharray="4 4"/>
          <polyline points="${observedPath}" fill="none" stroke="#e879f9" stroke-width="2.7" stroke-linecap="round" stroke-linejoin="round"/>
          <polyline points="${controlledPath}" fill="none" stroke="#22c55e" stroke-width="2.4" stroke-linecap="round"/>
          <polyline points="${bauPath}" fill="none" stroke="#eab308" stroke-width="2.4" stroke-linecap="round"/>
          <polyline points="${worstPath}" fill="none" stroke="#be123c" stroke-width="2.6" stroke-linecap="round"/>
          <polyline points="${proposedPath}" fill="none" stroke="#0ea5e9" stroke-width="2.4" stroke-linecap="round"/>
          <line x1="${breakX.toFixed(2)}" y1="0" x2="${breakX.toFixed(2)}" y2="${height}" stroke="#ef4444" stroke-width="1.2" stroke-dasharray="4 4"/>
          <circle cx="${breakX.toFixed(2)}" cy="${breakY.toFixed(2)}" r="4.5" fill="#ef4444"/>
          <text x="${Math.min(width - 92, breakX + 8).toFixed(2)}" y="${Math.max(14, breakY - 10).toFixed(2)}" fill="#ef4444" font-size="10" font-weight="700">Breakout point</text>
          ${yLabels}
        </svg>
      `;
    }

    function buildRiskProfileAxis(data) {
      const observedLabels = Array.from({ length: data.observed.length }, (_, index) => `W${String(index + 1).padStart(2, "0")}`);
      const scenarioLabels = data.predictionWeeks?.length
        ? data.predictionWeeks.map(week => `W${String(week).padStart(2, "0")}`)
        : Array.from({ length: data.proposed.length - 1 }, (_, index) => `P${index + 1}`);
      return observedLabels.concat(scenarioLabels);
    }

    function toSeriesAxisData(data, key) {
      const axis = buildRiskProfileAxis(data);
      const offset = data.observed.length - 1;
      return axis.map((_, index) => {
        if (index <= data.observed.length - 1) {
          if (key === "moving") return Number.isFinite(data.moving[index]) ? Number(data.moving[index].toFixed(2)) : null;
          if (key === "observed") return Number.isFinite(data.observed[index]) ? Number(data.observed[index].toFixed(2)) : null;
          if (index === offset) return Number.isFinite(data[key]?.[0]) ? Number(data[key][0].toFixed(2)) : null;
          return null;
        }
        const scenarioIndex = index - offset;
        return Number.isFinite(data[key][scenarioIndex]) ? Number(data[key][scenarioIndex].toFixed(2)) : null;
      });
    }

    function formatRiskProfileTooltip(params, siteName) {
      const items = Array.isArray(params) ? params : [params];
      const axisLabel = String(items[0]?.axisValueLabel || items[0]?.axisValue || "");
      const week = axisLabel.startsWith("W") ? Number(axisLabel.replace(/[^\d]/g, "")) : null;
      const seriesRows = items
        .filter(item => item.value !== null && item.value !== undefined && item.value !== "-")
        .map(item => `
          <div style="display:flex; justify-content:space-between; gap:14px; margin-top:4px;">
            <span>${item.marker || ""}${escapeHtml(item.seriesName)}</span>
            <b>${formatTooltipValue(item.value)}</b>
          </div>
        `).join("");

      let historicalRows = "";
      if (week && siteName) {
        const record = getHistoricalRecordByWeek(siteName, week);
        if (record) {
          historicalRows = historicalTooltipFields
            .filter(field => Object.prototype.hasOwnProperty.call(record, field))
            .map(field => `
              <div style="display:grid; grid-template-columns:minmax(150px,1fr) auto; gap:12px; padding:3px 0; border-bottom:1px solid rgba(255,255,255,.08);">
                <span>${escapeHtml(field)}</span>
                <b>${formatTooltipValue(record[field], field)}</b>
              </div>
            `).join("");
        }
      } else if (week) {
        const weekValues = historicalRiskProfileData
          .filter(row => Number(row.Minggu) === week)
          .map(getHistoricalObservedValue)
          .filter(value => Number.isFinite(value));
        if (weekValues.length) {
          const average = weekValues.reduce((sum, value) => sum + value, 0) / weekValues.length;
          historicalRows = `
            <div style="display:grid; grid-template-columns:minmax(150px,1fr) auto; gap:12px; padding:3px 0; border-bottom:1px solid rgba(255,255,255,.08);">
              <span>Site Count</span>
              <b>${weekValues.length}</b>
            </div>
            <div style="display:grid; grid-template-columns:minmax(150px,1fr) auto; gap:12px; padding:3px 0;">
              <span>Avg Observed Risk</span>
              <b>${formatTooltipValue(average)}</b>
            </div>
          `;
        }
      }

      return `
        <div style="min-width:280px; max-width:460px;">
          <div style="font-weight:800; margin-bottom:8px;">${escapeHtml(siteName || "All Site")} · ${escapeHtml(axisLabel)}</div>
          ${seriesRows}
          ${historicalRows ? `
            <div style="margin-top:10px; padding-top:8px; border-top:1px solid rgba(255,255,255,.22); font-weight:800;">Historical Data</div>
            <div style="margin-top:4px; max-height:320px; overflow:auto; padding-right:4px;">${historicalRows}</div>
          ` : ""}
        </div>
      `;
    }

    function getRiskProfileYAxisBounds(seriesList) {
      const values = seriesList.flat().filter(Number.isFinite);
      if (!values.length) return { min: 0, max: 70 };
      const min = Math.min(...values);
      const max = Math.max(...values);
      const padding = Math.max(3, (max - min) * 0.18);
      return {
        min: Math.max(0, Math.floor(min - padding)),
        max: Math.ceil(max + padding)
      };
    }

    function renderRiskProfiling() {
      const mainEl = document.getElementById("riskProfileMainChart");
      const miniEl = document.getElementById("riskProfileMiniGrid");
      if (!mainEl || !miniEl || typeof echarts === "undefined") return;
      riskProfileMiniChartInstances.forEach(chart => chart.dispose());
      riskProfileMiniChartInstances = [];
      if (riskProfileMainChartInstance) riskProfileMainChartInstance.dispose();

      const isAllSite = selectedRiskProfileSite === "ALL";
      const activeSite = isAllSite ? null : getSite(selectedRiskProfileSite);
      const profileSource = isAllSite
        ? { site: "ALL", score: Math.round(sites.reduce((sum, site) => sum + site.score, 0) / sites.length), status: "Unstable" }
        : { site: activeSite.site, score: activeSite.score, status: activeSite.status };
      const seed = isAllSite ? 2 : Math.max(1, sites.findIndex(item => item.site === activeSite.site) + 1);
      const profileData = buildProfileData(profileSource, seed);
      const axis = buildRiskProfileAxis(profileData);
      const breakoutLabel = axis[profileData.breakout];
      const movingData = toSeriesAxisData(profileData, "moving");
      const observedData = toSeriesAxisData(profileData, "observed");
      const controlledData = toSeriesAxisData(profileData, "controlled");
      const bauData = toSeriesAxisData(profileData, "bau");
      const worstData = toSeriesAxisData(profileData, "worst");
      const proposedData = toSeriesAxisData(profileData, "proposed");
      const yBounds = getRiskProfileYAxisBounds([movingData, observedData, controlledData, bauData, worstData, proposedData]);
      if (riskProfileSiteLabel) {
        riskProfileSiteLabel.textContent = isAllSite ? "All Site" : activeSite.site;
      }
      if (riskProfileSiteFilter && riskProfileSiteFilter.value !== selectedRiskProfileSite) {
        riskProfileSiteFilter.value = selectedRiskProfileSite;
      }
      riskProfileMainChartInstance = echarts.init(mainEl);
      riskProfileMainChartInstance.setOption({
        animationDuration: 900,
        grid: { left: 38, right: 20, top: 16, bottom: 34 },
        tooltip: {
          trigger: "axis",
          axisPointer: { type: "line" },
          confine: true,
          appendToBody: true,
          enterable: true,
          hideDelay: 500,
          extraCssText: "max-width:480px; white-space:normal;",
          formatter: params => formatRiskProfileTooltip(params, isAllSite ? null : activeSite.site)
        },
        xAxis: {
          type: "category",
          data: axis,
          axisTick: { show: false },
          axisLine: { lineStyle: { color: "#d6dbe3" } },
          axisLabel: {
            color: "#7c8798",
            fontSize: 9,
            interval: 0,
            hideOverlap: false
          }
        },
        yAxis: {
          type: "value",
          min: yBounds.min,
          max: yBounds.max,
          splitLine: { lineStyle: { color: "#e5e7eb", type: "dashed" } },
          axisLabel: { color: "#7c8798", fontSize: 10 }
        },
        series: [
          { name: "Moving Average 4 Week", type: "line", data: movingData, smooth: 0.25, symbol: "none", lineStyle: { color: "#9ca3af", width: 2, type: "dashed" } },
          {
            name: "Observed Risk Profile",
            type: "line",
            data: observedData,
            smooth: 0.3,
            symbol: "none",
            lineStyle: { width: 3, color: "#e879f9" },
            areaStyle: {
              color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                { offset: 0, color: "rgba(232,121,249,0.35)" },
                { offset: 1, color: "rgba(232,121,249,0.02)" }
              ])
            },
            markPoint: { symbol: "circle", symbolSize: 10, itemStyle: { color: "#ef4444" }, data: [{ coord: [breakoutLabel, observedData[profileData.breakout]] }] },
            markLine: { symbol: "none", label: { formatter: "Breakout", color: "#ef4444" }, lineStyle: { color: "#ef4444", type: "dashed" }, data: [{ xAxis: breakoutLabel }] }
          },
          { name: "Controlled Recovery Scenario", type: "line", data: controlledData, smooth: 0.62, smoothMonotone: "x", symbol: "circle", symbolSize: 3, showSymbol: false, lineStyle: { color: "#22c55e", width: 2.2 } },
          { name: "BAU Scenario", type: "line", data: bauData, smooth: 0.62, smoothMonotone: "x", symbol: "circle", symbolSize: 3, showSymbol: false, lineStyle: { color: "#eab308", width: 2.2 } },
          { name: "Worst Scenario", type: "line", data: worstData, smooth: 0.62, smoothMonotone: "x", symbol: "circle", symbolSize: 3, showSymbol: false, lineStyle: { color: "#be123c", width: 2.3 } },
          { name: "Proposed Scenario", type: "line", data: proposedData, smooth: 0.62, smoothMonotone: "x", symbol: "circle", symbolSize: 3, showSymbol: false, lineStyle: { color: "#0ea5e9", width: 2.2 } }
        ]
      });

      miniEl.innerHTML = sites.map((siteData, index) => `
        <article class="risk-profile-mini ${selectedRiskProfileSite === siteData.site ? "selected" : ""}" data-site="${escapeHtml(siteData.site)}" style="background:${siteData.status === "Best Profile" ? "#ecfdf5" : siteData.status === "Unstable" ? "#fefce8" : "#fdf2f8"}">
          <div class="risk-profile-head">
            <div>
              <div class="risk-profile-site">${escapeHtml(siteData.site)}</div>
            </div>
            <div class="risk-profile-chip-wrap">
              <span class="risk-chip">Leading</span>
              <span class="risk-chip">Leadership</span>
            </div>
          </div>
          <div class="risk-profile-mini-chart" data-mini-risk-chart="${index}"></div>
        </article>
      `).join("");

      sites.forEach((siteData, index) => {
        const el = miniEl.querySelector(`[data-mini-risk-chart="${index}"]`);
        if (!el) return;
        const profile = buildProfileData(siteData, index + 1);
        const miniAxis = buildRiskProfileAxis(profile);
        const miniObserved = toSeriesAxisData(profile, "observed");
        const miniControlled = toSeriesAxisData(profile, "controlled");
        const miniBau = toSeriesAxisData(profile, "bau");
        const miniProposed = toSeriesAxisData(profile, "proposed");
        const miniWorst = toSeriesAxisData(profile, "worst");
        const miniYBounds = getRiskProfileYAxisBounds([miniObserved, miniControlled, miniBau, miniWorst, miniProposed]);
        const miniChart = echarts.init(el);
        miniChart.setOption({
          animationDuration: 500,
          grid: { left: 8, right: 8, top: 6, bottom: 6 },
          xAxis: { type: "category", data: miniAxis, show: false },
          yAxis: { type: "value", min: miniYBounds.min, max: miniYBounds.max, show: false },
          tooltip: {
            trigger: "axis",
            axisPointer: { type: "none" },
            confine: true,
            appendToBody: true,
            enterable: true,
            hideDelay: 500,
            extraCssText: "max-width:480px; white-space:normal;",
            formatter: params => formatRiskProfileTooltip(params, siteData.site)
          },
          series: [
            {
              type: "line",
              name: "Observed",
              data: miniObserved,
              smooth: 0.3,
              symbol: "none",
              lineStyle: { width: 2, color: "#e879f9" },
              areaStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                  { offset: 0, color: "rgba(232,121,249,0.24)" },
                  { offset: 1, color: "rgba(232,121,249,0.01)" }
                ])
              }
            },
            { type: "line", name: "Controlled Recovery", data: miniControlled, smooth: 0.62, smoothMonotone: "x", symbol: "circle", symbolSize: 2, showSymbol: false, lineStyle: { width: 1.7, color: "#22c55e" } },
            { type: "line", name: "BAU", data: miniBau, smooth: 0.62, smoothMonotone: "x", symbol: "circle", symbolSize: 2, showSymbol: false, lineStyle: { width: 1.7, color: "#eab308" } },
            { type: "line", name: "Worst", data: miniWorst, smooth: 0.62, smoothMonotone: "x", symbol: "circle", symbolSize: 2, showSymbol: false, lineStyle: { width: 1.8, color: "#be123c" } },
            {
              type: "line",
              name: "Proposed",
              data: miniProposed,
              smooth: 0.62,
              smoothMonotone: "x",
              symbol: "circle",
              symbolSize: 2,
              showSymbol: false,
              lineStyle: {
                width: 1.8,
                color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                  { offset: 0, color: "#38bdf8" },
                  { offset: 1, color: "#2563eb" }
                ])
              }
            }
          ]
        });
        riskProfileMiniChartInstances.push(miniChart);
      });
    }

    function syncPageOnlyContent(view) {
      document.querySelectorAll("[data-page-only]").forEach(element => {
        element.hidden = element.dataset.pageOnly !== view;
      });
    }

    function switchView(view) {
      activeView = view;
      document.querySelectorAll(".tabs button").forEach(button => button.classList.toggle("active", button.dataset.view === view));
      document.querySelectorAll("[data-section]").forEach(section => {
        section.hidden = section.dataset.section !== view;
      });
      syncPageOnlyContent(view);
      renderPageIntro(view);
      closeSitePopup();
      hideTooltip();
      bindTooltips();
    }

    function selectSite(site) {
      selectedSite = site;
      renderCards();
      renderScatter();
      renderSitePopup();
    }

    function renderAll() {
      renderCards();
      renderRiskProfiling();
      renderSitePerformance();
      renderDriverDictionary();
      renderRanking();
      renderScatter();
      renderDriverContribution();
      renderHeatmap();
      renderTrendTable();
      renderBridgeSummary();
      renderAction();
      switchView(activeView);
    }

    document.getElementById("tabs")?.addEventListener("click", event => {
      const button = event.target.closest("button[data-view]");
      if (!button) return;
      switchView(button.dataset.view);
    });

    siteCards?.addEventListener("click", event => {
      const card = event.target.closest(".score-card");
      if (!card) return;
      selectSite(card.dataset.site);
    });

    document.getElementById("scatterBox")?.addEventListener("click", event => {
      const point = event.target.closest(".point");
      if (!point) return;
      selectSite(point.dataset.site);
    });

    riskFilter?.addEventListener("change", () => {
      if (activeView !== "overview") return;
      renderCards();
      closeSitePopup();
    });
    if (riskProfileSiteFilter) {
      riskProfileSiteFilter.addEventListener("change", () => {
        selectedRiskProfileSite = riskProfileSiteFilter.value || "ALL";
        renderRiskProfiling();
      });
    }
    document.getElementById("riskProfileMiniGrid")?.addEventListener("click", event => {
      const miniCard = event.target.closest(".risk-profile-mini[data-site]");
      if (!miniCard) return;
      selectedRiskProfileSite = miniCard.dataset.site || "ALL";
      renderRiskProfiling();
    });

    document.addEventListener("click", event => {
      if (event.target.closest("[data-close-popup='true']")) {
        closeSitePopup();
        return;
      }
      if (!sitePopup?.classList.contains("show")) return;
      if (event.target.closest(".score-card") || event.target.closest("#sitePopup")) return;
      closeSitePopup();
    });

    window.addEventListener("resize", () => {
      if (sitePopup?.classList.contains("show")) renderSitePopup();
      if (riskProfileMainChartInstance) riskProfileMainChartInstance.resize();
      riskProfileMiniChartInstances.forEach(chart => chart.resize());
    });

    setupRiskProfileSiteFilter();
    bindImprovementRekapTable();
    bindRiskMatrixCells();
    renderAll();
  </script>
</body>
</html>
