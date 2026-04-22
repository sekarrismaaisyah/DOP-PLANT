<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Gate Go No Go Cards</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script>
    window.PilotProjectValidation = {
      portfolioUrl: @json(route('pilot-project-validation.portfolio.show')),
      portfolioSaveUrl: @json(route('pilot-project-validation.portfolio.store')),
      importExcelUrl: @json(route('pilot-project-validation.portfolio.import-excel')),
      projectsAdminUrl: @json(route('pilot-project-validation.projects.index')),
      projectPdfUrlTemplate: @json(route('pilot-project-validation.project-pdf.show', ['key' => '__KEY__'])),
    };
  </script>
  <style>
    :root {
      --bg: #f0f2f5;
      --surface: #ffffff;
      --surface-soft: #f8fafc;
      --line: #dfe3e6;
      --line-strong: #abadaf;
      --text: #2c2f31;
      --muted: #595c5e;
      --blue: #3952bc;
      --blue-2: #748cf9;
      --green: #2b8a57;
      --green-soft: #e8f6ee;
      --amber: #a2741b;
      --amber-soft: #f9f1dc;
      --red: #b42348;
      --red-soft: #fbe7ec;
      --slate: #5d6368;
      --slate-soft: #edf1f3;
      --shadow: 0 4px 0 rgba(0, 0, 0, 0.04), 0 12px 24px -6px rgba(0, 0, 0, 0.14);
      --shadow-soft: 0 2px 8px rgba(0, 0, 0, 0.08);
      --radius-xl: 22px;
      --radius-lg: 16px;
      --radius-md: 12px;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: "Poppins", "Inter", "Segoe UI", Roboto, Arial, sans-serif;
      color: var(--text);
      background: var(--bg);
    }

    a { color: inherit; }

    .page {
      max-width: 1560px;
      margin: 0 auto;
      padding: 28px;
    }

    .hero {
      background: #fff;
      color: var(--text);
      border-radius: 22px;
      padding: 24px 26px;
      box-shadow: var(--shadow);
      border: 1px solid rgba(171, 173, 175, 0.35);
    }

    .hero h1 {
      margin: 0 0 8px;
      font-size: 28px;
      line-height: 1.1;
      letter-spacing: -0.03em;
      color: var(--blue);
    }

    .hero p {
      margin: 0;
      max-width: 1080px;
      font-size: 14px;
      line-height: 1.6;
      color: var(--muted);
    }

    .toolbar,
    .nav-shell,
    .inline-actions,
    .table-actions,
    .legend-row,
    .upload-row,
    .template-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
    }

    .toolbar { margin-top: 14px; }
    .nav-shell {
      margin-top: 18px;
      background: #fff;
      border: 1px solid rgba(171, 173, 175, 0.35);
      border-radius: 14px;
      padding: 8px;
      box-shadow: var(--shadow-soft);
    }
    .toolbar .btn-ghost { margin-left: auto; }

    .pill,
    .nav-btn,
    .btn-primary,
    .btn-secondary,
    .btn-danger,
    .btn-ghost,
    .btn-mini,
    .file-name {
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: 0.02em;
      border: 1px solid transparent;
      white-space: nowrap;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .pill,
    .file-name {
      padding: 8px 12px;
      background: #f5f7f9;
      border-color: rgba(171, 173, 175, 0.35);
      color: var(--muted);
    }

    .file-name {
      background: #fff;
      border-color: var(--line-strong);
      color: var(--muted);
    }

    .nav-btn,
    .btn-secondary,
    .btn-ghost,
    .btn-mini {
      padding: 9px 12px;
      background: #fff;
      border-color: rgba(171, 173, 175, 0.45);
      color: var(--text);
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .nav-btn {
      background: transparent;
      border: none;
      border-bottom: 2px solid transparent;
      border-radius: 0;
      padding: 8px 14px;
      font-weight: 700;
      color: var(--muted);
    }

    .nav-btn.active,
    .btn-primary {
      padding: 9px 12px;
      background: linear-gradient(135deg, var(--blue), #4c67d8);
      color: #fff;
      box-shadow: 0 8px 18px rgba(57, 82, 188, 0.22);
      cursor: pointer;
    }

    .nav-btn.active {
      background: transparent;
      box-shadow: none;
      border-bottom-color: var(--blue);
      color: var(--blue);
      padding: 8px 14px;
    }

    .btn-danger {
      padding: 9px 12px;
      background: linear-gradient(135deg, #b42348, #db4f72);
      color: #fff;
      box-shadow: 0 8px 18px rgba(180, 35, 72, 0.2);
      cursor: pointer;
    }

    .nav-btn:hover,
    .btn-secondary:hover,
    .btn-ghost:hover,
    .btn-mini:hover {
      border-color: rgba(57, 82, 188, 0.45);
      color: var(--blue);
      transform: translateY(-1px);
    }

    .btn-mini {
      padding: 6px 10px;
      font-size: 11px;
    }

    .page-view {
      display: none;
      margin-top: 18px;
    }

    .page-view.active { display: block; }

    .section-title-bar {
      display: flex;
      justify-content: space-between;
      align-items: end;
      gap: 16px;
      margin-bottom: 12px;
    }

    .section-title h2 {
      margin: 0;
      font-size: 22px;
      letter-spacing: -0.02em;
      color: var(--blue);
    }

    .section-title p,
    .muted-copy,
    .helper-text,
    .table-note,
    .curve-note,
    .notice-copy {
      margin: 6px 0 0;
      font-size: 12px;
      color: var(--muted);
      line-height: 1.55;
    }

    .overview-shell {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 12px;
      margin-bottom: 16px;
    }

    .overview-card,
    .project-card,
    .input-card,
    .panel,
    .mini-card,
    .modal-section,
    .format-card,
    .legend-card,
    .notice-card {
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-soft);
    }

    .overview-card,
    .legend-card,
    .notice-card,
    .format-card {
      padding: 16px;
    }

    .label,
    .overview-label,
    .premium-label,
    .legend-label {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--muted);
      font-weight: 900;
      margin-bottom: 6px;
    }

    .overview-value,
    .decision-score,
    .modal-value,
    .big-value {
      font-size: 26px;
      font-weight: 900;
      line-height: 1;
      letter-spacing: -0.03em;
    }

    .dashboard-grid,
    .input-grid,
    .curve-layout,
    .format-grid {
      display: grid;
      gap: 16px;
    }

    .dashboard-grid {
      grid-template-columns: 1fr;
      gap: 18px;
      align-items: start;
    }

    .curve-layout { grid-template-columns: 1.4fr 320px; margin-bottom: 16px; }
    .format-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }

    .project-card,
    .input-card { overflow: hidden; }

    .dashboard-grid .project-card {
      position: relative;
      border-radius: 24px;
      border: 1px solid rgba(146, 165, 193, 0.34);
      background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
      box-shadow: 0 10px 28px -16px rgba(47, 84, 124, 0.45), 0 2px 8px rgba(31, 55, 89, 0.08);
      transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .dashboard-grid .project-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 34px -18px rgba(47, 84, 124, 0.52), 0 8px 18px -12px rgba(31, 55, 89, 0.28);
    }

    .dashboard-grid .project-card::before {
      content: '';
      position: absolute;
      inset: 0 0 auto 0;
      height: 4px;
      background: linear-gradient(90deg, #3952bc 0%, #6289ff 45%, #3aa0d9 100%);
      opacity: 0.88;
      pointer-events: none;
    }

    .project-card,
    .input-card,
    .overview-card,
    .curve-card,
    .legend-card,
    .notice-card,
    .format-card {
      box-shadow: var(--shadow);
    }

    .project-card .panel,
    .input-card .panel,
    .modal-section,
    .project-card .mini-card,
    .input-card .mini-card,
    .project-card .status-box,
    .input-card .status-box,
    .project-card .curve-box,
    .input-card .curve-box {
      box-shadow: none;
      background: #f8fafd;
      border-color: rgba(201, 214, 227, 0.75);
    }

    .decision-box {
      background: var(--surface-soft);
      border: 1px solid var(--line);
      border-radius: var(--radius-lg);
      padding: 14px 16px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .decision-badge,
    .status-chip,
    .task-status,
    .metric-status {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 7px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 900;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      width: fit-content;
      white-space: nowrap;
    }

    .decision-badge { font-size: 12px; padding: 8px 11px; }

    .decision-go, .status-pass, .task-done, .metric-pass { background: var(--green-soft); color: var(--green); border: 1px solid rgba(32,178,107,0.22); }
    .decision-conditional, .status-conditional, .task-progress, .metric-conditional { background: var(--amber-soft); color: var(--amber); border: 1px solid rgba(224,161,27,0.22); }
    .decision-nogo, .status-fail, .task-risk, .metric-fail { background: var(--red-soft); color: var(--red); border: 1px solid rgba(217,83,79,0.22); }
    .task-plan { background: var(--slate-soft); color: var(--slate); border: 1px solid rgba(107,127,147,0.20); }

    .dashboard-body {
      display: grid;
      grid-template-columns: 1.05fr 1fr;
      gap: 24px;
      padding: 22px 24px 24px;
    }

    .panel,
    .modal-section { overflow: hidden; }

    .dashboard-body .panel {
      border-color: rgba(169, 187, 214, 0.55);
      background: linear-gradient(180deg, #fcfdff 0%, #f4f8ff 100%);
    }

    .panel-head,
    .modal-section-head { padding: 16px 18px 0; }

    .dashboard-body .panel-head {
      border-bottom: 1px solid rgba(169, 187, 214, 0.42);
      padding-bottom: 12px;
    }

    .panel-inner,
    .modal-section-inner { padding: 16px 18px 18px; }

    .panel-title {
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: var(--muted);
      font-weight: 900;
    }

    .panel-subtitle {
      color: var(--muted);
      font-size: 12px;
      line-height: 1.55;
      margin-top: 6px;
    }

    .progress-band {
      display: grid;
      grid-template-columns: 1fr minmax(190px, 220px) minmax(190px, 220px);
      gap: 14px;
      align-items: center;
      margin-bottom: 14px;
    }

    .progress-info h4,
    .status-box h4,
    .dashboard-summary h3,
    .collapse-summary h3 {
      margin: 0 0 5px;
      font-size: 22px;
      letter-spacing: -0.02em;
    }

    .dashboard-summary p,
    .collapse-summary p {
      margin: 8px 0 0;
      color: var(--muted);
      font-size: 14px;
      line-height: 1.55;
      max-width: 860px;
    }

    .bar-head {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      align-items: center;
      font-size: 12px;
      color: var(--text);
      font-weight: 800;
      margin-bottom: 7px;
    }

    .bar-track {
      width: 100%;
      height: 18px;
      border-radius: 999px;
      background: #ecf2f6;
      border: 1px solid var(--line);
      overflow: hidden;
    }

    .bar-fill {
      height: 100%;
      border-radius: 999px;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      padding-right: 8px;
      color: rgba(255,255,255,0.92);
      font-size: 10px;
      font-weight: 900;
      letter-spacing: 0.04em;
      background: linear-gradient(90deg, #2e6f99, #6c91ad);
    }

    .status-grid,
    .dashboard-meta-stack {
      display: grid;
      gap: 14px;
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .status-box,
    .mini-card,
    .curve-box {
      background: var(--surface-soft);
      border: 1px solid rgba(201, 214, 227, 0.7);
      border-radius: var(--radius-lg);
      padding: 14px 16px;
    }

    .status-box {
      background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
      border-color: rgba(162, 183, 212, 0.55);
    }

    .mini-value,
    .curve-value {
      font-size: 14px;
      font-weight: 700;
      line-height: 1.45;
    }

    .status-mini-list,
    .task-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .status-mini-item,
    .task-item {
      display: grid;
      gap: 8px;
      align-items: center;
      font-size: 12px;
      color: var(--muted);
      padding: 8px 10px;
      border-radius: 12px;
      background: #f9fbfd;
      border: 1px solid rgba(201, 214, 227, 0.7);
    }

    .status-mini-item { grid-template-columns: 1fr auto; }
    .task-item { grid-template-columns: auto 1fr auto; }

    .task-bullet {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: var(--blue);
      box-shadow: 0 0 0 4px rgba(46,111,153,0.08);
    }

    .task-text { color: var(--text); font-weight: 700; line-height: 1.45; }
    .task-owner { color: var(--muted); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; }

    .input-card .panel + .panel { margin-top: 14px; }
    .input-body { padding: 18px 20px 20px; }

    .project-collapsible,
    .dashboard-collapsible { overflow: hidden; }

    .dashboard-collapse-head {
      display: grid;
      grid-template-columns: 1.2fr 260px auto;
      gap: 18px;
      align-items: start;
      padding: 20px 24px;
      border-bottom: 1px solid var(--line);
      background: linear-gradient(135deg, #f6f9ff 0%, #edf4ff 54%, #f6fbff 100%);
    }

    .dashboard-summary h3 {
      color: #244274;
      font-size: 24px;
      margin-bottom: 4px;
    }

    .dashboard-meta-stack .mini-card {
      background: #ffffff;
      border-color: rgba(169, 187, 214, 0.7);
    }

    .dashboard-collapse-actions,
    .collapse-actions {
      display: flex;
      gap: 10px;
      align-items: center;
      justify-content: flex-end;
      flex-wrap: wrap;
    }

    .collapse-head {
      display: grid;
      grid-template-columns: 1.15fr 280px;
      gap: 18px;
      align-items: start;
      padding: 20px 24px;
      border-bottom: 1px solid var(--line);
      background: #f8fbfd;
    }

    .collapse-main {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 16px;
      align-items: start;
    }

    .collapse-meta {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 10px;
    }

    .collapse-pill,
    .template-tag {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 7px 10px;
      border-radius: 999px;
      background: #fff;
      border: 1px solid var(--line);
      color: var(--muted);
      font-size: 11px;
      font-weight: 800;
      letter-spacing: 0.03em;
      text-decoration: none;
    }

    .collapse-toggle {
      min-width: 124px;
      justify-content: center;
    }

    .collapse-chevron {
      width: 34px;
      height: 34px;
      border-radius: 999px;
      border: 1px solid var(--line-strong);
      background: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: var(--text);
      font-size: 16px;
      font-weight: 900;
      transition: transform 0.2s ease;
      flex: 0 0 auto;
    }

    .project-collapsible.is-open .collapse-chevron,
    .dashboard-collapsible.is-open .collapse-chevron { transform: rotate(180deg); }

    .dashboard-empty {
      border-radius: 22px;
      border: 1px dashed rgba(116, 140, 171, 0.52);
      background: linear-gradient(180deg, #f9fbff 0%, #edf4ff 100%);
      padding: 28px 24px;
      text-align: center;
      color: #365375;
      font-size: 13px;
      font-weight: 600;
    }

    .table-wrap {
      overflow: auto;
      border: 1px solid var(--line);
      border-radius: 14px;
      background: #fff;
    }

    table.clean-table {
      width: 100%;
      border-collapse: collapse;
      min-width: 980px;
      font-size: 12px;
    }

    .clean-table th,
    .clean-table td {
      border-bottom: 1px solid var(--line);
      padding: 10px 10px;
      vertical-align: top;
      text-align: left;
    }

    .clean-table th {
      position: sticky;
      top: 0;
      z-index: 1;
      background: #f8fafc;
      color: var(--muted);
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.15em;
      font-weight: 900;
    }

    .clean-table tr:last-child td { border-bottom: none; }
    .clean-table tbody tr:hover td { background: #f8fafc; }

    .table-input,
    .table-select,
    .table-textarea {
      width: 100%;
      border: 1px solid var(--line-strong);
      border-radius: 10px;
      padding: 8px 10px;
      background: #fff;
      color: var(--text);
      font-size: 12px;
      font-family: inherit;
    }

    .table-textarea {
      min-height: 64px;
      resize: vertical;
    }

    .table-number {
      width: 92px;
      border: 1px solid var(--line-strong);
      border-radius: 10px;
      padding: 8px 10px;
      background: #fff;
      color: var(--text);
      font-size: 12px;
      font-family: inherit;
    }

    .table-range {
      width: 180px;
      accent-color: var(--blue);
    }

    .compact-stack {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .metric-badges {
      display: flex;
      flex-wrap: wrap;
      gap: 6px;
    }

    .import-panel { margin-bottom: 16px; }

    .format-card h4,
    .legend-card h4,
    .notice-card h4,
    .curve-card h4 {
      margin: 0;
      font-size: 18px;
      letter-spacing: -0.02em;
    }

    .curve-card {
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow);
      padding: 16px;
    }

    .curve-svg-wrap {
      width: 100%;
      overflow: auto;
      border: 1px solid var(--line);
      border-radius: 18px;
      background: #fbfdff;
      padding: 8px;
    }

    .curve-svg-wrap svg {
      display: block;
      width: 100%;
      min-width: 860px;
      height: 420px;
    }

    .curve-svg-wrap.project-curve-wrap svg {
      min-width: 480px;
      height: 280px;
    }

    .project-curve-stack {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .legend-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      display: inline-block;
    }

    .legend-item {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      color: var(--muted);
      font-weight: 700;
    }

    .notice-success {
      background: var(--green-soft);
      border-color: rgba(32,178,107,0.20);
    }

    .notice-warning {
      background: var(--amber-soft);
      border-color: rgba(224,161,27,0.20);
    }

    .notice-error {
      background: var(--red-soft);
      border-color: rgba(217,83,79,0.20);
    }

    .upload-input { display: none; }

    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(11, 24, 38, 0.46);
      backdrop-filter: blur(8px);
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
      z-index: 1000;
    }

    .modal-overlay.active { display: flex; }

    .modal-shell {
      width: min(1320px, 100%);
      max-height: 94vh;
      overflow: auto;
      background: linear-gradient(180deg, #f4f8fb 0%, #eef4f8 100%);
      border-radius: 24px;
      box-shadow: 0 26px 70px rgba(17, 34, 51, 0.28);
      border: 1px solid rgba(255,255,255,0.9);
      position: relative;
    }

    .modal-close {
      position: sticky;
      top: 14px;
      margin: 14px 14px 0 auto;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 999px;
      border: 1px solid var(--line-strong);
      background: rgba(255,255,255,0.9);
      color: var(--text);
      font-size: 18px;
      font-weight: 900;
      cursor: pointer;
      z-index: 2;
    }

    .modal-content { padding: 0 18px 24px; }

    .modal-hero {
      background: linear-gradient(135deg, #17324d, #294c6e);
      color: #fff;
      border-radius: 20px;
      padding: 22px;
      margin-top: -8px;
      box-shadow: 0 14px 30px rgba(22, 45, 67, 0.18);
    }

    .modal-hero h2 {
      margin: 0;
      font-size: 30px;
      line-height: 1.05;
      letter-spacing: -0.03em;
    }

    .modal-hero h2 span { color: #f6b437; }

    .modal-hero p {
      margin: 10px 0 0;
      max-width: 780px;
      font-size: 14px;
      line-height: 1.65;
      color: rgba(255,255,255,0.9);
    }

    .modal-flags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 14px;
    }

    .modal-flag {
      padding: 8px 12px;
      border-radius: 999px;
      background: rgba(255,255,255,0.12);
      border: 1px solid rgba(255,255,255,0.16);
      font-size: 11px;
      font-weight: 800;
      text-transform: uppercase;
    }

    .modal-grid {
      display: grid;
      grid-template-columns: 320px 1fr;
      gap: 16px;
      margin-top: 16px;
    }

    @media (max-width: 1360px) {
      .overview-shell { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .curve-layout,
      .format-grid,
      .dashboard-collapse-head,
      .collapse-head,
      .collapse-main,
      .dashboard-body,
      .modal-grid { grid-template-columns: 1fr; }
      .status-grid,
      .dashboard-meta-stack { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .progress-band { grid-template-columns: 1fr; }
    }

    @media (max-width: 860px) {
      .page { padding: 16px; }
      .hero h1,
      .modal-hero h2 { font-size: 24px; }
      .overview-shell,
      .status-grid,
      .dashboard-meta-stack,
      .format-grid { grid-template-columns: 1fr; }
      .toolbar,
      .nav-shell,
      .upload-row { align-items: stretch; }
      .toolbar .btn-ghost { margin-left: 0; }
      .modal-overlay { padding: 10px; }
      .modal-content { padding: 0 10px 18px; }
      .task-item { grid-template-columns: auto 1fr; }
      .task-owner { grid-column: 2; }
    }

    /* Alur kerja & kejelasan upload */
    .flow-strip {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 12px;
      margin-bottom: 16px;
    }
    .flow-item {
      background: var(--surface-soft);
      border: 1px solid var(--line);
      border-radius: var(--radius-lg);
      padding: 12px 14px;
      font-size: 12px;
      line-height: 1.5;
      color: var(--text);
    }
    .flow-item strong {
      display: block;
      font-size: 11px;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      color: var(--blue);
      margin-bottom: 6px;
    }
    .flow-num {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 22px;
      height: 22px;
      border-radius: 999px;
      background: linear-gradient(135deg, var(--blue), var(--blue-2));
      color: #fff;
      font-size: 11px;
      font-weight: 900;
      margin-right: 8px;
      vertical-align: middle;
    }
    .callout-warn {
      background: var(--amber-soft);
      border: 1px solid rgba(216,148,16,0.35);
      border-radius: var(--radius-md);
      padding: 10px 12px;
      font-size: 12px;
      color: var(--text);
      line-height: 1.55;
      margin-bottom: 14px;
    }
    .callout-warn strong { color: #a66f0a; }
    .upload-steps {
      margin: 0 0 12px;
      padding-left: 1.2em;
      font-size: 12px;
      color: var(--muted);
      line-height: 1.65;
    }
    .upload-steps li { margin-bottom: 4px; }
    .sheet-names {
      font-family: ui-monospace, Consolas, monospace;
      font-size: 11px;
      background: var(--slate-soft);
      padding: 2px 6px;
      border-radius: 4px;
    }
    .subpanel-title {
      font-size: 13px;
      font-weight: 800;
      color: var(--text);
      margin: 0 0 6px;
    }
    @media (max-width: 860px) {
      .flow-strip { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="page">
    <section class="hero">
      <h1>Key Pilot Projects &amp; Technical Validation</h1>
      <!-- <p>Konsep tampilan mengikuti gaya Peer Pressure Edukasi: clean, fokus data, dan konsisten untuk monitoring harian serta validasi keputusan GO/NO-GO.</p> -->
      
      <div class="toolbar">
        <div class="pill">Bobot gate: G1 25% · G2 30% · G3 25% · G4 20%</div>
        <div class="pill">Gate 1 &amp; Gate 3 = hard gate</div>
        <div class="pill">Impor Excel = isi ulang di browser</div>
        <button class="btn-ghost" id="resetBtn" type="button" title="Kembalikan contoh data bawaan di browser">Reset ke contoh default</button>
        <button class="btn-ghost" id="loadFromServerBtn" type="button" title="Ambil portfolio tersimpan dari database">Muat dari server</button>
        <button class="btn-primary" id="saveToServerBtn" type="button" title="Simpan portfolio saat ini ke database">Simpan ke server</button>
        <a class="btn-ghost" id="ppvAdminLink" href="{{ route('pilot-project-validation.projects.index') }}" style="text-decoration:none;">Kelola daftar proyek</a>
      </div>
    </section>

    <div class="nav-shell">
      <button class="nav-btn active" id="dashboardTab" type="button">Dashboard</button>
      <button class="nav-btn" id="inputTab" type="button">Input &amp; impor Excel</button>
      <a href="/pilot-project-validation/projects" class="nav-btn" id="" type="button">Database Input</a>
    </div>

    <section class="page-view active" id="dashboardPage">
      <div class="section-title-bar">
        <div class="section-title">
          <h2>Dashboard</h2>
          <p>Tampilan monitoring: ringkasan portofolio, kurva S, dan kartu proyek. Pengeditan tidak dilakukan di tab ini.</p>
        </div>
      </div>
      <section class="overview-shell" id="overviewShell"></section>
      <section class="curve-layout">
        <div class="curve-card">
          <div class="panel-title">Kurva S portofolio per periode</div>
          <div class="curve-note" id="curveModeNote" lang="id"></div>
          <div class="legend-row" style="margin:12px 0 14px;">
            <span class="legend-item"><span class="legend-dot" style="background:#2e6f99;"></span>Kurva progress</span>
            <span class="legend-item"><span class="legend-dot" style="background:#d89410;"></span>Kurva keputusan</span>
          </div>
          <div class="curve-svg-wrap" id="curveChartWrap"></div>
        </div>
        <div class="legend-card">
          <div class="legend-label">List Need Support</div>
          <div class="notice-copy" style="margin-top:8px;">Daftar kebutuhan support per proyek. Nilainya diambil dari kolom <strong>Support</strong> pada data proyek.</div>
          <div id="needSupportList" style="margin-top:12px;">
            @forelse(($needSupportProjects ?? []) as $supportRow)
              <div class="curve-box" style="margin-top:10px;">
                <div class="legend-label">{{ $supportRow->project_name }}</div>
                <div class="curve-value">{{ trim((string) $supportRow->support) !== '' ? $supportRow->support : 'Tidak ada need support' }}</div>
              </div>
            @empty
              <div class="curve-value">Belum ada proyek.</div>
            @endforelse
          </div>
        </div>
      </section>
      <section class="dashboard-grid" id="dashboardGrid"></section>
    </section>

    <section class="page-view" id="inputPage">
      <div class="section-title-bar">
        <div class="section-title">
          <h2>Input &amp; impor</h2>
          <p>Isi data dengan tabel di bawah (manual) atau impor satu file Excel. Setelah puas, gunakan <strong>Simpan ke server</strong> di atas agar data masuk database.</p>
        </div>
        <div class="inline-actions">
          <button class="btn-primary" type="button" data-action="add-project">Tambah proyek</button>
        </div>
      </div>

      <div class="flow-strip" aria-label="Alur data">
        <div class="flow-item">
          <strong><span class="flow-num">1</span> Input di browser</strong>
          Edit manual di tabel di halaman ini, atau impor Excel. Perubahan ada di memori halaman (belum otomatis ke database).
        </div>
        <div class="flow-item">
          <strong><span class="flow-num">2</span> Impor Excel (opsional)</strong>
          File .xlsx/.xls dengan sheet wajib <span class="sheet-names">PROJECTS</span>, <span class="sheet-names">GATES</span>, <span class="sheet-names">METRICS</span>, serta timeline: <span class="sheet-names">TIMELINE_PERIODS</span> / <span class="sheet-names">TIMELINE_TASKS</span> (disarankan) atau <span class="sheet-names">TIMELINE</span> (format lama).
        </div>
        <div class="flow-item">
          <strong><span class="flow-num">3</span> Simpan ke server</strong>
          Tombol di banner atas mengirim data ke database. <strong>Muat dari server</strong> mengambil kembali data tersimpan.
        </div>
      </div>

      <section class="panel import-panel" id="excelImportSection">
        <div class="panel-head">
          <div class="panel-title">Impor dari file Excel</div>
          <div class="panel-subtitle">Ganti seluruh portofolio di layar dengan isi workbook. Sheet wajib: PROJECTS, GATES, METRICS. Timeline: TIMELINE_PERIODS dan TIMELINE_TASKS (template baru) atau satu sheet TIMELINE (lama). HISTORY opsional.</div>
        </div>
        <div class="panel-inner">
          <div class="notice-card" id="importStatusCard" style="margin-bottom:16px;">
            <div class="legend-label">Status impor / sinkron</div>
            <h4 id="importStatusTitle">Siap</h4>
            <div class="notice-copy" id="importStatusText">Pilih file Excel lalu klik Impor. Setelah berhasil, klik Simpan ke server jika ingin menyimpan ke database.</div>
          </div>

          <div class="callout-warn">
            <strong>Perhatian:</strong> <strong>Impor Excel</strong> menghapus isi portofolio di layar dan menggantinya dengan data dari file (satu arah, di browser).
            Itu berbeda dari <strong>Simpan ke server</strong>, yang menyimpan snapshot portofolio saat ini ke database.
          </div>

          <div class="notice-card" style="margin-bottom:16px;">
            <div class="legend-label">Langkah impor file</div>
            <p class="subpanel-title">Upload workbook (.xlsx atau .xls)</p>
            <ol class="upload-steps">
              <li>Klik <strong>Pilih file</strong> dan pilih satu workbook dari komputer Anda.</li>
              <li>Pastikan ada <span class="sheet-names">PROJECTS</span>, <span class="sheet-names">GATES</span>, <span class="sheet-names">METRICS</span>, dan salah satu rangkaian timeline: <span class="sheet-names">TIMELINE_PERIODS</span> / <span class="sheet-names">TIMELINE_TASKS</span> atau <span class="sheet-names">TIMELINE</span>.</li>
              <li>Klik <strong>Impor workbook</strong>. Data akan dimuat ke halaman ini; Dashboard ikut memperbarui.</li>
              <li>Jika ingin menyimpan ke database, klik <strong>Simpan ke server</strong> di banner atas.</li>
            </ol>
            <div class="upload-row" style="margin-top:12px;">
              <input class="upload-input" id="excelFileInput" type="file" accept=".xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" />
              <button class="btn-primary" id="chooseFileBtn" type="button">Pilih file</button>
              <button class="btn-secondary" id="importExcelBtn" type="button">Impor ke layar (browser)</button>
              <button class="btn-primary" id="importExcelSaveDbBtn" type="button">Impor &amp; simpan ke database</button>
              <span class="file-name" id="selectedFileName">Belum ada file</span>
            </div>
            <p class="table-note" style="margin-top:12px;"><strong>Impor ke layar</strong> memakai JavaScript di browser. <strong>Impor &amp; simpan ke database</strong> mengunggah file ke server (PhpSpreadsheet) lalu menulis langsung ke tabel — disarankan untuk data besar. Tip: nama sheet harus sama persis dengan referensi di bawah.</p>
          </div>

          <div class="notice-card" style="margin-bottom:16px;">
            <div class="legend-label">Unduh template Excel (contoh)</div>
            <p class="subpanel-title">Satu file .xlsx siap impor</p>
            <p class="table-note" style="margin-bottom:10px;">
              Berisi <strong>PROJECTS</strong>, <strong>TIMELINE_PERIODS</strong>, <strong>TIMELINE_TASKS</strong>, <strong>TIMELINE</strong> (header saja, opsional), <strong>GATES</strong>, <strong>METRICS</strong>, <strong>HISTORY</strong> — contoh <strong>Arcas HD</strong>. Nama sheet harus sama persis.
            </p>
            <div class="upload-row">
              <a class="btn-primary" href="{{ route('pilot-project-validation.template-excel') }}" style="text-decoration:none;">Unduh template-pilot-project-validation.xlsx</a>
            </div>
          </div>

          <div class="notice-card" style="margin-bottom:16px;">
            <div class="legend-label">Unduh contoh (CSV)</div>
            <p class="subpanel-title">Template per sheet (bukan file Excel tunggal)</p>
            <p class="table-note" style="margin-bottom:10px;">
              Tombol ini mengunduh beberapa file <strong>.csv</strong> (PROJECTS, TIMELINE, …). Anda bisa membuka masing-masing di Excel, menyalin ke workbook baru dengan nama sheet yang benar, lalu menyimpan sebagai <strong>.xlsx</strong> untuk diimpor.
            </p>
            <div class="upload-row">
              <button class="btn-secondary" id="downloadTemplateBtn" type="button">Unduh template CSV (semua sheet)</button>
            </div>
            <div class="template-tags" style="margin-top:12px;">
              <span class="template-tag">PROJECTS</span>
              <span class="template-tag">TIMELINE_PERIODS</span>
              <span class="template-tag">TIMELINE_TASKS</span>
              <span class="template-tag">TIMELINE</span>
              <span class="template-tag">GATES</span>
              <span class="template-tag">METRICS</span>
              <span class="template-tag">HISTORY opsional</span>
            </div>
          </div>

          <div class="section-title" style="margin-bottom:10px;">
            <h3 style="margin:0 0 6px;font-size:16px;letter-spacing:-0.02em;">Referensi nama kolom (header baris pertama di Excel)</h3>
            <p class="muted-copy" style="margin:0;">Sesuaikan nama sheet dan kolom dengan tabel berikut agar impor berhasil.</p>
          </div>

          <div class="format-card" style="margin-bottom:16px;">
            <div class="legend-label">Skema tabel & relasi</div>
            <h4>Struktur database Pilot Project Validation</h4>
            <div class="table-note" style="margin-top:8px;">
              Tabel yang digunakan: <code>pilot_project_validation_projects</code>, <code>pilot_project_validation_roadmap_periods</code>, <code>pilot_project_validation_timeline_tasks</code>, <code>pilot_project_validation_gates</code>, <code>pilot_project_validation_metrics</code>, <code>pilot_project_validation_history_snapshots</code>.
            </div>
            <div class="table-wrap" style="margin-top:10px;">
              <table class="clean-table" style="min-width:980px;">
                <thead>
                  <tr>
                    <th>Tabel</th>
                    <th>Primary Key</th>
                    <th>Foreign Key</th>
                    <th>Relasi</th>
                    <th>Keterangan</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><code>pilot_project_validation_projects</code></td>
                    <td><code>id</code></td>
                    <td>-</td>
                    <td>Parent utama</td>
                    <td>Satu proyek punya banyak periode roadmap, gate, dan history.</td>
                  </tr>
                  <tr>
                    <td><code>pilot_project_validation_roadmap_periods</code></td>
                    <td><code>id</code></td>
                    <td><code>project_id</code> -&gt; <code>projects.id</code></td>
                    <td>projects 1:N roadmap_periods</td>
                    <td>Satu baris periode roadmap untuk satu proyek.</td>
                  </tr>
                  <tr>
                    <td><code>pilot_project_validation_timeline_tasks</code></td>
                    <td><code>id</code></td>
                    <td><code>roadmap_period_id</code> -&gt; <code>roadmap_periods.id</code></td>
                    <td>roadmap_periods 1:N timeline_tasks</td>
                    <td>Task detail per periode roadmap.</td>
                  </tr>
                  <tr>
                    <td><code>pilot_project_validation_gates</code></td>
                    <td><code>id</code></td>
                    <td><code>project_id</code> -&gt; <code>projects.id</code></td>
                    <td>projects 1:N gates</td>
                    <td>Definisi gate per proyek.</td>
                  </tr>
                  <tr>
                    <td><code>pilot_project_validation_metrics</code></td>
                    <td><code>id</code></td>
                    <td><code>gate_id</code> -&gt; <code>gates.id</code></td>
                    <td>gates 1:N metrics</td>
                    <td>Metrik penilaian pada setiap gate.</td>
                  </tr>
                  <tr>
                    <td><code>pilot_project_validation_history_snapshots</code></td>
                    <td><code>id</code></td>
                    <td><code>project_id</code> -&gt; <code>projects.id</code></td>
                    <td>projects 1:N history_snapshots</td>
                    <td>Snapshot historis progress dan decision score.</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p class="table-note" style="margin-top:10px;">
              Semua relasi FK di atas menggunakan <strong>cascadeOnDelete</strong>: jika proyek dihapus, data turunan (periode, task, gate, metrik, history) ikut terhapus.
            </p>
          </div>

          <div class="format-grid">
            <div class="format-card">
              <div class="legend-label">Sheet</div>
              <h4>PROJECTS</h4>
              <div class="table-note">Tabel <code>pilot_project_validation_projects</code> — satu baris per proyek.</div>
              <div class="table-wrap" style="margin-top:10px;">
                <table class="clean-table" style="min-width:760px;">
                  <thead>
                    <tr>
                      <th>project_name</th>
                      <th>subtitle</th>
                      <th>pilot_area</th>
                      <th>support</th>
                      <th>current_phase</th>
                      <th>progress</th>
                      <th>current_period</th>
                      <th>next_milestone</th>
                      <th>need_support_pic</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Arcas HD</td>
                      <td>…</td>
                      <td>…</td>
                      <td>…</td>
                      <td>…</td>
                      <td>62.5</td>
                      <td>Apr–Jun 2026</td>
                      <td>…</td>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="format-card">
              <div class="legend-label">Sheet</div>
              <h4>TIMELINE_PERIODS</h4>
              <div class="table-note"><code>pilot_project_validation_roadmap_periods</code> + <code>project_name</code>. Kolom <code>period</code> = periode roadmap (bukan label tampilan).</div>
              <div class="table-wrap" style="margin-top:10px;">
                <table class="clean-table" style="min-width:920px;">
                  <thead>
                    <tr>
                      <th>project_name</th>
                      <th>display_current_period</th>
                      <th>period</th>
                      <th>phase</th>
                      <th>status</th>
                      <th>period_progress_percent</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Arcas HD</td>
                      <td>Apr - Jun 2026</td>
                      <td>Jan - Mar 2026</td>
                      <td>Infrastructure …</td>
                      <td>done</td>
                      <td>100</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="format-card">
              <div class="legend-label">Sheet</div>
              <h4>TIMELINE_TASKS</h4>
              <div class="table-note"><code>pilot_project_validation_timeline_tasks</code> + <code>project_name</code>, <code>period</code>, <code>phase</code> harus cocok dengan TIMELINE_PERIODS.</div>
              <div class="table-wrap" style="margin-top:10px;">
                <table class="clean-table" style="min-width:760px;">
                  <thead>
                    <tr>
                      <th>project_name</th>
                      <th>period</th>
                      <th>phase</th>
                      <th>task_text</th>
                      <th>task_owner</th>
                      <th>task_status</th>
                      <th>pic_actual_percent</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Arcas HD</td>
                      <td>Jan - Mar 2026</td>
                      <td>Infrastructure …</td>
                      <td>Validate network…</td>
                      <td>IT / Automation</td>
                      <td>done</td>
                      <td>1</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="format-card">
              <div class="legend-label">Sheet</div>
              <h4>GATES</h4>
              <div class="table-note"><code>pilot_project_validation_gates</code> — lihat template .xlsx untuk kolom lengkap (<code>gate_definition</code>, <code>what_gate_confirms</code>, …).</div>
              <div class="table-wrap" style="margin-top:10px;">
                <table class="clean-table" style="min-width:760px;">
                  <thead>
                    <tr>
                      <th>project_name</th>
                      <th>gate_label</th>
                      <th>gate_title</th>
                      <th>gate_caption</th>
                      <th>hard_gate</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Arcas HD</td>
                      <td>Gate 1</td>
                      <td>Technical Feasibility</td>
                      <td></td>
                      <td>yes</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="format-card">
              <div class="legend-label">Sheet</div>
              <h4>METRICS</h4>
              <div class="table-note"><code>pilot_project_validation_metrics</code>. Tipe <strong>range</strong>: isi <code>metric_value</code>, <code>min_value</code>, <code>max_value</code>, <code>step_value</code>, ambang batas. Tipe <strong>select</strong>: <code>metric_value</code> = pass / conditional / fail. Alias <code>current_value</code>, <code>min</code>, … tetap didukung.</div>
              <div class="table-wrap" style="margin-top:10px;">
                <table class="clean-table" style="min-width:1020px;">
                  <thead>
                    <tr>
                      <th>project_name</th>
                      <th>gate_label</th>
                      <th>metric_name</th>
                      <th>metric_type</th>
                      <th>metric_desc</th>
                      <th>direction</th>
                      <th>unit</th>
                      <th>critical</th>
                      <th>metric_value</th>
                      <th>pass_threshold</th>
                      <th>conditional_threshold</th>
                      <th>min_value</th>
                      <th>max_value</th>
                      <th>step_value</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Arcas HD</td>
                      <td>Gate 1</td>
                      <td>Network uptime</td>
                      <td>range</td>
                      <td>Live network support</td>
                      <td>high</td>
                      <td>%</td>
                      <td>no</td>
                      <td>90.1</td>
                      <td>98</td>
                      <td>96</td>
                      <td>90</td>
                      <td>100</td>
                      <td>0.1</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="format-card">
              <div class="legend-label">Sheet</div>
              <h4>HISTORY</h4>
              <div class="table-note"><code>pilot_project_validation_history_snapshots</code> — <code>snapshot_date</code>, <code>progress</code>, <code>decision_score</code> (opsional), atau <code>decision_status</code> untuk skor tersirat.</div>
              <div class="table-wrap" style="margin-top:10px;">
                <table class="clean-table" style="min-width:520px;">
                  <thead>
                    <tr>
                      <th>project_name</th>
                      <th>snapshot_date</th>
                      <th>progress</th>
                      <th>decision_score</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Arcas HD</td>
                      <td>2026-04-01</td>
                      <td>52</td>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="input-grid" id="inputGrid"></section>
    </section>
  </div>

  <div class="modal-overlay" id="modalOverlay">
    <div class="modal-shell">
      <button class="modal-close" id="modalClose" aria-label="Close">×</button>
      <div class="modal-content" id="modalContent"></div>
    </div>
  </div>

  {{-- SheetJS (xlsx): diperlukan untuk Import Workbook (.xlsx / .xls) di browser --}}
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js" crossorigin="anonymous"></script>
  <script>
    (function () {
      const SCORE_MAP = { pass: 100, conditional: 70, fail: 35 };
      const GATE_WEIGHTS = [25, 30, 25, 20];

      let currentPage = 'dashboard';
      let activeModal = null;
      let expandedProjects = new Set([0]);
      let expandedDashboardProjects = new Set([0]);
      let historySnapshots = [];
      let selectedWorkbookFile = null;

      const seedProjects = [
        {
          name: 'Arcas HD',
          subtitle: 'Heavy automation use case with high dependency on network, ROC monitoring, and safe fallback control.',
          pilotArea: 'PAMA BMO2, MTN SMO',
          support: 'Infrastructure Network (5G), ROC / Monitoring, autonomous zone readiness',
          currentPhase: 'Pilot proving & controlled expansion',
          progress: 68,
          currentPeriod: 'Apr–Jun 2026',
          nextMilestone: 'Safety drill closeout & scale recommendation',
          needSupportPic: '',
          roadmap: [
            {
              period: 'Jan–Mar 2026',
              phase: 'Infrastructure & technical proving',
              status: 'done',
              tasks: [
                { text: 'Validate network backbone and ROC connection stability', owner: 'IT / Automation', status: 'done' },
                { text: 'Complete initial end-to-end system integration test', owner: 'Vendor + Ops', status: 'done' }
              ]
            },
            {
              period: 'Apr–Jun 2026',
              phase: 'Pilot operation & safety proving',
              status: 'progress',
              tasks: [
                { text: 'Run supervised multi-shift pilot scenario', owner: 'Ops', status: 'progress' },
                { text: 'Prepare Gate 3 evidence pack for approval', owner: 'Project Team', status: 'plan' }
              ]
            }
          ],
          gates: [
            {
              gate: 'Gate 1',
              title: 'Technical Feasibility',
              caption: 'Network readiness, stability, and end-to-end integration.',
              hardGate: true,
              metrics: [
                { name: 'Network uptime', desc: 'Live network support for remote operation.', type: 'range', direction: 'high', unit: '%', min: 90, max: 100, step: 0.1, value: 99.1, pass: 98, conditional: 96, critical: false },
                { name: 'Latency', desc: 'Round-trip response delay during operation.', type: 'range', direction: 'low', unit: ' ms', min: 50, max: 400, step: 5, value: 165, pass: 200, conditional: 250, critical: true },
                { name: 'Integration', desc: 'ROC and monitoring end-to-end connection status.', type: 'select', value: 'pass', critical: true }
              ]
            },
            {
              gate: 'Gate 2',
              title: 'Performance / Effectiveness',
              caption: 'Accuracy, intervention quality, and stability across shifts.',
              hardGate: false,
              metrics: [
                { name: 'Precision', desc: 'Correct positive detection quality.', type: 'range', direction: 'high', unit: '%', min: 50, max: 100, step: 1, value: 92, pass: 90, conditional: 85, critical: false },
                { name: 'Recall', desc: 'Detection coverage against true events.', type: 'range', direction: 'high', unit: '%', min: 50, max: 100, step: 1, value: 89, pass: 85, conditional: 80, critical: false }
              ]
            },
            {
              gate: 'Gate 3',
              title: 'Safety Case & Procedure',
              caption: 'Emergency stop, override, SOP, handover, and drill readiness.',
              hardGate: true,
              metrics: [
                { name: 'SOP readiness', desc: 'Procedure and handover governance completion.', type: 'select', value: 'pass', critical: true },
                { name: 'Field drill', desc: 'Simulation and field drill outcome.', type: 'select', value: 'pass', critical: true }
              ]
            },
            {
              gate: 'Gate 4',
              title: 'Business / Assurance',
              caption: 'Exposure reduction, downtime prevention, and scale rationale.',
              hardGate: false,
              metrics: [
                { name: 'Exposure reduction', desc: 'Index of direct exposure removal from hazard zone.', type: 'range', direction: 'high', unit: ' pts', min: 0, max: 100, step: 1, value: 85, pass: 80, conditional: 60, critical: false },
                { name: 'Business case quality', desc: 'Overall economic and assurance justification.', type: 'select', value: 'pass', critical: false }
              ]
            }
          ]
        },
        {
          name: 'MGD PC',
          subtitle: 'Precision-guidance pilot where technical accuracy and field consistency are more critical than raw ROI alone.',
          pilotArea: 'PAMA BMO2',
          support: 'Positioning quality, operator interface, supervised pilot protocol',
          currentPhase: 'Accuracy stabilization',
          progress: 56,
          currentPeriod: 'Apr–Jun 2026',
          nextMilestone: 'Guidance accuracy closeout for broader field test',
          roadmap: [
            {
              period: 'Jan–Feb 2026',
              phase: 'Configuration & setup',
              status: 'done',
              tasks: [
                { text: 'Install positioning and guidance configuration', owner: 'Vendor', status: 'done' },
                { text: 'Validate data logging and field test protocol', owner: 'Data Team', status: 'done' },
                { text: 'Train pilot supervisors on assisted workflow', owner: 'Ops', status: 'done' }
              ]
            },
            {
              period: 'Mar–Jun 2026',
              phase: 'Pilot proving',
              status: 'progress',
              tasks: [
                { text: 'Improve guidance accuracy at variable work fronts', owner: 'Vendor + Ops', status: 'progress' },
                { text: 'Monitor user adoption and coaching needs', owner: 'Supervisor', status: 'progress' },
                { text: 'Review rework reduction evidence by shift', owner: 'Project Analyst', status: 'plan' }
              ]
            }
          ],
          gates: [
            {
              gate: 'Gate 1',
              title: 'Technical Feasibility',
              caption: 'Sensor reliability, positioning accuracy, and data logging.',
              hardGate: true,
              metrics: [
                { name: 'Position stability', desc: 'Stability of location/guidance reference.', type: 'range', direction: 'high', unit: '%', min: 70, max: 100, step: 1, value: 96, pass: 95, conditional: 90, critical: false },
                { name: 'Data completeness', desc: 'Availability of pilot evidence for analysis.', type: 'range', direction: 'high', unit: '%', min: 70, max: 100, step: 1, value: 95, pass: 95, conditional: 90, critical: false },
                { name: 'System uptime', desc: 'Application and field support uptime.', type: 'range', direction: 'high', unit: '%', min: 80, max: 100, step: 0.1, value: 97.4, pass: 97, conditional: 95, critical: true }
              ]
            },
            {
              gate: 'Gate 2',
              title: 'Performance / Effectiveness',
              caption: 'Guidance accuracy, error reduction, and operational acceptance.',
              hardGate: false,
              metrics: [
                { name: 'Guidance accuracy', desc: 'Accuracy of field guidance versus target.', type: 'range', direction: 'high', unit: '%', min: 60, max: 100, step: 1, value: 86, pass: 90, conditional: 85, critical: false },
                { name: 'Rework reduction', desc: 'Reduction in repeated work or adjustment.', type: 'range', direction: 'high', unit: '%', min: 0, max: 20, step: 1, value: 6, pass: 8, conditional: 4, critical: false },
                { name: 'User acceptance', desc: 'Supervisor/operator willingness to use the solution.', type: 'range', direction: 'high', unit: '%', min: 0, max: 100, step: 1, value: 72, pass: 80, conditional: 70, critical: false }
              ]
            },
            {
              gate: 'Gate 3',
              title: 'Safety Case & Procedure',
              caption: 'Manual fallback, override logic, and safe exception handling.',
              hardGate: true,
              metrics: [
                { name: 'Override readiness', desc: 'Manual fallback and override usability.', type: 'select', value: 'pass', critical: true },
                { name: 'Pilot SOP', desc: 'Approved procedure for assisted/manual operation.', type: 'select', value: 'pass', critical: true },
                { name: 'Critical risk closure', desc: 'Closure of open major safety issues.', type: 'range', direction: 'high', unit: '%', min: 0, max: 100, step: 1, value: 100, pass: 100, conditional: 90, critical: true }
              ]
            },
            {
              gate: 'Gate 4',
              title: 'Business / Assurance',
              caption: 'Operational efficiency, waste reduction, and quality improvement.',
              hardGate: false,
              metrics: [
                { name: 'Cycle benefit', desc: 'Cycle improvement from assisted guidance.', type: 'range', direction: 'high', unit: '%', min: 0, max: 20, step: 1, value: 6, pass: 8, conditional: 5, critical: false },
                { name: 'Waste / rework gain', desc: 'Reduction in avoidable quality loss.', type: 'range', direction: 'high', unit: '%', min: 0, max: 20, step: 1, value: 5, pass: 7, conditional: 4, critical: false },
                { name: 'Scale rationale', desc: 'Readiness to move beyond proof-of-concept stage.', type: 'select', value: 'conditional', critical: false }
              ]
            }
          ]
        },
        {
          name: 'Remote Pump',
          subtitle: 'Remote operation pilot emphasizing interlock integrity, fail-safe response, and exposure reduction in pump areas.',
          pilotArea: 'BC SMO - ACI, BC BMO1 - PMO',
          support: '5G / telemetry network, PLC integration, remote command governance',
          currentPhase: 'Functional validation & availability proving',
          progress: 73,
          currentPeriod: 'Apr–Jul 2026',
          nextMilestone: 'Interlock sign-off and rollout readiness',
          roadmap: [
            {
              period: 'Jan–Mar 2026',
              phase: 'Remote control commissioning',
              status: 'done',
              tasks: [
                { text: 'Commission telemetry and command loop', owner: 'Automation', status: 'done' },
                { text: 'Validate remote signal reliability', owner: 'IT / Vendor', status: 'done' },
                { text: 'Prepare pump isolation and takeover workflow', owner: 'Maintenance', status: 'done' }
              ]
            },
            {
              period: 'Apr–Jul 2026',
              phase: 'Operational pilot and fail-safe proving',
              status: 'progress',
              tasks: [
                { text: 'Complete fail-safe loss-of-signal scenarios', owner: 'Automation + OHS', status: 'progress' },
                { text: 'Track downtime reduction against baseline', owner: 'Project Analyst', status: 'progress' },
                { text: 'Document local override discipline by area', owner: 'Maintenance', status: 'plan' }
              ]
            }
          ],
          gates: [
            {
              gate: 'Gate 1',
              title: 'Technical Feasibility',
              caption: 'Command success, telemetry completeness, and communication stability.',
              hardGate: true,
              metrics: [
                { name: 'Command success rate', desc: 'Success of issued remote command.', type: 'range', direction: 'high', unit: '%', min: 80, max: 100, step: 0.1, value: 98.8, pass: 98, conditional: 95, critical: true },
                { name: 'Telemetry completeness', desc: 'Availability of remote feedback and audit data.', type: 'range', direction: 'high', unit: '%', min: 70, max: 100, step: 1, value: 97, pass: 95, conditional: 90, critical: false },
                { name: 'Communication uptime', desc: 'Reliability of communication channel.', type: 'range', direction: 'high', unit: '%', min: 80, max: 100, step: 0.1, value: 99, pass: 98, conditional: 96, critical: true }
              ]
            },
            {
              gate: 'Gate 2',
              title: 'Performance / Effectiveness',
              caption: 'Availability, remote-control reliability, and downtime impact.',
              hardGate: false,
              metrics: [
                { name: 'Remote availability', desc: 'Ability to operate remotely when needed.', type: 'range', direction: 'high', unit: '%', min: 50, max: 100, step: 1, value: 91, pass: 90, conditional: 85, critical: false },
                { name: 'Response latency', desc: 'Control response time under operating condition.', type: 'range', direction: 'low', unit: ' ms', min: 50, max: 400, step: 5, value: 180, pass: 200, conditional: 250, critical: false },
                { name: 'Downtime reduction', desc: 'Reduction in event-driven downtime.', type: 'range', direction: 'high', unit: '%', min: 0, max: 30, step: 1, value: 11, pass: 10, conditional: 5, critical: false }
              ]
            },
            {
              gate: 'Gate 3',
              title: 'Safety Case & Procedure',
              caption: 'Interlock, local override, fail-safe loss-of-signal logic.',
              hardGate: true,
              metrics: [
                { name: 'Safety interlock', desc: 'Integrity of remote command permissive chain.', type: 'select', value: 'pass', critical: true },
                { name: 'Loss-of-signal fail-safe', desc: 'Safe fallback when signal is lost.', type: 'select', value: 'pass', critical: true },
                { name: 'Isolation procedure', desc: 'LOTO and local takeover procedure readiness.', type: 'select', value: 'pass', critical: true }
              ]
            },
            {
              gate: 'Gate 4',
              title: 'Business / Assurance',
              caption: 'Travel exposure reduction, avoided delays, and operating value.',
              hardGate: false,
              metrics: [
                { name: 'Travel exposure reduction', desc: 'Reduction in personnel movement to pump area.', type: 'range', direction: 'high', unit: '%', min: 0, max: 40, step: 1, value: 18, pass: 15, conditional: 8, critical: false },
                { name: 'Delay prevention', desc: 'Avoided delay from remote intervention.', type: 'range', direction: 'high', unit: '%', min: 0, max: 20, step: 1, value: 11, pass: 10, conditional: 5, critical: false },
                { name: 'Economic case', desc: 'Scale case from avoided loss and assurance benefit.', type: 'select', value: 'pass', critical: false }
              ]
            }
          ]
        },
        {
          name: 'Hauling Fleet Management',
          subtitle: 'Optimization pilot where dispatch quality, queue reduction, and utilization improvement drive the business case.',
          pilotArea: 'MTL BMO1',
          support: 'Fleet data pipeline, dispatch integration, performance dashboard',
          currentPhase: 'Optimization scaling',
          progress: 79,
          currentPeriod: 'Apr–Aug 2026',
          nextMilestone: 'Portfolio-scale dispatch recommendation',
          roadmap: [
            {
              period: 'Jan–Mar 2026',
              phase: 'Data and dashboard stabilization',
              status: 'done',
              tasks: [
                { text: 'Stabilize fleet data ingestion pipeline', owner: 'Data Eng', status: 'done' },
                { text: 'Launch dispatcher dashboard and KPI baseline', owner: 'Analytics', status: 'done' },
                { text: 'Validate refresh cycle for operational use', owner: 'Ops Control', status: 'done' }
              ]
            },
            {
              period: 'Apr–Aug 2026',
              phase: 'Optimization proving',
              status: 'progress',
              tasks: [
                { text: 'Track queue and idle reduction by shift', owner: 'Dispatcher', status: 'progress' },
                { text: 'Improve user adoption across supervisors', owner: 'Ops', status: 'progress' },
                { text: 'Lock fuel and utilization uplift evidence', owner: 'Project Analyst', status: 'plan' }
              ]
            }
          ],
          gates: [
            {
              gate: 'Gate 1',
              title: 'Technical Feasibility',
              caption: 'Data ingestion, GPS continuity, dashboard uptime, and integration.',
              hardGate: true,
              metrics: [
                { name: 'Data ingestion', desc: 'Consistency of live fleet data ingestion.', type: 'range', direction: 'high', unit: '%', min: 70, max: 100, step: 1, value: 97, pass: 95, conditional: 90, critical: false },
                { name: 'Dashboard uptime', desc: 'Operational dashboard availability.', type: 'range', direction: 'high', unit: '%', min: 80, max: 100, step: 0.1, value: 99.2, pass: 98, conditional: 96, critical: true },
                { name: 'Refresh cycle', desc: 'Minutes needed for usable data refresh.', type: 'range', direction: 'low', unit: ' min', min: 1, max: 60, step: 1, value: 5, pass: 10, conditional: 20, critical: false }
              ]
            },
            {
              gate: 'Gate 2',
              title: 'Performance / Effectiveness',
              caption: 'Queue time, idle time, and cycle optimization effectiveness.',
              hardGate: false,
              metrics: [
                { name: 'Queue time reduction', desc: 'Reduction in waiting queue for loading/unloading.', type: 'range', direction: 'high', unit: '%', min: 0, max: 30, step: 1, value: 14, pass: 10, conditional: 5, critical: false },
                { name: 'Idle reduction', desc: 'Reduction in idle or non-productive unit time.', type: 'range', direction: 'high', unit: '%', min: 0, max: 20, step: 1, value: 10, pass: 8, conditional: 4, critical: false },
                { name: 'User adoption', desc: 'Dispatcher and supervisor usage level.', type: 'range', direction: 'high', unit: '%', min: 0, max: 100, step: 1, value: 89, pass: 85, conditional: 75, critical: false }
              ]
            },
            {
              gate: 'Gate 3',
              title: 'Safety Case & Procedure',
              caption: 'Override, rule compliance, and alignment with road safety logic.',
              hardGate: true,
              metrics: [
                { name: 'Safety conflict check', desc: 'No contradiction with road-rule protections.', type: 'select', value: 'pass', critical: true },
                { name: 'Audit trail', desc: 'Decision traceability and reviewability.', type: 'select', value: 'pass', critical: false },
                { name: 'Escalation path', desc: 'Manual override and escalation remain available.', type: 'select', value: 'pass', critical: true }
              ]
            },
            {
              gate: 'Gate 4',
              title: 'Business / Assurance',
              caption: 'Fuel efficiency, utilization, and dispatch-led value creation.',
              hardGate: false,
              metrics: [
                { name: 'Fuel benefit', desc: 'Improvement in fuel-related operating value.', type: 'range', direction: 'high', unit: '%', min: 0, max: 15, step: 1, value: 6, pass: 5, conditional: 2, critical: false },
                { name: 'Utilization uplift', desc: 'Increase in productive fleet use.', type: 'range', direction: 'high', unit: '%', min: 0, max: 20, step: 1, value: 9, pass: 7, conditional: 4, critical: false },
                { name: 'Scale economics', desc: 'Strength of economic case for wider deployment.', type: 'select', value: 'pass', critical: false }
              ]
            }
          ]
        },
        {
          name: 'Mining Eyes Analytics',
          subtitle: 'AI surveillance and analytics pilot where assurance value can be as important as direct cash ROI.',
          pilotArea: 'PAMA BMO2, PAMA GMO, MTN SMO',
          support: '5G infrastructure, cloud, surveillance dashboard (HRZ)',
          currentPhase: 'Coverage improvement & workflow hardening',
          progress: 61,
          currentPeriod: 'Apr–Jul 2026',
          nextMilestone: 'False positive reduction and closure workflow maturity',
          roadmap: [
            {
              period: 'Jan–Mar 2026',
              phase: 'Camera and analytics setup',
              status: 'done',
              tasks: [
                { text: 'Deploy camera integration and basic inference flow', owner: 'Vendor', status: 'done' },
                { text: 'Stand up surveillance dashboard and alert feed', owner: 'IT / Analytics', status: 'done' },
                { text: 'Define escalation owners and verification workflow', owner: 'OHS', status: 'done' }
              ]
            },
            {
              period: 'Apr–Jul 2026',
              phase: 'Model and workflow improvement',
              status: 'progress',
              tasks: [
                { text: 'Reduce false positives at priority use cases', owner: 'AI Team', status: 'progress' },
                { text: 'Improve closure discipline and evidence linkage', owner: 'Ops + OHS', status: 'progress' },
                { text: 'Strengthen coverage in unstable network areas', owner: 'IT', status: 'risk' }
              ]
            }
          ],
          gates: [
            {
              gate: 'Gate 1',
              title: 'Technical Feasibility',
              caption: 'Camera uptime, inference flow, bandwidth, and stream stability.',
              hardGate: true,
              metrics: [
                { name: 'Camera uptime', desc: 'Video source availability for analytics.', type: 'range', direction: 'high', unit: '%', min: 70, max: 100, step: 0.1, value: 96.5, pass: 97, conditional: 95, critical: false },
                { name: 'Inference latency', desc: 'Speed from event to generated alert.', type: 'range', direction: 'low', unit: ' ms', min: 50, max: 700, step: 10, value: 220, pass: 250, conditional: 350, critical: false },
                { name: 'Coverage stability', desc: 'Consistency of effective area coverage.', type: 'range', direction: 'high', unit: '%', min: 0, max: 100, step: 1, value: 82, pass: 85, conditional: 75, critical: true }
              ]
            },
            {
              gate: 'Gate 2',
              title: 'Performance / Effectiveness',
              caption: 'Precision, recall, alert usefulness, and missed-event reduction.',
              hardGate: false,
              metrics: [
                { name: 'Precision', desc: 'Alert correctness rate.', type: 'range', direction: 'high', unit: '%', min: 50, max: 100, step: 1, value: 84, pass: 90, conditional: 80, critical: false },
                { name: 'Recall', desc: 'Ability to capture true events.', type: 'range', direction: 'high', unit: '%', min: 50, max: 100, step: 1, value: 78, pass: 85, conditional: 75, critical: false },
                { name: 'False positive rate', desc: 'Lower rate reduces alert fatigue.', type: 'range', direction: 'low', unit: '/hr', min: 0, max: 1, step: 0.01, value: 0.45, pass: 0.25, conditional: 0.45, critical: false }
              ]
            },
            {
              gate: 'Gate 3',
              title: 'Safety Case & Procedure',
              caption: 'Alert escalation, role clarity, verification workflow, and evidence retention.',
              hardGate: true,
              metrics: [
                { name: 'Escalation SOP', desc: 'Owner and response flow for incoming alerts.', type: 'select', value: 'pass', critical: true },
                { name: 'Audit trail', desc: 'Evidence trace and alert history reviewability.', type: 'select', value: 'pass', critical: true },
                { name: 'Closure workflow', desc: 'Quality of action closure discipline.', type: 'range', direction: 'high', unit: '%', min: 0, max: 100, step: 1, value: 72, pass: 80, conditional: 70, critical: false }
              ]
            },
            {
              gate: 'Gate 4',
              title: 'Business / Assurance',
              caption: 'Monitoring coverage, assurance value, and reduction of missed critical events.',
              hardGate: false,
              metrics: [
                { name: 'Coverage uplift', desc: 'Increase in monitored area or observation capability.', type: 'range', direction: 'high', unit: '%', min: 0, max: 50, step: 1, value: 31, pass: 25, conditional: 15, critical: false },
                { name: 'Manual review reduction', desc: 'Potential reduction in CCTV review effort.', type: 'range', direction: 'high', unit: '%', min: 0, max: 40, step: 1, value: 18, pass: 20, conditional: 10, critical: false },
                { name: 'Assurance case', desc: 'Strength of visibility and auditability justification.', type: 'select', value: 'pass', critical: false }
              ]
            }
          ]
        },
        {
          name: 'Remote Dozer',
          subtitle: 'Remote dozer pilot with strong linkage between technical readiness, safe remote operation, and direct economical value from fuel and efficiency improvement.',
          pilotArea: 'Kalimantan OB Disposal',
          support: '5G network, remote control station, video telemetry, autonomous zone & dashboard',
          currentPhase: 'Integrated readiness and economic proving',
          progress: 74,
          currentPeriod: 'Apr–Aug 2026',
          nextMilestone: 'Technical + economic pack for scale-up decision',
          roadmap: [
            {
              period: 'Jan–Mar 2026',
              phase: 'Remote system setup',
              status: 'done',
              tasks: [
                { text: 'Complete network and control station setup', owner: 'Automation', status: 'done' },
                { text: 'Define remote zone boundary and procedure', owner: 'OHS', status: 'done' }
              ]
            },
            {
              period: 'Apr–Aug 2026',
              phase: 'Pilot run and economic proof',
              status: 'progress',
              tasks: [
                { text: 'Track dozing distance reduction and remote availability', owner: 'Ops', status: 'progress' },
                { text: 'Refresh payback and ROI assumption model', owner: 'Project Analyst', status: 'progress' }
              ]
            }
          ],
          gates: [
            {
              gate: 'Gate 1',
              title: 'Technical Feasibility',
              caption: 'Network readiness, command latency, and telemetry stability for remote dozing.',
              hardGate: true,
              metrics: [
                { name: 'Network uptime', desc: 'Availability of network used for remote dozer control.', type: 'range', direction: 'high', unit: '%', min: 80, max: 100, step: 0.1, value: 99.0, pass: 98, conditional: 96, critical: true },
                { name: 'Control latency', desc: 'Round-trip command latency during remote operation.', type: 'range', direction: 'low', unit: ' ms', min: 50, max: 400, step: 5, value: 170, pass: 200, conditional: 250, critical: true }
              ]
            },
            {
              gate: 'Gate 2',
              title: 'Performance / Effectiveness',
              caption: 'Distance reduction, remote availability, and fuel-saving effectiveness.',
              hardGate: false,
              metrics: [
                { name: 'Dozing distance reduction', desc: 'Reduction in average push distance versus conventional operation.', type: 'range', direction: 'high', unit: '%', min: 0, max: 100, step: 1, value: 75, pass: 60, conditional: 40, critical: false },
                { name: 'Remote operating availability', desc: 'Percentage of planned operating time executed in remote mode.', type: 'range', direction: 'high', unit: '%', min: 40, max: 100, step: 1, value: 91, pass: 85, conditional: 75, critical: false }
              ]
            },
            {
              gate: 'Gate 3',
              title: 'Safety Case & Procedure',
              caption: 'Emergency stop, fail-safe logic, zone control, and remote handover readiness.',
              hardGate: true,
              metrics: [
                { name: 'Emergency stop readiness', desc: 'Remote and local emergency stop function.', type: 'select', value: 'pass', critical: true },
                { name: 'Loss-of-signal fail-safe', desc: 'Machine response when communication is interrupted.', type: 'select', value: 'pass', critical: true }
              ]
            },
            {
              gate: 'Gate 4',
              title: 'Business / Economical Pass',
              caption: 'Net saving, payback, and long-term ROI from remote dozer deployment.',
              hardGate: false,
              metrics: [
                { name: 'Net saving / month', desc: 'Monthly net saving after fuel saving and maintenance impact.', type: 'range', direction: 'high', unit: ' M IDR', min: -100, max: 300, step: 1, value: 165, pass: 100, conditional: 40, critical: false },
                { name: 'Simple payback', desc: 'Capital recovery period. Lower is better.', type: 'range', direction: 'low', unit: ' mo', min: 1, max: 120, step: 1, value: 21, pass: 36, conditional: 60, critical: false },
                { name: '5-year ROI', desc: 'Return on investment over five years after CAPEX recovery.', type: 'range', direction: 'high', unit: '%', min: -100, max: 400, step: 1, value: 183, pass: 50, conditional: 0, critical: false }
              ]
            }
          ]
        }
      ];

      const projects = [];

      function restoreSeedProjects() {
        projects.length = 0;
        JSON.parse(JSON.stringify(seedProjects)).forEach(function (project) { projects.push(project); });
        historySnapshots = [];
        expandedProjects = new Set([0]);
        expandedDashboardProjects = new Set([0]);
      }

      function newTask() {
        return {
          text: 'New task item',
          owner: 'Owner',
          status: 'plan',
          originalOwner: 'Owner',
          originalStatus: 'plan',
          picActualOwner: '',
          picStartDate: '',
          picActualPercent: null,
          picProgressNote: '',
          evidenceLink: '',
          targetDate: '',
          dependencyBlocker: '',
          taskProgressPercentNormalized: null
        };
      }

      function newPeriod() {
        return {
          displayCurrentPeriod: '',
          period: 'New Period',
          phase: 'New phase',
          status: 'plan',
          periodExplanation: '',
          plannedObjectiveOutcome: '',
          picUpdateSummary: '',
          picRisksDependencies: '',
          picOwner: '',
          targetDate: '',
          reviewerStatus: '',
          periodProgressPercent: null,
          tasks: [newTask()]
        };
      }

      function newMetric() {
        return {
          name: 'New metric',
          desc: 'Describe the metric logic',
          type: 'range',
          direction: 'high',
          unit: '%',
          min: 0,
          max: 100,
          step: 1,
          value: 50,
          pass: 80,
          conditional: 60,
          critical: false,
          picCurrentFinding: '',
          picEvidenceSource: '',
          picComment: '',
          metricStatus: ''
        };
      }

      function newGate(idx) {
        return {
          gate: 'Gate ' + (idx + 1),
          title: 'New Gate',
          caption: 'Describe the purpose of this gate.',
          hardGate: false,
          gateDefinition: '',
          projectSpecificExplanation: '',
          whatGateConfirms: '',
          whatPicNeedsToFill: '',
          picStatus: '',
          picNotesKeyFindings: '',
          evidenceLinkFolder: '',
          picOwner: '',
          targetCloseDate: '',
          reviewerStatus: '',
          metrics: [newMetric()]
        };
      }

      function newProject(idx) {
        return {
          name: 'New Project ' + (idx + 1),
          subtitle: 'Describe the project objective and scope.',
          pilotArea: 'Pilot Area',
          support: 'Support requirement',
          currentPhase: 'New phase',
          progress: 0,
          currentPeriod: 'Current period',
          nextMilestone: 'Next milestone',
          needSupportPic: '',
          roadmap: [newPeriod()],
          gates: [newGate(0), newGate(1), newGate(2), newGate(3)]
        };
      }

      function decisionStrengthFromStatus(status) {
        const normalized = String(status || '').trim().toLowerCase();
        if (normalized === 'go' || normalized === 'pass') return 100;
        if (normalized === 'conditional go' || normalized === 'conditional') return 70;
        if (normalized === 'no-go' || normalized === 'nogo' || normalized === 'fail') return 35;
        return 70;
      }

      function normalizeMetricType(value) {
        return String(value || '').trim().toLowerCase() === 'select' ? 'select' : 'range';
      }

      function normalizeGateDecisionValue(value) {
        const v = String(value || '').trim().toLowerCase();
        if (v === 'go' || v === 'pass') return 'pass';
        if (v === 'conditional' || v === 'conditional go' || v === 'conditional_go') return 'conditional';
        if (v === 'no-go' || v === 'nogo' || v === 'fail') return 'fail';
        return 'conditional';
      }

      function normalizeTaskStatus(value) {
        const v = String(value || '').trim().toLowerCase();
        if (v === 'done' || v === 'complete' || v === 'completed') return 'done';
        if (v === 'progress' || v === 'in progress' || v === 'ongoing') return 'progress';
        if (v === 'risk' || v === 'issue') return 'risk';
        return 'plan';
      }

      function parseBool(value) {
        const v = String(value == null ? '' : value).trim().toLowerCase();
        return v === 'true' || v === '1' || v === 'yes' || v === 'y';
      }

      function parseLocaleNumber(value) {
        if (value === '' || value == null) return NaN;
        if (typeof value === 'number' && Number.isFinite(value)) return value;
        var s = String(value).replace(/\u00A0/g, '').replace(/%/g, '').trim();
        if (!s) return NaN;
        if (s.indexOf(',') !== -1 && s.indexOf('.') === -1) s = s.replace(',', '.');
        else if (s.indexOf(',') !== -1 && s.indexOf('.') !== -1) s = s.replace(/,/g, '');
        var n = Number(s);
        return Number.isFinite(n) ? n : NaN;
      }

      function parseNumber(value, fallback) {
        var n = parseLocaleNumber(value);
        return Number.isFinite(n) ? n : fallback;
      }

      function nullableProgressPercent(value) {
        var n = parseLocaleNumber(value);
        if (!Number.isFinite(n)) return null;
        return Math.max(0, Math.min(100, Math.round(n * 100) / 100));
      }

      function parseOptionalDateStr(value) {
        var s = String(value || '').trim();
        if (!s) return '';
        var d = Date.parse(s);
        if (!Number.isFinite(d)) return '';
        try {
          var dt = new Date(d);
          return dt.toISOString().slice(0, 10);
        } catch (e) { return ''; }
      }

      function normalizeHeader(key) {
        var k = String(key || '').trim().toLowerCase();
        k = k.replace(/%/g, '').replace(/[()]/g, '');
        k = k.replace(/[\s\-\/]+/g, '_');
        k = k.replace(/_+/g, '_').replace(/^_|_$/g, '');
        return k;
      }

      function normalizeRow(row) {
        const normalized = {};
        Object.keys(row || {}).forEach(function (key) {
          normalized[normalizeHeader(key)] = row[key];
        });
        return normalized;
      }

      function csvEscape(value) {
        const text = String(value == null ? '' : value);
        if (/[",\n]/.test(text)) return '"' + text.replace(/"/g, '""') + '"';
        return text;
      }

      function buildCsv(rows) {
        if (!rows.length) return '';
        const headers = Object.keys(rows[0]);
        const body = rows.map(function (row) {
          return headers.map(function (header) { return csvEscape(row[header]); }).join(',');
        });
        return headers.join(',') + '\n' + body.join('\n');
      }

      function downloadText(filename, text) {
        const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
      }

      function maybeReadSheetRows(workbook, sheetName) {
        if (!workbook || !window.XLSX || !workbook.Sheets || !workbook.Sheets[sheetName]) return [];
        return window.XLSX.utils.sheet_to_json(workbook.Sheets[sheetName], { defval: '' }).map(normalizeRow);
      }

      function ensureMetricShape(metric) {
        if (metric.type === 'select') {
          metric.value = ['pass', 'conditional', 'fail'].indexOf(metric.value) >= 0 ? metric.value : 'conditional';
        } else {
          metric.direction = metric.direction || 'high';
          metric.unit = metric.unit == null ? '%' : metric.unit;
          metric.min = Number.isFinite(+metric.min) ? +metric.min : 0;
          metric.max = Number.isFinite(+metric.max) ? +metric.max : 100;
          metric.step = Number.isFinite(+metric.step) ? +metric.step : 1;
          metric.value = Number.isFinite(+metric.value) ? +metric.value : 50;
          metric.pass = Number.isFinite(+metric.pass) ? +metric.pass : 80;
          metric.conditional = Number.isFinite(+metric.conditional) ? +metric.conditional : 60;
        }
        return metric;
      }

      function evaluateMetric(metric) {
        ensureMetricShape(metric);
        let status = 'conditional';
        if (metric.type === 'select') {
          status = metric.value || 'conditional';
        } else if (metric.direction === 'high') {
          if (+metric.value >= +metric.pass) status = 'pass';
          else if (+metric.value >= +metric.conditional) status = 'conditional';
          else status = 'fail';
        } else {
          if (+metric.value <= +metric.pass) status = 'pass';
          else if (+metric.value <= +metric.conditional) status = 'conditional';
          else status = 'fail';
        }
        return { status: status, score: SCORE_MAP[status], critical: !!metric.critical };
      }

      function evaluateGate(gate) {
        const metricResults = gate.metrics.map(evaluateMetric);
        const score = metricResults.length ? Math.round(metricResults.reduce(function (a, b) { return a + b.score; }, 0) / metricResults.length) : 0;
        const failCount = metricResults.filter(function (x) { return x.status === 'fail'; }).length;
        const criticalFail = metricResults.some(function (x) { return x.status === 'fail' && x.critical; });
        let status = 'conditional';
        if (gate.hardGate && criticalFail) status = 'fail';
        else if (score >= 80 && failCount === 0) status = 'pass';
        else if (score >= 65 && failCount <= 1) status = 'conditional';
        else status = 'fail';
        return { status: status, score: score, metricResults: metricResults, failCount: failCount, criticalFail: criticalFail };
      }

      function overallDecision(project) {
        const gateResults = project.gates.map(evaluateGate);
        const weighted = project.gates.length ? Math.round(gateResults.reduce(function (sum, gate, idx) {
          return sum + gate.score * (GATE_WEIGHTS[idx] || 20);
        }, 0) / 100) : 0;
        const hardFail = !!(gateResults[0] && gateResults[0].status === 'fail') || !!(gateResults[2] && gateResults[2].status === 'fail');
        const anyFail = gateResults.some(function (x) { return x.status === 'fail'; });
        let status = 'CONDITIONAL GO';
        let note = 'Scale-up can continue in a controlled manner with closure actions.';
        if (hardFail || weighted < 65) {
          status = 'NO-GO';
          note = hardFail ? 'A hard gate is failing.' : 'Overall weighted score is below minimum threshold.';
        } else if (!anyFail && gateResults[0] && gateResults[2] && gateResults[0].status === 'pass' && gateResults[2].status === 'pass' && weighted >= 80) {
          status = 'GO';
          note = 'All critical gates are healthy and the project is scale-ready.';
        }
        return { status: status, score: weighted, note: note, gateResults: gateResults };
      }

      function statusClass(status) {
        const s = String(status).toLowerCase();
        if (s === 'pass') return 'status-pass';
        if (s === 'conditional') return 'status-conditional';
        return 'status-fail';
      }

      function decisionClass(status) {
        if (status === 'GO') return 'decision-go';
        if (status === 'CONDITIONAL GO') return 'decision-conditional';
        return 'decision-nogo';
      }

      function taskStatusClass(status) {
        if (status === 'done') return 'task-done';
        if (status === 'progress') return 'task-progress';
        if (status === 'risk') return 'task-risk';
        return 'task-plan';
      }

      function setImportStatus(title, message, variant) {
        const card = document.getElementById('importStatusCard');
        const titleEl = document.getElementById('importStatusTitle');
        const textEl = document.getElementById('importStatusText');
        if (!card || !titleEl || !textEl) return;
        card.className = ('notice-card ' + (variant || '')).trim();
        titleEl.textContent = title;
        textEl.textContent = message;
      }

      function escapeHtml(value) {
        return String(value == null ? '' : value)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;');
      }

      function attr(value) {
        return escapeHtml(value);
      }

      function parsePeriodDate(label) {
        const text = String(label || '').trim();
        if (!text) return new Date('2100-01-01T00:00:00');
        const direct = new Date(text);
        if (!Number.isNaN(direct.getTime())) return direct;
        const normalized = text.toLowerCase().replace('–', '-').replace('—', '-');
        const months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
        let monthIndex = -1;
        months.forEach(function (month, index) {
          if (monthIndex === -1 && normalized.indexOf(month) >= 0) monthIndex = index;
        });
        const yearMatch = normalized.match(/[0-9]{4}/);
        if (yearMatch) return new Date(Number(yearMatch[0]), monthIndex >= 0 ? monthIndex : 0, 1);
        const yearMonthMatch = normalized.match(/[0-9]{4}[\/-][0-9]{1,2}/);
        if (yearMonthMatch) {
          const parts = yearMonthMatch[0].replace('/', '-').split('-');
          return new Date(Number(parts[0]), Math.max(0, Number(parts[1]) - 1), 1);
        }
        return new Date('2100-01-01T00:00:00');
      }

      function normalizeProjectName(name) {
        return String(name || '').trim().toLowerCase();
      }

      function sortPeriodLabels(labels) {
        return labels.slice().sort(function (a, b) {
          return parsePeriodDate(a) - parsePeriodDate(b);
        });
      }

      function aggregateSeriesPoints(rows) {
        const grouped = new Map();
        rows.forEach(function (row) {
          if (!grouped.has(row.label)) grouped.set(row.label, { label: row.label, progressSum: 0, decisionSum: 0, count: 0 });
          const bucket = grouped.get(row.label);
          bucket.progressSum += row.progress;
          bucket.decisionSum += row.decision;
          bucket.count += 1;
        });
        return sortPeriodLabels(Array.from(grouped.keys())).map(function (label) {
          const item = grouped.get(label);
          return {
            label: label,
            progress: item.count ? item.progressSum / item.count : 0,
            decision: item.count ? item.decisionSum / item.count : 0
          };
        });
      }

      function buildDerivedProjectCurve(project) {
        const roadmap = (project.roadmap || []).slice().sort(function (a, b) {
          return parsePeriodDate(a.period) - parsePeriodDate(b.period);
        });
        const periods = roadmap.length ? roadmap : [{ period: project.currentPeriod || 'Current Period', phase: project.currentPhase || 'Current Phase', status: 'progress' }];
        const currentDecision = decisionStrengthFromStatus(overallDecision(project).status);
        const count = Math.max(periods.length, 1);
        return periods.map(function (period, index) {
          const ratio = (index + 1) / count;
          return {
            label: period.period || ('Period ' + (index + 1)),
            progress: Math.round(project.progress * ratio),
            decision: Math.round(currentDecision * ratio)
          };
        });
      }

      function getProjectCurveData(project) {
        const projectHistory = historySnapshots.filter(function (item) {
          return normalizeProjectName(item.projectName) === normalizeProjectName(project.name);
        }).map(function (item) {
          return { label: item.date, progress: item.progress, decision: item.decisionScore };
        });
        const points = projectHistory.length ? aggregateSeriesPoints(projectHistory) : buildDerivedProjectCurve(project);
        return {
          mode: projectHistory.length ? 'history' : 'derived',
          points: points
        };
      }

      function buildPortfolioFromProjectCurves(seriesList) {
        const labelSet = new Set();
        seriesList.forEach(function (series) {
          series.points.forEach(function (point) { labelSet.add(point.label); });
        });
        const orderedLabels = sortPeriodLabels(Array.from(labelSet));
        return orderedLabels.map(function (label) {
          const currentDate = parsePeriodDate(label);
          let progressSum = 0;
          let decisionSum = 0;
          let count = 0;
          seriesList.forEach(function (series) {
            let latestPoint = null;
            series.points.forEach(function (point) {
              if (parsePeriodDate(point.label) <= currentDate) latestPoint = point;
            });
            if (latestPoint) {
              progressSum += latestPoint.progress;
              decisionSum += latestPoint.decision;
              count += 1;
            }
          });
          return {
            label: label,
            progress: count ? progressSum / count : 0,
            decision: count ? decisionSum / count : 0
          };
        });
      }

      function getCurveData() {
        const projectSeries = projects.map(function (project) {
          return { project: project.name, points: getProjectCurveData(project).points };
        }).filter(function (series) {
          return series.points.length;
        });
        return {
          mode: historySnapshots.length ? 'history' : 'derived',
          points: buildPortfolioFromProjectCurves(projectSeries)
        };
      }

      function renderCurveSvg(points, options) {
        const opts = options || {};
        if (!points.length) return '';
        const width = opts.width || 1080;
        const height = opts.height || 420;
        const compact = !!opts.compact;
        const left = compact ? 56 : 78;
        const right = compact ? 20 : 28;
        const top = compact ? 18 : 26;
        const bottom = compact ? 52 : 74;
        const innerWidth = width - left - right;
        const innerHeight = height - top - bottom;
        const maxIndex = Math.max(points.length - 1, 1);
        function xPos(index) { return left + innerWidth * index / maxIndex; }
        function yPos(value) { return top + innerHeight - Math.max(0, Math.min(100, value)) / 100 * innerHeight; }
        function buildPath(key) {
          return points.map(function (point, index) {
            return (index === 0 ? 'M' : 'L') + ' ' + xPos(index).toFixed(2) + ' ' + yPos(point[key]).toFixed(2);
          }).join(' ');
        }
        const gridLines = [0, 25, 50, 75, 100].map(function (value) {
          return '<line x1="' + left + '" y1="' + yPos(value) + '" x2="' + (width - right) + '" y2="' + yPos(value) + '" stroke="#d9e3ec" stroke-width="1" />'
            + '<text x="' + (left - 10) + '" y="' + (yPos(value) + 4) + '" fill="#64788b" font-size="' + (compact ? 10 : 11) + '" text-anchor="end">' + value + '%</text>';
        }).join('');
        const xLabels = points.map(function (point, index) {
          return '<text x="' + xPos(index) + '" y="' + (height - (compact ? 12 : 18)) + '" fill="#64788b" font-size="' + (compact ? 10 : 11) + '" text-anchor="middle">' + escapeHtml(point.label) + '</text>';
        }).join('');
        const progressDots = points.map(function (point, index) {
          return '<circle cx="' + xPos(index) + '" cy="' + yPos(point.progress) + '" r="' + (compact ? 3.8 : 4.5) + '" fill="#2e6f99"></circle>';
        }).join('');
        const decisionDots = points.map(function (point, index) {
          return '<circle cx="' + xPos(index) + '" cy="' + yPos(point.decision) + '" r="' + (compact ? 3.8 : 4.5) + '" fill="#d89410"></circle>';
        }).join('');
        return '<svg viewBox="0 0 ' + width + ' ' + height + '" preserveAspectRatio="xMidYMid meet" role="img" aria-label="Progress and decision curve">'
          + gridLines
          + '<line x1="' + left + '" y1="' + top + '" x2="' + left + '" y2="' + (height - bottom) + '" stroke="#b8c7d4" stroke-width="1.4"></line>'
          + '<line x1="' + left + '" y1="' + (height - bottom) + '" x2="' + (width - right) + '" y2="' + (height - bottom) + '" stroke="#b8c7d4" stroke-width="1.4"></line>'
          + '<path d="' + buildPath('progress') + '" fill="none" stroke="#2e6f99" stroke-width="' + (compact ? 3 : 3.5) + '" stroke-linecap="round" stroke-linejoin="round"></path>'
          + '<path d="' + buildPath('decision') + '" fill="none" stroke="#d89410" stroke-width="' + (compact ? 3 : 3.5) + '" stroke-linecap="round" stroke-linejoin="round"></path>'
          + progressDots + decisionDots + xLabels
          + '<text x="' + left + '" y="' + (top - 6) + '" fill="#64788b" font-size="' + (compact ? 10 : 11) + '">0–100%</text>'
          + '</svg>';
      }

      function renderCurveChart() {
        const wrap = document.getElementById('curveChartWrap');
        const note = document.getElementById('curveModeNote');
        if (!wrap || !note) return;
        const curveData = getCurveData();
        const points = curveData.points;
        if (!points.length) {
          note.textContent = 'Tidak ada data periode untuk digambar.';
          wrap.innerHTML = '';
          return;
        }
        note.textContent = curveData.mode === 'history'
          ? 'Mode riwayat: kurva digabung dari sheet HISTORY (urut waktu) untuk semua proyek.'
          : 'Mode turunan: kurva dihitung dari roadmap periode dan progress proyek saat HISTORY belum diisi.';
        wrap.innerHTML = renderCurveSvg(points, { width: 1080, height: 420 });
      }

      function renderProjectCurvePanel(project) {
        const curveData = getProjectCurveData(project);
        if (!curveData.points.length) return '';
        const note = curveData.mode === 'history'
          ? 'Kurva proyek dari data sheet HISTORY.'
          : 'Kurva dihitung dari periode roadmap proyek (mode turunan).';
        return '<div class="mini-card">'
          + '<div class="label">Kurva S proyek per periode</div>'
          + '<div class="helper-text">' + escapeHtml(note) + '</div>'
          + '<div class="curve-svg-wrap project-curve-wrap" style="margin-top:10px;">' + renderCurveSvg(curveData.points, { width: 660, height: 280, compact: true }) + '</div>'
          + '</div>';
      }

      function renderOverview() {
        const summaries = projects.map(overallDecision);
        const goCount = summaries.filter(function (x) { return x.status === 'GO'; }).length;
        const conditionalCount = summaries.filter(function (x) { return x.status === 'CONDITIONAL GO'; }).length;
        const noGoCount = summaries.filter(function (x) { return x.status === 'NO-GO'; }).length;
        const avgProgress = projects.length ? Math.round(projects.reduce(function (sum, p) { return sum + (+p.progress || 0); }, 0) / projects.length) : 0;
        document.getElementById('overviewShell').innerHTML = [
          '<div class="overview-card"><div class="overview-label">Progress portofolio</div><div class="overview-value">' + avgProgress + '%</div><div class="helper-text">Rata-rata progress semua proyek.</div></div>',
          '<div class="overview-card"><div class="overview-label">Proyek GO</div><div class="overview-value">' + goCount + '</div><div class="helper-text">Memenuhi skor terbobot dan gate keras.</div></div>',
          '<div class="overview-card"><div class="overview-label">Proyek conditional</div><div class="overview-value">' + conditionalCount + '</div><div class="helper-text">Lanjut terbatas; masih ada tindakan penutupan.</div></div>',
          '<div class="overview-card"><div class="overview-label">Proyek NO-GO</div><div class="overview-value">' + noGoCount + '</div><div class="helper-text">Terblokir gate keras atau skor di bawah ambang.</div></div>'
        ].join('');
      }

      function renderTaskBlock(period) {
        return '<div class="mini-card">'
          + '<div class="table-actions" style="justify-content:space-between; margin-bottom:8px;">'
          + '<div><div class="label">' + escapeHtml(period.period) + '</div><div class="helper-text">' + escapeHtml(period.phase) + '</div></div>'
          + '<div class="task-status ' + taskStatusClass(period.status) + '">' + escapeHtml(period.status.toUpperCase()) + '</div>'
          + '</div>'
          + '<ul class="task-list">'
          + period.tasks.map(function (task) {
              return '<li class="task-item">'
                + '<span class="task-bullet"></span>'
                + '<span class="task-text">' + escapeHtml(task.text) + '</span>'
                + '<span class="task-owner">' + escapeHtml(task.owner) + ' · <span class="task-status ' + taskStatusClass(task.status) + '">' + escapeHtml(task.status.toUpperCase()) + '</span></span>'
                + '</li>';
            }).join('')
          + '</ul></div>';
      }

      function renderDashboard() {
        const root = document.getElementById('dashboardGrid');
        if (!projects.length) {
          root.innerHTML = '<div class="dashboard-empty">Belum ada data proyek. Tambahkan proyek baru atau impor data dari Excel/Database untuk menampilkan dashboard.</div>';
          return;
        }
        root.innerHTML = projects.map(function (project, pIdx) {
          const overall = overallDecision(project);
          const isOpen = expandedDashboardProjects.has(pIdx);
          const pdfUrl = getProjectPdfUrl(project);
          const gateHtml = project.gates.map(function (gate, gIdx) {
            const gateResult = overall.gateResults[gIdx];
            const metricItems = gate.metrics.map(function (metric, idx) {
              return '<li class="status-mini-item"><span>' + escapeHtml(metric.name) + '</span><span class="metric-status ' + statusClass(gateResult.metricResults[idx].status).replace('status', 'metric') + '">' + escapeHtml(gateResult.metricResults[idx].status.toUpperCase()) + '</span></li>';
            }).join('');
            return '<div class="status-box">'
              + '<div><div class="label">' + escapeHtml(gate.gate) + '</div><h4>' + escapeHtml(gate.title) + '</h4></div>'
              + '<div class="status-chip ' + statusClass(gateResult.status) + '">' + escapeHtml(gateResult.status.toUpperCase()) + '</div>'
              + '<div class="mini-card"><div class="label">Gate Score</div><div class="mini-value">' + gateResult.score + '/100</div></div>'
              + '<ul class="status-mini-list">' + metricItems + '</ul>'
              + '</div>';
          }).join('');
          return '<article class="project-card dashboard-collapsible ' + (isOpen ? 'is-open' : '') + '" id="dashboard-project-' + pIdx + '">'
            + '<div class="dashboard-collapse-head">'
            + '<div class="dashboard-summary"><h3>' + escapeHtml(project.name) + '</h3><p>' + escapeHtml(project.subtitle) + '</p></div>'
            + '<div class="dashboard-meta-stack">'
            + '<div class="mini-card"><div class="label">Overall Progress</div><div class="mini-value">' + project.progress + '%</div></div>'
            + '<div class="mini-card"><div class="label">Overall Decision</div><div class="decision-badge ' + decisionClass(overall.status) + '">' + escapeHtml(overall.status) + '</div></div>'
            + '</div>'
            + '<div class="dashboard-collapse-actions">'
            + (pdfUrl ? '<button class="btn-secondary collapse-toggle" type="button" data-action="open-project-pdf" data-project="' + pIdx + '" title="Lihat PDF proyek">👁</button>' : '')
            + '<button class="btn-secondary collapse-toggle" type="button" data-action="toggle-dashboard-project" data-project="' + pIdx + '">' + (isOpen ? 'Ciutkan' : 'Bentang') + '</button><div class="collapse-chevron">⌄</div></div>'
            + '</div>'
            + (isOpen ? '<div class="dashboard-body">'
              + '<section class="panel"><div class="panel-head"><div class="panel-title">Progress &amp; status gate</div><div class="panel-subtitle">Hanya tampilan monitoring.</div></div><div class="panel-inner">'
              + '<div class="progress-band"><div class="progress-info"><h4>Project Completion</h4><p>Current progress based on the latest values from Input Page.</p></div><div class="mini-card"><div class="label">Progress</div><div class="mini-value">' + project.progress + '%</div></div><div class="mini-card"><div class="label">Program Window</div><div class="mini-value">' + escapeHtml(project.currentPeriod) + '</div></div></div>'
              + '<div class="bar-head"><span>Progress Bar</span><span>' + project.progress + '%</span></div><div class="bar-track"><div class="bar-fill" style="width:' + project.progress + '%">' + project.progress + '%</div></div>'
              + '<div class="status-grid" style="margin-top:14px;">' + gateHtml + '</div></div></section>'
              + '<section class="panel"><div class="panel-head"><div class="panel-title">Kurva periode &amp; timeline</div><div class="panel-subtitle">Kurva proyek dan daftar tugas per periode.</div></div><div class="panel-inner"><div class="project-curve-stack">' + renderProjectCurvePanel(project) + '<div class="task-list" style="gap:12px;">' + project.roadmap.map(renderTaskBlock).join('') + '</div></div></div></section>'
              + '</div>' : '')
            + '</article>';
        }).join('');
      }

      function renderNeedSupportList() {
        const root = document.getElementById('needSupportList');
        if (!root) return;
        if (!projects.length) {
          root.innerHTML = '<div class="curve-value">Belum ada proyek.</div>';
          return;
        }
        root.innerHTML = projects.map(function (project) {
          const support = String(project.support || '').trim();
          return '<div class="curve-box" style="margin-top:10px;">'
            + '<div class="legend-label">' + escapeHtml(project.name || 'Tanpa nama proyek') + '</div>'
            + '<div class="curve-value">' + escapeHtml(support || 'Tidak ada need support') + '</div>'
            + '</div>';
        }).join('');
      }

      function getProjectPdfKey(projectName) {
        const name = String(projectName || '').toLowerCase();
        if (!name) return '';
        if (name.indexOf('arcas') >= 0) return 'arcas';
        if (name.indexOf('mining eyes') >= 0 || name.indexOf('mea') >= 0) return 'mea';
        if (name.indexOf('mgd') >= 0 || name.indexOf('mgc') >= 0) return 'mgc';

        return '';
      }

      function getProjectPdfUrl(project) {
        const cfg = window.PilotProjectValidation || {};
        const template = String(cfg.projectPdfUrlTemplate || '');
        if (!template) return '';
        const key = getProjectPdfKey(project && project.name);
        if (!key) return '';

        return template.replace('__KEY__', encodeURIComponent(key));
      }

      function openProjectPdfModal(projectIdx) {
        const project = projects[projectIdx];
        if (!project) return;
        const pdfUrl = getProjectPdfUrl(project);
        if (!pdfUrl) {
          setImportStatus('PDF belum tersedia', 'Belum ada file PDF yang dipetakan untuk proyek ini.', 'notice-card notice-warning');
          return;
        }
        activeModal = { projectIdx: projectIdx, gateIdx: null, mode: 'pdf' };
        document.getElementById('modalContent').innerHTML = '<section class="modal-hero">'
          + '<div class="premium-label">' + escapeHtml(project.name) + '</div>'
          + '<h2>Dokumen <span>Project PDF</span></h2>'
          + '<p>Pratinjau dokumen PDF proyek.</p>'
          + '</section>'
          + '<section class="modal-section"><div class="modal-section-inner" style="padding-top:16px;">'
          + '<iframe src="' + attr(pdfUrl) + '" title="PDF ' + attr(project.name) + '" style="width:100%;height:70vh;border:1px solid #d9e3ec;border-radius:10px;background:#fff;"></iframe>'
          + '</div></section>';
        document.getElementById('modalOverlay').classList.add('active');
      }

      function renderProjectMasterTable(project, pIdx) {
        return '<div class="table-wrap"><table class="clean-table"><thead><tr>'
          + '<th>Name</th><th>Subtitle</th><th>Pilot Area</th><th>Support</th><th>Current Phase</th><th>Current Period</th><th>Next Milestone</th><th>Progress</th><th>Actions</th>'
          + '</tr></thead><tbody><tr>'
          + '<td><input class="table-input" type="text" value="' + attr(project.name) + '" data-role="project-field" data-project="' + pIdx + '" data-field="name" /></td>'
          + '<td><textarea class="table-textarea" data-role="project-field" data-project="' + pIdx + '" data-field="subtitle">' + escapeHtml(project.subtitle) + '</textarea></td>'
          + '<td><input class="table-input" type="text" value="' + attr(project.pilotArea) + '" data-role="project-field" data-project="' + pIdx + '" data-field="pilotArea" /></td>'
          + '<td><input class="table-input" type="text" value="' + attr(project.support) + '" data-role="project-field" data-project="' + pIdx + '" data-field="support" /></td>'
          + '<td><input class="table-input" type="text" value="' + attr(project.currentPhase) + '" data-role="project-field" data-project="' + pIdx + '" data-field="currentPhase" /></td>'
          + '<td><input class="table-input" type="text" value="' + attr(project.currentPeriod) + '" data-role="project-field" data-project="' + pIdx + '" data-field="currentPeriod" /></td>'
          + '<td><input class="table-input" type="text" value="' + attr(project.nextMilestone) + '" data-role="project-field" data-project="' + pIdx + '" data-field="nextMilestone" /></td>'
          + '<td><div class="compact-stack"><input class="table-range" type="range" min="0" max="100" step="1" value="' + project.progress + '" data-role="project-progress-range" data-project="' + pIdx + '" /><input class="table-number" type="number" min="0" max="100" step="1" value="' + project.progress + '" data-role="project-progress-number" data-project="' + pIdx + '" /></div></td>'
          + '<td><div class="table-actions"><button class="btn-mini btn-secondary" data-action="duplicate-project" data-project="' + pIdx + '">Duplikat</button><button class="btn-mini btn-danger" data-action="remove-project" data-project="' + pIdx + '">Hapus</button></div></td>'
          + '</tr></tbody></table></div>';
      }

      function renderTimelineTable(project, pIdx) {
        let rows = '';
        project.roadmap.forEach(function (period, periodIdx) {
          period.tasks.forEach(function (task, taskIdx) {
            rows += '<tr>'
              + '<td><input class="table-input" type="text" value="' + attr(period.period) + '" data-role="period-field" data-project="' + pIdx + '" data-period="' + periodIdx + '" data-field="period" /></td>'
              + '<td><input class="table-input" type="text" value="' + attr(period.phase) + '" data-role="period-field" data-project="' + pIdx + '" data-period="' + periodIdx + '" data-field="phase" /></td>'
              + '<td>' + selectHtml('period-field', pIdx, periodIdx, taskIdx, 'status', period.status, ['done', 'progress', 'plan', 'risk'], true) + '</td>'
              + '<td><textarea class="table-textarea" data-role="task-field" data-project="' + pIdx + '" data-period="' + periodIdx + '" data-task="' + taskIdx + '" data-field="text">' + escapeHtml(task.text) + '</textarea></td>'
              + '<td><input class="table-input" type="text" value="' + attr(task.owner) + '" data-role="task-field" data-project="' + pIdx + '" data-period="' + periodIdx + '" data-task="' + taskIdx + '" data-field="owner" /></td>'
              + '<td>' + selectHtml('task-field', pIdx, periodIdx, taskIdx, 'status', task.status, ['done', 'progress', 'plan', 'risk'], false) + '</td>'
              + '<td><div class="table-actions"><button class="btn-mini btn-secondary" data-action="add-task" data-project="' + pIdx + '" data-period="' + periodIdx + '">Tambah tugas</button><button class="btn-mini btn-secondary" data-action="add-period" data-project="' + pIdx + '">Tambah periode</button><button class="btn-mini btn-danger" data-action="remove-task" data-project="' + pIdx + '" data-period="' + periodIdx + '" data-task="' + taskIdx + '">Hapus tugas</button><button class="btn-mini btn-danger" data-action="remove-period" data-project="' + pIdx + '" data-period="' + periodIdx + '">Hapus periode</button></div></td>'
              + '</tr>';
          });
        });
        return '<div class="table-wrap"><table class="clean-table"><thead><tr><th>Period</th><th>Phase</th><th>Period Status</th><th>Task</th><th>Owner</th><th>Task Status</th><th>Actions</th></tr></thead><tbody>' + rows + '</tbody></table></div>';
      }

      function renderGateTable(project, pIdx, overall) {
        let rows = '';
        project.gates.forEach(function (gate, gIdx) {
          const gateResult = overall.gateResults[gIdx];
          rows += '<tr>'
            + '<td><input class="table-input" type="text" value="' + attr(gate.gate) + '" data-role="gate-field" data-project="' + pIdx + '" data-gate="' + gIdx + '" data-field="gate" /></td>'
            + '<td><input class="table-input" type="text" value="' + attr(gate.title) + '" data-role="gate-field" data-project="' + pIdx + '" data-gate="' + gIdx + '" data-field="title" /></td>'
            + '<td><textarea class="table-textarea" data-role="gate-field" data-project="' + pIdx + '" data-gate="' + gIdx + '" data-field="caption">' + escapeHtml(gate.caption) + '</textarea></td>'
            + '<td><select class="table-select" data-role="gate-field" data-project="' + pIdx + '" data-gate="' + gIdx + '" data-field="hardGate"><option value="true"' + (gate.hardGate ? ' selected' : '') + '>TRUE</option><option value="false"' + (!gate.hardGate ? ' selected' : '') + '>FALSE</option></select></td>'
            + '<td><span class="status-chip ' + statusClass(gateResult.status) + '">' + escapeHtml(gateResult.status.toUpperCase()) + '</span></td>'
            + '<td>' + gateResult.score + '/100</td>'
            + '<td><div class="metric-badges">' + gate.metrics.map(function (metric, idx) {
                return '<span class="metric-status ' + statusClass(gateResult.metricResults[idx].status).replace('status', 'metric') + '">' + escapeHtml(metric.name) + '</span>';
              }).join('') + '</div></td>'
            + '<td><div class="table-actions"><button class="btn-mini btn-primary" data-action="open-gate" data-project="' + pIdx + '" data-gate="' + gIdx + '">Buka kalkulator</button><button class="btn-mini btn-secondary" data-action="add-metric" data-project="' + pIdx + '" data-gate="' + gIdx + '">Tambah metrik</button><button class="btn-mini btn-danger" data-action="remove-gate" data-project="' + pIdx + '" data-gate="' + gIdx + '">Hapus gate</button></div></td>'
            + '</tr>';
        });
        return '<div class="table-wrap"><table class="clean-table"><thead><tr><th>Gate Label</th><th>Title</th><th>Caption</th><th>Hard Gate</th><th>Status</th><th>Score</th><th>Metrics</th><th>Actions</th></tr></thead><tbody>' + rows + '</tbody></table></div>';
      }

      function renderMetricTable(project, pIdx) {
        let rows = '';
        project.gates.forEach(function (gate, gIdx) {
          gate.metrics.forEach(function (metric, mIdx) {
            rows += '<tr>'
              + '<td>' + escapeHtml(gate.gate) + '</td>'
              + '<td><input class="table-input" type="text" value="' + attr(metric.name) + '" data-role="metric-field" data-project="' + pIdx + '" data-gate="' + gIdx + '" data-metric="' + mIdx + '" data-field="name" /></td>'
              + '<td><select class="table-select" data-role="metric-field" data-project="' + pIdx + '" data-gate="' + gIdx + '" data-metric="' + mIdx + '" data-field="type"><option value="range"' + (metric.type === 'range' ? ' selected' : '') + '>RANGE</option><option value="select"' + (metric.type === 'select' ? ' selected' : '') + '>SELECT</option></select></td>'
              + '<td><textarea class="table-textarea" data-role="metric-field" data-project="' + pIdx + '" data-gate="' + gIdx + '" data-metric="' + mIdx + '" data-field="desc">' + escapeHtml(metric.desc) + '</textarea></td>'
              + '<td><select class="table-select" data-role="metric-field" data-project="' + pIdx + '" data-gate="' + gIdx + '" data-metric="' + mIdx + '" data-field="direction"><option value="high"' + (metric.direction === 'high' ? ' selected' : '') + '>HIGH</option><option value="low"' + (metric.direction === 'low' ? ' selected' : '') + '>LOW</option></select></td>'
              + '<td><input class="table-input" type="text" value="' + attr(metric.unit || '') + '" data-role="metric-field" data-project="' + pIdx + '" data-gate="' + gIdx + '" data-metric="' + mIdx + '" data-field="unit" /></td>'
              + '<td><select class="table-select" data-role="metric-field" data-project="' + pIdx + '" data-gate="' + gIdx + '" data-metric="' + mIdx + '" data-field="critical"><option value="true"' + (metric.critical ? ' selected' : '') + '>TRUE</option><option value="false"' + (!metric.critical ? ' selected' : '') + '>FALSE</option></select></td>'
              + '<td>' + metricValueInput(metric, pIdx, gIdx, mIdx) + '</td>'
              + '<td>' + (metric.type === 'range' ? numberInput(metric.min, pIdx, gIdx, mIdx, 'min') : '—') + '</td>'
              + '<td>' + (metric.type === 'range' ? numberInput(metric.max, pIdx, gIdx, mIdx, 'max') : '—') + '</td>'
              + '<td>' + (metric.type === 'range' ? numberInput(metric.step, pIdx, gIdx, mIdx, 'step') : '—') + '</td>'
              + '<td>' + (metric.type === 'range' ? numberInput(metric.pass, pIdx, gIdx, mIdx, 'pass') : '—') + '</td>'
              + '<td>' + (metric.type === 'range' ? numberInput(metric.conditional, pIdx, gIdx, mIdx, 'conditional') : '—') + '</td>'
              + '<td><div class="table-actions"><button class="btn-mini btn-secondary" data-action="add-metric" data-project="' + pIdx + '" data-gate="' + gIdx + '">Add</button><button class="btn-mini btn-danger" data-action="remove-metric" data-project="' + pIdx + '" data-gate="' + gIdx + '" data-metric="' + mIdx + '">Remove</button></div></td>'
              + '</tr>';
          });
        });
        return '<div class="table-wrap"><table class="clean-table"><thead><tr><th>Gate</th><th>Metric Name</th><th>Type</th><th>Description</th><th>Direction</th><th>Unit</th><th>Critical</th><th>Current</th><th>Min</th><th>Max</th><th>Step</th><th>PASS</th><th>Conditional</th><th>Actions</th></tr></thead><tbody>' + rows + '</tbody></table></div>';
      }

      function renderInput() {
        const root = document.getElementById('inputGrid');
        root.innerHTML = projects.map(function (project, pIdx) {
          const overall = overallDecision(project);
          const isOpen = expandedProjects.has(pIdx);
          const totalTasks = project.roadmap.reduce(function (sum, period) { return sum + period.tasks.length; }, 0);
          return '<article class="input-card project-collapsible ' + (isOpen ? 'is-open' : '') + '" id="input-project-' + pIdx + '">'
            + '<div class="collapse-head">'
            + '<div class="collapse-main"><div class="collapse-summary"><h3>' + escapeHtml(project.name) + '</h3><p>' + escapeHtml(project.subtitle) + '</p><div class="collapse-meta"><span class="collapse-pill">Progress ' + project.progress + '%</span><span class="collapse-pill">' + escapeHtml(project.currentPeriod) + '</span><span class="collapse-pill">' + project.gates.length + ' gate</span><span class="collapse-pill">' + project.roadmap.length + ' periode</span><span class="collapse-pill">' + totalTasks + ' tugas</span></div></div><div class="collapse-actions"><button class="btn-secondary collapse-toggle" type="button" data-action="toggle-project" data-project="' + pIdx + '">' + (isOpen ? 'Ciutkan' : 'Bentang') + '</button><div class="collapse-chevron">⌄</div></div></div>'
            + '<div class="decision-box"><div class="label">Ringkasan keputusan proyek</div><div class="decision-badge ' + decisionClass(overall.status) + '">' + escapeHtml(overall.status) + '</div><div class="decision-score">' + overall.score + '/100</div><div class="muted-copy">Data di sini hanya di browser sampai Anda klik Simpan ke server. Impor Excel mengganti isi portofolio di layar.</div><div class="inline-actions"><button class="btn-secondary" type="button" data-action="duplicate-project" data-project="' + pIdx + '">Duplikat proyek</button><button class="btn-danger" type="button" data-action="remove-project" data-project="' + pIdx + '">Hapus proyek</button></div></div>'
            + '</div>'
            + (isOpen ? '<div class="input-body">'
              + '<section class="panel"><div class="panel-head"><div class="panel-title">Data master proyek</div><div class="panel-subtitle">Satu baris ringkasan per proyek (nama, fase, progress, dll.).</div></div><div class="panel-inner">' + renderProjectMasterTable(project, pIdx) + '</div></section>'
              + '<section class="panel"><div class="panel-head"><div class="panel-title">Timeline &amp; tugas</div><div class="panel-subtitle">Periode roadmap dan tugas per periode dalam satu tabel.</div></div><div class="panel-inner">' + renderTimelineTable(project, pIdx) + '</div></section>'
              + '<section class="panel"><div class="panel-head"><div class="panel-title">Gate</div><div class="panel-subtitle">Struktur gate (label, judul, hard gate).</div></div><div class="panel-inner"><div class="table-actions" style="margin-bottom:10px;"><button class="btn-primary" data-action="add-gate" data-project="' + pIdx + '">Tambah gate</button></div>' + renderGateTable(project, pIdx, overall) + '</div></section>'
              + '<section class="panel"><div class="panel-head"><div class="panel-title">Metrik</div><div class="panel-subtitle">Semua metrik per gate dalam satu tabel.</div></div><div class="panel-inner">' + renderMetricTable(project, pIdx) + '</div></section>'
              + '</div>' : '')
            + '</article>';
        }).join('');
      }

      function renderMetricRowsForModal(projectIdx, gateIdx, gate, gateResult) {
        let rows = '';
        gate.metrics.forEach(function (metric, mIdx) {
          rows += '<tr>'
            + '<td><input class="table-input" type="text" value="' + attr(metric.name) + '" data-role="metric-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-metric="' + mIdx + '" data-field="name" /></td>'
            + '<td><select class="table-select" data-role="metric-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-metric="' + mIdx + '" data-field="type"><option value="range"' + (metric.type === 'range' ? ' selected' : '') + '>RANGE</option><option value="select"' + (metric.type === 'select' ? ' selected' : '') + '>SELECT</option></select></td>'
            + '<td><textarea class="table-textarea" data-role="metric-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-metric="' + mIdx + '" data-field="desc">' + escapeHtml(metric.desc) + '</textarea></td>'
            + '<td>' + metricValueInput(metric, projectIdx, gateIdx, mIdx, true) + '</td>'
            + '<td><select class="table-select" data-role="metric-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-metric="' + mIdx + '" data-field="direction"><option value="high"' + (metric.direction === 'high' ? ' selected' : '') + '>HIGH</option><option value="low"' + (metric.direction === 'low' ? ' selected' : '') + '>LOW</option></select></td>'
            + '<td><input class="table-input" type="text" value="' + attr(metric.unit || '') + '" data-role="metric-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-metric="' + mIdx + '" data-field="unit" /></td>'
            + '<td>' + (metric.type === 'range' ? numberInput(metric.min, projectIdx, gateIdx, mIdx, 'min') : '—') + '</td>'
            + '<td>' + (metric.type === 'range' ? numberInput(metric.max, projectIdx, gateIdx, mIdx, 'max') : '—') + '</td>'
            + '<td>' + (metric.type === 'range' ? numberInput(metric.step, projectIdx, gateIdx, mIdx, 'step') : '—') + '</td>'
            + '<td>' + (metric.type === 'range' ? numberInput(metric.pass, projectIdx, gateIdx, mIdx, 'pass') : '—') + '</td>'
            + '<td>' + (metric.type === 'range' ? numberInput(metric.conditional, projectIdx, gateIdx, mIdx, 'conditional') : '—') + '</td>'
            + '<td><select class="table-select" data-role="metric-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-metric="' + mIdx + '" data-field="critical"><option value="true"' + (metric.critical ? ' selected' : '') + '>TRUE</option><option value="false"' + (!metric.critical ? ' selected' : '') + '>FALSE</option></select></td>'
            + '<td><span class="metric-status ' + statusClass(gateResult.metricResults[mIdx].status).replace('status', 'metric') + '">' + escapeHtml(gateResult.metricResults[mIdx].status.toUpperCase()) + '</span></td>'
            + '<td><div class="table-actions"><button class="btn-mini btn-secondary" data-action="add-metric" data-project="' + projectIdx + '" data-gate="' + gateIdx + '">Add</button><button class="btn-mini btn-danger" data-action="remove-metric" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-metric="' + mIdx + '">Remove</button></div></td>'
            + '</tr>';
        });
        return '<div class="table-wrap"><table class="clean-table"><thead><tr><th>Name</th><th>Type</th><th>Description</th><th>Current</th><th>Direction</th><th>Unit</th><th>Min</th><th>Max</th><th>Step</th><th>PASS</th><th>Conditional</th><th>Critical</th><th>Status</th><th>Actions</th></tr></thead><tbody>' + rows + '</tbody></table></div>';
      }

      function renderGateModal(projectIdx, gateIdx, customTitle) {
        const project = projects[projectIdx];
        const gate = project.gates[gateIdx];
        const overall = overallDecision(project);
        const gateResult = overall.gateResults[gateIdx];
        const metricPass = gateResult.metricResults.filter(function (x) { return x.status === 'pass'; }).length;
        const metricCond = gateResult.metricResults.filter(function (x) { return x.status === 'conditional'; }).length;
        const metricFail = gateResult.metricResults.filter(function (x) { return x.status === 'fail'; }).length;
        const headerSuffix = customTitle || 'Calculator';
        return '<section class="modal-hero">'
          + '<div class="premium-label">' + escapeHtml(project.name) + ' · ' + escapeHtml(gate.gate) + '</div>'
          + '<h2>' + escapeHtml(gate.title) + ' <span>' + escapeHtml(headerSuffix) + '</span></h2>'
          + '<p>Clean gate calculator with a single metrics table. The structure is fully editable here as well.</p>'
          + '<div class="modal-flags"><div class="modal-flag">Pilot Area: ' + escapeHtml(project.pilotArea) + '</div><div class="modal-flag">Current Period: ' + escapeHtml(project.currentPeriod) + '</div><div class="modal-flag">' + escapeHtml(gate.hardGate ? 'Hard Gate' : 'Soft Gate') + '</div></div>'
          + '</section>'
          + '<section class="modal-grid">'
          + '<div class="modal-section"><div class="modal-section-head"><div class="panel-title">Gate Summary</div><div class="panel-subtitle">Live result from current gate inputs.</div></div><div class="modal-section-inner">'
          + '<div class="mini-card"><div class="label">Gate Status</div><div class="modal-value">' + escapeHtml(gateResult.status.toUpperCase()) + '</div></div>'
          + '<div class="mini-card" style="margin-top:10px;"><div class="label">Gate Score</div><div class="modal-value">' + gateResult.score + '/100</div></div>'
          + '<div class="mini-card" style="margin-top:10px;"><div class="label">Metric Mix</div><div class="modal-value">' + metricPass + '/' + metricCond + '/' + metricFail + '</div></div>'
          + '<div class="mini-card" style="margin-top:10px;"><div class="label">Overall Project</div><div class="modal-value">' + escapeHtml(overall.status) + '</div></div>'
          + '</div></div>'
          + '<div class="modal-section"><div class="modal-section-head"><div class="panel-title">Gate Structure</div><div class="panel-subtitle">Edit gate label, title, caption, and hard-gate behavior.</div></div><div class="modal-section-inner">'
          + '<div class="table-wrap" style="margin-bottom:14px;"><table class="clean-table" style="min-width:780px;"><thead><tr><th>Gate Label</th><th>Title</th><th>Caption</th><th>Hard Gate</th></tr></thead><tbody><tr>'
          + '<td><input class="table-input" type="text" value="' + attr(gate.gate) + '" data-role="gate-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-field="gate" /></td>'
          + '<td><input class="table-input" type="text" value="' + attr(gate.title) + '" data-role="gate-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-field="title" /></td>'
          + '<td><textarea class="table-textarea" data-role="gate-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-field="caption">' + escapeHtml(gate.caption) + '</textarea></td>'
          + '<td><select class="table-select" data-role="gate-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-field="hardGate"><option value="true"' + (gate.hardGate ? ' selected' : '') + '>TRUE</option><option value="false"' + (!gate.hardGate ? ' selected' : '') + '>FALSE</option></select></td>'
          + '</tr></tbody></table></div>'
          + renderMetricRowsForModal(projectIdx, gateIdx, gate, gateResult)
          + '</div></div></section>';
      }

      function openGateModal(projectIdx, gateIdx) {
        activeModal = { projectIdx: projectIdx, gateIdx: gateIdx };
        const project = projects[projectIdx];
        const header = project.name === 'Remote Dozer' && gateIdx === 3 ? 'Economical Pass' : 'Calculator';
        document.getElementById('modalContent').innerHTML = renderGateModal(projectIdx, gateIdx, header);
        document.getElementById('modalOverlay').classList.add('active');
      }

      function closeModal() {
        activeModal = null;
        document.getElementById('modalOverlay').classList.remove('active');
      }

      function parseFieldValue(field, value) {
        if (['progress', 'min', 'max', 'step', 'value', 'pass', 'conditional'].indexOf(field) >= 0) return Number(value);
        if (field === 'hardGate' || field === 'critical') return value === 'true' || value === true;
        return value;
      }

      function updateProjectField(projectIdx, field, value) {
        projects[projectIdx][field] = field === 'progress' ? Math.min(100, Math.max(0, Number(value) || 0)) : value;
        renderAll();
      }

      function updatePeriodField(projectIdx, periodIdx, field, value) {
        projects[projectIdx].roadmap[periodIdx][field] = value;
        renderAll();
      }

      function updateTaskField(projectIdx, periodIdx, taskIdx, field, value) {
        projects[projectIdx].roadmap[periodIdx].tasks[taskIdx][field] = value;
        renderAll();
      }

      function updateGateField(projectIdx, gateIdx, field, value) {
        projects[projectIdx].gates[gateIdx][field] = parseFieldValue(field, value);
        renderAll();
        if (activeModal && activeModal.projectIdx === projectIdx && activeModal.gateIdx === gateIdx) openGateModal(projectIdx, gateIdx);
      }

      function updateMetricField(projectIdx, gateIdx, metricIdx, field, value) {
        const metric = projects[projectIdx].gates[gateIdx].metrics[metricIdx];
        metric[field] = parseFieldValue(field, value);
        if (field === 'type') {
          if (value === 'select') {
            metric.value = ['pass', 'conditional', 'fail'].indexOf(metric.value) >= 0 ? metric.value : 'conditional';
          } else {
            metric.direction = metric.direction || 'high';
            metric.unit = metric.unit == null ? '%' : metric.unit;
            metric.min = Number.isFinite(+metric.min) ? +metric.min : 0;
            metric.max = Number.isFinite(+metric.max) ? +metric.max : 100;
            metric.step = Number.isFinite(+metric.step) ? +metric.step : 1;
            metric.value = Number.isFinite(+metric.value) ? +metric.value : 50;
            metric.pass = Number.isFinite(+metric.pass) ? +metric.pass : 80;
            metric.conditional = Number.isFinite(+metric.conditional) ? +metric.conditional : 60;
          }
        }
        renderAll();
        if (activeModal && activeModal.projectIdx === projectIdx && activeModal.gateIdx === gateIdx) openGateModal(projectIdx, gateIdx);
      }

      function addProject() {
        projects.push(newProject(projects.length));
        expandedProjects.add(projects.length - 1);
        expandedDashboardProjects.add(projects.length - 1);
        renderAll();
      }

      function duplicateProject(projectIdx) {
        const copy = JSON.parse(JSON.stringify(projects[projectIdx]));
        copy.name = copy.name + ' Copy';
        projects.splice(projectIdx + 1, 0, copy);
        expandedProjects = new Set(Array.from(expandedProjects).map(function (index) { return index > projectIdx ? index + 1 : index; }));
        expandedProjects.add(projectIdx + 1);
        expandedDashboardProjects = new Set(Array.from(expandedDashboardProjects).map(function (index) { return index > projectIdx ? index + 1 : index; }));
        expandedDashboardProjects.add(projectIdx + 1);
        renderAll();
      }

      function removeProject(projectIdx) {
        projects.splice(projectIdx, 1);
        expandedProjects = new Set(Array.from(expandedProjects).filter(function (index) { return index !== projectIdx; }).map(function (index) { return index > projectIdx ? index - 1 : index; }));
        expandedDashboardProjects = new Set(Array.from(expandedDashboardProjects).filter(function (index) { return index !== projectIdx; }).map(function (index) { return index > projectIdx ? index - 1 : index; }));
        renderAll();
        closeModal();
      }

      function addPeriod(projectIdx) {
        projects[projectIdx].roadmap.push(newPeriod());
        renderAll();
      }

      function removePeriod(projectIdx, periodIdx) {
        projects[projectIdx].roadmap.splice(periodIdx, 1);
        if (!projects[projectIdx].roadmap.length) projects[projectIdx].roadmap.push(newPeriod());
        renderAll();
      }

      function addTask(projectIdx, periodIdx) {
        projects[projectIdx].roadmap[periodIdx].tasks.push(newTask());
        renderAll();
      }

      function removeTask(projectIdx, periodIdx, taskIdx) {
        projects[projectIdx].roadmap[periodIdx].tasks.splice(taskIdx, 1);
        if (!projects[projectIdx].roadmap[periodIdx].tasks.length) projects[projectIdx].roadmap[periodIdx].tasks.push(newTask());
        renderAll();
      }

      function addGate(projectIdx) {
        projects[projectIdx].gates.push(newGate(projects[projectIdx].gates.length));
        renderAll();
      }

      function removeGate(projectIdx, gateIdx) {
        projects[projectIdx].gates.splice(gateIdx, 1);
        if (!projects[projectIdx].gates.length) projects[projectIdx].gates.push(newGate(0));
        renderAll();
        closeModal();
      }

      function addMetric(projectIdx, gateIdx) {
        projects[projectIdx].gates[gateIdx].metrics.push(newMetric());
        renderAll();
        if (activeModal && activeModal.projectIdx === projectIdx && activeModal.gateIdx === gateIdx) openGateModal(projectIdx, gateIdx);
      }

      function removeMetric(projectIdx, gateIdx, metricIdx) {
        projects[projectIdx].gates[gateIdx].metrics.splice(metricIdx, 1);
        if (!projects[projectIdx].gates[gateIdx].metrics.length) projects[projectIdx].gates[gateIdx].metrics.push(newMetric());
        renderAll();
        if (activeModal && activeModal.projectIdx === projectIdx && activeModal.gateIdx === gateIdx) openGateModal(projectIdx, gateIdx);
      }

      function buildProjectsFromWorkbook(workbook) {
        const projectRows = maybeReadSheetRows(workbook, 'PROJECTS');
        const periodRows = maybeReadSheetRows(workbook, 'TIMELINE_PERIODS');
        const taskRows = maybeReadSheetRows(workbook, 'TIMELINE_TASKS');
        const timelineRows = maybeReadSheetRows(workbook, 'TIMELINE');
        const gateRows = maybeReadSheetRows(workbook, 'GATES');
        const metricRows = maybeReadSheetRows(workbook, 'METRICS');
        const historyRows = maybeReadSheetRows(workbook, 'HISTORY');

        const hasSplit = periodRows.length > 0 || taskRows.length > 0;
        const hasLegacy = timelineRows.length > 0;
        if (!projectRows.length || !gateRows.length || !metricRows.length) {
          throw new Error('Workbook harus memiliki sheet PROJECTS, GATES, dan METRICS — masing-masing minimal satu baris data (baris header + data).');
        }
        if (!hasSplit && !hasLegacy) {
          throw new Error('Isi sheet TIMELINE (format lama) atau setidaknya salah satu TIMELINE_PERIODS / TIMELINE_TASKS (format template spreadsheet).');
        }

        function emptyImportPeriod(rp, ph) {
          return {
            displayCurrentPeriod: '',
            period: rp,
            phase: ph,
            status: 'plan',
            periodExplanation: '',
            plannedObjectiveOutcome: '',
            picUpdateSummary: '',
            picRisksDependencies: '',
            picOwner: '',
            targetDate: '',
            reviewerStatus: '',
            periodProgressPercent: null,
            tasks: []
          };
        }

        const projectMap = new Map();
        const order = [];

        function ensureImportedProject(name) {
          const projectName = String(name || '').trim();
          if (!projectName) return null;
          if (!projectMap.has(projectName)) {
            projectMap.set(projectName, {
              name: projectName,
              subtitle: '',
              pilotArea: '',
              support: '',
              currentPhase: '',
              progress: 0,
              currentPeriod: '',
              nextMilestone: '',
              needSupportPic: '',
              roadmap: [],
              gates: [],
              _periodMap: new Map(),
              _gateMap: new Map()
            });
            order.push(projectName);
          }
          return projectMap.get(projectName);
        }

        function ensurePeriod(project, rp, ph) {
          const period = String(rp || '').trim() || 'New Period';
          const phase = String(ph || '').trim() || 'New phase';
          const periodKey = period + '|||' + phase;
          if (!project._periodMap.has(periodKey)) {
            const t = emptyImportPeriod(period, phase);
            project._periodMap.set(periodKey, t);
            project.roadmap.push(t);
          }
          return project._periodMap.get(periodKey);
        }

        function gateFromRow(gateLabel, row) {
          return {
            gate: gateLabel,
            title: String(row.gate_title || row.title || 'New Gate').trim() || 'New Gate',
            caption: String(row.gate_caption || row.caption || row.original_caption || '').trim(),
            hardGate: parseBool(row.hard_gate),
            gateDefinition: String(row.gate_definition || '').trim(),
            projectSpecificExplanation: String(row.project_specific_explanation || '').trim(),
            whatGateConfirms: String(row.what_this_gate_confirms || row.what_gate_confirms || '').trim(),
            whatPicNeedsToFill: String(row.what_pic_needs_to_fill || '').trim(),
            picStatus: String(row.pic_status || '').trim(),
            picNotesKeyFindings: String(row.pic_notes_key_findings || '').trim(),
            evidenceLinkFolder: String(row.evidence_link_folder || '').trim(),
            picOwner: String(row.pic_owner || '').trim(),
            targetCloseDate: parseOptionalDateStr(row.target_close_date),
            reviewerStatus: String(row.reviewer_status || '').trim(),
            metrics: []
          };
        }

        function mergeGateRow(gate, row) {
          gate.title = String(row.gate_title || row.title || gate.title || 'New Gate').trim() || gate.title;
          const cap = String(row.gate_caption || row.caption || row.original_caption || '').trim();
          if (cap) gate.caption = cap;
          if (row.hard_gate !== undefined && row.hard_gate !== null && String(row.hard_gate).trim() !== '') {
            gate.hardGate = parseBool(row.hard_gate);
          }
          const keys = [
            ['gateDefinition', 'gate_definition'],
            ['projectSpecificExplanation', 'project_specific_explanation'],
            ['whatGateConfirms', 'what_this_gate_confirms', 'what_gate_confirms'],
            ['whatPicNeedsToFill', 'what_pic_needs_to_fill'],
            ['picStatus', 'pic_status'],
            ['picNotesKeyFindings', 'pic_notes_key_findings'],
            ['evidenceLinkFolder', 'evidence_link_folder'],
            ['picOwner', 'pic_owner'],
            ['reviewerStatus', 'reviewer_status']
          ];
          keys.forEach(function (spec) {
            const prop = spec[0];
            for (let i = 1; i < spec.length; i++) {
              const v = String(row[spec[i]] || '').trim();
              if (v) { gate[prop] = v; break; }
            }
          });
          const tcd = parseOptionalDateStr(row.target_close_date);
          if (tcd) gate.targetCloseDate = tcd;
        }

        function defaultGateShell(gateLabel) {
          const g = newGate(0);
          g.gate = gateLabel;
          g.title = gateLabel;
          g.metrics = [];
          return g;
        }

        projectRows.forEach(function (row) {
          const project = ensureImportedProject(row.project_name || row.project || row.name);
          if (!project) return;
          project.subtitle = String(row.subtitle || row.subtitle_context || project.subtitle || '');
          project.pilotArea = String(row.pilot_area || row.pilotarea || project.pilotArea || '');
          project.support = String(row.support || row.support_needed || project.support || '');
          project.currentPhase = String(row.current_phase || row.currentphase || project.currentPhase || '');
          project.progress = Math.max(0, Math.min(100, Math.round(parseNumber(row.progress, project.progress) * 100) / 100));
          project.currentPeriod = String(row.current_period || row.currentperiod || project.currentPeriod || '');
          project.nextMilestone = String(row.next_milestone || row.nextmilestone || project.nextMilestone || '');
          project.needSupportPic = String(row.need_support_pic || project.needSupportPic || '');
        });

        if (hasSplit) {
          periodRows.forEach(function (row) {
            const project = ensureImportedProject(row.project_name || row.project || row.name);
            if (!project) return;
            const rp = String(row.roadmap_period || row.period || '').trim() || 'New Period';
            const ph = String(row.phase || '').trim() || 'New phase';
            const timeline = ensurePeriod(project, rp, ph);
            const cur = String(row.display_current_period || row.current_period || '').trim();
            if (cur) timeline.displayCurrentPeriod = cur;
            timeline.status = normalizeTaskStatus(row.period_status || row.status || timeline.status);
            if (row.period_explanation != null && row.period_explanation !== '') timeline.periodExplanation = String(row.period_explanation);
            if (row.planned_objective_outcome != null && row.planned_objective_outcome !== '') timeline.plannedObjectiveOutcome = String(row.planned_objective_outcome);
            if (row.pic_update_summary != null && row.pic_update_summary !== '') timeline.picUpdateSummary = String(row.pic_update_summary);
            if (row.pic_risks_dependencies != null && row.pic_risks_dependencies !== '') timeline.picRisksDependencies = String(row.pic_risks_dependencies);
            if (row.pic_owner != null && row.pic_owner !== '') timeline.picOwner = String(row.pic_owner);
            const td = parseOptionalDateStr(row.target_date);
            if (td) timeline.targetDate = td;
            if (row.reviewer_status != null && row.reviewer_status !== '') timeline.reviewerStatus = String(row.reviewer_status);
            const pp = nullableProgressPercent(row.period_progress_percent || row.period_progress);
            if (pp != null) timeline.periodProgressPercent = pp;
          });

          taskRows.forEach(function (row) {
            const project = ensureImportedProject(row.project_name || row.project || row.name);
            if (!project) return;
            const rp = String(row.roadmap_period || row.period || '').trim() || 'New Period';
            const ph = String(row.phase || '').trim() || 'New phase';
            const timeline = ensurePeriod(project, rp, ph);
            const taskText = String(row.task || row.task_text || '').trim();
            if (!taskText) return;
            const origOwn = String(row.original_owner || '').trim();
            const picOwn = String(row.pic_actual_owner || '').trim();
            const fallbackOwner = String(row.task_owner || row.owner || 'Owner').trim() || 'Owner';
            const owner = picOwn || (origOwn || fallbackOwner);
            const origSt = normalizeTaskStatus(row.original_status || 'plan');
            const status = normalizeTaskStatus(row.task_status || row.status || origSt);
            timeline.tasks.push({
              text: taskText,
              owner: owner,
              status: status,
              originalOwner: origOwn || owner,
              originalStatus: origSt,
              picActualOwner: picOwn,
              picStartDate: parseOptionalDateStr(row.pic_start_date),
              picActualPercent: nullableProgressPercent(row.pic_actual_input || row.pic_actual_percent),
              picProgressNote: String(row.pic_progress_note || '').trim(),
              evidenceLink: String(row.evidence_link || '').trim(),
              targetDate: parseOptionalDateStr(row.target_date),
              dependencyBlocker: String(row.dependency_blocker || '').trim(),
              taskProgressPercentNormalized: nullableProgressPercent(row.task_progress_percent_normalized || row.task_progress_normalized)
            });
          });
        } else {
          timelineRows.forEach(function (row) {
            const project = ensureImportedProject(row.project_name || row.project || row.name);
            if (!project) return;
            const period = String(row.period || 'New Period').trim() || 'New Period';
            const phase = String(row.phase || 'New phase').trim() || 'New phase';
            const timeline = ensurePeriod(project, period, phase);
            const cur = String(row.display_current_period || row.current_period || '').trim();
            if (cur) timeline.displayCurrentPeriod = cur;
            timeline.status = normalizeTaskStatus(row.period_status || row.status || timeline.status);
            if (row.period_explanation != null && row.period_explanation !== '') timeline.periodExplanation = String(row.period_explanation);
            if (row.planned_objective_outcome != null && row.planned_objective_outcome !== '') timeline.plannedObjectiveOutcome = String(row.planned_objective_outcome);
            if (row.pic_update_summary != null && row.pic_update_summary !== '') timeline.picUpdateSummary = String(row.pic_update_summary);
            if (row.pic_risks_dependencies != null && row.pic_risks_dependencies !== '') timeline.picRisksDependencies = String(row.pic_risks_dependencies);
            if (row.pic_owner != null && row.pic_owner !== '') timeline.picOwner = String(row.pic_owner);
            const td = parseOptionalDateStr(row.target_date);
            if (td) timeline.targetDate = td;
            if (row.reviewer_status != null && row.reviewer_status !== '') timeline.reviewerStatus = String(row.reviewer_status);
            const pp = nullableProgressPercent(row.period_progress_percent);
            if (pp != null) timeline.periodProgressPercent = pp;
            const taskText = String(row.task_text || row.task || '').trim();
            if (!taskText) return;
            const origOwn = String(row.original_owner || '').trim();
            const picOwn = String(row.pic_actual_owner || '').trim();
            const fallbackOwner = String(row.task_owner || row.owner || 'Owner').trim() || 'Owner';
            const owner = picOwn || (origOwn || fallbackOwner);
            const origSt = normalizeTaskStatus(row.original_status || 'plan');
            const status = normalizeTaskStatus(row.task_status || row.taskstate || origSt);
            timeline.tasks.push({
              text: taskText,
              owner: owner,
              status: status,
              originalOwner: origOwn || owner,
              originalStatus: origSt,
              picActualOwner: picOwn,
              picStartDate: parseOptionalDateStr(row.pic_start_date),
              picActualPercent: nullableProgressPercent(row.pic_actual_percent),
              picProgressNote: String(row.pic_progress_note || '').trim(),
              evidenceLink: String(row.evidence_link || '').trim(),
              targetDate: parseOptionalDateStr(row.target_date),
              dependencyBlocker: String(row.dependency_blocker || '').trim(),
              taskProgressPercentNormalized: nullableProgressPercent(row.task_progress_percent_normalized)
            });
          });
        }

        gateRows.forEach(function (row) {
          const project = ensureImportedProject(row.project_name || row.project || row.name);
          if (!project) return;
          const gateLabel = String(row.gate_label || row.gate || 'Gate 1').trim() || 'Gate 1';
          if (!project._gateMap.has(gateLabel)) {
            const gate = gateFromRow(gateLabel, row);
            project._gateMap.set(gateLabel, gate);
            project.gates.push(gate);
          } else {
            mergeGateRow(project._gateMap.get(gateLabel), row);
          }
        });

        metricRows.forEach(function (row) {
          const project = ensureImportedProject(row.project_name || row.project || row.name);
          if (!project) return;
          const gateLabel = String(row.gate_label || row.gate || 'Gate 1').trim() || 'Gate 1';
          if (!project._gateMap.has(gateLabel)) {
            const gate = defaultGateShell(gateLabel);
            project._gateMap.set(gateLabel, gate);
            project.gates.push(gate);
          }
          const gate = project._gateMap.get(gateLabel);
          const metricType = normalizeMetricType(row.metric_type || row.type);
          const metric = {
            name: String(row.metric_name || row.name || 'New metric').trim() || 'New metric',
            desc: String(row.metric_desc || row.metric_description || row.description || '').trim(),
            type: metricType,
            direction: String(row.direction || 'high').trim().toLowerCase() === 'low' ? 'low' : 'high',
            unit: String(row.unit || '%').trim() || '%',
            critical: parseBool(row.critical),
            picCurrentFinding: String(row.pic_current_finding || '').trim(),
            picEvidenceSource: String(row.pic_evidence_source || '').trim(),
            picComment: String(row.pic_comment || '').trim(),
            metricStatus: String(row.metric_status || '').trim()
          };
          if (metricType === 'select') {
            metric.value = normalizeGateDecisionValue(row.metric_value || row.current_value || row.value);
          } else {
            metric.min = parseNumber(row.min_value || row.min, 0);
            metric.max = parseNumber(row.max_value || row.max, 100);
            metric.step = parseNumber(row.step_value || row.step, 1);
            metric.value = parseNumber(row.metric_value || row.current_value || row.value, 50);
            metric.pass = parseNumber(row.pass_threshold || row.pass, 80);
            metric.conditional = parseNumber(row.conditional_threshold || row.conditional, 60);
          }
          gate.metrics.push(metric);
        });

        const importedProjects = order.map(function (name) {
          const project = projectMap.get(name);
          if (!project.roadmap.length) project.roadmap.push(newPeriod());
          project.roadmap.forEach(function (period) { if (!period.tasks.length) period.tasks.push(newTask()); });
          if (!project.gates.length) project.gates.push(newGate(0));
          project.gates.forEach(function (gate) { if (!gate.metrics.length) gate.metrics.push(newMetric()); });
          delete project._periodMap;
          delete project._gateMap;
          return project;
        });

        const importedHistory = historyRows.map(function (row) {
          const date = String(row.snapshot_date || row.date || row.period || '').trim();
          if (!date) return null;
          const score = row.decision_score !== '' && row.decision_score !== undefined ? parseNumber(row.decision_score, 70) : decisionStrengthFromStatus(row.decision_status || row.decision || row.status);
          return {
            date: date,
            projectName: String(row.project_name || row.project || '').trim(),
            progress: Math.max(0, Math.min(100, Math.round(parseNumber(row.progress, 0) * 100) / 100)),
            decisionScore: Math.max(0, Math.min(100, score))
          };
        }).filter(Boolean);

        return { importedProjects: importedProjects, importedHistory: importedHistory };
      }

      function importWorkbookFromFile(file) {
        if (!file) {
          setImportStatus('Belum ada file', 'Pilih file .xlsx atau .xls terlebih dahulu (tombol Pilih file).', 'notice-card notice-warning');
          return;
        }
        if (!window.XLSX) {
          setImportStatus('Library Excel tidak ada', 'Pustaka pembaca Excel gagal dimuat. Muat ulang halaman atau periksa koneksi (CDN jsDelivr).', 'notice-card notice-warning');
          return;
        }
        setImportStatus('Membaca file…', 'Memproses workbook, harap tunggu.', 'notice-card');
        const reader = new FileReader();
        reader.onload = function (event) {
          try {
            const workbook = window.XLSX.read(event.target.result, { type: 'array' });
            const result = buildProjectsFromWorkbook(workbook);
            projects.length = 0;
            result.importedProjects.forEach(function (project) { projects.push(project); });
            historySnapshots = result.importedHistory;
            expandedProjects = new Set([0]);
            expandedDashboardProjects = new Set([0]);
            renderAll();
            var h = result.importedHistory.length;
            var msg = 'Berhasil memuat ' + result.importedProjects.length + ' proyek ke halaman ini.';
            if (h) msg += ' Riwayat (HISTORY): ' + h + ' baris.';
            msg += ' Data ini di memori browser — klik Simpan ke server agar masuk database.';
            setImportStatus('Impor berhasil', msg, 'notice-card notice-success');
          } catch (error) {
            setImportStatus('Impor gagal', error && error.message ? error.message : 'Format workbook tidak valid. Periksa nama sheet (PROJECTS, GATES, METRICS, timeline TIMELINE atau TIMELINE_PERIODS/TIMELINE_TASKS).', 'notice-card notice-error');
          }
        };
        reader.onerror = function () {
          setImportStatus('Baca file gagal', 'File tidak bisa dibaca. Coba file lain atau tutup file di Excel lalu ulangi.', 'notice-card notice-error');
        };
        reader.readAsArrayBuffer(file);
      }

      function downloadTemplateWorkbook() {
        const projectsCsv = buildCsv([{ project_name: 'Arcas HD', subtitle: 'Heavy automation use case', pilot_area: 'PAMA BMO2', support: '5G, ROC', current_phase: 'Pilot proving', progress: 68, current_period: 'Apr–Jun 2026', next_milestone: 'Safety drill closeout', need_support_pic: '' }]);
        const timelineCsv = buildCsv([{ project_name: 'Arcas HD', display_current_period: '', period: 'Apr–Jun 2026', phase: 'Pilot operation', status: 'progress', task_text: 'Run supervised multi-shift pilot scenario', task_owner: 'Ops', task_status: 'progress' }]);
        const gatesCsv = buildCsv([{ project_name: 'Arcas HD', gate_label: 'Gate 1', gate_title: 'Technical Feasibility', gate_caption: 'Network readiness and integration', hard_gate: 'yes' }]);
        const metricsCsv = buildCsv([
          { project_name: 'Arcas HD', gate_label: 'Gate 1', metric_name: 'Network uptime', metric_type: 'range', metric_desc: 'Live network support', direction: 'high', unit: '%', critical: 'no', metric_value: 99.1, min_value: 90, max_value: 100, step_value: 0.1, pass_threshold: 98, conditional_threshold: 96 },
          { project_name: 'Arcas HD', gate_label: 'Gate 3', metric_name: 'SOP readiness', metric_type: 'select', metric_desc: 'Procedure readiness', direction: '', unit: '', critical: 'yes', metric_value: 'pass', min_value: '', max_value: '', step_value: '', pass_threshold: '', conditional_threshold: '' }
        ]);
        const historyCsv = buildCsv([{ project_name: 'Arcas HD', snapshot_date: '2026-04-01', progress: 52, decision_status: 'conditional go', decision_score: '' }, { project_name: 'Arcas HD', snapshot_date: '2026-05-01', progress: 68, decision_status: 'go', decision_score: '' }]);
        downloadText('PROJECTS.csv', projectsCsv);
        setTimeout(function () { downloadText('TIMELINE.csv', timelineCsv); }, 50);
        setTimeout(function () { downloadText('GATES.csv', gatesCsv); }, 100);
        setTimeout(function () { downloadText('METRICS.csv', metricsCsv); }, 150);
        setTimeout(function () { downloadText('HISTORY.csv', historyCsv); }, 200);
        setImportStatus('Template diunduh', 'Beberapa file CSV telah diunduh. Buka di Excel, tempatkan ke workbook dengan nama sheet PROJECTS / TIMELINE / GATES / METRICS / HISTORY, simpan sebagai .xlsx, lalu impor di atas.', 'notice-card notice-success');
      }

      function applyPageState() {
        document.getElementById('dashboardPage').classList.toggle('active', currentPage === 'dashboard');
        document.getElementById('inputPage').classList.toggle('active', currentPage === 'input');
        document.getElementById('dashboardTab').classList.toggle('active', currentPage === 'dashboard');
        document.getElementById('inputTab').classList.toggle('active', currentPage === 'input');
      }

      function renderAll() {
        renderOverview();
        renderCurveChart();
        renderDashboard();
        renderNeedSupportList();
        renderInput();
        applyPageState();
      }

      function numberInput(value, projectIdx, gateIdx, metricIdx, field) {
        return '<input class="table-number" type="number" value="' + value + '" data-role="metric-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-metric="' + metricIdx + '" data-field="' + field + '" />';
      }

      function metricValueInput(metric, projectIdx, gateIdx, metricIdx, withRange) {
        if (metric.type === 'select') {
          return '<select class="table-select" data-role="metric-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-metric="' + metricIdx + '" data-field="value"><option value="pass"' + (metric.value === 'pass' ? ' selected' : '') + '>PASS</option><option value="conditional"' + (metric.value === 'conditional' ? ' selected' : '') + '>CONDITIONAL</option><option value="fail"' + (metric.value === 'fail' ? ' selected' : '') + '>FAIL</option></select>';
        }
        if (withRange) {
          return '<div class="compact-stack"><input class="table-range" type="range" min="' + metric.min + '" max="' + metric.max + '" step="' + metric.step + '" value="' + metric.value + '" data-role="metric-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-metric="' + metricIdx + '" data-field="value" /><input class="table-number" type="number" value="' + metric.value + '" data-role="metric-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-metric="' + metricIdx + '" data-field="value" /></div>';
        }
        return '<input class="table-number" type="number" value="' + metric.value + '" data-role="metric-field" data-project="' + projectIdx + '" data-gate="' + gateIdx + '" data-metric="' + metricIdx + '" data-field="value" />';
      }

      function selectHtml(role, pIdx, periodIdx, taskIdx, field, currentValue, options, isPeriod) {
        return '<select class="table-select" data-role="' + role + '" data-project="' + pIdx + '" data-period="' + periodIdx + '"' + (isPeriod ? '' : ' data-task="' + taskIdx + '"') + ' data-field="' + field + '">'
          + options.map(function (opt) {
              return '<option value="' + opt + '"' + (currentValue === opt ? ' selected' : '') + '>' + opt.toUpperCase() + '</option>';
            }).join('')
          + '</select>';
      }

      function csrfToken() {
        const m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') || '' : '';
      }

      function loadPortfolioFromServer() {
        const cfg = window.PilotProjectValidation || {};
        if (!cfg.portfolioUrl) return;
        setImportStatus('Memuat…', 'Mengambil portfolio dari database server.', 'notice-card');
        fetch(cfg.portfolioUrl, {
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
          .then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
          })
          .then(function (data) {
            if (data.projects && data.projects.length) {
              projects.length = 0;
              data.projects.forEach(function (project) { projects.push(project); });
              historySnapshots = data.historySnapshots && data.historySnapshots.length ? data.historySnapshots : [];
              expandedProjects = new Set([0]);
              expandedDashboardProjects = new Set([0]);
              renderAll();
              closeModal();
              setImportStatus('Berhasil dimuat', data.projects.length + ' proyek dimuat dari database ke halaman ini.', 'notice-card notice-success');
            } else {
              restoreSeedProjects();
              renderAll();
              setImportStatus('Database kosong', 'Belum ada data tersimpan. Ditampilkan contoh default. Anda bisa input/impor lalu Simpan ke server.', 'notice-card notice-warning');
            }
          })
          .catch(function (err) {
            restoreSeedProjects();
            renderAll();
            setImportStatus('Gagal memuat', err && err.message ? err.message : 'Tidak dapat menghubungi server.', 'notice-card notice-error');
          });
      }

      function savePortfolioToServer() {
        const cfg = window.PilotProjectValidation || {};
        if (!cfg.portfolioSaveUrl) return;
        setImportStatus('Menyimpan…', 'Mengirim portfolio ke database server.', 'notice-card');
        fetch(cfg.portfolioSaveUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken()
          },
          body: JSON.stringify({ projects: projects, historySnapshots: historySnapshots })
        })
          .then(function (r) {
            return r.json().then(function (body) {
              if (!r.ok) {
                var msg = (body && body.message) ? body.message : ('HTTP ' + r.status);
                throw new Error(msg);
              }
              return body;
            });
          })
          .then(function (body) {
            const msg = body && body.message ? body.message : 'Tersimpan di database.';
            setImportStatus('Tersimpan di server', msg, 'notice-card notice-success');
          })
          .catch(function (err) {
            setImportStatus('Gagal menyimpan', err && err.message ? err.message : 'Tidak dapat menyimpan ke server.', 'notice-card notice-error');
          });
      }

      function importExcelToDatabase() {
        const cfg = window.PilotProjectValidation || {};
        if (!cfg.importExcelUrl) return;
        const file = selectedWorkbookFile || (document.getElementById('excelFileInput').files && document.getElementById('excelFileInput').files[0]);
        if (!file) {
          setImportStatus('Belum ada file', 'Pilih file .xlsx / .xls terlebih dahulu.', 'notice-card notice-warning');
          return;
        }
        setImportStatus('Mengunggah…', 'Memproses Excel di server dan menulis ke database.', 'notice-card');
        const fd = new FormData();
        fd.append('file', file);
        fd.append('_token', csrfToken());
        fetch(cfg.importExcelUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken() },
          body: fd
        })
          .then(function (r) {
            return r.json().then(function (body) {
              if (!r.ok) {
                var msg = (body && body.message) ? body.message : ('HTTP ' + r.status);
                throw new Error(msg);
              }
              return body;
            });
          })
          .then(function (body) {
            if (body.projects && body.projects.length) {
              projects.length = 0;
              body.projects.forEach(function (project) { projects.push(project); });
              historySnapshots = body.historySnapshots && body.historySnapshots.length ? body.historySnapshots : [];
              expandedProjects = new Set([0]);
              expandedDashboardProjects = new Set([0]);
              renderAll();
              closeModal();
            }
            const msg = body && body.message ? body.message : 'Selesai.';
            setImportStatus('Tersimpan di database', msg, 'notice-card notice-success');
          })
          .catch(function (err) {
            setImportStatus('Impor database gagal', err && err.message ? err.message : 'Server error.', 'notice-card notice-error');
          });
      }

      function bindGlobalEvents() {
        document.getElementById('dashboardTab').onclick = function () {
          currentPage = 'dashboard';
          applyPageState();
          var el = document.getElementById('dashboardPage');
          if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        };
        document.getElementById('inputTab').onclick = function () {
          currentPage = 'input';
          applyPageState();
          var el = document.getElementById('inputPage');
          if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        };
        document.getElementById('loadFromServerBtn').onclick = function () { loadPortfolioFromServer(); };
        document.getElementById('saveToServerBtn').onclick = function () { savePortfolioToServer(); };
        document.getElementById('chooseFileBtn').onclick = function () { document.getElementById('excelFileInput').click(); };
        document.getElementById('excelFileInput').addEventListener('change', function (event) {
          selectedWorkbookFile = event.target.files && event.target.files[0] ? event.target.files[0] : null;
          document.getElementById('selectedFileName').textContent = selectedWorkbookFile ? selectedWorkbookFile.name : 'Belum ada file';
        });
        document.getElementById('importExcelBtn').onclick = function () { importWorkbookFromFile(selectedWorkbookFile); };
        document.getElementById('importExcelSaveDbBtn').onclick = function () { importExcelToDatabase(); };
        document.getElementById('downloadTemplateBtn').onclick = function () { downloadTemplateWorkbook(); };
        document.getElementById('resetBtn').onclick = function () {
          restoreSeedProjects();
          selectedWorkbookFile = null;
          document.getElementById('excelFileInput').value = '';
          document.getElementById('selectedFileName').textContent = 'Belum ada file';
          renderAll();
          closeModal();
          setImportStatus('Siap', 'Data dikembalikan ke contoh default di browser. Belum mengubah database — gunakan Simpan ke server jika perlu.', 'notice-card');
        };
        document.getElementById('modalClose').onclick = closeModal;
        document.getElementById('modalOverlay').onclick = function (event) {
          if (event.target.id === 'modalOverlay') closeModal();
        };
        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape') closeModal();
        });
        document.addEventListener('click', function (event) {
          const btn = event.target.closest('[data-action]');
          if (!btn) return;
          const p = Number(btn.dataset.project);
          const period = Number(btn.dataset.period);
          const gate = Number(btn.dataset.gate);
          const metric = Number(btn.dataset.metric);
          const task = Number(btn.dataset.task);
          switch (btn.dataset.action) {
            case 'toggle-dashboard-project':
              if (expandedDashboardProjects.has(p)) expandedDashboardProjects.delete(p); else expandedDashboardProjects.add(p);
              renderAll();
              break;
            case 'open-project-pdf':
              openProjectPdfModal(p);
              break;
            case 'toggle-project':
              if (expandedProjects.has(p)) expandedProjects.delete(p); else expandedProjects.add(p);
              renderAll();
              break;
            case 'add-project': addProject(); break;
            case 'duplicate-project': duplicateProject(p); break;
            case 'remove-project': removeProject(p); break;
            case 'add-period': addPeriod(p); break;
            case 'remove-period': removePeriod(p, period); break;
            case 'add-task': addTask(p, period); break;
            case 'remove-task': removeTask(p, period, task); break;
            case 'add-gate': addGate(p); break;
            case 'remove-gate': removeGate(p, gate); break;
            case 'open-gate': openGateModal(p, gate); break;
            case 'add-metric': addMetric(p, gate); break;
            case 'remove-metric': removeMetric(p, gate, metric); break;
          }
        });
        document.addEventListener('change', function (event) {
          const t = event.target;
          if (!t || !t.dataset) return;
          if (t.dataset.role === 'project-field') updateProjectField(Number(t.dataset.project), t.dataset.field, t.value);
          if (t.dataset.role === 'project-progress-number') updateProjectField(Number(t.dataset.project), 'progress', t.value);
          if (t.dataset.role === 'period-field') updatePeriodField(Number(t.dataset.project), Number(t.dataset.period), t.dataset.field, t.value);
          if (t.dataset.role === 'task-field') updateTaskField(Number(t.dataset.project), Number(t.dataset.period), Number(t.dataset.task), t.dataset.field, t.value);
          if (t.dataset.role === 'gate-field') updateGateField(Number(t.dataset.project), Number(t.dataset.gate), t.dataset.field, t.value);
          if (t.dataset.role === 'metric-field' && (t.tagName === 'SELECT' || t.type === 'number' || t.type === 'text' || t.tagName === 'TEXTAREA')) updateMetricField(Number(t.dataset.project), Number(t.dataset.gate), Number(t.dataset.metric), t.dataset.field, t.value);
        });
        document.addEventListener('input', function (event) {
          const t = event.target;
          if (!t || !t.dataset) return;
          if (t.dataset.role === 'project-progress-range') updateProjectField(Number(t.dataset.project), 'progress', t.value);
          if (t.dataset.role === 'metric-field' && t.type === 'range') updateMetricField(Number(t.dataset.project), Number(t.dataset.gate), Number(t.dataset.metric), t.dataset.field, t.value);
        });
      }

      bindGlobalEvents();
      setImportStatus('Memuat…', 'Mengambil data dashboard dari database.', 'notice-card');
      loadPortfolioFromServer();
    })();
  </script>
</body>
</html>
