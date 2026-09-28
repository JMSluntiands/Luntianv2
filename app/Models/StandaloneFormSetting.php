<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StandaloneFormSetting extends Model
{
    protected $fillable = [
        'form_key',
        'field_key',
        'is_required',
        'is_visible',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_visible' => 'boolean',
    ];

    /**
     * @return array<string, bool>
     */
    public static function requiredMap(string $formKey): array
    {
        return static::query()
            ->where('form_key', $formKey)
            ->whereNotNull('field_key')
            ->pluck('is_required', 'field_key')
            ->map(fn ($value) => (bool) $value)
            ->all();
    }

    /**
     * @return array<string, bool>
     */
    public static function visibleMap(string $formKey): array
    {
        return static::query()
            ->where('form_key', $formKey)
            ->whereNotNull('field_key')
            ->pluck('is_visible', 'field_key')
            ->map(fn ($value) => (bool) $value)
            ->all();
    }
}
