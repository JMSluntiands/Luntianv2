@extends('layouts.dashboard')

@section('title', 'Archived Branches')

@section('body_class', 'page-branch-archive')

@section('content')
    <div class="staff-page staff-page-enter">
        <div class="staff-header">
            <div class="staff-header-text">
                <h1 class="staff-title">Archived Branches</h1>
                <p class="staff-subtitle">Branches that have been moved to archive.</p>
            </div>
        </div>

        <div class="staff-table-card">
            <div class="staff-table-wrap">
                <table class="staff-table" id="archivedBranchesTable">
                    <colgroup>
                        <col class="staff-col-id">
                        <col class="staff-col-name">
                        <col class="staff-col-action">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="staff-th">ID</th>
                            <th class="staff-th">Branch Name</th>
                            <th class="staff-th staff-th-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                            <tr>
                                <td class="staff-td">{{ $branch->id }}</td>
                                <td class="staff-td">{{ $branch->branch_name }}</td>
                                <td class="staff-td staff-td-action">
                                    <form action="{{ route('branch.restore', $branch->id) }}" method="POST" autocomplete="off">
                                        @csrf
                                        <button type="submit" class="staff-action-icon staff-action-edit" title="Restore" aria-label="Restore">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="staff-td staff-td-empty">No archived branches.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($branches->hasPages())
                <div class="staff-pagination">
                    {{ $branches->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    @endpush

