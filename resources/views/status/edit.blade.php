@extends('layouts.dashboard')

@section('title', 'Edit Status')

@section('body_class', 'page-status-edit')

@section('content')
    <div class="status-form-page">
        <div class="status-form-header">
            <h1 class="status-form-title">Edit Status</h1>
            <p class="status-form-subtitle">Update status record #{{ $status->id }}.</p>
        </div>

        @if($errors->any())
            <div class="status-alert status-alert-error" role="alert">
                <ul class="status-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('status.update', $status) }}" method="POST" class="status-form" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="status-card">
                <div class="status-form-group">
                    <label class="status-label" for="name">Name</label>
                    <input type="text" id="name" name="name" class="status-input" placeholder="e.g. Pending, Completed" value="{{ old('name', $status->name) }}" autocomplete="off">
                </div>
                <div class="status-form-group status-form-group-color">
                    <label class="status-label" for="color">Color (hex)</label>
                    <div class="status-color-input-wrap">
                        @php
                            $colorVal = old('color', $status->color ?? '#22c55e');
                            $colorVal = $colorVal ? (str_starts_with($colorVal, '#') ? $colorVal : '#' . $colorVal) : '#22c55e';
                        @endphp
                        <input type="color" id="colorPicker" class="status-color-picker" value="{{ strlen($colorVal) === 7 ? $colorVal : '#22c55e' }}" title="Pick color">
                        <input type="text" id="color" name="color" class="status-input status-input-hex" placeholder="#ff0000 or ff0000" value="{{ old('color', $status->color) }}" maxlength="7" autocomplete="off">
                    </div>
                    <span class="status-hint">Enter hex with or without # (e.g. #ff0000 or ff0000)</span>
                </div>
            </div>
            <div class="status-form-actions">
                <a href="{{ route('status.index') }}" class="btn-status-cancel">Cancel</a>
                <button type="submit" class="btn-status-save" id="statusSaveBtn">
                    <span class="btn-text">Update</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .status-form-page { display: block; padding-bottom: 0; }
        body.page-status-edit .content { padding-bottom: 0; }
        .status-form-header { margin-bottom: 1.75rem; }
        .status-form-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .status-form-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .status-alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; }
        .status-alert-error { background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fca5a5; }
        .status-error-list { margin: 0; padding-left: 1.25rem; }
        .status-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        .status-form-group { margin-bottom: 1.25rem; }
        .status-form-group:last-of-type { margin-bottom: 0; }
        .status-form-group-color { margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid #334155; }
        .status-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; display: block; margin-bottom: 0.5rem; }
        .status-input { width: 100%; max-width: 400px; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; min-height: 2.75rem; }
        .status-input::placeholder { color: #64748b; }
        .status-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .status-color-input-wrap { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
        .status-color-picker { width: 48px; height: 2.75rem; padding: 2px; border: 1px solid #334155; border-radius: 10px; background: #1e293b; cursor: pointer; }
        .status-input-hex { max-width: 140px; font-family: ui-monospace, monospace; }
        .status-hint { font-size: 0.75rem; color: #64748b; margin-top: 0.35rem; display: block; }
        .status-form-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #334155; }
        .btn-status-save { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #2C528B; color: #fff; padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; box-shadow: 0 2px 6px rgba(44,82,139,0.35); transition: background 0.2s; min-width: 110px; }
        .btn-status-save:hover { background: #234a77; }
        .btn-status-save:disabled { cursor: not-allowed; opacity: 0.85; }
        .btn-status-save .btn-spinner { display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: status-spin 0.7s linear infinite; }
        .btn-status-save.is-saving .btn-text { display: none; }
        .btn-status-save.is-saving .btn-spinner { display: inline-block; }
        @keyframes status-spin { to { transform: rotate(360deg); } }
        .btn-status-cancel { padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; color: #94a3b8; text-decoration: none; transition: color 0.2s, background 0.2s; }
        .btn-status-cancel:hover { color: #e2e8f0; }
        html[data-theme="light"] .status-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .status-form-title { color: #1e293b; }
        html[data-theme="light"] .status-form-subtitle { color: #64748b; }
        html[data-theme="light"] .status-form-group-color { border-top-color: #e2e8f0; }
        html[data-theme="light"] .status-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .status-color-picker { background: #f8fafc; border-color: #e2e8f0; }
        html[data-theme="light"] .status-form-actions { border-top-color: #e2e8f0; }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            var form = document.querySelector('.status-form');
            var btn = document.getElementById('statusSaveBtn');
            var picker = document.getElementById('colorPicker');
            var hexInput = document.getElementById('color');

            function hexToFullHex(val) {
                val = (val || '').replace(/^#/, '');
                if (/^[A-Fa-f0-9]{6}$/.test(val)) return '#' + val;
                if (/^[A-Fa-f0-9]{3}$/.test(val)) return '#' + val[0]+val[0]+val[1]+val[1]+val[2]+val[2];
                return val ? '#' + val : '';
            }
            if (picker && hexInput) {
                picker.addEventListener('input', function() {
                    hexInput.value = this.value;
                });
                hexInput.addEventListener('input', function() {
                    var v = hexToFullHex(this.value);
                    if (v.length === 7) picker.value = v;
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
