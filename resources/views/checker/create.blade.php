@extends('layouts.dashboard')

@section('title', 'Add Checker')

@section('body_class', 'page-checker-create')

@section('content')
    <div class="checker-form-page">
        <div class="checker-form-header">
            <h1 class="checker-form-title">Add Checker</h1>
            <p class="checker-form-subtitle">Create a new checker account.</p>
        </div>

        @if($errors->any())
            <div class="checker-alert checker-alert-error" role="alert">
                <ul class="checker-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checker.store') }}" method="POST" class="checker-form" autocomplete="off">
            @csrf
            <div class="checker-card">
                <div class="checker-form-grid">
                    <div class="checker-form-group">
                        <label class="checker-label" for="checker_id">Code</label>
                        <input type="text" id="checker_id" name="checker_id" class="checker-input" placeholder="e.g. JDR, PEP" value="{{ old('checker_id') }}" autocomplete="off">
                    </div>
                    <div class="checker-form-group">
                        <label class="checker-label" for="name">Name</label>
                        <input type="text" id="name" name="name" class="checker-input" placeholder="Full name" value="{{ old('name') }}" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="checker-form-actions">
                <a href="{{ route('checker.index') }}" class="btn-checker-cancel">Cancel</a>
                <button type="submit" class="btn-checker-save" id="checkerSaveBtn">
                    <span class="btn-text">Save</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .checker-form-page { display: block; padding-bottom: 0; }
        body.page-checker-create .content { padding-bottom: 0; }
        .checker-form-header { margin-bottom: 1.75rem; }
        .checker-form-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .checker-form-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .checker-alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; }
        .checker-alert-error { background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fca5a5; }
        .checker-error-list { margin: 0; padding-left: 1.25rem; }
        .checker-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        .checker-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem 1.5rem; }
        .checker-form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .checker-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; }
        .checker-input { width: 100%; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; min-height: 2.75rem; }
        .checker-input::placeholder { color: #64748b; }
        .checker-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .checker-form-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #334155; }
        .btn-checker-save { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #2C528B; color: #fff; padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; box-shadow: 0 2px 6px rgba(44,82,139,0.35); transition: background 0.2s; min-width: 100px; }
        .btn-checker-save:hover { background: #234a77; }
        .btn-checker-save:disabled { cursor: not-allowed; opacity: 0.85; }
        .btn-checker-save .btn-spinner { display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: checker-spin 0.7s linear infinite; }
        .btn-checker-save.is-saving .btn-text { display: none; }
        .btn-checker-save.is-saving .btn-spinner { display: inline-block; }
        @keyframes checker-spin { to { transform: rotate(360deg); } }
        .btn-checker-cancel { padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; color: #94a3b8; text-decoration: none; transition: color 0.2s, background 0.2s; }
        .btn-checker-cancel:hover { color: #e2e8f0; }
        html[data-theme="light"] .checker-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .checker-form-title { color: #1e293b; }
        html[data-theme="light"] .checker-form-subtitle { color: #64748b; }
        html[data-theme="light"] .checker-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .checker-form-actions { border-top-color: #e2e8f0; }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            var form = document.querySelector('.checker-form');
            var btn = document.getElementById('checkerSaveBtn');
            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.disabled = true;
                    btn.classList.add('is-saving');
                });
            }
        })();
    </script>
@endpush

