<?php

namespace App\Http\Controllers\PembatasanLV;

use App\Http\Controllers\Controller;
use App\Http\Requests\PembatasanLV\PembatasanLVControlRoomPengawasRequest;
use App\Models\CctvControlRoomPengawas;
use App\Models\CctvData;
use App\Models\PembatasanBatasLvPerLokasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PembatasanLVControlRoomPengawasController extends Controller
{
    public function data(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $perPage = min(max((int) $request->query('per_page', 10), 5), 100);
        $page = max((int) $request->query('page', 1), 1);

        $controlRooms = $this->controlRoomNames();
        $pengawasByRoom = CctvControlRoomPengawas::query()
            ->orderBy('control_room')
            ->orderBy('nama_pengawas')
            ->get()
            ->groupBy('control_room');

        $cctvByRoom = CctvData::query()
            ->whereNotNull('control_room')
            ->where('control_room', '!=', '')
            ->get()
            ->groupBy('control_room');

        $batasByLokasi = PembatasanBatasLvPerLokasi::query()->get()->groupBy('lokasi');

        $groups = $controlRooms->map(function (string $controlRoom) use ($pengawasByRoom, $cctvByRoom, $batasByLokasi, $q) {
            $pengawas = ($pengawasByRoom->get($controlRoom) ?? collect())
                ->map(fn (CctvControlRoomPengawas $row) => [
                    'id' => $row->id,
                    'control_room' => $row->control_room,
                    'nama_pengawas' => $row->nama_pengawas,
                    'email_pengawas' => $row->email_pengawas ?? '',
                    'no_hp_pengawas' => $row->no_hp_pengawas ?? '',
                    'keterangan' => $row->keterangan ?? '',
                ])
                ->values();

            $cctvItems = $cctvByRoom->get($controlRoom) ?? collect();
            $site = (string) ($cctvItems->first()?->site ?? '');
            if ($site === '') {
                $batasMatch = ($batasByLokasi->get($controlRoom) ?? collect())->first();
                $site = (string) ($batasMatch?->site ?? '—');
            }

            $batasRow = ($batasByLokasi->get($controlRoom) ?? collect())->first();
            $batasLv = (int) ($batasRow?->batas_lv ?? 0);

            $detailItems = $this->detailItemsForRoom($controlRoom, $batasRow, $cctvItems);

            $kode = $this->shortCode($controlRoom, $site);

            if ($q !== '') {
                $roomMatch = stripos($controlRoom, $q) !== false
                    || stripos($site, $q) !== false
                    || stripos($kode, $q) !== false;
                $detailMatch = collect($detailItems)->contains(fn (string $item) => stripos($item, $q) !== false);

                $pengawas = $pengawas->filter(function (array $row) use ($q, $roomMatch) {
                    if ($roomMatch) {
                        return true;
                    }

                    return stripos($row['nama_pengawas'] ?? '', $q) !== false
                        || stripos($row['email_pengawas'] ?? '', $q) !== false
                        || stripos($row['no_hp_pengawas'] ?? '', $q) !== false
                        || stripos($row['keterangan'] ?? '', $q) !== false;
                })->values();

                if ($pengawas->isEmpty() && ! $roomMatch && ! $detailMatch) {
                    return null;
                }
            }

            return [
                'control_room' => $controlRoom,
                'kode' => $kode,
                'site' => $site !== '' ? $site : '—',
                'batas_lv' => $batasLv,
                'detail_items' => $detailItems,
                'pengawas_count' => $pengawas->count(),
                'pengawas' => $pengawas,
            ];
        })->filter()->values();

        $total = $groups->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;
        $items = $groups->slice($offset, $perPage)->values();
        $from = $total === 0 ? 0 : $offset + 1;
        $to = min($offset + $perPage, $total);

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total,
                'from' => $from,
                'to' => $to,
            ],
        ]);
    }

    public function options(): JsonResponse
    {
        return response()->json([
            'data' => $this->controlRoomNames()->values(),
        ]);
    }

    public function show(CctvControlRoomPengawas $controlRoomPengawas): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $controlRoomPengawas->id,
                'control_room' => $controlRoomPengawas->control_room,
                'nama_pengawas' => $controlRoomPengawas->nama_pengawas,
                'email_pengawas' => $controlRoomPengawas->email_pengawas ?? '',
                'no_hp_pengawas' => $controlRoomPengawas->no_hp_pengawas ?? '',
                'keterangan' => $controlRoomPengawas->keterangan ?? '',
            ],
        ]);
    }

    public function store(PembatasanLVControlRoomPengawasRequest $request): JsonResponse
    {
        $row = CctvControlRoomPengawas::query()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pengawas control room berhasil ditambahkan.',
            'data' => $row,
        ], 201);
    }

    public function update(PembatasanLVControlRoomPengawasRequest $request, CctvControlRoomPengawas $controlRoomPengawas): JsonResponse
    {
        $controlRoomPengawas->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data pengawas control room berhasil diperbarui.',
            'data' => $controlRoomPengawas->fresh(),
        ]);
    }

    public function destroy(CctvControlRoomPengawas $controlRoomPengawas): JsonResponse
    {
        $controlRoomPengawas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengawas control room berhasil dihapus.',
        ]);
    }

    private function controlRoomNames(): Collection
    {
        $fromCctv = CctvData::query()
            ->whereNotNull('control_room')
            ->where('control_room', '!=', '')
            ->distinct()
            ->pluck('control_room');

        $fromPengawas = CctvControlRoomPengawas::query()
            ->whereNotNull('control_room')
            ->where('control_room', '!=', '')
            ->distinct()
            ->pluck('control_room');

        return $fromCctv
            ->merge($fromPengawas)
            ->map(fn ($name) => trim((string) $name))
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * @return array<int, string>
     */
    private function detailItemsForRoom(string $controlRoom, ?PembatasanBatasLvPerLokasi $batasRow, Collection $cctvItems): array
    {
        if ($batasRow && filled($batasRow->detail_lokasi)) {
            $lines = preg_split('/\r\n|\r|\n/', (string) $batasRow->detail_lokasi) ?: [];

            return collect($lines)
                ->map(fn (string $line) => trim($line))
                ->filter()
                ->values()
                ->all();
        }

        return $cctvItems
            ->take(12)
            ->map(function ($cctv) {
                $parts = array_filter([
                    $cctv->no_cctv ?? null,
                    $cctv->nama_cctv ?? null,
                    $cctv->lokasi_pemasangan ?? null,
                ]);

                return trim(implode(' - ', $parts));
            })
            ->filter()
            ->values()
            ->all();
    }

    private function shortCode(string $controlRoom, string $site): string
    {
        if (preg_match('/\b([A-Z]{2,6})\b/', $controlRoom, $matches)) {
            return $matches[1];
        }

        $words = preg_split('/\s+/', trim($controlRoom)) ?: [];
        if (count($words) >= 2) {
            return strtoupper(collect($words)->take(3)->map(fn (string $w) => mb_substr($w, 0, 1))->implode(''));
        }

        if ($site !== '' && $site !== '—') {
            $siteWords = preg_split('/\s+/', trim($site)) ?: [];

            return strtoupper(collect($siteWords)->take(2)->map(fn (string $w) => mb_substr($w, 0, 1))->implode(''));
        }

        return strtoupper(mb_substr(preg_replace('/\s+/', '', $controlRoom) ?: 'CR', 0, 4));
    }
}
