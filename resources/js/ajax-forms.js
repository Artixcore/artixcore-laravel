import { announceToScreenReader, getCsrfTokenFromMeta } from './alerts.js';

const JSON_HEADERS = {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
};

function clearFieldErrors(form) {
    form.querySelectorAll('[data-error-for]').forEach((el) => {
        el.textContent = '';
    });
    form.querySelectorAll('[data-field-error]').forEach((el) => {
        el.textContent = '';
        el.classList.add('hidden');
    });
    form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
}

function showFieldErrors(form, errors) {
    if (!errors || typeof errors !== 'object') return;
    Object.keys(errors).forEach((field) => {
        const msgs = errors[field];
        const msg = Array.isArray(msgs) ? msgs[0] : String(msgs);
        const el =
            form.querySelector(`[data-error-for="${field}"]`) ??
            form.querySelector(`[data-field-error="${field}"]`);
        if (el && msg) {
            el.textContent = msg;
            el.classList.remove('hidden', 'd-none');
        }
        const input = form.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');
        }
    });

    const firstInvalid = form.querySelector('.is-invalid');
    if (firstInvalid && typeof firstInvalid.scrollIntoView === 'function') {
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    if (firstInvalid && typeof firstInvalid.focus === 'function') {
        try {
            firstInvalid.focus();
        } catch (_) {
            /* ignore */
        }
    }
}

function hasBanner(form) {
    return !!(form.querySelector('[data-ajax-banner]') || form.querySelector('[data-auth-banner]'));
}

function showBanner(form, message) {
    const banner =
        form.querySelector('[data-ajax-banner]') ?? form.querySelector('[data-auth-banner]');
    if (!banner || !message) return;
    banner.textContent = message;
    banner.classList.remove('hidden', 'd-none');
    announceToScreenReader(message);
}

function clearBanners(form) {
    form.querySelectorAll('[data-ajax-banner], [data-auth-banner]').forEach((el) => {
        el.textContent = '';
        el.classList.add('hidden', 'd-none');
    });
}

function notifyUser(form, message, type) {
    if (!message) return;
    if (hasBanner(form)) {
        showBanner(form, message);
        return;
    }
    if (typeof window.adminToast === 'function') {
        window.adminToast(message, type === 'error' ? 'error' : 'success');
        announceToScreenReader(message, type === 'error' ? 'assertive' : 'polite');
    } else {
        showBanner(form, message);
    }
}

/**
 * @param {HTMLFormElement} form
 */
export function bindAjaxForm(form) {
    if (!form || form.dataset.ajaxFormBound === '1') return;
    form.dataset.ajaxFormBound = '1';

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (form.dataset.ajaxInFlight === '1') return;
        form.dataset.ajaxInFlight = '1';

        const btn = form.querySelector('[type="submit"]');
        if (btn) btn.disabled = true;

        clearFieldErrors(form);
        clearBanners(form);

        try {
            const fd = new FormData(form);
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    ...JSON_HEADERS,
                    'X-CSRF-TOKEN': getCsrfTokenFromMeta(),
                },
                body: fd,
                credentials: 'same-origin',
            });

            const data = await res.json().catch(() => ({}));

            if (res.status === 422) {
                showFieldErrors(form, data.errors || {});
                notifyUser(form, data.message || 'Please check the form and try again.', 'error');
                return;
            }

            if (res.status === 401 || res.status === 403) {
                notifyUser(
                    form,
                    data.message || 'You are not allowed to perform this action.',
                    'error',
                );
                return;
            }

            if (res.status === 404) {
                notifyUser(form, data.message || 'Not found.', 'error');
                return;
            }

            if (res.status === 429) {
                notifyUser(
                    form,
                    data.message || 'Too many attempts. Please wait and try again.',
                    'error',
                );
                return;
            }

            if (!res.ok || data.ok === false) {
                const fallback =
                    res.status >= 500
                        ? 'Something went wrong. Please try again.'
                        : 'Something went wrong.';
                notifyUser(form, data.message || fallback, 'error');
                return;
            }

            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }

            const okMsg = data.message || 'Saved successfully.';
            notifyUser(form, okMsg, 'success');
            form.dispatchEvent(new CustomEvent('artixcore:ajax-form-success', { detail: { data, form } }));
        } catch {
            notifyUser(form, 'Network error. Please try again.', 'error');
        } finally {
            form.dataset.ajaxInFlight = '0';
            if (btn) btn.disabled = false;
        }
    });
}

export function initAjaxForms(root = document) {
    root.querySelectorAll('form[data-ajax-form]').forEach((form) => {
        bindAjaxForm(form);
    });
}
