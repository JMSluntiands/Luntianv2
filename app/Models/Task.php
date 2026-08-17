<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class Task extends Model
{
    public const STATUS_NOT_STARTED = 'not_started';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_ON_HOLD = 'on_hold';

    public const STATUS_DONE = 'done';

    public const VISIBILITY_PUBLIC = 'public';

    public const VISIBILITY_PERSONAL = 'personal';

    /** @var list<string> */
    public const STATUSES = [
        self::STATUS_NOT_STARTED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_ON_HOLD,
        self::STATUS_DONE,
    ];

    /** @var list<string> */
    public const VISIBILITIES = [
        self::VISIBILITY_PUBLIC,
        self::VISIBILITY_PERSONAL,
    ];

    protected $fillable = [
        'title',
        'assignee_user_id',
        'due_date',
        'status',
        'notes',
        'visibility',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_IN_PROGRESS => 'In progress',
            self::STATUS_ON_HOLD => 'On hold',
            self::STATUS_DONE => 'Done',
            default => 'Not started',
        };
    }

    /**
     * @return array{label: string, pill: string, dot: string}
     */
    public static function statusMeta(string $status): array
    {
        return match ($status) {
            self::STATUS_IN_PROGRESS => [
                'label' => 'In progress',
                'pill' => 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-300',
                'dot' => 'bg-sky-500',
            ],
            self::STATUS_ON_HOLD => [
                'label' => 'On hold',
                'pill' => 'bg-amber-100 text-amber-900 dark:bg-amber-500/20 dark:text-amber-300',
                'dot' => 'bg-amber-500',
            ],
            self::STATUS_DONE => [
                'label' => 'Done',
                'pill' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300',
                'dot' => 'bg-emerald-500',
            ],
            default => [
                'label' => 'Not started',
                'pill' => 'bg-slate-100 text-slate-700 dark:bg-slate-700/70 dark:text-slate-300',
                'dot' => 'bg-slate-400',
            ],
        };
    }

    public function isPersonal(): bool
    {
        if (! static::supportsVisibility()) {
            return false;
        }

        return strtolower(trim((string) $this->visibility)) === self::VISIBILITY_PERSONAL;
    }

    public static function supportsVisibility(): bool
    {
        static $supported = null;
        if ($supported === null) {
            $supported = Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'visibility');
        }

        return $supported;
    }

    /**
     * Public tasks are visible to everyone with Task Management access.
     * Personal tasks are visible only to the user who created them.
     */
    public function scopeVisibleTo($query, int $userId)
    {
        if (! static::supportsVisibility()) {
            return $query;
        }

        return $query->where(function ($q) use ($userId) {
            $q->where(function ($public) {
                $public->whereNull('visibility')
                    ->orWhere('visibility', self::VISIBILITY_PUBLIC);
            });

            if ($userId > 0) {
                $q->orWhere(function ($personal) use ($userId) {
                    $personal->where('visibility', self::VISIBILITY_PERSONAL)
                        ->where('created_by', $userId);
                });
            }
        });
    }
}
