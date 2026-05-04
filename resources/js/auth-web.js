import { initAjaxForms } from './ajax-forms.js';

document.addEventListener('DOMContentLoaded', () => {
    if (window.__artixcoreAuthInitialized) {
        return;
    }
    window.__artixcoreAuthInitialized = true;
    initAjaxForms(document);
});

document.addEventListener('click', (e) => {
    const b = e.target.closest('[data-toggle-password]');
    if (!b) return;
    const sel = b.getAttribute('data-toggle-password');
    const input = document.querySelector(sel);
    if (!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
    b.textContent = input.type === 'password' ? 'Show' : 'Hide';
});
