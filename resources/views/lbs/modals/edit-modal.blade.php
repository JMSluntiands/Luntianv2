<div class="job-view-modal-overlay" id="jobViewEditModalOverlay" aria-hidden="true">
    <div class="job-view-modal job-view-modal-edit" id="jobViewEditModal" role="dialog" aria-modal="true" aria-labelledby="jobViewEditModalTitle">
        <div class="job-view-modal-header">
            <h2 class="job-view-modal-title" id="jobViewEditModalTitle">Edit</h2>
        </div>
        <div class="job-view-modal-body">
            <div class="job-view-edit-form job-view-edit-form-client" id="jobViewEditFormClient" hidden>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-client-log-date">Log Date</label>
                    <input type="datetime-local" id="edit-client-log-date" class="job-view-form-input job-view-form-input-readonly" value="2026-03-06T16:40" readonly autocomplete="off">
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-client-ref">Client Reference</label>
                    <input type="text" id="edit-client-ref" class="job-view-form-input job-view-form-input-readonly" value="172726" readonly autocomplete="off">
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-job-number">Job Number</label>
                    <input type="text" id="edit-job-number" class="job-view-form-input job-view-form-input-readonly" value="{{ $jobId ?? '42376' }}" readonly autocomplete="off">
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-compliance">Compliance</label>
                    <input type="text" id="edit-compliance" class="job-view-form-input" value="2022 Whole of Home (WOH)" autocomplete="off">
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-client-name">Client</label>
                    <input type="text" id="edit-client-name" class="job-view-form-input" value="Summit Homes Group" autocomplete="off">
                </div>
            </div>
            <div class="job-view-edit-form job-view-edit-form-job" id="jobViewEditFormJob" hidden>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-job-status">Job Status</label>
                    <select id="edit-job-status" class="job-view-form-input select2-single" autocomplete="off">
                        <option value="Pending">Pending</option>
                        <option value="Accepted" selected>Accepted</option>
                        <option value="Allocated">Allocated</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-job-address">Job Address</label>
                    <input type="text" id="edit-job-address" class="job-view-form-input" value="Lot 183 Seapray Drive, Dollywup, WA 6230" autocomplete="off">
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-priority">Priority</label>
                    <select id="edit-priority" class="job-view-form-input select2-single" autocomplete="off">
                        <option value="High 1 day" selected>High 1 day</option>
                        <option value="Standard 2 days">Standard 2 days</option>
                    </select>
                </div>
                <div class="job-view-form-group">
                    <label class="job-view-form-label" for="edit-job-type">Job Type</label>
                    <input type="text" id="edit-job-type" class="job-view-form-input" value="19 DB Base Model · 19 Design Builder Model" autocomplete="off">
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
                            <p>This job requires <strong>priority review</strong> due to the client's timeline. Please ensure all <em>compliance checks</em> are completed before submission.</p>
                            <ul>
                                <li><strong>Verify</strong> 2022 WOH requirements</li>
                                <li>Confirm <em>address details</em> with site plan</li>
                                <li>Check energy rating documentation</li>
                            </ul>
                            <p>Contact the client if any <strong><em>discrepancies</em></strong> are found.</p>
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
