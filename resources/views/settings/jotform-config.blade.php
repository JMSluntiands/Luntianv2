@extends('layouts.dashboard')

@section('title', 'Jot Form Configuration')

@section('body_class', 'page-settings-jotform-config')

@section('content')
    @php
        $webhookUrl = $config?->webhookUrl() ?? url('/webhooks/jotform');
        $isActive = (bool) ($config?->is_active ?? false);
    @endphp
    <div class="w-full max-w-3xl">
        <div class="mb-8 flex flex-wrap items-start gap-4">
            <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-orange-500/10 shadow-lg dark:bg-orange-500/20">
                <svg class="h-8 w-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h1 class="mb-1.5 text-2xl font-bold tracking-tight text-slate-800 dark:text-slate-100">Jot Form Configuration</h1>
                <p class="text-slate-500 dark:text-slate-400">Connect JotForm to LBS. Submissions create jobs that appear on the LBS job list.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200" role="alert">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 dark:border-red-800 dark:bg-red-900/20 dark:text-red-200" role="alert">
                <ul class="list-inside list-disc space-y-1 text-sm">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-600 dark:bg-slate-800/80 dark:text-slate-300">
            <p class="font-semibold text-slate-800 dark:text-slate-100">Setup in JotForm</p>
            <ol class="mt-2 list-inside list-decimal space-y-1">
                <li>Save this configuration and copy the <strong>Webhook URL</strong> below.</li>
                <li>In JotForm: Settings → Integrations → Webhooks → Add Webhook → paste the URL.</li>
                <li>Use the same field names in <strong>Field mapping</strong> as your JotForm question unique names (e.g. <code class="text-xs">referenceNo</code>, <code class="text-xs">jobAddress</code>).</li>
                <li>Turn integration <strong>ON</strong> when ready.</li>
            </ol>
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Jobs land in <strong>Forms Submitted Jobs</strong> on the LBS list (same as the public add form) unless you choose direct main list below.</p>
            <p class="mt-2 rounded-lg border border-amber-200 bg-amber-50/80 px-3 py-2 text-xs text-amber-900 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-100"><strong>Important:</strong> Paste the Webhook URL in JotForm → <strong>Settings → Integrations → Webhooks</strong>. The <em>Publish / Share link</em> (<code class="text-xs">form.jotform.com/…</code>) is not the webhook. After each submit, check <code class="text-xs">storage/logs/jotform.log</code> on the server.</p>
        </div>

        @if(!$isActive)
            <div class="mb-6 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-100" role="alert">
                <strong>Integration is OFF.</strong> JotForm submissions will not enter LUNTIAN until you check <strong>Enable JotForm integration</strong> below and save.
            </div>
        @endif

        <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">JotForm integration</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Status: <span class="font-semibold {{ $isActive ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">{{ $isActive ? 'ON — accepting webhooks' : 'OFF — webhooks rejected' }}</span></p>
                </div>
                @if($config && \App\Models\RolePermission::userMayAccessRoute('settings.jotform_config.toggle'))
                    <form action="{{ route('settings.jotform_config.toggle') }}" method="POST">
                        @csrf
                        <input type="hidden" name="is_active" value="{{ $isActive ? '0' : '1' }}">
                        <button type="submit" class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold {{ $isActive ? 'bg-red-600 text-white hover:bg-red-500' : 'bg-emerald-600 text-white hover:bg-emerald-500' }}">
                            {{ $isActive ? 'Turn OFF' : 'Turn ON' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <form id="jotformConfigForm" action="{{ route('settings.jotform_config.store') }}" method="POST" autocomplete="off" class="space-y-6">
            @csrf

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                <div class="border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Webhook</h2>
                </div>
                <div class="space-y-5 p-5">
                    <label class="inline-flex cursor-pointer items-center gap-2.5 rounded-lg border border-emerald-200 bg-emerald-50/80 px-4 py-3 dark:border-emerald-800 dark:bg-emerald-900/20">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $isActive) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 dark:border-slate-500 dark:bg-slate-700">
                        <span class="text-sm font-medium text-slate-800 dark:text-slate-100">Enable JotForm integration (required for submissions to enter LBS)</span>
                    </label>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Webhook URL (paste in JotForm)</label>
                        <div class="flex gap-2">
                            <input type="text" id="jotform_webhook_url" readonly value="{{ $webhookUrl }}"
                                class="w-full cursor-text rounded-lg border border-slate-300 bg-slate-50 px-4 py-2.5 font-mono text-xs text-slate-800 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                            <button type="button" id="copyJotformWebhook" class="shrink-0 rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">Copy</button>
                        </div>
                    </div>
                    <div>
                        <label for="jotform_form_id" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">JotForm Form ID (optional)</label>
                        <input type="text" id="jotform_form_id" name="jotform_form_id" value="{{ old('jotform_form_id', $config?->jotform_form_id ?? '') }}" placeholder="e.g. 241234567890123"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Leave empty to accept any form. Set to restrict webhook to one form only.</p>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Submissions are added directly to the main LBS job list.</p>
                    @if($config)
                        <label class="inline-flex cursor-pointer items-center gap-2.5">
                            <input type="checkbox" name="regenerate_secret" value="1" class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500 dark:border-slate-500 dark:bg-slate-700">
                            <span class="text-sm text-slate-700 dark:text-slate-300">Regenerate webhook secret (update JotForm URL after saving)</span>
                        </label>
                    @endif
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800/50">
                <div class="border-b border-slate-200 bg-slate-50/80 px-5 py-4 dark:border-slate-700 dark:bg-slate-800/80">
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Field mapping</h2>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Map only the fields on your JotForm. Leave a row blank if that field is not on the form — LBS will leave the matching column empty.</p>
                    <p class="mt-2 rounded-lg border border-sky-200 bg-sky-50/80 px-3 py-2 text-xs text-sky-900 dark:border-sky-800 dark:bg-sky-900/20 dark:text-sky-100">
                        <strong>Form 252677783982477 — use these unique names:</strong>
                        LBS Ref = <code class="text-xs">lbsRef116</code> (not lbsRef118),
                        Client Ref = <code class="text-xs">clientRef113</code>,
                        Account Client = <code class="text-xs">accountClient</code>,
                        NCC = <code class="text-xs">nccCompliance</code>,
                        Address = <code class="text-xs">jobAddress</code>,
                        Job Type = <code class="text-xs">jobType</code>,
                        Priority = <code class="text-xs">priority</code>,
                        Staff = <code class="text-xs">staffInitials</code>,
                        Status = <code class="text-xs">jobStatus</code>,
                        Notes = <code class="text-xs">notes</code>,
                        Plans = <code class="text-xs">uploadPlans</code>,
                        Docs = <code class="text-xs">uploadDocuments</code>.
                        Short names like <code class="text-xs">lbsRef</code> also work.
                    </p>
                </div>
                <div class="grid gap-5 p-5 sm:grid-cols-2">
                    <div>
                        <label for="map_reference_no" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">LBS Ref#</label>
                        <input type="text" id="map_reference_no" name="map_reference_no" value="{{ old('map_reference_no', $config?->map_reference_no ?? 'lbsRef') }}" placeholder="e.g. lbsRef"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="map_client_reference" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Client Ref#</label>
                        <input type="text" id="map_client_reference" name="map_client_reference" value="{{ old('map_client_reference', $config?->map_client_reference ?? 'clientRef') }}" placeholder="e.g. clientRef"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="map_compliance" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">NCC Compliance</label>
                        <input type="text" id="map_compliance" name="map_compliance" value="{{ old('map_compliance', $config?->map_compliance ?? 'nccCompliance') }}" placeholder="e.g. nccCompliance"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="map_client" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Account Client</label>
                        <input type="text" id="map_client" name="map_client" value="{{ old('map_client', $config?->map_client ?? 'accountClient') }}" placeholder="e.g. accountClient"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="map_job_address" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Job Address</label>
                        <input type="text" id="map_job_address" name="map_job_address" value="{{ old('map_job_address', $config?->map_job_address ?? 'jobAddress') }}" placeholder="e.g. jobAddress"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="map_job_type" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Job Type</label>
                        <input type="text" id="map_job_type" name="map_job_type" value="{{ old('map_job_type', $config?->map_job_type ?? 'jobType') }}" placeholder="e.g. jobType"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="map_priority" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Priority</label>
                        <input type="text" id="map_priority" name="map_priority" value="{{ old('map_priority', $config?->map_priority ?? 'priority') }}" placeholder="e.g. priority"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="map_assigned_to" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Staff Initials <span class="font-normal text-slate-400">(optional)</span></label>
                        <input type="text" id="map_assigned_to" name="map_assigned_to" value="{{ old('map_assigned_to', $config?->map_assigned_to ?? 'staffInitials') }}" placeholder="e.g. staffInitials"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="map_checked_by" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Checker Initials <span class="font-normal text-slate-400">(optional)</span></label>
                        <input type="text" id="map_checked_by" name="map_checked_by" value="{{ old('map_checked_by', $config?->map_checked_by ?? '') }}" placeholder="Leave blank to use Staff Initials for checker too"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">If your JotForm only has Staff, leave this empty — checker will copy staff automatically.</p>
                    </div>
                    <div>
                        <label for="map_job_status" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Job Status <span class="font-normal text-slate-400">(optional)</span></label>
                        <input type="text" id="map_job_status" name="map_job_status" value="{{ old('map_job_status', $config?->map_job_status ?? '') }}" placeholder="e.g. jobStatus — leave blank if not on form"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="map_notes" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Notes</label>
                        <input type="text" id="map_notes" name="map_notes" value="{{ old('map_notes', $config?->map_notes ?? 'notes') }}" placeholder="e.g. notes"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="map_upload_plans" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Upload Plans</label>
                        <input type="text" id="map_upload_plans" name="map_upload_plans" value="{{ old('map_upload_plans', $config?->map_upload_plans ?? 'uploadPlans') }}" placeholder="e.g. uploadPlans"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                    <div>
                        <label for="map_upload_documents" class="mb-1.5 block text-sm font-medium text-slate-700 dark:text-slate-300">Upload Documents</label>
                        <input type="text" id="map_upload_documents" name="map_upload_documents" value="{{ old('map_upload_documents', $config?->map_upload_documents ?? 'uploadDocuments') }}" placeholder="e.g. uploadDocuments"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" id="jotformConfigSubmitBtn" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500">
                    {{ $config ? 'Update configuration' : 'Save configuration' }}
                </button>
                <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('copyJotformWebhook')?.addEventListener('click', function() {
            var el = document.getElementById('jotform_webhook_url');
            if (!el) return;
            el.select();
            navigator.clipboard.writeText(el.value).catch(function() {});
        });
        var form = document.getElementById('jotformConfigForm');
        var btn = document.getElementById('jotformConfigSubmitBtn');
        if (form && btn) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                btn.textContent = 'Saving...';
            });
        }
    </script>
@endpush
