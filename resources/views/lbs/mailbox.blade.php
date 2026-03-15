@extends('layouts.dashboard')

@section('title', 'LBS Mailbox')

@section('body_class', 'page-lbs-mailbox')

@section('content')
    <div class="block max-w-full pb-0">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-white dark:text-white">Job Mailbox</h1>
                <p class="m-0 text-[0.9375rem] leading-snug text-slate-400">View jobs waiting for email confirmation.</p>
            </div>
            <div class="shrink-0">
                <label for="lbsMailboxSearch" class="mb-1.5 block text-xs font-semibold text-slate-400">Search</label>
                <div class="relative flex min-w-[260px] items-center">
                    <svg class="pointer-events-none absolute left-3 text-slate-500 dark:text-slate-500" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsMailboxSearch" class="w-full rounded-lg border border-slate-300 bg-slate-50 py-2 pl-9 pr-3.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:placeholder-slate-500 dark:focus:border-blue-700 dark:focus:ring-blue-700/25" placeholder="Search by reference, recipient..." autocomplete="off" aria-label="Search mailbox">
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full min-w-[800px] table-fixed border-collapse text-sm" id="lbsMailboxTable">
                    <colgroup>
                        <col style="width: 140px">
                        <col style="width: 160px">
                        <col style="width: 120px">
                        <col style="width: 200px">
                        <col style="width: 100px">
                        <col style="width: 120px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="cursor-default border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400"><span>Action</span></th>
                            <th class="cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Log Date</span></th>
                            <th class="cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Job Reference</span></th>
                            <th class="cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>To</span></th>
                            <th class="cursor-default border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400"><span>Email Format</span></th>
                            <th class="cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Files</span></th>
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
                            <tr class="lbs-data-row lbs-mailbox-row border-b border-slate-200 align-middle text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5" data-job-id="{{ $job->job_id }}" data-update-url="{{ route('lbs.job.update', ['id' => $job->job_id]) }}">
                                <td class="border-b border-slate-200 px-4 py-3 align-middle dark:border-slate-700">
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 transition-colors hover:bg-amber-500/15 hover:text-amber-500 dark:hover:bg-amber-500/15 dark:hover:text-amber-400" title="Revert (set status to For Checking)" aria-label="Revert" data-revert-job>
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                                        </button>
                                        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 transition-colors hover:bg-green-500/15 hover:text-green-500 dark:hover:bg-green-500/15 dark:hover:text-green-400" title="Send email" aria-label="Send" data-send-job="{{ $job->job_id }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Log Date" data-sort="{{ $job->log_date }}">{{ $logFormatted }}</td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Job Reference">{{ $job->job_reference_no ?? $job->reference ?? '—' }}</td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="To">{{ $toEmail }}</td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle dark:border-slate-700" data-label="Email Format">
                                    <button type="button" class="rounded-lg border border-slate-300 bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 transition-colors hover:bg-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600" data-preview-job="{{ $job->job_id }}" title="Preview email" aria-label="Preview email">Preview</button>
                                </td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Files">
                                    @if($hasFiles)
                                        <a href="{{ route('lbs.job.view', ['id' => $job->job_id]) }}#files" class="text-blue-600 underline hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Uploaded Files</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="border-b border-slate-200 px-4 py-3 text-center text-slate-400 dark:border-slate-700 dark:text-slate-400" colspan="6">No jobs currently For Email Confirmation.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-opacity duration-200 [&.show]:opacity-100 [&.show]:pointer-events-auto" id="revertMailboxModal" role="dialog" aria-labelledby="revertMailboxModalTitle" aria-modal="true">
        <div class="w-full max-w-md overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg></span>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="revertMailboxModalTitle">Revert to For Checking</h2>
            </div>
            <div class="px-5 py-4">
                <div id="revertModalConfirm">
                    <p class="text-slate-600 dark:text-slate-300">Set this job status to <strong>For Checking</strong>? The job will be removed from the mailbox.</p>
                </div>
                <div id="revertModalCountdown" hidden>
                    <p class="text-slate-600 dark:text-slate-300">Reverting in</p>
                    <div class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400" id="revertCountdownNumber">3</div>
                    <p class="mt-1 text-sm text-slate-500">Click Cancel to abort</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600" id="revertMailboxModalCancel">Cancel</button>
                <button type="button" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600" id="revertMailboxModalConfirm"><span class="btn-text">Revert</span></button>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-opacity duration-200 [&.show]:opacity-100 [&.show]:pointer-events-auto" id="emailPreviewModal" role="dialog" aria-labelledby="emailPreviewModalTitle" aria-modal="true">
        <div class="w-full max-w-lg overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="emailPreviewModalTitle">Email Preview</h2>
                <button type="button" class="rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-600 dark:hover:text-slate-200" id="emailPreviewModalClose" aria-label="Close">&times;</button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto px-5 py-4">
                <div class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                    <div class="mb-4">
                        <img src="{{ asset('storage/logo-light.png') }}" alt="LUNTIAN" class="h-8" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <span class="text-lg font-bold text-slate-800 dark:text-white" style="display:none;">LUNTIAN</span>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400">Residential Building Design Solutions</p>
                    <p class="text-slate-500 dark:text-slate-400">• ENERGY • BUILDING DESIGN • VR • AR</p>
                    <p>Hi there!</p>
                    <p><span id="emailPreviewRef">—</span></p>
                    <p>status has been updated to</p>
                    <p class="font-semibold" id="emailPreviewStatus">—</p>
                    <p>Assessor: <span id="emailPreviewAssessor">—</span></p>
                    <p>Assessor Email: <a href="#" id="emailPreviewAssessorEmailLink" class="text-blue-600 underline dark:text-blue-400">—</a></p>
                    <div class="mt-4">
                        <p class="font-semibold text-slate-700 dark:text-slate-200">Submission Notes:</p>
                        <div id="emailPreviewNotes" class="mt-1">—</div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600" id="emailPreviewModalCloseBtn">Close</button>
            </div>
        </div>
    </div>

    <div class="send-email-modal-backdrop fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-all duration-300 [&.show]:opacity-100 [&.show]:pointer-events-auto" id="sendEmailModal" role="dialog" aria-labelledby="sendEmailModalTitle" aria-modal="true">
        <div class="send-email-modal-dialog w-full max-w-sm overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800">
            <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="sendEmailModalTitle">Sending Email</h2>
            </div>
            <div class="space-y-3 px-5 py-4">
                <div class="send-email-step flex items-center gap-3 transition-all duration-300" id="sendStep1">
                    <span class="send-email-step-icon send-email-step-spinner h-5 w-5 shrink-0 rounded-full border-2 border-slate-300 border-t-blue-600" aria-hidden="true"></span>
                    <span class="send-email-step-text text-sm text-slate-700 dark:text-slate-300">Sending email...</span>
                </div>
                <div class="send-email-step flex items-center gap-3 text-slate-500 transition-all duration-300" id="sendStep2">
                    <span class="send-email-step-icon text-lg" aria-hidden="true">○</span>
                    <span class="text-sm">Updating status to Completed...</span>
                </div>
                <div class="send-email-done flex items-center gap-3 text-green-600 dark:text-green-400" id="sendStepDone" hidden>
                    <span class="text-lg font-bold">✓</span>
                    <span class="text-sm font-medium">Done!</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* Send email modal: backdrop fade + dialog scale-in */
.send-email-modal-backdrop .send-email-modal-dialog { transform: scale(0.95); opacity: 0; transition: transform 0.3s ease, opacity 0.3s ease; }
.send-email-modal-backdrop.show .send-email-modal-dialog { transform: scale(1); opacity: 1; }
.send-email-step-spinner { animation: send-email-spin 0.8s linear infinite; }
@keyframes send-email-spin { to { transform: rotate(360deg); } }
.send-email-step.done .send-email-step-icon { animation: send-email-check-pop 0.35s ease; }
@keyframes send-email-check-pop { 0% { transform: scale(0.5); opacity: 0; } 50% { transform: scale(1.15); } 100% { transform: scale(1); opacity: 1; } }
.send-email-step:not(.active):not(.done) { opacity: 0.5; }
.send-email-done { animation: send-email-done-in 0.35s ease; }
@keyframes send-email-done-in { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
</style>
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
