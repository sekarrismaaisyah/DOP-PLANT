@extends('sid-meeting.layouts.app')
@section('title', 'Semantic Evaluation')
@section('content')
<div class="bg-white rounded-2xl shadow p-4 space-y-4">
    <form class="grid md:grid-cols-4 gap-3">
        <input type="range" name="threshold" min="30" max="95" value="{{ $threshold }}" class="w-full">
        <input name="q" placeholder="Search issue/PIC/site" class="border rounded-xl px-3 py-2">
        <label class="flex items-center gap-2"><input type="checkbox" name="cross_site_only" value="1" {{ $crossSiteOnly ? 'checked' : '' }}> Cross-site only</label>
        <button class="bg-cyan-600 text-white rounded-xl px-3 py-2">Apply</button>
    </form>
    <div class="grid md:grid-cols-4 gap-3">
        <div class="bg-slate-50 p-3 rounded-xl">Total Issue: <b>{{ $summary['total_issue'] }}</b></div>
        <div class="bg-slate-50 p-3 rounded-xl">Similar Pairs: <b>{{ $summary['similar_pairs'] }}</b></div>
        <div class="bg-slate-50 p-3 rounded-xl">Repeated Groups: <b>{{ $summary['repeated_groups'] }}</b></div>
        <div class="bg-slate-50 p-3 rounded-xl">Cross-site Pairs: <b>{{ $summary['cross_site_pairs'] }}</b></div>
    </div>
    <a href="{{ route('sid-meeting.semantic.export', ['threshold' => $threshold, 'cross_site_only' => $crossSiteOnly ? 1 : 0]) }}" class="inline-block bg-slate-800 text-white rounded-xl px-3 py-2">Export Similarity CSV</a>
    <h3 class="font-semibold">Similarity Issue Pairs</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm"><thead><tr class="border-b text-left"><th>Similarity</th><th>Level</th><th>Site A</th><th>Issue A</th><th>Site B</th><th>Issue B</th><th>Action</th></tr></thead>
            <tbody>@foreach($pairs as $pair)<tr class="border-b"><td>{{ $pair['similarity'] }}%</td><td>{{ $pair['level'] }}</td><td>{{ $pair['site_a'] }}</td><td>{{ $pair['issue_a'] }}</td><td>{{ $pair['site_b'] }}</td><td>{{ $pair['issue_b'] }}</td><td>{{ $pair['action_signal'] }}</td></tr>@endforeach</tbody>
        </table>
    </div>
    <h3 class="font-semibold">Repeated Groups</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm"><thead><tr class="border-b text-left"><th>Group</th><th>Sites</th><th>Issue Count</th><th>Top Terms</th></tr></thead>
            <tbody>@foreach($groups as $group)<tr class="border-b"><td>{{ $group['group'] }}</td><td>{{ $group['sites']->join(', ') }}</td><td>{{ $group['issue_count'] }}</td><td>{{ $group['top_terms']->join(', ') }}</td></tr>@endforeach</tbody>
        </table>
    </div>
</div>
@endsection
