<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\ZoomSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Universal cross-page global search for student portal.
     * Searches across Seleksi, Sesi Zoom, Papan Peringkat, Profil, and Dashboard.
     */
    public function search(Request $request): JsonResponse
    {
        $rawQuery = (string) $request->input('q', '');

        // 1. Length constraint: limit to 80 chars
        $trimmed = mb_substr(trim($rawQuery), 0, 80);

        // 2. Security: Strip scripts, style, and HTML tags
        $sanitized = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $trimmed);
        $sanitized = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $sanitized);
        $sanitized = strip_tags($sanitized);
        $sanitized = preg_replace('/<[^>]*>/', '', $sanitized);
        $sanitized = preg_replace('/(javascript|vbscript|data):/i', '', $sanitized);
        $sanitized = preg_replace('/(onload|onerror|onclick|onmouseover|onfocus|onblur)\s*=/i', '', $sanitized);
        $sanitized = preg_replace('/[\x00-\x1F\x7F]/u', '', $sanitized);
        $cleanTerm = trim(preg_replace('/\s+/', ' ', $sanitized));

        if (mb_strlen($cleanTerm) < 2) {
            return response()->json([
                'success' => true,
                'query' => htmlspecialchars($cleanTerm, ENT_QUOTES, 'UTF-8'),
                'total' => 0,
                'results' => [],
            ]);
        }

        $termLower = strtolower($cleanTerm);
        $escapedSqlTerm = addcslashes($cleanTerm, '%_\\');

        // 3. System Navigation Pages
        $systemPages = [
            [
                'title' => 'Dashboard Siswa',
                'description' => 'Beranda utama siswa, capaian seleksi, dan status pengawasan',
                'category' => 'Halaman Menu',
                'type' => 'nav',
                'badge_class' => 'badge-search-nav',
                'icon' => 'fi fi-rr-home',
                'pillar' => 'Menu Utama',
                'meta' => 'Navigasi Sistem',
                'url' => route('siswa.dashboard'),
                'keywords' => ['dashboard', 'beranda', 'home', 'utama', 'statistik', 'ringkasan', 'progres'],
            ],
            [
                'title' => 'Mulai Seleksi (Evaluasi Resmi)',
                'description' => 'Paket ujian seleksi resmi Empat Pilar MPR RI (1x pengerjaan)',
                'category' => 'Halaman Menu',
                'type' => 'nav',
                'badge_class' => 'badge-search-nav',
                'icon' => 'fi fi-rr-document-signed',
                'pillar' => 'Seleksi Resmi',
                'meta' => 'Ujian Resmi',
                'url' => route('siswa.real-materi.index'),
                'keywords' => ['mulai seleksi', 'real materi', 'evaluasi', 'seleksi', 'ujian resmi', 'tes resmi', 'cat', 'soal'],
            ],
            [
                'title' => 'Sesi Zoom Pengawasan',
                'description' => 'Jadwal tatap muka dan ruang pengawasan ujian seleksi virtual',
                'category' => 'Halaman Menu',
                'type' => 'nav',
                'badge_class' => 'badge-search-nav',
                'icon' => 'fi fi-rr-video-camera-alt',
                'pillar' => 'Pengawasan',
                'meta' => 'Ruang Virtual',
                'url' => route('siswa.zoom-sessions.index'),
                'keywords' => ['zoom', 'sesi zoom', 'pengawas', 'virtual', 'webinar', 'ruang zoom', 'meeting'],
            ],
            [
                'title' => 'Papan Peringkat (Leaderboard)',
                'description' => 'Pantau peringkat dan klasemen nilai seleksi tingkat provinsi & nasional',
                'category' => 'Halaman Menu',
                'type' => 'nav',
                'badge_class' => 'badge-search-nav',
                'icon' => 'fi fi-rr-trophy',
                'pillar' => 'Menu Utama',
                'meta' => 'Peringkat & Klasemen',
                'url' => route('siswa.leaderboard'),
                'keywords' => ['peringkat', 'leaderboard', 'ranking', 'papan peringkat', 'juara', 'klasemen', 'skor', 'nilai tertinggi'],
            ],
            [
                'title' => 'Edit Profil & Kata Sandi Akun',
                'description' => 'Kelola biodata, foto profil, asal sekolah, dan ubah kata sandi',
                'category' => 'Halaman Menu',
                'type' => 'nav',
                'badge_class' => 'badge-search-nav',
                'icon' => 'fi fi-rr-user',
                'pillar' => 'Pengaturan Akun',
                'meta' => 'Profil Pengguna',
                'url' => route('siswa.profile.edit'),
                'keywords' => ['profil', 'profile', 'akun', 'edit profil', 'ganti password', 'kata sandi', 'biodata', 'foto'],
            ],
        ];

        // Filter navigation pages matching query
        $matchedNavs = collect($systemPages)->filter(function ($nav) use ($termLower) {
            if (str_contains(strtolower($nav['title']), $termLower) || str_contains(strtolower($nav['description']), $termLower)) {
                return true;
            }
            foreach ($nav['keywords'] as $keyword) {
                if (str_contains($termLower, $keyword) || str_contains($keyword, $termLower)) {
                    return true;
                }
            }
            return false;
        })->map(function ($nav) {
            unset($nav['keywords']);
            return $nav;
        });

        // 4. Content Search: Paket Seleksi (Real Materi)
        $isSearchingReal = str_contains($termLower, 'real') || str_contains($termLower, 'evaluasi') || str_contains($termLower, 'seleksi') || str_contains($termLower, 'resmi') || str_contains($termLower, 'ujian');

        $realQuizzes = Quiz::query()
            ->withCount('questions')
            ->where('type', 'real')
            ->where('is_active', true)
            ->where(function ($q) use ($escapedSqlTerm, $isSearchingReal) {
                if ($isSearchingReal) {
                    $q->whereNotNull('id');
                } else {
                    $q->where('title', 'like', "%{$escapedSqlTerm}%")
                      ->orWhere('description', 'like', "%{$escapedSqlTerm}%")
                      ->orWhere('pillar', 'like', "%{$escapedSqlTerm}%")
                      ->orWhere('package_code', 'like', "%{$escapedSqlTerm}%");
                }
            })
            ->limit(5)
            ->get()
            ->map(function ($quiz) {
                return [
                    'id' => $quiz->id,
                    'category' => 'Paket Seleksi',
                    'type' => 'real-materi',
                    'badge_class' => 'badge-search-real',
                    'icon' => 'fi fi-rr-shield-check',
                    'title' => htmlspecialchars($quiz->title, ENT_QUOTES, 'UTF-8'),
                    'pillar' => htmlspecialchars($quiz->formatted_pillar ?? $quiz->pillar, ENT_QUOTES, 'UTF-8'),
                    'meta' => 'Evaluasi Resmi • ' . ($quiz->questions_count ?? 0) . ' Soal',
                    'url' => route('siswa.real-materi.show', $quiz),
                ];
            });

        // 5. Content Search: Sesi Zoom
        $zoomSessions = ZoomSession::query()
            ->where('is_active', true)
            ->where(function ($q) use ($escapedSqlTerm) {
                $q->where('title', 'like', "%{$escapedSqlTerm}%")
                  ->orWhere('description', 'like', "%{$escapedSqlTerm}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'category' => 'Sesi Zoom Pengawas',
                    'type' => 'zoom',
                    'badge_class' => 'badge-search-nav',
                    'icon' => 'fi fi-rr-video-camera-alt',
                    'title' => htmlspecialchars($session->title, ENT_QUOTES, 'UTF-8'),
                    'pillar' => 'Pengawasan Ujian',
                    'meta' => 'Ruang Zoom Resmi',
                    'url' => route('siswa.zoom-sessions.index'),
                ];
            });

        // Combine all results
        $all = collect()
            ->concat($matchedNavs)
            ->concat($realQuizzes)
            ->concat($zoomSessions);

        return response()->json([
            'success' => true,
            'query' => htmlspecialchars($cleanTerm, ENT_QUOTES, 'UTF-8'),
            'total' => $all->count(),
            'results' => $all->values()->all(),
        ]);
    }
}
