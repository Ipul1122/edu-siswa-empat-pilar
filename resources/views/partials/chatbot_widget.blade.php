<!-- Floating Customer Service Chatbot Widget -->
<div id="cs-chatbot-root">
    <!-- Floating Trigger Button -->
    <button type="button" id="cs-chat-trigger" class="cs-chat-trigger" aria-label="Buka Layanan Bantuan CS" title="Customer Service & Tanya Jawab Seleksi">
        <div class="cs-badge-dot"></div>
        <i class="fi fi-rr-comment-alt-dots cs-icon-open"></i>
        <i class="fi fi-rr-cross cs-icon-close" style="display: none;"></i>
        <span class="cs-trigger-label">Bantuan CS</span>
    </button>

    <!-- Chat Modal Window -->
    <div id="cs-chat-modal" class="cs-chat-modal" style="display: none;" aria-hidden="true">
        <!-- Header -->
        <div class="cs-chat-header">
            <div class="cs-header-avatar">
                <img src="{{ asset('img/mpr-logo.svg') }}" alt="CS Logo">
                <span class="cs-online-status"></span>
            </div>
            <div class="cs-header-info">
                <h4>CS Seleksi Empat Pilar MPR RI</h4>
                <p>Respons Cepat • 38 Provinsi Se-Indonesia</p>
            </div>
            <button type="button" id="cs-chat-close-btn" class="cs-close-btn" aria-label="Tutup">
                ✕
            </button>
        </div>

        <!-- Chat Body / Messages -->
        <div class="cs-chat-body" id="cs-chat-body">
            <!-- Bot Greeting Message -->
            <div class="cs-msg cs-msg-bot">
                <div class="cs-bubble">
                    Halo! Kami siap membantu kendala teknis atau pertanyaan seputar <strong>Seleksi Online Empat Pilar MPR RI</strong>. Silakan pilih topik cepat di bawah atau ketikkan pertanyaan Anda:
                </div>
            </div>

            <!-- Quick Suggestion Chips -->
            <div class="cs-chips-container" id="cs-chips-container">
                <button type="button" class="cs-chip" data-query="Bagaimana jika internet daerah putus saat tes?">
                    📶 Sinyal daerah putus?
                </button>
                <button type="button" class="cs-chip" data-query="Ketentuan tim 10 siswa dalam 1 device">
                    👥 Aturan 10 anak 1 laptop
                </button>
                <button type="button" class="cs-chip" data-query="Pengawasan Zoom di HP terpisah kapasitas 500">
                    📹 Kamera Zoom di HP
                </button>
                <button type="button" class="cs-chip" data-query="Prosedur tes ulang jika mati listrik / kendala fatal">
                    🔄 Prosedur Tes Ulang
                </button>
                <button type="button" class="cs-chip" data-query="Jadwal seleksi Maret dan penentuan Top 9 lolos">
                    🏆 Jadwal & Top 9 Lolos
                </button>
            </div>
        </div>

        <!-- Hotline Call Center Bar -->
        <div class="cs-hotline-bar">
            <span>Butuh bantuan darurat petugas?</span>
            <a href="https://wa.me/6281234567890?text={{ urlencode('Halo Admin CS Seleksi Empat Pilar MPR RI, saya membutuhkan bantuan terkait seleksi online.') }}" target="_blank" rel="noopener" class="cs-wa-btn" id="cs-wa-hotline-link">
                <i class="fi fi-rr-phone-call"></i> WhatsApp Hotline CS
            </a>
        </div>

        <!-- Input Bar -->
        <form id="cs-chat-form" class="cs-chat-input-bar">
            <input type="text" id="cs-chat-input" placeholder="Ketik pertanyaan Anda di sini..." autocomplete="off" required>
            <button type="submit" id="cs-chat-send-btn" aria-label="Kirim Pesan">
                ➔
            </button>
        </form>
    </div>
</div>

<style>
/* Chatbot Floating Widget Styles */
.cs-chat-trigger {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: linear-gradient(135deg, rgb(var(--color-primary-rgb)) 0%, #b91c1c 100%);
    color: #fff;
    border: none;
    border-radius: 50px;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    box-shadow: 0 10px 25px rgba(220, 38, 38, 0.35);
    z-index: 99990;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    font-family: inherit;
    font-weight: 700;
    font-size: 0.88rem;
}
.cs-chat-trigger:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 14px 30px rgba(220, 38, 38, 0.45);
}
.cs-badge-dot {
    width: 8px;
    height: 8px;
    background: #4ade80;
    border-radius: 50%;
    box-shadow: 0 0 0 2px rgba(74, 222, 128, 0.4);
    animation: pulseGlow 2s infinite;
}
@keyframes pulseGlow {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.3); opacity: 0.7; }
}

.cs-chat-modal {
    position: fixed;
    bottom: 85px;
    right: 24px;
    width: 380px;
    max-width: calc(100vw - 32px);
    height: 520px;
    max-height: calc(100vh - 110px);
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.22);
    border: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    z-index: 99991;
    overflow: hidden;
    animation: csSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes csSlideUp {
    from { opacity: 0; transform: translateY(20px) scale(0.96); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.cs-chat-header {
    background: linear-gradient(135deg, var(--color-primary) 0%, #1e1b4b 100%);
    color: #fff;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.cs-header-avatar {
    position: relative;
    width: 38px;
    height: 38px;
    background: #fff;
    border-radius: 50%;
    padding: 4px;
    flex-shrink: 0;
}
.cs-header-avatar img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
.cs-online-status {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 10px;
    height: 10px;
    background: #22c55e;
    border: 2px solid #fff;
    border-radius: 50%;
}
.cs-header-info {
    flex: 1;
    min-width: 0;
}
.cs-header-info h4 {
    font-size: 0.88rem;
    font-weight: 700;
    margin: 0 0 2px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cs-header-info p {
    font-size: 0.72rem;
    opacity: 0.85;
    margin: 0;
}
.cs-close-btn {
    background: rgba(255,255,255,0.15);
    border: none;
    color: #fff;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    cursor: pointer;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.cs-close-btn:hover {
    background: rgba(255,255,255,0.3);
}

.cs-chat-body {
    flex: 1;
    padding: 14px 16px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 12px;
    background: #f8fafc;
}

.cs-msg {
    display: flex;
    flex-direction: column;
    max-width: 85%;
}
.cs-msg-bot {
    align-self: flex-start;
}
.cs-msg-user {
    align-self: flex-end;
}
.cs-bubble {
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 0.83rem;
    line-height: 1.5;
}
.cs-msg-bot .cs-bubble {
    background: #ffffff;
    color: #1e293b;
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 2px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.03);
}
.cs-msg-user .cs-bubble {
    background: rgb(var(--color-primary-rgb));
    color: #ffffff;
    border-bottom-right-radius: 2px;
}

.cs-chips-container {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 4px;
}
.cs-chip {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    padding: 6px 12px;
    font-size: 0.76rem;
    color: #334155;
    cursor: pointer;
    text-align: left;
    transition: all 0.2s;
    font-family: inherit;
    font-weight: 500;
}
.cs-chip:hover {
    background: rgba(var(--color-primary-rgb), 0.08);
    border-color: rgb(var(--color-primary-rgb));
    color: rgb(var(--color-primary-rgb));
}

.cs-hotline-bar {
    background: #f1f5f9;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.73rem;
    border-top: 1px solid #e2e8f0;
    color: #475569;
}
.cs-wa-btn {
    background: #16a34a;
    color: #fff;
    padding: 4px 10px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.72rem;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: background 0.2s;
}
.cs-wa-btn:hover {
    background: #15803d;
}

.cs-chat-input-bar {
    display: flex;
    border-top: 1px solid #e2e8f0;
    padding: 8px 10px;
    background: #ffffff;
}
.cs-chat-input-bar input {
    flex: 1;
    border: none;
    outline: none;
    padding: 8px 10px;
    font-size: 0.84rem;
    font-family: inherit;
}
.cs-chat-input-bar button {
    background: rgb(var(--color-primary-rgb));
    color: #fff;
    border: none;
    border-radius: 8px;
    width: 36px;
    height: 36px;
    cursor: pointer;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.cs-chat-input-bar button:hover {
    background: #b91c1c;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const trigger = document.getElementById('cs-chat-trigger');
    const modal = document.getElementById('cs-chat-modal');
    const closeBtn = document.getElementById('cs-chat-close-btn');
    const form = document.getElementById('cs-chat-form');
    const input = document.getElementById('cs-chat-input');
    const body = document.getElementById('cs-chat-body');
    const chipsContainer = document.getElementById('cs-chips-container');
    const hotlineLink = document.getElementById('cs-wa-hotline-link');

    if (!trigger || !modal) return;

    let isOpen = false;
    function toggleChat(open) {
        isOpen = (open !== undefined) ? open : !isOpen;
        modal.style.display = isOpen ? 'flex' : 'none';
        trigger.querySelector('.cs-icon-open').style.display = isOpen ? 'none' : 'inline-block';
        trigger.querySelector('.cs-icon-close').style.display = isOpen ? 'inline-block' : 'none';
        if (isOpen) {
            setTimeout(() => input.focus(), 100);
        }
    }

    trigger.addEventListener('click', () => toggleChat());
    closeBtn.addEventListener('click', () => toggleChat(false));

    function appendMessage(sender, text) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'cs-msg cs-msg-' + sender;
        const bubble = document.createElement('div');
        bubble.className = 'cs-bubble';
        
        // Simple markdown formatting
        let formatted = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
        bubble.innerHTML = formatted;
        
        msgDiv.appendChild(bubble);
        body.appendChild(msgDiv);
        body.scrollTop = body.scrollHeight;
    }

    function sendQuery(queryText) {
        if (!queryText.trim()) return;
        appendMessage('user', queryText);
        input.value = '';

        // Typing indicator
        const typingDiv = document.createElement('div');
        typingDiv.className = 'cs-msg cs-msg-bot cs-typing';
        typingDiv.innerHTML = '<div class="cs-bubble" style="color: #64748b; font-style: italic;">Sedang mengetik jawaban...</div>';
        body.appendChild(typingDiv);
        body.scrollTop = body.scrollHeight;

        fetch('{{ route("siswa.chatbot.ask") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: queryText })
        })
        .then(res => res.json())
        .then(data => {
            typingDiv.remove();
            if (data.reply) {
                appendMessage('bot', data.reply);
            }
            if (data.hotline_wa && hotlineLink) {
                hotlineLink.href = data.hotline_wa;
            }
            // Update suggestions
            if (data.suggestions && data.suggestions.length > 0) {
                chipsContainer.innerHTML = '';
                data.suggestions.forEach(s => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'cs-chip';
                    btn.textContent = s;
                    btn.dataset.query = s;
                    btn.addEventListener('click', () => sendQuery(s));
                    chipsContainer.appendChild(btn);
                });
                body.appendChild(chipsContainer);
                body.scrollTop = body.scrollHeight;
            }
        })
        .catch(() => {
            typingDiv.remove();
            appendMessage('bot', 'Koneksi ke server bantuan terputus. Silakan hubungi kami langsung via WhatsApp Hotline di bawah.');
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        sendQuery(input.value);
    });

    chipsContainer.querySelectorAll('.cs-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            sendQuery(this.dataset.query);
        });
    });
});
</script>
