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

    // 2. SweetAlert2 - Flash Toast Notifications
    // We will check for meta tags or script-passed variables in the layouts
    const flashSuccess = document.querySelector('meta[name="flash-success"]');
    const flashError = document.querySelector('meta[name="flash-error"]');
    
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        if (flashSuccess && flashSuccess.content) {
            Toast.fire({
                icon: 'success',
                title: flashSuccess.content
            });
        }

        if (flashError && flashError.content) {
            Toast.fire({
                icon: 'error',
                title: flashError.content
            });
        }
    }

    // 3. SweetAlert2 - Delete Confirmations
    const deleteForms = document.querySelectorAll('.delete-confirm-form');
    if (deleteForms.length > 0 && typeof Swal !== 'undefined') {
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                
                const itemType = this.getAttribute('data-item-type') || 'data';
                
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Tindakan ini akan menghapus ${itemType} secara permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'swal2-glass-card'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
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
                        quizForm.submit();
                    });
                } else {
                    alert('Waktu habis! Jawaban Anda akan dikirimkan otomatis.');
                    quizForm.submit();
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

        quizForm.addEventListener('submit', function () {
            quizForm.dataset.submitted = 'true';
        });
    }
});
