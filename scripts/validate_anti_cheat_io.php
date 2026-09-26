<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$passed = 0;
$failed = 0;

function assertCondition($testName, $condition, $details = '') {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] {$testName}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$testName} - {$details}\n";
        $failed++;
    }
}

echo "=== VALIDASI I/O ITEM 2.6: SISTEM KEAMANAN ANTI-SCREENSHOT & ANTI-CHEAT LAYAR ===\n\n";

// TEST 1: Database Schema Check
echo "1. Validasi Struktur Database (quiz_attempts.violations_count)\n";
$hasColumn = Schema::hasColumn('quiz_attempts', 'violations_count');
assertCondition("Kolom 'violations_count' ada pada tabel 'quiz_attempts'", $hasColumn);

$attemptModel = new QuizAttempt();
$isFillable = in_array('violations_count', $attemptModel->getFillable());
assertCondition("Atribut 'violations_count' terdaftar di fillable QuizAttempt model", $isFillable);

// TEST 2: I/O Persistence of Violations Count (0, 1, 2, 3)
echo "\n2. Validasi I/O Penyimpanan Pelanggaran (QuizAttempt Model & Controller)\n";
$student = User::where('role', 'siswa')->first();
$quiz = Quiz::where('is_active', true)->first();

if ($student && $quiz) {
    // 2a. Attempt with 0 violations
    $attemptClean = QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'score' => 85,
        'correct_answers' => 17,
        'total_questions' => 20,
        'duration_seconds_taken' => 300,
        'violations_count' => 0,
        'answers' => ['1' => 'A'],
    ]);
    assertCondition("Penyimpanan pengerjaan bersih (0 pelanggaran)", $attemptClean && $attemptClean->violations_count === 0);

    // 2b. Attempt with 2 violations (warning state)
    $attemptWarning = QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'score' => 70,
        'correct_answers' => 14,
        'total_questions' => 20,
        'duration_seconds_taken' => 450,
        'violations_count' => 2,
        'answers' => ['1' => 'B'],
    ]);
    assertCondition("Penyimpanan pengerjaan dengan peringatan (2 pelanggaran)", $attemptWarning && $attemptWarning->violations_count === 2);

    // 2c. Attempt with 3 violations (disqualification threshold)
    $attemptDisqualified = QuizAttempt::create([
        'user_id' => $student->id,
        'quiz_id' => $quiz->id,
        'score' => 40,
        'correct_answers' => 8,
        'total_questions' => 20,
        'duration_seconds_taken' => 120,
        'violations_count' => 3,
        'answers' => ['1' => 'C'],
    ]);
    assertCondition("Penyimpanan pengerjaan diskualifikasi (3 pelanggaran / auto-submit)", $attemptDisqualified && $attemptDisqualified->violations_count === 3);

    // Clean up temporary test records
    $attemptClean->delete();
    $attemptWarning->delete();
    $attemptDisqualified->delete();
} else {
    echo "  [SKIP] Siswa / Quiz tidak ditemukan untuk pengujian DB create\n";
}

// TEST 3: Blade Partial Exam Security File Structure
echo "\n3. Validasi Komponen Blade Keamanan (exam_security.blade.php)\n";
$partialPath = resource_path('views/siswa/partials/exam_security.blade.php');
assertCondition("File blade partial 'siswa.partials.exam_security' tersedia", file_exists($partialPath));

if (file_exists($partialPath)) {
    $partialContent = file_get_contents($partialPath);
    assertCondition("Hidden input 'violations_count' ada di partial", str_contains($partialContent, 'name="violations_count"') && str_contains($partialContent, 'id="violations_count"'));
    assertCondition("Forensic watermark overlay '#exam-watermark-overlay' ada", str_contains($partialContent, 'id="exam-watermark-overlay"') && str_contains($partialContent, 'class="exam-watermark-overlay"'));
    assertCondition("Elemen watermark memuat Nama, Sekolah/Dapil, dan IP", str_contains($partialContent, '$studentName') && str_contains($partialContent, '$studentIdentity') && str_contains($partialContent, '$clientIp'));
    assertCondition("Elemen live clock '.watermark-live-clock' ada di watermark", str_contains($partialContent, 'class="watermark-live-clock"'));
    assertCondition("Blackout overlay '#exam-security-blackout' dengan tombol refocus ada", str_contains($partialContent, 'id="exam-security-blackout"') && str_contains($partialContent, 'id="blackout-refocus-btn"'));
}

// TEST 4: Blade Quiz Views Integration
echo "\n4. Validasi Integrasi Blade Quiz Views (quizzes/start & real_quizzes/start)\n";
$quizStartPath = resource_path('views/siswa/quizzes/start.blade.php');
$realQuizStartPath = resource_path('views/siswa/real_quizzes/start.blade.php');

$quizStartContent = file_get_contents($quizStartPath);
assertCondition("quizzes/start.blade.php mengikutsertakan '@include('siswa.partials.exam_security')'", str_contains($quizStartContent, "@include('siswa.partials.exam_security')"));
assertCondition("quizzes/start.blade.php memuat indikator integritas '#exam-violation-pill'", str_contains($quizStartContent, 'id="exam-violation-pill"'));

$realQuizStartContent = file_get_contents($realQuizStartPath);
assertCondition("real_quizzes/start.blade.php mengikutsertakan '@include('siswa.partials.exam_security')'", str_contains($realQuizStartContent, "@include('siswa.partials.exam_security')"));
assertCondition("real_quizzes/start.blade.php memuat indikator integritas '#exam-violation-pill'", str_contains($realQuizStartContent, 'id="exam-violation-pill"'));

// TEST 5: Frontend JS Logic (app.js)
echo "\n5. Validasi Logika JavaScript Anti-Cheat & Anti-Screenshot (app.js)\n";
$appJsPath = resource_path('js/app.js');
$appJsContent = file_get_contents($appJsPath);

assertCondition("JS memuat live clock updater per detik untuk '.watermark-live-clock'", str_contains($appJsContent, 'updateWatermarkClock') && str_contains($appJsContent, '.watermark-live-clock'));
assertCondition("JS memblokir klik kanan (contextmenu preventDefault)", str_contains($appJsContent, "contextmenu") && str_contains($appJsContent, "e.preventDefault()"));
assertCondition("JS memblokir drag gambar dan text selection copy/cut", str_contains($appJsContent, "dragstart") && str_contains($appJsContent, "copy") && str_contains($appJsContent, "cut"));
assertCondition("JS menangkap tombol PrintScreen dan mengosongkan clipboard sistem", str_contains($appJsContent, "PrintScreen") && str_contains($appJsContent, "navigator.clipboard.writeText('')"));
assertCondition("JS memblokir shortcut inspeksi & cetak (Ctrl+P, Ctrl+S, Ctrl+U, F12, DevTools)", str_contains($appJsContent, "F12") && str_contains($appJsContent, "Ctrl+P") || str_contains($appJsContent, "e.key === 'p'"));
assertCondition("JS mendeteksi tab switch via 'visibilitychange' & 'blur'", str_contains($appJsContent, "visibilitychange") && str_contains($appJsContent, "window.addEventListener('blur'") && str_contains($appJsContent, "handleUserLeft"));
assertCondition("JS menghitung pelanggaran dan mengupdate '#violations_count'", str_contains($appJsContent, "violationsCount++") && str_contains($appJsContent, "violationsInput.value = violationsCount"));
assertCondition("JS menghentikan ujian otomatis saat mencapai batas 3 pelanggaran (Auto-Submit)", str_contains($appJsContent, "MAX_VIOLATIONS = 3") && str_contains($appJsContent, "quizForm.submit()"));

// TEST 6: Frontend CSS Styling (app.css)
echo "\n6. Validasi Gaya CSS Anti-Cheat & Watermark (app.css)\n";
$appCssPath = resource_path('css/app.css');
$appCssContent = file_get_contents($appCssPath);

assertCondition("CSS menerapkan 'user-select: none !important' pada quiz form & wrapper", str_contains($appCssContent, "user-select: none !important"));
assertCondition("CSS mendefinisikan overlay watermark '.exam-watermark-overlay' dengan pointer-events: none", str_contains($appCssContent, ".exam-watermark-overlay") && str_contains($appCssContent, "pointer-events: none !important"));
assertCondition("CSS mendefinisikan '.exam-security-blackout' dengan full-screen backdrop-filter blur", str_contains($appCssContent, ".exam-security-blackout") && str_contains($appCssContent, "backdrop-filter: blur"));
assertCondition("CSS mendefinisikan '.exam-violation-pill' dengan warning classes", str_contains($appCssContent, ".exam-violation-pill") && str_contains($appCssContent, "warning-1") && str_contains($appCssContent, "warning-2"));

// TEST 7: Result & Admin Views Verification
echo "\n7. Validasi Tampilan Hasil & Panel Pengawas Admin\n";
$realResultContent = file_get_contents(resource_path('views/siswa/real_quizzes/result.blade.php'));
assertCondition("Hasil Real Materi menampilkan informasi 'Integritas Layar'", str_contains($realResultContent, 'Integritas Layar:') && str_contains($realResultContent, 'violations_count'));

$adminStudentShowContent = file_get_contents(resource_path('views/admin/students/show.blade.php'));
assertCondition("Panel Admin menampilkan kolom 'Integritas Layar' di riwayat pengerjaan", str_contains($adminStudentShowContent, '<th>Integritas Layar</th>') && str_contains($adminStudentShowContent, 'violations_count'));

echo "\n=======================================================\n";
echo "HASIL AKHIR: {$passed} BERHASIL, {$failed} GAGAL\n";
echo "=======================================================\n";

exit($failed > 0 ? 1 : 0);
