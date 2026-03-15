@extends('layouts.dashboard')

@section('title', 'Account Settings')

@section('body_class', 'page-account-settings')

@section('content')
    <div class="staff-form-page">
        <div class="staff-form-header">
            <h1 class="staff-form-title">Account Settings</h1>
            <p class="staff-form-subtitle">Update your personal details and profile image.</p>
        </div>

        @if(session('success'))
            <div class="staff-alert staff-alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="staff-alert staff-alert-error" role="alert">
                <ul class="staff-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('account.settings.update') }}" method="POST" enctype="multipart/form-data" class="staff-form account-settings-form" autocomplete="off">
            @csrf
            <div class="staff-card">
                <div class="account-settings-grid">
                    <div class="account-settings-main">
                        <div class="staff-form-group">
                            <label class="staff-label" for="fullname">Full name</label>
                            <input type="text" id="fullname" name="fullname" class="staff-input" value="{{ old('fullname', $user->fullname) }}" required>
                        </div>

                        <div class="staff-form-group">
                            <label class="staff-label" for="email">Email</label>
                            <input type="email" id="email" name="email" class="staff-input" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="staff-form-group">
                            <label class="staff-label" for="username">Username</label>
                            <input type="text" id="username" name="username" class="staff-input" value="{{ old('username', $user->username) }}" required>
                        </div>

                        <div class="staff-form-group">
                            <label class="staff-label" for="password">New password <span class="text-muted">(optional)</span></label>
                            <input type="password" id="password" name="password" class="staff-input" autocomplete="new-password" placeholder="Leave blank to keep current password">
                        </div>
                    </div>

                    <div class="account-settings-sidebar">
                        <div class="profile-image-section">
                            <h2 class="section-title">Profile Image</h2>

                            <div class="profile-image-preview">
                                @if($user->profile_image)
                                    <img src="{{ route('account.settings.image') }}" alt="Profile image">
                                @else
                                    <span class="profile-image-placeholder">
                                        {{ strtoupper(mb_substr($user->fullname ?? 'U', 0, 1)) }}
                                    </span>
                                @endif
                            </div>

                            <div class="staff-form-group">
                                <label class="staff-label" for="profile_image">Upload new image</label>
                                <div class="profile-image-upload">
                                    <button type="button" class="profile-image-upload-btn" id="profileImageChooseBtn">
                                        Choose file
                                    </button>
                                    <span class="profile-image-upload-name" id="profileImageFileName">No file chosen</span>
                                    <input type="file" id="profile_image" name="profile_image" class="profile-image-input-hidden" accept="image/*">
                                </div>
                                <p class="staff-branch-note">Max 2MB. Recommended square image.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="staff-form-actions">
                <button type="submit" class="btn-staff-save" id="accountSaveBtn">
                    <span class="btn-text">Save changes</span>
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
            var form = document.querySelector('.account-settings-form');
            var btn = document.getElementById('accountSaveBtn');
            var fileInput = document.getElementById('profile_image');
            var chooseBtn = document.getElementById('profileImageChooseBtn');
            var fileNameEl = document.getElementById('profileImageFileName');

            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.disabled = true;
                    btn.classList.add('is-saving');
                });
            }

            if (chooseBtn && fileInput) {
                chooseBtn.addEventListener('click', function() {
                    fileInput.click();
                });
            }
            if (fileInput && fileNameEl) {
                fileInput.addEventListener('change', function() {
                    if (fileInput.files && fileInput.files.length > 0) {
                        fileNameEl.textContent = fileInput.files[0].name;
                    } else {
                        fileNameEl.textContent = 'No file chosen';
                    }
                });
            }
        })();
    </script>
@endpush

