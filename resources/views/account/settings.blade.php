@extends('layouts.dashboard')

@section('title', 'Account Settings')

@section('body_class', 'page-account-settings')

@section('content')
    <div class="mx-auto w-full max-w-5xl">
        {{-- Page header --}}
        <div class="mb-8 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-500/15 shadow-lg ring-1 ring-emerald-500/20 dark:bg-emerald-500/25 dark:ring-emerald-400/20">
                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Account settings</h1>
                <p class="max-w-2xl text-[15px] leading-relaxed text-slate-600 dark:text-slate-400">
                    Update your name, sign-in details, and profile photo. Changes apply to how you appear in the app and on job activity.
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 dark:border-emerald-800/60 dark:bg-emerald-900/25" role="alert">
                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-700 dark:text-emerald-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
                <p class="text-sm font-medium text-emerald-900 dark:text-emerald-200">{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3.5 dark:border-red-800 dark:bg-red-900/20" role="alert">
                <p class="mb-2 text-sm font-semibold text-red-800 dark:text-red-200">Please fix the following:</p>
                <ul class="list-inside list-disc space-y-1 text-sm text-red-700 dark:text-red-300/90">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('account.settings.update') }}" method="POST" enctype="multipart/form-data" class="account-settings-form space-y-6" autocomplete="off">
            @csrf

            <div class="grid gap-6 lg:grid-cols-12 lg:items-start">
                {{-- Main fields --}}
                <div class="space-y-6 lg:col-span-7">
                    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
                        <div class="flex items-center gap-3 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-200/80 dark:bg-slate-700">
                                <svg class="h-5 w-5 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Profile information</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Name and contact shown across the dashboard</p>
                            </div>
                        </div>
                        <div class="space-y-5 p-5">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-slate-600 dark:text-slate-400" for="fullname">Full name</label>
                                <input type="text" id="fullname" name="fullname" required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition-colors placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-emerald-500/30"
                                    value="{{ old('fullname', $user->fullname) }}">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-slate-600 dark:text-slate-400" for="email">Email</label>
                                <input type="email" id="email" name="email" required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition-colors placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-emerald-500/30"
                                    value="{{ old('email', $user->email) }}">
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
                        <div class="flex items-center gap-3 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-200/80 dark:bg-slate-700">
                                <svg class="h-5 w-5 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Sign-in & security</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Username and optional password change</p>
                            </div>
                        </div>
                        <div class="space-y-5 p-5">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-slate-600 dark:text-slate-400" for="username">Username</label>
                                <input type="text" id="username" name="username" required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition-colors placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-emerald-500/30"
                                    value="{{ old('username', $user->username) }}">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-slate-600 dark:text-slate-400" for="password">
                                    New password <span class="font-normal text-slate-500">(optional)</span>
                                </label>
                                <input type="password" id="password" name="password" autocomplete="new-password"
                                    placeholder="Leave blank to keep current password"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-sm transition-colors placeholder:text-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-emerald-500/30">
                                <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Minimum 6 characters when you choose a new one.</p>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- Profile photo --}}
                <div class="lg:col-span-5">
                    <section class="sticky top-4 rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
                        <div class="flex items-center gap-3 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 dark:bg-emerald-500/25">
                                <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <div>
                                <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Profile photo</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Shown in the header and activity</p>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="mx-auto mb-5 flex justify-center">
                                <div class="relative">
                                    <div class="flex h-36 w-36 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 ring-4 ring-white shadow-lg dark:from-slate-700 dark:to-slate-800 dark:ring-slate-800">
                                        @if($user->profile_image)
                                            <img src="{{ route('account.settings.image') }}" alt="" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-4xl font-semibold tracking-tight text-slate-400 dark:text-slate-500">
                                                {{ strtoupper(mb_substr($user->fullname ?? 'U', 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 flex h-9 w-9 items-center justify-center rounded-xl border-2 border-white bg-emerald-500 text-white shadow-md dark:border-slate-800" aria-hidden="true">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-medium text-slate-600 dark:text-slate-400" for="profile_image">Upload new image</label>
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                    <button type="button" id="profileImageChooseBtn"
                                        class="inline-flex cursor-pointer items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-200 dark:hover:bg-slate-700">
                                        Choose file
                                    </button>
                                    <span class="min-w-0 truncate text-xs text-slate-500 dark:text-slate-400" id="profileImageFileName" title="No file chosen">No file chosen</span>
                                    <input type="file" id="profile_image" name="profile_image" class="sr-only" accept="image/*">
                                </div>
                                <p class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">JPEG or PNG, max 2MB. A square image looks best.</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-700/60 dark:hover:text-slate-200">
                    Back to dashboard
                </a>
                <button type="submit" id="accountSaveBtn"
                    class="inline-flex min-w-[140px] items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 disabled:pointer-events-none disabled:opacity-60">
                    <span class="btn-text">Save changes</span>
                    <span class="account-save-spinner hidden h-4 w-4 shrink-0 rounded-full border-2 border-white/30 border-t-white animate-spin" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

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
                    var sp = btn.querySelector('.account-save-spinner');
                    if (sp) sp.classList.remove('hidden');
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
                        var n = fileInput.files[0].name;
                        fileNameEl.textContent = n;
                        fileNameEl.setAttribute('title', n);
                    } else {
                        fileNameEl.textContent = 'No file chosen';
                        fileNameEl.setAttribute('title', 'No file chosen');
                    }
                });
            }
        })();
    </script>
@endpush
