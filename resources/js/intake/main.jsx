import { createRoot } from 'react-dom/client';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';

function randomVisitorToken() {
    const a = new Uint8Array(24);
    crypto.getRandomValues(a);
    return Array.from(a, (b) => b.toString(16).padStart(2, '0')).join('');
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

const QUICK_REPLIES = [
    'I need help scoping a new product or platform',
    'I want to augment my team with senior engineers',
    "I'm exploring options — not sure where to start",
];

function IntakeApp({ storeUrl, chatUrl, captchaDriver, turnstileSiteKey }) {
    const visitorToken = useMemo(() => randomVisitorToken(), []);
    const [phase, setPhase] = useState('form');
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [phone, setPhone] = useState('');
    const [honeypot, setHoneypot] = useState('');
    const [error, setError] = useState('');
    const [messages, setMessages] = useState([]);
    const [conversationId, setConversationId] = useState(null);
    const [agentSlug, setAgentSlug] = useState(null);
    const [typing, setTyping] = useState(false);
    const [input, setInput] = useState('');
    const listRef = useRef(null);

    useEffect(() => {
        const el = listRef.current;
        if (el) {
            el.scrollTop = el.scrollHeight;
        }
    }, [messages, typing, phase]);

    const submitForm = async (e) => {
        e.preventDefault();
        setError('');
        if (honeypot !== '') {
            return;
        }
        setPhase('loading');
        try {
            const tz = Intl.DateTimeFormat().resolvedOptions().timeZone ?? '';
            const locale = navigator.language ?? '';
            const res = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    name: name.trim(),
                    email: email.trim(),
                    phone: phone.trim() || null,
                    visitor_token: visitorToken,
                    client_context: {
                        timezone: tz || null,
                        locale: locale || null,
                    },
                    address_line_2: honeypot,
                    'cf-turnstile-response':
                        document.querySelector('[name="cf-turnstile-response"]')?.value ?? '',
                }),
            });
            const json = await res.json().catch(() => ({}));
            if (!res.ok) {
                setPhase('form');
                setError(json.message || 'Something went wrong. Please try again.');
                return;
            }
            const d = json.data;
            setConversationId(d.conversation_public_id);
            setAgentSlug(d.agent_slug);
            setMessages([{ role: 'assistant', content: d.opening_message }]);
            setPhase('chat');
        } catch {
            setPhase('form');
            setError('Network error. Please check your connection.');
        }
    };

    const sendChat = useCallback(
        async (text) => {
            const trimmed = text.trim();
            if (!trimmed || !agentSlug || !conversationId) return;
            setMessages((m) => [...m, { role: 'user', content: trimmed }]);
            setInput('');
            setTyping(true);
            setError('');
            try {
                const res = await fetch(chatUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        agent_slug: agentSlug,
                        message: trimmed,
                        conversation_public_id: conversationId,
                        visitor_token: visitorToken,
                        'cf-turnstile-response':
                            document.querySelector('[name="cf-turnstile-response"]')?.value ?? '',
                    }),
                });
                const json = await res.json().catch(() => ({}));
                setTyping(false);
                if (!res.ok) {
                    setError(json.message || 'Could not send message.');
                    return;
                }
                setConversationId(json.data.conversation_public_id);
                setMessages((m) => [...m, { role: 'assistant', content: json.data.reply }]);
            } catch {
                setTyping(false);
                setError('Network error.');
            }
        },
        [agentSlug, chatUrl, conversationId, visitorToken]
    );

    const onChatSubmit = (e) => {
        e.preventDefault();
        sendChat(input);
    };

    return (
        <div className="intake-app mx-auto max-w-lg px-4 pb-20 pt-6 sm:pt-12">
            <div
                className={`transition-all duration-500 ease-out ${phase === 'chat' ? 'pointer-events-none absolute inset-x-0 -z-10 opacity-0' : 'opacity-100'}`}
                aria-hidden={phase === 'chat'}
            >
                <p className="font-display text-center text-sm font-medium tracking-wide text-emerald-900/70">
                    Welcome
                </p>
                <h1 className="font-display mt-3 text-center text-3xl font-semibold leading-tight tracking-tight text-stone-900 sm:text-4xl">
                    Tell us about your needs
                </h1>
                <p className="mx-auto mt-4 max-w-md text-center text-base leading-relaxed text-stone-600">
                    Share a few details, and our smart assistant will guide you to the right solution.
                </p>
                <p className="mx-auto mt-3 max-w-md text-center text-sm leading-relaxed text-stone-500">
                    Your information stays private and is only used to help us serve you better.
                </p>

                <form
                    onSubmit={submitForm}
                    className="mt-10 rounded-3xl border border-stone-200/80 bg-white/90 p-6 shadow-[0_20px_50px_-24px_rgba(15,23,42,0.25)] backdrop-blur-sm sm:p-8"
                >
                    <div className="space-y-5">
                        <label className="block">
                            <span className="mb-1.5 block text-sm font-medium text-stone-700">Full name</span>
                            <input
                                type="text"
                                name="name"
                                required
                                autoComplete="name"
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                                className="w-full rounded-2xl border border-stone-200 bg-stone-50/50 px-4 py-3 text-stone-900 outline-none ring-emerald-900/10 transition placeholder:text-stone-400 focus:border-emerald-800/25 focus:bg-white focus:ring-4"
                                placeholder="Alex Morgan"
                            />
                        </label>
                        <label className="block">
                            <span className="mb-1.5 block text-sm font-medium text-stone-700">Email</span>
                            <input
                                type="email"
                                name="email"
                                required
                                autoComplete="email"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                className="w-full rounded-2xl border border-stone-200 bg-stone-50/50 px-4 py-3 text-stone-900 outline-none ring-emerald-900/10 transition placeholder:text-stone-400 focus:border-emerald-800/25 focus:bg-white focus:ring-4"
                                placeholder="you@company.com"
                            />
                        </label>
                        <label className="block">
                            <span className="mb-1.5 block text-sm font-medium text-stone-700">
                                Phone <span className="font-normal text-stone-400">(optional)</span>
                            </span>
                            <input
                                type="tel"
                                name="phone"
                                autoComplete="tel"
                                value={phone}
                                onChange={(e) => setPhone(e.target.value)}
                                className="w-full rounded-2xl border border-stone-200 bg-stone-50/50 px-4 py-3 text-stone-900 outline-none ring-emerald-900/10 transition placeholder:text-stone-400 focus:border-emerald-800/25 focus:bg-white focus:ring-4"
                                placeholder="+1 …"
                            />
                        </label>
                    </div>
                    <div className="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
                        <label>
                            Address line 2
                            <input
                                type="text"
                                name="address_line_2"
                                tabIndex={-1}
                                autoComplete="off"
                                value={honeypot}
                                onChange={(e) => setHoneypot(e.target.value)}
                            />
                        </label>
                    </div>
                    {captchaDriver === 'turnstile' && turnstileSiteKey ? (
                        <div
                            id="intake-form-turnstile"
                            className="cf-turnstile mt-6 flex justify-center"
                            data-sitekey={turnstileSiteKey}
                            data-theme="light"
                        />
                    ) : null}
                    {error && phase === 'form' ? (
                        <p className="mt-4 text-sm text-red-600" role="alert">
                            {error}
                        </p>
                    ) : null}
                    <button
                        type="submit"
                        className="mt-8 w-full rounded-2xl bg-stone-900 py-3.5 text-sm font-semibold text-white shadow-md transition hover:bg-stone-800 focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-900/20"
                    >
                        Continue
                    </button>
                </form>
            </div>

            {phase === 'loading' ? (
                <div className="flex min-h-[40vh] flex-col items-center justify-center gap-4 py-16 opacity-100 transition-opacity duration-500">
                    <div
                        className="h-10 w-10 rounded-full border-2 border-stone-200 border-t-emerald-800 animate-spin"
                        aria-hidden="true"
                    />
                    <p className="text-center text-sm text-stone-600">Connecting you with our assistant…</p>
                </div>
            ) : null}

            {phase === 'chat' ? (
                <div className="opacity-100 transition-all duration-500">
                    <div className="mb-6 rounded-3xl border border-stone-200/80 bg-white/95 p-5 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.3)] backdrop-blur-sm">
                        <div className="flex items-start gap-3">
                            <div className="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-100 to-stone-100 text-lg">
                                ✦
                            </div>
                            <div>
                                <p className="font-display text-lg font-semibold text-stone-900">Artixcore assistant</p>
                                <p className="text-xs font-medium text-emerald-800/80">Online now · Typically replies instantly</p>
                            </div>
                        </div>
                    </div>

                    <div
                        ref={listRef}
                        className="max-h-[min(52vh,28rem)] space-y-4 overflow-y-auto rounded-3xl border border-stone-200/60 bg-white/60 p-4 shadow-inner sm:p-5"
                    >
                        {messages.map((m, i) => (
                            <div
                                key={`${i}-${m.role}`}
                                className={`flex ${m.role === 'user' ? 'justify-end' : 'justify-start'}`}
                            >
                                <div
                                    className={
                                        m.role === 'user'
                                            ? 'max-w-[85%] rounded-2xl rounded-br-md bg-stone-900 px-4 py-3 text-sm leading-relaxed text-white shadow-sm'
                                            : 'max-w-[90%] rounded-2xl rounded-bl-md border border-stone-100 bg-stone-50/90 px-4 py-3 text-sm leading-relaxed text-stone-800 shadow-sm'
                                    }
                                >
                                    {m.content}
                                </div>
                            </div>
                        ))}
                        {typing ? (
                            <div className="flex justify-start">
                                <div className="flex gap-1.5 rounded-2xl border border-stone-100 bg-stone-50 px-4 py-3">
                                    <span className="size-2 animate-bounce rounded-full bg-stone-400 [animation-delay:0ms]" />
                                    <span className="size-2 animate-bounce rounded-full bg-stone-400 [animation-delay:150ms]" />
                                    <span className="size-2 animate-bounce rounded-full bg-stone-400 [animation-delay:300ms]" />
                                </div>
                            </div>
                        ) : null}
                    </div>

                    <div className="mt-4 flex flex-wrap gap-2">
                        {QUICK_REPLIES.map((q) => (
                            <button
                                key={q}
                                type="button"
                                onClick={() => sendChat(q)}
                                className="rounded-full border border-stone-200 bg-white px-3 py-1.5 text-left text-xs font-medium text-stone-700 transition hover:border-emerald-900/20 hover:bg-stone-50"
                            >
                                {q}
                            </button>
                        ))}
                    </div>

                    {error && phase === 'chat' ? (
                        <p className="mt-3 text-center text-sm text-red-600" role="alert">
                            {error}
                        </p>
                    ) : null}

                    <form onSubmit={onChatSubmit} className="mt-5 flex gap-2">
                        <label className="sr-only" htmlFor="intake-chat-input">
                            Your message
                        </label>
                        <textarea
                            id="intake-chat-input"
                            rows={2}
                            value={input}
                            onChange={(e) => setInput(e.target.value)}
                            onKeyDown={(e) => {
                                if (e.key === 'Enter' && !e.shiftKey) {
                                    e.preventDefault();
                                    sendChat(input);
                                }
                            }}
                            className="min-h-[3rem] flex-1 resize-none rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm text-stone-900 outline-none ring-emerald-900/10 transition focus:ring-4"
                            placeholder="Type your message…"
                        />
                        <button
                            type="submit"
                            className="self-end rounded-2xl bg-stone-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-stone-800"
                        >
                            Send
                        </button>
                    </form>
                </div>
            ) : null}
        </div>
    );
}

const el = document.getElementById('intake-root');
if (el) {
    const storeUrl = el.dataset.storeUrl ?? '';
    const chatUrl = el.dataset.chatUrl ?? '';
    const captchaDriver = el.dataset.captchaDriver ?? 'turnstile';
    const turnstileSiteKey = el.dataset.turnstileSiteKey ?? '';
    createRoot(el).render(
        <IntakeApp
            storeUrl={storeUrl}
            chatUrl={chatUrl}
            captchaDriver={captchaDriver}
            turnstileSiteKey={turnstileSiteKey}
        />
    );
}
