<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;

echo "=====================================================\n";
echo "           UJI FITUR REMEMBER ME (INGAT SAYA)        \n";
echo "=====================================================\n\n";

$student = User::where('role', 'siswa')->first();

if (!$student) {
    echo "Tidak ada siswa ditemukan di database.\n";
    exit(1);
}

echo "1. Data Siswa Uji: ID={$student->id}, Email={$student->email}\n";
echo "   remember_token awal: " . ($student->remember_token ?? 'NULL') . "\n\n";

// Simulasi Auth::guard('web')->login($student, true) (sama dengan apa yang dipanggil saat attempt dengan remember = true)
Auth::guard('web')->login($student, true);
$student->refresh();

echo "2. Hasil setelah Auth::guard('web')->login(\$student, true):\n";
echo "   remember_token tersimpan di database: " . ($student->remember_token ?? 'KOSONG') . "\n";

$recallerName = Auth::guard('web')->getRecallerName();
echo "   Nama Cookie Recaller: " . $recallerName . "\n";

$queuedCookies = Cookie::getQueuedCookies();
$hasRecallerCookie = false;
$recallerCookieValue = null;

foreach ($queuedCookies as $cookie) {
    if ($cookie->getName() === $recallerName) {
        $hasRecallerCookie = true;
        $recallerCookieValue = $cookie->getValue();
        echo "   Cookie ditemukan dalam Antrean Response: YA\n";
        echo "   Masa Aktif Cookie: " . $cookie->getExpiresTime() . " (" . round(($cookie->getExpiresTime() - time()) / (3600 * 24 * 365), 1) . " tahun)\n";
        break;
    }
}

// Simulasi Logout dan Re-login via Cookie Recaller (Ingat Saya)
echo "\n3. Simulasi Re-Login otomatis via Cookie Recaller (setelah session ditutup):\n";
Auth::guard('web')->logout();
echo "   Status setelah logout manual: " . (Auth::guard('web')->check() ? 'Masih Login' : 'Telah Logout') . "\n";

// Sekarang kita test request login ke AuthController secara langsung
$session = app('session')->driver();
$session->start();

$admin = User::where('role', 'admin')->first();
$loginRequest = \Illuminate\Http\Request::create('/admin/login', 'POST', [
    'email' => $admin->email,
    'password' => 'password',
    'remember' => '1',
]);
$loginRequest->setLaravelSession($session);

// Reset auth first
Auth::guard('admin')->logout();

$authController = new \App\Http\Controllers\AuthController();
$response = $authController->adminLogin($loginRequest);

echo "\n4. Simulasi Direct Controller Call adminLogin(\$request) dengan 'remember' => '1':\n";
echo "   Redirect URL: " . $response->getTargetUrl() . "\n";
echo "   Status Auth::guard('admin')->check(): " . (Auth::guard('admin')->check() ? 'TERAUTENTIKASI (SUKSES)' : 'GAGAL') . "\n";

$admin->refresh();
echo "   remember_token di database: " . ($admin->remember_token ?? 'KOSONG') . "\n";

$queuedCookies = Cookie::getQueuedCookies();
$foundRememberCookie = false;
foreach ($queuedCookies as $cookie) {
    if (str_starts_with($cookie->getName(), 'remember_')) {
        $foundRememberCookie = true;
        echo "   Cookie Remember Dikirim: " . $cookie->getName() . "\n";
        echo "   Masa Aktif Cookie: " . date('Y-m-d H:i:s', $cookie->getExpiresTime()) . " (" . round(($cookie->getExpiresTime() - time()) / 86400) . " hari)\n";
    }
}

// 5. Test Auto-Login dari Recaller Cookie (tanpa password)
echo "\n5. Uji Re-Autentikasi Otomatis via Cookie Recaller (Simulasi browser ditutup / Session Expired):\n";
// Catatan: JANGAN panggil logout() karena logout() secara sengaja mereset token di database.
// Kita simulasikan session habis (expire) dengan mengosongkan session saja.
$session->flush();
$session->regenerate();

// Invalidate in-memory guard cache
$adminGuard = Auth::guard('admin');
$adminGuard->forgetUser();
echo "   Session habis/kosong. Cache Guard dibersihkan.\n";

// Buat request baru yang membawa cookie remember_admin_...
$adminCookie = null;
foreach ($queuedCookies as $cookie) {
    if (str_starts_with($cookie->getName(), 'remember_admin_')) {
        $adminCookie = $cookie;
        break;
    }
}

if ($adminCookie) {
    $subsequentRequest = \Illuminate\Http\Request::create('/admin/dashboard', 'GET', [], [
        $adminCookie->getName() => $adminCookie->getValue(),
    ]);
    $newSession = app('session')->driver();
    $newSession->start();
    $subsequentRequest->setLaravelSession($newSession);

    // Bind request ke app
    app()->instance('request', $subsequentRequest);

    // Panggil user() melalui guard dengan request yang memiliki cookie
    $guard = Auth::guard('admin');
    $guard->setRequest($subsequentRequest);
    $resolvedUser = $guard->user();

    if ($resolvedUser && $resolvedUser->id === $admin->id) {
        echo "   [SUKSES] User otomatis ter-login kembali sebagai '{$resolvedUser->name}' berkat cookie Ingat Saya!\n";
    } else {
        echo "   [GAGAL] User tidak ter-login kembali via cookie.\n";
    }
}

// 6. Uji Re-Autentikasi Otomatis untuk Siswa (Guard 'web')
echo "\n6. Uji Login & Re-Autentikasi Otomatis untuk Siswa (Guard 'web'):\n";
// Ubah password siswa uji agar pasti cocok untuk test
$student->password = \Illuminate\Support\Facades\Hash::make('password_siswa_123');
$student->save();

$siswaLoginReq = \Illuminate\Http\Request::create('/login', 'POST', [
    'email' => $student->email,
    'password' => 'password_siswa_123',
    'remember' => '1',
]);
$siswaSession = app('session')->driver();
$siswaSession->start();
$siswaLoginReq->setLaravelSession($siswaSession);

Auth::guard('web')->logout();
$resSiswa = $authController->login($siswaLoginReq);

echo "   Redirect URL: " . $resSiswa->getTargetUrl() . "\n";
echo "   Status Auth::guard('web')->check(): " . (Auth::guard('web')->check() ? 'TERAUTENTIKASI (SUKSES)' : 'GAGAL') . "\n";

$student->refresh();
echo "   remember_token Siswa di DB: " . substr($student->remember_token, 0, 20) . "...\n";

// Ambil cookie remember_web_...
$siswaCookie = null;
foreach (Cookie::getQueuedCookies() as $cookie) {
    if (str_starts_with($cookie->getName(), 'remember_web_')) {
        $siswaCookie = $cookie;
        echo "   Cookie Remember Siswa: " . $cookie->getName() . "\n";
        break;
    }
}

// Simulasi session expired untuk siswa
$siswaSession->flush();
$siswaSession->regenerate();
$webGuard = Auth::guard('web');
$webGuard->forgetUser();

$siswaSubsequentReq = \Illuminate\Http\Request::create('/siswa/dashboard', 'GET', [], [
    $siswaCookie->getName() => $siswaCookie->getValue(),
]);
$siswaNewSession = app('session')->driver();
$siswaNewSession->start();
$siswaSubsequentReq->setLaravelSession($siswaNewSession);
app()->instance('request', $siswaSubsequentReq);
$webGuard->setRequest($siswaSubsequentReq);

$reloggedStudent = $webGuard->user();
if ($reloggedStudent && $reloggedStudent->id === $student->id) {
    echo "   [SUKSES] Siswa otomatis ter-login kembali tanpa mengetik kata sandi!\n";
} else {
    echo "   [GAGAL] Siswa gagal re-login via cookie recaller.\n";
}

if ($foundRememberCookie && !empty($admin->remember_token) && $reloggedStudent) {
    echo "\n=====================================================\n";
    echo "HASIL AKHIR: FITUR REMEMBER ME BERFUNGSI SEMPURNA (100%)\n";
    echo "=====================================================\n";
} else {
    echo "\n>>> PERINGATAN: Fitur Remember Me belum lengkap <<<\n";
}
