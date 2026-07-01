<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\QuizAttempt;
use App\Models\StudentProgress;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display student dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Calculate reading progress percentage
        $totalMaterials = Material::count();
        $completedMaterials = StudentProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();

        $readingProgress = $totalMaterials > 0 
            ? round(($completedMaterials / $totalMaterials) * 100) 
            : 0;

        // 2. Fetch quiz statistics
        $attempts = QuizAttempt::where('user_id', $user->id)->get();
        $averageScore = $attempts->count() > 0 
            ? round($attempts->avg('score'), 1) 
            : 0;
        $totalQuizzesTaken = $attempts->count();

        // 3. Fetch 3 recent quiz attempts
        $recentAttempts = QuizAttempt::where('user_id', $user->id)
            ->with('quiz')
            ->latest()
            ->take(3)
            ->get();

        // 4. Find one unread material to recommend, if any
        $completedMaterialIds = StudentProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('material_id')
            ->toArray();

        $recommendedMaterial = Material::whereNotIn('id', $completedMaterialIds)
            ->first();

        return view('siswa.dashboard', compact(
            'readingProgress',
            'completedMaterials',
            'totalMaterials',
            'averageScore',
            'totalQuizzesTaken',
            'recentAttempts',
            'recommendedMaterial'
        ));
    }
}
