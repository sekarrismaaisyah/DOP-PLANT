<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AlignmentController extends Controller
{
    /**
     * Menampilkan halaman Alignment (Peer Pressure Program Evaluation).
     */
    public function index(): View
    {
        return view('alignment.index');
    }
}
