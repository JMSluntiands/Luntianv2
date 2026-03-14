@extends('layouts.dashboard')

@section('title', 'Client Accounts')

@section('body_class', 'page-accounts-clients-index')

@section('content')
    <div class="ac-client-page ac-client-page-enter">
        <div class="ac-client-header">
            <div class="ac-client-header-text">
                <h1 class="ac-client-title">Client Accounts</h1>
                <p class="ac-client-subtitle">Manage client accounts linked to user codes (unique_code from Users).</p>
            </div>
            <a href="{{ route('accounts.clients.create') }}" class="btn-ac-client-add">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Client
            </a>
        </div>

        <div class="ac-client-table-card">
            <div class="ac-client-table-wrap">
                <table class="ac-client-table" id="acClientTable">
                    <colgroup>
                        <col class="ac-client-col-id">
                        <col class="ac-client-col-code">
                        <col class="ac-client-col-name">
                        <col class="ac-client-col-email">
                        <col class="ac-client-col-action">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="ac-client-th">ID</th>
                            <th class="ac-client-th">Client Code</th>
                            <th class="ac-client-th">Client Name</th>
                            <th class="ac-client-th">Client Email</th>
                            <th class="ac-client-th ac-client-th-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td class="ac-client-td">{{ $client->id }}</td>
                                <td class="ac-client-td"><span class="ac-client-code-badge">{{ $client->client_code }}</span></td>
                                <td class="ac-client-td">{{ $client->client_name ?: '—' }}</td>
                                <td class="ac-client-td">{{ $client->client_email ?: '—' }}</td>
                                <td class="ac-client-td ac-client-td-action">
                                    <div class="ac-client-action-btns">
                                        <a href="{{ route('accounts.clients.edit', $client) }}" class="ac-client-action-icon ac-client-action-edit" title="Edit" aria-label="Edit">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <form action="{{ route('accounts.clients.destroy', $client) }}" method="POST" class="ac-client-delete-form" data-delete-form autocomplete="off">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="ac-client-action-icon ac-client-action-delete" data-delete-trigger title="Delete" aria-label="Delete">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="ac-client-td ac-client-td-empty">No client accounts yet. <a href="{{ route('accounts.clients.create') }}">Add one</a>.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($clients->hasPages())
                <div class="ac-client-pagination">
                    {{ $clients->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="modal-backdrop" id="deleteClientModal" role="dialog" aria-labelledby="deleteClientModalTitle" aria-modal="true">
        <div class="modal-box">
            <div class="modal-header">
                <svg class="modal-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <h2 class="modal-title" id="deleteClientModalTitle">Delete Client Account</h2>
            </div>
            <div class="modal-body">
                <div class="delete-modal-confirm" id="deleteModalConfirm">
                    <p>Are you sure you want to delete this client account? This action cannot be undone.</p>
                </div>
                <div class="delete-modal-countdown" id="deleteModalCountdown" hidden>
                    <p class="delete-countdown-text">Deleting in</p>
                    <div class="delete-countdown-number" id="deleteCountdownNumber">3</div>
                    <p class="delete-countdown-cancel-hint">Click Cancel to abort</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" id="deleteClientModalCancel">Cancel</button>
                <button type="button" class="btn btn-confirm btn-danger" id="deleteClientModalConfirm"><span class="btn-text">Delete</span></button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .ac-client-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-accounts-clients-index .content { padding-bottom: 0; }
        .ac-client-header { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.75rem; }
        .ac-client-header-text { min-width: 0; }
        .ac-client-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .ac-client-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .btn-ac-client-add { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; font-size: 0.9375rem; font-weight: 600; color: #fff; background: linear-gradient(135deg, #2C528B 0%, #2B6CB0 100%); border-radius: 10px; text-decoration: none; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 2px 8px rgba(44,82,139,0.4); }
        .btn-ac-client-add:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(44,82,139,0.5); }
        .ac-client-alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; }
        .ac-client-alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.4); color: #86efac; }
        .ac-client-page-enter { animation: ac-client-page-in 0.4s ease-out; }
        @keyframes ac-client-page-in { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .ac-client-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.2); max-width: 100%; }
        .ac-client-table-wrap { overflow-x: auto; }
        .ac-client-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .ac-client-col-id { width: 70px; }
        .ac-client-col-code { width: 140px; }
        .ac-client-col-name { width: min(200px, 25%); }
        .ac-client-col-email { width: auto; }
        .ac-client-col-action { width: 110px; }
        .ac-client-th { text-align: left; padding: 0.875rem 1rem; font-weight: 600; color: #94a3b8; background: #1e293b; border-bottom: 1px solid #334155; }
        .ac-client-th-action { text-align: center; }
        .ac-client-td { padding: 0.875rem 1rem; border-bottom: 1px solid #334155; color: #e2e8f0; vertical-align: middle; }
        .ac-client-td-action { text-align: center; }
        .ac-client-td-empty { text-align: center; color: #94a3b8; padding: 2.5rem; }
        .ac-client-td-empty a { color: #60a5fa; text-decoration: none; font-weight: 500; }
        .ac-client-td-empty a:hover { text-decoration: underline; }
        .ac-client-code-badge { display: inline-block; padding: 0.25rem 0.5rem; background: rgba(44,82,139,0.3); border-radius: 6px; font-weight: 600; color: #93c5fd; font-size: 0.8125rem; }
        .ac-client-action-btns { display: flex; align-items: center; gap: 0.35rem; justify-content: center; flex-wrap: wrap; }
        .ac-client-delete-form { display: inline; margin: 0; padding: 0; }
        .ac-client-action-icon { display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; padding: 0; border: none; border-radius: 8px; background: transparent; color: #94a3b8; cursor: pointer; transition: background 0.15s, color 0.15s; text-decoration: none; }
        .ac-client-action-icon:hover { color: #e2e8f0; background: rgba(255,255,255,0.08); }
        .ac-client-action-icon svg { display: block; pointer-events: none; }
        .ac-client-action-edit:hover { color: #93c5fd; background: rgba(44,82,139,0.25); }
        .ac-client-action-delete:hover { color: #fca5a5; background: rgba(248,113,113,0.15); }
        .ac-client-pagination { padding: 1rem; border-top: 1px solid #334155; }
        .ac-client-table tbody tr:hover { background: rgba(255,255,255,0.03); }
        html[data-theme="light"] .ac-client-table-card { background: #fff; border-color: #e2e8f0; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        html[data-theme="light"] .ac-client-title { color: #1e293b; }
        html[data-theme="light"] .ac-client-subtitle { color: #64748b; }
        html[data-theme="light"] .ac-client-th { background: #f8fafc; color: #64748b; border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .ac-client-td { border-bottom-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .ac-client-table tbody tr:hover { background: #f8fafc; }
        html[data-theme="light"] .ac-client-action-icon { color: #64748b; }
        html[data-theme="light"] .ac-client-action-icon:hover { color: #334155; background: #e2e8f0; }
        html[data-theme="light"] .ac-client-code-badge { background: rgba(59, 130, 246, 0.15); color: #2563eb; }
        .delete-modal-confirm p { margin: 0; }
        .delete-modal-countdown { text-align: center; padding: 0.5rem 0; }
        .delete-countdown-text { font-size: 0.9375rem; color: #94a3b8; margin: 0 0 1rem 0; }
        .delete-countdown-number { font-size: 4rem; font-weight: 800; color: #f87171; line-height: 1; letter-spacing: -0.05em; min-height: 4rem; display: flex; align-items: center; justify-content: center; animation: delete-countdown-pop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .delete-countdown-cancel-hint { font-size: 0.8125rem; color: #64748b; margin: 1rem 0 0 0; }
        @keyframes delete-countdown-pop { 0% { opacity: 0; transform: scale(0.3); } 70% { transform: scale(1.1); } 100% { opacity: 1; transform: scale(1); } }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            var modal = document.getElementById('deleteClientModal');
            var cancelBtn = document.getElementById('deleteClientModalCancel');
            var confirmBtn = document.getElementById('deleteClientModalConfirm');
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
