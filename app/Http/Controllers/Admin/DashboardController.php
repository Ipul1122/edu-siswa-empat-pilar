<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\QuizAttempt;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index()
    {
        $totalStudents = User::where('role', 'siswa')->count();
        $totalMaterials = Material::count();
        $totalQuizzes = Quiz::count();
        $totalAttempts = QuizAttempt::count();

        // Get 5 recent attempts with student and quiz relations
        $recentAttempts = QuizAttempt::with(['user', 'quiz'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents', 
            'totalMaterials', 
            'totalQuizzes', 
            'totalAttempts', 
            'recentAttempts'
        ));
    }
}
