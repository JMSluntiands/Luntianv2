@extends('layouts.dashboard')

@section('title', 'Add New Job (BLUINQ)')

@section('body_class', 'page-bluinq-add')

@section('content')
    <div class="bluinq-add-page">
        <div class="bluinq-form-header">
            <h1 class="bluinq-form-title">Add New Job (BLUINQ)</h1>
            <p class="bluinq-form-subtitle">Fill in the form below to create a new BLUINQ job.</p>
        </div>

        <form id="bluinqAddForm" action="#" method="POST" autocomplete="off">
            @csrf
            <div class="bluinq-form-card">
                <div class="bluinq-form-card-header">
                    <h2 class="bluinq-form-section-title">Client Details</h2>
                    <span class="bluinq-form-ref" id="bluinqJobReferenceContent">JOB0903-001</span>
                </div>
                <div class="bluinq-form-grid">
                    <div class="bluinq-form-group bluinq-form-col12 bluinq-form-group-urgent">
                        <label class="bluinq-form-urgent-label" for="urgent_job">
                            <input type="checkbox" id="urgent_job" name="urgent_job" value="1" class="bluinq-form-checkbox" autocomplete="off">
                            <span class="bluinq-form-urgent-title">Urgent Job (YES)</span>
                        </label>
                    </div>
                    <div class="bluinq-form-group bluinq-form-col6">
                        <label class="bluinq-form-label" for="ncc_compliance">NCC Compliance</label>
                        <select id="ncc_compliance" name="ncc_compliance" class="bluinq-form-select select2-single">
                            <option value="">Select compliance</option>
                            <option value="2019" selected>2019</option>
                            <option value="2022_woh">2022 Whole of Home (WOH)</option>
                            <option value="2023_woh">2023 Whole of Home (WOH)</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="bluinq-form-group bluinq-form-col6">
                        <label class="bluinq-form-label" for="job_type_request">Job Type Request</label>
                        <select id="job_type_request" name="job_type_request" class="bluinq-form-select select2-single">
                            <option value="">Select job type</option>
                            <option value="just_a_query" selected>Just a query</option>
                            <option value="prelim">Prelim</option>
                            <option value="ea_bluinq_1s">EA_BLUINQ_1S</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="bluinq-form-group">
                        <label class="bluinq-form-label" for="job_number">Job Number <span class="bluinq-form-required">*</span></label>
                        <input type="text" id="job_number" name="job_number" class="bluinq-form-input" placeholder="e.g. 12345B" required autocomplete="off">
                        <span class="bluinq-form-hint">Enter 5 digits followed by letter B (e.g. 12345B)</span>
                    </div>
                    <div class="bluinq-form-group">
                        <label class="bluinq-form-label" for="client_name">Client Name <span class="bluinq-form-required">*</span></label>
                        <input type="text" id="client_name" name="client_name" class="bluinq-form-input" placeholder="Enter client name" required autocomplete="off">
                    </div>
                    <div class="bluinq-form-group">
                        <label class="bluinq-form-label" for="contact_email">Contact Email <span class="bluinq-form-required">*</span></label>
                        <input type="email" id="contact_email" name="contact_email" class="bluinq-form-input" placeholder="Enter contact email" required autocomplete="off">
                    </div>
                    <div class="bluinq-form-group bluinq-form-col6">
                        <label class="bluinq-form-label" for="please_select">Please Select</label>
                        <select id="please_select" name="please_select" class="bluinq-form-select select2-single">
                            <option value="">Select option</option>
                            <option value="option1">Option 1</option>
                            <option value="option2">Option 2</option>
                            <option value="option3">Option 3</option>
                        </select>
                    </div>
                    <div class="bluinq-form-group full-width">
                        <label class="bluinq-form-label" for="notes-body">Notes (BLUINQ)</label>
                        <input type="hidden" name="notes" id="bluinq_notes" autocomplete="off">
                        <div class="bluinq-notes-editor">
                            <div class="bluinq-notes-toolbar">
                                <button type="button" class="bluinq-notes-btn bluinq-notes-icon-b" data-cmd="bold" title="Bold"><span>B</span></button>
                                <button type="button" class="bluinq-notes-btn bluinq-notes-icon-i" data-cmd="italic" title="Italic"><span>I</span></button>
                                <button type="button" class="bluinq-notes-btn bluinq-notes-icon-u" data-cmd="underline" title="Underline"><span>U</span></button>
                                <button type="button" class="bluinq-notes-btn bluinq-notes-icon-svg" data-cmd="insertOrderedList" title="Numbered list">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg>
                                </button>
                                <button type="button" class="bluinq-notes-btn bluinq-notes-icon-svg" data-cmd="insertUnorderedList" title="Bullets">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
                                </button>
                            </div>
                            <div id="bluinq-notes-body" class="bluinq-notes-body" contenteditable="true" data-placeholder="Write notes here"></div>
                        </div>
                    </div>
                    <div class="bluinq-form-group bluinq-form-col6">
                        <label class="bluinq-form-label">Upload Plans</label>
                        <div class="bluinq-file-upload-wrapper">
                            <label class="bluinq-file-upload-zone" for="bluinq_upload_plans" data-upload-id="bluinq_upload_plans">
                                <input type="file" id="bluinq_upload_plans" name="upload_plans[]" multiple class="bluinq-file-upload-input" tabindex="-1">
                                <span class="bluinq-file-upload-text">Drag files here or <span class="bluinq-file-upload-browse">browse</span></span>
                                <span class="bluinq-file-status" data-for="bluinq_upload_plans">No file chosen</span>
                            </label>
                            <div class="bluinq-file-attached-list" data-for="bluinq_upload_plans"></div>
                        </div>
                    </div>
                    <div class="bluinq-form-group bluinq-form-col6">
                        <label class="bluinq-form-label">Upload Document</label>
                        <div class="bluinq-file-upload-wrapper">
                            <label class="bluinq-file-upload-zone" for="bluinq_upload_document" data-upload-id="bluinq_upload_document">
                                <input type="file" id="bluinq_upload_document" name="upload_document[]" multiple class="bluinq-file-upload-input" tabindex="-1">
                                <span class="bluinq-file-upload-text">Drag files here or <span class="bluinq-file-upload-browse">browse</span></span>
                                <span class="bluinq-file-status" data-for="bluinq_upload_document">No file chosen</span>
                            </label>
                            <div class="bluinq-file-attached-list" data-for="bluinq_upload_document"></div>
                        </div>
                    </div>
                    <div class="bluinq-form-group">
                        <label class="bluinq-form-label" for="assigned_to">Assigned To</label>
                        <select id="assigned_to" name="assigned_to" class="bluinq-form-select select2-single">
                            <option value="">Select user</option>
                            <option value="gm" selected>GM</option>
                            <option value="ajs">AJS</option>
                            <option value="sb">SB</option>
                            <option value="pep">PEP</option>
                            <option value="jdr">JDR</option>
                            <option value="js">JS</option>
                        </select>
                    </div>
                    <div class="bluinq-form-group">
                        <label class="bluinq-form-label" for="checked_by">Checked By</label>
                        <select id="checked_by" name="checked_by" class="bluinq-form-select select2-single">
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

            <div class="bluinq-form-actions">
                <button type="submit" class="btn btn-add-bluinq">Add Job (BLUINQ)</button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    @endpush
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            var notesBody = document.getElementById('bluinq-notes-body');
            var noteBtns = document.querySelectorAll('.bluinq-notes-btn');
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
            document.getElementById('bluinqAddForm').addEventListener('submit', function() {
                document.getElementById('bluinq_notes').value = notesBody.innerHTML;
            });
            $('.select2-single').select2({ width: '100%', allowClear: false });
            $('.bluinq-file-upload-zone').each(function() {
                var $zone = $(this);
                var $input = $zone.find('.bluinq-file-upload-input');
                var uploadId = $zone.data('upload-id');
                var $status = $('[data-for="' + uploadId + '"].bluinq-file-status');
                var $attachedList = $('.bluinq-file-attached-list[data-for="' + uploadId + '"]');
                $input.on('change', function() {
                    var files = this.files;
                    $status.text(files.length ? files.length + ' file(s) chosen' : 'No file chosen');
                    $attachedList.find('.bluinq-file-attached-item').remove();
                    for (var i = 0; i < files.length; i++) {
                        var name = $('<div>').text(files[i].name).html();
                        $attachedList.append(
                            '<div class="bluinq-file-attached-item" data-index="' + i + '"><span class="bluinq-file-attached-name">' + name + '</span><button type="button" class="bluinq-file-attached-remove" aria-label="Remove">Remove</button></div>'
                        );
                    }
                    this.blur();
                });
                $attachedList.on('click', '.bluinq-file-attached-remove', function() {
                    var $item = $(this).closest('.bluinq-file-attached-item');
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
                    $attachedList.find('.bluinq-file-attached-item').each(function(j) {
                        $(this).data('index', j);
                    });
                });
            });
        });
        document.getElementById('bluinqAddForm').addEventListener('submit', function(e) {
            e.preventDefault();
        });
    </script>
@endpush

