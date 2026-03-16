@extends('layouts.dashboard')

@section('title', 'Add Job Request')

@section('body_class', 'page-job-request-create')

@section('content')
    <div class="w-full">
        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-500/20 shadow-lg dark:bg-emerald-500/30">
                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 7V5a2 2 0 012-2h10a2 2 0 012 2v2m-2 4h-4m-4 0H5m14 0v8a2 2 0 01-2 2H7a2 2 0 01-2-2v-8"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Add Job Request</h1>
                <p class="text-slate-600 dark:text-slate-400">Create a new job request definition that will be available when logging LBS jobs.</p>
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

        <form action="{{ route('job_request.store') }}" method="POST" class="space-y-6" autocomplete="off">
            @csrf
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
                <div class="grid gap-5 md:grid-cols-[2fr,3fr]">
                    <div class="space-y-4">
                        <div>
                            <label for="client_code" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Client code</label>
                            <select
                                id="client_code"
                                name="client_code"
                                class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 shadow-sm transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:focus:ring-emerald-500/30"
                                required
                            >
                                <option value="">— Select client —</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->client_code }}" {{ old('client_code') === $client->client_code ? 'selected' : '' }}>
                                        {{ $client->client_code }} — {{ $client->client_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="job_request_id" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Job request ID</label>
                            <input
                                type="text"
                                id="job_request_id"
                                name="job_request_id"
                                value="{{ old('job_request_id') }}"
                                maxlength="50"
                                required
                                autocomplete="off"
                                placeholder="e.g. EA_LBS_1SNatHERS"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30"
                            >
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Unique identifier used in spreadsheets / exports (max 50 characters).</p>
                        </div>
                        <div>
                            <label for="job_request_type" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Job request type</label>
                            <input
                                type="text"
                                id="job_request_type"
                                name="job_request_type"
                                value="{{ old('job_request_type') }}"
                                maxlength="255"
                                required
                                autocomplete="off"
                                placeholder="e.g. 1S NatHERS Base Model"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30"
                            >
                        </div>
                    </div>
                    <div class="flex items-stretch">
                        <div class="flex w-full flex-col justify-between rounded-xl bg-slate-50 px-4 py-4 text-sm text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800/60 dark:text-slate-200 dark:ring-slate-700">
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Preview</span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[0.72rem] font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    LBS header
                                </span>
                            </div>
                            <div class="space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Client code</p>
                                <p id="jrPreviewClient" class="rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-800 ring-1 ring-slate-200 dark:bg-slate-900/40 dark:text-slate-100 dark:ring-slate-700">
                                    {{ old('client_code', 'SHG') }}
                                </p>
                            </div>
                            <div class="mt-3 space-y-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Job request</p>
                                <p id="jrPreviewId" class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ old('job_request_id', 'EA_LBS_1SNatHERS') }}
                                </p>
                                <p id="jrPreviewType" class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ old('job_request_type', '1S NatHERS Base Model') }}
                                </p>
                            </div>
                            <p class="mt-3 text-[0.75rem] leading-snug text-slate-500 dark:text-slate-400">
                                This is how the job request will appear when assigning job types in LBS.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('job_request.index') }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:focus:ring-offset-slate-800">Cancel</a>
                <button type="submit" id="jobRequestSaveBtn" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-70 dark:focus:ring-offset-slate-900">
                    <span class="btn-text">Save</span>
                    <span class="btn-spinner hidden h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function() {
            var form = document.querySelector('form[action="{{ route('job_request.store') }}"]');
            var btn = document.getElementById('jobRequestSaveBtn');
            var clientSelect = document.getElementById('client_code');
            var jrIdInput = document.getElementById('job_request_id');
            var jrTypeInput = document.getElementById('job_request_type');
            var previewClient = document.getElementById('jrPreviewClient');
            var previewId = document.getElementById('jrPreviewId');
            var previewType = document.getElementById('jrPreviewType');

            if (typeof $ !== 'undefined' && $.fn.select2 && clientSelect) {
                $('#client_code').select2({ width: '100%', allowClear: false });
                $('#client_code').on('change', function() {
                    var val = this.value || 'SHG';
                    if (previewClient) previewClient.textContent = val;
                });
            } else if (clientSelect && previewClient) {
                clientSelect.addEventListener('change', function() {
                    previewClient.textContent = this.value || 'SHG';
                });
            }

            if (jrIdInput && previewId) {
                jrIdInput.addEventListener('input', function() {
                    previewId.textContent = this.value || 'EA_LBS_1SNatHERS';
                });
            }
            if (jrTypeInput && previewType) {
                jrTypeInput.addEventListener('input', function() {
                    previewType.textContent = this.value || '1S NatHERS Base Model';
                });
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
