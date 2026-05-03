/**
 * Lead form: AJAX submit. Turnstile uses implicit rendering (.cf-turnstile); do not call turnstile.ready().
 */
document.addEventListener('DOMContentLoaded', function () {
    if (window.__artixcoreLeadInitialized) {
        return;
    }
    window.__artixcoreLeadInitialized = true;

    const form = document.getElementById('lead-form');
    if (!form) {
        return;
    }

    const successEl = document.getElementById('lead-success');
    const generalError = document.getElementById('lead-form-error');
    const submitBtn = document.getElementById('lead-submit');
    const submitLabel = submitBtn?.querySelector('.lead-submit-label');
    const submitSpinner = submitBtn?.querySelector('[data-lead-submit-spinner]');
    const captchaEl =
        document.getElementById('lead-turnstile') ??
        document.getElementById('lead-captcha');

    const fieldFocusOrder = [
        'name',
        'email',
        'phone',
        'service_type',
        'message',
        'cf-turnstile-response',
        'captcha',
    ];

    function clearErrors() {
        form.querySelectorAll('[data-error-for]').forEach((el) => {
            el.textContent = '';
            el.classList.add('d-none');
        });
        form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
        if (generalError) {
            generalError.textContent = '';
            generalError.classList.add('d-none');
        }
    }

    function showFieldErrors(errors) {
        if (!errors || typeof errors !== 'object') {
            return;
        }

        Object.keys(errors).forEach((key) => {
            const messages = errors[key];
            const msg = Array.isArray(messages) ? messages[0] : String(messages);
            const lookupKey =
                key === 'cf-turnstile-response' ? 'captcha' : key;
            const target =
                form.querySelector(`[data-error-for="${key}"]`) ??
                (lookupKey !== key ? form.querySelector(`[data-error-for="${lookupKey}"]`) : null);

            if (target && msg) {
                target.textContent = msg;
                target.classList.remove('d-none');
            }

            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                input.classList.add('is-invalid');
            }
        });
    }

    function focusFirstInvalid(errors) {
        if (!errors || typeof errors !== 'object') {
            return;
        }
        for (const key of fieldFocusOrder) {
            if (!errors[key]) {
                continue;
            }
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                input.focus();
                return;
            }
        }
        const captchaErrorKeys = ['cf-turnstile-response', 'g-recaptcha-response', 'captcha'];
        for (const key of captchaErrorKeys) {
            if (!errors[key]) {
                continue;
            }
            const tokenInput = form.querySelector(
                `input[name="${key}"], textarea[name="${key}"]`
            );
            if (tokenInput && typeof tokenInput.focus === 'function') {
                tokenInput.focus();
                return;
            }
            if (captchaEl) {
                captchaEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            const capLabel = document.getElementById('lead-captcha-label');
            if (capLabel && typeof capLabel.focus === 'function') {
                capLabel.setAttribute('tabindex', '-1');
                capLabel.focus();
            }
            return;
        }
    }

    function resetCaptchaWidget() {
        if (!captchaEl) {
            return;
        }
        if (
            captchaEl.classList.contains('g-recaptcha') &&
            typeof window.grecaptcha !== 'undefined' &&
            typeof window.grecaptcha.reset === 'function'
        ) {
            try {
                window.grecaptcha.reset();
            } catch (_) {
                /* ignore */
            }
            return;
        }
        if (
            typeof window.turnstile === 'undefined' ||
            typeof window.turnstile.reset !== 'function'
        ) {
            return;
        }
        const wid =
            captchaEl.getAttribute('data-cf-widget-id') ||
            captchaEl.getAttribute('cf-turnstile-widget-id') ||
            captchaEl.dataset.cfWidgetId;
        try {
            if (wid) {
                window.turnstile.reset(wid);
            }
        } catch (_) {
            /* ignore */
        }
    }

    function setLoading(loading) {
        if (!submitBtn) {
            return;
        }
        submitBtn.disabled = loading;
        const defaultLabel = submitBtn.getAttribute('data-label-default') ?? 'Submit Project Request';
        if (submitLabel) {
            submitLabel.textContent = loading ? 'Sending…' : defaultLabel;
        }
        if (submitSpinner) {
            submitSpinner.classList.toggle('d-none', !loading);
        }
    }

    function csrfHeader() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        const fromMeta = meta?.getAttribute('content');
        if (fromMeta) {
            return { 'X-CSRF-TOKEN': fromMeta };
        }
        const input = form.querySelector('input[name="_token"]');
        const fromInput = input?.getAttribute('value');
        if (fromInput) {
            return { 'X-CSRF-TOKEN': fromInput };
        }
        return {};
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        setLoading(true);

        const tokenInput = form.querySelector(
            'input[name="cf-turnstile-response"], textarea[name="cf-turnstile-response"], input[name="g-recaptcha-response"]'
        );
        if (
            captchaEl &&
            tokenInput &&
            (!tokenInput.value || tokenInput.value.trim() === '')
        ) {
            const capErr = form.querySelector('[data-error-for="captcha"]');
            if (capErr) {
                capErr.textContent =
                    'Please complete the verification challenge before submitting.';
                capErr.classList.remove('d-none');
            }
            setLoading(false);
            return;
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...csrfHeader(),
                },
                body: new FormData(form),
                credentials: 'same-origin',
            });

            const contentType = response.headers.get('content-type') ?? '';
            let data = null;
            if (contentType.includes('application/json')) {
                try {
                    data = await response.json();
                } catch (_) {
                    data = null;
                }
            }

            if (response.ok && data && data.ok === true) {
                setLoading(false);
                form.classList.add('d-none');
                if (successEl) {
                    const nameSpan = successEl.querySelector('[data-lead-success-name]');
                    const emailSpan = successEl.querySelector('[data-lead-success-email]');
                    if (nameSpan && data.lead) {
                        nameSpan.textContent = data.lead.name ?? '';
                    }
                    if (emailSpan && data.lead) {
                        emailSpan.textContent = data.lead.email ?? '';
                    }
                    successEl.removeAttribute('hidden');
                    successEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                return;
            }

            if (response.status === 422 && data && data.errors) {
                showFieldErrors(data.errors);
                focusFirstInvalid(data.errors);
                resetCaptchaWidget();
                setLoading(false);
                return;
            }

            if (response.status === 429) {
                if (generalError) {
                    generalError.textContent =
                        (data && typeof data.message === 'string' && data.message) ||
                        'Too many submissions. Please wait a minute and try again.';
                    generalError.classList.remove('d-none');
                }
                resetCaptchaWidget();
                setLoading(false);
                return;
            }

            if (response.status === 419) {
                if (generalError) {
                    generalError.textContent =
                        'Your session expired. Please refresh the page and try again.';
                    generalError.classList.remove('d-none');
                }
                resetCaptchaWidget();
                setLoading(false);
                return;
            }

            const msg =
                (data && typeof data.message === 'string' && data.message) ||
                'Something went wrong. Please try again in a moment.';
            if (generalError) {
                generalError.textContent = msg;
                generalError.classList.remove('d-none');
            }
            resetCaptchaWidget();
        } catch (_) {
            if (generalError) {
                generalError.textContent =
                    'We could not reach the server. Check your connection and try again.';
                generalError.classList.remove('d-none');
            }
            resetCaptchaWidget();
        }

        setLoading(false);
    });
});
