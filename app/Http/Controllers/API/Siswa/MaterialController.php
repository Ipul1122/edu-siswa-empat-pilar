<?php

namespace App\Http\Controllers\API\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MaterialController extends Controller
{
    /**
     * Display a listing of materials grouped by pillar with completion status.
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
        $materials = Material::all();

        // Group by pillar and append is_completed flag
        $groupedMaterials = [
            'pancasila' => [],
            'uud_1945' => [],
            'nkri' => [],
            'bhinneka_tunggal_ika' => []
        ];

        foreach ($materials as $material) {
            $data = [
                'id' => $material->id,
                'pillar' => $material->pillar,
                'title' => $material->title,
                'read_time' => $material->read_time,
                'is_completed' => in_array($material->id, $completedMaterialIds),
                'created_at' => $material->created_at,
            ];
            
            if (array_key_exists($material->pillar, $groupedMaterials)) {
                $groupedMaterials[$material->pillar][] = $data;
            }
        }

        return response()->json([
            'status' => 'success',
            'materials' => $groupedMaterials
        ]);
    }

    /**
     * Display a specific material.
     */
    public function show(Material $material)
    {
        $user = Auth::user();

        // Check if already completed
        $progress = StudentProgress::query()->where('user_id', $user->id)
            ->where('material_id', $material->id)
            ->first();

        $isCompleted = $progress ? (bool) $progress->is_completed : false;

        return response()->json([
            'status' => 'success',
            'material' => [
                'id' => $material->id,
                'pillar' => $material->pillar,
                'title' => $material->title,
                'content' => $material->content,
                'read_time' => $material->read_time,
                'is_completed' => $isCompleted,
                'created_at' => $material->created_at,
            ]
        ]);
    }

    /**
     * Mark material as completed/read.
     */
    public function complete(Material $material)
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

        // Clear leaderboard cache
        Cache::forget('leaderboard_data');

        return response()->json([
            'status' => 'success',
            'message' => 'Selamat! Anda telah menyelesaikan materi ini.',
            'material_id' => $material->id,
            'is_completed' => true,
        ]);
    }
}
