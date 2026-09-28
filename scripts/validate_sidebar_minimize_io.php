<?php
/**
 * VALIDASI I/O: FITUR SIDEBAR MINIMIZE (ADMIN & SISWA)
 */

$passed = 0;
$failed = 0;

function assertCheck($condition, $description) {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] $description\n";
        $passed++;
    } else {
        echo "  [FAIL] $description\n";
        $failed++;
    }
}

echo "=== VALIDASI I/O: FITUR SIDEBAR MINIMIZE (ADMIN & SISWA) ===\n\n";

// 1. Validasi Layout Siswa (resources/views/layouts/siswa.blade.php)
echo "1. Validasi Layout Siswa (layouts/siswa.blade.php)\n";
$siswaLayout = file_get_contents(__DIR__ . '/../resources/views/layouts/siswa.blade.php');

assertCheck(
    str_contains($siswaLayout, "localStorage.getItem('sidebar_minimized')"),
    "Script anti-FOUC sidebar_minimized terpasang di <head> layout Siswa"
);

assertCheck(
    str_contains($siswaLayout, 'id="sidebar-collapse-btn"'),
    "Tombol collapse sidebar (#sidebar-collapse-btn) terpasang di sidebar header Siswa"
);

assertCheck(
    str_contains($siswaLayout, 'data-title="Dashboard"') && str_contains($siswaLayout, 'data-title="Materi Belajar"'),
    "Menu navigasi Siswa dilengkapi atribut 'data-title' untuk floating tooltip"
);

assertCheck(
    str_contains($siswaLayout, '<span class="logout-label">Keluar</span>'),
    "Label logout Siswa dibungkus class '.logout-label' agar bisa disembunyikan saat minimized"
);

assertCheck(
    str_contains($siswaLayout, 'id="menu-toggle"'),
    "Tombol topbar #menu-toggle tersedia di navbar Siswa"
);
echo "\n";

// 2. Validasi Layout Admin (resources/views/layouts/admin.blade.php)
echo "2. Validasi Layout Admin (layouts/admin.blade.php)\n";
$adminLayout = file_get_contents(__DIR__ . '/../resources/views/layouts/admin.blade.php');

assertCheck(
    str_contains($adminLayout, "localStorage.getItem('sidebar_minimized')"),
    "Script anti-FOUC sidebar_minimized terpasang di <head> layout Admin"
);

assertCheck(
    str_contains($adminLayout, 'id="sidebar-collapse-btn"'),
    "Tombol collapse sidebar (#sidebar-collapse-btn) terpasang di sidebar header Admin"
);

assertCheck(
    str_contains($adminLayout, 'data-title="Dashboard"') && str_contains($adminLayout, 'data-title="Pemantauan Siswa"'),
    "Menu navigasi Admin dilengkapi atribut 'data-title' untuk floating tooltip"
);

assertCheck(
    str_contains($adminLayout, '<span class="logout-label">Keluar (Logout)</span>'),
    "Label logout Admin dibungkus class '.logout-label' agar bisa disembunyikan saat minimized"
);

assertCheck(
    str_contains($adminLayout, 'id="menu-toggle"'),
    "Tombol topbar #menu-toggle tersedia di navbar Admin"
);
echo "\n";

// 3. Validasi Kontroler JavaScript (resources/js/app.js)
echo "3. Validasi Logika JavaScript (resources/js/app.js)\n";
$appJs = file_get_contents(__DIR__ . '/../resources/js/app.js');

assertCheck(
    str_contains($appJs, 'toggleSidebarMinimize'),
    "app.js memuat fungsi 'toggleSidebarMinimize'"
);

assertCheck(
    str_contains($appJs, "localStorage.setItem('sidebar_minimized'"),
    "app.js menyimpan preferensi minimized pengguna ke localStorage"
);

assertCheck(
    str_contains($appJs, "localStorage.getItem('sidebar_minimized') === 'true'"),
    "app.js membaca preferensi minimized dari localStorage saat DOM dimuat"
);

assertCheck(
    str_contains($appJs, 'sidebarCollapseBtn'),
    "app.js mengikat event listener pada tombol #sidebar-collapse-btn"
);

assertCheck(
    str_contains($appJs, 'menuToggle'),
    "app.js mengikat event listener pada tombol topbar #menu-toggle"
);

assertCheck(
    str_contains($appJs, 'e.ctrlKey && e.key.toLowerCase() === \'b\''),
    "app.js menyediakan shortcut keyboard Ctrl+B untuk meminimize sidebar desktop"
);
echo "\n";

// 4. Validasi Styling CSS (resources/css/app.css)
echo "4. Validasi Styling CSS (resources/css/app.css)\n";
$appCss = file_get_contents(__DIR__ . '/../resources/css/app.css');

assertCheck(
    str_contains($appCss, 'body.sidebar-minimized .app-sidebar') && str_contains($appCss, 'width: 80px'),
    "app.css mendefinisikan lebar sidebar 80px saat state 'sidebar-minimized'"
);

assertCheck(
    str_contains($appCss, 'body.sidebar-minimized .app-content') && str_contains($appCss, 'margin-left: 80px'),
    "app.css menyesuaikan margin-left konten menjadi 80px saat state 'sidebar-minimized'"
);

assertCheck(
    str_contains($appCss, 'transition: width 0.3s') && str_contains($appCss, 'transition: margin-left 0.3s'),
    "app.css mengimplementasikan transisi animasi halus (0.3s) untuk width & margin-left"
);

assertCheck(
    str_contains($appCss, 'body.sidebar-minimized .sidebar-menu-item a::after') && str_contains($appCss, 'content: attr(data-title)'),
    "app.css mengimplementasikan floating tooltip cerdas via data-title pada hover item minimized"
);

assertCheck(
    str_contains($appCss, '.sidebar-collapse-btn') && str_contains($appCss, 'top: 50%') && str_contains($appCss, 'right: -22px'),
    "app.css memposisikan tombol .sidebar-collapse-btn di tengah vertikal garis tepi sidebar (top: 50%, right: -22px)"
);

assertCheck(
    str_contains($appCss, 'border-radius: 0 10px 10px 0') && str_contains($appCss, 'border-left: none'),
    "app.css membentuk tab yang menyatu mulus dengan bodi sidebar (border-radius: 0 10px 10px 0, border-left: none)"
);

assertCheck(
    str_contains($appCss, 'body.sidebar-minimized .sidebar-collapse-btn i') && str_contains($appCss, 'rotate(180deg)'),
    "app.css memutar ikon panah 180 derajat saat sidebar dalam mode minimized"
);

assertCheck(
    str_contains($appCss, '.menu-toggle') && str_contains($appCss, 'display: inline-flex'),
    "app.css menampilkan tombol .menu-toggle di topbar untuk desktop"
);
echo "\n";

echo "=======================================================\n";
echo "HASIL AKHIR: $passed BERHASIL, $failed GAGAL\n";
echo "=======================================================\n";

exit($failed > 0 ? 1 : 0);
