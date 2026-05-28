<?php

namespace App\Http\Controllers\Hira;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HiraImprovementController extends Controller
{
    public function index(): View
    {
        return view('HiraImprovement.index');
    }
}
