{{-- Partial: Exam Security System (Watermark, Blackout Overlay & Violation Input) --}}
@php
    $student = Auth::user();
    $studentName = $student->name ?? 'Peserta Seleksi';
    $studentIdentity = $student->school_name ?? $student->dapil ?? $student->email ?? 'MPR-RI';
    $clientIp = request()->ip() ?? '127.0.0.1';
@endphp

<!-- Hidden input for tracking violation count during quiz -->
<input type="hidden" name="violations_count" id="violations_count" value="0">

<!-- 1. Dynamic Forensic Watermark Overlay (Anti-Foto HP & Anti-Leak) -->
<div id="exam-watermark-overlay" class="exam-watermark-overlay" aria-hidden="true">
    <div class="watermark-grid">
        @for($i = 0; $i < 30; $i++)
            <div class="watermark-cell">
                <span class="wm-title">MPR RI • SELEKSI 4 PILAR</span>
                <span class="wm-user">{{ $studentName }}</span>
                <span class="wm-school">{{ $studentIdentity }}</span>
                <span class="wm-meta">IP: {{ $clientIp }} | <span class="watermark-live-clock">--:--:-- WIB</span></span>
            </div>
        @endfor
    </div>
</div>

<!-- 2. Security Blackout Overlay (Tab Switch / Window Blur Detection) -->
<div id="exam-security-blackout" class="exam-security-blackout" style="display: none;" aria-live="assertive" role="alert">
    <div class="blackout-backdrop"></div>
    <div class="blackout-card">
        <div class="blackout-icon-pulse">
            <i class="fi fi-rr-shield-exclamation"></i>
        </div>
        <div class="blackout-tag">SISTEM INTEGRITAS SELEKSI MPR RI</div>
        <h2 class="blackout-title">⚠️ PERINGATAN INTEGRITAS UJIAN</h2>
        <p class="blackout-desc">
            Layar ditutup sementara karena Anda terdeteksi <strong>meninggalkan halaman ujian</strong> atau beralih ke jendela / tab / aplikasi lain.
        </p>

        <div class="blackout-status-box">
            <div class="blackout-status-row">
                <span class="status-dot-pulse"></span>
                <span>Aktivitas perpindahan tab terekam secara otomatis ke server pengawas seleksi.</span>
            </div>
            <div class="blackout-counter-preview" id="blackout-counter-preview">
                Toleransi maksimal: <strong>3 kali pelanggaran</strong> sebelum ujian dihentikan.
            </div>
        </div>

        <button type="button" id="blackout-refocus-btn" class="btn btn-primary blackout-action-btn">
            <i class="fi fi-rr-arrow-right"></i> Klik Disini untuk Kembali ke Ujian
        </button>
    </div>
</div>
