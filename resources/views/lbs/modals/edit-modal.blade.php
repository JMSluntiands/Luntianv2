<div class="job-view-modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-opacity duration-200" id="jobViewEditModalOverlay" aria-hidden="true">
    <div class="job-view-modal w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-800" id="jobViewEditModal" role="dialog" aria-modal="true" aria-labelledby="jobViewEditModalTitle">
        <div class="sticky top-0 z-10 border-b border-slate-200 bg-white px-5 py-4 dark:border-slate-700 dark:bg-slate-800">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="jobViewEditModalTitle">Edit</h2>
        </div>
        <div class="px-5 py-4">
            <div class="job-view-edit-form job-view-edit-form-client space-y-4" id="jobViewEditFormClient" hidden>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300" for="edit-client-log-date">Log Date</label>
                    <input
                        type="datetime-local"
                        id="edit-client-log-date"
                        class="rounded-lg border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-600 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-400"
                        value="{{ !empty($job->log_date) ? \Carbon\Carbon::parse($job->log_date)->format('Y-m-d\TH:i') : '' }}"
                        readonly
                        autocomplete="off">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300" for="edit-client-ref">Client Reference</label>
                    <input
                        type="text"
                        id="edit-client-ref"
                        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"
                        value="{{ $job->client_reference_no ?? '' }}"
                        autocomplete="off">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300" for="edit-job-number">Job Number</label>
                    <input
                        type="text"
                        id="edit-job-number"
                        class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"
                        value="{{ $job->job_reference_no ?? ($job->reference ?? $jobId ?? '') }}"
                        autocomplete="off">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300" for="edit-compliance">Compliance</label>
                    <select
                        id="edit-compliance"
                        class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"
                        autocomplete="off">
                        @foreach($compliances ?? [] as $c)
                            <option value="{{ $c->column }}" @selected(($job->ncc_compliance ?? '') === ($c->column ?? ''))>
                                {{ $c->column ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300" for="edit-client-name">Client</label>
                    <select
                        id="edit-client-name"
                        class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"
                        autocomplete="off">
                        @foreach($clientAccounts ?? [] as $client)
                            @php
                                $displayName = $client->client_account_name ?? $client->client_code ?? '';
                                $currentId = $job->client_account_id ?? null;
                            @endphp
                            <option value="{{ $client->client_account_id }}"
                                    data-name="{{ $displayName }}"
                                    @selected((int) $currentId === (int) $client->client_account_id)>
                                {{ $displayName }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="job-view-edit-form job-view-edit-form-job space-y-4" id="jobViewEditFormJob" hidden>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300" for="edit-job-status">Job Status</label>
                    <select id="edit-job-status" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200" autocomplete="off">
                        @php
                            $currentStatus = trim($job->job_status ?? '');
                            $currentStatusLower = strtolower($currentStatus);
                        @endphp

                        {{-- Allocation / checking flow:
                             - If Allocated       → options: Accepted, Processing
                             - If Accepted/Processing/Revised → option: For Checking
                             - If For Checking    → options: For Review, Revised
                             - Otherwise          → show all statuses
                        --}}
                        @if($currentStatusLower === 'allocated')
                            {{-- Current Allocated (display only) --}}
                            <option value="{{ $currentStatus }}" selected disabled>{{ $currentStatus }}</option>
                            @foreach($statuses ?? [] as $status)
                                @php
                                    $name = (string) ($status->name ?? '');
                                    $nameLower = strtolower($name);
                                @endphp
                                @if(in_array($nameLower, ['accepted', 'processing'], true))
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endif
                            @endforeach
                        @elseif(in_array($currentStatusLower, ['accepted', 'processing', 'revised'], true))
                            {{-- From Accepted / Processing / Revised → can only move to For Checking --}}
                            <option value="{{ $currentStatus }}" selected disabled>{{ $currentStatus }}</option>
                            @php
                                $forCheckingName = null;
                                foreach ($statuses ?? [] as $s) {
                                    if (strtolower(trim((string)($s->name ?? ''))) === 'for checking') { $forCheckingName = $s->name; break; }
                                }
                            @endphp
                            @if($forCheckingName)
                                <option value="{{ $forCheckingName }}">{{ $forCheckingName }}</option>
                            @endif
                        @elseif($currentStatusLower === 'for checking')
                            {{-- From For Checking → can move to For Review or Revised --}}
                            <option value="{{ $currentStatus }}" selected disabled>{{ $currentStatus }}</option>
                            @foreach($statuses ?? [] as $status)
                                @php
                                    $name = (string) ($status->name ?? '');
                                    $nameLower = strtolower($name);
                                @endphp
                                @if(in_array($nameLower, ['for review', 'revised'], true))
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endif
                            @endforeach
                        @else
                            {{-- Fallback: show all statuses --}}
                            @foreach($statuses ?? [] as $status)
                                <option value="{{ $status->name }}" @selected(($job->job_status ?? '') === $status->name)>{{ $status->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300" for="edit-job-address">Job Address</label>
                    <input
                        type="text"
                        id="edit-job-address"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"
                        value="{{ $job->address_client ?? '' }}"
                        autocomplete="off">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300" for="edit-priority">Priority</label>
                    <select id="edit-priority" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200" autocomplete="off">
                        @foreach($priorities ?? [] as $priority)
                            <option value="{{ $priority->name }}" @selected(($job->priority ?? '') === $priority->name)>{{ $priority->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300" for="edit-job-type">Job Type</label>
                    <select
                        id="edit-job-type"
                        class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"
                        autocomplete="off">
                        <option value="">Select job type</option>
                        @foreach($jobRequests ?? [] as $jobRequest)
                            <option
                                value="{{ $jobRequest->job_request_type ?? '' }}"
                                @selected(($job->job_type ?? '') === ($jobRequest->job_request_type ?? ''))>
                                {{ $jobRequest->job_request_type ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="job-view-edit-form job-view-edit-form-notes" id="jobViewEditFormNotes" hidden>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Notes</label>
                    <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-3 dark:border-slate-600 dark:bg-slate-800/30">
                        <div class="mb-2 flex gap-1">
                            <button type="button" class="rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="bold" title="Bold"><span class="font-bold">B</span></button>
                            <button type="button" class="rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="italic" title="Italic"><span class="italic">I</span></button>
                            <button type="button" class="rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="underline" title="Underline"><span class="underline">U</span></button>
                            <button type="button" class="rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="insertUnorderedList" title="Bullets"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg></button>
                            <button type="button" class="rounded p-1.5 text-slate-600 hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-600" data-cmd="insertOrderedList" title="Numbered list"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg></button>
                        </div>
                        <div id="jobViewEditNotesBody" class="min-h-[100px] rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200" contenteditable="true" data-placeholder="Enter notes...">{!! $job->notes ?: '' !!}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
            <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600" data-job-view-close-edit>Cancel</button>
            <button type="button" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" id="jobViewEditSaveBtn">Save</button>
        </div>
    </div>
</div>
