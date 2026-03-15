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

