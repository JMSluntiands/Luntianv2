@push('styles')
<style>
/* Sort icon arrows - JS toggles data-sort */
.lbs-th[data-sort="asc"] .lbs-sort-icon { font-size: 0; }
.lbs-th[data-sort="asc"] .lbs-sort-icon::before { content: '↑'; font-size: 0.75rem; }
.lbs-th[data-sort="desc"] .lbs-sort-icon { font-size: 0; }
.lbs-th[data-sort="desc"] .lbs-sort-icon::before { content: '↓'; font-size: 0.75rem; }
.lbs-th:not([data-sort=""]) .lbs-sort-icon { opacity: 1; }
/* Expand icon rotate when open */
/* Status badge variants (JS applies these on update/revert) */
.lbs-badge-allocated { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
.lbs-badge-completed { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
.lbs-badge-pending { background: rgba(234, 179, 8, 0.2); color: #eab308; }
.lbs-badge-accepted { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
.lbs-badge-awaiting-further-information { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.lbs-badge-for-email-confirmation { background: rgba(99, 102, 241, 0.2); color: #6366f1; }
.lbs-badge-cancelled { background: rgba(100, 116, 139, 0.3); color: #64748b; }
.lbs-badge-for-review { background: rgba(168, 85, 247, 0.2); color: #a855f7; }
.lbs-badge-processing { background: rgba(14, 165, 233, 0.2); color: #0ea5e9; }
.lbs-badge-for-checking { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.lbs-badge-revised { background: rgba(100, 116, 139, 0.2); color: #94a3b8; }
.lbs-status-menu[hidden], .lbs-initials-menu[hidden] { display: none !important; }
.lbs-status-trigger, .lbs-initials-trigger { cursor: pointer; }
.lbs-initials-select {
    display: inline-block;
    max-width: 4.75rem;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    border-radius: 0.375rem;
    border: 1px solid #cbd5e1;
    background-color: #f8fafc;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.3rem center;
    background-size: 12px;
    padding: 0.25rem 1.15rem 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #1e293b;
    line-height: 1.25;
}
.lbs-initials-select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
}
[data-theme="dark"] .lbs-initials-select {
    border-color: #475569;
    background-color: rgba(30, 41, 59, 0.5);
    color: #e2e8f0;
}
.lbs-priority-select,
.lbs-status-select {
    display: inline-block;
    max-width: 11rem;
    min-width: 6.5rem;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    border-radius: 0.375rem;
    border: 1px solid #cbd5e1;
    background-color: #f8fafc;
    color: #1e293b;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 0.35rem center !important;
    background-size: 12px !important;
    padding: 0.25rem 1.2rem 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    line-height: 1.25;
}
.lbs-priority-select:focus,
.lbs-status-select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
}
[data-theme="dark"] .lbs-priority-select,
[data-theme="dark"] .lbs-status-select {
    border-color: #475569;
}
.list-fold-toggle {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    margin: 0;
    padding: 0;
    border: 0;
    background: transparent;
    color: inherit;
    cursor: pointer;
}
.list-fold-chevron {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
    transition: transform 0.15s ease;
}
.list-fold.is-collapsed .list-fold-chevron { transform: rotate(-90deg); }
.list-fold.is-collapsed .list-fold-body { display: none; }
</style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    (function () {
        function applyListFolds(root) {
            (root || document).querySelectorAll('.list-fold').forEach(function (section) {
                var key = section.getAttribute('data-list-fold');
                var collapsed = !!(key && localStorage.getItem('luntian.list-fold:' + key) === '1');
                section.classList.toggle('is-collapsed', collapsed);
                var btn = section.querySelector('.list-fold-toggle');
                if (btn) btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            });
        }
        window.applyListFolds = applyListFolds;
        document.addEventListener('click', function (e) {
            var btn = e.target.closest && e.target.closest('.list-fold-toggle');
            if (!btn) return;
            var section = btn.closest('.list-fold');
            if (!section) return;
            var collapsed = section.classList.toggle('is-collapsed');
            btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            var key = section.getAttribute('data-list-fold');
            if (key) localStorage.setItem('luntian.list-fold:' + key, collapsed ? '1' : '0');
        });
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { applyListFolds(); });
        else applyListFolds();
    })();
    </script>
    <script src="{{ asset('js/lbs-list.js') }}?v={{ @filemtime(public_path('js/lbs-list.js')) ?: '1' }}"></script>
@endpush
