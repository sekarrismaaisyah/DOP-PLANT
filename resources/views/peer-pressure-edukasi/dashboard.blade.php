<!DOCTYPE html>
<html class="light" lang="en">
   <head>
      <meta charset="utf-8"/>
      <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
      <title>BMO2 Safety - Peer Pressure Program Evaluation</title>
      <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&amp;family=Inter:wght@300;400;500;600&amp;display=swap" rel="stylesheet"/>
      <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
      <script id="tailwind-config">
         tailwind.config = {
           darkMode: "class",
           theme: {
             extend: {
               colors: {
                 "outline": "#747779",
                 "primary-container": "#859aff",
                 "on-error-container": "#510017",
                 "on-error": "#ffefef",
                 "secondary-fixed": "#e4c6ff",
                 "on-tertiary-fixed-variant": "#00377b",
                 "primary-fixed-dim": "#748cf9",
                 "surface-container-low": "#eef1f3",
                 "secondary-dim": "#653b91",
                 "secondary-fixed-dim": "#dab4ff",
                 "on-primary-container": "#001867",
                 "error": "#b41340",
                 "error-dim": "#a70138",
                 "on-tertiary-fixed": "#00163b",
                 "secondary": "#72479e",
                 "error-container": "#f74b6d",
                 "surface-container-high": "#dfe3e6",
                 "on-secondary-fixed": "#481c73",
                 "surface-tint": "#3952bc",
                 "on-secondary-container": "#5d3288",
                 "surface-container": "#e5e9eb",
                 "inverse-primary": "#7991ff",
                 "on-background": "#2c2f31",
                 "surface": "#f5f7f9",
                 "surface-container-lowest": "#ffffff",
                 "on-primary-fixed-variant": "#00207e",
                 "inverse-on-surface": "#9a9d9f",
                 "on-secondary": "#fbefff",
                 "tertiary-fixed": "#8ab0ff",
                 "tertiary-fixed-dim": "#73a2ff",
                 "surface-container-highest": "#d9dde0",
                 "surface-variant": "#d9dde0",
                 "tertiary": "#0057bd",
                 "on-primary-fixed": "#000000",
                 "primary": "#3952bc",
                 "background": "#f5f7f9",
                 "on-surface": "#2c2f31",
                 "surface-bright": "#f5f7f9",
                 "secondary-container": "#e4c6ff",
                 "on-tertiary-container": "#002e6a",
                 "on-secondary-fixed-variant": "#663c92",
                 "inverse-surface": "#0b0f10",
                 "surface-dim": "#d0d5d8",
                 "outline-variant": "#abadaf",
                 "on-tertiary": "#f0f2ff",
                 "primary-dim": "#2b45af",
                 "tertiary-dim": "#004ca6",
                 "primary-fixed": "#859aff",
                 "tertiary-container": "#8ab0ff",
                 "on-primary": "#f2f1ff",
                 "on-surface-variant": "#595c5e"
               },
               fontFamily: {
                 "headline": ["Poppins"],
                 "body": ["Poppins"],
                 "label": ["Poppins"]
               },
               borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "2xl": "1rem", "full": "9999px"},
               boxShadow: {
                 'card': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
                 'card-heavy': '0 10px 15px -3px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.1)',
                 'card-sharp': '0 8px 0px -2px rgba(0, 0, 0, 0.05), 0 15px 30px -5px rgba(0, 0, 0, 0.12)',
               }
             },
           },
         }
      </script>
      <style>
         .material-symbols-outlined {
         font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
         vertical-align: middle;
         }
         .signature-gradient {
         background: linear-gradient(135deg, #3952bc 0%, #72479e 100%);
         }
         .glass-panel {
         background: rgba(255, 255, 255, 0.7);
         backdrop-filter: blur(20px);
         }
         .hide-scrollbar::-webkit-scrollbar { display: none; }
         .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
         /* Custom Shadow Overrides for Crispness */
         .anchored-card {
         box-shadow: 0 4px 0px 0px rgba(0, 0, 0, 0.05), 0 12px 24px -4px rgba(0, 0, 0, 0.15);
         border: 1px solid rgba(0, 0, 0, 0.08);
         transition: transform 0.2s ease, box-shadow 0.2s ease;
         }
         .anchored-card:hover {
         transform: translateY(-2px);
         box-shadow: 0 6px 0px 0px rgba(0, 0, 0, 0.05), 0 20px 32px -8px rgba(0, 0, 0, 0.2);
         }
      </style>
   </head>
   <body class="bg-[#f0f2f5] font-body text-on-surface min-h-screen flex flex-col">
      <!-- TopAppBar -->
      <header class="w-full sticky top-0 bg-[#ffffff] border-b border-[#dfe3e6] z-50 shadow-sm">
         <div class=" mx-auto px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-10">
               <div class="flex flex-col">
                  <h1 class="font-headline font-bold text-[#3952bc] text-xl tracking-tighter leading-tight">Sambarata Mining Operation</h1>
                  <p class="text-on-surface-variant text-[10px] font-bold uppercase tracking-widest">Peer Pressure Program</p>
               </div>
               <div class="h-8 w-px bg-[#dfe3e6] hidden lg:block"></div>
               <nav class="hidden md:flex gap-8">
                  <a class="text-[#3952bc] border-b-2 border-[#3952bc] pb-1 font-bold text-sm tracking-tight" href="#">Overview</a>
                  <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Site Filters</a>
                  <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Department</a>
                  <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Reports</a>
                  <a class="text-[#595c5e] hover:text-[#3952bc] font-semibold text-sm tracking-tight transition-colors" href="#">Analytics</a>
               </nav>
            </div>
            <div class="flex items-center gap-6">
               <div class="relative group hidden xl:block">
                  <input class="bg-[#f5f7f9] border-none rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-primary w-80 transition-all shadow-inner" placeholder="Search safety records..." type="text"/>
                  <span class="material-symbols-outlined absolute right-3 top-2 text-on-surface-variant" data-icon="search">search</span>
               </div>
               <div class="flex items-center gap-3">
                  <button class="p-2 hover:bg-[#dfe3e6] rounded-full transition-colors relative">
                  <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                  <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-error border-2 border-white rounded-full"></span>
                  </button>
                  <button class="flex items-center gap-2 p-1.5 pr-4 bg-white hover:bg-[#dfe3e6] rounded-full transition-colors border border-outline-variant/30 shadow-sm">
                     <span class="material-symbols-outlined text-3xl text-primary" data-icon="account_circle">account_circle</span>
                     <div class="text-left">
                        <p class="text-[10px] font-bold text-primary uppercase leading-none">Safety Admin</p>
                        <p class="text-[9px] text-on-surface-variant font-medium">Site Manager</p>
                     </div>
                  </button>
               </div>
            </div>
         </div>
      </header>
      <!-- Main Content Area -->
      <main class="flex-grow w-full  mx-auto p-8 space-y-8">
         <!-- Header & Top Filters -->
         <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6 pb-6 border-b border-outline-variant/30">
            <div>
               <nav class="flex items-center gap-2 text-xs font-bold text-on-surface-variant uppercase mb-2">
                  <span>Dashboard</span>
                  <span class="material-symbols-outlined text-xs">chevron_right</span>
                  <span class="text-primary">Peer Pressure Evaluation</span>
               </nav>
               <h2 class="font-headline font-extrabold text-4xl text-on-background tracking-tight">Peer Pressure Evaluation</h2>
               <p class="text-on-surface-variant font-medium mt-1">Program performance metrics for Q3 2024 • Updated 5 mins ago</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
               <div class="bg-white px-4 py-2.5 rounded-xl border border-outline-variant/30 flex items-center gap-2 text-sm font-semibold text-on-surface-variant cursor-pointer hover:shadow-md transition-all">
                  <span class="material-symbols-outlined text-lg" data-icon="calendar_today">calendar_today</span>
                  Last 30 Days
                  <span class="material-symbols-outlined text-lg">expand_more</span>
               </div>
               <div class="bg-white px-4 py-2.5 rounded-xl border border-outline-variant/30 flex items-center gap-2 text-sm font-semibold text-on-surface-variant cursor-pointer hover:shadow-md transition-all">
                  <span class="material-symbols-outlined text-lg" data-icon="category">category</span>
                  All Departments
                  <span class="material-symbols-outlined text-lg">expand_more</span>
               </div>
               <button class="signature-gradient text-white font-bold px-6 py-2.5 rounded-xl shadow-lg hover:opacity-90 active:scale-[0.98] transition-all flex items-center gap-2">
               <span class="material-symbols-outlined text-lg" data-icon="file_download">file_download</span>
               Export Report
               </button>
            </div>
         </div>
         <!-- KPI Row -->
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between">
               <div class="flex justify-between items-start">
                  <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Total Cases</span>
                  <div class="p-2 bg-primary/10 rounded-lg">
                     <span class="material-symbols-outlined text-primary" data-icon="assignment_late">assignment_late</span>
                  </div>
               </div>
               <div class="mt-4">
                  <p class="font-headline font-extrabold text-4xl">1,284</p>
                  <p class="text-[#059669] text-[11px] font-bold flex items-center gap-1 mt-1">
                     <span class="material-symbols-outlined text-xs" data-icon="trending_down">trending_down</span>
                     12% from last month
                  </p>
               </div>
            </div>
            <div class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between">
               <div class="flex justify-between items-start">
                  <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Completion Rate</span>
                  <div class="p-2 bg-[#dcfce7] rounded-lg">
                     <span class="material-symbols-outlined text-[#16a34a]" data-icon="task_alt">task_alt</span>
                  </div>
               </div>
               <div class="mt-4">
                  <div class="flex justify-between items-end">
                     <p class="font-headline font-extrabold text-4xl">98.2%</p>
                     <span class="text-[11px] font-bold text-[#16a34a]">+0.4%</span>
                  </div>
                  <div class="w-full bg-[#f1f5f9] h-2 rounded-full mt-3 overflow-hidden border border-outline-variant/10">
                     <div class="bg-[#16a34a] h-full rounded-full" style="width: 98.2%"></div>
                  </div>
               </div>
            </div>
            <div class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between">
               <div class="flex justify-between items-start">
                  <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Avg Group Size</span>
                  <div class="p-2 bg-[#fef3c7] rounded-lg">
                     <span class="material-symbols-outlined text-[#d97706]" data-icon="groups">groups</span>
                  </div>
               </div>
               <div class="mt-4">
                  <p class="font-headline font-extrabold text-4xl">6.4</p>
                  <p class="text-on-surface-variant text-[11px] font-medium mt-1">Target: 5-8 peers</p>
               </div>
            </div>
            <div class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between">
               <div class="flex justify-between items-start">
                  <span class="text-on-surface-variant text-[11px] font-bold tracking-wider uppercase">Education Duration</span>
                  <div class="p-2 bg-secondary/10 rounded-lg">
                     <span class="material-symbols-outlined text-secondary" data-icon="timer">timer</span>
                  </div>
               </div>
               <div class="mt-4">
                  <p class="font-headline font-extrabold text-4xl">14.8m</p>
                  <p class="text-on-surface-variant text-[11px] font-medium mt-1">Target: 15 mins</p>
               </div>
            </div>
         </div>
         <!-- Charts & Recommendations Grid -->
         <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Trend Analysis -->
            <div class="lg:col-span-8 bg-white p-8 rounded-2xl anchored-card">
               <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                  <div>
                     <h3 class="font-headline font-bold text-xl">Cases Trend Analysis</h3>
                     <p class="text-xs text-on-surface-variant font-medium">Weekly distribution of reported safety cases</p>
                  </div>
                  <div class="flex items-center gap-6 text-[10px] font-bold uppercase tracking-wider">
                     <span class="flex items-center gap-2">
                     <span class="w-3 h-3 bg-primary rounded-full shadow-sm"></span> 
                     Actual Performance
                     </span>
                     <span class="flex items-center gap-2">
                     <span class="w-3 h-1 bg-error/30 rounded-full border-t border-dashed border-error"></span> 
                     -25% Target Line
                     </span>
                  </div>
               </div>
               <div class="h-80 flex items-end justify-between gap-4 px-2 relative">
                  <div class="absolute bottom-[25%] left-0 w-full h-px border-t-2 border-dashed border-error opacity-40 z-0"></div>
                  <div class="flex-grow h-full flex items-end gap-3 z-10">
                     <div class="w-full bg-[#f8fafc] border-x border-t border-outline-variant/10 h-full flex flex-col justify-end group cursor-pointer rounded-t-lg">
                        <div class="bg-primary h-[60%] rounded-t-md transition-all group-hover:bg-primary-dim relative shadow-lg">
                           <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold hidden group-hover:block">182</span>
                        </div>
                     </div>
                     <div class="w-full bg-[#f8fafc] border-x border-t border-outline-variant/10 h-full flex flex-col justify-end group cursor-pointer rounded-t-lg">
                        <div class="bg-primary h-[50%] rounded-t-md transition-all group-hover:bg-primary-dim relative shadow-lg">
                           <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold hidden group-hover:block">145</span>
                        </div>
                     </div>
                     <div class="w-full bg-[#f8fafc] border-x border-t border-outline-variant/10 h-full flex flex-col justify-end group cursor-pointer rounded-t-lg">
                        <div class="bg-primary h-[70%] rounded-t-md transition-all group-hover:bg-primary-dim relative shadow-lg">
                           <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold hidden group-hover:block">210</span>
                        </div>
                     </div>
                     <div class="w-full bg-[#f8fafc] border-x border-t border-outline-variant/10 h-full flex flex-col justify-end group cursor-pointer rounded-t-lg">
                        <div class="bg-primary h-[40%] rounded-t-md transition-all group-hover:bg-primary-dim relative shadow-lg">
                           <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold hidden group-hover:block">120</span>
                        </div>
                     </div>
                     <div class="w-full bg-[#f8fafc] border-x border-t border-outline-variant/10 h-full flex flex-col justify-end group cursor-pointer rounded-t-lg">
                        <div class="bg-primary h-[30%] rounded-t-md transition-all group-hover:bg-primary-dim relative shadow-lg">
                           <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold hidden group-hover:block">95</span>
                        </div>
                     </div>
                     <div class="w-full bg-[#f8fafc] border-x border-t border-outline-variant/10 h-full flex flex-col justify-end group cursor-pointer rounded-t-lg">
                        <div class="bg-primary h-[20%] rounded-t-md transition-all group-hover:bg-primary-dim relative shadow-lg">
                           <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold hidden group-hover:block">68</span>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="flex justify-between mt-6 px-2 text-[10px] text-on-surface-variant font-bold uppercase tracking-widest">
                  <span>WK 28</span><span>WK 29</span><span>WK 30</span><span>WK 31</span><span>WK 32</span><span>WK 33</span>
               </div>
            </div>
            <!-- Priority Panels -->
            <div class="lg:col-span-4 flex flex-col gap-6">
               <div class="bg-[#fff5f5] p-6 rounded-2xl anchored-card relative overflow-hidden group border-error/20">
                  <div class="absolute top-0 left-0 w-2 h-full bg-error"></div>
                  <div class="flex items-center justify-between mb-4">
                     <div class="flex items-center gap-2 text-error">
                        <span class="material-symbols-outlined text-lg" data-icon="report">report</span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em]">Urgent Action</span>
                     </div>
                     <span class="text-[10px] font-bold text-on-surface-variant">Aug 24, 2024</span>
                  </div>
                  <h4 class="font-headline font-bold text-lg mb-2 group-hover:text-primary transition-colors">Fatigue Intervention Needed</h4>
                  <p class="text-sm text-on-surface-variant leading-relaxed mb-6">Deviation spike detected in Night Shift Operation. 23% increase in fatigue-related alerts at Sector 7.</p>
                  <button class="text-xs font-bold text-primary flex items-center gap-1 group-hover:gap-2 transition-all">
                  Action Intervention Plan <span class="material-symbols-outlined text-sm" data-icon="arrow_forward">arrow_forward</span>
                  </button>
               </div>
               <div class="bg-white p-6 rounded-2xl anchored-card flex flex-col justify-between">
                  <div>
                     <div class="flex items-center gap-2 mb-4 text-[#d97706]">
                        <span class="material-symbols-outlined text-lg" data-icon="priority_high">priority_high</span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em]">High Priority</span>
                     </div>
                     <h4 class="font-headline font-bold text-lg mb-2">BeRecord Compliance</h4>
                     <p class="text-sm text-on-surface-variant leading-relaxed">Evidence links missing for Loading Area cases from Aug 12-14. 12 records affected.</p>
                  </div>
                  <button class="mt-6 text-xs font-bold text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
                  Review Records <span class="material-symbols-outlined text-sm">open_in_new</span>
                  </button>
               </div>
            </div>
         </div>
         <!-- Secondary Metrics Grid -->
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-2xl anchored-card">
               <h3 class="font-headline font-bold text-[11px] mb-6 uppercase tracking-widest text-on-surface-variant">Deviation Category</h3>
               <div class="flex justify-center mb-8">
                  <div class="relative w-36 h-36 rounded-full border-[14px] border-primary flex items-center justify-center shadow-inner">
                     <div class="absolute inset-0 border-[14px] border-secondary border-t-transparent border-l-transparent rotate-45 rounded-full"></div>
                     <div class="text-center">
                        <span class="block font-extrabold text-2xl">1.2k</span>
                        <span class="block text-[9px] uppercase font-bold text-on-surface-variant">Total</span>
                     </div>
                  </div>
               </div>
               <div class="space-y-3">
                  <div class="flex justify-between items-center text-xs">
                     <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-primary shadow-sm"></span> PSPP/Violations</span>
                     <span class="font-bold">62%</span>
                  </div>
                  <div class="flex justify-between items-center text-xs">
                     <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-secondary shadow-sm"></span> Alert Fatigue</span>
                     <span class="font-bold">28%</span>
                  </div>
                  <div class="flex justify-between items-center text-xs">
                     <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-tertiary shadow-sm"></span> TBC Hazards</span>
                     <span class="font-bold">10%</span>
                  </div>
               </div>
            </div>
            <div class="bg-white p-6 rounded-2xl anchored-card">
               <h3 class="font-headline font-bold text-[11px] mb-6 uppercase tracking-widest text-on-surface-variant">Compliance Radar</h3>
               <div class="relative w-full aspect-square flex items-center justify-center">
                  <div class="absolute inset-0 flex items-center justify-center">
                     <div class="w-[85%] h-[85%] border border-outline-variant/20 rounded-full"></div>
                     <div class="absolute w-[60%] h-[60%] border border-outline-variant/20 rounded-full"></div>
                     <div class="absolute w-[35%] h-[35%] border border-outline-variant/20 rounded-full"></div>
                  </div>
                  <div class="w-0 h-0 border-l-[45px] border-l-transparent border-r-[45px] border-r-transparent border-b-[70px] border-b-primary/40 rotate-12 scale-125 transition-transform hover:scale-150 cursor-crosshair"></div>
                  <div class="absolute inset-0 p-1 flex flex-col justify-between items-center text-[10px] font-bold text-on-surface-variant uppercase tracking-tighter">
                     <span>BeRecord</span>
                     <div class="w-full flex justify-between px-1">
                        <span>Evidence</span>
                        <span>Size</span>
                     </div>
                     <div class="w-full flex justify-between px-3 pb-3">
                        <span>H+1</span>
                        <span>Duration</span>
                     </div>
                  </div>
               </div>
            </div>
            <div class="bg-white p-6 rounded-2xl anchored-card">
               <h3 class="font-headline font-bold text-[11px] mb-6 uppercase tracking-widest text-on-surface-variant">Location Analysis</h3>
               <div class="space-y-5">
                  <div class="space-y-2">
                     <div class="flex justify-between text-[10px] font-bold uppercase tracking-wider"><span>Loading Area</span><span class="text-primary">342</span></div>
                     <div class="w-full bg-[#f1f5f9] h-2.5 rounded-full overflow-hidden border border-outline-variant/10 shadow-inner">
                        <div class="bg-primary h-full rounded-full" style="width: 85%"></div>
                     </div>
                  </div>
                  <div class="space-y-2">
                     <div class="flex justify-between text-[10px] font-bold uppercase tracking-wider"><span>Mine Road</span><span class="text-primary">211</span></div>
                     <div class="w-full bg-[#f1f5f9] h-2.5 rounded-full overflow-hidden border border-outline-variant/10 shadow-inner">
                        <div class="bg-primary h-full rounded-full" style="width: 60%"></div>
                     </div>
                  </div>
                  <div class="space-y-2">
                     <div class="flex justify-between text-[10px] font-bold uppercase tracking-wider"><span>Drill/Blast</span><span class="text-primary">154</span></div>
                     <div class="w-full bg-[#f1f5f9] h-2.5 rounded-full overflow-hidden border border-outline-variant/10 shadow-inner">
                        <div class="bg-primary h-full rounded-full" style="width: 45%"></div>
                     </div>
                  </div>
                  <div class="space-y-2">
                     <div class="flex justify-between text-[10px] font-bold uppercase tracking-wider"><span>Workshop</span><span class="text-primary">98</span></div>
                     <div class="w-full bg-[#f1f5f9] h-2.5 rounded-full overflow-hidden border border-outline-variant/10 shadow-inner">
                        <div class="bg-primary h-full rounded-full" style="width: 30%"></div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="bg-white p-6 rounded-2xl anchored-card">
               <h3 class="font-headline font-bold text-[11px] mb-6 uppercase tracking-widest text-on-surface-variant">Program Health</h3>
               <div class="space-y-7">
                  <div>
                     <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Blindspot Detection</p>
                     <div class="flex items-center justify-between">
                        <span class="text-3xl font-extrabold text-[#059669]">18%</span>
                        <span class="px-2 py-0.5 bg-[#dcfce7] text-[#16a34a] text-[9px] font-bold rounded border border-[#16a34a]/20">TARGET &lt; 23%</span>
                     </div>
                  </div>
                  <div>
                     <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Repeat Offender Rate</p>
                     <div class="flex items-center justify-between">
                        <span class="text-3xl font-extrabold text-error">4.2%</span>
                        <span class="px-2 py-0.5 bg-error/10 text-error text-[9px] font-bold rounded border border-error/20">+1.1% RISK</span>
                     </div>
                  </div>
                  <div>
                     <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Speak-Up Growth</p>
                     <div class="flex items-center justify-between">
                        <span class="text-3xl font-extrabold text-primary">+34%</span>
                        <span class="px-2 py-0.5 bg-primary/10 text-primary text-[9px] font-bold rounded border border-primary/20">QOQ GROWTH</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- Data Table Section -->
         <div class="bg-white rounded-2xl anchored-card overflow-hidden">
            <div class="p-6 border-b border-outline-variant/20 flex flex-col md:flex-row justify-between items-center gap-4">
               <div>
                  <h3 class="font-headline font-bold text-xl">Data Peer Pressure</h3>
                  <p class="text-xs text-on-surface-variant font-medium">Detailed log of safety incidents and peer interactions</p>
               </div>
               <div class="flex gap-3">
                  <div class="flex bg-[#f1f5f9] p-1 rounded-xl border border-outline-variant/20">
                     <button class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-wider bg-white shadow-sm rounded-lg border border-outline-variant/10">Real-time</button>
                     <button class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-wider text-on-surface-variant hover:text-primary transition-colors">Archived</button>
                  </div>
                  <button class="px-4 py-2 bg-white text-xs font-bold rounded-xl hover:bg-surface-container-high transition-colors flex items-center gap-2 border border-outline-variant/30 shadow-sm">
                  <span class="material-symbols-outlined text-sm">filter_list</span> Filters
                  </button>
                  <button class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow-md transition-transform active:scale-95 flex items-center gap-2">
                  <span class="material-symbols-outlined text-sm">download</span> CSV
                  </button>
               </div>
            </div>
            <div class="overflow-x-auto">
               <table class="w-full text-sm text-left">
                  <thead class="bg-[#f8fafc] text-on-surface-variant font-bold text-[10px] uppercase tracking-[0.15em] border-b border-outline-variant/20">
                     <tr>
                        <th class="px-8 py-5">Incident Detail</th>
                        <th class="px-8 py-5">Violator &amp; Dept</th>
                        <th class="px-8 py-5">Peer Group</th>
                        <th class="px-8 py-5">Duration</th>
                        <th class="px-8 py-5">Evidence</th>
                        <th class="px-8 py-5">Status</th>
                     </tr>
                  </thead>
                  <tbody class="divide-y divide-outline-variant/10">
                     @forelse ($kejadian as $k)
                        @php
                           $pelanggar = $k->peserta->firstWhere('peran', 'pelanggar');
                           $peers = $k->peserta->where('peran', 'peer')->values();
                           $visiblePeers = $peers->take(3);
                           $extraPeerCount = max(0, $peers->count() - 3);
                           $avatarBgs = ['bg-secondary-fixed', 'bg-primary-fixed', 'bg-tertiary-fixed'];
                           $catPrimary = 'mt-2 inline-block bg-primary-container/20 text-primary text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider border border-primary/10';
                           $catSecondary = 'mt-2 inline-block bg-secondary-container/20 text-secondary text-[9px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider border border-secondary/10';
                           $statusBadge = $k->dashboardStatusBadge();
                        @endphp
                     <tr class="hover:bg-[#f8fafc] transition-colors cursor-pointer group js-peer-kejadian-row" data-kejadian-id="{{ $k->id }}" role="button" tabindex="0">
                        <td class="px-8 py-5">
                           <div class="font-bold text-on-surface">{{ $k->formattedTemuanDatetime() }}</div>
                           <div class="text-[10px] text-on-surface-variant flex items-center gap-1 mt-0.5">
                              <span class="material-symbols-outlined text-[12px]" data-icon="location_on">location_on</span> {{ $k->lokasi_temuan }}
                           </div>
                           <span class="{{ $loop->iteration % 2 === 1 ? $catPrimary : $catSecondary }}">{{ $k->kategori_deviasi }}</span>
                        </td>
                        <td class="px-8 py-5">
                           <div class="font-bold">{{ $pelanggar ? $pelanggar->sid . ' | ' . ($pelanggar->nama ?: '—') : '—' }}</div>
                           <div class="text-xs text-on-surface-variant">{{ $k->departemen ?: '—' }} / {{ $k->aktivitas_pekerjaan ?: '—' }}</div>
                        </td>
                        <td class="px-8 py-5">
                           <div class="flex -space-x-2">
                              @foreach ($visiblePeers as $peer)
                                 @php
                                    $pi = $loop->index;
                                    $sidKey = \Illuminate\Support\Str::lower(trim((string) $peer->sid));
                                    $peerFoto = $peerFotoUrls[$sidKey] ?? null;
                                 @endphp
                                 <div class="relative w-8 h-8 shrink-0 rounded-full border-2 border-white shadow-md overflow-hidden bg-surface-container-high">
                                    @if (filled($peerFoto))
                                    <img src="{{ $peerFoto }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" width="32" height="32" decoding="async" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
                                    <div class="hidden absolute inset-0 flex items-center justify-center text-[10px] font-bold {{ $avatarBgs[$pi] ?? 'bg-surface-container-high' }}">{{ $peer->initials() }}</div>
                                    @else
                                    <div class="flex h-full w-full items-center justify-center text-[10px] font-bold {{ $avatarBgs[$pi] ?? 'bg-surface-container-high' }}">{{ $peer->initials() }}</div>
                                    @endif
                                 </div>
                              @endforeach
                              @if ($extraPeerCount > 0)
                                 <div class="w-8 h-8 rounded-full bg-surface-container-high text-[10px] flex items-center justify-center font-bold border-2 border-white shadow-md text-on-surface-variant">+{{ $extraPeerCount }}</div>
                              @endif
                           </div>
                           <div class="text-[10px] mt-2 font-bold text-on-surface-variant">Leader: {{ $k->pemimpin_edukasi ?: '—' }}</div>
                        </td>
                        <td class="px-8 py-5 font-bold text-xs text-on-surface">{{ $k->durasi_edukasi_menit }}m</td>
                        <td class="px-8 py-5">
                           @if (filled($k->evidence_url))
                           <a href="{{ $k->evidence_url }}" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()" class="text-primary hover:underline flex items-center gap-1 text-xs font-bold transition-all relative z-10">
                           <span class="material-symbols-outlined text-lg" data-icon="attach_file">attach_file</span> View Records
                           </a>
                           @else
                           <div class="text-error font-bold text-xs flex items-center gap-1">
                           <span class="material-symbols-outlined text-lg" data-icon="warning">warning</span> Missing Evidence
                           </div>
                           @endif
                        </td>
                        <td class="px-8 py-5">
                           <span class="{{ $statusBadge['spanClass'] }}">{{ $statusBadge['label'] }}</span>
                        </td>
                     </tr>
                     @empty
                     <tr>
                        <td colspan="6" class="px-8 py-10 text-center text-sm text-on-surface-variant font-medium">Belum ada data kejadian.</td>
                     </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
            <div class="p-6 bg-[#f8fafc] flex justify-between items-center border-t border-outline-variant/20">
               <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">@if ($kejadian->total() === 0)Showing 0 of 0 entries @else Showing {{ $kejadian->firstItem() }}-{{ $kejadian->lastItem() }} of {{ number_format($kejadian->total()) }} entries @endif</p>
               <div class="flex gap-2">
                  @if ($kejadian->onFirstPage())
                  <button type="button" class="p-2 border border-outline-variant/30 rounded-lg opacity-40 cursor-not-allowed" disabled aria-disabled="true"><span class="material-symbols-outlined text-sm">chevron_left</span></button>
                  @else
                  <a href="{{ $kejadian->previousPageUrl() }}" class="p-2 border border-outline-variant/30 rounded-lg hover:bg-white hover:shadow-md transition-all inline-flex" rel="prev"><span class="material-symbols-outlined text-sm">chevron_left</span></a>
                  @endif
                  @if (! $kejadian->hasMorePages())
                  <button type="button" class="p-2 border border-outline-variant/30 rounded-lg opacity-40 cursor-not-allowed" disabled aria-disabled="true"><span class="material-symbols-outlined text-sm">chevron_right</span></button>
                  @else
                  <a href="{{ $kejadian->nextPageUrl() }}" class="p-2 border border-outline-variant/30 rounded-lg hover:bg-white hover:shadow-md transition-all inline-flex" rel="next"><span class="material-symbols-outlined text-sm">chevron_right</span></a>
                  @endif
               </div>
            </div>
         </div>
         <!-- Performance & Reward Section -->
         <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Company Case Breakdown -->
            <div class="bg-white p-8 rounded-2xl anchored-card">
               <div class="flex justify-between items-start mb-8">
                  <div>
                     <h3 class="font-headline font-extrabold text-2xl text-primary">PT Madhani Talatah Nusantara</h3>
                     <p class="text-xs text-on-surface-variant font-medium mt-1">Operational site breakdown and load metrics</p>
                  </div>
                  <span class="px-3 py-1 bg-primary/5 border border-primary/20 rounded-full text-[10px] font-bold text-primary shadow-sm">Q3 DATA</span>
               </div>
               <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                  <div class="space-y-6">
                     <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] mb-4">Department Load</p>
                     <div class="flex items-center gap-5 p-3 hover:bg-[#f8fafc] rounded-xl transition-all cursor-pointer border border-transparent hover:border-outline-variant/20 group">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary shadow-inner group-hover:scale-110 transition-transform">
                           <span class="material-symbols-outlined text-2xl" data-icon="engineering">engineering</span>
                        </div>
                        <div>
                           <p class="text-[11px] font-bold text-on-surface-variant uppercase">Operation</p>
                           <p class="text-2xl font-extrabold">842 <span class="text-xs font-medium text-on-surface-variant tracking-normal">Cases</span></p>
                        </div>
                     </div>
                     <div class="flex items-center gap-5 p-3 hover:bg-[#f8fafc] rounded-xl transition-all cursor-pointer border border-transparent hover:border-outline-variant/20 group">
                        <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center text-secondary shadow-inner group-hover:scale-110 transition-transform">
                           <span class="material-symbols-outlined text-2xl" data-icon="architecture">architecture</span>
                        </div>
                        <div>
                           <p class="text-[11px] font-bold text-on-surface-variant uppercase">Technical</p>
                           <p class="text-2xl font-extrabold">442 <span class="text-xs font-medium text-on-surface-variant tracking-normal">Cases</span></p>
                        </div>
                     </div>
                  </div>
                  <div class="bg-[#f8fafc] rounded-2xl p-6 flex flex-col justify-between border border-outline-variant/20 shadow-inner">
                     <div>
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em] mb-4">Status Distribution</p>
                        <div class="flex items-end gap-3 h-24 mb-4">
                           <div class="bg-[#16a34a] w-full rounded-t-lg group relative shadow-lg" style="height: 90%" title="Closed">
                              <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[9px] font-bold hidden group-hover:block whitespace-nowrap bg-white px-1 rounded shadow-md border">1,155 Closed</div>
                           </div>
                           <div class="bg-[#d97706] w-full rounded-t-lg group relative shadow-lg" style="height: 30%" title="Open">
                              <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[9px] font-bold hidden group-hover:block whitespace-nowrap bg-white px-1 rounded shadow-md border">102 Open</div>
                           </div>
                           <div class="bg-error w-full rounded-t-lg group relative shadow-lg" style="height: 10%" title="Pending">
                              <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[9px] font-bold hidden group-hover:block whitespace-nowrap bg-white px-1 rounded shadow-md border">27 Pending</div>
                           </div>
                        </div>
                     </div>
                     <div class="grid grid-cols-3 gap-1 text-[9px] font-bold uppercase tracking-tighter text-center">
                        <div class="flex flex-col"><span class="text-[#16a34a]">90% CL</span></div>
                        <div class="flex flex-col"><span class="text-[#d97706]">8% OP</span></div>
                        <div class="flex flex-col"><span class="text-error">2% PE</span></div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- Reward/Punishment -->
            <div class="bg-white p-8 rounded-2xl anchored-card flex flex-col justify-between">
               <div>
                  <h3 class="font-headline font-bold text-2xl mb-2">Program Accountability</h3>
                  <p class="text-sm text-on-surface-variant mb-8 leading-relaxed">System-generated eligibility list for quarterly safety incentives based on BeRecord compliance and duration targets.</p>
               </div>
               <div class="space-y-4">
                  <div class="p-4 bg-[#f0fdf4] border border-[#16a34a]/20 rounded-2xl flex items-center justify-between hover:shadow-md transition-all cursor-pointer">
                     <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-[#16a34a]/10 flex items-center justify-center shadow-inner">
                           <span class="material-symbols-outlined text-[#16a34a]" data-icon="workspace_premium">workspace_premium</span>
                        </div>
                        <div>
                           <p class="text-sm font-bold text-on-surface">Shift A Night Operations</p>
                           <p class="text-[10px] font-bold text-[#16a34a] uppercase tracking-widest mt-0.5">100% Compliance Record</p>
                        </div>
                     </div>
                     <div class="text-right">
                        <span class="text-[11px] font-bold text-[#16a34a] uppercase border border-[#16a34a]/30 px-3 py-1 rounded-full bg-white shadow-sm">Eligible</span>
                     </div>
                  </div>
                  <div class="p-4 bg-[#fff1f2] border border-error/20 rounded-2xl flex items-center justify-between hover:shadow-md transition-all cursor-pointer">
                     <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-error/10 flex items-center justify-center shadow-inner">
                           <span class="material-symbols-outlined text-error" data-icon="warning_amber">warning_amber</span>
                        </div>
                        <div>
                           <p class="text-sm font-bold text-on-surface">Drill Group 4</p>
                           <p class="text-[10px] font-bold text-error uppercase tracking-widest mt-0.5">Avg Duration &lt; 5 mins</p>
                        </div>
                     </div>
                     <div class="text-right">
                        <span class="text-[11px] font-bold text-error uppercase border border-error/30 px-3 py-1 rounded-full bg-white shadow-sm">Review Flag</span>
                     </div>
                  </div>
               </div>
               <button class="w-full mt-6 py-3 border-2 border-dashed border-outline-variant/30 rounded-xl text-xs font-bold text-on-surface-variant hover:border-primary/50 hover:text-primary hover:bg-[#f8fafc] transition-all">
               View Full Accountability Report
               </button>
            </div>
         </div>
         <!-- Footer -->
         <footer class="flex flex-col sm:flex-row justify-between items-center py-10 border-t border-outline-variant/30 text-on-surface-variant">
            <div class="flex flex-col items-center sm:items-start gap-1">
               <span class="text-xs font-bold">© 2024 Site BMO2 Safety Management System</span>
               <span class="text-[10px] opacity-70">Empowering proactive safety through peer evaluation</span>
            </div>
            <div class="flex gap-8 mt-6 sm:mt-0">
               <a class="text-[11px] font-bold uppercase tracking-wider hover:text-primary transition-colors border-b-2 border-transparent hover:border-primary pb-0.5" href="#">Contact Admin</a>
               <a class="text-[11px] font-bold uppercase tracking-wider hover:text-primary transition-colors border-b-2 border-transparent hover:border-primary pb-0.5" href="#">Technical Support</a>
               <a class="text-[11px] font-bold uppercase tracking-wider hover:text-primary transition-colors border-b-2 border-transparent hover:border-primary pb-0.5" href="#">System Status</a>
            </div>
         </footer>
      </main>
      <!-- Detail modal (data dari API peer-pressure-edukasi.kejadian.detail) -->
      <div id="peer-pressure-detail-modal" class="hidden fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-6 bg-black/40 backdrop-blur-sm" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="peer-pressure-detail-title">
         <div class="absolute inset-0 peer-pressure-modal-backdrop" aria-hidden="true"></div>
         <div class="relative z-10 bg-white max-w-6xl w-full max-h-[min(96vh,1040px)] flex flex-col rounded-2xl border border-neutral-200/90 overflow-hidden">
            <div class="flex items-start justify-between gap-4 px-6 py-5 sm:px-8 sm:py-6 border-b border-neutral-200 shrink-0">
               <div>
                  <h2 id="peer-pressure-detail-title" class="font-headline font-semibold text-xl sm:text-2xl text-neutral-900 tracking-tight">Detail kejadian</h2>
                  <p class="text-[13px] text-neutral-500 mt-1">Kejadian edukasi lalu profil BeRecord pelanggar (jika tersedia)</p>
               </div>
               <button type="button" id="peer-pressure-detail-close" class="p-2 rounded-lg text-neutral-500 hover:text-neutral-900 hover:bg-neutral-100 transition-colors -mr-1" aria-label="Tutup">
                  <span class="material-symbols-outlined text-2xl" data-icon="close">close</span>
               </button>
            </div>
            <div id="peer-pressure-detail-body" class="overflow-y-auto px-6 py-6 sm:px-8 sm:py-8 flex-1 min-h-0 bg-neutral-50/80">
               <div id="peer-pressure-detail-loading" class="hidden flex flex-col items-center justify-center py-16 gap-3 text-on-surface-variant">
                  <span class="material-symbols-outlined text-4xl animate-pulse text-primary" data-icon="progress_activity">progress_activity</span>
                  <p class="text-xs font-bold uppercase tracking-widest">Memuat detail…</p>
               </div>
               <div id="peer-pressure-detail-error" class="hidden text-center py-12 text-error text-sm font-medium"></div>
               <div id="peer-pressure-detail-content" class="hidden max-w-full"></div>
            </div>
         </div>
      </div>
      <script>
      (function () {
        const modal = document.getElementById('peer-pressure-detail-modal');
        const bodyEl = document.getElementById('peer-pressure-detail-body');
        const loadingEl = document.getElementById('peer-pressure-detail-loading');
        const errorEl = document.getElementById('peer-pressure-detail-error');
        const contentEl = document.getElementById('peer-pressure-detail-content');
        const closeBtn = document.getElementById('peer-pressure-detail-close');
        const titleEl = document.getElementById('peer-pressure-detail-title');
        const detailUrlBase = @json(rtrim(url('/peer-pressure-edukasi/kejadian'), '/'));

        function escapeHtml(s) {
          if (s == null || s === '') return '';
          const d = document.createElement('div');
          d.textContent = String(s);
          return d.innerHTML;
        }
        function dash(s) {
          if (s == null || s === '') return '—';
          return String(s);
        }
        /** Avatar: foto dari API (nitip.bep_vw_wp_karyawan) atau inisial di belakang */
        function avatarStack(fotoUrl, initials, sizeClass) {
          var sz = sizeClass || 'w-10 h-10';
          var ini = escapeHtml(initials || '?');
          var img = fotoUrl
            ? '<img src="' + escapeHtml(fotoUrl) + '" alt="" class="absolute inset-0 z-10 h-full w-full rounded-full object-cover" loading="lazy" decoding="async" referrerpolicy="no-referrer" onerror="this.remove()" />'
            : '';
          return '<div class="relative ' + sz + ' shrink-0 overflow-hidden rounded-full bg-neutral-200">'
            + '<span class="absolute inset-0 z-0 flex items-center justify-center text-[11px] font-bold text-neutral-600">' + ini + '</span>'
            + img
            + '</div>';
        }
        function peranLabel(p) {
          return p === 'pelanggar' ? 'Pelanggar' : (p === 'peer' ? 'Peer' : dash(p));
        }

        function hasBerecordData(d) {
          return d.pelanggar_berecord != null && typeof d.pelanggar_berecord === 'object';
        }

        function brRow(label, key, br, wide) {
          var v = br[key];
          return '<div class="' + (wide ? 'sm:col-span-2' : '') + ' flex flex-col gap-1 py-3 first:pt-0 last:pb-0">'
            + '<span class="text-[11px] font-medium text-neutral-500">' + label + '</span>'
            + '<span class="text-sm text-neutral-900 leading-snug break-words">' + escapeHtml(dash(v)) + '</span></div>';
        }

        function renderBerecordSection(d) {
          var br = d.pelanggar_berecord;
          var pg = null;
          (d.peserta || []).forEach(function (p) {
            if (p.peran === 'pelanggar') pg = p;
          });
          if (!pg) return '';
          if (!br || typeof br !== 'object') {
            return ''
              + '<div class="mt-10 pt-8 border-t border-neutral-200 space-y-4">'
              + '<p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">BeRecord pelanggar</p>'
              + '<div class="flex gap-4 text-sm text-neutral-600">'
              + avatarStack(pg.foto_url, pg.initials, 'w-12 h-12')
              + '<div class="flex min-w-0 flex-1 gap-3">'
              + '<span class="material-symbols-outlined text-neutral-400 shrink-0 text-xl" data-icon="link_off">link_off</span>'
              + '<div><p class="font-medium text-neutral-900 mb-1">Tidak terhubung ke BeRecord Nitip</p>'
              + 'Tidak ada baris di <code class="text-xs bg-neutral-100 px-1.5 py-0.5 rounded text-neutral-700">bep_vw_berecord</code> untuk SID <span class="font-mono font-semibold text-neutral-900">' + escapeHtml(pg.sid) + '</span>.</div>'
              + '</div></div></div>';
          }
          var st = (br.status_proses_berecord || '').toUpperCase();
          var statusChip = st.indexOf('ACTIVE') >= 0
            ? 'bg-emerald-50 text-emerald-800'
            : 'bg-neutral-100 text-neutral-700';
          return ''
            + '<div class="mt-10 pt-8 border-t border-neutral-200 space-y-8">'
            + '<p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">BeRecord pelanggar</p>'
            + '<div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">'
            + '<div class="flex min-w-0 gap-4">'
            + avatarStack(pg.foto_url, pg.initials, 'w-14 h-14')
            + '<div class="min-w-0">'
            + '<p class="text-xs text-neutral-500 mb-1">Nama (Nitip · bep_vw_berecord)</p>'
            + '<p class="text-lg font-semibold text-neutral-900 leading-snug">' + escapeHtml(dash(br.nama)) + '</p>'
            + '</div></div>'
            + '<div class="flex flex-wrap gap-2 shrink-0">'
            + '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono bg-neutral-100 text-neutral-900">' + escapeHtml(dash(br.kode_sid)) + '</span>'
            + '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono bg-neutral-100 text-neutral-900">#' + escapeHtml(dash(br.be_record)) + '</span>'
            + '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium ' + statusChip + '">' + escapeHtml(dash(br.status_proses_berecord)) + '</span>'
            + '</div></div>'
            + '<div>'
            + '<p class="text-xs font-semibold text-neutral-500 mb-2">Ringkasan kasus</p>'
            + '<p class="text-sm text-neutral-800 leading-relaxed">' + escapeHtml(dash(br.diskripsi)) + '</p>'
            + '</div>'
            + '<div class="flex flex-wrap gap-x-10 gap-y-2 text-sm">'
            + '<div><span class="text-xs text-neutral-500 block mb-0.5">Mulai berlaku</span><span class="font-medium text-neutral-900">' + escapeHtml(dash(br.start_date_be_record)) + '</span></div>'
            + '<div><span class="text-xs text-neutral-500 block mb-0.5">Berakhir</span><span class="font-medium text-neutral-900">' + escapeHtml(dash(br.end_date_be_record)) + '</span></div>'
            + '</div>'
            + '<div class="space-y-3 pt-2 border-t border-neutral-100">'
            + '<p class="text-xs font-semibold text-neutral-500">Organisasi &amp; lokasi</p>'
            + '<div class="divide-y divide-neutral-100">'
            + brRow('Perusahaan', 'perusahaan', br) + brRow('Provinsi', 'alamat_province', br) + brRow('Struktural', 'j_strutural', br) + brRow('Fungsional', 'j_fungsional', br)
            + '</div></div>'
            + '<div class="space-y-3 pt-2 border-t border-neutral-100">'
            + '<p class="text-xs font-semibold text-neutral-500">Aturan &amp; izin</p>'
            + '<div class="divide-y divide-neutral-100">' + brRow('Work permit', 'work_permit', br, true) + brRow('Golden rules', 'golden_rules', br, true) + '</div></div>'
            + '<div class="space-y-3 pt-2 border-t border-neutral-100">'
            + '<p class="text-xs font-semibold text-neutral-500">PIC &amp; status</p>'
            + '<div class="divide-y divide-neutral-100">'
            + brRow('PIC persetujuan', 'pic_approval', br) + brRow('PIC verifikasi', 'pic_verifikasi', br)
            + brRow('Status izin', 'status_permit', br) + brRow('Tipe', 'tipe_berecord', br) + brRow('Status berlaku', 'status_berecord', br) + brRow('Kategori', 'kategori_berecord', br) + brRow('Kategori kecelakaan', 'kategori_kecelakaan', br)
            + '</div></div>'
            + '<div class="flex flex-wrap gap-x-8 gap-y-2 text-xs text-neutral-500 pt-4 border-t border-neutral-100">'
            + '<span>ID karyawan: <span class="font-mono text-neutral-800">' + escapeHtml(dash(br.id_status_karyawan)) + '</span></span>'
            + '<span>ID sink Nitip: <span class="font-mono text-neutral-800">' + escapeHtml(dash(br.id)) + '</span></span>'
            + '</div>'
            + '</div>';
        }

        function renderPeerPressureSection(d) {
          const sb = d.status_badge || {};
          const statusHtml = '<span class="' + escapeHtml(sb.spanClass || '') + '">' + escapeHtml(sb.label || '') + '</span>';
          const showPerusahaanHere = !hasBerecordData(d);
          let pesertaRows = '';
          const list = d.peserta || [];
          if (list.length === 0) {
            pesertaRows = '<tr><td colspan="5" class="py-3 text-sm text-neutral-500">Tidak ada peserta.</td></tr>';
          } else {
            list.forEach(function (p) {
              pesertaRows += '<tr>' +
                '<td class="py-3 pr-2 align-middle">' + avatarStack(p.foto_url, p.initials, 'w-10 h-10') + '</td>' +
                '<td class="py-3 pr-4 text-sm font-medium font-mono text-neutral-900 align-middle">' + escapeHtml(p.sid) + '</td>' +
                '<td class="py-3 pr-4 text-sm text-neutral-800 align-middle">' + escapeHtml(p.nama) + '</td>' +
                '<td class="py-3 pr-4 text-sm text-neutral-600 align-middle">' + escapeHtml(peranLabel(p.peran)) + '</td>' +
                '<td class="py-3 text-sm text-neutral-500 tabular-nums align-middle w-14">' + escapeHtml(String(p.urutan != null ? p.urutan : '')) + '</td></tr>';
            });
          }
          const ev = d.evidence_url;
          const evidenceBlock = ev
            ? '<a href="' + escapeHtml(ev) + '" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline"><span class="material-symbols-outlined text-lg" data-icon="open_in_new">open_in_new</span>Buka tautan evidence</a>'
            : '<span class="text-sm text-neutral-500">Belum ada tautan evidence</span>';
          var ringkasan = '';
          if (showPerusahaanHere) {
            ringkasan += '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Perusahaan (kejadian)</span><span class="text-sm text-neutral-900">' + escapeHtml(dash(d.perusahaan)) + '</span></div>';
          }
          ringkasan += ''
            + '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Departemen</span><span class="text-sm text-neutral-900">' + escapeHtml(dash(d.departemen)) + '</span></div>'
            + '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Aktivitas &amp; kelompok</span><span class="text-sm text-neutral-900">' + escapeHtml(dash(d.aktivitas_pekerjaan)) + (d.kelompok_aktivitas_pekerjaan ? ' · ' + escapeHtml(dash(d.kelompok_aktivitas_pekerjaan)) : '') + '</span></div>'
            + '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Jenis kelompok kerja</span><span class="text-sm text-neutral-900">' + escapeHtml(dash(d.jenis_kelompok_kerja)) + '</span></div>'
            + '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Tasklist temuan</span><span class="text-sm text-neutral-900">' + escapeHtml(dash(d.tasklist_temuan)) + '</span></div>'
            + '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Referensi BeRecord (impor Excel)</span><span class="text-sm font-mono text-neutral-900">' + escapeHtml(dash(d.id_berecord)) + '</span></div>'
            + '<div class="flex flex-col gap-1 py-3"><span class="text-[11px] font-medium text-neutral-500">Status pelaksanaan edukasi</span><span class="text-sm text-neutral-900">' + escapeHtml(dash(d.status_pelaksanaan_edukasi)) + '</span></div>';
          return ''
            + '<div class="space-y-8">'
            + '<div class="flex flex-wrap items-start justify-between gap-3 pb-5 border-b border-neutral-200">'
            + '<div>'
            + '<h3 class="text-base font-semibold text-neutral-900">Kejadian &amp; edukasi</h3>'
            + '<p class="text-[13px] text-neutral-500 mt-0.5">Catatan Peer Pressure di aplikasi</p></div>'
            + '<div class="flex items-center gap-2">' + statusHtml + '</div></div>'
            + '<div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">'
            + '<div class="space-y-2 pl-4 border-l-2 border-neutral-300">'
            + '<p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">Temuan</p>'
            + '<p class="text-sm font-medium text-neutral-900">' + escapeHtml(d.formatted_temuan) + '</p>'
            + '<p class="text-sm text-neutral-600 leading-relaxed">' + escapeHtml(dash(d.kelompok_lokasi_temuan)) + ' · ' + escapeHtml(dash(d.lokasi_temuan)) + '</p>'
            + '</div>'
            + '<div class="space-y-2 pl-4 border-l-2 border-neutral-300">'
            + '<p class="text-xs font-semibold uppercase tracking-wide text-neutral-400">Edukasi</p>'
            + '<p class="text-sm font-medium text-neutral-900">' + escapeHtml(d.formatted_edukasi) + '</p>'
            + '<p class="text-sm text-neutral-600 leading-relaxed">' + escapeHtml(dash(d.kelompok_lokasi_edukasi)) + ' · ' + escapeHtml(dash(d.lokasi_edukasi)) + '</p>'
            + '<p class="text-sm text-neutral-600">Pemimpin: ' + escapeHtml(dash(d.pemimpin_edukasi)) + ' · Durasi ' + escapeHtml(String(d.durasi_edukasi_menit != null ? d.durasi_edukasi_menit : '—')) + ' menit</p>'
            + '</div></div>'
            + '<div>'
            + '<p class="text-xs font-semibold text-neutral-500 mb-2">Kategori deviasi</p>'
            + '<span class="text-sm font-medium text-neutral-900">' + escapeHtml(dash(d.kategori_deviasi)) + '</span>'
            + '</div>'
            + '<div>'
            + '<p class="text-xs font-semibold text-neutral-500 mb-2">Kronologi</p>'
            + '<p class="text-sm text-neutral-800 leading-relaxed whitespace-pre-wrap">' + escapeHtml(dash(d.kronologi_temuan)) + '</p></div>'
            + '<div class="space-y-3">'
            + '<p class="text-xs font-semibold text-neutral-500">Konteks tambahan</p>'
            + '<div class="divide-y divide-neutral-200">' + ringkasan + '</div></div>'
            + '<div>'
            + '<p class="text-xs font-semibold text-neutral-500 mb-2">Evidence</p>' + evidenceBlock + '</div>'
            + '<div>'
            + '<p class="text-xs font-semibold text-neutral-500 mb-1">Peserta</p>'
            + '<p class="text-[11px] text-neutral-400 mb-2">Foto dari Nitip · <span class="font-mono">bep_vw_wp_karyawan</span> (kode_sid)</p>'
            + '<div class="overflow-x-auto">'
            + '<table class="w-full text-left border-collapse">'
            + '<thead><tr class="border-b border-neutral-200 text-[11px] font-semibold text-neutral-500">'
            + '<th class="w-12 py-2.5 pr-2 text-left"><span class="sr-only">Foto</span></th>'
            + '<th class="py-2.5 pr-4 text-left">SID</th><th class="py-2.5 pr-4 text-left">Nama</th><th class="py-2.5 pr-4 text-left">Peran</th><th class="py-2.5 w-16 text-left">Urut</th></tr></thead>'
            + '<tbody class="divide-y divide-neutral-100">' + pesertaRows + '</tbody></table></div></div>'
            + '</div>';
        }

        function renderDetail(d) {
          return '<div class="space-y-0 max-w-full">' + renderPeerPressureSection(d) + renderBerecordSection(d) + '</div>';
        }

        function setOpen(open) {
          if (!modal) return;
          if (open) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
          } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
          }
        }

        function showLoading() {
          loadingEl.classList.remove('hidden');
          loadingEl.classList.add('flex');
          errorEl.classList.add('hidden');
          contentEl.classList.add('hidden');
          errorEl.textContent = '';
          contentEl.innerHTML = '';
        }

        function showError(msg) {
          loadingEl.classList.add('hidden');
          loadingEl.classList.remove('flex');
          errorEl.classList.remove('hidden');
          errorEl.textContent = msg;
          contentEl.classList.add('hidden');
        }

        function showContent(html) {
          loadingEl.classList.add('hidden');
          loadingEl.classList.remove('flex');
          errorEl.classList.add('hidden');
          contentEl.classList.remove('hidden');
          contentEl.innerHTML = html;
        }

        async function openForId(id) {
          showLoading();
          setOpen(true);
          titleEl.textContent = 'Detail kejadian #' + id;
          try {
            const res = await fetch(detailUrlBase + '/' + id, {
              headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
              credentials: 'same-origin'
            });
            if (!res.ok) {
              showError(res.status === 404 ? 'Data tidak ditemukan.' : 'Gagal memuat detail (' + res.status + ').');
              return;
            }
            const data = await res.json();
            showContent(renderDetail(data));
          } catch (e) {
            showError('Tidak dapat memuat detail. Periksa koneksi lalu coba lagi.');
          }
        }

        document.querySelectorAll('.js-peer-kejadian-row').forEach(function (row) {
          row.addEventListener('click', function (e) {
            if (e.target.closest('a')) return;
            const id = row.getAttribute('data-kejadian-id');
            if (id) openForId(id);
          });
          row.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            e.preventDefault();
            const id = row.getAttribute('data-kejadian-id');
            if (id) openForId(id);
          });
        });

        function closeModal() {
          setOpen(false);
        }

        closeBtn.addEventListener('click', closeModal);
        modal.querySelector('.peer-pressure-modal-backdrop').addEventListener('click', closeModal);
        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
        });
      })();
      </script>
   </body>
</html>