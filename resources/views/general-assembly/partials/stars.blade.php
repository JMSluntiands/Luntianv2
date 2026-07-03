@php
    $rating = (int) ($rating ?? 0);
    $rating = max(0, min(5, $rating));
@endphp
<span class="inline-flex items-center gap-0.5" role="img">
    @for ($i = 1; $i <= 5; $i++)
        @if ($i <= $rating)
            <svg class="h-4 w-4 shrink-0 text-amber-400 dark:text-amber-400" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        @else
            <svg class="h-4 w-4 shrink-0 text-slate-500 opacity-80 dark:text-slate-500" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        @endif
    @endfor
</span>
