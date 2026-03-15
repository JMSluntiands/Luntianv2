@extends('layouts.dashboard')

@section('title', 'Add Priority')

@section('body_class', 'page-priority-create')

@section('content')
    <div class="priority-form-page">
        <div class="priority-form-header">
            <h1 class="priority-form-title">Add Priority</h1>
            <p class="priority-form-subtitle">Create a new priority with name and hex color.</p>
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

        <form action="{{ route('priority.store') }}" method="POST" class="priority-form" autocomplete="off">
            @csrf
            <div class="priority-card">
                <div class="priority-form-group">
                    <label class="priority-label" for="name">Name</label>
                    <input type="text" id="name" name="name" class="priority-input" placeholder="e.g. High, Low, Urgent" value="{{ old('name') }}" autocomplete="off">
                </div>
                <div class="priority-form-group priority-form-group-color">
                    <label class="priority-label" for="color">Color (hex)</label>
                    <div class="priority-color-input-wrap">
                        <input type="color" id="colorPicker" class="priority-color-picker" value="#22c55e" title="Pick color">
                        <input type="text" id="color" name="color" class="priority-input priority-input-hex" placeholder="#ff0000 or ff0000" value="{{ old('color', '#22c55e') }}" maxlength="7" autocomplete="off">
                    </div>
                    <span class="priority-hint">Enter hex with or without # (e.g. #ff0000 or ff0000)</span>
                </div>
            </div>
            <div class="priority-form-actions">
                <a href="{{ route('priority.index') }}" class="btn-priority-cancel">Cancel</a>
                <button type="submit" class="btn-priority-save" id="prioritySaveBtn">
                    <span class="btn-text">Save</span>
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
            var form = document.querySelector('.priority-form');
            var btn = document.getElementById('prioritySaveBtn');
            var picker = document.getElementById('colorPicker');
            var hexInput = document.getElementById('color');
            function hexToFullHex(val) {
                val = (val || '').replace(/^#/, '');
                if (/^[A-Fa-f0-9]{6}$/.test(val)) return '#' + val;
                if (/^[A-Fa-f0-9]{3}$/.test(val)) return '#' + val[0]+val[0]+val[1]+val[1]+val[2]+val[2];
                return val ? '#' + val : '';
            }
            if (picker && hexInput) {
                picker.addEventListener('input', function() { hexInput.value = this.value; });
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
