<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Luntian Form') - Luntian</title>
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
        /* Public standalone form must scroll; app.css locks html/body for dashboard shell. */
        html, body {
            height: auto;
            overflow: auto;
        }
    </style>
    @stack('styles')
    @include('layouts.partials.select2-theme')
</head>
<body class="overflow-x-hidden @yield('body_class', '')">
    <main class="min-h-screen bg-slate-50 p-4 dark:bg-slate-900 md:p-6">
        <div class="mx-auto w-full max-w-6xl">
            <div class="mb-4 flex flex-wrap items-center justify-end gap-3">
                <span class="text-sm font-medium text-slate-500 dark:text-slate-400 max-[480px]:sr-only">Theme</span>
                @include('layouts.partials.theme-toggle-button')
            </div>
            @yield('content')
        </div>
    </main>

    @include('layouts.partials.app-toast')
    <script>
        @include('layouts.partials.theme-toggle-script')
    </script>
    @include('partials.assignment-user-select2')
    @stack('scripts')
</body>
</html>
