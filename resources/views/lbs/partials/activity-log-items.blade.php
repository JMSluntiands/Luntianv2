@foreach($activityLogs as $log)
    @php
        $date = \Carbon\Carbon::parse($log->activity_date, 'Asia/Manila');
    @endphp
    <li class="job-view-activity-item">
        <div class="job-view-activity-user">
            <span class="job-view-activity-avatar" aria-hidden="true">
                {{ strtoupper(substr($log->updated_by ?? 'L', 0, 1)) }}
            </span>
            <span class="job-view-activity-name">{{ $log->updated_by ?? 'LUNTIAN' }}</span>
        </div>
        <div class="job-view-activity-content">
            <span class="job-view-activity-time">{{ $date->format('M d, Y h:i A') }}</span>
            <p class="job-view-activity-label">{{ $log->activity_type ?? 'Update' }}</p>
            @if(!empty($log->activity_description))
                <p class="job-view-activity-text">{!! nl2br(e($log->activity_description)) !!}</p>
            @endif
        </div>
    </li>
@endforeach

