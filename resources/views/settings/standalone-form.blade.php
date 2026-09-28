@extends('layouts.dashboard')

@section('title', 'Standalone Form')

@section('content')
    <div class="standalone-settings flex flex-col">
        <div class="mb-4 shrink-0">
            <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Standalone Form</h1>
            <p class="text-slate-500 dark:text-slate-400">Search a form. Set each field as required, and turn it on or off.</p>
        </div>

        <div class="relative mb-4 shrink-0">
            <label for="standaloneFormSearch" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Search</label>
            <input type="search" id="standaloneFormSearch" autocomplete="off" placeholder="Search LBS, Generic EA, FYRS..."
                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder-slate-500">
            <ul id="standaloneFormResults" class="absolute z-20 mt-1 hidden w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg dark:border-slate-600 dark:bg-slate-800" role="listbox"></ul>
        </div>

        <div id="standaloneFormLinkBar" class="mb-4 hidden shrink-0 items-center gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-3 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
            <a id="standaloneFormLink" href="#" target="_blank" rel="noopener noreferrer" class="min-w-0 flex-1 truncate text-sm font-medium text-emerald-700 hover:underline dark:text-emerald-400"></a>
            <button type="button" id="standaloneFormCopy" class="shrink-0 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700">Copy</button>
        </div>

        <div id="standaloneFormFields" class="standalone-settings-panel hidden">
            <div class="standalone-settings-fields flex min-h-0 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                <div class="shrink-0 border-b border-slate-200 bg-slate-50/80 px-3 py-3 dark:border-slate-700 dark:bg-slate-800/80">
                    <h2 id="standaloneFormTitle" class="text-base font-semibold text-slate-800 dark:text-slate-100"></h2>
                </div>
                <ul id="standaloneFieldList" class="min-h-0 flex-1 divide-y divide-slate-200 overflow-y-auto dark:divide-slate-700"></ul>
            </div>
            <div class="standalone-settings-preview overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                <iframe id="standaloneFormFrame" title="Standalone form" class="standalone-settings-frame w-full border-0 bg-slate-50 dark:bg-slate-900" src="about:blank"></iframe>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .standalone-settings {
        display: flex;
        flex-direction: column;
        height: calc(100dvh - 5.5rem);
        min-height: 32rem;
    }
    @media (min-width: 768px) {
        .standalone-settings {
            height: calc(100dvh - 6.5rem);
        }
    }
    .standalone-settings-panel {
        min-height: 0;
        flex: 1 1 auto;
    }
    .standalone-settings-panel:not(.hidden) {
        display: grid;
        gap: 1rem;
        grid-template-columns: minmax(0, 1fr);
    }
    @media (min-width: 1024px) {
        .standalone-settings-panel:not(.hidden) {
            grid-template-columns: 17.5rem minmax(0, 1fr);
        }
    }
    .standalone-settings-fields,
    .standalone-settings-preview {
        min-height: 0;
        overflow: hidden;
    }
    .standalone-settings-preview,
    .standalone-settings-frame {
        width: 100%;
        height: 100%;
        min-height: 28rem;
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    var forms = @json($forms);
    var toggleUrl = @json($toggleUrl);
    var optionUrl = @json($optionUrl);
    var token = document.querySelector('meta[name="csrf-token"]');
    var search = document.getElementById('standaloneFormSearch');
    var results = document.getElementById('standaloneFormResults');
    var panel = document.getElementById('standaloneFormFields');
    var title = document.getElementById('standaloneFormTitle');
    var list = document.getElementById('standaloneFieldList');
    var frame = document.getElementById('standaloneFormFrame');
    var activeForm = null;
    var linkBar = document.getElementById('standaloneFormLinkBar');
    var link = document.getElementById('standaloneFormLink');
    var copyBtn = document.getElementById('standaloneFormCopy');
    if (!search || !results || !panel || !title || !list || !frame || !linkBar || !link) return;

    function currentTheme() {
        return document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
    }

    function embedUrl(url) {
        if (!url) return 'about:blank';
        return url + (url.indexOf('?') === -1 ? '?' : '&') + 'embed=1&theme=' + currentTheme();
    }

    function syncFrameTheme() {
        postToFrame({ type: 'standalone-theme', theme: currentTheme() });
    }

    function postToFrame(message) {
        if (!frame.contentWindow) return;
        var origin = '*';
        try {
            origin = new URL(frame.src, window.location.href).origin;
        } catch (err) {}
        frame.contentWindow.postMessage(message, origin);
    }

    function renderResults(query) {
        var q = (query || '').trim().toLowerCase();
        var matches = forms.filter(function (form) {
            return q === '' || String(form.name || '').toLowerCase().indexOf(q) !== -1;
        });
        results.innerHTML = '';
        if (!matches.length) {
            results.classList.add('hidden');
            return;
        }
        matches.forEach(function (form) {
            var li = document.createElement('li');
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'block w-full px-4 py-2.5 text-left text-sm text-slate-800 hover:bg-slate-100 dark:text-slate-100 dark:hover:bg-slate-700';
            btn.textContent = form.name;
            btn.addEventListener('click', function () { showForm(form); });
            li.appendChild(btn);
            results.appendChild(li);
        });
        results.classList.remove('hidden');
    }

    function showForm(form) {
        activeForm = form;
        search.value = form.name;
        results.classList.add('hidden');
        title.textContent = form.name;
        list.innerHTML = '';
        (form.fields || []).forEach(function (field) {
            list.appendChild(fieldRow(form.key, field));
        });
        frame.src = embedUrl(form.url || '');
        link.href = form.url || '#';
        link.textContent = form.url || '';
        linkBar.classList.remove('hidden');
        linkBar.classList.add('flex');
        panel.classList.remove('hidden');
    }

    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            var url = link.href && link.href !== '#' ? link.href : link.textContent;
            if (!url || !navigator.clipboard) return;
            navigator.clipboard.writeText(url).then(function () {
                copyBtn.textContent = 'Copied';
                setTimeout(function () { copyBtn.textContent = 'Copy'; }, 1500);
            });
        });
    }

    function paintState(state, on) {
        state.textContent = on ? state.dataset.on : state.dataset.off;
        state.className = 'text-xs font-medium ' + (on ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400');
    }

    function saveToggle(formKey, field, payload, onOk, onFail) {
        var body = {
            form_key: formKey,
            field_key: field.key
        };
        Object.keys(payload).forEach(function (key) { body[key] = payload[key]; });
        fetch(toggleUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token ? token.getAttribute('content') : ''
            },
            body: JSON.stringify(body)
        }).then(function (res) {
            if (!res.ok) onFail();
            else onOk();
        }).catch(function () {
            onFail();
        });
    }

    function toggleControl(options) {
        var label = document.createElement('label');
        label.className = 'inline-flex cursor-pointer items-center gap-1.5';

        var state = document.createElement('span');
        state.dataset.on = options.onText;
        state.dataset.off = options.offText;
        paintState(state, options.checked);

        var input = document.createElement('input');
        input.type = 'checkbox';
        input.className = 'peer sr-only';
        input.checked = !!options.checked;

        var track = document.createElement('span');
        track.className = 'relative h-5 w-9 shrink-0 rounded-full bg-slate-300 transition-colors peer-checked:bg-emerald-600 peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-500/40 after:absolute after:left-0.5 after:top-0.5 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-transform peer-checked:after:translate-x-4 dark:bg-slate-600';

        input.addEventListener('change', function () {
            paintState(state, input.checked);
            options.onChange(input.checked, input, state);
        });

        label.appendChild(state);
        label.appendChild(input);
        label.appendChild(track);
        return label;
    }

    function fieldRow(formKey, field) {
        var li = document.createElement('li');
        li.className = 'flex flex-col gap-2 px-3 py-2.5';

        var name = document.createElement('span');
        name.className = 'min-w-0 text-sm font-medium text-slate-800 dark:text-slate-100';
        name.textContent = field.label;

        var controls = document.createElement('div');
        controls.className = 'flex items-center justify-between gap-2';

        controls.appendChild(toggleControl({
            onText: 'Required',
            offText: 'Not required',
            checked: !!field.is_required,
            onChange: function (checked, input, state) {
                field.is_required = checked;
                saveToggle(formKey, field, { is_required: checked }, function () {
                    postToFrame({ type: 'standalone-required', field: field.key, required: checked });
                }, function () {
                    input.checked = !checked;
                    field.is_required = !checked;
                    paintState(state, input.checked);
                });
            }
        }));

        controls.appendChild(toggleControl({
            onText: 'On',
            offText: 'Off',
            checked: field.is_visible !== false,
            onChange: function (checked, input, state) {
                field.is_visible = checked;
                saveToggle(formKey, field, { is_visible: checked }, function () {
                    postToFrame({ type: 'standalone-visible', field: field.key, visible: checked });
                }, function () {
                    input.checked = !checked;
                    field.is_visible = !checked;
                    paintState(state, input.checked);
                });
            }
        }));

        li.appendChild(name);
        li.appendChild(controls);
        return li;
    }

    function saveOption(formKey, field, payload, onOk, onFail) {
        var body = {
            form_key: formKey,
            field_key: field.key,
            action: payload.action,
            id: payload.id || null,
            label: payload.label || ''
        };
        fetch(optionUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token ? token.getAttribute('content') : ''
            },
            body: JSON.stringify(body)
        }).then(function (res) {
            return res.json().then(function (data) {
                if (!res.ok) {
                    onFail(data && data.message ? data.message : 'Could not save that option.');
                    return;
                }
                onOk(data);
            });
        }).catch(function () {
            onFail('Could not save that option.');
        });
    }

    window.addEventListener('message', function (event) {
        if (!frame.contentWindow || event.source !== frame.contentWindow || !activeForm) return;
        var data = event.data || {};
        if (data.type !== 'standalone-option') return;
        var field = null;
        (activeForm.fields || []).some(function (item) {
            if (item.key === data.field) {
                field = item;
                return true;
            }
            return false;
        });
        if (!field || !Array.isArray(field.options)) return;
        saveOption(activeForm.key, field, {
            action: data.action,
            id: data.id,
            label: data.label
        }, function (res) {
            field.options = res.options || [];
            postToFrame({ type: 'standalone-options', field: field.key, options: field.options });
        }, function (message) {
            postToFrame({ type: 'standalone-option-error', field: data.field, message: message });
        });
    });

    frame.addEventListener('load', syncFrameTheme);
    document.addEventListener('themechange', function () {
        if (!frame.src || frame.src === 'about:blank') return;
        syncFrameTheme();
    });

    search.addEventListener('focus', function () { renderResults(search.value); });
    search.addEventListener('input', function () { renderResults(search.value); });
    document.addEventListener('click', function (e) {
        if (!results.contains(e.target) && e.target !== search) {
            results.classList.add('hidden');
        }
    });
})();
</script>
@endpush
