@extends('layouts.dashboard')

@section('title', 'Leading Energy Job — ' . ($job->reference ?? $job->job_number ?? 'View'))

@section('body_class', 'page-leading-energy-view')

@section('content')
    @php
        $job = $job ?? null;
        $canViewClient = \App\Models\RolePermission::userMayAccessRoute('job_view.leading_energy.card.client_details');
        $canViewJob = \App\Models\RolePermission::userMayAccessRoute('job_view.leading_energy.card.job_details');
        $canViewNotes = \App\Models\RolePermission::userMayAccessRoute('job_view.leading_energy.card.notes');
        $canViewPlans = \App\Models\RolePermission::userMayAccessRoute('job_view.leading_energy.card.plans');
        $canViewDocuments = \App\Models\RolePermission::userMayAccessRoute('job_view.leading_energy.card.documents');

        $plans = json_decode((string) ($job->plans_files ?? '[]'), true);
        $docs = json_decode((string) ($job->docs_files ?? '[]'), true);
        if (!is_array($plans)) { $plans = []; }
        if (!is_array($docs)) { $docs = []; }
    @endphp
    <div class="mx-auto max-w-5xl pb-10">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="m-0 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Leading Energy Job</h1>
                <p class="m-0 mt-1 text-sm text-slate-600 dark:text-slate-400">
                    Reference: <span class="font-mono text-slate-800 dark:text-slate-200">{{ $job->reference ?? '—' }}</span>
                </p>
            </div>
            <a href="{{ route('leading_energy.list') }}" class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600">
                Back to list
            </a>
        </div>

        @if($canViewClient || $canViewJob || $canViewNotes || $canViewPlans || $canViewDocuments)
            <div class="space-y-4">
                @if($canViewClient || $canViewJob)
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @if($canViewClient)
                            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                                <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Client Details</h2>
                                <dl class="space-y-2 text-sm">
                                    <div><dt class="text-slate-500 dark:text-slate-400">Client</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $job->client_code ?? 'LE01' }}</dd></div>
                                    <div><dt class="text-slate-500 dark:text-slate-400">Client name</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $job->client_name ?? '—' }}</dd></div>
                                    <div><dt class="text-slate-500 dark:text-slate-400">Contact email</dt><dd class="break-all font-medium text-slate-900 dark:text-slate-100">{{ $job->contact_email ?? '—' }}</dd></div>
                                </dl>
                            </section>
                        @endif
                        @if($canViewJob)
                            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                                <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Job Details</h2>
                                <dl class="space-y-2 text-sm">
                                    <div><dt class="text-slate-500 dark:text-slate-400">Status</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $job->status ?? '—' }}</dd></div>
                                    <div><dt class="text-slate-500 dark:text-slate-400">Job type</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $job->job_type ?? '—' }}</dd></div>
                                    <div><dt class="text-slate-500 dark:text-slate-400">NCC</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $job->ncc ?? '—' }}</dd></div>
                                    <div><dt class="text-slate-500 dark:text-slate-400">Job number</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ $job->job_number ?? '—' }}</dd></div>
                                    <div><dt class="text-slate-500 dark:text-slate-400">Assigned / Checker</dt><dd class="font-medium text-slate-900 dark:text-slate-100">{{ strtoupper((string) ($job->assigned ?? '—')) }} / {{ strtoupper((string) ($job->checked ?? '—')) }}</dd></div>
                                </dl>
                            </section>
                        @endif
                    </div>
                @endif

                @if($canViewNotes)
                    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                        <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Notes</h2>
                        <div class="whitespace-pre-wrap text-sm text-slate-900 dark:text-slate-100">{{ $job->notes ?? '—' }}</div>
                    </section>
                @endif

                @if($canViewPlans)
                    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                        <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Plans</h2>
                        @if(empty($plans))
                            <p class="text-sm text-slate-500 dark:text-slate-400">No plan files.</p>
                        @else
                            <ul class="list-disc space-y-1 pl-5 text-sm text-slate-900 dark:text-slate-100">
                                @foreach($plans as $f)
                                    <li>{{ (string) $f }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </section>
                @endif

                @if($canViewDocuments)
                    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                        <h2 class="mb-3 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Documents</h2>
                        @if(empty($docs))
                            <p class="text-sm text-slate-500 dark:text-slate-400">No document files.</p>
                        @else
                            <ul class="list-disc space-y-1 pl-5 text-sm text-slate-900 dark:text-slate-100">
                                @foreach($docs as $f)
                                    <li>{{ (string) $f }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </section>
                @endif
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-300 bg-white px-5 py-6 text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                You do not have permission to view Leading Energy job cards.
            </div>
        @endif
    </div>
@endsection
