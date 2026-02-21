<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daily Report · Dashboard IKK–DOPM</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Instrument+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --ink:      #111110;
      --ink-mid:  #555552;
      --ink-soft: #999994;
      --ink-xsoft:#c8c8c2;
      --rule:     #e8e7e3;
      --surface:  #faf9f7;
      --white:    #ffffff;
      --red:      #b83232;
      --red-bg:   #fdf4f4;
      --red-bd:   #f0cece;
      --amber:    #9a6200;
      --amber-bg: #fdf8f0;
      --amber-bd: #ecd9a8;
      --green:    #1e6e48;
      --green-bg: #f2fbf6;
      --green-bd: #a8d8bc;
    }

    * { margin:0; padding:0; box-sizing:border-box; }

    body {
      background: #e9e8e4;
      font-family: 'Instrument Sans', sans-serif;
      font-weight: 300;
      color: var(--ink);
      -webkit-font-smoothing: antialiased;
    }

    /* ─── SHELL ─────────────────────────────── */
    .shell {
      max-width: 640px;
      margin: 0 auto;
      padding: 48px 20px 64px;
    }

    /* ─── PRE-HEADER ─────────────────────────── */
    .pre-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
      padding: 0 2px;
    }

    .pre-header-left {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .logo-box {
      width: 28px;
      height: 28px;
      background: var(--ink);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .logo-box svg { width:14px; height:14px; }

    .org-name {
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--ink);
    }

    .pre-header-date {
      font-size: 11px;
      color: var(--ink-soft);
      letter-spacing: 0.04em;
    }

    /* ─── CARD ───────────────────────────────── */
    .card {
      background: var(--white);
      border: 1px solid var(--rule);
      overflow: hidden;
    }

    /* ─── TOP BAND ───────────────────────────── */
    .top-band {
      height: 4px;
      background: var(--ink);
    }

    /* ─── HERO ───────────────────────────────── */
    .hero {
      padding: 48px 48px 40px;
      border-bottom: 1px solid var(--rule);
      position: relative;
    }

    .report-eyebrow {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 24px;
    }

    .eyebrow-chip {
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: var(--ink-soft);
      border: 1px solid var(--rule);
      padding: 4px 10px;
      background: var(--surface);
    }

    .eyebrow-line {
      flex: 1;
      height: 1px;
      background: var(--rule);
    }

    .eyebrow-time {
      font-size: 10px;
      color: var(--ink-soft);
      letter-spacing: 0.06em;
      white-space: nowrap;
    }

    .hero-title {
      font-family: 'Playfair Display', serif;
      font-size: 36px;
      font-weight: 400;
      line-height: 1.18;
      letter-spacing: -0.5px;
      color: var(--ink);
      margin-bottom: 18px;
    }

    .hero-title em {
      font-style: italic;
      font-weight: 400;
    }

    .hero-lead {
      font-size: 14px;
      font-weight: 300;
      color: var(--ink-mid);
      line-height: 1.75;
      max-width: 440px;
    }

    /* corner ornament */
    .hero::after {
      content: '';
      position: absolute;
      bottom: -1px;
      right: 48px;
      width: 80px;
      height: 3px;
      background: var(--ink);
    }

    /* ─── SUMMARY METRICS ────────────────────── */
    .summary {
      padding: 36px 48px;
      border-bottom: 1px solid var(--rule);
    }

    .section-eyebrow {
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: var(--ink-xsoft);
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .section-eyebrow::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--rule);
    }

    /* big number row */
    .kpi-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 0;
      border: 1px solid var(--rule);
      margin-bottom: 1px;
    }

    .kpi-cell {
      padding: 22px 20px 18px;
      border-right: 1px solid var(--rule);
    }
    .kpi-cell:last-child { border-right: none; }

    .kpi-label {
      font-size: 10px;
      font-weight: 500;
      letter-spacing: 0.09em;
      text-transform: uppercase;
      color: var(--ink-soft);
      margin-bottom: 10px;
      line-height: 1.4;
    }

    .kpi-val {
      font-family: 'Playfair Display', serif;
      font-size: 38px;
      font-weight: 400;
      line-height: 1;
      letter-spacing: -1px;
      color: var(--ink);
      margin-bottom: 6px;
    }

    .kpi-val.danger { color: var(--red); }
    .kpi-val.warn   { color: var(--amber); }

    .kpi-sub {
      font-size: 11px;
      font-weight: 300;
      color: var(--ink-soft);
    }

    /* secondary row */
    .kpi-row-2 {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0;
      border: 1px solid var(--rule);
      border-top: none;
      margin-bottom: 28px;
    }

    .kpi-cell-2 {
      padding: 18px 20px;
      border-right: 1px solid var(--rule);
    }
    .kpi-cell-2:last-child { border-right: none; }

    /* status section */
    .status-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px;
    }

    .status-item {
      padding: 14px 16px;
      border: 1px solid;
    }

    .status-item.red    { border-color: var(--red-bd);   background: var(--red-bg); }
    .status-item.amber  { border-color: var(--amber-bd); background: var(--amber-bg); }
    .status-item.green  { border-color: var(--green-bd); background: var(--green-bg); }

    .status-dot-row {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 8px;
    }

    .status-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .red   .status-dot { background: var(--red); }
    .amber .status-dot { background: var(--amber); }
    .green .status-dot { background: var(--green); }

    .status-name {
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 0.1em;
      text-transform: uppercase;
    }

    .red   .status-name { color: var(--red); }
    .amber .status-name { color: var(--amber); }
    .green .status-name { color: var(--green); }

    .status-count {
      font-family: 'Playfair Display', serif;
      font-size: 24px;
      font-weight: 400;
      letter-spacing: -0.5px;
      color: var(--ink);
      margin-bottom: 2px;
    }

    .status-desc {
      font-size: 10px;
      color: var(--ink-soft);
    }

    /* ─── SCREENSHOT SECTION ─────────────────── */
    .screenshot-section {
      padding: 36px 48px;
      border-bottom: 1px solid var(--rule);
      background: var(--surface);
    }

    .ss-frame {
      border: 1px solid var(--rule);
      background: var(--white);
      overflow: hidden;
    }

    .ss-topbar {
      padding: 10px 14px;
      background: #f4f3f0;
      border-bottom: 1px solid var(--rule);
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .ss-dots {
      display: flex;
      gap: 5px;
    }

    .ss-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
    }

    .ss-dot:nth-child(1) { background: #e8c4c4; }
    .ss-dot:nth-child(2) { background: #e8dbc4; }
    .ss-dot:nth-child(3) { background: #c4e8d4; }

    .ss-url {
      flex: 1;
      font-size: 10px;
      color: var(--ink-soft);
      background: var(--white);
      border: 1px solid var(--rule);
      padding: 3px 10px;
      letter-spacing: 0.02em;
    }

    .ss-body {
      padding: 0;
      position: relative;
    }

    /* placeholder for the actual screenshot image */
    .ss-placeholder {
      width: 100%;
      min-height: 220px;
      background: linear-gradient(145deg, #f7f6f4 0%, #eeede9 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 12px;
    }

    .ss-placeholder svg {
      width: 36px;
      height: 36px;
      opacity: 0.3;
    }

    .ss-placeholder-text {
      font-size: 12px;
      color: var(--ink-soft);
      letter-spacing: 0.04em;
    }

    /* when you have an actual img tag replace placeholder with: */
    .ss-img {
      width: 100%;
      display: block;
    }

    .ss-caption {
      padding: 10px 16px;
      border-top: 1px solid var(--rule);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .ss-caption-left {
      font-size: 10px;
      color: var(--ink-soft);
      letter-spacing: 0.04em;
    }

    .ss-caption-right {
      display: flex;
      align-items: center;
      gap: 4px;
      font-size: 10px;
      color: var(--ink-xsoft);
    }

    .ss-caption-right svg { width:11px; height:11px; }

    /* ─── ATTACHMENT PILL ────────────────────── */
    .attachment-wrap {
      padding: 0 48px 28px;
      background: var(--surface);
      border-bottom: 1px solid var(--rule);
    }

    .attachment-pill {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px 18px;
      border: 1px solid var(--rule);
      background: var(--white);
    }

    .attach-icon {
      width: 36px;
      height: 36px;
      background: var(--ink);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .attach-icon svg { width:16px; height:16px; }

    .attach-info { flex:1; }

    .attach-name {
      font-size: 13px;
      font-weight: 500;
      color: var(--ink);
      margin-bottom: 2px;
    }

    .attach-meta {
      font-size: 10px;
      color: var(--ink-soft);
      letter-spacing: 0.04em;
    }

    .attach-size {
      font-size: 11px;
      color: var(--ink-xsoft);
      font-weight: 400;
    }

    /* ─── CTA ────────────────────────────────── */
    .cta-section {
      padding: 36px 48px;
      border-bottom: 1px solid var(--rule);
      display: flex;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .cta-btn {
      display: inline-block;
      background: var(--ink);
      color: #fff;
      text-decoration: none;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      padding: 15px 36px;
      transition: opacity .15s;
    }

    .cta-note {
      font-size: 12px;
      color: var(--ink-soft);
      font-weight: 300;
    }

    /* ─── FOOTER ─────────────────────────────── */
    .footer {
      padding: 24px 48px;
      background: var(--surface);
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 20px;
      flex-wrap: wrap;
    }

    .footer-left {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .footer-brand {
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--ink);
    }

    .footer-copy {
      font-size: 10px;
      color: var(--ink-xsoft);
      letter-spacing: 0.03em;
    }

    .footer-right {
      font-size: 10px;
      color: var(--ink-xsoft);
      letter-spacing: 0.04em;
      text-align: right;
      line-height: 1.7;
    }

    /* bottom rule stripe */
    .bottom-band {
      height: 2px;
      background: linear-gradient(to right, var(--ink) 0%, var(--ink) 30%, var(--rule) 30%);
    }

    /* ─── RESPONSIVE ─────────────────────────── */
    @media (max-width: 560px) {
      .shell             { padding: 24px 12px 40px; }
      .hero              { padding: 32px 24px 28px; }
      .hero::after       { right: 24px; }
      .hero-title        { font-size: 26px; }
      .summary           { padding: 28px 24px; }
      .kpi-row           { grid-template-columns: 1fr 1fr; }
      .kpi-cell:nth-child(3) { border-right: none; border-top: 1px solid var(--rule); grid-column: 1/-1; }
      .kpi-val           { font-size: 28px; }
      .kpi-row-2         { grid-template-columns: 1fr 1fr; }
      .status-grid       { grid-template-columns: 1fr 1fr; }
      .status-item:last-child { grid-column: 1/-1; }
      .screenshot-section { padding: 28px 24px; }
      .attachment-wrap   { padding: 0 24px 24px; }
      .cta-section       { padding: 28px 24px; flex-direction: column; align-items: flex-start; }
      .footer            { padding: 20px 24px; }
      .footer-right      { text-align: left; }
      .pre-header-date   { display: none; }
    }
  </style>
</head>
<body>
<div class="shell">

  <!-- Pre-header -->
  <div class="pre-header">
    <div class="pre-header-left">
      <div class="logo-box">
        <svg viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="1" y="1" width="5" height="5" fill="white"/>
          <rect x="8" y="1" width="5" height="5" fill="white" opacity=".5"/>
          <rect x="1" y="8" width="5" height="5" fill="white" opacity=".5"/>
          <rect x="8" y="8" width="5" height="5" fill="white"/>
        </svg>
      </div>
      <span class="org-name">IKK Monitoring System</span>
    </div>
    <span class="pre-header-date">{{ now()->format('d F Y') }} · {{ now()->format('H:i') }} WIB</span>
  </div>

  <!-- Card -->
  <div class="card">
    <div class="top-band"></div>

    <!-- ① HERO -->
    <div class="hero">
      <div class="report-eyebrow">
        <div class="eyebrow-chip">Daily Report</div>
        <div class="eyebrow-line"></div>
        <div class="eyebrow-time">Sesi {{ $timeOfDay ?? 'Pagi' }} · {{ now()->format('H:i') }} WIB</div>
      </div>

      <h1 class="hero-title">
        Dashboard <em>IKK</em><br>
        Laporan Harian
      </h1>

      <p class="hero-lead">
        Berikut adalah ringkasan otomatis kondisi Dashboard IKK per
        <strong>{{ isset($summary['reportDate']) ? \Carbon\Carbon::parse($summary['reportDate'])->translatedFormat('d F Y') : now()->format('d F Y') }}</strong>.
        Screenshot dashboard terlampir pada email ini untuk keperluan monitoring dan tindak lanjut.
      </p>
    </div>

    <!-- ② SUMMARY METRICS -->
    <div class="summary">
      <div class="section-eyebrow">Ringkasan Metrik Utama</div>

      @php
        $summary = $summary ?? [];
        $needVerification = $summary['needVerification'] ?? 0;
        $cancelCount = $summary['cancelCount'] ?? 0;
        $compliance = $summary['compliance'] ?? '0%';
        $oakToday = $summary['oakToday'] ?? 0;
        $weeklyCount = $summary['weeklyCount'] ?? 0;
        $needAction = $summary['needAction'] ?? 0;
        $warningCount = $summary['warningCount'] ?? 0;
        $completeCount = $summary['completeCount'] ?? 0;
        $reportDateFormatted = isset($summary['reportDate']) ? \Carbon\Carbon::parse($summary['reportDate'])->translatedFormat('d M Y') : now()->format('d M Y');
      @endphp
      <!-- KPI baris 1 -->
      <div class="kpi-row">
        <div class="kpi-cell">
          <div class="kpi-label">Need<br>Verification</div>
          <div class="kpi-val">{{ $needVerification }}</div>
          <div class="kpi-sub">item IKK pending</div>
        </div>
        <div class="kpi-cell">
          <div class="kpi-label">Pekerjaan<br>Batal</div>
          <div class="kpi-val danger">{{ $cancelCount }}</div>
          <div class="kpi-sub">cancel hari ini</div>
        </div>
        <div class="kpi-cell">
          <div class="kpi-label">Compliance<br>Rate</div>
          <div class="kpi-val {{ (float) str_replace('%', '', $compliance) >= 80 ? '' : 'danger' }}">{{ $compliance }}</div>
          <div class="kpi-sub">target &gt; 80%</div>
        </div>
      </div>

      <!-- KPI baris 2 -->
      <div class="kpi-row-2">
        <div class="kpi-cell-2">
          <div class="kpi-label" style="font-size:10px;font-weight:500;letter-spacing:.09em;text-transform:uppercase;color:var(--ink-soft);margin-bottom:8px;">OAK Hari Ini</div>
          <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:400;letter-spacing:-0.5px;color:var(--ink);margin-bottom:4px;">{{ $oakToday }}</div>
          <div style="font-size:11px;font-weight:300;color:var(--ink-soft);">Total OAK IKK</div>
        </div>
        <div class="kpi-cell-2">
          <div class="kpi-label" style="font-size:10px;font-weight:500;letter-spacing:.09em;text-transform:uppercase;color:var(--ink-soft);margin-bottom:8px;">Data IKK Minggu Ini</div>
          <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:400;letter-spacing:-0.5px;color:var(--ink);margin-bottom:4px;">{{ $weeklyCount }}<span style="font-size:16px;color:var(--ink-soft);">+</span></div>
          <div style="font-size:11px;font-weight:300;color:var(--ink-soft);">per {{ $reportDateFormatted }}</div>
        </div>
      </div>

      <!-- Status breakdown -->
      <div class="section-eyebrow" style="margin-bottom:14px;margin-top:28px;">Status Pekerjaan</div>
      <div class="status-grid">
        <div class="status-item red">
          <div class="status-dot-row">
            <div class="status-dot"></div>
            <div class="status-name">Need Action</div>
          </div>
          <div class="status-count">{{ $needAction }}</div>
          <div class="status-desc">item membutuhkan tindakan segera</div>
        </div>
        <div class="status-item amber">
          <div class="status-dot-row">
            <div class="status-dot"></div>
            <div class="status-name">Warning</div>
          </div>
          <div class="status-count">{{ $warningCount }}</div>
          <div class="status-desc">item dalam status peringatan</div>
        </div>
        <div class="status-item green">
          <div class="status-dot-row">
            <div class="status-dot"></div>
            <div class="status-name">Complete</div>
          </div>
          <div class="status-count">{{ $completeCount }}</div>
          <div class="status-desc">item selesai & terverifikasi</div>
        </div>
      </div>
    </div>

    <!-- ③ SCREENSHOT PREVIEW -->
    <div class="screenshot-section">
      <div class="section-eyebrow" style="margin-bottom:16px;">Screenshot Dashboard</div>
      <div class="ss-frame">
        <div class="ss-topbar">
          <div class="ss-dots">
            <div class="ss-dot"></div>
            <div class="ss-dot"></div>
            <div class="ss-dot"></div>
          </div>
          <div class="ss-url">IKK Dashboard</div>
        </div>
        <div class="ss-body">
          {{-- Screenshot dikirim sebagai lampiran email (attachment), tidak di-embed di body --}}
          <div class="ss-placeholder">
            <svg viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="2" y="6" width="32" height="24" rx="2" stroke="#111110" stroke-width="1.5"/>
              <path d="M2 12h32" stroke="#111110" stroke-width="1.5"/>
              <rect x="6" y="16" width="8" height="10" rx="1" fill="#111110" opacity=".15"/>
              <rect x="16" y="18" width="8" height="8" rx="1" fill="#111110" opacity=".15"/>
              <rect x="26" y="14" width="4" height="12" rx="1" fill="#111110" opacity=".25"/>
            </svg>
            <span class="ss-placeholder-text">Screenshot terlampir di email ini</span>
          </div>
        </div>
        <div class="ss-caption">
          <span class="ss-caption-left">IKK Dashboard · {{ now()->format('d F Y, H:i') }} WIB</span>
          <span class="ss-caption-right">
            <svg viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="5" stroke="#c8c8c2" stroke-width="1"/><path d="M6 4v3M6 8.5v.5" stroke="#c8c8c2" stroke-width="1" stroke-linecap="round"/></svg>
            Semua Situs
          </span>
        </div>
      </div>
    </div>

    <!-- ④ ATTACHMENT PILL -->
    <div class="attachment-wrap">
      <div class="attachment-pill">
        <div class="attach-icon">
          <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="1" width="9" height="13" rx="1" stroke="white" stroke-width="1.2"/>
            <path d="M9 1L13 5" stroke="white" stroke-width="1.2"/>
            <path d="M9 1V5H13" stroke="white" stroke-width="1.2"/>
            <path d="M4.5 7.5h5M4.5 9.5h3.5" stroke="white" stroke-width="1" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="attach-info">
          <div class="attach-name">dashboard-ikk-dopm-{{ now()->format('Ymd') }}-{{ strtolower($timeOfDay ?? 'pagi') }}.png</div>
          <div class="attach-meta">Screenshot otomatis · {{ now()->format('d F Y, H:i') }} WIB</div>
        </div>
        <div class="attach-size">PNG</div>
      </div>
    </div>

    <!-- ⑤ CTA -->
    <div class="cta-section">
      <a href="{{ $dashboardUrl ?? '#' }}" class="cta-btn">Buka Dashboard →</a>
      <span class="cta-note">Atau akses melalui portal internal DOPM</span>
    </div>

    <!-- ⑥ FOOTER -->
    <div class="footer">
      <div class="footer-left">
        <div class="footer-brand">DOPM System</div>
        <div class="footer-copy">© {{ now()->format('Y') }} · IKK Monitoring · Semua Situs</div>
      </div>
      <div class="footer-right">
        Email ini dikirim otomatis sesuai jadwal harian.<br>
        Jangan balas pesan ini · <a href="#" style="color:var(--ink-xsoft);">Unsubscribe</a>
      </div>
    </div>

    <div class="bottom-band"></div>
  </div>

</div>
</body>
</html>