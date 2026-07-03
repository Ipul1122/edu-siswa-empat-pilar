<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class QuizController extends Controller
{
    /**
     * Display a listing of quizzes grouped by pillar.
     */
    public function index()
    {
        $user = Auth::user();

        // Get quizzes with questions count
        $quizzes = Quiz::withCount('questions')->get();

        // Get highest attempt score for each quiz by this user
        $highestScores = QuizAttempt::query()->where('user_id', $user->id)
            ->selectRaw('quiz_id, max(score) as max_score')
            ->groupBy('quiz_id')
            ->pluck('max_score', 'quiz_id')
            ->toArray();

        $groupedQuizzes = [
            'pancasila' => [],
            'uud_1945' => [],
            'nkri' => [],
            'bhinneka_tunggal_ika' => []
        ];

        foreach ($quizzes as $quiz) {
            $quiz->highest_score = $highestScores[$quiz->id] ?? null;
            if (array_key_exists($quiz->pillar, $groupedQuizzes)) {
                $groupedQuizzes[$quiz->pillar][] = $quiz;
            }
        }

        return view('siswa.quizzes.index', compact('groupedQuizzes'));
    }

    /**
     * Show quiz details before starting.
     */
    public function show(Quiz $quiz)
    {
        $quiz->loadCount('questions');
        return view('siswa.quizzes.show', compact('quiz'));
    }

    /**
     * Start the quiz (renders the questions with timer).
     */
    public function start(Quiz $quiz)
    {
        $quiz->load('questions');
        if ($quiz->questions->count() === 0) {
            return redirect()->route('siswa.quizzes.show', $quiz)
                ->with('error', 'Kuis ini belum memiliki soal. Silakan hubungi Admin.');
        }

        return view('siswa.quizzes.start', compact('quiz'));
    }

    /**
     * Submit quiz answers and calculate results.
     */
    public function submit(Request $request, Quiz $quiz)
    {
        $user = Auth::user();
        $questions = $quiz->questions;
        $submittedAnswers = $request->input('answers', []);
        
        $correctAnswersCount = 0;
        $totalQuestionsCount = $questions->count();

        // Match answers
        foreach ($questions as $question) {
            $submitted = $submittedAnswers[$question->id] ?? null;
            if ($submitted && strtolower($submitted) === strtolower($question->correct_option)) {
                $correctAnswersCount++;
            }
        }

        $score = $totalQuestionsCount > 0 
            ? round(($correctAnswersCount / $totalQuestionsCount) * 100) 
            : 0;

        $durationTaken = (int) $request->input('duration_seconds_taken', 0);

        // Save Attempt
        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'correct_answers' => $correctAnswersCount,
            'total_questions' => $totalQuestionsCount,
            'duration_seconds_taken' => $durationTaken,
        ]);

        // Clear leaderboard cache
        Cache::forget('leaderboard_data');

        // Flash student's detailed choices to the session for review on the next screen
        session()->flash('last_attempt_answers_' . $attempt->id, $submittedAnswers);

        return redirect()->route('siswa.quizzes.result', $attempt)
            ->with('success', 'Kuis berhasil diselesaikan!');
    }

    /**
     * Display the result of a quiz attempt.
     */
    public function result(QuizAttempt $attempt)
    {
        $user = Auth::user();
        if ($attempt->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $attempt->load(['quiz.questions']);
        
        // Retrieve student's choices from session
        $studentAnswers = session('last_attempt_answers_' . $attempt->id) ?? [];

        return view('siswa.quizzes.result', compact('attempt', 'studentAnswers'));
    }
}
