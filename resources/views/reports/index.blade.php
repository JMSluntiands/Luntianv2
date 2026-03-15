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
