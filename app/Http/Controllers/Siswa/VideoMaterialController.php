<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class VideoMaterialController extends Controller
{
    /**
     * Display listing of video materials grouped by pillar.
     */
    public function index()
    {
        $user = Auth::user();

        // Fetch completed material IDs for this student
        $completedMaterialIds = StudentProgress::query()->where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('material_id')
            ->toArray();

        // Get only video materials
        $materials = Material::query()->where('type', 'video')->latest()->get();

        // Group by pillar and append is_completed flag
        $groupedVideos = [
            'pancasila' => [],
            'uud_1945' => [],
            'nkri' => [],
            'bhinneka_tunggal_ika' => [],
            'twk_kedinasan' => []
        ];

        foreach ($materials as $material) {
            $material->is_completed = in_array($material->id, $completedMaterialIds);
            if (array_key_exists($material->pillar, $groupedVideos)) {
                $groupedVideos[$material->pillar][] = $material;
            }
        }

        return view('siswa.videos.index', compact('groupedVideos'));
    }

    /**
     * Display a specific video material.
     */
    public function show(Material $material)
    {
        if ($material->type !== 'video') {
            abort(404);
        }

        $user = Auth::user();

        // Check if already completed
        $progress = StudentProgress::query()->where('user_id', $user->id)
            ->where('material_id', $material->id)
            ->first();

        $isCompleted = $progress ? $progress->is_completed : false;

        return view('siswa.videos.show', compact('material', 'isCompleted'));
    }

    /**
     * Mark video material as completed/watched.
     */
    public function complete(Request $request, Material $material)
    {
        if ($material->type !== 'video') {
            abort(404);
        }

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

        return redirect()->route('siswa.videos.show', $material)
            ->with('success', 'Selamat! Anda telah selesai menonton video ini.');
    }
}
