@php
    use App\Support\LiveChatSettings;

    if (! LiveChatSettings::isEnabled()) {
        return;
    }

    $whatsappUrl = LiveChatSettings::whatsappUrl(LiveChatSettings::whatsappPrefill());
    $welcome = LiveChatSettings::welcomeMessage();
    $chatSendUrl = route('chat.send');
@endphp

<div
    x-data="beautageLiveChat({
        sendUrl: @js($chatSendUrl),
        welcome: @js($welcome),
        whatsappUrl: @js($whatsappUrl),
    })"
    x-cloak
    class="fixed bottom-20 md:bottom-6 right-4 z-[60] flex flex-col items-end gap-3"
>
    <div
        x-show="open"
        x-transition
        class="w-[min(100vw-2rem,380px)] bg-white border border-purple-100 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[min(70vh,520px)]"
        style="display: none;"
    >
        <div class="bg-gradient-to-r from-beautage-purple to-purple-900 text-white px-4 py-3 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold">Shop Pakistan</p>
                <p class="text-[10px] text-purple-200">Usually replies instantly</p>
            </div>
            <button type="button" @click="open = false" class="p-1 rounded-lg hover:bg-white/10" aria-label="Close chat">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="px-3 py-2 bg-purple-50/80 border-b border-purple-100 grid grid-cols-2 gap-2">
            <input type="text" x-model="customerName" placeholder="Your name (optional)" class="text-xs px-2 py-1.5 rounded-lg border border-purple-200 bg-white">
            <input type="tel" x-model="customerPhone" placeholder="Phone (optional)" class="text-xs px-2 py-1.5 rounded-lg border border-purple-200 bg-white">
        </div>

        <div class="flex-1 overflow-y-auto p-3 space-y-2 bg-slate-50/50 min-h-[220px]" x-ref="scroll">
            <template x-for="(msg, idx) in messages" :key="msg.id ?? idx">
                <div :class="msg.sender_type === 'customer' ? 'flex justify-end' : 'flex justify-start'">
                    <div
                        class="max-w-[85%] text-xs px-3 py-2 rounded-2xl whitespace-pre-wrap break-words"
                        :class="msg.sender_type === 'customer'
                            ? 'bg-beautage-pink text-white rounded-br-md'
                            : 'bg-white border border-purple-100 text-slate-700 rounded-bl-md shadow-sm'"
                        x-text="msg.message"
                    ></div>
                </div>
            </template>
            <div x-show="loading" class="text-[10px] text-slate-400 animate-pulse">Typing...</div>
        </div>

        <div class="p-3 border-t border-purple-100 bg-white space-y-2">
            <div x-show="productLinks.length" class="space-y-1.5 max-h-32 overflow-y-auto">
                <template x-for="(p, pIdx) in productLinks" :key="p.id ?? pIdx">
                    <a
                        :href="p.url"
                        class="flex items-center gap-2 p-2 rounded-xl border border-purple-100 hover:bg-purple-50 transition text-left"
                    >
                        <img x-show="p.image" :src="p.image" alt="" class="w-10 h-10 rounded-lg object-cover shrink-0 bg-slate-100">
                        <span class="min-w-0 flex-1">
                            <span class="block text-[11px] font-bold text-slate-800 truncate" x-text="p.name"></span>
                            <span class="block text-[10px] text-beautage-purple font-semibold" x-text="p.price_label"></span>
                        </span>
                    </a>
                </template>
            </div>
            <template x-if="lastWhatsappUrl">
                <a :href="lastWhatsappUrl" target="_blank" rel="noopener noreferrer" class="block text-center text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 py-2 rounded-xl transition">
                    Continue on WhatsApp
                </a>
            </template>
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input
                    type="text"
                    x-model="draft"
                    :disabled="loading"
                    placeholder="Order #, product name, delivery, payment..."
                    class="flex-1 text-xs px-3 py-2.5 rounded-xl border border-purple-200 focus:border-beautage-purple outline-none"
                    maxlength="2000"
                >
                <button type="submit" :disabled="loading || !draft.trim()" class="shrink-0 px-3 py-2 rounded-xl bg-beautage-purple text-white text-xs font-bold disabled:opacity-50">
                    Send
                </button>
            </form>
        </div>
    </div>

    <div class="flex flex-col gap-2 items-end">
        <a
            :href="whatsappUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="w-12 h-12 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg shadow-emerald-500/30 flex items-center justify-center transition hover:scale-105"
            title="Chat on WhatsApp"
        >
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>
        <button
            type="button"
            @click="toggle()"
            class="w-14 h-14 rounded-full bg-beautage-pink hover:bg-beautage-pinkHover text-white shadow-xl shadow-pink-500/30 flex items-center justify-center transition hover:scale-105"
            title="Live chat"
        >
            <i data-lucide="message-circle" class="w-7 h-7"></i>
        </button>
    </div>
</div>

<script>
function beautageLiveChat(config) {
    return {
        open: false,
        loading: false,
        draft: '',
        sessionId: '',
        customerName: '',
        customerPhone: '',
        messages: [],
        lastWhatsappUrl: null,
        productLinks: [],
        sendUrl: config.sendUrl,
        whatsappUrl: config.whatsappUrl,

        init() {
            ['beautage_chat_session', 'beautage_chat_name', 'beautage_chat_phone'].forEach((key) => {
                try {
                    sessionStorage.removeItem(key);
                } catch (e) { /* ignore */ }
            });
            this.messages.push({
                id: 'welcome',
                sender_type: 'bot',
                message: config.welcome,
            });
        },

        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                    this.scrollToBottom();
                });
            }
        },

        mergeServerMessages(serverMessages, pendingCustomerId) {
            (serverMessages || []).forEach((m) => {
                if (m.sender_type === 'customer') {
                    const idx = this.messages.findIndex((x) => x.id === pendingCustomerId);
                    if (idx >= 0) {
                        this.messages[idx] = m;
                    } else if (!this.messages.some((x) => x.id === m.id)) {
                        this.messages.push(m);
                    }
                    return;
                }
                if (!this.messages.some((x) => x.id === m.id)) {
                    this.messages.push(m);
                }
            });
        },

        scrollToBottom() {
            const el = this.$refs.scroll;
            if (el) {
                el.scrollTop = el.scrollHeight;
            }
        },

        async sendMessage() {
            const text = this.draft.trim();
            if (!text || this.loading) {
                return;
            }

            const pendingCustomerId = 'pending-' + Date.now();
            const clientMessageId = 'cmid-' + Date.now() + '-' + Math.random().toString(36).slice(2, 9);

            this.loading = true;
            this.draft = '';
            this.messages.push({
                id: pendingCustomerId,
                sender_type: 'customer',
                message: text,
            });
            this.$nextTick(() => this.scrollToBottom());

            try {
                const res = await fetch(this.sendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        session_id: this.sessionId || null,
                        customer_name: this.customerName || null,
                        customer_phone: this.customerPhone || null,
                        message: text,
                        client_message_id: clientMessageId,
                    }),
                });
                const data = window.BeautageDom
                    ? await window.BeautageDom.parseJsonResponse(res)
                    : await res.json();
                if (!res.ok || !data.success) {
                    this.messages = this.messages.filter((m) => m.id !== pendingCustomerId);
                    this.draft = text;
                    this.messages.push({
                        id: 'err-' + Date.now(),
                        sender_type: 'bot',
                        message: data.message || 'Could not send message. Please try WhatsApp.',
                    });
                    return;
                }
                if (data.session_id) {
                    this.sessionId = data.session_id;
                }
                this.mergeServerMessages(data.messages, pendingCustomerId);
                if (data.whatsapp_url) {
                    this.lastWhatsappUrl = data.whatsapp_url;
                }
                if (Array.isArray(data.products) && data.products.length) {
                    this.productLinks = data.products;
                }
            } catch (e) {
                this.messages = this.messages.filter((m) => m.id !== pendingCustomerId);
                this.draft = text;
                this.messages.push({
                    id: 'err-' + Date.now(),
                    sender_type: 'bot',
                    message: 'Network error — please use WhatsApp or try again.',
                });
            } finally {
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },
    };
}
</script>
<style>[x-cloak]{display:none!important}</style>
