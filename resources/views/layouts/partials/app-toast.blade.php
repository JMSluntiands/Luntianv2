{{-- App toasts: success (green), warning (amber), error (red). --}}
<style>
#appWarningToast,
.app-toast-warning {
    position: fixed;
    top: 1.5rem;
    right: 1.5rem;
    z-index: 99999;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    max-width: 22rem;
    background: #d97706;
    color: #fff;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 1rem;
    box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.2), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    animation: app-toast-in 0.3s ease;
}
.app-toast-warning.app-toast-exit {
    animation: app-toast-out 0.28s ease forwards;
}
</style>
<script>
(function() {
    function showToast(message, variant) {
        variant = variant || 'success';
        var isError = variant === 'error';
        var isWarning = variant === 'warning';
        var id = isError ? 'appErrorToast' : (isWarning ? 'appWarningToast' : 'appSuccessToast');
        var prev = document.getElementById(id);
        if (prev) prev.remove();
        var el = document.createElement('div');
        el.id = id;
        el.setAttribute('role', (isError || isWarning) ? 'alert' : 'status');
        el.setAttribute('aria-live', (isError || isWarning) ? 'assertive' : 'polite');
        el.className = isError ? 'app-toast-error' : (isWarning ? 'app-toast-warning' : 'app-toast-success');
        var icon = isWarning ? '⚠' : (isError ? '!' : '✓');
        el.innerHTML = '<span class="app-toast-icon" aria-hidden="true">' + icon + '</span><span class="app-toast-msg"></span><button type="button" class="app-toast-close" aria-label="Close">&times;</button>';
        el.querySelector('.app-toast-msg').textContent = message || (isWarning ? 'Please check and try again.' : (isError ? 'Something went wrong.' : 'Saved successfully.'));
        document.body.appendChild(el);
        var hide = function() {
            el.classList.add('app-toast-exit');
            setTimeout(function() { el.remove(); }, 280);
        };
        el.querySelector('.app-toast-close').addEventListener('click', hide);
        setTimeout(hide, (isError || isWarning) ? 7000 : 4500);
    }
    window.showSuccessToast = function(message) { showToast(message, 'success'); };
    window.showWarningToast = function(message) { showToast(message, 'warning'); };
    window.showErrorToast = function(message) { showToast(message, 'error'); };
    @if(session('success'))
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { showToast({{ json_encode(session('success')) }}, 'success'); });
    } else {
        showToast({{ json_encode(session('success')) }}, 'success');
    }
    @endif
})();
</script>
