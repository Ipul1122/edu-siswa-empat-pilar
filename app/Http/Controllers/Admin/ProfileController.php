<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the admin profile settings form.
     */
    public function edit()
    {
        /** @var User $admin */
        $admin = Auth::guard('admin')->user();
        return view('admin.profile.edit', compact('admin'));
    }

    /**
     * Update the admin's profile (photo, name, email, password).
     */
    public function update(Request $request)
    {
        /** @var User $admin */
        $admin = Auth::guard('admin')->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];

        $messages = [
            'name.required' => 'Nama administrator wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'image.image' => 'File foto harus berupa gambar.',
            'image.mimes' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran foto maksimal 2MB.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ];

        $request->validate($rules, $messages);

        $admin->name = $request->input('name');
        $admin->email = $request->input('email');

        // Handle Image Upload
        if ($request->hasFile('image')) {
            if ($admin->image && Storage::disk('public')->exists($admin->image)) {
                Storage::disk('public')->delete($admin->image);
            }
            $imagePath = $request->file('image')->store('avatars', 'public');
            $admin->image = $imagePath;
        }

        // Handle Password Change
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->input('password'));
        }

        $admin->save();

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Profil dan akun administrator berhasil diperbarui!');
    }
}
