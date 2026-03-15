<!DOCTYPE html>
<html class="light" lang="en">
   <head>
      <meta charset="utf-8"/>
      <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
      <title>Fleet Operations Compliance Dashboard</title>
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
                     <h1 class="text-lg font-bold leading-tight">Fleet Operations Compliance</h1>
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
            <!-- Card 1 -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
               <div class="flex justify-between items-start mb-3">
                  <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Active Units</p>
                  <div class="bg-primary/10 p-2 rounded-lg text-primary">
                     <span class="material-symbols-outlined text-xl">commute</span>
                  </div>
               </div>
               <div class="flex items-end gap-3">
                  <h3 class="text-3xl font-bold">142</h3>
                  <div class="flex items-center text-success text-sm font-bold mb-1">
                     <span class="material-symbols-outlined text-base">arrow_upward</span>
                     <span>5.2%</span>
                  </div>
               </div>
               <p class="text-xs text-slate-400 mt-2">vs previous month (135 units)</p>
            </div>
            <!-- Card 2 -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
               <div class="flex justify-between items-start mb-3">
                  <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Compliance Rate</p>
                  <div class="bg-success/10 p-2 rounded-lg text-success">
                     <span class="material-symbols-outlined text-xl">verified</span>
                  </div>
               </div>
               <div class="flex items-center justify-between">
                  <h3 class="text-3xl font-bold">88%</h3>
                  <div class="relative w-12 h-12">
                     <svg class="w-full h-full transform -rotate-90" viewbox="0 0 36 36">
                        <path class="text-slate-100 dark:text-slate-800" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4"></path>
                        <path class="text-success" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="88, 100" stroke-linecap="round" stroke-width="4"></path>
                     </svg>
                  </div>
               </div>
               <p class="text-xs text-danger font-medium mt-2">-2.1% from last week</p>
            </div>
            <!-- Card 3 -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
               <div class="flex justify-between items-start mb-3">
                  <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Avg Duration / Unit</p>
                  <div class="bg-warning/10 p-2 rounded-lg text-warning">
                     <span class="material-symbols-outlined text-xl">timer</span>
                  </div>
               </div>
               <h3 class="text-3xl font-bold">12.4 <span class="text-sm font-normal text-slate-500">jam</span></h3>
               <div class="mt-4 h-8 w-full flex items-end gap-1">
                  <div class="bg-primary/30 w-full h-1/2 rounded-sm"></div>
                  <div class="bg-primary/30 w-full h-3/4 rounded-sm"></div>
                  <div class="bg-primary/30 w-full h-1/2 rounded-sm"></div>
                  <div class="bg-primary w-full h-full rounded-sm"></div>
                  <div class="bg-primary/30 w-full h-2/3 rounded-sm"></div>
                  <div class="bg-primary/30 w-full h-1/2 rounded-sm"></div>
                  <div class="bg-primary/30 w-full h-3/4 rounded-sm"></div>
               </div>
            </div>
            <!-- Card 4 -->
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
               <h3 class="text-3xl font-bold text-slate-300">--.-</h3>
               <p class="text-xs text-slate-400 mt-2 italic">Awaiting sensor integration</p>
            </div>
         </div>
         <!-- Main Content Area -->
         <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Unit Performance Table (60%) -->
            <div class="lg:col-span-3 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col">
               <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                  <h3 class="font-bold">Unit Performance Details</h3>
                  <div class="flex items-center gap-3">
                     <label class="flex items-center gap-2 cursor-pointer group">
                     <input class="rounded text-primary focus:ring-primary h-4 w-4 bg-slate-100 dark:bg-slate-800 border-none" type="checkbox"/>
                     <span class="text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">Non-compliant only</span>
                     </label>
                     <button class="text-slate-400 hover:text-primary transition-colors">
                     <span class="material-symbols-outlined">filter_list</span>
                     </button>
                  </div>
               </div>
               <div class="overflow-x-auto">
                  <table class="w-full text-left border-collapse">
                     <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                           <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase">Unit No</th>
                           <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase">Company</th>
                           <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase">Distance</th>
                           <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase">Permit Status</th>
                           <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase">Expiry</th>
                           <th class="px-5 py-3 text-[11px] font-bold text-slate-500 uppercase">MTD</th>
                        </tr>
                     </thead>
                     <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr>
                           <td class="px-5 py-4 font-bold text-primary">HD785-001</td>
                           <td class="px-5 py-4 text-sm">PAMA</td>
                           <td class="px-5 py-4 text-sm font-medium">42.5 km</td>
                           <td class="px-5 py-4">
                              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-success/10 text-success border border-success/20">
                              <span class="material-symbols-outlined text-xs">check_circle</span> PASSED
                              </span>
                           </td>
                           <td class="px-5 py-4 text-sm">12 Dec 2024</td>
                           <td class="px-5 py-4 text-sm font-bold text-success">98.2%</td>
                        </tr>
                        <tr>
                           <td class="px-5 py-4 font-bold text-primary">DT20-112</td>
                           <td class="px-5 py-4 text-sm">SIS</td>
                           <td class="px-5 py-4 text-sm font-medium">38.2 km</td>
                           <td class="px-5 py-4">
                              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-warning/10 text-warning border border-warning/20">
                              <span class="material-symbols-outlined text-xs">warning</span> EXPIRING
                              </span>
                           </td>
                           <td class="px-5 py-4 text-sm text-warning font-semibold italic">Oct 30, 2023</td>
                           <td class="px-5 py-4 text-sm font-bold text-success">92.5%</td>
                        </tr>
                        <tr>
                           <td class="px-5 py-4 font-bold text-primary">GD511-05</td>
                           <td class="px-5 py-4 text-sm">BUMA</td>
                           <td class="px-5 py-4 text-sm font-medium">12.1 km</td>
                           <td class="px-5 py-4">
                              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-danger/10 text-danger border border-danger/20">
                              <span class="material-symbols-outlined text-xs">cancel</span> NOT PASSED
                              </span>
                           </td>
                           <td class="px-5 py-4 text-sm text-danger font-bold">EXPIRED</td>
                           <td class="px-5 py-4 text-sm font-bold text-danger">74.1%</td>
                        </tr>
                        <tr>
                           <td class="px-5 py-4 font-bold text-primary">HD785-042</td>
                           <td class="px-5 py-4 text-sm">PAMA</td>
                           <td class="px-5 py-4 text-sm font-medium">45.8 km</td>
                           <td class="px-5 py-4">
                              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-success/10 text-success border border-success/20">
                              <span class="material-symbols-outlined text-xs">check_circle</span> PASSED
                              </span>
                           </td>
                           <td class="px-5 py-4 text-sm">05 Jan 2025</td>
                           <td class="px-5 py-4 text-sm font-bold text-success">96.8%</td>
                        </tr>
                        <tr>
                           <td class="px-5 py-4 font-bold text-primary">PC200-8</td>
                           <td class="px-5 py-4 text-sm">BUMA</td>
                           <td class="px-5 py-4 text-sm font-medium">18.4 km</td>
                           <td class="px-5 py-4">
                              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-success/10 text-success border border-success/20">
                              <span class="material-symbols-outlined text-xs">check_circle</span> PASSED
                              </span>
                           </td>
                           <td class="px-5 py-4 text-sm">18 Feb 2024</td>
                           <td class="px-5 py-4 text-sm font-bold text-success">89.4%</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="mt-auto p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                  <p class="text-xs text-slate-500">Showing 1-5 of 142 units</p>
                  <div class="flex gap-2">
                     <button class="px-3 py-1 text-xs border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-50">Prev</button>
                     <button class="px-3 py-1 text-xs bg-primary text-white rounded-lg">Next</button>
                  </div>
               </div>
            </div>
            <!-- Compliance Overview (40%) -->
            <div class="lg:col-span-2 space-y-6">
               <!-- Donut Chart -->
               <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                  <h3 class="font-bold mb-6">Compliance Status Distribution</h3>
                  <div class="flex items-center justify-center relative mb-6">
                     <!-- Abstract Donut Shape using Gradients -->
                     <div class="w-40 h-40 rounded-full border-[12px] border-success relative flex items-center justify-center">
                        <div class="absolute inset-[-12px] rounded-full border-[12px] border-warning border-t-transparent border-r-transparent border-b-transparent rotate-[30deg]"></div>
                        <div class="absolute inset-[-12px] rounded-full border-[12px] border-danger border-t-transparent border-l-transparent border-b-transparent rotate-[-15deg]"></div>
                        <div class="text-center">
                           <p class="text-3xl font-bold">142</p>
                           <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Units</p>
                        </div>
                     </div>
                  </div>
                  <div class="grid grid-cols-3 gap-2">
                     <div class="text-center p-2 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-[10px] font-bold text-success uppercase">Passed</p>
                        <p class="text-lg font-bold">125</p>
                     </div>
                     <div class="text-center p-2 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-[10px] font-bold text-warning uppercase">Expiring</p>
                        <p class="text-lg font-bold">12</p>
                     </div>
                     <div class="text-center p-2 rounded-lg bg-slate-50 dark:bg-slate-800/50">
                        <p class="text-[10px] font-bold text-danger uppercase">Not Passed</p>
                        <p class="text-lg font-bold">5</p>
                     </div>
                  </div>
               </div>
               <!-- Site Distribution Bar Chart -->
               <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                  <h3 class="font-bold mb-4">Site Performance Ranking</h3>
                  <div class="space-y-4">
                     <div class="space-y-1">
                        <div class="flex justify-between text-xs font-semibold">
                           <span>BMO 1</span>
                           <span>94%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
                           <div class="bg-primary h-2 rounded-full w-[94%]"></div>
                        </div>
                     </div>
                     <div class="space-y-1">
                        <div class="flex justify-between text-xs font-semibold">
                           <span>LMO</span>
                           <span>82%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
                           <div class="bg-primary h-2 rounded-full w-[82%]"></div>
                        </div>
                     </div>
                     <div class="space-y-1">
                        <div class="flex justify-between text-xs font-semibold">
                           <span>GMO</span>
                           <span>78%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
                           <div class="bg-primary h-2 rounded-full w-[78%]"></div>
                        </div>
                     </div>
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
   </body>
</html>