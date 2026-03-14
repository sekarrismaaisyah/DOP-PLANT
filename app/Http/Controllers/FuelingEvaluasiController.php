<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FuelingEvaluasiController extends Controller
{
    /**
     * Menampilkan halaman Fueling Evaluasi.
     */
    public function index()
    {
        return view('fuelingEvaluasi.index');
    }
}
