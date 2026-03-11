<div class="job-view-modal-overlay" id="jobViewAddFilesModalOverlay" aria-hidden="true">
    <div class="job-view-modal job-view-modal-add-files" id="jobViewAddFilesModal" role="dialog" aria-modal="true" aria-labelledby="jobViewAddFilesModalTitle">
        <div class="job-view-modal-header">
            <h2 class="job-view-modal-title" id="jobViewAddFilesModalTitle">Add Files</h2>
        </div>
        <div class="job-view-modal-body">
            <p class="job-view-modal-label">Adding files to <strong id="jobViewAddFilesModalSection">this section</strong>.</p>
            <div class="job-view-modal-existing" id="jobViewModalExistingWrap">
                <h3 class="job-view-modal-existing-title">Existing files</h3>
                <ul class="job-view-modal-files" id="jobViewModalExistingFiles">
                    <li class="job-view-modal-file-item">
                        <span class="job-view-modal-file-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg>
                        </span>
                        <span class="job-view-modal-file-name">172726_PLANS_LBS42376.pdf</span>
                        <div class="job-view-modal-file-actions">
                            <a href="#" class="job-view-modal-file-btn" title="Download" aria-label="Download">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                            </a>
                            <a href="#" class="job-view-modal-file-btn" title="View" aria-label="View">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <button type="button" class="job-view-modal-file-btn job-view-modal-file-btn-danger" title="Delete" aria-label="Delete" data-job-view-modal-delete-file>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                            </button>
                        </div>
                    </li>
                    <li class="job-view-modal-file-item">
                        <span class="job-view-modal-file-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg>
                        </span>
                        <span class="job-view-modal-file-name">Compliance_doc_LBS42376.pdf</span>
                        <div class="job-view-modal-file-actions">
                            <a href="#" class="job-view-modal-file-btn" title="Download" aria-label="Download">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                            </a>
                            <a href="#" class="job-view-modal-file-btn" title="View" aria-label="View">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <button type="button" class="job-view-modal-file-btn job-view-modal-file-btn-danger" title="Delete" aria-label="Delete" data-job-view-modal-delete-file>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                            </button>
                        </div>
                    </li>
                </ul>
                <p class="job-view-modal-no-files" id="jobViewModalNoFiles" hidden>No files in this section yet.</p>
            </div>
            <div class="job-view-modal-file-zone">
                <input type="file" id="jobViewAddFilesInput" multiple class="job-view-modal-file-input" autocomplete="off">
                <label for="jobViewAddFilesInput" class="job-view-modal-file-label">Choose files or drag here</label>
            </div>
            <div class="job-view-modal-selected-wrap" id="jobViewModalSelectedWrap" hidden>
                <h3 class="job-view-modal-existing-title">Selected files</h3>
                <ul class="job-view-modal-files job-view-modal-selected-files" id="jobViewModalSelectedFiles"></ul>
            </div>
            <div class="job-view-modal-checker-notes" id="jobViewModalCheckerNotes" hidden>
                <h3 class="job-view-modal-existing-title">Notes</h3>
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
                    <div class="job-view-modal-notes-body" contenteditable="true" data-placeholder="Add notes for this upload..."></div>
                </div>
            </div>
        </div>
        <div class="job-view-modal-footer">
            <button type="button" class="job-view-modal-btn job-view-modal-btn-cancel" data-job-view-close-add>Cancel</button>
            <button type="button" class="job-view-modal-btn job-view-modal-btn-primary" id="jobViewAddFilesUploadBtn">Upload</button>
        </div>
    </div>
</div>
