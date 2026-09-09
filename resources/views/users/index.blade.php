@extends('layouts.dashboard')

@section('title', 'User Accounts')

@section('body_class', 'page-users-index')

@push('styles')
<style>
    .page-users-index .dataTables_wrapper .dataTables_length,
    .page-users-index .dataTables_wrapper .dataTables_filter,
    .page-users-index .dataTables_wrapper .dataTables_info,
    .page-users-index .dataTables_wrapper .dataTables_paginate {
        color: rgb(71 85 105);
        font-size: 0.875rem;
    }
    .dark .page-users-index .dataTables_wrapper .dataTables_length,
    .dark .page-users-index .dataTables_wrapper .dataTables_filter,
    .dark .page-users-index .dataTables_wrapper .dataTables_info,
    .dark .page-users-index .dataTables_wrapper .dataTables_paginate {
        color: rgb(148 163 184);
    }
    .page-users-index .dataTables_wrapper .dataTables_length select,
    .page-users-index .dataTables_wrapper .dataTables_filter input {
        border: 1px solid rgb(203 213 225);
        border-radius: 0.5rem;
        background: #fff;
        color: rgb(30 41 59);
        padding: 0.35rem 0.6rem;
        margin-left: 0.35rem;
    }
    .dark .page-users-index .dataTables_wrapper .dataTables_length select,
    .dark .page-users-index .dataTables_wrapper .dataTables_filter input {
        border-color: rgb(71 85 105);
        background: rgb(30 41 59);
        color: rgb(226 232 240);
    }
    .page-users-index .dataTables_wrapper .dataTables_filter input:focus,
    .page-users-index .dataTables_wrapper .dataTables_length select:focus {
        outline: none;
        border-color: rgb(16 185 129);
        box-shadow: 0 0 0 2px rgb(16 185 129 / 0.25);
    }
    .page-users-index .dataTables_wrapper .top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgb(226 232 240);
        background: rgb(248 250 252 / 0.8);
    }
    .dark .page-users-index .dataTables_wrapper .top {
        border-bottom-color: rgb(51 65 85);
        background: rgb(15 23 42 / 0.35);
    }
    .page-users-index .dataTables_wrapper .bottom {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 1.25rem;
        border-top: 1px solid rgb(226 232 240);
        background: rgb(248 250 252 / 0.5);
    }
    .dark .page-users-index .dataTables_wrapper .bottom {
        border-top-color: rgb(51 65 85);
        background: rgb(15 23 42 / 0.25);
    }
    .page-users-index .dataTables_wrapper .dataTables_paginate .paginate_button {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        margin: 0 0.1rem !important;
        padding: 0.35rem 0.65rem !important;
        border-radius: 0.5rem !important;
        border: 1px solid transparent !important;
        background: transparent !important;
        color: rgb(71 85 105) !important;
        box-shadow: none !important;
    }
    .dark .page-users-index .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: rgb(148 163 184) !important;
    }
    .page-users-index .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgb(16 185 129 / 0.12) !important;
        border-color: rgb(16 185 129 / 0.25) !important;
        color: rgb(5 150 105) !important;
    }
    .page-users-index .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .page-users-index .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: rgb(5 150 105) !important;
        border-color: rgb(5 150 105) !important;
        color: #fff !important;
    }
    .page-users-index .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    .page-users-index .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        opacity: 0.4;
        background: transparent !important;
        border-color: transparent !important;
        color: rgb(148 163 184) !important;
        cursor: default !important;
    }
    .page-users-index table.dataTable thead th {
        cursor: pointer;
        white-space: nowrap;
    }
    .page-users-index table.dataTable thead th.sorting_disabled {
        cursor: default;
    }
    .page-users-index table.dataTable.no-footer {
        border-bottom: 0 !important;
    }
    .page-users-index .dataTables_wrapper .dataTables_processing {
        background: rgb(255 255 255 / 0.9);
        color: rgb(15 23 42);
        border-radius: 0.75rem;
    }
    .dark .page-users-index .dataTables_wrapper .dataTables_processing {
        background: rgb(30 41 59 / 0.95);
        color: rgb(226 232 240);
    }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
@endpush

@section('content')
    <div class="w-full">
        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-500/20 shadow-lg dark:bg-emerald-500/30">
                    <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5.33-3.8M9 20H4v-2a4 4 0 015.33-3.8M8 7a4 4 0 118 0 4 4 0 01-8 0z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">User Accounts</h1>
                    <p class="text-slate-600 dark:text-slate-400">View and manage all application users (archived users are on Archive).</p>
                </div>
            </div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-700/70 dark:bg-emerald-900/30 dark:text-emerald-200">
                <span class="mt-0.5 inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white dark:bg-emerald-500">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="flex-1">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Table card --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px] border-collapse text-sm display" id="usersTable" style="width:100%">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/80">
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">ID</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Code</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Username</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Email</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Full Name</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Role</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Branch</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Staff modules</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Checker modules</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Status</th>
                            <th class="w-28 px-5 py-3.5 text-right font-semibold text-slate-600 dark:text-slate-300">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php
                                $staffModules = is_array($user->add_job_staff_modules) ? $user->add_job_staff_modules : [];
                                $checkerModules = is_array($user->add_job_checker_modules) ? $user->add_job_checker_modules : [];
                                $staffLabels = collect($staffModules)->map(fn ($k) => \App\Support\AddJobModules::label((string) $k))->implode(' ');
                                $checkerLabels = collect($checkerModules)->map(fn ($k) => \App\Support\AddJobModules::label((string) $k))->implode(' ');
                                $rawStatus = strtolower(trim((string) ($user->status ?: $user->task ?: 'Active')));
                                $currentStatus = in_array($rawStatus, ['inactive', 'archived'], true) ? 'Inactive' : 'Active';
                            @endphp
                            <tr class="border-b border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800/50">
                                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400" data-order="{{ $user->id }}">{{ $user->id }}</td>
                                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:ring-slate-600">
                                        {{ $user->unique_code }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">{{ $user->username }}</td>
                                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-300">{{ $user->email }}</td>
                                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-200">{{ $user->fullname }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center rounded-full bg-slate-900/5 px-2.5 py-0.5 text-xs font-semibold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-900/40 dark:text-slate-200 dark:ring-slate-600">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300">{{ $user->branch ?: '—' }}</td>
                                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300" data-search="{{ $staffLabels }}">
                                    @if(empty($staffModules))
                                        <span class="text-slate-400 dark:text-slate-500">—</span>
                                    @else
                                        <div class="flex max-w-[180px] flex-wrap gap-1">
                                            @foreach($staffModules as $moduleKey)
                                                <span class="inline-flex items-center rounded-full bg-blue-500/10 px-2 py-0.5 text-[0.7rem] font-medium text-blue-700 ring-1 ring-blue-500/20 dark:bg-blue-500/15 dark:text-blue-300 dark:ring-blue-500/30">
                                                    {{ \App\Support\AddJobModules::label((string) $moduleKey) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300" data-search="{{ $checkerLabels }}">
                                    @if(empty($checkerModules))
                                        <span class="text-slate-400 dark:text-slate-500">—</span>
                                    @else
                                        <div class="flex max-w-[180px] flex-wrap gap-1">
                                            @foreach($checkerModules as $moduleKey)
                                                <span class="inline-flex items-center rounded-full bg-violet-500/10 px-2 py-0.5 text-[0.7rem] font-medium text-violet-700 ring-1 ring-violet-500/20 dark:bg-violet-500/15 dark:text-violet-300 dark:ring-violet-500/30">
                                                    {{ \App\Support\AddJobModules::label((string) $moduleKey) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5" data-order="{{ $currentStatus }}" data-search="{{ $currentStatus }}">
                                    <form method="POST" action="{{ route('users.status', $user) }}" class="inline-block" data-user-status-form>
                                        @csrf
                                        @method('PATCH')
                                        <label class="sr-only" for="user-status-{{ $user->id }}">Status</label>
                                        <div class="relative inline-flex items-center">
                                            <span class="pointer-events-none absolute left-2.5 top-1/2 h-1.5 w-1.5 -translate-y-1/2 rounded-full {{ $currentStatus === 'Active' ? 'bg-emerald-500' : 'bg-slate-400' }}" data-status-dot></span>
                                            <select
                                                id="user-status-{{ $user->id }}"
                                                name="status"
                                                onchange="this.form.requestSubmit()"
                                                class="cursor-pointer appearance-none rounded-full border border-slate-200 bg-slate-900/5 py-1 pl-5 pr-7 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-900/10 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-200 dark:hover:bg-slate-900/60"
                                            >
                                                <option value="Active" @selected($currentStatus === 'Active')>Active</option>
                                                <option value="Inactive" @selected($currentStatus === 'Inactive')>Inactive</option>
                                            </select>
                                        </div>
                                    </form>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('users.edit', $user) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-emerald-500/15 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400" title="Edit" aria-label="Edit">
                                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" data-delete-form autocomplete="off">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-amber-500/15 hover:text-amber-600 dark:text-slate-400 dark:hover:text-amber-400" data-delete-trigger title="Archive" aria-label="Archive">
                                                <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2M5 7v10a2 2 0 002 2h10a2 2 0 002-2V7"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Archive confirmation modal --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-opacity duration-200 backdrop-blur-sm" id="deleteUserModal" role="dialog" aria-labelledby="deleteUserModalTitle" aria-modal="true">
        <div class="w-full max-w-sm overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-600 dark:bg-slate-800" role="document">
            <div class="flex items-center gap-3 px-5 py-5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-500/20 text-amber-600 dark:bg-amber-500/30 dark:text-amber-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2M5 7v10a2 2 0 002 2h10a2 2 0 002-2V7"/></svg>
                </span>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="deleteUserModalTitle">Archive User</h2>
            </div>
            <div class="px-5 pb-4">
                <div id="deleteUserModalConfirmText">
                    <p class="text-slate-600 dark:text-slate-300">Are you sure you want to move this user to archive? This action cannot be undone.</p>
                </div>
                <div class="hidden" id="deleteUserModalCountdown">
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Archiving in</p>
                    <div class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400" id="deleteUserCountdownNumber">3</div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-500">Click Cancel to abort</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:focus:ring-offset-slate-800" id="deleteUserModalCancel">Cancel</button>
                <button type="button" class="rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800" id="deleteUserModalConfirm"><span class="btn-text">Archive</span></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script>
        (function() {
            var $table = $('#usersTable');
            if ($table.length && $.fn.DataTable) {
                $table.DataTable({
                    order: [[0, 'desc']],
                    pageLength: 15,
                    lengthMenu: [[10, 15, 25, 50, 100, -1], [10, 15, 25, 50, 100, 'All']],
                    columnDefs: [
                        { orderable: false, targets: [10] },
                        { searchable: false, targets: [10] }
                    ],
                    language: {
                        search: 'Search:',
                        lengthMenu: 'Show _MENU_',
                        info: 'Showing _START_ to _END_ of _TOTAL_ users',
                        infoEmpty: 'No users to show',
                        infoFiltered: '(filtered from _MAX_ total)',
                        emptyTable: 'No user accounts yet.',
                        zeroRecords: 'No matching users found.',
                        paginate: {
                            previous: 'Prev',
                            next: 'Next'
                        }
                    },
                    dom: '<"top"lf>rt<"bottom"ip>'
                });
            }

            var modal = document.getElementById('deleteUserModal');
            var cancelBtn = document.getElementById('deleteUserModalCancel');
            var confirmBtn = document.getElementById('deleteUserModalConfirm');
            var confirmBlock = document.getElementById('deleteUserModalConfirmText');
            var countdownBlock = document.getElementById('deleteUserModalCountdown');
            var countdownNumber = document.getElementById('deleteUserCountdownNumber');
            var formToSubmit = null;
            var countdownTimer = null;

            function resetModal() {
                if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
                confirmBlock.hidden = false;
                countdownBlock.hidden = true;
                countdownBlock.classList.add('hidden');
                confirmBtn.disabled = false;
                confirmBtn.querySelector('.btn-text').textContent = 'Archive';
            }
            function closeModal() {
                modal.classList.remove('show');
                formToSubmit = null;
                resetModal();
            }

            document.addEventListener('click', function(e) {
                var btn = e.target.closest('[data-delete-trigger]');
                if (!btn || !modal) return;
                e.preventDefault();
                formToSubmit = btn.closest('[data-delete-form]');
                if (formToSubmit) {
                    resetModal();
                    modal.classList.add('show');
                }
            });
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
            if (modal) modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
            if (confirmBtn) confirmBtn.addEventListener('click', function() {
                if (!formToSubmit || countdownTimer) return;
                confirmBlock.hidden = true;
                countdownBlock.hidden = false;
                countdownBlock.classList.remove('hidden');
                confirmBtn.disabled = true;
                confirmBtn.querySelector('.btn-text').textContent = 'Archiving...';
                var count = 3;
                countdownNumber.textContent = count;
                countdownTimer = setInterval(function() {
                    count--;
                    if (count <= 0) {
                        clearInterval(countdownTimer);
                        countdownTimer = null;
                        formToSubmit.submit();
                        return;
                    }
                    countdownNumber.textContent = count;
                }, 1000);
            });
        })();
    </script>
@endpush
