@extends('sid-meeting.layouts.app')
@section('title', 'SID Meeting Dashboard')
@section('content')
<div class="grid md:grid-cols-4 gap-4">
    @foreach($stats as $label => $value)
        <div class="bg-white rounded-2xl shadow p-4">
            <p class="text-xs uppercase text-slate-500">{{ str_replace('_', ' ', $label) }}</p>
            <p class="text-2xl font-semibold mt-1">{{ $value }}</p>
        </div>
    @endforeach
</div>
@endsection
