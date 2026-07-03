<?php

namespace App\Services;

/**
 * Maps LBS permission grants to General Assembly equivalents when bootstrapping GA access.
 */
class GeneralAssemblyPermissionMirror
{
    /** @return array<string, true> */
    public static function generalAssemblyModuleRouteSet(): array
    {
        $modules = config('permissions.job_ui_modules.general_assembly', []);
        $routes = array_merge(
            $modules['sidebar'] ?? [],
            $modules['card'] ?? [],
            $modules['buttons'] ?? []
        );
        $set = [];
        foreach ($routes as $name) {
            if (is_string($name) && $name !== '') {
                $set[$name] = true;
            }
        }

        return $set;
    }

    public static function mapRouteNameToGeneralAssembly(string $name): ?string
    {
        $targets = self::generalAssemblyModuleRouteSet();
        $mapped = null;

        if (str_starts_with($name, 'job_view.lbs.')) {
            $mapped = str_replace('job_view.lbs.', 'job_view.general_assembly.', $name);
        } elseif (preg_match('/^lbs\.(.+)$/', $name, $m)) {
            $mapped = 'general_assembly.'.$m[1];
        }

        if ($mapped === null || ! isset($targets[$mapped])) {
            return null;
        }

        return $mapped;
    }

    /** @return list<string> */
    public static function generalAssemblyConfigurableRouteNames(): array
    {
        return array_keys(self::generalAssemblyModuleRouteSet());
    }
}
