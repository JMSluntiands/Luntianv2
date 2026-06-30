<?php

namespace App\Http\Controllers;

use App\Services\DashboardJobStatsService;
use Illuminate\Http\JsonResponse;

class DashboardStatsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json(DashboardJobStatsService::fetch());
    }
}
