const STORAGE_KEY = 'admin-sidebar-collapsed';
const MOBILE_QUERY = '(max-width: 767px)';

function isMobile() {
    return window.matchMedia(MOBILE_QUERY).matches;
}

function initSidebar() {
    const root = document.documentElement;
    const sidebar = document.getElementById('admin-sidebar');
    const backdrop = document.getElementById('admin-sidebar-backdrop');
    const toggleDesktop = document.getElementById('admin-sidebar-toggle-desktop');
    const toggleMobile = document.getElementById('admin-sidebar-toggle-mobile');

    if (!sidebar) return;

    const setCollapsed = (collapsed) => {
        if (isMobile()) return;
        root.classList.toggle('admin-sidebar-collapsed', collapsed);
        try {
            localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
        } catch (_) {}
    };

    const openMobile = () => {
        sidebar.classList.remove('-translate-x-full');
        sidebar.setAttribute('data-mobile-open', 'true');
        backdrop?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden', 'md:overflow-auto');
    };

    const closeMobile = () => {
        sidebar.classList.add('-translate-x-full');
        sidebar.removeAttribute('data-mobile-open');
        backdrop?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden', 'md:overflow-auto');
    };

    if (!isMobile()) {
        try {
            setCollapsed(localStorage.getItem(STORAGE_KEY) === '1');
        } catch (_) {}
    } else {
        sidebar.classList.add('-translate-x-full');
    }

    toggleDesktop?.addEventListener('click', () => {
        const next = !root.classList.contains('admin-sidebar-collapsed');
        setCollapsed(next);
    });

    toggleMobile?.addEventListener('click', () => {
        if (sidebar.getAttribute('data-mobile-open') === 'true') {
            closeMobile();
        } else {
            openMobile();
        }
    });

    backdrop?.addEventListener('click', closeMobile);

    window.addEventListener('resize', () => {
        if (!isMobile()) {
            closeMobile();
            try {
                setCollapsed(localStorage.getItem(STORAGE_KEY) === '1');
            } catch (_) {}
        } else {
            document.documentElement.classList.remove('admin-sidebar-collapsed');
            sidebar.classList.add('-translate-x-full');
            backdrop?.classList.add('hidden');
            document.body.classList.remove('overflow-hidden', 'md:overflow-auto');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.getAttribute('data-mobile-open') === 'true') {
            closeMobile();
        }
    });
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function showToast(message, type = 'success') {
    const el = document.getElementById('admin-toast');
    const body = document.getElementById('admin-toast-body');
    if (!el || !body) return;
    body.textContent = message;
    el.classList.remove('hidden', 'admin-toast--success', 'admin-toast--error');
    el.classList.add(type === 'error' ? 'admin-toast--error' : 'admin-toast--success');
    clearTimeout(el._hideTimer);
    el._hideTimer = setTimeout(() => {
        el.classList.add('hidden');
    }, 4000);
}

function initAdminDelete() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-admin-delete]');
        if (!btn) return;
        e.preventDefault();
        if (!confirm('Delete this record?')) return;
        const url = btn.getAttribute('data-admin-delete');
        try {
            const body = new URLSearchParams();
            body.set('_method', 'DELETE');
            body.set('_token', getCsrfToken());
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body,
            });
            const data = res.ok ? await res.json().catch(() => ({})) : null;
            if (res.ok) {
                showToast(data?.message || 'Deleted.', 'success');
                btn.closest('[data-admin-row]')?.remove();
            } else {
                showToast('Could not delete.', 'error');
            }
        } catch {
            showToast('Could not delete.', 'error');
        }
    });
}

window.adminToast = function (message, type) {
    showToast(message, type === 'error' ? 'error' : 'success');
};

function initAdminAjaxForms() {
    document.querySelectorAll('form[data-admin-ajax-form]').forEach((form) => {
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
                    const msg = data.message || 'Validation failed.';
                    showToast(msg, 'error');
                    return;
                }
                if (!res.ok) {
                    showToast(data.message || 'Request failed.', 'error');
                    return;
                }
                showToast(data.message || 'Saved.', 'success');
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            } catch {
                showToast('Network error.', 'error');
            } finally {
                if (btn) btn.disabled = false;
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initAdminDelete();
    initAdminAjaxForms();
});
