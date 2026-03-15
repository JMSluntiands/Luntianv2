@if(session('success'))
<div class="toast-container" id="toastContainer" role="status" aria-live="polite">
    <div class="toast toast-success" id="appToast">
        <span class="toast-icon">✓</span>
        <span class="toast-message">{{ session('success') }}</span>
        <button type="button" class="toast-close" aria-label="Close">&times;</button>
    </div>
</div>
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
