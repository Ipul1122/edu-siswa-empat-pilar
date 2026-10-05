<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Display the student leaderboard for administrators with Province and Regency filters.
     */
    public function index(Request $request)
    {
        $selectedProvinceId = $request->input('province_id');
        $selectedRegencyId = $request->input('regency_id');
        $search = trim($request->input('search', ''));

        $provinces = Province::orderBy('name')->get();
        $regencies = $selectedProvinceId 
            ? Regency::where('province_id', $selectedProvinceId)->orderBy('name')->get()
            : collect();

        $allRegencies = Regency::orderBy('name')->get(['id', 'province_id', 'name', 'type']);

        // Progress based on completed Seleksi packages
        $seleksiProgressSub = DB::table('quiz_attempts')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->where('quizzes.type', 'real')
            ->select('quiz_attempts.user_id')
            ->selectRaw('COUNT(DISTINCT quiz_attempts.quiz_id) as completed_count')
            ->groupBy('quiz_attempts.user_id');

        $maxAttemptsSub = DB::table('quiz_attempts')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->where('quizzes.type', 'real')
            ->select('quiz_attempts.user_id', 'quiz_attempts.quiz_id')
            ->selectRaw('MAX(quiz_attempts.score) as max_score')
            ->groupBy('quiz_attempts.user_id', 'quiz_attempts.quiz_id');

        $quizScoresSub = DB::table($maxAttemptsSub, 'max_attempts')
            ->select('user_id')
            ->selectRaw('SUM(max_score) as total_score')
            ->groupBy('user_id');

        $attemptsStatsSub = DB::table('quiz_attempts')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->where('quizzes.type', 'real')
            ->select('quiz_attempts.user_id')
            ->selectRaw('ROUND(AVG(quiz_attempts.score), 1) as avg_score')
            ->selectRaw('COUNT(*) as attempts_count')
            ->groupBy('quiz_attempts.user_id');

        $query = User::query()
            ->select('users.*')
            ->selectRaw('COALESCE(seleksi_progress.completed_count, 0) as completed_seleksi_count')
            ->selectRaw('COALESCE(quiz_scores.total_score, 0) as total_quiz_score')
            ->selectRaw('COALESCE(quiz_scores.total_score, 0) as points')
            ->selectRaw('COALESCE(attempts_stats.avg_score, 0) as average_score')
            ->selectRaw('COALESCE(attempts_stats.attempts_count, 0) as quizzes_count')
            ->leftJoinSub($seleksiProgressSub, 'seleksi_progress', 'seleksi_progress.user_id', '=', 'users.id')
            ->leftJoinSub($quizScoresSub, 'quiz_scores', 'quiz_scores.user_id', '=', 'users.id')
            ->leftJoinSub($attemptsStatsSub, 'attempts_stats', 'attempts_stats.user_id', '=', 'users.id')
            ->with(['province', 'regency'])
            ->where('users.role', 'siswa');

        if ($selectedProvinceId) {
            $query->where('users.province_id', $selectedProvinceId);
        }

        if ($selectedRegencyId) {
            $query->where('users.regency_id', $selectedRegencyId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.school_name', 'like', "%{$search}%");
            });
        }

        $filterTop9 = $request->boolean('top9_only');

        $leaderboard = $query
            ->orderByDesc('points')
            ->orderByDesc('average_score')
            ->orderBy('users.name')
            ->get();

        // Assign Rank and Qualification Status (Top 9, Reserve, etc.)
        foreach ($leaderboard as $index => $school) {
            $rank = $index + 1;
            $school->calculated_rank = $rank;
            if ($rank <= 9) {
                $school->qualification_status = 'Lolos Babak Berikutnya';
                $school->qualification_badge = 'badge-success';
            } elseif ($rank <= 11) {
                $school->qualification_status = 'Cadangan (Waitlist)';
                $school->qualification_badge = 'badge-warning';
            } else {
                $school->qualification_status = 'Belum Lolos';
                $school->qualification_badge = 'badge-secondary';
            }
        }

        // Apply Top 9 Filter if requested
        if ($filterTop9) {
            $leaderboard = $leaderboard->take(9);
        }

        // Calculate KPI Metrics
        $totalRankedStudents = $leaderboard->count();
        $highestPoints = $totalRankedStudents > 0 ? $leaderboard->max('points') : 0;
        $averageScore = $totalRankedStudents > 0 ? round($leaderboard->avg('average_score'), 1) : 0;
        $totalRealMateri = Quiz::query()->where('type', '=', 'real')->count();

        // Podium (Top 3)
        $topThree = $leaderboard->take(3);

        return view('admin.leaderboard.index', compact(
            'leaderboard',
            'topThree',
            'totalRankedStudents',
            'highestPoints',
            'averageScore',
            'totalRealMateri',
            'provinces',
            'regencies',
            'allRegencies',
            'selectedProvinceId',
            'selectedRegencyId',
            'search',
            'filterTop9'
        ));
    }

    /**
     * Export all participants results to Spreadsheet (.csv with UTF-8 BOM for Excel).
     * Requirements: Nama User (Sekolah), Email, Score, Region, Duration, Status.
     */
    public function exportSpreadsheet(Request $request)
    {
        $selectedProvinceId = $request->input('province_id');
        $selectedRegencyId = $request->input('regency_id');

        $query = User::query()
            ->with(['province', 'regency', 'attempts'])
            ->where('role', 'siswa');

        if ($selectedProvinceId) {
            $query->where('province_id', $selectedProvinceId);
        }
        if ($selectedRegencyId) {
            $query->where('regency_id', $selectedRegencyId);
        }

        $schools = $query->get();

        // Calculate score for each school
        $data = [];
        foreach ($schools as $school) {
            $latestAttempt = $school->attempts()->latest()->first();
            $score = $latestAttempt ? $latestAttempt->score : 0;
            $durationSeconds = $latestAttempt ? $latestAttempt->duration_seconds_taken : 0;
            $durM = floor($durationSeconds / 60);
            $durS = $durationSeconds % 60;
            $completedAt = $latestAttempt ? $latestAttempt->created_at->format('d/m/Y H:i') : '-';

            $data[] = [
                'school_name' => $school->school_name ?? $school->name,
                'email' => $school->email,
                'province' => $school->province ? $school->province->name : '-',
                'regency' => $school->regency ? $school->regency->name : '-',
                'pic_name' => $school->pic_name ?? '-',
                'whatsapp' => $school->whatsapp ?? '-',
                'score' => $score,
                'duration' => sprintf('%02d:%02d', $durM, $durS),
                'completed_at' => $completedAt,
                'raw_score' => $score,
                'raw_duration' => $durationSeconds,
            ];
        }

        // Sort by Score DESC, then Duration ASC
        usort($data, function ($a, $b) {
            if ($a['raw_score'] !== $b['raw_score']) {
                return $b['raw_score'] <=> $a['raw_score'];
            }
            return $a['raw_duration'] <=> $b['raw_duration'];
        });

        // Filter to top 9 if requested
        $isTop9 = $request->boolean('top9_only');
        if ($isTop9) {
            $data = array_slice($data, 0, 9);
        }

        $filename = $isTop9 
            ? 'rekap_top9_lolos_seleksi_' . date('Ymd_His') . '.csv'
            : 'rekap_nilai_seleksi_empat_pilar_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=' . $filename,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            // Write BOM for Microsoft Excel UTF-8
            fputs($file, chr(239) . chr(187) . chr(191));

            // Header columns
            fputcsv($file, [
                'Peringkat',
                'Nama User (Sekolah)',
                'Email',
                'Provinsi',
                'Kabupaten/Kota',
                'Nama Guru Pembina (PIC)',
                'No. WhatsApp PIC',
                'Skor Nilai Akhir',
                'Durasi Pengerjaan',
                'Waktu Submit',
                'Status Seleksi (Top 9)'
            ]);

            foreach ($data as $index => $row) {
                $rank = $index + 1;
                $status = ($rank <= 9) ? 'LOLOS TOP 9 (Babak Berikutnya)' : (($rank <= 11) ? 'Cadangan' : 'Selesai');
                fputcsv($file, [
                    $rank,
                    $row['school_name'],
                    $row['email'],
                    $row['province'],
                    $row['regency'],
                    $row['pic_name'],
                    $row['whatsapp'],
                    $row['score'],
                    $row['duration'],
                    $row['completed_at'],
                    $status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Top 9 Qualified Schools specifically for Wednesday announcement.
     */
    public function exportTop9(Request $request)
    {
        $request->merge(['top9_only' => true]);
        return $this->exportSpreadsheet($request);
    }
}
