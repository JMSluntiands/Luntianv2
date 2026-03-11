@extends('layouts.dashboard')

@section('title', 'Edit Staff')

@section('body_class', 'page-staff-edit')

@section('content')
    <div class="staff-form-page">
        <div class="staff-form-header">
            <h1 class="staff-form-title">Edit Staff</h1>
            <p class="staff-form-subtitle">Update staff account #{{ $staff->id }}.</p>
        </div>

        @if($errors->any())
            <div class="staff-alert staff-alert-error" role="alert">
                <ul class="staff-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('staff.update', $staff) }}" method="POST" class="staff-form" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="staff-card">
                <div class="staff-form-grid">
                    <div class="staff-form-group">
                        <label class="staff-label" for="staff_id">Code</label>
                        <input type="text" id="staff_id" name="staff_id" class="staff-input" placeholder="e.g. SB, JS" value="{{ old('staff_id', $staff->staff_id) }}" autocomplete="off">
                    </div>
                    <div class="staff-form-group">
                        <label class="staff-label" for="name">Name</label>
                        <input type="text" id="name" name="name" class="staff-input" placeholder="Full name" value="{{ old('name', $staff->name) }}" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="staff-form-actions">
                <a href="{{ route('staff.index') }}" class="btn-staff-cancel">Cancel</a>
                <button type="submit" class="btn-staff-save" id="staffSaveBtn">
                    <span class="btn-text">Update</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .staff-form-page { display: block; padding-bottom: 0; }
        body.page-staff-edit .content { padding-bottom: 0; }
        .staff-form-header { margin-bottom: 1.75rem; }
        .staff-form-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .staff-form-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .staff-alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; }
        .staff-alert-error { background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fca5a5; }
        .staff-error-list { margin: 0; padding-left: 1.25rem; }
        .staff-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        .staff-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem 1.5rem; }
        .staff-form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .staff-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; }
        .staff-input { width: 100%; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; min-height: 2.75rem; }
        .staff-input::placeholder { color: #64748b; }
        .staff-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .staff-form-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #334155; }
        .btn-staff-save { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #2C528B; color: #fff; padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; box-shadow: 0 2px 6px rgba(44,82,139,0.35); transition: background 0.2s; min-width: 110px; }
        .btn-staff-save:hover { background: #234a77; }
        .btn-staff-save:disabled { cursor: not-allowed; opacity: 0.85; }
        .btn-staff-save .btn-spinner { display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: staff-spin 0.7s linear infinite; }
        .btn-staff-save.is-saving .btn-text { display: none; }
        .btn-staff-save.is-saving .btn-spinner { display: inline-block; }
        @keyframes staff-spin { to { transform: rotate(360deg); } }
        .btn-staff-cancel { padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; color: #94a3b8; text-decoration: none; transition: color 0.2s, background 0.2s; }
        .btn-staff-cancel:hover { color: #e2e8f0; }
        html[data-theme="light"] .staff-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .staff-form-title { color: #1e293b; }
        html[data-theme="light"] .staff-form-subtitle { color: #64748b; }
        html[data-theme="light"] .staff-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .staff-form-actions { border-top-color: #e2e8f0; }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            var form = document.querySelector('.staff-form');
            var btn = document.getElementById('staffSaveBtn');
            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.disabled = true;
                    btn.classList.add('is-saving');
                });
            }
        })();
    </script>
@endpush

