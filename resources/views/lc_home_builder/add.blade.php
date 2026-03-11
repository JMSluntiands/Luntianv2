@extends('layouts.dashboard')

@section('title', 'Add New Job (LC HOME BUILDER)')

@section('body_class', 'page-lc_home_builder-add')

@section('content')
    <div class="lc_home_builder-add-page">
        <div class="lc_home_builder-form-header">
            <h1 class="lc_home_builder-form-title">Add New Job (LC HOME BUILDER)</h1>
            <p class="lc_home_builder-form-subtitle">Fill in the form below to create a new LC HOME BUILDER job.</p>
        </div>

        <form id="lc_home_builderAddForm" action="#" method="POST" autocomplete="off">
            @csrf
            <div class="lc_home_builder-form-card">
                <div class="lc_home_builder-form-card-header">
                    <h2 class="lc_home_builder-form-section-title">Client Details</h2>
                    <span class="lc_home_builder-form-ref" id="lc_home_builderJobReferenceContent">JOB0903-001</span>
                </div>
                <div class="lc_home_builder-form-grid">
                    <div class="lc_home_builder-form-group lc_home_builder-form-col12 lc_home_builder-form-group-urgent">
                        <label class="lc_home_builder-form-urgent-label" for="urgent_job">
                            <input type="checkbox" id="urgent_job" name="urgent_job" value="1" class="lc_home_builder-form-checkbox" autocomplete="off">
                            <span class="lc_home_builder-form-urgent-title">Urgent Job (YES)</span>
                        </label>
                    </div>
                    <div class="lc_home_builder-form-group lc_home_builder-form-col6">
                        <label class="lc_home_builder-form-label" for="ncc_compliance">NCC Compliance</label>
                        <select id="ncc_compliance" name="ncc_compliance" class="lc_home_builder-form-select select2-single">
                            <option value="">Select compliance</option>
                            <option value="2019" selected>2019</option>
                            <option value="2022_woh">2022 Whole of Home (WOH)</option>
                            <option value="2023_woh">2023 Whole of Home (WOH)</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="lc_home_builder-form-group lc_home_builder-form-col6">
                        <label class="lc_home_builder-form-label" for="job_type_request">Job Type Request</label>
                        <select id="job_type_request" name="job_type_request" class="lc_home_builder-form-select select2-single">
                            <option value="">Select job type</option>
                            <option value="just_a_query" selected>Just a query</option>
                            <option value="prelim">Prelim</option>
                            <option value="ea_lc_home_builder_1s">EA_LC HOME BUILDER_1S</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="lc_home_builder-form-group">
                        <label class="lc_home_builder-form-label" for="job_number">Job Number <span class="lc_home_builder-form-required">*</span></label>
                        <input type="text" id="job_number" name="job_number" class="lc_home_builder-form-input" placeholder="e.g. 12345B" required autocomplete="off">
                        <span class="lc_home_builder-form-hint">Enter 5 digits followed by letter B (e.g. 12345B)</span>
                    </div>
                    <div class="lc_home_builder-form-group">
                        <label class="lc_home_builder-form-label" for="client_name">Client Name <span class="lc_home_builder-form-required">*</span></label>
                        <input type="text" id="client_name" name="client_name" class="lc_home_builder-form-input" placeholder="Enter client name" required autocomplete="off">
                    </div>
                    <div class="lc_home_builder-form-group">
                        <label class="lc_home_builder-form-label" for="contact_email">Contact Email <span class="lc_home_builder-form-required">*</span></label>
                        <input type="email" id="contact_email" name="contact_email" class="lc_home_builder-form-input" placeholder="Enter contact email" required autocomplete="off">
                    </div>
                    <div class="lc_home_builder-form-group lc_home_builder-form-col6">
                        <label class="lc_home_builder-form-label" for="please_select">Please Select</label>
                        <select id="please_select" name="please_select" class="lc_home_builder-form-select select2-single">
                            <option value="">Select option</option>
                            <option value="option1">Option 1</option>
                            <option value="option2">Option 2</option>
                            <option value="option3">Option 3</option>
                        </select>
                    </div>
                    <div class="lc_home_builder-form-group full-width">
                        <label class="lc_home_builder-form-label" for="notes-body">Notes (LC HOME BUILDER)</label>
                        <input type="hidden" name="notes" id="lc_home_builder_notes" autocomplete="off">
                        <div class="lc_home_builder-notes-editor">
                            <div class="lc_home_builder-notes-toolbar">
                                <button type="button" class="lc_home_builder-notes-btn lc_home_builder-notes-icon-b" data-cmd="bold" title="Bold"><span>B</span></button>
                                <button type="button" class="lc_home_builder-notes-btn lc_home_builder-notes-icon-i" data-cmd="italic" title="Italic"><span>I</span></button>
                                <button type="button" class="lc_home_builder-notes-btn lc_home_builder-notes-icon-u" data-cmd="underline" title="Underline"><span>U</span></button>
                                <button type="button" class="lc_home_builder-notes-btn lc_home_builder-notes-icon-svg" data-cmd="insertOrderedList" title="Numbered list">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg>
                                </button>
                                <button type="button" class="lc_home_builder-notes-btn lc_home_builder-notes-icon-svg" data-cmd="insertUnorderedList" title="Bullets">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
                                </button>
                            </div>
                            <div id="lc_home_builder-notes-body" class="lc_home_builder-notes-body" contenteditable="true" data-placeholder="Write notes here"></div>
                        </div>
                    </div>
                    <div class="lc_home_builder-form-group lc_home_builder-form-col6">
                        <label class="lc_home_builder-form-label">Upload Plans</label>
                        <div class="lc_home_builder-file-upload-wrapper">
                            <label class="lc_home_builder-file-upload-zone" for="lc_home_builder_upload_plans" data-upload-id="lc_home_builder_upload_plans">
                                <input type="file" id="lc_home_builder_upload_plans" name="upload_plans[]" multiple class="lc_home_builder-file-upload-input" tabindex="-1">
                                <span class="lc_home_builder-file-upload-text">Drag files here or <span class="lc_home_builder-file-upload-browse">browse</span></span>
                                <span class="lc_home_builder-file-status" data-for="lc_home_builder_upload_plans">No file chosen</span>
                            </label>
                            <div class="lc_home_builder-file-attached-list" data-for="lc_home_builder_upload_plans"></div>
                        </div>
                    </div>
                    <div class="lc_home_builder-form-group lc_home_builder-form-col6">
                        <label class="lc_home_builder-form-label">Upload Document</label>
                        <div class="lc_home_builder-file-upload-wrapper">
                            <label class="lc_home_builder-file-upload-zone" for="lc_home_builder_upload_document" data-upload-id="lc_home_builder_upload_document">
                                <input type="file" id="lc_home_builder_upload_document" name="upload_document[]" multiple class="lc_home_builder-file-upload-input" tabindex="-1">
                                <span class="lc_home_builder-file-upload-text">Drag files here or <span class="lc_home_builder-file-upload-browse">browse</span></span>
                                <span class="lc_home_builder-file-status" data-for="lc_home_builder_upload_document">No file chosen</span>
                            </label>
                            <div class="lc_home_builder-file-attached-list" data-for="lc_home_builder_upload_document"></div>
                        </div>
                    </div>
                    <div class="lc_home_builder-form-group">
                        <label class="lc_home_builder-form-label" for="assigned_to">Assigned To</label>
                        <select id="assigned_to" name="assigned_to" class="lc_home_builder-form-select select2-single">
                            <option value="">Select user</option>
                            <option value="gm" selected>GM</option>
                            <option value="ajs">AJS</option>
                            <option value="sb">SB</option>
                            <option value="pep">PEP</option>
                            <option value="jdr">JDR</option>
                            <option value="js">JS</option>
                        </select>
                    </div>
                    <div class="lc_home_builder-form-group">
                        <label class="lc_home_builder-form-label" for="checked_by">Checked By</label>
                        <select id="checked_by" name="checked_by" class="lc_home_builder-form-select select2-single">
                            <option value="">Select user</option>
                            <option value="gm" selected>GM</option>
                            <option value="ajs">AJS</option>
                            <option value="sb">SB</option>
                            <option value="pep">PEP</option>
                            <option value="jdr">JDR</option>
                            <option value="js">JS</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="lc_home_builder-form-actions">
                <button type="submit" class="btn btn-add-lc_home_builder">Add Job (LC HOME BUILDER)</button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .lc_home_builder-add-page { display: block; padding-bottom: 0; margin-bottom: 0; }
        body.page-lc_home_builder-add .content { padding-bottom: 0; }
        .lc_home_builder-form-header { margin-bottom: 1.75rem; }
        .lc_home_builder-form-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .lc_home_builder-form-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .lc_home_builder-form-ref { font-size: 1rem; font-weight: 600; color: #fff; padding: 0.35rem 0; }
        .lc_home_builder-form-card-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 0.625rem; border-bottom: 1px solid #334155; }
        .lc_home_builder-form-card-header .lc_home_builder-form-section-title { margin: 0; padding-bottom: 0; border-bottom: none; }
        .lc_home_builder-form-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        .lc_home_builder-form-card:last-of-type { margin-bottom: 0; }
        .lc_home_builder-form-section-title { font-size: 0.9375rem; font-weight: 600; color: #e2e8f0; margin: 0 0 1.25rem 0; padding-bottom: 0.625rem; border-bottom: 1px solid #334155; display: flex; align-items: center; gap: 0.5rem; }
        .lc_home_builder-form-card-header .lc_home_builder-form-section-title { margin: 0; padding: 0; border-bottom: none; }
        .lc_home_builder-form-section-title::before { content: ''; width: 4px; height: 1em; background: #2C528B; border-radius: 2px; }
        .lc_home_builder-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem 1.5rem; align-items: start; }
        .lc_home_builder-form-group { min-height: 0; }
        .lc_home_builder-form-col6 { grid-column: span 1; }
        .lc_home_builder-form-col12 { grid-column: 1 / -1; }
        .lc_home_builder-form-group-urgent { display: flex; flex-direction: column; gap: 0.5rem; }
        .lc_home_builder-form-urgent-label { display: flex; flex-direction: row; align-items: center; gap: 0.5rem; cursor: pointer; margin: 0; }
        .lc_home_builder-form-urgent-label .lc_home_builder-form-checkbox {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
            cursor: pointer;
            margin: 0;
            appearance: none;
            -webkit-appearance: none;
            border: 2px solid #64748b;
            border-radius: 6px;
            background: #1e293b;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }
        .lc_home_builder-form-urgent-label .lc_home_builder-form-checkbox:hover { border-color: #94a3b8; }
        .lc_home_builder-form-urgent-label .lc_home_builder-form-checkbox:focus { outline: none; box-shadow: 0 0 0 3px rgba(44,82,139,0.35); }
        .lc_home_builder-form-urgent-label .lc_home_builder-form-checkbox:checked {
            background: #2C528B;
            border-color: #2C528B;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
            background-size: 14px 14px;
            background-position: center;
            background-repeat: no-repeat;
        }
        .lc_home_builder-form-urgent-title { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; }
        html[data-theme="light"] .lc_home_builder-form-urgent-title { color: #64748b; }
        html[data-theme="light"] .lc_home_builder-form-urgent-label .lc_home_builder-form-checkbox {
            border-color: #94a3b8;
            background: #fff;
        }
        html[data-theme="light"] .lc_home_builder-form-urgent-label .lc_home_builder-form-checkbox:hover { border-color: #64748b; }
        html[data-theme="light"] .lc_home_builder-form-urgent-label .lc_home_builder-form-checkbox:checked {
            background: #2C528B;
            border-color: #2C528B;
        }
        .lc_home_builder-form-hint { margin-top: 0.25rem; line-height: 1.3; }
        @media (max-width: 768px) {
            .lc_home_builder-form-grid { grid-template-columns: 1fr; }
            .lc_home_builder-form-col6,
            .lc_home_builder-form-col12 { grid-column: span 1; }
            .lc_home_builder-form-header { margin-bottom: 1.25rem; }
            .lc_home_builder-form-title { font-size: 1.375rem; }
            .lc_home_builder-form-subtitle { font-size: 0.875rem; }
            .lc_home_builder-form-card { padding: 1.25rem; margin-bottom: 1.25rem; }
            .lc_home_builder-form-card-header { margin-bottom: 1rem; }
            .lc_home_builder-form-actions { margin-top: 1.25rem; padding-top: 1rem; flex-direction: column; }
            .lc_home_builder-form-actions .btn { width: 100%; justify-content: center; }
        }
        @media (max-width: 480px) {
            .lc_home_builder-form-card { padding: 1rem; }
            .lc_home_builder-form-title { font-size: 1.2rem; }
        }
        .lc_home_builder-form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .lc_home_builder-form-group.full-width { grid-column: 1 / -1; }
        .lc_home_builder-form-group .lc_home_builder-form-input,
        .lc_home_builder-form-group .lc_home_builder-form-select { min-height: 2.75rem; }
        .lc_home_builder-form-group.lc_home_builder-form-col6 { grid-column: span 1; }
        .lc_home_builder-form-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; letter-spacing: 0.01em; }
        .lc_home_builder-form-required { color: #f87171; }
        .lc_home_builder-form-hint { font-size: 0.75rem; color: #64748b; margin-top: 0.15rem; }
        .lc_home_builder-form-input, .lc_home_builder-form-select, .lc_home_builder-form-textarea { width: 100%; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; }
        .lc_home_builder-form-input::placeholder, .lc_home_builder-form-textarea::placeholder { color: #64748b; }
        .lc_home_builder-form-input:focus, .lc_home_builder-form-select:focus, .lc_home_builder-form-textarea:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .lc_home_builder-notes-editor { border: 1px solid #334155; border-radius: 10px; background: #1e293b; overflow: hidden; transition: border-color 0.2s, box-shadow 0.2s; }
        .lc_home_builder-notes-editor:focus-within { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .lc_home_builder-notes-toolbar { display: flex; align-items: center; gap: 2px; padding: 6px 10px; border-bottom: 1px solid #334155; background: #1e293b; }
        .lc_home_builder-notes-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border: none; border-radius: 6px; background: transparent; color: #94a3b8; font-size: 0.85rem; cursor: pointer; font-family: inherit; transition: background 0.15s, color 0.15s; }
        .lc_home_builder-notes-btn:hover { background: rgba(255,255,255,0.08); color: #e2e8f0; }
        .lc_home_builder-notes-btn.active { background: rgba(44,82,139,0.35); color: #93c5fd; }
        .lc_home_builder-notes-icon-b span { font-weight: 700; }
        .lc_home_builder-notes-icon-i span { font-style: italic; font-weight: 500; }
        .lc_home_builder-notes-icon-u span { text-decoration: underline; font-weight: 600; }
        .lc_home_builder-notes-body { min-height: 140px; max-height: 280px; padding: 0.625rem 0.875rem; color: #e2e8f0; font-size: 0.9375rem; line-height: 1.5; outline: none; overflow-y: auto; }
        .lc_home_builder-notes-body:empty::before { content: attr(data-placeholder); color: #64748b; }
        .lc_home_builder-notes-body ul, .lc_home_builder-notes-body ol { margin: 0.5em 0; padding-left: 1.5em; }
        .lc_home_builder-file-upload-wrapper { display: flex; flex-direction: column; gap: 0.5rem; }
        .lc_home_builder-file-upload-zone { position: relative; display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; padding: 0.75rem 1rem; border: 1px dashed #475569; border-radius: 10px; background: rgba(30,41,59,0.5); min-height: 48px; cursor: pointer; }
        .lc_home_builder-file-upload-zone .lc_home_builder-file-upload-input { position: absolute; left: 0; top: 0; width: 0.1px; height: 0.1px; opacity: 0; overflow: hidden; z-index: -1; }
        .lc_home_builder-file-upload-text { font-size: 0.875rem; color: #94a3b8; cursor: pointer; }
        .lc_home_builder-file-upload-browse { color: #2C528B; font-weight: 500; cursor: pointer; text-decoration: underline; }
        .lc_home_builder-file-status { font-size: 0.8125rem; color: #64748b; }
        .lc_home_builder-file-attached-list { display: flex; flex-direction: column; gap: 0.35rem; margin-top: 0.25rem; }
        .lc_home_builder-file-attached-item { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #94a3b8; padding: 0.35rem 0; }
        .lc_home_builder-file-attached-item .lc_home_builder-file-attached-name { color: #e2e8f0; flex: 1; }
        .lc_home_builder-file-attached-remove { margin-left: auto; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 500; border-radius: 6px; background: #334155; color: #e2e8f0; border: none; cursor: pointer; transition: background 0.15s; }
        .lc_home_builder-file-attached-remove:hover { background: #475569; }
        .lc_home_builder-form-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; padding-bottom: 1.25rem; border-top: 1px solid #334155; flex-wrap: wrap; }
        .lc_home_builder-form-actions .btn { padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; transition: background 0.2s, transform 0.05s; }
        .btn-add-lc_home_builder { background: #2C528B; color: #fff; box-shadow: 0 2px 6px rgba(44,82,139,0.35); }
        .btn-add-lc_home_builder:hover { background: #234a77; box-shadow: 0 4px 10px rgba(44,82,139,0.4); }
        .lc_home_builder-form-actions .btn:active { transform: scale(0.98); }
        html[data-theme="light"] .lc_home_builder-form-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .lc_home_builder-form-title { color: #0f172a; }
        html[data-theme="light"] .lc_home_builder-form-subtitle { color: #64748b; }
        html[data-theme="light"] .lc_home_builder-form-ref { color: #0f172a; }
        html[data-theme="light"] .lc_home_builder-form-card-header { border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .lc_home_builder-form-section-title { color: #334155; border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .lc_home_builder-form-input, html[data-theme="light"] .lc_home_builder-form-select { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .lc_home_builder-notes-editor { border-color: #e2e8f0; background: #fff; }
        html[data-theme="light"] .lc_home_builder-notes-body { color: #1e293b; }
        html[data-theme="light"] .lc_home_builder-notes-body:empty::before { color: #94a3b8; }
        html[data-theme="light"] .lc_home_builder-file-upload-zone { border-color: #cbd5e1; background: #f8fafc; }
        html[data-theme="light"] .lc_home_builder-file-upload-text { color: #64748b; }
        html[data-theme="light"] .lc_home_builder-form-actions { border-top-color: #e2e8f0; }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            var notesBody = document.getElementById('lc_home_builder-notes-body');
            var noteBtns = document.querySelectorAll('.lc_home_builder-notes-btn');
            function updateNotesActiveState() {
                noteBtns.forEach(function(btn) {
                    var cmd = btn.getAttribute('data-cmd');
                    var active = document.queryCommandState(cmd);
                    btn.classList.toggle('active', active);
                });
            }
            noteBtns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var cmd = this.getAttribute('data-cmd');
                    notesBody.focus();
                    document.execCommand(cmd, false, null);
                    updateNotesActiveState();
                });
            });
            notesBody.addEventListener('focus', updateNotesActiveState);
            notesBody.addEventListener('keyup', updateNotesActiveState);
            notesBody.addEventListener('mouseup', updateNotesActiveState);
            document.getElementById('lc_home_builderAddForm').addEventListener('submit', function() {
                document.getElementById('lc_home_builder_notes').value = notesBody.innerHTML;
            });
            $('.select2-single').select2({ width: '100%', allowClear: false });
            $('.lc_home_builder-file-upload-zone').each(function() {
                var $zone = $(this);
                var $input = $zone.find('.lc_home_builder-file-upload-input');
                var uploadId = $zone.data('upload-id');
                var $status = $('[data-for="' + uploadId + '"].lc_home_builder-file-status');
                var $attachedList = $('.lc_home_builder-file-attached-list[data-for="' + uploadId + '"]');
                $input.on('change', function() {
                    var files = this.files;
                    $status.text(files.length ? files.length + ' file(s) chosen' : 'No file chosen');
                    $attachedList.find('.lc_home_builder-file-attached-item').remove();
                    for (var i = 0; i < files.length; i++) {
                        var name = $('<div>').text(files[i].name).html();
                        $attachedList.append(
                            '<div class="lc_home_builder-file-attached-item" data-index="' + i + '"><span class="lc_home_builder-file-attached-name">' + name + '</span><button type="button" class="lc_home_builder-file-attached-remove" aria-label="Remove">Remove</button></div>'
                        );
                    }
                    this.blur();
                });
                $attachedList.on('click', '.lc_home_builder-file-attached-remove', function() {
                    var $item = $(this).closest('.lc_home_builder-file-attached-item');
                    var index = parseInt($item.data('index'), 10);
                    if (!isNaN(index)) {
                        var inputEl = $input[0];
                        var dt = new DataTransfer();
                        for (var i = 0; i < inputEl.files.length; i++) {
                            if (i !== index) dt.items.add(inputEl.files[i]);
                        }
                        inputEl.files = dt.files;
                        $status.text(dt.files.length ? dt.files.length + ' file(s) chosen' : 'No file chosen');
                    }
                    $item.remove();
                    $attachedList.find('.lc_home_builder-file-attached-item').each(function(j) {
                        $(this).data('index', j);
                    });
                });
            });
        });
        document.getElementById('lc_home_builderAddForm').addEventListener('submit', function(e) {
            e.preventDefault();
        });
    </script>
@endpush
