@extends('layouts.dashboard')

@section('title', 'CSP List')

@section('body_class', 'page-lbs-list page-csp-list')

@section('content')
    <div class="block max-w-full pb-0">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">CSP List</h1>
                <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View and manage all CSP jobs.</p>
            </div>
            <div class="shrink-0">
                <label for="lbsSearch" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Search</label>
                <div class="relative flex min-w-[260px] items-center">
                    <svg class="pointer-events-none absolute left-3 text-slate-500 dark:text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsSearch" class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3.5 text-sm text-slate-900 placeholder-slate-500 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:placeholder-slate-500 dark:focus:border-blue-700 dark:focus:ring-blue-700/25" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search CSP jobs">
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
            <div class="max-w-full overflow-x-auto">
                <table class="lbs-table w-full min-w-[1320px] table-fixed border-collapse text-sm" id="lbsTable">
                    <colgroup>
                        <col style="width: 110px">
                        <col style="width: 140px">
                        <col style="width: 110px">
                        <col style="width: 90px">
                        <col style="width: 220px">
                        <col style="width: 180px">
                        <col style="width: 110px">
                        <col style="width: 130px">
                        <col style="width: 220px">
                        <col style="width: 170px">
                        <col style="width: 110px">
                        <col style="width: 110px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="lbs-th-action cursor-default border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400" data-sort=""><span>Action</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Log Date</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Client</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Urgent</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Job Type</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>NCC</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Job Number</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Client Name</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Client Email</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Status</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Assigned To</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Checked By</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $cspListExcludedStatuses = [
                                'completed',
                                'for review',
                                'for email confirmation',
                                'archived',
                                'archive',
                            ];
                            $rows = \Illuminate\Support\Facades\DB::table('job_csp')
                                ->whereRaw('(status IS NULL OR LOWER(TRIM(status)) NOT IN (' . implode(',', array_fill(0, count($cspListExcludedStatuses), '?')) . '))', $cspListExcludedStatuses)
                                ->orderByDesc('created_at')
                                ->limit(300)
                                ->get();
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
                        @endphp

                        @foreach($rows as $index => $row)
                            @php
                                $rowStatusNorm = strtolower(trim((string) ($row->status ?? '')));
                                if (in_array($rowStatusNorm, $cspListExcludedStatuses, true)) {
                                    continue;
                                }
                                $logRaw = $row->created_at ?? $row->updated_at ?? now('Asia/Manila')->format('Y-m-d H:i:s');
                                $log = \Carbon\Carbon::parse($logRaw, 'Asia/Manila');
                                $logDate1 = $log->format('F j, Y');
                                $logDate2 = $log->format('g:i A');
                                $status = (string) ($row->status ?? 'Allocated');
                                $statusClass = $statusClasses[$status] ?? 'lbs-badge-allocated';
                                $lowerStatus = strtolower($status);
                                $allStatusOptions = ['Pending', 'Accepted', 'Allocated', 'Awaiting Further Information', 'Completed', 'For Review', 'Processing', 'For Checking', 'Revised'];
                                if ($lowerStatus === 'allocated') {
                                    $statusOptions = ['Accepted', 'Processing'];
                                } elseif (in_array($lowerStatus, ['accepted', 'processing', 'revised'], true)) {
                                    $statusOptions = ['For Checking'];
                                } elseif ($lowerStatus === 'for checking') {
                                    $statusOptions = ['For Review', 'Revised'];
                                } else {
                                    $statusOptions = $allStatusOptions;
                                }
                                $client = $row->client_code ?? 'CSP01';
                                $urgent = strtoupper((string) ($row->urgent ?? 'NO'));
                                $jobType1 = (string) ($row->job_type ?? '—');
                                $jobType2 = '';
                                $ncc = (string) ($row->ncc ?? '—');
                                $jobNumber = (string) ($row->job_number ?? '—');
                                $clientName = (string) ($row->client_name ?? '—');
                                $clientEmail = (string) ($row->contact_email ?? '—');
                                $assigned = strtoupper((string) ($row->assigned ?? 'GM'));
                                $checker = strtoupper((string) ($row->checked ?? 'GM'));
                            @endphp
                            <tr class="lbs-data-row border-b border-slate-200 overflow-hidden align-middle text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5"
                                data-update-url="{{ route('csp.update', ['id' => $row->id]) }}">
                                <td class="overflow-visible px-4 py-3 text-center align-middle text-slate-800 dark:text-slate-200" style="white-space: nowrap;">
                                    <div class="relative z-10 flex flex-nowrap items-center gap-1.5">
                                        <a href="{{ route('csp.add', [
                                            'duplicate' => 1,
                                            'job_number' => $jobNumber,
                                            'client_name' => $clientName,
                                            'contact_email' => $clientEmail,
                                            'urgent_job' => ($urgent === 'YES') ? 1 : 0,
                                        ]) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 transition-colors hover:bg-blue-900/25 hover:text-blue-300 dark:text-slate-400 dark:hover:bg-blue-900/25 dark:hover:text-blue-300 no-underline" title="Duplicate" aria-label="Duplicate">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        </a>
                                        <button type="button" class="lbs-action-icon lbs-action-expand inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 transition-colors hover:bg-amber-500/15 hover:text-amber-400 dark:text-slate-400 dark:hover:bg-amber-500/15 dark:hover:text-amber-400" title="View full row details below" aria-label="Show full row details" aria-expanded="false" data-expand-row>
                                            <svg class="lbs-expand-icon block transition-transform duration-200" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-sort="{{ $logRaw }}">
                                    <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $logDate1 }}</span>
                                    <span class="block text-[0.8125rem] text-slate-400">{{ $logDate2 }}</span>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200">{{ $client }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200">{{ $urgent }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200">
                                    <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $jobType1 }}</span>
                                    <span class="block text-[0.8125rem] text-slate-400">{{ $jobType2 }}</span>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200">{{ $ncc }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200">{{ $jobNumber }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200">{{ $clientName }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200">{{ $clientEmail }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200">
                                    <div class="lbs-status-wrap relative inline-block" data-status-wrap>
                                        <button type="button" class="lbs-badge lbs-status-trigger {{ $statusClass }} inline-block rounded-md border-0 px-2 py-1 text-xs font-semibold leading-tight cursor-pointer hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-600/40 focus:ring-offset-0 dark:focus:ring-blue-500/40" data-status-trigger aria-haspopup="true" aria-expanded="false" data-reference="{{ $jobNumber }}">{{ $status }}</button>
                                        <div class="lbs-status-menu fixed z-[9999] flex min-w-[90px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu" hidden>
                                            @foreach($statusOptions as $opt)
                                                <button type="button" role="menuitem" class="lbs-status-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10" data-status-value="{{ $opt }}">{{ $opt }}</button>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200">
                                    <div class="lbs-initials-wrap relative inline-block" data-initials-wrap data-role="staff">
                                        <button type="button" class="lbs-initials lbs-initials-trigger inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200" data-initials-trigger aria-haspopup="true" aria-expanded="false">{{ $assigned }}</button>
                                        <div class="lbs-initials-menu fixed z-[9999] flex min-w-[70px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu" hidden>
                                            @foreach(['AJS','SB','GM','PEP','JDR','JS'] as $u)
                                                <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10" data-value="{{ $u }}">{{ $u }}</button>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200">
                                    <div class="lbs-initials-wrap relative inline-block" data-initials-wrap data-role="checker">
                                        <button type="button" class="lbs-initials lbs-initials-trigger inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200" data-initials-trigger aria-haspopup="true" aria-expanded="false">{{ $checker }}</button>
                                        <div class="lbs-initials-menu fixed z-[9999] flex min-w-[70px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu" hidden>
                                            @foreach(['AJS','SB','GM','PEP','JDR','JS'] as $u)
                                                <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10" data-value="{{ $u }}">{{ $u }}</button>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="lbs-row-detail border-b border-slate-200 dark:border-slate-700" hidden>
                                <td colspan="12" class="bg-slate-50 p-0 align-top dark:bg-slate-900">
                                    <div class="grid gap-x-6 gap-y-4 px-5 py-5" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Client</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $client }}</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Urgent</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $urgent }}</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Job Number</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $jobNumber }}</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Client Email</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $clientEmail }}</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Status</span><span class="lbs-detail-status-badge text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $status }}</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Assigned</span><span class="lbs-detail-staff-badge text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $assigned }}</span></div>
                                        <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Checker</span><span class="lbs-detail-checker-badge text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">{{ $checker }}</span></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.lbs-status-menu[hidden], .lbs-initials-menu[hidden] { display: none !important; }
</style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/lbs-list.js') }}"></script>
@endpush
