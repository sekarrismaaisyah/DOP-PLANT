@extends('sid-meeting.layouts.app')
@section('title', 'Master Jenis Meeting')
@section('content')
<div class="bg-white rounded-2xl shadow p-4 space-y-4">
    <form method="post" action="{{ route('sid-meeting.meeting-types.store') }}" class="grid md:grid-cols-4 gap-3">@csrf
        <input name="name" placeholder="Nama jenis meeting" class="border rounded-xl px-3 py-2" required>
        <input name="description" placeholder="Deskripsi" class="border rounded-xl px-3 py-2">
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Active</label>
        <button class="bg-cyan-600 text-white rounded-xl px-3 py-2">Tambah</button>
    </form>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left border-b"><th>Nama</th><th>Deskripsi</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($rows as $row)
                <tr class="border-b">
                    <td>{{ $row->name }}</td><td>{{ $row->description }}</td><td>{{ $row->is_active ? 'Active' : 'Inactive' }}</td>
                    <td><form method="post" action="{{ route('sid-meeting.meeting-types.destroy', $row) }}">@csrf @method('delete')<button class="text-red-600">Hapus</button></form></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
