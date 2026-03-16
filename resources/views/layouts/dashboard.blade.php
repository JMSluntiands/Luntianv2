<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Luntian</title>
    <script>
        (function(){
            var t = (typeof localStorage !== 'undefined' && localStorage.getItem('theme')) || '';
            var theme = (String(t).toLowerCase() === 'light') ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    @include('layouts.partials.dashboard-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/layout.ts'])
    @stack('styles')
    @include('layouts.partials.select2-theme')
</head>
<body class="overflow-x-hidden @yield('body_class', '')">
    <div class="page-loader" id="pageLoader" aria-hidden="true" data-theme="">
        <div class="page-loader-spinner"></div>
        <span class="page-loader-logo">Luntian</span>
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
        (function initThemeToggle() {
            var themeToggle = document.getElementById('themeToggle');
            var iconSun = document.getElementById('themeIconSun');
            var iconMoon = document.getElementById('themeIconMoon');
            function applyTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                if (iconSun && iconMoon) {
                    var isDark = theme !== 'light';
                    iconSun.classList.toggle('active', isDark);
                    iconSun.classList.toggle('inactive', !isDark);
                    iconMoon.classList.toggle('active', !isDark);
                    iconMoon.classList.toggle('inactive', isDark);
                }
            }
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    var next = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
                    applyTheme(next);
                });
                applyTheme(localStorage.getItem('theme') === 'light' ? 'light' : 'dark');
            }
        })();
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
    })();
    </script>
    @stack('scripts')
</body>
</html>
