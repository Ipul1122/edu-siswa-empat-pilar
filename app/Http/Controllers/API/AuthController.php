<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Handle Student (Siswa) login request.
     */
    public function siswaLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // Check role
            if ($user->role !== 'siswa') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akses ditolak. Halaman login ini khusus untuk Siswa.'
                ], 401);
            }
            
            // Generate API Token
            $token = Str::random(80);
            $user->api_token = $token;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil.',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'class_name' => $user->class_name,
                    'school_name' => $user->school_name,
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Email atau kata sandi yang Anda masukkan salah.'
        ], 401);
    }

    /**
     * Handle Admin login request.
     */
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // Check role
            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akses ditolak. Halaman login ini khusus untuk Admin.'
                ], 401);
            }
            
            // Generate API Token
            $token = Str::random(80);
            $user->api_token = $token;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil.',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'class_name' => $user->class_name,
                    'school_name' => $user->school_name,
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Email atau kata sandi yang Anda masukkan salah.'
        ], 401);
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
            'class_name' => ['required', 'string', 'in:X,XI,XII'],
            'school_name' => ['required', 'string', 'max:100'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'class_name.required' => 'Kelas wajib diisi.',
            'class_name.in' => 'Pilihan kelas tidak valid.',
            'school_name.required' => 'Nama sekolah wajib diisi.',
        ]);

        $token = Str::random(80);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
            'class_name' => $request->class_name,
            'school_name' => $request->school_name,
            'api_token' => $token,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'class_name' => $user->class_name,
                'school_name' => $user->school_name,
            ]
        ], 210); // Created/Success
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil.'
        ]);
    }
}
