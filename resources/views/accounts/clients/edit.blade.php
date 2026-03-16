@extends('layouts.dashboard')

@section('title', 'Edit Client Account')

@section('body_class', 'page-accounts-clients-edit')

@section('content')
    <div class="w-full">
        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-500/20 shadow-lg dark:bg-emerald-500/30">
                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Edit Client Account</h1>
                <p class="text-slate-600 dark:text-slate-400">Update client account #{{ $client->id }} ({{ $client->client_code }}).</p>
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

        <form action="{{ route('accounts.clients.update', $client) }}" method="POST" class="space-y-6" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1.6fr)]">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300" for="client_code">Client Code <span class="text-red-500">*</span></label>
                            <select id="client_code" name="client_code" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30" required>
                                <option value="{{ $client->client_code }}" {{ old('client_code', $client->client_code) === $client->client_code ? 'selected' : '' }}>
                                    {{ $client->client_code }} (current)
                                </option>
                                @foreach($users as $u)
                                    @if($u->unique_code !== $client->client_code)
                                        <option value="{{ $u->unique_code }}" {{ old('client_code') === $u->unique_code ? 'selected' : '' }}
                                            data-fullname="{{ e($u->fullname ?? '') }}"
                                            data-email="{{ e($u->email ?? '') }}">
                                            {{ $u->unique_code }} — {{ $u->fullname ?? 'N/A' }} ({{ $u->email ?? 'N/A' }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Shows users with role Branch or User only (Admin, Staff, Checker are excluded). Code = unique_code from User Accounts.</p>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300" for="client_name">Client Name <span class="text-red-500">*</span></label>
                                <input type="text" id="client_name" name="client_name" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30" placeholder="Client display name" value="{{ old('client_name', $client->client_name) }}" required>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300" for="client_email">Client Email <span class="text-red-500">*</span></label>
                                <input type="email" id="client_email" name="client_email" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30" placeholder="client@example.com" value="{{ old('client_email', $client->client_email) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Preview panel --}}
                <div class="flex w-full flex-col justify-between rounded-2xl bg-slate-50 px-4 py-4 text-sm text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800/60 dark:text-slate-200 dark:ring-slate-700">
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Preview</span>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[0.72rem] font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Client account
                        </span>
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Code & name</p>
                        <div class="flex items-center gap-3">
                            <span id="previewClientCode" class="inline-flex items-center rounded-full bg-slate-900/5 px-2.5 py-0.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-900/40 dark:text-slate-200 dark:ring-slate-600">
                                {{ old('client_code', $client->client_code) }}
                            </span>
                            <div class="min-w-0">
                                <p id="previewClientName" class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ old('client_name', $client->client_name) }}
                                </p>
                                <p id="previewClientEmail" class="truncate text-xs text-slate-500 dark:text-slate-400">
                                    {{ old('client_email', $client->client_email) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 text-[0.75rem] leading-snug text-slate-500 dark:text-slate-400">
                        This is how the client account will appear when selecting clients in LBS-related flows.
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('accounts.clients.index') }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:focus:ring-offset-slate-800">Cancel</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-70 dark:focus:ring-offset-slate-900" id="acClientSaveBtn">
                    <span class="btn-text">Update</span>
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
            var form = document.querySelector('form[action="{{ route('accounts.clients.update', $client) }}"]');
            var btn = document.getElementById('acClientSaveBtn');
            var codeSelect = document.getElementById('client_code');
            var nameInput = document.getElementById('client_name');
            var emailInput = document.getElementById('client_email');
            var previewCode = document.getElementById('previewClientCode');
            var previewName = document.getElementById('previewClientName');
            var previewEmail = document.getElementById('previewClientEmail');

            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#client_code').select2({ width: '100%', allowClear: false });
            }

            if (codeSelect && nameInput && emailInput) {
                codeSelect.addEventListener('change', function() {
                    var opt = this.options[this.selectedIndex];
                    if (opt && opt.value && opt.dataset.fullname !== undefined) {
                        nameInput.value = opt.dataset.fullname || nameInput.value;
                        emailInput.value = opt.dataset.email || emailInput.value;
                    }
                    if (previewCode) previewCode.textContent = this.value || '{{ $client->client_code }}';
                    if (previewName && nameInput) previewName.textContent = nameInput.value || '{{ $client->client_name }}';
                    if (previewEmail && emailInput) previewEmail.textContent = emailInput.value || '{{ $client->client_email }}';
                });
            }

            function updatePreviewText() {
                if (previewName && nameInput) previewName.textContent = nameInput.value || '{{ $client->client_name }}';
                if (previewEmail && emailInput) previewEmail.textContent = emailInput.value || '{{ $client->client_email }}';
            }

            if (nameInput) nameInput.addEventListener('input', updatePreviewText);
            if (emailInput) emailInput.addEventListener('input', updatePreviewText);

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
