@extends('layouts.dashboard')

@section('title', 'Redistar Completed')

@section('body_class', 'page-lbs-list page-fyrs-list page-fyrs-completed')

@section('content')
    @php
        $jobQ = \Illuminate\Support\Facades\DB::table('job_fyrs')
            ->whereRaw('LOWER(TRIM(status)) = ?', ['completed']);
        \App\Services\JobCountsScope::applyJobBphAssignment($jobQ);
        \App\Services\JobCountsScope::applyJobBphBranchVerticalScope($jobQ);
        $rows = $jobQ->orderByDesc('created_at')->limit(300)->get();
    @endphp
    <div class="flex max-w-full flex-col pb-0">
        <div class="mb-5">
            <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">Redistar Completed</h1>
            <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View completed Redistar jobs.</p>
        </div>

        @include('partials.pipeline-jobs.filters', ['rows' => $rows, 'searchLabel' => 'Search completed Redistar jobs'])
        @include('partials.pipeline-jobs.table', [
            'rows' => $rows,
            'variant' => 'completed',
            'module' => 'fyrs',
            'clientFallback' => 'FYRS01',
            'allowDuplicate' => false,
            'emptyMessage' => 'No completed Redistar jobs found.',
        ])
    </div>
@endsection

@include('partials.pipeline-jobs.assets')
