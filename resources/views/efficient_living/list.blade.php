@extends('layouts.dashboard')

@section('title', 'Efficient Living List')

@section('body_class', 'page-lbs-list page-efficient_living-list')

@section('content')
    @php
        $statusColors = $statusColors ?? [];
        $statusFontColors = $statusFontColors ?? [];
    @endphp
    <div class="flex max-w-full flex-col pb-0">
        <div class="mb-5">
            <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">Efficient Living List</h1>
            <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">View and manage all Efficient Living jobs.</p>
        </div>

        @include('lbs.partials.list-filters', ['listLabel' => 'Efficient Living'])

        <div id="lbs-list-tables-inner">
            @include('lbs.partials.list-tables-body', ['listModule' => 'efficient_living', 'listLabel' => 'Efficient Living'])
        </div>
    </div>
@endsection

@include('lbs.partials.list-assets')
