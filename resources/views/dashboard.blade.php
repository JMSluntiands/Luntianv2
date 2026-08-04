@php
    $sidebar_active = 'dashboard';
    $dashboardStats = $dashboardStats ?? \App\Services\DashboardJobStatsService::fetch();
    $dashboardStatusChart = $dashboardStatusChart ?? \App\Services\DashboardJobStatsService::fetchStatusChart();
    $dashboardBranchFilter = \App\Models\RolePermission::dashboardStatCardsBranchFilter();
    $labelOrder = \App\Models\RolePermission::dashboardStatCardLabels();
    $dashOrderBucket = static function (array $bucket) use ($labelOrder): array {
        $out = [];
        foreach ($labelOrder as $k) {
            $out[$k] = (int) ($bucket[$k] ?? 0);
        }

        return $out;
    };
    $dashPick = static function (array $labelToCount) use ($dashboardBranchFilter): array {
        if ($dashboardBranchFilter === '') {
            return $labelToCount;
        }
        foreach ($labelToCount as $label => $num) {
            if (strcasecmp((string) $label, $dashboardBranchFilter) === 0) {
                return [$label => $num];
            }
        }

        return [$dashboardBranchFilter => 0];
    };
    $dTotal = $dashPick($dashOrderBucket($dashboardStats['total'] ?? []));
    $dCompleted = $dashPick($dashOrderBucket($dashboardStats['completed'] ?? []));
    $dProcessing = $dashPick($dashOrderBucket($dashboardStats['processing'] ?? []));
    $dPending = $dashPick($dashOrderBucket($dashboardStats['pending'] ?? []));
@endphp
@extends('layouts.dashboard')

@section('title', 'Dashboard')

@push('styles')
    <style>
        /* Force solid stat cards for both Blade fallback and React render */
        .dashboard-card::before {
            content: none !important;
            display: none !important;
            background: none !important;
        }

        .animate-dashboard-card > .pointer-events-none.absolute.inset-0 {
            display: none !important;
        }

        /* Soft amber cards — easy on eyes in light + dark */
        .dashboard-card--total,
        .dashboard-card--completed,
        .dashboard-card--processing,
        .dashboard-card--pending,
        .dashboard-cards > .animate-dashboard-card:nth-child(1),
        .dashboard-cards > .animate-dashboard-card:nth-child(2),
        .dashboard-cards > .animate-dashboard-card:nth-child(3),
        .dashboard-cards > .animate-dashboard-card:nth-child(4) {
            background: #F0C48A !important;
            color: #422006 !important;
        }

        html.dark .dashboard-card--total,
        html.dark .dashboard-card--completed,
        html.dark .dashboard-card--processing,
        html.dark .dashboard-card--pending,
        html.dark .dashboard-cards > .animate-dashboard-card:nth-child(1),
        html.dark .dashboard-cards > .animate-dashboard-card:nth-child(2),
        html.dark .dashboard-cards > .animate-dashboard-card:nth-child(3),
        html.dark .dashboard-cards > .animate-dashboard-card:nth-child(4),
        .dark .dashboard-card--total,
        .dark .dashboard-card--completed,
        .dark .dashboard-card--processing,
        .dark .dashboard-card--pending,
        .dark .dashboard-cards > .animate-dashboard-card:nth-child(1),
        .dark .dashboard-cards > .animate-dashboard-card:nth-child(2),
        .dark .dashboard-cards > .animate-dashboard-card:nth-child(3),
        .dark .dashboard-cards > .animate-dashboard-card:nth-child(4) {
            background: #A67C3A !important;
            color: #FFF7ED !important;
        }
    </style>
@endpush

@section('content')
    @php
        $dashboardPublicHolidaysYear = $dashboardPublicHolidaysYear ?? (int) now()->format('Y');
        $dashboardPublicHolidays = $dashboardPublicHolidays ?? \App\Services\PublicHolidayService::forYear($dashboardPublicHolidaysYear);
        $bladeHolidayMonth = now()->format('Y-m');
        $bladePhMonth = [];
        foreach ($dashboardPublicHolidays['ph'] ?? [] as $_h) {
            if (is_array($_h) && str_starts_with((string) ($_h['date'] ?? ''), $bladeHolidayMonth)) {
                $bladePhMonth[] = $_h;
            }
        }
        usort($bladePhMonth, static fn ($a, $b) => strcmp((string) ($a['date'] ?? ''), (string) ($b['date'] ?? '')));
        $bladeAuMonth = [];
        foreach ($dashboardPublicHolidays['au'] ?? [] as $_h) {
            if (is_array($_h) && str_starts_with((string) ($_h['date'] ?? ''), $bladeHolidayMonth)) {
                $bladeAuMonth[] = $_h;
            }
        }
        usort($bladeAuMonth, static fn ($a, $b) => strcmp((string) ($a['date'] ?? ''), (string) ($b['date'] ?? '')));
        $holidayDateHasPh = [];
        foreach ($dashboardPublicHolidays['ph'] ?? [] as $_h) {
            if (is_array($_h) && ! empty($_h['date'])) {
                $holidayDateHasPh[substr((string) $_h['date'], 0, 10)] = true;
            }
        }
        $holidayDateHasAu = [];
        foreach ($dashboardPublicHolidays['au'] ?? [] as $_h) {
            if (is_array($_h) && ! empty($_h['date'])) {
                $holidayDateHasAu[substr((string) $_h['date'], 0, 10)] = true;
            }
        }
    @endphp
    <script type="application/json" id="dashboard-stats-json">@json($dashboardStats)</script>
    <script type="application/json" id="dashboard-chart-json">@json($dashboardStatusChart)</script>
    <script type="application/json" id="dashboard-announcement-json">@json(['announcements' => $dashboardAnnouncements ?? []])</script>
    <script type="application/json" id="dashboard-holidays-initial" data-year="{{ $dashboardPublicHolidaysYear }}">@json($dashboardPublicHolidays)</script>
    <script type="application/json" id="dashboard-attendance-json">@json($dashboardAttendance ?? null)</script>
    <div class="dashboard-layout w-full min-w-0">
        <div
            id="dashboard-root"
            class="dashboard-layout__main w-full min-w-0"
            data-dashboard-branch-filter="{{ $dashboardBranchFilter }}"
            data-holidays-api-base="/dashboard/holidays"
            data-stats-api-base="{{ route('dashboard.stats', [], false) }}"
            data-chart-api-base="{{ route('dashboard.chart', [], false) }}"
            data-announcement-list-url="{{ route('forum_thread', [], false) }}"
            data-attendance-clock-in-url="{{ route('attendance.clockIn', [], false) }}"
            data-attendance-clock-out-url="{{ route('attendance.clockOut', [], false) }}"
        >
        {{-- Fallback: visible if React has not mounted yet or JS fails --}}
        <div class="dashboard-page" data-dashboard-fallback>
            <header class="dashboard-page__header mb-2 flex flex-col gap-3 pb-2 sm:flex-row sm:items-start sm:justify-between">
                <div class="min-w-0">
                    <h1 class="dashboard-page__title">Dashboard</h1>
                    <p class="dashboard-page__subtitle">As of today, {{ now('Asia/Manila')->format('F j, Y') }} &mdash; overview of your jobs and calendar.</p>
                </div>
                @php
                    $dashTzOptions = [
                        'Asia/Manila' => ['short' => 'PHT', 'label' => 'Philippines (PHT)'],
                        'Australia/Sydney' => ['short' => 'AEST', 'label' => 'Australia — Sydney'],
                        'Australia/Perth' => ['short' => 'AWST', 'label' => 'Australia — Perth'],
                        'UTC' => ['short' => 'UTC', 'label' => 'UTC'],
                    ];
                    $dashTz = 'Asia/Manila';
                    $dashNow = now($dashTz);
                @endphp
                <div class="relative shrink-0 self-start sm:self-center" data-dashboard-tz-widget>
                    <div class="inline-flex items-center overflow-hidden rounded-md bg-[#0b2a4a] text-[11px] font-semibold uppercase tracking-wide text-white shadow-sm sm:text-xs">
                        <span class="px-3 py-2 tabular-nums" data-dashboard-tz-date>{{ strtoupper($dashNow->format('M j, Y')) }}</span>
                        <span class="w-px self-stretch bg-white/35" aria-hidden="true"></span>
                        <span class="px-3 py-2 tabular-nums" data-dashboard-tz-time>{{ strtoupper($dashNow->format('g:i A')) }}</span>
                        <span class="w-px self-stretch bg-white/35" aria-hidden="true"></span>
                        <button type="button" class="inline-flex cursor-pointer items-center gap-1.5 px-3 py-2 hover:bg-white/10" data-dashboard-tz-toggle aria-haspopup="listbox" aria-expanded="false" title="Change timezone">
                            <span data-dashboard-tz-short>{{ $dashTzOptions[$dashTz]['short'] }}</span>
                            <svg class="h-3 w-3 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    <ul class="absolute right-0 z-50 mt-1.5 hidden min-w-[12.5rem] overflow-hidden rounded-lg border border-slate-200 bg-white py-1 shadow-lg dark:border-slate-600 dark:bg-slate-800" role="listbox" aria-label="Timezone" data-dashboard-tz-menu>
                        @foreach ($dashTzOptions as $tzId => $tzMeta)
                            <li role="option">
                                <button type="button" class="flex w-full cursor-pointer items-center justify-between gap-3 px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700/60" data-dashboard-tz-option data-tz="{{ $tzId }}" data-short="{{ $tzMeta['short'] }}">
                                    <span>{{ $tzMeta['label'] }}</span>
                                    <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $tzMeta['short'] }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </header>
            @php
                $att = $dashboardAttendance ?? [
                    'clocked_in' => false,
                    'clocked_out' => false,
                    'clocked_in_at' => null,
                    'clocked_out_at' => null,
                    'local_time' => now('Asia/Manila')->format('g:i A'),
                    'cutoff_label' => '8:00 AM',
                    'timezone_label' => 'Philippines (PHT)',
                    'can_clock_in' => true,
                    'can_clock_out' => false,
                ];
                $attCanAct = !empty($att['can_clock_in']) || !empty($att['can_clock_out']);
                if (!empty($att['clocked_out'])) {
                    $attBtnLabel = 'CLOCKED OUT';
                } elseif (!empty($att['can_clock_out'])) {
                    $attBtnLabel = 'CLOCK OUT';
                } elseif (!empty($att['clocked_in'])) {
                    $attBtnLabel = 'CLOCKED IN';
                } else {
                    $attBtnLabel = 'CLOCK IN';
                }
            @endphp
            <section class="mb-5 flex flex-col gap-3 rounded-xl border border-slate-200/90 bg-white px-4 py-3.5 shadow-sm dark:border-slate-700/70 dark:bg-slate-800/90 sm:flex-row sm:items-center sm:justify-between sm:gap-4 sm:px-5" data-attendance-banner>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-base font-semibold tracking-tight text-slate-900 dark:text-slate-100">Daily attendance</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-700/80 dark:text-slate-300">
                            <span aria-hidden class="text-[0.8125rem] leading-none">🇵🇭</span>
                            {{ $att['timezone_label'] }}
                        </span>
                    </div>
                    <p class="mt-1.5 text-sm leading-snug text-slate-500 dark:text-slate-400">
                        @if(!empty($att['clocked_out']))
                            You clocked in at <span class="font-medium text-slate-700 dark:text-slate-200">{{ $att['clocked_in_at'] }}</span>
                            and clocked out at <span class="font-medium text-slate-700 dark:text-slate-200">{{ $att['clocked_out_at'] }}</span>
                            ({{ $att['timezone_label'] }}). Local time now:
                            <span class="font-medium text-slate-700 dark:text-slate-200" data-attendance-local-time>{{ $att['local_time'] }}</span>
                        @elseif(!empty($att['clocked_in']))
                            You clocked in at <span class="font-medium text-slate-700 dark:text-slate-200">{{ $att['clocked_in_at'] }}</span>
                            ({{ $att['timezone_label'] }}). Local time now:
                            <span class="font-medium text-slate-700 dark:text-slate-200" data-attendance-local-time>{{ $att['local_time'] }}</span>
                        @else
                            Staff who have not clocked in by {{ $att['cutoff_label'] }} ({{ $att['timezone_label'] }}) will appear as absent.
                            Local time now: <span class="font-medium text-slate-700 dark:text-slate-200" data-attendance-local-time>{{ $att['local_time'] }}</span>
                        @endif
                    </p>
                </div>
                <button
                    type="button"
                    class="inline-flex shrink-0 items-center justify-center rounded-lg px-5 py-2.5 text-sm font-semibold tracking-wide text-white transition-colors {{ $attCanAct ? 'cursor-pointer bg-slate-900 hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white' : 'cursor-not-allowed bg-slate-400 dark:bg-slate-600 dark:text-slate-300' }}"
                    data-attendance-action
                    data-clock-in-url="{{ route('attendance.clockIn') }}"
                    data-clock-out-url="{{ route('attendance.clockOut') }}"
                    data-can-clock-in="{{ !empty($att['can_clock_in']) ? '1' : '0' }}"
                    data-can-clock-out="{{ !empty($att['can_clock_out']) ? '1' : '0' }}"
                    @disabled(!$attCanAct)
                >
                    {{ $attBtnLabel }}
                </button>
            </section>
            <section class="dashboard-cards">
                <div class="dashboard-card dashboard-card--total" data-dashboard-card>
                    <div class="dashboard-card__gradient" aria-hidden></div>
                    <div class="dashboard-card__inner">
                        <div class="dashboard-card__header">
                            <span class="dashboard-card__label">Total Jobs</span>
                            <p class="dashboard-card__value" data-dashboard-stat="total" data-dashboard-scope="sum">{{ array_sum($dTotal) }}</p>
                        </div>
                        <span class="dashboard-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="width:1.75rem;height:1.75rem"><path d="M12 12h.01"/><path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><path d="M22 13a18.15 18.15 0 0 1-20 0"/><rect width="20" height="14" x="2" y="6" rx="2"/></svg></span>
                        <div id="dashboard-card-panel-total" class="dashboard-card__collapse" hidden>
                            <div class="dashboard-card__sep"></div>
                            <div class="dashboard-card__rows">
                                @foreach ($dTotal as $rowLabel => $rowValue)
                                    <div class="dashboard-card__row">
                                        <span class="dashboard-card__row-label-group">
                                            <span class="dashboard-card__row-label dashboard-card__row-status">Total jobs</span>
                                            <span class="dashboard-card__row-meta">Branch: <strong>{{ $rowLabel }}</strong></span>
                                        </span>
                                        <span class="dashboard-card__row-value" data-dashboard-stat="total" data-dashboard-branch="{{ $rowLabel }}">{{ $rowValue }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-card dashboard-card--completed" data-dashboard-card>
                    <div class="dashboard-card__gradient" aria-hidden></div>
                    <div class="dashboard-card__inner">
                        <div class="dashboard-card__header">
                            <span class="dashboard-card__label">Completed Jobs</span>
                            <p class="dashboard-card__value" data-dashboard-stat="completed" data-dashboard-scope="sum">{{ array_sum($dCompleted) }}</p>
                        </div>
                        <span class="dashboard-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="width:1.75rem;height:1.75rem"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg></span>
                        <div id="dashboard-card-panel-completed" class="dashboard-card__collapse" hidden>
                            <div class="dashboard-card__sep"></div>
                            <div class="dashboard-card__rows">
                                @foreach ($dCompleted as $rowLabel => $rowValue)
                                    <div class="dashboard-card__row">
                                        <span class="dashboard-card__row-label-group">
                                            <span class="dashboard-card__row-label dashboard-card__row-status">Completed</span>
                                            <span class="dashboard-card__row-meta">Branch: <strong>{{ $rowLabel }}</strong></span>
                                        </span>
                                        <span class="dashboard-card__row-value" data-dashboard-stat="completed" data-dashboard-branch="{{ $rowLabel }}">{{ $rowValue }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-card dashboard-card--processing" data-dashboard-card>
                    <div class="dashboard-card__gradient" aria-hidden></div>
                    <div class="dashboard-card__inner">
                        <div class="dashboard-card__header">
                            <span class="dashboard-card__label">Processing</span>
                            <p class="dashboard-card__value" data-dashboard-stat="processing" data-dashboard-scope="sum">{{ array_sum($dProcessing) }}</p>
                        </div>
                        <span class="dashboard-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="width:1.75rem;height:1.75rem"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span>
                        <div id="dashboard-card-panel-processing" class="dashboard-card__collapse" hidden>
                            <div class="dashboard-card__sep"></div>
                            <div class="dashboard-card__rows">
                                @foreach ($dProcessing as $rowLabel => $rowValue)
                                    <div class="dashboard-card__row">
                                        <span class="dashboard-card__row-label-group">
                                            <span class="dashboard-card__row-label dashboard-card__row-status">Processing</span>
                                            <span class="dashboard-card__row-meta">Branch: <strong>{{ $rowLabel }}</strong></span>
                                        </span>
                                        <span class="dashboard-card__row-value" data-dashboard-stat="processing" data-dashboard-branch="{{ $rowLabel }}">{{ $rowValue }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-card dashboard-card--pending" data-dashboard-card>
                    <div class="dashboard-card__gradient" aria-hidden></div>
                    <div class="dashboard-card__inner">
                        <div class="dashboard-card__header">
                            <span class="dashboard-card__label">Pending</span>
                            <p class="dashboard-card__value" data-dashboard-stat="pending" data-dashboard-scope="sum">{{ array_sum($dPending) }}</p>
                        </div>
                        <span class="dashboard-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="width:1.75rem;height:1.75rem"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg></span>
                        <div id="dashboard-card-panel-pending" class="dashboard-card__collapse" hidden>
                            <div class="dashboard-card__sep"></div>
                            <div class="dashboard-card__rows">
                                @foreach ($dPending as $rowLabel => $rowValue)
                                    <div class="dashboard-card__row">
                                        <span class="dashboard-card__row-label-group">
                                            <span class="dashboard-card__row-label dashboard-card__row-status">Pending</span>
                                            <span class="dashboard-card__row-meta">Branch: <strong>{{ $rowLabel }}</strong></span>
                                        </span>
                                        <span class="dashboard-card__row-value" data-dashboard-stat="pending" data-dashboard-branch="{{ $rowLabel }}">{{ $rowValue }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="mb-6 mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2 lg:items-stretch">
                <div class="min-w-0">
                    @include('layouts.partials.dashboard-status-chart-fallback')
                </div>
                <div class="min-w-0">
                    @include('layouts.partials.dashboard-announcement-fallback')
                </div>
            </section>
            <section class="dashboard-section">
                <div class="dashboard-panel">
                    <h2 class="dashboard-panel__header">
                        <span class="dashboard-panel__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.25rem;height:1.25rem"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                        Calendar
                    </h2>
                    <div class="dashboard-panel__body">
                        <div id="calendar-root" class="dashboard-calendar-wrapper" role="application" aria-label="Month calendar">
                            @include('layouts.partials.dashboard-calendar-fallback', ['holidayPayload' => $dashboardPublicHolidays])
                        </div>
                    </div>
                </div>
                <div class="dashboard-panel">
                    <h2 class="dashboard-panel__header">
                        <span class="dashboard-panel__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.25rem;height:1.25rem"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg></span>
                        Holidays
                    </h2>
                    <div class="dashboard-panel__body">
                        <div class="dashboard-holidays">
                            <div class="dashboard-holiday-box">
                                <div class="dashboard-holiday-box__title">Philippine Holidays</div>
                                <div class="dashboard-holiday-box__text">
                                    @forelse ($bladePhMonth as $_row)
                                        @php
                                            $_dk = substr((string) ($_row['date'] ?? ''), 0, 10);
                                            $_bothRow = ($holidayDateHasPh[$_dk] ?? false) && ($holidayDateHasAu[$_dk] ?? false);
                                            $_rv = $_bothRow ? 'both' : 'ph';
                                        @endphp
                                        <div class="dashboard-holiday-fallback-line dashboard-holiday-row--{{ $_rv }}">
                                            <strong class="tabular-nums">{{ \Illuminate\Support\Carbon::parse($_row['date'])->format('M j (D)') }}</strong>
                                            — {{ $_row['localName'] ?? $_row['name'] ?? 'Holiday' }}
                                        </div>
                                    @empty
                                        No holidays this month
                                    @endforelse
                                </div>
                            </div>
                            <div class="dashboard-holiday-box">
                                <div class="dashboard-holiday-box__title">Australian Holidays</div>
                                <div class="dashboard-holiday-box__text">
                                    @forelse ($bladeAuMonth as $_row)
                                        @php
                                            $_dk = substr((string) ($_row['date'] ?? ''), 0, 10);
                                            $_bothRow = ($holidayDateHasPh[$_dk] ?? false) && ($holidayDateHasAu[$_dk] ?? false);
                                            $_rv = $_bothRow ? 'both' : 'au';
                                        @endphp
                                        <div class="dashboard-holiday-fallback-line dashboard-holiday-row--{{ $_rv }}">
                                            <strong class="tabular-nums">{{ \Illuminate\Support\Carbon::parse($_row['date'])->format('M j (D)') }}</strong>
                                            — {{ $_row['localName'] ?? $_row['name'] ?? 'Holiday' }}
                                        </div>
                                    @empty
                                        No holidays this month
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/dashboard.tsx'])
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var cards = Array.prototype.slice.call(document.querySelectorAll('[data-dashboard-card]'));
            if (cards.length) {
                function setAllExpanded(open) {
                    cards.forEach(function (card) {
                        card.classList.toggle('is-expanded', open);
                        card.setAttribute('aria-expanded', open ? 'true' : 'false');
                        var panel = card.querySelector('.dashboard-card__collapse');
                        if (!panel) return;
                        if (open) {
                            panel.removeAttribute('hidden');
                        } else {
                            panel.setAttribute('hidden', '');
                        }
                    });
                }

                cards.forEach(function (card) {
                    card.setAttribute('role', 'button');
                    card.setAttribute('tabindex', '0');
                    card.setAttribute('aria-expanded', 'false');
                    card.style.cursor = 'pointer';

                    card.addEventListener('click', function () {
                        var open = card.getAttribute('aria-expanded') !== 'true';
                        setAllExpanded(open);
                    });

                    card.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            var open = card.getAttribute('aria-expanded') !== 'true';
                            setAllExpanded(open);
                        }
                    });
                });
            }

            var clockBtn = document.querySelector('[data-attendance-action]');
            if (clockBtn && !clockBtn.disabled) {
                clockBtn.addEventListener('click', function () {
                    var canOut = clockBtn.getAttribute('data-can-clock-out') === '1';
                    var canIn = clockBtn.getAttribute('data-can-clock-in') === '1';
                    var url = canOut
                        ? clockBtn.getAttribute('data-clock-out-url')
                        : (canIn ? clockBtn.getAttribute('data-clock-in-url') : null);
                    if (!url) return;
                    clockBtn.disabled = true;
                    clockBtn.textContent = 'Saving…';
                    var token = document.querySelector('meta[name="csrf-token"]');
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token ? (token.getAttribute('content') || '') : ''
                        },
                        credentials: 'same-origin'
                    }).then(function (res) { return res.json().then(function (body) { return { res: res, body: body }; }); })
                    .then(function (payload) {
                        if (!payload.res.ok || !payload.body || payload.body.status !== 'success') {
                            clockBtn.disabled = false;
                            clockBtn.textContent = canOut ? 'CLOCK OUT' : 'CLOCK IN';
                            if (window.showSuccessToast) window.showSuccessToast((payload.body && payload.body.message) || (canOut ? 'Could not clock out.' : 'Could not clock in.'));
                            return;
                        }
                        var att = payload.body.attendance || {};
                        if (att.clocked_out) {
                            clockBtn.textContent = 'CLOCKED OUT';
                            clockBtn.setAttribute('data-can-clock-in', '0');
                            clockBtn.setAttribute('data-can-clock-out', '0');
                            clockBtn.classList.add('cursor-not-allowed', 'bg-slate-400');
                            clockBtn.classList.remove('cursor-pointer', 'bg-slate-900', 'hover:bg-slate-800');
                        } else if (att.can_clock_out) {
                            clockBtn.disabled = false;
                            clockBtn.textContent = 'CLOCK OUT';
                            clockBtn.setAttribute('data-can-clock-in', '0');
                            clockBtn.setAttribute('data-can-clock-out', '1');
                        } else {
                            clockBtn.textContent = 'CLOCKED IN';
                            clockBtn.setAttribute('data-can-clock-in', '0');
                            clockBtn.setAttribute('data-can-clock-out', '0');
                            clockBtn.classList.add('cursor-not-allowed', 'bg-slate-400');
                        }
                        if (window.showSuccessToast) window.showSuccessToast(payload.body.message || (canOut ? 'Clocked out successfully.' : 'Clocked in successfully.'));
                    }).catch(function () {
                        clockBtn.disabled = false;
                        clockBtn.textContent = canOut ? 'CLOCK OUT' : 'CLOCK IN';
                        if (window.showSuccessToast) window.showSuccessToast(canOut ? 'Could not clock out.' : 'Could not clock in.');
                    });
                });
            }

            (function () {
                var root = document.querySelector('[data-dashboard-tz-widget]');
                if (!root || root.dataset.bound === '1') return;
                root.dataset.bound = '1';
                var toggle = root.querySelector('[data-dashboard-tz-toggle]');
                var menu = root.querySelector('[data-dashboard-tz-menu]');
                var dateEl = root.querySelector('[data-dashboard-tz-date]');
                var timeEl = root.querySelector('[data-dashboard-tz-time]');
                var shortEl = root.querySelector('[data-dashboard-tz-short]');
                var key = 'dashboard_display_timezone';
                var zones = {
                    'Asia/Manila': 'PHT',
                    'Australia/Sydney': 'AEST',
                    'Australia/Perth': 'AWST',
                    'UTC': 'UTC'
                };
                var tz = 'Asia/Manila';
                try {
                    var saved = localStorage.getItem(key);
                    if (saved && zones[saved]) tz = saved;
                } catch (e) {}

                function paint() {
                    try {
                        var now = new Date();
                        dateEl.textContent = new Intl.DateTimeFormat('en-US', { timeZone: tz, month: 'short', day: 'numeric', year: 'numeric' }).format(now).toUpperCase();
                        timeEl.textContent = new Intl.DateTimeFormat('en-US', { timeZone: tz, hour: 'numeric', minute: '2-digit', hour12: true }).format(now).toUpperCase();
                        shortEl.textContent = zones[tz] || 'PHT';
                    } catch (e) {}
                }

                function setOpen(open) {
                    if (!menu || !toggle) return;
                    menu.classList.toggle('hidden', !open);
                    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                }

                if (toggle && menu) {
                    toggle.addEventListener('click', function (e) {
                        e.stopPropagation();
                        setOpen(menu.classList.contains('hidden'));
                    });
                    menu.querySelectorAll('[data-dashboard-tz-option]').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            tz = btn.getAttribute('data-tz') || 'Asia/Manila';
                            try { localStorage.setItem(key, tz); } catch (e) {}
                            paint();
                            setOpen(false);
                        });
                    });
                    document.addEventListener('click', function () { setOpen(false); });
                }
                paint();
                window.setInterval(paint, 1000);
            })();
        });
    </script>
@endpush
