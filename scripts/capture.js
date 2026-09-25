import puppeteer from 'puppeteer-core';
import path from 'path';
import fs from 'fs';

const BASE_URL = 'http://127.0.0.1:8000';
const SCREENSHOT_DIR = path.resolve('screenshots');
const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

async function capture(page, url, filename, options = {}) {
  const filePath = path.join(SCREENSHOT_DIR, filename);
  console.log(`\n[Capturing] ${filename} from ${url || 'current page'}`);
  try {
    if (url) {
      await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
    }
    await sleep(options.delay || 1200);

    if (options.action) {
      await options.action(page);
      await sleep(options.postActionDelay || 800);
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
  console.log('===============================================================');
  console.log('    AUTOMATISASI LENGKAP PENGAMBILAN SCREENSHOT EDU EMPAT PILAR ');
  console.log('===============================================================');

  if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
  }

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
  // FASE 1: HALAMAN PUBLIK & AUTENTIKASI
  // ================================================================
  console.log('\n>>> FASE 1: HALAMAN PUBLIK & AUTENTIKASI <<<');

  // 01. Beranda Landing Page (Viewport)
  await capture(page, `${BASE_URL}/`, '01-landing-page.png', { delay: 1800 });

  // 02. Beranda Landing Page (Full Page)
  await capture(page, null, '02-landing-page-full.png', { fullPage: true });

  // 03. Login Siswa
  await capture(page, `${BASE_URL}/login`, '03-login-siswa.png');

  // 04. Register Siswa
  await capture(page, `${BASE_URL}/register`, '04-register-siswa.png');

  // 05. Lupa Password Siswa
  await capture(page, `${BASE_URL}/forgot-password`, '05-lupa-password.png');

  // 06. Login Administrator
  await capture(page, `${BASE_URL}/admin/login`, '06-login-admin.png');

  // ================================================================
  // FASE 2: PANEL SISWA (PORTAL PESERTA DIDIK)
  // ================================================================
  console.log('\n>>> FASE 2: PANEL SISWA (PORTAL PESERTA DIDIK) <<<');

  console.log('Melakukan login Siswa (msyaifulloh2024@gmail.com)...');
  await page.goto(`${BASE_URL}/login`, { waitUntil: 'domcontentloaded' });
  await page.type('#email', 'msyaifulloh2024@gmail.com');
  await page.type('#password', 'password');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 15000 }),
    page.click('button.submit-btn')
  ]);
  console.log('Berhasil login sebagai Siswa! URL:', page.url());

  // 07. Dashboard Siswa (Viewport)
  await capture(page, `${BASE_URL}/siswa/dashboard`, '07-siswa-dashboard.png', { delay: 1800 });

  // 08. Dashboard Siswa (Full Page)
  await capture(page, null, '08-siswa-dashboard-full.png', { fullPage: true });

  // 09. Katalog Materi Bacaan
  await capture(page, `${BASE_URL}/siswa/materials`, '09-siswa-materi-bacaan.png');

  // 10. Detail Membaca Materi Edukasi (Viewport)
  await capture(page, `${BASE_URL}/siswa/materials/1`, '10-siswa-detail-materi.png', { delay: 1500 });

  // 11. Detail Membaca Materi Edukasi (Full Page)
  await capture(page, null, '11-siswa-detail-materi-full.png', { fullPage: true });

  // 12. Katalog Video Edukasi
  await capture(page, `${BASE_URL}/siswa/videos`, '12-siswa-video-edukasi.png');

  // 13. Detail & Player Video Edukasi
  await capture(page, `${BASE_URL}/siswa/videos/6`, '13-siswa-detail-video.png', { delay: 1500 });

  // 14. Katalog Latihan Kuis
  await capture(page, `${BASE_URL}/siswa/quizzes`, '14-siswa-latihan-kuis.png');

  // 15. Detail Petunjuk Latihan Kuis
  await capture(page, `${BASE_URL}/siswa/quizzes/1`, '15-siswa-detail-kuis.png');

  // 16. Antarmuka Pengerjaan Soal Kuis Latihan
  await capture(page, `${BASE_URL}/siswa/quizzes/2/start`, '16-siswa-pengerjaan-kuis.png', { delay: 1500 });

  // 17. Hasil Skor 100 & Pembahasan Kuis Siswa
  await capture(page, `${BASE_URL}/siswa/attempts/1/result`, '17-siswa-hasil-kuis.png', { delay: 1500 });

  // 18. Katalog Real Materi / Ujian
  await capture(page, `${BASE_URL}/siswa/real-materi`, '18-siswa-real-materi-ujian.png');

  // 19. Detail Informasi Ujian Real
  await capture(page, `${BASE_URL}/siswa/real-materi/7`, '19-siswa-detail-real-materi.png');

  // 20. Hasil Lembar Nilai Ujian Real Siswa
  await capture(page, `${BASE_URL}/siswa/real-attempts/2/result`, '20-siswa-hasil-real-ujian.png', { delay: 1500 });

  // 21. Leaderboard / Papan Peringkat Siswa Nasional
  await capture(page, `${BASE_URL}/siswa/leaderboard`, '21-siswa-leaderboard.png', { delay: 1500 });

  // 22. Profil Siswa
  await capture(page, `${BASE_URL}/siswa/profile`, '22-siswa-profil.png', { delay: 1200 });

  // 23. Profil Siswa (Full Page)
  await capture(page, null, '23-siswa-profil-full.png', { fullPage: true });

  // ================================================================
  // FASE 3: PANEL ADMIN (PORTAL ADMINISTRATOR)
  // ================================================================
  console.log('\n>>> FASE 3: PANEL ADMIN (PORTAL ADMINISTRATOR) <<<');

  // Buat new context/page terpisah untuk admin
  const adminContext = await browser.createBrowserContext();
  const adminPage = await adminContext.newPage();
  await adminPage.setViewport({ width: 1440, height: 900, deviceScaleFactor: 1.5 });

  console.log('Melakukan login Admin (admin@gmail.com)...');
  await adminPage.goto(`${BASE_URL}/admin/login`, { waitUntil: 'domcontentloaded' });
  await adminPage.type('#email', 'admin@gmail.com');
  await adminPage.type('#password', 'password');
  await Promise.all([
    adminPage.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 15000 }),
    adminPage.click('button.submit-btn')
  ]);
  console.log('Berhasil login sebagai Admin! URL:', adminPage.url());

  // 24. Dashboard Admin (Viewport)
  await capture(adminPage, `${BASE_URL}/admin/dashboard`, '24-admin-dashboard.png', { delay: 2500 });

  // 25. Dashboard Admin (Full Page)
  await capture(adminPage, null, '25-admin-dashboard-full.png', { fullPage: true });

  // 26. Kelola Bahan Bacaan
  await capture(adminPage, `${BASE_URL}/admin/materials`, '26-admin-kelola-materi.png');

  // 27. Tambah Bahan Bacaan Baru
  await capture(adminPage, `${BASE_URL}/admin/materials/create`, '27-admin-tambah-materi.png');

  // 28. Edit Bahan Bacaan
  await capture(adminPage, `${BASE_URL}/admin/materials/1/edit`, '28-admin-edit-materi.png');

  // 29. Kelola Video Edukasi
  await capture(adminPage, `${BASE_URL}/admin/videos`, '29-admin-kelola-video.png');

  // 30. Tambah Video Edukasi Baru
  await capture(adminPage, `${BASE_URL}/admin/videos/create`, '30-admin-tambah-video.png');

  // 31. Kelola Latihan Kuis
  await capture(adminPage, `${BASE_URL}/admin/quizzes`, '31-admin-kelola-kuis.png');

  // 32. Tambah Latihan Kuis Baru
  await capture(adminPage, `${BASE_URL}/admin/quizzes/create`, '32-admin-tambah-kuis.png');

  // 33. Kelola Butir Soal Kuis (Bank Soal & Import)
  await capture(adminPage, `${BASE_URL}/admin/quizzes/1`, '33-admin-kelola-soal.png', { delay: 1500 });

  // 34. Tambah Butir Soal Kuis Baru
  await capture(adminPage, `${BASE_URL}/admin/quizzes/1/questions/create`, '34-admin-tambah-soal.png');

  // 35. Kelola Real Materi / Ujian (Toggle Status)
  await capture(adminPage, `${BASE_URL}/admin/real-materi`, '35-admin-kelola-real-materi.png');

  // 36. Monitoring Siswa
  await capture(adminPage, `${BASE_URL}/admin/students`, '36-admin-monitoring-siswa.png');

  // 37. Laporan & Rekapitulasi Nilai Siswa
  await capture(adminPage, `${BASE_URL}/admin/students/report`, '37-admin-laporan-rekap.png', { delay: 1500 });

  // 38. Detail Siswa & Riwayat Nilai
  await capture(adminPage, `${BASE_URL}/admin/students/2`, '38-admin-detail-siswa.png', { delay: 1500 });

  // 39. Profil Administrator
  await capture(adminPage, `${BASE_URL}/admin/profile`, '39-admin-profil.png');

  await browser.close();
  console.log('\n===============================================================');
  console.log('    SEMUA 39 SCREENSHOT PNG BERHASIL DIAMBIL & DISIMPAN!       ');
  console.log('===============================================================\n');
}

main().catch((err) => {
  console.error('Terjadi error dalam proses capture:', err);
  process.exit(1);
});
