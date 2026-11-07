<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Task extends Model
{
    public const STATUS_NOT_STARTED = 'not_started';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_ON_HOLD = 'on_hold';

    public const STATUS_DONE = 'done';

    public const VISIBILITY_PUBLIC = 'public';

    public const VISIBILITY_PERSONAL = 'personal';

    public const VISIBILITY_SHARED = 'shared';

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
        self::VISIBILITY_SHARED,
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

    public function isShared(): bool
    {
        if (! static::supportsVisibility()) {
            return false;
        }

        return strtolower(trim((string) $this->visibility)) === self::VISIBILITY_SHARED;
    }

    /**
     * @return list<int>
     */
    public function memberIds(): array
    {
        if (! $this->id || ! Schema::hasTable('task_members')) {
            return [];
        }

        return DB::table('task_members')
            ->where('task_id', $this->id)
            ->pluck('user_id')
            ->map(static fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    /**
     * @param  list<int>  $userIds
     */
    public function syncMembers(array $userIds): void
    {
        static::ensureMembersTable();
        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds), static fn ($id) => $id > 0)));

        DB::table('task_members')->where('task_id', $this->id)->delete();
        $now = now();
        foreach ($userIds as $userId) {
            DB::table('task_members')->insert([
                'task_id' => $this->id,
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Creator, assignee and (for shared tasks) members.
     *
     * @return list<int>
     */
    public function participantIds(): array
    {
        $ids = [(int) ($this->created_by ?? 0), (int) ($this->assignee_user_id ?? 0)];
        if ($this->isShared()) {
            $ids = array_merge($ids, $this->memberIds());
        }

        return array_values(array_unique(array_filter($ids, static fn ($id) => $id > 0)));
    }

    public function isVisibleTo(int $userId): bool
    {
        if ($this->isPersonal()) {
            return $userId > 0 && (int) ($this->created_by ?? 0) === $userId;
        }
        if ($this->isShared()) {
            return $userId > 0 && in_array($userId, $this->participantIds(), true);
        }

        return true;
    }

    public static function ensureMembersTable(): void
    {
        if (Schema::hasTable('task_members')) {
            return;
        }

        Schema::create('task_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->timestamps();
            $table->unique(['task_id', 'user_id']);
        });
    }

    public static function ensureCommentTables(): void
    {
        if (! Schema::hasTable('task_comments')) {
            Schema::create('task_comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('task_id')->index();
                $table->unsignedBigInteger('parent_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('author_name', 120)->nullable();
                $table->text('body');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('task_comment_notifications')) {
            Schema::create('task_comment_notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('task_id');
                $table->unsignedBigInteger('comment_id');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
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
     * Shared tasks are visible to their creator, assignee and members.
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
                $q->orWhere(function ($shared) use ($userId) {
                    $shared->where('visibility', self::VISIBILITY_SHARED)
                        ->where(function ($who) use ($userId) {
                            $who->where('created_by', $userId)
                                ->orWhere('assignee_user_id', $userId);
                            if (Schema::hasTable('task_members')) {
                                $who->orWhereExists(function ($member) use ($userId) {
                                    $member->select(DB::raw(1))
                                        ->from('task_members')
                                        ->whereColumn('task_members.task_id', 'tasks.id')
                                        ->where('task_members.user_id', $userId);
                                });
                            }
                        });
                });
            }
        });
    }
}
