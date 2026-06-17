@php
    $role = (string) ($role ?? 'staff');
    $current = strtoupper(trim((string) ($current ?? '')));
    $display = $current !== '' ? $current : '--';
    $options = collect($options ?? [])
        ->map(fn ($code) => strtoupper(trim((string) (is_object($code) ? ($code->unique_code ?? '') : $code))))
        ->filter()
        ->unique()
        ->values();

    if ($current !== '' && ! $options->contains($current)) {
        $options->prepend($current);
    }
@endphp
<div class="lbs-initials-wrap relative inline-block" data-initials-wrap data-role="{{ $role }}">
    <button type="button" class="lbs-initials lbs-initials-trigger inline-block rounded-md border border-slate-300 bg-slate-50 px-2 py-1 text-xs font-semibold text-slate-800 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200" data-initials-trigger aria-haspopup="true" aria-expanded="false">{{ $display }}</button>
    <div class="lbs-initials-menu fixed z-[9999] flex min-w-[70px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu" hidden>
        @forelse($options as $code)
            <button type="button" role="menuitem" class="lbs-initials-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-value="{{ $code }}">{{ $code }}</button>
        @empty
            <span class="block px-2.5 py-1.5 text-xs text-slate-400">—</span>
        @endforelse
    </div>
</div>
