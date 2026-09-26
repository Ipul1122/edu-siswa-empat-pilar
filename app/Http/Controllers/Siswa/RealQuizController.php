<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Services\ExamShufflerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RealQuizController extends Controller
{
    protected ExamShufflerService $shuffler;

    public function __construct(ExamShufflerService $shuffler)
    {
        $this->shuffler = $shuffler;
    }

    /**
     * Display a listing of Real Materi quizzes grouped by pillar,
     * prioritizing province-specific packages if available for the student's region.
     */
    public function index()
    {
        $user = Auth::user();
        $provinceId = $user->province_id;

        // Retrieve real quizzes with province relation and question count
        $allQuizzes = Quiz::query()
            ->where('type', '=', 'real')
            ->with(['province'])
            ->withCount('questions')
            ->latest()
            ->get();

        // Level 2 (Question Pool / Paket Soal Wilayah):
        // Pick province-specific quiz package first, then national package (province_id IS NULL)
        $chosenQuizzes = collect();
        foreach ($allQuizzes->groupBy('pillar') as $pillar => $pillarQuizzes) {
            $provQuiz = $provinceId ? $pillarQuizzes->firstWhere('province_id', $provinceId) : null;
            $chosen = $provQuiz ?? $pillarQuizzes->firstWhere('province_id', null) ?? $pillarQuizzes->first();
            if ($chosen) {
                $chosenQuizzes->push($chosen);
            }
        }

        // Get attempt score for each quiz by this user
        $attempts = QuizAttempt::query()->where('user_id', '=', $user->id)
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
            $quiz->highest_score = $attempts[$quiz->id] ?? null;
            $quiz->is_completed = array_key_exists($quiz->id, $attempts);
            if (array_key_exists($quiz->pillar, $groupedQuizzes)) {
                $groupedQuizzes[$quiz->pillar][] = $quiz;
            }
        }

        return view('siswa.real_quizzes.index', compact('groupedQuizzes'));
    }

    /**
     * Show quiz details before starting.
     */
    public function show(Quiz $quiz)
    {
        if ($quiz->type !== 'real') {
            abort(404);
        }

        // Check if quiz is closed by admin
        if (!$quiz->is_active) {
            return redirect()->route('siswa.real-materi.index')
                ->with('error', 'Kuis Real Materi ini sedang ditutup oleh Admin dan tidak dapat diakses saat ini.');
        }

        // Check if already completed
        $exists = QuizAttempt::query()->where('user_id', '=', Auth::id())->where('quiz_id', '=', $quiz->id)->exists();
        if ($exists) {
            return redirect()->route('siswa.real-materi.index')
                ->with('error', 'Anda sudah mengerjakan kuis ini. Setiap kuis Real Materi hanya dapat dikerjakan 1 kali.');
        }

        $quiz->loadCount('questions');
        $quiz->load('province');
        return view('siswa.real_quizzes.show', compact('quiz'));
    }

    /**
     * Start the quiz (renders questions with deterministic seed shuffling).
     */
    public function start(Quiz $quiz)
    {
        if ($quiz->type !== 'real') {
            abort(404);
        }

        // Check if quiz is closed by admin
        if (!$quiz->is_active) {
            return redirect()->route('siswa.real-materi.index')
                ->with('error', 'Kuis Real Materi ini sedang ditutup oleh Admin.');
        }

        $user = Auth::user();

        // Check if already completed
        $exists = QuizAttempt::query()->where('user_id', '=', $user->id)->where('quiz_id', '=', $quiz->id)->exists();
        if ($exists) {
            return redirect()->route('siswa.real-materi.index')
                ->with('error', 'Anda sudah mengerjakan kuis ini. Setiap kuis Real Materi hanya dapat dikerjakan 1 kali.');
        }

        $quiz->load(['questions', 'province']);
        if ($quiz->questions->count() === 0) {
            return redirect()->route('siswa.real-materi.show', $quiz)
                ->with('error', 'Kuis ini belum memiliki soal. Silakan hubungi Admin.');
        }

        // Level 1: Deterministic Seed Shuffling based on user_id and province_id
        $shuffledQuestions = $this->shuffler->getShuffledQuestionsForUser(
            $quiz, 
            $user, 
            $quiz->randomize_questions ?? true, 
            $quiz->randomize_options ?? true
        );

        return view('siswa.real_quizzes.start', compact('quiz', 'shuffledQuestions'));
    }

    /**
     * Submit quiz answers and calculate results using validated I/O matching.
     */
    public function submit(Request $request, Quiz $quiz)
    {
        if ($quiz->type !== 'real') {
            abort(404);
        }

        // Check if quiz is closed by admin
        if (!$quiz->is_active) {
            return redirect()->route('siswa.real-materi.index')
                ->with('error', 'Kuis Real Materi ini telah ditutup oleh Admin.');
        }

        $user = Auth::user();

        // Acquire atomic lock to prevent race condition (double submit)
        $lock = Cache::lock('submit_real_quiz_' . $user->id . '_' . $quiz->id, 15);
        if (!$lock->get()) {
            return redirect()->route('siswa.real-materi.index')
                ->with('error', 'Jawaban Anda sedang diproses. Mohon jangan menekan tombol berulang kali.');
        }

        try {
            // Check if already completed
            $exists = QuizAttempt::query()->where('user_id', '=', $user->id)->where('quiz_id', '=', $quiz->id)->exists();
            if ($exists) {
                return redirect()->route('siswa.real-materi.index')
                    ->with('error', 'Anda sudah mengerjakan kuis ini.');
            }

            $quiz->load('questions');
            $submittedAnswers = $request->input('answers', []);

            // Evaluate answers deterministically
            $evaluation = $this->shuffler->evaluateAnswers($quiz, $submittedAnswers);
            $durationTaken = (int) $request->input('duration_seconds_taken', 0);
            $violationsCount = min(99, max(0, (int) $request->input('violations_count', 0)));

            // Save Attempt within database transaction
            $attempt = DB::transaction(function () use ($user, $quiz, $evaluation, $durationTaken, $violationsCount, $submittedAnswers) {
                return QuizAttempt::create([
                    'user_id' => $user->id,
                    'quiz_id' => $quiz->id,
                    'score' => $evaluation['score'],
                    'correct_answers' => $evaluation['correct_count'],
                    'total_questions' => $evaluation['total_count'],
                    'duration_seconds_taken' => $durationTaken,
                    'violations_count' => $violationsCount,
                    'answers' => $submittedAnswers,
                ]);
            });

            // Flash student's detailed choices to session as backup
            session()->flash('last_attempt_answers_' . $attempt->id, $submittedAnswers);

            return redirect()->route('siswa.real-materi.result', $attempt)
                ->with('success', 'Kuis Real Materi berhasil diselesaikan!');
        } finally {
            $lock->release();
        }
    }

    /**
     * Display the result of a quiz attempt with reviewed deterministic shuffled layout.
     */
    public function result(QuizAttempt $attempt)
    {
        $user = Auth::user();
        if ($attempt->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $attempt->load(['quiz.questions', 'quiz.province']);
        if ($attempt->quiz->type !== 'real') {
            abort(404);
        }
        
        // Retrieve student's choices permanently from database (or fallback to session)
        $studentAnswers = $attempt->answers ?? session('last_attempt_answers_' . $attempt->id) ?? [];

        // Reproduce the exact deterministic shuffled questions & options sequence seen during exam
        $shuffledQuestions = $this->shuffler->getShuffledQuestionsForUser(
            $attempt->quiz, 
            $user, 
            $attempt->quiz->randomize_questions ?? true, 
            $attempt->quiz->randomize_options ?? true
        );

        return view('siswa.real_quizzes.result', compact('attempt', 'studentAnswers', 'shuffledQuestions'));
    }
}
