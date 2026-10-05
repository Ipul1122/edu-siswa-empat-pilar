<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Public / Guest Routes
Route::get('/', function () {
    $stats = Cache::remember('welcome_stats', 600, function () {
        return [
            'siswaCount' => \App\Models\User::query()->where('role', '=', 'siswa', 'and')->count('*'),
            'seleksiCount' => \App\Models\Quiz::query()->where('type', '=', 'real', 'and')->count('*'),
            'soalCount' => \App\Models\Question::query()->whereHas('quiz', function ($q) {
                $q->where('type', '=', 'real');
            })->count('*'),
        ];
    });

    return view('welcome', $stats);
})->name('home');

Route::middleware('guest:web')->group(function () {
    // Siswa (Student) Auth
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::get('/register/verify-otp', [AuthController::class, 'showRegisterVerifyOtp'])->name('register.verify_otp');
    Route::post('/register/verify-otp', [AuthController::class, 'registerVerifyOtp'])->middleware('throttle:auth');
    Route::post('/register/resend-otp', [AuthController::class, 'registerResendOtp'])->name('register.resend_otp')->middleware('throttle:otp');
    Route::get('/regions/provinces/{province}/regencies', [AuthController::class, 'getRegencies'])->name('regions.regencies');

    // Siswa Lupa Password OTP (2 Langkah: Verifikasi OTP -> Buat Kata Sandi Baru)
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetOtp'])->name('password.email')->middleware('throttle:otp');
    
    // Langkah 1: Verifikasi Kode OTP
    Route::get('/forgot-password/verify-otp', [AuthController::class, 'showResetVerifyOtp'])->name('password.verify_otp');
    Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyResetOtp'])->name('password.verify_otp.submit')->middleware('throttle:auth');
    Route::post('/forgot-password/resend-otp', [AuthController::class, 'resetResendOtp'])->name('password.resend_otp')->middleware('throttle:otp');

    // Langkah 2: Buat Kata Sandi Baru di Halaman Berbeda
    Route::get('/forgot-password/reset', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:auth');
});

Route::middleware('guest:admin')->group(function () {
    // Admin Auth
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->middleware('throttle:auth');
});

// Auth Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:web,admin');

// Admin Panel Routes
Route::middleware(['auth:admin', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Soal Seleksi (Real Materi) CRUD & Global / Individual Toggle Status
    Route::post('/real-materi/toggle-all', [App\Http\Controllers\Admin\RealQuizController::class, 'toggleAll'])->name('real-materi.toggle-all');
    Route::patch('/real-materi/{real_materi}/toggle', [App\Http\Controllers\Admin\RealQuizController::class, 'toggleStatus'])->name('real-materi.toggle');
    Route::resource('real-materi', App\Http\Controllers\Admin\RealQuizController::class)->names('real-materi');
    Route::get('/quizzes/{quiz}', [App\Http\Controllers\Admin\RealQuizController::class, 'show'])->name('quizzes.show');

    // Butir Soal Seleksi CRUD (Terkait dengan paket seleksi)
    Route::get('/quizzes/{quiz}/questions/template', [App\Http\Controllers\Admin\QuestionController::class, 'template'])->name('questions.template');
    Route::post('/quizzes/{quiz}/questions/import', [App\Http\Controllers\Admin\QuestionController::class, 'import'])->name('questions.import');
    Route::get('/quizzes/{quiz}/questions/create', [App\Http\Controllers\Admin\QuestionController::class, 'create'])->name('questions.create');
    Route::post('/quizzes/{quiz}/questions', [App\Http\Controllers\Admin\QuestionController::class, 'store'])->name('questions.store');
    Route::get('/questions/{question}/edit', [App\Http\Controllers\Admin\QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{question}', [App\Http\Controllers\Admin\QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [App\Http\Controllers\Admin\QuestionController::class, 'destroy'])->name('questions.destroy');

    // Pemantauan Siswa & Leaderboard
    Route::get('/students', [App\Http\Controllers\Admin\StudentController::class, 'index'])->name('students.index');
    Route::get('/students/export', [App\Http\Controllers\Admin\StudentController::class, 'export'])->name('students.export');
    Route::get('/students/report', [App\Http\Controllers\Admin\StudentController::class, 'report'])->name('students.report');
    Route::post('/students/broadcast', [App\Http\Controllers\Admin\StudentController::class, 'broadcast'])->name('students.broadcast');
    Route::post('/students/{student}/grant-retest', [App\Http\Controllers\Admin\StudentController::class, 'grantRetest'])->name('students.grant-retest');
    Route::get('/students/{student}', [App\Http\Controllers\Admin\StudentController::class, 'show'])->name('students.show');

    // Leaderboard & Spreadsheet Exports (Requirement 3 & 10)
    Route::get('/leaderboard', [App\Http\Controllers\Admin\LeaderboardController::class, 'index'])->name('leaderboard');
    Route::get('/leaderboard/export-spreadsheet', [App\Http\Controllers\Admin\LeaderboardController::class, 'exportSpreadsheet'])->name('leaderboard.export-spreadsheet');
    Route::get('/leaderboard/export-top9', [App\Http\Controllers\Admin\LeaderboardController::class, 'exportTop9'])->name('leaderboard.export-top9');

    // Zoom Virtual Sessions CRUD & Participant Management
    Route::patch('/zoom-sessions/{zoom_session}/toggle', [App\Http\Controllers\Admin\ZoomSessionController::class, 'toggleStatus'])->name('zoom-sessions.toggle');
    Route::post('/zoom-sessions/{zoom_session}/participants', [App\Http\Controllers\Admin\ZoomSessionController::class, 'addParticipant'])->name('zoom-sessions.participants.add');
    Route::delete('/zoom-sessions/{zoom_session}/participants/{user}', [App\Http\Controllers\Admin\ZoomSessionController::class, 'removeParticipant'])->name('zoom-sessions.participants.remove');
    Route::get('/zoom-sessions/{zoom_session}/export', [App\Http\Controllers\Admin\ZoomSessionController::class, 'exportParticipants'])->name('zoom-sessions.export');
    Route::resource('zoom-sessions', App\Http\Controllers\Admin\ZoomSessionController::class);

    // Admin Profile Settings
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
});

// Siswa (School Mode) Panel Routes
Route::middleware(['auth:web', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    // Dashboard & Global Cross-Page Live Search
    Route::get('/dashboard', [App\Http\Controllers\Siswa\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [App\Http\Controllers\Siswa\SearchController::class, 'search'])->name('search');

    // 1. Tutorial Penggunaan & Petunjuk Teknis
    Route::get('/tutorial', [App\Http\Controllers\Siswa\TutorialController::class, 'index'])->name('tutorial');

    // 3. Zoom Virtual Sessions & Live Connection Ping/Trouble Tracking
    Route::get('/zoom-sessions', [App\Http\Controllers\Siswa\ZoomSessionController::class, 'index'])->name('zoom-sessions.index');
    Route::post('/zoom-sessions/{zoom_session}/join', [App\Http\Controllers\Siswa\ZoomSessionController::class, 'join'])->name('zoom-sessions.join');
    Route::post('/zoom-sessions/{zoom_session}/leave', [App\Http\Controllers\Siswa\ZoomSessionController::class, 'leave'])->name('zoom-sessions.leave');
    Route::post('/zoom-sessions/{zoom_session}/ping', [App\Http\Controllers\Siswa\ZoomSessionController::class, 'ping'])->name('zoom-sessions.ping');
    Route::post('/zoom-sessions/{zoom_session}/report-trouble', [App\Http\Controllers\Siswa\ZoomSessionController::class, 'reportTrouble'])->name('zoom-sessions.report_trouble');

    // 4. Ujian Seleksi Online (CBT) & Asynchronous Zero-Reload Auto-Save
    Route::get('/real-materi', [App\Http\Controllers\Siswa\RealQuizController::class, 'index'])->name('real-materi.index');
    Route::get('/real-materi/{quiz}', [App\Http\Controllers\Siswa\RealQuizController::class, 'show'])->name('real-materi.show');
    Route::get('/real-materi/{quiz}/start', [App\Http\Controllers\Siswa\RealQuizController::class, 'start'])->name('real-materi.start');
    Route::post('/real-materi/{quiz}/save-answer', [App\Http\Controllers\Siswa\RealQuizController::class, 'saveAnswer'])->name('real-materi.save-answer');
    Route::post('/real-materi/{quiz}/submit', [App\Http\Controllers\Siswa\RealQuizController::class, 'submit'])->name('real-materi.submit');
    Route::get('/real-attempts/{attempt}/result', [App\Http\Controllers\Siswa\RealQuizController::class, 'result'])->name('real-materi.result');

    // 5. Hasil Skor Mandiri Sekolah (Kerahasiaan Terjaga)
    Route::get('/hasil-tes', [App\Http\Controllers\Siswa\ResultController::class, 'index'])->name('my-results');
    Route::get('/leaderboard', [App\Http\Controllers\Siswa\LeaderboardController::class, 'index'])->name('leaderboard');

    // 6. Customer Service Chatbot API
    Route::post('/chatbot/ask', [App\Http\Controllers\Siswa\ChatbotController::class, 'ask'])->name('chatbot.ask');

    // 7. Pengaturan Akun Sekolah & PIC
    Route::get('/profile', [App\Http\Controllers\Siswa\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Siswa\ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/extract-icons', function () {
    $url = 'https://www.flaticon.com/free-icons/web';
    
    $response = Http::withHeaders([
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
        'Accept-Language' => 'en-US,en;q=0.9',
        'Cache-Control' => 'no-cache',
        'Pragma' => 'no-cache',
        'Referer' => 'https://www.google.com/',
    ])->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36')
      ->get($url);

    if (!$response->successful()) {
        return response()->json([
            'status' => 'error',
            'code' => $response->status(),
            'message' => 'Failed to fetch Flaticon page (status: ' . $response->status() . ')',
            'html_preview' => substr($response->body(), 0, 1000)
        ]);
    }

    $html = $response->body();
    preg_match_all('/https:\/\/cdn-icons-png\.flaticon\.com\/(?:128|512|64|32)\/\d+\/\d+\.png/i', $html, $matches);
    $imageUrls = array_unique($matches[0]);

    return response()->json([
        'status' => 'success',
        'found_urls_count' => count($imageUrls),
        'urls' => array_values($imageUrls),
        'html_length' => strlen($html)
    ]);
});

