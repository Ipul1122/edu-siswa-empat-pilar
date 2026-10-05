<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TutorialController extends Controller
{
    /**
     * Display the official test guidelines and interactive tutorial for schools.
     */
    public function index()
    {
        $school = Auth::user();
        return view('siswa.tutorial.index', compact('school'));
    }
}
