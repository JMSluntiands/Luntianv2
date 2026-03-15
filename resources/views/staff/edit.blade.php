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

