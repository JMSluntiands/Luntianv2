@extends('layouts.dashboard')

@section('title', 'Add New Job (BPH)')

@section('body_class', 'page-bph-add')

@section('content')
    <div class="w-full max-w-full px-0">
        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="mb-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Add New Job (BPH)</h1>
            <p class="text-slate-500 dark:text-slate-400">Fill in the form below to create a new BPH job.</p>
        </div>

        <form id="bphAddForm" action="#" method="POST" autocomplete="off" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Client Details Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Client Details</h2>
                    <span id="bphJobReferenceContent" class="rounded-lg bg-slate-200/80 px-3 py-1.5 font-mono text-sm font-medium text-slate-700 dark:bg-slate-700 dark:text-slate-300">JOB0903-001</span>
                </div>
                <div class="p-5">
                    <div class="space-y-5">
                        @php
                            $selCompliance = $defaultComplianceId ?? null;
                            $selJobRequest = $defaultJobRequestId ?? null;
                            $selectedContactEmail = old('contact_email', request('contact_email'));
                            $selectedUrgent = old('urgent_job', request('urgent_job'));
                        @endphp
                        <div class="flex items-center gap-3">
                            <label class="inline-flex cursor-pointer items-center gap-2.5">
                                <input type="checkbox" id="urgent_job" name="urgent_job" value="1" autocomplete="off" {{ (string) $selectedUrgent === '1' ? 'checked' : '' }}
                                    class="h-4 w-4 shrink-0 rounded border-2 border-slate-300 bg-white text-emerald-600 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:border-slate-500 dark:bg-slate-700 dark:focus:ring-offset-slate-800 dark:checked:border-emerald-500 dark:checked:bg-emerald-500">
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Urgent Job (YES)</span>
                            </label>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="ncc_compliance" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">NCC Compliance</label>
                                <select id="ncc_compliance" name="ncc_compliance" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" autocomplete="off">
                                    <option value="">Select compliance</option>
                                    @foreach($compliances ?? [] as $c)
                                        <option value="{{ $c->id }}" {{ $selCompliance !== null && (int) $selCompliance === (int) $c->id ? 'selected' : '' }}>{{ $c->column ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="job_type_request" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Job Type Request</label>
                                <select id="job_type_request" name="job_type_request" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" autocomplete="off">
                                    <option value="">Select job type</option>
                                    @foreach($jobRequests ?? [] as $jr)
                                        <option value="{{ $jr->id }}" {{ $selJobRequest !== null && (int) $selJobRequest === (int) $jr->id ? 'selected' : '' }}>{{ $jr->job_request_type ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="job_number" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Job Number <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" id="job_number" name="job_number" required placeholder="e.g. 12345B" autocomplete="off" maxlength="6" value="{{ old('job_number', request('job_number')) }}"
                                    class="bph-job-number-input w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 pr-10 text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                                <span id="job_number_error_icon" class="pointer-events-none absolute right-3 top-1/2 hidden -translate-y-1/2 rounded-full bg-red-100 p-1 text-red-600 dark:bg-red-900/40 dark:text-red-400" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                </span>
                            </div>
                            <p class="bph-job-number-hint mt-1 text-xs text-slate-500 dark:text-slate-400">5 digits + letter B only, max 6 characters (e.g. 12345B)</p>
                            <p id="job_number_error_msg" class="mt-1 hidden text-xs font-medium text-red-600 dark:text-red-400">Job number must end with letter B</p>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="client_name" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Client Name <span class="text-red-500">*</span></label>
                                <input type="text" id="client_name" name="client_name" required placeholder="Enter client name" autocomplete="off" value="{{ old('client_name', request('client_name')) }}"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
                            </div>
                            <div>
                                <label for="contact_email" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Contact Email <span class="text-red-500">*</span></label>
                                <select id="contact_email" name="contact_email" required class="select2-single w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" autocomplete="off" data-placeholder="Enter contact email">
                                    <option value=""></option>
                                    @if(!empty($selectedContactEmail) && !collect($bphClientEmails ?? [])->pluck('email')->contains($selectedContactEmail))
                                        <option value="{{ $selectedContactEmail }}" selected>{{ $selectedContactEmail }}</option>
                                    @endif
                                    @foreach($bphClientEmails ?? [] as $row)
                                        <option value="{{ $row->email }}" {{ (string) $selectedContactEmail === (string) $row->email ? 'selected' : '' }}>{{ $row->email }}</option>
                                    @endforeach
                                </select>
                                @if(($bphClientEmails ?? collect())->isEmpty())
                                    <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">No emails in <span class="font-mono">client_email_bph</span> yet. Add addresses under <a href="{{ route('bph_client_email.index') }}" class="font-medium underline hover:no-underline">BPH Email</a>.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Job Details (Notes) Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Job Details</h2>
                </div>
                <div class="p-5">
                    <div>
                        <label for="bph-notes-body" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Notes (BPH)</label>
                        <input type="hidden" name="notes" id="bph_notes" autocomplete="off">
                        <div class="overflow-hidden rounded-lg border border-slate-300 dark:border-slate-600">
                            <div class="flex items-center gap-1 border-b border-slate-200 bg-slate-50 px-2 py-1.5 dark:border-slate-600 dark:bg-slate-800/80">
                                <button type="button" class="bph-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="bold" title="Bold"><span class="font-bold">B</span></button>
                                <button type="button" class="bph-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="italic" title="Italic"><span class="italic">I</span></button>
                                <button type="button" class="bph-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="underline" title="Underline"><span class="underline">U</span></button>
                                <span class="mx-1 h-5 w-px bg-slate-300 dark:bg-slate-600"></span>
                                <button type="button" class="bph-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="insertOrderedList" title="Numbered list">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h12M9 12h12M9 19h12M3 5h.01M3 12h.01M3 19h.01"/></svg>
                                </button>
                                <button type="button" class="bph-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="insertUnorderedList" title="Bullets">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M2 6h.01M2 12h.01M2 18h.01"/></svg>
                                </button>
                            </div>
                            <div id="bph-notes-body" contenteditable="true" data-placeholder="Write notes here"
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
                            <label for="bph_upload_plans" class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50/50 py-4 px-3 text-center transition-colors hover:border-emerald-400 hover:bg-emerald-50/50 dark:border-slate-600 dark:bg-slate-800/50 dark:hover:border-emerald-600 dark:hover:bg-emerald-950/30">
                                <svg class="mb-1 h-6 w-6 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Drag here</span>
                                <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-500">or</span>
                                <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">browse</span>
                                <input type="file" id="bph_upload_plans" name="upload_plans[]" class="hidden" multiple tabindex="-1">
                            </label>
                            <div id="bph-plans-file-list" class="mt-2 min-h-0 space-y-1 text-xs text-slate-600 dark:text-slate-400"></div>
                            <button type="button" id="bph-plans-clear" class="mt-1 hidden text-xs text-slate-500 hover:text-red-600 dark:text-slate-400 dark:hover:text-red-400">Clear</button>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Upload Document</label>
                            <label for="bph_upload_document" class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50/50 py-4 px-3 text-center transition-colors hover:border-emerald-400 hover:bg-emerald-50/50 dark:border-slate-600 dark:bg-slate-800/50 dark:hover:border-emerald-600 dark:hover:bg-emerald-950/30">
                                <svg class="mb-1 h-6 w-6 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-400">Drag here</span>
                                <span class="mt-0.5 text-xs text-slate-500 dark:text-slate-500">or</span>
                                <span class="rounded bg-emerald-500/20 px-2 py-0.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">browse</span>
                                <input type="file" id="bph_upload_document" name="upload_document[]" class="hidden" multiple tabindex="-1">
                            </label>
                            <div id="bph-docs-file-list" class="mt-2 min-h-0 space-y-1 text-xs text-slate-600 dark:text-slate-400"></div>
                            <button type="button" id="bph-docs-clear" class="mt-1 hidden text-xs text-slate-500 hover:text-red-600 dark:text-slate-400 dark:hover:text-red-400">Clear</button>
                        </div>
                    </div>
                </div>
            </div>

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
                                @include('partials.assignment-user-options', ['assignmentUsers' => $assignmentStaffUsers ?? [], 'selected' => '', 'includeSelectPlaceholder' => true, 'includeGm' => false])
                            </select>
                        </div>
                        <div>
                            <label for="checked_by" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Checked By</label>
                            <select id="checked_by" name="checked_by" class="select2-single assignment-user-select w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                                @include('partials.assignment-user-options', ['assignmentUsers' => $assignmentCheckerUsers ?? [], 'selected' => '', 'includeSelectPlaceholder' => true, 'includeGm' => false])
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" id="submitBPHBtn"
                    class="cursor-pointer inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Save Job
                </button>
                <a href="{{ route('bph.list') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
.lbs-after-save-overlay { animation: lbsOverlayFadeIn 0.25s ease forwards; }
.lbs-after-save-dialog {
    animation: lbsDialogScaleIn 0.35s ease 0.1s forwards;
    transform: scale(0.9);
    opacity: 0;
}
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
            var notesBody = document.getElementById('bph-notes-body');
            var noteBtns = document.querySelectorAll('.bph-notes-btn');

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

            function initBphSelect2() {
                $('#bphAddForm select:not(.assignment-user-select)').each(function() {
                    var $el = $(this);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }
                    var ph = $el.data('placeholder');
                    var opts = { width: '100%', allowClear: false };
                    if (ph) {
                        opts.placeholder = ph;
                        opts.allowClear = true;
                        opts.minimumResultsForSearch = 0;
                    }
                    $el.select2(opts);
                });
            }
            initBphSelect2();
            initAssignmentUserSelect2($('#bphAddForm'));

            // Job number validation: 5 digits + letter B (e.g. 12345B)
            var jobNumberInput = document.getElementById('job_number');
            var jobNumberErrorIcon = document.getElementById('job_number_error_icon');
            var jobNumberErrorMsg = document.getElementById('job_number_error_msg');

            function validateJobNumber() {
                var val = (jobNumberInput.value || '').trim();
                var isValid = val === '' || /^\d{5}B$/i.test(val);
                if (val.length > 0 && !isValid) {
                    jobNumberInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500/25', 'dark:border-red-500');
                    jobNumberInput.classList.remove('border-slate-300', 'focus:border-emerald-500', 'focus:ring-emerald-500/25', 'dark:border-slate-600');
                    if (jobNumberErrorIcon) jobNumberErrorIcon.classList.remove('hidden');
                    if (jobNumberErrorMsg) jobNumberErrorMsg.classList.remove('hidden');
                } else {
                    jobNumberInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500/25', 'dark:border-red-500');
                    jobNumberInput.classList.add('border-slate-300', 'focus:border-emerald-500', 'focus:ring-emerald-500/25', 'dark:border-slate-600');
                    if (jobNumberErrorIcon) jobNumberErrorIcon.classList.add('hidden');
                    if (jobNumberErrorMsg) jobNumberErrorMsg.classList.add('hidden');
                }
                return isValid;
            }

            if (jobNumberInput) {
                jobNumberInput.addEventListener('input', validateJobNumber);
                jobNumberInput.addEventListener('blur', validateJobNumber);
            }

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

            renderFileList('bph_upload_plans', 'bph-plans-file-list', 'bph-plans-clear');
            renderFileList('bph_upload_document', 'bph-docs-file-list', 'bph-docs-clear');

            var $btn = $('#submitBPHBtn');
            var originalBtnHtml = $btn.html();

            $btn.on('click', function(e) {
                e.preventDefault();
                document.getElementById('bph_notes').value = notesBody.innerHTML;

                if (!validateJobNumber()) {
                    jobNumberInput.focus();
                    return;
                }

                var formEl = document.getElementById('bphAddForm');
                var formData = new FormData(formEl);
                var headerRef = ($('#bphJobReferenceContent').text() || '').trim();
                if (headerRef) {
                    formData.append('header_reference', headerRef);
                }

                $.ajax({
                    url: '{{ route("bph.store") }}',
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
                        if (resp && resp.status === 'success') {
                            if (window.showSuccessToast) showSuccessToast(resp.message || 'Job saved.');
                            formEl.reset();
                            notesBody.innerHTML = '';
                            document.getElementById('bph_upload_plans') && document.getElementById('bph_upload_plans').dispatchEvent(new Event('change'));
                            document.getElementById('bph_upload_document') && document.getElementById('bph_upload_document').dispatchEvent(new Event('change'));
                            initBphSelect2();
                            showBphAfterSavePrompt(resp.job_id);
                        } else {
                            if (window.showSuccessToast) showSuccessToast((resp && resp.message) ? resp.message : 'Failed to save job.');
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Unexpected error while saving.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        if (window.showSuccessToast) showSuccessToast(msg);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).removeClass('is-loading').html(originalBtnHtml);
                    }
                });
            });

            function showBphAfterSavePrompt(jobId) {
                var sendSlackUrl = '{{ url("dashboard/bph/job") }}/' + jobId + '/send-slack';
                var sendUrl = '{{ url("dashboard/bph/job") }}/' + jobId + '/send-submission-email';
                var listUrl = '{{ route("bph.list") }}';

                var $overlay = $(
                    '<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 lbs-after-save-overlay">' +
                        '<div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800 overflow-hidden lbs-after-save-dialog">' +
                            '<div class="p-6 text-center lbs-modal-step lbs-modal-step-question">' +
                                '<div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/40">' +
                                    '<svg class="h-7 w-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' +
                                '</div>' +
                                '<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Job saved</h3>' +
                                '<p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Do you want to create another BPH job?</p>' +
                                '<div class="mt-6 flex gap-3">' +
                                    '<button type="button" data-bph-go-list class="cursor-pointer flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">Go to BPH list</button>' +
                                    '<button type="button" data-bph-new-job class="cursor-pointer flex-1 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-500">Create another job</button>' +
                                '</div>' +
                            '</div>' +
                            '<div class="p-6 text-center lbs-modal-step lbs-modal-step-slack" style="display:none;">' +
                                '<div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[#4A154B]/10 dark:bg-[#4A154B]/20">' +
                                    '<span class="lbs-slack-spinner inline-block h-7 w-7 rounded-full border-2 border-[#4A154B]/30 border-t-[#4A154B] dark:border-t-[#E01E5A]"></span>' +
                                '</div>' +
                                '<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Sending to Slack...</h3>' +
                                '<p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Notifying your channel.</p>' +
                            '</div>' +
                            '<div class="p-6 text-center lbs-modal-step lbs-modal-step-sending" style="display:none;">' +
                                '<div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 dark:bg-slate-700">' +
                                    '<span class="lbs-send-spinner inline-block h-7 w-7 rounded-full border-2 border-slate-300 border-t-emerald-500 dark:border-slate-600 dark:border-t-emerald-400"></span>' +
                                '</div>' +
                                '<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Sending submission email...</h3>' +
                                '<p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Please wait.</p>' +
                            '</div>' +
                            '<div class="p-6 text-center lbs-modal-step lbs-modal-step-sent" style="display:none;">' +
                                '<div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/40 lbs-email-sent-animate">' +
                                    '<svg class="h-7 w-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>' +
                                '</div>' +
                                '<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Email sent!</h3>' +
                                '<p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Submission email has been sent.</p>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );

                $('body').append($overlay);

                $overlay.on('click', function(e) {
                    if (e.target === this) $overlay.remove();
                });

                function sendEmailThen(action) {
                    var $question = $overlay.find('.lbs-modal-step-question');
                    var $slack = $overlay.find('.lbs-modal-step-slack');
                    var $sending = $overlay.find('.lbs-modal-step-sending');
                    var $sent = $overlay.find('.lbs-modal-step-sent');

                    $question.hide();
                    $slack.show().addClass('lbs-step-animate-in');
                    $sending.hide();
                    $sent.hide();

                    function goToEmailStep() {
                        $slack.hide().removeClass('lbs-step-animate-in');
                        $sending.show().addClass('lbs-sending-animate-in');
                        $.ajax({
                            url: sendUrl,
                            method: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            dataType: 'json'
                        }).done(function(resp) {
                            if (resp && resp.status === 'success') {
                                $sending.hide().removeClass('lbs-sending-animate-in');
                                $sent.show().addClass('lbs-email-sent-animate');
                                setTimeout(function() {
                                    $overlay.remove();
                                    if (action === 'list') {
                                        window.location.href = listUrl;
                                    }
                                }, 1200);
                                return;
                            }

                            $sending.hide().removeClass('lbs-sending-animate-in');
                            $question.show();
                            if (window.showSuccessToast) showSuccessToast((resp && resp.message) ? resp.message : 'Could not send email.');
                        }).fail(function() {
                            $sending.hide().removeClass('lbs-sending-animate-in');
                            $question.show();
                            if (window.showSuccessToast) showSuccessToast('Could not send email. Try again or go to list.');
                        });
                    }

                    $.ajax({
                        url: sendSlackUrl,
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        dataType: 'json'
                    }).done(function() {
                        goToEmailStep();
                    }).fail(function() {
                        goToEmailStep();
                    });
                }

                $overlay.find('[data-bph-new-job]').on('click', function() {
                    sendEmailThen('stay');
                });

                $overlay.find('[data-bph-go-list]').on('click', function() {
                    sendEmailThen('list');
                });
            }
        });
    </script>
@endpush
