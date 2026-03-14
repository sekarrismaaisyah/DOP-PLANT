<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8"/>
      <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
      <title>Dashboard Coverage Area PT Berau Coal</title>
      <!-- Tailwind CSS v3 CDN -->
      <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
      <style data-purpose="custom-scrollbar">
         ::-webkit-scrollbar {
         width: 6px;
         height: 6px;
         }
         ::-webkit-scrollbar-track {
         background: #f1f1f1;
         }
         ::-webkit-scrollbar-thumb {
         background: #888;
         border-radius: 3px;
         }
         ::-webkit-scrollbar-thumb:hover {
         background: #555;
         }
      </style>
      <style data-purpose="chart-placeholders">
         .bar-segment {
         transition: height 0.3s ease;
         }
         .status-green { background-color: #15803d; color: white; }
         .status-light-green { background-color: #86efac; color: #166534; }
         .status-yellow { background-color: #fde047; color: #854d0e; }
         .status-red { background-color: #dc2626; color: white; }
      </style>
   </head>
   <body class="bg-gray-100 font-sans text-gray-800">
      <!-- BEGIN: MainHeader -->
      <header class="bg-[#C1E001] px-4 py-2 flex items-center justify-between shadow-sm border-b border-gray-300">
         <div class="flex items-center gap-4">
            <!-- Logo Placeholder -->
            <div class="bg-white p-1 rounded">
               <img alt="Berau Coal Logo" class="h-10 object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAgL9Awc9FLqroFiL3A17Ob7wgBvQlFUc3HSYJXlrX8bxUAE9RrAhcGLMU_G6dQJ8hDjZlhqAkissBQkVjzFx3rYeB4IE3WpnERN4Ky-nWdhjSItz0RmqtUuIiuWxKW4mFIHcxmm-smzIyscrOZ73fxqk87fIA0YY93shm2GBaAVzz-j5ZniolP1n1ys8eioBgJ0gYnpt6-ZRJNWCjjxr472fQswJwNkbtSDfBfug_q6Kgqb1jXNQEj6uHza9DwqA9UlYeUH18vTwc8"/>
            </div>
            <h1 class="text-xl font-bold uppercase tracking-tight text-gray-800">Dashboard Coverage Area PT Berau Coal</h1>
         </div>
         <div class="flex items-center gap-2 text-xs font-semibold text-gray-600">
            <span>Edit</span>
            <span class="border-l border-gray-400 h-4 mx-2"></span>
            <span class="flex items-center gap-1"><i class="w-2 h-2 bg-blue-500 rounded-full"></i> View: Original</span>
         </div>
      </header>
      <!-- END: MainHeader -->
      <!-- BEGIN: FilterSection -->
      <section class="bg-white border-b border-gray-200 px-4 py-3 flex flex-wrap items-end gap-4 text-xs font-bold text-gray-500 uppercase">
         <span class="mb-2">Filter -&gt;</span>
         <div class="flex flex-col gap-1">
            <label for="last-4-week">Last 4 Week</label>
            <select class="text-xs border-gray-300 rounded p-1 w-32 focus:ring-lime-500 focus:border-lime-500" id="last-4-week">
               <option>(Multiple values)</option>
            </select>
         </div>
         <div class="flex flex-col gap-1">
            <label for="week">Week</label>
            <select class="text-xs border-gray-300 rounded p-1 w-24 focus:ring-lime-500 focus:border-lime-500" id="week">
               <option>W11</option>
            </select>
         </div>
         <div class="flex flex-col gap-1">
            <label for="site">Site</label>
            <select class="text-xs border-gray-300 rounded p-1 w-32 focus:ring-lime-500 focus:border-lime-500" id="site">
               <option>(All)</option>
            </select>
         </div>
         <div class="flex flex-col gap-1">
            <label for="pembagian-area">Pembagian Area</label>
            <select class="text-xs border-gray-300 rounded p-1 w-32 focus:ring-lime-500 focus:border-lime-500" id="pembagian-area">
               <option>(All)</option>
            </select>
         </div>
         <div class="flex flex-col gap-1">
            <label for="coverage-status">Coverage Status</label>
            <select class="text-xs border-gray-300 rounded p-1 w-32 focus:ring-lime-500 focus:border-lime-500" id="coverage-status">
               <option>(All)</option>
            </select>
         </div>
      </section>
      <!-- END: FilterSection -->
      <main class="p-4 space-y-4">
         <!-- BEGIN: TopGrid -->
         <div class="grid grid-cols-12 gap-4">
            <!-- Summary Coverage Area Chart -->
            <section class="col-span-12 lg:col-span-6 bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
               <div class="bg-gray-50 px-3 py-1 border-b border-gray-200 text-center font-bold text-xs uppercase tracking-wider">
                  Summary Coverage Area Last 4 Week
               </div>
               <div class="p-4 h-64 flex items-end justify-between gap-1 overflow-x-auto">
                  <!-- Mockup of the bar chart with groups -->
                  <div class="flex flex-col items-center h-full flex-1 min-w-[60px]" data-purpose="chart-group">
                     <div class="text-[10px] font-bold mb-2">BMO 1</div>
                     <div class="w-full flex h-full gap-1 items-end">
                        <div class="w-full bg-gray-200 h-[100%] relative group"><span class="absolute -top-4 left-1/2 -translate-x-1/2 text-[9px]">100%</span></div>
                        <div class="w-full bg-gray-300 h-[98%] relative group"><span class="absolute -top-4 left-1/2 -translate-x-1/2 text-[9px]">98%</span></div>
                     </div>
                  </div>
                  <div class="flex flex-col items-center h-full flex-1 min-w-[60px]" data-purpose="chart-group">
                     <div class="text-[10px] font-bold mb-2 text-gray-400">BMO 2</div>
                     <div class="w-full flex h-full gap-1 items-end">
                        <div class="w-full bg-gray-200 h-[99%]"></div>
                        <div class="w-full bg-gray-300 h-[95%]"></div>
                     </div>
                  </div>
                  <div class="flex flex-col items-center h-full flex-1 min-w-[60px]" data-purpose="chart-group">
                     <div class="text-[10px] font-bold mb-2">BMO 3</div>
                     <div class="w-full flex h-full gap-1 items-end">
                        <div class="w-full bg-green-900 h-[100%] relative group"><span class="absolute -top-4 left-1/2 -translate-x-1/2 text-[9px] font-bold text-green-800">100%</span></div>
                        <div class="w-full bg-gray-300 h-[98%]"></div>
                     </div>
                  </div>
                  <div class="flex flex-col items-center h-full flex-1 min-w-[60px]" data-purpose="chart-group">
                     <div class="text-[10px] font-bold mb-2 text-gray-400">EKSPLORASI</div>
                     <div class="w-full flex h-full gap-1 items-end">
                        <div class="w-full bg-gray-200 h-[90%]"></div>
                        <div class="w-full bg-gray-300 h-[92%]"></div>
                     </div>
                  </div>
                  <!-- Additional bars repeated to match look -->
                  <div class="flex flex-col items-center h-full flex-1 min-w-[60px]" data-purpose="chart-group">
                     <div class="text-[10px] font-bold mb-2">GMO</div>
                     <div class="w-full flex h-full gap-1 items-end">
                        <div class="w-full bg-gray-200 h-[96%]"></div>
                        <div class="w-full bg-gray-300 h-[96%]"></div>
                     </div>
                  </div>
                  <div class="flex flex-col items-center h-full flex-1 min-w-[60px]" data-purpose="chart-group">
                     <div class="text-[10px] font-bold mb-2">HO</div>
                     <div class="w-full flex h-full gap-1 items-end">
                        <div class="w-full bg-pink-100 h-[90%]"></div>
                        <div class="w-full bg-pink-200 h-[86%]"></div>
                     </div>
                  </div>
                  <div class="flex flex-col items-center h-full flex-1 min-w-[60px]" data-purpose="chart-group">
                     <div class="text-[10px] font-bold mb-2">LMO</div>
                     <div class="w-full flex h-full gap-1 items-end">
                        <div class="w-full bg-gray-200 h-[96%]"></div>
                        <div class="w-full bg-gray-300 h-[98%]"></div>
                     </div>
                  </div>
               </div>
            </section>
            <!-- Key Metrics & Geotagging -->
            <section class="col-span-12 lg:col-span-2 space-y-4">
               <div class="bg-white border border-gray-200 rounded shadow-sm p-4 text-center">
                  <h3 class="text-[10px] font-bold border-b pb-1 mb-2">A. % PENGECEKAN COVERAGE AREA</h3>
                  <div class="text-4xl font-black text-gray-800">97%</div>
                  <div class="text-[10px] text-gray-500 mt-1">
                     Lokasi Tercover: <span class="font-bold text-gray-700">1,474 / Lokasi</span><br/>
                     Teregister: <span class="font-bold text-gray-700">1,524</span>
                  </div>
               </div>
               <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden flex flex-col h-56">
                  <h3 class="text-[10px] font-bold border-b p-1 text-center bg-gray-50">METODE LAPORAN</h3>
                  <div class="flex-1 flex flex-col p-1">
                     <div class="flex-1 bg-green-600 rounded-t flex flex-col items-center justify-center text-white p-2">
                        <span class="text-[9px] uppercase font-bold">Geotagging</span>
                        <span class="text-sm font-bold">37,794 (86.7%)</span>
                     </div>
                     <div class="h-12 bg-orange-400 rounded-b flex flex-col items-center justify-center text-white p-1 mt-1">
                        <span class="text-[9px] uppercase font-bold">Non Geotagging</span>
                        <span class="text-xs font-bold">5,793 (13.3%)</span>
                     </div>
                  </div>
               </div>
            </section>
            <!-- Daily Trend Line Charts -->
            <section class="col-span-12 lg:col-span-4 bg-white border border-gray-200 rounded shadow-sm flex flex-col">
               <div class="flex-1 p-3 border-b border-gray-100 flex flex-col">
                  <h3 class="text-[10px] font-bold text-center mb-1">B. %PENGECEKAN DAILY COVERAGE AREA</h3>
                  <div class="flex-1 relative flex items-center justify-center">
                     <canvas class="w-full h-full" data-purpose="line-chart" id="chartB"></canvas>
                     <!-- Static fallback visualization if canvas is empty -->
                     <div class="absolute inset-0 flex items-center justify-around pointer-events-none px-4">
                        <div class="text-[9px] font-bold text-gray-400">80%</div>
                        <div class="text-[9px] font-bold text-gray-400">83%</div>
                        <div class="text-[9px] font-bold text-gray-400">83%</div>
                        <div class="text-[9px] font-bold text-gray-400">83%</div>
                        <div class="text-[9px] font-bold text-red-600">31%</div>
                     </div>
                  </div>
               </div>
               <div class="flex-1 p-3 border-b border-gray-100 flex flex-col">
                  <h3 class="text-[10px] font-bold text-center mb-1">C. COVERAGE DAILY - AREA KRITIS</h3>
                  <div class="flex-1 relative">
                     <canvas class="w-full h-full" data-purpose="line-chart" id="chartC"></canvas>
                  </div>
               </div>
               <div class="flex-1 p-3 flex flex-col">
                  <h3 class="text-[10px] font-bold text-center mb-1">D. COVERAGE DAILY - AREA NON KRITIS</h3>
                  <div class="flex-1 relative">
                     <canvas class="w-full h-full" data-purpose="line-chart" id="chartD"></canvas>
                  </div>
               </div>
            </section>
         </div>
         <!-- END: TopGrid -->
         <!-- BEGIN: TableLokasiTerlapor -->
         <section class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">
            <header class="bg-gray-50 border-b border-gray-200 px-4 py-2">
               <h2 class="text-xs font-bold uppercase tracking-wider">E. COVERAGE DAILY - LOKASI TERLAPOR</h2>
            </header>
            <div class="overflow-x-auto">
               <table class="w-full text-[10px] border-collapse" id="coverage-table">
                  <thead>
                     <tr class="bg-white border-b border-gray-200 text-left">
                        <th class="p-2 border-r border-gray-100 min-w-[80px]">Site Used.</th>
                        <th class="p-2 border-r border-gray-100 min-w-[150px]">Lokasi</th>
                        <th class="p-2 border-r border-gray-100 min-w-[120px]">Pembagian Area</th>
                        <th class="p-2 border-r border-gray-100 text-center w-40">March 9, 2026</th>
                        <th class="p-2 border-r border-gray-100 text-center w-40">March 10, 2026</th>
                        <th class="p-2 border-r border-gray-100 text-center w-40">March 11, 2026</th>
                        <th class="p-2 border-r border-gray-100 text-center w-40">March 12, 2026</th>
                        <th class="p-2 text-center w-40">March 13, 2026</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr class="border-b border-gray-50">
                        <td class="p-2 font-bold align-top">BMO 1</td>
                        <td class="p-2">(B 56) Area Kerja FAD</td>
                        <td class="p-2">Area Kritis</td>
                        <td class="p-2 text-center status-green">100%<br/>(1/1)</td>
                        <td class="p-2 text-center status-red">0%<br/>(0/1)</td>
                        <td class="p-2 border border-gray-100">-</td>
                        <td class="p-2 border border-gray-100">-</td>
                        <td class="p-2 border border-gray-100">-</td>
                     </tr>
                     <tr class="border-b border-gray-50">
                        <td class="p-2"></td>
                        <td class="p-2">(BUMA) Pos Pengawas</td>
                        <td class="p-2">Area Non Kritis</td>
                        <td class="p-2 text-center status-green">100%<br/>(1/1)</td>
                        <td class="p-2 text-center status-green">100%<br/>(1/1)</td>
                        <td class="p-2 text-center status-green">100%<br/>(1/1)</td>
                        <td class="p-2 text-center status-green">100%<br/>(1/1)</td>
                        <td class="p-2 text-center status-red">0%<br/>(0/1)</td>
                     </tr>
                     <tr class="border-b border-gray-50">
                        <td class="p-2"></td>
                        <td class="p-2">(BUMA) Workshop</td>
                        <td class="p-2">Area Non Kritis</td>
                        <td class="p-2 text-center status-light-green">86%<br/>(6/7)</td>
                        <td class="p-2 text-center status-green">100%<br/>(7/7)</td>
                        <td class="p-2 text-center status-green">100%<br/>(7/7)</td>
                        <td class="p-2 text-center status-green">100%<br/>(7/7)</td>
                        <td class="p-2 text-center status-yellow">57%<br/>(4/7)</td>
                     </tr>
                     <tr class="border-b border-gray-50">
                        <td class="p-2"></td>
                        <td class="p-2">Area Revegetasi</td>
                        <td class="p-2">Area Non Kritis</td>
                        <td class="p-2 text-center status-green">100%<br/>(1/1)</td>
                        <td class="p-2 text-center status-green">100%<br/>(1/1)</td>
                        <td class="p-2 text-center status-green">100%<br/>(1/1)</td>
                        <td class="p-2 text-center status-green">100%<br/>(1/1)</td>
                        <td class="p-2 text-center status-green">100%<br/>(1/1)</td>
                     </tr>
                     <tr class="border-b border-gray-50">
                        <td class="p-2"></td>
                        <td class="p-2">Area Transportasi</td>
                        <td class="p-2">Area Non Kritis</td>
                        <td class="p-2 text-center status-green">100%<br/>(3/3)</td>
                        <td class="p-2 text-center status-yellow">67%<br/>(2/3)</td>
                        <td class="p-2 text-center status-green">100%<br/>(3/3)</td>
                        <td class="p-2 text-center status-green">100%<br/>(3/3)</td>
                        <td class="p-2 text-center status-yellow">67%<br/>(2/3)</td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </section>
         <!-- END: TableLokasiTerlapor -->
         <!-- BEGIN: TableShiftChecklist -->
         <section class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden mb-8">
            <header class="bg-gray-50 border-b border-gray-200 px-4 py-2">
               <h2 class="text-xs font-bold uppercase tracking-wider">F. COVERAGE DAILY - LIST AREA PENGECEKAN PER SHIFT</h2>
            </header>
            <div class="overflow-x-auto">
               <table class="w-full text-[9px] border-collapse table-fixed" id="shift-table">
                  <thead>
                     <tr class="bg-gray-100 text-left border-b border-gray-200">
                        <th class="p-1 border-r border-gray-200 w-48">Lokasi - Detail Lokasi</th>
                        <th class="p-1 border-r border-gray-200 w-24">Pembagian Ar..</th>
                        <th class="p-1 border-r border-gray-200 w-24">shift bedraf..</th>
                        <th class="p-1 border-r border-gray-200 text-center" colspan="4">March 9, 2026</th>
                        <th class="p-1 border-r border-gray-200 text-center" colspan="4">March 10, 2026</th>
                        <th class="p-1 border-r border-gray-200 text-center" colspan="4">March 11, 2026</th>
                        <th class="p-1 border-r border-gray-200 text-center" colspan="4">March 12, 2026</th>
                        <th class="p-1 text-center" colspan="4">March 13, 2026</th>
                     </tr>
                     <tr class="bg-gray-50 text-[8px] text-gray-400 border-b border-gray-200">
                        <th colspan="3"></th>
                        <!-- Repeat for each date column set -->
                        <th class="p-0.5 border-r border-gray-100 font-normal">No Laporan</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">Awal</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">Tengah</th>
                        <th class="p-0.5 border-r border-gray-200 font-normal">Akhir</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">No Laporan</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">Awal</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">Tengah</th>
                        <th class="p-0.5 border-r border-gray-200 font-normal">Akhir</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">No Laporan</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">Awal</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">Tengah</th>
                        <th class="p-0.5 border-r border-gray-200 font-normal">Akhir</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">No Laporan</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">Awal</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">Tengah</th>
                        <th class="p-0.5 border-r border-gray-200 font-normal">Akhir</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">No Laporan</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">Awal</th>
                        <th class="p-0.5 border-r border-gray-100 font-normal">Tengah</th>
                        <th class="p-0.5 border-r border-gray-200 font-normal">Akhir</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr class="border-b border-gray-100">
                        <td class="p-1 align-top border-r">( BC ) Kantin | Dapur ARCO</td>
                        <td class="p-1 align-top border-r">Area Non Kritis</td>
                        <td class="p-1 border-r">
                           <div class="text-gray-400">No Laporan</div>
                           <div class="bg-gray-50">Shift 1</div>
                           <div>Shift 2</div>
                        </td>
                        <!-- Mar 9 -->
                        <td class="p-0 border-r text-center align-middle" colspan="4">
                           <div class="h-4"></div>
                           <div class="h-4 text-green-600">✔</div>
                           <div class="h-4"></div>
                        </td>
                        <!-- Mar 10 -->
                        <td class="p-0 border-r text-center align-middle" colspan="4">
                           <div class="h-4 text-red-600 font-bold">✕</div>
                           <div class="h-4"></div>
                           <div class="h-4"></div>
                        </td>
                        <!-- Mar 11 -->
                        <td class="p-0 border-r text-center align-middle" colspan="4">
                           <div class="h-4"></div>
                           <div class="h-4 text-green-600">✔</div>
                           <div class="h-4 text-green-600">✔</div>
                        </td>
                        <!-- Mar 12 -->
                        <td class="p-0 border-r text-center align-middle" colspan="4">
                           <div class="h-4"></div>
                           <div class="h-4 text-green-600">✔</div>
                           <div class="h-4"></div>
                        </td>
                        <!-- Mar 13 -->
                        <td class="p-0 text-center align-middle" colspan="4">
                           <div class="h-4 text-red-600 font-bold">✕</div>
                           <div class="h-4"></div>
                           <div class="h-4"></div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </section>
         <!-- END: TableShiftChecklist -->
      </main>
      <!-- BEGIN: ChartScripts -->
      <script data-purpose="chart-drawing">
         function drawSimpleTrend(id, points, color) {
           const canvas = document.getElementById(id);
           if (!canvas) return;
           const ctx = canvas.getContext('2d');
           const w = canvas.width = canvas.offsetWidth;
           const h = canvas.height = canvas.offsetHeight;
           
           ctx.clearRect(0, 0, w, h);
           ctx.beginPath();
           ctx.strokeStyle = color;
           ctx.lineWidth = 2;
           
           const step = w / (points.length - 1);
           points.forEach((p, i) => {
             const x = i * step;
             const y = h - (p * h / 100);
             if (i === 0) ctx.moveTo(x, y);
             else ctx.lineTo(x, y);
             
             // Draw dot
             ctx.fillStyle = color;
             ctx.beginPath();
             ctx.arc(x, y, 3, 0, Math.PI * 2);
             ctx.fill();
             ctx.moveTo(x, y);
           });
           ctx.stroke();
         }
         
         // Initialize pseudo-charts on load
         window.addEventListener('load', () => {
           drawSimpleTrend('chartB', [80, 83, 83, 83, 31], '#15803d');
           drawSimpleTrend('chartC', [72, 69, 75, 78, 36], '#b91c1c');
           drawSimpleTrend('chartD', [80, 84, 83, 84, 30], '#15803d');
         });
         
         // Redraw on resize
         window.addEventListener('resize', () => {
           drawSimpleTrend('chartB', [80, 83, 83, 83, 31], '#15803d');
           drawSimpleTrend('chartC', [72, 69, 75, 78, 36], '#b91c1c');
           drawSimpleTrend('chartD', [80, 84, 83, 84, 30], '#15803d');
         });
      </script>
      <!-- END: ChartScripts -->
   </body>
</html>