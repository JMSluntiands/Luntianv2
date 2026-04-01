@php
    $sidebar_active = $sidebar_active ?? ($announcement->exists ? 'announcement.edit' : 'announcement.create');
    $isEdit = $announcement->exists;
@endphp
@extends('layouts.dashboard')

@section('title', $isEdit ? 'Edit Announcement' : 'New Announcement')

@section('content')
    <div class="page-bph-list bph-list-page">
        <div class="bph-list-header">
            <div>
                <h1 class="bph-list-title">{{ $isEdit ? 'Edit Announcement' : 'New Announcement' }}</h1>
                <p class="bph-list-subtitle">
                    {{ $isEdit ? 'Update the message shown in the announcement ticker.' : 'Create a message to show in the announcement ticker.' }}
                </p>
            </div>
        </div>

        <div class="bph-table-card">
            <div class="bph-row-detail-inner">
                <form method="POST" action="{{ $isEdit ? route('announcement.update', $announcement) : route('announcement.store') }}">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    <div class="bph-row-detail-grid">
                        <div class="bph-row-detail-item">
                            <label class="bph-row-detail-label" for="title">Title</label>
                            <input
                                id="title"
                                type="text"
                                name="title"
                                value="{{ old('title', $announcement->title) }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
                                required
                            />
                            @error('title')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bph-row-detail-item">
                            <label class="bph-row-detail-label" for="status">Status</label>
                            <select
                                id="status"
                                name="status"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
                                required
                            >
                                @php
                                    $statusValue = old('status', $announcement->status ?: 'draft');
                                @endphp
                                <option value="draft" {{ $statusValue === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ $statusValue === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $statusValue === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bph-row-detail-item">
                            <label class="bph-row-detail-label" for="start_date">Start Date</label>
                            <input
                                id="start_date"
                                type="date"
                                name="start_date"
                                value="{{ old('start_date', optional($announcement->start_date)->format('Y-m-d')) }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
                                required
                            />
                            @error('start_date')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bph-row-detail-item">
                            <label class="bph-row-detail-label" for="end_date">End Date</label>
                            <input
                                id="end_date"
                                type="date"
                                name="end_date"
                                value="{{ old('end_date', optional($announcement->end_date)->format('Y-m-d')) }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
                            />
                            @error('end_date')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="bph-row-detail-label" for="message">Message</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="3"
                            class="mt-1 w-full resize-y rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
                            required
                        >{{ old('message', $announcement->message) }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            This text will appear in the moving announcement bar at the top of every page.
                        </p>
                    </div>

                    <div class="mt-6 flex items-center justify-between gap-3">
                        <a href="{{ route('announcement.index') }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                            {{ $isEdit ? 'Save Changes' : 'Create Announcement' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

