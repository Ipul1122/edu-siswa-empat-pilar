<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        $dapilDetails = User::DAPIL_DETAILS;
        return view('siswa.profile.edit', compact('user', 'dapilDetails'));
    }

    /**
     * Update the student's profile.
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'school_name' => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:500'],
            'dapil' => ['required', 'string', 'in:' . implode(',', User::DAPIL_LIST)],
            'image' => [$user->image ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'address.required' => 'Alamat rumah tinggal wajib diisi.',
            'dapil.required' => 'Daerah Pemilihan (Dapil) wajib dipilih.',
            'dapil.in' => 'Pilihan Daerah Pemilihan (Dapil) tidak valid.',
            'image.required' => 'Foto profil siswa wajib diunggah.',
            'image.image' => 'File foto harus berupa gambar.',
            'image.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran foto maksimal 2MB.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ];

        $request->validate($rules, $messages);

        $user->name = $request->input('name');
        $user->school_name = $request->input('school_name');
        $user->address = $request->input('address');
        $user->dapil = $request->input('dapil');

        // Handle Image Upload
        if ($request->hasFile('image')) {
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $imagePath = $request->file('image')->store('avatars', 'public');
            $user->image = $imagePath;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('siswa.profile.edit')
            ->with('success', 'Biodata profil dan foto Anda berhasil diperbarui!');
    }
}
