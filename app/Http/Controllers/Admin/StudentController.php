<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Material;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display a listing of students with their summary metrics, filter by Dapil, sorting (asc/desc), and custom pagination.
     */
    public function index(Request $request)
    {
        $attemptsStatsSub = DB::table('quiz_attempts')
            ->select('user_id')
            ->selectRaw('ROUND(AVG(score), 1) as avg_score')
            ->selectRaw('COUNT(*) as attempts_count')
            ->groupBy('user_id');

        // Progress based on completed Real Materi quizzes
        $realProgressSub = DB::table('quiz_attempts')
            ->join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->where('quizzes.type', '=', 'real')
            ->select('quiz_attempts.user_id')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.quiz_id) as completed_count')
            ->groupBy('quiz_attempts.user_id');

        $query = User::query()
            ->select('users.*')
            ->selectRaw('COALESCE(real_progress.completed_count, 0) as completed_progress_count')
            ->selectRaw('COALESCE(attempts_stats.avg_score, 0) as average_score')
            ->selectRaw('COALESCE(attempts_stats.attempts_count, 0) as total_quizzes_taken')
            ->leftJoinSub($realProgressSub, 'real_progress', 'real_progress.user_id', '=', 'users.id')
            ->leftJoinSub($attemptsStatsSub, 'attempts_stats', 'attempts_stats.user_id', '=', 'users.id')
            ->where('users.role', 'siswa');

        // Filter by Dapil
        if ($request->filled('dapil')) {
            $query->where('users.dapil', $request->dapil);
        }

        // Search query (name, email, school_name, dapil)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.school_name', 'like', "%{$search}%")
                  ->orWhere('users.dapil', 'like', "%{$search}%");
            });
        }

        // Sorting (asc - desc)
        $allowedSorts = ['name', 'school_name', 'dapil', 'created_at', 'average_score', 'total_quizzes_taken', 'completed_progress_count'];
        $sortBy = in_array($request->query('sort_by'), $allowedSorts) ? $request->query('sort_by') : 'name';
        $order = strtolower($request->query('order', 'asc')) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'average_score') {
            $query->orderBy('average_score', $order);
        } elseif ($sortBy === 'total_quizzes_taken') {
            $query->orderBy('total_quizzes_taken', $order);
        } elseif ($sortBy === 'completed_progress_count') {
            $query->orderBy('completed_progress_count', $order);
        } else {
            $query->orderBy("users.{$sortBy}", $order);
        }

        // Custom Pagination (per_page: 10, 25, 50, 100)
        $perPage = in_array((int)$request->query('per_page'), [10, 25, 50, 100]) ? (int)$request->query('per_page') : 10;
        $students = $query->paginate($perPage)->withQueryString();

        foreach ($students as $student) {
            if ($student->total_quizzes_taken == 0) {
                $student->average_score = '-';
            }
        }

        // Total count based on Real Materi quizzes in database
        $totalRealMateriCount = Quiz::query()->where('type', '=', 'real')->count('*');
        $dapilList = User::DAPIL_LIST;

        return view('admin.students.index', compact('students', 'totalRealMateriCount', 'dapilList', 'sortBy', 'order', 'perPage'));
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

        $pillarScores = [];
        foreach (['pancasila', 'uud_1945', 'nkri', 'bhinneka_tunggal_ika'] as $p) {
            $pillarAttempts = $attempts->filter(function($a) use ($p) {
                return $a->quiz && $a->quiz->pillar === $p;
            });
            $pillarScores[$p] = $pillarAttempts->count() > 0 ? round($pillarAttempts->avg('score')) : 0;
        }

        return view('admin.students.show', compact(
            'student', 
            'textMaterials', 
            'videoMaterials', 
            'completedTextCount', 
            'totalTextCount', 
            'completedVideoCount', 
            'totalVideoCount', 
            'completedRealMateriCount',
            'totalRealMateriCount',
            'attempts', 
            'averageScore',
            'pillarScores'
        ));
    }

    /**
     * Export students' progress and scores report as a CSV.
     */
    public function export()
    {
        $realProgressSub = DB::table('quiz_attempts')
            ->join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->where('quizzes.type', '=', 'real')
            ->select('quiz_attempts.user_id')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.quiz_id) as completed_count')
            ->groupBy('quiz_attempts.user_id');

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
            ->selectRaw('COALESCE(real_progress.completed_count, 0) as completed_progress_count')
            ->selectRaw('COALESCE(quiz_scores.total_score, 0) as total_quiz_score')
            ->selectRaw('(COALESCE(real_progress.completed_count, 0) * 10 + COALESCE(quiz_scores.total_score, 0)) as points')
            ->selectRaw('COALESCE(attempts_stats.avg_score, 0) as average_score')
            ->selectRaw('COALESCE(attempts_stats.attempts_count, 0) as quizzes_count')
            ->leftJoinSub($realProgressSub, 'real_progress', 'real_progress.user_id', '=', 'users.id')
            ->leftJoinSub($quizScoresSub, 'quiz_scores', 'quiz_scores.user_id', '=', 'users.id')
            ->leftJoinSub($attemptsStatsSub, 'attempts_stats', 'attempts_stats.user_id', '=', 'users.id')
            ->where('users.role', 'siswa')
            ->orderByDesc('points')
            ->orderByDesc('average_score')
            ->orderBy('users.name')
            ->get();

        $totalRealMateriCount = Quiz::query()->where('type', '=', 'real')->count('*');

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=laporan_siswa_empat_pilar_' . date('Ymd_His') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($students, $totalRealMateriCount) {
            $file = fopen('php://output', 'w');
            // Write BOM for UTF-8 compatibility with MS Excel
            fputs($file, chr(239) . chr(187) . chr(191));
            
            // CSV Headers
            fputcsv($file, [
                'Peringkat', 
                'Nama Siswa', 
                'Email', 
                'Sekolah', 
                'Dapil',
                'Alamat',
                'Real Materi Selesai', 
                'Total Kuis Diikuti', 
                'Rerata Nilai (%)', 
                'Total Poin'
            ]);

            foreach ($students as $index => $student) {
                fputcsv($file, [
                    $index + 1,
                    $student->name,
                    $student->email,
                    $student->school_name,
                    $student->dapil ?? '-',
                    $student->address ?? '-',
                    $student->completed_progress_count . ' / ' . $totalRealMateriCount,
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
        $realProgressSub = DB::table('quiz_attempts')
            ->join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->where('quizzes.type', '=', 'real')
            ->select('quiz_attempts.user_id')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.quiz_id) as completed_count')
            ->groupBy('quiz_attempts.user_id');

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
            ->selectRaw('COALESCE(real_progress.completed_count, 0) as completed_progress_count')
            ->selectRaw('COALESCE(quiz_scores.total_score, 0) as total_quiz_score')
            ->selectRaw('(COALESCE(real_progress.completed_count, 0) * 10 + COALESCE(quiz_scores.total_score, 0)) as points')
            ->selectRaw('COALESCE(attempts_stats.avg_score, 0) as average_score')
            ->selectRaw('COALESCE(attempts_stats.attempts_count, 0) as quizzes_count')
            ->leftJoinSub($realProgressSub, 'real_progress', 'real_progress.user_id', '=', 'users.id')
            ->leftJoinSub($quizScoresSub, 'quiz_scores', 'quiz_scores.user_id', '=', 'users.id')
            ->leftJoinSub($attemptsStatsSub, 'attempts_stats', 'attempts_stats.user_id', '=', 'users.id')
            ->where('users.role', 'siswa')
            ->orderByDesc('points')
            ->orderByDesc('average_score')
            ->orderBy('users.name')
            ->get();

        $totalRealMateriCount = Quiz::query()->where('type', '=', 'real')->count('*');

        return view('admin.students.report', compact('students', 'totalRealMateriCount'));
    }
}
