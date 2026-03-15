@php
    $sidebar_active = 'dashboard';
@endphp
@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('header_center')
    <div id="announcement-root" class="flex-1 min-w-0 flex items-center overflow-hidden"></div>
@endsection

@section('content')
    <div id="dashboard-root" class="w-full min-w-0">
        {{-- Fallback: visible if React has not mounted yet or JS fails --}}
        <div class="dashboard-page" data-dashboard-fallback>
            <header class="dashboard-page__header">
                <h1 class="dashboard-page__title">Dashboard</h1>
                <p class="dashboard-page__subtitle">Welcome back! Here's an overview of your jobs and calendar.</p>
            </header>
            <section class="dashboard-cards">
                <div class="dashboard-card dashboard-card--total">
                    <div class="dashboard-card__gradient" aria-hidden></div>
                    <div class="dashboard-card__inner">
                        <span class="dashboard-card__label">Total Jobs</span>
                        <p class="dashboard-card__value">143</p>
                        <span class="dashboard-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="width:1.75rem;height:1.75rem"><path d="M12 12h.01"/><path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><path d="M22 13a18.15 18.15 0 0 1-20 0"/><rect width="20" height="14" x="2" y="6" rx="2"/></svg></span>
                        <div class="dashboard-card__sep"></div>
                        <div class="dashboard-card__rows">
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">LBS</span><span class="dashboard-card__row-value">45</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">BPH</span><span class="dashboard-card__row-value">10</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">BLUINQ</span><span class="dashboard-card__row-value">56</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">CSP</span><span class="dashboard-card__row-value">8</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">NH</span><span class="dashboard-card__row-value">5</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">LC HOME BUILDER</span><span class="dashboard-card__row-value">7</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">EFFICIENT LIVING</span><span class="dashboard-card__row-value">6</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">LEADING ENERGY</span><span class="dashboard-card__row-value">6</span></div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-card dashboard-card--completed">
                    <div class="dashboard-card__gradient" aria-hidden></div>
                    <div class="dashboard-card__inner">
                        <span class="dashboard-card__label">Completed Jobs</span>
                        <p class="dashboard-card__value">87</p>
                        <span class="dashboard-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="width:1.75rem;height:1.75rem"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg></span>
                        <div class="dashboard-card__sep"></div>
                        <div class="dashboard-card__rows">
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">LBS</span><span class="dashboard-card__row-value">32</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">BPH</span><span class="dashboard-card__row-value">8</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">BLUINQ</span><span class="dashboard-card__row-value">22</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">CSP</span><span class="dashboard-card__row-value">6</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">NH</span><span class="dashboard-card__row-value">4</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">LC HOME BUILDER</span><span class="dashboard-card__row-value">5</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">EFFICIENT LIVING</span><span class="dashboard-card__row-value">5</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">LEADING ENERGY</span><span class="dashboard-card__row-value">5</span></div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-card dashboard-card--processing">
                    <div class="dashboard-card__gradient" aria-hidden></div>
                    <div class="dashboard-card__inner">
                        <span class="dashboard-card__label">Processing</span>
                        <p class="dashboard-card__value">28</p>
                        <span class="dashboard-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="width:1.75rem;height:1.75rem"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span>
                        <div class="dashboard-card__sep"></div>
                        <div class="dashboard-card__rows">
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">LBS</span><span class="dashboard-card__row-value">10</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">BPH</span><span class="dashboard-card__row-value">2</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">BLUINQ</span><span class="dashboard-card__row-value">9</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">CSP</span><span class="dashboard-card__row-value">2</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">NH</span><span class="dashboard-card__row-value">1</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">LC HOME BUILDER</span><span class="dashboard-card__row-value">2</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">EFFICIENT LIVING</span><span class="dashboard-card__row-value">1</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">LEADING ENERGY</span><span class="dashboard-card__row-value">1</span></div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-card dashboard-card--pending">
                    <div class="dashboard-card__gradient" aria-hidden></div>
                    <div class="dashboard-card__inner">
                        <span class="dashboard-card__label">Pending</span>
                        <p class="dashboard-card__value">28</p>
                        <span class="dashboard-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" style="width:1.75rem;height:1.75rem"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg></span>
                        <div class="dashboard-card__sep"></div>
                        <div class="dashboard-card__rows">
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">LBS</span><span class="dashboard-card__row-value">3</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">BPH</span><span class="dashboard-card__row-value">0</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">BLUINQ</span><span class="dashboard-card__row-value">25</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">CSP</span><span class="dashboard-card__row-value">0</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">NH</span><span class="dashboard-card__row-value">0</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">LC HOME BUILDER</span><span class="dashboard-card__row-value">0</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">EFFICIENT LIVING</span><span class="dashboard-card__row-value">0</span></div>
                            <div class="dashboard-card__row"><span class="dashboard-card__row-label">LEADING ENERGY</span><span class="dashboard-card__row-value">0</span></div>
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
