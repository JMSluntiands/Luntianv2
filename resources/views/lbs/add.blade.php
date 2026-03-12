@extends('layouts.dashboard')

@section('title', 'Add New Job (LBS)')

@section('body_class', 'page-lbs-add')

@section('content')
    <div class="lbs-add-page">
        <div class="lbs-form-header">
            <h1 class="lbs-form-title">Add New Job (LBS)</h1>
            <p class="lbs-form-subtitle">Fill in the form below to create a new LBS job.</p>
        </div>

        <form id="lbsAddForm" action="#" method="POST" autocomplete="off" enctype="multipart/form-data">
            @csrf
            <div class="lbs-form-card">
                <div class="lbs-form-card-header">
                    <h2 class="lbs-form-section-title">Client Details</h2>
                    <span class="lbs-form-ref" id="jobReferenceContent">JOBS0823-003</span>
                </div>
                <div class="lbs-form-grid">
                    <div class="lbs-form-group">
                        <label class="lbs-form-label" for="reference_no">Reference No.</label>
                        <input type="text" id="reference_no" name="reference_no" class="lbs-form-input" placeholder="Enter Reference Number" autocomplete="off">
                    </div>
                    <div class="lbs-form-group">
                        <label class="lbs-form-label" for="client_reference">Client Reference</label>
                        <input type="text" id="client_reference" name="client_reference" class="lbs-form-input" placeholder="Enter Client Reference" autocomplete="off">
                    </div>
                    <div class="lbs-form-group">
                        <label class="lbs-form-label" for="compliance">Compliance</label>
                        <select id="compliance" name="compliance" class="lbs-form-select select2-single" autocomplete="off">
                            <option value="">Select compliance</option>
                            @foreach($compliances ?? [] as $c)
                                <option value="{{ $c->id }}" {{ (isset($defaultComplianceId) && (int) $defaultComplianceId === (int) $c->id) ? 'selected' : '' }}>
                                    {{ $c->column ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lbs-form-group">
                        <label class="lbs-form-label" for="client">Client</label>
                        <select id="client" name="client" class="lbs-form-select select2-single" autocomplete="off">
                            <option value="">Select client</option>
                            @foreach($clientAccounts ?? [] as $client)
                                <option value="{{ $client->client_account_id }}" {{ (isset($defaultClientAccountId) && (int) $defaultClientAccountId === (int) $client->client_account_id) ? 'selected' : '' }}>
                                    {{ $client->client_account_name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="lbs-form-card">
                <h2 class="lbs-form-section-title">Job Details</h2>
                <div class="lbs-form-grid">
                    <div class="lbs-form-group full-width">
                        <label class="lbs-form-label" for="job_address">Job Address</label>
                        <input type="text" id="job_address" name="job_address" class="lbs-form-input" placeholder="Complete Address" autocomplete="off">
                    </div>
                    <div class="lbs-form-row-three">
                        <div class="lbs-form-group">
                            <label class="lbs-form-label" for="priority">Priority</label>
                            <select id="priority" name="priority" class="lbs-form-select select2-single" autocomplete="off">
                                <option value="">Select priority</option>
                                @foreach($priorities ?? [] as $priority)
                                    <option value="{{ $priority->id }}" {{ (isset($defaultPriorityId) && (int) $defaultPriorityId === (int) $priority->id) ? 'selected' : '' }}>
                                        {{ $priority->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lbs-form-group">
                            <label class="lbs-form-label" for="job_type">Job Type</label>
                            <select id="job_type" name="job_type" class="lbs-form-select select2-single" autocomplete="off">
                                <option value="">Select job type</option>
                                @foreach($jobRequests ?? [] as $jobRequest)
                                    <option value="{{ $jobRequest->id }}" {{ (isset($defaultJobRequestId) && (int) $defaultJobRequestId === (int) $jobRequest->id) ? 'selected' : '' }}>
                                        {{ $jobRequest->job_request_type ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lbs-form-group">
                            <label class="lbs-form-label" for="job_status">Job Status</label>
                            <select id="job_status" name="job_status" class="lbs-form-select select2-single" autocomplete="off">
                                <option value="">Select status</option>
                                <option value="allocated" selected>Allocated</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="lbs-form-group full-width">
                        <label class="lbs-form-label" for="notes-body">Notes</label>
                        <input type="hidden" name="notes" id="notes" autocomplete="off">
                        <div class="lbs-notes-editor">
                            <div class="lbs-notes-toolbar">
                                <button type="button" class="lbs-notes-btn lbs-notes-icon-b" data-cmd="bold" title="Bold"><span>B</span></button>
                                <button type="button" class="lbs-notes-btn lbs-notes-icon-i" data-cmd="italic" title="Italic"><span>I</span></button>
                                <button type="button" class="lbs-notes-btn lbs-notes-icon-u" data-cmd="underline" title="Underline"><span>U</span></button>
                                <button type="button" class="lbs-notes-btn lbs-notes-icon-svg" data-cmd="insertOrderedList" title="Numbered list">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg>
                                </button>
                                <button type="button" class="lbs-notes-btn lbs-notes-icon-svg" data-cmd="insertUnorderedList" title="Bullets">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
                                </button>
                            </div>
                            <div id="lbs-notes-body" class="lbs-notes-body" contenteditable="true" data-placeholder="Write notes here"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lbs-form-card lbs-attachments-card">
                <h2 class="lbs-form-section-title">Attachments</h2>
                <div class="lbs-attachments-grid">
                    <div class="lbs-attach-col">
                        <label class="lbs-form-label">Upload Plans</label>
                        <label class="lbs-attach-drop" for="plans">
                            <div class="lbs-attach-drop-inner">
                                <span class="lbs-attach-drop-title">Drag files here</span>
                                <span class="lbs-attach-drop-sep">or</span>
                                <span class="lbs-attach-browse">browse</span>
                                <span class="lbs-attach-filename" id="plans-help">No file chosen</span>
                            </div>
                            <input type="file"
                                   id="plans"
                                   name="plans[]"
                                   class="lbs-form-input lbs-form-input-file"
                                   tabindex="-1"
                                   multiple>
                        </label>
                    </div>
                    <div class="lbs-attach-col">
                        <label class="lbs-form-label">Upload Document</label>
                        <label class="lbs-attach-drop" for="docs">
                            <div class="lbs-attach-drop-inner">
                                <span class="lbs-attach-drop-title">Drag files here</span>
                                <span class="lbs-attach-drop-sep">or</span>
                                <span class="lbs-attach-browse">browse</span>
                                <span class="lbs-attach-filename" id="docs-help">No file chosen</span>
                            </div>
                            <input type="file"
                                   id="docs"
                                   name="docs[]"
                                   class="lbs-form-input lbs-form-input-file"
                                   tabindex="-1"
                                   multiple>
                        </label>
                    </div>
                </div>
            </div>

            <div class="lbs-form-card">
                <h2 class="lbs-form-section-title">Assignment</h2>
                <div class="lbs-form-grid">
                    <div class="lbs-form-group">
                        <label class="lbs-form-label" for="assigned_to">Assigned To</label>
                        <select id="assigned_to" name="assigned_to" class="lbs-form-select select2-single">
                            <option value="">Select user</option>
                            <option value="GM" selected>GM</option>
                            @foreach($assignmentUsers ?? [] as $user)
                                @if(strtoupper($user->unique_code ?? '') !== 'GM')
                                    <option value="{{ $user->unique_code }}">{{ $user->unique_code }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="lbs-form-group">
                        <label class="lbs-form-label" for="checked_by">Checked By</label>
                        <select id="checked_by" name="checked_by" class="lbs-form-select select2-single">
                            <option value="">Select user</option>
                            <option value="GM" selected>GM</option>
                            @foreach($assignmentUsers ?? [] as $user)
                                @if(strtoupper($user->unique_code ?? '') !== 'GM')
                                    <option value="{{ $user->unique_code }}">{{ $user->unique_code }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="lbs-form-actions">
                <button type="button" id="submitLBSBtn" class="btn btn-add-lbs">Add Job (LBS)</button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .lbs-add-page { display: block; padding-bottom: 0; margin-bottom: 0; }
        body.page-lbs-add .content { padding-bottom: 0; }
        .lbs-form-header { margin-bottom: 1.75rem; }
        .lbs-form-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .lbs-form-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .lbs-form-ref { font-size: 1rem; font-weight: 600; color: #fff; padding: 0.35rem 0; text-align: right; }
        .lbs-form-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        .lbs-form-card:last-of-type { margin-bottom: 0; }
        .lbs-form-card-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 0.625rem; border-bottom: 1px solid #334155; }
        .lbs-form-section-title { font-size: 0.9375rem; font-weight: 600; color: #e2e8f0; margin: 0 0 1.25rem 0; padding-bottom: 0.625rem; border-bottom: 1px solid #334155; display: flex; align-items: center; gap: 0.5rem; }
        .lbs-form-card-header .lbs-form-section-title { margin: 0; padding: 0; border-bottom: none; }
        .lbs-form-section-title::before { content: ''; width: 4px; height: 1em; background: #2C528B; border-radius: 2px; }
        .lbs-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.25rem 1.5rem; align-items: start; }
        .lbs-form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .lbs-form-group.full-width { grid-column: 1 / -1; }
        .lbs-form-row-three { grid-column: 1 / -1; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.25rem 1.5rem; }
        .lbs-form-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; letter-spacing: 0.01em; }
        .lbs-form-input, .lbs-form-select { width: 100%; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; min-height: 2.75rem; }
        .lbs-form-input::placeholder { color: #64748b; }
        .lbs-form-input:focus, .lbs-form-select:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .lbs-notes-editor { border: 1px solid #334155; border-radius: 10px; background: #1e293b; overflow: hidden; transition: border-color 0.2s, box-shadow 0.2s; }
        .lbs-notes-editor:focus-within { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .lbs-notes-toolbar { display: flex; align-items: center; gap: 2px; padding: 6px 10px; border-bottom: 1px solid #334155; background: #1e293b; }
        .lbs-notes-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border: none; border-radius: 6px; background: transparent; color: #94a3b8; font-size: 0.85rem; cursor: pointer; font-family: inherit; transition: background 0.15s, color 0.15s; }
        .lbs-notes-btn:hover { background: rgba(255,255,255,0.08); color: #e2e8f0; }
        .lbs-notes-btn.active { background: rgba(44,82,139,0.35); color: #93c5fd; }
        .lbs-notes-icon-b span { font-weight: 700; }
        .lbs-notes-icon-i span { font-style: italic; font-weight: 500; }
        .lbs-notes-icon-u span { text-decoration: underline; font-weight: 600; }
        .lbs-notes-body { min-height: 140px; max-height: 280px; padding: 0.625rem 0.875rem; color: #e2e8f0; font-size: 0.9375rem; line-height: 1.5; outline: none; overflow-y: auto; }
        .lbs-notes-body:empty::before { content: attr(data-placeholder); color: #64748b; }
        .lbs-attachments-card { margin-top: 1.5rem; }
        .lbs-attachments-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.25rem 1.5rem; }
        .lbs-attach-col { display: flex; flex-direction: column; gap: 0.5rem; }
        .lbs-attach-drop { position: relative; border-radius: 10px; border: 1px dashed #334155; background: #020617; padding: 0.65rem 0.9rem; cursor: pointer; transition: border-color 0.2s, background 0.2s; display: block; }
        .lbs-attach-drop:hover { border-color: #475569; background: #020617; }
        .lbs-attach-drop-inner { display: flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; color: #94a3b8; white-space: nowrap; overflow: hidden; }
        .lbs-attach-drop-title { color: #e2e8f0; }
        .lbs-attach-drop-sep { opacity: 0.7; }
        .lbs-attach-browse { background: none; border: none; padding: 0; margin: 0; font-size: 0.8rem; color: #38bdf8; text-decoration: underline; cursor: pointer; }
        .lbs-attach-browse:hover { color: #0ea5e9; }
        .lbs-attach-filename { margin-left: auto; font-size: 0.75rem; color: #64748b; overflow: hidden; text-overflow: ellipsis; }
        .lbs-form-input-file { position: absolute; inset: 0; opacity: 0; pointer-events: none; }
        .lbs-form-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; padding-bottom: 1.25rem; border-top: 1px solid #334155; flex-wrap: wrap; }
        .lbs-form-actions .btn { padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; transition: background 0.2s, transform 0.05s, box-shadow 0.15s, opacity 0.15s; }
        .btn-add-lbs { background: #2C528B; color: #fff; box-shadow: 0 2px 6px rgba(44,82,139,0.35); }
        .btn-add-lbs:hover { background: #234a77; box-shadow: 0 4px 10px rgba(44,82,139,0.4); }
        .lbs-form-actions .btn:active { transform: scale(0.98); }
        .btn-add-lbs.is-loading { position: relative; opacity: 0.8; pointer-events: none; transform: translateY(1px); box-shadow: 0 1px 3px rgba(15,23,42,0.6); }
        @media (max-width: 768px) {
            .lbs-form-grid { grid-template-columns: 1fr; }
            .lbs-form-row-three { grid-template-columns: 1fr; }
            .lbs-attachments-grid { grid-template-columns: 1fr; }
            .lbs-form-header { margin-bottom: 1.25rem; }
            .lbs-form-title { font-size: 1.375rem; }
            .lbs-form-subtitle { font-size: 0.875rem; }
            .lbs-form-card { padding: 1.25rem; margin-bottom: 1.25rem; }
            .lbs-form-card-header { margin-bottom: 1rem; }
            .lbs-form-actions { margin-top: 1.25rem; padding-top: 1rem; flex-direction: column; }
            .lbs-form-actions .btn { width: 100%; justify-content: center; }
        }
        @media (max-width: 480px) {
            .lbs-form-card { padding: 1rem; }
            .lbs-form-title { font-size: 1.2rem; }
        }
        html[data-theme="light"] .lbs-form-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .lbs-form-title { color: #0f172a; }
        html[data-theme="light"] .lbs-form-subtitle { color: #64748b; }
        html[data-theme="light"] .lbs-form-ref { color: #0f172a; }
        html[data-theme="light"] .lbs-form-section-title { color: #334155; border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .lbs-form-input, html[data-theme="light"] .lbs-form-select { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .lbs-notes-editor { border-color: #e2e8f0; background: #fff; }
        html[data-theme="light"] .lbs-notes-body { color: #1e293b; }
        html[data-theme="light"] .lbs-notes-body:empty::before { color: #94a3b8; }
        html[data-theme="light"] .lbs-form-actions { border-top-color: #e2e8f0; }
        html[data-theme="light"] .lbs-attach-drop { background: #ffffff; border-color: #e2e8f0; }
        html[data-theme="light"] .lbs-attach-drop-inner { color: #475569; }
        html[data-theme="light"] .lbs-attach-drop-title { color: #0f172a; }
        html[data-theme="light"] .lbs-attach-filename { color: #94a3b8; }
        .lbs-after-save-backdrop { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); display: flex; align-items: center; justify-content: center; z-index: 9999; animation: lbsFadeIn 0.18s ease-out; }
        .lbs-after-save-modal { background: #0f172a; border-radius: 14px; padding: 1.5rem 1.75rem; border: 1px solid #334155; max-width: 360px; width: 100%; box-shadow: 0 20px 40px rgba(15,23,42,0.65); animation: lbsSlideUp 0.2s ease-out; }
        .lbs-after-save-title { font-size: 1.05rem; font-weight: 600; color: #e2e8f0; margin-bottom: 0.5rem; }
        .lbs-after-save-text { font-size: 0.9rem; color: #94a3b8; margin-bottom: 1.25rem; }
        .lbs-after-save-actions { display: flex; gap: 0.75rem; justify-content: flex-end; flex-wrap: wrap; }
        .lbs-btn-primary, .lbs-btn-secondary { border-radius: 999px; font-size: 0.875rem; font-weight: 500; padding: 0.55rem 1.2rem; border: none; cursor: pointer; }
        .lbs-btn-primary { background: #22c55e; color: #0f172a; }
        .lbs-btn-primary:hover { background: #16a34a; }
        .lbs-btn-secondary { background: #1e293b; color: #e2e8f0; }
        .lbs-btn-secondary:hover { background: #111827; }
        @keyframes lbsFadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes lbsSlideUp { from { opacity: 0; transform: translateY(12px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        html[data-theme="light"] .lbs-after-save-modal { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .lbs-after-save-title { color: #0f172a; }
        html[data-theme="light"] .lbs-after-save-text { color: #64748b; }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            var notesBody = document.getElementById('lbs-notes-body');
            var noteBtns = document.querySelectorAll('.lbs-notes-btn');

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

            $('.select2-single').select2({ width: '100%', allowClear: false });

            var $btn = $('#submitLBSBtn');
            var originalBtnHtml = $btn.html();

            function bindFileSummary(inputId, helpId) {
                var input = document.getElementById(inputId);
                var help = document.getElementById(helpId);
                if (!input || !help) return;
                input.addEventListener('change', function() {
                    if (!this.files || this.files.length === 0) {
                        help.textContent = 'No files selected.';
                        return;
                    }
                    if (this.files.length === 1) {
                        help.textContent = this.files[0].name;
                    } else {
                        help.textContent = this.files.length + ' files selected';
                    }
                });
            }

            bindFileSummary('plans', 'plans-help');
            bindFileSummary('docs', 'docs-help');

            $btn.on('click', function(e) {
                e.preventDefault();

                $('#notes').val($('#lbs-notes-body').html());

                var formEl = document.getElementById('lbsAddForm');
                var formData = new FormData(formEl);

                var headerRef = $('#jobReferenceContent').text().trim();
                if (headerRef) {
                    formData.append('header_reference', headerRef);
                }

                $.ajax({
                    url: '{{ route('lbs.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $btn.prop('disabled', true)
                            .addClass('is-loading')
                            .html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...');
                    },
                    success: function(resp) {
                        if (resp.status === 'success') {
                            if (window.showSuccessToast) showSuccessToast(resp.message || 'Job saved.');
                            formEl.reset();
                            $('#lbs-notes-body').empty();
                            showLbsAfterSavePrompt();
                        } else {
                            if (window.showSuccessToast) showSuccessToast(resp.message || 'Failed to save job.');
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Unexpected error while saving.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        if (window.showSuccessToast) showSuccessToast(msg);
                    },
                    complete: function() {
                        $btn.prop('disabled', false)
                            .removeClass('is-loading')
                            .html(originalBtnHtml);
                    }
                });
            });

            function showLbsAfterSavePrompt() {
                var $overlay = $(
                    '<div class="lbs-after-save-backdrop">' +
                        '<div class="lbs-after-save-modal">' +
                            '<div class="lbs-after-save-title">Job saved</div>' +
                            '<div class="lbs-after-save-text">Do you want to create another LBS job?</div>' +
                            '<div class="lbs-after-save-actions">' +
                                '<button type="button" class="lbs-btn-secondary" data-lbs-go-list>Go to LBS list</button>' +
                                '<button type="button" class="lbs-btn-primary" data-lbs-new-job>Create another job</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );

                $('body').append($overlay);

                $overlay.on('click', function(e) {
                    if (e.target === this) {
                        $overlay.remove();
                    }
                });

                $overlay.find('[data-lbs-new-job]').on('click', function() {
                    $overlay.remove();
                });

                $overlay.find('[data-lbs-go-list]').on('click', function() {
                    window.location.href = '{{ route('lbs.list') }}';
                });
            }
        });
    </script>
@endpush

