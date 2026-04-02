@extends('layouts.dashboard')

@section('title', 'CSP Completed')

@section('body_class', 'page-lbs-list page-csp-list page-csp-completed')

@section('content')
    <div class="block max-w-full pb-0">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">CSP Completed</h1>
                <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View completed CSP jobs.</p>
            </div>
            <div class="shrink-0">
                <label for="lbsSearch" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Search</label>
                <div class="relative flex min-w-[260px] items-center">
                    <svg class="pointer-events-none absolute left-3 text-slate-500 dark:text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsSearch" class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3.5 text-sm text-slate-900 placeholder-slate-500 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:placeholder-slate-500 dark:focus:border-blue-700 dark:focus:ring-blue-700/25" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search CSP completed jobs">
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
                            $jobQ = \Illuminate\Support\Facades\DB::table('job_csp')
                                ->whereRaw('LOWER(TRIM(status)) = ?', ['completed']);
                            \App\Services\JobCountsScope::applyJobBphAssignment($jobQ);
                            \App\Services\JobCountsScope::applyBranchExclusiveStatLabel($jobQ, 'CSP');
                            $rows = $jobQ->orderByDesc('created_at')->limit(300)->get();
                        @endphp

                        @foreach($rows as $index => $row)
                            @php
                                $logRaw = $row->created_at ?? $row->updated_at ?? now('Asia/Manila')->format('Y-m-d H:i:s');
                                $log = \Carbon\Carbon::parse($logRaw, 'Asia/Manila');
                                $logDate1 = $log->format('F j, Y');
                                $logDate2 = $log->format('g:i A');
                                $client = $row->client_code ?? 'CSP01';
                                $urgent = strtoupper((string) ($row->urgent ?? 'NO'));
                                $jobType1 = (string) ($row->job_type ?? '—');
                                $jobType2 = '';
                                $ncc = (string) ($row->ncc ?? '—');
                                $jobNumber = (string) ($row->job_number ?? '—');
                                $clientName = (string) ($row->client_name ?? '—');
                                $clientEmail = (string) ($row->contact_email ?? '—');
                                $status = (string) ($row->status ?? 'Completed');
                                $assigned = strtoupper((string) ($row->assigned ?? 'GM'));
                                $checker = strtoupper((string) ($row->checked ?? 'GM'));
                            @endphp

                            <tr class="lbs-data-row border-b border-slate-200 overflow-hidden align-middle text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5">
                                <td class="overflow-visible px-4 py-3 text-center align-middle text-slate-800 dark:text-slate-200" style="white-space: nowrap;">
                                    <div class="relative z-10 flex flex-nowrap items-center gap-1.5">
                                        @if(\App\Models\RolePermission::userMayAccessRoute('csp.view'))
                                        <a href="{{ route('csp.view', $row->id) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-emerald-500/15 hover:text-emerald-500 dark:text-slate-400 dark:hover:bg-emerald-500/15 dark:hover:text-emerald-400" title="View job" aria-label="View job">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        @endif
                                        <a href="{{ route('csp.add', [
                                            'duplicate' => 1,
                                            'job_number' => $jobNumber,
                                            'client_name' => $clientName,
                                            'contact_email' => $clientEmail,
                                            'urgent_job' => ($urgent === 'YES') ? 1 : 0,
                                        ]) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-blue-900/25 hover:text-blue-300 dark:text-slate-400 dark:hover:bg-blue-900/25 dark:hover:text-blue-300" title="Duplicate" aria-label="Duplicate">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                        </a>
                                    </div>
                                </td>
                                <td class="lbs-td lbs-td-log-date border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Log Date" data-sort="{{ $logRaw }}">
                                    <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $logDate1 }}</span>
                                    <span class="block text-[0.8125rem] text-slate-400">{{ $logDate2 }}</span>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client">{{ $client }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Urgent">{{ $urgent }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Job Type">
                                    <span class="block font-medium text-slate-800 dark:text-slate-200">{{ $jobType1 }}</span>
                                    <span class="block text-[0.8125rem] text-slate-400">{{ $jobType2 }}</span>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="NCC">{{ $ncc }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Job Number">{{ $jobNumber }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client Name">{{ $clientName }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client Email">{{ $clientEmail }}</td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Status">
                                    <span class="lbs-badge lbs-badge-completed inline-block rounded-md px-2 py-1 text-xs font-semibold" style="background: rgba(34, 197, 94, 0.2); color: #22c55e;">{{ $status }}</span>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Assigned To">
                                    <span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">{{ $assigned }}</span>
                                </td>
                                <td class="lbs-td border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Checked By">
                                    <span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">{{ $checker }}</span>
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
