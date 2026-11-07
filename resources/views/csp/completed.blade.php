@extends('layouts.dashboard')

@section('title', 'CSP Completed')

@section('body_class', 'page-lbs-list page-csp-list page-csp-completed')

@section('content')
    @php
        $jobQ = \Illuminate\Support\Facades\DB::table('job_csp')
            ->whereRaw('LOWER(TRIM(status)) = ?', ['completed']);
        \App\Services\JobCountsScope::applyJobBphAssignment($jobQ);
        \App\Services\JobCountsScope::applyBranchExclusiveStatLabel($jobQ, 'CSP');
        $rows = $jobQ->orderByDesc('created_at')->limit(300)->get();
    @endphp
    <div class="flex max-w-full flex-col pb-0">
        <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">CSP Completed</h1>
                <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View completed CSP jobs.</p>
            </div>
            @include('partials.completed-export-button', ['exportName' => 'csp-completed'])
        </div>

        @include('partials.pipeline-jobs.filters', ['rows' => $rows, 'showDateRange' => true, 'searchLabel' => 'Search completed CSP jobs'])
        @include('partials.pipeline-jobs.table', [
            'rows' => $rows,
            'variant' => 'completed',
            'module' => 'csp',
            'clientFallback' => 'CSP01',
            'emptyMessage' => 'No completed CSP jobs found.',
        ])
    </div>
@endsection

@include('partials.pipeline-jobs.assets')
