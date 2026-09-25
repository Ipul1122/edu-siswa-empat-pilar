<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Services\ExamShufflerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    protected ExamShufflerService $shuffler;

    public function __construct(ExamShufflerService $shuffler)
    {
        $this->shuffler = $shuffler;
    }

    /**
     * Display a listing of quizzes grouped by pillar,
     * prioritizing province-specific packages if available for the student.
     */
    public function index()
    {
        $user = Auth::user();
        $provinceId = $user->province_id;

        // Get practice quizzes with questions count & province
        $allQuizzes = Quiz::query()
            ->where('type', '=', 'practice')
            ->with(['province'])
            ->withCount('questions')
            ->latest()
            ->get();

        // Level 2: Pick province-specific quiz package first, then national package
        $chosenQuizzes = collect();
        foreach ($allQuizzes->groupBy('pillar') as $pillar => $pillarQuizzes) {
            $provQuiz = $provinceId ? $pillarQuizzes->firstWhere('province_id', $provinceId) : null;
            $chosen = $provQuiz ?? $pillarQuizzes->firstWhere('province_id', null) ?? $pillarQuizzes->first();
            if ($chosen) {
                $chosenQuizzes->push($chosen);
            }
        }

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
            'bhinneka_tunggal_ika' => [],
            'twk_kedinasan' => []
        ];

        foreach ($chosenQuizzes as $quiz) {
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
        if ($quiz->type !== 'practice') {
            abort(404);
        }
        $quiz->loadCount('questions');
        $quiz->load('province');
        return view('siswa.quizzes.show', compact('quiz'));
    }

    /**
     * Start the quiz (renders the questions with deterministic seed shuffling).
     */
    public function start(Quiz $quiz)
    {
        if ($quiz->type !== 'practice') {
            abort(404);
        }
        $quiz->load(['questions', 'province']);
        if ($quiz->questions->count() === 0) {
            return redirect()->route('siswa.quizzes.show', $quiz)
                ->with('error', 'Kuis ini belum memiliki soal. Silakan hubungi Admin.');
        }

        $user = Auth::user();

        // Level 1: Deterministic Seed Shuffling based on user_id and province_id
        $shuffledQuestions = $this->shuffler->getShuffledQuestionsForUser(
            $quiz, 
            $user, 
            $quiz->randomize_questions ?? true, 
            $quiz->randomize_options ?? true
        );

        return view('siswa.quizzes.start', compact('quiz', 'shuffledQuestions'));
    }

    /**
     * Submit quiz answers and calculate results with validated I/O matching.
     */
    public function submit(Request $request, Quiz $quiz)
    {
        if ($quiz->type !== 'practice') {
            abort(404);
        }
        $user = Auth::user();
        $quiz->load('questions');
        $submittedAnswers = $request->input('answers', []);
        
        $evaluation = $this->shuffler->evaluateAnswers($quiz, $submittedAnswers);
        $durationTaken = (int) $request->input('duration_seconds_taken', 0);

        // Save Attempt
        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $evaluation['score'],
            'correct_answers' => $evaluation['correct_count'],
            'total_questions' => $evaluation['total_count'],
            'duration_seconds_taken' => $durationTaken,
            'answers' => $submittedAnswers,
        ]);

        // Flash student's detailed choices to session as backup
        session()->flash('last_attempt_answers_' . $attempt->id, $submittedAnswers);

        return redirect()->route('siswa.quizzes.result', $attempt)
            ->with('success', 'Kuis berhasil diselesaikan!');
    }

    /**
     * Display the result of a quiz attempt with deterministic shuffled sequence review.
     */
    public function result(QuizAttempt $attempt)
    {
        $user = Auth::user();
        if ($attempt->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $attempt->load(['quiz.questions', 'quiz.province']);
        
        // Retrieve student's choices permanently from database (or fallback to session)
        $studentAnswers = $attempt->answers ?? session('last_attempt_answers_' . $attempt->id) ?? [];

        // Reproduce the exact deterministic shuffled questions & options sequence seen during exam
        $shuffledQuestions = $this->shuffler->getShuffledQuestionsForUser(
            $attempt->quiz, 
            $user, 
            $attempt->quiz->randomize_questions ?? true, 
            $attempt->quiz->randomize_options ?? true
        );

        return view('siswa.quizzes.result', compact('attempt', 'studentAnswers', 'shuffledQuestions'));
    }
}
