<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Validates post-login return paths (Slack deep links) and builds absolute URLs.
 * Only same-app dashboard paths are allowed to avoid open redirects.
 */
final class LoginReturnPath
{
    public static function isAllowed(string $requestUri): bool
    {
        if ($requestUri === '' || strlen($requestUri) > 4096) {
            return false;
        }

        if (! str_starts_with($requestUri, '/dashboard')) {
            return false;
        }

        if (str_contains($requestUri, '..')) {
            return false;
        }

        if (str_contains($requestUri, "\0") || str_contains($requestUri, '\\')) {
            return false;
        }

        if (str_starts_with($requestUri, '//')) {
            return false;
        }

        return true;
    }

    public static function absoluteUrlFor(Request $request, string $requestUri): string
    {
        return rtrim($request->getSchemeAndHttpHost(), '/').$requestUri;
    }
}
