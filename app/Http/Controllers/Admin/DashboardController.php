<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\ZoomSession;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index()
    {
        $totalStudents = User::query()->where('role', '=', 'siswa', 'and')->count('*');
        $totalRealQuizzes = Quiz::query()->where('type', '=', 'real', 'and')->count('*');
        $totalZoomSessions = ZoomSession::query()->count('*');
        $totalAttempts = QuizAttempt::query()
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->where('quizzes.type', 'real')
            ->count('*');

        // Get 5 recent attempts for Seleksi
        $recentAttempts = QuizAttempt::query()
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->where('quizzes.type', 'real')
            ->select('quiz_attempts.*')
            ->with(['user', 'quiz'])
            ->latest('quiz_attempts.created_at')
            ->take(5)
            ->get();

        // 1. Calculate global average scores per pillar for Chart.js (filtered by Seleksi)
        $pillarScores = QuizAttempt::query()->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id', 'inner', false)
            ->where('quizzes.type', 'real')
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

        // 2. Calculate daily seleksi attempts over the last 7 days
        $startDate = Carbon::today()->subDays(6)->startOfDay();
        $rawCounts = QuizAttempt::query()
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->where('quizzes.type', 'real')
            ->where('quiz_attempts.created_at', '>=', $startDate)
            ->selectRaw('DATE(quiz_attempts.created_at) as date_str, COUNT(*) as total')
            ->groupBy('date_str')
            ->pluck('total', 'date_str')
            ->toArray();

        $activityLast7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $dateKey = $d->format('Y-m-d');
            $formattedDate = $d->format('d M');
            $activityLast7Days[$formattedDate] = (int)($rawCounts[$dateKey] ?? 0);
        }

        return view('admin.dashboard', compact(
            'totalStudents', 
            'totalRealQuizzes',
            'totalZoomSessions',
            'totalAttempts', 
            'recentAttempts',
            'chartData',
            'activityLast7Days'
        ));
    }
}

