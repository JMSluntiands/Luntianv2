@extends('layouts.dashboard')

@section('title', 'Add Job Request')

@section('body_class', 'page-job-request-create')

@section('content')
    <div class="jobrequest-form-page">
        <div class="jobrequest-form-header">
            <h1 class="jobrequest-form-title">Add Job Request</h1>
            <p class="jobrequest-form-subtitle">Create a new job request with client code, request ID, and type.</p>
        </div>

        @if($errors->any())
            <div class="jobrequest-alert jobrequest-alert-error" role="alert">
                <ul class="jobrequest-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('job_request.store') }}" method="POST" class="jobrequest-form" autocomplete="off">
            @csrf
            <div class="jobrequest-card">
                <div class="jobrequest-form-group">
                    <label class="jobrequest-label" for="client_code">Client Code</label>
                    <select id="client_code" name="client_code" class="jobrequest-select select2-single" required>
                        <option value="">— Select client —</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->client_code }}" {{ old('client_code') === $client->client_code ? 'selected' : '' }}>
                                {{ $client->client_code }} — {{ $client->client_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="jobrequest-form-group">
                    <label class="jobrequest-label" for="job_request_id">Job Request ID</label>
                    <input type="text" id="job_request_id" name="job_request_id" class="jobrequest-input" placeholder="e.g. EA_LBS_1SNatHERS" value="{{ old('job_request_id') }}" maxlength="50" required autocomplete="off">
                    <span class="jobrequest-hint">Unique identifier (max 50 characters)</span>
                </div>
                <div class="jobrequest-form-group">
                    <label class="jobrequest-label" for="job_request_type">Job Request Type</label>
                    <input type="text" id="job_request_type" name="job_request_type" class="jobrequest-input" placeholder="e.g. 1S NatHERS Base Model" value="{{ old('job_request_type') }}" maxlength="255" required autocomplete="off">
                </div>
            </div>
            <div class="jobrequest-form-actions">
                <a href="{{ route('job_request.index') }}" class="btn-jobrequest-cancel">Cancel</a>
                <button type="submit" class="btn-jobrequest-save" id="jobRequestSaveBtn">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function() {
            var form = document.querySelector('.jobrequest-form');
            var btn = document.getElementById('jobRequestSaveBtn');
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2-single').select2({ width: '100%', allowClear: false });
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
