@extends('layouts.dashboard')

@section('title', 'Job Notes')

@section('body_class', 'page-task-management')

@section('content')
    <div class="w-full max-w-full px-0">
        <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h1 class="mb-1 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Job Notes</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Jobs where a staff or checker was tagged in Discussion.</p>
            </div>
            <form method="get" action="{{ route('task_management.job_notes') }}" class="flex items-center gap-2">
                <input type="search" name="q" value="{{ $search }}" placeholder="Search notes, client, reference"
                       class="w-64 max-w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">Search</button>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:border-slate-700 dark:bg-slate-800/80 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Tagged</th>
                            <th class="px-4 py-3">Module</th>
                            <th class="px-4 py-3">Client</th>
                            <th class="px-4 py-3">Reference</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Comment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($notes as $row)
                            <tr class="align-top">
                                <td class="whitespace-nowrap px-4 py-3 font-semibold text-blue-700 dark:text-blue-300">{{ '@'.$row->tagged }}</td>
                                <td class="whitespace-nowrap px-4 py-3 font-medium text-slate-700 dark:text-slate-200">{{ $row->module }}</td>
                                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $row->client }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    @if(!empty($row->view_url))
                                        <a href="{{ $row->view_url }}" class="font-medium text-emerald-700 no-underline hover:underline dark:text-emerald-400">{{ $row->reference }}</a>
                                    @else
                                        <span class="text-slate-700 dark:text-slate-200">{{ $row->reference }}</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-600 dark:text-slate-300">{{ $row->status !== '' ? $row->status : '—' }}</td>
                                <td class="max-w-xl px-4 py-3 text-slate-600 dark:text-slate-300">{{ $row->notes }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">No tagged jobs yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($notes->hasPages())
                <div class="border-t border-slate-200 px-4 py-3 dark:border-slate-700">
                    {{ $notes->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
