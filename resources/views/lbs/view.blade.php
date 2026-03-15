@extends('layouts.dashboard')

@section('title', 'Job Details')

@section('body_class', 'page-lbs-view')

@section('content')
    <div class="min-h-0 w-full max-w-full">
        {{-- Breadcrumb --}}
        <nav class="mb-6 flex flex-wrap items-center gap-1 text-sm" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}" class="text-slate-500 transition-colors hover:text-slate-800 dark:text-slate-400 dark:hover:text-white">Home</a>
            <span class="text-slate-400 dark:text-slate-500">/</span>
            <a href="{{ route('lbs.list') }}" class="text-slate-500 transition-colors hover:text-slate-800 dark:text-slate-400 dark:hover:text-white">Job List</a>
            <span class="text-slate-400 dark:text-slate-500">/</span>
            <span class="font-medium text-slate-800 dark:text-white">Job {{ $job->reference ?? $job->job_id ?? $jobId ?? '' }}</span>
        </nav>

        @php
            $isArchived = strtolower($job->job_status ?? '') === 'archived';
        @endphp
        {{-- Page header --}}
        <header class="mb-8 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="m-0 mb-1 text-[1.625rem] font-bold tracking-tight text-slate-800 dark:text-white">Job Details</h1>
                <p class="m-0 text-sm text-slate-500 dark:text-slate-400">
                    Reference: <span class="font-mono font-medium text-slate-700 dark:text-slate-300">{{ $job->reference ?? $job->job_reference_no ?? $jobId ?? '—' }}</span>
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                @if(!$isArchived)
                    <button type="button" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700" id="jobViewArchiveJobBtn" aria-label="Archive this job">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8v13H3V8M1 3h22v5H1zM10 12h4"/></svg>
                        Archive this job
                    </button>
                @endif
                <a href="{{ route('lbs.list') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Back to List
                </a>
            </div>
        </header>

        @php
            $planFiles = [];
            $docFiles = [];
            $folderName = $job->job_reference_no ?? $job->client_reference_no ?? $job->reference ?? '';

            if (!empty($job->upload_files)) {
                $decoded = json_decode($job->upload_files, true);
                if (is_array($decoded)) {
                    $planFiles = $decoded;
                }
            }

            if (!empty($job->upload_project_files)) {
                $decoded = json_decode($job->upload_project_files, true);
                if (is_array($decoded)) {
                    $docFiles = $decoded;
                }
            }
        @endphp

        @php
            $rawStatus = $job->job_status ?? '';
            $lowerStatus = strtolower($rawStatus);
            $isAllocated = $lowerStatus === 'allocated';
            $statusBg = $statusColor ?? null;
            $priorityBg = $priorityColor ?? null;
            // Disable all Edit (Client/Job/Notes) when status is Completed, For Review, or For Email Confirmation
            $canEditDetails = !in_array($lowerStatus, ['completed', 'for review', 'for email confirmation', 'processing'], true);
            // Same flow as Edit Job Details modal: Allocated→Accepted/Processing; Accepted/Processing/Revised→For Checking; For Checking→For Review/Revised
            $canEditStatusInline = in_array($lowerStatus, ['allocated', 'accepted', 'processing', 'revised', 'for checking'], true);
            $inlineStatusOptions = [];
            if ($lowerStatus === 'allocated') {
                foreach ($statuses ?? [] as $s) {
                    $n = strtolower((string)($s->name ?? ''));
                    if (in_array($n, ['accepted', 'processing'], true)) $inlineStatusOptions[] = $s->name;
                }
            } elseif (in_array($lowerStatus, ['accepted', 'processing', 'revised'], true)) {
                foreach ($statuses ?? [] as $s) {
                    if (strtolower((string)($s->name ?? '')) === 'for checking') $inlineStatusOptions[] = $s->name;
                }
            } elseif ($lowerStatus === 'for checking') {
                foreach ($statuses ?? [] as $s) {
                    $n = strtolower((string)($s->name ?? ''));
                    if (in_array($n, ['for review', 'revised'], true)) $inlineStatusOptions[] = $s->name;
                }
            } else {
                foreach ($statuses ?? [] as $s) { $inlineStatusOptions[] = $s->name; }
            }
        @endphp

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 lg:gap-8">
            {{-- Main content column --}}
            <div class="flex flex-col gap-6 lg:col-span-3 lg:gap-8">
                {{-- Section: Details --}}
                <div class="space-y-4">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Details</h2>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {{-- Client Details --}}
                        <section class="job-details-card rounded-xl border shadow-sm" id="jobClientCard">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-600 dark:bg-slate-700/40">
                                <h2 class="m-0 text-base font-semibold text-slate-800 dark:text-slate-100">Client Details</h2>
                                @if($canEditDetails)
                                    <button type="button" class="rounded-lg border border-slate-300 bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-200 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600" aria-label="Edit" data-job-view-edit data-edit-title="Client Details" data-edit-target="client">Edit</button>
                                @else
                                    <span class="rounded-lg border border-slate-200 bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-400 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-500" aria-hidden="true">Edit</span>
                                @endif
                            </div>
                            <dl class="job-details-dl">
                                <div class="job-details-row">
                                    <dt class="job-details-dt">Log Date</dt>
                                    <dd class="job-details-dd">@if(!empty($job->log_date)){{ \Carbon\Carbon::parse($job->log_date)->format('M d, Y h:i A') }}@else—@endif</dd>
                                </div>
                                <div class="job-details-row">
                                    <dt class="job-details-dt">Job Number</dt>
                                    <dd class="job-details-dd font-mono">{{ $job->job_reference_no ?? $job->reference ?? $jobId ?? '—' }}</dd>
                                </div>
                                <div class="job-details-row">
                                    <dt class="job-details-dt">Client</dt>
                                    <dd class="job-details-dd">{{ $job->client_account_name ?? $job->client_code ?? '—' }}</dd>
                                </div>
                                <div class="job-details-row">
                                    <dt class="job-details-dt">Compliance</dt>
                                    <dd class="job-details-dd">{{ $job->ncc_compliance ?? '—' }}</dd>
                                </div>
                                <div class="job-details-row">
                                    <dt class="job-details-dt">Client Reference</dt>
                                    <dd class="job-details-dd">{{ $job->client_reference_no ?? '—' }}</dd>
                                </div>
                            </dl>
                        </section>

                        {{-- Job Details --}}
                        <section class="job-details-card rounded-xl border shadow-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-600 dark:bg-slate-700/40">
                                <h2 class="m-0 text-base font-semibold text-slate-800 dark:text-slate-100">Job Details</h2>
                                @if($canEditDetails)
                                    <button type="button" class="rounded-lg border border-slate-300 bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-200 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600" aria-label="Edit" data-job-view-edit data-edit-title="Job Details" data-edit-target="job">Edit</button>
                                @else
                                    <span class="rounded-lg border border-slate-200 bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-400 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-500" aria-hidden="true">Edit</span>
                                @endif
                            </div>
                            <dl class="job-details-dl">
                                <div class="job-details-row">
                                    <dt class="job-details-dt">Job Status</dt>
                                    <dd>
                                        @if($canEditStatusInline && count($inlineStatusOptions) > 0)
                                            <div class="lbs-status-wrap relative inline-block" data-status-wrap>
                                                <button type="button" class="lbs-badge lbs-status-trigger inline-block rounded-full border-0 px-3 py-1 text-xs font-semibold text-white cursor-pointer hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-500/40" @if($statusBg) style="background-color: {{ $statusBg }};" @endif data-status-trigger aria-haspopup="true" aria-expanded="false">{{ $job->job_status ?? '—' }}</button>
                                                <div class="lbs-status-menu fixed z-[9999] flex min-w-[120px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg" role="menu" hidden>
                                                    @foreach($inlineStatusOptions as $opt)
                                                        <button type="button" role="menuitem" class="lbs-status-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10" data-status-value="{{ $opt }}">{{ $opt }}</button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <span class="job-details-badge inline-block rounded-full px-3 py-1 text-xs font-semibold text-white" @if($statusBg) style="background-color: {{ $statusBg }};" @endif aria-disabled="true">{{ $job->job_status ?? '—' }}</span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="job-details-row">
                                    <dt class="job-details-dt">Job Address</dt>
                                    <dd class="job-details-dd">{{ $job->address_client ?? '—' }}</dd>
                                </div>
                                <div class="job-details-row">
                                    <dt class="job-details-dt">Priority</dt>
                                    <dd><span class="job-details-badge inline-block rounded-full px-3 py-1 text-xs font-semibold text-white" @if($priorityBg) style="background-color: {{ $priorityBg }};" @endif>{{ $job->priority ?? '—' }}</span></dd>
                                </div>
                                <div class="job-details-row">
                                    <dt class="job-details-dt">Job Type</dt>
                                    <dd class="job-details-dd">{{ $job->job_type ?? '—' }}</dd>
                                </div>
                            </dl>
                        </section>
                    </div>
                    {{-- Notes (full width below) --}}
                    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                    <h2 class="m-0 text-base font-semibold text-slate-800 dark:text-white">Notes</h2>
                    @if($canEditDetails)
                        <button type="button" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600" aria-label="Edit" data-job-view-edit data-edit-title="Notes" data-edit-target="notes">Edit</button>
                    @else
                        <span class="rounded-lg border border-slate-200 bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-400 dark:border-slate-600 dark:bg-slate-700/50 dark:text-slate-500" aria-hidden="true">Edit</span>
                    @endif
                </div>
                <div class="px-5 py-4">
                    <div class="prose prose-sm max-w-none text-slate-700 dark:prose-invert dark:text-slate-300">
                        {!! $job->notes ?: '<p class="text-slate-500 dark:text-slate-400">No notes yet.</p>' !!}
                    </div>
                </div>
            </section>

                </div>

                {{-- Section: Files (col-4 each = 3 columns) --}}
                <div class="space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Files</h2>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        {{-- Plans --}}
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50" id="jobViewPlansCard">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="m-0 text-lg font-semibold text-slate-800 dark:text-white">Plans</h2>
                    @if($isAllocated)
                        <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" data-job-view-add-files data-add-title="Plans">Add Files</button>
                    @else
                        <button type="button" class="inline-flex cursor-not-allowed items-center gap-2 rounded-lg bg-slate-300 px-3 py-2 text-sm font-medium text-slate-500 dark:bg-slate-600 dark:text-slate-400" disabled aria-disabled="true">Add Files</button>
                    @endif
                </div>
                @if(!empty($planFiles) && $folderName)
                    <ul class="space-y-2">
                        @foreach($planFiles as $file)
                            @php $fileName = (string) $file; $fileUrl = route('lbs.job.file', ['id' => $jobId, 'file' => $fileName]); @endphp
                            <li class="flex flex-col gap-2 rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-3 dark:border-slate-600 dark:bg-slate-800/50">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-400" aria-hidden="true"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg></span>
                                    <span class="truncate text-sm font-medium text-slate-800 dark:text-slate-200">{{ $fileName }}</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="{{ $fileUrl }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600" title="Download" aria-label="Download" download><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg></a>
                                    <a href="{{ $fileUrl }}" target="_blank" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600" title="View" aria-label="View"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                                    @if($isAllocated)
                                        <button type="button" class="job-view-file-btn-delete inline-flex h-8 w-8 items-center justify-center rounded-md border border-red-200 bg-red-50 text-red-600 transition-colors hover:bg-red-100 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50" data-job-file-type="plans" data-job-file-name="{{ $fileName }}" title="Delete" aria-label="Delete"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg></button>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="flex flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-slate-300 py-10 dark:border-slate-600">
                    <svg class="h-10 w-10 text-slate-400 dark:text-slate-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-sm text-slate-500 dark:text-slate-400">No plan files uploaded yet.</p>
                </div>
                @endif
            </section>

                        {{-- Documents --}}
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="m-0 text-lg font-semibold text-slate-800 dark:text-white">Documents</h2>
                    @if($isAllocated)
                        <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" data-job-view-add-files data-add-title="Documents">Add Files</button>
                    @else
                        <button type="button" class="inline-flex cursor-not-allowed items-center gap-2 rounded-lg bg-slate-300 px-3 py-2 text-sm font-medium text-slate-500 dark:bg-slate-600 dark:text-slate-400" disabled aria-disabled="true">Add Files</button>
                    @endif
                </div>
                @if(!empty($docFiles) && $folderName)
                    <ul class="space-y-2">
                        @foreach($docFiles as $file)
                            @php $fileName = (string) $file; $fileUrl = route('lbs.job.file', ['id' => $jobId, 'file' => $fileName]); @endphp
                            <li class="flex flex-col gap-2 rounded-lg border border-slate-200 bg-slate-50/50 px-4 py-3 dark:border-slate-600 dark:bg-slate-800/50">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-400" aria-hidden="true"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg></span>
                                    <span class="truncate text-sm font-medium text-slate-800 dark:text-slate-200">{{ $fileName }}</span>
                                </div>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="{{ $fileUrl }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600" title="Download" aria-label="Download" download><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg></a>
                                    <a href="{{ $fileUrl }}" target="_blank" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600" title="View" aria-label="View"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                                    @if($isAllocated)
                                        <button type="button" class="job-view-file-btn-delete inline-flex h-8 w-8 items-center justify-center rounded-md border border-red-200 bg-red-50 text-red-600 transition-colors hover:bg-red-100 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50" data-job-file-type="documents" data-job-file-name="{{ $fileName }}" title="Delete" aria-label="Delete"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg></button>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="flex flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-slate-300 py-10 dark:border-slate-600">
                    <svg class="h-10 w-10 text-slate-400 dark:text-slate-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-sm text-slate-500 dark:text-slate-400">No document files uploaded yet.</p>
                </div>
                @endif
            </section>

                        {{-- Checker Upload Files --}}
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="m-0 text-lg font-semibold text-slate-800 dark:text-white">Checker Upload Files</h2>
                    <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" data-job-view-add-files data-add-title="Checker Upload Files">Add Files</button>
                </div>
                @if(($checkerUploads ?? collect())->isEmpty())
                    <div class="flex flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-slate-300 py-10 dark:border-slate-600">
                    <svg class="h-10 w-10 text-slate-400 dark:text-slate-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-sm text-slate-500 dark:text-slate-400">No checker uploads yet.</p>
                </div>
                @else
                    <ul class="space-y-6">
                        @foreach($checkerUploads as $index => $upload)
                            @php $files = json_decode($upload->files_json ?? '[]', true) ?: []; $uploadNumber = $loop->iteration; @endphp
                            <li class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 dark:border-slate-600 dark:bg-slate-800/30">
                                <div class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-200">Upload {{ $uploadNumber }}</div>
                                <div class="space-y-2">
                                    @foreach($files as $fileName)
                                        @php $fileName = (string) $fileName; $fileUrl = isset($folderName) && $folderName ? route('lbs.job.file', ['id' => $jobId, 'file' => $fileName]) : '#'; @endphp
                                        <div class="flex flex-col gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 dark:border-slate-600 dark:bg-slate-800/50">
                                            <div class="flex min-w-0 items-center gap-2">
                                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-400"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4z"/></svg></span>
                                                <span class="truncate text-sm font-medium text-slate-800 dark:text-slate-200">{{ $fileName }}</span>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <a href="{{ $fileUrl }}" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600" title="Download" aria-label="Download" download><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg></a>
                                                <a href="{{ $fileUrl }}" target="_blank" class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600" title="View" aria-label="View"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if(trim($upload->comment ?? '') !== '')
                                    <div class="mt-3 border-t border-slate-200 pt-3 dark:border-slate-600">
                                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Notes</span>
                                        <div class="prose prose-sm mt-1 max-w-none text-slate-700 dark:prose-invert dark:text-slate-300">{!! $upload->comment !!}</div>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

                    </div>
                </div>

                {{-- Section: Discussion (col-6 each) --}}
                <div class="space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Discussion</h2>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        {{-- Run Comments --}}
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                <h2 class="mb-4 text-lg font-semibold text-slate-800 dark:text-white">Run Comments</h2>
                <ul class="mb-6 space-y-4" id="runCommentsList">
                    @forelse($runComments as $runComment)
                        <li class="flex gap-3 rounded-lg border border-slate-100 bg-slate-50/50 p-4 dark:border-slate-700 dark:bg-slate-800/30">
                            @php
                                $initial = strtoupper(mb_substr($runComment->name ?? 'L', 0, 1));
                                $showProfileRun = (($runComment->name ?? '') === (session('user_name') ?? '')) && session('user_profile_image');
                            @endphp
                            @if($showProfileRun)
                                <span class="flex h-9 w-9 shrink-0 overflow-hidden rounded-full ring-2 ring-slate-200 dark:ring-slate-600" aria-hidden="true"><img src="{{ route('account.settings.image') }}" alt="" class="h-full w-full object-cover"></span>
                            @else
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-600 text-sm font-semibold text-white dark:bg-slate-500" aria-hidden="true">{{ $initial }}</span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="mb-1 text-sm font-medium text-slate-800 dark:text-slate-200">{{ $runComment->name ?? 'LUNTIAN' }}</p>
                                <div class="prose prose-sm max-w-none text-slate-700 dark:prose-invert dark:text-slate-300">{!! $runComment->message !!}</div>
                                <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">{{ $runComment->created_at }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="flex flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-slate-200 py-8 dark:border-slate-600">
                            <svg class="h-9 w-9 text-slate-400 dark:text-slate-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span class="text-sm text-slate-500 dark:text-slate-400">No run comments yet.</span>
                        </li>
                    @endforelse
                </ul>
                <div class="job-view-comment-editor rounded-xl border border-slate-200 bg-slate-50/50 p-3 dark:border-slate-600 dark:bg-slate-800/30">
                    <div class="mb-2 flex flex-wrap gap-1">
                        <button type="button" class="job-view-comment-btn rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="bold" title="Bold"><span class="font-bold">B</span></button>
                        <button type="button" class="job-view-comment-btn rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="italic" title="Italic"><span class="italic">I</span></button>
                        <button type="button" class="job-view-comment-btn rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="underline" title="Underline"><span class="underline">U</span></button>
                        <button type="button" class="job-view-comment-btn rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="insertUnorderedList" title="Bullets"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg></button>
                        <button type="button" class="job-view-comment-btn rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="insertOrderedList" title="Numbered list"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg></button>
                    </div>
                    <div class="job-view-comment-body min-h-[80px] rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus-within:ring-2 focus-within:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200" id="runCommentBody" contenteditable="true" data-placeholder="Write a run comment..." role="textbox"></div>
                    <div class="mt-2 flex justify-end"><button type="button" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" id="runCommentSendBtn">Send</button></div>
                </div>
            </section>

                        {{-- Comments --}}
                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                <h2 class="mb-4 text-lg font-semibold text-slate-800 dark:text-white">Comments</h2>
                <ul class="mb-6 space-y-4" id="jobCommentsList">
                    @forelse($jobComments as $comment)
                        <li class="flex gap-3 rounded-lg border border-slate-100 bg-slate-50/50 p-4 dark:border-slate-700 dark:bg-slate-800/30">
                            @php
                                $initial = strtoupper(mb_substr($comment->username ?? 'L', 0, 1));
                                $showProfileComment = (($comment->username ?? '') === (session('user_name') ?? '')) && session('user_profile_image');
                            @endphp
                            @if($showProfileComment)
                                <span class="flex h-9 w-9 shrink-0 overflow-hidden rounded-full ring-2 ring-slate-200 dark:ring-slate-600" aria-hidden="true"><img src="{{ route('account.settings.image') }}" alt="" class="h-full w-full object-cover"></span>
                            @else
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-600 text-sm font-semibold text-white dark:bg-slate-500" aria-hidden="true">{{ $initial }}</span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="mb-1 text-sm font-medium text-slate-800 dark:text-slate-200">{{ $comment->username ?? 'LUNTIAN' }}</p>
                                <div class="prose prose-sm max-w-none text-slate-700 dark:prose-invert dark:text-slate-300">{!! $comment->message !!}</div>
                                <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">{{ $comment->created_at }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="flex flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-slate-200 py-8 dark:border-slate-600">
                            <svg class="h-9 w-9 text-slate-400 dark:text-slate-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span class="text-sm text-slate-500 dark:text-slate-400">No comments yet.</span>
                        </li>
                    @endforelse
                </ul>
                <div class="job-view-comment-editor rounded-xl border border-slate-200 bg-slate-50/50 p-3 dark:border-slate-600 dark:bg-slate-800/30">
                    <div class="mb-2 flex flex-wrap gap-1">
                        <button type="button" class="job-view-comment-btn rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="bold" title="Bold"><span class="font-bold">B</span></button>
                        <button type="button" class="job-view-comment-btn rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="italic" title="Italic"><span class="italic">I</span></button>
                        <button type="button" class="job-view-comment-btn rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="underline" title="Underline"><span class="underline">U</span></button>
                        <button type="button" class="job-view-comment-btn rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="insertUnorderedList" title="Bullets"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg></button>
                        <button type="button" class="job-view-comment-btn rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="insertOrderedList" title="Numbered list"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg></button>
                    </div>
                    <div class="job-view-comment-body min-h-[80px] rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus-within:ring-2 focus-within:ring-blue-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200" id="jobCommentBody" contenteditable="true" data-placeholder="Write a comment..." role="textbox"></div>
                    <div class="mt-2 flex justify-end"><button type="button" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" id="jobCommentSendBtn">Send</button></div>
                </div>
            </section>

                    </div>
                </div>

                {{-- Section: Activity --}}
                <div class="space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Activity</h2>
                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50" id="jobActivityCard" data-current-user="{{ e(session('user_name', '')) }}" data-profile-image-url="{{ session('user_profile_image') ? route('account.settings.image') : '' }}">
                        <h2 class="mb-4 text-lg font-semibold text-slate-800 dark:text-white">Activity log</h2>
                        <ul class="space-y-4 job-view-activity" id="jobViewActivityList">
                            @forelse($activityLogs ?? [] as $log)
                                @php
                                    $date = \Carbon\Carbon::parse($log->activity_date, 'Asia/Manila');
                                    $dateText = $date->format('M d, Y h:i A');
                                    $initial = strtoupper(mb_substr($log->updated_by ?? 'L', 0, 1));
                                    $type = trim($log->activity_type ?? '');
                                    $isRich = in_array($type, ['Run comment', 'Comment', 'Checker upload'], true);
                                    $showProfileActivity = (($log->updated_by ?? '') === (session('user_name') ?? '')) && session('user_profile_image');
                                @endphp
                                <li class="job-view-activity-item flex gap-3 rounded-lg border border-slate-100 bg-slate-50/50 p-3 dark:border-slate-700 dark:bg-slate-800/30">
                                    <div class="job-view-activity-user flex shrink-0 flex-col items-center gap-1">
                                        @if($showProfileActivity)
                                            <span class="job-view-activity-avatar flex h-8 w-8 shrink-0 overflow-hidden rounded-full ring-2 ring-slate-200 dark:ring-slate-600" aria-hidden="true"><img src="{{ route('account.settings.image') }}" alt="" class="h-full w-full object-cover"></span>
                                        @else
                                            <span class="job-view-activity-avatar flex h-8 w-8 items-center justify-center rounded-full bg-slate-500 text-xs font-semibold text-white dark:bg-slate-500" aria-hidden="true">{{ $initial }}</span>
                                        @endif
                                        <span class="job-view-activity-name text-xs font-medium text-slate-600 dark:text-slate-400">{{ $log->updated_by ?? 'LUNTIAN' }}</span>
                                    </div>
                                    <div class="job-view-activity-content min-w-0 flex-1">
                                        <span class="job-view-activity-time block text-xs text-slate-500 dark:text-slate-400">{{ $dateText }}</span>
                                        <p class="job-view-activity-label mt-0.5 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ $log->activity_type ?? 'Update' }}</p>
                                        @if(!empty(trim($log->activity_description ?? '')))
                                            <div class="job-view-activity-text prose prose-sm mt-1 max-w-none text-slate-700 dark:prose-invert dark:text-slate-300">
                                                @if($isRich)
                                                    {!! $log->activity_description !!}
                                                @else
                                                    {!! nl2br(e($log->activity_description)) !!}
                                                @endif
                                            </div>
                                        @else
                                            <p class="job-view-activity-text mt-1 text-sm text-slate-500 dark:text-slate-400">—</p>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="job-view-activity-item flex gap-3 rounded-lg border border-dashed border-slate-200 py-8 dark:border-slate-600">
                                    <div class="job-view-activity-content mx-auto text-center">
                                        <p class="job-view-activity-text text-sm text-slate-500 dark:text-slate-400">No activity yet.</p>
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                    </section>
                </div>
            </div>

        </div>

        @include('lbs.modals.edit-modal')
        @include('lbs.modals.add-files-modal')
        {{-- Delete file modal --}}
        <div class="job-view-modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-opacity duration-200" id="jobViewDeleteFileModalOverlay" aria-hidden="true">
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800" role="dialog" aria-modal="true" aria-labelledby="jobViewDeleteFileModalTitle">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="jobViewDeleteFileModalTitle">Delete file</h2>
                </div>
                <div class="px-5 py-4">
                    <div id="jobViewDeleteFileConfirm">
                        <p class="text-slate-600 dark:text-slate-300">Are you sure you want to delete this file? This cannot be undone.</p>
                    </div>
                    <div id="jobViewDeleteFileCountdown" hidden>
                        <p class="text-slate-600 dark:text-slate-300">Deleting in</p>
                        <div class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400" id="jobViewDeleteFileCountdownNumber">3</div>
                        <p class="mt-1 text-sm text-slate-500">Click Cancel to abort</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                    <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600" id="jobViewDeleteFileModalCancel">Cancel</button>
                    <button type="button" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600" id="jobViewDeleteFileModalConfirm"><span class="job-view-delete-btn-text">Delete</span></button>
                </div>
            </div>
        </div>

        {{-- Archive job modal --}}
        <div class="job-view-modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-opacity duration-200" id="jobViewArchiveJobModalOverlay" aria-hidden="true">
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800" role="dialog" aria-modal="true" aria-labelledby="jobViewArchiveJobModalTitle">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="jobViewArchiveJobModalTitle">Archive this job</h2>
                </div>
                <div class="px-5 py-4">
                    <div id="jobViewArchiveJobConfirm">
                        <p class="text-slate-600 dark:text-slate-300">Are you sure you want to archive this job? The job will be moved to the archive list.</p>
                    </div>
                    <div id="jobViewArchiveJobCountdown" hidden>
                        <p class="text-slate-600 dark:text-slate-300">Archiving in</p>
                        <div class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400" id="jobViewArchiveJobCountdownNumber">3</div>
                        <p class="mt-1 text-sm text-slate-500">Click Cancel to abort</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                    <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600" id="jobViewArchiveJobModalCancel">Cancel</button>
                    <button type="button" class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600" id="jobViewArchiveJobModalConfirm"><span class="job-view-archive-btn-text">Archive</span></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @include('lbs.modals.styles')
<style>
/* Job Details cards: label/value rows with dividers */
.job-details-dl { margin: 0; }
.job-details-card {
    background: #fff;
    border-color: rgb(226 232 240);
}
.dark .job-details-card,
html[data-theme="dark"] .job-details-card {
    background: rgb(30 41 59);
    border-color: rgb(51 65 85);
}
.job-details-row {
    display: flex;
    flex-direction: column;
    gap: 0.375rem;
    padding: 0.875rem 1.25rem;
    border-bottom: 1px solid rgb(226 232 240);
    min-height: 3rem;
}
.dark .job-details-row,
html[data-theme="dark"] .job-details-row { border-bottom-color: rgb(51 65 85); }
.job-details-row:last-child { border-bottom: none; }
.job-details-dt {
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: rgb(100 116 139);
}
.dark .job-details-dt,
html[data-theme="dark"] .job-details-dt { color: rgb(148 163 184); }
.job-details-dd {
    font-size: 0.9375rem;
    font-weight: 500;
    line-height: 1.45;
    color: rgb(15 23 42);
    margin: 0;
    word-wrap: break-word;
    overflow-wrap: break-word;
}
.dark .job-details-dd,
html[data-theme="dark"] .job-details-dd {
    color: rgb(241 245 249);
    font-weight: 600;
}
.job-details-badge {
    font-size: 0.8125rem;
    padding: 0.35rem 0.75rem;
}
.job-view-modal-overlay.is-open { opacity: 1; pointer-events: auto; }
.lbs-status-menu[hidden], .lbs-initials-menu[hidden] { display: none !important; }
/* Rich text toolbar button active state */
.job-view-comment-btn.active {
    background-color: rgb(203 213 225);
    color: rgb(30 41 59);
}
html[data-theme="dark"] .job-view-comment-btn.active {
    background-color: rgb(71 85 105);
    color: rgb(226 232 240);
}
</style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function() {
    var csrfToken = '{{ csrf_token() }}';
    var updateUrl = '{{ route('lbs.job.update', ['id' => $jobId]) }}';
    var uploadFilesUrl = '{{ route('lbs.job.uploadFiles', ['id' => $jobId]) }}';
    var deleteFileUrl = '{{ route('lbs.job.deleteFile', ['id' => $jobId]) }}';
    var archiveJobUrl = '{{ route('lbs.job.archive', ['id' => $jobId]) }}';
    var checkerUploadUrl = '{{ route('lbs.job.checkerUploads', ['id' => $jobId]) }}';
    var runCommentUrl = '{{ route('lbs.job.runComment', ['id' => $jobId]) }}';
    var jobCommentUrl = '{{ route('lbs.job.comment', ['id' => $jobId]) }}';
    var jobViewFilesData = {
        planFiles: @json($planFiles ?? []),
        docFiles: @json($docFiles ?? [])
    };
    var jobViewFileUrlTemplate = @json(route('lbs.job.file', ['id' => $jobId, 'file' => '__FILE__']));
    function getJobViewActivityAvatarHtml(log) {
        var card = document.getElementById('jobActivityCard');
        var currentUser = card ? (card.getAttribute('data-current-user') || '') : '';
        var profileImageUrl = card ? (card.getAttribute('data-profile-image-url') || '') : '';
        var name = (log && log.updated_by) ? String(log.updated_by) : 'LUNTIAN';
        var initial = (log && log.updated_by ? String(log.updated_by) : 'L').charAt(0).toUpperCase();
        if (currentUser && name === currentUser && profileImageUrl) {
            return '<span class="job-view-activity-avatar flex h-8 w-8 shrink-0 overflow-hidden rounded-full ring-2 ring-slate-200 dark:ring-slate-600" aria-hidden="true"><img src="' + profileImageUrl.replace(/"/g, '&quot;') + '" alt="" class="h-full w-full object-cover"></span>';
        }
        return '<span class="job-view-activity-avatar flex h-8 w-8 items-center justify-center rounded-full bg-slate-500 text-xs font-semibold text-white dark:bg-slate-500" aria-hidden="true">' + initial + '</span>';
    }
    var currentAddFilesSection = null;
    var currentAddFilesMode = null; // 'plans' | 'documents' | 'checker'
    var editOverlay = document.getElementById('jobViewEditModalOverlay');

    var addOverlay = document.getElementById('jobViewAddFilesModalOverlay');
    var formClient = document.getElementById('jobViewEditFormClient');
    var formJob = document.getElementById('jobViewEditFormJob');
    var formNotes = document.getElementById('jobViewEditFormNotes');
    function openEditModal(title, target) {
        if (editOverlay) {
            var titleEl = document.getElementById('jobViewEditModalTitle');
            if (titleEl) titleEl.textContent = 'Edit ' + (title || '');
            if (formClient) formClient.hidden = true;
            if (formJob) formJob.hidden = true;
            if (formNotes) formNotes.hidden = true;
            var show = target === 'client' ? formClient : (target === 'job' ? formJob : (target === 'notes' ? formNotes : null));
            if (show) show.hidden = false;
            editOverlay.classList.add('is-open');
            editOverlay.setAttribute('aria-hidden', 'false');
        }
    }
    function closeEditModal() {
        if (editOverlay) {
            editOverlay.classList.remove('is-open');
            editOverlay.setAttribute('aria-hidden', 'true');
        }
    }
    function openAddFilesModal(title) {
        if (addOverlay) {
            var titleEl = document.getElementById('jobViewAddFilesModalTitle');
            var sectionEl = document.getElementById('jobViewAddFilesModalSection');
            if (titleEl) titleEl.textContent = 'Add Files — ' + (title || '');
            if (sectionEl) sectionEl.textContent = title || 'this section';
            var input = document.getElementById('jobViewAddFilesInput');
            if (input) input.value = '';
            var selectedWrap = document.getElementById('jobViewModalSelectedWrap');
            var selectedList = document.getElementById('jobViewModalSelectedFiles');
            if (selectedWrap) selectedWrap.hidden = true;
            if (selectedList) selectedList.innerHTML = '';
            var checkerNotes = document.getElementById('jobViewModalCheckerNotes');
            var existingWrap = document.getElementById('jobViewModalExistingWrap');
            if (checkerNotes) checkerNotes.hidden = (title !== 'Checker Upload Files');
            if (existingWrap) existingWrap.hidden = (title === 'Checker Upload Files');

            if (title === 'Plans') {
                currentAddFilesSection = 'plans';
                currentAddFilesMode = 'plans';
            } else if (title === 'Documents') {
                currentAddFilesSection = 'documents';
                currentAddFilesMode = 'documents';
            } else if (title === 'Checker Upload Files') {
                currentAddFilesSection = null;
                currentAddFilesMode = 'checker';
            } else {
                currentAddFilesSection = null;
                currentAddFilesMode = null;
            }

            var existingList = document.getElementById('jobViewModalExistingFiles');
            var noFilesEl = document.getElementById('jobViewModalNoFiles');
            if (existingList && noFilesEl) {
                existingList.innerHTML = '';
                var files = currentAddFilesSection === 'plans' ? (jobViewFilesData.planFiles || []) : (currentAddFilesSection === 'documents' ? (jobViewFilesData.docFiles || []) : []);
                if (files.length === 0) {
                    noFilesEl.hidden = false;
                } else {
                    noFilesEl.hidden = true;
                    files.forEach(function(fileName) {
                        var url = jobViewFileUrlTemplate.replace('__FILE__', encodeURIComponent(fileName));
                        var li = document.createElement('li');
                        li.className = 'job-view-modal-file-item';
                        li.innerHTML = '<span class="job-view-modal-file-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg></span>' +
                            '<span class="job-view-modal-file-name">' + fileName + '</span>' +
                            '<div class="job-view-modal-file-actions">' +
                            '<a href="' + url + '" class="job-view-modal-file-btn" title="Download" aria-label="Download" download><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg></a>' +
                            '<a href="' + url + '" target="_blank" class="job-view-modal-file-btn" title="View" aria-label="View"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>' +
                            '</div>';
                        existingList.appendChild(li);
                    });
                }
            }

            addOverlay.classList.add('is-open');
            addOverlay.setAttribute('aria-hidden', 'false');
        }
    }
    function closeAddFilesModal() {
        if (addOverlay) {
            addOverlay.classList.remove('is-open');
            addOverlay.setAttribute('aria-hidden', 'true');
        }
    }
    document.querySelectorAll('[data-job-view-edit]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            openEditModal(this.getAttribute('data-edit-title'), this.getAttribute('data-edit-target'));
        });
    });
    document.querySelectorAll('[data-job-view-add-files]').forEach(function(btn) {
        btn.addEventListener('click', function() { openAddFilesModal(this.getAttribute('data-add-title')); });
    });
    (function() {
        var input = document.getElementById('jobViewAddFilesInput');
        var selectedWrap = document.getElementById('jobViewModalSelectedWrap');
        var selectedList = document.getElementById('jobViewModalSelectedFiles');
        if (!input || !selectedWrap || !selectedList) return;
        var fileIconSvg = '<span class="job-view-modal-file-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg></span>';
        input.addEventListener('change', function() {
            selectedList.innerHTML = '';
            var files = this.files;
            if (files && files.length > 0) {
                for (var i = 0; i < files.length; i++) {
                    var li = document.createElement('li');
                    li.className = 'job-view-modal-file-item job-view-modal-file-item-new';
                    li.innerHTML = fileIconSvg + '<span class="job-view-modal-file-name">' + (files[i].name || 'File ' + (i + 1)) + '</span>';
                    selectedList.appendChild(li);
                }
                selectedWrap.hidden = false;
            } else {
                selectedWrap.hidden = true;
            }
        });
    })();

    (function bindComplexityStars() {
        var btn = document.querySelector('.job-view-complexity-button');
        if (!btn) return;
        var stars = btn.querySelectorAll('.lbs-star');
        function setStars(value) {
            stars.forEach(function(star, idx) {
                var i = idx + 1;
                if (i <= value) {
                    star.classList.add('lbs-star-filled');
                    star.classList.remove('lbs-star-empty');
                } else {
                    star.classList.add('lbs-star-empty');
                    star.classList.remove('lbs-star-filled');
                }
            });
            btn.setAttribute('data-complexity-rating', String(value));
        }
        function sendComplexity(value) {
            var current = parseInt(btn.getAttribute('data-complexity-rating') || '0', 10) || 0;
            if (current === value) return;
            var formData = new URLSearchParams();
            formData.append('_token', csrfToken);
            formData.append('plan_complexity', String(value));
            fetch(updateUrl, {
                method: 'PUT',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            }).then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: r.ok, data: { message: r.ok ? 'Updated.' : 'Failed to update complexity.' } }; }); }).then(function(result) {
                var msg = (result.data && result.data.message) || (result.ok ? 'Complexity updated.' : 'Failed to update complexity.');
                if (window.showSuccessToast) showSuccessToast(msg);
                if (result.ok) {
                    setStars(value);
                    if (result.data && Array.isArray(result.data.logs) && result.data.logs.length) {
                        var list = document.querySelector('.job-view-activity');
                        if (list) {
                            if (list.children.length === 1 && list.children[0].querySelector('.job-view-activity-text')) list.innerHTML = '';
                            result.data.logs.forEach(function(log) {
                                var li = document.createElement('li');
                                li.className = 'job-view-activity-item flex gap-3 rounded-lg border border-slate-100 bg-slate-50/50 p-3 dark:border-slate-700 dark:bg-slate-800/30';
                                var dateText = log.activity_date || '';
                                li.innerHTML =
                                    '<div class="job-view-activity-user flex shrink-0 flex-col items-center gap-1">' +
                                        getJobViewActivityAvatarHtml(log) +
                                        '<span class="job-view-activity-name text-xs font-medium text-slate-600 dark:text-slate-400">' + (log.updated_by || 'LUNTIAN') + '</span>' +
                                    '</div>' +
                                    '<div class="job-view-activity-content min-w-0 flex-1">' +
                                        '<span class="job-view-activity-time block text-xs text-slate-500 dark:text-slate-400">' + dateText + '</span>' +
                                        '<p class="job-view-activity-label mt-0.5 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">' + (log.activity_type || 'Update') + '</p>' +
                                        (log.activity_description ? '<div class="job-view-activity-text prose prose-sm mt-1 max-w-none text-slate-700 dark:prose-invert dark:text-slate-300">' + log.activity_description + '</div>' : '') +
                                    '</div>';
                                list.insertBefore(li, list.firstChild);
                            });
                        }
                    }
                    setTimeout(function() { window.location.reload(); }, 1800);
                }
            }).catch(function() {
                if (window.showSuccessToast) showSuccessToast('Failed to update complexity.');
            });
        }
        stars.forEach(function(star, idx) {
            star.style.cursor = 'pointer';
            star.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var value = idx + 1;
                sendComplexity(value);
            });
        });
    })();

    (function handleRunComments() {
        var sendBtn = document.getElementById('runCommentSendBtn');
        var bodyEl = document.getElementById('runCommentBody');
        var list = document.getElementById('runCommentsList');
        if (!sendBtn || !bodyEl || !list) return;
        sendBtn.addEventListener('click', function() {
            var html = bodyEl.innerHTML || '';
            var text = html.replace(/<[^>]*>/g, '').trim();
            if (!text) return;
            sendBtn.disabled = true;
            var formData = new URLSearchParams();
            formData.append('_token', csrfToken);
            formData.append('message', html);
            fetch(runCommentUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            }).then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: false, data: { message: 'Failed to add comment.' } }; }); }).then(function(result) {
                sendBtn.disabled = false;
                if (result.ok) {
                    if (window.showSuccessToast) showSuccessToast((result.data && result.data.message) || 'Run comment added.');
                } else {
                    if (window.showSuccessToast) showSuccessToast((result.data && result.data.message) || 'Failed to add comment.');
                }
                if (result.ok && result.data && result.data.comment) {
                    var c = result.data.comment;
                    bodyEl.innerHTML = '';
                    if (list.children.length === 1 && list.children[0].textContent.trim().indexOf('No run comments') !== -1) {
                        list.innerHTML = '';
                    }
                    var li = document.createElement('li');
                    li.className = 'flex gap-3 rounded-lg border border-slate-100 bg-slate-50/50 p-4 dark:border-slate-700 dark:bg-slate-800/30';
                    var initial = (c.name || 'L').toString().charAt(0).toUpperCase();
                    var name = (c.name || 'LUNTIAN').toString();
                    var created = (c.created_at || '').toString();
                    var avatar = document.createElement('span');
                    avatar.className = 'flex h-9 w-9 shrink-0 overflow-hidden rounded-full ring-2 ring-slate-200 dark:ring-slate-600';
                    avatar.setAttribute('aria-hidden', 'true');
                    if (c.profile_image_url) {
                        var img = document.createElement('img');
                        img.src = c.profile_image_url;
                        img.alt = '';
                        img.className = 'h-full w-full object-cover';
                        avatar.appendChild(img);
                    } else {
                        avatar.classList.add('items-center', 'justify-center', 'bg-slate-600', 'text-sm', 'font-semibold', 'text-white', 'dark:bg-slate-500');
                        avatar.textContent = initial;
                    }
                    var wrap = document.createElement('div');
                    wrap.className = 'min-w-0 flex-1';
                    wrap.innerHTML = '<p class="mb-1 text-sm font-medium text-slate-800 dark:text-slate-200"></p><div class="prose prose-sm max-w-none text-slate-700 dark:prose-invert dark:text-slate-300"></div><span class="mt-1 block text-xs text-slate-500 dark:text-slate-400"></span>';
                    wrap.querySelector('p').textContent = name;
                    wrap.querySelector('.prose').innerHTML = c.message || '';
                    wrap.querySelector('span').textContent = created;
                    li.appendChild(avatar);
                    li.appendChild(wrap);
                    list.insertBefore(li, list.firstChild);
                }
            }).catch(function() {
                sendBtn.disabled = false;
                if (window.showSuccessToast) showSuccessToast('Failed to add comment.');
            });
        });
    })();

    (function handleJobComments() {
        var sendBtn = document.getElementById('jobCommentSendBtn');
        var bodyEl = document.getElementById('jobCommentBody');
        var list = document.getElementById('jobCommentsList');
        if (!sendBtn || !bodyEl || !list) return;
        sendBtn.addEventListener('click', function() {
            var html = bodyEl.innerHTML || '';
            var text = html.replace(/<[^>]*>/g, '').trim();
            if (!text) return;
            sendBtn.disabled = true;
            var formData = new URLSearchParams();
            formData.append('_token', csrfToken);
            formData.append('message', html);
            fetch(jobCommentUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            }).then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: false, data: { message: 'Failed to add comment.' } }; }); }).then(function(result) {
                sendBtn.disabled = false;
                if (result.ok) {
                    if (window.showSuccessToast) showSuccessToast((result.data && result.data.message) || 'Comment added.');
                } else {
                    if (window.showSuccessToast) showSuccessToast((result.data && result.data.message) || 'Failed to add comment.');
                }
                if (result.ok && result.data && result.data.comment) {
                    var c = result.data.comment;
                    bodyEl.innerHTML = '';
                    if (list.children.length === 1 && list.children[0].textContent.trim().indexOf('No comments yet') !== -1) {
                        list.innerHTML = '';
                    }
                    var li = document.createElement('li');
                    li.className = 'flex gap-3 rounded-lg border border-slate-100 bg-slate-50/50 p-4 dark:border-slate-700 dark:bg-slate-800/30';
                    var initial = (c.username || 'L').toString().charAt(0).toUpperCase();
                    var name = (c.username || 'LUNTIAN').toString();
                    var created = (c.created_at || '').toString();
                    var avatar = document.createElement('span');
                    avatar.className = 'flex h-9 w-9 shrink-0 overflow-hidden rounded-full ring-2 ring-slate-200 dark:ring-slate-600';
                    avatar.setAttribute('aria-hidden', 'true');
                    if (c.profile_image_url) {
                        var img = document.createElement('img');
                        img.src = c.profile_image_url;
                        img.alt = '';
                        img.className = 'h-full w-full object-cover';
                        avatar.appendChild(img);
                    } else {
                        avatar.classList.add('items-center', 'justify-center', 'bg-slate-600', 'text-sm', 'font-semibold', 'text-white', 'dark:bg-slate-500');
                        avatar.textContent = initial;
                    }
                    var wrap = document.createElement('div');
                    wrap.className = 'min-w-0 flex-1';
                    wrap.innerHTML = '<p class="mb-1 text-sm font-medium text-slate-800 dark:text-slate-200"></p><div class="prose prose-sm max-w-none text-slate-700 dark:prose-invert dark:text-slate-300"></div><span class="mt-1 block text-xs text-slate-500 dark:text-slate-400"></span>';
                    wrap.querySelector('p').textContent = name;
                    wrap.querySelector('.prose').innerHTML = c.message || '';
                    wrap.querySelector('span').textContent = created;
                    li.appendChild(avatar);
                    li.appendChild(wrap);
                    list.insertBefore(li, list.firstChild);
                }
            }).catch(function() {
                sendBtn.disabled = false;
                if (window.showSuccessToast) showSuccessToast('Failed to add comment.');
            });
        });
    })();

    (function() {
        var uploadBtn = document.getElementById('jobViewAddFilesUploadBtn');
        var fileInput = document.getElementById('jobViewAddFilesInput');
        if (!uploadBtn || !fileInput) return;
        uploadBtn.addEventListener('click', function() {
            var files = fileInput.files;
            if (!files || files.length === 0) {
                if (window.showSuccessToast) showSuccessToast('Choose files first.');
                return;
            }
            var formData = new FormData();
            formData.append('_token', csrfToken);
            if (currentAddFilesMode === 'plans' || currentAddFilesMode === 'documents') {
                formData.append('section', currentAddFilesSection);
                for (var i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }
            } else if (currentAddFilesMode === 'checker') {
                for (var j = 0; j < files.length; j++) {
                    formData.append('files[]', files[j]);
                }
                var notesBody = document.querySelector('#jobViewModalCheckerNotes .job-view-modal-notes-body');
                if (notesBody && notesBody.innerHTML) {
                    formData.append('notes', notesBody.innerHTML);
                }
            } else {
                if (window.showSuccessToast) showSuccessToast('Please select a valid section.');
                return;
            }
            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';
            var targetUrl = currentAddFilesMode === 'checker' ? checkerUploadUrl : uploadFilesUrl;
            fetch(targetUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            }).then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: false, data: { message: 'Upload failed.' } }; }); }).then(function(result) {
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload';
                var msg = (result.data && result.data.message) || (result.ok ? (currentAddFilesMode === 'checker' ? 'Checker upload saved.' : 'Files added successfully.') : 'Upload failed.');
                if (window.showSuccessToast) showSuccessToast(msg);
                if (result.ok) {
                    closeAddFilesModal();
                    setTimeout(function() { window.location.reload(); }, 1500);
                }
            }).catch(function() {
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload';
                if (window.showSuccessToast) showSuccessToast('Upload failed.');
            });
        });
    })();
    document.addEventListener('click', function(e) {
        var closeEdit = e.target.closest('[data-job-view-close-edit]');
        var closeAdd = e.target.closest('[data-job-view-close-add]');
        if (closeEdit) { e.preventDefault(); e.stopPropagation(); closeEditModal(); }
        if (closeAdd) { e.preventDefault(); e.stopPropagation(); closeAddFilesModal(); }
    });
    if (editOverlay) editOverlay.addEventListener('click', function(e) { if (e.target === editOverlay) closeEditModal(); });
    if (addOverlay) addOverlay.addEventListener('click', function(e) { if (e.target === addOverlay) closeAddFilesModal(); });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeEditModal(); closeAddFilesModal(); }
    });
    function bindRichTextToolbar(containerSel, bodySel) {
        document.querySelectorAll(containerSel).forEach(function(container) {
            var editor = container.querySelector(bodySel);
            var btns = container.querySelectorAll('.job-view-comment-btn[data-cmd], [data-cmd]');
            function updateActiveState() {
                if (!editor || document.activeElement !== editor) return;
                btns.forEach(function(btn) {
                    var cmd = btn.getAttribute('data-cmd');
                    var active = cmd ? document.queryCommandState(cmd) : false;
                    btn.classList.toggle('active', !!active);
                });
            }
            btns.forEach(function(btn) {
                btn.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    var cmd = this.getAttribute('data-cmd');
                    if (!cmd || !editor) return;
                    editor.focus();
                    document.execCommand(cmd, false, null);
                    setTimeout(updateActiveState, 0);
                });
            });
            if (editor) {
                editor.addEventListener('focus', updateActiveState);
                editor.addEventListener('keyup', updateActiveState);
                editor.addEventListener('mouseup', updateActiveState);
                document.addEventListener('selectionchange', function() { if (document.activeElement === editor) updateActiveState(); });
            }
        });
    }
    bindRichTextToolbar('.job-view-comment-editor', '.job-view-comment-body');
    bindRichTextToolbar('.job-view-modal-notes-editor', '.job-view-modal-notes-body');
    bindRichTextToolbar('#jobViewEditFormNotes .rounded-xl.border', '#jobViewEditNotesBody');
    document.querySelectorAll('.job-view-comment-body, .job-view-modal-notes-body').forEach(function(body) {
        body.addEventListener('paste', function(e) { e.preventDefault(); var t = e.clipboardData.getData('text/plain'); document.execCommand('insertText', false, t); });
    });
    function closeAllLbsMenus() {
        document.querySelectorAll('.lbs-status-menu').forEach(function(m) { m.hidden = true; });
        document.querySelectorAll('.lbs-initials-menu').forEach(function(m) { m.hidden = true; });
        document.querySelectorAll('[data-status-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
        document.querySelectorAll('[data-initials-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
    }
    document.querySelectorAll('[data-initials-wrap]').forEach(function(wrap) {
        var trigger = wrap.querySelector('[data-initials-trigger]');
        var menu = wrap.querySelector('.lbs-initials-menu');
        var role = wrap.getAttribute('data-role');
        if (!trigger || !menu) return;
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!menu.hidden) {
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                return;
            }
            closeAllLbsMenus();
            var rect = this.getBoundingClientRect();
            menu.style.cssText = 'position:fixed;top:' + (rect.bottom + 4) + 'px;left:' + rect.left + 'px;min-width:' + Math.max(rect.width, 70) + 'px;';
            menu.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
        });
        menu.querySelectorAll('.lbs-initials-option').forEach(function(opt) {
            opt.addEventListener('click', function(e) {
                e.stopPropagation();
                var val = this.getAttribute('data-value');
                if (!val) return;
                trigger.textContent = val;
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                var formData = new URLSearchParams();
                formData.append('_token', csrfToken);
                if (role === 'staff') formData.append('staff_id', val); else formData.append('checker_id', val);
                fetch(updateUrl, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                }).then(function(r) {
                    return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: r.ok, data: {} }; });
                }).then(function(result) {
                    var msg = (result.data && result.data.message) || (result.ok ? 'Staff/Checker updated successfully.' : 'Something went wrong.');
                    if (window.showSuccessToast) showSuccessToast(msg);
                    if (result.ok) setTimeout(function() { window.location.reload(); }, 2500);
                }).catch(function() {
                    if (window.showSuccessToast) showSuccessToast('Failed to update.');
                });
            });
        });
    });
    document.querySelectorAll('[data-status-wrap]').forEach(function(wrap) {
        var trigger = wrap.querySelector('[data-status-trigger]');
        var menu = wrap.querySelector('.lbs-status-menu');
        if (!trigger || !menu) return;
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!menu.hidden) {
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                return;
            }
            closeAllLbsMenus();
            var rect = this.getBoundingClientRect();
            menu.style.cssText = 'position:fixed;top:' + (rect.bottom + 4) + 'px;left:' + rect.left + 'px;min-width:' + Math.max(rect.width, 90) + 'px;';
            menu.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
        });
        menu.querySelectorAll('.lbs-status-option').forEach(function(opt) {
            opt.addEventListener('click', function(e) {
                e.stopPropagation();
                var val = this.getAttribute('data-status-value');
                if (!val) return;
                var badgeClass = 'lbs-badge-' + String(val).toLowerCase().replace(/\s+/g, '-');
                ['lbs-badge-pending', 'lbs-badge-accepted', 'lbs-badge-allocated', 'lbs-badge-awaiting-further-information', 'lbs-badge-completed', 'lbs-badge-for-email-confirmation', 'lbs-badge-processing', 'lbs-badge-for-checking', 'lbs-badge-for-review', 'lbs-badge-revised'].forEach(function(c) { trigger.classList.remove(c); });
                trigger.classList.add(badgeClass, 'lbs-status-updating');
                trigger.textContent = val;
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                var formData = new URLSearchParams();
                formData.append('_token', csrfToken);
                formData.append('job_status', val);
                fetch(updateUrl, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                }).then(function(r) {
                    return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: r.ok, data: {} }; });
                }).then(function(result) {
                    trigger.classList.remove('lbs-status-updating');
                    if (result.ok) trigger.classList.add('lbs-status-success');
                    var msg = (result.data && result.data.message) || (result.ok ? 'Status updated successfully.' : 'Something went wrong.');
                    if (window.showSuccessToast) showSuccessToast(msg);
                    if (result.ok) setTimeout(function() { trigger.classList.remove('lbs-status-success'); window.location.reload(); }, 2500);
                }).catch(function() {
                    trigger.classList.remove('lbs-status-updating');
                    if (window.showSuccessToast) showSuccessToast('Failed to update status.');
                });
            });
        });
    });
    document.addEventListener('click', closeAllLbsMenus);

    (function deleteFileModal() {
        var overlay = document.getElementById('jobViewDeleteFileModalOverlay');
        var confirmBlock = document.getElementById('jobViewDeleteFileConfirm');
        var countdownBlock = document.getElementById('jobViewDeleteFileCountdown');
        var countdownNumber = document.getElementById('jobViewDeleteFileCountdownNumber');
        var cancelBtn = document.getElementById('jobViewDeleteFileModalCancel');
        var confirmBtn = document.getElementById('jobViewDeleteFileModalConfirm');
        var btnTextEl = confirmBtn && confirmBtn.querySelector('.job-view-delete-btn-text');
        var countdownTimer = null;
        var pendingDelete = null;

        function resetDeleteFileModal() {
            if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
            if (confirmBlock) confirmBlock.hidden = false;
            if (countdownBlock) countdownBlock.hidden = true;
            if (confirmBtn) confirmBtn.disabled = false;
            if (btnTextEl) btnTextEl.textContent = 'Delete';
        }
        function closeDeleteFileModal() {
            if (overlay) overlay.classList.remove('is-open');
            overlay && overlay.setAttribute('aria-hidden', 'true');
            pendingDelete = null;
            resetDeleteFileModal();
        }

        document.addEventListener('click', function(e) {
            var delBtn = e.target.closest('.job-view-file-btn-delete');
            if (delBtn) {
                e.preventDefault();
                var section = delBtn.getAttribute('data-job-file-type');
                var fileName = delBtn.getAttribute('data-job-file-name');
                var listItem = delBtn.closest('.job-view-file-item');
                if (!section || !fileName) return;
                pendingDelete = { section: section, fileName: fileName, listItem: listItem };
                resetDeleteFileModal();
                if (overlay) { overlay.classList.add('is-open'); overlay.setAttribute('aria-hidden', 'false'); }
                return;
            }
            var modalDelBtn = e.target.closest('[data-job-view-modal-delete-file]');
            if (modalDelBtn) {
                var item = modalDelBtn.closest('.job-view-modal-file-item');
                if (!item) return;
                item.remove();
                var list = document.getElementById('jobViewModalExistingFiles');
                var noFiles = document.getElementById('jobViewModalNoFiles');
                if (list && list.children.length === 0 && noFiles) { list.hidden = true; noFiles.hidden = false; }
            }
        });

        if (cancelBtn) cancelBtn.addEventListener('click', closeDeleteFileModal);
        if (overlay) overlay.addEventListener('click', function(e) { if (e.target === overlay) closeDeleteFileModal(); });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay && overlay.classList.contains('is-open')) closeDeleteFileModal();
        });

        if (confirmBtn && confirmBlock && countdownBlock && countdownNumber) {
            confirmBtn.addEventListener('click', function() {
                if (!pendingDelete || countdownTimer) return;
                confirmBlock.hidden = true;
                countdownBlock.hidden = false;
                confirmBtn.disabled = true;
                if (btnTextEl) btnTextEl.textContent = 'Deleting...';
                var count = 3;
                countdownNumber.textContent = count;
                countdownNumber.style.animation = 'none';
                countdownNumber.offsetHeight;
                countdownNumber.style.animation = '';
                countdownTimer = setInterval(function() {
                    count--;
                    if (count <= 0) {
                        clearInterval(countdownTimer);
                        countdownTimer = null;
                        var section = pendingDelete.section;
                        var fileName = pendingDelete.fileName;
                        var listItem = pendingDelete.listItem;
                        var body = JSON.stringify({ _token: csrfToken, section: section, file_name: fileName });
                        fetch(deleteFileUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                            body: body
                        }).then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: false, data: {} }; });                         }).then(function(result) {
                            var msg = (result.data && result.data.message) || (result.ok ? 'File removed.' : 'Failed to remove file.');
                            if (window.showSuccessToast) showSuccessToast(msg);
                            if (result.ok) {
                                if (listItem) listItem.remove();
                                if (section === 'plans' && jobViewFilesData.planFiles) {
                                    jobViewFilesData.planFiles = (jobViewFilesData.planFiles || []).filter(function(n) { return n !== fileName; });
                                } else if (section === 'documents' && jobViewFilesData.docFiles) {
                                    jobViewFilesData.docFiles = (jobViewFilesData.docFiles || []).filter(function(n) { return n !== fileName; });
                                }
                                var log = result.data && result.data.log;
                                if (log) {
                                    var list = document.querySelector('.job-view-activity');
                                    if (list) {
                                        if (list.children.length === 1 && list.children[0].querySelector('.job-view-activity-text')) list.innerHTML = '';
                                        var li = document.createElement('li');
                                        li.className = 'job-view-activity-item flex gap-3 rounded-lg border border-slate-100 bg-slate-50/50 p-3 dark:border-slate-700 dark:bg-slate-800/30';
                                        var dateText = log.activity_date || '';
                                        li.innerHTML = '<div class="job-view-activity-user flex shrink-0 flex-col items-center gap-1">' + getJobViewActivityAvatarHtml(log) + '<span class="job-view-activity-name text-xs font-medium text-slate-600 dark:text-slate-400">' + (log.updated_by || 'LUNTIAN') + '</span></div><div class="job-view-activity-content min-w-0 flex-1"><span class="job-view-activity-time block text-xs text-slate-500 dark:text-slate-400">' + dateText + '</span><p class="job-view-activity-label mt-0.5 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">' + (log.activity_type || 'Update') + '</p>' + (log.activity_description ? '<div class="job-view-activity-text prose prose-sm mt-1 max-w-none text-slate-700 dark:prose-invert dark:text-slate-300">' + log.activity_description + '</div>' : '') + '</div>';
                                        list.insertBefore(li, list.firstChild);
                                    }
                                }
                            }
                            closeDeleteFileModal();
                        }).catch(function() {
                            if (window.showSuccessToast) showSuccessToast('Failed to remove file.');
                            closeDeleteFileModal();
                        });
                        return;
                    }
                    countdownNumber.textContent = count;
                    countdownNumber.style.animation = 'none';
                    countdownNumber.offsetHeight;
                    countdownNumber.style.animation = '';
                }, 1000);
            });
        }
    })();

    (function archiveJobModal() {
        var archiveBtn = document.getElementById('jobViewArchiveJobBtn');
        var overlay = document.getElementById('jobViewArchiveJobModalOverlay');
        var confirmBlock = document.getElementById('jobViewArchiveJobConfirm');
        var countdownBlock = document.getElementById('jobViewArchiveJobCountdown');
        var countdownNumber = document.getElementById('jobViewArchiveJobCountdownNumber');
        var cancelBtn = document.getElementById('jobViewArchiveJobModalCancel');
        var confirmBtn = document.getElementById('jobViewArchiveJobModalConfirm');
        var btnTextEl = confirmBtn && confirmBtn.querySelector('.job-view-archive-btn-text');
        var countdownTimer = null;

        function resetArchiveModal() {
            if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
            if (confirmBlock) confirmBlock.hidden = false;
            if (countdownBlock) countdownBlock.hidden = true;
            if (confirmBtn) confirmBtn.disabled = false;
            if (btnTextEl) btnTextEl.textContent = 'Archive';
        }
        function closeArchiveModal() {
            if (overlay) { overlay.classList.remove('is-open'); overlay.setAttribute('aria-hidden', 'true'); }
            resetArchiveModal();
        }

        if (archiveBtn) {
            archiveBtn.addEventListener('click', function() {
                resetArchiveModal();
                if (overlay) { overlay.classList.add('is-open'); overlay.setAttribute('aria-hidden', 'false'); }
            });
        }
        if (cancelBtn) cancelBtn.addEventListener('click', closeArchiveModal);
        if (overlay) overlay.addEventListener('click', function(e) { if (e.target === overlay) closeArchiveModal(); });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay && overlay.classList.contains('is-open')) closeArchiveModal();
        });

        if (confirmBtn && confirmBlock && countdownBlock && countdownNumber) {
            confirmBtn.addEventListener('click', function() {
                if (countdownTimer) return;
                confirmBlock.hidden = true;
                countdownBlock.hidden = false;
                confirmBtn.disabled = true;
                if (btnTextEl) btnTextEl.textContent = 'Archiving...';
                var count = 3;
                countdownNumber.textContent = count;
                countdownNumber.style.animation = 'none';
                countdownNumber.offsetHeight;
                countdownNumber.style.animation = '';
                countdownTimer = setInterval(function() {
                    count--;
                    if (count <= 0) {
                        clearInterval(countdownTimer);
                        countdownTimer = null;
                        var formData = new URLSearchParams();
                        formData.append('_token', csrfToken);
                        fetch(archiveJobUrl, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: formData.toString()
                        }).then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: false, data: {} }; }); }).then(function(result) {
                            var msg = (result.data && result.data.message) || (result.ok ? 'Job archived.' : 'Failed to archive.');
                            if (window.showSuccessToast) showSuccessToast(msg);
                            var redirect = (result.data && result.data.redirect) || '{{ route('lbs.trash') }}';
                            if (result.ok && redirect) { window.location.href = redirect; return; }
                            closeArchiveModal();
                        }).catch(function() {
                            if (window.showSuccessToast) showSuccessToast('Failed to archive.');
                            closeArchiveModal();
                        });
                        return;
                    }
                    countdownNumber.textContent = count;
                    countdownNumber.style.animation = 'none';
                    countdownNumber.offsetHeight;
                    countdownNumber.style.animation = '';
                }, 1000);
            });
        }
    })();

    function showInlineToast(message) {
        var existing = document.getElementById('jobViewInlineToast');
        if (existing) existing.remove();
        var el = document.createElement('div');
        el.id = 'jobViewInlineToast';
        el.className = 'job-view-inline-toast';
        el.textContent = message;
        document.body.appendChild(el);
        setTimeout(function() {
            el.classList.add('hide');
            setTimeout(function() { el.remove(); }, 350);
        }, 3200);
    }

    // Save handler for Edit modal
    var saveBtn = document.getElementById('jobViewEditSaveBtn');
    if (saveBtn) {
        var saveBtnOriginalHtml = saveBtn.innerHTML;
        saveBtn.addEventListener('click', function () {
            var payload = {};
            if (!editOverlay) return;
            // Determine which form is visible
            if (!formClient.hidden) {
                payload.client_reference = document.getElementById('edit-client-ref')?.value || '';
                payload.job_reference_no = document.getElementById('edit-job-number')?.value || '';
                payload.compliance = document.getElementById('edit-compliance')?.value || '';
                var clientSelect = $('#edit-client-name');
                var clientId = clientSelect.val();
                var clientName = clientSelect.find('option:selected').data('name') || '';
                payload.client_id = clientId || '';
                payload.client_name = clientName;
            } else if (!formJob.hidden) {
                payload.job_status = $('#edit-job-status').val();
                payload.job_address = document.getElementById('edit-job-address')?.value || '';
                payload.priority = $('#edit-priority').val();
                payload.job_type = document.getElementById('edit-job-type')?.value || '';
            } else if (!formNotes.hidden) {
                var notesBody = document.getElementById('jobViewEditNotesBody');
                payload.notes = notesBody ? notesBody.innerHTML : '';
            } else {
                return;
            }

            // loading state + simple animation
            saveBtn.disabled = true;
            saveBtn.classList.add('job-view-modal-btn-loading');
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

            $.ajax({
                url: updateUrl,
                method: 'PUT',
                data: Object.assign({_token: csrfToken}, payload),
                success: function (res) {
                    var msg = (res && res.message) || 'Job updated successfully.';
                    if (window.showSuccessToast) showSuccessToast(msg);

                    // Realtime append of new activity logs (if any)
                    if (res && Array.isArray(res.logs) && res.logs.length > 0) {
                        var list = document.querySelector('.job-view-activity');
                        if (list) {
                            // Remove "no activity" placeholder if present
                            if (list.children.length === 1 && list.children[0].querySelector('.job-view-activity-text')) {
                                list.innerHTML = '';
                            }
                            res.logs.forEach(function(log) {
                                var li = document.createElement('li');
                                li.className = 'job-view-activity-item flex gap-3 rounded-lg border border-slate-100 bg-slate-50/50 p-3 dark:border-slate-700 dark:bg-slate-800/30';
                                var dateText = log.activity_date || '';
                                li.innerHTML =
                                    '<div class="job-view-activity-user flex shrink-0 flex-col items-center gap-1">' +
                                        getJobViewActivityAvatarHtml(log) +
                                        '<span class="job-view-activity-name text-xs font-medium text-slate-600 dark:text-slate-400">' + (log.updated_by || 'LUNTIAN') + '</span>' +
                                    '</div>' +
                                    '<div class="job-view-activity-content min-w-0 flex-1">' +
                                        '<span class="job-view-activity-time block text-xs text-slate-500 dark:text-slate-400">' + dateText + '</span>' +
                                        '<p class="job-view-activity-label mt-0.5 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">' + (log.activity_type || 'Update') + '</p>' +
                                        (log.activity_description ? '<div class="job-view-activity-text prose prose-sm mt-1 max-w-none text-slate-700 dark:prose-invert dark:text-slate-300">' + log.activity_description + '</div>' : '') +
                                    '</div>';
                                list.insertBefore(li, list.firstChild);
                            });
                        }
                    }

                    // small loading animation on affected cards, then reload
                    var clientCard = document.getElementById('jobClientCard');
                    var activityCard = document.getElementById('jobActivityCard');
                    if (clientCard) clientCard.classList.add('job-view-card-reloading');
                    if (activityCard) activityCard.classList.add('job-view-card-reloading');

                    closeEditModal();

                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                },
                error: function (xhr) {
                    var msg = 'Failed to save changes. Please try again.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (window.showSuccessToast) showSuccessToast(msg);
                },
                complete: function () {
                    saveBtn.disabled = false;
                    saveBtn.classList.remove('job-view-modal-btn-loading');
                    saveBtn.innerHTML = saveBtnOriginalHtml;
                }
            });
        });
    }
})();
</script>
    <script>
    $(function() {
        $('.job-view-modal .select2-single').select2({ width: '100%', allowClear: false });
    });
    </script>
@endpush
