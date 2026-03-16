@extends('layouts.dashboard')

@section('title', 'Archived Users')

@section('body_class', 'page-users-archive')

@section('content')
    <div class="w-full">
        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-amber-500/15 shadow-lg dark:bg-amber-500/25">
                <svg class="h-8 w-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5.33-3.8M9 20H4v-2a4 4 0 015.33-3.8M8 7a4 4 0 118 0 4 4 0 01-8 0z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Archived Users</h1>
                <p class="text-slate-600 dark:text-slate-400">Users that have been moved to archive. Restore them to make active again.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-700/70 dark:bg-emerald-900/30 dark:text-emerald-200">
                <span class="mt-0.5 inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white dark:bg-emerald-500">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span class="flex-1">{{ session('success') }}</span>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] border-collapse text-sm" id="archivedUsersTable">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800/80">
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">ID</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Code</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Username</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Email</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Full Name</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Role</th>
                            <th class="px-5 py-3.5 text-left font-semibold text-slate-600 dark:text-slate-300">Status</th>
                            <th class="w-28 px-5 py-3.5 text-right font-semibold text-slate-600 dark:text-slate-300">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="border-b border-slate-100 transition-colors hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-800/50">
                                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400">{{ $user->id }}</td>
                                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-100 dark:ring-slate-600">
                                        {{ $user->unique_code }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-slate-200">{{ $user->username }}</td>
                                <td class="px-5 py-3.5 text-slate-600 dark:text-slate-300">{{ $user->email }}</td>
                                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-200">{{ $user->fullname }}</td>
                                <td class="px-5 py-3.5 text-slate-700 dark:text-slate-300">{{ $user->role }}</td>
                                <td class="px-5 py-3.5">
                                    @php $status = $user->task ?: 'Active'; @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1"
                                          @class([
                                              'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-600/60' => $status === 'Active',
                                              'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-500/10 dark:text-rose-300 dark:ring-rose-600/60' => $status === 'Archived',
                                              'bg-slate-50 text-slate-700 ring-slate-200 dark:bg-slate-700/60 dark:text-slate-100 dark:ring-slate-500/80' => ! in_array($status, ['Active', 'Archived']),
                                          ])>
                                        <span class="mr-1 h-1.5 w-1.5 rounded-full"
                                              @class([
                                                  'bg-emerald-500' => $status === 'Active',
                                                  'bg-rose-500' => $status === 'Archived',
                                                  'bg-slate-400' => ! in_array($status, ['Active', 'Archived']),
                                              ])></span>
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <form action="{{ route('users.restore', $user) }}" method="POST" autocomplete="off" class="inline-flex">
                                        @csrf
                                        <button type="submit" class="inline-flex h-9 items-center justify-center rounded-lg bg-emerald-600 px-3 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 dark:focus:ring-offset-slate-900" title="Restore" aria-label="Restore">
                                            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                                            Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                    <svg class="mx-auto mb-3 h-12 w-12 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-5.33-3.8M9 20H4v-2a4 4 0 015.33-3.8M8 7a4 4 0 118 0 4 4 0 01-8 0z"/></svg>
                                    <p class="font-medium">No archived users.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 dark:border-slate-700 dark:bg-slate-800/40">
                    {{ $users->links('vendor.pagination.dashboard') }}
                </div>
            @endif
        </div>
    </div>
@endsection

