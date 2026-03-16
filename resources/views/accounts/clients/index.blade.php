@extends('layouts.dashboard')

@section('title', 'Client Accounts')

@section('body_class', 'page-accounts-clients-index')

@section('content')
    <div class="w-full">
        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-500/20 shadow-lg dark:bg-emerald-500/30">
                    <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2M5 7v10a2 2 0 002 2h10a2 2 0 002-2V7"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Client Accounts</h1>
                    <p class="text-slate-600 dark:text-slate-400">Manage client accounts linked to user codes (unique_code from User Accounts).</p>
                </div>
            </div>
            <a href="{{ route('accounts.clients.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Client
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
                <table class="w-full min-w-[760px] border-collapse text-sm" id="acClientTable">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/80">
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">ID</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Client Code</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Client Name</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Client Email</th>
                            <th class="w-24 px-5 py-3.5 text-right font-semibold text-slate-600 dark:text-slate-300">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr class="border-b border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800/50">
                                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400">{{ $client->id }}</td>
                                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:ring-slate-600">
                                        {{ $client->client_code }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">{{ $client->client_name ?: '—' }}</td>
                                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-300">{{ $client->client_email ?: '—' }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('accounts.clients.edit', $client) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-emerald-500/15 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400" title="Edit" aria-label="Edit">
                                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('accounts.clients.destroy', $client) }}" method="POST" class="inline" data-delete-form autocomplete="off">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-red-500/15 hover:text-red-600 dark:text-slate-400 dark:hover:text-red-400" data-delete-trigger title="Delete" aria-label="Delete">
                                                <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                    <svg class="mx-auto mb-3 h-12 w-12 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M5 7V5a2 2 0 012-2h10a2 2 0 012 2v2M5 7v10a2 2 0 002 2h10a2 2 0 002-2V7"/></svg>
                                    <p class="font-medium">No client accounts yet.</p>
                                    <p class="mt-1 text-sm"><a href="{{ route('accounts.clients.create') }}" class="text-emerald-600 hover:underline dark:text-emerald-400">Add one</a> to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($clients->hasPages())
                <div class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 dark:border-slate-700 dark:bg-slate-800/40">
                    {{ $clients->links('vendor.pagination.dashboard') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Delete confirmation modal --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 opacity-0 pointer-events-none transition-opacity duration-200 backdrop-blur-sm" id="deleteClientModal" role="dialog" aria-labelledby="deleteClientModalTitle" aria-modal="true">
        <div class="w-full max-w-sm overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-600 dark:bg-slate-800" role="document">
            <div class="flex items-center gap-3 px-5 py-5">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-500/20 text-red-600 dark:bg-red-500/30 dark:text-red-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </span>
                <h2 class="text-lg font-bold text-slate-800 dark:text-white" id="deleteClientModalTitle">Delete Client Account</h2>
            </div>
            <div class="px-5 pb-4">
                <div id="deleteModalConfirm">
                    <p class="text-slate-600 dark:text-slate-300">Are you sure you want to delete this client account? This action cannot be undone.</p>
                </div>
                <div class="hidden" id="deleteModalCountdown">
                    <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Deleting in</p>
                    <div class="mt-2 text-2xl font-bold text-red-600 dark:text-red-400" id="deleteCountdownNumber">3</div>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-500">Click Cancel to abort</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-200 px-5 py-4 dark:border-slate-700">
                <button type="button" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:focus:ring-offset-slate-800" id="deleteClientModalCancel">Cancel</button>
                <button type="button" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800" id="deleteClientModalConfirm"><span class="btn-text">Delete</span></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            var modal = document.getElementById('deleteClientModal');
            var cancelBtn = document.getElementById('deleteClientModalCancel');
            var confirmBtn = document.getElementById('deleteClientModalConfirm');
            var confirmBlock = document.getElementById('deleteModalConfirm');
            var countdownBlock = document.getElementById('deleteModalCountdown');
            var countdownNumber = document.getElementById('deleteCountdownNumber');
            var formToSubmit = null;
            var countdownTimer = null;

            function resetModal() {
                if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
                confirmBlock.hidden = false;
                countdownBlock.hidden = true;
                confirmBtn.disabled = false;
                confirmBtn.querySelector('.btn-text').textContent = 'Delete';
            }
            function closeModal() {
                modal.classList.remove('show');
                formToSubmit = null;
                resetModal();
            }

            document.querySelectorAll('[data-delete-trigger]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    formToSubmit = this.closest('[data-delete-form]');
                    if (formToSubmit && modal) { resetModal(); modal.classList.add('show'); }
                });
            });
            if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
            if (modal) modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
            if (confirmBtn) confirmBtn.addEventListener('click', function() {
                if (!formToSubmit || countdownTimer) return;
                confirmBlock.hidden = true;
                countdownBlock.hidden = false;
                confirmBtn.disabled = true;
                confirmBtn.querySelector('.btn-text').textContent = 'Deleting...';
                var count = 3;
                countdownNumber.textContent = count;
                countdownNumber.style.animation = 'none';
                countdownNumber.offsetHeight;
                countdownNumber.style.animation = '';
                countdownTimer = setInterval(function() {
                    count--;
                    if (count <= 0) {
                        clearInterval(countdownTimer);
                        countdownTimer = null;
                        formToSubmit.submit();
                        return;
                    }
                    countdownNumber.textContent = count;
                    countdownNumber.style.animation = 'none';
                    countdownNumber.offsetHeight;
                    countdownNumber.style.animation = '';
                }, 1000);
            });
        })();
    </script>
@endpush
