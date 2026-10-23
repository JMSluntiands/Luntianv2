@extends('layouts.dashboard')

@section('title', 'Logo Management')

@section('content')
    <div class="flex flex-col">
        <div class="mb-6">
            <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Logo Management</h1>
            <p class="text-slate-500 dark:text-slate-400">Add, update, or delete the logo for each client. Clients without a logo use the default icon.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200" role="alert">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($clients as $client)
                <div class="flex flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ $client['label'] }}</h2>
                        @if($client['url'])
                            <span class="rounded-md bg-emerald-500/15 px-2 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">Custom</span>
                        @else
                            <span class="rounded-md bg-slate-200 px-2 py-0.5 text-xs font-semibold text-slate-500 dark:bg-slate-700 dark:text-slate-400">Default</span>
                        @endif
                    </div>

                    <div class="mb-4 flex h-28 items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 p-2 dark:border-slate-600 dark:bg-white">
                        @if($client['url'])
                            <img src="{{ $client['url'] }}" alt="{{ $client['label'] }} logo" class="h-full w-full object-contain">
                        @else
                            <svg class="h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-label="Default icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        @endif
                    </div>

                    <div class="mt-auto flex items-center gap-2">
                        <form action="{{ route('settings.logo_management.store', $client['key']) }}" method="POST" enctype="multipart/form-data" class="flex-1">
                            @csrf
                            <label class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M16 8l-4-4m0 0L8 8m4-4v12"/></svg>
                                {{ $client['url'] ? 'Update Logo' : 'Add Logo' }}
                                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/gif" class="sr-only" onchange="if (this.files.length) this.form.submit();">
                            </label>
                        </form>
                        @if($client['url'])
                            <form action="{{ route('settings.logo_management.destroy', $client['key']) }}" method="POST" onsubmit="return confirm('Delete the {{ addslashes($client['label']) }} logo? The default icon will be shown instead.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center justify-center gap-2 rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/30">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                    <p class="mt-2 text-xs text-slate-400">PNG, JPG, WEBP, or GIF, up to 2 MB.</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
