<style>
   .ds-surface-card {
      background: rgba(255, 255, 255, 0.92);
      border: 1px solid rgba(57, 82, 188, 0.07);
      box-shadow: 0 1px 2px rgba(44, 47, 49, 0.04), 0 8px 24px -6px rgba(57, 82, 188, 0.08);
      transition: box-shadow 0.35s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.35s cubic-bezier(0.4, 0, 0.2, 1);
   }
   .ds-surface-card:hover {
      box-shadow: 0 2px 4px rgba(44, 47, 49, 0.05), 0 14px 32px -8px rgba(57, 82, 188, 0.12);
      border-color: rgba(57, 82, 188, 0.12);
   }
   .ds-badge {
      display: inline-flex;
      align-items: center;
      border-radius: 0.375rem;
      padding: 0.125rem 0.5rem;
      font-size: 10px;
      font-weight: 600;
      border: 1px solid rgba(57, 82, 188, 0.12);
      background: rgba(57, 82, 188, 0.06);
      color: #3952bc;
   }
   .ds-badge--success { background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2); color: #047857; }
   .ds-badge--warning { background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2); color: #b45309; }
   .ds-badge--danger { background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #b91c1c; }
   .ds-level-pill { font-size: 10px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; padding: 0.25rem 0.625rem; border-radius: 9999px; }
   .ds-level-pill--l1 { background: #dbeafe; color: #1d4ed8; }
   .ds-level-pill--l2 { background: #e0e7ff; color: #4338ca; }
   .ds-level-pill--l3 { background: #fef3c7; color: #b45309; }
   .ds-level-pill--l4 { background: #fce7f3; color: #be185d; }
   .ds-flow-step { position: relative; }
   .ds-flow-step:not(:last-child)::after {
      content: '↓';
      display: block;
      text-align: center;
      color: #3952bc;
      font-weight: 700;
      padding: 0.35rem 0;
      opacity: 0.5;
   }
   .ds-table thead th { font-weight: 600; letter-spacing: 0.04em; font-size: 11px; text-transform: uppercase; color: #595c5e; }
   .ds-table tbody tr { transition: background 0.2s ease; }
   .ds-table tbody tr:hover { background: rgba(57, 82, 188, 0.02); }
   .ds-progress-bar { height: 6px; border-radius: 9999px; background: #e8ecf4; overflow: hidden; }
   .ds-progress-fill { height: 100%; border-radius: 9999px; background: linear-gradient(90deg, #3952bc, #72479e); transition: width 0.8s ease; }
   .ds-alert-critical {
      border-left: 4px solid #b41340;
      background: linear-gradient(90deg, rgba(180, 19, 64, 0.06), rgba(255,255,255,0.95));
   }
</style>
