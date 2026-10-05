<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ZoomSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ZoomSessionController extends Controller
{
    /**
     * Display listing of Zoom meetings for students.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = ZoomSession::query()
            ->where('is_active', true)
            ->withCount('participants')
            ->orderBy('start_time', 'asc');

        // Filter search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter pillar
        if ($request->filled('pillar')) {
            $query->where('pillar', $request->pillar);
        }

        $sessions = $query->paginate(9)->withQueryString();

        // Get array of session IDs that this student has already joined
        $joinedSessionIds = $user->zoomSessions()->pluck('zoom_sessions.id')->toArray();

        // Count metrics for student dashboard summary
        $totalAvailableSessions = ZoomSession::where('is_active', true)->count();
        $myJoinedCount = count($joinedSessionIds);

        // Find available alternative sessions (not full and not joined yet)
        $alternativeQuery = ZoomSession::query()
            ->where('is_active', true)
            ->withCount('participants')
            ->whereRaw('(SELECT COUNT(*) FROM zoom_participants WHERE zoom_participants.zoom_session_id = zoom_sessions.id) < zoom_sessions.capacity')
            ->orderBy('start_time', 'asc');

        if (!empty($joinedSessionIds)) {
            $alternativeQuery->whereNotIn('id', $joinedSessionIds);
        }

        $alternativeSessions = $alternativeQuery->take(4)->get();

        return view('siswa.zoom.index', compact(
            'sessions',
            'joinedSessionIds',
            'totalAvailableSessions',
            'myJoinedCount',
            'alternativeSessions'
        ));
    }

    /**
     * Student joins a Zoom session.
     */
    public function join(Request $request, ZoomSession $zoom_session)
    {
        $user = Auth::user();

        if (!$zoom_session->is_active) {
            return back()->with('error', 'Sesi Zoom ini sedang tidak aktif atau pendaftaran telah ditutup.');
        }

        // Check if student already joined before
        if ($zoom_session->hasUserJoined($user->id)) {
            return redirect()->away($zoom_session->zoom_link);
        }

        $deviceInfo = substr((string) $request->userAgent(), 0, 255);

        try {
            DB::transaction(function () use ($zoom_session, $user, $deviceInfo) {
                // Lock session row to ensure strict capacity checks without race conditions
                $lockedSession = ZoomSession::query()->where('id', '=', $zoom_session->id)->lockForUpdate()->first();
                $currentCount = $lockedSession ? $lockedSession->participants()->count() : 0;

                if ($lockedSession && $currentCount >= $lockedSession->capacity) {
                    throw new \RuntimeException('CAPACITY_EXCEEDED');
                }

                if ($lockedSession) {
                    $lockedSession->participants()->syncWithoutDetaching([
                        $user->id => [
                            'joined_at' => now(),
                            'last_ping_at' => now(),
                            'status' => 'connected',
                            'device_info' => $deviceInfo,
                            'notes' => 'Terhubung Tim Sekolah (10 Siswa)',
                        ]
                    ]);
                }
            });

            return redirect()->route('siswa.zoom-sessions.index')
                ->with('swal_title', 'Berhasil Terhubung ke Zoom!')
                ->with('swal_text', "Sekolah Anda telah terhubung di sesi \"{$zoom_session->title}\". Kamera pengawas dapat dibuka via HP/tablet terpisah.")
                ->with('swal_icon', 'success')
                ->with('open_zoom_url', $zoom_session->zoom_link)
                ->with('open_zoom_title', $zoom_session->title);

        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'CAPACITY_EXCEEDED') {
                return redirect()->route('siswa.zoom-sessions.index')
                    ->with('swal_title', 'Kapasitas Batch Penuh (Maks 500 Sekolah)!')
                    ->with('swal_text', "Maaf, kapasitas batch sesi \"{$zoom_session->title}\" telah mencapai batas maksimum ({$zoom_session->capacity} sekolah). Silakan pilih Batch Zoom alternatif lainnya.")
                    ->with('swal_icon', 'warning');
            }

            return back()->with('error', 'Terjadi kesalahan sistem saat mencoba bergabung. Silakan coba lagi.');
        }
    }

    /**
     * Periodic ping endpoint from student device to track active connection.
     * Requirement 2: mencatat status join/leave/terkendala internet pada sistem.
     */
    public function ping(Request $request, ZoomSession $zoom_session)
    {
        $user = Auth::user();
        DB::table('zoom_participants')
            ->where('zoom_session_id', $zoom_session->id)
            ->where('user_id', $user->id)
            ->update([
                'last_ping_at' => now(),
                'status' => 'connected',
            ]);

        return response()->json(['status' => 'ok', 'time' => now()->format('H:i:s')]);
    }

    /**
     * Report internet/connection trouble from student.
     */
    public function reportTrouble(Request $request, ZoomSession $zoom_session)
    {
        $user = Auth::user();
        $notes = $request->input('notes', 'Terkendala koneksi internet daerah');

        DB::table('zoom_participants')
            ->where('zoom_session_id', $zoom_session->id)
            ->where('user_id', $user->id)
            ->update([
                'status' => 'trouble',
                'notes' => $notes,
            ]);

        $user->update([
            'is_troubled' => true,
            'trouble_notes' => "Laporan kendala pada sesi Zoom {$zoom_session->title}: {$notes}",
        ]);

        return response()->json(['status' => 'trouble_recorded', 'message' => 'Status kendala berhasil dicatat panitia pengawas.']);
    }

    /**
     * Student leaves a Zoom session.
     */
    public function leave(Request $request, ZoomSession $zoom_session)
    {
        $user = Auth::user();

        if ($zoom_session->hasUserJoined($user->id)) {
            DB::table('zoom_participants')
                ->where('zoom_session_id', $zoom_session->id)
                ->where('user_id', $user->id)
                ->update([
                    'status' => 'left',
                    'left_at' => now(),
                ]);

            return back()->with('info', "Sekolah Anda telah mencatatkan keluar dari ruang Zoom \"{$zoom_session->title}\".");
        }

        return back();
    }
}
