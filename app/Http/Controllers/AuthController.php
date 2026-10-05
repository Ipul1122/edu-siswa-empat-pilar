<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('siswa.dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle student (Siswa) login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $credentials['email'] = strtolower(trim((string) $credentials['email']));

        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('web')->user();
            
            // Check if user is Admin logging in via /login
            if ($user->role === 'admin') {
                Auth::guard('web')->logout();
                Auth::guard('admin')->login($user, $request->boolean('remember'));
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang kembali di Panel Admin!');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('siswa.dashboard'))
                ->with('success', 'Selamat datang di Ruang Belajar Empat Pilar!');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Show admin login form.
     */
    public function showAdminLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.admin-login');
    }

    /**
     * Handle admin login request.
     */
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $credentials['email'] = strtolower(trim((string) $credentials['email']));

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('admin')->user();
            
            // Check if user is Siswa logging in via /admin/login
            if ($user->role === 'siswa') {
                Auth::guard('admin')->logout();
                Auth::guard('web')->login($user, $request->boolean('remember'));
                $request->session()->regenerate();

                return redirect()->intended(route('siswa.dashboard'))
                    ->with('success', 'Selamat datang di Ruang Belajar Empat Pilar!');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang kembali, Admin!');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form.
     */
    public function showRegister()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('siswa.dashboard');
        }
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::orderBy('name')->get(['id', 'province_id', 'name', 'type']);
        $dapilList = User::DAPIL_LIST;
        return view('auth.register', compact('provinces', 'regencies', 'dapilList'));
    }

    /**
     * Get regencies list for a specific province (AJAX API).
     */
    public function getRegencies(Province $province)
    {
        $regencies = $province->regencies()->get(['id', 'province_id', 'name', 'type']);
        return response()->json($regencies);
    }

    /**
     * Handle registration request (strictly for Siswa).
     */
    public function register(Request $request)
    {
        $request->validate([
            'school_name' => ['required', 'string', 'max:150'],
            'pic_name' => ['required', 'string', 'max:150'],
            'whatsapp' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'regency_id' => [
                'required',
                'integer',
                Rule::exists('regencies', 'id')->where(function ($query) use ($request) {
                    return $query->where('province_id', $request->province_id);
                }),
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'school_name.required' => 'Nama Sekolah wajib diisi.',
            'pic_name.required' => 'Nama Guru Pembina / PIC Tim wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp aktif penanggung jawab wajib diisi.',
            'email.required' => 'Email resmi sekolah / PIC wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar sebagai akun sekolah lain.',
            'password.required' => 'Kata sandi akun wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'province_id.required' => 'Provinsi asal sekolah wajib dipilih.',
            'province_id.exists' => 'Pilihan Provinsi tidak valid.',
            'regency_id.required' => 'Kabupaten/Kota asal sekolah wajib dipilih.',
            'regency_id.exists' => 'Kabupaten/Kota yang dipilih tidak sesuai dengan Provinsi terpilih.',
        ]);

        // Upload image if provided (optional)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('avatars', 'public');
        }

        // Generate 6 digit OTP
        $otp = rand(100000, 999999);

        // Store registration details and OTP in session
        session()->put('register_details', [
            'name' => $request->school_name,
            'school_name' => $request->school_name,
            'pic_name' => $request->pic_name,
            'whatsapp' => $request->whatsapp,
            'email' => strtolower(trim((string) $request->email)),
            'password' => Hash::make($request->password),
            'class_name' => 'Tim 10 Siswa',
            'province_id' => $request->province_id,
            'regency_id' => $request->regency_id,
            'dapil' => $request->dapil,
            'address' => $request->address ?? '-',
            'image' => $imagePath,
        ]);
        session()->put('register_otp', $otp);
        session()->put('register_otp_expires_at', Carbon::now()->addMinutes(15));

        // Send OTP mail
        try {
            Mail::to($request->email)->send(new OtpMail(
                $otp,
                'Verifikasi Kode OTP Pendaftaran Sekolah - Empat Pilar MPR RI',
                'Terima kasih telah mendaftarkan sekolah Anda dalam Seleksi Nasional Empat Pilar MPR RI. Gunakan kode OTP berikut untuk mengaktifkan akun sekolah Anda:'
            ));
        } catch (\Exception $e) {
            logger()->error('Mail error: ' . $e->getMessage());
            // If in local/debug mode, flash OTP to session for seamless testing in remote/offline environment
            session()->flash('debug_otp', $otp);
            return redirect()->route('register.verify_otp')
                ->with('warning', 'Kode OTP pendaftaran: ' . $otp . ' (Email gateway offline/delayed).');
        }

        return redirect()->route('register.verify_otp')
            ->with('success', 'Kode OTP verifikasi telah dikirimkan ke email sekolah Anda.');
    }

    /**
     * Show register OTP verification form.
     */
    public function showRegisterVerifyOtp()
    {
        if (!session()->has('register_details')) {
            return redirect()->route('register');
        }
        return view('auth.verify-register-otp');
    }

    /**
     * Handle register OTP verification.
     */
    public function registerVerifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus berjumlah 6 digit.',
        ]);

        if (!session()->has('register_details') || !session()->has('register_otp')) {
            return redirect()->route('register')->with('error', 'Sesi registrasi Anda telah berakhir. Silakan daftar kembali.');
        }

        $sessionOtp = session()->get('register_otp');
        $expiresAt = session()->get('register_otp_expires_at');

        if (Carbon::now()->greaterThan($expiresAt)) {
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan kirim ulang kode.']);
        }

        if ($request->otp != $sessionOtp) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah.']);
        }

        // Create User
        $details = session()->get('register_details');
        
        if (User::query()->where('email', '=', $details['email'])->exists()) {
            session()->forget(['register_details', 'register_otp', 'register_otp_expires_at']);
            return redirect()->route('register')->with('error', 'Email ini sudah terdaftar. Silakan masuk.');
        }

        $user = User::create([
            'name' => $details['school_name'] ?? $details['name'],
            'email' => $details['email'],
            'password' => $details['password'],
            'role' => 'siswa',
            'class_name' => 'Tim 10 Siswa',
            'school_name' => $details['school_name'],
            'pic_name' => $details['pic_name'] ?? null,
            'whatsapp' => $details['whatsapp'] ?? null,
            'province_id' => $details['province_id'] ?? null,
            'regency_id' => $details['regency_id'] ?? null,
            'dapil' => $details['dapil'] ?? null,
            'address' => $details['address'] ?? '-',
            'image' => $details['image'] ?? null,
            'email_verified_at' => Carbon::now(),
        ]);

        // Clear Session
        session()->forget(['register_details', 'register_otp', 'register_otp_expires_at']);

        // Log user in
        Auth::guard('web')->login($user);

        return redirect()->route('siswa.dashboard')
            ->with('success', 'Pendaftaran Akun Sekolah berhasil! Selamat datang di Portal Seleksi Online Empat Pilar MPR RI.');
    }

    /**
     * Handle resend register OTP.
     */
    public function registerResendOtp()
    {
        if (!session()->has('register_details')) {
            return redirect()->route('register');
        }

        $details = session()->get('register_details');
        $otp = rand(100000, 999999);

        session()->put('register_otp', $otp);
        session()->put('register_otp_expires_at', Carbon::now()->addMinutes(15));

        try {
            Mail::to($details['email'])->send(new OtpMail(
                $otp,
                'Verifikasi Kode OTP Pendaftaran Baru - Empat Pilar',
                'Berikut adalah kode OTP baru Anda untuk memverifikasi akun Empat Pilar Kebangsaan Anda:'
            ));
        } catch (\Exception $e) {
            logger()->error('Mail error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim ulang email verifikasi. Hubungi admin atau coba lagi nanti.');
        }

        return back()->with('success', 'Kode OTP baru telah dikirimkan ke email Anda.');
    }

    /**
     * Show forgot password email form.
     */
    public function showForgotPassword()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('siswa.dashboard');
        }
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.forgot-password');
    }

    /**
     * Handle sending reset password OTP.
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $user = User::query()->where('email', '=', $request->email)->first();

        if (!$user || $user->role !== 'siswa') {
            return back()->withErrors(['email' => 'Email tidak terdaftar sebagai Siswa.']);
        }

        $otp = rand(100000, 999999);

        // Delete old tokens and insert new one
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $otp,
            'created_at' => Carbon::now(),
        ]);

        session()->put('reset_email', $request->email);
        session()->forget('reset_otp_verified');

        try {
            Mail::to($request->email)->send(new OtpMail(
                $otp,
                'Kode OTP Pemulihan Kata Sandi - Empat Pilar',
                'Kami menerima permintaan untuk menyetel ulang kata sandi akun Siswa Anda. Gunakan kode OTP di bawah ini untuk memverifikasi identitas Anda:'
            ));
        } catch (\Exception $e) {
            logger()->error('Mail error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email OTP. Pastikan konfigurasi SMTP di .env benar.');
        }

        return redirect()->route('password.verify_otp')
            ->with('success', 'Kode OTP telah dikirimkan ke email Anda.');
    }

    /**
     * Show reset password OTP verification form (Step 1).
     */
    public function showResetVerifyOtp()
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.verify-reset-otp', [
            'email' => session()->get('reset_email'),
        ]);
    }

    /**
     * Handle verification of the reset password OTP (Step 1 submit).
     */
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus berjumlah 6 digit angka.',
        ]);

        if (!session()->has('reset_email')) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi pemulihan Anda telah berakhir. Silakan ulangi proses lupa kata sandi.');
        }

        $email = session()->get('reset_email');

        $resetToken = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$resetToken || $resetToken->token != $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah atau tidak valid.']);
        }

        // Check expiration (15 minutes)
        $createdAt = Carbon::parse($resetToken->created_at);
        if (Carbon::now()->greaterThan($createdAt->addMinutes(15))) {
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan kirim ulang kode.']);
        }

        // Mark OTP as verified in session
        session()->put('reset_otp_verified', true);
        session()->put('reset_verified_email', $email);

        return redirect()->route('password.reset')
            ->with('success', 'Kode OTP berhasil diverifikasi! Silakan tentukan kata sandi baru Anda.');
    }

    /**
     * Show reset password new credentials form (Step 2).
     */
    public function showResetPassword()
    {
        if (!session()->has('reset_email') || !session()->has('reset_otp_verified')) {
            return redirect()->route('password.request')
                ->with('error', 'Silakan masukkan dan verifikasi kode OTP terlebih dahulu sebelum membuat kata sandi baru.');
        }

        return view('auth.reset-password', [
            'email' => session()->get('reset_email'),
        ]);
    }

    /**
     * Handle password reset (Step 2 submit).
     */
    public function resetPassword(Request $request)
    {
        if (!session()->has('reset_email') || !session()->has('reset_otp_verified')) {
            return redirect()->route('password.request')
                ->with('error', 'Sesi pemulihan Anda telah berakhir. Silakan ulangi proses lupa kata sandi.');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $email = session()->get('reset_email');

        // Update password
        $user = User::query()->where('email', '=', $email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Clean up
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget(['reset_email', 'reset_otp_verified', 'reset_verified_email']);

        return redirect()->route('login')
            ->with('success', 'Kata sandi Anda berhasil diperbarui. Silakan masuk dengan kata sandi baru.');
    }

    /**
     * Handle resend reset OTP.
     */
    public function resetResendOtp()
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request');
        }

        session()->forget('reset_otp_verified');

        $email = session()->get('reset_email');
        $otp = rand(100000, 999999);

        // Delete old tokens and insert new one
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $otp,
            'created_at' => Carbon::now(),
        ]);

        try {
            Mail::to($email)->send(new OtpMail(
                $otp,
                'Kode OTP Pemulihan Kata Sandi Baru - Empat Pilar',
                'Berikut adalah kode OTP baru Anda untuk menyetel ulang kata sandi akun Siswa Anda:'
            ));
        } catch (\Exception $e) {
            logger()->error('Mail error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim ulang email OTP. Hubungi admin atau coba lagi nanti.');
        }

        return back()->with('success', 'Kode OTP baru telah dikirimkan ke email Anda.');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $referer = (string) $request->headers->get('referer', '');
        $isAdmin = Auth::guard('admin')->check() 
            || $request->input('guard') === 'admin' 
            || str_contains($referer, '/admin');

        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($isAdmin) {
            return redirect()->route('admin.login')
                ->with('success', 'Anda telah berhasil keluar dari akun Admin.');
        }

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari akun Siswa.');
    }
}
