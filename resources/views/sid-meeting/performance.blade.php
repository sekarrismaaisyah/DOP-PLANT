@extends('sid-meeting.layouts.app')
@section('title', 'Site Performance')
@section('content')
<div class="grid md:grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl shadow p-4">Total Site: <b>{{ $totalSite }}</b></div>
    <div class="bg-white rounded-2xl shadow p-4">Average Attendance Rate: <b>{{ $avgRate }}%</b></div>
    <div class="bg-white rounded-2xl shadow p-4">Week Coverage: <b>{{ $weekCoverage }}</b></div>
</div>
<div class="bg-white rounded-2xl shadow p-4 mt-4">
    <canvas id="trendChart" height="80"></canvas>
</div>
<div class="bg-white rounded-2xl shadow p-4 mt-4 overflow-x-auto">
    <table class="min-w-full text-sm"><thead><tr class="border-b text-left"><th>Rank</th><th>Perusahaan</th><th>Expected Event</th><th>Event Hadir</th><th>Total Absensi</th><th>Rate</th><th>Label</th></tr></thead>
        <tbody>@foreach($companyRows as $row)<tr class="border-b"><td>{{ $row['rank'] }}</td><td>{{ $row['company'] }}</td><td>{{ $row['expected_event'] }}</td><td>{{ $row['event_hadir'] }}</td><td>{{ $row['total_absensi'] }}</td><td>{{ $row['rate'] }}%</td><td>{{ $row['label'] }}</td></tr>@endforeach</tbody>
    </table>
</div>
<script>
    const trend = @json($trend);
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: { labels: trend.map(t => t.week), datasets: [{ label: 'Attendance Rate', data: trend.map(t => t.rate), borderColor: '#0891b2', tension: 0.35 }] }
    });
</script>
@endsection
