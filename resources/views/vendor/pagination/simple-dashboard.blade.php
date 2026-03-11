@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="dashboard-pagination">
        <div class="dashboard-pagination-inner">
            <div class="dashboard-pagination-mobile">
                @if ($paginator->onFirstPage())
                    <span class="dashboard-pagination-btn dashboard-pagination-btn-disabled">{{ __('pagination.previous') }}</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="dashboard-pagination-btn">{{ __('pagination.previous') }}</a>
                @endif
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="dashboard-pagination-btn">{{ __('pagination.next') }}</a>
                @else
                    <span class="dashboard-pagination-btn dashboard-pagination-btn-disabled">{{ __('pagination.next') }}</span>
                @endif
            </div>
        </div>
    </nav>
@endif
