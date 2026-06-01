@extends('layouts.dashboard')

@section('title', 'Slack Configuration')

@section('body_class', 'page-settings-slack-config')

@section('content')
    <div class="w-full max-w-3xl">
        <div class="mb-8 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-[#4A154B] shadow-lg dark:bg-[#3d0e3e]">
                <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zM8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312zM18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312zM15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.521-2.523 2.526 2.526 0 0 1 2.521-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z"/>
                </svg>
            </div>
            <div>
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Slack Configuration</h1>
                <p class="text-slate-500 dark:text-slate-400">Connect Slack with two optional webhooks: one for new jobs, one when staff or checker assignment changes.</p>
            </div>
        </div>

        @if($config)
            @php
                $newJobOn = (bool) ($config->new_job_slack_active ?? true);
                $assignmentOn = (bool) ($config->assignment_slack_active ?? true);
            @endphp
            <div class="mb-6 space-y-3">
                <div class="rounded-xl border px-4 py-3 text-sm {{ $newJobOn ? 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-800 dark:bg-emerald-900/25 dark:text-emerald-100' : 'border-amber-200 bg-amber-50 text-amber-950 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-100' }}">
                    <p class="font-semibold">New job Slack: {{ $newJobOn ? 'ON' : 'OFF' }}</p>
                    @if(!$newJobOn)
                        <p class="mt-1 text-xs text-slate-700 dark:text-slate-300">No new-job messages are sent while this switch is off, even if the new-job webhook URL is saved.</p>
                    @endif
                </div>
                <div class="rounded-xl border px-4 py-3 text-sm {{ $assignmentOn ? 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-800 dark:bg-emerald-900/25 dark:text-emerald-100' : 'border-amber-200 bg-amber-50 text-amber-950 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-100' }}">
                    <p class="font-semibold">Assignment change Slack: {{ $assignmentOn ? 'ON' : 'OFF' }}</p>
                    @if(!$assignmentOn)
                        <p class="mt-1 text-xs text-slate-700 dark:text-slate-300">No assignment messages are sent while this switch is off, even if the assignment webhook URL is saved.</p>
                    @endif
                </div>
            </div>
        @endif

        <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-800/80 dark:text-slate-300">
            <p class="font-semibold text-slate-800 dark:text-slate-100">When do messages appear in Slack?</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                <li><strong>New job</strong> — Uses the <em>New job webhook</em>. After you save a job, the modal flow posts there (not when you only open this settings page).</li>
                <li><strong>Staff / Checker change</strong> — Uses the <em>Assignment change webhook</em>. Sent when assignment is changed on the job view and saved; includes date, time, and names.</li>
            </ul>
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Each webhook posts to the Slack channel you picked when creating that Incoming Webhook in Slack.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200" role="alert">
                <ul class="list-inside list-disc space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($config && \App\Models\RolePermission::userMayAccessRoute('settings.slack_config.toggle'))
            <div class="mb-6 space-y-4">
                <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Switches</h2>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">New job Slack</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Status: <span class="font-medium">{{ ($config->new_job_slack_active ?? true) ? 'ON' : 'OFF' }}</span></p>
                        </div>
                        <form action="{{ route('settings.slack_config.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="purpose" value="new_job">
                            <button type="submit" class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold {{ ($config->new_job_slack_active ?? true) ? 'bg-red-600 text-white hover:bg-red-500' : 'bg-emerald-600 text-white hover:bg-emerald-500' }}">
                                {{ ($config->new_job_slack_active ?? true) ? 'Turn OFF' : 'Turn ON' }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">Assignment change Slack</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Status: <span class="font-medium">{{ ($config->assignment_slack_active ?? true) ? 'ON' : 'OFF' }}</span></p>
                        </div>
                        <form action="{{ route('settings.slack_config.toggle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="purpose" value="assignment">
                            <button type="submit" class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold {{ ($config->assignment_slack_active ?? true) ? 'bg-red-600 text-white hover:bg-red-500' : 'bg-emerald-600 text-white hover:bg-emerald-500' }}">
                                {{ ($config->assignment_slack_active ?? true) ? 'Turn OFF' : 'Turn ON' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <form id="slackConfigForm" action="{{ route('settings.slack_config.store') }}" method="POST" autocomplete="off" class="space-y-6">
            @csrf

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="flex items-center gap-3 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#4A154B]/10 dark:bg-[#4A154B]/20">
                        <svg class="h-5 w-5 text-[#4A154B] dark:text-[#E01E5A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </span>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Incoming webhooks</h2>
                </div>
                <div class="p-5 space-y-6">
                    <div>
                        <label for="webhook_new_job_url" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">New job webhook URL</label>
                        <input type="url" id="webhook_new_job_url" name="webhook_new_job_url"
                            class="slack-input w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-mono text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500"
                            placeholder="https://hooks.slack.com/services/..."
                            value="{{ old('webhook_new_job_url', $config?->webhook_new_job_url ?? $config?->webhook_url ?? '') }}" autocomplete="off">
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Slack channel for notifications when a job is created (post-save / modal flow).</p>
                    </div>
                    <div>
                        <label for="webhook_assignment_url" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Assignment change webhook URL</label>
                        <input type="url" id="webhook_assignment_url" name="webhook_assignment_url"
                            class="slack-input w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-mono text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500"
                            placeholder="https://hooks.slack.com/services/..."
                            value="{{ old('webhook_assignment_url', $config?->webhook_assignment_url ?? $config?->webhook_url ?? '') }}" autocomplete="off">
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Slack channel for notifications when staff or checker assignment is updated on a job.</p>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Create separate Incoming Webhooks in Slack if you want new jobs and assignment changes in different channels. Leave a field empty to skip that type. If upgrading from a single saved URL, run migrations so existing URLs are copied to both fields.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" id="slackSubmitBtn"
                    class="cursor-pointer inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <script>
        (function() {
            var form = document.getElementById('slackConfigForm');
            var btn = document.getElementById('slackSubmitBtn');
            if (!form || !btn) return;
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="mr-2 inline-block h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white" aria-hidden="true"></span>Saving...';
            });
        })();
    </script>
@endpush
