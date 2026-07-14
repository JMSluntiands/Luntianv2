@php
    $p = $idPrefix ?? 'fyrs_';
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
    $selectedTasks = old('tasks', $g('tasks'));
    if (is_string($selectedTasks) && $selectedTasks !== '') {
        $decoded = json_decode($selectedTasks, true);
        $selectedTasks = is_array($decoded) ? $decoded : [];
    }
    if (! is_array($selectedTasks)) {
        $selectedTasks = [];
    }
    $taskOptions = [
        'base_file' => 'Base file',
        'optimization' => 'Optimization',
        'basix' => 'BASIX',
        'duplicate' => 'Duplicate',
        'amendment' => 'Amendment',
    ];
    $staffUsers = $assignmentStaffUsers ?? collect();
    $selectedStaff = strtoupper(trim((string) old('assigned', $g('assigned') ?? $g('staff') ?? '')));
    $builderOptions = \App\Http\Controllers\FyrsJobController::BUILDERS;
    $savedBuilder = trim((string) old('builder', $g('builder') ?? $g('estate') ?? ''));
    $builderIsOther = $savedBuilder !== '' && ! in_array($savedBuilder, $builderOptions, true);
    $builderSelectValue = old('builder', $builderIsOther ? '__other__' : $savedBuilder);
    $builderOtherValue = old('builder_other', $builderIsOther ? $savedBuilder : '');
    $inputClass = 'w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/25 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100';
    $labelClass = 'mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300';
@endphp
<div class="fyrs-assessor-fields space-y-5">
  <div class="grid gap-5 sm:grid-cols-2">
    <div>
      <label for="{{ $p }}job_date" class="{{ $labelClass }}">Date</label>
      <input type="text" id="{{ $p }}job_date" readonly value="{{ now('Asia/Manila')->format('n/j/Y') }}"
        class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-600 dark:border-slate-600 dark:bg-slate-800/80 dark:text-slate-400"
        autocomplete="off">
    </div>
    <div>
      <label for="job_number" class="{{ $labelClass }}">Job Ref # <span class="text-red-500">*</span></label>
      <input type="text" id="job_number" name="job_number" required value="{{ old('job_number', $g('job_number')) }}" placeholder="e.g. Saric 0030261"
        class="{{ $inputClass }}" autocomplete="off" maxlength="100">
    </div>
  </div>

  <div class="grid gap-5 sm:grid-cols-3">
    <div>
      <label for="{{ $p }}builder" class="{{ $labelClass }}">Builder</label>
      <select id="{{ $p }}builder" name="builder" class="select2-single fyrs-builder-select {{ $inputClass }}" data-fyrs-builder-select>
        <option value="">Select builder</option>
        @foreach ($builderOptions as $builderName)
          <option value="{{ $builderName }}" {{ (string) $builderSelectValue === $builderName ? 'selected' : '' }}>{{ $builderName }}</option>
        @endforeach
        <option value="__other__" {{ (string) $builderSelectValue === '__other__' ? 'selected' : '' }}>Other (add another builder)</option>
      </select>
      <div class="fyrs-builder-other mt-2 {{ (string) $builderSelectValue === '__other__' ? '' : 'hidden' }}" data-fyrs-builder-other>
        <label for="{{ $p }}builder_other" class="{{ $labelClass }}">Other builder name</label>
        <input type="text" id="{{ $p }}builder_other" name="builder_other" value="{{ $builderOtherValue }}" placeholder="Enter builder name"
          class="{{ $inputClass }}" autocomplete="off" maxlength="255">
      </div>
    </div>
    <div>
      <label for="{{ $p }}storeys" class="{{ $labelClass }}">Storeys</label>
      <input type="text" id="{{ $p }}storeys" name="storeys" value="{{ old('storeys', $g('storeys')) }}" placeholder="e.g. 1 or 2"
        class="{{ $inputClass }}">
    </div>
    <div>
      <label for="{{ $p }}climate_zone" class="{{ $labelClass }}">Climate zone</label>
      <input type="text" id="{{ $p }}climate_zone" name="climate_zone" value="{{ old('climate_zone', $g('climate_zone')) }}" placeholder="e.g. 28"
        class="{{ $inputClass }}">
    </div>
  </div>

  <div>
    <span class="{{ $labelClass }}">Tasks</span>
    <div class="flex flex-wrap gap-4 rounded-lg border border-slate-200 bg-slate-50/80 px-4 py-3 dark:border-slate-600 dark:bg-slate-800/50">
      @foreach ($taskOptions as $taskKey => $taskLabel)
        <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
          <input type="checkbox" name="tasks[]" value="{{ $taskKey }}" {{ in_array($taskKey, $selectedTasks, true) ? 'checked' : '' }}
            class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-500 dark:bg-slate-700">
          <span>{{ $taskLabel }}</span>
        </label>
      @endforeach
    </div>
  </div>

  <div>
    <label for="fyrs-notes-body" class="{{ $labelClass }}">Notes</label>
    <input type="hidden" name="notes" id="fyrs_notes" autocomplete="off">
    <div class="overflow-hidden rounded-lg border border-slate-300 dark:border-slate-600">
      <div class="flex items-center gap-1 border-b border-slate-200 bg-slate-50 px-2 py-1.5 dark:border-slate-600 dark:bg-slate-800/80">
        <button type="button" class="fyrs-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="bold" title="Bold"><span class="font-bold">B</span></button>
        <button type="button" class="fyrs-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="italic" title="Italic"><span class="italic">I</span></button>
        <button type="button" class="fyrs-notes-btn rounded p-2 text-slate-600 transition-colors hover:bg-slate-200 dark:text-slate-400 dark:hover:bg-slate-700" data-cmd="underline" title="Underline"><span class="underline">U</span></button>
      </div>
      <div id="fyrs-notes-body" contenteditable="true" data-placeholder="DP NO, Council, Base Rating, instructions..."
        class="min-h-[140px] bg-white px-4 py-3 text-slate-800 focus:outline-none dark:bg-slate-800 dark:text-slate-100 [&:empty::before]:content-[attr(data-placeholder)] [&:empty::before]:text-slate-400 dark:[&:empty::before]:text-slate-500">{!! old('notes', $g('notes')) !!}</div>
    </div>
  </div>

  <div class="grid gap-5 sm:grid-cols-2">
    <div>
      <label for="{{ $p }}assigned" class="{{ $labelClass }}">Staff</label>
      <select id="{{ $p }}assigned" name="assigned" class="select2-single {{ $inputClass }}">
        <option value="">Select staff</option>
        @foreach ($staffUsers as $staffUser)
          @php $code = strtoupper(trim((string) ($staffUser->unique_code ?? ''))); @endphp
          @if ($code !== '')
            <option value="{{ $code }}" {{ $selectedStaff === $code ? 'selected' : '' }}>{{ $staffUser->assignmentOptionLabel() }}</option>
          @endif
        @endforeach
      </select>
    </div>
    <div>
      <label for="{{ $p }}stage" class="{{ $labelClass }}">Stage</label>
      <input type="text" id="{{ $p }}stage" name="stage" value="{{ old('stage', $g('stage')) }}" placeholder="e.g. for BASIX"
        class="{{ $inputClass }}">
    </div>
  </div>

  <div class="grid gap-5 sm:grid-cols-2">
    <div>
      <label for="{{ $p }}basix_number" class="{{ $labelClass }}">BASIX #</label>
      <input type="text" id="{{ $p }}basix_number" name="basix_number" value="{{ old('basix_number', $g('basix_number')) }}" placeholder="BASIX number"
        class="{{ $inputClass }}">
    </div>
    <div>
      <label for="{{ $p }}due_date" class="{{ $labelClass }}">Due date</label>
      <input type="date" id="{{ $p }}due_date" name="due_date" value="{{ old('due_date', $g('due_date') ? \Illuminate\Support\Carbon::parse($g('due_date'))->format('Y-m-d') : '') }}"
        class="{{ $inputClass }}">
    </div>
  </div>

  <div class="grid gap-5 sm:grid-cols-2">
    <div>
      <label for="{{ $p }}est_completion_certification" class="{{ $labelClass }} text-red-600 dark:text-red-400">Estimated completion certification</label>
      <input type="date" id="{{ $p }}est_completion_certification" name="est_completion_certification"
        value="{{ old('est_completion_certification', $g('est_completion_certification') ? \Illuminate\Support\Carbon::parse($g('est_completion_certification'))->format('Y-m-d') : '') }}"
        class="{{ $inputClass }}">
    </div>
    <div>
      <label for="{{ $p }}est_completion_basix" class="{{ $labelClass }} text-red-600 dark:text-red-400">Estimated completion BASIX</label>
      <input type="date" id="{{ $p }}est_completion_basix" name="est_completion_basix"
        value="{{ old('est_completion_basix', $g('est_completion_basix') ? \Illuminate\Support\Carbon::parse($g('est_completion_basix'))->format('Y-m-d') : '') }}"
        class="{{ $inputClass }}">
    </div>
  </div>
</div>
