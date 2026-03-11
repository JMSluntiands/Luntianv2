@extends('layouts.dashboard')

@section('title', 'Job Details')

@section('body_class', 'page-lbs-view')

@section('content')
    <div class="job-view-page">
        <nav class="job-view-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}">Home</a>
            <span class="job-view-breadcrumb-sep">/</span>
            <a href="{{ route('lbs.list') }}">Job List</a>
            <span class="job-view-breadcrumb-sep">/</span>
            <span class="job-view-breadcrumb-current">
                Job {{ $job->reference ?? $job->job_id ?? $jobId ?? '' }}
            </span>
        </nav>

        <header class="job-view-header">
            <div class="job-view-header-inner">
                <h1 class="job-view-title">Job Details</h1>
                <p class="job-view-ref">
                    Reference: {{ $job->reference ?? $job->job_reference_no ?? $jobId ?? '—' }}
                </p>
            </div>
            <a href="{{ route('lbs.list') }}" class="job-view-back">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to List
            </a>
        </header>

        @php
            $planFiles = [];
            $docFiles = [];
            $folderName = $job->job_reference_no ?? $job->client_reference_no ?? $job->reference ?? '';

            if (!empty($job->upload_files)) {
                $decoded = json_decode($job->upload_files, true);
                if (is_array($decoded)) {
                    $planFiles = $decoded;
                }
            }

            if (!empty($job->upload_project_files)) {
                $decoded = json_decode($job->upload_project_files, true);
                if (is_array($decoded)) {
                    $docFiles = $decoded;
                }
            }
        @endphp

        @php
            $isAllocated = strtolower($job->job_status ?? '') === 'allocated';
            $statusBg = $statusColor ?? null;
            $priorityBg = $priorityColor ?? null;
        @endphp

        <div class="job-view-grid">
            <section class="job-view-card job-view-card-wide">
                <div class="job-view-card-head">
                    <h2 class="job-view-card-title">Client Details</h2>
                    <button type="button" class="job-view-card-action" aria-label="Edit" data-job-view-edit data-edit-title="Client Details" data-edit-target="client">Edit</button>
                </div>
                <dl class="job-view-dl">
                    <div class="job-view-dl-row">
                        <dt>Log Date</dt>
                        <dd>
                            @if(!empty($job->log_date))
                                {{ \Carbon\Carbon::parse($job->log_date)->format('M d, Y h:i A') }}
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div class="job-view-dl-row">
                        <dt>Client Reference</dt>
                        <dd>{{ $job->client_reference_no ?? '—' }}</dd>
                    </div>
                    <div class="job-view-dl-row">
                        <dt>Job Number</dt>
                        <dd>{{ $job->job_reference_no ?? $job->reference ?? $jobId ?? '—' }}</dd>
                    </div>
                    <div class="job-view-dl-row">
                        <dt>Compliance</dt>
                        <dd>{{ $job->ncc_compliance ?? '—' }}</dd>
                    </div>
                    <div class="job-view-dl-row">
                        <dt>Client</dt>
                        <dd>{{ $job->client_account_name ?? $job->client_code ?? '—' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="job-view-card job-view-card-wide">
                <div class="job-view-card-head">
                    <h2 class="job-view-card-title">Job Details</h2>
                    <button type="button" class="job-view-card-action" aria-label="Edit" data-job-view-edit data-edit-title="Job Details" data-edit-target="job">Edit</button>
                </div>
                <dl class="job-view-dl">
                    <div class="job-view-dl-row">
                        <dt>Job Status</dt>
                        <dd>
                            @if($isAllocated)
                                <button type="button"
                                        class="job-view-badge job-view-status-btn"
                                        @if($statusBg)
                                            style="background-color: {{ $statusBg }};"
                                        @endif
                                        data-job-view-edit
                                        data-edit-title="Job Status"
                                        data-edit-target="job">
                                    {{ $job->job_status ?? '—' }}
                                </button>
                            @else
                                <span
                                    class="job-view-badge job-view-status-badge-disabled"
                                    @if($statusBg)
                                        style="background-color: {{ $statusBg }};"
                                    @endif
                                    aria-disabled="true"
                                >
                                    {{ $job->job_status ?? '—' }}
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div class="job-view-dl-row">
                        <dt>Job Address</dt>
                        <dd>{{ $job->address_client ?? '—' }}</dd>
                    </div>
                    <div class="job-view-dl-row">
                        <dt>Priority</dt>
                        <dd>
                            <span
                                class="job-view-pill"
                                @if($priorityBg)
                                    style="background-color: {{ $priorityBg }};"
                                @endif
                            >
                                {{ $job->priority ?? '—' }}
                            </span>
                        </dd>
                    </div>
                    <div class="job-view-dl-row">
                        <dt>Job Type</dt>
                        <dd>{{ $job->job_type ?? '—' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="job-view-card job-view-card-notes">
                <div class="job-view-card-head">
                    <h2 class="job-view-card-title">Notes</h2>
                    <button type="button" class="job-view-card-action" aria-label="Edit" data-job-view-edit data-edit-title="Notes" data-edit-target="notes">Edit</button>
                </div>
                <div class="job-view-notes job-view-notes-rich">
                    {!! $job->notes ?: '<p>No notes yet.</p>' !!}
                </div>
            </section>

            <div class="job-view-notes-side">
                <section class="job-view-card job-view-card-compact">
                    <h2 class="job-view-card-title">Assigned</h2>
                    <dl class="job-view-dl job-view-dl-compact job-view-dl-assigned">
                        <div class="job-view-dl-row">
                            <dt>Staff</dt>
                            <dd class="job-view-assigned-wrap">
                                <button type="button" class="job-view-assigned-select" aria-haspopup="listbox" aria-expanded="false" aria-label="Change staff" data-assigned-trigger>
                                    <span class="job-view-assigned-value">SB</span>
                                    <svg class="job-view-assigned-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                                </button>
                                <ul class="job-view-assigned-dropdown" role="listbox" id="staff-listbox" hidden>
                                    <li role="option" data-value="SB">SB</li>
                                    <li role="option" data-value="GM">GM</li>
                                    <li role="option" data-value="JDR">JDR</li>
                                    <li role="option" data-value="PEP">PEP</li>
                                    <li role="option" data-value="JS">JS</li>
                                </ul>
                            </dd>
                        </div>
                        <div class="job-view-dl-row">
                            <dt>Checker</dt>
                            <dd class="job-view-assigned-wrap">
                                <button type="button" class="job-view-assigned-select" aria-haspopup="listbox" aria-expanded="false" aria-label="Change checker" data-assigned-trigger>
                                    <span class="job-view-assigned-value">GM</span>
                                    <svg class="job-view-assigned-arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                                </button>
                                <ul class="job-view-assigned-dropdown" role="listbox" id="checker-listbox" hidden>
                                    <li role="option" data-value="SB">SB</li>
                                    <li role="option" data-value="GM">GM</li>
                                    <li role="option" data-value="JDR">JDR</li>
                                    <li role="option" data-value="PEP">PEP</li>
                                    <li role="option" data-value="JS">JS</li>
                                </ul>
                            </dd>
                        </div>
                    </dl>
                </section>
                <section class="job-view-card job-view-card-compact">
                    <h2 class="job-view-card-title">Complexity</h2>
                    <div class="job-view-complexity">
                        <span class="job-view-stars" aria-label="4 out of 5">@include('lbs.partials.stars', ['rating' => 4])</span>
                    </div>
                </section>
            </div>

            <section class="job-view-card job-view-card-col-4">
                <div class="job-view-card-head">
                    <h2 class="job-view-card-title">Plans</h2>
                    @if($isAllocated)
                        <button type="button"
                                class="job-view-card-action job-view-card-action-primary"
                                data-job-view-add-files
                                data-add-title="Plans">
                            Add Files
                        </button>
                    @else
                        <button type="button"
                                class="job-view-card-action job-view-card-action-primary job-view-card-action-disabled"
                                disabled
                                aria-disabled="true">
                            Add Files
                        </button>
                    @endif
                </div>
                @if(!empty($planFiles) && $folderName)
                    <ul class="job-view-files">
                        @foreach($planFiles as $file)
                            @php
                                $fileName = (string) $file;
                                $fileUrl = asset('document/' . $folderName . '/' . $fileName);
                            @endphp
                            <li class="job-view-file-item">
                                <div class="job-view-file-main">
                                    <span class="job-view-file-icon" aria-hidden="true">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg>
                                    </span>
                                    <span class="job-view-file-name">{{ $fileName }}</span>
                                </div>
                                <div class="job-view-file-actions">
                                    <a href="{{ $fileUrl }}" class="job-view-file-btn" title="Download" aria-label="Download" download>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                        <span class="job-view-file-btn-label">Download</span>
                                    </a>
                                    <a href="{{ $fileUrl }}" target="_blank" class="job-view-file-btn" title="View" aria-label="View">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <span class="job-view-file-btn-label">View</span>
                                    </a>
                                    @if($isAllocated)
                                        <button type="button"
                                                class="job-view-file-btn job-view-file-btn-danger job-view-file-btn-delete"
                                                data-job-file-type="plans"
                                                data-job-file-name="{{ $fileName }}"
                                                title="Delete"
                                                aria-label="Delete">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                                            <span class="job-view-file-btn-label">Delete</span>
                                        </button>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="job-view-empty">No plan files uploaded yet.</p>
                @endif
            </section>

            <section class="job-view-card job-view-card-col-4">
                <div class="job-view-card-head">
                    <h2 class="job-view-card-title">Documents</h2>
                    @if($isAllocated)
                        <button type="button"
                                class="job-view-card-action job-view-card-action-primary"
                                data-job-view-add-files
                                data-add-title="Documents">
                            Add Files
                        </button>
                    @else
                        <button type="button"
                                class="job-view-card-action job-view-card-action-primary job-view-card-action-disabled"
                                disabled
                                aria-disabled="true">
                            Add Files
                        </button>
                    @endif
                </div>
                @if(!empty($docFiles) && $folderName)
                    <ul class="job-view-files">
                        @foreach($docFiles as $file)
                            @php
                                $fileName = (string) $file;
                                $fileUrl = asset('document/' . $folderName . '/' . $fileName);
                            @endphp
                            <li class="job-view-file-item">
                                <div class="job-view-file-main">
                                    <span class="job-view-file-icon" aria-hidden="true">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg>
                                    </span>
                                    <span class="job-view-file-name">{{ $fileName }}</span>
                                </div>
                                <div class="job-view-file-actions">
                                    <a href="{{ $fileUrl }}" class="job-view-file-btn" title="Download" aria-label="Download" download>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                        <span class="job-view-file-btn-label">Download</span>
                                    </a>
                                    <a href="{{ $fileUrl }}" target="_blank" class="job-view-file-btn" title="View" aria-label="View">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <span class="job-view-file-btn-label">View</span>
                                    </a>
                                    @if($isAllocated)
                                        <button type="button"
                                                class="job-view-file-btn job-view-file-btn-danger job-view-file-btn-delete"
                                                data-job-file-type="documents"
                                                data-job-file-name="{{ $fileName }}"
                                                title="Delete"
                                                aria-label="Delete">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                                            <span class="job-view-file-btn-label">Delete</span>
                                        </button>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="job-view-empty">No document files uploaded yet.</p>
                @endif
            </section>

            <section class="job-view-card job-view-card-col-4">
                <div class="job-view-card-head">
                    <h2 class="job-view-card-title">Checker Upload Files</h2>
                    <button type="button" class="job-view-card-action job-view-card-action-primary" data-job-view-add-files data-add-title="Checker Upload Files">Add Files</button>
                </div>
                <ul class="job-view-checker-uploads">
                    <li class="job-view-checker-upload">
                        <div class="job-view-checker-upload-head">Upload 1</div>
                        <div class="job-view-file-item">
                            <span class="job-view-file-icon" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg>
                            </span>
                            <span class="job-view-file-name">Checker_review_172726.pdf</span>
                            <div class="job-view-file-actions">
                                <a href="#" class="job-view-file-btn" title="Download" aria-label="Download">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                </a>
                                <a href="#" class="job-view-file-btn" title="View" aria-label="View">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <button type="button" class="job-view-file-btn job-view-file-btn-danger" title="Delete" aria-label="Delete">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="job-view-checker-notes">
                            <span class="job-view-checker-notes-label">Notes</span>
                            <div class="job-view-notes-rich">
                                <p><strong>Review completed.</strong> All items verified. Please see <em>section 2.1</em> for energy rating.</p>
                                <ul>
                                    <li><strong>Plans</strong> — approved</li>
                                    <li><em>Compliance</em> — 2022 WOH met</li>
                                    <li>No changes required</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="job-view-checker-upload">
                        <div class="job-view-checker-upload-head">Upload 2</div>
                        <div class="job-view-file-item">
                            <span class="job-view-file-icon" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg>
                            </span>
                            <span class="job-view-file-name">Compliance_check_{{ $jobId ?? '42376' }}.pdf</span>
                            <div class="job-view-file-actions">
                                <a href="#" class="job-view-file-btn" title="Download" aria-label="Download">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                </a>
                                <a href="#" class="job-view-file-btn" title="View" aria-label="View">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <button type="button" class="job-view-file-btn job-view-file-btn-danger" title="Delete" aria-label="Delete">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M10 11v6M14 11v6"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="job-view-checker-notes">
                            <span class="job-view-checker-notes-label">Notes</span>
                            <div class="job-view-notes-rich">
                                <p><strong>Compliance check</strong> — 2022 WOH requirements <em>confirmed</em>.</p>
                                <ul>
                                    <li>Energy rating <strong>passed</strong></li>
                                    <li>Documentation <em>complete</em></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </section>

            <section class="job-view-card job-view-card-comments">
                <h2 class="job-view-card-title">Run Comments</h2>
                <ul class="job-view-comment-list">
                    <li class="job-view-comment-item">
                        <div class="job-view-comment-user">
                            <span class="job-view-comment-avatar" aria-hidden="true">L</span>
                            <span class="job-view-comment-name">LUNTIAN</span>
                        </div>
                        <div class="job-view-comment-content">
                            <p class="job-view-comment-text">Starting the run now. Will update when <strong>section 2</strong> is done.</p>
                            <span class="job-view-comment-time">Mar 06, 2026 04:45 PM</span>
                        </div>
                    </li>
                    <li class="job-view-comment-item">
                        <div class="job-view-comment-user">
                            <span class="job-view-comment-avatar" aria-hidden="true">G</span>
                            <span class="job-view-comment-name">GM</span>
                        </div>
                        <div class="job-view-comment-content">
                            <p class="job-view-comment-text"><em>Noted.</em> Check compliance box before submitting.</p>
                            <span class="job-view-comment-time">Mar 06, 2026 05:10 PM</span>
                        </div>
                    </li>
                </ul>
                <div class="job-view-comment-editor">
                    <div class="job-view-comment-toolbar">
                        <button type="button" class="job-view-comment-btn" data-cmd="bold" title="Bold"><span>B</span></button>
                        <button type="button" class="job-view-comment-btn" data-cmd="italic" title="Italic"><span>I</span></button>
                        <button type="button" class="job-view-comment-btn" data-cmd="underline" title="Underline"><span>U</span></button>
                        <button type="button" class="job-view-comment-btn job-view-comment-btn-icon" data-cmd="insertUnorderedList" title="Bullets">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
                        </button>
                        <button type="button" class="job-view-comment-btn job-view-comment-btn-icon" data-cmd="insertOrderedList" title="Numbered list">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="job-view-comment-body" contenteditable="true" data-placeholder="Write a run comment..." role="textbox"></div>
                    <div class="job-view-comment-footer">
                        <button type="button" class="job-view-comment-send">Send</button>
                    </div>
                </div>
            </section>

            <section class="job-view-card job-view-card-comments">
                <h2 class="job-view-card-title">Comments</h2>
                <ul class="job-view-comment-list">
                    <li class="job-view-comment-item">
                        <div class="job-view-comment-user">
                            <span class="job-view-comment-avatar" aria-hidden="true">L</span>
                            <span class="job-view-comment-name">LUNTIAN</span>
                        </div>
                        <div class="job-view-comment-content">
                            <p class="job-view-comment-text">Priority set to <strong>High 1 day</strong>. Client requested quick turnaround.</p>
                            <span class="job-view-comment-time">Mar 06, 2026 04:41 PM</span>
                        </div>
                    </li>
                    <li class="job-view-comment-item">
                        <div class="job-view-comment-user">
                            <span class="job-view-comment-avatar" aria-hidden="true">S</span>
                            <span class="job-view-comment-name">SB</span>
                        </div>
                        <div class="job-view-comment-content">
                            <p class="job-view-comment-text">Assigned to me. I'll have the <em>plans</em> reviewed by EOD.</p>
                            <span class="job-view-comment-time">Mar 06, 2026 03:20 PM</span>
                        </div>
                    </li>
                </ul>
                <div class="job-view-comment-editor">
                    <div class="job-view-comment-toolbar">
                        <button type="button" class="job-view-comment-btn" data-cmd="bold" title="Bold"><span>B</span></button>
                        <button type="button" class="job-view-comment-btn" data-cmd="italic" title="Italic"><span>I</span></button>
                        <button type="button" class="job-view-comment-btn" data-cmd="underline" title="Underline"><span>U</span></button>
                        <button type="button" class="job-view-comment-btn job-view-comment-btn-icon" data-cmd="insertUnorderedList" title="Bullets">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor"/><circle cx="4" cy="12" r="1" fill="currentColor"/><circle cx="4" cy="18" r="1" fill="currentColor"/></svg>
                        </button>
                        <button type="button" class="job-view-comment-btn job-view-comment-btn-icon" data-cmd="insertOrderedList" title="Numbered list">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="job-view-comment-body" contenteditable="true" data-placeholder="Write a comment..." role="textbox"></div>
                    <div class="job-view-comment-footer">
                        <button type="button" class="job-view-comment-send">Send</button>
                    </div>
                </div>
            </section>

            <aside class="job-view-sidebar">
                <section class="job-view-card job-view-card-activity">
                    <h2 class="job-view-card-title">Activity</h2>
                    <ul class="job-view-activity">
                        <li class="job-view-activity-item">
                            <div class="job-view-activity-user">
                                <span class="job-view-activity-avatar" aria-hidden="true">L</span>
                                <span class="job-view-activity-name">LUNTIAN</span>
                            </div>
                            <div class="job-view-activity-content">
                                <span class="job-view-activity-time">Mar 06, 2026 04:41 PM</span>
                                <p class="job-view-activity-label">Job details updated</p>
                                <ul class="job-view-activity-changes">
                                    <li><span class="job-view-activity-old">Pending</span> <span class="job-view-activity-arrow" aria-hidden="true">→</span> <span class="job-view-activity-new">Accepted</span></li>
                                    <li><span class="job-view-activity-old">Standard 2 days</span> <span class="job-view-activity-arrow" aria-hidden="true">→</span> <span class="job-view-activity-new">High 1 day</span></li>
                                    <li><span class="job-view-activity-old">1S DB Base Model</span> <span class="job-view-activity-arrow" aria-hidden="true">→</span> <span class="job-view-activity-new">19 DB Base Model · 19 Design Builder Model</span></li>
                                </ul>
                            </div>
                        </li>
                        <li class="job-view-activity-item">
                            <div class="job-view-activity-user">
                                <span class="job-view-activity-avatar" aria-hidden="true">L</span>
                                <span class="job-view-activity-name">LUNTIAN</span>
                            </div>
                            <div class="job-view-activity-content">
                                <span class="job-view-activity-time">Mar 06, 2026 04:41 PM</span>
                                <p class="job-view-activity-label">Plan complexity updated</p>
                                <ul class="job-view-activity-changes">
                                    <li><span class="job-view-activity-old">3</span> <span class="job-view-activity-arrow" aria-hidden="true">→</span> <span class="job-view-activity-new">4</span></li>
                                </ul>
                            </div>
                        </li>
                        <li class="job-view-activity-item">
                            <div class="job-view-activity-user">
                                <span class="job-view-activity-avatar" aria-hidden="true">L</span>
                                <span class="job-view-activity-name">LUNTIAN</span>
                            </div>
                            <div class="job-view-activity-content">
                                <span class="job-view-activity-time">Mar 06, 2026 04:41 PM</span>
                                <p class="job-view-activity-label">Staff assigned</p>
                                <ul class="job-view-activity-changes">
                                    <li><span class="job-view-activity-old">—</span> <span class="job-view-activity-arrow" aria-hidden="true">→</span> <span class="job-view-activity-new">SB</span></li>
                                </ul>
                            </div>
                        </li>
                        <li class="job-view-activity-item">
                            <div class="job-view-activity-user">
                                <span class="job-view-activity-avatar" aria-hidden="true">L</span>
                                <span class="job-view-activity-name">LUNTIAN</span>
                            </div>
                            <div class="job-view-activity-content">
                                <span class="job-view-activity-time">Mar 06, 2026 03:20 PM</span>
                                <p class="job-view-activity-label">Notes updated</p>
                                <ul class="job-view-activity-changes">
                                    <li><span class="job-view-activity-old">(empty)</span> <span class="job-view-activity-arrow" aria-hidden="true">→</span> <span class="job-view-activity-new">Added: priority review and compliance checklist</span></li>
                                </ul>
                            </div>
                        </li>
                        <li class="job-view-activity-item">
                            <div class="job-view-activity-user">
                                <span class="job-view-activity-avatar" aria-hidden="true">L</span>
                                <span class="job-view-activity-name">LUNTIAN</span>
                            </div>
                            <div class="job-view-activity-content">
                                <span class="job-view-activity-time">Mar 06, 2026 02:15 PM</span>
                                <p class="job-view-activity-label">Notes updated</p>
                                <ul class="job-view-activity-changes">
                                    <li><span class="job-view-activity-old">Short note</span> <span class="job-view-activity-arrow" aria-hidden="true">→</span> <span class="job-view-activity-new">Added bullet points for WOH requirements</span></li>
                                </ul>
                            </div>
                        </li>
                        <li class="job-view-activity-item">
                            <div class="job-view-activity-user">
                                <span class="job-view-activity-avatar" aria-hidden="true">L</span>
                                <span class="job-view-activity-name">LUNTIAN</span>
                            </div>
                            <div class="job-view-activity-content">
                                <span class="job-view-activity-time">Mar 05, 2026 11:00 AM</span>
                                <p class="job-view-activity-label">Notes created</p>
                                <ul class="job-view-activity-changes">
                                    <li><span class="job-view-activity-old">—</span> <span class="job-view-activity-arrow" aria-hidden="true">→</span> <span class="job-view-activity-new">Initial notes for this job</span></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </section>
            </aside>
        </div>

        @include('lbs.modals.edit-modal')
        @include('lbs.modals.add-files-modal')
    </div>
@endsection

@push('styles')
    @include('lbs.modals.styles')
    <style>
        @keyframes jobViewFadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes jobViewFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes jobViewSlideIn {
            from { opacity: 0; transform: translateX(-8px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .job-view-page { padding-bottom: 2rem; max-width: 1400px; margin: 0 auto; }
        .job-view-breadcrumb { font-size: 0.8125rem; color: #64748b; margin-bottom: 1rem; opacity: 0; animation: jobViewFadeInUp 0.45s ease-out forwards; }
        .job-view-breadcrumb a { color: #94a3b8; text-decoration: none; }
        .job-view-breadcrumb a:hover { color: #e2e8f0; text-decoration: underline; }
        .job-view-breadcrumb-sep { margin: 0 0.35rem; opacity: 0.7; }
        .job-view-breadcrumb-current { color: #e2e8f0; }
        .job-view-header { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.75rem; opacity: 0; animation: jobViewFadeInUp 0.5s ease-out 0.06s forwards; }
        .job-view-back { transition: transform 0.2s ease; }
        .job-view-back:hover { transform: translateX(-2px); }
        .job-view-header-inner { min-width: 0; }
        .job-view-title { font-size: 1.75rem; font-weight: 700; color: #fff; letter-spacing: -0.02em; margin: 0 0 0.25rem 0; }
        .job-view-ref { font-size: 0.9375rem; color: #94a3b8; margin: 0; }
        .job-view-back { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; font-weight: 500; color: #94a3b8; text-decoration: none; padding: 0.5rem 0.75rem; border-radius: 8px; transition: color 0.2s, background 0.2s; }
        .job-view-back:hover { color: #e2e8f0; background: rgba(255,255,255,0.06); }
        .job-view-grid { display: grid; grid-template-columns: repeat(12, 1fr); gap: 1.25rem; }
        .job-view-grid > * { opacity: 0; animation: jobViewFadeInUp 0.5s ease-out forwards; animation-fill-mode: both; }
        .job-view-grid > *:nth-child(1) { animation-delay: 0.1s; }
        .job-view-grid > *:nth-child(2) { animation-delay: 0.14s; }
        .job-view-grid > *:nth-child(3) { animation-delay: 0.18s; }
        .job-view-grid > *:nth-child(4) { animation-delay: 0.22s; }
        .job-view-grid > *:nth-child(5) { animation-delay: 0.26s; }
        .job-view-grid > *:nth-child(6) { animation-delay: 0.3s; }
        .job-view-grid > *:nth-child(7) { animation-delay: 0.34s; }
        .job-view-grid > *:nth-child(8) { animation-delay: 0.38s; }
        .job-view-grid > *:nth-child(9) { animation-delay: 0.42s; }
        .job-view-grid > *:nth-child(10) { animation-delay: 0.46s; }
        .job-view-card-wide { grid-column: span 6; }
        .job-view-card-full { grid-column: span 12; }
        .job-view-card-notes { grid-column: span 8; }
        .job-view-notes-side { grid-column: span 4; display: flex; flex-direction: column; gap: 1rem; }
        .job-view-notes-side .job-view-card { flex: 0 0 auto; }
        .job-view-card-col-4 { grid-column: span 4; }
        .job-view-card-col-6 { grid-column: span 6; }
        .job-view-sidebar { grid-column: span 12; }
        .job-view-card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 1.25rem 1.5rem; transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .job-view-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.18); }
        .job-view-card-head { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 1rem; }
        .job-view-card-title { font-size: 1rem; font-weight: 600; color: #e2e8f0; margin: 0; }
        .job-view-card-action { font-size: 0.8125rem; color: #94a3b8; background: none; border: none; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 6px; transition: color 0.2s, background 0.2s, transform 0.2s; }
        .job-view-card-action:hover { transform: scale(1.02); }
        .job-view-card-action:hover { color: #e2e8f0; background: rgba(255,255,255,0.06); }
        .job-view-card-action-primary { color: #60a5fa; }
        .job-view-card-action-primary:hover { color: #93c5fd; background: rgba(96,165,250,0.15); }
        .job-view-dl { display: grid; gap: 0.6rem 1rem; margin: 0; }
        .job-view-dl-row { display: grid; grid-template-columns: 140px 1fr; gap: 0.5rem; align-items: baseline; }
        .job-view-dl dt { font-size: 0.8125rem; color: #64748b; font-weight: 500; margin: 0; }
        .job-view-dl dd { font-size: 0.9375rem; color: #e2e8f0; margin: 0; }
        .job-view-dl-compact .job-view-dl-row { grid-template-columns: 80px 1fr; }
        .job-view-assigned-wrap { position: relative; }
        .job-view-assigned-select { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0; font-size: 0.9375rem; font-weight: 500; color: #e2e8f0; background: transparent; border: none; cursor: pointer; font-family: inherit; transition: color 0.2s, transform 0.2s; }
        .job-view-assigned-select:hover { transform: scale(1.05); }
        .job-view-assigned-select:hover { color: #f8fafc; }
        .job-view-assigned-arrow { flex-shrink: 0; color: #94a3b8; transition: transform 0.2s; }
        .job-view-assigned-select[aria-expanded="true"] .job-view-assigned-arrow { transform: rotate(180deg); }
        .job-view-assigned-dropdown { position: absolute; left: 0; top: 100%; margin: 0.25rem 0 0 0; padding: 0.35rem 0; min-width: 100%; background: #1e293b; border: 1px solid #334155; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); list-style: none; z-index: 10; max-height: 200px; overflow-y: auto; }
        .job-view-assigned-dropdown[hidden] { display: none; }
        .job-view-assigned-dropdown li { padding: 0.4rem 0.75rem; font-size: 0.875rem; color: #e2e8f0; cursor: pointer; }
        .job-view-assigned-dropdown li:hover { background: rgba(255,255,255,0.08); }
        html[data-theme="light"] .job-view-assigned-select { color: #1e293b; }
        html[data-theme="light"] .job-view-assigned-select:hover { color: #0f172a; }
        html[data-theme="light"] .job-view-assigned-arrow { color: #64748b; }
        html[data-theme="light"] .job-view-assigned-dropdown { background: #fff; border-color: #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        html[data-theme="light"] .job-view-assigned-dropdown li { color: #334155; }
        html[data-theme="light"] .job-view-assigned-dropdown li:hover { background: #f1f5f9; }
        .job-view-badge { display: inline-block; padding: 0.2rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 6px; }
        .job-view-badge-accepted { background: rgba(34,197,94,0.2); color: #86efac; }
        .job-view-status-btn { cursor: pointer; border: none; background: transparent; }
        .job-view-status-btn:hover { background: rgba(34,197,94,0.18); }
        .job-view-status-badge-disabled { opacity: 0.6; cursor: not-allowed; }
        .job-view-pill { display: inline-block; padding: 0.2rem 0.5rem; font-size: 0.75rem; font-weight: 600; border-radius: 6px; }
        .job-view-pill-high { background: #ea580c; color: #fff; }
        .job-view-notes { min-height: 60px; }
        .job-view-notes-rich { font-size: 0.9375rem; line-height: 1.5; color: #e2e8f0; }
        .job-view-notes-rich p { margin: 0 0 0.75em 0; }
        .job-view-notes-rich p:last-child { margin-bottom: 0; }
        .job-view-notes-rich strong { font-weight: 700; color: #e2e8f0; }
        .job-view-notes-rich em { font-style: italic; }
        .job-view-notes-rich ul { margin: 0.5em 0 0.75em 0; padding-left: 1.5em; list-style-type: disc; }
        .job-view-notes-rich ol { margin: 0.5em 0 0.75em 0; padding-left: 1.5em; list-style-type: decimal; }
        .job-view-notes-rich li { margin-bottom: 0.25em; }
        .job-view-empty { font-size: 0.875rem; color: #64748b; margin: 0; }
        .job-view-files { list-style: none; margin: 0; padding: 0; }
        .job-view-file-item { display: flex; flex-direction: column; align-items: flex-start; gap: 0.4rem; padding: 0.5rem 0; border-bottom: 1px solid #334155; transition: opacity 0.2s ease; }
        .job-view-file-item:hover { opacity: 0.95; }
        .job-view-file-item:last-child { border-bottom: none; }
        .job-view-file-main { display: flex; align-items: center; gap: 0.5rem; width: 100%; }
        .job-view-file-icon { color: #f87171; flex-shrink: 0; }
        .job-view-file-name { font-size: 0.875rem; color: #e2e8f0; flex: 1; min-width: 0; }
        .job-view-file-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 0.25rem; }
        .job-view-file-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 0.5rem; border-radius: 8px; color: #94a3b8; background: transparent; border: none; cursor: pointer; text-decoration: none; transition: color 0.2s, background 0.2s; font-size: 0.75rem; gap: 0.25rem; }
        .job-view-file-btn:hover { color: #e2e8f0; background: rgba(255,255,255,0.08); }
        .job-view-file-btn-danger:hover { color: #f87171; background: rgba(248,113,113,0.15); }
        .job-view-file-btn-label { display: inline-block; }
        .job-view-checker-uploads { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 1rem; }
        .job-view-checker-upload { background: #0f172a; border: 1px solid #334155; border-radius: 10px; padding: 1rem; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .job-view-checker-upload:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .job-view-checker-upload-head { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid #334155; }
        .job-view-checker-upload .job-view-file-item { border-bottom: none; padding: 0 0 0.5rem 0; }
        .job-view-checker-notes { margin-top: 0.5rem; padding-top: 0.75rem; border-top: 1px solid #334155; }
        .job-view-checker-notes-label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.5rem; }
        .job-view-checker-notes .job-view-notes-rich { font-size: 0.875rem; }
        .job-view-checker-notes .job-view-notes-rich p { margin-bottom: 0.5em; }
        .job-view-checker-notes .job-view-notes-rich ul { margin: 0.35em 0 0 0; padding-left: 1.25rem; }
        .job-view-complexity { margin-top: 0.25rem; }
        .job-view-complexity .job-view-stars { display: flex; justify-content: space-between; align-items: center; width: 100%; max-width: 140px; }
        .job-view-complexity .lbs-stars-inner { display: flex; justify-content: space-between; align-items: center; width: 100%; gap: 0; }
        .job-view-complexity .lbs-star { width: 20px; height: 20px; flex-shrink: 0; }
        .job-view-complexity .lbs-star-filled { color: #eab308; }
        .job-view-complexity .lbs-star { transition: transform 0.2s ease; }
        .job-view-complexity .lbs-star:hover { transform: scale(1.15); }
        .job-view-complexity .lbs-star-empty { color: #64748b; opacity: 0.7; }
        .job-view-card-compact { }
        .job-view-card-compact .job-view-card-title { margin-bottom: 0.75rem; }
        .job-view-card-comments { grid-column: span 6; }
        .job-view-comment-empty { font-size: 0.875rem; color: #64748b; margin: 0 0 0.75rem 0; }
        .job-view-comment-list { list-style: none; margin: 0 0 1rem 0; padding: 0; }
        .job-view-comment-item { display: flex; gap: 0.75rem; align-items: flex-start; padding: 0.75rem 0; border-bottom: 1px solid #334155; opacity: 0; animation: jobViewFadeInUp 0.35s ease-out forwards; animation-fill-mode: both; transition: background 0.2s ease; }
        .job-view-comment-item:nth-child(1) { animation-delay: 0.15s; }
        .job-view-comment-item:nth-child(2) { animation-delay: 0.2s; }
        .job-view-comment-item:hover { background: rgba(255,255,255,0.02); }
        .job-view-comment-item:last-child { border-bottom: none; padding-bottom: 0; }
        .job-view-comment-user { display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; min-width: 88px; }
        .job-view-comment-avatar { width: 32px; height: 32px; border-radius: 50%; background: #475569; color: #e2e8f0; font-size: 0.8125rem; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .job-view-comment-name { font-size: 0.75rem; font-weight: 600; color: #e2e8f0; white-space: nowrap; }
        .job-view-comment-content { flex: 1; min-width: 0; align-self: stretch; display: flex; flex-direction: column; justify-content: flex-start; }
        .job-view-comment-text { font-size: 0.875rem; color: #94a3b8; line-height: 1.45; margin: 0 0 0.25rem 0; }
        .job-view-comment-text strong { color: #e2e8f0; font-weight: 700; }
        .job-view-comment-text em { font-style: italic; }
        .job-view-comment-time { font-size: 0.7rem; color: #64748b; }
        .job-view-comment-editor { border: 1px solid #334155; border-radius: 10px; background: #0f172a; overflow: hidden; }
        .job-view-comment-toolbar { display: flex; align-items: center; gap: 2px; padding: 6px 10px; border-bottom: 1px solid #334155; background: #1e293b; }
        .job-view-comment-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border: none; border-radius: 6px; background: transparent; color: #94a3b8; font-size: 0.85rem; cursor: pointer; font-family: inherit; transition: background 0.2s, color 0.2s; }
        .job-view-comment-btn:hover { background: rgba(255,255,255,0.08); color: #e2e8f0; }
        .job-view-comment-btn.active { background: #2c5282; color: #e2e8f0; }
        .job-view-comment-btn.active:hover { background: #2b6cb0; color: #fff; }
        .job-view-comment-btn span { font-weight: 700; }
        .job-view-comment-btn-icon span { display: none; }
        .job-view-comment-btn-icon svg { display: block; }
        .job-view-comment-body { min-height: 80px; padding: 0.625rem 0.875rem; color: #e2e8f0; font-size: 0.9375rem; line-height: 1.5; outline: none; }
        .job-view-comment-body:empty::before { content: attr(data-placeholder); color: #64748b; }
        .job-view-comment-body ul, .job-view-comment-body ol { margin: 0.5em 0; padding-left: 1.5em; }
        .job-view-comment-footer { padding: 0.5rem 0.75rem; border-top: 1px solid #334155; display: flex; justify-content: flex-end; }
        .job-view-comment-send { padding: 0.4rem 1rem; font-size: 0.8125rem; font-weight: 600; color: #fff; background: #2563eb; border: none; border-radius: 6px; cursor: pointer; font-family: inherit; transition: background 0.2s ease, transform 0.2s ease; }
        .job-view-comment-send:hover { background: #1d4ed8; transform: scale(1.03); }
        html[data-theme="light"] .job-view-comment-editor { border-color: #e2e8f0; background: #f8fafc; }
        html[data-theme="light"] .job-view-comment-toolbar { border-bottom-color: #e2e8f0; background: #f1f5f9; }
        html[data-theme="light"] .job-view-comment-btn { color: #64748b; }
        html[data-theme="light"] .job-view-comment-btn:hover { background: #e2e8f0; color: #334155; }
        html[data-theme="light"] .job-view-comment-btn.active { background: rgba(44,82,139,0.35); color: #1e40af; }
        html[data-theme="light"] .job-view-comment-btn.active:hover { background: rgba(44,82,139,0.45); color: #1e40af; }
        html[data-theme="light"] .job-view-comment-body { color: #1e293b; }
        html[data-theme="light"] .job-view-comment-body:empty::before { color: #94a3b8; }
        html[data-theme="light"] .job-view-comment-footer { border-top-color: #e2e8f0; }
        html[data-theme="light"] .job-view-comment-item { border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .job-view-comment-avatar { background: #cbd5e1; color: #1e293b; }
        html[data-theme="light"] .job-view-comment-name { color: #1e293b; }
        html[data-theme="light"] .job-view-comment-text { color: #475569; }
        html[data-theme="light"] .job-view-comment-text strong { color: #1e293b; }
        .job-view-card-activity { grid-column: 1 / -1; }
        .job-view-activity { list-style: none; margin: 0; padding: 0; max-height: 320px; overflow-y: auto; }
        .job-view-activity-item { display: flex; gap: 1rem; align-items: flex-start; padding: 0.85rem 0; border-bottom: 1px solid #334155; opacity: 0; animation: jobViewSlideIn 0.4s ease-out forwards; animation-fill-mode: both; transition: background 0.2s ease; }
        .job-view-activity-item:nth-child(1) { animation-delay: 0.5s; }
        .job-view-activity-item:nth-child(2) { animation-delay: 0.54s; }
        .job-view-activity-item:nth-child(3) { animation-delay: 0.58s; }
        .job-view-activity-item:nth-child(4) { animation-delay: 0.62s; }
        .job-view-activity-item:nth-child(5) { animation-delay: 0.66s; }
        .job-view-activity-item:nth-child(6) { animation-delay: 0.7s; }
        .job-view-activity-item:hover { background: rgba(255,255,255,0.03); }
        .job-view-activity-item:last-child { border-bottom: none; }
        .job-view-activity-user { display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; }
        .job-view-activity-avatar { width: 36px; height: 36px; border-radius: 50%; background: #475569; color: #e2e8f0; font-size: 0.9375rem; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; }
        .job-view-activity-name { font-size: 0.8125rem; font-weight: 600; color: #e2e8f0; }
        .job-view-activity-content { flex: 1; min-width: 0; }
        .job-view-activity-time { font-size: 0.75rem; color: #64748b; display: block; margin-bottom: 0.25rem; }
        .job-view-activity-label { font-size: 0.8125rem; font-weight: 600; color: #94a3b8; margin: 0 0 0.4rem 0; }
        .job-view-activity-changes { list-style: none; margin: 0; padding: 0; }
        .job-view-activity-changes li { font-size: 0.875rem; color: #94a3b8; margin-bottom: 0.35rem; line-height: 1.45; display: flex; align-items: baseline; flex-wrap: wrap; gap: 0.35rem; }
        .job-view-activity-changes li:last-child { margin-bottom: 0; }
        .job-view-activity-old { color: #64748b; text-decoration: line-through; }
        .job-view-activity-arrow { color: #64748b; font-size: 0.75rem; flex-shrink: 0; }
        .job-view-activity-new { color: #e2e8f0; font-weight: 500; }
        .job-view-activity-text { font-size: 0.875rem; color: #94a3b8; margin: 0; line-height: 1.4; }
        html[data-theme="light"] .job-view-breadcrumb-current { color: #1e293b; }
        html[data-theme="light"] .job-view-breadcrumb a { color: #64748b; }
        html[data-theme="light"] .job-view-breadcrumb a:hover { color: #1e293b; }
        html[data-theme="light"] .job-view-title { color: #1e293b; }
        html[data-theme="light"] .job-view-ref { color: #64748b; }
        html[data-theme="light"] .job-view-back { color: #64748b; }
        html[data-theme="light"] .job-view-back:hover { color: #1e293b; background: #e2e8f0; }
        html[data-theme="light"] .job-view-card { background: #fff; border-color: #e2e8f0; }
        html[data-theme="light"] .job-view-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        html[data-theme="light"] .job-view-card-title { color: #1e293b; }
        html[data-theme="light"] .job-view-dl dd { color: #334155; }
        html[data-theme="light"] .job-view-file-name { color: #334155; }
        html[data-theme="light"] .job-view-file-item { border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .job-view-activity-item { border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .job-view-activity-text { color: #475569; }
        html[data-theme="light"] .job-view-activity-label { color: #64748b; }
        html[data-theme="light"] .job-view-activity-changes li { color: #475569; }
        html[data-theme="light"] .job-view-activity-old { color: #94a3b8; }
        html[data-theme="light"] .job-view-activity-arrow { color: #94a3b8; }
        html[data-theme="light"] .job-view-activity-new { color: #1e293b; }
        html[data-theme="light"] .job-view-activity-item { border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .job-view-activity-avatar { background: #cbd5e1; color: #1e293b; }
        html[data-theme="light"] .job-view-activity-name { color: #1e293b; }
        html[data-theme="light"] .job-view-notes-rich { color: #334155; }
        html[data-theme="light"] .job-view-notes-rich strong { color: #1e293b; }
        html[data-theme="light"] .job-view-checker-upload { background: #f1f5f9; border-color: #e2e8f0; }
        html[data-theme="light"] .job-view-checker-upload-head { color: #64748b; border-bottom-color: #e2e8f0; }
        html[data-theme="light"] .job-view-checker-notes { border-top-color: #e2e8f0; }
        html[data-theme="light"] .job-view-checker-notes-label { color: #64748b; }
        html[data-theme="light"] .job-view-complexity .lbs-star-filled { color: #ca8a04; }
        html[data-theme="light"] .job-view-complexity .lbs-star-empty { color: #94a3b8; }
        .job-view-card-action-disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }
        @media (max-width: 900px) {
            .job-view-grid { grid-template-columns: 1fr; }
            .job-view-card-wide,
            .job-view-card-full,
            .job-view-card-notes,
            .job-view-notes-side,
            .job-view-card-comments,
            .job-view-card-col-4,
            .job-view-card-col-6 { grid-column: 1; }
            .job-view-notes-side { flex-direction: column; }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function() {
    var editOverlay = document.getElementById('jobViewEditModalOverlay');
    var addOverlay = document.getElementById('jobViewAddFilesModalOverlay');
    var formClient = document.getElementById('jobViewEditFormClient');
    var formJob = document.getElementById('jobViewEditFormJob');
    var formNotes = document.getElementById('jobViewEditFormNotes');
    function openEditModal(title, target) {
        if (editOverlay) {
            var titleEl = document.getElementById('jobViewEditModalTitle');
            if (titleEl) titleEl.textContent = 'Edit ' + (title || '');
            if (formClient) formClient.hidden = true;
            if (formJob) formJob.hidden = true;
            if (formNotes) formNotes.hidden = true;
            var show = target === 'client' ? formClient : (target === 'job' ? formJob : (target === 'notes' ? formNotes : null));
            if (show) show.hidden = false;
            editOverlay.classList.add('is-open');
            editOverlay.setAttribute('aria-hidden', 'false');
        }
    }
    function closeEditModal() {
        if (editOverlay) {
            editOverlay.classList.remove('is-open');
            editOverlay.setAttribute('aria-hidden', 'true');
        }
    }
    function openAddFilesModal(title) {
        if (addOverlay) {
            var titleEl = document.getElementById('jobViewAddFilesModalTitle');
            var sectionEl = document.getElementById('jobViewAddFilesModalSection');
            if (titleEl) titleEl.textContent = 'Add Files — ' + (title || '');
            if (sectionEl) sectionEl.textContent = title || 'this section';
            var input = document.getElementById('jobViewAddFilesInput');
            if (input) input.value = '';
            var selectedWrap = document.getElementById('jobViewModalSelectedWrap');
            var selectedList = document.getElementById('jobViewModalSelectedFiles');
            if (selectedWrap) selectedWrap.hidden = true;
            if (selectedList) selectedList.innerHTML = '';
            var checkerNotes = document.getElementById('jobViewModalCheckerNotes');
            var existingWrap = document.getElementById('jobViewModalExistingWrap');
            if (checkerNotes) checkerNotes.hidden = (title !== 'Checker Upload Files');
            if (existingWrap) existingWrap.hidden = (title === 'Checker Upload Files');
            addOverlay.classList.add('is-open');
            addOverlay.setAttribute('aria-hidden', 'false');
        }
    }
    function closeAddFilesModal() {
        if (addOverlay) {
            addOverlay.classList.remove('is-open');
            addOverlay.setAttribute('aria-hidden', 'true');
        }
    }
    document.querySelectorAll('[data-job-view-edit]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            openEditModal(this.getAttribute('data-edit-title'), this.getAttribute('data-edit-target'));
        });
    });
    document.querySelectorAll('[data-job-view-add-files]').forEach(function(btn) {
        btn.addEventListener('click', function() { openAddFilesModal(this.getAttribute('data-add-title')); });
    });
    (function() {
        var input = document.getElementById('jobViewAddFilesInput');
        var selectedWrap = document.getElementById('jobViewModalSelectedWrap');
        var selectedList = document.getElementById('jobViewModalSelectedFiles');
        if (!input || !selectedWrap || !selectedList) return;
        var fileIconSvg = '<span class="job-view-modal-file-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg></span>';
        input.addEventListener('change', function() {
            selectedList.innerHTML = '';
            var files = this.files;
            if (files && files.length > 0) {
                for (var i = 0; i < files.length; i++) {
                    var li = document.createElement('li');
                    li.className = 'job-view-modal-file-item job-view-modal-file-item-new';
                    li.innerHTML = fileIconSvg + '<span class="job-view-modal-file-name">' + (files[i].name || 'File ' + (i + 1)) + '</span>';
                    selectedList.appendChild(li);
                }
                selectedWrap.hidden = false;
            } else {
                selectedWrap.hidden = true;
            }
        });
    })();
    document.addEventListener('click', function(e) {
        var closeEdit = e.target.closest('[data-job-view-close-edit]');
        var closeAdd = e.target.closest('[data-job-view-close-add]');
        if (closeEdit) { e.preventDefault(); e.stopPropagation(); closeEditModal(); }
        if (closeAdd) { e.preventDefault(); e.stopPropagation(); closeAddFilesModal(); }
    });
    if (editOverlay) editOverlay.addEventListener('click', function(e) { if (e.target === editOverlay) closeEditModal(); });
    if (addOverlay) addOverlay.addEventListener('click', function(e) { if (e.target === addOverlay) closeAddFilesModal(); });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeEditModal(); closeAddFilesModal(); }
    });
    function bindRichTextToolbar(containerSel, bodySel) {
        document.querySelectorAll(containerSel).forEach(function(container) {
            var editor = container.querySelector(bodySel);
            var btns = container.querySelectorAll('.job-view-comment-btn[data-cmd]');
            function updateActiveState() {
                if (!editor || document.activeElement !== editor) return;
                btns.forEach(function(btn) {
                    var cmd = btn.getAttribute('data-cmd');
                    var active = cmd ? document.queryCommandState(cmd) : false;
                    btn.classList.toggle('active', !!active);
                });
            }
            btns.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var cmd = this.getAttribute('data-cmd');
                    if (!cmd || !editor) return;
                    editor.focus();
                    document.execCommand(cmd, false, null);
                    setTimeout(updateActiveState, 0);
                });
            });
            if (editor) {
                editor.addEventListener('focus', updateActiveState);
                editor.addEventListener('keyup', updateActiveState);
                editor.addEventListener('mouseup', updateActiveState);
                document.addEventListener('selectionchange', function() { if (document.activeElement === editor) updateActiveState(); });
            }
        });
    }
    bindRichTextToolbar('.job-view-comment-editor', '.job-view-comment-body');
    bindRichTextToolbar('.job-view-modal-notes-editor', '.job-view-modal-notes-body');
    document.querySelectorAll('.job-view-comment-body, .job-view-modal-notes-body').forEach(function(body) {
        body.addEventListener('paste', function(e) { e.preventDefault(); var t = e.clipboardData.getData('text/plain'); document.execCommand('insertText', false, t); });
    });
    function closeAllAssignedDropdowns() {
        document.querySelectorAll('.job-view-assigned-dropdown').forEach(function(list) { list.hidden = true; });
        document.querySelectorAll('[data-assigned-trigger]').forEach(function(btn) { btn.setAttribute('aria-expanded', 'false'); });
    }
    document.querySelectorAll('[data-assigned-trigger]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            var wrap = this.closest('.job-view-assigned-wrap');
            var list = wrap && wrap.querySelector('.job-view-assigned-dropdown');
            var isOpen = list && !list.hidden;
            closeAllAssignedDropdowns();
            if (list && !isOpen) {
                list.hidden = false;
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });
    document.querySelectorAll('.job-view-assigned-dropdown li').forEach(function(option) {
        option.addEventListener('click', function(e) {
            e.stopPropagation();
            var wrap = this.closest('.job-view-assigned-wrap');
            var valueEl = wrap && wrap.querySelector('.job-view-assigned-value');
            var list = wrap && wrap.querySelector('.job-view-assigned-dropdown');
            var val = this.getAttribute('data-value');
            if (valueEl && val) valueEl.textContent = val;
            if (list) list.hidden = true;
            var btn = wrap && wrap.querySelector('[data-assigned-trigger]');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        });
    });
    document.addEventListener('click', closeAllAssignedDropdowns);
    document.addEventListener('click', function(e) {
        var delBtn = e.target.closest('[data-job-view-modal-delete-file]');
        if (!delBtn) return;
        var item = delBtn.closest('.job-view-modal-file-item');
        if (!item) return;
        if (typeof confirm !== 'undefined' && !confirm('Delete this file?')) return;
        var list = document.getElementById('jobViewModalExistingFiles');
        var noFiles = document.getElementById('jobViewModalNoFiles');
        item.remove();
        if (list && list.children.length === 0 && noFiles) {
            list.hidden = true;
            noFiles.hidden = false;
        }
    });
})();
</script>
    <script>
    $(function() {
        $('.job-view-modal .select2-single').select2({ width: '100%', allowClear: false });
    });
    </script>
@endpush
