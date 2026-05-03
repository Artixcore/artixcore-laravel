@php
	$slug = config('ai.widget_agent_slug');
	$turnstileSiteKey = (string) (config('services.turnstile.site_key') ?: config('captcha.turnstile.site_key', ''));
@endphp
@if (is_string($slug) && $slug !== '' && config('ai.chat_enabled', true))
	<div
		id="ai-chat-widget-root"
		class="fixed bottom-4 right-4 z-[100] flex flex-col items-end gap-2"
		data-agent-slug="{{ $slug }}"
		data-chat-url="{{ url('/api/v1/ai/chat') }}"
		data-profile-url="{{ url('/api/v1/ai/agents/'.$slug.'/profile') }}"
		data-turnstile-site-key="{{ $turnstileSiteKey }}"
	>
		<div
			id="ai-chat-panel"
			class="hidden w-[min(100vw-2rem,22rem)] rounded-2xl border border-zinc-200 bg-white shadow-xl"
			role="dialog"
			aria-label="Chat"
		>
			<div class="flex items-center justify-between border-b border-zinc-100 px-4 py-3">
				<p id="ai-chat-title" class="text-sm font-semibold text-zinc-900">Chat</p>
				<button type="button" id="ai-chat-close" class="rounded-lg p-1 text-zinc-500 hover:bg-zinc-100" aria-label="Close">&times;</button>
			</div>
			<div id="ai-chat-messages" class="max-h-72 space-y-3 overflow-y-auto px-4 py-3 text-sm"></div>
			@if (config('captcha.driver') === 'turnstile' && $turnstileSiteKey !== '')
				<div
					id="ai-chat-turnstile"
					class="cf-turnstile border-t border-zinc-100 px-4 py-3"
					data-sitekey="{{ $turnstileSiteKey }}"
					data-theme="light"
				></div>
			@endif
			<form id="ai-chat-form" class="border-t border-zinc-100 p-3">
				<label class="sr-only" for="ai-chat-input">Message</label>
				<textarea
					id="ai-chat-input"
					name="message"
					rows="2"
					class="mb-2 w-full resize-none rounded-xl border border-zinc-200 px-3 py-2 text-sm"
					placeholder="Type a message…"
					required
				></textarea>
				<button type="submit" class="w-full rounded-xl bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500">
					Send
				</button>
			</form>
		</div>
		<button
			type="button"
			id="ai-chat-toggle"
			class="flex size-14 items-center justify-center rounded-full bg-indigo-600 text-2xl text-white shadow-lg hover:bg-indigo-500"
			aria-expanded="false"
			aria-controls="ai-chat-panel"
		>
			💬
		</button>
	</div>
	<script>
		(function () {
			var root = document.getElementById('ai-chat-widget-root');
			if (!root) return;
			var slug = root.dataset.agentSlug;
			var chatUrl = root.dataset.chatUrl;
			var profileUrl = root.dataset.profileUrl;
			var tokenKey = 'ai_visitor_token_v1';
			function visitorToken() {
				try {
					var t = sessionStorage.getItem(tokenKey);
					if (t && t.length >= 16) return t;
					var a = new Uint8Array(24);
					crypto.getRandomValues(a);
					t = Array.from(a, function (b) {
						return b.toString(16).padStart(2, '0');
					}).join('');
					sessionStorage.setItem(tokenKey, t);
					return t;
				} catch (e) {
					return 'fallbacktoken123456789012';
				}
			}
			var convId = null;
			var panel = document.getElementById('ai-chat-panel');
			var toggle = document.getElementById('ai-chat-toggle');
			var closeBtn = document.getElementById('ai-chat-close');
			var form = document.getElementById('ai-chat-form');
			var input = document.getElementById('ai-chat-input');
			var msgs = document.getElementById('ai-chat-messages');
			var title = document.getElementById('ai-chat-title');
			fetch(profileUrl, { headers: { Accept: 'application/json' } })
				.then(function (r) {
					return r.json();
				})
				.then(function (j) {
					if (j && j.data && j.data.name) title.textContent = j.data.name;
				})
				.catch(function () {});
			function append(role, text) {
				var d = document.createElement('div');
				d.className = role === 'user' ? 'ml-6 rounded-lg bg-indigo-50 px-3 py-2 text-zinc-900' : 'mr-6 rounded-lg bg-zinc-100 px-3 py-2 text-zinc-800';
				d.textContent = text;
				msgs.appendChild(d);
				msgs.scrollTop = msgs.scrollHeight;
			}
			toggle.addEventListener('click', function () {
				var open = !panel.classList.contains('hidden');
				panel.classList.toggle('hidden', open);
				toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
			});
			closeBtn.addEventListener('click', function () {
				panel.classList.add('hidden');
				toggle.setAttribute('aria-expanded', 'false');
			});
			form.addEventListener('submit', function (e) {
				e.preventDefault();
				var text = (input.value || '').trim();
				if (!text) return;
				append('user', text);
				input.value = '';
				var chatPayload = {
					agent_slug: slug,
					message: text,
					conversation_public_id: convId,
					visitor_token: visitorToken(),
				};
				if (!convId) {
					var tsEl = document.querySelector('#ai-chat-widget-root [name="cf-turnstile-response"]');
					if (tsEl && tsEl.value) {
						chatPayload['cf-turnstile-response'] = tsEl.value;
					}
				}
				fetch(chatUrl, {
					method: 'POST',
					headers: {
						Accept: 'application/json',
						'Content-Type': 'application/json',
						'X-Requested-With': 'XMLHttpRequest',
					},
					body: JSON.stringify(chatPayload),
				})
					.then(function (r) {
						return r.json().then(function (j) {
							return { ok: r.ok, j: j };
						});
					})
					.then(function (x) {
						if (!x.ok) {
							append('assistant', x.j.message || 'Something went wrong.');
							return;
						}
						convId = x.j.data.conversation_public_id;
						append('assistant', x.j.data.reply);
					})
					.catch(function () {
						append('assistant', 'Network error.');
					});
			});
		})();
	</script>
	@if (config('captcha.driver') === 'turnstile' && $turnstileSiteKey !== '')
		<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
	@endif
@endif
