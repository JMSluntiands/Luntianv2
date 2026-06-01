@extends('layouts.dashboard')

@section('title', 'Add New Job (LEADING ENERGY)')

@section('body_class', 'page-leading_energy-add')

@section('content')
    <div class="w-full max-w-full px-0">
        <div class="mb-8">
            <h1 class="mb-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Add New Job (LEADING ENERGY)</h1>
            <p class="text-slate-500 dark:text-slate-400">Fill in the form below to create a new LEADING ENERGY job.</p>
        </div>

        <form id="leadingEnergyAddForm" action="#" method="POST" autocomplete="off" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Client Details</h2>
                    <span id="leadingEnergyJobReferenceContent" class="rounded-lg bg-slate-200/80 px-3 py-1.5 font-mono text-sm font-medium text-slate-700 dark:bg-slate-700 dark:text-slate-300">JOB0903-001</span>
                </div>
                <div class="p-5">
                    @php
                        $selCompliance = $defaultComplianceId ?? null;
                        $selJobRequest = $defaultJobRequestId ?? null;
                        $selectedContactEmail = old('contact_email', request('contact_email'));
                        $selectedUrgent = old('urgent_job', request('urgent_job'));
                    @endphp
                    <div class="space-y-5">
                        <label class="inline-flex cursor-pointer items-center gap-2.5">
                            <input type="checkbox" id="urgent_job" name="urgent_job" value="1" autocomplete="off" {{ (string) $selectedUrgent === '1' ? 'checked' : '' }} class="h-4 w-4 shrink-0 rounded border-2 border-slate-300 bg-white text-emerald-600 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:border-slate-500 dark:bg-slate-700">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Urgent Job (YES)</span>
                        </label>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="ncc_compliance" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">NCC Compliance</label>
                                <select id="ncc_compliance" name="ncc_compliance" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                                    <option value="">Select compliance</option>
                                    @foreach($compliances ?? [] as $c)
                                        <option value="{{ $c->id }}" {{ $selCompliance !== null && (int) $selCompliance === (int) $c->id ? 'selected' : '' }}>{{ $c->column ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="job_type_request" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Job Type Request</label>
                                <select id="job_type_request" name="job_type_request" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
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
                                <input type="text" id="job_number" name="job_number" required placeholder="e.g. 12345B" maxlength="6" value="{{ old('job_number', request('job_number')) }}" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 pr-10 text-slate-800 placeholder-slate-400 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                                <span id="job_number_error_icon" class="pointer-events-none absolute right-3 top-1/2 hidden -translate-y-1/2 rounded-full bg-red-100 p-1 text-red-600 dark:bg-red-900/40 dark:text-red-400" aria-hidden="true">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">5 digits + letter B only, max 6 characters.</p>
                            <p id="job_number_error_msg" class="mt-1 hidden text-xs font-medium text-red-600 dark:text-red-400">Job number must end with letter B</p>
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="client_name" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Client Name <span class="text-red-500">*</span></label>
                                <input type="text" id="client_name" name="client_name" required value="{{ old('client_name', request('client_name')) }}" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                            </div>
                            <div>
                                <label for="contact_email" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Contact Email <span class="text-red-500">*</span></label>
                                <select id="contact_email" name="contact_email" required class="select2-single w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" data-placeholder="Enter contact email">
                                    <option value=""></option>
                                    @if(!empty($selectedContactEmail) && !collect($bphClientEmails ?? [])->pluck('email')->contains($selectedContactEmail))
                                        <option value="{{ $selectedContactEmail }}" selected>{{ $selectedContactEmail }}</option>
                                    @endif
                                    @foreach($bphClientEmails ?? [] as $row)
                                        <option value="{{ $row->email }}" {{ (string) $selectedContactEmail === (string) $row->email ? 'selected' : '' }}>{{ $row->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Job Details</h2>
                </div>
                <div class="p-5">
                    <label for="le-notes-body" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Notes (LEADING ENERGY)</label>
                    <input type="hidden" name="notes" id="le_notes" autocomplete="off">
                    <div class="overflow-hidden rounded-lg border border-slate-300 dark:border-slate-600">
                        <div class="flex items-center gap-1 border-b border-slate-200 bg-slate-50 px-2 py-1.5 dark:border-slate-600 dark:bg-slate-800/80">
                            <button type="button" class="le-notes-btn rounded p-2 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="bold"><span class="font-bold">B</span></button>
                            <button type="button" class="le-notes-btn rounded p-2 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="italic"><span class="italic">I</span></button>
                            <button type="button" class="le-notes-btn rounded p-2 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="underline"><span class="underline">U</span></button>
                            <span class="mx-1 h-5 w-px bg-slate-300 dark:bg-slate-600"></span>
                            <button type="button" class="le-notes-btn rounded p-2 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="insertOrderedList" title="Numbered list">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h12M9 12h12M9 19h12M3 5h.01M3 12h.01M3 19h.01"/></svg>
                            </button>
                            <button type="button" class="le-notes-btn rounded p-2 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="insertUnorderedList" title="Bullets">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M2 6h.01M2 12h.01M2 18h.01"/></svg>
                            </button>
                        </div>
                        <div id="le-notes-body" contenteditable="true" data-placeholder="Write notes here" class="min-h-[120px] bg-white px-4 py-3 text-slate-800 dark:bg-slate-800 dark:text-slate-100 [&:empty::before]:content-[attr(data-placeholder)] [&:empty::before]:text-slate-400"></div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80"><h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Attachments</h2></div>
                <div class="p-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Upload Plans</label>
                        <input type="file" id="le_upload_plans" name="upload_plans[]" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" multiple>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Upload Document</label>
                        <input type="file" id="le_upload_document" name="upload_document[]" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" multiple>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80"><h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Assignment</h2></div>
                <div class="p-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="assigned_to" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Assigned To</label>
                        <select id="assigned_to" name="assigned_to" class="select2-single assignment-user-select w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                            @include('partials.assignment-user-options', ['selected' => 'GM', 'includeSelectPlaceholder' => true])
                        </select>
                    </div>
                    <div>
                        <label for="checked_by" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Checked By</label>
                        <select id="checked_by" name="checked_by" class="select2-single assignment-user-select w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                            @include('partials.assignment-user-options', ['selected' => 'GM', 'includeSelectPlaceholder' => true])
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="button" id="submitLEBtn" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-500">Save Job</button>
                <a href="{{ route('leading_energy.list') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 dark:border-slate-600 dark:text-slate-300">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
.lbs-after-save-overlay { animation: lbsOverlayFadeIn 0.25s ease forwards; }
.lbs-after-save-dialog { animation: lbsDialogScaleIn 0.35s ease 0.1s forwards; transform: scale(0.9); opacity: 0; }
.lbs-modal-step-slack, .lbs-modal-step-sending { opacity: 0; transform: translateY(8px); }
.lbs-modal-step-slack.lbs-step-animate-in, .lbs-modal-step-sending.lbs-sending-animate-in { animation: lbsSendingStepIn 0.4s ease forwards; }
.lbs-slack-spinner, .lbs-send-spinner { animation: lbsSpinner 0.8s linear infinite; }
.lbs-email-sent-animate { animation: lbsEmailSentFadeIn 0.5s ease 0.3s forwards; opacity: 0; }
@keyframes lbsOverlayFadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes lbsDialogScaleIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
@keyframes lbsSendingStepIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
@keyframes lbsSpinner { to { transform: rotate(360deg); } }
@keyframes lbsEmailSentFadeIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function() {
    var notesBody = document.getElementById('le-notes-body');
    document.querySelectorAll('.le-notes-btn').forEach(function(btn) {
        btn.addEventListener('mousedown', function(e) {
            e.preventDefault();
            notesBody.focus();
            document.execCommand(this.getAttribute('data-cmd'), false, null);
        });
    });
    $('#leadingEnergyAddForm select:not(.assignment-user-select)').select2({ width: '100%', allowClear: false });
    initAssignmentUserSelect2($('#leadingEnergyAddForm'));

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

    var $btn = $('#submitLEBtn');
    var originalBtnHtml = $btn.html();
    $btn.on('click', function(e) {
        e.preventDefault();
        document.getElementById('le_notes').value = notesBody.innerHTML;
        if (!validateJobNumber()) {
            jobNumberInput.focus();
            return;
        }
        var formEl = document.getElementById('leadingEnergyAddForm');
        var formData = new FormData(formEl);
        var headerRef = ($('#leadingEnergyJobReferenceContent').text() || '').trim();
        if (headerRef) formData.append('header_reference', headerRef);

        $.ajax({
            url: '{{ route("leading_energy.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                $btn.prop('disabled', true).html('<span class="mr-2 inline-block h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>Saving...');
            },
            success: function(resp) {
                if (resp && resp.status === 'success') {
                    if (window.showSuccessToast) showSuccessToast(resp.message || 'Job saved.');
                    formEl.reset();
                    notesBody.innerHTML = '';
                    $('#leadingEnergyAddForm select').each(function() { $(this).val('').trigger('change'); });
                    $('#assigned_to').val('GM').trigger('change');
                    $('#checked_by').val('GM').trigger('change');
                    showLEAfterSavePrompt(resp.job_id);
                } else {
                    if (window.showSuccessToast) showSuccessToast((resp && resp.message) ? resp.message : 'Failed to save job.');
                }
            },
            error: function(xhr) {
                var msg = 'Unexpected error while saving.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (window.showSuccessToast) showSuccessToast(msg);
            },
            complete: function() { $btn.prop('disabled', false).html(originalBtnHtml); }
        });
    });

    function showLEAfterSavePrompt(jobId) {
        var sendSlackUrl = '{{ url("dashboard/leading-energy/job") }}/' + jobId + '/send-slack';
        var listUrl = '{{ route("leading_energy.list") }}';
        var $overlay = $(
            '<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 lbs-after-save-overlay">' +
                '<div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800 overflow-hidden lbs-after-save-dialog">' +
                    '<div class="p-6 text-center lbs-modal-step lbs-modal-step-question">' +
                        '<div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/40">' +
                            '<svg class="h-7 w-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' +
                        '</div>' +
                        '<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Job saved</h3>' +
                        '<p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Do you want to create another LEADING ENERGY job?</p>' +
                        '<div class="mt-6 flex gap-3">' +
                            '<button type="button" data-le-go-list class="cursor-pointer flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">Go to list</button>' +
                            '<button type="button" data-le-new-job class="cursor-pointer flex-1 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-500">Create another job</button>' +
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
        function proceedAfterSlack(action) {
            var $question = $overlay.find('.lbs-modal-step-question');
            var $updating = $overlay.find('.lbs-modal-step-updating');
            var $goListBtn = $overlay.find('[data-le-go-list]');
            var $newJobBtn = $overlay.find('[data-le-new-job]');
            $question.hide(); $updating.show();
            $goListBtn.prop('disabled', true).addClass('opacity-60 pointer-events-none');
            $newJobBtn.prop('disabled', true).addClass('opacity-60 pointer-events-none');
            $.post(sendSlackUrl, { _token: '{{ csrf_token() }}' }).always(function() {
                $overlay.remove();
                if (action === 'list') {
                    window.location.href = listUrl;
                } else if (action === 'stay') {
                    window.location.reload();
                }
            });
        }
        $overlay.find('[data-le-new-job]').on('click', function() { proceedAfterSlack('stay'); });
        $overlay.find('[data-le-go-list]').on('click', function() { proceedAfterSlack('list'); });
        $overlay.on('click', function(e) { if (e.target === this) $overlay.remove(); });
    }
});
</script>
@endpush
