@extends('layouts.dashboard')

@section('title', 'BPH Job View')

@section('body_class', 'page-bph-view')

@section('content')
    <div class="w-full max-w-4xl">
        <div class="mb-6 flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">BPH Job View</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Reference: <span class="font-mono">{{ $job->reference ?? '—' }}</span></p>
            </div>
            <a href="{{ route('bph.list') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">Back to list</a>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-700/60 dark:bg-emerald-900/20 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-700/60 dark:bg-red-900/20 dark:text-red-300">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('bph.update', $job->id) }}" method="POST" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800/60">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Urgent</label>
                    <select name="urgent" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                        <option value="NO" {{ old('urgent', $job->urgent) === 'NO' ? 'selected' : '' }}>NO</option>
                        <option value="YES" {{ old('urgent', $job->urgent) === 'YES' ? 'selected' : '' }}>YES</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                    @php
                        $selStatus = (string) old('status', $job->status);
                        $statusOptions = ['Pending', 'Accepted', 'Allocated', 'Awaiting Further Information', 'Completed', 'For Review', 'Revised', 'For Checking', 'Processing'];
                    @endphp
                    <select name="status" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100" data-placeholder="Select status">
                        @if($selStatus !== '' && !collect($statusOptions)->contains($selStatus))
                            <option value="{{ $selStatus }}" selected>{{ $selStatus }}</option>
                        @endif
                        @foreach($statusOptions as $opt)
                            <option value="{{ $opt }}" {{ $selStatus === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Job Type</label>
                    @php
                        $selJobType = (string) old('job_type', $job->job_type);
                    @endphp
                    <select name="job_type" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100" data-placeholder="Select job type">
                        @if($selJobType !== '' && !collect($jobRequests ?? [])->pluck('job_request_type')->contains($selJobType))
                            <option value="{{ $selJobType }}" selected>{{ $selJobType }}</option>
                        @endif
                        @foreach(($jobRequests ?? collect()) as $jr)
                            <option value="{{ $jr->job_request_type }}" {{ $selJobType === (string) $jr->job_request_type ? 'selected' : '' }}>{{ $jr->job_request_type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">NCC</label>
                    @php
                        $selNcc = (string) old('ncc', $job->ncc);
                    @endphp
                    <select name="ncc" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100" data-placeholder="Select NCC">
                        @if($selNcc !== '' && !collect($compliances ?? [])->pluck('column')->contains($selNcc))
                            <option value="{{ $selNcc }}" selected>{{ $selNcc }}</option>
                        @endif
                        @foreach(($compliances ?? collect()) as $c)
                            <option value="{{ $c->column }}" {{ $selNcc === (string) $c->column ? 'selected' : '' }}>{{ $c->column }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Job Number</label>
                    <input type="text" name="job_number" maxlength="6" value="{{ old('job_number', $job->job_number) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Client Name</label>
                    <input type="text" name="client_name" value="{{ old('client_name', $job->client_name) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Contact Email</label>
                    @php
                        $selContactEmail = (string) old('contact_email', $job->contact_email);
                    @endphp
                    <select name="contact_email" class="select2-single w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100" data-placeholder="Select contact email">
                        @if($selContactEmail !== '' && !collect($bphClientEmails ?? [])->pluck('email')->contains($selContactEmail))
                            <option value="{{ $selContactEmail }}" selected>{{ $selContactEmail }}</option>
                        @endif
                        @foreach(($bphClientEmails ?? collect()) as $row)
                            <option value="{{ $row->email }}" {{ $selContactEmail === (string) $row->email ? 'selected' : '' }}>{{ $row->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Assigned</label>
                        @php
                            $selAssigned = strtoupper((string) old('assigned', $job->assigned));
                        @endphp
                        <select name="assigned" class="select2-single assignment-user-select w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                            @include('partials.assignment-user-options', ['assignmentUsers' => $assignmentStaffUsers ?? [], 'selected' => $selAssigned, 'includeSelectPlaceholder' => true, 'preserveSelected' => true, 'includeGm' => false])
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Checked</label>
                        @php
                            $selChecked = strtoupper((string) old('checked', $job->checked));
                        @endphp
                        <select name="checked" class="select2-single assignment-user-select w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">
                            @include('partials.assignment-user-options', ['assignmentUsers' => $assignmentCheckerUsers ?? [], 'selected' => $selChecked, 'includeSelectPlaceholder' => true, 'preserveSelected' => true, 'includeGm' => false])
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Notes</label>
                <textarea name="notes" rows="5" class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100">{{ old('notes', $job->notes) }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-500">Save Changes</button>
                <span class="text-xs text-slate-500 dark:text-slate-400">ID: {{ $job->id }}</span>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        (function () {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2-single:not(.assignment-user-select)').each(function () {
                    var $el = $(this);
                    var ph = $el.data('placeholder');
                    var opts = { width: '100%', allowClear: false };
                    if (ph) {
                        opts.placeholder = ph;
                        opts.allowClear = true;
                        opts.minimumResultsForSearch = 0;
                    }
                    $el.select2(opts);
                });
                initAssignmentUserSelect2($(document));
            }
        })();
    </script>
@endpush
