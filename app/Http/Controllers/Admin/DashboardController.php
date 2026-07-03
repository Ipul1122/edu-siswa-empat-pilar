<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Carbon\Carbon;

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

        // 1. Calculate global average scores per pillar for Chart.js
        $pillarScores = QuizAttempt::join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
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

        // 2. Calculate daily quiz attempts over the last 7 days
        $activityLast7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $formattedDate = Carbon::today()->subDays($i)->format('d M');
            
            $count = QuizAttempt::whereDate('created_at', $date)->count();
            $activityLast7Days[$formattedDate] = $count;
        }

        return view('admin.dashboard', compact(
            'totalStudents', 
            'totalMaterials', 
            'totalQuizzes', 
            'totalAttempts', 
            'recentAttempts',
            'chartData',
            'activityLast7Days'
        ));
    }
}
