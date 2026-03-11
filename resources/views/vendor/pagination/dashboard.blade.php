@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="dashboard-pagination">
        <div class="dashboard-pagination-inner">
            {{-- Mobile: Previous / Next only --}}
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

            {{-- Desktop: Info + Full nav --}}
            <div class="dashboard-pagination-desktop">
                <div class="dashboard-pagination-info">
                    <p class="dashboard-pagination-info-text">
                        {!! __('Showing') !!}
                        @if ($paginator->firstItem())
                            <span class="dashboard-pagination-info-num">{{ $paginator->firstItem() }}</span>
                            {!! __('to') !!}
                            <span class="dashboard-pagination-info-num">{{ $paginator->lastItem() }}</span>
                        @else
                            {{ $paginator->count() }}
                        @endif
                        {!! __('of') !!}
                        <span class="dashboard-pagination-info-num">{{ $paginator->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>
                <div class="dashboard-pagination-nav">
                    {{-- Previous --}}
                    @if ($paginator->onFirstPage())
                        <span class="dashboard-pagination-nav-btn dashboard-pagination-nav-btn-disabled dashboard-pagination-nav-prev" aria-label="{{ __('pagination.previous') }}">
                            <svg class="dashboard-pagination-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="dashboard-pagination-nav-btn dashboard-pagination-nav-prev" aria-label="{{ __('pagination.previous') }}">
                            <svg class="dashboard-pagination-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </a>
                    @endif

                    {{-- Page numbers --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="dashboard-pagination-ellipsis">{{ $element }}</span>
                        @endif
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="dashboard-pagination-nav-btn dashboard-pagination-nav-btn-active" aria-current="page">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="dashboard-pagination-nav-btn" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="dashboard-pagination-nav-btn dashboard-pagination-nav-next" aria-label="{{ __('pagination.next') }}">
                            <svg class="dashboard-pagination-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                        </a>
                    @else
                        <span class="dashboard-pagination-nav-btn dashboard-pagination-nav-btn-disabled dashboard-pagination-nav-next" aria-label="{{ __('pagination.next') }}">
                            <svg class="dashboard-pagination-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </nav>
@endif
