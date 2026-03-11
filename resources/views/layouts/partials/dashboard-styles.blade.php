{{-- Shared layout: sidebar, navbar, content scrollbar, dropdowns, modal, theme. Include via @include('layouts.partials.dashboard-styles') --}}
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html { height: 100%; width: 100%; max-height: 100vh; background: #1e293b; overflow: hidden; }
    body { font-family: system-ui, -apple-system, sans-serif; background: #1e293b; color: #e2e8f0; width: 100%; max-width: 100%; height: 100vh; max-height: 100vh; overflow: hidden; margin: 0; }
    /* Sidebar – fixed so body height = main content only, no extra space from sidebar */
    .sidebar { position: fixed; left: 0; top: 0; width: 240px; height: 100vh; background: #0f172a; padding: 0; display: flex; flex-direction: column; z-index: 30; overflow: hidden; }
    .sidebar-logo { height: 56px; min-height: 56px; display: flex; align-items: center; padding: 0 1.25rem; margin-bottom: 1rem; font-weight: 700; font-size: 1.1rem; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.08); flex-shrink: 0; }
    .sidebar-nav { padding: 1rem 0 0.5rem; flex: 1; min-height: 0; overflow-y: auto; }
    .sidebar .nav-item { display: flex; align-items: center; gap: 10px; padding: 12px 20px; color: #94a3b8; text-decoration: none; transition: background 0.2s, color 0.2s; }
    .sidebar .nav-item:hover { background: rgba(255,255,255,0.06); color: #e2e8f0; }
    .sidebar .nav-item.active { background: rgba(44,82,139,0.25); color: #93c5fd; border-left: 3px solid #2C528B; padding-left: 17px; }
    .sidebar .nav-icon { width: 20px; height: 20px; opacity: 0.9; flex-shrink: 0; }
    .sidebar .nav-category { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; padding: 14px 20px 6px; margin-top: 4px; }
    .nav-dropdown { margin: 0; }
    .nav-dropdown-trigger { width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 20px; color: #94a3b8; background: none; border: none; font: inherit; cursor: pointer; text-align: left; transition: background 0.2s, color 0.2s; }
    .nav-dropdown-trigger:hover { background: rgba(255,255,255,0.06); color: #e2e8f0; }
    .nav-dropdown-trigger .nav-trigger-inner { display: flex; align-items: center; gap: 10px; }
    .nav-dropdown-trigger .nav-chevron { width: 16px; height: 16px; opacity: 0.6; flex-shrink: 0; transition: transform 0.25s ease; }
    .nav-dropdown.open .nav-chevron { transform: rotate(180deg); }
    .nav-submenu { overflow: hidden; max-height: 0; transition: max-height 0.35s ease-out; }
    .nav-dropdown.open .nav-submenu { max-height: 320px; }
    .nav-submenu-inner { padding: 4px 0 8px 0; }
    .nav-subitem { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 8px 20px 8px 44px; color: #94a3b8; text-decoration: none; font-size: 0.9rem; transition: background 0.2s, color 0.2s; opacity: 0; transform: translateY(-4px); transition: opacity 0.25s ease, transform 0.25s ease, background 0.2s, color 0.2s; }
    .nav-dropdown.open .nav-subitem { opacity: 1; transform: translateY(0); }
    .nav-dropdown.open .nav-subitem:nth-child(1) { transition-delay: 0.03s; }
    .nav-dropdown.open .nav-subitem:nth-child(2) { transition-delay: 0.06s; }
    .nav-dropdown.open .nav-subitem:nth-child(3) { transition-delay: 0.09s; }
    .nav-dropdown.open .nav-subitem:nth-child(4) { transition-delay: 0.12s; }
    .nav-dropdown.open .nav-subitem:nth-child(5) { transition-delay: 0.15s; }
    .nav-dropdown.open .nav-subitem:nth-child(6) { transition-delay: 0.18s; }
    .nav-subitem:hover { background: rgba(255,255,255,0.06); color: #e2e8f0; }
    .nav-subitem.active { background: rgba(44,82,139,0.25); color: #93c5fd; border-left: 3px solid #2C528B; margin-left: 0; padding-left: 41px; }
    .nav-subitem .nav-subitem-label { flex: 1; min-width: 0; }
    .nav-subitem .nav-badge { flex-shrink: 0; min-width: 20px; height: 20px; padding: 0 6px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 600; color: #fee2e2; background: #b91c1c; border-radius: 10px; line-height: 1; }
    .nav-subitem.active .nav-badge { background: #ef4444; color: #fef2f2; }
    .nav-badge-danger { background: #b91c1c; color: #fee2e2; }
    .nav-subitem.active .nav-badge-danger { background: #ef4444; color: #fef2f2; }
    /* Main layout */
    .main-wrap { margin-left: 240px; min-width: 0; max-width: 100%; height: 100vh; max-height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
    /* Header / Navbar */
    .header { position: sticky; top: 0; z-index: 50; height: 56px; min-height: 56px; background: #0f172a; border-bottom: 1px solid #1e293b; display: flex; align-items: center; justify-content: space-between; padding: 0 1rem 0 1.5rem; flex-shrink: 0; gap: 0.75rem; overflow: visible; }
    .header-logo { font-weight: 700; font-size: 1rem; color: #fff; flex-shrink: 0; }
    .header-announcement { flex: 1; min-width: 0; display: flex; align-items: center; min-height: 56px; overflow: hidden; margin: 0 0.5rem; }
    .announcement-ticker-track { width: 100%; overflow: hidden; }
    .announcement-ticker-text { white-space: nowrap; color: #e2e8f0; font-size: 0.9rem; display: inline-block; }
    .announcement-ticker-text.run { animation: announcement-scroll 28s linear forwards; }
    @keyframes announcement-scroll { from { transform: translateX(var(--start-x, 100%)); } to { transform: translateX(var(--end-x, -100%)); } }
    .header-actions { display: flex; align-items: center; gap: 8px; position: relative; flex-shrink: 0; }
    .header-actions .icon-btn { background: transparent; border: none; color: #e2e8f0; width: 40px; height: 40px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .header-actions .icon-btn:hover { background: rgba(255,255,255,0.08); }
    .header-actions .icon-btn svg { width: 20px; height: 20px; }
    .user-btn { background: transparent; border: none; color: #e2e8f0; padding: 6px 10px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; text-decoration: none; font: inherit; }
    .user-btn:hover { background: rgba(255,255,255,0.08); }
    .user-btn .user-avatar { width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .user-btn .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .user-btn .user-avatar-letter { background: #334155; color: #e2e8f0; font-size: 0.85rem; font-weight: 600; }
    .user-dropdown { position: absolute; top: 100%; right: 0; margin-top: 4px; background: #fff; color: #1e293b; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); min-width: 180px; padding: 6px; z-index: 100; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: opacity 0.2s ease-out, transform 0.2s ease-out, visibility 0.2s; pointer-events: none; }
    .user-dropdown.show { opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto; }
    .user-dropdown a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; color: #334155; text-decoration: none; border-radius: 6px; font-size: 0.9rem; }
    .user-dropdown a:hover { background: #f1f5f9; }
    .user-dropdown a svg { width: 18px; height: 18px; opacity: 0.8; }
    .notification-wrap { position: relative; }
    .notification-dropdown { position: absolute; top: 100%; right: 0; margin-top: 4px; background: #fff; color: #1e293b; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); min-width: 320px; max-width: 380px; max-height: 400px; z-index: 100; overflow: hidden; display: flex; flex-direction: column; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: opacity 0.2s ease-out, transform 0.2s ease-out, visibility 0.2s; pointer-events: none; }
    .notification-dropdown.show { opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto; }
    .notification-dropdown-title { padding: 12px 16px; font-size: 0.9rem; font-weight: 600; color: #334155; border-bottom: 1px solid #e2e8f0; }
    .notification-dropdown-list { overflow-y: auto; max-height: 280px; }
    .notification-item { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; color: #475569; cursor: pointer; transition: background 0.15s; }
    .notification-item:hover { background: #f8fafc; }
    .notification-item:last-of-type { border-bottom: none; }
    .notification-item-content { flex: 1; min-width: 0; }
    .notification-item-title { font-weight: 500; color: #334155; margin-bottom: 2px; }
    .notification-item-time { font-size: 0.75rem; color: #94a3b8; }
    .notification-dropdown-footer { padding: 10px 16px; border-top: 1px solid #e2e8f0; background: #f8fafc; }
    .notification-dropdown-footer a { display: block; text-align: center; padding: 8px 12px; font-size: 0.875rem; font-weight: 500; color: #2C528B; text-decoration: none; border-radius: 6px; transition: background 0.15s; }
    .notification-dropdown-footer a:hover { background: rgba(44,82,139,0.1); }
    /* Content + scrollbar */
    .content { flex: 1; min-height: 0; padding: 1.5rem; overflow-x: hidden; overflow-y: auto; }
    .content h1 { font-size: 1.5rem; margin-bottom: 0.25rem; color: #fff; }
    .content .subtitle { color: #94a3b8; font-size: 0.9rem; margin-bottom: 1.5rem; }
    .card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .card { background: #0f172a; border: 1px solid #334155; border-radius: 12px; padding: 1.25rem; }
    .card .label { font-size: 0.8rem; color: #94a3b8; margin-bottom: 0.25rem; }
    .card .value { font-size: 1.5rem; font-weight: 700; color: #fff; }
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 8px; font-size: 0.9rem; font-weight: 500; cursor: pointer; text-decoration: none; border: none; transition: background 0.2s, transform 0.05s; }
    .btn:active { transform: scale(0.98); }
    .btn-primary { background: #2C528B; color: #fff; }
    .btn-primary:hover { background: #234a77; }
    .btn-secondary { background: #334155; color: #e2e8f0; }
    .btn-secondary:hover { background: #475569; }
    .section-title { font-size: 1rem; color: #cbd5e1; margin-bottom: 0.75rem; }
    /* Modal – centered, crystal/glass overlay (dark/light theme) */
    .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.35); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); display: none; align-items: center; justify-content: center; z-index: 1000; padding: 1rem; }
    .modal-backdrop.show { display: flex; animation: modal-backdrop-in 0.25s ease-out forwards; }
    .modal-box { background: rgba(30,41,59,0.95); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid #334155; border-radius: 12px; box-shadow: 0 20px 50px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,255,255,0.05) inset; max-width: 400px; width: 100%; overflow: hidden; opacity: 0; transform: scale(0.95); }
    .modal-backdrop.show .modal-box { animation: modal-box-in 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) 0.06s forwards; }
    @keyframes modal-backdrop-in { from { opacity: 0; } to { opacity: 1; } }
    @keyframes modal-box-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .modal-header { display: flex; align-items: center; gap: 10px; padding: 1.25rem 1.5rem; border-bottom: 1px solid #334155; }
    .modal-header .modal-icon { width: 24px; height: 24px; stroke: #f87171; flex-shrink: 0; }
    .modal-header .modal-title { font-size: 1.125rem; font-weight: 700; color: #e2e8f0; }
    .modal-body { padding: 1.25rem 1.5rem; color: #94a3b8; font-size: 0.9375rem; line-height: 1.5; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 1rem 1.5rem 1.25rem; background: #1e293b; border-top: 1px solid #334155; }
    .modal-footer .btn { padding: 10px 1.25rem; font-size: 0.9rem; border-radius: 8px; cursor: pointer; border: none; font-weight: 500; }
    .modal-footer .btn-cancel { background: #64748b; color: #fff; }
    .modal-footer .btn-cancel:hover { background: #475569; }
    .modal-footer .btn-confirm { background: #334155; color: #fff; }
    .modal-footer .btn-confirm:hover:not(:disabled) { background: #1e293b; }
    .modal-footer .btn-confirm:disabled { opacity: 0.8; cursor: not-allowed; }
    .modal-footer .btn-danger { background: #dc2626; color: #fff; }
    .modal-footer .btn-danger:hover:not(:disabled) { background: #b91c1c; }
    html[data-theme="light"] .modal-backdrop { background: rgba(0,0,0,0.2); }
    html[data-theme="light"] .modal-box { background: rgba(255,255,255,0.95); border-color: rgba(255,255,255,0.4); box-shadow: 0 20px 50px rgba(0,0,0,0.2), 0 0 0 1px rgba(255,255,255,0.1) inset; }
    html[data-theme="light"] .modal-header { border-bottom-color: #e2e8f0; }
    html[data-theme="light"] .modal-header .modal-icon { stroke: #dc2626; }
    html[data-theme="light"] .modal-header .modal-title { color: #0f172a; }
    html[data-theme="light"] .modal-body { color: #475569; }
    html[data-theme="light"] .modal-footer { background: #f8fafc; border-top-color: #e2e8f0; }
    .modal-footer .btn-confirm .spinner,
    .modal-footer .btn-danger .spinner { display: inline-block; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: modal-spin 0.7s linear infinite; vertical-align: middle; margin-right: 6px; }
    @keyframes modal-spin { to { transform: rotate(360deg); } }
    /* Page load overlay – light style via html or loader data-theme so it applies on first paint */
    .page-loader { position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; transition: opacity 0.3s ease, visibility 0.3s ease; background: #0f172a; }
    html[data-theme="light"] .page-loader,
    .page-loader[data-theme="light"] { background: #f1f5f9; }
    html[data-theme="light"] .page-loader .page-loader-spinner,
    .page-loader[data-theme="light"] .page-loader-spinner { border-color: #e2e8f0; border-top-color: #2C528B; }
    html[data-theme="light"] .page-loader .page-loader-logo,
    .page-loader[data-theme="light"] .page-loader-logo { color: #94a3b8; }
    .page-loader.hide { opacity: 0; visibility: hidden; pointer-events: none; }
    .page-loader-spinner { width: 48px; height: 48px; border: 4px solid #334155; border-top-color: #2C528B; border-radius: 50%; animation: page-loader-spin 0.8s linear infinite; }
    .page-loader-logo { position: absolute; font-weight: 700; font-size: 1.1rem; color: #64748b; margin-top: 72px; }
    @keyframes page-loader-spin { to { transform: rotate(360deg); } }
    /* Theme toggle */
    .theme-toggle-btn { position: relative; }
    .theme-toggle-icon { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.25s ease; display: inline-flex; align-items: center; justify-content: center; }
    .theme-toggle-icon.active { transform: rotate(0deg); opacity: 1; }
    .theme-toggle-icon.inactive { transform: rotate(-90deg); opacity: 0; pointer-events: none; position: absolute; left: 0; top: 0; right: 0; bottom: 0; margin: auto; }
    /* Global scrollbar – iisang design sa lahat (content, notes, table wrap, dropdown, modal, etc.) */
    .content, .lbs-notes-body, .bph-notes-body, .csp-notes-body, .bluinq-notes-body, .nh-notes-body, .lc_home_builder-notes-body, .efficient_living-notes-body, .leading_energy-notes-body, .lbs-table-wrap, .bph-table-wrap, .csp-table-wrap, .bluinq-table-wrap, .nh-table-wrap, .lc_home_builder-table-wrap, .efficient_living-table-wrap, .leading_energy-table-wrap, .select2-results__options, .job-view-activity, .notification-dropdown-list, .modal-box,
    [class*="-detail-panel"], [class*="-comment-body"] { scrollbar-width: thin; scrollbar-color: #475569 #1e293b; }
    *::-webkit-scrollbar { width: 8px; height: 8px; }
    *::-webkit-scrollbar-track { background: #1e293b; }
    *::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #475569 0%, #334155 100%);
        border-radius: 4px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.06);
    }
    *::-webkit-scrollbar-thumb:hover { background: #64748b; }
    *::-webkit-scrollbar-button:vertical:decrement,
    *::-webkit-scrollbar-button:vertical:increment {
        height: 14px;
        background: #1e293b;
        border: none;
        display: block;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3E%3Cpath fill='%2394a3b8' d='M4 2L1 5h6L4 2z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
    }
    *::-webkit-scrollbar-button:vertical:increment {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3E%3Cpath fill='%2394a3b8' d='M4 6L1 3h6L4 6z'/%3E%3C/svg%3E");
    }
    *::-webkit-scrollbar-button:horizontal:decrement,
    *::-webkit-scrollbar-button:horizontal:increment {
        width: 14px;
        background: #1e293b;
        border: none;
        display: block;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3E%3Cpath fill='%2394a3b8' d='M2 4L5 1v6L2 4z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
    }
    *::-webkit-scrollbar-button:horizontal:increment {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3E%3Cpath fill='%2394a3b8' d='M6 4L3 1v6l3-3z'/%3E%3C/svg%3E");
    }
    /* Sidebar: scrollable but hide scrollbar (nav only) */
    .sidebar .sidebar-nav,
    .sidebar-nav { scrollbar-width: none !important; -ms-overflow-style: none !important; }
    .sidebar .sidebar-nav::-webkit-scrollbar,
    .sidebar-nav::-webkit-scrollbar { display: none !important; width: 0 !important; height: 0 !important; background: transparent !important; }
    /* Light theme - layout */
    html[data-theme="light"],
    html[data-theme="light"] body { background: #f1f5f9; color: #1e293b; transition: background 0.25s, color 0.25s; }
    html[data-theme="light"] .sidebar { background: #fff; border-right: 1px solid #e2e8f0; }
    html[data-theme="light"] .sidebar-logo { color: #0f172a; border-bottom-color: #e2e8f0; }
    html[data-theme="light"] .sidebar .nav-item { color: #64748b; }
    html[data-theme="light"] .sidebar .nav-item:hover { background: #f1f5f9; color: #334155; }
    html[data-theme="light"] .sidebar .nav-item.active { background: rgba(44,82,139,0.12); color: #2C528B; border-left-color: #2C528B; }
    html[data-theme="light"] .sidebar .nav-category { color: #94a3b8; }
    html[data-theme="light"] .nav-dropdown-trigger { color: #64748b; }
    html[data-theme="light"] .nav-dropdown-trigger:hover { background: #f1f5f9; color: #334155; }
    html[data-theme="light"] .nav-subitem { color: #64748b; }
    html[data-theme="light"] .nav-subitem:hover { background: #f1f5f9; color: #334155; }
    html[data-theme="light"] .nav-subitem.active { background: rgba(44,82,139,0.12); color: #2C528B; border-left-color: #2C528B; }
    html[data-theme="light"] .nav-subitem .nav-badge { background: #dc2626; color: #fef2f2; }
    html[data-theme="light"] .nav-subitem.active .nav-badge { background: #b91c1c; color: #fee2e2; }
    html[data-theme="light"] .nav-badge-danger { background: #dc2626; color: #fef2f2; }
    html[data-theme="light"] .nav-subitem.active .nav-badge-danger { background: #b91c1c; color: #fee2e2; }
    html[data-theme="light"] .header { background: #fff; border-bottom-color: #e2e8f0; }
    html[data-theme="light"] .header-logo { color: #0f172a; }
    html[data-theme="light"] .header-actions .icon-btn { color: #475569; }
    html[data-theme="light"] .header-actions .icon-btn:hover { background: #f1f5f9; }
    html[data-theme="light"] .user-btn { color: #334155; }
    html[data-theme="light"] .user-btn:hover { background: #f1f5f9; }
    html[data-theme="light"] .user-btn .user-avatar-letter { background: #cbd5e1; color: #334155; }
    html[data-theme="light"] .notification-dropdown { box-shadow: 0 4px 20px rgba(0,0,0,0.12); }
    html[data-theme="light"] .notification-dropdown-footer a:hover { background: rgba(44,82,139,0.08); }
    html[data-theme="light"] .announcement-ticker-text { color: #475569; }
    html[data-theme="light"] .content h1 { color: #0f172a; }
    html[data-theme="light"] .content .subtitle { color: #64748b; }
    html[data-theme="light"] .card { background: #fff; border-color: #e2e8f0; }
    html[data-theme="light"] .card .label { color: #64748b; }
    html[data-theme="light"] .card .value { color: #0f172a; }
    html[data-theme="light"] .section-title { color: #475569; }
    html[data-theme="light"] .content,
    html[data-theme="light"] .lbs-notes-body,
    html[data-theme="light"] .bph-notes-body,
    html[data-theme="light"] .csp-notes-body,
    html[data-theme="light"] .bluinq-notes-body,
    html[data-theme="light"] .nh-notes-body,
    html[data-theme="light"] .lc_home_builder-notes-body,
    html[data-theme="light"] .efficient_living-notes-body,
    html[data-theme="light"] .leading_energy-notes-body,
    html[data-theme="light"] .lbs-table-wrap,
    html[data-theme="light"] .bph-table-wrap,
    html[data-theme="light"] .csp-table-wrap,
    html[data-theme="light"] .bluinq-table-wrap,
    html[data-theme="light"] .nh-table-wrap,
    html[data-theme="light"] .lc_home_builder-table-wrap,
    html[data-theme="light"] .efficient_living-table-wrap,
    html[data-theme="light"] .leading_energy-table-wrap,
    html[data-theme="light"] .select2-results__options { scrollbar-color: #94a3b8 #e2e8f0; }
    html[data-theme="light"] *::-webkit-scrollbar-track { background: #e2e8f0; }
    html[data-theme="light"] *::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #cbd5e1 0%, #94a3b8 100%);
        border-radius: 4px;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.5);
    }
    html[data-theme="light"] *::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    html[data-theme="light"] *::-webkit-scrollbar-button:vertical:decrement,
    html[data-theme="light"] *::-webkit-scrollbar-button:vertical:increment {
        background: #e2e8f0 !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3E%3Cpath fill='%2364748b' d='M4 2L1 5h6L4 2z'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: center !important;
    }
    html[data-theme="light"] *::-webkit-scrollbar-button:vertical:increment {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3E%3Cpath fill='%2364748b' d='M4 6L1 3h6L4 6z'/%3E%3C/svg%3E") !important;
    }
    html[data-theme="light"] *::-webkit-scrollbar-button:horizontal:decrement,
    html[data-theme="light"] *::-webkit-scrollbar-button:horizontal:increment {
        background: #e2e8f0 !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3E%3Cpath fill='%2364748b' d='M2 4L5 1v6L2 4z'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: center !important;
    }
    html[data-theme="light"] *::-webkit-scrollbar-button:horizontal:increment {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3E%3Cpath fill='%2364748b' d='M6 4L3 1v6l3-3z'/%3E%3C/svg%3E") !important;
    }

    /* ========== Dashboard pagination (Client, Staff, Compliance, etc.) ========== */
    .dashboard-pagination { width: 100%; }
    .dashboard-pagination-inner { display: flex; flex-direction: column; gap: 0.75rem; }
    .dashboard-pagination-mobile { display: flex; gap: 0.5rem; align-items: center; }
    .dashboard-pagination-desktop { display: none; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; }
    @media (min-width: 640px) {
        .dashboard-pagination-mobile { display: none; }
        .dashboard-pagination-desktop { display: flex; }
    }
    .dashboard-pagination-info-text { font-size: 0.875rem; color: #94a3b8; margin: 0; }
    .dashboard-pagination-info-num { font-weight: 600; color: #e2e8f0; }
    .dashboard-pagination-nav { display: inline-flex; align-items: center; flex-wrap: wrap; gap: 0; border-radius: 10px; overflow: hidden; border: 1px solid #334155; box-shadow: 0 1px 2px rgba(0,0,0,0.08); }
    .dashboard-pagination-nav-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 0.5rem; font-size: 0.875rem; font-weight: 500; color: #94a3b8; background: #1e293b; border: none; border-right: 1px solid #334155; text-decoration: none; transition: background 0.15s, color 0.15s; }
    .dashboard-pagination-nav-btn:last-of-type { border-right: none; }
    .dashboard-pagination-nav-btn:hover { background: #334155; color: #e2e8f0; }
    .dashboard-pagination-nav-btn-active { background: #2C528B; color: #fff; cursor: default; font-weight: 600; }
    .dashboard-pagination-nav-btn-disabled { opacity: 0.6; cursor: not-allowed; pointer-events: none; }
    .dashboard-pagination-nav-prev { border-radius: 10px 0 0 10px; }
    .dashboard-pagination-nav-next { border-radius: 0 10px 10px 0; }
    .dashboard-pagination-ellipsis { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; font-size: 0.875rem; color: #64748b; }
    .dashboard-pagination-icon { width: 18px; height: 18px; }
    .dashboard-pagination-btn { display: inline-flex; align-items: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; color: #e2e8f0; background: #2C528B; border-radius: 8px; text-decoration: none; transition: background 0.15s; }
    .dashboard-pagination-btn:hover { background: #234a77; color: #fff; }
    .dashboard-pagination-btn-disabled { background: #334155; color: #94a3b8; cursor: not-allowed; }
    html[data-theme="light"] .dashboard-pagination-info-text { color: #64748b; }
    html[data-theme="light"] .dashboard-pagination-info-num { color: #1e293b; }
    html[data-theme="light"] .dashboard-pagination-nav { border-color: #e2e8f0; }
    html[data-theme="light"] .dashboard-pagination-nav-btn { color: #64748b; background: #f8fafc; border-right-color: #e2e8f0; }
    html[data-theme="light"] .dashboard-pagination-nav-btn:hover { background: #e2e8f0; color: #334155; }
    html[data-theme="light"] .dashboard-pagination-nav-btn-active { background: #2C528B; color: #fff; }
    html[data-theme="light"] .dashboard-pagination-nav-btn-disabled { opacity: 0.6; }
    html[data-theme="light"] .dashboard-pagination-ellipsis { color: #94a3b8; }
    html[data-theme="light"] .dashboard-pagination-btn { background: #2C528B; color: #fff; }
    html[data-theme="light"] .dashboard-pagination-btn-disabled { background: #e2e8f0; color: #94a3b8; }

    /* ========== RESPONSIVE: Admin Dashboard ========== */
    /* Sidebar overlay (mobile) */
    .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 25; opacity: 0; visibility: hidden; transition: opacity 0.25s ease, visibility 0.25s ease; }
    body.sidebar-open .sidebar-overlay { display: block; opacity: 1; visibility: visible; }
    .header-menu-btn { display: none; align-items: center; justify-content: center; width: 40px; height: 40px; padding: 0; border: none; border-radius: 8px; background: transparent; color: #e2e8f0; cursor: pointer; margin-right: 0.25rem; }
    .header-menu-btn:hover { background: rgba(255,255,255,0.08); }
    .header-menu-btn svg { width: 22px; height: 22px; }
    html[data-theme="light"] .header-menu-btn { color: #475569; }
    html[data-theme="light"] .header-menu-btn:hover { background: #f1f5f9; }

    @media (max-width: 1024px) {
        .sidebar { width: 260px; transform: translateX(-100%); transition: transform 0.25s ease-out, box-shadow 0.25s ease-out; }
        body.sidebar-open .sidebar { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,0.25); }
        .main-wrap { margin-left: 0; }
        .header-menu-btn { display: flex; }
        .content { padding: 1rem; }
        .card-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 0.75rem; }
    }

    @media (max-width: 768px) {
        .sidebar { width: 280px; }
        .header { padding: 0 0.75rem 0 1rem; gap: 0.5rem; min-height: 52px; height: auto; }
        .header-logo { font-size: 0.9rem; }
        .header-announcement { margin: 0 0.25rem; min-height: 48px; }
        .announcement-ticker-text { font-size: 0.8rem; }
        .user-btn span:not(.user-avatar):not(.user-avatar-letter) { display: none; }
        .user-btn svg { margin-left: 0; }
        .notification-dropdown { min-width: 0; width: min(320px, calc(100vw - 2rem)); max-width: calc(100vw - 2rem); right: 0; left: auto; }
        .user-dropdown { min-width: 160px; }
        .content { padding: 0.75rem 1rem; }
        .content h1 { font-size: 1.25rem; }
        .content .subtitle { font-size: 0.85rem; margin-bottom: 1rem; }
        .modal-backdrop { padding: 0.75rem; align-items: center; justify-content: center; }
        .modal-box { max-width: calc(100% - 1.5rem); margin: 0; border-radius: 12px; }
    }

    @media (max-width: 480px) {
        .header-actions { gap: 4px; }
        .header .icon-btn { width: 36px; height: 36px; }
        .content { padding: 0.5rem 0.75rem; }
        .card-grid { grid-template-columns: 1fr; }
        .header-announcement { flex: 1; min-width: 0; }
        .announcement-ticker-text { font-size: 0.75rem; }
    }
</style>
