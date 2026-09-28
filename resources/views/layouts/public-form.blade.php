<!DOCTYPE html>
<html lang="en" @class(['form-embed' => request()->boolean('embed')])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Luntian Form') - Luntian</title>
    @includeIf('layouts.partials.favicon')
    <script>
        (function() {
            var t = (typeof localStorage !== 'undefined' && localStorage.getItem('theme')) || '';
            var theme = (String(t).toLowerCase() === 'light') ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    @include('layouts.partials.dashboard-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/layout.ts'])
    <style>
        /* After Vite so this beats the dashboard html/body lock in app.css. */
        html, body {
            height: auto;
            overflow: auto;
        }
        .standalone-off { display: none !important; }

        html.form-embed,
        html.form-embed body {
            height: 100%;
        }
        html.form-embed body {
            overflow: auto;
        }
        html.form-embed main {
            display: flex;
            flex-direction: column;
            min-height: 100%;
            padding: 0.75rem;
        }
        html.form-embed main > div {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            width: 100%;
            max-width: none;
        }
        html.form-embed main form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
        }
        html.form-embed main form > div:first-child {
            margin-bottom: 0.75rem;
        }
        html.form-embed [data-standalone-group="job-details"]:has([data-standalone-field="notes"]:not(.standalone-off)) {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
        }
        html.form-embed [data-standalone-group="job-details"]:has([data-standalone-field="notes"]:not(.standalone-off)) > .p-5 {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
        }
        html.form-embed [data-standalone-field="notes"]:not(.standalone-off) {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 8rem;
        }
        html.form-embed [data-standalone-field="notes"]:not(.standalone-off) > div {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
        }
        html.form-embed #lbs-notes-body,
        html.form-embed #fyrs-notes-body {
            flex: 1 1 auto;
            min-height: 8rem;
        }
        html.form-embed form:not(:has([data-standalone-field="notes"]:not(.standalone-off))) [data-standalone-group="attachments"]:not(.standalone-off),
        html.form-embed form:not(:has([data-standalone-field="notes"]:not(.standalone-off))) [data-standalone-field="upload_files"]:not(.standalone-off) {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
        }
        html.form-embed form:not(:has([data-standalone-field="notes"]:not(.standalone-off))) [data-standalone-group="attachments"] > .p-5,
        html.form-embed form:not(:has([data-standalone-field="notes"]:not(.standalone-off))) [data-standalone-field="upload_files"] > .p-5 {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
        }
        html.form-embed form:not(:has([data-standalone-field="notes"]:not(.standalone-off))) [data-standalone-group="attachments"] .grid {
            flex: 1 1 auto;
        }
        html.form-embed form:not(:has([data-standalone-field="notes"]:not(.standalone-off))) [data-standalone-field="plans"]:not(.standalone-off),
        html.form-embed form:not(:has([data-standalone-field="notes"]:not(.standalone-off))) [data-standalone-field="documents"]:not(.standalone-off) {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        html.form-embed #plans-dropzone,
        html.form-embed #docs-dropzone,
        html.form-embed label[for="fyrs_upload_files"] {
            flex: 1 1 auto;
            min-height: 9rem;
        }
        .standalone-option-row {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            width: 100%;
        }
        .standalone-option-label {
            min-width: 0;
            flex: 1 1 auto;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .standalone-option-actions {
            display: flex;
            flex-shrink: 0;
            gap: 0.2rem;
        }
        .standalone-option-edit,
        .standalone-option-delete {
            border: 0;
            border-radius: 0.25rem;
            background: transparent;
            padding: 0.1rem 0.35rem;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.4;
            cursor: pointer;
        }
        .standalone-option-edit { color: #334155; }
        .standalone-option-delete { color: #dc2626; }
        .standalone-option-edit:hover,
        .standalone-option-delete:hover { background: rgba(255, 255, 255, 0.7); }
        html.form-embed .select2-search--dropdown.standalone-search-add {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }
        html.form-embed .select2-search--dropdown.standalone-search-add .select2-search__field {
            flex: 1 1 auto;
            width: auto !important;
            margin: 0;
        }
        html.form-embed .standalone-search-add-btn {
            flex-shrink: 0;
            margin-right: 0.35rem;
            border: 0;
            border-radius: 0.35rem;
            background: #059669;
            color: #fff;
            padding: 0.35rem 0.65rem;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.2;
            cursor: pointer;
        }
        html.form-embed .standalone-search-add-btn:hover { background: #047857; }
        html[data-theme="dark"] .standalone-option-edit { color: #e2e8f0; }
        html[data-theme="dark"] .standalone-option-delete { color: #fca5a5; }
        html[data-theme="dark"] .standalone-option-edit:hover,
        html[data-theme="dark"] .standalone-option-delete:hover { background: #475569; }
    </style>
    @stack('styles')
    @include('layouts.partials.select2-theme')
</head>
<body class="overflow-x-hidden @yield('body_class', '')">
    <main class="min-h-screen bg-slate-50 p-4 dark:bg-slate-900 md:p-6">
        <div class="mx-auto w-full max-w-6xl">
            @unless(request()->boolean('embed'))
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <a href="{{ url('/') }}" class="inline-flex items-center no-underline">
                    @includeIf('layouts.partials.brand-logo', ['compact' => true])
                </a>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-slate-500 dark:text-slate-400 max-[480px]:sr-only">Theme</span>
                    @include('layouts.partials.theme-toggle-button')
                </div>
            </div>
            @endunless
            @yield('content')
        </div>
    </main>

    @include('layouts.partials.app-toast')
    <script>
        @include('layouts.partials.theme-toggle-script')
    </script>
    @include('partials.assignment-user-select2')
    <script>
        function syncStandaloneGroups() {
            document.querySelectorAll('[data-standalone-group]').forEach(function (group) {
                var fields = group.querySelectorAll('[data-standalone-field]');
                if (!fields.length) return;
                var anyVisible = Array.prototype.some.call(fields, function (field) {
                    return !field.classList.contains('standalone-off');
                });
                group.classList.toggle('standalone-off', !anyVisible);
            });
        }

        window.addEventListener('message', function (event) {
            if (event.source !== window.parent) return;
            var data = event.data || {};
            if (!data.field) return;
            if (data.type === 'standalone-required') {
                var mark = document.querySelector('[data-required-mark="' + CSS.escape(String(data.field)) + '"]');
                if (mark) mark.classList.toggle('hidden', !data.required);
                return;
            }
            if (data.type === 'standalone-visible') {
                var field = document.querySelector('[data-standalone-field="' + CSS.escape(String(data.field)) + '"]');
                if (field) field.classList.toggle('standalone-off', !data.visible);
                syncStandaloneGroups();
            }
        });
        syncStandaloneGroups();
    </script>
    @stack('scripts')
    @if(request()->boolean('embed'))
    <script>
        window.enhanceStandaloneDropdowns = function () {
            if (!window.jQuery) return;
            var $ = window.jQuery;
            var ids = ['compliance', 'priority', 'job_type'];
            var holdOpen = false;
            var reopenField = null;

            function formatOption(data) {
                if (!data.id) return data.text;
                var $label = $('<span class="standalone-option-label"></span>').text(data.text);
                var $edit = $('<button type="button" class="standalone-option-edit">Edit</button>')
                    .attr('data-id', data.id)
                    .attr('data-label', data.text);
                var $del = $('<button type="button" class="standalone-option-delete">Delete</button>')
                    .attr('data-id', data.id)
                    .attr('data-label', data.text);
                var $actions = $('<span class="standalone-option-actions"></span>').append($edit, $del);
                return $('<span class="standalone-option-row"></span>').append($label, $actions);
            }

            function requestOption(field, action, id, label) {
                if (window.parent === window) return;
                reopenField = field;
                window.parent.postMessage({
                    type: 'standalone-option',
                    field: field,
                    action: action,
                    id: id,
                    label: label
                }, '*');
            }

            function applyOptions(field, options) {
                var sel = document.getElementById(field);
                if (!sel) return;
                var $sel = $(sel);
                var current = sel.value;
                var placeholder = sel.querySelector('option[value=""]');
                var placeholderText = placeholder ? placeholder.textContent : '';
                holdOpen = false;
                if ($sel.data('select2')) $sel.select2('close');
                sel.innerHTML = '';
                var empty = document.createElement('option');
                empty.value = '';
                empty.textContent = placeholderText;
                sel.appendChild(empty);
                (options || []).forEach(function (opt) {
                    var option = document.createElement('option');
                    option.value = String(opt.id);
                    option.textContent = opt.label;
                    sel.appendChild(option);
                });
                var stillThere = current && sel.querySelector('option[value="' + CSS.escape(current) + '"]');
                $sel.val(stillThere ? current : '').trigger('change');
                if (reopenField === field) {
                    $sel.select2('open');
                }
            }

            if (!window.__standaloneOptionEvents) {
                window.__standaloneOptionEvents = true;
                document.addEventListener('mouseup', function (e) {
                    if (!e.target || !e.target.closest) return;
                    if (e.target.closest('.standalone-option-edit, .standalone-option-delete, .standalone-search-add-btn')) {
                        e.stopPropagation();
                    }
                }, true);
                $(document).on('click', '.standalone-option-edit', function () {
                    var id = $(this).attr('data-id');
                    var label = $(this).attr('data-label') || '';
                    var openId = null;
                    ids.forEach(function (selectId) {
                        var s2 = $('#' + selectId).data('select2');
                        if (s2 && s2.$container && s2.$container.hasClass('select2-container--open')) openId = selectId;
                    });
                    if (!openId) return;
                    holdOpen = false;
                    var next = window.prompt('Edit option', label);
                    if (next === null) {
                        $('#' + openId).select2('open');
                        return;
                    }
                    next = next.trim();
                    if (!next || next === label) {
                        $('#' + openId).select2('open');
                        return;
                    }
                    requestOption(openId, 'update', id, next);
                });
                $(document).on('click', '.standalone-option-delete', function () {
                    var id = $(this).attr('data-id');
                    var label = $(this).attr('data-label') || '';
                    var openId = null;
                    ids.forEach(function (selectId) {
                        var s2 = $('#' + selectId).data('select2');
                        if (s2 && s2.$container && s2.$container.hasClass('select2-container--open')) openId = selectId;
                    });
                    if (!openId) return;
                    holdOpen = false;
                    if (!window.confirm('Delete "' + label + '" from this dropdown?')) {
                        $('#' + openId).select2('open');
                        return;
                    }
                    requestOption(openId, 'delete', id, '');
                });
                $(document).on('mousedown', function (e) {
                    if (!$(e.target).closest('.standalone-option-add, .select2-dropdown').length) holdOpen = false;
                });
                window.addEventListener('message', function (event) {
                    if (event.source !== window.parent) return;
                    var data = event.data || {};
                    if (data.type === 'standalone-options') {
                        applyOptions(String(data.field || ''), data.options || []);
                        return;
                    }
                    if (data.type === 'standalone-option-error') {
                        window.alert(data.message || 'Could not save that option.');
                        if (reopenField) $('#' + reopenField).select2('open');
                    }
                });
            }

            ids.forEach(function (id) {
                var $sel = $('#' + id);
                if (!$sel.length) return;
                if ($sel.data('select2')) $sel.select2('destroy');
                $sel.select2({
                    width: '100%',
                    allowClear: false,
                    templateResult: formatOption
                });
                $sel.off('select2:open.standalone select2:closing.standalone');
                $sel.on('select2:open.standalone', function () {
                    var s2 = $sel.data('select2');
                    if (!s2 || !s2.$dropdown) return;
                    var $search = s2.$dropdown.find('.select2-search--dropdown');
                    var $input = $search.find('.select2-search__field');
                    if (!$search.length || !$input.length || $search.find('.standalone-search-add-btn').length) return;
                    $search.addClass('standalone-search-add');
                    $input.attr('placeholder', 'Type a choice, then Add');
                    var $btn = $('<button type="button" class="standalone-search-add-btn">Add</button>');
                    $search.append($btn);
                    function addFromSearch(e) {
                        if (e) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                        var label = String($input.val() || '').trim();
                        if (!label) return;
                        holdOpen = false;
                        requestOption(id, 'add', null, label);
                    }
                    $btn.on('mousedown', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    });
                    $btn.on('click', addFromSearch);
                    $input[0].addEventListener('keydown', function (e) {
                        if (e.key !== 'Enter') return;
                        addFromSearch(e);
                    }, true);
                });
            });

        };
        function runStandaloneDropdowns() {
            if (window.enhanceStandaloneDropdowns) window.enhanceStandaloneDropdowns();
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', runStandaloneDropdowns);
        } else {
            runStandaloneDropdowns();
        }
    </script>
    @endif
</body>
</html>
