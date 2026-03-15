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
            @php
                $preRef = isset($duplicateJob) ? ($duplicateJob->reference_no ?? '') : 'JOBS0823-003';
                $selCompliance = isset($duplicateJob) ? ($duplicateJob->compliance_id ?? null) : ($defaultComplianceId ?? null);
                $selClient = isset($duplicateJob) ? ($duplicateJob->client_account_id ?? null) : ($defaultClientAccountId ?? null);
                $selPriority = isset($duplicateJob) ? ($duplicateJob->priority_id ?? null) : ($defaultPriorityId ?? null);
                $selJobRequest = isset($duplicateJob) ? ($duplicateJob->job_request_id ?? null) : ($defaultJobRequestId ?? null);
            @endphp
            <div class="lbs-form-card">
                <div class="lbs-form-card-header">
                    <h2 class="lbs-form-section-title">Client Details</h2>
                    <span class="lbs-form-ref" id="jobReferenceContent">{{ $preRef ?: 'JOBS0823-003' }}</span>
                </div>
                <div class="lbs-form-grid">
                    <div class="lbs-form-group">
                        <label class="lbs-form-label" for="reference_no">Reference No.</label>
                        <input type="text" id="reference_no" name="reference_no" class="lbs-form-input {{ isset($duplicateJob) ? 'lbs-form-input-readonly' : '' }}" placeholder="Enter Reference Number" autocomplete="off" value="{{ isset($duplicateJob) ? e($duplicateJob->reference_no ?? '') : '' }}" {{ isset($duplicateJob) ? 'readonly' : '' }}>
                    </div>
                    <div class="lbs-form-group">
                        <label class="lbs-form-label" for="client_reference">Client Reference</label>
                        <input type="text" id="client_reference" name="client_reference" class="lbs-form-input {{ isset($duplicateJob) ? 'lbs-form-input-readonly' : '' }}" placeholder="Enter Client Reference" autocomplete="off" value="{{ isset($duplicateJob) ? e($duplicateJob->client_reference ?? '') : '' }}" {{ isset($duplicateJob) ? 'readonly' : '' }}>
                    </div>
                    <div class="lbs-form-group">
                        <label class="lbs-form-label" for="compliance">Compliance</label>
                        <select id="compliance" name="compliance" class="lbs-form-select select2-single" autocomplete="off">
                            <option value="">Select compliance</option>
                            @foreach($compliances ?? [] as $c)
                                <option value="{{ $c->id }}" {{ $selCompliance !== null && (int) $selCompliance === (int) $c->id ? 'selected' : '' }}>
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
                                <option value="{{ $client->client_account_id }}" {{ $selClient !== null && (int) $selClient === (int) $client->client_account_id ? 'selected' : '' }}>
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
                        <input type="text" id="job_address" name="job_address" class="lbs-form-input" placeholder="Complete Address" autocomplete="off" value="{{ isset($duplicateJob) ? e($duplicateJob->job_address ?? '') : '' }}">
                    </div>
                    <div class="lbs-form-row-three">
                        <div class="lbs-form-group">
                            <label class="lbs-form-label" for="priority">Priority</label>
                            <select id="priority" name="priority" class="lbs-form-select select2-single" autocomplete="off">
                                <option value="">Select priority</option>
                                @foreach($priorities ?? [] as $priority)
                                    <option value="{{ $priority->id }}" {{ $selPriority !== null && (int) $selPriority === (int) $priority->id ? 'selected' : '' }}>
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
                                    <option value="{{ $jobRequest->id }}" {{ $selJobRequest !== null && (int) $selJobRequest === (int) $jobRequest->id ? 'selected' : '' }}>
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

            @php
                $selAssigned = isset($duplicateJob) ? ($duplicateJob->staff_id ?? 'GM') : 'GM';
                $selChecked = isset($duplicateJob) ? ($duplicateJob->checker_id ?? 'GM') : 'GM';
            @endphp
            <div class="lbs-form-card">
                <h2 class="lbs-form-section-title">Assignment</h2>
                <div class="lbs-form-grid">
                    <div class="lbs-form-group">
                        <label class="lbs-form-label" for="assigned_to">Assigned To</label>
                        <select id="assigned_to" name="assigned_to" class="lbs-form-select select2-single">
                            <option value="">Select user</option>
                            <option value="GM" {{ strtoupper($selAssigned ?? '') === 'GM' ? 'selected' : '' }}>GM</option>
                            @foreach($assignmentUsers ?? [] as $user)
                                @if(strtoupper($user->unique_code ?? '') !== 'GM')
                                    <option value="{{ $user->unique_code }}" {{ strtoupper($user->unique_code ?? '') === strtoupper($selAssigned ?? '') ? 'selected' : '' }}>{{ $user->unique_code }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="lbs-form-group">
                        <label class="lbs-form-label" for="checked_by">Checked By</label>
                        <select id="checked_by" name="checked_by" class="lbs-form-select select2-single">
                            <option value="">Select user</option>
                            <option value="GM" {{ strtoupper($selChecked ?? '') === 'GM' ? 'selected' : '' }}>GM</option>
                            @foreach($assignmentUsers ?? [] as $user)
                                @if(strtoupper($user->unique_code ?? '') !== 'GM')
                                    <option value="{{ $user->unique_code }}" {{ strtoupper($user->unique_code ?? '') === strtoupper($selChecked ?? '') ? 'selected' : '' }}>{{ $user->unique_code }}</option>
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

            @if(isset($duplicateJob) && ($duplicateJob->notes ?? '') !== '')
                (function() {
                    var notesHtml = {!! json_encode($duplicateJob->notes ?? '') !!};
                    var notesEl = document.getElementById('lbs-notes-body');
                    var notesHidden = document.getElementById('notes');
                    if (notesEl) notesEl.innerHTML = notesHtml;
                    if (notesHidden) notesHidden.value = notesHtml;
                })();
            @endif

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

