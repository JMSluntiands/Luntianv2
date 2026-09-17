@php
    $priorityText = trim((string) ($priority ?? ''));
    $priorityBg = $priorityBg ?? null;
    $priorityOptions = collect($priorityOptions ?? [])
        ->map(fn ($name) => trim((string) $name))
        ->filter()
        ->unique()
        ->values();

    if ($priorityOptions->isEmpty()) {
        try {
            $priorityOptions = collect(\App\Models\Priority::optionsForSelect());
        } catch (\Throwable) {
            $priorityOptions = collect();
        }
    }

    if ($priorityText !== '' && ! $priorityOptions->contains($priorityText)) {
        $priorityOptions->prepend($priorityText);
    }

    // Always show a select when we have a current value or any options.
    $showSelect = $priorityOptions->isNotEmpty();
@endphp
@if($showSelect)
    <select
        class="lbs-priority-select"
        data-priority-select
        data-prev="{{ $priorityText }}"
        aria-label="Priority"
        title="Change priority"
        @if($priorityBg) style="--lbs-select-bg: {{ $priorityBg }};" @endif
    >
        @if($priorityText === '')
            <option value="" selected>—</option>
        @endif
        @foreach($priorityOptions as $opt)
            <option value="{{ $opt }}" @selected($opt === $priorityText)>{{ $opt }}</option>
        @endforeach
    </select>
@else
    <span class="lbs-priority inline-block whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold">—</span>
@endif
