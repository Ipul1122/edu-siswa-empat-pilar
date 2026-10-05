<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Quiz;
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
            ->with(['province', 'regency'])
            ->where('users.role', 'siswa');

        // Filter by Province & Regency
        if ($request->filled('province_id')) {
            $query->where('users.province_id', $request->province_id);
        }
        if ($request->filled('regency_id')) {
            $query->where('users.regency_id', $request->regency_id);
        }

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
        $provinces = \App\Models\Province::orderBy('name')->get();
        $regencies = $request->filled('province_id')
            ? \App\Models\Regency::where('province_id', $request->province_id)->orderBy('name')->get()
            : collect();
        $allRegencies = \App\Models\Regency::orderBy('name')->get(['id', 'province_id', 'name', 'type']);

        return view('admin.students.index', compact(
            'students',
            'totalRealMateriCount',
            'dapilList',
            'provinces',
            'regencies',
            'allRegencies',
            'sortBy',
            'order',
            'perPage'
        ));
    }

    /**
     * Display student details (materials read and quiz attempts).
     */
    public function show(User $student)
    {
        if ($student->role !== 'siswa') {
            abort(404);
        }

        // Real Materi (Seleksi) Counts
        $totalRealMateriCount = Quiz::query()->where('type', 'real')->count();
        $completedRealMateriCount = $student->attempts()
            ->whereHas('quiz', function($q) {
                $q->where('type', 'real');
            })
            ->distinct('quiz_id')
            ->count('quiz_id');

        // Fetch all Seleksi attempts by this student
        $attempts = $student->attempts()
            ->whereHas('quiz', function($q) {
                $q->where('type', 'real');
            })
            ->with('quiz')
            ->latest()
            ->get();

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
                'Paket Seleksi Selesai', 
                'Total Ujian Diikuti', 
                'Rerata Nilai (%)', 
                'Total Skor'
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

    /**
     * Grant a re-test to a school that experienced network/power outage trouble during exam day.
     * Requirement 7: "kalau ada yg trouble pada saat hari tes, diwajibkan melaksanakan tes ulang"
     */
    public function grantRetest(Request $request, User $student)
    {
        if ($student->role !== 'siswa') {
            abort(404);
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => 'Alasan izin tes ulang wajib diisi.',
        ]);

        $reason = $request->input('reason');
        $adminId = auth('admin')->user()?->id ?? auth()->user()?->id;

        // Remove previous attempt so the school can take the test again cleanly
        $attempts = $student->attempts()->get();
        foreach ($attempts as $a) {
            $a->delete();
        }

        $student->update([
            'is_troubled' => false,
            'trouble_notes' => 'Izin tes ulang disetujui oleh Admin (Alasan: ' . $reason . ') pada ' . now()->format('d/m/Y H:i') . ' WIB',
        ]);

        // Attempt to send email notification to the school PIC
        try {
            if ($student->email) {
                \Illuminate\Support\Facades\Mail::to($student->email)->send(new \App\Mail\OtpMail(
                    'RE-TEST',
                    'Persetujuan Sesi Tes Ulang - Seleksi Empat Pilar MPR RI',
                    "Permohonan tes ulang sekolah {$student->school_name} telah DISETUJUI oleh Panitia Pusat (Alasan: {$reason}). Silakan login kembali ke sistem untuk memulai sesi tes susulan."
                ));
            }
        } catch (\Exception $e) {
            logger()->error('Retest mail error: ' . $e->getMessage());
        }

        return back()->with('success', "Izin Tes Ulang untuk {$student->school_name} berhasil diaktifkan. Sekolah dapat segera melaksanakan tes susulan.");
    }

    /**
     * Broadcast announcement / schedule updates / zoom links to schools.
     * Requirement 7: "blast email, call center selama 13 minggu masa tes (3,5-4 bulan)"
     */
    public function broadcast(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'province_id' => 'nullable|integer',
        ], [
            'subject.required' => 'Judul pengumuman blast wajib diisi.',
            'message.required' => 'Isi pesan blast wajib diisi.',
        ]);

        $query = User::query()->where('role', '=', 'siswa');
        if ($request->filled('province_id')) {
            $query->where('province_id', $request->province_id);
        }

        $targetSchools = $query->get(['id', 'email', 'name', 'school_name', 'pic_name']);
        $count = $targetSchools->count();

        // Queue or send blast
        foreach ($targetSchools as $target) {
            try {
                \Illuminate\Support\Facades\Mail::to($target->email)->send(new \App\Mail\OtpMail(
                    'INFO-MPR',
                    $request->subject,
                    $request->message
                ));
            } catch (\Exception $e) {
                logger()->error('Broadcast email error for ' . $target->email . ': ' . $e->getMessage());
            }
        }

        return back()->with('success', "Pengumuman blast berhasil diproses dan dikirimkan ke {$count} akun sekolah.");
    }
}
