<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    /**
     * Display the student leaderboard with Province and Regency filters based on Seleksi only.
     */
    public function index(Request $request)
    {
        // Enforce Requirement 3: Only Admin can view all participants' results.
        // Each school participant can only view their own score.
        return redirect()->route('siswa.my-results')
            ->with('info', 'Papan peringkat keseluruhan peserta bersifat tertutup dan hanya dapat diakses oleh Panitia/Admin Seleksi MPR RI. Hasil tes sekolah Anda dapat dilihat di bawah ini.');
    }
}
