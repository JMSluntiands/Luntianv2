@extends('layouts.dashboard')

@section('title', 'Reports')

@section('body_class', 'page-reports')

@section('content')
    <div class="reports-page">
        <div class="reports-header">
            <h1 class="reports-title">Reports</h1>
            <p class="reports-subtitle">View and export report data by user and completion date.</p>
        </div>

        <div class="reports-summary-card">
            <span class="reports-summary-briefcase" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></span>
            <div class="reports-summary-content">
                <span class="reports-summary-title">Total Jobs</span>
                <span class="reports-summary-total">143</span>
                <div class="reports-summary-sep"></div>
                <ul class="reports-summary-list">
                    <li class="reports-summary-row"><span class="reports-summary-label">LUNTIAN</span><span class="reports-summary-value">10</span></li>
                    <li class="reports-summary-row"><span class="reports-summary-label">LBS</span><span class="reports-summary-value">45</span></li>
                    <li class="reports-summary-row"><span class="reports-summary-label">B1</span><span class="reports-summary-value">32</span></li>
                    <li class="reports-summary-row"><span class="reports-summary-label">BLUINQ</span><span class="reports-summary-value">56</span></li>
                </ul>
            </div>
        </div>

        <div class="reports-filter-card">
            <h2 class="reports-section-title">Report Filter</h2>
            <div class="reports-filter-row">
                <div class="reports-filter-group reports-filter-group-client">
                    <label for="reportsClient" class="reports-filter-label">Client</label>
                    <select id="reportsClient" name="client" class="reports-filter-select select2-single" aria-label="Client filter">
                        <option value="">Select client</option>
                        <option value="all" selected>ALL</option>
                        <option value="shg">Standard Humans Group</option>
                        <option value="acme">Acme Corp</option>
                        <option value="leigh">Leigh Homes</option>
                        <option value="demo">Demo Client</option>
                    </select>
                </div>
                <div class="reports-filter-group">
                    <label for="reportsDateFrom" class="reports-filter-label">Date From (Completion Date)</label>
                    <div class="reports-filter-date-wrap">
                        <input type="text" id="reportsDateFrom" class="reports-filter-input" value="08/03/2026" aria-label="Date from" autocomplete="off">
                        <svg class="reports-filter-calendar" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                </div>
                <div class="reports-filter-group">
                    <label for="reportsDateTo" class="reports-filter-label">Date To (Completion Date)</label>
                    <div class="reports-filter-date-wrap">
                        <input type="text" id="reportsDateTo" class="reports-filter-input" value="08/03/2026" aria-label="Date to" autocomplete="off">
                        <svg class="reports-filter-calendar" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                </div>
                <div class="reports-filter-actions">
                    <button type="button" class="reports-btn reports-btn-apply">Apply</button>
                </div>
            </div>
        </div>

        <div class="reports-data-card">
            <div class="reports-data-header">
                <h2 class="reports-section-title">Report Data</h2>
                <button type="button" class="reports-btn reports-btn-export">
                    <svg class="reports-btn-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="M9 15l3 3 3-3"/></svg>
                    Export to Excel
                </button>
            </div>
            <p class="reports-records-count">0 records</p>
            <div class="reports-data-toolbar">
                <div class="reports-entries-wrap">
                    <label for="reportsEntries" class="reports-entries-label">Show</label>
                    <select id="reportsEntries" class="reports-entries-select select2-single" aria-label="Entries per page">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="reports-entries-text">entries</span>
                </div>
                <div class="reports-search-wrap">
                    <label for="reportsSearch" class="reports-search-label">Search:</label>
                    <input type="search" id="reportsSearch" class="reports-search-input" placeholder="" aria-label="Search report data" autocomplete="off">
                </div>
            </div>
            <div class="reports-table-wrap">
                <table class="reports-table" id="reportsTable">
                    <thead>
                        <tr>
                            <th class="reports-th" data-sort="">
                                <span>Date Completion</span>
                                <span class="reports-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="reports-th" data-sort="">
                                <span>User</span>
                                <span class="reports-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="reports-th" data-sort="">
                                <span>Job Type</span>
                                <span class="reports-sort-icon" aria-hidden="true">↕</span>
                            </th>
                            <th class="reports-th" data-sort="">
                                <span>Total Units</span>
                                <span class="reports-sort-icon" aria-hidden="true">↕</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="reports-empty-row">
                            <td colspan="4" class="reports-empty-cell">No data for this filter.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .reports-page { padding-bottom: 2rem; max-width: 100%; }
        body.page-reports .content { padding-bottom: 2rem; }
        .reports-header { margin-bottom: 1.5rem; }
        .reports-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.25rem 0; }
        .reports-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; }
        .reports-summary-card { background: #FF9800; border-radius: 12px; padding: 1.25rem 1.25rem 1.25rem 1.5rem; margin-bottom: 1.5rem; max-width: 320px; min-height: 180px; position: relative; overflow: hidden; display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: stretch; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .reports-summary-briefcase { position: absolute; right: 0; top: 50%; transform: translateY(-50%); width: 160px; height: 120px; color: rgba(255,255,255,0.12); pointer-events: none; }
        .reports-summary-briefcase svg { width: 100%; height: 100%; stroke: currentColor; }
        .reports-summary-content { display: flex; flex-direction: column; justify-content: center; min-width: 0; position: relative; z-index: 1; }
        .reports-summary-title { font-size: 0.95rem; font-weight: 600; color: #fff; margin-bottom: 0.25rem; display: block; }
        .reports-summary-total { font-size: 2.5rem; font-weight: 700; color: #fff; line-height: 1.1; letter-spacing: -0.02em; margin-bottom: 0.6rem; display: block; }
        .reports-summary-sep { height: 1px; background: rgba(255,255,255,0.3); margin-bottom: 0.65rem; }
        .reports-summary-list { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 0.4rem; }
        .reports-summary-row { display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: #fff; font-weight: 400; }
        .reports-summary-label { min-width: 0; }
        .reports-summary-value { flex-shrink: 0; margin-left: 0.5rem; }
        .reports-filter-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; }
        .reports-section-title { font-size: 1rem; font-weight: 600; color: #e2e8f0; margin: 0 0 1rem 0; }
        .reports-filter-row { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 1rem; }
        .reports-filter-group { display: flex; flex-direction: column; gap: 0.35rem; min-width: 140px; }
        .reports-filter-group-client { min-width: 200px; }
        .reports-filter-label { font-size: 0.75rem; font-weight: 600; color: #94a3b8; margin-bottom: 0; }
        .reports-filter-input { padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; font-family: inherit; min-width: 140px; min-height: 44px; height: 44px; box-sizing: border-box; }
        .reports-filter-select { width: 100%; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; font-family: inherit; min-width: 180px; min-height: 44px; box-sizing: border-box; }
        .reports-filter-select:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .reports-filter-group-client .select2-container { height: 44px !important; }
        .reports-filter-group-client .select2-container .select2-selection--single { height: 44px !important; min-height: 44px !important; }
        html[data-theme="light"] .reports-filter-select { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        .reports-filter-date-wrap { position: relative; display: inline-block; min-height: 44px; }
        .reports-filter-date-wrap .reports-filter-input { padding-right: 2.25rem; width: 100%; min-width: 160px; }
        .reports-filter-calendar { position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #64748b; pointer-events: none; }
        .reports-filter-actions { flex-shrink: 0; display: flex; align-items: flex-end; }
        .reports-filter-actions .reports-btn-apply { min-height: 44px; height: 44px; padding: 0 1rem; border-radius: 10px; font-size: 0.9375rem; box-sizing: border-box; display: inline-flex; align-items: center; justify-content: center; }
        .reports-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 600; border: none; border-radius: 10px; cursor: pointer; font-family: inherit; }
        .reports-btn-apply { background: #2563eb; color: #fff; }
        .reports-btn-apply:hover { background: #1d4ed8; }
        .reports-btn-export { background: #16a34a; color: #fff; }
        .reports-btn-export:hover { background: #15803d; }
        .reports-data-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.25rem 1.5rem; overflow: hidden; }
        .reports-data-header { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 0.75rem; }
        .reports-data-header .reports-section-title { margin-bottom: 0; }
        .reports-records-count { font-size: 0.875rem; color: #94a3b8; margin: 0 0 1rem 0; }
        .reports-data-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
        .reports-entries-wrap { display: flex; align-items: center; gap: 0.5rem; }
        .reports-entries-label { font-size: 0.875rem; color: #94a3b8; }
        .reports-entries-select { padding: 0.4rem 0.6rem; font-size: 0.875rem; border: 1px solid #334155; border-radius: 6px; background: #1e293b; color: #e2e8f0; font-family: inherit; }
        .reports-entries-text { font-size: 0.875rem; color: #94a3b8; }
        .reports-search-wrap { display: flex; align-items: center; gap: 0.5rem; }
        .reports-search-label { font-size: 0.875rem; color: #94a3b8; }
        .reports-search-input { padding: 0.4rem 0.75rem; font-size: 0.875rem; border: 1px solid #334155; border-radius: 6px; background: #1e293b; color: #e2e8f0; font-family: inherit; min-width: 180px; }
        .reports-table-wrap { overflow-x: auto; border: 1px solid #334155; border-radius: 10px; }
        .reports-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .reports-th { text-align: left; padding: 0.75rem 1rem; font-weight: 600; color: #94a3b8; background: #1e293b; border-bottom: 1px solid #334155; cursor: pointer; user-select: none; }
        .reports-th:hover { color: #e2e8f0; }
        .reports-th .reports-sort-icon { margin-left: 0.25rem; opacity: 0.6; font-size: 0.75rem; }
        .reports-empty-row td { padding: 2rem 1rem; text-align: center; color: #64748b; border-bottom: 1px solid #334155; }
        .reports-empty-cell { font-size: 0.9375rem; }
        html[data-theme="light"] .reports-title { color: #1e293b; }
        html[data-theme="light"] .reports-subtitle { color: #64748b; }
        html[data-theme="light"] .reports-filter-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .reports-section-title { color: #1e293b; }
        html[data-theme="light"] .reports-filter-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .reports-data-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .reports-records-count { color: #64748b; }
        html[data-theme="light"] .reports-entries-select { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .reports-search-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .reports-table-wrap { border-color: #e2e8f0; }
        html[data-theme="light"] .reports-th { background: #f8fafc; color: #64748b; border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .reports-th:hover { color: #334155; }
        html[data-theme="light"] .reports-empty-row td { border-bottom-color: #e2e8f0; color: #64748b; }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            $('.select2-single').select2({ width: '100%', allowClear: false });
        });
    </script>
@endpush
