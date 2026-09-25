<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Collection;

class ExamShufflerService
{
    /**
     * Generate deterministic integer seed from strings, numbers, or nulls.
     */
    public static function generateSeed(int|string|null ...$parts): int
    {
        $clean = array_map(fn($p) => $p ?? 0, $parts);
        return abs(crc32(implode('_', $clean)));
    }

    /**
     * Deterministic Fisher-Yates shuffle on an array using an explicit seed,
     * without modifying the global PHP random state.
     */
    public static function deterministicShuffle(array $items, int $seed): array
    {
        $count = count($items);
        if ($count <= 1) {
            return $items;
        }

        $result = array_values($items);
        $s = $seed;

        // Linear Congruential Generator (LCG) parameters (Knuth)
        for ($i = $count - 1; $i > 0; $i--) {
            $s = ($s * 1103515245 + 12345) & 0x7FFFFFFF;
            $j = $s % ($i + 1);

            $temp = $result[$i];
            $result[$i] = $result[$j];
            $result[$j] = $temp;
        }

        return $result;
    }

    /**
     * Shuffle quiz questions and their options deterministically for a student.
     *
     * @param Quiz $quiz
     * @param User $user
     * @param bool $shuffleQuestions
     * @param bool $shuffleOptions
     * @return Collection
     */
    public function getShuffledQuestionsForUser(
        Quiz $quiz, 
        User $user, 
        bool $shuffleQuestions = true, 
        bool $shuffleOptions = true
    ): Collection {
        // Base seed combining quiz_id, user_id, and province_id
        $provinceId = $user->province_id ?? 0;
        $baseSeed = self::generateSeed('exam', $quiz->id, $user->id, $provinceId);

        $questions = $quiz->questions;

        // 1. Deterministic Question Shuffling
        if ($shuffleQuestions && $questions->count() > 1) {
            $shuffledArray = self::deterministicShuffle($questions->all(), $baseSeed);
            $questions = collect($shuffledArray);
        }

        // 2. Deterministic Option Shuffling for each Question
        return $questions->map(function ($question) use ($user, $quiz, $provinceId, $shuffleOptions) {
            $rawOptions = [
                ['key' => 'a', 'text' => $question->option_a],
                ['key' => 'b', 'text' => $question->option_b],
                ['key' => 'c', 'text' => $question->option_c],
                ['key' => 'd', 'text' => $question->option_d],
                ['key' => 'e', 'text' => $question->option_e],
            ];

            // Filter out empty options if question has fewer than 5
            $validOptions = array_values(array_filter($rawOptions, fn($opt) => $opt['text'] !== null && $opt['text'] !== ''));

            if ($shuffleOptions && count($validOptions) > 1) {
                $optionSeed = self::generateSeed('opts', $quiz->id, $question->id, $user->id, $provinceId);
                $shuffledOptions = self::deterministicShuffle($validOptions, $optionSeed);
            } else {
                $shuffledOptions = $validOptions;
            }

            // Assign displayed label (A, B, C, D, E) to each option in its shuffled order
            foreach ($shuffledOptions as $idx => &$opt) {
                $opt['display_label'] = chr(65 + $idx); // 'A', 'B', 'C', 'D', 'E'
            }
            unset($opt);

            // Clone question to avoid mutating cached model relations directly
            $q = clone $question;
            $q->shuffled_options = $shuffledOptions;
            return $q;
        });
    }

    /**
     * Map student's submitted answers to verify against correct options.
     * Supports both direct key submission (e.g. 'a', 'b', 'c', 'd', 'e')
     * and array format with metadata.
     *
     * @param Quiz $quiz
     * @param array $submittedAnswers
     * @return array [ 'correct_count' => int, 'total_count' => int, 'score' => int, 'details' => array ]
     */
    public function evaluateAnswers(Quiz $quiz, array $submittedAnswers): array
    {
        $questions = $quiz->questions;
        $totalQuestionsCount = $questions->count();
        $correctAnswersCount = 0;
        $details = [];

        foreach ($questions as $question) {
            $submitted = $submittedAnswers[$question->id] ?? null;
            
            // In case submitted answer is structured { "selected": "b", "display": "A" }
            if (is_array($submitted)) {
                $selectedKey = $submitted['selected'] ?? null;
            } else {
                $selectedKey = $submitted;
            }

            $isCorrect = false;
            if ($selectedKey !== null && strtolower(trim((string)$selectedKey)) === strtolower(trim((string)$question->correct_option))) {
                $isCorrect = true;
                $correctAnswersCount++;
            }

            $details[$question->id] = [
                'selected' => $selectedKey,
                'correct' => $question->correct_option,
                'is_correct' => $isCorrect,
            ];
        }

        $score = $totalQuestionsCount > 0 
            ? (int) round(($correctAnswersCount / $totalQuestionsCount) * 100) 
            : 0;

        return [
            'correct_count' => $correctAnswersCount,
            'total_count' => $totalQuestionsCount,
            'score' => $score,
            'details' => $details,
        ];
    }
}
