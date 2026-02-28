<?php

namespace App\Http\Controllers;

use App\Models\CctvP2hChecklist;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CctvP2hChecklistController extends Controller
{
    /**
     * Display a listing of P2H Checklists.
     */
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $query = CctvP2hChecklist::query();

        // Filter by control room
        if ($request->filled('control_room')) {
            $query->where('control_room', $request->control_room);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pemeriksaan', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pemeriksaan', '<=', $request->end_date);
        }

        // Filter by shift
        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        $checklists = $query->orderByDesc('tanggal_pemeriksaan')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        // Get unique control rooms for filter dropdown
        $controlRooms = CctvP2hChecklist::select('control_room')
            ->distinct()
            ->orderBy('control_room')
            ->pluck('control_room');

        return view('cctv-p2h-checklist.index', compact('checklists', 'perPage', 'controlRooms'));
    }

    /**
     * Display the specified P2H Checklist.
     */
    public function show($id): View
    {
        $checklist = CctvP2hChecklist::findOrFail($id);
        return view('cctv-p2h-checklist.show', compact('checklist'));
    }

    /**
     * Remove the specified P2H Checklist from storage.
     */
    public function destroy($id)
    {
        $checklist = CctvP2hChecklist::findOrFail($id);
        $checklist->delete();

        return redirect()
            ->route('cctv-p2h-checklist.index')
            ->with('success', 'Data P2H Checklist berhasil dihapus.');
    }
}
