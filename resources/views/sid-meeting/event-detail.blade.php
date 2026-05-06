@extends('sid-meeting.layouts.app')
@section('title', 'Detail Event')
@section('content')
<div class="grid lg:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl shadow p-4 space-y-3">
        <h2 class="font-semibold text-lg">{{ $event->event_code }}</h2>
        <p>{{ $event->site->name }} | {{ $event->meetingType->name }} | {{ $event->meeting_date?->format('Y-m-d') }}</p>
        <p>Status: <span class="px-2 py-1 rounded bg-slate-100">{{ $event->computed_status }}</span></p>
        <p>Attendance Rate Company: {{ $event->attendanceRate() }}%</p>
        <a class="text-cyan-700" target="_blank" href="{{ route('sid-meeting.attendance.form', $event->qr_token) }}">Buka Link QR Absensi</a>
        <form method="post" action="{{ route('sid-meeting.events.close', $event) }}">@csrf<button class="bg-slate-800 text-white rounded-xl px-3 py-2">Close Event</button></form>
    </div>
    <div class="bg-white rounded-2xl shadow p-4 space-y-3">
        <h3 class="font-semibold">Manual Attendance</h3>
        <form method="post" action="{{ route('sid-meeting.events.manual-attendance', $event) }}" class="flex gap-2">@csrf
            <input name="kode_sid" class="border rounded-xl px-3 py-2 flex-1" placeholder="Input SID">
            <button class="bg-cyan-600 text-white rounded-xl px-3 py-2">Simpan</button>
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm"><thead><tr class="border-b text-left"><th>SID</th><th>Nama</th><th>Perusahaan</th><th>Method</th></tr></thead>
                <tbody>@foreach($event->attendances as $att)<tr class="border-b"><td>{{ $att->kode_sid }}</td><td>{{ $att->nama_snapshot }}</td><td>{{ $att->perusahaan_snapshot }}</td><td>{{ $att->input_method }}</td></tr>@endforeach</tbody></table>
        </div>
    </div>
</div>
<div class="bg-white rounded-2xl shadow p-4 mt-4 space-y-3">
    <h3 class="font-semibold">Notulensi</h3>
    <form method="post" action="{{ route('sid-meeting.events.minutes.save', $event) }}" class="grid md:grid-cols-4 gap-2">@csrf
        <input name="title" value="{{ optional($event->eventMinute)->title }}" placeholder="Judul" class="border rounded-xl px-3 py-2">
        <input name="notulis" value="{{ optional($event->eventMinute)->notulis }}" placeholder="Notulis" class="border rounded-xl px-3 py-2">
        <input name="location" value="{{ optional($event->eventMinute)->location }}" placeholder="Lokasi" class="border rounded-xl px-3 py-2">
        <button class="bg-cyan-600 text-white rounded-xl px-3 py-2">Simpan Header</button>
    </form>
    <form method="post" action="{{ route('sid-meeting.events.issues.store', $event) }}" class="grid md:grid-cols-4 gap-2">@csrf
        <select name="section" class="border rounded-xl px-3 py-2"><option>enviro</option><option>safety</option><option>general</option></select>
        <input type="number" name="nomor" placeholder="Nomor" class="border rounded-xl px-3 py-2">
        <input name="issued_by" placeholder="Issued by" class="border rounded-xl px-3 py-2">
        <input name="pic" placeholder="PIC" class="border rounded-xl px-3 py-2">
        <input type="date" name="due_date" class="border rounded-xl px-3 py-2">
        <select name="status" class="border rounded-xl px-3 py-2"><option>Open</option><option>Progress</option><option>Closed</option><option>Overdue</option></select>
        <input name="keterangan" placeholder="Keterangan" class="border rounded-xl px-3 py-2">
        <input name="catatan_meeting" placeholder="Catatan meeting" class="border rounded-xl px-3 py-2 md:col-span-3">
        <button class="bg-cyan-600 text-white rounded-xl px-3 py-2">Tambah Issue</button>
    </form>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm"><thead><tr class="border-b text-left"><th>Section</th><th>No</th><th>Catatan</th><th>PIC</th><th>Due</th><th>Status</th></tr></thead>
            <tbody>@foreach(optional($event->eventMinute)->issues ?? [] as $issue)<tr class="border-b"><td>{{ $issue->section }}</td><td>{{ $issue->nomor }}</td><td>{{ $issue->catatan_meeting }}</td><td>{{ $issue->pic }}</td><td>{{ optional($issue->due_date)->format('Y-m-d') }}</td><td>{{ $issue->computed_status }}</td></tr>@endforeach</tbody></table>
    </div>
</div>
@endsection
