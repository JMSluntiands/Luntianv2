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
    <style>
        .staff-form-page { display: block; padding-bottom: 0; }
        body.page-account-settings .content { padding-bottom: 0; }
        .staff-form-header { margin-bottom: 1.75rem; }
        .staff-form-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .staff-form-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .staff-alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; }
        .staff-alert-error { background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fca5a5; }
        .staff-alert-success { background: rgba(34, 197, 94, 0.12); border: 1px solid rgba(34, 197, 94, 0.45); color: #bbf7d0; }
        .staff-error-list { margin: 0; padding-left: 1.25rem; }
        .staff-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        .staff-form-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem 1.5rem; }
        .staff-form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .staff-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; }
        .staff-input { width: 100%; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; min-height: 2.75rem; }
        .staff-input::placeholder { color: #64748b; }
        .staff-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .staff-form-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #334155; }
        .btn-staff-save { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #2C528B; color: #fff; padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; box-shadow: 0 2px 6px rgba(44,82,139,0.35); transition: background 0.2s; min-width: 120px; }
        .btn-staff-save:hover { background: #234a77; }
        .btn-staff-save:disabled { cursor: not-allowed; opacity: 0.85; }
        .btn-staff-save .btn-spinner { display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: staff-spin 0.7s linear infinite; }
        .btn-staff-save.is-saving .btn-text { display: none; }
        .btn-staff-save.is-saving .btn-spinner { display: inline-block; }
        @keyframes staff-spin { to { transform: rotate(360deg); } }
        .account-settings-grid {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(260px, 1fr);
            gap: 1.5rem;
        }
        .account-settings-sidebar {
            border-left: 1px dashed #334155;
            padding-left: 1.5rem;
        }
        .profile-image-section .section-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #e2e8f0;
            margin: 0 0 0.75rem 0;
        }
        .profile-image-preview {
            width: 112px;
            height: 112px;
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 0.75rem;
            border: 2px solid rgba(148, 163, 184, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 30% 20%, #1f2937, #020617);
        }
        .profile-image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .profile-image-placeholder {
            font-size: 2.75rem;
            font-weight: 700;
            color: #e5e7eb;
        }
        .profile-image-upload {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.25rem;
        }
        .profile-image-upload-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1.1rem;
            border-radius: 999px;
            border: 1px solid #4b5563;
            background: linear-gradient(135deg, #1f2937, #111827);
            color: #e5e7eb;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s, transform 0.05s;
        }
        .profile-image-upload-btn:hover {
            background: linear-gradient(135deg, #111827, #020617);
            border-color: #6b7280;
        }
        .profile-image-upload-btn:active {
            transform: translateY(1px);
        }
        .profile-image-input-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }
        .profile-image-upload-name {
            font-size: 0.82rem;
            color: #94a3b8;
            max-width: 220px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        @media (max-width: 1024px) {
            .staff-form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 900px) {
            .account-settings-grid {
                grid-template-columns: 1fr;
            }
            .account-settings-sidebar {
                border-left: none;
                border-top: 1px dashed #334155;
                padding-left: 0;
                padding-top: 1.5rem;
                margin-top: 1rem;
            }
        }
        html[data-theme="light"] .staff-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .staff-form-title { color: #1e293b; }
        html[data-theme="light"] .staff-form-subtitle { color: #64748b; }
        html[data-theme="light"] .staff-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .staff-form-actions { border-top-color: #e2e8f0; }
        html[data-theme="light"] .staff-alert-success { background: rgba(220,252,231,0.95); border-color: rgba(74,222,128,0.8); color: #166534; }
        html[data-theme="light"] .profile-image-section .section-title { color: #1f2937; }
        html[data-theme="light"] .account-settings-sidebar { border-color: #e2e8f0; }
        html[data-theme="light"] .profile-image-preview { border-color: rgba(148,163,184,0.9); background: #f1f5f9; }
        html[data-theme="light"] .profile-image-placeholder { color: #1f2937; }
        html[data-theme="light"] .profile-image-upload-btn {
            background: #fff;
            border-color: #d1d5db;
            color: #111827;
        }
        html[data-theme="light"] .profile-image-upload-btn:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }
        html[data-theme="light"] .profile-image-upload-name { color: #6b7280; }
    </style>
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

