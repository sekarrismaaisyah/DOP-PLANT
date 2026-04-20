<!DOCTYPE html>
<html class="light" lang="id">
   <head>
      <meta charset="utf-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <title>BMO2 Safety — Tools Analisis</title>
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
         .anchored-card {
         box-shadow: 0 4px 0px 0px rgba(0, 0, 0, 0.05), 0 12px 24px -4px rgba(0, 0, 0, 0.15);
         border: 1px solid rgba(0, 0, 0, 0.08);
         transition: transform 0.2s ease, box-shadow 0.2s ease;
         }
         .anchored-card:hover {
         transform: translateY(-2px);
         box-shadow: 0 6px 0px 0px rgba(0, 0, 0, 0.05), 0 20px 32px -8px rgba(0, 0, 0, 0.2);
         }
         input[type=number]::-webkit-outer-spin-button,
         input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
         input[type=number] { -moz-appearance: textfield; }
      </style>
      <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
   </head>
   <body class="bg-[#f0f2f5] font-body text-on-surface min-h-screen min-w-0 overflow-x-hidden flex flex-col">
      <!-- <header class="w-full sticky top-0 bg-[#ffffff] border-b border-[#dfe3e6] z-50 shadow-sm">
         <div class="mx-auto flex items-center justify-between px-8 py-4">
            <div class="flex items-center gap-10">
               <div class="flex flex-col">
                  <h1 class="font-headline text-xl font-bold leading-tight tracking-tighter text-[#3952bc]">OHS Division</h1>
                  <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Safety Performance Review</p>
               </div>
               <div class="hidden h-8 w-px bg-[#dfe3e6] lg:block"></div>
               <nav class="hidden gap-8 md:flex">
                  <a class="text-sm font-semibold tracking-tight text-[#595c5e] transition-colors hover:text-[#3952bc]" href="{{ route('peer-pressure-edukasi.dashboard') }}">Lagging</a>
                  <a class="text-sm font-semibold tracking-tight text-[#595c5e] transition-colors hover:text-[#3952bc]" href="{{ route('peer-pressure-edukasi.dashboard-performance') }}">Dash Performance</a>
                  <a class="text-sm font-semibold tracking-tight text-[#595c5e] transition-colors hover:text-[#3952bc]" href="{{ route('peer-pressure-edukasi.tematic') }}">Thematic Alignment</a>
                  <a class="border-b-2 border-[#3952bc] pb-1 text-sm font-bold tracking-tight text-[#3952bc]" href="{{ route('peer-pressure-edukasi.tools-analisis') }}">Tools Analisis</a>
               </nav>
            </div>
            <div class="flex items-center gap-6">
               <div class="relative hidden group xl:block">
                  <input class="w-80 rounded-full border-none bg-[#f5f7f9] px-4 py-2 text-sm shadow-inner transition-all focus:ring-2 focus:ring-primary" placeholder="Search safety records..." type="text" readonly tabindex="-1"/>
                  <span class="material-symbols-outlined absolute right-3 top-2 text-on-surface-variant" data-icon="search">search</span>
               </div>
               <div class="flex items-center gap-3">
                  <span class="relative inline-flex rounded-full p-2 text-on-surface-variant">
                     <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                     <span class="absolute right-1 top-1 h-2.5 w-2.5 rounded-full border-2 border-white bg-error"></span>
                  </span>
                  <div class="flex items-center gap-2 rounded-full border border-outline-variant/30 bg-white p-1.5 pr-4 shadow-sm">
                     <span class="material-symbols-outlined text-3xl text-primary" data-icon="account_circle">account_circle</span>
                     <div class="text-left">
                        <p class="text-[10px] font-bold uppercase leading-none text-primary">Safety Admin</p>
                        <p class="text-[9px] font-medium text-on-surface-variant">Site Manager</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </header> -->
      <main class="mx-auto w-full min-w-0 max-w-[1600px] flex-grow space-y-8 px-4 py-6 sm:px-8 sm:py-8">
         <div id="app" class="min-w-0 space-y-8"></div>
      </main>

<script>
const FEATURE_META = [
  { key: 'blindspotTbc', label: 'Blindspot TBC', description: 'Area risiko yang belum cukup tertangkap atau ditindaklanjuti.' },
  { key: 'coverageArea', label: 'Daily Coverage Area', description: 'Jangkauan pengawasan terhadap area kritikal operasi.' },
  { key: 'goldenRules', label: 'Golden Rules', description: 'Disiplin terhadap kontrol kritikal dan aturan fatal risk.' },
  { key: 'hazard', label: 'Pelaporan Hazard', description: 'Sensitivitas identifikasi deviasi dan hazard lapangan.' },
  { key: 'tbc', label: 'Pelaporan TBC', description: 'Kemampuan mengangkat concern hazard signifikan.' },
  { key: 'rfidSupervisor', label: 'RFID Pengawas', description: 'Kapasitas presence pengawasan di lapangan.' },
  { key: 'ratioNonToSupervisor', label: 'Rasio Non Pengawas : Pengawas', description: 'Ketimpangan antara eksposur aktivitas dan kapasitas pengawasan.' },
];

const SITE_ROWS = {
  'All Site': [
    { week: 'W40', actualIncidents: 0, hazard: 23980, rfidNonSupervisor: 13180, rfidSupervisor: 4175, tbc: 8830, goldenRules: 3, blindspotTbc: 24, coverageArea: 73.2 },
    { week: 'W41', actualIncidents: 1, hazard: 23640, rfidNonSupervisor: 13260, rfidSupervisor: 4090, tbc: 8650, goldenRules: 2, blindspotTbc: 28, coverageArea: 72.1 },
    { week: 'W42', actualIncidents: 0, hazard: 23410, rfidNonSupervisor: 13310, rfidSupervisor: 4045, tbc: 8510, goldenRules: 2, blindspotTbc: 31, coverageArea: 71.4 },
    { week: 'W43', actualIncidents: 1, hazard: 23120, rfidNonSupervisor: 13395, rfidSupervisor: 3980, tbc: 8440, goldenRules: 2, blindspotTbc: 34, coverageArea: 70.9 },
    { week: 'W44', actualIncidents: 2, hazard: 22840, rfidNonSupervisor: 13490, rfidSupervisor: 3920, tbc: 8335, goldenRules: 1, blindspotTbc: 39, coverageArea: 69.7 },
    { week: 'W45', actualIncidents: 2, hazard: 22680, rfidNonSupervisor: 13540, rfidSupervisor: 3895, tbc: 8280, goldenRules: 1, blindspotTbc: 43, coverageArea: 68.4 },
    { week: 'W46', actualIncidents: 3, hazard: 22490, rfidNonSupervisor: 13620, rfidSupervisor: 3845, tbc: 8210, goldenRules: 1, blindspotTbc: 46, coverageArea: 67.6 },
    { week: 'W47', actualIncidents: 1, hazard: 22720, rfidNonSupervisor: 13510, rfidSupervisor: 3905, tbc: 8290, goldenRules: 2, blindspotTbc: 37, coverageArea: 69.1 },
    { week: 'W48', actualIncidents: 1, hazard: 22950, rfidNonSupervisor: 13420, rfidSupervisor: 3940, tbc: 8355, goldenRules: 2, blindspotTbc: 35, coverageArea: 69.8 },
    { week: 'W49', actualIncidents: 0, hazard: 23280, rfidNonSupervisor: 13360, rfidSupervisor: 3995, tbc: 8480, goldenRules: 2, blindspotTbc: 32, coverageArea: 70.5 },
    { week: 'W50', actualIncidents: 1, hazard: 22600, rfidNonSupervisor: 13480, rfidSupervisor: 3875, tbc: 8190, goldenRules: 1, blindspotTbc: 42, coverageArea: 67.8 },
    { week: 'W51', actualIncidents: 0, hazard: 23540, rfidNonSupervisor: 13295, rfidSupervisor: 4048, tbc: 8595, goldenRules: 2, blindspotTbc: 29, coverageArea: 71.3 },
  ],

  
  BMO1: [
    { week: 'W40', actualIncidents: 0, hazard: 7210, rfidNonSupervisor: 3850, rfidSupervisor: 1228, tbc: 2520, goldenRules: 2, blindspotTbc: 9, coverageArea: 75.0 },
    { week: 'W41', actualIncidents: 1, hazard: 7050, rfidNonSupervisor: 3890, rfidSupervisor: 1200, tbc: 2440, goldenRules: 2, blindspotTbc: 11, coverageArea: 73.9 },
    { week: 'W42', actualIncidents: 0, hazard: 6970, rfidNonSupervisor: 3925, rfidSupervisor: 1188, tbc: 2400, goldenRules: 2, blindspotTbc: 12, coverageArea: 73.0 },
    { week: 'W43', actualIncidents: 1, hazard: 6900, rfidNonSupervisor: 3960, rfidSupervisor: 1168, tbc: 2370, goldenRules: 1, blindspotTbc: 14, coverageArea: 72.2 },
    { week: 'W44', actualIncidents: 1, hazard: 6840, rfidNonSupervisor: 3995, rfidSupervisor: 1149, tbc: 2345, goldenRules: 1, blindspotTbc: 16, coverageArea: 71.6 },
    { week: 'W45', actualIncidents: 2, hazard: 6760, rfidNonSupervisor: 4035, rfidSupervisor: 1128, tbc: 2310, goldenRules: 1, blindspotTbc: 17, coverageArea: 70.7 },
    { week: 'W46', actualIncidents: 2, hazard: 6715, rfidNonSupervisor: 4060, rfidSupervisor: 1110, tbc: 2295, goldenRules: 1, blindspotTbc: 18, coverageArea: 69.8 },
    { week: 'W47', actualIncidents: 1, hazard: 6860, rfidNonSupervisor: 4010, rfidSupervisor: 1150, tbc: 2350, goldenRules: 2, blindspotTbc: 15, coverageArea: 71.2 },
    { week: 'W48', actualIncidents: 0, hazard: 6965, rfidNonSupervisor: 3970, rfidSupervisor: 1174, tbc: 2395, goldenRules: 2, blindspotTbc: 13, coverageArea: 72.6 },
    { week: 'W49', actualIncidents: 0, hazard: 7055, rfidNonSupervisor: 3920, rfidSupervisor: 1191, tbc: 2425, goldenRules: 2, blindspotTbc: 11, coverageArea: 73.4 },
    { week: 'W50', actualIncidents: 1, hazard: 6795, rfidNonSupervisor: 4050, rfidSupervisor: 1122, tbc: 2305, goldenRules: 1, blindspotTbc: 17, coverageArea: 70.1 },
    { week: 'W51', actualIncidents: 0, hazard: 7140, rfidNonSupervisor: 3895, rfidSupervisor: 1210, tbc: 2475, goldenRules: 2, blindspotTbc: 10, coverageArea: 74.0 },
  ],


  
  BMO2: [
    { week: 'W40', actualIncidents: 0, hazard: 7210, rfidNonSupervisor: 3850, rfidSupervisor: 1228, tbc: 2520, goldenRules: 2, blindspotTbc: 9, coverageArea: 75.0 },
    { week: 'W41', actualIncidents: 1, hazard: 7050, rfidNonSupervisor: 3890, rfidSupervisor: 1200, tbc: 2440, goldenRules: 2, blindspotTbc: 11, coverageArea: 73.9 },
    { week: 'W42', actualIncidents: 0, hazard: 6970, rfidNonSupervisor: 3925, rfidSupervisor: 1188, tbc: 2400, goldenRules: 2, blindspotTbc: 12, coverageArea: 73.0 },
    { week: 'W43', actualIncidents: 1, hazard: 6900, rfidNonSupervisor: 3960, rfidSupervisor: 1168, tbc: 2370, goldenRules: 1, blindspotTbc: 14, coverageArea: 72.2 },
    { week: 'W44', actualIncidents: 1, hazard: 6840, rfidNonSupervisor: 3995, rfidSupervisor: 1149, tbc: 2345, goldenRules: 1, blindspotTbc: 16, coverageArea: 71.6 },
    { week: 'W45', actualIncidents: 2, hazard: 6760, rfidNonSupervisor: 4035, rfidSupervisor: 1128, tbc: 2310, goldenRules: 1, blindspotTbc: 17, coverageArea: 70.7 },
    { week: 'W46', actualIncidents: 2, hazard: 6715, rfidNonSupervisor: 4060, rfidSupervisor: 1110, tbc: 2295, goldenRules: 1, blindspotTbc: 18, coverageArea: 69.8 },
    { week: 'W47', actualIncidents: 1, hazard: 6860, rfidNonSupervisor: 4010, rfidSupervisor: 1150, tbc: 2350, goldenRules: 2, blindspotTbc: 15, coverageArea: 71.2 },
    { week: 'W48', actualIncidents: 0, hazard: 6965, rfidNonSupervisor: 3970, rfidSupervisor: 1174, tbc: 2395, goldenRules: 2, blindspotTbc: 13, coverageArea: 72.6 },
    { week: 'W49', actualIncidents: 0, hazard: 7055, rfidNonSupervisor: 3920, rfidSupervisor: 1191, tbc: 2425, goldenRules: 2, blindspotTbc: 11, coverageArea: 73.4 },
    { week: 'W50', actualIncidents: 1, hazard: 6795, rfidNonSupervisor: 4050, rfidSupervisor: 1122, tbc: 2305, goldenRules: 1, blindspotTbc: 17, coverageArea: 70.1 },
    { week: 'W51', actualIncidents: 0, hazard: 7140, rfidNonSupervisor: 3895, rfidSupervisor: 1210, tbc: 2475, goldenRules: 2, blindspotTbc: 10, coverageArea: 74.0 },
  ],


  BMO3: [
    { week: 'W40', actualIncidents: 0, hazard: 7210, rfidNonSupervisor: 3850, rfidSupervisor: 1228, tbc: 2520, goldenRules: 2, blindspotTbc: 9, coverageArea: 75.0 },
    { week: 'W41', actualIncidents: 1, hazard: 7050, rfidNonSupervisor: 3890, rfidSupervisor: 1200, tbc: 2440, goldenRules: 2, blindspotTbc: 11, coverageArea: 73.9 },
    { week: 'W42', actualIncidents: 0, hazard: 6970, rfidNonSupervisor: 3925, rfidSupervisor: 1188, tbc: 2400, goldenRules: 2, blindspotTbc: 12, coverageArea: 73.0 },
    { week: 'W43', actualIncidents: 1, hazard: 6900, rfidNonSupervisor: 3960, rfidSupervisor: 1168, tbc: 2370, goldenRules: 1, blindspotTbc: 14, coverageArea: 72.2 },
    { week: 'W44', actualIncidents: 1, hazard: 6840, rfidNonSupervisor: 3995, rfidSupervisor: 1149, tbc: 2345, goldenRules: 1, blindspotTbc: 16, coverageArea: 71.6 },
    { week: 'W45', actualIncidents: 2, hazard: 6760, rfidNonSupervisor: 4035, rfidSupervisor: 1128, tbc: 2310, goldenRules: 1, blindspotTbc: 17, coverageArea: 70.7 },
    { week: 'W46', actualIncidents: 2, hazard: 6715, rfidNonSupervisor: 4060, rfidSupervisor: 1110, tbc: 2295, goldenRules: 1, blindspotTbc: 18, coverageArea: 69.8 },
    { week: 'W47', actualIncidents: 1, hazard: 6860, rfidNonSupervisor: 4010, rfidSupervisor: 1150, tbc: 2350, goldenRules: 2, blindspotTbc: 15, coverageArea: 71.2 },
    { week: 'W48', actualIncidents: 0, hazard: 6965, rfidNonSupervisor: 3970, rfidSupervisor: 1174, tbc: 2395, goldenRules: 2, blindspotTbc: 13, coverageArea: 72.6 },
    { week: 'W49', actualIncidents: 0, hazard: 7055, rfidNonSupervisor: 3920, rfidSupervisor: 1191, tbc: 2425, goldenRules: 2, blindspotTbc: 11, coverageArea: 73.4 },
    { week: 'W50', actualIncidents: 1, hazard: 6795, rfidNonSupervisor: 4050, rfidSupervisor: 1122, tbc: 2305, goldenRules: 1, blindspotTbc: 17, coverageArea: 70.1 },
    { week: 'W51', actualIncidents: 0, hazard: 7140, rfidNonSupervisor: 3895, rfidSupervisor: 1210, tbc: 2475, goldenRules: 2, blindspotTbc: 10, coverageArea: 74.0 },
  ],



  LMO: [
    { week: 'W40', actualIncidents: 0, hazard: 7210, rfidNonSupervisor: 3850, rfidSupervisor: 1228, tbc: 2520, goldenRules: 2, blindspotTbc: 9, coverageArea: 75.0 },
    { week: 'W41', actualIncidents: 1, hazard: 7050, rfidNonSupervisor: 3890, rfidSupervisor: 1200, tbc: 2440, goldenRules: 2, blindspotTbc: 11, coverageArea: 73.9 },
    { week: 'W42', actualIncidents: 0, hazard: 6970, rfidNonSupervisor: 3925, rfidSupervisor: 1188, tbc: 2400, goldenRules: 2, blindspotTbc: 12, coverageArea: 73.0 },
    { week: 'W43', actualIncidents: 1, hazard: 6900, rfidNonSupervisor: 3960, rfidSupervisor: 1168, tbc: 2370, goldenRules: 1, blindspotTbc: 14, coverageArea: 72.2 },
    { week: 'W44', actualIncidents: 1, hazard: 6840, rfidNonSupervisor: 3995, rfidSupervisor: 1149, tbc: 2345, goldenRules: 1, blindspotTbc: 16, coverageArea: 71.6 },
    { week: 'W45', actualIncidents: 2, hazard: 6760, rfidNonSupervisor: 4035, rfidSupervisor: 1128, tbc: 2310, goldenRules: 1, blindspotTbc: 17, coverageArea: 70.7 },
    { week: 'W46', actualIncidents: 2, hazard: 6715, rfidNonSupervisor: 4060, rfidSupervisor: 1110, tbc: 2295, goldenRules: 1, blindspotTbc: 18, coverageArea: 69.8 },
    { week: 'W47', actualIncidents: 1, hazard: 6860, rfidNonSupervisor: 4010, rfidSupervisor: 1150, tbc: 2350, goldenRules: 2, blindspotTbc: 15, coverageArea: 71.2 },
    { week: 'W48', actualIncidents: 0, hazard: 6965, rfidNonSupervisor: 3970, rfidSupervisor: 1174, tbc: 2395, goldenRules: 2, blindspotTbc: 13, coverageArea: 72.6 },
    { week: 'W49', actualIncidents: 0, hazard: 7055, rfidNonSupervisor: 3920, rfidSupervisor: 1191, tbc: 2425, goldenRules: 2, blindspotTbc: 11, coverageArea: 73.4 },
    { week: 'W50', actualIncidents: 1, hazard: 6795, rfidNonSupervisor: 4050, rfidSupervisor: 1122, tbc: 2305, goldenRules: 1, blindspotTbc: 17, coverageArea: 70.1 },
    { week: 'W51', actualIncidents: 0, hazard: 7140, rfidNonSupervisor: 3895, rfidSupervisor: 1210, tbc: 2475, goldenRules: 2, blindspotTbc: 10, coverageArea: 74.0 },
  ],



  SMO: [
    { week: 'W40', actualIncidents: 0, hazard: 7945, rfidNonSupervisor: 4290, rfidSupervisor: 1335, tbc: 2790, goldenRules: 3, blindspotTbc: 8, coverageArea: 73.6 },
    { week: 'W41', actualIncidents: 0, hazard: 7890, rfidNonSupervisor: 4325, rfidSupervisor: 1318, tbc: 2765, goldenRules: 2, blindspotTbc: 9, coverageArea: 73.0 },
    { week: 'W42', actualIncidents: 1, hazard: 7750, rfidNonSupervisor: 4375, rfidSupervisor: 1290, tbc: 2705, goldenRules: 2, blindspotTbc: 11, coverageArea: 71.8 },
    { week: 'W43', actualIncidents: 1, hazard: 7640, rfidNonSupervisor: 4420, rfidSupervisor: 1268, tbc: 2670, goldenRules: 1, blindspotTbc: 13, coverageArea: 70.9 },
    { week: 'W44', actualIncidents: 2, hazard: 7520, rfidNonSupervisor: 4460, rfidSupervisor: 1245, tbc: 2620, goldenRules: 1, blindspotTbc: 14, coverageArea: 69.9 },
    { week: 'W45', actualIncidents: 2, hazard: 7445, rfidNonSupervisor: 4490, rfidSupervisor: 1226, tbc: 2590, goldenRules: 1, blindspotTbc: 16, coverageArea: 69.0 },
    { week: 'W46', actualIncidents: 3, hazard: 7380, rfidNonSupervisor: 4540, rfidSupervisor: 1208, tbc: 2560, goldenRules: 1, blindspotTbc: 18, coverageArea: 68.1 },
    { week: 'W47', actualIncidents: 1, hazard: 7540, rfidNonSupervisor: 4465, rfidSupervisor: 1250, tbc: 2635, goldenRules: 2, blindspotTbc: 14, coverageArea: 70.4 },
    { week: 'W48', actualIncidents: 1, hazard: 7630, rfidNonSupervisor: 4410, rfidSupervisor: 1278, tbc: 2675, goldenRules: 2, blindspotTbc: 12, coverageArea: 71.0 },
    { week: 'W49', actualIncidents: 0, hazard: 7755, rfidNonSupervisor: 4360, rfidSupervisor: 1298, tbc: 2710, goldenRules: 2, blindspotTbc: 11, coverageArea: 71.9 },
    { week: 'W50', actualIncidents: 1, hazard: 7460, rfidNonSupervisor: 4515, rfidSupervisor: 1216, tbc: 2585, goldenRules: 1, blindspotTbc: 16, coverageArea: 68.9 },
    { week: 'W51', actualIncidents: 0, hazard: 7830, rfidNonSupervisor: 4335, rfidSupervisor: 1310, tbc: 2750, goldenRules: 2, blindspotTbc: 9, coverageArea: 72.5 },
  ],


  GMO: [
    { week: 'W40', actualIncidents: 0, hazard: 7130, rfidNonSupervisor: 3890, rfidSupervisor: 1260, tbc: 2520, goldenRules: 2, blindspotTbc: 7, coverageArea: 71.5 },
    { week: 'W41', actualIncidents: 1, hazard: 7010, rfidNonSupervisor: 3920, rfidSupervisor: 1248, tbc: 2485, goldenRules: 2, blindspotTbc: 8, coverageArea: 70.8 },
    { week: 'W42', actualIncidents: 0, hazard: 6950, rfidNonSupervisor: 3960, rfidSupervisor: 1235, tbc: 2460, goldenRules: 2, blindspotTbc: 9, coverageArea: 70.0 },
    { week: 'W43', actualIncidents: 0, hazard: 6880, rfidNonSupervisor: 4010, rfidSupervisor: 1218, tbc: 2435, goldenRules: 2, blindspotTbc: 10, coverageArea: 69.6 },
    { week: 'W44', actualIncidents: 1, hazard: 6795, rfidNonSupervisor: 4035, rfidSupervisor: 1204, tbc: 2400, goldenRules: 1, blindspotTbc: 11, coverageArea: 68.8 },
    { week: 'W45', actualIncidents: 1, hazard: 6735, rfidNonSupervisor: 4065, rfidSupervisor: 1190, tbc: 2375, goldenRules: 1, blindspotTbc: 12, coverageArea: 68.2 },
    { week: 'W46', actualIncidents: 2, hazard: 6660, rfidNonSupervisor: 4095, rfidSupervisor: 1176, tbc: 2355, goldenRules: 1, blindspotTbc: 13, coverageArea: 67.4 },
    { week: 'W47', actualIncidents: 1, hazard: 6780, rfidNonSupervisor: 4040, rfidSupervisor: 1201, tbc: 2405, goldenRules: 2, blindspotTbc: 10, coverageArea: 68.9 },
    { week: 'W48', actualIncidents: 0, hazard: 6865, rfidNonSupervisor: 3995, rfidSupervisor: 1220, tbc: 2440, goldenRules: 2, blindspotTbc: 9, coverageArea: 69.7 },
    { week: 'W49', actualIncidents: 0, hazard: 6940, rfidNonSupervisor: 3960, rfidSupervisor: 1232, tbc: 2470, goldenRules: 2, blindspotTbc: 8, coverageArea: 70.1 },
    { week: 'W50', actualIncidents: 1, hazard: 6705, rfidNonSupervisor: 4088, rfidSupervisor: 1185, tbc: 2370, goldenRules: 1, blindspotTbc: 12, coverageArea: 67.9 },
    { week: 'W51', actualIncidents: 0, hazard: 7040, rfidNonSupervisor: 3925, rfidSupervisor: 1244, tbc: 2492, goldenRules: 2, blindspotTbc: 8, coverageArea: 70.8 },
  ],
};

const DEFAULT_LOOKBACK = 6;
const RIDGE_ALPHA = 1.2;
const DEFAULT_ALERT_THRESHOLD = 30;

function fmt(num, digits = 2) {
  if (!Number.isFinite(num)) return '-';
  return Number(num).toLocaleString('en-US', {
    minimumFractionDigits: digits,
    maximumFractionDigits: digits,
  });
}

function mean(values) {
  if (!values.length) return 0;
  return values.reduce((sum, value) => sum + value, 0) / values.length;
}

function std(values) {
  if (values.length <= 1) return 1;
  const avg = mean(values);
  const variance = values.reduce((sum, value) => sum + (value - avg) ** 2, 0) / (values.length - 1);
  return Math.sqrt(variance) || 1;
}

function median(values) {
  if (!values.length) return 0;
  const sorted = [...values].sort((a, b) => a - b);
  const middle = Math.floor(sorted.length / 2);
  return sorted.length % 2 === 0 ? (sorted[middle - 1] + sorted[middle]) / 2 : sorted[middle];
}

function quantile(values, q) {
  if (!values.length) return 0;
  const sorted = [...values].sort((a, b) => a - b);
  const position = clamp(q, 0, 1) * (sorted.length - 1);
  const base = Math.floor(position);
  const rest = position - base;
  if (sorted[base + 1] !== undefined) {
    return sorted[base] + rest * (sorted[base + 1] - sorted[base]);
  }
  return sorted[base];
}

function clamp(value, min, max) {
  return Math.min(max, Math.max(min, value));
}

function toFiniteNumber(value, fallback = 0) {
  const parsed = Number(value);
  return Number.isFinite(parsed) ? parsed : fallback;
}

function enrichRow(row) {
  const hazard = toFiniteNumber(row.hazard);
  const rfidNonSupervisor = toFiniteNumber(row.rfidNonSupervisor);
  const rfidSupervisor = Math.max(toFiniteNumber(row.rfidSupervisor), 0);
  const tbc = toFiniteNumber(row.tbc);
  const goldenRules = toFiniteNumber(row.goldenRules);
  const blindspotTbc = toFiniteNumber(row.blindspotTbc);
  const coverageArea = toFiniteNumber(row.coverageArea);
  const actualIncidents = Math.max(toFiniteNumber(row.actualIncidents), 0);
  const ratioNonToSupervisor = rfidSupervisor > 0 ? rfidNonSupervisor / rfidSupervisor : 0;

  return {
    ...row,
    hazard,
    rfidNonSupervisor,
    rfidSupervisor,
    tbc,
    goldenRules,
    blindspotTbc,
    coverageArea,
    actualIncidents,
    ratioNonToSupervisor,
  };
}

function getFeatureValue(row, key) {
  return toFiniteNumber(row[key], 0);
}

function solveLinearSystem(matrix, vector) {
  const n = matrix.length;
  const augmented = matrix.map((row, rowIndex) => [...row, vector[rowIndex]]);

  for (let col = 0; col < n; col += 1) {
    let pivotRow = col;
    for (let row = col + 1; row < n; row += 1) {
      if (Math.abs(augmented[row][col]) > Math.abs(augmented[pivotRow][col])) {
        pivotRow = row;
      }
    }

    if (Math.abs(augmented[pivotRow][col]) < 1e-10) {
      augmented[col][col] = 1e-10;
    } else if (pivotRow !== col) {
      [augmented[col], augmented[pivotRow]] = [augmented[pivotRow], augmented[col]];
    }

    const pivot = augmented[col][col];
    for (let j = col; j <= n; j += 1) {
      augmented[col][j] /= pivot;
    }

    for (let row = 0; row < n; row += 1) {
      if (row === col) continue;
      const factor = augmented[row][col];
      for (let j = col; j <= n; j += 1) {
        augmented[row][j] -= factor * augmented[col][j];
      }
    }
  }

  return augmented.map((row) => row[n]);
}

function fitStatisticalModel(rows) {
  const enrichedRows = rows.map(enrichRow);
  const featureKeys = FEATURE_META.map((feature) => feature.key);
  const xRaw = enrichedRows.map((row) => featureKeys.map((key) => getFeatureValue(row, key)));
  const yRaw = enrichedRows.map((row) => row.actualIncidents);

  const xMeans = featureKeys.map((_, index) => mean(xRaw.map((row) => row[index])));
  const xStds = featureKeys.map((_, index) => {
    const value = std(xRaw.map((row) => row[index]));
    return value > 0 ? value : 1;
  });
  const yMean = mean(yRaw);
  const yStd = std(yRaw) || 1;

  const xStandardized = xRaw.map((row) => row.map((value, index) => (value - xMeans[index]) / xStds[index]));
  const yStandardized = yRaw.map((value) => (value - yMean) / yStd);

  const dimension = featureKeys.length;
  const xtx = Array.from({ length: dimension }, () => Array.from({ length: dimension }, () => 0));
  const xty = Array.from({ length: dimension }, () => 0);

  for (let i = 0; i < xStandardized.length; i += 1) {
    for (let j = 0; j < dimension; j += 1) {
      xty[j] += xStandardized[i][j] * yStandardized[i];
      for (let k = 0; k < dimension; k += 1) {
        xtx[j][k] += xStandardized[i][j] * xStandardized[i][k];
      }
    }
  }

  for (let i = 0; i < dimension; i += 1) {
    xtx[i][i] += RIDGE_ALPHA;
  }

  const betas = solveLinearSystem(xtx, xty).map((value) => (Number.isFinite(value) ? value : 0));

  function predict(rawRow) {
    const enrichedRow = enrichRow(rawRow);
    const standardizedFeatures = featureKeys.map((key, index) => (getFeatureValue(enrichedRow, key) - xMeans[index]) / xStds[index]);
    const yStandardizedHat = standardizedFeatures.reduce((sum, value, index) => sum + value * betas[index], 0);
    const predictedIncidents = Math.max(yMean + yStandardizedHat * yStd, 0);

    return {
      predictedIncidents,
      standardizedFeatures,
      enrichedRow,
    };
  }

  const fitted = enrichedRows.map((row) => {
    const prediction = predict(row);
    return {
      ...row,
      predictedIncidents: prediction.predictedIncidents,
      standardizedFeatures: prediction.standardizedFeatures,
    };
  });

  const fittedPredictions = fitted.map((row) => row.predictedIncidents);
  const minPred = Math.min(...fittedPredictions);
  const maxPred = Math.max(...fittedPredictions);
  const scoreRange = maxPred - minPred || 1;
  const scoreThresholdYellow = quantile(fittedPredictions, 0.5);
  const scoreThresholdRed = quantile(fittedPredictions, 0.8);

  function predictedToScore(predictedIncidents) {
    return clamp(((predictedIncidents - minPred) / scoreRange) * 100, 0, 100);
  }

  function predictedToStatus(predictedIncidents) {
    if (predictedIncidents >= scoreThresholdRed) return 'Merah';
    if (predictedIncidents >= scoreThresholdYellow) return 'Kuning';
    return 'Hijau';
  }

  return {
    featureKeys,
    xMeans,
    xStds,
    yMean,
    yStd,
    betas,
    fitted,
    predict,
    predictedToScore,
    predictedToStatus,
    scoreThresholdYellow,
    scoreThresholdRed,
  };
}

function getBaselineWindow(rows, selectedIndex, lookback) {
  const safeIndex = selectedIndex >= 0 ? selectedIndex : rows.length - 1;
  const safeLookback = clamp(Math.floor(toFiniteNumber(lookback, DEFAULT_LOOKBACK)), 2, Math.max(rows.length - 1, 2));
  const start = Math.max(0, safeIndex - safeLookback);
  let baselineRows = rows.slice(start, safeIndex);

  if (baselineRows.length >= 2) {
    return baselineRows;
  }

  baselineRows = rows.filter((_, index) => index !== safeIndex).slice(0, Math.min(safeLookback, Math.max(rows.length - 1, 0)));
  return baselineRows;
}

function computeBaselineStats(rows, selectedIndex, lookback) {
  const baselineRows = getBaselineWindow(rows, selectedIndex, lookback).map(enrichRow);
  const byFeature = FEATURE_META.reduce((accumulator, feature) => {
    const values = baselineRows.map((row) => getFeatureValue(row, feature.key));
    accumulator[feature.key] = {
      mean: mean(values),
      median: median(values),
      std: std(values) || 1,
    };
    return accumulator;
  }, {});

  return { baselineRows, byFeature };
}

function computeContributionHistory(rows, model, lookback) {
  const history = FEATURE_META.reduce((accumulator, feature) => {
    accumulator[feature.key] = [];
    return accumulator;
  }, {});

  rows.forEach((row, rowIndex) => {
    const baselineStats = computeBaselineStats(rows, rowIndex, lookback);
    FEATURE_META.forEach((feature, featureIndex) => {
      const stats = baselineStats.byFeature[feature.key];
      const zScore = stats.std ? (getFeatureValue(row, feature.key) - stats.mean) / stats.std : 0;
      const contribution = zScore * model.betas[featureIndex];
      history[feature.key].push(Number.isFinite(contribution) ? contribution : 0);
    });
  });

  return history;
}

function contributionStatus(contribution, historyValues) {
  const safeContribution = Number.isFinite(contribution) ? contribution : 0;
  const positiveHistory = historyValues.filter((value) => value > 0);
  const yellowCutoff = positiveHistory.length ? quantile(positiveHistory, 0.5) : 0;
  const redCutoff = positiveHistory.length ? quantile(positiveHistory, 0.8) : 0;

  if (safeContribution <= 0) return { color: 'green', label: 'Hijau' };
  if (safeContribution >= redCutoff && redCutoff > 0) return { color: 'red', label: 'Merah' };
  if (safeContribution >= yellowCutoff && yellowCutoff > 0) return { color: 'yellow', label: 'Kuning' };
  return { color: 'yellow', label: 'Kuning' };
}

function averageRanks(values) {
  const indexed = values.map((value, index) => ({ value, index })).sort((a, b) => a.value - b.value);
  const ranks = new Array(values.length).fill(0);
  let i = 0;

  while (i < indexed.length) {
    let j = i;
    while (j < indexed.length && indexed[j].value === indexed[i].value) {
      j += 1;
    }
    const averageRank = (i + j - 1) / 2 + 1;
    for (let k = i; k < j; k += 1) {
      ranks[indexed[k].index] = averageRank;
    }
    i = j;
  }

  return ranks;
}

function pearsonCorrelation(xs, ys) {
  if (!xs.length || xs.length !== ys.length) return 0;
  const meanX = mean(xs);
  const meanY = mean(ys);
  let numerator = 0;
  let denominatorX = 0;
  let denominatorY = 0;

  for (let i = 0; i < xs.length; i += 1) {
    const dx = xs[i] - meanX;
    const dy = ys[i] - meanY;
    numerator += dx * dy;
    denominatorX += dx * dx;
    denominatorY += dy * dy;
  }

  if (denominatorX === 0 || denominatorY === 0) return 0;
  return numerator / Math.sqrt(denominatorX * denominatorY);
}

function spearmanCorrelation(xs, ys) {
  if (!xs.length || xs.length !== ys.length) return 0;
  return pearsonCorrelation(averageRanks(xs), averageRanks(ys));
}

function aucBinary(scores, labels) {
  if (!scores.length || scores.length !== labels.length) return 0.5;
  const positives = labels.filter((label) => label === 1).length;
  const negatives = labels.filter((label) => label === 0).length;
  if (positives === 0 || negatives === 0) return 0.5;

  const ranks = averageRanks(scores);
  let positiveRankSum = 0;
  for (let i = 0; i < labels.length; i += 1) {
    if (labels[i] === 1) {
      positiveRankSum += ranks[i];
    }
  }

  return (positiveRankSum - (positives * (positives + 1)) / 2) / (positives * negatives);
}

function confusionMetrics(scores, incidents, threshold) {
  const labels = incidents.map((value) => (value >= 1 ? 1 : 0));
  let tp = 0;
  let tn = 0;
  let fp = 0;
  let fn = 0;

  for (let i = 0; i < scores.length; i += 1) {
    const predicted = scores[i] >= threshold ? 1 : 0;
    const actual = labels[i];
    if (predicted === 1 && actual === 1) tp += 1;
    if (predicted === 0 && actual === 0) tn += 1;
    if (predicted === 1 && actual === 0) fp += 1;
    if (predicted === 0 && actual === 1) fn += 1;
  }

  const total = tp + tn + fp + fn;
  const accuracy = total ? (tp + tn) / total : 0;
  const precision = tp + fp ? tp / (tp + fp) : 0;
  const recall = tp + fn ? tp / (tp + fn) : 0;
  const specificity = tn + fp ? tn / (tn + fp) : 0;
  const f1 = precision + recall ? (2 * precision * recall) / (precision + recall) : 0;

  return {
    tp,
    tn,
    fp,
    fn,
    accuracy,
    precision,
    recall,
    specificity,
    f1,
    auc: aucBinary(scores, labels),
  };
}

function badgeClass(color) {
  if (color === 'red') return 'bg-red-100 text-red-700 border-red-200';
  if (color === 'yellow') return 'bg-amber-100 text-amber-700 border-amber-200';
  return 'bg-emerald-100 text-emerald-700 border-emerald-200';
}

function statusColor(status) {
  if (status === 'Merah') return 'red';
  if (status === 'Kuning') return 'yellow';
  return 'green';
}

function chartStatusColor(status) {
  if (status === 'Merah') return '#ef4444';
  if (status === 'Kuning') return '#f59e0b';
  return '#10b981';
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function inputClass(extra = '') {
  return `flex h-10 w-full rounded-xl border border-outline-variant/30 bg-[#f8fafc] px-3 py-2 text-sm text-on-surface shadow-inner outline-none transition placeholder:text-on-surface-variant focus:border-primary/40 focus:ring-2 focus:ring-primary/20 ${extra}`;
}

function card(content, extra = '') {
  return `<div class="anchored-card rounded-2xl bg-white ${extra}">${content}</div>`;
}

function cardTitle(title, extra = '') {
  return `<div class="px-6 pt-6 pb-4"><h3 class="font-headline text-xl font-bold text-on-surface ${extra}">${title}</h3></div>`;
}

function badge(status) {
  const color = typeof status === 'string' ? statusColor(status) : status.color;
  const label = typeof status === 'string' ? status : status.label;
  return `<span class="inline-flex items-center rounded-full border px-3 py-1 text-sm font-medium ${badgeClass(color)}">${escapeHtml(label)}</span>`;
}

function solveLinearSystem(matrix, vector) {
  const n = matrix.length;
  const augmented = matrix.map((row, rowIndex) => [...row, vector[rowIndex]]);

  for (let col = 0; col < n; col += 1) {
    let pivotRow = col;
    for (let row = col + 1; row < n; row += 1) {
      if (Math.abs(augmented[row][col]) > Math.abs(augmented[pivotRow][col])) {
        pivotRow = row;
      }
    }

    if (Math.abs(augmented[pivotRow][col]) < 1e-10) {
      augmented[col][col] = 1e-10;
    } else if (pivotRow !== col) {
      [augmented[col], augmented[pivotRow]] = [augmented[pivotRow], augmented[col]];
    }

    const pivot = augmented[col][col];
    for (let j = col; j <= n; j += 1) {
      augmented[col][j] /= pivot;
    }

    for (let row = 0; row < n; row += 1) {
      if (row === col) continue;
      const factor = augmented[row][col];
      for (let j = col; j <= n; j += 1) {
        augmented[row][j] -= factor * augmented[col][j];
      }
    }
  }

  return augmented.map((row) => row[n]);
}

function fitStatisticalModel(rows) {
  const enrichedRows = rows.map(enrichRow);
  const featureKeys = FEATURE_META.map((feature) => feature.key);
  const xRaw = enrichedRows.map((row) => featureKeys.map((key) => getFeatureValue(row, key)));
  const yRaw = enrichedRows.map((row) => row.actualIncidents);

  const xMeans = featureKeys.map((_, index) => mean(xRaw.map((row) => row[index])));
  const xStds = featureKeys.map((_, index) => {
    const value = std(xRaw.map((row) => row[index]));
    return value > 0 ? value : 1;
  });
  const yMean = mean(yRaw);
  const yStd = std(yRaw) || 1;

  const xStandardized = xRaw.map((row) => row.map((value, index) => (value - xMeans[index]) / xStds[index]));
  const yStandardized = yRaw.map((value) => (value - yMean) / yStd);

  const dimension = featureKeys.length;
  const xtx = Array.from({ length: dimension }, () => Array.from({ length: dimension }, () => 0));
  const xty = Array.from({ length: dimension }, () => 0);

  for (let i = 0; i < xStandardized.length; i += 1) {
    for (let j = 0; j < dimension; j += 1) {
      xty[j] += xStandardized[i][j] * yStandardized[i];
      for (let k = 0; k < dimension; k += 1) {
        xtx[j][k] += xStandardized[i][j] * xStandardized[i][k];
      }
    }
  }

  for (let i = 0; i < dimension; i += 1) {
    xtx[i][i] += RIDGE_ALPHA;
  }

  const betas = solveLinearSystem(xtx, xty).map((value) => (Number.isFinite(value) ? value : 0));

  function predict(rawRow) {
    const enrichedRow = enrichRow(rawRow);
    const standardizedFeatures = featureKeys.map((key, index) => (getFeatureValue(enrichedRow, key) - xMeans[index]) / xStds[index]);
    const yStandardizedHat = standardizedFeatures.reduce((sum, value, index) => sum + value * betas[index], 0);
    const predictedIncidents = Math.max(yMean + yStandardizedHat * yStd, 0);

    return {
      predictedIncidents,
      standardizedFeatures,
      enrichedRow,
    };
  }

  const fitted = enrichedRows.map((row) => {
    const prediction = predict(row);
    return {
      ...row,
      predictedIncidents: prediction.predictedIncidents,
      standardizedFeatures: prediction.standardizedFeatures,
    };
  });

  const fittedPredictions = fitted.map((row) => row.predictedIncidents);
  const minPred = Math.min(...fittedPredictions);
  const maxPred = Math.max(...fittedPredictions);
  const scoreRange = maxPred - minPred || 1;
  const scoreThresholdYellow = quantile(fittedPredictions, 0.5);
  const scoreThresholdRed = quantile(fittedPredictions, 0.8);

  function predictedToScore(predictedIncidents) {
    return clamp(((predictedIncidents - minPred) / scoreRange) * 100, 0, 100);
  }

  function predictedToStatus(predictedIncidents) {
    if (predictedIncidents >= scoreThresholdRed) return 'Merah';
    if (predictedIncidents >= scoreThresholdYellow) return 'Kuning';
    return 'Hijau';
  }

  return {
    featureKeys,
    xMeans,
    xStds,
    yMean,
    yStd,
    betas,
    fitted,
    predict,
    predictedToScore,
    predictedToStatus,
    scoreThresholdYellow,
    scoreThresholdRed,
  };
}

function getBaselineWindow(rows, selectedIndex, lookback) {
  const safeIndex = selectedIndex >= 0 ? selectedIndex : rows.length - 1;
  const safeLookback = clamp(Math.floor(toFiniteNumber(lookback, DEFAULT_LOOKBACK)), 2, Math.max(rows.length - 1, 2));
  const start = Math.max(0, safeIndex - safeLookback);
  let baselineRows = rows.slice(start, safeIndex);

  if (baselineRows.length >= 2) return baselineRows;

  baselineRows = rows.filter((_, index) => index !== safeIndex).slice(0, Math.min(safeLookback, Math.max(rows.length - 1, 0)));
  return baselineRows;
}

function computeBaselineStats(rows, selectedIndex, lookback) {
  const baselineRows = getBaselineWindow(rows, selectedIndex, lookback).map(enrichRow);
  const byFeature = FEATURE_META.reduce((accumulator, feature) => {
    const values = baselineRows.map((row) => getFeatureValue(row, feature.key));
    accumulator[feature.key] = {
      mean: mean(values),
      median: median(values),
      std: std(values) || 1,
    };
    return accumulator;
  }, {});

  return { baselineRows, byFeature };
}

function computeContributionHistory(rows, model, lookback) {
  const history = FEATURE_META.reduce((accumulator, feature) => {
    accumulator[feature.key] = [];
    return accumulator;
  }, {});

  rows.forEach((row, rowIndex) => {
    const baselineStats = computeBaselineStats(rows, rowIndex, lookback);
    FEATURE_META.forEach((feature, featureIndex) => {
      const stats = baselineStats.byFeature[feature.key];
      const zScore = stats.std ? (getFeatureValue(row, feature.key) - stats.mean) / stats.std : 0;
      const contribution = zScore * model.betas[featureIndex];
      history[feature.key].push(Number.isFinite(contribution) ? contribution : 0);
    });
  });

  return history;
}

function contributionStatus(contribution, historyValues) {
  const safeContribution = Number.isFinite(contribution) ? contribution : 0;
  const positiveHistory = historyValues.filter((value) => value > 0);
  const yellowCutoff = positiveHistory.length ? quantile(positiveHistory, 0.5) : 0;
  const redCutoff = positiveHistory.length ? quantile(positiveHistory, 0.8) : 0;

  if (safeContribution <= 0) return { color: 'green', label: 'Hijau' };
  if (safeContribution >= redCutoff && redCutoff > 0) return { color: 'red', label: 'Merah' };
  if (safeContribution >= yellowCutoff && yellowCutoff > 0) return { color: 'yellow', label: 'Kuning' };
  return { color: 'yellow', label: 'Kuning' };
}

let appState = {
  page: 'dashboard',
  site: 'All Site',
  selectedWeek: 'W50',
  lookback: DEFAULT_LOOKBACK,
  alertThreshold: DEFAULT_ALERT_THRESHOLD,
  actualOverride: null,
};

let dashboardChart = null;
let accuracyChart = null;

function getDefaultWeek(site) {
  const rows = SITE_ROWS[site] || [];
  const row = rows[rows.length - 2] || rows[rows.length - 1];
  return row ? row.week : '';
}

appState.selectedWeek = getDefaultWeek(appState.site);

function deriveData() {
  const sites = Object.keys(SITE_ROWS);
  const siteRows = (SITE_ROWS[appState.site] || []).map(enrichRow);
  const model = fitStatisticalModel(siteRows);
  const selectedIndex = siteRows.findIndex((row) => row.week === appState.selectedWeek);
  const safeSelectedIndex = selectedIndex >= 0 ? selectedIndex : Math.max(siteRows.length - 1, 0);
  const selectedBaseRow = siteRows[safeSelectedIndex] || siteRows[siteRows.length - 1] || null;
  const actualRow = selectedBaseRow ? (appState.actualOverride ? enrichRow({ ...selectedBaseRow, ...appState.actualOverride }) : selectedBaseRow) : null;

  const baselineStats = computeBaselineStats(siteRows, safeSelectedIndex, appState.lookback);
  const contributionHistory = computeContributionHistory(siteRows, model, appState.lookback);

  const weeklySeries = siteRows.map((row, rowIndex) => {
    const prediction = model.predict(row);
    const score = model.predictedToScore(prediction.predictedIncidents);
    const status = model.predictedToStatus(prediction.predictedIncidents);
    const rollingBaseline = computeBaselineStats(siteRows, rowIndex, appState.lookback);
    const contributions = FEATURE_META.map((feature, featureIndex) => {
      const stats = rollingBaseline.byFeature[feature.key];
      const zScore = stats.std ? (getFeatureValue(row, feature.key) - stats.mean) / stats.std : 0;
      return {
        key: feature.key,
        contribution: zScore * model.betas[featureIndex],
      };
    }).sort((a, b) => b.contribution - a.contribution);

    const topDriverKey = contributions[0] ? contributions[0].key : null;
    const topDriver = FEATURE_META.find((feature) => feature.key === topDriverKey)?.label || 'Terkendali';

    return {
      week: row.week,
      score,
      status,
      actualIncidents: row.actualIncidents,
      predictedIncidents: prediction.predictedIncidents,
      topDriver,
      fill: chartStatusColor(status),
    };
  });

  let analysis = {
    score: 0,
    status: 'Hijau',
    predictedIncidents: 0,
    indicators: [],
    coefficientTable: [],
    topDrivers: [],
    actionPriority: [],
    narrative: 'Tidak ada data minggu terpilih.',
  };

  if (actualRow) {
    const prediction = model.predict(actualRow);
    const score = model.predictedToScore(prediction.predictedIncidents);
    const status = model.predictedToStatus(prediction.predictedIncidents);

    const indicators = FEATURE_META.map((feature, featureIndex) => {
      const stats = baselineStats.byFeature[feature.key];
      const actualValue = getFeatureValue(actualRow, feature.key);
      const zScore = stats.std ? (actualValue - stats.mean) / stats.std : 0;
      const beta = model.betas[featureIndex];
      const contribution = zScore * beta;
      const contributionState = contributionStatus(contribution, contributionHistory[feature.key] || []);
      return {
        ...feature,
        actual: actualValue,
        baselineMean: stats.mean,
        baselineMedian: stats.median,
        baselineStd: stats.std,
        zScore,
        beta,
        contribution,
        direction: contribution >= 0 ? 'Menaikkan predicted risk' : 'Menurunkan predicted risk',
        status: contributionState,
      };
    }).sort((a, b) => b.contribution - a.contribution);

    const topDrivers = indicators.filter((item) => item.contribution > 0).slice(0, 3);
    const topProtectors = indicators.filter((item) => item.contribution < 0).sort((a, b) => a.contribution - b.contribution).slice(0, 2);
    const coefficientTable = FEATURE_META.map((feature, index) => ({ ...feature, beta: model.betas[index] }))
      .sort((a, b) => Math.abs(b.beta) - Math.abs(a.beta));

    const narrativeParts = [
      `Pada ${appState.site} minggu ${appState.selectedWeek}, model statistik menghasilkan predicted incident level sebesar ${fmt(prediction.predictedIncidents, 2)} dengan score ${fmt(score, 1)} dan status ${status.toLowerCase()}.`,
      `Reference minggu ini dihitung dari rolling baseline ${baselineStats.baselineRows.length} minggu sebelumnya, dengan pendekatan mean dan standard deviation per indikator.`,
    ];

    if (actualRow.actualIncidents > 0) {
      narrativeParts.push(`Insiden aktual tercatat ${fmt(actualRow.actualIncidents, 0)}, sehingga pembacaan difokuskan pada kontributor statistik yang mendorong predicted risk ke arah positif.`);
    } else {
      narrativeParts.push('Belum ada insiden aktual, namun score tetap dibaca sebagai tekanan risiko relatif terhadap pola historis site.');
    }

    if (topDrivers.length) {
      narrativeParts.push(`Kontributor risiko terbesar minggu ini adalah ${topDrivers.map((item) => item.label).join(', ')}.`);
    }

    if (topProtectors.length) {
      narrativeParts.push(`Sementara itu, faktor yang masih menahan kenaikan risiko adalah ${topProtectors.map((item) => item.label).join(', ')}.`);
    }

    analysis = {
      score,
      status,
      predictedIncidents: prediction.predictedIncidents,
      indicators,
      coefficientTable,
      topDrivers,
      actionPriority: topDrivers.map((item, index) => `${index + 1}. ${item.label} — kontribusi ${fmt(item.contribution, 2)} pada model, dengan deviasi z-score ${fmt(item.zScore, 2)} dari rolling baseline.`),
      narrative: narrativeParts.join(' '),
    };
  }

  const summaryTrend = weeklySeries.length ? {
    avg: mean(weeklySeries.map((row) => row.score)),
    peak: weeklySeries.reduce((max, row) => (row.score > max.score ? row : max), weeklySeries[0]),
    latest: weeklySeries[weeklySeries.length - 1],
  } : {
    avg: 0,
    peak: { week: '-', score: 0, status: 'Hijau' },
    latest: { week: '-', score: 0, status: 'Hijau' },
  };

  const sameWeekScores = weeklySeries.map((row) => row.score);
  const sameWeekIncidents = weeklySeries.map((row) => row.actualIncidents);
  const nextWeekScores = weeklySeries.slice(0, -1).map((row) => row.score);
  const nextWeekIncidents = weeklySeries.slice(1).map((row) => row.actualIncidents);

  const validationMetrics = {
    sameWeek: {
      pearson: pearsonCorrelation(sameWeekScores, sameWeekIncidents),
      spearman: spearmanCorrelation(sameWeekScores, sameWeekIncidents),
      ...confusionMetrics(sameWeekScores, sameWeekIncidents, appState.alertThreshold),
    },
    nextWeek: {
      pearson: pearsonCorrelation(nextWeekScores, nextWeekIncidents),
      spearman: spearmanCorrelation(nextWeekScores, nextWeekIncidents),
      ...confusionMetrics(nextWeekScores, nextWeekIncidents, appState.alertThreshold),
    },
  };

  return {
    sites,
    siteRows,
    model,
    selectedBaseRow,
    actualRow,
    baselineStats,
    contributionHistory,
    weeklySeries,
    analysis,
    summaryTrend,
    validationMetrics,
  };
}

function statBox(label, value, sub, dark = false) {
  return `<div class="rounded-2xl p-5 ${dark ? 'bg-primary text-on-primary' : 'anchored-card bg-white text-on-surface'}">
    <div class="text-xs uppercase tracking-[0.2em] ${dark ? 'text-on-primary/80' : 'text-on-surface-variant'}">${escapeHtml(label)}</div>
    <div class="mt-2 font-headline text-3xl font-bold">${value}</div>
    <div class="mt-1 text-sm ${dark ? 'text-on-primary/85' : 'text-on-surface-variant'}">${escapeHtml(sub)}</div>
  </div>`;
}

function renderHeader(data) {
  return `
  <div class="anchored-card flex flex-col gap-4 rounded-2xl bg-white p-6 md:flex-row md:items-end md:justify-between">
    <div>
      <nav class="mb-2 flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-on-surface-variant">
        <span>Dashboard</span>
        <span class="material-symbols-outlined text-xs">chevron_right</span>
        <span class="text-primary">Tools Analisis</span>
      </nav>
      <div class="text-sm font-medium uppercase tracking-[0.2em] text-on-surface-variant">Incident Back Analysis</div>
      <h1 class="font-headline mt-2 text-2xl font-extrabold tracking-tight text-on-background sm:text-3xl">Fully Statistical Back Analysis Tool</h1>
     
    </div>
    <div class="flex flex-col items-start gap-3 md:items-end">
      <div class="flex flex-wrap gap-1 rounded-xl border border-outline-variant/25 bg-[#f8fafc] p-1">
        <button data-action="page" data-page="dashboard" class="rounded-lg px-3 py-2 text-sm font-bold transition sm:px-4 ${appState.page === 'dashboard' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant hover:bg-white/80'}">Analysis Dashboard</button>
        <button data-action="page" data-page="accuracy" class="rounded-lg px-3 py-2 text-sm font-bold transition sm:px-4 ${appState.page === 'accuracy' ? 'bg-white text-primary shadow-sm' : 'text-on-surface-variant hover:bg-white/80'}">Accuracy Check</button>
      </div>
      <button data-action="reset" type="button" class="rounded-xl bg-primary px-4 py-2 text-sm font-bold text-on-primary shadow-sm transition hover:bg-primary-dim">Reset Filter</button>
    </div>
  </div>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
    ${card(`<div class="p-5">
      <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">Site</div>
      <div class="mt-3">
        <select data-control="site" class="${inputClass()}">
          ${data.sites.map((site) => `<option value="${escapeHtml(site)}" ${site === appState.site ? 'selected' : ''}>${escapeHtml(site)}</option>`).join('')}
        </select>
      </div>
    </div>`)}
    ${card(`<div class="p-5">
      <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">Lookback baseline</div>
      <div class="mt-3">
        <input data-control="lookback" type="number" min="2" max="10" value="${appState.lookback}" class="${inputClass()}">
      </div>
      <div class="mt-2 text-xs text-on-surface-variant">Jumlah minggu historis untuk rolling reference.</div>
    </div>`)}
    ${card(`<div class="p-5">
      <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">Alert threshold</div>
      <div class="mt-3">
        <input data-control="alertThreshold" type="number" min="0" max="100" value="${appState.alertThreshold}" class="${inputClass()}">
      </div>
      <div class="mt-2 text-xs text-on-surface-variant">Dipakai hanya untuk klasifikasi di accuracy check.</div>
    </div>`)}
    ${card(`<div class="p-5">
      <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">Average score</div>
      <div class="mt-2 text-3xl font-semibold text-on-surface">${fmt(data.summaryTrend.avg,1)}</div>
      <div class="mt-1 text-sm text-on-surface-variant">Mean statistical risk score</div>
    </div>`)}
    ${card(`<div class="p-5">
      <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">Peak week</div>
      <div class="mt-2 text-3xl font-semibold text-on-surface">${escapeHtml(data.summaryTrend.peak.week)}</div>
      <div class="mt-1 text-sm text-on-surface-variant">Score ${fmt(data.summaryTrend.peak.score,1)} · ${escapeHtml(data.summaryTrend.peak.status)}</div>
    </div>`)}
    ${card(`<div class="p-5">
      <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">Latest week</div>
      <div class="mt-2 text-3xl font-semibold text-on-surface">${escapeHtml(data.summaryTrend.latest.week)}</div>
      <div class="mt-1 text-sm text-on-surface-variant">Score ${fmt(data.summaryTrend.latest.score,1)} · ${escapeHtml(data.summaryTrend.latest.status)}</div>
    </div>`)}
  </div>`;
}

function renderTopDrivers(analysis) {
  if (!analysis.topDrivers.length) {
    return `<div class="rounded-2xl bg-emerald-50 p-5 text-sm text-emerald-700 ring-1 ring-emerald-200">
      Tidak ada kontribusi positif besar pada minggu ini. Indikator utama cenderung netral atau protektif terhadap predicted risk.
    </div>`;
  }
  return analysis.topDrivers.map((item) => `
    <div class="flex items-start gap-3 rounded-2xl bg-[#f8fafc] p-4 border border-outline-variant/20">
      <div class="rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-on-surface border border-outline-variant/20">${escapeHtml(item.label.slice(0,2).toUpperCase())}</div>
      <div>
        <div class="font-medium text-on-surface">${escapeHtml(item.label)}</div>
        <div class="mt-1 text-sm text-on-surface-variant">β = ${fmt(item.beta,2)} · z = ${fmt(item.zScore,2)} · contribution = ${fmt(item.contribution,2)}</div>
        <div class="mt-1 text-sm text-on-surface-variant">${escapeHtml(item.description)}</div>
      </div>
    </div>
  `).join('');
}

function renderDashboard(data) {
  const actualRow = data.actualRow || {};
  return `
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.15fr_0.85fr]">
      ${card(cardTitle('Statistical score trend per week') + `<div class="space-y-6 px-6 pb-6">
        <div class="h-[260px] w-full min-h-0 rounded-2xl bg-[#f8fafc] p-4 border border-outline-variant/20 sm:h-[320px] lg:h-[340px]"><canvas id="dashboardTrendChart"></canvas></div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
          ${statBox('Predicted incident', fmt(data.analysis.predictedIncidents,2), 'Fitted from standardized ridge model')}
          ${statBox('Score', fmt(data.analysis.score,1), 'Normalized from model prediction')}
          <div class="rounded-2xl bg-white p-5 border border-outline-variant/20">
            <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">Status</div>
            <div class="mt-3">${badge(data.analysis.status)}</div>
            <div class="mt-3 text-sm text-on-surface-variant">Derived from site-specific score quantiles</div>
          </div>
        </div>
      </div>`)}
      ${card(cardTitle('Top statistical drivers') + `<div class="space-y-3 px-6 pb-6">${renderTopDrivers(data.analysis)}</div>`)}
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.1fr_0.9fr]">
      ${card(cardTitle('Selected week and rolling reference') + `<div class="space-y-6 px-6 pb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
          <div class="space-y-2">
            <label class="text-sm font-medium text-on-surface">Week</label>
            <select data-control="selectedWeek" class="${inputClass()}">
              ${(SITE_ROWS[appState.site] || []).map((row) => `<option value="${escapeHtml(row.week)}" ${row.week === appState.selectedWeek ? 'selected' : ''}>${escapeHtml(row.week)}</option>`).join('')}
            </select>
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium text-on-surface">Site</label>
            <input value="${escapeHtml(appState.site)}" readonly class="${inputClass('bg-[#f8fafc]')}" />
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium text-on-surface">Actual Incidents</label>
            <input data-field="actualIncidents" type="number" value="${actualRow.actualIncidents ?? 0}" class="${inputClass()}" />
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          ${card(`<div class="px-6 pt-6 pb-4"><h4 class="text-base font-semibold text-on-surface">Actual values</h4></div>
            <div class="grid grid-cols-1 gap-4 px-6 pb-6 sm:grid-cols-2">
              ${[
                ['hazard', 'Pelaporan Hazard'],
                ['rfidSupervisor', 'RFID Pengawas'],
                ['tbc', 'Pelaporan TBC'],
                ['goldenRules', 'Golden Rules'],
                ['blindspotTbc', 'Blindspot TBC'],
                ['coverageArea', 'Daily Coverage Area (%)'],
                ['rfidNonSupervisor', 'RFID Non Pengawas'],
              ].map(([field, label]) => `
                <div class="space-y-2">
                  <label class="text-sm font-medium text-on-surface">${escapeHtml(label)}</label>
                  <input data-field="${field}" type="number" value="${actualRow[field] ?? 0}" class="${inputClass()}">
                </div>
              `).join('')}
            </div>`, 'bg-[#f8fafc]')}
          ${card(`<div class="px-6 pt-6 pb-4"><h4 class="text-base font-semibold text-on-surface">Rolling baseline reference</h4></div>
            <div class="space-y-3 px-6 pb-6">
              <div class="rounded-2xl bg-white p-4 border border-outline-variant/20">
                <div class="text-sm font-medium text-on-surface">Window used</div>
                <div class="mt-1 text-sm text-on-surface-variant">${data.baselineStats.baselineRows.length} minggu historis sebelum ${escapeHtml(appState.selectedWeek)}</div>
              </div>
              ${FEATURE_META.map((feature) => `
                <div class="rounded-2xl bg-white p-4 border border-outline-variant/20">
                  <div class="font-medium text-on-surface">${escapeHtml(feature.label)}</div>
                  <div class="mt-1 text-sm text-on-surface-variant">Mean ${fmt(data.baselineStats.byFeature[feature.key].mean,2)} · Median ${fmt(data.baselineStats.byFeature[feature.key].median,2)} · Std ${fmt(data.baselineStats.byFeature[feature.key].std,2)}</div>
                </div>
              `).join('')}
            </div>`, 'bg-[#f8fafc]')}
        </div>
      </div>`)}

      <div class="space-y-6">
        ${card(cardTitle('Narrative insight') + `<div class="px-6 pb-6"><textarea readonly class="min-h-[260px] w-full rounded-2xl border border-outline-variant/30 bg-[#f8fafc] px-4 py-3 text-sm leading-6 text-on-surface shadow-sm">${escapeHtml(data.analysis.narrative)}</textarea></div>`)}
        ${card(cardTitle('Priority actions') + `<div class="space-y-3 px-6 pb-6">
          ${data.analysis.actionPriority.length ? data.analysis.actionPriority.map((line) => `<div class="rounded-2xl bg-[#f8fafc] p-4 text-sm text-on-surface border border-outline-variant/20">${escapeHtml(line)}</div>`).join('') : `
            <div class="rounded-2xl bg-emerald-50 p-5 text-sm text-emerald-700 ring-1 ring-emerald-200">
              Tidak ada kontribusi risiko yang menonjol pada minggu ini. Pertahankan konsistensi kontrol dan monitoring historis.
            </div>`}
        </div>`)}
      </div>
    </div>

    ${card(cardTitle('Indicator contribution table') + `<div class="overflow-x-auto px-6 pb-6">
      <table class="min-w-full text-left text-sm">
        <thead class="border-b border-outline-variant/30 text-on-surface-variant">
          <tr>
            <th class="px-4 py-3">Priority</th>
            <th class="px-4 py-3">Indicator</th>
            <th class="px-4 py-3">Actual</th>
            <th class="px-4 py-3">Baseline Mean</th>
            <th class="px-4 py-3">Z-score</th>
            <th class="px-4 py-3">Std. β</th>
            <th class="px-4 py-3">Contribution</th>
            <th class="px-4 py-3">Direction</th>
            <th class="px-4 py-3">Status</th>
          </tr>
        </thead>
        <tbody>
          ${data.analysis.indicators.map((item, index) => `
            <tr class="border-b border-outline-variant/15">
              <td class="px-4 py-4 font-medium text-on-surface">${index + 1}</td>
              <td class="px-4 py-4 font-medium text-on-surface">${escapeHtml(item.label)}</td>
              <td class="px-4 py-4 text-on-surface">${fmt(item.actual, 2)}</td>
              <td class="px-4 py-4 text-on-surface">${fmt(item.baselineMean, 2)}</td>
              <td class="px-4 py-4 font-medium ${item.zScore >= 0 ? 'text-on-surface' : 'text-on-surface-variant'}">${fmt(item.zScore, 2)}</td>
              <td class="px-4 py-4 font-medium ${item.beta >= 0 ? 'text-red-600' : 'text-emerald-600'}">${fmt(item.beta, 2)}</td>
              <td class="px-4 py-4 font-medium ${item.contribution >= 0 ? 'text-red-600' : 'text-emerald-600'}">${fmt(item.contribution, 2)}</td>
              <td class="px-4 py-4 text-on-surface-variant">${escapeHtml(item.direction)}</td>
              <td class="px-4 py-4">${badge(item.status)}</td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    </div>`)}

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      ${card(cardTitle('Statistical methodology') + `<div class="space-y-4 px-6 pb-6 text-sm text-on-surface-variant">
        <div class="rounded-2xl bg-[#f8fafc] p-5 border border-outline-variant/20">
          <div class="font-medium text-on-surface">Baseline / reference</div>
          <div class="mt-2">Rolling baseline dihitung dari ${appState.lookback} minggu historis sebelumnya pada site yang sama. Untuk tiap indikator digunakan mean, median, dan standard deviation.</div>
        </div>
        <div class="rounded-2xl bg-[#f8fafc] p-5 border border-outline-variant/20">
          <div class="font-medium text-on-surface">Weight / coefficient</div>
          <div class="mt-2">Bobot indikator berasal dari standardized coefficient ridge regression yang di-fit pada histori site. Tidak ada expert weight manual pada versi ini.</div>
        </div>
        <div class="rounded-2xl bg-[#f8fafc] p-5 border border-outline-variant/20">
          <div class="font-medium text-on-surface">Overall score</div>
          <div class="mt-2">Score mingguan berasal dari predicted incident level model lalu dinormalisasi ke 0–100 pada distribusi fitted prediction site yang sama.</div>
        </div>
      </div>`)}
      ${card(cardTitle('Model coefficient ranking') + `<div class="space-y-3 px-6 pb-6">
        ${data.analysis.coefficientTable.map((item) => `
          <div class="flex items-start gap-3 rounded-2xl bg-[#f8fafc] p-4 border border-outline-variant/20">
            <div class="rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-on-surface border border-outline-variant/20">${escapeHtml(item.label.slice(0,2).toUpperCase())}</div>
            <div class="flex-1">
              <div class="flex items-center justify-between gap-3">
                <div class="font-medium text-on-surface">${escapeHtml(item.label)}</div>
                <div class="text-sm font-semibold ${item.beta >= 0 ? 'text-red-600' : 'text-emerald-600'}">β = ${fmt(item.beta, 2)}</div>
              </div>
              <div class="mt-1 text-sm text-on-surface-variant">${escapeHtml(item.description)}</div>
            </div>
          </div>
        `).join('')}
      </div>`)}
    </div>
  `;
}

function metricCardSet(metrics) {
  return `
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
      <div class="rounded-2xl bg-primary p-5 text-on-primary">
        <div class="text-xs uppercase tracking-[0.2em] text-on-primary/80">Pearson</div>
        <div class="mt-2 text-3xl font-semibold">${fmt(metrics.pearson, 2)}</div>
      </div>
      <div class="rounded-2xl bg-white p-5 border border-outline-variant/20">
        <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">Spearman</div>
        <div class="mt-2 text-3xl font-semibold text-on-surface">${fmt(metrics.spearman, 2)}</div>
      </div>
      <div class="rounded-2xl bg-white p-5 border border-outline-variant/20">
        <div class="text-xs uppercase tracking-[0.2em] text-on-surface-variant">AUC</div>
        <div class="mt-2 text-3xl font-semibold text-on-surface">${fmt(metrics.auc, 2)}</div>
      </div>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-2xl bg-[#f8fafc] p-4 border border-outline-variant/20"><div class="text-xs text-on-surface-variant">Accuracy</div><div class="mt-2 text-2xl font-semibold text-on-surface">${fmt(metrics.accuracy * 100, 1)}%</div></div>
      <div class="rounded-2xl bg-[#f8fafc] p-4 border border-outline-variant/20"><div class="text-xs text-on-surface-variant">Precision</div><div class="mt-2 text-2xl font-semibold text-on-surface">${fmt(metrics.precision * 100, 1)}%</div></div>
      <div class="rounded-2xl bg-[#f8fafc] p-4 border border-outline-variant/20"><div class="text-xs text-on-surface-variant">Recall</div><div class="mt-2 text-2xl font-semibold text-on-surface">${fmt(metrics.recall * 100, 1)}%</div></div>
      <div class="rounded-2xl bg-[#f8fafc] p-4 border border-outline-variant/20"><div class="text-xs text-on-surface-variant">Specificity</div><div class="mt-2 text-2xl font-semibold text-on-surface">${fmt(metrics.specificity * 100, 1)}%</div></div>
    </div>
  `;
}

function renderAccuracy(data) {
  return `
  <div class="space-y-6">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.15fr_0.85fr]">
      ${card(cardTitle('Overlay score statistik vs insiden aktual') + `<div class="space-y-6 px-6 pb-6">
        <div class="h-[280px] w-full min-h-0 rounded-2xl bg-[#f8fafc] p-4 border border-outline-variant/20 sm:h-[360px]"><canvas id="accuracyChart"></canvas></div>
        <div class="rounded-2xl bg-[#f8fafc] p-5 text-sm text-on-surface border border-outline-variant/20">
          Same-week menilai seberapa baik score statistik menjelaskan minggu yang sedang berjalan. Next-week menilai seberapa baik score itu bekerja sebagai early warning satu minggu ke depan.
        </div>
      </div>`)}
      ${card(cardTitle('Tabel alert vs aktual') + `<div class="overflow-x-auto px-6 pb-6">
        <table class="min-w-full text-left text-sm">
          <thead class="border-b border-outline-variant/30 text-on-surface-variant">
            <tr>
              <th class="px-4 py-3">Week</th>
              <th class="px-4 py-3">Score</th>
              <th class="px-4 py-3">Pred. Inc</th>
              <th class="px-4 py-3">Actual</th>
              <th class="px-4 py-3">Prediksi</th>
              <th class="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody>
            ${data.weeklySeries.map((row) => `
              <tr class="border-b border-outline-variant/15">
                <td class="px-4 py-4 font-medium text-on-surface">${escapeHtml(row.week)}</td>
                <td class="px-4 py-4 text-on-surface">${fmt(row.score, 1)}</td>
                <td class="px-4 py-4 text-on-surface">${fmt(row.predictedIncidents, 2)}</td>
                <td class="px-4 py-4 text-on-surface">${fmt(row.actualIncidents, 0)}</td>
                <td class="px-4 py-4 text-on-surface">${row.score >= appState.alertThreshold ? 'Alert' : 'No Alert'}</td>
                <td class="px-4 py-4">${badge(row.status)}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>`)}
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      ${card(cardTitle('Accuracy check — same week') + `<div class="space-y-5 px-6 pb-6">${metricCardSet(data.validationMetrics.sameWeek)}</div>`)}
      ${card(cardTitle('Accuracy check — next week') + `<div class="space-y-5 px-6 pb-6">
        ${metricCardSet(data.validationMetrics.nextWeek)}
        <div class="rounded-2xl bg-amber-50 p-5 text-sm text-amber-800 ring-1 ring-amber-200">
          Bila same-week jauh lebih tinggi daripada next-week, maka model ini lebih kuat untuk back analysis daripada early warning murni.
        </div>
      </div>`)}
    </div>
  </div>
  `;
}

function render() {
  const data = deriveData();
  const app = document.getElementById('app');
  app.innerHTML = renderHeader(data) + (appState.page === 'accuracy' ? renderAccuracy(data) : renderDashboard(data));
  attachEvents();
  renderCharts(data);
}

function attachEvents() {
  document.querySelectorAll('[data-action="page"]').forEach((button) => {
    button.addEventListener('click', () => {
      appState.page = button.dataset.page;
      render();
    });
  });

  const resetBtn = document.querySelector('[data-action="reset"]');
  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      appState = {
        page: 'dashboard',
        site: 'All Site',
        selectedWeek: getDefaultWeek('All Site'),
        lookback: DEFAULT_LOOKBACK,
        alertThreshold: DEFAULT_ALERT_THRESHOLD,
        actualOverride: null,
      };
      render();
    });
  }

  const siteEl = document.querySelector('[data-control="site"]');
  if (siteEl) {
    siteEl.addEventListener('change', (e) => {
      appState.site = e.target.value;
      appState.selectedWeek = getDefaultWeek(appState.site);
      appState.actualOverride = null;
      render();
    });
  }

  const lookbackEl = document.querySelector('[data-control="lookback"]');
  if (lookbackEl) {
    lookbackEl.addEventListener('input', (e) => {
      appState.lookback = clamp(Math.floor(toFiniteNumber(e.target.value, DEFAULT_LOOKBACK)), 2, 10);
      render();
    });
  }

  const alertEl = document.querySelector('[data-control="alertThreshold"]');
  if (alertEl) {
    alertEl.addEventListener('input', (e) => {
      appState.alertThreshold = clamp(toFiniteNumber(e.target.value, DEFAULT_ALERT_THRESHOLD), 0, 100);
      render();
    });
  }

  const weekEl = document.querySelector('[data-control="selectedWeek"]');
  if (weekEl) {
    weekEl.addEventListener('change', (e) => {
      appState.selectedWeek = e.target.value;
      appState.actualOverride = null;
      render();
    });
  }

  document.querySelectorAll('[data-field]').forEach((input) => {
    input.addEventListener('input', (e) => {
      const field = e.target.dataset.field;
      const currentSiteRows = (SITE_ROWS[appState.site] || []).map(enrichRow);
      const selectedBaseRow = currentSiteRows.find((row) => row.week === appState.selectedWeek) || currentSiteRows[currentSiteRows.length - 1];
      if (!selectedBaseRow) return;
      const normalizedValue = field === 'actualIncidents'
        ? Math.max(toFiniteNumber(e.target.value, selectedBaseRow[field]), 0)
        : toFiniteNumber(e.target.value, selectedBaseRow[field]);
      appState.actualOverride = {
        ...(appState.actualOverride || {}),
        [field]: normalizedValue,
      };
      render();
    });
  });
}

function renderCharts(data) {
  if (dashboardChart) {
    dashboardChart.destroy();
    dashboardChart = null;
  }
  if (accuracyChart) {
    accuracyChart.destroy();
    accuracyChart = null;
  }

  const dashCanvas = document.getElementById('dashboardTrendChart');
  if (dashCanvas) {
    dashboardChart = new Chart(dashCanvas.getContext('2d'), {
      type: 'line',
      data: {
        labels: data.weeklySeries.map((row) => row.week),
        datasets: [
          {
            label: 'Score',
            data: data.weeklySeries.map((row) => row.score),
            borderColor: '#3952bc',
            backgroundColor: 'rgba(57,82,188,0.08)',
            borderWidth: 3,
            tension: 0.35,
            pointRadius: 4,
            pointHoverRadius: 6,
          },
          {
            label: 'Threshold 50',
            data: data.weeklySeries.map(() => 50),
            borderColor: '#f59e0b',
            borderDash: [6,6],
            borderWidth: 1.5,
            pointRadius: 0,
            tension: 0,
          },
          {
            label: 'Threshold 80',
            data: data.weeklySeries.map(() => 80),
            borderColor: '#ef4444',
            borderDash: [6,6],
            borderWidth: 1.5,
            pointRadius: 0,
            tension: 0,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              afterBody: (items) => {
                const idx = items[0].dataIndex;
                const point = data.weeklySeries[idx];
                return [
                  `Predicted Incidents: ${fmt(point.predictedIncidents, 2)}`,
                  `Actual Incidents: ${fmt(point.actualIncidents, 0)}`,
                  `Top Driver: ${point.topDriver}`,
                  `Status: ${point.status}`,
                ];
              }
            }
          }
        },
        scales: {
          y: {
            min: 0,
            max: 100,
            grid: { color: '#dfe3e6' },
            ticks: { color: '#595c5e' }
          },
          x: {
            grid: { color: '#eef1f3' },
            ticks: { color: '#595c5e' }
          }
        }
      }
    });
  }

  const accCanvas = document.getElementById('accuracyChart');
  if (accCanvas) {
    accuracyChart = new Chart(accCanvas.getContext('2d'), {
      type: 'line',
      data: {
        labels: data.weeklySeries.map((row) => row.week),
        datasets: [
          {
            label: 'Score',
            data: data.weeklySeries.map((row) => row.score),
            borderColor: '#3952bc',
            backgroundColor: 'rgba(57,82,188,0.08)',
            borderWidth: 3,
            tension: 0.35,
            pointRadius: 4,
            yAxisID: 'y',
          },
          {
            label: 'Alert Threshold',
            data: data.weeklySeries.map(() => appState.alertThreshold),
            borderColor: '#475569',
            borderDash: [6,6],
            borderWidth: 1.5,
            pointRadius: 0,
            yAxisID: 'y',
          },
          {
            label: 'Actual Incidents',
            data: data.weeklySeries.map((row) => row.actualIncidents),
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.08)',
            borderWidth: 3,
            tension: 0.35,
            pointRadius: 4,
            yAxisID: 'y1',
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { position: 'top' },
          tooltip: {
            callbacks: {
              afterBody: (items) => {
                const idx = items[0].dataIndex;
                const point = data.weeklySeries[idx];
                return [
                  `Predicted Incidents: ${fmt(point.predictedIncidents, 2)}`,
                  `Top Driver: ${point.topDriver}`,
                  `Status: ${point.status}`,
                ];
              }
            }
          }
        },
        scales: {
          y: {
            position: 'left',
            min: 0,
            max: 100,
            grid: { color: '#dfe3e6' },
            ticks: { color: '#595c5e' }
          },
          y1: {
            position: 'right',
            beginAtZero: true,
            grid: { drawOnChartArea: false },
            ticks: { color: '#595c5e', precision: 0 }
          },
          x: {
            grid: { color: '#eef1f3' },
            ticks: { color: '#595c5e' }
          }
        }
      }
    });
  }
}

render();
</script>
</body>
</html>