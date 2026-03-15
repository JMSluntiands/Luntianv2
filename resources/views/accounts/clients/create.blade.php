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
