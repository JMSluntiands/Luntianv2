@php
    $statusLabel = trim((string) ($status ?? ''));
    $statusBg = $statusBg ?? null;
    $statusFg = $statusFg ?? \App\Models\Status::DEFAULT_FONT_COLOR;
    $statusClass = trim((string) ($statusClass ?? ''));
    $statusOptions = collect($statusOptions ?? [])
        ->map(fn ($opt) => trim((string) $opt))
        ->filter()
        ->unique()
        ->values();
    $reference = (string) ($reference ?? '');

    $selectOptions = collect();
    if ($statusLabel !== '') {
        $selectOptions->push($statusLabel);
    }
    $selectOptions = $selectOptions->merge($statusOptions)->filter()->unique()->values();

    // Prefer explicit flag; otherwise editable when there is at least a current status.
    $canEditStatus = (bool) ($canEditStatus ?? $selectOptions->isNotEmpty());
@endphp
@if($canEditStatus && $selectOptions->isNotEmpty())
    <select
        class="lbs-status-select{{ $statusClass !== '' ? ' '.$statusClass : '' }}"
        data-status-select
        data-prev="{{ $statusLabel }}"
        @if($reference !== '') data-reference="{{ $reference }}" @endif
        aria-label="Status"
        title="Change status"
        @if($statusBg) style="--lbs-select-bg: {{ $statusBg }}; --lbs-select-fg: {{ $statusFg }};" @endif
    >
        @foreach($selectOptions as $opt)
            <option value="{{ $opt }}" @selected($opt === $statusLabel)>{{ $opt }}</option>
        @endforeach
    </select>
@else
    <span
        class="lbs-badge lbs-status-badge-readonly inline-block cursor-default rounded-md px-2 py-1 text-xs font-semibold opacity-95{{ $statusClass !== '' ? ' '.$statusClass : '' }}"
        @if($statusBg)
            style="background-color: {{ $statusBg }}; color: {{ $statusFg }};"
        @endif
        aria-disabled="true"
    >{{ $statusLabel !== '' ? $statusLabel : '—' }}</span>
@endif
