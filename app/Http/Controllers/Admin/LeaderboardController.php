<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Display the student leaderboard for administrators with Province and Regency filters.
     */
    public function index(Request $request)
    {
        $selectedProvinceId = $request->input('province_id');
        $selectedRegencyId = $request->input('regency_id');
        $search = trim($request->input('search', ''));

        $provinces = Province::orderBy('name')->get();
        $regencies = $selectedProvinceId 
            ? Regency::where('province_id', $selectedProvinceId)->orderBy('name')->get()
            : collect();

        $allRegencies = Regency::orderBy('name')->get(['id', 'province_id', 'name', 'type']);

        $progressSub = DB::table('student_progress')
            ->select('user_id')
            ->selectRaw('COUNT(*) as completed_count')
            ->where('is_completed', true)
            ->groupBy('user_id');

        $maxAttemptsSub = DB::table('quiz_attempts')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->where('quizzes.type', 'real')
            ->select('quiz_attempts.user_id', 'quiz_attempts.quiz_id')
            ->selectRaw('MAX(quiz_attempts.score) as max_score')
            ->groupBy('quiz_attempts.user_id', 'quiz_attempts.quiz_id');

        $quizScoresSub = DB::table($maxAttemptsSub, 'max_attempts')
            ->select('user_id')
            ->selectRaw('SUM(max_score) as total_score')
            ->groupBy('user_id');

        $attemptsStatsSub = DB::table('quiz_attempts')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->where('quizzes.type', 'real')
            ->select('quiz_attempts.user_id')
            ->selectRaw('ROUND(AVG(quiz_attempts.score), 1) as avg_score')
            ->selectRaw('COUNT(*) as attempts_count')
            ->groupBy('quiz_attempts.user_id');

        $query = User::query()
            ->select('users.*')
            ->selectRaw('COALESCE(progress_counts.completed_count, 0) as materials_read')
            ->selectRaw('COALESCE(quiz_scores.total_score, 0) as total_quiz_score')
            ->selectRaw('(COALESCE(progress_counts.completed_count, 0) * 10 + COALESCE(quiz_scores.total_score, 0)) as points')
            ->selectRaw('COALESCE(attempts_stats.avg_score, 0) as average_score')
            ->selectRaw('COALESCE(attempts_stats.attempts_count, 0) as quizzes_count')
            ->leftJoinSub($progressSub, 'progress_counts', 'progress_counts.user_id', '=', 'users.id')
            ->leftJoinSub($quizScoresSub, 'quiz_scores', 'quiz_scores.user_id', '=', 'users.id')
            ->leftJoinSub($attemptsStatsSub, 'attempts_stats', 'attempts_stats.user_id', '=', 'users.id')
            ->with(['province', 'regency'])
            ->where('users.role', 'siswa');

        if ($selectedProvinceId) {
            $query->where('users.province_id', $selectedProvinceId);
        }

        if ($selectedRegencyId) {
            $query->where('users.regency_id', $selectedRegencyId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.school_name', 'like', "%{$search}%");
            });
        }

        $leaderboard = $query
            ->orderByDesc('points')
            ->orderByDesc('average_score')
            ->orderBy('users.name')
            ->get();

        // Calculate KPI Metrics
        $totalRankedStudents = $leaderboard->count();
        $highestPoints = $totalRankedStudents > 0 ? $leaderboard->max('points') : 0;
        $averageScore = $totalRankedStudents > 0 ? round($leaderboard->avg('average_score'), 1) : 0;
        $totalRealMateri = Quiz::where('type', 'real')->count();

        // Podium (Top 3)
        $topThree = $leaderboard->take(3);

        return view('admin.leaderboard.index', compact(
            'leaderboard',
            'topThree',
            'totalRankedStudents',
            'highestPoints',
            'averageScore',
            'totalRealMateri',
            'provinces',
            'regencies',
            'allRegencies',
            'selectedProvinceId',
            'selectedRegencyId',
            'search'
        ));
    }
}
