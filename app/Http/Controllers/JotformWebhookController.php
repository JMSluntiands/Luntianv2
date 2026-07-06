<?php

namespace App\Http\Controllers;

use App\Models\JotformConfig;
use App\Services\JotformSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class JotformWebhookController extends Controller
{
    public function __invoke(Request $request, JotformSubmissionService $service): JsonResponse
    {
        $submissionId = $request->input('submissionID') ?? $request->input('submissionId');
        $formId = $request->input('formID') ?? $request->input('formId') ?? $request->input('form_id');

        $this->logJotform('info', 'Webhook hit', [
            'ip' => $request->ip(),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'form_id' => $formId,
            'submission_id' => $submissionId,
            'has_query_secret' => $request->query('secret') !== null,
            'payload_keys' => array_keys($request->all()),
            'raw_body_length' => strlen($request->getContent()),
        ]);

        $config = JotformConfig::current();
        if (! $config || ! $config->is_active) {
            $this->logJotform('warning', 'Rejected — integration inactive', [
                'form_id' => $formId,
                'submission_id' => $submissionId,
                'config_exists' => (bool) $config,
                'is_active' => (bool) ($config?->is_active ?? false),
            ]);

            return response()->json(['status' => 'error', 'message' => 'JotForm integration is not active.'], 503);
        }

        $secret = (string) ($config->webhook_secret ?? '');
        $provided = (string) ($request->query('secret')
            ?: $request->header('X-Luntian-Jotform-Secret')
            ?: $request->input('secret', ''));

        if ($secret === '' || ! hash_equals($secret, $provided)) {
            $this->logJotform('warning', 'Rejected — invalid webhook secret', [
                'form_id' => $formId,
                'submission_id' => $submissionId,
                'secret_configured' => $secret !== '',
                'secret_provided' => $provided !== '',
                'secret_source' => $request->query('secret') !== null
                    ? 'query'
                    : ($request->header('X-Luntian-Jotform-Secret') ? 'header' : ($request->input('secret') ? 'body' : 'none')),
            ]);

            return response()->json(['status' => 'error', 'message' => 'Invalid webhook secret.'], 403);
        }

        if ($request->isMethod('GET')) {
            $this->logJotform('info', 'Browser ping OK', [
                'form_id' => $config->jotform_form_id,
                'is_active' => true,
            ]);

            return response()->json([
                'status' => 'ok',
                'message' => 'LUNTIAN JotForm webhook is active. POST submissions here from JotForm Integrations → Webhooks.',
                'form_id' => $config->jotform_form_id,
                'log_file' => 'storage/logs/jotform.log',
            ]);
        }

        $payload = $request->all();
        if ($payload === [] && $request->getContent() !== '') {
            $decoded = json_decode($request->getContent(), true);
            if (is_array($decoded)) {
                $payload = $decoded;
            }
        }

        $fields = $service->parseSubmissionPayload($payload);
        $formId = $payload['formID'] ?? $payload['formId'] ?? $payload['form_id'] ?? $formId;

        if ($submissionId === null || $submissionId === '') {
            if ($fields === []) {
                $this->logJotform('info', 'Empty POST ping — endpoint ready', [
                    'form_id' => $formId,
                    'payload_keys' => array_keys($payload),
                ]);

                return response()->json([
                    'status' => 'ok',
                    'message' => 'Webhook endpoint ready. Waiting for JotForm submission.',
                ]);
            }
        }

        $this->logJotform('info', 'Payload parsed', [
            'form_id' => $formId,
            'submission_id' => $submissionId,
            'field_count' => count($fields),
            'field_keys' => array_keys($fields),
            'field_preview' => $this->fieldPreview($fields),
            'has_raw_request' => isset($payload['rawRequest']) && $payload['rawRequest'] !== '',
            'raw_request_type' => isset($payload['rawRequest']) ? gettype($payload['rawRequest']) : null,
        ]);

        try {
            $result = $service->createLbsJobFromSubmission($config, $fields, is_scalar($formId) ? (string) $formId : null);
        } catch (\Throwable $e) {
            $this->logJotform('error', 'Job create failed', [
                'form_id' => $formId,
                'submission_id' => $submissionId,
                'message' => $e->getMessage(),
                'exception' => $e::class,
                'field_keys' => array_keys($fields),
                'field_preview' => $this->fieldPreview($fields),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }

        $this->logJotform('info', 'Job created', [
            'form_id' => $formId,
            'submission_id' => $submissionId,
            'job_id' => $result['job_id'],
            'reference' => $result['reference'],
            'queued_as_forms' => (bool) $config->queue_in_forms_submitted,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'LBS job created from JotForm submission.',
            'job_id' => $result['job_id'],
            'reference' => $result['reference'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $fields
     * @return array<string, string>
     */
    private function fieldPreview(array $fields): array
    {
        $preview = [];

        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                $text = json_encode($value);
            } else {
                $text = (string) $value;
            }

            $text = preg_replace('/\s+/', ' ', trim($text)) ?? '';
            if (strlen($text) > 120) {
                $text = Str::limit($text, 120, '…');
            }

            $preview[(string) $key] = $text;
        }

        ksort($preview);

        return $preview;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function logJotform(string $level, string $message, array $context = []): void
    {
        $full = '[JotForm] '.$message;
        Log::channel('jotform')->log($level, $full, $context);
        Log::log($level, $full, $context);
    }
}
