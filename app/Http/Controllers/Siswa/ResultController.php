<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    /**
     * Display the authenticated school's private real-time exam results.
     */
    public function index()
    {
        $school = Auth::user();

        // Fetch all attempts by this school exclusively
        $attempts = QuizAttempt::query()
            ->where('user_id', $school->id)
            ->with(['quiz.province', 'retestGrantedBy'])
            ->latest()
            ->get();

        $latestAttempt = $attempts->first();

        return view('siswa.results.index', compact('school', 'attempts', 'latestAttempt'));
    }
}
