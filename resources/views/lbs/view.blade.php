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

        @php
            $isArchived = strtolower($job->job_status ?? '') === 'archived';
        @endphp
        <header class="job-view-header">
            <div class="job-view-header-inner">
                <h1 class="job-view-title">Job Details</h1>
                <p class="job-view-ref">
                    Reference: {{ $job->reference ?? $job->job_reference_no ?? $jobId ?? '—' }}
                </p>
            </div>
            <div class="job-view-header-actions">
                @if(!$isArchived)
                    <button type="button" class="job-view-archive-btn" id="jobViewArchiveJobBtn" aria-label="Archive this job">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8v13H3V8M1 3h22v5H1zM10 12h4"/></svg>
                        Archive this job
                    </button>
                @endif
                <a href="{{ route('lbs.list') }}" class="job-view-back">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                    Back to List
                </a>
            </div>
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
            $rawStatus = $job->job_status ?? '';
            $lowerStatus = strtolower($rawStatus);
            $isAllocated = $lowerStatus === 'allocated';
            $statusBg = $statusColor ?? null;
            $priorityBg = $priorityColor ?? null;
            // Same flow as Edit Job Details modal: Allocated→Accepted/Processing; Accepted/Processing/Revised→For Checking; For Checking→For Review/Revised
            $canEditStatusInline = in_array($lowerStatus, ['allocated', 'accepted', 'processing', 'revised', 'for checking'], true);
            $inlineStatusOptions = [];
            if ($lowerStatus === 'allocated') {
                foreach ($statuses ?? [] as $s) {
                    $n = strtolower((string)($s->name ?? ''));
                    if (in_array($n, ['accepted', 'processing'], true)) $inlineStatusOptions[] = $s->name;
                }
            } elseif (in_array($lowerStatus, ['accepted', 'processing', 'revised'], true)) {
                foreach ($statuses ?? [] as $s) {
                    if (strtolower((string)($s->name ?? '')) === 'for checking') $inlineStatusOptions[] = $s->name;
                }
            } elseif ($lowerStatus === 'for checking') {
                foreach ($statuses ?? [] as $s) {
                    $n = strtolower((string)($s->name ?? ''));
                    if (in_array($n, ['for review', 'revised'], true)) $inlineStatusOptions[] = $s->name;
                }
            } else {
                foreach ($statuses ?? [] as $s) { $inlineStatusOptions[] = $s->name; }
            }
        @endphp

        <div class="job-view-grid">
            <section class="job-view-card job-view-card-wide" id="jobClientCard">
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
                            @if($canEditStatusInline && count($inlineStatusOptions) > 0)
                                <div class="lbs-status-wrap job-view-inline-status" data-status-wrap>
                                    <button type="button"
                                            class="lbs-badge lbs-status-trigger"
                                            @if($statusBg)
                                                style="background-color: {{ $statusBg }};"
                                            @endif
                                            data-status-trigger
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                        {{ $job->job_status ?? '—' }}
                                    </button>
                                    <div class="lbs-status-menu" role="menu" hidden>
                                        @foreach($inlineStatusOptions as $opt)
                                            <button type="button" role="menuitem" class="lbs-status-option" data-status-value="{{ $opt }}">{{ $opt }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <span
                                    class="lbs-badge job-view-status-badge-disabled"
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
                            <dd>
                                <div class="lbs-initials-wrap" data-initials-wrap data-role="staff">
                                    <button type="button" class="lbs-initials lbs-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false" aria-label="Change staff">{{ $job->staff_id ? strtoupper($job->staff_id) : '--' }}</button>
                                    <div class="lbs-initials-menu" role="menu" hidden>
                                        @forelse($assignmentUsers ?? [] as $user)
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="{{ $user->unique_code }}">{{ $user->unique_code }}</button>
                                        @empty
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="SB">SB</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="GM">GM</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="PEP">PEP</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="JDR">JDR</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="JS">JS</button>
                                        @endforelse
                                    </div>
                                </div>
                            </dd>
                        </div>
                        <div class="job-view-dl-row">
                            <dt>Checker</dt>
                            <dd>
                                <div class="lbs-initials-wrap" data-initials-wrap data-role="checker">
                                    <button type="button" class="lbs-initials lbs-initials-trigger" data-initials-trigger aria-haspopup="true" aria-expanded="false" aria-label="Change checker">{{ $job->checker_id ? strtoupper($job->checker_id) : '--' }}</button>
                                    <div class="lbs-initials-menu" role="menu" hidden>
                                        @forelse($assignmentUsers ?? [] as $user)
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="{{ $user->unique_code }}">{{ $user->unique_code }}</button>
                                        @empty
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="SB">SB</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="GM">GM</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="PEP">PEP</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="JDR">JDR</button>
                                            <button type="button" role="menuitem" class="lbs-initials-option" data-value="JS">JS</button>
                                        @endforelse
                                    </div>
                                </div>
                            </dd>
                        </div>
                    </dl>
                </section>
                <section class="job-view-card job-view-card-compact">
                    <h2 class="job-view-card-title">Complexity</h2>
                    @php
                        $complexity = is_numeric($job->plan_complexity ?? null) ? (int) $job->plan_complexity : 0;
                        $complexity = max(0, min(5, $complexity));
                    @endphp
                    <div class="job-view-complexity">
                        <button type="button"
                                class="job-view-complexity-button"
                                data-complexity-rating="{{ $complexity }}"
                                aria-label="Set complexity (current: {{ $complexity }} of 5)">
                            @include('lbs.partials.stars', ['rating' => $complexity])
                        </button>
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
                                $fileUrl = route('lbs.job.file', ['id' => $jobId, 'file' => $fileName]);
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
                                $fileUrl = route('lbs.job.file', ['id' => $jobId, 'file' => $fileName]);
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
                @if(($checkerUploads ?? collect())->isEmpty())
                    <p class="job-view-empty">No checker uploads yet.</p>
                @else
                    <ul class="job-view-checker-uploads">
                        @foreach($checkerUploads as $index => $upload)
                            @php
                                $files = json_decode($upload->files_json ?? '[]', true) ?: [];
                                $uploadNumber = $loop->iteration;
                            @endphp
                            <li class="job-view-checker-upload">
                                <div class="job-view-checker-upload-head">Upload {{ $uploadNumber }}</div>
                                @foreach($files as $fileName)
                                    @php
                                        $fileName = (string) $fileName;
                                        $fileUrl = isset($folderName) && $folderName ? route('lbs.job.file', ['id' => $jobId, 'file' => $fileName]) : '#';
                                    @endphp
                                    <div class="job-view-file-item">
                                        <span class="job-view-file-icon" aria-hidden="true">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg>
                                        </span>
                                        <span class="job-view-file-name">{{ $fileName }}</span>
                                        <div class="job-view-file-actions">
                                            <a href="{{ $fileUrl }}" class="job-view-file-btn" title="Download" aria-label="Download" download>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                            </a>
                                            <a href="{{ $fileUrl }}" target="_blank" class="job-view-file-btn" title="View" aria-label="View">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                                @if(trim($upload->comment ?? '') !== '')
                                    <div class="job-view-checker-notes">
                                        <span class="job-view-checker-notes-label">Notes</span>
                                        <div class="job-view-notes-rich">
                                            {!! $upload->comment !!}
                                        </div>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="job-view-card job-view-card-comments">
                <h2 class="job-view-card-title">Run Comments</h2>
                <ul class="job-view-comment-list" id="runCommentsList">
                    @forelse($runComments as $runComment)
                        <li class="job-view-comment-item">
                            <div class="job-view-comment-user">
                                @php $initial = strtoupper(mb_substr($runComment->name ?? 'L', 0, 1)); @endphp
                                <span class="job-view-comment-avatar" aria-hidden="true">{{ $initial }}</span>
                                <span class="job-view-comment-name">{{ $runComment->name ?? 'LUNTIAN' }}</span>
                            </div>
                            <div class="job-view-comment-content">
                                <p class="job-view-comment-text">{!! $runComment->message !!}</p>
                                <span class="job-view-comment-time">{{ $runComment->created_at }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="job-view-comment-item">
                            <div class="job-view-comment-content">
                                <p class="job-view-comment-text">No run comments yet.</p>
                            </div>
                        </li>
                    @endforelse
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
                    <div class="job-view-comment-body" id="runCommentBody" contenteditable="true" data-placeholder="Write a run comment..." role="textbox"></div>
                    <div class="job-view-comment-footer">
                        <button type="button" class="job-view-comment-send" id="runCommentSendBtn">Send</button>
                    </div>
                </div>
            </section>

            <section class="job-view-card job-view-card-comments">
                <h2 class="job-view-card-title">Comments</h2>
                <ul class="job-view-comment-list" id="jobCommentsList">
                    @forelse($jobComments as $comment)
                        <li class="job-view-comment-item">
                            <div class="job-view-comment-user">
                                @php $initial = strtoupper(mb_substr($comment->username ?? 'L', 0, 1)); @endphp
                                <span class="job-view-comment-avatar" aria-hidden="true">{{ $initial }}</span>
                                <span class="job-view-comment-name">{{ $comment->username ?? 'LUNTIAN' }}</span>
                            </div>
                            <div class="job-view-comment-content">
                                <p class="job-view-comment-text">{!! $comment->message !!}</p>
                                <span class="job-view-comment-time">{{ $comment->created_at }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="job-view-comment-item">
                            <div class="job-view-comment-content">
                                <p class="job-view-comment-text">No comments yet.</p>
                            </div>
                        </li>
                    @endforelse
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
                    <div class="job-view-comment-body" id="jobCommentBody" contenteditable="true" data-placeholder="Write a comment..." role="textbox"></div>
                    <div class="job-view-comment-footer">
                        <button type="button" class="job-view-comment-send" id="jobCommentSendBtn">Send</button>
                    </div>
                </div>
            </section>

            <aside class="job-view-sidebar">
                <section class="job-view-card job-view-card-activity" id="jobActivityCard">
                    <h2 class="job-view-card-title">Activity</h2>
                    <ul class="job-view-activity" id="jobActivityList">
                        @if(($activityLogs ?? collect())->isEmpty())
                            <li class="job-view-activity-item">
                                <div class="job-view-activity-content">
                                    <p class="job-view-activity-text">No activity yet for this job.</p>
                                </div>
                            </li>
                        @else
                            @include('lbs.partials.activity-log-items', [
                                'activityLogs' => $activityLogs,
                                'jobStatus'    => $job->job_status ?? null,
                                'userRoleMap'  => $userRoleMap ?? [],
                            ])
                        @endif
                    </ul>
                </section>
            </aside>
        </div>

        @include('lbs.modals.edit-modal')
        @include('lbs.modals.add-files-modal')
        <div class="job-view-modal-overlay" id="jobViewDeleteFileModalOverlay" aria-hidden="true">
            <div class="job-view-modal" role="dialog" aria-modal="true" aria-labelledby="jobViewDeleteFileModalTitle">
                <div class="job-view-modal-header">
                    <h2 class="job-view-modal-title" id="jobViewDeleteFileModalTitle">Delete file</h2>
                </div>
                <div class="job-view-modal-body">
                    <div class="job-view-delete-confirm" id="jobViewDeleteFileConfirm">
                        <p class="job-view-modal-label">Are you sure you want to delete this file? This cannot be undone.</p>
                    </div>
                    <div class="job-view-delete-countdown" id="jobViewDeleteFileCountdown" hidden>
                        <p class="job-view-countdown-text">Deleting in</p>
                        <div class="job-view-countdown-number" id="jobViewDeleteFileCountdownNumber">3</div>
                        <p class="job-view-countdown-cancel-hint">Click Cancel to abort</p>
                    </div>
                </div>
                <div class="job-view-modal-footer">
                    <button type="button" class="job-view-modal-btn job-view-modal-btn-cancel" id="jobViewDeleteFileModalCancel">Cancel</button>
                    <button type="button" class="job-view-modal-btn job-view-modal-btn-primary job-view-modal-btn-danger" id="jobViewDeleteFileModalConfirm"><span class="job-view-delete-btn-text">Delete</span></button>
                </div>
            </div>
        </div>

        <div class="job-view-modal-overlay" id="jobViewArchiveJobModalOverlay" aria-hidden="true">
            <div class="job-view-modal" role="dialog" aria-modal="true" aria-labelledby="jobViewArchiveJobModalTitle">
                <div class="job-view-modal-header">
                    <h2 class="job-view-modal-title" id="jobViewArchiveJobModalTitle">Archive this job</h2>
                </div>
                <div class="job-view-modal-body">
                    <div class="job-view-delete-confirm" id="jobViewArchiveJobConfirm">
                        <p class="job-view-modal-label">Are you sure you want to archive this job? The job will be moved to the archive list.</p>
                    </div>
                    <div class="job-view-delete-countdown" id="jobViewArchiveJobCountdown" hidden>
                        <p class="job-view-countdown-text">Archiving in</p>
                        <div class="job-view-countdown-number" id="jobViewArchiveJobCountdownNumber">3</div>
                        <p class="job-view-countdown-cancel-hint">Click Cancel to abort</p>
                    </div>
                </div>
                <div class="job-view-modal-footer">
                    <button type="button" class="job-view-modal-btn job-view-modal-btn-cancel" id="jobViewArchiveJobModalCancel">Cancel</button>
                    <button type="button" class="job-view-modal-btn job-view-modal-btn-primary job-view-modal-btn-archive" id="jobViewArchiveJobModalConfirm"><span class="job-view-archive-btn-text">Archive</span></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    @include('lbs.modals.styles')
    <link rel="stylesheet" href="{{ asset('css/lbs-list.css') }}">
    @endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function() {
    var csrfToken = '{{ csrf_token() }}';
    var updateUrl = '{{ route('lbs.job.update', ['id' => $jobId]) }}';
    var uploadFilesUrl = '{{ route('lbs.job.uploadFiles', ['id' => $jobId]) }}';
    var deleteFileUrl = '{{ route('lbs.job.deleteFile', ['id' => $jobId]) }}';
    var archiveJobUrl = '{{ route('lbs.job.archive', ['id' => $jobId]) }}';
    var checkerUploadUrl = '{{ route('lbs.job.checkerUploads', ['id' => $jobId]) }}';
    var runCommentUrl = '{{ route('lbs.job.runComment', ['id' => $jobId]) }}';
    var jobCommentUrl = '{{ route('lbs.job.comment', ['id' => $jobId]) }}';
    var jobViewFilesData = {
        planFiles: @json($planFiles ?? []),
        docFiles: @json($docFiles ?? [])
    };
    var jobViewFileUrlTemplate = @json(route('lbs.job.file', ['id' => $jobId, 'file' => '__FILE__']));
    var currentAddFilesSection = null;
    var currentAddFilesMode = null; // 'plans' | 'documents' | 'checker'
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

            if (title === 'Plans') {
                currentAddFilesSection = 'plans';
                currentAddFilesMode = 'plans';
            } else if (title === 'Documents') {
                currentAddFilesSection = 'documents';
                currentAddFilesMode = 'documents';
            } else if (title === 'Checker Upload Files') {
                currentAddFilesSection = null;
                currentAddFilesMode = 'checker';
            } else {
                currentAddFilesSection = null;
                currentAddFilesMode = null;
            }

            var existingList = document.getElementById('jobViewModalExistingFiles');
            var noFilesEl = document.getElementById('jobViewModalNoFiles');
            if (existingList && noFilesEl) {
                existingList.innerHTML = '';
                var files = currentAddFilesSection === 'plans' ? (jobViewFilesData.planFiles || []) : (currentAddFilesSection === 'documents' ? (jobViewFilesData.docFiles || []) : []);
                if (files.length === 0) {
                    noFilesEl.hidden = false;
                } else {
                    noFilesEl.hidden = true;
                    files.forEach(function(fileName) {
                        var url = jobViewFileUrlTemplate.replace('__FILE__', encodeURIComponent(fileName));
                        var li = document.createElement('li');
                        li.className = 'job-view-modal-file-item';
                        li.innerHTML = '<span class="job-view-modal-file-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 2l5 5h-5V4zm-2 10v4h2v-4h-2zm0-4v2h2v-2h-2z"/></svg></span>' +
                            '<span class="job-view-modal-file-name">' + fileName + '</span>' +
                            '<div class="job-view-modal-file-actions">' +
                            '<a href="' + url + '" class="job-view-modal-file-btn" title="Download" aria-label="Download" download><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg></a>' +
                            '<a href="' + url + '" target="_blank" class="job-view-modal-file-btn" title="View" aria-label="View"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>' +
                            '</div>';
                        existingList.appendChild(li);
                    });
                }
            }

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

    (function bindComplexityStars() {
        var btn = document.querySelector('.job-view-complexity-button');
        if (!btn) return;
        var stars = btn.querySelectorAll('.lbs-star');
        function setStars(value) {
            stars.forEach(function(star, idx) {
                var i = idx + 1;
                if (i <= value) {
                    star.classList.add('lbs-star-filled');
                    star.classList.remove('lbs-star-empty');
                } else {
                    star.classList.add('lbs-star-empty');
                    star.classList.remove('lbs-star-filled');
                }
            });
            btn.setAttribute('data-complexity-rating', String(value));
        }
        function sendComplexity(value) {
            var current = parseInt(btn.getAttribute('data-complexity-rating') || '0', 10) || 0;
            if (current === value) return;
            var formData = new URLSearchParams();
            formData.append('_token', csrfToken);
            formData.append('plan_complexity', String(value));
            fetch(updateUrl, {
                method: 'PUT',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            }).then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: r.ok, data: { message: r.ok ? 'Updated.' : 'Failed to update complexity.' } }; }); }).then(function(result) {
                var msg = (result.data && result.data.message) || (result.ok ? 'Complexity updated.' : 'Failed to update complexity.');
                if (window.showSuccessToast) showSuccessToast(msg);
                if (result.ok) {
                    setStars(value);
                    if (result.data && Array.isArray(result.data.logs) && result.data.logs.length) {
                        var list = document.querySelector('.job-view-activity');
                        if (list) {
                            if (list.children.length === 1 && list.children[0].querySelector('.job-view-activity-text')) list.innerHTML = '';
                            result.data.logs.forEach(function(log) {
                                var li = document.createElement('li');
                                li.className = 'job-view-activity-item';
                                var initial = (log.updated_by || 'L').toString().charAt(0).toUpperCase();
                                var dateText = log.activity_date || '';
                                li.innerHTML =
                                    '<div class=\"job-view-activity-user\">' +
                                        '<span class=\"job-view-activity-avatar\" aria-hidden=\"true\">' + initial + '</span>' +
                                        '<span class=\"job-view-activity-name\">' + (log.updated_by || 'LUNTIAN') + '</span>' +
                                    '</div>' +
                                    '<div class=\"job-view-activity-content\">' +
                                        '<span class=\"job-view-activity-time\">' + dateText + '</span>' +
                                        '<p class=\"job-view-activity-label\">' + (log.activity_type || 'Update') + '</p>' +
                                        (log.activity_description ? '<p class=\"job-view-activity-text\">' + log.activity_description + '</p>' : '') +
                                    '</div>';
                                list.insertBefore(li, list.firstChild);
                            });
                        }
                    }
                    setTimeout(function() { window.location.reload(); }, 1800);
                }
            }).catch(function() {
                if (window.showSuccessToast) showSuccessToast('Failed to update complexity.');
            });
        }
        stars.forEach(function(star, idx) {
            star.style.cursor = 'pointer';
            star.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var value = idx + 1;
                sendComplexity(value);
            });
        });
    })();

    (function handleRunComments() {
        var sendBtn = document.getElementById('runCommentSendBtn');
        var bodyEl = document.getElementById('runCommentBody');
        var list = document.getElementById('runCommentsList');
        if (!sendBtn || !bodyEl || !list) return;
        sendBtn.addEventListener('click', function() {
            var html = bodyEl.innerHTML || '';
            var text = html.replace(/<[^>]*>/g, '').trim();
            if (!text) return;
            sendBtn.disabled = true;
            var formData = new URLSearchParams();
            formData.append('_token', csrfToken);
            formData.append('message', html);
            fetch(runCommentUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            }).then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: false, data: { message: 'Failed to add comment.' } }; }); }).then(function(result) {
                sendBtn.disabled = false;
                var msg = (result.data && result.data.message) || (result.ok ? 'Run comment added.' : 'Failed to add comment.');
                if (window.showSuccessToast) showSuccessToast(msg);
                if (result.ok && result.data && result.data.comment) {
                    var c = result.data.comment;
                    bodyEl.innerHTML = '';
                    if (list.children.length === 1 && list.children[0].querySelector('.job-view-comment-text') && list.children[0].textContent.trim().startsWith('No run comments')) {
                        list.innerHTML = '';
                    }
                    var li = document.createElement('li');
                    li.className = 'job-view-comment-item';
                    var initial = (c.name || 'L').toString().charAt(0).toUpperCase();
                    li.innerHTML =
                        '<div class="job-view-comment-user">' +
                            '<span class="job-view-comment-avatar" aria-hidden="true">' + initial + '</span>' +
                            '<span class="job-view-comment-name">' + (c.name || 'LUNTIAN') + '</span>' +
                        '</div>' +
                        '<div class="job-view-comment-content">' +
                            '<p class="job-view-comment-text">' + (c.message || '') + '</p>' +
                            '<span class="job-view-comment-time">' + (c.created_at || '') + '</span>' +
                        '</div>';
                    list.insertBefore(li, list.firstChild);
                }
            }).catch(function() {
                sendBtn.disabled = false;
                if (window.showSuccessToast) showSuccessToast('Failed to add comment.');
            });
        });
    })();

    (function handleJobComments() {
        var sendBtn = document.getElementById('jobCommentSendBtn');
        var bodyEl = document.getElementById('jobCommentBody');
        var list = document.getElementById('jobCommentsList');
        if (!sendBtn || !bodyEl || !list) return;
        sendBtn.addEventListener('click', function() {
            var html = bodyEl.innerHTML || '';
            var text = html.replace(/<[^>]*>/g, '').trim();
            if (!text) return;
            sendBtn.disabled = true;
            var formData = new URLSearchParams();
            formData.append('_token', csrfToken);
            formData.append('message', html);
            fetch(jobCommentUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString()
            }).then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: false, data: { message: 'Failed to add comment.' } }; }); }).then(function(result) {
                sendBtn.disabled = false;
                var msg = (result.data && result.data.message) || (result.ok ? 'Comment added.' : 'Failed to add comment.');
                if (window.showSuccessToast) showSuccessToast(msg);
                if (result.ok && result.data && result.data.comment) {
                    var c = result.data.comment;
                    bodyEl.innerHTML = '';
                    if (list.children.length === 1 && list.children[0].querySelector('.job-view-comment-text') && list.children[0].textContent.trim().startsWith('No comments')) {
                        list.innerHTML = '';
                    }
                    var li = document.createElement('li');
                    li.className = 'job-view-comment-item';
                    var initial = (c.username || 'L').toString().charAt(0).toUpperCase();
                    li.innerHTML =
                        '<div class="job-view-comment-user">' +
                            '<span class="job-view-comment-avatar" aria-hidden="true">' + initial + '</span>' +
                            '<span class="job-view-comment-name">' + (c.username || 'LUNTIAN') + '</span>' +
                        '</div>' +
                        '<div class="job-view-comment-content">' +
                            '<p class="job-view-comment-text">' + (c.message || '') + '</p>' +
                            '<span class="job-view-comment-time">' + (c.created_at || '') + '</span>' +
                        '</div>';
                    list.insertBefore(li, list.firstChild);
                }
            }).catch(function() {
                sendBtn.disabled = false;
                if (window.showSuccessToast) showSuccessToast('Failed to add comment.');
            });
        });
    })();

    (function() {
        var uploadBtn = document.getElementById('jobViewAddFilesUploadBtn');
        var fileInput = document.getElementById('jobViewAddFilesInput');
        if (!uploadBtn || !fileInput) return;
        uploadBtn.addEventListener('click', function() {
            var files = fileInput.files;
            if (!files || files.length === 0) {
                if (window.showSuccessToast) showSuccessToast('Choose files first.');
                return;
            }
            var formData = new FormData();
            formData.append('_token', csrfToken);
            if (currentAddFilesMode === 'plans' || currentAddFilesMode === 'documents') {
                formData.append('section', currentAddFilesSection);
                for (var i = 0; i < files.length; i++) {
                    formData.append('files[]', files[i]);
                }
            } else if (currentAddFilesMode === 'checker') {
                for (var j = 0; j < files.length; j++) {
                    formData.append('files[]', files[j]);
                }
                var notesBody = document.querySelector('#jobViewModalCheckerNotes .job-view-modal-notes-body');
                if (notesBody && notesBody.innerHTML) {
                    formData.append('notes', notesBody.innerHTML);
                }
            } else {
                if (window.showSuccessToast) showSuccessToast('Please select a valid section.');
                return;
            }
            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';
            var targetUrl = currentAddFilesMode === 'checker' ? checkerUploadUrl : uploadFilesUrl;
            fetch(targetUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            }).then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: false, data: { message: 'Upload failed.' } }; }); }).then(function(result) {
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload';
                var msg = (result.data && result.data.message) || (result.ok ? (currentAddFilesMode === 'checker' ? 'Checker upload saved.' : 'Files added successfully.') : 'Upload failed.');
                if (window.showSuccessToast) showSuccessToast(msg);
                if (result.ok) {
                    closeAddFilesModal();
                    setTimeout(function() { window.location.reload(); }, 1500);
                }
            }).catch(function() {
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload';
                if (window.showSuccessToast) showSuccessToast('Upload failed.');
            });
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
    function closeAllLbsMenus() {
        document.querySelectorAll('.lbs-status-menu').forEach(function(m) { m.hidden = true; });
        document.querySelectorAll('.lbs-initials-menu').forEach(function(m) { m.hidden = true; });
        document.querySelectorAll('[data-status-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
        document.querySelectorAll('[data-initials-trigger]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });
    }
    document.querySelectorAll('[data-initials-wrap]').forEach(function(wrap) {
        var trigger = wrap.querySelector('[data-initials-trigger]');
        var menu = wrap.querySelector('.lbs-initials-menu');
        var role = wrap.getAttribute('data-role');
        if (!trigger || !menu) return;
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!menu.hidden) {
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                return;
            }
            closeAllLbsMenus();
            var rect = this.getBoundingClientRect();
            menu.style.cssText = 'position:fixed;top:' + (rect.bottom + 4) + 'px;left:' + rect.left + 'px;min-width:' + Math.max(rect.width, 70) + 'px;';
            menu.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
        });
        menu.querySelectorAll('.lbs-initials-option').forEach(function(opt) {
            opt.addEventListener('click', function(e) {
                e.stopPropagation();
                var val = this.getAttribute('data-value');
                if (!val) return;
                trigger.textContent = val;
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                var formData = new URLSearchParams();
                formData.append('_token', csrfToken);
                if (role === 'staff') formData.append('staff_id', val); else formData.append('checker_id', val);
                fetch(updateUrl, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                }).then(function(r) {
                    return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: r.ok, data: {} }; });
                }).then(function(result) {
                    var msg = (result.data && result.data.message) || (result.ok ? 'Staff/Checker updated successfully.' : 'Something went wrong.');
                    if (window.showSuccessToast) showSuccessToast(msg);
                    if (result.ok) setTimeout(function() { window.location.reload(); }, 2500);
                }).catch(function() {
                    if (window.showSuccessToast) showSuccessToast('Failed to update.');
                });
            });
        });
    });
    document.querySelectorAll('[data-status-wrap]').forEach(function(wrap) {
        var trigger = wrap.querySelector('[data-status-trigger]');
        var menu = wrap.querySelector('.lbs-status-menu');
        if (!trigger || !menu) return;
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!menu.hidden) {
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                return;
            }
            closeAllLbsMenus();
            var rect = this.getBoundingClientRect();
            menu.style.cssText = 'position:fixed;top:' + (rect.bottom + 4) + 'px;left:' + rect.left + 'px;min-width:' + Math.max(rect.width, 90) + 'px;';
            menu.hidden = false;
            trigger.setAttribute('aria-expanded', 'true');
        });
        menu.querySelectorAll('.lbs-status-option').forEach(function(opt) {
            opt.addEventListener('click', function(e) {
                e.stopPropagation();
                var val = this.getAttribute('data-status-value');
                if (!val) return;
                var badgeClass = 'lbs-badge-' + String(val).toLowerCase().replace(/\s+/g, '-');
                ['lbs-badge-pending', 'lbs-badge-accepted', 'lbs-badge-allocated', 'lbs-badge-awaiting-further-information', 'lbs-badge-completed', 'lbs-badge-for-email-confirmation', 'lbs-badge-processing', 'lbs-badge-for-checking', 'lbs-badge-for-review', 'lbs-badge-revised'].forEach(function(c) { trigger.classList.remove(c); });
                trigger.classList.add(badgeClass);
                trigger.textContent = val;
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                var formData = new URLSearchParams();
                formData.append('_token', csrfToken);
                formData.append('job_status', val);
                fetch(updateUrl, {
                    method: 'PUT',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                }).then(function(r) {
                    return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: r.ok, data: {} }; });
                }).then(function(result) {
                    var msg = (result.data && result.data.message) || (result.ok ? 'Status updated successfully.' : 'Something went wrong.');
                    if (window.showSuccessToast) showSuccessToast(msg);
                    if (result.ok) setTimeout(function() { window.location.reload(); }, 2500);
                }).catch(function() {
                    if (window.showSuccessToast) showSuccessToast('Failed to update status.');
                });
            });
        });
    });
    document.addEventListener('click', closeAllLbsMenus);

    (function deleteFileModal() {
        var overlay = document.getElementById('jobViewDeleteFileModalOverlay');
        var confirmBlock = document.getElementById('jobViewDeleteFileConfirm');
        var countdownBlock = document.getElementById('jobViewDeleteFileCountdown');
        var countdownNumber = document.getElementById('jobViewDeleteFileCountdownNumber');
        var cancelBtn = document.getElementById('jobViewDeleteFileModalCancel');
        var confirmBtn = document.getElementById('jobViewDeleteFileModalConfirm');
        var btnTextEl = confirmBtn && confirmBtn.querySelector('.job-view-delete-btn-text');
        var countdownTimer = null;
        var pendingDelete = null;

        function resetDeleteFileModal() {
            if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
            if (confirmBlock) confirmBlock.hidden = false;
            if (countdownBlock) countdownBlock.hidden = true;
            if (confirmBtn) confirmBtn.disabled = false;
            if (btnTextEl) btnTextEl.textContent = 'Delete';
        }
        function closeDeleteFileModal() {
            if (overlay) overlay.classList.remove('is-open');
            overlay && overlay.setAttribute('aria-hidden', 'true');
            pendingDelete = null;
            resetDeleteFileModal();
        }

        document.addEventListener('click', function(e) {
            var delBtn = e.target.closest('.job-view-file-btn-delete');
            if (delBtn) {
                e.preventDefault();
                var section = delBtn.getAttribute('data-job-file-type');
                var fileName = delBtn.getAttribute('data-job-file-name');
                var listItem = delBtn.closest('.job-view-file-item');
                if (!section || !fileName) return;
                pendingDelete = { section: section, fileName: fileName, listItem: listItem };
                resetDeleteFileModal();
                if (overlay) { overlay.classList.add('is-open'); overlay.setAttribute('aria-hidden', 'false'); }
                return;
            }
            var modalDelBtn = e.target.closest('[data-job-view-modal-delete-file]');
            if (modalDelBtn) {
                var item = modalDelBtn.closest('.job-view-modal-file-item');
                if (!item) return;
                item.remove();
                var list = document.getElementById('jobViewModalExistingFiles');
                var noFiles = document.getElementById('jobViewModalNoFiles');
                if (list && list.children.length === 0 && noFiles) { list.hidden = true; noFiles.hidden = false; }
            }
        });

        if (cancelBtn) cancelBtn.addEventListener('click', closeDeleteFileModal);
        if (overlay) overlay.addEventListener('click', function(e) { if (e.target === overlay) closeDeleteFileModal(); });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay && overlay.classList.contains('is-open')) closeDeleteFileModal();
        });

        if (confirmBtn && confirmBlock && countdownBlock && countdownNumber) {
            confirmBtn.addEventListener('click', function() {
                if (!pendingDelete || countdownTimer) return;
                confirmBlock.hidden = true;
                countdownBlock.hidden = false;
                confirmBtn.disabled = true;
                if (btnTextEl) btnTextEl.textContent = 'Deleting...';
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
                        var section = pendingDelete.section;
                        var fileName = pendingDelete.fileName;
                        var listItem = pendingDelete.listItem;
                        var body = JSON.stringify({ _token: csrfToken, section: section, file_name: fileName });
                        fetch(deleteFileUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                            body: body
                        }).then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: false, data: {} }; });                         }).then(function(result) {
                            var msg = (result.data && result.data.message) || (result.ok ? 'File removed.' : 'Failed to remove file.');
                            if (window.showSuccessToast) showSuccessToast(msg);
                            if (result.ok) {
                                if (listItem) listItem.remove();
                                if (section === 'plans' && jobViewFilesData.planFiles) {
                                    jobViewFilesData.planFiles = (jobViewFilesData.planFiles || []).filter(function(n) { return n !== fileName; });
                                } else if (section === 'documents' && jobViewFilesData.docFiles) {
                                    jobViewFilesData.docFiles = (jobViewFilesData.docFiles || []).filter(function(n) { return n !== fileName; });
                                }
                                var log = result.data && result.data.log;
                                if (log) {
                                    var list = document.querySelector('.job-view-activity');
                                    if (list) {
                                        if (list.children.length === 1 && list.children[0].querySelector('.job-view-activity-text')) list.innerHTML = '';
                                        var li = document.createElement('li');
                                        li.className = 'job-view-activity-item';
                                        var initial = (log.updated_by || 'L').toString().charAt(0).toUpperCase();
                                        var dateText = log.activity_date || '';
                                        li.innerHTML = '<div class="job-view-activity-user"><span class="job-view-activity-avatar" aria-hidden="true">' + initial + '</span><span class="job-view-activity-name">' + (log.updated_by || 'LUNTIAN') + '</span></div><div class="job-view-activity-content"><span class="job-view-activity-time">' + dateText + '</span><p class="job-view-activity-label">' + (log.activity_type || 'Update') + '</p>' + (log.activity_description ? '<p class="job-view-activity-text">' + log.activity_description + '</p>' : '') + '</div>';
                                        list.insertBefore(li, list.firstChild);
                                    }
                                }
                            }
                            closeDeleteFileModal();
                        }).catch(function() {
                            if (window.showSuccessToast) showSuccessToast('Failed to remove file.');
                            closeDeleteFileModal();
                        });
                        return;
                    }
                    countdownNumber.textContent = count;
                    countdownNumber.style.animation = 'none';
                    countdownNumber.offsetHeight;
                    countdownNumber.style.animation = '';
                }, 1000);
            });
        }
    })();

    (function archiveJobModal() {
        var archiveBtn = document.getElementById('jobViewArchiveJobBtn');
        var overlay = document.getElementById('jobViewArchiveJobModalOverlay');
        var confirmBlock = document.getElementById('jobViewArchiveJobConfirm');
        var countdownBlock = document.getElementById('jobViewArchiveJobCountdown');
        var countdownNumber = document.getElementById('jobViewArchiveJobCountdownNumber');
        var cancelBtn = document.getElementById('jobViewArchiveJobModalCancel');
        var confirmBtn = document.getElementById('jobViewArchiveJobModalConfirm');
        var btnTextEl = confirmBtn && confirmBtn.querySelector('.job-view-archive-btn-text');
        var countdownTimer = null;

        function resetArchiveModal() {
            if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
            if (confirmBlock) confirmBlock.hidden = false;
            if (countdownBlock) countdownBlock.hidden = true;
            if (confirmBtn) confirmBtn.disabled = false;
            if (btnTextEl) btnTextEl.textContent = 'Archive';
        }
        function closeArchiveModal() {
            if (overlay) { overlay.classList.remove('is-open'); overlay.setAttribute('aria-hidden', 'true'); }
            resetArchiveModal();
        }

        if (archiveBtn) {
            archiveBtn.addEventListener('click', function() {
                resetArchiveModal();
                if (overlay) { overlay.classList.add('is-open'); overlay.setAttribute('aria-hidden', 'false'); }
            });
        }
        if (cancelBtn) cancelBtn.addEventListener('click', closeArchiveModal);
        if (overlay) overlay.addEventListener('click', function(e) { if (e.target === overlay) closeArchiveModal(); });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay && overlay.classList.contains('is-open')) closeArchiveModal();
        });

        if (confirmBtn && confirmBlock && countdownBlock && countdownNumber) {
            confirmBtn.addEventListener('click', function() {
                if (countdownTimer) return;
                confirmBlock.hidden = true;
                countdownBlock.hidden = false;
                confirmBtn.disabled = true;
                if (btnTextEl) btnTextEl.textContent = 'Archiving...';
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
                        var formData = new URLSearchParams();
                        formData.append('_token', csrfToken);
                        fetch(archiveJobUrl, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: formData.toString()
                        }).then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }).catch(function() { return { ok: false, data: {} }; }); }).then(function(result) {
                            var msg = (result.data && result.data.message) || (result.ok ? 'Job archived.' : 'Failed to archive.');
                            if (window.showSuccessToast) showSuccessToast(msg);
                            var redirect = (result.data && result.data.redirect) || '{{ route('lbs.trash') }}';
                            if (result.ok && redirect) { window.location.href = redirect; return; }
                            closeArchiveModal();
                        }).catch(function() {
                            if (window.showSuccessToast) showSuccessToast('Failed to archive.');
                            closeArchiveModal();
                        });
                        return;
                    }
                    countdownNumber.textContent = count;
                    countdownNumber.style.animation = 'none';
                    countdownNumber.offsetHeight;
                    countdownNumber.style.animation = '';
                }, 1000);
            });
        }
    })();

    function showInlineToast(message) {
        var existing = document.getElementById('jobViewInlineToast');
        if (existing) existing.remove();
        var el = document.createElement('div');
        el.id = 'jobViewInlineToast';
        el.className = 'job-view-inline-toast';
        el.textContent = message;
        document.body.appendChild(el);
        setTimeout(function() {
            el.classList.add('hide');
            setTimeout(function() { el.remove(); }, 350);
        }, 3200);
    }

    // Save handler for Edit modal
    var saveBtn = document.getElementById('jobViewEditSaveBtn');
    if (saveBtn) {
        var saveBtnOriginalHtml = saveBtn.innerHTML;
        saveBtn.addEventListener('click', function () {
            var payload = {};
            if (!editOverlay) return;
            // Determine which form is visible
            if (!formClient.hidden) {
                payload.client_reference = document.getElementById('edit-client-ref')?.value || '';
                payload.job_reference_no = document.getElementById('edit-job-number')?.value || '';
                payload.compliance = document.getElementById('edit-compliance')?.value || '';
                var clientSelect = $('#edit-client-name');
                var clientId = clientSelect.val();
                var clientName = clientSelect.find('option:selected').data('name') || '';
                payload.client_id = clientId || '';
                payload.client_name = clientName;
            } else if (!formJob.hidden) {
                payload.job_status = $('#edit-job-status').val();
                payload.job_address = document.getElementById('edit-job-address')?.value || '';
                payload.priority = $('#edit-priority').val();
                payload.job_type = document.getElementById('edit-job-type')?.value || '';
            } else if (!formNotes.hidden) {
                var notesBody = document.getElementById('jobViewEditNotesBody');
                payload.notes = notesBody ? notesBody.innerHTML : '';
            } else {
                return;
            }

            // loading state + simple animation
            saveBtn.disabled = true;
            saveBtn.classList.add('job-view-modal-btn-loading');
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';

            $.ajax({
                url: updateUrl,
                method: 'PUT',
                data: Object.assign({_token: csrfToken}, payload),
                success: function (res) {
                    var msg = (res && res.message) || 'Job updated successfully.';
                    if (window.showSuccessToast) showSuccessToast(msg);

                    // Realtime append of new activity logs (if any)
                    if (res && Array.isArray(res.logs) && res.logs.length > 0) {
                        var list = document.querySelector('.job-view-activity');
                        if (list) {
                            // Remove "no activity" placeholder if present
                            if (list.children.length === 1 && list.children[0].querySelector('.job-view-activity-text')) {
                                list.innerHTML = '';
                            }
                            res.logs.forEach(function(log) {
                                var li = document.createElement('li');
                                li.className = 'job-view-activity-item';
                                var initial = (log.updated_by || 'L').toString().charAt(0).toUpperCase();
                                var dateText = log.activity_date || '';
                                li.innerHTML =
                                    '<div class="job-view-activity-user">' +
                                        '<span class="job-view-activity-avatar" aria-hidden="true">' + initial + '</span>' +
                                        '<span class="job-view-activity-name">' + (log.updated_by || 'LUNTIAN') + '</span>' +
                                    '</div>' +
                                    '<div class="job-view-activity-content">' +
                                        '<span class="job-view-activity-time">' + dateText + '</span>' +
                                        '<p class="job-view-activity-label">' + (log.activity_type || 'Update') + '</p>' +
                                        (log.activity_description ? '<p class="job-view-activity-text">' + log.activity_description + '</p>' : '') +
                                    '</div>';
                                list.insertBefore(li, list.firstChild);
                            });
                        }
                    }

                    // small loading animation on affected cards, then reload
                    var clientCard = document.getElementById('jobClientCard');
                    var activityCard = document.getElementById('jobActivityCard');
                    if (clientCard) clientCard.classList.add('job-view-card-reloading');
                    if (activityCard) activityCard.classList.add('job-view-card-reloading');

                    closeEditModal();

                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                },
                error: function (xhr) {
                    var msg = 'Failed to save changes. Please try again.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (window.showSuccessToast) showSuccessToast(msg);
                },
                complete: function () {
                    saveBtn.disabled = false;
                    saveBtn.classList.remove('job-view-modal-btn-loading');
                    saveBtn.innerHTML = saveBtnOriginalHtml;
                }
            });
        });
    }
})();
</script>
    <script>
    $(function() {
        $('.job-view-modal .select2-single').select2({ width: '100%', allowClear: false });
    });
    </script>
@endpush
