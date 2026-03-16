@extends('layouts.dashboard')

@section('title', 'Slack Configuration')

@section('body_class', 'page-settings-slack-config')

@section('content')
    <div class="w-full max-w-3xl">
        {{-- Page Header with Slack branding --}}
        <div class="mb-8 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-[#4A154B] shadow-lg dark:bg-[#3d0e3e]">
                <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zM8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312zM18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312zM15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.521-2.523 2.526 2.526 0 0 1 2.521-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z"/>
                </svg>
            </div>
            <div>
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Slack Configuration</h1>
                <p class="text-slate-500 dark:text-slate-400">Connect your Slack workspace to receive LBS job notifications. New jobs will be posted to your chosen channel.</p>
            </div>
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

        <form id="slackConfigForm" action="{{ route('settings.slack_config.store') }}" method="POST" autocomplete="off" class="space-y-6">
            @csrf

            {{-- Webhook card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="flex items-center gap-3 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#4A154B]/10 dark:bg-[#4A154B]/20">
                        <svg class="h-5 w-5 text-[#4A154B] dark:text-[#E01E5A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </span>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Incoming Webhook</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label for="webhook_url" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Webhook URL</label>
                        <input type="url" id="webhook_url" name="webhook_url"
                            class="slack-input w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-mono text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500"
                            placeholder="https://hooks.slack.com/services/..."
                            value="{{ old('webhook_url', $config?->webhook_url ?? '') }}" autocomplete="off">
                        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Create an Incoming Webhook in your Slack app and paste the URL here. Leave empty to disable notifications.</p>
                    </div>
                </div>
            </div>

            {{-- Toggle card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50 overflow-hidden">
                <div class="flex items-center gap-3 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/10 dark:bg-emerald-500/20">
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </span>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Notifications</h2>
                </div>
                <div class="p-5">
                    <label class="flex cursor-pointer items-center justify-between gap-4 rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-3 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800/50 dark:hover:bg-slate-800/80">
                        <span class="flex items-center gap-3">
                            <input type="hidden" name="is_active" value="0">
                            <span class="relative inline-flex h-6 w-11 flex-shrink-0 rounded-full border-2 border-transparent bg-slate-200 transition-colors duration-200 ease-in-out focus-within:ring-2 focus-within:ring-emerald-500 focus-within:ring-offset-2 dark:bg-slate-600 dark:focus-within:ring-offset-slate-900 has-[:checked]:bg-emerald-500">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $config?->is_active ?? true) ? 'checked' : '' }}
                                    class="sr-only peer">
                                <span class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0.5 peer-checked:translate-x-5 dark:bg-slate-100"></span>
                            </span>
                            <div>
                                <span class="block text-sm font-medium text-slate-800 dark:text-slate-100">Enable Slack notifications</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400">When on, new LBS jobs will post to your webhook channel.</span>
                            </div>
                        </span>
                    </label>
                </div>
            </div>

            {{-- Submit --}}
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
