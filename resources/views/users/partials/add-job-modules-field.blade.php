@php
    $moduleOptions = \App\Support\AddJobModules::options();
    $selectedModules = old('add_job_modules', $user->add_job_modules ?? []);
    if (! is_array($selectedModules)) {
        $selectedModules = [];
    }
@endphp
<div class="md:col-span-2">
    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Add New Job modules</label>
    <p class="mb-2 text-xs text-slate-500 dark:text-slate-400">
        Choose where this user appears in <strong>Assigned To</strong> / <strong>Checked By</strong> on Add New Job forms only.
        User must have at least one module checked to appear in that module's assignment dropdown.
    </p>
    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($moduleOptions as $key => $label)
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-slate-50/80 px-3 py-2 text-sm text-slate-700 transition-colors hover:border-emerald-300 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200 dark:hover:border-emerald-600">
                <input
                    type="checkbox"
                    name="add_job_modules[]"
                    value="{{ $key }}"
                    class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-500 dark:bg-slate-700"
                    @checked(in_array($key, $selectedModules, true))
                >
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>
</div>
