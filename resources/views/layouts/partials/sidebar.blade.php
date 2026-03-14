@php
    $active = $sidebar_active ?? 'dashboard';
    $userRole = session('user_role');
    $isStaff = strtolower((string) ($userRole ?? '')) === 'staff';
    $lbsOpen = in_array($active, ['lbs.add', 'lbs.list', 'lbs.completed', 'lbs.review', 'lbs.mailbox', 'lbs.trash']) || str_starts_with((string)$active, 'lbs.');
    $bphOpen = in_array($active, ['bph.add', 'bph.list']) || str_starts_with((string)$active, 'bph.');
    $bluinqOpen = in_array($active, ['bluinq.add', 'bluinq.list', 'bluinq.completed', 'bluinq.review', 'bluinq.trash']) || str_starts_with((string)$active, 'bluinq.');
    $cspOpen = in_array($active, ['csp.add', 'csp.list', 'csp.completed', 'csp.review', 'csp.trash']) || str_starts_with((string)$active, 'csp.');
    $nhOpen = in_array($active, ['nh.add', 'nh.list', 'nh.completed', 'nh.review', 'nh.trash']) || str_starts_with((string)$active, 'nh.');
    $lcHomeBuilderOpen = in_array($active, ['lc_home_builder.add', 'lc_home_builder.list', 'lc_home_builder.completed', 'lc_home_builder.review', 'lc_home_builder.trash']) || str_starts_with((string)$active, 'lc_home_builder.');
    $efficientLivingOpen = in_array($active, ['efficient_living.add', 'efficient_living.list', 'efficient_living.completed', 'efficient_living.review', 'efficient_living.trash']) || str_starts_with((string)$active, 'efficient_living.');
    $leadingEnergyOpen = in_array($active, ['leading_energy.add', 'leading_energy.list', 'leading_energy.completed', 'leading_energy.review', 'leading_energy.trash']) || str_starts_with((string)$active, 'leading_energy.');
    $jobOpen = in_array($active, ['compliance.index', 'compliance.create', 'compliance.edit', 'priority.index', 'priority.create', 'priority.edit', 'status.index', 'status.create', 'status.edit', 'job_request.index', 'job_request.create', 'job_request.edit', 'client.index', 'client.create', 'client.edit']) || str_starts_with((string)$active, 'compliance.') || str_starts_with((string)$active, 'priority.') || str_starts_with((string)$active, 'status.') || str_starts_with((string)$active, 'job_request.') || str_starts_with((string)$active, 'client.');
    $branchOpen = in_array($active, ['branch.index', 'branch.create', 'branch.edit', 'branch.archive']) || str_starts_with((string)$active, 'branch.');
    $accountsOpen = in_array($active, ['users.index', 'users.create', 'users.edit', 'users.archive', 'accounts.clients.index', 'accounts.clients.create', 'accounts.clients.edit']) || str_starts_with((string)$active, 'users.') || str_starts_with((string)$active, 'accounts.clients.');
    $lbsListCount = $lbs_list_count ?? 13;
    $lbsReviewCount = $lbs_review_count ?? 28;
    $lbsMailboxCount = $lbs_mailbox_count ?? 55;
@endphp
<aside class="sidebar" id="sidebarNav" role="navigation" aria-label="Main navigation">
    <div class="sidebar-logo">Luntian</div>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ $active === 'dashboard' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            Dashboard
        </a>
        <div class="nav-category">Job Management</div>
        <div class="nav-dropdown {{ $lbsOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="{{ $lbsOpen ? 'true' : 'false' }}" aria-controls="nav-sub-lbs">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    LBS
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-lbs" role="region" aria-label="LBS submenu">
                <div class="nav-submenu-inner">
                    @if($isStaff)
                        <a href="{{ route('lbs.list') }}" class="nav-subitem {{ $active === 'lbs.list' ? 'active' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge nav-badge-danger" data-lbs-sidebar="allocated">{{ $lbsListCount }}</span>
                        </a>
                        <a href="{{ route('lbs.completed') }}" class="nav-subitem {{ $active === 'lbs.completed' ? 'active' : '' }}">Completed</a>
                    @else
                        <a href="{{ route('lbs.add') }}" class="nav-subitem {{ $active === 'lbs.add' ? 'active' : '' }}">Add New</a>
                        <a href="{{ route('lbs.list') }}" class="nav-subitem {{ $active === 'lbs.list' ? 'active' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge nav-badge-danger" data-lbs-sidebar="allocated">{{ $lbsListCount }}</span>
                        </a>
                        <a href="{{ route('lbs.completed') }}" class="nav-subitem {{ $active === 'lbs.completed' ? 'active' : '' }}">Completed</a>
                        <a href="{{ route('lbs.review') }}" class="nav-subitem {{ $active === 'lbs.review' ? 'active' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge nav-badge-danger" data-lbs-sidebar="for-review">{{ $lbsReviewCount }}</span>
                        </a>
                        <a href="{{ route('lbs.mailbox') }}" class="nav-subitem {{ $active === 'lbs.mailbox' ? 'active' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge nav-badge-danger">{{ $lbsMailboxCount }}</span>
                        </a>
                        <a href="{{ route('lbs.trash') }}" class="nav-subitem {{ $active === 'lbs.trash' ? 'active' : '' }}">Archive</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="nav-dropdown {{ $bphOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="{{ $bphOpen ? 'true' : 'false' }}" aria-controls="nav-sub-bph">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    BPH
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-bph" role="region" aria-label="BPH submenu">
                <div class="nav-submenu-inner">
                    @if($isStaff)
                        <a href="{{ route('bph.list') }}" class="nav-subitem {{ $active === 'bph.list' ? 'active' : '' }}">List</a>
                        <a href="#" class="nav-subitem">Completed</a>
                    @else
                        <a href="{{ route('bph.add') }}" class="nav-subitem {{ $active === 'bph.add' ? 'active' : '' }}">Add New</a>
                        <a href="{{ route('bph.list') }}" class="nav-subitem {{ $active === 'bph.list' ? 'active' : '' }}">List</a>
                        <a href="#" class="nav-subitem">Completed</a>
                        <a href="#" class="nav-subitem">For Review</a>
                        <a href="#" class="nav-subitem">Mailbox</a>
                        <a href="#" class="nav-subitem">Archive</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="nav-dropdown {{ $bluinqOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="{{ $bluinqOpen ? 'true' : 'false' }}" aria-controls="nav-sub-bluinq">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    BLUINQ
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-bluinq" role="region" aria-label="BLUINQ submenu">
                <div class="nav-submenu-inner">
                    @if($isStaff)
                        <a href="{{ route('bluinq.list') }}" class="nav-subitem {{ $active === 'bluinq.list' ? 'active' : '' }}">List</a>
                        <a href="{{ route('bluinq.completed') }}" class="nav-subitem {{ $active === 'bluinq.completed' ? 'active' : '' }}">Completed</a>
                    @else
                        <a href="{{ route('bluinq.add') }}" class="nav-subitem {{ $active === 'bluinq.add' ? 'active' : '' }}">Add New</a>
                        <a href="{{ route('bluinq.list') }}" class="nav-subitem {{ $active === 'bluinq.list' ? 'active' : '' }}">List</a>
                        <a href="{{ route('bluinq.completed') }}" class="nav-subitem {{ $active === 'bluinq.completed' ? 'active' : '' }}">Completed</a>
                        <a href="{{ route('bluinq.review') }}" class="nav-subitem {{ $active === 'bluinq.review' ? 'active' : '' }}">For Review</a>
                        <a href="#" class="nav-subitem">Mailbox</a>
                        <a href="{{ route('bluinq.trash') }}" class="nav-subitem {{ $active === 'bluinq.trash' ? 'active' : '' }}">Archive</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="nav-dropdown {{ $cspOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="{{ $cspOpen ? 'true' : 'false' }}" aria-controls="nav-sub-csp">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    CSP
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-csp" role="region" aria-label="CSP submenu">
                <div class="nav-submenu-inner">
                    @if($isStaff)
                        <a href="{{ route('csp.list') }}" class="nav-subitem {{ $active === 'csp.list' ? 'active' : '' }}">List</a>
                        <a href="{{ route('csp.completed') }}" class="nav-subitem {{ $active === 'csp.completed' ? 'active' : '' }}">Completed</a>
                    @else
                        <a href="{{ route('csp.add') }}" class="nav-subitem {{ $active === 'csp.add' ? 'active' : '' }}">Add New</a>
                        <a href="{{ route('csp.list') }}" class="nav-subitem {{ $active === 'csp.list' ? 'active' : '' }}">List</a>
                        <a href="{{ route('csp.completed') }}" class="nav-subitem {{ $active === 'csp.completed' ? 'active' : '' }}">Completed</a>
                        <a href="{{ route('csp.review') }}" class="nav-subitem {{ $active === 'csp.review' ? 'active' : '' }}">For Review</a>
                        <a href="#" class="nav-subitem">Mailbox</a>
                        <a href="{{ route('csp.trash') }}" class="nav-subitem {{ $active === 'csp.trash' ? 'active' : '' }}">Archive</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="nav-dropdown {{ $nhOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="{{ $nhOpen ? 'true' : 'false' }}" aria-controls="nav-sub-nh">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    NH
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-nh" role="region" aria-label="NH submenu">
                <div class="nav-submenu-inner">
                    @if($isStaff)
                        <a href="{{ route('nh.list') }}" class="nav-subitem {{ $active === 'nh.list' ? 'active' : '' }}">List</a>
                        <a href="{{ route('nh.completed') }}" class="nav-subitem {{ $active === 'nh.completed' ? 'active' : '' }}">Completed</a>
                    @else
                        <a href="{{ route('nh.add') }}" class="nav-subitem {{ $active === 'nh.add' ? 'active' : '' }}">Add New</a>
                        <a href="{{ route('nh.list') }}" class="nav-subitem {{ $active === 'nh.list' ? 'active' : '' }}">List</a>
                        <a href="{{ route('nh.completed') }}" class="nav-subitem {{ $active === 'nh.completed' ? 'active' : '' }}">Completed</a>
                        <a href="{{ route('nh.review') }}" class="nav-subitem {{ $active === 'nh.review' ? 'active' : '' }}">For Review</a>
                        <a href="#" class="nav-subitem">Mailbox</a>
                        <a href="{{ route('nh.trash') }}" class="nav-subitem {{ $active === 'nh.trash' ? 'active' : '' }}">Archive</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="nav-dropdown {{ $lcHomeBuilderOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="{{ $lcHomeBuilderOpen ? 'true' : 'false' }}" aria-controls="nav-sub-lc-home-builder">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    LC HOME BUILDER
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-lc-home-builder" role="region" aria-label="LC HOME BUILDER submenu">
                <div class="nav-submenu-inner">
                    @if($isStaff)
                        <a href="{{ route('lc_home_builder.list') }}" class="nav-subitem {{ $active === 'lc_home_builder.list' ? 'active' : '' }}">List</a>
                        <a href="{{ route('lc_home_builder.completed') }}" class="nav-subitem {{ $active === 'lc_home_builder.completed' ? 'active' : '' }}">Completed</a>
                    @else
                        <a href="{{ route('lc_home_builder.add') }}" class="nav-subitem {{ $active === 'lc_home_builder.add' ? 'active' : '' }}">Add New</a>
                        <a href="{{ route('lc_home_builder.list') }}" class="nav-subitem {{ $active === 'lc_home_builder.list' ? 'active' : '' }}">List</a>
                        <a href="{{ route('lc_home_builder.completed') }}" class="nav-subitem {{ $active === 'lc_home_builder.completed' ? 'active' : '' }}">Completed</a>
                        <a href="{{ route('lc_home_builder.review') }}" class="nav-subitem {{ $active === 'lc_home_builder.review' ? 'active' : '' }}">For Review</a>
                        <a href="#" class="nav-subitem">Mailbox</a>
                        <a href="{{ route('lc_home_builder.trash') }}" class="nav-subitem {{ $active === 'lc_home_builder.trash' ? 'active' : '' }}">Archive</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="nav-dropdown {{ $efficientLivingOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="{{ $efficientLivingOpen ? 'true' : 'false' }}" aria-controls="nav-sub-efficient-living">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    EFFICIENT LIVING
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-efficient-living" role="region" aria-label="EFFICIENT LIVING submenu">
                <div class="nav-submenu-inner">
                    @if($isStaff)
                        <a href="{{ route('efficient_living.list') }}" class="nav-subitem {{ $active === 'efficient_living.list' ? 'active' : '' }}">List</a>
                        <a href="{{ route('efficient_living.completed') }}" class="nav-subitem {{ $active === 'efficient_living.completed' ? 'active' : '' }}">Completed</a>
                    @else
                        <a href="{{ route('efficient_living.add') }}" class="nav-subitem {{ $active === 'efficient_living.add' ? 'active' : '' }}">Add New</a>
                        <a href="{{ route('efficient_living.list') }}" class="nav-subitem {{ $active === 'efficient_living.list' ? 'active' : '' }}">List</a>
                        <a href="{{ route('efficient_living.completed') }}" class="nav-subitem {{ $active === 'efficient_living.completed' ? 'active' : '' }}">Completed</a>
                        <a href="{{ route('efficient_living.review') }}" class="nav-subitem {{ $active === 'efficient_living.review' ? 'active' : '' }}">For Review</a>
                        <a href="#" class="nav-subitem">Mailbox</a>
                        <a href="{{ route('efficient_living.trash') }}" class="nav-subitem {{ $active === 'efficient_living.trash' ? 'active' : '' }}">Archive</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="nav-dropdown {{ $leadingEnergyOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="{{ $leadingEnergyOpen ? 'true' : 'false' }}" aria-controls="nav-sub-leading-energy">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    LEADING ENERGY
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-leading-energy" role="region" aria-label="LEADING ENERGY submenu">
                <div class="nav-submenu-inner">
                    @if($isStaff)
                        <a href="{{ route('leading_energy.list') }}" class="nav-subitem {{ $active === 'leading_energy.list' ? 'active' : '' }}">List</a>
                        <a href="{{ route('leading_energy.completed') }}" class="nav-subitem {{ $active === 'leading_energy.completed' ? 'active' : '' }}">Completed</a>
                    @else
                        <a href="{{ route('leading_energy.add') }}" class="nav-subitem {{ $active === 'leading_energy.add' ? 'active' : '' }}">Add New</a>
                        <a href="{{ route('leading_energy.list') }}" class="nav-subitem {{ $active === 'leading_energy.list' ? 'active' : '' }}">List</a>
                        <a href="{{ route('leading_energy.completed') }}" class="nav-subitem {{ $active === 'leading_energy.completed' ? 'active' : '' }}">Completed</a>
                        <a href="{{ route('leading_energy.review') }}" class="nav-subitem {{ $active === 'leading_energy.review' ? 'active' : '' }}">For Review</a>
                        <a href="#" class="nav-subitem">Mailbox</a>
                        <a href="{{ route('leading_energy.trash') }}" class="nav-subitem {{ $active === 'leading_energy.trash' ? 'active' : '' }}">Archive</a>
                    @endif
                </div>
            </div>
        </div>
        @unless($isStaff)
        <div class="nav-category">Reports</div>
        <a href="{{ route('reports') }}" class="nav-item {{ $active === 'reports' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2zM9 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H9a2 2 0 01-2-2V5z"/></svg>
            Reports
        </a>
        <div class="nav-category">Setting</div>
        <a href="{{ route('settings.email_config') }}" class="nav-item {{ ($active ?? '') === 'settings.email_config' ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 10.5V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h12.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 15.28c.2-.4.5-.8.9-1a2.1 2.1 0 0 1 2.6.4c.3.4.5.8.5 1.3 0 1.3-2 2-2 2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 22v.01"/></svg>
            Email Configuration
        </a>
        <div class="nav-dropdown {{ $jobOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="{{ $jobOpen ? 'true' : 'false' }}" aria-controls="nav-sub-job">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.44 11.05 12.25 20.24a6 6 0 1 1-8.49-8.49l9.19-9.19a4 4 0 1 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.82-2.82l8.49-8.48"/></svg>
                    Job
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-job" role="region" aria-label="Job submenu">
                <div class="nav-submenu-inner">
                    <a href="{{ route('compliance.index') }}" class="nav-subitem {{ in_array($active, ['compliance.index', 'compliance.create', 'compliance.edit']) ? 'active' : '' }}">Compliance</a>
                    <a href="{{ route('priority.index') }}" class="nav-subitem {{ in_array($active, ['priority.index', 'priority.create', 'priority.edit']) ? 'active' : '' }}">Priority</a>
                    <a href="{{ route('status.index') }}" class="nav-subitem {{ in_array($active, ['status.index', 'status.create', 'status.edit']) ? 'active' : '' }}">Status</a>
                    <a href="{{ route('job_request.index') }}" class="nav-subitem {{ in_array($active, ['job_request.index', 'job_request.create', 'job_request.edit']) ? 'active' : '' }}">Job Request</a>
                    <a href="{{ route('client.index') }}" class="nav-subitem {{ in_array($active, ['client.index', 'client.create', 'client.edit']) ? 'active' : '' }}">Client</a>
                </div>
            </div>
        </div>
        <div class="nav-dropdown {{ $branchOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="{{ $branchOpen ? 'true' : 'false' }}" aria-controls="nav-sub-branch">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2m-2 4h-4m-4 0H5m14 0v8a2 2 0 01-2 2H7a2 2 0 01-2-2v-8"/></svg>
                    Branch
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-branch" role="region" aria-label="Branch submenu">
                <div class="nav-submenu-inner">
                    <a href="{{ route('branch.index') }}" class="nav-subitem {{ in_array($active, ['branch.index', 'branch.create', 'branch.edit']) ? 'active' : '' }}">List</a>
                    <a href="{{ route('branch.archive') }}" class="nav-subitem {{ $active === 'branch.archive' ? 'active' : '' }}">Archive</a>
                </div>
            </div>
        </div>
        <div class="nav-dropdown {{ $accountsOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="{{ $accountsOpen ? 'true' : 'false' }}" aria-controls="nav-sub-accounts">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Accounts
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-accounts" role="region" aria-label="Accounts submenu">
                <div class="nav-submenu-inner">
                    <a href="{{ route('users.index') }}" class="nav-subitem {{ in_array($active, ['users.index', 'users.create', 'users.edit']) ? 'active' : '' }}">User Accounts</a>
                    <a href="{{ route('accounts.clients.index') }}" class="nav-subitem {{ in_array($active, ['accounts.clients.index', 'accounts.clients.create', 'accounts.clients.edit']) ? 'active' : '' }}">Client Accounts</a>
                    <a href="{{ route('users.archive') }}" class="nav-subitem {{ $active === 'users.archive' ? 'active' : '' }}">Archive</a>
                </div>
            </div>
        </div>
        <div class="nav-dropdown" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="false" aria-controls="nav-sub-announcement">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 6a13 13 0 0 0 8.4-2.8A1 1 0 0 1 21 4v12a1 1 0 0 1-1.6.8A13 13 0 0 0 11 14H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 14a12 12 0 0 0 2.4 7.2 2 2 0 0 0 3.2-2.4A8 8 0 0 1 10 14"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6v8"/></svg>
                    Announcement
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-announcement" role="region" aria-label="Announcement submenu">
                <div class="nav-submenu-inner">
                    <a href="#" class="nav-subitem">Announcement List</a>
                </div>
            </div>
        </div>
        <div class="nav-dropdown" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="false" aria-controls="nav-sub-bph-email">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    BPH Email
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-bph-email" role="region" aria-label="BPH Email submenu">
                <div class="nav-submenu-inner">
                    <a href="#" class="nav-subitem">Create Email</a>
                    <a href="#" class="nav-subitem">List</a>
                </div>
            </div>
        </div>
        <div class="nav-dropdown" data-dropdown>
            <button type="button" class="nav-dropdown-trigger" aria-expanded="false" aria-controls="nav-sub-job-notifications">
                <span class="nav-trigger-inner">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Settings
                </span>
                <svg class="nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="nav-submenu" id="nav-sub-job-notifications" role="region" aria-label="Job Notifications submenu">
                <div class="nav-submenu-inner">
                    <a href="#" class="nav-subitem">List</a>
                </div>
            </div>
        </div>
        @endunless
    </nav>
</aside>
