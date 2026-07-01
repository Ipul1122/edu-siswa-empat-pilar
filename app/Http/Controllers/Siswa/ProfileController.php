<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('siswa.profile.edit', compact('user'));
    }

    /**
     * Update the student's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'class_name' => ['required', 'string', 'in:X,XI,XII'],
            'school_name' => ['required', 'string', 'max:100'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'class_name.required' => 'Kelas wajib diisi.',
            'class_name.in' => 'Pilihan kelas tidak valid.',
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->name = $request->name;
        $user->class_name = $request->class_name;
        $user->school_name = $request->school_name;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('siswa.profile.edit')
            ->with('success', 'Profil Anda berhasil diperbarui!');
    }
}
