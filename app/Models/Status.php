<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    public const DEFAULT_FONT_COLOR = '#333333';

    protected $fillable = ['name', 'color', 'font_color', 'show_on_form'];

    protected $casts = [
        'show_on_form' => 'boolean',
    ];

    /**
     * Status names that standalone submissions can land in the For Inquiries table.
     *
     * @return list<string>
     */
    public static function formInquiryNames(): array
    {
        $names = ['For Inquiries', 'Allocated'];
        if (! \Illuminate\Support\Facades\Schema::hasColumn('statuses', 'show_on_form')) {
            return $names;
        }

        $extra = static::query()
            ->where('show_on_form', true)
            ->where('name', '!=', 'For Quotation')
            ->pluck('name')
            ->all();

        return array_values(array_unique(array_merge($names, $extra)));
    }

    public static function resolveFontColor(?string $hex): string
    {
        $c = trim((string) $hex);
        if ($c === '') {
            return self::DEFAULT_FONT_COLOR;
        }
        if ($c[0] !== '#') {
            $c = '#' . ltrim($c, '#');
        }
        if (preg_match('/^#[A-Fa-f0-9]{6}$/', $c)) {
            return strtolower($c);
        }

        return self::DEFAULT_FONT_COLOR;
    }

    /**
     * @return array<string, string|null>
     */
    public static function backgroundColorsByName(): array
    {
        return static::query()
            ->whereNotNull('name')
            ->pluck('color', 'name')
            ->toArray();
    }

    /**
     * @return array<string, string>
     */
    public static function fontColorsByName(): array
    {
        return static::query()
            ->whereNotNull('name')
            ->get(['name', 'font_color'])
            ->mapWithKeys(fn ($row) => [
                $row->name => static::resolveFontColor($row->font_color),
            ])
            ->all();
    }
}
