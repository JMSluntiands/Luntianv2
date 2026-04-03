<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

final class FecUnitsValidation
{
    public static function effectiveUnits(Request $request, object $job, string $unitsColumn = 'units'): int
    {
        if ($request->exists('units')) {
            return max(0, (int) $request->input('units'));
        }

        $stored = property_exists($job, $unitsColumn) ? $job->{$unitsColumn} : 0;

        return max(0, (int) $stored);
    }

    /**
     * Block transition to For Email Confirmation unless the job has at least one unit
     * (from request or already stored).
     */
    public static function jsonErrorIfFecWithoutUnits(
        Request $request,
        object $job,
        string $newStatus,
        string $unitsColumn = 'units'
    ): ?JsonResponse {
        if (strtolower(trim($newStatus)) !== 'for email confirmation') {
            return null;
        }
        if (self::effectiveUnits($request, $job, $unitsColumn) < 1) {
            return response()->json([
                'message' => 'Maglagay muna ng units (minimum 1) bago ilipat sa For Email Confirmation.',
            ], 422);
        }

        return null;
    }
}
