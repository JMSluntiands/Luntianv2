<div class="job-view-modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-opacity duration-200" id="jobViewAddFilesModalOverlay" aria-hidden="true">
    <div class="job-view-modal w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800" id="jobViewAddFilesModal" role="dialog" aria-modal="true" aria-labelledby="jobViewAddFilesModalTitle">
        <div class="sticky top-0 z-10 border-b border-slate-200 bg-white px-5 py-4 dark:border-slate-700 dark:bg-slate-800">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="jobViewAddFilesModalTitle">Add Files</h2>
        </div>
        <div class="px-5 py-4 space-y-4">
            <p class="text-sm text-slate-600 dark:text-slate-300">Adding files to <strong id="jobViewAddFilesModalSection" class="font-semibold text-slate-800 dark:text-white">this section</strong>.</p>
            <div id="jobViewModalExistingWrap">
                <h3 class="mb-2 text-sm font-semibold text-slate-700 dark:text-slate-200">Existing files</h3>
                <ul class="mb-4 space-y-1 rounded-lg border border-slate-200 bg-slate-50/50 p-2 dark:border-slate-600 dark:bg-slate-800/30" id="jobViewModalExistingFiles"></ul>
                <p class="text-sm text-slate-500 dark:text-slate-400" id="jobViewModalNoFiles" hidden>No files in this section yet.</p>
            </div>
            <div class="rounded-xl border-2 border-dashed border-slate-300 bg-slate-50/50 p-6 text-center transition-colors hover:border-slate-400 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800/30 dark:hover:border-slate-500 dark:hover:bg-slate-800/50">
                <input type="file" id="jobViewAddFilesInput" multiple class="sr-only" autocomplete="off">
                <label for="jobViewAddFilesInput" class="cursor-pointer text-sm font-medium text-slate-600 dark:text-slate-400">Choose files or drag here</label>
            </div>
            <div id="jobViewModalSelectedWrap" hidden>
                <h3 class="mb-2 text-sm font-semibold text-slate-700 dark:text-slate-200">Selected files</h3>
                <ul class="mb-4 space-y-1 rounded-lg border border-slate-200 bg-slate-50/50 p-2 dark:border-slate-600 dark:bg-slate-800/30" id="jobViewModalSelectedFiles"></ul>
            </div>
            <div id="jobViewModalCheckerNotes" hidden>
                <h3 class="mb-2 text-sm font-semibold text-slate-700 dark:text-slate-200">Notes</h3>
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3 dark:border-slate-600 dark:bg-slate-800/30">
                    <div class="mb-2 flex gap-1">
                        <button type="button" class="rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="bold" title="Bold"><span class="font-bold">B</span></button>
                        <button type="button" class="rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="italic" title="Italic"><span class="italic">I</span></button>
                        <button type="button" class="rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="underline" title="Underline"><span class="underline">U</span></button>
                        <button type="button" class="rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="insertUnorderedList" title="Bullets"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg></button>
                        <button type="button" class="rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="insertOrderedList" title="Numbered list"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg></button>
                    </div>
                    <div class="job-view-modal-notes-body min-h-[80px] rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200" contenteditable="true" data-placeholder="Add notes for this upload..."></div>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600" data-job-view-close-add>Cancel</button>
            <button type="button" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" id="jobViewAddFilesUploadBtn">Upload</button>
        </div>
    </div>
</div>
