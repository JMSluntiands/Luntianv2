@php
    $statusLabel = (string) ($status ?? '');
    $statusBg = $statusBg ?? null;
    $statusFg = $statusFg ?? \App\Models\Status::DEFAULT_FONT_COLOR;
    $statusOptions = $statusOptions ?? [];
    $canEditStatus = (bool) ($canEditStatus ?? count($statusOptions) > 0);
    $reference = (string) ($reference ?? '');
@endphp
@if($canEditStatus && count($statusOptions) > 0)
    <div class="lbs-status-wrap relative inline-block" data-status-wrap>
        <button
            type="button"
            class="lbs-badge lbs-status-trigger inline-block rounded-md border-0 px-2 py-1 text-xs font-semibold leading-tight cursor-pointer hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-600/40 focus:ring-offset-0 dark:focus:ring-blue-500/40"
            @if($statusBg)
                style="background-color: {{ $statusBg }}; color: {{ $statusFg }};"
            @endif
            data-status-trigger
            aria-haspopup="true"
            aria-expanded="false"
            @if($reference !== '')
                data-reference="{{ $reference }}"
            @endif
        >{{ $statusLabel }}</button>
        <div class="lbs-status-menu fixed z-[9999] flex min-w-[90px] flex-col gap-0.5 rounded-lg border border-slate-700 bg-slate-800 p-1 shadow-lg dark:border-slate-700 dark:bg-slate-800" role="menu" hidden>
            @foreach($statusOptions as $opt)
                <button type="button" role="menuitem" class="lbs-status-option block w-full rounded-md border-0 bg-transparent px-2.5 py-1.5 text-left text-xs font-medium text-slate-200 hover:bg-white/10 dark:text-slate-200 dark:hover:bg-white/10" data-status-value="{{ $opt }}">{{ $opt }}</button>
            @endforeach
        </div>
    </div>
@else
    <span
        class="lbs-badge lbs-status-badge-readonly inline-block cursor-default rounded-md px-2 py-1 text-xs font-semibold opacity-95"
        @if($statusBg)
            style="background-color: {{ $statusBg }}; color: {{ $statusFg }};"
        @endif
        aria-disabled="true"
    >{{ $statusLabel }}</span>
@endif
