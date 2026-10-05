<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\ZoomSession;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display student dashboard focused exclusively on Seleksi, Ranking, Zoom, and Profile.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Calculate Seleksi progress
        $totalSeleksiPackages = Quiz::query()->where('type', '=', 'real', 'and')->count('*');
        
        $attempts = QuizAttempt::query()
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id', 'inner', false)
            ->where('quiz_attempts.user_id', $user->id)
            ->where('quizzes.type', 'real')
            ->select('quiz_attempts.*')
            ->with('quiz')
            ->get();

        $completedQuizIds = $attempts->pluck('quiz_id')->unique()->toArray();
        $completedSeleksiCount = count($completedQuizIds);
        $seleksiProgress = $totalSeleksiPackages > 0 
            ? round(($completedSeleksiCount / $totalSeleksiPackages) * 100) 
            : 0;

        $averageScore = $attempts->count() > 0 
            ? round($attempts->avg('score'), 1) 
            : 0;
        $totalSeleksiTaken = $attempts->count();

        // 2. Fetch 5 recent Seleksi attempts
        $recentAttempts = QuizAttempt::query()
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id', 'inner', false)
            ->where('quiz_attempts.user_id', $user->id)
            ->where('quizzes.type', 'real')
            ->select('quiz_attempts.*')
            ->with('quiz')
            ->latest('quiz_attempts.created_at')
            ->take(5)
            ->get();

        // 3. Find next available Seleksi package not yet completed
        $nextSeleksi = Quiz::query()
            ->where('type', 'real')
            ->where('is_active', true)
            ->whereNotIn('id', $completedQuizIds)
            ->where(function ($q) use ($user) {
                if ($user->province_id) {
                    $q->where('province_id', $user->province_id)
                      ->orWhereNull('province_id');
                } else {
                    $q->whereNull('province_id');
                }
            })
            ->withCount('questions')
            ->orderBy('id')
            ->first();

        // 4. Calculate student's national rank
        $rankedUserIds = QuizAttempt::query()
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->where('quizzes.type', 'real')
            ->select('quiz_attempts.user_id')
            ->selectRaw('SUM(quiz_attempts.score) as total_score')
            ->groupBy('quiz_attempts.user_id')
            ->orderByDesc('total_score')
            ->pluck('user_id')
            ->toArray();

        $rankPosition = array_search($user->id, $rankedUserIds);
        $studentRank = $rankPosition !== false ? $rankPosition + 1 : '-';

        // 5. Active / upcoming Zoom sessions
        $activeZoomCount = ZoomSession::query()
            ->where('is_active', true)
            ->count('*');

        $upcomingZoom = ZoomSession::query()
            ->where('is_active', true)
            ->where('start_time', '>=', now()->subHours(2))
            ->orderBy('start_time')
            ->first();

        // 6. Calculate average scores per pillar for Chart.js visualization
        $pillarScores = QuizAttempt::query()
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id', 'inner', false)
            ->where('quiz_attempts.user_id', $user->id)
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

        return view('siswa.dashboard', compact(
            'totalSeleksiPackages',
            'completedSeleksiCount',
            'seleksiProgress',
            'averageScore',
            'totalSeleksiTaken',
            'recentAttempts',
            'nextSeleksi',
            'studentRank',
            'activeZoomCount',
            'upcomingZoom',
            'chartData'
        ));
    }
}
