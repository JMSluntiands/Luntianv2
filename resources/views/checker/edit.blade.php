@extends('layouts.dashboard')

@section('title', 'Edit Checker')

@section('body_class', 'page-checker-edit')

@section('content')
    <div class="checker-form-page">
        <div class="checker-form-header">
            <h1 class="checker-form-title">Edit Checker</h1>
            <p class="checker-form-subtitle">Update checker account #{{ $checker->id }}.</p>
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

        <form action="{{ route('checker.update', $checker) }}" method="POST" class="checker-form" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="checker-card">
                <div class="checker-form-grid">
                    <div class="checker-form-group">
                        <label class="checker-label" for="checker_id">Code</label>
                        <input type="text" id="checker_id" name="checker_id" class="checker-input" placeholder="e.g. JDR, PEP" value="{{ old('checker_id', $checker->checker_id) }}" autocomplete="off">
                    </div>
                    <div class="checker-form-group">
                        <label class="checker-label" for="name">Name</label>
                        <input type="text" id="name" name="name" class="checker-input" placeholder="Full name" value="{{ old('name', $checker->name) }}" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="checker-form-actions">
                <a href="{{ route('checker.index') }}" class="btn-checker-cancel">Cancel</a>
                <button type="submit" class="btn-checker-save" id="checkerSaveBtn">
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

