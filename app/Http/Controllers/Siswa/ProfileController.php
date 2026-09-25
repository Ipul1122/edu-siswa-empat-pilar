<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::orderBy('name')->get(['id', 'province_id', 'name', 'type']);
        $dapilList = User::DAPIL_LIST;
        return view('siswa.profile.edit', compact('user', 'provinces', 'regencies', 'dapilList'));
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
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'regency_id' => [
                'required',
                'integer',
                Rule::exists('regencies', 'id')->where(function ($query) use ($request) {
                    return $query->where('province_id', $request->province_id);
                }),
            ],
            'dapil' => ['nullable', 'string'],
            'image' => [$user->image ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'address.required' => 'Alamat rumah tinggal wajib diisi.',
            'province_id.required' => 'Provinsi wajib dipilih.',
            'province_id.exists' => 'Pilihan Provinsi tidak valid.',
            'regency_id.required' => 'Kabupaten/Kota wajib dipilih.',
            'regency_id.exists' => 'Kabupaten/Kota tidak sesuai dengan Provinsi terpilih.',
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
        $user->province_id = $request->input('province_id');
        $user->regency_id = $request->input('regency_id');
        if ($request->filled('dapil')) {
            $user->dapil = $request->input('dapil');
        }

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
