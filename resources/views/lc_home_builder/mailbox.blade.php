@extends('layouts.dashboard')

@section('title', 'LC HOME BUILDER Mailbox')

@section('body_class', 'page-lbs-mailbox')

@section('content')
    @php
        $branchLabel = 'LC HOME BUILDER';
        $updateRoute = 'lc_home_builder.update';
        $jobBaseUrl = url('/dashboard/lc-home-builder/job');
    @endphp
    <div class="block max-w-full pb-0">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">{{ $branchLabel }} Mailbox</h1>
                <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View jobs waiting for email confirmation.</p>
            </div>
            <div class="shrink-0">
                <label for="bphMailboxSearch" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Search</label>
                <div class="relative flex min-w-[260px] items-center">
                    <svg class="pointer-events-none absolute left-3 text-slate-500 dark:text-slate-500" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="bphMailboxSearch" class="w-full rounded-lg border border-slate-300 bg-slate-50 py-2 pl-9 pr-3.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:placeholder-slate-500 dark:focus:border-blue-700 dark:focus:ring-blue-700/25" placeholder="Search by reference, recipient..." autocomplete="off" aria-label="Search {{ $branchLabel }} mailbox">
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
            <div class="max-w-full overflow-x-auto">
                <table class="w-full min-w-[800px] table-fixed border-collapse text-sm" id="bphMailboxTable">
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
                            <th class="cursor-default border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400"><span>Print (PDF)</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                            @php
                                $log = $job->log_date ? \Carbon\Carbon::parse($job->log_date, 'Asia/Manila') : null;
                                $logFormatted = $log ? $log->format('Y-m-d H:i:s') : '—';
                                $toEmail = $job->to_email ?? '—';
                            @endphp
                            <tr class="lbs-data-row bph-mailbox-row border-b border-slate-200 align-middle text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5" data-job-id="{{ $job->job_id }}" data-update-url="{{ route($updateRoute, ['id' => $job->job_id]) }}">
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
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-500 dark:border-slate-700 dark:text-slate-400" data-label="Print (PDF)">
                                    N/A
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

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-opacity duration-200 [&.show]:opacity-100 [&.show]:pointer-events-auto" id="emailPreviewModal" role="dialog" aria-labelledby="emailPreviewModalTitle" aria-modal="true">
        <div class="w-full max-w-lg overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="emailPreviewModalTitle">Email Preview</h2>
                <button type="button" class="rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-600 dark:hover:text-slate-200" id="emailPreviewModalClose" aria-label="Close">&times;</button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto px-5 py-4">
                <div class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                    <p>Reference: <span id="emailPreviewRef">—</span></p>
                    <p>Status: <span id="emailPreviewStatus">—</span></p>
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
    <script>
        (function() {
            var table = document.getElementById('bphMailboxTable');
            var searchInput = document.getElementById('bphMailboxSearch');
            var csrfToken = document.querySelector('meta[name="csrf-token"]');
            csrfToken = csrfToken ? csrfToken.getAttribute('content') : '';
            var emailPreviewUrlBase = @json($jobBaseUrl);

            if (table && searchInput) {
                searchInput.addEventListener('input', function() {
                    var q = (this.value || '').toLowerCase().trim();
                    var rows = table.querySelectorAll('tbody tr.bph-mailbox-row');
                    rows.forEach(function(tr) {
                        var text = (tr.textContent || '').toLowerCase();
                        tr.style.display = q === '' || text.indexOf(q) !== -1 ? '' : 'none';
                    });
                });
            }

            document.querySelectorAll('[data-revert-job]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var row = this.closest('tr.bph-mailbox-row');
                    var url = row ? row.getAttribute('data-update-url') : null;
                    if (!url) return;
                    var formData = new FormData();
                    formData.append('_token', csrfToken);
                    formData.append('_method', 'PUT');
                    formData.append('job_status', 'For Checking');
                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    }).then(function() { window.location.reload(); });
                });
            });

            var emailPreviewModal = document.getElementById('emailPreviewModal');
            var emailPreviewClose = document.getElementById('emailPreviewModalClose');
            var emailPreviewCloseBtn = document.getElementById('emailPreviewModalCloseBtn');
            var sendEmailModal = document.getElementById('sendEmailModal');
            var sendStep1 = document.getElementById('sendStep1');
            var sendStep2 = document.getElementById('sendStep2');
            var sendStepDone = document.getElementById('sendStepDone');
            function openEmailPreviewModal() { if (emailPreviewModal) emailPreviewModal.classList.add('show'); }
            function closeEmailPreviewModal() { if (emailPreviewModal) emailPreviewModal.classList.remove('show'); }
            function resetSendModal() {
                if (sendStep1) {
                    sendStep1.classList.remove('done', 'active');
                    sendStep1.querySelector('.send-email-step-icon').className = 'send-email-step-icon send-email-step-spinner h-5 w-5 shrink-0 rounded-full border-2 border-slate-300 border-t-blue-600';
                    sendStep1.querySelector('.send-email-step-text').textContent = 'Sending email...';
                }
                if (sendStep2) {
                    sendStep2.classList.remove('done', 'active');
                    sendStep2.querySelector('.send-email-step-icon').className = 'send-email-step-icon text-lg';
                    sendStep2.querySelector('.send-email-step-icon').textContent = '○';
                }
                if (sendStepDone) sendStepDone.hidden = true;
            }

            document.querySelectorAll('[data-preview-job]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var jobId = this.getAttribute('data-preview-job');
                    if (!jobId) return;
                    fetch(emailPreviewUrlBase + '/' + jobId + '/email-preview', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            document.getElementById('emailPreviewRef').textContent = data.job_reference_no || '—';
                            document.getElementById('emailPreviewStatus').textContent = data.job_status || '—';
                            document.getElementById('emailPreviewAssessor').textContent = data.assessor || '—';
                            var linkEl = document.getElementById('emailPreviewAssessorEmailLink');
                            linkEl.textContent = data.assessor_email || '—';
                            linkEl.href = data.assessor_email ? ('mailto:' + data.assessor_email) : '#';
                            document.getElementById('emailPreviewNotes').innerHTML = data.notes || '—';
                            openEmailPreviewModal();
                        });
                });
            });
            if (emailPreviewClose) emailPreviewClose.addEventListener('click', closeEmailPreviewModal);
            if (emailPreviewCloseBtn) emailPreviewCloseBtn.addEventListener('click', closeEmailPreviewModal);

            document.querySelectorAll('[data-send-job]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var jobId = this.getAttribute('data-send-job');
                    if (!jobId) return;
                    var self = this;
                    if (self.disabled) return;
                    self.disabled = true;
                    resetSendModal();
                    if (sendStep1) sendStep1.classList.add('active');
                    if (sendEmailModal) sendEmailModal.classList.add('show');
                    var formData = new FormData();
                    formData.append('_token', csrfToken);
                    fetch(emailPreviewUrlBase + '/' + jobId + '/send-mailbox-email', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
                    .then(function(result) {
                        if (result.ok && result.data && result.data.status === 'success') {
                            if (sendStep1) {
                                sendStep1.classList.remove('active');
                                sendStep1.classList.add('done');
                                sendStep1.querySelector('.send-email-step-icon').className = 'send-email-step-icon send-email-step-check';
                                sendStep1.querySelector('.send-email-step-icon').textContent = '✓';
                                sendStep1.querySelector('.send-email-step-text').textContent = 'Email sent.';
                            }
                            if (sendStep2) {
                                sendStep2.classList.add('active', 'done');
                                sendStep2.querySelector('.send-email-step-icon').className = 'send-email-step-icon send-email-step-check';
                                sendStep2.querySelector('.send-email-step-icon').textContent = '✓';
                            }
                            setTimeout(function() {
                                if (sendStepDone) sendStepDone.hidden = false;
                                if (typeof window.showSuccessToast === 'function') {
                                    window.showSuccessToast(result.data.message || 'Email sent. Status updated to Completed.');
                                }
                                setTimeout(function() {
                                    if (sendEmailModal) sendEmailModal.classList.remove('show');
                                    resetSendModal();
                                    window.location.reload();
                                }, 600);
                            }, 700);
                        } else {
                            if (sendEmailModal) sendEmailModal.classList.remove('show');
                            resetSendModal();
                            alert(result.data && result.data.message ? result.data.message : 'Failed to send email.');
                            self.disabled = false;
                        }
                    })
                    .catch(function() {
                        if (sendEmailModal) sendEmailModal.classList.remove('show');
                        resetSendModal();
                        alert('Failed to send email.');
                        self.disabled = false;
                    });
                });
            });
        })();
    </script>
@endpush

