@extends('layouts.dashboard')

@section('title', 'Job Request List')

@section('body_class', 'page-job-request-index')

@section('content')
    <div class="jobrequest-page jobrequest-page-enter">
        <div class="jobrequest-header">
            <div class="jobrequest-header-text">
                <h1 class="jobrequest-title">Job Request</h1>
                <p class="jobrequest-subtitle">View and manage job request types (client code, request ID, type).</p>
            </div>
            <a href="{{ route('job_request.create') }}" class="btn-jobrequest-add">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add New
            </a>
        </div>

        @if(session('success'))
            <div class="jobrequest-alert jobrequest-alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="jobrequest-table-card">
            <div class="jobrequest-table-wrap">
                <table class="jobrequest-table" id="jobRequestTable">
                    <colgroup>
                        <col class="jobrequest-col-id">
                        <col class="jobrequest-col-client">
                        <col class="jobrequest-col-request-id">
                        <col class="jobrequest-col-type">
                        <col class="jobrequest-col-action">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="jobrequest-th">ID</th>
                            <th class="jobrequest-th">Client Code</th>
                            <th class="jobrequest-th">Job Request ID</th>
                            <th class="jobrequest-th">Job Request Type</th>
                            <th class="jobrequest-th jobrequest-th-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobRequests as $jr)
                            <tr>
                                <td class="jobrequest-td">{{ $jr->id }}</td>
                                <td class="jobrequest-td">{{ $jr->client_code ?? '—' }}</td>
                                <td class="jobrequest-td"><code class="jobrequest-code">{{ $jr->job_request_id ?? '—' }}</code></td>
                                <td class="jobrequest-td">{{ $jr->job_request_type ?? '—' }}</td>
                                <td class="jobrequest-td jobrequest-td-action">
                                    <div class="jobrequest-action-btns">
                                        <a href="{{ route('job_request.edit', $jr) }}" class="jobrequest-action-icon jobrequest-action-edit" title="Edit" aria-label="Edit">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <form action="{{ route('job_request.destroy', $jr) }}" method="POST" class="jobrequest-delete-form" data-delete-form autocomplete="off">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="jobrequest-action-icon jobrequest-action-delete" data-delete-trigger title="Delete" aria-label="Delete">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="jobrequest-td jobrequest-td-empty">No job requests yet. <a href="{{ route('job_request.create') }}">Add one</a>.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($jobRequests->hasPages())
                <div class="jobrequest-pagination">
                    {{ $jobRequests->links('vendor.pagination.dashboard') }}
                </div>
            @endif
        </div>
    </div>

    <div class="modal-backdrop" id="deleteJobRequestModal" role="dialog" aria-labelledby="deleteJobRequestModalTitle" aria-modal="true">
        <div class="modal-box">
            <div class="modal-header">
                <svg class="modal-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <h2 class="modal-title" id="deleteJobRequestModalTitle">Delete Job Request</h2>
            </div>
            <div class="modal-body">
                <div class="delete-modal-confirm" id="deleteModalConfirm">
                    <p>Are you sure you want to delete this job request? This action cannot be undone.</p>
                </div>
                <div class="delete-modal-countdown" id="deleteModalCountdown" hidden>
                    <p class="delete-countdown-text">Deleting in</p>
                    <div class="delete-countdown-number" id="deleteCountdownNumber">3</div>
                    <p class="delete-countdown-cancel-hint">Click Cancel to abort</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" id="deleteJobRequestModalCancel">Cancel</button>
                <button type="button" class="btn btn-confirm btn-danger" id="deleteJobRequestModalConfirm"><span class="btn-text">Delete</span></button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @endpush

@push('scripts')
    <script>
        (function() {
            var modal = document.getElementById('deleteJobRequestModal');
            var cancelBtn = document.getElementById('deleteJobRequestModalCancel');
            var confirmBtn = document.getElementById('deleteJobRequestModalConfirm');
            var confirmBlock = document.getElementById('deleteModalConfirm');
            var countdownBlock = document.getElementById('deleteModalCountdown');
            var countdownNumber = document.getElementById('deleteCountdownNumber');
            var formToSubmit = null;
            var countdownTimer = null;

            function resetModal() {
                if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
                confirmBlock.hidden = false;
                countdownBlock.hidden = true;
                confirmBtn.disabled = false;
                confirmBtn.querySelector('.btn-text').textContent = 'Delete';
            }
            function closeModal() {
                modal.classList.remove('show');
                formToSubmit = null;
                resetModal();
            }

            document.querySelectorAll('[data-delete-trigger]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    formToSubmit = this.closest('[data-delete-form]');
                    if (formToSubmit && modal) { resetModal(); modal.classList.add('show'); }
                });
            });
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
            if (modal) modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
            if (confirmBtn) confirmBtn.addEventListener('click', function() {
                if (!formToSubmit || countdownTimer) return;
                confirmBlock.hidden = true;
                countdownBlock.hidden = false;
                confirmBtn.disabled = true;
                confirmBtn.querySelector('.btn-text').textContent = 'Deleting...';
                var count = 3;
                countdownNumber.textContent = count;
                countdownNumber.style.animation = 'none';
                countdownNumber.offsetHeight;
                countdownNumber.style.animation = '';
                countdownTimer = setInterval(function() {
                    count--;
                    if (count <= 0) {
                        clearInterval(countdownTimer);
                        countdownTimer = null;
                        formToSubmit.submit();
                        return;
                    }
                    countdownNumber.textContent = count;
                    countdownNumber.style.animation = 'none';
                    countdownNumber.offsetHeight;
                    countdownNumber.style.animation = '';
                }, 1000);
            });
        })();
    </script>
@endpush
