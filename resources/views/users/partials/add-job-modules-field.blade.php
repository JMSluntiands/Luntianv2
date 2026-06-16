@php
    $moduleOptions = \App\Support\AddJobModules::options();
    $selectedStaffModules = old('add_job_staff_modules', $user->add_job_staff_modules ?? []);
    $selectedCheckerModules = old('add_job_checker_modules', $user->add_job_checker_modules ?? []);
    if (! is_array($selectedStaffModules)) {
        $selectedStaffModules = [];
    }
    if (! is_array($selectedCheckerModules)) {
        $selectedCheckerModules = [];
    }
@endphp
<div class="md:col-span-2 space-y-5">
    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Add New Job — Staff (Assigned To)</label>
        <p class="mb-2 text-xs text-slate-500 dark:text-slate-400">
            Modules where this user appears in <strong>Assigned To</strong> on Add New Job forms.
        </p>
        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($moduleOptions as $key => $label)
                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-slate-50/80 px-3 py-2 text-sm text-slate-700 transition-colors hover:border-blue-300 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200 dark:hover:border-blue-600">
                    <input
                        type="checkbox"
                        name="add_job_staff_modules[]"
                        value="{{ $key }}"
                        class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 dark:border-slate-500 dark:bg-slate-700"
                        @checked(in_array($key, $selectedStaffModules, true))
                    >
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Add New Job — Checker (Checked By)</label>
        <p class="mb-2 text-xs text-slate-500 dark:text-slate-400">
            Modules where this user appears in <strong>Checked By</strong> on Add New Job forms.
        </p>
        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($moduleOptions as $key => $label)
                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-slate-50/80 px-3 py-2 text-sm text-slate-700 transition-colors hover:border-violet-300 dark:border-slate-600 dark:bg-slate-800/50 dark:text-slate-200 dark:hover:border-violet-600">
                    <input
                        type="checkbox"
                        name="add_job_checker_modules[]"
                        value="{{ $key }}"
                        class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500 dark:border-slate-500 dark:bg-slate-700"
                        @checked(in_array($key, $selectedCheckerModules, true))
                    >
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>
</div>
