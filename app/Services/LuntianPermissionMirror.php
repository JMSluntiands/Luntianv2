<?php

namespace App\Services;

/**
 * Maps LBS permission grants to Luntian equivalents when bootstrapping Luntian access.
 * LBS-only routes (forms submitted, accept form, file/comment API routes, etc.) are never mirrored.
 */
class LuntianPermissionMirror
{
    /** @return array<string, true> */
    public static function luntianModuleRouteSet(): array
    {
        $modules = config('permissions.job_ui_modules.luntian', []);
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

    /** @return list<string> */
    public static function lbsRoutesExcludedFromMirror(): array
    {
        return [
            'lbs.list.formsSubmitted',
            'lbs.list.tablesFragment',
            'lbs.job.acceptForm',
            'lbs.job.uploadFiles',
            'lbs.job.deleteFile',
            'lbs.job.file',
            'lbs.job.checkerUploads',
            'lbs.job.runComment',
            'lbs.job.comment',
            'lbs.job.archive',
        ];
    }

    public static function mapRouteNameToLuntian(string $name): ?string
    {
        if (in_array($name, self::lbsRoutesExcludedFromMirror(), true)) {
            return null;
        }

        $targets = self::luntianModuleRouteSet();
        $mapped = null;

        if (str_starts_with($name, 'job_view.lbs.')) {
            $mapped = str_replace('job_view.lbs.', 'job_view.luntian.', $name);
        } elseif (preg_match('/^lbs\.(add|list|completed|review|mailbox|trash|store)$/', $name, $m)) {
            $mapped = 'luntian.'.$m[1];
        } elseif (preg_match('/^lbs\.job\.(view|update|restore|emailPreview|sendMailboxEmail|sendSlack|sendSubmissionEmail)$/', $name, $m)) {
            $mapped = 'luntian.job.'.$m[1];
        }

        if ($mapped === null || ! isset($targets[$mapped])) {
            return null;
        }

        return $mapped;
    }

    /** @return list<string> */
    public static function luntianConfigurableRouteNames(): array
    {
        return array_keys(self::luntianModuleRouteSet());
    }
}
