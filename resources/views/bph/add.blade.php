@extends('layouts.dashboard')

@section('title', 'Add New Job (BPH)')

@section('body_class', 'page-bph-add')

@section('content')
    <div class="bph-add-page">
        <div class="bph-form-header">
            <h1 class="bph-form-title">Add New Job (BPH)</h1>
            <p class="bph-form-subtitle">Fill in the form below to create a new BPH job.</p>
        </div>

        <form id="bphAddForm" action="#" method="POST" autocomplete="off">
            @csrf
            <div class="bph-form-card">
                <div class="bph-form-card-header">
                    <h2 class="bph-form-section-title">Client Details</h2>
                    <span class="bph-form-ref" id="bphJobReferenceContent">JOB0903-001</span>
                </div>
                <div class="bph-form-grid">
                    <div class="bph-form-group bph-form-col12 bph-form-group-urgent">
                        <label class="bph-form-urgent-label" for="urgent_job">
                            <input type="checkbox" id="urgent_job" name="urgent_job" value="1" class="bph-form-checkbox" autocomplete="off">
                            <span class="bph-form-urgent-title">Urgent Job (YES)</span>
                        </label>
                    </div>
                    <div class="bph-form-group bph-form-col6">
                        <label class="bph-form-label" for="ncc_compliance">NCC Compliance</label>
                        <select id="ncc_compliance" name="ncc_compliance" class="bph-form-select select2-single">
                            <option value="">Select compliance</option>
                            <option value="2019" selected>2019</option>
                            <option value="2022_woh">2022 Whole of Home (WOH)</option>
                            <option value="2023_woh">2023 Whole of Home (WOH)</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="bph-form-group bph-form-col6">
                        <label class="bph-form-label" for="job_type_request">Job Type Request</label>
                        <select id="job_type_request" name="job_type_request" class="bph-form-select select2-single">
                            <option value="">Select job type</option>
                            <option value="just_a_query" selected>Just a query</option>
                            <option value="prelim">Prelim</option>
                            <option value="ea_bph_1s">EA_BPH_1S</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="bph-form-group">
                        <label class="bph-form-label" for="job_number">Job Number <span class="bph-form-required">*</span></label>
                        <input type="text" id="job_number" name="job_number" class="bph-form-input" placeholder="e.g. 12345B" required autocomplete="off">
                        <span class="bph-form-hint">Enter 5 digits followed by letter B (e.g. 12345B)</span>
                    </div>
                    <div class="bph-form-group">
                        <label class="bph-form-label" for="client_name">Client Name <span class="bph-form-required">*</span></label>
                        <input type="text" id="client_name" name="client_name" class="bph-form-input" placeholder="Enter client name" required autocomplete="off">
                    </div>
                    <div class="bph-form-group">
                        <label class="bph-form-label" for="contact_email">Contact Email <span class="bph-form-required">*</span></label>
                        <input type="email" id="contact_email" name="contact_email" class="bph-form-input" placeholder="Enter contact email" required autocomplete="off">
                    </div>
                    <div class="bph-form-group bph-form-col6">
                        <label class="bph-form-label" for="please_select">Please Select</label>
                        <select id="please_select" name="please_select" class="bph-form-select select2-single">
                            <option value="">Select option</option>
                            <option value="option1">Option 1</option>
                            <option value="option2">Option 2</option>
                            <option value="option3">Option 3</option>
                        </select>
                    </div>
                    <div class="bph-form-group full-width">
                        <label class="bph-form-label" for="notes-body">Notes (BPH)</label>
                        <input type="hidden" name="notes" id="bph_notes" autocomplete="off">
                        <div class="bph-notes-editor">
                            <div class="bph-notes-toolbar">
                                <button type="button" class="bph-notes-btn bph-notes-icon-b" data-cmd="bold" title="Bold"><span>B</span></button>
                                <button type="button" class="bph-notes-btn bph-notes-icon-i" data-cmd="italic" title="Italic"><span>I</span></button>
                                <button type="button" class="bph-notes-btn bph-notes-icon-u" data-cmd="underline" title="Underline"><span>U</span></button>
                                <button type="button" class="bph-notes-btn bph-notes-icon-svg" data-cmd="insertOrderedList" title="Numbered list">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg>
                                </button>
                                <button type="button" class="bph-notes-btn bph-notes-icon-svg" data-cmd="insertUnorderedList" title="Bullets">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
                                </button>
                            </div>
                            <div id="bph-notes-body" class="bph-notes-body" contenteditable="true" data-placeholder="Write notes here"></div>
                        </div>
                    </div>
                    <div class="bph-form-group bph-form-col6">
                        <label class="bph-form-label">Upload Plans</label>
                        <div class="bph-file-upload-wrapper">
                            <label class="bph-file-upload-zone" for="bph_upload_plans" data-upload-id="bph_upload_plans">
                                <input type="file" id="bph_upload_plans" name="upload_plans[]" multiple class="bph-file-upload-input" tabindex="-1">
                                <span class="bph-file-upload-text">Drag files here or <span class="bph-file-upload-browse">browse</span></span>
                                <span class="bph-file-status" data-for="bph_upload_plans">No file chosen</span>
                            </label>
                            <div class="bph-file-attached-list" data-for="bph_upload_plans"></div>
                        </div>
                    </div>
                    <div class="bph-form-group bph-form-col6">
                        <label class="bph-form-label">Upload Document</label>
                        <div class="bph-file-upload-wrapper">
                            <label class="bph-file-upload-zone" for="bph_upload_document" data-upload-id="bph_upload_document">
                                <input type="file" id="bph_upload_document" name="upload_document[]" multiple class="bph-file-upload-input" tabindex="-1">
                                <span class="bph-file-upload-text">Drag files here or <span class="bph-file-upload-browse">browse</span></span>
                                <span class="bph-file-status" data-for="bph_upload_document">No file chosen</span>
                            </label>
                            <div class="bph-file-attached-list" data-for="bph_upload_document"></div>
                        </div>
                    </div>
                    <div class="bph-form-group">
                        <label class="bph-form-label" for="assigned_to">Assigned To</label>
                        <select id="assigned_to" name="assigned_to" class="bph-form-select select2-single">
                            <option value="">Select user</option>
                            <option value="gm" selected>GM</option>
                            <option value="ajs">AJS</option>
                            <option value="sb">SB</option>
                            <option value="pep">PEP</option>
                            <option value="jdr">JDR</option>
                            <option value="js">JS</option>
                        </select>
                    </div>
                    <div class="bph-form-group">
                        <label class="bph-form-label" for="checked_by">Checked By</label>
                        <select id="checked_by" name="checked_by" class="bph-form-select select2-single">
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

            <div class="bph-form-actions">
                <button type="submit" class="btn btn-add-bph">Add Job (BPH)</button>
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
            var notesBody = document.getElementById('bph-notes-body');
            var noteBtns = document.querySelectorAll('.bph-notes-btn');
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
            document.getElementById('bphAddForm').addEventListener('submit', function() {
                document.getElementById('bph_notes').value = notesBody.innerHTML;
            });
            $('.select2-single').select2({ width: '100%', allowClear: false });
            $('.bph-file-upload-zone').each(function() {
                var $zone = $(this);
                var $input = $zone.find('.bph-file-upload-input');
                var uploadId = $zone.data('upload-id');
                var $status = $('[data-for="' + uploadId + '"].bph-file-status');
                var $attachedList = $('.bph-file-attached-list[data-for="' + uploadId + '"]');
                $input.on('change', function() {
                    var files = this.files;
                    $status.text(files.length ? files.length + ' file(s) chosen' : 'No file chosen');
                    $attachedList.find('.bph-file-attached-item').remove();
                    for (var i = 0; i < files.length; i++) {
                        var name = $('<div>').text(files[i].name).html();
                        $attachedList.append(
                            '<div class="bph-file-attached-item" data-index="' + i + '"><span class="bph-file-attached-name">' + name + '</span><button type="button" class="bph-file-attached-remove" aria-label="Remove">Remove</button></div>'
                        );
                    }
                    this.blur();
                });
                $attachedList.on('click', '.bph-file-attached-remove', function() {
                    var $item = $(this).closest('.bph-file-attached-item');
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
                    $attachedList.find('.bph-file-attached-item').each(function(j) {
                        $(this).data('index', j);
                    });
                });
            });
        });
        document.getElementById('bphAddForm').addEventListener('submit', function(e) {
            e.preventDefault();
        });
    </script>
@endpush
