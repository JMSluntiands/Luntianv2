@extends('layouts.dashboard')

@section('title', 'CSP List')

@section('body_class', 'page-lbs-list page-csp-list')

@section('content')
    @php
        $cspListExcludedStatuses = [
            'completed',
            'for review',
            'for email confirmation',
            'archived',
            'archive',
        ];
        $jobQ = \Illuminate\Support\Facades\DB::table('job_csp')
            ->whereRaw('(status IS NULL OR LOWER(TRIM(status)) NOT IN (' . implode(',', array_fill(0, count($cspListExcludedStatuses), '?')) . '))', $cspListExcludedStatuses);
        \App\Services\JobCountsScope::applyJobBphAssignment($jobQ);
        \App\Services\JobCountsScope::applyBranchExclusiveStatLabel($jobQ, 'CSP');
        $rows = $jobQ->orderByDesc('created_at')->limit(300)->get();
    @endphp
    <div class="flex max-w-full flex-col pb-0">
        <div class="mb-5">
            <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">CSP List</h1>
            <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View and manage all CSP jobs.</p>
        </div>

        @include('partials.pipeline-jobs.filters', ['rows' => $rows, 'searchLabel' => 'Search CSP jobs'])
        @include('partials.pipeline-jobs.table', [
            'rows' => $rows,
            'variant' => 'list',
            'module' => 'csp',
            'clientFallback' => 'CSP01',
            'emptyMessage' => 'No CSP jobs found.',
        ])
    </div>
@endsection

@include('partials.pipeline-jobs.assets')
