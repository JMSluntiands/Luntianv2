<?php

namespace App\Http\Controllers;

use App\Models\Compliance;
use App\Models\JobModuleClient;
use App\Models\JobRequest;
use App\Models\Priority;
use App\Models\StandaloneFormSetting;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StandaloneFormSettingsController extends Controller
{
    public function index(): View
    {
        return view('settings.standalone-form', [
            'sidebar_active' => 'settings.standalone_form',
            'forms' => $this->forms(),
            'toggleUrl' => route('settings.standalone_form.toggle'),
            'optionUrl' => route('settings.standalone_form.options'),
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $formKey = (string) $request->input('form_key', '');
        $allowedFields = array_column($this->fieldDefinitions()[$formKey] ?? [], 'key');

        $data = $request->validate([
            'form_key' => ['required', 'string', Rule::in(array_keys($this->fieldDefinitions()))],
            'field_key' => ['required', 'string', Rule::in($allowedFields)],
            'is_required' => ['sometimes', 'boolean'],
            'is_visible' => ['sometimes', 'boolean'],
        ]);

        if (! array_key_exists('is_required', $data) && ! array_key_exists('is_visible', $data)) {
            return response()->json(['message' => 'Nothing to update.'], 422);
        }

        $existing = StandaloneFormSetting::query()
            ->where('form_key', $data['form_key'])
            ->where('field_key', $data['field_key'])
            ->first();

        $isRequired = array_key_exists('is_required', $data)
            ? (bool) $data['is_required']
            : (bool) ($existing->is_required ?? false);
        $isVisible = array_key_exists('is_visible', $data)
            ? (bool) $data['is_visible']
            : (bool) ($existing->is_visible ?? true);

        StandaloneFormSetting::query()->updateOrCreate(
            [
                'form_key' => $data['form_key'],
                'field_key' => $data['field_key'],
            ],
            [
                'is_required' => $isRequired,
                'is_visible' => $isVisible,
            ]
        );

        return response()->json([
            'status' => 'success',
            'form_key' => $data['form_key'],
            'field_key' => $data['field_key'],
            'is_required' => $isRequired,
            'is_visible' => $isVisible,
        ]);
    }

    public function saveOption(Request $request): JsonResponse
    {
        $formKey = (string) $request->input('form_key', '');
        $fieldKey = (string) $request->input('field_key', '');
        if (! $this->isDropdownField($formKey, $fieldKey)) {
            return response()->json(['message' => 'This field has no dropdown options.'], 422);
        }

        $data = $request->validate([
            'form_key' => ['required', 'string'],
            'field_key' => ['required', 'string'],
            'action' => ['required', 'string', Rule::in(['add', 'update', 'delete'])],
            'id' => ['nullable', 'integer'],
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        $label = trim((string) ($data['label'] ?? ''));
        if (in_array($data['action'], ['add', 'update'], true) && $label === '') {
            return response()->json(['message' => 'Enter a name.'], 422);
        }

        try {
            if ($data['action'] === 'add') {
                $this->addDropdownOption($formKey, $fieldKey, $label);
            } elseif ($data['action'] === 'update') {
                $this->updateDropdownOption($formKey, $fieldKey, (int) ($data['id'] ?? 0), $label);
            } else {
                $this->deleteDropdownOption($formKey, $fieldKey, (int) ($data['id'] ?? 0));
            }
        } catch (QueryException $e) {
            return response()->json(['message' => 'That option is still used, so it cannot be removed.'], 422);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'status' => 'success',
            'options' => $this->optionsFor($formKey, $fieldKey),
        ]);
    }

    /**
     * @return list<array{key: string, name: string, fields: list<array{key: string, label: string, is_required: bool}>}>
     */
    private function forms(): array
    {
        $urls = [
            'lbs' => $this->publicUrl((string) config('app.lbs_public_form_domain', ''), '/lbs/add-new'),
            'general_assembly' => $this->publicUrl((string) config('app.gen_ea_public_form_domain', ''), '/general-assembly/add-new'),
            'fyrs' => $this->publicUrl(
                trim((string) config('app.fyrs_public_form_domain', '')),
                trim((string) config('app.fyrs_public_form_domain', '')) !== '' ? '/' : '/fyrs/add-new'
            ),
        ];

        $forms = [];
        foreach ($this->fieldDefinitions() as $key => $fields) {
            $required = StandaloneFormSetting::requiredMap($key);
            $visible = StandaloneFormSetting::visibleMap($key);
            $forms[] = [
                'key' => $key,
                'name' => match ($key) {
                    'lbs' => 'LBS',
                    'general_assembly' => 'Generic EA',
                    'fyrs' => 'FYRS',
                    default => $key,
                },
                'url' => $urls[$key] ?? '',
                'fields' => array_map(function (array $field) use ($required, $visible, $key) {
                    $field['is_required'] = (bool) ($required[$field['key']] ?? false);
                    $field['is_visible'] = (bool) ($visible[$field['key']] ?? true);
                    $field['options'] = $this->isDropdownField($key, $field['key'])
                        ? $this->optionsFor($key, $field['key'])
                        : null;

                    return $field;
                }, $fields),
            ];
        }

        return $forms;
    }

    private function publicUrl(string $domain, string $path): string
    {
        $domain = trim($domain);
        if ($domain === '') {
            return url($path);
        }

        $domain = preg_replace('#^https?://#i', '', $domain) ?? $domain;
        $domain = rtrim($domain, '/');

        return 'https://'.$domain.($path === '/' ? '/' : $path);
    }

    private function isDropdownField(string $formKey, string $fieldKey): bool
    {
        return in_array($fieldKey, ['compliance', 'priority'], true)
            && in_array($formKey, ['lbs', 'general_assembly'], true);
    }

    /**
     * @return list<array{id: int, label: string}>
     */
    private function optionsFor(string $formKey, string $fieldKey): array
    {
        if ($fieldKey === 'compliance') {
            return Compliance::query()
                ->orderBy('column')
                ->get()
                ->map(fn (Compliance $row) => [
                    'id' => (int) $row->id,
                    'label' => trim((string) $row->column),
                ])
                ->filter(fn (array $row) => $row['label'] !== '')
                ->values()
                ->all();
        }

        if ($fieldKey === 'priority') {
            return Priority::query()
                ->orderBy('id')
                ->get()
                ->map(fn (Priority $row) => [
                    'id' => (int) $row->id,
                    'label' => trim((string) $row->name),
                ])
                ->filter(fn (array $row) => $row['label'] !== '')
                ->values()
                ->all();
        }

        return $this->jobRequestsForForm($formKey)
            ->map(fn (JobRequest $row) => [
                'id' => (int) $row->id,
                'label' => trim((string) $row->job_request_type),
            ])
            ->filter(fn (array $row) => $row['label'] !== '')
            ->values()
            ->all();
    }

    private function addDropdownOption(string $formKey, string $fieldKey, string $label): void
    {
        $this->assertUniqueLabel($formKey, $fieldKey, $label, null);

        if ($fieldKey === 'compliance') {
            Compliance::query()->create(['column' => $label]);

            return;
        }

        if ($fieldKey === 'priority') {
            Priority::query()->create(['name' => $label]);

            return;
        }

        $clientCode = $this->jobRequestClientCode($formKey);
        JobRequest::query()->create([
            'client_code' => $clientCode,
            'job_request_id' => $this->nextJobRequestCode($clientCode, $label),
            'job_request_type' => $label,
        ]);
    }

    private function updateDropdownOption(string $formKey, string $fieldKey, int $id, string $label): void
    {
        if ($id < 1) {
            throw new \InvalidArgumentException('Choose an option to edit.');
        }

        $this->assertUniqueLabel($formKey, $fieldKey, $label, $id);

        if ($fieldKey === 'compliance') {
            $row = Compliance::query()->find($id);
            if (! $row) {
                throw new \InvalidArgumentException('That option was not found.');
            }
            $row->update(['column' => $label]);

            return;
        }

        if ($fieldKey === 'priority') {
            $row = Priority::query()->find($id);
            if (! $row) {
                throw new \InvalidArgumentException('That option was not found.');
            }
            $row->update(['name' => $label]);

            return;
        }

        $row = $this->jobRequestsForForm($formKey)->firstWhere('id', $id);
        if (! $row) {
            throw new \InvalidArgumentException('That option was not found.');
        }
        $row->update(['job_request_type' => $label]);
    }

    private function deleteDropdownOption(string $formKey, string $fieldKey, int $id): void
    {
        if ($id < 1) {
            throw new \InvalidArgumentException('Choose an option to delete.');
        }

        if ($fieldKey === 'compliance') {
            $row = Compliance::query()->find($id);
        } elseif ($fieldKey === 'priority') {
            $row = Priority::query()->find($id);
        } else {
            $row = $this->jobRequestsForForm($formKey)->firstWhere('id', $id);
        }

        if (! $row) {
            throw new \InvalidArgumentException('That option was not found.');
        }

        $row->delete();
    }

    private function assertUniqueLabel(string $formKey, string $fieldKey, string $label, ?int $ignoreId): void
    {
        foreach ($this->optionsFor($formKey, $fieldKey) as $option) {
            if ($ignoreId !== null && (int) $option['id'] === $ignoreId) {
                continue;
            }
            if (strcasecmp($option['label'], $label) === 0) {
                throw new \InvalidArgumentException('That option is already in the dropdown.');
            }
        }
    }

    private function jobRequestClientCode(string $formKey): string
    {
        return JobModuleClient::codeFor($formKey === 'general_assembly' ? 'general_assembly' : 'lbs');
    }

    private function jobRequestsForForm(string $formKey)
    {
        $clientCode = $this->jobRequestClientCode($formKey);

        return JobRequest::query()
            ->whereRaw('UPPER(TRIM(client_code)) = ?', [strtoupper($clientCode)])
            ->orderBy('job_request_type')
            ->get();
    }

    private function nextJobRequestCode(string $clientCode, string $label): string
    {
        $slug = strtoupper((string) preg_replace('/[^A-Za-z0-9]+/', '', $label));
        $slug = $slug !== '' ? substr($slug, 0, 16) : 'TYPE';
        $base = 'EA_'.strtoupper($clientCode).'_'.$slug;
        $code = substr($base, 0, 50);
        $n = 2;
        while (JobRequest::query()->where('job_request_id', $code)->exists()) {
            $suffix = '_'.$n;
            $code = substr($base, 0, 50 - strlen($suffix)).$suffix;
            $n++;
        }

        return $code;
    }

    /**
     * @return array<string, list<array{key: string, label: string}>>
     */
    private function fieldDefinitions(): array
    {
        $jobFields = [
            ['key' => 'client_reference', 'label' => 'Client Reference'],
            ['key' => 'compliance', 'label' => 'Compliance'],
            ['key' => 'job_address', 'label' => 'Job Address'],
            ['key' => 'priority', 'label' => 'Priority'],
            ['key' => 'job_type', 'label' => 'Job Status'],
            ['key' => 'notes', 'label' => 'Notes'],
            ['key' => 'plans', 'label' => 'Plans'],
            ['key' => 'documents', 'label' => 'Documents'],
            ['key' => 'assigned_to', 'label' => 'Assigned To'],
            ['key' => 'checked_by', 'label' => 'Checked By'],
        ];

        return [
            'lbs' => array_merge(
                [
                    ['key' => 'client_reference', 'label' => 'Client Reference'],
                    ['key' => 'compliance', 'label' => 'Compliance'],
                    ['key' => 'client', 'label' => 'Client'],
                ],
                array_slice($jobFields, 2)
            ),
            'general_assembly' => array_map(
                fn (array $field) => $field['key'] === 'job_type'
                    ? ['key' => 'job_type', 'label' => 'Job Status']
                    : $field,
                array_merge(
                    [
                        ['key' => 'client_reference', 'label' => 'Client Reference'],
                        ['key' => 'compliance', 'label' => 'Compliance'],
                        ['key' => 'client', 'label' => 'Client Name'],
                        ['key' => 'email', 'label' => 'Email'],
                    ],
                    array_slice($jobFields, 2)
                )
            ),
            'fyrs' => [
                ['key' => 'job_date', 'label' => 'Date'],
                ['key' => 'job_number', 'label' => 'Job Ref #'],
                ['key' => 'builder', 'label' => 'Builder'],
                ['key' => 'builder_other', 'label' => 'Other builder name'],
                ['key' => 'storeys', 'label' => 'Storeys'],
                ['key' => 'climate_zone', 'label' => 'Climate zone'],
                ['key' => 'address', 'label' => 'Address'],
                ['key' => 'notes', 'label' => 'Notes'],
                ['key' => 'assigned', 'label' => 'Staff'],
                ['key' => 'stage', 'label' => 'Stage'],
                ['key' => 'due_date', 'label' => 'Due date'],
                ['key' => 'upload_files', 'label' => 'Files'],
            ],
        ];
    }
}
