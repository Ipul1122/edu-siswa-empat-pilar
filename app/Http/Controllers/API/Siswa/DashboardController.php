<?php

namespace App\Http\Controllers\API\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\QuizAttempt;
use App\Models\StudentProgress;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Get student dashboard metrics and overview.
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
            ->with('quiz:id,pillar,title,duration_minutes')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'quiz_id' => $attempt->quiz_id,
                    'quiz_title' => $attempt->quiz ? $attempt->quiz->title : 'Unknown Quiz',
                    'pillar' => $attempt->quiz ? $attempt->quiz->pillar : 'Unknown Pillar',
                    'score' => $attempt->score,
                    'correct_answers' => $attempt->correct_answers,
                    'total_questions' => $attempt->total_questions,
                    'duration_seconds_taken' => $attempt->duration_seconds_taken,
                    'created_at' => $attempt->created_at,
                ];
            });

        // 4. Find one unread material to recommend, if any
        $completedMaterialIds = StudentProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('material_id')
            ->toArray();

        $recommendedMaterial = Material::whereNotIn('id', $completedMaterialIds)
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'reading_progress' => [
                    'percentage' => $readingProgress,
                    'completed_count' => $completedMaterials,
                    'total_count' => $totalMaterials,
                ],
                'quiz_stats' => [
                    'average_score' => $averageScore,
                    'total_taken' => $totalQuizzesTaken,
                ],
                'recent_attempts' => $recentAttempts,
                'recommended_material' => $recommendedMaterial ? [
                    'id' => $recommendedMaterial->id,
                    'pillar' => $recommendedMaterial->pillar,
                    'title' => $recommendedMaterial->title,
                    'read_time' => $recommendedMaterial->read_time,
                ] : null,
            ]
        ]);
    }
}
