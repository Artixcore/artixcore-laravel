/**
 * Shared CSRF and optional page-level alerts (no external deps).
 */
export function getCsrfTokenFromMeta() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

export function announceToScreenReader(message, politeness = 'assertive') {
    if (!message) return;
    let el = document.getElementById('artixcore-ajax-live-region');
    if (!el) {
        el = document.createElement('div');
        el.id = 'artixcore-ajax-live-region';
        el.className = 'sr-only';
        el.setAttribute('aria-atomic', 'true');
        document.body.appendChild(el);
    }
    el.setAttribute('aria-live', politeness);
    el.textContent = '';
    // eslint-disable-next-line no-unused-expressions
    el.offsetHeight;
    el.textContent = message;
}
