{{-- App toasts: success (green) + error (red). Session flash uses success style. --}}
<script>
(function() {
    function showToast(message, variant) {
        variant = variant || 'success';
        var isError = variant === 'error';
        var id = isError ? 'appErrorToast' : 'appSuccessToast';
        var prev = document.getElementById(id);
        if (prev) prev.remove();
        var el = document.createElement('div');
        el.id = id;
        el.setAttribute('role', isError ? 'alert' : 'status');
        el.setAttribute('aria-live', isError ? 'assertive' : 'polite');
        el.className = isError ? 'app-toast-error' : 'app-toast-success';
        var icon = isError ? '!' : '✓';
        el.innerHTML = '<span class="app-toast-icon" aria-hidden="true">' + icon + '</span><span class="app-toast-msg"></span><button type="button" class="app-toast-close" aria-label="Close">&times;</button>';
        el.querySelector('.app-toast-msg').textContent = message || (isError ? 'Something went wrong.' : 'Saved successfully.');
        document.body.appendChild(el);
        var hide = function() {
            el.classList.add('app-toast-exit');
            setTimeout(function() { el.remove(); }, 280);
        };
        el.querySelector('.app-toast-close').addEventListener('click', hide);
        setTimeout(hide, isError ? 7000 : 4500);
    }
    window.showSuccessToast = function(message) { showToast(message, 'success'); };
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
