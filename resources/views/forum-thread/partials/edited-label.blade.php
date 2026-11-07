<button
    type="button"
    class="forum-history-btn cursor-pointer font-medium italic text-slate-500 underline-offset-2 hover:text-emerald-600 hover:underline dark:text-slate-400 dark:hover:text-emerald-400"
    data-history-url="{{ route('forum_thread.history', ['type' => $historyType, 'id' => $historyId], false) }}"
    title="Edited {{ $edits }} {{ $edits === 1 ? 'time' : 'times' }}{{ $editedAt ? ' · last on '.$editedAt->timezone('Asia/Manila')->format('M j, Y g:i A') : '' }} — click to view older versions"
>· Edited</button>
