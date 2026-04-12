<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class FuelingEvaluasiController extends Controller
{
    /**
     * Menampilkan halaman Fueling Evaluasi.
     */
    public function index(): View
    {
        return view('fuelingEvaluasi.index');
    }

    /**
     * Menampilkan Fleet Operations Compliance Dashboard.
     */
    public function dashboard(): View
    {
        return view('fuelingEvaluasi.dashboard');
    }

    /**
     * Menampilkan halaman embed Tableau untuk HSE Division.
     */
    public function tableau(): View
    {
        return view('fuelingEvaluasi.tableau');
    }
}
