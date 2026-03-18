@extends('layouts.dashboard')

@section('title', 'LBS Archive')

@section('body_class', 'page-lbs-trash')

@section('content')
    <div class="block max-w-full pb-0">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">LBS Archive</h1>
                <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View archived LBS jobs.</p>
            </div>
            <div class="shrink-0">
                <label for="lbsSearch" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Search</label>
                <div class="relative flex min-w-[260px] items-center">
                    <svg class="pointer-events-none absolute left-3 text-slate-500 dark:text-slate-500" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsSearch" class="w-full rounded-lg border border-slate-300 bg-slate-50 py-2 pl-9 pr-3.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:placeholder-slate-500 dark:focus:border-blue-700 dark:focus:ring-blue-700/25" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search archived LBS jobs">
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
            <div class="max-w-full overflow-x-auto">
                <table class="lbs-table w-full min-w-[1320px] table-fixed border-collapse text-sm" id="lbsTable">
                    <colgroup>
                        <col style="width: 110px">
                        <col style="width: 140px">
                        <col style="width: 200px">
                        <col style="width: 90px">
                        <col style="width: 105px">
                        <col style="width: 200px">
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
                            <th class="lbs-th-action cursor-default border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400" data-sort=""><span>Action</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Log Date</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Client</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Client Name</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Reference</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Job Type</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Priority</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Staff</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Checker</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Status</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Due Date</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Completion Date</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Complexity</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $index => $job)
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
                                    if ($start->lt($startOfDay)) $start = $startOfDay;
                                    $isTop = str_contains($priorityLower, 'top');
                                    if (!$isTop && $start->gt($cutoff)) $start = $start->copy()->addDay()->setTime(8, 0, 0);
                                    if ($isTop) {
                                        $due = $start->copy()->addHours(6);
                                    } else {
                                        $days = 0;
                                        if (preg_match('/(\d+)\s*day/', $priorityLower, $m)) $days = (int) ($m[1] ?? 0);
                                        if ($days > 0) $due = $start->copy()->addDays($days);
                                    }
                                }
                                $completion = $job->completion_date ? \Carbon\Carbon::parse($job->completion_date, 'Asia/Manila') : null;
                                $isOverdue = $due && !$completion && $due->lt(now('Asia/Manila'));
                                $dueDate1 = $due ? $due->format('F j, Y') : '—';
                                $dueDate2 = $due ? $due->format('g:i A') : '';
                                $completionDate1 = $completion ? $completion->format('F j, Y') : '—';
                                $completionDate2 = $completion ? $completion->format('g:i A') : '';
                                $completionText = $completion ? $completionDate1 . ' ' . $completionDate2 : '—';
                                $priorityBg = $priorityColors[$priorityText] ?? null;
                                $statusBg = $statusColors['Archived'] ?? null;
                                $complexity = is_numeric($job->plan_complexity ?? null) ? (int) $job->plan_complexity : 0;
                                $complexity = max(0, min(5, $complexity));
                            @endphp
                            <tr class="border-b border-slate-200 overflow-hidden align-middle text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5">
                                <td class="overflow-visible px-4 py-3 text-center align-middle text-slate-800 dark:text-slate-200" style="white-space: nowrap;">
                                    <div class="relative z-10 flex flex-nowrap items-center gap-1.5">
                                        <button type="button" class="lbs-restore-trigger inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 transition-colors hover:bg-amber-500/15 hover:text-amber-400 dark:text-slate-400 dark:hover:bg-amber-500/15 dark:hover:text-amber-400" title="Restore" aria-label="Restore" data-restore-url="{{ route('lbs.job.restore', ['id' => $job->job_id]) }}"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg></button>
                                        <a href="{{ route('lbs.job.view', ['id' => $job->job_id]) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-green-500/15 hover:text-green-400 dark:text-slate-400 dark:hover:bg-green-500/15 dark:hover:text-green-400" title="View" aria-label="View"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                                        <button type="button" class="lbs-action-expand inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 transition-colors hover:bg-amber-500/15 hover:text-amber-400 dark:text-slate-400 dark:hover:bg-amber-500/15 dark:hover:text-amber-400" title="View full row details below" aria-label="Show full row details" aria-expanded="false" data-expand-row><svg class="lbs-expand-icon block transition-transform duration-200" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></button>
                                    </div>
                                </td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Log Date" data-sort="{{ $job->log_date }}"><span class="block font-medium text-slate-800 dark:text-slate-200">{{ $logDate1 }}</span>@if($logDate2)<span class="block text-[0.8125rem] text-slate-400">{{ $logDate2 }}</span>@endif</td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client"><span class="block font-medium text-slate-800 dark:text-slate-200">{{ $job->client_account_name ?? $job->client_code ?? '—' }}</span><span class="block text-[0.8125rem] text-slate-400">{{ $job->ncc_compliance ?? '' }}</span></td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client Name" style="white-space: nowrap;">{{ $job->client_code ?? 'LBS' }}</td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Reference" data-sort="{{ $job->job_reference_no }}" style="white-space: nowrap;">{{ $job->job_reference_no ?? $job->reference ?? '—' }}</td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Job Type"><span class="block font-medium text-slate-800 dark:text-slate-200">{{ $job->job_type ?? '—' }}</span><span class="block text-[0.8125rem] text-slate-400">{{ $job->job_request_id ?? '' }}</span></td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Priority" style="white-space: nowrap;"><span class="lbs-priority inline-block whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold" @if($priorityBg) style="background-color: {{ $priorityBg }};" @endif>{{ $priorityText ?: '—' }}</span></td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Staff" style="white-space: nowrap;"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">{{ $job->staff_id ? strtoupper($job->staff_id) : '--' }}</span></td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Checker" style="white-space: nowrap;"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">{{ $job->checker_id ? strtoupper($job->checker_id) : '--' }}</span></td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Status" style="white-space: nowrap;"><span class="inline-block rounded-md bg-slate-500/20 px-2 py-1 text-xs font-semibold text-slate-600 dark:text-slate-400">Archived</span></td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Due Date" data-sort="{{ $due ? $due->format('Y-m-d H:i:s') : '' }}"><span class="block font-medium text-slate-800 dark:text-slate-200 {{ $isOverdue ? 'text-red-400 dark:text-red-400' : '' }}">{{ $dueDate1 }}</span>@if($dueDate2)<span class="block text-[0.8125rem] text-slate-400">{{ $dueDate2 }}</span>@endif @if($isOverdue)<span class="block text-[0.8125rem] font-medium text-red-400 mt-0.5">(Overdue)</span>@endif</td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Completion Date"><span class="block font-medium text-slate-800 dark:text-slate-200">{{ $completionDate1 }}</span>@if($completionDate2)<span class="block text-[0.8125rem] text-slate-400">{{ $completionDate2 }}</span>@endif</td>
                                <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Complexity" data-sort="{{ $complexity }}" style="white-space: nowrap;"><span class="lbs-stars inline-flex items-center" data-rating="{{ $complexity }}" aria-label="{{ $complexity }} out of 5">@include('lbs.partials.stars', ['rating' => $complexity])</span></td>
                            </tr>
                            <tr class="lbs-row-detail border-b border-slate-200 dark:border-slate-700" id="lbs-detail-{{ $index }}" hidden>
                                <td colspan="13" class="bg-slate-50 p-0 align-top dark:bg-slate-900">
                                    <div class="grid gap-x-6 gap-y-4 px-5 py-5" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Log Date</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $logDate1 }} {{ $logDate2 }}</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Client</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $job->client_account_name ?? $job->client_code ?? '—' }} @if($job->ncc_compliance) · {{ $job->ncc_compliance }} @endif</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Client Name</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $job->client_code ?? 'LBS' }}</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Reference</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $job->job_reference_no ?? $job->reference ?? '—' }}</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Job Type</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $job->job_type ?? '—' }} @if($job->job_request_id) · {{ $job->job_request_id }} @endif</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Priority</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="lbs-priority inline-block whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold mt-0.5" @if($priorityBg) style="background-color: {{ $priorityBg }};" @endif>{{ $priorityText ?: '—' }}</span></span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Staff</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 mt-0.5 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">{{ $job->staff_id ? strtoupper($job->staff_id) : '--' }}</span></span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Checker</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 mt-0.5 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">{{ $job->checker_id ? strtoupper($job->checker_id) : '--' }}</span></span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Status</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="inline-block rounded-md bg-slate-500/20 px-2 py-1 text-xs font-semibold text-slate-600 mt-0.5 dark:text-slate-400">Archived</span></span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Due Date</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $dueDate1 }} {{ $dueDate2 }} @if($isOverdue)<br><span class="text-red-400">(Overdue)</span>@endif</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Completion Date</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $completionText }}</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Complexity</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">@include('lbs.partials.stars', ['rating' => $complexity])</span></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="border-b border-slate-200 px-4 py-3 text-center text-slate-400 dark:border-slate-700 dark:text-slate-400" colspan="13">No archived jobs.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-opacity duration-200 [&.is-open]:opacity-100 [&.is-open]:pointer-events-auto" id="lbsTrashRestoreModalOverlay" aria-hidden="true">
            <div class="w-full max-w-md overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800" role="dialog" aria-modal="true" aria-labelledby="lbsTrashRestoreModalTitle">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="lbsTrashRestoreModalTitle">Restore job</h2>
                </div>
                <div class="px-5 py-4">
                    <div id="lbsTrashRestoreConfirm">
                        <p class="text-slate-600 dark:text-slate-300">Restore this job back to the list? It will be set to Allocated.</p>
                    </div>
                    <div id="lbsTrashRestoreCountdown" hidden>
                        <p class="text-slate-600 dark:text-slate-300">Restoring in</p>
                        <div class="mt-2 text-2xl font-bold text-green-600 dark:text-green-400" id="lbsTrashRestoreCountdownNumber">3</div>
                        <p class="mt-1 text-sm text-slate-500">Click Cancel to abort</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                    <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600" id="lbsTrashRestoreModalCancel">Cancel</button>
                    <button type="button" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600" id="lbsTrashRestoreModalConfirm"><span class="lbs-trash-restore-btn-text">Restore</span></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.lbs-th[data-sort="asc"] .lbs-sort-icon { font-size: 0; }
.lbs-th[data-sort="asc"] .lbs-sort-icon::before { content: '↑'; font-size: 0.75rem; }
.lbs-th[data-sort="desc"] .lbs-sort-icon { font-size: 0; }
.lbs-th[data-sort="desc"] .lbs-sort-icon::before { content: '↓'; font-size: 0.75rem; }
.lbs-th:not([data-sort=""]) .lbs-sort-icon { opacity: 1; }
.lbs-action-expand[aria-expanded="true"] .lbs-expand-icon { transform: rotate(180deg); }
</style>
@endpush

@push('scripts')
    <script>
        (function() {
            var searchEl = document.getElementById('lbsSearch');
            var table = document.getElementById('lbsTable');
            if (searchEl && table) {
                var tbody = table.querySelector('tbody');
                searchEl.addEventListener('input', function() {
                    var q = (this.value || '').trim().toLowerCase();
                    if (!tbody) return;
                    var rows = tbody.querySelectorAll('tr:not(.lbs-row-detail)');
                    rows.forEach(function(tr) {
                        var text = (tr.textContent || '').toLowerCase();
                        var match = !q || text.indexOf(q) !== -1;
                        tr.style.display = match ? '' : 'none';
                        var next = tr.nextElementSibling;
                        if (next && next.classList.contains('lbs-row-detail')) next.style.display = match ? '' : 'none';
                    });
                });
            }
            document.querySelectorAll('[data-expand-row]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var row = this.closest('tr');
                    var next = row.nextElementSibling;
                    var isDetail = next && next.classList.contains('lbs-row-detail');
                    if (!isDetail) return;
                    var open = next.hidden;
                    next.hidden = !open;
                    this.setAttribute('aria-expanded', open);
                    this.setAttribute('title', open ? 'Hide details' : 'View full row details below');
                });
            });
            document.querySelectorAll('[data-collapse-detail]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var detailRow = this.closest('tr.lbs-row-detail');
                    if (!detailRow) return;
                    var dataRow = detailRow.previousElementSibling;
                    if (dataRow) {
                        var expandBtn = dataRow.querySelector('[data-expand-row]');
                        if (expandBtn) { expandBtn.setAttribute('aria-expanded', 'false'); expandBtn.setAttribute('title', 'View full row details below'); }
                    }
                    detailRow.hidden = true;
                });
            });
            if (!table) return;
            var thead = table.querySelector('thead');
            thead.addEventListener('click', function(e) {
                var th = e.target.closest('th');
                if (!th || th.classList.contains('lbs-th-action')) return;
                var current = th.getAttribute('data-sort') || '';
                var next = current === 'asc' ? 'desc' : 'asc';
                thead.querySelectorAll('th').forEach(function(h) { h.setAttribute('data-sort', ''); });
                th.setAttribute('data-sort', next);
                var colIndex = Array.prototype.indexOf.call(thead.querySelectorAll('th'), th);
                var tbody = table.querySelector('tbody');
                var allRows = Array.from(tbody.querySelectorAll('tr'));
                var dataRows = allRows.filter(function(r) { return !r.classList.contains('lbs-row-detail'); });
                dataRows.sort(function(a, b) {
                    var aCell = a.children[colIndex], bCell = b.children[colIndex];
                    var aVal = (aCell && (aCell.getAttribute('data-sort') || aCell.textContent)) || '';
                    var bVal = (bCell && (bCell.getAttribute('data-sort') || bCell.textContent)) || '';
                    var aNum = parseFloat(aVal), bNum = parseFloat(bVal);
                    if (!isNaN(aNum) && !isNaN(bNum)) return next === 'asc' ? aNum - bNum : bNum - aNum;
                    if (next === 'asc') return String(aVal).localeCompare(String(bVal), undefined, { numeric: true });
                    return String(bVal).localeCompare(String(aVal), undefined, { numeric: true });
                });
                dataRows.forEach(function(r) {
                    tbody.appendChild(r);
                    var detail = r.nextElementSibling;
                    if (detail && detail.classList.contains('lbs-row-detail')) tbody.appendChild(detail);
                });
            });
        })();

        (function restoreModal() {
            var overlay = document.getElementById('lbsTrashRestoreModalOverlay');
            var confirmBlock = document.getElementById('lbsTrashRestoreConfirm');
            var countdownBlock = document.getElementById('lbsTrashRestoreCountdown');
            var countdownNumber = document.getElementById('lbsTrashRestoreCountdownNumber');
            var cancelBtn = document.getElementById('lbsTrashRestoreModalCancel');
            var confirmBtn = document.getElementById('lbsTrashRestoreModalConfirm');
            var btnTextEl = confirmBtn && confirmBtn.querySelector('.lbs-trash-restore-btn-text');
            var countdownTimer = null;
            var pendingRestoreUrl = null;

            function resetRestoreModal() {
                if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
                if (confirmBlock) confirmBlock.hidden = false;
                if (countdownBlock) countdownBlock.hidden = true;
                if (confirmBtn) confirmBtn.disabled = false;
                if (btnTextEl) btnTextEl.textContent = 'Restore';
            }
            function closeRestoreModal() {
                if (overlay) { overlay.classList.remove('is-open'); overlay.setAttribute('aria-hidden', 'true'); }
                pendingRestoreUrl = null;
                resetRestoreModal();
            }

            document.addEventListener('click', function(e) {
                var trigger = e.target.closest('.lbs-restore-trigger');
                if (trigger) {
                    e.preventDefault();
                    pendingRestoreUrl = trigger.getAttribute('data-restore-url');
                    if (!pendingRestoreUrl) return;
                    resetRestoreModal();
                    if (overlay) { overlay.classList.add('is-open'); overlay.setAttribute('aria-hidden', 'false'); }
                }
            });

            if (cancelBtn) cancelBtn.addEventListener('click', closeRestoreModal);
            if (overlay) overlay.addEventListener('click', function(e) { if (e.target === overlay) closeRestoreModal(); });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && overlay && overlay.classList.contains('is-open')) closeRestoreModal();
            });

            if (confirmBtn && confirmBlock && countdownBlock && countdownNumber) {
                confirmBtn.addEventListener('click', function() {
                    if (!pendingRestoreUrl || countdownTimer) return;
                    confirmBlock.hidden = true;
                    countdownBlock.hidden = false;
                    confirmBtn.disabled = true;
                    if (btnTextEl) btnTextEl.textContent = 'Restoring...';
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
                            window.location.href = pendingRestoreUrl;
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
    </script>
@endpush
