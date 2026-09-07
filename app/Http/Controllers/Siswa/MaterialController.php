<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MaterialController extends Controller
{
    /**
     * Display listing of materials grouped by pillar.
     */
    public function index()
    {
        $user = Auth::user();

        // Fetch completed material IDs for this student
        $completedMaterialIds = StudentProgress::query()->where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('material_id')
            ->toArray();

        // Get materials
        $materials = Material::query()->where('type', 'text')->latest()->get();

        // Group by pillar and append is_completed flag
        $groupedMaterials = [
            'pancasila' => [],
            'uud_1945' => [],
            'nkri' => [],
            'bhinneka_tunggal_ika' => [],
            'twk_kedinasan' => []
        ];

        foreach ($materials as $material) {
            $material->is_completed = in_array($material->id, $completedMaterialIds);
            if (array_key_exists($material->pillar, $groupedMaterials)) {
                $groupedMaterials[$material->pillar][] = $material;
            }
        }

        return view('siswa.materials.index', compact('groupedMaterials'));
    }

    /**
     * Display a specific material.
     */
    public function show(Material $material)
    {
        if ($material->type !== 'text') {
            abort(404);
        }

        $user = Auth::user();

        // Check if already completed
        $progress = StudentProgress::query()->where('user_id', $user->id)
            ->where('material_id', $material->id)
            ->first();

        $isCompleted = $progress ? $progress->is_completed : false;

        return view('siswa.materials.show', compact('material', 'isCompleted'));
    }

    /**
     * Mark material as completed/read.
     */
    public function complete(Request $request, Material $material)
    {
        $user = Auth::user();

        StudentProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'material_id' => $material->id,
            ],
            [
                'is_completed' => true,
            ]
        );

        return redirect()->route('siswa.materials.show', $material)
            ->with('success', 'Selamat! Anda telah menyelesaikan materi ini.');
    }
}
