@php
    $role = (string) ($role ?? 'staff');
    $normalizeUpper = in_array($role, ['staff', 'checker'], true);
    $rawCurrent = trim((string) ($current ?? ''));
    $current = $normalizeUpper ? strtoupper($rawCurrent) : $rawCurrent;
    $display = $current !== '' ? $current : '--';
    $options = collect($options ?? [])
        ->map(function ($code) use ($normalizeUpper) {
            $value = trim((string) (is_object($code) ? ($code->unique_code ?? '') : $code));

            return $normalizeUpper ? strtoupper($value) : $value;
        })
        ->filter()
        ->unique()
        ->values();

    if ($current !== '' && ! $options->contains($current)) {
        $options->prepend($current);
    }
@endphp
<div class="lbs-initials-wrap relative inline-block" data-initials-wrap data-role="{{ $role }}">
    <select
        class="lbs-initials-select"
        data-initials-select
        data-role="{{ $role }}"
        data-prev="{{ $current }}"
        aria-label="{{ ucfirst($role) }}"
        title="Change {{ $role }}"
    >
        @if($current === '')
            <option value="" selected>--</option>
        @endif
        @forelse($options as $code)
            <option value="{{ $code }}" @selected($code === $current)>{{ $code }}</option>
        @empty
            <option value="{{ $current }}" selected>{{ $display }}</option>
        @endforelse
    </select>
</div>
