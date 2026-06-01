<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class SlackAssignmentNotifier
{
    public static function normalizeCode(?string $code): string
    {
        return strtoupper(trim((string) ($code ?? '')));
    }

    public static function displayNameForCode(?string $code): string
    {
        $norm = self::normalizeCode($code);
        if ($norm === '') {
            return '—';
        }

        $user = User::query()
            ->whereRaw('UPPER(TRIM(unique_code)) = ?', [$norm])
            ->first(['fullname']);

        if ($user && trim((string) ($user->fullname ?? '')) !== '') {
            return trim((string) $user->fullname) . ' (' . $norm . ')';
        }

        return $norm;
    }

    /**
     * Post to the configured Incoming Webhook when staff/checker assignment is saved.
     *
     * @param  string|null  $jobStatus  Current job status label after save.
     * @param  string|null  $jobUrl  Absolute URL to open the job (or list) in the dashboard.
     */
    public static function notifyAssignment(
        string $productLabel,
        int $jobId,
        string $jobReference,
        ?string $staffCode,
        ?string $checkerCode,
        ?string $jobStatus = null,
        ?string $jobUrl = null,
    ): void {
        $webhook = SlackWebhookResolver::assignmentWebhook();
        if (!$webhook) {
            return;
        }

        $when = now('Asia/Manila')->format('M d, Y h:i A') . ' (Asia/Manila)';
        $staffNorm = self::normalizeCode($staffCode);
        $checkerNorm = self::normalizeCode($checkerCode);

        $staffLine = self::displayNameForCode($staffNorm !== '' ? $staffNorm : null);
        $checkerLine = self::displayNameForCode($checkerNorm !== '' ? $checkerNorm : null);

        $ref = trim($jobReference) !== '' ? trim($jobReference) : ('#' . $jobId);
        $statusLine = trim((string) ($jobStatus ?? ''));
        if ($statusLine === '') {
            $statusLine = '—';
        }

        $linkLine = ($jobUrl !== null && trim($jobUrl) !== '')
            ? '<' . trim($jobUrl) . '|Open job in dashboard>'
            : '—';

        try {
            $payload = [
                'text' => '📋 Assignment updated (' . $productLabel . ')',
                'attachments' => [
                    [
                        'color' => '#4A154B',
                        'mrkdwn_in' => ['fields'],
                        'fields' => [
                            ['title' => 'Job', 'value' => $ref . ' (ID ' . $jobId . ')', 'short' => true],
                            ['title' => 'Status', 'value' => $statusLine, 'short' => true],
                            ['title' => 'Dashboard link', 'value' => $linkLine, 'short' => false],
                            ['title' => 'Assigned at', 'value' => $when, 'short' => true],
                            ['title' => 'Staff', 'value' => $staffLine, 'short' => true],
                            ['title' => 'Checker', 'value' => $checkerLine, 'short' => true],
                        ],
                        'footer' => 'Luntian Job Management',
                        'ts' => time(),
                    ],
                ],
            ];

            $ch = curl_init($webhook);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
            ]);
            curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            if ($err) {
                Log::warning('Slack assignment notification failed', [
                    'error' => $err,
                    'job_id' => $jobId,
                    'product' => $productLabel,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Slack assignment notification exception', [
                'message' => $e->getMessage(),
                'job_id' => $jobId,
                'product' => $productLabel,
            ]);
        }
    }
}
