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
