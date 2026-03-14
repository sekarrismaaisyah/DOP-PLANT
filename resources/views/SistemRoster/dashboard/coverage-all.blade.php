<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8"/>
      <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
      <title>PT Berau Coal - Mining Operations Dashboard</title>
      <!-- Tailwind CSS v3 with Plugins (CDN: for dev only; for production use PostCSS/CLI: https://tailwindcss.com/docs/installation) -->
      <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
      <!-- Google Fonts: Inter -->
      <link href="https://fonts.googleapis.com" rel="preconnect"/>
      <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
      <!-- Swiper CSS -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
      <script>
         tailwind.config = {
           theme: {
             extend: {
               fontFamily: {
                 sans: ['Inter', 'sans-serif'],
               },
               colors: {
                 'berau-green-dark': '#1a5f2a',
                 'berau-green-light': '#2d7a3e',
                 'dashboard-bg': '#f8f9fa',
                 'success-green': '#228B22',
                 'soft-green': '#90EE90',
                 'alert-red': '#DC1432',
                 'warn-yellow': '#FFD700',
               }
             }
           }
         }
      </script>
      <style data-purpose="custom-layouts">
         .grid-dashboard {
         display: grid;
         grid-template-columns: 2fr 1fr;
         gap: 16px;
         }
         .chart-placeholder {
         background-image: linear-gradient(45deg, #f3f4f6 25%, transparent 25%, transparent 50%, #f3f4f6 50%, #f3f4f6 75%, transparent 75%, transparent);
         background-size: 20px 20px;
         }
         .trend-swiper-wrapper { padding: 0 4px; position: relative; }
         .trend-coverage-swiper { overflow: hidden; cursor: grab; -webkit-user-select: none; user-select: none; }
         .trend-coverage-swiper:active { cursor: grabbing; }
         .trend-coverage-swiper .swiper-slide { height: auto; flex-shrink: 0; }
         .trend-coverage-prev, .trend-coverage-next { color: #1a5f2a; }
         .trend-swiper-hint { font-size: 10px; color: #9ca3af; margin-top: 6px; text-align: center; }
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
   <body class="bg-dashboard-bg font-sans text-slate-800">
      <!-- BEGIN: MainHeader -->
    
      <!-- END: MainHeader -->
      <main class="p-6 max-w-[1600px] mx-auto space-y-4">
         <!-- BEGIN: FilterBar -->
         <nav class="bg-white p-4 rounded-lg shadow-sm flex flex-wrap gap-4 items-center" data-purpose="filter-controls">
            <div class="flex flex-col gap-1">
               <label class="text-[10px] font-semibold uppercase text-gray-500">Duration</label>
               <select class="bg-gray-50 border-none rounded-lg py-2 px-4 text-sm font-medium shadow-sm focus:ring-berau-green-light min-w-[150px]">
                  <option>Last 4 Week</option>
               </select>
            </div>
            <div class="flex flex-col gap-1">
               <label class="text-[10px] font-semibold uppercase text-gray-500">Timeframe</label>
               <select class="bg-gray-50 border-none rounded-lg py-2 px-4 text-sm font-medium shadow-sm focus:ring-berau-green-light min-w-[120px]">
                  <option>Week</option>
               </select>
            </div>
            <div class="flex flex-col gap-1">
               <label class="text-[10px] font-semibold uppercase text-gray-500">Location</label>
               <select class="bg-gray-50 border-none rounded-lg py-2 px-4 text-sm font-medium shadow-sm focus:ring-berau-green-light min-w-[120px]">
                  <option>Site</option>
               </select>
            </div>
            <div class="flex flex-col gap-1">
               <label class="text-[10px] font-semibold uppercase text-gray-500">Zone</label>
               <select class="bg-gray-50 border-none rounded-lg py-2 px-4 text-sm font-medium shadow-sm focus:ring-berau-green-light min-w-[160px]">
                  <option>Pembagian Area</option>
               </select>
            </div>
            <div class="flex flex-col gap-1">
               <label class="text-[10px] font-semibold uppercase text-gray-500">Status</label>
               <select class="bg-gray-50 border-none rounded-lg py-2 px-4 text-sm font-medium shadow-sm focus:ring-berau-green-light min-w-[160px]">
                  <option>Coverage Status</option>
               </select>
            </div>
         </nav>
         <!-- END: FilterBar -->
         <!-- BEGIN: TopSection -->
         <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Section A: Summary Bar Chart (Chart.js) -->
            <section class="lg:col-span-2 bg-white p-5 rounded-xl shadow-sm border border-gray-100" data-purpose="summary-bar-chart">
               <h2 class="text-sm font-bold text-gray-700 uppercase mb-6">SUMMARY COVERAGE AREA LAST WEEK</h2>
               <div class="h-64 min-h-[256px] relative">
                  <canvas id="summaryCoverageBarChart" aria-label="Summary Coverage Area Last Week"></canvas>
               </div>
            </section>
            <!-- Section B: KPI Metrics -->
            <section class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-center items-center text-center space-y-4" data-purpose="kpi-metrics-card">
               <div class="text-6xl font-black text-berau-green-light">97%</div>
               <div class="text-lg font-semibold text-gray-600 uppercase">Overall Coverage Rate</div>
               <div class="w-full border-t border-gray-100 pt-4 grid grid-cols-2 gap-4">
                  <div>
                     <p class="text-[10px] text-gray-400 font-bold uppercase">Total Sites</p>
                     <p class="text-2xl font-bold text-gray-800">124</p>
                  </div>
                  <div>
                     <p class="text-[10px] text-gray-400 font-bold uppercase">Covered Sites</p>
                     <p class="text-2xl font-bold text-berau-green-light">120</p>
                  </div>
               </div>
            </section>
         </div>
         <!-- END: TopSection -->
         <!-- BEGIN: TrendAnalysisGrid (Swiper - auto slide + scroll kiri/kanan) -->
         <div class="trend-swiper-wrapper relative">
            <div class="swiper trend-coverage-swiper">
               <div class="swiper-wrapper">
                  <!-- Trend 1 -->
                  <div class="swiper-slide">
                     <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-full">
                        <h3 class="text-[11px] font-bold text-gray-500 uppercase mb-3">BMO Coverage Trend</h3>
                        <div class="h-32 relative chart-placeholder rounded flex items-center justify-center">
                           <svg class="w-full h-full px-2" viewbox="0 0 100 40">
                              <polyline fill="none" points="0,10 25,5 50,15 75,8 100,12" stroke="#228B22" stroke-width="2"></polyline>
                              <polyline fill="none" points="0,20 25,18 50,25 75,20 100,22" stroke="#DC1432" stroke-dasharray="2" stroke-width="1.5"></polyline>
                           </svg>
                        </div>
                        <div class="flex justify-between mt-2 text-[10px] text-gray-400 font-medium">
                           <span>Mar 9</span><span>Mar 11</span><span>Mar 13</span>
                        </div>
                     </div>
                  </div>
                  <!-- Trend 2 -->
                  <div class="swiper-slide">
                     <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-full">
                        <h3 class="text-[11px] font-bold text-gray-500 uppercase mb-3">GMO Coverage Trend</h3>
                        <div class="h-32 relative chart-placeholder rounded flex items-center justify-center">
                           <svg class="w-full h-full px-2" viewbox="0 0 100 40">
                              <polyline fill="none" points="0,15 25,12 50,5 75,8 100,2" stroke="#228B22" stroke-width="2"></polyline>
                              <polyline fill="none" points="0,25 25,28 50,22 75,26 100,20" stroke="#DC1432" stroke-dasharray="2" stroke-width="1.5"></polyline>
                           </svg>
                        </div>
                        <div class="flex justify-between mt-2 text-[10px] text-gray-400 font-medium">
                           <span>Mar 9</span><span>Mar 11</span><span>Mar 13</span>
                        </div>
                     </div>
                  </div>
                  <!-- Trend 3 -->
                  <div class="swiper-slide">
                     <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-full">
                        <h3 class="text-[11px] font-bold text-gray-500 uppercase mb-3">MARINE Coverage Trend</h3>
                        <div class="h-32 relative chart-placeholder rounded flex items-center justify-center">
                           <svg class="w-full h-full px-2" viewbox="0 0 100 40">
                              <polyline fill="none" points="0,5 25,10 50,15 75,5 100,8" stroke="#228B22" stroke-width="2"></polyline>
                              <polyline fill="none" points="0,15 25,20 50,25 75,18 100,22" stroke="#DC1432" stroke-dasharray="2" stroke-width="1.5"></polyline>
                           </svg>
                        </div>
                        <div class="flex justify-between mt-2 text-[10px] text-gray-400 font-medium">
                           <span>Mar 9</span><span>Mar 11</span><span>Mar 13</span>
                        </div>
                     </div>
                  </div>
                  <!-- Trend 4 -->
                  <div class="swiper-slide">
                     <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-full">
                        <h3 class="text-[11px] font-bold text-gray-500 uppercase mb-3">SMO Coverage Trend</h3>
                        <div class="h-32 relative chart-placeholder rounded flex items-center justify-center">
                           <svg class="w-full h-full px-2" viewbox="0 0 100 40">
                              <polyline fill="none" points="0,8 25,15 50,10 75,12 100,5" stroke="#228B22" stroke-width="2"></polyline>
                              <polyline fill="none" points="0,20 25,25 50,18 75,22 100,15" stroke="#DC1432" stroke-dasharray="2" stroke-width="1.5"></polyline>
                           </svg>
                        </div>
                        <div class="flex justify-between mt-2 text-[10px] text-gray-400 font-medium">
                           <span>Mar 9</span><span>Mar 11</span><span>Mar 13</span>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- <div class="swiper-button-prev trend-coverage-prev"></div>
               <div class="swiper-button-next trend-coverage-next"></div>
               <div class="swiper-pagination trend-coverage-pagination"></div> -->
            </div>
            <p class="trend-swiper-hint" aria-hidden="true">Geser kiri/kanan untuk melihat card lainnya</p>
         </div>
         <!-- END: TrendAnalysisGrid -->
         <!-- BEGIN: DataTablesSection -->

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



         <div class="grid grid-cols-1 xl:grid-cols-2 gap-4" data-purpose="detailed-data-tables">
            <!-- Table 1 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
               <div class="bg-[#4a4a4a] p-3">
                  <h3 class="text-white text-xs font-bold uppercase">Coverage by Site</h3>
               </div>
               <div class="overflow-x-auto">
                  <table class="w-full text-xs text-left border-collapse">
                     <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                           <th class="p-3 font-bold text-gray-600">Site Name</th>
                           <th class="p-3 font-bold text-gray-600 text-center">Mar 09</th>
                           <th class="p-3 font-bold text-gray-600 text-center">Mar 10</th>
                           <th class="p-3 font-bold text-gray-600 text-center">Mar 11</th>
                           <th class="p-3 font-bold text-gray-600 text-center">Mar 12</th>
                           <th class="p-3 font-bold text-gray-600 text-center">Mar 13</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr class="border-b border-gray-50">
                           <td class="p-3 font-medium">Lati Central</td>
                           <td class="p-3 text-center bg-soft-green font-bold">98%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">97%</td>
                           <td class="p-3 text-center bg-warn-yellow font-bold">94%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">96%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">95%</td>
                        </tr>
                        <tr class="border-b border-gray-50">
                           <td class="p-3 font-medium">Binungan Block 7</td>
                           <td class="p-3 text-center bg-soft-green font-bold">99%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">98%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">99%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">97%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">99%</td>
                        </tr>
                        <tr class="border-b border-gray-50">
                           <td class="p-3 font-medium">Sambarata North</td>
                           <td class="p-3 text-center bg-alert-red text-white font-bold">82%</td>
                           <td class="p-3 text-center bg-warn-yellow font-bold">88%</td>
                           <td class="p-3 text-center bg-warn-yellow font-bold">92%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">95%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">97%</td>
                        </tr>
                        <tr class="border-b border-gray-50">
                           <td class="p-3 font-medium">Gurimba Port</td>
                           <td class="p-3 text-center bg-soft-green font-bold">95%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">95%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">96%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">95%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">96%</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>
            <!-- Table 2 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
               <div class="bg-[#4a4a4a] p-3">
                  <h3 class="text-white text-xs font-bold uppercase">Coverage by Area Type</h3>
               </div>
               <div class="overflow-x-auto">
                  <table class="w-full text-xs text-left border-collapse">
                     <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                           <th class="p-3 font-bold text-gray-600">Area Type</th>
                           <th class="p-3 font-bold text-gray-600 text-center">Mar 09</th>
                           <th class="p-3 font-bold text-gray-600 text-center">Mar 10</th>
                           <th class="p-3 font-bold text-gray-600 text-center">Mar 11</th>
                           <th class="p-3 font-bold text-gray-600 text-center">Mar 12</th>
                           <th class="p-3 font-bold text-gray-600 text-center">Mar 13</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr class="border-b border-gray-50">
                           <td class="p-3 font-medium">Production Area</td>
                           <td class="p-3 text-center bg-soft-green font-bold">96%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">96%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">95%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">97%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">98%</td>
                        </tr>
                        <tr class="border-b border-gray-50">
                           <td class="p-3 font-medium">Disposal Area</td>
                           <td class="p-3 text-center bg-warn-yellow font-bold">91%</td>
                           <td class="p-3 text-center bg-warn-yellow font-bold">89%</td>
                           <td class="p-3 text-center bg-alert-red text-white font-bold">84%</td>
                           <td class="p-3 text-center bg-warn-yellow font-bold">88%</td>
                           <td class="p-3 text-center bg-warn-yellow font-bold">92%</td>
                        </tr>
                        <tr class="border-b border-gray-50">
                           <td class="p-3 font-medium">Logistics Path</td>
                           <td class="p-3 text-center bg-soft-green font-bold">100%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">100%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">99%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">100%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">100%</td>
                        </tr>
                        <tr class="border-b border-gray-50">
                           <td class="p-3 font-medium">Workshop Area</td>
                           <td class="p-3 text-center bg-soft-green font-bold">97%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">97%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">98%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">97%</td>
                           <td class="p-3 text-center bg-soft-green font-bold">97%</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
         <!-- END: DataTablesSection -->
      </main>
      <footer class="p-6 text-center text-gray-400 text-xs">
         © 2023 PT Berau Coal - Business Intelligence Mining Monitoring System
      </footer>
      <!-- Chart.js -->
      <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
      <!-- Swiper JS -->
      <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
      <script>
         document.addEventListener('DOMContentLoaded', function () {
            // Summary Coverage Bar Chart
            var summaryCtx = document.getElementById('summaryCoverageBarChart');
            if (summaryCtx) {
               new Chart(summaryCtx, {
                  type: 'bar',
                  data: {
                     labels: ['BMO 1', 'BMO 2', 'BMO 3', 'EKSPLORASI', 'GMO', 'HO', 'LMO', 'MARINE', 'PMO', 'SMO'],
                     datasets: [
                        {
                           label: 'Actual',
                           data: [88, 90, 82, 55, 98, 70, 78, 96, 85, 92],
                           backgroundColor: 'rgba(34, 139, 34, 0.9)',
                           borderColor: '#1a5f2a',
                           borderWidth: 1,
                           borderRadius: { topLeft: 4, topRight: 4 },
                           borderSkipped: false,
                        }
                     ]
                  },
                  options: {
                     responsive: true,
                     maintainAspectRatio: false,
                     interaction: { intersect: false, mode: 'index' },
                     plugins: {
                        legend: { display: false },
                        tooltip: {
                           callbacks: {
                              label: function (ctx) { return 'Actual: ' + ctx.raw + '%'; }
                           }
                        }
                     },
                     scales: {
                        y: {
                           beginAtZero: true,
                           max: 100,
                           ticks: {
                              stepSize: 20,
                              callback: function (v) { return v + '%'; },
                              font: { size: 10 }
                           },
                           grid: { color: 'rgba(0,0,0,0.06)' }
                        },
                        x: {
                           ticks: { maxRotation: 45, minRotation: 35, font: { size: 10 } },
                           grid: { display: false }
                        }
                     },
                     datasets: { bar: { barPercentage: 0.6, categoryPercentage: 0.8 } }
                  }
               });
            }

            new Swiper('.trend-coverage-swiper', {
               slidesPerView: 1.15,
               spaceBetween: 16,
               loop: false,
               grabCursor: true,
               allowTouchMove: true,
               touchRatio: 1,
               threshold: 5,
               resistance: true,
               resistanceRatio: 0.85,
               autoplay: {
                  delay: 3000,
                  disableOnInteraction: false,
               },
               direction: 'horizontal',
               speed: 400,
               breakpoints: {
                  480:  { slidesPerView: 1.5 },
                  640:  { slidesPerView: 2.2 },
                  1024: { slidesPerView: 3.2 },
               },
               navigation: {
                  nextEl: '.trend-coverage-next',
                  prevEl: '.trend-coverage-prev',
               },
               pagination: {
                  el: '.trend-coverage-pagination',
                  clickable: true,
               },
            });
         });
      </script>
   </body>
</html>