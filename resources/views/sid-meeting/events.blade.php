@extends('sid-meeting.layouts.app')
@section('title', 'Event Meeting')
@section('content')
<div class="bg-white rounded-2xl shadow p-4 space-y-4">
    <form method="post" action="{{ route('sid-meeting.events.store') }}" class="grid md:grid-cols-3 gap-3">@csrf
        <select name="meeting_type_id" class="border rounded-xl px-3 py-2">@foreach($meetingTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach</select>
        <select name="site_id" class="border rounded-xl px-3 py-2">@foreach($sites as $site)<option value="{{ $site->id }}">{{ $site->name }}</option>@endforeach</select>
        <input type="date" name="meeting_date" class="border rounded-xl px-3 py-2" required>
        <input type="time" name="start_time" class="border rounded-xl px-3 py-2" required>
        <input type="time" name="end_time" class="border rounded-xl px-3 py-2" required>
        <button class="bg-cyan-600 text-white rounded-xl px-3 py-2">Create Event</button>
    </form>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="border-b text-left"><th>Kode</th><th>Site</th><th>Jenis</th><th>Tanggal</th><th>Status</th><th>QR</th><th></th></tr></thead>
            <tbody>
            @foreach($rows as $row)
                <tr class="border-b">
                    <td>{{ $row->event_code }}</td><td>{{ $row->site->name }}</td><td>{{ $row->meetingType->name }}</td><td>{{ $row->meeting_date?->format('Y-m-d') }}</td>
                    <td>{{ $row->computed_status }}</td><td><a class="text-cyan-700" href="{{ route('sid-meeting.attendance.form', $row->qr_token) }}" target="_blank">Link</a></td>
                    <td><a class="text-blue-700" href="{{ route('sid-meeting.events.show', $row) }}">Detail</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $rows->links() }}
</div>
@endsection
