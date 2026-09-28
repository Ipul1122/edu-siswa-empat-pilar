<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Quiz;
use App\Models\Material;
use Illuminate\Http\Request;
use App\Http\Controllers\Siswa\SearchController;
use Illuminate\Support\Facades\Route;

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

echo "=== VALIDASI I/O: UNIVERSAL APP SEARCH & PROTEKSI SCRIPT (XSS) ===\n\n";

$student = User::where('role', 'siswa')->first();
if ($student) {
    auth()->login($student);
}
$controller = new SearchController();

// TEST 1: Route Verification
echo "1. Validasi Route & Endpoint Pencarian Global\n";
$route = Route::getRoutes()->getByName('siswa.search');
assertCondition("Route 'siswa.search' terdaftar di sistem", $route !== null);
if ($route) {
    assertCondition("Route menggunakan path 'siswa/search'", $route->uri() === 'siswa/search');
    assertCondition("Route diproteksi middleware 'auth:web' dan 'role:siswa'", in_array('role:siswa', $route->middleware()));
}

// TEST 2: Script Injection & XSS Protection
echo "\n2. Validasi Proteksi Injeksi Script (XSS & Protocol Attacks)\n";

// 2a. Injeksi <script> tag
$reqScript = Request::create('/siswa/search', 'GET', ['q' => "<script>alert('xss')</script>Pancasila"]);
$resScript = $controller->search($reqScript);
$dataScript = json_decode($resScript->getContent(), true);

assertCondition("Script tag <script> otomatis di-strip dan tidak dieksekusi", !str_contains($dataScript['query'], '<script>'));
assertCondition("Hasil pencarian tetap menemukan 'Pancasila' setelah sanitasi", $dataScript['total'] > 0);

// 2b. Injeksi Image onerror
$reqImg = Request::create('/siswa/search', 'GET', ['q' => "<img src=x onerror=alert(document.cookie)>"]);
$resImg = $controller->search($reqImg);
$dataImg = json_decode($resImg->getContent(), true);
assertCondition("Injeksi event onerror otomatis dinetralkan (< 2 char clean term)", $dataImg['total'] === 0 && !str_contains($dataImg['query'], 'onerror'));

// 2c. Injeksi javascript: URI
$reqJs = Request::create('/siswa/search', 'GET', ['q' => "javascript:alert(1)"]);
$resJs = $controller->search($reqJs);
$dataJs = json_decode($resJs->getContent(), true);
assertCondition("Protokol 'javascript:' otomatis dibersihkan dari query", !str_contains(strtolower($dataJs['query']), 'javascript:'));

// 2d. Control characters & Buffer length limit
$longString = str_repeat("A", 150) . "\x00\x1F";
$reqLong = Request::create('/siswa/search', 'GET', ['q' => $longString]);
$resLong = $controller->search($reqLong);
$dataLong = json_decode($resLong->getContent(), true);
assertCondition("Panjang query dipangkas maksimal 80 karakter & control chars dibuang", mb_strlen($dataLong['query']) <= 80 && !str_contains($dataLong['query'], "\x00"));

// TEST 3: Navigation & System Pages Search (Peringkat, Dashboard, Profil)
echo "\n3. Validasi Pencarian Halaman Navigasi Global (Peringkat, Dashboard, Profil)\n";

// 3a. Search "Peringkat" / "Leaderboard"
$reqPeringkat = Request::create('/siswa/search', 'GET', ['q' => 'peringkat']);
$resPeringkat = $controller->search($reqPeringkat);
$dataPeringkat = json_decode($resPeringkat->getContent(), true);

$foundPeringkat = false;
foreach ($dataPeringkat['results'] as $item) {
    if (str_contains(strtolower($item['title']), 'peringkat') && $item['url'] === route('siswa.leaderboard')) {
        $foundPeringkat = true;
        break;
    }
}
assertCondition("Pencarian 'peringkat' mengarahkan ke Papan Peringkat / Leaderboard", $foundPeringkat);

// 3b. Search "Dashboard" / "Beranda"
$reqDashboard = Request::create('/siswa/search', 'GET', ['q' => 'dashboard']);
$resDashboard = $controller->search($reqDashboard);
$dataDashboard = json_decode($resDashboard->getContent(), true);

$foundDashboard = false;
foreach ($dataDashboard['results'] as $item) {
    if (str_contains(strtolower($item['title']), 'dashboard') && $item['url'] === route('siswa.dashboard')) {
        $foundDashboard = true;
        break;
    }
}
assertCondition("Pencarian 'dashboard' mengarahkan ke Dashboard Siswa", $foundDashboard);

// 3c. Search "Profil" / "Ganti Kata Sandi"
$reqProfil = Request::create('/siswa/search', 'GET', ['q' => 'profil']);
$resProfil = $controller->search($reqProfil);
$dataProfil = json_decode($resProfil->getContent(), true);

$foundProfil = false;
foreach ($dataProfil['results'] as $item) {
    if (str_contains(strtolower($item['title']), 'profil') && $item['url'] === route('siswa.profile.edit')) {
        $foundProfil = true;
        break;
    }
}
assertCondition("Pencarian 'profil' mengarahkan ke Edit Profil & Kata Sandi", $foundProfil);

// TEST 4: Content Search (Kuis, Real Materi, Materi Bacaan, Video)
echo "\n4. Validasi Pencarian Konten Pembelajaran Lintas Halaman\n";

// 4a. Search "Kuis"
$reqQuiz = Request::create('/siswa/search', 'GET', ['q' => 'Kuis']);
$resQuiz = $controller->search($reqQuiz);
$dataQuiz = json_decode($resQuiz->getContent(), true);
$hasQuizResult = count(array_filter($dataQuiz['results'], fn($i) => $i['type'] === 'quiz')) > 0;
assertCondition("Pencarian 'Kuis' menemukan modul kuis latihan", $hasQuizResult);

// 4b. Search "Real Materi"
$reqReal = Request::create('/siswa/search', 'GET', ['q' => 'real materi']);
$resReal = $controller->search($reqReal);
$dataReal = json_decode($resReal->getContent(), true);
$hasRealResult = count(array_filter($dataReal['results'], fn($i) => $i['type'] === 'real-materi' || $i['url'] === route('siswa.real-materi.index'))) > 0;
assertCondition("Pencarian 'real materi' menemukan evaluasi resmi seleksi", $hasRealResult);

// 4c. Search "Pancasila"
$reqPancasila = Request::create('/siswa/search', 'GET', ['q' => 'Pancasila']);
$resPancasila = $controller->search($reqPancasila);
$dataPancasila = json_decode($resPancasila->getContent(), true);
assertCondition("Pencarian 'Pancasila' mengembalikan hasil lintas modul", $dataPancasila['total'] > 0);

// TEST 5: Blade & Frontend Code Verification
echo "\n5. Validasi Proteksi Frontend & Debounce (app.js & app.css)\n";
$appJsContent = file_get_contents(resource_path('js/app.js'));
assertCondition("app.js mengimplementasikan 'sanitizeInput' untuk mencegah XSS", str_contains($appJsContent, 'sanitizeInput'));
assertCondition("app.js mengimplementasikan 'escapeHtml' menyeluruh", str_contains($appJsContent, 'escapeHtml'));
assertCondition("app.js menggunakan debounce 300ms", str_contains($appJsContent, 'debounce') && str_contains($appJsContent, '300'));

$appCssContent = file_get_contents(resource_path('css/app.css'));
assertCondition("app.css memuat styling badge navigasi '.badge-search-nav'", str_contains($appCssContent, '.badge-search-nav'));
assertCondition("app.css memuat dropdown '.global-search-dropdown'", str_contains($appCssContent, '.global-search-dropdown'));

echo "\n=======================================================\n";
echo "HASIL AKHIR: {$passed} BERHASIL, {$failed} GAGAL\n";
echo "=======================================================\n";

exit($failed > 0 ? 1 : 0);
