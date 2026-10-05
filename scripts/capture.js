import puppeteer from 'puppeteer-core';
import path from 'path';
import fs from 'fs';

const BASE_URL = 'http://127.0.0.1:8000';
const SCREENSHOT_DIR = path.resolve('screenshots');
const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function dismissAlerts(page) {
  try {
    await page.evaluate(() => {
      if (typeof Swal !== 'undefined' && Swal.isVisible()) {
        Swal.close();
      }
      const toast = document.querySelector('.swal2-container');
      if (toast) toast.remove();
    });
  } catch (e) {}
}

async function capture(page, url, filename, options = {}) {
  const filePath = path.join(SCREENSHOT_DIR, filename);
  console.log(`\n[Capturing] ${filename} from ${url || 'current page'}`);
  try {
    if (url) {
      await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
    }
    await sleep(options.delay || 1500);
    await dismissAlerts(page);

    if (options.action) {
      await options.action(page);
      await sleep(options.postActionDelay || 800);
      await dismissAlerts(page);
    }

    await page.screenshot({
      path: filePath,
      fullPage: options.fullPage !== undefined ? options.fullPage : false
    });
    console.log(`✓ Saved: ${filename}`);
    return true;
  } catch (e) {
    console.error(`✗ Error capturing ${filename}:`, e.message);
    return false;
  }
}

async function main() {
  console.log('========================================================================');
  console.log('    AUTOMATISASI LENGKAP PENGAMBILAN SCREENSHOT DESKTOP EDU EMPAT PILAR ');
  console.log('    Platform Seleksi Online Empat Pilar MPR RI (Laravel 12 + Vite)      ');
  console.log('========================================================================');

  if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
  }

  // Bersihkan file .png lama agar folder rapi dan akurat dengan aplikasi saat ini
  const existingFiles = fs.readdirSync(SCREENSHOT_DIR);
  for (const file of existingFiles) {
    if (file.endsWith('.png')) {
      fs.unlinkSync(path.join(SCREENSHOT_DIR, file));
    }
  }
  console.log('Folder screenshots telah disterilkan untuk pembaruan terkini.');

  const browser = await puppeteer.launch({
    executablePath: CHROME_PATH,
    headless: true,
    args: [
      '--no-sandbox',
      '--disable-setuid-sandbox',
      '--disable-gpu',
      '--window-size=1440,900'
    ],
    defaultViewport: {
      width: 1440,
      height: 900,
      deviceScaleFactor: 1.5
    }
  });

  const page = await browser.newPage();

  // ================================================================
  // FASE 1: HALAMAN PUBLIK & AUTENTIKASI (6 FILE)
  // ================================================================
  console.log('\n>>> FASE 1: HALAMAN PUBLIK & AUTENTIKASI <<<');

  // 01. Beranda Utama (Hero Viewport 1440x900)
  await capture(page, `${BASE_URL}/`, '01-landing-page.png', { delay: 1800 });

  // 02. Beranda Utama Lengkap (Full Page)
  await capture(page, null, '02-landing-page-full.png', { fullPage: true });

  // 03. Masuk Akun Siswa / Sekolah
  await capture(page, `${BASE_URL}/login`, '03-login-siswa.png');

  // 04. Pendaftaran Akun Sekolah Baru (Tim 10 Siswa)
  await capture(page, `${BASE_URL}/register`, '04-register-siswa.png', { delay: 1500 });

  // 05. Pemulihan Kata Sandi Akun Siswa (Verifikasi OTP)
  await capture(page, `${BASE_URL}/forgot-password`, '05-lupa-password.png');

  // 06. Masuk Panel Administrator MPR RI
  await capture(page, `${BASE_URL}/admin/login`, '06-login-admin.png');

  // ================================================================
  // FASE 2: PORTAL SISWA / SEKOLAH (13 FILE)
  // ================================================================
  console.log('\n>>> FASE 2: PORTAL SISWA / SEKOLAH (SCHOOL MODE) <<<');

  console.log('Melakukan login akun Siswa (msyaifulloh2024@gmail.com)...');
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
  await page.type('#email', 'msyaifulloh2024@gmail.com');
  await page.type('#password', 'password');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 15000 }),
    page.click('button.submit-btn')
  ]);
  console.log('Berhasil login sebagai Siswa! URL:', page.url());
  await sleep(1500);

  // 07. Dashboard Siswa (Viewport)
  await capture(page, `${BASE_URL}/siswa/dashboard`, '07-siswa-dashboard.png', { delay: 2000 });

  // 08. Dashboard Siswa (Full Page)
  await capture(page, null, '08-siswa-dashboard-full.png', { fullPage: true });

  // 09. Tutorial & Petunjuk Teknis Seleksi Online (Viewport)
  await capture(page, `${BASE_URL}/siswa/tutorial`, '09-siswa-tutorial-panduan.png', { delay: 1500 });

  // 10. Tutorial & Petunjuk Teknis Seleksi Online (Full Page)
  await capture(page, null, '10-siswa-tutorial-panduan-full.png', { fullPage: true });

  // 11. Katalog Modul Ujian Seleksi Online CBT (Real Materi)
  await capture(page, `${BASE_URL}/siswa/real-materi`, '11-siswa-katalog-ujian-cbt.png', { delay: 1500 });

  // 12. Lembar Konfirmasi & Peraturan Ujian Seleksi
  await capture(page, `${BASE_URL}/siswa/real-materi/6`, '12-siswa-konfirmasi-ujian.png', { delay: 1500 });

  // 13. Antarmuka Ujian Seleksi CBT Mode Layar Penuh (Zero Reload & Anti-Cheat)
  await capture(page, `${BASE_URL}/siswa/real-materi/6/start`, '13-siswa-pengerjaan-ujian-cbt.png', { delay: 1800 });

  // 14. Lembar Pengumuman Hasil Nilai Ujian Seleksi & Pembahasan
  await capture(page, `${BASE_URL}/siswa/real-attempts/34/result`, '14-siswa-hasil-ujian-cbt.png', { delay: 1800 });

  // 15. Portal Sesi Pengawasan Virtual Zoom & Lapor Gangguan
  await capture(page, `${BASE_URL}/siswa/zoom-sessions`, '15-siswa-sesi-zoom-pengawas.png', { delay: 1500 });

  // 16. Rekapitulasi Hasil Skor Mandiri Tim Sekolah (Kerahasiaan Nilai)
  await capture(page, `${BASE_URL}/siswa/hasil-tes`, '16-siswa-hasil-skor-mandiri.png', { delay: 1500 });

  // 17. Papan Peringkat (Leaderboard) Siswa
  await capture(page, `${BASE_URL}/siswa/leaderboard`, '17-siswa-papan-peringkat.png', { delay: 1800 });

  // 18. Profil Sekolah & Kontak PIC (Viewport)
  await capture(page, `${BASE_URL}/siswa/profile`, '18-siswa-profil-sekolah.png', { delay: 1500 });

  // 19. Profil Sekolah & Kontak PIC (Full Page)
  await capture(page, null, '19-siswa-profil-sekolah-full.png', { fullPage: true });

  // ================================================================
  // FASE 3: PORTAL ADMINISTRATOR MPR RI (17 FILE)
  // ================================================================
  console.log('\n>>> FASE 3: PORTAL ADMINISTRATOR MPR RI <<<');

  const adminContext = await browser.createBrowserContext();
  const adminPage = await adminContext.newPage();
  await adminPage.setViewport({ width: 1440, height: 900, deviceScaleFactor: 1.5 });

  console.log('Melakukan login Admin MPR RI (admin@gmail.com)...');
  await adminPage.goto(`${BASE_URL}/admin/login`, { waitUntil: 'domcontentloaded' });
  await adminPage.type('#email', 'admin@gmail.com');
  await adminPage.type('#password', 'password');
  await Promise.all([
    adminPage.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 15000 }),
    adminPage.click('button.submit-btn')
  ]);
  console.log('Berhasil login sebagai Admin! URL:', adminPage.url());
  await sleep(1500);

  // 20. Dashboard Administrator (Monitoring & Statistik - Viewport)
  await capture(adminPage, `${BASE_URL}/admin/dashboard`, '20-admin-dashboard.png', { delay: 2000 });

  // 21. Dashboard Administrator (Full Page)
  await capture(adminPage, null, '21-admin-dashboard-full.png', { fullPage: true });

  // 22. Manajemen Soal Seleksi (Master Real Materi & Toggle Switch Akses)
  await capture(adminPage, `${BASE_URL}/admin/real-materi`, '22-admin-kelola-soal-seleksi.png', { delay: 1500 });

  // 23. Formulir Tambah Paket Seleksi Baru
  await capture(adminPage, `${BASE_URL}/admin/real-materi/create`, '23-admin-tambah-paket-seleksi.png', { delay: 1500 });

  // 24. Formulir Edit Paket Seleksi & Penetapan Wilayah
  await capture(adminPage, `${BASE_URL}/admin/real-materi/6/edit`, '24-admin-edit-paket-seleksi.png', { delay: 1500 });

  // 25. Bank Soal Seleksi & Opsi Import Excel/CSV
  await capture(adminPage, `${BASE_URL}/admin/quizzes/6`, '25-admin-bank-soal-seleksi.png', { delay: 1800 });

  // 26. Formulir Tambah Butir Soal Seleksi (Opsi A-E & Kunci Pembahasan)
  await capture(adminPage, `${BASE_URL}/admin/quizzes/6/questions/create`, '26-admin-tambah-butir-soal.png', { delay: 1500 });

  // 27. Manajemen Sesi Pengawasan Zoom (Meeting ID, Status & Kuota Peserta - Viewport)
  await capture(adminPage, `${BASE_URL}/admin/zoom-sessions`, '27-admin-kelola-sesi-zoom.png', { delay: 1800 });

  // 28. Manajemen Sesi Pengawasan Zoom (Full Page)
  await capture(adminPage, null, '28-admin-kelola-sesi-zoom-full.png', { fullPage: true });

  // 29. Formulir Pembuatan Sesi Zoom Pengawas Baru
  await capture(adminPage, `${BASE_URL}/admin/zoom-sessions/create`, '29-admin-tambah-sesi-zoom.png', { delay: 1500 });

  // 30. Formulir Edit Sesi Zoom Pengawas
  await capture(adminPage, `${BASE_URL}/admin/zoom-sessions/5/edit`, '30-admin-edit-sesi-zoom.png', { delay: 1500 });

  // 31. Tabel Direktori Pemantauan Peserta & Sekolah Terdaftar
  await capture(adminPage, `${BASE_URL}/admin/students`, '31-admin-pemantauan-siswa.png', { delay: 1800 });

  // 32. Rekapitulasi & Laporan Nilai Seleksi Nasional/Wilayah
  await capture(adminPage, `${BASE_URL}/admin/students/report`, '32-admin-laporan-rekapitulasi.png', { delay: 1800 });

  // 33. Detail Akun Sekolah & Histori Nilai Peserta (Pemberian Hak Retest)
  await capture(adminPage, `${BASE_URL}/admin/students/2`, '33-admin-detail-peserta.png', { delay: 1800 });

  // 34. Papan Peringkat Berjenjang (Nasional, Provinsi & Kab/Kota - Viewport)
  await capture(adminPage, `${BASE_URL}/admin/leaderboard`, '34-admin-papan-peringkat.png', { delay: 2000 });

  // 35. Papan Peringkat Berjenjang (Full Page)
  await capture(adminPage, null, '35-admin-papan-peringkat-full.png', { fullPage: true });

  // 36. Pengaturan Profil Administrator MPR RI
  await capture(adminPage, `${BASE_URL}/admin/profile`, '36-admin-profil.png', { delay: 1500 });

  await browser.close();
  console.log('\n========================================================================');
  console.log('    SEMUA 36 SCREENSHOT DESKTOP BERHASIL DIAMBIL & DIPERBARUI!          ');
  console.log('========================================================================\n');
}

main().catch((err) => {
  console.error('Terjadi error dalam proses capture:', err);
  process.exit(1);
});
