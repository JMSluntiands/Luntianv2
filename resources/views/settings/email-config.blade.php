@extends('layouts.dashboard')

@section('title', 'Email Configuration (SMTP2Go)')

@section('body_class', 'page-settings-email-config')

@section('content')
    <div class="w-full max-w-3xl">
        {{-- Header --}}
        <div class="mb-8 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-500/10 shadow-lg dark:bg-emerald-500/20">
                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M22 6l-10 7L2 6"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Email Configuration</h1>
                <p class="text-slate-500 dark:text-slate-400">Configure SMTP (SMTP2Go) to send email and set the default sender address and name.</p>
            </div>
        </div>

        {{-- Flash success --}}
        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200" role="alert">
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200" role="alert">
                <ul class="list-inside list-disc space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="emailConfigForm" action="{{ route('settings.email_config.store') }}" method="POST" autocomplete="off" class="space-y-6">
            @csrf
            {{-- Hidden dummy fields so browser fills these instead of real SMTP fields --}}
            <div aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;pointer-events:none;">
                <input type="text" name="autofill_trap_username" tabindex="-1" autocomplete="off">
                <input type="password" name="autofill_trap_password" tabindex="-1" autocomplete="off">
            </div>

            {{-- SMTP setup card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="flex items-center gap-3 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/10">
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 17h10"/>
                        </svg>
                    </span>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">SMTP Setup (for sending email)</h2>
                </div>

                <div class="p-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="smtp_host" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                SMTP Host <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="smtp_host"
                                name="smtp_host"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500"
                                placeholder="mail.smtp2go.com"
                                value="{{ old('smtp_host', $config?->smtp_host ?? 'mail.smtp2go.com') }}"
                                required
                                autocomplete="off"
                            >
                        </div>

                        <div>
                            <label for="smtp_port" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                SMTP Port <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="smtp_port"
                                name="smtp_port"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500"
                                placeholder="2525"
                                min="1"
                                max="65535"
                                value="{{ old('smtp_port', $config?->smtp_port ?? 2525) }}"
                                required
                                autocomplete="off"
                            >
                            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">SMTP2Go: 2525, 587, or 465 (SSL)</p>
                        </div>

                        <div>
                            <label for="encryption" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Encryption</label>
                            <select
                                id="encryption"
                                name="encryption"
                                class="select2-single w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                                autocomplete="off"
                            >
                                <option value="">None</option>
                                <option value="tls" {{ old('encryption', $config?->encryption ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ old('encryption', $config?->encryption ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                        </div>

                        <div>
                            <label for="smtp_username" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">SMTP Username</label>
                            <input
                                type="text"
                                id="smtp_username"
                                name="smtp_username"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500"
                                placeholder="SMTP2Go username"
                                value="{{ old('smtp_username', $config?->smtp_username ?? '') }}"
                                autocomplete="off"
                                readonly
                                data-no-autofill
                            >
                        </div>

                        <div class="sm:col-span-2">
                            <label for="smtp_password" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">SMTP Password</label>
                            <input
                                type="password"
                                id="smtp_password"
                                name="smtp_password"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500"
                                placeholder="{{ $config ? 'Leave blank to keep current' : 'SMTP2Go password' }}"
                                autocomplete="new-password"
                                readonly
                                data-no-autofill
                            >
                        </div>
                    </div>
                </div>
            </div>

            {{-- Default sender card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="flex items-center gap-3 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/10">
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 15V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 15l9 6 9-6"/>
                        </svg>
                    </span>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Default sender (where email comes from)</h2>
                </div>

                <div class="p-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="from_email" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">From Email</label>
                            <input
                                type="email"
                                id="from_email"
                                name="from_email"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500"
                                placeholder="noreply@yourdomain.com"
                                value="{{ old('from_email', $config?->from_email ?? '') }}"
                                autocomplete="off"
                                readonly
                                data-no-autofill
                            >
                            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Default email address shown in “From”</p>
                        </div>

                        <div>
                            <label for="from_name" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">From Name</label>
                            <input
                                type="text"
                                id="from_name"
                                name="from_name"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500"
                                placeholder="Luntian"
                                value="{{ old('from_name', $config?->from_name ?? '') }}"
                                autocomplete="off"
                            >
                            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Name shown in “From”</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Toggle card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="flex items-center gap-3 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20">
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 6l-10 7L2 6"/>
                        </svg>
                    </span>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Notifications</h2>
                </div>

                <div class="p-5">
                    <label class="flex cursor-pointer items-center justify-between gap-4 rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-3 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800/50 dark:hover:bg-slate-800">
                        <span class="flex items-center gap-3">
                            <input type="hidden" name="is_active" value="0">
                            <span class="relative inline-flex h-6 w-11 flex-shrink-0 rounded-full border-2 border-transparent bg-slate-200 transition-colors duration-200 ease-in-out focus-within:ring-2 focus-within:ring-emerald-500 focus-within:ring-offset-2 dark:bg-slate-600 dark:focus-within:ring-offset-slate-900 has-[:checked]:bg-emerald-500">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $config?->is_active ?? true) ? 'checked' : '' }} class="sr-only peer">
                                <span class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0.5 peer-checked:translate-x-5 dark:bg-slate-100"></span>
                            </span>
                            <div>
                                <span class="block text-sm font-medium text-slate-800 dark:text-slate-100">Enable email sending</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400">When on, job submission and updates will send emails.</span>
                            </div>
                        </span>
                    </label>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-3">
                <button
                    type="submit"
                    id="emailConfigSubmitBtn"
                    class="cursor-pointer inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $config ? 'Update configuration' : 'Save configuration' }}
                </button>
                <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    @endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            $('.select2-single').select2({ width: '100%', allowClear: false });
            document.querySelectorAll('#emailConfigForm [data-no-autofill]').forEach(function(input) {
                function removeReadOnly() {
                    input.removeAttribute('readonly');
                    input.removeEventListener('focus', removeReadOnly);
                    input.removeEventListener('click', removeReadOnly);
                }
                input.addEventListener('focus', removeReadOnly);
                input.addEventListener('click', removeReadOnly);
            });
        });
    </script>
    <script>
        (function() {
            var form = document.getElementById('emailConfigForm');
            var btn = document.getElementById('emailConfigSubmitBtn');
            if (!form || !btn) return;

            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="mr-2 inline-block h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white" aria-hidden="true"></span>Saving...';
            });
        })();
    </script>
@endpush
