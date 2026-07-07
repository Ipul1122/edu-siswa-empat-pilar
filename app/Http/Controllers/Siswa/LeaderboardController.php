<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use App\Models\StudentProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Display the student leaderboard.
     */
    public function index()
    {
        $currentUserId = Auth::id();

        // Fetch student leaderboard using optimized database query and caching
        $leaderboard = Cache::remember('leaderboard_data', 300, function () {
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

            return User::query()
                ->select('users.*')
                ->selectRaw('COALESCE(progress_counts.completed_count, 0) as materials_read')
                ->selectRaw('COALESCE(quiz_scores.total_score, 0) as total_quiz_score')
                ->selectRaw('(COALESCE(progress_counts.completed_count, 0) * 10 + COALESCE(quiz_scores.total_score, 0)) as points')
                ->selectRaw('COALESCE(attempts_stats.avg_score, 0) as average_score')
                ->selectRaw('COALESCE(attempts_stats.attempts_count, 0) as quizzes_count')
                ->leftJoinSub($progressSub, 'progress_counts', 'progress_counts.user_id', '=', 'users.id')
                ->leftJoinSub($quizScoresSub, 'quiz_scores', 'quiz_scores.user_id', '=', 'users.id')
                ->leftJoinSub($attemptsStatsSub, 'attempts_stats', 'attempts_stats.user_id', '=', 'users.id')
                ->where('users.role', 'siswa')
                ->orderByDesc('points')
                ->orderByDesc('average_score')
                ->orderBy('users.name')
                ->get();
        });

        // Find current user's rank
        $currentUserRank = null;
        $currentUserData = null;
        foreach ($leaderboard as $index => $student) {
            if ($student->id === $currentUserId) {
                $currentUserRank = $index + 1;
                $currentUserData = $student;
                break;
            }
        }

        return view('siswa.leaderboard.index', compact('leaderboard', 'currentUserRank', 'currentUserData'));
    }
}
