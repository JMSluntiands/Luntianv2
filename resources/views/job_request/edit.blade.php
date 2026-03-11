@extends('layouts.dashboard')

@section('title', 'Edit Job Request')

@section('body_class', 'page-job-request-edit')

@section('content')
    <div class="jobrequest-form-page">
        <div class="jobrequest-form-header">
            <h1 class="jobrequest-form-title">Edit Job Request</h1>
            <p class="jobrequest-form-subtitle">Update job request #{{ $jobRequest->id }}.</p>
        </div>

        @if($errors->any())
            <div class="jobrequest-alert jobrequest-alert-error" role="alert">
                <ul class="jobrequest-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('job_request.update', $jobRequest) }}" method="POST" class="jobrequest-form" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="jobrequest-card">
                <div class="jobrequest-form-group">
                    <label class="jobrequest-label" for="client_code">Client Code</label>
                    <select id="client_code" name="client_code" class="jobrequest-select" required>
                        <option value="">— Select client —</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->client_code }}" {{ old('client_code', $jobRequest->client_code) === $client->client_code ? 'selected' : '' }}>
                                {{ $client->client_code }} — {{ $client->client_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="jobrequest-form-group">
                    <label class="jobrequest-label" for="job_request_id">Job Request ID</label>
                    <input type="text" id="job_request_id" name="job_request_id" class="jobrequest-input" placeholder="e.g. EA_LBS_1SNatHERS" value="{{ old('job_request_id', $jobRequest->job_request_id) }}" maxlength="50" required autocomplete="off">
                    <span class="jobrequest-hint">Unique identifier (max 50 characters)</span>
                </div>
                <div class="jobrequest-form-group">
                    <label class="jobrequest-label" for="job_request_type">Job Request Type</label>
                    <input type="text" id="job_request_type" name="job_request_type" class="jobrequest-input" placeholder="e.g. 1S NatHERS Base Model" value="{{ old('job_request_type', $jobRequest->job_request_type) }}" maxlength="255" required autocomplete="off">
                </div>
            </div>
            <div class="jobrequest-form-actions">
                <a href="{{ route('job_request.index') }}" class="btn-jobrequest-cancel">Cancel</a>
                <button type="submit" class="btn-jobrequest-save" id="jobRequestSaveBtn">
                    <span class="btn-text">Update</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .jobrequest-form-page { display: block; padding-bottom: 0; }
        body.page-job-request-edit .content { padding-bottom: 0; }
        .jobrequest-form-header { margin-bottom: 1.75rem; }
        .jobrequest-form-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .jobrequest-form-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .jobrequest-alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; }
        .jobrequest-alert-error { background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fca5a5; }
        .jobrequest-error-list { margin: 0; padding-left: 1.25rem; }
        .jobrequest-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        .jobrequest-form-group { margin-bottom: 1.25rem; }
        .jobrequest-form-group:last-of-type { margin-bottom: 0; }
        .jobrequest-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; display: block; margin-bottom: 0.5rem; }
        .jobrequest-input, .jobrequest-select { width: 100%; max-width: 400px; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; min-height: 2.75rem; }
        .jobrequest-input::placeholder { color: #64748b; }
        .jobrequest-input:focus, .jobrequest-select:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .jobrequest-select option { background: #1e293b; color: #e2e8f0; }
        .jobrequest-hint { font-size: 0.75rem; color: #64748b; margin-top: 0.35rem; display: block; }
        .jobrequest-form-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #334155; }
        .btn-jobrequest-save { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #2C528B; color: #fff; padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; box-shadow: 0 2px 6px rgba(44,82,139,0.35); transition: background 0.2s; min-width: 110px; }
        .btn-jobrequest-save:hover { background: #234a77; }
        .btn-jobrequest-save:disabled { cursor: not-allowed; opacity: 0.85; }
        .btn-jobrequest-save .btn-spinner { display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: jobrequest-spin 0.7s linear infinite; }
        .btn-jobrequest-save.is-saving .btn-text { display: none; }
        .btn-jobrequest-save.is-saving .btn-spinner { display: inline-block; }
        @keyframes jobrequest-spin { to { transform: rotate(360deg); } }
        .btn-jobrequest-cancel { padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; color: #94a3b8; text-decoration: none; transition: color 0.2s, background 0.2s; }
        .btn-jobrequest-cancel:hover { color: #e2e8f0; }
        html[data-theme="light"] .jobrequest-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .jobrequest-form-title { color: #1e293b; }
        html[data-theme="light"] .jobrequest-form-subtitle { color: #64748b; }
        html[data-theme="light"] .jobrequest-input, html[data-theme="light"] .jobrequest-select { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .jobrequest-select option { background: #fff; color: #1e293b; }
        html[data-theme="light"] .jobrequest-form-actions { border-top-color: #e2e8f0; }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            var form = document.querySelector('.jobrequest-form');
            var btn = document.getElementById('jobRequestSaveBtn');
            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.disabled = true;
                    btn.classList.add('is-saving');
                });
            }
        })();
    </script>
@endpush
