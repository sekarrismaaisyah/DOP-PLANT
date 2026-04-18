<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PeerPressure\PeerPressureBerecordNitipService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class PeerPressureBerecordTableController extends Controller
{
    public function index(Request $request, PeerPressureBerecordNitipService $berecordNitip): View
    {
        $q = trim((string) $request->get('q', ''));
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 12;

        $result = $berecordNitip->paginateView($page, $perPage, $q);

        $rows = $result['rows'] ?? [];
        $total = (int) ($result['total'] ?? 0);
        $connected = (bool) ($result['connected'] ?? false);
        $chError = $result['error'] ?? null;

        $paginator = new LengthAwarePaginator(
            $rows,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
                'query' => $request->query(),
            ]
        );

        return view('peer-pressure-edukasi.berecord.index', [
            'rows' => $paginator,
            'q' => $q,
            'connected' => $connected,
            'chError' => $chError,
            'columnLabels' => PeerPressureBerecordNitipService::columnLabels(),
            'navActive' => 'berecord',
        ]);
    }
}
