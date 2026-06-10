{{--
    Partial header halaman (breadcrumb + judul + aksi).

    Variabel:
    - $breadcrumbParent  (string, default: 'Dashboard')
    - $breadcrumbCurrent (string, wajib)
    - $pageTitle         (string, default: $breadcrumbCurrent)
    - $pageSubtitle      (string|null)
--}}
@php
   $breadcrumbParent = $breadcrumbParent ?? 'Dashboard';
   $pageTitle = $pageTitle ?? ($breadcrumbCurrent ?? 'Halaman');
   $pageSubtitle = $pageSubtitle ?? null;
@endphp
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6 pb-6 border-b border-outline-variant/30">
   <div>
      <nav class="flex items-center gap-2 text-xs font-bold text-on-surface-variant uppercase mb-2">
         <span>{{ $breadcrumbParent }}</span>
         <span class="material-symbols-outlined text-xs">chevron_right</span>
         <span class="text-primary">{{ $breadcrumbCurrent ?? $pageTitle }}</span>
      </nav>
      <h2 class="font-headline font-extrabold text-4xl text-on-background tracking-tight">{{ $pageTitle }}</h2>
      @if($pageSubtitle)
      <p class="text-on-surface-variant font-medium mt-1">{{ $pageSubtitle }}</p>
      @endif
   </div>
   @if(!empty($actionsPartial))
   <div class="flex flex-wrap items-center gap-3">
      @include($actionsPartial, $actionsData ?? [])
   </div>
   @elseif(!empty($actions))
   <div class="flex flex-wrap items-center gap-3">
      {!! $actions !!}
   </div>
   @endif
</div>
