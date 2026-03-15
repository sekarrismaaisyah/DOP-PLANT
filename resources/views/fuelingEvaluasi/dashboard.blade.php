<!DOCTYPE html>
<html class="light" lang="en">
   <head>
      <meta charset="utf-8"/>
      <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
      <title>Dashboard</title>
      <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
      <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap" rel="stylesheet"/>
      <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
      <script id="tailwind-config">
         tailwind.config = {
             darkMode: "class",
             theme: {
                 extend: {
                     colors: {
                         "primary": "#1e3fae",
                         "background-light": "#f6f6f8",
                         "background-dark": "#121520",
                         "success": "#07883d",
                         "warning": "#f59e0b",
                         "danger": "#dc2626",
                     },
                     fontFamily: {
                         "display": ["Inter"]
                     },
                     borderRadius: {
                         "DEFAULT": "0.5rem",
                         "lg": "1rem",
                         "xl": "1.5rem",
                         "full": "9999px"
                     },
                 },
             },
         }
      </script>
      <style>
         body { font-family: 'Inter', sans-serif; }
         .scrollbar-hide::-webkit-scrollbar { display: none; }
      </style>
   </head>
   <body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen font-display">
      <!-- Top Navigation Bar -->
      <header class="sticky top-0 z-50 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-3">
         <div class=" mx-auto flex items-center justify-between gap-4">
            <div class="flex items-center gap-6">
               <div class="flex items-center gap-3">
                  <div class="bg-primary p-2 rounded-lg">
                     <span class="material-symbols-outlined text-white">forklift</span>
                  </div>
                  <div>
                     <h1 class="text-lg font-bold leading-tight">SPIP Dashboard</h1>
                     <p class="text-xs text-slate-500 dark:text-slate-400">Coal Mining Monitoring System</p>
                  </div>
               </div>
               <nav class="hidden lg:flex items-center gap-6 ml-4">
                  <a class="text-sm font-semibold text-primary border-b-2 border-primary pb-1" href="#">Dashboard</a>
                  <a class="text-sm font-medium text-slate-500 hover:text-primary transition-colors" href="#">Units</a>
                  <a class="text-sm font-medium text-slate-500 hover:text-primary transition-colors" href="#">Permits</a>
                  <a class="text-sm font-medium text-slate-500 hover:text-primary transition-colors" href="#">Reports</a>
               </nav>
            </div>
            <div class="flex items-center gap-4 flex-1 justify-end">
               <div class="relative max-w-xs w-full hidden md:block">
                  <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
                  <input class="w-full pl-10 pr-4 py-2 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-sm focus:ring-2 focus:ring-primary" placeholder="Search Unit No..." type="text"/>
               </div>
               <div class="flex items-center gap-2 border-l border-slate-200 dark:border-slate-700 pl-4">
                  <button class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg relative">
                  <span class="material-symbols-outlined">notifications</span>
                  <span class="absolute top-2 right-2 w-2 h-2 bg-danger rounded-full ring-2 ring-white"></span>
                  </button>
                  <div class="flex items-center gap-3 ml-2">
                     <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold">Admin User</p>
                        <p class="text-[10px] text-slate-500">Operation Lead</p>
                     </div>
                     <img class="w-9 h-9 rounded-full object-cover border-2 border-slate-200" data-alt="User profile picture" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD5rUIJMNnte4SxTCOC8vq0eB_7lNDKVAoQkBJFewRVl6Krab-ivPCCSP6TSjlyIzAUtKjIbtYzXZ4OdeeL3l7n6UXiV7CM8ZUjwQ0tK3iN4RiAJV1ipIzbX4u69a490pbJ4GojKHoA6qsUEudp2lfCBP5W6gPBagY0Zdx_0qr-hmc7Jpuac-32BNBYO7j6aasnMjH4M7kRfu8g1t3KmlvYiMiHQI87b6Q553EjEYj8qflLZh1xG4A6fMtxcBpHmKRpRb50XugHvHw7"/>
                  </div>
               </div>
            </div>
         </div>
      </header>
      <main class=" mx-auto p-6 space-y-6">
         <!-- Header Controls -->
         <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
               <h2 class="text-2xl font-bold tracking-tight">Real-time SPIP Permit &amp; Performance Monitoring</h2>
               <p class="text-sm text-slate-500">Last updated: <span class="font-medium">Today, 10:45 AM</span> • Source: HSE Automation System</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
               <!-- <div class="flex items-center bg-white dark:bg-slate-900 rounded-lg p-1 border border-slate-200 dark:border-slate-800">
                  <button class="px-4 py-1.5 text-xs font-semibold bg-primary text-white rounded-md shadow-sm">BMO 1</button>
                  <button class="px-4 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-md">LMO</button>
                  <button class="px-4 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-md">GMO</button>
               </div> -->
               <button class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-sm font-medium hover:bg-slate-50">
               <span class="material-symbols-outlined text-lg">calendar_today</span>
               <span>Oct 24, 2023</span>
               </button>
               <div class="flex gap-2">
                  <button class="p-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg hover:text-primary transition-colors">
                  <span class="material-symbols-outlined">download</span>
                  </button>
               </div>
            </div>
         </div>
         <!-- KPI Cards Row -->
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total unit beroperasi (jarak > 10m) -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
               <div class="flex justify-between items-start mb-3">
                  <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Unit Beroperasi</p>
                  <div class="bg-primary/10 p-2 rounded-lg text-primary">
                     <span class="material-symbols-outlined text-xl">commute</span>
                  </div>
               </div>
               <div class="flex items-end gap-3">
                  <h3 class="text-3xl font-bold" id="kpi_total_unit">—</h3>
               </div>
               <p class="text-xs text-slate-400 mt-2">Unit dengan jarak tempuh &gt; 10 m</p>
            </div>
            <!-- Card 2: Compliance rate (PASSED / total beroperasi) -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
               <div class="flex justify-between items-start mb-3">
                  <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Compliance Rate</p>
                  <div class="bg-success/10 p-2 rounded-lg text-success">
                     <span class="material-symbols-outlined text-xl">verified</span>
                  </div>
               </div>
               <div class="flex items-center justify-between">
                  <h3 class="text-3xl font-bold" id="kpi_compliance">—</h3>
                  <div class="relative w-12 h-12" id="kpi_compliance_ring">
                     <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                        <path class="text-slate-100 dark:text-slate-800" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4"></path>
                        <path class="text-success" id="kpi_compliance_arc" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="0, 100" stroke-linecap="round" stroke-width="4"></path>
                     </svg>
                  </div>
               </div>
               <p class="text-xs text-slate-400 mt-2">% unit beroperasi dengan status PASSED (Becomline)</p>
            </div>
            <!-- Card 3: Avg waktu per unit (jam) -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
               <div class="flex justify-between items-start mb-3">
                  <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Avg Durasi / Unit</p>
                  <div class="bg-warning/10 p-2 rounded-lg text-warning">
                     <span class="material-symbols-outlined text-xl">timer</span>
                  </div>
               </div>
               <h3 class="text-3xl font-bold"><span id="kpi_avg_waktu">—</span> <span class="text-sm font-normal text-slate-500">jam</span></h3>
               <p class="text-xs text-slate-400 mt-2">Rata-rata total jam operasi per unit beroperasi</p>
            </div>
            <!-- Card 4: Fuel efficiency (km/L) - coming soon -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
               <div class="absolute top-0 right-0">
                  <span class="bg-primary text-[10px] text-white px-3 py-1 font-bold rounded-bl-lg uppercase tracking-widest">Coming Soon</span>
               </div>
               <div class="flex justify-between items-start mb-3">
                  <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Fuel Efficiency</p>
                  <div class="bg-slate-100 dark:bg-slate-800 p-2 rounded-lg text-slate-400">
                     <span class="material-symbols-outlined text-xl">local_gas_station</span>
                  </div>
               </div>
               <h3 class="text-3xl font-bold text-slate-300" id="kpi_fuel">—</h3>
               <p class="text-xs text-slate-400 mt-2 italic">km/L (total km / total konsumsi fuel)</p>
            </div>
         </div>
         <!-- Main Content Area -->
         <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Unit Performance Table (60%) -->
            <div class="lg:col-span-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
               <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-wrap justify-between items-center gap-3">
                  <h3 class="font-bold">Unit Performance Details</h3>
                  <div class="flex flex-wrap items-center gap-3">
                     <input type="date" id="dashboard_date_from" class="px-3 py-1.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800"/>
                     <span class="text-slate-400">s/d</span>
                     <input type="date" id="dashboard_date_to" class="px-3 py-1.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800"/>
                     <button type="button" id="dashboard_btn_load" class="flex items-center gap-1.5 px-4 py-1.5 text-xs font-semibold bg-primary text-white rounded-lg hover:opacity-90">
                        <span class="material-symbols-outlined text-sm">refresh</span> Muat Data
                     </button>
                     <input type="text" id="dashboard_search" placeholder="Cari..." class="px-3 py-1.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 w-40" />
                     <a href="{{ route('fueling-evaluasi.per-hari') }}?date_from=" id="dashboard_link_perhari" class="text-xs font-medium text-primary hover:underline whitespace-nowrap">Tabel lengkap →</a>
                  </div>
               </div>
               <div class="overflow-x-auto">
                  <table class="w-full text-left border-collapse">
                     <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                           <th class="px-3 py-2.5 text-[11px] font-bold text-slate-500 uppercase whitespace-nowrap">TANGGAL</th>
                           <th class="px-3 py-2.5 text-[11px] font-bold text-slate-500 uppercase whitespace-nowrap">NO UNIT</th>
                           <th class="px-3 py-2.5 text-[11px] font-bold text-slate-500 uppercase whitespace-nowrap">JARAK DITEMPUH</th>
                           <th class="px-3 py-2.5 text-[11px] font-bold text-slate-500 uppercase whitespace-nowrap">DURASI (jam)</th>
                           <th class="px-3 py-2.5 text-[11px] font-bold text-slate-500 uppercase whitespace-nowrap">Perusahaan Pemilik</th>
                           <th class="px-3 py-2.5 text-[11px] font-bold text-slate-500 uppercase whitespace-nowrap">Site Operasional</th>
                           <th class="px-3 py-2.5 text-[11px] font-bold text-slate-500 uppercase whitespace-nowrap">Jenis Unit SPIP</th>
                           <th class="px-3 py-2.5 text-[11px] font-bold text-slate-500 uppercase whitespace-nowrap">Expired</th>
                           <th class="px-3 py-2.5 text-[11px] font-bold text-slate-500 uppercase whitespace-nowrap">Status Permit SPIP</th>
                           <th class="px-3 py-2.5 text-[11px] font-bold text-slate-500 uppercase whitespace-nowrap">AVG per Day</th>
                        </tr>
                     </thead>
                     <tbody id="dashboard_table_body" class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr><td colspan="10" class="px-5 py-8 text-center text-slate-500 text-sm">Pilih rentang tanggal dan klik Muat Data.</td></tr>
                     </tbody>
                  </table>
               </div>
               <div class="mt-auto p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2 flex-wrap">
                  <p class="text-xs text-slate-500" id="dashboard_table_info">—</p>
                  <div class="flex items-center gap-2" id="dashboard_pagination">
                     <button type="button" id="dashboard_prev" class="px-3 py-1 text-xs border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-50 disabled:pointer-events-none">Prev</button>
                     <span class="text-xs text-slate-500" id="dashboard_page_text">Halaman 1</span>
                     <button type="button" id="dashboard_next" class="px-3 py-1 text-xs bg-primary text-white rounded-lg hover:opacity-90 disabled:opacity-50 disabled:pointer-events-none">Next</button>
                  </div>
               </div>
            </div>
            <!-- Compliance Overview (40%) -->
            <div class="lg:col-span-2 space-y-6">
               <!-- Donut Chart -->
               <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                  <h3 class="font-bold mb-6">Compliance Status Distribution</h3>
                  <div class="flex items-center justify-center relative mb-6">
                     <div class="w-40 h-40 rounded-full relative flex items-center justify-center" id="compliance_donut_ring">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                           <path class="text-slate-100 dark:text-slate-800" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4"></path>
                           <path id="donut_passed" class="text-success" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831" fill="none" stroke="currentColor" stroke-dasharray="0, 100" stroke-linecap="round" stroke-width="4"></path>
                           <path id="donut_expiring" class="text-warning" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831" fill="none" stroke="currentColor" stroke-dasharray="0, 100" stroke-linecap="round" stroke-width="4"></path>
                           <path id="donut_notpassed" class="text-danger" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831" fill="none" stroke="currentColor" stroke-dasharray="0, 100" stroke-linecap="round" stroke-width="4"></path>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                           <p class="text-3xl font-bold" id="kpi_donut_total">—</p>
                           <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Units</p>
                        </div>
                     </div>
                  </div>
                  <div class="grid grid-cols-3 gap-2">
                     <div class="text-center p-2 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-[10px] font-bold text-success uppercase">Passed</p>
                        <p class="text-lg font-bold" id="kpi_passed">—</p>
                     </div>
                     <div class="text-center p-2 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-[10px] font-bold text-warning uppercase">Expiring</p>
                        <p class="text-lg font-bold" id="kpi_expiring">—</p>
                     </div>
                     <div class="text-center p-2 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-[10px] font-bold text-danger uppercase">Not Passed</p>
                        <p class="text-lg font-bold" id="kpi_not_passed">—</p>
                     </div>
                  </div>
               </div>
               <!-- Site Distribution Bar Chart -->
               <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                  <h3 class="font-bold mb-4">Site Performance Ranking</h3>
                  <div class="space-y-4" id="site_ranking_list">
                     <p class="text-sm text-slate-500">Memuat...</p>
                  </div>
               </div>
            </div>
         </div>
         <!-- Bottom Performance Trend -->
         <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
               <div>
                  <h3 class="font-bold">MTD Performance Trend</h3>
                  <p class="text-xs text-slate-500">Fleet wide compliance and availability metric</p>
               </div>
               <div class="flex items-center gap-2">
                  <button class="flex items-center gap-2 px-3 py-1.5 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-bold hover:bg-slate-50 transition-colors">
                  <span class="material-symbols-outlined text-sm">description</span> CSV
                  </button>
                  <button class="flex items-center gap-2 px-3 py-1.5 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-bold hover:bg-slate-50 transition-colors">
                  <span class="material-symbols-outlined text-sm">picture_as_pdf</span> PDF
                  </button>
               </div>
            </div>
            <!-- Mockup for Line Chart -->
            <div class="h-48 w-full flex items-end justify-between px-2 gap-4">
               <div class="flex-1 bg-primary/10 hover:bg-primary/20 transition-colors rounded-t-sm h-[40%] group relative">
                  <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">72%</span>
               </div>
               <div class="flex-1 bg-primary/10 hover:bg-primary/20 transition-colors rounded-t-sm h-[55%] group relative"></div>
               <div class="flex-1 bg-primary/10 hover:bg-primary/20 transition-colors rounded-t-sm h-[48%] group relative"></div>
               <div class="flex-1 bg-primary/10 hover:bg-primary/20 transition-colors rounded-t-sm h-[65%] group relative"></div>
               <div class="flex-1 bg-primary/20 hover:bg-primary/30 transition-colors rounded-t-sm h-[72%] group relative"></div>
               <div class="flex-1 bg-primary hover:bg-primary/90 transition-colors rounded-t-sm h-[88%] group relative">
                  <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold opacity-0 group-hover:opacity-100 transition-opacity">88%</span>
               </div>
               <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-t-sm h-[10%]"></div>
               <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-t-sm h-[10%]"></div>
               <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-t-sm h-[10%]"></div>
               <div class="flex-1 bg-slate-100 dark:bg-slate-800 rounded-t-sm h-[10%]"></div>
            </div>
            <div class="flex justify-between mt-4 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
               <span>Week 1</span>
               <span>Week 2</span>
               <span>Week 3</span>
               <span>Week 4</span>
               <span>Week 5</span>
            </div>
         </div>
         <!-- Permit Expiry Timeline mini section -->
         
      </main>
      <footer class=" mx-auto px-6 py-8 mt-12 border-t border-slate-200 dark:border-slate-800 text-center">
         <p class="text-sm text-slate-500 font-medium">Data updated hourly | Source: HSE Automation System</p>
         <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-[0.2em] font-bold">© 2024 Coal Mining Fleet Ops. All rights reserved.</p>
      </footer>
      <script>
      (function() {
         var apiUrl = "{{ url()->route('fueling-evaluasi.per-hari.all-data') }}";
         var statsUrl = "{{ url()->route('fueling-evaluasi.per-hari.dashboard-stats') }}";
         var PAGE_SIZE = 9;
         var dateFrom = document.getElementById('dashboard_date_from');
         var dateTo = document.getElementById('dashboard_date_to');
         var btnLoad = document.getElementById('dashboard_btn_load');
         var searchInput = document.getElementById('dashboard_search');
         var tbody = document.getElementById('dashboard_table_body');
         var infoEl = document.getElementById('dashboard_table_info');
         var linkPerhari = document.getElementById('dashboard_link_perhari');
         var btnPrev = document.getElementById('dashboard_prev');
         var btnNext = document.getElementById('dashboard_next');
         var pageText = document.getElementById('dashboard_page_text');
         var fullData = [];
         var currentPage = 1;

         function setDefaultDates() {
            var to = new Date();
            var from = new Date(to);
            from.setDate(from.getDate() - 30);
            dateFrom.value = from.toISOString().slice(0, 10);
            dateTo.value = to.toISOString().slice(0, 10);
         }

         function loadStats(from, to) {
            if (!from || !to) return;
            var siteListEl = document.getElementById('site_ranking_list');
            if (siteListEl) siteListEl.innerHTML = '<p class="text-sm text-slate-500">Memuat...</p>';
            fetch(statsUrl + '?date_from=' + encodeURIComponent(from) + '&date_to=' + encodeURIComponent(to), { credentials: 'same-origin' })
               .then(function(res) { return res.json(); })
               .then(function(s) {
                  var totalEl = document.getElementById('kpi_total_unit');
                  var compEl = document.getElementById('kpi_compliance');
                  var compArc = document.getElementById('kpi_compliance_arc');
                  var avgEl = document.getElementById('kpi_avg_waktu');
                  var fuelEl = document.getElementById('kpi_fuel');
                  if (totalEl) totalEl.textContent = s.total_unit_beroperasi != null ? Number(s.total_unit_beroperasi) : '—';
                  if (compEl) compEl.textContent = s.compliance_pct != null ? s.compliance_pct + '%' : '—';
                  if (compArc) compArc.setAttribute('stroke-dasharray', (s.compliance_pct != null ? s.compliance_pct : 0) + ', 100');
                  if (avgEl) avgEl.textContent = s.avg_waktu_jam_per_unit != null ? Number(s.avg_waktu_jam_per_unit) : '—';
                  if (fuelEl) fuelEl.textContent = s.avg_fuel_km_per_l != null ? Number(s.avg_fuel_km_per_l) : '—';
                  var donutTotal = document.getElementById('kpi_donut_total');
                  var kpiPassed = document.getElementById('kpi_passed');
                  var kpiExpiring = document.getElementById('kpi_expiring');
                  var kpiNotPassed = document.getElementById('kpi_not_passed');
                  if (donutTotal) donutTotal.textContent = s.total_unit_beroperasi != null ? s.total_unit_beroperasi : '—';
                  if (kpiPassed) kpiPassed.textContent = s.passed_count != null ? s.passed_count : '—';
                  if (kpiExpiring) kpiExpiring.textContent = s.expiring_count != null ? s.expiring_count : '—';
                  if (kpiNotPassed) kpiNotPassed.textContent = s.not_passed_count != null ? s.not_passed_count : '—';
                  var total = s.total_unit_beroperasi || 0;
                  var p1 = total > 0 ? (s.passed_count || 0) / total * 100 : 0;
                  var p2 = total > 0 ? (s.expiring_count || 0) / total * 100 : 0;
                  var p3 = total > 0 ? (s.not_passed_count || 0) / total * 100 : 0;
                  var arcPassed = document.getElementById('donut_passed');
                  var arcExpiring = document.getElementById('donut_expiring');
                  var arcNotPassed = document.getElementById('donut_notpassed');
                  if (arcPassed) { arcPassed.setAttribute('stroke-dasharray', p1 + ', 100'); arcPassed.setAttribute('stroke-dashoffset', '0'); }
                  if (arcExpiring) { arcExpiring.setAttribute('stroke-dasharray', p2 + ', 100'); arcExpiring.setAttribute('stroke-dashoffset', '-' + p1); }
                  if (arcNotPassed) { arcNotPassed.setAttribute('stroke-dasharray', p3 + ', 100'); arcNotPassed.setAttribute('stroke-dashoffset', '-' + (p1 + p2)); }
                  var list = s.site_ranking || [];
                  if (siteListEl) {
                     if (list.length === 0) {
                        siteListEl.innerHTML = '<p class="text-sm text-slate-500">Tidak ada data site.</p>';
                     } else {
                        var html = '';
                        for (var i = 0; i < list.length; i++) {
                           var x = list[i];
                           var pct = Math.min(100, Math.max(0, x.pct || 0));
                           var siteLabel = (x.site === '-' ? '(Tanpa site)' : String(x.site)).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                           html += '<div class="space-y-1"><div class="flex justify-between text-xs font-semibold"><span>' + siteLabel + '</span><span>' + pct + '%</span></div><div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2"><div class="bg-primary h-2 rounded-full transition-all" style="width:' + pct + '%"></div></div></div>';
                        }
                        siteListEl.innerHTML = html;
                     }
                  }
               })
               .catch(function() {
                  var totalEl = document.getElementById('kpi_total_unit');
                  var compEl = document.getElementById('kpi_compliance');
                  var avgEl = document.getElementById('kpi_avg_waktu');
                  if (totalEl) totalEl.textContent = '—';
                  if (compEl) compEl.textContent = '—';
                  if (document.getElementById('kpi_compliance_arc')) document.getElementById('kpi_compliance_arc').setAttribute('stroke-dasharray', '0, 100');
                  if (avgEl) avgEl.textContent = '—';
                  var donutTotal = document.getElementById('kpi_donut_total');
                  if (donutTotal) donutTotal.textContent = '—';
                  var kpiPassed = document.getElementById('kpi_passed');
                  var kpiExpiring = document.getElementById('kpi_expiring');
                  var kpiNotPassed = document.getElementById('kpi_not_passed');
                  if (kpiPassed) kpiPassed.textContent = '—';
                  if (kpiExpiring) kpiExpiring.textContent = '—';
                  if (kpiNotPassed) kpiNotPassed.textContent = '—';
                  var arcPassed = document.getElementById('donut_passed');
                  var arcExpiring = document.getElementById('donut_expiring');
                  var arcNotPassed = document.getElementById('donut_notpassed');
                  if (arcPassed) arcPassed.setAttribute('stroke-dasharray', '0, 100');
                  if (arcExpiring) arcExpiring.setAttribute('stroke-dasharray', '0, 100');
                  if (arcNotPassed) arcNotPassed.setAttribute('stroke-dasharray', '0, 100');
                  if (siteListEl) siteListEl.innerHTML = '<p class="text-sm text-slate-500">Gagal memuat data.</p>';
               });
         }

         function statusBadge(status) {
            var s = (status || '').toUpperCase();
            if (s === 'PASSED') return '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-success/10 text-success border border-success/20"><span class="material-symbols-outlined text-xs">check_circle</span> PASSED</span>';
            if (s === 'N/A' || s === 'NOT PASSED' || s === 'EXPIRED') return '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-danger/10 text-danger border border-danger/20">' + (status || 'N/A') + '</span>';
            return '<span class="text-sm">' + (status || '-') + '</span>';
         }

         function escapeHtml(str) {
            if (str == null) return '';
            var s = String(str);
            return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
         }

         function rowMatchSearch(r, q) {
            if (!q) return true;
            var low = q.toLowerCase();
            var concat = [r.tanggal, r.no_unit, r.jarak, r.perusahaan_pemilik, r.site_operasional, r.jenis_unit_spip, r.expired, r.status_permit_spip, r.avg_per_day].join(' ').toLowerCase();
            return concat.indexOf(low) !== -1;
         }

         function applyFilterAndRender() {
            var query = (searchInput && searchInput.value) ? searchInput.value.trim() : '';
            var filtered = query ? fullData.filter(function(r) { return rowMatchSearch(r, query); }) : fullData;
            var total = filtered.length;
            var totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
            currentPage = Math.min(Math.max(1, currentPage), totalPages);
            var start = (currentPage - 1) * PAGE_SIZE;
            var pageRows = filtered.slice(start, start + PAGE_SIZE);

            if (pageRows.length === 0) {
               tbody.innerHTML = '<tr><td colspan="10" class="px-5 py-8 text-center text-slate-500 text-sm">' + (fullData.length === 0 ? 'Tidak ada data untuk rentang tanggal ini.' : 'Tidak ada hasil untuk pencarian.') + '</td></tr>';
            } else {
               var html = '';
               for (var i = 0; i < pageRows.length; i++) {
                  var r = pageRows[i];
                  var durasi = (r.total_jam != null && r.total_jam !== '') ? (Number(r.total_jam) + ' jam') : '-';
                  html += '<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">' +
                     '<td class="px-3 py-3 text-sm">' + escapeHtml(r.tanggal) + '</td>' +
                     '<td class="px-3 py-3 text-sm font-bold text-primary">' + escapeHtml(r.no_unit) + '</td>' +
                     '<td class="px-3 py-3 text-sm font-medium">' + escapeHtml(r.jarak) + '</td>' +
                     '<td class="px-3 py-3 text-sm">' + escapeHtml(durasi) + '</td>' +
                     '<td class="px-3 py-3 text-sm">' + escapeHtml(r.perusahaan_pemilik) + '</td>' +
                     '<td class="px-3 py-3 text-sm">' + escapeHtml(r.site_operasional) + '</td>' +
                     '<td class="px-3 py-3 text-sm">' + escapeHtml(r.jenis_unit_spip) + '</td>' +
                     '<td class="px-3 py-3 text-sm">' + escapeHtml(r.expired) + '</td>' +
                     '<td class="px-3 py-3">' + statusBadge(r.status_permit_spip) + '</td>' +
                     '<td class="px-3 py-3 text-sm">' + escapeHtml(r.avg_per_day) + '</td>' +
                     '</tr>';
               }
               tbody.innerHTML = html;
            }
            var end = Math.min(start + PAGE_SIZE, total);
            infoEl.textContent = total === 0 ? '0 baris' : 'Menampilkan ' + (start + 1) + '-' + end + ' dari ' + total + ' baris';
            if (pageText) pageText.textContent = 'Halaman ' + currentPage + ' dari ' + totalPages;
            if (btnPrev) { btnPrev.disabled = currentPage <= 1; }
            if (btnNext) { btnNext.disabled = currentPage >= totalPages; }
         }

         function loadData() {
            var from = (dateFrom && dateFrom.value) || '';
            var to = (dateTo && dateTo.value) || '';
            if (!from || !to) {
               fullData = [];
               tbody.innerHTML = '<tr><td colspan="10" class="px-5 py-8 text-center text-slate-500 text-sm">Pilih Tanggal Dari dan Tanggal Sampai.</td></tr>';
               infoEl.textContent = '—';
               if (pageText) pageText.textContent = 'Halaman 1';
               if (btnPrev) btnPrev.disabled = true;
               if (btnNext) btnNext.disabled = true;
               return;
            }
            tbody.innerHTML = '<tr><td colspan="10" class="px-5 py-8 text-center text-slate-500 text-sm"><span class="inline-block animate-pulse">Memuat data...</span></td></tr>';
            infoEl.textContent = '—';
            if (linkPerhari) linkPerhari.href = "{{ url()->route('fueling-evaluasi.per-hari') }}?date_from=" + encodeURIComponent(from) + "&date_to=" + encodeURIComponent(to);
            loadStats(from, to);

            fetch(apiUrl + '?date_from=' + encodeURIComponent(from) + '&date_to=' + encodeURIComponent(to), { credentials: 'same-origin' })
               .then(function(res) { return res.json(); })
               .then(function(json) {
                  fullData = json.data || [];
                  currentPage = 1;
                  applyFilterAndRender();
               })
               .catch(function() {
                  fullData = [];
                  tbody.innerHTML = '<tr><td colspan="10" class="px-5 py-8 text-center text-danger text-sm">Gagal memuat data.</td></tr>';
                  infoEl.textContent = '—';
               });
         }

         setDefaultDates();
         if (btnLoad) btnLoad.addEventListener('click', loadData);
         if (searchInput) searchInput.addEventListener('input', function() { currentPage = 1; applyFilterAndRender(); });
         if (btnPrev) btnPrev.addEventListener('click', function() { currentPage--; applyFilterAndRender(); });
         if (btnNext) btnNext.addEventListener('click', function() { currentPage++; applyFilterAndRender(); });
         loadData();
      })();
      </script>
   </body>
</html>