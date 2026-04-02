<?php

namespace App\Http\Controllers;

use App\Services\PublicHolidayService;
use Illuminate\Http\JsonResponse;

class DashboardHolidayController extends Controller
{
    public function __invoke(int $year): JsonResponse
    {
        if ($year < 2000 || $year > 2100) {
            abort(400, 'Invalid year');
        }

        $data = PublicHolidayService::forYear($year);

        return response()->json($data);
    }
}
