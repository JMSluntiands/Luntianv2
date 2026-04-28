@extends('layouts.dashboard')

@section('title', 'LBS List')

@section('body_class', 'page-lbs-list')

@section('content')
    @php
        $filterBuilderOptions = collect($filterBuilders ?? []);
        $filterPriorityOptions = collect($filterPriorities ?? []);
    @endphp
    <div class="flex max-w-full flex-col pb-0">
        <div class="mb-5">
            <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">LBS List</h1>
            <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View and manage all LBS jobs.</p>
        </div>

        <div class="mb-6 rounded-xl border border-slate-200 bg-white/80 p-3 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                <div>
                    <label for="lbsSearch" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Search</label>
                    <div class="relative flex items-center">
                        <svg class="pointer-events-none absolute left-3 text-slate-500 dark:text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <input type="search" id="lbsSearch" class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3.5 text-sm text-slate-900 placeholder-slate-500 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:placeholder-slate-500 dark:focus:border-blue-700 dark:focus:ring-blue-700/25" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search LBS jobs">
                    </div>
                </div>
                <div>
                    <label for="lbsFilterDate" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Date Logged</label>
                    <input type="date" id="lbsFilterDate" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-blue-700 dark:focus:ring-blue-700/25">
                </div>
                <div>
                    <label for="lbsFilterBuilder" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Builder</label>
                    <select id="lbsFilterBuilder" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-blue-700 dark:focus:ring-blue-700/25">
                        <option value="">All</option>
                        @foreach($filterBuilderOptions as $builderName)
                            <option value="{{ $builderName }}">{{ $builderName }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="lbsFilterPriority" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Priority</label>
                    <select id="lbsFilterPriority" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-blue-700 dark:focus:ring-blue-700/25">
                        <option value="">All</option>
                        @foreach($filterPriorityOptions as $priorityOption)
                            <option value="{{ $priorityOption }}">{{ $priorityOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-transparent select-none">Reset</label>
                    <button type="button" id="lbsFilterReset" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">Reset</button>
                </div>
            </div>
        </div>

        <div class="mt-1 max-w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
            <div class="max-w-full overflow-x-auto">
                <table class="lbs-table w-full min-w-[1320px] table-fixed border-collapse text-sm" id="lbsTable">
                    <colgroup>
                        <col style="width: 110px">
                        <col style="width: 140px">
                        <col style="width: 260px">
                        <col style="width: 105px">
                        <col style="width: 260px">
                        <col style="width: 150px">
                        <col style="width: 70px">
                        <col style="width: 70px">
                        <col style="width: 200px">
                        <col style="width: 155px">
                        <col style="width: 115px">
                        <col style="width: 95px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="lbs-th-action cursor-default border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400" data-sort="">
                                <span>Action</span>
                            </th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort="">
                                <span>Log Date</span>
                                <span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort="">
                                <span>Client</span>
                                <span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort="">
                                <span>Reference</span>
                                <span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort="">
                                <span>Job Type</span>
                                <span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort="">
                                <span>Priority</span>
                                <span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort="">
                                <span>Staff</span>
                                <span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort="">
                                <span>Checker</span>
                                <span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort="">
                                <span>Status</span>
                                <span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort="">
                                <span>Due Date</span>
                                <span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort="">
                                <span>Completion Date</span>
                                <span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span>
                            </th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort="">
                                <span>Complexity</span>
                                <span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs ?? [] as $index => $job)
                            @php
                                $log = $job->log_date ? \Carbon\Carbon::parse($job->log_date, 'Asia/Manila') : null;
                                $logDate1 = $log ? $log->format('F j, Y') : '—';
                                $logDate2 = $log ? $log->format('g:i A') : '';

                                $priorityText = $job->priority ?? '';
                                $priorityLower = strtolower($priorityText);

                                $due = null;
                                if ($log) {
                                    $start = $log->copy();
                                    $startOfDay = $start->copy()->setTime(8, 0, 0);
                                    $cutoff = $start->copy()->setTime(15, 0, 0);

                                    if ($start->lt($startOfDay)) {
                                        $start = $startOfDay;
                                    }

                                    $isTop = str_contains($priorityLower, 'top');
                                    if (!$isTop && $start->gt($cutoff)) {
                                        $start = $start->copy()->addDay()->setTime(8, 0, 0);
                                    }

                                    if ($isTop) {
                                        $due = $start->copy()->addHours(6);
                                    } else {
                                        $days = 0;
                                        if (preg_match('/(\d+)\s*day/', $priorityLower, $m)) {
                                            $days = (int) ($m[1] ?? 0);
                                        }
                                        if ($days > 0) {
                                            $due = $start->copy()->addDays($days);
                                        }
                                    }
                                }

                                $completion = $job->completion_date ? \Carbon\Carbon::parse($job->completion_date, 'Asia/Manila') : null;
                                $isOverdue = $due && !$completion && $due->lt(now('Asia/Manila'));

                                $dueDate1 = $due ? $due->format('F j, Y') : '—';
                                $dueDate2 = $due ? $due->format('g:i A') : '';
                                $completionDate1 = $completion ? $completion->format('F j, Y') : '—';
                                $completionDate2 = $completion ? $completion->format('g:i A') : '';
                                $completionText = $completion ? $completionDate1 . ' ' . $completionDate2 : '—';
                                $logDateFilter = $log ? $log->format('Y-m-d') : '';
                                $builderFilter = trim((string) ($job->client_account_name ?? ''));
                                $priorityFilter = trim((string) ($priorityText ?? ''));

                                $priorityBg = $priorityColors[$priorityText] ?? null;

                                $status = $job->job_status ?? 'Allocated';
                                $statusBg  = $statusColors[$status] ?? null;
                                $statusLower = strtolower($status);
                                $canEditStatus = in_array($statusLower, ['allocated', 'accepted', 'processing', 'revised', 'for checking', 'for review'], true);
                                $statusOptions = [];
                                if ($statusLower === 'allocated') {
                                    foreach ($statuses ?? [] as $s) {
                                        $n = strtolower((string)($s->name ?? ''));
                                        if (in_array($n, ['accepted', 'processing'], true)) $statusOptions[] = $s->name;
                                    }
                                } elseif (in_array($statusLower, ['accepted', 'processing', 'revised'], true)) {
                                    foreach ($statuses ?? [] as $s) {
                                        if (strtolower((string)($s->name ?? '')) === 'for checking') $statusOptions[] = $s->name;
                                    }
                                } elseif ($statusLower === 'for checking') {
                                    foreach ($statuses ?? [] as $s) {
                                        $n = strtolower((string)($s->name ?? ''));
                                        if (in_array($n, ['for review', 'revised'], true)) $statusOptions[] = $s->name;
                                    }
                                } elseif ($statusLower === 'for review') {
                                    foreach ($statuses ?? [] as $s) {
                                        $n = strtolower((string)($s->name ?? ''));
                                        if (in_array($n, ['for email confirmation', 'cancelled', 'revised', 'for checking'], true)) $statusOptions[] = $s->name;
                                    }
                                } else {
                                    foreach ($statuses ?? [] as $s) { $statusOptions[] = $s->name; }
                                }

                                $complexity = is_numeric($job->plan_complexity ?? null) ? (int) $job->plan_complexity : 0;
                                $complexity = max(0, min(5, $complexity));
                            @endphp
                            <tr class="lbs-data-row border-b border-slate-200 overflow-hidden align-middle text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5" data-job-id="{{ $job->job_id }}" data-job-units="{{ (int) ($job->units ?? 0) }}" data-update-url="{{ route('lbs.job.update', ['id' => $job->job_id]) }}" data-log-date-key="{{ $logDateFilter }}" data-builder="{{ $builderFilter }}" data-priority="{{ $priorityFilter }}">
                                <td class="overflow-visible px-4 py-3 text-center align-middle text-slate-800 dark:text-slate-200" style="white-space: nowrap;">
                                    <div class="relative z-10 flex flex-nowrap items-center gap-1.5">
                                        <a href="{{ route('lbs.add', ['duplicate' => $job->job_id]) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-blue-900/25 hover:text-blue-300 dark:text-slate-400 dark:hover:bg-blue-900/25 dark:hover:text-blue-300" title="Duplicate" aria-label="Duplicate job to Add New form">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        </a>
                                        <a href="{{ route('lbs.job.view', ['id' => $job->job_id]) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-green-500/15 hover:text-green-400 dark:text-slate-400 dark:hover:bg-green-500/15 dark:hover:text-green-400" title="View" aria-label="View job {{ $job->job_reference_no }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        <button type="button" class="lbs-action-icon lbs-action-expand inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 transition-colors hover:bg-amber-500/15 hover:text-amber-400 dark:text-slate-400 dark:hover:bg-amber-500/15 dark:hover:text-amber-400" title="View full row details below" aria-label="Show full row details" aria-expanded="false" data-expand-row>
                                            <svg class="lbs-expand-icon block transition-transform duration-200" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="lbs-td lbs-td-log-date border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Log Date" data-sort="{{ $job->log_date }}">
                                    <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $logDate1 }}</span>
                                    @if($logDate2)<span class="block text-[0.8125rem] text-slate-400">{{ $logDate2 }}</span>@endif
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client">
                                    <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $job->client_account_name ?? $job->client_code ?? '—' }}</span>
                                    <span class="block text-[0.8125rem] text-slate-400">{{ $job->ncc_compliance ?? '' }}</span>
                                </td>
                                @php $tableReference = $job->job_reference_no ?? $job->reference ?? '—'; @endphp
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Reference" data-sort="{{ $tableReference }}" style="white-space: nowrap;">{{ $tableReference }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Job Type">
                                    <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $job->job_type }}</span>
                                    <span class="block text-[0.8125rem] text-slate-400">{{ $job->job_request_id }}</span>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Priority" style="white-space: nowrap;">
                                    <span
                                        class="lbs-priority inline-block whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold"
                                        @if($priorityBg)
                                            style="background-color: {{ $priorityBg }};"
                                        @endif
                                    >
                                        {{ $priorityText }}
                                    </span>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Staff" style="white-space: nowrap;">
                                    <div class="lbs-initials-wrap relative inline-block" data-initials-wrap data-role="staff">
                                        <button type="button" class="lbs-initials lbs-initials-trigger inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200" data-initials-trigger aria-haspopup="true" aria-expanded="false">{{ $job->staff_id ? strtoupper($job->staff_id) : '--' }}</button>
                                        <div class="lbs-initials-menu fixed z-[9999] flex min-w-[70px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu" hidden>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="SB">SB</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="GM">GM</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="PEP">PEP</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="JDR">JDR</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="JS">JS</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Checker" style="white-space: nowrap;">
                                    <div class="lbs-initials-wrap relative inline-block" data-initials-wrap data-role="checker">
                                        <button type="button" class="lbs-initials lbs-initials-trigger inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200" data-initials-trigger aria-haspopup="true" aria-expanded="false">{{ $job->checker_id ? strtoupper($job->checker_id) : '--' }}</button>
                                        <div class="lbs-initials-menu fixed z-[9999] flex min-w-[70px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu" hidden>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="SB">SB</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="GM">GM</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="PEP">PEP</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="JDR">JDR</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="JS">JS</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Status" style="white-space: nowrap;">
                                    @if($canEditStatus && count($statusOptions) > 0)
                                        <div class="lbs-status-wrap relative inline-block" data-status-wrap>
                                            <button
                                                type="button"
                                                class="lbs-badge lbs-status-trigger inline-block rounded-md border-0 px-2 py-1 text-xs font-semibold leading-tight cursor-pointer hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-600/40 focus:ring-offset-0 dark:focus:ring-blue-500/40"
                                                @if($statusBg)
                                                    style="background-color: {{ $statusBg }};"
                                                @endif
                                                data-status-trigger
                                                aria-haspopup="true"
                                                aria-expanded="false"
                                                data-reference="{{ $job->job_reference_no }}"
                                            >
                                                {{ $status }}
                                            </button>
                                            <div class="lbs-status-menu fixed z-[9999] flex min-w-[90px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu" hidden>
                                                @foreach($statusOptions as $opt)
                                                    <button type="button" role="menuitem" class="lbs-status-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-status-value="{{ $opt }}">{{ $opt }}</button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span
                                            class="lbs-badge lbs-status-badge-readonly inline-block cursor-default rounded-md px-2 py-1 text-xs font-semibold opacity-95"
                                            @if($statusBg)
                                                style="background-color: {{ $statusBg }};"
                                            @endif
                                            aria-disabled="true"
                                        >
                                            {{ $status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="lbs-td lbs-td-due border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Due Date" data-sort="{{ $due ? $due->format('Y-m-d H:i:s') : '' }}" data-overdue="{{ $isOverdue ? '1' : '0' }}">
                                    <span class="block font-medium text-slate-800 dark:text-slate-200 {{ $isOverdue ? 'text-red-400 dark:text-red-400' : '' }}">{{ $dueDate1 }}</span>
                                    @if($dueDate2)
                                        <span class="block text-[0.8125rem] text-slate-400">{{ $dueDate2 }}</span>
                                        @if($isOverdue)
                                            <span class="block text-[0.8125rem] font-medium text-red-400 mt-0.5">(Overdue)</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Completion Date">
                                    <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $completionDate1 }}</span>
                                    @if($completionDate2)
                                        <span class="block text-[0.8125rem] text-slate-400">{{ $completionDate2 }}</span>
                                    @endif
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Complexity" data-sort="{{ $complexity }}" style="white-space: nowrap;">
                                    <span class="lbs-stars inline-flex items-center" data-rating="{{ $complexity }}" aria-label="{{ $complexity }} out of 5">
                                        @include('lbs.partials.stars', ['rating' => $complexity])
                                    </span>
                                </td>
                            </tr>
                            <tr class="lbs-row-detail border-b border-slate-200 dark:border-slate-700" id="lbs-detail-{{ $index }}" hidden>
                                <td colspan="12" class="bg-slate-50 p-0 align-top dark:bg-slate-900">
                                    <div class="px-0 py-0">
                                        <div class="grid gap-x-6 gap-y-4 px-5 py-5" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                                            <div class="flex flex-col gap-1.5">
                                                <span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Log Date</span>
                                                <span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $logDate1 }} {{ $logDate2 }}</span>
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Client</span>
                                                <span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $job->client_account_name ?? $job->client_code ?? '—' }} @if($job->ncc_compliance) · {{ $job->ncc_compliance }} @endif</span>
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Reference</span>
                                                <span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $tableReference }}</span>
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Job Type</span>
                                                <span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $job->job_type }} @if($job->job_request_id) · {{ $job->job_request_id }} @endif</span>
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Priority</span>
                                                <span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">
                                                    <span
                                                        class="lbs-priority inline-block whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold mt-0.5"
                                                        @if($priorityBg)
                                                            style="background-color: {{ $priorityBg }};"
                                                        @endif
                                                    >
                                                        {{ $priorityText }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Staff</span>
                                                <span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="lbs-initials lbs-detail-staff-badge inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 mt-0.5 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">{{ $job->staff_id ? strtoupper($job->staff_id) : '--' }}</span></span>
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Checker</span>
                                                <span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="lbs-initials lbs-detail-checker-badge inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 mt-0.5 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">{{ $job->checker_id ? strtoupper($job->checker_id) : '--' }}</span></span>
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Status</span>
                                                <span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">
                                                    <span
                                                        class="lbs-detail-status-badge lbs-badge inline-block rounded-md px-2 py-1 text-xs font-semibold mt-0.5"
                                                        @if($statusBg)
                                                            style="background-color: {{ $statusBg }};"
                                                        @endif
                                                    >
                                                        {{ $status }}
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Due Date</span>
                                                <span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">
                                                    {{ $dueDate1 }} {{ $dueDate2 }}
                                                    @if($isOverdue)
                                                        <br>
                                                        <span class="text-red-400">(Overdue)</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Completion Date</span>
                                                <span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $completionText }}</span>
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Complexity</span>
                                                <span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">@include('lbs.partials.stars', ['rating' => $complexity])</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="border-b border-slate-200 px-4 py-3 text-center text-slate-400 dark:border-slate-700 dark:text-slate-400" colspan="12">
                                    No LBS jobs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-7">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Forms Submitted Jobs</h2>
                <span class="inline-flex items-center rounded-md bg-cyan-500/15 px-2.5 py-1 text-xs font-semibold text-cyan-300">Source: forms.luntian.com.au</span>
            </div>
            <div class="max-w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
                <div class="max-w-full overflow-x-auto">
                    <table class="lbs-table w-full min-w-[1320px] table-fixed border-collapse text-sm">
                        <colgroup>
                            <col style="width: 110px">
                            <col style="width: 140px">
                            <col style="width: 260px">
                            <col style="width: 105px">
                            <col style="width: 260px">
                            <col style="width: 140px">
                            <col style="width: 70px">
                            <col style="width: 70px">
                            <col style="width: 200px">
                            <col style="width: 155px">
                            <col style="width: 115px">
                            <col style="width: 120px">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="border-b border-slate-200 bg-slate-100 px-4 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Action</th>
                                <th class="border-b border-slate-200 bg-slate-100 px-4 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Log Date</th>
                                <th class="border-b border-slate-200 bg-slate-100 px-4 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Client</th>
                                <th class="border-b border-slate-200 bg-slate-100 px-4 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Reference</th>
                                <th class="border-b border-slate-200 bg-slate-100 px-4 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Job Type</th>
                                <th class="border-b border-slate-200 bg-slate-100 px-4 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Priority</th>
                                <th class="border-b border-slate-200 bg-slate-100 px-4 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Staff</th>
                                <th class="border-b border-slate-200 bg-slate-100 px-4 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Checker</th>
                                <th class="border-b border-slate-200 bg-slate-100 px-4 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Status</th>
                                <th class="border-b border-slate-200 bg-slate-100 px-4 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Due Date</th>
                                <th class="border-b border-slate-200 bg-slate-100 px-4 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Completion Date</th>
                                <th class="border-b border-slate-200 bg-slate-100 px-4 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Source</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($formsJobs ?? [] as $formJob)
                                @php
                                    $formLog = $formJob->log_date ? \Carbon\Carbon::parse($formJob->log_date, 'Asia/Manila') : null;
                                    $priorityText = $formJob->priority ?? '';
                                    $priorityBg = $priorityColors[$priorityText] ?? null;
                                    $status = $formJob->job_status ?? 'Allocated';
                                    $statusBg = $statusColors[$status] ?? null;
                                    $due = null;
                                    if ($formLog) {
                                        $start = $formLog->copy();
                                        $startOfDay = $start->copy()->setTime(8, 0, 0);
                                        $cutoff = $start->copy()->setTime(15, 0, 0);
                                        if ($start->lt($startOfDay)) {
                                            $start = $startOfDay;
                                        }
                                        $isTop = str_contains(strtolower($priorityText), 'top');
                                        if (!$isTop && $start->gt($cutoff)) {
                                            $start = $start->copy()->addDay()->setTime(8, 0, 0);
                                        }
                                        if ($isTop) {
                                            $due = $start->copy()->addHours(6);
                                        } elseif (preg_match('/(\d+)\s*day/', strtolower($priorityText), $m)) {
                                            $due = $start->copy()->addDays((int) ($m[1] ?? 0));
                                        }
                                    }
                                    $completion = $formJob->completion_date ? \Carbon\Carbon::parse($formJob->completion_date, 'Asia/Manila') : null;
                                @endphp
                                <tr class="border-b border-slate-200 text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5">
                                    <td class="px-4 py-3">
                                        <div class="relative z-10 flex flex-nowrap items-center gap-1.5">
                                            <form method="POST" action="{{ route('lbs.job.acceptForm', ['id' => $formJob->job_id]) }}">
                                                @csrf
                                                <button type="submit" class="rounded-md bg-emerald-600 px-2 py-1 text-xs font-semibold text-white transition-colors hover:bg-emerald-500">Accept</button>
                                            </form>
                                            <a href="{{ route('lbs.job.view', ['id' => $formJob->job_id]) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-green-500/15 hover:text-green-400 dark:text-slate-400 dark:hover:bg-green-500/15 dark:hover:text-green-400" title="View">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="block font-medium">{{ $formLog ? $formLog->format('F j, Y') : '—' }}</span>
                                        @if($formLog)<span class="block text-[0.8125rem] text-slate-400">{{ $formLog->format('g:i A') }}</span>@endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="block font-medium">{{ $formJob->client_account_name ?? $formJob->client_code ?? '—' }}</span>
                                        <span class="block text-[0.8125rem] text-slate-400">{{ $formJob->ncc_compliance ?? '' }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $formJob->job_reference_no ?: '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="block font-medium">{{ $formJob->job_type ?: '—' }}</span>
                                        <span class="block text-[0.8125rem] text-slate-400">{{ $formJob->job_request_id ?? '' }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="lbs-priority inline-block whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold" @if($priorityBg) style="background-color: {{ $priorityBg }};" @endif>{{ $priorityText ?: '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3"><span class="lbs-initials inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">{{ $formJob->staff_id ? strtoupper($formJob->staff_id) : '--' }}</span></td>
                                    <td class="px-4 py-3"><span class="lbs-initials inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">{{ $formJob->checker_id ? strtoupper($formJob->checker_id) : '--' }}</span></td>
                                    <td class="px-4 py-3">
                                        <span class="lbs-badge inline-block rounded-md border-0 px-2 py-1 text-xs font-semibold" @if($statusBg) style="background-color: {{ $statusBg }};" @endif>{{ $status }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="block font-medium">{{ $due ? $due->format('F j, Y') : '—' }}</span>
                                        @if($due)<span class="block text-[0.8125rem] text-slate-400">{{ $due->format('g:i A') }}</span>@endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="block font-medium">{{ $completion ? $completion->format('F j, Y') : '—' }}</span>
                                        @if($completion)<span class="block text-[0.8125rem] text-slate-400">{{ $completion->format('g:i A') }}</span>@endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block rounded-md bg-cyan-500/15 px-2 py-1 text-xs font-semibold text-cyan-300">FORMS</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-4 py-4 text-center text-slate-400 dark:text-slate-500">
                                        No jobs submitted from forms yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* Sort icon arrows - JS toggles data-sort */
.lbs-th[data-sort="asc"] .lbs-sort-icon { font-size: 0; }
.lbs-th[data-sort="asc"] .lbs-sort-icon::before { content: '↑'; font-size: 0.75rem; }
.lbs-th[data-sort="desc"] .lbs-sort-icon { font-size: 0; }
.lbs-th[data-sort="desc"] .lbs-sort-icon::before { content: '↓'; font-size: 0.75rem; }
.lbs-th:not([data-sort=""]) .lbs-sort-icon { opacity: 1; }
/* Expand icon rotate when open */
.lbs-action-expand[aria-expanded="true"] .lbs-expand-icon { transform: rotate(180deg); }
/* Status badge variants (JS applies these on update/revert) */
.lbs-badge-allocated { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
.lbs-badge-completed { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
.lbs-badge-pending { background: rgba(234, 179, 8, 0.2); color: #eab308; }
.lbs-badge-accepted { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
.lbs-badge-awaiting-further-information { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.lbs-badge-for-email-confirmation { background: rgba(99, 102, 241, 0.2); color: #6366f1; }
.lbs-badge-cancelled { background: rgba(100, 116, 139, 0.3); color: #64748b; }
.lbs-badge-for-review { background: rgba(168, 85, 247, 0.2); color: #a855f7; }
.lbs-badge-processing { background: rgba(14, 165, 233, 0.2); color: #0ea5e9; }
.lbs-badge-for-checking { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.lbs-badge-revised { background: rgba(100, 116, 139, 0.2); color: #94a3b8; }
/* Dropdowns: hidden must override display */
.lbs-status-menu[hidden], .lbs-initials-menu[hidden] { display: none !important; }
</style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/lbs-list.js') }}"></script>
@endpush
