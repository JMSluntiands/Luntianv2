@extends('layouts.dashboard')

@section('title', 'BPH List')

@section('body_class', 'page-lbs-list page-bph-list')

@section('content')
    @php
        $bphListExcludedStatuses = [
            'completed',
            'for review',
            'for email confirmation',
            'archived',
            'archive',
        ];
        $bphQ = \Illuminate\Support\Facades\DB::table('job_bph')
            ->whereRaw('LOWER(TRIM(COALESCE(client_code, \'\'))) NOT IN (?, ?, ?)', ['bluinq01', 'amt01', 'fyrs01'])
            ->whereRaw('(status IS NULL OR LOWER(TRIM(status)) NOT IN (' . implode(',', array_fill(0, count($bphListExcludedStatuses), '?')) . '))', $bphListExcludedStatuses);
        \App\Services\JobCountsScope::applyJobBphAssignment($bphQ);
        \App\Services\JobCountsScope::applyJobBphBranchVerticalScope($bphQ);
        $rows = $bphQ->orderByDesc('created_at')->limit(300)->get();
    @endphp
    <div class="flex max-w-full flex-col pb-0">
        <div class="mb-5">
            <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">BPH List</h1>
            <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View and manage all BPH jobs.</p>
        </div>

        @include('partials.pipeline-jobs.filters', ['rows' => $rows, 'searchLabel' => 'Search BPH jobs'])
        @include('partials.pipeline-jobs.table', [
            'rows' => $rows,
            'variant' => 'list',
            'module' => 'bph',
            'clientFallback' => 'BPH01',
            'emptyMessage' => 'No BPH jobs found.',
        ])
    </div>
@endsection

@include('partials.pipeline-jobs.assets')
