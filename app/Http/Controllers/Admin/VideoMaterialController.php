<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class VideoMaterialController extends Controller
{
    /**
     * Display a listing of video materials.
     */
    public function index()
    {
        $materials = Material::query()->where('type', 'video')->latest()->get();
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
            'pillar' => ['required', 'string', 'in:pancasila,uud_1945,nkri,bhinneka_tunggal_ika'],
            'title' => ['required', 'string', 'max:255', 'unique:materials,title'],
            'video_url' => ['required', 'string'],
            'read_time' => ['required', 'integer', 'min:1'],
            'content' => ['nullable', 'string'],
        ], [
            'pillar.required' => 'Pilar Kebangsaan wajib dipilih.',
            'pillar.in' => 'Pilar Kebangsaan tidak valid.',
            'title.required' => 'Judul video wajib diisi.',
            'title.unique' => 'Judul video sudah digunakan.',
            'video_url.required' => 'Tautan video wajib diisi.',
            'read_time.required' => 'Estimasi durasi video wajib diisi.',
            'read_time.integer' => 'Estimasi durasi video harus berupa angka.',
            'read_time.min' => 'Estimasi durasi video minimal 1 menit.',
        ]);

        $data = $request->all();
        $data['type'] = 'video';
        if (empty($data['content'])) {
            $data['content'] = '<p>Belum ada deskripsi untuk video ini.</p>';
        }
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
            'pillar' => ['required', 'string', 'in:pancasila,uud_1945,nkri,bhinneka_tunggal_ika'],
            'title' => ['required', 'string', 'max:255', 'unique:materials,title,' . $video->id],
            'video_url' => ['required', 'string'],
            'read_time' => ['required', 'integer', 'min:1'],
            'content' => ['nullable', 'string'],
        ], [
            'pillar.required' => 'Pilar Kebangsaan wajib dipilih.',
            'pillar.in' => 'Pilar Kebangsaan tidak valid.',
            'title.required' => 'Judul video wajib diisi.',
            'title.unique' => 'Judul video sudah digunakan.',
            'video_url.required' => 'Tautan video wajib diisi.',
            'read_time.required' => 'Estimasi durasi video wajib diisi.',
            'read_time.integer' => 'Estimasi durasi video harus berupa angka.',
            'read_time.min' => 'Estimasi durasi video minimal 1 menit.',
        ]);

        $data = $request->all();
        if (empty($data['content'])) {
            $data['content'] = '<p>Belum ada deskripsi untuk video ini.</p>';
        }
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

        Material::destroy($video->id);

        return redirect()->route('admin.videos.index')
            ->with('success', 'Materi video berhasil dihapus!');
    }
}
