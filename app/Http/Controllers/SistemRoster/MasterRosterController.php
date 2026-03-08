<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MasterRosterController extends Controller
{
    /** Tabel roster per site beserta label untuk tab. */
    private const ROSTER_TABLES = [
        'roster' => ['label' => 'Roster Utama'],
        'roster_gmo' => ['label' => 'GMO'],
        'roster_bmo3' => ['label' => 'BMO 3'],
        'roster_bmo1' => ['label' => 'BMO 1'],
        'roster_pmo' => ['label' => 'PMO'],
        'roster_smo' => ['label' => 'SMO'],
        'roster_lmo' => ['label' => 'LMO'],
        'roster_hote' => ['label' => 'HOTE'],
    ];

    /**
     * Menampilkan halaman Master Roster Weekly: mapping roster per tabel/site,
     * tampilan per minggu (Senin–Minggu).
     */
    public function index(Request $request): View
    {
        $startOfWeek = $this->resolveStartOfWeek($request);
        $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::SUNDAY);

        $weekDays = collect(range(0, 6))->map(function ($i) use ($startOfWeek) {
            return $startOfWeek->copy()->addDays($i);
        });

        $weeklyData = collect();
        foreach (self::ROSTER_TABLES as $tableName => $meta) {
            try {
                $weeklyData[$tableName] = [
                    'label' => $meta['label'],
                    'data' => $this->getWeeklyRosterData($tableName, $startOfWeek, $endOfWeek),
                ];
            } catch (\Throwable $e) {
                $weeklyData[$tableName] = [
                    'label' => $meta['label'],
                    'data' => collect(),
                    'error' => $e->getMessage(),
                ];
            }
        }

        return view('SistemRoster.masterRoster.index', [
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
            'weekDays' => $weekDays,
            'weeklyData' => $weeklyData,
        ]);
    }

    /**
     * Tentukan awal minggu (Senin). Bisa dari query ?start_date=YYYY-MM-DD.
     */
    private function resolveStartOfWeek(Request $request): Carbon
    {
        $input = $request->get('start_date');
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $input)) {
            $date = Carbon::parse($input);
            return $date->copy()->startOfWeek(Carbon::MONDAY);
        }
        return Carbon::today()->startOfWeek(Carbon::MONDAY);
    }

    /**
     * Ambil data mingguan untuk satu tabel roster.
     * Dikelompokkan per nama, lalu per tanggal (Y-m-d).
     */
    private function getWeeklyRosterData(string $table, Carbon $startOfWeek, Carbon $endOfWeek): Collection
    {
        $rows = DB::table($table)
            ->whereBetween('date_ins', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->orderBy('nama')
            ->orderBy('date_ins')
            ->get();

        return $rows->groupBy(function ($row) {
            return $row->nama ?: 'Tanpa Nama';
        })->map(function ($items) {
            return $items->keyBy(function ($row) {
                return Carbon::parse($row->date_ins)->toDateString();
            });
        });
    }
}
