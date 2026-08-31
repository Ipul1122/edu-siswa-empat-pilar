<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoMaterialController extends Controller
{
    /**
     * Display a listing of video materials.
     */
    public function index()
    {
        $materials = Material::query()->where('type', '=', 'video', 'and')->latest()->get();
        return view('admin.videos.index', compact('materials'));
    }

    /**
     * Show form to create video material.
     */
    public function create()
    {
        return view('admin.videos.create');
    }

    /**
     * Store new video material.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pillar' => ['required', 'string', 'in:pancasila,uud_1945,nkri,bhinneka_tunggal_ika,twk_kedinasan'],
            'title' => ['required', 'string', 'max:255', 'unique:materials,title'],
            'video_source_type' => ['required', 'string', 'in:upload,url'],
            'video_file' => ['required_if:video_source_type,upload', 'nullable', 'file', 'mimes:mp4,webm,mov,ogg,mkv,avi', 'max:102400'], // max 100MB
            'video_url' => ['required_if:video_source_type,url', 'nullable', 'string', 'max:1000'],
            'read_time' => ['required', 'integer', 'min:1'],
            'content' => ['nullable', 'string'],
        ], [
            'pillar.required' => 'Kategori / Pilar wajib dipilih.',
            'pillar.in' => 'Kategori / Pilar tidak valid.',
            'title.required' => 'Judul video wajib diisi.',
            'title.unique' => 'Judul video sudah digunakan.',
            'video_source_type.required' => 'Pilih metode sumber video.',
            'video_file.required_if' => 'File video MP4 wajib diunggah.',
            'video_file.mimes' => 'Format file video harus berupa MP4, WebM, MOV, OGG, atau MKV.',
            'video_file.max' => 'Ukuran file video maksimal 100MB.',
            'video_url.required_if' => 'Tautan / link video wajib diisi.',
            'read_time.required' => 'Estimasi durasi video wajib diisi.',
            'read_time.integer' => 'Estimasi durasi video harus berupa angka.',
            'read_time.min' => 'Estimasi durasi video minimal 1 menit.',
        ]);

        $videoUrl = null;
        if ($request->input('video_source_type') === 'upload' && $request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('videos', 'public');
            $videoUrl = 'storage/' . $path;
        } else {
            $videoUrl = $request->input('video_url');
        }

        $data = [
            'pillar' => $request->input('pillar'),
            'title' => $request->input('title'),
            'type' => 'video',
            'video_url' => $videoUrl,
            'read_time' => $request->input('read_time'),
            'content' => $request->input('content') ?: '<p>Belum ada deskripsi untuk video ini.</p>',
        ];

        Material::create($data);

        return redirect()->route('admin.videos.index')
            ->with('success', 'Materi video berhasil ditambahkan!');
    }

    /**
     * Show form to edit video material.
     */
    public function edit(Material $video)
    {
        if ($video->type !== 'video') {
            abort(404);
        }
        return view('admin.videos.edit', compact('video'));
    }

    /**
     * Update video material.
     */
    public function update(Request $request, Material $video)
    {
        if ($video->type !== 'video') {
            abort(404);
        }

        $request->validate([
            'pillar' => ['required', 'string', 'in:pancasila,uud_1945,nkri,bhinneka_tunggal_ika,twk_kedinasan'],
            'title' => ['required', 'string', 'max:255', 'unique:materials,title,' . $video->id],
            'video_source_type' => ['required', 'string', 'in:upload,url'],
            'video_file' => ['nullable', 'file', 'mimes:mp4,webm,mov,ogg,mkv,avi', 'max:102400'], // max 100MB
            'video_url' => ['required_if:video_source_type,url', 'nullable', 'string', 'max:1000'],
            'read_time' => ['required', 'integer', 'min:1'],
            'content' => ['nullable', 'string'],
        ], [
            'pillar.required' => 'Kategori / Pilar wajib dipilih.',
            'pillar.in' => 'Kategori / Pilar tidak valid.',
            'title.required' => 'Judul video wajib diisi.',
            'title.unique' => 'Judul video sudah digunakan.',
            'video_source_type.required' => 'Pilih metode sumber video.',
            'video_file.mimes' => 'Format file video harus berupa MP4, WebM, MOV, OGG, atau MKV.',
            'video_file.max' => 'Ukuran file video maksimal 100MB.',
            'video_url.required_if' => 'Tautan / link video wajib diisi.',
            'read_time.required' => 'Estimasi durasi video wajib diisi.',
            'read_time.integer' => 'Estimasi durasi video harus berupa angka.',
            'read_time.min' => 'Estimasi durasi video minimal 1 menit.',
        ]);

        $videoUrl = $video->video_url;

        if ($request->input('video_source_type') === 'upload') {
            if ($request->hasFile('video_file')) {
                // Delete old storage file if existed
                if ($video->video_url && str_starts_with($video->video_url, 'storage/')) {
                    $oldPath = str_replace('storage/', '', $video->video_url);
                    Storage::disk('public')->delete($oldPath);
                }
                $path = $request->file('video_file')->store('videos', 'public');
                $videoUrl = 'storage/' . $path;
            }
        } else {
            // Switched to URL, delete old storage file if existed
            if ($video->video_url && str_starts_with($video->video_url, 'storage/')) {
                $oldPath = str_replace('storage/', '', $video->video_url);
                Storage::disk('public')->delete($oldPath);
            }
            $videoUrl = $request->input('video_url');
        }

        $data = [
            'pillar' => $request->input('pillar'),
            'title' => $request->input('title'),
            'video_url' => $videoUrl,
            'read_time' => $request->input('read_time'),
            'content' => $request->input('content') ?: '<p>Belum ada deskripsi untuk video ini.</p>',
        ];

        $video->update($data);

        return redirect()->route('admin.videos.index')
            ->with('success', 'Materi video berhasil diperbarui!');
    }

    /**
     * Delete video material.
     */
    public function destroy(Material $video)
    {
        if ($video->type !== 'video') {
            abort(404);
        }

        // Delete uploaded file if stored locally
        if ($video->video_url && str_starts_with($video->video_url, 'storage/')) {
            $oldPath = str_replace('storage/', '', $video->video_url);
            Storage::disk('public')->delete($oldPath);
        }

        Material::destroy($video->id);

        return redirect()->route('admin.videos.index')
            ->with('success', 'Materi video berhasil dihapus!');
    }
}
