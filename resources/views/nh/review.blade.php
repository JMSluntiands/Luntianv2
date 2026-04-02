@extends('layouts.dashboard')

@section('title', 'NH For Review')

@section('body_class', 'page-lbs-list page-nh-list page-nh-review')

@section('content')
    <div class="block max-w-full pb-0">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">NH For Review</h1>
                <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View NH jobs that are for review.</p>
            </div>
            <div class="shrink-0">
                <label for="lbsSearch" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Search</label>
                <div class="relative flex min-w-[260px] items-center">
                    <svg class="pointer-events-none absolute left-3 text-slate-500 dark:text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsSearch" class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3.5 text-sm text-slate-900 placeholder-slate-500 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:placeholder-slate-500 dark:focus:border-blue-700 dark:focus:ring-blue-700/25" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search NH review jobs">
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
            <div class="max-w-full overflow-x-auto">
                <table class="lbs-table w-full min-w-[1320px] table-fixed border-collapse text-sm" id="lbsTable">
                    <colgroup>
                        <col style="width: 110px"><col style="width: 140px"><col style="width: 110px"><col style="width: 90px">
                        <col style="width: 220px"><col style="width: 180px"><col style="width: 110px"><col style="width: 130px">
                        <col style="width: 220px"><col style="width: 170px"><col style="width: 110px"><col style="width: 110px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="lbs-th-action cursor-default border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Action</th>
                            <th class="lbs-th border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Log Date</th>
                            <th class="lbs-th border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Client</th>
                            <th class="lbs-th border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Urgent</th>
                            <th class="lbs-th border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Job Type</th>
                            <th class="lbs-th border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">NCC</th>
                            <th class="lbs-th border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Job Number</th>
                            <th class="lbs-th border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Client Name</th>
                            <th class="lbs-th border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Client Email</th>
                            <th class="lbs-th border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Status</th>
                            <th class="lbs-th border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Assigned To</th>
                            <th class="lbs-th border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Checked By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $jobQ = \Illuminate\Support\Facades\DB::table('job_nh')
                                ->whereRaw('LOWER(status) = ?', ['for review']);
                            \App\Services\JobCountsScope::applyJobBphAssignment($jobQ);
                            \App\Services\JobCountsScope::applyBranchExclusiveStatLabel($jobQ, 'NH');
                            $rows = $jobQ->orderByDesc('created_at')->limit(300)->get();
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
                        @foreach($rows as $row)
                            @php
                                $logRaw = $row->created_at ?? $row->updated_at ?? now('Asia/Manila')->format('Y-m-d H:i:s');
                                $log = \Carbon\Carbon::parse($logRaw, 'Asia/Manila');
                                $client = $row->client_code ?? 'NH01';
                                $urgent = strtoupper((string) ($row->urgent ?? 'NO'));
                                $jobType = (string) ($row->job_type ?? '—');
                                $ncc = (string) ($row->ncc ?? '—');
                                $jobNumber = (string) ($row->job_number ?? '—');
                                $clientName = (string) ($row->client_name ?? '—');
                                $clientEmail = (string) ($row->contact_email ?? '—');
                                $status = (string) ($row->status ?? 'For Review');
                                $statusClass = $statusClasses[$status] ?? 'lbs-badge-for-review';
                                $statusOptions = ['For Email Confirmation', 'Cancelled'];
                                $assigned = strtoupper((string) ($row->assigned ?? 'GM'));
                                $checker = strtoupper((string) ($row->checked ?? 'GM'));
                            @endphp
                            <tr class="lbs-data-row border-b border-slate-200 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-update-url="{{ route('nh.update', ['id' => $row->id]) }}">
                                <td class="px-4 py-3 text-center"><span class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="8"/></svg></span></td>
                                <td class="px-4 py-3"><span class="block font-medium">{{ $log->format('F j, Y') }}</span><span class="block text-[0.8125rem] text-slate-400">{{ $log->format('g:i A') }}</span></td>
                                <td class="px-4 py-3">{{ $client }}</td><td class="px-4 py-3">{{ $urgent }}</td><td class="px-4 py-3">{{ $jobType }}</td>
                                <td class="px-4 py-3">{{ $ncc }}</td><td class="px-4 py-3">{{ $jobNumber }}</td><td class="px-4 py-3">{{ $clientName }}</td><td class="px-4 py-3">{{ $clientEmail }}</td>
                                <td class="px-4 py-3">
                                    <div class="lbs-status-wrap relative inline-block" data-status-wrap>
                                        <button type="button" class="lbs-badge lbs-status-trigger {{ $statusClass }} inline-block rounded-md border-0 px-2 py-1 text-xs font-semibold leading-tight cursor-pointer hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-600/40 focus:ring-offset-0 dark:focus:ring-blue-500/40" data-status-trigger aria-haspopup="true" aria-expanded="false" data-reference="{{ $jobNumber }}">{{ $status }}</button>
                                        <div class="lbs-status-menu fixed z-[9999] flex min-w-[90px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu" hidden>
                                            @foreach($statusOptions as $opt)
                                                <button type="button" role="menuitem" class="lbs-status-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10" data-status-value="{{ $opt }}">{{ $opt }}</button>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ $assigned }}</td><td class="px-4 py-3">{{ $checker }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/lbs-list.js') }}"></script>
@endpush
