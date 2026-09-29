<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Luntian</title>
    @includeIf('layouts.partials.favicon')
    <script>
        (function(){
            var t = (typeof localStorage !== 'undefined' && localStorage.getItem('theme')) || '';
            var theme = (String(t).toLowerCase() === 'light') ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', theme);
            try {
                if (localStorage.getItem('sidebar-mini') === '1') {
                    document.documentElement.classList.add('sidebar-mini');
                }
            } catch (e) {}
        })();
    </script>
    @include('layouts.partials.dashboard-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/layout.ts'])
    @include('layouts.partials.site-type')
    <style>
        /* Sidebar minimize. Kept inline so it still works when the Vite build on the server is stale. */
        .sidebar-collapse-btn { display: none; }
        @media (min-width: 1024px) {
            #sidebarNav,
            .main-wrap {
                transition-property: transform, box-shadow, width, margin, margin-left;
                transition-duration: 250ms;
                transition-timing-function: ease-out;
            }
            .sidebar-collapse-btn {
                position: absolute;
                right: 0.35rem;
                top: 50%;
                z-index: 2;
                display: inline-flex;
                height: 1.75rem;
                width: 1.75rem;
                transform: translateY(-50%);
                cursor: pointer;
                align-items: center;
                justify-content: center;
                border-radius: 0.5rem;
                border: 1px solid #e2e8f0;
                background: #fff;
                color: #475569;
            }
            .sidebar-collapse-btn:hover {
                background: #f1f5f9;
                color: #0f172a;
            }
            [data-theme="dark"] .sidebar-collapse-btn {
                border-color: #334155;
                background: #1e293b;
                color: #cbd5e1;
            }
            [data-theme="dark"] .sidebar-collapse-btn:hover {
                background: #334155;
                color: #f8fafc;
            }
            html.sidebar-mini #sidebarNav {
                width: 4.5rem;
                height: 100vh;
                overflow: hidden;
            }
            html.sidebar-mini #sidebarNav .sidebar-brand {
                height: 3.5rem;
                width: 100%;
                justify-content: center;
                padding: 0;
                gap: 0;
            }
            html.sidebar-mini #sidebarNav .sidebar-brand > a {
                display: none;
            }
            html.sidebar-mini .sidebar-collapse-btn {
                position: static;
                transform: none;
                flex-shrink: 0;
            }
            html.sidebar-mini .sidebar-collapse-icon {
                transform: rotate(180deg);
            }
            html.sidebar-mini #sidebarNav .sidebar-profile-card {
                justify-content: center;
                gap: 0;
                padding: 0.65rem 0.25rem;
            }
            html.sidebar-mini #sidebarNav .sidebar-profile-card > div {
                display: none;
            }
            html.sidebar-mini #sidebarNav > nav {
                display: block;
                overflow-x: hidden;
                overflow-y: auto;
                padding-left: 0.35rem;
                padding-right: 0.35rem;
            }
            html.sidebar-mini #sidebarNav > nav > div:not([data-dropdown]) {
                display: none;
            }
            html.sidebar-mini #sidebarNav .nav-item,
            html.sidebar-mini #sidebarNav .nav-dropdown-trigger {
                justify-content: center;
                gap: 0;
                padding-left: 0.4rem;
                padding-right: 0.4rem;
                font-size: 0;
            }
            html.sidebar-mini #sidebarNav .nav-dropdown-trigger > span {
                justify-content: center;
                gap: 0;
            }
            html.sidebar-mini #sidebarNav .nav-dropdown-trigger > svg {
                display: none;
            }
            html.sidebar-mini #sidebarNav .nav-dropdown > [role="region"] {
                display: none !important;
                max-height: 0 !important;
            }
            html.sidebar-mini .main-wrap {
                margin-left: 4.5rem;
                width: calc(100% - 4.5rem);
            }
            html.sidebar-mini .header {
                padding-left: 0.75rem;
            }
        }
        .sidebar-mini-flyout {
            display: block;
            z-index: 80;
            width: 13.5rem;
            max-height: min(20rem, calc(100vh - 16px));
            overflow: auto;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background: #fff;
            padding: 0.35rem;
            font-size: 13px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.16);
        }
        .sidebar-mini-flyout .nav-subitem {
            font-size: 13px !important;
            padding: 0.45rem 0.75rem !important;
        }
        .sidebar-mini-flyout-title {
            margin-bottom: 0.25rem;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.4rem 0.75rem 0.35rem;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
            color: #0f172a;
        }
        [data-theme="dark"] .sidebar-mini-flyout {
            border-color: #334155;
            background: #0f172a;
            color: #e2e8f0;
        }
        [data-theme="dark"] .sidebar-mini-flyout-title {
            border-bottom-color: #334155;
            color: #f8fafc;
        }
    </style>
    <style>
        /* Inline job-table dropdowns (always available even if Vite build is stale) */
        .lbs-initials-select {
            display: inline-block;
            max-width: 4.75rem;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            border-radius: 0.375rem;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.3rem center;
            background-size: 12px;
            padding: 0.25rem 1.15rem 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #1e293b;
            line-height: 1.25;
        }
        .lbs-priority-select,
        .lbs-status-select {
            display: inline-block;
            max-width: 11rem;
            min-width: 6.5rem;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            border-radius: 0.375rem;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            color: #1e293b;
            /* Chevron kept via !important so inline background-color does not wipe it */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 0.35rem center !important;
            background-size: 12px !important;
            padding: 0.25rem 1.2rem 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.25;
        }
        [data-theme="dark"] .lbs-initials-select,
        .dark .lbs-initials-select {
            border-color: #475569;
            background-color: rgba(30, 41, 59, 0.5);
            color: #e2e8f0;
        }
        [data-theme="dark"] .lbs-priority-select:not([style*="background-color"]),
        [data-theme="dark"] .lbs-status-select:not([style*="background-color"]),
        .dark .lbs-priority-select:not([style*="background-color"]),
        .dark .lbs-status-select:not([style*="background-color"]) {
            border-color: #475569;
            background-color: rgba(30, 41, 59, 0.5);
            color: #e2e8f0;
        }
        [data-theme="dark"] .lbs-priority-select,
        [data-theme="dark"] .lbs-status-select,
        .dark .lbs-priority-select,
        .dark .lbs-status-select {
            border-color: #475569;
        }

        /* Shared asc/desc sort icons for all dashboard tables */
        th .lbs-sort-icon,
        th .efficient_living-sort-icon,
        th .luntian-sort-icon,
        th .reports-sort-icon,
        th .bph-sort-icon {
            display: inline-block;
            margin-left: 0.25rem;
            opacity: 0.55;
            font-size: 0.75rem;
            line-height: 1;
            vertical-align: middle;
        }
        th[data-sort="asc"] .lbs-sort-icon,
        th[data-sort="asc"] .efficient_living-sort-icon,
        th[data-sort="asc"] .luntian-sort-icon,
        th[data-sort="asc"] .reports-sort-icon,
        th[data-sort="asc"] .bph-sort-icon,
        th[data-sort="desc"] .lbs-sort-icon,
        th[data-sort="desc"] .efficient_living-sort-icon,
        th[data-sort="desc"] .luntian-sort-icon,
        th[data-sort="desc"] .reports-sort-icon,
        th[data-sort="desc"] .bph-sort-icon {
            opacity: 1;
            font-size: 0;
        }
        th[data-sort="asc"] .lbs-sort-icon::before,
        th[data-sort="asc"] .efficient_living-sort-icon::before,
        th[data-sort="asc"] .luntian-sort-icon::before,
        th[data-sort="asc"] .reports-sort-icon::before,
        th[data-sort="asc"] .bph-sort-icon::before { content: "↑"; font-size: 0.75rem; color: #34d399; }
        th[data-sort="desc"] .lbs-sort-icon::before,
        th[data-sort="desc"] .efficient_living-sort-icon::before,
        th[data-sort="desc"] .luntian-sort-icon::before,
        th[data-sort="desc"] .reports-sort-icon::before,
        th[data-sort="desc"] .bph-sort-icon::before { content: "↓"; font-size: 0.75rem; color: #34d399; }
        th[data-sort="asc"]::after,
        th[data-sort="desc"]::after {
            content: "";
            display: inline-block;
            width: 0.375rem;
            height: 0.375rem;
            margin-left: 0.35rem;
            border-radius: 9999px;
            background: #34d399;
            vertical-align: middle;
        }
    </style>
    @stack('styles')
    @include('layouts.partials.select2-theme')
</head>
<body class="overflow-x-hidden @yield('body_class', '')">
    <div class="page-loader" id="pageLoader" aria-hidden="true" data-theme="">
        <div class="page-loader-spinner"></div>
        <span class="page-loader-logo">LUNTIAN</span>
    </div>
    <script>
        (function(){
            var t = (typeof localStorage !== 'undefined' && localStorage.getItem('theme')) || '';
            var theme = (String(t).toLowerCase() === 'light') ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', theme);
            var loader = document.getElementById('pageLoader');
            if (loader) loader.setAttribute('data-theme', theme);
        })();
    </script>
    <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true" tabindex="-1"></div>
    @include('layouts.partials.sidebar')

    <div class="main-wrap ml-0 flex h-screen min-h-0 min-w-0 flex-col overflow-hidden transition-[margin] duration-250 ease-out lg:ml-60 lg:w-[calc(100%-15rem)]">
        <header class="header-wrap flex-shrink-0">
            @include('layouts.partials.header')
        </header>
        <main class="content min-h-0 min-w-0 flex-1 overflow-y-auto overflow-x-hidden bg-slate-50 p-4 dark:bg-slate-900 md:p-6">
            @yield('content')
        </main>
    </div>

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 opacity-0 pointer-events-none transition-opacity duration-200" id="logoutModal" role="dialog" aria-labelledby="logoutModalTitle" aria-modal="true">
        <div class="w-full max-w-sm rounded-2xl shadow-xl overflow-hidden bg-white border border-slate-200 dark:bg-[#2D3748] dark:border-slate-600" role="document">
            <div class="flex items-center gap-3 px-5 py-5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-pink-500 text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </span>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="logoutModalTitle">Logout</h2>
            </div>
            <div class="px-5 pb-4">
                <p id="logoutModalMessage" class="text-slate-600 dark:text-slate-200 text-[15px]">Are you sure you want to logout?</p>
            </div>
            <div class="flex justify-end gap-3 px-5 pb-5 pt-1">
                <button type="button" class="cursor-pointer rounded-lg bg-slate-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 focus:ring-offset-slate-700 dark:focus:ring-offset-[#2D3748]" id="logoutModalCancel">Cancel</button>
                <button type="button" class="cursor-pointer rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-pink-500 border border-pink-500 transition-colors hover:bg-pink-50 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-[#2D3748]" id="logoutModalConfirm"><span class="btn-text">Logout</span></button>
            </div>
        </div>
    </div>

    <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display: none;" autocomplete="off">
        @csrf
    </form>

    @include('layouts.partials.app-toast')

    <script>
    (function() {
        /* Sidebar nav dropdowns – run inline so they work even if layout.ts loads late */
        document.querySelectorAll('.nav-dropdown[data-dropdown]').forEach(function(wrap) {
            var trigger = wrap.querySelector('button');
            if (!trigger) return;
            trigger.addEventListener('click', function() {
                var isOpen = wrap.classList.contains('open');
                document.querySelectorAll('.nav-dropdown.open').forEach(function(open) {
                    open.classList.remove('open');
                    var t = open.querySelector('button');
                    if (t) t.setAttribute('aria-expanded', 'false');
                });
                if (!isOpen) {
                    wrap.classList.add('open');
                    trigger.setAttribute('aria-expanded', 'true');
                }
            });
        });
        var dropdown = document.getElementById('userDropdown');
        var btn = document.getElementById('userMenuBtn');
        if (btn && dropdown) {
            btn.addEventListener('click', function() {
                dropdown.classList.toggle('show');
                var notifDrop = document.getElementById('notificationDropdown');
                if (notifDrop) notifDrop.classList.remove('show');
            });
            document.addEventListener('click', function(e) {
                if (!btn.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.remove('show');
            });
        }
        var notificationDropdown = document.getElementById('notificationDropdown');
        var notificationBtn = document.getElementById('notificationBtn');
        if (notificationBtn && notificationDropdown) {
            notificationBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('show');
                notificationBtn.setAttribute('aria-expanded', notificationDropdown.classList.contains('show'));
                if (notificationDropdown.classList.contains('show') && dropdown) dropdown.classList.remove('show');
            });
            document.addEventListener('click', function(e) {
                if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.remove('show');
                    notificationBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }
        var logoutModal = document.getElementById('logoutModal');
        var logoutBtn = document.getElementById('logoutBtn');
        var logoutModalCancel = document.getElementById('logoutModalCancel');
        var logoutModalConfirm = document.getElementById('logoutModalConfirm');
        var logoutForm = document.getElementById('logoutForm');
        if (logoutBtn && logoutModal) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (dropdown) dropdown.classList.remove('show');
                if (notificationDropdown) notificationDropdown.classList.remove('show');
                if (notificationBtn) notificationBtn.setAttribute('aria-expanded', 'false');
                logoutModal.classList.add('show');
                logoutModalConfirm.disabled = false;
                logoutModalConfirm.innerHTML = '<span class="btn-text">Logout</span>';
            });
        }
        if (logoutModalCancel) logoutModalCancel.addEventListener('click', function() { logoutModal.classList.remove('show'); });
        if (logoutModal) logoutModal.addEventListener('click', function(e) { if (e.target === logoutModal) logoutModal.classList.remove('show'); });
        if (logoutModalConfirm && logoutForm) {
            logoutModalConfirm.addEventListener('click', function() {
                if (logoutModalConfirm.disabled) return;
                logoutModalConfirm.disabled = true;
                logoutModalConfirm.innerHTML = '<span class="spinner"></span> Logging out...';
                setTimeout(function() { logoutForm.submit(); }, 600);
            });
        }
        @include('layouts.partials.theme-toggle-script')
        (function announcementMarqueeGap() {
            var marquee = document.getElementById('announcementMarquee');
            if (!marquee) return;
            function setGap() {
                marquee.style.setProperty('--marquee-gap', marquee.offsetWidth + 'px');
            }
            if (document.readyState === 'complete') {
                requestAnimationFrame(setGap);
            } else {
                window.addEventListener('load', function() { requestAnimationFrame(setGap); });
            }
            window.addEventListener('resize', setGap);
        })();
        (function hidePageLoader() {
            var loader = document.getElementById('pageLoader');
            if (!loader) return;
            var minShowMs = 450;
            var start = Date.now();
            function hide() {
                var elapsed = Date.now() - start;
                var delay = Math.max(0, minShowMs - elapsed);
                setTimeout(function() {
                    loader.classList.add('hide');
                    loader.style.pointerEvents = 'none';
                    try { document.dispatchEvent(new CustomEvent('pageLoaderHidden')); } catch (e) {}
                    setTimeout(function() { loader.remove(); }, 350);
                }, delay);
            }
            if (document.readyState === 'complete') {
                hide();
            } else {
                window.addEventListener('load', hide);
            }
        })();
        (function sidebarMobile() {
            var toggle = document.getElementById('sidebarToggle');
            var overlay = document.getElementById('sidebarOverlay');
            var sidebar = document.getElementById('sidebarNav');
            function openSidebar() {
                document.body.classList.add('sidebar-open');
                if (toggle) { toggle.setAttribute('aria-expanded', 'true'); toggle.setAttribute('aria-label', 'Close menu'); }
                document.body.style.overflow = 'hidden';
            }
            function closeSidebar() {
                document.body.classList.remove('sidebar-open');
                if (toggle) { toggle.setAttribute('aria-expanded', 'false'); toggle.setAttribute('aria-label', 'Open menu'); }
                document.body.style.overflow = '';
            }
            function toggleSidebar() {
                if (document.body.classList.contains('sidebar-open')) closeSidebar(); else openSidebar();
            }
            if (toggle) toggle.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) closeSidebar();
            });
            if (sidebar) {
                sidebar.addEventListener('click', function(e) {
                    if (window.matchMedia('(max-width: 1024px)').matches && e.target.closest('a')) closeSidebar();
                });
            }
        })();
        (function sidebarMini() {
            var btn = document.getElementById('sidebarCollapseToggle');
            var sidebar = document.getElementById('sidebarNav');
            var root = document.documentElement;
            var hideTimer = null;
            var parked = null;
            function restoreFlyout() {
                if (!parked) return;
                var title = parked.panel.querySelector(':scope > .sidebar-mini-flyout-title');
                if (title) title.remove();
                parked.panel.classList.remove('sidebar-mini-flyout');
                parked.panel.style.cssText = '';
                if (parked.next && parked.next.parentNode === parked.parent) {
                    parked.parent.insertBefore(parked.panel, parked.next);
                } else {
                    parked.parent.appendChild(parked.panel);
                }
                parked = null;
            }
            function showFlyout(drop) {
                if (!root.classList.contains('sidebar-mini')) return;
                clearTimeout(hideTimer);
                var panel = drop.querySelector(':scope > [role="region"]');
                if (!panel) return;
                if (parked && parked.panel === panel) return;
                restoreFlyout();
                var trigger = drop.querySelector('.nav-dropdown-trigger') || drop;
                var label = trigger.getAttribute('data-mini-title') || (trigger.innerText || '').replace(/\s+/g, ' ').trim();
                var rect = trigger.getBoundingClientRect();
                parked = { panel: panel, parent: drop, next: panel.nextSibling };
                if (label) {
                    var title = document.createElement('div');
                    title.className = 'sidebar-mini-flyout-title';
                    title.textContent = label;
                    panel.insertBefore(title, panel.firstChild);
                }
                document.body.appendChild(panel);
                panel.classList.add('sidebar-mini-flyout');
                var top = rect.top;
                if (top < 8) top = 8;
                if (top > window.innerHeight - 80) top = Math.max(8, window.innerHeight - 80);
                panel.style.position = 'fixed';
                panel.style.top = top + 'px';
                panel.style.left = (rect.right + 6) + 'px';
            }
            function queueHide() {
                clearTimeout(hideTimer);
                hideTimer = setTimeout(restoreFlyout, 160);
            }
            function syncTitles() {
                if (!sidebar) return;
                var mini = root.classList.contains('sidebar-mini');
                sidebar.querySelectorAll('.nav-item, .nav-dropdown-trigger').forEach(function (el) {
                    var label = el.getAttribute('data-mini-title');
                    if (!label) {
                        label = (el.innerText || '').replace(/\s+/g, ' ').trim();
                        el.setAttribute('data-mini-title', label);
                    }
                    el.title = mini && !el.classList.contains('nav-dropdown-trigger') ? label : '';
                });
            }
            if (sidebar && sidebar.dataset.flyouts !== '1') {
                sidebar.dataset.flyouts = '1';
                sidebar.querySelectorAll('[data-dropdown]').forEach(function (drop) {
                    drop.addEventListener('mouseenter', function () { showFlyout(drop); });
                    drop.addEventListener('mouseleave', queueHide);
                });
                document.addEventListener('mouseover', function (e) {
                    if (parked && parked.panel.contains(e.target)) clearTimeout(hideTimer);
                });
                document.addEventListener('mouseout', function (e) {
                    if (!parked) return;
                    var next = e.relatedTarget;
                    if (next && (parked.panel.contains(next) || parked.parent.contains(next))) return;
                    queueHide();
                });
            }
            function apply(on) {
                if (!on) restoreFlyout();
                root.classList.toggle('sidebar-mini', on);
                syncTitles();
                if (btn) {
                    btn.setAttribute('aria-pressed', on ? 'true' : 'false');
                    btn.setAttribute('aria-label', on ? 'Expand sidebar' : 'Minimize sidebar');
                    btn.title = on ? 'Expand sidebar' : 'Minimize sidebar';
                }
                window.setTimeout(function () {
                    window.dispatchEvent(new Event('resize'));
                }, 280);
            }
            if (btn && root.classList.contains('sidebar-mini')) {
                btn.setAttribute('aria-pressed', 'true');
                btn.setAttribute('aria-label', 'Expand sidebar');
                btn.title = 'Expand sidebar';
            }
            syncTitles();
            if (!btn) return;
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var on = !root.classList.contains('sidebar-mini');
                try { localStorage.setItem('sidebar-mini', on ? '1' : '0'); } catch (err) {}
                apply(on);
            });
        })();
    })();
    </script>
    @include('partials.assignment-user-select2')
    @stack('scripts')
    <script src="{{ asset('js/table-sort.js') }}?v={{ @filemtime(public_path('js/table-sort.js')) ?: '1' }}"></script>
    <script src="{{ asset('js/job-list-pagination.js') }}?v={{ @filemtime(public_path('js/job-list-pagination.js')) ?: '1' }}"></script>
    <script src="{{ asset('js/job-list-autosave.js') }}?v={{ @filemtime(public_path('js/job-list-autosave.js')) ?: '1' }}"></script>
    <script>
        if (typeof window.initAllTableSorts === 'function') {
            window.initAllTableSorts();
        }
        if (typeof window.JobListPagination === 'object' && window.JobListPagination.init) {
            window.JobListPagination.init();
        }
    </script>
</body>
</html>
