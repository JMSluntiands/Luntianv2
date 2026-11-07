{{--
    LBS-style job table for job_bph / job_csp rows (BPH, CSP, BluInq).
    $variant: list | review | completed | trash
    $module: bph | csp | bluinq | fyrs
--}}
@php
    $variant = $variant ?? 'list';
    $module = $module ?? 'bph';
    $allowDuplicate = $allowDuplicate ?? true;
    $rows = collect($rows ?? []);
    $clientFallback = $clientFallback ?? strtoupper($module).'01';
    $emptyMessage = $emptyMessage ?? 'No jobs found.';

    $isList = $variant === 'list';
    $isCompleted = $variant === 'completed';
    $isTrash = $variant === 'trash';
    $isEditable = in_array($variant, ['list', 'review'], true);

    if (! isset($assignmentStaffCodes) || ! isset($assignmentCheckerCodes)) {
        $__assign = \App\Models\User::assignmentInitialsViewData($module);
        $assignmentStaffCodes = $assignmentStaffCodes ?? $__assign['assignmentStaffCodes'];
        $assignmentCheckerCodes = $assignmentCheckerCodes ?? $__assign['assignmentCheckerCodes'];
    }

    $statusColors = \App\Models\Status::backgroundColorsByName();
    $statusFontColors = \App\Models\Status::fontColorsByName();

    $priorityOptions = ['Urgent', 'Standard'];
    $priorityColorOverrides = ['Urgent' => '#fca5a5', 'Standard' => '#cbd5e1'];

    $statusClasses = [
        'Pending' => 'lbs-badge-pending',
        'Accepted' => 'lbs-badge-accepted',
        'Allocated' => 'lbs-badge-allocated',
        'Awaiting Further Information' => 'lbs-badge-awaiting-further-information',
        'Completed' => 'lbs-badge-completed',
        'For Review' => 'lbs-badge-for-review',
        'Processing' => 'lbs-badge-processing',
        'For Checking' => 'lbs-badge-for-checking',
        'Revised' => 'lbs-badge-revised',
        'Cancelled' => 'lbs-badge-cancelled',
    ];
    $defaultStatus = match ($variant) {
        'review' => 'For Review',
        'completed' => 'Completed',
        'trash' => 'Archived',
        default => 'Allocated',
    };

    $columns = ['Action', 'Log Date'];
    if ($isCompleted) {
        $columns[] = 'Completion Date';
    }
    $columns[] = 'Client';
    if ($isList) {
        array_push($columns, 'Reference', 'Client reference', 'Address');
    } else {
        array_push($columns, 'Client Name', 'Reference');
    }
    array_push($columns, 'Job Type', 'Priority', 'Staff', 'Checker', 'Status', 'Due Date', 'Complexity');

    $widths = [
        'Action' => 110, 'Log Date' => 140, 'Completion Date' => 150, 'Client' => 200,
        'Reference' => 140, 'Client reference' => 220, 'Client Name' => 220, 'Address' => 240, 'Job Type' => 200,
        'Priority' => 150, 'Staff' => 70, 'Checker' => 70, 'Status' => 200, 'Due Date' => 155, 'Complexity' => 95,
    ];
    $thBase = 'border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400';
    $tdBase = 'lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200';
    $readonlyBadge = 'inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200';
    $mayView = $module !== 'csp' || \App\Models\RolePermission::userMayAccessRoute('csp.view');
@endphp
<div class="max-w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
    <div class="max-w-full overflow-x-auto">
        <table class="lbs-table w-full table-fixed border-collapse text-sm" id="lbsTable">
            <colgroup>
                @foreach($columns as $col)
                    <col style="width: {{ $widths[$col] ?? 120 }}px">
                @endforeach
            </colgroup>
            <thead>
                <tr>
                    @foreach($columns as $col)
                        @if($col === 'Action')
                            <th class="lbs-th lbs-th-action cursor-default {{ $thBase }}" data-sort=""><span>Action</span></th>
                        @else
                            <th class="lbs-th cursor-pointer select-none whitespace-nowrap {{ $thBase }} dark:hover:text-slate-200" data-sort=""><span>{{ $col }}</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    @php
                        $logRaw = $row->created_at ?? $row->updated_at ?? null;
                        $log = $logRaw ? \Carbon\Carbon::parse($logRaw, 'Asia/Manila') : null;
                        $completionRaw = $row->completion_date ?? null;
                        $completion = $completionRaw ? \Carbon\Carbon::parse($completionRaw, 'Asia/Manila') : null;

                        $client = $row->client_code ?? $clientFallback;
                        $ncc = trim((string) ($row->ncc ?? ''));
                        $jobNumber = trim((string) ($row->job_number ?? ''));
                        $reference = $jobNumber !== '' ? $jobNumber : trim((string) ($row->reference ?? ''));
                        $clientName = trim((string) ($row->client_name ?? ''));
                        $clientEmail = trim((string) ($row->contact_email ?? ''));
                        $urgent = strtoupper((string) ($row->urgent ?? 'NO'));
                        $priorityText = $urgent === 'YES' ? 'Urgent' : 'Standard';
                        $assigned = strtoupper((string) ($row->assigned ?? ''));
                        $checker = strtoupper((string) ($row->checked ?? ''));

                        $status = trim((string) ($row->status ?? '')) ?: $defaultStatus;
                        $lowerStatus = strtolower($status);
                        if ($variant === 'list') {
                            if ($lowerStatus === 'allocated') {
                                $statusOptions = ['Accepted', 'Processing'];
                            } elseif (in_array($lowerStatus, ['accepted', 'processing', 'revised'], true)) {
                                $statusOptions = ['For Checking'];
                            } elseif ($lowerStatus === 'for checking') {
                                $statusOptions = ['For Review', 'Revised'];
                            } else {
                                $statusOptions = ['Pending', 'Accepted', 'Allocated', 'Awaiting Further Information', 'Completed', 'For Review', 'Processing', 'For Checking', 'Revised'];
                            }
                        } elseif ($variant === 'review') {
                            $statusOptions = ['For Email Confirmation', 'Cancelled'];
                        } else {
                            $statusOptions = [];
                        }
                    @endphp
                    <tr class="lbs-data-row border-b border-slate-200 align-middle text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5"
                        data-job-id="{{ $row->id }}"
                        data-job-units="{{ (int) ($row->units ?? 0) }}"
                        @if($isEditable) data-update-url="{{ route($module.'.update', ['id' => $row->id], false) }}" @endif
                        data-log-date-key="{{ $log ? $log->format('Y-m-d') : '' }}"
                        data-builder="{{ $clientName }}"
                        data-priority="{{ $priorityText }}"
                        @if($isCompleted)
                            data-completion-date-key="{{ $completion ? $completion->format('Y-m-d') : '' }}"
                            data-export-date="{{ ($completion ?? $log)?->format('M d, Y') }}"
                            data-export-address="{{ trim((string) ($row->address ?? '')) }}"
                            data-export-client="{{ $clientName !== '' ? $clientName : $client }}"
                            data-export-job-type="{{ $row->job_type ?? '' }}"
                        @endif>
                        <td class="lbs-td overflow-visible px-4 py-3 text-center align-middle text-slate-800 dark:text-slate-200" data-label="Action" style="white-space: nowrap;">
                            <div class="relative z-10 flex flex-nowrap items-center justify-center gap-1.5">
                                @if($allowDuplicate && ! $isTrash)
                                    <a href="{{ route($module.'.add', [
                                        'duplicate' => 1,
                                        'job_number' => $jobNumber,
                                        'client_name' => $clientName,
                                        'contact_email' => $clientEmail,
                                        'urgent_job' => $urgent === 'YES' ? 1 : 0,
                                    ]) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-blue-900/25 hover:text-blue-300 dark:text-slate-400 dark:hover:bg-blue-900/25 dark:hover:text-blue-300" title="Duplicate" aria-label="Duplicate job to Add New form">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                    </a>
                                @endif
                                @if($mayView)
                                    <a href="{{ route($module.'.view', $row->id) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-green-500/15 hover:text-green-400 dark:text-slate-400 dark:hover:bg-green-500/15 dark:hover:text-green-400" title="View" aria-label="View job {{ $reference }}">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="{{ $tdBase }} lbs-td-log-date" data-label="Log Date" data-sort="{{ $logRaw }}">
                            <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $log ? $log->format('F j, Y') : '—' }}</span>
                            @if($log)<span class="block text-[0.8125rem] text-slate-400">{{ $log->format('g:i A') }}</span>@endif
                        </td>
                        @if($isCompleted)
                            <td class="{{ $tdBase }} lbs-td-completion-date" data-label="Completion Date" data-sort="{{ $completionRaw }}">
                                <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $completion ? $completion->format('F j, Y') : '—' }}</span>
                                @if($completion)<span class="block text-[0.8125rem] text-slate-400">{{ $completion->format('g:i A') }}</span>@endif
                            </td>
                        @endif
                        <td class="{{ $tdBase }}" data-label="Client">
                            <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $client }}</span>
                            <span class="block text-[0.8125rem] text-slate-400">{{ $ncc }}</span>
                        </td>
                        @if($isList)
                            <td class="{{ $tdBase }}" data-label="Reference" data-sort="{{ $reference }}" title="{{ $reference ?: '—' }}">{{ $reference ?: '—' }}</td>
                        @endif
                        <td class="{{ $tdBase }}" data-label="{{ $isList ? 'Client reference' : 'Client Name' }}" data-sort="{{ $clientName }}" title="{{ $clientName ?: '—' }}">
                            <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $clientName ?: '—' }}</span>
                            @if($clientEmail !== '')<span class="block truncate text-[0.8125rem] text-slate-400">{{ $clientEmail }}</span>@endif
                        </td>
                        @if($isList)
                            @php $addressDisplay = trim((string) ($row->address ?? '')); @endphp
                            <td class="{{ $tdBase }}" data-label="Address" data-sort="{{ $addressDisplay }}" title="{{ $addressDisplay !== '' ? $addressDisplay : '—' }}">{{ $addressDisplay !== '' ? $addressDisplay : '—' }}</td>
                        @else
                            <td class="{{ $tdBase }}" data-label="Reference" data-sort="{{ $reference }}" title="{{ $reference ?: '—' }}">{{ $reference ?: '—' }}</td>
                        @endif
                        <td class="{{ $tdBase }}" data-label="Job Type">
                            <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $row->job_type ?? '—' }}</span>
                        </td>
                        <td class="{{ $tdBase }}" data-label="Priority" data-sort="{{ $priorityText }}" style="white-space: nowrap;">
                            @if($isEditable)
                                @include('partials.lbs-inline-priority-cell', [
                                    'priority' => $priorityText,
                                    'priorityBg' => $priorityColorOverrides[$priorityText],
                                    'priorityOptions' => $priorityOptions,
                                    'priorityColorOverrides' => $priorityColorOverrides,
                                ])
                            @else
                                <span class="lbs-priority inline-block whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold text-slate-800" style="background-color: {{ $priorityColorOverrides[$priorityText] }};">{{ $priorityText }}</span>
                            @endif
                        </td>
                        <td class="{{ $tdBase }}" data-label="Staff" style="white-space: nowrap;">
                            @if($isEditable)
                                @include('partials.assignment-initials-cell', ['role' => 'staff', 'current' => $assigned, 'options' => $assignmentStaffCodes ?? []])
                            @else
                                <span class="{{ $readonlyBadge }}">{{ $assigned !== '' ? $assigned : '--' }}</span>
                            @endif
                        </td>
                        <td class="{{ $tdBase }}" data-label="Checker" style="white-space: nowrap;">
                            @if($isEditable)
                                @include('partials.assignment-initials-cell', ['role' => 'checker', 'current' => $checker, 'options' => $assignmentCheckerCodes ?? []])
                            @else
                                <span class="{{ $readonlyBadge }}">{{ $checker !== '' ? $checker : '--' }}</span>
                            @endif
                        </td>
                        <td class="{{ $tdBase }}" data-label="Status" style="white-space: nowrap;">
                            @include('partials.lbs-inline-status-cell', [
                                'status' => $status,
                                'statusBg' => $statusColors[$status] ?? null,
                                'statusFg' => $statusFontColors[$status] ?? \App\Models\Status::DEFAULT_FONT_COLOR,
                                'statusClass' => ($statusColors[$status] ?? null) ? null : ($statusClasses[$status] ?? null),
                                'statusOptions' => $statusOptions,
                                'canEditStatus' => count($statusOptions) > 0,
                                'reference' => $reference,
                            ])
                        </td>
                        <td class="{{ $tdBase }} lbs-td-due" data-label="Due Date" data-sort="" data-overdue="0">
                            <span class="block font-medium text-slate-800 dark:text-slate-200">—</span>
                        </td>
                        <td class="{{ $tdBase }}" data-label="Complexity" data-sort="" style="white-space: nowrap;">
                            <span class="text-slate-400">—</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="border-b border-slate-200 px-4 py-3 text-center text-slate-400 dark:border-slate-700 dark:text-slate-400" colspan="{{ count($columns) }}">{{ $emptyMessage }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
