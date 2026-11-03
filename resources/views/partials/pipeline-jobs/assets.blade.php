@push('styles')
<style>
.lbs-th[data-sort="asc"] .lbs-sort-icon { font-size: 0; }
.lbs-th[data-sort="asc"] .lbs-sort-icon::before { content: '↑'; font-size: 0.75rem; }
.lbs-th[data-sort="desc"] .lbs-sort-icon { font-size: 0; }
.lbs-th[data-sort="desc"] .lbs-sort-icon::before { content: '↓'; font-size: 0.75rem; }
.lbs-th:not([data-sort=""]) .lbs-sort-icon { opacity: 1; }
.lbs-badge-allocated { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
.lbs-badge-completed { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
.lbs-badge-pending { background: rgba(234, 179, 8, 0.2); color: #eab308; }
.lbs-badge-accepted { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
.lbs-badge-awaiting-further-information { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.lbs-badge-cancelled { background: rgba(100, 116, 139, 0.3); color: #64748b; }
.lbs-badge-for-review { background: rgba(168, 85, 247, 0.2); color: #a855f7; }
.lbs-badge-processing { background: rgba(14, 165, 233, 0.2); color: #0ea5e9; }
.lbs-badge-for-checking { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.lbs-badge-revised { background: rgba(100, 116, 139, 0.2); color: #94a3b8; }
.lbs-status-menu[hidden], .lbs-initials-menu[hidden] { display: none !important; }
</style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/lbs-list.js') }}?v={{ @filemtime(public_path('js/lbs-list.js')) ?: '1' }}"></script>
@endpush
