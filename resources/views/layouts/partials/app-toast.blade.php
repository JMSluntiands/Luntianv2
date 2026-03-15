{{-- Shared success toast: green strip, checkmark, close. Use window.showSuccessToast(message) from any edit/save handler. --}}
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
