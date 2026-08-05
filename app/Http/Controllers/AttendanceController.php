<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function clockIn(Request $request): JsonResponse
    {
        $userId = (int) session('user_id', 0);
        if ($userId <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please log in first.',
            ], 401);
        }

        if (! Attendance::tableReady()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Attendance is not set up yet. Please run migrations.',
            ], 503);
        }

        $now = Carbon::now(Attendance::TIMEZONE);
        $today = $now->toDateString();

        try {
            $attendance = DB::transaction(function () use ($userId, $now, $today) {
                $existing = Attendance::query()
                    ->where('user_id', $userId)
                    ->whereDate('attendance_date', $today)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return $existing;
                }

                return Attendance::create([
                    'user_id' => $userId,
                    'attendance_date' => $today,
                    'clocked_in_at' => $now->format('Y-m-d H:i:s'),
                ]);
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not clock in. Please try again.',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Clocked in successfully.',
            'attendance' => Attendance::dashboardStatusForUser($userId),
        ]);
    }

    public function clockOut(Request $request): JsonResponse
    {
        $userId = (int) session('user_id', 0);
        if ($userId <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please log in first.',
            ], 401);
        }

        if (! Attendance::tableReady()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Attendance is not set up yet. Please run migrations.',
            ], 503);
        }

        $now = Carbon::now(Attendance::TIMEZONE);
        $today = $now->toDateString();

        try {
            $attendance = DB::transaction(function () use ($userId, $now, $today) {
                $existing = Attendance::query()
                    ->where('user_id', $userId)
                    ->whereDate('attendance_date', $today)
                    ->lockForUpdate()
                    ->first();

                if (! $existing || empty($existing->clocked_in_at)) {
                    return ['error' => 'missing'];
                }

                // After midnight, yesterday's open session cannot be clocked out
                if (! Attendance::canClockOutTodayRecord($existing, $now)) {
                    return ['error' => 'locked'];
                }

                if (empty($existing->clocked_out_at)) {
                    $existing->clocked_out_at = $now->format('Y-m-d H:i:s');
                    $existing->save();
                }

                return ['record' => $existing];
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Could not clock out. Please try again.',
            ], 500);
        }

        if (($attendance['error'] ?? null) === 'locked') {
            return response()->json([
                'status' => 'error',
                'message' => 'Clock out is no longer available after midnight. Yesterday is marked as no clock out.',
                'attendance' => Attendance::dashboardStatusForUser($userId),
            ], 422);
        }

        if (($attendance['error'] ?? null) === 'missing' || empty($attendance['record'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Clock in first before clocking out.',
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Clocked out successfully.',
            'attendance' => Attendance::dashboardStatusForUser($userId),
        ]);
    }

    public function status(): JsonResponse
    {
        $userId = (int) session('user_id', 0);

        return response()->json([
            'status' => 'success',
            'attendance' => Attendance::dashboardStatusForUser($userId > 0 ? $userId : null),
        ]);
    }
}
