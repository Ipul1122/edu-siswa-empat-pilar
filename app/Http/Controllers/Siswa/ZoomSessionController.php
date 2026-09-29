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

        try {
            DB::transaction(function () use ($zoom_session, $user) {
                // Lock session row to ensure strict capacity checks without race conditions
                $lockedSession = ZoomSession::where('id', $zoom_session->id)->lockForUpdate()->first();
                $currentCount = $lockedSession->participants()->count();

                if ($currentCount >= $lockedSession->capacity) {
                    throw new \RuntimeException('CAPACITY_EXCEEDED');
                }

                $lockedSession->participants()->syncWithoutDetaching([
                    $user->id => [
                        'joined_at' => now(),
                        'notes' => 'Peserta Mandiri Siswa',
                    ]
                ]);
            });

            return redirect()->route('siswa.zoom-sessions.index')
                ->with('swal_title', 'Berhasil Bergabung!')
                ->with('swal_text', "Anda telah terdaftar di sesi \"{$zoom_session->title}\". Kuota berhasil diamankan. Klik tombol 'Buka Ruang Zoom' untuk masuk ke aplikasi Zoom.")
                ->with('swal_icon', 'success')
                ->with('open_zoom_url', $zoom_session->zoom_link)
                ->with('open_zoom_title', $zoom_session->title);

        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'CAPACITY_EXCEEDED') {
                return redirect()->route('siswa.zoom-sessions.index')
                    ->with('swal_title', 'Kuota Sesi Ini Sudah Penuh!')
                    ->with('swal_text', "Maaf, kapasitas sesi \"{$zoom_session->title}\" telah mencapai batas maksimum ({$zoom_session->capacity} peserta). Silakan pilih dan gabung ke link Zoom alternatif lainnya yang masih tersedia di bawah.")
                    ->with('swal_icon', 'warning');
            }

            return back()->with('error', 'Terjadi kesalahan sistem saat mencoba bergabung. Silakan coba lagi.');
        }
    }

    /**
     * Student cancels join from a Zoom session (frees up capacity for others).
     */
    public function leave(Request $request, ZoomSession $zoom_session)
    {
        $user = Auth::user();

        if ($zoom_session->hasUserJoined($user->id)) {
            $zoom_session->participants()->detach($user->id);

            return back()->with('info', "Anda telah membatalkan keikutsertaan di sesi \"{$zoom_session->title}\". Kuota Anda telah dilepaskan dan Anda dapat bergabung ke sesi Zoom lainnya.");
        }

        return back();
    }
}
