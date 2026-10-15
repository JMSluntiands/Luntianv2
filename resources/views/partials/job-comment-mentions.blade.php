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
@endphp
<style>
    .job-mention {
        display: inline;
        font-weight: 600;
        color: #2563eb;
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
    .job-comment-replying {
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #2563eb;
    }
    html[data-theme="dark"] .job-comment-replying {
        color: #93c5fd;
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
    function taggedComment(li) {
        var message = li.querySelector('.prose');
        if (!message) return false;
        return !!message.querySelector('.job-mention, [data-code]');
    }

    function addReplyButton(li) {
        if (!taggedComment(li) || li.querySelector('.job-comment-reply')) return;
        var section = li.closest('section');
        if (!section || !section.querySelector('.job-view-comment-body')) return;
        var body = li.querySelector('.min-w-0');
        if (!body) return;
        var author = '';
        var nameEl = body.querySelector('p');
        if (nameEl) author = nameEl.textContent.trim();
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'job-comment-reply';
        button.textContent = 'Reply';
        button.setAttribute('data-author', author);
        body.appendChild(button);
    }

    function scan() {
        document.querySelectorAll('#runCommentsList > li, #jobCommentsList > li').forEach(addReplyButton);
    }

    function showReplying(section, author) {
        var editor = section.querySelector('.job-view-comment-body');
        if (!editor) return;
        var banner = section.querySelector('.job-comment-replying');
        if (!banner) {
            banner = document.createElement('div');
            banner.className = 'job-comment-replying';
            editor.parentNode.insertBefore(banner, editor);
        }
        banner.textContent = author ? 'Replying to ' + author : 'Replying';
        editor.focus();
        editor.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    scan();
    ['runCommentsList', 'jobCommentsList'].forEach(function (id) {
        var list = document.getElementById(id);
        if (!list || typeof MutationObserver === 'undefined') return;
        new MutationObserver(scan).observe(list, { childList: true });
    });

    document.addEventListener('click', function (event) {
        var button = event.target.closest('.job-comment-reply');
        if (!button) return;
        var section = button.closest('section');
        if (!section) return;
        showReplying(section, button.getAttribute('data-author') || '');
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('#runCommentSendBtn, #jobCommentSendBtn')) return;
        var section = event.target.closest('section');
        if (!section) return;
        var banner = section.querySelector('.job-comment-replying');
        if (banner) banner.remove();
    });
})();
</script>
