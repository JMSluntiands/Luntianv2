@php
    $now = now();
    $calYear = (int) $now->format('Y');
    $calMonth = (int) $now->format('n');
    $calDate = \Carbon\Carbon::createFromDate($calYear, $calMonth, 1);
    $calMonthName = $calDate->format('F');
    $firstDay = (int) $calDate->format('w');
    $daysInMonth = (int) $calDate->format('t');
    $daysInPrev = (int) \Carbon\Carbon::createFromDate($calYear, $calMonth - 1, 1)->format('t');
    $today = ($calYear === (int)$now->format('Y') && $calMonth === (int)$now->format('n')) ? (int) $now->format('j') : 0;
    $weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $cells = [];
    for ($i = 0; $i < $firstDay; $i++) { $cells[] = ['day' => $daysInPrev - $firstDay + $i + 1, 'other' => true]; }
    for ($d = 1; $d <= $daysInMonth; $d++) { $cells[] = ['day' => $d, 'other' => false, 'today' => $d === $today]; }
    $remaining = 42 - count($cells);
    for ($d = 1; $d <= $remaining; $d++) { $cells[] = ['day' => $d, 'other' => true]; }
@endphp
@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('header_center')
    <div class="announcement-ticker-track">
        <span id="announcementText" class="announcement-ticker-text">Welcome to Luntian Dashboard. Check your jobs and calendar for updates.</span>
    </div>
@endsection

@section('content')
    <h1>Dashboard</h1>
    <p class="subtitle">Welcome back! Here's an overview of your jobs and calendar.</p>

    <div class="job-cards-row">
        <div class="job-card job-card--total">
            <div class="job-card-content">
                <span class="job-card-title">Total Jobs</span>
                <span class="job-card-value" data-value="143">0</span>
                <div class="job-card-sep"></div>
                <div class="job-card-list">
                    <div class="job-card-list-row"><span>LUNTIAN</span><span data-value="10">0</span></div>
                    <div class="job-card-list-row"><span>LBS</span><span data-value="45">0</span></div>
                    <div class="job-card-list-row"><span>B1</span><span data-value="32">0</span></div>
                    <div class="job-card-list-row"><span>BLUINQ</span><span data-value="56">0</span></div>
                </div>
            </div>
            <span class="job-card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M12 12h.01"/><path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><path d="M22 13a18.15 18.15 0 0 1-20 0"/><rect width="20" height="14" x="2" y="6" rx="2"/></svg></span>
        </div>
        <div class="job-card job-card--completed">
            <div class="job-card-content">
                <span class="job-card-title">Completed Jobs</span>
                <span class="job-card-value" data-value="87">0</span>
                <div class="job-card-sep"></div>
                <div class="job-card-list">
                    <div class="job-card-list-row"><span>LUNTIAN</span><span data-value="8">0</span></div>
                    <div class="job-card-list-row"><span>LBS</span><span data-value="32">0</span></div>
                    <div class="job-card-list-row"><span>B1</span><span data-value="25">0</span></div>
                    <div class="job-card-list-row"><span>BLUINQ</span><span data-value="22">0</span></div>
                </div>
            </div>
            <span class="job-card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg></span>
        </div>
        <div class="job-card job-card--processing">
            <div class="job-card-content">
                <span class="job-card-title">Processing</span>
                <span class="job-card-value" data-value="28">0</span>
                <div class="job-card-sep"></div>
                <div class="job-card-list">
                    <div class="job-card-list-row"><span>LUNTIAN</span><span data-value="2">0</span></div>
                    <div class="job-card-list-row"><span>LBS</span><span data-value="10">0</span></div>
                    <div class="job-card-list-row"><span>B1</span><span data-value="7">0</span></div>
                    <div class="job-card-list-row"><span>BLUINQ</span><span data-value="9">0</span></div>
                </div>
            </div>
            <span class="job-card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span>
        </div>
        <div class="job-card job-card--pending">
            <div class="job-card-content">
                <span class="job-card-title">Pending</span>
                <span class="job-card-value" data-value="28">0</span>
                <div class="job-card-sep"></div>
                <div class="job-card-list">
                    <div class="job-card-list-row"><span>LUNTIAN</span><span data-value="0">0</span></div>
                    <div class="job-card-list-row"><span>LBS</span><span data-value="3">0</span></div>
                    <div class="job-card-list-row"><span>B1</span><span data-value="0">0</span></div>
                    <div class="job-card-list-row"><span>BLUINQ</span><span data-value="25">0</span></div>
                </div>
            </div>
            <span class="job-card-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg></span>
        </div>
    </div>

    <div class="dashboard-row">
        <div class="dashboard-calendar-wrap">
            <p class="section-title">
                <svg class="cal-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Calendar
            </p>
            <div id="calendar-root" role="application" aria-label="Month calendar">
                <div class="dashboard-calendar" id="calendar-fallback">
                    <div class="dashboard-calendar-header">
                        <button type="button" class="dashboard-calendar-nav" id="cal-prev" aria-label="Previous month">‹</button>
                        <span class="dashboard-calendar-title" id="cal-title">{{ $calMonthName }} {{ $calYear }}</span>
                        <button type="button" class="dashboard-calendar-nav" id="cal-next" aria-label="Next month">›</button>
                    </div>
                    <div class="dashboard-calendar-body">
                        <div class="dashboard-calendar-weekdays">
                            @foreach($weekdays as $wd)<div class="dashboard-calendar-weekday">{{ $wd }}</div>@endforeach
                        </div>
                        <div class="dashboard-calendar-grid" id="cal-grid">
                            @foreach($cells as $c)
                            <span class="dashboard-calendar-cell {{ $c['other'] ?? false ? 'other-month' : '' }} {{ ($c['today'] ?? false) ? 'selected' : '' }}">{{ $c['day'] }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="holidays-panel">
            <p class="section-title">
                <svg class="holidays-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                Holidays
            </p>
            <div class="holidays-panel-cards">
                <div class="holiday-box">
                    <div class="holiday-box-header ph">Philippine Holidays</div>
                    <div class="holiday-box-body">No holidays this month</div>
                </div>
                <div class="holiday-box">
                    <div class="holiday-box-header au">Australian Holidays</div>
                    <div class="holiday-box-body">No holidays this month</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .job-cards-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    .job-card { border-radius: 12px; padding: 1.25rem 0 1.25rem 1.25rem; min-height: 180px; position: relative; overflow: hidden; display: grid; grid-template-columns: 60% 40%; grid-gap: 1.25rem; gap: 1.25rem; align-items: stretch; opacity: 0; transform: translateY(24px); animation: job-card-in 0.5s ease-out forwards; }
    .job-card:nth-child(1) { animation-delay: 0.05s; }
    .job-card:nth-child(2) { animation-delay: 0.15s; }
    .job-card:nth-child(3) { animation-delay: 0.25s; }
    .job-card:nth-child(4) { animation-delay: 0.35s; }
    @keyframes job-card-in { to { opacity: 1; transform: translateY(0); } }
    .job-card--total { background: #FF9800; }
    .job-card--completed { background: #795548; }
    .job-card--processing { background: #FFAC33; }
    .job-card--pending { background: #FFE0B2; }
    .job-card--pending .job-card-title, .job-card--pending .job-card-value, .job-card--pending .job-card-list, .job-card--pending .job-card-sep { color: #5d4037; }
    .job-card--pending .job-card-sep { background: rgba(93,64,55,0.3); }
    .job-card--pending .job-card-icon svg { stroke: #5d4037; }
    .job-card-content { display: flex; flex-direction: column; justify-content: center; min-width: 0; }
    .job-card-title { font-size: 0.95rem; font-weight: 700; color: #fff; margin-bottom: 0.35rem; letter-spacing: 0.01em; }
    .job-card-value { font-size: 2.5rem; font-weight: 700; color: #fff; margin-bottom: 0.6rem; line-height: 1.1; letter-spacing: -0.02em; }
    .job-card-sep { height: 1px; background: rgba(255,255,255,0.3); margin-bottom: 0.65rem; }
    .job-card-list { display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.875rem; color: #fff; font-weight: 400; min-width: 0; }
    .job-card-list-row { display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; min-width: 0; }
    .job-card-list-row span:first-child { min-width: 0; overflow: hidden; text-overflow: ellipsis; }
    .job-card-list-row span:last-child { margin-left: 0.5rem; flex-shrink: 0; }
    .job-card-icon { display: flex; align-items: flex-end; justify-content: flex-end; padding: 0; min-width: 0; overflow: hidden; }
    .job-card-icon svg { width: 180px; height: 100%; min-height: 165px; stroke: #fff; stroke-width: 1.8; fill: none; opacity: 0.1; align-self: stretch; flex-shrink: 0; }
    .dashboard-row { display: grid; grid-template-columns: repeat(12, 1fr); gap: 1.5rem; align-items: start; animation: dashboard-row-in 0.5s ease-out 0.2s both; }
    .dashboard-calendar-wrap { grid-column: span 6; min-height: 280px; }
    .holidays-panel { grid-column: span 6; display: flex; flex-direction: column; }
    .holidays-panel-cards { display: flex; flex-direction: column; gap: 1rem; }
    .dashboard-calendar-wrap .section-title { margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
    .dashboard-calendar-wrap .section-title .cal-icon { width: 1.1rem; height: 1.1rem; color: #93c5fd; }
    .holidays-panel .section-title { margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
    .holidays-panel .section-title .holidays-icon { width: 1.1rem; height: 1.1rem; color: #f59e0b; }
    @keyframes dashboard-row-in { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
    .dashboard-calendar { background: #0f172a; border: 1px solid #334155; border-radius: 12px; padding: 1rem; animation: calendar-in 0.4s ease-out 0.35s both; }
    @keyframes calendar-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .dashboard-calendar-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; position: relative; z-index: 2; }
    .dashboard-calendar-title { font-size: 1rem; font-weight: 600; color: #e2e8f0; }
    .dashboard-calendar-nav { background: transparent; border: none; color: #94a3b8; font-size: 1.25rem; cursor: pointer; padding: 0.35rem 0.6rem; line-height: 1; transition: color 0.2s, transform 0.2s; position: relative; z-index: 1; }
    .dashboard-calendar-nav:hover { color: #e2e8f0; transform: scale(1.1); }
    .dashboard-calendar-nav:focus { outline: 2px solid #93c5fd; outline-offset: 2px; }
    a.dashboard-calendar-nav { text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
    .dashboard-calendar-body { border: 1px solid #334155; border-top: none; border-radius: 0 0 12px 12px; overflow: hidden; }
    .dashboard-calendar-weekdays { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 0; border-bottom: 1px solid #334155; }
    .dashboard-calendar-weekday { font-size: 0.7rem; color: #94a3b8; text-align: center; padding: 0.35rem; border-right: 1px solid #334155; box-sizing: border-box; }
    .dashboard-calendar-weekday:last-child { border-right: none; }
    .dashboard-calendar-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 0; }
    .dashboard-calendar-cell { background: transparent; border: none; border-right: 1px solid #334155; border-bottom: 1px solid #334155; color: #e2e8f0; font-size: 0.85rem; padding: 0.5rem 0.4rem; height: 70px; min-height: 70px; display: flex; align-items: flex-start; justify-content: flex-start; border-radius: 0; cursor: pointer; transition: background 0.2s, color 0.2s; box-sizing: border-box; }
    .dashboard-calendar-cell:nth-child(7n) { border-right: none; }
    .dashboard-calendar-cell:hover { background: rgba(255,255,255,0.06); }
    .dashboard-calendar-cell.other-month { color: #64748b; }
    .dashboard-calendar-cell.selected { background: rgba(44,82,139,0.35); color: #93c5fd; }
    .holiday-box { border-radius: 10px; border: 1px solid #334155; background: #0f172a; animation: holiday-box-in 0.4s ease-out both; overflow: hidden; }
    .holiday-box:nth-child(1) { animation-delay: 0.4s; border-left: 4px solid #dc2626; }
    .holiday-box:nth-child(2) { animation-delay: 0.5s; border-left: 4px solid #d97706; }
    @keyframes holiday-box-in { from { opacity: 0; transform: translateX(12px); } to { opacity: 1; transform: translateX(0); } }
    .holiday-box-header { padding: 0.6rem 1rem 0.25rem; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 0.35rem; line-height: 1.3; box-sizing: border-box; background: transparent; }
    .holiday-box-header.ph { color: #f87171; }
    .holiday-box-header.au { color: #fbbf24; }
    .holiday-box-header::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .holiday-box-header.ph::before { background: #dc2626; }
    .holiday-box-header.au::before { background: #d97706; }
    .holiday-box-body { padding: 0.25rem 1rem 1rem; color: #94a3b8; font-size: 0.875rem; line-height: 1.4; }
    html[data-theme="light"] .dashboard-calendar { background: #fff; border-color: #e2e8f0; }
    html[data-theme="light"] .dashboard-calendar-title { color: #334155; }
    html[data-theme="light"] .dashboard-calendar-nav { color: #64748b; }
    html[data-theme="light"] .dashboard-calendar-nav:hover { color: #0f172a; }
    html[data-theme="light"] .dashboard-calendar-body { border-color: #e2e8f0; }
    html[data-theme="light"] .dashboard-calendar-weekdays { border-bottom-color: #e2e8f0; }
    html[data-theme="light"] .dashboard-calendar-weekday { color: #64748b; border-right-color: #e2e8f0; }
    html[data-theme="light"] .dashboard-calendar-cell { color: #334155; border-right-color: #e2e8f0; border-bottom-color: #e2e8f0; }
    html[data-theme="light"] .dashboard-calendar-cell:hover { background: #f8fafc; }
    html[data-theme="light"] .dashboard-calendar-cell.other-month { color: #94a3b8; }
    html[data-theme="light"] .dashboard-calendar-cell.selected { background: rgba(44,82,139,0.15); color: #2C528B; }
    html[data-theme="light"] .holiday-box { background: #fff; border-color: #e2e8f0; }
    html[data-theme="light"] .holiday-box-body { color: #64748b; }

    /* Dashboard responsive */
    @media (max-width: 1200px) {
        .job-cards-row { grid-template-columns: repeat(2, 1fr); }
        .dashboard-row { grid-template-columns: 1fr; }
        .dashboard-calendar-wrap { grid-column: span 1; }
        .holidays-panel { grid-column: span 1; }
    }
    @media (max-width: 768px) {
        .job-cards-row { grid-template-columns: 1fr; gap: 0.75rem; margin-bottom: 1rem; }
        .job-card { min-height: 140px; padding: 1rem 1rem 1rem 1.25rem; grid-template-columns: 1fr auto; gap: 0.75rem; }
        .job-card-content { padding-right: 0.25rem; }
        .job-card-value { font-size: 1.75rem; }
        .job-card-icon { display: flex; }
        .job-card-icon svg { width: 80px; min-height: 100px; }
        .job-card-list-row span:last-child { margin-left: 0.25rem; }
        .dashboard-row { gap: 1rem; }
        .dashboard-calendar-wrap { min-height: auto; }
        .dashboard-calendar-cell { height: 48px; min-height: 48px; font-size: 0.8rem; padding: 0.35rem 0.25rem; }
        .dashboard-calendar-weekday { font-size: 0.65rem; padding: 0.25rem; }
        .holidays-panel-cards { gap: 0.75rem; }
    }
    @media (max-width: 480px) {
        .job-card { padding: 0.875rem 0.875rem 0.875rem 1rem; grid-template-columns: 1fr auto; }
        .job-card-icon svg { width: 56px; min-height: 80px; }
        .job-card-value { font-size: 1.5rem; }
        .job-card-title { font-size: 0.9rem; }
        .job-card-list { font-size: 0.8125rem; }
        .dashboard-calendar { padding: 0.75rem; }
        .dashboard-calendar-header { margin-bottom: 0.75rem; }
        .dashboard-calendar-title { font-size: 0.9rem; }
    }
</style>
@endpush

@push('scripts')
    @vite(['resources/js/dashboard.tsx'])
    <script>
    (function() {
        var fallback = document.getElementById('calendar-fallback');
        if (fallback) {
            var calMonth = {{ $calMonth }};
            var calYear = {{ $calYear }};
            var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            var now = new Date();
            var todayDate = now.getDate();
            var todayMonth = now.getMonth() + 1;
            var todayYear = now.getFullYear();
            function buildCells(m, y) {
                var first = new Date(y, m - 1, 1).getDay();
                var daysIn = new Date(y, m, 0).getDate();
                var daysPrev = new Date(y, m - 1, 0).getDate();
                var cells = [];
                for (var i = 0; i < first; i++) cells.push({ d: daysPrev - first + i + 1, other: true });
                for (var d = 1; d <= daysIn; d++) cells.push({ d: d, other: false, today: (y === todayYear && m === todayMonth && d === todayDate) });
                var remain = 42 - cells.length;
                for (var d = 1; d <= remain; d++) cells.push({ d: d, other: true });
                return cells;
            }
            function render() {
                var titleEl = document.getElementById('cal-title');
                var gridEl = document.getElementById('cal-grid');
                if (titleEl) titleEl.textContent = monthNames[calMonth - 1] + ' ' + calYear;
                if (!gridEl) return;
                var cells = buildCells(calMonth, calYear);
                gridEl.innerHTML = cells.map(function(c) {
                    var cls = 'dashboard-calendar-cell';
                    if (c.other) cls += ' other-month';
                    if (c.today) cls += ' selected';
                    return '<span class="' + cls + '">' + c.d + '</span>';
                }).join('');
            }
            var prevBtn = document.getElementById('cal-prev');
            var nextBtn = document.getElementById('cal-next');
            if (prevBtn) prevBtn.addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); calMonth--; if (calMonth < 1) { calMonth = 12; calYear--; } render(); return false; });
            if (nextBtn) nextBtn.addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); calMonth++; if (calMonth > 12) { calMonth = 1; calYear++; } render(); return false; });
        }
        (function countUpJobCards() {
            var duration = 1200, cardAnimEnd = 900;
            function easeOutQuart(t) { return 1 - Math.pow(1 - t, 4); }
            function runCountUp(el) {
                var target = parseInt(el.getAttribute('data-value'), 10) || 0;
                if (target === 0) { el.textContent = '0'; return; }
                var startTime = null;
                function step(timestamp) {
                    if (!startTime) startTime = timestamp;
                    var elapsed = timestamp - startTime;
                    var progress = Math.min(elapsed / duration, 1);
                    var eased = easeOutQuart(progress);
                    el.textContent = Math.round(eased * target);
                    if (progress < 1) requestAnimationFrame(step);
                    else el.textContent = target;
                }
                requestAnimationFrame(step);
            }
            setTimeout(function() {
                document.querySelectorAll('.job-card-value[data-value]').forEach(runCountUp);
                document.querySelectorAll('.job-card-list-row span[data-value]').forEach(runCountUp);
            }, cardAnimEnd);
        })();
    })();
    </script>
@endpush
