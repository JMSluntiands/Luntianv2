@php
    $module = trim((string) ($complexityModule ?? 'lbs'));
    $status = $status ?? '—';
    $statusBg = $statusBg ?? null;
    $statusFg = $statusFg ?? '#fff';
    $statusOptions = $statusOptions ?? [];
    $canEditStatus = (bool) ($canEditStatus ?? false);
    $dueDate1 = $dueDate1 ?? '—';
    $dueDate2 = $dueDate2 ?? '';
    $isOverdue = (bool) ($isOverdue ?? false);
    $complexity = is_numeric($complexity ?? null) ? (int) $complexity : 0;
    $staffId = $staffId ?? '';
    $checkerId = $checkerId ?? '';
    $assignmentStaffCodes = $assignmentStaffCodes ?? [];
    $assignmentCheckerCodes = $assignmentCheckerCodes ?? [];
    $reference = $reference ?? '';
@endphp
<div class="lbs-overflow-panel">
    <div class="lbs-overflow-panel__label">More columns</div>
    <div class="lbs-overflow-panel__grid">
        <div class="lbs-overflow-panel__item">
            <span class="lbs-overflow-panel__key">Staff</span>
            <div class="lbs-overflow-panel__val">
                @include('partials.assignment-initials-cell', ['role' => 'staff', 'current' => $staffId, 'options' => $assignmentStaffCodes])
            </div>
        </div>
        <div class="lbs-overflow-panel__item">
            <span class="lbs-overflow-panel__key">Checker</span>
            <div class="lbs-overflow-panel__val">
                @include('partials.assignment-initials-cell', ['role' => 'checker', 'current' => $checkerId, 'options' => $assignmentCheckerCodes])
            </div>
        </div>
        <div class="lbs-overflow-panel__item">
            <span class="lbs-overflow-panel__key">Status</span>
            <div class="lbs-overflow-panel__val">
                @include('partials.lbs-inline-status-cell', [
                    'status' => $status,
                    'statusBg' => $statusBg,
                    'statusFg' => $statusFg,
                    'statusOptions' => $statusOptions,
                    'canEditStatus' => $canEditStatus,
                    'reference' => $reference,
                ])
            </div>
        </div>
        <div class="lbs-overflow-panel__item">
            <span class="lbs-overflow-panel__key">Due Date</span>
            <div class="lbs-overflow-panel__val {{ $isOverdue ? 'lbs-overflow-panel__val--overdue' : '' }}">
                <span>{{ trim($dueDate1.' '.$dueDate2) !== '' ? trim($dueDate1.' '.$dueDate2) : '—' }}</span>
                @if($isOverdue)
                    <span class="lbs-overflow-panel__overdue">Overdue</span>
                @endif
            </div>
        </div>
        <div class="lbs-overflow-panel__item">
            <span class="lbs-overflow-panel__key">Complexity</span>
            <div class="lbs-overflow-panel__val">
                @include('partials.lbs-inline-complexity-cell', ['rating' => $complexity, 'complexityModule' => $module])
            </div>
        </div>
    </div>
</div>
