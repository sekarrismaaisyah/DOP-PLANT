<!DOCTYPE html>

<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>404 - Server Capacity Reached</title>
<!-- BEGIN: Tailwind CSS Integration -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'warning-red': '#ef4444',
            'warning-orange': '#f97316',
            'dark-slate': '#111827',
          },
          animation: {
            'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            'flicker': 'flicker 2s infinite',
          },
          keyframes: {
            flicker: {
              '0%, 19.999%, 22%, 62.999%, 64%, 64.999%, 70%, 100%': { opacity: '1', textShadow: '0 0 10px #ff0000, 0 0 20px #ff0000' },
              '20%, 21.999%, 63%, 63.999%, 65%, 69.999%': { opacity: '0.4', textShadow: 'none' },
            }
          }
        }
      }
    }
  </script>
<!-- END: Tailwind CSS Integration -->
<style data-purpose="layout-and-bg">
    body {
      background-color: #0f172a;
      overflow: hidden;
    }
    .neon-sign {
      color: #ff3131;
      text-shadow: 0 0 5px #ff3131, 0 0 20px #ff3131;
    }
  </style>
<style data-purpose="animations">
    @keyframes steam {
      0% { transform: translateY(0) scale(1); opacity: 0; }
      50% { opacity: 0.5; }
      100% { transform: translateY(-100px) scale(2); opacity: 0; }
    }
    .steam-particle {
      animation: steam 3s infinite linear;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center font-sans text-gray-100">
<!-- BEGIN: Main Container -->
<main class="relative w-full max-w-4xl px-6 py-12 flex flex-col md:flex-row items-center justify-between gap-12" data-purpose="error-page-container">
<!-- BEGIN: Visual Illustration Section -->
<section class="relative w-full md:w-1/2 flex justify-center items-center" data-purpose="server-illustration">
<!-- The Server Rack -->
<div class="relative w-64 h-96 bg-gray-800 rounded-lg border-4 border-gray-700 shadow-2xl flex flex-col p-4 space-y-4 overflow-visible">
<!-- Server Units -->
<div class="h-12 w-full bg-gray-900 rounded border-b-2 border-red-500/30 flex items-center px-3 space-x-2">
<div class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></div>
<div class="w-2 h-2 rounded-full bg-orange-500"></div>
<div class="h-1 w-2/3 bg-gray-700 rounded"></div>
</div>
<div class="h-12 w-full bg-gray-900 rounded border-b-2 border-red-500/30 flex items-center px-3 space-x-2 relative">
<div class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></div>
<!-- Files Spilling Out -->
<div class="absolute -right-4 top-2 w-12 h-8 bg-orange-100/20 rotate-12 border border-orange-200/40 rounded-sm"></div>
<div class="absolute -right-2 top-4 w-12 h-8 bg-white/10 rotate-6 border border-white/20 rounded-sm"></div>
</div>
<div class="h-12 w-full bg-gray-900 rounded border-b-2 border-red-500/30 flex items-center px-3 space-x-2">
<div class="w-2 h-2 rounded-full bg-red-500"></div>
<div class="w-32 h-1 bg-red-900/50 rounded"></div>
</div>
<div class="h-12 w-full bg-gray-900 rounded border-b-2 border-red-500/30 flex items-center px-3 space-x-2 relative">
<div class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></div>
<!-- More Spilled Data -->
<div class="absolute -left-6 top-0 w-10 h-10 bg-gray-300/10 -rotate-12 border border-gray-400/20 rounded-sm"></div>
</div>
<!-- Neon Sign "NO VACANCY" -->
<div class="absolute -top-12 -right-8 transform rotate-12 bg-gray-900 border-2 border-gray-700 p-2 rounded shadow-lg">
<span class="neon-sign font-bold text-xs uppercase tracking-widest animate-flicker">No Vacancy</span>
</div>
<!-- Steam/Smoke Elements -->
<div class="absolute -top-8 left-1/4 w-8 h-8 bg-gray-400/20 rounded-full blur-xl steam-particle" style="animation-delay: 0s;"></div>
<div class="absolute -top-12 left-2/4 w-12 h-12 bg-gray-300/10 rounded-full blur-xl steam-particle" style="animation-delay: 1.5s;"></div>
<div class="absolute top-20 -left-4 w-6 h-6 bg-gray-400/20 rounded-full blur-xl steam-particle" style="animation-delay: 0.8s;"></div>
</div>
<!-- Warning Floor Shadow -->
<div class="absolute bottom-[-20px] w-48 h-8 bg-red-900/20 blur-2xl rounded-[100%]"></div>
</section>
<!-- END: Visual Illustration Section -->
<!-- BEGIN: Content Section -->
<section class="w-full md:w-1/2 text-center md:text-left space-y-6" data-purpose="error-message-content">
<div class="space-y-2">
<h2 class="text-orange-500 font-mono font-bold tracking-tighter uppercase text-sm">Error Code: 404</h2>
<h1 class="text-5xl md:text-6xl font-black text-white leading-tight">
          Capacity <br/> <span class="text-red-500">Reached</span>
</h1>
</div>
<p class="text-gray-400 text-lg max-w-md">
        Our digital storage is currently bursting at the seams. This server is overstuffed with data and can't find the space for your request.
      </p>
<div class="flex flex-col sm:flex-row gap-4 pt-4 justify-center md:justify-start">
<a class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-all transform hover:scale-105 shadow-[0_0_20px_rgba(239,68,68,0.4)] text-center" data-purpose="home-button" href="/">
          Go Back Home
        </a>
<button class="px-8 py-4 bg-transparent border-2 border-gray-700 hover:border-gray-500 text-gray-300 font-bold rounded-lg transition-all text-center" data-purpose="retry-button" onclick="window.location.reload()">
          Retry Connection
        </button>
</div>
<!-- Server Status Indicators -->
<div class="pt-8 border-t border-gray-800 flex items-center space-x-4 justify-center md:justify-start">
<div class="flex items-center space-x-2">
<span class="relative flex h-3 w-3">
<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
<span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
</span>
<span class="text-xs text-gray-500 font-mono uppercase">CPU: 99.9%</span>
</div>
<div class="flex items-center space-x-2">
<span class="relative flex h-3 w-3">
<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
<span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
</span>
<span class="text-xs text-gray-500 font-mono uppercase">RAM: FULL</span>
</div>
</div>
</section>
<!-- END: Content Section -->
</main>
<!-- END: Main Container -->
<!-- BEGIN: Decorative Background Elements -->
<div class="fixed top-0 left-0 w-full h-full pointer-events-none -z-10 opacity-20">
<div class="absolute top-10 left-10 w-64 h-64 bg-red-900 rounded-full blur-[120px]"></div>
<div class="absolute bottom-10 right-10 w-96 h-96 bg-orange-900 rounded-full blur-[150px]"></div>
</div>
<!-- END: Decorative Background Elements -->
</body></html>