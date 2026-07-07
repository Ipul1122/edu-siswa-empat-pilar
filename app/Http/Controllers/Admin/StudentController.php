<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Material;
use App\Models\StudentProgress;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display a listing of students with their summary metrics.
     */
    public function index()
    {
        $attemptsStatsSub = DB::table('quiz_attempts')
            ->select('user_id')
            ->selectRaw('ROUND(AVG(score), 1) as avg_score')
            ->selectRaw('COUNT(*) as attempts_count')
            ->groupBy('user_id');

        $progressSub = DB::table('student_progress')
            ->select('user_id')
            ->selectRaw('COUNT(*) as completed_count')
            ->where('is_completed', true)
            ->groupBy('user_id');

        $students = User::query()
            ->select('users.*')
            ->selectRaw('COALESCE(progress_counts.completed_count, 0) as completed_progress_count')
            ->selectRaw('COALESCE(attempts_stats.avg_score, 0) as average_score')
            ->selectRaw('COALESCE(attempts_stats.attempts_count, 0) as total_quizzes_taken')
            ->leftJoinSub($progressSub, 'progress_counts', 'progress_counts.user_id', '=', 'users.id')
            ->leftJoinSub($attemptsStatsSub, 'attempts_stats', 'attempts_stats.user_id', '=', 'users.id')
            ->where('users.role', 'siswa')
            ->latest()
            ->get();

        foreach ($students as $student) {
            if ($student->total_quizzes_taken == 0) {
                $student->average_score = '-';
            }
        }

        $totalMaterialsCount = Material::query()->count('*');

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

        // Fetch completed material IDs for this student
        $completedMaterialIds = StudentProgress::query()->where('user_id', $student->id)
            ->where('is_completed', true)
            ->pluck('material_id')
            ->toArray();

        // Fetch text materials
        $textMaterials = Material::query()->where('type', 'text')->get();
        foreach ($textMaterials as $material) {
            $material->is_completed_by_student = in_array($material->id, $completedMaterialIds);
        }

        // Fetch video materials
        $videoMaterials = Material::query()->where('type', 'video')->get();
        foreach ($videoMaterials as $video) {
            $video->is_completed_by_student = in_array($video->id, $completedMaterialIds);
        }

        $totalTextCount = $textMaterials->count();
        $completedTextCount = $textMaterials->where('is_completed_by_student', true)->count();

        $totalVideoCount = $videoMaterials->count();
        $completedVideoCount = $videoMaterials->where('is_completed_by_student', true)->count();

        // Fetch all quiz attempts by this student
        $attempts = $student->attempts()->with('quiz')->latest()->get();

        // Calculate average score
        $averageScore = $attempts->count() > 0 
            ? round($attempts->avg('score'), 1) 
            : '-';

        return view('admin.students.show', compact(
            'student', 
            'textMaterials', 
            'videoMaterials', 
            'completedTextCount', 
            'totalTextCount',
            'completedVideoCount',
            'totalVideoCount',
            'attempts', 
            'averageScore'
        ));
    }

    /**
     * Export students' progress and scores report as a CSV.
     */
    public function export()
    {
        $progressSub = DB::table('student_progress')
            ->select('user_id')
            ->selectRaw('COUNT(*) as completed_count')
            ->where('is_completed', true)
            ->groupBy('user_id');

        $maxAttemptsSub = DB::table('quiz_attempts')
            ->select('user_id', 'quiz_id')
            ->selectRaw('MAX(score) as max_score')
            ->groupBy('user_id', 'quiz_id');

        $quizScoresSub = DB::table($maxAttemptsSub, 'max_attempts')
            ->select('user_id')
            ->selectRaw('SUM(max_score) as total_score')
            ->groupBy('user_id');

        $attemptsStatsSub = DB::table('quiz_attempts')
            ->select('user_id')
            ->selectRaw('ROUND(AVG(score), 1) as avg_score')
            ->selectRaw('COUNT(*) as attempts_count')
            ->groupBy('user_id');

        $students = User::query()
            ->select('users.*')
            ->selectRaw('COALESCE(progress_counts.completed_count, 0) as completed_progress_count')
            ->selectRaw('COALESCE(quiz_scores.total_score, 0) as total_quiz_score')
            ->selectRaw('(COALESCE(progress_counts.completed_count, 0) * 10 + COALESCE(quiz_scores.total_score, 0)) as points')
            ->selectRaw('COALESCE(attempts_stats.avg_score, 0) as average_score')
            ->selectRaw('COALESCE(attempts_stats.attempts_count, 0) as quizzes_count')
            ->leftJoinSub($progressSub, 'progress_counts', 'progress_counts.user_id', '=', 'users.id')
            ->leftJoinSub($quizScoresSub, 'quiz_scores', 'quiz_scores.user_id', '=', 'users.id')
            ->leftJoinSub($attemptsStatsSub, 'attempts_stats', 'attempts_stats.user_id', '=', 'users.id')
            ->where('users.role', 'siswa')
            ->orderByDesc('points')
            ->orderByDesc('average_score')
            ->orderBy('users.name')
            ->get();

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
        $progressSub = DB::table('student_progress')
            ->select('user_id')
            ->selectRaw('COUNT(*) as completed_count')
            ->where('is_completed', true)
            ->groupBy('user_id');

        $maxAttemptsSub = DB::table('quiz_attempts')
            ->select('user_id', 'quiz_id')
            ->selectRaw('MAX(score) as max_score')
            ->groupBy('user_id', 'quiz_id');

        $quizScoresSub = DB::table($maxAttemptsSub, 'max_attempts')
            ->select('user_id')
            ->selectRaw('SUM(max_score) as total_score')
            ->groupBy('user_id');

        $attemptsStatsSub = DB::table('quiz_attempts')
            ->select('user_id')
            ->selectRaw('ROUND(AVG(score), 1) as avg_score')
            ->selectRaw('COUNT(*) as attempts_count')
            ->groupBy('user_id');

        $students = User::query()
            ->select('users.*')
            ->selectRaw('COALESCE(progress_counts.completed_count, 0) as completed_progress_count')
            ->selectRaw('COALESCE(quiz_scores.total_score, 0) as total_quiz_score')
            ->selectRaw('(COALESCE(progress_counts.completed_count, 0) * 10 + COALESCE(quiz_scores.total_score, 0)) as points')
            ->selectRaw('COALESCE(attempts_stats.avg_score, 0) as average_score')
            ->selectRaw('COALESCE(attempts_stats.attempts_count, 0) as quizzes_count')
            ->leftJoinSub($progressSub, 'progress_counts', 'progress_counts.user_id', '=', 'users.id')
            ->leftJoinSub($quizScoresSub, 'quiz_scores', 'quiz_scores.user_id', '=', 'users.id')
            ->leftJoinSub($attemptsStatsSub, 'attempts_stats', 'attempts_stats.user_id', '=', 'users.id')
            ->where('users.role', 'siswa')
            ->orderByDesc('points')
            ->orderByDesc('average_score')
            ->orderBy('users.name')
            ->get();

        $totalMaterialsCount = Material::query()->count('*');

        return view('admin.students.report', compact('students', 'totalMaterialsCount'));
    }
}
