function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function showFieldErrors(form, errors) {
    form.querySelectorAll('[data-field-error]').forEach((el) => {
        el.textContent = '';
        el.classList.add('hidden');
    });
    if (!errors || typeof errors !== 'object') return;
    for (const [key, msgs] of Object.entries(errors)) {
        const el = form.querySelector(`[data-field-error="${key}"]`);
        if (el && Array.isArray(msgs) && msgs[0]) {
            el.textContent = msgs[0];
            el.classList.remove('hidden');
        }
    }
}

function bindAuthForm(form) {
    if (!form) return;
    form.addEventListener('submit', async (e) => {
        if (form.getAttribute('data-auth-ajax') !== '1') return;
        e.preventDefault();
        if (form.dataset.authInFlight === '1') {
            return;
        }
        form.dataset.authInFlight = '1';
        const btn = form.querySelector('[type="submit"]');
        if (btn) btn.disabled = true;
        showFieldErrors(form, {});
        const banner = form.querySelector('[data-auth-banner]');
        if (banner) {
            banner.textContent = '';
            banner.classList.add('hidden');
        }
        try {
            const fd = new FormData(form);
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: fd,
            });
            const data = await res.json().catch(() => ({}));
            if (res.status === 422) {
                showFieldErrors(form, data.errors || {});
                if (banner && data.message) {
                    banner.textContent = data.message;
                    banner.classList.remove('hidden');
                }
                return;
            }
            if (!res.ok) {
                if (banner) {
                    const fallback =
                        res.status >= 500
                            ? 'Something went wrong. Please try again.'
                            : 'Something went wrong.';
                    banner.textContent = data.message || fallback;
                    banner.classList.remove('hidden');
                }
                return;
            }
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } catch {
            if (banner) {
                banner.textContent = 'Network error. Try again.';
                banner.classList.remove('hidden');
            }
        } finally {
            form.dataset.authInFlight = '0';
            if (btn) btn.disabled = false;
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.__artixcoreAuthInitialized) {
        return;
    }
    window.__artixcoreAuthInitialized = true;
    document.querySelectorAll('form[data-auth-ajax="1"]').forEach((form) => bindAuthForm(form));
});
