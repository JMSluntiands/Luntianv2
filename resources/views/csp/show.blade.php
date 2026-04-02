@extends('layouts.dashboard')

@section('title', 'CSP Job — ' . ($job->reference ?? $job->job_number ?? 'View'))

@section('body_class', 'page-csp-view')

@section('content')
    @php
        $job = $job ?? null;
        $canPrint = \App\Models\RolePermission::userMayAccessRoute('csp.job.printCompliance');
        $canUpdate = \App\Models\RolePermission::userMayAccessRoute('csp.update');
    @endphp
    <div class="mx-auto max-w-4xl pb-10">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="m-0 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">CSP job</h1>
                <p class="m-0 mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Reference: <span class="font-mono text-slate-800 dark:text-slate-200">{{ $job->reference ?? '—' }}</span>
                    @if(!empty($job->job_number))
                        · Job # <span class="font-mono">{{ $job->job_number }}</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if($canPrint)
                    <a href="{{ route('csp.job.printCompliance', ['id' => $job->id]) }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        Compliance PDF
                    </a>
                @endif
                <a href="{{ route('csp.list') }}" class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600">
                    Back to list
                </a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <dl class="grid gap-0 sm:grid-cols-2">
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:border-r">
                    <dt class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $job->status ?? '—' }}</dd>
                </div>
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <dt class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Urgent</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">{{ strtoupper((string) ($job->urgent ?? 'NO')) }}</dd>
                </div>
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:border-r">
                    <dt class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Client</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $job->client_name ?? '—' }}</dd>
                </div>
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <dt class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Contact email</dt>
                    <dd class="mt-1 break-all text-sm text-slate-800 dark:text-slate-200">{{ $job->contact_email ?? '—' }}</dd>
                </div>
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:border-r">
                    <dt class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Job type</dt>
                    <dd class="mt-1 text-sm text-slate-800 dark:text-slate-200">{{ $job->job_type ?? '—' }}</dd>
                </div>
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <dt class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">NCC</dt>
                    <dd class="mt-1 text-sm text-slate-800 dark:text-slate-200">{{ $job->ncc ?? '—' }}</dd>
                </div>
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:border-r">
                    <dt class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Assigned to</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">{{ strtoupper((string) ($job->assigned ?? '—')) }}</dd>
                </div>
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                    <dt class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Checked by</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900 dark:text-slate-100">{{ strtoupper((string) ($job->checked ?? '—')) }}</dd>
                </div>
                <div class="border-b border-slate-100 px-5 py-4 dark:border-slate-800 sm:col-span-2">
                    <dt class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Notes</dt>
                    <dd class="mt-1 whitespace-pre-wrap text-sm text-slate-800 dark:text-slate-200">{{ $job->notes ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        @if($canUpdate)
            <p class="mt-4 text-sm text-slate-500 dark:text-slate-400">
                To change status, assignment, or checker, use the <a href="{{ route('csp.list') }}" class="font-medium text-emerald-600 underline hover:text-emerald-700 dark:text-emerald-400">CSP list</a>
                (inline actions).
            </p>
        @endif
    </div>
@endsection
