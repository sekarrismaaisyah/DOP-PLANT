<!DOCTYPE html>
<html class="light" lang="en">
   <head>
      <meta charset="utf-8"/>
      <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
      <title>Detailed Pre-Inspection Intelligence Hub</title>
      <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
      <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
      <script id="tailwind-config">
         tailwind.config = {
             darkMode: "class",
             theme: {
                 extend: {
                     colors: {
                         "primary": "#0df259",
                         "sage-green": "#8BA88E",
                         "muted-coral": "#E57373",
                         "soft-amber": "#FFB74D",
                         "background-light": "#f9fafb",
                         "background-dark": "#0f172a",
                     },
                     fontFamily: {
                         "display": ["Inter"]
                     },
                     borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                 },
             },
         }
      </script>
      <style type="text/tailwindcss">
         @layer base {
         body { @apply bg-background-light font-display text-slate-900; }
         }
         .thin-divider { @apply border-t border-slate-100 dark:border-slate-800; }
         .data-label { @apply text-[10px] font-bold uppercase tracking-wider text-slate-400; }
         .data-value { @apply text-sm font-semibold text-slate-700; }
         .stat-card { @apply bg-white rounded-lg border border-slate-200 p-4 shadow-sm flex items-center gap-4 transition-all hover:shadow-md; }
      </style>
   </head>
   <body class="bg-background-light dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen">
      <div class="relative flex min-h-screen w-full flex-col overflow-x-hidden">
         <header class="sticky top-0 z-50 w-full border-b border-slate-200 bg-white/90 backdrop-blur-md dark:border-slate-800 dark:bg-slate-900/90 px-6 lg:px-12 py-3">
            <div class="mx-auto flex max-w-[1600px] items-center justify-between">
               <div class="flex items-center gap-10">
                  <div class="flex items-center gap-2.5">
                     <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <span class="material-symbols-outlined text-2xl font-bold">analytics</span>
                     </div>
                     <h2 class="text-lg font-bold tracking-tight">Intelligence Hub</h2>
                  </div>
                  <nav class="hidden md:flex items-center gap-8">
                     <a class="text-xs font-bold text-emerald-600" href="#">Operations</a>
                     <a class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors" href="#">Security</a>
                     <a class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors" href="#">Permits</a>
                     <a class="text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors" href="#">Analytics</a>
                  </nav>
               </div>
               <div class="flex items-center gap-4">
                  <div class="relative hidden sm:block">
                     <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                     <input class="h-9 w-64 rounded border-slate-200 bg-slate-50 pl-9 text-xs focus:border-emerald-500 focus:ring-emerald-500" placeholder="Quick find ID/Permit..." type="text"/>
                  </div>
                  <div class="h-9 w-9 rounded-full bg-slate-200 overflow-hidden border border-slate-200">
                     <img alt="User" class="h-full w-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCaiC29KzHvXxP2HsCwtyo0T81G-NydBpFFXHqPStut62Dn268gr3aAfAtbHfV2r_SOs_eR5MpdjVvVGdw2yYbhxQQq2hh5q-oYm5turip7dIkoDgvjjWTnXW5ZDhCEgegTnUYAvJzVOw7HRnjfkvH0QjGx8X2dZoDnHpVT4rhBbpr8fjs2LMPY6_jmtzEc9ONUnkPhhf9Zq248NEcgl6Ukyo4Vwjf7J8WqFxV3eblQip-Suu-qF0g8IZZKAvIeUoYVUI6WBibW2W9W"/>
                  </div>
               </div>
            </div>
         </header>
         <main class="mx-auto flex w-full max-w-[1600px] flex-col gap-8 px-6 lg:px-12 py-8 pb-32">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
               <div>
                  <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">Detailed Pre-Inspection Intelligence Hub</h1>
                  <p class="text-sm text-slate-500 mt-1">Industrial Operational Overview • Last sync: <span class="font-mono">14:22:05</span></p>
               </div>
               <div class="flex gap-2">
                  <button class="flex h-10 items-center justify-center rounded border border-slate-200 bg-white px-4 text-xs font-bold hover:bg-slate-50">
                  <span class="material-symbols-outlined mr-2 text-lg">settings_input_component</span> System Config
                  </button>
                  <button class="flex h-10 items-center justify-center rounded bg-emerald-600 px-6 text-xs font-bold text-white hover:bg-emerald-700 shadow-sm transition-all">
                  Generate Shift Report
                  </button>
               </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
               <div class="stat-card">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-600">
                     <span class="material-symbols-outlined">groups</span>
                  </div>
                  <div>
                     <p class="data-label">Total Active Workers</p>
                     <p class="text-xl font-black text-slate-800">1,284</p>
                  </div>
               </div>
               <div class="stat-card">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-50 text-muted-coral">
                     <span class="material-symbols-outlined">warning</span>
                  </div>
                  <div>
                     <p class="data-label">Open Hazards</p>
                     <p class="text-xl font-black text-muted-coral">12</p>
                  </div>
               </div>
               <div class="stat-card">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-sage-green">
                     <span class="material-symbols-outlined">assignment_turned_in</span>
                  </div>
                  <div>
                     <p class="data-label">Active Special Permits</p>
                     <p class="text-xl font-black text-sage-green">08</p>
                  </div>
               </div>
               <div class="stat-card">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-50 text-soft-amber">
                     <span class="material-symbols-outlined">emergency</span>
                  </div>
                  <div>
                     <p class="data-label">Incidents MTD</p>
                     <p class="text-xl font-black text-slate-800">03</p>
                  </div>
               </div>
               <div class="stat-card">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                     <span class="material-symbols-outlined">timer</span>
                  </div>
                  <div>
                     <p class="data-label">Safe Man-Hours</p>
                     <p class="text-xl font-black text-slate-800">42,500</p>
                  </div>
               </div>
            </div>
            <div class="grid grid-cols-12 gap-6">
               <div class="col-span-12 lg:col-span-4 flex flex-col gap-6">
                  <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">
                     <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-4 py-3">
                        <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest">CCTV Monitoring Status</h3>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">8 Active / 1 Offline</span>
                     </div>
                     <div class="divide-y divide-slate-100">
                        <div class="flex items-center gap-3 p-3 hover:bg-slate-50 transition-colors">
                           <div class="relative h-12 w-20 overflow-hidden rounded bg-slate-200">
                              <img alt="CCTV Snapshot" class="w-full h-full object-cover opacity-80" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCyR_CiOEJphtz0dO0Uqv0J6qYKUNdcqx3g4fMhwQnN4JXq-WefE5kK5DBKpvZ89ytyDLqmtcOfcDbTVr47Pk4MbmBJtbFmEC7RpW3NIqPkZZ4CmUngXmhQy7sGQkj1G_isqc4_6eckuwvj8UGCNdhEcA6iR-wlEbv7uJCrBEYcoAV3rVVMR1NwVcWAg9zrbi9w93EP1eIjT-MJOgEsC55_Y0cGv8IT8s_T3Ray_rx2JoYQW6ZexReSob5Xsj9HZSlnOj1VmZmu_a8t"/>
                              <div class="absolute inset-0 bg-black/10"></div>
                           </div>
                           <div class="flex-1 min-w-0">
                              <div class="flex items-center gap-2">
                                 <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                                 <span class="text-sm font-bold truncate">Gate 1: Primary Entrance</span>
                              </div>
                              <p class="text-[10px] text-slate-400 font-medium">HB: 2s ago • 192.168.1.101</p>
                           </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 hover:bg-slate-50 transition-colors">
                           <div class="relative h-12 w-20 overflow-hidden rounded bg-slate-200">
                              <img alt="CCTV Snapshot" class="w-full h-full object-cover opacity-80" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA4pTYDGLYLmCxb31Znb7FJA2fJ9QgEYT_4YN-2LqVvbeG1aWMx4qh6rFkpHhyZbBDCr643A14RPzMleT-_lwy5b5S83yIvKaCNusumG_xNQHOms5PldLU7tnofH2MtOc21zvA_evvNMf38Zj5Nsusuj8fGvGeeTTOf5l_sAINscFg-BwEFrMAopNS79uDXXMH0NwcSQjoyOt1FIwpQuUQOgiufVG_P9ITE09zuTL7hgWCAo2zW-h4dcbMeMXqwt-nwZOA1m5i8f8jN"/>
                              <div class="absolute inset-0 bg-black/10"></div>
                           </div>
                           <div class="flex-1 min-w-0">
                              <div class="flex items-center gap-2">
                                 <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                                 <span class="text-sm font-bold truncate">North Wing: Corridor A</span>
                              </div>
                              <p class="text-[10px] text-slate-400 font-medium">HB: 5s ago • 192.168.1.105</p>
                           </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 hover:bg-slate-50 transition-colors">
                           <div class="relative h-12 w-20 overflow-hidden rounded bg-slate-200">
                              <div class="h-full w-full bg-slate-800 flex items-center justify-center text-slate-500">
                                 <span class="material-symbols-outlined">videocam_off</span>
                              </div>
                           </div>
                           <div class="flex-1 min-w-0">
                              <div class="flex items-center gap-2">
                                 <div class="h-2 w-2 rounded-full bg-red-500"></div>
                                 <span class="text-sm font-bold truncate">Storage B: Chemicals</span>
                              </div>
                              <p class="text-[10px] text-red-400 font-bold uppercase tracking-tight">Signal Lost - 14:05</p>
                           </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 hover:bg-slate-50 transition-colors">
                           <div class="relative h-12 w-20 overflow-hidden rounded bg-slate-200">
                              <img alt="CCTV Snapshot" class="w-full h-full object-cover opacity-80" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB2VB9dnp2obhE99qqcxoaZh26gqv-AmdyhL_fUx18wEP4zIpzp8d8-hfXxMkGcq8CZp-r38MO6MAlFIzJsB2Jp_d2K_cfHbuttVaDSFKJuCD9To211WZi9JNXLrjL5N6PtehoLVxsmqpAMh_bosx4gtstbdrG03euh6DSd1gN8iU10PZgl-HVbj6BdRVNCE0A8Ed7VntJ6YrS5Vf7DaXVivMyOJzL0oSTJobbW1EXu4ze5ohh7Z1_x2VNsiKqghIYOoG-Vn-7n4WpP"/>
                           </div>
                           <div class="flex-1 min-w-0">
                              <div class="flex items-center gap-2">
                                 <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                                 <span class="text-sm font-bold truncate">Loading Dock 4</span>
                              </div>
                              <p class="text-[10px] text-slate-400 font-medium">HB: 1s ago • 192.168.1.112</p>
                           </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 hover:bg-slate-50 transition-colors">
                           <div class="relative h-12 w-20 overflow-hidden rounded bg-slate-200">
                              <img alt="CCTV Snapshot" class="w-full h-full object-cover opacity-80" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCyR_CiOEJphtz0dO0Uqv0J6qYKUNdcqx3g4fMhwQnN4JXq-WefE5kK5DBKpvZ89ytyDLqmtcOfcDbTVr47Pk4MbmBJtbFmEC7RpW3NIqPkZZ4CmUngXmhQy7sGQkj1G_isqc4_6eckuwvj8UGCNdhEcA6iR-wlEbv7uJCrBEYcoAV3rVVMR1NwVcWAg9zrbi9w93EP1eIjT-MJOgEsC55_Y0cGv8IT8s_T3Ray_rx2JoYQW6ZexReSob5Xsj9HZSlnOj1VmZmu_a8t"/>
                           </div>
                           <div class="flex-1 min-w-0">
                              <div class="flex items-center gap-2">
                                 <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                                 <span class="text-sm font-bold truncate">Assembly Line 02</span>
                              </div>
                              <p class="text-[10px] text-slate-400 font-medium">HB: 12s ago • 192.168.1.109</p>
                           </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 hover:bg-slate-50 transition-colors">
                           <div class="relative h-12 w-20 overflow-hidden rounded bg-slate-200">
                              <img alt="CCTV Snapshot" class="w-full h-full object-cover opacity-80" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA4pTYDGLYLmCxb31Znb7FJA2fJ9QgEYT_4YN-2LqVvbeG1aWMx4qh6rFkpHhyZbBDCr643A14RPzMleT-_lwy5b5S83yIvKaCNusumG_xNQHOms5PldLU7tnofH2MtOc21zvA_evvNMf38Zj5Nsusuj8fGvGeeTTOf5l_sAINscFg-BwEFrMAopNS79uDXXMH0NwcSQjoyOt1FIwpQuUQOgiufVG_P9ITE09zuTL7hgWCAo2zW-h4dcbMeMXqwt-nwZOA1m5i8f8jN"/>
                           </div>
                           <div class="flex-1 min-w-0">
                              <div class="flex items-center gap-2">
                                 <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                                 <span class="text-sm font-bold truncate">Admin Reception</span>
                              </div>
                              <p class="text-[10px] text-slate-400 font-medium">HB: 8s ago • 192.168.1.121</p>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
                     <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-4 py-3">
                        <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest">Incident Summary</h3>
                        <span class="text-[10px] font-bold text-slate-400 tracking-tighter uppercase">MTD Metrics</span>
                     </div>
                     <div class="p-4">
                        <div class="grid grid-cols-2 gap-4 mb-6">
                           <div class="rounded border border-slate-100 bg-slate-50 p-3 text-center">
                              <p class="data-label">Total MTD</p>
                              <p class="text-2xl font-black text-slate-800">03</p>
                           </div>
                           <div class="rounded border border-slate-100 bg-slate-50 p-3 text-center">
                              <p class="data-label">Last Event</p>
                              <p class="text-sm font-black text-slate-800">Oct 24</p>
                           </div>
                        </div>
                        <div class="space-y-3">
                           <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b pb-1">Recent Logs</p>
                           <div class="flex items-center justify-between">
                              <div class="min-w-0">
                                 <p class="text-xs font-bold truncate">Minor Slip in Zone C</p>
                                 <p class="text-[10px] text-slate-500">Oct 24 • Area: Warehouse</p>
                              </div>
                              <span class="rounded bg-orange-50 px-2 py-0.5 text-[10px] font-bold text-orange-600 border border-orange-100">Investigating</span>
                           </div>
                           <div class="flex items-center justify-between">
                              <div class="min-w-0">
                                 <p class="text-xs font-bold truncate">Spill (Non-Toxic) L-4</p>
                                 <p class="text-[10px] text-slate-500">Oct 19 • Area: Loading</p>
                              </div>
                              <span class="rounded bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-600 border border-emerald-100">Resolved</span>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-span-12 lg:col-span-8 flex flex-col gap-6">
                  <div class="rounded-lg border border-slate-200 bg-white shadow-sm overflow-hidden">
                     <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-5 py-3">
                        <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest">Hazard Management Tracker</h3>
                        <div class="flex gap-4">
                           <div class="flex items-center gap-1.5">
                              <span class="h-2 w-2 rounded-full bg-muted-coral"></span>
                              <span class="text-[10px] font-bold text-slate-500">Unclosed Critical</span>
                           </div>
                           <div class="flex items-center gap-1.5">
                              <span class="h-2 w-2 rounded-full bg-soft-amber"></span>
                              <span class="text-[10px] font-bold text-slate-500">To Be Concern</span>
                           </div>
                        </div>
                     </div>
                     <div class="overflow-x-auto">
                        <table class="w-full text-left">
                           <thead class="bg-slate-50/50 text-[10px] uppercase tracking-wider text-slate-500">
                              <tr>
                                 <th class="px-5 py-3 font-bold">ID / Badge</th>
                                 <th class="px-5 py-3 font-bold">Category</th>
                                 <th class="px-5 py-3 font-bold">Description</th>
                                 <th class="px-5 py-3 font-bold">Days Open</th>
                                 <th class="px-5 py-3 font-bold">PIC Name</th>
                                 <th class="px-5 py-3 font-bold text-right">Action</th>
                              </tr>
                           </thead>
                           <tbody class="divide-y divide-slate-100 text-sm">
                              <tr class="hover:bg-slate-50 transition-colors">
                                 <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                       <span class="rounded-sm bg-muted-coral px-1.5 py-0.5 text-[10px] font-black text-white">CRIT</span>
                                       <span class="font-mono text-xs font-bold">#HZ-442</span>
                                    </div>
                                 </td>
                                 <td class="px-5 py-4 font-medium text-slate-600">Electrical</td>
                                 <td class="px-5 py-4 text-xs font-medium text-slate-900">Exposed wiring near water outlet L-04</td>
                                 <td class="px-5 py-4 font-mono font-bold text-red-600">12d</td>
                                 <td class="px-5 py-4 text-xs">Aris Sulaiman</td>
                                 <td class="px-5 py-4 text-right">
                                    <button class="text-[10px] font-black uppercase text-emerald-600 hover:underline">Close</button>
                                 </td>
                              </tr>
                              <tr class="hover:bg-slate-50 transition-colors">
                                 <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                       <span class="rounded-sm bg-soft-amber px-1.5 py-0.5 text-[10px] font-black text-white">CONC</span>
                                       <span class="font-mono text-xs font-bold">#HZ-445</span>
                                    </div>
                                 </td>
                                 <td class="px-5 py-4 font-medium text-slate-600">Ergonomics</td>
                                 <td class="px-5 py-4 text-xs font-medium text-slate-900">Improper lifting technique observed at Hub B</td>
                                 <td class="px-5 py-4 font-mono font-bold text-orange-400">03d</td>
                                 <td class="px-5 py-4 text-xs">Sarah Chen</td>
                                 <td class="px-5 py-4 text-right">
                                    <button class="text-[10px] font-black uppercase text-emerald-600 hover:underline">Escalate</button>
                                 </td>
                              </tr>
                              <tr class="hover:bg-slate-50 transition-colors">
                                 <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                       <span class="rounded-sm bg-muted-coral px-1.5 py-0.5 text-[10px] font-black text-white">CRIT</span>
                                       <span class="font-mono text-xs font-bold">#HZ-448</span>
                                    </div>
                                 </td>
                                 <td class="px-5 py-4 font-medium text-slate-600">Structural</td>
                                 <td class="px-5 py-4 text-xs font-medium text-slate-900">Cracked support beam in North Storage</td>
                                 <td class="px-5 py-4 font-mono font-bold text-red-600">01d</td>
                                 <td class="px-5 py-4 text-xs">Mike Donahue</td>
                                 <td class="px-5 py-4 text-right">
                                    <button class="text-[10px] font-black uppercase text-emerald-600 hover:underline">Close</button>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>
                  <div class="flex flex-col gap-4">
                     <h3 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest pl-1">Active Special Permits (IPK/OKK)</h3>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-lg border-l-4 border-l-emerald-500 border border-slate-200 bg-white p-4 shadow-sm relative overflow-hidden">
                           <div class="flex justify-between items-start mb-4">
                              <div>
                                 <div class="flex items-center gap-2">
                                    <span class="rounded bg-emerald-50 px-1.5 py-0.5 text-[10px] font-black text-emerald-700 border border-emerald-100">IPK</span>
                                    <span class="font-mono text-xs font-bold text-slate-500 tracking-tight">#PR-8821990</span>
                                 </div>
                                 <h4 class="mt-2 text-sm font-extrabold text-slate-900 uppercase">Hot Work: Welding B-7</h4>
                              </div>
                              <div class="text-right">
                                 <p class="text-[10px] font-bold text-slate-400 uppercase">Time Remaining</p>
                                 <p class="font-mono text-lg font-black text-emerald-600">02:44:12</p>
                              </div>
                           </div>
                           <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                              <div>
                                 <p class="data-label">Scope</p>
                                 <p class="data-value truncate">Pipe Welding</p>
                              </div>
                              <div>
                                 <p class="data-label">Supervisor</p>
                                 <p class="data-value truncate">David K. (Safety)</p>
                              </div>
                           </div>
                        </div>
                        <div class="rounded-lg border-l-4 border-l-soft-amber border border-slate-200 bg-white p-4 shadow-sm relative overflow-hidden">
                           <div class="flex justify-between items-start mb-4">
                              <div>
                                 <div class="flex items-center gap-2">
                                    <span class="rounded bg-orange-50 px-1.5 py-0.5 text-[10px] font-black text-orange-700 border border-orange-100">OKK</span>
                                    <span class="font-mono text-xs font-bold text-slate-500 tracking-tight">#PR-8822004</span>
                                 </div>
                                 <h4 class="mt-2 text-sm font-extrabold text-slate-900 uppercase">Confined Space: Tank 4</h4>
                              </div>
                              <div class="text-right">
                                 <p class="text-[10px] font-bold text-slate-400 uppercase">Time Remaining</p>
                                 <p class="font-mono text-lg font-black text-orange-500">00:12:44</p>
                              </div>
                           </div>
                           <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                              <div>
                                 <p class="data-label">Scope</p>
                                 <p class="data-value truncate">Internal Inspection</p>
                              </div>
                              <div>
                                 <p class="data-label">Supervisor</p>
                                 <p class="data-value truncate">Rina M. (HSE)</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                     <div class="flex items-center justify-between mb-4">
                        <h4 class="text-xs font-extrabold text-slate-700 uppercase tracking-widest">Zone Compliance Visualization</h4>
                        <span class="text-[10px] font-bold text-slate-400">Floor 01 Schematic</span>
                     </div>
                     <div class="aspect-[21/9] w-full rounded bg-slate-50 relative overflow-hidden border border-slate-100">
                        <img alt="Site Map" class="w-full h-full object-cover opacity-40 grayscale" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB2VB9dnp2obhE99qqcxoaZh26gqv-AmdyhL_fUx18wEP4zIpzp8d8-hfXxMkGcq8CZp-r38MO6MAlFIzJsB2Jp_d2K_cfHbuttVaDSFKJuCD9To211WZi9JNXLrjL5N6PtehoLVxsmqpAMh_bosx4gtstbdrG03euh6DSd1gN8iU10PZgl-HVbj6BdRVNCE0A8Ed7VntJ6YrS5Vf7DaXVivMyOJzL0oSTJobbW1EXu4ze5ohh7Z1_x2VNsiKqghIYOoG-Vn-7n4WpP"/>
                        <div class="absolute top-1/2 left-1/4 h-4 w-4">
                           <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-muted-coral opacity-75"></span>
                           <span class="relative inline-flex rounded-full h-4 w-4 bg-muted-coral"></span>
                        </div>
                        <div class="absolute bottom-1/4 right-1/3 h-4 w-4">
                           <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-soft-amber opacity-75"></span>
                           <span class="relative inline-flex rounded-full h-4 w-4 bg-soft-amber"></span>
                        </div>
                        <div class="absolute top-1/4 right-1/4 h-3 w-3 rounded-full bg-emerald-500"></div>
                     </div>
                  </div>
               </div>
            </div>
         </main>
         <footer class="fixed bottom-0 z-40 w-full border-t border-slate-200 bg-white/95 backdrop-blur-sm py-4 px-6 lg:px-12 dark:border-slate-800 dark:bg-slate-900/95 shadow-[0_-8px_30px_rgb(0,0,0,0.04)]">
            <div class="mx-auto flex max-w-[1600px] items-center justify-between">
               <div class="flex items-center gap-8">
                  <div class="flex items-center gap-2">
                     <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                     <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">System Nominal</span>
                  </div>
                  <div class="hidden sm:flex gap-4">
                     <div class="flex flex-col">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Hazards MTD</span>
                        <span class="text-xs font-black text-slate-700">124 Detected</span>
                     </div>
                     <div class="flex flex-col">
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Resolution Rate</span>
                        <span class="text-xs font-black text-emerald-600">92.4%</span>
                     </div>
                  </div>
               </div>
               <div class="flex items-center gap-3">
                  <span class="text-[10px] font-bold text-slate-400 uppercase mr-2">Ready for Next Inspection Cycle</span>
                  <button class="inline-flex h-10 items-center justify-center rounded bg-slate-900 px-8 text-xs font-black text-white hover:bg-slate-800 transition-all shadow-md">
                  INITIATE FULL SITE AUDIT
                  </button>
               </div>
            </div>
         </footer>
      </div>
   </body>
</html>