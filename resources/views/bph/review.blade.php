@extends('layouts.dashboard')

@section('title', 'BPH For Review')

@section('body_class', 'page-lbs-list page-bph-list page-bph-review')

@section('content')
    @php
        $bphQ = \Illuminate\Support\Facades\DB::table('job_bph')
            ->whereRaw('LOWER(TRIM(COALESCE(client_code, \'\'))) NOT IN (?, ?, ?)', ['bluinq01', 'amt01', 'fyrs01'])
            ->whereRaw('LOWER(TRIM(status)) = ?', ['for review']);
        \App\Services\JobCountsScope::applyJobBphAssignment($bphQ);
        \App\Services\JobCountsScope::applyJobBphBranchVerticalScope($bphQ);
        $rows = $bphQ->orderByDesc('created_at')->limit(300)->get();
    @endphp
    <div class="flex max-w-full flex-col pb-0">
        <div class="mb-5">
            <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">BPH For Review</h1>
            <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View BPH jobs pending review.</p>
        </div>

        @include('partials.pipeline-jobs.filters', ['rows' => $rows, 'searchLabel' => 'Search BPH jobs for review'])
        @include('partials.pipeline-jobs.table', [
            'rows' => $rows,
            'variant' => 'review',
            'module' => 'bph',
            'clientFallback' => 'BPH01',
            'emptyMessage' => 'No BPH jobs for review.',
        ])
    </div>
@endsection

@include('partials.pipeline-jobs.assets')
