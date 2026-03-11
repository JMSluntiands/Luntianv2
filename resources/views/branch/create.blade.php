@extends('layouts.dashboard')

@section('title', 'Add Branch')

@section('body_class', 'page-branch-create')

@section('content')
    <div class="priority-form-page">
        <div class="priority-form-header">
            <h1 class="priority-form-title">Add Branch</h1>
            <p class="priority-form-subtitle">Create a new branch.</p>
        </div>

        @if($errors->any())
            <div class="priority-alert priority-alert-error" role="alert">
                <ul class="priority-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('branch.store') }}" method="POST" class="priority-form" autocomplete="off">
            @csrf
            <div class="priority-card">
                <div class="priority-form-group">
                    <label class="priority-label" for="branch_name">Branch Name</label>
                    <input type="text" id="branch_name" name="branch_name" class="priority-input" placeholder="e.g. LBS, BPH" value="{{ old('branch_name') }}" autocomplete="off">
                </div>
            </div>
            <div class="priority-form-actions">
                <a href="{{ route('branch.index') }}" class="btn-priority-cancel">Cancel</a>
                <button type="submit" class="btn-priority-save" id="branchSaveBtn">
                    <span class="btn-text">Save</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .priority-form-page { display: block; padding-bottom: 0; }
        body.page-branch-create .content { padding-bottom: 0; }
        .priority-form-header { margin-bottom: 1.75rem; }
        .priority-form-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .priority-form-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .priority-alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; }
        .priority-alert-error { background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fca5a5; }
        .priority-error-list { margin: 0; padding-left: 1.25rem; }
        .priority-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        .priority-form-group { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.25rem; }
        .priority-form-group:last-of-type { margin-bottom: 0; }
        .priority-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; }
        .priority-input { width: 100%; max-width: 400px; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; min-height: 2.75rem; }
        .priority-input::placeholder { color: #64748b; }
        .priority-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .priority-form-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #334155; }
        .btn-priority-save { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #2C528B; color: #fff; padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; box-shadow: 0 2px 6px rgba(44,82,139,0.35); transition: background 0.2s; min-width: 100px; }
        .btn-priority-save:hover { background: #234a77; }
        .btn-priority-save:disabled { cursor: not-allowed; opacity: 0.85; }
        .btn-priority-save .btn-spinner { display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: priority-spin 0.7s linear infinite; }
        .btn-priority-save.is-saving .btn-text { display: none; }
        .btn-priority-save.is-saving .btn-spinner { display: inline-block; }
        @keyframes priority-spin { to { transform: rotate(360deg); } }
        .btn-priority-cancel { padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; color: #94a3b8; text-decoration: none; transition: color 0.2s, background 0.2s; }
        .btn-priority-cancel:hover { color: #e2e8f0; }
        html[data-theme="light"] .priority-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .priority-form-title { color: #1e293b; }
        html[data-theme="light"] .priority-form-subtitle { color: #64748b; }
        html[data-theme="light"] .priority-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .priority-form-actions { border-top-color: #e2e8f0; }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            var form = document.querySelector('.priority-form');
            var btn = document.getElementById('branchSaveBtn');
            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.disabled = true;
                    btn.classList.add('is-saving');
                });
            }
        })();
    </script>
@endpush

