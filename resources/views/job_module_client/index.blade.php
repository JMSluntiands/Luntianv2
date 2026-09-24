@extends('layouts.dashboard')

@section('title', 'Assign Client')

@section('body_class', 'page-job-module-client')

@section('content')
    <div class="w-full">
        <div class="mb-6 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-500/20 shadow-lg dark:bg-emerald-500/30">
                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Assign Client</h1>
                <p class="text-slate-600 dark:text-slate-400">Choose which client code appears in Job Request for each job. Add New Job Type lists follow this assignment — no need to recode the client account.</p>
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

        @if(session('success'))
            <div class="mb-4 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-700/70 dark:bg-emerald-900/30 dark:text-emerald-200">
                <span class="mt-0.5 inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white dark:bg-emerald-500">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="flex-1">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('job_module_client.update') }}" method="POST" class="space-y-4" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/80">
                                <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Job</th>
                                <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Client code</th>
                                <th class="w-40 px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Job requests</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                <tr class="border-b border-slate-100 dark:border-slate-700/80">
                                    <td class="px-5 py-3 font-medium text-slate-800 dark:text-slate-100">{{ $row['label'] }}</td>
                                    <td class="px-5 py-3">
                                        <select
                                            name="assignments[{{ $row['module'] }}]"
                                            class="job-module-client-select w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                                        >
                                            <option value="">— Select client —</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->client_code }}" @selected(old("assignments.{$row['module']}", $row['client_code']) === $client->client_code)>
                                                    {{ $client->client_code }} — {{ $client->client_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600 dark:text-slate-300">{{ number_format($row['job_request_count']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-200">
                <input type="hidden" name="retag_job_requests" value="0">
                <input type="checkbox" name="retag_job_requests" value="1" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" @checked(old('retag_job_requests', '1') === '1')>
                <span>
                    <span class="font-medium">Update matching Job Requests to the selected client code</span>
                    <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">Keeps Add New Job types in sync without renaming the client account. Uncheck to change the assignment only.</span>
                </span>
            </label>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    Save assignments
                </button>
                <p class="text-xs text-slate-500 dark:text-slate-400">Missing a code? Add it first under Accounts → Client Accounts.</p>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    @include('layouts.partials.select2-theme')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function() {
            if (typeof $ === 'undefined' || !$.fn.select2) return;
            $('.job-module-client-select').select2({ width: '100%', allowClear: false });
        })();
    </script>
@endpush
