@extends('layouts.dashboard')

@section('title', 'Version Details')

@section('content')
    @php
        $versions = collect($versions ?? [])->sortByDesc('version')->values();
        $current = $versions->first();
    @endphp
    <div class="flex max-w-4xl flex-col pb-0">
        <div class="mb-5">
            <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">Version Details</h1>
            <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">What was added on each update.</p>
        </div>

        <div class="mb-6 flex flex-wrap items-center gap-4 rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 dark:border-emerald-800/60 dark:bg-emerald-900/20">
            <div>
                <div class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Current version</div>
                <div class="text-3xl font-bold text-emerald-700 dark:text-emerald-300">v{{ $current['version'] ?? 0 }}</div>
            </div>
            <div class="text-sm text-slate-600 dark:text-slate-300">
                {{ $versions->count() }} {{ \Illuminate\Support\Str::plural('update', $versions->count()) }} so far
                @if(! empty($current['date']))
                    &middot; last updated {{ \Carbon\Carbon::parse($current['date'])->format('F j, Y') }}
                @endif
            </div>
        </div>

        <div class="space-y-4">
            @forelse($versions as $entry)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="rounded-md bg-slate-100 px-2.5 py-1 text-sm font-bold text-slate-800 dark:bg-slate-800 dark:text-slate-100">v{{ $entry['version'] }}</span>
                            @if($loop->first)
                                <span class="rounded-md bg-emerald-500/15 px-2 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">Latest</span>
                            @endif
                        </div>
                        @if(! empty($entry['date']))
                            <span class="text-sm text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($entry['date'])->format('F j, Y') }}</span>
                        @endif
                    </div>
                    <ul class="m-0 list-disc space-y-1 pl-5 text-sm text-slate-700 dark:text-slate-300">
                        @foreach($entry['changes'] ?? [] as $change)
                            <li>{{ $change }}</li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="text-slate-500 dark:text-slate-400">No version history yet.</p>
            @endforelse
        </div>
    </div>
@endsection
