@props([
    'audience' => 'public',
    'endpoint' => route('chatbot.message'),
])

<div class="ai-chatbot" data-chatbot data-audience="{{ $audience }}" data-endpoint="{{ $endpoint }}">
    <section class="ai-chatbot-panel" data-chatbot-panel aria-label="Chatbot Godong Ijo" hidden>
        <header class="ai-chatbot-header">
            <div class="ai-chatbot-identity">
                <span class="ai-chatbot-avatar" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 3a8 8 0 0 0-8 8v1a3 3 0 0 0 3 3h1v-5H6a6 6 0 0 1 12 0h-2v5h1a3 3 0 0 0 3-3v-1a8 8 0 0 0-8-8Z"/>
                        <path d="M8 19h8M10 21h4"/>
                    </svg>
                </span>
                <span>
                    <strong>{{ $audience === 'admin' ? 'Asisten Admin' : 'Asisten Godong Ijo' }}</strong>
                    <small><i aria-hidden="true"></i>{{ $audience === 'admin' ? 'Pusat bantuan operasional' : 'Pusat bantuan pelanggan' }}</small>
                </span>
            </div>
            <button type="button" class="ai-chatbot-close" data-chatbot-close aria-label="Tutup chatbot">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18"/>
                </svg>
            </button>
        </header>

        <div class="ai-chatbot-messages" data-chatbot-messages aria-live="polite">
            <div class="ai-chatbot-welcome-label">Bantuan cepat</div>
            <div class="ai-chatbot-message ai-chatbot-message-bot ai-chatbot-message-welcome">{{ $audience === 'admin' ? 'Halo Admin. Tanyakan tentang paket, booking, atau operasional.' : 'Halo. Mau cari info paket, harga, jam buka, atau cara booking?' }}</div>
            <div class="ai-chatbot-quick-replies" data-chatbot-quick-replies>
                @if($audience === 'admin')
                    @foreach([
                        'Bantuan fitur admin',
                        'Berapa booking pending?',
                        'Paket paling ramai bulan ini?',
                        'Berapa notifikasi belum dibaca?',
                        'Ada berapa pelanggan?',
                        'Aktivitas terbaru apa saja?',
                        'Ringkasan pendapatan dan booking',
                        'Cara cari booking berdasarkan kode',
                    ] as $suggestion)
                        <button type="button" data-chatbot-suggestion="{{ $suggestion }}">{{ $suggestion }}</button>
                    @endforeach
                @else
                    <button type="button" data-chatbot-suggestion="Lihat harga paket">Lihat harga paket</button>
                    <button type="button" data-chatbot-suggestion="Jam buka">Jam buka</button>
                    <button type="button" data-chatbot-suggestion="Cara booking">Cara booking</button>
                @endif
            </div>
        </div>

        <form class="ai-chatbot-form" data-chatbot-form>
            @csrf
            <input type="text" data-chatbot-input maxlength="500" placeholder="Tulis pertanyaan..." autocomplete="off">
            <button type="submit" aria-label="Kirim pertanyaan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
                </svg>
            </button>
        </form>
    </section>

    <button type="button" class="ai-chatbot-toggle" data-chatbot-toggle aria-label="Buka chatbot">
        <svg class="ai-chatbot-toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M20 11.5a7.5 7.5 0 0 1-8 7.5 8.7 8.7 0 0 1-3.5-.7L4 20l1.7-3.6A7.3 7.3 0 0 1 4 11.5 7.5 7.5 0 0 1 12 4a7.5 7.5 0 0 1 8 7.5Z"/>
            <path d="M8.5 11.5h.01M12 11.5h.01M15.5 11.5h.01"/>
        </svg>
        <span class="ai-chatbot-toggle-label">Chat</span>
    </button>
</div>

@once
<style>
.ai-chatbot { --chat-ink: #17352b; --chat-green: #087f5b; --chat-pale: #edf6f1; position: fixed; right: 24px; bottom: 24px; z-index: 1100; font-family: inherit; }
.ai-chatbot[data-audience="admin"] { --chat-green: #176b5a; --chat-pale: #edf5f2; }
.ai-chatbot[data-audience="public"] { bottom: 104px; }
.ai-chatbot-toggle { display: inline-flex; align-items: center; gap: 9px; border: 1px solid rgba(255,255,255,.18); border-radius: 15px; padding: 12px 16px; background: var(--chat-green); color: #fff; font: inherit; font-size: 13px; font-weight: 750; cursor: pointer; box-shadow: 0 12px 28px rgba(8, 127, 91, .24); transition: transform .2s ease, box-shadow .2s ease, background .2s ease; }
.ai-chatbot-toggle:hover { transform: translateY(-2px); background: #066b4d; box-shadow: 0 16px 32px rgba(8, 127, 91, .3); }
.ai-chatbot-toggle-icon { width: 18px; height: 18px; }
.ai-chatbot-panel { position: absolute; right: 0; bottom: 62px; display: flex; flex-direction: column; width: min(380px, calc(100vw - 32px)); height: min(560px, calc(100vh - 110px)); overflow: hidden; background: #fff; border: 1px solid #dce8e2; border-radius: 22px; box-shadow: 0 24px 70px rgba(15, 42, 35, .22); }
.ai-chatbot-panel[hidden] { display: none; }
.ai-chatbot-header { position: relative; display: flex; align-items: center; justify-content: space-between; padding: 17px 18px; background: linear-gradient(135deg, #087f5b 0%, #075f49 100%); color: #fff; }
.ai-chatbot-header::after { content: ''; position: absolute; right: -26px; bottom: -38px; width: 120px; height: 120px; border: 1px solid rgba(255,255,255,.12); border-radius: 50%; box-shadow: 0 0 0 16px rgba(255,255,255,.035), 0 0 0 32px rgba(255,255,255,.025); }
.ai-chatbot-identity { position: relative; z-index: 1; display: flex; align-items: center; gap: 11px; }
.ai-chatbot-identity strong, .ai-chatbot-identity small { display: block; }
.ai-chatbot-identity strong { font-size: 14px; letter-spacing: -.01em; }
.ai-chatbot-identity small { display: flex; align-items: center; gap: 5px; margin-top: 4px; color: rgba(255,255,255,.75); font-size: 10px; }
.ai-chatbot-identity small i { display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #9be7b5; box-shadow: 0 0 0 3px rgba(155,231,181,.12); }
.ai-chatbot-avatar { display: grid; place-items: center; width: 38px; height: 38px; border: 1px solid rgba(255,255,255,.18); border-radius: 12px; background: rgba(255,255,255,.14); }
.ai-chatbot-avatar svg { width: 19px; height: 19px; }
.ai-chatbot-close { position: relative; z-index: 1; display: grid; place-items: center; width: 30px; height: 30px; border: 1px solid rgba(255,255,255,.18); border-radius: 9px; background: rgba(255,255,255,.1); color: #fff; cursor: pointer; transition: background .2s ease; }
.ai-chatbot-close:hover { background: rgba(255,255,255,.2); }
.ai-chatbot-close svg { width: 15px; height: 15px; }
.ai-chatbot-messages { display: flex; flex: 1; flex-direction: column; gap: 10px; overflow-y: auto; padding: 20px 16px; background: #f5f8f6; }
.ai-chatbot-welcome-label { margin: 1px 0 -3px; color: #7b9087; font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
.ai-chatbot-message { max-width: 88%; padding: 9px 13px; border-radius: 15px; white-space: pre-line; font-size: 12px; line-height: 1.45; }
.ai-chatbot-message-welcome { margin-top: -1px; }
.ai-chatbot-message-bot { align-self: flex-start; background: #fff; border: 1px solid #e0eae4; border-top-left-radius: 5px; color: var(--chat-ink); box-shadow: 0 4px 12px rgba(22, 53, 43, .035); }
.ai-chatbot-message-user { align-self: flex-end; background: var(--chat-green); border-bottom-right-radius: 5px; color: #fff; }
.ai-chatbot-message-loading { color: #698078; font-style: italic; }
.ai-chatbot-quick-replies { display: flex; flex-wrap: wrap; gap: 7px; margin-top: 2px; }
.ai-chatbot-quick-replies button { border: 1px solid #b9dccc; border-radius: 10px; padding: 8px 10px; background: #fff; color: var(--chat-green); font: inherit; font-size: 11px; cursor: pointer; transition: border-color .2s ease, background .2s ease, transform .2s ease; }
.ai-chatbot-quick-replies button:hover { transform: translateY(-1px); border-color: var(--chat-green); background: var(--chat-pale); }
.ai-chatbot-form { display: flex; gap: 8px; padding: 12px 13px 13px; border-top: 1px solid #e1eae5; background: #fff; }
.ai-chatbot-form input { min-width: 0; flex: 1; border: 1px solid #d5e0db; border-radius: 12px; padding: 11px 12px; color: var(--chat-ink); font: inherit; font-size: 12px; outline: none; }
.ai-chatbot-form input:focus { border-color: var(--chat-green); box-shadow: 0 0 0 3px rgba(11,124,90,.1); }
.ai-chatbot-form button { display: grid; flex: 0 0 40px; place-items: center; border: 0; border-radius: 12px; background: var(--chat-green); color: #fff; cursor: pointer; transition: transform .2s ease, background .2s ease; }
.ai-chatbot-form button:hover { transform: translateY(-1px); background: #066b4d; }
.ai-chatbot-form button svg { width: 17px; height: 17px; }
@media (max-width: 768px) {
    .ai-chatbot { right: 16px; bottom: 16px; }
    .ai-chatbot[data-audience="public"] { bottom: 88px; }
    .ai-chatbot-panel { bottom: 58px; }
}
@media (max-width: 480px) {
    .ai-chatbot { right: 12px; bottom: 12px; }
    .ai-chatbot[data-audience="public"] { bottom: 78px; }
    .ai-chatbot-panel { right: -4px; width: min(380px, calc(100vw - 24px)); height: min(590px, calc(100vh - 96px)); border-radius: 20px; }
    .ai-chatbot-toggle { padding: 12px 14px; }
}
</style>
<script>
(() => {
    const root = document.querySelector('[data-chatbot]');
    if (!root || root.dataset.ready) return;
    root.dataset.ready = 'true';

    const panel = root.querySelector('[data-chatbot-panel]');
    const toggle = root.querySelector('[data-chatbot-toggle]');
    const close = root.querySelector('[data-chatbot-close]');
    const messages = root.querySelector('[data-chatbot-messages]');
    const form = root.querySelector('[data-chatbot-form]');
    const input = root.querySelector('[data-chatbot-input]');
    const quickReplies = root.querySelector('[data-chatbot-quick-replies]');

    const addMessage = (text, type, extraClass = '') => {
        const message = document.createElement('div');
        message.className = `ai-chatbot-message ai-chatbot-message-${type} ${extraClass}`;
        message.textContent = text;
        messages.appendChild(message);
        messages.scrollTop = messages.scrollHeight;
        return message;
    };

    const ask = async (value) => {
        const question = value.trim();
        if (!question) return;
        addMessage(question, 'user');
        input.value = '';
        quickReplies.hidden = true;
        const loading = addMessage('Sedang mencari jawaban...', 'bot', 'ai-chatbot-message-loading');

        try {
            const response = await fetch(root.dataset.endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': root.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify({ message: question }),
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Request failed');
            loading.remove();
            addMessage(data.message, 'bot');
            if (Array.isArray(data.quick_replies) && data.quick_replies.length) {
                quickReplies.innerHTML = '';
                data.quick_replies.forEach((reply) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.dataset.chatbotSuggestion = reply;
                    button.textContent = reply;
                    quickReplies.appendChild(button);
                });
                quickReplies.hidden = false;
            }
        } catch (error) {
            loading.remove();
            addMessage('Maaf, chatbot sedang tidak tersedia. Silakan hubungi WhatsApp kami untuk bantuan langsung.', 'bot');
        }
    };

    toggle.addEventListener('click', () => { panel.hidden = !panel.hidden; if (!panel.hidden) input.focus(); });
    close.addEventListener('click', () => { panel.hidden = true; });
    form.addEventListener('submit', (event) => { event.preventDefault(); ask(input.value); });
    quickReplies.addEventListener('click', (event) => {
        const button = event.target.closest('[data-chatbot-suggestion]');
        if (button) ask(button.dataset.chatbotSuggestion);
    });
})();
</script>
@endonce
