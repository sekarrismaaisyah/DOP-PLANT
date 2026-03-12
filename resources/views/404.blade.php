<!DOCTYPE html>

<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>TOTAL SYSTEM OVERLOAD - 404</title>
<!-- Tailwind CSS CDN with Plugins -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- BEGIN: Custom Configuration -->
<script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'glitch-red': '#ff003c',
            'glitch-orange': '#ff8a00',
            'cyber-dark': '#050505',
          },
          animation: {
            'glitch': 'glitch 1s infinite linear alternate-reverse',
            'pulse-fast': 'pulse 0.5s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            'flicker': 'flicker 0.15s infinite',
            'scanline': 'scanline 6s linear infinite',
          },
          keyframes: {
            glitch: {
              '0%': { transform: 'translate(0)' },
              '20%': { transform: 'translate(-3px, 3px)' },
              '40%': { transform: 'translate(-3px, -3px)' },
              '60%': { transform: 'translate(3px, 3px)' },
              '80%': { transform: 'translate(3px, -3px)' },
              '100%': { transform: 'translate(0)' },
            },
            flicker: {
              '0%, 100%': { opacity: '1' },
              '50%': { opacity: '0.8' },
            },
            scanline: {
              '0%': { transform: 'translateY(-100%)' },
              '100%': { transform: 'translateY(100%)' },
            }
          }
        }
      }
    }
  </script>
<!-- END: Custom Configuration -->
<!-- BEGIN: Layout Styles -->
<style data-purpose="layout-and-effects">
    body {
      background-color: #050505;
      color: #ff003c;
      overflow: hidden;
      font-family: 'Courier New', Courier, monospace;
    }

    /* Glitch Text Effect */
    .glitch-text {
      position: relative;
      text-shadow: 0.05em 0 0 rgba(255, 0, 0, 0.75),
                  -0.025em -0.05em 0 rgba(0, 255, 0, 0.75),
                  0.025em 0.05em 0 rgba(0, 0, 255, 0.75);
      animation: glitch 500ms infinite;
    }

    /* Scanline overlay */
    .scanlines {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        to bottom,
        rgba(18, 16, 16, 0) 50%,
        rgba(0, 0, 0, 0.25) 50%
      );
      background-size: 100% 4px;
      z-index: 50;
      pointer-events: none;
    }

    /* CRT Vignette */
    .vignette {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle, transparent 40%, black 150%);
      pointer-events: none;
      z-index: 60;
    }

    /* Gauge Fill Animation */
    @keyframes fillGauge {
      from { width: 0%; }
      to { width: 100%; }
    }
    .gauge-active {
      animation: fillGauge 2s ease-out forwards;
    }
  </style>
<!-- END: Layout Styles -->
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4">
<!-- BEGIN: Background Effects -->
<div class="scanlines"></div>
<div class="vignette"></div>
<div class="fixed inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 pointer-events-none"></div>
<!-- END: Background Effects -->
<!-- BEGIN: Main Content -->
<main class="relative z-10 w-full max-w-4xl text-center" data-purpose="main-error-container">
<!-- BEGIN: Visual Illustration -->
<div class="relative mb-8 flex justify-center" data-purpose="server-overload-illustration">
<!-- Server Rack Container -->
<div class="relative w-64 h-80 bg-zinc-900 border-4 border-zinc-800 rounded-lg shadow-[0_0_50px_rgba(255,0,0,0.3)] flex flex-col p-4 gap-4 overflow-hidden">
<!-- Server Components -->
<div class="h-8 bg-zinc-800 rounded border border-zinc-700 flex items-center px-2 gap-2">
<div class="w-2 h-2 rounded-full bg-red-600 animate-flicker"></div>
<div class="flex-1 h-1 bg-zinc-700"></div>
</div>
<div class="h-8 bg-zinc-800 rounded border border-zinc-700 flex items-center px-2 gap-2">
<div class="w-2 h-2 rounded-full bg-red-600 animate-pulse-fast"></div>
<div class="flex-1 h-1 bg-zinc-700"></div>
</div>
<!-- Sparks & Particles (CSS elements) -->
<div class="absolute inset-0 pointer-events-none overflow-hidden">
<div class="absolute top-1/2 left-1/2 w-40 h-40 bg-red-600/20 blur-3xl animate-pulse"></div>
<!-- CSS-based "sparks" -->
<div class="absolute top-1/3 left-1/4 w-1 h-4 bg-orange-400 rotate-45 animate-bounce"></div>
<div class="absolute top-2/3 right-1/4 w-1 h-4 bg-yellow-400 -rotate-12 animate-flicker"></div>
<div class="absolute top-1/2 right-10 w-2 h-2 bg-red-500 rounded-full animate-ping"></div>
</div>
<!-- Overload Text -->
<div class="mt-auto bg-black p-2 border border-red-500 text-red-500 text-xs font-bold uppercase tracking-widest animate-flicker">
          OVERLOAD
        </div>
</div>
<!-- Floating Warnings -->
<div class="absolute -top-10 -left-10 md:-left-20 bg-red-600 text-black px-4 py-2 font-black rotate-[-15deg] shadow-lg animate-pulse" data-purpose="alarm-tag">
        CRITICAL FAILURE
      </div>
<div class="absolute bottom-0 -right-5 md:-right-20 bg-orange-500 text-black px-4 py-2 font-black rotate-[10deg] shadow-lg" data-purpose="alarm-tag">
        SYSTEM MELTDOWN
      </div>
</div>
<!-- END: Visual Illustration -->
<!-- BEGIN: Typography -->
<section class="space-y-6" data-purpose="error-messaging">
<h1 class="text-5xl md:text-7xl font-black uppercase tracking-tighter glitch-text mb-4">
        SYSTEM OVERLOAD
      </h1>
<div class="inline-block border-y border-red-900/50 py-4 px-8 mb-8">
<p class="text-xl md:text-2xl text-orange-500 font-mono">
          [ERROR 503: CAPACITY_EXCEEDED]
        </p>
<p class="text-zinc-400 mt-2 max-w-xl mx-auto">
          Our servers have reached their breaking point. The data storm has consumed all available space and the neural mesh is destabilizing.
        </p>
</div>
</section>
<!-- END: Typography -->
<!-- BEGIN: Tech UI Elements -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-12 max-w-2xl mx-auto" data-purpose="ui-gauges">
<!-- CPU Gauge -->
<div class="bg-zinc-900/50 border border-zinc-800 p-4 rounded">
<div class="flex justify-between text-[10px] mb-2 text-zinc-500">
<span>CPU_CORE_01</span>
<span class="text-red-500">100%</span>
</div>
<div class="w-full bg-zinc-800 h-2 rounded-full overflow-hidden">
<div class="bg-red-600 h-full gauge-active" style="width: 100%"></div>
</div>
</div>
<!-- Temp Gauge -->
<div class="bg-zinc-900/50 border border-zinc-800 p-4 rounded">
<div class="flex justify-between text-[10px] mb-2 text-zinc-500">
<span>CORE_TEMP</span>
<span class="text-red-500">CRITICAL</span>
</div>
<div class="w-full bg-zinc-800 h-2 rounded-full overflow-hidden">
<div class="bg-orange-500 h-full gauge-active" style="width: 100%"></div>
</div>
</div>
<!-- Packet Gauge -->
<div class="bg-zinc-900/50 border border-zinc-800 p-4 rounded">
<div class="flex justify-between text-[10px] mb-2 text-zinc-500">
<span>PACKET_LOSS</span>
<span class="text-red-500">98.2%</span>
</div>
<div class="w-full bg-zinc-800 h-2 rounded-full overflow-hidden">
<div class="bg-yellow-600 h-full gauge-active" style="width: 98%"></div>
</div>
</div>
</section>
<!-- END: Tech UI Elements -->
<!-- BEGIN: Navigation Actions -->
<div class="flex flex-col md:flex-row items-center justify-center gap-6" data-purpose="action-buttons">
<a class="group relative px-8 py-4 bg-zinc-900 border-2 border-zinc-700 text-zinc-400 font-bold uppercase tracking-widest hover:bg-zinc-800 hover:text-white transition-all duration-300" href="/">
<span class="relative z-10">Emergency Evacuation</span>
<div class="absolute inset-0 h-full w-0 bg-red-600/10 transition-all group-hover:w-full"></div>
</a>
<button class="group relative px-8 py-4 bg-red-600 text-black font-black uppercase tracking-widest hover:bg-red-500 transition-all duration-300 shadow-[0_0_20px_rgba(220,38,38,0.5)]" onclick="location.reload()">
<span class="relative z-10">Try Force Connection</span>
<div class="absolute -inset-1 bg-red-600 blur opacity-30 group-hover:opacity-60"></div>
</button>
</div>
<!-- END: Navigation Actions -->
<!-- BEGIN: Flashing Alarms -->
<div class="mt-12 flex justify-center gap-8 opacity-50">
<div class="flex items-center gap-2 text-red-500 animate-pulse">
<svg class="h-5 w-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
<path clip-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" fill-rule="evenodd"></path>
</svg>
<span class="text-[10px] tracking-[0.2em]">ALARM_ACTIVE</span>
</div>
<div class="flex items-center gap-2 text-red-500 animate-pulse" style="animation-delay: 0.2s">
<svg class="h-5 w-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
<path clip-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" fill-rule="evenodd"></path>
</svg>
<span class="text-[10px] tracking-[0.2em]">POWER_SURGE</span>
</div>
</div>
<!-- END: Flashing Alarms -->
</main>
<!-- END: Main Content -->
<!-- BEGIN: Interaction Logic -->
<script data-purpose="interaction-logic">
    // Add a slight mouse tracking effect for the "chaotic" feel
    document.addEventListener('mousemove', (e) => {
      const x = (e.clientX / window.innerWidth - 0.5) * 10;
      const y = (e.clientY / window.innerHeight - 0.5) * 10;
      const container = document.querySelector('main');
      container.style.transform = `translate(${x}px, ${y}px)`;
    });

    // Console log for "techy" flavor
    console.warn("%c [SYSTEM]: OVERLOAD DETECTED. ALL THREADS SATURATED. ", "background: red; color: black; font-weight: bold;");
  </script>
<!-- END: Interaction Logic -->
</body></html>