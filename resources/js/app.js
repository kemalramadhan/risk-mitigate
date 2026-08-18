// Bootstrap 5 JS (aktifkan tab, dropdown, collapse, alert, dsb. via data-bs-*)
import 'bootstrap';

// Toggle visibilitas password via tombol ikon mata (delegasi, berlaku global).
document.addEventListener('click', (e) => {
    const btn = e.target.closest('.pw-toggle');
    if (!btn) return;
    const input = document.getElementById(btn.dataset.pwTarget);
    if (!input) return;
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.classList.toggle('is-shown', show);
    btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
});
