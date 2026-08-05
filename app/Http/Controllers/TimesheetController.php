<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveDay;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class TimesheetController extends Controller
{
    public function index(Request $request)
    {
        $tz = 'Asia/Manila';
        $today = Carbon::now($tz)->startOfDay();

        $year = (int) $request->query('year', $today->year);
        $month = (int) $request->query('month', $today->month);
        if ($month < 1 || $month > 12) {
            $month = (int) $today->month;
        }
        if ($year < 2000 || $year > 2100) {
            $year = (int) $today->year;
        }

        $monthStart = Carbon::create($year, $month, 1, 0, 0, 0, $tz)->startOfDay();
        $gridStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
        $gridEnd = $monthStart->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        // Keep a fixed 6-week grid when the month fits in 5 weeks
        if ($gridStart->diffInDays($gridEnd) < 41) {
            $gridEnd = $gridStart->copy()->addDays(41);
        }

        $days = [];
        $cursor = $gridStart->copy();
        while ($cursor->lte($gridEnd)) {
            $days[] = [
                'date' => $cursor->toDateString(),
                'day' => (int) $cursor->day,
                'dow' => $cursor->format('D'),
                'is_weekend' => $cursor->isWeekend(),
                'is_today' => $cursor->isSameDay($today),
                'in_month' => $cursor->month === $month,
            ];
            $cursor->addDay();
        }

        $userFilter = trim((string) $request->query('user', 'all'));
        $teamQuery = User::query()
            ->orderByRaw('COALESCE(NULLIF(TRIM(fullname), ""), NULLIF(TRIM(username), ""), email)')
            ->orderBy('id');

        // Active accounts only (exclude Inactive / Archived on status or task)
        if (Schema::hasColumn('users', 'status')) {
            $teamQuery->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereRaw('LOWER(TRIM(COALESCE(status, ""))) = ?', ['active']);
            });
        }

        if (Schema::hasColumn('users', 'task')) {
            $teamQuery->where(function ($q) {
                $q->whereNull('task')
                    ->orWhereRaw('TRIM(COALESCE(task, "")) = ?', [''])
                    ->orWhereRaw('LOWER(TRIM(task)) = ?', ['active']);
            });
        }

        if (Schema::hasColumn('users', 'is_employee')) {
            $teamQuery->where('is_employee', true);
        }

        $userColumns = ['id', 'fullname', 'username', 'email', 'profile_image'];
        if (Schema::hasColumn('users', 'leave_credits')) {
            $userColumns[] = 'leave_credits';
        }

        $allUsers = $teamQuery->get($userColumns);

        $selectedUserId = null;
        if ($userFilter !== '' && $userFilter !== 'all' && ctype_digit($userFilter)) {
            $selectedUserId = (int) $userFilter;
        }

        $users = $selectedUserId
            ? $allUsers->where('id', $selectedUserId)->values()
            : $allUsers->values();

        $leaveByUser = [];
        if (Schema::hasTable('leave_days') && $users->isNotEmpty()) {
            $rows = LeaveDay::query()
                ->whereIn('user_id', $users->pluck('id')->all())
                ->where('status', LeaveDay::STATUS_APPROVED)
                ->whereBetween('leave_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
                ->get(['user_id', 'leave_date', 'leave_type']);

            foreach ($rows as $row) {
                $uid = (int) $row->user_id;
                $dateKey = $row->leave_date?->toDateString();
                if ($dateKey === null) {
                    continue;
                }
                $leaveByUser[$uid][$dateKey] = (string) ($row->leave_type ?: 'leave');
            }
        }

        $hoursByUser = [];
        if (Attendance::tableReady() && $users->isNotEmpty()) {
            $now = Carbon::now($tz);
            $attendanceRows = Attendance::query()
                ->whereIn('user_id', $users->pluck('id')->all())
                ->whereBetween('attendance_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
                ->get(['user_id', 'attendance_date', 'clocked_in_at', 'clocked_out_at']);

            foreach ($attendanceRows as $row) {
                if (empty($row->clocked_in_at)) {
                    continue;
                }

                $uid = (int) $row->user_id;
                $dateKey = $row->attendance_date?->toDateString();
                if ($dateKey === null) {
                    continue;
                }

                $inAt = Carbon::parse($row->clocked_in_at)->timezone($tz);
                $noClockOut = empty($row->clocked_out_at);
                $isPastDay = $dateKey < $today->toDateString();
                $isToday = $dateKey === $today->toDateString();

                if (! $noClockOut) {
                    $outAt = Carbon::parse($row->clocked_out_at)->timezone($tz);
                } elseif ($isToday) {
                    $outAt = $now->copy();
                } else {
                    // Past day without clock out — freeze at midnight (end of that day)
                    $outAt = Carbon::parse($dateKey, $tz)->endOfDay();
                }

                if ($outAt->lt($inAt)) {
                    $outAt = $inAt->copy();
                }

                $hours = round(max(0, $inAt->diffInMinutes($outAt)) / 60, 1);
                $dayStart = Carbon::parse($dateKey, $tz)->startOfDay();
                $dayMinutes = 24 * 60;
                $startMin = max(0, min($dayMinutes, $dayStart->diffInMinutes($inAt, false)));
                $endMin = max($startMin, min($dayMinutes, $dayStart->diffInMinutes($outAt, false)));
                $fillStartPct = round(($startMin / $dayMinutes) * 100, 2);
                $fillEndPct = round(($endMin / $dayMinutes) * 100, 2);
                $fillPct = round(min(100, max(0, ($hours / 24) * 100)), 2);

                // Late after 8:00 + 15 min grace
                $lateThreshold = Carbon::parse($dateKey.' '.Attendance::CUTOFF_TIME, $tz)
                    ->addMinutes(Attendance::CLOCK_IN_GRACE_MINUTES);
                $late = $inAt->gt($lateThreshold);

                $noClockOutPast = $noClockOut && $isPastDay;
                // Parallel colors (can stack): green = hours, orange = late, red = no clock out
                $colors = ['green'];
                if ($late) {
                    $colors[] = 'orange';
                }
                if ($noClockOutPast) {
                    $colors[] = 'red';
                }

                $hoursByUser[$uid][$dateKey] = [
                    'hours' => $hours,
                    'clocked_in' => $inAt->format('g:i A'),
                    'clocked_out' => ! $noClockOut
                        ? Carbon::parse($row->clocked_out_at)->timezone($tz)->format('g:i A')
                        : null,
                    'open' => $noClockOut && $isToday,
                    'no_clock_out' => $noClockOutPast,
                    'late' => $late,
                    'colors' => $colors,
                    'fill_start_pct' => $fillStartPct,
                    'fill_end_pct' => $fillEndPct,
                    'fill_pct' => $fillPct,
                ];
            }
        }

        $team = $users->map(function (User $user) use ($leaveByUser, $hoursByUser) {
            $name = trim((string) ($user->fullname ?? ''));
            if ($name === '') {
                $name = trim((string) ($user->username ?? ''));
            }
            if ($name === '') {
                $name = trim((string) ($user->email ?? '')) ?: 'User';
            }

            $parts = preg_split('/\s+/', $name) ?: [];
            $initials = '';
            foreach (array_slice($parts, 0, 2) as $part) {
                $initials .= mb_strtoupper(mb_substr($part, 0, 1));
            }
            if ($initials === '') {
                $initials = 'U';
            }

            $img = trim((string) ($user->profile_image ?? ''));
            $avatar = null;
            if ($img !== '') {
                if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://') || str_starts_with($img, '/')) {
                    $avatar = $img;
                } else {
                    $avatar = asset('storage/'.$img);
                }
            }

            return [
                'id' => (int) $user->id,
                'name' => $name,
                'initials' => $initials,
                'avatar' => $avatar,
                'leave_credits' => (int) ($user->leave_credits ?? 15),
                'leave' => $leaveByUser[(int) $user->id] ?? [],
                'hours' => $hoursByUser[(int) $user->id] ?? [],
            ];
        })->values()->all();

        $prev = $monthStart->copy()->subMonth();
        $next = $monthStart->copy()->addMonth();

        return view('timesheet.index', [
            'sidebar_active' => 'timesheet',
            'monthLabel' => $monthStart->format('F Y'),
            'rangeLabel' => $gridStart->format('d/m/Y').' to '.$gridEnd->format('d/m/Y'),
            'days' => $days,
            'team' => $team,
            'userOptions' => $allUsers,
            'selectedUser' => $selectedUserId ? (string) $selectedUserId : 'all',
            'prevYear' => (int) $prev->year,
            'prevMonth' => (int) $prev->month,
            'nextYear' => (int) $next->year,
            'nextMonth' => (int) $next->month,
            'year' => $year,
            'month' => $month,
        ]);
    }
}
