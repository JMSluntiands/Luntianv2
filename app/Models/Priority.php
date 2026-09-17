<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    protected $fillable = ['name', 'color'];

    /**
     * Priority names for inline list dropdowns.
     *
     * @return list<string>
     */
    public static function optionsForSelect(): array
    {
        return static::query()
            ->whereNotNull('name')
            ->orderBy('id')
            ->pluck('name')
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
