@php
    $selectedValue = strtoupper(trim((string) ($selected ?? '')));
    $showPlaceholder = (bool) ($includeSelectPlaceholder ?? false);
    $showGm = (bool) ($includeGm ?? true);
@endphp

@if($showPlaceholder)
    <option value="" @selected($selectedValue === '')>{{ $placeholderLabel ?? 'Select user' }}</option>
@endif

@if($showGm)
    <option value="GM" @selected($selectedValue === 'GM')>GM</option>
@endif

@foreach($assignmentUsers ?? [] as $user)
    @php
        if (is_object($user)) {
            $code = trim((string) ($user->unique_code ?? ''));
            $label = method_exists($user, 'assignmentOptionLabel')
                ? $user->assignmentOptionLabel()
                : $code;
            $searchText = strtolower(trim($code . ' ' . ($user->username ?? '') . ' ' . ($user->fullname ?? '')));
        } else {
            $code = trim((string) $user);
            $label = $code;
            $searchText = strtolower($code);
        }
    @endphp
    @if($code !== '' && (strtoupper($code) !== 'GM' || ! $showGm))
        <option
            value="{{ $code }}"
            data-search="{{ $searchText }}"
            @selected(strtoupper($code) === $selectedValue)
        >{{ $label }}</option>
    @endif
@endforeach

@if(!empty($preserveSelected) && $selectedValue !== '' && $selectedValue !== 'GM')
    @php
        $existingCodes = collect($assignmentUsers ?? [])
            ->map(fn ($u) => strtoupper(trim((string) (is_object($u) ? ($u->unique_code ?? '') : $u))))
            ->filter()
            ->all();
    @endphp
    @if(!in_array($selectedValue, $existingCodes, true))
        <option value="{{ $selected ?? '' }}" selected>{{ $selected ?? '' }}</option>
    @endif
@endif
