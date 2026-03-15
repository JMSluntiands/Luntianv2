@extends('layouts.dashboard')

@section('title', 'Edit Client')

@section('body_class', 'page-client-edit')

@section('content')
    <div class="client-form-page">
        <div class="client-form-header">
            <h1 class="client-form-title">Edit Client</h1>
            <p class="client-form-subtitle">Update client account #{{ $client->client_account_id }}.</p>
        </div>

        @if($errors->any())
            <div class="client-alert client-alert-error" role="alert">
                <ul class="client-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('client.update', $client) }}" method="POST" class="client-form" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="client-card">
                <div class="client-form-group">
                    <label class="client-label" for="client_account_name">Name</label>
                    <input type="text" id="client_account_name" name="client_account_name" class="client-input" placeholder="e.g. Company Name, Client Name" value="{{ old('client_account_name', $client->client_account_name) }}" autocomplete="off">
                </div>
            </div>
            <div class="client-form-actions">
                <a href="{{ route('client.index') }}" class="btn-client-cancel">Cancel</a>
                <button type="submit" class="btn-client-save" id="clientSaveBtn">
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
            var form = document.querySelector('.client-form');
            var btn = document.getElementById('clientSaveBtn');
            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.disabled = true;
                    btn.classList.add('is-saving');
                });
            }
        })();
    </script>
@endpush
