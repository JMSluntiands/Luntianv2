@php
    $mentionMap = [];
    $addMention = function ($user, string $role) use (&$mentionMap): void {
        if (is_object($user)) {
            $code = strtoupper(trim((string) ($user->unique_code ?? '')));
            $name = trim((string) ($user->fullname ?? ''));
            if ($name === '') {
                $name = trim((string) ($user->username ?? ''));
            }
        } else {
            $code = strtoupper(trim((string) $user));
            $name = '';
        }
        if ($code === '') {
            return;
        }
        if ($name === '') {
            $name = $code;
        }
        if (! isset($mentionMap[$code])) {
            $mentionMap[$code] = ['code' => $code, 'name' => $name, 'roles' => []];
        } elseif ($mentionMap[$code]['name'] === $code && $name !== $code) {
            $mentionMap[$code]['name'] = $name;
        }
        if (! in_array($role, $mentionMap[$code]['roles'], true)) {
            $mentionMap[$code]['roles'][] = $role;
        }
    };
    foreach ($assignmentStaffUsers ?? [] as $staffUser) {
        $addMention($staffUser, 'Staff');
    }
    foreach ($assignmentCheckerUsers ?? [] as $checkerUser) {
        $addMention($checkerUser, 'Checker');
    }
    if (isset($job)) {
        $addMention($job->staff_id ?? '', 'Staff');
        $addMention($job->checker_id ?? '', 'Checker');
    }
    $mentionPeople = array_values($mentionMap);
    usort($mentionPeople, function (array $a, array $b): int {
        $aStaff = in_array('Staff', $a['roles'], true) ? 0 : 1;
        $bStaff = in_array('Staff', $b['roles'], true) ? 0 : 1;
        if ($aStaff !== $bStaff) {
            return $aStaff <=> $bStaff;
        }

        return strcasecmp($a['name'], $b['name']);
    });
    $viewerId = (int) session('user_id', 0);
    $viewerCode = $viewerId > 0
        ? strtoupper(trim((string) (\App\Models\User::query()->where('id', $viewerId)->value('unique_code') ?? '')))
        : '';
    if ($viewerId > 0 && $viewerCode !== '' && !empty($jobId) && \Illuminate\Support\Facades\Schema::hasTable('job_mention_reads')) {
        \App\Services\AllocatedJobsTaskFeed::ensureMentionSeenColumn();
        $mentionFamily = !empty($isBphView) ? 'bph' : 'jobs';
        $seenComment = '';
        foreach (\App\Services\AllocatedJobsTaskFeed::taggedMentions() as $mentionRow) {
            if (($mentionRow->family ?? '') === $mentionFamily
                && (int) ($mentionRow->job_id ?? 0) === (int) $jobId
                && strtoupper((string) ($mentionRow->tagged ?? '')) === $viewerCode) {
                $seenComment = (string) ($mentionRow->comment_anchor ?? '');
                break;
            }
        }
        if ($seenComment !== '') {
            $mentionReadNow = now();
            \Illuminate\Support\Facades\DB::table('job_mention_reads')->updateOrInsert(
                [
                    'user_id' => $viewerId,
                    'family' => $mentionFamily,
                    'job_id' => (int) $jobId,
                    'tagged_code' => $viewerCode,
                ],
                [
                    'seen_comment' => $seenComment,
                    'read_at' => $mentionReadNow,
                    'updated_at' => $mentionReadNow,
                    'created_at' => $mentionReadNow,
                ]
            );
        }
    }
    if ($viewerId > 0 && !empty($jobId) && \Illuminate\Support\Facades\Schema::hasTable('discussion_reply_notifications')) {
        \Illuminate\Support\Facades\DB::table('discussion_reply_notifications')
            ->where('user_id', $viewerId)
            ->where('job_id', (int) $jobId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
    $discussionReplies = [];
    if (!empty($jobId) && \Illuminate\Support\Facades\Schema::hasTable('discussion_replies')) {
        $discussionReplies = \Illuminate\Support\Facades\DB::table('discussion_replies')
            ->where('job_id', (int) $jobId)
            ->orderBy('id')
            ->get(['source', 'parent_id', 'author_name', 'message', 'created_at'])
            ->all();
    }
@endphp
<style>
    .job-mention {
        display: inline;
        font-weight: 600;
        color: #2563eb;
    }
    .job-comment-image {
        display: block;
        max-width: min(100%, 420px);
        height: auto;
        margin-top: 8px;
        border-radius: 8px;
    }
    .job-comment-image-draft {
        position: relative;
        display: inline-block;
        max-width: min(100%, 420px);
        margin-top: 8px;
    }
    .job-comment-image-draft .job-comment-image {
        margin-top: 0;
    }
    .job-comment-image-remove {
        position: absolute;
        top: 8px;
        right: 8px;
        border: 0;
        border-radius: 6px;
        padding: 4px 8px;
        background: rgba(15, 23, 42, 0.86);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.2;
        cursor: pointer;
    }
    .job-comment-image-remove:hover {
        background: #b91c1c;
    }
    .job-comment-image-loading {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 6px 0;
        padding: 6px 10px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
    }
    .job-comment-image-loading::before {
        content: '';
        width: 14px;
        height: 14px;
        border: 2px solid #cbd5e1;
        border-top-color: #2563eb;
        border-radius: 50%;
        animation: job-comment-image-spin 0.7s linear infinite;
    }
    @keyframes job-comment-image-spin {
        to { transform: rotate(360deg); }
    }
    html[data-theme="dark"] .job-comment-image-loading {
        background: #1e293b;
        color: #cbd5e1;
    }
    html[data-theme="dark"] .job-mention {
        color: #93c5fd;
    }
    .job-mention-menu {
        position: fixed;
        z-index: 80;
        display: none;
        min-width: 220px;
        max-width: 280px;
        max-height: 240px;
        overflow: auto;
        margin: 0;
        padding: 4px;
        list-style: none;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
    }
    .job-mention-menu.is-open {
        display: block;
    }
    html[data-theme="dark"] .job-mention-menu {
        border-color: #475569;
        background: #1e293b;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.35);
    }
    .job-mention-item {
        display: block;
        width: 100%;
        margin: 0;
        padding: 8px 10px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        text-align: left;
        cursor: pointer;
        color: #0f172a;
    }
    .job-mention-item.is-active,
    .job-mention-item:hover {
        background: #eff6ff;
    }
    html[data-theme="dark"] .job-mention-item {
        color: #e2e8f0;
    }
    html[data-theme="dark"] .job-mention-item.is-active,
    html[data-theme="dark"] .job-mention-item:hover {
        background: #1e3a5f;
    }
    .job-mention-name {
        display: block;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.2;
    }
    .job-mention-code {
        display: block;
        margin-top: 2px;
        font-size: 11px;
        font-weight: 500;
        color: #64748b;
    }
    html[data-theme="dark"] .job-mention-code {
        color: #94a3b8;
    }
    .job-mention-empty {
        padding: 8px 10px;
        font-size: 13px;
        color: #64748b;
    }
    .job-comment-reply {
        display: inline-flex;
        margin-top: 6px;
        padding: 0;
        border: 0;
        background: transparent;
        font-size: 12px;
        font-weight: 600;
        color: #2563eb;
        cursor: pointer;
    }
    .job-comment-reply:hover {
        text-decoration: underline;
    }
    html[data-theme="dark"] .job-comment-reply {
        color: #93c5fd;
    }
    .job-thread {
        margin-top: 8px;
    }
    .job-thread-summary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        padding: 2px 0;
        border: 0;
        background: transparent;
        cursor: pointer;
        color: #1264a3;
        font-size: 13px;
        font-weight: 700;
    }
    .job-thread-summary:hover {
        text-decoration: underline;
    }
    html[data-theme="dark"] .job-thread-summary {
        color: #7dd3fc;
    }
    .job-thread-avatars {
        display: inline-flex;
        align-items: center;
    }
    .job-thread-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        margin-left: -6px;
        border: 2px solid #fff;
        border-radius: 9999px;
        background: #64748b;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
    }
    .job-thread-avatar:first-child {
        margin-left: 0;
    }
    html[data-theme="dark"] .job-thread-avatar {
        border-color: #1e293b;
    }
    .job-thread-panel {
        margin-top: 8px;
        padding-left: 8px;
        border-left: 2px solid #e2e8f0;
    }
    html[data-theme="dark"] .job-thread-panel {
        border-left-color: #475569;
    }
    .job-thread-item {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
    }
    .job-thread-item-name {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }
    html[data-theme="dark"] .job-thread-item-name {
        color: #f1f5f9;
    }
    .job-thread-item-text,
    .job-thread-item-time {
        font-size: 13px;
        color: #334155;
    }
    .job-thread-item-time {
        display: block;
        margin-top: 2px;
        font-size: 11px;
        color: #64748b;
    }
    html[data-theme="dark"] .job-thread-item-text {
        color: #e2e8f0;
    }
    .job-thread-form {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .job-thread-input {
        flex: 1;
        min-width: 0;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        padding: 6px 10px;
        font-size: 13px;
        color: #0f172a;
    }
    html[data-theme="dark"] .job-thread-input {
        border-color: #475569;
        background: #0f172a;
        color: #f8fafc;
    }
    .job-thread-send {
        border: 0;
        border-radius: 8px;
        background: #2563eb;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
    }
    [id^="discussion-"] {
        scroll-margin-top: 5rem;
    }
    .job-comment-target {
        box-shadow: 0 0 0 2px #2563eb;
    }
</style>
<script>
(function () {
    var people = @json($mentionPeople);
    if (!people.length) return;

    var menu = document.createElement('ul');
    menu.className = 'job-mention-menu';
    menu.setAttribute('role', 'listbox');
    document.body.appendChild(menu);

    var activeIndex = 0;
    var currentEditor = null;
    var currentQuery = '';
    var filtered = [];

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function closeMenu() {
        menu.classList.remove('is-open');
        menu.innerHTML = '';
        currentEditor = null;
        filtered = [];
    }

    function caretRect(editor) {
        var sel = window.getSelection();
        if (!sel || !sel.rangeCount) return editor.getBoundingClientRect();
        var range = sel.getRangeAt(0).cloneRange();
        range.collapse(true);
        var rect = range.getClientRects()[0];
        if (rect && (rect.width || rect.height)) return rect;
        return editor.getBoundingClientRect();
    }

    function textBeforeCaret(editor) {
        var sel = window.getSelection();
        if (!sel || !sel.rangeCount) return '';
        var range = sel.getRangeAt(0);
        if (!range.collapsed || !editor.contains(range.endContainer)) return '';
        var pre = range.cloneRange();
        pre.selectNodeContents(editor);
        pre.setEnd(range.endContainer, range.endOffset);
        return pre.toString().replace(/\u00a0/g, ' ');
    }

    function mentionQuery(editor) {
        var text = textBeforeCaret(editor);
        var at = text.lastIndexOf('@');
        if (at < 0) return null;
        if (at > 0 && /[A-Za-z0-9._]/.test(text.charAt(at - 1))) return null;
        var query = text.slice(at + 1);
        if (query.length > 40 || /[\n\r]/.test(query)) return null;
        return query;
    }

    function renderMenu(editor, query) {
        currentEditor = editor;
        currentQuery = query;
        var needle = query.trim().toLowerCase();
        filtered = people.filter(function (person) {
            if (!needle) return true;
            var hay = (person.name + ' ' + person.code + ' ' + person.roles.join(' ')).toLowerCase();
            return hay.indexOf(needle) !== -1;
        });
        activeIndex = 0;
        if (!filtered.length) {
            menu.innerHTML = '<li class="job-mention-empty">No matching staff or checker</li>';
        } else {
            menu.innerHTML = filtered.map(function (person, index) {
                var codeLine = person.name.toLowerCase() !== person.code.toLowerCase()
                    ? '<span class="job-mention-code">' + escapeHtml(person.code) + '</span>'
                    : '';
                return '<li><button type="button" class="job-mention-item' + (index === 0 ? ' is-active' : '') + '" data-index="' + index + '" role="option">' +
                    '<span class="job-mention-name">' + escapeHtml(person.name) + '</span>' + codeLine +
                    '</button></li>';
            }).join('');
        }
        menu.classList.add('is-open');
        var rect = caretRect(editor);
        var left = Math.max(8, rect.left);
        var top = rect.bottom + 6;
        menu.style.left = left + 'px';
        menu.style.top = top + 'px';
        var box = menu.getBoundingClientRect();
        if (box.right > window.innerWidth - 8) {
            menu.style.left = Math.max(8, window.innerWidth - box.width - 8) + 'px';
        }
        if (box.bottom > window.innerHeight - 8) {
            menu.style.top = Math.max(8, rect.top - box.height - 6) + 'px';
        }
    }

    function moveActive(step) {
        if (!filtered.length) return;
        activeIndex = (activeIndex + step + filtered.length) % filtered.length;
        menu.querySelectorAll('.job-mention-item').forEach(function (btn, index) {
            btn.classList.toggle('is-active', index === activeIndex);
            if (index === activeIndex) btn.scrollIntoView({ block: 'nearest' });
        });
    }

    function stepBack(node, root) {
        if (node.previousSibling) {
            node = node.previousSibling;
            while (node.lastChild) node = node.lastChild;
            return node;
        }
        if (node.parentNode && node.parentNode !== root) return node.parentNode;
        return null;
    }

    function deleteBeforeCaret(editor, count) {
        var sel = window.getSelection();
        if (!sel || !sel.rangeCount) return;
        var range = sel.getRangeAt(0);
        var node = range.startContainer;
        var offset = range.startOffset;
        var left = count;
        while (left > 0 && node && editor.contains(node)) {
            if (node.nodeType === Node.TEXT_NODE) {
                var take = Math.min(offset, left);
                if (take > 0) {
                    node.deleteData(offset - take, take);
                    offset -= take;
                    left -= take;
                }
                if (left === 0) break;
            }
            var prev = stepBack(node, editor);
            if (!prev) break;
            node = prev;
            offset = node.nodeType === Node.TEXT_NODE ? (node.nodeValue || '').length : 0;
        }
        if (!node || !editor.contains(node)) return;
        var next = document.createRange();
        var caretOffset = node.nodeType === Node.TEXT_NODE ? offset : 0;
        next.setStart(node, caretOffset);
        next.collapse(true);
        sel.removeAllRanges();
        sel.addRange(next);
    }

    function insertMention(person) {
        var editor = currentEditor;
        if (!editor || !person) return;
        editor.focus();
        deleteBeforeCaret(editor, currentQuery.length + 1);
        var sel = window.getSelection();
        if (!sel || !sel.rangeCount) return;
        var range = sel.getRangeAt(0);
        var chip = document.createElement('span');
        chip.className = 'job-mention';
        chip.contentEditable = 'false';
        chip.setAttribute('data-code', person.code);
        chip.textContent = '@' + person.code;
        var space = document.createTextNode('\u00A0');
        var frag = document.createDocumentFragment();
        frag.appendChild(chip);
        frag.appendChild(space);
        range.insertNode(frag);
        var after = document.createRange();
        after.setStartAfter(space);
        after.collapse(true);
        sel.removeAllRanges();
        sel.addRange(after);
        closeMenu();
    }

    function refresh(editor) {
        var query = mentionQuery(editor);
        if (query === null) {
            if (currentEditor === editor) closeMenu();
            return;
        }
        renderMenu(editor, query);
    }

    document.querySelectorAll('.job-view-comment-body').forEach(function (editor) {
        editor.addEventListener('input', function () { refresh(editor); });
        editor.addEventListener('keyup', function (event) {
            if (event.key === 'ArrowUp' || event.key === 'ArrowDown' || event.key === 'Enter' || event.key === 'Escape') return;
            refresh(editor);
        });
        editor.addEventListener('keydown', function (event) {
            if (!menu.classList.contains('is-open') || currentEditor !== editor) return;
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                moveActive(1);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                moveActive(-1);
            } else if (event.key === 'Enter' || event.key === 'Tab') {
                if (!filtered.length) return;
                event.preventDefault();
                insertMention(filtered[activeIndex]);
            } else if (event.key === 'Escape') {
                event.preventDefault();
                closeMenu();
            }
        });
        editor.addEventListener('blur', function () {
            setTimeout(function () {
                if (currentEditor === editor && !menu.contains(document.activeElement)) closeMenu();
            }, 150);
        });
    });

    menu.addEventListener('mousedown', function (event) {
        event.preventDefault();
    });
    menu.addEventListener('click', function (event) {
        var btn = event.target.closest('.job-mention-item');
        if (!btn) return;
        insertMention(filtered[Number(btn.getAttribute('data-index'))]);
    });
    document.addEventListener('click', function (event) {
        if (!menu.classList.contains('is-open')) return;
        if (menu.contains(event.target)) return;
        if (currentEditor && currentEditor.contains(event.target)) return;
        closeMenu();
    });
})();
</script>
<script>
(function () {
    var replyUrl = @json(\Illuminate\Support\Facades\Route::has('discussion.reply') ? route('discussion.reply') : '');
    var jobId = @json((int) ($jobId ?? 0));
    var isBph = @json(!empty($isBphView));
    var savedReplies = @json($discussionReplies ?? []);
    var threads = {};
    savedReplies.forEach(function (reply) {
        var key = reply.source + ':' + reply.parent_id;
        if (!threads[key]) threads[key] = [];
        threads[key].push(reply);
    });

    var imageUploadUrl = @json(\Illuminate\Support\Facades\Route::has('discussion.image.store') ? route('discussion.image.store') : '');

    function csrf() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function clipboardImage(event) {
        var data = event.clipboardData;
        if (!data) return null;
        var items = data.items || [];
        for (var i = 0; i < items.length; i++) {
            if (items[i].kind === 'file' && items[i].type.indexOf('image/') === 0) {
                return items[i].getAsFile();
            }
        }
        var files = data.files || [];
        for (var f = 0; f < files.length; f++) {
            if (files[f].type && files[f].type.indexOf('image/') === 0) return files[f];
        }
        return null;
    }

    function placeNode(editor, node) {
        var sel = window.getSelection();
        if (sel && sel.rangeCount && editor.contains(sel.getRangeAt(0).startContainer)) {
            var range = sel.getRangeAt(0);
            range.deleteContents();
            range.insertNode(node);
            range.setStartAfter(node);
            range.collapse(true);
            sel.removeAllRanges();
            sel.addRange(range);
            return;
        }
        editor.appendChild(node);
    }

    function draftImage(url) {
        var wrap = document.createElement('span');
        wrap.className = 'job-comment-image-draft';
        wrap.setAttribute('contenteditable', 'false');
        var image = document.createElement('img');
        image.src = url;
        image.alt = '';
        image.className = 'job-comment-image';
        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'job-comment-image-remove';
        remove.textContent = 'Delete';
        remove.setAttribute('aria-label', 'Delete image');
        remove.addEventListener('mousedown', function (event) {
            event.preventDefault();
            event.stopPropagation();
        });
        remove.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var next = wrap.nextSibling;
            wrap.remove();
            if (next && next.nodeType === 3 && next.textContent === ' ') next.remove();
        });
        wrap.appendChild(image);
        wrap.appendChild(remove);
        return wrap;
    }

    function unwrapDraftImages(box) {
        if (!box) return;
        box.querySelectorAll('.job-comment-image-draft').forEach(function (draft) {
            var image = draft.querySelector('img');
            if (image && draft.parentNode) draft.parentNode.insertBefore(image, draft);
            draft.remove();
        });
    }

    function insertImage(editor, url, loading) {
        editor.focus();
        var draft = draftImage(url);
        var space = document.createTextNode(' ');
        if (loading && loading.parentNode) {
            loading.parentNode.insertBefore(draft, loading);
            loading.parentNode.insertBefore(space, loading);
            loading.remove();
            return;
        }
        placeNode(editor, draft);
        if (draft.parentNode) draft.parentNode.insertBefore(space, draft.nextSibling);
    }

    function insertLoading(editor) {
        editor.focus();
        var note = document.createElement('span');
        note.className = 'job-comment-image-loading';
        note.setAttribute('contenteditable', 'false');
        note.textContent = 'Uploading image…';
        placeNode(editor, note);
        return note;
    }

    document.querySelectorAll('.job-view-comment-body').forEach(function (editor) {
        editor.addEventListener('paste', function (event) {
            var image = clipboardImage(event);
            event.preventDefault();
            if (!image) {
                var text = event.clipboardData ? event.clipboardData.getData('text/plain') : '';
                document.execCommand('insertText', false, text);
                return;
            }
            if (!imageUploadUrl) return;
            var loading = insertLoading(editor);
            var body = new FormData();
            body.append('image', image, image.name || 'snip.png');
            body.append('_token', csrf());
            fetch(imageUploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
                body: body,
                credentials: 'same-origin'
            }).then(function (response) {
                return response.json().then(function (data) { return { ok: response.ok, data: data }; });
            }).then(function (result) {
                if (!result.ok || !result.data || !result.data.url) {
                    if (loading) loading.remove();
                    if (window.showSuccessToast) showSuccessToast('Could not paste the image.');
                    return;
                }
                insertImage(editor, result.data.url, loading);
            }).catch(function () {
                if (loading) loading.remove();
                if (window.showSuccessToast) showSuccessToast('Could not paste the image.');
            });
        });
    });

    ['runCommentSendBtn', 'jobCommentSendBtn'].forEach(function (id) {
        var button = document.getElementById(id);
        if (!button) return;
        button.addEventListener('click', function (event) {
            var editor = button.closest('.job-view-comment-editor');
            var box = editor ? editor.querySelector('.job-view-comment-body') : null;
            if (box && box.querySelector('.job-comment-image-loading')) {
                event.preventDefault();
                event.stopImmediatePropagation();
                if (window.showSuccessToast) showSuccessToast('Still uploading the image.');
                return;
            }
            unwrapDraftImages(box);
        }, true);
    });

    function taggedComment(li) {
        var message = li.querySelector('.prose');
        if (!message) return false;
        return !!message.querySelector('.job-mention, [data-code]');
    }

    function threadInfo(li) {
        var match = (li.id || '').match(/^discussion-(run|job)-(\d+)$/);
        if (!match) return null;
        var source = match[1] === 'run'
            ? (isBph ? 'bph_run_comments' : 'run_comments')
            : (isBph ? 'bph_comments' : 'comments');
        return { source: source, parentId: match[2], key: source + ':' + match[2] };
    }

    function initial(name) {
        var text = String(name || 'L').trim();
        return text ? text.charAt(0).toUpperCase() : 'L';
    }

    function replyLabel(count) {
        return count === 1 ? '1 reply' : count + ' replies';
    }

    function paintSummary(thread, replies) {
        var summary = thread.querySelector('.job-thread-summary');
        var avatars = thread.querySelector('.job-thread-avatars');
        var count = thread.querySelector('.job-thread-count');
        if (!summary || !avatars || !count) return;
        if (!replies.length) {
            summary.hidden = true;
            return;
        }
        summary.hidden = false;
        var seen = {};
        var faces = [];
        replies.forEach(function (reply) {
            var name = reply.author_name || 'LUNTIAN';
            if (seen[name] || faces.length >= 5) return;
            seen[name] = true;
            faces.push(name);
        });
        avatars.innerHTML = faces.map(function (name) {
            var safe = String(name).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
            return '<span class="job-thread-avatar" title="' + safe + '">' + initial(name) + '</span>';
        }).join('');
        count.textContent = replyLabel(replies.length);
    }

    function paintList(thread, replies) {
        var list = thread.querySelector('.job-thread-list');
        if (!list) return;
        list.innerHTML = replies.map(function (reply) {
            var name = reply.author_name || 'LUNTIAN';
            return '<div class="job-thread-item">' +
                '<span class="job-thread-avatar">' + initial(name) + '</span>' +
                '<div><div class="job-thread-item-name"></div><div class="job-thread-item-text"></div><span class="job-thread-item-time"></span></div>' +
                '</div>';
        }).join('');
        var items = list.querySelectorAll('.job-thread-item');
        replies.forEach(function (reply, index) {
            var item = items[index];
            if (!item) return;
            item.querySelector('.job-thread-item-name').textContent = reply.author_name || 'LUNTIAN';
            item.querySelector('.job-thread-item-text').textContent = reply.message || '';
            item.querySelector('.job-thread-item-time').textContent = reply.created_at || '';
        });
    }

    function ensureThread(li) {
        var info = threadInfo(li);
        var body = li.querySelector('.min-w-0');
        if (!info || !body) return null;
        var thread = body.querySelector('.job-thread');
        if (thread) return thread;
        thread = document.createElement('div');
        thread.className = 'job-thread';
        thread.innerHTML = '<button type="button" class="job-thread-summary" hidden><span class="job-thread-avatars"></span><span class="job-thread-count"></span></button>' +
            '<div class="job-thread-panel" hidden><div class="job-thread-list"></div>' +
            '<form class="job-thread-form"><input type="text" class="job-thread-input" placeholder="Reply..." maxlength="5000"><button type="submit" class="job-thread-send">Reply</button></form></div>';
        body.appendChild(thread);
        return thread;
    }

    function refreshThread(li) {
        var info = threadInfo(li);
        var thread = ensureThread(li);
        if (!info || !thread) return;
        var replies = threads[info.key] || [];
        paintSummary(thread, replies);
        paintList(thread, replies);
    }

    function openThread(li) {
        var thread = ensureThread(li);
        if (!thread) return;
        refreshThread(li);
        var panel = thread.querySelector('.job-thread-panel');
        if (panel) panel.hidden = false;
        var input = thread.querySelector('.job-thread-input');
        if (input) input.focus();
    }

    function addReplyButton(li) {
        if (!taggedComment(li)) return;
        var info = threadInfo(li);
        var section = li.closest('section');
        var canReply = !!(section && section.querySelector('.job-view-comment-body'));
        var replies = info && threads[info.key] ? threads[info.key] : [];
        if (!canReply && !replies.length) return;
        var body = li.querySelector('.min-w-0');
        if (!body) return;
        if (canReply && !li.querySelector('.job-comment-reply')) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'job-comment-reply';
            button.textContent = 'Reply';
            body.appendChild(button);
        }
        refreshThread(li);
        if (!canReply) {
            var form = li.querySelector('.job-thread-form');
            if (form) form.hidden = true;
        }
    }

    function scan() {
        document.querySelectorAll('#runCommentsList > li, #jobCommentsList > li').forEach(addReplyButton);
    }

    scan();
    ['runCommentsList', 'jobCommentsList'].forEach(function (id) {
        var list = document.getElementById(id);
        if (!list || typeof MutationObserver === 'undefined') return;
        new MutationObserver(scan).observe(list, { childList: true });
    });

    document.addEventListener('click', function (event) {
        var replyButton = event.target.closest('.job-comment-reply');
        if (replyButton) {
            var li = replyButton.closest('li');
            if (li) openThread(li);
            return;
        }
        var summary = event.target.closest('.job-thread-summary');
        if (!summary) return;
        var panel = summary.parentNode.querySelector('.job-thread-panel');
        if (panel) panel.hidden = !panel.hidden;
    });

    document.addEventListener('submit', function (event) {
        var form = event.target.closest('.job-thread-form');
        if (!form) return;
        event.preventDefault();
        var li = form.closest('li');
        var info = li ? threadInfo(li) : null;
        var input = form.querySelector('.job-thread-input');
        var message = input ? input.value.trim() : '';
        if (!info || !message || !replyUrl) return;
        var send = form.querySelector('.job-thread-send');
        if (send) send.disabled = true;
        var body = new URLSearchParams();
        body.append('_token', csrf());
        body.append('source', info.source);
        body.append('parent_id', info.parentId);
        body.append('job_id', String(jobId));
        body.append('message', message);
        fetch(replyUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString()
        }).then(function (response) {
            return response.json().then(function (data) { return { ok: response.ok, data: data }; });
        }).then(function (result) {
            if (send) send.disabled = false;
            if (!result.ok || !result.data || !result.data.reply) return;
            if (!threads[info.key]) threads[info.key] = [];
            threads[info.key].push(result.data.reply);
            input.value = '';
            refreshThread(li);
            openThread(li);
        }).catch(function () {
            if (send) send.disabled = false;
        });
    });

    var commentHash = window.location.hash || '';
    if (commentHash.indexOf('#discussion-') === 0) {
        var commentEl = document.getElementById(decodeURIComponent(commentHash.slice(1)));
        if (commentEl) {
            commentEl.classList.add('job-comment-target');
            setTimeout(function () {
                commentEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 80);
        }
    }
})();
</script>
