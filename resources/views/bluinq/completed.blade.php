@extends('layouts.dashboard')

@section('title', 'BluInq Completed')

@section('body_class', 'page-lbs-list page-bluinq-list page-bluinq-completed')

@section('content')
    @php
        $bphQ = \Illuminate\Support\Facades\DB::table('job_bph')
            ->where('client_code', 'BLUINQ01')
            ->whereRaw('LOWER(TRIM(status)) = ?', ['completed']);
        \App\Services\JobCountsScope::applyJobBphAssignment($bphQ);
        \App\Services\JobCountsScope::applyJobBphBranchVerticalScope($bphQ);
        $rows = $bphQ->orderByDesc('created_at')->limit(300)->get();
    @endphp
    <div class="flex max-w-full flex-col pb-0">
        <div class="mb-5">
            <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">BluInq Completed</h1>
            <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View completed BluInq jobs.</p>
        </div>

        @include('partials.pipeline-jobs.filters', ['rows' => $rows, 'searchLabel' => 'Search completed BluInq jobs'])
        @include('partials.pipeline-jobs.table', [
            'rows' => $rows,
            'variant' => 'completed',
            'module' => 'bluinq',
            'clientFallback' => 'BLUINQ01',
            'emptyMessage' => 'No completed BluInq jobs found.',
        ])
    </div>
@endsection

@include('partials.pipeline-jobs.assets')
