@extends('layouts.dashboard')

@section('title', 'Edit Client')

@section('body_class', 'page-client-edit')

@section('content')
    <div class="client-form-page">
        <div class="client-form-header">
            <h1 class="client-form-title">Edit Client</h1>
            <p class="client-form-subtitle">Update client account #{{ $client->client_account_id }}.</p>
        </div>

        @if($errors->any())
            <div class="client-alert client-alert-error" role="alert">
                <ul class="client-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('client.update', $client) }}" method="POST" class="client-form" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="client-card">
                <div class="client-form-group">
                    <label class="client-label" for="client_account_name">Name</label>
                    <input type="text" id="client_account_name" name="client_account_name" class="client-input" placeholder="e.g. Company Name, Client Name" value="{{ old('client_account_name', $client->client_account_name) }}" autocomplete="off">
                </div>
            </div>
            <div class="client-form-actions">
                <a href="{{ route('client.index') }}" class="btn-client-cancel">Cancel</a>
                <button type="submit" class="btn-client-save" id="clientSaveBtn">
                    <span class="btn-text">Update</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .client-form-page { display: block; padding-bottom: 0; }
        body.page-client-edit .content { padding-bottom: 0; }
        .client-form-header { margin-bottom: 1.75rem; }
        .client-form-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .client-form-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .client-alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; }
        .client-alert-error { background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fca5a5; }
        .client-error-list { margin: 0; padding-left: 1.25rem; }
        .client-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        .client-form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .client-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; }
        .client-input { width: 100%; max-width: 400px; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; min-height: 2.75rem; }
        .client-input::placeholder { color: #64748b; }
        .client-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .client-form-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #334155; }
        .btn-client-save { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #2C528B; color: #fff; padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; box-shadow: 0 2px 6px rgba(44,82,139,0.35); transition: background 0.2s; min-width: 110px; }
        .btn-client-save:hover { background: #234a77; }
        .btn-client-save:disabled { cursor: not-allowed; opacity: 0.85; }
        .btn-client-save .btn-spinner { display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: client-spin 0.7s linear infinite; }
        .btn-client-save.is-saving .btn-text { display: none; }
        .btn-client-save.is-saving .btn-spinner { display: inline-block; }
        @keyframes client-spin { to { transform: rotate(360deg); } }
        .btn-client-cancel { padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; color: #94a3b8; text-decoration: none; transition: color 0.2s, background 0.2s; }
        .btn-client-cancel:hover { color: #e2e8f0; }
        html[data-theme="light"] .client-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .client-form-title { color: #1e293b; }
        html[data-theme="light"] .client-form-subtitle { color: #64748b; }
        html[data-theme="light"] .client-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .client-form-actions { border-top-color: #e2e8f0; }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            var form = document.querySelector('.client-form');
            var btn = document.getElementById('clientSaveBtn');
            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.disabled = true;
                    btn.classList.add('is-saving');
                });
            }
        })();
    </script>
@endpush
