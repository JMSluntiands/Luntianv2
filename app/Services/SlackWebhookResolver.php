<?php

namespace App\Services;

use App\Models\SlackConfig;

/**
 * Resolves Slack Incoming Webhook URLs by purpose (new job vs assignment change).
 * Uses per-purpose active flags on {@see SlackConfig}; falls back to legacy {@see SlackConfig::$webhook_url} then env when a purpose-specific URL is empty.
 */
class SlackWebhookResolver
{
    public static function newJobWebhook(): ?string
    {
        $slackConfig = SlackConfig::first();
        if ($slackConfig) {
            if (!$slackConfig->new_job_slack_active) {
                return null;
            }
            $specific = trim((string) ($slackConfig->webhook_new_job_url ?? ''));
            if ($specific !== '') {
                return $specific;
            }
            $legacy = trim((string) ($slackConfig->webhook_url ?? ''));
            if ($legacy !== '') {
                return $legacy;
            }

            return null;
        }

        $url = config('services.slack.lbs_webhook_new_job') ?: config('services.slack.lbs_webhook');

        return (is_string($url) && $url !== '') ? $url : null;
    }

    public static function assignmentWebhook(): ?string
    {
        $slackConfig = SlackConfig::first();
        if ($slackConfig) {
            if (!$slackConfig->assignment_slack_active) {
                return null;
            }
            $specific = trim((string) ($slackConfig->webhook_assignment_url ?? ''));
            if ($specific !== '') {
                return $specific;
            }
            $legacy = trim((string) ($slackConfig->webhook_url ?? ''));
            if ($legacy !== '') {
                return $legacy;
            }

            return null;
        }

        $url = config('services.slack.lbs_webhook_assignment') ?: config('services.slack.lbs_webhook');

        return (is_string($url) && $url !== '') ? $url : null;
    }
}
