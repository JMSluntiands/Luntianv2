<style>
{{-- Modal overlay & box --}}
.job-view-modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.35); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1rem; opacity: 0; visibility: hidden; transition: opacity 0.28s ease-out, visibility 0.28s ease-out; }
.job-view-modal-overlay.is-open { opacity: 1; visibility: visible; }
.job-view-modal { background: rgba(30,41,59,0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(71,85,105,0.5); border-radius: 14px; max-width: 520px; width: 100%; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,255,255,0.05) inset; opacity: 0; transform: scale(0.92); transition: opacity 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) 0.05s, transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) 0.05s; }
.job-view-modal-overlay.is-open .job-view-modal { opacity: 1; transform: scale(1); }
.job-view-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1px solid #334155; }
.job-view-modal-title { font-size: 1.125rem; font-weight: 600; color: #e2e8f0; margin: 0; }
.job-view-modal-close { position: relative; z-index: 2; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; line-height: 1; color: #94a3b8; background: none; border: none; border-radius: 8px; cursor: pointer; transition: color 0.2s, background 0.2s; pointer-events: auto; }
.job-view-modal-close:hover { color: #e2e8f0; background: rgba(255,255,255,0.08); }
.job-view-modal-body { padding: 1.5rem; overflow-y: auto; flex: 1; min-height: 0; }
.job-view-modal-placeholder, .job-view-modal-label { font-size: 0.9375rem; color: #94a3b8; margin: 0 0 1rem 0; }
.job-view-modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1rem 1.5rem; border-top: 1px solid #334155; }
.job-view-modal-btn { padding: 0.5rem 1.25rem; font-size: 0.875rem; font-weight: 600; border-radius: 8px; cursor: pointer; font-family: inherit; transition: background 0.2s, color 0.2s, transform 0.2s; }
.job-view-modal-btn-cancel { background: transparent; border: 1px solid #334155; color: #94a3b8; }
.job-view-modal-btn-cancel:hover { background: rgba(255,255,255,0.06); color: #e2e8f0; }
.job-view-modal-btn-primary { background: #2563eb; border: none; color: #fff; }
.job-view-modal-btn-primary:hover { background: #1d4ed8; transform: scale(1.02); }
{{-- Edit form --}}
.job-view-form-group { margin-bottom: 1rem; }
.job-view-form-group:last-child { margin-bottom: 0; }
.job-view-form-label { display: block; font-size: 0.8125rem; font-weight: 600; color: #94a3b8; margin-bottom: 0.35rem; }
.job-view-form-input { width: 100%; padding: 0.5rem 0.75rem; font-size: 0.9375rem; color: #e2e8f0; background: #0f172a; border: 1px solid #334155; border-radius: 8px; font-family: inherit; }
.job-view-form-input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 2px rgba(37,99,235,0.25); }
.job-view-form-input-readonly.job-view-form-input,
.job-view-form-input[readonly] { background: #1e293b; color: #94a3b8; cursor: default; }
.job-view-form-input-readonly.job-view-form-input:focus,
.job-view-form-input[readonly]:focus { border-color: #334155; box-shadow: none; }
.job-view-modal-notes-editor { border: 1px solid #334155; border-radius: 10px; background: #0f172a; overflow: hidden; }
.job-view-modal-notes-toolbar { display: flex; align-items: center; gap: 2px; padding: 6px 10px; border-bottom: 1px solid #334155; background: #1e293b; }
.job-view-modal-notes-editor .job-view-comment-btn.active { background: #2c5282; color: #e2e8f0; }
.job-view-modal-notes-editor .job-view-comment-btn.active:hover { background: #2b6cb0; color: #fff; }
.job-view-modal-notes-body { min-height: 140px; max-height: 240px; padding: 0.625rem 0.875rem; color: #e2e8f0; font-size: 0.9375rem; line-height: 1.5; outline: none; overflow-y: auto; }
.job-view-modal-notes-body:empty::before { content: attr(data-placeholder); color: #64748b; }
.job-view-modal-notes-body ul, .job-view-modal-notes-body ol { margin: 0.5em 0; padding-left: 1.5em; }
{{-- Existing files in add-files modal --}}
.job-view-modal-existing { margin-bottom: 1.25rem; }
.job-view-modal-existing-title { font-size: 0.875rem; font-weight: 600; color: #94a3b8; margin: 0 0 0.5rem 0; }
.job-view-modal-files { list-style: none; margin: 0; padding: 0; border: 1px solid #334155; border-radius: 10px; background: rgba(15,23,42,0.5); overflow: hidden; }
.job-view-modal-file-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0.75rem; border-bottom: 1px solid #334155; transition: background 0.2s; }
.job-view-modal-file-item:last-child { border-bottom: none; }
.job-view-modal-file-item:hover { background: rgba(255,255,255,0.04); }
.job-view-modal-file-icon { color: #f87171; flex-shrink: 0; }
.job-view-modal-file-name { font-size: 0.875rem; color: #e2e8f0; flex: 1; min-width: 0; }
.job-view-modal-file-actions { display: flex; align-items: center; gap: 0.25rem; }
.job-view-modal-file-btn { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; border-radius: 8px; color: #94a3b8; background: transparent; border: none; cursor: pointer; text-decoration: none; transition: color 0.2s, background 0.2s; }
.job-view-modal-file-btn:hover { color: #e2e8f0; background: rgba(255,255,255,0.08); }
.job-view-modal-file-btn-danger:hover { color: #f87171; background: rgba(248,113,113,0.15); }
.job-view-modal-no-files { font-size: 0.875rem; color: #64748b; margin: 0; padding: 0.75rem; }
.job-view-modal-selected-wrap { margin-top: 1rem; }
.job-view-modal-file-item-new .job-view-modal-file-actions { display: none; }
.job-view-modal-checker-notes { margin-top: 1.25rem; }
.job-view-modal-checker-notes .job-view-modal-notes-body { min-height: 100px; }
{{-- Add files --}}
.job-view-modal-file-zone { border: 2px dashed #334155; border-radius: 10px; padding: 2rem; text-align: center; transition: border-color 0.2s, background 0.2s; }
.job-view-modal-file-zone:hover { border-color: #475569; background: rgba(255,255,255,0.03); }
.job-view-modal-file-input { position: absolute; width: 0.1px; height: 0.1px; opacity: 0; overflow: hidden; z-index: -1; }
.job-view-modal-file-label { font-size: 0.875rem; color: #94a3b8; cursor: pointer; display: block; }
{{-- Light theme --}}
html[data-theme="light"] .job-view-modal-overlay { background: rgba(248,250,252,0.4); }
html[data-theme="light"] .job-view-modal { background: rgba(255,255,255,0.88); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-color: rgba(226,232,240,0.8); box-shadow: 0 25px 50px rgba(0,0,0,0.12), 0 0 0 1px rgba(255,255,255,0.8) inset; }
html[data-theme="light"] .job-view-modal-header { border-bottom-color: #e2e8f0; }
html[data-theme="light"] .job-view-modal-title { color: #1e293b; }
html[data-theme="light"] .job-view-modal-close { color: #64748b; }
html[data-theme="light"] .job-view-modal-close:hover { color: #1e293b; background: #f1f5f9; }
html[data-theme="light"] .job-view-modal-placeholder, html[data-theme="light"] .job-view-modal-label { color: #64748b; }
html[data-theme="light"] .job-view-form-input { background: #f8fafc; color: #1e293b; border-color: #e2e8f0; }
html[data-theme="light"] .job-view-form-input-readonly.job-view-form-input,
html[data-theme="light"] .job-view-form-input[readonly] { background: #f1f5f9; color: #64748b; }
html[data-theme="light"] .job-view-form-label { color: #64748b; }
html[data-theme="light"] .job-view-modal-notes-editor { border-color: #e2e8f0; background: #fff; }
html[data-theme="light"] .job-view-modal-notes-toolbar { border-bottom-color: #e2e8f0; background: #f8fafc; }
html[data-theme="light"] .job-view-modal-notes-editor .job-view-comment-btn.active { background: rgba(44,82,139,0.35); color: #1e40af; }
html[data-theme="light"] .job-view-modal-notes-editor .job-view-comment-btn.active:hover { background: rgba(44,82,139,0.45); color: #1e40af; }
html[data-theme="light"] .job-view-modal-notes-body { color: #1e293b; }
html[data-theme="light"] .job-view-modal-notes-body:empty::before { color: #94a3b8; }
html[data-theme="light"] .job-view-modal-file-zone { border-color: #e2e8f0; }
html[data-theme="light"] .job-view-modal-file-zone:hover { background: #f8fafc; border-color: #cbd5e1; }
html[data-theme="light"] .job-view-modal-file-label { color: #64748b; }
html[data-theme="light"] .job-view-modal-existing-title { color: #64748b; }
html[data-theme="light"] .job-view-modal-files { border-color: #e2e8f0; background: #f8fafc; }
html[data-theme="light"] .job-view-modal-file-item { border-bottom-color: #e2e8f0; }
html[data-theme="light"] .job-view-modal-file-item:hover { background: #f1f5f9; }
html[data-theme="light"] .job-view-modal-file-name { color: #334155; }
html[data-theme="light"] .job-view-modal-no-files { color: #94a3b8; }
html[data-theme="light"] .job-view-modal-footer { border-top-color: #e2e8f0; }
html[data-theme="light"] .job-view-modal-btn-cancel { border-color: #e2e8f0; color: #64748b; }
html[data-theme="light"] .job-view-modal-btn-cancel:hover { background: #f1f5f9; color: #334155; }
</style>
