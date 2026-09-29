<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ZoomSession;
use App\Models\ZoomParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ZoomSessionController extends Controller
{
    /**
     * Display a listing of Zoom sessions.
     */
    public function index(Request $request)
    {
        $query = ZoomSession::query()
            ->withCount('participants')
            ->latest('start_time');

        // Filter search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('meeting_id', 'like', "%{$search}%");
            });
        }

        // Filter pillar
        if ($request->filled('pillar')) {
            $query->where('pillar', $request->pillar);
        }

        // Filter status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'full') {
                $query->has('participants', '>=', DB::raw('zoom_sessions.capacity'));
            }
        }

        $sessions = $query->paginate(9)->withQueryString();

        // Summary stats
        $totalSessions = ZoomSession::count();
        $activeSessions = ZoomSession::where('is_active', true)->count();
        $totalParticipants = ZoomParticipant::count();
        $fullSessionsCount = ZoomSession::has('participants', '>=', DB::raw('zoom_sessions.capacity'))->count();

        return view('admin.zoom.index', compact(
            'sessions',
            'totalSessions',
            'activeSessions',
            'totalParticipants',
            'fullSessionsCount'
        ));
    }

    /**
     * Show form to create a new Zoom session.
     */
    public function create()
    {
        return view('admin.zoom.create');
    }

    /**
     * Store newly created Zoom session.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'pillar' => ['required', 'string', 'in:umum,pancasila,uud_1945,nkri,bhinneka_tunggal_ika,twk_kedinasan'],
            'zoom_link' => ['required', 'url', 'max:1000'],
            'meeting_id' => ['nullable', 'string', 'max:100'],
            'passcode' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:5000'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Judul sesi pertemuan wajib diisi.',
            'zoom_link.required' => 'Link URL Zoom wajib diisi.',
            'zoom_link.url' => 'Format URL link Zoom tidak valid (gunakan awalan https://).',
            'capacity.required' => 'Kapasitas link Zoom wajib diisi.',
            'capacity.min' => 'Kapasitas minimal 1 peserta.',
            'start_time.required' => 'Waktu mulai pertemuan wajib diisi.',
            'end_time.after_or_equal' => 'Waktu selesai harus sama atau setelah waktu mulai.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_by'] = Auth::guard('admin')->id();

        $session = ZoomSession::create($validated);

        return redirect()->route('admin.zoom-sessions.show', $session)
            ->with('success', 'Sesi Zoom baru berhasil dibuat dan dijadwalkan!');
    }

    /**
     * Display details of a Zoom session and participant list.
     */
    public function show(ZoomSession $zoom_session, Request $request)
    {
        $zoom_session->loadCount('participants');

        $participantsQuery = $zoom_session->participants()
            ->with(['province', 'regency'])
            ->orderByPivot('joined_at', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $participantsQuery->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.school_name', 'like', "%{$search}%")
                  ->orWhere('users.dapil', 'like', "%{$search}%");
            });
        }

        $participants = $participantsQuery->paginate(15)->withQueryString();

        // Candidates for manual addition (students not yet in this session)
        $existingStudentIds = $zoom_session->participants()->pluck('users.id')->toArray();
        $availableStudents = User::where('role', 'siswa')
            ->whereNotIn('id', $existingStudentIds)
            ->orderBy('name')
            ->take(50)
            ->get();

        return view('admin.zoom.show', compact('zoom_session', 'participants', 'availableStudents'));
    }

    /**
     * Show form to edit a Zoom session.
     */
    public function edit(ZoomSession $zoom_session)
    {
        return view('admin.zoom.edit', compact('zoom_session'));
    }

    /**
     * Update an existing Zoom session.
     */
    public function update(Request $request, ZoomSession $zoom_session)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'pillar' => ['required', 'string', 'in:umum,pancasila,uud_1945,nkri,bhinneka_tunggal_ika,twk_kedinasan'],
            'zoom_link' => ['required', 'url', 'max:1000'],
            'meeting_id' => ['nullable', 'string', 'max:100'],
            'passcode' => ['nullable', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:5000'],
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Judul sesi pertemuan wajib diisi.',
            'zoom_link.required' => 'Link URL Zoom wajib diisi.',
            'zoom_link.url' => 'Format URL link Zoom tidak valid.',
            'capacity.required' => 'Kapasitas link Zoom wajib diisi.',
            'capacity.min' => 'Kapasitas minimal 1 peserta.',
            'start_time.required' => 'Waktu mulai pertemuan wajib diisi.',
            'end_time.after_or_equal' => 'Waktu selesai harus sama atau setelah waktu mulai.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $zoom_session->update($validated);

        return redirect()->route('admin.zoom-sessions.show', $zoom_session)
            ->with('success', 'Data sesi Zoom dan kapasitas berhasil diperbarui!');
    }

    /**
     * Delete a Zoom session.
     */
    public function destroy(ZoomSession $zoom_session)
    {
        $title = $zoom_session->title;
        $zoom_session->delete();

        return redirect()->route('admin.zoom-sessions.index')
            ->with('success', "Sesi Zoom \"{$title}\" berhasil dihapus.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(ZoomSession $zoom_session)
    {
        $zoom_session->update([
            'is_active' => !$zoom_session->is_active,
        ]);

        $status = $zoom_session->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Sesi Zoom berhasil {$status}.");
    }

    /**
     * Admin manually adds a participant student to this session.
     */
    public function addParticipant(Request $request, ZoomSession $zoom_session)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'user_id.required' => 'Pilih siswa yang akan ditambahkan.',
            'user_id.exists' => 'Data siswa tidak ditemukan.',
        ]);

        $currentCount = $zoom_session->participants()->count();
        if ($currentCount >= $zoom_session->capacity) {
            return back()->with('error', "Kapasitas ruangan sudah penuh ({$zoom_session->capacity} peserta). Silakan naikkan kapasitas terlebih dahulu jika ingin menambahkan peserta.");
        }

        $userId = $request->user_id;

        if ($zoom_session->hasUserJoined($userId)) {
            return back()->with('info', 'Siswa tersebut sudah terdaftar dalam sesi Zoom ini.');
        }

        $zoom_session->participants()->attach($userId, [
            'joined_at' => now(),
            'notes' => $request->notes ?? 'Ditambahkan manual oleh Administrator',
        ]);

        return back()->with('success', 'Siswa berhasil ditambahkan ke dalam daftar peserta sesi Zoom.');
    }

    /**
     * Admin removes a participant student from this session.
     */
    public function removeParticipant(ZoomSession $zoom_session, User $user)
    {
        $zoom_session->participants()->detach($user->id);

        return back()->with('success', "Peserta {$user->name} berhasil dihapus dari sesi Zoom. 1 kursi kuota telah kembali tersedia.");
    }

    /**
     * Export attendees list to CSV.
     */
    public function exportParticipants(ZoomSession $zoom_session)
    {
        $participants = $zoom_session->participants()
            ->with(['province', 'regency'])
            ->orderByPivot('joined_at', 'asc')
            ->get();

        $fileName = 'peserta_zoom_' . Str::slug($zoom_session->title) . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        return response()->stream(function () use ($zoom_session, $participants) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            // Header Info
            fputcsv($handle, ['DAFTAR PRESENSI PESERTA SESI ZOOM']);
            fputcsv($handle, ['Topik / Judul', $zoom_session->title]);
            fputcsv($handle, ['Kategori Pilar', $zoom_session->formatted_pillar]);
            fputcsv($handle, ['Jadwal Mulai', $zoom_session->start_time->format('d/m/Y H:i') . ' WIB']);
            fputcsv($handle, ['Kapasitas', $zoom_session->capacity . ' Peserta']);
            fputcsv($handle, ['Total Bergabung', $participants->count() . ' Peserta']);
            fputcsv($handle, ['Link Zoom', $zoom_session->zoom_link]);
            fputcsv($handle, []);

            // Table Columns
            fputcsv($handle, [
                'No',
                'Nama Lengkap',
                'Email Siswa',
                'Asal Sekolah',
                'Kelas',
                'Daerah Pemilihan (Dapil)',
                'Kabupaten / Kota',
                'Waktu Bergabung',
                'Catatan / Status'
            ]);

            foreach ($participants as $index => $student) {
                fputcsv($handle, [
                    $index + 1,
                    $student->name,
                    $student->email,
                    $student->school_name ?? '-',
                    $student->class_name ?? '-',
                    $student->dapil ?? '-',
                    $student->regency->name ?? '-',
                    $student->pivot->joined_at ? \Carbon\Carbon::parse($student->pivot->joined_at)->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') : '-',
                    $student->pivot->notes ?? 'Hadir / Terdaftar'
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
