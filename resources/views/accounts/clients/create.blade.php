@extends('layouts.dashboard')

@section('title', 'Add Client Account')

@section('body_class', 'page-accounts-clients-create')

@section('content')
    <div class="ac-client-form-page">
        <div class="ac-client-form-header">
            <h1 class="ac-client-form-title">Add Client Account</h1>
            <p class="ac-client-form-subtitle">Create a new client account linked to a user code (unique_code from User Accounts).</p>
        </div>

        @if($errors->any())
            <div class="ac-client-alert ac-client-alert-error" role="alert">
                <ul class="ac-client-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('accounts.clients.store') }}" method="POST" class="ac-client-form" autocomplete="off">
            @csrf
            <div class="ac-client-card">
                <div class="ac-client-form-grid">
                    <div class="ac-client-form-group ac-client-form-group-full">
                        <label class="ac-client-label" for="client_code">Client Code <span class="ac-client-required">*</span></label>
                        <select id="client_code" name="client_code" class="ac-client-input ac-client-select select2-single" required>
                            <option value="">Select user (unique code)</option>
                            @foreach($users as $u)
                                <option value="{{ $u->unique_code }}" {{ old('client_code') === $u->unique_code ? 'selected' : '' }}
                                    data-fullname="{{ e($u->fullname ?? '') }}"
                                    data-email="{{ e($u->email ?? '') }}">
                                    {{ $u->unique_code }} — {{ $u->fullname ?? 'N/A' }} ({{ $u->email ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="ac-client-hint">Shows users with role Branch or User only (Admin, Staff, Checker are excluded). Code = unique_code from User Accounts.</p>
                    </div>
                    <div class="ac-client-form-group">
                        <label class="ac-client-label" for="client_name">Client Name <span class="ac-client-required">*</span></label>
                        <input type="text" id="client_name" name="client_name" class="ac-client-input" placeholder="Client display name" value="{{ old('client_name') }}" required>
                    </div>
                    <div class="ac-client-form-group">
                        <label class="ac-client-label" for="client_email">Client Email <span class="ac-client-required">*</span></label>
                        <input type="email" id="client_email" name="client_email" class="ac-client-input" placeholder="client@example.com" value="{{ old('client_email') }}" required>
                    </div>
                </div>
            </div>
            <div class="ac-client-form-actions">
                <a href="{{ route('accounts.clients.index') }}" class="btn-ac-client-cancel">Cancel</a>
                <button type="submit" class="btn-ac-client-save" id="acClientSaveBtn">
                    <span class="btn-text">Save</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .ac-client-form-page { display: block; padding-bottom: 0; max-width: 720px; }
        body.page-accounts-clients-create .content { padding-bottom: 0; }
        .ac-client-form-header { margin-bottom: 1.75rem; }
        .ac-client-form-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .ac-client-form-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .ac-client-alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; }
        .ac-client-alert-error { background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fca5a5; }
        .ac-client-error-list { margin: 0; padding-left: 1.25rem; }
        .ac-client-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
        .ac-client-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.25rem 1.5rem; }
        .ac-client-form-group-full { grid-column: 1 / -1; }
        @media (max-width: 640px) { .ac-client-form-grid { grid-template-columns: 1fr; } }
        .ac-client-form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .ac-client-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; }
        .ac-client-required { color: #f87171; }
        .ac-client-input { width: 100%; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; min-height: 2.75rem; }
        .ac-client-input::placeholder { color: #64748b; }
        .ac-client-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .ac-client-select { cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.75rem center; padding-right: 2.25rem; }
        .ac-client-hint { font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0; }
        .ac-client-form-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #334155; }
        .btn-ac-client-save { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: linear-gradient(135deg, #2C528B 0%, #2B6CB0 100%); color: #fff; padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; box-shadow: 0 2px 8px rgba(44,82,139,0.4); transition: transform 0.2s; min-width: 100px; }
        .btn-ac-client-save:hover { transform: translateY(-1px); }
        .btn-ac-client-save:disabled { cursor: not-allowed; opacity: 0.85; transform: none; }
        .btn-ac-client-save .btn-spinner { display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: ac-client-spin 0.7s linear infinite; }
        .btn-ac-client-save.is-saving .btn-text { display: none; }
        .btn-ac-client-save.is-saving .btn-spinner { display: inline-block; }
        @keyframes ac-client-spin { to { transform: rotate(360deg); } }
        .btn-ac-client-cancel { padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; color: #94a3b8; text-decoration: none; transition: color 0.2s, background 0.2s; }
        .btn-ac-client-cancel:hover { color: #e2e8f0; }
        html[data-theme="light"] .ac-client-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .ac-client-form-title { color: #1e293b; }
        html[data-theme="light"] .ac-client-form-subtitle { color: #64748b; }
        html[data-theme="light"] .ac-client-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .ac-client-form-actions { border-top-color: #e2e8f0; }
        html[data-theme="light"] .ac-client-hint { color: #64748b; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function() {
            var form = document.querySelector('.ac-client-form');
            var btn = document.getElementById('acClientSaveBtn');
            var codeSelect = document.getElementById('client_code');
            var nameInput = document.getElementById('client_name');
            var emailInput = document.getElementById('client_email');

            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#client_code').select2({ width: '100%', allowClear: false });
            }

            if (codeSelect && nameInput && emailInput) {
                codeSelect.addEventListener('change', function() {
                    var opt = this.options[this.selectedIndex];
                    if (opt && opt.value && opt.dataset.fullname !== undefined) {
                        if (!nameInput.value || nameInput.value === nameInput.placeholder) nameInput.value = opt.dataset.fullname || '';
                        if (!emailInput.value || emailInput.value === emailInput.placeholder) emailInput.value = opt.dataset.email || '';
                    }
                });
            }

            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.disabled = true;
                    btn.classList.add('is-saving');
                });
            }
        })();
    </script>
@endpush
