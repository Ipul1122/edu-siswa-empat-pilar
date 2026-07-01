<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use App\Models\StudentProgress;

class StudentController extends Controller
{
    /**
     * Display a listing of students with their summary metrics.
     */
    public function index()
    {
        $students = User::where('role', 'siswa')
            ->withCount(['progress as completed_progress_count' => function ($query) {
                $query->where('is_completed', true);
            }])
            ->with(['attempts'])
            ->latest()
            ->get();

        $totalMaterialsCount = Material::count();

        // Calculate average score for each student manually in php or using SQL
        foreach ($students as $student) {
            $attempts = $student->attempts;
            $student->average_score = $attempts->count() > 0 
                ? round($attempts->avg('score'), 1) 
                : '-';
            $student->total_quizzes_taken = $attempts->count();
        }

        return view('admin.students.index', compact('students', 'totalMaterialsCount'));
    }

    /**
     * Display student details (materials read and quiz attempts).
     */
    public function show(User $student)
    {
        if ($student->role !== 'siswa') {
            abort(404);
        }

        $totalMaterialsCount = Material::count();
        
        // Fetch all materials and check if this student has completed them
        $materials = Material::all();
        $completedMaterialIds = StudentProgress::where('user_id', $student->id)
            ->where('is_completed', true)
            ->pluck('material_id')
            ->toArray();

        foreach ($materials as $material) {
            $material->is_completed_by_student = in_array($material->id, $completedMaterialIds);
        }

        // Fetch all quiz attempts by this student
        $attempts = $student->attempts()->with('quiz')->latest()->get();

        // Calculate average score
        $averageScore = $attempts->count() > 0 
            ? round($attempts->avg('score'), 1) 
            : '-';

        return view('admin.students.show', compact('student', 'materials', 'attempts', 'averageScore', 'totalMaterialsCount'));
    }
}
