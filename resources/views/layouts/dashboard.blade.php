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
    @vite(['resources/js/layout.ts'])
    @stack('styles')
    @include('layouts.partials.select2-theme')
</head>
<body class="@yield('body_class', '')">
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

    <div class="main-wrap">
        @include('layouts.partials.header')

        <main class="content">
            @yield('content')
        </main>
    </div>

    <div class="modal-backdrop" id="logoutModal" role="dialog" aria-labelledby="logoutModalTitle" aria-modal="true">
        <div class="modal-box">
            <div class="modal-header">
                <svg class="modal-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <h2 class="modal-title" id="logoutModalTitle">Logout</h2>
            </div>
            <div class="modal-body">
                <p id="logoutModalMessage">Are you sure you want to logout?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" id="logoutModalCancel">Cancel</button>
                <button type="button" class="btn btn-confirm" id="logoutModalConfirm"><span class="btn-text">Logout</span></button>
            </div>
        </div>
    </div>

    <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display: none;" autocomplete="off">
        @csrf
    </form>

    @include('layouts.partials.toast')
    @include('layouts.partials.app-toast')

    <script>
    (function() {
        document.querySelectorAll('.nav-dropdown[data-dropdown]').forEach(function(wrap) {
            var trigger = wrap.querySelector('.nav-dropdown-trigger');
            if (!trigger) return;
            trigger.addEventListener('click', function() {
                var isOpen = wrap.classList.contains('open');
                document.querySelectorAll('.nav-dropdown.open').forEach(function(open) {
                    open.classList.remove('open');
                    var t = open.querySelector('.nav-dropdown-trigger');
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
        (function announcementLoop() {
            var textEl = document.getElementById('announcementText');
            var trackEl = document.querySelector('.announcement-ticker-track');
            if (!textEl || !trackEl) return;
            function setVarsAndStart() {
                var trackW = trackEl.offsetWidth;
                var textW = textEl.offsetWidth;
                textEl.style.setProperty('--start-x', trackW + 'px');
                textEl.style.setProperty('--end-x', (-textW) + 'px');
                textEl.style.transform = 'translateX(' + trackW + 'px)';
            }
            setVarsAndStart();
            requestAnimationFrame(function() { textEl.classList.add('run'); });
            textEl.addEventListener('animationend', function() {
                setVarsAndStart();
                textEl.classList.remove('run');
                requestAnimationFrame(function() {
                    void textEl.offsetWidth;
                    textEl.classList.add('run');
                });
            });
        })();
        (function hidePageLoader() {
            var loader = document.getElementById('pageLoader');
            if (!loader) return;
            function hide() {
                loader.classList.add('hide');
                setTimeout(function() { loader.remove(); }, 350);
            }
            if (document.readyState === 'complete') {
                setTimeout(hide, 80);
            } else {
                window.addEventListener('load', function() { setTimeout(hide, 80); });
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
