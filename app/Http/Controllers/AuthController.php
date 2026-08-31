<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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

        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('web')->user();
            
            // Check if user is Siswa
            if ($user->role !== 'siswa') {
                Auth::guard('web')->logout();
                
                return back()->withErrors([
                    'email' => 'Akses ditolak. Halaman login ini khusus untuk Siswa.',
                ])->onlyInput('email');
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

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('admin')->user();
            
            // Check if user is Admin
            if ($user->role !== 'admin') {
                Auth::guard('admin')->logout();
                
                return back()->withErrors([
                    'email' => 'Akses ditolak. Halaman login ini khusus untuk Admin.',
                ])->onlyInput('email');
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
        $dapilDetails = User::DAPIL_DETAILS;
        return view('auth.register', compact('dapilDetails'));
    }

    /**
     * Handle registration request (strictly for Siswa).
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'school_name' => ['required', 'string', 'max:100'],
            'dapil' => ['required', 'string', 'in:' . implode(',', User::DAPIL_LIST)],
            'address' => ['required', 'string', 'max:500'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'dapil.required' => 'Daerah Pemilihan (Dapil) wajib dipilih.',
            'dapil.in' => 'Pilihan Daerah Pemilihan (Dapil) tidak valid.',
            'address.required' => 'Alamat rumah tinggal wajib diisi.',
            'image.required' => 'Foto profil siswa wajib diunggah.',
            'image.image' => 'File foto harus berupa gambar.',
            'image.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        // Upload image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('avatars', 'public');
        }

        // Generate 6 digit OTP
        $otp = rand(100000, 999999);

        // Store registration details and OTP in session
        session()->put('register_details', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'class_name' => 'SMA/SMK',
            'school_name' => $request->school_name,
            'dapil' => $request->dapil,
            'address' => $request->address,
            'image' => $imagePath,
        ]);
        session()->put('register_otp', $otp);
        session()->put('register_otp_expires_at', Carbon::now()->addMinutes(15));

        // Send OTP mail
        try {
            Mail::to($request->email)->send(new OtpMail(
                $otp,
                'Verifikasi Kode OTP Pendaftaran - Empat Pilar',
                'Terima kasih telah melakukan pendaftaran di platform pendidikan Empat Pilar Kebangsaan. Gunakan kode OTP di bawah ini untuk memverifikasi akun Anda:'
            ));
        } catch (\Exception $e) {
            logger()->error('Mail error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal mengirim email verifikasi. Pastikan konfigurasi email di .env sudah benar.');
        }

        return redirect()->route('register.verify_otp')
            ->with('success', 'Kode OTP telah dikirimkan ke email Anda.');
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
            'name' => $details['name'],
            'email' => $details['email'],
            'password' => $details['password'],
            'role' => 'siswa',
            'class_name' => $details['class_name'] ?? 'SMA/SMK',
            'school_name' => $details['school_name'],
            'dapil' => $details['dapil'] ?? null,
            'address' => $details['address'] ?? null,
            'image' => $details['image'] ?? null,
            'email_verified_at' => Carbon::now(),
        ]);

        // Clear Session
        session()->forget(['register_details', 'register_otp', 'register_otp_expires_at']);

        // Log user in
        Auth::guard('web')->login($user);

        return redirect()->route('siswa.dashboard')
            ->with('success', 'Registrasi berhasil dan email Anda telah diverifikasi! Selamat belajar.');
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

        try {
            Mail::to($request->email)->send(new OtpMail(
                $otp,
                'Kode OTP Pemulihan Kata Sandi - Empat Pilar',
                'Kami menerima permintaan untuk menyetel ulang kata sandi akun Siswa Anda. Gunakan kode OTP di bawah ini untuk menyetel ulang sandi Anda:'
            ));
        } catch (\Exception $e) {
            logger()->error('Mail error: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email OTP. Pastikan konfigurasi SMTP di .env benar.');
        }

        return redirect()->route('password.verify_otp')
            ->with('success', 'Kode OTP telah dikirimkan ke email Anda.');
    }

    /**
     * Show reset password OTP verification form.
     */
    public function showResetVerifyOtp()
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.verify-reset-otp');
    }

    /**
     * Handle password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus berjumlah 6 digit.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        if (!session()->has('reset_email')) {
            return redirect()->route('password.request')->with('error', 'Sesi pemulihan Anda telah berakhir. Silakan ulangi.');
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

        // Update password
        $user = User::query()->where('email', '=', $email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Clean up
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        session()->forget('reset_email');

        return redirect()->route('login')
            ->with('success', 'Kata sandi Anda berhasil disetel ulang. Silakan masuk dengan sandi baru.');
    }

    /**
     * Handle resend reset OTP.
     */
    public function resetResendOtp()
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request');
        }

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
        if ($request->input('guard') === 'admin' || str_contains($request->headers->get('referer'), '/admin')) {
            Auth::guard('admin')->logout();
        } else {
            Auth::guard('web')->logout();
        }

        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}
