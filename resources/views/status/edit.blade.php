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
