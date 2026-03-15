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

