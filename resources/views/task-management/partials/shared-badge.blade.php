@if(!$isJob && ($task->visibility ?? '') === \App\Models\Task::VISIBILITY_SHARED)
    @php $memberCodes = $task->member_codes ?? []; @endphp
    <span class="mt-1 inline-flex max-w-full items-center gap-1 rounded-md bg-violet-600 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white"
          title="{{ $memberCodes ? 'Members: '.implode(', ', $memberCodes) : 'Shared' }}">
        Shared
        @if($memberCodes)
            <span class="truncate font-medium normal-case opacity-90">· {{ implode(', ', array_slice($memberCodes, 0, 4)) }}{{ count($memberCodes) > 4 ? ' +'.(count($memberCodes) - 4) : '' }}</span>
        @endif
    </span>
@endif
