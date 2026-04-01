@php
    use App\Models\RolePermission;

    $sidebar_active = $sidebar_active ?? 'announcement.index';
    $role = (string) (session('user_role') ?? '');
    $branch = session('user_branch');
    $announcementRoutes = RolePermission::allowedRoutesForRole($role, $branch);
    $canCreateAnnouncement = in_array('announcement.create', $announcementRoutes, true);
    $canEditAnnouncement = in_array('announcement.edit', $announcementRoutes, true);
    $canDeleteAnnouncement = in_array('announcement.destroy', $announcementRoutes, true);
@endphp
@extends('layouts.dashboard')

@section('title', 'Announcements')

@section('content')
    <div class="page-bph-list bph-list-page">
        <div class="bph-list-header">
            <div>
                <h1 class="bph-list-title">Announcement List</h1>
                <p class="bph-list-subtitle">Manage messages shown in the top ticker across the app.</p>
            </div>
            @if($canCreateAnnouncement)
                <div>
                    <a href="{{ route('announcement.create') }}" class="inline-flex items-center rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                        + New Announcement
                    </a>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-3 rounded-lg bg-emerald-500/10 px-3 py-2 text-sm text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="bph-table-card">
            <div class="bph-table-wrap">
                <table class="bph-table">
                    <thead>
                        <tr>
                            <th class="bph-th">Title</th>
                            <th class="bph-th">Message</th>
                            <th class="bph-th">Start Date</th>
                            <th class="bph-th">End Date</th>
                            <th class="bph-th">Status</th>
                            @if($canEditAnnouncement || $canDeleteAnnouncement)
                                <th class="bph-th bph-th-action">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                            <tr>
                                <td class="bph-td bph-td-nowrap">{{ $announcement->title }}</td>
                                <td class="bph-td">
                                    <span title="{{ $announcement->message }}">
                                        {{ \Illuminate\Support\Str::limit($announcement->message, 80) }}
                                    </span>
                                </td>
                                <td class="bph-td bph-td-nowrap">
                                    {{ optional($announcement->start_date)->format('Y-m-d') }}
                                </td>
                                <td class="bph-td bph-td-nowrap">
                                    {{ optional($announcement->end_date)->format('Y-m-d') ?: '—' }}
                                </td>
                                <td class="bph-td bph-td-nowrap">
                                    @php
                                        $status = $announcement->status;
                                        $statusLabel = strtoupper($status);
                                        $classes = match ($status) {
                                            'active' => 'bph-badge bph-badge-accepted',
                                            'inactive' => 'bph-badge bph-badge-awaiting-further-information',
                                            default => 'bph-badge bph-badge-pending',
                                        };
                                    @endphp
                                    <span class="{{ $classes }}">{{ $statusLabel }}</span>
                                </td>
                                @if($canEditAnnouncement || $canDeleteAnnouncement)
                                    <td class="bph-td bph-td-action">
                                        <div class="bph-action-btns">
                                            @if($canEditAnnouncement)
                                                <a href="{{ route('announcement.edit', $announcement) }}" class="bph-action-icon bph-action-view" title="Edit">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232a2.5 2.5 0 013.536 3.536L9.5 18.036 5 19l.964-4.5 9.268-9.268z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($canDeleteAnnouncement)
                                                <form action="{{ route('announcement.destroy', $announcement) }}" method="POST" class="inline" data-announcement-delete-form autocomplete="off">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="bph-action-icon bph-action-duplicate" title="Delete" data-announcement-delete-trigger>
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-9 0h10"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ ($canEditAnnouncement || $canDeleteAnnouncement) ? 6 : 5 }}" class="bph-td text-center text-slate-500 dark:text-slate-400">
                                    No announcements yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($announcements instanceof \Illuminate\Contracts\Pagination\Paginator)
                <div class="p-3">
                    {{ $announcements->links('vendor.pagination.dashboard') }}
                </div>
            @endif
        </div>
    </div>
        {{-- Delete announcement modal (same style as priority) --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-opacity duration-200 backdrop-blur-sm" id="deleteAnnouncementModal" role="dialog" aria-labelledby="deleteAnnouncementModalTitle" aria-modal="true">
            <div class="w-full max-w-sm overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-600 dark:bg-slate-800" role="document">
                <div class="flex items-center gap-3 px-5 py-5">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-500/20 text-red-600 dark:bg-red-500/30 dark:text-red-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </span>
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="deleteAnnouncementModalTitle">Delete announcement</h2>
                </div>
                <div class="px-5 pb-4">
                    <div id="deleteAnnouncementConfirm">
                        <p class="text-slate-600 dark:text-slate-300">This announcement will be removed from the ticker. This action cannot be undone.</p>
                    </div>
                    <div class="hidden" id="deleteAnnouncementCountdown">
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Deleting in</p>
                        <div class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400" id="deleteAnnouncementCountdownNumber">3</div>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-500">Click Cancel to abort</p>
                    </div>
                </div>
                <div class="flex justify-end gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                    <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:focus:ring-offset-slate-800" id="deleteAnnouncementModalCancel">Cancel</button>
                    <button type="button" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800" id="deleteAnnouncementModalConfirm"><span class="btn-text">Delete</span></button>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var modal = document.getElementById('deleteAnnouncementModal');
            var cancelBtn = document.getElementById('deleteAnnouncementModalCancel');
            var confirmBtn = document.getElementById('deleteAnnouncementModalConfirm');
            var confirmBlock = document.getElementById('deleteAnnouncementConfirm');
            var countdownBlock = document.getElementById('deleteAnnouncementCountdown');
            var countdownNumber = document.getElementById('deleteAnnouncementCountdownNumber');
            var formToSubmit = null;
            var countdownTimer = null;

            function resetModal() {
                if (countdownTimer) {
                    clearInterval(countdownTimer);
                    countdownTimer = null;
                }
                confirmBlock.hidden = false;
                countdownBlock.hidden = true;
                countdownBlock.classList.add('hidden');
                confirmBtn.disabled = false;
                confirmBtn.querySelector('.btn-text').textContent = 'Delete';
            }

            function closeModal() {
                modal.classList.remove('show');
                formToSubmit = null;
                resetModal();
            }

            document.querySelectorAll('[data-announcement-delete-trigger]').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    formToSubmit = this.closest('[data-announcement-delete-form]');
                    if (formToSubmit && modal) {
                        resetModal();
                        modal.classList.add('show');
                    }
                });
            });

            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
            if (modal) modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
            if (confirmBtn) confirmBtn.addEventListener('click', function () {
                if (!formToSubmit) return;
                if (countdownTimer) return;
                confirmBlock.hidden = true;
                countdownBlock.hidden = false;
                countdownBlock.classList.remove('hidden');
                confirmBtn.disabled = true;
                confirmBtn.querySelector('.btn-text').textContent = 'Deleting...';
                var count = 3;
                countdownNumber.textContent = count;
                countdownTimer = setInterval(function () {
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
