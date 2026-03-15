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
    @endpush

