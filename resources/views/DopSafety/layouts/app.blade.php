<!DOCTYPE html>
<html class="light" lang="id">
   <head>
      <meta charset="utf-8"/>
      <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
      <title>@yield('title', 'DOP Safety') — PAMA GMO</title>
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
                 "primary": "#3952bc",
                 "secondary": "#72479e",
                 "error": "#b41340",
                 "on-background": "#2c2f31",
                 "on-surface": "#2c2f31",
                 "on-surface-variant": "#595c5e",
                 "outline-variant": "#abadaf",
                 "surface-container-lowest": "#ffffff",
               },
               fontFamily: {
                 "headline": ["Poppins"],
                 "body": ["Poppins"],
                 "label": ["Poppins"]
               },
             },
           },
         }
      </script>
      <style>
         .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
         }
         .hide-scrollbar::-webkit-scrollbar { display: none; }
         .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
      </style>
      @stack('head')
   </head>
   <body class="bg-[#f0f2f5] font-body text-on-surface min-h-screen flex flex-col">
      @php
         $navActive = $navActive ?? 'dashboard';
         $programLabel = $programLabel ?? 'Program Darurat Keselamatan Plant DOP';
         $navItems = $navItems ?? [];
      @endphp
      <header class="w-full sticky top-0 bg-white border-b border-[#dfe3e6] z-50 shadow-sm">
         <div class="mx-auto px-6 py-3 flex justify-between items-center gap-4">
            <div class="flex items-center gap-6 min-w-0 flex-1">
               <div class="flex flex-col shrink-0">
                  <h1 class="font-headline font-bold text-primary text-lg tracking-tighter leading-tight">PAMA GMO</h1>
                  <p class="text-on-surface-variant text-[9px] font-bold uppercase tracking-widest max-w-[12rem] truncate">{{ $programLabel }}</p>
               </div>
               @if(count($navItems) > 0)
               <div class="h-8 w-px bg-[#dfe3e6] hidden lg:block shrink-0"></div>
               <nav class="hidden lg:flex gap-4 overflow-x-auto hide-scrollbar flex-nowrap min-w-0">
                  @foreach($navItems as $item)
                  @php
                     $isActive = ($navActive ?? '') === ($item['key'] ?? '');
                     $href = $item['url'] ?? (isset($item['route']) ? route($item['route'], $item['params'] ?? []) : '#');
                  @endphp
                  <a class="whitespace-nowrap shrink-0 {{ $isActive ? 'text-primary border-b-2 border-primary pb-1 font-bold text-xs tracking-tight' : 'text-on-surface-variant hover:text-primary font-semibold text-xs tracking-tight transition-colors' }}" href="{{ $href }}">{{ $item['label'] }}</a>
                  @endforeach
               </nav>
               @endif
            </div>
            <div class="flex items-center gap-2 shrink-0">
               <span class="ds-badge hidden sm:inline-flex">{{ $programCode ?? config('dop_safety.program_code') }}</span>
               <div class="flex items-center gap-2 p-1 pr-3 bg-white rounded-full border border-outline-variant/30 shadow-sm">
                  <span class="material-symbols-outlined text-2xl text-primary">account_circle</span>
                  <div class="text-left hidden sm:block">
                     <p class="text-[10px] font-bold text-primary uppercase leading-none">{{ auth()->user()->name ?? 'User' }}</p>
                     <p class="text-[9px] text-on-surface-variant font-medium">PAMA BRCG</p>
                  </div>
               </div>
            </div>
         </div>
         @if(count($navItems) > 0)
         <nav class="lg:hidden flex gap-3 overflow-x-auto hide-scrollbar px-4 pb-3 border-t border-[#eef1f3] pt-2">
            @foreach($navItems as $item)
            @php
               $isActive = ($navActive ?? '') === ($item['key'] ?? '');
               $href = $item['url'] ?? (isset($item['route']) ? route($item['route'], $item['params'] ?? []) : '#');
            @endphp
            <a class="whitespace-nowrap shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold {{ $isActive ? 'bg-primary text-white' : 'bg-white border border-outline-variant/30 text-on-surface-variant' }}" href="{{ $href }}">{{ $item['label'] }}</a>
            @endforeach
         </nav>
         @endif
      </header>
      <main class="flex-grow w-full mx-auto p-6 md:p-8 space-y-6">
         @if(session('success'))
         <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900" role="status">{{ session('success') }}</div>
         @endif
         @if(session('error'))
         <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-900" role="alert">{{ session('error') }}</div>
         @endif
         @yield('content')
      </main>
      @stack('scripts')
   </body>
</html>
