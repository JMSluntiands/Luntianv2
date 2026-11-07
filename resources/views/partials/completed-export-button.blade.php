{{--
    Export to Excel for completed job tables (#lbsTable).
    Rows need data-export-date, data-export-address, data-export-client, data-export-job-type.
    Only rows matching the current search/filters are exported.
--}}
<button type="button"
        data-completed-export="{{ $exportName ?? 'completed-jobs' }}"
        class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-green-600 bg-green-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-600/30 dark:border-green-700 dark:bg-green-700 dark:hover:bg-green-600">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
    Export to Excel
</button>

@once
@push('scripts')
<script>
(function () {
    function esc(v) {
        return String(v == null ? '' : v)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function pad(n) { return n < 10 ? '0' + n : String(n); }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-completed-export]');
        if (!btn) return;
        var table = document.getElementById('lbsTable');
        if (!table) return;

        var rows = Array.prototype.filter.call(
            table.querySelectorAll('tbody tr.lbs-data-row'),
            function (tr) { return tr.getAttribute('data-job-filter-match') !== '0'; }
        );
        if (!rows.length) {
            if (window.showSuccessToast) window.showSuccessToast('No jobs to export.');
            else window.alert('No jobs to export.');
            return;
        }

        var html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">'
            + '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body><table border="1"><thead><tr>';
        ['Date', 'Address', 'Client', 'Job Type Request'].forEach(function (h) {
            html += '<th>' + esc(h) + '</th>';
        });
        html += '</tr></thead><tbody>';
        rows.forEach(function (tr) {
            html += '<tr>'
                + '<td>' + esc(tr.getAttribute('data-export-date')) + '</td>'
                + '<td>' + esc(tr.getAttribute('data-export-address')) + '</td>'
                + '<td>' + esc(tr.getAttribute('data-export-client')) + '</td>'
                + '<td>' + esc(tr.getAttribute('data-export-job-type')) + '</td>'
                + '</tr>';
        });
        html += '</tbody></table></body></html>';

        var now = new Date();
        var stamp = now.getFullYear() + pad(now.getMonth() + 1) + pad(now.getDate()) + '-' + pad(now.getHours()) + pad(now.getMinutes()) + pad(now.getSeconds());
        var blob = new Blob(['\ufeff', html], { type: 'application/vnd.ms-excel;charset=utf-8' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = (btn.getAttribute('data-completed-export') || 'completed-jobs') + '-' + stamp + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
    });
})();
</script>
@endpush
@endonce
