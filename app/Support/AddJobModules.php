<?php

namespace App\Support;

final class AddJobModules
{
    /** @return array<string, string> module key => display label */
    public static function options(): array
    {
        $options = [];

        foreach (config('permissions.job_ui_modules', []) as $key => $module) {
            if (! is_array($module)) {
                continue;
            }

            $sidebar = $module['sidebar'] ?? [];
            $hasAdd = collect($sidebar)->contains(
                fn ($route) => is_string($route) && str_ends_with($route, '.add')
            );

            if ($hasAdd) {
                $options[$key] = (string) ($module['label'] ?? ucfirst(str_replace('_', ' ', $key)));
            }
        }

        return $options;
    }

    /** @return list<string> */
    public static function keys(): array
    {
        return array_keys(self::options());
    }

    public static function label(string $key): string
    {
        return self::options()[$key] ?? $key;
    }

    /** @param list<string>|null $modules */
    public static function formatList(?array $modules): string
    {
        if (empty($modules)) {
            return '—';
        }

        return collect($modules)
            ->map(fn ($key) => self::label((string) $key))
            ->join(', ');
    }
}
