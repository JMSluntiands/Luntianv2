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
