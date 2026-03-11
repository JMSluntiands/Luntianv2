@extends('layouts.dashboard')

@section('title', 'Add Compliance')

@section('body_class', 'page-compliance-create')

@section('content')
    <div class="compliance-form-page">
        <div class="compliance-form-header">
            <h1 class="compliance-form-title">Add Compliance</h1>
            <p class="compliance-form-subtitle">Create a new compliance record.</p>
        </div>

        @if($errors->any())
            <div class="compliance-alert compliance-alert-error" role="alert">
                <ul class="compliance-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('compliance.store') }}" method="POST" class="compliance-form" autocomplete="off">
            @csrf
            <div class="compliance-card">
                <div class="compliance-form-group">
                    <label class="compliance-label" for="column">Column</label>
                    <input type="text" id="column" name="column" class="compliance-input" placeholder="Enter value" value="{{ old('column') }}" autocomplete="off">
                </div>
            </div>
            <div class="compliance-form-actions">
                <a href="{{ route('compliance.index') }}" class="btn-compliance-cancel">Cancel</a>
                <button type="submit" class="btn-compliance-save" id="complianceSaveBtn">
                    <span class="btn-text">Save</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .compliance-form-page { display: block; padding-bottom: 0; }
        body.page-compliance-create .content { padding-bottom: 0; }
        .compliance-form-header { margin-bottom: 1.75rem; }
        .compliance-form-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .compliance-form-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .compliance-alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; }
        .compliance-alert-error { background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fca5a5; }
        .compliance-error-list { margin: 0; padding-left: 1.25rem; }
        .compliance-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        .compliance-form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .compliance-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; }
        .compliance-input { width: 100%; max-width: 400px; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; min-height: 2.75rem; }
        .compliance-input::placeholder { color: #64748b; }
        .compliance-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .compliance-form-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #334155; }
        .btn-compliance-save { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #2C528B; color: #fff; padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; box-shadow: 0 2px 6px rgba(44,82,139,0.35); transition: background 0.2s; min-width: 100px; }
        .btn-compliance-save:hover { background: #234a77; }
        .btn-compliance-save:disabled { cursor: not-allowed; opacity: 0.85; }
        .btn-compliance-save .btn-spinner { display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: compliance-spin 0.7s linear infinite; }
        .btn-compliance-save.is-saving .btn-text { display: none; }
        .btn-compliance-save.is-saving .btn-spinner { display: inline-block; }
        @keyframes compliance-spin { to { transform: rotate(360deg); } }
        .btn-compliance-cancel { padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; color: #94a3b8; text-decoration: none; transition: color 0.2s, background 0.2s; }
        .btn-compliance-cancel:hover { color: #e2e8f0; }
        html[data-theme="light"] .compliance-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .compliance-form-title { color: #1e293b; }
        html[data-theme="light"] .compliance-form-subtitle { color: #64748b; }
        html[data-theme="light"] .compliance-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .compliance-form-actions { border-top-color: #e2e8f0; }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            var form = document.querySelector('.compliance-form');
            var btn = document.getElementById('complianceSaveBtn');
            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.disabled = true;
                    btn.classList.add('is-saving');
                });
            }
        })();
    </script>
@endpush
