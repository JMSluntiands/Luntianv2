@php
    $priorityText = trim((string) ($priority ?? ''));
    $priorityBg = $priorityBg ?? null;
    $priorityOptions = collect($priorityOptions ?? [])
        ->map(fn ($name) => trim((string) $name))
        ->filter()
        ->unique()
        ->values();

    if ($priorityText !== '' && ! $priorityOptions->contains($priorityText)) {
        $priorityOptions->prepend($priorityText);
    }
@endphp
@if($priorityOptions->isNotEmpty())
    <select
        class="lbs-priority-select"
        data-priority-select
        data-prev="{{ $priorityText }}"
        aria-label="Priority"
        title="Change priority"
        @if($priorityBg) style="background-color: {{ $priorityBg }};" @endif
    >
        @if($priorityText === '')
            <option value="" selected>—</option>
        @endif
        @foreach($priorityOptions as $opt)
            <option value="{{ $opt }}" @selected($opt === $priorityText)>{{ $opt }}</option>
        @endforeach
    </select>
@else
    <span
        class="lbs-priority inline-block whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold"
        @if($priorityBg) style="background-color: {{ $priorityBg }};" @endif
    >{{ $priorityText !== '' ? $priorityText : '—' }}</span>
@endif
