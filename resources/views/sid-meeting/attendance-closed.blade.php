<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><script src="https://cdn.tailwindcss.com"></script><title>Absensi Ditutup</title></head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center">
<div class="bg-white rounded-2xl shadow p-8 text-center">
    <h1 class="text-xl font-semibold text-red-600">Absensi sudah ditutup</h1>
    <p class="mt-2">{{ $event->event_code }} - {{ $event->site->name }}</p>
</div>
</body></html>
