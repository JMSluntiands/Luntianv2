@php
    $p = $idPrefix ?? 'bph_add_';
    $v = $values ?? [];
    $g = function (string $key) use ($v) {
        if (is_object($v) && isset($v->{$key})) {
            return $v->{$key};
        }
        if (is_array($v) && array_key_exists($key, $v)) {
            return $v[$key];
        }

        return null;
    };
    $dateDisplay = $g('date');
    if ($dateDisplay) {
        try {
            $dateDisplay = \Carbon\Carbon::parse($dateDisplay)->timezone('Asia/Manila')->format('Y-m-d');
        } catch (\Throwable) {
            $dateDisplay = (string) $dateDisplay;
        }
    } else {
        $dateDisplay = '';
    }
@endphp
<div class="bph-additional-fields space-y-6">
    <div>
        <label for="{{ $p }}job_bph_date" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Date</label>
        <input type="text" id="{{ $p }}job_bph_date" readonly value="{{ $dateDisplay }}"
            class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-600 dark:border-slate-600 dark:bg-slate-800/80 dark:text-slate-400"
            autocomplete="off">
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Date will be automatically set when saved</p>
    </div>
    <div>
        <label for="{{ $p }}job_address" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Address</label>
        <textarea id="{{ $p }}job_address" name="job_address" rows="3" placeholder="Enter address"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('job_address', $g('address')) }}</textarea>
    </div>
    <div>
        <label for="{{ $p }}climate_zone" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Climate Zone</label>
        <input type="text" id="{{ $p }}climate_zone" name="climate_zone" value="{{ old('climate_zone', $g('climate_zone')) }}" placeholder="Enter climate zone"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
    </div>
    <div class="space-y-2">
        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Compliance Summary</h3>
        <label for="{{ $p }}compliance_summary_description" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Description</label>
        <textarea id="{{ $p }}compliance_summary_description" name="compliance_summary_description" rows="3" placeholder="Enter compliance summary description"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('compliance_summary_description', $g('compliance_summary_description')) }}</textarea>
    </div>
    <div class="space-y-2">
        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Specification Requirements</h3>
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="{{ $p }}spec_client_no" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Client No</label>
                <input type="text" id="{{ $p }}spec_client_no" name="spec_client_no" value="{{ old('spec_client_no', $g('spec_client_no')) }}" placeholder="Enter client number"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            </div>
            <div>
                <label for="{{ $p }}spec_lbs_no" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">LBS No</label>
                <input type="text" id="{{ $p }}spec_lbs_no" name="spec_lbs_no" value="{{ old('spec_lbs_no', $g('spec_lbs_no')) }}" placeholder="Enter LBS number"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            </div>
        </div>
    </div>
    <div>
        <label for="{{ $p }}spec_plans" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Plans</label>
        <textarea id="{{ $p }}spec_plans" name="spec_plans" rows="3" placeholder="Enter plans details"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('spec_plans', $g('spec_plans')) }}</textarea>
    </div>
    <div>
        <label for="{{ $p }}spec_insulation" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Insulation</label>
        <textarea id="{{ $p }}spec_insulation" name="spec_insulation" rows="3" placeholder="Enter insulation details"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('spec_insulation', $g('spec_insulation')) }}</textarea>
    </div>
    <div>
        <label for="{{ $p }}spec_glazing" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Glazing</label>
        <textarea id="{{ $p }}spec_glazing" name="spec_glazing" rows="3" placeholder="Enter glazing details"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('spec_glazing', $g('spec_glazing')) }}</textarea>
    </div>
    <div>
        <label for="{{ $p }}spec_sealing" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Sealing</label>
        <textarea id="{{ $p }}spec_sealing" name="spec_sealing" rows="3" placeholder="Enter sealing details"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('spec_sealing', $g('spec_sealing')) }}</textarea>
    </div>
    <div>
        <label for="{{ $p }}spec_services" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Services</label>
        <textarea id="{{ $p }}spec_services" name="spec_services" rows="3" placeholder="Enter services details"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('spec_services', $g('spec_services')) }}</textarea>
    </div>
    <div>
        <label for="{{ $p }}spec_additional" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Additional</label>
        <textarea id="{{ $p }}spec_additional" name="spec_additional" rows="3" placeholder="Enter additional information"
            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">{{ old('spec_additional', $g('spec_additional')) }}</textarea>
    </div>
    <div>
        <label for="{{ $p }}spec_print_merge_file" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Upload PDF or Image (for print merge)</label>
        <input type="file" id="{{ $p }}spec_print_merge_file" name="spec_print_merge_file" accept=".pdf,.jpg,.jpeg,.png,.gif,.bmp,.webp"
            class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-emerald-600 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-emerald-500 dark:text-slate-400">
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Upload a PDF or image file (JPG, PNG, GIF, BMP, WEBP) that will be merged as the next page when printing.</p>
        @if(!empty($mergeFileDownloadUrl) && !empty($mergeFileLabel))
            <p class="mt-2 text-xs text-slate-600 dark:text-slate-400">Current file: <a href="{{ $mergeFileDownloadUrl }}" class="font-medium text-emerald-600 underline hover:no-underline dark:text-emerald-400" target="_blank" rel="noopener">{{ $mergeFileLabel }}</a></p>
        @endif
    </div>
</div>
