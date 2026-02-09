<?php

namespace App\Http\Controllers\DMS;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DetectionController extends Controller
{
    /**
     * Halaman khusus Mode Deteksi Operator (DMS).
     * Terpisah dari halaman Kalibrasi.
     */
    public function index(): View
    {
        return view('dms.detection');
    }
}
