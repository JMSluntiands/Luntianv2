<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JotformConfig extends Model
{
    protected $table = 'jotform_configs';

    protected $fillable = [
        'is_active',
        'webhook_secret',
        'jotform_form_id',
        'queue_in_forms_submitted',
        'default_compliance_id',
        'default_client_account_id',
        'default_priority_id',
        'default_job_request_id',
        'default_assigned_to',
        'default_checked_by',
        'map_reference_no',
        'map_client_reference',
        'map_job_address',
        'map_notes',
        'map_compliance',
        'map_client',
        'map_priority',
        'map_job_type',
        'map_assigned_to',
        'map_checked_by',
        'map_job_status',
        'map_upload_plans',
        'map_upload_documents',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'queue_in_forms_submitted' => 'boolean',
    ];

    public static function current(): ?self
    {
        return self::query()->orderBy('id')->first();
    }

    public static function ensureSecret(self $config): self
    {
        if ($config->webhook_secret !== null && $config->webhook_secret !== '') {
            return $config;
        }

        $config->webhook_secret = Str::random(40);
        $config->save();

        return $config;
    }

    public function webhookUrl(): string
    {
        $base = url('/webhooks/jotform');
        $secret = (string) ($this->webhook_secret ?? '');

        return $secret !== '' ? $base.'?secret='.rawurlencode($secret) : $base;
    }
}
