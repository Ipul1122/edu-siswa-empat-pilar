<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display a listing of materials.
     */
    public function index()
    {
        $materials = Material::query()->where('type', 'text')->latest()->get();
        return view('admin.materials.index', compact('materials'));
    }

    /**
     * Show form to create material.
     */
    public function create()
    {
        return view('admin.materials.create');
    }

    /**
     * Store new material.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pillar' => ['required', 'string', 'in:pancasila,uud_1945,nkri,bhinneka_tunggal_ika'],
            'title' => ['required', 'string', 'max:255', 'unique:materials,title'],
            'content' => ['required', 'string'],
            'read_time' => ['required', 'integer', 'min:1'],
        ], [
            'pillar.required' => 'Pilar Kebangsaan wajib dipilih.',
            'pillar.in' => 'Pilar Kebangsaan tidak valid.',
            'title.required' => 'Judul materi wajib diisi.',
            'title.unique' => 'Judul materi sudah digunakan.',
            'content.required' => 'Konten materi wajib diisi.',
            'read_time.required' => 'Estimasi waktu baca wajib diisi.',
            'read_time.integer' => 'Estimasi waktu baca harus berupa angka.',
            'read_time.min' => 'Estimasi waktu baca minimal 1 menit.',
        ]);

        $data = $request->all();
        $data['type'] = 'text';
        Material::create($data);

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi berhasil ditambahkan!');
    }

    /**
     * Show form to edit material.
     */
    public function edit(Material $material)
    {
        if ($material->type !== 'text') {
            abort(404);
        }
        return view('admin.materials.edit', compact('material'));
    }

    /**
     * Update material.
     */
    public function update(Request $request, Material $material)
    {
        if ($material->type !== 'text') {
            abort(404);
        }

        $request->validate([
            'pillar' => ['required', 'string', 'in:pancasila,uud_1945,nkri,bhinneka_tunggal_ika'],
            'title' => ['required', 'string', 'max:255', 'unique:materials,title,' . $material->id],
            'content' => ['required', 'string'],
            'read_time' => ['required', 'integer', 'min:1'],
        ], [
            'pillar.required' => 'Pilar Kebangsaan wajib dipilih.',
            'pillar.in' => 'Pilar Kebangsaan tidak valid.',
            'title.required' => 'Judul materi wajib diisi.',
            'title.unique' => 'Judul materi sudah digunakan.',
            'content.required' => 'Konten materi wajib diisi.',
            'read_time.required' => 'Estimasi waktu baca wajib diisi.',
            'read_time.integer' => 'Estimasi waktu baca harus berupa angka.',
            'read_time.min' => 'Estimasi waktu baca minimal 1 menit.',
        ]);

        $material->update($request->all());

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi berhasil diperbarui!');
    }

    /**
     * Delete material.
     */
    public function destroy(Material $material)
    {
        if ($material->type !== 'text') {
            abort(404);
        }

        Material::destroy($material->id);

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi berhasil dihapus!');
    }
}
