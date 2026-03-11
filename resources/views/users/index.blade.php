@extends('layouts.dashboard')

@section('title', 'User Accounts')

@section('body_class', 'page-users-index')

@section('content')
    <div class="staff-page staff-page-enter">
        <div class="staff-header">
            <div class="staff-header-text">
                <h1 class="staff-title">User Accounts</h1>
                <p class="staff-subtitle">View and manage application users (excluding admin).</p>
            </div>
            <a href="{{ route('users.create') }}" class="btn-staff-add">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add New
            </a>
        </div>

        <div class="staff-table-card">
            <div class="staff-table-wrap">
                <table class="staff-table" id="usersTable">
                    <colgroup>
                        <col class="staff-col-id">
                        <col class="staff-col-code">
                        <col class="staff-col-name">
                        <col class="staff-col-name">
                        <col class="staff-col-name">
                        <col class="staff-col-name">
                        <col class="staff-col-name">
                        <col class="staff-col-name">
                        <col class="staff-col-action">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="staff-th">ID</th>
                            <th class="staff-th">Code</th>
                            <th class="staff-th">Username</th>
                            <th class="staff-th">Email</th>
                            <th class="staff-th">Full Name</th>
                            <th class="staff-th">Role</th>
                            <th class="staff-th">Branch</th>
                            <th class="staff-th">Status</th>
                            <th class="staff-th staff-th-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="staff-td">{{ $user->id }}</td>
                                <td class="staff-td">{{ $user->unique_code }}</td>
                                <td class="staff-td">{{ $user->username }}</td>
                                <td class="staff-td">{{ $user->email }}</td>
                                <td class="staff-td">{{ $user->fullname }}</td>
                                <td class="staff-td">{{ $user->role }}</td>
                                <td class="staff-td">{{ $user->branch ?: '—' }}</td>
                                <td class="staff-td">{{ $user->task ?: 'Active' }}</td>
                                <td class="staff-td staff-td-action">
                                    <div class="staff-action-btns">
                                        <a href="{{ route('users.edit', $user) }}" class="staff-action-icon staff-action-edit" title="Edit" aria-label="Edit">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="staff-delete-form" data-delete-form autocomplete="off">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="staff-action-icon staff-action-delete" data-delete-trigger title="Delete" aria-label="Delete">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="staff-td staff-td-empty">No user accounts yet. <a href="{{ route('users.create') }}">Add one</a>.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="staff-pagination">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="modal-backdrop" id="deleteUserModal" role="dialog" aria-labelledby="deleteUserModalTitle" aria-modal="true">
        <div class="modal-box">
            <div class="modal-header">
                <svg class="modal-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <h2 class="modal-title" id="deleteUserModalTitle">Archive User</h2>
            </div>
            <div class="modal-body">
                <div class="delete-modal-confirm" id="deleteUserModalConfirmText">
                    <p>Are you sure you want to move this user to archive? This action cannot be undone.</p>
                </div>
                <div class="delete-modal-countdown" id="deleteUserModalCountdown" hidden>
                    <p class="delete-countdown-text">Archiving in</p>
                    <div class="delete-countdown-number" id="deleteUserCountdownNumber">3</div>
                    <p class="delete-countdown-cancel-hint">Click Cancel to abort</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" id="deleteUserModalCancel">Cancel</button>
                <button type="button" class="btn btn-confirm btn-danger" id="deleteUserModalConfirm"><span class="btn-text">Archive</span></button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .staff-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-users-index .content { padding-bottom: 0; }
        .staff-header { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.75rem; }
        .staff-header-text { min-width: 0; }
        .staff-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .staff-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .btn-staff-add { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; font-size: 0.9375rem; font-weight: 600; color: #fff; background: #2C528B; border-radius: 10px; text-decoration: none; transition: background 0.2s; box-shadow: 0 2px 6px rgba(44,82,139,0.35); }
        .btn-staff-add:hover { background: #234a77; color: #fff; }
        .staff-page-enter { animation: staff-page-in 0.4s ease-out; }
        @keyframes staff-page-in { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .staff-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.15); max-width: 100%; }
        .staff-table-wrap { overflow-x: auto; }
        .staff-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .staff-col-id { width: 70px; }
        .staff-col-code { width: 90px; }
        .staff-col-name { width: auto; }
        .staff-col-action { width: 110px; }
        .staff-th { text-align: left; padding: 0.75rem 1rem; font-weight: 600; color: #94a3b8; background: #1e293b; border-bottom: 1px solid #334155; }
        .staff-th-action { text-align:fü center; }
        .staff-td { padding: 0.75rem 1rem; border-bottom: 1px solid #334155; color: #e2e8f0; vertical-align: middle; }
        .staff-td-action { text-align: center; }
        .staff-td-empty { text-align: center; color: #94a3b8; padding: 2rem; }
        .staff-td-empty a { color: #2C528B; text-decoration: none; }
        .staff-td-empty a:hover { text-decoration: underline; }
        .staff-action-btns { display: flex; align-items: center; gap: 0.35rem; justify-content: center; flex-wrap: wrap; }
        .staff-delete-form { display: inline; margin: 0; padding: 0; }
        .staff-action-icon { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; border: none; border-radius: 8px; background: transparent; color: #94a3b8; cursor: pointer; transition: background 0.15s, color 0.15s; text-decoration: none; }
        .staff-action-icon:hover { color: #e2e8f0; background: rgba(255,255,255,0.08); }
        .staff-action-icon svg { display: block; pointer-events: none; }
        .staff-action-edit:hover { color: #93c5fd; background: rgba(44,82,139,0.25); }
        .staff-action-delete:hover { color: #fca5a5; background: rgba(248,113,113,0.15); }
        .staff-pagination { padding: 1rem; border-top: 1px solid #334155; }
        .staff-table tbody tr:hover { background: rgba(255,255,255,0.03); }
        html[data-theme="light"] .staff-table-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .staff-title { color: #1e293b; }
        html[data-theme="light"] .staff-subtitle { color: #64748b; }
        html[data-theme="light"] .staff-th { background: #f8fafc; color: #64748b; border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .staff-td { border-bottom-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .staff-table tbody tr:hover { background: #f8fafc; }
        html[data-theme="light"] .staff-action-icon { color: #64748b; }
        html[data-theme="light"] .staff-action-icon:hover { color: #334155; background: #e2e8f0; }
        .delete-modal-confirm p { margin: 0; }
        .delete-modal-countdown { text-align: center; padding: 0.5rem 0; }
        .delete-countdown-text { font-size: 0.9375rem; color: #94a3b8; margin: 0 0 1rem 0; }
        .delete-countdown-number { font-size: 4rem; font-weight: 800; color: #f87171; line-height: 1; letter-spacing: -0.05em; min-height: 4rem; display: flex; align-items: center; justify-content: center; animation: delete-countdown-pop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .delete-countdown-cancel-hint { font-size: 0.8125rem; color: #64748b; margin: 1rem 0 0 0; }
        @keyframes delete-countdown-pop { 0% { opacity: 0; transform: scale(0.3); } 70% { transform: scale(1.1); } 100% { opacity: 1; transform: scale(1); } }
        html[data-theme="light"] .delete-countdown-text { color: #64748b; }
        html[data-theme="light"] .delete-countdown-number { color: #dc2626; }
        html[data-theme="light"] .delete-countdown-cancel-hint { color: #94a3b8; }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            var modal = document.getElementById('deleteUserModal');
            var cancelBtn = document.getElementById('deleteUserModalCancel');
            var confirmBtn = document.getElementById('deleteUserModalConfirm');
            var confirmBlock = document.getElementById('deleteUserModalConfirmText');
            var countdownBlock = document.getElementById('deleteUserModalCountdown');
            var countdownNumber = document.getElementById('deleteUserCountdownNumber');
            var formToSubmit = null;
            var countdownTimer = null;

            function resetModal() {
                if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
                confirmBlock.hidden = false;
                countdownBlock.hidden = true;
                confirmBtn.disabled = false;
                confirmBtn.querySelector('.btn-text').textContent = 'Archive';
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
                confirmBtn.querySelector('.btn-text').textContent = 'Archiving...';
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

