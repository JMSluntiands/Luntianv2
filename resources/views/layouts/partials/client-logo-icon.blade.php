@php
    $iconClass = $class ?? 'nav-icon h-5 w-5 flex-shrink-0 opacity-90';
    $logoUrl = \App\Support\ClientLogos::url($key);
    $label = $label ?? null;
@endphp
@if($logoUrl)
<img src="{{ $logoUrl }}" alt="{{ $label ?? '' }}" title="{{ $label ?? '' }}" class="{{ str_replace(['h-5', 'w-5'], ['h-6', 'w-auto max-w-[9.5rem]'], $iconClass) }} client-logo-icon object-contain object-left dark:rounded-sm dark:bg-white dark:px-1 dark:py-px" loading="lazy" decoding="async">
@if($label !== null)<span class="sr-only">{{ $label }}</span>@endif
@else
<svg class="{{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
@if($label !== null){{ $label }}@endif
@endif
