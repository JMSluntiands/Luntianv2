<?php

namespace App\Http\Controllers;

use App\Models\JotformConfig;
use App\Services\JotformSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JotformWebhookController extends Controller
{
    public function __invoke(Request $request, JotformSubmissionService $service)
    {
        Log::info('JotForm webhook received', [
            'ip' => $request->ip(),
            'form_id' => $request->input('formID') ?? $request->input('formId'),
            'submission_id' => $request->input('submissionID'),
        ]);

        $config = JotformConfig::current();
        if (! $config || ! $config->is_active) {
            return response()->json(['status' => 'error', 'message' => 'JotForm integration is not active.'], 503);
        }

        $secret = (string) ($config->webhook_secret ?? '');
        $provided = (string) ($request->query('secret')
            ?: $request->header('X-Luntian-Jotform-Secret')
            ?: $request->input('secret', ''));

        if ($secret === '' || ! hash_equals($secret, $provided)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid webhook secret.'], 403);
        }

        $payload = $request->all();
        if ($payload === [] && $request->getContent() !== '') {
            $decoded = json_decode($request->getContent(), true);
            if (is_array($decoded)) {
                $payload = $decoded;
            }
        }

        $fields = $service->parseSubmissionPayload($payload);
        $formId = $payload['formID'] ?? $payload['formId'] ?? $payload['form_id'] ?? null;

        try {
            $result = $service->createLbsJobFromSubmission($config, $fields, is_scalar($formId) ? (string) $formId : null);
        } catch (\Throwable $e) {
            Log::warning('JotForm webhook failed', [
                'message' => $e->getMessage(),
                'form_id' => $formId,
                'field_keys' => array_keys($fields),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'LBS job created from JotForm submission.',
            'job_id' => $result['job_id'],
            'reference' => $result['reference'],
        ]);
    }
}
