<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    public const TIMEZONE = 'Asia/Manila';

    /** Staff who have not clocked in by this time (PHT) are considered absent. */
    public const CUTOFF_TIME = '08:00';

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

    public static function forUserToday(int $userId): ?self
    {
        return static::query()
            ->where('user_id', $userId)
            ->whereDate('attendance_date', self::todayDate())
            ->first();
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
     *   can_clock_out: bool
     * }
     */
    public static function dashboardStatusForUser(?int $userId): array
    {
        $now = Carbon::now(self::TIMEZONE);
        $record = $userId ? self::forUserToday($userId) : null;
        $clockedIn = $record !== null && ! empty($record->clocked_in_at);
        $clockedOut = $record !== null && ! empty($record->clocked_out_at);

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
            'can_clock_in' => $userId !== null && $userId > 0 && ! $clockedIn,
            'can_clock_out' => $userId !== null && $userId > 0 && $clockedIn && ! $clockedOut,
        ];
    }
}
