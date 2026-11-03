@php
    $filterBuilderOptions = collect($filterBuilders ?? []);
    $filterPriorityOptions = collect($filterPriorities ?? []);
    $listLabel = $listLabel ?? 'LBS';
@endphp
<div class="mb-6 rounded-xl border border-slate-200 bg-white/80 p-3 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
        <div>
            <label for="lbsSearch" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Search</label>
            <div class="relative flex items-center">
                <svg class="pointer-events-none absolute left-3 text-slate-500 dark:text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="search" id="lbsSearch" class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3.5 text-sm text-slate-900 placeholder-slate-500 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:placeholder-slate-500 dark:focus:border-blue-700 dark:focus:ring-blue-700/25" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search {{ $listLabel }} jobs">
            </div>
        </div>
        <div>
            <label for="lbsFilterDate" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Date Logged</label>
            <input type="date" id="lbsFilterDate" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-blue-700 dark:focus:ring-blue-700/25">
        </div>
        <div>
            <label for="lbsFilterBuilder" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Builder</label>
            <select id="lbsFilterBuilder" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-blue-700 dark:focus:ring-blue-700/25">
                <option value="">All</option>
                @foreach($filterBuilderOptions as $builderName)
                    <option value="{{ $builderName }}">{{ $builderName }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="lbsFilterPriority" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Priority</label>
            <select id="lbsFilterPriority" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-blue-700 dark:focus:ring-blue-700/25">
                <option value="">All</option>
                @foreach($filterPriorityOptions as $priorityOption)
                    <option value="{{ $priorityOption }}">{{ $priorityOption }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-xs font-semibold text-transparent select-none">Reset</label>
            <button type="button" id="lbsFilterReset" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">Reset</button>
        </div>
    </div>
</div>
