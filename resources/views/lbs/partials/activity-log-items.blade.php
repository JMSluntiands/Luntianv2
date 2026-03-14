@foreach($activityLogs as $log)
    @php
        $date = \Carbon\Carbon::parse($log->activity_date, 'Asia/Manila');
    @endphp
    <li class="job-view-activity-item">
        <div class="job-view-activity-user">
            <span class="job-view-activity-avatar" aria-hidden="true">
                {{ strtoupper(substr($log->updated_by ?? 'L', 0, 1)) }}
            </span>
            <div class="job-view-activity-user-meta">
                <span class="job-view-activity-name">{{ $log->updated_by ?? 'LUNTIAN' }}</span>
                @if(!empty($userRoleMap[$log->updated_by ?? '']))
                    <span class="job-view-activity-code">{{ strtoupper($userRoleMap[$log->updated_by]) }}</span>
                @endif
            </div>
        </div>
        <div class="job-view-activity-content">
            <span class="job-view-activity-time">{{ $date->format('M d, Y h:i A') }}</span>
            <p class="job-view-activity-label">{{ $log->activity_type ?? 'Update' }}</p>
            @php
                $type = trim($log->activity_type ?? '');
                $isRich = in_array($type, ['Run comment', 'Comment', 'Checker upload'], true);
            @endphp
            <p class="job-view-activity-text">
                @if($isRich)
                    {!! $log->activity_description !!}
                @else
                    {!! !empty(trim($log->activity_description ?? '')) ? nl2br(e($log->activity_description)) : '—' !!}
                @endif
            </p>
        </div>
    </li>
@endforeach

