<div class="job-view-modal-overlay" id="jobViewAddFilesModalOverlay" aria-hidden="true">
    <div class="job-view-modal job-view-modal-add-files" id="jobViewAddFilesModal" role="dialog" aria-modal="true" aria-labelledby="jobViewAddFilesModalTitle">
        <div class="job-view-modal-header">
            <h2 class="job-view-modal-title" id="jobViewAddFilesModalTitle">Add Files</h2>
        </div>
        <div class="job-view-modal-body">
            <p class="job-view-modal-label">Adding files to <strong id="jobViewAddFilesModalSection">this section</strong>.</p>
            <div class="job-view-modal-existing" id="jobViewModalExistingWrap">
                <h3 class="job-view-modal-existing-title">Existing files</h3>
                <ul class="job-view-modal-files" id="jobViewModalExistingFiles"></ul>
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
