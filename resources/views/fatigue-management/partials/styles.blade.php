<style>
   .fm-mon { --fm-ease: cubic-bezier(0.4, 0, 0.2, 1); }
   .fm-mon-card {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(57, 82, 188, 0.07);
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04), 0 8px 24px -6px rgba(57, 82, 188, 0.08);
      border-radius: 1rem;
   }
   .fm-status {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      border-radius: 9999px;
      padding: 0.25rem 0.65rem;
      font-size: 0.6875rem;
      font-weight: 700;
      letter-spacing: 0.02em;
      white-space: nowrap;
   }
   .fm-status--gray { background: #f1f5f9; color: #64748b; }
   .fm-status--blue { background: #eef2ff; color: #3952bc; }
   .fm-status--amber { background: #fff7ed; color: #c2410c; }
   .fm-status--green { background: #ecfdf5; color: #047857; }
   .fm-status--indigo { background: #e0e7ff; color: #4338ca; }
   .fm-status--red { background: #fef2f2; color: #b91c1c; }
   .fm-type-pill {
      display: inline-flex;
      border-radius: 0.375rem;
      padding: 0.15rem 0.45rem;
      font-size: 0.625rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
   }
   .fm-type-pill--mandatory { background: #dcfce7; color: #15803d; }
   .fm-type-pill--upgrade { background: #dbeafe; color: #1d4ed8; }
   .fm-type-pill--mitra { background: #eef2ff; color: #3952bc; }
   .fm-filter-pill {
      background: #ffffff;
      border: 1px solid rgba(171, 173, 175, 0.28);
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04);
   }
   .fm-action-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      border-radius: 0.625rem;
      padding: 0.4rem 0.65rem;
      font-size: 0.6875rem;
      font-weight: 700;
      transition: opacity 0.2s var(--fm-ease);
   }
   .fm-action-btn:hover { opacity: 0.92; }
   .fm-action-btn--primary { background: #3952bc; color: #fff; }
   .fm-action-btn--ghost {
      background: #fff;
      border: 1px solid rgba(171, 173, 175, 0.35);
      color: #2c2f31;
   }
   .fm-modal-backdrop {
      background: rgba(15, 23, 42, 0.45);
      backdrop-filter: blur(4px);
   }
   .fm-modal-panel { max-height: min(85vh, 640px); }
   .fm-check-yes { color: #047857; }
   .fm-check-no { color: #b91c1c; }

   /* Daftar perusahaan — kartu premium */
   .fm-company-list {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1rem;
      padding: 1.25rem 1.5rem 1.5rem;
      background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
   }
   @media (min-width: 1280px) {
      .fm-company-list { grid-template-columns: repeat(2, 1fr); gap: 1.125rem; }
   }
   .fm-company-card {
      position: relative;
      border-radius: 1rem;
      border: 1px solid rgba(226, 232, 240, 0.9);
      background: #fff;
      overflow: hidden;
      box-shadow:
         0 1px 2px rgba(15, 23, 42, 0.04),
         0 4px 16px -4px rgba(57, 82, 188, 0.06);
      transition:
         border-color 0.35s var(--fm-ease),
         box-shadow 0.35s var(--fm-ease),
         transform 0.35s var(--fm-ease);
      animation: fm-card-in 0.45s var(--fm-ease) both;
   }
   .fm-company-card::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 3px;
      background: var(--fm-accent, #3952bc);
      opacity: 0.85;
      transition: opacity 0.3s var(--fm-ease);
   }
   .fm-company-card:nth-child(1) { animation-delay: 0.03s; }
   .fm-company-card:nth-child(2) { animation-delay: 0.06s; }
   .fm-company-card:nth-child(3) { animation-delay: 0.09s; }
   .fm-company-card:nth-child(4) { animation-delay: 0.12s; }
   .fm-company-card:nth-child(n+5) { animation-delay: 0.15s; }
   @keyframes fm-card-in {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
   }
   .fm-company-card:hover {
      border-color: rgba(57, 82, 188, 0.12);
      box-shadow:
         0 4px 6px -2px rgba(15, 23, 42, 0.05),
         0 12px 28px -8px rgba(57, 82, 188, 0.12);
      transform: translateY(-1px);
   }
   .fm-company-card.is-open {
      grid-column: 1 / -1;
      border-color: rgba(57, 82, 188, 0.16);
      box-shadow:
         0 8px 24px -6px rgba(57, 82, 188, 0.14),
         0 2px 8px rgba(15, 23, 42, 0.04);
      transform: none;
   }
   .fm-company-card.is-open::before { opacity: 1; }
   .fm-company-card--complete { --fm-accent: #047857; --fm-accent-bg: #ecfdf5; }
   .fm-company-card--good { --fm-accent: #3952bc; --fm-accent-bg: #eef2ff; }
   .fm-company-card--warning { --fm-accent: #c2410c; --fm-accent-bg: #fff7ed; }
   .fm-company-card--critical { --fm-accent: #b91c1c; --fm-accent-bg: #fef2f2; }

   .fm-company-summary {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 1.125rem;
      padding: 1.25rem 1.35rem 1.15rem;
      cursor: pointer;
      text-align: left;
      width: 100%;
      background: linear-gradient(135deg, #ffffff 0%, #fcfdff 100%);
      border: none;
      transition: background 0.3s var(--fm-ease);
   }
   .fm-company-summary:hover { background: linear-gradient(135deg, #fafbff 0%, #f5f7ff 100%); }
   .fm-company-card.is-open .fm-company-summary {
      background: #fafbfc;
      border-bottom: 1px solid rgba(226, 232, 240, 0.8);
   }
   @media (min-width: 768px) {
      .fm-company-summary {
         grid-template-columns: auto 1fr auto;
         align-items: start;
      }
   }

   .fm-company-avatar {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 3.25rem;
      height: 3.25rem;
      border-radius: 0.9rem;
      background: linear-gradient(145deg, var(--fm-accent-bg, #eef2ff) 0%, #fff 120%);
      color: var(--fm-accent, #3952bc);
      font-weight: 800;
      font-size: 0.8rem;
      letter-spacing: 0.05em;
      flex-shrink: 0;
      border: 1px solid rgba(255, 255, 255, 0.9);
      box-shadow:
         inset 0 1px 0 rgba(255, 255, 255, 0.8),
         0 4px 12px -2px rgba(57, 82, 188, 0.12);
      transition: transform 0.3s var(--fm-ease), box-shadow 0.3s var(--fm-ease);
   }
   .fm-company-card:hover .fm-company-avatar {
      transform: scale(1.03);
      box-shadow:
         inset 0 1px 0 rgba(255, 255, 255, 0.8),
         0 6px 16px -2px rgba(57, 82, 188, 0.16);
   }

   .fm-company-meta { min-width: 0; }
   .fm-company-name {
      font-weight: 800;
      font-size: 1.05rem;
      color: #0f172a;
      line-height: 1.25;
      letter-spacing: -0.01em;
   }
   .fm-company-sub {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.4rem;
      margin-top: 0.35rem;
      font-size: 0.6875rem;
      color: #64748b;
   }
   .fm-tier-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      border-radius: 9999px;
      padding: 0.2rem 0.55rem;
      font-size: 0.625rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      background: var(--fm-accent-bg);
      color: var(--fm-accent);
   }

   .fm-ring-wrap {
      position: relative;
      width: 4.75rem;
      height: 4.75rem;
      flex-shrink: 0;
      filter: drop-shadow(0 2px 6px rgba(57, 82, 188, 0.08));
   }
   .fm-ring {
      width: 100%;
      height: 100%;
      transform: rotate(-90deg);
   }
   .fm-ring-bg { fill: none; stroke: #eef2f7; stroke-width: 2.75; }
   .fm-ring-fill {
      fill: none;
      stroke: var(--fm-accent, #3952bc);
      stroke-width: 2.75;
      stroke-linecap: round;
      transition: stroke-dasharray 0.8s cubic-bezier(0.34, 1.2, 0.64, 1);
   }
   .fm-ring-label {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      line-height: 1;
   }
   .fm-ring-pct { font-size: 1rem; color: var(--fm-accent, #3952bc); }
   .fm-ring-caption { font-size: 0.5rem; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8; margin-top: 0.15rem; }

   .fm-stat-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 0.55rem;
      margin-top: 1rem;
   }
   @media (min-width: 640px) {
      .fm-stat-grid { grid-template-columns: repeat(4, 1fr); }
   }
   .fm-stat-box {
      border-radius: 0.8rem;
      padding: 0.65rem 0.75rem;
      background: rgba(248, 250, 252, 0.85);
      border: 1px solid rgba(226, 232, 240, 0.7);
      transition:
         background 0.25s var(--fm-ease),
         border-color 0.25s var(--fm-ease),
         transform 0.25s var(--fm-ease);
   }
   .fm-company-summary:hover .fm-stat-box {
      background: #fff;
      border-color: rgba(226, 232, 240, 0.95);
   }
   .fm-stat-box__value {
      font-size: 1.2rem;
      font-weight: 800;
      font-variant-numeric: tabular-nums;
      line-height: 1.1;
      letter-spacing: -0.02em;
   }
   .fm-stat-box__label {
      font-size: 0.58rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #94a3b8;
      margin-top: 0.2rem;
   }
   .fm-stat-box--submit .fm-stat-box__value { color: #3952bc; }
   .fm-stat-box--pending .fm-stat-box__value { color: #c2410c; }
   .fm-stat-box--check .fm-stat-box__value { color: #047857; }
   .fm-stat-box--verify .fm-stat-box__value { color: #6366f1; }

   .fm-type-bars {
      display: flex;
      flex-direction: column;
      gap: 0.55rem;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid rgba(226, 232, 240, 0.65);
   }
   .fm-type-bar-row {
      display: grid;
      grid-template-columns: auto 1fr 2.75rem;
      gap: 0.65rem;
      align-items: center;
   }
   .fm-freq-bar-label {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      min-width: 5.5rem;
      font-size: 0.65rem;
      font-weight: 700;
      color: #64748b;
      white-space: nowrap;
   }
   .fm-freq-bar-dot {
      width: 0.45rem;
      height: 0.45rem;
      border-radius: 9999px;
      flex-shrink: 0;
   }
   .fm-freq-bar-dot--shift { background: linear-gradient(135deg, #fb923c, #ea580c); }
   .fm-freq-bar-dot--daily { background: linear-gradient(135deg, #4ade80, #16a34a); }
   .fm-freq-bar-dot--weekly { background: linear-gradient(135deg, #60a5fa, #2563eb); }
   .fm-type-bar-track {
      height: 0.5rem;
      border-radius: 9999px;
      background: #eef2f7;
      overflow: hidden;
      box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
   }
   .fm-type-bar-fill {
      height: 100%;
      border-radius: 9999px;
      width: 0;
      transition: width 0.9s cubic-bezier(0.34, 1.15, 0.64, 1);
      box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
   }
   .fm-type-bar-fill.is-animated { /* width set via inline style + class */ }
   .fm-type-bar-fill--shift { background: linear-gradient(90deg, #fdba74, #f97316); }
   .fm-type-bar-fill--daily { background: linear-gradient(90deg, #86efac, #22c55e); }
   .fm-type-bar-fill--weekly { background: linear-gradient(90deg, #93c5fd, #3b82f6); }
   .fm-type-bar-count {
      font-size: 0.65rem;
      font-weight: 700;
      font-variant-numeric: tabular-nums;
      color: #64748b;
      text-align: right;
   }

   .fm-type-bar-fill--mandatory { background: linear-gradient(90deg, #86efac, #22c55e); }
   .fm-type-bar-fill--upgrade { background: linear-gradient(90deg, #93c5fd, #3b82f6); }
   .fm-type-bar-fill--mitra { background: linear-gradient(90deg, #a5b4fc, #3952bc); }

   .fm-detail-toggle {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.35rem;
      margin-top: 0.85rem;
      padding: 0.5rem 0.85rem;
      border-radius: 9999px;
      font-size: 0.6875rem;
      font-weight: 700;
      color: var(--fm-accent, #3952bc);
      background: rgba(255, 255, 255, 0.9);
      border: 1px solid rgba(57, 82, 188, 0.1);
      width: 100%;
      transition:
         background 0.25s var(--fm-ease),
         border-color 0.25s var(--fm-ease),
         color 0.25s var(--fm-ease);
   }
   .fm-company-summary:hover .fm-detail-toggle {
      background: var(--fm-accent-bg, #eef2ff);
      border-color: rgba(57, 82, 188, 0.15);
   }
   .fm-detail-toggle .material-symbols-outlined {
      font-size: 1.1rem;
      transition: transform 0.35s cubic-bezier(0.34, 1.2, 0.64, 1);
   }
   .fm-company-card.is-open .fm-detail-toggle .material-symbols-outlined { transform: rotate(180deg); }

   .fm-program-panel-wrap {
      display: grid;
      grid-template-rows: 0fr;
      transition: grid-template-rows 0.4s cubic-bezier(0.4, 0, 0.2, 1);
   }
   .fm-company-card.is-open .fm-program-panel-wrap { grid-template-rows: 1fr; }
   .fm-program-panel-inner { overflow: hidden; min-height: 0; }

   .fm-program-panel {
      border-top: none;
      background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
      padding: 1.25rem 1.35rem 1.5rem;
      opacity: 0;
      transform: translateY(-4px);
      transition:
         opacity 0.35s var(--fm-ease) 0.05s,
         transform 0.35s var(--fm-ease) 0.05s;
   }
   .fm-company-card.is-open .fm-program-panel {
      opacity: 1;
      transform: translateY(0);
   }

   .fm-detail-header {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 0.75rem;
      margin-bottom: 1rem;
      padding: 0.75rem 1rem;
      border-radius: 0.75rem;
      background: #fff;
      border: 1px solid rgba(57, 82, 188, 0.08);
   }
   .fm-detail-header h3 {
      font-size: 0.75rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #64748b;
   }
   .fm-detail-chips { display: flex; flex-wrap: wrap; gap: 0.35rem; }
   .fm-detail-chip {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      border-radius: 9999px;
      padding: 0.2rem 0.55rem;
      font-size: 0.625rem;
      font-weight: 700;
      background: #f1f5f9;
      color: #475569;
   }

   .fm-program-section { margin-bottom: 1.25rem; }
   .fm-program-section:last-child { margin-bottom: 0; }
   .fm-program-section-title {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.6875rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #475569;
      margin-bottom: 0.65rem;
      padding-bottom: 0.4rem;
      border-bottom: 2px solid rgba(57, 82, 188, 0.08);
   }
   .fm-program-section-count {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 1.25rem;
      height: 1.25rem;
      padding: 0 0.35rem;
      border-radius: 9999px;
      background: #eef2ff;
      color: #3952bc;
      font-size: 0.6rem;
   }

   .fm-program-item {
      display: flex;
      align-items: flex-start;
      gap: 0.75rem;
      padding: 0.8rem 0.9rem;
      border-radius: 0.8rem;
      background: #fff;
      border: 1px solid rgba(226, 232, 240, 0.8);
      margin-bottom: 0.5rem;
      box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
      transition:
         border-color 0.25s var(--fm-ease),
         box-shadow 0.25s var(--fm-ease),
         transform 0.25s var(--fm-ease);
   }
   .fm-program-item:hover {
      border-color: rgba(57, 82, 188, 0.12);
      box-shadow: 0 4px 12px -4px rgba(57, 82, 188, 0.1);
      transform: translateX(2px);
   }
   .fm-program-item.is-done {
      border-left: 3px solid #22c55e;
      background: linear-gradient(90deg, #f0fdf4 0%, #fff 28%);
   }
   .fm-program-item.is-pending { border-left: 3px solid #e2e8f0; }
   .fm-program-item:last-child { margin-bottom: 0; }

   .fm-program-check-wrap {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 1.5rem;
      height: 1.5rem;
      border-radius: 0.4rem;
      flex-shrink: 0;
      margin-top: 0.1rem;
   }
   .fm-program-check-wrap.is-done { background: #ecfdf5; color: #047857; }
   .fm-program-check-wrap.is-pending { background: #f8fafc; color: #cbd5e1; border: 1px solid #e2e8f0; }
   .fm-program-check-wrap .material-symbols-outlined { font-size: 1.1rem; }

   .fm-freq-pill {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      border-radius: 0.375rem;
      padding: 0.15rem 0.45rem;
      font-size: 0.625rem;
      font-weight: 700;
      background: #f1f5f9;
      color: #475569;
      white-space: nowrap;
   }
   .fm-freq-slots {
      display: flex;
      flex-wrap: wrap;
      gap: 0.25rem;
      margin-top: 0.4rem;
   }
   .fm-freq-slot {
      width: 1.45rem;
      height: 1.45rem;
      border-radius: 0.4rem;
      border: 1px solid #e2e8f0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.55rem;
      font-weight: 700;
      color: #94a3b8;
      background: #fff;
      transition:
         background 0.2s var(--fm-ease),
         border-color 0.2s var(--fm-ease),
         transform 0.2s var(--fm-ease);
   }
   .fm-freq-slot.is-done {
      background: #ecfdf5;
      border-color: #86efac;
      color: #15803d;
      transform: scale(1.02);
   }

   /* Ringkasan frekuensi — kartu atas */
   .fm-freq-summary-card {
      transition:
         transform 0.3s var(--fm-ease),
         box-shadow 0.3s var(--fm-ease);
   }
   .fm-freq-summary-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px -8px rgba(57, 82, 188, 0.14);
   }

   /* Upload — slot grid per frekuensi */
   .fm-freq-section-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 2rem;
      height: 2rem;
      border-radius: 0.5rem;
   }
   .fm-freq-section-icon--shift { background: #fff7ed; color: #c2410c; }
   .fm-freq-section-icon--daily { background: #ecfdf5; color: #047857; }
   .fm-freq-section-icon--weekly { background: #eef2ff; color: #3952bc; }

   .fm-slot-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 0.35rem;
   }
   .fm-slot-grid--shift { max-width: 22rem; }
   .fm-slot-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      border-radius: 0.5rem;
      padding: 0.3rem 0.5rem;
      font-size: 0.625rem;
      font-weight: 700;
      border: 1px solid #e2e8f0;
      background: #fff;
      color: #64748b;
      cursor: pointer;
      transition: border-color 0.15s ease, background 0.15s ease;
      max-width: 100%;
   }
   .fm-slot-btn:hover {
      border-color: rgba(57, 82, 188, 0.35);
      background: #fafbff;
   }
   .fm-slot-btn--done {
      background: #ecfdf5;
      border-color: #a7f3d0;
      color: #047857;
   }
   .fm-slot-btn--pending {
      background: #f8fafc;
      border-style: dashed;
   }
   .fm-slot-btn--active {
      background: #fff7ed;
      border-color: #fdba74;
      border-style: solid;
      color: #c2410c;
      box-shadow: 0 0 0 2px rgba(251, 146, 60, 0.15);
   }
   .fm-slot-btn--locked {
      background: #f1f5f9;
      border-color: #e2e8f0;
      color: #94a3b8;
      cursor: not-allowed;
      opacity: 0.85;
   }
   .fm-slot-btn__window {
      display: block;
      font-size: 0.5rem;
      font-weight: 600;
      opacity: 0.75;
      line-height: 1.1;
   }
   .fm-slot-btn--active .fm-slot-btn__window,
   .fm-slot-btn--done .fm-slot-btn__window {
      display: block;
   }
   .fm-slot-btn__label {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 6.5rem;
   }
   .fm-slot-btn__icon {
      font-size: 0.875rem !important;
      flex-shrink: 0;
   }
   .fm-slot-btn__file {
      display: inline-flex;
      color: #3952bc;
      margin-left: 0.1rem;
   }
   .fm-upload-table td { vertical-align: top; }

   /* Tutorial upload */
   .fm-upload-tutorial {
      border: 1px solid rgba(57, 82, 188, 0.12);
      border-radius: 1rem;
      background: #fff;
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04);
      overflow: hidden;
   }
   .fm-upload-tutorial__toggle {
      display: flex;
      align-items: center;
      gap: 0.875rem;
      padding: 1rem 1.25rem;
      cursor: pointer;
      list-style: none;
      user-select: none;
   }
   .fm-upload-tutorial__toggle::-webkit-details-marker { display: none; }
   .fm-upload-tutorial__toggle-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 2.5rem;
      height: 2.5rem;
      border-radius: 0.75rem;
      background: rgba(57, 82, 188, 0.1);
      color: #3952bc;
      flex-shrink: 0;
   }
   .fm-upload-tutorial__body {
      padding: 0 1.25rem 1.25rem;
      border-top: 1px solid rgba(171, 173, 175, 0.2);
   }
   .fm-tutorial-steps {
      list-style: none;
      margin: 1rem 0 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      gap: 1rem;
   }
   .fm-tutorial-step {
      display: flex;
      gap: 0.875rem;
      align-items: flex-start;
   }
   .fm-tutorial-step__num {
      flex-shrink: 0;
      width: 1.75rem;
      height: 1.75rem;
      border-radius: 0.5rem;
      background: #3952bc;
      color: #fff;
      font-size: 0.75rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
   }
   .fm-tutorial-step__title {
      font-size: 0.875rem;
      font-weight: 700;
      color: #2c2f31;
      margin: 0 0 0.25rem;
   }
   .fm-tutorial-step__desc {
      font-size: 0.8125rem;
      color: #64748b;
      margin: 0;
      line-height: 1.5;
   }
   .fm-tutorial-slot-legend {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-top: 0.75rem;
   }
   .fm-tutorial-slot-demo {
      pointer-events: none;
      cursor: default;
      min-width: 7rem;
   }
   .fm-tutorial-legend-labels {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      margin-top: 0.5rem;
      font-size: 0.6875rem;
      color: #64748b;
   }
   .fm-tutorial-file-list {
      margin: 0.5rem 0 0;
      padding-left: 1.125rem;
      font-size: 0.75rem;
      color: #64748b;
   }
   .fm-tutorial-file-list li { margin-bottom: 0.25rem; }
   .fm-tutorial-rules {
      margin-top: 1.25rem;
      padding-top: 1rem;
      border-top: 1px dashed rgba(171, 173, 175, 0.35);
   }
   .fm-tutorial-rules__grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 0.75rem;
   }
   .fm-tutorial-rules__item {
      display: flex;
      gap: 0.625rem;
      align-items: flex-start;
      padding: 0.75rem;
      border-radius: 0.75rem;
      background: #f8fafc;
      border: 1px solid rgba(171, 173, 175, 0.2);
      font-size: 0.75rem;
      color: #64748b;
   }
   .fm-tutorial-rules__item p { margin: 0; line-height: 1.45; }
   .fm-tutorial-rules__item strong {
      display: block;
      color: #2c2f31;
      font-size: 0.8125rem;
      margin-bottom: 0.2rem;
   }
   .fm-tutorial-rules__note {
      margin: 0.75rem 0 0;
      font-size: 0.6875rem;
      color: #64748b;
      padding: 0.625rem 0.75rem;
      background: #eff6ff;
      border-radius: 0.5rem;
      border-left: 3px solid #3952bc;
   }

   /* Tutorial full page */
   .fm-tutorial-full__hero {
      background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
   }
   .fm-tutorial-full__hero-icon {
      width: 4rem;
      height: 4rem;
      border-radius: 1rem;
      background: #fff;
      border: 1px solid rgba(57, 82, 188, 0.12);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
   }
   .fm-tutorial-full__meta-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(5rem, 1fr));
      gap: 0.75rem;
   }
   .fm-tutorial-full__meta-grid dt {
      font-size: 0.625rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #64748b;
   }
   .fm-tutorial-full__meta-grid dd {
      font-size: 0.8125rem;
      font-weight: 700;
      color: #2c2f31;
      margin: 0.15rem 0 0;
   }
   .fm-tutorial-full__step {
      display: flex;
      gap: 1rem;
      padding-bottom: 1.75rem;
      margin-bottom: 1.75rem;
      border-bottom: 1px dashed rgba(171, 173, 175, 0.35);
      position: relative;
   }
   .fm-tutorial-full__step--last {
      border-bottom: none;
      margin-bottom: 0;
      padding-bottom: 0;
   }
   .fm-tutorial-full__step-badge {
      flex-shrink: 0;
      width: 2.25rem;
      height: 2.25rem;
      border-radius: 0.625rem;
      background: #3952bc;
      color: #fff;
      font-size: 0.9375rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
   }
   .fm-tutorial-full__step-content { flex: 1; min-width: 0; }
   .fm-tutorial-full__step-content h3 {
      font-size: 1rem;
      font-weight: 700;
      color: #2c2f31;
      margin: 0 0 0.5rem;
   }
   .fm-tutorial-full__step-content > p {
      font-size: 0.875rem;
      color: #64748b;
      margin: 0;
      line-height: 1.6;
   }
   .fm-tutorial-full__table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.8125rem;
      margin-top: 1rem;
   }
   .fm-tutorial-full__table th,
   .fm-tutorial-full__table td {
      border: 1px solid rgba(171, 173, 175, 0.25);
      padding: 0.625rem 0.75rem;
      text-align: left;
      vertical-align: top;
   }
   .fm-tutorial-full__table th {
      background: #f1f5f9;
      font-size: 0.6875rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #64748b;
   }
   .fm-tutorial-full__table--compact { max-width: 36rem; }
   .fm-tutorial-full__callout {
      display: flex;
      gap: 0.625rem;
      align-items: flex-start;
      margin-top: 0.875rem;
      padding: 0.75rem 1rem;
      border-radius: 0.625rem;
      font-size: 0.8125rem;
      line-height: 1.5;
   }
   .fm-tutorial-full__callout--info {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      color: #1e40af;
   }
   .fm-tutorial-full__callout--success {
      background: #ecfdf5;
      border: 1px solid #a7f3d0;
      color: #047857;
   }
   .fm-tutorial-full__mock {
      margin-top: 1rem;
      border: 1px solid rgba(171, 173, 175, 0.3);
      border-radius: 0.75rem;
      background: #fafbfc;
      overflow: hidden;
   }
   .fm-tutorial-full__mock-label {
      font-size: 0.625rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #64748b;
      padding: 0.5rem 0.75rem;
      background: #f1f5f9;
      border-bottom: 1px solid rgba(171, 173, 175, 0.2);
   }
   .fm-tutorial-full__mock--filter { padding: 0.75rem; }
   .fm-tutorial-full__mock--filter .flex { padding: 0.5rem; }
   .fm-tutorial-full__mock-field {
      background: #fff;
      border: 1px solid rgba(171, 173, 175, 0.3);
      border-radius: 0.5rem;
      padding: 0.4rem 0.75rem;
      font-size: 0.8125rem;
   }
   .fm-tutorial-full__mock-field span {
      display: block;
      font-size: 0.625rem;
      font-weight: 700;
      text-transform: uppercase;
      color: #64748b;
   }
   .fm-tutorial-full__mock-btn {
      display: inline-flex;
      align-items: center;
      padding: 0.45rem 1rem;
      border-radius: 0.625rem;
      background: #3952bc;
      color: #fff;
      font-size: 0.75rem;
      font-weight: 700;
   }
   .fm-tutorial-full__mock-btn--ghost {
      background: #fff;
      border: 1px solid rgba(171, 173, 175, 0.35);
      color: #2c2f31;
   }
   .fm-tutorial-full__mock-modal-header {
      padding: 1rem;
      border-bottom: 1px solid rgba(171, 173, 175, 0.2);
      background: #fff;
   }
   .fm-tutorial-full__mock-modal-body { padding: 1rem; }
   .fm-tutorial-full__mock-label-field {
      display: block;
      font-size: 0.625rem;
      font-weight: 700;
      text-transform: uppercase;
      color: #64748b;
      margin-bottom: 0.35rem;
   }
   .fm-tutorial-full__mock-file,
   .fm-tutorial-full__mock-input,
   .fm-tutorial-full__mock-textarea {
      background: #fff;
      border: 1px dashed rgba(171, 173, 175, 0.5);
      border-radius: 0.5rem;
      padding: 0.5rem 0.75rem;
      font-size: 0.8125rem;
      color: #94a3b8;
   }
   .fm-tutorial-full__mock-textarea { min-height: 3rem; }
   .fm-tutorial-full__slot-demo { margin-top: 0.75rem; }
   .fm-tutorial-full__dot {
      display: inline-block;
      width: 0.625rem;
      height: 0.625rem;
      border-radius: 50%;
      vertical-align: middle;
      margin-right: 0.25rem;
   }
   .fm-tutorial-full__dot--blue { background: #3952bc; }
   .fm-tutorial-full__dot--green { background: #047857; }
   .fm-tutorial-full__dot--gray { background: #94a3b8; }
   .fm-tutorial-full__faq {
      padding: 0.875rem 0;
   }
   .fm-tutorial-full__faq summary {
      font-size: 0.875rem;
      font-weight: 700;
      color: #2c2f31;
      cursor: pointer;
      list-style: none;
   }
   .fm-tutorial-full__faq summary::-webkit-details-marker { display: none; }
   .fm-tutorial-full__faq p {
      margin: 0.5rem 0 0;
      font-size: 0.8125rem;
      color: #64748b;
      line-height: 1.55;
      padding-left: 0.25rem;
   }
</style>
