<div class="job-view-modal-overlay" id="jobViewEditModalOverlay" aria-hidden="true">
    <div class="job-view-modal job-view-modal-edit" id="jobViewEditModal" role="dialog" aria-modal="true" aria-labelledby="jobViewEditModalTitle">
        <div class="job-view-modal-header">
            <h2 class="job-view-modal-title" id="jobViewEditModalTitle">Edit</h2>
        </div>
        <div class="job-view-modal-body">
            <div class="job-view-edit-form job-view-edit-form-client" id="jobViewEditFormClient" hidden>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-client-log-date">Log Date</label>
                    <input
                        type="datetime-local"
                        id="edit-client-log-date"
                        class="job-view-form-input job-view-form-input-readonly"
                        value="{{ !empty($job->log_date) ? \Carbon\Carbon::parse($job->log_date)->format('Y-m-d\TH:i') : '' }}"
                        readonly
                        autocomplete="off">
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-client-ref">Client Reference</label>
                    <input
                        type="text"
                        id="edit-client-ref"
                        class="job-view-form-input"
                        value="{{ $job->client_reference_no ?? '' }}"
                        autocomplete="off">
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-job-number">Job Number</label>
                    <input
                        type="text"
                        id="edit-job-number"
                        class="job-view-form-input"
                        value="{{ $job->job_reference_no ?? ($job->reference ?? $jobId ?? '') }}"
                        autocomplete="off">
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-compliance">Compliance</label>
                    <select
                        id="edit-compliance"
                        class="job-view-form-input select2-single"
                        autocomplete="off">
                        @foreach($compliances ?? [] as $c)
                            <option value="{{ $c->column }}" @selected(($job->ncc_compliance ?? '') === ($c->column ?? ''))>
                                {{ $c->column ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-client-name">Client</label>
                    <select
                        id="edit-client-name"
                        class="job-view-form-input select2-single"
                        autocomplete="off">
                        @foreach($clientAccounts ?? [] as $client)
                            @php
                                $displayName = $client->client_account_name ?? $client->client_code ?? '';
                                $currentId = $job->client_account_id ?? null;
                            @endphp
                            <option value="{{ $client->client_account_id }}"
                                    data-name="{{ $displayName }}"
                                    @selected((int) $currentId === (int) $client->client_account_id)>
                                {{ $displayName }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="job-view-edit-form job-view-edit-form-job" id="jobViewEditFormJob" hidden>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-job-status">Job Status</label>
                    <select id="edit-job-status" class="job-view-form-input select2-single" autocomplete="off">
                        @foreach($statuses ?? [] as $status)
                            <option value="{{ $status->name }}" @selected(($job->job_status ?? '') === $status->name)>{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-job-address">Job Address</label>
                    <input
                        type="text"
                        id="edit-job-address"
                        class="job-view-form-input"
                        value="{{ $job->address_client ?? '' }}"
                        autocomplete="off">
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-priority">Priority</label>
                    <select id="edit-priority" class="job-view-form-input select2-single" autocomplete="off">
                        @foreach($priorities ?? [] as $priority)
                            <option value="{{ $priority->name }}" @selected(($job->priority ?? '') === $priority->name)>{{ $priority->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-job-type">Job Type</label>
                    <input
                        type="text"
                        id="edit-job-type"
                        class="job-view-form-input"
                        value="{{ $job->job_type ?? '' }}"
                        autocomplete="off">
                </div>
            </div>
            <div class="job-view-edit-form job-view-edit-form-notes" id="jobViewEditFormNotes" hidden>
                <div class="job-view-form-group">
                    <label class="job-view-form-label">Notes</label>
                    <div class="job-view-modal-notes-editor">
                        <div class="job-view-modal-notes-toolbar">
                            <button type="button" class="job-view-comment-btn" data-cmd="bold" title="Bold"><span>B</span></button>
                            <button type="button" class="job-view-comment-btn" data-cmd="italic" title="Italic"><span>I</span></button>
                            <button type="button" class="job-view-comment-btn" data-cmd="underline" title="Underline"><span>U</span></button>
                            <button type="button" class="job-view-comment-btn job-view-comment-btn-icon" data-cmd="insertUnorderedList" title="Bullets">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
                            </button>
                            <button type="button" class="job-view-comment-btn job-view-comment-btn-icon" data-cmd="insertOrderedList" title="Numbered list">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg>
                            </button>
                        </div>
                        <div id="jobViewEditNotesBody" class="job-view-modal-notes-body" contenteditable="true" data-placeholder="Enter notes...">
                            {!! $job->notes ?: '' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="job-view-modal-footer">
            <button type="button" class="job-view-modal-btn job-view-modal-btn-cancel" data-job-view-close-edit>Cancel</button>
            <button type="button" class="job-view-modal-btn job-view-modal-btn-primary" id="jobViewEditSaveBtn">Save</button>
        </div>
    </div>
</div>
