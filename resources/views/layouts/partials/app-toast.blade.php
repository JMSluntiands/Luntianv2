{{-- Single success toast for entire app (session flash + showSuccessToast). Same style as login page toast. --}}
<script>
(function() {
    function showToast(message) {
        var prev = document.getElementById('appSuccessToast');
        if (prev) prev.remove();
        var el = document.createElement('div');
        el.id = 'appSuccessToast';
        el.setAttribute('role', 'status');
        el.setAttribute('aria-live', 'polite');
        el.className = 'app-toast-success';
        el.innerHTML = '<span class="app-toast-icon" aria-hidden="true">✓</span><span class="app-toast-msg"></span><button type="button" class="app-toast-close" aria-label="Close">&times;</button>';
        el.querySelector('.app-toast-msg').textContent = message || 'Saved successfully.';
        document.body.appendChild(el);
        var hide = function() {
            el.classList.add('app-toast-exit');
            setTimeout(function() { el.remove(); }, 280);
        };
        el.querySelector('.app-toast-close').addEventListener('click', hide);
        setTimeout(hide, 4500);
    }
    window.showSuccessToast = showToast;
    @if(session('success'))
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { showToast({{ json_encode(session('success')) }}); });
    } else {
        showToast({{ json_encode(session('success')) }});
    }
    @endif
})();
</script>
