<!DOCTYPE html>
<html class="light" lang="en">
   <head>
      <meta charset="utf-8"/>
      <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
      <title>Executive Ledger | Enterprise Performance Monitoring</title>
      <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
      <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
      <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
      <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
      <script id="tailwind-config">
         tailwind.config = {
           darkMode: "class",
           theme: {
             extend: {
               "colors": {
                       "surface-container-high": "#e6e8eb",
                       "surface-container-highest": "#e0e3e6",
                       "secondary": "#48626e",
                       "surface-dim": "#d8dadd",
                       "on-secondary-fixed": "#021f29",
                       "surface-variant": "#e0e3e6",
                       "on-primary-container": "#7194cd",
                       "surface-container": "#eceef1",
                       "tertiary-container": "#5e0024",
                       "primary-fixed-dim": "#a7c8ff",
                       "secondary-container": "#cbe7f5",
                       "on-tertiary-container": "#ff5484",
                       "on-background": "#191c1e",
                       "on-tertiary-fixed": "#3f0016",
                       "background": "#f7f9fc",
                       "surface": "#f7f9fc",
                       "secondary-fixed-dim": "#afcbd8",
                       "on-error": "#ffffff",
                       "outline": "#747780",
                       "on-surface-variant": "#43474f",
                       "primary-container": "#002c59",
                       "tertiary-fixed-dim": "#ffb2bf",
                       "outline-variant": "#c4c6d0",
                       "surface-container-lowest": "#ffffff",
                       "primary": "#001734",
                       "error": "#ba1a1a",
                       "error-container": "#ffdad6",
                       "tertiary-fixed": "#ffd9de",
                       "on-secondary": "#ffffff",
                       "inverse-on-surface": "#eff1f4",
                       "on-tertiary-fixed-variant": "#90003b",
                       "surface-tint": "#3a5f94",
                       "on-secondary-container": "#4e6874",
                       "inverse-surface": "#2d3133",
                       "inverse-primary": "#a7c8ff",
                       "on-tertiary": "#ffffff",
                       "surface-bright": "#f7f9fc",
                       "on-error-container": "#93000a",
                       "tertiary": "#380012",
                       "on-surface": "#191c1e",
                       "surface-container-low": "#f2f4f7",
                       "on-primary": "#ffffff",
                       "on-primary-fixed-variant": "#1f477b",
                       "on-primary-fixed": "#001b3c",
                       "secondary-fixed": "#cbe7f5",
                       "primary-fixed": "#d5e3ff",
                       "on-secondary-fixed-variant": "#304a55"
               },
               "borderRadius": {
                       "DEFAULT": "0.125rem",
                       "lg": "0.25rem",
                       "xl": "0.5rem",
                       "full": "0.75rem"
               },
               "fontFamily": {
                       "headline": ["Manrope"],
                       "body": ["Inter"],
                       "label": ["Inter"]
               }
             },
           },
         }
      </script>
      <style>
         body { font-family: 'Inter', sans-serif; background-color: #f7f9fc; color: #191c1e; }
         .font-headline { font-family: 'Manrope', sans-serif; }
         .glass-effect { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(20px); }
         .no-scrollbar::-webkit-scrollbar { display: none; }
         .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
         .tonal-nesting { background-color: #f2f4f7; }
      </style>
   </head>
   <body class="bg-surface text-on-surface min-h-screen">
      <!-- Side Navigation Bar -->
      <aside class="h-screen w-64 fixed left-0 top-0 bg-slate-100 dark:bg-slate-950 flex flex-col p-4 gap-2 z-50">
         <div class="flex items-center gap-3 px-2 mb-8">
            <div class="w-10 h-10 rounded bg-primary flex items-center justify-center text-on-primary">
               <span class="material-symbols-outlined" data-icon="business_center">business_center</span>
            </div>
            <div>
               <h2 class="font-['Manrope'] font-extrabold text-blue-900 dark:text-blue-100 leading-none">BMO Operations</h2>
               <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">Enterprise Tier</p>
            </div>
         </div>
         <nav class="flex-1 flex flex-col gap-1">
            <a class="flex items-center gap-3 px-3 py-2.5 bg-white dark:bg-blue-900/30 text-blue-950 dark:text-blue-100 shadow-sm rounded-lg font-['Inter'] text-sm font-medium tracking-wide transition-transform duration-200 active:scale-95" href="#">
            <span class="material-symbols-outlined text-xl" data-icon="dashboard">dashboard</span>
            <span>Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-900 font-['Inter'] text-sm font-medium tracking-wide transition-transform duration-200 hover:translate-x-1" href="#">
            <span class="material-symbols-outlined text-xl" data-icon="business_center">business_center</span>
            <span>Business Units</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-900 font-['Inter'] text-sm font-medium tracking-wide transition-transform duration-200 hover:translate-x-1" href="#">
            <span class="material-symbols-outlined text-xl" data-icon="speed">speed</span>
            <span>Performance</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-900 font-['Inter'] text-sm font-medium tracking-wide transition-transform duration-200 hover:translate-x-1" href="#">
            <span class="material-symbols-outlined text-xl" data-icon="menu_book">menu_book</span>
            <span>Ledger</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-900 font-['Inter'] text-sm font-medium tracking-wide transition-transform duration-200 hover:translate-x-1" href="#">
            <span class="material-symbols-outlined text-xl" data-icon="inventory_2">inventory_2</span>
            <span>Archive</span>
            </a>
         </nav>
         <div class="mt-auto flex flex-col gap-1 border-t border-slate-200 pt-4">
            <a class="flex items-center gap-3 px-3 py-2 text-slate-500 font-medium text-sm hover:text-primary" href="#">
            <span class="material-symbols-outlined" data-icon="help">help</span>
            <span>Support</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2 text-slate-500 font-medium text-sm hover:text-error" href="#">
            <span class="material-symbols-outlined" data-icon="logout">logout</span>
            <span>Logout</span>
            </a>
         </div>
      </aside>
      <!-- Main Content Area -->
      <main class="ml-64 min-h-screen">
         <!-- Top App Bar -->
         <header class="sticky top-0 z-40 bg-slate-50/80 dark:bg-slate-900/80 backdrop-blur-xl shadow-sm">
            <div class="flex items-center justify-between px-8 py-4 w-full max-w-[1440px] mx-auto">
               <div class="flex items-center gap-8">
                  <h1 class="text-xl font-['Manrope'] font-bold tracking-tighter text-blue-950 dark:text-blue-50">Executive Ledger</h1>
                  <nav class="hidden md:flex items-center gap-6">
                     <a class="text-blue-900 dark:text-blue-200 border-b-2 border-blue-900 dark:border-blue-400 pb-1 font-['Manrope'] font-semibold tracking-tight text-sm" href="#">Overview</a>
                     <a class="text-slate-500 dark:text-slate-400 hover:text-blue-800 font-['Manrope'] font-semibold tracking-tight text-sm" href="#">Analytics</a>
                     <a class="text-slate-500 dark:text-slate-400 hover:text-blue-800 font-['Manrope'] font-semibold tracking-tight text-sm" href="#">Reporting</a>
                     <a class="text-slate-500 dark:text-slate-400 hover:text-blue-800 font-['Manrope'] font-semibold tracking-tight text-sm" href="#">Insights</a>
                  </nav>
               </div>
               <div class="flex items-center gap-4">
                  <div class="relative group">
                     <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 material-symbols-outlined text-sm">search</span>
                     <input class="pl-9 pr-4 py-2 bg-slate-200/50 border-none rounded-lg text-xs w-64 focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Global search..." type="text"/>
                  </div>
                  <div class="flex items-center gap-2">
                     <button class="p-2 hover:bg-slate-100 rounded-lg text-slate-600 transition-colors">
                     <span class="material-symbols-outlined" data-icon="filter_list">filter_list</span>
                     </button>
                     <button class="p-2 hover:bg-slate-100 rounded-lg text-slate-600 transition-colors">
                     <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                     </button>
                     <button class="p-2 hover:bg-slate-100 rounded-lg text-slate-600 transition-colors">
                     <span class="material-symbols-outlined" data-icon="settings">settings</span>
                     </button>
                  </div>
                  <div class="h-8 w-px bg-slate-200 mx-2"></div>
                  <div class="flex items-center gap-3">
                     <div class="text-right">
                        <p class="text-xs font-bold text-on-surface">Alex Mercer</p>
                        <p class="text-[10px] text-on-surface-variant font-medium">Operations Lead</p>
                     </div>
                     <img alt="User profile avatar" class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover" data-alt="professional headshot of a corporate executive in a modern office setting with soft natural light" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBDHemZVSedQoUHskD1AqB85NWDpIVCcfbcBMR9d3-2yPVL8ou2X6yQbFDRTSZ98KsUiGv6WWutFlTuO2G5ErOMQjxCN38WfgeguGxPp_LzWwmd-aNCRIXge30TwFK1STh9tjQSZvyGwKcEOFgLfqcuGWduA0aKjK93s5hDCUrNOI3fOxUsFYOOBVFVFQnK7xr6e22L69Muws-V5ew8M3b51fFe3QuZnusOL2n_QClFTKzARwHCkh39dPPclUMrz78m-BrndxGTBw"/>
                  </div>
               </div>
            </div>
            <div class="bg-slate-200/50 dark:bg-slate-800/50 h-px w-full"></div>
         </header>
         <!-- Content Canvas -->
         <div class="p-8 max-w-[1440px] mx-auto">
            <!-- Dashboard Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
               <div>
                  <span class="inline-block px-2 py-1 bg-primary/5 text-primary text-[10px] font-bold tracking-widest uppercase rounded mb-2">Performance Monitoring</span>
                  <h2 class="text-4xl font-headline font-extrabold tracking-tight text-primary">Operational Ledger</h2>
                  <p class="text-on-surface-variant max-w-xl mt-2 text-sm leading-relaxed">Cross-business unit metric aggregation for the current fiscal period. Real-time data sync active.</p>
               </div>
               <div class="flex items-center gap-3">
                  <div class="flex items-center bg-white p-1 rounded-lg shadow-sm border border-slate-100">
                     <button class="px-4 py-1.5 text-xs font-bold text-primary bg-primary-fixed rounded">Weekly</button>
                     <button class="px-4 py-1.5 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-colors rounded">Monthly</button>
                     <button class="px-4 py-1.5 text-xs font-medium text-slate-500 hover:bg-slate-50 transition-colors rounded">Quarterly</button>
                  </div>
                  <button class="flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">
                  <span class="material-symbols-outlined text-lg" data-icon="download">download</span>
                  Generate Report
                  </button>
               </div>
            </div>
            <!-- Bento Metrics Grid -->
            <div class="grid grid-cols-12 gap-6 mb-8">
               <!-- Large Primary Metric Card -->
               <div class="col-span-12 lg:col-span-4 bg-primary rounded-xl p-8 relative overflow-hidden shadow-xl">
                  <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
                  <div class="relative z-10">
                     <p class="text-primary-fixed-dim text-sm font-medium tracking-wide mb-1">Global Compliance Rate</p>
                     <div class="flex items-baseline gap-4 mb-6">
                        <h3 class="text-6xl font-headline font-extrabold text-white tracking-tighter">94.2%</h3>
                        <span class="flex items-center gap-1 text-on-primary-container bg-primary-container px-2 py-0.5 rounded text-xs font-bold">
                        <span class="material-symbols-outlined text-sm">trending_up</span>
                        +2.4%
                        </span>
                     </div>
                     <div class="space-y-4">
                        <div class="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                           <div class="h-full bg-primary-fixed-dim rounded-full" style="width: 94%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] uppercase font-bold tracking-widest text-primary-fixed-dim/60">
                           <span>Target: 92.0%</span>
                           <span>Status: Exceeding</span>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- Metric Cards -->
               <div class="col-span-12 lg:col-span-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-slate-100 flex flex-col justify-between hover:translate-y-[-4px] transition-transform duration-300">
                     <div class="flex justify-between items-start">
                        <div class="w-10 h-10 rounded-lg bg-tertiary-fixed flex items-center justify-center text-on-tertiary-fixed">
                           <span class="material-symbols-outlined" data-icon="warning">warning</span>
                        </div>
                        <span class="text-[10px] font-bold text-on-tertiary-fixed-variant bg-tertiary-fixed/30 px-2 py-1 rounded">CRITICAL</span>
                     </div>
                     <div>
                        <p class="text-on-surface-variant text-sm font-medium mb-1">Overdue Hazards</p>
                        <h4 class="text-3xl font-headline font-bold text-on-surface">12 <span class="text-lg font-normal text-slate-400">Cases</span></h4>
                     </div>
                  </div>
                  <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-slate-100 flex flex-col justify-between hover:translate-y-[-4px] transition-transform duration-300">
                     <div class="flex justify-between items-start">
                        <div class="w-10 h-10 rounded-lg bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed">
                           <span class="material-symbols-outlined" data-icon="monitoring">monitoring</span>
                        </div>
                     </div>
                     <div>
                        <p class="text-on-surface-variant text-sm font-medium mb-1">PJA Performance</p>
                        <h4 class="text-3xl font-headline font-bold text-on-surface">88.5<span class="text-lg font-normal text-slate-400">%</span></h4>
                     </div>
                  </div>
                  <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-slate-100 flex flex-col justify-between hover:translate-y-[-4px] transition-transform duration-300">
                     <div class="flex justify-between items-start">
                        <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center text-primary">
                           <span class="material-symbols-outlined" data-icon="visibility">visibility</span>
                        </div>
                     </div>
                     <div>
                        <p class="text-on-surface-variant text-sm font-medium mb-1">Coverage Area</p>
                        <h4 class="text-3xl font-headline font-bold text-on-surface">412 <span class="text-lg font-normal text-slate-400">KM²</span></h4>
                     </div>
                  </div>
               </div>
            </div>
            <!-- Performance Table Section -->
            <div class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-xl shadow-slate-200/50 border border-slate-100">
               <div class="px-8 py-6 flex items-center justify-between border-b border-slate-50">
                  <div class="flex items-center gap-3">
                     <span class="material-symbols-outlined text-primary" data-icon="analytics">analytics</span>
                     <h3 class="text-lg font-headline font-bold text-on-surface">Operational Performance Matrix</h3>
                  </div>
                  <div class="flex items-center gap-4">
                     <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <span class="w-2.5 h-2.5 rounded-full bg-tertiary-fixed-dim"></span> Lagging
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span> Leading
                     </div>
                  </div>
               </div>
               <div class="overflow-x-auto no-scrollbar">
                  <table class="w-full text-left border-collapse">
                     <thead>
                        <tr class="bg-surface-container-low border-b border-slate-100">
                           <th class="px-8 py-5 text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500 whitespace-nowrap">Performance Indicator</th>
                           <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500 text-center">BUMA (KDC)</th>
                           <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500 text-center">MTL</th>
                           <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500 text-center">PAMA</th>
                           <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500 text-center">BAR</th>
                           <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500 text-center">BUMA (LMO)</th>
                           <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500 text-center">FAD</th>
                           <th class="px-6 py-5 text-[11px] font-bold uppercase tracking-[0.1em] text-slate-500 text-center">MTN</th>
                        </tr>
                     </thead>
                     <tbody class="divide-y divide-slate-50">
                        <!-- Category: Lagging Indicator -->
                        <tr class="hover:bg-slate-50/50 transition-colors">
                           <td class="px-8 py-5">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-tertiary-fixed-dim" data-icon="history">history</span>
                                 <div>
                                    <p class="text-sm font-bold text-on-surface">Lagging Indicator</p>
                                    <p class="text-[10px] text-slate-400 font-medium">Historical baseline</p>
                                 </div>
                              </div>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold text-on-surface">11.4</p>
                              <p class="text-[10px] text-emerald-600 font-bold">(+2.4%)</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold text-on-surface">8.2</p>
                              <p class="text-[10px] text-tertiary-fixed-dim font-bold">(-0.5%)</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold text-on-surface">15.1</p>
                              <p class="text-[10px] text-emerald-600 font-bold">(+4.1%)</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold text-on-surface">9.6</p>
                              <p class="text-[10px] text-slate-400 font-bold">(0.0%)</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold text-on-surface">10.3</p>
                              <p class="text-[10px] text-emerald-600 font-bold">(+1.2%)</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold text-on-surface">7.4</p>
                              <p class="text-[10px] text-tertiary-fixed-dim font-bold">(-2.3%)</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold text-on-surface">12.0</p>
                              <p class="text-[10px] text-emerald-600 font-bold">(+0.8%)</p>
                           </td>
                        </tr>
                        <!-- Category: Valid GR & PSPP -->
                        <tr class="bg-surface-container-low/30 hover:bg-slate-50/50 transition-colors">
                           <td class="px-8 py-5">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-secondary" data-icon="verified">verified</span>
                                 <div>
                                    <p class="text-sm font-bold text-on-surface">Valid GR &amp; PSPP</p>
                                    <p class="text-[10px] text-slate-400 font-medium">Compliance verification</p>
                                 </div>
                              </div>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">94%</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">88%</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">91%</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold text-on-tertiary-container">76%</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">82%</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">95%</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">89%</p>
                           </td>
                        </tr>
                        <!-- Category: Blindspot TBC -->
                        <tr class="hover:bg-slate-50/50 transition-colors">
                           <td class="px-8 py-5">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-slate-400" data-icon="visibility_off">visibility_off</span>
                                 <div>
                                    <p class="text-sm font-bold text-on-surface">Blindspot TBC</p>
                                    <p class="text-[10px] text-slate-400 font-medium">Detection gaps identified</p>
                                 </div>
                              </div>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">2</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">0</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold text-on-tertiary-container">5</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">1</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">3</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">0</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">2</p>
                           </td>
                        </tr>
                        <!-- Category: Overdue Hazard -->
                        <tr class="bg-surface-container-low/30 hover:bg-slate-50/50 transition-colors">
                           <td class="px-8 py-5">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-error" data-icon="report_problem">report_problem</span>
                                 <div>
                                    <p class="text-sm font-bold text-on-surface">Overdue Hazard</p>
                                    <p class="text-[10px] text-slate-400 font-medium">High risk tickets</p>
                                 </div>
                              </div>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <div class="inline-block px-3 py-1 rounded bg-error-container text-on-error-container text-xs font-bold">3</div>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">0</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <div class="inline-block px-3 py-1 rounded bg-error-container text-on-error-container text-xs font-bold">6</div>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">1</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">2</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">0</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">0</p>
                           </td>
                        </tr>
                        <!-- Category: PJA Performance -->
                        <tr class="hover:bg-slate-50/50 transition-colors">
                           <td class="px-8 py-5">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-blue-500" data-icon="task_alt">task_alt</span>
                                 <div>
                                    <p class="text-sm font-bold text-on-surface">PJA Performance</p>
                                    <p class="text-[10px] text-slate-400 font-medium">Project execution score</p>
                                 </div>
                              </div>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">88.5</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">92.1</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">84.8</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">89.0</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">91.4</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">93.2</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">87.5</p>
                           </td>
                        </tr>
                        <!-- Category: Ratio Pelaporan -->
                        <tr class="bg-surface-container-low/30 hover:bg-slate-50/50 transition-colors">
                           <td class="px-8 py-5">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-secondary" data-icon="pie_chart">pie_chart</span>
                                 <div>
                                    <p class="text-sm font-bold text-on-surface">Ratio Pelaporan</p>
                                    <p class="text-[10px] text-slate-400 font-medium">Reporting frequency ratio</p>
                                 </div>
                              </div>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">1.25</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">0.98</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">1.44</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">1.10</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">1.12</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold">1.05</p>
                           </td>
                           <td class="px-6 py-5 text-center">
                              <p class="text-sm font-bold text-on-tertiary-container">0.82</p>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="px-8 py-4 bg-slate-50/50 flex items-center justify-between">
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Showing data as of October 24, 2023</p>
                  <div class="flex items-center gap-2">
                     <span class="text-[10px] font-bold text-primary cursor-pointer hover:underline">Download Matrix CSV</span>
                     <span class="text-slate-300">|</span>
                     <span class="text-[10px] font-bold text-primary cursor-pointer hover:underline">View Historical Trend</span>
                  </div>
               </div>
            </div>
            <!-- Footer Stats / Insights Section -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
               <div class="bg-white rounded-xl p-6 border border-slate-100 flex items-center gap-6">
                  <div class="flex-shrink-0 w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                     <span class="material-symbols-outlined text-2xl text-primary" data-icon="insights">insights</span>
                  </div>
                  <div>
                     <h4 class="text-sm font-bold text-primary mb-1">Weekly Intelligence Insight</h4>
                     <p class="text-xs text-on-surface-variant leading-relaxed">Performance in BUMA (KDC) has improved by 2.4% following the implementation of the new GR verification protocol. MTL remains stable despite increased volume.</p>
                  </div>
               </div>
               <div class="bg-white rounded-xl p-6 border border-slate-100 flex items-center gap-6">
                  <div class="flex-shrink-0 w-16 h-16 rounded-full bg-tertiary-fixed flex items-center justify-center">
                     <span class="material-symbols-outlined text-2xl text-on-tertiary-fixed" data-icon="campaign">campaign</span>
                  </div>
                  <div>
                     <h4 class="text-sm font-bold text-tertiary-container mb-1">Attention Required: PAMA Blindspots</h4>
                     <p class="text-xs text-on-surface-variant leading-relaxed">Critical increase in detection blindspots (TBC) noted in PAMA sector. Immediate recalibration of monitoring sensors recommended for the next reporting cycle.</p>
                  </div>
               </div>
            </div>
         </div>
      </main>
   </body>
</html>