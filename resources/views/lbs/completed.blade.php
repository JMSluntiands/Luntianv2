@extends('layouts.dashboard')

@section('title', 'LBS Completed')

@section('body_class', 'page-lbs-completed')

@section('content')
    <div class="block max-w-full pb-0">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-white dark:text-white">LBS Completed</h1>
                <p class="m-0 text-[0.9375rem] leading-snug text-slate-400">View completed LBS jobs.</p>
            </div>
            <div class="shrink-0">
                <label for="lbsSearch" class="mb-1.5 block text-xs font-semibold text-slate-400">Search</label>
                <div class="relative flex min-w-[260px] items-center">
                    <svg class="pointer-events-none absolute left-3 text-slate-500 dark:text-slate-500" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsSearch" class="w-full rounded-lg border border-slate-300 bg-slate-50 py-2 pl-9 pr-3.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:placeholder-slate-500 dark:focus:border-blue-700 dark:focus:ring-blue-700/25" placeholder="Search by client, reference, job type..." autocomplete="off" aria-label="Search completed LBS jobs">
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
            <div class="max-w-full overflow-x-auto">
                <table class="lbs-table w-full min-w-[1320px] table-fixed border-collapse text-sm" id="lbsTable">
                    <colgroup>
                        <col style="width: 110px">
                        <col style="width: 140px">
                        <col style="width: 200px">
                        <col style="width: 90px">
                        <col style="width: 105px">
                        <col style="width: 200px">
                        <col style="width: 150px">
                        <col style="width: 70px">
                        <col style="width: 70px">
                        <col style="width: 200px">
                        <col style="width: 155px">
                        <col style="width: 115px">
                        <col style="width: 95px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="lbs-th-action cursor-default border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400" data-sort=""><span>Action</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Log Date</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Client</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Client Name</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Reference</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Job Type</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Priority</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Staff</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Checker</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Status</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Due Date</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Completion Date</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                            <th class="lbs-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-5 py-3 text-left align-middle font-semibold text-slate-500 whitespace-nowrap dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200" data-sort=""><span>Complexity</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-slate-200 overflow-hidden align-middle text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5">
                            <td class="overflow-visible px-4 py-3 text-center align-middle text-slate-800 dark:text-slate-200" style="white-space: nowrap;">
                                <div class="relative z-10 flex flex-nowrap items-center gap-1.5">
                                    <a href="#" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-blue-900/25 hover:text-blue-300 dark:text-slate-400 dark:hover:bg-blue-900/25 dark:hover:text-blue-300" title="Duplicate" aria-label="Duplicate"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></a>
                                    <a href="#" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-green-500/15 hover:text-green-400 dark:text-slate-400 dark:hover:bg-green-500/15 dark:hover:text-green-400" title="View" aria-label="View"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                                    <button type="button" class="lbs-action-icon lbs-action-expand inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 transition-colors hover:bg-amber-500/15 hover:text-amber-400 dark:text-slate-400 dark:hover:bg-amber-500/15 dark:hover:text-amber-400" title="View full row details below" aria-label="Show full row details" aria-expanded="false" data-expand-row><svg class="lbs-expand-icon block transition-transform duration-200" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></button>
                                </div>
                            </td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Log Date"><span class="block font-medium text-slate-800 dark:text-slate-200">March 1, 2026</span><span class="block text-[0.8125rem] text-slate-400">9:00 AM</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client"><span class="block font-medium text-slate-800 dark:text-slate-200">Summit Homes Group</span><span class="block text-[0.8125rem] text-slate-400">2022 Whole of Home (WOH)</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client Name" style="white-space: nowrap;">LBS</td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Reference" style="white-space: nowrap;">42250</td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Job Type"><span class="block font-medium text-slate-800 dark:text-slate-200">1S DB Base Model</span><span class="block text-[0.8125rem] text-slate-400">1S Design Builder Model</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Priority" style="white-space: nowrap;"><span class="lbs-priority inline-block whitespace-nowrap rounded-md bg-orange-500 px-2 py-1 text-xs font-semibold text-white">High 1 day</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Staff" style="white-space: nowrap;"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">SB</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Checker" style="white-space: nowrap;"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">GM</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Status" style="white-space: nowrap;"><span class="lbs-badge lbs-badge-completed inline-block rounded-md px-2 py-1 text-xs font-semibold">Completed</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Due Date"><span class="block font-medium text-slate-800 dark:text-slate-200">March 3, 2026</span><span class="block text-[0.8125rem] text-slate-400">8:00 AM</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Completion Date"><span class="block font-medium text-slate-800 dark:text-slate-200">March 2, 2026</span><span class="block text-[0.8125rem] text-slate-400">5:30 PM</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Complexity" style="white-space: nowrap;"><span class="lbs-stars inline-flex items-center" data-rating="4" aria-label="4 out of 5">@include('lbs.partials.stars', ['rating' => 4])</span></td>
                        </tr>
                        <tr class="lbs-row-detail border-b border-slate-200 dark:border-slate-700" id="lbs-detail-0" hidden>
                            <td colspan="13" class="bg-slate-50 p-0 align-top dark:bg-slate-900">
                                <div class="grid gap-x-6 gap-y-4 px-5 py-5" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Log Date</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">March 1, 2026 9:00 AM</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Client</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">Summit Homes Group · 2022 Whole of Home (WOH)</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Client Name</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">LBS</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Reference</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">42250</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Job Type</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">1S DB Base Model · 1S Design Builder Model</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Priority</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="lbs-priority inline-block whitespace-nowrap rounded-md bg-orange-500 px-2 py-1 text-xs font-semibold text-white mt-0.5">High 1 day</span></span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Staff</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 mt-0.5 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">SB</span></span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Checker</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 mt-0.5 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">GM</span></span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Status</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="lbs-badge lbs-badge-completed inline-block rounded-md px-2 py-1 text-xs font-semibold mt-0.5">Completed</span></span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Due Date</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">March 3, 2026 8:00 AM</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Completion Date</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">March 2, 2026 5:30 PM</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Complexity</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">@include('lbs.partials.stars', ['rating' => 4])</span></div>
                                </div>
                            </td>
                        </tr>
                        <tr class="border-b border-slate-200 overflow-hidden align-middle text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5">
                            <td class="overflow-visible px-4 py-3 text-center align-middle text-slate-800 dark:text-slate-200" style="white-space: nowrap;">
                                <div class="relative z-10 flex flex-nowrap items-center gap-1.5">
                                    <a href="#" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-blue-900/25 hover:text-blue-300 dark:text-slate-400 dark:hover:bg-blue-900/25 dark:hover:text-blue-300" title="Duplicate" aria-label="Duplicate"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></a>
                                    <a href="#" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-green-500/15 hover:text-green-400 dark:text-slate-400 dark:hover:bg-green-500/15 dark:hover:text-green-400" title="View" aria-label="View"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                                    <button type="button" class="lbs-action-icon lbs-action-expand inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 transition-colors hover:bg-amber-500/15 hover:text-amber-400 dark:text-slate-400 dark:hover:bg-amber-500/15 dark:hover:text-amber-400" title="View full row details below" aria-label="Show full row details" aria-expanded="false" data-expand-row><svg class="lbs-expand-icon block transition-transform duration-200" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></button>
                                </div>
                            </td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Log Date"><span class="block font-medium text-slate-800 dark:text-slate-200">February 28, 2026</span><span class="block text-[0.8125rem] text-slate-400">2:15 PM</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client"><span class="block font-medium text-slate-800 dark:text-slate-200">Leigh Homes</span><span class="block text-[0.8125rem] text-slate-400">2022 Whole of Home (WOH)</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client Name" style="white-space: nowrap;">LBS</td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Reference" style="white-space: nowrap;">42230</td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Job Type"><span class="block font-medium text-slate-800 dark:text-slate-200">2S DB Base Model</span><span class="block text-[0.8125rem] text-slate-400">2S Design Builder Model</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Priority" style="white-space: nowrap;"><span class="lbs-priority inline-block whitespace-nowrap rounded-md bg-violet-600 px-2 py-1 text-xs font-semibold text-white">Standard 2 days</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Staff" style="white-space: nowrap;"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">JDR</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Checker" style="white-space: nowrap;"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">JDR</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Status" style="white-space: nowrap;"><span class="lbs-badge lbs-badge-completed inline-block rounded-md px-2 py-1 text-xs font-semibold">Completed</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Due Date"><span class="block font-medium text-slate-800 dark:text-slate-200">March 2, 2026</span><span class="block text-[0.8125rem] text-slate-400">8:00 AM</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Completion Date"><span class="block font-medium text-slate-800 dark:text-slate-200">March 1, 2026</span><span class="block text-[0.8125rem] text-slate-400">11:00 AM</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Complexity" style="white-space: nowrap;"><span class="lbs-stars inline-flex items-center" data-rating="3" aria-label="3 out of 5">@include('lbs.partials.stars', ['rating' => 3])</span></td>
                        </tr>
                        <tr class="lbs-row-detail border-b border-slate-200 dark:border-slate-700" id="lbs-detail-1" hidden>
                            <td colspan="13" class="bg-slate-50 p-0 align-top dark:bg-slate-900">
                                <div class="grid gap-x-6 gap-y-4 px-5 py-5" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Log Date</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">February 28, 2026 2:15 PM</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Client</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">Leigh Homes · 2022 Whole of Home (WOH)</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Client Name</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">LBS</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Reference</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">42230</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Job Type</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">2S DB Base Model · 2S Design Builder Model</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Priority</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="lbs-priority inline-block whitespace-nowrap rounded-md bg-violet-600 px-2 py-1 text-xs font-semibold text-white mt-0.5">Standard 2 days</span></span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Staff</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 mt-0.5 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">JDR</span></span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Checker</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 mt-0.5 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">JDR</span></span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Status</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="lbs-badge lbs-badge-completed inline-block rounded-md px-2 py-1 text-xs font-semibold mt-0.5">Completed</span></span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Due Date</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">March 2, 2026 8:00 AM</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Completion Date</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">March 1, 2026 11:00 AM</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Complexity</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">@include('lbs.partials.stars', ['rating' => 3])</span></div>
                                </div>
                            </td>
                        </tr>
                        <tr class="border-b border-slate-200 overflow-hidden align-middle text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5">
                            <td class="overflow-visible px-4 py-3 text-center align-middle text-slate-800 dark:text-slate-200" style="white-space: nowrap;">
                                <div class="relative z-10 flex flex-nowrap items-center gap-1.5">
                                    <a href="#" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-blue-900/25 hover:text-blue-300 dark:text-slate-400 dark:hover:bg-blue-900/25 dark:hover:text-blue-300" title="Duplicate" aria-label="Duplicate"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></a>
                                    <a href="#" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-green-500/15 hover:text-green-400 dark:text-slate-400 dark:hover:bg-green-500/15 dark:hover:text-green-400" title="View" aria-label="View"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                                    <button type="button" class="lbs-action-icon lbs-action-expand inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 transition-colors hover:bg-amber-500/15 hover:text-amber-400 dark:text-slate-400 dark:hover:bg-amber-500/15 dark:hover:text-amber-400" title="View full row details below" aria-label="Show full row details" aria-expanded="false" data-expand-row><svg class="lbs-expand-icon block transition-transform duration-200" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></button>
                                </div>
                            </td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Log Date"><span class="block font-medium text-slate-800 dark:text-slate-200">February 26, 2026</span><span class="block text-[0.8125rem] text-slate-400">10:45 AM</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client"><span class="block font-medium text-slate-800 dark:text-slate-200">Non Account</span><span class="block text-[0.8125rem] text-slate-400">2022 Whole of Home (WOH)</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Client Name" style="white-space: nowrap;">LBS</td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Reference" style="white-space: nowrap;">42201</td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Job Type"><span class="block font-medium text-slate-800 dark:text-slate-200">1S DB Base Model</span><span class="block text-[0.8125rem] text-slate-400">1S Design Builder Model</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Priority" style="white-space: nowrap;"><span class="lbs-priority inline-block whitespace-nowrap rounded-md bg-orange-500 px-2 py-1 text-xs font-semibold text-white">High 1 day</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Staff" style="white-space: nowrap;"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">PEP</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Checker" style="white-space: nowrap;"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">GM</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Status" style="white-space: nowrap;"><span class="lbs-badge lbs-badge-completed inline-block rounded-md px-2 py-1 text-xs font-semibold">Completed</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Due Date"><span class="block font-medium text-slate-800 dark:text-slate-200">February 27, 2026</span><span class="block text-[0.8125rem] text-slate-400">8:00 AM</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Completion Date"><span class="block font-medium text-slate-800 dark:text-slate-200">February 27, 2026</span><span class="block text-[0.8125rem] text-slate-400">9:20 AM</span></td>
                            <td class="border-b border-slate-200 px-4 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200" data-label="Complexity" style="white-space: nowrap;"><span class="lbs-stars inline-flex items-center" data-rating="4" aria-label="4 out of 5">@include('lbs.partials.stars', ['rating' => 4])</span></td>
                        </tr>
                        <tr class="lbs-row-detail border-b border-slate-200 dark:border-slate-700" id="lbs-detail-2" hidden>
                            <td colspan="13" class="bg-slate-50 p-0 align-top dark:bg-slate-900">
                                <div class="grid gap-x-6 gap-y-4 px-5 py-5" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Log Date</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">February 26, 2026 10:45 AM</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Client</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">Non Account · 2022 Whole of Home (WOH)</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Client Name</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">LBS</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Reference</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">42201</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Job Type</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">1S DB Base Model · 1S Design Builder Model</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Priority</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="lbs-priority inline-block whitespace-nowrap rounded-md bg-orange-500 px-2 py-1 text-xs font-semibold text-white mt-0.5">High 1 day</span></span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Staff</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 mt-0.5 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">PEP</span></span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Checker</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 mt-0.5 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200">GM</span></span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Status</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200"><span class="lbs-badge lbs-badge-completed inline-block rounded-md px-2 py-1 text-xs font-semibold mt-0.5">Completed</span></span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Due Date</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">February 27, 2026 8:00 AM</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Completion Date</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">February 27, 2026 9:20 AM</span></div>
                                    <div class="flex flex-col gap-1.5"><span class="text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Complexity</span><span class="text-[0.9375rem] font-medium leading-snug text-slate-800 dark:text-slate-200">@include('lbs.partials.stars', ['rating' => 4])</span></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.lbs-th[data-sort="asc"] .lbs-sort-icon { font-size: 0; }
.lbs-th[data-sort="asc"] .lbs-sort-icon::before { content: '↑'; font-size: 0.75rem; }
.lbs-th[data-sort="desc"] .lbs-sort-icon { font-size: 0; }
.lbs-th[data-sort="desc"] .lbs-sort-icon::before { content: '↓'; font-size: 0.75rem; }
.lbs-th:not([data-sort=""]) .lbs-sort-icon { opacity: 1; }
.lbs-action-expand[aria-expanded="true"] .lbs-expand-icon { transform: rotate(180deg); }
.lbs-badge-completed { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
</style>
    @endpush

@push('scripts')
    <script>
        (function() {
            var searchEl = document.getElementById('lbsSearch');
            var table = document.getElementById('lbsTable');
            if (searchEl && table) {
                var tbody = table.querySelector('tbody');
                searchEl.addEventListener('input', function() {
                    var q = (this.value || '').trim().toLowerCase();
                    if (!tbody) return;
                    var rows = tbody.querySelectorAll('tr:not(.lbs-row-detail)');
                    rows.forEach(function(tr) {
                        var text = (tr.textContent || '').toLowerCase();
                        var match = !q || text.indexOf(q) !== -1;
                        tr.style.display = match ? '' : 'none';
                        var next = tr.nextElementSibling;
                        if (next && next.classList.contains('lbs-row-detail')) next.style.display = match ? '' : 'none';
                    });
                });
            }
            document.querySelectorAll('[data-expand-row]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var row = this.closest('tr');
                    var next = row.nextElementSibling;
                    var isDetail = next && next.classList.contains('lbs-row-detail');
                    if (!isDetail) return;
                    var open = next.hidden;
                    next.hidden = !open;
                    this.setAttribute('aria-expanded', open);
                    this.setAttribute('title', open ? 'Hide details' : 'View full row details below');
                });
            });
            if (!table) return;
            var thead = table.querySelector('thead');
            thead.addEventListener('click', function(e) {
                var th = e.target.closest('th');
                if (!th || th.classList.contains('lbs-th-action')) return;
                var current = th.getAttribute('data-sort') || '';
                var next = current === 'asc' ? 'desc' : 'asc';
                thead.querySelectorAll('th').forEach(function(h) { h.setAttribute('data-sort', ''); });
                th.setAttribute('data-sort', next);
                var colIndex = Array.prototype.indexOf.call(thead.querySelectorAll('th'), th);
                var tbody = table.querySelector('tbody');
                var allRows = Array.from(tbody.querySelectorAll('tr'));
                var dataRows = allRows.filter(function(r) { return !r.classList.contains('lbs-row-detail'); });
                dataRows.sort(function(a, b) {
            var aCell = a.children[colIndex], bCell = b.children[colIndex];
                    var aVal = (aCell && (aCell.getAttribute('data-sort') || aCell.textContent)) || '';
                    var bVal = (bCell && (bCell.getAttribute('data-sort') || bCell.textContent)) || '';
            var aNum = parseFloat(aVal), bNum = parseFloat(bVal);
            if (!isNaN(aNum) && !isNaN(bNum)) return next === 'asc' ? aNum - bNum : bNum - aNum;
                    if (next === 'asc') return String(aVal).localeCompare(String(bVal), undefined, { numeric: true });
                    return String(bVal).localeCompare(String(aVal), undefined, { numeric: true });
                });
                dataRows.forEach(function(r) {
                    tbody.appendChild(r);
                    var detail = r.nextElementSibling;
                    if (detail && detail.classList.contains('lbs-row-detail')) tbody.appendChild(detail);
                });
            });
        })();
    </script>
@endpush
