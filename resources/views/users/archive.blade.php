@extends('layouts.dashboard')

@section('title', 'Archived Users')

@section('body_class', 'page-users-archive')

@section('content')
    <div class="staff-page staff-page-enter">
        <div class="staff-header">
            <div class="staff-header-text">
                <h1 class="staff-title">Archived Users</h1>
                <p class="staff-subtitle">Users that have been moved to archive.</p>
            </div>
        </div>

        <div class="staff-table-card">
            <div class="staff-table-wrap">
                <table class="staff-table" id="archivedUsersTable">
                    <colgroup>
                        <col class="staff-col-id">
                        <col class="staff-col-code">
                        <col class="staff-col-name">
                        <col class="staff-col-name">
                        <col class="staff-col-action">
                        <col class="staff-col-name">
                        <col class="staff-col-name">
                        <col class="staff-col-name">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="staff-th">ID</th>
                            <th class="staff-th">Code</th>
                            <th class="staff-th">Username</th>
                            <th class="staff-th">Email</th>
                            <th class="staff-th">Full Name</th>
                            <th class="staff-th">Role</th>
                            <th class="staff-th">Status</th>
                            <th class="staff-th staff-th-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="staff-td">{{ $user->id }}</td>
                                <td class="staff-td">{{ $user->unique_code }}</td>
                                <td class="staff-td">{{ $user->username }}</td>
                                <td class="staff-td">{{ $user->email }}</td>
                                <td class="staff-td">{{ $user->fullname }}</td>
                                <td class="staff-td">{{ $user->role }}</td>
                                <td class="staff-td">{{ $user->task ?: 'Active' }}</td>
                                <td class="staff-td staff-td-action">
                                    <form action="{{ route('users.restore', $user) }}" method="POST" autocomplete="off">
                                        @csrf
                                        <button type="submit" class="staff-action-icon staff-action-edit" title="Restore" aria-label="Restore">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="staff-td staff-td-empty">No archived users.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="staff-pagination">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .staff-page { display: block; padding-bottom: 0; max-width: 100%; }
        body.page-users-archive .content { padding-bottom: 0; }
        .staff-header { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.75rem; }
        .staff-header-text { min-width: 0; }
        .staff-title { font-size: 1.625rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.375rem 0; }
        .staff-subtitle { font-size: 0.9375rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        .staff-page-enter { animation: staff-page-in 0.4s ease-out; }
        @keyframes staff-page-in { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .staff-table-card { background: #0f172a; border: 1px solid #334155; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.15); max-width: 100%; }
        .staff-table-wrap { overflow-x: auto; }
        .staff-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .staff-col-id { width: 70px; }
        .staff-col-code { width: 90px; }
        .staff-col-name { width: auto; }
        .staff-col-action { width: 110px; }
        .staff-th { text-align: left; padding: 0.75rem 1rem; font-weight: 600; color: #94a3b8; background: #1e293b; border-bottom: 1px solid #334155; }
        .staff-th-action { text-align: center; }
        .staff-td { padding: 0.75rem 1rem; border-bottom: 1px solid #334155; color: #e2e8f0; vertical-align: middle; }
        .staff-td-action { text-align: center; }
        .staff-td-empty { text-align: center; color: #94a3b8; padding: 2rem; }
        .staff-table tbody tr:hover { background: rgba(255,255,255,0.03); }
        .staff-action-btns { display: flex; align-items: center; gap: 0.35rem; justify-content: center; flex-wrap: wrap; }
        .staff-action-icon { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; padding: 0; border: none; border-radius: 999px; background: rgba(34,197,94,0.12); color: #bbf7d0; cursor: pointer; transition: background 0.15s, color 0.15s; text-decoration: none; }
        .staff-action-icon svg { display: block; pointer-events: none; }
        .staff-action-icon:hover { background: rgba(34,197,94,0.22); color: #22c55e; }
        html[data-theme="light"] .staff-table-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .staff-title { color: #1e293b; }
        html[data-theme="light"] .staff-subtitle { color: #64748b; }
        html[data-theme="light"] .staff-th { background: #f8fafc; color: #64748b; border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .staff-td { border-bottom-color: #e2e8f0; color: #1e293b; }
        html[data-theme="light"] .staff-table tbody tr:hover { background: #f8fafc; }
        html[data-theme="light"] .staff-action-icon { background: rgba(34,197,94,0.16); color: #16a34a; }
        html[data-theme="light"] .staff-action-icon:hover { background: rgba(34,197,94,0.24); color: #15803d; }
    </style>
@endpush

