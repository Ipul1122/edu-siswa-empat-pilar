<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Quiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Universal cross-page global search for student portal.
     * Features:
     * - Strict script & XSS injection sanitization
     * - Searches across Navigation menus (Dashboard, Peringkat, Profil, etc.)
     * - Searches across Content (Materi Bacaan, Video, Kuis Latihan, Real Materi)
     */
    public function search(Request $request): JsonResponse
    {
        $rawQuery = (string) $request->input('q', '');

        // 1. Length constraint: limit to 80 chars to prevent memory/buffer abuse
        $trimmed = mb_substr(trim($rawQuery), 0, 80);

        // 2. Security: Strip entire <script>...</script> and <style>...</style> blocks (including inner payload)
        $sanitized = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $trimmed);
        $sanitized = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $sanitized);

        // 3. Strip remaining HTML tags & dangerous protocols/events (XSS Protection)
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

        // 3. System Navigation Pages (Dashboard, Peringkat, Profil, dll)
        $systemPages = [
            [
                'title' => 'Dashboard Siswa',
                'description' => 'Beranda utama siswa, statistik progres belajar, dan aktivitas terbaru',
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
                'title' => 'Papan Peringkat (Leaderboard)',
                'description' => 'Pantau peringkat dan klasemen nilai evaluasi tingkat provinsi & nasional',
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
                'title' => 'Latihan Kuis & Try Out',
                'description' => 'Daftar lengkap paket latihan kuis simulasi PPKn dan TWK Kedinasan',
                'category' => 'Halaman Menu',
                'type' => 'nav',
                'badge_class' => 'badge-search-nav',
                'icon' => 'fi fi-rr-edit',
                'pillar' => 'Kuis Simulasi',
                'meta' => 'Katalog Kuis',
                'url' => route('siswa.quizzes.index'),
                'keywords' => ['kuis', 'latihan kuis', 'try out', 'simulasi', 'paket kuis', 'soal latihan'],
            ],
            [
                'title' => 'Real Materi Evaluasi Resmi',
                'description' => 'Halaman seleksi evaluasi resmi Empat Pilar MPR RI (1x pengerjaan)',
                'category' => 'Halaman Menu',
                'type' => 'nav',
                'badge_class' => 'badge-search-nav',
                'icon' => 'fi fi-rr-document-signed',
                'pillar' => 'Seleksi Resmi',
                'meta' => 'Ujian Resmi',
                'url' => route('siswa.real-materi.index'),
                'keywords' => ['real materi', 'evaluasi', 'seleksi', 'ujian resmi', 'tes resmi', 'cat'],
            ],
            [
                'title' => 'Materi Belajar Interaktif',
                'description' => 'Daftar modul bacaan teks 4 Pilar Kebangsaan',
                'category' => 'Halaman Menu',
                'type' => 'nav',
                'badge_class' => 'badge-search-nav',
                'icon' => 'fi fi-rr-book-alt',
                'pillar' => 'Materi Bacaan',
                'meta' => 'Daftar Materi',
                'url' => route('siswa.materials.index'),
                'keywords' => ['materi', 'bacaan', 'modul', 'artikel', 'buku', 'teks materi'],
            ],
            [
                'title' => 'Video Pembelajaran Kebangsaan',
                'description' => 'Koleksi video materi visual interaktif Empat Pilar MPR RI',
                'category' => 'Halaman Menu',
                'type' => 'nav',
                'badge_class' => 'badge-search-nav',
                'icon' => 'fi fi-rr-play-alt',
                'pillar' => 'Video Pembelajaran',
                'meta' => 'Daftar Video',
                'url' => route('siswa.videos.index'),
                'keywords' => ['video', 'tonton', 'rekaman', 'visual', 'youtube', 'daftar video'],
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

        // 4. Intent detection for content
        $isSearchingQuiz = str_contains($termLower, 'kuis') || str_contains($termLower, 'quiz') || str_contains($termLower, 'try out') || str_contains($termLower, 'simulasi');
        $isSearchingReal = str_contains($termLower, 'real') || str_contains($termLower, 'evaluasi') || str_contains($termLower, 'seleksi') || str_contains($termLower, 'resmi');
        $isSearchingVideo = str_contains($termLower, 'video') || str_contains($termLower, 'tonton') || str_contains($termLower, 'youtube');
        $isSearchingMaterial = str_contains($termLower, 'baca') || str_contains($termLower, 'artikel') || ($termLower === 'materi');

        // 5. Kuis Latihan (Practice Quizzes)
        $quizzes = Quiz::query()
            ->withCount('questions')
            ->where('type', 'practice')
            ->where('is_active', true)
            ->where(function ($q) use ($escapedSqlTerm, $isSearchingQuiz) {
                if ($isSearchingQuiz) {
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
                    'category' => 'Kuis Latihan',
                    'type' => 'quiz',
                    'badge_class' => 'badge-search-quiz',
                    'icon' => 'fi fi-rr-interrogation',
                    'title' => htmlspecialchars($quiz->title, ENT_QUOTES, 'UTF-8'),
                    'pillar' => htmlspecialchars($quiz->formatted_pillar ?? $quiz->pillar, ENT_QUOTES, 'UTF-8'),
                    'meta' => ($quiz->questions_count ?? 0) . ' Soal • ' . $quiz->duration_minutes . ' Menit',
                    'url' => route('siswa.quizzes.show', $quiz),
                ];
            });

        // 6. Real Materi Evaluasi Resmi
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
                    'category' => 'Real Materi Evaluasi',
                    'type' => 'real-materi',
                    'badge_class' => 'badge-search-real',
                    'icon' => 'fi fi-rr-shield-check',
                    'title' => htmlspecialchars($quiz->title, ENT_QUOTES, 'UTF-8'),
                    'pillar' => htmlspecialchars($quiz->formatted_pillar ?? $quiz->pillar, ENT_QUOTES, 'UTF-8'),
                    'meta' => 'Evaluasi Resmi • ' . ($quiz->questions_count ?? 0) . ' Soal',
                    'url' => route('siswa.real-materi.show', $quiz),
                ];
            });

        // 7. Materi Bacaan (Reading Materials)
        $materials = Material::query()
            ->where(function ($q) {
                $q->where('type', 'text')->orWhereNull('type');
            })
            ->where(function ($q) use ($escapedSqlTerm, $isSearchingMaterial) {
                if ($isSearchingMaterial) {
                    $q->whereNotNull('id');
                } else {
                    $q->where('title', 'like', "%{$escapedSqlTerm}%")
                      ->orWhere('content', 'like', "%{$escapedSqlTerm}%")
                      ->orWhere('pillar', 'like', "%{$escapedSqlTerm}%");
                }
            })
            ->limit(5)
            ->get()
            ->map(function ($material) {
                return [
                    'id' => $material->id,
                    'category' => 'Materi Bacaan',
                    'type' => 'material',
                    'badge_class' => 'badge-search-material',
                    'icon' => 'fi fi-rr-book-alt',
                    'title' => htmlspecialchars($material->title, ENT_QUOTES, 'UTF-8'),
                    'pillar' => htmlspecialchars($material->formatted_pillar ?? $material->pillar, ENT_QUOTES, 'UTF-8'),
                    'meta' => ($material->read_time ?? 5) . ' Menit Baca',
                    'url' => route('siswa.materials.show', $material),
                ];
            });

        // 8. Video Pembelajaran (Video Materials)
        $videos = Material::query()
            ->where('type', 'video')
            ->where(function ($q) use ($escapedSqlTerm, $isSearchingVideo) {
                if ($isSearchingVideo) {
                    $q->whereNotNull('id');
                } else {
                    $q->where('title', 'like', "%{$escapedSqlTerm}%")
                      ->orWhere('content', 'like', "%{$escapedSqlTerm}%")
                      ->orWhere('pillar', 'like', "%{$escapedSqlTerm}%");
                }
            })
            ->limit(5)
            ->get()
            ->map(function ($video) {
                return [
                    'id' => $video->id,
                    'category' => 'Video Materi',
                    'type' => 'video',
                    'badge_class' => 'badge-search-video',
                    'icon' => 'fi fi-rr-play-alt',
                    'title' => htmlspecialchars($video->title, ENT_QUOTES, 'UTF-8'),
                    'pillar' => htmlspecialchars($video->formatted_pillar ?? $video->pillar, ENT_QUOTES, 'UTF-8'),
                    'meta' => 'Video Pembelajaran Interaktif',
                    'url' => route('siswa.videos.show', $video),
                ];
            });

        // Combine all results: Navigation first if matched, then content
        $all = collect()
            ->concat($matchedNavs)
            ->concat($realQuizzes)
            ->concat($quizzes)
            ->concat($materials)
            ->concat($videos);

        return response()->json([
            'success' => true,
            'query' => htmlspecialchars($cleanTerm, ENT_QUOTES, 'UTF-8'),
            'total' => $all->count(),
            'results' => $all->values()->all(),
        ]);
    }
}
