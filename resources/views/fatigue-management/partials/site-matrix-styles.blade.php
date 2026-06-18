{{-- Matriks Program × Evidence — selaras gaya PembatasanLV --}}
<style>
   .fm-mx { --fm-mx-ease: cubic-bezier(0.4, 0, 0.2, 1); }

   .fm-mx-surface {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(57, 82, 188, 0.07);
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04), 0 8px 24px -6px rgba(57, 82, 188, 0.08);
      transition: box-shadow 0.35s var(--fm-mx-ease), border-color 0.35s var(--fm-mx-ease);
   }
   .fm-mx-surface:hover {
      box-shadow: 0 2px 4px rgba(44, 47, 49, 0.05), 0 14px 32px -8px rgba(57, 82, 188, 0.12);
      border-color: rgba(57, 82, 188, 0.12);
   }

   .fm-mx-stat {
      position: relative;
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(57, 82, 188, 0.07);
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04), 0 8px 24px -6px rgba(57, 82, 188, 0.08);
      border-radius: 1rem;
      padding: 1rem 1.1rem;
      min-height: 5.5rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
   }
   .fm-mx-stat__label {
      font-size: 0.625rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #595c5e;
   }
   .fm-mx-stat__value {
      margin-top: 0.35rem;
      font-family: Poppins, sans-serif;
      font-weight: 700;
      font-size: 1.625rem;
      line-height: 1;
      color: #2c2f31;
      font-variant-numeric: tabular-nums;
   }
   .fm-mx-stat__icon {
      position: absolute;
      top: 0.85rem;
      right: 0.85rem;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 2rem;
      height: 2rem;
      border-radius: 0.65rem;
      background: rgba(57, 82, 188, 0.08);
      color: #3952bc;
   }
   .fm-mx-stat__icon .material-symbols-outlined { font-size: 1.125rem; }

   .fm-mx-scroll {
      overflow: auto;
      max-height: min(68vh, 600px);
   }

   .fm-mx-table {
      width: max-content;
      min-width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      font-size: 0.8125rem;
   }
   .fm-mx-table thead th {
      position: sticky;
      z-index: 20;
      background: rgba(250, 251, 252, 0.96);
      backdrop-filter: blur(6px);
      font-weight: 600;
      letter-spacing: 0.04em;
   }
   .fm-mx-table thead tr:first-child th { top: 0; }
   .fm-mx-table thead tr:nth-child(2) th { top: 2.125rem; }
   .fm-mx-table.fm-mx-table--single-site thead tr:first-child th { top: 0; }

   .fm-mx-corner {
      left: 0;
      z-index: 30 !important;
      min-width: 15rem;
      max-width: 15rem;
      padding: 0.65rem 0.85rem !important;
      text-align: left;
      font-size: 0.625rem;
      font-weight: 600;
      text-transform: uppercase;
      color: rgba(89, 92, 94, 0.75);
      border-bottom: 1px solid rgba(171, 173, 175, 0.2);
      border-right: 1px solid rgba(171, 173, 175, 0.15);
      background: rgba(250, 251, 252, 0.98) !important;
   }

   .fm-mx-site-th {
      text-align: center;
      font-size: 0.625rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.07em;
      padding: 0.55rem 0.45rem !important;
      color: #3952bc;
      background: rgba(57, 82, 188, 0.06);
      border-bottom: 1px solid rgba(57, 82, 188, 0.1);
      border-right: 1px solid rgba(57, 82, 188, 0.06);
   }
   .fm-mx-site-th + .fm-mx-site-th {
      border-left: 1px solid rgba(171, 173, 175, 0.12);
   }

   .fm-mx-partner-th {
      text-align: center;
      font-size: 0.625rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      padding: 0.45rem 0.35rem !important;
      min-width: 4.5rem;
      color: #595c5e;
      border-bottom: 1px solid rgba(171, 173, 175, 0.2);
      border-right: 1px solid rgba(171, 173, 175, 0.08);
   }
   .fm-mx-partner-th--sep {
      border-left: 1px solid rgba(171, 173, 175, 0.15);
   }

   .fm-mx-group-row td {
      padding: 0 !important;
      border: none;
      background: #fafbfc;
   }
   .fm-mx-group-band {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      font-size: 0.625rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #595c5e;
      border-top: 1px solid rgba(171, 173, 175, 0.12);
      border-bottom: 1px solid rgba(171, 173, 175, 0.1);
   }
   .fm-mx-group-band--orange { border-left: 3px solid #f97316; }
   .fm-mx-group-band--yellow { border-left: 3px solid #eab308; }
   .fm-mx-group-band--green { border-left: 3px solid #22c55e; }
   .fm-mx-group-band--blue { border-left: 3px solid #3b82f6; }
   .fm-mx-group-band--slate { border-left: 3px solid #64748b; }

   .fm-mx-program-cell {
      position: sticky;
      left: 0;
      z-index: 10;
      min-width: 15rem;
      max-width: 15rem;
      padding: 0.6rem 0.85rem !important;
      background: #fff;
      border-right: 1px solid rgba(171, 173, 175, 0.15);
      border-bottom: 1px solid rgba(171, 173, 175, 0.08);
      vertical-align: middle;
   }
   .fm-mx-table tbody tr:hover .fm-mx-program-cell {
      background: #fafbfc;
   }
   .fm-mx-table tbody tr:hover td:not(.fm-mx-program-cell) {
      background: rgba(248, 250, 252, 0.7);
   }

   .fm-mx-program-title {
      font-size: 0.75rem;
      font-weight: 600;
      color: #2c2f31;
      line-height: 1.35;
   }
   .fm-mx-program-meta {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.35rem;
      margin-top: 0.35rem;
   }
   .fm-mx-program-no {
      font-size: 0.625rem;
      font-weight: 700;
      color: #3952bc;
      background: rgba(57, 82, 188, 0.08);
      border-radius: 0.35rem;
      padding: 0.1rem 0.4rem;
   }
   .fm-mx-freq-chip {
      display: inline-flex;
      align-items: center;
      gap: 0.15rem;
      font-size: 0.625rem;
      font-weight: 600;
      color: #595c5e;
   }
   .fm-mx-freq-chip .material-symbols-outlined {
      font-size: 0.75rem;
      opacity: 0.55;
   }

   .fm-mx-data-cell {
      text-align: center;
      padding: 0.5rem 0.35rem !important;
      border-bottom: 1px solid rgba(171, 173, 175, 0.08);
      border-right: 1px solid rgba(171, 173, 175, 0.05);
      vertical-align: middle;
      min-width: 4.5rem;
   }
   .fm-mx-data-cell--sep {
      border-left: 1px solid rgba(171, 173, 175, 0.12);
   }

   .fm-ev-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.2rem;
      min-width: 4.25rem;
      border-radius: 9999px;
      padding: 0.2rem 0.45rem;
      font-size: 0.5625rem;
      font-weight: 700;
      letter-spacing: 0.02em;
      white-space: nowrap;
      line-height: 1.2;
   }
   .fm-ev-badge--ok {
      border: 1px solid #a7f3d0;
      background: #ecfdf5;
      color: #047857;
   }
   .fm-ev-badge--warn {
      border: 1px solid #fed7aa;
      background: #fff7ed;
      color: #c2410c;
   }
   .fm-ev-badge--bad {
      border: 1px solid #fecaca;
      background: #fef2f2;
      color: #b91c1c;
   }
   .fm-ev-badge--na {
      border: 1px solid transparent;
      background: transparent;
      color: #cbd5e1;
      font-weight: 600;
   }
   .fm-ev-badge .material-symbols-outlined {
      font-size: 0.75rem;
   }

   .fm-mx-empty-row {
      padding: 2.5rem 1rem !important;
      text-align: center;
      color: #595c5e;
      font-size: 0.8125rem;
   }

   .fm-mx-risk-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(10.5rem, 1fr));
      gap: 0.65rem;
   }
   .fm-mx-risk-card {
      border-radius: 0.75rem;
      padding: 0.75rem 0.85rem;
      border: 1px solid rgba(171, 173, 175, 0.2);
      background: #fafbfc;
   }
   .fm-mx-risk-card--best { border-left: 3px solid #10b981; }
   .fm-mx-risk-card--unstable { border-left: 3px solid #f97316; }
   .fm-mx-risk-card--high { border-left: 3px solid #ef4444; }

   .fm-mx-risk-site {
      font-size: 0.625rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #595c5e;
   }
   .fm-mx-risk-tier {
      font-size: 0.6875rem;
      font-weight: 700;
      margin-top: 0.25rem;
      line-height: 1.3;
   }
   .fm-mx-risk--best { color: #047857; }
   .fm-mx-risk--unstable { color: #c2410c; }
   .fm-mx-risk--high { color: #b91c1c; }

   .fm-mx-risk-meter {
      margin-top: 0.45rem;
      height: 0.25rem;
      border-radius: 9999px;
      background: #e2e8f0;
      overflow: hidden;
   }
   .fm-mx-risk-meter-fill {
      height: 100%;
      border-radius: 9999px;
      background: #3952bc;
   }
   .fm-mx-risk-avg {
      font-size: 0.5625rem;
      color: #595c5e;
      margin-top: 0.3rem;
      font-weight: 600;
   }
   .fm-mx-risk-count {
      display: inline-flex;
      align-items: center;
      gap: 0.2rem;
      margin-top: 0.35rem;
      font-size: 0.5625rem;
      font-weight: 600;
   }
   .fm-mx-risk-count--warn { color: #c2410c; }
   .fm-mx-risk-count--danger { color: #b91c1c; }
   .fm-mx-risk-count--ok { color: #047857; }

   .fm-mx-legend {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.65rem 1rem;
      padding: 0.75rem 1.25rem;
      background: #fafbfc;
      border-top: 1px solid rgba(171, 173, 175, 0.15);
      font-size: 0.625rem;
      font-weight: 600;
      color: #595c5e;
   }
</style>
