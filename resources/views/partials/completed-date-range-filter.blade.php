@php
    $rangeInputClass = 'w-full min-w-0 rounded-lg border border-slate-300 bg-white px-2.5 py-2 text-sm text-slate-900 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:focus:border-blue-700 dark:focus:ring-blue-700/25';
@endphp
<div class="md:col-span-2">
    <label for="lbsFilterDateFrom" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Completion Date Range</label>
    <div class="flex items-center gap-2">
        <input type="date" id="lbsFilterDateFrom" class="{{ $rangeInputClass }}" aria-label="Completion date from">
        <span class="shrink-0 text-xs text-slate-500 dark:text-slate-400">to</span>
        <input type="date" id="lbsFilterDateTo" class="{{ $rangeInputClass }}" aria-label="Completion date to">
    </div>
</div>
