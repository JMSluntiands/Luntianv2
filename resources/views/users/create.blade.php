@extends('layouts.dashboard')

@section('title', 'Add User')

@section('body_class', 'page-users-create')

@section('content')
    <div class="w-full">
        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-500/20 shadow-lg dark:bg-emerald-500/30">
                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5.33-3.8M9 20H4v-2a4 4 0 015.33-3.8M8 7a4 4 0 118 0 4 4 0 01-8 0z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Add User</h1>
                <p class="text-slate-600 dark:text-slate-400">Create a new application user (non-admin) with role and branch assignment.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-800 dark:bg-red-900/20" role="alert">
                <ul class="list-inside list-disc space-y-1 text-sm text-red-700 dark:text-red-200">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" class="space-y-6" autocomplete="off">
            @csrf
            <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1.4fr)]">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300" for="unique_code">Code</label>
                            <input type="text" id="unique_code" name="unique_code" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30" placeholder="Unique code" value="{{ old('unique_code') }}">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300" for="username">Username</label>
                            <input type="text" id="username" name="username" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30" placeholder="Login username" value="{{ old('username') }}">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300" for="email">Email</label>
                            <input type="email" id="email" name="email" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30" placeholder="Email address" value="{{ old('email') }}">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300" for="fullname">Full Name</label>
                            <input type="text" id="fullname" name="fullname" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30" placeholder="Full name" value="{{ old('fullname') }}">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300" for="role">Role</label>
                            <select id="role" name="role" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30">
                                <option value="">Select role</option>
                                <option value="Branch" {{ old('role') === 'Branch' ? 'selected' : '' }}>Branch</option>
                                <option value="Admin" {{ old('role') === 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="Staff" {{ old('role') === 'Staff' ? 'selected' : '' }}>Staff</option>
                                <option value="Checker" {{ old('role') === 'Checker' ? 'selected' : '' }}>Checker</option>
                                <option value="User" {{ old('role') === 'User' ? 'selected' : '' }}>User</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300" for="password">Password</label>
                            <input type="password" id="password" name="password" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30" placeholder="Leave blank for default 123456">
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300" for="branch">Branch</label>
                            <select id="branch" name="branch" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30">
                                <option value="">Select branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branch_name }}" {{ old('branch') === $branch->branch_name ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400" id="branchNote">
                                Branch is <strong>required</strong> only when role is set to <strong>Branch</strong>.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Preview panel --}}
                <div class="flex w-full flex-col justify-between rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800/60 dark:text-slate-200 dark:ring-slate-700">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Preview</span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[0.72rem] font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            User card
                        </span>
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Identity</p>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/20 text-sm font-semibold text-emerald-700 dark:bg-emerald-500/25 dark:text-emerald-200">
                                <span id="previewInitials">{{ Str::of(old('fullname', 'User Name'))->trim()->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->join('') }}</span>
                            </div>
                            <div class="min-w-0">
                                <p id="previewFullname" class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ old('fullname', 'User Name') }}
                                </p>
                                <p id="previewEmail" class="truncate text-xs text-slate-500 dark:text-slate-400">
                                    {{ old('email', 'user@example.com') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <p class="mb-1 text-[0.7rem] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Role</p>
                            <p id="previewRole" class="rounded-full bg-slate-900/5 px-2 py-1 text-[0.72rem] font-medium text-slate-700 ring-1 ring-slate-200 dark:bg-slate-900/40 dark:text-slate-200 dark:ring-slate-600">
                                {{ old('role', 'Staff') ?: 'Staff' }}
                            </p>
                        </div>
                        <div>
                            <p class="mb-1 text-[0.7rem] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Branch</p>
                            <p id="previewBranch" class="truncate rounded-full bg-slate-900/5 px-2 py-1 text-[0.72rem] font-medium text-slate-700 ring-1 ring-slate-200 dark:bg-slate-900/40 dark:text-slate-200 dark:ring-slate-600">
                                {{ old('branch', 'LBS') ?: '—' }}
                            </p>
                        </div>
                    </div>
                    <p class="mt-3 text-[0.75rem] leading-snug text-slate-500 dark:text-slate-400">
                        This is how the user will appear in tables and selectors across the dashboard.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('users.index') }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:focus:ring-offset-slate-800">Cancel</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-70 dark:focus:ring-offset-slate-900" id="userSaveBtn">
                    <span class="btn-text">Save</span>
                    <span class="btn-spinner hidden h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    @include('layouts.partials.select2-theme')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function() {
            var form = document.querySelector('form[action="{{ route('users.store') }}"]');
            var btn = document.getElementById('userSaveBtn');
            var roleSelect = document.getElementById('role');
            var branchSelect = document.getElementById('branch');
            var branchNote = document.getElementById('branchNote');
            var fullnameInput = document.getElementById('fullname');
            var emailInput = document.getElementById('email');
            var previewFullname = document.getElementById('previewFullname');
            var previewEmail = document.getElementById('previewEmail');
            var previewRole = document.getElementById('previewRole');
            var previewBranch = document.getElementById('previewBranch');
            var previewInitials = document.getElementById('previewInitials');

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

            function updatePreview() {
                if (fullnameInput && previewFullname && previewInitials) {
                    var name = fullnameInput.value || 'User Name';
                    previewFullname.textContent = name;
                    var parts = name.trim().split(/\s+/).filter(Boolean);
                    var initials = parts.map(function(p) { return p.charAt(0); }).join('').toUpperCase();
                    previewInitials.textContent = initials || 'UN';
                }
                if (emailInput && previewEmail) {
                    previewEmail.textContent = emailInput.value || 'user@example.com';
                }
                if (roleSelect && previewRole) {
                    previewRole.textContent = roleSelect.value || 'Staff';
                }
                if (branchSelect && previewBranch) {
                    previewBranch.textContent = branchSelect.value || 'LBS';
                }
            }

            updatePreview();

            if (roleSelect) {
                roleSelect.addEventListener('change', function() {
                    updateBranchRequirement();
                    updatePreview();
                });
            }

            if (branchSelect) {
                branchSelect.addEventListener('change', updatePreview);
            }
            if (fullnameInput) {
                fullnameInput.addEventListener('input', updatePreview);
            }
            if (emailInput) {
                emailInput.addEventListener('input', updatePreview);
            }

            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.disabled = true;
                    var text = btn.querySelector('.btn-text');
                    var spinner = btn.querySelector('.btn-spinner');
                    if (text) text.textContent = 'Saving...';
                    if (spinner) spinner.classList.remove('hidden');
                });
            }
        })();
    </script>
@endpush

