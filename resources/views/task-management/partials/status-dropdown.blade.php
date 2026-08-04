@if($canManage)
    <div class="relative inline-block" data-status-cell>
        <button
            type="button"
            class="task-status-btn inline-flex cursor-pointer items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium transition-shadow hover:ring-2 hover:ring-slate-200 dark:hover:ring-slate-600 {{ $meta['pill'] }}"
            data-status-toggle
            data-current-status="{{ $task->status }}"
            aria-haspopup="listbox"
            aria-expanded="false"
        >
            <span class="h-1.5 w-1.5 rounded-full {{ $meta['dot'] }}" data-status-dot></span>
            <span data-status-label>{{ $meta['label'] }}</span>
            <svg class="h-3 w-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <ul class="absolute left-0 z-30 mt-1 hidden min-w-[10.5rem] overflow-hidden rounded-lg border border-slate-200 bg-white py-1 shadow-lg dark:border-slate-600 dark:bg-slate-800" role="listbox" data-status-menu>
            @foreach($statusOptions as $value => $opt)
                <li role="option">
                    <button
                        type="button"
                        class="flex w-full cursor-pointer items-center gap-2 px-3 py-1.5 text-left text-xs font-medium text-slate-700 hover:bg-slate-50 dark:text-slate-200 dark:hover:bg-slate-700/60"
                        data-status-option
                        data-status="{{ $value }}"
                        data-label="{{ $opt['label'] }}"
                        data-pill="{{ $opt['pill'] }}"
                        data-dot="{{ $opt['dot'] }}"
                    >
                        <span class="h-1.5 w-1.5 rounded-full {{ $opt['dot'] }}"></span>
                        {{ $opt['label'] }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
@else
    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $meta['pill'] }}">
        <span class="h-1.5 w-1.5 rounded-full {{ $meta['dot'] }}"></span>
        {{ $meta['label'] }}
    </span>
@endif
