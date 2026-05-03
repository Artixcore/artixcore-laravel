function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function masterToast(message, type) {
    if (typeof window.adminToast === 'function') {
        window.adminToast(message, type);
    }
}

function initMasterIpForms() {
    document.querySelectorAll('form[data-master-ip-form]').forEach((form) => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('[type="submit"]');
            if (btn) btn.disabled = true;
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
                    masterToast(data.message || 'Validation failed.', 'error');
                    return;
                }
                if (!res.ok) {
                    masterToast(data.message || 'Request failed.', 'error');
                    return;
                }
                masterToast(data.message || 'Saved.', 'success');
                window.location.reload();
            } catch {
                masterToast('Network error.', 'error');
            } finally {
                if (btn) btn.disabled = false;
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initMasterIpForms();
});
