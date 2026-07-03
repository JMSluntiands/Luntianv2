@extends('layouts.dashboard')

@section('title', 'Fyrs Energy Wise List')

@section('body_class', 'page-lbs-list page-fyrs-list')

@section('content')
    @php
        $fmtDate = function ($value) {
            if ($value === null || $value === '') {
                return '—';
            }
            try {
                return \Carbon\Carbon::parse($value)->format('n/j/Y');
            } catch (\Throwable $e) {
                return (string) $value;
            }
        };
        $fmtTasks = function ($raw) use ($taskLabels) {
            if ($raw === null || $raw === '') {
                return '—';
            }
            $tasks = is_string($raw) ? json_decode($raw, true) : $raw;
            if (! is_array($tasks) || $tasks === []) {
                return '—';
            }
            return collect($tasks)
                ->map(fn ($key) => $taskLabels[$key] ?? $key)
                ->implode(' ');
        };
        $fmtText = fn ($value) => ($value === null || trim((string) $value) === '') ? '—' : trim((string) $value);
        $fmtNotes = function ($html) {
            $plain = trim(strip_tags((string) $html));
            if ($plain === '') {
                return '—';
            }
            return \Illuminate\Support\Str::limit($plain, 80);
        };
        $fmtFeedback = function ($bers, $basix, $commitments) {
            return [
                'bers' => trim((string) ($bers ?? '')),
                'basix' => trim((string) ($basix ?? '')),
                'commitments' => trim((string) ($commitments ?? '')),
            ];
        };
        $columns = [
            ['key' => 'row_num', 'label' => '#', 'width' => '52px', 'cellClass' => 'text-center tabular-nums'],
            ['key' => 'job_date', 'label' => 'Date', 'width' => '96px'],
            ['key' => 'job_number', 'label' => 'Job Ref #', 'width' => '140px'],
            ['key' => 'estate', 'label' => 'Estate', 'width' => '120px'],
            ['key' => 'house_type', 'label' => 'House type', 'width' => '220px'],
            ['key' => 'facade', 'label' => 'Façade', 'width' => '120px'],
            ['key' => 'garage', 'label' => 'Garage', 'width' => '72px', 'cellClass' => 'text-center'],
            ['key' => 'tasks', 'label' => 'Tasks', 'width' => '160px'],
            ['key' => 'notes', 'label' => 'Notes', 'width' => '280px', 'cellClass' => 'whitespace-normal'],
            ['key' => 'stage', 'label' => 'Stage', 'width' => '110px'],
            ['key' => 'climate_zone', 'label' => 'Climate zone', 'width' => '96px', 'cellClass' => 'text-center'],
            ['key' => 'basix_number', 'label' => 'BASIX #', 'width' => '100px'],
            ['key' => 'storeys', 'label' => 'Storeys', 'width' => '72px', 'cellClass' => 'text-center'],
            ['key' => 'due_date', 'label' => 'Due date', 'width' => '96px'],
            ['key' => 'est_completion_certification', 'label' => 'Estimated Completion Certification', 'width' => '180px', 'headerClass' => 'text-red-600 dark:text-red-400'],
            ['key' => 'est_completion_basix', 'label' => 'Estimated Completion BASIX', 'width' => '170px', 'headerClass' => 'text-red-600 dark:text-red-400'],
            ['key' => 'feedback', 'label' => 'Feedback', 'width' => '180px', 'cellClass' => 'fyrs-feedback-cell align-top'],
            ['key' => 'basix_note', 'label' => 'Basix Note', 'width' => '160px', 'cellClass' => 'whitespace-normal'],
        ];
        $colCount = count($columns) + 1;
    @endphp

    <div class="block max-w-full pb-0">
        <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="m-0 mb-1.5 text-[1.625rem] font-bold tracking-tight text-slate-900 dark:text-white">Fyrs Energy Wise List</h1>
                <p class="m-0 text-[0.9375rem] leading-snug text-slate-600 dark:text-slate-400">NatHERS and BASIX assessor workflow — same fields as the add form.</p>
            </div>
            <div class="shrink-0">
                <label for="lbsSearch" class="mb-1.5 block text-xs font-semibold text-slate-600 dark:text-slate-400">Search</label>
                <div class="relative flex min-w-[260px] items-center">
                    <svg class="pointer-events-none absolute left-3 text-slate-500 dark:text-slate-400" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" id="lbsSearch" class="w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3.5 text-sm text-slate-900 placeholder-slate-500 transition-colors focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/25 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:placeholder-slate-500 dark:focus:border-blue-700 dark:focus:ring-blue-700/25" placeholder="Search job ref #, estate, stage..." autocomplete="off" aria-label="Search Fyrs Energy Wise jobs">
                </div>
            </div>
        </div>

        <div class="max-w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow dark:border-slate-700 dark:bg-slate-900">
            <div class="max-w-full overflow-x-auto">
                <table class="lbs-table fyrs-list-table w-full min-w-[2280px] table-fixed border-collapse text-sm" id="lbsTable">
                    <colgroup>
                        <col style="width: 90px">
                        @foreach ($columns as $col)
                            <col style="width: {{ $col['width'] ?? '120px' }}">
                        @endforeach
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="lbs-th-action w-[90px] cursor-default border-b border-slate-200 bg-slate-100 px-3 py-3 text-left align-middle font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400">Action</th>
                            @foreach ($columns as $col)
                                <th class="lbs-th fyrs-list-th cursor-pointer select-none border-b border-slate-200 bg-slate-100 px-3 py-3 text-left align-middle text-xs font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400 dark:hover:text-slate-200 {{ $col['headerClass'] ?? '' }}" data-sort="">
                                    <span>{{ $col['label'] }}</span><span class="lbs-sort-icon ml-1 text-xs opacity-60" aria-hidden="true">↕</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            @php
                                $jobId = (int) ($row->job_id ?? 0);
                                $cellValues = [
                                    'row_num' => $row->id ?? '—',
                                    'job_date' => $fmtDate($row->job_date ?? null),
                                    'job_number' => $fmtText($row->job_number ?? null),
                                    'estate' => $fmtText($row->estate ?? null),
                                    'house_type' => $fmtText($row->house_type ?? null),
                                    'facade' => $fmtText($row->facade ?? null),
                                    'garage' => $fmtText($row->garage ?? null),
                                    'tasks' => $fmtTasks($row->tasks ?? null),
                                    'notes' => $fmtNotes($row->notes ?? null),
                                    'stage' => $fmtText($row->stage ?? null),
                                    'climate_zone' => $fmtText($row->climate_zone ?? null),
                                    'basix_number' => $fmtText($row->basix_number ?? null),
                                    'storeys' => $fmtText($row->storeys ?? null),
                                    'due_date' => $fmtDate($row->due_date ?? null),
                                    'est_completion_certification' => $fmtDate($row->est_completion_certification ?? null),
                                    'est_completion_basix' => $fmtDate($row->est_completion_basix ?? null),
                                    'feedback' => $fmtFeedback(
                                        $row->feedback_bers ?? null,
                                        $row->feedback_basix ?? null,
                                        $row->feedback_commitments_form ?? null
                                    ),
                                    'basix_note' => $fmtText($row->basix_note ?? null),
                                ];
                            @endphp
                            <tr class="lbs-data-row border-b border-slate-200 align-middle text-slate-800 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-white/5">
                                <td class="overflow-visible px-4 py-3 text-center align-middle">
                                    <div class="relative z-10 flex flex-nowrap items-center justify-center gap-1.5">
                                        <a href="{{ route('fyrs.view', $jobId) }}" class="lbs-action-icon inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 no-underline transition-colors hover:bg-green-500/15 hover:text-green-400 dark:text-slate-400 dark:hover:bg-green-500/15 dark:hover:text-green-400" title="View" aria-label="View">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                        <button type="button" class="lbs-action-icon lbs-action-expand inline-flex h-8 w-8 items-center justify-center rounded-lg border-0 bg-transparent p-0 text-slate-400 transition-colors hover:bg-amber-500/15 hover:text-amber-400 dark:text-slate-400 dark:hover:bg-amber-500/15 dark:hover:text-amber-400" title="View full notes" aria-label="Show full notes" aria-expanded="false" data-expand-row>
                                            <svg class="lbs-expand-icon block transition-transform duration-200" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                                        </button>
                                    </div>
                                </td>
                                @foreach ($columns as $col)
                                    @php
                                        $val = $cellValues[$col['key']] ?? '—';
                                        $sortVal = ($col['key'] === 'feedback' && is_array($val))
                                            ? collect($val)->filter()->implode(' ')
                                            : $val;
                                    @endphp
                                    <td class="lbs-td border-b border-slate-200 px-3 py-3 align-middle text-slate-800 dark:border-slate-700 dark:text-slate-200 {{ $col['cellClass'] ?? '' }}" data-sort="{{ $sortVal }}">
                                        @if ($col['key'] === 'feedback' && is_array($val))
                                            <div class="space-y-1.5 text-xs leading-snug">
                                                <div><span class="font-medium text-slate-600 dark:text-slate-400">BERS:</span> {{ $val['bers'] !== '' ? $val['bers'] : '—' }}</div>
                                                <div><span class="font-medium text-slate-600 dark:text-slate-400">BASIX:</span> {{ $val['basix'] !== '' ? $val['basix'] : '—' }}</div>
                                                <div><span class="font-medium text-slate-600 dark:text-slate-400">Commitments Form:</span> {{ $val['commitments'] !== '' ? $val['commitments'] : '—' }}</div>
                                            </div>
                                        @else
                                            {{ $val }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr class="lbs-row-detail border-b border-slate-200 dark:border-slate-700" hidden>
                                <td colspan="{{ $colCount }}" class="bg-slate-50 p-0 align-top dark:bg-slate-900">
                                    <div class="grid gap-6 px-5 py-5 lg:grid-cols-2">
                                        <div>
                                            <span class="mb-2 block text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Notes</span>
                                            <div class="prose prose-sm max-w-none text-slate-800 dark:prose-invert dark:text-slate-200">
                                                {!! $row->notes ?: '<span class="text-slate-400">—</span>' !!}
                                            </div>
                                        </div>
                                        <div>
                                            <span class="mb-2 block text-[0.6875rem] font-bold uppercase tracking-wider text-slate-500">Basix Note</span>
                                            <p class="m-0 whitespace-pre-wrap text-sm text-slate-800 dark:text-slate-200">{{ $fmtText($row->basix_note ?? null) }}</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $colCount }}" class="px-5 py-10 text-center text-sm text-slate-500 dark:text-slate-400">No Fyrs assessor jobs yet. <a href="{{ route('fyrs.add') }}" class="font-medium text-emerald-600 hover:underline dark:text-emerald-400">Add a job</a>.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/lbs-list.js') }}"></script>
@endpush
