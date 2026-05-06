@extends('sid-meeting.layouts.app')
@section('title', 'Rekap & Export')
@section('content')
<div class="bg-white rounded-2xl shadow p-4 space-y-4">
    <div class="flex gap-3">
        <a class="bg-cyan-600 text-white px-3 py-2 rounded-xl" href="{{ route('sid-meeting.reports.export-attendance') }}">Export Attendance CSV</a>
        <a class="bg-slate-800 text-white px-3 py-2 rounded-xl" href="{{ route('sid-meeting.reports.export-minutes') }}">Export Minutes CSV</a>
    </div>
    <h3 class="font-semibold">Data Absensi</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm"><thead><tr class="border-b text-left"><th>Tanggal</th><th>Week</th><th>Site</th><th>Meeting</th><th>Event</th><th>SID</th><th>Nama</th><th>Perusahaan</th></tr></thead>
            <tbody>@foreach($attendanceRows as $row)<tr class="border-b"><td>{{ optional($row->event)->meeting_date?->format('Y-m-d') }}</td><td>{{ optional($row->event)->week }}</td><td>{{ optional(optional($row->event)->site)->name }}</td><td>{{ optional(optional($row->event)->meetingType)->name }}</td><td>{{ optional($row->event)->event_code }}</td><td>{{ $row->kode_sid }}</td><td>{{ $row->nama_snapshot }}</td><td>{{ $row->perusahaan_snapshot }}</td></tr>@endforeach</tbody>
        </table>
    </div>
    {{ $attendanceRows->links() }}
    <h3 class="font-semibold mt-6">List Notulen</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm"><thead><tr class="border-b text-left"><th>Tanggal</th><th>Site</th><th>Event</th><th>Section</th><th>No</th><th>Catatan</th><th>PIC</th><th>Status</th></tr></thead>
            <tbody>@foreach($issueRows as $row)<tr class="border-b"><td>{{ optional(optional($row->eventMinute)->event)->meeting_date?->format('Y-m-d') }}</td><td>{{ optional(optional(optional($row->eventMinute)->event)->site)->name }}</td><td>{{ optional(optional($row->eventMinute)->event)->event_code }}</td><td>{{ $row->section }}</td><td>{{ $row->nomor }}</td><td>{{ $row->catatan_meeting }}</td><td>{{ $row->pic }}</td><td>{{ $row->computed_status }}</td></tr>@endforeach</tbody>
        </table>
    </div>
    {{ $issueRows->links() }}
</div>
@endsection
