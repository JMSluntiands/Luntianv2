@php
    if (old('is_employee') !== null) {
        $isEmployee = in_array((string) old('is_employee'), ['1', 'true', 'on'], true);
    } else {
        $isEmployee = isset($user) ? (bool) ($user->is_employee ?? true) : true;
    }
@endphp
<div class="md:col-span-2">
    <p class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Account type</p>
    <div class="inline-flex overflow-hidden rounded-lg border border-slate-200 bg-slate-50 p-0.5 dark:border-slate-600 dark:bg-slate-900/40" role="group" aria-label="Account type">
        <label class="cursor-pointer">
            <input type="radio" name="is_employee" value="1" class="peer sr-only account-type-radio" @checked($isEmployee)>
            <span class="inline-flex items-center gap-2 rounded-md px-3.5 py-2 text-sm font-semibold text-slate-600 transition-colors peer-checked:bg-white peer-checked:text-emerald-700 peer-checked:shadow-sm dark:text-slate-300 dark:peer-checked:bg-slate-700 dark:peer-checked:text-emerald-300">
                <svg class="h-4 w-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                Employee
            </span>
        </label>
        <label class="cursor-pointer">
            <input type="radio" name="is_employee" value="0" class="peer sr-only account-type-radio" @checked(! $isEmployee)>
            <span class="inline-flex items-center gap-2 rounded-md px-3.5 py-2 text-sm font-semibold text-slate-600 transition-colors peer-checked:bg-white peer-checked:text-amber-700 peer-checked:shadow-sm dark:text-slate-300 dark:peer-checked:bg-slate-700 dark:peer-checked:text-amber-300">
                <svg class="h-4 w-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Dummy account
            </span>
        </label>
    </div>
    <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">
        Only <strong>Employee</strong> accounts appear on the Timesheet leave overview. Dummy accounts stay hidden there.
    </p>
</div>
