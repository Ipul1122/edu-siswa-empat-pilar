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
        $students = User::query()->where('role', 'siswa')
            ->withCount(['progress as completed_progress_count' => function ($query) {
                $query->where('is_completed', true);
            }])
            ->with(['attempts'])
            ->latest()
            ->get();

        $totalMaterialsCount = Material::query()->count();

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

        $totalMaterialsCount = Material::query()->count();
        
        // Fetch all materials and check if this student has completed them
        $materials = Material::all();
        $completedMaterialIds = StudentProgress::query()->where('user_id', $student->id)
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

    /**
     * Export students' progress and scores report as a CSV.
     */
    public function export()
    {
        $students = User::query()->where('role', 'siswa')
            ->withCount(['progress as completed_progress_count' => function ($query) {
                $query->where('is_completed', true);
            }])
            ->with(['attempts'])
            ->get();

        foreach ($students as $student) {
            $highestQuizScores = [];
            foreach ($student->attempts as $attempt) {
                $quizId = $attempt->quiz_id;
                if (!isset($highestQuizScores[$quizId]) || $attempt->score > $highestQuizScores[$quizId]) {
                    $highestQuizScores[$quizId] = $attempt->score;
                }
            }
            $student->points = ($student->completed_progress_count * 10) + array_sum($highestQuizScores);
            $student->average_score = $student->attempts->count() > 0 
                ? round($student->attempts->avg('score'), 1) 
                : 0;
            $student->quizzes_count = $student->attempts->count();
        }

        $students = $students->sort(function ($a, $b) {
            if ($b->points !== $a->points) {
                return $b->points <=> $a->points;
            }
            if ($b->average_score !== $a->average_score) {
                return $b->average_score <=> $a->average_score;
            }
            return strcmp($a->name, $b->name);
        })->values();

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=laporan_siswa_empat_pilar_' . date('Ymd_His') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            // Write BOM for UTF-8 compatibility with MS Excel
            fputs($file, chr(239) . chr(187) . chr(191));
            
            // CSV Headers
            fputcsv($file, [
                'Peringkat', 
                'Nama Siswa', 
                'Email', 
                'Kelas', 
                'Sekolah', 
                'Materi Dibaca', 
                'Kuis Diikuti', 
                'Rerata Nilai (%)', 
                'Total Poin'
            ]);

            foreach ($students as $index => $student) {
                fputcsv($file, [
                    $index + 1,
                    $student->name,
                    $student->email,
                    $student->class_name,
                    $student->school_name,
                    $student->completed_progress_count,
                    $student->quizzes_count,
                    $student->average_score,
                    $student->points
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show print-friendly report page for printing/saving as PDF.
     */
    public function report()
    {
        $students = User::query()->where('role', 'siswa')
            ->withCount(['progress as completed_progress_count' => function ($query) {
                $query->where('is_completed', true);
            }])
            ->with(['attempts'])
            ->get();

        foreach ($students as $student) {
            $highestQuizScores = [];
            foreach ($student->attempts as $attempt) {
                $quizId = $attempt->quiz_id;
                if (!isset($highestQuizScores[$quizId]) || $attempt->score > $highestQuizScores[$quizId]) {
                    $highestQuizScores[$quizId] = $attempt->score;
                }
            }
            $student->points = ($student->completed_progress_count * 10) + array_sum($highestQuizScores);
            $student->average_score = $student->attempts->count() > 0 
                ? round($student->attempts->avg('score'), 1) 
                : 0;
            $student->quizzes_count = $student->attempts->count();
        }

        $students = $students->sort(function ($a, $b) {
            if ($b->points !== $a->points) {
                return $b->points <=> $a->points;
            }
            if ($b->average_score !== $a->average_score) {
                return $b->average_score <=> $a->average_score;
            }
            return strcmp($a->name, $b->name);
        })->values();

        $totalMaterialsCount = Material::query()->count();

        return view('admin.students.report', compact('students', 'totalMaterialsCount'));
    }
}
