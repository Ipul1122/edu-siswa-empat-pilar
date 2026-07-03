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
        $totalMaterials = Material::query()->count('*');
        $completedMaterials = StudentProgress::query()->where('user_id', $user->id)
            ->where('is_completed', true)
            ->count('*');

        $readingProgress = $totalMaterials > 0 
            ? round(($completedMaterials / $totalMaterials) * 100) 
            : 0;

        // 2. Fetch quiz statistics
        $attempts = QuizAttempt::query()->where('user_id', $user->id)->get();
        $averageScore = $attempts->count() > 0 
            ? round($attempts->avg('score'), 1) 
            : 0;
        $totalQuizzesTaken = $attempts->count();

        // 3. Fetch 3 recent quiz attempts
        $recentAttempts = QuizAttempt::query()->where('user_id', $user->id)
            ->with('quiz')
            ->latest()
            ->take(3)
            ->get();

        // 4. Find one unread material to recommend, if any
        $completedMaterialIds = StudentProgress::query()->where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('material_id')
            ->toArray();

        $recommendedMaterial = Material::query()->whereNotIn('id', $completedMaterialIds, 'and')
            ->first();

        // 5. Calculate average scores per pillar for Chart.js visualization
        $pillarScores = QuizAttempt::query()->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id', 'inner', false)
            ->where('quiz_attempts.user_id', $user->id)
            ->selectRaw('quizzes.pillar, AVG(quiz_attempts.score) as avg_score')
            ->groupBy('quizzes.pillar')
            ->pluck('avg_score', 'quizzes.pillar')
            ->toArray();

        $chartData = [
            'pancasila' => round($pillarScores['pancasila'] ?? 0),
            'uud_1945' => round($pillarScores['uud_1945'] ?? 0),
            'nkri' => round($pillarScores['nkri'] ?? 0),
            'bhinneka_tunggal_ika' => round($pillarScores['bhinneka_tunggal_ika'] ?? 0),
        ];

        return view('siswa.dashboard', compact(
            'readingProgress',
            'completedMaterials',
            'totalMaterials',
            'averageScore',
            'totalQuizzesTaken',
            'recentAttempts',
            'recommendedMaterial',
            'chartData'
        ));
    }
}
