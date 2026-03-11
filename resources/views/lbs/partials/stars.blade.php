@php
    $rating = (int) ($rating ?? 0);
    $rating = max(0, min(5, $rating));
@endphp
<span class="lbs-stars-inner" role="img">
    @for ($i = 1; $i <= 5; $i++)
        @if ($i <= $rating)
            <svg class="lbs-star lbs-star-filled" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        @else
            <svg class="lbs-star lbs-star-empty" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        @endif
    @endfor
</span>
