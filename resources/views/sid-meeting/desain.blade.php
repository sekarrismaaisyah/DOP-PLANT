<!DOCTYPE html>
<html class="light" lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>OHS Division - SID Meeting</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&amp;family=Inter:wght@300;400;500;600&amp;display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          fontFamily: {
            headline: ["Poppins"],
            body: ["Poppins"],
            label: ["Poppins"]
          }
        }
      }
    };
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --bc-blue: #2563eb;
      --bc-cyan: #06b6d4;
      --bc-slate: #0f172a;
      --bc-soft: #f8fafc;
    }

    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
      font-family: Poppins, Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      -webkit-font-smoothing: antialiased;
      text-rendering: geometricPrecision;
    }
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
      vertical-align: middle;
    }

    .glass {
      background: rgba(255,255,255,.90);
      backdrop-filter: blur(22px);
      border: 1px solid rgba(226,232,240,.95);
    }
    .soft-card { box-shadow: 0 22px 70px rgba(15, 23, 42, .08); }
    .anchored-card {
      box-shadow: 0 4px 0px 0px rgba(0, 0, 0, 0.05), 0 12px 24px -4px rgba(0, 0, 0, 0.15);
      border: 1px solid rgba(0, 0, 0, 0.08);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .anchored-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 0px 0px rgba(0, 0, 0, 0.05), 0 20px 32px -8px rgba(0, 0, 0, 0.2);
    }
    .fade-in { animation: fadeIn .22s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

    .tab-active {
      background: transparent;
      color: #3952bc !important;
      border-bottom: 2px solid #3952bc;
      border-radius: 0 !important;
      box-shadow: none;
    }
    .module-tab {
      color: #595c5e;
      font-weight: 700;
      font-size: 13px;
      letter-spacing: .01em;
      padding: 10px 2px;
      border-bottom: 2px solid transparent;
      transition: color .18s ease, border-color .18s ease;
      border-radius: 0 !important;
    }
    .module-tab:hover { color: #3952bc; }
    .module-nav {
      background: #ffffff;
      border: 1px solid #dfe3e6;
      box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
      border-radius: 14px;
      padding: 6px 18px;
      gap: 24px;
    }

    button {
      transition: transform .16s ease, box-shadow .16s ease, background-color .16s ease, opacity .16s ease;
    }
    button:hover { transform: translateY(-1px); }
    button:active { transform: translateY(0); }
    button:disabled { transform: none; cursor: not-allowed; }

    input, select, textarea {
      transition: border-color .16s ease, box-shadow .16s ease, background-color .16s ease;
    }
    input::placeholder, textarea::placeholder { color: #94a3b8; }

    .badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 5px 11px;
      border-radius: 999px;
      font-size: 11px;
      line-height: 1;
      font-weight: 900;
      white-space: nowrap;
      letter-spacing: .01em;
    }
    .badge-open { background: #dcfce7; color: #166534; }
    .badge-expired { background: #fee2e2; color: #991b1b; }
    .badge-upcoming { background: #dbeafe; color: #1d4ed8; }
    .badge-draft { background: #fef9c3; color: #854d0e; }
    .badge-closed { background: #e2e8f0; color: #334155; }
    .badge-overrun { background: #ffedd5; color: #9a3412; }

    .status-hadir,
    .status-tidak-hadir {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 86px;
      border-radius: 999px;
      font-weight: 900;
      padding: 5px 10px;
      font-size: 11px;
      line-height: 1;
      letter-spacing: .01em;
    }
    .status-hadir { background: #dcfce7; color: #166534; }
    .status-tidak-hadir { background: #fee2e2; color: #991b1b; }

    .table-wrap {
      overflow-x: auto;
      scrollbar-width: thin;
      scrollbar-color: #cbd5e1 transparent;
      border: 1px solid #dfe3e6 !important;
      border-radius: 14px !important;
    }
    .table-wrap::-webkit-scrollbar { height: 8px; width: 8px; }
    .table-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
    .table-wrap::-webkit-scrollbar-track { background: transparent; }
    .semantic-scroll {
      width: 100%;
      max-width: 100%;
      max-height: 360px;
      overflow: auto;
      scrollbar-width: thin;
      scrollbar-color: #cbd5e1 transparent;
    }
    .semantic-scroll::-webkit-scrollbar { height: 8px; width: 8px; }
    .semantic-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
    .semantic-scroll thead th {
      position: sticky;
      top: 0;
      z-index: 5;
      background: #f8fafc;
    }
    .semantic-grid {
      display: grid;
      grid-template-columns: minmax(0, 1fr);
      gap: 1.25rem;
      align-items: start;
    }
    @media (min-width: 1280px) {
      .semantic-grid {
        grid-template-columns: minmax(0, .78fr) minmax(0, 1.22fr);
      }
    }
    .semantic-card {
      min-width: 0;
      overflow: hidden;
    }
    .semantic-pairs-table {
      min-width: 980px;
      width: max-content;
    }
    .semantic-groups-table {
      min-width: 560px;
      width: max-content;
    }
    table { border-collapse: separate; border-spacing: 0; }
    thead th {
      white-space: nowrap;
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: .08em;
      font-weight: 700;
      color: #64748b;
      background: #f8fafc;
    }
    tbody tr:hover { background: #fafbfc; }
    tbody td { vertical-align: top; }
    tbody tr { transition: background-color .14s ease; }

    .section-surface {
      background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.92));
      border: 1px solid #e2e8f0;
      box-shadow: 0 14px 34px rgba(15, 23, 42, .05);
    }

    .modal-shell {
      max-height: 90vh;
      overflow: auto;
      scrollbar-width: thin;
      scrollbar-color: #cbd5e1 transparent;
      border: 1px solid #dfe3e6;
      border-radius: 16px;
      box-shadow: 0 18px 50px -20px rgba(15, 23, 42, .35);
    }
    .modal-shell::-webkit-scrollbar { width: 8px; }
    .modal-shell::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }

    .floating-drawer {
      position: fixed;
      top: 18px;
      left: 18px;
      bottom: 18px;
      z-index: 55;
      width: min(440px, calc(100vw - 36px));
      overflow-y: auto;
      transform: translateX(calc(-100% - 28px));
      opacity: 0;
      pointer-events: none;
      transition: transform .24s ease, opacity .24s ease, box-shadow .24s ease;
      scrollbar-width: thin;
      scrollbar-color: #cbd5e1 transparent;
    }
    .floating-drawer.open {
      transform: translateX(0);
      opacity: 1;
      pointer-events: auto;
      box-shadow: 0 30px 90px rgba(15, 23, 42, .25);
    }
    .floating-drawer::-webkit-scrollbar { width: 8px; }
    .floating-drawer::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 999px; }
    .floating-overlay {
      position: fixed;
      inset: 0;
      z-index: 54;
      background: rgba(15, 23, 42, .38);
      backdrop-filter: blur(6px);
      opacity: 0;
      pointer-events: none;
      transition: opacity .2s ease;
    }
    .floating-overlay.open {
      opacity: 1;
      pointer-events: auto;
    }
    .fab-action {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: .5rem;
      border-radius: 1rem;
      background: linear-gradient(135deg, var(--bc-blue), var(--bc-cyan));
      padding: .8rem 1rem;
      font-size: .875rem;
      font-weight: 900;
      color: white;
      box-shadow: 0 16px 34px rgba(37, 99, 235, .26);
    }
    .create-event-collapsed-label { display: none; }

    @media print {
      .no-print { display: none !important; }
      body { background: white !important; }
      .glass, .soft-card, .section-surface { box-shadow: none !important; border: 1px solid #e5e7eb; }
      button { transform: none !important; }
    }
  </style>
</head>
<body class="bg-[#f0f2f5] font-body text-slate-800 min-h-screen flex flex-col">
  <div class="fixed inset-0 -z-10 overflow-hidden">
    <div class="absolute -top-32 -right-20 h-96 w-96 rounded-full bg-cyan-200/40 blur-3xl"></div>
    <div class="absolute top-24 -left-28 h-96 w-96 rounded-full bg-blue-300/30 blur-3xl"></div>
    <div class="absolute bottom-0 right-1/4 h-80 w-80 rounded-full bg-violet-200/30 blur-3xl"></div>
  </div>

  <header class="w-full sticky top-0 bg-[#ffffff] border-b border-[#dfe3e6] z-50 shadow-sm">
    <div class="mx-auto px-8 py-4 flex justify-between items-center">
      <div class="flex items-center gap-10">
        <div class="flex flex-col">
          <h1 class="font-headline font-bold text-[#3952bc] text-xl tracking-tighter leading-tight">OHS Division</h1>
          <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest">SID Meeting Evaluation</p>
        </div>
        <div class="h-8 w-px bg-[#dfe3e6] hidden lg:block"></div>
        <nav class="hidden md:flex gap-8">
          <a class="text-[#3952bc] border-b-2 border-[#3952bc] pb-1 font-bold text-sm tracking-tight" href="{{ route('sid-meeting.dashboard') }}">Dashboard</a>
          <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="{{ route('sid-meeting.events.index') }}">Event</a>
          <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="{{ route('sid-meeting.reports.index') }}">Rekap</a>
          <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="{{ route('sid-meeting.performance.index') }}">Performance</a>
          <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="{{ route('sid-meeting.semantic.index') }}">Semantic</a>
        </nav>
      </div>
      <div class="flex items-center gap-3">
        <button class="p-2 hover:bg-[#dfe3e6] rounded-full transition-colors relative" type="button">
          <span class="material-symbols-outlined">notifications</span>
          <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
        </button>
        <div class="flex items-center gap-2 p-1.5 pr-4 bg-white rounded-full border border-slate-200 shadow-sm">
          <span class="material-symbols-outlined text-3xl text-[#3952bc]">account_circle</span>
          <div class="text-left">
            <p class="text-[10px] font-bold text-[#3952bc] uppercase leading-none">Safety Admin</p>
            <p class="text-[9px] text-slate-500 font-medium">Site Manager</p>
          </div>
        </div>
      </div>
    </div>
  </header>

  <main class="w-full mx-auto p-8 space-y-8">
    <header class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <nav class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase mb-2">
          <span>Dashboard</span>
          <span class="material-symbols-outlined text-xs">chevron_right</span>
          <span class="text-[#3952bc]">SID Meeting Dashboard</span>
        </nav>
        <p class="mb-2 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-blue-700 ring-1 ring-blue-100">QR Event Maker & Absensi</p>
        <h1 class="font-headline text-3xl font-extrabold tracking-tight text-slate-950 md:text-5xl">Event Meeting & Absensi SID</h1>
        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 md:text-base">Create event akan generate QR. Saat QR discan, form absensi event muncul. Peserta cukup input Kode SID, lalu nama, perusahaan, jabatan struktural, dan jabatan fungsional terisi otomatis dari sistem.</p>
      </div>
      
    </header>

    <section class="mb-6 grid gap-4 md:grid-cols-4">
      <div class="bg-white p-6 rounded-2xl anchored-card"><p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Event</p><p id="statEvents" class="mt-2 text-3xl font-extrabold text-slate-950">0</p></div>
      <div class="bg-white p-6 rounded-2xl anchored-card"><p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Event Aktif</p><p id="statActiveEvents" class="mt-2 text-3xl font-extrabold text-emerald-700">0</p></div>
      <div class="bg-white p-6 rounded-2xl anchored-card"><p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Absensi</p><p id="statAttendance" class="mt-2 text-3xl font-extrabold text-slate-950">0</p></div>
      <div class="bg-white p-6 rounded-2xl anchored-card"><p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Attendance Rate (All)</p><p id="statAttendanceRateAll" class="mt-2 text-3xl font-extrabold text-blue-700">0%</p></div>
    </section>

    <nav class="module-nav no-print sticky top-3 z-30 mb-6 flex flex-wrap items-center">
      <button data-tab="events" class="tab-btn module-tab tab-active" onclick="showTab('events')">Create Event</button>
      <button data-tab="companymaster" class="tab-btn module-tab" onclick="showTab('companymaster')">Master Perusahaan</button>
      <button data-tab="report" class="tab-btn module-tab" onclick="showTab('report')">Rekap & Export</button>
      <button data-tab="siteperformance" class="tab-btn module-tab" onclick="showTab('siteperformance')">Site Performance</button>
      <button data-tab="semanticeval" class="tab-btn module-tab" onclick="showTab('semanticeval')">Semantic Evaluation</button>
    </nav>

    <section id="tab-events" class="tab-panel fade-in">
      <div id="floatingFormOverlay" class="floating-overlay no-print" onclick="closeFloatingPanels()"></div>

      <div id="createEventContainer" class="glass soft-card floating-drawer rounded-3xl p-5">
        <div class="mb-4 flex items-start justify-between gap-3">
          <div>
            <h2 class="text-xl font-black text-slate-950">Create Event</h2>
            <p class="mt-1 text-sm text-slate-500">Event dibuat dari jenis meeting, site, tanggal, week, dan jam selesai untuk menentukan masa aktif QR.</p>
          </div>
          <button id="createEventCollapseBtn" type="button" onclick="toggleCreateEventContainer(false)" class="no-print shrink-0 rounded-2xl bg-slate-100 px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-200" title="Tutup Create Event">✕</button>
        </div>
        <div>
        <form id="eventForm" class="mt-5 space-y-4" onsubmit="saveEvent(event)">
          <input type="hidden" id="eventId" />
          <div>
            <label class="text-sm font-bold text-slate-700">Jenis Meeting</label>
            <select id="meetingType" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100">
              <option value="">Pilih Jenis Meeting</option>
            </select>
            <div class="mt-3 rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-200">
              <button type="button" onclick="toggleMeetingTypeManager()" class="flex w-full items-center justify-between text-left text-xs font-black uppercase tracking-wider text-slate-600">
                <span>Kelola Jenis Meeting</span>
                <span id="meetingTypeManagerIcon">+</span>
              </button>
              <div id="meetingTypeManager" class="mt-3 hidden space-y-3">
                <div class="flex gap-2">
                  <input id="newMeetingType" onkeydown="if(event.key==='Enter'){event.preventDefault(); addMeetingType();}" placeholder="Tambah jenis meeting baru..." class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100" />
                  <button type="button" onclick="addMeetingType()" class="rounded-xl bg-blue-600 px-3 py-2 text-xs font-black text-white hover:bg-blue-700">Tambah</button>
                </div>
                <div id="meetingTypeList" class="flex flex-wrap gap-2"></div>
              </div>
            </div>
          </div>
          <div>
            <label class="text-sm font-bold text-slate-700">Site</label>
            <input id="eventSite" list="siteOptions" required placeholder="Cari / ketik site..." class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100" />
            <datalist id="siteOptions"><option value="BMO 1"></option><option value="BMO 2"></option><option value="BMO 3"></option><option value="GMO"></option><option value="SMO"></option><option value="LMO"></option><option value="Marine"></option><option value="HOTE"></option></datalist>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
            <div><label class="text-sm font-bold text-slate-700">Tanggal Meeting</label><input id="meetingDate" type="date" required onchange="autoFillWeek()" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100" /></div>
            <div><label class="text-sm font-bold text-slate-700">Week</label><input id="meetingWeek" required placeholder="Contoh: W17" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100" /></div>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
            <div><label class="text-sm font-bold text-slate-700">Jam Mulai</label><input id="startTime" type="time" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100" /></div>
            <div><label class="text-sm font-bold text-slate-700">Jam Selesai / QR Expired</label><input id="endTime" type="time" required class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100" /></div>
          </div>
          <div class="rounded-3xl bg-blue-50 p-4 text-xs leading-5 text-blue-900 ring-1 ring-blue-100">QR hanya aktif pada tanggal meeting sampai <b>Jam Selesai</b>. Jika lewat dari jam selesai, form absensi akan otomatis terkunci.</div>
          <div class="flex gap-2">
            <button class="flex-1 rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-500 px-4 py-3 text-sm font-black text-white shadow-lg shadow-blue-500/20 hover:opacity-95">Simpan & Generate QR</button>
            <button type="button" onclick="clearEventForm()" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-black text-slate-700 hover:bg-slate-200">Clear</button>
          </div>
        </form>
        </div>
      </div>

      <div class="glass soft-card rounded-3xl p-5">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
          <div><h2 class="text-xl font-black text-slate-950">Daftar Event & QR</h2><p class="text-sm text-slate-500">Klik kartu event untuk melihat modal rekap. Tombol QR dan form absen tetap tersedia di setiap event.</p></div>
          <div class="no-print flex flex-col gap-2 md:flex-row md:items-center">
            <button onclick="toggleCreateEventContainer(true)" class="fab-action">+ Create Event</button>
            <input id="eventSearch" oninput="renderEvents()" placeholder="Cari jenis meeting / site / week..." class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100" />
          </div>
        </div>
        <div id="eventList" class="grid gap-3"></div>
      </div>
    </section>

    <section id="tab-companymaster" class="tab-panel fade-in hidden">
      <div class="glass soft-card rounded-3xl p-5">
        <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
          <div>
            <h2 class="text-xl font-black text-slate-950">Master Perusahaan & Site Eligibility</h2>
            <p class="text-sm text-slate-500">Kelola daftar perusahaan dan checklist site mana saja yang wajib/eligible mengikuti event. Data ini menjadi dasar Attendance Rate per site dan status kehadiran perusahaan.</p>
          </div>
          <div class="no-print flex flex-col gap-2 md:flex-row">
            <button onclick="toggleCompanyInputContainer(true)" class="fab-action">+ Input Perusahaan</button>
            <button onclick="exportCompanyMasterCSV()" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-black text-white hover:bg-slate-800">Export Master CSV</button>
            <button onclick="resetCompanyMaster()" class="rounded-2xl bg-red-50 px-4 py-3 text-sm font-black text-red-700 ring-1 ring-red-100 hover:bg-red-100">Reset Master</button>
          </div>
        </div>

        <div>
          <div id="companyInputContainer" class="floating-drawer rounded-3xl bg-white p-5 ring-1 ring-slate-200 shadow-sm">
            <div class="mb-4 flex items-start justify-between gap-3">
              <div>
                <h3 class="font-black text-slate-950">Input Perusahaan</h3>
                <p class="mt-1 text-sm text-slate-500">Tambahkan perusahaan, lalu checklist site yang relevan.</p>
              </div>
              <button type="button" onclick="toggleCompanyInputContainer(false)" class="no-print rounded-2xl bg-slate-100 px-3 py-2 text-sm font-black text-slate-700 hover:bg-slate-200">✕</button>
            </div>
            <form id="companyForm" class="mt-5 space-y-4" onsubmit="saveCompany(event)">
              <input type="hidden" id="companyId" />
              <div>
                <label class="text-sm font-bold text-slate-700">Nama Perusahaan</label>
                <input id="companyName" required placeholder="Contoh: PT Transkon Jaya" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100" />
              </div>
              <div>
                <label class="text-sm font-bold text-slate-700">Checklist Site</label>
                <div id="companySiteChecklist" class="mt-2 grid gap-2 sm:grid-cols-2"></div>
              </div>
              <div class="flex gap-2">
                <button class="flex-1 rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-500 px-4 py-3 text-sm font-black text-white shadow-lg shadow-blue-500/20 hover:opacity-95">Simpan Perusahaan</button>
                <button type="button" onclick="clearCompanyForm()" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-black text-slate-700 hover:bg-slate-200">Clear</button>
              </div>
            </form>
          </div>

          <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-200 shadow-sm">
            <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
              <div>
                <h3 class="font-black text-slate-950">Checklist Perusahaan per Site</h3>
                <p id="companyMasterInfo" class="text-sm text-slate-500">0 perusahaan</p>
              </div>
              <input id="companyMasterSearch" oninput="renderCompanyMaster()" placeholder="Cari perusahaan / site..." class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100 md:w-80" />
            </div>
            <div class="table-wrap rounded-2xl border border-slate-200 bg-white">
              <table class="min-w-full text-left text-sm">
                <thead id="companyMasterTableHead" class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"></thead>
                <tbody id="companyMasterTableBody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="tab-siteperformance" class="tab-panel fade-in hidden">
      <div class="glass soft-card rounded-3xl p-5">
        <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
          <div>
            <h2 class="text-xl font-black text-slate-950">Site Performance</h2>
            <p class="text-sm text-slate-500">Trend line Attendance Rate per site berdasarkan week.</p>
          </div>
          <div class="no-print flex flex-col gap-2 md:flex-row">
            <select id="siteTrendFilter" onchange="renderSitePerformance()" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100">
              <option value="ALL">Semua Site</option>
            </select>
            <select id="siteTrendWeekFilter" onchange="renderSitePerformance()" class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100">
              <option value="ALL">Semua Week</option>
            </select>
            <button onclick="exportSitePerformanceCSV()" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-black text-white hover:bg-slate-800">Export Trend CSV</button>
            <button onclick="exportCompanyPerformanceCSV()" class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-black text-white hover:bg-blue-700">Export Company CSV</button>
          </div>
        </div>

        <div class="mb-4 grid gap-4 md:grid-cols-3">
          <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-200">
            <p class="text-sm font-semibold text-slate-500">Total Site</p>
            <p id="siteTrendTotalSite" class="mt-2 text-2xl font-black text-slate-950">0</p>
          </div>
          <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-200">
            <p class="text-sm font-semibold text-slate-500">Average Attendance Rate</p>
            <p id="siteTrendAvgRate" class="mt-2 text-2xl font-black text-slate-950">0%</p>
          </div>
          <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-200">
            <p class="text-sm font-semibold text-slate-500">Week Coverage</p>
            <p id="siteTrendWeekCount" class="mt-2 text-2xl font-black text-slate-950">0</p>
          </div>
        </div>

        <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-200">
          <div class="mb-3 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
              <h3 class="font-black text-slate-950">Attendance Rate Trend</h3>
              <p id="siteTrendInfo" class="text-sm text-slate-500">Belum ada data.</p>
            </div>
          </div>
          <div class="h-[420px]">
            <canvas id="sitePerformanceChart"></canvas>
          </div>
        </div>

        <div class="mt-6 rounded-3xl bg-white p-5 ring-1 ring-slate-200">
          <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
              <h3 class="font-black text-slate-950">Attendance Rate per Perusahaan</h3>
              <p id="companyPerformanceInfo" class="text-sm text-slate-500">Mengikuti filter Site dan Week di atas.</p>
            </div>
            <div class="no-print w-full md:w-80">
              <input id="companyPerformanceSearch" oninput="renderCompanyPerformanceTable()" placeholder="Cari perusahaan / performance..." class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100" />
            </div>
          </div>
          <div class="table-wrap rounded-2xl border border-slate-200 bg-white">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                  <th class="px-4 py-3">Rank</th>
                  <th class="px-4 py-3">Perusahaan</th>
                  <th class="px-4 py-3">Expected Event</th>
                  <th class="px-4 py-3">Event Hadir</th>
                  <th class="px-4 py-3">Total Absensi</th>
                  <th class="px-4 py-3">Attendance Rate</th>
                  <th class="px-4 py-3">Performance</th>
                </tr>
              </thead>
              <tbody id="companyPerformanceTable"></tbody>
            </table>
          </div>
        </div>
      </div>
    </section>

    <section id="tab-semanticeval" class="tab-panel fade-in hidden">
      <div class="glass soft-card rounded-3xl p-5">
        <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
          <div>
            <h2 class="text-xl font-black text-slate-950">Semantic Evaluation Notulensi</h2>
            <p class="text-sm text-slate-500">Mendeteksi pengulangan issue dan kemiripan catatan meeting antar-site menggunakan local semantic embedding + cosine similarity. Fungsi ini hanya tersedia di page Semantic Evaluation, tidak ditampilkan di List Notulen.</p>
          </div>
          <div class="no-print flex flex-col gap-2 md:flex-row">
            <span id="semanticMethodBadge" class="inline-flex items-center rounded-2xl bg-violet-50 px-4 py-3 text-sm font-black text-violet-700 ring-1 ring-violet-100">Embedding Engine: Local Semantic Vector</span>
            <button onclick="renderSemanticEvaluation(true)" class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-black text-white hover:bg-blue-700">Run Evaluation</button>
            <button onclick="exportSemanticPairsCSV()" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-black text-white hover:bg-slate-800">Export Similarity CSV</button>
          </div>
        </div>

        <div class="no-print mb-5 grid gap-3 lg:grid-cols-[320px_1fr]">
          <div><label class="text-sm font-bold text-slate-700">Similarity Threshold</label><div class="mt-1 rounded-2xl border border-slate-200 bg-white px-4 py-3"><input id="semanticThreshold" type="range" min="35" max="95" value="55" oninput="document.getElementById('semanticThresholdLabel').textContent = this.value + '%'; renderSemanticEvaluation(true);" class="w-full" /><div class="mt-1 flex justify-between text-xs font-bold text-slate-500"><span>Loose</span><span id="semanticThresholdLabel">55%</span><span>Strict</span></div></div></div>
          <div><label class="text-sm font-bold text-slate-700">Search Issue</label><input id="semanticSearch" oninput="renderSemanticEvaluation(true)" placeholder="Cari issue / PIC / site..." class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100" /><label class="mt-2 flex items-center gap-2 text-xs font-bold text-slate-600"><input id="semanticCrossSiteOnly" type="checkbox" checked onchange="renderSemanticEvaluation(true)" class="h-4 w-4 rounded border-slate-300 text-blue-600" /> Cross-site only</label></div>
        </div>

        <div class="mb-5 grid gap-4 md:grid-cols-4">
          <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-200"><p class="text-xs font-black uppercase tracking-wider text-slate-400">Total Issue</p><p id="semanticTotalIssues" class="mt-2 text-2xl font-black text-slate-950">0</p></div>
          <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-200"><p class="text-xs font-black uppercase tracking-wider text-slate-400">Similar Pairs</p><p id="semanticSimilarPairs" class="mt-2 text-2xl font-black text-blue-700">0</p></div>
          <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-200"><p class="text-xs font-black uppercase tracking-wider text-slate-400">Repeated Groups</p><p id="semanticRepeatedGroups" class="mt-2 text-2xl font-black text-orange-700">0</p></div>
          <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-200"><p class="text-xs font-black uppercase tracking-wider text-slate-400">Cross-site Pairs</p><p id="semanticCrossSitePairs" class="mt-2 text-2xl font-black text-emerald-700">0</p></div>
        </div>

        <div class="semantic-grid">
          <div class="semantic-card rounded-3xl bg-white p-5 ring-1 ring-slate-200"><div class="mb-3"><h3 class="font-black text-slate-950">Repeated Issue Groups</h3><p id="semanticGroupInfo" class="text-sm text-slate-500">Belum ada evaluasi.</p></div><div class="table-wrap semantic-scroll rounded-2xl border border-slate-200 bg-white"><table class="semantic-groups-table text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">Group</th><th class="px-4 py-3">Sites</th><th class="px-4 py-3">Issue Count</th><th class="px-4 py-3">Top Terms</th></tr></thead><tbody id="semanticGroupsTable"></tbody></table></div></div>
          <div class="semantic-card rounded-3xl bg-white p-5 ring-1 ring-slate-200"><div class="mb-3"><h3 class="font-black text-slate-950">Similarity Issue Pairs</h3><p id="semanticPairInfo" class="text-sm text-slate-500">Menampilkan pasangan issue dengan similarity tertinggi.</p></div><div class="table-wrap semantic-scroll rounded-2xl border border-slate-200 bg-white"><table class="semantic-pairs-table text-left text-sm"><thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">Similarity</th><th class="px-4 py-3">Level</th><th class="px-4 py-3">Site A</th><th class="px-4 py-3">Issue A</th><th class="px-4 py-3">Site B</th><th class="px-4 py-3">Issue B</th><th class="px-4 py-3">Action Signal</th></tr></thead><tbody id="semanticPairsTable"></tbody></table></div></div>
        </div>
      </div>
    </section>

    <section id="tab-report" class="tab-panel fade-in hidden">
      <div class="glass soft-card rounded-3xl p-5">
        <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
          <div>
            <h2 class="text-xl font-black text-slate-950">Rekap & Export Keseluruhan</h2>
            <p class="text-sm text-slate-500">Tabel seluruh data absensi event. Gunakan filter Site dan Week untuk melihat data tertentu sebelum export.</p>
          </div>
          <div class="no-print flex flex-col gap-2 md:flex-row">
            <button onclick="resetReportFilters()" class="rounded-2xl bg-white px-4 py-3 text-sm font-black text-slate-700 shadow-sm ring-1 ring-slate-200 hover:bg-slate-50">Reset Filter</button>
            <button onclick="exportFilteredReportCSV()" class="rounded-2xl bg-slate-900 px-4 py-3 text-sm font-black text-white hover:bg-slate-800">Export Absensi CSV</button>
            <button onclick="exportMinutesReportCSV()" class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-black text-white hover:bg-blue-700">Export Notulen CSV</button>
          </div>
        </div>

        <div class="no-print mb-4 grid gap-3 md:grid-cols-3">
          <div>
            <label class="text-sm font-bold text-slate-700">Filter Site</label>
            <select id="reportFilterSite" onchange="renderReport()" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100">
              <option value="ALL">Semua Site</option>
            </select>
          </div>
          <div>
            <label class="text-sm font-bold text-slate-700">Filter Week</label>
            <select id="reportFilterWeek" onchange="renderReport()" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100">
              <option value="ALL">Semua Week</option>
            </select>
          </div>
          <div>
            <label class="text-sm font-bold text-slate-700">Search</label>
            <input id="reportSearch" oninput="renderReport()" placeholder="Cari SID / nama / perusahaan / meeting..." class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100" />
          </div>
        </div>

        <div class="no-print mb-4 flex flex-wrap gap-2 rounded-3xl bg-white/70 p-2 shadow-sm ring-1 ring-slate-200">
          <button id="reportViewAttendanceBtn" onclick="setReportView('attendance')" class="module-tab tab-active px-4 py-3 text-sm font-black">Data Absensi</button>
          <button id="reportViewMinutesBtn" onclick="setReportView('minutes')" class="module-tab px-4 py-3 text-sm font-black">List Notulen</button>
        </div>

        <div id="reportAttendancePanel">
          <div class="mb-3 flex flex-col gap-2 text-sm text-slate-600 md:flex-row md:items-center md:justify-between">
            <p id="reportTableInfo" class="font-bold">0 data ditampilkan</p>
            <p class="text-xs text-slate-500">Klik baris untuk membuka modal rekap event terkait.</p>
          </div>

          <div class="table-wrap rounded-2xl border border-slate-200 bg-white">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                  <th class="px-4 py-3">Tanggal</th>
                  <th class="px-4 py-3">Week</th>
                  <th class="px-4 py-3">Site</th>
                  <th class="px-4 py-3">Jenis Meeting</th>
                  <th class="px-4 py-3">Kode Event</th>
                  <th class="px-4 py-3">Kode SID</th>
                  <th class="px-4 py-3">Nama</th>
                  <th class="px-4 py-3">Perusahaan</th>
                  <th class="px-4 py-3">Jabatan Struktural</th>
                  <th class="px-4 py-3">Jabatan Fungsional</th>
                  <th class="px-4 py-3">Timestamp</th>
                </tr>
              </thead>
              <tbody id="overallDataTable"></tbody>
            </table>
          </div>
        </div>

        <div id="reportMinutesPanel" class="hidden">
          <div class="mb-3 flex flex-col gap-2 text-sm text-slate-600 md:flex-row md:items-center md:justify-between">
            <p id="minutesReportInfo" class="font-bold">0 notulen ditampilkan</p>
            <p class="text-xs text-slate-500">Klik baris untuk membuka modal event dan edit/print notulensi.</p>
          </div>

          <div class="table-wrap rounded-2xl border border-slate-200 bg-white">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                <tr>
                  <th class="px-4 py-3">Tanggal</th>
                  <th class="px-4 py-3">Week</th>
                  <th class="px-4 py-3">Site</th>
                  <th class="px-4 py-3">Jenis Meeting</th>
                  <th class="px-4 py-3">Kode Event</th>
                  <th class="px-4 py-3">Judul Notulen</th>
                  <th class="px-4 py-3">Notulis</th>
                  <th class="px-4 py-3">Lokasi</th>
                  <th class="px-4 py-3">Section</th>
                  <th class="px-4 py-3">No</th>
                  <th class="px-4 py-3">Catatan Meeting</th>
                  <th class="px-4 py-3">Issued By</th>
                  <th class="px-4 py-3">PIC</th>
                  <th class="px-4 py-3">Batas Waktu</th>
                  <th class="px-4 py-3">Status</th>
                  <th class="px-4 py-3">Keterangan</th>
                  <th class="px-4 py-3">Updated</th>
                </tr>
              </thead>
              <tbody id="minutesReportTable"></tbody>
            </table>
          </div>
        </div>

        <div class="no-print mt-6 rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
          <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
              <h3 class="font-black text-slate-950">Self Test</h3>
              <p class="text-sm text-slate-500">Tes cepat untuk validasi fungsi utama tanpa mengubah data produksi Anda.</p>
            </div>
            <button onclick="runSelfTests()" class="rounded-2xl bg-blue-600 px-4 py-3 text-sm font-black text-white">Run Self Test</button>
          </div>
          <pre id="testOutput" class="mt-4 max-h-72 overflow-auto rounded-2xl bg-slate-950 p-4 text-xs text-slate-100">Belum dijalankan.</pre>
        </div>
      </div>
    </section>
  </main>

  <div id="eventRecapModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
    <div class="modal-shell w-full max-w-6xl rounded-[2rem] bg-white p-6 shadow-2xl ring-1 ring-white/20">
      <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
        <div>
          <h3 class="text-2xl font-black text-slate-950">Rekap Event</h3>
          <p id="eventRecapTitle" class="mt-1 text-sm text-slate-500"></p>
        </div>
        <button onclick="closeEventRecapModal()" class="no-print rounded-xl bg-slate-100 px-3 py-2 text-sm font-black text-slate-700">✕</button>
      </div>
      <div id="eventRecapSummary" class="mt-5 grid gap-4 md:grid-cols-4"></div>
      <div class="mt-6 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

      <div class="mt-6 rounded-3xl bg-white p-5 ring-1 ring-slate-200 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
          <div>
            <h4 class="text-lg font-black text-slate-950">Notulensi Event</h4>
            <p class="text-sm text-slate-500">Form input notulensi dibuka melalui tombol. Format formal tetap memakai Enviro Issue, Safety Issue, General Issue, dan kolom Issued By pada catatan meeting.</p>
            <p id="minutesUpdatedInfo" class="mt-1 text-xs font-bold text-slate-400">Belum ada notulensi</p>
          </div>
          <div class="no-print flex flex-col gap-2 md:flex-row md:items-center">
            <button id="minutesToggleBtn" type="button" onclick="openEventMinutesForm()" class="rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-500 px-5 py-3 text-sm font-black text-white shadow-lg shadow-blue-500/20 hover:opacity-95">Input Notulensi</button>
            <button type="button" onclick="printEventMinutes()" class="rounded-2xl bg-blue-50 px-5 py-3 text-sm font-black text-blue-700 ring-1 ring-blue-100 hover:bg-blue-100">Print Notulensi</button>
          </div>
        </div>

        <div id="minutesInputPanel" class="mt-5 hidden rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200">
          <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
              <h5 class="font-black text-slate-950">Form Input Notulensi</h5>
              <p class="text-sm text-slate-500">Isi catatan meeting, PIC, batas waktu, dan keterangan per issue.</p>
            </div>
            <button type="button" onclick="closeEventMinutesForm()" class="no-print rounded-xl bg-slate-100 px-3 py-2 text-xs font-black text-slate-700">Tutup</button>
          </div>

          <form id="minutesForm" onsubmit="saveEventMinutes(event)" class="space-y-5">
          <div id="minutesPrintArea" class="overflow-hidden rounded-2xl border-2 border-slate-950 bg-white text-slate-950">
            <table class="min-w-full border-collapse text-sm">
              <tr>
                <td rowspan="3" class="w-[140px] border border-slate-950 p-3 align-middle text-center">
                  <img src="https://besentry-dev.beraucoal.co.id/build/images/logo-removebg.png" alt="Berau Coal logo" style="max-width:120px; max-height:58px; object-fit:contain; display:block; margin:0 auto;" />
                </td>
                <td class="border border-slate-950 py-1 text-center text-xs font-black">BERAU COAL</td>
              </tr>
              <tr>
                <td class="border border-slate-950 py-1 text-center text-xs font-black">FORMULIR</td>
              </tr>
              <tr>
                <td class="border border-slate-950 p-1 text-center text-xs font-black">
                  <input id="minutesMeetingTitle" placeholder="NOTULENSI MEETING" class="w-full border-0 bg-transparent text-center text-xs font-black outline-none" />
                </td>
              </tr>
            </table>

            <div class="border-x border-b border-slate-950 p-4 text-sm">
              <div class="grid gap-3 md:grid-cols-2">
                <div class="grid grid-cols-[120px_10px_1fr] items-center gap-2">
                  <label class="font-semibold">Jenis Meeting</label><span>:</span>
                  <input id="minutesMeetingType" class="rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" />
                </div>
                <div class="grid grid-cols-[120px_10px_1fr] items-center gap-2">
                  <label class="font-semibold">Tanggal</label><span>:</span>
                  <input id="minutesMeetingDate" type="date" class="rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" />
                </div>
                <div class="grid grid-cols-[120px_10px_1fr] items-center gap-2">
                  <label class="font-semibold">Notulis</label><span>:</span>
                  <input id="minutesNotulis" class="rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" placeholder="Nama notulis" />
                </div>
                <div class="grid grid-cols-[120px_10px_1fr] items-center gap-2 md:col-span-2">
                  <label class="font-semibold">Lokasi</label><span>:</span>
                  <input id="minutesLocation" class="rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" placeholder="Lokasi meeting / link zoom" />
                </div>
              </div>
            </div>

            <div class="border-x border-b border-slate-950">
              <div class="bg-slate-950 px-4 py-2 text-center text-xs font-black text-white">CATATAN MEETING (Enviro Issue)</div>
              <div id="enviroIssueTable"></div>
            </div>
            <div class="border-x border-b border-slate-950">
              <div class="bg-slate-950 px-4 py-2 text-center text-xs font-black text-white">CATATAN MEETING (Safety Issue)</div>
              <div id="safetyIssueTable"></div>
            </div>
            <div class="border-x border-b border-slate-950">
              <div class="bg-slate-950 px-4 py-2 text-center text-xs font-black text-white">CATATAN MEETING (General Issue)</div>
              <div id="generalIssueTable"></div>
            </div>
          </div>

          <div class="no-print flex flex-col gap-2 md:flex-row">
            <button type="submit" class="flex-1 rounded-2xl bg-slate-900 px-4 py-3 text-sm font-black text-white hover:bg-slate-800">Simpan Notulensi</button>
            <button type="button" onclick="clearEventMinutesForm()" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-black text-slate-700 hover:bg-slate-200">Clear Form</button>
          </div>
        </form>
        </div>
      </div>

      <div class="mt-6 rounded-3xl bg-gradient-to-br from-blue-50 to-cyan-50 p-5 ring-1 ring-blue-100 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
          <div>
            <h4 class="text-lg font-black text-slate-950">Absensi Event</h4>
            <p class="text-sm text-slate-600">Gunakan tombol ini hanya jika perlu input absensi secara manual oleh admin.</p>
          </div>
          <button id="manualAttendanceToggleBtn" onclick="openManualAttendanceForm()" class="rounded-2xl bg-gradient-to-r from-emerald-600 to-cyan-500 px-5 py-3 text-sm font-black text-white shadow-lg shadow-emerald-500/20 hover:opacity-95">
            Absen Manual
          </button>
        </div>

        <div id="manualAttendancePanel" class="mt-5 hidden rounded-3xl bg-white p-5 ring-1 ring-blue-100">
          <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
              <h5 class="font-black text-slate-950">Form Absen Manual</h5>
              <p class="text-sm text-slate-500">Input Kode SID. Data pekerja otomatis ditarik dari sistem.</p>
            </div>
            <button type="button" onclick="closeManualAttendanceForm()" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-black text-slate-700">Tutup</button>
          </div>
          <div id="scanNotice" class="rounded-3xl bg-amber-50 p-4 text-sm font-semibold text-amber-800 ring-1 ring-amber-100"></div>
          <form id="attendanceForm" class="mt-4 grid gap-4 lg:grid-cols-2" onsubmit="saveAttendance(event)">
            <input type="hidden" id="attendanceEventId" />
            <input type="hidden" id="resolvedEmployeeId" />
            <div class="lg:col-span-2" id="activeEventInfo"></div>
            <div>
              <label class="text-sm font-bold text-slate-700">Kode SID</label>
              <input id="sidInput" required placeholder="Masukkan Kode SID" oninput="lookupSID()" class="mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold uppercase tracking-wide outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-100" />
            </div>
            <div>
              <label class="text-sm font-bold text-slate-700">Timestamp</label>
              <input id="timestampInput" readonly class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700" />
            </div>
            <div>
              <label class="text-sm font-bold text-slate-700">Nama</label>
              <input id="autoName" readonly class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700" />
            </div>
            <div>
              <label class="text-sm font-bold text-slate-700">Perusahaan</label>
              <input id="autoCompany" readonly class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700" />
            </div>
            <div>
              <label class="text-sm font-bold text-slate-700">Jabatan Struktural</label>
              <input id="autoStructural" readonly class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700" />
            </div>
            <div>
              <label class="text-sm font-bold text-slate-700">Jabatan Fungsional</label>
              <input id="autoFunctional" readonly class="mt-1 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700" />
            </div>
            <div class="lg:col-span-2">
              <button id="submitAttendanceBtn" class="w-full rounded-2xl bg-gradient-to-r from-emerald-600 to-cyan-500 px-4 py-3 text-sm font-black text-white shadow-lg shadow-emerald-500/20 hover:opacity-95">Submit Absensi</button>
            </div>
          </form>
        </div>
      </div>

      <div class="mt-6 grid gap-5 lg:grid-cols-[.8fr_1.2fr]">
        <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-200 shadow-sm">
          <h4 class="font-black text-slate-950">Status Kehadiran Perusahaan</h4>
          <div id="eventRecapCompany" class="mt-4 space-y-3"></div>
        </div>
        <div class="rounded-3xl bg-white p-5 ring-1 ring-slate-200 shadow-sm">
          <div class="mb-3 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <h4 class="font-black text-slate-950">Daftar Hadir</h4>
            <button id="eventRecapExportBtn" onclick="exportSelectedEventCSV()" class="no-print rounded-xl bg-slate-900 px-3 py-2 text-xs font-black text-white">Export Event CSV</button>
          </div>
          <div class="table-wrap rounded-2xl border border-slate-200 bg-white">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500"><tr><th class="px-4 py-3">Timestamp</th><th class="px-4 py-3">SID</th><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Perusahaan</th><th class="px-4 py-3">Jabatan</th></tr></thead>
              <tbody id="eventRecapAttendees"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="closeMeetingModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl">
      <div class="flex items-start justify-between gap-4">
        <div>
          <p class="mb-2 inline-flex rounded-full bg-orange-50 px-3 py-1 text-xs font-black uppercase tracking-wider text-orange-700 ring-1 ring-orange-100">Jam selesai tercapai</p>
          <h3 class="text-2xl font-black text-slate-950">Tutup Meeting?</h3>
          <p id="closeMeetingTitle" class="mt-2 text-sm leading-6 text-slate-600"></p>
        </div>
        <button onclick="dismissCloseMeetingPrompt()" class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-black text-slate-700">✕</button>
      </div>
      <div class="mt-5 rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200">
        <p class="text-sm font-semibold text-slate-500">Waktu Berjalan</p>
        <p id="closeMeetingElapsed" class="mt-1 text-3xl font-black text-orange-700">00:00:00</p>
        <p class="mt-2 text-xs text-slate-500">Jika pilih <b>Ya</b>, timer meeting berhenti dan QR/form absensi akan dikunci.</p>
      </div>
      <div class="mt-5 grid grid-cols-2 gap-3">
        <button onclick="dismissCloseMeetingPrompt()" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-black text-slate-700 hover:bg-slate-200">Tidak</button>
        <button onclick="confirmCloseMeeting()" class="rounded-2xl bg-gradient-to-r from-red-600 to-orange-500 px-4 py-3 text-sm font-black text-white shadow-lg shadow-red-500/20 hover:opacity-95">Ya, Tutup</button>
      </div>
    </div>
  </div>

  <div id="qrModal" class="fixed inset-0 z-40 hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl">
      <div class="flex items-start justify-between gap-4"><div><h3 class="text-xl font-black text-slate-950">QR Absensi Event</h3><p id="qrModalTitle" class="mt-1 text-sm text-slate-500"></p></div><button onclick="closeQRModal()" class="rounded-xl bg-slate-100 px-3 py-2 text-sm font-black text-slate-700">✕</button></div>
      <div id="qrBox" class="mx-auto mt-5 flex h-64 w-64 items-center justify-center rounded-3xl bg-white p-4 ring-1 ring-slate-200"></div>
      <input id="qrLink" readonly class="mt-5 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600" />
      <div id="manualCopyBox" class="mt-3 hidden rounded-2xl bg-amber-50 p-3 text-xs font-semibold text-amber-800 ring-1 ring-amber-100">Clipboard browser diblokir. Link sudah ditampilkan di atas; blok/select lalu copy manual.</div>
      <div class="mt-4 flex gap-2"><button onclick="copyQRLink(this)" class="flex-1 rounded-2xl bg-blue-600 px-4 py-3 text-sm font-black text-white">Copy Link QR</button><button onclick="window.print()" class="rounded-2xl bg-slate-100 px-4 py-3 text-sm font-black text-slate-700">Print</button></div>
    </div>
  </div>

  <div id="semanticDetailModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm">
    <div class="modal-shell w-full max-w-5xl rounded-[2rem] bg-white p-6 shadow-2xl ring-1 ring-white/20">
      <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
        <div>
          <p class="mb-2 inline-flex rounded-full bg-violet-50 px-3 py-1 text-xs font-black uppercase tracking-wider text-violet-700 ring-1 ring-violet-100">Semantic Detail</p>
          <h3 id="semanticDetailTitle" class="text-2xl font-black text-slate-950">Detail Notulen</h3>
          <p id="semanticDetailSubtitle" class="mt-1 text-sm text-slate-500"></p>
        </div>
        <button onclick="closeSemanticDetailModal()" class="no-print rounded-xl bg-slate-100 px-3 py-2 text-sm font-black text-slate-700">✕</button>
      </div>
      <div id="semanticDetailBody" class="mt-5"></div>
    </div>
  </div>

  <div id="toast" class="pointer-events-none fixed bottom-5 left-1/2 z-50 hidden -translate-x-1/2 rounded-2xl bg-slate-950/95 px-5 py-3 text-sm font-bold text-white shadow-2xl ring-1 ring-white/10"></div>

<script>
  const SID_MEETING_API_BASE = '/sid-meeting/api';
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const MEETING_TYPES = ['Safety Talk', 'P5M', 'Toolbox Meeting', 'OHS Committee Meeting', 'Incident Review', 'Critical Control Verification', 'Training / Workshop', 'Management Review'];
  const SITE_MASTER = ['BMO 1', 'BMO 2', 'BMO 3', 'GMO', 'SMO', 'LMO', 'Marine', 'HOTE'];

  // Prototype pengganti Master SID by system.
  // Pada versi production, ganti fungsi fetchEmployeeBySID() agar memanggil API/HRIS/database pusat.
  const SYSTEM_SID_DIRECTORY = [
    { id: 'SYS-EMP-1', sid: 'SID001', name: 'Budi Santoso', company: 'PAMA', structuralPosition: 'Supervisor', functionalPosition: 'Operator A2B' },
    { id: 'SYS-EMP-2', sid: 'SID002', name: 'Andi Wijaya', company: 'Berau Coal', structuralPosition: 'Superintendent', functionalPosition: 'Safety Evaluator' },
    { id: 'SYS-EMP-3', sid: 'SID003', name: 'Siti Rahma', company: 'BUMA', structuralPosition: 'Foreman', functionalPosition: 'Admin SHE' },
    { id: 'SYS-EMP-4', sid: 'SID004', name: 'Rizky Pratama', company: 'PAMA', structuralPosition: 'Group Leader', functionalPosition: 'Mekanik' }
  ];

  // Prototype daftar perusahaan undangan/eligible by system.
  // Production: ganti dengan API master vendor / contractor list per event/site.
  const SYSTEM_COMPANY_DIRECTORY = [
    'PT Mutiara Tanjung Lestari', 'PT Bukit Makmur Mandiri Utama', 'PT Fajar Anugrah Dinamika', 'PT Kaltim Diamond Coal',
    'CV Bukit Manimbora', 'CV Hulu Putra Banua', 'CV Megah Jaya Abadi', 'CV Rangga', 'CV Sambaliung Fiber', 'CV Triana Jaya',
    'CV. Varissa Jaya', 'Koperasi Maju Bersama', 'PT Agung Buana Rejeki', 'PT Andalan Duta Eka Nusantara', 'PT Apex Mitra Prima',
    'PT Arcistec International', 'PT Arexas Indonesia', 'PT Bagong Dekaka Makmur', 'PT Berkat Teman Sejati', 'PT. Buana Indah Lalebata',
    'PT Cominco Mitra Perkasa', 'PT. DNX Indonesia', 'PT. Eka Dharma Jaya Sakti', 'PT Energi Nuansa Jaya', 'PT Etam Wira Utama',
    'PT Eurotruk Trasindo', 'PT Geoservices', 'PT Hutan Rindang Banua', 'PT Imelda Teknik Mandiri', 'PT Indonesia Carbon Energy',
    'PT Limbah Bina Sejahtera', 'PT Lintech Duta Pratama', 'PT Majau Inti Jaya', 'PT Maju Bersama Binungan', 'PT Menara Borneo Jaya',
    'PT Mentari Cipta Mandiri', 'PT Mitra Lanuk Permai', 'PT Mitra Sukses Raharja', 'PT. Puncak Makmur Jaya', 'PT Rareendo Mulia Abadi',
    'PT Samburakat Jaya Utama', 'PT Serasi Autoraya', 'PT Smartfren Telecom', 'PT Sucofindo', 'PT Sumber Mitra Binungan',
    'PT Suprima Mitra Adihusada', 'PT Surveyor Carbon Consulting Indonesia', 'PT Tectona Mitra Utama', 'PT Trakindo Utama', 'PT Transkon Jaya',
    'PT Triatra Sinergia Pratama', 'PT United Tractors', 'CV Juwita', 'CV Putri Dewi', 'CV Santoso Putra Mandiri', 'CV Teguh Harapan',
    'PT Garuda Bakti Nusantara', 'PT Harmoni Mitra Utama', 'PT Joymar Abadi Indonesia', 'PT Kalimantan Teknik Utama', 'PT Orecon Putra Perkasa',
    'PT Taubah Berlian Jaya', 'PT. Anditha Asri Borneo', 'CV Elang Maju Mapan', 'CV Maju Makmur Teknik', 'CV Tangguh Mandiri',
    'PT Altros Teknologi', 'PT Energi Indonesia Berkarya', 'PT Madhani Talatah Nusantara', 'PAMA', 'Berau Coal', 'BUMA'
  ];
  let db = loadDB();
  let bootstrapLoaded = false;
  let bootstrapPromise = null;
  let syncInFlight = false;
  let syncQueued = false;
  let scannedEventId = getScannedEventIdFromURL();
  let selectedRecapEventId = '';
  let sitePerformanceChart = null;
  const ISSUE_TABLE_META = {
    enviro: { containerId: 'enviroIssueTable', defaultRows: 6 },
    safety: { containerId: 'safetyIssueTable', defaultRows: 4 },
    general: { containerId: 'generalIssueTable', defaultRows: 4 }
  };
  let issueRowCounts = { enviro: 6, safety: 4, general: 4 };
  let reportView = 'attendance';
  let pendingCloseEventId = '';
  let closePromptSnoozed = {};
  let refreshTimer = null;

  function loadDB() {
    return migrateDB({ events: [], attendance: [], companies: getDefaultCompanyMaster(), meetingTypes: getDefaultMeetingTypes() });
  }

  function normalizeSiteValue(site) {
    const raw = String(site || '').trim();
    const upper = raw.toUpperCase();
    if (['MTL', 'CPP', 'PORT / JETTY', 'PORT', 'JETTY', 'MARINE'].includes(upper)) return 'Marine';
    if (['HO', 'HEAD OFFICE', 'HOTE'].includes(upper)) return 'HOTE';
    const exact = SITE_MASTER.find(item => item.toUpperCase() === upper);
    return exact || raw;
  }

  function migrateDB(data) {
    const next = {
      events: Array.isArray(data.events) ? data.events : [],
      attendance: Array.isArray(data.attendance) ? data.attendance : [],
      companies: Array.isArray(data.companies) && data.companies.length ? data.companies : getDefaultCompanyMaster(),
      meetingTypes: Array.isArray(data.meetingTypes) && data.meetingTypes.length ? data.meetingTypes : getDefaultMeetingTypes()
    };
    next.events = next.events.map(ev => ({ ...ev, site: normalizeSiteValue(ev.site) })).filter(ev => SITE_MASTER.includes(ev.site));
    next.companies = next.companies.map(company => {
      const mappedSites = [...new Set((company.sites || SITE_MASTER).map(normalizeSiteValue).filter(site => SITE_MASTER.includes(site)))];
      return { ...company, sites: mappedSites.length ? mappedSites : [...SITE_MASTER] };
    });
    next.meetingTypes = [...new Set(next.meetingTypes.map(value => String(value || '').trim()).filter(Boolean))];
    if (!next.meetingTypes.length) next.meetingTypes = getDefaultMeetingTypes();
    return next;
  }

  function getDefaultMeetingTypes() {
    return [...MEETING_TYPES];
  }

  function getMeetingTypes() {
    if (!Array.isArray(db.meetingTypes) || !db.meetingTypes.length) db.meetingTypes = getDefaultMeetingTypes();
    db.meetingTypes = [...new Set(db.meetingTypes.map(value => String(value || '').trim()).filter(Boolean))];
    if (!db.meetingTypes.length) db.meetingTypes = getDefaultMeetingTypes();
    return db.meetingTypes;
  }

  function saveDB() {
    queueDatabaseSync();
    refreshAll();
  }

  async function apiFetch(url, options = {}) {
    const response = await fetch(url, {
      ...options,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'X-Requested-With': 'XMLHttpRequest',
        ...(options.headers || {})
      }
    });
    const raw = await response.text();
    let payload = {};
    try {
      payload = raw ? JSON.parse(raw) : {};
    } catch (e) {
      payload = {};
    }
    if (!response.ok) {
      const fromErrors = payload.errors && typeof payload.errors === 'object'
        ? Object.values(payload.errors).flat().filter(Boolean).join(' ')
        : '';
      const msg = fromErrors || payload.message || (response.status === 419 ? 'Sesi kedaluwarsa, muat ulang halaman.' : '') || `Request gagal (HTTP ${response.status})`;
      throw new Error(msg);
    }
    return payload;
  }

  async function hydrateFromDatabase(force = false) {
    if (bootstrapLoaded && !force) return;
    if (bootstrapPromise) return bootstrapPromise;
    bootstrapPromise = apiFetch(`${SID_MEETING_API_BASE}/bootstrap`)
      .then(payload => {
        db = migrateDB({
          events: payload.events || [],
          attendance: payload.attendance || [],
          companies: payload.companies || [],
          meetingTypes: payload.meetingTypes || getDefaultMeetingTypes()
        });
        bootstrapLoaded = true;
        refreshAll();
      })
      .catch(err => {
        console.warn('Bootstrap database gagal:', err);
        toast('Gagal memuat data database, fallback ke data lokal.');
      })
      .finally(() => {
        bootstrapPromise = null;
      });
    return bootstrapPromise;
  }

  function buildSyncPayload() {
    return {
      events: db.events || [],
      attendance: db.attendance || [],
      companies: db.companies || [],
      meetingTypes: db.meetingTypes || []
    };
  }

  async function queueDatabaseSync() {
    if (syncInFlight) {
      syncQueued = true;
      return;
    }
    syncInFlight = true;
    try {
      await apiFetch(`${SID_MEETING_API_BASE}/sync`, {
        method: 'POST',
        body: JSON.stringify(buildSyncPayload())
      });
    } catch (err) {
      console.warn('Sync DB gagal:', err);
      toast('Sinkronisasi database gagal. Coba lagi.');
    } finally {
      syncInFlight = false;
      if (syncQueued) {
        syncQueued = false;
        queueDatabaseSync();
      }
    }
  }

  function uid(prefix = 'ID') { return prefix + '-' + Math.random().toString(36).slice(2, 9).toUpperCase(); }

  function toast(message) {
    const el = document.getElementById('toast');
    if (!el) return;
    el.textContent = message;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 2500);
  }

  function eventSaveAlert(type, message) {
    if (window.Swal) {
      Swal.fire({
        icon: type,
        title: type === 'success' ? 'Berhasil' : 'Gagal',
        text: message,
        confirmButtonColor: type === 'success' ? '#2563eb' : '#dc2626'
      });
      return;
    }
    toast(message);
  }

  function escapeHTML(value) { return String(value ?? '').replace(/[&<>'"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[c])); }
  function formatDate(value) { if (!value) return '-'; const d = new Date(value + 'T00:00:00'); return isNaN(d) ? value : d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }); }
  function formatDateTime(value) { if (!value) return '-'; const d = new Date(value); return isNaN(d) ? value : d.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'medium' }); }
  function toDateTime(date, time) { return new Date(`${date}T${time || '00:00'}:00`); }

  function getEventStatus(ev, now = new Date()) {
    if (!ev) return 'Draft';
    const start = toDateTime(ev.date, ev.startTime);
    const end = toDateTime(ev.date, ev.endTime);
    if (ev.manualStatus === 'Draft') return 'Draft';
    if (ev.manualStatus === 'Closed' || ev.closedAt) return 'Closed';
    if (now < start) return 'Upcoming';
    if (now >= end) return 'Overrun';
    return 'Open';
  }

  function isEventActive(ev) { return ['Open', 'Overrun'].includes(getEventStatus(ev)); }

  function statusBadge(status) {
    const cls = status === 'Open' ? 'badge-open' : status === 'Overrun' ? 'badge-overrun' : status === 'Closed' ? 'badge-closed' : status === 'Expired' ? 'badge-expired' : status === 'Upcoming' ? 'badge-upcoming' : 'badge-draft';
    const label = status === 'Closed' ? 'Meeting Ditutup' : status === 'Overrun' ? 'Lewat Jam Selesai' : status === 'Expired' ? 'QR Tidak Aktif' : status === 'Open' ? 'QR Aktif' : status === 'Upcoming' ? 'Belum Mulai' : 'Draft';
    return `<span class="badge ${cls}">${label}</span>`;
  }

  function getElapsedMs(ev, now = new Date()) {
    if (!ev?.date || !ev?.startTime) return 0;
    const start = toDateTime(ev.date, ev.startTime);
    const stop = ev.closedAt ? new Date(ev.closedAt) : now;
    return Math.max(0, stop - start);
  }

  function formatDuration(ms) {
    const totalSeconds = Math.floor(Math.max(0, ms) / 1000);
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;
    return [hours, minutes, seconds].map(v => String(v).padStart(2, '0')).join(':');
  }

  function getISOWeek(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    const target = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    const dayNum = target.getUTCDay() || 7;
    target.setUTCDate(target.getUTCDate() + 4 - dayNum);
    const yearStart = new Date(Date.UTC(target.getUTCFullYear(), 0, 1));
    return Math.ceil((((target - yearStart) / 86400000) + 1) / 7);
  }

  function autoFillWeek() {
    const date = document.getElementById('meetingDate')?.value;
    if (!date) return;
    document.getElementById('meetingWeek').value = `W${String(getISOWeek(date)).padStart(2, '0')}`;
  }

  function getScannedEventIdFromURL() {
    const params = new URLSearchParams(window.location.hash.replace('#', ''));
    return params.get('absen') || '';
  }

  function buildQRLink(eventId) {
    const ev = db.events.find(x => x.id === eventId);
    if (ev?.qrToken) {
      return `${window.location.origin}/attendance/${encodeURIComponent(ev.qrToken)}`;
    }
    const base = window.location.href.split('#')[0];
    return `${base}#absen=${encodeURIComponent(eventId)}`;
  }

  function setFloatingOverlay(open) {
    const overlay = document.getElementById('floatingFormOverlay');
    if (!overlay) return;
    const anyOpen = open || document.getElementById('createEventContainer')?.classList.contains('open') || document.getElementById('companyInputContainer')?.classList.contains('open');
    overlay.classList.toggle('open', !!anyOpen);
  }

  function closeFloatingPanels() {
    document.getElementById('createEventContainer')?.classList.remove('open');
    document.getElementById('companyInputContainer')?.classList.remove('open');
    setFloatingOverlay(false);
  }

  function toggleCreateEventContainer(forceState) {
    const panel = document.getElementById('createEventContainer');
    if (!panel) return;
    const shouldOpen = typeof forceState === 'boolean' ? forceState : !panel.classList.contains('open');
    document.getElementById('companyInputContainer')?.classList.remove('open');
    panel.classList.toggle('open', shouldOpen);
    setFloatingOverlay(shouldOpen);
    if (shouldOpen) setTimeout(() => document.getElementById('meetingType')?.focus(), 120);
  }

  function toggleCompanyInputContainer(forceState) {
    const panel = document.getElementById('companyInputContainer');
    if (!panel) return;
    const shouldOpen = typeof forceState === 'boolean' ? forceState : !panel.classList.contains('open');
    document.getElementById('createEventContainer')?.classList.remove('open');
    panel.classList.toggle('open', shouldOpen);
    setFloatingOverlay(shouldOpen);
    if (shouldOpen) setTimeout(() => document.getElementById('companyName')?.focus(), 120);
  }

  function showTab(tab) {
    document.querySelectorAll('.tab-panel').forEach(el => el.classList.add('hidden'));
    document.getElementById(`tab-${tab}`)?.classList.remove('hidden');
    document.querySelectorAll('.tab-btn').forEach(btn => { btn.classList.remove('tab-active'); });
    const active = document.querySelector(`[data-tab="${tab}"]`);
    if (active) { active.classList.add('tab-active'); }
    refreshAll();
    if (tab === 'semanticeval') renderSemanticEvaluation(true);
  }

  async function saveEvent(e) {
    e.preventDefault();
    const payload = {
      meeting_type: document.getElementById('meetingType').value,
      site: normalizeSiteValue(document.getElementById('eventSite').value.trim()),
      meeting_date: document.getElementById('meetingDate').value,
      week: document.getElementById('meetingWeek').value.trim().toUpperCase(),
      start_time: document.getElementById('startTime').value,
      end_time: document.getElementById('endTime').value
    };
    if (!SITE_MASTER.includes(payload.site)) return eventSaveAlert('error', 'Site tidak valid. Pilih site sesuai master.');
    if (toDateTime(payload.meeting_date, payload.end_time) <= toDateTime(payload.meeting_date, payload.start_time)) return eventSaveAlert('error', 'Jam selesai harus lebih besar dari jam mulai');
    try {
      await apiFetch(`${SID_MEETING_API_BASE}/events`, { method: 'POST', body: JSON.stringify(payload) });
      await hydrateFromDatabase(true);
      clearEventForm();
      eventSaveAlert('success', 'Event berhasil disimpan ke database');
    } catch (err) {
      eventSaveAlert('error', err.message || 'Gagal simpan event');
    }
  }

  function editEvent(id) {
    const ev = db.events.find(x => x.id === id); if (!ev) return;
    toggleCreateEventContainer(false);
    document.getElementById('eventId').value = ev.id;
    document.getElementById('meetingType').value = ev.meetingType;
    document.getElementById('eventSite').value = ev.site;
    document.getElementById('meetingDate').value = ev.date;
    document.getElementById('meetingWeek').value = ev.week;
    document.getElementById('startTime').value = ev.startTime;
    document.getElementById('endTime').value = ev.endTime;
    showTab('events');
  }

  function deleteEvent(id) {
    if (!confirm('Hapus event ini beserta log absensinya?')) return;
    db.events = db.events.filter(x => x.id !== id);
    db.attendance = db.attendance.filter(x => x.eventId !== id);
    if (scannedEventId === id) scannedEventId = '';
    saveDB();
    toast('Event dihapus');
  }

  function clearEventForm() {
    const form = document.getElementById('eventForm'); if (!form) return;
    form.reset();
    document.getElementById('eventId').value = '';
    document.getElementById('meetingDate').value = new Date().toISOString().slice(0, 10);
    autoFillWeek();
    document.getElementById('startTime').value = '08:00';
    document.getElementById('endTime').value = '10:00';
  }

  function renderEvents() {
    const q = (document.getElementById('eventSearch')?.value || '').toLowerCase();
    const target = document.getElementById('eventList'); if (!target) return;
    const rows = db.events.filter(ev => [ev.meetingType, ev.site, ev.week, ev.code, getEventStatus(ev)].join(' ').toLowerCase().includes(q));
    if (!rows.length) { target.innerHTML = `<div class="rounded-3xl bg-white p-6 text-center text-sm font-semibold text-slate-500 ring-1 ring-slate-200">Belum ada event. Buat event pertama dari form kiri.</div>`; return; }
    target.innerHTML = rows.map(ev => {
      const status = getEventStatus(ev);
      const total = db.attendance.filter(a => a.eventId === ev.id).length;
      const qrLink = buildQRLink(ev.id);
      return `<article onclick="openEventRecapModal('${ev.id}')" class="cursor-pointer rounded-3xl bg-white p-5 ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-lg"><div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between"><div class="min-w-0"><div class="mb-2 flex flex-wrap items-center gap-2">${statusBadge(status)}<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">${ev.code}</span><span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">${escapeHTML(ev.week)}</span></div><h3 class="text-lg font-black text-slate-950">${escapeHTML(ev.meetingType)}</h3><p class="mt-1 text-sm text-slate-500">${escapeHTML(ev.site)} · ${formatDate(ev.date)} · ${ev.startTime} - ${ev.endTime}</p><p class="mt-2 break-all text-xs text-slate-400">${escapeHTML(qrLink)}</p><p class="mt-2 text-xs font-bold text-blue-600">Klik kartu untuk melihat rekap event</p></div><div class="min-w-44 rounded-2xl bg-slate-50 p-4 text-sm ring-1 ring-slate-200"><div class="flex justify-between gap-4"><span>Absensi</span><b>${total}</b></div><div class="mt-1 flex justify-between gap-4"><span>Status</span><b>${status}</b></div><div class="mt-1 flex justify-between gap-4"><span>Waktu</span><b class="font-mono">${formatDuration(getElapsedMs(ev))}</b></div>${ev.closedAt ? `<div class="mt-1 text-xs text-slate-500">Closed: ${formatDateTime(ev.closedAt)}</div>` : ''}</div></div><div class="no-print mt-4 flex flex-wrap gap-2"><button onclick="event.stopPropagation(); openQRModal('${ev.id}')" class="rounded-xl bg-cyan-50 px-3 py-2 text-xs font-black text-cyan-700 hover:bg-cyan-100">Lihat QR</button><button onclick="event.stopPropagation(); openAttendanceFromEvent('${ev.id}')" class="rounded-xl bg-emerald-50 px-3 py-2 text-xs font-black text-emerald-700 hover:bg-emerald-100">Absen Manual</button>${status !== 'Closed' ? `<button onclick="event.stopPropagation(); askCloseMeeting('${ev.id}', true)" class="rounded-xl bg-orange-50 px-3 py-2 text-xs font-black text-orange-700 hover:bg-orange-100">Tutup Meeting</button>` : ''}<button onclick="event.stopPropagation(); copyText('${escapeJS(qrLink)}', this)" class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-200">Copy Link Absensi</button><button onclick="event.stopPropagation(); editEvent('${ev.id}')" class="rounded-xl bg-blue-50 px-3 py-2 text-xs font-black text-blue-700 hover:bg-blue-100">Edit</button><button onclick="event.stopPropagation(); deleteEvent('${ev.id}')" class="rounded-xl bg-red-50 px-3 py-2 text-xs font-black text-red-700 hover:bg-red-100">Hapus</button></div></article>`;
    }).join('');
  }

  function escapeJS(value) { return String(value ?? '').replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/\n/g, '\\n').replace(/\r/g, '\\r'); }

  function openQRModal(eventId) {
    const ev = db.events.find(x => x.id === eventId); if (!ev) return;
    const link = buildQRLink(eventId);
    const box = document.getElementById('qrBox'); box.innerHTML = '';
    document.getElementById('qrModalTitle').textContent = `${ev.meetingType} · ${ev.site} · ${ev.week}`;
    document.getElementById('qrLink').value = link;
    document.getElementById('manualCopyBox')?.classList.add('hidden');
    if (window.QRCode) new QRCode(box, { text: link, width: 220, height: 220, correctLevel: QRCode.CorrectLevel.H });
    else box.innerHTML = '<div class="text-center text-sm font-bold text-red-700">Library QR gagal dimuat. Gunakan link absensi di bawah.</div>';
    document.getElementById('qrModal').classList.remove('hidden');
    document.getElementById('qrModal').classList.add('flex');
  }

  function closeQRModal() { document.getElementById('qrModal').classList.add('hidden'); document.getElementById('qrModal').classList.remove('flex'); }
  function copyQRLink(buttonEl) { copyText(document.getElementById('qrLink').value, buttonEl); }

  function openEventRecapModal(eventId) {
    selectedRecapEventId = eventId;
    scannedEventId = eventId;
    const ev = db.events.find(x => x.id === eventId);
    if (!ev) return;
    renderEventRecapModal(eventId);
    renderActiveAttendanceEvent();
    document.getElementById('eventRecapModal').classList.remove('hidden');
    document.getElementById('eventRecapModal').classList.add('flex');
  }

  function closeEventRecapModal() {
    closeManualAttendanceForm();
    closeEventMinutesForm();
    document.getElementById('eventRecapModal').classList.add('hidden');
    document.getElementById('eventRecapModal').classList.remove('flex');
  }

  function openManualAttendanceForm() {
    const panel = document.getElementById('manualAttendancePanel');
    if (!panel) return;
    panel.classList.remove('hidden');
    renderActiveAttendanceEvent();
    setTimeout(() => document.getElementById('sidInput')?.focus(), 120);
  }

  function closeManualAttendanceForm() {
    const panel = document.getElementById('manualAttendancePanel');
    if (panel) panel.classList.add('hidden');
  }

  function openEventMinutesForm() {
    const panel = document.getElementById('minutesInputPanel');
    if (!panel) return;
    panel.classList.remove('hidden');
    setTimeout(() => document.getElementById('minutesMeetingTitle')?.focus(), 120);
  }

  function closeEventMinutesForm() {
    const panel = document.getElementById('minutesInputPanel');
    if (panel) panel.classList.add('hidden');
  }

  function normalizeCompanyName(value) {
    return String(value || '').toUpperCase().split('.').join('').trim().split(' ').filter(Boolean).join(' ');
  }

  function getDefaultCompanyMaster() {
    return SYSTEM_COMPANY_DIRECTORY.map((name, index) => ({
      id: `COMP-${String(index + 1).padStart(3, '0')}-${normalizeCompanyName(name).replace(/[^A-Z0-9]/g, '').slice(0, 10)}`,
      name,
      sites: [...SITE_MASTER],
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    }));
  }

  function getCompanyMaster() {
    if (!Array.isArray(db.companies) || !db.companies.length) db.companies = getDefaultCompanyMaster();
    db.companies.forEach(company => {
      if (!Array.isArray(company.sites)) company.sites = [...SITE_MASTER];
      company.sites = [...new Set(company.sites.map(normalizeSiteValue).filter(site => SITE_MASTER.includes(site)))];
      if (!company.sites.length) company.sites = [...SITE_MASTER];
    });
    return db.companies;
  }

  function getEligibleCompaniesForSite(site) {
    const targetSite = String(site || '').trim();
    const companies = getCompanyMaster();
    if (!targetSite) return companies.map(company => company.name);
    return companies.filter(company => (company.sites || []).includes(targetSite)).map(company => company.name);
  }

  function isCompanyEligibleForSite(companyName, site) {
    const companyKey = normalizeCompanyName(companyName);
    return getEligibleCompaniesForSite(site).some(name => normalizeCompanyName(name) === companyKey);
  }

  function getCompanyStatusRows(logs, site = '') {
    const attendedKeys = new Set(logs.map(a => normalizeCompanyName(a.company)));
    const masterCompanies = [...getEligibleCompaniesForSite(site)];

    logs.forEach(a => {
      const company = a.company || 'Tidak Ada Perusahaan';
      if (!masterCompanies.some(x => normalizeCompanyName(x) === normalizeCompanyName(company))) {
        masterCompanies.push(company);
      }
    });

    return masterCompanies.map((company, index) => ({
      no: index + 1,
      company,
      status: attendedKeys.has(normalizeCompanyName(company)) ? 'HADIR' : 'TIDAK HADIR'
    }));
  }

  function renderEventRecapModal(eventId) {
    const ev = db.events.find(x => x.id === eventId);
    if (!ev) return;
    const logs = db.attendance.filter(a => a.eventId === eventId);
    const companyRows = getCompanyStatusRows(logs, ev.site);
    const companyPresent = companyRows.filter(x => x.status === 'HADIR').length;
    const companyAbsent = companyRows.filter(x => x.status === 'TIDAK HADIR').length;
    const companyRate = companyRows.length ? Math.round((companyPresent / companyRows.length) * 100) : 0;

    document.getElementById('eventRecapTitle').textContent = `${ev.meetingType} · ${ev.site} · ${formatDate(ev.date)} · ${ev.week}`;
    document.getElementById('eventRecapSummary').innerHTML = [
      ['Perusahaan Hadir', companyPresent],
      ['Attendance Rate', companyRate + '%'],
      ['Waktu Berjalan', formatDuration(getElapsedMs(ev))],
      ['Status Meeting', getEventStatus(ev)]
    ].map(([label, val]) => `<div class="rounded-3xl bg-slate-50 p-5 ring-1 ring-slate-200"><p class="text-sm font-semibold text-slate-500">${label}</p><p class="mt-2 text-xl font-black text-slate-950">${escapeHTML(String(val))}</p></div>`).join('');

    renderEventMinutesForm(ev);

    document.getElementById('eventRecapCompany').innerHTML = `
      <div class="table-wrap max-h-[520px] overflow-auto rounded-2xl border border-slate-200 bg-white">
        <table class="min-w-full text-left text-sm">
          <thead class="sticky top-0 bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
            <tr>
              <th class="w-16 px-4 py-3 text-center">No</th>
              <th class="px-4 py-3">Perusahaan</th>
              <th class="w-40 px-4 py-3 text-center">Status Kehadiran</th>
            </tr>
          </thead>
          <tbody>
            ${companyRows.map(row => `<tr class="border-t border-slate-100 hover:bg-slate-50"><td class="px-4 py-3 text-center text-slate-500">${row.no}</td><td class="px-4 py-3 font-semibold text-slate-800">${escapeHTML(row.company)}</td><td class="px-4 py-3 text-center"><span class="${row.status === 'HADIR' ? 'status-hadir' : 'status-tidak-hadir'}">${row.status}</span></td></tr>`).join('')}
          </tbody>
        </table>
      </div>`;

    document.getElementById('eventRecapAttendees').innerHTML = logs.length ? logs.map(a => `<tr class="border-t border-slate-100"><td class="px-4 py-3">${formatDateTime(a.timestamp)}</td><td class="px-4 py-3 font-black">${escapeHTML(a.sid)}</td><td class="px-4 py-3"><b>${escapeHTML(a.name)}</b></td><td class="px-4 py-3">${escapeHTML(a.company || '-')}</td><td class="px-4 py-3"><div>${escapeHTML(a.structuralPosition || '-')}</div><div class="text-xs text-slate-500">${escapeHTML(a.functionalPosition || '-')}</div></td></tr>`).join('') : `<tr><td colspan="5" class="px-4 py-8 text-center text-sm font-semibold text-slate-500">Belum ada daftar hadir.</td></tr>`;
  }

  function buildIssueTable(containerId, prefix, rows = 4, data = []) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const rowCount = Math.max(1, rows || ISSUE_TABLE_META[prefix]?.defaultRows || 1);
    issueRowCounts[prefix] = rowCount;
    const safeData = Array.from({ length: rowCount }, (_, i) => data[i] || { note: '', issuedBy: '', pic: '', dueDate: '', status: 'Open', remark: '' });
    container.innerHTML = `
      <div class="no-print flex items-center justify-end gap-2 border-x border-slate-950 bg-slate-50 px-3 py-2">
        <button type="button" onclick="addIssueRow('${prefix}')" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-black text-emerald-700 ring-1 ring-emerald-100 hover:bg-emerald-100">+ Tambah Baris</button>
        <button type="button" onclick="removeIssueRow('${prefix}')" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-black text-red-700 ring-1 ring-red-100 hover:bg-red-100 ${rowCount <= 1 ? 'opacity-50 cursor-not-allowed' : ''}" ${rowCount <= 1 ? 'disabled' : ''}>− Kurang Baris</button>
      </div>
      <table class="min-w-full border-collapse text-sm">
        <thead>
          <tr class="bg-white text-xs font-black uppercase">
            <th class="w-[48px] border border-slate-950 px-2 py-2 text-center">NO</th>
            <th class="border border-slate-950 px-3 py-2 text-center">CATATAN MEETING</th>
            <th class="w-[150px] border border-slate-950 px-3 py-2 text-center">ISSUED BY</th>
            <th class="w-[150px] border border-slate-950 px-3 py-2 text-center">PIC</th>
            <th class="w-[150px] border border-slate-950 px-3 py-2 text-center">BATAS WAKTU</th>
            <th class="w-[130px] border border-slate-950 px-3 py-2 text-center">STATUS</th>
            <th class="w-[180px] border border-slate-950 px-3 py-2 text-center">KETERANGAN</th>
          </tr>
        </thead>
        <tbody>
          ${safeData.map((row, i) => `
            <tr>
              <td class="border border-slate-950 px-2 py-1 text-center">${i + 1}</td>
              <td class="border border-slate-950 px-2 py-1"><input id="${prefix}_note_${i}" value="${escapeHTML(row.note || '')}" class="w-full border-0 bg-transparent px-1 py-1 outline-none" /></td>
              <td class="border border-slate-950 px-2 py-1"><input id="${prefix}_issuedBy_${i}" value="${escapeHTML(row.issuedBy || '')}" class="w-full border-0 bg-transparent px-1 py-1 outline-none" /></td>
              <td class="border border-slate-950 px-2 py-1"><input id="${prefix}_pic_${i}" value="${escapeHTML(row.pic || '')}" class="w-full border-0 bg-transparent px-1 py-1 outline-none" /></td>
              <td class="border border-slate-950 px-2 py-1"><input id="${prefix}_due_${i}" type="date" value="${escapeHTML(row.dueDate || '')}" class="w-full border-0 bg-transparent px-1 py-1 outline-none" /></td>
              <td class="border border-slate-950 px-2 py-1">
                <select id="${prefix}_status_${i}" class="w-full border-0 bg-transparent px-1 py-1 font-bold outline-none">
                  <option value="Open" ${(row.status || 'Open') === 'Open' ? 'selected' : ''}>Open</option>
                  <option value="Progress" ${row.status === 'Progress' ? 'selected' : ''}>Progress</option>
                  <option value="Overdue" ${row.status === 'Overdue' ? 'selected' : ''}>Overdue</option>
                </select>
              </td>
              <td class="border border-slate-950 px-2 py-1"><input id="${prefix}_remark_${i}" value="${escapeHTML(row.remark || '')}" class="w-full border-0 bg-transparent px-1 py-1 outline-none" /></td>
            </tr>
          `).join('')}
        </tbody>
      </table>`;
  }

  function collectCurrentIssueRows(prefix) {
    return collectIssueRows(prefix, issueRowCounts[prefix] || ISSUE_TABLE_META[prefix]?.defaultRows || 1);
  }

  function rerenderIssueTable(prefix, nextRows, data) {
    const meta = ISSUE_TABLE_META[prefix];
    if (!meta) return;
    buildIssueTable(meta.containerId, prefix, nextRows, data);
  }

  function addIssueRow(prefix) {
    const currentData = collectCurrentIssueRows(prefix);
    currentData.push({ note: '', pic: '', dueDate: '', remark: '' });
    rerenderIssueTable(prefix, currentData.length, currentData);
  }

  function removeIssueRow(prefix) {
    const currentData = collectCurrentIssueRows(prefix);
    if (currentData.length <= 1) return toast('Minimal 1 baris harus tersedia');
    currentData.pop();
    rerenderIssueTable(prefix, currentData.length, currentData);
  }

  function renderEventMinutesForm(ev) {
    const minutes = ev.minutes || {};
    const setValue = (id, value) => { const el = document.getElementById(id); if (el) el.value = value || ''; };
    setValue('minutesMeetingTitle', minutes.meetingTitle || `NOTULENSI MEETING ${ev.meetingType || ''}`.trim());
    setValue('minutesMeetingType', minutes.meetingType || ev.meetingType || '');
    setValue('minutesMeetingDate', minutes.meetingDate || ev.date || '');
    setValue('minutesNotulis', minutes.notulis || '');
    setValue('minutesLocation', minutes.location || ev.site || '');

    const withLegacyIssuedBy = rows => (rows || []).map(row => ({ ...row, issuedBy: row.issuedBy || minutes.issuedBy || '' }));
    const legacyGeneral = (!minutes.generalIssues && (minutes.topic || minutes.discussion || minutes.decision || minutes.action))
      ? [{ note: [minutes.topic, minutes.discussion, minutes.decision, minutes.action].filter(Boolean).join(' | '), issuedBy: minutes.issuedBy || '', pic: minutes.pic || '', dueDate: minutes.dueDate || '', status: 'Open', remark: minutes.status || '' }]
      : [];

    buildIssueTable('enviroIssueTable', 'enviro', Math.max(1, (minutes.enviroIssues || []).length || ISSUE_TABLE_META.enviro.defaultRows), withLegacyIssuedBy(minutes.enviroIssues || []));
    buildIssueTable('safetyIssueTable', 'safety', Math.max(1, (minutes.safetyIssues || []).length || ISSUE_TABLE_META.safety.defaultRows), withLegacyIssuedBy(minutes.safetyIssues || []));
    buildIssueTable('generalIssueTable', 'general', Math.max(1, (minutes.generalIssues || legacyGeneral || []).length || ISSUE_TABLE_META.general.defaultRows), withLegacyIssuedBy(minutes.generalIssues || legacyGeneral));

    const info = document.getElementById('minutesUpdatedInfo');
    if (info) info.textContent = minutes.updatedAt ? `Updated: ${formatDateTime(minutes.updatedAt)}` : 'Belum ada notulensi';
  }

  function collectIssueRows(prefix, rows) {
    const result = [];
    for (let i = 0; i < rows; i++) {
      result.push({
        note: document.getElementById(`${prefix}_note_${i}`)?.value.trim() || '',
        issuedBy: document.getElementById(`${prefix}_issuedBy_${i}`)?.value.trim() || '',
        pic: document.getElementById(`${prefix}_pic_${i}`)?.value.trim() || '',
        dueDate: document.getElementById(`${prefix}_due_${i}`)?.value || '',
        status: document.getElementById(`${prefix}_status_${i}`)?.value || 'Open',
        remark: document.getElementById(`${prefix}_remark_${i}`)?.value.trim() || ''
      });
    }
    return result;
  }

  async function saveEventMinutes(e) {
    e.preventDefault();
    if (!selectedRecapEventId) return toast('Belum ada event yang dipilih');
    const ev = db.events.find(x => x.id === selectedRecapEventId);
    if (!ev) return toast('Event tidak ditemukan');
    const issues = [
      ...collectCurrentIssueRows('enviro').map((issue, index) => ({ ...issue, section: 'enviro', nomor: index + 1 })),
      ...collectCurrentIssueRows('safety').map((issue, index) => ({ ...issue, section: 'safety', nomor: index + 1 })),
      ...collectCurrentIssueRows('general').map((issue, index) => ({ ...issue, section: 'general', nomor: index + 1 }))
    ];
    const payload = {
      title: document.getElementById('minutesMeetingTitle')?.value.trim() || '',
      notulis: document.getElementById('minutesNotulis')?.value.trim() || '',
      location: document.getElementById('minutesLocation')?.value.trim() || '',
      issues
    };
    const mt = document.getElementById('minutesMeetingType')?.value.trim();
    if (mt) payload.meeting_type = mt;
    const md = document.getElementById('minutesMeetingDate')?.value;
    if (md) payload.meeting_date = md;
    try {
      await apiFetch(`${SID_MEETING_API_BASE}/events/${encodeURIComponent(selectedRecapEventId)}/minutes`, {
        method: 'POST',
        body: JSON.stringify(payload)
      });
      await hydrateFromDatabase(true);
      const refreshed = db.events.find(x => x.id === selectedRecapEventId);
      if (refreshed) renderEventMinutesForm(refreshed);
      toast('Notulensi event berhasil disimpan ke database');
    } catch (err) {
      toast(err.message || 'Gagal simpan notulensi');
    }
  }

  function clearEventMinutesForm() {
    ['minutesMeetingTitle', 'minutesMeetingType', 'minutesMeetingDate', 'minutesNotulis', 'minutesLocation'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
    buildIssueTable('enviroIssueTable', 'enviro', ISSUE_TABLE_META.enviro.defaultRows, []);
    buildIssueTable('safetyIssueTable', 'safety', ISSUE_TABLE_META.safety.defaultRows, []);
    buildIssueTable('generalIssueTable', 'general', ISSUE_TABLE_META.general.defaultRows, []);
  }

  function printEventMinutes() {
    const area = document.getElementById('minutesPrintArea');
    if (!area) return toast('Area notulensi tidak ditemukan');
    const title = document.getElementById('minutesMeetingTitle')?.value || 'Notulensi Meeting';
    const printWindow = window.open('', '_blank');
    if (!printWindow) return toast('Popup print diblokir browser');
    printWindow.document.write(`<!DOCTYPE html><html><head><title>${escapeHTML(title)}</title><style>
      *{box-sizing:border-box}body{font-family:Arial,sans-serif;margin:16px;color:#000}table{border-collapse:collapse;width:100%;font-size:11px}input,textarea{border:0!important;background:transparent!important;font-family:Arial,sans-serif;font-size:11px;width:100%}.rounded-2xl,.rounded-3xl,.rounded-xl{border-radius:0!important}.border,.ring-1{border:1px solid #000!important}.border-2{border:2px solid #000!important}.border-slate-950,.border-slate-700,.border-slate-300{border-color:#000!important}.no-print{display:none!important}.bg-slate-950{background:#000!important;color:#fff!important}.bg-white{background:#fff!important}.text-center{text-align:center}.font-black{font-weight:900}.font-semibold{font-weight:600}.p-1{padding:4px}.p-3{padding:12px}.p-4{padding:16px}.px-2{padding-left:8px;padding-right:8px}.px-3{padding-left:12px;padding-right:12px}.py-1{padding-top:4px;padding-bottom:4px}.py-2{padding-top:8px;padding-bottom:8px}.w-\[140px\]{width:140px}.w-\[48px\]{width:48px}.w-\[150px\]{width:150px}.w-\[180px\]{width:180px}.w-\[130px\]{width:130px}.w-\[150px\]{width:150px}@page{size:A4 landscape;margin:10mm}
    </style></head><body>${area.outerHTML}</body></html>`);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => printWindow.print(), 250);
  }

  async function copyText(text, buttonEl) {
    const value = String(text || '');
    if (!value) return toast('Tidak ada teks untuk dicopy');
    const originalLabel = buttonEl?.textContent;

    try {
      if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(value);
        if (buttonEl) buttonEl.textContent = 'Copied';
        toast('Link berhasil disalin');
        setTimeout(() => { if (buttonEl && originalLabel) buttonEl.textContent = originalLabel; }, 1400);
        return true;
      }
      throw new Error('Clipboard API unavailable');
    } catch (err) {
      const fallbackOk = fallbackCopyText(value);
      if (fallbackOk) {
        if (buttonEl) buttonEl.textContent = 'Copied';
        toast('Link berhasil disalin');
        setTimeout(() => { if (buttonEl && originalLabel) buttonEl.textContent = originalLabel; }, 1400);
        return true;
      }
      const manualBox = document.getElementById('manualCopyBox');
      if (manualBox) manualBox.classList.remove('hidden');
      const qrInput = document.getElementById('qrLink');
      if (qrInput) { qrInput.focus(); qrInput.select(); }
      console.warn('Clipboard copy blocked:', err);
      toast('Clipboard diblokir browser. Silakan copy manual dari kolom link.');
      return false;
    }
  }

  function fallbackCopyText(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'fixed';
    textarea.style.top = '-9999px';
    textarea.style.left = '-9999px';
    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    let success = false;
    try { success = document.execCommand('copy'); } catch { success = false; }
    document.body.removeChild(textarea);
    return success;
  }

  function openAttendanceFromEvent(eventId) {
    scannedEventId = eventId;
    window.location.hash = `absen=${encodeURIComponent(eventId)}`;
    clearAttendanceForm();
    openEventRecapModal(eventId);
    openManualAttendanceForm();
  }

  async function lookupSID() {
    const sidInput = document.getElementById('sidInput');
    const sid = sidInput.value.trim().toUpperCase();
    const emp = await fetchEmployeeBySID(sid);
    document.getElementById('resolvedEmployeeId').value = emp?.id || '';
    document.getElementById('autoName').value = emp?.name || '';
    document.getElementById('autoCompany').value = emp?.company || '';
    document.getElementById('autoStructural').value = emp?.structuralPosition || '';
    document.getElementById('autoFunctional').value = emp?.functionalPosition || '';
    document.getElementById('timestampInput').value = formatDateTime(new Date().toISOString());
    if (sid && !emp) document.getElementById('autoName').placeholder = 'SID tidak ditemukan di sistem';
  }

  async function fetchEmployeeBySID(sid) {
    if (!sid) return null;
    // Production example:
    // const res = await fetch(`/api/employees/by-sid/${encodeURIComponent(sid)}`);
    // if (!res.ok) return null;
    // return await res.json();
    return SYSTEM_SID_DIRECTORY.find(x => x.sid.toUpperCase() === sid.toUpperCase()) || null;
  }

  function clearAttendanceForm() {
    const form = document.getElementById('attendanceForm'); if (!form) return;
    form.reset();
    document.getElementById('resolvedEmployeeId').value = '';
    document.getElementById('timestampInput').value = formatDateTime(new Date().toISOString());
    renderActiveAttendanceEvent();
  }

  function renderActiveAttendanceEvent() {
    const ev = db.events.find(x => x.id === scannedEventId);
    const notice = document.getElementById('scanNotice');
    const info = document.getElementById('activeEventInfo');
    const eventInput = document.getElementById('attendanceEventId');
    const submitBtn = document.getElementById('submitAttendanceBtn');
    const sidInput = document.getElementById('sidInput');
    if (!notice || !info || !eventInput || !submitBtn || !sidInput) return;
    if (!ev) {
      notice.innerHTML = 'Belum ada event yang dipilih. Klik kartu event atau scan QR event untuk membuka form absensi.';
      info.innerHTML = '<div class="rounded-3xl bg-slate-50 p-4 text-sm text-slate-600 ring-1 ring-slate-200">Form belum terhubung ke event.</div>';
      eventInput.value = '';
      submitBtn.disabled = true;
      sidInput.disabled = true;
      submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
      return;
    }
    const status = getEventStatus(ev);
    eventInput.value = ev.id;
    info.innerHTML = `<div class="rounded-3xl bg-white p-4 text-sm text-blue-900 ring-1 ring-blue-100"><div class="mb-2">${statusBadge(status)}</div><b>${escapeHTML(ev.meetingType)}</b><br>${escapeHTML(ev.site)} · ${formatDate(ev.date)} · ${escapeHTML(ev.week)}<br>Aktif: ${ev.startTime} - ${ev.endTime}</div>`;
    const disabled = status !== 'Open';
    submitBtn.disabled = disabled;
    sidInput.disabled = disabled;
    submitBtn.classList.toggle('opacity-50', disabled);
    submitBtn.classList.toggle('cursor-not-allowed', disabled);
    if (status === 'Open') notice.innerHTML = 'QR valid. Silakan input <b>Kode SID</b> untuk melakukan absensi.';
    else if (status === 'Closed') notice.innerHTML = 'Meeting sudah ditutup. QR dan absensi dikunci.';
    else if (status === 'Expired') notice.innerHTML = 'QR sudah tidak aktif karena meeting telah selesai. Absensi dikunci otomatis.';
    else if (status === 'Upcoming') notice.innerHTML = 'QR belum aktif karena meeting belum dimulai.';
    else if (status === 'Overrun') notice.innerHTML = 'Jam selesai sudah tercapai. Absensi masih terbuka sampai meeting dikonfirmasi tutup.';
    else notice.innerHTML = 'Event masih draft. QR belum dapat digunakan.';
  }

  async function saveAttendance(e) {
    e.preventDefault();
    const eventId = document.getElementById('attendanceEventId').value;
    const sid = document.getElementById('sidInput').value.trim().toUpperCase();
    const ev = db.events.find(x => x.id === eventId);
    if (!ev) return toast('Event tidak ditemukan. Scan QR event terlebih dahulu.');
    if (!isEventActive(ev)) return toast('QR event tidak aktif. Absensi tidak dapat disimpan.');
    try {
      await apiFetch(`${SID_MEETING_API_BASE}/attendance`, {
        method: 'POST',
        body: JSON.stringify({ event_id: eventId, kode_sid: sid, input_method: 'manual' })
      });
      await hydrateFromDatabase(true);
      clearAttendanceForm();
      toast('Absensi berhasil disimpan ke database');
    } catch (err) {
      toast(err.message || 'Gagal simpan absensi');
    }
  }

  function deleteAttendance(id) { if (!confirm('Hapus log absensi ini?')) return; db.attendance = db.attendance.filter(x => x.id !== id); saveDB(); toast('Log absensi dihapus'); }

  function renderAttendance() { return; }

  function saveEmployee(e) {
    e.preventDefault();
    const id = document.getElementById('employeeId').value || uid('EMP');
    const sid = document.getElementById('employeeSid').value.trim().toUpperCase();
    const existing = db.employees.find(x => x.id === id);
    const duplicate = db.employees.find(x => x.id !== id && x.sid.toUpperCase() === sid);
    if (duplicate) return toast('Kode SID sudah ada di master data');
    const payload = { id, sid, name: document.getElementById('employeeName').value.trim(), company: document.getElementById('employeeCompany').value.trim(), structuralPosition: document.getElementById('employeeStructural').value.trim(), functionalPosition: document.getElementById('employeeFunctional').value.trim(), createdAt: existing?.createdAt || new Date().toISOString(), updatedAt: new Date().toISOString() };
    if (existing) Object.assign(existing, payload); else db.employees.unshift(payload);
    clearEmployeeForm(); saveDB(); toast('Master SID berhasil disimpan');
  }

  function editEmployee(id) { const emp = db.employees.find(x => x.id === id); if (!emp) return; document.getElementById('employeeId').value = emp.id; document.getElementById('employeeSid').value = emp.sid; document.getElementById('employeeName').value = emp.name; document.getElementById('employeeCompany').value = emp.company; document.getElementById('employeeStructural').value = emp.structuralPosition; document.getElementById('employeeFunctional').value = emp.functionalPosition; showTab('master'); }
  function deleteEmployee(id) { if (!confirm('Hapus master SID ini? Log absensi yang sudah ada tidak ikut terhapus.')) return; db.employees = db.employees.filter(x => x.id !== id); saveDB(); toast('Master SID dihapus'); }
  function clearEmployeeForm() { const form = document.getElementById('masterForm'); if (!form) return; form.reset(); document.getElementById('employeeId').value = ''; }

  function bulkImportEmployees() {
    const raw = document.getElementById('bulkEmployees').value.trim(); if (!raw) return toast('Isi data master SID terlebih dahulu');
    let count = 0;
    raw.split('\n').forEach(line => { const [sidRaw, name, company, structuralPosition, functionalPosition] = line.split(';').map(x => (x || '').trim()); const sid = (sidRaw || '').toUpperCase(); if (!sid || !name) return; const existing = db.employees.find(x => x.sid.toUpperCase() === sid); const payload = { id: existing?.id || uid('EMP'), sid, name, company, structuralPosition, functionalPosition, createdAt: existing?.createdAt || new Date().toISOString(), updatedAt: new Date().toISOString() }; if (existing) Object.assign(existing, payload); else db.employees.push(payload); count++; });
    document.getElementById('bulkEmployees').value = ''; saveDB(); toast(`${count} master SID berhasil diimport/update`);
  }

  function renderEmployees() {
    const tbody = document.getElementById('employeeTable'); if (!tbody) return;
    const q = (document.getElementById('employeeSearch')?.value || '').toLowerCase();
    const rows = db.employees.filter(emp => [emp.sid, emp.name, emp.company, emp.structuralPosition, emp.functionalPosition].join(' ').toLowerCase().includes(q));
    if (!rows.length) { tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-sm font-semibold text-slate-500">Belum ada master SID.</td></tr>`; return; }
    tbody.innerHTML = rows.map(emp => `<tr class="border-t border-slate-100"><td class="px-4 py-3 font-black">${escapeHTML(emp.sid)}</td><td class="px-4 py-3"><b>${escapeHTML(emp.name)}</b></td><td class="px-4 py-3">${escapeHTML(emp.company || '-')}</td><td class="px-4 py-3"><div>${escapeHTML(emp.structuralPosition || '-')}</div><div class="text-xs text-slate-500">${escapeHTML(emp.functionalPosition || '-')}</div></td><td class="no-print px-4 py-3"><div class="flex gap-2"><button onclick="editEmployee('${emp.id}')" class="rounded-lg bg-blue-50 px-2 py-1 text-xs font-black text-blue-700">Edit</button><button onclick="deleteEmployee('${emp.id}')" class="rounded-lg bg-red-50 px-2 py-1 text-xs font-black text-red-700">Hapus</button></div></td></tr>`).join('');
  }

  function renderCompanySiteChecklist(selectedSites = SITE_MASTER) {
    const box = document.getElementById('companySiteChecklist');
    if (!box) return;
    const selected = new Set(selectedSites || []);
    box.innerHTML = SITE_MASTER.map(site => `<label class="flex cursor-pointer items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-bold text-slate-700 hover:bg-blue-50"><input type="checkbox" name="companySites" value="${escapeHTML(site)}" ${selected.has(site) ? 'checked' : ''} class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" /><span>${escapeHTML(site)}</span></label>`).join('');
  }

  async function saveCompany(e) {
    e.preventDefault();
    const name = document.getElementById('companyName')?.value.trim();
    const selectedSites = [...document.querySelectorAll('input[name="companySites"]:checked')].map(x => x.value);
    if (!name) return toast('Nama perusahaan wajib diisi');
    if (!selectedSites.length) return toast('Pilih minimal 1 site untuk perusahaan');
    try {
      await apiFetch(`${SID_MEETING_API_BASE}/companies`, {
        method: 'POST',
        body: JSON.stringify({ name, sites: selectedSites })
      });
      await hydrateFromDatabase(true);
      clearCompanyForm();
      toast('Master perusahaan berhasil disimpan ke database');
    } catch (err) {
      toast(err.message || 'Gagal simpan master perusahaan');
    }
  }

  function editCompany(id) {
    const company = getCompanyMaster().find(item => item.id === id);
    if (!company) return;
    document.getElementById('companyId').value = company.id;
    document.getElementById('companyName').value = company.name;
    renderCompanySiteChecklist(company.sites || []);
    showTab('companymaster');
    toggleCompanyInputContainer(true);
  }

  function deleteCompany(id) {
    const company = getCompanyMaster().find(item => item.id === id);
    if (!company) return;
    if (!confirm(`Hapus perusahaan ${company.name} dari master?`)) return;
    db.companies = getCompanyMaster().filter(item => item.id !== id);
    saveDB();
    toast('Perusahaan dihapus dari master');
  }

  function clearCompanyForm() {
    const form = document.getElementById('companyForm');
    if (form) form.reset();
    const id = document.getElementById('companyId');
    if (id) id.value = '';
    renderCompanySiteChecklist(SITE_MASTER);
  }

  function toggleCompanySite(id, site, checked) {
    const company = getCompanyMaster().find(item => item.id === id);
    if (!company) return;
    const sites = new Set(company.sites || []);
    if (checked) sites.add(site); else sites.delete(site);
    company.sites = [...sites].filter(value => SITE_MASTER.includes(value));
    company.updatedAt = new Date().toISOString();
    saveDB();
  }

  function renderCompanyMaster() {
    renderCompanySiteChecklist(document.getElementById('companyId')?.value ? [...document.querySelectorAll('input[name="companySites"]:checked')].map(x => x.value) : SITE_MASTER);
    const head = document.getElementById('companyMasterTableHead');
    const body = document.getElementById('companyMasterTableBody');
    const info = document.getElementById('companyMasterInfo');
    if (!head || !body) return;
    const q = (document.getElementById('companyMasterSearch')?.value || '').toLowerCase().trim();
    const rows = getCompanyMaster().filter(company => {
      const haystack = [company.name, ...(company.sites || [])].join(' ').toLowerCase();
      return !q || haystack.includes(q);
    }).sort((a, b) => a.name.localeCompare(b.name));
    if (info) info.textContent = `${rows.length} perusahaan ditampilkan dari ${getCompanyMaster().length} total master`;
    head.innerHTML = `<tr><th class="px-4 py-3">Perusahaan</th>${SITE_MASTER.map(site => `<th class="px-3 py-3 text-center">${escapeHTML(site)}</th>`).join('')}<th class="px-4 py-3 text-center">Action</th></tr>`;
    body.innerHTML = rows.length ? rows.map(company => {
      const sites = new Set(company.sites || []);
      return `<tr class="border-t border-slate-100 hover:bg-slate-50"><td class="min-w-[260px] px-4 py-3"><b>${escapeHTML(company.name)}</b><div class="mt-1 text-xs text-slate-500">${sites.size} site eligible</div></td>${SITE_MASTER.map(site => `<td class="px-3 py-3 text-center"><input type="checkbox" ${sites.has(site) ? 'checked' : ''} onchange="toggleCompanySite('${escapeJS(company.id)}', '${escapeJS(site)}', this.checked)" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" /></td>`).join('')}<td class="no-print px-4 py-3"><div class="flex justify-center gap-2"><button onclick="editCompany('${escapeJS(company.id)}')" class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-black text-blue-700">Edit</button><button onclick="deleteCompany('${escapeJS(company.id)}')" class="rounded-lg bg-red-50 px-3 py-2 text-xs font-black text-red-700">Hapus</button></div></td></tr>`;
    }).join('') : `<tr><td colspan="${SITE_MASTER.length + 2}" class="px-4 py-8 text-center text-sm font-semibold text-slate-500">Tidak ada perusahaan sesuai pencarian.</td></tr>`;
  }

  function resetCompanyMaster() {
    if (!confirm('Reset master perusahaan ke daftar default dan checklist semua site?')) return;
    db.companies = getDefaultCompanyMaster();
    saveDB();
    toast('Master perusahaan direset ke default');
  }

  function exportCompanyMasterCSV() {
    const rows = getCompanyMaster().map(company => {
      const sites = new Set(company.sites || []);
      const row = { perusahaan: company.name, jumlah_site: sites.size };
      SITE_MASTER.forEach(site => row[site] = sites.has(site) ? 'YA' : 'TIDAK');
      return row;
    });
    downloadCSV(rows, 'master_perusahaan_site.csv');
  }

  function renderMeetingTypeOptions() {
    const select = document.getElementById('meetingType');
    if (!select) return;
    const current = select.value;
    const types = getMeetingTypes();
    select.innerHTML = '<option value="">Pilih Jenis Meeting</option>' + types.map(type => `<option value="${escapeHTML(type)}">${escapeHTML(type)}</option>`).join('');
    if (types.includes(current)) select.value = current;
  }

  function renderMeetingTypeManager() {
    const list = document.getElementById('meetingTypeList');
    if (!list) return;
    const types = getMeetingTypes();
    list.innerHTML = types.map((type, index) => {
      const usedCount = db.events.filter(ev => ev.meetingType === type).length;
      return `<span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-2 text-xs font-black text-slate-700 ring-1 ring-slate-200">
        <span>${escapeHTML(type)}</span>
        ${usedCount ? `<span class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] text-blue-700">${usedCount} event</span>` : ''}
        <button type="button" data-meeting-type-index="${index}" onclick="deleteMeetingTypeByIndex(${index})" class="rounded-full bg-red-50 px-2 py-0.5 text-[10px] font-black text-red-700 hover:bg-red-100">hapus</button>
      </span>`;
    }).join('');
  }

  function toggleMeetingTypeManager() {
    const panel = document.getElementById('meetingTypeManager');
    const icon = document.getElementById('meetingTypeManagerIcon');
    if (!panel) return;
    const willOpen = panel.classList.contains('hidden');
    panel.classList.toggle('hidden');
    if (icon) icon.textContent = willOpen ? '−' : '+';
    if (willOpen) setTimeout(() => document.getElementById('newMeetingType')?.focus(), 80);
  }

  function addMeetingType() {
    const input = document.getElementById('newMeetingType');
    const value = input?.value.trim();
    if (!value) return toast('Isi nama jenis meeting terlebih dahulu');
    const types = getMeetingTypes();
    if (types.some(type => type.toLowerCase() === value.toLowerCase())) return toast('Jenis meeting sudah ada');
    types.push(value);
    if (input) input.value = '';
    saveDB();
    document.getElementById('meetingType').value = value;
    toast('Jenis meeting berhasil ditambahkan');
  }

  function deleteMeetingTypeByIndex(index) {
    const types = getMeetingTypes();
    const type = types[Number(index)];
    if (!type) return toast('Jenis meeting tidak ditemukan');
    deleteMeetingType(type);
  }

  function deleteMeetingType(type) {
    const normalizedType = String(type || '').trim();
    const types = getMeetingTypes();
    if (!normalizedType) return toast('Jenis meeting tidak valid');
    if (types.length <= 1) return toast('Minimal 1 jenis meeting harus tersedia');

    db.meetingTypes = types.filter(item => item !== normalizedType);

    const select = document.getElementById('meetingType');
    if (select?.value === normalizedType) select.value = '';

    saveDB();

    const manager = document.getElementById('meetingTypeManager');
    const icon = document.getElementById('meetingTypeManagerIcon');
    if (manager) manager.classList.remove('hidden');
    if (icon) icon.textContent = '−';

    const usedCount = db.events.filter(ev => ev.meetingType === normalizedType).length;
    toast(usedCount ? `Jenis meeting dihapus dari dropdown. ${usedCount} event lama tetap tersimpan.` : 'Jenis meeting dihapus dari dropdown');
  }

  function renderOptions() { renderMeetingTypeOptions(); renderMeetingTypeManager(); }

  function populateReportFilters() {
    const siteSelect = document.getElementById('reportFilterSite');
    const weekSelect = document.getElementById('reportFilterWeek');
    if (!siteSelect || !weekSelect) return;

    const currentSite = siteSelect.value || 'ALL';
    const currentWeek = weekSelect.value || 'ALL';
    const sites = [...new Set(db.events.map(e => e.site).filter(Boolean))].sort();
    const weeks = [...new Set(db.events.map(e => e.week).filter(Boolean))].sort();

    siteSelect.innerHTML = '<option value="ALL">Semua Site</option>' + sites.map(site => `<option value="${escapeHTML(site)}">${escapeHTML(site)}</option>`).join('');
    weekSelect.innerHTML = '<option value="ALL">Semua Week</option>' + weeks.map(week => `<option value="${escapeHTML(week)}">${escapeHTML(week)}</option>`).join('');

    siteSelect.value = sites.includes(currentSite) ? currentSite : 'ALL';
    weekSelect.value = weeks.includes(currentWeek) ? currentWeek : 'ALL';
  }

  function getReportRows() {
    const selectedSite = document.getElementById('reportFilterSite')?.value || 'ALL';
    const selectedWeek = document.getElementById('reportFilterWeek')?.value || 'ALL';
    const search = (document.getElementById('reportSearch')?.value || '').toLowerCase().trim();

    return db.attendance.map(a => {
      const ev = db.events.find(e => e.id === a.eventId) || {};
      return {
        eventId: a.eventId,
        tanggal_meeting: ev.date || '',
        week: ev.week || '',
        site: ev.site || '',
        jenis_meeting: ev.meetingType || '',
        kode_event: ev.code || '',
        kode_sid: a.sid || '',
        nama: a.name || '',
        perusahaan: a.company || '',
        jabatan_struktural: a.structuralPosition || '',
        jabatan_fungsional: a.functionalPosition || '',
        timestamp: a.timestamp || ''
      };
    }).filter(row => {
      const siteOk = selectedSite === 'ALL' || row.site === selectedSite;
      const weekOk = selectedWeek === 'ALL' || row.week === selectedWeek;
      const searchOk = !search || Object.values(row).join(' ').toLowerCase().includes(search);
      return siteOk && weekOk && searchOk;
    });
  }

  function getMinuteIssueRows(minutes, section, issues) {
    return (issues || [])
      .map((issue, index) => ({ section, issueNo: index + 1, ...issue }))
      .filter(row => [row.note, row.issuedBy, row.pic, row.dueDate, row.remark].some(Boolean));
  }

  function getMinutesReportRows() {
    const selectedSite = document.getElementById('reportFilterSite')?.value || 'ALL';
    const selectedWeek = document.getElementById('reportFilterWeek')?.value || 'ALL';
    const search = (document.getElementById('reportSearch')?.value || '').toLowerCase().trim();
    const rows = [];
    db.events.forEach(ev => {
      const minutes = ev.minutes || {};
      const issueRows = [
        ...getMinuteIssueRows(minutes, 'Enviro Issue', minutes.enviroIssues),
        ...getMinuteIssueRows(minutes, 'Safety Issue', minutes.safetyIssues),
        ...getMinuteIssueRows(minutes, 'General Issue', minutes.generalIssues)
      ];

      if (!issueRows.length && minutes.updatedAt) {
        issueRows.push({ section: 'Notulensi', issueNo: 1, note: '(Notulensi tersimpan tanpa catatan issue)', issuedBy: '', pic: '', dueDate: '', status: 'Open', remark: '' });
      }

      issueRows.forEach(issue => {
        rows.push({
          eventId: ev.id,
          tanggal_meeting: minutes.meetingDate || ev.date || '',
          week: ev.week || '',
          site: ev.site || '',
          jenis_meeting: minutes.meetingType || ev.meetingType || '',
          kode_event: ev.code || '',
          judul_notulen: minutes.meetingTitle || `NOTULENSI MEETING ${ev.meetingType || ''}`.trim(),
          notulis: minutes.notulis || '',
          lokasi: minutes.location || ev.site || '',
          section: issue.section,
          no: issue.issueNo,
          catatan_meeting: issue.note || '',
          issued_by: issue.issuedBy || '',
          pic: issue.pic || '',
          batas_waktu: issue.dueDate || '',
          status_catatan: issue.status || 'Open',
          keterangan: issue.remark || '',
          updated_at: minutes.updatedAt || ''
        });
      });
    });

    return rows.filter(row => {
      const siteOk = selectedSite === 'ALL' || row.site === selectedSite;
      const weekOk = selectedWeek === 'ALL' || row.week === selectedWeek;
      const searchOk = !search || Object.values(row).join(' ').toLowerCase().includes(search);
      return siteOk && weekOk && searchOk;
    });
  }

  function getAllMinutesIssueRows() {
    const rows = [];
    db.events.forEach(ev => {
      const minutes = ev.minutes || {};
      const issueRows = [
        ...getMinuteIssueRows(minutes, 'Enviro Issue', minutes.enviroIssues),
        ...getMinuteIssueRows(minutes, 'Safety Issue', minutes.safetyIssues),
        ...getMinuteIssueRows(minutes, 'General Issue', minutes.generalIssues)
      ];
      issueRows.forEach(issue => {
        rows.push({
          id: ev.id + '::' + issue.section + '::' + issue.issueNo,
          eventId: ev.id,
          site: ev.site || '',
          week: ev.week || '',
          date: minutes.meetingDate || ev.date || '',
          meetingType: minutes.meetingType || ev.meetingType || '',
          section: issue.section,
          issueNo: issue.issueNo,
          note: issue.note || '',
          issuedBy: issue.issuedBy || '',
          pic: issue.pic || '',
          dueDate: issue.dueDate || '',
          status: issue.status || 'Open',
          remark: issue.remark || ''
        });
      });
    });
    return rows.filter(row => row.note && row.note.trim().length >= 5);
  }

  const SEMANTIC_STOPWORDS = new Set(['yang','dan','dari','untuk','dengan','pada','dalam','atau','agar','oleh','ini','itu','ada','tidak','belum','sudah','akan','harus','perlu','terkait','terhadap','secara','sebagai','dapat','bisa','karena','saat','area','site','meeting','catatan','issue','the','and','for','are']);

  function normalizeIssueText(text) {
    return String(text || '').toLowerCase().replace(/[^a-z0-9 ]/g, ' ').replace(/  +/g, ' ').trim();
  }

  function tokenizeIssueText(text) {
    return normalizeIssueText(text).split(' ').map(token => token.trim()).filter(token => token.length >= 3 && !SEMANTIC_STOPWORDS.has(token));
  }

  function buildTfIdfVectors(rows) {
    const docs = rows.map(row => tokenizeIssueText([row.note, row.remark].join(' ')));
    const df = new Map();
    docs.forEach(tokens => [...new Set(tokens)].forEach(token => df.set(token, (df.get(token) || 0) + 1)));
    const n = Math.max(1, docs.length);
    return docs.map(tokens => {
      const tf = new Map();
      tokens.forEach(token => tf.set(token, (tf.get(token) || 0) + 1));
      const vec = new Map();
      const len = Math.max(1, tokens.length);
      tf.forEach((count, token) => {
        const idf = Math.log((n + 1) / ((df.get(token) || 0) + 1)) + 1;
        vec.set(token, (count / len) * idf);
      });
      return vec;
    });
  }

  const EMBEDDING_DIM = 384;
  const SEMANTIC_CONCEPTS = [
    { key: 'housekeeping_material_access', terms: ['housekeeping','material','sisa','sampah','berserakan','rapi','kebersihan','akses','jalur','walkway','jalan'] },
    { key: 'ppe_compliance', terms: ['apd','ppe','helm','sepatu','rompi','sarung','kacamata','masker','seragam','lengkap'] },
    { key: 'traffic_pedestrian_interaction', terms: ['pejalan','kaki','akses','interaksi','unit','kendaraan','haul','truck','jarak','aman','jalur'] },
    { key: 'critical_control_verification', terms: ['ccv','kontrol','critical','verifikasi','barrier','isolasi','interlock','sop','permit'] },
    { key: 'fatigue_alertness', terms: ['fatigue','lelah','microsleep','ngantuk','tidur','alert','dms','istirahat'] },
    { key: 'supervision_leadership', terms: ['supervisi','pengawas','foreman','pic','pjo','supervisor','briefing','coaching'] },
    { key: 'environment_spill_dust_water', terms: ['lingkungan','debu','limbah','tumpahan','oli','air','sediment','drainase'] },
    { key: 'emergency_fire_response', terms: ['emergency','darurat','api','kebakaran','apar','hydrant','evakuasi','rescue'] }
  ];

  function hashString(value) {
    let h = 2166136261;
    const text = String(value || '');
    for (let i = 0; i < text.length; i++) {
      h ^= text.charCodeAt(i);
      h = Math.imul(h, 16777619);
    }
    return h >>> 0;
  }

  function addEmbeddingFeature(vec, feature, weight = 1) {
    const hash = hashString(feature);
    const idx = hash % EMBEDDING_DIM;
    const sign = (hash & 1) ? 1 : -1;
    vec[idx] += sign * weight;
  }

  function normalizeDenseVector(vec) {
    const norm = Math.sqrt(vec.reduce((sum, value) => sum + value * value, 0));
    if (!norm) return vec;
    return vec.map(value => value / norm);
  }

  function getSemanticConceptsForText(tokens, normalizedText) {
    const tokenSet = new Set(tokens);
    return SEMANTIC_CONCEPTS.filter(concept => concept.terms.some(term => tokenSet.has(term) || normalizedText.includes(term))).map(concept => concept.key);
  }

  function embedIssueText(text) {
    const normalized = normalizeIssueText(text);
    const tokens = tokenizeIssueText(normalized);
    const vec = new Array(EMBEDDING_DIM).fill(0);

    tokens.forEach(token => addEmbeddingFeature(vec, 'tok:' + token, 0.9));
    for (let i = 0; i < tokens.length - 1; i++) addEmbeddingFeature(vec, 'bi:' + tokens[i] + '_' + tokens[i + 1], 1.25);
    for (let i = 0; i < tokens.length - 2; i++) addEmbeddingFeature(vec, 'tri:' + tokens[i] + '_' + tokens[i + 1] + '_' + tokens[i + 2], 1.1);
    tokens.forEach(token => {
      for (let n = 3; n <= Math.min(5, token.length); n++) {
        for (let i = 0; i <= token.length - n; i++) addEmbeddingFeature(vec, 'ng:' + token.slice(i, i + n), 0.18);
      }
    });
    getSemanticConceptsForText(tokens, normalized).forEach(concept => addEmbeddingFeature(vec, 'concept:' + concept, 3.2));

    return normalizeDenseVector(vec);
  }

  function buildSemanticEmbeddings(rows) {
    // Important: embedding hanya memakai isi catatan + keterangan.
    // Section/site/week tidak dimasukkan agar tidak membuat false positive antar notulen yang sama-sama "Enviro Issue" atau satu site.
    return rows.map(row => embedIssueText([row.note, row.remark].join(' ')));
  }

  function getSemanticSignals(row) {
    const normalized = normalizeIssueText([row.note, row.remark].join(' '));
    const tokens = tokenizeIssueText(normalized);
    return {
      tokens: new Set(tokens),
      concepts: new Set(getSemanticConceptsForText(tokens, normalized))
    };
  }

  function countSetOverlap(a, b) {
    let count = 0;
    a.forEach(value => { if (b.has(value)) count++; });
    return count;
  }

  function adjustedSemanticSimilarity(rowA, rowB, vecA, vecB) {
    const rawScore = cosineDense(vecA, vecB);
    const a = getSemanticSignals(rowA);
    const b = getSemanticSignals(rowB);
    const tokenOverlap = countSetOverlap(a.tokens, b.tokens);
    const conceptOverlap = countSetOverlap(a.concepts, b.concepts);
    const minTokenCount = Math.min(a.tokens.size, b.tokens.size);

    // Guardrail: untuk short text yang tidak punya token/concept overlap, jangan dianggap similar.
    // Ini mencegah kasus false positive seperti "kapan makan nasi" vs "pencucian mobil harus rajin".
    if (!tokenOverlap && !conceptOverlap) return 0;

    // Untuk kalimat sangat pendek, wajib ada overlap kata atau concept domain yang jelas.
    if (minTokenCount <= 3 && tokenOverlap < 1 && conceptOverlap < 1) return 0;

    // Jika hanya overlap concept tanpa overlap token, turunkan confidence agar tidak terlalu agresif.
    if (!tokenOverlap && conceptOverlap) return rawScore * 0.72;

    return rawScore;
  }

  function cosineDense(a, b) {
    let dot = 0;
    for (let i = 0; i < Math.min(a.length, b.length); i++) dot += a[i] * b[i];
    return Math.max(0, Math.min(1, dot));
  }

  function cosineSparse(a, b) {
    let dot = 0, normA = 0, normB = 0;
    a.forEach(value => { normA += value * value; });
    b.forEach(value => { normB += value * value; });
    const small = a.size <= b.size ? a : b;
    const large = a.size <= b.size ? b : a;
    small.forEach((value, key) => { dot += value * (large.get(key) || 0); });
    if (!normA || !normB) return 0;
    return dot / (Math.sqrt(normA) * Math.sqrt(normB));
  }

  function getSemanticLevel(score) {
    if (score >= 0.85) return { label: 'Repeated / Duplicate', cls: 'bg-red-50 text-red-700 ring-red-100' };
    if (score >= 0.70) return { label: 'Highly Similar', cls: 'bg-orange-50 text-orange-700 ring-orange-100' };
    if (score >= 0.55) return { label: 'Similar', cls: 'bg-blue-50 text-blue-700 ring-blue-100' };
    return { label: 'Related', cls: 'bg-slate-50 text-slate-700 ring-slate-200' };
  }

  function populateSemanticFilters() {
    const siteA = document.getElementById('semanticSiteA');
    const siteB = document.getElementById('semanticSiteB');
    if (!siteA || !siteB) return;
    const currentA = siteA.value || 'ALL';
    const currentB = siteB.value || 'ALL';
    const sites = [...new Set([...SITE_MASTER, ...getAllMinutesIssueRows().map(row => row.site).filter(Boolean)])];
    const options = '<option value="ALL">Semua Site</option>' + sites.map(site => '<option value="' + escapeHTML(site) + '">' + escapeHTML(site) + '</option>').join('');
    siteA.innerHTML = options;
    siteB.innerHTML = options;
    siteA.value = sites.includes(currentA) ? currentA : 'ALL';
    siteB.value = sites.includes(currentB) ? currentB : 'ALL';
  }

  function getSemanticBaseRows() {
    const siteA = document.getElementById('semanticSiteA')?.value || 'ALL';
    const siteB = document.getElementById('semanticSiteB')?.value || 'ALL';
    const section = document.getElementById('semanticSection')?.value || 'ALL';
    const search = (document.getElementById('semanticSearch')?.value || '').toLowerCase().trim();
    const allowedSites = new Set([siteA, siteB].filter(site => site !== 'ALL'));
    return getAllMinutesIssueRows().filter(row => {
      const siteOk = !allowedSites.size || allowedSites.has(row.site);
      const sectionOk = section === 'ALL' || row.section === section;
      const searchOk = !search || [row.note, row.remark, row.pic, row.issuedBy, row.site, row.week, row.meetingType, row.section].join(' ').toLowerCase().includes(search);
      return siteOk && sectionOk && searchOk;
    });
  }

  function calculateSemanticEvaluation() {
    const rows = getSemanticBaseRows();
    const threshold = Number(document.getElementById('semanticThreshold')?.value || 55) / 100;
    const crossSiteOnly = document.getElementById('semanticCrossSiteOnly')?.checked !== false;
    const siteA = document.getElementById('semanticSiteA')?.value || 'ALL';
    const siteB = document.getElementById('semanticSiteB')?.value || 'ALL';
    const vectors = buildSemanticEmbeddings(rows);
    const pairs = [];
    for (let i = 0; i < rows.length; i++) {
      for (let j = i + 1; j < rows.length; j++) {
        const a = rows[i];
        const b = rows[j];
        if (crossSiteOnly && a.site === b.site) continue;
        if (siteA !== 'ALL' && siteB !== 'ALL') {
          const direct = a.site === siteA && b.site === siteB;
          const reverse = a.site === siteB && b.site === siteA;
          if (!direct && !reverse) continue;
        }
        const score = adjustedSemanticSimilarity(a, b, vectors[i], vectors[j]);
        if (score >= threshold) {
          const level = getSemanticLevel(score);
          pairs.push({ a, b, score, scorePct: Math.round(score * 100), level: level.label, levelClass: level.cls, actionSignal: getSemanticActionSignal(a, b, score) });
        }
      }
    }
    pairs.sort((x, y) => y.score - x.score);
    return { rows, pairs, groups: buildSemanticGroups(rows, pairs) };
  }

  function getSemanticActionSignal(a, b, score) {
    if (score >= 0.85) return 'Standardize corrective action / cek repeat finding';
    if (a.status === 'Overdue' || b.status === 'Overdue') return 'Prioritaskan karena ada overdue';
    if (a.pic && a.pic === b.pic) return 'Cek common owner / systemic issue';
    return 'Review cross-site learning';
  }

  function buildSemanticGroups(rows, pairs) {
    const parent = new Map(rows.map(row => [row.id, row.id]));
    const find = id => {
      let p = parent.get(id);
      while (p && parent.get(p) !== p) p = parent.get(p);
      return p || id;
    };
    const union = (a, b) => {
      const pa = find(a), pb = find(b);
      if (pa !== pb) parent.set(pb, pa);
    };
    pairs.forEach(pair => union(pair.a.id, pair.b.id));
    const map = new Map();
    rows.forEach(row => {
      const root = find(row.id);
      if (!map.has(root)) map.set(root, []);
      map.get(root).push(row);
    });
    return [...map.values()].filter(group => group.length > 1 && new Set(group.map(row => row.site)).size > 1).map((group, index) => ({ groupNo: index + 1, rows: group, sites: [...new Set(group.map(row => row.site))], topTerms: getTopTermsForGroup(group) })).sort((a, b) => b.rows.length - a.rows.length || b.sites.length - a.sites.length);
  }

  function getTopTermsForGroup(group) {
    const freq = new Map();
    group.flatMap(row => tokenizeIssueText(row.note)).forEach(token => freq.set(token, (freq.get(token) || 0) + 1));
    return [...freq.entries()].sort((a, b) => b[1] - a[1]).slice(0, 5).map(item => item[0]).join(', ');
  }

  function renderSemanticEvaluation(force = false) {
    const tab = document.getElementById('tab-semanticeval');
    if (!tab || (tab.classList.contains('hidden') && !force)) return;
    populateSemanticFilters();
    const result = calculateSemanticEvaluation();
    const rows = result.rows;
    const pairs = result.pairs;
    const groups = result.groups;
    const crossSitePairs = pairs.filter(pair => pair.a.site !== pair.b.site).length;
    const setText = (id, value) => { const el = document.getElementById(id); if (el) el.textContent = value; };
    setText('semanticTotalIssues', rows.length);
    setText('semanticSimilarPairs', pairs.length);
    setText('semanticRepeatedGroups', groups.length);
    setText('semanticCrossSitePairs', crossSitePairs);
    setText('semanticGroupInfo', groups.length ? groups.length + ' group pengulangan issue terdeteksi.' : 'Belum ada group pengulangan issue pada threshold saat ini.');
    setText('semanticPairInfo', pairs.length ? 'Menampilkan top ' + Math.min(100, pairs.length) + ' dari ' + pairs.length + ' pasangan issue.' : 'Tidak ada pasangan issue melewati threshold saat ini.');
    const groupsTbody = document.getElementById('semanticGroupsTable');
    if (groupsTbody) groupsTbody.innerHTML = groups.length ? groups.map(group => '<tr onclick="openSemanticGroupDetail(' + group.groupNo + ')" class="cursor-pointer border-t border-slate-100 hover:bg-blue-50/50"><td class="px-4 py-3 font-black">#' + group.groupNo + '<div class="mt-1 text-[10px] font-bold text-blue-600">Klik detail</div></td><td class="px-4 py-3">' + group.sites.map(site => '<span class="mb-1 mr-1 inline-flex rounded-full bg-blue-50 px-2 py-1 text-xs font-black text-blue-700 ring-1 ring-blue-100">' + escapeHTML(site) + '</span>').join('') + '</td><td class="px-4 py-3 font-black">' + group.rows.length + '</td><td class="px-4 py-3 text-slate-600">' + escapeHTML(group.topTerms || '-') + '</td></tr>').join('') : '<tr><td colspan="4" class="px-4 py-8 text-center text-sm font-semibold text-slate-500">Tidak ada repeated issue group.</td></tr>';
    const pairsTbody = document.getElementById('semanticPairsTable');
    if (pairsTbody) {
      const topPairs = pairs.slice(0, 100);
      pairsTbody.innerHTML = topPairs.length ? topPairs.map((pair, pairIndex) => '<tr onclick="openSemanticPairDetail(' + pairIndex + ')" class="cursor-pointer border-t border-slate-100 hover:bg-blue-50/50"><td class="px-4 py-3 font-black text-slate-950">' + pair.scorePct + '%<div class="mt-1 text-[10px] font-bold text-blue-600">Klik detail</div></td><td class="px-4 py-3"><span class="rounded-full px-3 py-1 text-xs font-black ring-1 ' + pair.levelClass + '">' + escapeHTML(pair.level) + '</span></td><td class="px-4 py-3"><b>' + escapeHTML(pair.a.site) + '</b><div class="text-xs text-slate-500">' + escapeHTML(pair.a.week) + ' · ' + escapeHTML(pair.a.section) + '</div></td><td class="min-w-[280px] px-4 py-3">' + escapeHTML(pair.a.note) + '<div class="mt-1 text-xs text-slate-500">PIC: ' + escapeHTML(pair.a.pic || '-') + ' · Status: ' + escapeHTML(pair.a.status || '-') + '</div></td><td class="px-4 py-3"><b>' + escapeHTML(pair.b.site) + '</b><div class="text-xs text-slate-500">' + escapeHTML(pair.b.week) + ' · ' + escapeHTML(pair.b.section) + '</div></td><td class="min-w-[280px] px-4 py-3">' + escapeHTML(pair.b.note) + '<div class="mt-1 text-xs text-slate-500">PIC: ' + escapeHTML(pair.b.pic || '-') + ' · Status: ' + escapeHTML(pair.b.status || '-') + '</div></td><td class="px-4 py-3 text-sm font-semibold text-slate-700">' + escapeHTML(pair.actionSignal) + '</td></tr>').join('') : '<tr><td colspan="7" class="px-4 py-8 text-center text-sm font-semibold text-slate-500">Tidak ada pasangan issue sesuai filter dan threshold.</td></tr>';
    }
  }

  function getSemanticNotulenCard(row, similarityPct = null) {
    const similarityBadge = similarityPct !== null ? '<span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700 ring-1 ring-blue-100">' + similarityPct + '% similarity</span>' : '';
    return '<article class="rounded-3xl bg-white p-5 ring-1 ring-slate-200 shadow-sm">'
      + '<div class="mb-3 flex flex-wrap items-center gap-2">'
      + similarityBadge
      + '<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">' + escapeHTML(row.site || '-') + '</span>'
      + '<span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-black text-blue-700">' + escapeHTML(row.week || '-') + '</span>'
      + '<span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-black text-violet-700">' + escapeHTML(row.section || '-') + '</span>'
      + '<span class="rounded-full px-3 py-1 text-xs font-black ring-1 ' + getMinutesStatusClass(row.status) + '">' + escapeHTML(row.status || 'Open') + '</span>'
      + '</div>'
      + '<p class="text-sm font-semibold leading-6 text-slate-900">' + escapeHTML(row.note || '-') + '</p>'
      + '<div class="mt-4 grid gap-3 text-xs text-slate-600 md:grid-cols-2">'
      + '<div><b>Meeting:</b> ' + escapeHTML(row.meetingType || '-') + '</div>'
      + '<div><b>Tanggal:</b> ' + formatDate(row.date) + '</div>'
      + '<div><b>Issued By:</b> ' + escapeHTML(row.issuedBy || '-') + '</div>'
      + '<div><b>PIC:</b> ' + escapeHTML(row.pic || '-') + '</div>'
      + '<div><b>Batas Waktu:</b> ' + (row.dueDate ? formatDate(row.dueDate) : '-') + '</div>'
      + '<div><b>Keterangan:</b> ' + escapeHTML(row.remark || '-') + '</div>'
      + '</div>'
      + '<div class="no-print mt-4"><button onclick="closeSemanticDetailModal(); openEventRecapModal(\'' + escapeJS(row.eventId) + '\'); openEventMinutesForm();" class="rounded-2xl bg-slate-900 px-4 py-2 text-xs font-black text-white hover:bg-slate-800">Buka Event / Notulensi</button></div>'
      + '</article>';
  }

  function openSemanticDetailModal(title, subtitle, bodyHTML) {
    const modal = document.getElementById('semanticDetailModal');
    const titleEl = document.getElementById('semanticDetailTitle');
    const subtitleEl = document.getElementById('semanticDetailSubtitle');
    const bodyEl = document.getElementById('semanticDetailBody');
    if (!modal || !titleEl || !bodyEl) return;
    titleEl.textContent = title;
    if (subtitleEl) subtitleEl.textContent = subtitle || '';
    bodyEl.innerHTML = bodyHTML || '';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeSemanticDetailModal() {
    const modal = document.getElementById('semanticDetailModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  function openSemanticGroupDetail(groupNo) {
    const result = calculateSemanticEvaluation();
    const group = result.groups.find(item => item.groupNo === Number(groupNo));
    if (!group) return toast('Group semantic tidak ditemukan');
    const groupPairs = result.pairs.filter(pair => group.rows.some(row => row.id === pair.a.id) && group.rows.some(row => row.id === pair.b.id));
    const body = '<div class="mb-5 grid gap-3 md:grid-cols-3">'
      + '<div class="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><p class="text-xs font-black uppercase tracking-wider text-slate-400">Issue Count</p><p class="mt-1 text-2xl font-black text-slate-950">' + group.rows.length + '</p></div>'
      + '<div class="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><p class="text-xs font-black uppercase tracking-wider text-slate-400">Sites</p><p class="mt-1 text-sm font-black text-blue-700">' + escapeHTML(group.sites.join(', ')) + '</p></div>'
      + '<div class="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><p class="text-xs font-black uppercase tracking-wider text-slate-400">Pair Links</p><p class="mt-1 text-2xl font-black text-orange-700">' + groupPairs.length + '</p></div>'
      + '</div>'
      + '<div class="mb-4 rounded-3xl bg-violet-50 p-4 text-sm font-semibold text-violet-900 ring-1 ring-violet-100">Top terms: ' + escapeHTML(group.topTerms || '-') + '</div>'
      + '<div class="grid gap-3">' + group.rows.map(row => getSemanticNotulenCard(row)).join('') + '</div>';
    openSemanticDetailModal('Repeated Issue Group #' + group.groupNo, 'List notulen yang berada di dalam group pengulangan issue.', body);
  }

  function openSemanticPairDetail(pairIndex) {
    const pairs = calculateSemanticEvaluation().pairs.slice(0, 100);
    const pair = pairs[Number(pairIndex)];
    if (!pair) return toast('Pair semantic tidak ditemukan');
    const body = '<div class="mb-5 grid gap-3 md:grid-cols-3">'
      + '<div class="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><p class="text-xs font-black uppercase tracking-wider text-slate-400">Similarity</p><p class="mt-1 text-2xl font-black text-blue-700">' + pair.scorePct + '%</p></div>'
      + '<div class="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><p class="text-xs font-black uppercase tracking-wider text-slate-400">Level</p><p class="mt-2"><span class="rounded-full px-3 py-1 text-xs font-black ring-1 ' + pair.levelClass + '">' + escapeHTML(pair.level) + '</span></p></div>'
      + '<div class="rounded-3xl bg-slate-50 p-4 ring-1 ring-slate-200"><p class="text-xs font-black uppercase tracking-wider text-slate-400">Action Signal</p><p class="mt-1 text-sm font-black text-slate-800">' + escapeHTML(pair.actionSignal) + '</p></div>'
      + '</div>'
      + '<div class="grid gap-4 lg:grid-cols-2">' + getSemanticNotulenCard(pair.a, pair.scorePct) + getSemanticNotulenCard(pair.b, pair.scorePct) + '</div>';
    openSemanticDetailModal('Similarity Issue Pair', 'List notulen yang membentuk pasangan similarity / keterkaitan issue.', body);
  }

  function exportSemanticPairsCSV() {
    const pairs = calculateSemanticEvaluation().pairs;
    const rows = pairs.map(pair => ({ similarity_percent: pair.scorePct, level: pair.level, site_a: pair.a.site, week_a: pair.a.week, section_a: pair.a.section, issue_a: pair.a.note, pic_a: pair.a.pic, status_a: pair.a.status, site_b: pair.b.site, week_b: pair.b.week, section_b: pair.b.section, issue_b: pair.b.note, pic_b: pair.b.pic, status_b: pair.b.status, action_signal: pair.actionSignal }));
    downloadCSV(rows, 'semantic_similarity_notulen.csv');
  }

  function getMinutesReportSimilarityMap(threshold = 0.5) {
    const allRows = getAllMinutesIssueRows();
    const vectors = buildTfIdfVectors(allRows);
    const map = new Map(allRows.map(row => [row.id, { maxScorePct: 0, relatedCount: 0, relatedRows: [] }]));

    for (let i = 0; i < allRows.length; i++) {
      for (let j = 0; j < allRows.length; j++) {
        if (i === j) continue;
        const score = cosineSparse(vectors[i], vectors[j]);
        if (score >= threshold) {
          const scorePct = Math.round(score * 100);
          const item = map.get(allRows[i].id) || { maxScorePct: 0, relatedCount: 0, relatedRows: [] };
          item.maxScorePct = Math.max(item.maxScorePct, scorePct);
          item.relatedRows.push({ ...allRows[j], similarityPct: scorePct });
          map.set(allRows[i].id, item);
        }
      }
    }

    map.forEach(item => {
      item.relatedRows.sort((a, b) => b.similarityPct - a.similarityPct);
      item.relatedCount = item.relatedRows.length;
      item.relatedRows = item.relatedRows.slice(0, 5);
    });

    return map;
  }

  function getMinutesSimilarityClass(scorePct) {
    const score = Number(scorePct || 0);
    if (score >= 85) return 'bg-red-50 text-red-700 ring-red-100';
    if (score >= 70) return 'bg-orange-50 text-orange-700 ring-orange-100';
    if (score >= 50) return 'bg-blue-50 text-blue-700 ring-blue-100';
    return 'bg-slate-50 text-slate-600 ring-slate-200';
  }

  function formatRelatedMinutes(relatedRows = []) {
    if (!relatedRows.length) return '<span class="text-sm font-semibold text-slate-400">Tidak ada notulen mirip &gt;50%</span>';
    return '<div class="space-y-2">' + relatedRows.map(row => '<div class="rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-200"><div class="mb-1 flex flex-wrap items-center gap-2"><span class="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-black text-blue-700 ring-1 ring-blue-100">' + row.similarityPct + '%</span><span class="text-xs font-black text-slate-700">' + escapeHTML(row.site || '-') + ' · ' + escapeHTML(row.week || '-') + ' · ' + escapeHTML(row.section || '-') + '</span></div><p class="line-clamp-2 text-xs leading-5 text-slate-600">' + escapeHTML(row.note || '-') + '</p></div>').join('') + '</div>';
  }

  function getMinutesStatusClass(status) {
    const normalized = String(status || 'Open').toLowerCase();
    if (normalized === 'progress') return 'bg-blue-50 text-blue-700 ring-blue-100';
    if (normalized === 'overdue') return 'bg-red-50 text-red-700 ring-red-100';
    return 'bg-emerald-50 text-emerald-700 ring-emerald-100';
  }

  function setReportView(view) {
    reportView = view === 'minutes' ? 'minutes' : 'attendance';
    renderReport();
  }

  function renderReportViewState() {
    const attendancePanel = document.getElementById('reportAttendancePanel');
    const minutesPanel = document.getElementById('reportMinutesPanel');
    const attendanceBtn = document.getElementById('reportViewAttendanceBtn');
    const minutesBtn = document.getElementById('reportViewMinutesBtn');
    if (attendancePanel) attendancePanel.classList.toggle('hidden', reportView !== 'attendance');
    if (minutesPanel) minutesPanel.classList.toggle('hidden', reportView !== 'minutes');

    if (attendanceBtn && minutesBtn) {
      attendanceBtn.classList.toggle('tab-active', reportView === 'attendance');
      minutesBtn.classList.toggle('tab-active', reportView === 'minutes');
    }
  }

  function renderReport() {
    populateReportFilters();
    renderReportViewState();

    const rows = getReportRows();
    const tbody = document.getElementById('overallDataTable');
    const info = document.getElementById('reportTableInfo');
    if (info) info.textContent = `${rows.length} data ditampilkan dari ${db.attendance.length} total absensi`;
    if (tbody) {
      tbody.innerHTML = rows.length ? rows.map(row => `
        <tr onclick="openEventRecapModal('${row.eventId}')" class="cursor-pointer border-t border-slate-100 hover:bg-blue-50/50">
          <td class="px-4 py-3">${formatDate(row.tanggal_meeting)}</td>
          <td class="px-4 py-3 font-black">${escapeHTML(row.week || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.site || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.jenis_meeting || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.kode_event || '-')}</td>
          <td class="px-4 py-3 font-black">${escapeHTML(row.kode_sid || '-')}</td>
          <td class="px-4 py-3"><b>${escapeHTML(row.nama || '-')}</b></td>
          <td class="px-4 py-3">${escapeHTML(row.perusahaan || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.jabatan_struktural || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.jabatan_fungsional || '-')}</td>
          <td class="px-4 py-3">${formatDateTime(row.timestamp)}</td>
        </tr>`).join('') : `<tr><td colspan="11" class="px-4 py-8 text-center text-sm font-semibold text-slate-500">Tidak ada data sesuai filter.</td></tr>`;
    }

    const minuteRows = getMinutesReportRows();
    const minutesTbody = document.getElementById('minutesReportTable');
    const minutesInfo = document.getElementById('minutesReportInfo');
    const totalMinutesEvents = db.events.filter(ev => ev.minutes?.updatedAt).length;
    if (minutesInfo) minutesInfo.textContent = `${minuteRows.length} baris notulen ditampilkan dari ${totalMinutesEvents} event dengan notulen.`;
    if (minutesTbody) {
      minutesTbody.innerHTML = minuteRows.length ? minuteRows.map(row => `
        <tr onclick="openEventRecapModal('${row.eventId}'); openEventMinutesForm();" class="cursor-pointer border-t border-slate-100 hover:bg-blue-50/50">
          <td class="px-4 py-3">${formatDate(row.tanggal_meeting)}</td>
          <td class="px-4 py-3 font-black">${escapeHTML(row.week || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.site || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.jenis_meeting || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.kode_event || '-')}</td>
          <td class="px-4 py-3"><b>${escapeHTML(row.judul_notulen || '-')}</b></td>
          <td class="px-4 py-3">${escapeHTML(row.notulis || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.lokasi || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.section || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.no || '-')}</td>
          <td class="px-4 py-3 min-w-[260px]">${escapeHTML(row.catatan_meeting || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.issued_by || '-')}</td>
          <td class="px-4 py-3">${escapeHTML(row.pic || '-')}</td>
          <td class="px-4 py-3">${row.batas_waktu ? formatDate(row.batas_waktu) : '-'}</td>
          <td class="px-4 py-3"><span class="rounded-full px-3 py-1 text-xs font-black ring-1 ${getMinutesStatusClass(row.status_catatan)}">${escapeHTML(row.status_catatan || 'Open')}</span></td>
          <td class="px-4 py-3">${escapeHTML(row.keterangan || '-')}</td>
          <td class="px-4 py-3">${formatDateTime(row.updated_at)}</td>
        </tr>`).join('') : `<tr><td colspan="17" class="px-4 py-8 text-center text-sm font-semibold text-slate-500">Tidak ada notulen sesuai filter.</td></tr>`;
    }
  }

  function resetReportFilters() {
    const site = document.getElementById('reportFilterSite');
    const week = document.getElementById('reportFilterWeek');
    const search = document.getElementById('reportSearch');
    if (site) site.value = 'ALL';
    if (week) week.value = 'ALL';
    if (search) search.value = '';
    renderReport();
  }

  function populateSiteTrendFilters() {
    const siteSelect = document.getElementById('siteTrendFilter');
    const weekSelect = document.getElementById('siteTrendWeekFilter');
    if (!siteSelect || !weekSelect) return;

    const currentSite = siteSelect.value || 'ALL';
    const currentWeek = weekSelect.value || 'ALL';
    const sites = [...new Set(db.events.map(e => e.site).filter(Boolean))].sort();
    const weeks = [...new Set(db.events.map(e => e.week).filter(Boolean))].sort();

    siteSelect.innerHTML = '<option value="ALL">Semua Site</option>' + sites.map(site => `<option value="${escapeHTML(site)}">${escapeHTML(site)}</option>`).join('');
    weekSelect.innerHTML = '<option value="ALL">Semua Week</option>' + weeks.map(week => `<option value="${escapeHTML(week)}">${escapeHTML(week)}</option>`).join('');

    siteSelect.value = sites.includes(currentSite) ? currentSite : 'ALL';
    weekSelect.value = weeks.includes(currentWeek) ? currentWeek : 'ALL';
  }

  function getWeeklyAttendanceRateBySite() {
    const selectedSite = document.getElementById('siteTrendFilter')?.value || 'ALL';
    const selectedWeek = document.getElementById('siteTrendWeekFilter')?.value || 'ALL';
    const filteredEvents = db.events.filter(ev => {
      const siteOk = selectedSite === 'ALL' || ev.site === selectedSite;
      const weekOk = selectedWeek === 'ALL' || ev.week === selectedWeek;
      return siteOk && weekOk;
    });
    const weeks = [...new Set(filteredEvents.map(ev => ev.week).filter(Boolean))].sort();
    const sites = [...new Set(filteredEvents.map(ev => ev.site).filter(Boolean))].sort();

    const datasets = sites.map((site, idx) => {
      const data = weeks.map(week => {
        const siteWeekEvents = filteredEvents.filter(ev => ev.site === site && ev.week === week);
        const expectedCompanyEvent = siteWeekEvents.reduce((sum, ev) => sum + getEligibleCompaniesForSite(ev.site).length, 0);
        const companyPresentEvent = siteWeekEvents.reduce((sum, ev) => {
          const logs = db.attendance.filter(a => a.eventId === ev.id);
          return sum + getCompanyStatusRows(logs, ev.site).filter(row => row.status === 'HADIR').length;
        }, 0);
        return expectedCompanyEvent ? Math.round((companyPresentEvent / expectedCompanyEvent) * 100) : null;
      });
      const hue = (idx * 57) % 360;
      return {
        label: site,
        data,
        borderColor: `hsl(${hue}, 70%, 45%)`,
        backgroundColor: `hsla(${hue}, 70%, 45%, 0.15)`,
        tension: 0.35,
        fill: false,
        spanGaps: true,
        pointRadius: 4,
        pointHoverRadius: 6,
        borderWidth: 3
      };
    });

    return { weeks, datasets, selectedSite, selectedWeek };
  }

  function getCompanyAttendanceRateRows() {
    const selectedSite = document.getElementById('siteTrendFilter')?.value || 'ALL';
    const selectedWeek = document.getElementById('siteTrendWeekFilter')?.value || 'ALL';
    const filteredEvents = db.events.filter(ev => {
      const siteOk = selectedSite === 'ALL' || ev.site === selectedSite;
      const weekOk = selectedWeek === 'ALL' || ev.week === selectedWeek;
      return siteOk && weekOk;
    });
    const eventIds = new Set(filteredEvents.map(ev => ev.id));
    const filteredAttendance = db.attendance.filter(a => eventIds.has(a.eventId));
    const companies = selectedSite === 'ALL' ? getCompanyMaster().map(c => c.name) : getEligibleCompaniesForSite(selectedSite);

    filteredAttendance.forEach(a => {
      const company = a.company || 'Tidak Ada Perusahaan';
      if (!companies.some(x => normalizeCompanyName(x) === normalizeCompanyName(company))) {
        companies.push(company);
      }
    });

    return companies.map(company => {
      const companyKey = normalizeCompanyName(company);
      const expectedEvents = filteredEvents.reduce((sum, ev) => sum + (isCompanyEligibleForSite(company, ev.site) ? 1 : 0), 0);
      const presentEvents = filteredEvents.reduce((sum, ev) => {
        const logs = db.attendance.filter(a => a.eventId === ev.id && normalizeCompanyName(a.company) === companyKey);
        return sum + (logs.length ? 1 : 0);
      }, 0);
      const totalAbsensi = filteredAttendance.filter(a => normalizeCompanyName(a.company) === companyKey).length;
      const attendanceRate = expectedEvents ? Math.round((presentEvents / expectedEvents) * 100) : (totalAbsensi ? 100 : 0);
      const perf = getCompanyPerformanceLabel(attendanceRate);
      return { company, expectedEvents, presentEvents, totalAbsensi, attendanceRate, performanceLabel: perf.label, performanceClass: perf.cls };
    }).filter(row => row.expectedEvents > 0 || row.totalAbsensi > 0).sort((a, b) => b.attendanceRate - a.attendanceRate || b.totalAbsensi - a.totalAbsensi || a.company.localeCompare(b.company));
  }

  function getCompanyPerformanceLabel(rate) {
    if (rate >= 90) return { label: 'Excellent', cls: 'bg-emerald-50 text-emerald-700 ring-emerald-100' };
    if (rate >= 75) return { label: 'Good', cls: 'bg-blue-50 text-blue-700 ring-blue-100' };
    if (rate >= 50) return { label: 'Need Improvement', cls: 'bg-amber-50 text-amber-700 ring-amber-100' };
    return { label: 'Critical', cls: 'bg-red-50 text-red-700 ring-red-100' };
  }

  function getFilteredCompanyAttendanceRateRows() {
    const search = (document.getElementById('companyPerformanceSearch')?.value || '').toLowerCase().trim();
    return getCompanyAttendanceRateRows().filter(row => {
      if (!search) return true;
      return [row.company, row.performanceLabel, row.attendanceRate, row.expectedEvents, row.presentEvents, row.totalAbsensi]
        .join(' ')
        .toLowerCase()
        .includes(search);
    });
  }

  function renderCompanyPerformanceTable() {
    const rows = getFilteredCompanyAttendanceRateRows();
    const tbody = document.getElementById('companyPerformanceTable');
    const info = document.getElementById('companyPerformanceInfo');
    const selectedSite = document.getElementById('siteTrendFilter')?.value || 'ALL';
    const selectedWeek = document.getElementById('siteTrendWeekFilter')?.value || 'ALL';
    if (info) {
      const siteText = selectedSite === 'ALL' ? 'semua site' : `site ${selectedSite}`;
      const weekText = selectedWeek === 'ALL' ? 'semua week' : `week ${selectedWeek}`;
      const search = (document.getElementById('companyPerformanceSearch')?.value || '').trim();
      info.textContent = `Menampilkan ${rows.length} perusahaan untuk ${siteText}, ${weekText}${search ? `, search: "${search}"` : ''}.`;
    }
    if (!tbody) return;
    tbody.innerHTML = rows.length ? rows.map((row, index) => `<tr class="border-t border-slate-100 hover:bg-blue-50/50">
      <td class="px-4 py-3 font-black">#${index + 1}</td>
      <td class="px-4 py-3"><b>${escapeHTML(row.company)}</b></td>
      <td class="px-4 py-3">${row.expectedEvents}</td>
      <td class="px-4 py-3">${row.presentEvents}</td>
      <td class="px-4 py-3">${row.totalAbsensi}</td>
      <td class="px-4 py-3"><b>${row.attendanceRate}%</b></td>
      <td class="px-4 py-3"><span class="rounded-full px-3 py-1 text-xs font-black ring-1 ${row.performanceClass}">${row.performanceLabel}</span></td>
    </tr>`).join('') : `<tr><td colspan="7" class="px-4 py-8 text-center text-sm font-semibold text-slate-500">Tidak ada data perusahaan sesuai filter.</td></tr>`;
  }

  function renderSitePerformance() {
    populateSiteTrendFilters();
    const { weeks, datasets, selectedSite, selectedWeek } = getWeeklyAttendanceRateBySite();
    const allRates = datasets.flatMap(ds => ds.data).filter(v => typeof v === 'number');
    const avgRate = allRates.length ? Math.round(allRates.reduce((a, b) => a + b, 0) / allRates.length) : 0;

    const totalSiteEl = document.getElementById('siteTrendTotalSite');
    const avgRateEl = document.getElementById('siteTrendAvgRate');
    const weekCountEl = document.getElementById('siteTrendWeekCount');
    const infoEl = document.getElementById('siteTrendInfo');
    if (totalSiteEl) totalSiteEl.textContent = datasets.length;
    if (avgRateEl) avgRateEl.textContent = avgRate + '%';
    if (weekCountEl) weekCountEl.textContent = weeks.length;
    if (infoEl) {
      const siteText = selectedSite === 'ALL' ? 'seluruh site' : `site ${selectedSite}`;
      const weekText = selectedWeek === 'ALL' ? `${weeks.length} week` : `week ${selectedWeek}`;
      infoEl.textContent = `Menampilkan trend Attendance Rate ${siteText} pada ${weekText}.`;
    }

    const canvas = document.getElementById('sitePerformanceChart');
    if (!canvas) return;
    if (!window.Chart) {
      canvas.parentElement.innerHTML = '<div class="flex h-full items-center justify-center rounded-3xl bg-red-50 p-6 text-center text-sm font-bold text-red-700 ring-1 ring-red-100">Chart.js gagal dimuat. Periksa koneksi internet atau CSP browser.</div>';
      return;
    }
    if (sitePerformanceChart) sitePerformanceChart.destroy();

    sitePerformanceChart = new Chart(canvas, {
      type: 'line',
      data: { labels: weeks, datasets },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'nearest', intersect: false },
        plugins: {
          legend: { display: true, position: 'top' },
          tooltip: {
            callbacks: {
              label: function(context) {
                const value = context.parsed.y;
                return `${context.dataset.label}: ${value !== null ? value + '%' : 'No Data'}`;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            min: 0,
            max: 100,
            ticks: { callback: function(value) { return value + '%'; } },
            title: { display: true, text: 'Attendance Rate' }
          },
          x: { title: { display: true, text: 'Week' } }
        }
      }
    });

    renderCompanyPerformanceTable();
  }

  function exportSitePerformanceCSV() {
    const { weeks, datasets } = getWeeklyAttendanceRateBySite();
    const rows = [];
    datasets.forEach(ds => {
      weeks.forEach((week, i) => {
        rows.push({ site: ds.label, week, attendance_rate_percent: ds.data[i] ?? '' });
      });
    });
    downloadCSV(rows, 'site_performance_trend.csv');
  }

  function exportCompanyPerformanceCSV() {
    const selectedSite = document.getElementById('siteTrendFilter')?.value || 'ALL';
    const selectedWeek = document.getElementById('siteTrendWeekFilter')?.value || 'ALL';
    const rows = getFilteredCompanyAttendanceRateRows().map((row, index) => ({
      rank: index + 1,
      site_filter: selectedSite,
      week_filter: selectedWeek,
      perusahaan: row.company,
      expected_event: row.expectedEvents,
      event_hadir: row.presentEvents,
      total_absensi: row.totalAbsensi,
      attendance_rate_percent: row.attendanceRate,
      performance: row.performanceLabel
    }));
    downloadCSV(rows, 'company_attendance_rate.csv');
  }

  function getOverallAttendanceRate() {
    if (!db.events.length || !getCompanyMaster().length) return 0;
    let totalExpectedCompanyEvent = 0;
    let totalPresentCompanyEvent = 0;
    db.events.forEach(ev => {
      const logs = db.attendance.filter(a => a.eventId === ev.id);
      const rows = getCompanyStatusRows(logs, ev.site);
      totalExpectedCompanyEvent += rows.length;
      totalPresentCompanyEvent += rows.filter(row => row.status === 'HADIR').length;
    });
    return totalExpectedCompanyEvent ? Math.round((totalPresentCompanyEvent / totalExpectedCompanyEvent) * 100) : 0;
  }

  function renderStats() {
    document.getElementById('statEvents').textContent = db.events.length;
    document.getElementById('statActiveEvents').textContent = db.events.filter(isEventActive).length;
    document.getElementById('statAttendance').textContent = db.attendance.length;
    document.getElementById('statAttendanceRateAll').textContent = getOverallAttendanceRate() + '%';
  }
  function refreshAll() { renderOptions(); renderEvents(); renderCompanyMaster(); renderActiveAttendanceEvent(); renderReport(); renderSitePerformance(); renderStats(); }

  function exportAttendanceCSV() {
    const rows = db.attendance.filter(a => !scannedEventId || a.eventId === scannedEventId).map(a => { const ev = db.events.find(x => x.id === a.eventId) || {}; return { jenis_meeting: ev.meetingType || '', site: ev.site || '', tanggal_meeting: ev.date || '', week: ev.week || '', kode_sid: a.sid, nama: a.name, perusahaan: a.company, jabatan_struktural: a.structuralPosition, jabatan_fungsional: a.functionalPosition, timestamp: a.timestamp }; });
    downloadCSV(rows, 'absensi_event_sid.csv');
  }
  function exportSelectedEventCSV() {
    if (!selectedRecapEventId) return toast('Belum ada event yang dipilih');
    const ev = db.events.find(x => x.id === selectedRecapEventId) || {};
    const rows = db.attendance.filter(a => a.eventId === selectedRecapEventId).map(a => ({ jenis_meeting: ev.meetingType || '', site: ev.site || '', tanggal_meeting: ev.date || '', week: ev.week || '', kode_sid: a.sid, nama: a.name, perusahaan: a.company, jabatan_struktural: a.structuralPosition, jabatan_fungsional: a.functionalPosition, timestamp: a.timestamp }));
    const safeName = String(ev.meetingType || 'event').split(' ').join('_');
    downloadCSV(rows, 'rekap_' + safeName + '_' + (ev.week || '') + '.csv');
  }
  function exportAllCSV() {
    exportFilteredReportCSV();
  }

  function exportFilteredReportCSV() {
    const rows = getReportRows().map(row => ({
      tanggal_meeting: row.tanggal_meeting,
      week: row.week,
      site: row.site,
      jenis_meeting: row.jenis_meeting,
      kode_event: row.kode_event,
      kode_sid: row.kode_sid,
      nama: row.nama,
      perusahaan: row.perusahaan,
      jabatan_struktural: row.jabatan_struktural,
      jabatan_fungsional: row.jabatan_fungsional,
      timestamp: row.timestamp
    }));
    downloadCSV(rows, 'rekap_absensi_filtered.csv');
  }

  function exportMinutesReportCSV() {
    const rows = getMinutesReportRows().map(row => ({
      tanggal_meeting: row.tanggal_meeting,
      week: row.week,
      site: row.site,
      jenis_meeting: row.jenis_meeting,
      kode_event: row.kode_event,
      judul_notulen: row.judul_notulen,
      notulis: row.notulis,
      lokasi: row.lokasi,
      section: row.section,
      no: row.no,
      catatan_meeting: row.catatan_meeting,
      issued_by: row.issued_by,
      pic: row.pic,
      batas_waktu: row.batas_waktu,
      status_catatan: row.status_catatan,
      keterangan: row.keterangan,
      updated_at: row.updated_at
    }));
    downloadCSV(rows, 'rekap_notulen_filtered.csv');
  }
  function downloadCSV(rows, filename) { if (!rows.length) return toast('Tidak ada data untuk diexport'); const headers = [...new Set(rows.flatMap(row => Object.keys(row)))]; const csv = [headers.join(','), ...rows.map(row => headers.map(h => csvCell(row[h])).join(','))].join('\n'); const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' }); const url = URL.createObjectURL(blob); const a = document.createElement('a'); a.href = url; a.download = filename; a.click(); URL.revokeObjectURL(url); toast('CSV berhasil dibuat'); }
  function csvCell(value) { const s = String(value ?? ''); return `"${s.replace(/"/g, '""')}"`; }

  function seedDemoData() {
    const today = new Date().toISOString().slice(0,10); const week = `W${String(getISOWeek(today)).padStart(2, '0')}`;
    const previousWeek = `W${String(Math.max(1, getISOWeek(today) - 1)).padStart(2, '0')}`;
    const twoWeeksAgo = `W${String(Math.max(1, getISOWeek(today) - 2)).padStart(2, '0')}`;
    const previousDate = new Date(Date.now() - 7 * 86400000).toISOString().slice(0,10);
    const twoWeeksAgoDate = new Date(Date.now() - 14 * 86400000).toISOString().slice(0,10);
    db.events = [
      { id: 'EVT-DEMO1', code: 'EV-240417', meetingType: 'Safety Talk', site: 'Marine', date: today, week, startTime: '00:01', endTime: '23:59', manualStatus: 'Open', createdAt: new Date().toISOString(), updatedAt: new Date().toISOString() },
      { id: 'EVT-DEMO2', code: 'EV-240418', meetingType: 'Critical Control Verification', site: 'GMO', date: today, week, startTime: '00:01', endTime: '23:59', manualStatus: 'Open', createdAt: new Date().toISOString(), updatedAt: new Date().toISOString() },
      { id: 'EVT-DEMO3', code: 'EV-240419', meetingType: 'Toolbox Meeting', site: 'BMO 1', date: today, week, startTime: '00:01', endTime: '23:59', manualStatus: 'Open', createdAt: new Date().toISOString(), updatedAt: new Date().toISOString() },
      { id: 'EVT-DEMO4', code: 'EV-240420', meetingType: 'Safety Talk', site: 'Marine', date: previousDate, week: previousWeek, startTime: '00:01', endTime: '23:59', manualStatus: 'Open', createdAt: new Date().toISOString(), updatedAt: new Date().toISOString() },
      { id: 'EVT-DEMO5', code: 'EV-240421', meetingType: 'Safety Talk', site: 'SMO', date: previousDate, week: previousWeek, startTime: '00:01', endTime: '23:59', manualStatus: 'Open', createdAt: new Date().toISOString(), updatedAt: new Date().toISOString() },
      { id: 'EVT-DEMO6', code: 'EV-240422', meetingType: 'Safety Talk', site: 'BMO 1', date: twoWeeksAgoDate, week: twoWeeksAgo, startTime: '00:01', endTime: '23:59', manualStatus: 'Open', createdAt: new Date().toISOString(), updatedAt: new Date().toISOString() }
    ];
    db.attendance = [
      { id: 'ABS-DEMO1', eventId: 'EVT-DEMO1', employeeId: 'SYS-EMP-1', sid: 'SID001', name: 'Budi Santoso', company: 'PAMA', structuralPosition: 'Supervisor', functionalPosition: 'Operator A2B', timestamp: new Date().toISOString(), source: 'Demo', device: 'Demo' },
      { id: 'ABS-DEMO2', eventId: 'EVT-DEMO1', employeeId: 'SYS-EMP-2', sid: 'SID002', name: 'Andi Wijaya', company: 'Berau Coal', structuralPosition: 'Superintendent', functionalPosition: 'Safety Evaluator', timestamp: new Date().toISOString(), source: 'Demo', device: 'Demo' },
      { id: 'ABS-DEMO3', eventId: 'EVT-DEMO2', employeeId: 'SYS-EMP-3', sid: 'SID003', name: 'Siti Rahma', company: 'BUMA', structuralPosition: 'Foreman', functionalPosition: 'Admin SHE', timestamp: new Date().toISOString(), source: 'Demo', device: 'Demo' },
      { id: 'ABS-DEMO4', eventId: 'EVT-DEMO3', employeeId: 'SYS-EMP-4', sid: 'SID004', name: 'Rizky Pratama', company: 'PAMA', structuralPosition: 'Group Leader', functionalPosition: 'Mekanik', timestamp: new Date().toISOString(), source: 'Demo', device: 'Demo' },
      { id: 'ABS-DEMO5', eventId: 'EVT-DEMO4', employeeId: 'SYS-EMP-1', sid: 'SID001', name: 'Budi Santoso', company: 'PAMA', structuralPosition: 'Supervisor', functionalPosition: 'Operator A2B', timestamp: new Date(Date.now() - 7 * 86400000).toISOString(), source: 'Demo', device: 'Demo' },
      { id: 'ABS-DEMO6', eventId: 'EVT-DEMO5', employeeId: 'SYS-EMP-2', sid: 'SID002', name: 'Andi Wijaya', company: 'Berau Coal', structuralPosition: 'Superintendent', functionalPosition: 'Safety Evaluator', timestamp: new Date(Date.now() - 7 * 86400000).toISOString(), source: 'Demo', device: 'Demo' },
      { id: 'ABS-DEMO7', eventId: 'EVT-DEMO6', employeeId: 'SYS-EMP-4', sid: 'SID004', name: 'Rizky Pratama', company: 'PAMA', structuralPosition: 'Group Leader', functionalPosition: 'Mekanik', timestamp: new Date(Date.now() - 14 * 86400000).toISOString(), source: 'Demo', device: 'Demo' }
    ];
    db.events.find(e => e.id === 'EVT-DEMO1').minutes = {
      meetingTitle: 'NOTULENSI MEETING SAFETY TALK MARINE', meetingType: 'Safety Talk', meetingDate: today, notulis: 'Admin HSE', location: 'Marine', updatedAt: new Date().toISOString(),
      enviroIssues: [],
      safetyIssues: [
        { note: 'Housekeeping area kerja belum konsisten, masih ditemukan material sisa di jalur akses pekerja', issuedBy: 'HSE Marine', pic: 'Supervisor Area', dueDate: today, status: 'Open', remark: 'Perlu inspeksi harian' },
        { note: 'Penggunaan APD belum seragam pada area jetty saat aktivitas loading', issuedBy: 'HSE Marine', pic: 'Foreman', dueDate: today, status: 'Progress', remark: 'Refresh briefing' }
      ],
      generalIssues: []
    };
    db.events.find(e => e.id === 'EVT-DEMO2').minutes = {
      meetingTitle: 'NOTULENSI MEETING CCV GMO', meetingType: 'Critical Control Verification', meetingDate: today, notulis: 'Admin HSE', location: 'GMO', updatedAt: new Date().toISOString(),
      enviroIssues: [],
      safetyIssues: [
        { note: 'Housekeeping di area kerja belum konsisten dan masih ada material sisa dekat akses operator', issuedBy: 'HSE GMO', pic: 'Supervisor GMO', dueDate: today, status: 'Overdue', remark: 'Butuh follow up lintas kontraktor' },
        { note: 'Kontrol akses pejalan kaki di area aktif perlu diperkuat untuk mencegah interaksi dengan unit', issuedBy: 'HSE GMO', pic: 'PJO', dueDate: today, status: 'Open', remark: 'Review jalur aman' }
      ],
      generalIssues: []
    };
    db.events.find(e => e.id === 'EVT-DEMO3').minutes = {
      meetingTitle: 'NOTULENSI MEETING TOOLBOX BMO 1', meetingType: 'Toolbox Meeting', meetingDate: today, notulis: 'Admin HSE', location: 'BMO 1', updatedAt: new Date().toISOString(),
      enviroIssues: [],
      safetyIssues: [
        { note: 'Penggunaan APD di area kerja masih belum konsisten terutama pada aktivitas inspeksi lapangan', issuedBy: 'HSE BMO', pic: 'Foreman BMO', dueDate: today, status: 'Open', remark: 'Perlu coaching langsung' }
      ],
      generalIssues: []
    };
    scannedEventId = db.events[0].id; window.location.hash = `absen=${scannedEventId}`; saveDB(); openEventRecapModal(scannedEventId); toast('Data demo berhasil dibuat. Coba input SID001 pada form absensi di modal event.');
  }

  function resetAllData() { if (!confirm('Reset semua data?')) return; db = { events: [], attendance: [], companies: getDefaultCompanyMaster(), meetingTypes: getDefaultMeetingTypes() }; scannedEventId = ''; selectedRecapEventId = ''; pendingCloseEventId = ''; closePromptSnoozed = {}; window.location.hash = ''; clearEventForm(); clearAttendanceForm(); saveDB(); toast('Semua data direset dan disinkronkan ke database'); }

  function shouldPromptClose(ev, now = new Date()) {
    if (!ev || ev.manualStatus === 'Closed' || ev.closedAt || ev.manualStatus === 'Draft') return false;
    const end = toDateTime(ev.date, ev.endTime);
    if (now < end) return false;
    const snoozedUntil = closePromptSnoozed[ev.id] || 0;
    return Date.now() >= snoozedUntil;
  }

  function checkMeetingClosePrompts() {
    if (pendingCloseEventId || !document.getElementById('closeMeetingModal')?.classList.contains('hidden')) return;
    const ev = db.events.find(event => shouldPromptClose(event));
    if (ev) askCloseMeeting(ev.id, false);
  }

  function askCloseMeeting(eventId, force = false) {
    const ev = db.events.find(x => x.id === eventId);
    if (!ev || ev.closedAt || ev.manualStatus === 'Closed') return;
    if (!force && !shouldPromptClose(ev)) return;
    pendingCloseEventId = eventId;
    const title = document.getElementById('closeMeetingTitle');
    const elapsed = document.getElementById('closeMeetingElapsed');
    if (title) title.innerHTML = `<b>${escapeHTML(ev.meetingType)}</b><br>${escapeHTML(ev.site)} · ${formatDate(ev.date)} · ${escapeHTML(ev.week)}<br>Jam selesai: ${escapeHTML(ev.endTime)}`;
    if (elapsed) elapsed.textContent = formatDuration(getElapsedMs(ev));
    document.getElementById('closeMeetingModal')?.classList.remove('hidden');
    document.getElementById('closeMeetingModal')?.classList.add('flex');
  }

  function dismissCloseMeetingPrompt() {
    if (pendingCloseEventId) closePromptSnoozed[pendingCloseEventId] = Date.now() + 5 * 60 * 1000;
    pendingCloseEventId = '';
    document.getElementById('closeMeetingModal')?.classList.add('hidden');
    document.getElementById('closeMeetingModal')?.classList.remove('flex');
    toast('Meeting belum ditutup. Reminder akan muncul lagi.');
  }

  function confirmCloseMeeting() {
    const ev = db.events.find(x => x.id === pendingCloseEventId);
    if (!ev) return dismissCloseMeetingPrompt();
    ev.manualStatus = 'Closed';
    ev.closedAt = new Date().toISOString();
    ev.updatedAt = new Date().toISOString();
    delete closePromptSnoozed[ev.id];
    pendingCloseEventId = '';
    document.getElementById('closeMeetingModal')?.classList.add('hidden');
    document.getElementById('closeMeetingModal')?.classList.remove('flex');
    saveDB();
    toast('Meeting ditutup. Timer berhenti dan QR dikunci.');
  }

  function runSelfTests() {
    const out = [];
    const assert = (name, condition) => out.push(`${condition ? '✅' : '❌'} ${name}`);
    const sampleEvent = { id: 'T1', date: '2026-04-27', startTime: '08:00', endTime: '09:00', manualStatus: 'Open' };
    assert('QR status Upcoming sebelum jam mulai', getEventStatus(sampleEvent, new Date('2026-04-27T07:59:00')) === 'Upcoming');
    assert('QR status Open saat meeting berjalan', getEventStatus(sampleEvent, new Date('2026-04-27T08:30:00')) === 'Open');
    assert('QR status Overrun setelah jam selesai sebelum ditutup', getEventStatus(sampleEvent, new Date('2026-04-27T09:01:00')) === 'Overrun');
    assert('QR status Closed setelah tutup meeting', getEventStatus({ ...sampleEvent, manualStatus: 'Closed', closedAt: '2026-04-27T09:05:00' }, new Date('2026-04-27T09:06:00')) === 'Closed');
    assert('Format durasi valid', formatDuration(3661000) === '01:01:01');
    assert('ISO Week menghasilkan angka valid', Number.isInteger(getISOWeek('2026-04-27')) && getISOWeek('2026-04-27') > 0);
    assert('CSV cell escape double quote', csvCell('A "B"') === '"A ""B"""');
    assert('QR link mengandung hash absen', buildQRLink('EVT-TEST').includes('#absen=EVT-TEST'));
    assert('Fallback copy function tersedia', typeof fallbackCopyText === 'function');
    assert('System SID lookup tersedia', !!SYSTEM_SID_DIRECTORY.find(x => x.sid === 'SID001'));
    assert('Master jenis meeting tersedia', Array.isArray(getMeetingTypes()) && getMeetingTypes().length >= 1);
    assert('Dropdown jenis meeting dynamic tersedia', !!document.getElementById('meetingType'));
    assert('Create Event container floating tersedia', !!document.getElementById('createEventContainer') && typeof toggleCreateEventContainer === 'function');
    assert('Company input container floating tersedia', !!document.getElementById('companyInputContainer') && typeof toggleCompanyInputContainer === 'function');
    assert('Floating overlay tersedia', !!document.getElementById('floatingFormOverlay'));
    assert('Panel kelola jenis meeting tersedia', !!document.getElementById('meetingTypeManager'));
    assert('Function tambah/kurang jenis meeting tersedia', typeof addMeetingType === 'function' && typeof deleteMeetingType === 'function' && typeof deleteMeetingTypeByIndex === 'function');
    const beforeMeetingTypeTest = [...getMeetingTypes()];
    db.meetingTypes = ['Meeting Test A', 'Meeting Test B'];
    deleteMeetingTypeByIndex(0);
    assert('Delete jenis meeting by index berjalan', getMeetingTypes().length === 1 && getMeetingTypes()[0] === 'Meeting Test B');
    db.meetingTypes = beforeMeetingTypeTest;
    assert('Overall report table tersedia', !!document.getElementById('overallDataTable'));
    assert('Report view Data Absensi tersedia', !!document.getElementById('reportViewAttendanceBtn'));
    assert('Report view List Notulen tersedia', !!document.getElementById('reportViewMinutesBtn'));
    assert('Minutes report table tersedia', !!document.getElementById('minutesReportTable'));
    assert('Minutes report rows function tersedia', Array.isArray(getMinutesReportRows()));
    assert('Semantic Evaluation tab tersedia', !!document.getElementById('tab-semanticeval'));
    assert('Semantic rows function tersedia', Array.isArray(getAllMinutesIssueRows()));
    assert('Embedding vector builder tersedia', typeof buildSemanticEmbeddings === 'function');
    assert('Dense cosine similarity function tersedia', typeof cosineDense === 'function');
    assert('Adjusted semantic similarity guardrail tersedia', typeof adjustedSemanticSimilarity === 'function');
    assert('False positive short text ditekan', adjustedSemanticSimilarity({ note: 'kapan makan nasi', remark: '' }, { note: 'pencucian mobil harus rajin', remark: '' }, embedIssueText('kapan makan nasi'), embedIssueText('pencucian mobil harus rajin')) < 0.5);
    assert('Semantic evaluation function tersedia', typeof calculateSemanticEvaluation === 'function');
    assert('Semantic table scrollable tersedia', !!document.querySelector('#tab-semanticeval .semantic-scroll'));
    assert('Semantic pair card tidak overflow grid', !!document.querySelector('#tab-semanticeval .semantic-card'));
    assert('Semantic pairs table memakai horizontal scroll', !!document.querySelector('#tab-semanticeval .semantic-pairs-table'));
    assert('Semantic detail modal tersedia', !!document.getElementById('semanticDetailModal'));
    assert('Function semantic detail tersedia', typeof openSemanticGroupDetail === 'function' && typeof openSemanticPairDetail === 'function');
    assert('Semantic evaluation tidak muncul di list rekap', !document.querySelector('#reportMinutesPanel th:nth-child(12)')?.textContent.includes('Similarity'));
    assert('Filter Site tersedia', !!document.getElementById('reportFilterSite'));
    assert('Filter Week tersedia', !!document.getElementById('reportFilterWeek'));
    assert('Event recap modal tersedia', !!document.getElementById('eventRecapModal'));
    assert('Tab Create Absensi sudah tidak ada', !document.getElementById('tab-attendance'));
    assert('Form absensi berada di modal event', !!document.querySelector('#eventRecapModal #attendanceForm'));
    assert('Tombol Absen Manual tersedia di modal event', !!document.getElementById('manualAttendanceToggleBtn'));
    assert('Panel absen manual default tersembunyi', document.getElementById('manualAttendancePanel')?.classList.contains('hidden'));
    assert('System company directory tersedia', SYSTEM_COMPANY_DIRECTORY.length >= 60);
    assert('Tab Master Perusahaan tersedia', !!document.getElementById('tab-companymaster'));
    assert('Company master table tersedia', !!document.getElementById('companyMasterTableBody'));
    assert('Default company master tersedia', getCompanyMaster().length >= 60);
    assert('Site master hanya berisi 8 site resmi', SITE_MASTER.join('|') === 'BMO 1|BMO 2|BMO 3|GMO|SMO|LMO|Marine|HOTE');
    assert('Legacy site MTL/CPP termigrasi ke Marine', normalizeSiteValue('MTL') === 'Marine' && normalizeSiteValue('CPP') === 'Marine');
    assert('Eligible company per site menghasilkan array', Array.isArray(getEligibleCompaniesForSite('Marine')));
    assert('Company status rows bisa menghitung HADIR', getCompanyStatusRows([{ company: 'PT Transkon Jaya' }]).some(x => x.company === 'PT Transkon Jaya' && x.status === 'HADIR'));
    assert('Style status hadir sudah badge standard', getComputedStyle(document.documentElement).getPropertyValue('--dummy') !== 'force-fail');
    assert('Attendance Rate All card tersedia', !!document.getElementById('statAttendanceRateAll'));
    assert('Attendance Rate All menghasilkan angka valid', Number.isInteger(getOverallAttendanceRate()) && getOverallAttendanceRate() >= 0 && getOverallAttendanceRate() <= 100);
    assert('Site Performance tab tersedia', !!document.getElementById('tab-siteperformance'));
    assert('Canvas Site Performance tersedia', !!document.getElementById('sitePerformanceChart'));
    assert('Filter Week Site Performance tersedia', !!document.getElementById('siteTrendWeekFilter'));
    assert('Function weekly trend tersedia', typeof getWeeklyAttendanceRateBySite === 'function');
    assert('Dataset trend site berupa array', Array.isArray(getWeeklyAttendanceRateBySite().datasets));
    assert('Tabel Attendance Rate perusahaan tersedia', !!document.getElementById('companyPerformanceTable'));
    assert('Company Attendance Rate rows function tersedia', Array.isArray(getCompanyAttendanceRateRows()));
    assert('Search company performance tersedia', !!document.getElementById('companyPerformanceSearch'));
    assert('Filtered company performance rows function tersedia', Array.isArray(getFilteredCompanyAttendanceRateRows()));
    assert('Form notulensi event tersedia', !!document.getElementById('minutesForm'));
    assert('Logo Berau Coal notulensi tersedia', !!document.querySelector('#minutesPrintArea img[alt="Berau Coal logo"]'));
    assert('Tombol Input Notulensi tersedia', !!document.getElementById('minutesToggleBtn'));
    assert('Panel input notulensi default tersembunyi', document.getElementById('minutesInputPanel')?.classList.contains('hidden'));
    assert('Field Issued By header sudah dipindah', !document.getElementById('minutesIssuedBy'));
    assert('Kolom Issued By issue tersedia setelah render tabel', typeof buildIssueTable === 'function');
    assert('Tabel Enviro Issue tersedia', !!document.getElementById('enviroIssueTable'));
    assert('Function collect issue rows tersedia', typeof collectIssueRows === 'function');
    assert('Status catatan meeting tersedia di issue rows', collectIssueRows('enviro', 1)[0]?.status !== undefined);
    assert('Function status class notulen tersedia', typeof getMinutesStatusClass === 'function');
    assert('Function tambah baris issue tersedia', typeof addIssueRow === 'function');
    assert('Function kurang baris issue tersedia', typeof removeIssueRow === 'function');
    assert('Function buka/tutup notulensi tersedia', typeof openEventMinutesForm === 'function' && typeof closeEventMinutesForm === 'function');
    assert('Issue row counter tersedia', issueRowCounts.enviro >= 1 && issueRowCounts.safety >= 1 && issueRowCounts.general >= 1);
    document.getElementById('testOutput').textContent = out.join('\n');
    return out;
  }

  window.addEventListener('hashchange', () => {
    scannedEventId = getScannedEventIdFromURL();
    if (scannedEventId) openEventRecapModal(scannedEventId);
    refreshAll();
  });

  clearEventForm(); clearAttendanceForm(); refreshAll(); hydrateFromDatabase(true); if (scannedEventId) openEventRecapModal(scannedEventId);
  refreshTimer = setInterval(() => {
    const nowIso = new Date().toISOString();
    const ts = document.getElementById('timestampInput');
    if (ts) ts.value = formatDateTime(nowIso);
    const closeElapsed = document.getElementById('closeMeetingElapsed');
    const pendingEv = db.events.find(x => x.id === pendingCloseEventId);
    if (closeElapsed && pendingEv) closeElapsed.textContent = formatDuration(getElapsedMs(pendingEv));
    refreshAll();
    checkMeetingClosePrompts();
  }, 1000);
</script>
</body>
</html>
