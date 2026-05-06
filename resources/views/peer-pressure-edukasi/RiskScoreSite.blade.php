<!DOCTYPE html>
<html class="light" lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Risk Driver Correlation Dashboard | Week 18 2026</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
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
            line: "#dfe3e6"
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
        @apply m-0 min-h-screen text-ink font-body;
        background:
          radial-gradient(circle at top left, rgba(77, 178, 92, 0.16), transparent 28rem),
          radial-gradient(circle at top right, rgba(245, 189, 36, 0.14), transparent 24rem),
          var(--bg);
      }

      button,
      select { font: inherit; }

      button,
      select {
        @apply cursor-pointer rounded-full border border-line bg-white px-3.5 py-2.5 text-[13px] font-semibold text-ink shadow-[0_8px_18px_rgba(15,23,42,0.07)] transition duration-200 ease-in-out;
      }

      button:hover,
      select:hover { @apply -translate-y-px shadow-[0_12px_28px_rgba(31,60,42,0.10)]; }

      button.active { @apply border-ink bg-ink text-white; }

      table { @apply w-full min-w-[860px] border-collapse; }
      th, td { @apply border-b border-[#edf1f5] px-3.5 py-3 text-left align-middle text-[13px]; }
      th { @apply whitespace-nowrap bg-[#f8fafc] text-[10px] font-bold uppercase tracking-[.07em] text-[#5f6772]; }
      tr:last-child td { @apply border-b-0; }

      [hidden] { display: none !important; }
    }

    @layer components {
      .app-shell { width: min(1120px, calc(100% - 28px)); @apply mx-auto pt-6 pb-[60px]; }

      .hero { @apply relative overflow-hidden rounded-2xl p-7 text-white shadow-soft border border-[#1b3f87]/20; background: linear-gradient(140deg, #243d8f 0%, #3952bc 52%, #5d75d8 100%); }
      .hero-content { @apply relative z-[2] grid grid-cols-[1.4fr_.9fr] items-end gap-6; }
      .hero::before, .hero::after { content: ""; @apply absolute rounded-full bg-white/15; }
      .hero::before { @apply -right-[110px] -top-[130px] h-[330px] w-[330px]; }
      .hero::after { content: ""; @apply -bottom-[180px] right-40 h-[260px] w-[260px] rounded-full bg-[#ffd652]/20; }

      .eyebrow { @apply mb-3.5 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-3 py-2 text-[11px] font-bold uppercase tracking-[.1em] backdrop-blur; }
      .dot { @apply h-2 w-2 rounded-full bg-[#ffd85c] shadow-[0_0_0_5px_rgba(255,216,92,0.15)]; }
      .hero h1 { @apply m-0 text-[clamp(28px,4vw,44px)] font-extrabold leading-[1.06] tracking-[-.03em]; }
      .subtitle { @apply mt-3 max-w-[760px] text-[14px] leading-[1.6] text-white/85 font-medium; }

      .hero-kpis { @apply grid grid-cols-2 gap-3; }
      .hero-kpi { @apply rounded-2xl border border-white/20 bg-white/10 p-4 backdrop-blur-md; }
      .hero-kpi .label { @apply mb-2 text-[11px] text-white/75 font-semibold; }
      .hero-kpi .value { @apply text-[25px] font-extrabold tracking-[-.02em]; }
      .hero-kpi .note { @apply mt-1 text-[11px] text-white/80; }

      .toolbar { @apply my-5 flex flex-wrap items-center justify-between gap-3; }
      .tabs, .filters { @apply flex flex-wrap items-center gap-2.5; }

      .page-intro { @apply mb-[18px] grid grid-cols-[1fr_auto] items-center gap-4; }
      .page-intro h2 { @apply m-0 mb-1.5 text-[24px] tracking-[-.02em] font-extrabold text-[#1f2937]; }
      .page-intro p { @apply m-0 text-[13px] leading-[1.55] text-muted; }
      .page-counter { @apply inline-flex min-w-[72px] items-center justify-center whitespace-nowrap rounded-full bg-[#e9f2ff] px-3 py-2.5 text-xs font-black text-[#1559a8]; }

      .grid { @apply gap-[18px]; }
      .grid-4 { @apply grid-cols-4; }
      .grid-2 { @apply grid-cols-2; }
      .card {
        @apply rounded-card border border-slate-200 bg-white p-5;
        box-shadow: 0 4px 0px 0px rgba(15, 23, 42, 0.04), 0 14px 24px -10px rgba(15, 23, 42, 0.2);
        transition: transform .2s ease, box-shadow .2s ease;
      }
      .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 0px 0px rgba(15, 23, 42, 0.04), 0 18px 34px -12px rgba(15, 23, 42, 0.26);
      }
      .card-title { @apply mb-3.5 flex items-start justify-between gap-3; }
      .card-title h2, .card-title h3 { @apply m-0 leading-[1.12] tracking-[-.03em]; }
      .card-title h2 { @apply text-[22px] font-extrabold text-[#1f2937]; }
      .card-title h3 { @apply text-lg font-bold text-[#1f2937]; }

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
      .score-card { @apply relative min-h-[186px] cursor-pointer overflow-hidden transition duration-200 ease-in-out border border-slate-200; }
      .score-card:hover { @apply -translate-y-[3px]; }
      .score-card.selected { outline: 2px solid rgba(57,82,188,.28); }
      .score-card::after { content: ""; @apply absolute -bottom-[75px] -right-[50px] h-[170px] w-[170px] rounded-full bg-[#3952bc]/10; }
      .score-top { @apply relative z-[2] flex items-start justify-between gap-2.5; }
      .site-name { @apply m-0 text-[25px] font-extrabold tracking-[-.03em] text-[#1f2937]; }
      .risk-score { @apply mt-[18px] text-[34px] font-extrabold tracking-[-.04em] text-[#111827]; }

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

      .scatter-box { @apply relative min-h-[420px] overflow-hidden rounded-[22px] border border-line; background: linear-gradient(90deg, rgba(51,168,82,.10) 0 50%, rgba(217,40,40,.10) 50% 100%), linear-gradient(0deg, rgba(51,168,82,.08) 0 50%, rgba(239,138,33,.08) 50% 100%), #fff; }
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
      .hero-content,
      .grid-4,
      .grid-2,
      .popup-grid { @apply grid-cols-1; }

      .hero-kpis { @apply grid-cols-2; }
      .scatter-box { @apply min-h-[360px]; }
      .bridge { @apply grid-cols-1; }
      .site-popup { position: fixed; left: 14px !important; right: 14px !important; bottom: 14px; top: auto !important; width: auto; max-height: 78vh; overflow: auto; }
      .site-popup::before { display: none; }
    }

    @media (max-width: 620px) {
      .page-intro { @apply grid-cols-1; }
      .page-counter { @apply justify-self-start; }
      .app-shell { width: min(100% - 18px, 1120px); @apply pt-2.5; }
      .hero { @apply rounded-[22px] p-5; }
      .hero-kpis { @apply grid-cols-1; }
      .card { @apply rounded-[20px] p-4; }
      .toolbar { @apply items-stretch; }
      .tabs, .filters { @apply w-full; }
      button, select { @apply flex-1; }
      .driver-row { @apply grid-cols-[122px_1fr_34px]; }
      .metric-line { @apply grid-cols-[76px_1fr_34px]; }
      .summary-item { @apply grid-cols-[34px_1fr]; }
      .summary-item .badge { @apply col-start-2 justify-self-start; }
    }
  </style>
</head>
<body class="bg-[#f0f2f5] font-body text-on-surface min-h-screen flex flex-col">
  <header class="w-full sticky top-0 bg-[#ffffff] border-b border-[#dfe3e6] z-50 shadow-sm">
    <div class="mx-auto px-8 py-4 flex justify-between items-center">
      <div class="flex items-center gap-10">
        <div class="flex flex-col">
          <h1 class="font-headline font-bold text-[#3952bc] text-xl tracking-tighter leading-tight">OHS Division</h1>
          <p class="text-on-surface-variant text-[10px] font-bold uppercase tracking-widest">Safety Performance Review</p>
        </div>
        <div class="h-8 w-px bg-[#dfe3e6] hidden lg:block"></div>
        <nav class="hidden md:flex gap-8">
          <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="{{ route('peer-pressure-edukasi.dashboard', request()->only(['year', 'month', 'q', 'hazard_site'])) }}">Lagging</a>
          <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="{{ route('peer-pressure-edukasi.dashboard-performance', request()->only(['year', 'month', 'q', 'hazard_site'])) }}">Dash Performance</a>
          <a class="text-[#3952bc] border-b-2 border-[#3952bc] pb-1 font-bold text-sm tracking-tight" href="{{ route('peer-pressure-edukasi.dashboard-risk-score', request()->only(['year', 'month', 'q', 'hazard_site'])) }}">Risk Score Site</a>
          <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="{{ route('peer-pressure-edukasi.tematic', request()->only(['year', 'month', 'q', 'hazard_site'])) }}">Thematic Alignment</a>
          <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Site Notic</a>
        </nav>
      </div>
      <div class="flex items-center gap-6">
        <div class="relative group hidden xl:block">
          <input class="bg-[#f5f7f9] border-none rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-primary w-80 transition-all shadow-inner" placeholder="Search safety records..." type="text"/>
          <span class="material-symbols-outlined absolute right-3 top-2 text-on-surface-variant">search</span>
        </div>
        <div class="flex items-center gap-3">
          <button type="button" class="p-2 hover:bg-[#dfe3e6] rounded-full transition-colors relative">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-error border-2 border-white rounded-full"></span>
          </button>
          <button type="button" class="flex items-center gap-2 p-1.5 pr-4 bg-white hover:bg-[#dfe3e6] rounded-full transition-colors border border-outline-variant/30 shadow-sm">
            <span class="material-symbols-outlined text-3xl text-primary">account_circle</span>
            <div class="text-left">
              <p class="text-[10px] font-bold text-primary uppercase leading-none">Safety Admin</p>
              <p class="text-[9px] text-on-surface-variant font-medium">Site Manager</p>
            </div>
          </button>
        </div>
      </div>
    </div>
  </header>

  <main class="flex-grow w-full mx-auto p-8 space-y-8">
    <div class="app-shell">
    <section class="hero">
      <div class="hero-content">
        <div>
          <div class="eyebrow"><span class="dot"></span> Risk Driver Correlation</div>
          <h1>Overview Risk Site & Korelasi Driver Kontraktor</h1>
          <p class="subtitle">Week 18 Tahun 2026 · Menghubungkan risk score site dengan empat driver utama: Safety Accountability, Coverage Quality, Exposure/RFID, dan Fatigue Management.</p>
        </div>
        <div class="hero-kpis">
          <div class="hero-kpi">
            <div class="label">Prioritas Utama</div>
            <div class="value">BMO 1</div>
            <div class="note">Risk score 39.47% · lonjakan tertinggi</div>
          </div>
          <div class="hero-kpi">
            <div class="label">Benchmark</div>
            <div class="value">LMO</div>
            <div class="note">Risk score 19.64% · paling stabil</div>
          </div>
          <div class="hero-kpi">
            <div class="label">Driver Dominan</div>
            <div class="value">Blindspot & Fatigue</div>
            <div class="note">GMO fatigue spike diklasifikasikan Critical</div>
          </div>
          <div class="hero-kpi">
            <div class="label">Metode Korelasi</div>
            <div class="value">W15–W18</div>
            <div class="note">Akumulasi driver 4 minggu terhadap risk W18</div>
          </div>
        </div>
      </div>
    </section>

    <div class="toolbar">
      <div class="tabs" id="tabs">
        <button class="active" type="button" data-view="overview">Overview</button>
        <button type="button" data-view="driver">Driver Kontraktor</button>
        <button type="button" data-view="correlation">Correlation Bridge</button>
        <button type="button" data-view="action">Action Priority</button>
      </div>
      <div class="filters" data-page-only="overview">
        <select id="riskFilter" aria-label="Pilih Risk Status">
          <option value="ALL">Semua Status</option>
          <option value="High Risk">High Risk</option>
          <option value="Unstable">Unstable</option>
          <option value="Best Profile">Best Profile</option>
        </select>
      </div>
    </div>

    <section class="card page-intro" id="pageIntro"></section>

    <div class="cards-wrap" data-page-only="overview">
      <section class="grid grid-4" id="siteCards"></section>
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
          <span class="badge green">AI Generated</span>
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

  <script>
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
        title: "Overview Risk Site",
        description: "Halaman ini menampilkan ringkasan risk score per site, ranking W18, kontribusi driver utama, scatter korelasi, serta kamus komponen driver.",
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

    let selectedSite = "BMO 1";
    let activeView = "overview";

    const riskFilter = document.getElementById("riskFilter");
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
      const statusFilter = riskFilter.value;
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
      tooltip.textContent = element.dataset.tooltip || "";
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

    document.getElementById("tabs").addEventListener("click", event => {
      const button = event.target.closest("button[data-view]");
      if (!button) return;
      switchView(button.dataset.view);
    });

    siteCards.addEventListener("click", event => {
      const card = event.target.closest(".score-card");
      if (!card) return;
      selectSite(card.dataset.site);
    });

    document.getElementById("scatterBox").addEventListener("click", event => {
      const point = event.target.closest(".point");
      if (!point) return;
      selectSite(point.dataset.site);
    });

    riskFilter.addEventListener("change", () => {
      if (activeView !== "overview") return;
      renderCards();
      closeSitePopup();
    });

    document.addEventListener("click", event => {
      if (event.target.closest("[data-close-popup='true']")) {
        closeSitePopup();
        return;
      }
      if (!sitePopup.classList.contains("show")) return;
      if (event.target.closest(".score-card") || event.target.closest("#sitePopup")) return;
      closeSitePopup();
    });

    window.addEventListener("resize", () => {
      if (sitePopup.classList.contains("show")) renderSitePopup();
    });

    renderAll();
  </script>
</body>
</html>
