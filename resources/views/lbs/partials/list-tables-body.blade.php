        <section class="list-fold" data-list-fold="jobs">
            <div class="mb-3 flex items-center justify-between gap-2">
                <button type="button" class="list-fold-toggle" aria-expanded="true">
                    <svg class="list-fold-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    <h2 class="m-0 text-lg font-semibold text-slate-900 dark:text-white">Jobs</h2>
                </button>
            </div>
            <div class="list-fold-body">
        <div class="max-w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
            <div class="max-w-full overflow-x-auto">
                <table class="lbs-table w-full table-fixed border-collapse text-sm" id="lbsTable">
                    <colgroup>
                        <col style="width: 120px">
                        <col style="width: 140px">
                        <col style="width: 260px">
                        <col style="width: 170px">
                        <col style="width: 140px">
                        <col style="width: 260px">
                        <col style="width: 150px">
                        <col style="width: 70px">
                        <col style="width: 70px">
                        <col style="width: 200px">
                        <col style="width: 155px">
                        <col style="width: 130px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="lbs-th lbs-th-action cursor-default border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400" data-sort="">
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
                                <span>Client reference</span>
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
                                $logDateFilter = $log ? $log->format('Y-m-d') : '';
                                $builderFilter = trim((string) ($job->client_account_name ?? ''));
                                $priorityFilter = trim((string) ($priorityText ?? ''));

                                $priorityBg = $priorityColors[$priorityText] ?? null;

                                $status = $job->job_status ?? 'Allocated';
                                $statusBg  = $statusColors[$status] ?? null;
                                $statusFg = $statusFontColors[$status] ?? \App\Models\Status::DEFAULT_FONT_COLOR;
                                $statusOptions = \App\Support\LbsJobStatusFlow::nextAllowedLabels($status, $statuses ?? []);
                                $canEditStatus = count($statusOptions) > 0;

                                $complexity = is_numeric($job->plan_complexity ?? null) ? (int) $job->plan_complexity : 0;
                                $complexity = max(0, min(5, $complexity));
                            @endphp
                            <tr class="lbs-data-row border-b border-slate-200 align-middle text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5" data-job-id="{{ $job->job_id }}" data-job-units="{{ (int) ($job->units ?? 0) }}" data-update-url="{{ url()->route('lbs.job.update', ['id' => $job->job_id], false) }}" data-log-date-key="{{ $logDateFilter }}" data-builder="{{ $builderFilter }}" data-priority="{{ $priorityFilter }}">
                                <td class="lbs-td overflow-visible px-4 py-3 text-center align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Action" style="white-space: nowrap;">
                                    <div class="relative z-10 flex flex-nowrap items-center justify-center gap-1.5">
                                        <a href="{{ route('lbs.add', ['duplicate' => $job->job_id]) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-blue-900/25 hover:text-blue-300 dark:text-slate-400 dark:hover:bg-blue-900/25 dark:hover:text-blue-300" title="Duplicate" aria-label="Duplicate job to Add New form">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        </a>
                                        <a href="{{ route('lbs.job.view', ['id' => $job->job_id]) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-green-500/15 hover:text-green-400 dark:text-slate-400 dark:hover:bg-green-500/15 dark:hover:text-green-400" title="View" aria-label="View job {{ $job->job_reference_no }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
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
                                @php
                                    $tableReference = $job->job_reference_no ?? $job->reference ?? '—';
                                    $clientRefDisplay = trim((string) ($job->client_reference_no ?? ''));
                                @endphp
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Reference" data-sort="{{ $tableReference }}" title="{{ $tableReference }}">{{ $tableReference }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client reference" data-sort="{{ $clientRefDisplay }}" title="{{ $clientRefDisplay !== '' ? $clientRefDisplay : '—' }}">{{ $clientRefDisplay !== '' ? $clientRefDisplay : '—' }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Job Type">
                                    <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $job->job_type }}</span>
                                    <span class="block text-[0.8125rem] text-slate-400">{{ $job->job_request_id }}</span>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Priority" data-sort="{{ $priorityText }}" style="white-space: nowrap;">
                                    @include('partials.lbs-inline-priority-cell', [
                                        'priority' => $priorityText,
                                        'priorityBg' => $priorityBg,
                                        'priorityOptions' => $priorityOptions ?? [],
                                    ])
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Staff" style="white-space: nowrap;">
                                    @include('partials.assignment-initials-cell', ['role' => 'staff', 'current' => $job->staff_id ?? '', 'options' => $assignmentStaffCodes ?? []])
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Checker" style="white-space: nowrap;">
                                    @include('partials.assignment-initials-cell', ['role' => 'checker', 'current' => $job->checker_id ?? '', 'options' => $assignmentCheckerCodes ?? []])
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Status" style="white-space: nowrap;">
                                    @include('partials.lbs-inline-status-cell', [
                                        'status' => $status,
                                        'statusBg' => $statusBg,
                                        'statusFg' => $statusFg,
                                        'statusOptions' => $statusOptions,
                                        'canEditStatus' => $canEditStatus,
                                        'reference' => $job->job_reference_no ?? $job->reference ?? '',
                                    ])
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
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Complexity" data-sort="{{ $complexity }}" style="white-space: nowrap;">
                                    @include('partials.lbs-inline-complexity-cell', ['rating' => $complexity, 'complexityModule' => 'lbs'])
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
            </div>
        </section>
