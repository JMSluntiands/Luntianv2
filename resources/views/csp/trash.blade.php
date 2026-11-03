@extends('layouts.dashboard')

@section('title', 'CSP Archive')

@section('body_class', 'page-lbs-list page-csp-list page-csp-trash')

@section('content')
    @php
        $jobQ = \Illuminate\Support\Facades\DB::table('job_csp')
            ->whereRaw('LOWER(status) = ?', ['archived']);
        \App\Services\JobCountsScope::applyJobBphAssignment($jobQ);
        \App\Services\JobCountsScope::applyBranchExclusiveStatLabel($jobQ, 'CSP');
        $rows = $jobQ->orderByDesc('updated_at')->limit(300)->get();
    @endphp
    <div class="flex max-w-full flex-col pb-0">
        <div class="mb-5">
            <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">CSP Archive</h1>
            <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View archived CSP jobs.</p>
        </div>

        @include('partials.pipeline-jobs.filters', ['rows' => $rows, 'searchLabel' => 'Search archived CSP jobs'])
        @include('partials.pipeline-jobs.table', [
            'rows' => $rows,
            'variant' => 'trash',
            'module' => 'csp',
            'clientFallback' => 'CSP01',
            'emptyMessage' => 'No archived jobs.',
        ])
    </div>
@endsection

@include('partials.pipeline-jobs.assets')
