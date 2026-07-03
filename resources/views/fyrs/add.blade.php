@extends('layouts.dashboard')

@section('title', 'Add New Job (Fyrs Energy Wise)')

@section('body_class', 'page-fyrs-add')

@section('content')
    <div class="w-full max-w-full px-0">
        <div class="mb-8">
            <h1 class="mb-2 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Add New Job (Fyrs Energy Wise)</h1>
            <p class="text-slate-500 dark:text-slate-400">NatHERS and BASIX assessor workflow — fields match the Luntian Assessor spreadsheet.</p>
        </div>

        <form id="fyrsAddForm" action="#" method="POST" autocomplete="off" class="space-y-6">
            @csrf

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                <div class="border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">NatHERS &amp; BASIX Workflow</h2>
                </div>
                <div class="p-5">
                    @include('fyrs.partials.assessor-workflow-fields', ['idPrefix' => 'fyrs_'])
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="button" id="submitBluinqBtn"
                    class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Save Job
                </button>
                <a href="{{ route('fyrs.list') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</a>
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
@keyframes lbsOverlayFadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes lbsDialogScaleIn {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
</style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            var notesBody = document.getElementById('fyrs-notes-body');
            var noteBtns = document.querySelectorAll('.fyrs-notes-btn');

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

            if (notesBody) {
                notesBody.addEventListener('focus', updateNotesActiveState);
                notesBody.addEventListener('keyup', updateNotesActiveState);
                notesBody.addEventListener('mouseup', updateNotesActiveState);
            }

            $('#fyrsAddForm select.select2-single').each(function() {
                $(this).select2({ width: '100%', allowClear: true });
            });

            var $btn = $('#submitBluinqBtn');
            var originalBtnHtml = $btn.html();

            $btn.on('click', function(e) {
                e.preventDefault();
                if (notesBody) {
                    document.getElementById('fyrs_notes').value = notesBody.innerHTML;
                }

                var formEl = document.getElementById('fyrsAddForm');
                var formData = new FormData(formEl);

                $.ajax({
                    url: '{{ route("fyrs.store") }}',
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
                            if (notesBody) notesBody.innerHTML = '';
                            $('#fyrsAddForm select.select2-single').val('').trigger('change');
                            showFyrsAfterSavePrompt(resp.job_id);
                        } else {
                            if (window.showSuccessToast) showSuccessToast((resp && resp.message) ? resp.message : 'Failed to save job.');
                        }
                    },
                    error: function(xhr) {
                        var msg = 'Unexpected error while saving.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var first = Object.values(xhr.responseJSON.errors)[0];
                            if (Array.isArray(first) && first[0]) msg = first[0];
                        }
                        if (window.showSuccessToast) showSuccessToast(msg);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).removeClass('is-loading').html(originalBtnHtml);
                    }
                });
            });

            function showFyrsAfterSavePrompt(jobId) {
                var sendSlackUrl = '{{ url("dashboard/fyrs/job") }}/' + jobId + '/send-slack';
                var listUrl = '{{ route("fyrs.list") }}';

                var $overlay = $(
                    '<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 lbs-after-save-overlay">' +
                        '<div class="w-full max-w-sm overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800 lbs-after-save-dialog">' +
                            '<div class="p-6 text-center">' +
                                '<div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/40">' +
                                    '<svg class="h-7 w-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' +
                                '</div>' +
                                '<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Job saved</h3>' +
                                '<p class="mt-4 text-sm text-slate-500 dark:text-slate-400">Do you want to create another Fyrs Energy Wise job?</p>' +
                                '<div class="mt-6 flex gap-3">' +
                                    '<button type="button" data-fyrs-go-list class="flex-1 cursor-pointer rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">Go to list</button>' +
                                    '<button type="button" data-fyrs-new-job class="flex-1 cursor-pointer rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-500">Create another</button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );

                $('body').append($overlay);

                $overlay.on('click', function(e) {
                    if (e.target === this) $overlay.remove();
                });

                function proceedAfterSlack(action) {
                    $.ajax({
                        url: sendSlackUrl,
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        dataType: 'json'
                    }).always(function() {
                        $overlay.remove();
                        if (action === 'list') {
                            window.location.href = listUrl;
                        } else {
                            window.location.reload();
                        }
                    });
                }

                $overlay.find('[data-fyrs-new-job]').on('click', function() { proceedAfterSlack('stay'); });
                $overlay.find('[data-fyrs-go-list]').on('click', function() { proceedAfterSlack('list'); });
            }
        });
    </script>
@endpush
