<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveDay extends Model
{
    public const STATUS_APPROVED = 'approved';

    public const STATUS_PENDING = 'pending';

    protected $fillable = [
        'user_id',
        'leave_date',
        'leave_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'leave_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isApproved(): bool
    {
        return strtolower(trim((string) $this->status)) === self::STATUS_APPROVED;
    }
}
