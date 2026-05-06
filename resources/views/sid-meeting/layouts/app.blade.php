<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SID Meeting')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen">
<div class="max-w-7xl mx-auto p-6 space-y-6">
    <div class="bg-white rounded-2xl shadow p-4 flex flex-wrap gap-3">
        <a href="{{ route('sid-meeting.dashboard') }}" class="px-3 py-2 rounded-xl bg-cyan-600 text-white">Dashboard</a>
        <a href="{{ route('sid-meeting.sites.index') }}" class="px-3 py-2 rounded-xl bg-slate-100">Site</a>
        <a href="{{ route('sid-meeting.meeting-types.index') }}" class="px-3 py-2 rounded-xl bg-slate-100">Jenis Meeting</a>
        <a href="{{ route('sid-meeting.companies.index') }}" class="px-3 py-2 rounded-xl bg-slate-100">Perusahaan</a>
        <a href="{{ route('sid-meeting.employees.index') }}" class="px-3 py-2 rounded-xl bg-slate-100">Employee</a>
        <a href="{{ route('sid-meeting.events.index') }}" class="px-3 py-2 rounded-xl bg-slate-100">Event</a>
        <a href="{{ route('sid-meeting.reports.index') }}" class="px-3 py-2 rounded-xl bg-slate-100">Report</a>
        <a href="{{ route('sid-meeting.performance.index') }}" class="px-3 py-2 rounded-xl bg-slate-100">Performance</a>
        <a href="{{ route('sid-meeting.semantic.index') }}" class="px-3 py-2 rounded-xl bg-slate-100">Semantic</a>
    </div>
    @if(session('success'))<div class="bg-emerald-100 text-emerald-800 rounded-xl p-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-100 text-red-800 rounded-xl p-3">{{ session('error') }}</div>@endif
    @yield('content')
</div>
</body>
</html>
