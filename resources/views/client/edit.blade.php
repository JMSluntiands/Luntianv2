@extends('layouts.dashboard')

@section('title', 'Edit Client')

@section('body_class', 'page-client-edit')

@section('content')
    <div class="w-full">
        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-emerald-500/20 shadow-lg dark:bg-emerald-500/30">
                <svg class="h-8 w-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Edit Client</h1>
                <p class="text-slate-600 dark:text-slate-400">Update client account #{{ $client->client_account_id }}.</p>
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

        <form action="{{ route('client.update', $client) }}" method="POST" class="space-y-6" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
                <div class="max-w-xl space-y-4">
                    <div>
                        <label for="client_account_name" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Client name</label>
                        <input
                            type="text"
                            id="client_account_name"
                            name="client_account_name"
                            value="{{ old('client_account_name', $client->client_account_name) }}"
                            autocomplete="off"
                            placeholder="e.g. Company Name, Client Name"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-800 placeholder-slate-400 transition-colors focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100 dark:placeholder-slate-500 dark:focus:ring-emerald-500/30"
                        >
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('client.index') }}" class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:focus:ring-offset-slate-800">Cancel</a>
                <button type="submit" id="clientSaveBtn" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-70 dark:focus:ring-offset-slate-900">
                    <span class="btn-text">Update</span>
                    <span class="btn-spinner hidden h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent" aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            var form = document.querySelector('form[action="{{ route('client.update', $client) }}"]');
            var btn = document.getElementById('clientSaveBtn');
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
