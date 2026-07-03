<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use App\Models\StudentProgress;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    /**
     * Display the student leaderboard.
     */
    public function index()
    {
        $currentUserId = Auth::id();

        // Fetch all students with completed progress count and their attempts
        $students = User::query()->where('role', 'siswa')
            ->withCount(['progress as completed_progress_count' => function ($query) {
                $query->where('is_completed', true);
            }])
            ->with(['attempts'])
            ->get();

        foreach ($students as $student) {
            // Group attempts by quiz_id to get the highest score for each unique quiz
            $highestQuizScores = [];
            foreach ($student->attempts as $attempt) {
                $quizId = $attempt->quiz_id;
                if (!isset($highestQuizScores[$quizId]) || $attempt->score > $highestQuizScores[$quizId]) {
                    $highestQuizScores[$quizId] = $attempt->score;
                }
            }

            $totalQuizScore = array_sum($highestQuizScores);
            $materialsRead = $student->completed_progress_count;
            
            // Formula: (Materials completed * 10) + Total highest score of each quiz
            $student->points = ($materialsRead * 10) + $totalQuizScore;
            $student->total_quiz_score = $totalQuizScore;
            $student->materials_read = $materialsRead;
            
            // Average score calculation
            $student->average_score = $student->attempts->count() > 0 
                ? round($student->attempts->avg('score'), 1) 
                : 0;
            $student->quizzes_count = $student->attempts->count();
        }

        // Sort by points desc, then by average_score desc, then by name asc
        $leaderboard = $students->sort(function ($a, $b) {
            if ($b->points !== $a->points) {
                return $b->points <=> $a->points;
            }
            if ($b->average_score !== $a->average_score) {
                return $b->average_score <=> $a->average_score;
            }
            return strcmp($a->name, $b->name);
        })->values();

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
