<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Material;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;

echo "===============================================================\n";
echo "       UJI VALIDASI I/O: FULL PAGE FOCUS MODE SISWA            \n";
echo "===============================================================\n\n";

$passed = 0;
$failed = 0;

/**
 * Assert test condition and report result
 *
 * @param bool $condition
 * @param string $testName
 * @return void
 */
function assertTest(bool $condition, string $testName): void {
    global $passed, $failed;
    if ($condition) {
        echo " [PASS] $testName\n";
        $passed++;
    } else {
        echo " [FAIL] $testName\n";
        $failed++;
    }
}

// Login as student
$student = User::query()->where('role', '=', 'siswa', 'and')->first();
if (!$student) {
    die("Error: Siswa tidak ditemukan di database.\n");
}
Auth::guard('web')->login($student);

// Retrieve sample material, video material, practice quiz, and real quiz
$textMaterial = Material::where('type', 'article')->first() ?? Material::first();
$videoMaterial = Material::where('type', 'video')->first() ?? Material::first();
$practiceQuiz = Quiz::where('type', 'practice')->has('questions')->first() ?? Quiz::has('questions')->first();

$realQuiz = Quiz::where('type', 'real')->has('questions')->where('is_active', true)->first();
if (!$realQuiz) {
    $realQuiz = Quiz::has('questions')->first();
    $realQuiz->type = 'real';
    $realQuiz->is_active = true;
    $realQuiz->save();
}

// Clear any attempts by this student on real quiz to allow starting
\App\Models\QuizAttempt::where('user_id', $student->id)->where('quiz_id', $realQuiz->id)->delete();

$session = app('session')->driver();
$session->start();

// Helper to simulate request & render view
function renderRouteHtml($routeName, $params = []) {
    global $session;
    $url = route($routeName, $params);
    $req = \Illuminate\Http\Request::create($url, 'GET');
    $req->setLaravelSession($session);
    app()->instance('request', $req);
    $response = app()->handle($req);
    return $response->getContent();
}

// -------------------------------------------------------------------------
// TEST 1: Baca Materi (siswa.materials.show)
// -------------------------------------------------------------------------
echo "--- UJI 1: BACA MATERI (siswa.materials.show) ---\n";
if ($textMaterial) {
    $htmlMaterial = renderRouteHtml('siswa.materials.show', $textMaterial);
    assertTest(
        str_contains($htmlMaterial, 'fullpage-mode has-fullpage-support'),
        "Halaman Baca Materi otomatis mengaktifkan 'fullpage-mode'."
    );
    assertTest(
        str_contains($htmlMaterial, 'id="fullpage-exit-btn"') && str_contains($htmlMaterial, 'Keluar Layar Penuh'),
        "Tombol floating '✕' (Keluar Layar Penuh) tersedia di Halaman Baca Materi."
    );
    assertTest(
        str_contains($htmlMaterial, 'id="fullpage-mobile-pill"'),
        "Pill panduan swipe mobile tersedia di Halaman Baca Materi."
    );
} else {
    echo " [SKIP] Tidak ada data materi teks.\n";
}
echo "\n";

// -------------------------------------------------------------------------
// TEST 2: Tonton Video (siswa.videos.show)
// -------------------------------------------------------------------------
echo "--- UJI 2: TONTON VIDEO (siswa.videos.show) ---\n";
if ($videoMaterial) {
    $htmlVideo = renderRouteHtml('siswa.videos.show', $videoMaterial);
    assertTest(
        str_contains($htmlVideo, 'fullpage-mode has-fullpage-support'),
        "Halaman Tonton Video otomatis mengaktifkan 'fullpage-mode'."
    );
    assertTest(
        str_contains($htmlVideo, 'id="fullpage-exit-btn"'),
        "Tombol floating '✕' tersedia di Halaman Tonton Video."
    );
} else {
    echo " [SKIP] Tidak ada data materi video.\n";
}
echo "\n";

// -------------------------------------------------------------------------
// TEST 3: Mulai Kuis Latihan (siswa.quizzes.start)
// -------------------------------------------------------------------------
echo "--- UJI 3: MULAI KUIS LATIHAN (siswa.quizzes.start) ---\n";
if ($practiceQuiz) {
    $htmlQuiz = renderRouteHtml('siswa.quizzes.start', $practiceQuiz);
    assertTest(
        str_contains($htmlQuiz, 'fullpage-mode has-fullpage-support'),
        "Halaman Mulai Kuis Latihan otomatis mengaktifkan 'fullpage-mode'."
    );
    assertTest(
        str_contains($htmlQuiz, 'id="fullpage-exit-btn"'),
        "Tombol floating '✕' tersedia di Halaman Mulai Kuis Latihan."
    );
} else {
    echo " [SKIP] Tidak ada data kuis latihan.\n";
}
echo "\n";

// -------------------------------------------------------------------------
// TEST 4: Mulai Real Materi (siswa.real-materi.start)
// -------------------------------------------------------------------------
echo "--- UJI 4: MULAI REAL MATERI (siswa.real-materi.start) ---\n";
if ($realQuiz) {
    $htmlRealQuiz = renderRouteHtml('siswa.real-materi.start', $realQuiz);
    assertTest(
        str_contains($htmlRealQuiz, 'fullpage-mode has-fullpage-support'),
        "Halaman Mulai Real Materi otomatis mengaktifkan 'fullpage-mode'."
    );
    assertTest(
        str_contains($htmlRealQuiz, 'id="fullpage-exit-btn"'),
        "Tombol floating '✕' tersedia di Halaman Mulai Real Materi."
    );
} else {
    echo " [SKIP] Tidak ada data real kuis.\n";
}
echo "\n";

// -------------------------------------------------------------------------
// TEST 5: Halaman Normal (Bukan Target Fullpage, misal: Dashboard & Daftar Materi)
// -------------------------------------------------------------------------
echo "--- UJI 5: HALAMAN NORMAL (TIDAK MENGGUNAKAN FULLPAGE) ---\n";
$htmlDashboard = renderRouteHtml('siswa.dashboard');
assertTest(
    !str_contains($htmlDashboard, 'class="fullpage-mode'),
    "Halaman Dashboard Siswa tetap normal (tidak mengaktifkan fullpage mode)."
);
assertTest(
    !str_contains($htmlDashboard, 'id="fullpage-controls"'),
    "Controls fullpage tidak dimuat di halaman non-target."
);

$htmlMaterialsIndex = renderRouteHtml('siswa.materials.index');
assertTest(
    !str_contains($htmlMaterialsIndex, 'class="fullpage-mode'),
    "Halaman Daftar Materi tetap normal (tidak mengaktifkan fullpage mode)."
);
echo "\n";

echo "===============================================================\n";
echo "HASIL AKHIR: $passed UJI BERHASIL (PASS), $failed UJI GAGAL (FAIL)\n";
echo "===============================================================\n";

exit($failed > 0 ? 1 : 0);
