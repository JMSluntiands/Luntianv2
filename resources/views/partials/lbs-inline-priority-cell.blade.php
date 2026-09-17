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

    // Resolve color map for options (so select + change keep badge colors).
    $priorityColorMap = [];
    try {
        $priorityColorMap = \App\Models\Priority::query()
            ->whereNotNull('name')
            ->pluck('color', 'name')
            ->map(fn ($c) => $c ? trim((string) $c) : null)
            ->filter()
            ->all();
    } catch (\Throwable) {
        $priorityColorMap = [];
    }

    if (! $priorityBg && $priorityText !== '') {
        $priorityBg = $priorityColorMap[$priorityText] ?? null;
    }

    $showSelect = $priorityOptions->isNotEmpty();
@endphp
@if($showSelect)
    <select
        class="lbs-priority-select"
        data-priority-select
        data-prev="{{ $priorityText }}"
        data-priority-colors='@json($priorityColorMap)'
        aria-label="Priority"
        title="Change priority"
        @if($priorityBg) style="background-color: {{ $priorityBg }};" @endif
    >
        @if($priorityText === '')
            <option value="" selected>—</option>
        @endif
        @foreach($priorityOptions as $opt)
            <option
                value="{{ $opt }}"
                @selected($opt === $priorityText)
                @if(!empty($priorityColorMap[$opt])) data-color="{{ $priorityColorMap[$opt] }}" @endif
            >{{ $opt }}</option>
        @endforeach
    </select>
@else
    <span
        class="lbs-priority inline-block whitespace-nowrap rounded-md px-2 py-1 text-xs font-semibold"
        @if($priorityBg) style="background-color: {{ $priorityBg }};" @endif
    >—</span>
@endif
