@extends('layouts.dashboard')

@section('title', 'Notification Controls')

@section('body_class', 'page-settings-notifications')

@section('content')
    <div class="w-full max-w-3xl space-y-6">
        <div class="mb-2 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-500/10 shadow-lg dark:bg-emerald-500/20">
                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Notification Controls</h1>
                <p class="text-slate-500 dark:text-slate-400">Turn Email and Slack sending on or off. Configuration details stay in their own pages.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200">
                <ul class="list-inside list-disc space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(\App\Models\RolePermission::userMayAccessRoute('settings.email_config.toggle'))
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Email Sending</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Current status: <span class="font-medium">{{ !empty($emailConfig?->is_active) ? 'ON' : 'OFF' }}</span></p>
                </div>
                <form action="{{ route('settings.email_config.toggle') }}" method="POST">
                    @csrf
                    <input type="hidden" name="is_active" value="{{ !empty($emailConfig?->is_active) ? 0 : 1 }}">
                    <button type="submit" class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold {{ !empty($emailConfig?->is_active) ? 'bg-red-600 text-white hover:bg-red-500' : 'bg-emerald-600 text-white hover:bg-emerald-500' }}">
                        {{ !empty($emailConfig?->is_active) ? 'Turn OFF' : 'Turn ON' }}
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if(\App\Models\RolePermission::userMayAccessRoute('settings.slack_config.toggle'))
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Slack Notifications</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Current status: <span class="font-medium">{{ !empty($slackConfig?->is_active) ? 'ON' : 'OFF' }}</span></p>
                </div>
                <form action="{{ route('settings.slack_config.toggle') }}" method="POST">
                    @csrf
                    <input type="hidden" name="is_active" value="{{ !empty($slackConfig?->is_active) ? 0 : 1 }}">
                    <button type="submit" class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold {{ !empty($slackConfig?->is_active) ? 'bg-red-600 text-white hover:bg-red-500' : 'bg-emerald-600 text-white hover:bg-emerald-500' }}">
                        {{ !empty($slackConfig?->is_active) ? 'Turn OFF' : 'Turn ON' }}
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
@endsection
