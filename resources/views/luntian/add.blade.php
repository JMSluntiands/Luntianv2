@extends('layouts.dashboard')

@section('title', 'Add New Job (Luntian)')

@section('body_class', 'page-luntian-add')

@section('content')
    <div class="w-full max-w-full px-0">
        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="mb-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Add New Job (Luntian)</h1>
            <p class="text-slate-500 dark:text-slate-400">Fill in the form below to create a new Luntian job.</p>
        </div>

        <form id="elAddForm" action="#" method="POST" autocomplete="off" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @php
                $preRef = isset($duplicateJob) ? ($duplicateJob->reference_no ?? '') : 'JOBS0823-003';
                $selCompliance = isset($duplicateJob) ? ($duplicateJob->compliance_id ?? null) : ($defaultComplianceId ?? null);
                $selClient = isset($duplicateJob) ? ($duplicateJob->client_account_id ?? null) : ($defaultClientAccountId ?? null);
                $selPriority = isset($duplicateJob) ? ($duplicateJob->priority_id ?? null) : ($defaultPriorityId ?? null);
                $selJobRequest = isset($duplicateJob) ? ($duplicateJob->job_request_id ?? null) : ($defaultJobRequestId ?? null);
            @endphp

            {{-- Client Details Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Client Details</h2>
                    <span id="jobReferenceContent" class="rounded-lg bg-slate-200/80 px-3 py-1.5 font-mono text-sm font-medium text-slate-700 dark:bg-slate-700 dark:text-slate-300">{{ $preRef ?: 'JOBS0823-003' }}</span>
                </div>
                <div class="p-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="reference_no" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Reference No.</label>
                            <input type="text" id="reference_no" name="reference_no" value="{{ isset($duplicateJob) ? e($duplicateJob->reference_no ?? '') : '' }}" placeholder="Enter Reference Number" autocomplete="off" {{ isset($duplicateJob) ? 'readonly' : '' }}
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500 {{ isset($duplicateJob) ? 'cursor-not-allowed bg-slate-100 dark:bg-slate-700/50' : '' }}">
                        </div>
                        <div>
                            <label for="client_reference" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Client Reference</label>
                            <input type="text" id="client_reference" name="client_reference" value="{{ isset($duplicateJob) ? e($duplicateJob->client_reference ?? '') : '' }}" placeholder="Enter Client Reference" autocomplete="off" {{ isset($duplicateJob) ? 'readonly' : '' }}
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500 {{ isset($duplicateJob) ? 'cursor-not-allowed bg-slate-100 dark:bg-slate-700/50' : '' }}">
                        </div>
                        <div>
                            <label for="compliance" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Compliance</label>
                            <select id="compliance" name="compliance" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" autocomplete="off">
                                <option value="">Select compliance</option>
                                @foreach($compliances ?? [] as $c)
                                    <option value="{{ $c->id }}" {{ $selCompliance !== null && (int) $selCompliance === (int) $c->id ? 'selected' : '' }}>{{ $c->column ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="client" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Client</label>
                            <select id="client" name="client" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" autocomplete="off">
                                <option value="">Select client</option>
                                @foreach($clientAccounts ?? [] as $client)
                                    <option value="{{ $client->client_account_id }}" {{ $selClient !== null && (int) $selClient === (int) $client->client_account_id ? 'selected' : '' }}>{{ $client->client_account_name ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Job Details Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Job Details</h2>
                </div>
                <div class="p-5 space-y-5">
                    <div>
                        <label for="job_address" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Job Address</label>
                        <input type="text" id="job_address" name="job_address" value="{{ isset($duplicateJob) ? e($duplicateJob->job_address ?? '') : '' }}" placeholder="Complete Address" autocomplete="off"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="priority" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Priority</label>
                            <select id="priority" name="priority" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" autocomplete="off">
                                <option value="">Select priority</option>
                                @foreach($priorities ?? [] as $priority)
                                    <option value="{{ $priority->id }}" {{ $selPriority !== null && (int) $selPriority === (int) $priority->id ? 'selected' : '' }}>{{ $priority->name ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="job_type" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Job Type</label>
                            <select id="job_type" name="job_type" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" autocomplete="off">
                                <option value="">Select job type</option>
                                @foreach($jobRequests ?? [] as $jobRequest)
                                    @if(($jobRequest->client_code ?? '') === 'LT01')
                                        <option value="{{ $jobRequest->id }}" {{ $selJobRequest !== null && (int) $selJobRequest === (int) $jobRequest->id ? 'selected' : '' }}>{{ $jobRequest->job_request_type ?? '' }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="notes-body" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Notes</label>
                        <input type="hidden" name="notes" id="notes" autocomplete="off">
                        <div class="overflow-hidden rounded-lg border border-slate-300 dark:border-slate-600">
                            <div class="flex items-center gap-1 border-b border-slate-200 bg-slate-50 px-2 py-1.5 dark:border-slate-600 dark:bg-slate-800/80">
                                <button type="button" class="el-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="bold" title="Bold"><span class="font-bold">B</span></button>
                                <button type="button" class="el-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="italic" title="Italic"><span class="italic">I</span></button>
                                <button type="button" class="el-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="underline" title="Underline"><span class="underline">U</span></button>
                                <span class="mx-1 h-5 w-px bg-slate-300 dark:bg-slate-600"></span>
                                <button type="button" class="el-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="insertOrderedList" title="Numbered list">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h12M9 12h12M9 19h12M3 5h.01M3 12h.01M3 19h.01"/></svg>
                                </button>
                                <button type="button" class="el-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="insertUnorderedList" title="Bullets">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M2 6h.01M2 12h.01M2 18h.01"/></svg>
                                </button>
                            </div>
                            <div id="el-notes-body" contenteditable="true" data-placeholder="Write notes here"
                                class="min-h-[120px] bg-white px-4 py-3 text-slate-800 placeholder-slate-400 focus:outline-none dark:bg-slate-800 dark:text-slate-100 [&:empty::before]:content-[attr(data-placeholder)] [&:empty::before]:text-slate-400 dark:[&:empty::before]:text-slate-500"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attachments Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Attachments</h2>
                </div>
                <div class="p-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Upload Plans</label>
                            <label for="plans" class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50/50 py-4 px-3 text-center transition-colors hover:border-emerald-400 hover:bg-emerald-50/50 dark:border-slate-600 dark:bg-slate-800/50 dark:hover:border-emerald-600 dark:hover:bg-emerald-950/30">
                                <svg class="mb-1 h-6 w-6 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Drag here</span>
                                <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-500">or</span>
                                <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">browse</span>
                                <input type="file" id="plans" name="plans[]" class="hidden" multiple tabindex="-1">
                            </label>
                            <div id="plans-file-list" class="mt-2 min-h-0 space-y-1 text-xs text-slate-600 dark:text-slate-400"></div>
                            <button type="button" id="plans-clear" class="mt-1 hidden text-xs text-slate-500 hover:text-red-600 dark:text-slate-400 dark:hover:text-red-400">Clear</button>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Upload Document</label>
                            <label for="docs" class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50/50 py-4 px-3 text-center transition-colors hover:border-emerald-400 hover:bg-emerald-50/50 dark:border-slate-600 dark:bg-slate-800/50 dark:hover:border-emerald-600 dark:hover:bg-emerald-950/30">
                                <svg class="mb-1 h-6 w-6 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Drag here</span>
                                <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-500">or</span>
                                <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">browse</span>
                                <input type="file" id="docs" name="docs[]" class="hidden" multiple tabindex="-1">
                            </label>
                            <div id="docs-file-list" class="mt-2 min-h-0 space-y-1 text-xs text-slate-600 dark:text-slate-400"></div>
                            <button type="button" id="docs-clear" class="mt-1 hidden text-xs text-slate-500 hover:text-red-600 dark:text-slate-400 dark:hover:text-red-400">Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $selAssigned = isset($duplicateJob) ? ($duplicateJob->staff_id ?? 'GM') : 'GM';
                $selChecked = isset($duplicateJob) ? ($duplicateJob->checker_id ?? 'GM') : 'GM';
            @endphp

            {{-- Assignment Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Assignment</h2>
                </div>
                <div class="p-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="assigned_to" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Assigned To</label>
                            <select id="assigned_to" name="assigned_to" class="select2-single assignment-user-select w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                                @include('partials.assignment-user-options', ['selected' => $selAssigned ?? 'GM', 'includeSelectPlaceholder' => true])
                            </select>
                        </div>
                        <div>
                            <label for="checked_by" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Checked By</label>
                            <select id="checked_by" name="checked_by" class="select2-single assignment-user-select w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                                @include('partials.assignment-user-options', ['selected' => $selChecked ?? 'GM', 'includeSelectPlaceholder' => true])
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" id="submitELBtn"
                    class="cursor-pointer inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Save Job
                </button>
                <a href="{{ route('luntian.list') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
/* Post-save modal: backdrop fade-in, dialog scale-in */
.lbs-after-save-overlay { animation: lbsOverlayFadeIn 0.25s ease forwards; }
.lbs-after-save-dialog {
    animation: lbsDialogScaleIn 0.35s ease 0.1s forwards;
    transform: scale(0.9);
    opacity: 0;
}
/* Slack step & email step: animate in */
.lbs-modal-step-slack,
.lbs-modal-step-sending {
    opacity: 0;
    transform: translateY(8px);
}
.lbs-modal-step-slack.lbs-step-animate-in,
.lbs-modal-step-sending.lbs-sending-animate-in {
    animation: lbsSendingStepIn 0.4s ease forwards;
}
.lbs-slack-spinner,
.lbs-send-spinner {
    animation: lbsSpinner 0.8s linear infinite;
}
.lbs-email-sent-animate {
    animation: lbsEmailSentFadeIn 0.5s ease 0.3s forwards;
    opacity: 0;
}
@keyframes lbsOverlayFadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes lbsDialogScaleIn {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
@keyframes lbsSendingStepIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes lbsSpinner {
    to { transform: rotate(360deg); }
}
@keyframes lbsEmailSentFadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            function getElCsrfToken() {
                var m = document.querySelector('meta[name="csrf-token"]');
                return (m && m.getAttribute('content')) || '{{ csrf_token() }}';
            }
            function elAjaxErrorMessage(xhr, fallback) {
                var fb = fallback || 'Request failed.';
                if (!xhr) return fb;
                var j = xhr.responseJSON;
                if (j) {
                    if (typeof j.message === 'string' && j.message) return j.message;
                    if (j.errors && typeof j.errors === 'object') {
                        var keys = Object.keys(j.errors);
                        if (keys.length && j.errors[keys[0]] && j.errors[keys[0]][0]) return j.errors[keys[0]][0];
                    }
                    if (typeof j.error === 'string' && j.error) return j.error;
                }
                var text = xhr.responseText || '';
                if (text) {
                    try {
                        var p = JSON.parse(text);
                        if (p.message && typeof p.message === 'string') return p.message;
                    } catch (e2) {}
                }
                if (xhr.status === 419) return 'Session expired. Refresh the page and try again.';
                if (xhr.status === 404) return 'Server could not find this job or route.';
                if (xhr.status === 0) return 'Network error. Check your connection.';
                return fb;
            }
            function notifyElError(msg) {
                if (window.showErrorToast) window.showErrorToast(msg);
                else if (window.showSuccessToast) window.showSuccessToast(msg);
            }

            var notesBody = document.getElementById('el-notes-body');
            var noteBtns = document.querySelectorAll('.el-notes-btn');

            function updateNotesActiveState() {
                noteBtns.forEach(function(btn) {
                    var cmd = btn.getAttribute('data-cmd');
                    var active = document.queryCommandState(cmd);
                    btn.classList.toggle('bg-emerald-100', active);
                    btn.classList.toggle('text-emerald-700', active);
                    btn.classList.toggle('dark:bg-emerald-900/40', active);
                    btn.classList.toggle('dark:text-emerald-300', active);
                });
            }

            noteBtns.forEach(function(btn) {
                btn.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    var cmd = this.getAttribute('data-cmd');
                    if (!cmd || !notesBody) return;
                    notesBody.focus();
                    document.execCommand(cmd, false, null);
                    updateNotesActiveState();
                });
            });

            notesBody.addEventListener('focus', updateNotesActiveState);
            notesBody.addEventListener('keyup', updateNotesActiveState);
            notesBody.addEventListener('mouseup', updateNotesActiveState);

            // Select2: all selects in this form become searchable dropdowns
            $('#elAddForm select:not(.assignment-user-select)').select2({ width: '100%', allowClear: false });
            initAssignmentUserSelect2($('#elAddForm'));

            @if(isset($duplicateJob) && ($duplicateJob->notes ?? '') !== '')
                (function() {
                    var notesHtml = {!! json_encode($duplicateJob->notes ?? '') !!};
                    var notesEl = document.getElementById('el-notes-body');
                    var notesHidden = document.getElementById('notes');
                    if (notesEl) notesEl.innerHTML = notesHtml;
                    if (notesHidden) notesHidden.value = notesHtml;
                })();
            @endif

            var $btn = $('#submitELBtn');
            var originalBtnHtml = $btn.html();

            function renderFileList(inputId, listId, clearBtnId) {
                var input = document.getElementById(inputId);
                var listEl = document.getElementById(listId);
                var clearBtn = document.getElementById(clearBtnId);
                if (!input || !listEl) return;
                function update() {
                    var files = input.files || [];
                    listEl.innerHTML = '';
                    if (files.length === 0) {
                        listEl.innerHTML = '<span class="text-slate-400 dark:text-slate-500">No file chosen</span>';
                        if (clearBtn) clearBtn.classList.add('hidden');
                    } else {
                        for (var i = 0; i < files.length; i++) {
                            var li = document.createElement('div');
                            li.className = 'flex items-center gap-2 truncate rounded bg-slate-100 dark:bg-slate-700/50 px-2 py-1';
                            li.innerHTML = '<svg class="h-3.5 w-3.5 flex-shrink-0 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span class="truncate" title="' + (files[i].name || '').replace(/"/g, '&quot;') + '">' + (files[i].name || '') + '</span>';
                            listEl.appendChild(li);
                        }
                        if (clearBtn) clearBtn.classList.remove('hidden');
                    }
                }
                input.addEventListener('change', update);
                if (clearBtn) {
                    clearBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        input.value = '';
                        update();
                    });
                }
                update();
            }

            renderFileList('plans', 'plans-file-list', 'plans-clear');
            renderFileList('docs', 'docs-file-list', 'docs-clear');

            $btn.on('click', function(e) {
                e.preventDefault();

                $('#notes').val($('#el-notes-body').html());

                var formEl = document.getElementById('elAddForm');
                var formData = new FormData(formEl);

                var headerRef = $('#jobReferenceContent').text().trim();
                if (headerRef) {
                    formData.append('header_reference', headerRef);
                }

                $.ajax({
                    url: '{{ route('luntian.store') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $btn.prop('disabled', true).addClass('is-loading')
                            .html('<span class="mr-2 inline-block h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white" role="status" aria-hidden="true"></span>Saving...');
                    },
                    success: function(resp) {
                        if (resp.status === 'success') {
                            if (window.showSuccessToast) showSuccessToast(resp.message || 'Job saved.');
                            formEl.reset();
                            $('#el-notes-body').empty();
                            $('#elAddForm select').trigger('change');
                            document.getElementById('plans') && document.getElementById('plans').dispatchEvent(new Event('change'));
                            document.getElementById('docs') && document.getElementById('docs').dispatchEvent(new Event('change'));
                            showElAfterSavePrompt(resp.job_id);
                        } else {
                            notifyElError(resp.message || 'Failed to save job.');
                        }
                    },
                    error: function(xhr) {
                        notifyElError(elAjaxErrorMessage(xhr, 'Unexpected error while saving.'));
                    },
                    complete: function() {
                        $btn.prop('disabled', false).removeClass('is-loading').html(originalBtnHtml);
                    }
                });
            });

            function showElAfterSavePrompt(jobId) {
                var sendSlackUrl = '{{ url('dashboard/luntian/job') }}/' + jobId + '/send-slack';
                var listUrl = '{{ route('luntian.list') }}';
                var addUrl = '{{ route('luntian.add') }}';

                var $overlay = $(
                    '<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 lbs-after-save-overlay">' +
                        '<div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800 overflow-hidden lbs-after-save-dialog">' +
                            '<div class="p-6 text-center lbs-modal-step lbs-modal-step-question">' +
                                '<div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/40">' +
                                    '<svg class="h-7 w-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' +
                                '</div>' +
                                '<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Job saved</h3>' +
                                '<p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Do you want to create another Luntian job?</p>' +
                                '<div class="mt-6 flex gap-3">' +
                                    '<button type="button" data-el-go-list class="cursor-pointer flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">Go to Luntian list</button>' +
                                    '<button type="button" data-el-new-job class="cursor-pointer flex-1 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-500">Create another job</button>' +
                                '</div>' +
                            '</div>' +
                            '<div class="p-6 text-center lbs-modal-step lbs-modal-step-updating" style="display:none;">' +
                                '<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Updating status...</h3>' +
                                '<p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Please wait.</p>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );

                $('body').append($overlay);

                $overlay.on('click', function(e) {
                    if (e.target === this) $overlay.remove();
                });

                function proceedAfterSlack(action) {
                    var $question = $overlay.find('.lbs-modal-step-question');
                    var $updating = $overlay.find('.lbs-modal-step-updating');
                    var $goListBtn = $overlay.find('[data-el-go-list]');
                    var $newJobBtn = $overlay.find('[data-el-new-job]');

                    $question.hide();
                    $updating.show();
                    $goListBtn.prop('disabled', true).addClass('opacity-60 pointer-events-none');
                    $newJobBtn.prop('disabled', true).addClass('opacity-60 pointer-events-none');

                    $.ajax({
                        url: sendSlackUrl,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': getElCsrfToken(),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        data: { _token: getElCsrfToken() },
                        dataType: 'json'
                    }).done(function() {
                        $overlay.remove();
                        if (action === 'list') {
                            window.location.href = listUrl;
                        } else if (action === 'stay') {
                            // Refresh the form for a truly new job entry.
                            window.location.href = addUrl;
                        }
                    }).fail(function() {
                        $overlay.remove();
                        if (action === 'list') {
                            window.location.href = listUrl;
                        } else if (action === 'stay') {
                            window.location.href = addUrl;
                        }
                    });
                }

                $overlay.find('[data-el-new-job]').on('click', function() {
                    proceedAfterSlack('stay');
                });

                $overlay.find('[data-el-go-list]').on('click', function() {
                    proceedAfterSlack('list');
                });
            }
        });
    </script>
@endpush
