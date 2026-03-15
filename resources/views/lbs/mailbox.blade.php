@extends('layouts.dashboard')

@section('title', 'LBS Mailbox')

@section('body_class', 'page-lbs-mailbox')

@section('content')
    <div class="lbs-list-page">
        <div class="lbs-list-header">
            <div class="lbs-list-header-text">
                <h1 class="lbs-list-title">Job Mailbox</h1>
                <p class="lbs-list-subtitle">View jobs waiting for email confirmation.</p>
            </div>
            <div class="lbs-list-search-wrap">
                <label for="lbsMailboxSearch" class="lbs-search-label">Search</label>
                <div class="lbs-search-input-wrap">
                    <svg class="lbs-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsMailboxSearch" class="lbs-search-input" placeholder="Search by reference, recipient..." autocomplete="off" aria-label="Search mailbox">
                </div>
            </div>
        </div>

        <div class="lbs-table-card">
            <div class="lbs-table-wrap">
                <table class="lbs-table lbs-table-mailbox" id="lbsMailboxTable">
                    <colgroup>
                        <col class="lbs-col-action">
                        <col class="lbs-col-log-date">
                        <col class="lbs-col-reference">
                        <col class="lbs-col-to">
                        <col class="lbs-col-email-format">
                        <col class="lbs-col-files">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="lbs-th lbs-th-action"><span>Action</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Log Date</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>Job Reference</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th" data-sort=""><span>To</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                            <th class="lbs-th"><span>Email Format</span></th>
                            <th class="lbs-th" data-sort=""><span>Files</span><span class="lbs-sort-icon" aria-hidden="true">↕</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $index => $job)
                            @php
                                $log = $job->log_date ? \Carbon\Carbon::parse($job->log_date, 'Asia/Manila') : null;
                                $logFormatted = $log ? $log->format('Y-m-d H:i:s') : '—';
                                $toEmail = $job->to_email ?? '—';
                                $planFiles = is_string($job->upload_files ?? null) ? json_decode($job->upload_files, true) : [];
                                $projFiles = is_string($job->upload_project_files ?? null) ? json_decode($job->upload_project_files, true) : [];
                                $hasFiles = (!empty($planFiles) && is_array($planFiles)) || (!empty($projFiles) && is_array($projFiles));
                            @endphp
                            <tr class="lbs-data-row lbs-mailbox-row" data-job-id="{{ $job->job_id }}" data-update-url="{{ route('lbs.job.update', ['id' => $job->job_id]) }}">
                                <td class="lbs-td lbs-td-action">
                                    <div class="lbs-mailbox-action-btns">
                                        <button type="button" class="lbs-btn lbs-btn-icon lbs-btn-revert" title="Revert (set status to For Checking)" aria-label="Revert" data-revert-job>
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                                        </button>
                                        <button type="button" class="lbs-btn lbs-btn-icon lbs-btn-send" title="Send email (same as preview, with latest checker upload files)" aria-label="Send" data-send-job="{{ $job->job_id }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="lbs-td lbs-td-log-date" data-label="Log Date" data-sort="{{ $job->log_date }}">{{ $logFormatted }}</td>
                                <td class="lbs-td lbs-td-reference" data-label="Job Reference">{{ $job->job_reference_no ?? $job->reference ?? '—' }}</td>
                                <td class="lbs-td lbs-td-to" data-label="To">{{ $toEmail }}</td>
                                <td class="lbs-td lbs-td-email-format" data-label="Email Format">
                                    <button type="button" class="lbs-btn lbs-btn-preview" data-preview-job="{{ $job->job_id }}" title="Preview email" aria-label="Preview email">Preview</button>
                                </td>
                                <td class="lbs-td lbs-td-files" data-label="Files">
                                    @if($hasFiles)
                                        <a href="{{ route('lbs.job.view', ['id' => $job->job_id]) }}#files" class="lbs-link-uploaded-files">Uploaded Files</a>
                                    @else
                                        <span class="lbs-no-files">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="lbs-td" colspan="6" style="text-align:center; padding:1.5rem; color:#94a3b8;">
                                    No jobs currently For Email Confirmation.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal-backdrop" id="revertMailboxModal" role="dialog" aria-labelledby="revertMailboxModalTitle" aria-modal="true">
        <div class="modal-box">
            <div class="modal-header">
                <svg class="modal-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                <h2 class="modal-title" id="revertMailboxModalTitle">Revert to For Checking</h2>
            </div>
            <div class="modal-body">
                <div class="revert-modal-confirm" id="revertModalConfirm">
                    <p>Set this job status to <strong>For Checking</strong>? The job will be removed from the mailbox.</p>
                </div>
                <div class="revert-modal-countdown" id="revertModalCountdown" hidden>
                    <p class="revert-countdown-text">Reverting in</p>
                    <div class="revert-countdown-number" id="revertCountdownNumber">3</div>
                    <p class="revert-countdown-cancel-hint">Click Cancel to abort</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" id="revertMailboxModalCancel">Cancel</button>
                <button type="button" class="btn btn-confirm btn-danger" id="revertMailboxModalConfirm"><span class="btn-text">Revert</span></button>
            </div>
        </div>
    </div>

    <div class="modal-backdrop" id="emailPreviewModal" role="dialog" aria-labelledby="emailPreviewModalTitle" aria-modal="true">
        <div class="modal-box email-preview-modal-box">
            <div class="email-preview-modal-header">
                <h2 class="modal-title" id="emailPreviewModalTitle">Email Preview</h2>
                <button type="button" class="email-preview-close" id="emailPreviewModalClose" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body email-preview-body">
                <div class="email-preview-content">
                    <div class="email-preview-logo-wrap">
                        <img src="{{ asset('storage/logo-light.png') }}" alt="LUNTIAN" class="email-preview-logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <span class="email-preview-logo-fallback" style="display:none;">LUNTIAN</span>
                    </div>
                    <p class="email-preview-tagline">Residential Building Design Solutions</p>
                    <p class="email-preview-services">• ENERGY • BUILDING DESIGN • VR • AR</p>
                    <p class="email-preview-greeting">Hi there!</p>
                    <p class="email-preview-ref-wrap">
                        <span class="email-preview-ref" id="emailPreviewRef">—</span>
                    </p>
                    <p class="email-preview-status-text">status has been updated to</p>
                    <p class="email-preview-status-value" id="emailPreviewStatus">—</p>
                    <p class="email-preview-assessor">Assessor: <span id="emailPreviewAssessor">—</span></p>
                    <p class="email-preview-assessor-email">Assessor Email: <a href="#" id="emailPreviewAssessorEmailLink" class="email-preview-link">—</a></p>
                    <div class="email-preview-notes-wrap">
                        <p class="email-preview-notes-title">Submission Notes:</p>
                        <div class="email-preview-notes" id="emailPreviewNotes">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" id="emailPreviewModalCloseBtn">Close</button>
            </div>
        </div>
    </div>

    <div class="modal-backdrop" id="sendEmailModal" role="dialog" aria-labelledby="sendEmailModalTitle" aria-modal="true">
        <div class="modal-box send-email-modal-box">
            <div class="modal-header">
                <h2 class="modal-title" id="sendEmailModalTitle">Sending Email</h2>
            </div>
            <div class="modal-body send-email-modal-body">
                <div class="send-email-steps">
                    <div class="send-email-step" id="sendStep1">
                        <span class="send-email-step-icon send-email-step-spinner" aria-hidden="true"></span>
                        <span class="send-email-step-text">Sending email...</span>
                    </div>
                    <div class="send-email-step" id="sendStep2">
                        <span class="send-email-step-icon send-email-step-pending" aria-hidden="true">○</span>
                        <span class="send-email-step-text">Updating status to Completed...</span>
                    </div>
                    <div class="send-email-step send-email-done" id="sendStepDone" hidden>
                        <span class="send-email-step-icon send-email-step-check">✓</span>
                        <span class="send-email-step-text">Done!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/lbs-list.css') }}">
    @endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/lbs-list.js') }}"></script>
    <script>
        (function() {
            var table = document.getElementById('lbsMailboxTable');
            var searchInput = document.getElementById('lbsMailboxSearch');
            var csrfToken = document.querySelector('meta[name="csrf-token"]');
            csrfToken = csrfToken ? csrfToken.getAttribute('content') : '';

            if (table && searchInput) {
                searchInput.addEventListener('input', function() {
                    var q = (this.value || '').toLowerCase().trim();
                    var rows = table.querySelectorAll('tbody tr.lbs-mailbox-row');
                    rows.forEach(function(tr) {
                        var text = (tr.textContent || '').toLowerCase();
                        tr.style.display = q === '' || text.indexOf(q) !== -1 ? '' : 'none';
                    });
                });
            }

            var revertModal = document.getElementById('revertMailboxModal');
            var revertCancelBtn = document.getElementById('revertMailboxModalCancel');
            var revertConfirmBtn = document.getElementById('revertMailboxModalConfirm');
            var revertConfirmBlock = document.getElementById('revertModalConfirm');
            var revertCountdownBlock = document.getElementById('revertModalCountdown');
            var revertCountdownNumber = document.getElementById('revertCountdownNumber');
            var pendingRevert = null;
            var revertCountdownTimer = null;

            function resetRevertModal() {
                if (revertCountdownTimer) { clearInterval(revertCountdownTimer); revertCountdownTimer = null; }
                revertConfirmBlock.hidden = false;
                revertCountdownBlock.hidden = true;
                revertConfirmBtn.disabled = false;
                revertConfirmBtn.querySelector('.btn-text').textContent = 'Revert';
            }
            function closeRevertModal() {
                revertModal.classList.remove('show');
                pendingRevert = null;
                resetRevertModal();
            }
            function doRevertRequest(row, url, btn) {
                var formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('_method', 'PUT');
                formData.append('job_status', 'For Checking');
                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
                .then(function(result) {
                    if (result.ok && result.data && result.data.status !== 'error') {
                        if (row) row.remove();
                        closeRevertModal();
                    } else {
                        alert(result.data && result.data.message ? result.data.message : 'Failed to revert status.');
                        if (btn) btn.disabled = false;
                        resetRevertModal();
                    }
                })
                .catch(function() {
                    alert('Failed to revert status.');
                    if (btn) btn.disabled = false;
                    resetRevertModal();
                });
            }

            document.querySelectorAll('[data-revert-job]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var row = this.closest('tr.lbs-mailbox-row');
                    var url = row ? row.getAttribute('data-update-url') : null;
                    if (!url) return;
                    if (this.disabled) return;
                    pendingRevert = { row: row, url: url, btn: this };
                    resetRevertModal();
                    revertModal.classList.add('show');
                });
            });

            if (revertCancelBtn) revertCancelBtn.addEventListener('click', closeRevertModal);
            if (revertModal) revertModal.addEventListener('click', function(e) { if (e.target === revertModal) closeRevertModal(); });
            var emailPreviewModal = document.getElementById('emailPreviewModal');
            var emailPreviewClose = document.getElementById('emailPreviewModalClose');
            var emailPreviewCloseBtn = document.getElementById('emailPreviewModalCloseBtn');
            var emailPreviewUrlBase = '{{ url("/dashboard/lbs/job") }}';

            function openEmailPreviewModal() {
                if (emailPreviewModal) emailPreviewModal.classList.add('show');
            }
            function closeEmailPreviewModal() {
                if (emailPreviewModal) emailPreviewModal.classList.remove('show');
            }

            document.querySelectorAll('[data-preview-job]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var jobId = this.getAttribute('data-preview-job');
                    if (!jobId) return;
                    var url = emailPreviewUrlBase + '/' + jobId + '/email-preview';
                    document.getElementById('emailPreviewRef').textContent = '…';
                    document.getElementById('emailPreviewStatus').textContent = '…';
                    document.getElementById('emailPreviewAssessor').textContent = '…';
                    var linkEl = document.getElementById('emailPreviewAssessorEmailLink');
                    linkEl.href = '#';
                    linkEl.textContent = '…';
                    document.getElementById('emailPreviewNotes').innerHTML = '…';
                    openEmailPreviewModal();
                    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            if (data.status !== 'success') return;
                            document.getElementById('emailPreviewRef').textContent = data.job_reference_no || '—';
                            document.getElementById('emailPreviewStatus').textContent = data.job_status || '—';
                            document.getElementById('emailPreviewAssessor').textContent = data.assessor || '—';
                            linkEl.textContent = data.assessor_email || '—';
                            linkEl.href = data.assessor_email ? ('mailto:' + data.assessor_email) : '#';
                            var notesEl = document.getElementById('emailPreviewNotes');
                            if (data.notes) {
                                notesEl.innerHTML = data.notes;
                                notesEl.style.display = '';
                            } else {
                                notesEl.textContent = '—';
                                notesEl.style.display = '';
                            }
                        })
                        .catch(function() {
                            document.getElementById('emailPreviewRef').textContent = '—';
                            document.getElementById('emailPreviewStatus').textContent = '—';
                            document.getElementById('emailPreviewAssessor').textContent = '—';
                            linkEl.textContent = '—';
                            document.getElementById('emailPreviewNotes').textContent = 'Error loading preview.';
                        });
                });
            });
            if (emailPreviewClose) emailPreviewClose.addEventListener('click', closeEmailPreviewModal);
            if (emailPreviewCloseBtn) emailPreviewCloseBtn.addEventListener('click', closeEmailPreviewModal);
            if (emailPreviewModal) emailPreviewModal.addEventListener('click', function(e) { if (e.target === emailPreviewModal) closeEmailPreviewModal(); });

            var sendEmailModal = document.getElementById('sendEmailModal');
            var sendStep1 = document.getElementById('sendStep1');
            var sendStep2 = document.getElementById('sendStep2');
            var sendStepDone = document.getElementById('sendStepDone');

            function resetSendModal() {
                if (sendStep1) {
                    sendStep1.classList.remove('done', 'active');
                    sendStep1.querySelector('.send-email-step-icon').className = 'send-email-step-icon send-email-step-spinner';
                    sendStep1.querySelector('.send-email-step-text').textContent = 'Sending email...';
                }
                if (sendStep2) {
                    sendStep2.classList.remove('done', 'active');
                    sendStep2.querySelector('.send-email-step-icon').className = 'send-email-step-icon send-email-step-pending';
                    sendStep2.querySelector('.send-email-step-icon').textContent = '○';
                }
                if (sendStepDone) sendStepDone.hidden = true;
            }

            document.querySelectorAll('[data-send-job]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var jobId = this.getAttribute('data-send-job');
                    if (!jobId) return;
                    if (this.disabled) return;
                    var row = this.closest('tr.lbs-mailbox-row');
                    var sendUrl = emailPreviewUrlBase + '/' + jobId + '/send-mailbox-email';
                    this.disabled = true;
                    var self = this;
                    resetSendModal();
                    sendStep1.classList.add('active');
                    if (sendEmailModal) sendEmailModal.classList.add('show');

                    var formData = new FormData();
                    formData.append('_token', csrfToken);
                    fetch(sendUrl, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
                    .then(function(result) {
                        if (result.ok && result.data && result.data.status === 'success') {
                            sendStep1.classList.remove('active');
                            sendStep1.classList.add('done');
                            sendStep1.querySelector('.send-email-step-icon').className = 'send-email-step-icon send-email-step-check';
                            sendStep1.querySelector('.send-email-step-icon').textContent = '✓';
                            sendStep1.querySelector('.send-email-step-text').textContent = 'Email sent.';
                            sendStep2.classList.add('active', 'done');
                            sendStep2.querySelector('.send-email-step-icon').className = 'send-email-step-icon send-email-step-check';
                            sendStep2.querySelector('.send-email-step-icon').textContent = '✓';
                            setTimeout(function() {
                                sendStepDone.hidden = false;
                                if (typeof window.showSuccessToast === 'function') {
                                    window.showSuccessToast(result.data.message || 'Email sent. Status updated to Completed.');
                                }
                                setTimeout(function() {
                                    sendEmailModal.classList.remove('show');
                                    resetSendModal();
                                    window.location.reload();
                                }, 600);
                            }, 700);
                        } else {
                            sendEmailModal.classList.remove('show');
                            resetSendModal();
                            alert(result.data && result.data.message ? result.data.message : 'Failed to send email.');
                            self.disabled = false;
                        }
                    })
                    .catch(function(err) {
                        sendEmailModal.classList.remove('show');
                        resetSendModal();
                        alert('Failed to send email.');
                        self.disabled = false;
                    });
                });
            });

            if (revertConfirmBtn) revertConfirmBtn.addEventListener('click', function() {
                if (!pendingRevert || revertCountdownTimer) return;
                revertConfirmBlock.hidden = true;
                revertCountdownBlock.hidden = false;
                revertConfirmBtn.disabled = true;
                revertConfirmBtn.querySelector('.btn-text').textContent = 'Reverting...';
                var count = 3;
                var row = pendingRevert.row;
                var url = pendingRevert.url;
                var btn = pendingRevert.btn;
                revertCountdownNumber.textContent = count;
                revertCountdownNumber.style.animation = 'none';
                revertCountdownNumber.offsetHeight;
                revertCountdownNumber.style.animation = '';
                revertCountdownTimer = setInterval(function() {
                    count--;
                    if (count <= 0) {
                        clearInterval(revertCountdownTimer);
                        revertCountdownTimer = null;
                        if (btn) btn.disabled = true;
                        doRevertRequest(row, url, null);
                        return;
                    }
                    revertCountdownNumber.textContent = count;
                    revertCountdownNumber.style.animation = 'none';
                    revertCountdownNumber.offsetHeight;
                    revertCountdownNumber.style.animation = '';
                }, 1000);
            });
        })();
    </script>
@endpush
