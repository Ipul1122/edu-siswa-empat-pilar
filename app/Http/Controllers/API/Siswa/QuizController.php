<?php

namespace App\Http\Controllers\API\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $highestScores = QuizAttempt::where('user_id', $user->id)
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
            $data = [
                'id' => $quiz->id,
                'pillar' => $quiz->pillar,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'duration_minutes' => $quiz->duration_minutes,
                'questions_count' => $quiz->questions_count,
                'highest_score' => $highestScores[$quiz->id] ?? null,
                'created_at' => $quiz->created_at,
            ];
            
            if (array_key_exists($quiz->pillar, $groupedQuizzes)) {
                $groupedQuizzes[$quiz->pillar][] = $data;
            }
        }

        return response()->json([
            'status' => 'success',
            'quizzes' => $groupedQuizzes
        ]);
    }

    /**
     * Show quiz details.
     */
    public function show(Quiz $quiz)
    {
        $quiz->loadCount('questions');

        return response()->json([
            'status' => 'success',
            'quiz' => [
                'id' => $quiz->id,
                'pillar' => $quiz->pillar,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'duration_minutes' => $quiz->duration_minutes,
                'questions_count' => $quiz->questions_count,
                'created_at' => $quiz->created_at,
            ]
        ]);
    }

    /**
     * Start the quiz (returns questions *without* exposing correct answers or explanations).
     */
    public function start(Quiz $quiz)
    {
        $questions = $quiz->questions;

        if ($questions->count() === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kuis ini belum memiliki soal. Silakan hubungi Admin.'
            ], 400);
        }

        // Map questions to hide correct option and explanation to prevent cheating
        $securedQuestions = $questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'option_a' => $question->option_a,
                'option_b' => $question->option_b,
                'option_c' => $question->option_c,
                'option_d' => $question->option_d,
                'option_e' => $question->option_e,
            ];
        });

        return response()->json([
            'status' => 'success',
            'quiz' => [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'duration_minutes' => $quiz->duration_minutes,
            ],
            'questions' => $securedQuestions
        ]);
    }

    /**
     * Submit quiz answers, calculate score, save attempt, and return results.
     */
    public function submit(Request $request, Quiz $quiz)
    {
        $user = Auth::user();
        $questions = $quiz->questions;
        
        if ($questions->count() === 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kuis ini tidak memiliki soal.'
            ], 400);
        }

        $submittedAnswers = $request->input('answers', []);
        
        $correctAnswersCount = 0;
        $totalQuestionsCount = $questions->count();
        $details = [];

        // Match answers and build detailed response
        foreach ($questions as $question) {
            $submitted = $submittedAnswers[$question->id] ?? null;
            $isCorrect = ($submitted && strtolower($submitted) === strtolower($question->correct_option));
            
            if ($isCorrect) {
                $correctAnswersCount++;
            }

            $details[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'options' => [
                    'a' => $question->option_a,
                    'b' => $question->option_b,
                    'c' => $question->option_c,
                    'd' => $question->option_d,
                    'e' => $question->option_e,
                ],
                'submitted_answer' => $submitted,
                'correct_option' => $question->correct_option,
                'is_correct' => $isCorrect,
                'explanation' => $question->explanation,
            ];
        }

        $score = round(($correctAnswersCount / $totalQuestionsCount) * 100);
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

        return response()->json([
            'status' => 'success',
            'message' => 'Kuis berhasil diselesaikan!',
            'attempt' => [
                'id' => $attempt->id,
                'quiz_id' => $attempt->quiz_id,
                'score' => $attempt->score,
                'correct_answers' => $attempt->correct_answers,
                'total_questions' => $attempt->total_questions,
                'duration_seconds_taken' => $attempt->duration_seconds_taken,
                'created_at' => $attempt->created_at,
            ],
            'details' => $details
        ]);
    }

    /**
     * Get quiz attempts history for the authenticated student.
     */
    public function attemptsHistory()
    {
        $user = Auth::user();

        $attempts = QuizAttempt::where('user_id', $user->id)
            ->with('quiz:id,title,pillar')
            ->latest()
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

        return response()->json([
            'status' => 'success',
            'attempts' => $attempts
        ]);
    }

    /**
     * Get detailed result of a specific quiz attempt.
     */
    public function attemptResult(QuizAttempt $attempt)
    {
        $user = Auth::user();

        if ($attempt->user_id !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak.'
            ], 403);
        }

        $attempt->load(['quiz.questions']);
        $questions = $attempt->quiz->questions;
        
        $details = $questions->map(function ($question) {
            return [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'options' => [
                    'a' => $question->option_a,
                    'b' => $question->option_b,
                    'c' => $question->option_c,
                    'd' => $question->option_d,
                    'e' => $question->option_e,
                ],
                'correct_option' => $question->correct_option,
                'explanation' => $question->explanation,
            ];
        });

        return response()->json([
            'status' => 'success',
            'attempt' => [
                'id' => $attempt->id,
                'quiz_id' => $attempt->quiz_id,
                'quiz_title' => $attempt->quiz ? $attempt->quiz->title : 'Unknown Quiz',
                'pillar' => $attempt->quiz ? $attempt->quiz->pillar : 'Unknown Pillar',
                'score' => $attempt->score,
                'correct_answers' => $attempt->correct_answers,
                'total_questions' => $attempt->total_questions,
                'duration_seconds_taken' => $attempt->duration_seconds_taken,
                'created_at' => $attempt->created_at,
            ],
            'questions' => $details
        ]);
    }
}
