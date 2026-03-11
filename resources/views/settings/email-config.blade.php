@extends('layouts.dashboard')

@section('title', 'Email Configuration (SMTP2Go)')

@section('body_class', 'page-settings-email-config')

@section('content')
    <div class="email-config-page">
        <div class="email-config-header">
            <h1 class="email-config-title">Email Configuration</h1>
            <p class="email-config-subtitle">Configure SMTP (SMTP2Go) to send email and set the default sender address and name.</p>
        </div>

        @if($errors->any())
            <div class="email-config-alert email-config-alert-error" role="alert">
                <ul class="email-config-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="emailConfigForm" action="{{ route('settings.email_config.store') }}" method="POST" autocomplete="off">
            @csrf
            {{-- Hidden dummy fields so browser fills these instead of real SMTP fields --}}
            <div class="email-config-autofill-trap" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;pointer-events:none;">
                <input type="text" name="autofill_trap_username" tabindex="-1" autocomplete="off">
                <input type="password" name="autofill_trap_password" tabindex="-1" autocomplete="off">
            </div>
            <div class="email-config-card">
                <div class="email-config-card-header">
                    <h2 class="email-config-section-title">SMTP Setup (for sending email)</h2>
                </div>
                <div class="email-config-grid">
                    <div class="email-config-group">
                        <label class="email-config-label" for="smtp_host">SMTP Host <span class="email-config-required">*</span></label>
                        <input type="text" id="smtp_host" name="smtp_host" class="email-config-input" placeholder="mail.smtp2go.com" value="{{ old('smtp_host', $config?->smtp_host ?? 'mail.smtp2go.com') }}" required autocomplete="off">
                    </div>
                    <div class="email-config-group">
                        <label class="email-config-label" for="smtp_port">SMTP Port <span class="email-config-required">*</span></label>
                        <input type="number" id="smtp_port" name="smtp_port" class="email-config-input" placeholder="2525" min="1" max="65535" value="{{ old('smtp_port', $config?->smtp_port ?? 2525) }}" required autocomplete="off">
                        <span class="email-config-hint">SMTP2Go: 2525, 587, or 465 (SSL)</span>
                    </div>
                    <div class="email-config-group">
                        <label class="email-config-label" for="encryption">Encryption</label>
                        <select id="encryption" name="encryption" class="email-config-select select2-single">
                            <option value="">None</option>
                            <option value="tls" {{ old('encryption', $config?->encryption ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('encryption', $config?->encryption ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                    <div class="email-config-group">
                        <label class="email-config-label" for="smtp_username">SMTP Username</label>
                        <input type="text" id="smtp_username" name="smtp_username" class="email-config-input" placeholder="SMTP2Go username" value="{{ old('smtp_username', $config?->smtp_username ?? '') }}" autocomplete="off" readonly data-no-autofill>
                    </div>
                    <div class="email-config-group">
                        <label class="email-config-label" for="smtp_password">SMTP Password</label>
                        <input type="password" id="smtp_password" name="smtp_password" class="email-config-input" placeholder="{{ $config ? 'Leave blank to keep current' : 'SMTP2Go password' }}" autocomplete="new-password" readonly data-no-autofill>
                    </div>
                </div>
            </div>

            <div class="email-config-card">
                <div class="email-config-card-header">
                    <h2 class="email-config-section-title">Default sender (where email comes from)</h2>
                </div>
                <div class="email-config-grid">
                    <div class="email-config-group">
                        <label class="email-config-label" for="from_email">From Email</label>
                        <input type="email" id="from_email" name="from_email" class="email-config-input" placeholder="noreply@yourdomain.com" value="{{ old('from_email', $config?->from_email ?? '') }}" autocomplete="off" readonly data-no-autofill>
                        <span class="email-config-hint">Default email address shown in "From"</span>
                    </div>
                    <div class="email-config-group">
                        <label class="email-config-label" for="from_name">From Name</label>
                        <input type="text" id="from_name" name="from_name" class="email-config-input" placeholder="Luntian" value="{{ old('from_name', $config?->from_name ?? '') }}" autocomplete="off">
                        <span class="email-config-hint">Name shown in "From"</span>
                    </div>
                </div>
            </div>

            <div class="email-config-actions">
                <button type="submit" class="btn btn-email-config-save">
                    {{ $config ? 'Update' : 'Save' }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .email-config-page { display: block; padding-bottom: 0; margin-bottom: 0; }
        body.page-settings-email-config .content { padding-bottom: 0; }
        .email-config-header { margin-bottom: 1.75rem; }
        .email-config-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .email-config-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .email-config-alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; }
        .email-config-alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.4); color: #86efac; }
        .email-config-alert-error { background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fca5a5; }
        .email-config-error-list { margin: 0; padding-left: 1.25rem; }
        .email-config-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        .email-config-card-header { margin-bottom: 1.25rem; padding-bottom: 0.625rem; border-bottom: 1px solid #334155; }
        .email-config-section-title { font-size: 0.9375rem; font-weight: 600; color: #e2e8f0; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
        .email-config-section-title::before { content: ''; width: 4px; height: 1em; background: #2C528B; border-radius: 2px; }
        .email-config-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem 1.5rem; align-items: start; }
        .email-config-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .email-config-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; }
        .email-config-required { color: #f87171; }
        .email-config-hint { font-size: 0.75rem; color: #64748b; margin-top: 0.15rem; }
        .email-config-input, .email-config-select { width: 100%; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; min-height: 2.75rem; }
        .email-config-input::placeholder { color: #64748b; }
        .email-config-input:focus, .email-config-select:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .email-config-checkbox-label { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin: 0; font-size: 0.9375rem; color: #e2e8f0; }
        .email-config-checkbox { width: 1.25rem; height: 1.25rem; cursor: pointer; margin: 0; appearance: none; -webkit-appearance: none; border: 2px solid #64748b; border-radius: 6px; background: #1e293b; transition: border-color 0.2s, background 0.2s; }
        .email-config-checkbox:checked { background: #2C528B; border-color: #2C528B; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E"); background-size: 14px 14px; background-position: center; background-repeat: no-repeat; }
        .email-config-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #334155; }
        .btn-email-config-save { background: #2C528B; color: #fff; padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; box-shadow: 0 2px 6px rgba(44,82,139,0.35); transition: background 0.2s; }
        .btn-email-config-save:hover { background: #234a77; }
        @media (max-width: 768px) {
            .email-config-grid { grid-template-columns: 1fr; }
        }
        html[data-theme="light"] .email-config-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .email-config-title { color: #0f172a; }
        html[data-theme="light"] .email-config-subtitle { color: #64748b; }
        html[data-theme="light"] .email-config-card-header { border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .email-config-section-title { color: #334155; }
        html[data-theme="light"] .email-config-input, html[data-theme="light"] .email-config-select { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .email-config-checkbox-label { color: #1e293b; }
        html[data-theme="light"] .email-config-checkbox { background: #fff; border-color: #94a3b8; }
        html[data-theme="light"] .email-config-actions { border-top-color: #e2e8f0; }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            $('.select2-single').select2({ width: '100%', allowClear: false });
            document.querySelectorAll('#emailConfigForm [data-no-autofill]').forEach(function(input) {
                function removeReadOnly() {
                    input.removeAttribute('readonly');
                    input.removeEventListener('focus', removeReadOnly);
                    input.removeEventListener('click', removeReadOnly);
                }
                input.addEventListener('focus', removeReadOnly);
                input.addEventListener('click', removeReadOnly);
            });
        });
    </script>
@endpush
