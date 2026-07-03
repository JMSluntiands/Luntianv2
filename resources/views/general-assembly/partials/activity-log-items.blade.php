@foreach($activityLogs as $log)
    @php
        $date = \Carbon\Carbon::parse($log->activity_date, 'Asia/Manila');
        $type = trim($log->activity_type ?? '');
        $isRich = in_array($type, ['Run comment', 'Comment', 'Checker upload'], true);
    @endphp
    <li class="flex gap-3 rounded-lg border border-slate-100 bg-slate-50/50 p-3 dark:border-slate-700 dark:bg-slate-800/30">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-500 text-xs font-semibold text-white" aria-hidden="true">{{ strtoupper(substr($log->updated_by ?? 'L', 0, 1)) }}</span>
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $log->updated_by ?? 'LUNTIAN' }}</span>
                @if(!empty($userRoleMap[$log->updated_by ?? '']))
                    <span class="rounded bg-slate-200 px-1.5 py-0.5 text-xs font-medium text-slate-600 dark:bg-slate-600 dark:text-slate-300">{{ strtoupper($userRoleMap[$log->updated_by]) }}</span>
                @endif
            </div>
            <span class="block text-xs text-slate-500 dark:text-slate-400">{{ $date->format('M d, Y h:i A') }}</span>
            <p class="mt-0.5 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $log->activity_type ?? 'Update' }}</p>
            <div class="prose prose-sm mt-1 max-w-none text-slate-700 dark:prose-invert dark:text-slate-300">
                @if($isRich)
                    {!! $log->activity_description !!}
                @else
                    {!! !empty(trim($log->activity_description ?? '')) ? nl2br(e($log->activity_description)) : '—' !!}
                @endif
            </div>
        </div>
    </li>
@endforeach

