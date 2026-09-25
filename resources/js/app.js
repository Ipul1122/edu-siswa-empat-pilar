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
});
