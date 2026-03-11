@if(session('success'))
<div class="toast-container" id="toastContainer" role="status" aria-live="polite">
    <div class="toast toast-success" id="appToast">
        <span class="toast-icon">✓</span>
        <span class="toast-message">{{ session('success') }}</span>
        <button type="button" class="toast-close" aria-label="Close">&times;</button>
    </div>
</div>
<style>
    .toast-container { position: fixed; top: 1rem; right: 1rem; z-index: 9999; max-width: 90vw; }
    .toast { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.25rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.25); animation: toast-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); background: #0f172a; border: 1px solid #334155; }
    .toast-success { border-left: 4px solid #22c55e; }
    .toast-success .toast-icon { display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 50%; background: rgba(34, 197, 94, 0.25); color: #86efac; font-size: 1rem; font-weight: 700; flex-shrink: 0; }
    .toast-message { flex: 1; font-size: 0.9375rem; color: #e2e8f0; line-height: 1.4; }
    .toast-close { flex-shrink: 0; width: 28px; height: 28px; padding: 0; border: none; background: transparent; color: #94a3b8; font-size: 1.25rem; line-height: 1; cursor: pointer; border-radius: 6px; transition: color 0.2s, background 0.2s; display: flex; align-items: center; justify-content: center; }
    .toast-close:hover { color: #e2e8f0; background: rgba(255,255,255,0.08); }
    @keyframes toast-in {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
    .toast.toast-exit { animation: toast-out 0.3s ease forwards; }
    @keyframes toast-out {
        to { opacity: 0; transform: translateX(100%); }
    }
    html[data-theme="light"] .toast { background: #fff; border-color: #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
    html[data-theme="light"] .toast-success .toast-icon { background: rgba(34, 197, 94, 0.2); color: #16a34a; }
    html[data-theme="light"] .toast-message { color: #1e293b; }
    html[data-theme="light"] .toast-close { color: #64748b; }
    html[data-theme="light"] .toast-close:hover { color: #334155; background: #f1f5f9; }
</style>
<script>
(function() {
    var container = document.getElementById('toastContainer');
    if (!container) return;
    var toast = document.getElementById('appToast');
    var hide = function() {
        if (toast) toast.classList.add('toast-exit');
        setTimeout(function() { container.remove(); }, 300);
    };
    setTimeout(hide, 4500);
    toast?.querySelector('.toast-close')?.addEventListener('click', hide);
})();
</script>
@endif
