@extends('sid-meeting.layouts.app')
@section('title', 'Master Perusahaan')
@section('content')
<div class="bg-white rounded-2xl shadow p-4 space-y-4">
    <form method="post" action="{{ route('sid-meeting.companies.store') }}" class="grid md:grid-cols-2 gap-3">@csrf
        <input name="name" placeholder="Nama perusahaan" class="border rounded-xl px-3 py-2" required>
        <input name="code" placeholder="Kode perusahaan" class="border rounded-xl px-3 py-2">
        <div class="md:col-span-2">
            <p class="text-sm mb-2">Site Eligibility</p>
            <div class="flex flex-wrap gap-3">@foreach($sites as $site)<label><input type="checkbox" name="site_ids[]" value="{{ $site->id }}"> {{ $site->name }}</label>@endforeach</div>
        </div>
        <button class="bg-cyan-600 text-white rounded-xl px-3 py-2 md:col-span-2">Tambah</button>
    </form>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="border-b text-left"><th>Perusahaan</th><th>Site Eligible</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($rows as $row)
                <tr class="border-b"><td>{{ $row->name }}</td><td>{{ $row->sites->pluck('name')->join(', ') }}</td><td><form method="post" action="{{ route('sid-meeting.companies.destroy', $row) }}">@csrf @method('delete')<button class="text-red-600">Hapus</button></form></td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
