@extends('layouts.dashboard')

@section('title', 'Add Priority')

@section('body_class', 'page-priority-create')

@section('content')
    <div class="w-full">
        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-500/20 shadow-lg dark:bg-emerald-500/30">
                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Add Priority</h1>
                <p class="text-slate-600 dark:text-slate-400">Create a new priority level with a name and hex color to highlight urgency.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-800 dark:bg-red-900/20" role="alert">
                <ul class="list-inside list-disc space-y-1 text-sm text-red-700 dark:text-red-200">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('priority.store') }}" method="POST" class="space-y-6" autocomplete="off">
            @csrf
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
                <div class="grid gap-5 md:grid-cols-[2fr,3fr]">
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Priority name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Top Priority, 3 days, Low"
                                   class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30">
                        </div>
                        <div>
                            <label for="color" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Color (hex)</label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="colorPicker" class="h-10 w-10 rounded-lg border border-slate-200 bg-transparent p-0 shadow-sm dark:border-slate-600"
                                       value="{{ old('color', '#22c55e') }}" title="Pick color">
                                <input type="text" id="color" name="color" value="{{ old('color', '#22c55e') }}" maxlength="7" autocomplete="off"
                                       placeholder="#22c55e"
                                       class="flex-1 rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30">
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Use a hex color (e.g. <code>#22c55e</code>). This color is used for chips and badges.</p>
                        </div>
                    </div>
                    <div class="flex items-stretch">
                        <div class="flex w-full flex-col justify-between rounded-xl bg-slate-50 px-4 py-4 text-sm text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800/60 dark:text-slate-200 dark:ring-slate-700">
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Preview</span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[0.72rem] font-medium text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-300">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    Priority chip
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span id="priorityPreviewDot" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-emerald-500 text-xs font-semibold text-white shadow-sm">
                                    P
                                </span>
                                <div class="min-w-0">
                                    <p id="priorityPreviewName" class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">
                                        {{ old('name', 'Top Priority') }}
                                    </p>
                                    <p id="priorityPreviewHex" class="text-xs text-slate-500 dark:text-slate-400">{{ old('color', '#22c55e') }}</p>
                                </div>
                            </div>
                            <p class="mt-3 text-[0.75rem] leading-snug text-slate-500 dark:text-slate-400">
                                This is how the priority will look in lists and filters.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('priority.index') }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:focus:ring-offset-slate-800">Cancel</a>
                <button type="submit" id="prioritySaveBtn" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-70 dark:focus:ring-offset-slate-900">
                    <span class="btn-text">Save</span>
                    <span class="btn-spinner hidden h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            var form = document.querySelector('form[action="{{ route('priority.store') }}"]');
            var btn = document.getElementById('prioritySaveBtn');
            var picker = document.getElementById('colorPicker');
            var hexInput = document.getElementById('color');
            var nameInput = document.getElementById('name');
            var previewDot = document.getElementById('priorityPreviewDot');
            var previewName = document.getElementById('priorityPreviewName');
            var previewHex = document.getElementById('priorityPreviewHex');

            function normalizeHex(val) {
                val = String(val || '').trim().replace(/^#/, '');
                if (/^[A-Fa-f0-9]{6}$/.test(val)) return '#' + val.toLowerCase();
                if (/^[A-Fa-f0-9]{3}$/.test(val)) {
                    return '#' + (val[0] + val[0] + val[1] + val[1] + val[2] + val[2]).toLowerCase();
                }
                return '#22c55e';
            }

            if (picker && hexInput) {
                picker.addEventListener('input', function() {
                    hexInput.value = this.value;
                    if (previewDot) previewDot.style.backgroundColor = this.value;
                    if (previewHex) previewHex.textContent = this.value;
                });
                hexInput.addEventListener('input', function() {
                    var full = normalizeHex(this.value);
                    if (this.value.length >= 3) {
                        picker.value = full;
                        if (previewDot) previewDot.style.backgroundColor = full;
                        if (previewHex) previewHex.textContent = full;
                    }
                });
            }
            if (nameInput && previewName) {
                nameInput.addEventListener('input', function() {
                    previewName.textContent = this.value || 'Top Priority';
                    if (previewDot) {
                        previewDot.textContent = (this.value || 'P').trim().charAt(0).toUpperCase() || 'P';
                    }
                });
            }
            if (form && btn) {
                form.addEventListener('submit', function() {
                    btn.disabled = true;
                    var text = btn.querySelector('.btn-text');
                    var spinner = btn.querySelector('.btn-spinner');
                    if (text) text.textContent = 'Saving...';
                    if (spinner) spinner.classList.remove('hidden');
                });
            }
        })();
    </script>
@endpush
