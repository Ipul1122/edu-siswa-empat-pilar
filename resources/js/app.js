// Import SweetAlert2 if using npm, otherwise we assume it's loaded via CDN in layout
// Since this is a monolith with Vite, we will support both. To make it extremely robust, 
// we will check if Swal is defined globally (CDN) or use a fallback.

document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile Sidebar Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const appSidebar = document.querySelector('.app-sidebar');
    
    if (menuToggle && appSidebar) {
        menuToggle.addEventListener('click', function () {
            appSidebar.classList.toggle('open');
        });
        
        // Close sidebar when clicking outside of it on mobile
        document.addEventListener('click', function (event) {
            const isClickInside = appSidebar.contains(event.target) || menuToggle.contains(event.target);
            if (!isClickInside && appSidebar.classList.contains('open')) {
                appSidebar.classList.remove('open');
            }
        });
    }

    // 2. SweetAlert2 - Global Helpers & Flash Notifications
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // Expose helpers globally
        window.showToast = function (icon, title, timer = 4000) {
            Toast.fire({ icon, title, timer });
        };

        window.showModal = function (title, htmlOrText = '', icon = 'info', confirmText = 'Baik, Mengerti') {
            return Swal.fire({
                title: title,
                html: htmlOrText,
                icon: icon,
                confirmButtonColor: 'rgb(var(--color-primary-rgb, 229, 57, 53))',
                confirmButtonText: confirmText,
                customClass: { popup: 'swal2-glass-card' }
            });
        };

        window.showConfirm = function (options = {}) {
            return Swal.fire({
                title: options.title || 'Apakah Anda yakin?',
                text: options.text || '',
                html: options.html || undefined,
                icon: options.icon || 'warning',
                showCancelButton: true,
                confirmButtonColor: options.confirmButtonColor || 'rgb(var(--color-primary-rgb, 229, 57, 53))',
                cancelButtonColor: options.cancelButtonColor || '#64748b',
                confirmButtonText: options.confirmButtonText || 'Ya, Lanjutkan',
                cancelButtonText: options.cancelButtonText || 'Batal',
                reverseButtons: true,
                customClass: { popup: 'swal2-glass-card' }
            });
        };

        // Safe drop-in override for window.alert
        window.alert = function (message) {
            Swal.fire({
                title: 'Pemberitahuan',
                text: message,
                icon: 'info',
                confirmButtonColor: 'rgb(var(--color-primary-rgb, 229, 57, 53))',
                confirmButtonText: 'Tutup'
            });
        };

        // Flash message detection from meta tags
        const flashSuccess = document.querySelector('meta[name="flash-success"]');
        const flashError = document.querySelector('meta[name="flash-error"]');
        const flashWarning = document.querySelector('meta[name="flash-warning"]');
        const flashInfo = document.querySelector('meta[name="flash-info"]');
        const flashStatus = document.querySelector('meta[name="flash-status"]');
        const swalModalTitle = document.querySelector('meta[name="flash-swal-title"]');
        const swalModalText = document.querySelector('meta[name="flash-swal-text"]');
        const swalModalIcon = document.querySelector('meta[name="flash-swal-icon"]');

        if (swalModalTitle && swalModalTitle.content) {
            Swal.fire({
                title: swalModalTitle.content,
                text: swalModalText ? swalModalText.content : '',
                icon: swalModalIcon ? swalModalIcon.content : 'info',
                confirmButtonColor: 'rgb(var(--color-primary-rgb, 229, 57, 53))',
                confirmButtonText: 'Mengerti'
            });
        } else {
            if (flashSuccess && flashSuccess.content) {
                Toast.fire({ icon: 'success', title: flashSuccess.content });
            }
            if (flashError && flashError.content) {
                Toast.fire({ icon: 'error', title: flashError.content, timer: 5000 });
            }
            if (flashWarning && flashWarning.content) {
                Toast.fire({ icon: 'warning', title: flashWarning.content, timer: 4500 });
            }
            if (flashInfo && flashInfo.content) {
                Toast.fire({ icon: 'info', title: flashInfo.content });
            }
            if (flashStatus && flashStatus.content) {
                Toast.fire({ icon: 'info', title: flashStatus.content });
            }
        }
    }

    // 3. SweetAlert2 - Delete & Logout Confirmations
    if (typeof Swal !== 'undefined') {
        // Delete Confirmations
        const deleteForms = document.querySelectorAll('.delete-confirm-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const itemType = this.getAttribute('data-item-type') || 'data';
                
                Swal.fire({
                    title: 'Hapus ' + itemType + '?',
                    text: `Tindakan ini tidak dapat dibatalkan. ${itemType} akan dihapus secara permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e53935',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'swal2-glass-card' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Logout Confirmations
        const logoutTriggers = document.querySelectorAll('#logout-form, .topbar-logout-btn, .logout-btn-link');
        logoutTriggers.forEach(el => {
            const form = el.tagName === 'FORM' ? el : el.closest('form');
            if (form && !form.dataset.swalAttached) {
                form.dataset.swalAttached = 'true';
                form.addEventListener('submit', function (e) {
                    if (form.dataset.confirmed === 'true') {
                        return true;
                    }
                    e.preventDefault();

                    Swal.fire({
                        title: 'Konfirmasi Keluar',
                        text: 'Apakah Anda yakin ingin keluar dari sesi akun Anda saat ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: 'rgb(var(--color-primary-rgb, 229, 57, 53))',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Keluar',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: { popup: 'swal2-glass-card' }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = 'true';
                            form.submit();
                        }
                    });
                });
            }
        });
    }

    // 4. Interactive Quiz Option Selection Styling
    const optionLabels = document.querySelectorAll('.option-item label');
    if (optionLabels.length > 0) {
        optionLabels.forEach(label => {
            label.addEventListener('click', function () {
                const optionItem = this.closest('.option-item');
                const optionsList = optionItem.closest('.options-list');
                
                // Remove selected class from all options in this question
                optionsList.querySelectorAll('.option-item').forEach(item => {
                    item.classList.remove('selected');
                });
                
                // Add selected class to the clicked one
                optionItem.classList.add('selected');
            });
        });
        
        // Initialize style on loaded page for old inputs (e.g. back button or validation error)
        document.querySelectorAll('.option-item input[type="radio"]:checked').forEach(radio => {
            radio.closest('.option-item').classList.add('selected');
        });
    }

    // 4.1 CBT Question Palette & Flag Logic
    const paletteGrid = document.querySelector('.palette-grid');
    if (paletteGrid) {
        const updatePaletteProgress = () => {
            const answeredSet = new Set();
            document.querySelectorAll('input[type="radio"][name^="answers["]:checked').forEach(radio => {
                if (radio.value) {
                    const qId = radio.dataset.questionId;
                    answeredSet.add(qId);
                    const pBtn = document.getElementById('palette-btn-' + qId);
                    if (pBtn) {
                        pBtn.classList.add('answered');
                    }
                }
            });

            const totalAnswered = answeredSet.size;
            const totalBtns = document.querySelectorAll('.palette-btn').length;
            const countBadge = document.getElementById('answered-count-badge');
            const progressFill = document.getElementById('palette-progress-fill');

            if (countBadge) {
                countBadge.textContent = `${totalAnswered} / ${totalBtns}`;
            }
            if (progressFill && totalBtns > 0) {
                const percent = Math.round((totalAnswered / totalBtns) * 100);
                progressFill.style.width = `${percent}%`;
            }
        };

        // Palette jump buttons
        document.querySelectorAll('.palette-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Highlight animation
                    targetElement.style.transition = 'box-shadow 0.3s ease, border-color 0.3s ease';
                    targetElement.style.borderColor = 'rgb(var(--color-primary-rgb))';
                    targetElement.style.boxShadow = '0 0 0 4px rgba(var(--color-primary-rgb), 0.18)';
                    setTimeout(() => {
                        targetElement.style.borderColor = '';
                        targetElement.style.boxShadow = '';
                    }, 1200);
                }
            });
        });

        // Question flag toggling
        document.querySelectorAll('.flag-question-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const qId = this.getAttribute('data-question-id');
                this.classList.toggle('active');
                const pBtn = document.getElementById('palette-btn-' + qId);
                if (pBtn) {
                    pBtn.classList.toggle('flagged');
                }
            });
        });

        // Radio change updates palette state & progress
        document.querySelectorAll('input[type="radio"][name^="answers["]').forEach(radio => {
            radio.addEventListener('change', function () {
                updatePaletteProgress();
            });
        });

        // Initial check on load
        updatePaletteProgress();
    }

    // 5. Quiz Timer Logic
    const timerElement = document.getElementById('quiz-timer');
    const quizForm = document.getElementById('quiz-form');
    
    if (timerElement && quizForm) {
        const durationMinutes = parseInt(timerElement.getAttribute('data-duration'), 10);
        let timeRemaining = durationMinutes * 60;
        
        const secondsTakenField = document.getElementById('duration_seconds_taken');
        let secondsElapsed = 0;

        const timerInterval = setInterval(function () {
            secondsElapsed++;
            if (secondsTakenField) {
                secondsTakenField.value = secondsElapsed;
            }

            timeRemaining--;
            
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            
            // Format time: MM:SS
            timerElement.textContent = 
                (minutes < 10 ? '0' : '') + minutes + ':' + 
                (seconds < 10 ? '0' : '') + seconds;

            // Timer warning when less than 1 minute remaining
            if (timeRemaining <= 60) {
                timerElement.classList.add('timer-warning');
            }

            // Time's up!
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Waktu Habis!',
                        text: 'Waktu pengerjaan kuis telah habis. Jawaban Anda akan dikirimkan otomatis.',
                        icon: 'info',
                        confirmButtonText: 'Kirim Sekarang',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        if (quizForm.dataset.submitting !== 'true') {
                            quizForm.dataset.submitting = 'true';
                            quizForm.dataset.submitted = 'true';
                            quizForm.submit();
                        }
                    });
                } else {
                    alert('Waktu habis! Jawaban Anda akan dikirimkan otomatis.');
                    if (quizForm.dataset.submitting !== 'true') {
                        quizForm.dataset.submitting = 'true';
                        quizForm.dataset.submitted = 'true';
                        quizForm.submit();
                    }
                }
            }
        }, 1000);
        
        // Prevent leaving page accidentally during quiz
        window.addEventListener('beforeunload', function (e) {
            // Only confirm if they haven't submitted yet
            if (quizForm.dataset.submitted === 'true') {
                return;
            }
            e.preventDefault();
            e.returnValue = 'Apakah Anda yakin ingin meninggalkan kuis? Progress pengerjaan Anda akan hilang.';
        });

        quizForm.addEventListener('submit', function (e) {
            if (quizForm.dataset.submitting === 'true' || quizForm.dataset.confirmed === 'true') {
                quizForm.dataset.submitted = 'true';
                quizForm.dataset.submitting = 'true';
                return true;
            }

            e.preventDefault();

            // Calculate answered vs unanswered questions
            const totalQuestions = document.querySelectorAll('.palette-btn').length || document.querySelectorAll('.question-block').length;
            const answeredSet = new Set();
            document.querySelectorAll('input[type="radio"][name^="answers["]:checked').forEach(radio => {
                if (radio.value) answeredSet.add(radio.dataset.questionId);
            });
            const answeredCount = answeredSet.size;
            const unansweredCount = Math.max(0, totalQuestions - answeredCount);

            let confirmHtml = `<div style="text-align: left; font-size: 0.95rem; margin-top: 8px;">
                <p>Apakah Anda yakin ingin menyelesaikan dan mengumpulkan lembar jawaban ujian ini?</p>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-top: 10px; line-height: 1.6;">
                    <div>📝 <strong>Total Soal:</strong> ${totalQuestions} butir</div>
                    <div style="color: #10b981;">✅ <strong>Sudah Terjawab:</strong> ${answeredCount} butir</div>
                    ${unansweredCount > 0 ? `<div style="color: #ef4444; font-weight: 700;">⚠️ <strong>Belum Dijawab:</strong> ${unansweredCount} butir</div>` : '<div style="color: #10b981; font-weight: 600;">✨ Seluruh soal telah terjawab lengkap!</div>'}
                </div>
                <p style="margin-top: 10px; font-size: 0.85rem; color: #64748b;">⚠️ Lembar jawaban yang telah dikumpulkan tidak dapat diubah kembali.</p>
            </div>`;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi Selesai Ujian',
                    html: confirmHtml,
                    icon: unansweredCount > 0 ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Selesai & Kumpulkan',
                    cancelButtonText: 'Periksa Kembali',
                    reverseButtons: true,
                    customClass: { popup: 'swal2-glass-card' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        quizForm.dataset.confirmed = 'true';
                        quizForm.dataset.submitted = 'true';
                        quizForm.dataset.submitting = 'true';

                        const submitBtns = quizForm.querySelectorAll('button[type="submit"]');
                        submitBtns.forEach(btn => {
                            btn.disabled = true;
                            btn.textContent = 'Menyimpan...';
                        });

                        quizForm.submit();
                    }
                });
            } else {
                if (confirm(`Apakah Anda yakin ingin mengumpulkan ujian? Terjawab: ${answeredCount}/${totalQuestions}`)) {
                    quizForm.dataset.confirmed = 'true';
                    quizForm.dataset.submitted = 'true';
                    quizForm.dataset.submitting = 'true';
                    quizForm.submit();
                }
            }
        });

        // 5.1 Exam Anti-Cheat & Anti-Screenshot Suite (Section 2.6)
        // A. Dynamic Forensic Watermark Live Clock Updater
        const updateWatermarkClock = () => {
            const now = new Date();
            const pad = (n) => String(n).padStart(2, '0');
            const timeStr = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())} WIB`;
            document.querySelectorAll('.watermark-live-clock').forEach(el => {
                el.textContent = timeStr;
            });
        };
        updateWatermarkClock();
        setInterval(updateWatermarkClock, 1000);

        // B. Disable Context Menu (Right Click)
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
            if (typeof window.showToast === 'function') {
                window.showToast('warning', 'Klik kanan dinonaktifkan demi integritas ujian seleksi.', 2500);
            }
        });

        // C. Disable Dragging Text / Images
        document.addEventListener('dragstart', function (e) {
            e.preventDefault();
        });

        // D. Disable Copy & Cut
        document.addEventListener('copy', function (e) {
            e.preventDefault();
        });
        document.addEventListener('cut', function (e) {
            e.preventDefault();
        });

        // E. Block Print, Save, DevTools & Clear Clipboard on PrintScreen
        window.addEventListener('keydown', function (e) {
            const isCtrlOrMeta = e.ctrlKey || e.metaKey;

            // 1. PrintScreen Handler (Clear system clipboard immediately)
            if (e.key === 'PrintScreen' || e.code === 'PrintScreen') {
                e.preventDefault();
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText('').catch(() => {});
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Tangkapan Layar Dilarang!',
                        text: 'Pengambilan tangkapan layar (PrintScreen) dilarang selama ujian berlangsung. Clipboard sistem telah dikosongkan.',
                        icon: 'warning',
                        confirmButtonColor: 'rgb(var(--color-primary-rgb, 229, 57, 53))',
                        confirmButtonText: 'Saya Mengerti',
                        customClass: { popup: 'swal2-glass-card' }
                    });
                }
                return;
            }

            // 2. Block Ctrl+P (Print), Ctrl+S (Save), Ctrl+U (View Source)
            if (isCtrlOrMeta && (e.key === 'p' || e.key === 'P' || e.key === 's' || e.key === 'S' || e.key === 'u' || e.key === 'U')) {
                e.preventDefault();
                if (typeof window.showToast === 'function') {
                    window.showToast('warning', 'Fitur cetak, simpan, dan inspeksi kode dilarang selama ujian.', 2500);
                }
                return;
            }

            // 3. Block F12 and DevTools shortcuts (Ctrl+Shift+I / J / C)
            if (e.key === 'F12' || (isCtrlOrMeta && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.key === 'J' || e.key === 'j' || e.key === 'C' || e.key === 'c'))) {
                e.preventDefault();
                if (typeof window.showToast === 'function') {
                    window.showToast('warning', 'Fitur Developer Tools dinonaktifkan.', 2500);
                }
                return;
            }
        });

        // F. Tab Switch & Focus Blur Detection (Anti-Joki / Perekam)
        let violationsCount = 0;
        const MAX_VIOLATIONS = 3;
        const violationsInput = document.getElementById('violations_count');
        const blackoutOverlay = document.getElementById('exam-security-blackout');
        const refocusBtn = document.getElementById('blackout-refocus-btn');
        const violationPill = document.getElementById('exam-violation-pill');
        const violationLabel = document.getElementById('violation-label');
        let isAway = false;

        const updateViolationUI = () => {
            if (violationsInput) {
                violationsInput.value = violationsCount;
            }
            if (violationLabel) {
                if (violationsCount === 0) {
                    violationLabel.textContent = 'Integritas 0/3';
                } else {
                    violationLabel.textContent = `Pelanggaran ${violationsCount}/3`;
                }
            }
            if (violationPill) {
                violationPill.classList.remove('warning-1', 'warning-2');
                if (violationsCount === 1) violationPill.classList.add('warning-1');
                if (violationsCount >= 2) violationPill.classList.add('warning-2');
            }
        };

        const handleUserLeft = () => {
            if (quizForm.dataset.submitted === 'true' || quizForm.dataset.submitting === 'true' || isAway) return;
            isAway = true;
            if (blackoutOverlay) {
                blackoutOverlay.style.display = 'flex';
            }
        };

        const handleUserReturned = () => {
            if (!isAway || quizForm.dataset.submitted === 'true' || quizForm.dataset.submitting === 'true') return;
            isAway = false;
            if (blackoutOverlay) {
                blackoutOverlay.style.display = 'none';
            }

            violationsCount++;
            updateViolationUI();

            if (violationsCount < MAX_VIOLATIONS) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '⚠️ Peringatan Integritas Ujian',
                        html: `
                            <div style="text-align: left; font-size: 0.95rem; line-height: 1.6;">
                                <p>Anda terdeteksi beralih dari jendela / tab ujian. Seluruh aktivitas perpindahan layar terekam oleh server pengawas.</p>
                                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px; margin-top: 10px; color: #b91c1c;">
                                    <strong>Pelanggaran Ke-${violationsCount} dari ${MAX_VIOLATIONS}</strong>
                                </div>
                                <p style="margin-top: 10px; font-size: 0.85rem; color: #64748b;">
                                    ⚠️ Batas toleransi adalah <strong>${MAX_VIOLATIONS} kali</strong>. Jika mencapai 3 kali pelanggaran, ujian akan <strong>otomatis dihentikan dan dikumpulkan</strong>.
                                </p>
                            </div>
                        `,
                        icon: 'warning',
                        confirmButtonColor: '#b91c1c',
                        confirmButtonText: 'Kembali Mengerjakan Ujian',
                        allowOutsideClick: false,
                        customClass: { popup: 'swal2-glass-card' }
                    });
                }
            } else {
                // Violations reached limit -> Auto Submit!
                quizForm.dataset.confirmed = 'true';
                quizForm.dataset.submitted = 'true';
                quizForm.dataset.submitting = 'true';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '⛔ Batas Pelanggaran Terlampaui!',
                        html: `
                            <div style="text-align: center; font-size: 0.95rem;">
                                <p>Anda telah mencapai <strong>${MAX_VIOLATIONS} kali pelanggaran</strong> pergantian layar/tab.</p>
                                <div style="background: #fee2e2; border: 1px solid #ef4444; border-radius: 8px; padding: 12px; margin: 12px 0; color: #991b1b; font-weight: 700;">
                                    Status: Diskualifikasi Otomatis (Auto-Submit)
                                </div>
                                <p style="color: #64748b; font-size: 0.85rem;">Lembar jawaban Anda sedang dikumpulkan otomatis ke server pengawas.</p>
                            </div>
                        `,
                        icon: 'error',
                        confirmButtonText: 'Mengerti & Kumpulkan',
                        confirmButtonColor: '#dc2626',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        customClass: { popup: 'swal2-glass-card' }
                    }).then(() => {
                        const submitBtns = quizForm.querySelectorAll('button[type="submit"]');
                        submitBtns.forEach(btn => {
                            btn.disabled = true;
                            btn.textContent = 'Mengumpulkan...';
                        });
                        quizForm.submit();
                    });
                } else {
                    alert(`Batas pelanggaran terlampaui (${MAX_VIOLATIONS}/${MAX_VIOLATIONS})! Lembar jawaban Anda dikumpulkan otomatis.`);
                    quizForm.submit();
                }
            }
        };

        // Visibility & blur listeners
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                handleUserLeft();
            } else {
                handleUserReturned();
            }
        });

        window.addEventListener('blur', function () {
            handleUserLeft();
        });

        window.addEventListener('focus', function () {
            if (isAway) {
                handleUserReturned();
            }
        });

        if (refocusBtn) {
            refocusBtn.addEventListener('click', function () {
                handleUserReturned();
            });
        }
    }

    // 6. Topbar Global Live Search Filter
    const searchInputs = document.querySelectorAll('#global-search-input, #admin-global-search');
    searchInputs.forEach(searchInput => {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            
            // Filter cards on material / video / quiz pages
            const cards = document.querySelectorAll('.card, .quiz-card, .table tbody tr');
            if (cards.length > 0) {
                cards.forEach(card => {
                    const text = card.textContent.toLowerCase();
                    if (query === '' || text.includes(query)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
        });
    });

    // 7. Full Page Focus Mode Controller (Real Materi, Kuis, Baca Materi, Tonton Video)
    const fullpageEnabled = document.body.dataset.fullpageEnabled === 'true';

    if (fullpageEnabled) {
        const exitBtn = document.getElementById('fullpage-exit-btn');
        const reenterBtn = document.getElementById('fullpage-reenter-btn');
        const mobilePill = document.getElementById('fullpage-mobile-pill');

        const exitFullPage = (showToast = true) => {
            if (!document.body.classList.contains('fullpage-mode')) return;
            document.body.classList.remove('fullpage-mode');

            // Exit browser native fullscreen if active
            if (document.fullscreenElement || document.webkitFullscreenElement) {
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(() => {});
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
            }

            if (showToast && typeof window.showToast === 'function') {
                window.showToast('info', 'Keluar dari mode layar penuh', 2500);
            }
        };

        const enterFullPage = (requestBrowserFullscreen = true) => {
            document.body.classList.add('fullpage-mode');

            if (requestBrowserFullscreen && !document.fullscreenElement) {
                const docEl = document.documentElement;
                if (docEl.requestFullscreen) {
                    docEl.requestFullscreen().catch(() => {});
                } else if (docEl.webkitRequestFullscreen) {
                    docEl.webkitRequestFullscreen();
                }
            }

            if (mobilePill) {
                mobilePill.classList.remove('fade-out');
                setTimeout(() => {
                    mobilePill.classList.add('fade-out');
                }, 4000);
            }

            if (typeof window.showToast === 'function') {
                window.showToast('success', 'Mode layar penuh aktif', 2000);
            }
        };

        // 1. Click "X" button to exit
        if (exitBtn) {
            exitBtn.addEventListener('click', function (e) {
                e.preventDefault();
                exitFullPage(true);
            });
        }

        // 2. Click re-enter button to return to full page
        if (reenterBtn) {
            reenterBtn.addEventListener('click', function (e) {
                e.preventDefault();
                enterFullPage(true);
            });
        }

        // 3. Keyboard 'Esc' key listener
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                if (document.body.classList.contains('fullpage-mode')) {
                    exitFullPage(true);
                }
            }
        });

        // 4. Mobile Swipe Down Gesture Listener
        let touchStartY = 0;
        let touchStartX = 0;
        let touchStartTime = 0;

        window.addEventListener('touchstart', function (e) {
            if (!document.body.classList.contains('fullpage-mode')) return;
            if (e.touches && e.touches.length === 1) {
                touchStartY = e.touches[0].clientY;
                touchStartX = e.touches[0].clientX;
                touchStartTime = Date.now();
            }
        }, { passive: true });

        window.addEventListener('touchmove', function (e) {
            if (!document.body.classList.contains('fullpage-mode')) return;
            if (e.touches && e.touches.length === 1) {
                const currentY = e.touches[0].clientY;
                const deltaY = currentY - touchStartY;
                // If near top of page and dragging down
                if (window.scrollY <= 15 && deltaY > 30 && mobilePill) {
                    mobilePill.classList.remove('fade-out');
                    mobilePill.classList.add('active');
                }
            }
        }, { passive: true });

        window.addEventListener('touchend', function (e) {
            if (!document.body.classList.contains('fullpage-mode')) return;
            if (e.changedTouches && e.changedTouches.length === 1) {
                const endY = e.changedTouches[0].clientY;
                const endX = e.changedTouches[0].clientX;
                const deltaY = endY - touchStartY;
                const deltaX = Math.abs(endX - touchStartX);
                const duration = Date.now() - touchStartTime;

                // Swipe down condition:
                // Scrolled near top (window.scrollY <= 25), dragged down > 70px, more vertical than horizontal
                if (window.scrollY <= 25 && deltaY > 70 && deltaY > deltaX * 1.4 && (duration < 800 || touchStartY < 120)) {
                    exitFullPage(true);
                } else if (mobilePill) {
                    mobilePill.classList.remove('active');
                    mobilePill.classList.add('fade-out');
                }
            }
        }, { passive: true });

        // Auto-fade mobile pill initially after 4s
        if (mobilePill) {
            setTimeout(() => {
                mobilePill.classList.add('fade-out');
            }, 4000);
        }
    }
});
