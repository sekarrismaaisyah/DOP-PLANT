@extends('sid-meeting.layouts.app')
@section('title', 'Master Site')
@section('content')
<div class="bg-white rounded-2xl shadow p-4 space-y-4">
    <form method="post" action="{{ route('sid-meeting.sites.store') }}" class="grid md:grid-cols-4 gap-3">@csrf
        <input name="name" placeholder="Nama site" class="border rounded-xl px-3 py-2" required>
        <input name="code" placeholder="Kode site" class="border rounded-xl px-3 py-2">
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Active</label>
        <button class="bg-cyan-600 text-white rounded-xl px-3 py-2">Tambah</button>
    </form>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead><tr class="text-left border-b"><th>Site</th><th>Kode</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($rows as $row)
                <tr class="border-b">
                    <td>{{ $row->name }}</td><td>{{ $row->code }}</td><td>{{ $row->is_active ? 'Active' : 'Inactive' }}</td>
                    <td class="py-2">
                        <form method="post" action="{{ route('sid-meeting.sites.destroy', $row) }}">@csrf @method('delete')
                            <button class="text-red-600">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
