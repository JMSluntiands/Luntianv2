<header class="header">
    <button type="button" class="header-menu-btn" id="sidebarToggle" aria-label="Open menu" aria-expanded="false" aria-controls="sidebarNav">
        <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <span class="header-logo"></span>
    <div class="header-announcement">
        @hasSection('header_center')
            @yield('header_center')
        @else
            <div class="announcement-ticker-track">
                <span id="announcementText" class="announcement-ticker-text">Welcome to Luntian Dashboard. Check your jobs and calendar for updates.</span>
            </div>
        @endif
    </div>
    <div class="header-actions">
        @yield('header_extra')
        <div class="notification-wrap">
            <button type="button" class="icon-btn" id="notificationBtn" aria-label="Notifications" aria-expanded="false" aria-haspopup="true">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </button>
            <div class="notification-dropdown" id="notificationDropdown" role="menu" aria-label="Notifications menu">
                <div class="notification-dropdown-title">Notifications</div>
                <div class="notification-dropdown-list">
                    <div class="notification-item" role="menuitem">
                        <div class="notification-item-content">
                            <div class="notification-item-title">New job assigned</div>
                            <p class="notification-item-time">2 minutes ago</p>
                        </div>
                    </div>
                    <div class="notification-item" role="menuitem">
                        <div class="notification-item-content">
                            <div class="notification-item-title">Calendar event updated</div>
                            <p class="notification-item-time">1 hour ago</p>
                        </div>
                    </div>
                    <div class="notification-item" role="menuitem">
                        <div class="notification-item-content">
                            <div class="notification-item-title">Reminder: Task due tomorrow</div>
                            <p class="notification-item-time">Yesterday</p>
                        </div>
                    </div>
                </div>
                <div class="notification-dropdown-footer">
                    <a href="#" id="seeAllNotifications">See all notifications</a>
                </div>
            </div>
        </div>
        <button type="button" class="icon-btn theme-toggle-btn" id="themeToggle" aria-label="Toggle theme" title="Toggle dark/light mode">
            <span class="theme-toggle-icon active" id="themeIconSun" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg></span>
            <span class="theme-toggle-icon inactive" id="themeIconMoon" aria-hidden="true"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg></span>
        </button>
        <div style="position: relative;">
            <button type="button" class="user-btn" id="userMenuBtn" aria-expanded="false" aria-haspopup="true">
                @if(session('user_profile_image'))
                    <span class="user-avatar"><img src="{{ asset('storage/' . session('user_profile_image')) }}" alt=""></span>
                @else
                    <span class="user-avatar user-avatar-letter" aria-hidden="true">{{ strtoupper(substr(session('user_name', 'User'), 0, 1)) }}</span>
                @endif
                <span>{{ session('user_name', 'User') }}</span>
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="user-dropdown" id="userDropdown" role="menu">
                <a href="#" role="menuitem">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Account Settings
                </a>
                <a href="#" role="menuitem" id="logoutBtn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </a>
            </div>
        </div>
    </div>
</header>
