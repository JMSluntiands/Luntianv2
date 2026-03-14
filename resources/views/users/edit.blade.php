@extends('layouts.dashboard')

@section('title', 'Edit User')

@section('body_class', 'page-users-edit')

@section('content')
    <div class="staff-form-page">
        <div class="staff-form-header">
            <h1 class="staff-form-title">Edit User</h1>
            <p class="staff-form-subtitle">Update user account #{{ $user->id }}.</p>
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

        <form action="{{ route('users.update', $user) }}" method="POST" class="staff-form" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="staff-card">
                <div class="staff-form-grid">
                    <div class="staff-form-group">
                        <label class="staff-label" for="unique_code">Code</label>
                        <input type="text" id="unique_code" name="unique_code" class="staff-input" value="{{ old('unique_code', $user->unique_code) }}">
                    </div>
                    <div class="staff-form-group">
                        <label class="staff-label" for="username">Username</label>
                        <input type="text" id="username" name="username" class="staff-input" value="{{ old('username', $user->username) }}">
                    </div>
                    <div class="staff-form-group">
                        <label class="staff-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="staff-input" value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="staff-form-group">
                        <label class="staff-label" for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" class="staff-input" value="{{ old('fullname', $user->fullname) }}">
                    </div>
                    <div class="staff-form-group">
                        <label class="staff-label" for="role">Role</label>
                        <select id="role" name="role" class="staff-input select2-single">
                            <option value="">Select role</option>
                            @php $currentRole = old('role', $user->role); @endphp
                            <option value="Branch" {{ $currentRole === 'Branch' ? 'selected' : '' }}>Branch</option>
                            <option value="Admin" {{ $currentRole === 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="Staff" {{ $currentRole === 'Staff' ? 'selected' : '' }}>Staff</option>
                            <option value="Checker" {{ $currentRole === 'Checker' ? 'selected' : '' }}>Checker</option>
                            <option value="User" {{ $currentRole === 'User' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>
                    <div class="staff-form-group">
                        <label class="staff-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="staff-input" placeholder="Leave blank to keep current password">
                    </div>
                    <div class="staff-form-group">
                        <label class="staff-label" for="branch">Branch</label>
                        <select id="branch" name="branch" class="staff-input select2-single">
                            <option value="">Select branch</option>
                            @php $currentBranch = old('branch', $user->branch); @endphp
                            @foreach($branches as $branch)
                                <option value="{{ $branch->branch_name }}" {{ $currentBranch === $branch->branch_name ? 'selected' : '' }}>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="staff-branch-note" id="branchNote">
                            Branch is <strong>required</strong> only when role is set to <strong>Branch</strong>.
                        </p>
                    </div>
                </div>
            </div>
            <div class="staff-form-actions">
                <a href="{{ route('users.index') }}" class="btn-staff-cancel">Cancel</a>
                <button type="submit" class="btn-staff-save" id="userSaveBtn">
                    <span class="btn-text">Update</span>
                    <span class="btn-spinner" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .staff-form-page { display: block; padding-bottom: 0; }
        body.page-users-edit .content { padding-bottom: 0; }
        .staff-form-header { margin-bottom: 1.75rem; }
        .staff-form-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .staff-form-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .staff-alert { padding: 0.875rem 1rem; border-radius: 10px; margin-bottom: 1.25rem; }
        .staff-alert-error { background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.4); color: #fca5a5; }
        .staff-error-list { margin: 0; padding-left: 1.25rem; }
        .staff-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; padding: 1.75rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
        .staff-form-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem 1.5rem; }
        @media (max-width: 1024px) {
            .staff-form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 640px) {
            .staff-form-grid { grid-template-columns: 1fr; }
        }
        .staff-form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .staff-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; }
        .staff-input { width: 100%; padding: 0.625rem 0.875rem; font-size: 0.9375rem; line-height: 1.4; border: 1px solid #334155; border-radius: 10px; background: #1e293b; color: #e2e8f0; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; min-height: 2.75rem; }
        .staff-input::placeholder { color: #64748b; }
        .staff-input:focus { outline: none; border-color: #2C528B; box-shadow: 0 0 0 3px rgba(44,82,139,0.25); }
        .staff-form-actions { display: flex; align-items: center; gap: 0.75rem; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1px solid #334155; }
        .btn-staff-save { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: #2C528B; color: #fff; padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; cursor: pointer; border: none; box-shadow: 0 2px 6px rgba(44,82,139,0.35); transition: background 0.2s; min-width: 110px; }
        .btn-staff-save:hover { background: #234a77; }
        .btn-staff-save:disabled { cursor: not-allowed; opacity: 0.85; }
        .btn-staff-save .btn-spinner { display: none; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: staff-spin 0.7s linear infinite; }
        .btn-staff-save.is-saving .btn-text { display: none; }
        .btn-staff-save.is-saving .btn-spinner { display: inline-block; }
        @keyframes staff-spin { to { transform: rotate(360deg); } }
        .btn-staff-cancel { padding: 0.75rem 1.5rem; font-size: 0.9375rem; font-weight: 600; border-radius: 10px; color: #94a3b8; text-decoration: none; transition: color 0.2s, background 0.2s; }
        .btn-staff-cancel:hover { color: #e2e8f0; }
        html[data-theme="light"] .staff-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .staff-form-title { color: #1e293b; }
        html[data-theme="light"] .staff-form-subtitle { color: #64748b; }
        html[data-theme="light"] .staff-input { background: #f8fafc; border-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .staff-form-actions { border-top-color: #e2e8f0; }
        .staff-branch-note { font-size: 0.75rem; color: #64748b; margin: 0.15rem 0 0; }
        html[data-theme="light"] .staff-branch-note { color: #6b7280; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function() {
            var form = document.querySelector('.staff-form');
            var btn = document.getElementById('userSaveBtn');
            var roleSelect = document.getElementById('role');
            var branchSelect = document.getElementById('branch');
            var branchNote = document.getElementById('branchNote');

            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2-single').select2({ width: '100%', allowClear: false });
            }

            function updateBranchRequirement() {
                if (!roleSelect || !branchSelect || !branchNote) return;
                var isBranchRole = roleSelect.value === 'Branch';
                branchSelect.required = isBranchRole;
                if (isBranchRole) {
                    branchNote.innerHTML = 'Branch is <strong>required</strong> when role is set to <strong>Branch</strong>.';
                } else {
                    branchNote.innerHTML = 'Branch is <strong>optional</strong> for this role. It is only required when role is <strong>Branch</strong>.';
                }
            }

            updateBranchRequirement();

            if (roleSelect) {
                roleSelect.addEventListener('change', updateBranchRequirement);
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

