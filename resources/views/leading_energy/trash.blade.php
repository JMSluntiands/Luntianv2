@extends('layouts.dashboard')

@section('title', 'LEADING ENERGY Archive')

@section('body_class', 'page-lbs-list page-leading-energy-list page-leading-energy-trash')

@section('content')
    <div class="block max-w-full pb-0">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">LEADING ENERGY Archive</h1>
                <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">Archived LEADING ENERGY jobs.</p>
            </div>
        </div>

        <div class="max-w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
            <div class="max-w-full overflow-x-auto">
                <table class="lbs-table w-full min-w-[1120px] table-fixed border-collapse text-sm" id="lbsTable">
                    <thead>
                        <tr>
                            <th class="border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Action</th>
                            <th class="border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Log Date</th>
                            <th class="border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Reference</th>
                            <th class="border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Client Name</th>
                            <th class="border-b border-slate-200 bg-slate-100 px-5 py-3 text-left font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $rows = collect();
                            if (\Illuminate\Support\Facades\Schema::hasTable('job_leading_energy')) {
                                $jobQ = \Illuminate\Support\Facades\DB::table('job_leading_energy')
                                    ->whereRaw('LOWER(status) = ?', ['archived']);
                                \App\Services\JobCountsScope::applyJobBphAssignment($jobQ);
                                \App\Services\JobCountsScope::applyBranchExclusiveStatLabel($jobQ, 'LEADING ENERGY');
                                $rows = $jobQ->orderByDesc('updated_at')->limit(300)->get();
                            }
                        @endphp
                        @forelse($rows as $row)
                            @php $log = \Carbon\Carbon::parse($row->updated_at ?? $row->created_at ?? now(), 'Asia/Manila'); @endphp
                            <tr class="border-b border-slate-200 dark:border-slate-700">
                                <td class="px-5 py-3 text-slate-500 dark:text-slate-400">N/A</td>
                                <td class="px-5 py-3">{{ $log->format('M j, Y g:i A') }}</td>
                                <td class="px-5 py-3 font-mono">{{ $row->reference ?? '—' }}</td>
                                <td class="px-5 py-3">{{ $row->client_name ?? '—' }}</td>
                                <td class="px-5 py-3">{{ $row->status ?? 'Archived' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500 dark:text-slate-400">No archived jobs.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

