@php
    $active = $sidebar_active ?? 'dashboard';
    $userRole = session('user_role');
    $isStaff = strtolower((string) ($userRole ?? '')) === 'staff';
    $lbsOpen = in_array($active, ['lbs.add', 'lbs.list', 'lbs.completed', 'lbs.review', 'lbs.mailbox', 'lbs.trash']) || str_starts_with((string)$active, 'lbs.');
    $bphOpen = in_array($active, ['bph.add', 'bph.list', 'bph.completed', 'bph.review', 'bph.mailbox', 'bph.trash']) || str_starts_with((string)$active, 'bph.');
    $bluinqOpen = in_array($active, ['bluinq.add', 'bluinq.list', 'bluinq.completed', 'bluinq.review', 'bluinq.mailbox', 'bluinq.trash']) || str_starts_with((string)$active, 'bluinq.');
    $amtOpen = in_array($active, ['amt.add', 'amt.list', 'amt.completed', 'amt.review', 'amt.mailbox', 'amt.trash']) || str_starts_with((string)$active, 'amt.');
    $fyrsOpen = in_array($active, ['fyrs.add', 'fyrs.list', 'fyrs.completed', 'fyrs.review', 'fyrs.mailbox', 'fyrs.trash']) || str_starts_with((string)$active, 'fyrs.');
    $cspOpen = in_array($active, ['csp.add', 'csp.list', 'csp.completed', 'csp.review', 'csp.trash']) || str_starts_with((string)$active, 'csp.');
    $nhOpen = in_array($active, ['nh.add', 'nh.list', 'nh.completed', 'nh.review', 'nh.trash']) || str_starts_with((string)$active, 'nh.');
    $lcHomeBuilderOpen = in_array($active, ['lc_home_builder.add', 'lc_home_builder.list', 'lc_home_builder.completed', 'lc_home_builder.review', 'lc_home_builder.trash']) || str_starts_with((string)$active, 'lc_home_builder.');
    $efficientLivingOpen = in_array($active, ['efficient_living.add', 'efficient_living.list', 'efficient_living.completed', 'efficient_living.review', 'efficient_living.mailbox', 'efficient_living.trash']) || str_starts_with((string)$active, 'efficient_living.');
    $leadingEnergyOpen = in_array($active, ['leading_energy.add', 'leading_energy.list', 'leading_energy.completed', 'leading_energy.review', 'leading_energy.mailbox', 'leading_energy.trash']) || str_starts_with((string)$active, 'leading_energy.');
    $jobOpen = in_array($active, ['compliance.index', 'compliance.create', 'compliance.edit', 'priority.index', 'priority.create', 'priority.edit', 'status.index', 'status.create', 'status.edit', 'job_request.index', 'job_request.create', 'job_request.edit', 'client.index', 'client.create', 'client.edit']) || str_starts_with((string)$active, 'compliance.') || str_starts_with((string)$active, 'priority.') || str_starts_with((string)$active, 'status.') || str_starts_with((string)$active, 'job_request.') || str_starts_with((string)$active, 'client.');
    $branchOpen = in_array($active, ['branch.index', 'branch.create', 'branch.edit', 'branch.archive']) || str_starts_with((string)$active, 'branch.');
    $accountsOpen = in_array($active, ['users.index', 'users.create', 'users.edit', 'users.archive', 'accounts.clients.index', 'accounts.clients.create', 'accounts.clients.edit']) || str_starts_with((string)$active, 'users.') || str_starts_with((string)$active, 'accounts.clients.');
    $bphEmailOpen = in_array($active, ['bph_client_email.index', 'bph_client_email.create', 'bph_client_email.edit']) || str_starts_with((string)$active, 'bph_client_email.');
    $amtEmailOpen = in_array($active, ['amt_client_email.index', 'amt_client_email.create', 'amt_client_email.edit']) || str_starts_with((string)$active, 'amt_client_email.');
    $lbsListCount = $lbs_list_count ?? 0;
    $lbsReviewCount = $lbs_review_count ?? 0;
    $lbsMailboxCount = $lbs_mailbox_count ?? 0;
    $elListCount = $efficient_living_list_count ?? 0;
    $elReviewCount = $efficient_living_review_count ?? 0;
    $elMailboxCount = $efficient_living_mailbox_count ?? 0;
    $bphListCount = $bph_list_count ?? 0;
    $bphReviewCount = $bph_review_count ?? 0;
    $bphMailboxCount = $bph_mailbox_count ?? 0;
    $bluinqListCount = $bluinq_list_count ?? 0;
    $bluinqReviewCount = $bluinq_review_count ?? 0;
    $bluinqMailboxCount = $bluinq_mailbox_count ?? 0;
    $amtListCount = $amt_list_count ?? 0;
    $amtReviewCount = $amt_review_count ?? 0;
    $amtMailboxCount = $amt_mailbox_count ?? 0;
    $fyrsListCount = $fyrs_list_count ?? 0;
    $fyrsReviewCount = $fyrs_review_count ?? 0;
    $fyrsMailboxCount = $fyrs_mailbox_count ?? 0;
    $cspListCount = $csp_list_count ?? 0;
    $cspReviewCount = $csp_review_count ?? 0;
    $cspMailboxCount = $csp_mailbox_count ?? 0;
    $nhListCount = $nh_list_count ?? 0;
    $nhReviewCount = $nh_review_count ?? 0;
    $nhMailboxCount = $nh_mailbox_count ?? 0;
    $lcHomeBuilderListCount = $lc_home_builder_list_count ?? 0;
    $lcHomeBuilderReviewCount = $lc_home_builder_review_count ?? 0;
    $lcHomeBuilderMailboxCount = $lc_home_builder_mailbox_count ?? 0;
    $leadingEnergyListCount = $leading_energy_list_count ?? 0;
    $leadingEnergyReviewCount = $leading_energy_review_count ?? 0;
    $leadingEnergyMailboxCount = $leading_energy_mailbox_count ?? 0;

    $may = function (string $route) {
        return \App\Models\RolePermission::userMayAccessRoute($route);
    };
    $anyMay = function (array $routes) use ($may) {
        foreach ($routes as $r) {
            if ($may($r)) {
                return true;
            }
        }
        return false;
    };
    /** Must match Permission settings → job_ui_modules → sidebar (so checked items actually reveal this nav). */
    $jobSidebarRoutes = static function (string $jobKey): array {
        $sidebar = config('permissions.job_ui_modules.'.$jobKey.'.sidebar', []);
        if (! is_array($sidebar)) {
            return [];
        }

        return array_values(array_filter($sidebar, static fn ($r) => is_string($r) && $r !== ''));
    };
    $showJobNavFromConfig = static function (string $jobKey, array $also = []) use ($anyMay, $jobSidebarRoutes): bool {
        return $anyMay(array_values(array_unique([...$jobSidebarRoutes($jobKey), ...$also])));
    };
    $showLbsNav = $showJobNavFromConfig('lbs');
    $showBphNav = $showJobNavFromConfig('bph');
    $showEfficientLivingNav = $showJobNavFromConfig('efficient_living');
    $showBluinqNav = $showJobNavFromConfig('bluinq');
    $roleNormForNav = strtolower(trim((string) ($userRole ?? '')));
    $showAmtNav = $showJobNavFromConfig('amt', [
        'amt.job.sendSlack', 'amt.job.sendSubmissionEmail', 'amt.job.emailPreview', 'amt.job.sendMailboxEmail',
    ]) || $roleNormForNav === 'admin';
    $showFyrsNav = $showJobNavFromConfig('fyrs', [
        'fyrs.job.sendSlack', 'fyrs.job.sendSubmissionEmail', 'fyrs.job.emailPreview', 'fyrs.job.sendMailboxEmail',
        'fyrs.job.uploadFiles', 'fyrs.job.deleteFile', 'fyrs.job.file', 'fyrs.job.mergeFile', 'fyrs.job.printCompliance',
        'fyrs.job.checkerUploads', 'fyrs.job.runComment', 'fyrs.job.comment', 'fyrs.job.archive',
    ]) || $roleNormForNav === 'admin';
    $showCspNav = $showJobNavFromConfig('csp', [
        'csp.job.emailPreview', 'csp.job.sendMailboxEmail', 'csp.job.printCompliance',
    ]);
    $showNhNav = $showJobNavFromConfig('nh');
    $showLcHomeBuilderNav = $showJobNavFromConfig('lc_home_builder', [
        'lc_home_builder.job.sendSlack', 'lc_home_builder.job.sendSubmissionEmail', 'lc_home_builder.job.emailPreview', 'lc_home_builder.job.sendMailboxEmail',
    ]);
    $showLeadingEnergyNav = $showJobNavFromConfig('leading_energy', [
        'leading_energy.job.sendSlack', 'leading_energy.job.sendSubmissionEmail', 'leading_energy.job.emailPreview', 'leading_energy.job.sendMailboxEmail',
    ]);
    $showJobManagement = $showLbsNav || $showBphNav || $showEfficientLivingNav || $showBluinqNav || $showAmtNav || $showFyrsNav || $showCspNav || $showNhNav || $showLcHomeBuilderNav || $showLeadingEnergyNav;
    $showJobMasterNav = $anyMay(['compliance.index', 'priority.index', 'status.index', 'job_request.index', 'client.index']);
    $showBranchNav = $anyMay(['branch.index', 'branch.archive']);
    $showAccountsNav = $anyMay(['users.index', 'accounts.clients.index', 'users.archive']);
    $showBphEmailNav = $showJobNavFromConfig('bph_email');
    $showAmtEmailNav = $showJobNavFromConfig('amt_email');
    $announcementRoutes = \App\Models\RolePermission::allowedRoutesForRole((string) ($userRole ?? ''), session('user_branch'));
    $showAnnouncementNav = in_array('announcement.index', $announcementRoutes, true);
    $showReportsNav = $may('reports');
    $showSettingsColumn = $may('settings.email_config')
        || $may('settings.slack_config')
        || $may('settings.notifications')
        || $may('settings.permission')
        || $showJobMasterNav
        || $showBranchNav
        || $showAccountsNav
        || $showBphEmailNav
        || $showAmtEmailNav;
    $showNotificationControls = $may('settings.notifications');
    $settingsOpen = in_array($active, ['settings.email_config', 'settings.slack_config', 'settings.notifications']);
@endphp
<aside id="sidebarNav" role="navigation" aria-label="Main navigation" class="sidebar fixed left-0 top-0 z-50 flex h-screen w-60 flex-col overflow-hidden border-r border-slate-200 bg-white py-0 shadow-sm transition-[transform,box-shadow] duration-250 ease-out dark:border-slate-700/50 dark:bg-slate-900 dark:shadow-slate-950/30 -translate-x-full lg:translate-x-0">
    <div class="sidebar-brand flex h-14 w-full flex-shrink-0 items-center justify-center border-b border-slate-200 bg-slate-50/50 px-4 dark:border-slate-700 dark:bg-slate-800/50">
        <a href="{{ route('dashboard') }}" class="flex w-full items-center justify-center no-underline">
            <img src="{{ asset('storage/logo-light.png') }}" alt="Luntian" class="sidebar-logo-img mx-auto block h-8 w-auto max-w-[130px] object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
            <span class="text-center font-bold text-xl tracking-tight text-emerald-600 dark:text-emerald-300" style="display:none;">Luntian</span>
        </a>
    </div>
    @php
        $userName = session('user_name', 'User');
        $userRole = session('user_role', '');
        $roleLabel = $userRole ? ucfirst(strtolower((string) $userRole)) : 'User';
    @endphp
    {{-- User profile: centered row, image + Welcome/username, padding sa x --}}
    <a href="{{ route('account.settings.edit') }}" class="sidebar-profile flex flex-shrink-0 no-underline focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500/50 rounded-none">
        <div class="sidebar-profile-card relative flex w-full items-center justify-center gap-3 overflow-hidden border-0 px-5 py-4 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/80">
            <span class="sidebar-profile-bg" aria-hidden="true"></span>
            <span class="relative z-10 flex h-10 w-10 flex-shrink-0 items-center justify-center">
            @if(session('user_profile_image'))
                <img src="{{ route('account.settings.image') }}" alt="" class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-200/80 dark:ring-slate-600">
            @else
                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500/25 text-sm font-semibold text-emerald-600 ring-2 ring-emerald-400/30 dark:bg-emerald-500/30 dark:text-emerald-400 dark:ring-emerald-500/30">{{ strtoupper(substr($userName, 0, 1)) }}</span>
            @endif
            </span>
            <div class="relative z-10 flex min-w-0 flex-shrink flex-col items-start justify-center">
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Welcome</p>
                <p class="mt-0.5 truncate text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $userName }}</p>
            </div>
        </div>
    </a>
    <nav class="flex-1 min-h-0 space-y-0.5 overflow-y-auto overflow-x-hidden border-t border-slate-200 px-2 py-4 dark:border-slate-700 scrollbar-hide">
        @if($may('dashboard'))
        <a href="{{ route('dashboard') }}" class="nav-item flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/30 focus:ring-inset dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'dashboard' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 dark:pl-[15px] pl-[15px]' : '' }}">
            <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            Dashboard
        </a>
        @endif
        @if($showJobManagement)
        <div class="mt-4 mb-1 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Job Management</div>
        @endif
        @if($showLbsNav)
        <div class="group nav-dropdown {{ $lbsOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $lbsOpen ? 'true' : 'false' }}" aria-controls="nav-sub-lbs">
                <span class="flex items-center gap-2.5">
                    <svg class="h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    LBS
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-lbs" role="region" aria-label="LBS submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($isStaff)
                        @if($may('lbs.list'))
                        <a href="{{ route('lbs.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lbs.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 dark:pl-9 pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white" data-lbs-sidebar="allocated">{{ $lbsListCount }}</span>
                        </a>
                        @endif
                        @if($may('lbs.completed'))
                        <a href="{{ route('lbs.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lbs.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('lbs.review'))
                        <a href="{{ route('lbs.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lbs.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white" data-lbs-sidebar="for-review">{{ $lbsReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('lbs.mailbox'))
                        <a href="{{ route('lbs.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lbs.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $lbsMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('lbs.trash'))
                        <a href="{{ route('lbs.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lbs.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @else
                        @if($may('lbs.add'))
                        <a href="{{ route('lbs.add') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lbs.add' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Add New</a>
                        @endif
                        @if($may('lbs.list'))
                        <a href="{{ route('lbs.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lbs.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white" data-lbs-sidebar="allocated">{{ $lbsListCount }}</span>
                        </a>
                        @endif
                        @if($may('lbs.completed'))
                        <a href="{{ route('lbs.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lbs.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('lbs.review'))
                        <a href="{{ route('lbs.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lbs.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white" data-lbs-sidebar="for-review">{{ $lbsReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('lbs.mailbox'))
                        <a href="{{ route('lbs.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lbs.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $lbsMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('lbs.trash'))
                        <a href="{{ route('lbs.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lbs.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showBphNav)
        <div class="group nav-dropdown {{ $bphOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $bphOpen ? 'true' : 'false' }}" aria-controls="nav-sub-bph">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    BPH
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-bph" role="region" aria-label="BPH submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($isStaff)
                        @if($may('bph.list'))
                        <a href="{{ route('bph.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bph.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $bphListCount }}</span>
                        </a>
                        @endif
                        @if($may('bph.completed'))
                        <a href="{{ route('bph.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bph.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('bph.review'))
                        <a href="{{ route('bph.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bph.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $bphReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('bph.mailbox'))
                        <a href="{{ route('bph.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bph.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $bphMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('bph.trash'))
                        <a href="{{ route('bph.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bph.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @else
                        @if($may('bph.add'))
                        <a href="{{ route('bph.add') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bph.add' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Add New</a>
                        @endif
                        @if($may('bph.list'))
                        <a href="{{ route('bph.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bph.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $bphListCount }}</span>
                        </a>
                        @endif
                        @if($may('bph.completed'))
                        <a href="{{ route('bph.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bph.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('bph.review'))
                        <a href="{{ route('bph.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bph.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $bphReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('bph.mailbox'))
                        <a href="{{ route('bph.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bph.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $bphMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('bph.trash'))
                        <a href="{{ route('bph.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bph.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showEfficientLivingNav)
        <div class="group nav-dropdown {{ $efficientLivingOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $efficientLivingOpen ? 'true' : 'false' }}" aria-controls="nav-sub-efficient-living">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    EFFICIENT LIVING
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-efficient-living" role="region" aria-label="EFFICIENT LIVING submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($isStaff)
                        @if($may('efficient_living.list'))
                        <a href="{{ route('efficient_living.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'efficient_living.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white" data-el-sidebar="allocated">{{ $elListCount }}</span>
                        </a>
                        @endif
                        @if($may('efficient_living.completed'))
                        <a href="{{ route('efficient_living.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'efficient_living.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('efficient_living.review'))
                        <a href="{{ route('efficient_living.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'efficient_living.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white" data-el-sidebar="for-review">{{ $elReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('efficient_living.mailbox'))
                        <a href="{{ route('efficient_living.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'efficient_living.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white" data-el-sidebar="mailbox">{{ $elMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('efficient_living.trash'))
                        <a href="{{ route('efficient_living.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'efficient_living.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @else
                        @if($may('efficient_living.add'))
                        <a href="{{ route('efficient_living.add') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'efficient_living.add' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Add New</a>
                        @endif
                        @if($may('efficient_living.list'))
                        <a href="{{ route('efficient_living.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'efficient_living.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white" data-el-sidebar="allocated">{{ $elListCount }}</span>
                        </a>
                        @endif
                        @if($may('efficient_living.completed'))
                        <a href="{{ route('efficient_living.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'efficient_living.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('efficient_living.review'))
                        <a href="{{ route('efficient_living.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'efficient_living.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white" data-el-sidebar="for-review">{{ $elReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('efficient_living.mailbox'))
                        <a href="{{ route('efficient_living.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'efficient_living.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white" data-el-sidebar="mailbox">{{ $elMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('efficient_living.trash'))
                        <a href="{{ route('efficient_living.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'efficient_living.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showBluinqNav)
        <div class="group nav-dropdown {{ $bluinqOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $bluinqOpen ? 'true' : 'false' }}" aria-controls="nav-sub-bluinq">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    BLUINQ
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-bluinq" role="region" aria-label="BLUINQ submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($isStaff)
                        @if($may('bluinq.list'))
                        <a href="{{ route('bluinq.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bluinq.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $bluinqListCount }}</span>
                        </a>
                        @endif
                        @if($may('bluinq.completed'))
                        <a href="{{ route('bluinq.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bluinq.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('bluinq.review'))
                        <a href="{{ route('bluinq.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bluinq.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $bluinqReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('bluinq.mailbox'))
                        <a href="{{ route('bluinq.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bluinq.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $bluinqMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('bluinq.trash'))
                        <a href="{{ route('bluinq.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bluinq.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @else
                        @if($may('bluinq.add'))
                        <a href="{{ route('bluinq.add') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bluinq.add' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Add New</a>
                        @endif
                        @if($may('bluinq.list'))
                        <a href="{{ route('bluinq.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bluinq.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $bluinqListCount }}</span>
                        </a>
                        @endif
                        @if($may('bluinq.completed'))
                        <a href="{{ route('bluinq.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bluinq.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('bluinq.review'))
                        <a href="{{ route('bluinq.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bluinq.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $bluinqReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('bluinq.mailbox'))
                        <a href="{{ route('bluinq.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bluinq.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $bluinqMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('bluinq.trash'))
                        <a href="{{ route('bluinq.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bluinq.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showAmtNav)
        <div class="group nav-dropdown {{ $amtOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $amtOpen ? 'true' : 'false' }}" aria-controls="nav-sub-amt">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    A&amp;M
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-amt" role="region" aria-label="A&amp;M submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($isStaff)
                        @if($may('amt.rfq'))
                        <a href="{{ route('amt.rfq') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.rfq' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">RFQ</a>
                        @endif
                        @if($may('amt.purchase'))
                        <a href="{{ route('amt.purchase') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.purchase' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Purchase</a>
                        @endif
                        @if($may('amt.list'))
                        <a href="{{ route('amt.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $amtListCount }}</span>
                        </a>
                        @endif
                        @if($may('amt.completed'))
                        <a href="{{ route('amt.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('amt.review'))
                        <a href="{{ route('amt.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $amtReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('amt.mailbox'))
                        <a href="{{ route('amt.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $amtMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('amt.trash'))
                        <a href="{{ route('amt.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @else
                        @if($may('amt.add'))
                        <a href="{{ route('amt.add') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ in_array($active, ['amt.add', 'amt.rfq']) ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">RFQ</a>
                        @endif
                        @if($may('amt.purchase'))
                        <a href="{{ route('amt.purchase') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.purchase' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Purchase</a>
                        @endif
                        @if($may('amt.list'))
                        <a href="{{ route('amt.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $amtListCount }}</span>
                        </a>
                        @endif
                        @if($may('amt.completed'))
                        <a href="{{ route('amt.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('amt.review'))
                        <a href="{{ route('amt.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $amtReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('amt.mailbox'))
                        <a href="{{ route('amt.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $amtMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('amt.trash'))
                        <a href="{{ route('amt.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showFyrsNav)
        <div class="group nav-dropdown {{ $fyrsOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $fyrsOpen ? 'true' : 'false' }}" aria-controls="nav-sub-fyrs">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Fyrs Energy Wise
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-96" id="nav-sub-fyrs" role="region" aria-label="Fyrs Energy Wise submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($isStaff)
                        @if($may('fyrs.list'))
                        <a href="{{ route('fyrs.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'fyrs.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $fyrsListCount }}</span>
                        </a>
                        @endif
                        @if($may('fyrs.completed'))
                        <a href="{{ route('fyrs.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'fyrs.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('fyrs.review'))
                        <a href="{{ route('fyrs.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'fyrs.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $fyrsReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('fyrs.mailbox'))
                        <a href="{{ route('fyrs.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'fyrs.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $fyrsMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('fyrs.trash'))
                        <a href="{{ route('fyrs.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'fyrs.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @else
                        @if($may('fyrs.add'))
                        <a href="{{ route('fyrs.add') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'fyrs.add' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Add New</a>
                        @endif
                        @if($may('fyrs.list'))
                        <a href="{{ route('fyrs.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'fyrs.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $fyrsListCount }}</span>
                        </a>
                        @endif
                        @if($may('fyrs.completed'))
                        <a href="{{ route('fyrs.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'fyrs.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('fyrs.review'))
                        <a href="{{ route('fyrs.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'fyrs.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $fyrsReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('fyrs.mailbox'))
                        <a href="{{ route('fyrs.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'fyrs.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $fyrsMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('fyrs.trash'))
                        <a href="{{ route('fyrs.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'fyrs.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showCspNav)
        <div class="group nav-dropdown {{ $cspOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $cspOpen ? 'true' : 'false' }}" aria-controls="nav-sub-csp">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    CSP
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-csp" role="region" aria-label="CSP submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($isStaff)
                        @if($may('csp.list'))
                        <a href="{{ route('csp.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'csp.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $cspListCount }}</span>
                        </a>
                        @endif
                        @if($may('csp.completed'))
                        <a href="{{ route('csp.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'csp.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('csp.review'))
                        <a href="{{ route('csp.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'csp.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $cspReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('csp.mailbox'))
                        <a href="{{ route('csp.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'csp.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $cspMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('csp.trash'))
                        <a href="{{ route('csp.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'csp.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @else
                        @if($may('csp.add'))
                        <a href="{{ route('csp.add') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'csp.add' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Add New</a>
                        @endif
                        @if($may('csp.list'))
                        <a href="{{ route('csp.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'csp.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $cspListCount }}</span>
                        </a>
                        @endif
                        @if($may('csp.completed'))
                        <a href="{{ route('csp.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'csp.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('csp.review'))
                        <a href="{{ route('csp.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'csp.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $cspReviewCount }}</span>
                        </a>
                        @endif
                       @if($may('csp.mailbox'))
                        <a href="{{ route('csp.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'csp.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $cspMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('csp.trash'))
                        <a href="{{ route('csp.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'csp.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showNhNav)
        <div class="group nav-dropdown {{ $nhOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $nhOpen ? 'true' : 'false' }}" aria-controls="nav-sub-nh">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    NH
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-nh" role="region" aria-label="NH submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($isStaff)
                        @if($may('nh.list'))
                        <a href="{{ route('nh.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'nh.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $nhListCount }}</span>
                        </a>
                        @endif
                        @if($may('nh.completed'))
                        <a href="{{ route('nh.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'nh.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('nh.review'))
                        <a href="{{ route('nh.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'nh.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $nhReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('nh.mailbox'))
                        <a href="{{ route('nh.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'nh.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $nhMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('nh.trash'))
                        <a href="{{ route('nh.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'nh.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @else
                        @if($may('nh.add'))
                        <a href="{{ route('nh.add') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'nh.add' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Add New</a>
                        @endif
                        @if($may('nh.list'))
                        <a href="{{ route('nh.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'nh.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $nhListCount }}</span>
                        </a>
                        @endif
                        @if($may('nh.completed'))
                        <a href="{{ route('nh.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'nh.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('nh.review'))
                        <a href="{{ route('nh.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'nh.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $nhReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('nh.mailbox'))
                        <a href="{{ route('nh.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'nh.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $nhMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('nh.trash'))
                        <a href="{{ route('nh.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'nh.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showLcHomeBuilderNav)
        <div class="group nav-dropdown {{ $lcHomeBuilderOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $lcHomeBuilderOpen ? 'true' : 'false' }}" aria-controls="nav-sub-lc-home-builder">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    LC HOME BUILDER
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-lc-home-builder" role="region" aria-label="LC HOME BUILDER submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($isStaff)
                        @if($may('lc_home_builder.list'))
                        <a href="{{ route('lc_home_builder.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lc_home_builder.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $lcHomeBuilderListCount }}</span>
                        </a>
                        @endif
                        @if($may('lc_home_builder.completed'))
                        <a href="{{ route('lc_home_builder.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lc_home_builder.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('lc_home_builder.review'))
                        <a href="{{ route('lc_home_builder.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lc_home_builder.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $lcHomeBuilderReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('lc_home_builder.mailbox'))
                        <a href="{{ route('lc_home_builder.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lc_home_builder.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $lcHomeBuilderMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('lc_home_builder.trash'))
                        <a href="{{ route('lc_home_builder.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lc_home_builder.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @else
                        @if($may('lc_home_builder.add'))
                        <a href="{{ route('lc_home_builder.add') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lc_home_builder.add' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Add New</a>
                        @endif
                        @if($may('lc_home_builder.list'))
                        <a href="{{ route('lc_home_builder.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lc_home_builder.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $lcHomeBuilderListCount }}</span>
                        </a>
                        @endif
                        @if($may('lc_home_builder.completed'))
                        <a href="{{ route('lc_home_builder.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lc_home_builder.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('lc_home_builder.review'))
                        <a href="{{ route('lc_home_builder.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lc_home_builder.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">For Review</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $lcHomeBuilderReviewCount }}</span>
                        </a>
                        @endif
                        @if($may('lc_home_builder.mailbox'))
                        <a href="{{ route('lc_home_builder.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lc_home_builder.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">Mailbox</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $lcHomeBuilderMailboxCount }}</span>
                        </a>
                        @endif
                        @if($may('lc_home_builder.trash'))
                        <a href="{{ route('lc_home_builder.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'lc_home_builder.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showLeadingEnergyNav)
        <div class="group nav-dropdown {{ $leadingEnergyOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $leadingEnergyOpen ? 'true' : 'false' }}" aria-controls="nav-sub-leading-energy">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    LEADING ENERGY
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-leading-energy" role="region" aria-label="LEADING ENERGY submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($isStaff)
                        @if($may('leading_energy.list'))
                        <a href="{{ route('leading_energy.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'leading_energy.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}"><span class="nav-subitem-label">List</span><span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $leadingEnergyListCount }}</span></a>
                        @endif
                        @if($may('leading_energy.completed'))
                        <a href="{{ route('leading_energy.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'leading_energy.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('leading_energy.review'))
                        <a href="{{ route('leading_energy.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'leading_energy.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}"><span class="nav-subitem-label">For Review</span><span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $leadingEnergyReviewCount }}</span></a>
                        @endif
                        @if($may('leading_energy.mailbox'))
                        <a href="{{ route('leading_energy.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'leading_energy.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}"><span class="nav-subitem-label">Mailbox</span><span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $leadingEnergyMailboxCount }}</span></a>
                        @endif
                        @if($may('leading_energy.trash'))
                        <a href="{{ route('leading_energy.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'leading_energy.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @else
                        @if($may('leading_energy.add'))
                        <a href="{{ route('leading_energy.add') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'leading_energy.add' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Add New</a>
                        @endif
                        @if($may('leading_energy.list'))
                        <a href="{{ route('leading_energy.list') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'leading_energy.list' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                            <span class="nav-subitem-label">List</span>
                            <span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $leadingEnergyListCount }}</span>
                        </a>
                        @endif
                        @if($may('leading_energy.completed'))
                        <a href="{{ route('leading_energy.completed') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'leading_energy.completed' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Completed</a>
                        @endif
                        @if($may('leading_energy.review'))
                        <a href="{{ route('leading_energy.review') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'leading_energy.review' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}"><span class="nav-subitem-label">For Review</span><span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $leadingEnergyReviewCount }}</span></a>
                        @endif
                        @if($may('leading_energy.mailbox'))
                        <a href="{{ route('leading_energy.mailbox') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'leading_energy.mailbox' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}"><span class="nav-subitem-label">Mailbox</span><span class="nav-badge inline-flex h-5 min-w-5 flex-shrink-0 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-semibold text-white">{{ $leadingEnergyMailboxCount }}</span></a>
                        @endif
                        @if($may('leading_energy.trash'))
                        <a href="{{ route('leading_energy.trash') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'leading_energy.trash' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @endif
        @unless($isStaff)
        @if($showReportsNav)
        <div class="mt-4 mb-1 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Reports</div>
        <a href="{{ route('reports') }}" class="nav-item flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'reports' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-[15px] dark:pl-[15px]' : '' }}">
            <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2zM9 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H9a2 2 0 01-2-2V5z"/></svg>
            Reports
        </a>
        @endif
        @if($showSettingsColumn)
        <div class="mt-4 mb-1 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Setting</div>
        @endif
        @if($may('settings.email_config'))
        <a href="{{ route('settings.email_config') }}" class="nav-item flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ ($active ?? '') === 'settings.email_config' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-[15px] dark:pl-[15px]' : '' }}">
            <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 10.5V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h12.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 15.28c.2-.4.5-.8.9-1a2.1 2.1 0 0 1 2.6.4c.3.4.5.8.5 1.3 0 1.3-2 2-2 2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 22v.01"/></svg>
            Email Configuration
        </a>
        @endif
        @if($may('settings.slack_config'))
        <a href="{{ route('settings.slack_config') }}" class="nav-item flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ ($active ?? '') === 'settings.slack_config' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-[15px] dark:pl-[15px]' : '' }}">
            <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
            Slack Configuration
        </a>
        @endif
        @if($may('settings.permission'))
        <a href="{{ route('settings.permission') }}" class="nav-item flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ ($active ?? '') === 'settings.permission' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-[15px] dark:pl-[15px]' : '' }}">
            <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Permission
        </a>
        @endif
        @if($showJobMasterNav)
        <div class="group nav-dropdown {{ $jobOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $jobOpen ? 'true' : 'false' }}" aria-controls="nav-sub-job">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.44 11.05 12.25 20.24a6 6 0 1 1-8.49-8.49l9.19-9.19a4 4 0 1 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.82-2.82l8.49-8.48"/></svg>
                    Job
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-job" role="region" aria-label="Job submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($may('compliance.index'))
                    <a href="{{ route('compliance.index') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ in_array($active, ['compliance.index', 'compliance.create', 'compliance.edit']) ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Compliance</a>
                    @endif
                    @if($may('priority.index'))
                    <a href="{{ route('priority.index') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ in_array($active, ['priority.index', 'priority.create', 'priority.edit']) ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Priority</a>
                    @endif
                    @if($may('status.index'))
                    <a href="{{ route('status.index') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ in_array($active, ['status.index', 'status.create', 'status.edit']) ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Status</a>
                    @endif
                    @if($may('job_request.index'))
                    <a href="{{ route('job_request.index') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ in_array($active, ['job_request.index', 'job_request.create', 'job_request.edit']) ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Job Request</a>
                    @endif
                    @if($may('client.index'))
                    <a href="{{ route('client.index') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ in_array($active, ['client.index', 'client.create', 'client.edit']) ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Client</a>
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showBranchNav)
        <div class="group nav-dropdown {{ $branchOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $branchOpen ? 'true' : 'false' }}" aria-controls="nav-sub-branch">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2m-2 4h-4m-4 0H5m14 0v8a2 2 0 01-2 2H7a2 2 0 01-2-2v-8"/></svg>
                    Branch
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-branch" role="region" aria-label="Branch submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($may('branch.index'))
                    <a href="{{ route('branch.index') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ in_array($active, ['branch.index', 'branch.create', 'branch.edit']) ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">List</a>
                    @endif
                    @if($may('branch.archive'))
                    <a href="{{ route('branch.archive') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'branch.archive' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showAccountsNav)
        <div class="group nav-dropdown {{ $accountsOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $accountsOpen ? 'true' : 'false' }}" aria-controls="nav-sub-accounts">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Accounts
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-accounts" role="region" aria-label="Accounts submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($may('users.index'))
                    <a href="{{ route('users.index') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ in_array($active, ['users.index', 'users.create', 'users.edit']) ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">User Accounts</a>
                    @endif
                    @if($may('accounts.clients.index'))
                    <a href="{{ route('accounts.clients.index') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ in_array($active, ['accounts.clients.index', 'accounts.clients.create', 'accounts.clients.edit']) ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Client Accounts</a>
                    @endif
                    @if($may('users.archive'))
                    <a href="{{ route('users.archive') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'users.archive' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Archive</a>
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showAnnouncementNav)
        <div class="group nav-dropdown {{ str_starts_with((string) $active, 'announcement.') ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ str_starts_with((string) $active, 'announcement.') ? 'true' : 'false' }}" aria-controls="nav-sub-announcement">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 6a13 13 0 0 0 8.4-2.8A1 1 0 0 1 21 4v12a1 1 0 0 1-1.6.8A13 13 0 0 0 11 14H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 14a12 12 0 0 0 2.4 7.2 2 2 0 0 0 3.2-2.4A8 8 0 0 1 10 14"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6v8"/></svg>
                    Announcement
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-announcement" role="region" aria-label="Announcement submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    <a href="{{ route('announcement.index') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ str_starts_with((string) $active, 'announcement.') ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Announcement List</a>
                </div>
            </div>
        </div>
        @endif
        @if($showBphEmailNav)
        <div class="group nav-dropdown {{ $bphEmailOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $bphEmailOpen ? 'true' : 'false' }}" aria-controls="nav-sub-bph-email">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    BPH Email
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-bph-email" role="region" aria-label="BPH Email submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($may('bph_client_email.create'))
                    <a href="{{ route('bph_client_email.create') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'bph_client_email.create' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Create Email</a>
                    @endif
                    @if($may('bph_client_email.index'))
                    <a href="{{ route('bph_client_email.index') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ in_array($active, ['bph_client_email.index', 'bph_client_email.edit']) ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">List</a>
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showAmtEmailNav)
        <div class="group nav-dropdown {{ $amtEmailOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $amtEmailOpen ? 'true' : 'false' }}" aria-controls="nav-sub-amt-email">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    A&amp;M Email
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-amt-email" role="region" aria-label="A&amp;M Email submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($may('amt_client_email.create'))
                    <a href="{{ route('amt_client_email.create') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'amt_client_email.create' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">Create Email</a>
                    @endif
                    @if($may('amt_client_email.index'))
                    <a href="{{ route('amt_client_email.index') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ in_array($active, ['amt_client_email.index', 'amt_client_email.edit']) ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">List</a>
                    @endif
                </div>
            </div>
        </div>
        @endif
        @if($showNotificationControls)
        <div class="group nav-dropdown {{ $settingsOpen ? 'open' : '' }}" data-dropdown>
            <button type="button" class="nav-dropdown-trigger flex w-full cursor-pointer items-center justify-between gap-3 rounded-lg border-0 bg-transparent px-4 py-3 text-left text-sm text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-expanded="{{ $settingsOpen ? 'true' : 'false' }}" aria-controls="nav-sub-job-notifications">
                <span class="nav-trigger-inner flex items-center gap-2.5">
                    <svg class="nav-icon h-5 w-5 flex-shrink-0 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Settings
                </span>
                <svg class="h-4 w-4 flex-shrink-0 opacity-60 transition-transform duration-200 group-[.open]:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div class="max-h-0 overflow-hidden transition-[max-height] duration-300 ease-out group-[.open]:max-h-80" id="nav-sub-job-notifications" role="region" aria-label="Job Notifications submenu">
                <div class="space-y-0.5 py-1 pb-2 pl-1">
                    @if($may('settings.notifications'))
                    <a href="{{ route('settings.notifications') }}" class="nav-subitem relative z-10 flex cursor-pointer items-center justify-between gap-2 rounded-lg px-3 py-2 pl-10 text-sm text-slate-600 no-underline transition-colors hover:bg-slate-100 hover:text-slate-800 dark:text-slate-400 dark:hover:bg-slate-800 dark:hover:text-slate-200 {{ $active === 'settings.notifications' ? 'nav-item-active border-l-4 border-emerald-500 bg-emerald-500/10 font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 pl-9 dark:pl-9' : '' }}">
                        Notification Controls
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endif
        @endunless
    </nav>
</aside>
