<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Province;
use App\Models\QuizAttempt;
use App\Services\ExamShufflerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

echo "===============================================================\n";
echo "    VALIDASI I/O 2.2: PENGACAKAN SOAL PER PROVINSI & PAKET     \n";
echo "===============================================================\n\n";

$shuffler = new ExamShufflerService();
$passed = 0;
$failed = 0;

/**
 * Assert condition and output test status
 *
 * @param bool $condition
 * @param string $testName
 * @return void
 */
function assertCondition(bool $condition, string $testName): void {
    global $passed, $failed;
    if ($condition) {
        echo " [PASS] $testName\n";
        $passed++;
    } else {
        echo " [FAIL] $testName\n";
        $failed++;
    }
}

// Ensure database has at least 1 quiz with questions
$quiz = Quiz::with('questions')->withCount('questions')->orderByDesc('questions_count')->first();

if (!$quiz || $quiz->questions->count() < 2) {
    die("Error: Tidak ada kuis dengan minimal 2 butir soal di database untuk pengujian.\n");
}

echo "Menggunakan Kuis ID: {$quiz->id} ('{$quiz->title}') dengan {$quiz->questions->count()} butir soal.\n\n";

// Dummy mock users for testing
$studentAceh1 = new User(['name' => 'Siswa Aceh 1', 'province_id' => 11, 'role' => 'siswa']);
$studentAceh1->id = 101;
$studentAceh2 = new User(['name' => 'Siswa Aceh 2', 'province_id' => 11, 'role' => 'siswa']);
$studentAceh2->id = 102;
$studentJabar = new User(['name' => 'Siswa Jawa Barat', 'province_id' => 32, 'role' => 'siswa']);
$studentJabar->id = 103;

// ------------------------------------------------------------------------------------------------
// 1. TEST DETERMINISTIC REPRODUCIBILITY (Level 1)
// ------------------------------------------------------------------------------------------------
echo "--- UJI 1: KONSISTENSI DETERMINISTIK (DETERMINISTIC SEED REPRODUCIBILITY) ---\n";
$run1 = $shuffler->getShuffledQuestionsForUser($quiz, $studentAceh1, true, true);
$run2 = $shuffler->getShuffledQuestionsForUser($quiz, $studentAceh1, true, true);
$run3 = $shuffler->getShuffledQuestionsForUser($quiz, $studentAceh1, true, true);

$order1 = $run1->pluck('id')->all();
$order2 = $run2->pluck('id')->all();
$order3 = $run3->pluck('id')->all();

assertCondition($order1 === $order2 && $order2 === $order3, "Urutan nomor soal 100% konsisten antar-refresh untuk siswa yang sama.");

// Check options reproducibility
$optRun1 = $run1->first()->shuffled_options;
$optRun2 = $run2->first()->shuffled_options;
$optOrder1 = array_column($optRun1, 'key');
$optOrder2 = array_column($optRun2, 'key');

assertCondition($optOrder1 === $optOrder2, "Urutan opsi pilihan jawaban 100% konsisten antar-refresh untuk siswa yang sama.");
echo "\n";

// ------------------------------------------------------------------------------------------------
// 2. TEST CROSS-STUDENT ANTI-CHEAT SCRAMBLING
// ------------------------------------------------------------------------------------------------
echo "--- UJI 2: PENGACAKAN ANTI-KEBOCORAN ANTAR-SISWA & ANTAR-PROVINSI ---\n";
$shuffledAceh1 = $shuffler->getShuffledQuestionsForUser($quiz, $studentAceh1, true, true);
$shuffledAceh2 = $shuffler->getShuffledQuestionsForUser($quiz, $studentAceh2, true, true);
$shuffledJabar = $shuffler->getShuffledQuestionsForUser($quiz, $studentJabar, true, true);

$orderAceh1 = $shuffledAceh1->pluck('id')->all();
$orderAceh2 = $shuffledAceh2->pluck('id')->all();
$orderJabar = $shuffledJabar->pluck('id')->all();

assertCondition($orderAceh1 !== $orderAceh2, "Urutan nomor soal siswa A (Aceh) berbeda dengan siswa B (Aceh).");
assertCondition($orderAceh1 !== $orderJabar, "Urutan nomor soal siswa Aceh berbeda dengan siswa Jawa Barat.");

// Check options differ
$firstQAceh1 = $shuffledAceh1->first();
$matchingQInJabar = $shuffledJabar->firstWhere('id', $firstQAceh1->id);
$optsAceh1 = array_column($firstQAceh1->shuffled_options, 'key');
$optsJabar = array_column($matchingQInJabar->shuffled_options, 'key');

echo "  Urutan Opsi Siswa 1 pada Soal ID {$firstQAceh1->id}: " . implode(',', $optsAceh1) . "\n";
echo "  Urutan Opsi Siswa 3 pada Soal ID {$firstQAceh1->id}: " . implode(',', $optsJabar) . "\n";
assertCondition(!empty($optsAceh1) && count($optsAceh1) === 5, "Setiap soal menyajikan 5 opsi pilihan terstruktur.");
echo "\n";

// ------------------------------------------------------------------------------------------------
// 3. TEST ANSWER EVALUATION I/O VALIDATION
// ------------------------------------------------------------------------------------------------
echo "--- UJI 3: VALIDASI I/O PENILAIAN JAWABAN (EVALUATE ANSWERS) ---\n";
// Case A: 100% Correct Answers
$allCorrectPayload = [];
foreach ($quiz->questions as $q) {
    $allCorrectPayload[$q->id] = $q->correct_option;
}
$eval100 = $shuffler->evaluateAnswers($quiz, $allCorrectPayload);
assertCondition(
    $eval100['score'] === 100 && $eval100['correct_count'] === $quiz->questions->count(),
    "Payload 100% jawaban benar menghasilkan skor 100 dan jumlah benar tepat ({$eval100['correct_count']}/{$eval100['total_count']})."
);

// Case B: 0% Correct Answers (all wrong)
$allWrongPayload = [];
foreach ($quiz->questions as $q) {
    // Pick any option that is NOT the correct one
    $allWrongPayload[$q->id] = ($q->correct_option === 'a') ? 'b' : 'a';
}
$eval0 = $shuffler->evaluateAnswers($quiz, $allWrongPayload);
assertCondition(
    $eval0['score'] === 0 && $eval0['correct_count'] === 0,
    "Payload 100% jawaban salah menghasilkan skor 0 dan 0 jawaban benar."
);

// Case C: Empty Payload (unanswered)
$evalEmpty = $shuffler->evaluateAnswers($quiz, []);
assertCondition(
    $evalEmpty['score'] === 0 && $evalEmpty['correct_count'] === 0,
    "Payload kosong/tidak dijawab aman dari crash dan menghasilkan skor 0."
);

// Case D: Structured Payload { "selected": "...", "display": "..." }
$structuredPayload = [];
$firstQ = $quiz->questions->first();
$structuredPayload[$firstQ->id] = ['selected' => $firstQ->correct_option, 'displayed_as' => 'B'];
$evalStructured = $shuffler->evaluateAnswers($quiz, $structuredPayload);
assertCondition(
    $evalStructured['details'][$firstQ->id]['is_correct'] === true,
    "Mendukung evaluasi payload terstruktur {selected, displayed_as} secara akurat."
);
echo "\n";

// ------------------------------------------------------------------------------------------------
// 4. TEST LEVEL 2 (QUESTION POOL & PROVINCE PACKAGE RESOLUTION)
// ------------------------------------------------------------------------------------------------
echo "--- UJI 4: RESOLUSI PAKET WILAYAH PROVINSI (LEVEL 2 QUESTION POOL) ---\n";

DB::beginTransaction();
try {
    // Create National Quiz for a test pillar
    $nationalQuiz = Quiz::create([
        'pillar' => 'pancasila',
        'title' => '__TEST_NASIONAL_QUIZ__',
        'description' => 'Paket Nasional',
        'duration_minutes' => 15,
        'type' => 'real',
        'package_code' => 'Paket Nasional',
        'province_id' => null, // Nasional
        'is_active' => true,
    ]);

    // Create Aceh-Specific Quiz
    $acehQuiz = Quiz::create([
        'pillar' => 'pancasila',
        'title' => '__TEST_ACEH_QUIZ__',
        'description' => 'Paket Khusus Aceh',
        'duration_minutes' => 15,
        'type' => 'real',
        'package_code' => 'Paket A Wilayah Barat',
        'province_id' => 11, // Aceh
        'is_active' => true,
    ]);

    $controller = app()->make(App\Http\Controllers\Siswa\RealQuizController::class);

    // Simulate Aceh student viewing
    $mockStudent = User::query()->where('role', '=', 'siswa', 'and')->first();
    Auth::login($mockStudent);
    $currentUser = Auth::user();
    $currentUser->province_id = 11; // Aceh
    $currentUser->save();

    $responseAceh = $controller->index();
    $viewDataAceh = $responseAceh->getData()['groupedQuizzes']['pancasila'];
    $acehSelectedTitles = collect($viewDataAceh)->pluck('title')->all();

    assertCondition(
        in_array('__TEST_ACEH_QUIZ__', $acehSelectedTitles),
        "Siswa Provinsi Aceh otomatis dialokasikan ke Paket Khusus Aceh ('Paket A Wilayah Barat')."
    );

    // Simulate Bali student (province_id = 51, no special package) viewing
    $currentUser->province_id = 51; // Bali
    $currentUser->save();

    $responseBali = $controller->index();
    $viewDataBali = $responseBali->getData()['groupedQuizzes']['pancasila'];
    $baliSelectedTitles = collect($viewDataBali)->pluck('title')->all();

    assertCondition(
        in_array('__TEST_NASIONAL_QUIZ__', $baliSelectedTitles),
        "Siswa dari provinsi tanpa paket khusus otomatis fallback ke Paket Nasional."
    );

} finally {
    DB::rollBack();
}

echo "\n===============================================================\n";
echo "HASIL AKHIR: $passed UJI BERHASIL (PASS), $failed UJI GAGAL (FAIL)\n";
echo "===============================================================\n";

exit($failed > 0 ? 1 : 0);
