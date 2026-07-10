<?php

namespace App\Http\Controllers;

use App\Services\DashboardJobStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardStatsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(DashboardJobStatsService::fetch());
    }

    public function chart(Request $request): JsonResponse
    {
        $date = $request->query('date');
        if (is_string($date) && $date !== '' && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json(['message' => 'Invalid date. Use YYYY-MM-DD.'], 422);
        }

        return response()->json(DashboardJobStatsService::fetchStatusChart(
            is_string($date) && $date !== '' ? $date : null,
            [
                'client' => trim((string) $request->query('client', '')),
                'status' => trim((string) $request->query('status', '')),
                'staff' => trim((string) $request->query('staff', '')),
            ]
        ));
    }
}
