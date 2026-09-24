@php
    $rating = is_numeric($rating ?? null) ? (int) $rating : 0;
    $rating = max(0, min(5, $rating));
    $module = trim((string) ($complexityModule ?? 'lbs'));
    if ($module === '') {
        $module = 'lbs';
    }
    $canEdit = $canEditComplexity ?? null;
    if ($canEdit === null) {
        try {
            $canEdit = \App\Models\RolePermission::userMayAccessRoute('job_view.'.$module.'.button.edit.complexity');
        } catch (\Throwable) {
            $canEdit = true;
        }
    }
    $canEdit = (bool) $canEdit;
@endphp
@if($canEdit)
    <button
        type="button"
        class="lbs-inline-complexity inline-flex items-center gap-0.5 rounded-md border-0 bg-transparent p-0.5 outline-none ring-emerald-500/30 transition-colors hover:bg-slate-100 focus-visible:ring-2 dark:hover:bg-slate-700/50"
        data-complexity-select
        data-complexity-rating="{{ $rating }}"
        data-prev="{{ $rating }}"
        title="Click a star to set complexity (1–5)"
        aria-label="Plan complexity {{ $rating }} of 5, click a star to change"
    >
        @for ($i = 1; $i <= 5; $i++)
            <span
                class="lbs-star {{ $i <= $rating ? 'lbs-star-filled' : 'lbs-star-empty' }} inline-flex shrink-0"
                data-star-value="{{ $i }}"
                role="presentation"
            >
                <svg class="lbs-star-solid h-4 w-4 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                <svg class="lbs-star-outline h-4 w-4 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </span>
        @endfor
    </button>
@else
    <span class="lbs-stars inline-flex items-center" data-rating="{{ $rating }}" aria-label="{{ $rating }} out of 5">
        @include('lbs.partials.stars', ['rating' => $rating])
    </span>
@endif
