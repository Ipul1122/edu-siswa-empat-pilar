<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

echo "===============================================================\n";
echo "      UJI VALIDASI I/O: 2 TAHAP FORGOT PASSWORD & RESET        \n";
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

$controller = new AuthController();
$student = User::query()->where('role', '=', 'siswa', 'and')->first();

if (!$student) {
    die("Error: Siswa tidak ditemukan di database.\n");
}

echo "Target Siswa Uji: ID={$student->id}, Email={$student->email}\n\n";

// -------------------------------------------------------------------------
// TEST 1: Gatekeeper tanpa session reset_email
// -------------------------------------------------------------------------
echo "--- UJI 1: GATEKEEPER SESI BELUM ADA ---\n";
$sessionStore = session()->driver();
$sessionStore->start();
$sessionStore->flush();

$reqVerifyNoSession = Request::create('/forgot-password/verify-otp', 'GET');
$reqVerifyNoSession->setLaravelSession($sessionStore);
$res1 = $controller->showResetVerifyOtp();
assertTest(
    $res1->isRedirect(route('password.request')),
    "Akses langsung /forgot-password/verify-otp tanpa sesi otomatis redirect ke halaman request email."
);

$res2 = $controller->showResetPassword();
assertTest(
    $res2->isRedirect(route('password.request')),
    "Akses langsung /forgot-password/reset tanpa verifikasi OTP otomatis redirect ke halaman request email."
);
echo "\n";

// -------------------------------------------------------------------------
// TEST 2: Simulasi Email Terkirim & Cek OTP Salah
// -------------------------------------------------------------------------
echo "--- UJI 2: VERIFIKASI KODE OTP (LANGKAH 1) ---\n";
$mockOtp = '765432';

// Set up reset token di database
DB::table('password_reset_tokens')->where('email', $student->email)->delete();
DB::table('password_reset_tokens')->insert([
    'email' => $student->email,
    'token' => $mockOtp,
    'created_at' => Carbon::now(),
]);
session()->put('reset_email', $student->email);

// Test OTP salah
$wrongReq = Request::create('/forgot-password/verify-otp', 'POST', ['otp' => '111111']);
$wrongReq->setLaravelSession($sessionStore);

try {
    $resWrong = $controller->verifyResetOtp($wrongReq);
    // Back with errors
    assertTest(
        session()->has('errors'),
        "Input OTP salah ('111111') ditolak sistem dan menghasilkan pesan kesalahan."
    );
} catch (\Illuminate\Validation\ValidationException $e) {
    assertTest(true, "Input OTP salah ditolak melalui ValidationException.");
}

// Pastikan sesi reset_otp_verified BELUM ada saat OTP salah
assertTest(
    !session()->has('reset_otp_verified'),
    "Flag 'reset_otp_verified' belum aktif saat OTP salah."
);

// Test OTP benar
$correctReq = Request::create('/forgot-password/verify-otp', 'POST', ['otp' => $mockOtp]);
$correctReq->setLaravelSession($sessionStore);
$resCorrect = $controller->verifyResetOtp($correctReq);

assertTest(
    $resCorrect->isRedirect(route('password.reset')),
    "Input OTP benar ('$mockOtp') berhasil dan otomatis me-redirect ke halaman Langkah 2 (/forgot-password/reset)."
);
assertTest(
    session()->get('reset_otp_verified') === true,
    "Flag 'reset_otp_verified' berhasil diset 'true' di sesi."
);
echo "\n";

// -------------------------------------------------------------------------
// TEST 3: Halaman Baru Buat Kata Sandi (LANGKAH 2)
// -------------------------------------------------------------------------
echo "--- UJI 3: HALAMAN FORM KATA SANDI BARU (LANGKAH 2) ---\n";
$viewResetRes = $controller->showResetPassword();
assertTest(
    $viewResetRes->name() === 'auth.reset-password',
    "Halaman Langkah 2 sukses merender view terpisah: 'auth.reset-password'."
);
assertTest(
    $viewResetRes->getData()['email'] === $student->email,
    "View reset-password menerima data email terverifikasi: '{$student->email}'."
);
echo "\n";

// -------------------------------------------------------------------------
// TEST 4: Submit Kata Sandi Baru
// -------------------------------------------------------------------------
echo "--- UJI 4: PENYIMPANAN KATA SANDI BARU & LOGIN RE-CHECK ---\n";
$newPassword = 'PasswordBaru99!';

// Submit valid new password
$resetReq = Request::create('/forgot-password/reset', 'POST', [
    'password' => $newPassword,
    'password_confirmation' => $newPassword,
]);
$resetReq->setLaravelSession($sessionStore);
$resSubmitPass = $controller->resetPassword($resetReq);

assertTest(
    $resSubmitPass->isRedirect(route('login')),
    "Submit kata sandi baru sukses dan me-redirect ke halaman /login."
);

// Cek di database apakah password user sudah ter-update
$student->refresh();
assertTest(
    Hash::check($newPassword, $student->password),
    "Password siswa di database cocok 100% dengan kata sandi baru yang baru disetel."
);

// Cek token di password_reset_tokens sudah terhapus
$tokenInDb = DB::table('password_reset_tokens')->where('email', $student->email)->first();
assertTest(
    $tokenInDb === null,
    "Token OTP di tabel 'password_reset_tokens' otomatis dibersihkan (deleted)."
);

// Cek sesi dibersihkan
assertTest(
    !session()->has('reset_email') && !session()->has('reset_otp_verified'),
    "Sesi pemulihan ('reset_email', 'reset_otp_verified') otomatis dibersihkan dari server."
);

// Test login dengan password baru
$loginReq = Request::create('/login', 'POST', [
    'email' => $student->email,
    'password' => $newPassword,
]);
$loginSession = app('session')->driver();
$loginSession->start();
$loginReq->setLaravelSession($loginSession);

Auth::guard('web')->logout();
$loginRes = $controller->login($loginReq);

assertTest(
    Auth::guard('web')->check() && Auth::guard('web')->id() === $student->id,
    "Siswa sukses login dengan kata sandi baru yang baru saja disetel!"
);
echo "\n";

echo "===============================================================\n";
echo "HASIL AKHIR: $passed UJI BERHASIL (PASS), $failed UJI GAGAL (FAIL)\n";
echo "===============================================================\n";

exit($failed > 0 ? 1 : 0);
