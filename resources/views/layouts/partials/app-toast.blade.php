{{-- Shared success toast: green strip, checkmark, close. Use window.showSuccessToast(message) from any edit/save handler. --}}
<style>
    .app-toast-success {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 99999;
        max-width: 90vw;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1.25rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        animation: app-toast-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        background: #0f172a;
        border: 1px solid #334155;
        border-left: 4px solid #22c55e;
    }
    .app-toast-success .app-toast-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(34, 197, 94, 0.25);
        color: #86efac;
        font-size: 1rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .app-toast-success .app-toast-msg { flex: 1; font-size: 0.9375rem; color: #e2e8f0; line-height: 1.4; }
    .app-toast-success .app-toast-close {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        padding: 0;
        border: none;
        background: transparent;
        color: #94a3b8;
        font-size: 1.25rem;
        line-height: 1;
        cursor: pointer;
        border-radius: 6px;
    }
    .app-toast-success .app-toast-close:hover { color: #e2e8f0; background: rgba(255,255,255,0.08); }
    @keyframes app-toast-in {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
    html[data-theme="light"] .app-toast-success { background: #fff; border-color: #e2e8f0; border-left-color: #22c55e; box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
    html[data-theme="light"] .app-toast-success .app-toast-msg { color: #1e293b; }
</style>
<script>
(function() {
    window.showSuccessToast = function(message) {
        var prev = document.getElementById('appSuccessToast');
        if (prev) prev.remove();
        var el = document.createElement('div');
        el.id = 'appSuccessToast';
        el.className = 'app-toast-success';
        el.innerHTML = '<span class="app-toast-icon">✓</span><span class="app-toast-msg"></span><button type="button" class="app-toast-close" aria-label="Close">&times;</button>';
        el.querySelector('.app-toast-msg').textContent = message || 'Updated successfully.';
        document.body.appendChild(el);
        var hide = function() {
            el.style.animation = 'app-toast-in 0.3s ease reverse';
            setTimeout(function() { el.remove(); }, 300);
        };
        el.querySelector('.app-toast-close').addEventListener('click', hide);
        setTimeout(hide, 4500);
    };
})();
</script>
