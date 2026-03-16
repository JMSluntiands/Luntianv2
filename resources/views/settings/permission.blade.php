@extends('layouts.dashboard')

@section('title', 'Permission')

@section('body_class', 'page-settings-permission')

@section('content')
    <div class="w-full">
        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-amber-500/20 shadow-lg dark:bg-amber-500/30">
                <svg class="h-8 w-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Permission</h1>
                <p class="text-slate-600 dark:text-slate-400">Choose which pages each role can access. Select a role below to set its permissions. <span class="font-medium text-slate-700 dark:text-slate-300">Empty = role can access all pages.</span></p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200" role="alert">
                <ul class="list-inside list-disc space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="permissionForm" action="{{ route('settings.permission.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Role select: label left, select right (Select2) --}}
            <div class="rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <label for="roleSelect" class="text-sm font-medium text-slate-700 dark:text-slate-200">Select role</label>
                    <div class="w-64 min-w-[12rem]">
                        <select id="roleSelect" class="permission-role-select select2-single w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:focus:ring-emerald-500/30">
                            @foreach($roles as $index => $role)
                                @php
                                    $allowedForRole = $allowedMap[$role] ?? [];
                                    $count = count($allowedForRole);
                                @endphp
                                <option value="{{ $index }}" {{ $index === 0 ? 'selected' : '' }}>
                                    {{ $role }}{{ $count > 0 ? ' (' . $count . ' page' . ($count !== 1 ? 's' : '') . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Panel per role --}}
            @foreach($roles as $index => $role)
                @php
                    $allowedForRole = $allowedMap[$role] ?? [];
                    $isFirst = $index === 0;
                @endphp
                <div role="tabpanel"
                    id="panel-{{ $index }}"
                    class="role-panel rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/60 {{ $isFirst ? '' : 'hidden' }}"
                    data-role-panel="{{ $index }}">
                    <div class="flex items-center gap-3 rounded-t-2xl border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/15 dark:bg-emerald-500/25">
                            <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">{{ $role }}</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                @if(empty($allowedForRole))
                                    Can access all pages (no restrictions)
                                @else
                                    Can access {{ count($allowedForRole) }} page(s) only
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="grid gap-6 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($sections as $sectionName => $routes)
                                <div class="permission-section-card rounded-xl border border-slate-200 bg-slate-50/50 p-4 dark:border-slate-600 dark:bg-slate-800/40">
                                    <div class="mb-3 flex items-center justify-between gap-2">
                                        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $sectionName }}</h3>
                                        <label class="flex cursor-pointer items-center gap-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300">
                                            <input type="checkbox" class="permission-check-all h-3.5 w-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-700" aria-label="Check all {{ $sectionName }}">
                                            <span>Check all</span>
                                        </label>
                                    </div>
                                    <ul class="space-y-2">
                                        @foreach($routes as $routeName => $label)
                                            @php
                                                $checked = in_array($routeName, $allowedForRole, true);
                                            @endphp
                                            <li>
                                                <label class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2 transition-colors hover:bg-white dark:hover:bg-slate-800/50">
                                                    <input type="hidden" name="permissions[{{ $role }}][{{ $routeName }}]" value="0">
                                                    <input type="checkbox"
                                                        name="permissions[{{ $role }}][{{ $routeName }}]"
                                                        value="1"
                                                        {{ $checked ? 'checked' : '' }}
                                                        class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-700">
                                                    <span class="text-sm text-slate-700 dark:text-slate-300">{{ $label }}</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Save bar --}}
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" id="permissionSubmitBtn"
                    class="cursor-pointer inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save permissions
                </button>
                <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Toast after save: use query param so it works after redirect --}}
    @if(request()->has('saved'))
    <script>
    (function() {
        var msg = 'Permissions updated successfully.';
        function show() {
            if (typeof window.showSuccessToast === 'function') {
                window.showSuccessToast(msg);
                if (window.history && window.history.replaceState) {
                    var u = new URL(window.location.href);
                    u.searchParams.delete('saved');
                    window.history.replaceState({}, '', u.toString());
                }
            } else {
                setTimeout(show, 50);
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() { setTimeout(show, 150); });
        } else {
            setTimeout(show, 150);
        }
    })();
    </script>
    @endif
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function() {
            var form = document.getElementById('permissionForm');
            var btn = document.getElementById('permissionSubmitBtn');
            if (!form || !btn) return;

            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.innerHTML = '<span class="mr-2 inline-block h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white" aria-hidden="true"></span>Saving...';
            });

            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#roleSelect').select2({ width: '100%', allowClear: false });
                $('#roleSelect').on('change', function() {
                    var idx = $(this).val();
                    $('.role-panel').addClass('hidden');
                    $('#panel-' + idx).removeClass('hidden');
                    updateAllCheckAllStates();
                });
            } else {
                var roleSelect = document.getElementById('roleSelect');
                if (roleSelect) {
                    roleSelect.addEventListener('change', function() {
                        var idx = this.value;
                        document.querySelectorAll('.role-panel').forEach(function(panel) {
                            panel.classList.toggle('hidden', panel.getAttribute('data-role-panel') !== idx);
                        });
                        updateAllCheckAllStates();
                    });
                }
            }

            function updateCheckAllState(card) {
                var checkAll = card.querySelector('.permission-check-all');
                var boxes = card.querySelectorAll('input[name^="permissions"][type="checkbox"]');
                if (!checkAll || !boxes.length) return;
                var checkedCount = 0;
                boxes.forEach(function(b) { if (b.checked) checkedCount++; });
                checkAll.checked = checkedCount === boxes.length;
                checkAll.indeterminate = checkedCount > 0 && checkedCount < boxes.length;
            }

            function updateAllCheckAllStates() {
                document.querySelectorAll('.permission-section-card').forEach(updateCheckAllState);
            }

            document.querySelectorAll('.permission-check-all').forEach(function(checkAll) {
                checkAll.addEventListener('change', function() {
                    var card = this.closest('.permission-section-card');
                    if (!card) return;
                    card.querySelectorAll('input[name^="permissions"][type="checkbox"]').forEach(function(cb) {
                        cb.checked = checkAll.checked;
                    });
                });
            });

            document.querySelectorAll('.permission-section-card').forEach(function(card) {
                card.querySelectorAll('input[name^="permissions"][type="checkbox"]').forEach(function(cb) {
                    cb.addEventListener('change', function() { updateCheckAllState(card); });
                });
            });

            updateAllCheckAllStates();
        })();
    </script>
@endpush
