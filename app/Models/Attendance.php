<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Attendance extends Model
{
    public const TIMEZONE = 'Asia/Manila';

    /** Staff who have not clocked in by this time (PHT) are considered absent. */
    public const CUTOFF_TIME = '08:00';

    /** Grace period after cutoff before clock-in is considered late. */
    public const CLOCK_IN_GRACE_MINUTES = 15;

    /** Open session turns overdue after this many hours (legacy / dashboard warning). */
    public const OVERDUE_HOURS = 8;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'clocked_in_at',
        'clocked_out_at',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'clocked_in_at' => 'datetime',
            'clocked_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function todayDate(): string
    {
        return Carbon::now(self::TIMEZONE)->toDateString();
    }

    public static function tableReady(): bool
    {
        try {
            return Schema::hasTable('attendances');
        } catch (\Throwable) {
            return false;
        }
    }

    public static function forUserToday(int $userId): ?self
    {
        if (! self::tableReady()) {
            return null;
        }

        return static::query()
            ->where('user_id', $userId)
            ->whereDate('attendance_date', self::todayDate())
            ->first();
    }

    /**
     * Clock-out is only allowed on the same calendar day (PHT) as the clock-in.
     * After midnight, previous day's open session is locked as "no clock out".
     */
    public static function canClockOutTodayRecord(?self $record, ?Carbon $now = null): bool
    {
        if ($record === null || empty($record->clocked_in_at) || ! empty($record->clocked_out_at)) {
            return false;
        }

        $now = $now ?? Carbon::now(self::TIMEZONE);
        $attendanceDate = $record->attendance_date
            ? Carbon::parse($record->attendance_date)->timezone(self::TIMEZONE)->toDateString()
            : null;

        return $attendanceDate === $now->toDateString();
    }

    /**
     * Payload for the dashboard Daily attendance banner.
     *
     * @return array{
     *   clocked_in: bool,
     *   clocked_out: bool,
     *   clocked_in_at: string|null,
     *   clocked_out_at: string|null,
     *   local_time: string,
     *   cutoff_label: string,
     *   timezone_label: string,
     *   can_clock_in: bool,
     *   can_clock_out: bool,
     *   overdue: bool,
     *   hours_open: float|null
     * }
     */
    public static function dashboardStatusForUser(?int $userId): array
    {
        $now = Carbon::now(self::TIMEZONE);
        $empty = [
            'clocked_in' => false,
            'clocked_out' => false,
            'clocked_in_at' => null,
            'clocked_out_at' => null,
            'local_time' => $now->format('g:i A'),
            'cutoff_label' => '8:00 AM',
            'timezone_label' => 'Philippines (PHT)',
            'can_clock_in' => false,
            'can_clock_out' => false,
            'overdue' => false,
            'hours_open' => null,
        ];

        if (! self::tableReady()) {
            return $empty;
        }

        try {
            $record = $userId ? self::forUserToday($userId) : null;
            $clockedIn = $record !== null && ! empty($record->clocked_in_at);
            $clockedOut = $record !== null && ! empty($record->clocked_out_at);
            $canClockOut = $userId !== null && $userId > 0 && self::canClockOutTodayRecord($record, $now);

            $hoursOpen = null;
            $overdue = false;
            if ($clockedIn && ! $clockedOut) {
                $inAt = Carbon::parse($record->clocked_in_at)->timezone(self::TIMEZONE);
                $hoursOpen = round(max(0, $inAt->diffInMinutes($now)) / 60, 1);
                $overdue = $hoursOpen >= self::OVERDUE_HOURS;
            }

            $clockedInAt = $clockedIn
                ? Carbon::parse($record->clocked_in_at)->timezone(self::TIMEZONE)->format('g:i A')
                : null;
            $clockedOutAt = $clockedOut
                ? Carbon::parse($record->clocked_out_at)->timezone(self::TIMEZONE)->format('g:i A')
                : null;

            return [
                'clocked_in' => $clockedIn,
                'clocked_out' => $clockedOut,
                'clocked_in_at' => $clockedInAt,
                'clocked_out_at' => $clockedOutAt,
                'local_time' => $now->format('g:i A'),
                'cutoff_label' => '8:00 AM',
                'timezone_label' => 'Philippines (PHT)',
                // New day (after midnight): yesterday's open session is ignored → clock in available again
                'can_clock_in' => $userId !== null && $userId > 0 && ! $clockedIn,
                'can_clock_out' => $canClockOut,
                'overdue' => $overdue,
                'hours_open' => $hoursOpen,
            ];
        } catch (\Throwable) {
            return $empty;
        }
    }
}
