<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use App\Models\MasterAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterAktivitasController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $items = MasterAktivitas::orderBy('nama_aktivitas')
            ->paginate($perPage)
            ->withQueryString();

        return view('SistemRoster.masterAktivitas.index', [
            'items' => $items,
            'perPage' => $perPage,
        ]);
    }

    public function create(): View
    {
        return view('SistemRoster.masterAktivitas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_aktivitas' => ['required', 'string', 'max:255'],
            'periode_check' => ['nullable', 'string', 'max:100'],
        ]);

        MasterAktivitas::create($validated);

        return redirect()
            ->route('sistem-roster.master-aktivitas.index')
            ->with('success', 'Master aktivitas berhasil ditambahkan.');
    }

    public function edit(int $id): View|RedirectResponse
    {
        $item = MasterAktivitas::find($id);

        if (!$item) {
            return redirect()
                ->route('sistem-roster.master-aktivitas.index')
                ->with('error', 'Data tidak ditemukan.');
        }

        return view('SistemRoster.masterAktivitas.edit', compact('item'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $item = MasterAktivitas::findOrFail($id);

        $validated = $request->validate([
            'nama_aktivitas' => ['required', 'string', 'max:255'],
            'periode_check' => ['nullable', 'string', 'max:100'],
        ]);

        $item->update($validated);

        return redirect()
            ->route('sistem-roster.master-aktivitas.index')
            ->with('success', 'Master aktivitas berhasil diupdate.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $item = MasterAktivitas::findOrFail($id);
        $item->delete();

        return redirect()
            ->route('sistem-roster.master-aktivitas.index')
            ->with('success', 'Master aktivitas berhasil dihapus.');
    }
}
