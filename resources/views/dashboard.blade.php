@php
    $sidebar_active = 'dashboard';
    $dashboardStats = $dashboardStats ?? \App\Services\DashboardJobStatsService::fetch();
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

@section('content')
    <script type="application/json" id="dashboard-stats-json">@json($dashboardStats)</script>
    <div
        id="dashboard-root"
        class="w-full min-w-0"
        data-dashboard-branch-filter="{{ $dashboardBranchFilter }}"
    >
        {{-- Fallback: visible if React has not mounted yet or JS fails --}}
        <div class="dashboard-page" data-dashboard-fallback>
            <header class="dashboard-page__header">
                <h1 class="dashboard-page__title">Dashboard</h1>
                <p class="dashboard-page__subtitle">Welcome back! Here&apos;s an overview of your jobs and calendar.</p>
            </header>
            <section class="dashboard-cards">
                <div class="dashboard-card dashboard-card--total">
                    <div class="dashboard-card__gradient" aria-hidden></div>
                    <div class="dashboard-card__inner">
                        <span class="dashboard-card__label">Total Jobs</span>
                        <p class="dashboard-card__value">{{ array_sum($dTotal) }}</p>
                        <span class="dashboard-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="width:1.75rem;height:1.75rem"><path d="M12 12h.01"/><path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><path d="M22 13a18.15 18.15 0 0 1-20 0"/><rect width="20" height="14" x="2" y="6" rx="2"/></svg></span>
                        <div class="dashboard-card__sep"></div>
                        <div class="dashboard-card__rows">
                            @foreach ($dTotal as $rowLabel => $rowValue)
                                <div class="dashboard-card__row">
                                    <span class="dashboard-card__row-label-group">
                                        <span class="dashboard-card__row-label dashboard-card__row-status">Total jobs</span>
                                        <span class="dashboard-card__row-meta">Branch: <strong>{{ $rowLabel }}</strong></span>
                                    </span>
                                    <span class="dashboard-card__row-value">{{ $rowValue }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="dashboard-card dashboard-card--completed">
                    <div class="dashboard-card__gradient" aria-hidden></div>
                    <div class="dashboard-card__inner">
                        <span class="dashboard-card__label">Completed Jobs</span>
                        <p class="dashboard-card__value">{{ array_sum($dCompleted) }}</p>
                        <span class="dashboard-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="width:1.75rem;height:1.75rem"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg></span>
                        <div class="dashboard-card__sep"></div>
                        <div class="dashboard-card__rows">
                            @foreach ($dCompleted as $rowLabel => $rowValue)
                                <div class="dashboard-card__row">
                                    <span class="dashboard-card__row-label-group">
                                        <span class="dashboard-card__row-label dashboard-card__row-status">Completed</span>
                                        <span class="dashboard-card__row-meta">Branch: <strong>{{ $rowLabel }}</strong></span>
                                    </span>
                                    <span class="dashboard-card__row-value">{{ $rowValue }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="dashboard-card dashboard-card--processing">
                    <div class="dashboard-card__gradient" aria-hidden></div>
                    <div class="dashboard-card__inner">
                        <span class="dashboard-card__label">Processing</span>
                        <p class="dashboard-card__value">{{ array_sum($dProcessing) }}</p>
                        <span class="dashboard-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="width:1.75rem;height:1.75rem"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span>
                        <div class="dashboard-card__sep"></div>
                        <div class="dashboard-card__rows">
                            @foreach ($dProcessing as $rowLabel => $rowValue)
                                <div class="dashboard-card__row">
                                    <span class="dashboard-card__row-label-group">
                                        <span class="dashboard-card__row-label dashboard-card__row-status">Processing</span>
                                        <span class="dashboard-card__row-meta">Branch: <strong>{{ $rowLabel }}</strong></span>
                                    </span>
                                    <span class="dashboard-card__row-value">{{ $rowValue }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="dashboard-card dashboard-card--pending">
                    <div class="dashboard-card__gradient" aria-hidden></div>
                    <div class="dashboard-card__inner">
                        <span class="dashboard-card__label">Pending</span>
                        <p class="dashboard-card__value">{{ array_sum($dPending) }}</p>
                        <span class="dashboard-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="width:1.75rem;height:1.75rem"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg></span>
                        <div class="dashboard-card__sep"></div>
                        <div class="dashboard-card__rows">
                            @foreach ($dPending as $rowLabel => $rowValue)
                                <div class="dashboard-card__row">
                                    <span class="dashboard-card__row-label-group">
                                        <span class="dashboard-card__row-label dashboard-card__row-status">Pending</span>
                                        <span class="dashboard-card__row-meta">Branch: <strong>{{ $rowLabel }}</strong></span>
                                    </span>
                                    <span class="dashboard-card__row-value">{{ $rowValue }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
            <section class="dashboard-section">
                <div class="dashboard-panel">
                    <h2 class="dashboard-panel__header">
                        <span class="dashboard-panel__icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.25rem;height:1.25rem"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                        Calendar
                    </h2>
                    <div class="dashboard-panel__body">
                        <div id="calendar-root" class="dashboard-calendar-wrapper" role="application" aria-label="Month calendar"></div>
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
                                <div class="dashboard-holiday-box__text">No holidays this month</div>
                            </div>
                            <div class="dashboard-holiday-box">
                                <div class="dashboard-holiday-box__title">Australian Holidays</div>
                                <div class="dashboard-holiday-box__text">No holidays this month</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/dashboard.tsx'])
@endpush
