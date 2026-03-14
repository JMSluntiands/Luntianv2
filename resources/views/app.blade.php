<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logo-url" content="{{ asset('storage/logo-light.png') }}">
    <title>{{ config('app.name', 'BLIUNQ') }}</title>
    <style>
        html, body { margin: 0; min-height: 100vh; overflow-x: hidden; }
        html { background-color: #1A2534; }
        html[data-theme="light"] { background-color: #f3f4f6; }
        body { background-color: inherit; }
        #app { min-height: 100vh; }
    </style>
    <script>
        (function(){
            var t = localStorage.getItem('theme');
            document.documentElement.setAttribute('data-theme', t === 'light' ? 'light' : 'dark');
        })();
    </script>
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.tsx'])
</head>
<body>
    <div id="app">
        <div style="display:flex;min-height:100vh;align-items:center;justify-content:center;color:#94B4C1;font-family:system-ui,sans-serif;">
            Loading…
        </div>
    </div>
</body>
</html>
