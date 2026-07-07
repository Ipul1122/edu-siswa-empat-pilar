<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Public / Guest Routes
Route::get('/', function () {
    $siswaCount = \App\Models\User::query()->where('role', '=', 'siswa', 'and')->count('*');
    $materiCount = \App\Models\Material::query()->where('type', '=', 'text', 'and')->count('*');
    $videoCount = \App\Models\Material::query()->where('type', '=', 'video', 'and')->count('*');
    $quizCount = \App\Models\Quiz::query()->count('*');
    $soalCount = \App\Models\Question::query()->count('*');

    return view('welcome', compact('siswaCount', 'materiCount', 'videoCount', 'quizCount', 'soalCount'));
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

    // Siswa Lupa Password OTP
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetOtp'])->name('password.email')->middleware('throttle:otp');
    Route::get('/forgot-password/verify-otp', [AuthController::class, 'showResetVerifyOtp'])->name('password.verify_otp');
    Route::post('/forgot-password/verify-otp', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:auth');
    Route::post('/forgot-password/resend-otp', [AuthController::class, 'resetResendOtp'])->name('password.resend_otp')->middleware('throttle:otp');
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

    // Materials CRUD
    Route::resource('materials', App\Http\Controllers\Admin\MaterialController::class)->except(['show']);

    // Videos CRUD
    Route::resource('videos', App\Http\Controllers\Admin\VideoMaterialController::class)->except(['show']);

    // Quizzes CRUD
    Route::resource('quizzes', App\Http\Controllers\Admin\QuizController::class);

    // Questions CRUD (Nest within quiz context)
    Route::get('/quizzes/{quiz}/questions/create', [App\Http\Controllers\Admin\QuestionController::class, 'create'])->name('questions.create');
    Route::post('/quizzes/{quiz}/questions', [App\Http\Controllers\Admin\QuestionController::class, 'store'])->name('questions.store');
    Route::get('/questions/{question}/edit', [App\Http\Controllers\Admin\QuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{question}', [App\Http\Controllers\Admin\QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [App\Http\Controllers\Admin\QuestionController::class, 'destroy'])->name('questions.destroy');

    // Student Monitoring
    Route::get('/students', [App\Http\Controllers\Admin\StudentController::class, 'index'])->name('students.index');
    Route::get('/students/export', [App\Http\Controllers\Admin\StudentController::class, 'export'])->name('students.export');
    Route::get('/students/report', [App\Http\Controllers\Admin\StudentController::class, 'report'])->name('students.report');
    Route::get('/students/{student}', [App\Http\Controllers\Admin\StudentController::class, 'show'])->name('students.show');
});

// Siswa Panel Routes
Route::middleware(['auth:web', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Siswa\DashboardController::class, 'index'])->name('dashboard');

    // Reading Materials
    Route::get('/materials', [App\Http\Controllers\Siswa\MaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/{material}', [App\Http\Controllers\Siswa\MaterialController::class, 'show'])->name('materials.show');
    Route::post('/materials/{material}/complete', [App\Http\Controllers\Siswa\MaterialController::class, 'complete'])->name('materials.complete');

    // Video Materials
    Route::get('/videos', [App\Http\Controllers\Siswa\VideoMaterialController::class, 'index'])->name('videos.index');
    Route::get('/videos/{material}', [App\Http\Controllers\Siswa\VideoMaterialController::class, 'show'])->name('videos.show');
    Route::post('/videos/{material}/complete', [App\Http\Controllers\Siswa\VideoMaterialController::class, 'complete'])->name('videos.complete');

    // Quizzes & pengerjaan
    Route::get('/quizzes', [App\Http\Controllers\Siswa\QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/quizzes/{quiz}', [App\Http\Controllers\Siswa\QuizController::class, 'show'])->name('quizzes.show');
    Route::get('/quizzes/{quiz}/start', [App\Http\Controllers\Siswa\QuizController::class, 'start'])->name('quizzes.start');
    Route::post('/quizzes/{quiz}/submit', [App\Http\Controllers\Siswa\QuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/attempts/{attempt}/result', [App\Http\Controllers\Siswa\QuizController::class, 'result'])->name('quizzes.result');

    // Real Materi & pengerjaan (Hanya 1x pengerjaan)
    Route::get('/real-materi', [App\Http\Controllers\Siswa\RealQuizController::class, 'index'])->name('real-materi.index');
    Route::get('/real-materi/{quiz}', [App\Http\Controllers\Siswa\RealQuizController::class, 'show'])->name('real-materi.show');
    Route::get('/real-materi/{quiz}/start', [App\Http\Controllers\Siswa\RealQuizController::class, 'start'])->name('real-materi.start');
    Route::post('/real-materi/{quiz}/submit', [App\Http\Controllers\Siswa\RealQuizController::class, 'submit'])->name('real-materi.submit');
    Route::get('/real-attempts/{attempt}/result', [App\Http\Controllers\Siswa\RealQuizController::class, 'result'])->name('real-materi.result');

    // Leaderboard
    Route::get('/leaderboard', [App\Http\Controllers\Siswa\LeaderboardController::class, 'index'])->name('leaderboard');

    // Profile Settings
    Route::get('/profile', [App\Http\Controllers\Siswa\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Siswa\ProfileController::class, 'update'])->name('profile.update');
});

Route::get('/extract-icons', function () {
    $url = 'https://www.flaticon.com/free-icons/web';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.9',
        'Cache-Control: no-cache',
        'Pragma: no-cache',
        'Referer: https://www.google.com/'
    ]);
    $html = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200) {
        return response()->json([
            'status' => 'error',
            'code' => $status,
            'message' => 'Failed to fetch Flaticon page (status: ' . $status . ')',
            'html_preview' => substr($html, 0, 1000)
        ]);
    }

    preg_match_all('/https:\/\/cdn-icons-png\.flaticon\.com\/(?:128|512|64|32)\/\d+\/\d+\.png/i', $html, $matches);
    $imageUrls = array_unique($matches[0]);

    return response()->json([
        'status' => 'success',
        'found_urls_count' => count($imageUrls),
        'urls' => array_values($imageUrls),
        'html_length' => strlen($html)
    ]);
});

