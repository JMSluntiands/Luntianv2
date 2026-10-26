<?php

namespace App\Services;

use App\Models\SlackConfig;
use Illuminate\Support\Facades\Log;

/**
 * Posts the new-job message: the branch on the first line, then the job details and a link to the job.
 */
class SlackNewJobNotifier
{
    /**
     * Branch labels passed to send(); also the options of the branch checklist in Slack settings.
     */
    public const BRANCHES = [
        'LBS',
        'Luntian',
        'Efficient Living',
        'Gen EA',
        'BPH',
        'BluInq',
        'Redistar',
    ];

    /**
     * Default side-bar color of the Slack message per branch; Slack cannot color the text itself.
     * Overridden per branch from Slack settings.
     */
    public const DEFAULT_BRANCH_COLORS = [
        'LBS' => '#1E88E5',
        'Luntian' => '#F4511E',
        'Efficient Living' => '#00ACC1',
        'Gen EA' => '#8E24AA',
        'BPH' => '#2E7D32',
        'BluInq' => '#3949AB',
        'Redistar' => '#E53935',
    ];

    private const DEFAULT_COLOR = '#757575';

    public static function colorFor(string $branch, ?SlackConfig $config = null): string
    {
        return $config?->newJobBranchColor($branch)
            ?? self::DEFAULT_BRANCH_COLORS[$branch]
            ?? self::DEFAULT_COLOR;
    }

    /**
     * @param  array<string, mixed>  $details  Label => value, in display order.
     */
    public static function send(string $branch, array $details, ?string $jobUrl, int $jobId): void
    {
        $webhook = SlackWebhookResolver::newJobWebhook();
        if (! $webhook) {
            return;
        }
        $config = SlackConfig::first();
        if ($config && ! $config->newJobBranchEnabled($branch)) {
            return;
        }

        $lines = ['*'.self::escape($branch).'*'];
        foreach ($details as $label => $value) {
            $value = trim((string) ($value ?? ''));
            $lines[] = '*'.self::escape((string) $label).':* '.($value !== '' ? self::escape($value) : '—');
        }
        if ($jobUrl !== null && trim($jobUrl) !== '') {
            $lines[] = '<'.self::publicJobUrl(trim($jobUrl)).'|Open job>';
        }

        try {
            $ch = curl_init($webhook);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode([
                    'attachments' => [[
                        'color' => self::colorFor($branch, $config),
                        'fallback' => 'New job: '.$branch,
                        'text' => implode("\n", $lines),
                        'mrkdwn_in' => ['text'],
                    ]],
                ]),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
            ]);
            curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            if ($err) {
                Log::warning('Slack new job notification failed', ['error' => $err, 'branch' => $branch, 'job_id' => $jobId]);
            }
        } catch (\Throwable $e) {
            Log::warning('Slack new job notification exception', ['message' => $e->getMessage(), 'branch' => $branch, 'job_id' => $jobId]);
        }
    }

    /**
     * Links must open on the main app, not localhost or a public-form domain the job was submitted from.
     */
    private static function publicJobUrl(string $url): string
    {
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        if (in_array($host, (array) config('services.slack.job_link_hosts', []), true)) {
            return $url;
        }

        $base = rtrim((string) config('services.slack.job_link_base_url'), '/');
        if ($base === '') {
            return $url;
        }

        return $base
            .($parts['path'] ?? '/')
            .(isset($parts['query']) ? '?'.$parts['query'] : '')
            .(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');
    }

    private static function escape(string $text): string
    {
        return str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $text);
    }
}
