@extends('sid-meeting.layouts.app')
@section('title', 'Master Employee')
@section('content')
<div class="bg-white rounded-2xl shadow p-4 space-y-4">
    <form method="get"><input name="q" placeholder="Cari SID/nama/perusahaan" class="border rounded-xl px-3 py-2 w-full"></form>
    <form method="post" action="{{ route('sid-meeting.employees.store') }}" class="grid md:grid-cols-3 gap-3">@csrf
        <input name="kode_sid" placeholder="Kode SID" class="border rounded-xl px-3 py-2" required>
        <input name="nama" placeholder="Nama" class="border rounded-xl px-3 py-2" required>
        <select name="company_id" class="border rounded-xl px-3 py-2" required>@foreach($companies as $company)<option value="{{ $company->id }}">{{ $company->name }}</option>@endforeach</select>
        <input name="jabatan_struktural" placeholder="Jabatan Struktural" class="border rounded-xl px-3 py-2">
        <input name="jabatan_fungsional" placeholder="Jabatan Fungsional" class="border rounded-xl px-3 py-2">
        <button class="bg-cyan-600 text-white rounded-xl px-3 py-2">Tambah</button>
    </form>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="border-b text-left"><th>SID</th><th>Nama</th><th>Perusahaan</th><th>Struktural</th><th>Fungsional</th></tr></thead>
            <tbody>@foreach($rows as $row)<tr class="border-b"><td>{{ $row->kode_sid }}</td><td>{{ $row->nama }}</td><td>{{ $row->company->name }}</td><td>{{ $row->jabatan_struktural }}</td><td>{{ $row->jabatan_fungsional }}</td></tr>@endforeach</tbody>
        </table>
    </div>
    {{ $rows->links() }}
</div>
@endsection
